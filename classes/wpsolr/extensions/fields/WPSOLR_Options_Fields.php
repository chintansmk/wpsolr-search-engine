<?php

namespace wpsolr\extensions\fields;

use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\WPSOLR_Schema;

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

		$old_custom_fields = WPSOLR_Global::getOption()->get_fields_custom_fields();

		// Save the new option
		//self::set_option_data( self::OPTION_FIELDS, $new_options );

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
							'comments'         => 0,
							'p_types'          => '',
							'taxonomies'       => '',
							'cust_fields'      => '',
							'attachment_types' => ''
						]
					),
					'solr_types'                => WPSOLR_Schema::get_solr_types(),
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
					'selected_custom_fields'    => WPSOLR_Global::getOption()->get_fields_custom_fields()

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

		// custom fields
		$custom_fields = $wpdb->get_col( "SELECT distinct meta_key
						FROM $wpdb->postmeta
						WHERE meta_key!='bwps_enable_ssl'
						ORDER BY meta_key" );


		// woocommerce attributes
		$wcoocommerce_attributes = $this->get_woocommerce_attributes();

		return array_merge( $custom_fields, $wcoocommerce_attributes );
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