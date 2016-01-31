<?php

namespace wpsolr\extensions\facets;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\solr\WPSOLR_Field_Types;
use wpsolr\solr\WPSOLR_Schema;
use wpsolr\ui\widget\WPSOLR_Widget;
use wpsolr\ui\widget\WPSOLR_Widget_Facet;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;
use wpsolr\utilities\WPSOLR_Regexp;

/**
 * Class WPSOLR_Options_Facets
 *
 * Manage Facets
 */
class WPSOLR_Options_Facets extends WPSOLR_Extensions {

	// Facet types
	const FACET_TYPE_FIELD = 'facet_field';
	const FACET_TYPE_RANGE = 'facet_range';
	const FACET_TYPE_QUERY = 'facet_query';

	// Facet labels
	const FACET_FIELD_LABEL = 'label'; // Facet label
	const FACET_FIELD_LABEL_FIRST = 'label_first'; // Label of the first label element
	const FACET_FIELD_LABEL_LAST = 'label_last'; // Label of the last label element

	// Facet labels templates
	const FACET_LABEL_TEMPLATE = '%1$s (%2$s)';
	const FACET_LABEL_TEMPLATE_RANGE = '%1$s - %2$s (%3$d)';

	// Facet sort
	const FACET_FIELD_SORT = 'sort';
	const FACET_SORT_ALPHABETICAL = 'index';
	const FACET_SORT_COUNT = 'count';

	// Is a facet in an exclusion tag (show misssing elements)
	const FACET_FIELD_IS_EXCLUSION = 'missing';

	// Facet range
	const FACET_FIELD_RANGE = 'range';
	const FACET_FIELD_RANGE_START = 'start';
	const FACET_FILED_RANGE_START_DEFAULT = '0';
	const FACET_FIELD_RANGE_END = 'end';
	const FACET_FIELD_RANGE_END_DEFAULT = '100';
	const FACET_FIELD_RANGE_GAP = 'gap';
	const FACET_FIELD_RANGE_GAP_DEFAULT = '10';

	// Facet query
	const FACET_FIELD_QUERY = 'query';
	const FACET_FIELD_QUERY_RANGES = 'custom_ranges';
	const FACET_FIELD_QUERY_RANGES_DEFAULT = '0|10|%1$s - %2$s (%3$d)';
	const FACET_FIELD_QUERY_RANGE_INF = 'range_inf';
	const FACET_FIELD_QUERY_RANGE_SUP = 'range_sup';
	const FACET_FIELD_QUERY_RANGE_LABEL = 'range_label';

