<?php

namespace wpsolr\services;

/**
 * WP Global functions mocked, to enable phpunit testing
 */
class WPSOLR_Service_Wordpress {

	public static function apply_filters( $tag, $value, $var ) {
		return apply_filters( $tag, $value, $var );
	}

	public static function add_action( $string, $array ) {
		add_action( $string, $array );
	}

	public static function add_filter( $tag, $function_to_add, $priority, $accepted_args ) {
		add_filter( $tag, $function_to_add, $priority, $accepted_args );
	}

	public static function get_option( $option_name, $default_value = null ) {
		return get_option( $option_name, $default_value );
	}

	public static function settings_fields( $options_name ) {
		settings_fields( $options_name );
	}

	public static function checked( $checked, $current = true, $echo = true ) {
		return checked( $checked, $current, $echo );
	}

}