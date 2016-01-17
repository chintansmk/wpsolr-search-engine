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

}