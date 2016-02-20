<?php

namespace wpsolr\extensions\shortcodes;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\ui\shortcode\WPSOLR_Shortcode;
use wpsolr\ui\WPSOLR_UI;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_Shortcodes
 *
 * Generate shortcodes
 */
class WPSOLR_Options_Shortcodes extends WPSOLR_Extensions {

	// Form fields
	const SHORTCODE_FIELD_SHORTCODE_NAME = 'shortcode_name';
	const SHORTCODE_FIELD_CODE = 'code';

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

		// Clone some shortcodes
		$shortcodes = WPSOLR_Global::getOption()->get_option_shortcodes();
		if ( ! is_array( $shortcodes ) ) {
			$shortcodes = [ ];
		}
		$shortcodes = $this->clone_some_shortcodes( $shortcodes );

		// Generate shortcodes code
		$this->generate_codes( $shortcodes );

		// Add new uuid to shortcodes types
		$new_shortcodes      = [ ];
		$new_shortcode_uuids = [ ];
		foreach ( WPSOLR_Shortcode::get_shortcodes() as $shortcode_name => $shortcode ) {

			$new_uuid = WPSOLR_Global::getExtensionIndexes()->generate_uuid();

			$new_shortcode_uuids[]             = $new_uuid;
			$new_shortcodes[ $shortcode_name ] = [
				$new_uuid => [
					WPSOLR_UI::FORM_FIELD_TITLE => 'New shortcode'
				]
			];
		}

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'               => WPSOLR_Global::getOption()->get_option_shortcodes(),
					'predefined_shortcodes' => WPSOLR_Shortcode::get_shortcodes(),
					'shortcodes'            => $shortcodes,
					'new_shortcode_uuids'   => $new_shortcode_uuids,
					'new_shortcodes'        => $new_shortcodes
				],
				$plugin_parameters
			)
		);
	}

	/**
	 * Generate the code of each shortcode
	 *
	 * @param $shortcodes
	 */
	public function generate_codes( &$shortcodes ) {

		foreach ( $shortcodes as $shortcode_type_name => &$shortcode_type ) {

			foreach ( $shortcode_type as $shortcode_uuid => &$shortcode ) {

				$shortcode[ self::SHORTCODE_FIELD_CODE ] = sprintf( '[%s name="%s" id="%s"]',
					$shortcode_type_name, $shortcode[ WPSOLR_UI::FORM_FIELD_TITLE ], $shortcode_uuid );
			}
		}
	}

	/**
	 * Clone the shortcodes marked.
	 *
	 * @param $shortcodes
	 */
	public function clone_some_shortcodes( &$shortcodes ) {

		foreach ( $shortcodes as $shortcode_type_name => &$shortcode_type ) {

			foreach ( $shortcode_type as $shortcode_uuid => &$shortcode ) {

				if ( ! empty( $shortcode['is_to_be_cloned'] ) ) {

					unset( $shortcode['is_to_be_cloned'] );

					// Clone the shortcode
					$shortcode_cloned         = $shortcode;
					$shortcode_cloned_uuid    = WPSOLR_Global::getExtensionIndexes()->generate_uuid();
					$shortcode_cloned['name'] = 'Clone of ' . $shortcode_cloned[ WPSOLR_UI::FORM_FIELD_TITLE ];

					$shortcodes[ $shortcode_type_name ][ $shortcode_cloned_uuid ] = $shortcode_cloned;

				}

			}
		}

		return $shortcodes;
	}

	public function get_shortcode_by_type_and_id( $shortcode_type, $shortcode_id ) {

		if ( empty( $shortcode_id ) ) {
			throw new WPSOLR_Exception( sprintf( 'shortcode "%s" needs an id.', $shortcode_type ) );
		}

		$shortcodes = WPSOLR_Global::getOption()->get_option_shortcodes();
		$shortcode  = ! empty( $shortcodes ) && ! empty( $shortcodes[ $shortcode_type ] ) && ! empty( $shortcodes[ $shortcode_type ][ $shortcode_id ] )
			? $shortcodes[ $shortcode_type ][ $shortcode_id ] : [ ];

		if ( empty( $shortcode ) ) {
			throw new WPSOLR_Exception( sprintf( 'shortcode "%s" with id %s does not exist.', $shortcode_type, $shortcode_id ) );
		}

		return $shortcode;
	}

	/**
	 * Get results page
	 *
	 * @param $shortcode
	 *
	 * @return string
	 */
	public function get_results_page( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_RESULTS_PAGE ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_RESULTS_PAGE ] : '';
	}

	/**
	 * Get shortcode layout id
	 *
	 * @param $shortcode
	 *
	 * @return string
	 */
	public function get_shortcode_layout_id( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] : '';
	}

	/**
	 * Get shortcode group id
	 *
	 * @param $shortcode
	 *
	 * @return string
	 */
	public function get_shortcode_group_id( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] : '';
	}

	/**
	 * Get shortcode regexp lines
	 *
	 * @param $shortcode
	 *
	 * @return string
	 */
	public function get_shortcode_url_regexp_lines( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] : '';
	}

	/**
	 * Get shortcode is_debug_js
	 *
	 * @param $shortcode
	 *
	 * @return boolean
	 */
	public function get_shortcode_is_debug_js( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_IS_DEBUG_JS ] );
	}

	/**
	 * Get shortcode is_show_when_empty
	 *
	 * @param $shortcode
	 *
	 * @return boolean
	 */
	public function get_shortcode_is_show_when_empty( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY ] );
	}

	/**
	 * Get shortcode is_show_title_on_front_end
	 *
	 * @param $shortcode
	 *
	 * @return boolean
	 */
	public function get_shortcode_is_show_title_on_front_end( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END ] );
	}

	/**
	 * Get shortcode title
	 *
	 * @param $shortcode
	 *
	 * @return string
	 */
	public function get_shortcode_title( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_TITLE ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_TITLE ] : '';
	}

	/**
	 * Get shortcode before_title
	 *
	 * @param $shortcode
	 *
	 * @return string
	 */
	public function get_shortcode_before_title( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_BEFORE_TITLE ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_BEFORE_TITLE ] : '';
	}

	/**
	 * Get shortcode after_title
	 *
	 * @param $shortcode
	 *
	 * @return string
	 */
	public function get_shortcode_after_title( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_AFTER_TITLE ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_AFTER_TITLE ] : '';
	}

	/**
	 * Get shortcode before_ui
	 *
	 * @param $shortcode
	 *
	 * @return string
	 */
	public function get_shortcode_before_ui( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_BEFORE_UI ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_BEFORE_UI ] : '';
	}

	/**
	 * Get shortcode after_ui
	 *
	 * @param $shortcode
	 *
	 * @return string
	 */
	public function get_shortcode_after_ui( $shortcode ) {

		return ! empty( $shortcode[ WPSOLR_UI::FORM_FIELD_AFTER_UI ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_AFTER_UI ] : '';
	}

	/**
	 * Format a string translation
	 *
	 * @param $name
	 * @param $text
	 * @param $domain
	 * @param $is_multiligne
	 *
	 * @return array
	 */
	protected function get_string_to_translate( $name, $text, $domain, $is_multiligne ) {

		return [
			'name'          => $name,
			'text'          => $text,
			'domain'        => $domain,
			'is_multiligne' => $is_multiligne
		];
	}

	/**
	 * Get the strings to translate among the selected facets data
	 * @return array
	 */
	public function get_strings_to_translate() {

		$results = [ ];
		$domain  = 'wpsolr shortcodes'; // never change this

		// Fields that can be translated and their definition
		$fields_translatable = [
			WPSOLR_UI::FORM_FIELD_TITLE => [ 'name' => 'Shortcode title', 'is_multiline' => false ]
		];

		$shortcodes_types = WPSOLR_Global::getOption()->get_option_shortcodes();

		foreach ( $shortcodes_types as $shortcode_type => $shortcodes ) {

			foreach ( $shortcodes as $shortcode_id => $field ) {

				foreach ( $fields_translatable as $translatable_name => $translatable ) {

					if ( ! empty( $field[ $translatable_name ] ) ) {

						$results[] = $this->get_string_to_translate(
							$field[ $translatable_name ],
							$field[ $translatable_name ],
							$domain,
							$translatable['is_multiline']
						);
					}

				}
			}
		}


		return $results;
	}

}