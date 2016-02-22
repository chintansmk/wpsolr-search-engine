<?php

namespace wpsolr\ui\widget;

use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\ui\widget;
use wpsolr\ui\WPSOLR_UI_Facet;
use wpsolr\ui\WPSOLR_UI_Filter;
use wpsolr\utilities\WPSOLR_Global;

/**
 * WPSOLR Widget Sort List
 */
class WPSOLR_Widget_Filter extends WPSOLR_Widget_facet {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		WPSOLR_Widget::__construct(
			'wpsolr_widget_filter', // Base ID
			__( 'WPSOLR Filters', 'wpsolr_admin' ), // Name
			array( 'description' => __( 'Display filters selected', 'wpsolr_admin' ) ), // Args
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
			Display a list of elements that a user can sort the Solr results with. Sort options must have been defined
			in WPSOLR admin pages.
		</p>

		<?php
	}

	/**
	 * Returns the UI object
	 *
	 * @return WPSOLR_UI_Facet
	 */
	protected function get_ui() {
		return new WPSOLR_UI_Filter();
	}

}