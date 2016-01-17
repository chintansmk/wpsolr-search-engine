<?php
use wpsolr\services\WPSOLR_Service_Wordpress;

?>

<script>
	jQuery(document).ready(function () {

		// Declare the list sortable
		jQuery("#sortable").sortable();
		jQuery("#sortable").accordion({active: false});

		jQuery('.plus_icon').click(function () {
			jQuery(this).parent().parent().addClass('facet_selected');
			jQuery(this).hide();
			jQuery(this).siblings().css('display', 'inline');

			jQuery(this).parent().next().children().prop('disabled', false);
		})

		jQuery('.minus_icon').click(function () {
			jQuery(this).parent().parent().removeClass('facet_selected');
			jQuery(this).hide();
			jQuery(this).siblings().css('display', 'inline');

			jQuery(this).parent().next().children().prop('disabled', true);
		})

		jQuery('#save_facets_options_form').click(function () {

		});

	});

</script>

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
				<div class='wpsolr-1col'>
					<h4>Available items for facets</h4>

					<ul id="sortable" class="connectedSortable">
						<?php
						foreach ( $facets_selected as $facet_selected_name => $facet_selected ) {

							echo <<<FACET_SELECTED_TAG
										<li class='ui-state-default facets facet_selected'>
											<div>
												<a>$facet_selected_name</a>
												<img src='$image_plus'  class='plus_icon' style='display:none'>
												<img src='$image_minus' class='minus_icon' style='display:inline' title='Click to Remove the Facet'>
											</div>
											<div id='$facet_selected_name' >
												<input type='hidden' name='wdm_solr_facet_data[facets][$facet_selected_name]' value='$facet_selected_name'/>
												test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>
											</div>
										</li>
FACET_SELECTED_TAG;
						}


						foreach ( $facets_candidates as $facet_candidate_name => $facet_candidate ) {

							$facet_candidate_name = strtolower( $facet_candidate_name );

							if ( ! isset( $facets_selected[ $facet_candidate_name ] ) ) {

								echo <<<FACET_CANDIDATE_TAG
                                        <li class='ui-state-default facets'>
                                            <div>
                                                <a>$facet_candidate_name</a>
                                                <img src='$image_plus'  class='plus_icon' style='display:inline' title='Click to Add the Facet'>
                                                <img src='$image_minus' class='minus_icon' style='display:none'>
                                            </div>
                                            <div id='$facet_candidate_name' >
                                                <input type='hidden' name='wdm_solr_facet_data[facets][$facet_candidate_name]' value='$facet_candidate_name'/>
                                                <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>test <br/>
                                            </div>
                                        </li>
FACET_CANDIDATE_TAG;
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

