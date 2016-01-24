<?php

namespace wpsolr\utilities;

use wpsolr\WPSOLR_Unit_Test;

class WPSOLR_RegexpTest extends WPSOLR_Unit_Test {

	public function test_remove_string_at_the_end() {

		// Usual case
		$this->assertEquals(
			'prefix',
			WPSOLR_Regexp::remove_string_at_the_end( 'prefix_str', '_str' )
		);

		// Extension before
		$this->assertEquals(
			'_str_postfix',
			WPSOLR_Regexp::remove_string_at_the_end( '_str_postfix', '_str' )
		);

		// extension in the middle
		$this->assertEquals(
			'prefix_str_postfix',
			WPSOLR_Regexp::remove_string_at_the_end( 'prefix_str_postfix', '_str' )
		);

		// Remove only the last extension
		$this->assertEquals(
			'prefix_str',
			WPSOLR_Regexp::remove_string_at_the_end( 'prefix_str_str', '_str' )
		);

		// Empty string
		$this->assertEquals(
			'',
			WPSOLR_Regexp::remove_string_at_the_end( '', '_str' )
		);
	}

	public function test_extract_filter_query_simple() {

		$matches = WPSOLR_Regexp::extract_filter_query( '' );
		$this->assertEquals(
			[ ],
			$matches
		);

		$matches = WPSOLR_Regexp::extract_filter_query( 'field1:value1' );
		$this->assertEquals(
			[ 'field1:value1' ],
			$matches
		);

		// Do not remove blanks
		$matches = WPSOLR_Regexp::extract_filter_query( 'field1:value1_begin   value1_end' );
		$this->assertEquals(
			[ 'field1:value1_begin   value1_end' ],
			$matches
		);

		$matches = WPSOLR_Regexp::extract_filter_query( 'field1:"value1   "' );
		$this->assertEquals(
			[ 'field1:"value1   "' ],
			$matches
		);

	}

	public function test_extract_filter_query_complex() {

		foreach ( [ 'AND', 'and', 'OR', 'or', '|', '&', '&&', '!' ] as $separator ) {

			$matches = WPSOLR_Regexp::extract_filter_query( sprintf( 'field1:value1 %s field2:value2', $separator ) );
			$this->assertEquals(
				[ 'field1:value1', 'field2:value2' ],
				$matches
			);

			$matches = WPSOLR_Regexp::extract_filter_query( sprintf( 'field1:value1 %s field1:value1', $separator ) );
			$this->assertEquals(
				[ 'field1:value1' ],
				$matches
			);

			$matches = WPSOLR_Regexp::extract_filter_query( sprintf( 'field1:value1      %s    field2:value2', $separator ) );
			$this->assertEquals(
				[ 'field1:value1', 'field2:value2' ],
				$matches
			);

			$matches = WPSOLR_Regexp::extract_filter_query( sprintf( '(field1:value1 OR ((field2:value2 %s field3:value3) AND (field1:value1)))', $separator ) );
			$this->assertEquals(
				[ 'field1:value1', 'field2:value2', 'field3:value3' ],
				$matches
			);

		}

	}
}
