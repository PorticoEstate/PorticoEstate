<?php
	include("common.php");
	phpgwapi_yui::load_widget('tabview');
	phpgwapi_yui::tabview_setup('contract_tabview');
	$config	= CreateObject('phpgwapi.config','rental');
	$config->read();
?>

<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_warning($contract->get_validation_warnings()) ?>
<?php echo rental_uicommon::get_page_warning($contract->get_consistency_warnings()) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<div class="identifier-header">
	<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/text-x-generic.png" /> <?php echo lang('showing_contract') ?></h1>
	<div style="float: left; width: 50%;">
		<button onclick="javascript:window.location.href ='<?php echo $cancel_link;?>;'">&laquo;&nbsp;<?php echo lang('contract_back');?></button><br/>
		<label><?php echo lang('contract_number') ?> </label>
		<?php if($contract->get_old_contract_id()){ 
			echo $contract->get_old_contract_id(); 
		} ?>
		<br/>
		<label><?php echo lang('parties') ?> </label>
		<?php
		 	echo $contract->get_party_name_as_list();
		 ?>
		 <br/>
		<label><?php echo lang('last_updated') ?> </label>
		<?php
			echo date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_last_updated());
		 ?>
		<label>&nbsp;&nbsp;<?php echo lang('name') ?></label>
		<?php
		 	echo $contract->get_last_edited_by();
		 ?>
		<br/>
		<label><?php echo lang('composite') ?> </label>
		<?php
		 	echo $contract->get_composite_name_as_list();
		 ?>		 
	</div>
	<div style="float: right; width: 50%;">
	 
	<?php 
	if($contract->get_id() > 0) {
		$list_form = false;
		$list_id = 'total_price';
		unset($related);
		$url_add_on = '&amp;contract_id='.$contract->get_id();
		unset($extra_cols);

		include('total_price_partial.php');
	}
	?>
		</div>
</div>

