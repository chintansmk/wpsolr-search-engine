<?php

namespace wpsolr\solr;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Select\Query\Query;
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\extensions\sorts\WPSOLR_Options_Sorts;
use wpsolr\ui\WPSOLR_Query;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Regexp;
use wpsolr\WPSOLR_Filters;

class WPSOLR_SearchSolrClient extends WPSOLR_AbstractSolrClient {

	protected $solarium_results;

	protected $solarium_query;

	protected $solarium_config;

	// Solr tag for exclusion between facets of a group
	const SOLR_EXCLUDE_ALL_TAG = "wpsolr_all";
	const SOLR_NOT_EXCLUDE_ALL_TAG = 'wpsolr_none';

	// Search template
	const _SEARCH_PAGE_TEMPLATE = 'wpsolr-search-engine/search.php';

	// Search page slug
	const _SEARCH_PAGE_SLUG = 'search-wpsolr';

	// Default maximum number of items returned by facet
	const DEFAULT_MAX_NB_ITEMS_BY_FACET = 10;

	// Defaut minimum count for a facet to be returned
	const DEFAULT_MIN_COUNT_BY_FACET = 1;

	// Default maximum size of highliting fragments
	const DEFAULT_HIGHLIGHTING_FRAGMENT_SIZE = 100;

	// Default highlighting prefix
	const DEFAULT_HIGHLIGHTING_PREFIX = '<b>';

	// Default highlighting postfix
	const DEFAULT_HIGHLIGHTING_POSFIX = '</b>';

	const PARAMETER_HIGHLIGHTING_FIELD_NAMES = 'field_names';
	const PARAMETER_HIGHLIGHTING_FRAGMENT_SIZE = 'fragment_size';
	const PARAMETER_HIGHLIGHTING_PREFIX = 'prefix';
	const PARAMETER_HIGHLIGHTING_POSTFIX = 'postfix';

	const PARAMETER_FACET_FIELD_NAMES = 'field_names';
	const PARAMETER_FACET_LIMIT = 'limit';
	const PARAMETER_FACET_MIN_COUNT = 'min_count';

	const PARAMETER_IS_EXCLUSION_BETWEEN_FACETS = 'is_exclusion_betweeen_facets';

	// Create using a configuration
	static function create_from_solarium_config( $solarium_config ) {

		return new self( $solarium_config );
	}


	/**
	 * Constructor used by factory WPSOLR_Global
	 * Create using the default index configuration
	 *
	 * @return WPSOLR_SearchSolrClient
	 */
	static function global_object() {

		return self::create_from_index_indice( null );
	}

	// Create using an index configuration
	static function create_from_index_indice( $index_indice ) {

		// Build Solarium config from the default indexing Solr index
		$solarium_config = WPSOLR_Global::getExtensionIndexes()->build_solarium_config( $index_indice, null, self::DEFAULT_SOLR_TIMEOUT_IN_SECOND );

		return new self( $solarium_config );
	}

	public function __construct( $solarium_config ) {

		$this->solarium_client = new Client( $solarium_config );
	}


	/**
	 * Get suggestions from Solr suggester.
	 *
	 * @param string $query Keywords to suggest from
	 *
	 * @return array
	 */
	public function get_suggestions( $query ) {

		$results = array();

		$client = $this->solarium_client;


		$suggestqry = $client->createSuggester();
		$suggestqry->setHandler( 'suggest' );
		$suggestqry->setDictionary( 'suggest' );
		$suggestqry->setQuery( $query );
		$suggestqry->setCount( 5 );
		$suggestqry->setCollate( true );
		$suggestqry->setOnlyMorePopular( true );

		$resultset = $client->execute( $suggestqry );

		foreach ( $resultset as $term => $termResult ) {

			foreach ( $termResult as $result ) {

				array_push( $results, $result );
			}
		}

		return $results;
	}

	/**
	 * Retrieve or create the search page
	 */
	static function get_search_page() {

		// Let other plugins (POLYLANG, ...) modify the search page slug
		$search_page_slug = apply_filters( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_SLUG, self::_SEARCH_PAGE_SLUG );

		// Search page is found by it's path (hard-coded).
		$search_page = get_page_by_path( $search_page_slug );

		if ( ! $search_page ) {

			$search_page = self::create_default_search_page();

		} else {

			if ( $search_page->post_status != 'publish' ) {

				$search_page->post_status = 'publish';

				wp_update_post( $search_page );
			}
		}


		return $search_page;
	}


