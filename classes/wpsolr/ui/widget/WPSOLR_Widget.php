<?php

namespace wpsolr\ui\widget;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\ui\WPSOLR_Query_Parameters;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Regexp;


/**
 * Top level widget class from which all WPSOLR widgets inherit.
 */
class WPSOLR_Widget extends \WP_Widget {

	// All WPOLR Widget classes must begin with this prefix to be autoloaded.
	const WPSOLR_WIDGET_CLASS_NAME_PREFIX = 'WPSOLR_Widget_';

	// Layouts of the widgets
	protected static $wpsolr_layouts;

	// Layout types
	const TYPE_GROUP_LAYOUT = 'type_group_layout';
	const TYPE_GROUP_ELEMENT_LAYOUT = 'type_group_element_layout';

	// Generic layout id
	const GENERIC_LAYOUT_ID = 'generic';

	// Layout fields in array definition
	const LAYOUT_FIELD_TEMPLATE_HTML = 'template_html';
	const LAYOUT_FIELD_TEMPLATE_CSS = 'template_css';
	const LAYOUT_FIELD_TEMPLATE_JS = 'template_js';
	const LAYOUT_FIELD_TEMPLATE_NAME = 'name';
	const LAYOUT_FIELD_FACET_TYPE = 'facet_type';
	const LAYOUT_FIELD_TYPES = 'field_types';

	// Form fields
	const FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_STRING = 'is_custom_twig_template_string';
	const FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_CSS_STRING = 'is_custom_twig_template_css_string';
	const FORM_FIELD_CUSTOM_TWIG_TEMPLATE_STRING = 'custom_twig_template_string';
	const FORM_FIELD_CUSTOM_TWIG_TEMPLATE_CSS_STRING = 'custom_twig_template_css_string';
	const FORM_FIELD_LAYOUT_ID = 'layout_id';
	const FORM_FIELD_URL_REGEXP = 'url_regexp';
	const FORM_FIELD_ERROR_MESSAGE = 'error_message';
	const FORM_FIELD_GROUP_ID = 'group_id';
	const FORM_FIELD_GROUP_NAME = 'name';

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public
	function widget(
		$args, $instance
	) {

		/**
		 * Only display a widget when:
		 * - Current url is a WP search page
		 * - WPSOLR is replacing the default search
		 * - WPSOLR displays the current theme search/seach form templates
		 *
		 */

		/*if ( \wpsolr\utilities\WPSOLR_Global::getOption()->get_search_is_replace_default_wp_search()
		     && \wpsolr\utilities\WPSOLR_Global::getOption()->get_search_is_use_current_theme_search_template()
		     && \wpsolr\ui\WPSOLR_Query_Parameters::is_wp_search()
		) */

		try {

			if ( $this->wpsolr_url_is_authorized( $instance ) ) {

				$this->wpsolr_form( $args, $instance );
			}

		} catch ( WPSOLR_Exception $e ) {

			// Display custom error in Widget area
			echo sprintf( '<div style=\'margin:10px;\'>Error in \'%s\': %s</div>', $args['widget_name'], $e->get_message() );
		}

	}

	protected
	function wpsolr_form(
		$args, $instance
	) {
		echo 'Widget form not implemented!!!';
	}

	protected
	function wpsolr_header(
		$instance
	) {
		echo 'Widget header not implemented!!!';
	}

