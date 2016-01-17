<?php

namespace wpsolr\solr;

/**
 * Class representing a float field type
 *
 * Class WPSOLR_Field_Type_Float
 * @package classes\wpsolr\solr
 */
class WPSOLR_Field_Type_Float extends WPSOLR_Field_Type {

	/**
	 * Sanitize a float value
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

		if ( ! is_int( 0 + $value ) && ! is_float( 0 + $value ) ) {
			$this->throw_error( $post, $field_name, $value );
		}

		return floatval( $value );
	}

}