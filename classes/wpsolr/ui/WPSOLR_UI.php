<?php

namespace wpsolr\ui;

use wpsolr\ui\widget\WPSOLR_Widget;
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
	 * @param $facets_group_id
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
	public static function Build( $facets_group_id, $data, $localization_options, $widget_args, $widget_instance, $layout ) {

		$html = '';

		// Twig parameters delegated to child classes
		$twig_parameters = static::create_twig_parameters( $data, $localization_options, $widget_instance );

		// JS template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$layout[ WPSOLR_Widget::LAYOUT_FIELD_TEMPLATE_JS ],
			array_merge(
				$twig_parameters
			)
		);

		// CSS template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$layout[ WPSOLR_Widget::LAYOUT_FIELD_TEMPLATE_CSS ],
			array_merge(
				$twig_parameters,
				array(
					'plugin_dir_url' => self::plugin_dir_url()
				)
			)
		);

		// HTML template
		$html .= WPSOLR_Global::getTwig()->getTwigEnvironment()->render(
			$layout[ WPSOLR_Widget::LAYOUT_FIELD_TEMPLATE_HTML ],
			array_merge(
				$twig_parameters,
				array(
					'facets_group_id' => $facets_group_id,
					'widget_args'     => $widget_args,
					'plugin_dir_url'  => self::plugin_dir_url()
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
