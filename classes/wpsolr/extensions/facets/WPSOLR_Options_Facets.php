<?php

namespace wpsolr\extensions\facets;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;

/**
 * Class WPSOLR_Options_Facets
 *
 * Manage Facets
 */
class WPSOLR_Options_Facets extends WPSOLR_Extensions {


	/**
	 * Post constructor.
	 */
	protected function post_constructor() {

	}

	/**
	 * Display admin form.
	 *
	 * @param array $plugin_parameters Parameters
	 */
	public function output_form( $form_file = null, $plugin_parameters = [ ] ) {

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'           => WPSOLR_Global::getOption()->get_option_facet(
						[ WPSOLR_Option::OPTION_FACETS_SELECTED => '' ]
					),
					'facets_selected_array'   => WPSOLR_Global::getOption()->get_facets_selected_array(),
					'facets_selected'   => WPSOLR_Global::getOption()->get_facets_selected(),
					'facets_candidates' => array_merge(
						array( 'Type', 'Author', 'Categories', 'Tags' ),
						WPSOLR_Global::getOption()->get_indexing_custom_fields_array(),
						WPSOLR_Global::getOption()->get_indexing_taxonomies_array()
					),
					'image_plus'        => plugins_url( '../../../../images/plus.png', __FILE__ ),
					'image_minus'       => plugins_url( '../../../../images/success.png', __FILE__ )
				],
				$plugin_parameters
			)
		);
	}

}