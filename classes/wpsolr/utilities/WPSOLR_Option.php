<?php

namespace wpsolr\utilities;

use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\solr\WPSOLR_SearchSolrClient;
use wpsolr\WPSOLR_Filters;

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
		add_filter( WPSOLR_Filters::WPSOLR_FILTER_AFTER_GET_OPTION_VALUE, array(
					$this,
					'debug',
				), 10, 2 );
		*/

	}

	/**
	 * Test filter WPSOLR_Filters::WPSOLR_FILTER_AFTER_GET_OPTION_VALUE
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
	private function get_option( $option_name, $default_value = [ ] ) {

		// Retrieve option in cache, or in database
		if ( isset( $this->cached_options[ $option_name ] ) ) {

			// Retrieve option from cache
			$option = $this->cached_options[ $option_name ];

		} else {

			// Not in cache, retrieve option from database
			$option = WPSOLR_Service_Wordpress::get_option( $option_name, $default_value );

			// Add option to cached options
			$this->cached_options[ $option_name ] = $option;
		}

		return $option;
	}

	private function get_option_value( $caller_function_name, $option_name, $option_key, $option_default = null ) {

		if ( ! empty( $caller_function_name ) ) {

			// Filter before retrieving an option value
			$result = WPSOLR_Service_Wordpress::apply_filters( WPSOLR_Filters::WPSOLR_FILTER_BEFORE_GET_OPTION_VALUE, null, array(
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


		$result = isset( $option[ $option_key ] ) ? $option[ $option_key ] : $option_default;

		if ( ! empty( $caller_function_name ) ) {
			// Filter after retrieving an option value
			return WPSOLR_Service_Wordpress::apply_filters( WPSOLR_Filters::WPSOLR_FILTER_AFTER_GET_OPTION_VALUE, $result, array(
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
	 * Shared options
	 *
	 **************************************************************************************************************/
	const OPTION_SHARED_IS_EXTENSION_ACTIVE = 'is_extension_active';
	const OPTION_SHARED_SOLR_INDEX_INDICE = 'solr_index_indice';

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
	const OPTION_SEARCH_IS_DO_NOT_SHOW_SUGGESTIONS = 'is_do_not_show_suggestions';
	const OPTION_SEARCH_IS_QUERY_PARTIAL_MATCH_BEGIN_WITH = 'is_query_partial_match_begin_with';
	const OPTION_SEARCH_QUERY_DEFAULT_OPERATOR = 'query_default_operator';

	/**
	 * Get search options array
	 * @return array
	 */
	public function get_option_search() {
		return self::get_option( self::OPTION_SEARCH );
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
	 * Is "Do not show suggestions?" activated ?
	 * @return boolean
	 */
	public function get_search_is_do_not_show_suggestions() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_IS_DO_NOT_SHOW_SUGGESTIONS ) );
	}

	/**
	 * Is "Partial keyword match" activated ?
	 * @return boolean
	 */
	public function get_search_is_query_partial_match_begin_with() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_IS_QUERY_PARTIAL_MATCH_BEGIN_WITH ) );
	}

	/**
	 * Is "Partial keyword match" activated ?
	 * @return boolean
	 */
	public function get_search_query_default_operator() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_SEARCH, self::OPTION_SEARCH_QUERY_DEFAULT_OPERATOR, WPSOLR_SearchSolrClient::QUERY_OPERATOR_AND );
	}

	/***************************************************************************************************************
	 *
	 * Facets option and items
	 *
	 **************************************************************************************************************/
	const OPTION_FACETS = 'wdm_solr_facet_data';
	const OPTION_FACETS_FACETS = 'facets';
	const OPTION_FACETS_GROUPS = 'facets_groups';
	const OPTION_FACETS_GROUP_DEFAULT_ID = 'facets_group_default_id';
	const OPTION_FACETS_GROUP_FILTER_QUERY = 'filter_query';
	const OPTION_FACETS_GROUP_EXCLUSION = 'missing';

	/**
	 * Get facet options array
	 * @return array
	 */
	public function get_option_facet() {
		return self::get_option( self::OPTION_FACETS );
	}

	/**
	 * Comma separated facets selected
	 * @return "type,author,categories,tags,acf2_str"
	 */
	public function migrate_data_from_v7_6_get_facets_selected_array() {
		return $this->explode( $this->get_option_value( __FUNCTION__, self::OPTION_FACETS, self::OPTION_FACETS_FACETS, '' ) );
	}

	/**
	 * Array of facets selected
	 * @return array ["type" => [...]]
	 */
	public function get_facets_selected_array() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_FACETS, self::OPTION_FACETS_FACETS, [ ] );
	}

	/**
	 * Array of facets groups
	 * @return array [ '1' => ['name' => 'Group 1']]
	 */
	public function get_facets_groups() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_FACETS, self::OPTION_FACETS_GROUPS, [ ] );
	}


	/**
	 * Get the default facets group, used in ?s urls
	 *
	 * @return string
	 */
	public function get_default_facets_group_id() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_FACETS, self::OPTION_FACETS_GROUP_DEFAULT_ID, '' );
	}


	/***************************************************************************************************************
	 *
	 * Sort option and items
	 *
	 **************************************************************************************************************/
	const OPTION_SORTS = 'wdm_solr_sortby_data';
	const OPTION_SORTS_SORTS = 'sorts';
	const OPTION_SORTS_GROUPS = 'sorts_groups';
	const OPTION_SORTS_GROUP_DEFAULT_ID = 'sorts_group_default_id';

	/**
	 * Get facet options array
	 * @return array
	 */
	public function get_option_sort() {
		return self::get_option( self::OPTION_SORTS );
	}

	/**
	 * Comma separated sorts selected
	 * @return "type,author,categories,tags,acf2_str"
	 */
	public function migrate_data_from_v7_6_get_sorts_selected_array() {
		return $this->explode( $this->get_option_value( __FUNCTION__, self::OPTION_SORTS, self::OPTION_SORTS_SORTS, '' ) );
	}

	/**
	 * Array of sorts selected
	 * @return array ["type" => [...]]
	 */
	public function get_sorts_selected_array() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_SORTS, self::OPTION_SORTS_SORTS, [ ] );
	}

	/**
	 * Array of sorts groups
	 * @return array [ '1' => ['name' => 'Group 1']]
	 */
	public function get_sorts_groups() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_SORTS, self::OPTION_SORTS_GROUPS, [ ] );
	}


	/**
	 * Get the default sorts group, used in ?s urls
	 *
	 * @return string
	 */
	public function get_default_sorts_group_id() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_SORTS, self::OPTION_SORTS_GROUP_DEFAULT_ID, '' );
	}

	/***************************************************************************************************************
	 *
	 * Indexing option and items
	 *
	 **************************************************************************************************************/
	const OPTION_FIELDS = 'wdm_solr_form_data';
	const OPTION_FIELDS_ARE_COMMENTS_INDEXED = 'comments';
	const OPTION_FIELDS_CUSTOM_FIELDS_FROM_7_6 = 'cust_fields';
	const OPTION_FIELDS_CUSTOM_FIELDS = 'custom_fields';
	const OPTION_FIELDS_TAXONOMIES = 'taxonomies';
	const OPTION_FIELDS_POST_TYPES = 'p_types';
	const OPTION_FIELDS_ATTACHMENTS = 'attachment_types';
	const OPTION_FIELDS_EXCLUDE_IDS = 'exclude_ids';
	const OPTION_FIELDS_ARE_POST_EXCERPTS_INDEXED = 'p_excerpt';
	const OPTION_FIELDS_IS_SHORTCODE_EXPANDED = 'is_shortcode_expanded';

	/**
	 * Get indexing options array
	 * @return array
	 */
	public function get_option_fields() {
		return self::get_option( self::OPTION_FIELDS );
	}

	/**
	 * Index comments ?
	 * @return boolean
	 */
	public function get_fields_are_comments_indexed() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_FIELDS, self::OPTION_FIELDS_ARE_COMMENTS_INDEXED ) );
	}

	/**
	 * Custom fields indexed
	 * @return array
	 */
	public function get_fields_custom_fields_array() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_FIELDS, self::OPTION_FIELDS_CUSTOM_FIELDS, [ ] );
	}

	/**
	 * Custom fields indexed, <= 7.6
	 * @return array
	 */
	public function migrate_data_from_v7_6_get_fields_custom_fields_array() {
		return $this->explode( $this->get_option_value( __FUNCTION__, self::OPTION_FIELDS, self::OPTION_FIELDS_CUSTOM_FIELDS_FROM_7_6, '' ) );
	}

	/**
	 * Taxonomies indexed
	 * @return string
	 */
	public function get_indexing_taxonomies() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_FIELDS, self::OPTION_FIELDS_TAXONOMIES, '' );
	}

	/**
	 * Taxonomies indexed
	 * @return array
	 */
	public function get_fields_taxonomies_array() {
		return $this->explode( $this->get_indexing_taxonomies() );
	}

	/**
	 * POst types indexed
	 * @return string
	 */
	public function get_fields_post_types() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_FIELDS, self::OPTION_FIELDS_POST_TYPES, '' );
	}

	/**
	 * Attachements indexed
	 * @return string
	 */
	public function get_fields_attachements() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_FIELDS, self::OPTION_FIELDS_ATTACHMENTS, '' );
	}

	/**
	 * Attachements indexed
	 * @return array
	 */
	public function get_fields_attachements_array() {
		return $this->explode( $this->get_fields_attachements() );
	}

	/**
	 * Ids exluded from index
	 * @return string
	 */
	public function get_fields_exclude_ids() {
		return $this->get_option_value( __FUNCTION__, self::OPTION_FIELDS, self::OPTION_FIELDS_EXCLUDE_IDS, '' );
	}

	/**
	 * Ids exluded from index
	 * @return array
	 */
	public function get_fields_exclude_ids_array() {
		return $this->explode( $this->get_fields_exclude_ids() );
	}

	/**
	 * Index post excerpts ?
	 * @return boolean
	 */
	public function get_fields_are_post_excertps_indexed() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_FIELDS, self::OPTION_FIELDS_ARE_POST_EXCERPTS_INDEXED ) );
	}

	/**
	 * Index post excerpts ?
	 * @return boolean
	 */
	public function get_fields_is_shortcode_expanded() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_FIELDS, self::OPTION_FIELDS_IS_SHORTCODE_EXPANDED ) );
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

	/***************************************************************************************************************
	 *
	 * Debug/Dev environments
	 *
	 **************************************************************************************************************/
	public function get_is_debug_environment() {
		return ( ! isset( $_SERVER['HTTP_HOST'] ) ? false : $_SERVER['HTTP_HOST'] === 'dev-wpsolr-search-engine.dev' );
	}

	/***************************************************************************************************************
	 *
	 * Plugin Acf
	 *
	 **************************************************************************************************************/
	const OPTION_PLUGIN_ACF = 'wdm_solr_extension_acf_data';
	const OPTION_PLUGIN_ACF_IS_REPLACE_CUSTOM_FIELD_NAME = 'display_acf_label_on_facet';

	public function get_option_plugin_acf( $default_value = [ ] ) {
		return self::get_option( self::OPTION_PLUGIN_ACF, $default_value );
	}

	public function get_plugin_acf_is_replace_custom_field_name() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_ACF, self::OPTION_PLUGIN_ACF_IS_REPLACE_CUSTOM_FIELD_NAME ) );
	}

	/***************************************************************************************************************
	 *
	 * Plugin Groups
	 *
	 **************************************************************************************************************/
	const OPTION_PLUGIN_GROUPS = 'wdm_solr_extension_groups_data';
	const OPTION_PLUGIN_GROUPS_IS_USERS_WITHOUT_GROUPS_SEE_ALL_RESULTS = 'is_users_without_groups_see_all_results';
	const OPTION_PLUGIN_GROUPS_IS_RESULT_WITHOUT_CAPABILITIES_SEEN_BY_ALL_USERS = 'is_result_without_capabilities_seen_by_all_users';
	const OPTION_PLUGIN_MESSAGE_USER_WITHOUT_GROUPS_SHOWN_NO_RESULTS = 'message_user_without_groups_shown_no_results';
	const OPTION_PLUGIN_MESSAGE_RESULT_CAPABILITY_MATCHES_USER_GROUP = 'message_result_capability_matches_user_group';

	public function get_option_plugin_groups( $default_value = [ ] ) {
		return self::get_option( self::OPTION_PLUGIN_GROUPS, $default_value );
	}

	public function get_plugin_groups_is_users_without_groups_see_all_results() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_GROUPS, self::OPTION_PLUGIN_GROUPS_IS_USERS_WITHOUT_GROUPS_SEE_ALL_RESULTS ) );
	}

	public function get_plugin_groups_is_result_without_capabilities_seen_by_all_users() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_GROUPS, self::OPTION_PLUGIN_GROUPS_IS_RESULT_WITHOUT_CAPABILITIES_SEEN_BY_ALL_USERS ) );
	}

	public function get_plugin_groups_message_user_without_groups_shown_no_results( $default_value = '' ) {
		return $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_GROUPS, self::OPTION_PLUGIN_MESSAGE_USER_WITHOUT_GROUPS_SHOWN_NO_RESULTS, $default_value );
	}

	public function get_plugin_groups_message_result_capability_matches_user_group( $default_value = '' ) {
		return $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_GROUPS, self::OPTION_PLUGIN_MESSAGE_RESULT_CAPABILITY_MATCHES_USER_GROUP, $default_value );
	}

	/***************************************************************************************************************
	 *
	 * Plugin Woocommerce
	 *
	 **************************************************************************************************************/
	const OPTION_PLUGIN_WOOCOMMERCE = 'wdm_solr_extension_woocommerce_data';

	public function get_option_plugin_woocommerce( $default_value = [ ] ) {
		return self::get_option( self::OPTION_PLUGIN_WOOCOMMERCE, $default_value );
	}


	/***************************************************************************************************************
	 *
	 * Plugin Types
	 *
	 **************************************************************************************************************/
	const OPTION_PLUGIN_TYPES = 'wdm_solr_extension_types_data';
	const OPTION_PLUGIN_TYPES_DISPLAY_TYPES_LABEL_ON_FACET = 'display_types_label_on_facet';

	public function get_option_plugin_types( $default_value = [ ] ) {
		return self::get_option( self::OPTION_PLUGIN_TYPES, $default_value );
	}

	public function get_plugin_groups_is_display_types_label_on_facet() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_TYPES, self::OPTION_PLUGIN_TYPES_DISPLAY_TYPES_LABEL_ON_FACET ) );
	}

	/***************************************************************************************************************
	 *
	 * Plugin WPML
	 *
	 **************************************************************************************************************/
	const OPTION_PLUGIN_WPML = 'wdm_solr_extension_wpml_data';

	public function get_option_plugin_wpml( $default_value = [ ] ) {
		return self::get_option( self::OPTION_PLUGIN_WPML, $default_value );
	}

	public function get_plugin_wpml_solr_index_indices( $default_value = null ) {
		return $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_WPML, self::OPTION_SHARED_SOLR_INDEX_INDICE, $default_value );
	}

	/***************************************************************************************************************
	 *
	 * Plugin Polylang
	 *
	 **************************************************************************************************************/

	const OPTION_PLUGIN_POLYLANG = 'wdm_solr_extension_polylang_data';

	public function get_option_plugin_polylang( $default_value = [ ] ) {
		return self::get_option( self::OPTION_PLUGIN_POLYLANG, $default_value );
	}

	public function get_plugin_polylang_solr_index_indices( $default_value = null ) {
		return $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_POLYLANG, self::OPTION_SHARED_SOLR_INDEX_INDICE, $default_value );
	}

	/***************************************************************************************************************
	 *
	 * Plugin s2member
	 *
	 **************************************************************************************************************/

	const OPTION_PLUGIN_S2MEMBER = 'wdm_solr_extension_s2member_data';
	const OPTION_PLUGIN_S2MEMBER_IS_USERS_WITHOUT_CAPABILITIES_SEE_ALL_RESULTS = 'is_users_without_capabilities_see_all_results';
	const OPTION_PLUGIN_S2MEMBER_IS_RESULT_WITHOUT_CAPABILITIES_SEEN_BY_ALL_USERS = 'is_result_without_capabilities_seen_by_all_users';
	const OPTION_PLUGIN_S2MEMBER_MESSAGE_USER_WITHOUT_CAPABILITIES_SHOWN_NO_RESULTS = 'message_user_without_capabilities_shown_no_results';


	public function get_option_plugin_s2member( $default_value = [ ] ) {
		return self::get_option( self::OPTION_PLUGIN_S2MEMBER, $default_value );
	}

	public function get_plugin_s2member_is_users_without_capabilities_see_all_results() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_S2MEMBER, self::OPTION_PLUGIN_S2MEMBER_IS_USERS_WITHOUT_CAPABILITIES_SEE_ALL_RESULTS ) );
	}

	public function get_plugin_s2member_is_result_without_capabilities_seen_by_all_users() {
		return ! $this->is_empty( $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_S2MEMBER, self::OPTION_PLUGIN_S2MEMBER_IS_RESULT_WITHOUT_CAPABILITIES_SEEN_BY_ALL_USERS ) );
	}

	public function get_plugin_s2member_message_user_without_capabilities_shown_no_results( $default_value = '' ) {
		return $this->get_option_value( __FUNCTION__, self::OPTION_PLUGIN_S2MEMBER, self::OPTION_PLUGIN_S2MEMBER_MESSAGE_USER_WITHOUT_CAPABILITIES_SHOWN_NO_RESULTS, $default_value );
	}

	/***************************************************************************************************************
	 *
	 * Indexing option and items
	 *
	 **************************************************************************************************************/
	const OPTION_INDEXES = 'wpsolr_solr_indexes';

	/**
	 * Get indexing options array
	 * @return array
	 */
	public function get_option_indexes() {
		return self::get_option( self::OPTION_INDEXES );
	}

	/***************************************************************************************************************
	 *
	 * Import/Export option
	 *
	 **************************************************************************************************************/
	const OPTION_IMPORTEXPORT = 'wpsolr_import_export';
	const OPTION_IMPORTEXPORT_IS_SOLR_INDEXES = 'is_solr_indexes';
	const OPTION_IMPORTEXPORT_IS_SEARCH = 'is_search';
	const OPTION_IMPORTEXPORT_IS_FACETS_GROUPS = 'is_facets_groups';
	const OPTION_IMPORTEXPORT_IS_SORTS_GROUPS = 'is_sorts_groups';
	const OPTION_IMPORTEXPORT_IS_PLUGINS = 'is_plugins';

	/**
	 * Get options array
	 * @return array
	 */
	public function get_option_importexports() {
		return self::get_option( self::OPTION_IMPORTEXPORT, [ ] );
	}

	/***************************************************************************************************************
	 *
	 * Layouts option
	 *
	 **************************************************************************************************************/
	const OPTION_LAYOUTS = 'wpsolr_layouts';

	/**
	 * Get layouts options array
	 * @return array
	 */
	public function get_option_layouts() {
		return self::get_option( self::OPTION_LAYOUTS, [ ] );
	}

	/***************************************************************************************************************
	 *
	 * Components option
	 *
	 **************************************************************************************************************/
	const OPTION_COMPONENTS = 'wpsolr_components';

	/**
	 * Get component options array
	 * @return array
	 */
	public function get_option_components() {
		return self::get_option( self::OPTION_COMPONENTS, [ ] );
	}


	/***************************************************************************************************************
	 *
	 * Results rows option
	 *
	 **************************************************************************************************************/
	const OPTION_RESULTS_ROWS = 'wpsolr_results_rows';

	/**
	 * Get results rows options array
	 * @return array
	 */
	public function get_option_results_rows() {
		return self::get_option( self::OPTION_RESULTS_ROWS, [ ] );
	}

	/***************************************************************************************************************
	 *
	 * Results header option
	 *
	 **************************************************************************************************************/
	const OPTION_RESULTS_HEADERS = 'wpsolr_results_headers';

	/**
	 * Get results header options array
	 * @return array
	 */
	public function get_option_results_header() {
		return self::get_option( self::OPTION_RESULTS_HEADERS, [ ] );
	}

	/***************************************************************************************************************
	 *
	 * Results page navigation option
	 *
	 **************************************************************************************************************/
	const OPTION_RESULTS_PAGE_NAVIGATION = 'wpsolr_results_page_navigations';

	/**
	 * Get results page navigation options array
	 * @return array
	 */
	public function get_option_results_page_navigation() {
		return self::get_option( self::OPTION_RESULTS_PAGE_NAVIGATION, [ ] );
	}

	/***************************************************************************************************************
	 *
	 * Search form option
	 *
	 **************************************************************************************************************/
	const OPTION_SEARCH_FORM = 'wpsolr_search_forms';

	/**
	 * Get search form options array
	 * @return array
	 */
	public function get_option_search_form() {
		return self::get_option( self::OPTION_SEARCH_FORM, [ ] );
	}

}