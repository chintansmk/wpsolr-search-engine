<?php
/**
 * Twig extension
 *
 */

namespace wpsolr\ui\templates\twig;

use wpsolr\extensions\facets\WPSOLR_Options_Facets;

class WPSOLR_Twig_Extension extends \Twig_Extension {

	public function getName() {
		return 'wpsolr';
	}


	public function getFunctions() {
		return array(
			new \Twig_SimpleFunction( 'wpsolr_create_data_facet', array(
				WPSOLR_Twig_Extension::class,
				'wpsolr_create_data_facet'
			) ),
		);
	}

	/**
	 * Generate HTML5 data used to store information in the DOM.
	 *
	 * @param $facet
	 * @param $item
	 *
	 * @return array
	 */
	public static function wpsolr_create_data_facet( $facet, $item ) {

		$result = [
			'facet_id'          => $facet['id'],
			'facet_type'        => $facet['definition'][ WPSOLR_Options_Facets::FACET_FIELD_TYPE ],
			'facet_delay_in_ms' => $facet['definition'][ WPSOLR_Options_Facets::FACET_FIELD_JS_REFRESH_DELAY_IN_MS ],
			'count'             => $item['count']
		];

		switch ( $facet['definition'][ WPSOLR_Options_Facets::FACET_FIELD_TYPE ] ) {
			case WPSOLR_Options_Facets::FACET_TYPE_RANGE:
			case WPSOLR_Options_Facets::FACET_TYPE_CUSTOM_RANGE:
				$result['facet_value'] = $item[ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_INF ];
				$result['range_sup']   = $item[ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_SUP ];
				break;

			default:
				$result['facet_value'] = $item['name'];
				break;
		}

		return $result;
	}

}