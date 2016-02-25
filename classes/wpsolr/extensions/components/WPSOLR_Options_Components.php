<?php

namespace wpsolr\extensions\components;

use Solarium\QueryType\Select\Query\Query;
use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\ui\shortcode\WPSOLR_Shortcode_Facet;
use wpsolr\ui\shortcode\WPSOLR_Shortcode_Filter;
use wpsolr\ui\shortcode\WPSOLR_Shortcode_Result_Row;
use wpsolr\ui\shortcode\WPSOLR_Shortcode_Sort;
use wpsolr\ui\WPSOLR_UI;
use wpsolr\ui\WPSOLR_UI_Facet;
use wpsolr\ui\WPSOLR_UI_Filter;
use wpsolr\ui\WPSOLR_UI_Result_Row;
use wpsolr\ui\WPSOLR_UI_Sort;
use wpsolr\utilities\WPSOLR_Global;

/**
 * Class WPSOLR_Options_Components
 *
 * Generate components
 */
class WPSOLR_Options_Components extends WPSOLR_Extensions {

	// Component types
	const COMPONENT_TYPE_FACETS = 'facets';
	const COMPONENT_TYPE_SORTS = 'sorts';
	const COMPONENT_TYPE_FILTERS = 'filters';
	const COMPONENT_TYPE_RESULTS_HEADER = 'results_header';
	const COMPONENT_TYPE_RESULTS_NAVIGATION = 'results_navigation';
	const COMPONENT_TYPE_RESULTS_ROWS = 'results_rows';
	const COMPONENT_TYPE_SEARCH_FORM = 'search_form';

	// Form fields
	const COMPONENT_FIELD_LABEL = 'label';
	const COMPONENT_FIELD_UI = 'ui';
	const COMPONENT_FIELD_SHORTCODE_NAME = 'shortcode_name';
	const SHORTCODE_FIELD_CODE = 'shortcode_code';
	const COMPONENT_FIELD_TYPE = 'type';

	/**
	 * Post constructor.
	 */
	protected function post_constructor() {

	}

	/**
	 * Component definitions
	 * @return array[WPSOLR_UI]
	 */
	protected static function components_types() {
		return [
			self::COMPONENT_TYPE_FACETS             => [
				self::COMPONENT_FIELD_LABEL          => 'Facets',
				self::COMPONENT_FIELD_SHORTCODE_NAME => ( new WPSOLR_Shortcode_Facet() )->get_shortcode_name(),
				self::COMPONENT_FIELD_UI             => new WPSOLR_UI_Facet()
			],
			self::COMPONENT_TYPE_SORTS              => [
				self::COMPONENT_FIELD_LABEL          => 'Sorts',
				self::COMPONENT_FIELD_SHORTCODE_NAME => ( new WPSOLR_Shortcode_Sort() )->get_shortcode_name(),
				self::COMPONENT_FIELD_UI             => new WPSOLR_UI_Sort()
			],
			self::COMPONENT_TYPE_FILTERS            => [
				self::COMPONENT_FIELD_LABEL          => 'Filters',
				self::COMPONENT_FIELD_SHORTCODE_NAME => ( new WPSOLR_Shortcode_Filter() )->get_shortcode_name(),
				self::COMPONENT_FIELD_UI             => new WPSOLR_UI_Filter()
			],
			self::COMPONENT_TYPE_SEARCH_FORM        => [
				self::COMPONENT_FIELD_LABEL          => 'Search box',
				self::COMPONENT_FIELD_SHORTCODE_NAME => ( new WPSOLR_Shortcode_Facet() )->get_shortcode_name(),
				self::COMPONENT_FIELD_UI             => new WPSOLR_UI_Facet()
			],
			self::COMPONENT_TYPE_RESULTS_HEADER     => [
				self::COMPONENT_FIELD_LABEL          => 'Results header',
				self::COMPONENT_FIELD_SHORTCODE_NAME => ( new WPSOLR_Shortcode_Facet() )->get_shortcode_name(),
				self::COMPONENT_FIELD_UI             => new WPSOLR_UI_Facet()
			],
			self::COMPONENT_TYPE_RESULTS_ROWS       => [
				self::COMPONENT_FIELD_LABEL          => 'Results rows',
				self::COMPONENT_FIELD_SHORTCODE_NAME => ( new WPSOLR_Shortcode_Result_Row() )->get_shortcode_name(),
				self::COMPONENT_FIELD_UI             => new WPSOLR_UI_Result_Row()
			],
			self::COMPONENT_TYPE_RESULTS_NAVIGATION => [
				self::COMPONENT_FIELD_LABEL          => 'Results page navigation',
				self::COMPONENT_FIELD_SHORTCODE_NAME => ( new WPSOLR_Shortcode_Facet() )->get_shortcode_name(),
				self::COMPONENT_FIELD_UI             => new WPSOLR_UI_Facet()
			],
		];
	}

