<?php
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\extensions\queries\WPSOLR_Options_Query;
use wpsolr\extensions\schemas\WPSOLR_Options_Schemas;
use wpsolr\extensions\WPSOLR_Extensions;
use wpsolr\utilities\WPSOLR_Global;
use wpsolr\utilities\WPSOLR_Option;

?>

<script>
	jQuery(document).ready(function () {

		// Layouts
		var layouts = <?php echo json_encode( WPSOLR_Global::getExtensionLayouts()->get_layouts_from_type( WPSOLR_Options_Layouts::TYPE_LAYOUT_FACET ) ); ?>;

		function display_facet_types(layout_element, layout_facet_type) {
			layout_element.parent().parent().parent().children(".wpsolr_facet_type").hide(); // hide all facet type sections
			layout_element.parent().parent().parent().children(".wpsolr_facet_type").addClass("group_content_not_selected"); // Detach at form submit

			layout_element.parent().parent().parent().children(".wpsolr_" + layout_facet_type).show(); // show facet section type of the selected layout
			layout_element.parent().parent().parent().children(".wpsolr_" + layout_facet_type).removeClass("group_content_not_selected"); // Detach at form submit
		}

		// Display facet sections depending on the select layout facet type
		jQuery(".wpsolr_layout_select").each(function () {
			var layout_facet_type = layouts[jQuery(this).val()].facet_type;
			display_facet_types(jQuery(this), layout_facet_type);
		});

		// Change facet layout selection
		jQuery(".wpsolr_layout_select").on("change", function (event) {
			var layout_facet_type = layouts[jQuery(this).val()].facet_type;
			display_facet_types(jQuery(this), layout_facet_type);
		});

	});
</script>


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
		<div class='col_left'>Always show facets</div>
		<div class='col_right'>
			<input type="checkbox"
			       name="<?php echo $options_name; ?>[<?php echo $group_uuid; ?>][<?php echo WPSOLR_Option::OPTION_FACETS_GROUP_EXCLUSION; ?>]"
			       value="1" <?php checked( ! empty( $group[ WPSOLR_Option::OPTION_FACETS_GROUP_EXCLUSION ] ) ); ?>/>
		</div>
		<div class="clear"></div>
	</div>

	<ul class="sortable connectedSortable">

		<?php
		try {
			$group_schema        = WPSOLR_Global::getExtensionSchemas()->get_group( WPSOLR_Global::getExtensionFacets()->get_facet_schema_id( $groups[ $group_uuid ] ) );
			$field_custom_fields = WPSOLR_Global::getExtensionSchemas()->get_custom_fields( $group_schema );
			$field_taxonomies    = WPSOLR_Global::getExtensionSchemas()->get_taxonomies( $group_schema );
		} catch ( Exception $e ) {
			$group_schema        = '';
			$field_custom_fields = [ ];
			$field_taxonomies    = [ ];
		}

		foreach ( ! empty( $groups[ $group_uuid ][ WPSOLR_Option::OPTION_FACETS_FACETS ] ) ? $groups[ $group_uuid ][ WPSOLR_Option::OPTION_FACETS_FACETS ] : [ ] as $group_content_selected_name => $group_content_selected ) {


			if ( isset( $group_content_selected['is_active'] ) ) {

				WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet.inc.php' ),
					array(
						'options_name'                 => $options_name,
						'facets_group_uuid'            => $group_uuid,
						'layouts_facets'               => $layouts_facets[ WPSOLR_Global::getExtensionSchemas()->get_field_type_definition( $group_schema, $group_content_selected_name )->get_id() ],
						'layouts_filters'              => $layouts_filters,
						'facet_name'                   => $group_content_selected_name,
						'is_numeric'                   => WPSOLR_Global::getExtensionSchemas()->get_field_type_definition( $group_schema, $group_content_selected_name )->get_is_numeric(),
						'facet'                        => $group_content_selected,
						'group_content_selected_class' => 'group_content_selected',
						'image_plus_display'           => 'none',
						'image_minus_display'          => 'inline',
						'image_plus'                   => $image_plus,
						'image_minus'                  => $image_minus

					) );
			}
		}

		foreach ( array_merge( $field_custom_fields, $field_taxonomies ) as $field_name => $field ) {

			$field_name = strtolower( $field_name );

			if ( ! ( isset( $groups[ $group_uuid ][ WPSOLR_Option::OPTION_FACETS_FACETS ] )
			         && isset( $groups[ $group_uuid ][ WPSOLR_Option::OPTION_FACETS_FACETS ][ $field_name ] )
			         && isset( $groups[ $group_uuid ][ WPSOLR_Option::OPTION_FACETS_FACETS ][ $field_name ]['is_active'] )
			)
			) {

				WPSOLR_Extensions::require_with( WPSOLR_Extensions::get_option_template_file( WPSOLR_Extensions::OPTION_FACETS, 'facet.inc.php' ),
					array(
						'options_name'                 => $options_name,
						'facets_group_uuid'            => $group_uuid,
						'layouts_facets'               => $layouts_facets[ WPSOLR_Global::getExtensionSchemas()->get_field_type_definition( $group_schema, $field_name )->get_id() ],
						'layouts_filters'              => $layouts_filters,
						'facet_name'                   => $field_name,
						'is_numeric'                   => WPSOLR_Global::getExtensionSchemas()->get_field_type_definition( $group_schema, $field_name )->get_is_numeric(),
						'facet'                        => $field,
						'group_content_selected_class' => 'group_content_not_selected',
						'image_plus_display'           => 'inline',
						'image_minus_display'          => 'none',
						'image_plus'                   => $image_plus,
						'image_minus'                  => $image_minus
					) );

			}
		}
		?>

	</ul>

<?php } ?>




