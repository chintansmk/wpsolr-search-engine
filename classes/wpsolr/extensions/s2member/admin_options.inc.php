<?php

/**
 * Included file to display admin options
 */

use wpsolr\extensions\s2member\WPSOLR_Plugin_S2member;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Option;

?>

<div id="extension_s2member-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST" id='extension_s2member_settings_form'>
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>

		<div class='wrapper'>
			<h4 class='head_div'>s2Member plugin Options</h4>

			<div class="wdm_note">

				In this section, you will configure how to restrict Solr search with levels
				and capabilities.<br/>

				<?php if ( ! $is_plugin_active ): ?>
					<p>
						Status: <a href="https://wordpress.org/plugins/s2member/"
						           target="_blank">s2Member
							plugin</a> is not activated. First, you need to install and
						activate it to configure WPSOLR.
					</p>
					<p>
						You will also need to re-index all your data if you activated
						<a href="https://wordpress.org/plugins/s2member/"
						   target="_blank">s2Member
							plugin</a>
						after you activated WPSOLR.
					</p>
				<?php else: ?>
					<p>
						Status: <a href="https://wordpress.org/plugins/s2member/"
						           target="_blank">s2Member
							plugin</a>
						is activated. You can now configure WPSOLR to use it.
					</p>
				<?php endif; ?>
				<?php if ( ( ! $is_plugin_custom_field_for_indexing ) && ( isset( $options[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE ] ) ) ): ?>
					<p>
						The custom field <b>'<?php echo $custom_field_for_indexing_name ?>
							'</b>
						is not selected,
						which means WPSOLR will not be able to index data from <a
							href="https://wordpress.org/plugins/s2member/"
							target="_blank">s2Member
							plugin</a>.
						<br/>Please go to 'Indexing options' tab, and check
						<b>'<?php echo $custom_field_for_indexing_name ?>'</b>.
						<br/>You should also better re-index your data.
					</p>
				<?php endif; ?>

			</div>
			<div class="wdm_row">
				<div class='col_left'>Use the <a
						href="https://wordpress.org/plugins/s2member/"
						target="_blank">s2Member (>= 150203)
						plugin</a>
					to filter search results.
					<br/>Think of re-indexing all your data if <a
						href="https://wordpress.org/plugins/s2member/"
						target="_blank">s2Member
						plugin</a> was installed after WPSOLR.
				</div>
				<div class='col_right'>
					<input type='checkbox'
					       name='wdm_solr_extension_s2member_data[<?php echo WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE; ?>]'
					       value='<?php echo WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE; ?>'
						<?php WPSOLR_Service_Wordpress::checked( WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE, isset( $options[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE ] ) ? $options[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE ] : '', true ); ?>>
				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Users without levels/custom capabilities can see all results, <br/>
					whatever the results levels/custom capabilities.
				</div>
				<div class='col_right'>
					<input type='checkbox'
					       name='wdm_solr_extension_s2member_data[is_users_without_capabilities_see_all_results]'
					       value='is_users_without_capabilities_see_all_results'
						<?php WPSOLR_Service_Wordpress::checked( 'is_users_without_capabilities_see_all_results', isset( $options['is_users_without_capabilities_see_all_results'] ) ? $options['is_users_without_capabilities_see_all_results'] : '', true ); ?>>
				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Results without level/custom capabilities can be seen by all users,
					<br/> whatever the users levels/custom capabilities.
				</div>
				<div class='col_right'>
					<input type='checkbox'
					       name='wdm_solr_extension_s2member_data[is_result_without_capabilities_seen_by_all_users]'
					       value='is_result_without_capabilities_seen_by_all_users'
						<?php WPSOLR_Service_Wordpress::checked( 'is_result_without_capabilities_seen_by_all_users', isset( $options['is_result_without_capabilities_seen_by_all_users'] ) ? $options['is_result_without_capabilities_seen_by_all_users'] : '', true ); ?>>
				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Phrase to display if you forbid users without levels/custom capabilities
					to see any results
				</div>
				<div class='col_right'>
												<textarea id='message_user_without_capabilities_shown_no_results'
												          name='wdm_solr_extension_s2member_data[message_user_without_capabilities_shown_no_results]'
												          rows="4" cols="100"
												          placeholder="<?php echo WPSOLR_Plugin_S2member::DEFAULT_MESSAGE_NOT_AUTHORIZED; ?>"><?php echo empty( $options['message_user_without_capabilities_shown_no_results'] ) ? trim( WPSOLR_Plugin_S2member::DEFAULT_MESSAGE_NOT_AUTHORIZED ) : $options['message_user_without_capabilities_shown_no_results']; ?></textarea>
					<span class='res_err'></span><br>
				</div>
				<div class="clear"></div>
			</div>
			<div class='wdm_row'>
				<div class="submit">
					<input <?php echo $is_plugin_active ? '' : 'disabled' ?>
						name="save_selected_options_res_form"
						id="save_selected_extension_s2member_form" type="submit"
						class="button-primary wdm-save"
						value="<?php echo $is_plugin_active ? 'Save Options' : sprintf( 'Install and activate the plugin %s first.', $plugin_name ); ?>"/>
				</div>
			</div>
		</div>

	</form>
</div>