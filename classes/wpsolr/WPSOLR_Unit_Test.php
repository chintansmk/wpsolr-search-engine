<?php

namespace wpsolr;

use Mockery as m;
use wpsolr\services\WPSOLR_Service_Wordpress;

/**
 * Top level unit test class
 * @package wpsolr
 */
abstract class WPSOLR_Unit_Test extends \PHPUnit_Framework_TestCase {

	public function tearDown() {
		m::close();
	}


	/**
	 * Mock WP function apply_filters
	 */
	protected function wpsolr_mock_apply_filter( $tag, $value, $var, $times, $mock = null ) {

		if ( empty( $mock ) ) {
			$mock = m::mock( 'alias:' . WPSOLR_Service_Wordpress::CLASS );
		}

		$mock->shouldReceive( 'apply_filters' )
		     ->with( $tag, $value, $var )
		     ->andReturnUsing(
			     function ( $tag, $value, $var ) {
				     return $value;
			     } )
		     ->times( $times );

		return $mock;
	}

	/**
	 * Mock WP function apply_filters
	 */
	protected function wpsolr_mock_add_action( $tag, $value, $priority, $accepted_args, $mock = null ) {

		if ( empty( $mock ) ) {
			$mock = m::mock( 'alias:' . WPSOLR_Service_Wordpress::CLASS );
		}

		$mock->shouldReceive( 'add_action' )
		     ->with( $tag, $value, $priority, $accepted_args );

		return $mock;
	}

	/**
	 * Mock WP function apply_filters
	 */
	protected function wpsolr_mock_add_filter( $tag, $function_to_add, $priority, $accepted_args, $times, $mock = null ) {

		if ( empty( $mock ) ) {
			$mock = m::mock( 'alias:' . WPSOLR_Service_Wordpress::CLASS );
		}

		$mock->shouldReceive( 'add_filter' )
		     ->with( $tag, $function_to_add, $priority, $accepted_args )
		     ->times( $times );

		return $mock;
	}

	protected function wpsolr_mock_get_option( $option_name, $default_value, $mock = null ) {

		if ( empty( $mock ) ) {
			$mock = m::mock( 'alias:' . WPSOLR_Service_Wordpress::CLASS );
		}

		$mock->shouldReceive( 'get_option' )
		     ->with( $option_name, $default_value );

		return $mock;
	}

	protected function wpsolr_expect_end_of_file() {
		$this->expectOutputRegex( '/<!-- WPSOLR_END -->/' );
	}

	protected function wpsolr_mock_settings_fields( $options_name, $mock ) {

		if ( empty( $mock ) ) {
			$mock = m::mock( 'alias:' . WPSOLR_Service_Wordpress::CLASS );
		}

		$mock->shouldReceive( 'settings_fields' )
		     ->with( $options_name );

		return $mock;
	}

	protected function wpsolr_mock_checked( $checked, $current = true, $echo = true, $mock ) {

		if ( empty( $mock ) ) {
			$mock = m::mock( 'alias:' . WPSOLR_Service_Wordpress::CLASS );
		}

		$mock->shouldReceive( 'checked' )
		     ->with( $checked, $current, $echo );

		return $mock;
	}

}