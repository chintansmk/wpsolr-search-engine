<?php
/**
 * WPSOLR shorcodes to level class
 */

namespace wpsolr\ui\shortcode;


use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\ui\WPSOLR_UI;

/**
 * Class WPSOLR_Shortcode
 * Class root of all WPSOLR shortcodes
 * @package wpsolr\ui\shortcode
 */
class WPSOLR_Shortcode {

	// All WPSOLR shortcode classes must begin with this prefix to be autoloaded.
	const WPSOLR_SHORTCODE_CLASS_NAME_PREFIX = 'WPSOLR_Shortcode_';

	// Shorcode attributes
	const ATTRIBUTE_COMPONENT_ID = 'id';

	// Shortcode definitions list
	protected static $shortcodes;

	protected $component_id;
	protected $ui;

	/**
	 * Add all shortcodes present in this directory
	 */
	public static function add_shortcodes() {

		self::$shortcodes = [ ];

		// Loop on all shortcode files in current directory
		$shortcode_file_pattern = dirname( __FILE__ ) . "/" . self::WPSOLR_SHORTCODE_CLASS_NAME_PREFIX . "*.php";
		foreach ( glob( $shortcode_file_pattern ) as $file ) {

			//  The shortcode class name is base name of file, without the extension
			$shortcode_class_name = __NAMESPACE__ . '\\' . basename( $file, '.php' );

			// Register shortcode
			$shortcode_object = new $shortcode_class_name();
			add_shortcode( $shortcode_object->shortcode_name, array( $shortcode_object, 'output' ) );

			// Register shortcode definition
			self::$shortcodes[ $shortcode_object->shortcode_name ] = $shortcode_object;
		}

	}

	/**
	 * Get all shortcodes
	 *
	 * @return WPSOLR_Shortcode[]
	 */
	public static function get_shortcodes() {
		return self::$shortcodes;
	}

	/**
	 * Display the shortcode
	 *
	 * @param $attributes
	 * @param string $content
	 *
	 * @return string
	 */
	public function output( $attributes, $content = "" ) {

		try {

			$this->component_id = ! empty( $attributes[ self::ATTRIBUTE_COMPONENT_ID ] ) ? $attributes[ self::ATTRIBUTE_COMPONENT_ID ] : '';
			$result             = $this->get_ui()->display(
				sprintf( 'shortcode %s', $this->shortcode_name ),
				$this->component_id,
				'',
				'',
				'',
				'',
				''
			);

			return $result;

		} catch ( WPSOLR_Exception $e ) {

			return $e->get_message();
		}

	}

	/**
	 * Returns the UI object
	 *
	 * @return WPSOLR_UI
	 */
	public function get_ui() {
		return $this->ui;
	}

	/**
	 * Get shortcode name
	 *
	 * @return string
	 */
	public function get_shortcode_name() {
		return $this->shortcode_name;
	}

}