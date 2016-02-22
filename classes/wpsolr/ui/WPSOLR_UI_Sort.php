<?php

namespace wpsolr\ui;

use wpsolr\extensions\components\WPSOLR_Options_Components;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Display Sort List
 *
 */
class WPSOLR_UI_Sort extends WPSOLR_UI {

	/**
	 * @inheritDoc
	 */
	public function __construct() {

		$this->component_type = WPSOLR_Options_Components::COMPONENT_TYPE_SORTS;
		$this->layout_type    = WPSOLR_Options_Layouts::TYPE_LAYOUT_SORT_GROUP;
	}

	public function create_twig_parameters( $localization_options ) {

		return array(
			'sort_header' => ! $this->is_show_title_on_front_end ? '' : ( ! empty( $this->title ) ? $this->title : WPSOLR_Localization::get_term( $localization_options, 'sort_header' ) ),
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

	/**
	 * @inheritDoc
	 */
	public function get_groups() {
		return WPSOLR_Global::getOption()->get_sorts_groups();
	}

}
