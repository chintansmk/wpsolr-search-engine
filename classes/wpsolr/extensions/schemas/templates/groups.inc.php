<?php
use wpsolr\extensions\schemas\WPSOLR_Options_Schemas;

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

		jQuery('[name=save_selected_index_options_form]').click(function () {

			<?php
			$fields_checkboxes_concatenated = [
				WPSOLR_Options_Schemas::OPTION_FIELDS_POST_TYPES,
				WPSOLR_Options_Schemas::OPTION_FIELDS_ATTACHMENTS,
				WPSOLR_Options_Schemas::FORM_FIELD_TAXONOMIES
			];
			foreach ($fields_checkboxes_concatenated as $field_name) { ?>

			$concatenated_value = '';
			jQuery("input:checkbox[name=<?php echo $field_name; ?>]:checked").each(function () {
				$concatenated_value += jQuery(this).val() + ',';
			});
			$concatenated_value = $concatenated_value.substring(0, $concatenated_value.length - 1);
			jQuery('#<?php echo $field_name ?>').val($concatenated_value);

			<?php } ?>

		});

		jQuery(".<?php echo WPSOLR_Options_Schemas::OPTION_FIELDS_CUSTOM_FIELDS ?> input:checkbox").click(function () {

			jQuery(this).parent().next().children().toggle(jQuery(this).prop("checked"));
			jQuery(this).parent().next().children().prop('disabled', !jQuery(this).prop("checked"));
		});


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

		// Toggle default group
		jQuery('.is_default').click(function () {

			if (jQuery(this).prop("checked")) {

				// Uncheck all other checks
				jQuery('.is_default').not(this).prop("checked", false);
			}

		})
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
				<div class='col_left'>Solr indexes with theses fields</div>
				<div class='col_right'>
					<?php
					$indexes_selected = ! empty( $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_SOLR_INDEXES ] ) ? $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_SOLR_INDEXES ] : [ ];
					foreach ( $indexes as $index_id => $index ) {
						?>
						<div class='wpsolr-3col'>
							<input type='checkbox'
							       name="<?php echo sprintf( '%s[%s][%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::OPTION_FIELDS_SOLR_INDEXES, $index_id ); ?>"
							       value='<?php echo $index_id ?>'
								<?php checked( ! empty( $indexes_selected[ $index_id ] ) ); ?>>
							<?php echo $index['index_name']; ?>
						</div>

						<?php
					}
					?>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>
					Index post excerpt
				</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_ARE_POST_EXCERPTS_INDEXED ] ); ?>
					<input type='checkbox'
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::OPTION_FIELDS_ARE_POST_EXCERPTS_INDEXED ); ?>"
					       value='1'
						<?php echo checked( $value ); ?>>

					<p>Excerpt will be added to the post content, and be searchable, highlighted,
						and
						autocompleted.</p>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>
					Expand shortcodes
				</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_IS_SHORTCODE_EXPANDED ] ); ?>
					<input type='checkbox'
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::OPTION_FIELDS_IS_SHORTCODE_EXPANDED ); ?>"
					       value='1'
						<?php echo checked( $value ); ?>>

					<p>Expand shortcodes of post content before indexing. Else, shortcodes will simply be stripped.</p>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Post types to be indexed</div>
				<div class='col_right'>
					<?php
					$indexed_items = ! empty( $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_POST_TYPES ] ) ? $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_POST_TYPES ] : [ ];
					foreach ( $indexable_post_types as $indexable_item ) {
						?>
						<div class='wpsolr-4col'>
							<input type='checkbox'
							       name="<?php echo sprintf( '%s[%s][%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::OPTION_FIELDS_POST_TYPES, $indexable_item ); ?>"
							       value='<?php echo $indexable_item ?>'
								<?php checked( ! empty( $indexed_items[ $indexable_item ] ) ); ?>>
							<?php echo $indexable_item ?>
						</div>

						<?php
					}
					?>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>
					Custom Fields to be indexed

					<p>If a custom field is a <b>numeric</b> (price, volume, distance, weight), or a <b>date</b>, it can
						be
						queried by range. If so, select it's numeric type among: integer, long, double, float, date.
						<br/><br/>
						If a custom field has been specifically <b>defined in Solr schema.xml</b>, select it's type as
						'Custom type'.</p>
				</div>
				<div class='col_right'>
					<div class="wpsolr_overflow">
						<?php
						if ( count( $indexable_custom_fields ) > 0 ) {
							foreach ( $indexable_custom_fields as $indexable_custom_field ) {

								$selected_custom_fields  = ! empty( $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_CUSTOM_FIELDS ] ) ? $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_CUSTOM_FIELDS ] : [ ];
								$is_indexed_custom_field = ( ! empty( $selected_custom_fields[ $indexable_custom_field ] ) );
								$solr_type               = $is_indexed_custom_field && ! empty( $selected_custom_fields[ $indexable_custom_field ]['solr_type'] )
									? $selected_custom_fields[ $indexable_custom_field ]['solr_type']
									: '';
								?>

								<div class='wpsolr-1col'>
									<div
										class='wpsolr-2col <?php echo WPSOLR_Options_Schemas::OPTION_FIELDS_CUSTOM_FIELDS ?>'>
										<input
											type='checkbox'
											name="<?php echo sprintf( '%s[%s][%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::OPTION_FIELDS_CUSTOM_FIELDS, $indexable_custom_field ); ?>"
											value='<?php echo $indexable_custom_field; ?>'
											<?php if ( $is_indexed_custom_field ) { ?> checked <?php } ?>>
										<?php echo $indexable_custom_field ?>
									</div>

									<div class="<?php echo $indexable_custom_field; ?>"
									     style="float:right;">
										<select style="<?php if ( ! $is_indexed_custom_field ) {
											echo 'display:none';
										} ?>"
											<?php if ( ! $is_indexed_custom_field ) {
												echo ' disabled ';
											} ?>
											    name="<?php echo sprintf( '%s[%s][%s][%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::OPTION_FIELDS_CUSTOM_FIELDS, $indexable_custom_field, 'solr_type' ); ?>"
										<?php
										foreach ( $solr_field_types as $solr_field_type_id => $solr_field_type ) {
											echo sprintf( '<option value="%s" %s>%s</option>', $solr_field_type_id, selected( $solr_type, $solr_field_type_id, false ), $solr_field_type->get_name() );
										}
										?>
										</select>
									</div>
								</div>

								<?php
							}

						} else {
							echo 'None';
						}
						?>
					</div>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Attachment types to be indexed</div>
				<div class='col_right'>
					<div class="wpsolr_overflow">
						<?php
						$indexed_items = ! empty( $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_ATTACHMENTS ] ) ? $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_ATTACHMENTS ] : [ ];
						foreach ( $allowed_attachments_types as $indexable_item ) {
							?>

							<div class='wpsolr-2col'>
								<input type='checkbox'
								       value='<?php echo $indexable_item ?>'
								       name="<?php echo sprintf( '%s[%s][%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::OPTION_FIELDS_ATTACHMENTS, $indexable_item ); ?>"
									<?php echo checked( ( ! empty( $indexed_items[ $indexable_item ] ) ) ); ?>>
								<?php echo $indexable_item ?>
							</div>

							<?php
						}
						?>
					</div>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Custom taxonomies to be indexed</div>
				<div class='col_right'>
					<div>
						<?php
						$indexed_items = ! empty( $group[ WPSOLR_Options_Schemas::FORM_FIELD_TAXONOMIES ] ) ? $group[ WPSOLR_Options_Schemas::FORM_FIELD_TAXONOMIES ] : [ ];
						if ( count( $taxonomies ) > 0 ) {
							foreach ( $taxonomies as $indexable_item ) {
								?>

								<div class='wpsolr-2col'>
									<input type='checkbox'
									       name="<?php echo sprintf( '%s[%s][%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::FORM_FIELD_TAXONOMIES, $indexable_item ); ?>"
									       value='<?php echo $indexable_item; ?>'
										<?php checked( ( ! empty( $indexed_items[ $indexable_item ] ) ) ); ?>>
									<?php echo $indexable_item ?>
								</div>

								<?php
							}

						} else {
							echo 'None';
						} ?>
					</div>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Index Comments
				</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Schemas::FORM_FIELD_IS_INDEX_COMMENTS ] ); ?>
					<input type='checkbox'
					       class="is_default"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::FORM_FIELD_IS_INDEX_COMMENTS ); ?>"
					       value='1'
						<?php echo checked( $value ); ?>>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Exclude items ids (posts, pages,...)</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_EXCLUDE_IDS ] ) ? $group[ WPSOLR_Options_Schemas::OPTION_FIELDS_EXCLUDE_IDS ] : ''; ?>
					<input type="text"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::OPTION_FIELDS_EXCLUDE_IDS ); ?>"
					       value="<?php echo esc_attr( $value ); ?>"
					/>
					<p>(Comma separated ids list)</p>
				</div>
				<div class="clear"></div>
			</div>

		</div>
	<?php } ?>

</div>


