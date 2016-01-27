<?php

namespace wpsolr\ui;

use wpsolr\ui\widget\WPSOLR_Widget;
use wpsolr\ui\widget\WPSOLR_Widget_Sort;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Sort data
 *
 */
class WPSOLR_Data_Sort {

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
	public static function get_data( $sort_selected, $sorts_to_display, $sort_default_name, $localization_options ) {

		$results          = array();
		$results['items'] = array();

		if ( count( $sorts_to_display ) ) {

			foreach ( $sorts_to_display as $sort_to_display ) {

				$sort_to_display_name = WPSOLR_Global::getExtensionSorts()->get_sort_name( $sort_to_display );

				$sort = array(
					'id'       => $sort_to_display_name,
					'name'     => WPSOLR_Global::getExtensionSorts()->get_sort_label( $sort_to_display ),
					'selected' => ( $sort_to_display_name === ( ! empty( $sort_selected ) ? $sort_selected : $sort_default_name )
					)
				);

				// Templates
				$sort[ WPSOLR_Widget::LAYOUT_FIELD_TEMPLATE_HTML ] = WPSOLR_Widget_Sort::wpsolr_get_layout_template_html( $sort_to_display[ WPSOLR_Widget::FORM_FIELD_LAYOUT_ID ], WPSOLR_Widget::TYPE_GROUP_ELEMENT_LAYOUT );
				$sort[ WPSOLR_Widget::LAYOUT_FIELD_TEMPLATE_CSS ]  = WPSOLR_Widget_Sort::wpsolr_get_layout_template_css( $sort_to_display[ WPSOLR_Widget::FORM_FIELD_LAYOUT_ID ], WPSOLR_Widget::TYPE_GROUP_ELEMENT_LAYOUT );
				$sort[ WPSOLR_Widget::LAYOUT_FIELD_TEMPLATE_JS ]   = WPSOLR_Widget_Sort::wpsolr_get_layout_template_js( $sort_to_display[ WPSOLR_Widget::FORM_FIELD_LAYOUT_ID ], WPSOLR_Widget::TYPE_GROUP_ELEMENT_LAYOUT );

				array_push( $results['items'], $sort );
			}
		}

		return $results;
	}

}
