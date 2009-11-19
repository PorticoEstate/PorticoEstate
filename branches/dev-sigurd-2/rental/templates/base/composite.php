<?php
	include("common.php");
	phpgwapi_yui::load_widget('tabview');
	phpgwapi_yui::tabview_setup('composite_tabview');
	$config	= CreateObject('phpgwapi.config','rental');
	$config->read();
?>
<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<div class="identifier-header">
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/go-home.png" /> <?php echo lang('showing_composite') ?></h1>
	<div>
		<label><?php echo lang('name') ?> </label><?php echo $composite->get_name() ?>
	</div>
</div>

<div id="composite_tabview" class="yui-navset">
	<ul class="yui-nav">
		<li class="selected"><a href="#details"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-home.png" alt="icon" /> <?php echo lang('details') ?></em></a></li>
		
		<?php if($composite->get_id() > 0) { ?>
		
		<li><a href="#elements"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-drawing-template.png" alt="icon" /> <?php echo lang('units') ?></em></a></li>
		<li><a href="#contracts"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" />   <?php echo lang('contracts') ?></em></a></li>

		<?php } ?>
	</ul>

	<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value="<?php echo $composite->get_id() ?>"/>
				<dl class="proplist-col">
					<dt>
						<label for="name"><?php echo lang('name') ?></label>
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
						<label for="name"><?php echo lang('address') ?></label>
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
							}
						?>
					</dd>

					<?php if ($editable) { // Only show custom address fields if we're in edit mode ?>
					<dt>
						<label for="address_1"><?php echo lang('overridden_address') ?></label> / <label for="house_number"><?php echo lang('house_number') ?></label>
					</dt>
					<dd>
						<input type="text" name="address_1" id="address_1" value="<?php echo $composite->get_custom_address_1() ?>" />
						<input type="text" name="house_number" id="house_number" value="<?php echo $composite->get_custom_house_number() ?>" /><br/>
						<input type="text" name="address_2" id="address_2" value="<?php echo $composite->get_custom_address_2() ?>" />
					</dd>
					<dt>
						<label for="postcode"><?php echo lang('post_code') ?></label>
						/ <label for="place"><?php echo lang('post_place') ?></label>
					</dt>
					<dd>
						<input type="text" name="postcode" id="postcode" class="postcode" value="<?php echo $composite->get_custom_postcode() ?>"/>
						<input type="text" name="place" id="place" value="<?php echo $composite->get_custom_place() ?>"/>
					</dd>
					<?php } // if ($editable) ?>
				</dl>

				<dl class="proplist-col">
					<dt><?php echo lang('area_gros') ?></dt>
					<dd><?php echo $composite->get_area_gros().' '.isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm'; ?></dd>
					<dt><?php echo lang('area_net') ?></dt>
					<dd><?php echo $composite->get_area_net().' '.isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm'; ?></dd>
					<dt><label for="rented_area"><?php echo lang('rented_area') ?></label></dt>
					<dd>
						<?php
							if ($editable) {?>
								<input type="text" name="rented_area" id="rented_area" value="<?php echo $composite->get_rented_area() ?>"/>&nbsp;<?php echo isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm';?>
							<?php } else {?>
								<?php echo $composite->get_rented_area()?>&nbsp;<?php echo isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm';?>
							<?php }
						?>
					</dd>
					<dt>
						<label for="is_active"><?php echo lang('available?') ?></label>
					</dt>
					<dd>
						<input type="checkbox" name="is_active" id="is_active"<?php echo $composite->is_active() ? ' checked="checked"' : '' ?> <?php echo !$editable ? ' disabled="disabled"' : '' ?>/>
					</dd>
				</dl>

				<dl class="rental-description-edit">
					<dt>
						<label for="description"><?php echo lang('description') ?></label>
					</dt>
					<dd>
						<textarea name="description" id="description" rows="10" cols="50" <?php echo !$editable ? ' disabled="disabled"' : '' ?>><?php echo $composite->get_description() ?></textarea>
					</dd>
				</dl>

				<div class="form-buttons">
					<?php
						if ($editable) {
							echo '<input type="submit" name="save_composite" value="' . lang('save') . '"/>';
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('cancel') . '</a>';
						} else {
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('back') . '</a>';
						}
					?>
				</div>
			</form>
		</div>

		<?php if($composite->get_id() > 0) { ?>

		<div id="elements">
			<h3><?php echo lang('included_units') ?></h3>
			<?php 
				$list_form = false; 
				$list_id = 'included_units';
				$url_add_on = '&amp;composite_id='.$composite->get_id();
				unset($extra_cols);
				include('unit_list_partial.php');

				
            if ($editable) { 
			    echo '<h3>'.lang('all_locations').'</h3>';
				$list_form = true; 
				$list_id = 'property_uilocations';
				$url_add_on = '&amp;composite_id='.$composite->get_id();
				unset($extra_cols);
				$related = array('included_units');
				include('property_location_partial.php');
			}
			?>
		</div>
		<div id="contracts">
			<?php 
				$list_form = true; 
				$list_id = 'contracts_for_composite';
				$url_add_on = '&amp;type='.$list_id.'&amp;composite_id='.$composite->get_id();
				$editable = false;
				$extra_cols = array(
					array("key" => "type", "label" => lang('title'), "index" => 3),
					array("key" => "party", "label" => lang('party'), "index" => 4),
					array("key" => "old_contract_id", "label" => lang('old_contract_id'), "index" => 5)
				);
				unset($related);
				include('contract_list_partial.php');
			?>
		</div>
		
		<?php  } ?>
	</div>
</div>
