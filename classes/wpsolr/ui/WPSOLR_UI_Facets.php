<?php

namespace wpsolr\ui;

use wpsolr\extensions\localization\WPSOLR_Localization;

/**
 * Display facets
 *
 * Class WPSOLR_UI_Facets
 */
class WPSOLR_UI_Facets extends WPSOLR_UI {

	public static function create_twig_parameters( $data, $localization_options ) {

		return array(
			'facets_header'              => WPSOLR_Localization::get_term( $localization_options, 'facets_header' ),
			'facets_title'               => WPSOLR_Localization::get_term( $localization_options, 'facets_title' ),
			'facets_element'             => WPSOLR_Localization::get_term( $localization_options, 'facets_element' ),
			'facets_element_all_results' => WPSOLR_Localization::get_term( $localization_options, 'facets_element_all_results' ),
			'facets'                     => $data
		);

	}
}
