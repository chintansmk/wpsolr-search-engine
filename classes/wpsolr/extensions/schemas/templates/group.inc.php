<?php
use wpsolr\extensions\schemas\WPSOLR_Options_Schemas;

?>

<script>
	jQuery(document).ready(function () {

		// Manage custom fields selection
		jQuery(".<?php echo WPSOLR_Options_Schemas::OPTION_FIELDS_CUSTOM_FIELDS ?> input:checkbox").click(function () {

			jQuery(this).parent().next().children().toggle(jQuery(this).prop("checked"));
			jQuery(this).parent().next().children().prop('disabled', !jQuery(this).prop("checked"));
		});

	});
</script>

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

		Excerpt will be added to the post content, and be searchable, highlighted,
		and
		autocompleted.
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

		Expand shortcodes of post content before indexing. Else, shortcodes will simply be stripped.
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

		<p>If a custom field is a numeric (price, volume, distance, weight), or a date, it can
			be
			queried by range. If so, select it's numeric type among: integer, long, double, float, date.
			<br/><br/>
			If a custom field has been specifically defined in Solr schema.xml, select it's type as
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
		Comma separated ids list
	</div>
	<div class="clear"></div>
</div>




