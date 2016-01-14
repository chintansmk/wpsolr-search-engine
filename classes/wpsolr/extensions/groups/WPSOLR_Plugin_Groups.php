<?php

namespace wpsolr\extensions\groups;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;
use wpsolr\WPSOLR_Filters;

/**
 * Manage authorizations for groups plugin
 * @link https://wordpress.org/plugins/groups/
 * @link http://api.itthinx.com/groups/package-groups.html
 */
class WPSOLR_Plugin_Groups extends WPSOLR_Extensions {

	const CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES = 'groups-groups_read_post_str';
	const DEFAULT_MESSAGE_NOT_AUTHORIZED = 'Sorry, your profile is not associated whith any group, therefore you are not allowed to see any results.
<br/>Please contact your administrator.';

	// [capability, group]
	private $_user_capabilities_groups;

	/**
	 * After constructor.
	 */
	protected function post_constructor() {

		/**
		 * Filters/Actions
		 */
		WPSOLR_Service_Wordpress::add_action( WPSOLR_Filters::WPSOLR_ACTION_SOLARIUM_QUERY, array(
			$this,
			'set_custom_query'
		), 10, 1 );

		WPSOLR_Service_Wordpress::add_filter( WPSOLR_Filters::WPSOLR_FILTER_SOLR_RESULTS_DOCUMENT_GROUPS_INFOS, array(
			$this,
			'get_groups_of_user_document'
		), 10, 2 );

	}

