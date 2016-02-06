<?php
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\ui\widget\WPSOLR_Widget_Facet;
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
		var layouts = <?php echo json_encode( WPSOLR_Widget_Facet::get_facets_layouts() ); ?>;

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

<div class="wdm_row">
	<div class='col_left'>Facets displayed in search pages</div>
	<div class='col_right'>
		<select
			name="<?php echo $options_name; ?>[<?php echo WPSOLR_Option::OPTION_FACETS_GROUP_DEFAULT_ID; ?>]">
			<?php
			foreach ( array_merge( [ '' => [ 'name' => 'Select a group' ] ], $facets_groups ) as $facets_group_uuid => $facets_group ) { ?>
				<?php if ( $new_facets_group_uuid != $facets_group_uuid ) { ?>
					<option
						value="<?php echo $facets_group_uuid; ?>" <?php selected( $facets_group_uuid, $default_facets_group_uuid, true ); ?>>
						<?php echo $facets_group['name']; ?>
					</option>
				<?php } ?>
			<?php } ?>
		</select>
	</div>
	<div class="clear"></div>
</div>

<div class="tabs">
	<ul>
		<?php
		foreach ( $facets_groups as $facets_group_uuid => $facets_group ) { ?>
			<li><a href="#<?php echo $facets_group_uuid; ?>"><?php echo $facets_group['name']; ?></a>
				<?php if ( $new_facets_group_uuid !== $facets_group_uuid ) { ?>
					<span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
	<?php foreach ( $facets_groups as $facets_group_uuid => $facets_group ) { ?>
		<div id="<?php echo $facets_group_uuid; ?>">

			<div class="wdm_row">
				<div class='col_left'>Group name</div>
				<div class='col_right'>
					<input type="text" id="<?php echo esc_attr( $facets_group_uuid ); ?>_group_name"
					       name="<?php echo $options_name; ?>[<?php echo WPSOLR_Option::OPTION_FACETS_GROUPS; ?>][<?php echo $facets_group_uuid; ?>][name]"
					       value="<?php echo $new_facets_group_uuid == $facets_group_uuid ? '' : esc_attr( $facets_group['name'] ); ?>"/>

					<!--<input name="create_group" value="Clone the group"/>-->
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Group filter</div>
				<div class='col_right'>
					<input type="text" style="width: 100%"
					       name="<?php echo $options_name; ?>[<?php echo WPSOLR_Option::OPTION_FACETS_GROUPS; ?>][<?php echo $facets_group_uuid; ?>][<?php echo WPSOLR_Option::OPTION_FACETS_GROUP_FILTER_QUERY; ?>]"
					       value="<?php echo ! empty( $facets_group[ WPSOLR_Option::OPTION_FACETS_GROUP_FILTER_QUERY ] ) ? esc_attr( $facets_group[ WPSOLR_Option::OPTION_FACETS_GROUP_FILTER_QUERY ] ) : ''; ?>"/>
					<!--<input name="create_group" value="Clone the group"/>-->
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Always show facets</div>
				<div class='col_right'>
					<input type="checkbox"
					       name="<?php echo $options_name; ?>[<?php echo WPSOLR_Option::OPTION_FACETS_GROUPS; ?>][<?php echo $facets_group_uuid; ?>][<?php echo WPSOLR_Option::OPTION_FACETS_GROUP_EXCLUSION; ?>]"
					       value="1" <?php checked( ! empty( $facets_group[ WPSOLR_Option::OPTION_FACETS_GROUP_EXCLUSION ] ) ); ?>/>
				</div>
				<div class="clear"></div>
			</div>

			<ul class="sortable connectedSortable">

				<?php
				foreach ( ! empty( $facets_selected[ $facets_group_uuid ] ) ? $facets_selected[ $facets_group_uuid ] : [ ] as $facet_selected_name => $facet_selected ) {


					WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet.inc.php' ),
						array(
							'options_name'         => $options_name,
							'facets_group_uuid'    => $facets_group_uuid,
							'layouts_facets'       => $layouts_facets[ WPSOLR_Global::getExtensionFields()->get_field_type_definition( $facet_selected_name )->get_id() ],
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


				foreach ( $fields as $field_name => $field ) {

					$field_name = strtolower( $field_name );

					if ( ! isset( $facets_selected[ $facets_group_uuid ] ) || ! isset( $facets_selected[ $facets_group_uuid ][ $field_name ] ) ) {

						WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet.inc.php' ),
							array(
								'options_name'         => $options_name,
								'facets_group_uuid'    => $facets_group_uuid,
								'layouts_facets'       => $layouts_facets[ $field['solr_type'] ],
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

		</div>

	<?php } ?>

</div>



