<?php

namespace wpsolr\extensions\facets;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\solr\WPSOLR_Field_Types;
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

		$new_facets_group_uuid = WPSOLR_Global::getExtensionIndexes()->generate_uuid();

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'                    => WPSOLR_Global::getOption()->get_option_facet(
						[ WPSOLR_Option::OPTION_FACETS_FACETS => '' ]
					),
					'layouts'                    => [
						'1' => [ 'name' => 'Layout 1' ],
						'2' => [ 'name' => 'Layout 2' ]
					],
					'selected_facets_group_uuid' => '2',
					'new_facets_group_uuid'      => $new_facets_group_uuid,
					'facets_groups'              => array_merge(
						WPSOLR_Global::getOption()->get_facets_groups(),
						[
							$new_facets_group_uuid => [
								'name' => 'New group'
							]
						] ),
					'facets_selected'            => WPSOLR_Global::getOption()->get_facets_selected_array(),
					'facets_candidates'          => array_merge(
						WPSOLR_Field_Types::get_fields_type_string( [ 'Type', 'Author', 'Categories', 'Tags' ] ),
						WPSOLR_Global::getOption()->get_fields_custom_fields_array(),
						WPSOLR_Field_Types::get_fields_type_string( WPSOLR_Global::getOption()->get_fields_taxonomies_array() )
					),
					'image_plus'                 => plugins_url( '../../../../images/plus.png', __FILE__ ),
					'image_minus'                => plugins_url( '../../../../images/success.png', __FILE__ )
				],
				$plugin_parameters
			)
		);
	}

}