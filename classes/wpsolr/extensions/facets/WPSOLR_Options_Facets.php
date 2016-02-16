<?php

namespace wpsolr\extensions\facets;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\solr\WPSOLR_Field_Types;
use wpsolr\solr\WPSOLR_Schema;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;
use wpsolr\utilities\WPSOLR_Regexp;

/**
 * Class WPSOLR_Options_Facets
 *
 * Manage Facets
 */
class WPSOLR_Options_Facets extends WPSOLR_Extensions {

	// Facet types
	const FACET_TYPE_FIELD = 'facet_field';
	const FACET_TYPE_RANGE = 'facet_range';
	const FACET_TYPE_CUSTOM_RANGE = 'facet_range_custom';
	const FACET_TYPE_MIN_MAX = 'facet_min_max';

	// Facet type field
	const FACET_FIELD_TYPE = 'type';

	// Layout of the facet
	const FACET_FIELD_FACET_LAYOUT_ID = 'layout_id';

	// Layout of the facet filter
	const FACET_FIELD_FILTER_LAYOUT_ID = 'filter_layout_id';

	// Facet minimum count to show an element
	const FACET_FIELD_MIN_COUNT = 'min_count';

	// Facet delay in ms before javascript triggers a search after a user clicks on the facet
	const FACET_FIELD_JS_REFRESH_DELAY_IN_MS = 'js_refresh_delay_in_ms';

	// Label of facet showed on front-end (translated as string)
	const FACET_FIELD_LABEL_FRONT_END = 'label_front_end';

	// Facet labels
	const FACET_FIELD_LABEL = 'label'; // Facet label
	const FACET_FIELD_LABEL_FIRST = 'label_first'; // Label of the first label element
	const FACET_FIELD_LABEL_LAST = 'label_last'; // Label of the last label element

	// Facet labels templates
	const FACET_LABEL_TEMPLATE = '%1$s (%2$s)';
	const FACET_LABEL_TEMPLATE_RANGE = '%1$s - %2$s (%3$d)';
	const FACET_LABEL_TEMPLATE_MIN_MAX = 'From %1$s to %2$s (%3$d)';

	// Facet sort
	const FACET_FIELD_SORT = 'sort';
	const FACET_SORT_ALPHABETICAL = 'index';
	const FACET_SORT_COUNT = 'count';

	// Is a facet in an exclusion tag (show misssing elements)
	const FACET_FIELD_IS_EXCLUSION = 'missing';

	// Operator between elements of a facet
	const FACET_FIELD_QUERY_OPERATOR = 'elements_operator';

	// Facet range
	const FACET_FIELD_RANGE = 'range';
	const FACET_FIELD_RANGE_START = 'start';
	const FACET_FILED_RANGE_START_DEFAULT = '0';
	const FACET_FIELD_RANGE_END = 'end';
	const FACET_FIELD_RANGE_END_DEFAULT = '100';
	const FACET_FIELD_RANGE_GAP = 'gap';
	const FACET_FIELD_RANGE_GAP_DEFAULT = '10';

	// Facet custom range
	const FACET_FIELD_CUSTOM_RANGE = 'custom_range';
	const FACET_FIELD_CUSTOM_RANGE_RANGES = 'custom_ranges';
	const FACET_FIELD_CUSTOM_RANGE_RANGES_DEFAULT = '0|10|%1$s - %2$s (%3$d)';
	const FACET_FIELD_CUSTOM_RANGE_INF = 'range_inf';
	const FACET_FIELD_CUSTOM_RANGE_SUP = 'range_sup';
	const FACET_FIELD_CUSTOM_RANGE_LABEL = 'range_label';

	// Facet min/max
	const FACET_FIELD_MIN_MAX = 'min_max';
	const FACET_FIELD_MIN_MAX_STEP = 'step';
	const FACET_FIELD_MIN_MAX_STEP_DEFAULT = '100';

	// Layouts available for each field type
	protected $layouts;

	/**
	 * Post constructor.
	 */
	protected function post_constructor() {

	}

