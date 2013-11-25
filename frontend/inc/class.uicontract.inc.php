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
	
	
	public $public_functions = array
	(
		'index'	 => true
	);

	public function __construct()
	{
		parent::__construct();
//		$this->get_contracts_per_location();
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
			$this->contract_filter = isset($filter) && $filter ? $filter : 'active';
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

		$contracts_per_location = $this->get_contracts_per_location();

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


	private function get_contracts_per_location()
	{
		$org_unit = $this->header_state['selected_org_unit'];
		if($org_unit == 'all' || $org_unit == 'none')
		{
			phpgwapi_cache::message_set('Velg organisasjon', 'error');
			return array();
		}

		$values = phpgwapi_cache::session_get('frontend', $this->contracts_per_location_identifier);
		
		if(isset($values[$org_unit]))
		{
			return $values[$org_unit];
		}

		$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, array('org_unit_id' => $org_unit));

       	$types = rental_socontract::get_instance()->get_fields_of_responsibility();
		$location_id_internal = array_search('contract_type_internleie', $types);
       	$location_id_in = array_search('contract_type_innleie', $types);
       	$location_id_ex = array_search('contract_type_eksternleie', $types);

		$contracts_per_location		= array();
		$contracts_in_per_location	= array();
		$contracts_ex_per_location	= array();
		
		//For all parties connected to the internal organization unit
		foreach($parties as $party)
		{
			//... get the contracts
			$contracts = rental_socontract::get_instance()->get(null, null, null, null, null, null, array('party_id' => $party->get_id()));
			//... and for each contract connected to this contract part
			foreach($contracts as $id => $contract)
			{
				//... get the composites
				$composites = rental_socomposite::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $contracts[$id]->get_id()));

				//...and for each composite in the contract in which this contract part is connected
				foreach($composites as $composite)
				{
					//... get the units
					$units = $composite->get_units();

					//... and for each unit retrieve the property locations we are after
					foreach($units as $unit)
					{
						$property_location = $unit->get_location();
						$property_locations[$property_location->get_location_code()] = $property_location;

						// Contract holders: contracts_per_location (internal) and contracts_in_per_location (in)

						// Internal contract should have impact on total price
						if($contract->get_location_id() == $location_id_internal)
						{
							$total_price = rental_socontract_price_item::get_instance()->get_total_price($contract->get_id());
							$contract->set_total_price($total_price);

							if(!is_array($contracts_per_location[$org_unit][$property_location->get_location_code()]))
							{
								$contracts_per_location[$org_unit][$property_location->get_location_code()] = array();
							}
							array_push($contracts_per_location[$org_unit][$property_location->get_location_code()], $contract);
						}
						else if($contract->get_location_id() == $location_id_in)
						{
							$total_price = rental_socontract_price_item::get_instance()->get_total_price($contract->get_id());
							$contract->set_total_price($total_price);

							if(!is_array($contracts_in_per_location[$org_unit][$property_location->get_location_code()]))
							{
								$contracts_in_per_location[$org_unit][$property_location->get_location_code()] = array();
							}
							array_push($contracts_in_per_location[$org_unit][$property_location->get_location_code()], $contract);
						}
						else if($contract->get_location_id() == $location_id_ex)
						{
							$total_price = rental_socontract_price_item::get_instance()->get_total_price($contract->get_id());
							$contract->set_total_price($total_price);

							if(!is_array($contracts_ex_per_location[$org_unit][$property_location->get_location_code()]))
							{
								$contracts_ex_per_location[$org_unit][$property_location->get_location_code()] = array();
							}
							array_push($contracts_ex_per_location[$org_unit][$property_location->get_location_code()], $contract);
						}
					}
				}
			}
		}
       	phpgwapi_cache::session_set('frontend', 'contracts_per_location', $contracts_per_location);
       	phpgwapi_cache::session_set('frontend', 'contracts_in_per_location', $contracts_in_per_location);
       	phpgwapi_cache::session_set('frontend', 'contracts_ex_per_location', $contracts_ex_per_location);
       	return $$this->contracts_per_location_identifier[$org_unit];
	}
}
