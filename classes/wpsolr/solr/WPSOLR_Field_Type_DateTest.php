<?php

namespace classes\wpsolr\solr;

use Solarium\Core\Query\Helper;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\WPSOLR_Unit_Test;

class WPSOLR_Field_Type_DateTest extends WPSOLR_Unit_Test {

	public function testSolariumDate() {

		$helper = new Helper();

		$this->assertEquals( '2010-12-30T00:00:00Z', $helper->formatDate( '12/30/2010' ) );
		$this->assertEquals( false, $helper->formatDate( '12-30-2010' ) );
	}


	public function testBlank() {

		$this->assertEquals( '', WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '', [ 'solr_type' => 'date' ] ) );
	}

	public function testNull() {

		$this->assertEquals( null, WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', null, [ 'solr_type' => 'date' ] ) );
	}

	public function testDate() {

		$this->assertEquals( '2010-12-30T00:00:00Z', WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '12/30/2010', [ 'solr_type' => 'date' ] ) );
	}

	/**
	 * @expectedException Exception
	 */
	public function testString() {

		$this->assertEquals( '???', WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', 'not a date', [ 'solr_type' => 'date' ] ) );
	}

	/**
	 * @expectedException Exception
	 */
	public function testWrongFormat() {

		$this->assertEquals( '???', WPSOLR_Global::getSolrFieldTypes()->get_sanitized_value( null, 'field', '12-30-2010', [ 'solr_type' => 'date' ] ) );
	}
}