<?php

namespace wpsolr\ui;

use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\solr\WPSOLR_Field_Types;
use wpsolr\ui\widget\WPSOLR_Widget;
use wpsolr\ui\widget\WPSOLR_Widget_Facet;
use wpsolr\utilities\WPSOLR_Global;
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

			$extension_facets = WPSOLR_Global::getExtensionFacets();

			foreach ( $facets_to_display as $facet_to_display_id => $facet_to_display ) {

				if ( isset( $facets_in_results[ $facet_to_display_id ] ) && count( $facets_in_results[ $facet_to_display_id ] ) > 0 ) {

					$facet_to_display_type = $extension_facets->get_facet_type( $facet_to_display );

					$facet_min_count = $extension_facets->get_facet_min_count( $facet_to_display );

					// Remove the ending "_str"
					$facet_to_display_id_without_str = WPSOLR_Regexp::remove_string_at_the_end( $facet_to_display_id, WPSOLR_Field_Types::SOLR_TYPE_STRING );

					// Give plugins a chance to change the facet name (ACF).
					$facet_to_display_name = WPSOLR_Service_Wordpress::apply_filters( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_FACET_NAME, $facet_to_display_id_without_str, null );

					$facet_to_display_name = str_replace( '_', ' ', $facet_to_display_name );
					$facet_to_display_name = ucfirst( $facet_to_display_name );

					$facet_label = '';
					switch ( $facet_to_display_type ) {

						case WPSOLR_Options_Facets::FACET_TYPE_FIELD:
						case WPSOLR_Options_Facets::FACET_TYPE_RANGE:
							$facet_label_other = $extension_facets->get_facet_label( $facet_to_display );
							$facet_label_first = $extension_facets->get_facet_label_first( $facet_to_display );
							$facet_label_last  = $extension_facets->get_facet_label_last( $facet_to_display );
							break;

						case WPSOLR_Options_Facets::FACET_TYPE_CUSTOM_RANGE:
							$facet_query_custom_ranges = $extension_facets->get_facet_custom_ranges( $facet_to_display );
							break;

						case WPSOLR_Options_Facets::FACET_TYPE_MIN_MAX:
							$facet_label_other = $extension_facets->get_facet_label( $facet_to_display );
							break;
					}

					$facet               = array();
					$facet['items']      = array();
					$facet['id']         = $facet_to_display_id;
					$facet['name']       = $facet_to_display_name;
					$facet['definition'] = $facet_to_display;

					// Templates
					$facet[ WPSOLR_Widget::LAYOUT_FIELD_TEMPLATE_HTML ] = WPSOLR_Widget_Facet::wpsolr_get_layout_template_html( $facet_to_display[ WPSOLR_Widget_Facet::FORM_FIELD_LAYOUT_ID ], WPSOLR_Widget::TYPE_GROUP_ELEMENT_LAYOUT );
					$facet[ WPSOLR_Widget::LAYOUT_FIELD_TEMPLATE_CSS ]  = WPSOLR_Widget_Facet::wpsolr_get_layout_template_css( $facet_to_display[ WPSOLR_Widget_Facet::FORM_FIELD_LAYOUT_ID ], WPSOLR_Widget::TYPE_GROUP_ELEMENT_LAYOUT );
					$facet[ WPSOLR_Widget::LAYOUT_FIELD_TEMPLATE_JS ]   = WPSOLR_Widget_Facet::wpsolr_get_layout_template_js( $facet_to_display[ WPSOLR_Widget_Facet::FORM_FIELD_LAYOUT_ID ], WPSOLR_Widget::TYPE_GROUP_ELEMENT_LAYOUT );

					$loop      = 0;
					$nb_facets = count( $facets_in_results[ $facet_to_display_id ] );
					foreach ( $facets_in_results[ $facet_to_display_id ] as $facet_in_results ) {

						$loop ++;

						$count = $facet_in_results[1];

						if ( $count >= $facet_min_count ) { // Do not display facets under min count

							switch ( $facet_to_display_type ) {

								case WPSOLR_Options_Facets::FACET_TYPE_FIELD:
								case WPSOLR_Options_Facets::FACET_TYPE_RANGE:
									// Which facet template to use depends on current loop
									$facet_label = $facet_label_other;
									switch ( $loop ) {
										case 1:
											$facet_label = $facet_label_first;
											break;
										case $nb_facets:
											$facet_label = $facet_label_last;
											break;
									}
									break;

								case WPSOLR_Options_Facets::FACET_TYPE_CUSTOM_RANGE:
									$facet_label = $facet_query_custom_ranges[ $loop - 1 ][ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_LABEL ];
									break;

								case WPSOLR_Options_Facets::FACET_TYPE_MIN_MAX:
									$facet_label = $facet_label_other;
									break;
							}


							$facet_value          = $facet_in_results[0];
							$facet_label_expanded = apply_filters( WPSOLR_Filters::WPSOLR_FILTER_TRANSLATION_STRING, $facet_label );


							switch ( $facet_to_display_type ) {

								case WPSOLR_Options_Facets::FACET_TYPE_FIELD:
									// Replace label pattern with values
									$facet_label_expanded = @sprintf(
										$facet_label_expanded,
										is_numeric( $facet_in_results[0] ) ? number_format_i18n( $facet_in_results[0] ) : $facet_in_results[0],
										$count );

									break;

								case WPSOLR_Options_Facets::FACET_TYPE_RANGE:
									$range_inf = $facet_value;
									$range_sup = $facet_value + $facet['definition']['range']['gap'] - 1;

									// Facet range come as [10 TO 19]
									$facet_value = @sprintf( '[%s TO %s]', $range_inf, $range_sup );

									// Replace label pattern with values
									$facet_label_expanded = @sprintf(
										$facet_label_expanded,
										is_numeric( $range_inf ) ? number_format_i18n( $range_inf ) : $range_inf,
										is_numeric( $range_sup ) ? number_format_i18n( $range_sup ) : $range_sup,
										$count );

									break;

								case WPSOLR_Options_Facets::FACET_TYPE_CUSTOM_RANGE:
									$range_inf = $facet_query_custom_ranges[ $loop - 1 ][ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_INF ];
									$range_sup = $facet_query_custom_ranges[ $loop - 1 ][ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_SUP ];

									// Facet range come as [10 TO 19]
									$facet_value = @sprintf( '[%s TO %s]', $range_inf, $range_sup );

									// Replace label pattern with values
									$facet_label_expanded = @sprintf(
										$facet_label_expanded,
										is_numeric( $range_inf ) ? number_format_i18n( $range_inf ) : $range_inf,
										is_numeric( $range_sup ) ? number_format_i18n( $range_sup ) : $range_sup,
										$count );

									break;

								case WPSOLR_Options_Facets::FACET_TYPE_MIN_MAX:

									// [10 TO 19]
									$facet_values = ! empty( $facets_selected[ $facet_to_display_id ] )
										? WPSOLR_Regexp::extract_filter_range_values( $facets_selected[ $facet_to_display_id ][0] )
										: null;

									$range_inf = ( ! empty( $facet_values ) ? $facet_values[0] : $facet_in_results[0] );
									$range_sup = ( ! empty( $facet_values ) ? $facet_values[1] : $facet_in_results[1] );

									// Replace label pattern with values
									$facet_label_expanded = @sprintf(
										$facet_label_expanded,
										number_format_i18n( $range_inf ),
										number_format_i18n( $range_sup ),
										number_format_i18n( $facet_in_results[2] )
									);
									break;
							}


							// Current item selected ?
							$item_selected = isset( $facets_selected[ $facet_to_display_id ] ) && ( in_array( $facet_value, $facets_selected[ $facet_to_display_id ] ) );

							// Update, once, $results['has_facet_elements_selected'], if current element is selected
							if ( $item_selected && ! $results['has_facet_elements_selected'] ) {
								$results['has_facet_elements_selected'] = true;
							}

							$name = trim( $facet_in_results[0] );
							if ( ! empty( $name ) || $name === '0' ) { // Only add facet if non blank name (it happens). '0' is authorized for ranges.

								switch ( $facet_to_display_type ) {
									case WPSOLR_Options_Facets::FACET_TYPE_RANGE:
									case WPSOLR_Options_Facets::FACET_TYPE_CUSTOM_RANGE:
										array_push( $facet['items'], array(
											'label'     => $facet_label_expanded,
											'range_inf' => $range_inf,
											'range_sup' => $range_sup,
											'count'     => $facet_in_results[1],
											'selected'  => $item_selected
										) );

										break;

									case WPSOLR_Options_Facets::FACET_TYPE_FIELD:
										array_push( $facet['items'], array(
											'label'    => $facet_label_expanded,
											'name'     => $facet_in_results[0],
											'count'    => $facet_in_results[1],
											'selected' => $item_selected
										) );
										break;

									case WPSOLR_Options_Facets::FACET_TYPE_MIN_MAX:
										array_push( $facet['items'], array(
											'label'     => $facet_label_expanded,
											'min_value' => 0 + $range_inf, // convert to numeric
											'max_value' => 0 + $range_sup, // convert to numeric
											'step'      => 0 + $extension_facets->get_facet_min_max_step( $facet_to_display ),
											'count'     => $facet_in_results[2],
											'selected'  => $item_selected
										) );
										break;

								}

							}
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
