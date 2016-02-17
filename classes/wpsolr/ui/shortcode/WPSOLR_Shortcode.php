<?php
/**
 * WPSOLR shorcodes to level class
 */

namespace wpsolr\ui\shortcode;


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
	const ATTRIBUTE_GROUP_ID = 'group_id';
	const ATTRIBUTE_GROUP_LAYOUT_ID = 'layout_id';

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
			$shortcode_object = new $shortcode_class_name();
			add_shortcode( $shortcode_object->shortcode_name, array( $shortcode_object, 'output' ) );
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
	public function output( $attributes, $content = "" ) {

		$this->layout_id                  = ! empty( $attributes['layout_id'] ) ? $attributes['layout_id'] : '';
		$this->group_id                   = ! empty( $attributes['group_id'] ) ? $attributes['group_id'] : '';
		$this->url_regexp_lines           = ! empty( $attributes['url_regexp_lines'] ) ? $attributes['url_regexp_lines'] : '';
		$this->is_show_when_no_data       = ! empty( $attributes['is_show_when_no_data'] ) ? $attributes['is_show_when_no_data'] : '';
		$this->is_show_title_on_front_end = ! empty( $attributes['is_show_title_on_front_end'] ) ? $attributes['is_show_title_on_front_end'] : '';
		$this->title                      = ! empty( $attributes['title'] ) ? $attributes['title'] : '';
		$this->before_title               = ! empty( $attributes['before_title'] ) ? $attributes['before_title'] : '';
		$this->after_title                = ! empty( $attributes['after_title'] ) ? $attributes['after_title'] : '';
		$this->before_ui                  = ! empty( $attributes['before_ui'] ) ? $attributes['before_ui'] : '';
		$this->after_ui                   = ! empty( $attributes['after_ui'] ) ? $attributes['after_ui'] : '';


		$result = $this->get_ui()->display(
			sprintf( 'shortcode %s', $this->shortcode_name ),
			$this->layout_id,
			$this->group_id,
			$this->url_regexp_lines,
			$this->is_show_when_no_data,
			$this->is_show_title_on_front_end,
			$this->title,
			$this->before_title,
			$this->after_title,
			$this->before_ui,
			$this->after_ui
		);

		return $result;
	}

	/**
	 * Returns the UI object
	 *
	 * @return WPSOLR_UI
	 */
	protected function get_ui() {
		die( 'get_ui not implemented' );
	}

}