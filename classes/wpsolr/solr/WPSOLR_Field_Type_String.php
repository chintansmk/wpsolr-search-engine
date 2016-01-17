<?php

namespace wpsolr\solr;

/**
 * Class representing a string field type
 *
 * Class WPSOLR_Field_Type_String
 * @package classes\wpsolr\solr
 */
class WPSOLR_Field_Type_String extends WPSOLR_Field_Type {

	/**
	 * Sanitize a string value
	 *
	 * @param string $post
	 * @param string $field_name
	 * @param string $value
	 *
	 * @return string
	 */
	public function get_sanitized_value( $post, $field_name, $value ) {

		return strip_tags( $value );
	}

}