<?php

namespace wpsolr\utilities;


/**
 * Common Regexp expressions used in WPSOLR.
 *
 * Class WPSOLR_Regexp
 * @package classes\wpsolr\utilities
 */
class WPSOLR_Regexp {


	/**
	 * Remove $text_to_remove at the end of $text
	 *
	 * @param $text
	 * @param $text_to_remove
	 *
	 * @return mixed
	 */
	static function remove_string_at_the_end( $text, $text_to_remove ) {

		return preg_replace( sprintf( '/%s$/', $text_to_remove ), '', $text );
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
		$text = str_replace( [ 'AND', 'and', 'OR', 'or' ], '|', $text );

		// Negate all special caracters to get the 'field:value' array
		preg_match_all( '/[^()|&!]+/', $text, $matches );

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

}