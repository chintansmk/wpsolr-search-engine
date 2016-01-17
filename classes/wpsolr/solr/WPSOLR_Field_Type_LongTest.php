<?php

namespace classes\wpsolr\solr;

use wpsolr\utilities\WPSOLR_Global;
use wpsolr\WPSOLR_Unit_Test;

class WPSOLR_Field_Type_LongTest extends WPSOLR_Unit_Test {

	public function testBlank() {

		$this->assertEquals( '', WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '', [ 'solr_type' => 'long' ] ) );
	}

	public function testNull() {

		$this->assertEquals( null, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', null, [ 'solr_type' => 'long' ] ) );
	}

	public function testLong() {

		$this->assertEquals( 99999999, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 99999999, [ 'solr_type' => 'long' ] ) );
		$this->assertEquals( 9999999999999999999999999999999999999, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '9999999999999999999999999999999999999', [ 'solr_type' => 'long' ] ) );
		$this->assertEquals( 1.0E15, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 1.0E15, [ 'solr_type' => 'long' ] ) );
		$this->assertEquals( '1.0E15', WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '1.0E15', [ 'solr_type' => 'long' ] ) );
	}

	/**
	 * @expectedException Exception
	 */
	public function testString() {

		$this->assertEquals( 10, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 'not an integer', [ 'solr_type' => 'long' ] ) );
	}

	/**
	 * @expectedException Exception
	 */
	public function testFloat() {

		$this->assertEquals( 10.50, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 10.50, [ 'solr_type' => 'long' ] ) );
	}

}