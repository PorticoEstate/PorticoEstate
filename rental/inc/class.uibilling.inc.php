<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.sobilling');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.soinvoice');
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'billing', 'inc/model/');

	require_once PHPGW_API_INC . '/flysystem/autoload.php';

	use League\Flysystem\Filesystem;
	use League\Flysystem\Sftp\SftpAdapter;

	class rental_uibilling extends rental_uicommon
	{

		public $public_functions = array
			(
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'delete' => true,
			'commit' => true,
			'download' => true,
			'download_export' => true
		);

		public $message = array();

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('rental::contracts::invoice');
			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();
			$billing_time_limit = isset($config->config_data['billing_time_limit']) && $config->config_data['billing_time_limit'] ? (int)$config->config_data['billing_time_limit'] : 500;
			set_time_limit($billing_time_limit); // Set time limit
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('invoice_menu');
		}

		private function _object_to_array( $contract, $include_billing = false )
		{
			$values['old_contract_id'] = $contract->get_old_contract_id();
			$values['start_date'] = ($contract->get_contract_date()->has_start_date()) ? date($this->dateFormat, $contract->get_contract_date()->get_start_date()) : '';
			$values['end_date'] = ($contract->get_contract_date()->has_end_date()) ? date($this->dateFormat, $contract->get_contract_date()->get_end_date()) : '';
			$values['composite_name'] = $contract->get_composite_name();
			$values['party_name'] = $contract->get_party_name();
			$values['total_price'] = $contract->get_total_price();
			$values['rented_area'] = $contract->get_rented_area();
			if ($contract->get_bill_only_one_time())
			{
				$values['bill_only_one_time'] = lang('only_one_time_yes');
				$values['old_contract_id'] .= '<input name="bill_only_one_time[]" value="' . $contract->get_id() . '" type="hidden"/>'
					. '<input name="contract[]" value="' . $contract->get_id() . '" type="hidden"/>';
			}
			else
			{
				$values['bill_only_one_time'] = lang('only_one_time_no');
			}

			if ($include_billing)
			{
				$values['old_contract_id'] .= '<input name="contract[]" value="' . $contract->get_id() . '" type="hidden"/>';
			}

			$values['billing_start_date'] = date($this->dateFormat, $contract->get_billing_start_date());
			$values['id'] = $contract->get_id();

			return $values;
		}

		public function add()
		{
			$contract_type = phpgw::get_var('contract_type');
			// No messages so far
			$errorMsgs = array();
			$warningMsgs = array();
			$infoMsgs = array();
			$step = null; // Used for overriding the user's selection and choose where to go by code

			$link_add = array
				(
				'menuaction' => 'rental.uibilling.add'
			);

			$tabs = array();
			$tabs['details'] = array('label' => lang('Details'), 'link' => '#details');
			$active_tab = 'details';

			// Step 'simulation' of the billing job
			if (phpgw::get_var('step') == 2  && phpgw::get_var('next') != null) // User clicked next on step 2
			{
				$names = $this->locations->get_name($contract_type);
				if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					if (!$this->hasPermissionOn($names['location'], PHPGW_ACL_ADD))
					{
						phpgw::no_access();
					}
				}

				$use_existing = phpgw::get_var('use_existing');
				$existing_billing = phpgw::get_var('existing_billing');
				if ($use_existing < 1)
				{
					$existing_billing = 0;
				}
				$contract_ids = phpgw::get_var('contract'); // Ids of the contracts to bill
				$contract_ids_override = phpgw::get_var('override_start_date'); //Ids of the contracts that should override billing start date with first day in period
				$contract_bill_only_one_time = phpgw::get_var('bill_only_one_time');
				if(is_array($contract_ids))
				{
					$contract_ids = array_unique($contract_ids);
				}
				if(is_array($contract_bill_only_one_time))
				{
					$contract_bill_only_one_time = array_unique($contract_bill_only_one_time);
				}
				if (($contract_ids != null && is_array($contract_ids) && count($contract_ids) > 0) || (is_array($contract_bill_only_one_time) && count($contract_bill_only_one_time) > 0)) // User submitted contracts to bill
				{
					$missing_billing_info = rental_sobilling::get_instance()->get_missing_billing_info(phpgw::get_var('billing_term'), phpgw::get_var('year'), phpgw::get_var('month'), $contract_ids, (array)$contract_ids_override, phpgw::get_var('export_format'));

					if ($missing_billing_info == null || count($missing_billing_info) == 0)
					{
						$_decimal_place = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) ? isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) : 2;
						$invoices = rental_sobilling::get_instance()->create_billing(
							$_decimal_place,
							phpgw::get_var('contract_type'),
							phpgw::get_var('billing_term'),
							phpgw::get_var('year'),
							phpgw::get_var('month'),
							phpgw::get_var('title'),
							$GLOBALS['phpgw_info']['user']['account_id'],
							$contract_ids,
							(array) $contract_ids_override,
							phpgw::get_var('export_format'),
							$existing_billing,
							(array) $contract_bill_only_one_time,
							$_dry_run = true
						);
						$simulation_data = array();
						$_total_sum = 0;

						phpgw::import_class('rental.soparty');

						foreach ($invoices as $invoice)
						{
							$party = rental_soparty::get_instance()->get_single($invoice->get_party_id());
							$contract_url = '<a target="_blank" href="' . self::link(array('menuaction'=>'rental.uicontract.edit', 'id' => $invoice->get_contract_id())) . '">' .$invoice->get_old_contract_id() . '</a>';
							$simulation_data[] = array(
								'old_contract_id' => $contract_url,
								'composite_name' => $invoice->get_header(),
								'party_name' => $party->get_name(),
								'total_sum' => $invoice->get_total_sum(),
								'serial_number' => $invoice->get_id(),
							);
							$_total_sum += $invoice->get_total_sum();
						}

						$datatable_def[] = array
						(
							'container' => 'datatable-container_0',
							'requestUrl' => "''",
							'data' => json_encode(array()),
							'ColumnDefs' => array(
								array('key' => 'old_contract_id', 'label' => lang('contract_id'), 'className' => '',
									'sortable' => true, 'hidden' => false),
								array('key' => 'composite_name', 'label' => lang('invoice_header'), 'className' => '',
									'sortable' => true, 'hidden' => false),
								array('key' => 'party_name', 'label' => lang('party_name'), 'className' => '',
									'sortable' => true, 'hidden' => false),
								array('key' => 'total_sum', 'label' => lang('Total sum'), 'className' => 'right',
									'sortable' => true, 'hidden' => false, 'formatter' => 'formatterPrice'),
								array('key' => 'serial_number', 'label' => lang('serial_number'), 'className' => 'center',
									'sortable' => true, 'hidden' => false)
							),
							'data' => json_encode($simulation_data),
							'config' => array(
								array('disableFilter' => true)
							)
						);
						$template = 'simulation';

						//Get year
						$year = phpgw::get_var('year');

						//Get term and month
						$billing_term_tmp = phpgw::get_var('billing_term_selection');
						$billing_term_selection = $billing_term_tmp;
						$billing_term = substr($billing_term_tmp, 0, 1);
						$billing_month = substr($billing_term_tmp, 2);

						if ($billing_term == '1')
						{ // monthly
							$month = $billing_month;
						}
						else if ($billing_term == '4')
						{ // quarterly
							if ($billing_month == '1')
							{ //1. quarter
								$month = 3;
								$billing_term_label = lang('first_quarter');
							}
							else if ($billing_month == '2')
							{ //2. quarter
								$month = 6;
								$billing_term_label = lang('second_quarter');
							}
							else if ($billing_month == '3')
							{ //3. quarter
								$month = 9;
								$billing_term_label = lang('third_quarter');
							}
							else
							{ //4. quarter
								$month = 12;
								$billing_term_label = lang('fourth_quarter');
							}
						}
						else if ($billing_term == '3')
						{ // half year
							if ($billing_month == '1')
							{
								$month = 6;
								$billing_term_label = lang('first_half');
							}
							else
							{
								$month = 12;
								$billing_term_label = lang('second_half');
							}
						}
						else if ($billing_term == '5')
						{ // half year
							if ($billing_month == '1')
							{
								$month = 1;
								$billing_term_label = lang('free_of_charge');
							}
							else if ($billing_month == '2')
							{
								$month = 2;
								$billing_term_label = lang('credits');
							}
							else
							{
								$month = 3;
								$billing_term_label = lang('positive one time');
							}
						}
						else // yearly
						{
							$month = 12;
							$billing_term_label = lang('annually');
						}

						//Use existing billing?
						$use_existing = false;
						$existing_billing = phpgw::get_var('existing_billing');
						if ($existing_billing != 'new_billing')
						{
							$use_existing = true;
						}

						//Determine title
						$title = phpgw::get_var('title');
						if (!isset($title) || $title == '')
						{
							$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
							foreach ($fields as $id => $label)
							{
								if ($id == $contract_type)
								{
									$description = lang($label) . ' ';
								}
							}
							$description .= lang('month ' . $month) . ' ';
							$description .= $year;
							$title = $description;
						}

						if ($use_existing)
						{
							$billing_tmp = rental_sobilling::get_instance()->get_single($existing_billing);
							$title = $billing_tmp->get_title();
						}


						$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
						foreach ($fields as $id => $label)
						{
							if ($id == $contract_type)
							{
								$fields_of_responsibility_label = lang($label);
							}
						}

					// Get the number of months in selected term for contract
						$months = rental_socontract::get_instance()->get_months_in_term($billing_term);

						// The billing should start from the first date of the periode (term) we're billing for
						$first_day_of_selected_month = strtotime($year . '-' . $month . '-01');
						$bill_from_timestamp = strtotime('-' . ($months - 1) . ' month', $first_day_of_selected_month);
						$billing_start = date($this->dateFormat, $bill_from_timestamp);

						if ($billing_term == 1)
						{
							foreach (rental_sobilling::get_instance()->get_billing_terms() as $term_id => $term_title)
							{
								if ($term_id == $billing_term)
								{
									$billing_term_label = lang($term_title);
								}
							}
						}

						$data = array
							(
							'datatable_def' => $datatable_def,
							'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_add),
							'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uibilling.index')),
							'contract_type' => $contract_type,
							'billing_start' => $billing_start,
							'billing_term' => $billing_term,
							'billing_term_label' => $billing_term_label,
							'billing_term_selection' => $billing_term_selection,
							'fields_of_responsibility_label' => $fields_of_responsibility_label,
							'year' => $year,
							'month' => $month,
							'month_label' => lang('month ' . $month . ' capitalized'),
							'title' => $title,
							'sum' => number_format($_total_sum, $this->decimalPlaces, $this->decimalSeparator, $this->thousandsSeparator) . ' ' . $this->currency_suffix,
							'use_existing' => $use_existing,
							'existing_billing' => $existing_billing,
							'export_format' => phpgw::get_var('export_format'),
							'contract_ids' => $contract_ids, //phpgw::get_var('contract'); // Ids of the contracts to bill
							'contract_ids_override' => $contract_ids_override, //phpgw::get_var('override_start_date'); //Ids of the contracts that should override billing start date with first day in period
							'contract_bill_only_one_time' => $contract_bill_only_one_time,// phpgw::get_var('bill_only_one_time');
							'errorMsgs' => $errorMsgs,
							'warningMsgs' => $warningMsgs,
							'infoMsgs' => $infoMsgs,
							'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
						);
						$code = <<<JS
						var thousandsSeparator = '{$this->thousandsSeparator}';
						var decimalSeparator = '{$this->decimalSeparator}';
						var decimalPlaces = '{$this->decimalPlaces}';
						var currency_suffix = '{$this->currency_suffix}';
						var area_suffix = '{$this->area_suffix}';
