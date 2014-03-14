<?php
phpgw::import_class('rental.uicommon');
phpgw::import_class('rental.sobilling');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.soinvoice');
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'billing', 'inc/model/');

class rental_uibilling extends rental_uicommon
{
	public $public_functions = array
	(
		'index'     		=> true,
		'query'			    => true,
		'view'			    => true,
		'delete'			=> true,
		'commit'			=> true,
		'download'			=> true,
		'download_export'	=> true
	);
	
	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('rental::contracts::invoice');
		$config	= CreateObject('phpgwapi.config','rental');
		$config->read();
		$billing_time_limit = $config->config_data['billing_time_limit'];
		set_time_limit($billing_time_limit); // Set time limit
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('invoice_menu');
	}
	
	public function index()
	{
		// No messages so far
		$errorMsgs = array();
		$warningMsgs = array();
		$infoMsgs = array();
		$step = null; // Used for overriding the user's selection and choose where to go by code
		
		// Step 3 - the billing job
		if(phpgw::get_var('step') == '2' && phpgw::get_var('next') != null) // User clicked next on step 2
		{
			$use_existing = phpgw::get_var('use_existing');
			$existing_billing = phpgw::get_var('existing_billing');
			if($use_existing < 1){
				$existing_billing = 0;
			}
			$contract_ids = phpgw::get_var('contract'); // Ids of the contracts to bill
			
			$contract_ids_override = phpgw::get_var('override_start_date'); //Ids of the contracts that should override billing start date with first day in period
			$contract_bill_only_one_time = phpgw::get_var('bill_only_one_time');
			if(($contract_ids != null && is_array($contract_ids) && count($contract_ids) > 0) || (isset($contract_bill_only_one_time) && is_array($contract_bill_only_one_time) && count($contract_bill_only_one_time) > 0)) // User submitted contracts to bill
			{
				$missing_billing_info = rental_sobilling::get_instance()->get_missing_billing_info(phpgw::get_var('billing_term'), phpgw::get_var('year'), phpgw::get_var('month'), $contract_ids, $contract_ids_override, phpgw::get_var('export_format'));
				
				if($missing_billing_info == null || count($missing_billing_info) == 0)
				{
					$billing_job = rental_sobilling::get_instance()->create_billing(isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) ? isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) : 2, phpgw::get_var('contract_type'), phpgw::get_var('billing_term'), phpgw::get_var('year'), phpgw::get_var('month'), phpgw::get_var('title'), $GLOBALS['phpgw_info']['user']['account_id'], $contract_ids, $contract_ids_override, phpgw::get_var('export_format'), $existing_billing, $contract_bill_only_one_time);
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uibilling.view', 'id' => $billing_job->get_id()));
					return;
				}
				else // Incomplete biling info
				{
					foreach($missing_billing_info as $contract_id => $info_array)
					{
						if($info_array != null && count($info_array) > 0)
						{
							$errorMsgs[] = lang('Missing billing information.', $contract_id);
							foreach($info_array as $info)
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
		if($step == 2 || (phpgw::get_var('step') == '1' && phpgw::get_var('next') != null) || phpgw::get_var('step') == '3' && phpgw::get_var('previous') != null) // User clicked next on step 1 or previous on step 3
		{
			//Responsibility area
			$contract_type = phpgw::get_var('contract_type');
			
			//Check permission
			$names = $this->locations->get_name($contract_type);
			if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
			{
				if(!$this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
				{
					$this->render('permission_denied.php');
					return;
				}
			}
			
			//Get year
			$year = phpgw::get_var('year');
			
			//Get term and month
			if($step == 2){
				$billing_term_tmp = phpgw::get_var('billing_term_selection');
			}
			else{
				$billing_term_tmp = phpgw::get_var('billing_term');
			}
			$billing_term_selection = $billing_term_tmp;
			$billing_term = substr($billing_term_tmp,0,1);
			$billing_month = substr($billing_term_tmp,2);
			
			if($billing_term == '1'){ // monthly
				$month = $billing_month;
			}
			else if($billing_term == '4'){ // quarterly
				if($billing_month == '1'){ //1. quarter
					$month = 3;
					$billing_term_label = lang('first_quarter');
				}
				else if($billing_month == '2'){ //2. quarter
					$month = 6;
					$billing_term_label = lang('second_quarter');
				}
				else if($billing_month == '3'){ //3. quarter
					$month = 9;
					$billing_term_label = lang('third_quarter');
				}
				else{ //4. quarter
					$month = 12;
					$billing_term_label = lang('fourth_quarter');
				}
			}
			else if($billing_term == '3'){ // half year
				if($billing_month == '1'){
					$month = 6;
					$billing_term_label = lang('first_half');
				}
				else{
					$month = 12;
					$billing_term_label = lang('second_half');
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
			if($existing_billing != 'new_billing'){
				$use_existing = true;
			}
			
			//Determine title
			$title = phpgw::get_var('title');
			if(!isset($title) || $title == ''){
				$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach($fields as $id => $label)
				{
					if($id == $contract_type)
					{
						$description = lang($label) . ' ';
					}
				}
				$description .= lang('month ' . $month) . ' ';
				$description .= $year;
				$title = $description;
			}
			
			if($use_existing){
				$billing_tmp = rental_sobilling::get_instance()->get_single($existing_billing);
				$title = $billing_tmp->get_title();
			}
			
					
			//Check to see if the period har been billed before
			if(rental_sobilling::get_instance()->has_been_billed($contract_type, $billing_term, $year, $month)) // Checks if period has been billed before
			{	
				// We only give a warning and let the user go to step 2
				$warningMsgs[] = lang('the period has been billed before.');
			}
			else
			{
				//... and if not start retrieving contracts for billing
			
				$socontract_price_item = rental_socontract_price_item::get_instance();
				
				//... 1. Contracts following regular billing cycle
				$filters = array('contracts_for_billing' => true, 'contract_type' => $contract_type, 'billing_term_id' => $billing_term, 'year' => $year, 'month' => $month);
				$contracts = rental_socontract::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				
				//... 2. Contracts with one-time price items
				$filters2 = array('contract_ids_one_time' => true, 'billing_term_id' => $billing_term, 'year' => $year, 'month' => $month);
				$contract_price_items = $socontract_price_item->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters2);
				
				foreach($contract_price_items as $contract_price_item){
					if(!array_key_exists($contract_price_item->get_contract_id(), $contracts)){
						$aditional_contracts = rental_socontract::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $contract_price_item->get_contract_id(), 'contract_type' => $contract_type));
						if(count($aditional_contracts) == 1){
							$c = $aditional_contracts[$contract_price_item->get_contract_id()];
							$c->set_bill_only_one_time();
							//$contracts[$contract_price_item->get_contract_id()] = $c;
							$contracts_with_one_time[$contract_price_item->get_contract_id()] = $c; // used for information purposes
						}
					}
					else
					{
						$cid = $contract_price_item->get_contract_id();
						$contracts_with_one_time[$cid] = $contracts[$cid];
					}
				}
		
			
				// Get the number of months in selected term for contract
				$months = rental_socontract::get_instance()->get_months_in_term($billing_term);
				
				// The billing should start from the first date of the periode (term) we're billing for
				$first_day_of_selected_month = strtotime($year . '-' . $month . '-01');
				$bill_from_timestamp = strtotime('-'.($months-1).' month', $first_day_of_selected_month); 
				
				foreach($contracts as $id => $contract)
				{	
					if(isset($contract))
					{
						$total_price = $socontract_price_item->get_total_price_invoice($contract->get_id(), $billing_term, $month, $year);
						$type_id = $contract->get_contract_type_id();
						$responsible_type_id = $contract->get_location_id();
						
						// Gets location title from table rental_contract_responsibility
						$location_title = rental_socontract::get_instance()->get_responsibility_title($responsible_type_id);
						
						if($type_id == 4) // Remove contract of a specific type (KF)
						{
							$warningMsgs[] = lang('billing_removed_KF_contract') . " " . $contract->get_old_contract_id();
							unset($contracts[$id]);
							$removed_contracts[$contract->get_id()] = $contract;
						}
						// A contract with responibility type contract_type_eksternleie must have a rental_contract_type 
						else if( ($type_id == 0 && strcmp($location_title, "contract_type_eksternleie") == 0) || (empty($type_id) && strcmp($location_title, "contract_type_eksternleie") == 0 )) 
						{
							$contract->set_total_price($total_price);
							$warningMsgs[] = lang('billing_removed_contract_part_1') . " " . $contract->get_old_contract_id() . " " . lang('billing_removed_external_contract');
							unset($contracts[$id]);
							$removed_contracts[$contract->get_id()] = $contract;
						} 
						else if(isset($total_price) && $total_price == 0) // Remove contract if total price is equal to zero
						{
							$warningMsgs[] = lang('billing_removed_contract_part_1') . " " . $contract->get_old_contract_id() . " " . lang('billing_removed_contract_part_2');
							unset($contracts[$id]);
							$removed_contracts[$id] = $contract;
						}
						else // Prepare contract for billing
						{
							$contract->set_total_price($total_price);
							
							// Find the last day of the last period the contract was billed before the specified date
							$last_bill_timestamp = $contract->get_last_invoice_timestamp($bill_from_timestamp); 
							
							// If the contract has not been billed before, select the billing start date
							if($last_bill_timestamp == null) 
							{
								$next_bill_timestamp = $contract->get_billing_start_date();
								$not_billed_contracts[$id] = $contract;
								$irregular_contracts[$id] = $contract;
								unset($contracts[$id]);
							}
							else
							{ 
								// ... select the next that day that the contract should be billed from
								$next_bill_timestamp = strtotime('+1 day', $last_bill_timestamp);
								$contract->set_next_bill_timestamp($next_bill_timestamp);
								
								// The next time the contract should be billed from equals the first day of the current selected period
								if($next_bill_timestamp == $bill_from_timestamp) 
								{
									//The contract follows the regular billing cycle
								} 
								else
								{
									unset($contracts[$id]);
									$irregular_contracts[$id] = $contract;
								}
							}
							
						}
					}
				}
			}
				
			$data = array
			(
				'contracts' => $contracts,
				'irregular_contracts' => $irregular_contracts,
				'removed_contracts'	=> $removed_contracts,
				'not_billed_contracts'	=> $not_billed_contracts,
				'contracts_with_one_time' => $contracts_with_one_time,
				'bill_from_timestamp' => $bill_from_timestamp,
				'contract_type' => phpgw::get_var('contract_type'),
				'billing_term' => $billing_term,
				'billing_term_label' => $billing_term_label,
				'billing_term_selection' => $billing_term_selection,
				'year' => $year,
				'month' => $month,
				'title' => $title,
				'use_existing' => $use_existing,
				'existing_billing' => $existing_billing,
				'export_format'	=> phpgw::get_var('export_format'),
				'errorMsgs' => $errorMsgs,
				'warningMsgs' => $warningMsgs,
				'infoMsgs' => $infoMsgs
			);
			$this->render('billing_step2.php', $data);
		}
		else if($step == 1 || (phpgw::get_var('step') == '0' && phpgw::get_var('next') != null) || phpgw::get_var('step') == '2' && phpgw::get_var('previous') != null) // User clicked next on step 0 or previous on step 2
		{
				$contract_type = phpgw::get_var('contract_type');
				$export_format = rental_sobilling::get_instance()->get_agresso_export_format($contract_type);
				$data = array
				(
					'contract_type' => phpgw::get_var('contract_type'),
					'billing_term' => phpgw::get_var('billing_term'),
					'billing_term_selection' => phpgw::get_var('billing_term_selection'),
					'title' => phpgw::get_var('title'),
					'year' => phpgw::get_var('year'),
					'existing_billing' => phpgw::get_var('existing_billing'),
					'export_format' => $export_format,
					'errorMsgs' => $errorMsgs,
					'warningMsgs' => $warningMsgs,
					'infoMsgs' => $infoMsgs
				);
				$this->render('billing_step1.php', $data);
		}
		// Step 0 - List all billing jobs
		else
		{
		
			$data = array
			(
				'contract_type' => phpgw::get_var('contract_type'),
				'billing_term' => phpgw::get_var('billing_term'),
				'year' => phpgw::get_var('year'),
				'month' => phpgw::get_var('month'),
				'errorMsgs' => $errorMsgs,
				'warningMsgs' => $warningMsgs,
				'infoMsgs' => $infoMsgs
			);
			$this->render('billing_step0.php', $data);
		}
	}
	
	/**
	 * Displays info about one single billing job.
	 */
	public function view()
	{
		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}

		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('invoice_run');

		$errorMsgs = array();
		$infoMsgs = array();
		$billing_job = rental_sobilling::get_instance()->get_single((int)phpgw::get_var('id'));
		$billing_info_array = rental_sobilling_info::get_instance()->get(null, null, null, null, null, null, array('billing_id' => phpgw::get_var('id')));
		
		if($billing_job == null) // Not found
		{
			$errorMsgs[] = lang('Could not find specified billing job.');
		}
		else if(phpgw::get_var('generate_export') != null) // User wants to generate export
		{
		
			$open_and_exported = rental_soinvoice::get_instance()->number_of_open_and_exported_rental_billings($billing_job->get_location_id());
			
			if($open_and_exported == 0)
			{
				//Loop through  billing info array to find the first month
				$month = 12;
				foreach($billing_info_array as $billing_info)
				{
					$year = $billing_info->get_year();
					if($month > $billing_info->get_month())
					{
						$month = $billing_info->get_month();
					}
				}
				
				$billing_job->set_year($year);
				$billing_job->set_month($month);
				
				if(rental_sobilling::get_instance()->generate_export($billing_job))
				{
					$infoMsgs[] = lang('Export generated.');
					$billing_job->set_generated_export(true); // The template need to know that we've genereated the export
				}
				else
				{
					$errorMsgs = lang('Export failed.');
				}
			}
			else
			{
				$errorMsgs[] = lang('open_and_exported_exist');
			}
		}
		else if(phpgw::get_var('commit') != null) // User wants to commit/close billing so that it cannot be deleted
		{
			$billing_job->set_timestamp_commit(time());
			rental_sobilling::get_instance()->store($billing_job);
		}
		$data = array
		(
			'billing_job' => $billing_job,
			'billing_info_array' => $billing_info_array,
			'errorMsgs' => $errorMsgs,
			'infoMsgs' => $infoMsgs,
			'back_link' => html_entity_decode(self::link(array('menuaction' => 'rental.uibilling.index'))),
			'download_link' => html_entity_decode(self::link(array('menuaction' => 'rental.uibilling.download_export', 'id' => (($billing_job != null) ? $billing_job->get_id() : ''), 'date' => $billing_job->get_timestamp_stop(), 'export_format' => $billing_job->get_export_format())))
		);
		$this->render('billing.php', $data);
	}
	
	/**
	 * Deletes an uncommited billing job.
	 */
	public function delete()
	{
		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
		$billing_job = rental_sobilling::get_instance()->get_single((int)phpgw::get_var('id'));
		$billing_job->set_deleted(true);
		rental_sobilling::get_instance()->store($billing_job);
		
		//set deleted=true on billing_info
		$billing_infos = rental_sobilling_info::get_instance()->get(null, null, null, null, null, null, array('billing_id' => phpgw::get_var('id')));
		foreach($billing_infos as $billing_info){
			$billing_info->set_deleted(true);
			rental_sobilling_info::get_instance()->store($billing_info);
		}
		
		//set is_billed on invoice price items to false
		$billing_job_invoices = rental_soinvoice::get_instance()->get(null, null, null, null, null, null, array('billing_id' => phpgw::get_var('id')));
		foreach($billing_job_invoices as $invoice){
			$price_items = rental_socontract_price_item::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $invoice->get_contract_id(), 'one_time' => true));
			foreach($price_items as $price_item){
				if($price_item->get_date_start() >= $invoice->get_timestamp_start() && $price_item->get_date_start() <= $invoice->get_timestamp_end()){
					$price_item->set_is_billed(false);
					rental_socontract_price_item::get_instance()->store($price_item);
				}
			}
			$invoice->set_serial_number(null);
			rental_soinvoice::get_instance()->store($invoice);
		}
	}
	
	/**
	 * Commits a billing job. After it's commited it cannot be deleted.
	 */
	public function commit()
	{
		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}			
		$billing_job = rental_sobilling::get_instance()->get_single((int)phpgw::get_var('id'));
		$billing_job->set_timestamp_commit(time());
		rental_sobilling::get_instance()->store($billing_job);
	}
	
	public function query()
	{
		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
		if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
		{
			$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
		}
		else {
			$user_rows_per_page = 10;
		}
		// YUI variables for paging and sorting
		$start_index	= phpgw::get_var('startIndex', 'int');
		$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
		$sort_field		= phpgw::get_var('sort');
		$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
		// Form variables
		$search_for 	= phpgw::get_var('query');
		$search_type	= phpgw::get_var('search_option');
		// Create an empty result set
		$result_objects = array();
		$result_count = 0;
		//Retrieve the type of query and perform type specific logic
		$query_type = phpgw::get_var('type');
		
		$exp_param 	= phpgw::get_var('export');
		$export = false;
		if(isset($exp_param)){
			$export=true;
			$num_of_objects = null;
		}
		
		switch($query_type)
		{
			case 'all_billings':
				$filters = array();
				if($sort_field == 'responsibility_title'){
					$sort_field = 'location_id';
				}
				$result_objects = rental_sobilling::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_sobilling::get_instance()->get_count($search_for, $search_type, $filters);
				break;
			case 'invoices':
				if($sort_field == 'term_label'){
					$sort_field = 'term_id';
				}
				$filters = array('billing_id' => phpgw::get_var('billing_id'));
				$result_objects = rental_soinvoice::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_soinvoice::get_instance()->get_count($search_for, $search_type, $filters);
				break;
		}
		
		//Create an empty row set
		$rows = array();
		foreach($result_objects as $result) {
			if(isset($result))
			{
				if($result->has_permission(PHPGW_ACL_READ))
				{
					// ... add a serialized result
					$rows[] = $result->serialize();
				}
			}
		}
		
		// ... add result data
		$result_data = array('results' => $rows, 'total_records' => $object_count);
		
		if(!$export){
			//Add action column to each row in result table
			array_walk($result_data['results'], array($this, 'add_actions'), array($query_type));
		}

		return $this->yui_results($result_data, 'total_records', 'results');
	}
		
	/**
	 * Add action links and labels for the context menu of the list items
	 *
	 * @param $value pointer to
	 * @param $key ?
	 * @param $params [composite_id, type of query, editable]
	 */
	public function add_actions(&$value, $key, $params)
	{
		//Defining new columns
		$value['ajax'] = array();
		$value['actions'] = array();
		$value['labels'] = array();

		$query_type = $params[0];
		
		switch($query_type)
		{
			case 'all_billings':
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uibilling.view', 'id' => $value['id'])));
				$value['labels'][] = lang('show');
				if($value['timestamp_commit'] == null || $value['timestamp_commit'] == '')
				{
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uibilling.delete', 'id' => $value['id'])));
					$value['labels'][] = lang('delete');
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uibilling.commit', 'id' => $value['id'])));
					$value['labels'][] = lang('commit');
				}
				break;
			case 'invoices':
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['contract_id']))) . '#price';
				$value['labels'][] = lang('show');
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['contract_id']))) . '#price';
				$value['labels'][] = lang('edit');
				break;
		}
    }
    
    public function download_export()
    {
		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
    	//$browser = CreateObject('phpgwapi.browser');
		//$browser->content_header('export.txt','text/plain');
		
		$stop = phpgw::get_var('date');
		
		$cs15 = phpgw::get_var('generate_cs15');
                $toExcel = phpgw::get_var('toExcel');
		if($cs15 == null){
                    if($toExcel == null)
                    {
                        $export_format = explode('_',phpgw::get_var('export_format'));
			$file_ending = $export_format[1];
			if($file_ending == 'gl07')
			{
				$type = 'intern';
			}
			else if($file_ending == 'lg04')
			{
				$type = 'faktura';
			}
			$date = date('Ymd', $stop);
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=PE_{$type}_{$date}.{$file_ending}");
			
			$id = phpgw::get_var('id');
			$path = "/rental/billings/{$id}";
			
			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;
			
			print $vfs->read
			(
				array
				(
					'string' => $path,
					RELATIVE_NONE
				)
			);
			
			//print rental_sobilling::get_instance()->get_export_data((int)phpgw::get_var('id'));
                    }
                    else
                    {
			$billing_job = rental_sobilling::get_instance()->get_single((int)phpgw::get_var('id'));
                        $billing_info_array = rental_sobilling_info::get_instance()->get(null, null, null, null, null, null, array('billing_id' => phpgw::get_var('id')));

                        if($billing_job == null) // Not found
                        {
                                $errorMsgs[] = lang('Could not find specified billing job.');
                        }
                        else
                        {
                            //Loop through  billing info array to find the first month
                            $month = 12;
                            foreach($billing_info_array as $billing_info)
                            {
                                    $year = $billing_info->get_year();
                                    if($month > $billing_info->get_month())
                                    {
                                            $month = $billing_info->get_month();
                                    }
                            }

                            $billing_job->set_year($year);
                            $billing_job->set_month($month);
                            
                            $list = rental_sobilling::get_instance()->generate_export($billing_job, true);
                            //_debug_array($list[0]);
                            /*foreach ($list as $l)
                            {
                                _debug_array($l);
                            }*/
                            
                            if(isset($list))
                            {
                                    $infoMsgs[] = lang('Export generated.');

                                    $keys = array();

                                    if(count($list[0]) > 0) {
                                        foreach($list[0] as $key => $value) {
                                            if(!is_array($value)) {
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
                                    for($j=0;$j<$count_keys;$j++)
                                    {
                                        array_push($headings, lang($keys[$j]));
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
		else{
			$file_ending = 'cs15';
			$type = 'kundefil';
			$date = date('Ymd', $stop);
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=PE_{$type}_{$date}.{$file_ending}");
			print rental_sobilling::get_instance()->generate_customer_export((int)phpgw::get_var('id'));
		}
    }

}
?>
