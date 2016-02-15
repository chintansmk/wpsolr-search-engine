<?php
use Solarium\QueryType\Select\Query\Query;
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Option;

?>

<?php
$facet_option_array_name = sprintf( '%s[%s][%s][%s]', $options_name, WPSOLR_Option::OPTION_FACETS_FACETS, $facets_group_uuid, $facet_name );

$facet_layout_id        = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_FACET_LAYOUT_ID ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_FACET_LAYOUT_ID ] : '';
$facet_filter_layout_id = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_FILTER_LAYOUT_ID ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_FILTER_LAYOUT_ID ] : '';

$facet_range_start = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_START ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_START ] : WPSOLR_Options_Facets::FACET_FILED_RANGE_START_DEFAULT;
$facet_range_end   = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_END ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_END ] : WPSOLR_Options_Facets::FACET_FIELD_RANGE_END_DEFAULT;
$facet_range_gap   = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_GAP ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_GAP ] : WPSOLR_Options_Facets::FACET_FIELD_RANGE_GAP_DEFAULT;

$facet_slider_start = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_START ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_START ] : WPSOLR_Options_Facets::FACET_FILED_RANGE_START_DEFAULT;
$facet_slider_end   = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_END ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_END ] : WPSOLR_Options_Facets::FACET_FIELD_RANGE_END_DEFAULT;
$facet_min_max_step = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX ][ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX_STEP ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX ][ WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX_STEP ] : WPSOLR_Options_Facets::FACET_FIELD_MIN_MAX_STEP_DEFAULT;

$facet_custom_range_ranges = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_RANGES ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_RANGES ] : WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_RANGES_DEFAULT;

$facet_elements_operator = ! empty( $facet['elements_operator'] ) ? $facet['elements_operator'] : 'AND';
$facet_sort              = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_SORT ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_SORT ] : WPSOLR_Options_Facets::FACET_SORT_COUNT;
$facet_min_count         = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_COUNT ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_MIN_COUNT ] : '0';
$facet_js_delay_in_ms    = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_JS_REFRESH_DELAY_IN_MS ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_JS_REFRESH_DELAY_IN_MS ] : '0';
$facet_is_exclusion_tag  = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_IS_EXCLUSION ] );
$facet_is_active         = ! empty( $facet['is_active'] ) ? $facet['is_active'] : '0';
$facet_label             = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL ] : '';
$facet_label_first       = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL_FIRST ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL_FIRST ] : $facet_label;
$facet_label_last        = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL_LAST ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL_LAST ] : $facet_label;

$facet_query = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE ] : [ ];

$facet_field_name_front_end = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL_FRONT_END ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL_FRONT_END ] : $facet_name;

?>

