<?php
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\services\WPSOLR_Service_Wordpress;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;

$option_names_to_export = [
	WPSOLR_Option::OPTION_INDEXES            => [
		'description' => 'Indexes',
		'data'        => WPSOLR_Global::getOption()->get_option_indexes()
	],
	WPSOLR_Option::OPTION_SEARCH             => [
		'description' => 'Search',
		'data'        => WPSOLR_Global::getOption()->get_option_search()
	],
	WPSOLR_Option::OPTION_FACETS             => [
		'description' => 'Facet groups',
		'data'        => WPSOLR_Global::getOption()->get_option_facet()
	],
	WPSOLR_Option::OPTION_SORTS              => [
		'description' => 'Sort groups',
		'data'        => WPSOLR_Global::getOption()->get_option_sort()
	],
	WPSOLR_Option::OPTION_LOCALIZATION       => [
		'description' => 'Localizations',
		'data'        => WPSOLR_Global::getOption()->get_option_localization()
	],
	WPSOLR_Option::OPTION_PLUGIN_ACF         => [
		'description' => WPSOLR_Extensions::get_option_plugin_name( WPSOLR_Extensions::EXTENSION_ACF ),
		'data'        => WPSOLR_Global::getOption()->get_option_plugin_acf()
	],
	WPSOLR_Option::OPTION_PLUGIN_GROUPS      => [
		'description' => WPSOLR_Extensions::get_option_plugin_name( WPSOLR_Extensions::EXTENSION_GROUPS ),
		'data'        => WPSOLR_Global::getOption()->get_option_plugin_groups()
	],
	WPSOLR_Option::OPTION_PLUGIN_POLYLANG    => [
		'description' => WPSOLR_Extensions::get_option_plugin_name( WPSOLR_Extensions::EXTENSION_POLYLANG ),
		'data'        => WPSOLR_Global::getOption()->get_option_plugin_polylang()
	],
	WPSOLR_Option::OPTION_PLUGIN_S2MEMBER    => [
		'description' => WPSOLR_Extensions::get_option_plugin_name( WPSOLR_Extensions::EXTENSION_S2MEMBER ),
		'data'        => WPSOLR_Global::getOption()->get_option_plugin_s2member()
	],
	WPSOLR_Option::OPTION_PLUGIN_TYPES       => [
		'description' => WPSOLR_Extensions::get_option_plugin_name( WPSOLR_Extensions::EXTENSION_TYPES ),
		'data'        => WPSOLR_Global::getOption()->get_option_plugin_types()
	],
	WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE => [
		'description' => WPSOLR_Extensions::get_option_plugin_name( WPSOLR_Extensions::EXTENSION_WOOCOMMERCE ),
		'data'        => WPSOLR_Global::getOption()->get_option_plugin_woocommerce()
	],
	WPSOLR_Option::OPTION_PLUGIN_WPML        => [
		'description' => WPSOLR_Extensions::get_option_plugin_name( WPSOLR_Extensions::EXTENSION_WPML ),
		'data'        => WPSOLR_Global::getOption()->get_option_plugin_wpml()
	]
];

// Import
$wpsolr_data_to_import_string = '';
if ( ! empty( $_POST['wpsolr_action'] ) && ( 'wpsolr_action_import_settings' === $_POST['wpsolr_action'] ) ) {

	// Remove escaped quotes added by the POST
	$wpsolr_data_to_import_string = ! empty( $_POST['wpsolr_data_to_import'] ) ? stripslashes( $_POST['wpsolr_data_to_import'] ) : '';
	if ( ! empty( $wpsolr_data_to_import_string ) ) {

		$wpsolr_data_to_import = json_decode( $wpsolr_data_to_import_string, true );

		foreach ( $option_names_to_export as $option_name => $option_description ) {

			if ( ! empty( $wpsolr_data_to_import[ $option_name ] ) ) {

				// Save the option
				update_option( $option_name, $wpsolr_data_to_import[ $option_name ] );
			}

		}
	}
}

// Export
$exports = [ ];
foreach ( $option_names_to_export as $option_name => $option ) {

	if ( empty( $options ) || isset( $options[ $option_name ] ) ) {
		// Export options selected, or everything if no option is selected

		$exports[ $option_name ] = $option['data'];
	}
}


?>

<div id="export-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST" id='settings_form'>
		<input type="hidden" name="wpsolr_action" value="wpsolr_action_export_settings"/>
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>

		<div class='indexing_option wrapper'>
			<h4 class='head_div'>Export configuration</h4>

			<div class="wdm_note">

				Choose the WPSOLR settings that you want to export to a file.
			</div>

			<div class="wdm_row">
				<div class='col_left'>
					Data to export.<br/>
					To export everything, don't select any data.
				</div>
				<div class='col_right'>

					<?php foreach ( $option_names_to_export as $option_name => $option ) { ?>
						<input type='checkbox'
						       name='<?php echo $options_name ?>[<?php echo $option_name; ?>]'
						       value='1' <?php checked( '1', isset( $options[ $option_name ] ) ? $options[ $option_name ] : '' ); ?>>
						<?php echo $option['description']; ?>
					<?php } ?>

				</div>
				<div class="clear"></div>
			</div>
			<div class="wdm_row">
				<div class='col_left'>
					Data exported.<br/>
					Copy that data to your target WPSOLR import text area.
				</div>
				<div class='col_right'>
					<textarea name="wpsolr_data_exported" rows="10"
					          style="width: 100%"><?php echo ! empty( $exports ) ? json_encode( $exports, JSON_PRETTY_PRINT ) : ''; ?></textarea>
				</div>
				<div class="clear"></div>
			</div>
			<div class='wdm_row'>
				<div class="submit">
					<input name="save_selected_importexport_options_form"
					       type="submit"
					       class="button-primary wdm-save" value="Generate data to export"/>
				</div>
			</div>
	</form>
</div>

<form method="POST" id='import_form'>
	<input type="hidden" name="wpsolr_action" value="wpsolr_action_import_settings"/>
	<?php
	WPSOLR_Service_Wordpress::settings_fields( $options_name );
	?>

	<div class='indexing_option wrapper'>
		<h4 class='head_div'>Import configuration</h4>

		<div class="wdm_note">

			Paste here, from the source WPSOLR, the data to import.
		</div>

		<div class="wdm_row">
			<div class='col_left'>
				Data to import
			</div>
			<div class='col_right'>
					<textarea name="wpsolr_data_to_import" rows="20"
					          style="width: 100%"><?php echo ! empty( $wpsolr_data_to_import_string ) ? $wpsolr_data_to_import_string : ''; ?></textarea>
			</div>
			<div class="clear"></div>
		</div>
		<div class='wdm_row'>
			<div class="submit">
				<input name="import"
				       type="submit"
				       class="button-primary wdm-save" value="Import generated data"/>
			</div>
		</div>

	</div>
</form>

</div>