	// Layouts available for each field type
	protected $layouts;

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
					'layouts'                   => $this->get_facets_layouts_by_field_types(),
					'default_facets_group_uuid' => $this->get_default_facets_group_id(),
					'new_facets_group_uuid'     => $new_facets_group_uuid,
					'facets_groups'             => array_merge(
						WPSOLR_Global::getOption()->get_facets_groups(),
						[
							$new_facets_group_uuid => [
								'name'                                          => 'New group',
								WPSOLR_Option::OPTION_FACETS_GROUP_FILTER_QUERY => ''
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
	 * Get all layouts by field type
	 *
	 * @return array
	 */
	public function get_facets_layouts_by_field_types() {

		$results = [ ];

		$field_types_id = array_keys( WPSOLR_Global::getSolrFieldTypes()->get_field_types() );
		$layouts        = WPSOLR_Widget_Facet::get_facets_layouts();

		foreach ( $layouts as $layout_id => $layout ) {

			$layout_field_types = isset( $layout[ WPSOLR_Widget::LAYOUT_FIELD_TYPES ] ) ? $layout[ WPSOLR_Widget::LAYOUT_FIELD_TYPES ] : $field_types_id;

			foreach ( $layout_field_types as $layout_field_type ) {

				$results[ $layout_field_type ][ $layout_id ] = $layout;
			}
		}

		return $results;
	}

	/**
	 * Get facets of a facets group
	 *
	 * @param string $facets_group_id Group of facets
	 *
	 * @return array Facets of the group
	 */
	public function get_facets_from_group( $facets_group_id ) {

		$facets_groups = WPSOLR_Global::getOption()->get_facets_selected_array();

		return ! empty( $facets_groups[ $facets_group_id ] ) ? $facets_groups[ $facets_group_id ] : [ ];
	}


	/**
	 * Get facets of default group
	 *
	 * @return array Facets of default group
	 */
	public function get_facets_from_default_group() {

		$default_facets_group_id = WPSOLR_Global::getOption()->get_default_facets_group_id();

		if ( ! empty( $default_facets_group_id ) ) {
			return $this->get_facets_from_group( $default_facets_group_id );
		}

		return [ ];
	}

	/**
	 * Get facets group
	 *
	 * @@param string $facets_group_id
	 * @return array Facets group
	 */
	public function get_facets_group( $facets_group_id ) {

		$facets_groups = WPSOLR_Global::getOption()->get_facets_groups();

		if ( ! empty( $facets_group_id ) && ! empty( $facets_groups ) && ! empty( $facets_groups[ $facets_group_id ] ) ) {
			return $facets_groups[ $facets_group_id ];
		}

		return [ ];
	}

	/**
	 * Get facets group filter query
	 *
	 * @@param string $facets_group_id
	 * @return string Facets group filter query
	 */
	public function get_facets_group_filter_query( $facets_group_id ) {

		$facets_groups = $this->get_facets_group( $facets_group_id );

		if ( ! empty( $facets_groups ) && ! empty( $facets_groups[ WPSOLR_Option::OPTION_FACETS_GROUP_FILTER_QUERY ] ) ) {
			return $facets_groups[ WPSOLR_Option::OPTION_FACETS_GROUP_FILTER_QUERY ];
		}

		return '';
	}

	/**
	 * Get default facets group id
	 *
	 * @return string Default facets group id
	 */
	public function get_default_facets_group_id() {

		return WPSOLR_Global::getOption()->get_default_facets_group_id();
	}

	/**
	 * Get default facets group
	 *
	 * @return array Default facets group
	 */
	public function get_default_facets_group() {

		$default_facets_group_id = $this->get_default_facets_group_id();
		if ( ! empty( $default_facets_group_id ) ) {
			return $this->get_facets_group( $default_facets_group_id );
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
		return isset( $facet[ self::FACET_FIELD_RANGE ] );
	}

	/**
	 * Is facet query ?
	 *
	 * @param $facet
	 */
	public function get_facet_is_query( $facet ) {
		return isset( $facet[ self::FACET_FIELD_QUERY ] );
	}

	/**
	 * Get range start of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_range_start( $facet ) {
		return isset( $facet[ self::FACET_FIELD_RANGE ] ) && isset( $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_START ] ) ? $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_START ] : self::FACET_FILED_RANGE_START_DEFAULT;
	}

	/**
	 * Get range end of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_range_end( $facet ) {
		return isset( $facet[ self::FACET_FIELD_RANGE ] ) && isset( $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_END ] ) ? $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_END ] : self::FACET_FIELD_RANGE_END_DEFAULT;
	}

	/**
	 * Get range gap of a facet
	 *
	 * @param $facet
	 */
	public function get_facet_range_gap( $facet ) {
		return isset( $facet[ self::FACET_FIELD_RANGE ] ) && isset( $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_GAP ] ) ? $facet[ self::FACET_FIELD_RANGE ][ self::FACET_FIELD_RANGE_GAP ] : self::FACET_FIELD_RANGE_GAP_DEFAULT;
	}

	/**
	 * Does a facet shows also items not in results ?
	 *
	 * @param $facet
	 */
	public function get_is_facet_in_exclusion_tag( $facet ) {
		return isset( $facet[ self::FACET_FIELD_IS_EXCLUSION ] );
	}

	/**
	 * Get label of a facet element
	 *
	 * @param $facet
	 */
	public function get_facet_label( $facet ) {
		return isset( $facet[ self::FACET_FIELD_LABEL ] ) ? $facet[ self::FACET_FIELD_LABEL ] : '';
	}

	/**
	 * Get label of a facet first element
	 *
	 * @param $facet
	 */
	public function get_facet_label_first( $facet ) {
		return isset( $facet[ self::FACET_FIELD_LABEL_FIRST ] ) ? $facet[ self::FACET_FIELD_LABEL_FIRST ] : '';
	}

	/**
	 * Get label of a facet last element
	 *
	 * @param $facet
	 */
	public function get_facet_label_last( $facet ) {
		return isset( $facet[ self::FACET_FIELD_LABEL_LAST ] ) ? $facet[ self::FACET_FIELD_LABEL_LAST ] : '';
	}


	/**
	 * Get an array of custom ranges for a range query facet
	 *
	 * 0|9|%1$s - %2$s (%3$d)
	 * 10|20|%1$s TO %2$s (%3$d)
	 * =>
	 * [
	 *  ['range_inf' => '0',  'range_sup' => '9',  'range_label' => '%1$s - %2$s (%3$d)']
	 *  ['range_inf' => '10', 'range_sup' => '20', 'range_label' => '%1$s TO %2$s (%3$d)']
	 * ]
	 *
	 * @param $facet
	 *
	 * @return array
	 */
	public function get_facet_query_custom_ranges( $facet ) {

		$results = [ ];

		$custom_ranges_string = isset( $facet[ self::FACET_FIELD_QUERY ] ) && isset( $facet[ self::FACET_FIELD_QUERY ][ self::FACET_FIELD_QUERY_RANGES ] ) ? $facet[ self::FACET_FIELD_QUERY ][ self::FACET_FIELD_QUERY_RANGES ] : '';

		if ( ! empty( $custom_ranges_string ) ) {

			foreach ( WPSOLR_Regexp::split_lines( $custom_ranges_string ) as $custom_range_string ) {

				$custom_range = explode( '|', $custom_range_string );
				$results[]    = [
					self::FACET_FIELD_QUERY_RANGE_INF   => $custom_range[0],
					self::FACET_FIELD_QUERY_RANGE_SUP   => $custom_range[1],
					self::FACET_FIELD_QUERY_RANGE_LABEL => $custom_range[2]
				];
			}
		}

		return $results;
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
		$domain  = 'wpsolr facets'; // never change this

		// Fields that can be translated and their definition
		$fields_translatable = [
			self::FACET_FIELD_LABEL_FIRST  => [ 'name' => 'First facet Label', 'is_multiline' => false ],
			self::FACET_FIELD_LABEL        => [ 'name' => 'Middle facet Label', 'is_multiline' => false ],
			self::FACET_FIELD_LABEL_LAST   => [ 'name' => 'Last facet Label', 'is_multiline' => false ],
			self::FACET_FIELD_QUERY_RANGES => [ 'name' => 'Uneven Range facet Labels', 'is_multiline' => true ]
		];

		$groups = WPSOLR_Global::getOption()->get_facets_selected_array();

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

					if ( ! empty( $field[ self::FACET_FIELD_QUERY ] ) && ! empty( $field[ self::FACET_FIELD_QUERY ][ $translatable_name ] ) ) {

						// Extract the 2rd column of each line
						$label = '';
						foreach ( WPSOLR_Regexp::split_lines( $field[ self::FACET_FIELD_QUERY ][ $translatable_name ] ) as $line ) {
							$label = explode( '|', $line )[2];

							$results[] = $this->get_string_to_translate(
								$label, //sprintf( ' % s of % s % s', $translatable['name'], $this->get_facets_group( $facets_group_name )['name'], $facet_field['name'] ),
								$label,
								$domain,
								$translatable['is_multiline']
							);

						}
					}
				}
			}
		}

		return $results;
	}

}