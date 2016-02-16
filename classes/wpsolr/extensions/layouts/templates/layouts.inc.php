<?php
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\utilities\WPSOLR_Global;

$new_layout_uuid = key( $new_layout );

$current_layout_option_name = sprintf( "%s[%s][%s]", $options_name, $layout_type, WPSOLR_Options_Layouts::TYPE_LAYOUT_FIELD_LAYOUTS )
?>

<style>

	#dialog label, #dialog input {
		display: block;
	}

	#dialog label {
		margin-top: 0.5em;
	}

	#dialog input, #dialog textarea {
		width: 95%;
	}

	.tabs {
		margin-top: 1em;
	}

	.tabs li .ui-icon-close {
		float: left;
		margin: 0.4em 0.2em 0 0;
		cursor: pointer;
	}

	#add_tab {
		cursor: pointer;
	}
</style>

<script>
	jQuery(document).ready(function () {

		// Create tabs and activate the group tab selected in the url
		// Create tabs and activate group tab selected in url
		var tabs = jQuery(".tabs").tabs({active: <?php echo $group_tab_selected; ?>});

		// tabs are sortable.
		tabs.find(".ui-tabs-nav").sortable({
			axis: "x",
			stop: function (event, ui) {
				var container = jQuery(this); // ul

				// Move the tab content with the tab nav
				var panel;
				jQuery(this).children().each(function () {
					panel = jQuery(jQuery(this).find('a').attr('href'));
					panel.insertAfter(container);
					container = panel; // div
				});

				// Active the dragged tab
				jQuery(".tabs").tabs("option", "active", event, ui);
				console.log(ui.item.index() + 1);

			}
		});

		tabs.delegate("span.ui-icon-close", "click", function () {
			var panelId = jQuery(this).closest("li").remove().attr("aria-controls");
			jQuery("#" + panelId).remove();
			tabs.tabs("refresh");
		});

		// Group facets accordeon
		jQuery(".sortable").sortable();
		jQuery(".sortable").accordion({active: false, collapsible: true, heightStyle: "content"});

	});
</script>


<div class="tabs">
	<ul>
		<?php
		foreach ( array_merge( $layouts, $new_layout ) as $layout_uuid => $layout ) { ?>
			<li><a href="#<?php echo $layout_uuid; ?>"><?php echo $layout['name']; ?></a>
				<?php if ( $new_layout_uuid !== $layout_uuid ) { ?>
					<span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
	<?php foreach ( array_merge( $layouts, $new_layout ) as $layout_uuid => $layout ) { ?>
		<div id="<?php echo $layout_uuid; ?>">

			<?php if ( $new_layout_uuid != $layout_uuid ) { ?>
				<div class="wdm_row">
					<div class='col_left'>Layout id<br/>
						Used in shortcodes
					</div>
					<div class='col_right'>
						<?php echo $layout_uuid; ?>
					</div>
					<div class="clear"></div>
				</div>
			<?php } ?>

			<div class="wdm_row">
				<div class='col_left'>Layout name</div>
				<div class='col_right'>
					<input type="text" style="width: 40%" id="<?php echo esc_attr( $layout_uuid ); ?>_layout_name"
					       name="<?php echo $current_layout_option_name; ?>[<?php echo $layout_uuid; ?>][<?php echo WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_NAME; ?>]"
					       value="<?php echo $new_layout_uuid == $layout_uuid ? '' : esc_attr( $layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_NAME ] ); ?>"/>

					<?php if ( $new_layout_uuid != $layout_uuid ) { ?>
						<input type="checkbox"
						       name="<?php echo $current_layout_option_name; ?>[<?php echo $layout_uuid; ?>][is_to_be_cloned]"
						       value="1"/> Clone this layout when saving
					<?php } ?>

				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Based on layout</div>
				<div class='col_right'>
					<?php if ( $new_layout_uuid != $layout_uuid ) {
						$layout_predefined_layout_id = isset( $layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_PREDEFINED_LAYOUT_ID ] ) ? $layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_PREDEFINED_LAYOUT_ID ] : '';
						?>
						<input type="hidden"
						       name="<?php echo $current_layout_option_name; ?>[<?php echo $layout_uuid; ?>][<?php echo WPSOLR_Options_Layouts::LAYOUT_FIELD_PREDEFINED_LAYOUT_ID; ?>]"
						       value="<?php echo $layout_predefined_layout_id ?>"/>
						<?php echo isset( $predefined_layouts[ $layout_predefined_layout_id ] ) ? $predefined_layouts[ $layout_predefined_layout_id ][ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_NAME ] : "" ?>
					<?php } else {
						$layout_predefined_layout_id = isset( $layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_PREDEFINED_LAYOUT_ID ] ) ? $layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_PREDEFINED_LAYOUT_ID ] : '';
						?>
						<select
							name="<?php echo $current_layout_option_name; ?>[<?php echo $layout_uuid; ?>][<?php echo WPSOLR_Options_Layouts::LAYOUT_FIELD_PREDEFINED_LAYOUT_ID; ?>]">
							<?php
							foreach ( $predefined_layouts as $predefined_layout_id => $predefined_layout ) { ?>
								<option value="<?php echo esc_attr( $predefined_layout_id ); ?>"
									<?php selected( $predefined_layout_id, $layout_predefined_layout_id, true ); ?> ><?php echo $predefined_layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_NAME ]; ?></option>
							<?php } ?>
						</select>
					<?php } ?>
				</div>
				<div class="clear"></div>
			</div>

			<?php if ( $new_layout_uuid != $layout_uuid ) { ?>
				<div class="accordion">
					<?php foreach (
						[
							WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_HTML => 'html',
							WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_JS   => 'javascript',
							WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_CSS  => 'css style'
						] as $template_type_name => $template_type_label
					) {
						$template_content1 = WPSOLR_Global::getTwig()->get_twig_template_file_content( array_values( $predefined_layouts )[0][ $template_type_name ] );
						$template_content  = isset( $layout[ $template_type_name ] ) ? esc_textarea( $layout[ $template_type_name ] ) : $template_content1;
						?>
						<h4><?php echo $template_type_label; ?></h4>
						<div>
						<textarea
							name="<?php echo $current_layout_option_name; ?>[<?php echo $layout_uuid; ?>][<?php echo $template_type_name; ?>]"
							rows="20"
							style="width:95%"><?php echo $template_content; ?></textarea>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

		</div>

	<?php } ?>

</div>