	/**
	 * Display admin form.
	 *
	 * @param array $plugin_parameters Parameters
	 */
	public function output_form( $form_file = null, $plugin_parameters = [ ] ) {

		// Clone some components
		$components = WPSOLR_Global::getOption()->get_option_components();
		if ( ! is_array( $components ) ) {
			$components = [ ];
		}
		$components = $this->clone_some_components( $components );

		// Generate components code
		$this->generate_codes( $components );

		// Add new uuid to components types
		$new_components      = [ ];
		$new_component_uuids = [ ];
		foreach ( $this->components_types() as $component_type => $component ) {

			$new_uuid = WPSOLR_Global::getExtensionIndexes()->generate_uuid();

			$new_component_uuids[]             = $new_uuid;
			$new_components[ $component_type ] = [
				$new_uuid => [
					WPSOLR_UI::FORM_FIELD_TITLE          => 'New component',
					WPSOLR_UI::FORM_FIELD_COMPONENT_TYPE => $component_type
				]
			];
		}

		// Add current plugin parameters to default parent parameters
		parent::output_form(
			$form_file,
			array_merge(
				[
					'options'             => WPSOLR_Global::getOption()->get_option_components(),
					'components_types'    => $this->components_types(),
					'components'          => $components,
					'new_component_uuids' => $new_component_uuids,
					'new_components'      => $new_components
				],
				$plugin_parameters
			)
		);
	}

	/**
	 * Generate the code of each component
	 *
	 * @param $components
	 */
	public function generate_codes( &$components ) {

		$components_types = self::components_types();

		foreach ( $components as $component_type_name => &$component_type ) {

			foreach ( $component_type as $component_uuid => &$component ) {

				$component[ self::SHORTCODE_FIELD_CODE ] = sprintf( '[%s name="%s" id="%s"]',
					$components_types[ $component_type_name ][ self::COMPONENT_FIELD_SHORTCODE_NAME ], $component[ WPSOLR_UI::FORM_FIELD_TITLE ], $component_uuid );
			}
		}
	}

	/**
	 * Clone the components marked.
	 *
	 * @param $components
	 */
	public function clone_some_components( &$components ) {

		foreach ( $components as $component_type_name => &$component_type ) {

			foreach ( $component_type as $component_uuid => &$component ) {

				if ( ! empty( $component['is_to_be_cloned'] ) ) {

					unset( $component['is_to_be_cloned'] );

					// Clone the component
					$component_cloned                                = $component;
					$component_cloned_uuid                           = WPSOLR_Global::getExtensionIndexes()->generate_uuid();
					$component_cloned[ WPSOLR_UI::FORM_FIELD_TITLE ] = 'Clone of ' . $component_cloned[ WPSOLR_UI::FORM_FIELD_TITLE ];

					$components[ $component_type_name ][ $component_cloned_uuid ] = $component_cloned;

				}

			}
		}

		return $components;
	}

	/**
	 * Retrieve a component by id
	 *
	 * @param $component_id
	 *
	 * @return array Component
	 * @throws WPSOLR_Exception
	 */
	public function get_component_by_id( $component_id ) {

		if ( empty( $component_id ) ) {
			throw new WPSOLR_Exception( sprintf( 'component id is missing.' ) );
		}

		$components = WPSOLR_Global::getOption()->get_option_components();
		foreach ( $components as $component_type => $components ) {
			if ( isset( $components[ $component_id ] ) ) {
				return $components[ $component_id ];
			}
		}

		throw new WPSOLR_Exception( sprintf( 'component with id %s does not exist.', $component_id ) );
	}


