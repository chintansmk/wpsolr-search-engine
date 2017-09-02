<?php
session_start();

include( WPSOLR_PLUGIN_DIR . '/classes/solr/wpsolr-index-solr-client.php' );
include( WPSOLR_PLUGIN_DIR . '/classes/solr/wpsolr-search-solr-client.php' );
include( WPSOLR_PLUGIN_DIR . '/classes/ui/WPSOLR_Data_facets.php' );

// Load localization class
WpSolrExtensions::require_once_wpsolr_extension( WpSolrExtensions::OPTION_LOCALIZATION, true );
WpSolrExtensions::load();


function solr_format_date( $thedate ) {
	$datere  = '/(\d{4}-\d{2}-\d{2})\s(\d{2}:\d{2}:\d{2})/';
	$replstr = '${1}T${2}Z';

	return preg_replace( $datere, $replstr, $thedate );
}

function fun_search_indexed_data($atts) {

    //echo "<br/>BEFORE SOLR FILTER QUERY";
    //foreach (WPSOLR_Global::getQuery()->get_filter_query_fields() as $f){
    //    echo "<br/>FILTER: ".$f;
    //}
    $my_atts = shortcode_atts( array(
        'apply_facets' => 'comma separted facets:value',
        'facets_to_display'=> 'comma separted facet1,facet2 to be displayed',
        'search_placeholder' => 'Enter Keyword...',
        'hide_facets' => 'yes or no',
        'hide_search_bar' => 'yes or no',
        'initial_template' => 'template',
        'display' => 'list (default) or gallery'
    ), $atts );


    $search_placeholder = 'Enter Keyword...';
    if (isset($atts['search_placeholder'])){
        $search_placeholder = $atts['search_placeholder'];
    }

    if(isset($atts['apply_facets'])){
        //echo "<br/><br/>ATTS".$atts['apply_facets'];
        $scode_facets = explode(",",$atts['apply_facets']);
        $facets = WPSOLR_Global::getQuery()->get_filter_query_fields();
        $merged_facets = array_merge($facets, $scode_facets);

        WPSOLR_Global::getQuery()->set_filter_query_fields($merged_facets);
    }  
    $facets_to_display = array();
    if(isset($atts['facets_to_display'])){
        $facets_to_display = explode(",",$atts['facets_to_display']);
    }

    $hide_facets_style = '';
    if(isset($atts['hide_facets'])){
        if($atts['hide_facets'] == 'yes'){
            $hide_facets_style = 'display:none';
        }
    }
    
    $hide_search_style = '';
    if(isset($atts['hide_search_bar'])){
        if($atts['hide_search_bar'] == 'yes'){
            $hide_search_style = 'display:none';
        }
    }

    $results_display_css = 'gallery-results';
    if(isset($atts['display'])){
        if($atts['display'] == 'list'){
            $results_display_css = 'list-results';
        }
    }

    $initial_template = '';
    if (isset($atts['initial_template'])){
        $initial_template = $atts['initial_template'];
    }

   //this is used to show the initial template on publication page 
    $showing_template = false;  
    if ($initial_template != '' && 
            !isset($_REQUEST['search'])  
                &&
            !isset($_REQUEST['wpsolr_fq']) 
        ) {
        $showing_template = true;  
    }


    //echo "<br/>AFTER SOLR FILTER QUERY";
    //foreach (WPSOLR_Global::getQuery()->get_filter_query_fields() as $f){
    //    echo "<br/>FILTER: ".$f;
    //}

	$ad_url = admin_url();

	// Retrieve search form page url
	$get_page_info = WPSolrSearchSolrClient::get_search_page();
	$url           = get_permalink( $get_page_info->ID );
	// Filter the search page url. Used for multi-language search forms.
	$url = apply_filters( WpSolrFilters::WPSOLR_FILTER_SEARCH_PAGE_URL, $url, $get_page_info->ID );

	// Load localization options
	$localization_options = OptionLocalization::get_options();

	$wdm_typehead_request_handler = 'wdm_return_solr_rows';
    
    if(!$showing_template){
          echo '<div id="reset-publications"><a href="'.site_url("/publications/").'">
                  <i class="fa fa-refresh"></i> View All Publications</a>
                </div>';
    }       

    echo '<div class="vc_row wpb_row section vc_row-fluid " style=" text-align:left;">';
    echo '    <div class=" full_section_inner clearfix">';
    echo '        <div class="search-left-sidebar wpb_column vc_column_container vc_col-sm-3">';
    echo '            <div class="vc_column-inner ">';
    echo '                <div class="wpb_wrapper">';

	echo "<form action='.' method='get'  class='search-frm' >";
	echo '<input type="hidden" value="' . $wdm_typehead_request_handler . '" id="path_to_fold">';
	echo '<input type="hidden" value="' . $ad_url . '" id="path_to_admin">';
	echo '<input type="hidden" value="' . WPSOLR_Global::getQuery()->get_wpsolr_query() . '" id="search_opt">';

	$ajax_nonce = wp_create_nonce( "nonce_for_autocomplete" );

	echo $form = '
	<input type="hidden"  id="ajax_nonce" value="' . $ajax_nonce . '">
        <input type="text" placeholder="' . OptionLocalization::get_term( $localization_options, 'search_form_edit_placeholder' ) . '" value="' . WPSOLR_Global::getQuery()->get_wpsolr_query() . '" name="search" id="search_que" class="search-field sfl2" autocomplete="off"/>
	<input type="submit" value="' . OptionLocalization::get_term( $localization_options, 'search_form_button_label' ) . '" id="searchsubmit">
	<input type="hidden" value="' . WPSOLR_Global::getOption()->get_search_after_autocomplete_block_submit() . '" id="is_after_autocomplete_block_submit">
	<input type="hidden" value="' . WPSOLR_Global::getQuery()->get_wpsolr_paged() . '" id="paginate">
<div style="clear:both"></div>
        </form>';

    //display as list or gallery
    if(isset($_REQUEST['ltype'])){
        $results_display_css = $_REQUEST['ltype']."-results";
        $_SESSION["ltype"] = $results_display_css;
    }elseif (isset($_SESSION["ltype"])){
        $results_display_css = $_SESSION["ltype"];
    }



	try {

		try {

			$final_result = WPSOLR_Global::getSolrClient()->display_results( WPSOLR_Global::getQuery(), $results_display_css );

		} catch ( Exception $e ) {

			$message = $e->getMessage();
			echo "<span class='infor'>$message</span>";
			die();
		}

		//if ( $final_result[2] == 0 ) {
		//	echo "<span class='infor'>" . sprintf( OptionLocalization::get_term( $localization_options, 'results_header_no_results_found' ), WPSOLR_Global::getQuery()->get_wpsolr_query() ) . "</span>";
		//} else {
			//echo '<div class="wdm_resultContainer">
             //       <div class="wdm_list">';

			
       
            

			// Display facets UI
			echo '<div id="res_facets">' . WPSOLR_UI_Facets::Build(
					WPSOLR_Data_Facets::get_data(
						WPSOLR_Global::getQuery()->get_filter_query_fields_group_by_name(),
						WPSOLR_Global::getOption()->get_facets_to_display(),
						$final_result[1] ),
                    $localization_options );
            echo '      </div> <!-- #/res_facets -->';
            echo '    </div> <!-- .search-left-sidebar ./wpb_wrapper -->';
            echo '  </div> <!-- .search-left-sidebar vc_column_inner -->';
            echo '</div> <!-- .search-left-sidebar wpb_column -->';

            if(!$showing_template){
                echo '<div id="ltype-switch">';
                echo ' <a href="?'.http_build_query(array_merge($_GET, array('ltype'=>'gallery'))).'"> 
                       <i class="qode_icon_font_awesome fa fa-th-large"> </i> Gallery View</a>';
                echo '      &nbsp; | &nbsp;';
                echo ' <a href="?'.http_build_query(array_merge($_GET, array('ltype'=>'list'))).'">
                       <i class="qode_icon_font_awesome fa fa-list"> </i> List View</a>';
                echo '<br/>';     
            // Display the sort list
			$selected_sort_values = WPSOLR_Global::getOption()->get_sortby_items_as_array();
			if ( isset( $selected_sort_values ) && ( $selected_sort_values != '' ) ) {

				$term        = OptionLocalization::get_term( $localization_options, 'sort_header' );
				$sort_select = "<label class='wdm_label'>$term</label> <select class='select_field'>";

				// Add options
				$sort_options = WPSolrSearchSolrClient::get_sort_options();
				foreach ( $selected_sort_values as $sort_code ) {

					$sort_label = OptionLocalization::get_term( $localization_options, $sort_code );

					$selected = ( $sort_code === WPSOLR_Global::getQuery()->get_wpsolr_sort() ) ? 'selected' : '';
					$sort_select .= "<option value='$sort_code' $selected>$sort_label</option>";
				}

				$sort_select .= "</select>";

				echo '<div>' . $sort_select . '</div>';
			}

                echo '</div>';
            } 

            echo '<div class="search-right-sidebar wpb_column vc_column_container vc_col-sm-9">';
                       
            echo '  <div class="vc_column-inner ">';
            echo '    <div class="wpb_wrapper">';

          

            if($showing_template){
                include(ABSPATH.'wp-content/themes/bridge/'.$initial_template);
            }

            echo '<div id="search-results-gallery" class="'.$results_display_css.'"';
            if($showing_template){
                echo ' style="display:none"';
            }
            echo ' >';
            
            
                        
			if ( $final_result[0] != '0' ) {
				echo $final_result[0];
			}

			$ui_result_rows = $final_result[3];
            if ( WPSOLR_Global::getOption()->get_search_is_display_results_info() && $ui_result_rows != 0 ) {
                //if (WPSOLR_Global::getQuery()->get_wpsolr_query() != ''){
                //    echo '<div class="res_info">' . $final_result[4] . '</div>';
                //}else{
                echo '<div class="res_info">' . $final_result[4] . '</div>';
                //}
			}

			if ( $ui_result_rows != 0 ) {
				$img = plugins_url( 'images/gif-load.gif', __FILE__ );
				echo '<div class="loading_res"><img src="' . $img . '"></div>';
                echo '<div class="results-by-facets">';
            
                            
                $rindx = 0;
                echo '<div class="vc_row wpb_row section vc_row-fluid vc_inner " style=" text-align:left;">';
                echo ' <div class=" full_section_inner clearfix">';

                
				foreach ( $ui_result_rows as $resarr ) {
                   if ($rindx > 0 && $rindx % 3 == 0){
                     echo ' </div>';
                     echo '</div>';
                     echo '<div class="vc_row wpb_row section vc_row-fluid vc_inner " style=" text-align:left;">';
                     echo '   <div class=" full_section_inner clearfix">';
                   }
				   echo $resarr;
                   $rindx += 1;
				}

                if($rindx % 3 != 0){ //we exited loop in middle so close the last row
                     echo ' </div>';
                    echo '</div>';
                }

				echo "</div> <!-- ./results_by_facets -->";
				echo "<div class='paginate_div'>";
				$total         = $final_result[2];
				$number_of_res = WPSOLR_Global::getOption()->get_search_max_nb_results_by_page();
				if ( $total > $number_of_res ) {
					$pages = ceil( $total / $number_of_res );
					echo '<ul id="pagination-flickr" class="wdm_ul">';
					for ( $k = 1; $k <= $pages; $k ++ ) {
						echo "<li ><a class='paginate' href='javascript:void(0)' id='$k'>$k</a></li>";
					}
				}
				echo '</ul></div>';

			}

            echo '</div> <!-- ./wdm_results -->';
            echo '</div> <!-- ./wpb_wrapper -->';
            echo '</div> <!-- ./vc_column-inner -->';
            echo '</div> <!-- ./right-sidebar -->';
            echo '</div> <!-- ./full_Section -->';
            echo '</div> <!-- ./vc_row -->';

   
		//}
	} catch ( Exception $e ) {

		echo sprintf( 'The search could not be performed. An error occured while trying to connect to the Apache Solr server. <br/><br/>%s<br/>', $e->getMessage() );
	}

	echo '</div>';
}

