<?php

namespace wpsolr\ui\shortcode;

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
	}

	/**
	 * @return WPSOLR_UI_Filter
	 */
	public function get_ui() {
		return new WPSOLR_UI_Filter();
	}

}