<li class='facets <?php echo $facet_selected_class; ?>'>
	<div>
		<a><?php echo $facet_name; ?></a>
		<img src='<?php echo $image_plus; ?>' class='plus_icon' style='display:<?php echo $image_plus_display; ?>'>
		<img src='<?php echo $image_minus; ?>' class='minus_icon' style='display:<?php echo $image_minus_display; ?>'
		     title='Click to Remove the Facet'>
	</div>
	<div id='<?php echo $facets_group_uuid . '_' . $facet_name; ?>'>
		<input type='hidden'
		       name='<?php echo $facet_option_array_name; ?>[name]'
		       value='<?php echo esc_attr( $facet_name ); ?>'/>

		<div class="wdm_row">
			<div class='col_left'>
				Show this facet in this group
			</div>
			<div class='col_right'>
				<input type='checkbox' id="<?php echo $facets_group_uuid . '_' . $facet_name; ?>_is_active"
				       name='<?php echo $facet_option_array_name; ?>[is_active]' value='1'
					<?php checked( '1', $facet_is_active, true ); ?>/>
			</div>
			<div class="clear"></div>
		</div>

		<div class="wdm_row">
			<div class='col_left'>
				Label displayed on front-end<br/>
				Translated as string.
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL_FRONT_END; ?>]'
				       value='<?php echo esc_attr( $facet_field_name_front_end ); ?>'/>
			</div>
			<div class="clear"></div>
		</div>

		<div class="wdm_row">
			<div class='col_left'>
				Display in widget "WPSOLR Facet" as
			</div>
			<div class='col_right'>
				<select class="wpsolr_layout_select"
				        name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_FACET_LAYOUT_ID; ?>]'>
					<?php foreach ( $layouts_facets as $layout_id => $layout ) { ?>
						<option
							value='<?php echo $layout_id; ?>' <?php selected( $layout_id, $facet_layout_id, true ); ?>><?php echo $layout['name']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="clear"></div>
		</div>

		<div class="wdm_row">
			<div class='col_left'>
				Display in widget "WPSOLR filter" as
			</div>
			<div class='col_right'>
				<select class="wpsolr_filters_layout_select"
				        name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_FILTER_LAYOUT_ID; ?>]'>
					<?php foreach ( $layouts_filters as $layout_id => $layout ) { ?>
						<option
							value='<?php echo $layout_id; ?>' <?php selected( $layout_id, $facet_filter_layout_id, true ); ?>><?php echo $layout['name']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="clear"></div>
		</div>

		<!-- Facet common section-->
		<div class="wdm_row">
			<div class='col_left'>
				Delay in milliseconds before a click on the facet refreshes the page
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_JS_REFRESH_DELAY_IN_MS; ?>]'
				       value='<?php echo esc_attr( $facet_js_delay_in_ms ); ?>'/> ms
			</div>
			<div class="clear"></div>
		</div>

		<div class="wdm_row">
			<div class='col_left'>
				Show if count greater or equal than
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_MIN_COUNT; ?>]'
				       value='<?php echo esc_attr( $facet_min_count ); ?>'/>
			</div>
			<div class="clear"></div>
		</div>

		<div class="wdm_row">
			<div class='col_left'>
				Show facet count as if no selection was made
			</div>
			<div class='col_right'>
				<input type="checkbox"
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_IS_EXCLUSION; ?>]'
				       value='1' <?php checked( $facet_is_exclusion_tag ); ?> />
			</div>
			<div class="clear"></div>
		</div>

		<div class="wdm_row">
			<div class='col_left'>
				Operator inside the facet
			</div>
			<div class='col_right'>
				<select
					name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_QUERY_OPERATOR; ?>]'>
					<option
						value='<?php echo Query::QUERY_OPERATOR_AND; ?>' <?php selected( Query::QUERY_OPERATOR_AND, $facet_elements_operator, true ); ?> ><?php echo Query::QUERY_OPERATOR_AND; ?>
					</option>
					<option
						value='<?php echo Query::QUERY_OPERATOR_OR; ?>' <?php selected( Query::QUERY_OPERATOR_OR, $facet_elements_operator, true ); ?> ><?php echo Query::QUERY_OPERATOR_OR; ?></option>
				</select>
			</div>
			<div class="clear"></div>
		</div>

		<div class="wdm_row">
			<div class='col_left'>
				Sort by
			</div>
			<div class='col_right'>
				<select name='<?php echo $facet_option_array_name; ?>[sort]'>
					<option
						value='<?php echo WPSOLR_Options_Facets::FACET_SORT_COUNT ?>' <?php selected( 'count', $facet_sort, true ); ?>>
						Count
					</option>
					<option
						value='<?php echo WPSOLR_Options_Facets::FACET_SORT_ALPHABETICAL ?>' <?php selected( 'index', $facet_sort, true ); ?>>
						Alphabetical
					</option>
				</select>
			</div>
			<div class="clear"></div>
		</div>

		<?php
		// Facet field section
		WPSOLR_Extensions::require_with(
			WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet_field.inc.php' ),
			[
				'facet_option_array_name' => $facet_option_array_name,
				'facet_label_first'       => $facet_label_first,
				'facet_label'             => $facet_label,
				'facet_label_last'        => $facet_label_last
			]
		);
		?>

		<?php
		// Facet range section
		WPSOLR_Extensions::require_with(
			WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet_range.inc.php' ),
			[
				'facet_option_array_name' => $facet_option_array_name,
				'facet_label_first'       => $facet_label_first,
				'facet_label'             => $facet_label,
				'facet_label_last'        => $facet_label_last,
				'facet_range_start'       => $facet_range_start,
				'facet_range_end'         => $facet_range_end,
				'facet_range_gap'         => $facet_range_gap
			]
		);
		?>

		<?php
		// Facet custom range section
		WPSOLR_Extensions::require_with(
			WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet_custom_range.inc.php' ),
			[
				'facet_option_array_name'   => $facet_option_array_name,
				'facet_custom_range_ranges' => $facet_custom_range_ranges
			]
		);
		?>

		<?php
		// Facet min/max section
		WPSOLR_Extensions::require_with(
			WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet_min_max.inc.php' ),
			[
				'facet_option_array_name' => $facet_option_array_name,
				'facet_label'             => $facet_label,
				'facet_min_max_step'      => $facet_min_max_step
			]
		);
		?>

	</div>
</li>