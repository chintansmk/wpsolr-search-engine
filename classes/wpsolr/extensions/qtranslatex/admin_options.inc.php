<?php

/**
 * Included file to display admin options
 */

$options_name = WPSOLR_Extensions::get_option_name( WPSOLR_Extensions::EXTENSION_QTRANSLATEX );

$options          = get_option( $options_name, array(
	WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE => '0',
) );
$is_plugin_active = WPSOLR_Extensions::is_plugin_active( WPSOLR_Extensions::EXTENSION_QTRANSLATEX );

$plugin_name    = "qTranslate X";
$plugin_link    = "https://wordpress.org/plugins/qtranslate-x/";
$plugin_version = "";

if ( $is_plugin_active ) {
	$ml_plugin = WPSOLR_Plugin_QTranslatex::create();
}
?>

<?php
include_once( WPSOLR_Extensions::get_option_file( WPSOLR_Extensions::EXTENSION_WPML, 'template.inc.php' ) );