<div id="contract_tabview" class="yui-navset">
	<ul class="yui-nav">
		<?php if($contract->get_id() > 0) {?>

		<li><a href="#composites"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-home.png" alt="icon" /> <?php echo lang('composite') ?></em></a></li>
		<li><a href="#parties" ><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-address-book.png" alt="icon" /> <?php echo lang('parties') ?></em></a></li>
		<li><a href="#price"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png" alt="icon" />   <?php echo lang('price') ?></em></a></li>
		<?php }?>
		
		<li <?php echo (!isset($_POST['add_notification'])) ? 'class="selected"' : "" ?>><a href="#details"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" /> <?php echo lang('details') ?></em></a></li>
		
		<?php if($contract->get_id() > 0) {?>
		<li><a href="#invoice"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" />   <?php echo lang('invoice') ?></em></a></li>
		<li <?php echo (phpgw::get_var('tab') == 'documents') ?  'class="selected"' : ""?>><a href="#documents"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/apps/system-file-manager.png" alt="icon" /> <?php echo lang('documents') ?></em></a></li>
		<li <?php echo isset($_POST['add_notification']) ? 'class="selected"' : "" ?>><a href="#notfications"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/appointment-new.png" alt="icon" /> <?php echo lang('notifications') ?></em></a></li>
		
		<?php } ?>
	</ul>
	<div class="yui-content">
		<?php if($contract->get_id() > 0) {?>
		<div id="composites">
			<h3><?php echo lang('selected_composites') ?></h3>
			<?php
				$list_form = false;
				$list_id = 'included_composites';
				$related = array('not_included_composites');
				$url_add_on = '&amp;type=included_composites&amp;contract_id='.$contract->get_id();
				include('composite_list_partial.php'); ?>

			<?php if ($editable) { ?>
			<h3><?php echo lang('available_composites') ?> (<?php echo lang('messages_right_click_to_add') ?>)</h3>
			<?php
				$list_form = true;
				$list_id = 'not_included_composites';
				$related = array('included_composites');
				$url_add_on = '&amp;type=not_included_composites&amp;contract_id='.$contract->get_id();
				include('composite_list_partial.php'); ?>
			<?php } ?>
		</div>
		<div id="parties">
			<h3><?php echo lang('selected_parties') ?></h3>
			<?php
				$list_form = false;
				$list_id = 'included_parties';
				$extra_cols = array(array("key" => "is_payer", "label" => lang('is_payer'), "index" => 3));
				$related = array('not_included_parties');
				$url_add_on = '&amp;type=included_parties&amp;contract_id='.$contract->get_id();
				include('party_list_partial.php');
				$extra_cols = array();
			?>

			<?php if ($editable) {?>
			<h3><?php echo lang('available_parties') ?> (<?php echo lang('messages_right_click_to_add') ?>)</h3>
			<?php
				$list_form = true;
				$list_id = 'not_included_parties';
				$related = array('included_parties');
				$url_add_on = '&amp;type=not_included_parties&amp;contract_id='.$contract->get_id();
				include('party_list_partial.php'); ?>
			<?php } ?>
		</div>
		<div id="price">
			<h3><?php echo lang('selected_price_items') ?></h3>
			
		 	<?php
				$list_form = false;
				$list_id = 'included_price_items';
				$related = array('not_included_price_items','total_price');
				$url_add_on = '&amp;type=included_price_items&amp;contract_id='.$contract->get_id();
				$extra_cols = array(
					array("key" => "area", "label" => lang('area'), "index" => 4, "formatter" => "formatArea"),
					array("key" => "count", "label" => lang('count'), "index" => 5, "formatter" => "formatCount"),
					array("key" => "total_price", "label" => lang('total_price'), "formatter" => "formatPrice", "index" => 6),
					array("key" => "date_start", "label" => lang('date_start'), "index" => 7, "formatter" => "YAHOO.rental.formatDate", "parser" => '"date"'),
					array("key" => "date_end", "label" => lang('date_end'), "index" => 8, "formatter" => "YAHOO.rental.formatDate", "parser" => '"date"')
				);

				$editor_action = 'rental.uiprice_item.set_value';

				if ($editable) {
					$editors = array(
						'title' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:true})',
						'count' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:true})',
						'price' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:true})',
						'date_start' => 'new YAHOO.widget.DateCellEditor()',
						'date_end' => 'new YAHOO.widget.DateCellEditor()'
					);
				}

				include('price_item_partial.php'); ?>
			<?php if ($editable) { ?>
			<h3><?php echo lang('available_price_items') ?> (<?php echo lang('messages_right_click_to_add') ?>)</h3>
			<?php
				$list_form = true;
				$list_id = 'not_included_price_items';
				$related = array('included_price_items','total_price');
				$url_add_on = '&amp;type=not_included_price_items&amp;contract_id='.$contract->get_id(). '&amp;responsibility_id='.$contract->get_location_id();
				unset($extra_cols);
				unset($editors);
				include('price_item_partial.php'); ?>
			<?php } ?>
		</div>
		<?php }?>
		<div class="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value="<?php echo $contract->get_id() ?>"/>
				<dl class="proplist-col">
					<dt>
						<label for="name"><?php echo lang('field_of_responsibility') ?></label>
					</dt>
					<dd>
						<?php 
							$contract_id = $contract->get_id();
							if($editable && (!isset($contract_id) || $contract_id <= 0)) {
								
							 ?>
							 	<input type="hidden" name="location_id" id="location_id" value="<?php echo $contract->get_location_id() ?>"/>
							 <?php 
							}
							echo lang($contract->get_contract_type_title());
							?>
					</dd>
					<dt>
						<label for="contract_type"><?php echo lang('contract_type') ?></label>
					</dt>
					<dd>
						<?php
						$current_contract_type_id = $contract->get_contract_type_id();
						if ($editable)
						{
							?>
							<select name="contract_type">
								<option>Ingen type</option>
								<?php
								foreach(rental_socontract::get_instance()->get_contract_types($contract->get_location_id()) as $contract_type_id => $contract_type_label)
								{
									echo "<option ".($current_contract_type_id == $contract_type_id ? 'selected="selected"' : "")." value=\"{$contract_type_id}\">".$contract_type_label."</option>";
								}
								?>
							</select>
							<?php
						?>
						<?php
						}
						else // Non-editable
						{
							echo rental_socontract::get_instance()->get_contract_type_label($current_contract_type_id);
						}
						?>
					</dd>
					<dt>
						<label for="executive_officer"><?php echo lang('executive_officer') ?></label>
					</dt>
					<dd>
						<?php if($editable) { ?>
								<select name="executive_officer" id="executive_officer">
									<option value=""><?php echo lang('nobody') ?></option>
									<?php
										$executive_officer = $contract->get_executive_officer_id();
										$accounts = $GLOBALS['phpgw']->accounts->get_list('accounts',-1,'','account_lastname');
										foreach($accounts as $account)
										{
											$account_id = $account->__get('id');
											$GLOBALS['phpgw']->acl->set_account_id($account_id);											
											if($contract->has_permission(PHPGW_ACL_ADD))
											{
												$selected = '';
												if($account_id == $executive_officer){
													$selected = 'selected=\'selected\'';
												}
												echo '<option value="'.$account_id.'" '.$selected.'>'.$account->__get('firstname')." ".$account->__get('lastname')."</option>";
											}
										}
										$GLOBALS['phpgw']->acl->set_account_id(0); // Reset acl to current user
									?>
								</select>
						<?php } else { 
							$executive_officer = $contract->get_executive_officer_id();
							if(isset($executive_officer)){
								 $account = $GLOBALS['phpgw']->accounts->get($executive_officer);
								 if(isset($account)){
								 	echo $account->__get('firstname')." ".$account->__get('lastname');
								 } 
								 else
								 {
								 	echo lang('nobody');
								 }
							}
							else
							{
								echo lang('nobody');
							}
							
						}?>
						
						
					</dd>
					<dt>
						<label for="name"><?php echo lang('date_start') ?></label>
					</dt>
					<dd>
						<?php
							$start_date = $contract->get_contract_date() && $contract->get_contract_date()->has_start_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_contract_date()->get_start_date()) : '-';
							$start_date_yui = $contract->get_contract_date() && $contract->get_contract_date()->has_start_date() ? date('Y-m-d', $contract->get_contract_date()->get_start_date()) : '';
							$start_date_cal = $GLOBALS['phpgw']->yuical->add_listener('date_start', $start_date);?>
						<?php if ($editable) {
								echo $start_date_cal;
							} else {
								echo $start_date;
							}
						?>
					</dd>

					<dt>
						<label for="name"><?php echo lang('date_end') ?></label>
					</dt>
					<dd>
						<?php
							$end_date = $contract->get_contract_date() && $contract->get_contract_date()->has_end_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_contract_date()->get_end_date()) : '-';
							$end_date_yui = $contract->get_contract_date() && $contract->get_contract_date()->has_end_date() ? date('Y-m-d', $contract->get_contract_date()->get_end_date()) : '';
							$end_date_cal =  $GLOBALS['phpgw']->yuical->add_listener('date_end', $end_date);
						?>
						<?php if ($editable) {
								echo $end_date_cal;
							} else {
								echo $end_date;
						 }?>
						<br/>
					</dd>
					<dt>
						<label for="due_date"><?php echo lang('due_date') ?></label>
					</dt>
					<dd>
						<?php
							$due_date = $contract->get_due_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_due_date()) : '-';
							$due_date_yui = $contract->get_due_date() ? date('Y-m-d', $contract->get_due_date()) : '';
							if ($editable) {
								echo $GLOBALS['phpgw']->yuical->add_listener('due_date', $due_date);
							} else {
								echo $due_date;
							}
						?>
						<br/>
					</dd>
					<dt>
						<label for="invoice_header"><?php echo lang('invoice_header') ?></label>
					</dt>
					<dd>
						<?php
						if ($editable) {
						?>
							<input type="text" name="invoice_header" id="invoice_header" value="<?php echo $contract->get_invoice_header(); ?>"/>
						<?php
						}
						else
						{
							echo $contract->get_invoice_header();
						}
						?>
					</dd>
					<dt>
						<label for="billing_term"><?php echo lang('billing_term') ?></label>
					</dt>
					<dd>
						<?php
						$current_term_id = $contract->get_term_id();
						if ($editable)
						{
							?>
							<select name="billing_term">
								<?php
								foreach(rental_sobilling::get_instance()->get_billing_terms() as $term_id => $term_title)
								{
									echo "<option ".($current_term_id == $term_id ? 'selected="selected"' : "")." value=\"{$term_id}\">".lang($term_title)."</option>";
								}
								?>
							</select>
							<?php
						?>
						<?php
						}
						else // Non-editable
						{
							echo lang(rental_socontract::get_instance()->get_term_label($current_term_id));
						}
						?>
					</dd>
					<dt>
						<label for="billing_start_date"><?php echo lang('billing_start') ?></label>
					</dt>
					<dd>
						<?php
							$billing_start_date = $contract->get_billing_start_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_billing_start_date()) : '-';
							$billing_start_date_yui = $contract->get_billing_start_date() ? date('Y-m-d', $contract->get_billing_start_date()) : '';
							if ($editable) {
								echo $GLOBALS['phpgw']->yuical->add_listener('billing_start_date', get_billing_start_date);
							} else {
								echo $billing_start_date;
							}
						?>
					</dd>
					<dt>
						<label for="reference"><?php echo lang('reference') ?></label>
					</dt>
					<dd>
						<?php
						if ($editable) {
						?>
							<input type="text" name="reference" id="reference" value="<?php echo $contract->get_reference(); ?>"/>
						<?php
						}
						else
						{
							echo $contract->get_reference();
						}
						?>
					</dd>
				</dl>
				<dl class="proplist-col">
					<dt>
						<label for="service_id"><?php echo lang('service') ?></label>
					</dt>
					<dd>
						<?php
						if ($editable) {
						?>
							<input type="text" name="service_id" id="service_id" value="<?php echo $contract->get_service_id(); ?>"/>
						<?php
						}
						else
						{
							echo $contract->get_service_id();
						}
						?>
					</dd>
					<dt>
						<label for="responsibility_id"><?php echo lang('responsibility') ?></label>
					</dt>
					<dd>
						<?php
						if ($editable) {
						?>
							<input type="text" name="responsibility_id" id="responsibility_id" value="<?php echo $contract->get_responsibility_id(); ?>"/>
						<?php
						}
						else
						{
							echo $contract->get_responsibility_id();
						}
						?>
					</dd>
					<dt>
						<label for="account_in"><?php echo lang('account_in') ?></label>
					</dt>
					<dd>
						<?php
						if ($editable) {
						?>
							<input type="text" name="account_in" id="account_in" value="<?php 
							$cid = $contract->get_id();
							if(!isset($cid) || $cid <= 0)
							{
								
								echo rental_socontract::get_instance()->get_default_account($contract->get_location_id(), true);
							}
							else
							{
								echo $contract->get_account_in(); 
							}
							?>"/>
						<?php
						}
						else
						{
							echo $contract->get_account_in();
						}
						?>
					</dd>
					<dt>
						<label for="account_out"><?php echo lang('account_out') ?></label>
					</dt>
					<dd>
						<?php
						if ($editable) {
						?>
							<input type="text" name="account_out" id="account_out" value="<?php 
							$cid = $contract->get_id();
							if(!isset($cid) || $cid <= 0)
							{
								echo rental_socontract::get_instance()->get_default_account($contract->get_location_id(), false);
							}
							else
							{
								echo $contract->get_account_out(); 
							}
							?>"/>
						<?php
						}
						else
						{
							echo $contract->get_account_out();
						}
						?>
					</dd>
					<dt>
						<label for="project_id"><?php echo lang('project_id') ?></label>
					</dt>
					<dd>
						<?php
						if ($editable) {
						?>
							<input type="text" name="project_id" id="project_id" value="<?php 
							$cid = $contract->get_id();
							if(!isset($cid) || $cid <= 0)
							{
								echo rental_socontract::get_instance()->get_default_project_number($contract->get_location_id(), false);
							}
							else
							{
								echo $contract->get_project_id() ;
							}
							?>"/>
						<?php
						}
						else
						{
							echo $contract->get_project_id();
						}
						?>
					</dd>
					<dt>
						<label for="security_type"><?php echo lang('security') ?></label>
					</dt>
					<dd>
						<?php
						if ($editable) {
						?>
						<select name="security_type" id="security_type">
							<option value="0"><?php echo lang('nobody') ?></option>
							<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_BANK_GUARANTEE ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_BANK_GUARANTEE ?>"><?php echo lang('bank_guarantee') ?></option>
							<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_DEPOSIT ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_DEPOSIT ?>"><?php echo lang('deposit') ?></option>
							<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_ADVANCE ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_ADVANCE ?>"><?php echo lang('advance') ?></option>
							<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_OTHER_GUARANTEE ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_OTHER_GUARANTEE ?>"><?php echo lang('other_guarantee') ?></option>
						</select>
						<?php 
						}
						else
						{
							if ($editable) {
								?>

								<table><tr>
								<td>
									<select name="security_type" id="security_type">
									<option value="-1"></option>
									<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_BANK_GUARANTEE ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_BANK_GUARANTEE ?>"><?php echo lang('bank_guarantee') ?></option>
									<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_DEPOSIT ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_DEPOSIT ?>"><?php echo lang('deposit') ?></option>
									<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_ADVANCE ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_ADVANCE ?>"><?php echo lang('advance') ?></option>
									<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_OTHER_GUARANTEE ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_OTHER_GUARANTEE ?>"><?php echo lang('other_guarantee') ?></option>
								</select>
								</td>
								<td><label for="security_amount"><?php echo isset($config->config_data['currency_prefix']) && $config->config_data['currency_prefix'] ? $config->config_data['currency_prefix'] : 'Kr'; ?></label></td>
								<td><input type="text" name="security_amount" id="security_amount" value="<?php echo $contract->get_security_amount(); ?>"/></td>
								</tr></table>


								<?php
							}
							else
							{
								switch ($contract->get_security_type())
								{
									case rental_contract::SECURITY_TYPE_BANK_GUARANTEE:
										echo lang('bank_guarantee');
										break;
									case rental_contract::SECURITY_TYPE_DEPOSIT:
										echo lang('deposit');
										break;
									case rental_contract::SECURITY_TYPE_ADVANCE:
										echo lang('advance');
										break;
									case rental_contract::SECURITY_TYPE_OTHER_GUARANTEE:
										echo lang('other_guarantee');
										break;
									default:
										echo lang('nobody');
										break;
								}
							}
						}
						?>
					</dd>
					<dt>
						<label for="security_amount"><?php echo lang('security_amount') ?></label>
					</dt>
					<dd>
						<label for="security_amount"><?php echo $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'] ?></label>
						<?php
						if ($editable) {
						?>
							<input type="text" name="security_amount" id="security_amount" value="<?php echo $contract->get_security_amount(); ?>"/>
						<?php
						}
						else
						{	
							if($contract->get_security_amount() && $contract->get_security_amount() > 0)
							{
								echo $contract->get_security_amount();
							}
							else
							{
								echo '0';
							}
						}
						?>
					</dd>
					<dt><label for="rented_area"><?php echo lang('rented_area') ?></label></dt>
					<dd>
						<?php
							if ($editable) {?>
								<input type="text" name="rented_area" id="rented_area" value="<?php echo $contract->get_rented_area() ?>"/>&nbsp;<?php echo isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm';?>
							<?php } else {?>
								<?php echo $contract->get_rented_area()?>&nbsp;<?php echo isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm';?>
							<?php }
						?>
					</dd>
				</dl>
                <dl class="proplist-col">
                    <dt>
                        <label for="comment"><?php echo lang('comment') ?></label>
                    </dt>
                    <dd>
                        <?php
                        if ($editable)
                        {
                            ?>
                            <textarea cols="40" rows="10" name="comment" id="comment"><?php echo $contract->get_comment(); ?></textarea>
                            <?php
                        }
                        else
                        {
                            echo $contract->get_comment();
                        }
                        ?>
                    </dd>
                </dl>
				<div class="form-buttons">
					<?php
						if ($editable) {
							echo '<input type="submit" name="save_contract" value="' . lang('save') . '"/>';
						}
					?>
				</div>
			</form>
		</div>
		<?php if($contract->get_id() > 0) {?>		
		<div id="invoice">
			<?php
				$list_form = true;
				$list_id = 'invoice_price_items';
				$url_add_on = "&amp;type={$list_id}";
				$extra_cols = null;
				include('invoice_price_item_list_partial.php');
			?>
		</div>
		<div id="documents">
			<?php
				$list_form = true;
				$list_id = 'documents_for_contract';
				$url_add_on = "&amp;type={$list_id}&amp;contract_id={$contract->get_id()}";
				$upload_url_add_on = "&amp;contract_id={$contract->get_id()}";
				unset($extra_cols);
				unset($editors);
				unset($related);
				include('document_list_partial.php'); 
			?>
		</div>
		<div id="notifications">
			<h3><?php echo lang('contract_notifications') ?></h3>
			<?php
			$list_form = false;
			$list_id = 'rental_notifications';
			$url_add_on = '&amp;type=notifications&amp;sort=date&amp;dir=DESC&amp;editable=true&amp;contract_id='.$contract->get_id();
			$disable_left_click = true;
			unset($extra_cols);
			unset($editors);
			include('notification_list.php');
			?>
			<h3><?php echo lang('new_notification') ?></h3>
			<?php
			if ($editable) {

			?>
				<form action="?menuaction=rental.uicontract.edit&id=<?php echo $contract->get_id() ?>" method="post">
					<?php
					$notification_date = date('Y-m-d');
					if(isset($notification))
					{
						$notification_date = date('Y-m-d',$notification->get_date());
					}
					?>


					<input type="hidden" name="notification_contract_id" value="<?php echo $contract->get_id() ?>"/>
					<!-- <input type="hidden" name="date_notification_hidden" id="date_notification_hidden" value="<?php echo $date ?>"/> -->
					<fieldset>

								<label for="calendarNotificationDate"><?php echo lang('date') ?></label>
								<!--<input type="text" name="date_notification" id="date_notification" size="10" value="<?php echo isset($notification) ? htmlentities($notification->get_date()) : '' ?>" /> -->
								<?php echo $GLOBALS['phpgw']->yuical->add_listener('date_notification', $notification_date); ?>
								<?php echo rental_uicommon::get_field_error($notification, 'date') ?>
									<label for="notification_recurrence"><?php echo lang('recurrence') ?></label>
								<select name="notification_recurrence" id="notification_recurrence">
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_NEVER ? 'selected="selected"' : '' ?>value="<?php echo rental_notification::RECURRENCE_NEVER ?>"><?php echo lang('never') ?></option>
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_ANNUALLY ? 'selected="selected"' : '' ?> value="<?php echo rental_notification::RECURRENCE_ANNUALLY ?>"><?php echo lang('annually') ?></option>
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_MONTHLY ? 'selected="selected"' : '' ?> value="<?php echo rental_notification::RECURRENCE_MONTHLY ?>"><?php echo lang('monthly') ?></option>
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_WEEKLY ? 'selected="selected"' : '' ?> value="<?php echo rental_notification::RECURRENCE_WEEKLY ?>"><?php echo lang('weekly') ?></option>
								</select>
					</fieldset>
					<fieldset>
						<label for="notification_message"><?php echo lang('message') ?></label>
								<input type="text" name="notification_message" id="notification_message" size="50" value="<?php echo isset($notification) ? htmlentities($notification->get_message()) : '' ?>" />
					</fieldset>
					<fieldset>
						<label><?php echo lang('audience') ?></label>
						<label for="notification_target"><?php echo lang('user_or_group') ?></label>
						<select name="notification_target" id="notification_target">
							<option value=""><?php echo lang('target_none') ?></option>
							
							<?php
								$accounts = $GLOBALS['phpgw']->accounts->get_list('accounts');
								$label = lang('notification_optgroup_users');
								echo '<optgroup label="'.$label.'">';
								echo '<option value="'.$GLOBALS['phpgw_info']['user']['account_id'].'">'.lang('target_me').'</option>';
								foreach($accounts as $account)
								{
									$id = $account->__get('id');
									if($id != $GLOBALS['phpgw_info']['user']['account_id'])
									{
										echo '<option value="'.$id.'">'.$account->__get('firstname')." ".$account->__get('lastname')."</option>";
									}
								}
								echo '</optgroup>';
								$accounts = $GLOBALS['phpgw']->accounts->get_list('groups');
								$label = lang('notification_optgroup_groups');
								echo '<optgroup label="'.$label.'">';
								foreach($accounts as $account)
								{
										$id = $account->__get('id');
										echo '<option value="'.$id.'">'.$account->__get('firstname')." ".$account->__get('lastname')."</option>";
								}
								echo '</optgroup>';
							?>
						</select>
						<label for="notification_location"><?php echo lang('field_of_responsibility') ?></label>
						<select name="notification_location" id="notification_location">
							<option value=""><?php echo lang('target_none') ?></option>
							<?php
							$types = rental_socontract::get_instance()->get_fields_of_responsibility();
							foreach($types as $id => $label)
							{
								$names = $this->locations->get_name($id);
								if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
								{
									if($id == $contract->get_location_id()){
										$selected = 'selected="selected"';	
									} 
									echo '<option value="'.$id.'" '.$selected.'>'.lang($label).'</option>';
								}
							}
							?>
						</select>
					</fieldset>
					<fieldset>
								<input type="submit" name="add_notification" id="" value="<?php echo lang('add') ?>" />
					</fieldset>
				</form>
			<?php
			}
			else
			{
				echo lang('log_in_to_add_notfications');
			}
			?>
		</div>
		
		<?php } ?>
	</div>
</div>
