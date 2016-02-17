<?php

namespace wpsolr\ui;

use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\localization\WPSOLR_Localization;

/**
 * Display facets
 *
 * Class WPSOLR_UI_Facet
 */
class WPSOLR_UI_Facet extends WPSOLR_UI {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		$this->layout_type = WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET_GROUP;
	}

	public function create_twig_parameters( $localization_options ) {

		return array(
			'facets_header'              => ! empty( $this->title ) ? $this->title : WPSOLR_Localization::get_term( $localization_options, 'facets_header' ),
			'facets_title'               => WPSOLR_Localization::get_term( $localization_options, 'facets_title' ),
			'facets_element'             => WPSOLR_Localization::get_term( $localization_options, 'facets_element' ),
			'facets_element_all_results' => WPSOLR_Localization::get_term( $localization_options, 'facets_element_all_results' ),
			'facets'                     => $this->data['data']
		);

	}

	/**
	 * @inheritDoc
	 */
	protected function is_data_empty() {

		return ( ! isset( $this->data ) || empty( $this->data['data'] ) || ( count( $this->data['data'][ WPSOLR_Options_Facets::OPTION_FACETS ] ) == 0 ) );
	}

	/**
	 * @inheritDoc
	 */
	protected function extract_data() {

		return WPSOLR_Data_Facets::extract_data( $this->group_id );
	}

}
