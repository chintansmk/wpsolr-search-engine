<?php

namespace wpsolr\solr;


/**
 * List of Solr field types
 *
 * Class WPSOLR_Field_Types
 * @package classes\wpsolr\solr
 */
class WPSOLR_Field_Types {

	// Solr types ids (do not change)
	const SOLR_TYPE_STRING = 'string';
	const SOLR_TYPE_INTEGER = 'integer';
	const SOLR_TYPE_INTEGER_LONG = 'long';
	const SOLR_TYPE_FLOAT = 'float';
	const SOLR_TYPE_FLOAT_DOUBLE = 'double';
	const SOLR_TYPE_DATE = 'date';
	const SOLR_TYPE_CUSTOM_FIELD = 'custom_field';

	// Solr dynamic types added to the field name
	const SOLR_DYNAMIC_TYPE_STRING = '_str';
	const SOLR_DYNAMIC_TYPE_INTEGER = '_i';
	const SOLR_DYNAMIC_TYPE_INTEGER_LONG = '_l';
	const SOLR_DYNAMIC_TYPE_FLOAT = '_f';
	const SOLR_DYNAMIC_TYPE_FLOAT_DOUBLE = '_d';
	const SOLR_DYNAMIC_TYPE_DATE = '_dt';
	const SOLR_DYNAMIC_TYPE_CUSTOM_FIELD = '';

	protected $solr_field_types;


	public function __construct( $solr_field_types ) {

		$this->solr_field_types = $solr_field_types;
	}


	/**
	 * Singleton constructor called from WPSOLR_Global
	 * @return WPSOLR_Field_Types
	 */
	public static function global_object() {

		$result = new WPSOLR_Field_Types( [
			self::SOLR_TYPE_STRING       => new WPSOLR_Field_Type_String( self::SOLR_TYPE_STRING, 'String', self::SOLR_DYNAMIC_TYPE_STRING ),
			self::SOLR_TYPE_INTEGER      => new WPSOLR_Field_Type_Integer( self::SOLR_TYPE_INTEGER, 'Integer', self::SOLR_DYNAMIC_TYPE_INTEGER ),
			self::SOLR_TYPE_INTEGER_LONG => new WPSOLR_Field_Type_Long( self::SOLR_TYPE_INTEGER_LONG, 'Integer long', self::SOLR_DYNAMIC_TYPE_INTEGER_LONG ),
			self::SOLR_TYPE_FLOAT        => new WPSOLR_Field_Type_Float( self::SOLR_TYPE_FLOAT, 'Float', self::SOLR_DYNAMIC_TYPE_FLOAT ),
			self::SOLR_TYPE_FLOAT_DOUBLE => new WPSOLR_Field_Type_Double( self::SOLR_TYPE_FLOAT_DOUBLE, 'Float double', self::SOLR_DYNAMIC_TYPE_FLOAT_DOUBLE ),
			self::SOLR_TYPE_DATE         => new WPSOLR_Field_Type_Date( self::SOLR_TYPE_DATE, 'Date', self::SOLR_DYNAMIC_TYPE_DATE ),
			self::SOLR_TYPE_CUSTOM_FIELD => new WPSOLR_Field_Type( self::SOLR_TYPE_CUSTOM_FIELD, 'Custom field in schema.xml', self::SOLR_DYNAMIC_TYPE_CUSTOM_FIELD )
		] );

		return $result;
	}

	/**
	 * Create string type fields from an array of field names
	 *
	 * @param array $field_names [ 'Type', 'Author' ]
	 *
	 * @return array [ 'Type' => ['solr_type' => 'string'], 'Author' => ['solr_type' => 'string']]
	 */
	public static function get_fields_type_string( $field_names ) {

		$results = [ ];

		foreach ( $field_names as $field_name ) {
			$results[ $field_name ] = [ 'solr_type' => self::SOLR_TYPE_STRING ];
		}

		return $results;
	}

	/**
	 * Convert a field name in a dynamic strinf field type
	 * 'field1' => 'field1_str'
	 *
	 * @param string $field_name 'price'
	 *
	 * @return string 'price_f'
	 */
	public function get_dynamic_type_name( $field_name, $field_type ) {

		// Solr field names cannot contain a blank
		$field_name = strtolower( str_replace( ' ', '_', $field_name ) );

		return $field_name . $this->get_field_type( $field_type['solr_type'] )->get_dynamic_type();
	}

	/**
	 * Get a solr type
	 *
	 * @return array
	 */
	public function get_field_type( $solr_type_id ) {

		return $this->get_field_types()[ $solr_type_id ];
	}

	/**
	 * Get all solr types
	 *
	 * @return array
	 */
	public function get_field_types() {

		return $this->solr_field_types;

	}

	public function get_sanitized_value( $post, $field_name, $field_value, $field_type ) {

		return $this->get_field_type( $field_type['solr_type'] )->get_sanitized_value( $post, $field_name, $field_value );

	}

}