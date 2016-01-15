<?php

namespace wpsolr\utilities;

use wpsolr\extensions\acf\WPSOLR_Plugin_Acf;
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\groups\WPSOLR_Plugin_Groups;
use wpsolr\extensions\indexes\WPSOLR_Options_Indexes;
use wpsolr\extensions\polylang\WPSOLR_Plugin_Polylang;
use wpsolr\extensions\s2member\WPSOLR_Plugin_S2member;
use wpsolr\extensions\types\WPSOLR_Plugin_Types;
use wpsolr\extensions\woocommerce\WPSOLR_Plugin_Woocommerce;
use wpsolr\extensions\wpml\WPSOLR_Plugin_Wpml;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\solr\WPSOLR_SearchSolrClient;
use wpsolr\ui\templates\twig\WPSOLR_Twig;
use wpsolr\ui\WPSOLR_Query;
use wpsolr\ui\WPSOLR_Query_Parameters;

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

}