function get_url_parameter($param){
    $url_params = array();
	if ( isset( $_SERVER['QUERY_STRING'] ) ) {
		parse_str( $_SERVER['QUERY_STRING'], $url_params );
    }

    if (isset($url_params[$param])){
        return $url_params[$param];
    }

    return NULL;
}



function return_solr_instance() {

	$path = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
	require_once $path;


	$spath    = $_POST['spath'];
	$port     = $_POST['sport'];
	$host     = $_POST['shost'];
	$username = $_POST['skey'];
	$password = $_POST['spwd'];
	$protocol = $_POST['sproto'];

	$client = WPSolrSearchSolrClient::create_from_solarium_config( array(
		'endpoint' => array(
			'localhost1' => array(
				'scheme'   => $protocol,
				'host'     => $host,
				'port'     => $port,
				'path'     => $spath,
				'username' => $username,
				'password' => $password
			)
		)
	) );

	try {

		$result = $client->ping();

	} catch ( Exception $e ) {

		$str_err     = "";
		$solrCode    = $e->getCode();
		$solrMessage = $e->getMessage();

		switch ( $e->getCode() ) {

			case 401:
				$str_err .= "<br /><span>The server authentification failed. Please check your user/password (Solr code http $solrCode)</span><br />";
				break;

			case 400:
			case 404:

				$str_err .= "<br /><span>We could not join your Solr server. Your Solr path could be malformed, or your Solr server down (Solr code $solrCode)</span><br />";
				break;

			default:

				// Try to interpret some special errors with code "0"
				if ( ( strpos( $e->getStatusMessage(), 'Failed to connect' ) > 0 ) && ( strpos( $e->getStatusMessage(), 'Connection refused' ) > 0 ) ) {

					$str_err .= "<br /><span>We could not connect to your Solr server. It's probably because the port is blocked. Please try another port, for instance 443, or contact your hosting provider/network administrator to unblock your port.</span><br />";

				} else {

					$str_err .= "<span>$solrMessage ($solrCode)</span><br /><br />\n";
				}

				break;

		}


		echo $str_err;
		echo '<br>';
		echo htmlentities( $solrMessage );

		return;

	}


}

