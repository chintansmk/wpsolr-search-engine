<?php

namespace wpsolr\ui;

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
					'name'     => WPSOLR_Global::getExtensionSorts()->get_sort_label_asc( $sort_to_display ),
					'selected' => ( $sort_to_display_name === ( ! empty( $sort_selected ) ? $sort_selected : $sort_default_name )
					)
				);

				array_push( $results['items'], $sort );
			}
		}

		return $results;
	}

}
