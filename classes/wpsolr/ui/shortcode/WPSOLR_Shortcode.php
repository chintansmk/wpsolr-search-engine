<?php
/**
 * WPSOLR shorcodes to level class
 */

namespace wpsolr\ui\shortcode;


use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\ui\WPSOLR_UI;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\WPSOLR_Filters;

/**
 * Class WPSOLR_Shortcode
 * Class root of all WPSOLR shortcodes
 * @package wpsolr\ui\shortcode
 */
class WPSOLR_Shortcode {

	// All WPSOLR shortcode classes must begin with this prefix to be autoloaded.
	const WPSOLR_SHORTCODE_CLASS_NAME_PREFIX = 'WPSOLR_Shortcode_';

	// Shorcode attributes
	const ATTRIBUTE_SHORTCODE_ID = 'id';

	// Shortcode definitions
	protected static $shortcodes;

	protected $shortcode_id;
	protected $group_id;
	protected $layout_id;
	protected $is_show_when_no_data;
	protected $is_show_title_on_front_end;
	protected $layout;
	protected $title;
	protected $before_title;
	protected $after_title;
	protected $before_ui;
	protected $after_ui;
	protected $layout_type;
	protected $shortcode_name;
	protected $results_page;

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
	 * Get a shortcode
	 *
	 * @return WPSOLR_Shortcode
	 */
	public static function get_shortcode( $shortcode_name ) {

		if ( ! isset( self::$shortcodes[ $shortcode_name ] ) ) {
			throw new WPSOLR_Exception( sprintf( 'unknow shortcode %s.' ), $shortcode_name );
		}

		return self::$shortcodes[ $shortcode_name ];
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

			$extension_shortcodes = WPSOLR_Global::getExtensionShortcodes();

			$this->shortcode_id = ! empty( $attributes[ self::ATTRIBUTE_SHORTCODE_ID ] ) ? $attributes[ self::ATTRIBUTE_SHORTCODE_ID ] : '';
			$shortcode          = $extension_shortcodes->get_shortcode_by_type_and_id( $this->get_shortcode_name(), $this->shortcode_id );


			$this->results_page               = $extension_shortcodes->get_results_page( $shortcode );
			$this->layout_id                  = $extension_shortcodes->get_shortcode_layout_id( $shortcode );
			$this->group_id                   = $extension_shortcodes->get_shortcode_group_id( $shortcode );
			$this->url_regexp_lines           = $extension_shortcodes->get_shortcode_url_regexp_lines( $shortcode );
			$this->is_debug_js                = $extension_shortcodes->get_shortcode_is_debug_js( $shortcode );
			$this->is_show_when_no_data       = $extension_shortcodes->get_shortcode_is_show_when_empty( $shortcode );
			$this->is_show_title_on_front_end = $extension_shortcodes->get_shortcode_is_show_title_on_front_end( $shortcode );
			$this->title                      = $extension_shortcodes->get_shortcode_title( $shortcode );
			$this->before_title               = $extension_shortcodes->get_shortcode_before_title( $shortcode );
			$this->after_title                = $extension_shortcodes->get_shortcode_after_title( $shortcode );
			$this->before_ui                  = $extension_shortcodes->get_shortcode_before_ui( $shortcode );
			$this->after_ui                   = $extension_shortcodes->get_shortcode_after_ui( $shortcode );


			$result = $this->get_ui()->display(
				sprintf( 'shortcode %s', $this->shortcode_name ),
				$this->results_page,
				$this->layout_id,
				$this->group_id,
				$this->url_regexp_lines,
				$this->is_debug_js,
				$this->is_show_when_no_data,
				$this->is_show_title_on_front_end,
				apply_filters( WPSOLR_Filters::WPSOLR_FILTER_TRANSLATION_STRING, $this->title ),
				$this->before_title,
				$this->after_title,
				$this->before_ui,
				$this->after_ui
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
		die( 'get_ui not implemented' );
	}

	/**
	 * Get shortcode name
	 *
	 * @return string
	 */
	public function get_shortcode_name() {
		return $this->shortcode_name;
	}

	/**
	 * Get layout type
	 *
	 * @return String
	 */
	public function get_layout_type() {
		return $this->layout_type;
	}


}