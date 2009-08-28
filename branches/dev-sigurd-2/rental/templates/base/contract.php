<?php 
	include("common.php");
	phpgwapi_yui::load_widget('tabview');	
	phpgwapi_yui::tabview_setup('contract_tabview');
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/text-x-generic.png" /> <?php echo lang('rental_common_showing_contract') ?> <?php echo $contract->get_id() ?></h1>

<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<div id="contract_tabview" class="yui-navset">
	<ul class="yui-nav">
	
		<li <?php echo (!isset($_POST['add_notification']) && !isset($_POST['save_invoice'])) ? 'class="selected"' : "" ?>><a href="#rental_common_details"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" /> <?php echo lang('rental_common_details') ?></em></a></li>
		<li><a href="#rental_common_parties"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-address-book.png" alt="icon" /> <?php echo lang('rental_common_parties') ?></em></a></li>
		<li><a href="#rental_common_composites"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-home.png" alt="icon" /> <?php echo lang('rental_common_composite') ?></em></a></li>
		<li><a href="#rental_common_price"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png" alt="icon" />   <?php echo lang('rental_common_price') ?></em></a></li>
		<li <?php echo isset($_POST['save_invoice']) ? 'class="selected"' : "" ?>><a href="#rental_common_invoice"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" /> <?php echo lang('rental_common_bill') ?></em></a></li>
		<li><a href="#rental_common_documents"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/apps/system-file-manager.png" alt="icon" /> <?php echo lang('rental_common_documents') ?></em></a></li>
		<li <?php echo isset($_POST['add_notification']) ? 'class="selected"' : "" ?>><a href="#rental_common_notfications"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/appointment-new.png" alt="icon" /> <?php echo lang('rental_common_notifications') ?></em></a></li>
		<li><a href="#rental_common_others"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" /> <?php echo lang('rental_common_others') ?></em></a></li>
	</ul>
	<div class="yui-content">
		<div class="details">
			<form action="#" method="post">
				<dl class="proplist-col">
					<dt>
						<label for="name"><?php echo lang('rental_common_contract_type') ?></label>
					</dt>
					<dd>
						<?php echo lang($contract->get_contract_type_title()) ?>
					</dd>
					
					<dt>
						<label for="name"><?php echo lang('rental_common_date_start') ?></label>
					</dt>
					<dd>
						<?php
							$start_date = $contract->get_contract_date() && $contract->get_contract_date()->has_start_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_contract_date()->get_start_date()) : '';
							$start_date_yui = $contract->get_contract_date() && $contract->get_contract_date()->has_start_date() ? date('Y-m-d', $contract->get_contract_date()->get_start_date()) : '';
							if ($editable) {
								echo $GLOBALS['phpgw']->yuical->add_listener('date_start', $start_date);
							} else {
								echo $start_date;
							}
						?>
					</dd>
					
					<dt>
						<label for="name"><?php echo lang('rental_common_date_end') ?></label>
					</dt>
					<dd>
						<?php
							$end_date = $contract->get_contract_date() && $contract->get_contract_date()->has_end_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_contract_date()->get_end_date()) : '';
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
						<label for="name"><?php echo lang('rental_common_billing_unit') ?></label>
					</dt>
					<dd>
						<?php
							if ($editable) {
								echo '<input type="text" name="billing_unit" id="billing_unit" value="' . $contract->get_billing_unit() . '"/>';
							} else {
								echo $contract->get_billing_unit();
							}
						?>
					</dd>
					
					<dt>
						<label for="name"><?php echo lang('rental_common_security') ?></label>
					</dt>
					<dd>
						<?php
							if ($editable) {
								?>
								<select name="security_type" id="security_type">
									<option value="-1"></option>
									<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_BANK_GUARANTEE ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_BANK_GUARANTEE ?>"><?php echo lang('rental_common_bank_guarantee') ?></option>
									<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_DEPOSIT ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_DEPOSIT ?>"><?php echo lang('rental_common_deposit') ?></option>
									<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_ADVANCE ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_ADVANCE ?>"><?php echo lang('rental_common_advance') ?></option>
									<option <?php echo $contract->get_security_type() == rental_contract::SECURITY_TYPE_OTHER_GUARANTEE ? 'selected="selected"' : '' ?>value="<?php echo rental_contract::SECURITY_TYPE_OTHER_GUARANTEE ?>"><?php echo lang('rental_common_other_guarantee') ?></option>
								</select>
								<input type="text" name="security_amount" id="security_amount" value="<?php echo $contract->get_security_amount(); ?>"/>
								<?php
							} 
							else
							{
								switch ($contract->get_security_type())
								{
									case rental_contract::SECURITY_TYPE_BANK_GUARANTEE:
										echo lang('rental_common_bank_guarantee');
										break;
									case rental_contract::SECURITY_TYPE_DEPOSIT:
										echo lang('rental_common_deposit');
										break;
									case rental_contract::SECURITY_TYPE_ADVANCE:
										echo lang('rental_common_advance');
										break;
									case rental_contract::SECURITY_TYPE_OTHER_GUARANTEE:
										echo lang('rental_common_other_guarantee');
										break;
									default:
										/* no-op */
										break;
								}
								echo '<br/>'.$contract->get_security_amount();
							}
						?>
					</dd>
				</dl>
				<div class="form-buttons">
					<?php
						if ($editable) {
							echo '<input type="submit" name="save_contract" value="' . lang('rental_common_save') . '"/>';
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_common_cancel') . '</a>';
						} else {
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_common_back') . '</a>';
						}
					?>
				</div>
			</form>
		</div>
		<div id="parties">
			<h3><?php echo lang('rental_common_selected_parties') ?></h3>
			<?php
				$list_form = false;
				$list_id = 'included_parties';
				$extra_cols = array(array("key" => "is_payer", "label" => lang('rental_common_is_payer'), "index" => 3));
				$related = array('not_included_parties');
				$url_add_on = '&amp;type=included_parties&amp;contract_id='.$contract->get_id();
				include('party_list_partial.php');
				$extra_cols = array();
			?>
			
			<?php if ($editable) {?>
			<h3><?php echo lang('rental_common_available_parties') ?> (<?php echo lang('rental_messages_right_click_to_add') ?>)</h3>
			<?php
				$list_form = true;
				$list_id = 'not_included_parties';
				$related = array('included_parties');
				$url_add_on = '&amp;type=not_included_parties&amp;contract_id='.$contract->get_id();
				include('party_list_partial.php'); ?>
			<?php } ?>
		</div>
		<div id="composites">
			<h3><?php echo lang('rental_common_selected_composites') ?></h3>
			<?php
				$list_form = false;
				$list_id = 'included_composites';
				$related = array('not_included_composites');
				$url_add_on = '&amp;type=included_composites&amp;contract_id='.$contract->get_id();
				include('composite_list_partial.php'); ?>
			
			<?php if ($editable) { ?>
			<h3><?php echo lang('rental_common_available_composites') ?> (<?php echo lang('rental_messages_right_click_to_add') ?>)</h3>
			<?php 
				$list_form = true;
				$list_id = 'not_included_composites';
				$related = array('included_composites');
				$url_add_on = '&amp;type=not_included_composites&amp;contract_id='.$contract->get_id();
				include('composite_list_partial.php'); ?>
			<?php } ?>
		</div>
		<div id="price">
			<h3><?php echo lang('rental_common_selected_price_items') ?></h3>
			<strong>Total pris:</strong> <?php echo number_format($contract->get_price(), 2, ",", " "); ?><br /><br />
			<?php 
				$list_form = false;
				$list_id = 'included_price_items';
				$related = array('not_included_price_items');
				$url_add_on = '&amp;type=included_price_items&amp;contract_id='.$contract->get_id();
				$extra_cols = array(
					array("key" => "area", "label" => lang('rental_common_area'), "index" => 4),
					array("key" => "count", "label" => lang('rental_common_count'), "index" => 5),
					array("key" => "total_price", "label" => lang('rental_common_total_price'), "formatter" => "YAHOO.widget.DataTable.formatCurrency", "index" => 6),
					array("key" => "date_start", "label" => lang('rental_common_date_start'), "index" => 7, "formatter" => "YAHOO.rental.formatDate", "parser" => '"date"'),
					array("key" => "date_end", "label" => lang('rental_common_date_end'), "index" => 8, "formatter" => "YAHOO.rental.formatDate", "parser" => '"date"')
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
			<h3><?php echo lang('rental_common_available_price_items') ?> (<?php echo lang('rental_messages_right_click_to_add') ?>)</h3>
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
		<div id="invoice">
			<form action="#" method="post">
				<dl class="proplist-col">
					<dt>
						<label for="billing_term"><?php echo lang('rental_common_billing_term') ?></label>
					</dt>
					<dd>
						<?php 
						if ($editable)
						{
							$current_term_id = $contract->get_term_id();
							?>
							<select name="billing_term">
								<?php
								foreach(rental_contract::get_billing_terms() as $term_id => $term_title)
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
						<label for="billing_start_date"><?php echo lang('rental_common_billing_start') ?></label>
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
							echo $GLOBALS['phpgw']->yuical->add_listener('billing_start_date', date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $billing_start_date));
						}
						else{ // Non-ediable
							echo date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $billing_start_date);
						}
						?>
					</dd>
				</dl>
				<div class="form-buttons">
					<?php
						if ($editable) {
							echo '<input type="submit" name="save_invoice" value="' . lang('rental_common_save') . '"/>';
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_common_cancel') . '</a>';
						} else {
							echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_common_back') . '</a>';
						}
					?>
				</div>
			</form>
		</div>
		<div id="documents">
		</div>
		<div id="notfications">
			<h3><?php echo lang('rental_common_new_notification') ?></h3>
			<?php 
			if ($editable) {
			?>
				<div id="calendarNotificationDate">
					<div id="calendarNotificationDate_body"></div>
					<div class="calheader">
						<button id="calendarNotificationDateCloseButton"><?php echo lang('rental_common_close') ?></button>
						<button id="calendarNotificationDateClearButton"><?php echo lang('rental_common_reset') ?></button>
					</div>
				</div>
				<form action="?menuaction=rental.uicontract.edit&id=<?php echo $contract->get_id() ?>" method="post">
					<?php
					if(isset($notification))
					{
						$date = $notification->get_date();
						if($date)
						{
							$date = date('Y-m-d', $date);
						}
					}
					?>
					<input type="hidden" name="notification_contract_id" value="<?php echo $contract->get_id() ?>"/>
					<input type="hidden" name="date_notification_hidden" id="date_notification_hidden" value="<?php echo $date ?>"/>
					<fieldset>
					
								<label for="calendarNotificationDate"><b><i><?php echo lang('rental_common_date') ?></i></b></label>
								<input type="text" name="date_notification" id="date_notification" size="10" value="<?php echo isset($notification) ? htmlentities($notification->get_date()) : '' ?>" />
								<?php echo rental_uicommon::get_field_error($notification, 'date') ?>
								<label for="notification_message"><b><i><?php echo lang('rental_common_message') ?></i></b></label>
								<input type="text" name="notification_message" id="notification_message" size="50" value="<?php echo isset($notification) ? htmlentities($notification->get_message()) : '' ?>" />
					</fieldset>	
					<fieldset>		
								<label for="notification_recurrence"><b><i><?php echo lang('rental_common_recurrence') ?></i></b></label>
								<select name="notification_recurrence" id="notification_recurrence">
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_NEVER ? 'selected="selected"' : '' ?>value="<?php echo rental_notification::RECURRENCE_NEVER ?>"><?php echo lang('rental_common_never') ?></option>
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_ANNUALLY ? 'selected="selected"' : '' ?> value="<?php echo rental_notification::RECURRENCE_ANNUALLY ?>"><?php echo lang('rental_common_annually') ?></option>
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_MONTHLY ? 'selected="selected"' : '' ?> value="<?php echo rental_notification::RECURRENCE_MONTHLY ?>"><?php echo lang('rental_common_monthly') ?></option>
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_WEEKLY ? 'selected="selected"' : '' ?> value="<?php echo rental_notification::RECURRENCE_WEEKLY ?>"><?php echo lang('rental_common_weekly') ?></option>
								</select>	
								<label for="notification_target"><b><i><?php echo lang('rental_common_audience') ?></i></b></label>
								<select name="notification_target" id="notification_target">
									<option value="<?php echo $GLOBALS['phpgw_info']['user']['account_id']; ?>"><?php echo lang('rental_common_target_me') ?></option>
									<?php 
										$accounts = $GLOBALS['phpgw']->accounts->get_list();
										foreach($accounts as $account)
										{
											echo '<option value="'.$account->__get('id').'">'.$account->__get('firstname')." ".$account->__get('lastname')."</option>";			
										}
									?>
								</select>
					</fieldset>	
					<fieldset>		
								<input type="submit" name="add_notification" id="" value="<?php echo lang('rental_common_add') ?>" />
					</fieldset>		
				</form>
			<?php 
			}
			else
			{
				?>
				<?php echo lang('rental_common_log_in_to_add_notfications') ?>
				<?php
			}
			?>
			<h3><?php echo lang('rental_common_your_notifications') ?></h3>
			<?php
			$list_form = false;
			$list_id = 'rental_notifications';
			$url_add_on = '&amp;type=notifications&amp;sort=date&amp;dir=DESC&amp;editable=true&amp;contract_id='.$contract->get_id();
			unset($extra_cols);
			unset($editors);
			include('notification_list.php');
			?>
		</div>
		<div id="others">
		</div>
	</div>
</div>
