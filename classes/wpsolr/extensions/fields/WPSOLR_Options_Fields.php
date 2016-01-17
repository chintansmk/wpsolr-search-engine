<?php

namespace wpsolr\extensions\fields;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\solr\WPSOLR_Field_Types;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;
use wpsolr\utilities\WPSOLR_Regexp;

/**
 * Class WPSOLR_Options_Fields
 *
 * Manage Solr Fields definitions
 */
class WPSOLR_Options_Fields extends WPSOLR_Extensions {


	/**
	 * Migrate the old custom fields data to the new custom fields data.
	 */
	public function migrate_data_from_v7_6() {

		$old_custom_fields = WPSOLR_Global::getOption()->migrate_data_from_v7_6_get_fields_custom_fields_array();
		//$old_custom_fields = [ '_acf1_str', '_acf2_str', 'test1', '_str_test2' ];
		if ( ! count( $old_custom_fields ) ) {
			// Old custom fields empty: nothing to migrate
			return;
		}

		$new_custom_fields = WPSOLR_Global::getOption()->get_fields_custom_fields_array();
		//$new_custom_fields = [ ];
		if ( count( $new_custom_fields ) ) {
			// New custom fields not empty: migration already done
			return;
		}

		// Copy custom fields
		// ['_acf1_str', '_acf2_str'] ===> [['_acf1' => ['solr_type' => 'str']], ['_acf2' => ['solr_type' => 'str']]]
		$new_custom_fields = [ ];
		foreach ( $old_custom_fields as $old_custom_field ) {
			$old_custom_field_without_str = WPSOLR_Regexp::remove_string_at_the_end( $old_custom_field, WPSOLR_Field_Types::SOLR_TYPE_STRING );

			$new_custom_fields[ $old_custom_field_without_str ]['solr_type'] = WPSOLR_Field_Types::SOLR_TYPE_STRING;
		}


		// Save the new option
		$options                                               = WPSOLR_Global::getOption()->get_option_fields();
		$options[ WPSOLR_Option::OPTION_FIELDS_CUSTOM_FIELDS ] = $new_custom_fields;
		self::set_option_data( self::OPTION_FIELDS, $options );

		// Do not delete the old options. If the user wants to rollback the version, he can.
		return;
	}


	/**
	 * Post constructor.
	 */
	protected function post_constructor() {
	}

	/**
	 * Display admin form.
	 *
	 * @param array $plugin_parameters Parameters
	 */
	public function output_form( $form_file = null, $plugin_parameters = [ ] ) {
		$this->get_woocommerce_attributes();


		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'                   => WPSOLR_Global::getOption()->get_option_fields(
						[
							WPSOLR_Option::OPTION_FIELDS_ARE_COMMENTS_INDEXED => 0,
							WPSOLR_Option::OPTION_FIELDS_POST_TYPES           => '',
							WPSOLR_Option::OPTION_FIELDS_TAXONOMIES           => '',
							WPSOLR_Option::OPTION_FIELDS_CUSTOM_FIELDS        => '',
							WPSOLR_Option::OPTION_FIELDS_ATTACHMENTS          => ''
						]
					),
					'solr_field_types'          => WPSOLR_Global::getSolrFieldTypes()->get_field_types(),
					'indexable_post_types'      => $this->get_indexable_post_types(),
					'allowed_attachments_types' => get_allowed_mime_types(),
					'taxonomies'                => get_taxonomies(
						[
							'public'   => true,
							'_builtin' => false

						],
						'names',
						'and'
					),
					'indexable_custom_fields'   => $this->get_indexable_custom_fields(),
					'selected_custom_fields'    => WPSOLR_Global::getOption()->get_fields_custom_fields_array()

				],
				$plugin_parameters
			)
		);
	}


	/**
	 * Get all post types, except some.
	 *
	 * @return array
	 */
	protected function get_indexable_post_types() {

		$post_types = get_post_types();

		$results = array();
		foreach ( $post_types as $post_type ) {

			if ( $post_type != 'attachment' && $post_type != 'revision' && $post_type != 'nav_menu_item' ) {

				array_push( $results, $post_type );
			}
		}

		return $results;
	}

	private function get_indexable_custom_fields() {
		global $wpdb;

		// custom fields 'standard' first
		$custom_fields = $wpdb->get_col( "SELECT distinct meta_key
						FROM $wpdb->postmeta
						WHERE meta_key != 'bwps_enable_ssl'
						AND meta_key NOT LIKE '\_%'
						ORDER BY meta_key" );

		// custom fields 'special' then
		$custom_fields_special = $wpdb->get_col( "SELECT distinct meta_key
						FROM $wpdb->postmeta
						WHERE meta_key != 'bwps_enable_ssl'
						AND meta_key LIKE '\_%'
						ORDER BY meta_key" );


		// woocommerce attributes
		$wcoocommerce_attributes = $this->get_woocommerce_attributes();

		return array_merge( $wcoocommerce_attributes, $custom_fields, $custom_fields_special );
	}

	/**
	 * Get woocommerce attributes
	 */
	public function get_woocommerce_attributes() {

		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {

			return WPSOLR_Plugin_WooCommerce::get_attribute_taxonomy_names();
		}

		return [ ];
	}

}