<?php

namespace wpsolr\services;

/**
 * Polylang functions mocked, to enable phpunit testing
 */
class WPSOLR_Service_Polylang {

	public static function pll_languages_list( $format ) {

		if ( function_exists( '\pll_languages_list' ) ) {
			return \pll_languages_list( $format );
		}

		return null;
	}

}