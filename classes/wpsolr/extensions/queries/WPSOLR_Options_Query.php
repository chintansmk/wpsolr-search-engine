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
	const FORM_FIELD_QUERY_FILTER = 'query_filter';
	const FORM_FIELD_DEFAULT_MAX_NB_RESULTS_BY_PAGE = 20;
	const FORM_FIELD_MAX_NB_RESULTS_BY_PAGE = 'no_res';
	const FORM_FIELD_HIGHLIGHTING_FRAGSIZE = 'highlighting_fragsize';
	const FORM_FIELD_DEFAULT_HIGHLIGHTING_FRAGSIZE = 100;
	// Solr operators
	const QUERY_OPERATOR_AND = 'AND';
	const QUERY_OPERATOR_OR = 'OR';
	// Timeout in seconds when calling Solr
	const FORM_FIELD_DEFAULT_SOLR_TIMEOUT_IN_SECOND = 30;
	const FORM_FIELD_DEFAULT_OPERATOR = 'query_default_operator';
	const FORM_FIELD_IS_QUERY_PARTIAL_MATCH_BEGIN_WITH = 'is_query_partial_match_begin_with';
	const FORM_FIELD_IS_DEFAULT = 'is_default';
	const FORM_FIELD_IS_MULTI_LANGUAGE = 'is_multi_language';

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
					'fields'         => WPSOLR_Global::getExtensionSchemas()->get_groups(),
				],
				$plugin_parameters
			)
		);
	}

	/**
	 * Get group
	 *
	 * @@param string $group_id
	 * @return array Groups
	 */
	public function get_group( $group_id ) {

		$groups = WPSOLR_Global::getOption()->get_option_queries();

		if ( empty( $groups ) || empty( $groups[ $group_id ] ) ) {
			throw new WPSOLR_Exception( sprintf( 'Query \'%s\' is unknown.', $group_id ) );
		}

		return $groups[ $group_id ];
	}

	/**
	 * Get query filter
	 *
	 * @param $query
	 *
	 * @return string Query filter
	 */
	public function get_query_filter( $query ) {
		return isset( $query[ self::FORM_FIELD_QUERY_FILTER ] ) ? $query[ self::FORM_FIELD_QUERY_FILTER ] : '';
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