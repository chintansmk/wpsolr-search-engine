<?php
use wpsolr\extensions\queries\WPSOLR_Options_Query;
use wpsolr\extensions\schemas\WPSOLR_Options_Schemas;
use wpsolr\extensions\sorts\WPSOLR_Options_Sorts;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;

?>

<?php
$schema_fields = [ ];
if ( $new_group_uuid != $group_uuid ) {
	$schema_fields = WPSOLR_Global::getExtensionSorts()->get_fields_sortable( $group );
}
?>

<div class="wdm_row">
	<div class='col_left'>Schema</div>
	<div class='col_right'>
		<?php $value = isset( $group[ WPSOLR_Options_Schemas::FORM_FIELD_SCHEMA_ID ] ) ? $group[ WPSOLR_Options_Schemas::FORM_FIELD_SCHEMA_ID ] : ''; ?>

		<?php if ( ( $new_group_uuid != $group_uuid ) && ! empty( $value ) ) { ?>
			<input type="hidden"
			       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::FORM_FIELD_SCHEMA_ID ); ?>"
			       value="<?php echo $value; ?>"
			/>
			<?php echo ! empty( $schemas[ $value ] ) ? $schemas[ $value ][ WPSOLR_Options_Schemas::FORM_FIELD_NAME ] : ''; ?>
		<?php } else { ?>
			<select
				name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Schemas::FORM_FIELD_SCHEMA_ID ); ?>">
				<?php
				foreach ( $schemas as $schema_id => $field ) { ?>
					<option value="<?php echo esc_attr( $schema_id ); ?>"
						<?php selected( $schema_id, $value, true ); ?> >
						<?php echo $field[ WPSOLR_Options_Query::FORM_FIELD_NAME ]; ?>
					</option>
				<?php } ?>
			</select>
		<?php } ?>

	</div>
	<div class="clear"></div>
</div>

<?php if ( $new_group_uuid != $group_uuid ) { ?>
	<div class="wdm_row">
		<div class='col_left'>Default sort field</div>
		<div class='col_right'>
			<select
				name="<?php echo $options_name; ?>[<?php echo $group_uuid; ?>][<?php echo WPSOLR_Options_Sorts::SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD; ?>]">
				<?php
				$default_sort_field = isset( $group[ WPSOLR_Options_Sorts::SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD ] )
					? $group[ WPSOLR_Options_Sorts::SORTS_GROUP_FIELD_DEFAULT_SORT_FIELD ] :
					WPSOLR_Options_Sorts::SORT_CODE_BY_RELEVANCY_DESC;
				foreach ( $schema_fields as $field_name => $field ) { ?>
					<option
						value="<?php echo $field_name; ?>" <?php selected( $field_name, $default_sort_field, true ); ?>>
						<?php echo $field_name; ?>
					</option>
				<?php } ?>
			</select>
		</div>
		<div class="clear"></div>
	</div>

	<ul class="sortable connectedSortable">

		<?php
		foreach ( $schema_fields as $field_name => $field ) {


			if ( ! empty( $group[ WPSOLR_Options_Sorts::FORM_FIELD_SORTS ] ) && ! empty( $group[ WPSOLR_Options_Sorts::FORM_FIELD_SORTS ][ $field_name ] ) ) {

				WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_SORTS, 'sort.inc.php' ),
					array(
						'options_name'        => $options_name,
						'group_uuid'          => $group_uuid,
						'layouts'             => $layouts,
						'sort_name'           => $field_name,
						'is_numeric'          => WPSOLR_Global::getExtensionSchemas()->get_field_type_definition( $field, $field_name )->get_is_numeric(),
						'sort'                => $group[ WPSOLR_Options_Sorts::FORM_FIELD_SORTS ][ $field_name ],
						'sort_selected_class' => 'group_content_selected',
						'image_plus_display'  => 'none',
						'image_minus_display' => 'inline',
						'image_plus'          => $image_plus,
						'image_minus'         => $image_minus

					) );

			} else {

				WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_SORTS, 'sort.inc.php' ),
					array(
						'options_name'        => $options_name,
						'group_uuid'          => $group_uuid,
						'layouts'             => $layouts,
						'sort_name'           => $field_name,
						'is_numeric'          => WPSOLR_Global::getExtensionSchemas()->get_field_type_definition( $field, $field_name )->get_is_numeric(),
						'sort'                => $field,
						'sort_selected_class' => 'group_content_not_selected',
						'image_plus_display'  => 'inline',
						'image_minus_display' => 'none',
						'image_plus'          => $image_plus,
						'image_minus'         => $image_minus
					) );

			}
		}
		?>

	</ul>

<?php } ?>





