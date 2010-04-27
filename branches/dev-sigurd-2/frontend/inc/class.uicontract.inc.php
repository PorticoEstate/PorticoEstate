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
		// This is the main container for all contract data sent to XSLT template stuff
		$contractdata = array();

		// Array of errors and other notifications displayed to user
		$msglog = array();

		// Holds the contract object (for use in this function only), if any
		$contract = null;

		// Request parameter: the user wants to view details about anther contract
		$new_contract = phpgw::get_var('contract_id');
		var_dump("New contract: " . $new_contract);

		// The current state of the contract view of this user's session
		$this->contract_state = phpgwapi_cache::user_get('frontend', $this->contract_state_identifier , $GLOBALS['phpgw_info']['user']['account_id']);
		var_dump("Current contract state: " . $this->contract_state);
		var_dump("State: " . $this->contract_state_identifier);
		
		// If the user visits the contract tab for the first time...
		if(!isset($this->contract_state))
		{
			//... retrieve the first contract bound to the location (e.g. building)
			$contract = frontend_borental::get_first_contract_per_location($this->header_state['selected_location']);
			if(is_object($contract))
			{
				//... and set this contract as selected
				$this->contract_state['selected'] = $contract->get_id();
			}

			//... then store this contract on the session
			$this->contract_state['contract'] = $contract;
			phpgwapi_cache::user_set('frontend', $this->contract_state_identifier , $this->contract_state, $GLOBALS['phpgw_info']['user']['account_id']);
		}

		
		//User has applied a contract filter (active,ended,all)
		$contract_filter_param = phpgw::get_var('contract_filter');
		if(isset($contract_filter_param))
		{
			phpgwapi_cache::user_set('frontend', 'contract_filter', $contract_filter_param, $GLOBALS['phpgw_info']['user']['account_id']);
			$this->contract_filter = $contract_filter_param;
			if($contract_filter_param == 'active' || $contract_filter_param == 'not_active')
			{
				$change_contract = true;
			}
		} 
		else
		{
			$filter = phpgwapi_cache::user_get('frontend', 'contract_filter' , $GLOBALS['phpgw_info']['user']['account_id']);
			$this->contract_filter = isset($filter) ? $filter : 'active';
		}
		
		
		$contracts_per_location = phpgwapi_cache::user_get('frontend', $this->contracts_per_location_identifier, $GLOBALS['phpgw_info']['user']['account_id']);
		$contracts_for_selection = array();
		foreach($contracts_per_location[$this->header_state['selected_location']] as $contract)
		{
			if(	($this->contract_filter == 'active' && $contract->is_active()) ||
				($this->contract_filter == 'not_active' && !$contract->is_active()) ||
				$this->contract_filter == 'all'
			)
			{
				//Only select necessary fields
				$contracts_for_selection[] = array(
					'old_contract_id' => $contract->get_old_contract_id(),
					'contract_status' => $contract->get_contract_status()
					
				);
				
				if($change_contract)
				{
					$this->contract_state['selected'] = $contract->get_id();
				}
			}			
		}
		
		// If the user wants to view another contract connected to this location
		if(isset($new_contract))
		{
			//... first check to see if contract exist
			$exist = frontend_borental::contract_exist_per_location($new_contract,$this->header_state['selected'], $this->contract_state_identifier);
			if($exist)
			{
				// ... and if it exist set the identifier as selected and update the contract session state
				$this->contract_state['selected'] = $new_contract;
				$this->contract = rental_socontract::get_instance()->get_single($new_contract);
				$this->contract_state['contract'] = $this->contract;
				phpgwapi_cache::user_set('frontend', $this->contract_state_identifier , $this->contract_state, $GLOBALS['phpgw_info']['user']['account_id']);
			}
		}
		
		if(isset($this->contract_state['contract']))
		{
			
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
		
		var_dump("Number of contracts for selection: " . count($contracts_for_selection));
		var_dump("Selected contract" . $this->contract_state['selected']);
		
		$data = array (
			//'msgbox_data'   => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
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
		
		//var_dump($data);
                	
		$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $data));
		$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','contract'));
	}
}
