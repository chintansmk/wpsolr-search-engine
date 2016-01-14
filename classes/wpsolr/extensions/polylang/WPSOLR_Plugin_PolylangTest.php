<?php

namespace wpsolr\extensions\polylang;

use Mockery as m;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Polylang;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\WPSOLR_Filters;
use wpsolr\WPSOLR_Unit_Test;

class WPSOLR_Plugin_PolylangTest extends WPSOLR_Unit_Test {

	public function test_create_and_add_filter() {

		// Mocks
		$mock1 = $this->wpsolr_mock_add_filter( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_SLUG, m::any(), 10, 1, 1, null );
		$mock1 = $this->wpsolr_mock_add_filter( WPSOLR_Filters::WPSOLR_FILTER_SQL_QUERY_STATEMENT, m::any(), 10, 2, 1, $mock1 );
		$mock1 = $this->wpsolr_mock_add_filter( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_GET_DEFAULT_SOLR_INDEX_INDICE, m::any(), 10, 2, 1, $mock1 );
		$mock1 = $this->wpsolr_mock_add_filter( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_URL, m::any(), 10, 2, 1, $mock1 );
		$mock1 = $this->wpsolr_mock_add_filter( WPSOLR_Filters::WPSOLR_FILTER_POST_LANGUAGE, m::any(), 10, 2, 1, $mock1 );

		//m::mock( 'alias:' . WPSOLR_Service_Polylang::CLASS )->shouldReceive( 'pll_languages_list' )->with( m::any() )->times( 1 )->andReturn( [ ] );

		// Execute
		$result = WPSOLR_Global::getExtensionPolylang();

		// Verification: object is created and of the right type
		$this->assertNotEmpty( $result );
		$this->assertEquals( WPSOLR_Plugin_Polylang::CLASS, get_class( $result ) );
	}


	public function test_no_exception_in_form() {

		// Mocks
		$mock1 = $this->wpsolr_mock_get_option( m::any(), m::any() );
		$mock1 = $this->wpsolr_mock_settings_fields( m::any(), $mock1 );
		$mock1 = $this->wpsolr_mock_checked( m::any(), m::any(), m::any(), $mock1 );

		$mock1 = $this->wpsolr_mock_apply_filter( WPSOLR_Filters::WPSOLR_FILTER_BEFORE_GET_OPTION_VALUE, m::any(), m::any(), 1, $mock1 );
		$mock1 = $this->wpsolr_mock_apply_filter( WPSOLR_Filters::WPSOLR_FILTER_AFTER_GET_OPTION_VALUE, m::any(), m::any(), 1, $mock1 );

		//m::mock( 'alias:' . WPSOLR_Service_Polylang::CLASS )->shouldReceive( 'pll_languages_list' )->with( m::any() )->times( 1 )->andReturn( [ ] );

		// Expect file containing special marker
		$this->wpsolr_expect_end_of_file();

		// Verify the html contains the plugin name
		$this->expectOutputRegex( sprintf( '/%s/', WPSOLR_Extensions::get_option_plugin_name( WPSOLR_Extensions::EXTENSION_POLYLANG ) ) );

		WPSOLR_Global::getExtensionPolylang()->output_form();
	}


}
