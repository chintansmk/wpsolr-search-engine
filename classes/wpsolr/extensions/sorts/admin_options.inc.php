<?php
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;

?>

<script xmlns="http://www.w3.org/1999/html">
	jQuery(document).ready(function () {

		// Save form
		jQuery('[name=save_sorts_options_form]').click(function () {

			// Remove a new group without a name
			var new_group_element = jQuery('#<?php echo $new_sorts_group_uuid ?>_group_name');
			var new_group_name = new_group_element.val();
			if (!new_group_name) {
				if (new_group_element.is(':hidden')) {
					jQuery('#<?php echo $new_sorts_group_uuid ?>').remove();
				} else {
					new_group_element.css('border', '1px solid red');
					return false;
				}
			}


			// Remove all sorts not selected
			jQuery('.sort_not_selected').each(function () {
				jQuery(this).detach();
			});

			return true;
		});

		jQuery('.sorts input:checkbox').click(function () {
			// id is 'group_uuid_field_name_is_active', we want #group_uuid_field_name which holds the sort section
			var sort_section_id = '#' + jQuery(this).attr('id').replace('_is_active', '');

			console.log(sort_section_id);

			if (jQuery(this).prop("checked")) {
				jQuery(sort_section_id).parent().removeClass('sort_not_selected');
				jQuery(sort_section_id).parent().addClass('sort_selected');
			} else {
				jQuery(sort_section_id).parent().addClass('sort_not_selected');
				jQuery(sort_section_id).parent().removeClass('sort_selected');
			}
			jQuery(sort_section_id).parent().find('.plus_icon').toggle(!jQuery(this).prop("checked"));
			jQuery(sort_section_id).parent().find('.minus_icon').toggle(jQuery(this).prop("checked"));

		})

	});

</script>

<script>
	jQuery(document).ready(function () {

		var dialog, form;

		// Collapsable instructions
		jQuery(".instructions").accordion({active: false, collapsible: true});

		dialog = jQuery(".dialog_form_layout").dialog({
			autoOpen: false,
			height: 800,
			width: 1000,
			modal: true,
			buttons: {
				"Delete": function () {
				},
				"Update": function () {
				},
				Cancel: function () {
					dialog.dialog("close");
				}
			},
			close: function () {
				//form[0].reset();
				//allFields.removeClass("ui-state-error");
			}
		});

		form = dialog.find("form").on("submit", function (event) {
			event.preventDefault();
			addUser();
		});

		jQuery("input[name=button_edit_layout],input[name=button_clone_layout]").on("click", function () {
			dialog.dialog("open");
			event.preventDefault();
		});

	});
</script>


<div class="dialog_form_layout" title="Create a new layout">
	<p class="validateTips">Name a layout and customize it's html, css, js with Twig language. Select the layout on any
		sort to adapt it's display and behaviour.</p>

	<form>
		<fieldset>
			<label for="name">Name</label>
			<input type="text" name="name" id="name" value="" class="text ui-widget-content ui-corner-all">

			<div class="tabs" style="margin-top: 10px;">
				<ul>
					<li><a href="#tabs-1">html</a></li>
					<li><a href="#tabs-2">css</a></li>
					<li><a href="#tabs-3">js</a></li>
				</ul>
				<div id="tabs-1">
					<p><textarea name="template_html" rows="30" cols="100">html here</textarea></p>
				</div>
				<div id="tabs-2">
					<p><textarea name="template_css" rows="30" cols="100">css here</textarea></p>
				</div>
				<div id="tabs-3">
					<p><textarea name="template_js" rows="30" cols="100">js here</textarea></p>
				</div>
			</div>

			<!-- Allow form submission with keyboard without duplicating the dialog button -->
			<input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
		</fieldset>
	</form>
</div>

<div id="solr-sorts-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST">
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>
		<div class="wdm_note instructions">
			<h4>Instructions</h4>
			<ul class="wdm_ul wdm-instructions">
				<div class="wdm_note">

					In this section, you will choose which data you want to display as sorts in
					your search results. Sorts are extra filters usually seen in the left hand
					side of the results, displayed as a list of links. You can add sorts only
					to data you've selected to be indexed.

				</div>
				<li>Click on the
					<image src='<?php echo $image_plus; ?>'/>
					icon to add a sort
				</li>
				<li>Click on the
					<image src='<?php echo $image_minus; ?>'/>
					icon to remove a sort
				</li>
				<li>Sort the items in the order you want to display them by dragging and
					dropping them at the desired plcae
				</li>
			</ul>
		</div>

		<div class='wrapper'>
			<h4 class='head_div'>Sorts Options</h4>

			<div class="wdm_row">
				<div class='wpsolr-1col'>
					<h4 style="display:inline">Manage groups of sorts to show in WPSOLR Widgets.</h4>

					<?php
					WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file(
						WPSOLR_Extensions::OPTION_SORTS, 'groups.inc.php' ),
						array(
							'options_name'             => $options_name,
							'new_sorts_group_uuid'     => $new_sorts_group_uuid,
							'layouts'                  => $layouts,
							'default_sorts_group_uuid' => $default_sorts_group_uuid,
							'sorts_groups'             => $sorts_groups,
							'sorts_selected'           => $sorts_selected,
							'fields'                   => $fields,
							'sort_selected_class'      => 'sort_selected',
							'sort_not_selected_class'  => 'sort_not_selected',
							'image_plus_display'       => 'none',
							'image_minus_display'      => 'inline',
							'image_plus'               => $image_plus,
							'image_minus'              => $image_minus
						)
					);
					?>

				</div>

				<div class="clear"></div>
			</div>

			<div class='wdm_row'>
				<div class="submit">
					<input name="save_sorts_options_form" id="save_sorts_options_form"
					       type="submit" class="button-primary wdm-save"
					       value="Save Options"/>
				</div>
			</div>
		</div>
	</form>
</div>

