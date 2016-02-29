<?php
/**
 * Plugin Name: Apache Solr search by WPSOLR
 * Description: WPSOLR is the growing, blazing-fast, open source enterprise search plugin built on Apache Solr.
 * Version: 7.7
 * Author: wpsolr
 * Plugin URI: http://www.wpsolr.com
 * License: GPL2
 */

use wpsolr\extensions\localization\WPSOLR_Localization;
use wpsolr\extensions\sorts\WPSOLR_Options_Sorts;
use wpsolr\solr\WPSOLR_IndexSolrClient;
use wpsolr\solr\WPSOLR_SearchSolrClient;
use wpsolr\ui\shortcode\WPSOLR_Shortcode;
use wpsolr\ui\widget\WPSOLR_Widget;
use wpsolr\ui\WPSOLR_Query_Parameters;
use wpsolr\ui\WPSOLR_UI;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\WPSOLR_Filters;

// Constants
define( 'WPSOLR_DEFINE_PLUGIN_DIR_URL', substr_replace( plugin_dir_url( __FILE__ ), "", - 1 ), false );

// Composer autoloader
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// wpsolr namespace autoloader
require_once plugin_dir_path( __FILE__ ) . 'classes/wpsolr/WPSOLR_Autoloader.php';
spl_autoload_register( array( '\\wpsolr\\WPSOLR_Autoloader', 'Load' ) );

require_once 'ajax_solr_services.php';
require_once 'dashboard_settings.php';
require_once 'autocomplete.php';

/* Register Solr settings from dashboard
 * Add menu page in dashboard - Solr settings
 * Add solr settings- solr host, post and path
 *
 */
add_action( 'wp_head', 'check_default_options_and_function' );
add_action( 'admin_menu', 'fun_add_solr_settings' );
add_action( 'admin_init', 'wpsolr_admin_init' );
add_action( 'wp_enqueue_scripts', 'my_front_enqueue' );
add_action( 'admin_enqueue_scripts', 'my_admin_enqueue' );

// Register WpSolr widgets when current theme's search is used.
if ( WPSOLR_Global::getOption()->get_search_is_use_current_theme_search_template() ) {
	WPSOLR_Widget::wpsolr_autoload();
}

// Add all shortcodes
WPSOLR_Shortcode::add_shortcodes();

/*
 * Display Solr errors in admin when a save on a post can't index to Solr
 */
function solr_post_save_admin_notice() {
	if ( $out = get_transient( get_current_user_id() . 'error_solr_post_save_admin_notice' ) ) {
		delete_transient( get_current_user_id() . 'error_solr_post_save_admin_notice' );
		echo "<div class=\"error\"><p>(WPSOLR) Error while indexing this post/page in Solr:<br><br>$out</p></div>";
	}

	if ( $out = get_transient( get_current_user_id() . 'updated_solr_post_save_admin_notice' ) ) {
		delete_transient( get_current_user_id() . 'updated_solr_post_save_admin_notice' );
		echo "<div class=\"updated\"><p>(WPSOLR) $out</p></div>";
	}

	if ( $out = get_transient( get_current_user_id() . 'wpsolr_some_languages_have_no_solr_index_admin_notice' ) ) {
		delete_transient( get_current_user_id() . 'wpsolr_some_languages_have_no_solr_index_admin_notice' );
		echo "<div class=\"error\"><p>(WPSOLR) $out</p></div>";
	}

	if ( $out = get_transient( get_current_user_id() . 'wpsolr_generic_notice' ) ) {
		delete_transient( get_current_user_id() . 'wpsolr_generic_notice' );
		echo "<div class=\"error\"><p>(WPSOLR) $out</p></div>";
	}

}

add_action( 'admin_notices', "solr_post_save_admin_notice" );

/*
 * Add/remove document to/from Solr index when status changes to/from published
 * We have to use action 'save_post', as it is used by other plugins to trigger meta boxes save
 */
