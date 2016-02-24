<?php

namespace wpsolr\ui;

use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;

/**
 * Filters data
 *
 * Class WPSOLR_Data_Filter
 */
class WPSOLR_Data_Filter extends WPSOLR_Data_Facets {

	/**
	 * @inheritDoc
	 */
	protected static function get_layout_type() {
		return WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET_FILTER;
	}

	/**
	 * @inheritDoc
	 */
	protected static function discard_unselected_items() {
		return true;
	}

	protected static function get_layout_field_name() {
		return WPSOLR_Options_Facets::FACET_FIELD_FILTER_LAYOUT_ID;
	}

}
