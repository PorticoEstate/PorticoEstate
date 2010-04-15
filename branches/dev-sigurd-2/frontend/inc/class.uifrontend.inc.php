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
			//this module uses XSLT templates
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			//check to see if the 
			$mode = phpgwapi_cache::session_get('frontend', 'noframework');
			$noframework = isset($mode) ? $mode : true;
			


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

			// Check to see whether the user has specified tab or has a selected tab on session
			$type = phpgw::get_var('type', 'int', 'REQUEST');
			$tab = isset($type) ? $type : phpgwapi_cache::session_get('frontend','tab');

			$this->acl 	= & $GLOBALS['phpgw']->acl;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'header.list' , 'frontend' );

			//New location selected from header list
			$new_location = phpgw::get_var('location');
			
			//Get the organisation unit i
			$org_unit_id = phpgw::get_var('org_unit_id');

			// Get header state ...
			$this->header_state = phpgwapi_cache::session_get('frontend', 'header_state');

			$help_url = "javascript:openwindow('"
			 . $GLOBALS['phpgw']->link('/index.php', array
			 (
			 	'menuaction'=> 'manual.uimanual.help',
			 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
			 	'section' => isset($GLOBALS['phpgw_info']['apps']['manual']['section']) ? $GLOBALS['phpgw_info']['apps']['manual']['section'] : '',
			 	'referer' => phpgw::get_var('menuaction')
			 )) . "','700','600')";
			 
			
			 
			$this->header_state['help_url'] = $help_url;
			
			// ... and if new location check to see if user has access (exist in session)
			if(isset($new_location))
			{
				$locs = $this->header_state['locations'];
				$exist = false;
				foreach($locs as $loc)
				{
					if($loc['location_code'] == $new_location)
					{
						$exist = true;
					}
				}

				if($exist)
				{
					$tppl = phpgwapi_cache::session_get('frontend','total_price_per_location');
					$tapl = phpgwapi_cache::session_get('frontend','rented_area_per_location');
					$this->header_state['selected'] = $new_location;
					$this->header_state['selected_total_price'] = $tppl[$new_location];
					$this->header_state['selected_total_area'] = $tapl[$new_location];
					phpgwapi_cache::session_set('frontend', 'header_state', $this->header_state);
				}

				$tab = null; // No selected tab
				phpgwapi_cache::session_set('frontend','contract_state',null);
			}
			else if((count($this->header_state['locations']) == 0) || isset($org_unit_id)) // if the user has access to no locations
			{
				$org_unit_ids = isset($org_unit_id) ? array(0 =>$org_unit_id) : frontend_bofellesdata::get_instance()->get_result_units($GLOBALS['phpgw_info']['user']['account_lid']);
				
				if(isset($org_unit_id))
				{
					$tab = null;
					$noframework = false;
					phpgwapi_cache::session_set('frontend', 'noframework', $noframework);
				}
			
				$property_locations = frontend_borental::get_property_locations($org_unit_ids);
				
				$total_area = 0;
				$rented_area_per_location = phpgwapi_cache::session_get('frontend','rented_area_per_location');
				foreach($rented_area_per_location as $area_per_location)
				{
					$total_area += $area_per_location;
				}
				
				$total_price = 0;
				$total_price_per_location = phpgwapi_cache::session_get('frontend','total_price_per_location');
				foreach($total_price_per_location as $price_per_location)
				{
					$total_price += $price_per_location;
				}
				
				$name_of_user = $GLOBALS['phpgw_info']['user']['firstname']." ".$GLOBALS['phpgw_info']['user']['lastname'];
				
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
				
				$this->header_state = array(
					'selected' => count($property_locations) > 0 ? $property_locations[0]['location_code'] : '' ,
					'selected_total_price' => count($property_locations) > 0 ? $total_price_per_location[$property_locations[0]['location_code']] : '',
					'selected_total_area' => count($property_locations) > 0 ? $total_area_per_location[$property_locations[0]['location_code']] : '',
					'locations' => $property_locations,
					'number_of_locations' => count($property_locations),
					'total_price' => $total_price,
					'total_area' => $total_area,
					'number_of_org_units' => count($org_unit_ids),
					'name_of_user' => $name_of_user,
					'help_url' => $help_url,
					'contact_url' => $contact_url,
					'folder_url' => $folder_url
				);
				phpgwapi_cache::session_set('frontend', 'header_state', $this->header_state);
			}

			//Set selected tab; either user specified on this request, session based, or default: first in array
			$selected = isset($tab) ? $tab : array_shift(array_keys($tabs));
			$this->tabs = $GLOBALS['phpgw']->common->create_tabs($tabs, $selected);
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "frontend::{$selected}";
			phpgwapi_cache::session_set('frontend','tab',$selected);
			if($noframework)
			{
				$GLOBALS['phpgw']->css->add_external_file('phpgwapi/templates/bkbooking/css/frontend.css');
			}
			
			$GLOBALS['phpgw']->css->add_external_file('frontend/templates/base/base.css');
			$GLOBALS['phpgw_info']['flags']['noframework'] = $noframework;
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
