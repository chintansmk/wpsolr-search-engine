<?php
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;

?>

<?php
$group_tab_selected = isset( $_GET['group_tab'] ) ? $_GET['group_tab'] : 0;
?>

<style>

	.tabs {
		margin-top: 1em;
	}

	.tabs li .ui-icon-close {
		float: left;
		margin: 0.4em 0.2em 0 0;
		cursor: pointer;
	}

</style>

<script>
	jQuery(document).ready(function () {

		// Save form
		jQuery('[name=save_options_form]').click(function () {

			// Let children have a chance to say something about the save
			if (typeof wpsolr_form_saved === 'function') {
				if (!wpsolr_form_saved()) {
					// Stop the save
					return false;
				}
			}

			// Remove all group contents not selected
			jQuery('.group_content_not_selected').each(function () {
				console.log('detached');
				jQuery(this).detach();
			});

			// Remove a new group without a name
			var new_group_element = jQuery('#<?php echo $group_parameters['new_group_uuid'] ?>_group_name');
			var new_group_name = new_group_element.val();
			if (!new_group_name) {
				if (new_group_element.is(':hidden')) {
					jQuery('#<?php echo $group_parameters['new_group_uuid'] ?>').remove();
				} else {
					new_group_element.css('border', '1px solid red');
					return false;
				}
			}

			// Change the group selected in url referer
			var url = new Url(window.location.href);
			url.query["group_tab"] = jQuery(".tabs").tabs('option', 'active');
			jQuery('[name=_wp_http_referer]').val(url.toString());

			return true;
		});

		// Toggle the group content icon on selection
		jQuery('.group_content input:checkbox').click(function () {

			// id is 'group_uuid_field_name_is_active', we want #group_uuid_field_name which holds the facet section
			var group_content_id = '#' + jQuery(this).attr('id').replace('_is_active', '');

			if (jQuery(this).prop("checked")) {
				jQuery(group_content_id).parent().removeClass('group_content_not_selected');
				jQuery(group_content_id).parent().addClass('group_content_selected');
			} else {
				jQuery(group_content_id).parent().addClass('group_content_not_selected');
				jQuery(group_content_id).parent().removeClass('group_content_selected');
			}
			jQuery(group_content_id).parent().find('.plus_icon').toggle(!jQuery(this).prop("checked"));
			jQuery(group_content_id).parent().find('.minus_icon').toggle(jQuery(this).prop("checked"));

		});

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

		// Remove button action
		tabs.delegate("span.ui-icon-close", "click", function () {
			var panelId = jQuery(this).closest("li").remove().attr("aria-controls");
			jQuery("#" + panelId).remove();
			tabs.tabs("refresh");
		});

		// Tabs are sortable
		jQuery(".sortable").sortable();
		jQuery(".sortable").accordion({active: false, collapsible: true, heightStyle: "content"});

		// Toggle default group
		jQuery('.is_default').click(function () {

			if (jQuery(this).prop("checked")) {

				// Uncheck all other checks
				jQuery('.is_default').not(this).prop("checked", false);
			}

		})

	});
</script>

<script>
	jQuery(document).ready(function () {

		// Collapsable instructions
		jQuery(".instructions").accordion({active: false, collapsible: true});
	});
</script>

<div
	class="wpsolr-vertical-tabs-content <?php echo ! empty( $group_parameters['extra_classes'] ) ? $group_parameters['extra_classes'] : ''; ?>">
	<form action="options.php" method="POST">
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>

		<div class='wrapper'>
			<h4 class='head_div'><?php echo $plugin_title; ?></h4>

			<div class="wdm_row">
				<div class="tabs">
					<ul>
						<?php
						foreach ( $group_parameters['groups'] as $group_uuid => $group ) { ?>
							<li><a href="#<?php echo $group_uuid; ?>"><?php echo $group['name']; ?></a>
								<?php if ( $group_parameters['new_group_uuid'] !== $group_uuid ) { ?>
									<span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span>
								<?php } ?>
							</li>
						<?php } ?>
					</ul>
					<?php foreach ( $group_parameters['groups'] as $group_uuid => $group ) { ?>

						<div id="<?php echo $group_uuid; ?>">
							<div class="wdm_row">
								<div class='col_left'>Label</div>
								<div class='col_right'>
									<input style="width:40%" type="text"
									       id="<?php echo esc_attr( $group_uuid ); ?>_group_name"
									       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, 'name' ); ?>"
									       value="<?php echo $group_parameters['new_group_uuid'] == $group_uuid ? '' : esc_attr( $group['name'] ); ?>"/>

									<?php if ( $group_parameters['new_group_uuid'] != $group_uuid ) { ?>
										<input type="checkbox"
										       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, 'is_to_be_cloned' ); ?>"
										       value="1"/> Clone
									<?php } ?>

								</div>
								<div class="clear"></div>
							</div>

							<?php
							$group_parameters['options_name'] = $options_name;
							$group_parameters['group_uuid']   = $group_uuid;
							$group_parameters['group']        = $group;
							WPSOLR_Extensions::require_with(
								WPSOLR_Extensions::get_option_template_file(
									$group_parameters['extension_name'], 'group.inc.php' ),
								$group_parameters );
							?>
						</div>
					<?php } ?>

				</div>

			</div>

			<div class='wdm_row'>
				<div class="submit">
					<input name="save_options_form"
					       type="submit" class="button-primary wdm-save"
					       value="Save"/>
				</div>
			</div>
		</div>
	</form>
</div>