	/**
	 * Create a default search page
	 *
	 * @return WP_Post The search page
	 */
	static function create_default_search_page() {

		// Let other plugins (POLYLANG, ...) modify the search page slug
		$search_page_slug = apply_filters( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_SLUG, self::_SEARCH_PAGE_SLUG );

		$_search_page = array(
			'post_type'      => 'page',
			'post_title'     => 'Search Results',
			'post_content'   => '[solr_search_shortcode]',
			'post_status'    => 'publish',
			'post_author'    => 1,
			'comment_status' => 'closed',
			'post_name'      => $search_page_slug
		);

		// Let other plugins (POLYLANG, ...) modify the search page
		$_search_page = apply_filters( WPSOLR_Filters::WPSOLR_FILTER_BEFORE_CREATE_SEARCH_PAGE, $_search_page );

		$search_page_id = wp_insert_post( $_search_page );

		update_post_meta( $search_page_id, 'bwps_enable_ssl', '1' );

		return get_post( $search_page_id );
	}

	/**
	 * Get all sort by options available
	 *
	 * @param string $sort_code_to_retrieve
	 *
	 * @return array
	 */
	public static function get_sort_option_from_code( $sort_code_to_retrieve, $sort_options = null ) {

		if ( $sort_options == null ) {
			$sort_options = self::get_sort_options();
		}

		if ( $sort_code_to_retrieve != null ) {
			foreach ( $sort_options as $sort ) {

				if ( $sort['code'] === $sort_code_to_retrieve ) {
					return $sort;
				}
			}
		}


		return null;
	}

	/**
	 * Convert a $wpsolr_query in a Solarium select query
	 *
	 * @param WPSOLR_Query $wpsolr_query
	 *
	 * @return Query
	 */
	public function set_solarium_query( WPSOLR_Query $wpsolr_query ) {

		// Create the solarium query
		$solarium_query = $this->solarium_client->createSelect();

		// Set the query keywords.
		$this->set_keywords( $solarium_query, $wpsolr_query->get_wpsolr_query(), WPSOLR_Global::getOption()->get_search_is_query_partial_match_begin_with() );

		// Set default operator
		$test = WPSOLR_Global::getOption()->get_search_query_default_operator();
		$solarium_query->setQueryDefaultOperator( WPSOLR_Global::getOption()->get_search_query_default_operator() );

		// Limit nb of results
		$solarium_query->setStart( $wpsolr_query->get_start() )->setRows( WPSOLR_Global::getOption()->get_search_max_nb_results_by_page() );

		/*
		* Add sort field(s)
		*/
		$this->add_sort_field( $solarium_query, $wpsolr_query->get_wpsolr_sort() );

		/*
		* Add query fields
		*/
		if ( ! empty( $wpsolr_query->get_filter_query_fields() ) ) {
			$this->add_filter_query_fields( $solarium_query, $wpsolr_query->get_filter_query_fields(), $wpsolr_query->get_wpsolr_facets_fields(), self::SOLR_EXCLUDE_ALL_TAG );
		}

		/*
		* Add facets group query fields
		*/
		if ( ! empty( $wpsolr_query->get_wpsolr_facets_group_filter_query() ) ) {
			$this->add_filter_query_fields( $solarium_query, [ $wpsolr_query->get_wpsolr_facets_group_filter_query() ], $wpsolr_query->get_wpsolr_facets_fields(), self::SOLR_NOT_EXCLUDE_ALL_TAG );
		}

		/*
		* Add highlighting fields
		*/
		$this->add_highlighting_fields( $solarium_query,
			array(
				self::PARAMETER_HIGHLIGHTING_FIELD_NAMES   => array(
					WPSOLR_Schema::_FIELD_NAME_TITLE,
					WPSOLR_Schema::_FIELD_NAME_CONTENT,
					WPSOLR_Schema::_FIELD_NAME_COMMENTS
				),
				self::PARAMETER_HIGHLIGHTING_FRAGMENT_SIZE => WPSOLR_Global::getOption()->get_search_max_length_highlighting(),
				self::PARAMETER_HIGHLIGHTING_PREFIX        => self::DEFAULT_HIGHLIGHTING_PREFIX,
				self::PARAMETER_HIGHLIGHTING_POSTFIX       => self::DEFAULT_HIGHLIGHTING_POSFIX
			)
		);

		/*
		 * Add facet fields
		 */
		$this->add_facet_fields( $solarium_query,
			array(
				self::PARAMETER_FACET_FIELD_NAMES           => $wpsolr_query->get_wpsolr_facets_fields(),
				self::PARAMETER_FACET_LIMIT                 => WPSOLR_Global::getOption()->get_search_max_nb_items_by_facet(),
				self::PARAMETER_FACET_MIN_COUNT             => self::DEFAULT_MIN_COUNT_BY_FACET,
				self::PARAMETER_IS_EXCLUSION_BETWEEN_FACETS => WPSOLR_Global::getExtensionFacets()->get_facets_group_is_exlusion( $wpsolr_query->get_wpsolr_facets_groups_id() )
			)
		);

		/*
		 * Add fields
		 */
		$this->add_fields( $solarium_query );


		// Filter to change the solarium query
		do_action( WPSOLR_Filters::WPSOLR_ACTION_SOLARIUM_QUERY,
			array(
				WPSOLR_Filters::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SOLARIUM_QUERY => $solarium_query,
				WPSOLR_Filters::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SEARCH_TERMS   => $wpsolr_query->get_wpsolr_query(),
				WPSOLR_Filters::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SEARCH_USER    => wp_get_current_user(),
			)
		);

		// Done
		return $this->solarium_query = $solarium_query;
	}

