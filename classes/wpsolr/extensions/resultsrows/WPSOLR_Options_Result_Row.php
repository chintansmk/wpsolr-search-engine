<?php

namespace wpsolr\extensions\resultsrows;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_Result_Row
 *
 * Manage Results rows
 */
class WPSOLR_Options_Result_Row extends WPSOLR_Extensions {

	// Group name in error messages
	const GROUP_NAME = 'Results rows';

	const FORM_FIELD_IS_INFINITESCROLL = 'infinitescroll';

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

		$new_group_uuid = WPSOLR_Global::getExtensionIndexes()->generate_uuid();

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'        => $this->get_groups(),
					'layouts'        => WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( WPSOLR_Options_Layouts::TYPE_LAYOUT_RESULT_ROW ),
					'new_group_uuid' => $new_group_uuid,
					'groups'         => array_merge(
						$this->clone_some_groups(),
						[
							$new_group_uuid => [
								'name' => 'New results rows'
							]
						] ),
				],
				$plugin_parameters
			)
		);
	}

	public function get_groups() {

		$groups = WPSOLR_Global::getOption()->get_option_results_rows();

		return $groups;
	}

	/**
	 * Get name of a result
	 *
	 * @param $result
	 *
	 * @return string Result name
	 */
	public function get_result_name( $result ) {
		return isset( $result[ self::RESULT_FIELD_NAME ] ) ? $result[ self::RESULT_FIELD_NAME ] : '';
	}

	/**
	 * Get label of a result
	 *
	 * @param $result
	 *
	 * @return string Result label
	 */
	public function get_result_label( $result ) {
		return isset( $result[ self::RESULT_FIELD_LABEL ] ) ? $result[ self::RESULT_FIELD_LABEL ] : '';
	}

}