function add_remove_document_to_solr_index( $post_id, $post, $update ) {

	// If this is just a revision, don't go on.
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// If this is just a new post opened in editor, don't go on.
	if ( 'auto-draft' == $post->post_status ) {
		return;
	}

	try {
		if ( 'publish' == $post->post_status ) {
			// post published, add/update it from Solr index

			$solr = WPSOLR_IndexSolrClient::create_from_post( $post );

			$solr->index_data( 1, $post );

			// Display confirmation in admin
			set_transient( get_current_user_id() . 'updated_solr_post_save_admin_notice', 'Post/page indexed in Solr' );

		} else {
			// post unpublished, remove it from Solr index
			$solr = WPSOLR_IndexSolrClient::create_from_post( $post );

			$solr->delete_document( $post );

			// Display confirmation in admin
			set_transient( get_current_user_id() . 'updated_solr_post_save_admin_notice', 'Post/Page deleted from Solr' );
		}

	} catch ( Exception $e ) {
		set_transient( get_current_user_id() . 'error_solr_post_save_admin_notice', htmlentities( $e->getMessage() ) );
	}

}

add_action( 'save_post', 'add_remove_document_to_solr_index', 10, 3 );

/*
 * Add an attachment to Solr
 */
add_action( 'add_attachment', 'add_attachment_to_solr_index', 10, 3 );
function add_attachment_to_solr_index( $attachment_id ) {

	// Index the new attachment
	try {
		$solr = WPSOLR_IndexSolrClient::create();

		$solr->index_data( 1, get_post( $attachment_id ) );

		set_transient( get_current_user_id() . 'updated_solr_post_save_admin_notice', 'Attachment added to Solr' );

	} catch ( Exception $e ) {

		set_transient( get_current_user_id() . 'error_solr_post_save_admin_notice', htmlentities( $e->getMessage() ) );
	}

}

/*
 * Delete an attachment from Solr
 */
add_action( 'delete_attachment', 'delete_attachment_to_solr_index', 10, 3 );
function delete_attachment_to_solr_index( $attachment_id ) {

	// Remove the attachment from Solr index
	try {
		$solr = WPSOLR_IndexSolrClient::create();

		$solr->delete_document( get_post( $attachment_id ) );

		set_transient( get_current_user_id() . 'updated_solr_post_save_admin_notice', 'Attachment deleted from Solr' );

	} catch ( Exception $e ) {

		set_transient( get_current_user_id() . 'error_solr_post_save_admin_notice', htmlentities( $e->getMessage() ) );
	}

}


/* Replace WordPress search
 * Default WordPress will be replaced with Solr search
 */


function check_default_options_and_function() {

	if ( WPSOLR_Global::getOption()->get_search_is_replace_default_wp_search()
	     && ! WPSOLR_Global::getOption()->get_search_is_use_current_theme_search_template()
	) {

		add_filter( 'get_search_form', 'solr_search_form' );

	}
}

add_filter( 'template_include', 'portfolio_page_template', 99 );
function portfolio_page_template( $template ) {

	if ( is_page( WPSOLR_SearchSolrClient::_SEARCH_PAGE_SLUG ) ) {
		$new_template = locate_template( WPSOLR_SearchSolrClient::_SEARCH_PAGE_TEMPLATE );
		if ( '' != $new_template ) {
			return $new_template;
		}
	}

	return $template;
}

/* Create default page template for search results
*/
add_shortcode( 'solr_search_shortcode', 'fun_search_indexed_data' );
add_shortcode( 'solr_form', 'fun_dis_search' );
function fun_dis_search() {
	echo solr_search_form();
}


register_activation_hook( __FILE__, 'my_register_activation_hook' );
function my_register_activation_hook() {

	/*
	 * Migrate old data on plugin update
	 */
	WPSOLR_Global::getExtensionIndexes()->migrate_data_from_v4_9();
	WPSOLR_Global::getExtensionSchemas()->migrate_data_from_v7_6();
}


add_action( 'admin_notices', 'curl_dependency_check' );
function curl_dependency_check() {
	if ( ! in_array( 'curl', get_loaded_extensions() ) ) {

		echo "<div class='updated'><p><b>cURL</b> is not installed on your server. In order to make <b>'Solr for WordPress'</b> plugin work, you need to install <b>cURL</b> on your server </p></div>";
	}
}


