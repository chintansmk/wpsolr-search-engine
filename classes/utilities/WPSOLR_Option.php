<?php

/**
 * Manage options.
 */
class WPSOLR_Option {

	// Cache of options already retrieved from database.
	private $cached_options;

	/**
	 * WPSOLR_Option constructor.
	 */
	public function __construct() {
		$this->cached_options = array();

		/*
		add_filter( WpSolrFilters::WPSOLR_FILTER_AFTER_GET_OPTION_VALUE, array(
					$this,
					'debug',
				), 10, 2 );
		*/

	}

	/**
	 * Test filter WpSolrFilters::WPSOLR_FILTER_AFTER_GET_OPTION_VALUE
	 *
	 * @param $option_value
	 * @param $option
	 *
	 * @return string
	 */
	function test_filter( $option_value, $option ) {

		echo sprintf( "%s('%s') = '%s'<br/>", $option['option_name'], $option['$option_key'], $option_value );

		return $option_value;
	}

	/**
	 * Retrieve and cache an option
	 *
	 * @param $option_name
	 *
	 * @return array
	 */
	private function get_option( $option_name ) {

		// Retrieve option in cache, or in database
		if ( isset( $this->cached_options[ $option_name ] ) ) {

			// Retrieve option from cache
			$option = $this->cached_options[ $option_name ];

		} else {

			// Not in cache, retrieve option from database
			$option = get_option( $option_name );

			// Add option to cached options
			$this->cached_options[ $option_name ] = $option;
		}

		return $option;
	}

	private function get_option_value( $caller_function_name, $option_name, $option_key, $option_default = null ) {

		if ( ! empty( $caller_function_name ) ) {
			// Filter before retrieving an option value
			$result = apply_filters( WpSolrFilters::WPSOLR_FILTER_BEFORE_GET_OPTION_VALUE, null, array(
				'option_name'     => $caller_function_name,
				'$option_key'     => $option_key,
				'$option_default' => $option_default
			) );
			if ( ! empty( $result ) ) {
				return $result;
			}
		}

		// Retrieve option from cache or databse
		$option = $this->get_option( $option_name );

		// Retrieve option value from option
		if ( isset( $option[ $option_key ] ) || isset( $option[ $option_default ] ) ) {

			$result = isset( $option[ $option_key ] ) ? $option[ $option_key ] : $option_default;

		} else {

			// undefined
		}

		if ( ! empty( $caller_function_name ) ) {
			// Filter after retrieving an option value
			return apply_filters( WpSolrFilters::WPSOLR_FILTER_AFTER_GET_OPTION_VALUE, $result, array(
				'option_name'     => $caller_function_name,
				'$option_key'     => $option_key,
				'$option_default' => $option_default
			) );
		}
	}

	/**
	 * Convert a string to integer
	 *
	 * @param $string
	 * @param $object_name
	 *
	 * @return int
	 * @throws Exception
	 */
	private function to_integer( $string, $object_name ) {
		if ( is_numeric( $string ) ) {

			return intval( $string );

		} else {
			throw new Exception( sprintf( 'Option "%s" with value "%s" should be an integer.', $object_name, $string ) );
		}

	}

	/**
	 * Is value empty ?
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	private function is_empty( $value ) {
		return empty( $value );
	}

	/**
	 * Explode a comma delimited string in array.
	 * Returns empty array if string is empty
	 *
	 * @param $string
	 *
	 * @return array
	 */
	private function explode( $string ) {
		return empty( $string ) ? array() : explode( ',', $string );
	}

	/***************************************************************************************************************
	 *
	 * Sort by option and items
	 *
	 **************************************************************************************************************/
	const OPTION_SORTBY = 'wdm_solr_sortby_data';
	const OPTION_SORTBY_ITEM_DEFAULT = 'sort_default';
	const OPTION_SORTBY_ITEM_ITEMS = 'sort';


	/**
	 * Get sortby options array
	 * @return array
	 */
	public function get_option_sortby() {
		return self::get_option( self::OPTION_SORTBY );
	}

