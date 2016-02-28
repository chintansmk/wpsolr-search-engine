<?php
use wpsolr\extensions\fields\WPSOLR_Options_Fields;
use wpsolr\extensions\queries\WPSOLR_Options_Query;
use wpsolr\utilities\WPSOLR_Global;

?>

<style>

	.tabs {
		margin-top: 1em;
	}

	.tabs li .ui-icon-close {
		float: left;
		margin: 0.4em 0.2em 0 0;
		cursor: pointer;
	}

</style>

<script>
	jQuery(document).ready(function () {

		// Create tabs and activate group tab selected in url
		var tabs = jQuery(".tabs").tabs({active: <?php echo $group_tab_selected; ?>});

		// tabs are sortable.
		tabs.find(".ui-tabs-nav").sortable({
			axis: "x",
			stop: function (event, ui) {
				var container = jQuery(this); // ul

				// Move the tab content with the tab nav
				var panel;
				jQuery(this).children().each(function () {
					panel = jQuery(jQuery(this).find('a').attr('href'));
					panel.insertAfter(container);
					container = panel; // div
				});

				// Active the dragged tab
				jQuery(".tabs").tabs("option", "active", event, ui);
				console.log(ui.item.index() + 1);

			}
		});

		// Remove button action
		tabs.delegate("span.ui-icon-close", "click", function () {
			var panelId = jQuery(this).closest("li").remove().attr("aria-controls");
			jQuery("#" + panelId).remove();
			tabs.tabs("refresh");
		});

		// Tabs are sortable
		jQuery(".sortable").sortable();
		jQuery(".sortable").accordion({active: false, collapsible: true, heightStyle: "content"});

		// Toggle default group
		jQuery('.is_default').click(function () {

			if (jQuery(this).prop("checked")) {

				// Uncheck all other checks
				jQuery('.is_default').not(this).prop("checked", false);
			}

		})
	});
</script>

