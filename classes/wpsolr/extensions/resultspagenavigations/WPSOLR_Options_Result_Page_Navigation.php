<?php

namespace wpsolr\extensions\resultspagenavigations;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_Result_Page_Navigation
 *
 * Manage Results rows
 */
class WPSOLR_Options_Result_Page_Navigation extends WPSOLR_Extensions {

	// Group name in error messages
	const GROUP_NAME = 'Page navigation';

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
								'name' => 'New page navigation'
							]
						] ),
				],
				$plugin_parameters
			)
		);
	}

	public function get_groups() {

		$groups = WPSOLR_Global::getOption()->get_option_results_page_navigations();

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

	/**
	 * Get the strings to translate among the selected facets data
	 * @return array
	 */
	public function get_strings_to_translate() {

		$results = [ ];
		$domain  = 'wpsolr results'; // never change this

		// Fields that can be translated and their definition
		$fields_translatable = [
			self::RESULT_FIELD_LABEL => [ 'name' => 'Result Label', 'is_multiline' => false ]
		];

		$groups = WPSOLR_Global::getOption()->get_results_selected_array();

		foreach ( $groups as $group_name => $group ) {

			foreach ( $group as $field ) {

				foreach ( $fields_translatable as $translatable_name => $translatable ) {

					if ( ! empty( $field[ $translatable_name ] ) ) {

						$results[] = $this->get_string_to_translate(
							$field[ $translatable_name ], //sprintf( '%s of %s %s', $translatable['name'], $this->get_facets_group( $facets_group_name )['name'], $facet_field['name'] ),
							$field[ $translatable_name ],
							$domain,
							$translatable['is_multiline']
						);
					}

				}
			}
		}

		return $results;
	}

}