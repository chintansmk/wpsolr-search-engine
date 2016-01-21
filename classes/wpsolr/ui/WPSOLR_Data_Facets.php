<?php

namespace wpsolr\ui;

use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\solr\WPSOLR_Field_Types;
use wpsolr\ui\widget\WPSOLR_Widget;
use wpsolr\ui\widget\WPSOLR_Widget_Facet;
use wpsolr\utilities\WPSOLR_Regexp;
use wpsolr\WPSOLR_Filters;

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

		$results                                = array();
		$results['facets']                      = array();
		$results['has_facet_elements_selected'] = false;

		if ( count( $facets_in_results ) && count( $facets_to_display ) ) {

			foreach ( $facets_to_display as $facet_to_display_id => $facet_to_display ) {

				if ( isset( $facets_in_results[ $facet_to_display_id ] ) && count( $facets_in_results[ $facet_to_display_id ] ) > 0 ) {

					// Remove the ending "_str"
					$facet_to_display_id_without_str = WPSOLR_Regexp::remove_string_at_the_end( $facet_to_display_id, WPSOLR_Field_Types::SOLR_TYPE_STRING );

					// Give plugins a chance to change the facet name (ACF).
					$facet_to_display_name = WPSOLR_Service_Wordpress::apply_filters( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_FACET_NAME, $facet_to_display_id_without_str, null );

					$facet_to_display_name = str_replace( '_', ' ', $facet_to_display_name );
					$facet_to_display_name = ucfirst( $facet_to_display_name );

					$facet               = array();
					$facet['items']      = array();
					$facet['id']         = $facet_to_display_id;
					$facet['name']       = $facet_to_display_name;
					$facet['definition'] = $facet_to_display;

					// Templates
					$facet[ WPSOLR_Widget_Facet::LAYOUT_FIELD_TEMPLATE_HTML ] = WPSOLR_Widget_Facet::wpsolr_get_layout_template_html( $facet_to_display[ WPSOLR_Widget_Facet::FORM_FIELD_LAYOUT_ID ], WPSOLR_Widget::TYPE_GROUP_ELEMENT_LAYOUT );
					$facet[ WPSOLR_Widget_Facet::LAYOUT_FIELD_TEMPLATE_CSS ]  = WPSOLR_Widget_Facet::wpsolr_get_layout_template_css( $facet_to_display[ WPSOLR_Widget_Facet::FORM_FIELD_LAYOUT_ID ], WPSOLR_Widget::TYPE_GROUP_ELEMENT_LAYOUT );
					$facet[ WPSOLR_Widget_Facet::LAYOUT_FIELD_TEMPLATE_JS ]   = WPSOLR_Widget_Facet::wpsolr_get_layout_template_js( $facet_to_display[ WPSOLR_Widget_Facet::FORM_FIELD_LAYOUT_ID ], WPSOLR_Widget::TYPE_GROUP_ELEMENT_LAYOUT );

					foreach ( $facets_in_results[ $facet_to_display_id ] as $facet_in_results ) {

						// Current item selected ?
						$item_selected = isset( $facets_selected[ $facet_to_display_id ] ) && ( in_array( $facet_in_results[0], $facets_selected[ $facet_to_display_id ] ) );

						// Update, once, $results['has_facet_elements_selected'], if current element is selected
						if ( $item_selected && ! $results['has_facet_elements_selected'] ) {
							$results['has_facet_elements_selected'] = true;
						}


						$name = trim( $facet_in_results[0] );
						if ( ! empty( $name ) || $name === '0' ) { // Only add facet if non blank name (it happens). '0' is authorized for ranges.

							array_push( $facet['items'], array(
								'name'     => $facet_in_results[0],
								'count'    => $facet_in_results[1],
								'selected' => $item_selected
							) );

						}

					}

					// Add current facet to results, if not empty
					if ( ! empty( $facet['items'] ) ) {
						array_push( $results['facets'], $facet );
					}
				}

			}

		}

		return $results;
	}

}
