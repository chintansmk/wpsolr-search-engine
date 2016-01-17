<?php

namespace classes\wpsolr\solr;

use wpsolr\utilities\WPSOLR_Global;
use wpsolr\WPSOLR_Unit_Test;

class WPSOLR_Field_Type_IntegerTest extends WPSOLR_Unit_Test {

	public function testBlank() {

		$this->assertEquals( '', WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '', [ 'solr_type' => 'integer' ] ) );
	}

	public function testNull() {

		$this->assertEquals( null, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', null, [ 'solr_type' => 'integer' ] ) );
	}

	public function testInteger() {

		$this->assertEquals( 10, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 10, [ 'solr_type' => 'integer' ] ) );
		$this->assertEquals( 10, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '10', [ 'solr_type' => 'integer' ] ) );
	}

	/**
	 * @expectedException Exception
	 */
	public function testString() {

		$this->assertEquals( 10, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 'not an integer', [ 'solr_type' => 'integer' ] ) );
	}

	/**
	 * @expectedException Exception
	 */
	public function testLong() {

		$this->assertEquals( 999999999999999999999999999, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 999999999999999999999999999, [ 'solr_type' => 'integer' ] ) );
		$this->assertEquals( '999999999999999999999999999', WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '999999999999999999999999999', [ 'solr_type' => 'integer' ] ) );
	}

	/**
	 * @expectedException Exception
	 */
	public function testFloat() {

		$this->assertEquals( 1.2, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 1.2, [ 'solr_type' => 'integer' ] ) );
		$this->assertEquals( '1.2', WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '1.2', [ 'solr_type' => 'integer' ] ) );
	}
}