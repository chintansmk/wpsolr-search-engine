<?php

namespace wpsolr\ui\widget;

use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\ui\widget;
use wpsolr\ui\WPSOLR_Data_Sort;
use wpsolr\ui\WPSOLR_UI_Sort;
use wpsolr\utilities\WPSOLR_Global;

/**
 * WPSOLR Widget Sort List
 */
class WPSOLR_Widget_Sort extends WPSOLR_Widget {

	protected static $wpsolr_layouts = [
		self::TYPE_GROUP_LAYOUT => [
			'default'        => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR'
			],
			'customizr'      => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Customizr'
			],
			'graphene'       => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Graphene'
			],
			'hueman'         => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Hueman'
			],
			'spacious'       => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Spacious'
			],
			'twentyten'      => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Ten'
			],
			'twentyeleven'   => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Eleven'
			],
			'twentythirteen' => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Thirteen'
			],
			'twentyfourteen' => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Fourteen'
			],
			'twentyfifteen'  => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Fifteen'
			],
			'twentysixteen'  => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Sixteen'
			],
			'responsive'     => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Responsive'
			],
			'vantage'        => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Vantage'
			],
			'virtue'         => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Virtue'
			],
			'zerif-lite'     => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sort/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sort/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sort/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Zerif Lite'
			]
		]
	];

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'wpsolr_widget_sort', // Base ID
			__( 'WPSOLR Sort List', 'wpsolr_admin' ), // Name
			array( 'description' => __( 'Display Solr sort options', 'wpsolr_admin' ) ), // Args
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

		echo WPSOLR_UI_Sort::Build(
			$this->wpsolr_get_instance_layout( $instance, self::TYPE_GROUP_LAYOUT ), WPSOLR_Data_Sort::get_data(
			WPSOLR_Global::getQuery()->get_wpsolr_sort(),
			WPSOLR_Global::getOption()->get_sortby_items_as_array(),
			WPSOLR_Global::getOption()->get_sortby_default(),
			WPSOLR_Localization::get_options()
		), WPSOLR_Localization::get_options(), $args, $instance
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

}