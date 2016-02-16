<?php

namespace wpsolr\ui;

use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Facets root.
 *
 */
class WPSOLR_UI {

	/**
	 * Calculate the plugin root directory url.
	 * Use in templates to include images, css and js documents.
	 */
	protected static function plugin_dir_url() {

		return substr_replace( plugin_dir_url( __FILE__ . '../../../../../..' ), "", - 1 );
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
	public static function Build( $group_id, $data, $localization_options, $widget_args, $widget_instance, $layout ) {

		$html = '';

		// Twig parameters delegated to child classes
		$twig_parameters = static::create_twig_parameters( $data, $localization_options, $widget_instance );

		// JS template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_JS ],
			array_merge(
				$twig_parameters,
				array(
					'group_id' => $group_id
				)
			)
		);

		// CSS template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_CSS ],
			array_merge(
				$twig_parameters,
				array(
					'plugin_dir_url' => self::plugin_dir_url()
				)
			)
		);

		// HTML template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_HTML ],
			array_merge(
				$twig_parameters,
				array(
					'group_id'       => $group_id,
					'widget_args'    => $widget_args,
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
	public static function create_twig_parameters( $data, $localization_options, $widget_instance ) {
		dies( 'Missing implementation.' );
	}

}
