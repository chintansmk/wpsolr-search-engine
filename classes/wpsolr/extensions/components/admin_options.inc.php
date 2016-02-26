<?php
use wpsolr\extensions\components\WPSOLR_Options_Components;
use wpsolr\extensions\components\WPSOLR_Options_Layouts;
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
		jQuery('[name=save_components_options_form]').click(function () {


			var new_component_ids = <?php echo json_encode( $new_component_uuids );?>;
			for (var loop = 0; loop < new_component_ids.length; loop++) {

				new_component_uuid = new_component_ids[loop];

				// Remove a new component without a name
				var new_component_element = jQuery('#' + new_component_uuid + '_component_name');
				var new_component_name = new_component_element.val();
				if (!new_component_name) {
					if (new_component_element.is(':hidden')) {
						jQuery('#' + new_component_uuid).remove();
					} else {
						new_component_element.css('border', '1px solid red');
						return false;
					}
				}
			}


			// Remove all components not selected
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

		jQuery('.components input:checkbox').click(function () {
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


<div id="solr-components-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST">
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>

		<div class='wrapper'>
			<h4 class='head_div'>Components Options</h4>

			<div class="accordion">

				<?php foreach ( $components_types as $component_type_name => $component_type ) { ?>
					<h4 class='head_div'><?php echo $component_type[ WPSOLR_Options_Components::COMPONENT_FIELD_LABEL ]; ?></h4>
					<div>
						<?php
						WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file(
							WPSOLR_Extensions::OPTION_COMPONENTS, 'components.inc.php' ),
							array(
								'options_name'       => $options_name,
								'group_tab_selected' => $group_tab_selected,
								'component_type'     => $component_type_name,
								'new_component'      => $new_components[ $component_type_name ],
								'components'         => isset( $components[ $component_type_name ] ) ? $components[ $component_type_name ] : [ ],
								'layouts'            => $component_type[ WPSOLR_Options_Components::COMPONENT_FIELD_UI ]->get_layouts(),
								'groups'             => $component_type[ WPSOLR_Options_Components::COMPONENT_FIELD_UI ]->get_groups(),
								'queries'            => $queries
							)
						);
						?>
					</div>
				<?php } ?>

			</div>

			<div class='wdm_row'>
				<div class="submit">
					<input name="save_components_options_form" id="save_components_options_form"
					       type="submit" class="button-primary wdm-save"
					       value="Save components"/>
				</div>
			</div>
		</div>
	</form>
</div>

