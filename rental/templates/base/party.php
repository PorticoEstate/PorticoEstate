<?php
	//include common logic for all templates
	include("common.php");
	phpgwapi_yui::load_widget('tabview');
	phpgwapi_yui::tabview_setup('party_edit_tabview');
?>

<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<div class="identifier-header">
	<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/custom/contact.png" /><?php echo lang('party') ?></h1>
	<div>
		<button onclick="javascript: window.location.href='<?php echo $cancel_link;?>;'">&laquo;&nbsp;<?php echo lang('party_back');?></button><br/>
		<label><?php echo lang('name'); ?></label>
		 <?php if($party->get_name()){ echo $party->get_name(); } else { echo lang('no_value'); }?>
	</div>
</div>
<div id="party_edit_tabview" class="yui-navset">
	<ul class="yui-nav">
		<li class="selected"><a href="#details_party"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/custom/contact.png" alt="icon" /> <?php echo lang('details') ?></em></a></li>
		
		<?php  if($party->get_id() > 0) { ?>
		
		<li><a href="#contracts_party"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" /> <?php echo lang('contracts') ?></em></a></li>
		<li <?php echo (phpgw::get_var('tab') == 'documents') ?  'class="selected"' : ""?>><a href="#documents_party"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/apps/system-file-manager.png" alt="icon" /> <?php echo lang('documents') ?></em></a></li>

		<?php } ?>
	</ul>

	<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value="<?php if($party->get_id()){ echo $party->get_id(); } else { echo '0'; }  ?>"/>
				<dl class="proplist-col">
					<dt>
						<?php if($party->get_identifier() || $editable) { ?>
						<label for="identifier"><?php echo lang('identifier') ?></label>
						<?php } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="identifier" id="identifier" value="<?php echo $party->get_identifier() ?>" />
						<?php
						}
						else
						{
							echo $party->get_identifier();
						}
						?>
					</dd>
					
					<dt>
						<?php if($party->get_first_name() || $editable) { ?>
						<label for="firstname"><?php echo lang('firstname') ?></label>
						<?php } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="firstname" id="firstname" value="<?php echo $party->get_first_name() ?>" />
						<?php
						}
						else
						{
							echo $party->get_first_name();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_last_name() || $editable) { ?>
						<label for="lastname"><?php echo lang('lastname') ?></label>
						<?php  } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="lastname" id="lastname" value="<?php echo $party->get_last_name() ?>" />
						<?php
						}
						else
						{
							echo $party->get_last_name();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_title() || $editable) { ?>
						<label for="title"><?php echo lang('job_title') ?></label>
						<?php  } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="title" id="title" value="<?php echo $party->get_title() ?>" />
						<?php
						}
						else
						{
							echo $party->get_title();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_company_name() || $editable) { ?>
						<label for="company_name"><?php echo lang('company') ?></label>
						<?php  } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="company_name" id="company_name" value="<?php echo $party->get_company_name() ?>" />
						<?php
						}
						else
						{
							echo $party->get_company_name();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_department() || $editable) { ?>
						<label for="department"><?php echo lang('department') ?></label>
						<?php } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="department" id="department" value="<?php echo $party->get_department() ?>" />
						<?php
						}
						else
						{
							echo $party->get_department();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_address_1() || $party->get_address_2() || $editable) { ?>
						<label for="address1"><?php echo lang('address') ?></label>
						<?php } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="address1" id="address1" value="<?php echo $party->get_address_1() ?>" />
							<br/>
							<input type="text" name="address2" id="address2" value="<?php echo $party->get_address_2() ?>" />
						<?php
						}
						else
						{
							echo $party->get_address_1();
							if($party->get_address_2())
							{
								echo "<br/>";
								echo $party->get_address_2();
							}
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_postal_code() || $party->get_place() || $editable) { ?>
						<label for="postal_code"><?php echo lang('postal_code_place') ?></label>
						<?php  } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="postal_code" id="postal_code" class="postcode" value="<?php echo $party->get_postal_code() ?>" />
							<input type="text" name="place" id="place" value="<?php echo $party->get_place() ?>" />
						<?php
						}
						else
						{
							echo $party->get_postal_code() . " ";
							echo $party->get_place();
						}
						?>
					</dd>
				</dl>
				<dl class="proplist-col">
					<dt>
						<?php if($party->get_phone() || $editable) { ?>
						<label for="phone"><?php echo lang('phone') ?></label>
						<?php } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="phone" id="phone" value="<?php echo $party->get_phone() ?>" />
						<?php
						}
						else
						{
							echo $party->get_phone();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_mobile_phone() || $editable) { ?>
						<label for="mobile_phone"><?php echo lang('mobile_phone') ?></label>
						<?php } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="mobile_phone" id="mobile_phone" value="<?php echo $party->get_mobile_phone() ?>" />
						<?php
						}
						else
						{
							echo $party->get_mobile_phone();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_fax() || $editable) { ?>
						<label for="fax"><?php echo lang('fax') ?></label>
						<?php  } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="fax" id="fax" value="<?php echo $party->get_fax() ?>" />
						<?php
						}
						else
						{
							echo $party->get_fax();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_email() || $editable) { ?>
						<label for="email"><?php echo lang('email') ?></label>
						<?php } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="email" id="email" value="<?php echo $party->get_email() ?>" />
						<?php
							$validator = CreateObject('phpgwapi.EmailAddressValidator');
							$email = $party->get_email();
							if($validator->check_email_address($email) && !$GLOBALS['phpgw']->accounts->exists($email))
							{
								?><br/><a href="?menuaction=rental.uiparty.create_user_based_on_email&id=<?php echo $party->get_id() ?>"><?php echo lang('create_user_based_on_email_link') ?></a> <?php	
							}
						}
						else
						{
							echo $party->get_email();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_url() || $editable) { ?>
						<label for="url"><?php echo lang('url') ?></label>
						<?php } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="url" id="url" value="<?php echo $party->get_url() ?>" />
						<?php
						}
						else
						{
							echo $party->get_url();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_account_number	() || $editable) { ?>
						<label for="account_number"><?php echo lang('account_number') ?></label>
						<?php } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="account_number" id="account_number" value="<?php echo $party->get_account_number() ?>" />
						<?php
						}
						else
						{
							echo $party->get_account_number();
						}
						?>
					</dd>
					<?php if($editable) {?>
						<dt>
							<label for="is_inactive"><?php echo lang('inactive_party') ?></label>
						</dt>
						<dd>
							<input type="checkbox" name="is_inactive" id="is_inactive" <?php if($party->is_inactive()) { echo "checked='checked'";} ?>/>
						</dd>
					<?php 
					}else{ 
					?>
						<dt><label><?php if($party->is_inactive()){?><font style="color: red;"><?php echo lang('inactive_party');?></font><?php }else{ ?><font style="color: green;"><?php echo lang('active_party');?></font><?php } ?></label></dt>
						<dd>&nbsp;</dd>
					<?php }?>
					<!--<dt>
						<?php if($party->get_location_id() || $editable) {?>
						<label for="location_id"><?php echo lang('party_location') ?></label>
						<?php } ?>
					</dt>
					 <dd>
						<?php
						if ($editable)
						{
						?>
							<select id="location_id" name="location_id">
								<option value=""><?php echo lang('no_party_location') ?></option>
								<?php 
									$city_counsil_departments =  array_reverse(location_hierarchy::get_hierarchy());
									$party_location_id = $party->get_location_id();
									foreach($city_counsil_departments as $department)
									{
										$department_level_id = $department->get_level_identifier();
										$department_name = "{$department_level_id} - ". $department->get_description();
										
										echo "<optgroup label='{$department_name}'>";
										$units = $department->get_result_units();
										foreach($units as $unit)
										{
											$unit_location_id = $unit->get_location_id();
											$unit_level_id = $unit->get_level_identifier();
											$unit_name = "{$unit_level_id} - ". $unit->get_description();
											
											if($party_location_id == $unit_location_id)
											{
												echo "<option value='{$unit_location_id}' selected=selected >{$unit_name}</option>";
											}
											else
											{
												echo "<option value='{$unit->get_location_id()}'>{$unit_name}</option>";
											}
										}
										echo '</optgroup>';
									}
									
								?>
							</select>
						<?php
						}
						else
						{
							$loc_id = $party->get_location_id();
							if(isset($loc_id) && $loc_id > 0)
							{
								echo location_hierarchy::get_name_of_location($loc_id);
							}
							/*else
							{
								echo lang('no_party_location');
							}*/
						}
						?>
					</dd> -->
					<?php
						if($use_fellesdata)
						{
							include PHPGW_SERVER_ROOT . "/rental/inc/plugins/fellesdata/party.edit.php";
						}
					?>
					
				</dl>
		        <dl class="proplist-col">
					<dt>
						<?php if($party->get_comment() || $editable) { ?>
						<label for="comment"><?php echo lang('comment') ?></label>
						<?php } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
		                    ?>
		                    <textarea cols="40" rows="7" name="comment" id="comment"><?php echo $party->get_comment(); ?></textarea>
		                    <?php
						}
						else
						{
							echo $party->get_comment();
						}
						?>
					</dd>
					<dt>
						<?php if($party->get_unit_leader() || $editable) { ?>
						<label for="unit_leader"><?php echo lang('unit_leader') ?></label>
						<?php  } ?>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
						?>
							<input type="text" name="unit_leader" id="unit_leader" value="<?php echo $party->get_unit_leader() ?>" />
						<?php
						}
						else
						{
							echo $party->get_unit_leader();
						}
						?>
					</dd>
					</dl>
				<div class="form-buttons">
					<span id="unit_errorMsg">Du må velge organisasjonsenhet før du kan synkronisere</span>
					<?php
						if ($editable) {
							echo '<input type="submit" name="save_party" value="' . lang('save') . '"/>';
						}
							
						if ($use_fellesdata) {
							echo '<input type="button" id="fetchSyncData" name="synchronize" value="' . lang('get_sync_data') . '"/>';
						}
					?>
				</div>
				
			</form>
			
		</div>
		
		<?php  if($party->get_id() > 0) { ?>
		
		<div id="contracts">
			<?php
			$list_form = true;
			$list_id = 'contracts_part';
			$url_add_on = "&amp;type=contracts_part&amp;party_id=".$party->get_id();
			$extra_cols = array(
				array("key" => "type", "label" => lang('title'), "index" => 3),
				array("key" => "composite", "label" => lang('composite'), "index" => 4),
				array("key" => "contract_notification_status", "label" => lang('notification_status'))
			);
			include('contract_list_partial.php');
			?>
		</div>
		<div id="documents">
			<?php
				$list_form = true;
				$list_id = 'documents_for_party';
				$url_add_on = "&amp;type={$list_id}&amp;party_id={$party->get_id()}";
				$upload_url_add_on = "&amp;party_id={$party->get_id()}";
				unset($extra_cols);
				unset($editors);
				unset($related);
				include('document_list_partial.php'); 
			?>
		</div>
		
		<?php } ?>
	</div>
</div>

