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


	public static function pll_register_string( $name, $string, $group, $multiline ) {

		if ( function_exists( '\pll_register_string' ) ) {
			\pll_register_string( $name, $string, $group, $multiline );
		}
	}

	public static function pll__( $string ) {

		if ( function_exists( '\pll__' ) ) {
			return \pll__( $string );
		}

		return $string;
	}

	public static function pll_home_url() {
		if ( function_exists( '\pll_home_url' ) ) {
			return \pll_home_url();
		}
	}

}