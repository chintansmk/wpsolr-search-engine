<?php
use wpsolr\utilities\WPSOLR_Option;

?>

<?php
$facet_option_array_name = sprintf( '%s[%s][%s][%s]', $options_name, WPSOLR_Option::OPTION_FACETS_FACETS, $facets_group_uuid, $facet_name );

$facet_layout_id = ! empty( $facet['layout_id'] ) ? $facet['layout_id'] : '';

$facet_range_start = ! empty( $facet['range'] ) && ! empty( $facet['range']['start'] ) ? $facet['range']['start'] : 0;
$facet_range_end   = ! empty( $facet['range'] ) && ! empty( $facet['range']['end'] ) ? $facet['range']['end'] : 1000;
$facet_range_gap   = ! empty( $facet['range'] ) && ! empty( $facet['range']['gap'] ) ? $facet['range']['gap'] : 100;

$facet_elements_operator = ! empty( $facet['elements_operator'] ) ? $facet['elements_operator'] : 'AND';
$facet_sort              = ! empty( $facet['sort'] ) ? $facet['sort'] : 'count';
$facet_min_count         = ! empty( $facet['min_count'] ) ? $facet['min_count'] : '0';
$facet_missing           = ! empty( $facet['missing'] ) ? $facet['missing'] : '0';
$facet_is_active         = ! empty( $facet['is_active'] ) ? $facet['is_active'] : '0';

?>

<li class='facets <?php echo $facet_selected_class; ?>'>
	<div>
		<a><?php echo $facet_name; ?></a>
		<img src='<?php echo $image_plus; ?>' class='plus_icon' style='display:<?php echo $image_plus_display; ?>'>
		<img src='<?php echo $image_minus; ?>' class='minus_icon' style='display:<?php echo $image_minus_display; ?>'
		     title='Click to Remove the Facet'>
	</div>
	<div id='<?php echo $facet_name; ?>'>
		<input type='hidden'
		       name='<?php echo $facet_option_array_name; ?>[name]'
		       value='<?php echo $facet_name; ?>'/>

		<div class="wdm_row">
			<div class='col_left'>
				Show this facet in this group
			</div>
			<div class='col_right'>
				<input type='checkbox' id="<?php echo $facet_name; ?>_is_active"
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
				<select
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
				       value='<?php echo $facet_min_count; ?>'/>

			</div>
		</div>
		<div class="wdm_row">
			<div class='col_left'>
				Count missing elements
			</div>
			<div class='col_right'>
				<select name='<?php echo $facet_option_array_name; ?>[missing]'>
					<option value='0' <?php selected( '0', $facet_missing, true ); ?>>Do not count missing elements
					</option>
					<option value='1' <?php selected( '1', $facet_missing, true ); ?>>Count missing elements</option>
				</select>
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
					<option value='count' <?php selected( 'count', $facet_sort, true ); ?>>Count
					</option>
					<option value='index' <?php selected( 'index', $facet_sort, true ); ?>>Alphabetical</option>
				</select>
			</div>
		</div>

		<?php if ( $is_range ) { ?>
			<!-- Facet range section-->
			<div class="wdm_row" class="wpsolr_facet_range">
				<div class='col_left'>
					Range start
				</div>
				<div class='col_right'>
					<input type='text'
					       name='<?php echo $facet_option_array_name; ?>[range][start]'
					       value='<?php echo $facet_range_start; ?>'/>

				</div>
				<div class='col_left'>
					Range end
				</div>
				<div class='col_right'>
					<input type='text'
					       name='<?php echo $facet_option_array_name; ?>[range][end]'
					       value='<?php echo $facet_range_end; ?>'/>

				</div>
				<div class='col_left'>
					Range gap
				</div>
				<div class='col_right'>
					<input type='text'
					       name='<?php echo $facet_option_array_name; ?>[range][gap]'
					       value='<?php echo $facet_range_gap; ?>'/>

				</div>
			</div>
		<?php } ?>

	</div>
</li>