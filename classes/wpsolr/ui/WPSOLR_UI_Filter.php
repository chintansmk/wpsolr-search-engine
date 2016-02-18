<?php

namespace wpsolr\ui;

use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Display facets
 *
 * Class WPSOLR_UI_Facet
 */
class WPSOLR_UI_Filter extends WPSOLR_UI_Facet {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$this->layout_type = WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET_FILTER_GROUP;
	}

	/**
	 * @inheritDoc
	 */
	protected function extract_data() {

		return WPSOLR_Data_Filter::extract_data( $this->group_id );
	}

	/**
	 * @inheritDoc
	 */
	public function get_groups() {
		return WPSOLR_Global::getOption()->get_facets_groups();
	}

}
