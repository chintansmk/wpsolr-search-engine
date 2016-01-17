<?php
use wpsolr\services\WPSOLR_Service_Wordpress;

?>

<div id="solr-facets-options" class="wdm-vertical-tabs-content">
	<form action="options.php" method="POST" id='fac_settings_form'>
		<?php
		WPSOLR_Service_Wordpress::settings_fields( $options_name );
		?>
		<div class='wrapper'>
			<h4 class='head_div'>Facets Options</h4>

			<div class="wdm_note">

				In this section, you will choose which data you want to display as facets in
				your search results. Facets are extra filters usually seen in the left hand
				side of the results, displayed as a list of links. You can add facets only
				to data you've selected to be indexed.

			</div>
			<div class="wdm_note">
				<h4>Instructions</h4>
				<ul class="wdm_ul wdm-instructions">
					<li>Click on the
						<image src='<?php echo $image_plus; ?>'/>
						icon to add a facet
					</li>
					<li>Click on the
						<image src='<?php echo $image_minus; ?>'/>
						icon to remove a facet
					</li>
					<li>Sort the items in the order you want to display them by dragging and
						dropping them at the desired plcae
					</li>
				</ul>
			</div>

			<div class="wdm_row">
				<div class='avail_fac'>
					<h4>Available items for facets</h4>
					<input type='hidden' id='select_fac' name='wdm_solr_facet_data[facets]'
					       value='<?php echo $facets_selected ?>'>


					<ul id="sortable1" class="wdm_ul connectedSortable">
						<?php
						if ( $facets_selected != '' ) {
							foreach ( $facets_selected_array as $selected_val ) {
								if ( $selected_val != '' ) {
									if ( substr( $selected_val, ( strlen( $selected_val ) - 4 ), strlen( $selected_val ) ) == "_str" ) {
										$dis_text = substr( $selected_val, 0, ( strlen( $selected_val ) - 4 ) );
									} else {
										$dis_text = $selected_val;
									}


									echo "<li id='$selected_val' class='ui-state-default facets facet_selected'>$dis_text
                                                                                                    <img src='$image_plus'  class='plus_icon' style='display:none'>
                                                                                                <img src='$image_minus' class='minus_icon' style='display:inline' title='Click to Remove the Facet'></li>";
								}
							}
						}
						foreach ( $facets_candidates as $facet_candidate ) {

							if ( $facet_candidate != '' ) {

								$buil_fac = strtolower( $facet_candidate );
								if ( substr( $buil_fac, ( strlen( $buil_fac ) - 4 ), strlen( $buil_fac ) ) == "_str" ) {
									$dis_text = substr( $buil_fac, 0, ( strlen( $buil_fac ) - 4 ) );
								} else {
									$dis_text = $buil_fac;
								}

								if ( ! in_array( $buil_fac, $facets_selected_array ) ) {

									echo "<li id='$buil_fac' class='ui-state-default facets'>$dis_text
                                                                                                    <img src='$image_plus'  class='plus_icon' style='display:inline' title='Click to Add the Facet'>
                                                                                                <img src='$image_minus' class='minus_icon' style='display:none'></li>";
								}
							}
						}
						?>


					</ul>
				</div>

				<div class="clear"></div>
			</div>

			<div class='wdm_row'>
				<div class="submit">
					<input name="save_facets_options_form" id="save_facets_options_form"
					       type="submit" class="button-primary wdm-save"
					       value="Save Options"/>
				</div>
			</div>
		</div>
	</form>
</div>

