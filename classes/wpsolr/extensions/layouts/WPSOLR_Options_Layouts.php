<?php

namespace wpsolr\extensions\layouts;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_Layouts
 *
 * Manage Layout
 */
class WPSOLR_Options_Layouts extends WPSOLR_Extensions {

	// Layout types
	const TYPE_LAYOUT_FACET_GROUP = 'type_layout_facet_group';
	const TYPE_LAYOUT_FACET = 'type_layout_facet';
	const TYPE_LAYOUT_FACET_FILTER_GROUP = 'type_layout_facet_filter_group';
	const TYPE_LAYOUT_FACET_FILTER = 'type_layout_facet_filter';
	const TYPE_LAYOUT_SORT_GROUP = 'type_layout_sort_group';
	const TYPE_LAYOUT_SORT = 'type_layout_sort';
	const TYPE_LAYOUT_RESULT_ROW_GROUP = 'type_layout_result_group';
	const TYPE_LAYOUT_RESULT_ROW = 'type_layout_result';

	const TYPE_LAYOUT_FIELD_NAME = 'name';
	const TYPE_LAYOUT_FIELD_LAYOUTS = 'layouts';

	// Generic layout id
	const GENERIC_LAYOUT_ID = 'generic';
	// Layout fields in array definition
	const LAYOUT_FIELD_LAYOUT_ID = 'layout_id';
	const LAYOUT_FIELD_TEMPLATE_HTML = 'template_html';
	const LAYOUT_FIELD_TEMPLATE_CSS = 'template_css';
	const LAYOUT_FIELD_TEMPLATE_JS = 'template_js';
	const LAYOUT_FIELD_TEMPLATE_NAME = 'name';
	const LAYOUT_FIELD_FACET_TYPE = 'facet_type';
	const LAYOUT_FIELD_TYPES = 'field_types';
	const LAYOUT_FIELD_PREDEFINED_LAYOUT_ID = 'predefined_layout_id';

	protected static $predefined_layouts;

