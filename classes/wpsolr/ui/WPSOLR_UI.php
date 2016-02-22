<?php

namespace wpsolr\ui;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Regexp;
use wpsolr\WPSOLR_Filters;

/**
 * Facets root.
 *
 */
class WPSOLR_UI {

	// Form fields
	const FORM_FIELD_RESULTS_PAGE = 'results_page';
	const FORM_FIELD_SEARCH_METHOD = 'search_method';
	const FORM_FIELD_SEARCH_METHOD_VALUE_USE_CUSTOM_PAGE = 'use_custom_page';
	const FORM_FIELD_SEARCH_METHOD_VALUE_NO_AJAX = 'no_ajax';
	const FORM_FIELD_SEARCH_METHOD_VALUE_AJAX = 'ajax';
	const FORM_FIELD_SEARCH_METHOD_VALUE_AJAX_WITH_PARAMETERS = 'ajax_with_parameters';
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
	const FORM_FIELD_IS_DEBUG_JS = 'is_debug_js';
	const FORM_FIELD_COMPONENT_TYPE = 'component_type';
	const FORM_FIELD_COMPONENT_ID = 'component_id';

	// Data extracted from Solr search results
	protected $data;

	protected $results_page;
	protected $group_id;
	protected $layout_id;
	protected $layout_type;
	protected $layout;
	protected $title;
	protected $before_title;
	protected $after_title;
	protected $before_ui;
	protected $after_ui;
	protected $component_type;
	protected $is_show_when_no_data;
	protected $is_show_title_on_front_end;
	protected $is_debug_js;
	protected $search_method;
	protected $url_regexp_lines;
	protected $component_id;

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
		$name, $component_id, $title, $before_title, $after_title, $before_ui, $after_ui
	) {

		try {

			$extension_components = WPSOLR_Global::getExtensionComponents();
			$component            = $extension_components->get_component_by_id( $component_id );

			// Component elements
			$this->component_id               = $component_id;
			$this->name                       = $name;
			$this->search_method              = $extension_components->get_search_method( $component );
			$this->results_page               = $extension_components->get_results_page( $component );
			$this->is_debug_js                = $extension_components->get_component_is_debug_js( $component );
			$this->is_show_when_no_data       = $extension_components->get_component_is_show_when_empty( $component );
			$this->is_show_title_on_front_end = $extension_components->get_component_is_show_title_on_front_end( $component );
			$this->title                      = $this->is_show_title_on_front_end ? apply_filters( WPSOLR_Filters::WPSOLR_FILTER_TRANSLATION_STRING, ! empty( $title ) ? $title : $extension_components->get_component_title( $component ) ) : '';
			$this->before_title               = $this->is_show_title_on_front_end ? ! empty( $before_title ) ? $before_title : $extension_components->get_component_before_title( $component ) : '';
			$this->after_title                = $this->is_show_title_on_front_end ? ! empty( $after_title ) ? $after_title : $extension_components->get_component_after_title( $component ) : '';
			$this->before_ui                  = ! empty( $before_ui ) ? $before_ui : $extension_components->get_component_before_ui( $component );
			$this->after_ui                   = ! empty( $after_ui ) ? $after_ui : $extension_components->get_component_after_ui( $component );
			$this->url_regexp_lines           = $extension_components->get_component_url_regexp_lines( $component );

			$this->group_id  = $extension_components->get_component_group_id( $component );
			$this->layout_id = $extension_components->get_component_layout_id( $component );
			$this->layout    = $this->get_layout();

			// Extract data
			$this->data = $this->extract_data_with_cache();

			if ( $this->url_is_authorized( $this->url_regexp_lines ) && ( $this->is_show_when_no_data || ! $this->is_data_empty() ) ) {

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

		// Twig common parameters
		$twig_common_parameters = [
			'ui_id'                => $this->component_id,
			'query_page'           => $this->get_results_page_permalink(),
			'query_parameter_name' => $this->get_results_page_query_parameter_name(),
			'group_id'             => $this->group_id,
			'plugin_dir_url'       => self::plugin_dir_url(),
			'is_debug_js'          => json_encode( $this->is_debug_js ),
			// encoding required for true/false being sent to twig
			'is_ajax'              => json_encode( $this->get_is_search_method_ajax() )
		];

		// JS template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$this->layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_JS ],
			array_merge(
				$twig_common_parameters,
				$twig_parameters
			)
		);

		// CSS template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$this->layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_CSS ],
			array_merge(
				$twig_common_parameters,
				$twig_parameters
			)
		);

		// HTML template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$this->layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_HTML ],
			array_merge(
				$twig_common_parameters,
				array(
					'widget_args' => [
						'before_widget' => $this->before_ui,
						'after_widget'  => $this->after_ui,
						'before_title'  => $this->before_title,
						'after_title'   => $this->after_title
					]
				),
				$twig_parameters
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
	 * @return array
	 */
	public function get_groups() {
		die( 'get_groups not implemented' );
	}

	/**
	 * Returns the layouts of a ui
	 *
	 * @return array
	 */
	public function get_layouts() {
		return WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( $this->layout_id );
	}

	/**
	 * Get permalink of the results page
	 *
	 * @return string
	 */
	private function get_results_page_permalink() {

		// Standard search
		if ( $this->get_is_results_page_theme_search_page() ) {
			return get_home_url();
		}

		// A custom page
		if ( $this->get_is_search_method_custom_page() ) {
			return get_permalink( $this->results_page );
		}

		// Current home page
		if ( is_home() ) {
			return get_home_url();
		}

		// Current page
		return get_permalink( get_post() );
	}

	/**
	 * Get query parameter name of the results page
	 *
	 * @return string
	 */
	private function get_results_page_query_parameter_name() {

		// Standard search, or wpsolr search
		$result = $this->get_is_results_page_theme_search_page() ? WPSOLR_Query_Parameters::SEARCH_PARAMETER_S : WPSOLR_Query_Parameters::SEARCH_PARAMETER_Q;

		return $result;
	}

	/**
	 * Is results page the theme search page ?
	 *
	 * @return boolean
	 */
	private function get_is_results_page_theme_search_page() {

		return ( $this->get_is_search_method_custom_page() && empty( trim( $this->results_page ) ) );
	}

	/**
	 * Is search method a custom page ?
	 *
	 * @return boolean
	 */
	private function get_is_search_method_custom_page() {

		return ( $this->search_method == self::FORM_FIELD_SEARCH_METHOD_VALUE_USE_CUSTOM_PAGE );
	}

	/**
	 * Is search method ajax ?
	 *
	 * @return boolean
	 */
	private function get_is_search_method_ajax() {

		return ( ( $this->search_method == self::FORM_FIELD_SEARCH_METHOD_VALUE_AJAX ) || ( $this->search_method == self::FORM_FIELD_SEARCH_METHOD_VALUE_AJAX_WITH_PARAMETERS ) );
	}

	/**
	 * Get component type
	 *
	 * @return string
	 */
	public function get_component_type() {
		return $this->component_type;
	}

	/**
	 * Get components for the UI
	 *
	 * @return array
	 */
	public function get_components() {
		return WPSOLR_Global::getExtensionComponents()->get_components_from_type( $this->get_component_type() );
	}


}
