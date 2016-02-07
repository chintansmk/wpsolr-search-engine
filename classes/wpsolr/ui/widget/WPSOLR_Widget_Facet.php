<?php

namespace wpsolr\ui\widget;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\solr\WPSOLR_Field_Types;
use wpsolr\ui\widget;
use wpsolr\ui\WPSOLR_Data_Facets;
use wpsolr\ui\WPSOLR_UI_Facets;
use wpsolr\utilities\WPSOLR_Global;

/**
 * WPSOLR Widget Facets.
 */
class WPSOLR_Widget_Facet extends WPSOLR_Widget {

	// Form fields
	const FORM_FIELD_FACETS_GROUP_ID = 'facets_group_id';
	const FORM_FIELD_FACETS_GROUP_NAME = 'name';
	const FORM_FIELD_SOLR_QUERY_PARAMETERS = 'added_solr_query';

	protected static $wpsolr_layouts = [
		self::TYPE_GROUP_LAYOUT         => [
			self::GENERIC_LAYOUT_ID => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'List',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/js.twig'
			]
		],
		self::TYPE_GROUP_ELEMENT_LAYOUT => [
			'dropdownlist'          => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Drop down list',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/dropdownlist/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/dropdownlist/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/dropdownlist/js.twig',
				self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_FIELD
			],
			'radiobox'              => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Radio boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/checkbox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/radiobox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/radiobox/js.twig',
				self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_FIELD
			],
			'checkbox'              => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Check boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/checkbox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/checkbox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/checkbox/js.twig',
				self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_FIELD
			],
			'range'                 => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Regular ranges with radio boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/range/radiobox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/radiobox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/range/radiobox/js.twig',
				self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_RANGE,
				self::LAYOUT_FIELD_TYPES         => [
					WPSOLR_Field_Types::SOLR_TYPE_INTEGER,
					WPSOLR_Field_Types::SOLR_TYPE_INTEGER_LONG,
					WPSOLR_Field_Types::SOLR_TYPE_FLOAT,
					WPSOLR_Field_Types::SOLR_TYPE_FLOAT_DOUBLE
				]
			],
			'range_checkbox'        => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Regular ranges with check boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/range/radiobox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/checkbox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/range/checkbox/js.twig',
				self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_RANGE,
				self::LAYOUT_FIELD_TYPES         => [
					WPSOLR_Field_Types::SOLR_TYPE_INTEGER,
					WPSOLR_Field_Types::SOLR_TYPE_INTEGER_LONG,
					WPSOLR_Field_Types::SOLR_TYPE_FLOAT,
					WPSOLR_Field_Types::SOLR_TYPE_FLOAT_DOUBLE
				]
			],
			'custom_range'          => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Irregular ranges with radio boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/range/radiobox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/radiobox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/range/radiobox/js.twig',
				self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_CUSTOM_RANGE,
				self::LAYOUT_FIELD_TYPES         => [
					WPSOLR_Field_Types::SOLR_TYPE_INTEGER,
					WPSOLR_Field_Types::SOLR_TYPE_INTEGER_LONG,
					WPSOLR_Field_Types::SOLR_TYPE_FLOAT,
					WPSOLR_Field_Types::SOLR_TYPE_FLOAT_DOUBLE
				]
			],
			'custom_range_checkbox' => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Irregular ranges with check boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/range/radiobox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/checkbox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/range/checkbox/js.twig',
				self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_CUSTOM_RANGE,
				self::LAYOUT_FIELD_TYPES         => [
					WPSOLR_Field_Types::SOLR_TYPE_INTEGER,
					WPSOLR_Field_Types::SOLR_TYPE_INTEGER_LONG,
					WPSOLR_Field_Types::SOLR_TYPE_FLOAT,
					WPSOLR_Field_Types::SOLR_TYPE_FLOAT_DOUBLE
				]
			],
			'slider'                => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Slider',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/slider/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/slider/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/slider/js.twig',
				self::LAYOUT_FIELD_FACET_TYPE    => WPSOLR_Options_Facets::FACET_TYPE_MIN_MAX,
				self::LAYOUT_FIELD_TYPES         => [
					WPSOLR_Field_Types::SOLR_TYPE_INTEGER,
					WPSOLR_Field_Types::SOLR_TYPE_INTEGER_LONG,
					WPSOLR_Field_Types::SOLR_TYPE_FLOAT,
					WPSOLR_Field_Types::SOLR_TYPE_FLOAT_DOUBLE
				]
			]
		]
	];


	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'wpsolr_widget_facets', // Base ID
			__( 'WPSOLR Facets', 'wpsolr_admin' ), // Name
			array( 'description' => __( 'Display Solr Facets', 'wpsolr_admin' ) ), // Args
			array() // controls
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	protected function wpsolr_form( $args, $instance ) {

		// Widget can be on a search page ?s=
		$wpsolr_query = WPSOLR_Global::getQuery();
		$group_id     = $wpsolr_query->get_wpsolr_facets_groups_id();

		if ( ! $wpsolr_query->get_wpsolr_is_search() ) {

			// Facets of the group on the query url
			if ( empty( $group_id ) ) {

				// Facets group of the widget
				$group_id = $this->wpsolr_get_instance_group_id( $instance );
				if ( empty( $group_id ) ) {
					throw new WPSOLR_Exception( sprintf( 'Select a facets group.' ) );
				}

				$wpsolr_query->set_wpsolr_facets_groups_id( $group_id );
			}
			// Facets of the facets groups
			$facets = WPSOLR_Global::getExtensionFacets()->get_facets_from_group( $group_id );

			$wpsolr_query->set_wpsolr_facets_fields( $facets );

			// Add Solr query fields from the Widget filter
			$wpsolr_query->wpsolr_add_query_fields( WPSOLR_Global::getExtensionFacets()->get_facets_group_filter_query( $group_id ) );
		} else {

			// Facets of the group on the query url for a search url
			$facets = WPSOLR_Global::getExtensionFacets()->get_facets_from_group( $group_id );
		}

		// Call and get Solr results
		$results = WPSOLR_Global::getSolrClient()->display_results( $wpsolr_query );

		// Build the facets UI
		echo WPSOLR_UI_Facets::Build(
			$group_id,
			WPSOLR_Data_Facets::get_data(
				WPSOLR_Options_Facets::FACET_FIELD_FACET_LAYOUT_ID,
				WPSOLR_Global::getQuery()->get_filter_query_fields_group_by_name(),
				$facets,
				$results[1] ),
			WPSOLR_Localization::get_options(),
			$args,
			$instance,
			$this->wpsolr_get_instance_layout( $instance, self::TYPE_GROUP_LAYOUT )
		);

	}

	/**
	 * Write the widget header
	 *
	 * @param $instance
	 */
	protected function wpsolr_header( $instance ) {
		?>

		<p>
			Facets are dynamic filters that users can click on to filter out the search results, like categories, or
			tags. Facets
			must have been defined in WPSOLR admin pages.
		</p>

		<?php
	}

	/**
	 * Get all facets layouts
	 * @return array
	 */
	public static function get_facets_layouts() {
		return self::wpsolr_get_layout_definitions( self::TYPE_GROUP_ELEMENT_LAYOUT );
	}

	public function wpsolr_get_groups() {
		return WPSOLR_Global::getOption()->get_facets_groups();
	}

}