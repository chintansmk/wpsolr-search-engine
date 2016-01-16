<?php

namespace wpsolr;

/**
 * Manage schema.xml definitions
 */
class WPSOLR_Schema {

	// Field queried by default. Necessary to get highlighting right.
	const _FIELD_NAME_DEFAULT_QUERY = 'text';

	/*
	 * Solr document field names
	 */
	const _FIELD_NAME_ID = 'id';
	const _FIELD_NAME_PID = 'PID';
	const _FIELD_NAME_TITLE = 'title';
	const _FIELD_NAME_CONTENT = 'content';
	const _FIELD_NAME_AUTHOR = 'author';
	const _FIELD_NAME_AUTHOR_S = 'author_s';
	const _FIELD_NAME_TYPE = 'type';
	const _FIELD_NAME_DATE = 'date';
	const _FIELD_NAME_MODIFIED = 'modified';
	const _FIELD_NAME_DISPLAY_DATE = 'displaydate';
	const _FIELD_NAME_DISPLAY_MODIFIED = 'displaymodified';
	const _FIELD_NAME_PERMALINK = 'permalink';
	const _FIELD_NAME_COMMENTS = 'comments';
	const _FIELD_NAME_NUMBER_OF_COMMENTS = 'numcomments';
	const _FIELD_NAME_CATEGORIES = 'categories';
	const _FIELD_NAME_CATEGORIES_STR = 'categories_str';
	const _FIELD_NAME_TAGS = 'tags';
	const _FIELD_NAME_CUSTOM_FIELDS = 'categories';

	/*
	 * Dynamic types
	 */
	// Solr dynamic type postfix for text
	const _DYNAMIC_TYPE_POSTFIX_TEXT = '_t';


	// Definition translated fields when multi-languages plugins are activated
	public static $multi_language_fields = array(
		array(
			'field_name'      => WPSOLR_Schema::_FIELD_NAME_TITLE,
			'field_extension' => WPSOLR_Schema::_DYNAMIC_TYPE_POSTFIX_TEXT,
		),
		array(
			'field_name'      => WPSOLR_Schema::_FIELD_NAME_CONTENT,
			'field_extension' => WPSOLR_Schema::_DYNAMIC_TYPE_POSTFIX_TEXT,
		),
	);

	// Solr types
	const SOLR_TYPE_STRING = '_str';
	const SOLR_TYPE_INTEGER = '_i';
	const SOLR_TYPE_INTEGER_LONG = '_l';
	const SOLR_TYPE_FLOAT = '_f';
	const SOLR_TYPE_FLOAT_DOUBLE = '_d';
	const SOLR_TYPE_DATE = '_dt';
	const SOLR_TYPE_CUSTOM_FIELD = 'custom';

	private static $SOLR_TYPE_FIELD_NAME = 'name';

	/**
	 * Get all solr types
	 * @return array
	 */
	public static function get_solr_types() {

		return [
			self::SOLR_TYPE_STRING       => [ self::$SOLR_TYPE_FIELD_NAME => 'String' ],
			self::SOLR_TYPE_INTEGER      => [ self::$SOLR_TYPE_FIELD_NAME => 'Integer' ],
			self::SOLR_TYPE_INTEGER_LONG => [ self::$SOLR_TYPE_FIELD_NAME => 'Integer long' ],
			self::SOLR_TYPE_FLOAT        => [ self::$SOLR_TYPE_FIELD_NAME => 'Float' ],
			self::SOLR_TYPE_FLOAT_DOUBLE => [ self::$SOLR_TYPE_FIELD_NAME => 'Float double' ],
			self::SOLR_TYPE_DATE         => [ self::$SOLR_TYPE_FIELD_NAME => 'Date' ],
			self::SOLR_TYPE_CUSTOM_FIELD => [ self::$SOLR_TYPE_FIELD_NAME => 'Custom field in schema.xml' ]
		];

	}

	/**
	 * Get a Solr type name attribute
	 * @return array
	 */
	public static function get_solr_type_name( $solr_type ) {

		return $solr_type[ self::$SOLR_TYPE_FIELD_NAME ];
	}
}