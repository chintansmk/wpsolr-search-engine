<?php
use wpsolr\extensions\components\WPSOLR_Options_Components;
use wpsolr\extensions\layouts\WPSOLR_Options_Layouts;
use wpsolr\ui\WPSOLR_UI;

$new_component_uuid = key( $new_component );

$current_component_option_name = sprintf( "%s[%s]", $options_name, $component_type );
?>

<style>

	#dialog label, #dialog input {
		display: block;
	}

	#dialog label {
		margin-top: 0.5em;
	}

	#dialog input, #dialog textarea {
		width: 95%;
	}

	.tabs {
		margin-top: 1em;
	}

	.tabs li .ui-icon-close {
		float: left;
		margin: 0.4em 0.2em 0 0;
		cursor: pointer;
	}

	#add_tab {
		cursor: pointer;
	}
</style>

<script>
	jQuery(document).ready(function () {

		// Create tabs and activate the group tab selected in the url
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

		tabs.delegate("span.ui-icon-close", "click", function () {
			var panelId = jQuery(this).closest("li").remove().attr("aria-controls");
			jQuery("#" + panelId).remove();
			tabs.tabs("refresh");
		});

		// Group facets accordeon
		jQuery(".sortable").sortable();
		jQuery(".sortable").accordion({active: false, collapsible: true, heightStyle: "content"});

	});
</script>