	/**
	 * Execute a WPSOLR query.
	 *
	 * @param WPSOLR_Query $wpsolr_query
	 *
	 * @return ResultInterface
	 */
	public function execute_wpsolr_query( WPSOLR_Query $wpsolr_query ) {

		if ( isset( $this->solarium_results ) ) {
			// Return results already in cache
			return $this->solarium_results;
		}

		// Create the solarium query from the wpsolr query
		$this->set_solarium_query( $wpsolr_query );

		// Perform the query, return the Solarium result set
		return $this->execute_solarium_query();

	}

	/**
	 * Execute a Solarium query.
	 * Used internally, or when fine tuned solarium select query is better than using a WPSOLR query.
	 *
	 * @param Query $solarium_query
	 *
	 * @return ResultInterface
	 */
	public function execute_solarium_query( Query $solarium_query = null ) {

		// Perform the query, return the Solarium result set
		return $this->solarium_results = $this->solarium_client->execute( isset( $solarium_query ) ? $solarium_query : $this->solarium_query );
	}

	/**
	 *
	 * @param WPSOLR_Query $wpsolr_query
	 *
	 * @return array Array of html
	 */
	public function display_results( WPSOLR_Query $wpsolr_query ) {

		$output        = array();
		$search_result = array();

		// Load options
		$localization_options = WPSOLR_Localization::get_options();

		$resultset = $this->execute_wpsolr_query( $wpsolr_query );

		$found = $resultset->getNumFound();

		// No results: try a new query if did you mean is activated
		if ( ( $found === 0 ) && ( WPSOLR_Global::getOption()->get_search_is_did_you_mean() ) ) {

			// Add spellcheck to current solarium query
			$spell_check = $this->solarium_query->getSpellcheck();
			$spell_check->setCount( 10 );
			$spell_check->setCollate( true );
			$spell_check->setExtendedResults( true );
			$spell_check->setCollateExtendedResults( true );

			// Excecute the query modified
			$resultset = $this->execute_solarium_query();

			// Parse spell check results
			$spell_check_results = $resultset->getSpellcheck();
			if ( $spell_check_results && ! $spell_check_results->getCorrectlySpelled() ) {
				$collations          = $spell_check_results->getCollations();
				$queryTermsCorrected = $wpsolr_query->get_wpsolr_query(); // original query
				foreach ( $collations as $collation ) {
					foreach ( $collation->getCorrections() as $input => $correction ) {
						$queryTermsCorrected = str_replace( $input, is_array( $correction ) ? $correction[0] : $correction, $queryTermsCorrected );
					}

				}

				if ( $queryTermsCorrected != $wpsolr_query->get_wpsolr_query() ) {

					$err_msg         = sprintf( WPSOLR_Localization::get_term( $localization_options, 'results_header_did_you_mean' ), $queryTermsCorrected ) . '<br/>';
					$search_result[] = $err_msg;

					// Execute query with spelled terms
					$this->solarium_query->setQuery( $queryTermsCorrected );
					try {
						$resultset = $this->execute_solarium_query();
						$found     = $resultset->getNumFound();

					} catch ( Exception $e ) {
						// Sometimes, the spelling query returns errors
						// java.lang.StringIndexOutOfBoundsException: String index out of range: 15\n\tat java.lang.AbstractStringBuilder.charAt(AbstractStringBuilder.java:203)\n\tat
						// java.lang.StringBuilder.charAt(StringBuilder.java:72)\n\tat org.apache.solr.spelling.SpellCheckCollator.getCollation(SpellCheckCollator.java:164)\n\tat

						$found = 0;
					}

				} else {
					$search_result[] = 0;
				}

			} else {
				$search_result[] = 0;
			}

		} else {
			$search_result[] = 0;
		}

		// Retrieve facets from resultset
		$facets_to_display = $wpsolr_query->get_wpsolr_facets_fields();
		$special_fields    = WPSOLR_Global::getExtensionFacets()->get_special_fields();
		if ( count( $facets_to_display ) ) {
			$extension_facets      = WPSOLR_Global::getExtensionFacets();
			$extension_field_types = WPSOLR_Global::getSolrFieldTypes();
			$extension_fields      = WPSOLR_Global::getExtensionFields();

			foreach ( $facets_to_display as $field_name => $facet ) {

				$field_name = strtolower( $field_name );
				if ( WPSOLR_Schema::_FIELD_NAME_CATEGORIES === $field_name ) {
					$field_name_dynamic = WPSOLR_Schema::_FIELD_NAME_CATEGORIES_STR;

				} else if ( ! in_array( $field_name, $special_fields ) ) {

					// Add the solr type extension
					$field_name_dynamic = $extension_field_types->get_dynamic_name_from_dynamic_extension(
						$field_name,
						$extension_fields->get_field_type_definition( $field_name )->get_dynamic_type()
					);

				} else {

					$field_name_dynamic = $field_name;
				}

				if ( ! empty( $resultset->getFacetSet() ) ) {

					if ( $extension_facets->get_facet_is_custom_range_type( $facet ) ) {

						// Fetch all custom ranges of the facet definition
						$loop = 0;
						foreach ( $extension_facets->get_facet_custom_ranges( $facet ) as $facet_custom_range ) {

							$result_facet = $resultset->getFacetSet()->getFacet( 'query_' . "$field_name_dynamic" . '_' . $loop );

							if ( isset( $result_facet ) ) {
								$output[ $field_name ][] = array( $loop, $result_facet->getValue() );
							}

							$loop ++;
						}

					} else if ( $extension_facets->get_facet_is_min_max_type( $facet ) ) {

						// Extract statistics from results
						$result_stats_fields = $resultset->getStats();

						foreach ( $result_stats_fields as $result_stats_field ) {
							$output[ $field_name ][] = [
								$result_stats_field->getMin(),
								$result_stats_field->getMax(),
								$result_stats_field->getCount(),
								$result_stats_field->getMissing()
							];
						}

					} else {

						$result_facet = $resultset->getFacetSet()->getFacet( "$field_name_dynamic" );

						foreach ( ( ! empty( $result_facet ) ? $result_facet : [ ] ) as $value => $count ) {
							$output[ $field_name ][] = array( $value, $count );
						}

					}

				}

			}
			$search_result[] = $output;

		} else {
			$search_result[] = 0;
		}

		$search_result[] = $found;

		$results      = array();
		$highlighting = $resultset->getHighlighting();

		$i                    = 1;
		$cat_arr              = array();
		$are_comments_indexed = WPSOLR_Global::getOption()->get_fields_are_comments_indexed();
		foreach ( $resultset as $document ) {

			$id      = $document->id;
			$title   = $document->title;
			$content = '';

			$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $id ) );

