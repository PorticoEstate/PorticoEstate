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
			if($contract_ids != null && is_array($contract_ids) && count($contract_ids) > 0) // User submitted contracts to bill
			{
				$missing_billing_info = rental_sobilling::get_instance()->get_missing_billing_info(phpgw::get_var('billing_term'), phpgw::get_var('year'), phpgw::get_var('month'), $contract_ids, $contract_ids_override, phpgw::get_var('export_format'));
				if($missing_billing_info == null || count($missing_billing_info) == 0)
				{
					$billing_job = rental_sobilling::get_instance()->create_billing(isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) ? isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) : 2, phpgw::get_var('contract_type'), phpgw::get_var('billing_term'), phpgw::get_var('year'), phpgw::get_var('month'), phpgw::get_var('title'), $GLOBALS['phpgw_info']['user']['account_id'], $contract_ids, $contract_ids_override, phpgw::get_var('export_format'), $existing_billing);
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
			$contract_type = phpgw::get_var('contract_type');
			
			$names = $this->locations->get_name($contract_type);
			if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
			{
				if(!$this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
				{
					$this->render('permission_denied.php');
					return;
				}
			}
			$use_existing = false;
			$year = phpgw::get_var('year');
			$billing_term_tmp = phpgw::get_var('billing_term');
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
				if($billing_moth == '1'){
					$month = 7;
					$billing_term_label = lang('first_half');
				}
				else{
					$month = 1;
					$billing_term_label = lang('second_half');
				}
			}
			else // yearly
			{
				$month = 1;
				$billing_term_label = lang('annually');
			}

			$existing_billing = phpgw::get_var('existing_billing');
			if($existing_billing != 'new_billing'){
				$use_existing = true;
			}
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
			
			if(rental_sobilling::get_instance()->has_been_billed($contract_type, $billing_term, $year, $month)) // Checks if period has been billed before
			{
				// We only give a warning and let the user go to step 2
				$warningMsgs[] = lang('the period has been billed before.');
			}
			else
			{
				$filters = array('contracts_for_billing' => true, 'contract_type' => $contract_type, 'billing_term_id' => $billing_term, 'year' => $year, 'month' => $month);
				$contracts = rental_socontract::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
		
				$socontract_price_item = rental_socontract_price_item::get_instance();
				foreach($contracts as $id => $contract)
				{	
					if(isset($contract))
					{
						$total_price = $socontract_price_item->get_total_price($contract->get_id());
						$type_id = $contract->get_contract_type_id();
						
						if($type_id == 4)
						{
							$warningMsgs[] = "Removed KF contract " . $contract->get_old_contract_id();
							$contracts[$id] = null;
						} 
						else if(isset($total_price) && $total_price == 0)
						{
							$warningMsgs[] = "Removed contract " . $contract->get_old_contract_id() . " with total price equal to 0";
							$contracts[$id] = null;
						}
						else
						{
							$contract->set_total_price($total_price);
						}
					}
				}
			}
				
			$data = array
			(
				'contracts' => $contracts,
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
			'download_link' => html_entity_decode(self::link(array('menuaction' => 'rental.uibilling.download_export', 'id' => (($billing_job != null) ? $billing_job->get_id() : ''))))
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
		// YUI variables for paging and sorting
		$start_index	= phpgw::get_var('startIndex', 'int');
		$num_of_objects	= phpgw::get_var('results', 'int', 'GET', 10);
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
		switch($query_type)
		{
			case 'all_billings':
				$filters = array();
				$result_objects = rental_sobilling::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_sobilling::get_instance()->get_count($search_for, $search_type, $filters);
				break;
			case 'invoices':
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
		
		//Add action column to each row in result table
		array_walk($result_data['results'], array($this, 'add_actions'), array($query_type));

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
    	$browser = CreateObject('phpgwapi.browser');
		$browser->content_header('export.txt','text/plain');
		print rental_sobilling::get_instance()->get_export_data((int)phpgw::get_var('id'));
    }

}
?>