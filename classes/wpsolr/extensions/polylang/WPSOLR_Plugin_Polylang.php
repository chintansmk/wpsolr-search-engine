<?php

namespace wpsolr\extensions\polylang;

use wpsolr\extensions\wpml\WPSOLR_Plugin_Wpml;
use wpsolr\services\WPSOLR_Service_Polylang;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\solr\WPSOLR_SearchSolrClient;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;
use wpsolr\WPSOLR_Filters;

/**
 * Class WPSOLR_Plugin_Polylang
 *
 * Manage Polylang plugin
 * @link https://polylang.wordpress.com/documentation/
 */
class WPSOLR_Plugin_Polylang extends WPSOLR_Plugin_Wpml {

	/*
	 * Polylang database constants
	 */
	const TABLE_TERM_RELATION_SHIPS = "term_relationships";

	/**
	 * After constructor.
	 */
	protected function post_constructor() {

		// WPML actions/filters
		parent::post_constructor();

		/**
		 * Filters/Actions
		 */
		WPSOLR_Service_Wordpress::add_filter( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_SLUG, array(
			$this,
			'get_search_page_slug',
		), 10, 1 );

		WPSOLR_Service_Wordpress::add_action( WPSOLR_Filters::WPSOLR_ACTION_TRANSLATION_REGISTER_STRINGS, array(
			$this,
			'register_translation_strings',
		), 10, 1 );

		WPSOLR_Service_Wordpress::add_filter( WPSOLR_Filters::WPSOLR_FILTER_TRANSLATION_STRING, array(
			$this,
			'get_translation_string',
		), 10, 1 );

		WPSOLR_Service_Wordpress::add_filter( WPSOLR_Filters::WPSOLR_FILTER_HOME_URL, array(
			$this,
			'get_home_url',
		), 10, 1 );

		WPSOLR_Service_Wordpress::add_filter( WPSOLR_Filters::WPSOLR_FILTER_GET_LANGUAGES, array(
			$this,
			'get_languages',
		), 10, 1 );

	}

	/**
	 * Display admin form.
	 *
	 * @param array $plugin_parameters Parameters
	 */
	public function output_form( $form_file = null, $plugin_parameters = [ ] ) {

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options' => WPSOLR_Global::getOption()->get_option_plugin_polylang(
						[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE => '0' ]
					)
				],
				$plugin_parameters
			)
		);
	}

	/**
	 * Customize the sql query statements.
	 * Add a join with the current indexing language
	 *
	 * @param $sql_statements
	 *
	 * @return mixed
	 */
	function set_sql_query_statement( $sql_statements, $parameters ) {
		global $wpdb;

		// Get the index indexing language
		$language = $this->get_solr_index_indexing_language( $parameters['index_indice'] );

		// Get the languages
		$languages = $this->get_languages();

		// Retrieve the term_id used for this language code
		if ( isset( $languages[ $language ]['term_id'] ) ) {

			$language_term_id = $languages[ $language ]['term_id'];
			$term             = get_term( $language_term_id, 'language' );
			if ( isset( $term ) ) {
				$term_taxonomy_id = $term->term_taxonomy_id;

				if ( isset( $language ) ) {

					// Join statement
					$sql_joint_statement = ' JOIN ';
					$sql_joint_statement .= $wpdb->prefix . self::TABLE_TERM_RELATION_SHIPS . ' AS ' . 'wp_term_relationships';
					$sql_joint_statement .= " ON posts.ID = wp_term_relationships.object_id AND wp_term_relationships.term_taxonomy_id = '%s' ";

					$sql_statements['JOIN'] = sprintf( $sql_joint_statement, $term_taxonomy_id );
				}
			}
		}

		return $sql_statements;
	}

	/**
	 * Get current language code
	 *
	 * @return string Current language code
	 */
	function get_current_language_code() {

		return pll_current_language( 'slug' );

	}

	/**
	 * Get default language code
	 *
	 * @return string Default language code
	 */
	function get_default_language_code() {

		return pll_default_language( 'slug' );

	}

	/**
	 * Get the language of a post
	 *
	 * @return string Post language code
	 */
	function filter_get_post_language( $language_code, $post ) {

		$post_language = isset( $post ) ? pll_get_post_language( $post->ID, 'slug' ) : null;

		return $post_language;
	}

	/**
	 * Get active language codes
	 *
	 * @return array Language codes
	 */
	function get_languages() {

		if ( isset( $this->languages ) ) {
			// Use value
			return $this->languages;
		}

		$result = array();

		$languages = WPSOLR_Service_Polylang::pll_languages_list( array( 'fields' => '' ) );

		// Fill the result
		if ( ! empty( $languages ) ) {
			foreach ( $languages as $language ) {

				$result[ $language->slug ] = array(
					'language_code' => $language->slug,
					'active'        => true,
					'term_id'       => $language->term_id
				);

			}
		}

		return $this->languages = $result;
	}


	/**
	 * Define the sarch page url for the current language
	 *
	 * @param $default_search_page_id
	 * @param $default_search_page_url
	 *
	 * @return string
	 */
	function set_search_page_url( $default_search_page_url, $default_search_page_id ) {
		global $polylang;

		$current_language_code = $this->get_current_language_code();

		// Get search page in current language
		$default_search_page_id_translated = pll_get_post( $default_search_page_id, $current_language_code );

		if ( ! $default_search_page_id_translated ) {

			// Create a new search page for the translation
			$default_search_page = WPSOLR_SearchSolrClient::create_default_search_page();

			// Retrieve current search page translations
			$translations = $polylang->model->get_translations( 'post', $default_search_page_id );

			// Add current translation to translations
			$translations[ $current_language_code ] = $default_search_page->ID;

			// Save translations
			pll_save_post_translations( $translations );

		}

		$result = ( $default_search_page_id === $default_search_page_id_translated ) ? $default_search_page_url : get_permalink( $default_search_page_id_translated );

		return $result;
	}

	function get_search_page_slug( $slug = null ) {

		// POLYLANG cannot accept 2 pages with the same slug.
		// So, add the language to the slug.
		return WPSOLR_SearchSolrClient::_SEARCH_PAGE_SLUG . "-" . $this->get_current_language_code();
	}

	function get_home_url() {

		return WPSOLR_Service_Polylang::pll_home_url();
	}

	/**
	 * Retrieve index indices
	 *
	 * @return array
	 */
	function get_solr_index_indices() {

		return WPSOLR_Global::getOption()->get_plugin_polylang_solr_index_indices();
	}


	/**
	 * Register translation strings to POLYLANG translatable strings
	 *
	 * @param $parameters ["translations" => [ ["name" => "name1", "text" => "text 1", "is_multiligne" => true] ]
	 */
	function register_translation_strings( $parameters ) {

		foreach ( $parameters['translations'] as $text_to_add ) {

			WPSOLR_Service_Polylang::pll_register_string( $text_to_add['name'], $text_to_add['text'], $text_to_add['domain'], $text_to_add['is_multiligne'] );
		}

		return;
	}

	/**
	 * Add translation strings to POLYLANG translatable strings
	 *
	 * @param $parameters ["translations" => [ ["name" => "name1", "text" => "text 1", "is_multiligne" => true] ]
	 */
	function get_translation_string( $string ) {

		$result = WPSOLR_Service_Polylang::pll__( $string );

		return $result;
	}
}