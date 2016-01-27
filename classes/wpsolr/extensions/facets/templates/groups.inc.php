<?php
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

		var tabs = jQuery(".tabs").tabs();
		tabs.delegate("span.ui-icon-close", "click", function () {
			var panelId = jQuery(this).closest("li").remove().attr("aria-controls");
			jQuery("#" + panelId).remove();
			tabs.tabs("refresh");
		});


		jQuery(".sortable").sortable();
		jQuery(".sortable").accordion({active: false, collapsible: true, heightStyle: "content"});

		var dialog, form;

		dialog = jQuery("#dialog_form_group").dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons: {
				//"Create an account": addUser,
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

		jQuery("input[name=create_group]").button().on("click", function () {
			dialog.dialog("open");
			event.preventDefault();
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

			<ul class="sortable connectedSortable">

				<?php
				foreach ( ! empty( $facets_selected[ $facets_group_uuid ] ) ? $facets_selected[ $facets_group_uuid ] : [ ] as $facet_selected_name => $facet_selected ) {


					WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet.inc.php' ),
						array(
							'options_name'         => $options_name,
							'facets_group_uuid'    => $facets_group_uuid,
							'layouts'              => $layouts,
							'facet_name'           => $facet_selected_name,
							'is_range'             => WPSOLR_Global::getExtensionFields()->get_field_type_definition( $facet_selected_name )->get_is_range(),
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
								'layouts'              => $layouts,
								'facet_name'           => $field_name,
								'is_range'             => WPSOLR_Global::getExtensionFields()->get_field_type_definition( $field_name )->get_is_range(),
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



