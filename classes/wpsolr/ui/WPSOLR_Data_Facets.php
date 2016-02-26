<?php

namespace wpsolr\ui;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\solr\WPSOLR_Field_Types;
use wpsolr\solr\WPSOLR_Schema;
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
	 * Extract facets data from Solr results
	 *
	 * @param $attributes
	 * ['group_id' => '123', 'group_facets_type' => 'xyz']
	 *
	 * @return array
	 * @throws WPSOLR_Exception
	 */
	public static function extract_data( $component_id ) {

		// Widget can be on a search page ?s=
		$wpsolr_query = WPSOLR_Global::getQuery();
		$group_id     = $wpsolr_query->get_wpsolr_facets_groups_id();

		if ( ! $wpsolr_query->get_wpsolr_is_search() ) {

			// Facets of the group on the query url
			if ( empty( $group_id ) ) {

				// Facets group of the widget
				$group_id = $component_id;
				if ( empty( $group_id ) ) {
					throw new WPSOLR_Exception( sprintf( 'Select a facets group.' ) );
				}

				$wpsolr_query->set_wpsolr_facets_groups_id( $group_id );
			}
			// Facets of the facets groups
			$facets = WPSOLR_Global::getExtensionFacets()->get_facets_from_group( $group_id );

			$wpsolr_query->set_wpsolr_facets_fields( $facets );

			// Add Solr query fields from the Widget filter
			$wpsolr_query->set_wpsolr_facets_group_filter_query( WPSOLR_Global::getExtensionFacets()->get_facets_group_filter_query( $group_id ) );
		} else {

			// Facets of the group on the query url for a search url
			$facets = WPSOLR_Global::getExtensionFacets()->get_facets_from_group( $group_id );
		}

		// Call and get Solr results
		$results = WPSOLR_Global::getSolrClient()->display_results( $wpsolr_query );

		$data = static::format_data(
			WPSOLR_Global::getQuery()->get_filter_query_fields_group_by_name( $component_id ),
			$facets,
			$results[1] );


		return [ 'group_id' => $group_id, 'data' => $data ];
	}

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
	public static function format_data( $facets_selected, $facets_to_display, $facets_in_results ) {

		$results                                = [ ];
		$results['facets']                      = [ ];
		$results['has_facet_elements_selected'] = false;

		if ( count( $facets_in_results ) && count( $facets_to_display ) ) {

			$extension_facets  = WPSOLR_Global::getExtensionFacets();
			$extension_layouts = WPSOLR_Global::getExtensionLayouts();

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

						case WPSOLR_Options_Facets::FACET_TYPE_CUSTOM_RANGE:
							$facet_query_custom_ranges = $extension_facets->get_facet_custom_ranges( $facet_to_display );
							break;

						case WPSOLR_Options_Facets::FACET_TYPE_MIN_MAX:
							$facet_label_other = $extension_facets->get_facet_label( $facet_to_display );
							break;

						default:
							$facet_label_other = $extension_facets->get_facet_label( $facet_to_display );
							$facet_label_first = $extension_facets->get_facet_label_first( $facet_to_display );
							$facet_label_last  = $extension_facets->get_facet_label_last( $facet_to_display );
							break;

					}

					$facet               = [ ];
					$facet['items']      = [ ];
					$facet['id']         = $facet_to_display_id;
					$facet['name']       = apply_filters( WPSOLR_Filters::WPSOLR_FILTER_TRANSLATION_STRING, $extension_facets->get_facet_label_front_end( $facet_to_display ) );
					$facet['definition'] = $facet_to_display;

					// Facet templates or facet filter templates
					$layout_type = static::get_layout_type();

					$layout                                                      = $extension_layouts->get_layout_from_type_and_id( $layout_type, $facet_to_display[ static::get_layout_field_name() ] );
					$facet[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_HTML ] = $extension_layouts->get_layout_template_html( $layout );
					$facet[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_CSS ]  = $extension_layouts->get_layout_template_css( $layout );
					$facet[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_JS ]   = $extension_layouts->get_layout_template_js( $layout );

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


							$facet_value = $facet_in_results[0];
							switch ( $facet_to_display_type ) {

								case WPSOLR_Options_Facets::FACET_TYPE_FIELD:
									// Add quotes to prevent Solr special caracters error
									$facet_value = "\"$facet_value\"";
									break;
							}

							$facet_label_expanded = apply_filters( WPSOLR_Filters::WPSOLR_FILTER_TRANSLATION_STRING, $facet_label );


							switch ( $facet_to_display_type ) {

								case WPSOLR_Options_Facets::FACET_TYPE_FIELD:
									// Replace label pattern with values

									if ( WPSOLR_Schema::_FIELD_NAME_CATEGORIES === $facet_to_display_id ) {

										// Retrieve term
										$term = \get_term_by( 'slug', $facet_in_results[0], 'category' );

										$facet_label_expanded = @sprintf(
											$facet_label_expanded,
											$term->name,
											$count );


									} else {

										$facet_label_expanded = @sprintf(
											$facet_label_expanded,
											is_numeric( $facet_in_results[0] ) ? number_format_i18n( $facet_in_results[0] ) : $facet_in_results[0],
											$count );

									}


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

							// For Facet filters, only keep selected items
							if ( static::discard_unselected_items() && ! $item_selected ) {
								continue;
							}

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
											'name'     => $facet_value,
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

	/**
	 * Layout type ?
	 *
	 * @return string
	 */
	protected static function get_layout_type() {
		return WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET;
	}

	/**
	 * Discard items not selected ?
	 *
	 * @return bool
	 */
	protected static function discard_unselected_items() {
		return false;
	}

	protected static function get_layout_field_name() {
		return WPSOLR_Options_Layouts::LAYOUT_FIELD_LAYOUT_ID;
	}

}