			$no_comments = $document->numcomments;
			if ( $are_comments_indexed ) {
				$comments = $document->comments;
			}
			$date = date( 'm/d/Y', strtotime( $document->displaydate ) );

			if ( property_exists( $document, 'categories_str' ) ) {
				$cat_arr = $document->categories_str;
			}


			$cat  = implode( ',', $cat_arr );
			$auth = $document->author;

			$url = get_permalink( $id );

			$highlightedDoc = $highlighting->getResult( $document->id );
			$cont_no        = 0;
			$comm_no        = 0;
			if ( $highlightedDoc ) {

				foreach ( $highlightedDoc as $field => $highlight ) {

					if ( $field == WPSOLR_Schema::_FIELD_NAME_TITLE ) {

						$title = implode( ' (...) ', $highlight );

					} else if ( $field == WPSOLR_Schema::_FIELD_NAME_CONTENT ) {

						$content = implode( ' (...) ', $highlight );

					} else if ( $field == WPSOLR_Schema::_FIELD_NAME_COMMENTS ) {

						$comments = implode( ' (...) ', $highlight );
						$comm_no  = 1;

					}

				}

			}

			$msg = '';
			$msg .= "<div id='res$i'><div class='p_title'><a href='$url'>$title</a></div>";

			$image_fragment = '';
			// Display first image
			if ( is_array( $image_url ) && count( $image_url ) > 0 ) {
				$image_fragment .= "<img class='wdm_result_list_thumb' src='$image_url[0]' />";
			}

			if ( empty( $content ) ) {
				// Set a default value for content if no highlighting returned.
				$post_to_show = get_post( $id );
				if ( isset( $post_to_show ) ) {
					// Excerpt first, or content.
					$content = ( ! empty( $post_to_show->post_excerpt ) ) ? $post_to_show->post_excerpt : $post_to_show->post_content;

					if ( isset( $ind_opt['is_shortcode_expanded'] ) && ( strpos( $content, '[solr_search_shortcode]' ) === false ) ) {

						// Expand shortcodes which have a plugin active, and are not the search form shortcode (else pb).
						global $post;
						$post    = $post_to_show;
						$content = do_shortcode( $content );
					}

					// Remove shortcodes tags remaining, but not their content.
					// strip_shortcodes() does nothing, probably because shortcodes from themes are not loaded in admin.
					// Credit: https://wordpress.org/support/topic/stripping-shortcodes-keeping-the-content.
					// Modified to enable "/" in attributes
					$content = preg_replace( "~(?:\[/?)[^\]]+/?\]~s", '', $content );  # strip shortcodes, keep shortcode content;


					// Strip HTML and PHP tags
					$content = strip_tags( $content );

					if ( isset( $res_opt['highlighting_fragsize'] ) && is_numeric( $res_opt['highlighting_fragsize'] ) ) {
						// Cut content at the max length defined in options.
						$content = substr( $content, 0, $res_opt['highlighting_fragsize'] );
					}
				}
			}


