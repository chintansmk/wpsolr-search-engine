<?php

namespace wpsolr\solr;

use wpsolr\utilities\WPSOLR_Global;

/**
 * Class representing a date field type
 *
 * See: https://cwiki.apache.org/confluence/display/solr/Working+with+Dates
 *
 * Class WPSOLR_Field_Type_Date
 * @package classes\wpsolr\solr
 */
class WPSOLR_Field_Type_Date extends WPSOLR_Field_Type {

	/**
	 * Sanitize a date value
	 *
	 * @param \WP_Post $post
	 * @param string $field_name
	 * @param string $value
	 *
	 * @return bool|string
	 * @throws \Exception
	 */
	public function get_sanitized_value( $post, $field_name, $value ) {


		if ( empty( $value ) ) {
			return $value;
		}

		// Try to format date to Solr date format
		$result = WPSOLR_Global::getSolariumHelper()->formatDate( $value );

		if ( $result === false ) {
			$this->throw_error( $post, $field_name, $value );
		}

		return $result;
	}

}