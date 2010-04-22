<?php
	phpgw::import_class('rental.soparty');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.socontract_price_item');
	include_class('rental', 'contract', 'inc/model/');

    class frontend_borental {
    	
    	public static function contract_exist_per_location($contract_id, $location_code)
    	{
    		$contracts_per_location = phpgwapi_cache::user_get('frontend', 'contracts_per_location', $GLOBALS['phpgw_info']['user']['account_id']);
    		$exist = false;
    		foreach($contracts_per_location[$location_code] as $contract)
    		{
    			if($contract->get_id() == $contract_id)
    			{
    				$exist = true;
    			}
    		}
    		return $exist;
    	}
    	
    	public static function get_first_contract_per_location($location_code)
    	{
    		$contracts_per_location = phpgwapi_cache::user_get('frontend', 'contracts_per_location', $GLOBALS['phpgw_info']['user']['account_id']);
    		return $contracts_per_location[$location_code][0];
    	}
    	
   		public static function get_first_contract_in_per_location($location_code)
    	{
    		$contracts_in_per_location = phpgwapi_cache::user_get('frontend', 'contracts_in_per_location', $GLOBALS['phpgw_info']['user']['account_id']);
    		return $contracts_in_per_location[$location_code][0];
    	}
    	
        /**
         *
         * @param integer $org_unit_ids
         */
        public static function get_property_locations($org_unit_ids)
        {
        	
        	$property_locations = array();
        	
        	$total_price_all_buildings = 0;
        	$total_rented_area_all_builings = 0;
        	
        	$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			$location_id_internal = array_search('contract_type_internleie', $types);
        	$location_id_in = array_search('contract_type_innleie', $types);
        	
        	foreach($org_unit_ids as $org_unit_id){
        		/*
             * 1. hent alle kontraktsparter som har org unit id (foreløpig bruker vi result_unit_number i rentalparty)
             * 2. hent alle kontrakter på kontraktspartene
             * 3. hent alle leieobjekt på kontraktene
             * 4. hent ut bygg-ider, location_code, fra leieobjektet
             */
        	
        		if(!isset($org_unit_id['UNIT_ID']) || $org_unit_id['UNIT_ID'] == '')
        		{
        			continue;
        		}
        		
	        	$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, array('org_unit_id' => $org_unit_id['UNIT_ID']));
	        	
	        	$contracts = array();
	        	$composites = array();
	        	
	        	
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
			        				if(!is_array($contracts_per_location[$property_location->get_location_code()]))
				        			{
				        				$contracts_per_location[$property_location->get_location_code()] = array();	
				        			}
				        			array_push($contracts_per_location[$property_location->get_location_code()], $contract);
				        			
				        			if($contract->is_active())
				        			{
				        				$rented_area_per_location[$property_location->get_location_code()] += $contract->get_rented_area();
				        				$rented_price_per_location[$property_location->get_location_code()] += rental_socontract_price_item::get_instance()->get_total_price($contract->get_id());
				        			}
			        			}
			        			else if($contract->get_location_id() == $location_id_in)
			        			{
			        				if(!is_array($contracts_in_per_location[$property_location->get_location_code()]))
				        			{
				        				$contracts_in_per_location[$property_location->get_location_code()] = array();	
				        			}
				        			array_push($contracts_in_per_location[$property_location->get_location_code()], $contract);
			        			}
			        		}        		
			        	}
		        	}
	        	}
        	}
        	
        	
        	
        	phpgwapi_cache::user_set('frontend', 'contracts_per_location', $contracts_per_location, $GLOBALS['phpgw_info']['user']['account_id']);
        	phpgwapi_cache::user_set('frontend', 'contracts_in_per_location', $contracts_in_per_location, $GLOBALS['phpgw_info']['user']['account_id']);
        	phpgwapi_cache::user_set('frontend', 'rented_area_per_location', $rented_area_per_location, $GLOBALS['phpgw_info']['user']['account_id']);
        	phpgwapi_cache::user_set('frontend', 'total_price_per_location', $rented_price_per_location, $GLOBALS['phpgw_info']['user']['account_id']);
        	
  
        	//Serialize the properties
        	$serialized_properties = array();
        	foreach($property_locations as $key => $property_location)
        	{
        		$serialized_properties[] = $property_location->serialize();
        	}
        	return $serialized_properties;
        }

    }
