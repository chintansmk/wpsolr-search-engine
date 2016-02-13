<?php
/**
 * WPSOLR shorcodes to level class
 */

namespace wpsolr\ui\shortcode;


class WPSOLR_Shortcode {

	// All WPSOLR shortcode classes must begin with this prefix to be autoloaded.
	const WPSOLR_SHORTCODE_CLASS_NAME_PREFIX = 'WPSOLR_Shortcode_';

	/**
	 * Add all shortcodes present in this directory
	 */
	public static function add_shortcodes() {

		// Loop on all shortcode files in current directory
		$shortcode_file_pattern = dirname( __FILE__ ) . "/" . self::WPSOLR_SHORTCODE_CLASS_NAME_PREFIX . "*.php";
		foreach ( glob( $shortcode_file_pattern ) as $file ) {

			//  The shortcode class name is base name of file, without the extension
			$shortcode_class_name = __NAMESPACE__ . '\\' . basename( $file, '.php' );

			// Register shortcode
			add_shortcode( $shortcode_class_name::SHORTCODE_NAME, array( $shortcode_class_name, 'output' ) );
		}

	}

	public static function output( $attributes, $content = "" ) {
		return sprintf( 'Shortcode not implemented: %s', __CLASS__ );
	}

}