function solr_search_form() {

	ob_start();

	// Load current theme's wpsolr search form if it exists
	$search_form_template = locate_template( 'wpsolr-search-engine/searchform.php' );
	if ( '' != $search_form_template ) {

		require( $search_form_template );
		$form = ob_get_clean();

	} else {

		$ad_url = admin_url();

		if ( isset( $_GET[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_Q ] ) ) {
			$search_que = $_GET[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_Q ];
		} else if ( isset( $_GET[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_SEARCH ] ) ) {
			$search_que = $_GET[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_SEARCH ];
		} else {
			$search_que = '';
		}

		// Get localization options
		$localization_options = WPSOLR_Localization::get_options();

		$wdm_typehead_request_handler = 'wdm_return_solr_rows';

		$get_page_info = WPSOLR_SearchSolrClient::get_search_page();
		$ajax_nonce    = wp_create_nonce( "nonce_for_autocomplete" );


		$url = get_permalink( $get_page_info->ID );
		// Filter the search page url. Used for multi-language search forms.
		$url = apply_filters( WPSOLR_Filters::WPSOLR_FILTER_SEARCH_PAGE_URL, $url, $get_page_info->ID );

		$form = "<div class='cls_search' style='width:100%'><form action='$url' method='get'  class='search-frm2' >";
		$form .= '<input type="hidden" value="' . $wdm_typehead_request_handler . '" id="path_to_fold">';
		$form .= '<input type="hidden"  id="ajax_nonce" value="' . $ajax_nonce . '">';

		$form .= '<input type="hidden" value="' . $ad_url . '" id="path_to_admin">';
		$form .= '<input type="hidden" value="' . $search_que . '" id="search_opt">';
		$form .= '
       <div class="ui-widget search-box">
 	<input type="hidden"  id="ajax_nonce" value="' . $ajax_nonce . '">
        <input type="text" placeholder="' . WPSOLR_Localization::get_term( $localization_options, 'search_form_edit_placeholder' ) . '" value="' . $search_que . '" name="' . WPSOLR_Query_Parameters::SEARCH_PARAMETER_Q . '" id="search_que" class="search-field sfl1" autocomplete="off"/>
	<input type="submit" value="' . WPSOLR_Localization::get_term( $localization_options, 'search_form_button_label' ) . '" id="searchsubmit" style="position:relative;width:auto">
        <div style="clear:both"></div>
        </div>
	</div>
       </form>';
	}

	return $form;


}

add_action( 'plugins_loaded', 'my_plugins_loaded' );
function my_plugins_loaded() {
	/*
	global $g_wpsolr_extensions;

	// Load active extensions
	if ( ! isset( $g_wpsolr_extensions ) ) {
		$g_wpsolr_extensions = new WPSOLR_Extensions();
	}
	*/

	/*
	 * Load all active extensions
	 */
	WPSOLR_Global::getActiveExtensions();

	/*
	 * Load WPSOLR text domain to the Wordpress languages plugin directory (WP_LANG_DIR/plugins)
	 * Copy your .mo files there
	 * Example: /htdocs/wp-includes/languages/plugins/wpsolr-fr_FR.mo or /htdocs/wp-content/languages/plugins/wpsolr-fr_FR.mo
	 * You can find our template file in this plugin's /languages/wpsolr.pot file
	 */
	load_plugin_textdomain( 'wpsolr', false, false );

	/**
	 * Load string translations
	 */
	if ( is_admin() ) {

		// Translate facets
		do_action( WPSOLR_Filters::WPSOLR_ACTION_TRANSLATION_REGISTER_STRINGS,
			[
				'translations' => WPSOLR_Global::getExtensionFacets()->get_strings_to_translate()
			]
		);

		// Translate sorts
		do_action( WPSOLR_Filters::WPSOLR_ACTION_TRANSLATION_REGISTER_STRINGS,
			[
				'translations' => WPSOLR_Global::getExtensionSorts()->get_strings_to_translate()
			]
		);

		// Translate shortcodes
		do_action( WPSOLR_Filters::WPSOLR_ACTION_TRANSLATION_REGISTER_STRINGS,
			[
				'translations' => WPSOLR_Global::getExtensionComponents()->get_strings_to_translate()
			]
		);

	}

}

