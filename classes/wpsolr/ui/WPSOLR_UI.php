<?php

namespace wpsolr\ui;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Regexp;

/**
 * Facets root.
 *
 */
class WPSOLR_UI {

	// Form fields
	const FORM_FIELD_GROUP_ID = 'group_id';
	const FORM_FIELD_GROUP_NAME = 'name';
	const FORM_FIELD_LAYOUT_ID = 'layout_id';
	const FORM_FIELD_URL_REGEXP = 'url_regexp';
	const FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END = 'is_show_title_on_front_end';
	const FORM_FIELD_IS_SHOW_WHEN_EMPTY = 'is_show_widget_when_empty';
	const FORM_FIELD_TITLE = 'title';
	const FORM_FIELD_BEFORE_TITLE = 'before_title';
	const FORM_FIELD_AFTER_TITLE = 'after_title';
	const FORM_FIELD_BEFORE_UI = 'before_widget';
	const FORM_FIELD_AFTER_UI = 'after_widget';

	// Data extracted from Solr search results
	protected $data;

	protected $group_id;
	protected $layout_id;
	protected $layout;
	protected $title;
	protected $before_title;
	protected $after_title;
	protected $before_ui;
	protected $after_ui;
	protected $layout_type;
	protected $is_show_when_no_data;
	protected $is_show_title_on_front_end;

	/**
	 * Calculate the plugin root directory url.
	 * Use in templates to include images, css and js documents.
	 */
	protected static function plugin_dir_url() {

		return substr_replace( plugin_dir_url( __FILE__ . '../../../../../..' ), "", - 1 );
	}

	/**
	 * Front-end display of UI.
	 *
	 */
	public function display(
		$name, $layout_id, $group_id, $url_regexp_lines, $is_show_when_no_data, $is_show_title_on_front_end,
		$title, $before_title, $after_title, $before_ui, $after_ui
	) {

		/**
		 * Only display a widget when:
		 * - Current url is a WP search page
		 * - WPSOLR is replacing the default search
		 * - WPSOLR displays the current theme search/seach form templates
		 * - Current widget is not empty, or setup to show when empty
		 *
		 */

		try {

			// ui elements
			$this->name                 = $name;
			$this->is_show_title_on_front_end = $is_show_title_on_front_end;
			$this->is_show_when_no_data = $is_show_when_no_data;
			$this->title                = $is_show_title_on_front_end ? $title : '';
			$this->before_title         = $is_show_title_on_front_end ? $before_title : '';
			$this->after_title          = $is_show_title_on_front_end ? $after_title : '';
			$this->before_ui            = $before_ui;
			$this->after_ui             = $after_ui;

			$this->group_id  = $group_id;
			$this->layout_id = $layout_id;
			$this->layout    = $this->get_layout();

			// Extract data
			$this->data = $this->extract_data_with_cache();

			if ( $this->url_is_authorized( $url_regexp_lines ) && ( $is_show_when_no_data || ! $this->is_data_empty() ) ) {

				return $this->get_display_form();
			}

		} catch ( WPSOLR_Exception $e ) {

			// Display custom error in Widget area
			return sprintf( '<div style=\'margin:10px;\'>Error in %s: %s</div>', $this->name, $e->get_message() );
		}

	}

	/**
	 * Retrieve the layout
	 *
	 * @param string $layout_id
	 *
	 * @return array Layout of the widget instance
	 * @throws Exception
	 */
	protected function get_layout() {

		$layout = WPSOLR_Global::getExtensionLayouts()->get_layout_from_type_and_id( $this->layout_type, $this->layout_id );

		return $layout;
	}

	/**
	 * Get the form to be displayed on front-end.
	 *
	 * @return string
	 */
	protected function get_display_form() {

		return $this->build_from_templates(
			WPSOLR_Localization::get_options()
		);

	}

	/**
	 * Is the current url in the regexp definition ?
	 *
	 * @param $instance
	 *
	 * @return bool
	 * @throws WPSOLR_Exception
	 */
	protected function url_is_authorized( $url_regexp_lines ) {

		if ( $url_regexp_lines == null ) {
			// No url regexp defined on the widget: all url are authorized.
			return true;
		}

		// Is current url matching one of the regexp lines ?
		return WPSOLR_Regexp::preg_match_lines_of_regexp( $url_regexp_lines, WPSOLR_Query_Parameters::get_current_page_url() );
	}

	/**
	 * Verify if data is empty
	 *
	 * @return bool
	 */
	protected function is_data_empty() {
		// Override in children
		return false;
	}


	/**
	 * Get data to be displayed by the widget.
	 * Use the cached data if there.
	 *
	 * [ 'group_id' => $group_id, 'data' => $data ];
	 *
	 * @return array
	 */
	protected function extract_data_with_cache() {

		if ( isset( $this->data ) ) {
			// Get cache
			return $this->data;
		}

		// No cache: create it.
		$this->data = $this->extract_data();

		return $this->data;
	}

	/**
	 * Prepare data to be displayed by the widget.
	 *
	 * [ 'group_id' => $group_id, 'data' => $data ];
	 *
	 * @param $args
	 * @param $instance
	 */
	protected function extract_data() {

		// Override in children.
		return [ ];
	}

	/**
	 * Build facets UI
	 *
	 * @param $group_id
	 * @param array $data Data from Solr
	 * @param array $localization_options
	 * @param array $widget_args
	 *
	 * @param $widget_instance
	 *
	 * @param string $layout Layout to render (default file or custom string)
	 *
	 * @return string
	 */
	public function build_from_templates( $localization_options ) {

		$html = '';

		// Twig parameters delegated to child classes
		$twig_parameters = static::create_twig_parameters( $localization_options );

		// JS template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$this->layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_JS ],
			array_merge(
				$twig_parameters,
				array(
					'group_id' => $this->group_id
				)
			)
		);

		// CSS template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$this->layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_CSS ],
			array_merge(
				$twig_parameters,
				array(
					'plugin_dir_url' => self::plugin_dir_url()
				)
			)
		);

		// HTML template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$this->layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_HTML ],
			array_merge(
				$twig_parameters,
				array(
					'group_id'       => $this->group_id,
					'widget_args'    => [
						'before_widget' => $this->before_ui,
						'after_widget'  => $this->after_ui,
						'before_title'  => $this->before_title,
						'after_title'   => $this->after_title
					],
					'plugin_dir_url' => self::plugin_dir_url()
				)
			)
		);

		return $html;

	}

	/**
	 * Create Twig parameters
	 *
	 * @param $data
	 * @param $localization_options
	 *
	 * @param $widget_instance
	 *
	 * @return array
	 */
	public function create_twig_parameters( $localization_options ) {
		dies( 'Missing implementation.' );
	}

	/**
	 * Returns the groups of a ui
	 *
	 * @return object
	 */
	public function get_groups() {
		die( 'get_groups not implemented' );
	}

}
