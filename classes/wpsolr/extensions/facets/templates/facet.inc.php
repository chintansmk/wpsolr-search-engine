<?php
use wpsolr\utilities\WPSOLR_Option;

?>

<?php
$facet_option_path = sprintf( '%s[%s][%s][%s]', $options_name, WPSOLR_Option::OPTION_FACETS_FACETS, $facets_group_uuid, $facet_name );

$facet_layout_id = ! empty( $facet['layout_id'] ) ? $facet['layout_id'] : '';
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
		       name='<?php echo $facet_option_path; ?>[name]'
		       value='<?php echo $facet_name; ?>'/>
		<div class="wdm_row">
			<div class='col_left'>
				Display as
			</div>
			<div class='col_right'>
				<select
					name='<?php echo $facet_option_path; ?>[layout_id]'>
					<?php foreach ( $layouts as $layout_id => $layout ) { ?>
						<option
							value='<?php echo $layout_id; ?>' <?php selected( $layout_id, $facet_layout_id, true ); ?>><?php echo $layout['name']; ?></option>
					<?php } ?>
				</select>
				<input name="button_clone_layout"
				       type="submit" class="button-primary wdm-save"
				       value="Clone the layout"/>
				<input name="button_edit_layout"
				       type="submit" class="button-primary wdm-save"
				       value="Edit the layout"/>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</li>