JS;
						$GLOBALS['phpgw']->js->add_code('', $code);
						self::add_javascript('rental', 'rental', 'billing.add.js');
						phpgwapi_jquery::load_widget('numberformat');
						self::render_template_xsl(array('billing', 'datatable_inline'), array($template => $data));
						return;


					}
					else // Incomplete biling info
					{
						foreach ($missing_billing_info as $contract_id => $info_array)
						{
							if ($info_array != null && count($info_array) > 0)
							{
								$errorMsgs[] = lang('Missing billing information.', $contract_id);
								foreach ($info_array as $info)
								{
									$errorMsgs[] = ' - ' . lang($info);
								}
							}
						}
						$step = 2; // Go back to step 2
					}
				}
				else
				{
					$errorMsgs[] = lang('No contracts were selected.');
					$step = 2; // Go back to step 2
				}
			}
			// Step 3 - the billing job
			if (phpgw::get_var('step') == 'simulation' && phpgw::get_var('next') != null) // User clicked next on step 2
			{
				if ($GLOBALS['phpgw']->session->is_repost())
				{
					phpgwapi_cache::message_set(lang('Hmm... looks like a repost!'), 'error');
					self::redirect(array(
						'menuaction' => 'rental.uibilling.index'
						)
					);
				}

				$use_existing = phpgw::get_var('use_existing');
				$existing_billing = phpgw::get_var('existing_billing');
				if ($use_existing < 1)
				{
					$existing_billing = 0;
				}
				$contract_ids = phpgw::get_var('contract'); // Ids of the contracts to bill

				$contract_ids_override = phpgw::get_var('override_start_date'); //Ids of the contracts that should override billing start date with first day in period
				$contract_bill_only_one_time = phpgw::get_var('bill_only_one_time');
				if(is_array($contract_ids))
				{
					$contract_ids = array_unique($contract_ids);
				}
				if(is_array($contract_bill_only_one_time))
				{
					$contract_bill_only_one_time = array_unique($contract_bill_only_one_time);
				}
				if (($contract_ids != null && is_array($contract_ids) && count($contract_ids) > 0) || (is_array($contract_bill_only_one_time) && count($contract_bill_only_one_time) > 0)) // User submitted contracts to bill
				{
					$missing_billing_info = rental_sobilling::get_instance()->get_missing_billing_info(phpgw::get_var('billing_term'), phpgw::get_var('year'), phpgw::get_var('month'), $contract_ids, $contract_ids_override, phpgw::get_var('export_format'));

					if ($missing_billing_info == null || count($missing_billing_info) == 0)
					{
						$billing_job = rental_sobilling::get_instance()->create_billing(
							isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) ? isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) : 2,
							phpgw::get_var('contract_type'),
							phpgw::get_var('billing_term'),
							phpgw::get_var('year'),
							phpgw::get_var('month'),
							phpgw::get_var('title'),
							$GLOBALS['phpgw_info']['user']['account_id'],
							$contract_ids,
							$contract_ids_override,
							phpgw::get_var('export_format'),
							$existing_billing,
							$contract_bill_only_one_time,
							$_dry_run = false
						);
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uibilling.view',
							'id' => $billing_job->get_id()));
						return;
					}
					else // Incomplete biling info
					{
						foreach ($missing_billing_info as $contract_id => $info_array)
						{
							if ($info_array != null && count($info_array) > 0)
							{
								$errorMsgs[] = lang('Missing billing information.', $contract_id);
								foreach ($info_array as $info)
								{
									$errorMsgs[] = ' - ' . lang($info);
								}
							}
						}
						$step = 2; // Go back to step 2
					}
				}
				else
				{
					$errorMsgs[] = lang('No contracts were selected.');
					$step = 2; // Go back to step 2
				}
			}
			// Step 2 - list of contracts that should be billed
			if ($step == 2 || (phpgw::get_var('step') == '1' && phpgw::get_var('next') != null) || phpgw::get_var('step') == 'simulation' && phpgw::get_var('previous') != null) // User clicked next on step 1 or previous on step simulation
			{
				//Responsibility area
				//$contract_type = phpgw::get_var('contract_type');
				//Check permission
				$names = $this->locations->get_name($contract_type);
				if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					if (!$this->hasPermissionOn($names['location'], PHPGW_ACL_ADD))
					{
						phpgw::no_access();
					}
				}

				//Get year
				$year = phpgw::get_var('year');

				//Get term and month
				if ($step == 2 || phpgw::get_var('step') == 'simulation')
				{
					$billing_term_tmp = phpgw::get_var('billing_term_selection');
				}
				else
				{
					$billing_term_tmp = phpgw::get_var('billing_term');
				}
				$billing_term_selection = $billing_term_tmp;
				$billing_term = substr($billing_term_tmp, 0, 1);
				$billing_month = substr($billing_term_tmp, 2);

				if ($billing_term == '1')
				{ // monthly
					$month = $billing_month;
				}
				else if ($billing_term == '4')
				{ // quarterly
					if ($billing_month == '1')
					{ //1. quarter
						$month = 3;
						$billing_term_label = lang('first_quarter');
					}
					else if ($billing_month == '2')
					{ //2. quarter
						$month = 6;
						$billing_term_label = lang('second_quarter');
					}
					else if ($billing_month == '3')
					{ //3. quarter
						$month = 9;
						$billing_term_label = lang('third_quarter');
					}
					else
					{ //4. quarter
						$month = 12;
						$billing_term_label = lang('fourth_quarter');
					}
				}
				else if ($billing_term == '3')
				{ // half year
					if ($billing_month == '1')
					{
						$month = 6;
						$billing_term_label = lang('first_half');
					}
					else
					{
						$month = 12;
						$billing_term_label = lang('second_half');
					}
				}
				else if ($billing_term == '5')
				{ // half year
					if ($billing_month == '1')
					{
						$month = 1;
						$billing_term_label = lang('free_of_charge');
					}
					else if ($billing_month == '2')
					{
						$month = 2;
						$billing_term_label = lang('credits');
					}
					else
					{
						$month = 3;
						$billing_term_label = lang('positive one time');
					}
				}
				else // yearly
				{
					$month = 12;
					$billing_term_label = lang('annually');
				}

				//Use existing billing?
				$use_existing = false;
				$existing_billing = phpgw::get_var('existing_billing');
				if ($existing_billing != 'new_billing')
				{
					$use_existing = true;
				}

				//Determine title
				$title = phpgw::get_var('title');
				if (!isset($title) || $title == '')
				{
					$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
					foreach ($fields as $id => $label)
					{
						if ($id == $contract_type)
						{
							$description = lang($label) . ' ';
						}
					}
					$description .= lang('month ' . $month) . ' ';
					$description .= $year;
					$title = $description;
				}

				if ($use_existing)
				{
					$billing_tmp = rental_sobilling::get_instance()->get_single($existing_billing);
					$title = $billing_tmp->get_title();
				}

				$contracts_with_one_time = array();
				//Check to see if the period har been billed before, not including credits
				if ($billing_term != 5 && rental_sobilling::get_instance()->has_been_billed($contract_type, $billing_term, $year, $month)) // Checks if period has been billed before
				{
					// We only give a warning and let the user go to step 2
					$warningMsgs[] = lang('the period has been billed before.');
				}
				else
				{
					//$contracts_with_one_time = array();
					//... and if not start retrieving contracts for billing

					$socontract_price_item = rental_socontract_price_item::get_instance();

					$sort_ascending = false;
					$search_for = '';
					$search_type = '';
					//... 2. Contracts with one-time price items
					if($billing_term == 5)
					{
						$filters2 = array('contract_ids_one_time' => true);
						if($month == 2)
						{
							$filters2['credits'] = true;
						}
						else if($month == 3)
						{
							$filters2['positive_one_time'] = true;
						}
						$contracts = array();
					}
					else
					{
						//... 1. Contracts following regular billing cycle
						$filters = array('contracts_for_billing' => true, 'contract_type' => $contract_type,
							'billing_term_id' => $billing_term, 'year' => $year, 'month' => $month);
						$contracts = rental_socontract::get_instance()->get($start_index = 0, $num_of_objects = 0, $sort_field = '', (bool)$sort_ascending, (string)$search_for, (string)$search_type, $filters);
						$filters2 = array('contract_ids_one_time' => true, 'billing_term_id' => $billing_term,
							'year' => $year, 'month' => $month);
					}
					$contract_price_items = $socontract_price_item->get($start_index = 0, $num_of_objects = 0, $sort_field = '', (bool)$sort_ascending, (string)$search_for, (string)$search_type, $filters2);

					foreach ($contract_price_items as $contract_price_item)
					{
						if (!array_key_exists($contract_price_item->get_contract_id(), $contracts))
						{
							$aditional_contracts = rental_socontract::get_instance()->get(0, 0, '', false, '', '', array(
								'contract_id' => $contract_price_item->get_contract_id(), 'contract_type' => $contract_type));
							if (count($aditional_contracts) == 1)
							{
								$cid = $contract_price_item->get_contract_id();
								$c = $aditional_contracts[$cid];
								$c->set_bill_only_one_time();
								//$contracts[$contract_price_item->get_contract_id()] = $c;
								//$contracts_with_one_time[$contract_price_item->get_contract_id()] = $c; // used for information purposes
							}
							else
							{
								continue;
							}
						}
						else
						{
							$cid = $contract_price_item->get_contract_id();
							$c = $contracts[$cid];
							//$contracts_with_one_time[$cid] = $c;
						}

						if (!empty($c))
						{
							if($billing_term == 5)
							{
								$total_price = $contract_price_item->get_total_price();
							}
							else
							{
								$total_price = $socontract_price_item->get_total_price_invoice($c->get_id(), $billing_term, $month, $year);
							}
							$c->set_total_price($total_price);
							$contracts_with_one_time[] = $this->_object_to_array($c);
						}
					}

					/* foreach($contracts_with_one_time as $id => &$contract)
					  {
					  $total_price = $socontract_price_item->get_total_price_invoice($contract->get_id(), $billing_term, $month, $year);
					  $contract->set_total_price($total_price);
					  }
					  unset($contract); */

					// Get the number of months in selected term for contract
					$months = rental_socontract::get_instance()->get_months_in_term($billing_term);

					// The billing should start from the first date of the periode (term) we're billing for
					$first_day_of_selected_month = strtotime($year . '-' . $month . '-01');
					$bill_from_timestamp = strtotime('-' . ($months - 1) . ' month', $first_day_of_selected_month);

					$irregular_contracts = array();
					$array_contracts = array();
					$not_billed_contracts = array();
					$removed_contracts = array();
					foreach ($contracts as $id => $contract)
					{
						if (isset($contract))
						{
							$total_price = $socontract_price_item->get_total_price_invoice($contract->get_id(), $billing_term, $month, $year);
							$type_id = $contract->get_contract_type_id();
							$responsible_type_id = $contract->get_location_id();

							// Gets location title from table rental_contract_responsibility
							$location_title = rental_socontract::get_instance()->get_responsibility_title($responsible_type_id);

							if ($type_id == 4) // Remove contract of a specific type (KF)
							{
								$warningMsgs[] = lang('billing_removed_KF_contract') . " " . $contract->get_old_contract_id();
								unset($contracts[$id]);
								$removed_contracts[] = $this->_object_to_array($contract);
							}
							// A contract with responibility type contract_type_eksternleie must have a rental_contract_type
							else if (($type_id == 0 && strcmp($location_title, "contract_type_eksternleie") == 0) || (empty($type_id) && strcmp($location_title, "contract_type_eksternleie") == 0 ))
							{
								$contract->set_total_price($total_price);
								$warningMsgs[] = lang('billing_removed_contract_part_1') . " " . $contract->get_old_contract_id() . " " . lang('billing_removed_external_contract');
								unset($contracts[$id]);
								$removed_contracts[] = $this->_object_to_array($contract);
							}
							else if (isset($total_price) && $total_price == 0) // Remove contract if total price is equal to zero
							{
								$warningMsgs[] = lang('billing_removed_contract_part_1') . " " . $contract->get_old_contract_id() . " " . lang('billing_removed_contract_part_2');
								unset($contracts[$id]);
								$removed_contracts[] = $this->_object_to_array($contract);
							}
							else // Prepare contract for billing
							{
								$contract->set_total_price($total_price);

								// Find the last day of the last period the contract was billed before the specified date
								$last_bill_timestamp = $contract->get_last_invoice_timestamp($bill_from_timestamp);

								// If the contract has not been billed before, select the billing start date
								if ($last_bill_timestamp == null)
								{
									$next_bill_timestamp = $contract->get_billing_start_date();
									$not_billed_contracts[] = $this->_object_to_array($contract);
									$irregular_contracts[] = $this->_object_to_array($contract);
									unset($contracts[$id]);
								}
								else
								{
									// ... select the next that day that the contract should be billed from
									$next_bill_timestamp = strtotime('+1 day', $last_bill_timestamp);
									$contract->set_next_bill_timestamp($next_bill_timestamp);

									// The next time the contract should be billed from equals the first day of the current selected period
									if ($next_bill_timestamp == $bill_from_timestamp)
									{
										//The contract follows the regular billing cycle
									}
									else
									{
										unset($contracts[$id]);
										$irregular_contracts[] = $this->_object_to_array($contract);
									}
								}
							}

							if (!empty($contracts[$id]))
							{
								$array_contracts[] = $this->_object_to_array($contracts[$id], true);
							}
						}
					}
				}

				$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach ($fields as $id => $label)
				{
					if ($id == $contract_type)
					{
						$fields_of_responsibility_label = lang($label);
					}
				}

				$billing_start = date($this->dateFormat, $bill_from_timestamp);

				if ($billing_term == 1)
				{
					foreach (rental_sobilling::get_instance()->get_billing_terms() as $term_id => $term_title)
					{
						if ($term_id == $billing_term)
						{
							$billing_term_label = lang($term_title);
						}
					}
				}

				$uicols = array(
					array('key' => 'old_contract_id', 'label' => lang('contract'), 'hidden' => false),
					array('key' => 'start_date', 'label' => lang('date_start'), 'className' => 'center',
						'hidden' => false),
					array('key' => 'end_date', 'label' => lang('date_end'), 'className' => 'center',
						'hidden' => false),
					array('key' => 'composite_name', 'label' => lang('composite_name'), 'hidden' => false),
					array('key' => 'party_name', 'label' => lang('party_name'), 'hidden' => false),
					array('key' => 'total_price', 'label' => lang('total_price'), 'className' => 'right',
						'hidden' => false, 'formatter' => 'formatterPrice'),
					array('key' => 'rented_area', 'label' => lang('area'), 'className' => 'right',
						'hidden' => false, 'formatter' => 'formatterArea')
				);

				$uicols_irregular_contracts = $uicols;
				$uicols_irregular_contracts[] = array('key' => 'override', 'label' => lang('override'),
					'className' => 'center', 'formatter' => 'formatCheckOverride');
				$uicols_irregular_contracts[] = array('key' => 'bill2', 'label' => lang('bill2'),
					'className' => 'center', 'formatter' => 'formatCheckBill2');

				$tabletools_irregular_contracts[] = array
					(
					'my_name' => 'override_all',
					'className' => 'select',
					'text' => lang('Override'),
					'type' => 'custom',
					'custom_code' => "checkOverride();"
				);
				$tabletools_irregular_contracts[] = array
					(
					'my_name' => 'bill2_all',
					'className' => 'select',
					'text' => lang('Bill2'),
					'type' => 'custom',
					'custom_code' => "checkBill2()"
				);

				$datatable_def[] = array
					(
					'container' => 'datatable-container_0',
					'requestUrl' => "''",
					'ColumnDefs' => $uicols_irregular_contracts,
					'data' => json_encode($irregular_contracts),
					'tabletools' => $tabletools_irregular_contracts,
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);

				$uicols_contracts_with_one_time = $uicols;
				$uicols_contracts_with_one_time[5] = array('key' => 'bill_only_one_time',
					'label' => lang('only_one_time'),
					'className' => 'center');
				$uicols_contracts_with_one_time[6] = array('key' => 'total_price', 'label' => lang('total_price'),
					'className' => 'right', 'hidden' => false, 'formatter' => 'formatterPrice');

				$datatable_def[] = array
					(
					'container' => 'datatable-container_1',
					'requestUrl' => "''",
					'ColumnDefs' => $uicols_contracts_with_one_time,
					'data' => json_encode($contracts_with_one_time),
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);

				$datatable_def[] = array
					(
					'container' => 'datatable-container_2',
					'requestUrl' => "''",
					'ColumnDefs' => $uicols,
					'data' => json_encode($array_contracts),
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);

				$datatable_def[] = array
					(
					'container' => 'datatable-container_3',
					'requestUrl' => "''",
					'ColumnDefs' => $uicols,
					'data' => json_encode($not_billed_contracts),
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);

				$datatable_def[] = array
					(
					'container' => 'datatable-container_4',
					'requestUrl' => "''",
					'ColumnDefs' => $uicols,
					'data' => json_encode($removed_contracts),
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);

				$data = array
					(
					'datatable_def' => $datatable_def,
					'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_add),
					'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uibilling.index')),
					'contract_type' => $contract_type,
					'irregular_contracts' => $irregular_contracts,
					'contracts_with_one_time' => $contracts_with_one_time,
					'contracts' => $array_contracts,
					'not_billed_contracts' => $not_billed_contracts,
					'removed_contracts' => $removed_contracts,
					'billing_start' => $billing_start,
					'billing_term' => $billing_term,
					'billing_term_label' => $billing_term_label,
					'billing_term_selection' => $billing_term_selection,
					'fields_of_responsibility_label' => $fields_of_responsibility_label,
					'year' => $year,
					'month' => $month,
					'month_label' => lang('month ' . $month . ' capitalized'),
					'title' => $title,
					'use_existing' => $use_existing,
					'existing_billing' => $existing_billing,
					'export_format' => phpgw::get_var('export_format'),
					'errorMsgs' => $errorMsgs,
					'warningMsgs' => $warningMsgs,
					'infoMsgs' => $infoMsgs,
					'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
				);
				$template = 'step2';
			}
			else if ($step == null || (phpgw::get_var('next') != null) || phpgw::get_var('step') == '2' && phpgw::get_var('previous') != null) // User clicked next on step 0 or previous on step 2
			{
				//$contract_type = phpgw::get_var('contract_type');
				$export_format = rental_sobilling::get_instance()->get_agresso_export_format($contract_type);
				$existing_billing = phpgw::get_var('existing_billing');

				$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach ($fields as $id => $label)
				{
					if ($id == $contract_type)
					{
						$fields_of_responsibility_label = lang($label);
					}
				}

				$existing_billing_options[] = array('id' => 'new_billing', 'name' => lang('new_billing'));
				$result_objects = rental_sobilling::get_instance()->get(0, 0, '', false, '', '', array(
					'location_id' => $contract_type));
				foreach ($result_objects as $billing)
				{
					if ($billing->get_location_id() == $contract_type)
					{
						$selected = ($billing->get_id() == $existing_billing) ? 1 : 0;
						$existing_billing_options[] = array('id' => $billing->get_id(), 'name' => $billing->get_title(),
							'selected' => $selected);
					}
				}

				$this_year = phpgw::get_var('year');
				if (empty($this_year))
				{
					$this_year = date('Y');
				}
				$years = rental_contract::get_year_range();
				$year_options = array();
				foreach ($years as $year)
				{
					$selected = ($this_year == $year) ? 1 : 0;
					$year_options[] = array('id' => $year, 'name' => $year, 'selected' => $selected);
				}

				$billing_term_selection = phpgw::get_var('billing_term_selection');
				$current = 0;
				$billing_term_group_options = array();
				foreach (rental_sobilling::get_instance()->get_billing_terms() as $term_id => $term_title)
				{
					$options = array();
					if ($current == 0)
					{
						$options[] = array('id' => $term_id . '-1', 'name' => lang($term_title), 'selected' => (($term_id . '-1' == $billing_term_selection) ? 1 : 0));
					}
					else if ($current == 1)
					{
						$options[] = array('id' => $term_id . '-1', 'name' => '1. halv&aring;r', 'selected' => (($term_id . '-1' == $billing_term_selection) ? 1 : 0));
						$options[] = array('id' => $term_id . '-2', 'name' => '2. halv&aring;r', 'selected' => (($term_id . '-2' == $billing_term_selection) ? 1 : 0));
					}
					else if ($current == 2)
					{
						$options[] = array('id' => $term_id . '-1', 'name' => '1. kvartal', 'selected' => (($term_id . '-1' == $billing_term_selection) ? 1 : 0));
						$options[] = array('id' => $term_id . '-2', 'name' => '2. kvartal', 'selected' => (($term_id . '-2' == $billing_term_selection) ? 1 : 0));
						$options[] = array('id' => $term_id . '-3', 'name' => '3. kvartal', 'selected' => (($term_id . '-3' == $billing_term_selection) ? 1 : 0));
						$options[] = array('id' => $term_id . '-4', 'name' => '4. kvartal', 'selected' => (($term_id . '-4' == $billing_term_selection) ? 1 : 0));
					}
					else if ($current == 3)
					{
						for ($i = 1; $i <= 12; $i++)
						{
							$options[] = array('id' => $term_id . '-' . $i, 'name' => lang('month ' . $i . ' capitalized'),
								'selected' => (($term_id . '-' . $i == $billing_term_selection) ? 1 : 0));
						}
					}
					else
					{
						$options[] = array('id' => $term_id . '-1', 'name' => lang($term_title), 'selected' => (($term_id . '-1' == $billing_term_selection) ? 1 : 0));
						$options[] = array('id' => $term_id . '-2', 'name' => 'Kreditering', 'selected' => (($term_id . '-2' == $billing_term_selection) ? 1 : 0));
						$options[] = array('id' => $term_id . '-3', 'name' => lang('positive one time'), 'selected' => (($term_id . '-3' == $billing_term_selection) ? 1 : 0));
					}
					$current++;
					$billing_term_group_options[] = array('label' => lang($term_title), 'options' => $options);
				}

				$data = array
					(
					'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_add),
					'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uibilling.index')),
					'lang_next' => lang('next'),
					'lang_cancel' => lang('cancel'),
					'contract_type' => $contract_type,
					'billing_term' => phpgw::get_var('billing_term'),
					'title' => phpgw::get_var('title'),
					'existing_billing' => $existing_billing,
					'fields_of_responsibility_label' => $fields_of_responsibility_label,
					'list_existing_billing' => array('options' => $existing_billing_options),
					'list_year' => array('options' => $year_options),
					'list_billing_term_group' => array('option_group' => $billing_term_group_options),
					'export_format' => $export_format,
					'errorMsgs' => $errorMsgs,
					'warningMsgs' => $warningMsgs,
					'infoMsgs' => $infoMsgs,
					'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
				);
				$template = 'step1';
			}

			$code = <<<JS
			var thousandsSeparator = '{$this->thousandsSeparator}';
			var decimalSeparator = '{$this->decimalSeparator}';
			var decimalPlaces = '{$this->decimalPlaces}';
			var currency_suffix = '{$this->currency_suffix}';
			var area_suffix = '{$this->area_suffix}';
