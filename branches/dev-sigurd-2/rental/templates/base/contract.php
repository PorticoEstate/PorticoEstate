<?php
	include("common.php");
	phpgwapi_yui::load_widget('tabview');
	phpgwapi_yui::tabview_setup('contract_tabview');
	$config	= CreateObject('phpgwapi.config','rental');
	$config->read();
?>



<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<div class="identifier-header">
	<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/text-x-generic.png" /> <?php echo lang('showing_contract') ?></h1>
	<div>
		<label><?php echo lang('contract_number') ?> </label>
		<?php if($contract->get_id() > 0) { echo $contract->get_id(); } else { echo lang('no_value'); }?>
		<?php if($contract->get_old_contract_id()){ 
			echo ' ('.$contract->get_old_contract_id().' )'; 
		} ?>
	</div>
</div>

<div id="contract_tabview" class="yui-navset">
	<ul class="yui-nav">
		<li <?php echo (!isset($_POST['add_notification'])) ? 'class="selected"' : "" ?>><a href="#details"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" /> <?php echo lang('details') ?></em></a></li>
		<li><a href="#parties"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-address-book.png" alt="icon" /> <?php echo lang('parties') ?></em></a></li>
		<li><a href="#composites"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-home.png" alt="icon" /> <?php echo lang('composite') ?></em></a></li>
		<li><a href="#price"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png" alt="icon" />   <?php echo lang('price') ?></em></a></li>
		<li><a href="#documents"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/apps/system-file-manager.png" alt="icon" /> <?php echo lang('documents') ?></em></a></li>
		<li <?php echo isset($_POST['add_notification']) ? 'class="selected"' : "" ?>><a href="#notfications"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/appointment-new.png" alt="icon" /> <?php echo lang('notifications') ?></em></a></li>
	</ul>
	<div class="yui-content">
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
						<label for="executive_officer"><?php echo lang('executive_officer') ?></label>
					</dt>
					<dd>
						<?php if($editable) { ?>
								<select name="executive_officer" id="executive_officer">
									<option value=""><?php echo lang('nobody') ?></option>
									<?php
										$executive_officer = $contract->get_executive_officer_id();
										$accounts = $GLOBALS['phpgw']->accounts->get_list('accounts');
										foreach($accounts as $account)
										{
											$account_id = $account->__get('id');
											$selected = '';
											if($account_id == $executive_officer){
												$selected = 'selected=\'selected\'';
											}
											echo '<option value="'.$account_id.'" '.$selected.'>'.$account->__get('firstname')." ".$account->__get('lastname')."</option>";
										}
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
							if ($editable) {
								echo $GLOBALS['phpgw']->yuical->add_listener('date_start', $start_date);
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
							if ($editable) {
								echo $GLOBALS['phpgw']->yuical->add_listener('date_end', $end_date);
							} else {
								echo $end_date;
							}
						?>
						<br/>
					</dd>
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
				</dl>
				<dl class="proplist-col">
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
					<dd>
					<dt>
						<label for="security_amount"><?php echo lang('security_amount') ?></label>
					</dt>
					<dd>
						<label for="security_amount"><?php echo lang('currency_prefix') ?></label>
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
					<dt>
						<label for="billing_term"><?php echo lang('billing_term') ?></label>
					</dt>
					<dd>
						<?php
						if ($editable)
						{
							$current_term_id = $contract->get_term_id();
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
							echo lang($contract->get_term_id_title());
						}
						?>
					</dd>
					<dt>
						<label for="billing_start_date"><?php echo lang('billing_start') ?></label>
					</dt>
					<dd>
						<?php
						$billing_start_date = $contract->get_billing_start_date();
						if($billing_start_date == null || $billing_start_date == '') // No date set
						{
							// ..so we try to use the start date of the contract if any
							$contract_date = $contract->get_contract_date();
							if($contract_date != null && $contract_date->has_start_date())
							{
								$billing_start_date = $contract_date->get_start_date();
							}
							else // No start date of contract
							{
								// ..so we use the today's date
								$billing_start_date = time();
							}
						}
						if($editable)
						{
							echo $GLOBALS['phpgw']->yuical->add_listener('billing_start_date', $billing_start_date);
						}
						else{ // Non-ediable
							echo date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $billing_start_date);
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
								echo $contract->get_account_out(); 
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
								echo '9'; // Default project number
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
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('cancel') . '</a>';
						} else {
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('back') . '</a>';
						}
					?>
				</div>
			</form>
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
		<div id="price">
			<h3><?php echo lang('selected_price_items') ?></h3>
			<strong><?php echo lang('total_price') ?>:</strong> <?php echo number_format(0, isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] : 2, isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',',lang('currency_thousands_separator')); echo ' '.isset($config->config_data['currency_suffix']) && $config->config_data['currency_suffix'] ? $config->config_data['currency_suffix'] : 'NOK';?> <br /><br />
		 	<?php
				$list_form = false;
				$list_id = 'included_price_items';
				$related = array('not_included_price_items');
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
						'area' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:true})',
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
				$related = array('included_price_items');
				$url_add_on = '&amp;type=not_included_price_items&amp;contract_id='.$contract->get_id();
				unset($extra_cols);
				unset($editors);
				include('price_item_partial.php'); ?>
			<?php } ?>
		</div>
		<div id="documents">
		</div>
		<div id="notifications">
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
				?>
				<?php echo lang('log_in_to_add_notfications') ?>
				<?php
			}
			?>
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
		</div>
	</div>
</div>