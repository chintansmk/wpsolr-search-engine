<?php

namespace wpsolr\utilities;

use wpsolr\extensions\acf\WPSOLR_Plugin_Acf;
use wpsolr\extensions\components\WPSOLR_Options_Components;
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\fields\WPSOLR_Options_Fields;
use wpsolr\extensions\groups\WPSOLR_Plugin_Groups;
use wpsolr\extensions\importexport\WPSOLR_Options_ImportExports;
use wpsolr\extensions\indexes\WPSOLR_Options_Indexes;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\polylang\WPSOLR_Plugin_Polylang;
use wpsolr\extensions\queries\WPSOLR_Options_Query;
use wpsolr\extensions\results\WPSOLR_Options_ResultsRows;
use wpsolr\extensions\resultsheaders\WPSOLR_Options_Result_Header;
use wpsolr\extensions\resultspagenavigations\WPSOLR_Options_Result_Page_Navigation;
use wpsolr\extensions\resultsrows\WPSOLR_Options_Result_Row;
use wpsolr\extensions\s2member\WPSOLR_Plugin_S2member;
use wpsolr\extensions\searchform\WPSOLR_Options_Search_Form;
use wpsolr\extensions\sorts\WPSOLR_Options_Sorts;
use wpsolr\extensions\types\WPSOLR_Plugin_Types;
use wpsolr\extensions\woocommerce\WPSOLR_Plugin_Woocommerce;
use wpsolr\extensions\wpml\WPSOLR_Plugin_Wpml;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\solr\WPSOLR_Field_Types;
use wpsolr\solr\WPSOLR_SearchSolrClient;
use wpsolr\ui\templates\twig\WPSOLR_Twig;
use wpsolr\ui\WPSOLR_Query;
use wpsolr\ui\WPSOLR_Query_Parameters;
use wpsolr\ui\WPSOLR_UI;

/**
 * Replace class WP_Query by the child class WPSOLR_query
 * Action called at the end of wp-settings.php, before $wp_query is processed
 */
WPSOLR_Service_Wordpress::add_action( 'wp_loaded', array( WPSOLR_Global::CLASS, 'action_wp_loaded' ), 10, 1 );


/**
 * Manage a list of singleton objects (global objects).
 */
class WPSOLR_Global {

	private static $objects = array();

	public static function action_wp_loaded() {

		// Add ajax actions
		add_action( 'wp_ajax_nopriv_' . WPSOLR_UI::METHOD_DISPLAY_AJAX, [
			WPSOLR_UI::class,
			WPSOLR_UI::METHOD_DISPLAY_AJAX
		] );
		add_action( 'wp_ajax_' . WPSOLR_UI::METHOD_DISPLAY_AJAX, [ WPSOLR_UI::class, WPSOLR_UI::METHOD_DISPLAY_AJAX ] );

		if ( WPSOLR_Query_Parameters::is_replace_wp_search() ) {

			// Override global $wp_query with wpsolr_query
			$GLOBALS['wp_the_query'] = WPSOLR_Global::getQuery();
			$GLOBALS['wp_query']     = $GLOBALS['wp_the_query'];
		}
	}

	/**
	 * Get/create a singleton object from it's class.
	 *
	 * @param $cache_name
	 * @param $class_name
	 * @param null $parameter
	 *
	 * @return
	 */
	public static function getObject( $cache_name, $class_name, $parameter = null ) {

		if ( ! isset( self::$objects[ $cache_name ] ) ) {

			self::$objects[ $cache_name ] = method_exists( $class_name, "global_object" )
				? isset( $parameter ) ? $class_name::global_object( $parameter ) : $class_name::global_object()
				: new $class_name();
		}

		return self::$objects[ $cache_name ];
	}

	/**
	 * @return WPSOLR_Option
	 */
	public static function getOption() {

		return self::getObject( WPSOLR_Option::CLASS, WPSOLR_Option::CLASS );
	}

	/**
	 * @return WPSOLR_Query
	 */
	public static function getQuery( WPSOLR_Query $wpsolr_query = null ) {

		return self::getObject( WPSOLR_Query::CLASS, WPSOLR_Query::CLASS, $wpsolr_query );
	}

	/**
	 * @return WPSOLR_SearchSolrClient
	 */
	public static function getSolrClient() {

		return self::getObject( WPSOLR_SearchSolrClient::CLASS, WPSOLR_SearchSolrClient::CLASS );
	}

	/**
	 * @return WPSOLR_Twig
	 */
	public static function getTwig() {

		return self::getObject( WPSOLR_Twig::CLASS, WPSOLR_Twig::CLASS );
	}

	/**
	 * @return WPSOLR_Plugin_Woocommerce
	 */
	public static function getExtensionWoocommerce() {
		return self::getObject( WPSOLR_Extensions::EXTENSION_WOOCOMMERCE, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::EXTENSION_WOOCOMMERCE );
	}

	/**
	 * @return WPSOLR_Plugin_Acf
	 */
	public static function getExtensionAcf() {
		return self::getObject( WPSOLR_Extensions::EXTENSION_ACF, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::EXTENSION_ACF );
	}

	/**
	 * @return WPSOLR_Plugin_Groups
	 */
	public static function getExtensionGroups() {
		return self::getObject( WPSOLR_Extensions::EXTENSION_GROUPS, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::EXTENSION_GROUPS );
	}

