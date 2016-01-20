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

	// Form fields
	const FORM_FIELD_FACETS_GROUP_ID = 'facets_group_id';
	const FORM_FIELD_FACETS_GROUP_NAME = 'name';

	protected static $wpsolr_layouts = [
		self::TYPE_GROUP_LAYOUT         => [
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
		],
		self::TYPE_GROUP_ELEMENT_LAYOUT => [
			'radiobox' => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Radio boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/checkbox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/radiobox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/radiobox/js.twig'
			],
			'checkbox' => [
				self::LAYOUT_FIELD_TEMPLATE_NAME => 'Check boxes',
				self::LAYOUT_FIELD_TEMPLATE_HTML => 'generic/facets/checkbox/html.twig',
				self::LAYOUT_FIELD_TEMPLATE_CSS  => 'generic/facets/checkbox/css.twig',
				self::LAYOUT_FIELD_TEMPLATE_JS   => 'generic/facets/checkbox/js.twig'
			]
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

	public
	function form(
		$instance
	) {
		parent::form( $instance );

		// Add facets group selection
		$facets_group_id = ! empty( $instance[ self::FORM_FIELD_FACETS_GROUP_ID ] ) ? $instance[ self::FORM_FIELD_FACETS_GROUP_ID ] : '';
		$facets_groups   = WPSOLR_Global::getOption()->get_facets_groups();
		?>

		<p>
			Use facets group:
			<select id="<?php echo $this->get_field_id( self::FORM_FIELD_FACETS_GROUP_ID ); ?>"
			        name="<?php echo $this->get_field_name( self::FORM_FIELD_FACETS_GROUP_ID ); ?>">
				<?php foreach ( $facets_groups as $facet_group_id => $facets_group ) { ?>
					<option
						value="<?php echo $facet_group_id; ?>" <?php selected( $facets_group_id, $facet_group_id, true ) ?>><?php echo $facets_group[ self::FORM_FIELD_FACETS_GROUP_NAME ]; ?></option>
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

		// Facets group of the widget
		$facets_group_id = $this->wpsolr_get_instance_facets_group_id( $instance );
		if ( empty( $facets_group_id ) ) {
			throw new Exception( sptrinf( 'Select a facets group.' ) );
		}

		// Facets of the widget facets groups
		$facets = WPSOLR_Global::getExtensionFacets()->get_facets_from_group( $facets_group_id );

		// Call and get Solr results
		$results = WPSOLR_Global::getSolrClient()->display_results( WPSOLR_Global::getQuery()->set_wpsolr_facets_fields( $facets ) );

		// Build the facets UI
		echo WPSOLR_UI_Facets::Build(
			$this->wpsolr_get_instance_layout( $instance, self::TYPE_GROUP_LAYOUT ),
			WPSOLR_Data_Facets::get_data(
				WPSOLR_Global::getQuery()->get_filter_query_fields_group_by_name(),
				$facets,
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

	/**
	 * Get the facets group id from the widget instance
	 *
	 * @param $instance
	 *
	 * @return string facets group id
	 */
	private function wpsolr_get_instance_facets_group_id( $instance ) {

		return ! empty( $instance[ self::FORM_FIELD_FACETS_GROUP_ID ] ) ? $instance[ self::FORM_FIELD_FACETS_GROUP_ID ] : '';
	}

	/**
	 * Get all facets layouts
	 * @return array
	 */
	public static function get_facets_layouts() {
		return self::wpsolr_get_layout_definitions( self::TYPE_GROUP_ELEMENT_LAYOUT );
	}
}