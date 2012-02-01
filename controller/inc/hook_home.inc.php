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
	include_class('controller', 'check_list_status_info', 'inc/helper/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'location_finder', 'inc/helper/');
	
	$so = CreateObject('controller.socheck_list');
	$so_control = CreateObject('controller.socontrol');
	
	$config	= CreateObject('phpgwapi.config','controller');
	$config->read();
	$limit_no_of_planned = isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls'])? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls'] : (isset($config->config_data['no_of_planned_controls']) && $config->config_data['no_of_planned_controls'] > 0 ? $config->config_data['no_of_planned_controls']:5);
	$limit_no_of_assigned = isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_assigned_controls'])? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_assigned_controls'] : (isset($config->config_data['no_of_assigned_controls']) && $config->config_data['no_of_assigned_controls'] > 0 ? $config->config_data['no_of_assigned_controls']:10);

	//echo '<H1> Hook for controller </H1>';	
	//$location_code = '1101';
	$year = phpgw::get_var('year');
	
	if(empty($year)){
		$year = date("Y");	
	}
	
	$year = intval($year);
				
	//$from_date_ts = strtotime("01/01/$year");
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
		$controls_for_location_array[] = array($location["location_code"], $so_control->get_controls_by_location($location["location_code"], $from_date_ts, $to_date_ts, $repeat_type ));
	}
	
	$controls_array = array();
	$control_dates = array();
	foreach($controls_for_location_array as $control_arr){
		$current_location = $control_arr[0];
		$controls_for_loc_array = $control_arr[1];
		foreach($controls_for_loc_array as $control)
		{
			$date_generator = new date_generator($control->get_start_date(), $control->get_end_date(), $from_date_ts, $to_date_ts, $control->get_repeat_type(), $control->get_repeat_interval());
			$controls_array[] = array($current_location, $control, $date_generator->get_dates());
		}
	}
	
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

	foreach ( $var as $key => $value )
	{
		//			$portalbox->set_controls($key,$value);
	}

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
		$check_lists = $so->get_planned_check_lists_for_control($current_control->get_id());
		$location_array = execMethod('property.bolocation.read_single', array('location_code' => $curr_location));
		$location_name = $location_array["loc1_name"];
		foreach($control_areas['cat_list'] as $area)
		{
			if($area['cat_id'] == $current_control->get_control_area_id())
			{
				$control_area_name = $area['name'];
			}
		}
		foreach($check_lists as $check_list)
		{
			$next_date = "Planlagt: " . date('d/m/Y', $check_list->get_planned_date());
			$portalbox1_data[] = array
			($check_list->get_planned_date(), array
			(
				'text' => "{$location_name} - {$control_area_name} - {$current_control->get_title()} :: {$next_date}",
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
	echo "\n".'<!-- BEGIN checklist info -->'."\n<div class='controller_checklist' style='padding-left: 10px;'>".$portalbox1->draw()."</div>\n".'<!-- END checklist info -->'."\n";

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
		$check_lists = $so->get_planned_check_lists_for_control($current_control->get_id());
		//$control_location = $so_control->getLocationCodeFromControl($current_control->get_id());
		$location_array = execMethod('property.bolocation.read_single', array('location_code' => $curr_location));
		$location_name = $location_array["loc1_name"];
		foreach($control_areas['cat_list'] as $area)
		{
			if($area['cat_id'] == $current_control->get_control_area_id())
			{
				$control_area_name = $area['name'];
			}
		}
		$planned_lists = array();
		foreach($check_lists as $check_list)
		{
			$planned_lists = $check_list->get_deadline();
		}
		$current_dates = $control_instance[2];
		
		foreach($current_dates as $current_date)
		{
			if(isset($check_lists))
			{
				foreach($check_lists as $check_list)
				{
					if($current_date != $check_list->get_deadline())
					{
						$next_date = "Fristdato: " . date('d/m/Y', $current_date);
						$portalbox2_data[] = array
						($current_date, array
						(
							'text' => "{$location_name} - {$control_area_name} - {$current_control->get_title()} :: {$next_date}",
							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'date' => $current_date, 'control_id' => $current_control->get_id(), 'location_code' => '1101'))
						));
					}
				}
			}
			else
			{
				$next_date = "Fristdato: " . date('d/m/Y', $current_date);
				$portalbox2_data[] = array
				($current_date, array
				(
					'text' => "{$location_name} - {$control_area_name} - {$current_control->get_title()} :: {$next_date}",
					'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'date' => $current_date, 'control_id' => $current_control->get_id(), 'location_code' => '1101'))
				));					
			}
		}
	}
	//sort data by due date for check list
	sort($portalbox2_data);
	//$limit = 20;
	$tmp = 0;
	foreach($portalbox2_data as $check_list_dates)
	{
		if($tmp < $limit_no_of_assigned)
		{
			$portalbox2->data[] = $check_list_dates[1];
		}
		$tmp++;
	}
	echo "\n".'<!-- BEGIN assigned checklist info -->'."\n<div class='controller_checklist' style='padding-left: 10px;'>".$portalbox2->draw()."</div>\n".'<!-- END assigned checklist info -->'."\n";
