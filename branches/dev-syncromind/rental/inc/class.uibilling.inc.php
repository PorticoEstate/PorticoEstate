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
		$billing_time_limit = isset($config->config_data['billing_time_limit']) && $config->config_data['billing_time_limit'] ? (int)$config->config_data['billing_time_limit'] : 500;
		set_time_limit($billing_time_limit); // Set time limit
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('invoice_menu');
	}
	
	public function index()
	{
		if (phpgw::get_var('phpgw_return_as') == 'json')
		{
			return $this->query();
		}

		$field_of_responsibility_options = array();
		$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
		foreach($fields as $id => $label)
		{
			$names = $this->locations->get_name($id);
			if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
			{
				if($this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
				{
					$field_of_responsibility_options[] = array('id'=> $id, 'name'=>lang($label));
				}
			}
		}

		$data = array(
			'datatable_name'	=> lang('invoice_menu'),
			'form' => array(
				'toolbar' => array(
					'item' => array(
						array
						(
							'type'   => 'filter',
							'name'   => 'contract_type',
							'text'   => lang('field_of_responsibility'),
							'list'   => $field_of_responsibility_options
						)							
					)
				)
			),
			'datatable' => array(
				'source'	=> self::link(array(
					'menuaction'	=> 'rental.uibilling.index', 
					'type'			=> 'all_billings',
					'phpgw_return_as' => 'json'
				)),
				'allrows'	=> true,
				'editor_action' => '',
				'field' => array(
					array(
						'key'		=> 'description', 
						'label'		=> lang('title'), 
						'className'	=> '', 
						'sortable'	=> false, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'responsibility_title', 
						'label'		=> lang('contract_type'), 
						'className'	=> 'center', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'billing_info', 
						'label'		=> lang('billing_terms'), 
						'className'	=> '', 
						'sortable'	=> false, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'total_sum', 
						'label'		=> lang('sum'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'timestamp_stop', 
						'label'		=> lang('last_updated'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'created_by', 
						'label'		=> lang('run by'), 
						'className'	=> '', 
						'sortable'	=> false, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'timestamp_commit', 
						'label'		=> lang('Commited'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
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
						'name'		=> 'id',
						'source'	=> 'id'
					)
				)
			);

		$data['datatable']['actions'][] = array
			(
				'my_name'		=> 'show',
				'text' 			=> lang('show'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'rental.uibilling.view'
				)),
				'parameters'	=> json_encode($parameters)
			);


		self::render_template_xsl('datatable_jquery', $data);
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
			$price_items = rental_socontract_price_item::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $invoice->get_contract_id(), 'one_time' => true, 'include_billed' => true));
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
		/*$start_index	= phpgw::get_var('startIndex', 'int');
		$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
		$sort_field		= phpgw::get_var('sort');
		$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
		// Form variables
		$search_for 	= phpgw::get_var('query');
		$search_type	= phpgw::get_var('search_option');*/
		
		$search			= phpgw::get_var('search');
		$order			= phpgw::get_var('order');
		$draw			= phpgw::get_var('draw', 'int');
		$columns		= phpgw::get_var('columns');

		$start_index	= phpgw::get_var('start', 'int', 'REQUEST', 0);
		$num_of_objects	= (phpgw::get_var('length', 'int') <= 0) ? $user_rows_per_page : phpgw::get_var('length', 'int');
		$sort_field		= ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'id'; 
		$sort_ascending	= ($order[0]['dir'] == 'desc') ? false : true;
		// Form variables
		$search_for 	= $search['value'];
		$search_type	= phpgw::get_var('search_option', 'string', 'REQUEST', 'all');
			
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
				if(!$sort_field)
				{
					$sort_field = 'timestamp_stop';
					$sort_ascending = false;
				}
				else if($sort_field == 'responsibility_title')
				{
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
		//$result_data = array('results' => $rows, 'total_records' => $object_count);
		
		if(!$export){
			//Add action column to each row in result table
			array_walk($rows, array($this, 'add_actions'), array($query_type));
		}

		$result_data    =   array('results' =>  $rows);
		$result_data['total_records']	= $object_count;
		$result_data['draw']    = $draw;

		return $this->jquery_results($result_data);
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
						$type = phpgw::get_var('type', 'string', 'GET', 'bk');
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
                            
                            $list = rental_sobilling::get_instance()->generate_export($billing_job, $type);
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
