<?php

namespace classes\wpsolr\solr;

use wpsolr\utilities\WPSOLR_Global;
use wpsolr\WPSOLR_Unit_Test;

class WPSOLR_Field_Type_DoubleTest extends WPSOLR_Unit_Test {

	public function testBlank() {

		$this->assertEquals( '', WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '', [ 'solr_type' => 'double' ] ) );
	}

	public function testNull() {

		$this->assertEquals( null, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', null, [ 'solr_type' => 'double' ] ) );
	}

	public function testInteger() {

		$this->assertEquals( 10, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 10, [ 'solr_type' => 'double' ] ) );
		$this->assertEquals( 10, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '10', [ 'solr_type' => 'double' ] ) );
	}

	/**
	 * @expectedException Exception
	 */
	public function testString() {

		$this->assertEquals( 10, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 'not a double', [ 'solr_type' => 'double' ] ) );
	}

	public function testDouble() {

		$this->assertEquals( 10.56, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 10.56, [ 'solr_type' => 'double' ] ) );
		$this->assertEquals( 10.56, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '10.56', [ 'solr_type' => 'double' ] ) );
	}

}