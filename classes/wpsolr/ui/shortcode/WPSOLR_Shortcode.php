<?php
/**
 * WPSOLR shorcodes to level class
 */

namespace wpsolr\ui\shortcode;


use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Shortcode
 * Class root of all WPSOLR shortcodes
 * @package wpsolr\ui\shortcode
 */
class WPSOLR_Shortcode {

	// All WPSOLR shortcode classes must begin with this prefix to be autoloaded.
	const WPSOLR_SHORTCODE_CLASS_NAME_PREFIX = 'WPSOLR_Shortcode_';

	// Shortcode name
	const SHORTCODE_NAME = 'wpsolr_shortcode';

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

	/**
	 * Display the shortcode
	 *
	 * @param $attributes
	 * @param string $content
	 *
	 * @return string
	 */
	public static function output( $attributes, $content = "" ) {
		return sprintf( 'Shortcode not implemented: %s', __CLASS__ );
	}

	/**
	 * Retrieve the layout of the shortcode
	 *
	 * @param string $layout_id
	 *
	 * @return array Layout of the shortcode
	 * @throws WPSOLR_Exception
	 */
	protected static function get_layout( $layout_type, $layout_id ) {

		$layouts = WPSOLR_Global::getExtensionLayouts()->get_layout_from_type_and_id( $layout_type, $layout_id );

		if ( ! empty( $layouts ) ) {

			return $layouts;
		}

		throw new WPSOLR_Exception( sprintf( 'Shortcode \'%s\': undefined layout \'%s\'.', static::SHORTCODE_NAME, $layout_id ) );
	}

	/**
	 * Retrieve the group of the shortcode
	 *
	 * @param string $group_id
	 *
	 * @return array Group of the shortcode
	 * @throws WPSOLR_Exception
	 */
	protected static function get_group( $group_id ) {

		$group = static::get_group_child( $group_id );

		if ( ! empty( $group ) ) {

			return $group;
		}

		throw new WPSOLR_Exception( sprintf( 'Shortcode \'%s\': undefined group \'%s\'.', static::SHORTCODE_NAME, $group_id ) );
	}


	/**
	 * Implement get_group in children
	 *
	 * @param $group_id
	 *
	 * @return array Group
	 */
	protected static function get_group_child( $group_id ) {
		die( 'get_group_child not implemented.' );
	}

}