<div class="tabs">
	<ul>
		<?php
		foreach ( array_merge( $components, $new_component ) as $component_uuid => $component ) { ?>
			<li>
				<a href="#<?php echo $component_uuid; ?>"><?php echo $component[ WPSOLR_UI::FORM_FIELD_TITLE ]; ?></a>
				<?php if ( $new_component_uuid !== $component_uuid ) { ?>
					<span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
	<?php foreach ( array_merge( $components, $new_component ) as $component_uuid => $component ) { ?>
		<div id="<?php echo $component_uuid; ?>">

			<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_COMPONENT_TYPE ] ) ? $component[ WPSOLR_UI::FORM_FIELD_COMPONENT_TYPE ] : ''; ?>
			<input type="hidden"
			       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_COMPONENT_TYPE; ?>]"
			       value="<?php echo esc_attr( $value ); ?>"/>

			<?php if ( $new_component_uuid != $component_uuid ) { ?>
				<div class="wdm_row">
					<div class='col_left'>Shortcode<br/>You can also select this component in a widget.)
					</div>
					<div class='col_right'>
						<?php echo $component[ WPSOLR_Options_Components::SHORTCODE_FIELD_CODE ]; ?>
					</div>
					<div class="clear"></div>
				</div>
			<?php } ?>

			<div class="wdm_row">
				<div class='col_left'>Title<br/>
					Translated as string
				</div>
				<div class='col_right'>
					<input type="text" style="width: 40%" id="<?php echo esc_attr( $component_uuid ); ?>_component_name"
					       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_TITLE; ?>]"
					       value="<?php echo $new_component_uuid == $component_uuid ? '' : esc_attr( $component[ WPSOLR_UI::FORM_FIELD_TITLE ] ); ?>"/>

					<?php if ( $new_component_uuid != $component_uuid ) { ?>
						<input type="checkbox"
						       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][is_to_be_cloned]"
						       value="1"/> Clone this component when saving
					<?php } ?>

				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>
					Page where to display results:
				</div>
				<div class='col_right'>
					<?php
					$value = isset( $component[ WPSOLR_UI::FORM_FIELD_SEARCH_METHOD ] ) ? $component[ WPSOLR_UI::FORM_FIELD_SEARCH_METHOD ] : WPSOLR_UI::FORM_FIELD_SEARCH_METHOD_VALUE_USE_CUSTOM_PAGE; ?>

					<?php
					$options = array(
						array(
							'code'  => WPSOLR_UI::FORM_FIELD_SEARCH_METHOD_VALUE_USE_CUSTOM_PAGE,
							'label' => 'Page: '
						),
						array(
							'code'  => WPSOLR_UI::FORM_FIELD_SEARCH_METHOD_VALUE_USE_CUSTOM_CATEGORY,
							'label' => 'Category: '
						),
						array(
							'code'  => WPSOLR_UI::FORM_FIELD_SEARCH_METHOD_VALUE_NO_AJAX,
							'label' => 'Current page. No Ajax.'
						),
						array(
							'code'  => WPSOLR_UI::FORM_FIELD_SEARCH_METHOD_VALUE_AJAX,
							'label' => 'Current page. Ajax.'
						),
						array(
							'code'  => WPSOLR_UI::FORM_FIELD_SEARCH_METHOD_VALUE_AJAX_WITH_PARAMETERS,
							'label' => 'Current page. Ajax. Show parameters in url'
						)
					);
					foreach ( $options as $option ) {
						?>
						<input type="radio"
						       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_SEARCH_METHOD; ?>]"
						       value="<?php echo $option['code'] ?>" <?php checked( $option['code'], $value ); ?> /> <?php echo $option['label']; ?>
						<?php
						switch ( $option['code'] ) {
							case WPSOLR_UI::FORM_FIELD_SEARCH_METHOD_VALUE_USE_CUSTOM_PAGE:
								$args = array(
									'selected'          => isset( $component[ WPSOLR_UI::FORM_FIELD_RESULTS_PAGE ] ) ? $component[ WPSOLR_UI::FORM_FIELD_RESULTS_PAGE ] : '',
									'echo'              => 1,
									'name'              => sprintf( "%s[%s][%s]", $current_component_option_name, $component_uuid, WPSOLR_UI::FORM_FIELD_RESULTS_PAGE ),
									'show_option_none'  => 'My theme search page',
									'option_none_value' => ''
								);
								wp_dropdown_pages( $args );
								break;

							case WPSOLR_UI::FORM_FIELD_SEARCH_METHOD_VALUE_USE_CUSTOM_CATEGORY:
								$args = array(
									'selected'          => isset( $component[ WPSOLR_UI::FORM_FIELD_RESULTS_CATEGORY ] ) ? $component[ WPSOLR_UI::FORM_FIELD_RESULTS_CATEGORY ] : '',
									'echo'              => 1,
									'name'              => sprintf( "%s[%s][%s]", $current_component_option_name, $component_uuid, WPSOLR_UI::FORM_FIELD_RESULTS_CATEGORY ),
									'show_option_none'  => '',
									'option_none_value' => ''
								);
								wp_dropdown_categories( $args );
								break;
						} ?>
						</p>
					<?php } ?>

				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Display in it's own Ajax call.<br/>
					By default, all components displays are refreshed in a single Ajax call.
				</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_IS_OWN_AJAX ] ); ?>
					<input type="checkbox"
					       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_IS_OWN_AJAX; ?>]"
						<?php checked( $value ); ?>
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Group</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] ) ? $component[ WPSOLR_UI::FORM_FIELD_GROUP_ID ] : ''; ?>
					<select
						name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_GROUP_ID; ?>]">
						<?php
						foreach ( $groups as $group_uuid => $group ) { ?>
							<option value="<?php echo esc_attr( $group_uuid ); ?>"
								<?php selected( $group_uuid, $value, true ); ?> ><?php echo $group[ WPSOLR_UI::FORM_FIELD_GROUP_NAME ]; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Layout</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] ) ? $component[ WPSOLR_UI::FORM_FIELD_LAYOUT_ID ] : ''; ?>
					<select
						name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_LAYOUT_ID; ?>]">
						<?php
						foreach ( $layouts as $layout_id => $layout ) { ?>
							<option value="<?php echo esc_attr( $layout_id ); ?>"
								<?php selected( $layout_id, $value, true ); ?> ><?php echo $layout[ WPSOLR_Options_Layouts::LAYOUT_FIELD_TEMPLATE_NAME ]; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Regexp to filter urls</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] ) ? $component[ WPSOLR_UI::FORM_FIELD_URL_REGEXP ] : ''; ?>
					<textarea style="width: 95%" rows="5"
					          name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_URL_REGEXP; ?>]"
					><?php echo esc_textarea( $value ); ?></textarea>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Show if empty</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY ] ); ?>
					<input type="checkbox"
					       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_IS_SHOW_WHEN_EMPTY; ?>]"
						<?php checked( $value ); ?>
					/>
				</div>
				<div class="clear"></div>
			</div>


			<div class="wdm_row">
				<div class='col_left'>Show title on front-end</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END ] ); ?>
					<input type="checkbox"
					       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_IS_SHOW_TITLE_ON_FRONT_END; ?>]"
						<?php checked( $value ); ?>
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Before title</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_BEFORE_TITLE ] ) ? $component[ WPSOLR_UI::FORM_FIELD_BEFORE_TITLE ] : ''; ?>
					<input type="text"
					       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_BEFORE_TITLE; ?>]"
					       value="<?php echo esc_html( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>After title</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_AFTER_TITLE ] ) ? $component[ WPSOLR_UI::FORM_FIELD_AFTER_TITLE ] : ''; ?>
					<input type="text"
					       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_AFTER_TITLE; ?>]"
					       value="<?php echo esc_html( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Before component</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_BEFORE_UI ] ) ? $component[ WPSOLR_UI::FORM_FIELD_BEFORE_UI ] : ''; ?>
					<input type="text"
					       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_BEFORE_UI; ?>]"
					       value="<?php echo esc_html( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>After component</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_AFTER_UI ] ) ? $component[ WPSOLR_UI::FORM_FIELD_AFTER_UI ] : ''; ?>
					<input type="text"
					       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_AFTER_UI; ?>]"
					       value="<?php echo esc_html( $value ); ?>"
					/>
				</div>
				<div class="clear"></div>
			</div>

			<div class="wdm_row">
				<div class='col_left'>Show javascript debug messages in console</div>
				<div class='col_right'>
					<?php $value = isset( $component[ WPSOLR_UI::FORM_FIELD_IS_DEBUG_JS ] ); ?>
					<input type="checkbox"
					       name="<?php echo $current_component_option_name; ?>[<?php echo $component_uuid; ?>][<?php echo WPSOLR_UI::FORM_FIELD_IS_DEBUG_JS; ?>]"
						<?php checked( $value ); ?>
					/>
				</div>
				<div class="clear"></div>
			</div>

		</div>

	<?php } ?>

</div>



