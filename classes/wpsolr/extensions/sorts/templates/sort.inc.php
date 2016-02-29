<?php
use wpsolr\extensions\sorts\WPSOLR_Options_Sorts;

?>

<?php
$sort_option_array_name = sprintf( '%s[%s][%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Sorts::FORM_FIELD_SORTS, $sort_name );

$sort_layout_id = ! empty( $sort['layout_id'] ) ? $sort['layout_id'] : '';

$sort_is_active = ! empty( $sort['is_active'] ) ? $sort['is_active'] : '0';
$sort_label     = ! empty( $sort[ WPSOLR_Options_Sorts::SORT_FIELD_LABEL ] ) ? $sort[ WPSOLR_Options_Sorts::SORT_FIELD_LABEL ] : $sort_name;
?>

<li class='group_content <?php echo $sort_selected_class; ?>'>
	<div>
		<a><?php echo $sort_label; ?></a>
		<img src='<?php echo $image_plus; ?>' class='plus_icon' style='display:<?php echo $image_plus_display; ?>'>
		<img src='<?php echo $image_minus; ?>' class='minus_icon' style='display:<?php echo $image_minus_display; ?>'
		     title='Click to Remove the Sort'>
	</div>
	<div id='<?php echo $group_uuid . '_' . $sort_name; ?>'>
		<input type='hidden'
		       name='<?php echo $sort_option_array_name; ?>[name]'
		       value='<?php echo $sort_name; ?>'/>

		<div class="wdm_row">
			<div class='col_left'>
				Show this sort in this group
			</div>
			<div class='col_right'>
				<input type='checkbox' id="<?php echo $group_uuid . '_' . $sort_name; ?>_is_active"
				       name='<?php echo $sort_option_array_name; ?>[is_active]' value='1'
					<?php checked( '1', $sort_is_active, true ); ?>/>
			</div>
			<div class="clear"></div>
		</div>

		<div class="wdm_row">
			<div class='col_left'>
				Display as
			</div>
			<div class='col_right'>
				<select
					name='<?php echo $sort_option_array_name; ?>[layout_id]'>
					<?php foreach ( $layouts as $layout_id => $layout ) { ?>
						<option
							value='<?php echo $layout_id; ?>' <?php selected( $layout_id, $sort_layout_id, true ); ?>><?php echo $layout['name']; ?></option>
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

		<div class="wdm_row">
			<div class='col_left'>
				Label
			</div>
			<div class='col_right'>
				<input type='text'
				       name='<?php echo $sort_option_array_name; ?>[<?php echo WPSOLR_Options_Sorts::SORT_FIELD_LABEL; ?>]'
				       value='<?php echo esc_attr( $sort_label ); ?>'/>
			</div>
		</div>

	</div>
</li>