<?php

namespace wpsolr\ui\widget;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\ui\WPSOLR_Query_Parameters;
use wpsolr\utilities\WPSOLR_Global;


/**
 * Top level widget class from which all WPSOLR widgets inherit.
 */
class WPSOLR_Widget extends \WP_Widget {

	// All WPOLR Widget classes must begin with this prefix to be autoloaded.
	const WPSOLR_WIDGET_CLASS_NAME_PREFIX = 'WPSOLR_Widget_';

	// Layouts of the widgets
	protected static $wpsolr_layouts;

	// Default layout id
	const DEFAULT_LAYOUT_ID = 'default';

	// Layout fields in array definition
	const LAYOUT_FIELD_TEMPLATE_HTML = 'template_html';
	const LAYOUT_FIELD_TEMPLATE_CSS = 'template_css';
	const LAYOUT_FIELD_TEMPLATE_NAME = 'name';

	// Form fields
	const FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_STRING = 'is_custom_twig_template_string';
	const FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_CSS_STRING = 'is_custom_twig_template_css_string';
	const FORM_FIELD_CUSTOM_TWIG_TEMPLATE_STRING = 'custom_twig_template_string';
	const FORM_FIELD_CUSTOM_TWIG_TEMPLATE_CSS_STRING = 'custom_twig_template_css_string';
	const FORM_FIELD_LAYOUT_ID = 'layout_id';
	const FORM_FIELD_URL_REGEXP = 'url_regexp';
	const FORM_FIELD_ERROR_MESSAGE = 'error_message';

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

		if ( empty( $this->wpsolr_uuid ) ) {
			$this->wpsolr_uuid = uniqid();
		}

		$is_custom_twig_template_string     = ! empty( $instance[ self::FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_STRING ] );
		$is_custom_twig_template_css_string = ! empty( $instance[ self::FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_CSS_STRING ] );

		$custom_twig_template_string = ! empty( $instance[ self::FORM_FIELD_CUSTOM_TWIG_TEMPLATE_STRING ] )
			? $instance[ self::FORM_FIELD_CUSTOM_TWIG_TEMPLATE_STRING ]
			: WPSOLR_Global::getTwig()->get_twig_template_file_content( $this->wpsolr_get_default_twig_template_name( self::LAYOUT_FIELD_TEMPLATE_HTML ) );

		$custom_twig_template_css_string = ! empty( $instance[ self::FORM_FIELD_CUSTOM_TWIG_TEMPLATE_CSS_STRING ] )
			? $instance[ self::FORM_FIELD_CUSTOM_TWIG_TEMPLATE_CSS_STRING ]
			: WPSOLR_Global::getTwig()->get_twig_template_file_content( $this->wpsolr_get_default_twig_template_name( self::LAYOUT_FIELD_TEMPLATE_CSS ) );

		$url_regexp = ! empty( $instance[ self::FORM_FIELD_URL_REGEXP ] )
			? $instance[ self::FORM_FIELD_URL_REGEXP ] : '';


		$layout_id = ! empty( $instance[ self::FORM_FIELD_LAYOUT_ID ] ) ? $instance[ self::FORM_FIELD_LAYOUT_ID ] : self::DEFAULT_LAYOUT_ID;
		$layouts   = $this->wpsolr_get_layout_definitions();

		/**
		 * We need to generate our own unique ids for Jquery, because get_field_id() does not work outside of html elements
		 */
		$uuid_block_custom_twig_template_string = uniqid();
		$uuid_is_custom_twig_template_string    = uniqid();
		$uuid_custom_twig_template_string       = uniqid();

		$uuid_block_custom_twig_template_css_string = uniqid();
		$uuid_is_custom_twig_template_css_string    = uniqid();
		$uuid_custom_twig_template_css_string       = uniqid();

		$uuid_layout_id = uniqid();
		?>

