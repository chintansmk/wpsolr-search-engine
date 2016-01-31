<?php
use wpsolr\extensions\sorts\WPSOLR_Options_Sorts;
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

		var tabs = jQuery(".tabs").tabs({active: <?php echo $group_tab_selected; ?>});
		tabs.delegate("span.ui-icon-close", "click", function () {
			var panelId = jQuery(this).closest("li").remove().attr("aria-controls");
			jQuery("#" + panelId).remove();
			tabs.tabs("refresh");
		});

		jQuery(".sortable").sortable();
		jQuery(".sortable").accordion({active: false, collapsible: true, heightStyle: "content"});

	});
</script>

<div class="wdm_row">
	<div class='col_left'>Sorts displayed in search pages</div>
	<div class='col_right'>
		<select
			name="<?php echo $options_name; ?>[<?php echo WPSOLR_Option::OPTION_SORTS_GROUP_DEFAULT_ID; ?>]">
			<?php
			foreach ( array_merge( [ '' => [ 'name' => 'Select a group' ] ], $sorts_groups ) as $sorts_group_uuid => $sorts_group ) { ?>
				<?php if ( $new_sorts_group_uuid != $sorts_group_uuid ) { ?>
					<option
						value="<?php echo $sorts_group_uuid; ?>" <?php selected( $sorts_group_uuid, $default_sorts_group_uuid, true ); ?>>
						<?php echo $sorts_group['name']; ?>
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
		foreach ( $sorts_groups as $sorts_group_uuid => $sorts_group ) { ?>
			<li><a href="#<?php echo $sorts_group_uuid; ?>"><?php echo $sorts_group['name']; ?></a>
				<?php if ( $new_sorts_group_uuid !== $sorts_group_uuid ) { ?>
					<span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
	<?php foreach ( $sorts_groups as $sorts_group_uuid => $sorts_group ) { ?>
		<div id="<?php echo $sorts_group_uuid; ?>">

			<div class="wdm_row">
				<div class='col_left'>Group name</div>
				<div class='col_right'>
					<input type="text" id="<?php echo esc_attr( $sorts_group_uuid ); ?>_group_name"
					       name="<?php echo $options_name; ?>[<?php echo WPSOLR_Option::OPTION_SORTS_GROUPS; ?>][<?php echo $sorts_group_uuid; ?>][name]"
					       value="<?php echo $new_sorts_group_uuid == $sorts_group_uuid ? '' : esc_attr( $sorts_group['name'] ); ?>"/>

					<!--<input name="create_group" value="Clone the group"/>-->
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Default sort field</div>
				<div class='col_right'>
					<select
						name="<?php echo $options_name; ?>[<?php echo WPSOLR_Option::OPTION_SORTS_GROUPS; ?>][<?php echo $sorts_group_uuid; ?>][<?php echo WPSOLR_Options_Sorts::SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD; ?>]">
						<?php
						$default_sort_field = isset( $sorts_group[ WPSOLR_Options_Sorts::SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD ] )
							? $sorts_group[ WPSOLR_Options_Sorts::SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD ] :
							WPSOLR_Options_Sorts::SORT_CODE_BY_RELEVANCY_DESC;
						foreach ( $fields as $field_name => $field ) { ?>
							<option
								value="<?php echo $field_name; ?>" <?php selected( $field_name, $default_sort_field, true ); ?>>
								<?php echo $field_name; ?>
							</option>
						<?php } ?>
					</select>
				</div>
				<div class="clear"></div>
			</div>

			<ul class="sortable connectedSortable">

				<?php
				foreach ( ! empty( $sorts_selected[ $sorts_group_uuid ] ) ? $sorts_selected[ $sorts_group_uuid ] : [ ] as $sort_selected_name => $sort_selected ) {


					WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_SORTS, 'sort.inc.php' ),
						array(
							'options_name'        => $options_name,
							'sorts_group_uuid'    => $sorts_group_uuid,
							'layouts'             => $layouts,
							'sort_name'           => $sort_selected_name,
							'is_numeric'          => WPSOLR_Global::getExtensionFields()->get_field_type_definition( $sort_selected_name )->get_is_numeric(),
							'sort'                => $sort_selected,
							'sort_selected_class' => $sort_selected_class,
							'image_plus_display'  => 'none',
							'image_minus_display' => 'inline',
							'image_plus'          => $image_plus,
							'image_minus'         => $image_minus

						) );
				}


				foreach ( $fields as $field_name => $field ) {

					$field_name = strtolower( $field_name );

					if ( ! isset( $sorts_selected[ $sorts_group_uuid ] ) || ! isset( $sorts_selected[ $sorts_group_uuid ][ $field_name ] ) ) {

						WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_SORTS, 'sort.inc.php' ),
							array(
								'options_name'        => $options_name,
								'sorts_group_uuid'    => $sorts_group_uuid,
								'layouts'             => $layouts,
								'sort_name'           => $field_name,
								'is_numeric'          => WPSOLR_Global::getExtensionFields()->get_field_type_definition( $field_name )->get_is_numeric(),
								'sort'                => $field,
								'sort_selected_class' => $sort_not_selected_class,
								'image_plus_display'  => 'inline',
								'image_minus_display' => 'none',
								'image_plus'          => $image_plus,
								'image_minus'         => $image_minus
							) );

					}
				}
				?>

			</ul>

		</div>

	<?php } ?>

</div>



