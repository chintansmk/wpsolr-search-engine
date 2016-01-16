<?php
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\WPSOLR_Schema;

?>

<script>
	jQuery(document).ready(function () {

		jQuery('#save_selected_index_options_form').click(function () {

			ps_types = '';
			tax = '';
			fields = '';
			attachment_types = '';

			jQuery("input:checkbox[name=post_tys]:checked").each(function () {
				ps_types += jQuery(this).val() + ',';
			});
			pt_tp = ps_types.substring(0, ps_types.length - 1);
			jQuery('#p_types').val(pt_tp);

			jQuery("input:checkbox[name=attachment_types]:checked").each(function () {
				attachment_types += jQuery(this).val() + ',';
			});
			attachment_types = attachment_types.substring(0, attachment_types.length - 1);
			jQuery('#attachment_types').val(attachment_types);

			jQuery("input:checkbox[name=taxon]:checked").each(function () {
				tax += jQuery(this).val() + ',';
			});
			tx = tax.substring(0, tax.length - 1);
			jQuery('#tax_types').val(tx);

			jQuery("input:checkbox[name=cust_fields]:checked").each(function () {
				fields += jQuery(this).val() + ',';
			});
			fl = fields.substring(0, fields.length - 1);
			jQuery('#cust_fields').val(fl);

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
					<input type='checkbox' name='wdm_solr_form_data[p_excerpt]'
					       value='1' <?php checked( '1', isset( $options['p_excerpt'] ) ? $options['p_excerpt'] : '' ); ?>>

				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>
					Expand shortcodes of post content before indexing.<br/>
					Else, shortcodes will simply be stripped.
				</div>
				<div class='col_right'>
					<input type='checkbox' name='wdm_solr_form_data[is_shortcode_expanded]'
					       value='1' <?php checked( '1', isset( $options['is_shortcode_expanded'] ) ? $options['is_shortcode_expanded'] : '' ); ?>>

				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Post types to be indexed</div>
				<div class='col_right'>
					<input type='hidden' name='wdm_solr_form_data[p_types]' id='p_types'>
					<?php
					$post_types_selected = $options['p_types'];
					foreach ( $indexable_post_types as $indexable_post_type ) {
						?>

						<div class='wpsolr-2col'>
							<input type='checkbox' name='post_tys' value='<?php echo $indexable_post_type ?>'
								<?php if ( strpos( $post_types_selected, $indexable_post_type ) !== false ) { ?> checked <?php } ?>>
							<?php echo $indexable_post_type ?>
						</div>

						<?php
					}
					?>

				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Attachment types to be indexed</div>
				<div class='col_right'>
					<input type='hidden' name='wdm_solr_form_data[attachment_types]'
					       id='attachment_types'>
					<?php
					$attachment_types_opt = $options['attachment_types'];
					foreach ( $allowed_attachments_types as $indexable_post_type ) {
						?>

						<div class='wpsolr-2col'>
							<input type='checkbox' name='attachment_types'
							       value='<?php echo $indexable_post_type ?>'
								<?php if ( strpos( $attachment_types_opt, $indexable_post_type ) !== false ) { ?> checked <?php } ?>>
							<?php echo $indexable_post_type ?>
						</div>

						<?php
					}
					?>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Custom taxonomies to be indexed</div>
				<div class='col_right'>
					<div class='cust_tax'><!--new div class given-->
						<input type='hidden' name='wdm_solr_form_data[taxonomies]'
						       id='tax_types'>
						<?php
						$tax_types_opt = $options['taxonomies'];
						if ( count( $taxonomies ) > 0 ) {
							foreach ( $taxonomies as $indexable_post_type ) {
								?>

								<div class='wpsolr-2col'>
									<input type='checkbox' name='taxon'
									       value='<?php echo $indexable_post_type . "_str" ?>'
										<?php if ( strpos( $tax_types_opt, $indexable_post_type . "_str" ) !== false ) { ?> checked <?php } ?>>
									<?php echo $indexable_post_type ?>
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
				<div class='col_left'>Custom Fields to be indexed</div>

				<div class='col_right'>
					<!--<input type='hidden' name='wdm_solr_form_data[cust_fields]' id='cust_fields'>-->

					<div class='cust_fields'>
						<?php
						if ( count( $indexable_custom_fields ) > 0 ) {
							foreach ( $indexable_custom_fields as $indexable_custom_field ) {

								//$selected_custom_fields = [ '_acf1_str' => [ 'solr_type' => '_f' ], '_acf2_str' => [ 'solr_type' => '_s' ] ];

								$is_indexed_custom_field = ( ! empty( $selected_custom_fields[ $indexable_custom_field . "_str" ] ) );
								$solr_type               = $is_indexed_custom_field && ! empty( $selected_custom_fields[ $indexable_custom_field . '_str' ]['solr_type'] )
									? $selected_custom_fields[ $indexable_custom_field . '_str' ]['solr_type']
									: '';
								?>

								<div class='wpsolr-1col'>
									<div class='wpsolr-2col'>
										<input
											type='checkbox'
											name='cust_fields'
											value='<?php echo $indexable_custom_field . "_str" ?>'
											<?php if ( $is_indexed_custom_field ) { ?> checked <?php } ?>>
										<?php echo $indexable_custom_field ?>
									</div>

									<?php if ( $is_indexed_custom_field ) { ?>
										<div style="align:right">
											<select
												name='wdm_solr_form_data[cust_fields][<?php echo $indexable_custom_field . '_str' ?>][solr_type]'>
												<?php
												foreach ( $solr_types as $item => $value ) {
													echo sprintf( '<option value="%s" %s>%s</option>', $item, selected( $solr_type, $item, false ), WPSOLR_Schema::get_solr_type_name( $value ) );
												}
												?>
											</select>
										</div>
										<?php
									} /* if */ ?>

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
					<input type='text' name='wdm_solr_form_data[exclude_ids]'
					       placeholder="Comma separated ID's list"
					       value="<?php echo empty( $options['exclude_ids'] ) ? '' : $options['exclude_ids']; ?>">
					<br>
					(Comma separated ids list)
				</div>
				<div class="clear"></div>
			</div>
			<div class='wdm_row'>
				<div class="submit">
					<input name="save_selected_index_options_form"
					       id="save_selected_index_options_form" type="submit"
					       class="button-primary wdm-save" value="Save Options"/>


				</div>
			</div>

		</div>
	</form>
</div>