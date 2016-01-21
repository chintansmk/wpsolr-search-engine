<?php

namespace wpsolr\extensions\facets;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\solr\WPSOLR_Field_Types;
use wpsolr\solr\WPSOLR_Schema;
use wpsolr\ui\widget\WPSOLR_Widget_Facet;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;

/**
 * Class WPSOLR_Options_Facets
 *
 * Manage Facets
 */
class WPSOLR_Options_Facets extends WPSOLR_Extensions {

	// Facet sort
	const FACET_FIELD_SORT = 'sort';
	const FACET_SORT_ALPHABETICAL = 'index';
	const FACET_SORT_COUNT = 'count';

	// Is a facet in an exclusion tag (show misssing elements)
	const FACET_FIELD_IS_EXCLUSION = 'missing';

	// Facet range start
	const FACET_RANGE = 'range';
	const FACET_RANGE_START = 'start';
	const FACET_RANGE_START_DEFAULT = '0';
	const FACET_RANGE_END = 'end';
	const FACET_RANGE_END_DEFAULT = '100';
	const FACET_RANGE_GAP = 'gap';
	const FACET_RANGE_GAP_DEFAULT = '10';

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

		$new_facets_group_uuid = WPSOLR_Global::getExtensionIndexes()->generate_uuid();

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'                   => WPSOLR_Global::getOption()->get_option_facet(
						[ WPSOLR_Option::OPTION_FACETS_FACETS => '' ]
					),
					'layouts'                   => WPSOLR_Widget_Facet::get_facets_layouts(),
					'default_facets_group_uuid' => WPSOLR_Global::getOption()->get_facets_group_default(),
					'new_facets_group_uuid'     => $new_facets_group_uuid,
					'facets_groups'             => array_merge(
						WPSOLR_Global::getOption()->get_facets_groups(),
						[
							$new_facets_group_uuid => [
								'name' => 'New group'
							]
						] ),
					'facets_selected'           => WPSOLR_Global::getOption()->get_facets_selected_array(),
					'fields'                    => array_merge(
						WPSOLR_Field_Types::add_fields_type( $this->get_special_fields(), WPSOLR_Field_Types::SOLR_TYPE_STRING ),
						WPSOLR_Global::getOption()->get_fields_custom_fields_array(),
						WPSOLR_Field_Types::add_fields_type( WPSOLR_Global::getOption()->get_fields_taxonomies_array(), WPSOLR_Field_Types::SOLR_TYPE_STRING )
					),
					'image_plus'                => plugins_url( '../../../../images/plus.png', __FILE__ ),
					'image_minus'               => plugins_url( '../../../../images/success.png', __FILE__ )
				],
				$plugin_parameters
			)
		);
	}

	/**
	 * Get facets of a facets group
	 *
	 * @param string $facets_group Group of facets
	 *
	 * @return array Facets of the group
	 */
	public function get_facets_from_group( $facets_group ) {

		$facets_groups = WPSOLR_Global::getOption()->get_facets_selected_array();

		return ! empty( $facets_groups[ $facets_group ] ) ? $facets_groups[ $facets_group ] : [ ];
	}


	/**
	 * Get facets of default group
	 *
	 * @return array Facets of default group
	 */
	public function get_facets_from_default_group() {

		$default_facets_group_id = WPSOLR_Global::getOption()->get_facets_group_default();

		if ( ! empty( $default_facets_group_id ) ) {
			return $this->get_facets_from_group( $default_facets_group_id );
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
	 * Get sort value of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_sort( $facet ) {
		return isset( $facet[ self::FACET_FIELD_SORT ] ) ? $facet[ self::FACET_FIELD_SORT ] : self::FACET_SORT_ALPHABETICAL;
	}

	/**
	 * Is facet a range ?
	 *
	 * @param $facet
	 */
	public function get_facet_is_range( $facet ) {
		return isset( $facet[ self::FACET_RANGE ] );
	}

	/**
	 * Get range start of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_range_start( $facet ) {
		return isset( $facet[ self::FACET_RANGE ] ) && isset( $facet[ self::FACET_RANGE ][ self::FACET_RANGE_START ] ) ? $facet[ self::FACET_RANGE ][ self::FACET_RANGE_START ] : self::FACET_RANGE_START_DEFAULT;
	}

	/**
	 * Get range end of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_range_end( $facet ) {
		return isset( $facet[ self::FACET_RANGE ] ) && isset( $facet[ self::FACET_RANGE ][ self::FACET_RANGE_END ] ) ? $facet[ self::FACET_RANGE ][ self::FACET_RANGE_END ] : self::FACET_RANGE_END_DEFAULT;
	}

	/**
	 * Get range gap of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_range_gap( $facet ) {
		return isset( $facet[ self::FACET_RANGE ] ) && isset( $facet[ self::FACET_RANGE ][ self::FACET_RANGE_GAP ] ) ? $facet[ self::FACET_RANGE ][ self::FACET_RANGE_GAP ] : self::FACET_RANGE_GAP_DEFAULT;
	}

	/**
	 * Does a facet shows also items not in results ?
	 *
	 * @param $facet
	 */
	public function get_is_facet_in_exclusion_tag( $facet ) {
		return isset( $facet[ self::FACET_FIELD_IS_EXCLUSION ] );
	}
}