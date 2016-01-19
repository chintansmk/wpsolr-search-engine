<?php

namespace wpsolr\solr;


/**
 * Class representing a Solr field type
 *
 * Class WPSOLR_Field_Type
 * @package classes\wpsolr\solr
 */
class WPSOLR_Field_Type {

	// Sanitized error message
	const ERROR_SANITIZED_MESSAGE = 'Value %s of field %s %s should be a %s, according to it\'s definition in WPSOLR fields settings (tab 2.2) .';

	// id of the field: 'string'
	protected $id;

	// Name of the field 'String'
	protected $name;

	// Dynamic type extension of the field: '_str'
	protected $dynamic_type;

	// Is this a range type ?
	protected $is_range;

	/**
	 * WPSOLR_Field_Type constructor.
	 *
	 * @param $id
	 * @param $name
	 * @param $dynamic_type
	 * @param $is_range
	 */
	public function __construct( $id, $name, $dynamic_type, $is_range ) {
		$this->id           = $id;
		$this->name         = $name;
		$this->dynamic_type = $dynamic_type;
		$this->is_range     = $is_range;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function get_dynamic_type() {
		return $this->dynamic_type;
	}

	/**
	 * @param string $dynamic_type
	 */
	public function set_dynamic_type( $dynamic_type ) {
		$this->dynamic_type = $dynamic_type;
	}

	/**
	 * Sanitize a value based on it's type
	 * Implemented in children
	 *
	 * @param $post
	 * @param string $field_name
	 * @param string $value
	 *
	 * @return string
	 */
	public function get_sanitized_value( $post, $field_name, $value ) {
		return $value;
	}

	/**
	 * @return boolean
	 */
	public function get_is_range() {
		return $this->is_range;
	}

	/**
	 * @param boolean $is_range
	 */
	public function set_is_range( $is_range ) {
		$this->is_range = $is_range;
	}


	/**
	 * @param \WP_Post $post
	 * @param $field_name
	 * @param $value
	 *
	 * @throws \Exception
	 */
	protected function throw_error( $post, $field_name, $value ) {

		throw new \Exception( sprintf( self::ERROR_SANITIZED_MESSAGE, $value, $field_name,
			empty( $post ) ? '' : 'of post ' . $post->post_name,
			$this->name ) );
	}
}