	/**
	 * Back-end widget form.
	 * All common elements are there.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public
	function form(
		$instance
	) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'WPSOLR Widget', 'text_domain' );

		// Regexp: urls that display the widget
		$url_regexp = ! empty( $instance[ self::FORM_FIELD_URL_REGEXP ] ) ? $instance[ self::FORM_FIELD_URL_REGEXP ] : '';

		// Layout: display the widget
		$layout_id = ! empty( $instance[ self::FORM_FIELD_LAYOUT_ID ] ) ? $instance[ self::FORM_FIELD_LAYOUT_ID ] : self::GENERIC_LAYOUT_ID;
		$layouts   = $this->wpsolr_get_group_layouts();

		// Group: content of the widget
		$group_id = ! empty( $instance[ self::FORM_FIELD_GROUP_ID ] ) ? $instance[ self::FORM_FIELD_GROUP_ID ] : '';
		$groups   = $this->wpsolr_get_groups();
		?>

		<?php
		/* Let each widget write it's own header here */
		$this->wpsolr_header( $instance );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
			       value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			Use layout:
			<select
				id="<?php echo $this->get_field_id( self::FORM_FIELD_LAYOUT_ID ); ?>"
				name="<?php echo $this->get_field_name( self::FORM_FIELD_LAYOUT_ID ); ?>">
				<?php foreach ( $layouts as $index => $layout ) { ?>
					<option
						value="<?php echo $index; ?>" <?php selected( $layout_id, $index, true ) ?>><?php echo $layout[ self::LAYOUT_FIELD_TEMPLATE_NAME ]; ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			Use group:
			<select id="<?php echo $this->get_field_id( self::FORM_FIELD_GROUP_ID ); ?>"
			        name="<?php echo $this->get_field_name( self::FORM_FIELD_GROUP_ID ); ?>">
				<?php foreach ( $groups as $current_group_id => $current_group ) { ?>
					<option
						value="<?php echo $current_group_id; ?>" <?php selected( $group_id, $current_group_id, true ) ?>><?php echo $current_group[ self::FORM_FIELD_GROUP_NAME ]; ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			The widget is displayed for <a href="https://regex101.com/" target="_blank">Regexp</a> url(s):
			<textarea rows="3" class="widefat"
			          id="<?php echo $this->get_field_id( self::FORM_FIELD_URL_REGEXP ); ?>"
			          name="<?php echo $this->get_field_name( self::FORM_FIELD_URL_REGEXP ); ?>"><?php echo $url_regexp; ?></textarea>
		</p>


		<?php

	}

	public function update( $new_instance, $old_instance ) {

		return $new_instance;
	}


	/**
	 * Retrieve the twig template for the widget: custom string or selected layout or default file
	 *
	 * @param $instance
	 *
	 * @param $type_layout
	 * @param $template_type
	 *
	 * @return string
	 * @throws WPSOLR_Exception
	 */
	protected
	function wpsolr_get_instance_template(
		$instance, $type_layout, $template_type
	) {

		switch ( $template_type ) {

			case self::LAYOUT_FIELD_TEMPLATE_HTML:
				$form_field_is_custom_template = self::FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_STRING;
				$form_field_custom_template    = self::FORM_FIELD_CUSTOM_TWIG_TEMPLATE_STRING;
				break;

			case self::LAYOUT_FIELD_TEMPLATE_CSS:
				$form_field_is_custom_template = self::FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_CSS_STRING;
				$form_field_custom_template    = self::FORM_FIELD_CUSTOM_TWIG_TEMPLATE_CSS_STRING;
				break;

			default:
				throw new WPSOLR_Exception( 'template type \'%s\' is unknkown', $template_type );
		}

		/**
		 * Return the twig custom content if it exists
		 */
		if ( ! empty( $instance[ $form_field_is_custom_template ] ) ) {

			return ! empty( $instance[ $form_field_custom_template ] ) ? $instance[ $form_field_custom_template ] : '';
		}

		/**
		 * Return the layout's template selected
		 */
		return $this->wpsolr_get_layout_template( ! empty( $instance[ self::FORM_FIELD_LAYOUT_ID ] ) ? $instance[ self::FORM_FIELD_LAYOUT_ID ] : null, $type_layout, $template_type );
	}


	/**
	 * Retrieve the Twig template from a layout id
	 *
	 * @param $layout_id
	 * @param $type_layout Type of layout
	 * @param $template_type Template type
	 *
	 * @return string
	 */
	public static function wpsolr_get_layout_template(
		$layout_id, $type_layout, $template_type
	) {

		$layouts = self::wpsolr_get_layout_definitions( $type_layout );

		if ( ! empty( $layouts[ $layout_id ] ) ) {

			return $layouts[ $layout_id ][ $template_type ];
		}

		/**
		 * Return the twig template of the default layout
		 */
		return $layouts[ self::GENERIC_LAYOUT_ID ][ $template_type ];
	}

	/**
	 * Retrieve the Twig template html from a layout id
	 *
	 * @param $layout_id
	 * @param $type_layout Type of layout
	 *
	 * @return string
	 */
	public static function wpsolr_get_layout_template_html( $layout_id, $type_layout ) {

		return self::wpsolr_get_layout_template( $layout_id, $type_layout, self::LAYOUT_FIELD_TEMPLATE_HTML );
	}

	/**
	 * Retrieve the Twig template css from a layout id
	 *
	 * @param $layout_id
	 * @param $type_layout
	 *
	 * @return string
	 */
	public static function wpsolr_get_layout_template_css( $layout_id, $type_layout ) {

		return self::wpsolr_get_layout_template( $layout_id, $type_layout, self::LAYOUT_FIELD_TEMPLATE_CSS );
	}

	/**
	 * Retrieve the Twig template js from a layout id
	 *
	 * @param $layout_id
	 * @param $type_layout
	 *
	 * @return string
	 */
	public static function wpsolr_get_layout_template_js( $layout_id, $type_layout ) {

		return self::wpsolr_get_layout_template( $layout_id, $type_layout, self::LAYOUT_FIELD_TEMPLATE_JS );
	}

	/**
	 * Retrieve the layout of a widget $instance
	 *
	 * @param $instance
	 *
	 * @param $type_layout
	 *
	 * @return array Layout of the widget instance
	 * @throws Exception
	 * @throws WPSOLR_Exception
	 */
	protected function wpsolr_get_instance_layout( $instance, $type_layout ) {

		if ( empty( $instance[ self::FORM_FIELD_LAYOUT_ID ] ) ) {

			throw new WPSOLR_Exception( 'no layout selected.' );
		}

		$layout = $this->get_layout( $instance[ self::FORM_FIELD_LAYOUT_ID ], $type_layout );

		$layout[ self::LAYOUT_FIELD_TEMPLATE_HTML ] = $this->wpsolr_get_instance_template( $instance, $type_layout, self::LAYOUT_FIELD_TEMPLATE_HTML );
		$layout[ self::LAYOUT_FIELD_TEMPLATE_CSS ]  = $this->wpsolr_get_instance_template( $instance, $type_layout, self::LAYOUT_FIELD_TEMPLATE_CSS );

		return $layout;
	}

	/**
	 * Retrieve the layout of a widget $instance
	 *
	 * @param $layout_id
	 * @param $type_layout
	 *
	 * @return array Layout of the widget instance
	 * @throws Exception
	 * @internal param $instance
	 *
	 */
	protected function get_layout( $layout_id, $type_layout ) {


		$layouts = $this->wpsolr_get_layout_definitions( $type_layout );

		if ( ! empty( $layouts[ $layout_id ] ) ) {

			return $layouts[ $layout_id ];
		}

		throw new WPSOLR_Exception( sprintf( 'Undefined layout \'%s\'', $layout_id ) );
	}


	/**
	 * Retrieve the default twig template file for the widget
	 *
	 * @param $template_field_name
	 *
	 * @param $type_layout
	 *
	 * @return string
	 */
	protected
	function wpsolr_get_default_twig_template_name(
		$template_field_name, $type_layout
	) {

		return $this->wpsolr_get_layout_template( self::GENERIC_LAYOUT_ID, $type_layout, $template_field_name );
	}

	/**
	 * Load all widget classes in this very directory.
	 */
	public
	static function wpsolr_autoload() {

		add_action( 'widgets_init', function () {

			// Loop on all widget files in current directory
			$widget_file_pattern = dirname( __FILE__ ) . "/" . WPSOLR_Widget::WPSOLR_WIDGET_CLASS_NAME_PREFIX . "*.php";
			foreach ( glob( $widget_file_pattern ) as $file ) {

				//  The widget class name is base name of file, without the extension
				$widget_class_name = basename( $file, '.php' );

				// Include widget file
				include_once $widget_class_name . '.php';

				// Register widget
				register_widget( __NAMESPACE__ . '\\' . $widget_class_name );
			}

		} );
	}

	/**
	 * Get all Twig template layouts of this widget
	 *
	 * @param string $type_layout Type of layout
	 *
	 * @return array
	 */
	protected
	static function wpsolr_get_layout_definitions(
		$type_layout
	) {
		return static::$wpsolr_layouts[ $type_layout ];
	}

	/**
	 * Get default layout definition
	 *
	 * @return array
	 */
	public
	static function wpsolr_get_default_layout_definition() {
		return static::wpsolr_get_layout_definitions()[ self::GENERIC_LAYOUT_ID ];
	}

	/**
	 * Get all layouts file content
	 *
	 * @param string $type_layout Type of layout
	 *
	 * @return array
	 */
	private function get_twig_layout_contents( $type_layout ) {
		$results = [ ];
		$layouts = $this->wpsolr_get_layout_definitions( $type_layout );

		foreach ( $layouts as $key => $layout ) {

			$results[ $key ] = [
				self::LAYOUT_FIELD_TEMPLATE_HTML => WPSOLR_Global::getTwig()->get_twig_template_file_content( $layout[ self::LAYOUT_FIELD_TEMPLATE_HTML ] ),
				self::LAYOUT_FIELD_TEMPLATE_CSS  => WPSOLR_Global::getTwig()->get_twig_template_file_content( $layout[ self::LAYOUT_FIELD_TEMPLATE_CSS ] )
			];
		}

		return $results;
	}

	/**
	 * Is the current url in the $instance regexp definition ?
	 *
	 * @param $instance
	 */
	protected function wpsolr_url_is_authorized( $instance ) {

		$url_regexp_lines = $this->wpsolr_get_instance_url_regexp( $instance );

		if ( $url_regexp_lines == null ) {
			// No url regexp defined on the widget: all url are authorized.
			return true;
		}

		// Is current url matching one of the regexp lines ?
		return WPSOLR_Regexp::preg_match_lines_of_regexp( $url_regexp_lines, WPSOLR_Query_Parameters::get_current_page_url() );
	}

	/**
	 * Validate a regexp
	 *
	 * @param $regexp
	 *
	 * @return bool
	 */
	protected function wpsolr_validate_regex( $regexp ) {

		if ( empty( $regexp ) ) {
			// No regex is valid regexp
			return true;
		}

		// Validate on null
		// @is used to suppress the annoying warning if regexp is in syntax error
		$preg_match = @preg_match( $regexp, null, $matches );

		return ( $preg_match !== false );
	}

	protected function wpsolr_get_instance_url_regexp( $instance ) {

		return ! empty( $instance[ self::FORM_FIELD_URL_REGEXP ] ) ? $instance[ self::FORM_FIELD_URL_REGEXP ] : null;
	}

	/**
	 * List of groups available for the widget
	 *
	 * @throws Exception
	 */
	public function wpsolr_get_groups() {
		throw new Exception( "Groups list not implemented in the widget." );
	}

	/**
	 * Get the group id from the widget instance
	 *
	 * @param $instance
	 *
	 * @return string Group id
	 */
	public function wpsolr_get_instance_group_id( $instance ) {

		return ! empty( $instance[ self::FORM_FIELD_GROUP_ID ] ) ? $instance[ self::FORM_FIELD_GROUP_ID ] : '';
	}

	/**
	 * Get all content layouts of the widget
	 * @return array
	 */
	public static function wpsolr_get_group_element_layouts() {
		return self::wpsolr_get_layout_definitions( self::TYPE_GROUP_ELEMENT_LAYOUT );
	}

	/**
	 * Get all group layouts of the widget
	 * @return array
	 */
	public static function wpsolr_get_group_layouts() {
		return self::wpsolr_get_layout_definitions( self::TYPE_GROUP_LAYOUT );
	}

}