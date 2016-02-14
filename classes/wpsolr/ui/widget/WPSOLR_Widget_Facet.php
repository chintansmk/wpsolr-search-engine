<?php

namespace wpsolr\ui\widget;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\ui\widget;
use wpsolr\ui\WPSOLR_Data_Facets;
use wpsolr\ui\WPSOLR_UI_Facets;
use wpsolr\utilities\WPSOLR_Global;

/**
 * WPSOLR Widget Facets.
 */
class WPSOLR_Widget_Facet extends WPSOLR_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		WPSOLR_Widget::__construct(
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

		// Get data
		$data = $this->wpsolr_get_data( $args, $instance );

		// Build the facets UI
		echo WPSOLR_UI_Facets::Build(
			$data['group_id'],
			$data['data'],
			WPSOLR_Localization::get_options(),
			$args,
			$instance,
			$this->wpsolr_get_instance_layout( $instance)
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

	protected static function wpsolr_get_layouts() {
		return WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET_GROUP );
	}

	public function wpsolr_get_groups() {
		return WPSOLR_Global::getOption()->get_facets_groups();
	}

	protected function wpsolr_extract_data( $args, $instance ) {

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
			$wpsolr_query->set_wpsolr_facets_group_filter_query( WPSOLR_Global::getExtensionFacets()->get_facets_group_filter_query( $group_id ) );
		} else {

			// Facets of the group on the query url for a search url
			$facets = WPSOLR_Global::getExtensionFacets()->get_facets_from_group( $group_id );
		}

		// Call and get Solr results
		$results = WPSOLR_Global::getSolrClient()->display_results( $wpsolr_query );

		$data = WPSOLR_Data_Facets::get_data(
			$this->wpsolr_get_facet_type(),
			WPSOLR_Global::getQuery()->get_filter_query_fields_group_by_name(),
			$facets,
			$results[1] );


		return [ 'group_id' => $group_id, 'data' => $data ];
	}

	protected function wpsolr_is_widget_empty( $instance ) {

		return ( ! isset( $this->wpsolr_widget_data ) || empty( $this->wpsolr_widget_data['data'] ) || ( count( $this->wpsolr_widget_data['data'][ WPSOLR_Options_Facets::OPTION_FACETS ] ) == 0 ) );
	}

	protected function wpsolr_get_facet_type() {
		return WPSOLR_Options_Facets::FACET_FIELD_FACET_LAYOUT_ID;
	}

}