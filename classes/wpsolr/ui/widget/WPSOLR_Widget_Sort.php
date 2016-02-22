<?php

namespace wpsolr\ui\widget;

use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\ui\widget;
use wpsolr\ui\WPSOLR_UI_Sort;
use wpsolr\utilities\WPSOLR_Global;

/**
 * WPSOLR Widget Sort List
 */
class WPSOLR_Widget_Sort extends WPSOLR_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'wpsolr_widget_sort', // Base ID
			__( 'WPSOLR Sort List', 'wpsolr_admin' ), // Name
			array( 'description' => __( 'Display Solr sort options', 'wpsolr_admin' ) ), // Args
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
	 * @return WPSOLR_UI_Sort
	 */
	protected function get_ui() {
		return new WPSOLR_UI_Sort();
	}

}