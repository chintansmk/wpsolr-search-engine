<?php

namespace wpsolr\ui\shortcode;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Shortcode_Facet
 * Display facets shorcode
 * @package wpsolr\ui\shortcode
 */
class WPSOLR_Shortcode_Facet extends WPSOLR_Shortcode {

	// Shortcode name
	const SHORTCODE_NAME = 'wpsolr_shortcode_facet';

	// Shorcode attributes
	const ATTRIBUTE_GROUP_ID = 'group_id';
	const ATTRIBUTE_GROUP_LAYOUT_ID = 'layout_id';

	public static function output( $attributes, $content = "" ) {

		try {
			$attribute_group_id = ! empty( $attributes[ self::ATTRIBUTE_GROUP_ID ] ) ? $attributes[ self::ATTRIBUTE_GROUP_ID ] : '';
			$attribute_group    = self::get_group( $attribute_group_id );

			$attribute_group_layout_id = ! empty( $attributes[ self::ATTRIBUTE_GROUP_LAYOUT_ID ] ) ? $attributes[ self::ATTRIBUTE_GROUP_LAYOUT_ID ] : '';
			$attribute_group_layout    = self::get_layout( WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET_GROUP, $attribute_group_layout_id );


			/*
				// Get data
				$data = self::get_data( $group_id );

				// Build the facets UI
				return WPSOLR_UI_Facets::Build(
					$data['group_id'],
					$data['data'],
					WPSOLR_Localization::get_options(),
					null,
					null,
					$this->wpsolr_get_instance_layout( $instance, self::TYPE_GROUP_LAYOUT )
				);
				*/

			return 'hello there !!';

		} catch ( WPSOLR_Exception $e ) {

			// Display error
			return $e->get_message();
		}

	}

	protected static function get_group_child( $group_id ) {

		return WPSOLR_Global::getExtensionFacets()->get_facets_group( $group_id );
	}


	protected static function get_data( $shortcode_group_id ) {

		// Widget can be on a search page ?s=
		$wpsolr_query = WPSOLR_Global::getQuery();
		$group_id     = $wpsolr_query->get_wpsolr_facets_groups_id();

		if ( ! $wpsolr_query->get_wpsolr_is_search() ) {

			// Facets of the group on the query url
			if ( empty( $group_id ) ) {

				// Facets group of the widget
				$group_id = $shortcode_group_id;
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

		$data = WPSOLR_Data_Facets::get_data(
			self::get_facet_type(),
			WPSOLR_Global::getQuery()->get_filter_query_fields_group_by_name(),
			$facets,
			$results[1] );


		return [ self::ATTRIBUTE_GROUP_ID => $group_id, 'data' => $data ];
	}

	protected static function get_facet_type() {
		return WPSOLR_Options_Facets::FACET_FIELD_FACET_LAYOUT_ID;
	}

}