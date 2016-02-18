<?php

namespace wpsolr\ui\shortcode;

use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\ui\WPSOLR_UI_Filter;

/**
 * Class WPSOLR_Shortcode_Filter
 * Display filters shorcode
 * @package wpsolr\ui\shortcode
 */
class WPSOLR_Shortcode_Filter extends WPSOLR_Shortcode {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$this->shortcode_name = 'wpsolr_shortcode_filters';
		$this->layout_type    = WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET_FILTER_GROUP;
	}


	/**
	 * Returns the UI object
	 *
	 * @return WPSOLR_UI_Filter
	 */
	public function get_ui() {
		return new WPSOLR_UI_Filter();
	}

}