<?php

/**
 * Facets data
 *
 * Class WPSOLR_Data_Facets
 */
class WPSOLR_Data_Facets {

	/**
	 * @param $facets_selected
	 * @param $facets_to_display
	 * @param $facets_in_results
	 *
	 * @return array    [
	 *                  {"items":[{"name":"post","count":5,"selected":true}],"id":"type","name":"Type"},
	 *                  {"items":[{"name":"admin","count":6,"selected":false}],"id":"author","name":"Author"},
	 *                  {"items":[{"name":"Blog","count":13,"selected":true}],"id":"categories","name":"Categories"}
	 *                  ]
	 */
	public static function get_data( $facets_selected, $facets_to_display, $facets_in_results ) {

		$results = array();

		if ( count( $facets_in_results ) && count( $facets_to_display ) ) {

			foreach ( $facets_to_display as $facet_to_display_id ) {

				if ( isset( $facets_in_results[ $facet_to_display_id ] ) && count( $facets_in_results[ $facet_to_display_id ] ) > 0 ) {

					$facet_with_no_blank_id = strtolower( str_replace( ' ', '_', $facet_to_display_id ) );

					// Remove the ending "_str"
					$facet_to_display_id_without_str = preg_replace( '/_str$/', '', $facet_to_display_id );

					// Give plugins a chance to change the facet name (ACF).
					$facet_to_display_name = apply_filters( WpSolrFilters::WPSOLR_FILTER_SEARCH_PAGE_FACET_NAME, $facet_to_display_id_without_str );

					$facet_to_display_name = str_replace( '_', ' ', $facet_to_display_name );
					$facet_to_display_name = ucfirst( $facet_to_display_name );

					$facet          = array();
					$facet['items'] = array();
					$facet['id']    = $facet_to_display_id;
					$facet['name']  = $facet_to_display_name;

					$items_hierachy = array();
					self::buildHierarchies( $items_hierachy, $facets_in_results[ $facet_to_display_id ],
						! empty( $facets_selected[ $facet_with_no_blank_id ] ) ? $facets_selected[ $facet_with_no_blank_id ] : array() );

					foreach ( $items_hierachy as $facet_in_results ) {

                    //if($facet_in_results['value'] == "video-album"){
                    //    $facet_in_results['value'] = "Video";
                    //}
                    //if($facet_in_results['value'] == "granth"){
                    //    $facet_in_results['value'] = "Books";
                    //}
                    //if($facet_in_results['value'] == "product"){
                    //    $facet_in_results['value'] = "Other";
                    //}
                    //
                    //$facet_in_results['value'] = ucwords($facet_in_results['value']);

						array_push( $facet['items'], array(
							'value'    => $facet_in_results['value'],
							'count'    => $facet_in_results['count'],
							'items'    => $facet_in_results['items'],
							'selected' => $facet_in_results['selected']
						) );

					}

					// Add current facet to results
					array_push( $results, $facet );
				}

			}

		}

		return $results;
	}


	/**
	 * Build a hierachy of facets when facet name contains WpSolrSchema::FACET_HIERARCHY_SEPARATOR
	 * Recursive
	 *
	 * @param $results
	 * @param $items
	 */
	public static function buildHierarchies( &$results, $items, $facets_selected ) {

		$result = array();
		foreach ( $items as $item ) {

			$item_hierarcy_item_names = explode( WpSolrSchema::FACET_HIERARCHY_SEPARATOR, $item['value'] );
			$item_top_level_name      = array_shift( $item_hierarcy_item_names );

			if ( empty( $result[ $item_top_level_name ] ) ) {
				$result[ $item_top_level_name ]          = array(
					'value'    => $item_top_level_name,
					'count'    => $item['count'],
					'selected' => isset( $facets_selected ) && ( in_array( $item_top_level_name, $facets_selected, true ) )
				);
				$result[ $item_top_level_name ]['items'] = array();
			}

			if ( ! empty( $item_hierarcy_item_names ) ) {

				array_push( $result[ $item_top_level_name ]['items'],
					array(
						'value'    => implode( WpSolrSchema::FACET_HIERARCHY_SEPARATOR, $item_hierarcy_item_names ),
						'count'    => $item['count'],
						'selected' => isset( $facets_selected ) && ( in_array( $item_top_level_name, $facets_selected, true ) )
					)
				);
			}
		}


		foreach ( $result as $top_name => $sub_items ) {

			$level = array(
				'value'    => $sub_items['value'],
				'count'    => $sub_items['count'],
				'selected' => $sub_items['selected'],
				'items'    => array()
			);

			if ( ! empty( $sub_items['items'] ) ) {

				self::buildHierarchies( $level['items'], $sub_items['items'], $facets_selected );
			}

			// Calculate the count by summing children count
			if ( ! empty( $level['items'] ) ) {

				$count = 0;
				foreach ( $level['items'] as $item ) {

					$count += $item['count'];
				}
				$level['count'] = $count;
			}

			array_push( $results, $level );
		}

	}

}
