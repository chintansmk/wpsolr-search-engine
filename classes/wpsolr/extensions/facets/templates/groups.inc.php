<?php
use wpsolr\extensions\fields\WPSOLR_Options_Fields;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\queries\WPSOLR_Options_Query;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;

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

		// Layouts
		var layouts = <?php echo json_encode( WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET ) ); ?>;

		function display_facet_types(layout_element, layout_facet_type) {
			layout_element.parent().parent().parent().children(".wpsolr_facet_type").hide(); // hide all facet type sections
			layout_element.parent().parent().parent().children(".wpsolr_facet_type").addClass("facet_type_not_selected"); // Detach at form submit

			layout_element.parent().parent().parent().children(".wpsolr_" + layout_facet_type).show(); // show facet section type of the selected layout
			layout_element.parent().parent().parent().children(".wpsolr_" + layout_facet_type).removeClass("facet_type_not_selected"); // Detach at form submit
		}

		// Display facet sections depending on the select layout facet type
		jQuery(".wpsolr_layout_select").each(function () {
			var layout_facet_type = layouts[jQuery(this).val()].facet_type;
			display_facet_types(jQuery(this), layout_facet_type);
		});

		// Change facet layout selection
		jQuery(".wpsolr_layout_select").on("change", function (event) {
			var layout_facet_type = layouts[jQuery(this).val()].facet_type;
			display_facet_types(jQuery(this), layout_facet_type);
		});

	});
</script>

<div class="tabs">
	<ul>
		<?php
		foreach ( $groups as $group_uuid => $group ) { ?>
			<li><a href="#<?php echo $group_uuid; ?>"><?php echo $group['name']; ?></a>
				<?php if ( $new_group_uuid !== $group_uuid ) { ?>
					<span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
	<?php foreach ( $groups as $group_uuid => $group ) { ?>
		<div id="<?php echo $group_uuid; ?>">

			<div class="wdm_row">
				<div class='col_left'>Group name</div>
				<div class='col_right'>
					<input type="text" style="width: 40%" id="<?php echo esc_attr( $group_uuid ); ?>_group_name"
					       name="<?php echo $options_name; ?>[<?php echo $group_uuid; ?>][name]"
					       value="<?php echo $new_group_uuid == $group_uuid ? '' : esc_attr( $group['name'] ); ?>"/>

					<?php if ( $new_group_uuid != $group_uuid ) { ?>
						<input type="checkbox"
						       name="<?php echo $options_name; ?>[<?php echo $group_uuid; ?>][is_to_be_cloned]"
						       value="1"/> Clone this group when saving
					<?php } ?>


				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Fields</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Fields::FORM_FIELD_FIELD_ID ] ) ? $group[ WPSOLR_Options_Fields::FORM_FIELD_FIELD_ID ] : ''; ?>

					<?php if ( $new_group_uuid != $group_uuid ) { ?>
						<input type="hidden"
						       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Fields::FORM_FIELD_FIELD_ID ); ?>"
						       value="<?php echo $value; ?>"
						/>
						<?php echo ! empty( $groups_fields[ $value ] ) ? $groups_fields[ $value ][ WPSOLR_Options_Fields::FORM_FIELD_NAME ] : ''; ?>
					<?php } else { ?>
						<select
							name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Fields::FORM_FIELD_FIELD_ID ); ?>">
							<?php
							foreach ( $groups_fields as $field_id => $field ) { ?>
								<option value="<?php echo esc_attr( $field_id ); ?>"
									<?php selected( $field_id, $value, true ); ?> >
									<?php echo $field[ WPSOLR_Options_Query::FORM_FIELD_NAME ]; ?>
								</option>
							<?php } ?>
						</select>
					<?php } ?>

				</div>
				<div class="clear"></div>
			</div>

			<?php if ( $new_group_uuid != $group_uuid ) { ?>
				<div class="wdm_row">
					<div class='col_left'>Always show facets</div>
					<div class='col_right'>
						<input type="checkbox"
						       name="<?php echo $options_name; ?>[<?php echo $group_uuid; ?>][<?php echo WPSOLR_Option::OPTION_FACETS_GROUP_EXCLUSION; ?>]"
						       value="1" <?php checked( ! empty( $group[ WPSOLR_Option::OPTION_FACETS_GROUP_EXCLUSION ] ) ); ?>/>
					</div>
					<div class="clear"></div>
				</div>

				<ul class="sortable connectedSortable">

					<?php
					$group_field         = WPSOLR_Global::getExtensionFields()->get_group( WPSOLR_Global::getExtensionFacets()->get_facet_field_id( $groups[ $group_uuid ] ) );
					$field_custom_fields = WPSOLR_Global::getExtensionFields()->get_custom_fields( $group_field );
					$field_taxonomies    = WPSOLR_Global::getExtensionFields()->get_taxonomies( $group_field );

					foreach ( ! empty( $groups[ $group_uuid ][ WPSOLR_Option::OPTION_FACETS_FACETS ] ) ? $groups[ $group_uuid ][ WPSOLR_Option::OPTION_FACETS_FACETS ] : [ ] as $facet_selected_name => $facet_selected ) {


						WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet.inc.php' ),
							array(
								'options_name'         => $options_name,
								'facets_group_uuid'    => $group_uuid,
								'layouts_facets'       => $layouts_facets[ WPSOLR_Global::getExtensionFields()->get_field_type_definition($group_field, $facet_selected_name )->get_id() ],
								'layouts_filters'      => $layouts_filters,
								'facet_name'           => $facet_selected_name,
								'is_numeric'           => WPSOLR_Global::getExtensionFields()->get_field_type_definition( $facet_selected_name )->get_is_numeric(),
								'facet'                => $facet_selected,
								'facet_selected_class' => $facet_selected_class,
								'image_plus_display'   => 'none',
								'image_minus_display'  => 'inline',
								'image_plus'           => $image_plus,
								'image_minus'          => $image_minus

							) );
					}

					foreach ( array_merge( $field_custom_fields, $field_taxonomies ) as $field_name => $field ) {

						$field_name = strtolower( $field_name );

						if ( ! isset( $groups[ $group_uuid ][ WPSOLR_Option::OPTION_FACETS_FACETS ] ) || ! isset( $groups[ $group_uuid ][ WPSOLR_Option::OPTION_FACETS_FACETS ][ $field_name ] ) ) {

							WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet.inc.php' ),
								array(
									'options_name'         => $options_name,
									'facets_group_uuid'    => $group_uuid,
									'layouts_facets'       => $layouts_facets[ WPSOLR_Global::getExtensionFields()->get_field_type_definition($group_field, $field_name )->get_id() ],
									'layouts_filters'      => $layouts_filters,
									'facet_name'           => $field_name,
									'is_numeric'           => WPSOLR_Global::getExtensionFields()->get_field_type_definition( $field_name )->get_is_numeric(),
									'facet'                => $field,
									'facet_selected_class' => $facet_not_selected_class,
									'image_plus_display'   => 'inline',
									'image_minus_display'  => 'none',
									'image_plus'           => $image_plus,
									'image_minus'          => $image_minus
								) );

						}
					}
					?>

				</ul>

			<?php } ?>

		</div>

	<?php } ?>

</div>



