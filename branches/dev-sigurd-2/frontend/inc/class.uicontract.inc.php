<?php

phpgw::import_class('frontend.uifrontend');
phpgw::import_class('rental.uicontract');
phpgw::import_class('rental.socontract');

class frontend_uicontract extends frontend_uifrontend
{

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

		// The current state of the contract view of this user's session
		$this->contract_state = phpgwapi_cache::session_get('frontend', 'contract_state');

		// If the user visits the contract tab for the first time...
		if(!isset($this->contract_state))
		{
			//... retrieve the first contract bound to the location (e.g. building)
			$contract = frontend_borental::get_first_contract_per_location($this->header_state['selected']);
			if(is_object($contract))
			{
				//... and set this contract as selected
				$this->contract_state['selected'] = $contract->get_id();
			}

			//... then store this contract on the session
			$this->contract_state['contract'] = $contract;
			phpgwapi_cache::session_set('frontend', 'contract_state', $this->contract_state);
		}

		// If the user wants to view another contract connected to this location
		if(isset($new_contract))
		{
			//... first check to see if contract exist
			$exist = frontend_borental::contract_exist_per_location($new_contract,$this->header_state['selected']);
			if($exist)
			{
				// ... and if it exist set the identifier as selected and update the contract session state
				$this->contract_state['selected'] = $new_contract;
				$this->contract = rental_socontract::get_instance()->get_single($new_contract);
				$this->contract_state['contract'] = $this->contract;
				phpgwapi_cache::session_set('frontend', 'contract_state', $this->contract_state);
			}
		}
		
		$contracts_per_location = phpgwapi_cache::session_get('frontend', 'contracts_per_location');
		$contract_for_selection = array();
		foreach($contracts_per_location[$this->header_state['selected']] as $contract)
		{
			$contract_for_selection[] = $contract->serialize();
		}

		$data = array
                (
                //'msgbox_data'   => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
				'header' =>$this->header_state,
				'tabs' => $this->tabs,
                'selected_contract' =>  $this->contract_state['selected'],
				'contracts'      => array('select' => $contract_for_selection),
                'contract'		=> $this->contract_state['contract']->serialize()
                );
                	
		$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $data));
		$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','contract','datatable'));
	}
}
