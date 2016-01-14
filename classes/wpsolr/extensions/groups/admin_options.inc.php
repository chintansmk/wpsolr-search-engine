<?php

/**
 * Included file to display admin options
 */

namespace wpsolr\extensions\groups;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;

?>

<div id="extension_groups-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST" id='extension_groups_settings_form'>
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>

		<div class='wrapper'>
			<h4 class='head_div'>Groups plugin Options</h4>

			<div class="wdm_note">

				In this section, you will configure how to restrict Solr search with groups
				and capabilities.<br/>

				<?php if ( ! $is_plugin_active ): ?>
					<p>
						Status: <a href="https://wordpress.org/plugins/groups/"
						           target="_blank">Groups
							plugin</a> is not activated. First, you need to install and
						activate it to configure WPSOLR.
					</p>
					<p>
						You will also need to re-index all your data if you activated
						<a href="https://wordpress.org/plugins/groups/" target="_blank">Groups
							plugin</a>
						after you activated WPSOLR.
					</p>
				<?php else : ?>
					<p>
						Status: <a href="https://wordpress.org/plugins/groups/"
						           target="_blank">Groups
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
							href="https://wordpress.org/plugins/groups/" target="_blank">Groups
							plugin</a>.
						<br/>Please go to 'Indexing options' tab, and check
						<b>'<?php echo $custom_field_for_indexing_name ?>'</b>.
						<br/>You should also better re-index your data.
					</p>
				<?php endif; ?>

			</div>
			<div class="wdm_row">
				<div class='col_left'>Use the <a
						href="https://wordpress.org/plugins/groups/" target="_blank">Groups (>= 1.4.13)
						plugin</a>
					to filter search results.
					<br/>Think of re-indexing all your data if <a
						href="https://wordpress.org/plugins/groups/" target="_blank">Groups
						plugin</a> was installed after WPSOLR.
				</div>
				<div class='col_right'>
					<input type='checkbox'
					       name='<?php echo WPSOLR_Option::OPTION_PLUGIN_GROUPS; ?>[<?php echo WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE; ?>]'
					       value='<?php echo WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE; ?>'
						<?php WPSOLR_Service_Wordpress::checked( WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE, isset( $options[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE ] ) ? $options[ WPSOLR_Option::OPTION_SHARED_IS_EXTENSION_ACTIVE ] : '', true ); ?>>
				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Users without groups can see all results, <br/>
					whatever the results capabilities.
				</div>
				<div class='col_right'>
					<input type='checkbox'
					       name='<?php echo WPSOLR_Option::OPTION_PLUGIN_GROUPS; ?>[<?php echo WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_USERS_WITHOUT_GROUPS_SEE_ALL_RESULTS; ?>]'
					       value='<?php echo WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_USERS_WITHOUT_GROUPS_SEE_ALL_RESULTS; ?>'
						<?php WPSOLR_Service_Wordpress::checked( WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_USERS_WITHOUT_GROUPS_SEE_ALL_RESULTS, isset( $options[ WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_USERS_WITHOUT_GROUPS_SEE_ALL_RESULTS ] ) ? $options[ WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_USERS_WITHOUT_GROUPS_SEE_ALL_RESULTS ] : '?', true ); ?>>
				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Results without capabilities can be seen by all users,
					<br/> whatever the users groups.
				</div>
				<div class='col_right'>
					<input type='checkbox'
					       name='<?php echo WPSOLR_Option::OPTION_PLUGIN_GROUPS; ?>[<?php echo WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_RESULT_WITHOUT_CAPABILITIES_SEEN_BY_ALL_USERS; ?>]'
					       value='<?php echo WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_RESULT_WITHOUT_CAPABILITIES_SEEN_BY_ALL_USERS; ?>'
						<?php WPSOLR_Service_Wordpress::checked( WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_RESULT_WITHOUT_CAPABILITIES_SEEN_BY_ALL_USERS, isset( $options[ WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_RESULT_WITHOUT_CAPABILITIES_SEEN_BY_ALL_USERS ] ) ? $options[ WPSOLR_Option::OPTION_PLUGIN_GROUPS_IS_RESULT_WITHOUT_CAPABILITIES_SEEN_BY_ALL_USERS ] : '?', true ); ?>>
				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Phrase to display if you forbid users without groups
					to see any results
				</div>
				<div class='col_right'>
												<textarea
													id='<?php echo WPSOLR_Option::OPTION_PLUGIN_MESSAGE_USER_WITHOUT_GROUPS_SHOWN_NO_RESULTS; ?>'
													name='<?php echo WPSOLR_Option::OPTION_PLUGIN_GROUPS; ?>[<?php echo WPSOLR_Option::OPTION_PLUGIN_MESSAGE_USER_WITHOUT_GROUPS_SHOWN_NO_RESULTS; ?>]'
													rows="4" cols="100"
													placeholder="<?php echo WPSOLR_Plugin_Groups::DEFAULT_MESSAGE_NOT_AUTHORIZED; ?>"><?php echo WPSOLR_Global::getOption()->get_plugin_groups_message_user_without_groups_shown_no_results( WPSOLR_Plugin_Groups::DEFAULT_MESSAGE_NOT_AUTHORIZED ); ?></textarea>
					<span class='res_err'></span><br>
				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>Phrase to display when a result matches
					a user group. <br/>
					%1 will be replaced by the matched group(s).
				</div>
				<div class='col_right'>
					<input type='text'
					       id='<?php echo WPSOLR_Option::OPTION_PLUGIN_MESSAGE_RESULT_CAPABILITY_MATCHES_USER_GROUP; ?>'
					       name='<?php echo WPSOLR_Option::OPTION_PLUGIN_GROUPS; ?>[<?php echo WPSOLR_Option::OPTION_PLUGIN_MESSAGE_RESULT_CAPABILITY_MATCHES_USER_GROUP; ?>]'
					       placeholder="Private content : %1"
					       value="<?php echo WPSOLR_Global::getOption()->get_plugin_groups_message_result_capability_matches_user_group( 'Private content : %1' ); ?>"><span
						class='fac_err'></span> <br>
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