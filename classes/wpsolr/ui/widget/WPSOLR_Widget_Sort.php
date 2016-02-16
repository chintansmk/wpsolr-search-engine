<?php

namespace wpsolr\ui\widget;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\ui\widget;
use wpsolr\ui\WPSOLR_Data_Sort;
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
		$group_id     = $wpsolr_query->get_wpsolr_sorts_groups_id();

		if ( ! $wpsolr_query->get_wpsolr_is_search() ) {

			// Sorts of the group on the query url
			if ( empty( $group_id ) ) {

				// Sorts group of the widget
				$group_id = $this->wpsolr_get_instance_group_id( $instance );
				if ( empty( $group_id ) ) {
					throw new WPSOLR_Exception( sprintf( 'Select a sort group.' ) );
				}
			}

		} else {

			// No default sort group
			if ( empty( $group_id ) ) {
				throw new WPSOLR_Exception( sprintf( 'Select a default sort group.' ) );
			}

		}

		// Sorts of the Sorts group
		$sorts = WPSOLR_Global::getExtensionSorts()->get_sorts_from_group( $group_id );

		echo WPSOLR_UI_Sort::Build(
			$group_id,
			WPSOLR_Data_Sort::get_data(
				WPSOLR_Global::getQuery()->get_wpsolr_sort(),
				$sorts,
				WPSOLR_Global::getExtensionSorts()->get_sort_default_name( WPSOLR_Global::getExtensionSorts()->get_sorts_group( $group_id ) ),
				WPSOLR_Localization::get_options()
			),
			WPSOLR_Localization::get_options(),
			$args,
			$instance,
			$this->wpsolr_get_instance_layout( $instance )
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
		return WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( WPSOLR_Options_Layouts::TYPE_LAYOUT_SORT_GROUP );
	}

	public function wpsolr_get_groups() {
		return WPSOLR_Global::getOption()->get_sorts_groups();
	}

}