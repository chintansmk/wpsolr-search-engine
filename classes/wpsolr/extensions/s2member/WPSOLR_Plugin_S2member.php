<?php

namespace wpsolr\extensions\s2member;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;
use wpsolr\WPSOLR_Filters;

/**
 * Class WpSolrS2Member
 *
 * Manage authorizations for s2member plugin
 * @link https://wordpress.org/plugins/s2member/
 * @link http://www.s2member.com/
 */
class WPSOLR_Plugin_S2member extends WPSOLR_Extensions {

	const CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES = 's2member_ccaps_req_str';
	const CUSTOM_FIELD_NAME_STORING_POST_LEVEL = 's2member_level_req';
	const DEFAULT_MESSAGE_NOT_AUTHORIZED = 'Sorry, your profile is not associated whith any level/custom capabilities, therefore you are not allowed to see any results.
<br/>Please contact your administrator.';

	// s2Member's prefix for roles and capabilities
	const PREFIX_S2MEMBER_ROLE_OR_CAPABILITY = 'access_s2member_';
	const PREFIX_S2MEMBER_ROLE = 'level';
	const PREFIX_S2MEMBER_CAPABILITY = 'ccap_';

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

		WPSOLR_Service_Wordpress::add_filter( WPSOLR_Filters::WPSOLR_FILTER_POST_CUSTOM_FIELDS, array(
			$this,
			'filter_custom_fields'
		), 10, 2 );

	}

	/**
	 * Display admin form.
	 *
	 * @param array $plugin_parameters Parameters
	 */
	public function output_form( $form_file = null, $plugin_parameters = [ ] ) {

		self::update_custom_field_capabilities( self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES );

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'                             => WPSOLR_Global::getOption()->get_option_plugin_s2member(
						[
							WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE     => '0',
							'is_users_without_capabilities_see_all_results'      => '0',
							'is_result_without_capabilities_seen_by_all_users'   => '0',
							'message_user_without_capabilities_shown_no_results' => '',
							'message_result_capability_matches_user_group'       => ''
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
	 * Add levels and capabilities filters to the Solr query.
	 *
	 * We will use the user's levels and capabilities as filters
	 * Every post has been indexed with it's level and capabilities
	 * We will authorize a user to see a post if:
	 * - the post is indexed with the same level as the user's levels, or a level below
	 * and (one of user's levels >= post level)
	 * - the post is indexed with at least on capability similar to the user's capabilities
	 *
	 * Examples:
	 * Post is level1, User is level0 => post is not retrieved from Solr
	 * Post is level1, User is level1 => post is retrieved from Solr
	 * Post is level1, User is level2 => post is retrieved from Solr
	 * Post as capability1, User has level0, no capability => post is not retrieved from Solr
	 * Post as capability1, User has level0, capability1 => post is retrieved from Solr
	 * Post as capability1, User has level0, capability1 => post is retrieved from Solr
	 * Post as level1, capability1, User has level0, capability1 => post is not retrieved from Solr
	 * Post as level1, capability1, User has level1, capability2 => post is not retrieved from Solr
	 * Post as level1, capability1, User has level2, capability1 and capability2 => post is retrieved from Solr
	 * Post as level1, capability1, User has level0, capability1 and capability2 => post is not retrieved from Solr
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

		$is_users_without_capabilities_see_all_results    = WPSOLR_Global::getOption()->get_plugin_s2member_is_users_without_capabilities_see_all_results();
		$is_result_without_capabilities_seen_by_all_users = WPSOLR_Global::getOption()->get_plugin_s2member_is_result_without_capabilities_seen_by_all_users();

		// Get custom fields selected for indexing
		$array_cust_fields = WPSOLR_Global::getOption()->get_indexing_custom_fields_array();

		// Is the custom field used by s2Member plugin to store posts capabilities indexed ?
		if ( false !== array_search( self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES, $array_cust_fields ) ) {

			// Find s2member roles (levels) and capabilities (custom custom) in user's roles capabilities
			$s2member_levels_or_custom_capabilities = array();
			foreach ( $user->get_role_caps() as $key => $value ) {
				if ( substr_compare( $key, self::PREFIX_S2MEMBER_ROLE_OR_CAPABILITY, 0, strlen( self::PREFIX_S2MEMBER_ROLE_OR_CAPABILITY ) ) === 0 ) {
					$s2member_levels_or_custom_capabilities[] = $key;
				}
			}

			if ( ( count( $s2member_levels_or_custom_capabilities ) == 0 ) && ! $is_users_without_capabilities_see_all_results ) {

				// No activities for current user, and setup forbid display of any content: not allowed to see any content. Stop here.
				throw new Exception( WPSOLR_Global::getOption()->get_plugin_s2member_message_user_without_capabilities_shown_no_results( self::DEFAULT_MESSAGE_NOT_AUTHORIZED ) );
			}

			if ( ( count( $s2member_levels_or_custom_capabilities ) == 0 ) && $is_users_without_capabilities_see_all_results ) {

				// No activities for current user, and setup authorize display of any content. Stop here.
				return;
			}

			if ( count( $s2member_levels_or_custom_capabilities ) > 0 ) {

				$filter_query_str              = '';
				$filter_query_levels_str       = '';
				$filter_query_capabilities_str = '';
				$level_int                     = - 1;
				foreach ( $s2member_levels_or_custom_capabilities as $s2member_level_or_custom_capability ) {

					// Remove prefix
					$s2member_level_or_custom_capability = str_replace( self::PREFIX_S2MEMBER_ROLE_OR_CAPABILITY . self::PREFIX_S2MEMBER_CAPABILITY, '', $s2member_level_or_custom_capability );
					$s2member_level_or_custom_capability = str_replace( self::PREFIX_S2MEMBER_ROLE_OR_CAPABILITY . self::PREFIX_S2MEMBER_ROLE, '', $s2member_level_or_custom_capability );

					if ( preg_match( '/\d+$/', $s2member_level_or_custom_capability ) ) {
						// User's levels, as 'level0', 'level10'
						// Get max of user's levels, as an integer

						$level_int = max( $level_int, intval( $s2member_level_or_custom_capability ) );

					} else {
						// Others: custom capabilities
						$filter_query_capabilities_str .= ( $filter_query_capabilities_str === '' ? '' : self::_SOLR_OR_OPERATOR );
						$filter_query_capabilities_str .= '( ' . self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES . ':' . $s2member_level_or_custom_capability . ' )';
					}
				}

				if ( $level_int >= 0 ) {
					// User's levels, as 'level0', 'level10'
					// If user's level is n, it can see posts with level 0..n => OR (0..n)

					$level_int = max( $level_int, intval( $s2member_level_or_custom_capability ) );

					for ( $i = 0; $i <= $level_int; $i ++ ) {
						$filter_query_levels_str .= ( $filter_query_levels_str === '' ? '' : self::_SOLR_OR_OPERATOR );
						$filter_query_levels_str .= '( ' . self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES . ':' . $i . ' )';
					}

				}

				if ( $filter_query_levels_str !== '' ) {
					$filter_query_str .= '(' . $filter_query_levels_str . ')';
				}
				if ( $filter_query_capabilities_str !== '' ) {
					$filter_query_str .= ( $filter_query_str === '' ? '' : self::_SOLR_AND_OPERATOR );
					$filter_query_str .= '(' . $filter_query_capabilities_str . ')';
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
	 * Filter custom field self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES of a document
	 *
	 * s2Member serialize capabilities of the post
	 * ex: a:2:{i:0;s:25:"capability1";i:1;s:25:"capability2";}
	 *
	 * We must unserialize it
	 * ex: ['capability1', 'capability2']
	 *
	 * @param $custom_fields array Serialized array of capabilities
	 *
	 * return array Custom fields with unserialized array of capabilities in self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES
	 */
	public function filter_custom_fields( $custom_fields, $post_id ) {

		// Remove the '_str' at the end of the custom field
		$custom_field_name = str_replace( '_str', '', self::CUSTOM_FIELD_NAME_STORING_POST_CAPABILITIES );

		if ( $custom_fields && ( count( $custom_fields ) > 0 ) && isset( $custom_fields[ $custom_field_name ] ) ) {

			$serialized_custom_field_array = $custom_fields[ $custom_field_name ];
			if ( $serialized_custom_field_array ) {
				// Field is serialiezd by s2Member; unserialize it before indexing

				$custom_fields[ $custom_field_name ] = unserialize( $serialized_custom_field_array[0] );
			}
		}


		/*
			is_protected_by_s2member returns, after debugging:
		- false
		- or [s2member_level_req => i] is the level i (0, 1, 2, 3, 4) is set on the post
		- or [s2member_ccap_req => capability1] if capability1 is the first capability on the post
		Very different from what is described in the documentation !!!
		*/

		// levels used as filters too
		$protections = is_protected_by_s2member( $post_id );
		$level       = null;
		if ( is_array( $protections ) ) {
			$level = $protections[ self::CUSTOM_FIELD_NAME_STORING_POST_LEVEL ];

			if ( $level ) { // level is an integer >= 0
				// Add level to custom fields, as it should have been done
				$custom_fields[ $custom_field_name ][] = $level;
			}

		}


		return $custom_fields;
	}

}