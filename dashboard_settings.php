<?php
use wpsolr\extensions\indexes\WPSOLR_Options_Indexes;
use wpsolr\extensions\managedservers\WPSOLR_ManagedServers;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\solr\WPSOLR_IndexSolrClient;
use wpsolr\solr\WPSOLR_SearchSolrClient;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;

switch ( isset( $_POST['wpsolr_action'] ) ? $_POST['wpsolr_action'] : '' ) {
	case 'wpsolr_admin_action_form_temporary_index':
		unset( $response_object );

		if ( isset( $_POST['submit_button_form_temporary_index'] ) ) {
			wpsolr_admin_action_form_temporary_index( $response_object );
		}

		if ( isset( $_POST['submit_button_form_temporary_index_select_managed_solr_service_id'] ) ) {

			$form_data = WPSOLR_Extensions::extract_form_data( true, array(
					'managed_solr_service_id' => array( 'default_value' => '', 'can_be_empty' => false )
				)
			);

			$managed_solr_server = new WPSOLR_ManagedServers( $form_data['managed_solr_service_id']['value'] );
			$response_object     = $managed_solr_server->call_rest_create_google_recaptcha_token();

			if ( isset( $response_object ) && WPSOLR_ManagedServers::is_response_ok( $response_object ) ) {
				$google_recaptcha_site_key = WPSOLR_ManagedServers::get_response_result( $response_object, 'siteKey' );
				$google_recaptcha_token    = WPSOLR_ManagedServers::get_response_result( $response_object, 'token' );
			}

		}

		break;

}

function wpsolr_admin_action_form_temporary_index( &$response_object ) {


	// recaptcha response
	$g_recaptcha_response = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';

	// A recaptcha response must be set
	if ( empty( $g_recaptcha_response ) ) {

		return;
	}

	$form_data = WPSOLR_Extensions::extract_form_data( true, array(
			'managed_solr_service_id' => array( 'default_value' => '', 'can_be_empty' => false )
		)
	);

	$managed_solr_server = new WPSOLR_ManagedServers( $form_data['managed_solr_service_id']['value'] );
	$response_object     = $managed_solr_server->call_rest_create_solr_index( $g_recaptcha_response );

	if ( isset( $response_object ) && WPSOLR_ManagedServers::is_response_ok( $response_object ) ) {

		WPSOLR_Global::getExtensionIndexes()->create_index(
			$managed_solr_server->get_id(),
			WPSOLR_Options_Indexes::STORED_INDEX_TYPE_MANAGED_TEMPORARY,
			WPSOLR_ManagedServers::get_response_result( $response_object, 'urlCore' ),
			'Test index from ' . $managed_solr_server->get_label(),
			WPSOLR_ManagedServers::get_response_result( $response_object, 'urlScheme' ),
			WPSOLR_ManagedServers::get_response_result( $response_object, 'urlDomain' ),
			WPSOLR_ManagedServers::get_response_result( $response_object, 'urlPort' ),
			'/' . WPSOLR_ManagedServers::get_response_result( $response_object, 'urlPath' ) . '/' . WPSOLR_ManagedServers::get_response_result( $response_object, 'urlCore' ),
			WPSOLR_ManagedServers::get_response_result( $response_object, 'key' ),
			WPSOLR_ManagedServers::get_response_result( $response_object, 'secret' )
		);

		// Redirect automatically to Solr options if it is the first solr index created
		if ( count( WPSOLR_Global::getExtensionIndexes()->get_indexes() ) === 1 ) {
			$redirect_location = '?page=solr_settings&tab=solr_option';
			header( "Location: $redirect_location", true, 302 ); // wp_redirect() is not found
			exit;
		}
	}

}