JS;
			$GLOBALS['phpgw']->js->add_code('', $code);

			self::add_javascript('rental', 'rental', 'billing.add.js');
			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl(array('billing', 'datatable_inline'), array($template => $data));
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$field_of_responsibility_options = array();
			$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
			foreach ($fields as $id => $label)
			{
				$names = $this->locations->get_name($id);
				if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					if ($this->hasPermissionOn($names['location'], PHPGW_ACL_ADD))
					{
						$field_of_responsibility_options[] = array('id' => $id, 'name' => lang($label));
					}
				}
			}

			$data = array(
				'datatable_name' => lang('invoice_menu'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type' => 'filter',
								'name' => 'contract_type',
								'text' => lang('field_of_responsibility'),
								'list' => $field_of_responsibility_options
							),
							array(
								'type' => 'link',
								'value' => lang('create_billing'),
								'onclick' => 'onCreateBilling()',
								'class' => 'new_item'
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'rental.uibilling.index',
						'type' => 'all_billings',
						'phpgw_return_as' => 'json'
					)),
					'sorted_by'	=> array('key' => 4, 'dir' => 'desc'),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(
						array(
							'key' => 'description',
							'label' => lang('title'),
							'className' => '',
							'sortable' => false,
							'hidden' => false
						),
						array(
							'key' => 'responsibility_title',
							'label' => lang('contract_type'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'billing_info',
							'label' => lang('billing_terms'),
							'className' => '',
							'sortable' => false,
							'hidden' => false
						),
						array(
							'key' => 'total_sum',
							'label' => lang('sum'),
							'className' => 'right',
							'sortable' => true,
							'hidden' => false,
							'formatter' => 'formatterPrice'
						),
						array(
							'key' => 'timestamp_stop',
							'label' => lang('last_updated'),
							'className' => 'center',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'created_by',
							'label' => lang('run by'),
							'className' => '',
							'sortable' => false,
							'hidden' => false
						),
						array(
							'key' => 'timestamp_commit',
							'label' => lang('Commited'),
							'className' => 'center',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'other_operations',
							'label' => lang('other operations'),
							"className" => 'dt-center all',
							'sortable' => false,
							'hidden' => false
						)
					)
				)
			);

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'show',
				'text' => lang('show'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uibilling.view'
				)),
				'parameters' => json_encode($parameters)
			);

			$code = <<<JS
			var thousandsSeparator = '{$this->thousandsSeparator}';
			var decimalSeparator = '{$this->decimalSeparator}';
			var decimalPlaces = '{$this->decimalPlaces}';
			var currency_suffix = '{$this->currency_suffix}';
			var area_suffix = '{$this->area_suffix}';