add_action( 'wp_ajax_nopriv_' . 'return_solr_instance', 'return_solr_instance' );
add_action( 'wp_ajax_' . 'return_solr_instance', 'return_solr_instance' );


function return_solr_status() {

	echo $words = WPSOLR_Global::getSolrClient()->get_solr_status();
}

add_action( 'wp_ajax_nopriv_' . 'return_solr_status', 'return_solr_status' );
add_action( 'wp_ajax_' . 'return_solr_status', 'return_solr_status' );


function return_solr_results() {

    $results_display_css = "gallery-results";

    if(isset($_REQUEST["ltype"])){
        $results_display_css = $_REQUEST["ltype"]."-results";
    }elseif(isset($_SESSION["ltype"])){
        $results_display_css = $_SESSION["ltype"];
    }


	$final_result = WPSOLR_Global::getSolrClient()->display_results( WPSOLR_Global::getQuery(), $results_display_css );

	// Add result rows as html
	$res1[] = $final_result[3];

	// Add pagination html
	$total         = $final_result[2];
	$number_of_res = WPSOLR_Global::getOption()->get_search_max_nb_results_by_page();
	$paginat_var   = '';
	if ( $total > $number_of_res ) {
		$pages = ceil( $total / $number_of_res );
		$paginat_var .= '<ul id="pagination-flickr" class="wdm_ul">';
		for ( $k = 1; $k <= $pages; $k ++ ) {
			$paginat_var .= "<li ><a class='paginate' href='javascript:void(0)' id='$k'>$k</a></li>";
		}
		$paginat_var .= '</ul>';
	}
	$res1[] = $paginat_var;

	// Add results infos html ('showing x to y results out of n')
	$res1[] = $final_result[4];

	// Add facets data
	$res1[] = WPSOLR_UI_Facets::Build(
		WPSOLR_Data_Facets::get_data(
			WPSOLR_Global::getQuery()->get_filter_query_fields_group_by_name(),
			WPSOLR_Global::getOption()->get_facets_to_display(),
			$final_result[1] ),
		OptionLocalization::get_options()
	);

	// Output Json response to Ajax call
	echo json_encode( $res1 );


	die();
}