function wpsolr_admin_init() {

	WPSOLR_Extensions::register_settings();

	register_setting( 'solr_form_options', 'wdm_solr_form_data' );
	register_setting( 'solr_res_options', 'wdm_solr_res_data' );
	register_setting( 'solr_facet_options', 'wdm_solr_facet_data' );
	register_setting( 'solr_sort_options', WPSOLR_Option::OPTION_SORTS );
	register_setting( 'solr_operations_options', 'wdm_solr_operations_data' );
	register_setting( 'solr_importexports_options', WPSOLR_Option::OPTION_IMPORTEXPORT );
}

function fun_add_solr_settings() {
	$img_url = plugins_url( 'images/WPSOLRDashicon.png', __FILE__ );
	add_menu_page( 'WPSOLR', 'WPSOLR', 'manage_options', 'solr_settings', 'fun_set_solr_options', $img_url );
	wp_enqueue_style( 'dashboard_style', plugins_url( 'css/dashboard_css.css', __FILE__ ) );


	// Jquery dialog js and css
	wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-accordion' );
	wp_enqueue_script( 'jquery-ui-tabs' );

	wp_enqueue_script( 'dashboard_js1', plugins_url( 'js/dashboard.js', __FILE__ ), array(
		'jquery',
		'jquery-ui-sortable'
	) );

	$plugin_vals = array( 'plugin_url' => plugins_url( 'images/', __FILE__ ) );
	wp_localize_script( 'dashboard_js1', 'plugin_data', $plugin_vals );

	// Google api recaptcha - Used for temporary indexes creation
	wp_enqueue_script( 'google-api-recaptcha', '//www.google.com/recaptcha/api.js', array() );

}

function fun_set_solr_options() {

	// Button Index
	if ( isset( $_POST['solr_index_data'] ) ) {

		$solr = WPSOLR_IndexSolrClient::create();

		try {
			$res = $solr->get_solr_status();

			$val = $solr->index_data();

			if ( count( $val ) == 1 || $val == 1 ) {
				echo "<script type='text/javascript'>
                jQuery(document).ready(function(){
                jQuery('.status_index_message').removeClass('loading');
                jQuery('.status_index_message').addClass('success');
                });
            </script>";
			} else {
				echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
                jQuery('.status_index_message').removeClass('loading');
                jQuery('.status_index_message').addClass('warning');
                });
            </script>";
			}

		} catch ( Exception $e ) {

			$errorMessage = $e->getMessage();

			echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
               jQuery('.status_index_message').removeClass('loading');
               jQuery('.status_index_message').addClass('warning');
               jQuery('.wdm_note').html('<b>Error: <p>{$errorMessage}</p></b>');
            });
            </script>";

		}

	}

	// Button delete
	if ( isset( $_POST['solr_delete_index'] ) ) {
		$solr = WPSOLR_IndexSolrClient::create();

		try {
			$res = $solr->get_solr_status();

			$val = $solr->delete_documents();

			if ( $val == 0 ) {
				echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
               jQuery('.status_del_message').removeClass('loading');
               jQuery('.status_del_message').addClass('success');
            });
            </script>";
			} else {
				echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
               jQuery('.status_del_message').removeClass('loading');
                              jQuery('.status_del_message').addClass('warning');
            });
            </script>";
			}

		} catch ( Exception $e ) {

			$errorMessage = $e->getMessage();

			echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
               jQuery('.status_del_message').removeClass('loading');
               jQuery('.status_del_message').addClass('warning');
               jQuery('.wdm_note').html('<b>Error: <p>{$errorMessage}</p></b>');
            })
            </script>";
		}
	}


	?>
	<div class="wdm-wrap" xmlns="http://www.w3.org/1999/html">

	<?php
	if ( isset ( $_GET['tab'] ) ) {
		wpsolr_admin_tabs( $_GET['tab'] );
	} else {
		wpsolr_admin_tabs( 'solr_indexes' );
	}

	if ( isset ( $_GET['tab'] ) ) {
		$tab = $_GET['tab'];
	} else {
		$tab = 'solr_indexes';
	}

