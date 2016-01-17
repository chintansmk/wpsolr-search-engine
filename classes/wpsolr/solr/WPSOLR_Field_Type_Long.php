<?php

namespace wpsolr\solr;

/**
 * Class representing a long field type
 *
 * Class WPSOLR_Field_Type_Long
 * @package classes\wpsolr\solr
 */
class WPSOLR_Field_Type_Long extends WPSOLR_Field_Type {

	/**
	 * Sanitize a long value
	 *
	 * @param \WP_Post $post
	 * @param string $field_name
	 * @param string $value
	 *
	 * @return float|string
	 * @throws \Exception
	 */
	public function get_sanitized_value( $post, $field_name, $value ) {

		if ( empty( $value ) ) {
			return $value;
		}


		if ( ! is_numeric( $value ) ) {
			$this->throw_error( $post, $field_name, $value );
		}


		// Verify it does not contain a ".", except if it is a scientific notation without a decimal
		if ( strpos( $value, '.' ) !== false && ! preg_match( '/^1.0E+/', $value ) ) {
			$this->throw_error( $post, $field_name, $value );
		}

		return (double) ( 0 + $value );
	}

}