<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package property
	* @subpackage controller
 	* @version $Id$
	*/	

	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'check_list_status_info', 'inc/component/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'location_finder', 'inc/helper/');
		
	$so = CreateObject('controller.socheck_list');
	$so_control = CreateObject('controller.socontrol');
	
	$config	= CreateObject('phpgwapi.config','controller');
	$config->read();
	$limit_no_of_planned = isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls'])? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls'] : (isset($config->config_data['no_of_planned_controls']) && $config->config_data['no_of_planned_controls'] > 0 ? $config->config_data['no_of_planned_controls']:5);
	$limit_no_of_assigned = isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_assigned_controls'])? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_assigned_controls'] : (isset($config->config_data['no_of_assigned_controls']) && $config->config_data['no_of_assigned_controls'] > 0 ? $config->config_data['no_of_assigned_controls']:10);

	$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	$year = phpgw::get_var('year');
	
	if(empty($year)){
		$year = date("Y");	
	}
	
	$year = intval($year);
				
	$from_date_ts = strtotime("now");
	$to_year = $year + 1;
	$to_date_ts = strtotime("01/01/$to_year");	
				
	$criteria = array
	(
		'user_id' => $GLOBALS['phpgw_info']['user']['account_id'],
		'type_id' => 1,
		'role_id' => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
		'allrows' => false
	);

	$location_finder = new location_finder();
	$my_locations = $location_finder->get_responsibilities( $criteria );

	$repeat_type = null;
	$controls_for_location_array = array();
	foreach($my_locations as $location)
	{
	    $controls = array();
	    $controls_loc = $so_control->get_controls_by_location($location["location_code"], $from_date_ts, $to_date_ts, $repeat_type, '', $location["role_id"] );
	    $controls_comp = $so_control->get_controls_for_components_by_location($location["location_code"], $from_date_ts, $to_date_ts, $repeat_type, '', $location["role_id"] );
	    foreach($controls_loc as $cl)
	    {
	        $controls[] = $cl;
	    }
	    foreach($controls_comp as $cc)
	    {
	        $controls[] = $cc;
	    }
	    
        $controls_for_location_array[] = array($location["location_code"], $controls);
	}
	

	$controls_array = array();
	$control_dates = array();
	foreach($controls_for_location_array as $control_arr){
		$current_location = $control_arr[0];
		$controls_for_loc_array = $control_arr[1];
		foreach($controls_for_loc_array as $control)
		{
			$date_generator = new date_generator($control["start_date"], $control["end_date"], $from_date_ts, $to_date_ts, $control["repeat_type"], $control["repeat_interval"]);
			$controls_array[] = array($current_location, $control, $date_generator->get_dates());
		}
	}
	
	$portalbox0 = CreateObject('phpgwapi.listbox', array
	(
		'title'		=> "Mine glemte kontroller",
		'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'width'	=> '100%',
		'outerborderwidth'	=> '0',
		'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
	));

	$app_id = $GLOBALS['phpgw']->applications->name2id('controller');
	if( !isset($GLOBALS['portal_order']) ||!in_array($app_id, $GLOBALS['portal_order']) )
	{
		$GLOBALS['portal_order'][] = $app_id;
	}
	$var = array
	(
		'up'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'down'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'close'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'question'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'edit'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id)
	);

	$category_name = array(); // caching
	
	$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
	$cats->supress_info	= true;
	$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => '','globals' => true,'use_acl' => $this->_category_acl));

	$portalbox0->data = array();
	$portalbox0_data = array();
	foreach ($controls_array as $control_instance)
	{
		$curr_location = $control_instance[0];
		$current_control = $control_instance[1];
		$check_lists = $so->get_open_check_lists_for_control($current_control["id"], $curr_location, $from_date_ts);
		$location_array = execMethod('property.bolocation.read_single', array('location_code' => $curr_location));
		$location_name = $location_array["loc1_name"];
		if(isset($current_control['component_id']) && $current_control['component_id'])
		{
			if($short_desc = execMethod('property.soentity.get_short_description', array('location_id' => $current_control['location_id'], 'id' => $current_control['component_id'])))
			{
				$location_name .= "::{$short_desc}";
			}
		}

		foreach($control_areas['cat_list'] as $area)
		{
			if($area['cat_id'] == $current_control["control_area_id"])
			{
				$control_area_name = $area['name'];
			}
		}
		foreach($check_lists as $check_list)
		{
			$next_date = "Frist: " . date($dateformat, $check_list->get_deadline());
			$portalbox0_data[] = array
			($check_list->get_deadline(), array
			(
				'text' => "<span class='title'>{$location_name}</span><span class='control-area'>{$control_area_name}</span> <span class='control'>{$current_control["title"]}</span> <span class='date'>{$next_date}</span>",
				'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list->get_id()))
			));
		}
	}
	//sort data by planned date for check list
	sort($portalbox0_data);
	//$limit = 5;
	$tmp = 0;
	foreach($portalbox0_data as $check_list_dates)
	{
		if($tmp < $limit_no_of_planned)
		{
			$portalbox0->data[] = $check_list_dates[1];
		}
		$tmp++;
	}

	$styling  = "\n".'<!-- BEGIN checklist info -->'."\n ";
	$styling .= "<style> .home_portal_content a{color:#0066CC;text-decoration: none;text-transform: uppercase;} .home_portal{margin: 20px 10px 0;} "; 
	$styling .= " .home-box {background: none repeat scroll 0 0 #EDF5FF; border-color: #DBE5EF; border-radius: 4px; margin: 20px;}";
	$styling .= " .home-box .home_portal{margin: 0;border: 1px solid #DEEAF8;} .home_portal_content{padding:10px;} ";
	$styling .= " .home_portal_title h2{ background: #DEEAF8; margin: 0; padding: 5px 10px;} .home_portal_content ul li{padding: 3px;}";
	$styling .= " .home_portal_content .title{display:inline-block;width:300px;} .home_portal_content .control-area{display:inline-block;width:200px;}";
	$styling .= " .home_portal_content .control{display:inline-block;width:300px;} .home_portal_content .date{display:inline-block;width:300px;}";
	$styling .= "</style>"; 
	$styling .= "\n".'<!-- END checklist info -->'."\n";
	
	echo $styling;
	echo "\n".'<!-- BEGIN checklist info -->'."\n <div class='home-box'>".$portalbox0->draw()."</div>\n".'<!-- END checklist info -->'."\n";
	
	$portalbox1 = CreateObject('phpgwapi.listbox', array
	(
		'title'		=> "Mine planlagte kontroller",
		'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'width'	=> '100%',
		'outerborderwidth'	=> '0',
		'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
	));

	$app_id = $GLOBALS['phpgw']->applications->name2id('controller');
	if( !isset($GLOBALS['portal_order']) ||!in_array($app_id, $GLOBALS['portal_order']) )
	{
		$GLOBALS['portal_order'][] = $app_id;
	}
	$var = array
	(
		'up'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'down'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'close'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'question'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'edit'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id)
	);

	$category_name = array(); // caching
	
	$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
	$cats->supress_info	= true;
	$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => '','globals' => true,'use_acl' => $this->_category_acl));

	$portalbox1->data = array();
	$portalbox1_data = array();
	foreach ($controls_array as $control_instance)
	{
		$curr_location = $control_instance[0];
		$current_control = $control_instance[1];
		$check_lists = $so->get_planned_check_lists_for_control($current_control["id"], $curr_location, $current_control['location_id'], $current_control['component_id']);
		$location_array = execMethod('property.bolocation.read_single', array('location_code' => $curr_location));
		$location_name = $location_array["loc1_name"];
		if(isset($current_control['component_id']) && $current_control['component_id'])
		{
			if($short_desc = execMethod('property.soentity.get_short_description', array('location_id' => $current_control['location_id'], 'id' => $current_control['component_id'])))
			{
				$location_name .= "::{$short_desc}";
			}
		}

		foreach($control_areas['cat_list'] as $area)
		{
			if($area['cat_id'] == $current_control["control_area_id"])
			{
				$control_area_name = $area['name'];
			}
		}
		foreach($check_lists as $check_list)
		{
			$next_date = "Planlagt: " . date($dateformat, $check_list->get_planned_date());
			$portalbox1_data[] = array
			($check_list->get_planned_date(), array
			(
				'text' => "<span class='title'>{$location_name}</span><span class='control-area'>{$control_area_name}</span> <span class='control'>{$current_control["title"]}</span> <span class='date'>{$next_date}</span>",
				'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list->get_id()))
			));
		}
	}
	//sort data by planned date for check list
	sort($portalbox1_data);
	//$limit = 5;
	$tmp = 0;
	foreach($portalbox1_data as $check_list_dates)
	{
		if($tmp < $limit_no_of_planned)
		{
			$portalbox1->data[] = $check_list_dates[1];
		}
		$tmp++;
	}
	echo "\n".'<!-- BEGIN checklist info -->'."\n<div class='home-box'>".$portalbox1->draw()."</div>\n".'<!-- END checklist info -->'."\n";

	$portalbox2 = CreateObject('phpgwapi.listbox', array
	(
		'title'		=> "Mine tildelte kontroller",
		'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'width'	=> '100%',
		'outerborderwidth'	=> '0',
		'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
	));

	$app_id = $GLOBALS['phpgw']->applications->name2id('controller');
	if( !isset($GLOBALS['portal_order']) ||!in_array($app_id, $GLOBALS['portal_order']) )
	{
		$GLOBALS['portal_order'][] = $app_id;
	}
	$var = array
	(
		'up'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'down'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'close'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'question'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'edit'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id)
	);

	foreach ( $var as $key => $value )
	{
//					$portalbox2->set_controls($key,$value);
	}

	$category_name = array(); // caching

	$portalbox2->data = array();
	$portalbox2_data = array();
	foreach ($controls_array as $control_instance)
	{
		$curr_location = $control_instance[0];
		$current_control = $control_instance[1];
		//unset($check_lists);
		$check_lists = $so->get_unplanned_check_lists_for_control($current_control["id"], $curr_location);
		//$control_location = $so_control->getLocationCodeFromControl($current_control->get_id());
		$location_array = execMethod('property.bolocation.read_single', array('location_code' => $curr_location));
		$location_name = $location_array["loc1_name"];
		if(isset($current_control['component_id']) && $current_control['component_id'])
		{
			if($short_desc = execMethod('property.soentity.get_short_description', array('location_id' => $current_control['location_id'], 'id' => $current_control['component_id'])))
			{
				$location_name .= "::{$short_desc}";
			}
		}

		foreach($control_areas['cat_list'] as $area)
		{
			if($area['cat_id'] == $current_control["control_area_id"])
			{
				$control_area_name = $area['name'];
			}
		}

		$current_dates = $control_instance[2];
		
		foreach($current_dates as $current_date)
		{
			if(isset($check_lists))
			{
				foreach($check_lists as $check_list)
				{
					if($current_date > $check_list->get_deadline() && $current_date != $check_list->get_deadline())
					{
						$next_date = "Fristdato: " . date($dateformat, $current_date);
						$portalbox2_data[] = array
						($current_date, array
						(
							'text' => "<span class='title'>{$location_name}</span><span class='control-area'>{$control_area_name}</span> <span class='control'>{$current_control["title"]}</span> <span class='date'>{$next_date}</span>",
							'link' => $GLOBALS['phpgw']->link('/index.php', array
							(
								'menuaction'	=> 'controller.uicheck_list.add_check_list',
								'deadline_ts'	=> $current_date,
								'control_id'	=> $current_control["id"],
								'location_code' => $curr_location,
								'type'			=> $current_control['component_id'] ? 'component' : '',
								'location_id'	=> $current_control['location_id'],
								'component_id'	=> $current_control['component_id']
							))
						));
					}
					else
					{
					    if(!$check_list->get_planned_date())
					    {
    						$next_date = "Fristdato: " . date($dateformat, $check_list->get_deadline());
    						$portalbox2_data[] = array
    						($check_list->get_deadline(), array
    						(
    							'text' => "<span class='title'>{$location_name}</span><span class='control-area'>{$control_area_name}</span> <span class='control'>{$current_control["title"]}</span> <span class='date'>{$next_date}</span>",
    							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list->get_id()))
    						));
					    }					    
					}
				}
			}
			else
			{
				$next_date = "Fristdato: " . date($dateformat, $current_date);
			
				$portalbox2_data[] = array
				($current_date, array
				(
					'text' => "<span class='title'>{$location_name}</span><span class='control-area'>{$control_area_name}</span> <span class='control'>{$current_control["title"]}</span> <span class='date'>{$next_date}</span>",
					'link' => $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'controller.uicheck_list.add_check_list', 
						'deadline_ts'	=> $current_date, 
						'control_id'	=> $current_control["id"], 
						'location_code' => $curr_location,
						'type'			=> $current_control['component_id'] ? 'component' : '',
						'location_id'	=> $current_control['location_id'],
						'component_id'	=> $current_control['component_id']
					))
				));					
			}
		}
	}
	//sort data by due date for check list
	sort($portalbox2_data);
	//$limit = 20;
	//$limit_no_of_assigned = 50;
	$tmp = 0;
	foreach($portalbox2_data as $check_list_dates)
	{
		if($tmp < $limit_no_of_assigned)
		{
			$portalbox2->data[] = $check_list_dates[1];
		}
		$tmp++;
	}
	echo "\n".'<!-- BEGIN assigned checklist info -->'."\n<div class='home-box'>".$portalbox2->draw()."</div>\n".'<!-- END assigned checklist info -->'."\n";