switch ( $tab ) {
	case 'solr_importexports' :
		WPSOLR_Global::getExtensionImportExports()->output_form();
		break;

	case 'solr_indexes' :
		WPSOLR_Extensions::require_once_wpsolr_extension_admin_options( WPSOLR_Extensions::EXTENSION_INDEXES );
		break;

	case 'solr_option':
		?>
		<div id="solr-option-tab">

			<?php

			$subtabs = [
				WPSOLR_Option::OPTION_SEARCH                  => '2.1 Search',
				WPSOLR_Option::OPTION_FIELDS                  => '2.2 Fields',
				WPSOLR_Option::OPTION_FACETS                  => '2.3 Facets',
				WPSOLR_Option::OPTION_SORTS                   => '2.4 Sorting',
				WPSOLR_Option::OPTION_RESULTS_ROWS            => '2.x Results rows',
				WPSOLR_Option::OPTION_RESULTS_HEADERS         => '2.x Results headers',
				WPSOLR_Option::OPTION_RESULTS_PAGE_NAVIGATION => '2.x Results page navigations',
				WPSOLR_Option::OPTION_SEARCH_FORM             => '2.x Search forms',
				'localization_options'                        => '2.5 Localization',
				'layout_options'                              => '2.6 Layouts',
				'component_options'                           => '2.7 Components'
			];

			$subtab = wpsolr_admin_sub_tabs( $subtabs );

			switch ( $subtab ) {
				case WPSOLR_Option::OPTION_SEARCH:

					$solr_indexes = WPSOLR_Global::getExtensionIndexes()->get_indexes();

					?>
					<div id="solr-results-options" class="wdm-vertical-tabs-content">
						<form action="options.php" method="POST" id='res_settings_form'>
							<?php
							settings_fields( 'solr_res_options' );
							$solr_res_options = get_option( 'wdm_solr_res_data', array(
								'default_search'                     => 0,
								'res_info'                           => '0',
								'spellchecker'                       => '0',
								'is_after_autocomplete_block_submit' => '1',
							) );

							?>

							<div class='wrapper'>
								<h4 class='head_div'>Result Options</h4>

								<div class="wdm_note">

									In this section, you will choose how to display the results returned by a
									query to your Solr instance.

								</div>
								<div class="wdm_row">
									<div class='col_left'>Search with this Solr index<br/>

									</div>
									<div class='col_right'>
										<select name='wdm_solr_res_data[default_solr_index_for_search]'>
											<?php
											// Empty option
											echo sprintf( "<option value='%s' %s>%s</option>",
												'',
												'',
												'Your search is not managed by Solr. Please select a Solr index.'
											);

											foreach (
												$solr_indexes as $solr_index_indice => $solr_index
											) {

												echo sprintf( "
											<option value='%s' %s>%s</option>
											",
													$solr_index_indice,
													selected( $solr_index_indice, isset( $solr_res_options['default_solr_index_for_search'] ) ?
														$solr_res_options['default_solr_index_for_search'] : '' ),
													isset( $solr_index['index_name'] ) ? $solr_index['index_name'] : 'Unnamed
											Solr index' );

											}
											?>
										</select>

									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>
										Replace WordPress default search by WPSOLR's.<br/><br/>
									</div>
									<div class='col_right'>
										<input type='checkbox' name='wdm_solr_res_data[default_search]'
										       value='1'
											<?php checked( '1', isset( $solr_res_options['default_search'] ) ? $solr_res_options['default_search'] : '0' ); ?>>
										If your website is already in production, check this option after tabs
										1-4 are completed. <br/><br/>
										Warning: permalinks must be activated.
									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>
										Search theme
									</div>
									<div class='col_right'>
										<select name="wdm_solr_res_data[search_method]">
											<?php
											$options = array(
												array(
													'code'  => 'use_current_theme_search_template',
													'label' => '1. Use my current theme search templates (no keyword autocompletion, no \'Did you mean\', no facets, no sort)'
												),
												array(
													'code'  => 'ajax',
													'label' => '2. Use WPSOLR custom search templates with Ajax (full WPSOLR features)'
												),
												array(
													'code'  => 'ajax_with_parameters',
													'label' => '3. Use WPSOLR custom search templates with Ajax and show parameters in url (full WPSOLR features)'
												)
											);
											foreach ( $options as $option ) {
												$selected = $solr_res_options['search_method'] == $option['code'] ? 'selected' : '';
												?>
												<option
													value="<?php echo $option['code'] ?>" <?php echo $selected ?> ><?php echo $option['label'] ?></option>
											<?php } ?>

										</select>

										<div class="wdm_note">
											To display your search results, you can choose among:<br/>
											<ul>
												<li>
													1. <b>Full integration to your theme, but less Solr features.</b>
													Use your own theme's search templates customized with our Widgets
													"WPSOLR Facets" and "WPSOLR Sort", written in <a
														href="http://twig.sensiolabs.org/" target="_blank">Twig</a>. You
													can control 100% of the appearance of our Widgets, with the theme
													Customizer.
												</li>
												<li>
													2. 3. <b>Full Solr features, but less integration to your theme.</b>
													Use WPSOLR's custom search templates with your own css.
												</li>
											</ul>
										</div>

									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>Do not load WPSOLR front-end css.<br/>You can then use your
										own theme css.
									</div>
									<div class='col_right'>
										<?php $is_prevent_loading_front_end_css = isset( $solr_res_options['is_prevent_loading_front_end_css'] ) ? '1' : '0'; ?>
										<input type='checkbox'
										       name='wdm_solr_res_data[is_prevent_loading_front_end_css]'
										       value='1'
											<?php checked( '1', $is_prevent_loading_front_end_css ); ?>>
									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>Activate the "Infinite scroll" pagination.
									</div>
									<div class='col_right'>
										<input type='checkbox'
										       name='wdm_solr_res_data[<?php echo 'infinitescroll' ?>]'
										       value='infinitescroll'
											<?php checked( 'infinitescroll', isset( $solr_res_options['infinitescroll'] ) ? $solr_res_options['infinitescroll'] : '?' ); ?>>
										This feature loads the next page of results automatically when visitors approach
										the bottom of search page.
									</div>
									<div class="clear"></div>
								</div>

								<div class="wdm_row">
									<div class='col_left'>Query default operator<br/>
									</div>
									<div class='col_right'>
										<?php $query_default_operator = isset( $solr_res_options[ WPSOLR_Option::OPTION_SEARCH_QUERY_DEFAULT_OPERATOR ] )
											? $solr_res_options[ WPSOLR_Option::OPTION_SEARCH_QUERY_DEFAULT_OPERATOR ] : WPSOLR_SearchSolrClient::QUERY_OPERATOR_AND; ?>

										<select
											name='wdm_solr_res_data[<?php echo WPSOLR_Option::OPTION_SEARCH_QUERY_DEFAULT_OPERATOR; ?>]'>
											<option
												value='<?php echo WPSOLR_SearchSolrClient::QUERY_OPERATOR_AND; ?>' <?php selected( WPSOLR_SearchSolrClient::QUERY_OPERATOR_AND, $query_default_operator, true ); ?>
											>
												<?php echo WPSOLR_SearchSolrClient::QUERY_OPERATOR_AND; ?>
											</option>
											<option
												value='<?php echo WPSOLR_SearchSolrClient::QUERY_OPERATOR_OR; ?>' <?php selected( WPSOLR_SearchSolrClient::QUERY_OPERATOR_OR, $query_default_operator, true ); ?>
											>
												<?php echo WPSOLR_SearchSolrClient::QUERY_OPERATOR_OR; ?>
											</option>
										</select>

									</div>
									<div class="clear"></div>
								</div>

								<div class="wdm_row">
									<div class='col_left'>Display partial keyword matches in results
									</div>
									<div class='col_right'>
										<?php $is_query_partial_match = isset( $solr_res_options[ WPSOLR_Option::OPTION_SEARCH_IS_QUERY_PARTIAL_MATCH_BEGIN_WITH ] ) ? '1' : '0'; ?>
										<input type='checkbox'
										       name='wdm_solr_res_data[<?php echo WPSOLR_Option::OPTION_SEARCH_IS_QUERY_PARTIAL_MATCH_BEGIN_WITH; ?>]'
										       value='1'
											<?php checked( '1', $is_query_partial_match ); ?>>

										Warning: this will hurt both search performance and search accuracy !
										<p>This adds '*' to all keywords.
											For instance, 'search apache' will return results
											containing 'searching apachesolr'</p>
									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>Do not show a list of keywords suggestions in the
										search box while typing
									</div>
									<div class='col_right'>
										<?php $is_do_not_show_suggestions = isset( $solr_res_options[ WPSOLR_Option::OPTION_SEARCH_IS_DO_NOT_SHOW_SUGGESTIONS ] ) ? '1' : '0'; ?>
										<input type='checkbox'
										       name='wdm_solr_res_data[<?php echo WPSOLR_Option::OPTION_SEARCH_IS_DO_NOT_SHOW_SUGGESTIONS; ?>]'
										       value='1'
											<?php checked( '1', $is_do_not_show_suggestions ); ?>>
									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>Do not automatically trigger the search, when a user
										clicks on the
										suggestions list
									</div>
									<div class='col_right'>
										<?php $is_after_autocomplete_block_submit = isset( $solr_res_options['is_after_autocomplete_block_submit'] ) ? '1' : '0'; ?>
										<input type='checkbox'
										       name='wdm_solr_res_data[is_after_autocomplete_block_submit]'
										       value='1'
											<?php checked( '1', $is_after_autocomplete_block_submit ); ?>>
									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>Display suggestions (Did you mean?)</div>
									<div class='col_right'>
										<input type='checkbox'
										       name='wdm_solr_res_data[<?php echo 'spellchecker' ?>]'
										       value='spellchecker'
											<?php checked( 'spellchecker', isset( $solr_res_options['spellchecker'] ) ? $solr_res_options['spellchecker'] : '?' ); ?>>
									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>Display number of results and current page</div>
									<div class='col_right'>
										<input type='checkbox' name='wdm_solr_res_data[res_info]'
										       value='res_info'
											<?php checked( 'res_info', isset( $solr_res_options['res_info'] ) ? $solr_res_options['res_info'] : '?' ); ?>>
									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>No. of results per page</div>
									<div class='col_right'>
										<input type='text' id='number_of_res' name='wdm_solr_res_data[no_res]'
										       placeholder="Enter a Number"
										       value="<?php echo empty( $solr_res_options['no_res'] ) ? '20' : $solr_res_options['no_res']; ?>">
										<span class='res_err'></span><br>
									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>No. of values to be displayed by facets</div>
									<div class='col_right'>
										<input type='text' id='number_of_fac' name='wdm_solr_res_data[no_fac]'
										       placeholder="Enter a Number"
										       value="<?php echo empty( $solr_res_options['no_fac'] ) ? '20' : $solr_res_options['no_fac']; ?>"><span
											class='fac_err'></span> <br>
									</div>
									<div class="clear"></div>
								</div>
								<div class="wdm_row">
									<div class='col_left'>Maximum size of each snippet text in results</div>
									<div class='col_right'>
										<input type='text' id='highlighting_fragsize'
										       name='wdm_solr_res_data[highlighting_fragsize]'
										       placeholder="Enter a Number"
										       value="<?php echo empty( $solr_res_options['highlighting_fragsize'] ) ? '100' : $solr_res_options['highlighting_fragsize']; ?>"><span
											class='highlighting_fragsize_err'></span> <br>
									</div>
									<div class="clear"></div>
								</div>
								<div class='wdm_row'>
									<div class="submit">
										<input name="save_selected_options_res_form"
										       id="save_selected_res_options_form" type="submit"
										       class="button-primary wdm-save" value="Save Options"/>


									</div>
								</div>
							</div>

						</form>
					</div>
					<?php
					break;

				case WPSOLR_Option::OPTION_FIELDS:
					WPSOLR_Global::getExtensionFields()->output_form();
					break;

				case WPSOLR_Option::OPTION_FACETS:
					WPSOLR_Global::getExtensionFacets()->output_form();
					break;

				case WPSOLR_Option::OPTION_SORTS:
					WPSOLR_Global::getExtensionSorts()->output_form();
					break;

				case 'localization_options':
					WPSOLR_Extensions::require_once_wpsolr_extension_admin_options( WPSOLR_Extensions::OPTION_LOCALIZATION );
					break;

				case 'layout_options':
					WPSOLR_Global::getExtensionLayouts()->output_form();
					break;

				case 'component_options':
					WPSOLR_Global::getExtensionComponents()->output_form();
					break;

				case WPSOLR_Option::OPTION_RESULTS_ROWS:
					WPSOLR_Global::getExtensionResultsRows()->output_form();
					break;

				case WPSOLR_Option::OPTION_RESULTS_HEADERS:
					WPSOLR_Global::getExtensionResultsHeaders()->output_form();
					break;

				case WPSOLR_Option::OPTION_RESULTS_PAGE_NAVIGATION:
					WPSOLR_Global::getExtensionResultsPageNavigations()->output_form();
					break;

				case WPSOLR_Option::OPTION_SEARCH_FORM:
					WPSOLR_Global::getExtensionSearchForm()->output_form();
					break;
			}

			?>

		</div>
		<?php
		break;

case 'solr_plugins':
	?>
	<div id="solr-option-tab">

	<?php
	$subtabs = [ ];
	foreach ( WPSOLR_Extensions::get_extensions() as $extension ) {

		if ( ! empty( $extension['name'] ) ) {
			$subtabs[ $extension['id'] ] = [
				'name'       => $extension['name'],
				'is_checked' => $extension['is_active']
			];
		}
	}

	$subtab = wpsolr_admin_sub_tabs( $subtabs );

	WPSOLR_Global::getExtension( $subtab )->output_form();
	break;

	case 'solr_operations':

		$option_indexes_object = WPSOLR_Global::getExtensionIndexes();

		// Create the tabs from the Solr indexes already configured
		$subtabs = array();
		foreach ( $option_indexes_object->get_indexes() as $index_indice => $index ) {
			$subtabs[ $index_indice ] = isset( $index['index_name'] ) ? $index['index_name'] : 'Index with no name';
		}

		if ( empty( $subtabs ) ) {
			echo "Please create a Solr index configuration first.";

			return;
		}

		// Create subtabs on the left side
		$current_index_indice = wpsolr_admin_sub_tabs( $subtabs );
		if ( ! $option_indexes_object->has_index( $current_index_indice ) ) {
			$current_index_indice = key( $subtabs );
		}
		$current_index_name = $subtabs[ $current_index_indice ];


		try {
			$solr                             = WPSOLR_IndexSolrClient::create( $current_index_indice );
			$count_nb_documents_to_be_indexed = $solr->count_nb_documents_to_be_indexed();
		} catch ( Exception $e ) {
			echo '<b>An error occured while trying to connect to the Solr server:</b> <br>' . htmlentities( $e->getMessage() );

			return;
		}

		?>

		<div id="solr-operations-tab"
		     class="wdm-vertical-tabs-content">
			<form action="options.php" method='post' id='solr_actions'>
				<input type='hidden' id='solr_index_indice' name='wdm_solr_operations_data[solr_index_indice]'
				       value="<?php echo $current_index_indice; ?>">
				<?php

				settings_fields( 'solr_operations_options' );

				$solr_operations_options = get_option( 'wdm_solr_operations_data' );

				$batch_size = empty( $solr_operations_options['batch_size'][ $current_index_indice ] ) ? '100' : $solr_operations_options['batch_size'][ $current_index_indice ];

				?>
				<input type='hidden' id='adm_path' value='<?php echo admin_url(); ?>'> <!-- for ajax -->
				<div class='wrapper'>
					<h4 class='head_div'>Content of the Solr index "<?php echo $current_index_name ?>"</h4>

					<div class="wdm_note">
						<div>
							<?php
							try {
								$nb_documents_in_index = $solr->get_count_documents();
								echo sprintf( "<b>A total of %s documents are currently in your index \"%s\"</b>", $nb_documents_in_index, $current_index_name );
							} catch ( Exception $e ) {
								echo '<b>Please check your Solr Hosting, an exception occured while calling your Solr server:</b> <br><br>' . htmlentities( $e->getMessage() );
							}
							?>
						</div>
						<?php if ( $count_nb_documents_to_be_indexed >= 0 ): ?>
							<div><b>
									<?php
									echo $count_nb_documents_to_be_indexed;

									// Reset value so it's not displayed next time this page is displayed.
									//$solr->update_count_documents_indexed_last_operation();
									?>
								</b> document(s) remain to be indexed
							</div>
						<?php endif ?>
					</div>
					<div class="wdm_row">
						<p>The indexing is <b>incremental</b>: only documents updated after the last operation
							are sent to the index.</p>

						<p>So, the first operation will index all documents, by batches of
							<b><?php echo $batch_size; ?></b> documents.</p>

						<p>If a <b>timeout</b> occurs, you just have to click on the button again: the process
							will restart from where it stopped.</p>

						<p>If you need to reindex all again, delete the index first.</p>
					</div>
					<div class="wdm_row">
						<div class='col_left'>Number of documents sent in Solr as a single commit.<br>
							You can change this number to control indexing's performance.
						</div>
						<div class='col_right'>
							<input type='text' id='batch_size'
							       name='wdm_solr_operations_data[batch_size][<?php echo $current_index_indice ?>]'
							       placeholder="Enter a Number"
							       value="<?php echo $batch_size; ?>">
							<span class='res_err'></span><br>
						</div>
						<div class="clear"></div>
						<div class='col_left'>Display debug infos during indexing</div>
						<div class='col_right'>

							<input type='checkbox'
							       id='is_debug_indexing'
							       name='wdm_solr_operations_data[is_debug_indexing][<?php echo $current_index_indice ?>]'
							       value='is_debug_indexing'
								<?php checked( 'is_debug_indexing', isset( $solr_operations_options['is_debug_indexing'][ $current_index_indice ] ) ? $solr_operations_options['is_debug_indexing'][ $current_index_indice ] : '' ); ?>>
							<span class='res_err'></span><br>
						</div>
						<div class="clear"></div>
						<div class='col_left'>
							Continue indexing on field type conversion errors.<br/>
							If a field is declared a numeric, but contains a non numeric, skip indexing this field
							without stopping indexing other fields.
						</div>
						<div class='col_right'>
							<input type='checkbox'
							       id='is_continue_at_conversion_error'
							       name='wdm_solr_operations_data[is_continue_at_conversion_error][<?php echo $current_index_indice ?>]'
							       value='is_debug_indexing'
								<?php checked( 'is_debug_indexing', isset( $solr_operations_options['is_continue_at_conversion_error'][ $current_index_indice ] ) ? $solr_operations_options['is_continue_at_conversion_error'][ $current_index_indice ] : '' ); ?>>
							<span class='res_err'></span><br>
						</div>
						<div class="clear"></div>
						<div class='col_left'>
							Re-index all the data in place.<br/>
							If you check this option, it will restart the indexing from start, without deleting the
							data already in the Solr index.
						</div>
						<div class='col_right'>

							<input type='checkbox'
							       id='is_reindexing_all_posts'
							       name='is_reindexing_all_posts'
							       value='is_reindexing_all_posts'
								<?php checked( true, false ); ?>>
							<span class='res_err'></span><br>
						</div>
						<div class="clear"></div>
					</div>
					<div class="wdm_row">
						<div class="submit">
							<input name="solr_start_index_data" type="submit" class="button-primary wdm-save"
							       id='solr_start_index_data'
							       value="Synchronize Wordpress with '<?php echo $current_index_name ?>' "/>
							<input name="solr_stop_index_data" type="submit" class="button-primary wdm-save"
							       id='solr_stop_index_data' value="Stop current indexing"
							       style="visibility: hidden;"/>
							<span class='status_index_icon'></span>

							<input name="solr_delete_index" type="submit" class="button-primary wdm-save"
							       id="solr_delete_index"
							       value="Empty '<?php echo $current_index_name ?>' "/>


							<span class='status_index_message'></span>
							<span class='status_debug_message'></span>
							<span class='status_del_message'></span>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
		break;


}

	?>


	</div>
	<?php

}

