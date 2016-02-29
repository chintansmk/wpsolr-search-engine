<?php

namespace wpsolr\extensions\schemas;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\woocommerce\WPSOLR_Plugin_Woocommerce;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\solr\WPSOLR_Field_Type;
use wpsolr\solr\WPSOLR_Field_Types;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_Schemas
 *
 * Manage queries
 */
class WPSOLR_Options_Schemas extends WPSOLR_Extensions {

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
	const OPTION_FIELDS_ARE_POST_EXCERPTS_INDEXED = 'p_excerpt';
	const OPTION_FIELDS_EXCLUDE_IDS = 'exclude_ids';
	const OPTION_FIELDS_POST_TYPES = 'p_types';
	const OPTION_FIELDS_ARE_COMMENTS_INDEXED = 'comments';
	const FORM_FIELD_TAXONOMIES = 'taxonomies';
	const OPTION_FIELDS_CUSTOM_FIELDS = self::FORM_FIELD_CUSTOM_FIELDS;
	const OPTION_FIELDS_IS_SHORTCODE_EXPANDED = 'is_shortcode_expanded';
	const OPTION_FIELDS_ATTACHMENTS = 'attachment_types';
	const FORM_FIELD_IS_INDEX_COMMENTS = 'is_index_comments';
	const OPTION_FIELDS_SOLR_INDEXES = 'solr_indexes';
	const FORM_FIELD_FIELD_ID = 'field_id';
	const FORM_FIELD_CUSTOM_FIELDS = 'custom_fields';
	const FORM_FIELD_SOLR_TYPE = 'solr_type';
	const FORM_FIELD_SOLR_INDEX_ID = 'solr_index_id';

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
		$groups = WPSOLR_Global::getOption()->get_option_fields();
		$groups = $this->clone_some_groups( $groups );

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'                   => WPSOLR_Global::getOption()->get_option_fields(),
					'new_group_uuid'            => $new_group_uuid,
					'groups'                    => array_merge(
						$groups,
						[
							$new_group_uuid => [
								'name' => 'New group'
							]
						] ),
					'solr_field_types'          => WPSOLR_Global::getSolrFieldTypes()->get_field_types(),
					'indexable_post_types'      => $this->get_indexable_post_types(),
					'allowed_attachments_types' => get_allowed_mime_types(),
					'taxonomies'                => get_taxonomies(
						[
							'public'   => true,
							'_builtin' => false

						],
						'names',
						'and'
					),
					'indexable_custom_fields'   => $this->get_indexable_custom_fields(),
					'selected_custom_fields'    => WPSOLR_Global::getOption()->get_fields_custom_fields_array(),
					'indexes'                   => WPSOLR_Global::getExtensionIndexes()->get_indexes()
				],
				$plugin_parameters
			)
		);
	}

	/**
	 * Get groups
	 *
	 * @return array Groups
	 */
	public function get_groups() {

		$groups = WPSOLR_Global::getOption()->get_option_fields();

		return $groups;
	}

	/**
	 * Get group
	 *
	 * @@param string $group_id
	 * @return array Group
	 */
	public function get_group( $group_id ) {

		$groups = WPSOLR_Global::getOption()->get_option_fields();

		if ( empty( $groups ) || empty( $groups[ $group_id ] ) ) {
			throw new WPSOLR_Exception( sprintf( 'Fields \'%s\' is unknown.', $group_id ) );
		}

		return $groups[ $group_id ];
	}

	/**
	 * Get query filter
	 *
	 * @param $query
	 *
	 * @return string Fields filter
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

	/**
	 * Get all post types, except some.
	 *
	 * @return array
	 */
	protected function get_indexable_post_types() {

		$post_types = get_post_types();

		$results = array();
		foreach ( $post_types as $post_type ) {

			if ( $post_type != 'attachment' && $post_type != 'revision' && $post_type != 'nav_menu_item' ) {

				array_push( $results, $post_type );
			}
		}

		return $results;
	}

	private function get_indexable_custom_fields() {
		global $wpdb;

		// custom fields 'standard' first
		$custom_fields = $wpdb->get_col( "SELECT distinct meta_key
						FROM $wpdb->postmeta
						WHERE meta_key != 'bwps_enable_ssl'
						AND meta_key NOT LIKE '\_%'
						ORDER BY meta_key" );

		// custom fields 'special' then
		$custom_fields_special = $wpdb->get_col( "SELECT distinct meta_key
						FROM $wpdb->postmeta
						WHERE meta_key != 'bwps_enable_ssl'
						AND meta_key LIKE '\_%'
						ORDER BY meta_key" );


		// woocommerce attributes
		$wcoocommerce_attributes = $this->get_woocommerce_attributes();

		return array_merge( $wcoocommerce_attributes, $custom_fields, $custom_fields_special );
	}

	/**
	 * Get woocommerce attributes
	 */
	public function get_woocommerce_attributes() {

		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {

			return WPSOLR_Plugin_WooCommerce::get_attribute_taxonomy_names();
		}

		return [ ];
	}


	/**
	 * Retrieve the field type object of a field name
	 *
	 * @param string $field_name
	 *
	 * @return WPSOLR_Field_Type Field
	 */
	public function get_field_type_definition( $field, $field_name ) {

		// Default type if none found
		$solr_type = WPSOLR_Field_Types::SOLR_TYPE_STRING;

		if ( isset( $field[ self::FORM_FIELD_CUSTOM_FIELDS ] ) && isset( $field[ self::FORM_FIELD_CUSTOM_FIELDS ][ $field_name ] ) ) {
			$solr_type = $field[ self::FORM_FIELD_CUSTOM_FIELDS ][ $field_name ][ self::FORM_FIELD_SOLR_TYPE ];
		}

		// Get the type definition
		return WPSOLR_Global::getSolrFieldTypes()->get_field_type( $solr_type );
	}

	/**
	 * Get custom fields of a field
	 *
	 * @param $field
	 *
	 * @return array
	 */
	public function get_custom_fields( $field ) {

		$results = ! empty( $field[ self::FORM_FIELD_CUSTOM_FIELDS ] ) ? $field[ self::FORM_FIELD_CUSTOM_FIELDS ] : [ ];

		return $results;
	}

	/**
	 * Get taxonomies of a field
	 *
	 * @param $field
	 *
	 * @return array
	 */
	public function get_taxonomies( $field ) {

		$results = ! empty( $field[ self::FORM_FIELD_TAXONOMIES ] )
			? WPSOLR_Field_Types::add_fields_type( $field[ self::FORM_FIELD_TAXONOMIES ], WPSOLR_Field_Types::SOLR_TYPE_STRING )
			: [ ];

		return $results;
	}

}