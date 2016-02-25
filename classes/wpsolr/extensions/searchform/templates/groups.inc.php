<?php
use wpsolr\extensions\searchform\WPSOLR_Options_Search_Form;

?>

<style>

	.tabs {
		margin-top: 1em;
	}

	.tabs li .ui-icon-close {
		float: left;
		margin: 0.4em 0.2em 0 0;
		cursor: pointer;
	}

</style>

<script>
	jQuery(document).ready(function () {

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

		// Remove button action
		tabs.delegate("span.ui-icon-close", "click", function () {
			var panelId = jQuery(this).closest("li").remove().attr("aria-controls");
			jQuery("#" + panelId).remove();
			tabs.tabs("refresh");
		});

		// Tabs are sortable
		jQuery(".sortable").sortable();
		jQuery(".sortable").accordion({active: false, collapsible: true, heightStyle: "content"});

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
	<?php foreach ( $groups as $group_uuid => $group ) {
		?>
		<div id="<?php echo $group_uuid; ?>">

			<div class="wdm_row">
				<div class='col_left'>Group name</div>
				<div class='col_right'>
					<input style="width:40%" type="text" id="<?php echo esc_attr( $group_uuid ); ?>_group_name"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, 'name' ); ?>"
					       value="<?php echo $new_group_uuid == $group_uuid ? '' : esc_attr( $group['name'] ); ?>"/>

					<?php if ( $new_group_uuid != $group_uuid ) { ?>
						<input type="checkbox"
						       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, 'is_to_be_cloned' ); ?>"
						       value="1"/> Clone this group when saving
					<?php } ?>

				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Infinitescroll</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Search_Form::FORM_FIELD_IS_INFINITESCROLL ] ); ?>
					<input type="checkbox"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Search_Form::FORM_FIELD_IS_INFINITESCROLL ); ?>"
						<?php checked( $value ); ?>
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>No. of results per page</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Search_Form::FORM_FIELD_MAX_NB_RESULTS_BY_PAGE ] ) ? $group[ WPSOLR_Options_Search_Form::FORM_FIELD_MAX_NB_RESULTS_BY_PAGE ] : WPSOLR_Options_Search_Form::FORM_FIELD_DEFAULT_MAX_NB_RESULTS_BY_PAGE; ?>
					<input type="text"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Search_Form::FORM_FIELD_MAX_NB_RESULTS_BY_PAGE ); ?>"
					       value="<?php echo esc_attr( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Maximum size of each snippet text in results</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Search_Form::FORM_FIELD_HIGHLIGHTING_FRAGSIZE ] ) ? $group[ WPSOLR_Options_Search_Form::FORM_FIELD_HIGHLIGHTING_FRAGSIZE ] : WPSOLR_Options_Search_Form::FORM_FIELD_DEFAULT_HIGHLIGHTING_FRAGSIZE; ?>
					<input type="text"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Search_Form::FORM_FIELD_HIGHLIGHTING_FRAGSIZE ); ?>"
					       value="<?php echo esc_attr( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

		</div>
	<?php } ?>

</div>