	/**
	 * Display admin form.
	 *
	 * @param array $plugin_parameters Parameters
	 */
	public function output_form( $form_file = null, $plugin_parameters = [ ] ) {

		self::update_custom_field_capabilities( WPSOLR_Plugin_Groups::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES );

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'                             => WPSOLR_Global::getOption()->get_option_plugin_groups(
						[
							WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE                            => '0',
							WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_USERS_WITHOUT_GROUPS_SEE_ALL_RESULTS => '0',
							'is_result_without_capabilities_seen_by_all_users'                          => '0',
							'message_user_without_groups_shown_no_results'                              => '',
							'message_result_capability_matches_user_group'                              => ''
						]
					),
					'is_plugin_custom_field_for_indexing' => self::get_custom_field_capabilities( self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES ),
					'custom_field_for_indexing_name'      => self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES
				],
				$plugin_parameters
			)
		);
	}

	/**
	 *
	 * Add user's capabilities filters to the Solr query.
	 *
	 * @param $parameters array
	 *
	 * @throws Exception
	 */
	public function set_custom_query( $parameters ) {

		$query = $parameters[ WPSOLR_Filters::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SOLARIUM_QUERY ];
		$user  = $parameters[ WPSOLR_Filters::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SEARCH_USER ];

		if ( ! $user ) {
			return;
		}

		$is_users_without_groups_see_all_results          = WPSOLR_Global::getOption()->get_plugin_groups_is_users_without_groups_see_all_results();
		$is_result_without_capabilities_seen_by_all_users = WPSOLR_Global::getOption()->get_plugin_groups_is_result_without_capabilities_seen_by_all_users();

		// Get custom fields selected for indexing
		$array_custom_fields     = WPSOLR_Global::getOption()->get_indexing_custom_fields_array();

		// Is the custom field used by Groups plugin to store posts capabilities indexed ?
		if ( false !== array_search( self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES, $array_custom_fields ) ) {

			$user_capability_and_group_array = $this->get_user_capabilities_and_groups( $user->ID );

			if ( ( count( $user_capability_and_group_array ) == 0 ) && ! $is_users_without_groups_see_all_results ) {

				// No activities for current user, and setup forbid display of any content: not allowed to see any content. Stop here.
				throw new Exception( WPSOLR_Global::getOption()->get_plugin_groups_message_user_without_groups_shown_no_results( self::DEFAULT_MESSAGE_NOT_AUTHORIZED ) );
			}

			if ( ( count( $user_capability_and_group_array ) == 0 ) && $is_users_without_groups_see_all_results ) {

				// No activities for current user, and setup authorize display of any content. Stop here.
				return;
			}

			if ( count( $user_capability_and_group_array ) > 0 ) {

				$filter_query_str = '';
				foreach ( $user_capability_and_group_array as $user_capability_and_group ) {
					// Add capability to query field
					$filter_query_str .= ( $filter_query_str === '' ? '' : self::_SOLR_OR_OPERATOR );
					$filter_query_str .= '( ' . self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES . ':' . $user_capability_and_group['capability'] . ' )';
				}

				if ( ( $filter_query_str !== '' ) && $is_result_without_capabilities_seen_by_all_users ) {
					// Authorize documents without capabilities, or with empty capabilities, to be retrieved.
					$filter_query_no_capabilities_str = '';
					//$filter_query_no_capabilities_str .= '( ' . ' *:* -' . self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES . ' )'; // no capability
					//$filter_query_no_capabilities_str .= self::_SOLR_OR_OPERATOR;
					$filter_query_no_capabilities_str .= '( ' . ' *:* -' . self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES . ':' . '*' . ' )'; // capability empty
					$filter_query_no_capabilities_str = '( ' . $filter_query_no_capabilities_str . ' )';

					$filter_query_str .= self::_SOLR_OR_OPERATOR . ' ' . $filter_query_no_capabilities_str;
				}


				$filter_query_str = '( ' . $filter_query_str . ' )';

				// Add query fields (qf)
				$filterQuery = $query->createFilterQuery();
				$filterQuery->setKey( 'filter_query_or_user_capabilities' )
				            ->setQuery( $filter_query_str );
				$query->addFilterQuery( $filterQuery );

			}
		}

	}

	/**
	 * Get all the capabilities that the user has (only user defined).
	 *
	 * @param $user
	 *
	 * @return array array of capabilities and groups
	 */
	public function get_user_capabilities_and_groups( $user_id ) {

		if ( isset( $this->_user_capabilities_groups ) ) {
			// return value in cache
			return $this->_user_capabilities_groups;
		}

		$user_capabilities = array();

		// Fetch current user's groups
		$groups_user = new \Groups_User( $user_id );

		$groups = $groups_user->__get( \Groups_User::CACHE_GROUP );

		if ( ! isset( $groups ) ) {
			return null;
		}

		foreach ( $groups as $group ) {

			if ( ! isset( $group ) ) {
				continue;
			}

			// Fetch capabilities of current user's groups
			$capabilities = $group->__get( \Groups_User::CAPABILITIES );

			if ( ! isset( $capabilities ) ) {
				continue;
			}

			foreach ( $capabilities as $capability ) {

				if ( isset( $capability ) && isset( $capability->capability ) ) {

					$user_capabilities[] = array(
						'capability' => $capability->capability->capability,
						'group'      => $group->name
					);

				}
			}

		}

		// Store in cache: this value is used for all documents returned by a Solr query
		$this->_user_capabilities_groups = $user_capabilities;

		return $this->_user_capabilities_groups;
	}

	/**
	 * Get all the capabilities that the user has, including those that are inherited (not user defined).
	 * Ex: add_users, delete_posts, upload_files
	 *
	 * @param $user
	 *
	 * @return array Array of string capabilities
	 */
	public function get_user_deep_capabilities( $user_id ) {

		// Fetch current user's groups
		$groups_user = new \Groups_User( $user_id );

		return $groups_user->capabilities_deep;

	}

	/**
	 * Get groups of user containing at least one capability of document
	 *
	 * @param $user_id
	 * @param $document
	 *
	 * return array[string] Array of groups
	 */
	public function get_groups_of_user_document( $user_id, $document ) {

		$wpsolr_groups_array = array();

		$document_capabilities_array = $document[ self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES ];
		if ( is_array( $document_capabilities_array ) && ( count( $document_capabilities_array ) > 0 ) ) {

			// Calculate groups of this user which owns at least one the document capability
			$user_capabilities_groups = $this->get_user_capabilities_and_groups( $user_id );

			if ( is_array( $user_capabilities_groups ) && ( count( $user_capabilities_groups ) > 0 ) ) {

				foreach ( $user_capabilities_groups as $user_capabilities_group ) {

					// Add group if its capability is in capabilities
					if ( ! ( array_search( $user_capabilities_group['capability'], $document_capabilities_array ) === false ) ) {

						// Use associative to prevent duplicates
						$wpsolr_groups_array[ $user_capabilities_group['group'] ] = $user_capabilities_group['group'];

					}
				}
			}
		}

		// Message to display on every line
		$message = WPSOLR_Global::getOption()->get_plugin_groups_message_result_capability_matches_user_group();
		$message = str_replace( '%1', implode( ',', $wpsolr_groups_array ), $message );

		// Get values from associative
		$wpsolr_groups_array = array_values( $wpsolr_groups_array );

		return array( 'groups' => $wpsolr_groups_array, 'message' => $message );

	}

}