<?php
use wpsolr\extensions\shortcodes\WPSOLR_Options_Layouts;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Global;

?>

<?php
$group_tab_selected = isset( $_GET['group_tab'] ) ? $_GET['group_tab'] : 0;
?>

<script xmlns="http://www.w3.org/1999/html">
	jQuery(document).ready(function () {

		jQuery(".accordion").accordion({active: false, collapsible: true, heightStyle: "content"});

		// Save form
		jQuery('[name=save_shortcodes_options_form]').click(function () {


			var new_shortcode_ids = <?php echo json_encode( $new_shortcode_uuids );?>;
			for (var loop = 0; loop < new_shortcode_ids.length; loop++) {

				new_shortcode_uuid = new_shortcode_ids[loop];

				// Remove a new shortcode without a name
				var new_shortcode_element = jQuery('#' + new_shortcode_uuid + '_shortcode_name');
				var new_shortcode_name = new_shortcode_element.val();
				if (!new_shortcode_name) {
					if (new_shortcode_element.is(':hidden')) {
						jQuery('#' + new_shortcode_uuid).remove();
					} else {
						new_shortcode_element.css('border', '1px solid red');
						return false;
					}
				}
			}


			// Remove all shortcodes not selected
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

		jQuery('.shortcodes input:checkbox').click(function () {
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


<div id="solr-shortcodes-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST">
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>

		<div class='wrapper'>
			<h4 class='head_div'>Shortcodes Options</h4>

			<div class="accordion">

				<?php foreach ( $predefined_shortcodes as $predefined_shortcode_name => $predefined_shortcode_object ) { ?>
					<h4 class='head_div'><?php echo $predefined_shortcode_name; ?></h4>
					<div>
						<?php
						WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file(
							WPSOLR_Extensions::OPTION_SHORTCODES, 'shortcodes.inc.php' ),
							array(
								'options_name'       => $options_name,
								'group_tab_selected' => $group_tab_selected,
								'shortcode_type'     => $predefined_shortcode_name,
								'new_shortcode'      => $new_shortcodes[ $predefined_shortcode_name ],
								'shortcodes'         => isset( $shortcodes[ $predefined_shortcode_name ] ) ? $shortcodes[ $predefined_shortcode_name ] : [ ],
								'predefined_layouts' => WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( $predefined_shortcode_object->get_layout_type() ),
								'groups'             => $predefined_shortcode_object->get_ui()->get_groups()
							)
						);
						?>
					</div>
				<?php } ?>

			</div>

			<div class='wdm_row'>
				<div class="submit">
					<input name="save_shortcodes_options_form" id="save_shortcodes_options_form"
					       type="submit" class="button-primary wdm-save"
					       value="Save shortcodes"/>
				</div>
			</div>
		</div>
	</form>
</div>

