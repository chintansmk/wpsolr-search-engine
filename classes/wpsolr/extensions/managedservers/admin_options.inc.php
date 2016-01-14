<?php

/**
 * Included file to display admin options
 */

use wpsolr\extensions\managedservers\WPSOLR_ManagedServers;
use wpsolr\extensions\WPSOLR_Extensions;

// Options name
$option_name = WPSOLR_ManagedServers::get_option_name( WPSOLR_Extensions::OPTION_MANAGED_SOLR_SERVERS );

// Options data
$option_data = WPSOLR_ManagedServers::get_option_data( WPSOLR_Extensions::OPTION_MANAGED_SOLR_SERVERS );

?>

<?php

// Add menu item for unmanaged Solr server
$subtabs = array(
	'unmanaged_solr_servers' => 'Unmanaged Solr server',
);

// Add menu items for all the managed Solr services
foreach ( WPSOLR_ManagedServers::get_managed_solr_services() as $managed_solr_service_id => $managed_solr_service ) {
	$subtabs[ $managed_solr_service_id ] = $managed_solr_service[ WPSOLR_ManagedServers::MANAGED_SOLR_SERVICE_LABEL ];
}

// Display menu
$subtab                  = wpsolr_admin_sub_tabs( $subtabs );
$subtab_exploded         = explode( ':', $subtab );
$managed_solr_service_id = $subtab_exploded[0];

// When a menu item is selected, display specific template.
switch ( $managed_solr_service_id ) {

	case 'unmanaged_solr_servers':

		WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_MANAGED_SOLR_SERVERS, 'template-unmanaged-form.php' ) );
		break;

	default:

		$managed_solr_server = new WPSOLR_ManagedServers( $managed_solr_service_id );

		/*
		*  Form logout ?
		*/
		$is_submit_form_logout = isset( $_POST['submit-form-logout'] );
		if ( $is_submit_form_logout ) {
			// Clear the managed service token
			$managed_solr_server->set_service_option( 'token', '' );
		}

		/*
		*  Form signin ?
		*/
		$is_submit_form_signin = isset( $_POST['submit-form-signin'] );
		$form_data             = WPSOLR_Extensions::extract_form_data( $is_submit_form_signin, array(
				'email'    => array( 'default_value' => wp_get_current_user()->user_email, 'is_email' => true ),
				'password' => array( 'default_value' => '', 'can_be_empty' => false )
			)
		);
		if ( $is_submit_form_signin ) {

			if ( ! $form_data['is_error'] ) {
				$result_object = $managed_solr_server->call_rest_signin( $form_data['email']['value'], $form_data['password']['value'] );

				if ( WPSOLR_ManagedServers::is_response_ok( $result_object ) ) {

					$token = WPSOLR_ManagedServers::get_response_result( $result_object, 'token' );
					$managed_solr_server->set_service_option( 'token', $token );

				}
			}
		}

		// Display a signin form
		if ( '' != $managed_solr_server->get_service_option( 'token' ) ) {

			WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_MANAGED_SOLR_SERVERS, 'template-my-accounts.php' ),
				array(
					'form_data'           => $form_data,
					'managed_solr_server' => $managed_solr_server,
					'option_name'         => $option_name,
				)
			);

		} else {

			WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_MANAGED_SOLR_SERVERS, 'template-signin-form.php' ),
				array(
					'form_data'           => $form_data,
					'managed_solr_server' => $managed_solr_server,
					'option_name'         => $option_name,
				)
			);
		}

		break;
}
?>

