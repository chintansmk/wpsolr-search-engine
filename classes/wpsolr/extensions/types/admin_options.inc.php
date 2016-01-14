<?php

/**
 * Included file to display admin options
 */
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Option;

?>

<div id="extension_groups-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST" id='extension_groups_settings_form'>
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>

		<div class='wrapper'>
			<h4 class='head_div'><?php echo $plugin_name; ?> plugin Options</h4>

			<div class="wdm_note">

				In this section, you will configure WPSOLR to work with <?php echo $plugin_name; ?>.<br/>

				<?php if ( ! $is_plugin_active ): ?>
					<p>
						Status: <a href="<?php echo $plugin_link; ?>"
						           target="_blank"><?php echo $plugin_name; ?>
							plugin</a> is not activated. First, you need to install and
						activate it to configure WPSOLR.
					</p>
					<p>
						You will also need to re-index all your data if you activated
						<a href="<?php echo $plugin_link; ?>" target="_blank"><?php echo $plugin_name; ?>
							plugin</a>
						after you activated WPSOLR.
					</p>
				<?php else : ?>
					<p>
						Status: <a href="<?php echo $plugin_link; ?>"
						           target="_blank"><?php echo $plugin_name; ?>
							plugin</a>
						is activated. You can now configure WPSOLR to use it.
					</p>
				<?php endif; ?>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Use the <a
						href="<?php echo $plugin_link; ?>"
						target="_blank"><?php echo $plugin_name; ?> <?php echo $plugin_version; ?>
						plugin</a>
					to filter search results.
					<br/>Think of re-indexing all your data if <a
						href="<?php echo $plugin_link; ?>" target="_blank"><?php echo $plugin_name; ?>
						plugin</a> was installed after WPSOLR.
				</div>
				<div class='col_right'>
					<input type='checkbox' <?php echo $is_plugin_active ? '' : 'readonly' ?>
					       name='<?php echo $options_name; ?>[<?php echo WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE; ?>]'
					       value=WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE
						<?php WPSOLR_Service_Wordpress::checked( WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE, isset( $options[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE ] ) ? $options[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE ] : '', true ); ?>>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Replace custom field name by TYPES custom field label on facets.
				</div>
				<div class='col_right'>
					<input type='checkbox'
					       name='<?php echo $options_name; ?>[display_types_label_on_facet]'
					       value='display_types_label_on_facet'
						<?php WPSOLR_Service_Wordpress::checked( 'display_types_label_on_facet', isset( $options['display_types_label_on_facet'] ) ? $options['display_types_label_on_facet'] : '', true ); ?>>
				</div>
				<div class="clear"></div>
			</div>


			<div class='wdm_row'>
				<div class="submit">
					<input <?php echo $is_plugin_active ? '' : 'disabled' ?>
						name="save_selected_options_res_form"
						id="save_selected_extension_groups_form" type="submit"
						class="button-primary wdm-save"
						value="<?php echo $is_plugin_active ? 'Save Options' : sprintf( 'Install and activate the plugin %s first.', $plugin_name ); ?>"/>
				</div>
			</div>
		</div>

	</form>
</div>
<!-- WPSOLR_END -->