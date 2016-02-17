<?php

namespace wpsolr\ui\shortcode;

use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\ui\WPSOLR_UI_Sort;

/**
 * Class WPSOLR_Shortcode_Sort
 * Display sort shorcode
 * @package wpsolr\ui\shortcode
 */
class WPSOLR_Shortcode_Sort extends WPSOLR_Shortcode {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$this->shortcode_name = 'wpsolr_shortcode_sort';
		$this->layout_type    = WPSOLR_Options_Layouts::TYPE_LAYOUT_SORT_GROUP;
	}


	/**
	 * Returns the UI object
	 *
	 * @return WPSOLR_UI_Sort
	 */
	protected function get_ui() {
		return new WPSOLR_UI_Sort();
	}

}