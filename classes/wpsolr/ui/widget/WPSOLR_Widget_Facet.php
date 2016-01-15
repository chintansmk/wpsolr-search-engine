<?php

namespace wpsolr\ui\widget;

use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\ui\widget;
use wpsolr\ui\WPSOLR_Data_Facets;
use wpsolr\ui\WPSOLR_UI_Facets;
use wpsolr\utilities\WPSOLR_Global;

/**
 * WPSOLR Widget Facets.
 */
class WPSOLR_Widget_Facet extends WPSOLR_Widget {

	protected static $wpsolr_layouts = [
		'default'        => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'wpsolr/facets_html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR'
		],
		'customizr'      => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Customizr'
		],
		'graphene'       => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Graphene'
		],
		'hueman'         => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Hueman'
		],
		'spacious'       => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Spacious'
		],
		'twentyten'      => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Ten'
		],
		'twentyeleven'   => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Eleven'
		],
		'twentytwelve'   => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Twelve'
		],
		'twentythirteen' => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Thirteen'
		],
		'twentyfourteen' => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Fourteen'
		],
		'twentyfifteen'  => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Fifteen'
		],
		'twentysixteen'  => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Sixteen'
		],
		'responsive'     => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'responsive/facets_css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Responsive'
		],
		'vantage'        => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Vantage'
		],
		'virtue'         => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Virtue'
		],
		'zerif-lite'     => [
			self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/html.twig',
			self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/css.twig',
			self::LAYOUT_FIELD_TEMPLATE_NAME => 'Zerif Lite'
		]
	];

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'wpsolr_widget_facets', // Base ID
			__( 'WPSOLR Facets', 'wpsolr_admin' ), // Name
			array( 'description' => __( 'Display Solr Facets', 'wpsolr_admin' ) ), // Args
			array() // controls
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	protected function wpsolr_form( $args, $instance ) {

		$results = WPSOLR_Global::getSolrClient()->display_results( WPSOLR_Global::getQuery() );

		echo WPSOLR_UI_Facets::Build(
			$this->wpsolr_get_instance_layout( $instance ),
			WPSOLR_Data_Facets::get_data(
				WPSOLR_Global::getQuery()->get_filter_query_fields_group_by_name(),
				WPSOLR_Global::getOption()->get_facets_selected_array(),
				$results[1]
			),
			WPSOLR_Localization::get_options(),
			$args
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
			Facets are dynamic filters that users can click on to filter out the search results, like categories, or
			tags. Facets
			must have been defined in WPSOLR admin pages.
		</p>

		<?php
	}

}