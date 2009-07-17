<?php 
	include("common.php");
	phpgwapi_yui::load_widget('tabview');        
  phpgwapi_yui::tabview_setup('composite_tabview');
?>

<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/actions/go-home.png" /> <?= lang('rental_common_showing_composite') ?> <em><?= $composite->get_name() ?></em></h1>

<div id="composite_tabview" class="yui-navset">
	<ul class="yui-nav">
		<li class="selected"><a href="#rental_rc_details"><em><img src="<?= RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-home.png" alt="icon" /> <?= lang('rental_rc_details') ?></em></a></li>
		<li><a href="#rental_rc_elements"><em><img src="<?= RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-drawing-template.png" alt="icon" /> <?= lang('rental_rc_elements') ?></em></a></li>
		<li><a href="#rental_rc_contracts"><em><img src="<?= RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" />   <?= lang('rental_rc_contracts') ?></em></a></li>
	</ul>
	
	<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<dl class="proplist-col">
					<dt>
						<label for="name"><?= lang('rental_rc_name') ?></label>
					</dt>
					<dd>
						<?php
							if ($editable) {
								echo '<input type="text" name="name" id="name" value="' . $composite->get_name() . '"/>';
							} else {
								echo $composite->get_name();
							}
						?>
					</dd>
					
					<dt>
						<label for="name"><?= lang('rental_rc_address') ?></label>
					</dt>
					<dd>
						<?php
							if (!$editable && $composite->has_custom_address()) {
								// In view mode the custom address should be displayed if it's filled in
								echo $composite->get_custom_address_1() . "<br />";
								
								if ($composite->get_custom_address_2()) {
									echo ', ' . $composite->get_custom_address_2();
								}
								
								if ($composite->get_custom_house_number()) {
									echo ' ' . $composite->get_custom_house_number();
								}
								
								if ($composite->get_custom_postcode()) {
									echo '<br />' . $composite->get_custom_postcode() . ' ' . $composite->get_custom_place();
								}
							} else {
								// Always show the original address if in edit mode, or if the
								// custom address isn't filled in
								echo $composite->get_address_1();
								
								if ($composite->get_address_2()) {
									echo ', ' . $composite->get_address_2();
								}
								
								if ($composite->get_house_number()) {
									echo ' ' . $composite->get_house_number();
								}
								
								if ($composite->get_postcode()) {
									echo '<br />' . $composite->get_postcode() . ' ' . $composite->get_place();
								}
							}
						?>
					</dd>
					
					<?php if ($editable) { // Only show custom address fields if we're in edit mode ?>
					<dt>
						<label for="address_1"><?= lang('rental_rc_overridden_address') ?></label>
						/ <label for="house_number"><?= lang('rental_rc_house_number') ?></label>
					</dt>
					<dd>
						<input type="text" name="address_1" id="address_1" value="<?= $composite->get_custom_address_1() ?>" />
						<input type="text" name="house_number" id="house_number" value="<?= $composite->get_custom_house_number() ?>" />
					</dd>
					<dt>
						<label for="postcode"><?= lang('rental_rc_post_code') ?></label>
						/ <label for="place"><?= lang('rental_rc_post_place') ?></label>
					</dt>
					<dd>
						<input type="text" name="postcode" id="postcode" class="postcode" value="<?= $composite->get_custom_postcode() ?>"/>
						<input type="text" name="place" id="place" value="<?= $composite->get_custom_place() ?>"/>
					</dd>
					<?php } // if ($editable) ?>
				</dl>
				
				<dl class="proplist-col">
					<dt><?= lang('rental_rc_serial') ?></dt>
					<dd><?= $composite->get_id() ?></dd>
					<dt><?= lang('rental_rc_area_gros') ?>></dt>
					<dd><?= $composite->get_area_gros() ?> m<sup>2</sup></dd>
					<dt><?= lang('rental_rc_area_net') ?></dt>
					<dd><?= $composite->get_area_net() ?> m<sup>2</sup></dd>
					<dt><?= lang('rental_rc_propertyident') ?></dt>
					<dd><?= $composite->get_gab_id() ?></dd>
					
					<dt>
						<label for="is_active"><?= lang('rental_rc_available?') ?></label>
					</dt>
					<dd>
						<input type="checkbox" name="is_active" id="is_active"<?= $composite->is_active() ? ' checked="checked"' : '' ?> <?= !$editable ? ' disabled="disabled"' : '' ?>/>
					</dd>
				</dl>
				
				<dl class="rental-description-edit">
					<dt>
						<label for="description"><?= lang('rental_rc_description') ?></label>
					</dt>
					<dd>
						<textarea name="description" id="description" rows="10" cols="50" <?= !$editable ? ' disabled="disabled"' : '' ?>>
							<?= $composite->get_description() ?>
						</textarea>
					</dd>
				</dl>
				
				<div class="form-buttons">
					<?php
						if ($editable) {
							echo '<input type="submit" name="save" value="' . lang('rental_rc_save') . '"/>';
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_rc_cancel') . '</a>';
						} else {
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_rc_back') . '</a>';
						}
					?>
				</div>
			</form>
		</div>
		<div id="elements">
			<?php if ($editable) { ?>
				<h3><?= lang('rental_rc_added_areas') ?></h3>
				<div id="added-areas-datatable-container" class="datatable_container"></div>
			<?php } ?>
			<h3><?= lang('rental_rc_add_area') ?></h3>
			<form id="available_areas_form" method="GET">
				<fieldset>
					<!-- Filters -->
					<legend><?= lang('rental_rc_toolbar_filters') ?></legend>
					<label for="ctrl_toggle_level"><?= lang('rental_rc_level') ?></label>
					<select name="level" id="ctrl_toggle_level">
						<option value="1"><?= lang('rental_rc_property') ?></option>
						<option value="2" default=""><?= lang('rental_rc_building') ?></option>
						<option value="3"><?= lang('rental_rc_floor') ?></option>
						<option value="4"><?= lang('rental_rc_level') ?></option>
						<option value="5"><?= lang('rental_rc_section') ?></option>
					</select>
					
					<label class="toolbar_element_label" for="calendarPeriodFrom"><?= lang('rental_rc_available_at') ?></label>
					<input type="text" name="available_date" id="available_date" size="10"/>
					<input type="hidden" name="available_date_hidden" id="available_date_hidden"/>
					<div id="calendarPeriodFrom">
					</div>
					
					<input type="submit" id="ctrl_search_button" value="<?= lang('rental_rc_search') ?>" />
					<input type="button" id="ctrl_reset_button" value="<?= lang('rental_reset') ?>" />
				</fieldset>
				<div id="available-areas-datatable-container" class="datatable_container"></div>
			</form>
		</div>
		<div id="contracts">
			<form id="contracts_form" method="GET">
				<fieldset>
					<!-- Filters -->
					<legend><?= lang('rental_rc_toolbar_filters') ?></legend>
					<label class="toolbar_element_label" for="ctrl_toggle_contract_status"><?= lang('rental_rc_contract_status') ?></label>
						<select name="contract_status" id="ctrl_toggle_contract_status">
							<option value="active" default=""><?= lang('rental_rc_active') ?></option>
							<option value="not_started"><?= lang('rental_rc_not_started') ?></option>
							<option value="both"><?= lang('rental_rc_ended') ?></option>
						</select>
					
					<input type="submit" id="ctrl_search_button" value="<?= lang('rental_rc_search') ?>" />
					<input type="button" id="ctrl_reset_button" value="<?= lang('rental_reset') ?>" />
				</fieldset>
				<div id="contracts-container" class="datatable_container"></div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript">
	YAHOO.util.Event.onDOMReady(function() {

		//initCalendar('available_date', 'calendarPeriodFrom', 'cal1', 'Velg dato');
		
		
		//Columns for added areas datatable
		var addedAreasColumnDefs = [{
			key: "location_code",
			label: "<?= lang('rental_rc_id') ?>",
		  sortable: true
		},
		{
			key: "loc1_name",
			label: "<?= lang('rental_rc_property') ?>",
		  sortable: true
		},
		{
			key: "loc2_name",
			label: "<?= lang('rental_rc_building') ?>",
		  sortable: false
		},
		{
			key: "loc3_name",
			label: "<?= lang('rental_rc_section') ?>",
		  sortable: false
		},
		{
			key: "address",
			label: "<?= lang('rental_rc_address') ?>",
		  sortable: false
		},
		{
			key: "area_gros",
			label: "<?= lang('rental_rc_area_gros') ?>",
		  sortable: false
		},
		{
			key: "area_net",
			label: "<?= lang('rental_rc_area_net') ?>",
		  sortable: false
		},
		{
			key: "actions",
			hidden: true
		}
		];

		// Initiating the data source
		setDataSource(
				'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=included_areas&amp;id=<?= $composite->get_id() ?>',
				addedAreasColumnDefs,
				'added_areas_form',
				[],
				'added-areas-datatable-container',
				1,
				['<?= lang('rental_cm_remove') ?>'],
				['remove_unit']	
		);

		//Columns for available areas datatable
		var availableAreasColumnDefs = [{
			key: "location_code",
			label: "<?= lang('rental_rc_id') ?>",
		  sortable: true
		},
		{
			key: "loc1_name",
			label: "<?= lang('rental_rc_property') ?>",
		  sortable: true
		},
		{
			key: "loc2_name",
			label: "<?= lang('rental_rc_building') ?>",
		  sortable: false
		},
		{
			key: "loc3_name",
			label: "<?= lang('rental_rc_section') ?>",
		  sortable: false
		},
		{
			key: "address",
			label: "<?= lang('rental_rc_address') ?>",
		  sortable: false
		},
		{
			key: "area_gros",
			label: "<?= lang('rental_rc_area_gros') ?>",
		  sortable: false
		},
		{
			key: "area_net",
			label: "<?= lang('rental_rc_area_net') ?>",
		  sortable: false
		},
		{
			key: "occupied",
			label: "<?= lang('rental_rc_availibility') ?>",
		  sortable: false
		},
		{
			key: "actions",
			hidden: true
		}
		];
		
		// Initiating the data source
		setDataSource(
				'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=available_areas&amp;id=<?= $composite->get_id() ?>',
				availableAreasColumnDefs,
				'available_areas_form',
				['crtl_toggle_level'],
				'available-areas-datatable-container',
				2,
				['<?= lang('rental_cm_add') ?>'],
				['add_unit']	
		);

		
		// Columns for contracts table 
		var contractsColumnDefs = [{
			key: "id",
			label: "<?= lang('rental_rc_id') ?>",
		  sortable: true
		},
		{
			key: "date_start",
			label: "<?= lang('rental_rc_date_start') ?>",
		  sortable: true
		},
		{
			key: "date_end",
			label: "<?= lang('rental_rc_date_end') ?>",
		  sortable: false
		},
		{
			key: "tenant",
			label: "<?= lang('rental_common_party') ?>",
		  sortable: false
		},
		{
			key: "actions",
			hidden: true
		}
		];
		
		// Initiating the data source
		setDataSource(
				'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=contracts&amp;id=<?= $composite->get_id() ?>',
				availableAreasColumnDefs,
				'contracts_form',
				['ctrl_toggle_contract_status'],
				'contracts-container',
				3,
				['<?= lang('rental_cm_show') ?>', '<?= lang('rental_cm_edit') ?>'],
				['view_contract', 'edit_contract']	
		);
	});	
</script>