	/**
	 * @return WPSOLR_Plugin_Types
	 */
	public static function getExtensionTypes() {
		return self::getObject( WPSOLR_Extensions::EXTENSION_TYPES, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::EXTENSION_TYPES );
	}

	/**
	 * @return WPSOLR_Plugin_S2member
	 */
	public static function getExtensionS2member() {
		return self::getObject( WPSOLR_Extensions::EXTENSION_S2MEMBER, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::EXTENSION_S2MEMBER );
	}

	/**
	 * @return WPSOLR_Plugin_Wpml
	 */
	public static function getExtensionWpml() {
		return self::getObject( WPSOLR_Extensions::EXTENSION_WPML, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::EXTENSION_WPML );
	}

	/**
	 * @return WPSOLR_Plugin_Polylang
	 */
	public static function getExtensionPolylang() {
		return self::getObject( WPSOLR_Extensions::EXTENSION_POLYLANG, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::EXTENSION_POLYLANG );
	}

	/**
	 * @return WPSOLR_Options_Indexes
	 */
	public static function getExtensionIndexes() {
		return self::getObject( WPSOLR_Extensions::EXTENSION_INDEXES, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::EXTENSION_INDEXES );
	}

	/**
	 * @return WPSOLR_Options_Facets
	 */
	public static function getExtensionFacets() {
		return self::getObject( WPSOLR_Extensions::OPTION_FACETS, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_FACETS );
	}

	/**
	 * @return WPSOLR_Options_Fields
	 */
	public static function getExtensionFields() {
		return self::getObject( WPSOLR_Extensions::OPTION_FIELDS, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_FIELDS );
	}

	/**
	 * Get all active extensions.
	 * Load active extensions to catch specific filters/actions
	 *
	 * @return array
	 */
	public static function getActiveExtensions() {
		$results = [ ];

		foreach ( WPSOLR_Extensions::get_extensions() as $extension ) {

			if ( $extension['is_active'] ) {
				$object = self::getObject( $extension['id'], WPSOLR_Extensions::CLASS, $extension['id'] );
				array_push( $results, $object );
			}
		}

		return $results;
	}

	/**
	 * @return WPSOLR_Extensions
	 */
	public static function getExtension( $extension ) {
		return self::getObject( $extension, WPSOLR_Extensions::CLASS, $extension );
	}

	/**
	 * @return WPSOLR_Field_Types
	 */
	public static function getSolrFieldTypes() {
		return self::getObject( WPSOLR_Field_Types::CLASS, WPSOLR_Field_Types::CLASS );
	}

	/**
	 * @return \Solarium\Core\Query\Helper
	 */
	public static function getSolariumHelper() {
		return self::getObject( \Solarium\Core\Query\Helper::CLASS, \Solarium\Core\Query\Helper::CLASS );
	}

	/**
	 * @return WPSOLR_Options_Sorts
	 */
	public static function getExtensionSorts() {
		return self::getObject( WPSOLR_Extensions::OPTION_SORTS, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_SORTS );
	}

	/**
	 * @return WPSOLR_Options_ImportExports
	 */
	public static function getExtensionImportExports() {
		return self::getObject( WPSOLR_Extensions::OPTION_IMPORTEXPORT, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_IMPORTEXPORT );
	}

	/**
	 * @return WPSOLR_Options_Layouts
	 */
	public static function getExtensionLayouts() {
		return self::getObject( WPSOLR_Extensions::OPTION_LAYOUTS, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_LAYOUTS );
	}

	/**
	 * @return WPSOLR_Options_Components
	 */
	public static function getExtensionComponents() {
		return self::getObject( WPSOLR_Extensions::OPTION_COMPONENTS, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_COMPONENTS );
	}

	/**
	 * Get the plugin directory url (for js/css/images ...)
	 * @return string
	 */
	public static function get_plugin_dir_url() {
		return WPSOLR_DEFINE_PLUGIN_DIR_URL;
	}

	/**
	 * @return WPSOLR_Options_Result_Row
	 */
	public static function getExtensionResultsRows() {
		return self::getObject( WPSOLR_Extensions::OPTION_RESULTS_ROWS, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_RESULTS_ROWS );
	}

	/**
	 * @return WPSOLR_Options_Result_Header
	 */
	public static function getExtensionResultsHeaders() {
		return self::getObject( WPSOLR_Extensions::OPTION_RESULTS_HEADERS, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_RESULTS_HEADERS );
	}

	/**
	 * @return WPSOLR_Options_Result_Page_Navigation
	 */
	public static function getExtensionResultsPageNavigations() {
		return self::getObject( WPSOLR_Extensions::OPTION_RESULTS_PAGE_NAVIGATIONS, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_RESULTS_PAGE_NAVIGATIONS );
	}

	/**
	 * @return WPSOLR_Options_Search_Form
	 */
	public static function getExtensionSearchForm() {
		return self::getObject( WPSOLR_Extensions::OPTION_SEARCH_FORMS, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_SEARCH_FORMS );
	}

	/**
	 * @return WPSOLR_Options_Query
	 */
	public static function getExtensionQueries() {
		return self::getObject( WPSOLR_Extensions::OPTION_QUERIES, WPSOLR_Extensions::CLASS, WPSOLR_Extensions::OPTION_QUERIES );
	}

}