function wpsolr_admin_tabs( $current = 'solr_indexes' ) {

	// Get default search solr index indice
	$option_indexes            = WPSOLR_Global::getExtensionIndexes();
	$default_search_solr_index = $option_indexes->get_default_search_solr_index();

	$nb_indexes        = count( $option_indexes->get_indexes() );
	$are_there_indexes = ( $nb_indexes > 0 );

	$tabs                 = array();
	$tabs['solr_indexes'] = $are_there_indexes ? '1. Define your Solr Indexes' : '1. Define your Solr Index';
	if ( $are_there_indexes ) {
		$tabs['solr_option']        = sprintf( "2. Define your search with '%s'",
			! isset( $default_search_solr_index )
				? $are_there_indexes ? "<span class='text_error'>No index selected</span>" : ''
				: $option_indexes->get_index_name( $default_search_solr_index ) );
		$tabs['solr_plugins']       = '3. Define which plugins to work with';
		$tabs['solr_operations']    = '4. Send your data to Solr';
		$tabs['solr_importexports'] = '5. Import/Export your setup';
	}

	echo '<div id="icon-themes" class="icon32"><br></div>';
	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $tabs as $tab => $name ) {
		$class = ( $tab == $current ) ? 'wpsolr-nav-tab-active' : 'wpsolr-nav-tab-inactive';
		echo "<a class='nav-tab $class' href='admin.php?page=solr_settings&tab=$tab'>$name</a>";

	}
	echo '</h2>';
}


