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
	 * @param string $layout Layout to render (default file or custom string)
	 * @param array $data Data from Solr
	 * @param array $localization_options
	 * @param array $widget_args
	 *
	 * @return string
	 */
	public static function Build( $layout, $data, $localization_options, $widget_args ) {

		$html = '';

		// Twig parameters delegated to child classes
		$twig_parameters = static::create_twig_parameters( $data, $localization_options );

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
	 * @return array
	 */
	public static function create_twig_parameters( $data, $localization_options ) {
		dies( 'Missing implementation.' );
	}

}
