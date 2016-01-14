<?php

namespace wpsolr\services;

/**
 * Woocommerce functions mocked, to enable phpunit testing
 */
class WPSOLR_Service_Woocommerce {

	public static function WC_Product( $post_id ) {

		if ( class_exists( '\WC_Product' ) ) {
			return new \WC_Product( $post_id );
		}

		return null;
	}

	public static function wc_get_attribute_taxonomies() {

		if ( function_exists( '\wc_get_attribute_taxonomies' ) ) {
			return \wc_get_attribute_taxonomies();
		}

		return null;
	}

}