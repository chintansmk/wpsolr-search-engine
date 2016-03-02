<?php

namespace wpsolr\extensions\searchform;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_Search_Form
 *
 * Manage Results rows
 */
class WPSOLR_Options_Search_Form extends WPSOLR_Extensions {

	// Group name in error messages
	const GROUP_NAME = 'Search form';

	const FORM_FIELD_IS_INFINITESCROLL = 'infinitescroll';
	const FORM_FIELD_MAX_NB_RESULTS_BY_PAGE = 'no_res';
	const FORM_FIELD_HIGHLIGHTING_FRAGSIZE = 'highlighting_fragsize';
	const FORM_FIELD_DEFAULT_MAX_NB_RESULTS_BY_PAGE = 20;
	const FORM_FIELD_DEFAULT_HIGHLIGHTING_FRAGSIZE = 100;

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
								'name' => 'New search form'
							]
						] ),
				],
				$plugin_parameters
			)
		);
	}


	/**
	 * Get results of a group
	 *
	 * @param string $group_id Group of results
	 *
	 * @return array Results of the group
	 */
	public function get_results_from_group( $group_id ) {

		$groups = WPSOLR_Global::getOption()->get_option_results_rows();

		if ( ! isset( $groups[ $group_id ] ) ) {
			throw new WPSOLR_Exception( sprintf( 'group \'%s\' is unknown.', $group_id ) );
		}

		return ! empty( $groups[ $group_id ] ) ? $groups[ $group_id ] : [ ];
	}

	public function get_groups() {

		$groups = WPSOLR_Global::getOption()->get_option_search_forms();

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