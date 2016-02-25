<?php

namespace wpsolr\extensions\resultsheaders;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_Result_Header
 *
 * Manage Results headers
 */
class WPSOLR_Options_Result_Header extends WPSOLR_Extensions {

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

		// Clone some groups
		$groups = WPSOLR_Global::getOption()->get_option_results_rows();
		$groups = $this->clone_some_groups( $groups );

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'        => WPSOLR_Global::getOption()->get_option_results_rows(),
					'layouts'        => WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( WPSOLR_Options_Layouts::TYPE_LAYOUT_RESULT_ROW ),
					'new_group_uuid' => $new_group_uuid,
					'groups'         => array_merge(
						$groups,
						[
							$new_group_uuid => [
								'name' => 'New group'
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

	/**
	 * Get group
	 *
	 * @@param string $group_id
	 * @return array Groups
	 */
	public function get_group( $group_id ) {

		$groups = WPSOLR_Global::getOption()->get_option_results_rows();

		if ( ! empty( $group_id ) && ! empty( $groups ) && ! empty( $groups[ $group_id ] ) ) {
			return $groups[ $group_id ];
		}

		return [ ];
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
	 * Format a string translation
	 *
	 * @param $name
	 * @param $text
	 * @param $domain
	 * @param $is_multiligne
	 *
	 * @return array
	 */
	protected function get_string_to_translate( $name, $text, $domain, $is_multiligne ) {

		return [
			'name'          => $name,
			'text'          => $text,
			'domain'        => $domain,
			'is_multiligne' => $is_multiligne
		];
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

	/**
	 * Clone the groups marked.
	 *
	 * @param $groups_results
	 */
	public function clone_some_groups( &$groups_results ) {

		foreach ( $groups_results as $group_uuid => &$result ) {

			if ( ! empty( $result['is_to_be_cloned'] ) ) {

				unset( $result['is_to_be_cloned'] );

				// Clone the group
				$result_cloned         = $result;
				$result_cloned_uuid    = WPSOLR_Global::getExtensionIndexes()->generate_uuid();
				$result_cloned['name'] = 'Clone of ' . $result_cloned['name'];

				$groups_results[ $result_cloned_uuid ] = $result_cloned;
			}
		}

		return $groups_results;
	}

}