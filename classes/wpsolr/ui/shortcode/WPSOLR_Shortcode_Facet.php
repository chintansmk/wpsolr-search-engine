<?php

namespace wpsolr\ui\shortcode;

use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
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
		$this->shortcode_name = 'wpsolr_shortcode_facet';
		$this->layout_type    = WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET_GROUP;
	}


	/**
	 * Returns the UI object
	 *
	 * @return WPSOLR_UI_Facet
	 */
	protected function get_ui() {
		return new WPSOLR_UI_Facet();
	}

}