JS;
			$GLOBALS['phpgw']->js->add_code('', $code);

			self::add_javascript('rental', 'rental', 'billing.index.js');
			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl('datatable_jquery', $data);
		}

		/**
		 * Displays info about one single billing job.
		 */
		public function view()
		{
			if (!$this->isExecutiveOfficer())
			{
				phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp']);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('invoice_run');

			$billing_job = rental_sobilling::get_instance()->get_single((int)phpgw::get_var('id'));
			$billing_info_array = rental_sobilling_info::get_instance()->get(0, 0, '', false, '', '', array(
				'billing_id' => phpgw::get_var('id')));

			if ($billing_job == null) // Not found
			{
				//$errorMsgs[] = lang('Could not find specified billing job.');
				phpgwapi_cache::message_set(lang('Could not find specified billing job.'), 'error');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uibilling.index'));
			}
			else if (phpgw::get_var('generate_export') != null) // User wants to generate export
			{
				$open_and_exported = rental_soinvoice::get_instance()->number_of_open_and_exported_rental_billings($billing_job->get_location_id());

				if ($open_and_exported == 0)
				{
					//Loop through  billing info array to find the first month
					$month = 12;
					foreach ($billing_info_array as $billing_info)
					{
						$year = $billing_info->get_year();
						if ($month > $billing_info->get_month())
						{
							$month = $billing_info->get_month();
						}
					}

					$billing_job->set_year($year);
					$billing_job->set_month($month);

					if (rental_sobilling::get_instance()->generate_export($billing_job))
					{
						//$infoMsgs[] = lang('Export generated.');
						phpgwapi_cache::message_set(lang('Export generated.'), 'message');
						$billing_job->set_generated_export(true); // The template need to know that we've genereated the export
					}
					else
					{
						//$errorMsgs = lang('Export failed.');
						phpgwapi_cache::message_set(lang('Export failed.'), 'error');
					}
				}
				else
				{
					//$errorMsgs[] = lang('open_and_exported_exist');
					phpgwapi_cache::message_set(lang('open_and_exported_exist'), 'error');
				}
			}
			else if (phpgw::get_var('commit') != null) // User wants to commit/close billing so that it cannot be deleted
			{
				$billing_job->set_timestamp_commit(time());
				rental_sobilling::get_instance()->store($billing_job);
			}

			$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
			foreach ($fields as $id => $label)
			{
				if ($id == $billing_job->get_location_id())
				{
					$contract_type = lang($label);
				}
			}

			$billing_terms = array();
			if ($billing_info_array != null)
			{
				foreach ($billing_info_array as $billing_info)
				{
					if ($billing_info->get_term_id() == 1)
					{
						$billing_terms[] = lang('month ' . $billing_info->get_month() . ' capitalized') . " " . $billing_info->get_year();
					}
					else
					{
						$billing_terms[] = $billing_info->get_term_label() . " " . $billing_info->get_year();
					}
				}
			}

			$sum = number_format($billing_job->get_total_sum(), $this->decimalPlaces, $this->decimalSeparator, $this->thousandsSeparator) . ' ' . $this->currency_suffix;
			$last_updated = $GLOBALS['phpgw']->common->show_date($billing_job->get_timestamp_stop(), $this->dateFormat . ' H:i:s');
			$timestamp_commit = $billing_job->get_timestamp_commit();

			if (empty($timestamp_commit))
			{
				$timestamp_commit = lang('No');
			}
			else
			{
				$timestamp_commit = $GLOBALS['phpgw']->common->show_date($timestamp_commit, $this->dateFormat . ' H:i:s');
			}

			$tabletools[] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.view'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'contract_id'))))
			);
			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.edit'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'contract_id'))))
			);
			$tabletools[] = array
				(
				'my_name' => 'download',
				'text' => lang('download'),
				'download' => self::link(array('menuaction' => 'rental.uibilling.download',
					'billing_id' => $billing_job->get_id(),
					'type' => 'invoices',
					'export' => true,
					'allrows' => true))
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uibilling.query',
						'type' => 'invoices', 'editable' => true, 'billing_id' => $billing_job->get_id(),
						'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => array(
					array('key' => 'old_contract_id', 'label' => lang('contract_id'), 'className' => '',
						'sortable' => true, 'hidden' => false),
					array('key' => 'term_label', 'label' => lang('billing_term'), 'className' => '',
						'sortable' => true, 'hidden' => false),
					array('key' => 'composite_name', 'label' => lang('composite_name'), 'className' => '',
						'sortable' => true, 'hidden' => false),
					array('key' => 'party_name', 'label' => lang('party_name'), 'className' => '',
						'sortable' => true, 'hidden' => false),
					array('key' => 'total_sum', 'label' => lang('Total sum'), 'className' => 'right',
						'sortable' => true, 'hidden' => false, 'formatter' => 'formatterPrice'),
					array('key' => 'serial_number', 'label' => lang('serial_number'), 'className' => 'center',
						'sortable' => true, 'hidden' => false)
				),
				'data' => json_encode(array()),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true)
				)
			);

			$tabs = array();
			$tabs['details'] = array('label' => lang('Details'), 'link' => '#details');
			$active_tab = 'details';

			$download_link = self::link(array(
					'menuaction' => 'rental.uibilling.download_export',
					'id' => $billing_job->get_id(),
					'date' => $billing_job->get_timestamp_stop(),
					'export_format' => $billing_job->get_export_format())
			);
			$download_link_bk = self::link(array(
					'menuaction' => 'rental.uibilling.download_export',
					'id' => $billing_job->get_id(),
					'date' => $billing_job->get_timestamp_stop(),
					'export_format' => $billing_job->get_export_format(),
					'toExcel' => true,
					'type' => 'bk'
			));
			$download_link_nlsh = self::link(array(
					'menuaction' => 'rental.uibilling.download_export',
					'id' => $billing_job->get_id(),
					'date' => $billing_job->get_timestamp_stop(),
					'export_format' => $billing_job->get_export_format(),
					'toExcel' => true,
					'type' => 'nlsh'
			));
			$download_link_cs15 = self::link(array(
					'menuaction' => 'rental.uibilling.download_export',
					'id' => $billing_job->get_id(),
					'date' => $billing_job->get_timestamp_stop(),
					'generate_cs15' => true
			));

			$data = array
				(
				'datatable_def' => $datatable_def,
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uibilling.index')),
				'lang_cancel' => lang('cancel'),
				'contract_type' => $contract_type,
				'billing_terms' => $billing_terms,
				'sum' => $sum,
				'last_updated' => $last_updated,
				'commited' => $timestamp_commit,
				'success' => $billing_job->is_success() ? lang('yes') : lang('no'),
				'export_format' => lang($billing_job->get_export_format()),
				'has_generated_export' => ($billing_job->has_generated_export()) ? 1 : 0,
				'is_commited' => ($billing_job->is_commited()) ? 1 : 0,
				'download_link' => $download_link,
				'download_link_bk' => $download_link_bk,
				'download_link_nlsh' => $download_link_nlsh,
				'download_link_cs15' => $download_link_cs15,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			$code = <<<JS
			var thousandsSeparator = '{$this->thousandsSeparator}';
			var decimalSeparator = '{$this->decimalSeparator}';
			var decimalPlaces = '{$this->decimalPlaces}';
			var currency_suffix = '{$this->currency_suffix}';
			var area_suffix = '{$this->area_suffix}';

			function formatterPrice (key, oData)
			{
				var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + currency_suffix;
				return amount;
			}
JS;
			$GLOBALS['phpgw']->js->add_code('', $code);

			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl(array('billing', 'datatable_inline'), array('view' => $data));
		}

		/**
		 * Deletes an uncommited billing job.
		 */
		public function delete()
		{
			if (!$this->isExecutiveOfficer())
			{
				phpgw::no_access();
			}
			$billing_id = phpgw::get_var('id');
			rental_sobilling::get_instance()->transaction_begin();
			$billing_job = rental_sobilling::get_instance()->get_single((int)phpgw::get_var('id'));
			$billing_job->set_deleted(true);
			$result = rental_sobilling::get_instance()->store($billing_job);

			//set deleted=true on billing_info
			$billing_infos = rental_sobilling_info::get_instance()->get(0, 0, '', false, '', '', array(
				'billing_id' => phpgw::get_var('id')));
			foreach ($billing_infos as $billing_info)
			{
				$billing_info->set_deleted(true);
				rental_sobilling_info::get_instance()->store($billing_info);
			}

			//set is_billed on invoice price items to false
			$billing_job_invoices = rental_soinvoice::get_instance()->get(0, 0, '', false, '', '', array(
				'billing_id' => phpgw::get_var('id')));
			foreach ($billing_job_invoices as $invoice)
			{
				$price_items = rental_socontract_price_item::get_instance()->get(0, 0, '', false, '', '', array(
					'contract_id' => $invoice->get_contract_id(), 'one_time' => true, 'include_billed' => true));
				foreach ($price_items as $price_item)
				{
					//Check for credit or valid date
//					if (($price_item->get_is_one_time() && $price_item->get_total_price() < 0) || ($price_item->get_date_start() >= $invoice->get_timestamp_start() && $price_item->get_date_start() <= $invoice->get_timestamp_end()))
					if ($price_item->get_billing_id() == $billing_id)
					{
						$price_item->set_is_billed(false);
						rental_socontract_price_item::get_instance()->store($price_item);
					}
				}
				$invoice->set_serial_number(null);
				rental_soinvoice::get_instance()->store($invoice);
			}
			rental_sobilling::get_instance()->transaction_commit();

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				/* if ($result) {
				  $message['message'][] = array('msg'=>$billing_job->get_title().' '.lang('has been removed'));
				  } else {
				  $message['error'][] = array('msg'=>$billing_job->get_title().' '.lang('not removed'));
				  } */

				$message['message'][] = array('msg' => $billing_job->get_title() . ' ' . lang('has been removed'));
				return $message;
			}
		}

		/**
		 * Commits a billing job. After it's commited it cannot be deleted.
		 */
		public function commit()
		{
			if (!$this->isExecutiveOfficer())
			{
				phpgw::no_access();
			}

			$id = phpgw::get_var('id', 'int');

			rental_sobilling::get_instance()->transaction_begin();

			$billing_job = rental_sobilling::get_instance()->get_single($id);
			$export_format = $billing_job->get_export_format();

			$result_transfer = $this->transfer($id, $export_format);
			$result = null;

			if($result_transfer['transfer_ok'])
			{
				$billing_job->set_timestamp_commit(time());
				$billing_job->set_voucher_id($result_transfer['voucher_id']);
				$result = rental_sobilling::get_instance()->store($billing_job);
				rental_sobilling::get_instance()->transaction_commit();
			}
			else
			{
				/**
				 * Revert voucher increment
				 */
				rental_sobilling::get_instance()->transaction_abort();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				if($result)
				{
					$this->message['message'][] = array('msg' => $billing_job->get_title() . ' ' . lang('has been committed'));
				}
				else
				{
					$this->message['error'][] = array('msg'=>$billing_job->get_title() .' '. lang('not committed'));
				}

				return $this->message;
			}
		}

		private function transfer( $id, $export_format )
		{
			$voucher_id = null;
			$export_format_arr = explode('_', $export_format);
			$file_ending = $export_format_arr[1];
			if ($file_ending == 'gl07')
			{
				$type = 'intern';
			}
			else if ($file_ending == 'lg04')
			{
				$type = 'faktura';
			}
			$date = date('Ymd', $stop);
			$config_rental = CreateObject('phpgwapi.config', 'rental')->read();
			$organization = empty($config_rental['organization']) ? 'bergen' : $config_rental['organization'];
			if($organization == 'nlsh')
			{
				/**
				 * For now...not activated.
				 */
				if($type == 'intern')
				{
					return true;
				}

				/**
				 * Get voucher number
				 */
				$voucher_id = rental_sobilling::get_instance()->increment_id('faktura_buntnr');

				$old_buntnr =  'PU' . sprintf("%08s",$id);
				$new_buntnr =  'PU' . sprintf("%08s",$voucher_id);

				$filename = '14PU' . sprintf("%08s",$voucher_id) . ".txt";
			}
			else
			{
				$filename = "PE_{$type}_{$date}.{$file_ending}";
			}

			$path = "/rental/billings/{$id}";

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$content = $vfs->read(
				array
				(
					'string' => $path,
					'relatives' => array( RELATIVE_NONE)
				)
			);

			if(!empty($new_buntnr))
			{
				$content = str_replace($old_buntnr, $new_buntnr, $content);
			}

			if(empty($content))
			{
				$this->message['error'][] = array('msg' => lang('transfer failed'));
				return false;
			}

			$transfer_ok = false;

			$config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));

			if ($config->config_data['common']['method'] == 'ftp' || $config->config_data['common']['method'] == 'ssh')
			{
				$connection = $this->phpftp_connect( $config );

				$basedir = $config->config_data['export']['remote_basedir'];
				if ($basedir)
				{
					$remote_file = $basedir . '/' . basename($filename);
				}
				else
				{
					$remote_file = basename($filename);
				}

				switch ($config->config_data['common']['method'])
				{
					case 'ftp';
						$tmpfname = tempnam(sys_get_temp_dir());
						$handle = fopen($tmpfname, "w");
						fwrite($handle, $content);
						fclose($handle);
						$transfer_ok = ftp_put($connection, $remote_file, $tmpfname, FTP_BINARY);
						unlink($tmpfname);
						break;
					case 'ssh';
						$transfer_ok = $connection->write(basename($filename), $content);
						break;
					default:
						$transfer_ok = false;
				}
				if ($transfer_ok)
				{
					$this->message['message'][] = array('msg' => basename($filename) . ' ' . lang('has been transferred'));
				}
				else
				{
					$this->message['error'][] = array('msg' => lang('transfer failed'));
				}
			}
			else
			{
				$this->message['error'][] = array('msg' => lang('transfer is not configured'));
				$transfer_ok = true;
			}
			return array(
				'transfer_ok' => $transfer_ok,
				'voucher_id'	=> $voucher_id
				);
		}

		protected function phpftp_connect($config)
		{
			$server = $config->config_data['common']['host'];
			$user = $config->config_data['common']['user'];
			$password = $config->config_data['common']['password'];
			$basedir = $config->config_data['export']['remote_basedir'];
			$port = 22;

			$connection = null;

			switch ($config->config_data['common']['method'])
			{
				case 'ftp';
					if ($connection = ftp_connect($server))
					{
						ftp_login($connection, $user, $password);
					}
					break;
				case 'ssh';
					$connection = new Filesystem(new SftpAdapter([
						'host' => $server,
						'port' => $port,
						'username' => $user,
						'password' => $password,
						'root' => $basedir,
						'timeout' => 10,
					]));
					break;
			}
			return $connection;
		}

		public function query()
		{
			if (!$this->isExecutiveOfficer())
			{
				phpgw::no_access();
			}
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$start_index = (int)phpgw::get_var('start', 'int' );
			$num_of_objects = (phpgw::get_var('length', 'int') <= 0) ? $user_rows_per_page : phpgw::get_var('length', 'int');
			$sort_field = ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'id';
			$sort_ascending = ($order[0]['dir'] == 'desc') ? false : true;
			// Form variables
			$search_for = (string)$search['value'];
			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', 'all');

			// Create an empty result set
			$result_objects = array();
			$object_count = 0;
			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');

			$export = phpgw::get_var('export', 'bool');
			if ($export)
			{
				$num_of_objects = 0;
			}

			switch ($query_type)
			{
				case 'all_billings':
					$filters = array();
					if (!$sort_field)
					{
						$sort_field = 'timestamp_stop';
						$sort_ascending = false;
					}
					else if ($sort_field == 'responsibility_title')
					{
						$sort_field = 'location_id';
					}
					$result_objects = rental_sobilling::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_sobilling::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'invoices':
					if ($sort_field == 'term_label')
					{
						$sort_field = 'term_id';
					}
					$filters = array('billing_id' => phpgw::get_var('billing_id'));
					$result_objects = rental_soinvoice::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_soinvoice::get_instance()->get_count($search_for, $search_type, $filters);
					break;
			}

			//Create an empty row set
			$rows = array();
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
					if ($result->has_permission(PHPGW_ACL_READ))
					{
						// ... add a serialized result
						$rows[] = $result->serialize();
					}
				}
			}

			if (!$export)
			{
				//Add action column to each row in result table
				array_walk($rows, array($this, 'add_actions'), array($query_type));
			}

			if ($export)
			{
				return $rows;
			}

			$result_data = array('results' => $rows);
			$result_data['total_records'] = $object_count;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * Add action links and labels for the context menu of the list items
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [composite_id, type of query, editable]
		 */
		public function add_actions( &$value, $key, $params )
		{
			$value['other_operations'] = null;
			$query_type = $params[0];

			switch ($query_type)
			{
				case 'all_billings':
					if (empty($value['timestamp_commit']))
					{
						$url_delete = html_entity_decode(self::link(array('menuaction' => 'rental.uibilling.delete',
								'id' => $value['id'], 'phpgw_return_as' => 'json')));
						$actions[] = '<a href="#" onclick="onDelete(\'' . $url_delete . '\')">' . lang('delete') . '</a>';

						$url_commit = html_entity_decode(self::link(array('menuaction' => 'rental.uibilling.commit',
								'id' => $value['id'], 'phpgw_return_as' => 'json')));
						$actions[] = '<a href="#" onclick="onCommit(\'' . $url_commit . '\')">' . lang('commit') . '</a>';

						$value['other_operations'] = implode(' | ', $actions);
					}
					break;
			}
		}

		public function download_export()
		{
			if (!$this->isExecutiveOfficer())
			{
				phpgw::no_access();
			}
			//$browser = CreateObject('phpgwapi.browser');
			//$browser->content_header('export.txt','text/plain');

			$id = phpgw::get_var('id', 'int');
			$billing_job = rental_sobilling::get_instance()->get_single($id);

			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();
			$organization = empty($config->config_data['organization']) ? 'bergen' : $config->config_data['organization'];

			$stop = phpgw::get_var('date');

			$cs15 = phpgw::get_var('generate_cs15');
			$toExcel = phpgw::get_var('toExcel');
			if ($cs15 == null)
			{
				if ($toExcel == null)
				{
					$export_format = explode('_', phpgw::get_var('export_format'));
					$file_ending = $export_format[1];
					if ($file_ending == 'gl07')
					{
						$type = 'intern';
					}
					else if ($file_ending == 'lg04')
					{
						$type = 'faktura';
					}
					$date = date('Ymd', $stop);
					if($organization == 'nlsh')
					{
						$voucher_id = $billing_job->get_voucher_id();
						$_id = $voucher_id ? $voucher_id : $id;
//						$filename = '14PU' . sprintf("%08s",$_id) . ".{$file_ending}";
						$filename = '14PU' . sprintf("%08s",$_id) . ".txt";
						$old_buntnr =  'PU' . sprintf("%08s",$id);
						$new_buntnr =  'PU' . sprintf("%08s",$_id);
					}
					else
					{
						$filename = "PE_{$type}_{$date}.{$file_ending}";
					}

					header('Content-type: text/plain');
					header("Content-Disposition: attachment; filename={$filename}");

					$path = "/rental/billings/{$id}";

					$vfs = CreateObject('phpgwapi.vfs');
					$vfs->override_acl = 1;

					$content = $vfs->read(
							array
							(
								'string' => $path,
								'relatives' => array( RELATIVE_NONE)
							)
					);

					if(!empty($new_buntnr))
					{
						$content = str_replace($old_buntnr, $new_buntnr, $content);
					}
					echo $content;
				}
				else
				{
					$billing_info_array = rental_sobilling_info::get_instance()->get(0, 0, '', false, '', '', array(
						'billing_id' => phpgw::get_var('id')));
					$type = phpgw::get_var('type', 'string', 'GET', 'bk');
					if ($billing_job == null) // Not found
					{
						$errorMsgs[] = lang('Could not find specified billing job.');
					}
					else
					{
						//Loop through  billing info array to find the first month
						$month = 12;
						foreach ($billing_info_array as $billing_info)
						{
							$year = $billing_info->get_year();
							if ($month > $billing_info->get_month())
							{
								$month = $billing_info->get_month();
							}
						}

						$billing_job->set_year($year);
						$billing_job->set_month($month);

						$list = rental_sobilling::get_instance()->generate_export($billing_job, $type);
						//_debug_array($list[0]);
						/* foreach ($list as $l)
						  {
						  _debug_array($l);
						  } */

						if (isset($list))
						{
							$infoMsgs[] = lang('Export generated.');

							$keys = array();

							if (count($list[0]) > 0)
							{
								foreach ($list[0] as $key => $value)
								{
									if (!is_array($value))
									{
										array_push($keys, $key);
									}
								}
							}

							// Remove newlines from output
//                                    $count = count($list);
//                                    for($i = 0; $i < $count; $i++)
//                                    {
//                                        foreach ($list[$i] as $key => &$data)
//                                        {
//                                                $data = str_replace(array("\n","\r\n", "<br>"),'',$data);
//                                        }
//                                    }
							// Use keys as headings
							$headings = array();
							$count_keys = count($keys);
							for ($j = 0; $j < $count_keys; $j++)
							{
								array_push($headings, ltrim(lang($keys[$j]), '!'));
							}

//                                    _debug_array($list);

							$property_common = CreateObject('property.bocommon');
							$property_common->download($list, $keys, $headings);
						}
						else
						{
							$errorMsgs = lang('Export failed.');
						}
					}
				}
			}
			else
			{
				$file_ending = 'cs15';
				$type = 'kundefil';
				$date = date('Ymd', $stop);
				header('Content-type: text/plain');
				header("Content-Disposition: attachment; filename=PE_{$type}_{$date}.{$file_ending}");
				print rental_sobilling::get_instance()->generate_customer_export((int)phpgw::get_var('id'));
			}
		}
	}