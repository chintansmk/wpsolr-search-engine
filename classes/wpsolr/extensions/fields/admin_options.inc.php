<?php
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Option;

?>

<script>
	jQuery(document).ready(function () {

		jQuery('[name=save_selected_index_options_form]').click(function () {

			<?php
			$fields_checkboxes_concatenated = [
				WPSOLR_Option::OPTION_FIELDS_POST_TYPES,
				WPSOLR_Option::OPTION_FIELDS_ATTACHMENTS,
				WPSOLR_Option::OPTION_FIELDS_TAXONOMIES
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

		jQuery(".<?php echo WPSOLR_Option::OPTION_FIELDS_CUSTOM_FIELDS ?> input:checkbox").click(function () {

			console.log('.' + jQuery(this).val() + ':' + jQuery(this).prop("checked"));

			jQuery('.' + jQuery(this).val()).children().toggle(jQuery(this).prop("checked"));
			jQuery('.' + jQuery(this).val()).children().prop('disabled', !jQuery(this).prop("checked"));
		});

	});
</script>

<div id="solr-indexing-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST" id='settings_form'>
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>

		<div class='indexing_option wrapper'>
			<h4 class='head_div'>Indexing Options</h4>

			<div class="wdm_note">

				In this section, you will choose among all the data stored in your Wordpress
				site, which you want to load in your Solr index.

			</div>

			<div class="wdm_row">
				<div class='col_left'>
					Index post excerpt.<br/>
					Excerpt will be added to the post content, and be searchable, highlighted,
					and
					autocompleted.
				</div>
				<div class='col_right'>
					<input type='checkbox'
					       name='wdm_solr_form_data[<?php echo WPSOLR_Option::OPTION_FIELDS_ARE_POST_EXCERPTS_INDEXED ?>]'
					       value='1' <?php checked( '1', isset( $options[ WPSOLR_Option::OPTION_FIELDS_ARE_POST_EXCERPTS_INDEXED ] ) ? $options[ WPSOLR_Option::OPTION_FIELDS_ARE_POST_EXCERPTS_INDEXED ] : '' ); ?>>

				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>
					Expand shortcodes of post content before indexing.<br/>
					Else, shortcodes will simply be stripped.
				</div>
				<div class='col_right'>
					<input type='checkbox'
					       name='wdm_solr_form_data[<?php echo WPSOLR_Option::OPTION_FIELDS_IS_SHORTCODE_EXPANDED ?>]'
					       value='1' <?php checked( '1', isset( $options[ WPSOLR_Option::OPTION_FIELDS_IS_SHORTCODE_EXPANDED ] ) ? $options[ WPSOLR_Option::OPTION_FIELDS_IS_SHORTCODE_EXPANDED ] : '' ); ?>>
				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Post types to be indexed</div>
				<div class='col_right'>
					<input type='hidden'
					       name='wdm_solr_form_data[<?php echo WPSOLR_Option::OPTION_FIELDS_POST_TYPES ?>]'
					       id='<?php echo WPSOLR_Option::OPTION_FIELDS_POST_TYPES ?>'>
					<?php
					$indexed_items = ! empty( $options[ WPSOLR_Option::OPTION_FIELDS_POST_TYPES ] ) ? $options[ WPSOLR_Option::OPTION_FIELDS_POST_TYPES ] : '';
					foreach ( $indexable_post_types as $indexable_item ) {
						?>

						<div class='wpsolr-4col'>
							<input type='checkbox' name='<?php echo WPSOLR_Option::OPTION_FIELDS_POST_TYPES ?>'
							       value='<?php echo $indexable_item ?>'
								<?php if ( strpos( $indexed_items, $indexable_item ) !== false ) { ?> checked <?php } ?>>
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
					<br/><br/>
					If a custom field is a <b>numeric</b> (price, volume, distance, weight), or a <b>date</b>, it can be
					queried by range. If so, select it's numeric type among: integer, long, double, float, date.
					<br/><br/>
					If a custom field has been specifically <b>defined in Solr schema.xml</b>, select it's type as
					'Custom type'.
				</div>
				<div class='col_right'>
					<div class="wpsolr_overflow">
						<?php
						if ( count( $indexable_custom_fields ) > 0 ) {
							foreach ( $indexable_custom_fields as $indexable_custom_field ) {

								$is_indexed_custom_field = ( ! empty( $selected_custom_fields[ $indexable_custom_field ] ) );
								$solr_type               = $is_indexed_custom_field && ! empty( $selected_custom_fields[ $indexable_custom_field ]['solr_type'] )
									? $selected_custom_fields[ $indexable_custom_field ]['solr_type']
									: '';
								?>

								<div class='wpsolr-1col'>
									<div class='wpsolr-2col <?php echo WPSOLR_Option::OPTION_FIELDS_CUSTOM_FIELDS ?>'>
										<input
											type='checkbox'
											name='<?php echo WPSOLR_Option::OPTION_FIELDS_CUSTOM_FIELDS ?>'
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
											    name='wdm_solr_form_data[<?php echo WPSOLR_Option::OPTION_FIELDS_CUSTOM_FIELDS ?>][<?php echo $indexable_custom_field ?>][solr_type]'>
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
						<input type='hidden'
						       name='wdm_solr_form_data[<?php echo WPSOLR_Option::OPTION_FIELDS_ATTACHMENTS ?>]'
						       id='<?php echo WPSOLR_Option::OPTION_FIELDS_ATTACHMENTS ?>'>
						<?php
						$indexed_items = ! empty( $options[ WPSOLR_Option::OPTION_FIELDS_ATTACHMENTS ] ) ? $options[ WPSOLR_Option::OPTION_FIELDS_ATTACHMENTS ] : '';
						foreach ( $allowed_attachments_types as $indexable_item ) {
							?>

							<div class='wpsolr-2col'>
								<input type='checkbox' name='<?php echo WPSOLR_Option::OPTION_FIELDS_ATTACHMENTS ?>'
								       value='<?php echo $indexable_item ?>'
									<?php if ( strpos( $indexed_items, $indexable_item ) !== false ) { ?> checked <?php } ?>>
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
						<input type='hidden'
						       name='wdm_solr_form_data[<?php echo WPSOLR_Option::OPTION_FIELDS_TAXONOMIES ?>]'
						       id='<?php echo WPSOLR_Option::OPTION_FIELDS_TAXONOMIES ?>'>
						<?php
						$indexed_items = ! empty( $options[ WPSOLR_Option::OPTION_FIELDS_TAXONOMIES ] ) ? $options[ WPSOLR_Option::OPTION_FIELDS_TAXONOMIES ] : '';
						if ( count( $taxonomies ) > 0 ) {
							foreach ( $taxonomies as $indexable_item ) {
								?>

								<div class='wpsolr-2col'>
									<input type='checkbox' name='<?php echo WPSOLR_Option::OPTION_FIELDS_TAXONOMIES ?>'
									       value='<?php echo $indexable_item . "_str" ?>'
										<?php if ( strpos( $indexed_items, $indexable_item ) !== false ) { ?> checked <?php } ?>>
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
				<div class='col_left'>Index Comments</div>
				<div class='col_right'>
					<input type='checkbox' name='wdm_solr_form_data[comments]'
					       value='1' <?php checked( '1', isset( $options['comments'] ) ? $options['comments'] : '' ); ?>>

				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Exclude items (Posts,Pages,...)</div>
				<div class='col_right'>
					<input type='text' name='wdm_solr_form_data[<?php echo WPSOLR_Option::OPTION_FIELDS_EXCLUDE_IDS ?>]'
					       placeholder="Comma separated ID's list"
					       value="<?php echo empty( $options[ WPSOLR_Option::OPTION_FIELDS_EXCLUDE_IDS ] ) ? '' : $options[ WPSOLR_Option::OPTION_FIELDS_EXCLUDE_IDS ]; ?>">
					<br>
					(Comma separated ids list)
				</div>
				<div class="clear"></div>
			</div>
			<div class='wdm_row'>
				<div class="submit">
					<input name="save_selected_index_options_form"
					       type="submit"
					       class="button-primary wdm-save" value="Save Options"/>
				</div>
			</div>
		</div>
	</form>
</div>