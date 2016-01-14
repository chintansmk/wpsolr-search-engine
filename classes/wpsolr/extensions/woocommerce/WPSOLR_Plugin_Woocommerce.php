<?php

namespace wpsolr\extensions\woocommerce;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Woocommerce;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;
use wpsolr\WPSOLR_Filters;

/**
 * Class WPSOLR_Plugin_Woocommerce
 *
 * Manage WooCommerce plugin
 */
class WPSOLR_Plugin_Woocommerce extends WPSOLR_Extensions {

	/**
	 * After constructor.
	 */
	protected function post_constructor() {

		/**
		 * Filters
		 */
		WPSOLR_Service_Wordpress::add_filter( WPSOLR_Filters::WPSOLR_FILTER_POST_CUSTOM_FIELDS, array(
			$this,
			'filter_custom_fields'
		), 10, 2 );

	}

	/**
	 * Display admin form.
	 *
	 * @param array $plugin_parameters Parameters
	 */
	public function output_form( $form_file = null, $plugin_parameters = [ ] ) {

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options' => WPSOLR_Global::getOption()->get_option_plugin_woocommerce(
						[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE => '0' ]
					)
				],
				$plugin_parameters
			)
		);
	}

	/**
	 * Add woo attributes to a custom field with the same name
	 *
	 * @param $custom_fields
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function filter_custom_fields( $custom_fields, $post_id ) {

		if ( ! isset( $custom_fields ) ) {
			$custom_fields = array();
		}

		// Get the product correponding to this post
		$product = WPSOLR_Service_Woocommerce::WC_Product( $post_id );

		if ( ! empty( $product ) ) {
			foreach ( $product->get_attributes() as $attribute ) {

				//$terms = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );

				// Remove the eventual 'pa_' prefix from the attribute name
				$attribute_name = $attribute['name'];
				if ( substr( $attribute_name, 0, 3 ) == 'pa_' ) {
					$attribute_name = substr( $attribute_name, 3, strlen( $attribute_name ) );
				}

				$custom_fields[ $attribute_name ] = explode( ',', $product->get_attribute( $attribute['name'] ) );
			}
		}


		return $custom_fields;
	}


	/**
	 * Return all woo commerce attributes
	 * @return array
	 */
	static function get_attribute_taxonomies() {

		// Standard woo function
		return WPSOLR_Service_Woocommerce::wc_get_attribute_taxonomies();
	}

	/**
	 * Return all woo commerce attributes names (slugs)
	 * @return array
	 */
	static function get_attribute_taxonomy_names() {

		$results = array();

		foreach ( self::get_attribute_taxonomies() as $woo_attribute ) {

			// Add woo attribute terms to custom fields
			array_push( $results, $woo_attribute->attribute_name );
		}

		return $results;
	}

}