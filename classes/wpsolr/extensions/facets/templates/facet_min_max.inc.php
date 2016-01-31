<?php
use wpsolr\extensions\facets\WPSOLR_Options_Facets;

?>

<div
	class="wdm_row wpsolr_facet_type wpsolr_<?php echo WPSOLR_Options_Facets::FACET_TYPE_MIN_MAX; ?>">

	<input type='hidden'
	       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_TYPE; ?>]'
	       value='<?php echo WPSOLR_Options_Facets::FACET_TYPE_MIN_MAX; ?>'/>

	<div class='col_left'>
		Label
	</div>
	<div class='col_right'>
		<input type='text'
		       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL; ?>]'
		       value='<?php echo esc_attr( ! empty( $facet_label ) ? $facet_label : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE_MIN_MAX ); ?>'/>
	</div>
</div>