		<script>
			jQuery(document).ready(function () {

				/* Hide/Show the custom Twig template area */
				function display_twig_template_area(selector, checked) {

					if (checked) {
						jQuery(selector).show();
					} else {
						jQuery(selector).hide();
					}
				}

				/* Declare jQuery event to copy layout file in custom text area*/
				function declare_event_paste_layout_file_in_textarea(event_selector, text_area_selector, template_type) {

					/* Copy default template value in textarea when clicking on link */
					jQuery(event_selector).on('click', function () {

						var layout_id = jQuery('.<?php echo $uuid_layout_id; ?>').val();

						// Copy default template value in textarea
						jQuery(text_area_selector).val(layouts_contents[layout_id][template_type]);

						// Simulate user change to trigger customizer front refresh
						jQuery(text_area_selector).change();

					});
				}

				var layouts_contents = <?php echo json_encode( $this->get_twig_layout_contents() ); ?>;

				var uuid_block_custom_twig_template_string_selector = '#<?php echo $uuid_block_custom_twig_template_string; ?>';
				display_twig_template_area(uuid_block_custom_twig_template_string_selector, <?php echo json_encode( $is_custom_twig_template_string ); ?>);
				/* Detect change of checkbox selection */
				jQuery('.<?php echo $uuid_is_custom_twig_template_string; ?>').on('change', function () { // on change of state
					display_twig_template_area(uuid_block_custom_twig_template_string_selector, this.checked);
				});

				var uuid_block_custom_twig_template_string_css_selector = '#<?php echo $uuid_block_custom_twig_template_css_string; ?>';
				display_twig_template_area(uuid_block_custom_twig_template_string_css_selector, <?php echo json_encode( $is_custom_twig_template_css_string ); ?>);
				/* Detect change of checkbox selection */
				jQuery('.<?php echo $uuid_is_custom_twig_template_css_string; ?>').on('change', function () { // on change of state
					display_twig_template_area(uuid_block_custom_twig_template_string_css_selector, this.checked);
				});


				declare_event_paste_layout_file_in_textarea('#<?php echo 'reset_' . $uuid_custom_twig_template_string; ?>', '.<?php echo $uuid_custom_twig_template_string; ?>', '<?php echo self::LAYOUT_FIELD_TEMPLATE_HTML; ?>');
				declare_event_paste_layout_file_in_textarea('#<?php echo 'reset_' . $uuid_custom_twig_template_css_string; ?>', '.<?php echo $uuid_custom_twig_template_css_string; ?>', '<?php echo self::LAYOUT_FIELD_TEMPLATE_CSS; ?>');

			});
		</script>

		<?php
		/* Let each widget write it's own header here */
		$this->wpsolr_header( $instance );
		?>

		<p>
			You can also write your own widget HTML code below. The code of the layout must be in
			<a href="http://twig.sensiolabs.org/" target="_blank">Twig</a>.
		</p>

		<div style='border: 1px solid rgb(228, 228, 228);padding:7px;'>
			<p>
				Use layout:
				<select class="<?php echo $uuid_layout_id; ?>"
				        id="<?php echo $this->get_field_id( self::FORM_FIELD_LAYOUT_ID ); ?>"
				        name="<?php echo $this->get_field_name( self::FORM_FIELD_LAYOUT_ID ); ?>">
					<?php foreach ( $layouts as $index => $layout ) { ?>
						<option
							value="<?php echo $index; ?>" <?php selected( $layout_id, $index, true ) ?>><?php echo $layout[ self::LAYOUT_FIELD_TEMPLATE_NAME ]; ?></option>
					<?php } ?>
				</select>
			</p>

			<!-- Custom twig template block -->
			<p>
				<input class="checkbox <?php echo $uuid_is_custom_twig_template_string; ?>"
				       type="checkbox"<?php checked( $is_custom_twig_template_string ); ?>
				       id="<?php echo $this->get_field_id( self::FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_STRING ); ?>"
				       name="<?php echo $this->get_field_name( self::FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_STRING ); ?>"/>
				<label
					for="<?php echo $this->get_field_id( self::FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_STRING ); ?>"><?php _e( 'or use your own <a href="http://twig.sensiolabs.org/" target="_blank">Twig</a> layout' ); ?></label>
			</p>
			<p id="<?php echo $uuid_block_custom_twig_template_string; ?>">
				<a id="<?php echo 'reset_' . $uuid_custom_twig_template_string; ?>"
				   href="javascript:void(0);" title="Replace current template with default template.">Paste selected
					layout</a>
			<textarea rows="15" class="widefat <?php echo $uuid_custom_twig_template_string; ?>"
			          id="<?php echo $this->get_field_id( self::FORM_FIELD_CUSTOM_TWIG_TEMPLATE_STRING ); ?>"
			          name="<?php echo $this->get_field_name( self::FORM_FIELD_CUSTOM_TWIG_TEMPLATE_STRING ); ?>"><?php echo $custom_twig_template_string; ?></textarea>
			</p>
		</div>