	/**
	 * Display admin form.
	 *
	 * @param array $plugin_parameters Parameters
	 */
	public function output_form( $form_file = null, $plugin_parameters = [ ] ) {

		$new_facets_group_uuid = WPSOLR_Global::getExtensionIndexes()->generate_uuid();

		// Clone some groups
		$facets_selected = WPSOLR_Global::getOption()->get_facets_selected_array();
		$groups          = $this->clone_some_groups( WPSOLR_Global::getOption()->get_facets_groups(), $facets_selected );

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'                   => WPSOLR_Global::getOption()->get_option_facet(
						[ WPSOLR_Option::OPTION_FACETS_FACETS => '' ]
					),
					'layouts_facets'            => $this->get_facets_layouts_by_field_types(),
					'layouts_filters'           => WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET_FILTER ),
					'default_facets_group_uuid' => $this->get_default_facets_group_id(),
					'new_facets_group_uuid'     => $new_facets_group_uuid,
					'facets_groups'             => array_merge(
						$groups,
						[
							$new_facets_group_uuid => [
								'name'                                          => 'New group',
								WPSOLR_Option::OPTION_FACETS_GROUP_FILTER_QUERY => ''
							]
						] ),
					'facets_selected'           => $facets_selected,
					'fields'                    => array_merge(
						WPSOLR_Field_Types::add_fields_type( $this->get_special_fields(), WPSOLR_Field_Types::SOLR_TYPE_STRING ),
						WPSOLR_Global::getOption()->get_fields_custom_fields_array(),
						WPSOLR_Field_Types::add_fields_type( WPSOLR_Global::getOption()->get_fields_taxonomies_array(), WPSOLR_Field_Types::SOLR_TYPE_STRING )
					),
					'image_plus'                => plugins_url( '../../../../images/plus.png', __FILE__ ),
					'image_minus'               => plugins_url( '../../../../images/success.png', __FILE__ )
				],
				$plugin_parameters
			)
		);
	}


	/**
	 * Get all layouts by field type
	 *
	 * @return array
	 */
	public function get_facets_layouts_by_field_types() {

		$results = [ ];

		$field_types_id = array_keys( WPSOLR_Global::getSolrFieldTypes()->get_field_types() );
		$layouts        = WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET );

		foreach ( $layouts as $layout_id => $layout ) {

			$layout_field_types = isset( $layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TYPES ] ) ? $layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TYPES ] : $field_types_id;

			foreach ( $layout_field_types as $layout_field_type ) {

				$results[ $layout_field_type ][ $layout_id ] = $layout;
			}
		}

		return $results;
	}

	/**
	 * Get facets of a facets group
	 *
	 * @param string $facets_group_id Group of facets
	 *
	 * @return array Facets of the group
	 */
	public function get_facets_from_group( $facets_group_id ) {

		$facets_groups = WPSOLR_Global::getOption()->get_facets_selected_array();

		return ! empty( $facets_groups[ $facets_group_id ] ) ? $facets_groups[ $facets_group_id ] : [ ];
	}

	/**
	 * Get exclusion flag of a facets group
	 *
	 * @param string $facets_group_id Group of facets
	 *
	 * @return array Facets of the group
	 */
	public function get_facets_group_is_exlusion( $facets_group_id ) {

		$facets_group = $this->get_facets_group( $facets_group_id );

		if ( isset( $facets_group ) ) {

			return isset( $facets_group[ WPSOLR_Option::OPTION_FACETS_GROUP_EXCLUSION ] );
		}

		return false;
	}

	/**
	 * Get facets of default group
	 *
	 * @return array Facets of default group
	 */
	public function get_facets_from_default_group() {

		$default_facets_group_id = WPSOLR_Global::getOption()->get_default_facets_group_id();

		if ( ! empty( $default_facets_group_id ) ) {
			return $this->get_facets_from_group( $default_facets_group_id );
		}

		return [ ];
	}

	/**
	 * Get facets group
	 *
	 * @@param string $facets_group_id
	 * @return array Facets group
	 */
	public function get_facets_group( $facets_group_id ) {

		$facets_groups = WPSOLR_Global::getOption()->get_facets_groups();

		if ( ! empty( $facets_group_id ) && ! empty( $facets_groups ) && ! empty( $facets_groups[ $facets_group_id ] ) ) {
			return $facets_groups[ $facets_group_id ];
		}

		return [ ];
	}

	/**
	 * Get facets group filter query
	 *
	 * @@param string $facets_group_id
	 * @return string Facets group filter query
	 */
	public function get_facets_group_filter_query( $facets_group_id ) {

		$facets_groups = $this->get_facets_group( $facets_group_id );

		if ( ! empty( $facets_groups ) && ! empty( $facets_groups[ WPSOLR_Option::OPTION_FACETS_GROUP_FILTER_QUERY ] ) ) {
			return $facets_groups[ WPSOLR_Option::OPTION_FACETS_GROUP_FILTER_QUERY ];
		}

		return '';
	}

	/**
	 * Get default facets group id
	 *
	 * @return string Default facets group id
	 */
	public function get_default_facets_group_id() {

		return WPSOLR_Global::getOption()->get_default_facets_group_id();
	}

	/**
	 * Get default facets group
	 *
	 * @return array Default facets group
	 */
	public function get_default_facets_group() {

		$default_facets_group_id = $this->get_default_facets_group_id();
		if ( ! empty( $default_facets_group_id ) ) {
			return $this->get_facets_group( $default_facets_group_id );
		}

		return [ ];
	}

	public function get_special_fields() {
		return [
			WPSOLR_Schema::_FIELD_NAME_TYPE,
			WPSOLR_Schema::_FIELD_NAME_AUTHOR,
			WPSOLR_Schema::_FIELD_NAME_CATEGORIES,
			WPSOLR_Schema::_FIELD_NAME_TAGS
		];
	}


	/**
	 * Get sort value of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_sort( $facet ) {
		return isset( $facet[ self::FACET_FIELD_SORT ] ) ? $facet[ self::FACET_FIELD_SORT ] : self::FACET_SORT_ALPHABETICAL;
	}

	/**
	 * Get facet type ?
	 *
	 * @param $facet
	 */
	public function get_facet_type( $facet ) {
		return $facet[ self::FACET_FIELD_TYPE ];
	}


	/**
	 * Get facet front-end label
	 *
	 * @param $facet
	 */
	public function get_facet_label_front_end( $facet ) {
		return isset( $facet[ self::FACET_FIELD_LABEL_FRONT_END ] ) ? $facet[ self::FACET_FIELD_LABEL_FRONT_END ] : '';
	}


	/**
	 * Is facet a field type ?
	 *
	 * @param $facet
	 */
	public function get_facet_is_field_type( $facet ) {
		return ( $this->get_facet_type( $facet ) == self::FACET_TYPE_FIELD );
	}

	/**
	 * Is facet a range type ?
	 *
	 * @param $facet
	 */
	public function get_facet_is_range_type( $facet ) {
		return ( $this->get_facet_type( $facet ) == self::FACET_TYPE_RANGE );
	}

	/**
	 * Is facet a min/max type ?
	 *
	 * @param $facet
	 */
	public function get_facet_is_min_max_type( $facet ) {
		return ( $this->get_facet_type( $facet ) == self::FACET_TYPE_MIN_MAX );
	}

	/**
	 * Is facet custom range type ?
	 *
	 * @param $facet
	 */
	public function get_facet_is_custom_range_type( $facet ) {
		return ( $this->get_facet_type( $facet ) == self::FACET_TYPE_CUSTOM_RANGE );
	}


	/**
	 * Get min max step of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_min_max_step( $facet ) {
		return isset( $facet[ self::FACET_FIELD_MIN_MAX ] ) && isset( $facet[ self::FACET_FIELD_MIN_MAX ][ self::FACET_FIELD_MIN_MAX_STEP ] ) ? $facet[ self::FACET_FIELD_MIN_MAX ][ self::FACET_FIELD_MIN_MAX_STEP ] : self::FACET_FIELD_MIN_MAX_STEP_DEFAULT;
	}

	/**
	 * Is facet a OR query ?
	 *
	 * @param $facet
	 */
	public function get_facet_is_query_operator_or( $facet ) {
		return isset( $facet[ self::FACET_FIELD_QUERY_OPERATOR ] ) ? ( Query::QUERY_OPERATOR_OR === $facet[ self::FACET_FIELD_QUERY_OPERATOR ] ) : false;
	}

	/**
	 * Get range start of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_range_start( $facet ) {
		return isset( $facet[ self::FACET_FIELD_RANGE ] ) && isset( $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_START ] ) ? $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_START ] : self::FACET_FILED_RANGE_START_DEFAULT;
	}

	/**
	 * Get range end of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_range_end( $facet ) {
		return isset( $facet[ self::FACET_FIELD_RANGE ] ) && isset( $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_END ] ) ? $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_END ] : self::FACET_FIELD_RANGE_END_DEFAULT;
	}

	/**
	 * Get range gap of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_range_gap( $facet ) {
		return isset( $facet[ self::FACET_FIELD_RANGE ] ) && isset( $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_GAP ] ) ? $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_GAP ] : self::FACET_FIELD_RANGE_GAP_DEFAULT;
	}

	/**
	 * Does a facet shows also items not in results ?
	 *
	 * @param $facet
	 */
	public function get_is_facet_in_exclusion_tag( $facet ) {
		return isset( $facet[ self::FACET_FIELD_IS_EXCLUSION ] );
	}

	/**
	 * Get min count to show a label
	 *
	 * @param $facet
	 */
	public function get_facet_min_count( $facet ) {
		return isset( $facet[ self::FACET_FIELD_MIN_COUNT ] ) ? $facet[ self::FACET_FIELD_MIN_COUNT ] : 1;
	}

	/**
	 * Get js delay in ms before a facet click triggers a page refresh
	 *
	 * @param $facet
	 */
	public function get_facet_field_js_refresh_delay_in_ms( $facet ) {
		return isset( $facet[ self::FACET_FIELD_JS_REFRESH_DELAY_IN_MS ] ) ? $facet[ self::FACET_FIELD_JS_REFRESH_DELAY_IN_MS ] : 0;
	}

	/**
	 * Get label of a facet element
	 *
	 * @param $facet
	 */
	public function get_facet_label( $facet ) {
		return isset( $facet[ self::FACET_FIELD_LABEL ] ) ? $facet[ self::FACET_FIELD_LABEL ] : '';
	}

	/**
	 * Get label of a facet first element
	 *
	 * @param $facet
	 */
	public function get_facet_label_first( $facet ) {
		return isset( $facet[ self::FACET_FIELD_LABEL_FIRST ] ) ? $facet[ self::FACET_FIELD_LABEL_FIRST ] : '';
	}

	/**
	 * Get label of a facet last element
	 *
	 * @param $facet
	 */
	public function get_facet_label_last( $facet ) {
		return isset( $facet[ self::FACET_FIELD_LABEL_LAST ] ) ? $facet[ self::FACET_FIELD_LABEL_LAST ] : '';
	}


	/**
	 * Get an array of custom ranges for a custom range facet
	 *
	 * 0|9|%1$s - %2$s (%3$d)
	 * 10|20|%1$s TO %2$s (%3$d)
	 * =>
	 * [
	 *  ['range_inf' => '0',  'range_sup' => '9',  'range_label' => '%1$s - %2$s (%3$d)']
	 *  ['range_inf' => '10', 'range_sup' => '20', 'range_label' => '%1$s TO %2$s (%3$d)']
	 * ]
	 *
	 * @param $facet
	 *
	 * @return array
	 */
	public function get_facet_custom_ranges( $facet ) {

		$results = [ ];

		$custom_ranges_string = isset( $facet[ self::FACET_FIELD_CUSTOM_RANGE ] ) && isset( $facet[ self::FACET_FIELD_CUSTOM_RANGE ][ self::FACET_FIELD_CUSTOM_RANGE_RANGES ] ) ? $facet[ self::FACET_FIELD_CUSTOM_RANGE ][ self::FACET_FIELD_CUSTOM_RANGE_RANGES ] : '';

		if ( ! empty( $custom_ranges_string ) ) {

			foreach ( WPSOLR_Regexp::split_lines( $custom_ranges_string ) as $custom_range_string ) {

				$custom_range = explode( '|', $custom_range_string );

				if ( count( $custom_range ) == 3 ) {

					$results[] = [
						self::FACET_FIELD_CUSTOM_RANGE_INF   => $custom_range[0],
						self::FACET_FIELD_CUSTOM_RANGE_SUP   => $custom_range[1],
						self::FACET_FIELD_CUSTOM_RANGE_LABEL => $custom_range[2]
					];
				}

			}
		}

		return $results;
	}


	/**
	 * Format a string translation
	 *
	 * @param $name
	 * @param $text
	 * @param $domain
	 * @param $is_multiligne
	 *
	 * @return array
	 */
	protected function get_string_to_translate( $name, $text, $domain, $is_multiligne ) {

		return [
			'name'          => $name,
			'text'          => $text,
			'domain'        => $domain,
			'is_multiligne' => $is_multiligne
		];
	}

	/**
	 * Get the strings to translate among the selected facets data
	 * @return array
	 */
	public function get_strings_to_translate() {

		$results = [ ];
		$domain  = 'wpsolr facets'; // never change this

		// Fields that can be translated and their definition
		$fields_translatable = [
			self::FACET_FIELD_LABEL_FRONT_END     => [ 'name' => 'Facet Label on front-end', 'is_multiline' => false ],
			self::FACET_FIELD_LABEL_FIRST         => [ 'name' => 'First facet Label', 'is_multiline' => false ],
			self::FACET_FIELD_LABEL               => [ 'name' => 'Middle facet Label', 'is_multiline' => false ],
			self::FACET_FIELD_LABEL_LAST          => [ 'name' => 'Last facet Label', 'is_multiline' => false ],
			self::FACET_FIELD_CUSTOM_RANGE_RANGES => [ 'name' => 'Uneven Range facet Labels', 'is_multiline' => true ]
		];

		$groups = WPSOLR_Global::getOption()->get_facets_selected_array();

		if ( ! empty( $groups ) ) {

			foreach ( $groups as $group_uuid => $group ) {

				foreach ( $group as $field ) {

					foreach ( $fields_translatable as $translatable_name => $translatable ) {

						if ( ! empty( $field[ $translatable_name ] ) ) {

							$results[] = $this->get_string_to_translate(
								$field[ $translatable_name ], //sprintf( '%s of %s %s', $translatable['name'], $this->get_facets_group( $facets_group_name )['name'], $facet_field['name'] ),
								$field[ $translatable_name ],
								$domain,
								$translatable['is_multiline']
							);
						}

						if ( ! empty( $field[ self::FACET_FIELD_CUSTOM_RANGE ] ) && ! empty( $field[ self::FACET_FIELD_CUSTOM_RANGE ][ $translatable_name ] ) ) {

							// Extract the 2rd column of each line
							$label = '';
							foreach ( WPSOLR_Regexp::split_lines( $field[ self::FACET_FIELD_CUSTOM_RANGE ][ $translatable_name ] ) as $line ) {

								if ( ! empty( trim( $line ) ) ) {

									$columns = explode( '|', $line );

									if ( count( $columns ) != 3 ) {

										set_transient( get_current_user_id() . 'wpsolr_generic_notice', sprintf( 'Range facet "%s" from group "%s" should contain 3 columns separated by "|", rather than %d in current value "%s"', $field['name'], $this->get_facets_group( $group_uuid )['name'], count( $columns ), $line ) );

										continue;
									}

									// The 3rd column contains the label to translate
									$label = $columns[2];

									$results[] = $this->get_string_to_translate(
										$label, //sprintf( ' % s of % s % s', $translatable['name'], $this->get_facets_group( $facets_group_name )['name'], $facet_field['name'] ),
										$label,
										$domain,
										$translatable['is_multiline']
									);

								}

							}
						}
					}
				}
			}
		}

		return $results;
	}

	/**
	 * Clone the groups marked.
	 *
	 * @param $facets_groups
	 */
	public function clone_some_groups( $facets_groups, &$facets_selected ) {

		foreach ( $facets_groups as $facets_group_uuid => &$facets_group ) {

			if ( ! empty( $facets_group['is_to_be_cloned'] ) ) {

				unset( $facets_group['is_to_be_cloned'] );

				// Clone the group
				$facets_group_cloned         = $facets_group;
				$facet_group_cloned_uuid     = WPSOLR_Global::getExtensionIndexes()->generate_uuid();
				$facets_group_cloned['name'] = 'Clone of ' . $facets_group_cloned['name'];

				$facets_groups[ $facet_group_cloned_uuid ] = $facets_group_cloned;

				// Now clone the group facets
				if ( ! empty( $facets_selected[ $facets_group_uuid ] ) ) {

					$facets_selected[ $facet_group_cloned_uuid ] = $facets_selected[ $facets_group_uuid ];

				}
			}

		}

		return $facets_groups;
	}

}