			// Format content text a little bit
			$content = str_replace( '&nbsp;', '', $content );
			$content = str_replace( '  ', ' ', $content );
			$content = ucfirst( trim( $content ) );
			$content .= '...';

			//if ( $cont_no == 1 ) {
			if ( false ) {
				$msg .= "<div class='p_content'>$image_fragment $content - <a href='$url'>Content match</a></div>";
			} else {
				$msg .= "<div class='p_content'>$image_fragment $content</div>";
			}
			if ( $comm_no == 1 ) {
				$msg .= "<div class='p_comment'>" . $comments . "-<a href='$url'>Comment match</a></div>";
			}

			// Groups bloc - Bottom right
			$wpsolr_groups_message = apply_filters( WPSOLR_Filters::WPSOLR_FILTER_SOLR_RESULTS_DOCUMENT_GROUPS_INFOS, get_current_user_id(), $document );
			if ( isset( $wpsolr_groups_message ) ) {

				// Display groups of this user which owns at least one the document capability
				$message = $wpsolr_groups_message['message'];
				$msg .= "<div class='p_misc'>$message";
				$msg .= "</div>";
				$msg .= '<br/>';

			}

			// Informative bloc - Bottom right
			$msg .= "<div class='p_misc'>";
			$msg .= "<span class='pauthor'>" . sprintf( WPSOLR_Localization::get_term( $localization_options, 'results_row_by_author' ), $auth ) . "</span>";
			$msg .= empty( $cat ) ? "" : "<span class='pcat'>" . sprintf( WPSOLR_Localization::get_term( $localization_options, 'results_row_in_category' ), $cat ) . "</span>";
			$msg .= "<span class='pdate'>" . sprintf( WPSOLR_Localization::get_term( $localization_options, 'results_row_on_date' ), $date ) . "</span>";
			$msg .= empty( $no_comments ) ? "" : "<span class='pcat'>" . sprintf( WPSOLR_Localization::get_term( $localization_options, 'results_row_number_comments' ), $no_comments ) . "</span>";
			$msg .= "</div>";

			// End of snippet bloc
			$msg .= "</div><hr>";

