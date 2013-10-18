<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	phpgw::import_class('rental.soparty');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.socontract_price_item');
	include_class('rental', 'contract', 'inc/model/');

    class frontend_borental
    {

    	public static function contract_exist_per_location($contract_id, $location_code, $contract_state_identifier)
    	{
    		$contracts_per_location = phpgwapi_cache::session_get('frontend', $contract_state_identifier);
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

    	public static function send_contract_message(int $contract_id, string $contract_message, string $from_address)
    	{
    		$contract = rental_socontract::get_instance()->get_single($contract_id);
    		if(isset($contract) && isset($contract_message) && $contract_message != '')
    		{
	    		$title = lang('title_contract_message');
	    		$title .= " ".$contract->get_old_contract_id();
	    		$title .= "(".lang($contract->get_contract_type_title()).")";

	    		$config	= CreateObject('phpgwapi.config','frontend');
				$config->read();
	    		$to = $config->config_data['email_contract_messages'];

	    		if (isset($contract_message) && isset($to) && isset($from_address))
				{
					if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'] )
					{
						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						$from = "{$GLOBALS['phpgw_info']['user']['fullname']}<{$from_address}>";

						$receive_notification = false;
						$rcpt = $GLOBALS['phpgw']->send->msg('email',$to,$title,
							 stripslashes(nl2br($contract_message)), '', $from, '',
							 $from , $GLOBALS['phpgw_info']['user']['fullname'],
							 'html', '', array() , $receive_notification);

						if($rcpt)
						{
							return true;
						}
					}
				}
    		}

			return false;
    	}

    	//FIXME : Sigurd 16 okt 2013: not used?
    	public static function get_first_contract_per_location($location_code)
    	{
    		$contracts_per_location = phpgwapi_cache::session_get('frontend', 'contracts_per_location');
    		return $contracts_per_location[$location_code][0];
    	}

    	//FIXME : Sigurd 16 okt 2013: not used?
   		public static function get_first_contract_in_per_location($location_code)
    	{
    		$contracts_in_per_location = phpgwapi_cache::session_get('frontend', 'contracts_in_per_location');
    		return $contracts_in_per_location[$location_code][0];
    	}

        /**
         *
         * @param integer $org_unit_ids
         */
        public static function get_property_locations($array,$top_org_units)
        {

// _debug_array($array);die();
 			return self::get_property_locations_lean($array,$top_org_units);
 
        	$property_locations = array();
        	$property_locations_active = array();

        	$total_price_all_buildings = 0;
        	$total_rented_area_all_builings = 0;

        	$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			$location_id_internal = array_search('contract_type_internleie', $types);
        	$location_id_in = array_search('contract_type_innleie', $types);
        	$location_id_ex = array_search('contract_type_eksternleie', $types);

        	foreach($array as $row)
        	{
        		/*
             * 1. hent alle kontraktsparter som har org unit id (foreløpig bruker vi result_unit_number i rentalparty)
             * 2. hent alle kontrakter på kontraktspartene
             * 3. hent alle leieobjekt på kontraktene
             * 4. hent ut bygg-ider, location_code, fra leieobjektet
             */
        		if(is_array($row))
        		{
	        		if(!isset($row['ORG_UNIT_ID']) || $row['ORG_UNIT_ID'] == '')
	        		{
	        			continue;
	        		}
	        		$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, array('org_unit_id' => $row['ORG_UNIT_ID']));
        		}
        		else
        		{
        			$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, array('email' => $row));
        		}

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
			        				$total_price = rental_socontract_price_item::get_instance()->get_total_price($contract->get_id());
			        				$contract->set_total_price($total_price);

			        				if(!is_array($contracts_per_location[$property_location->get_location_code()]))
				        			{
				        				$contracts_per_location[$property_location->get_location_code()] = array();
				        			}
				        			array_push($contracts_per_location[$property_location->get_location_code()], $contract);

			        				if($contract->is_active())
				        			{
				        				$property_locations_active[$property_location->get_location_code()] = true;
				        				$rented_area_per_location[$property_location->get_location_code()] += $contract->get_rented_area();
				        				$rented_price_per_location[$property_location->get_location_code()] += $total_price;
				        			}
			        			}
			        			else if($contract->get_location_id() == $location_id_in)
			        			{
			        				$total_price = rental_socontract_price_item::get_instance()->get_total_price($contract->get_id());
			        				$contract->set_total_price($total_price);

			        				if($contract->is_active())
				        			{
				        				$property_locations_active[$property_location->get_location_code()] = true;
				        			}

			        				if(!is_array($contracts_in_per_location[$property_location->get_location_code()]))
				        			{
				        				$contracts_in_per_location[$property_location->get_location_code()] = array();
				        			}
				        			array_push($contracts_in_per_location[$property_location->get_location_code()], $contract);
			        			}
			        			else if($contract->get_location_id() == $location_id_ex)
			        			{
			        				$total_price = rental_socontract_price_item::get_instance()->get_total_price($contract->get_id());
			        				$contract->set_total_price($total_price);

			        				if(!is_array($contracts_ex_per_location[$property_location->get_location_code()]))
				        			{
				        				$contracts_ex_per_location[$property_location->get_location_code()] = array();
				        			}
				        			array_push($contracts_ex_per_location[$property_location->get_location_code()], $contract);

			        				if($contract->is_active())
				        			{
				        				$property_locations_active[$property_location->get_location_code()] = true;
				        				$rented_area_per_location[$property_location->get_location_code()] += $contract->get_rented_area();
				        				$rented_price_per_location[$property_location->get_location_code()] += $total_price;
				        			}
			        			}
			        		}
			        	}
		        	}
	        	}
        	}

        	//Not used ?
        	phpgwapi_cache::session_set('frontend', 'contracts_per_location', $contracts_per_location);
        	//Not used ?
        	phpgwapi_cache::session_set('frontend', 'contracts_in_per_location', $contracts_in_per_location);
        	//Not used ?
        	phpgwapi_cache::session_set('frontend', 'contracts_ex_per_location', $contracts_ex_per_location);
        	phpgwapi_cache::session_set('frontend', 'rented_area_per_location', $rented_area_per_location);
        	phpgwapi_cache::session_set('frontend', 'total_price_per_location', $rented_price_per_location);

        	//Serialize the properties
        	$serialized_properties = array();
        	foreach($property_locations as $key => $property_location)
        	{
        		if(isset($property_locations_active[$property_location->get_location_code()]) && $property_locations_active[$property_location->get_location_code()])
        		{
        			$serialized_properties[] = $property_location->serialize();
        		}
        	}

        	return $serialized_properties;
        }


        /**
         *
         * @param array $org_unit_ids
         */
        public static function get_property_locations_lean($array,$top_org_units)
        {

        	$property_locations = array();
        	$property_locations_active = array();

        	$total_price_all_buildings = 0;
        	$total_rented_area_all_builings = 0;

        	$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			$location_id_internal = array_search('contract_type_internleie', $types);
        	$location_id_in = array_search('contract_type_innleie', $types);
        	$location_id_ex = array_search('contract_type_eksternleie', $types);

        	foreach($array as $row)
        	{
        		/*
             * 1. hent alle kontraktsparter som har org unit id (foreløpig bruker vi result_unit_number i rentalparty)
             * 2. hent alle kontrakter på kontraktspartene
             * 3. hent alle leieobjekt på kontraktene
             * 4. hent ut bygg-ider, location_code, fra leieobjektet
             */
        		if(is_array($row))
        		{
	        		if(!isset($row['ORG_UNIT_ID']) || $row['ORG_UNIT_ID'] == '')
	        		{
	        			continue;
	        		}
	        //		$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, array('org_unit_id' => $row['ORG_UNIT_ID']));
			//      $parties = array_keys($parties);
	        		$parties = self::get_all_parties($top_org_units);
        		}
        		else
        		{
        			$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, array('email' => $row));
        			$parties = array_keys($parties);
        		}

	        	$contracts = array();
	        	$composites = array();

	    		$sorental	= CreateObject('frontend.sorental');

	        	//For all parties connected to the internal organization unit
				$locations = $sorental->get_location($parties);

        	}

			return $locations;
		}
		

		/**
		* Get the org_units by hierarchical inheritance
		*/
		function get_all_parties($top_org_units)
		{
			static $parties =array(); // cache result
			
			//already calculated
			if($parties)
			{
				return $parties;
			}
/*
			$bt = debug_backtrace();
			echo "<b>frontend_borental::{$bt[0]['function']} Called from file: {$bt[0]['file']} line: {$bt[0]['line']}</b><br/>";
			unset($bt);
*/

			$org_units = array();
			$selected_org_unit = phpgw::get_var('org_unit_id'); 			// New organisational unit selected from organisational units list
			if($selected_org_unit == 'all')
			{
				foreach($top_org_units as $entry)
				{
					$org_units[] = $entry['ORG_UNIT_ID'];
				}
			}
			else
			{
				$org_units[] = $selected_org_unit;
			}
			

			$bofellesdata = CreateObject('rental.bofellesdata');

			foreach ($org_units as $org_unit)
			{
				$bofellesdata->get_org_unit_ids_from_top($org_unit); 
			}

			$all_unit_ids = array_unique($bofellesdata->unit_ids);

 
 	  		$parties	= execMethod('frontend.sorental.get_parties', $all_unit_ids);

			return $parties;
		}


		public static function get_total_cost_and_area($org_units = array())
		{
    		$sorental	= CreateObject('frontend.sorental');
    		return $sorental->get_total_cost_and_area($org_units);
		}

    }