	/**
	 * Default sort by option
	 * @return string
	 */
	public function get_sortby_default() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_SORTBY, self::OPTION_SORTBY_ITEM_DEFAULT, WPSolrSearchSolrClient::SORT_CODE_BY_RELEVANCY_DESC );
	}

	/**
	 * Comma separated string of items selectable in sort by
	 * @return string Items
	 */
	public function get_sortby_items() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_SORTBY, self::OPTION_SORTBY_ITEM_ITEMS, WPSolrSearchSolrClient::SORT_CODE_BY_RELEVANCY_DESC );
	}

	/**
	 * Array of items selectable in sort by
	 * @return array Array of items
	 */
	public function get_sortby_items_as_array() {
		return $this->explode( $this->get_sortby_items() );
	}

	public function get_option_installation() {

		if ( ! get_option( self::OPTION_INSTALLATION, false ) ) {

			$search = $this->get_option_search();
			if ( empty( $search ) ) {

				update_option( self::OPTION_INSTALLATION, true );
			}

		}

	}

	/***************************************************************************************************************
	 *
	 * Search results option and items
	 *
	 **************************************************************************************************************/
	const OPTION_SEARCH = 'wdm_solr_res_data';
	const OPTION_SEARCH_ITEM_REPLACE_WP_SEARCH = 'default_search';
	const OPTION_SEARCH_ITEM_SEARCH_METHOD = 'search_method';
	const OPTION_SEARCH_ITEM_IS_INFINITESCROLL = 'infinitescroll';
	const OPTION_SEARCH_ITEM_IS_PREVENT_LOADING_FRONT_END_CSS = 'is_prevent_loading_front_end_css';
	const OPTION_SEARCH_ITEM_is_after_autocomplete_block_submit = 'is_after_autocomplete_block_submit';
	const OPTION_SEARCH_ITEM_is_display_results_info = 'res_info';
	const OPTION_SEARCH_ITEM_max_nb_results_by_page = 'no_res';
	const OPTION_SEARCH_ITEM_max_nb_items_by_facet = 'no_fac';
	const OPTION_SEARCH_ITEM_highlighting_fragsize = 'highlighting_fragsize';
	const OPTION_SEARCH_ITEM_is_spellchecker = 'spellchecker';
	const OPTION_SEARCH_ITEM_IS_PARTIAL_MATCHES = 'is_partial_matches';

	/**
	 * Get search options array
	 * @return array
	 */
	public function get_option_search() {
		return self::get_option( self::OPTION_SEARCH, array() );
	}

	/**
	 * Replace default WP search form and search results by WPSOLR's.
	 * @return boolean
	 */
	public function get_search_is_replace_default_wp_search() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_REPLACE_WP_SEARCH ) );
	}

	/**
	 * Search method
	 * @return boolean
	 */
	public function get_search_method() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_SEARCH_METHOD, 'ajax_with_parameters' );
	}

	/**
	 * Show search parameters in url ?
	 * @return boolean
	 */
	public function get_search_is_ajax_with_url_parameters() {
		return ( 'ajax_with_parameters' == $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_SEARCH_METHOD, '' ) );
	}

	/**
	 * Redirect url on facets click ?
	 * @return boolean
	 */
	public function get_search_is_use_current_theme_search_template() {
		return ( 'use_current_theme_search_template' == $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_SEARCH_METHOD, '' ) );
	}

	/**
	 * Show results with Infinitescroll pagination ?
	 * @return boolean
	 */
	public function get_search_is_infinitescroll() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_IS_INFINITESCROLL ) );
	}

	/**
	 * Prevent loading WPSOLR default front-end css files. It's then easier to use current theme css.
	 * @return boolean
	 */
	public function get_search_is_prevent_loading_front_end_css() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_IS_PREVENT_LOADING_FRONT_END_CSS ) );
	}

	/**
	 * Do not trigger a search after selecting an item in the autocomplete list.
	 * @return string '1 for yes
	 */
	public function get_search_after_autocomplete_block_submit() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_is_after_autocomplete_block_submit, '0' );
	}

	/**
	 * Display results information, or not
	 * @return boolean
	 */
	public function get_search_is_display_results_info() {
		return ( 'res_info' == $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_is_display_results_info, 'res_info' ) );
	}

	/**
	 * Maximum number of results displayed on a page
	 * @return integer
	 */
	public function get_search_max_nb_results_by_page() {
		return $this->to_integer( $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_max_nb_results_by_page, 20 ), 'Max results by page' );
	}

	/**
	 * Maximum number of facet items displayed in any facet
	 * @return integer
	 */
	public function get_search_max_nb_items_by_facet() {
		return $this->to_integer( $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_max_nb_items_by_facet, 10 ), 'Max items by facet' );
	}

	/**
	 * Maximum length of highligthing text
	 * @return integer
	 */
	public function get_search_max_length_highlighting() {
		return $this->to_integer( $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_highlighting_fragsize, 100 ), 'Max length of highlighting' );
	}

	/**
	 * Is "Did you mean?" activated ?
	 * @return boolean
	 */
	public function get_search_is_did_you_mean() {
		return ( 'spellchecker' == $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_is_spellchecker, false ) );
	}

	/**
	 * Is "Partial matches?" activated ?
	 * @return boolean
	 */
	public function get_search_is_partial_matches() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_ITEM_IS_PARTIAL_MATCHES ) );
	}

	/***************************************************************************************************************
	 *
	 * Installation
	 *
	 **************************************************************************************************************/
	const OPTION_INSTALLATION = 'wpsolr_install';

	/***************************************************************************************************************
	 *
	 * Facets option and items
	 *
	 **************************************************************************************************************/
	const OPTION_FACET = 'wdm_solr_facet_data';
	const OPTION_FACET_FACETS = 'facets';
	const OPTION_FACET_FACETS_TO_SHOW_AS_HIERARCH = 'facets_show_hierarchy';

	/**
	 * Get facet options array
	 * @return array
	 */
	public function get_option_facet() {
		return self::get_option( self::OPTION_FACET );
	}

	/**
	 * Comma separated facets
	 * @return array ["type","author","categories","tags","acf2_str"]
	 */
	public function get_facets_to_display() {
		return $this->explode( $this->get_option_value( __FUNCTION__, self::OPTION_FACET, self::OPTION_FACET_FACETS, '' ) );
	}

	/**
	 * Facets to show as a hierarcy
	 *
	 * @return array Facets names
	 */
	public function get_facets_to_show_as_hierarchy() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_FACET, self::OPTION_FACET_FACETS_TO_SHOW_AS_HIERARCH, array() );
	}

	/***************************************************************************************************************
	 *
	 * Indexing option and items
	 *
	 **************************************************************************************************************/
	const OPTION_INDEX = 'wdm_solr_form_data';
	const OPTION_INDEX_ARE_COMMENTS_INDEXED = 'comments';

	/**
	 * Get indexing options array
	 * @return array
	 */
	public function get_option_index() {
		return self::get_option( self::OPTION_INDEX );
	}

	/**
	 * Index comments ?
	 * @return boolean
	 */
	public function get_index_are_comments_indexed() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_INDEX, self::OPTION_INDEX_ARE_COMMENTS_INDEXED ) );
	}

	/**
	 * Is installed
	 * @return mixed|void
	 */
	public function get_option_is_installed() {

		return get_option( self::OPTION_INSTALLATION, false );
	}

	/***************************************************************************************************************
	 *
	 * Localization option and items
	 *
	 **************************************************************************************************************/
	const OPTION_LOCALIZATION = 'wdm_solr_localization_data';
	const OPTION_LOCALIZATION_LOCALIZATION_METHOD = 'localization_method';

	/**
	 * Get localization options array
	 * @return array
	 */
	public function get_option_localization() {
		return self::get_option( self::OPTION_LOCALIZATION );
	}

	public function get_localization_is_internal() {
		return ( 'localization_by_admin_options' == $this->get_option_value( __FUNCTION__, self::OPTION_LOCALIZATION, self::OPTION_LOCALIZATION_LOCALIZATION_METHOD, 'localization_by_admin_options' ) );
	}

}
