<?php

namespace wpsolr\ui\widget;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\ui\widget;
use wpsolr\ui\WPSOLR_Data_Facets;
use wpsolr\ui\WPSOLR_UI_Facets;
use wpsolr\utilities\WPSOLR_Global;

/**
 * WPSOLR Widget Sort List
 */
class WPSOLR_Widget_Filter extends WPSOLR_Widget {

	protected static $wpsolr_layouts = [
		self::TYPE_GROUP_LAYOUT         => [
			self::GENERIC_LAYOUT_ID => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'List',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/filters/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/filters/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/filters/js.twig'
			]
		],
		self::TYPE_GROUP_ELEMENT_LAYOUT => [
			'checkbox' => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Check boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/filters/checkbox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/filters/checkbox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/filters/checkbox/js.twig'
			],
			'radiobox' => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Radio boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/filters/checkbox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/filters/radiobox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/filters/checkbox/js.twig'
			]
		]
	];

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'wpsolr_widget_filter', // Base ID
			__( 'WPSOLR Filters', 'wpsolr_admin' ), // Name
			array( 'description' => __( 'Display filters selected', 'wpsolr_admin' ) ), // Args
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
				WPSOLR_Options_Facets::FACET_FIELD_FILTER_LAYOUT_ID,
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
			Display a list of elements that a user can sort the Solr results with. Sort options must have been defined
			in WPSOLR admin pages.
		</p>

		<?php
	}

	public function wpsolr_get_groups() {
		return WPSOLR_Global::getOption()->get_facets_groups();
	}


}