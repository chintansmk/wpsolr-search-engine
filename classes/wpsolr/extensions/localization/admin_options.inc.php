<?php

/**
 * Included file to display admin options
 */

use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\extensions\WPSOLR_Extensions;

$options_name = WPSOLR_Extensions::get_option_name( WPSOLR_Extensions::OPTION_LOCALIZATION );

// Retrieve all options in database
$options = WPSOLR_Localization::get_options( true );

?>
<div id="localization-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST" id='localization_settings_form'>

		<?php
		settings_fields( $options_name );
		$presentation = WPSOLR_Localization::get_presentation_options();
		?>

		<div class='wrapper'>
			<h4 class='head_div'>Localization Options</h4>

			<div class="wdm_note">

				In this section, you will configure (localize) all the texts displayed on the front-end forms.<br/>
			</div>

			<div class='wdm_row'>
				<div class='col_left'>
					Choose how the front-end texts are localized

				</div>
				<div class='col_right'>

					<?php
					$select_options = array(
						'localization_by_admin_options' => 'Use this page to localize all front-end texts',
						'localization_by_other_means'   => 'Use your theme/plugin .mo files or WPML string module to localize all front-end texts',
					);
					?>

					<select name='wdm_solr_localization_data[localization_method]' id='wpsolr_localization_method'>
						<?php foreach ( $select_options as $option_code => $option_label ) {

							echo sprintf( "<option value='%s' %s>%s</option>",
								$option_code,
								isset( $options['localization_method'] ) && $options['localization_method'] === $option_code ? "selected" : "",
								$option_label );

						}
						?>
					</select>

					<div class="wdm_note">
						<h4>How to use your translation file(s) ?</h4>
						You can find a wpsolr.pot file in WPSOLR's /languages folder.
						<br/>
						Use it to create your .po and .mo files (wpsolr-fr_FR.mo and wpsolr-fr_FR.po).
						<br/>
						Copy your .mo files in the Wordpress languages plugin directory (WP_LANG_DIR/plugins).
						<br/>
						Example: /htdocs/wp-includes/languages/plugins/wpsolr-fr_FR.mo or
						/htdocs/wp-content/languages/plugins/wpsolr-fr_FR.mo
						<br/>
					</div>

				</div>
			</div>
			<div style="clear:both"></div>

			<?php
			foreach ( $presentation as $section_name => $section ) {
				?>

				<div class='wdm_row'>

					<div class='wdm_row'><h4
							class='head_div'><?php echo $section_name; ?></h4></div>

					<?php
					foreach ( WPSOLR_Localization::get_section_terms( $section ) as $term_code => $term_content ) {
						?>

						<div class='wdm_row'>
							<div class='col_left'>
								<?php echo $term_content[0]; ?>
							</div>
							<div class='col_right'>

								<?php
								$term_localized = WPSOLR_Localization::get_term( $options, $term_code );
								echo "<textarea id='message_user_without_capabilities_shown_no_results' name='wdm_solr_localization_data[terms][$term_code]'
						          rows='4' cols='100'>$term_localized</textarea >"
								?>

							</div>
						</div>

						<?php
					} ?>
				</div>
				<div style="clear:both"></div>
				<?php
			}
			?>


			<div class='wdm_row'>
				<div class="submit">
					<input name="save_selected_options_res_form"
					       id="save_selected_extension_groups_form" type="submit"
					       class="button - primary wdm - save" value="Save Options"/>

				</div>
			</div>

		</div>

	</form>
</div>