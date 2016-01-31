<?php
use wpsolr\extensions\facets\WPSOLR_Options_Facets;

?>

<div
	class="wdm_row wpsolr_facet_type wpsolr_<?php echo WPSOLR_Options_Facets::FACET_TYPE_RANGE; ?>">

	<input type='hidden'
	       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_TYPE; ?>]'
	       value='<?php echo WPSOLR_Options_Facets::FACET_TYPE_RANGE; ?>'/>

	<div class='col_left'>
		Range start
	</div>
	<div class='col_right'>
		<input type='text'
		       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_RANGE ?>][<?php echo WPSOLR_Options_Facets::FACET_FIELD_RANGE_START ?>]'
		       value='<?php echo esc_attr( $facet_range_start ); ?>'/>

	</div>
	<div class='col_left'>
		Range end
	</div>
	<div class='col_right'>
		<input type='text'
		       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_RANGE ?>][<?php echo WPSOLR_Options_Facets::FACET_FIELD_RANGE_END ?>]'
		       value='<?php echo esc_attr( $facet_range_end ); ?>'/>

	</div>
	<div class='col_left'>
		Range gap
	</div>
	<div class='col_right'>
		<input type='text'
		       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_RANGE ?>][<?php echo WPSOLR_Options_Facets::FACET_FIELD_RANGE_GAP ?>]'
		       value='<?php echo esc_attr( $facet_range_gap ); ?>'/>

	</div>
	<div class='col_left'>
		First facet element label
	</div>
	<div class='col_right'>
		<input type='text'
		       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL_FIRST; ?>]'
		       value='<?php echo esc_attr( ! empty( $facet_label_first ) ? $facet_label_first : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE_RANGE ); ?>'/>
	</div>
	<div class='col_left'>
		Middle facet element label
	</div>
	<div class='col_right'>
		<input type='text'
		       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL; ?>]'
		       value='<?php echo esc_attr( ! empty( $facet_label ) ? $facet_label : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE_RANGE ); ?>'/>
	</div>
	<div class='col_left'>
		Last facet element label
	</div>
	<div class='col_right'>
		<input type='text'
		       name='<?php echo $facet_option_array_name; ?>[<?php echo WPSOLR_Options_Facets::FACET_FIELD_LABEL_LAST; ?>]'
		       value='<?php echo esc_attr( ! empty( $facet_label_last ) ? $facet_label_last : WPSOLR_Options_Facets::FACET_LABEL_TEMPLATE_RANGE ); ?>'/>
	</div>
</div>