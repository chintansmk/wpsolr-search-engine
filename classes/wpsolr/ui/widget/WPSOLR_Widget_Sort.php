<?php

namespace wpsolr\ui\widget;

use wpsolr\exceptions\WPSOLR_Exception;
use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\ui\widget;
use wpsolr\ui\WPSOLR_Data_Sort;
use wpsolr\ui\WPSOLR_UI_Sort;
use wpsolr\utilities\WPSOLR_Global;

/**
 * WPSOLR Widget Sort List
 */
class WPSOLR_Widget_Sort extends WPSOLR_Widget {

	// Form fields
	const FORM_FIELD_SORTS_GROUP_ID = 'sorts_group_id';
	const FORM_FIELD_SORTS_GROUP_NAME = 'name';

	protected static $wpsolr_layouts = [
		self::TYPE_GROUP_LAYOUT         => [
			'default'        => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'WPSOLR'
			],
			'customizr'      => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Customizr'
			],
			'graphene'       => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Graphene'
			],
			'hueman'         => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Hueman'
			],
			'spacious'       => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Spacious'
			],
			'twentyten'      => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Ten'
			],
			'twentyeleven'   => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Eleven'
			],
			'twentythirteen' => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Thirteen'
			],
			'twentyfourteen' => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Fourteen'
			],
			'twentyfifteen'  => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Fifteen'
			],
			'twentysixteen'  => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Twenty Sixteen'
			],
			'responsive'     => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Responsive'
			],
			'vantage'        => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Vantage'
			],
			'virtue'         => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Virtue'
			],
			'zerif-lite'     => [
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/sorts/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/sorts/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/sorts/select/js.twig',
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Zerif Lite'
			]
		],
		self::TYPE_GROUP_ELEMENT_LAYOUT => [
			'Select' => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Select',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/select/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/select/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/select/js.twig'
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

	public
	function form(
		$instance
	) {
		parent::form( $instance );

		// Add facets group selection
		$group_id = ! empty( $instance[ self::FORM_FIELD_SORTS_GROUP_ID ] ) ? $instance[ self::FORM_FIELD_SORTS_GROUP_ID ] : '';
		$groups   = WPSOLR_Global::getOption()->get_sorts_groups();
		?>

		<p>
			Use facets group:
			<select id="<?php echo $this->get_field_id( self::FORM_FIELD_SORTS_GROUP_ID ); ?>"
			        name="<?php echo $this->get_field_name( self::FORM_FIELD_SORTS_GROUP_ID ); ?>">
				<?php foreach ( $groups as $current_group_id => $current_group ) { ?>
					<option
						value="<?php echo $current_group_id; ?>" <?php selected( $group_id, $current_group_id, true ) ?>><?php echo $current_group[ self::FORM_FIELD_SORTS_GROUP_NAME ]; ?></option>
				<?php } ?>
			</select>
		</p>

		<?php
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

		// Widget can be on a search page ?s=
		$wpsolr_query = WPSOLR_Global::getQuery();
		$group_id     = $wpsolr_query->get_wpsolr_sorts_groups_id();

		if ( ! $wpsolr_query->get_wpsolr_is_search() ) {

			// Sorts of the group on the query url
			if ( empty( $group_id ) ) {

				// Sorts group of the widget
				$group_id = $this->wpsolr_get_instance_sorts_group_id( $instance );
				if ( empty( $group_id ) ) {
					throw new WPSOLR_Exception( sprintf( 'Select a sort group.' ) );
				}
			}

		}

		// Sorts of the Sorts group
		$sorts = WPSOLR_Global::getExtensionSorts()->get_sorts_from_group( $group_id );

		echo WPSOLR_UI_Sort::Build(
			$group_id,
			WPSOLR_Data_Sort::get_data(
				WPSOLR_Global::getQuery()->get_wpsolr_sort(),
				$sorts,
				WPSOLR_Global::getExtensionSorts()->get_sort_default_name( WPSOLR_Global::getExtensionSorts()->get_sorts_group( $group_id ) ),
				WPSOLR_Localization::get_options()
			),
			WPSOLR_Localization::get_options(),
			$args,
			$instance,
			$this->wpsolr_get_instance_layout( $instance, self::TYPE_GROUP_LAYOUT )
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

	/**
	 * Get all facets layouts
	 * @return array
	 */
	public static function get_sorts_layouts() {
		return self::wpsolr_get_layout_definitions( self::TYPE_GROUP_ELEMENT_LAYOUT );
	}

	/**
	 * Get the sorts group id from the widget instance
	 *
	 * @param $instance
	 *
	 * @return string Sorts group id
	 */
	private function wpsolr_get_instance_sorts_group_id( $instance ) {

		return ! empty( $instance[ self::FORM_FIELD_SORTS_GROUP_ID ] ) ? $instance[ self::FORM_FIELD_SORTS_GROUP_ID ] : '';
	}

}