		<!-- Custom twig template css block -->
		<p>
			<input class="checkbox <?php echo $uuid_is_custom_twig_template_css_string; ?>"
			       type="checkbox"<?php checked( $is_custom_twig_template_css_string ); ?>
			       id="<?php echo $this->get_field_id( self::FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_CSS_STRING ); ?>"
			       name="<?php echo $this->get_field_name( self::FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_CSS_STRING ); ?>"/>
			<label
				for="<?php echo $this->get_field_id( self::FORM_FIELD_IS_CUSTOM_TWIG_TEMPLATE_CSS_STRING ); ?>"><?php _e( 'use your own css' ); ?></label>
		</p>
		<p id="<?php echo $uuid_block_custom_twig_template_css_string; ?>">
			<a id="<?php echo 'reset_' . $uuid_custom_twig_template_css_string; ?>"
			   href="javascript:void(0);" title="Replace current template css with default template.">Paste selected
				layout css</a>
			<textarea rows="15" class="widefat <?php echo $uuid_custom_twig_template_css_string; ?>"
			          id="<?php echo $this->get_field_id( self::FORM_FIELD_CUSTOM_TWIG_TEMPLATE_CSS_STRING ); ?>"
			          name="<?php echo $this->get_field_name( self::FORM_FIELD_CUSTOM_TWIG_TEMPLATE_CSS_STRING ); ?>"><?php echo $custom_twig_template_css_string; ?></textarea>
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
	 * @return string
	 */
	protected
	function wpsolr_get_instance_template(
		$instance, $template_type
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
		return $this->wpsolr_get_layout_template( ! empty( $instance[ self::FORM_FIELD_LAYOUT_ID ] ) ? $instance[ self::FORM_FIELD_LAYOUT_ID ] : null, $template_type );
	}


	/**
	 * Retrieve the Twig template from a layout id
	 *
	 * @return string
	 */
	protected
	function wpsolr_get_layout_template(
		$layout_id, $template_type
	) {

		$layouts = $this->wpsolr_get_layout_definitions();

		if ( ! empty( $layouts[ $layout_id ] ) ) {

			return $layouts[ $layout_id ][ $template_type ];
		}


		/**
		 * Return the twig template of the default layout
		 */
		return $layouts[ self::DEFAULT_LAYOUT_ID ][ $template_type ];
	}

	/**
	 * Retrieve the layout of a widget $instance
	 *
	 * @param $instance
	 *
	 * @return array Layout of the widget instance
	 */
	protected function wpsolr_get_instance_layout( $instance ) {

		if ( empty( $instance[ self::FORM_FIELD_LAYOUT_ID ] ) ) {

			throw new WPSOLR_Exception( 'no layout selected.' );
		}

		$layout = $this->get_layout( $instance[ self::FORM_FIELD_LAYOUT_ID ] );

		$layout[ self::LAYOUT_FIELD_TEMPLATE_HTML ] = $this->wpsolr_get_instance_template( $instance, self::LAYOUT_FIELD_TEMPLATE_HTML );
		$layout[ self::LAYOUT_FIELD_TEMPLATE_CSS ]  = $this->wpsolr_get_instance_template( $instance, self::LAYOUT_FIELD_TEMPLATE_CSS );

		return $layout;
	}

	/**
	 * Retrieve the layout of a widget $instance
	 *
	 * @param $instance
	 *
	 * @return array Layout of the widget instance
	 */
	protected function get_layout( $layout_id ) {


		$layouts = $this->wpsolr_get_layout_definitions();

		if ( ! empty( $layouts[ $layout_id ] ) ) {

			return $layouts[ $layout_id ];
		}

		throw new Exception( sprintf( 'Undefined layout \'%s\'', $layout_id ) );
	}


	/**
	 * Retrieve the default twig template file for the widget
	 *
	 * @param $template_field_name
	 *
	 * @return string
	 */
	protected
	function wpsolr_get_default_twig_template_name(
		$template_field_name
	) {

		return $this->wpsolr_get_layout_template( self::DEFAULT_LAYOUT_ID, $template_field_name );
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
	 * @return array
	 */
	protected
	static function wpsolr_get_layout_definitions() {
		return static::$wpsolr_layouts;
	}

	/**
	 * Get default layout definition
	 *
	 * @return array
	 */
	public
	static function wpsolr_get_default_layout_definition() {
		return static::wpsolr_get_layout_definitions()[ self::DEFAULT_LAYOUT_ID ];
	}

	/**
	 * Get all layouts file content
	 * @return array
	 */
	private function get_twig_layout_contents() {
		$results = [ ];
		$layouts = $this->wpsolr_get_layout_definitions();

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

		$url_regexp = $this->wpsolr_get_instance_url_regexp( $instance );

		if ( $url_regexp == null ) {
			// No url regexp defined on the widget: all url are authorized.
			return true;
		}

		$url = WPSOLR_Query_Parameters::get_current_page_url();

		// Is the url found in the regexp ?
		// @is used to suppress the annoying warning if regexp is in syntax error
		$preg_match = @preg_match( $url_regexp, $url, $matches );


		if ( $preg_match === false ) {
			// regexp syntax error: no url is authorized
			throw new WPSOLR_Exception( sprintf( 'Invalid Regexp \'%s\'.', $url_regexp ) );
		}

		// Authorized url if regex matched
		return ( $matches != null );
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
}