<?php

namespace wpsolr\extensions\sorts;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\schemas\WPSOLR_Options_Schemas;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\solr\WPSOLR_Schema;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Regexp;

/**
 * Class WPSOLR_Options_Sorts
 *
 * Manage Sort
 */
class WPSOLR_Options_Sorts extends WPSOLR_Extensions {

	// Do not change - Sort by most relevant
	const SORT_CODE_BY_RELEVANCY_DESC = 'sort_by_relevancy_desc';

	// Do not change - Sort by newest
	const SORT_CODE_BY_DATE_DESC = 'sort_by_date_desc';

	// Do not change - Sort by oldest
	const SORT_CODE_BY_DATE_ASC = 'sort_by_date_asc';

	// Do not change - Sort by least comments
	const SORT_CODE_BY_NUMBER_COMMENTS_ASC = 'sort_by_number_comments_asc';

	// Do not change - Sort by most comments
	const SORT_CODE_BY_NUMBER_COMMENTS_DESC = 'sort_by_number_comments_desc';

	// Sort labels
	const SORT_FIELD_LABEL = 'label'; // Label of the sort element
	const SORT_FIELD_LABEL_DESC = 'label_desc';

	// Form fields
	const SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD = 'default_sort_field';
	const SORT_FIELD_NAME = 'name';

	// Sorting postfix in field names
	const SORT_FIELD_POSTFIX_ASC = '_asc';
	const SORT_FIELD_POSTFIX_DESC = '_desc';
	const FORM_FIELD_SORTS = 'sorts';


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
						'extension_name' => WPSOLR_Extensions::OPTION_SORTS,
						'extra_classes'  => 'wpsolr_2col',
						'options'        => $this->get_groups(),
						'new_group_uuid' => $new_group_uuid,
						'groups'         => array_merge(
							$this->clone_some_groups(),
							[
								$new_group_uuid => [
									'name' => 'New sorts'
								]
							] ),
						'schemas'        => WPSOLR_Global::getExtensionSchemas()->get_groups(),
						'layouts'        => WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( WPSOLR_Options_Layouts::TYPE_LAYOUT_SORT ),
						'image_plus'     => plugins_url( '../../../../images/plus.png', __FILE__ ),
						'image_minus'    => plugins_url( '../../../../images/success.png', __FILE__ )
					]
				],
				$plugin_parameters
			)
		);
	}

	public function get_groups() {

		$groups = WPSOLR_Global::getOption()->get_option_sorts();

		return $groups;
	}

	/**
	 * Get all indexed fields sortable
	 *
	 * @return array
	 */
	public function get_fields_sortable( $group ) {

		$results = [ ];

		$schema = WPSOLR_Global::getExtensionSchemas()->get_group( $this->get_schema_id( $group ) );

		// Custom fields indexed
		$custom_fields = WPSOLR_Global::getExtensionSchemas()->get_custom_fields( $schema );

		// Filter to get only sortable fields
		$sortable_fields = WPSOLR_Global::getSolrFieldTypes()->get_sortable( $custom_fields );

		// Add sorting postfixes to field names
		foreach ( $sortable_fields as $sortable_field_name => $sortable_field ) {
			$results[ $sortable_field_name . '_' . Query::SORT_ASC ]  = $sortable_field;
			$results[ $sortable_field_name . '_' . Query::SORT_DESC ] = $sortable_field;
		}

		return $results;
	}

	public function get_special_fields() {
		return [
			WPSOLR_Schema::_FIELD_NAME_TYPE,
			WPSOLR_Schema::_FIELD_NAME_AUTHOR,
			WPSOLR_Schema::_FIELD_NAME_CATEGORIES,
			WPSOLR_Schema::_FIELD_NAME_TAGS
		];
	}

	/**
	 * Get schema id of a group
	 *
	 * @param $group
	 */
	public function get_schema_id( $group ) {
		return isset( $group[ WPSOLR_Options_Schemas::FORM_FIELD_SCHEMA_ID ] ) ? $group[ WPSOLR_Options_Schemas::FORM_FIELD_SCHEMA_ID ] : '';
	}

	/**
	 * Get sorts of a group
	 *
	 * @param $group
	 */
	public function get_sorts( $group ) {
		return isset( $group[ self::FORM_FIELD_SORTS ] ) ? $group[ self::FORM_FIELD_SORTS ] : [ ];
	}

	/**
	 * Get default sort field of a group
	 *
	 * @param $sort
	 */
	public function get_sort_default_name( $sort ) {
		return isset( $sort[ self::SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD ] ) ? $sort[ self::SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD ] : self::SORT_CODE_BY_RELEVANCY_DESC;
	}


	/**
	 * Get name of a sort
	 *
	 * @param $sort
	 *
	 * @return string Sort name
	 */
	public function get_sort_name( $sort ) {
		return isset( $sort[ self::SORT_FIELD_NAME ] ) ? $sort[ self::SORT_FIELD_NAME ] : '';
	}

	/**
	 * Get label of a sort
	 *
	 * @param $sort
	 *
	 * @return string Sort label
	 */
	public function get_sort_label( $sort ) {
		return isset( $sort[ self::SORT_FIELD_LABEL ] ) ? $sort[ self::SORT_FIELD_LABEL ] : '';
	}


	/**
	 * Remove the sorting postfix from a field name
	 *
	 * 'field1_asc' => 'field1'
	 * 'field1_desc' => 'field1'
	 *
	 * @param $sort_field_name_for_solr Field name to strip
	 *
	 * @return string
	 */
	public function get_field_name_without_postfix( $sort_field_name_for_solr ) {

		$result = $sort_field_name_for_solr;

		$result = WPSOLR_Regexp::remove_string_at_the_end( $result, '_' . Query::SORT_ASC );
		$result = WPSOLR_Regexp::remove_string_at_the_end( $result, '_' . Query::SORT_DESC );

		return $result;
	}


	public function get_sort_order_by( $sort_field_name_with_postfix ) {

		$result = WPSOLR_Regexp::extract_last_separator( $sort_field_name_with_postfix, '_' );

		if ( ( $result != Query::SORT_ASC ) && ( $result != Query::SORT_DESC ) ) {
			throw new WPSOLR_Exception( sprintf( 'Sort field %s without order by postix.', $sort_field_name_with_postfix ) );
		}

		return $result;
	}

}