<?php

namespace wpsolr\extensions\importexport;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\solr\WPSOLR_ImportExport_Type;
use wpsolr\solr\WPSOLR_ImportExport_Types;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_ImportExport
 *
 * Import/Export options from/into files
 */
class WPSOLR_Options_ImportExports extends WPSOLR_Extensions {

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
					'options' => WPSOLR_Global::getOption()->get_option_importexports()
				],
				$plugin_parameters
			)
		);
	}


}