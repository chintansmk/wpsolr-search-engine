<?php

namespace wpsolr\ui\widget;

use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\ui\widget;
use wpsolr\utilities\WPSOLR_Global;

/**
 * WPSOLR Widget Sort List
 */
class WPSOLR_Widget_Filter extends WPSOLR_Widget_facet {

	protected static $wpsolr_layouts = [
		self::TYPE_GROUP_LAYOUT         => [
			self::GENERIC_LAYOUT_ID => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'List',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/filters/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/filters/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/filters/js.twig'
			]
		],
		self::TYPE_GROUP_ELEMENT_LAYOUT => [
			'checkbox' => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Check boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/filters/checkbox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/filters/checkbox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/filters/checkbox/js.twig'
			],
			'radiobox' => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Radio boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/filters/checkbox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/filters/radiobox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/filters/checkbox/js.twig'
			]
		]
	];

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		WPSOLR_Widget::__construct(
			'wpsolr_widget_filter', // Base ID
			__( 'WPSOLR Filters', 'wpsolr_admin' ), // Name
			array( 'description' => __( 'Display filters selected', 'wpsolr_admin' ) ), // Args
			array() // controls
		);
	}

	/**
	 * Write the widget header
	 *
	 * @param $instance
	 */
	protected function wpsolr_header( $instance ) {
		?>

		<p>
			Display a list of elements that a user can sort the Solr results with. Sort options must have been defined
			in WPSOLR admin pages.
		</p>

		<?php
	}

	public function wpsolr_get_groups() {
		return WPSOLR_Global::getOption()->get_facets_groups();
	}

	protected function wpsolr_get_facet_type() {
		return WPSOLR_Options_Facets::FACET_FIELD_FILTER_LAYOUT_ID;
	}

}