function wpsolr_admin_sub_tabs( $subtabs, $before = null ) {

	// Tab selected by the user
	$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'solr_indexes';

	if ( isset ( $_GET['subtab'] ) ) {

		$current_subtab_id = $_GET['subtab'];

	} else {
		// No user selection: use the first subtab in the list
		$current_subtab_id = key( $subtabs );
	}

	echo '<div id="icon-themes" class="icon32"><br></div>';
	echo '<h2 class="nav-tab-wrapper wdm-vertical-tabs">';

	if ( isset( $before ) ) {
		echo "$before<div style='clear: both;margin-bottom: 10px;'></div>";
	}

	foreach ( $subtabs as $subtab_id => $subtab ) {

		$class_active  = ( $subtab_id == $current_subtab_id ) ? 'wpsolr-nav-tab-active' : 'wpsolr-nav-tab-inactive';
		$class_checked = is_array( $subtab ) ? ( $subtab['is_checked'] ? 'wpsolr-nav-tab-success' : '' ) : '';
		$name          = is_array( $subtab ) ? $subtab['name'] : $subtab;

		echo "<a class='nav-tab $class_active $class_checked' href='admin.php?page=solr_settings&tab=$tab&subtab=$subtab_id'>$name</a>";

	}

	echo '</h2>';

	return $current_subtab_id;
}