			array_push( $results, $msg );
			$i = $i + 1;
		}
		//  $msg.='</div>';


		if ( count( $results ) < 0 ) {
			$search_result[] = 0;
		} else {
			$search_result[] = $results;
		}

		$fir = $wpsolr_query->get_start() + 1;

		$last = $wpsolr_query->get_start() + $wpsolr_query->get_nb_results_by_page();
		if ( $last > $found ) {
			$last = $found;
		}

		$search_result[] = "<span class='infor'>" . sprintf( WPSOLR_Localization::get_term( $localization_options, 'results_header_pagination_numbers' ), $fir, $last, $found ) . "</span>";


		return $search_result;
	}


	/**
	 * Add facet fields to the solarium query
	 *
	 * @param Query $solarium_query
	 * @param array $field_names
	 * @param int $max_nb_items_by_facet Maximum items by facet
	 * @param int $min_count_by_facet Do not return facet elements with less than this minimum count
	 */
	public
	function add_facet_fields(
		Query $solarium_query,
		$facets_parameters
	) {

		$extension_facets = WPSOLR_Global::getExtensionFacets();

		// Field names
		$fields = isset( $facets_parameters[ self::PARAMETER_FACET_FIELD_NAMES ] )
			? $facets_parameters[ self::PARAMETER_FACET_FIELD_NAMES ]
			: array();

		// Limit
		$limit = isset( $facets_parameters[ self::PARAMETER_FACET_LIMIT ] )
			? $facets_parameters[ self::PARAMETER_FACET_LIMIT ]
			: self::DEFAULT_MAX_NB_ITEMS_BY_FACET;

		// Min count
		$min_count_global = isset( $facets_parameters[ self::PARAMETER_FACET_MIN_COUNT ] )
			? $facets_parameters[ self::PARAMETER_FACET_MIN_COUNT ]
			: self::DEFAULT_MIN_COUNT_BY_FACET;

		// Exclusion between facets
		$is_exclusion_between_facets = isset( $facets_parameters[ self::PARAMETER_IS_EXCLUSION_BETWEEN_FACETS ] )
			? $facets_parameters[ self::PARAMETER_IS_EXCLUSION_BETWEEN_FACETS ]
			: false;

		$special_fields = WPSOLR_Global::getExtensionFacets()->get_special_fields();

		if ( count( $fields ) ) {

			$facetSet = $solarium_query->getFacetSet();

			foreach ( $fields as $field_name => $facet ) {

				$facet_min_count = $extension_facets->get_facet_min_count( $facet );

				$field_name = strtolower( $field_name );

				// Field 'categories' are now treated as other fields (dynamic string type)
				if ( WPSOLR_Schema::_FIELD_NAME_CATEGORIES === $field_name ) {

					$field_name = WPSOLR_Schema::_FIELD_NAME_CATEGORIES_STR;
				} else {

					if ( ! in_array( $field_name, $special_fields ) ) {
						// Add the solr type extension
						$field_name = WPSOLR_Global::getSolrFieldTypes()->get_dynamic_name_from_dynamic_extension(
							$field_name,
							WPSOLR_Global::getExtensionFields()->get_field_type_definition( $field_name )->get_dynamic_type()
						);
					}

				}

				// Add the facet
				if ( $extension_facets->get_facet_is_custom_range_type( $facet ) ) {

					// Set a query facet.
					// We don't use range intervals because it requires docValues and Solr 4.10

					$loop = 0;
					foreach ( $extension_facets->get_facet_custom_ranges( $facet ) as $facet_custom_range ) {

						$range_inf = $facet_custom_range[ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_INF ];
						$range_sup = $facet_custom_range[ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_SUP ];

						$solarium_facet = $facetSet->createFacetQuery( 'query_' . $field_name . '_' . $loop )->setQuery( "$field_name: [$range_inf TO $range_sup]" );

						// Display facet items not in results ?
						if ( $extension_facets->get_is_facet_in_exclusion_tag( $facet ) ) {

							// Exclude the tag corresponding to this facet. The tag was set on addFilterQuery().
							$solarium_facet->setExcludes( [ $field_name ] );
						}

						$loop ++;
					}

				} else if ( $extension_facets->get_facet_is_min_max_type( $facet ) ) {

					// Add statistics to this field to get min/max results
					$stats = $solarium_query->getStats();
					$stats->createField( "$field_name" );

				} else {

					if ( $extension_facets->get_facet_is_range_type( $facet ) ) {

						// Set a range facet
						$solarium_facet = $facetSet->createFacetRange( "$field_name" );
						$solarium_facet->setStart( $extension_facets->get_facet_range_start( $facet ) );
						$solarium_facet->setEnd( $extension_facets->get_facet_range_end( $facet ) );
						$solarium_facet->setGap( $extension_facets->get_facet_range_gap( $facet ) );

						$solarium_facet->setField( "$field_name" );

						// Set minimum count for this facet
						$solarium_facet->setMinCount( $facet_min_count );

					} else {

						// Set a field facet
						$solarium_facet = $facetSet->createFacetField( "$field_name" );
						$solarium_facet->setLimit( $limit );
						$solarium_facet->setSort( $extension_facets->get_facet_sort( $facet ) );

						$solarium_facet->setField( "$field_name" );

						// Set minimum count for this facet
						$solarium_facet->setMinCount( $facet_min_count );
					}

					// Display facet items not in results ?
					if ( $extension_facets->get_is_facet_in_exclusion_tag( $facet ) ) {

						// Exclude the tag corresponding to this facet. The tag was set on addFilterQuery().
						$solarium_facet->setExcludes( [ $field_name ] );
					}

				}

			}

		}


		// Exclude between facets
		if ( $is_exclusion_between_facets ) {

			foreach ( $solarium_query->getFacetSet()->getFacets() as $solarium_facet ) {

				$solarium_facet->addExclude( self::SOLR_EXCLUDE_ALL_TAG );
			}
		}


		return;
	}

	/**
	 * Add highlighting fields to the solarium query
	 *
	 * @param Query $solarium_query
	 * @param array $highlighting_parameters
	 */
	public
	function add_highlighting_fields(
		Query $solarium_query,
		$highlighting_parameters
	) {

		// Field names
		$field_names = isset( $highlighting_parameters[ self::PARAMETER_HIGHLIGHTING_FIELD_NAMES ] )
			? $highlighting_parameters[ self::PARAMETER_HIGHLIGHTING_FIELD_NAMES ]
			: array(
				WPSOLR_Schema::_FIELD_NAME_TITLE,
				WPSOLR_Schema::_FIELD_NAME_CONTENT,
				WPSOLR_Schema::_FIELD_NAME_COMMENTS
			);

		// Fragment size
		$fragment_size = isset( $highlighting_parameters[ self::PARAMETER_HIGHLIGHTING_FRAGMENT_SIZE ] )
			? $highlighting_parameters[ self::PARAMETER_HIGHLIGHTING_FRAGMENT_SIZE ]
			: self::DEFAULT_HIGHLIGHTING_FRAGMENT_SIZE;

		// Prefix
		$prefix = isset( $highlighting_parameters[ self::PARAMETER_HIGHLIGHTING_PREFIX ] )
			? $highlighting_parameters[ self::PARAMETER_HIGHLIGHTING_PREFIX ]
			: self::DEFAULT_HIGHLIGHTING_PREFIX;

		// Postfix
		$postfix = isset( $highlighting_parameters[ self::PARAMETER_HIGHLIGHTING_POSTFIX ] )
			? $highlighting_parameters[ self::PARAMETER_HIGHLIGHTING_POSTFIX ]
			: self::DEFAULT_HIGHLIGHTING_POSFIX;

		$highlighting = $solarium_query->getHighlighting();

		foreach ( $field_names as $field_name ) {

			$highlighting->getField( $field_name )->setSimplePrefix( $prefix )->setSimplePostfix( $postfix );

			// Max size of each highlighting fragment for post content
			$highlighting->getField( $field_name )->setFragSize( $fragment_size );
		}

	}

	/**
	 * Ping the Solr index
	 */
	public
	function ping() {

		$this->solarium_client->ping( $this->solarium_client->createPing() );
	}

	/**
	 * Add filter query fields to the solarium query
	 *
	 * @param Query $solarium_query
	 * @param array $filter_query_fields
	 * @param $facets_parameters
	 */
	private
	function add_filter_query_fields(
		Query $solarium_query, $filter_query_fields, $facets_parameters, $exclusion_tag
	) {

		$special_fields = WPSOLR_Global::getExtensionFacets()->get_special_fields();

		$facets_or = [ ];

		foreach ( $filter_query_fields as $filter_query_field ) {

			// Is it a filter query, coming from a facet with a 'OR'
			$is_facet_or             = false;
			$filter_query_field_name = explode( ':', $filter_query_field )[0];
			if ( isset( $facets_parameters[ $filter_query_field_name ] ) ) {

				$is_facet_or = WPSOLR_Global::getExtensionFacets()->get_facet_is_query_operator_or( $facets_parameters[ $filter_query_field_name ] );
			}

			if ( ! empty( $filter_query_field ) ) {

				$matches = WPSOLR_Regexp::extract_filter_query( $filter_query_field );

				foreach ( $matches as $match ) {

					$filter_query_field_array = explode( ':', $match );

					$filter_query_field_name  = strtolower( $filter_query_field_array[0] );
					$filter_query_field_value = isset( $filter_query_field_array[1] ) ? $filter_query_field_array[1] : '';


					if ( ! empty( $filter_query_field_name ) && ( ! empty( $filter_query_field_value ) || $filter_query_field_value === '0' ) ) {

						// Retrieve field type
						$field_definition = WPSOLR_Global::getExtensionFields()->get_field_type_definition( $filter_query_field_name );

						$fac_fd = "$filter_query_field_name";
						if ( WPSOLR_Schema::_FIELD_NAME_CATEGORIES === $filter_query_field_name ) {

							$filter_query_field_name = WPSOLR_Schema::_FIELD_NAME_CATEGORIES_STR;
							$fac_fd                  = "$filter_query_field_name";
							$filter_query_field      = "$filter_query_field_name:$filter_query_field_value";

							if ( $is_facet_or ) {

								// Remember the OR query field for later
								$facets_or[ $fac_fd ][] = $filter_query_field;
							}

						} else {

							if ( ! in_array( $filter_query_field_name, $special_fields ) ) {
								// Add the solr type extension
								$fac_fd = WPSOLR_Global::getSolrFieldTypes()->get_dynamic_name_from_dynamic_extension(
									$filter_query_field_name,
									WPSOLR_Global::getExtensionFields()->get_field_type_definition( $filter_query_field_name )->get_dynamic_type()
								);
							}

							if ( ! $field_definition->get_is_numeric() ) {
								$filter_query_field_value_escaped = $filter_query_field_value;

								// Escape special characters
								$filter_query_field_value_escaped = WPSOLR_Regexp::remove_string_at_the_end( $filter_query_field_value_escaped, "\"" );
								$filter_query_field_value_escaped = WPSOLR_Regexp::remove_string_at_the_begining( $filter_query_field_value_escaped, "\"" );

								// In case the facet contains white space or special caracters, we enclose it with ""
								$filter_query_field_value_escaped = "\"$filter_query_field_value_escaped\"";

							} else {

								// Create range [0 TO 10]
								$filter_query_field_value_escaped = $filter_query_field_value;
							}

							$filter_query_field = str_replace( "$filter_query_field_name:$filter_query_field_value", "$fac_fd:$filter_query_field_value_escaped", $filter_query_field );

							if ( $is_facet_or ) {

								// Remember the OR query field for later
								$facets_or[ $fac_fd ][] = $filter_query_field;
							}

						}
					}
				}

				if ( ! $is_facet_or ) {

					$solarium_query->addFilterQuery( array(
						'key'   => "$filter_query_field",
						'query' => "$filter_query_field",
						'tag'   => [ $exclusion_tag, "$fac_fd" ]
					) );

				}

				// Add statistics to this field to get min/max results
				/*$stats = $solarium_query->getStats();
				$stats->createField( "{!test}acf_integer_i" );
				$solarium_query->addFilterQuery( array(
					'key'   => "acf_integer_i:*",
					'query' => "acf_integer_i:*",
					'tag'   => "test"
				) );*/

			}
		}

		// Add the query fields with a OR operator
		foreach ( $facets_or as $fac_fd => $filter_query_fields ) {

			$filter_query_field_or = '';
			foreach ( $filter_query_fields as $filter_query_field ) {

				$filter_query_field_or .= ( ! empty( $filter_query_field_or ) ? ' ' . Query::QUERY_OPERATOR_OR . ' ' : '' ) . $filter_query_field;
			}

			if ( ! empty( $filter_query_field_or ) ) {

				$solarium_query->addFilterQuery( array(
					'key'   => "$fac_fd",
					'query' => "$filter_query_field_or",
					'tag'   => [ $exclusion_tag, "$fac_fd" ]
				) );
			}

		}

		return;
	}

	/**
	 * Add sort field to the solarium query
	 *
	 * @param Query $solarium_query
	 * @param string $sort_field_name
	 */
	private
	function add_sort_field(
		Query $solarium_query, $sort_field_name = WPSOLR_Options_Sorts::SORT_CODE_BY_RELEVANCY_DESC
	) {

		switch ( $sort_field_name ) {

			case WPSOLR_Options_Sorts::SORT_CODE_BY_DATE_DESC:
				$solarium_query->addSort( WPSOLR_Schema::_FIELD_NAME_DATE, Query::SORT_DESC );
				break;

			case WPSOLR_Options_Sorts::SORT_CODE_BY_DATE_ASC:
				$solarium_query->addSort( WPSOLR_Schema::_FIELD_NAME_DATE, Query::SORT_ASC );
				break;

			case WPSOLR_Options_Sorts::SORT_CODE_BY_NUMBER_COMMENTS_DESC:
				$solarium_query->addSort( WPSOLR_Schema::_FIELD_NAME_NUMBER_OF_COMMENTS, Query::SORT_DESC );
				break;

			case WPSOLR_Options_Sorts::SORT_CODE_BY_NUMBER_COMMENTS_ASC:
				$solarium_query->addSort( WPSOLR_Schema::_FIELD_NAME_NUMBER_OF_COMMENTS, Query::SORT_ASC );
				break;

			case WPSOLR_Options_Sorts::SORT_CODE_BY_RELEVANCY_DESC:
			case "":
				// Nothing to do, as relevancy is Solr default sort
				break;

			default:
				// Custom field
				$special_fields = WPSOLR_Global::getExtensionFacets()->get_special_fields();

				$sort_field_name_for_solr = $sort_field_name;
				if ( ! in_array( $sort_field_name_for_solr, $special_fields ) ) {

					// Strip the postix from the field name
					$sort_field_name_for_solr = WPSOLR_Global::getExtensionSorts()->get_field_name_without_postfix( $sort_field_name_for_solr );

					// Add the solr type extension
					$sort_field_name_for_solr = WPSOLR_Global::getSolrFieldTypes()->get_dynamic_name_from_dynamic_extension(
						$sort_field_name_for_solr,
						WPSOLR_Global::getExtensionFields()->get_field_type_definition( $sort_field_name_for_solr )->get_dynamic_type()
					);
				}

				$order_by = WPSOLR_Global::getExtensionSorts()->get_sort_order_by( $sort_field_name );
				$solarium_query->addSort( $sort_field_name_for_solr, $order_by );
				break;

		}

	}

	/**
	 * Set fields returned by the query.
	 * We do not ask for 'content', because it can be huge for attachments, and is anyway replaced by highlighting.
	 *
	 * @param Query $solarium_query
	 * @param array $field_names
	 */
	private
	function add_fields(
		Query $solarium_query,
		$field_names = array(
			WPSOLR_Schema::_FIELD_NAME_ID,
			WPSOLR_Schema::_FIELD_NAME_TITLE,
			WPSOLR_Schema::_FIELD_NAME_NUMBER_OF_COMMENTS,
			WPSOLR_Schema::_FIELD_NAME_COMMENTS,
			WPSOLR_Schema::_FIELD_NAME_DISPLAY_DATE,
			WPSOLR_Schema::_FIELD_NAME_CATEGORIES,
			WPSOLR_Schema::_FIELD_NAME_AUTHOR
		)
	) {
		$solarium_query->setFields( $field_names );
	}

	/**
	 * Set the query keywords.
	 *
	 * @param Query $solarium_query
	 * @param string $keywords
	 */
	private
	function set_keywords(
		Query $solarium_query, $keywords, $is_partial_matching
	) {

		if ( $is_partial_matching ) {
			// Add '*' to each world of the query string.
			// 'word1  word2 ' => 'word1*  word2* '
			$keywords = preg_replace( '/(\w+)/i', '$1*', $keywords );
		}

		$solarium_query->setQuery( WPSOLR_Schema::_FIELD_NAME_DEFAULT_QUERY . ':' . $keywords );
	}

}
