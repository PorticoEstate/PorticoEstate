<?php

    class frontend_borental {

        /**
         *
         * @param integer $org_unit_id
         */
        public function get_property_locations($org_unit_id)
        {
            /*
             * 1. hent alle kontraktsparter som har org unit id (foreløpig bruker vi result_unit_number i rentalparty)
             * 2. hent alle kontrakter på kontraktspartene
             * 3. hent alle leieobjekt på kontraktene
             * 4. hent ut bygg-ider, location_code, fra leieobjektet
             */
        	
        	$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, array('org_unit_id' => $org_unit_id));
        	
        	$contracts = array();
        	foreach($parties as $party)
        	{
        		$contracts[] = rental_socontract::get_instance()->get(null, null, null, null, null, null, array('party_id' => $party->get_id()));
        	}
        	
        	$composites = array();
        	foreach($contracts as $contract)
        	{
        		$composites[] = rental_socomposite::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $contract->get_id()));
        	}
        	
        	
        	$property_locations = array();
        	foreach($composites as $composite)
        	{
        		$units = $composite->get_units();
        		foreach($units as $unit)
        		{
        			$property_locations[] = $unit->get_location();
        		}        		
        	}
        	
        	return $property_locations;
        }

    }