function my_front_enqueue() {

	if ( ! WPSOLR_Global::getOption()->get_search_is_prevent_loading_front_end_css() ) {
		wp_enqueue_style( 'solr_auto_css', plugins_url( 'css/bootstrap.min.css', __FILE__ ) );
		wp_enqueue_style( 'solr_frontend', plugins_url( 'css/style.css', __FILE__ ) );
	}

	if ( ! WPSOLR_Global::getOption()->get_search_is_do_not_show_suggestions() ) {
		wp_enqueue_script( 'solr_auto_js1', plugins_url( 'js/bootstrap-typeahead.js', __FILE__ ), array( 'jquery' ), false, true );
	}

	// Url utilities to manipulate the url parameters
	wp_enqueue_script( 'urljs', plugins_url( 'bower_components/jsurl/url.min.js', __FILE__ ), array( 'jquery' ), false, true );

	wp_enqueue_script( 'autocomplete', plugins_url( 'js/autocomplete_solr.js', __FILE__ ), array(
		'solr_auto_js1',
		'urljs'
	), false, true );
	wp_localize_script( 'autocomplete', 'wp_localize_script_autocomplete',
		array(
			'ajax_url'                    => admin_url( 'admin-ajax.php' ),
			'is_show_url_parameters'      => WPSOLR_Global::getOption()->get_search_is_ajax_with_url_parameters(),
			'is_url_redirect'             => WPSOLR_Global::getOption()->get_search_is_use_current_theme_search_template(),
			'SEARCH_PARAMETER_SEARCH'     => WPSOLR_Query_Parameters::SEARCH_PARAMETER_SEARCH,
			'SEARCH_PARAMETER_Q'          => WPSOLR_Query_Parameters::get_query_parameter_name(),
			'SEARCH_PARAMETER_FQ'         => WPSOLR_Query_Parameters::SEARCH_PARAMETER_FQ,
			'SEARCH_PARAMETER_SORT'       => WPSOLR_Query_Parameters::SEARCH_PARAMETER_SORT,
			'SEARCH_PARAMETER_PAGE'       => WPSOLR_Query_Parameters::SEARCH_PARAMETER_PAGE,
			'SORT_CODE_BY_RELEVANCY_DESC' => WPSOLR_Options_Sorts::SORT_CODE_BY_RELEVANCY_DESC,
		) );

	wp_enqueue_script( 'wpsolr_ui', plugins_url( 'js/wpsolr_component.js', __FILE__ ), array( 'jquery' ), false, true );
	wp_localize_script( 'wpsolr_ui', 'wp_localize_script_wpsolr_component',
		array(
			'SEARCH_PARAMETER_Q' => WPSOLR_Query_Parameters::SEARCH_PARAMETER_Q,
			'SEARCH_PARAMETER_S' => WPSOLR_Query_Parameters::SEARCH_PARAMETER_S,
			'ajax_url'           => admin_url( 'admin-ajax.php' ),
			'ajax_action'        => WPSOLR_UI::METHOD_DISPLAY_AJAX
		) );

	/*
	 * Infinite scroll: load javascript if option is set.
	 */
	if ( WPSOLR_Global::getOption()->get_search_is_infinitescroll() ) {
		// Get localization options
		$localization_options = WPSOLR_Localization::get_options();

		wp_register_script( 'infinitescroll', plugins_url( '/js/jquery.infinitescroll.js', __FILE__ ), array( 'jquery' ), '1.0', true );

		wp_enqueue_script( 'infinitescroll' );

		// loadingtext for translation
		// loadimage custom loading image url
		wp_localize_script( 'infinitescroll', 'wp_localize_script_infinitescroll',
			array(
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'loadimage'          => plugins_url( '/images/infinitescroll.gif', __FILE__ ),
				'loadingtext'        => WPSOLR_Localization::get_term( $localization_options, 'infinitescroll_loading' ),
				'SEARCH_PARAMETER_Q' => WPSOLR_Query_Parameters::SEARCH_PARAMETER_Q,
			) );
	}

	// Jquery js and css
	wp_enqueue_style( 'jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );
	wp_enqueue_script( 'jquery-ui-slider' );

	// Bootstrap tooltips
	wp_enqueue_style( 'bootstrap-style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css' );
	wp_register_script( 'tooltip', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js', 'jquery' );
	wp_enqueue_script( 'tooltip' );


}


function my_admin_enqueue() {

	// Url utilities to manipulate the url parameters
	wp_enqueue_script( 'urljs', plugins_url( 'bower_components/jsurl/url.min.js', __FILE__ ), array( 'jquery' ), false, true );
}
