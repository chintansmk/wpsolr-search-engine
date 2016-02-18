<?php

namespace wpsolr\ui\widget;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\ui\WPSOLR_UI;
use wpsolr\utilities\WPSOLR_Global;


/**
 * Top level widget class from which all WPSOLR widgets inherit.
 */
class WPSOLR_Widget extends \WP_Widget {

	// Widget data, cached.
	protected $wpsolr_widget_data;

	// All WPOLR Widget classes must begin with this prefix to be autoloaded.
	const WPSOLR_WIDGET_CLASS_NAME_PREFIX = 'WPSOLR_Widget_';

	// Form fields
	const FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_STRING = 'is_custom_twig_template_string';
	const FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_CSS_STRING = 'is_custom_twig_template_css_string';
	const FORM_FIELD_CUSTOM_TWIG_TEMPLATE_STRING = 'custom_twig_template_string';
	const FORM_FIELD_CUSTOM_TWIG_TEMPLATE_CSS_STRING = 'custom_twig_template_css_string';
	const FORM_FIELD_ERROR_MESSAGE = 'error_message';
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

		$result = $this->get_ui()->display(
			$args['widget_name'],
			$this->wpsolr_get_instance_layout_id( $instance ),
			$this->wpsolr_get_instance_group_id( $instance ),
			$this->wpsolr_get_instance_url_regexp( $instance ),
			$this->wpsolr_get_instance_is_show_widget_when_empty( $instance ),
			$this->wpsolr_get_instance_is_show_title_on_front_end( $instance ),
			! empty( $instance['title'] ) ? $instance['title'] : '',
			! empty( $args['before_title'] ) ? $args['before_title'] : '',
			! empty( $args['after_title'] ) ? $args['after_title'] : '',
			! empty( $args['before_widget'] ) ? $args['before_widget'] : '',
			! empty( $args['after_widget'] ) ? $args['after_widget'] : ''
		);

		echo $result;
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

		$is_show_title_on_front_end     = ! empty( $instance[ WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END ] );
		$is_show_widget_when_no_content = ! empty( $instance[ WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY ] );

		// Regexp: urls that display the widget
		$url_regexp = ! empty( $instance[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] ) ? $instance[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] : '';

		// Layout: display the widget
		$layout_id = ! empty( $instance[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] ) ? $instance[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] : '';
		$layouts   = $this->wpsolr_get_layouts();

		// Group: content of the widget
		$group_id = ! empty( $instance[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] ) ? $instance[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] : '';
		$groups   = $this->get_ui()->get_groups();
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
			<label
				for="<?php echo $this->get_field_id( WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END ); ?>"><?php _e( 'Show title on front-end pages:' ); ?></label>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END ); ?>"
			       name="<?php echo $this->get_field_name( WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END ); ?>"
			       type="checkbox"
				<?php checked( $is_show_title_on_front_end ); ?>>
		</p>
		<p>
			<label
				for="<?php echo $this->get_field_id( WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY ); ?>"><?php _e( 'Show empty widget:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY ); ?>"
			       name="<?php echo $this->get_field_name( WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY ); ?>"
			       type="checkbox"
				<?php checked( $is_show_widget_when_no_content ); ?>>
		</p>
		<p>
			Use layout:
			<select
				id="<?php echo $this->get_field_id( WPSOLR_UI::FORM_FIELD_LAYOUT_ID ); ?>"
				name="<?php echo $this->get_field_name( WPSOLR_UI::FORM_FIELD_LAYOUT_ID ); ?>">
				<?php foreach ( $layouts as $layouts_layout_id => $layout ) { ?>
					<option
						value="<?php echo $layouts_layout_id; ?>" <?php selected( $layout_id, $layouts_layout_id, true ) ?>><?php echo WPSOLR_Global::getExtensionLayouts()->get_layout_name( $layout ); ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			Use group:
			<select id="<?php echo $this->get_field_id( WPSOLR_UI::FORM_FIELD_GROUP_ID ); ?>"
			        name="<?php echo $this->get_field_name( WPSOLR_UI::FORM_FIELD_GROUP_ID ); ?>">
				<?php foreach ( $groups as $current_group_id => $current_group ) { ?>
					<option
						value="<?php echo $current_group_id; ?>" <?php selected( $group_id, $current_group_id, true ) ?>><?php echo $current_group[ self::FORM_FIELD_GROUP_NAME ]; ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			The widget is displayed for <a href="https://regex101.com/" target="_blank">Regexp</a> url(s):
			<textarea rows="3" class="widefat"
			          id="<?php echo $this->get_field_id( WPSOLR_UI::FORM_FIELD_URL_REGEXP ); ?>"
			          name="<?php echo $this->get_field_name( WPSOLR_UI::FORM_FIELD_URL_REGEXP ); ?>"><?php echo $url_regexp; ?></textarea>
		</p>


		<?php
		return;

	}

	public function update( $new_instance, $old_instance ) {

		return $new_instance;
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
	 * @return array Layouts
	 */
	protected static function wpsolr_get_layouts() {
		die( 'wpsolr_get_layouts undefined' );
	}

	/**
	 * Show $instance title on front-end pages ?
	 *
	 * @param $instance
	 */
	protected function wpsolr_is_show_title_on_front_end( $instance ) {

		return ! empty( $instance[ WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END ] );
	}

	/**
	 * Show $instance when empty ?
	 *
	 * @param $instance
	 */
	protected function wpsolr_is_show_widget_when_empty( $instance ) {

		return ! empty( $instance[ WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY ] );
	}


	/**
	 * Is $instance widget empty ?
	 *
	 * @param $instance
	 */
	protected function wpsolr_is_widget_empty( $instance ) {

		// Override in children
		return false;
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

	/**
	 * Get instance url regexp
	 *
	 * @param $instance
	 *
	 * @return null
	 */
	protected function wpsolr_get_instance_url_regexp( $instance ) {

		return ! empty( $instance[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] ) ? $instance[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] : null;
	}

	/**
	 * Get the group id from the widget instance
	 *
	 * @param $instance
	 *
	 * @return string Group id
	 */
	public function wpsolr_get_instance_group_id( $instance ) {

		return ! empty( $instance[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] ) ? $instance[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] : '';
	}

	/**
	 * Get the layout id from the widget instance
	 *
	 * @param $instance
	 *
	 * @return string Layout id
	 */
	public function wpsolr_get_instance_layout_id( $instance ) {

		return ! empty( $instance[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] ) ? $instance[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] : '';
	}

	/**
	 * Returns the UI object
	 *
	 * @return WPSOLR_UI
	 */
	protected function get_ui() {
		die( 'get_ui not implemented' );
	}

	/**
	 * Show widget when no data ?
	 *
	 * @return bool
	 */
	public function wpsolr_get_instance_is_show_widget_when_empty( $instance ) {
		return ! empty( $instance[ WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY ] );
	}


	/**
	 * Show title on front-end ?
	 *
	 * @param $instance
	 *
	 * @return bool
	 */
	public function wpsolr_get_instance_is_show_title_on_front_end( $instance ) {
		return ! empty( $instance[ WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END ] );
	}

}