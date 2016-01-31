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
		jQuery('[name=save_facets_options_form]').click(function () {

			// Remove a new group without a name
			var new_group_element = jQuery('#<?php echo $new_facets_group_uuid ?>_group_name');
			var new_group_name = new_group_element.val();
			if (!new_group_name) {
				if (new_group_element.is(':hidden')) {
					jQuery('#<?php echo $new_facets_group_uuid ?>').remove();
				} else {
					new_group_element.css('border', '1px solid red');
					return false;
				}
			}

			// Remove all facets not selected
			jQuery('.facet_not_selected').each(function () {
				jQuery(this).detach();
			});

			// Remove all facet type sections not selected
			jQuery('.facet_type_not_selected').each(function () {
				jQuery(this).detach();
			});

			// Change the group selected in url referer
			var url = new Url(window.location.href);
			url.query["group_tab"] = jQuery(".tabs").tabs('option', 'active');
			jQuery('[name=_wp_http_referer]').val(url.toString());

			return true;
		});

		jQuery('.facets input:checkbox').click(function () {
			// id is 'group_uuid_field_name_is_active', we want #group_uuid_field_name which holds the facet section
			var facet_section_id = '#' + jQuery(this).attr('id').replace('_is_active', '');

			console.log(facet_section_id);

			if (jQuery(this).prop("checked")) {
				jQuery(facet_section_id).parent().removeClass('facet_not_selected');
				jQuery(facet_section_id).parent().addClass('facet_selected');
			} else {
				jQuery(facet_section_id).parent().addClass('facet_not_selected');
				jQuery(facet_section_id).parent().removeClass('facet_selected');
			}
			jQuery(facet_section_id).parent().find('.plus_icon').toggle(!jQuery(this).prop("checked"));
			jQuery(facet_section_id).parent().find('.minus_icon').toggle(jQuery(this).prop("checked"));

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

<div id="solr-facets-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST">
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>
		<div class="wdm_note instructions">
			<h4>Instructions</h4>
			<ul class="wdm_ul wdm-instructions">
				<div class="wdm_note">

					In this section, you will choose which data you want to display as facets in
					your search results. Facets are extra filters usually seen in the left hand
					side of the results, displayed as a list of links. You can add facets only
					to data you've selected to be indexed.

				</div>
				<li>Click on the
					<image src='<?php echo $image_plus; ?>'/>
					icon to add a facet
				</li>
				<li>Click on the
					<image src='<?php echo $image_minus; ?>'/>
					icon to remove a facet
				</li>
				<li>Sort the items in the order you want to display them by dragging and
					dropping them at the desired plcae
				</li>
			</ul>
		</div>

		<div class='wrapper'>
			<h4 class='head_div'>Facets Options</h4>

			<div class="wdm_row">
				<div class='wpsolr-1col'>
					<h4 style="display:inline">Manage groups of facets to show in WPSOLR Widgets.</h4>

					<?php
					WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file(
						WPSOLR_Extensions::OPTION_FACETS, 'groups.inc.php' ),
						array(
							'options_name'              => $options_name,
							'group_tab_selected'        => $group_tab_selected,
							'new_facets_group_uuid'     => $new_facets_group_uuid,
							'layouts'                   => $layouts,
							'default_facets_group_uuid' => $default_facets_group_uuid,
							'facets_groups'             => $facets_groups,
							'facets_selected'           => $facets_selected,
							'fields'                    => $fields,
							'facet_selected_class'      => 'facet_selected',
							'facet_not_selected_class'  => 'facet_not_selected',
							'image_plus_display'        => 'none',
							'image_minus_display'       => 'inline',
							'image_plus'                => $image_plus,
							'image_minus'               => $image_minus
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

