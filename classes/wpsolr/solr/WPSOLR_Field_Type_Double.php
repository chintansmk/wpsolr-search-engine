<?php

namespace wpsolr\solr;

/**
 * Class representing a double field type
 *
 * Class WPSOLR_Field_Type_Double
 * @package classes\wpsolr\solr
 */
class WPSOLR_Field_Type_Double extends WPSOLR_Field_Type {

	/**
	 * Sanitize a double value
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

		if ( ! is_int( 0 + $value ) && ! is_double( 0 + $value ) ) {
			$this->throw_error( $post, $field_name, $value );
		}

		return doubleval( $value );
	}

}