add_action( 'wp_ajax_nopriv_' . 'return_solr_results', 'return_solr_results' );
add_action( 'wp_ajax_' . 'return_solr_results', 'return_solr_results' );

/*
 * Ajax call to index Solr documents
 */
function return_solr_index_data() {

	try {
		// Indice of Solr index to index
		$solr_index_indice = $_POST['solr_index_indice'];

		// Batch size
		$batch_size = intval( $_POST['batch_size'] );

		// nb of document sent until now
		$nb_results = intval( $_POST['nb_results'] );

		// Debug infos displayed on screen ?
		$is_debug_indexing = ( $_POST['is_debug_indexing'] === "true" );

		// Re-index all the data ?
		$is_reindexing_all_posts = ( $_POST['is_reindexing_all_posts'] === "true" );

		$solr = WPSolrIndexSolrClient::create( $solr_index_indice );
		// Reset documents if requested
		if ( $is_reindexing_all_posts ) {
			$solr->reset_documents();
		}
		$res_final = $solr->index_data( $batch_size, null, $is_debug_indexing );

		// Increment nb of document sent until now
		$res_final['nb_results'] += $nb_results;

		echo json_encode( $res_final );

	} catch ( Exception $e ) {

		echo json_encode(
			array(
				'nb_results'        => 0,
				'status'            => $e->getCode(),
				'message'           => htmlentities( $e->getMessage() ),
				'indexing_complete' => false
			)
		);

	}

	die();
}

add_action( 'wp_ajax_nopriv_' . 'return_solr_index_data', 'return_solr_index_data' );
add_action( 'wp_ajax_' . 'return_solr_index_data', 'return_solr_index_data' );


/*
 * Ajax call to clear Solr documents
 */
function return_solr_delete_index() {

	try {

		// Indice of Solr index to delete
		$solr_index_indice = $_POST['solr_index_indice'];

		$solr = WPSolrIndexSolrClient::create( $solr_index_indice );
		$solr->delete_documents();

	} catch ( Exception $e ) {

		echo json_encode(
			array(
				'nb_results'        => 0,
				'status'            => $e->getCode(),
				'message'           => htmlentities( $e->getMessage() ),
				'indexing_complete' => false
			)
		);

	}

	die();
}

add_action( 'wp_ajax_nopriv_' . 'return_solr_delete_index', 'return_solr_delete_index' );
add_action( 'wp_ajax_' . 'return_solr_delete_index', 'return_solr_delete_index' );
