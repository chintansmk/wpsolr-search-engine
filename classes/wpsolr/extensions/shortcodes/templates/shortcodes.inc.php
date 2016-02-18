<?php
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\shortcodes\WPSOLR_Options_Shortcodes;
use wpsolr\ui\WPSOLR_UI;

$new_shortcode_uuid = key( $new_shortcode );

$current_shortcode_option_name = sprintf( "%s[%s]", $options_name, $shortcode_type );
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
		foreach ( array_merge( $shortcodes, $new_shortcode ) as $shortcode_uuid => $shortcode ) { ?>
			<li>
				<a href="#<?php echo $shortcode_uuid; ?>"><?php echo $shortcode[ WPSOLR_UI::FORM_FIELD_TITLE ]; ?></a>
				<?php if ( $new_shortcode_uuid !== $shortcode_uuid ) { ?>
					<span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
	<?php foreach ( array_merge( $shortcodes, $new_shortcode ) as $shortcode_uuid => $shortcode ) { ?>
		<div id="<?php echo $shortcode_uuid; ?>">

			<?php if ( $new_shortcode_uuid != $shortcode_uuid ) { ?>
				<div class="wdm_row">
					<div class='col_left'>Copy this code in your post/page:
					</div>
					<div class='col_right'>
						<?php echo $shortcode[ WPSOLR_Options_Shortcodes::SHORTCODE_FIELD_CODE ]; ?>
					</div>
					<div class="clear"></div>
				</div>
			<?php } ?>

			<div class="wdm_row">
				<div class='col_left'>Title<br/>
					Translated as string
				</div>
				<div class='col_right'>
					<input type="text" style="width: 40%" id="<?php echo esc_attr( $shortcode_uuid ); ?>_shortcode_name"
					       name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_TITLE; ?>]"
					       value="<?php echo $new_shortcode_uuid == $shortcode_uuid ? '' : esc_attr( $shortcode[ WPSOLR_UI::FORM_FIELD_TITLE ] ); ?>"/>

					<?php if ( $new_shortcode_uuid != $shortcode_uuid ) { ?>
						<input type="checkbox"
						       name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][is_to_be_cloned]"
						       value="1"/> Clone this shortcode when saving
					<?php } ?>

				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Group</div>
				<div class='col_right'>
					<?php $value = isset( $shortcode[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] : ''; ?>
					<select
						name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_GROUP_ID; ?>]">
						<?php
						foreach ( $groups as $group_uuid => $group ) { ?>
							<option value="<?php echo esc_attr( $group_uuid ); ?>"
								<?php selected( $group_uuid, $value, true ); ?> ><?php echo $group[ WPSOLR_UI::FORM_FIELD_GROUP_NAME ]; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Layout</div>
				<div class='col_right'>
					<?php $value = isset( $shortcode[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] : ''; ?>
					<select
						name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_LAYOUT_ID; ?>]">
						<?php
						foreach ( $predefined_layouts as $predefined_layout_id => $predefined_layout ) { ?>
							<option value="<?php echo esc_attr( $predefined_layout_id ); ?>"
								<?php selected( $predefined_layout_id, $value, true ); ?> ><?php echo $predefined_layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_NAME ]; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Regexp to filter urls</div>
				<div class='col_right'>
					<?php $value = isset( $shortcode[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] : ''; ?>
					<textarea style="width: 95%" rows="5"
					          name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_URL_REGEXP; ?>]"
					><?php echo esc_textarea( $value ); ?></textarea>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Show if empty</div>
				<div class='col_right'>
					<?php $value = isset( $shortcode[ WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY ] ); ?>
					<input type="checkbox"
					       name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY; ?>]"
						<?php checked( $value ); ?>
					/>
				</div>
				<div class="clear"></div>
			</div>


			<div class="wdm_row">
				<div class='col_left'>Show title on front-end</div>
				<div class='col_right'>
					<?php $value = isset( $shortcode[ WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END ] ); ?>
					<input type="checkbox"
					       name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END; ?>]"
						<?php checked( $value ); ?>
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Before title</div>
				<div class='col_right'>
					<?php $value = isset( $shortcode[ WPSOLR_UI::FORM_FIELD_BEFORE_TITLE ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_BEFORE_TITLE ] : ''; ?>
					<input type="text"
					       name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_BEFORE_TITLE; ?>]"
					       value="<?php echo esc_html( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>After title</div>
				<div class='col_right'>
					<?php $value = isset( $shortcode[ WPSOLR_UI::FORM_FIELD_AFTER_TITLE ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_AFTER_TITLE ] : ''; ?>
					<input type="text"
					       name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_AFTER_TITLE; ?>]"
					       value="<?php echo esc_html( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Before shortcode</div>
				<div class='col_right'>
					<?php $value = isset( $shortcode[ WPSOLR_UI::FORM_FIELD_BEFORE_UI ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_BEFORE_UI ] : ''; ?>
					<input type="text"
					       name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_BEFORE_UI; ?>]"
					       value="<?php echo esc_html( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>After shortcode</div>
				<div class='col_right'>
					<?php $value = isset( $shortcode[ WPSOLR_UI::FORM_FIELD_AFTER_UI ] ) ? $shortcode[ WPSOLR_UI::FORM_FIELD_AFTER_UI ] : ''; ?>
					<input type="text"
					       name="<?php echo $current_shortcode_option_name; ?>[<?php echo $shortcode_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_AFTER_UI; ?>]"
					       value="<?php echo esc_html( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

		</div>

	<?php } ?>

</div>



