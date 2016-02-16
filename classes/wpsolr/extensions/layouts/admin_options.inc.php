<?php
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;

?>

<?php
$group_tab_selected = isset( $_GET['group_tab'] ) ? $_GET['group_tab'] : 0;
?>

<script xmlns="http://www.w3.org/1999/html">
	jQuery(document).ready(function () {

		jQuery(".accordion").accordion({active: false, collapsible: true, heightStyle: "content"});

		// Save form
		jQuery('[name=save_layouts_options_form]').click(function () {


			var new_layout_ids = <?php echo json_encode( $new_layout_uuids );?>;
			for (var loop = 0; loop < new_layout_ids.length; loop++) {

				new_layout_uuid = new_layout_ids[loop];

				// Remove a new layout without a name
				var new_layout_element = jQuery('#' + new_layout_uuid + '_layout_name');
				var new_layout_name = new_layout_element.val();
				if (!new_layout_name) {
					if (new_layout_element.is(':hidden')) {
						jQuery('#' + new_layout_uuid).remove();
					} else {
						new_layout_element.css('border', '1px solid red');
						return false;
					}
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


<div id="solr-facets-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST">
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>

		<div class='wrapper'>
			<h4 class='head_div'>Layouts Options</h4>

			<div class="accordion">

				<?php foreach ( $predefined_layout_types as $predefined_layout_type => $predefined_layouts ) { ?>
					<h4 class='head_div'><?php echo $predefined_layouts[ WPSOLR_Options_Layouts::TYPE_LAYOUT_FIELD_NAME ]; ?></h4>
					<div>
						<?php
						WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file(
							WPSOLR_Extensions::OPTION_LAYOUTS, 'layouts.inc.php' ),
							array(
								'options_name'       => $options_name,
								'group_tab_selected' => $group_tab_selected,
								'new_layout'         => $new_layouts[ $predefined_layout_type ],
								'layout_type'        => $predefined_layout_type,
								'predefined_layouts' => $predefined_layouts[ WPSOLR_Options_Layouts::TYPE_LAYOUT_FIELD_LAYOUTS ],
								'layouts'            => isset( $layout_types[ $predefined_layout_type ] ) ? $layout_types[ $predefined_layout_type ][ WPSOLR_Options_Layouts::TYPE_LAYOUT_FIELD_LAYOUTS ] : [ ],
							)
						);
						?>
					</div>
				<?php } ?>

			</div>

			<div class='wdm_row'>
				<div class="submit">
					<input name="save_layouts_options_form" id="save_layouts_options_form"
					       type="submit" class="button-primary wdm-save"
					       value="Save Layouts"/>
				</div>
			</div>
		</div>
	</form>
</div>

