<?php

namespace wpsolr\ui\shortcode;

use wpsolr\ui\WPSOLR_UI_Facet;

/**
 * Class WPSOLR_Shortcode_Facet
 * Display facets shorcode
 * @package wpsolr\ui\shortcode
 */
class WPSOLR_Shortcode_Facet extends WPSOLR_Shortcode {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$this->shortcode_name = 'wpsolr_shortcode_facets';
	}

	/**
	 * @return WPSOLR_UI_Facet
	 */
	public function get_ui() {
		return new WPSOLR_UI_Facet();
	}

}