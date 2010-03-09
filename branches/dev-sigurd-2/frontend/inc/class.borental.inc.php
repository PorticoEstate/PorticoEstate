<?php
	phpgw::import_class('rental.soparty');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.socomposite');
	include_class('rental', 'contract', 'inc/model/');

    class frontend_borental {

        /**
         *
         * @param integer $org_unit_ids
         */
        public static function get_property_locations($org_unit_ids)
        {
        	
        	$property_locations = array();
        	
        	foreach($org_unit_ids as $org_unit_id){
        		/*
             * 1. hent alle kontraktsparter som har org unit id (foreløpig bruker vi result_unit_number i rentalparty)
             * 2. hent alle kontrakter på kontraktspartene
             * 3. hent alle leieobjekt på kontraktene
             * 4. hent ut bygg-ider, location_code, fra leieobjektet
             */
        	
	        	$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, array('org_unit_id' => $org_unit_id));
	        	
	        	$contracts = array();
	        	$composites = array();
	        	
	        	foreach($parties as $party)
	        	{
	        		$contracts = rental_socontract::get_instance()->get(null, null, null, null, null, null, array('party_id' => $party->get_id()));
	        		
		        	foreach($contracts as $id => $contract)
		        	{
		        		$composites = rental_socomposite::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $contracts[$id]->get_id()));
		        		
			        	foreach($composites as $composite)
			        	{
			        		$units = $composite->get_units();
			        		foreach($units as $unit)
			        		{
			        			$property_location = $unit->get_location();
			        			$property_locations[$property_location->get_location_code()] = $property_location;
			        		}        		
			        	}
		        	}
		        	
	        	
	        	
	        	}
        	}
  
        	$serialized_properties = array();
        	foreach($property_locations as $key => $property_location)
        	{
        		$serialized_properties[] = $property_location->serialize();
        	}
        	return $serialized_properties;
        }

    }