	/**
	 * Post constructor.
	 */
	protected function post_constructor() {

		$numeric_types = WPSOLR_Global::getSolrFieldTypes()->get_numeric_types();

		self::$predefined_layouts = [
			self::TYPE_LAYOUT_RESULT_ROW_GROUP   => [
				self::TYPE_LAYOUT_FIELD_NAME    => 'Results rows group',
				self::TYPE_LAYOUT_FIELD_LAYOUTS => [
					self::GENERIC_LAYOUT_ID => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - list',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/js.twig'
					]
				]
			],
			self::TYPE_LAYOUT_RESULT_ROW         => [
				self::TYPE_LAYOUT_FIELD_NAME    => 'Results rows line',
				self::TYPE_LAYOUT_FIELD_LAYOUTS => [
					self::GENERIC_LAYOUT_ID => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Line',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig'
					]
				]
			],
			self::TYPE_LAYOUT_SORT_GROUP         => [
				self::TYPE_LAYOUT_FIELD_NAME    => 'Sort group',
				self::TYPE_LAYOUT_FIELD_LAYOUTS => [
					self::GENERIC_LAYOUT_ID => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Drop down list',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/js.twig'
					]
				]
			],
			self::TYPE_LAYOUT_SORT               => [
				self::TYPE_LAYOUT_FIELD_NAME    => 'Sort content',
				self::TYPE_LAYOUT_FIELD_LAYOUTS => [
					self::GENERIC_LAYOUT_ID => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Text',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig'
					]
				]
			],
			self::TYPE_LAYOUT_FACET_FILTER_GROUP => [
				self::TYPE_LAYOUT_FIELD_NAME    => 'Facet filter group',
				self::TYPE_LAYOUT_FIELD_LAYOUTS => [
					self::GENERIC_LAYOUT_ID => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - List',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/filters/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/filters/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/filters/js.twig'
					]
				]
			],
			self::TYPE_LAYOUT_FACET_FILTER       => [
				self::TYPE_LAYOUT_FIELD_NAME    => 'Facet filter content',
				self::TYPE_LAYOUT_FIELD_LAYOUTS => [
					'checkbox' => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Check boxes',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/filters/checkbox/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/filters/checkbox/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/filters/checkbox/js.twig'
					],
					'radiobox' => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Radio boxes',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/filters/checkbox/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/filters/radiobox/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/filters/checkbox/js.twig'
					]
				]
			],
			self::TYPE_LAYOUT_FACET_GROUP        => [
				self::TYPE_LAYOUT_FIELD_NAME    => 'Facet group',
				self::TYPE_LAYOUT_FIELD_LAYOUTS => [
					self::GENERIC_LAYOUT_ID => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - List',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/js.twig'
					]
				]
			],
			self::TYPE_LAYOUT_FACET              => [
				self::TYPE_LAYOUT_FIELD_NAME    => 'Facet content',
				self::TYPE_LAYOUT_FIELD_LAYOUTS => [
					'dropdownlist'          => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Drop down list',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/dropdownlist/single/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/dropdownlist/single/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/dropdownlist/single/js.twig',
						self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_FIELD
					],
					'dropdownlist_multiple' => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Drop down list with multiple selection',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/dropdownlist/multiple/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/dropdownlist/multiple/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/dropdownlist/multiple/js.twig',
						self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_FIELD
					],
					'radiobox'              => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Radio boxes',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/checkbox/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/radiobox/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/radiobox/js.twig',
						self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_FIELD
					],
					'checkbox'              => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Check boxes',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/checkbox/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/checkbox/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/checkbox/js.twig',
						self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_FIELD
					],
					'range'                 => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Regular ranges with radio boxes',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/checkbox/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/radiobox/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/radiobox/js.twig',
						self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_RANGE,
						self::LAYOUT_FIELD_TYPES         => $numeric_types
					],
					'range_checkbox'        => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Regular ranges with check boxes',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/checkbox/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/checkbox/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/checkbox/js.twig',
						self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_RANGE,
						self::LAYOUT_FIELD_TYPES         => $numeric_types
					],
					'custom_range'          => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Irregular ranges with radio boxes',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/checkbox/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/radiobox/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/radiobox/js.twig',
						self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_CUSTOM_RANGE,
						self::LAYOUT_FIELD_TYPES         => $numeric_types
					],
					'custom_range_checkbox' => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Irregular ranges with check boxes',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/checkbox/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/checkbox/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/checkbox/js.twig',
						self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_CUSTOM_RANGE,
						self::LAYOUT_FIELD_TYPES         => $numeric_types
					],
					'slider'                => [
						self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR - Slider',
						self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/slider/html.twig',
						self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/slider/css.twig',
						self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/slider/js.twig',
						self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_MIN_MAX,
						self::LAYOUT_FIELD_TYPES         => $numeric_types
					]
				]
			]
		];

	}

	/**
	 * Display admin form.
	 *
	 * @param array $plugin_parameters Parameters
	 */
	public function output_form( $form_file = null, $plugin_parameters = [ ] ) {

		// Clone some layouts
		$layout_types = WPSOLR_Global::getOption()->get_option_layouts();
		if ( ! is_array( $layout_types ) ) {
			$layout_types = [ ];
		}
		$layout_types = $this->clone_some_layouts( $layout_types );


		// Add new uuid to layouts types
		$new_layouts      = [ ];
		$new_layout_uuids = [ ];
		foreach ( self::$predefined_layouts as $predefined_layouts_type => $predefined_layout ) {

			$new_uuid = WPSOLR_Global::getExtensionIndexes()->generate_uuid();

			$new_layout_uuids[]                      = $new_uuid;
			$new_layouts[ $predefined_layouts_type ] = [
				$new_uuid => [
					self::LAYOUT_FIELD_TEMPLATE_NAME => sprintf( 'New %s layout', $predefined_layout[ self::TYPE_LAYOUT_FIELD_NAME ] )
				]
			];
		}


		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'                 => WPSOLR_Global::getOption()->get_option_layouts(),
					'predefined_layout_types' => self::$predefined_layouts,
					'layout_types'            => $layout_types,
					'new_layout_uuids'        => $new_layout_uuids,
					'new_layouts'             => $new_layouts,
				],
				$plugin_parameters
			)
		);
	}

	/**
	 * Get layouts of a type
	 *
	 * @param $layout_type
	 *
	 * @return array Layouts
	 */
	public function get_layouts_from_type( $layout_type ) {

		$layouts = WPSOLR_Global::getOption()->get_option_layouts();

		// No custom layouts of this type
		if ( empty( $layouts ) || empty( $layouts[ $layout_type ] ) || empty( $layouts[ $layout_type ][ self::TYPE_LAYOUT_FIELD_LAYOUTS ] ) ) {

			// Must be a predefined layout
			if ( isset( self::$predefined_layouts[ $layout_type ] ) && isset( self::$predefined_layouts[ $layout_type ][ self::TYPE_LAYOUT_FIELD_LAYOUTS ] ) ) {
				return self::$predefined_layouts[ $layout_type ][ self::TYPE_LAYOUT_FIELD_LAYOUTS ];
			}

			throw new WPSOLR_Exception( sprintf( 'layout type %s is unknown', $layout_type ) );
		}

		// Fill the fields of custom layouts with their model fields
		foreach ( $layouts[ $layout_type ][ self::TYPE_LAYOUT_FIELD_LAYOUTS ] as &$layout ) {

			$predefined_layout = self::$predefined_layouts[ $layout_type ][ self::TYPE_LAYOUT_FIELD_LAYOUTS ][ $layout[ self::LAYOUT_FIELD_PREDEFINED_LAYOUT_ID ] ];

			foreach ( [ self::LAYOUT_FIELD_TYPES, self::LAYOUT_FIELD_FACET_TYPE ] as $layout_field ) {
				if ( isset( $predefined_layout[ $layout_field ] ) ) {
					$layout[ $layout_field ] = $predefined_layout[ $layout_field ];
				}
			}
		}

		$layouts = array_merge(
			$layouts[ $layout_type ][ self::TYPE_LAYOUT_FIELD_LAYOUTS ],
			self::$predefined_layouts[ $layout_type ][ self::TYPE_LAYOUT_FIELD_LAYOUTS ] );

		return $layouts;
	}


	/**
	 * Get a layout from a layout type and layout id
	 *
	 * @param $layout_type
	 * @param $layout_id
	 *
	 * @return array Layout
	 */
	public function get_layout_from_type_and_id( $layout_type, $layout_id ) {

		$layouts = $this->get_layouts_from_type( $layout_type );

		if ( empty( $layouts ) || empty( $layouts[ $layout_id ] ) ) {
			throw new WPSOLR_Exception( sprintf( 'layout \'%s\' is unknown.', $layout_id ) );
		}

		return ! empty( $layouts ) && ! empty( $layouts[ $layout_id ] ) ? $layouts[ $layout_id ] : [ ];
	}

	/**
	 * Get a layout name
	 *
	 * @param array $layout Layout
	 *
	 * @return string Layout name
	 */
	public function get_layout_name( $layout ) {

		return ! empty( $layout ) && ! empty( $layout[ self::LAYOUT_FIELD_TEMPLATE_NAME ] ) ? $layout[ self::LAYOUT_FIELD_TEMPLATE_NAME ] : '';
	}

	/**
	 * Get a layout html
	 *
	 * @param array $layout Layout
	 *
	 * @return string Layout html
	 */
	public function get_layout_template_html( $layout ) {

		return ! empty( $layout ) && ! empty( $layout[ self::LAYOUT_FIELD_TEMPLATE_HTML ] ) ? $layout[ self::LAYOUT_FIELD_TEMPLATE_HTML ] : '';
	}

	/**
	 * Get a layout css
	 *
	 * @param array $layout Layout
	 *
	 * @return string Layout css
	 */
	public function get_layout_template_css( $layout ) {

		return ! empty( $layout ) && ! empty( $layout[ self::LAYOUT_FIELD_TEMPLATE_CSS ] ) ? $layout[ self::LAYOUT_FIELD_TEMPLATE_CSS ] : '';
	}


	/**
	 * Get a layout js
	 *
	 * @param array $layout Layout
	 *
	 * @return string Layout js
	 */
	public function get_layout_template_js( $layout ) {

		return ! empty( $layout ) && ! empty( $layout[ self::LAYOUT_FIELD_TEMPLATE_JS ] ) ? $layout[ self::LAYOUT_FIELD_TEMPLATE_JS ] : '';
	}

	/**
	 * Clone the layouts marked.
	 *
	 * @param $layouts
	 */
	public function clone_some_layouts( &$layouts ) {

		foreach ( $layouts as $layout_type_name => &$layout_type ) {

			foreach ( $layout_type[ self::TYPE_LAYOUT_FIELD_LAYOUTS ] as $layout_uuid => &$layout ) {

				if ( ! empty( $layout['is_to_be_cloned'] ) ) {

					unset( $layout['is_to_be_cloned'] );

					// Clone the layout
					$layout_cloned         = $layout;
					$layout_cloned_uuid    = WPSOLR_Global::getExtensionIndexes()->generate_uuid();
					$layout_cloned['name'] = 'Clone of ' . $layout_cloned[ self::TYPE_LAYOUT_FIELD_NAME ];

					$layouts[ $layout_type_name ][ self::TYPE_LAYOUT_FIELD_LAYOUTS ][ $layout_cloned_uuid ] = $layout_cloned;

				}

			}
		}

		return $layouts;
	}

	/**
	 * @return mixed
	 */
	public function get_predefined_layouts() {
		return self::$predefined_layouts;
	}

}