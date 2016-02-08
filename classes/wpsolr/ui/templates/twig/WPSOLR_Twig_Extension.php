<?php
/**
 * Twig extension
 *
 */

namespace wpsolr\ui\templates\twig;


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
			'facet_id'   => $facet['id'],
			'facet_type' => $facet['definition']['type']
		];

		switch ( $facet['definition']['type'] ) {
			case 'facet_range':
				$result['facet_value'] = $item['range_inf'];
				$result['range_sup']   = $item['range_sup'];
				break;

			default:
				$result['facet_value'] = $item['name'];
				break;
		}

		return $result;
	}

}