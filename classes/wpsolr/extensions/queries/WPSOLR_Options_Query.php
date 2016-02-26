<?php

namespace wpsolr\extensions\queries;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_Query
 *
 * Manage queries
 */
class WPSOLR_Options_Query extends WPSOLR_Extensions {

	const FORM_FIELD_NAME = 'name';
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
		$groups = WPSOLR_Global::getOption()->get_option_queries();
		$groups = $this->clone_some_groups( $groups );

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'        => WPSOLR_Global::getOption()->get_option_queries(),
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
	 * Get elements of a group
	 *
	 * @param string $group_id Group id
	 *
	 * @return array Elements of the group
	 */
	public function get_group_elements( $group_id ) {

		$groups = WPSOLR_Global::getOption()->get_option_queries();

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

		$groups = WPSOLR_Global::getOption()->get_option_queries();

		if ( ! empty( $group_id ) && ! empty( $groups ) && ! empty( $groups[ $group_id ] ) ) {
			return $groups[ $group_id ];
		}

		return [ ];
	}

	/**
	 * Get name
	 *
	 * @param $result
	 *
	 * @return string Result name
	 */
	public function get_result_name( $result ) {
		return isset( $result[ self::RESULT_FIELD_NAME ] ) ? $result[ self::RESULT_FIELD_NAME ] : '';
	}

	/**
	 * Get label
	 *
	 * @param $result
	 *
	 * @return string Result label
	 */
	public function get_result_label( $result ) {
		return isset( $result[ self::RESULT_FIELD_LABEL ] ) ? $result[ self::RESULT_FIELD_LABEL ] : '';
	}

	/**
	 * Clone the groups marked.
	 *
	 * @param $groups
	 */
	public function clone_some_groups( &$groups ) {

		foreach ( $groups as $group_uuid => &$group ) {

			if ( ! empty( $group['is_to_be_cloned'] ) ) {

				unset( $group['is_to_be_cloned'] );

				// Clone the group
				$clone              = $group;
				$result_cloned_uuid = WPSOLR_Global::getExtensionIndexes()->generate_uuid();
				$clone['name']      = 'Clone of ' . $clone['name'];

				$groups[ $result_cloned_uuid ] = $clone;
			}
		}

		return $groups;
	}

}