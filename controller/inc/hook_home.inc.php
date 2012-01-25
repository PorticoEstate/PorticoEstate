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
	include_class('controller', 'calendar_builder', 'inc/component/');
	include_class('controller', 'location_finder', 'inc/helper/');
	
	$so = CreateObject('controller.socheck_list');
	$so_control = CreateObject('controller.socontrol');

	echo '<H1> Hook for controller </H1>';	
	$location_code = '1101';
	$year = phpgw::get_var('year');
	
	if(empty($year)){
		$year = date("Y");	
	}
	
	$year = intval($year);
				
	$from_date_ts = strtotime("01/01/$year");
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
	print_r($my_locations);
	
	if(empty($location_code)){
		$location_code = $my_locations[0]["location_code"];	
	}
	
	$repeat_type = null;
	
	$controls_for_location_array = $so_control->get_controls_by_location($location_code, $from_date_ts, $to_date_ts, $repeat_type );
	
	$calendar_builder = new calendar_builder($from_date_ts, $to_date_ts);

	$controls_calendar_array = array();

	// Puts aggregate values for daily controls in a twelve month array 
	foreach($controls_for_location_array as $control){
		if($control->get_repeat_type() == 0){
			$controls_calendar_array = $calendar_builder->build_agg_calendar_array($controls_calendar_array, $control, $location_code, $year);
		}
	}
	
	$repeat_type = 2;
	$control_check_list_array = $so->get_check_lists_for_location( $location_code, $from_date_ts, $to_date_ts, $repeat_type );
	
	$controls_calendar_array = $calendar_builder->build_calendar_array( $controls_calendar_array, $control_check_list_array, 12, "view_months" );
	//print_r($controls_calendar_array);
	$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
	
	$portalbox = CreateObject('phpgwapi.listbox', array
	(
		'title'		=> "Mine kontroller",
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

	$portalbox->data = array();
	foreach ($control_check_list_array as $checklist)
	{
/*		if(!$ticket['subject'])
		{
			if(!isset($category_name[$ticket['cat_id']]))
			{
				$ticket['subject']= execMethod('property.botts.get_category_name', $ticket['cat_id']);
				$category_name[$ticket['cat_id']] = $ticket['subject'];
			}
			else
			{
				$ticket['subject'] = $category_name[$ticket['cat_id']];
			}
		}

		$location = execMethod('property.bolocation.read_single', array('location_code' => $ticket['location_code'], 'extra' => array('view' => true))); 

		$group = '';
		if($ticket['group_id'])
		{
			$group = '[' . $GLOBALS['phpgw']->accounts->get($ticket['group_id'])->__toString() . ']';
		}*/
		$portalbox->data[] = array
		(
			'text' => "kontroll :: {$checklist->get_id()}",
			'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.index'))
		);
	}
	
	$portalbox->data[] = array
	(
		'text' => "test :: test",
		'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.index'))
	);

	echo "\n".'<!-- BEGIN ticket info -->'."\n".$portalbox->draw()."\n".'<!-- END ticket info -->'."\n";

	//var_dump($location_array);
	//$calendar->view_calendar_for_year();