	/**
	 * Get all components of a type
	 *
	 * @param $component_type
	 *
	 * @return array
	 */
	public function get_components_from_type( $component_type ) {

		$components = WPSOLR_Global::getOption()->get_option_components();

		return isset( $components[ $component_type ] ) ? $components[ $component_type ] : [ ];
	}


	/**
	 * Get results page
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_results_page( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_RESULTS_PAGE ] ) ? $component[ WPSOLR_UI::FORM_FIELD_RESULTS_PAGE ] : '';
	}

	/**
	 * Get results category
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_results_category( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_RESULTS_CATEGORY ] ) ? $component[ WPSOLR_UI::FORM_FIELD_RESULTS_CATEGORY ] : '';
	}

	/**
	 * Get search method
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_search_method( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_SEARCH_METHOD ] ) ? $component[ WPSOLR_UI::FORM_FIELD_SEARCH_METHOD ] : WPSOLR_UI::FORM_FIELD_SEARCH_METHOD_VALUE_USE_CUSTOM_PAGE;
	}


	/**
	 * Get component layout id
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_component_layout_id( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] ) ? $component[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] : '';
	}

	/**
	 * Get component group id
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_component_group_id( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] ) ? $component[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] : '';
	}

	/**
	 * Get component regexp lines
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_component_url_regexp_lines( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] ) ? $component[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] : '';
	}

	/**
	 * Get component is_debug_js
	 *
	 * @param $component
	 *
	 * @return boolean
	 */
	public function get_component_is_debug_js( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_IS_DEBUG_JS ] );
	}

	/**
	 * Get component is_own_ajax
	 *
	 * @param $component
	 *
	 * @return boolean
	 */
	public function get_component_is_own_ajax( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_IS_OWN_AJAX ] );
	}

	/**
	 * Get component is_show_when_empty
	 *
	 * @param $component
	 *
	 * @return boolean
	 */
	public function get_component_is_show_when_empty( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY ] );
	}

	/**
	 * Get component is_show_title_on_front_end
	 *
	 * @param $component
	 *
	 * @return boolean
	 */
	public function get_component_is_show_title_on_front_end( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END ] );
	}

	/**
	 * Get component title
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_component_title( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_TITLE ] ) ? $component[ WPSOLR_UI::FORM_FIELD_TITLE ] : '';
	}

	/**
	 * Get component before_title
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_component_before_title( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_BEFORE_TITLE ] ) ? $component[ WPSOLR_UI::FORM_FIELD_BEFORE_TITLE ] : '';
	}

	/**
	 * Get component after_title
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_component_after_title( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_AFTER_TITLE ] ) ? $component[ WPSOLR_UI::FORM_FIELD_AFTER_TITLE ] : '';
	}

	/**
	 * Get component before_ui
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_component_before_ui( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_BEFORE_UI ] ) ? $component[ WPSOLR_UI::FORM_FIELD_BEFORE_UI ] : '';
	}

	/**
	 * Get component type
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_component_type( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_COMPONENT_TYPE ] ) ? $component[ WPSOLR_UI::FORM_FIELD_COMPONENT_TYPE ] : '';
	}

	/**
	 * Get component UI
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_component_ui( $component_type ) {

		if ( ! empty( $component_type ) && ! empty( $this->components_types()[ $component_type ] ) ) {
			return $this->components_types()[ $component_type ][ self::COMPONENT_FIELD_UI ];
		}

		throw new WPSOLR_Exception( sprintf( 'unknown component type %s', $component_type ) );
	}

	/**
	 * Get component after_ui
	 *
	 * @param $component
	 *
	 * @return string
	 */
	public function get_component_after_ui( $component ) {

		return ! empty( $component[ WPSOLR_UI::FORM_FIELD_AFTER_UI ] ) ? $component[ WPSOLR_UI::FORM_FIELD_AFTER_UI ] : '';
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
		$domain  = 'wpsolr components'; // never change this

		// Fields that can be translated and their definition
		$fields_translatable = [
			WPSOLR_UI::FORM_FIELD_TITLE => [ 'name' => 'Component title', 'is_multiline' => false ]
		];

		$components_types = WPSOLR_Global::getOption()->get_option_components();

		foreach ( $components_types as $component_type => $components ) {

			foreach ( $components as $component_id => $field ) {

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