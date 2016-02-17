<?php

namespace wpsolr\ui;

use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\localization\WPSOLR_Localization;

/**
 * Display Sort List
 *
 */
class WPSOLR_UI_Sort extends WPSOLR_UI {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$this->layout_type = WPSOLR_Options_Layouts::TYPE_LAYOUT_SORT_GROUP;
	}

	public function create_twig_parameters( $localization_options ) {

		return array(
			'sort_header' => ! empty( $this->title ) ? $this->title : WPSOLR_Localization::get_term( $localization_options, 'sort_header' ),
			'sort_list'   => $this->data['data']
		);

	}

	/**
	 * @inheritDoc
	 */
	protected function is_data_empty() {

		return false;;
	}

	/**
	 * @inheritDoc
	 */
	protected function extract_data() {

		return WPSOLR_Data_Sort::extract_data( $this->group_id );
	}

}
