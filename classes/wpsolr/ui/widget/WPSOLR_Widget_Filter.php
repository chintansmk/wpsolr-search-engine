<?php

namespace wpsolr\ui\widget;

use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\ui\widget;
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

	protected static function wpsolr_get_layouts() {
		return WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET_FILTER_GROUP );
	}

	public function wpsolr_get_groups() {
		return WPSOLR_Global::getOption()->get_facets_groups();
	}

	protected function wpsolr_get_facet_type() {
		return WPSOLR_Options_Facets::FACET_FIELD_FILTER_LAYOUT_ID;
	}

}