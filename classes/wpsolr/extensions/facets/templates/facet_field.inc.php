<?php
use wpsolr\extensions\facets\WPSOLR_Options_Facets;

?>

<div
	class="wdm_row wpsolr_facet_type wpsolr_<?php echo WPSOLR_Options_Facets::FACET_TYPE_FIELD; ?>">

	<input type='hidden'
	       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_TYPE; ?>]'
	       value='<?php echo WPSOLR_Options_Facets::FACET_TYPE_FIELD; ?>'/>

	<div class='col_left'>
		First facet element label
	</div>
	<div class='col_right'>
		<input type='text'
		       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL_FIRST; ?>]'
		       value='<?php echo esc_attr( ! empty( $facet_label_first ) ? $facet_label_first : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE ); ?>'/>
	</div>
	<div class='col_left'>
		Middle facet element label
	</div>
	<div class='col_right'>
		<input type='text'
		       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL; ?>]'
		       value='<?php echo esc_attr( ! empty( $facet_label ) ? $facet_label : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE ); ?>'/>
	</div>
	<div class='col_left'>
		Last facet element label
	</div>
	<div class='col_right'>
		<input type='text'
		       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL_LAST; ?>]'
		       value='<?php echo esc_attr( ! empty( $facet_label_last ) ? $facet_label_last : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE ); ?>'/>
	</div>
</div>