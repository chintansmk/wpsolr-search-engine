<?php
use wpsolr\extensions\facets\WPSOLR_Options_Facets;
use wpsolr\utilities\WPSOLR_Option;

?>

<?php
$facet_option_array_name = sprintf( '%s[%s][%s][%s]', $options_name, WPSOLR_Option::OPTION_FACETS_FACETS, $facets_group_uuid, $facet_name );

$facet_layout_id = ! empty( $facet['layout_id'] ) ? $facet['layout_id'] : '';

$facet_range_start = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_START ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_START ] : WPSOLR_Options_Facets::FACET_FILED_RANGE_START_DEFAULT;
$facet_range_end   = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_END ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_END ] : WPSOLR_Options_Facets::FACET_FIELD_RANGE_END_DEFAULT;
$facet_range_gap   = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_GAP ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_RANGE ][ WPSOLR_Options_Facets::FACET_FIELD_RANGE_GAP ] : WPSOLR_Options_Facets::FACET_FIELD_RANGE_GAP_DEFAULT;

$facet_query_ranges = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_QUERY ] ) && ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_QUERY ][ WPSOLR_Options_Facets::FACET_FIELD_QUERY_RANGES ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_QUERY ][ WPSOLR_Options_Facets::FACET_FIELD_QUERY_RANGES ] : WPSOLR_Options_Facets::FACET_FIELD_QUERY_RANGES_DEFAULT;

$facet_elements_operator = ! empty( $facet['elements_operator'] ) ? $facet['elements_operator'] : 'AND';
$facet_sort              = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_SORT ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_SORT ] : WPSOLR_Options_Facets::FACET_SORT_COUNT;
$facet_min_count         = ! empty( $facet['min_count'] ) ? $facet['min_count'] : '1';
$facet_is_exclusion_tag  = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_IS_EXCLUSION ] );
$facet_is_active         = ! empty( $facet['is_active'] ) ? $facet['is_active'] : '0';
$facet_label             = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL ] : '';
$facet_label_first       = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL_FIRST ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL_FIRST ] : $facet_label;
$facet_label_last        = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL_LAST ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_LABEL_LAST ] : $facet_label;

$facet_query = ! empty( $facet[ WPSOLR_Options_Facets::FACET_FIELD_QUERY ] ) ? $facet[ WPSOLR_Options_Facets::FACET_FIELD_QUERY ] : [ ];

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
				Display as
			</div>
			<div class='col_right'>
				<select class="wpsolr_layout_select"
				        name='<?php echo $facet_option_array_name; ?>[layout_id]'>
					<?php foreach ( $layouts as $layout_id => $layout ) { ?>
						<option
							value='<?php echo $layout_id; ?>' <?php selected( $layout_id, $facet_layout_id, true ); ?>><?php echo $layout['name']; ?></option>
					<?php } ?>
				</select>
				<!--
				<input name="button_clone_layout"
				       type="submit" class="button-primary wdm-save"
				       value="Clone the layout"/>
				<input name="button_edit_layout"
				       type="submit" class="button-primary wdm-save"
				       value="Edit the layout"/>
				-->
				<div class="clear"></div>
			</div>
		</div>

		<!-- Facet common section-->
		<div class="wdm_row">
			<div class='col_left'>
				Show if count greater or equal than
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $facet_option_array_name; ?>[min_count]'
				       value='<?php echo esc_attr( $facet_min_count ); ?>'/>
			</div>
		</div>
		<div class="wdm_row">
			<div class='col_left'>
				Show missing facet contents
			</div>
			<div class='col_right'>
				<input type="checkbox"
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_IS_EXCLUSION; ?>]'
				       value='1' <?php checked( $facet_is_exclusion_tag ); ?> />
			</div>
		</div>
		<div class="wdm_row">
			<div class='col_left'>
				Operator inside the facet
			</div>
			<div class='col_right'>
				<select name='<?php echo $facet_option_array_name; ?>[elements_operator]'>
					<option value='AND' <?php selected( 'AND', $facet_elements_operator, true ); ?>>AND
					</option>
					<option value='OR' <?php selected( 'OR', $facet_elements_operator, true ); ?>>OR</option>
				</select>
			</div>
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
		</div>

		<!-- Facet field labels section-->
		<div
			class="wdm_row wpsolr_facet_type wpsolr_<?php echo WPSOLR_Options_Facets::FACET_TYPE_FIELD; ?>">
			<div class='col_left'>
				First facet element label
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL_FIRST; ?>]'
				       value='<?php echo esc_attr( ! empty( $facet_label_first ) ? $facet_label_first : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE ); ?>'/>
			</div>
			<div class='col_left'>
				Middle facet element label
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL; ?>]'
				       value='<?php echo esc_attr( ! empty( $facet_label ) ? $facet_label : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE ); ?>'/>
			</div>
			<div class='col_left'>
				Last facet element label
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL_LAST; ?>]'
				       value='<?php echo esc_attr( ! empty( $facet_label_last ) ? $facet_label_last : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE ); ?>'/>
			</div>
		</div>

		<!-- Facet range section-->
		<div
			class="wdm_row wpsolr_facet_type wpsolr_<?php echo WPSOLR_Options_Facets::FACET_TYPE_RANGE; ?>">
			<div class='col_left'>
				First facet element label
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL_FIRST; ?>]'
				       value='<?php echo esc_attr( ! empty( $facet_label_first ) ? $facet_label_first : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE_RANGE ); ?>'/>
			</div>
			<div class='col_left'>
				Middle facet element label
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL; ?>]'
				       value='<?php echo esc_attr( ! empty( $facet_label ) ? $facet_label : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE_RANGE ); ?>'/>
			</div>
			<div class='col_left'>
				Last facet element label
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL_LAST; ?>]'
				       value='<?php echo esc_attr( ! empty( $facet_label_last ) ? $facet_label_last : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE_RANGE ); ?>'/>
			</div>
		</div>

		<!-- Facet query section-->
		<div class="wdm_row wpsolr_facet_type wpsolr_<?php echo WPSOLR_Options_Facets::FACET_TYPE_QUERY; ?>">
			<div class='col_left'>
				Define your ranges</br></br>
				0|9|Range from %1$d - %2$d (%3$d)</br>
				10|20|Range 10 TO 20 (%3$d)</br>
				21|100|Range %s => %s (%3$d)</br>
				101|*|More than 100 (%3$d)</br>
			</div>
			<div class='col_right'>
				<textarea type='text' rows="10" style="width:98%"
				          name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_QUERY ?>][<?php echo WPSOLR_Options_Facets::FACET_FIELD_QUERY_RANGES ?>]'
				><?php echo esc_attr( $facet_query_ranges ); ?></textarea>

			</div>

		</div>

	</div>
</li>