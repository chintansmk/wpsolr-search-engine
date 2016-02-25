<?php

namespace wpsolr\ui\shortcode;

use wpsolr\ui\WPSOLR_UI_Result_Row;

/**
 * Class WPSOLR_Shortcode_Result_Row
 * Display results rows shorcode
 * @package wpsolr\ui\shortcode
 */
class WPSOLR_Shortcode_Result_Row extends WPSOLR_Shortcode {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$this->shortcode_name = 'wpsolr_shortcode_results_rows';
		$this->ui             = new WPSOLR_UI_Result_Row();
	}

}