<?php

namespace wpsolr\ui;

use wpsolr\extensions\localization\WPSOLR_Localization;

/**
 * Display Sort List
 *
 */
class WPSOLR_UI_Sort extends WPSOLR_UI {

	public static function create_twig_parameters( $data, $localization_options ) {

		return array(
			'sort_header' => WPSOLR_Localization::get_term( $localization_options, 'sort_header' ),
			'sort_list'   => $data
		);

	}

}
