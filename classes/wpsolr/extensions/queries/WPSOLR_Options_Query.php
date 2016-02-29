<?php

namespace wpsolr\extensions\queries;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_Query
 *
 * Manage queries
 */
class WPSOLR_Options_Query extends WPSOLR_Extensions {

	// Group name in error messages
	const GROUP_NAME = 'Query';

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

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$this->get_groups_template_file(),
			array_merge(
				[
					'group_parameters' => [
						'extension_name' => WPSOLR_Extensions::OPTION_QUERIES,
						'extra_classes'  => 'wpsolr_2col',
						'options'        => $this->get_groups(),
						'new_group_uuid' => $new_group_uuid,
						'groups'         => array_merge(
							$this->clone_some_groups(),
							[
								$new_group_uuid => [
									'name' => 'New query'
								]
							] ),
						'fields'         => WPSOLR_Global::getExtensionSchemas()->get_groups(),
					]
				],
				$plugin_parameters
			)
		);
	}

	public function get_groups() {

		$groups = WPSOLR_Global::getOption()->get_option_queries();

		return $groups;
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

}