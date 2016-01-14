<?php

namespace wpsolr\extensions\groups;

use Mockery as m;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\WPSOLR_Filters;
use wpsolr\WPSOLR_Unit_Test;

class WPSOLR_Plugin_GroupsTest extends WPSOLR_Unit_Test {

	public function test_create_and_add_filter() {

		// Mocks
		$mock1 = $this->wpsolr_mock_add_action( 'wp_loaded', [ WPSOLR_Global::CLASS, 'action_wp_loaded' ], 10, 1 );
		$mock1 = $this->wpsolr_mock_add_action( WPSOLR_Filters::WPSOLR_ACTION_SOLARIUM_QUERY, m::any(), 10, 1, $mock1 );
		$mock1 = $this->wpsolr_mock_add_filter( WPSOLR_Filters::WPSOLR_FILTER_SOLR_RESULTS_DOCUMENT_GROUPS_INFOS, m::any(), 10, 2, 1, $mock1 );


		// Execute
		$result = WPSOLR_Global::getExtensionGroups();

		// Verification: object is created and of the right type
		$this->assertNotEmpty( $result );
		$this->assertEquals( WPSOLR_Plugin_Groups::CLASS, get_class( $result ) );
	}

	public function test_no_exception_in_form() {

		// Mocks
		$mock1 = $this->wpsolr_mock_get_option( m::any(), m::any() );
		$mock1 = $this->wpsolr_mock_settings_fields( m::any(), $mock1 );
		$mock1 = $this->wpsolr_mock_checked( m::any(), m::any(), m::any(), $mock1 );

		$mock1 = $this->wpsolr_mock_apply_filter( WPSOLR_Filters::WPSOLR_FILTER_BEFORE_GET_OPTION_VALUE, m::any(), m::any(), 2, $mock1 );
		$mock1 = $this->wpsolr_mock_apply_filter( WPSOLR_Filters::WPSOLR_FILTER_AFTER_GET_OPTION_VALUE, m::any(), m::any(), 2, $mock1 );

		// Expect file containing special marker
		$this->wpsolr_expect_end_of_file();

		// Verify the html contains the plugin name
		$this->expectOutputRegex( sprintf( '/%s/', WPSOLR_Extensions::get_option_plugin_name( WPSOLR_Extensions::EXTENSION_GROUPS ) ) );

		WPSOLR_Global::getExtensionGroups()->output_form();
	}

}
