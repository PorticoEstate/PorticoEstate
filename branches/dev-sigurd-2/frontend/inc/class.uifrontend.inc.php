<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.uifrontend.inc.php 4859 2010-02-18 23:09:16Z sigurd $
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

	phpgw::import_class('frontend.bofrontend');
	phpgw::import_class('frontend.bofellesdata');
	phpgw::import_class('frontend.borental');
	
	
	/**
	 * Frontend main class
	 *
	 * @package Frontend
	 */

	class frontend_uifrontend
	{
		/**
		 * Used to save state of header (select box, ++) between requests
		 * @var array
		 */
		public $header_state;	

		public $public_functions = array
			(
			'index'		=> true,
			'objectimg' => true
		);

		public function __construct()
		{
			// This module uses XSLT templates
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			// Get the mode: in frame or full screen
			$mode = phpgwapi_cache::user_get('frontend', 'noframework', $GLOBALS['phpgw_info']['user']['account_id']);
			$noframework = isset($mode) ? $mode : true;
			
			/* Get the tabs and check to see whether the user has specified a tab or has a selected tab on session */
			$tabs = $this->get_tabs();
			$type = phpgw::get_var('type', 'int', 'REQUEST');
			$tab = isset($type) ? $type : phpgwapi_cache::user_get('frontend','tab', $GLOBALS['phpgw_info']['user']['account_id']);
			$selected = isset($tab) ? $tab : array_shift(array_keys($tabs));
			$this->tabs = $GLOBALS['phpgw']->common->create_tabs($tabs, $selected);
			phpgwapi_cache::user_set('frontend','tab',$selected, $GLOBALS['phpgw_info']['user']['account_id']);
			
			// Get header state
			$this->header_state = phpgwapi_cache::user_get('frontend', 'header_state', $GLOBALS['phpgw_info']['user']['account_id']);
			
			// Get navigation parameters
			$param_selected_location = phpgw::get_var('location'); 			// New location selected from locations list
			$param_selected_org_unit = phpgw::get_var('org_unit_id'); 			// New organisational unit selected from organisational units list
			$param_only_org_unit = phpgw::get_var('only_org_unit_id'); 	// Frontend access from rental module regarding specific organisational unit
			
			$refresh = phpgw::get_var('refresh'); //Refresh organisation list
			
			/* If the user has selected an organisational unit or all units */
			if(isset($param_selected_org_unit))
			{
				//Specify which unit(s)
				if($param_selected_org_unit == 'all')
				{
					$org_unit_ids = $this->header_state['org_unit'];
				}
				else
				{
					if($this->org_unit_in_selection($param_selected_org_unit,$this->header_state['org_unit']))
					{
						//Creating a temporary array holding the single organisational unit in query
						$org_unit_ids = array(
							array(
								"ORG_UNIT_ID" => 1,
								"ORG_NAME" => frontend_bofellesdata::get_instance()->get_organisational_unit_name($param_selected_org_unit),
								"UNIT_ID" => $param_selected_org_unit
							)
						);
					}
					else
					{
						//If the organisational unit selected is not in list; do default 'all'
						$org_unit_ids = $this->header_state['org_unit'];
						$param_selected_org_unit = 'all';
					}
				}
				$this->header_state['selected_org_unit'] = $param_selected_org_unit;

				//Update locations according to organisational unit specification
				$property_locations = frontend_borental::get_property_locations($org_unit_ids);
				if(count($property_locations) > 0)
				{
					$this->header_state['locations'] = $property_locations;
					$this->header_state['number_of_locations'] = count($property_locations);
					$this->header_state['selected_location'] = $property_locations[0]['location_code'];
					$param_selected_location = $property_locations[0]['location_code'];
					$this->calculate_totals($property_locations);
				}
				else
				{
					$this->header_state['selected_location'] = '';
				}
			}
			/* If the user selects a organisational unit in rental module */
			else if(isset($param_only_org_unit)) 
			{
				//TODO: check permissions
				
				//Specify unit
				$org_unit_ids = array(
					array(
						"ORG_UNIT_ID" => 1,
						"ORG_NAME" => frontend_bofellesdata::get_instance()->get_organisational_unit_name($param_only_org_unit),
						"UNIT_ID" => $param_only_org_unit
					)
				);
				
				//Update header state
				$this->header_state['org_unit'] = $org_unit_ids;
				$this->header_state['number_of_org_units'] = '1';
				$this->header_state['selected_org_unit'] = $param_only_org_unit;
				
				//Update locations
				$property_locations = frontend_borental::get_property_locations($org_unit_ids);
				
				if(count($property_locations) > 0)
				{
					$this->header_state['locations'] = $property_locations;
					$this->header_state['number_of_locations'] = count($property_locations);
					$this->header_state['selected_location'] = $property_locations[0]['location_code'];
					$param_selected_location = $property_locations[0]['location_code'];
					$this->calculate_totals($property_locations);
				}
				else
				{
					$this->header_state['selected_location'] = '';
				}
				
				$noframework = false; // In regular frames
				phpgwapi_cache::user_set('frontend', 'noframework', $noframework, $GLOBALS['phpgw_info']['user']['account_id']); // Store mode on session
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = "frontend::{$selected}";
				$this->insert_links_on_header_state();
			} 
			/* No state, first visit after login*/
			else if(!isset($this->header_state) || isset($refresh))
			{
				//Specify organisational units
				$org_unit_ids = frontend_bofellesdata::get_instance()->get_result_units($GLOBALS['phpgw_info']['user']['account_lid']);
				
				//Update org units on header state
				$this->header_state['org_unit'] = $org_unit_ids;
				$this->header_state['number_of_org_units'] = count($org_unit_ids);
				$this->header_state['selected_org_unit'] = 'all';
				
				//Update locations
				$property_locations = frontend_borental::get_property_locations($org_unit_ids);
				
				if(count($property_locations) > 0)
				{
					$this->header_state['locations'] = $property_locations;
					$this->header_state['number_of_locations'] = count($property_locations);
					$this->header_state['selected_location'] = $property_locations[0]['location_code'];
					$param_selected_location = $property_locations[0]['location_code'];
					$this->calculate_totals($property_locations);
				}
				else
				{
					$this->header_state['selected_location'] = '';
				}
				
				$this->insert_links_on_header_state();
				
			}
			
			/* If the user has selected a location or as a side-effect from selecting organisational unit */
			if(isset($param_selected_location))
			{
				$locs = $this->header_state['locations'];
				$exist = false;
				foreach($locs as $loc)
				{
					if($loc['location_code'] == $param_selected_location)
					{
						$exist = true;
					}
				}

				if($exist)
				{
					$tppl = phpgwapi_cache::user_get('frontend','total_price_per_location', $GLOBALS['phpgw_info']['user']['account_id']);
					$tapl = phpgwapi_cache::user_get('frontend','rented_area_per_location', $GLOBALS['phpgw_info']['user']['account_id']);
					$this->header_state['selected_location'] = $param_selected_location;
					$this->header_state['selected_total_price'] = number_format($tppl[$param_selected_location],2,","," ")." ".lang('currency');
					$this->header_state['selected_total_area'] = number_format($tapl[$param_selected_location],2,","," ")." ".lang('square_meters');
					phpgwapi_cache::user_set('frontend', 'header_state', $this->header_state, $GLOBALS['phpgw_info']['user']['account_id']);
				}

				phpgwapi_cache::user_set('frontend','contract_state',null, $GLOBALS['phpgw_info']['user']['account_id']);
				phpgwapi_cache::user_set('frontend','contract_state_in',null, $GLOBALS['phpgw_info']['user']['account_id']);
			}
			/* Store the header state on the session*/
			phpgwapi_cache::user_set('frontend', 'header_state', $this->header_state, $GLOBALS['phpgw_info']['user']['account_id']);

			//Add style sheet for full screen view
			//if($noframework)
			//{
				$GLOBALS['phpgw']->css->add_external_file('phpgwapi/templates/bkbooking/css/frontend.css');
			//}
			
			$GLOBALS['phpgw']->css->add_external_file('frontend/templates/base/base.css');
			$GLOBALS['phpgw_info']['flags']['noframework'] = $noframework;
		}
		
		function get_tabs()
		{
			// Get tabs from location hierarchy
			// tabs [location identidier] = {label => ..., link => ...}
			$locations = frontend_bofrontend::get_sections();
			$tabs = array();
			foreach ($locations as $key => $entry)
			{
				$name = $entry['name'];
				$location = $entry['location'];

				if ( $GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, 'frontend') )
				{
					$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', $location);
					$tabs[$location_id] = array(
						'label' => lang($name),
						'link'  => $GLOBALS['phpgw']->link('/',array('menuaction' => "frontend.ui{$name}.index", 'type'=>$location_id, 'noframework' => $noframework))
					);
				}
			}
			return $tabs;
		}
		
		function insert_links_on_header_state()
		{
			
			$help_url = "javascript:openwindow('"
						 . $GLOBALS['phpgw']->link('/index.php', array
						 (
						 	'menuaction'=> 'manual.uimanual.help',
						 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
						 	'section' => isset($GLOBALS['phpgw_info']['apps']['manual']['section']) ? $GLOBALS['phpgw_info']['apps']['manual']['section'] : '',
						 	'referer' => phpgw::get_var('menuaction')
						 )) . "','700','600')";
			
			$contact_url = "javascript:openwindow('"
				 . $GLOBALS['phpgw']->link('/index.php', array
				 (
				 	'menuaction'=> 'manual.uimanual.help',
				 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
				 	'section' => 'contact_BKB'
				 )) . "','700','600')";
		 
			$folder_url = "javascript:openwindow('"
				 . $GLOBALS['phpgw']->link('/index.php', array
				 (
				 	'menuaction'=> 'manual.uimanual.help',
				 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
				 	'section' => 'folder'
				 )) . "','700','600')"; 
				 
			$name_of_user = $GLOBALS['phpgw_info']['user']['firstname']." ".$GLOBALS['phpgw_info']['user']['lastname'];
				 
			$this->header_state['help_url'] = $help_url;
			$this->header_state['contact_url'] = $contact_url;
			$this->header_state['folder_url'] = $folder_url;
			$this->header_state['name_of_user'] = $name_of_user;
		}
		
		function calculate_totals($property_locations)
		{
			
			// Calculate
			$total_area = 0;
			$rented_area_per_location = phpgwapi_cache::user_get('frontend','rented_area_per_location', $GLOBALS['phpgw_info']['user']['account_id']);
			foreach($rented_area_per_location as $location_code => $area_per_location)
			{
				
				if($this->location_in_selection($location_code,$property_locations))
				{
					$total_area += $area_per_location;
				}
			}
			
			$total_price = 0;
			$total_price_per_location = phpgwapi_cache::user_get('frontend','total_price_per_location', $GLOBALS['phpgw_info']['user']['account_id']);
			foreach($total_price_per_location as $location_code => $price_per_location)
			{
				if($this->location_in_selection($location_code,$property_locations))
				{
					$total_price += $price_per_location;
				}
			}
			$this->header_state['total_price'] = number_format($total_price, 0, ","," ")." kr";
			$this->header_state['total_area'] = number_format($total_area, 0, ",", " ")." kvm";
		}
		
		function location_in_selection($location_code, $property_locations)
		{
			foreach($property_locations as $property_location)
			{
				if($location_code == $property_location['location_code'])
				{
					return true;
				}
			}
			return false;
		}
		
		function org_unit_in_selection($unit_id, $org_units)
		{
			foreach($org_units as $org_unit)
			{
				if($unit_id == $org_unit['UNIT_ID'])
				{
					return true;
				}
			}
			return false;
		}


		public function index()
		{
			//Forward to helpdesk
			$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', '.ticket');
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'frontend.uihelpdesk.index', 'type' => $location_id));
		}


		public function objectimg()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$config = CreateObject('phpgwapi.config','frontend');
			$config->read();
			$doc_type = $config->config_data['picture_building_cat'] ? $config->config_data['picture_building_cat'] : 'profilbilder';

			// Get object id from params or use 'dummy'
			$location_code = phpgw::get_var('loc_code') ? phpgw::get_var('loc_code') : 'dummy';

			$directory = "/property/document/{$location_code}/{$doc_type}";

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$ls_array = $vfs->ls(array(
				'string' => $directory,
				'relatives' => array(RELATIVE_NONE)
			));

			$file = isset($ls_array[0]['directory']['name']) ? "{$ls_array[0]['directory']}/{$ls_array[0]['name']}" : '';

			$document = $vfs->read(array(
				'string'	=> $file,
				'relatives' => array(RELATIVE_NONE))
			);

			$vfs->override_acl = 0;

			$mime_type = 'text/plain';
			if ($ls_array[0]['mime_type'])
			{
				$mime_type = $ls_array[0]['mime_type'];
			}
			header('Content-type: ' . $mime_type);
			echo $document;

			$GLOBALS['phpgw']->common->phpgw_exit();
 		}
	}
