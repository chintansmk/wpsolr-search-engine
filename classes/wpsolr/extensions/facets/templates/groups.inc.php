<?php
use wpsolr\extensions\WPSOLR_Extensions;
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

	#tabs {
		margin-top: 1em;
	}

	#tabs li .ui-icon-close {
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

		jQuery(".tabs").tabs();


		jQuery(".sortable").sortable();
		jQuery(".sortable").accordion({active: false, collapsible: true, heightStyle: "content"});

		var dialog, form;

		// Collapsable instructions
		jQuery(".instructions").accordion({active: false, collapsible: true});

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

<div class="tabs">
	<ul>
		<?php
		foreach ( $facets_groups as $facets_group_uuid => $facets_group ) { ?>
			<li><a href="#<?php echo $facets_group_uuid; ?>"><?php echo $facets_group['name']; ?></a>
				<span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span></li>
		<?php } ?>
	</ul>
	<?php foreach ( $facets_groups as $facets_group_uuid => $facets_group ) { ?>
		<div id="<?php echo $facets_group_uuid; ?>">

			<div class="wdm_note instructions">
				<h4>Instructions</h4>
				<ul class="wdm_ul wdm-instructions">
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

			<div class="wdm_row">
				<div class='col_left'>Group name</div>
				<div class='col_right'>
					<input type="text"
					       name="<?php echo $options_name; ?>[<?php echo WPSOLR_Option::OPTION_FACETS_GROUPS; ?>][<?php echo $facets_group_uuid; ?>][name]"
					       value="<?php echo $new_facets_group_uuid == $facets_group_uuid ? '' : $facets_group['name']; ?>"/>

					<input name="create_group" value="Clone the group"/>

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
							'facet'                => $facet_selected,
							'facet_selected_class' => 'facet_selected',
							'image_plus_display'   => 'none',
							'image_minus_display'  => 'inline',
							'image_plus'           => $image_plus,
							'image_minus'          => $image_minus

						) );
				}


				foreach ( $facets_candidates as $facet_candidate_name => $facet_candidate ) {

					$facet_candidate_name = strtolower( $facet_candidate_name );

					if ( ! $facets_selected[ $facets_group_uuid ] || ! isset( $facets_selected[ $facets_group_uuid ][ $facet_candidate_name ] ) ) {

						WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet.inc.php' ),
							array(
								'options_name'         => $options_name,
								'facets_group_uuid'    => $facets_group_uuid,
								'layouts'              => $layouts,
								'facet_name'           => $facet_candidate_name,
								'facet'                => $facet_candidate,
								'facet_selected_class' => '',
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



