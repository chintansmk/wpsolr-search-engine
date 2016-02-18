<?php

namespace wpsolr\ui\widget;

use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\ui\widget;
use wpsolr\ui\WPSOLR_UI_Facet;
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

	/**
	 * Returns the UI object
	 *
	 * @return WPSOLR_UI_Facet
	 */
	protected function get_ui() {
		return new WPSOLR_UI_Facet();
	}

}