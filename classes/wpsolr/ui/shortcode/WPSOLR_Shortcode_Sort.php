<?php

namespace wpsolr\ui\shortcode;

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
		$this->shortcode_name = 'wpsolr_shortcode_sorts';
		$this->ui             = new WPSOLR_UI_Sort();
	}

}