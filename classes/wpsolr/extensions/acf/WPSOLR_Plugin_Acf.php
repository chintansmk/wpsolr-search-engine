<?php

namespace wpsolr\extensions\acf;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;
use wpsolr\WPSOLR_Filters;

/**
 * Class WPSOLR_Plugin_Acf
 *
 * Manage Advanced Custom Fields (ACF) plugin
 * @link https://wordpress.org/plugins/advanced-custom-fields/
 */
class WPSOLR_Plugin_Acf extends WPSOLR_Extensions {

	// Prefix of ACF fields
	const FIELD_PREFIX = '_';

	// acf fields indexed by name.
	private $_fields;

	/**
	 * After constructor.
	 */
	protected function post_constructor() {

		/**
		 * Filters
		 */
		WPSOLR_Service_Wordpress::add_filter( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_FACET_NAME, array(
			$this,
			'get_field_label'
		), 10, 1 );

	}

	/**
	 * Display admin form.
	 *
	 * @param array $plugin_parameters Parameters
	 */
	public function output_form( $form_file = null, $plugin_parameters = [ ] ) {

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options' => WPSOLR_Global::getOption()->get_option_plugin_acf(
						[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE => '0' ]
					)
				],
				$plugin_parameters
			)
		);

	}

	/**
	 * Retrieve all field keys of all ACF fields.
	 *
	 * @return array
	 */
	function get_fields() {
		global $wpdb;

		// Uue cached fields if exist
		if ( isset( $this->_fields ) ) {
			return $this->_fields;
		}

		$fields = array();

		// Else create the cached fields
		$results = $wpdb->get_results( "SELECT distinct meta_key, meta_value
                                        FROM $wpdb->postmeta
                                        WHERE meta_key like '_%'
                                        AND   meta_value like 'field_%'" );

		$nb_results = count( $results );
		for ( $loop = 0; $loop < $nb_results; $loop ++ ) {
			$fields[ $results[ $loop ]->meta_key ] = $results[ $loop ]->meta_value;

		}

		// Save the cache
		$this->_fields = $fields;

		return $this->_fields;
	}


	/**
	 * Get the ACF field label from the custom field name.
	 *
	 * @param $custom_field_name
	 *
	 * @return mixed
	 */
	public
	function get_field_label(
		$custom_field_name
	) {

		$result = $custom_field_name;

		if ( ! WPSOLR_Global::getOption()->get_plugin_acf_is_replace_custom_field_name() ) {
			// No need to replace custom field name by acf field label
			return $result;
		}

		// Retrieve field among ACF fields
		$fields = $this->get_fields();
		if ( isset( $fields[ self::FIELD_PREFIX . $custom_field_name ] ) ) {
			$field_key = $fields[ self::FIELD_PREFIX . $custom_field_name ];
			$field     = get_field_object( $field_key );
			$result    = isset( $field['label'] ) ? $field['label'] : $custom_field_name;
		}

		return $result;
	}


}