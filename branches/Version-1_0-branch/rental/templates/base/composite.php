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
		<button onclick="javascript:window.location.href ='<?php echo $cancel_link;?>;'">&laquo;&nbsp;<?php echo lang('composite_back');?></button><br/>
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
						<label for="name"><?php echo lang('address');if (!$editable && $composite->has_custom_address()){echo " (<font style='color:red;'>" . lang('custom_address') . "</font>)";} ?></label>
					</dt>
					<dd>
						<?php
							if (!$editable && $composite->has_custom_address()) {
								// In view mode the custom address should be displayed if it's filled in
								echo $composite->get_custom_address_1();
								if ($composite->get_custom_address_2()) {
									echo ',<br/>' . $composite->get_custom_address_2();
								}
								if ($composite->get_custom_house_number()) {
									echo ',<br/>' . $composite->get_custom_house_number();
								}
								if ($composite->get_custom_postcode()) {
									echo ',<br />' . $composite->get_custom_postcode() . ' ' . $composite->get_custom_place();
								}
							}
							else if (!$editable){
								//no custom address
								foreach($composite->get_units() as $unit){
									echo $unit->get_location()->get_address_1();
								}
							}
						?>
					</dd>
					<dt>
						<!-- Furnish status  -->
						<label for="furnish_type_id"><?php echo lang('furnish_type') ?></label>
							<?php
								$furnish_types_arr = $composite->get_furnish_types();
								$cur_furnish_type_id = $composite->get_furnish_type_id();

							// Edit composite
							if ($editable) { ?>
								<select name="furnish_type_id">
								<?php 
									foreach($furnish_types_arr as $id => $title){
										if($cur_furnish_type_id == $id)
											echo "<option selected='true' value='$id'>" . $title . "</option>";
										else 
											echo "<option value='$id'>" . $title . "</option>";
									}
								?>
								</select>			
							<?php 
							// View composite
							}else{ ?>
								<input type="text" id="furnish_type_id" value='<?php echo $furnish_types_arr[$cur_furnish_type_id]; ?>' disabled="disabled" />
							<?php } ?>
					</dt>	
					<?php if ($editable) { // Only show custom address fields if we're in edit mode ?>
					<dt>
						<label for="has_custom_address"><?php echo lang('has_custom_address') ?></label>
					</dt>
					<dd>
						<input type="checkbox" name="has_custom_address" id="has_custom_address"<?php echo $composite->has_custom_address() ? ' checked="checked"' : '' ?>/>
					</dd>
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
					<dd><?php echo $composite->get_area_gros()?>&nbsp;<?php echo isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm'; ?></dd>
					<dt><?php echo lang('area_net') ?></dt>
					<dd><?php echo $composite->get_area_net()?>&nbsp;<?php echo isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm'; ?></dd>
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
					array("key" => "contract_notification_status", "label" => lang('notification_status'))
				);
				unset($related);
				include('contract_list_partial.php');
			?>
		</div>
		
		<?php  } ?>
	</div>
</div>
