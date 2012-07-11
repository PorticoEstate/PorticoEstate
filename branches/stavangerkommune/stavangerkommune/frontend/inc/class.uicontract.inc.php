<?php

phpgw::import_class('frontend.uifrontend');
phpgw::import_class('rental.uicontract');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.socomposite');
phpgw::import_class('rental.soparty');

class frontend_uicontract extends frontend_uifrontend
{

	protected $contract_state_identifier;
	protected $contracts_per_location_identifier;
	protected $form_url;
	
	
	public $public_functions = array(
            'index'     => true
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
	
		$contractdata = array();	// This is the main container for all contract data sent to XSLT template stuff
		$msglog = array();			// Array of errors and other notifications displayed to us
		
		$filter = phpgw::get_var('contract_filter');
		// The user wants to change the contract status filter
		if(isset($filter)) 
		{
				$this->contract_filter = $filter;
				phpgwapi_cache::session_set('frontend', 'contract_filter', $filter);				

				// ... if the user changes filter that may cause the
				if($filter == 'active' || $filter == 'not_active')
				{
					$change_contract = true;
				}	
		}
		else
		{
			$filter = phpgwapi_cache::session_get('frontend', 'contract_filter');
			$this->contract_filter = isset($filter) ? $filter : 'active';
		}
		
		if(isset($_POST['send']))
		{
			$contract_id = phpgw::get_var('contract_id');
			$contract_message = phpgw::get_var('contract_message');
			$config	= CreateObject('phpgwapi.config','rental');
			$config->read();
			$use_fellesdata = $config->config_data['use_fellesdata'];
			if($use_fellesdata)
			{
				$user_data = frontend_bofellesdata::get_instance()->get_user($GLOBALS['phpgw_info']['user']['account_lid']);
					
				if($user_data['email'])
				{
					if(isset($contract_message) && $contract_message != '')
					{
						$from_address = $user_data['email'];
						$result = frontend_borental::send_contract_message($contract_id, $contract_message, $from_address);
						if($result)
						{
							$msglog['message'] = lang('message_sent');
						}
						else
						{
							$msglog['error'] = lang('message_not_sent');
						}
					}
					else
					{
						$msglog['error'] = lang('message_empty');
					}
				}
				else
				{
					$msglog['error'] = lang('user_not_in_fellesdata');
				}
			}
			else
			{
				$msglog['error'] = lang('fellesdata_not_in_use');
			}
		}
		
		
		// If the user wants to view another contract connected to this location
		// Request parameter: the user wants to view details about anther contract
		// The current state of the contract view of this user's session
		$this->contract_state = phpgwapi_cache::session_get('frontend', $this->contract_state_identifier);
		$new_contract = phpgw::get_var('contract_id');
		$contracts_per_location = phpgwapi_cache::session_get('frontend', $this->contracts_per_location_identifier);
		$contracts_for_selection = array();
		$number_of_valid_contracts = 0;
		foreach($contracts_per_location[$this->header_state['selected_location']] as $contract)
		{
			if(	($this->contract_filter == 'active' && $contract->is_active()) ||
				($this->contract_filter == 'not_active' && !$contract->is_active()) ||
				$this->contract_filter == 'all'
			)
			{
				$number_of_valid_contracts += 1;
				//Only select necessary fields
				$contracts_for_selection[] = array(
					'id' 				=> $contract->get_id(),
					'old_contract_id' 	=> $contract->get_old_contract_id(),
					'contract_status' 	=> $contract->get_contract_status()
					
				);
				
				if($change_contract || $new_contract == $contract->get_id() || !isset($this->contract_state['contract']))
				{
					$this->contract_state['selected'] = $contract->get_id();
					$this->contract_state['contract'] = $contract;
					//$this->contract = rental_socontract::get_instance()->get_single($new_contract);
					phpgwapi_cache::session_set('frontend', $this->contract_state_identifier , $this->contract_state);
					$change_contract = false;
					//Get more details on contract parties
					$parties =  rental_soparty::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $this->contract_state['contract']->get_id()));
					$party_array = array();
					foreach($parties as $party)
					{
						$party_array[] = $party->serialize();
					}
					
					$composites = rental_socomposite::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $this->contract_state['contract']->get_id()));
					$composite_array = array();
					foreach($composites as $composite)
					{
						$composite_array[] = $composite->serialize();
					}
					
					$this->contract_state['contract']->set_total_price(number_format($this->contract_state['contract']->get_total_price(),2,","," ")." ".lang('currency'));
					$this->contract_state['contract']->set_rented_area(number_format($this->contract_state['contract']->get_rented_area(),2,","," ")." ".lang('square_meters'));
				}
			}			
		}
		
		if(!isset($party_array) && isset($this->contract_state['contract']))
		{
			$parties =  rental_soparty::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $this->contract_state['contract']->get_id()));
			$party_array = array();
			foreach($parties as $party)
			{
				$party_array[] = $party->serialize();
			}
		}
		
		if(!isset($composite_array) && isset($this->contract_state['contract']))
		{
			$composites = rental_socomposite::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $this->contract_state['contract']->get_id()));
			$composite_array = array();
			foreach($composites as $composite)
			{
				$composite_array[] = $composite->serialize();
			}
		}
		
		if($number_of_valid_contracts == 0)
		{
			$this->contract_state['selected'] = '';
			$this->contract_state['contract'] = null;
		}
		
		$data = array (
			'msgbox_data'   => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
			'header' 		=>	$this->header_state,
			'tabs' 			=> 	$this->tabs,
			'contract_data' => 	array (
				'select' => $contracts_for_selection, 
				'selected_contract' =>  $this->contract_state['selected'], 
				'contract'	=> isset($this->contract_state['contract']) ? $this->contract_state['contract']->serialize() : array(),
				'party'	=> $party_array,
				'composite' => $composite_array,
				'contract_filter' => $this->contract_filter,
				'form_url' => $this->form_url
			)
		);
                	
		$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $data));
		$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','contract'));
		
	}
}