<div class="tabs">
	<ul>
		<?php
		foreach ( $groups as $group_uuid => $group ) { ?>
			<li><a href="#<?php echo $group_uuid; ?>"><?php echo $group['name']; ?></a>
				<?php if ( $new_group_uuid !== $group_uuid ) { ?>
					<span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
	<?php foreach ( $groups as $group_uuid => $group ) {
		?>
		<div id="<?php echo $group_uuid; ?>">

			<div class="wdm_row">
				<div class='col_left'>Group name</div>
				<div class='col_right'>
					<input style="width:40%" type="text" id="<?php echo esc_attr( $group_uuid ); ?>_group_name"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, 'name' ); ?>"
					       value="<?php echo $new_group_uuid == $group_uuid ? '' : esc_attr( $group['name'] ); ?>"/>

					<?php if ( $new_group_uuid != $group_uuid ) { ?>
						<input type="checkbox"
						       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, 'is_to_be_cloned' ); ?>"
						       value="1"/> Clone this group when saving
					<?php } ?>

				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Fields</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Fields::FORM_FIELD_FIELD_ID ] ) ? $group[ WPSOLR_Options_Fields::FORM_FIELD_FIELD_ID ] : ''; ?>

					<?php if ( $new_group_uuid != $group_uuid ) { ?>
						<input type="hidden"
						       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Fields::FORM_FIELD_FIELD_ID ); ?>"
						       value="<?php echo $value; ?>"
						/>
						<?php
						$fields_name = ! empty( $fields[ $value ] ) ? $fields[ $value ][ WPSOLR_Options_Query::FORM_FIELD_NAME ] : '';
						echo $fields_name;
						?>
					<?php } else { ?>
						<select
							name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Fields::FORM_FIELD_FIELD_ID ); ?>">
							<?php
							foreach ( $fields as $field_id => $field ) { ?>
								<option value="<?php echo esc_attr( $field_id ); ?>"
									<?php selected( $field_id, $value, true ); ?> >
									<?php echo $field[ WPSOLR_Options_Query::FORM_FIELD_NAME ]; ?>
								</option>
							<?php } ?>
						</select>
					<?php } ?>

				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Index</div>
				<div class='col_right'>
					<?php
					$solr_indexes = WPSOLR_Global::getExtensionIndexes()->get_indexes_by_field_id( isset( $group[ WPSOLR_Options_Fields::FORM_FIELD_FIELD_ID ] ) ? $group[ WPSOLR_Options_Fields::FORM_FIELD_FIELD_ID ] : '' );
					//foreach ( WPSOLR_Global::getExtensionIndexes()->get_indexes() as $solr_index_indice => $solr_index ) {
					$value = isset( $group[ WPSOLR_Options_Fields::FORM_FIELD_SOLR_INDEX_ID ] ) ? $group[ WPSOLR_Options_Fields::FORM_FIELD_SOLR_INDEX_ID ] : '';
					?>

					<select
						name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Fields::FORM_FIELD_SOLR_INDEX_ID ); ?>">
						<?php
						foreach ( $solr_indexes as $solr_index_id => $solr_index ) { ?>
							<option value="<?php echo esc_attr( $solr_index_id ); ?>"
								<?php selected( $solr_index_id, $value, true ); ?> >
								<?php echo WPSOLR_Global::getExtensionIndexes()->get_index_name( $solr_index ); ?>
							</option>
						<?php } ?>
					</select>

					<p>Activate and configure wpsolr multi-language extensions (polylang, wpml) if you need to query a
						Solr index by language.</p>


				</div>
				<div class="clear"></div>
			</div>


			<div class="wdm_row">
				<div class='col_left'>Multi-language
				</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Query::FORM_FIELD_IS_MULTI_LANGUAGE ] ); ?>
					<input type='checkbox'
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Query::FORM_FIELD_IS_MULTI_LANGUAGE ); ?>"
					       value='1'
						<?php echo checked( $value ); ?>>
					<p>
						The query will use the index with fields '<?php echo $fields_name; ?>' that matches the
						language. If no index can be
						matched, the index selected above will be used.
					</p>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Used on your theme search page
				</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Query::FORM_FIELD_IS_DEFAULT ] ); ?>
					<input type='checkbox'
					       class="is_default"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Query::FORM_FIELD_IS_DEFAULT ); ?>"
					       value='1'
						<?php echo checked( $value ); ?>>
					<p>
						This query will be called to retrieve the content to be displayed on your standard theme search
						page, when no query is selected in the url.<br/>
						Leave empty if you do not want to use this plugin on your theme search page.
					</p>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Query default operator<br/>
				</div>
				<div class='col_right'>
					<?php
					$value = isset( $group[ WPSOLR_Options_Query::FORM_FIELD_DEFAULT_OPERATOR ] )
						? $group[ WPSOLR_Options_Query::FORM_FIELD_DEFAULT_OPERATOR ] : WPSOLR_Options_Query::QUERY_OPERATOR_AND; ?>

					<select
						name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Query::FORM_FIELD_DEFAULT_OPERATOR ); ?>">
						<option
							value='<?php echo WPSOLR_Options_Query::QUERY_OPERATOR_AND; ?>' <?php selected( WPSOLR_Options_Query::QUERY_OPERATOR_AND, $value, true ); ?>
						>
							<?php echo WPSOLR_Options_Query::QUERY_OPERATOR_AND; ?>
						</option>
						<option
							value='<?php echo WPSOLR_Options_Query::QUERY_OPERATOR_OR; ?>' <?php selected( WPSOLR_Options_Query::QUERY_OPERATOR_OR, $value, true ); ?>
						>
							<?php echo WPSOLR_Options_Query::QUERY_OPERATOR_OR; ?>
						</option>
					</select>

				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Display partial keyword matches in results
				</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Query::FORM_FIELD_IS_QUERY_PARTIAL_MATCH_BEGIN_WITH ] ); ?>
					<input type='checkbox'
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Query::FORM_FIELD_IS_QUERY_PARTIAL_MATCH_BEGIN_WITH ); ?>"
					       value='1'
						<?php echo checked( $value ); ?>>

					Warning: this will hurt both search performance and search accuracy !
					<p>This adds '*' to all keywords.
						For instance, 'search apache' will return results
						containing 'searching apachesolr'</p>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>No. of rows returned by the query</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Query::FORM_FIELD_MAX_NB_RESULTS_BY_PAGE ] ) ? $group[ WPSOLR_Options_Query::FORM_FIELD_MAX_NB_RESULTS_BY_PAGE ] : WPSOLR_Options_Query::FORM_FIELD_DEFAULT_MAX_NB_RESULTS_BY_PAGE; ?>
					<input type="text"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Query::FORM_FIELD_MAX_NB_RESULTS_BY_PAGE ); ?>"
					       value="<?php echo esc_attr( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Maximum size of each snippet text in results</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Query::FORM_FIELD_HIGHLIGHTING_FRAGSIZE ] ) ? $group[ WPSOLR_Options_Query::FORM_FIELD_HIGHLIGHTING_FRAGSIZE ] : WPSOLR_Options_Query::FORM_FIELD_DEFAULT_HIGHLIGHTING_FRAGSIZE; ?>
					<input type="text"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Query::FORM_FIELD_HIGHLIGHTING_FRAGSIZE ); ?>"
					       value="<?php echo esc_attr( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Filter</div>
				<div class='col_right'>
					<?php $value = isset( $group[ WPSOLR_Options_Query::FORM_FIELD_QUERY_FILTER ] ) ? $group[ WPSOLR_Options_Query::FORM_FIELD_QUERY_FILTER ] : ''; ?>
					<input type="text"
					       style="width: 95%;"
					       name="<?php echo sprintf( '%s[%s][%s]', $options_name, $group_uuid, WPSOLR_Options_Query::FORM_FIELD_QUERY_FILTER ); ?>"
					       value="<?php echo esc_attr( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

		</div>
	<?php } ?>

</div>


