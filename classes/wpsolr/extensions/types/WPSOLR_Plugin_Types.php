<?php

namespace wpsolr\extensions\types;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;
use wpsolr\WPSOLR_Filters;

/**
 * Class WPSOLR_Plugin_Types
 *
 * Manage "Types" plugin (Custom fields)
 * @link https://wordpress.org/plugins/types/
 */
class WPSOLR_Plugin_Types extends WPSOLR_Extensions {

	// Prefix of TYPES custom fields
	const CONST_TYPES_FIELD_PREFIX = 'wpcf-';

	/**
	 * After constructor.
	 */
	protected function post_constructor() {

		/**
		 * Filters/Actions
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
					'options' => WPSOLR_Global::getOption()->get_option_plugin_types(
						[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE => '0' ]
					)
				],
				$plugin_parameters
			)
		);
	}

	/**
	 * Get the TYPES field label from the custom field name.
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

		if ( ! WPSOLR_Global::getOption()->get_plugin_groups_is_display_types_label_on_facet() || ! ( self::CONST_TYPES_FIELD_PREFIX == substr( $custom_field_name, 0, strlen( self::CONST_TYPES_FIELD_PREFIX ) ) ) ) {
			// No need to replace custom field name by types field label
			return $result;
		}


		$custom_field_name_without_prefix = substr( $custom_field_name, strlen( self::CONST_TYPES_FIELD_PREFIX ) );
		$field                            = wpcf_fields_get_field_by_slug( $custom_field_name_without_prefix );

		// Retrieve field among TYPES fields
		if ( isset( $field ) ) {
			$result = $field['name'];
		}

		return $result;
	}

}