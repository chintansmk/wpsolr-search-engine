<?php
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;

?>


<script xmlns="http://www.w3.org/1999/html">
	jQuery(document).ready(function () {

		jQuery('.plus_icon').click(function () {
			jQuery(this).parent().parent().addClass('facet_selected');
			jQuery(this).hide();
			jQuery(this).siblings().css('display', 'inline');

			jQuery(this).parent().next().children().prop('disabled', false);
		})

		jQuery('.minus_icon').click(function () {
			jQuery(this).parent().parent().removeClass('facet_selected');
			jQuery(this).hide();
			jQuery(this).siblings().css('display', 'inline');

			jQuery(this).parent().next().children().prop('disabled', true);
		})

		jQuery('#save_facets_options_form').click(function () {

		});

	});

</script>

<script>
	jQuery(document).ready(function () {

		var dialog, form;

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
		facet to adapt it's display and behaviour.</p>

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

<div id="solr-facets-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST">
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>
		<div class='wrapper'>
			<h4 class='head_div'>Facets Options</h4>

			<div class="wdm_note">

				In this section, you will choose which data you want to display as facets in
				your search results. Facets are extra filters usually seen in the left hand
				side of the results, displayed as a list of links. You can add facets only
				to data you've selected to be indexed.

			</div>

			<div class="wdm_row">
				<div class='wpsolr-1col'>
					<h4 style="display:inline">Available items for facets of group</h4>

					<?php
					WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file(
						WPSOLR_Extensions::OPTION_FACETS, 'groups.inc.php' ),
						array(
							'options_name'               => $options_name,
							'new_facets_group_uuid'      => $new_facets_group_uuid,
							'layouts'                    => $layouts,
							'selected_facets_group_uuid' => $selected_facets_group_uuid,
							'facets_groups'              => $facets_groups,
							'facets_selected'            => $facets_selected,
							'facets_candidates'          => $facets_candidates,
							'facet_selected_class'       => 'facet_selected',
							'image_plus_display'         => 'none',
							'image_minus_display'        => 'inline',
							'image_plus'                 => $image_plus,
							'image_minus'                => $image_minus
						)
					);
					?>

				</div>

				<div class="clear"></div>
			</div>

			<div class='wdm_row'>
				<div class="submit">
					<input name="save_facets_options_form" id="save_facets_options_form"
					       type="submit" class="button-primary wdm-save"
					       value="Save Options"/>
				</div>
			</div>
		</div>
	</form>
</div>

