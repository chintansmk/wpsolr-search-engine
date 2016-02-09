<?php

namespace wpsolr\utilities;

use wpsolr\WPSOLR_Unit_Test;

class WPSOLR_RegexpTest extends WPSOLR_Unit_Test {

	public function test_extract_last_separator() {

		foreach ( [ '_', '__' ] as $separator ) {

			// No match
			$this->assertEquals(
				'',
				WPSOLR_Regexp::extract_last_separator( 'field1', $separator )
			);

			// Usual case
			$this->assertEquals(
				'asc',
				WPSOLR_Regexp::extract_last_separator( sprintf( 'field1%sasc', $separator ), $separator )
			);

			// Extract last separator only
			$this->assertEquals(
				'asc',
				WPSOLR_Regexp::extract_last_separator( sprintf( 'field1%snot_this%sasc', $separator, $separator ), $separator )
			);
		}

	}

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

	public function test_remove_string_at_the_beginning() {

		// Usual case
		$this->assertEquals(
			'postfix',
			WPSOLR_Regexp::remove_string_at_the_begining( 'str_postfix', 'str_' )
		);

		// Extension after
		$this->assertEquals(
			'postfix_str',
			WPSOLR_Regexp::remove_string_at_the_begining( 'postfix_str', 'str_' )
		);

		// extension in the middle
		$this->assertEquals(
			'prefix_str_postfix',
			WPSOLR_Regexp::remove_string_at_the_begining( 'prefix_str_postfix', 'str_' )
		);

		// Remove only the first extension
		$this->assertEquals(
			'str_postfix',
			WPSOLR_Regexp::remove_string_at_the_begining( 'str_str_postfix', 'str_' )
		);

		// Empty string
		$this->assertEquals(
			'',
			WPSOLR_Regexp::remove_string_at_the_begining( '', 'str_' )
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

		$matches = WPSOLR_Regexp::extract_filter_query( 'field1:[0 TO 10]' );
		$this->assertEquals(
			[ 'field1:[0 TO 10]' ],
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


		$matches = WPSOLR_Regexp::extract_filter_query( 'field1:"value1 (value)   "' );
		$this->assertEquals(
			[ 'field1:"value1 (value)   "' ],
			$matches
		);

		$matches = WPSOLR_Regexp::extract_filter_query( 'field1:*' );
		$this->assertEquals(
			[ 'field1:*' ],
			$matches
		);

		$matches = WPSOLR_Regexp::extract_filter_query( 'field1:1-2' );
		$this->assertEquals(
			[ 'field1:1-2' ],
			$matches
		);

	}

	public function test_extract_filter_query_complex() {

		foreach ( [ 'AND', 'and', 'OR', 'or', '|', '&&', '!' ] as $separator ) {

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

	public function test_split_lines() {

		$this->assertEquals(
			[ '' ],
			WPSOLR_Regexp::split_lines( '' )
		);

		$this->assertEquals(
			[ 'one line' ],
			WPSOLR_Regexp::split_lines( 'one line' )
		);

		foreach ( [ "\n", "\r", "\r\n" ] as $new_line_char ) {

			// One line ending with newline
			$this->assertEquals(
				[ 'line 1', '' ],
				WPSOLR_Regexp::split_lines( "line 1{$new_line_char}" )
			);

			// One line beginning with newline
			$this->assertEquals(
				[ '', 'line 1' ],
				WPSOLR_Regexp::split_lines( "{$new_line_char}line 1" )
			);

			// 2 lines
			$this->assertEquals(
				[ 'line 1', 'line 2' ],
				WPSOLR_Regexp::split_lines( "line 1{$new_line_char}line 2" )
			);

		}

		// 2 lines with a blank line
		$this->assertEquals(
			[ 'line 1', 'line 2', ' ' ],
			WPSOLR_Regexp::split_lines( "line 1{$new_line_char}line 2{$new_line_char} " )
		);

	}


	public
	function test_preg_match_lines() {

		foreach ( [ "\n", "\r", "\r\n" ] as $new_line_char ) {

			// One regep line: true
			$this->assertTrue(
				WPSOLR_Regexp::preg_match_lines_of_regexp( "/1/", "1" )
			);

			// Trim regep line: true
			$this->assertTrue(
				WPSOLR_Regexp::preg_match_lines_of_regexp( "/1/   ", "1" )
			);

			// One regep line: false
			$this->assertFalse(
				WPSOLR_Regexp::preg_match_lines_of_regexp( "/1/", "2" )
			);

			// 2 regep lines: matches on 1st regexp
			$this->assertTrue(
				WPSOLR_Regexp::preg_match_lines_of_regexp( "/1/{$new_line_char}/2/", "1" )
			);

			// 2 regep lines: matches on 2nd regexp
			$this->assertTrue(
				WPSOLR_Regexp::preg_match_lines_of_regexp( "/1/{$new_line_char}/2/", "2" )
			);

			// 2 regep lines: matches on 2nd regexp
			$this->assertTrue(
				WPSOLR_Regexp::preg_match_lines_of_regexp( "/1/{$new_line_char}/2/{$new_line_char}", "2" )
			);

			// 2 regep lines: no match
			$this->assertFalse(
				WPSOLR_Regexp::preg_match_lines_of_regexp( "/1/{$new_line_char}/2/", "3" )
			);

			// 2 regep lines with a blank line
			$this->assertTrue(
				WPSOLR_Regexp::preg_match_lines_of_regexp( "/1/{$new_line_char}/2/{$new_line_char} ", "1" )
			);

		}
	}

	/**
	 * @expectedException \wpsolr\exceptions\WPSOLR_Exception
	 * */
	public
	function test_preg_match_limes_syntax_error() {

		// regep syntax error should be trhrowing an exception
		$this->assertTrue(
			WPSOLR_Regexp::preg_match_lines_of_regexp( "1/", "1" )
		);
	}


	public function test_extract_filter_range_values() {

		$matches = WPSOLR_Regexp::extract_filter_range_values( '' );
		$this->assertEquals(
			[ ],
			$matches
		);

		$matches = WPSOLR_Regexp::extract_filter_range_values( '[5 TO 10]' );
		$this->assertEquals(
			[ '5', '10' ],
			$matches
		);

		// Same range bounds
		$matches = WPSOLR_Regexp::extract_filter_range_values( '[5 TO 5]' );
		$this->assertEquals(
			[ '5', '5' ],
			$matches
		);

	}


	public function test_replace_caracters_not_inside_quotes() {

		$this->assertEquals(
			null,
			WPSOLR_Regexp::replace_caracters_not_inside_quotes( null, '1', '2' )
		);

		$this->assertEquals(
			'',
			WPSOLR_Regexp::replace_caracters_not_inside_quotes( '', '1', '2' )
		);

		$this->assertEquals(
			'3',
			WPSOLR_Regexp::replace_caracters_not_inside_quotes( '3', '1', '2' )
		);

		$this->assertEquals(
			'2',
			WPSOLR_Regexp::replace_caracters_not_inside_quotes( '1', '1', '2' )
		);

		$this->assertEquals(
			'2 2',
			WPSOLR_Regexp::replace_caracters_not_inside_quotes( '1 1', '1', '2' )
		);

		$this->assertEquals(
			'"1"',
			WPSOLR_Regexp::replace_caracters_not_inside_quotes( '"1"', '1', '2' )
		);

		$this->assertEquals(
			'"1" 2',
			WPSOLR_Regexp::replace_caracters_not_inside_quotes( '"1" 1', '1', '2' )
		);

		$this->assertEquals(
			'"1" "1"',
			WPSOLR_Regexp::replace_caracters_not_inside_quotes( '"1" "1"', '1', '2' )
		);


		// Test all Solr special caracters
		foreach ( WPSOLR_Regexp::$solr_special_caracters as $special_caracter
		) {

			$this->assertEquals(
				'',
				WPSOLR_Regexp::replace_caracters_not_inside_quotes( $special_caracter, $special_caracter, '' )
			);

			$this->assertEquals(
				"\"$special_caracter\"",
				WPSOLR_Regexp::replace_caracters_not_inside_quotes( "\"$special_caracter\"", $special_caracter, '' )
			);

		}

	}

	public function test_replace_solr_special_caracters() {

		foreach (
			WPSOLR_Regexp::$solr_special_caracters as $special_caracter
		) {

			$this->assertEquals(
				'before  after',
				WPSOLR_Regexp::replace_solr_special_caracters( "before $special_caracter after", '' )
			);

			$this->assertEquals(
				"\"before $special_caracter after\"",
				WPSOLR_Regexp::replace_solr_special_caracters( "\"before $special_caracter after\"", '' )
			);

		}
	}

}
