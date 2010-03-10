<?php

phpgw::import_class('frontend.uifrontend');
phpgw::import_class('rental.uicontract');
phpgw::import_class('rental.socontract');

class frontend_uicontract extends frontend_uifrontend
{

	public $public_functions = array(
            'index'     => true,
            'show'      => true
	);

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Show single contract details
	 */
	public function index()
	{
		// This is the main container for all contract data sent to XSLT template stuff
		$contractdata = array();

		// Array of errors and other notifications displayed to user
		$msglog = array();

		// Holds the contract object (for use in this function only), if any
		$contract = null;

		$new_contract = phpgw::get_var('contract_id');
		$this->contract_state = phpgwapi_cache::session_get('frontend', 'contract_state');

		if(!isset($this->contract_state))
		{
			$contract = frontend_borental::get_first_contract_per_location($this->header_state['selected']);
			$this->contract_state['selected'] = $contract->get_id();
			$this->contract_state['contract'] = $contract;
			phpgwapi_cache::session_set('frontend', 'contract_state', $this->contract_state);
		}
			
		if(isset($new_contract))
		{
			//check to see if contract exist
			$exist = frontend_borental::contract_exist_per_location($new_contract,$this->header_state['selected']);
			if($exist)
			{
				$this->contract_state['selected'] = $new_contract;
				$this->contract = rental_socontract::get_instance()->get_single($new_contract);
				$this->contract_state['contract'] = $this->contract;
				phpgwapi_cache::session_set('frontend', 'contract_state', $this->contract_state);
			}
		}

		//prepare datatable
		$datatable['config']['allow_allrows'] = true;
		$datatable['config']['base_java_url'] = "menuaction:'frontend.uicontract.view'";
		
		$contracts_per_location = phpgwapi_cache::session_get('frontend', 'contracts_per_location');
		
		$j = 0;
		foreach($contracts_per_location[$this->header_state['selected']] as $contract)
		{
			
				$datatable['rows']['row'][$j]['column'][0]['name']		= 'id';
				$datatable['rows']['row'][$j]['column'][0]['value']	= $contract->get_id();

				$datatable['rows']['row'][$j]['column'][1]['name']		= 'old_contract_id';
				$datatable['rows']['row'][$j]['column'][1]['value']	= $contract->get_old_contract_id();	
				
				$datatable['rows']['row'][$j]['column'][2]['name']		= 'status';
				$datatable['rows']['row'][$j]['column'][2]['value']	= $contract->get_contract_status();	
				$j++;
		}
		
		 $parameters = array
                (
                'parameter' => array
                (
                    array
                    (
                        'name'		=> 'contract_id',
                        'source'	=> 'id'
                    ),
                )
            );

            $datatable['rowactions']['action'][] = array(
                'my_name' 			=> 'view',
                'statustext' 	=> lang('view the contract'),
                'text'			=> lang('view'),
                'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                (
                'menuaction'	=> 'frontend.uicontract.index'
                )),
                'parameters'	=> $parameters
            );
            
            $datatable['headers']['header'][0]['formatter'] 	= "''";
            $datatable['headers']['header'][0]['visible'] 	= false;
            $datatable['headers']['header'][0]['name'] 		= 'id';
            $datatable['headers']['header'][0]['sortable']	= false;
            
            $datatable['headers']['header'][1]['formatter'] 	= "''";
             $datatable['headers']['header'][1]['visible'] 	= true;
            $datatable['headers']['header'][1]['name'] 		= 'old_contract_id';
            $datatable['headers']['header'][1]['text'] 		= lang('old_contract_id');
            $datatable['headers']['header'][1]['sortable']	= false;
            
            $datatable['headers']['header'][2]['formatter'] 	= "''";
             $datatable['headers']['header'][2]['visible'] 	= true;
            $datatable['headers']['header'][2]['name'] 		= 'status';
            $datatable['headers']['header'][2]['text'] 		= lang('old_contract_id');
            $datatable['headers']['header'][1]['sortable']	= false;
            
//path for property.js
            $datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";
            
	// Pagination and sort values
            $datatable['pagination']['records_start'] 	= (int)$bo->start;
            $datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
            if($dry_run)
            {
                $datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
            }
            else
            {
                $datatable['pagination']['records_returned']= count($contracts_per_location[$this->header_state['selected']]);
            }
            $datatable['pagination']['records_total'] 	= count($contracts_per_location[$this->header_state['selected']]);

            $datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column

            $appname		= lang('contracts');
            $function_msg	= lang('list contract');

           
                $datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
                $datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
            

        //-- BEGIN----------------------------- JSON CODE ------------------------------
            //values for Pagination
            $json = array(
                'recordsReturned' 	=> $datatable['pagination']['records_returned'],
                'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
                'startIndex' 		=> $datatable['pagination']['records_start'],
                'sort'				=> $datatable['sorting']['order'],
                'dir'				=> $datatable['sorting']['sort'],
                'records'			=> array()
            );

            // values for datatable
            if(is_array($datatable['rows']['row']))
            {
                foreach( $datatable['rows']['row'] as $row )
                {
                    $json_row = array();
                    foreach( $row['column'] as $column)
                    {
                        $json_row[$column['name']] = $column['value'];
                    }
                    $json['records'][] = $json_row;
                }
            }

            // right in datatable
            if(is_array($datatable['rowactions']['action']))
            {
                $json['rights'] = $datatable['rowactions']['action'];
            }

            if( phpgw::get_var('phpgw_return_as') == 'json' )
            {
                return $json;
            }


            $datatable['json_data'] = json_encode($json);
//-------------------- JSON CODE ----------------------
            
            
		$data = array
		(
		//'msgbox_data'   => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
				'header' =>$this->header_state,
				'tabs' => $this->tabs,
				'contracts'      => array('datatable' => $datatable)
				//'contract'		=> $this->contract_state['contract']->serialize()
		);
			
		$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $data));
		$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','contract','datatable'));
	}

	/**
	 * TODO
	 */
	public function show()
	{
		$sotts = CreateObject('property.botts');

		/*$json = $sotts->read(array(
		 'query' => '1101-01-01-01-105'

		 ));*/
		$sotts->query = '1101-01-01-01-105';
		$json = $sotts->read();

		print_r($json);



	}
}
