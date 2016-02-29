<?php

namespace wpsolr\ui;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\WPSOLR_Filters;

/**
 * Sort data
 *
 */
class WPSOLR_Data_Sort {

	public static function extract_data( $ui_group_id ) {

		// Widget can be on a search page ?s=
		$wpsolr_query = WPSOLR_Global::getQuery();
		$group_id     = $wpsolr_query->get_wpsolr_sorts_groups_id();

		if ( ! $wpsolr_query->get_wpsolr_is_search() ) {

			// Sorts of the group on the query url
			if ( empty( $group_id ) ) {

				// Sorts group of the widget
				$group_id = $ui_group_id;
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
		$group = WPSOLR_Global::getExtensionSorts()->get_group( $group_id );
		$sorts = WPSOLR_Global::getExtensionSorts()->get_sorts( $group );

		$data = static::format_data(
			WPSOLR_Global::getQuery()->get_wpsolr_sort(),
			$sorts,
			WPSOLR_Global::getExtensionSorts()->get_sort_default_name( $group ),
			WPSOLR_Localization::get_options() );


		return [ 'group_id' => $group_id, 'data' => $data ];
	}

	/**
	 * @param string $sort_selected The sort currently selected.
	 * Exemple: "sort_by_date_desc"|null
	 * @param array $sorts_to_display The sorts to display.
	 * Exemple: ["sort_by_date_asc","sort_by_relevancy_desc","sort_by_date_desc"]
	 * @param string $sort_default_name The sort to select if $sort_selected is empty.
	 * Exemple: 'sort_by_relevancy_desc'
	 *
	 * @return array    ['sorts' => [
	 *                      {"id":"sort_by_date_asc", "name":"Oldest", "selected":false},
	 *                      {"id":"sort_by_relevancy_desc", "name":"More relevant", "selected":true},
	 *                      {"id":"sort_by_date_desc", "name":"Newest", "selected":false},
	 *                      ]
	 *                  ]
	 */
	public static function format_data( $sort_selected, $sorts_to_display, $sort_default_name, $localization_options ) {

		$results          = array();
		$results['items'] = array();

		$extension_layouts = WPSOLR_Global::getExtensionLayouts();

		if ( count( $sorts_to_display ) ) {

			foreach ( $sorts_to_display as $sort_to_display ) {

				$sort_to_display_name = WPSOLR_Global::getExtensionSorts()->get_sort_name( $sort_to_display );

				$sort = array(
					'id'       => $sort_to_display_name,
					'name'     => apply_filters( WPSOLR_Filters::WPSOLR_FILTER_TRANSLATION_STRING, WPSOLR_Global::getExtensionSorts()->get_sort_label( $sort_to_display ) ),
					'selected' => ( $sort_to_display_name === ( ! empty( $sort_selected ) ? $sort_selected : $sort_default_name )
					)
				);

				// Templates
				$layout                                                     = $extension_layouts->get_layout_from_type_and_id( WPSOLR_Options_Layouts::TYPE_LAYOUT_SORT, $sort_to_display[ WPSOLR_Options_Layouts::LAYOUT_FIELD_LAYOUT_ID ] );
				$sort[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_HTML ] = $extension_layouts->get_layout_template_html( $layout );
				$sort[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_CSS ]  = $extension_layouts->get_layout_template_css( $layout );
				$sort[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_JS ]   = $extension_layouts->get_layout_template_js( $layout );

				array_push( $results['items'], $sort );
			}
		}

		return $results;
	}

}
