<?php
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;

?>

<?php
$group_tab_selected = isset( $_GET['group_tab'] ) ? $_GET['group_tab'] : 0;
?>

<script xmlns="http://www.w3.org/1999/html">
	jQuery(document).ready(function () {

		// Save form
		jQuery('[name=save_results_options_form]').click(function () {

			// Remove a new group without a name
			var new_group_element = jQuery('#<?php echo $new_group_uuid ?>_group_name');
			var new_group_name = new_group_element.val();
			if (!new_group_name) {
				if (new_group_element.is(':hidden')) {
					jQuery('#<?php echo $new_group_uuid ?>').remove();
				} else {
					new_group_element.css('border', '1px solid red');
					return false;
				}
			}


			// Remove all results not selected
			jQuery('.result_not_selected').each(function () {
				jQuery(this).detach();
			});

			// Change the group selected in url referer
			var url = new Url(window.location.href);
			url.query["group_tab"] = jQuery(".tabs").tabs('option', 'active');
			jQuery('[name=_wp_http_referer]').val(url.toString());

			return true;
		});

		jQuery('.results input:checkbox').click(function () {
			// id is 'group_uuid_field_name_is_active', we want #group_uuid_field_name which holds the result section
			var result_section_id = '#' + jQuery(this).attr('id').replace('_is_active', '');

			console.log(result_section_id);

			if (jQuery(this).prop("checked")) {
				jQuery(result_section_id).parent().removeClass('result_not_selected');
				jQuery(result_section_id).parent().addClass('result_selected');
			} else {
				jQuery(result_section_id).parent().addClass('result_not_selected');
				jQuery(result_section_id).parent().removeClass('result_selected');
			}
			jQuery(result_section_id).parent().find('.plus_icon').toggle(!jQuery(this).prop("checked"));
			jQuery(result_section_id).parent().find('.minus_icon').toggle(jQuery(this).prop("checked"));

		})

	});

</script>

<script>
	jQuery(document).ready(function () {

		// Collapsable instructions
		jQuery(".instructions").accordion({active: false, collapsible: true});

		jQuery("input[name=button_edit_layout],input[name=button_clone_layout]").on("click", function () {
			dialog.dialog("open");
			event.preventDefault();
		});

	});
</script>

<div id="solr-results-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST">
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>

		<div class='wrapper'>
			<h4 class='head_div'>Fields Options</h4>

			<div class="wdm_row">
				<div class='wpsolr-1col'>
					<h4 style="display:inline">Manage fields used by your Solr indexes.</h4>

					<?php
					WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file(
						WPSOLR_Extensions::OPTION_SCHEMAS, 'groups.inc.php' ),
						array(
							'options_name'              => $options_name,
							'group_tab_selected'        => $group_tab_selected,
							'new_group_uuid'            => $new_group_uuid,
							'groups'                    => $groups,
							'indexable_post_types'      => $indexable_post_types,
							'solr_field_types'          => $solr_field_types,
							'indexable_post_types'      => $indexable_post_types,
							'allowed_attachments_types' => $allowed_attachments_types,
							'taxonomies'                => $taxonomies,
							'indexable_custom_fields'   => $indexable_custom_fields,
							'selected_custom_fields'    => $selected_custom_fields,
							'indexes'                   => $indexes

						)
					);
					?>

				</div>

				<div class="clear"></div>
			</div>

			<div class='wdm_row'>
				<div class="submit">
					<input name="save_results_options_form" id="save_results_options_form"
					       type="submit" class="button-primary wdm-save"
					       value="Save fields"/>
				</div>
			</div>
		</div>
	</form>
</div>

