<?php

namespace wpsolr\extensions\sorts;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\solr\WPSOLR_Schema;
use wpsolr\ui\widget\WPSOLR_Widget_Sort;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;

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
	const SORT_FIELD_LABEL_ASC = 'label_asc'; // Label of the sort element, ascending
	const SORT_FIELD_LABEL_DESC = 'label_desc';

	// Form fields
	const SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD = 'default_sort_field';
	const SORT_FIELD_NAME = 'name';

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

		$new_sorts_group_uuid = WPSOLR_Global::getExtensionIndexes()->generate_uuid();

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'                  => WPSOLR_Global::getOption()->get_option_sort(
						[ WPSOLR_Option::OPTION_SORTS_SORTS => '' ]
					),
					'layouts'                  => WPSOLR_Widget_Sort::get_sorts_layouts(),
					'default_sorts_group_uuid' => $this->get_default_sorts_group_id(),
					'new_sorts_group_uuid'     => $new_sorts_group_uuid,
					'sorts_groups'             => array_merge(
						WPSOLR_Global::getOption()->get_sorts_groups(),
						[
							$new_sorts_group_uuid => [
								'name' => 'New group'
							]
						] ),
					'sorts_selected'           => WPSOLR_Global::getOption()->get_sorts_selected_array(),
					'fields'                   => $this->get_fields_sortable(),
					'image_plus'               => plugins_url( '../../../../images/plus.png', __FILE__ ),
					'image_minus'              => plugins_url( '../../../../images/success.png', __FILE__ )
				],
				$plugin_parameters
			)
		);
	}


	/**
	 * Get all indexed fields sortable
	 *
	 * @return array
	 */
	public function get_fields_sortable() {

		// Custom fields indexed
		$indexed_custom_fields = WPSOLR_Global::getOption()->get_fields_custom_fields_array();

		// Filter on sortable fields
		$sortable_fields = WPSOLR_Global::getSolrFieldTypes()->get_sortable( $indexed_custom_fields );

		return $sortable_fields;
	}

	/**
	 * Get sorts of a sorts group
	 *
	 * @param string $sorts_group_id Group of sorts
	 *
	 * @return array Sorts of the group
	 */
	public function get_sorts_from_group( $sorts_group_id ) {

		$sorts_groups = WPSOLR_Global::getOption()->get_sorts_selected_array();

		return ! empty( $sorts_groups[ $sorts_group_id ] ) ? $sorts_groups[ $sorts_group_id ] : [ ];
	}


	/**
	 * Get sorts of default group
	 *
	 * @return array Sorts of default group
	 */
	public function get_sorts_from_default_group() {

		$default_sorts_group_id = WPSOLR_Global::getOption()->get_default_sorts_group_id();

		if ( ! empty( $default_sorts_group_id ) ) {
			return $this->get_sorts_from_group( $default_sorts_group_id );
		}

		return [ ];
	}

	/**
	 * Get sorts group
	 *
	 * @@param string $sorts_group_id
	 * @return array Sorts group
	 */
	public function get_sorts_group( $sorts_group_id ) {

		$sorts_groups = WPSOLR_Global::getOption()->get_sorts_groups();

		if ( ! empty( $sorts_group_id ) && ! empty( $sorts_groups ) && ! empty( $sorts_groups[ $sorts_group_id ] ) ) {
			return $sorts_groups[ $sorts_group_id ];
		}

		return [ ];
	}

	/**
	 * Get default sorts group id
	 *
	 * @return string Default sorts group id
	 */
	public function get_default_sorts_group_id() {

		return WPSOLR_Global::getOption()->get_default_sorts_group_id();
	}

	/**
	 * Get default sorts group
	 *
	 * @return array Default sorts group
	 */
	public function get_default_sorts_group() {

		$default_sorts_group_id = $this->get_default_sorts_group_id();
		if ( ! empty( $default_sorts_group_id ) ) {
			return $this->get_sorts_group( $default_sorts_group_id );
		}

		return [ ];
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
	 * Get default sort field of a group
	 *
	 * @param $sort
	 */
	public function get_sort_default_name( $sorts_group ) {
		return isset( $sorts_group[ self::SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD ] ) ? $sorts_group[ self::SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD ] : self::SORT_CODE_BY_RELEVANCY_DESC;
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
	 * Get label asc of a sort
	 *
	 * @param $sort
	 *
	 * @return string Sort label asc
	 */
	public function get_sort_label_asc( $sort ) {
		return isset( $sort[ self::SORT_FIELD_LABEL_ASC ] ) ? $sort[ self::SORT_FIELD_LABEL_ASC ] : '';
	}

}