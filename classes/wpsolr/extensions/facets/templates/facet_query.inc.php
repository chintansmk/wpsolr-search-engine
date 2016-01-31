<?php
use wpsolr\extensions\facets\WPSOLR_Options_Facets;

?>

<div class="wdm_row wpsolr_facet_type wpsolr_<?php echo WPSOLR_Options_Facets::FACET_TYPE_RANGE_CUSTOM; ?>">
	<div class='col_left'>
		Define your ranges</br></br>
		0|9|Range from %1$d - %2$d (%3$d)</br>
		10|20|Range 10 TO 20 (%3$d)</br>
		21|100|Range %s => %s (%3$d)</br>
		101|*|More than 100 (%3$d)</br>
	</div>
	<div class='col_right'>
				<textarea type='text' rows="10" style="width:98%"
				          name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE ?>][<?php echo WPSOLR_Options_Facets::FACET_FIELD_CUSTOM_RANGE_RANGES ?>]'
				><?php echo esc_attr( $facet_query_ranges ); ?></textarea>

	</div>

</div>