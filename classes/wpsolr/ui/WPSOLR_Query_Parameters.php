<?php

namespace wpsolr\ui;

use wpsolr\utilities\WPSOLR_Global;

/**
 * Manage search parameters, from url or Ajax.
 */
class WPSOLR_Query_Parameters {

	/**
	 * Search parameters used in url or ajax
	 */
	const SEARCH_PARAMETER_AJAX_URL_PARAMETERS = 'url_parameters';
	const SEARCH_PARAMETER_S = 's'; // Standard WP seach query
	const SEARCH_PARAMETER_SEARCH = 'search'; // Old query name, here for compatibility
	const SEARCH_PARAMETER_Q = 'wpsolr_q'; // New query name
	const SEARCH_PARAMETER_FQ = 'wpsolr_fq';
	const SEARCH_PARAMETER_PAGE = 'wpsolr_page';
	const SEARCH_PARAMETER_SORT = 'wpsolr_sort';
	const SEARCH_PARAMETER_FACETS_GROUP = 'wpsolr_facets_group';
	const SEARCH_PARAMETER_SORTS_GROUP = 'wpsolr_sorts_group';

	/**
	 * Copy url parameters to query.
	 */
	public static function copy_parameters_to_query( WPSOLR_Query $wpsolr_query, $url_parameters ) {

		if ( isset( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_Q ] ) ) {
			$wpsolr_query->set_wpsolr_query( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_Q ] );
		}

		if ( isset( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_FQ ] ) ) {
			$wpsolr_query->set_filter_query_fields( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_FQ ] );
		}

		if ( isset( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_PAGE ] ) ) {
			$wpsolr_query->set_wpsolr_paged( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_PAGE ] );
		}

		if ( isset( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_SORT ] ) ) {
			$wpsolr_query->set_wpsolr_sort( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_SORT ] );
		}

		if ( isset( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_FACETS_GROUP ] ) ) {
			$wpsolr_query->set_wpsolr_facets_groups_id( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_FACETS_GROUP ] );
		}

		if ( isset( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_SORTS_GROUP ] ) ) {
			$wpsolr_query->set_wpsolr_sorts_groups_id( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_SORTS_GROUP ] );
		}

	}

	/**
	 * Extract query from Ajax parameters.
	 * @return WPSOLR_Query
	 */
	public static function CreateQuery( WPSOLR_Query $wpsolr_query = null ) {

		$wpsolr_query = isset( $wpsolr_query ) ? $wpsolr_query : WPSOLR_Query::Create();

		if ( isset( $_POST[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_AJAX_URL_PARAMETERS ] ) ) {
			// It is an Ajax call

			// Parameters are in the url
			$url_parameters = ltrim( $_POST[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_AJAX_URL_PARAMETERS ], '?' );

			// Extract url parameters
			parse_str( $url_parameters, $url_parameters );

		} else {
			// It is a GET url

			// Extract all url parameters in an array
			$url_parameters = self::parse_query_string();

		}

		// Compatibility: copy old WPSOLR query and standard WP query in current WPSOLR query
		foreach (
			array(
				WPSOLR_Query_Parameters::SEARCH_PARAMETER_SEARCH,
				WPSOLR_Query_Parameters::SEARCH_PARAMETER_S
			) as $query_parameter
		) {
			if ( isset( $url_parameters[ $query_parameter ] ) ) {
				// Copy old parameter value to new parameter
				$url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_Q ] = $url_parameters[ $query_parameter ];
				unset( $url_parameters[ $query_parameter ] );
			}
		}

		// Copy url parameters to query
		self::copy_parameters_to_query( $wpsolr_query, $url_parameters );

		return $wpsolr_query;
	}

	/**
	 * Does the current url constain the standard WP search query ?
	 * @return bool
	 */
	public static function is_wp_search() {

		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( $_SERVER['QUERY_STRING'], $url_parameters );

			return isset( $url_parameters[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_S ] );
		}

		return false;
	}


	/**
	 * Parse the query string parameters
	 *
	 * @return bool
	 */
	public static function parse_query_string() {

		$url_parameters = array();

		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( $_SERVER['QUERY_STRING'], $url_parameters );
		}

		return $url_parameters;
	}


	/**
	 * Which query parameter to use: ?s= or ?wpsolr_q=
	 *
	 * @return bool
	 */
	public static function get_query_parameter_name() {

		// Extract all url parameters in an array
		$url_parameters = self::parse_query_string();

		if ( WPSOLR_Global::getOption()->get_search_is_use_current_theme_search_template() ) {
			// Option says it is a WP search parameter

			return self::SEARCH_PARAMETER_S;
		}

		// Default
		return self::SEARCH_PARAMETER_Q;
	}


	/**
	 * Do we need to replace wp search by WPSOLR's ?
	 * @return bool
	 */
	public static function is_replace_wp_search() {

		return ( WPSOLR_Query_Parameters::is_wp_search() && ! is_admin() && is_main_query()
		         && WPSOLR_Global::getOption()->get_search_is_replace_default_wp_search()
		         && WPSOLR_Global::getOption()->get_search_is_use_current_theme_search_template()
		);
	}

	/**
	 * What is the (full) current url ?
	 *
	 * @return string
	 */
	public static function get_current_page_url() {
		global $wp;

		return add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
	}

}