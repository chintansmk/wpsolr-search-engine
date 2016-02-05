<?php

namespace wpsolr\utilities;

use wpsolr\exceptions\WPSOLR_Exception;


/**
 * Common Regexp expressions used in WPSOLR.
 *
 * Class WPSOLR_Regexp
 * @package classes\wpsolr\utilities
 */
class WPSOLR_Regexp {

	/**
	 * Extract values from a range query parameter
	 * '[5 TO 30]' => ['5', '30']
	 *
	 * @param $text
	 *
	 * @return string
	 */
	static function extract_filter_range_values( $text ) {

		// Replace separator literals by a single special character. Much easier, because negate a literal is difficult with regexp.
		$text = str_replace( [ ' TO ', '[', ']' ], ' | ', $text );

		// Negate all special caracters to get the 'field:value' array
		preg_match_all( '/[^|\s]+/', $text, $matches );

		// Trim results
		$results_with_some_empty_key = ! empty( $matches[0] ) ? array_map( 'trim', $matches[0] ) : [ ];

		// Remove empty array rows (it happens), prevent duplicates.
		$results = [ ];
		foreach ( $results_with_some_empty_key as $result ) {
			if ( ! empty( $result ) ) {
				array_push( $results, $result );
			}
		}

		return $results;
	}

	/**
	 * Extract last occurence of a separator
	 * 'field1' => ''
	 * 'field1_asc' => 'asc'
	 * 'field1_notme_asc' => 'asc'
	 *
	 * @param $text
	 * @param $text_to_find
	 *
	 * @return string
	 */
	static function extract_last_separator( $text, $separator ) {

		preg_match( sprintf( '/[_]+[^_]*$/', $separator ), $text, $matches );

		return ! empty( $matches ) ? substr( $matches[0], strlen( $separator ) ) : '';
	}


	/**
	 * Remove $text_to_remove at the end of $text
	 *
	 * @param $text
	 * @param $text_to_remove
	 *
	 * @return string
	 */
	static function remove_string_at_the_end( $text, $text_to_remove ) {

		return preg_replace( sprintf( '/%s$/', $text_to_remove ), '', $text );
	}

	/**
	 * Remove $text_to_remove at the beginning of $text
	 *
	 * @param $text
	 * @param $text_to_remove
	 *
	 * @return string
	 */
	static function remove_string_at_the_begining( $text, $text_to_remove ) {

		return preg_replace( sprintf( '/^%s/', $text_to_remove ), '', $text );
	}

	/**
	 * Extract individual field query from  a filter query
	 * Facets: type:post => ['type:post']
	 * Facets group filter query: type:post OR type:page AND (categories:Blog) => ['type:post', 'type:page', 'categories:Blog']
	 *
	 * @param $text
	 *
	 * @return int
	 */
	static function extract_filter_query( $text ) {

		// Replace separator literals by a single special character. Much easier, because negate a literal is difficult with regexp.
		$text = str_replace( [ ' AND ', ' and ', ' OR ', ' or ' ], ' | ', $text );

		// Negate all special caracters to get the 'field:value' array
		preg_match_all( '/[^()|&!]+/', $text, $matches );

		// https://lucene.apache.org/core/2_9_4/queryparsersyntax.html#Escaping Special Characters
		// [+ - && || ! ( ) { } [ ] ^ " ~ * ? : \]
		// To escape these character use the \

		// Trim results
		$results_with_some_empty_key = ! empty( $matches[0] ) ? array_map( 'trim', $matches[0] ) : [ ];

		// Remove empty array rows (it happens), prevent duplicates.
		$results = [ ];
		foreach ( $results_with_some_empty_key as $result ) {
			if ( ! empty( $result ) & ! in_array( $result, $results ) ) {
				array_push( $results, $result );
			}
		}

		return $results;

	}

	/**
	 * Split a text containing lines in an array
	 * @see http://stackoverflow.com/questions/1483497/how-to-put-string-in-array-split-by-new-line
	 *
	 * @param $text_with_lines
	 *
	 * @return array
	 */
	static function split_lines( $text_with_lines ) {

		return preg_split( "/(\r\n|\n|\r)/", $text_with_lines );
	}


	/**
	 * Match a text with lines of regexp expressions
	 *
	 *
	 * @param $regexp_lines Lines of regexp
	 * @param $text Text to match
	 *
	 * @return bool True if at least one regexp matches the text
	 * @throws WPSOLR_Exception
	 */
	static function preg_match_lines_of_regexp( $regexp_lines, $text ) {

		foreach ( self::split_lines( $regexp_lines ) as $regexp_line ) { // loop on each line

			// Is the text match with the regexp line ?
			// @is used to suppress the annoying warning if regexp is in syntax error
			$preg_line_match = @preg_match( $regexp_line, $text, $line_matches );
			if ( $preg_line_match === false ) {
				// regexp syntax error: no url is authorized
				throw new WPSOLR_Exception( sprintf( 'Invalid Regexp \'%s\'.', $regexp_line ) );
			}

			if ( $line_matches != null ) {
				// This line matched: stop with success
				return true;
			};
		}

		// No lines matched
		return false;
	}

}