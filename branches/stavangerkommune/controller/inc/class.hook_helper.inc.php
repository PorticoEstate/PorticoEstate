<?php
	/**
	 * controller - Hook helper
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package controller
	 * @version $Id: class.hook_helper.inc.php 11508 2013-12-05 20:13:48Z sigurdne $
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

	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'check_list_status_info', 'inc/component/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'location_finder', 'inc/helper/');

	/**
	 * Hook helper
	 *
	 * @package controller
	 */
	class controller_hook_helper
	{
		/**
		 * Show info for homepage - called from backend
		 *
		 * @return void
		 */
		public function home_backend()
		{
			$this->home();
		}
		/**
		 * Show info for homepage - called from mobilefrontend
		 *
		 * @return void
		 */
		public function home_mobilefrontend()
		{
			$this->home();
		}
		/**
		 * Show info for homepage
		 *
		 * @return void
		 */
		public function home()
		{

			$location_array = array();
			$component_short_desc = array();
			$component_short_desc[0][0] = '';

			$so_check_list = CreateObject('controller.socheck_list');
			$so_control = CreateObject('controller.socontrol');

			$config	= CreateObject('phpgwapi.config','controller');
			$config->read();
			$limit_no_of_planned = isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls'])? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls'] : (isset($config->config_data['no_of_planned_controls']) && $config->config_data['no_of_planned_controls'] > 0 ? $config->config_data['no_of_planned_controls']:5);
			$limit_no_of_assigned = isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_assigned_controls'])? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_assigned_controls'] : (isset($config->config_data['no_of_assigned_controls']) && $config->config_data['no_of_assigned_controls'] > 0 ? $config->config_data['no_of_assigned_controls']:10);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$styling  = "\n".'<!-- BEGIN checklist info -->'."\n ";
			$styling .= "<style>";
			$styling .= " .home-box { background: none repeat scroll 0 0 #EDF5FF; border-color: #DBE5EF; border-radius: 4px; margin: 5px 20px 20px;}";
			$styling .= " .home-box .home_portal { margin: 0;border: 1px solid #EBF3FC;}";
			$styling .= " .home_portal { margin:20px 20px 0 10px; }";
			$styling .= " .home_portal a { color:#0066CC;text-decoration: none;text-transform: uppercase; clear:both;display:block;}";
			$styling .= " .home_portal h2 { overflow:hidden;clear:left;font-size: 13px;font-weight: bold;text-transform:uppercase; background: #D0DEF4; margin: 0; padding: 2px 10px; color: #1C3C6F;}";
			$styling .= " .home_portal h2 div{ display:block;float:left;cursor: pointer;vertical-align: middle;}";
			$styling .= " .home_portal .title { width:300px;margin:0 20px 0 0;}";
			$styling .= " .home_portal .control-area { width:200px;}";
			$styling .= " .home_portal .control { width:300px;}";
			$styling .= " .home_portal .date { margin-left: 20px;width:130px;}";
			$styling .= " .home_portal li { overflow: hidden;margin: 10px;}";
			$styling .= " .home_portal li div { display: block;float:left;cursor: pointer;vertical-align: middle;}";

			$styling .= " .home_portal_content ul li { clear: both; overflow: hidden;}";
			$styling .= " .home_portal_content { padding:5px 10px;}";
			$styling .= " .property_tickets .home_portal_title h2 { font-size: 20px; padding: 5px 10px;}";

			$styling .= " h2.heading { font-size: 22px; font-weight: normal;margin: 0 0 0 20px;}";
			$styling .= " th.heading { font-size: 22px; font-weight: normal;margin: 0 0 0 20px;}";
			$styling .= " tr.off {background: #DEEAF8 }";

			$styling .= " h4.expand_trigger { clear:both;overflow:hidden;font-size: 12px;color:#031647;background: #DEEAF8;padding:2px 4px;margin:0; }";
			$styling .= " h4.expand_trigger img { float:left;vertical-align:middle;margin-right:3px; }";
			$styling .= " h4.expand_trigger span { float:left;display:block;vertical-align:middle; }";
			$styling .= " h4.expand_trigger span.deadline { margin-right: 10px; }";
			$styling .= " h4.expand_trigger span.num_check_lists { width:200px; }";

			$styling .= " .expand_list{ display:none; overflow:hidden; }";
			$styling .= " .expand_list li{ clear:both;overflow:hidden;margin:5px 0; }";

			$styling .= "</style>";
			$styling .= "\n".'<!-- END checklist info -->'."\n";
			echo $styling;

			//Loaded from home.php

			$script = "<script>";
			$script .= "$(document).ready(function(){";
			$script .= " $('.expand_trigger').live('click', function() {";
			$script .= " var liTag = $(this).closest('li'); ";
			$script .= " var expandList = $(liTag).find('.expand_list'); ";
			$script .= " if( !$(expandList).hasClass('active') ){ $(expandList).show(); $(expandList).addClass('active');  ";
			$script .= " $(liTag).find('img').attr('src', 'controller/images/arrow_down.png');} ";
			$script .= " else{ $(expandList).hide(); $(expandList).removeClass('active');  ";
			$script .= " $(liTag).find('img').attr('src', 'controller/images/arrow_right.png');} ";
			$script .= " return false; ";
			$script .= " })";
			$script .= " })";
			$script .= "</script>";
			echo $script;

			// Fetches my properties
			$criteria = array
			(
				'user_id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'type_id' => 1, // Nivå i bygningsregisteret 1:eiendom
				'role_id' => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
				'allrows' => false
			);

			$location_finder = new location_finder();
			$my_properties = $location_finder->get_responsibilities( $criteria );

			// Fetches my buildings
			$criteria = array
			(
				'user_id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'type_id' => 2, // Nivå i bygningsregisteret 1:eiendom
				'role_id' => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
				'allrows' => false
			);

			$location_finder = new location_finder();
			$my_buildings = $location_finder->get_responsibilities( $criteria );

			$my_locations = array_merge($my_properties, $my_buildings);


		/* =======================================  PLANNED CONTROLS FOR CURRENT USER  ================================= */

			$my_planned_controls_HTML = <<<HTML
				<div class='home_portal'>
					<table>
					<tr>
						<th class='date heading'>Planlagt dato</th>
						<th class='date heading'>Fristdato</th>
						<th class='control heading'>Tittel på kontroll</th>
						<th class='title heading'>Lokasjonsnavn</th>
						<th class='control-area heading'>Kontrollområde</th>
					</tr>
HTML;
			// Todays date
			$from_date_ts =  mktime(0, 0, 0, date("n"), date("j"), date("Y") );
			// One month ahead in time
			$to_date_ts = mktime(0, 0, 0, date("n")+1, date("j"), date("Y") );

		// fetch all repeat types
			$repeat_type = null;

		// Fetches controls current user is responsible for 1 month ahead
			$my_controls = array();
//			$my_controls = $this->get_my_controls($my_locations, $from_date_ts, $to_date_ts, $repeat_type);

			$my_check_lists = $this->get_my_assigned_check_list($from_date_ts, $to_date_ts, $repeat_type);

			$_assigned_list = array();
			foreach ($my_check_lists as $_key => $my_check_list)
			{
				$_assigned_list[$my_check_list['location_code']][$_key] = $my_check_list;
			}

			foreach ($_assigned_list as $location_code => $controls_at_location)
			{
				$my_controls[] = array( $location_code, 'assigned', $controls_at_location );
			} 

			$my_planned_controls = array();
			// Generates an array with planned controls
			foreach($my_controls as $container_arr)
			{
				$location_code = $container_arr[0];
				$control_type = $container_arr[1];
				$controls = $container_arr[2];

				foreach($controls as $my_control)
				{
					if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_DAY)
					{
						// Daily control: To_date assigned to one week ahead in time if repeat type is daily
						$to_date_ts =  mktime(0, 0, 0, date("n"), date("j")+7, date("Y") );
					}
					else if(($my_control["repeat_type"] == controller_control::REPEAT_TYPE_WEEK)
								| ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_MONTH)
								| ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_YEAR))
					{
						// Daily, monthly yearly control: to_date in one month
						$to_date_ts =  mktime(0, 0, 0, date("n")+1, date("j"), date("Y") );
					}

					if($control_type == "location")
					{
						$check_list_array = $so_check_list->get_check_lists_for_control_and_location( $my_control['id'], $location_code, $from_date_ts, $to_date_ts, $repeat_type = null, $filter_assigned_to = true);
						foreach($check_list_array as $check_list)
						{
							$planned_date_for_check_list = $check_list->get_planned_date();

							if($planned_date_for_check_list > 0)
							{
								$my_planned_controls[$planned_date_for_check_list][] = array( $check_list->get_deadline(), $my_control, $check_list->get_id(), "location", $location_code );
							}
						}
					}
					else if($control_type == "component")
					{
						$component = $container_arr[3];
						$check_lists_for_control_and_component = $so_check_list->get_check_lists_for_control_and_component( $my_control['id'], $component['location_id'], $component['id'], $from_date_ts, $to_date_ts, $repeat_type = null );

						foreach($check_lists_for_control_and_component['check_lists_array'] as $check_list)
						{
							$planned_date_for_check_list = $check_list->get_planned_date();

							if($planned_date_for_check_list > 0)
							{
								$my_planned_controls[$planned_date_for_check_list][] = array($check_list->get_deadline(), $my_control, $check_list->get_id(), "component", $component['location_id'], $component['id'] );
							}
						}
					}
					else if($control_type == "assigned")
					{
						$check_list_array = $container_arr[2];
						foreach($check_list_array as $check_list)
						{
							$planned_date_for_check_list = $check_list['planned_date'];

							if($planned_date_for_check_list > 0)
							{
								$my_planned_controls[$planned_date_for_check_list][] = array( $check_list['deadline'], $my_control, $check_list['id'], "location", $location_code );
							}
						}
					}
				}
			}

//			$my_planned_controls_HTML .= "<ul style='overflow:hidden;'>";


			foreach($my_planned_controls as $planned_date_ts => $planned_controls_on_date)
			{
				foreach($planned_controls_on_date as $my_planned_control)
				{

					switch ($_row_class)
					{
						case 'on':
							$_row_class = 'off';
							break;
						case 'off':
							$_row_class = 'on';
							break;
						default:
							$_row_class = 'on';						
					}

					$my_planned_controls_HTML .= "<tr class = '{$_row_class}'>";

					$deadline_ts = $my_planned_control[0];
					$my_control = $my_planned_control[1];
					
					$location_id = isset($my_control['location_id']) && $my_control['location_id'] ? $my_control['location_id'] : 0;
					$component_id = isset($my_control['component_id']) && $my_control['component_id'] ? $my_control['component_id'] : 0;

					$control_area_name = $this->get_control_area_name( $my_control["control_area_id"] );

					$deadline_formatted = date($dateformat, $deadline_ts);
					$planned_formatted = date($dateformat, $planned_date_ts);

					$check_list_id = $my_planned_control[2];
					$location_code = $my_planned_control[4];

					$location_name = $this->get_location_name($location_code);
					
					if($component_id)
					{
						$short_descr = $this->get_short_description($location_id, $component_id);
						$location_name .= "::{$short_descr}";
					}

					$link = "";
				//	$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));
					$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicase.add_case', 'check_list_id' => $check_list_id));
//					$my_planned_controls_HTML .= "<li><a href='$link'><div class='date'>{$planned_formatted}</div><div class='date'>{$deadline_formatted}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a></li>";
					$my_planned_controls_HTML .= "<td><a href='$link'><div class='date'>{$planned_formatted}</div></a></td>";
					$my_planned_controls_HTML .= "<td><a href='$link'><div class='date'>{$deadline_formatted}</div></a></td>";
					$my_planned_controls_HTML .= "<td><a href='$link'><div class='control'>{$my_control['title']}</div></a></td>";
					$my_planned_controls_HTML .= "<td><a href='$link'><div class='title'>{$location_name}</div></a></td>";
					$my_planned_controls_HTML .= "<td><a href='$link'><div class='control-area'>{$control_area_name}</div></a></td>";
					$my_planned_controls_HTML .= "</tr>";
				}
			}

			if(count( $planned_controls_on_date ) > 1 )
			{
//				$my_planned_controls_HTML .= "</li></ul>";
			}

			$my_planned_controls_HTML .= "</table></div>"; // home_portal

			echo "\n".'<!-- BEGIN checklist info -->'."\n <h2 class='heading'>Mine planlagte kontroller</h2><div class='home-box'>" . $my_planned_controls_HTML . "</div>\n".'<!-- END checklist info -->'."\n";


	      {
			/* =======================================  UNDONE ASSIGNED CONTROLS FOR CURRENT USER  ================================= */

		// from date is set to 3 months back in time
			$from_date_ts =  mktime(0, 0, 0, date("n")-3, date("j"), date("Y") );
			$to_date_ts =  mktime(0, 0, 0, date("n"), date("j"), date("Y") );

		// fetch all repeat types
			$repeat_type = null;

		// Fetches controls current user is responsible for 3 months back in time
			$my_controls = $this->get_my_controls($my_locations, $from_date_ts, $to_date_ts, $repeat_type);
			$my_undone_controls = array();

			// Generates an array containing undone controls
			foreach($my_controls as $container_arr)
			{
				$location_code = $container_arr[0];
				$control_type = $container_arr[1];
				$controls = $container_arr[2];

				foreach($controls as $my_control)
				{
					if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_DAY)
					{
						// DAILY CONTROLS: Fetch undone controls one week back in time
						$from_date_ts =  mktime(0, 0, 0, date("n"), date("j")-7, date("Y") );
					}
					else if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_WEEK)
					{
						// WEEKLY CONTROLS: Fetch undone controls one month back in time
						$from_date_ts =  mktime(0, 0, 0, date("n")-1, date("j"), date("Y") );
					}
					else if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_MONTH)
					{
						// MONTHLY CONTROLS: Fetch undone controls three months back in time
						$from_date_ts =  mktime(0, 0, 0, date("n")-3, date("j"), date("Y") );
					}
					else if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_YEAR)
					{
						// YEARLY CONTROLS: Fetch undone controls one year back in time
						$from_date_ts =  mktime(0, 0, 0, date("n"), date("j"), date("Y")-1 );
					}

					$date_generator = new date_generator($my_control["start_date"], $my_control["end_date"], $from_date_ts, $to_date_ts, $my_control["repeat_type"], $my_control["repeat_interval"]);
					$deadline_dates_for_control = $date_generator->get_dates();

					$check_list_array = array();
					foreach($deadline_dates_for_control as $deadline_ts)
					{
						$check_list = null;

						if($control_type == "location")
						{
							$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, $location_code, null, null, "location"	);
						}
						else if($control_type == "component")
						{
							$component = $container_arr[3];

							$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, null, $component['location_id'], $component['id'], "component"	);
						}
						$control_id = $my_control['id'];

						if($check_list == null & $control_type == "location")
						{
							$my_undone_controls[$deadline_ts][] = array("add", $deadline_ts, $my_control, "location", $location_code );
						}
						else if($check_list == null & $control_type == "component")
						{
							$component = $container_arr[3];
							$my_undone_controls[$deadline_ts][]= array("add", $deadline_ts, $my_control, "component", $component['location_id'], $component['id'] );
						}
						else if( ($check_list->get_status() == controller_check_list::STATUS_NOT_DONE) || ($check_list->get_status() == controller_check_list::STATUS_CANCELED) )
						{
							$my_undone_controls[$deadline_ts][] = array("edit", $deadline_ts, $my_control, $check_list->get_id(), $location_code );
						}
					}

				}

			}

			//Add assigned
			$my_check_lists = $this->get_my_assigned_check_list($from_date_ts, $to_date_ts, $repeat_type, true);

/*??
			$_assigned_list = array();
			foreach ($my_check_lists as $_key => $my_check_list)
			{
				$_assigned_list[$my_check_list['location_code']][$_key] = $my_check_list;
			}
*/
			foreach ($my_check_lists as $_key => $my_check_list)
			{
				$my_undone_controls[$my_check_list['deadline']][] = array("edit", $my_check_list['deadline'], $my_check_list, $_key, $my_check_list['location_code'] );
			}
//_debug_array($my_undone_controls);
	
			ksort($my_undone_controls);

			$my_undone_controls_HTML = <<<HTML
			 <div class='home_portal'>
			 	<h2>
			 		<div class='date heading'>Fristdato</div>
			 		<div class='control heading'>Tittel på kontroll</div>
			 		<div class='title heading'>Lokasjonsnavn</div>
			 		<div class='control-area heading'>Kontrollområde</div>
			 	</h2>
HTML;
			// Sorts my_undone_controls by deadline date
			ksort($my_undone_controls);

			$my_undone_controls_HTML .= "<ul>";

			foreach($my_undone_controls as $date_ts => $controls_on_date)
			{
				// If number of controls on a date exceeds 1 it will be put in expand list
				if(count( $controls_on_date) > 1 )
				{
					$my_undone_controls_HTML .= "<li>";
					$my_undone_controls_HTML .= "<a href='#'><h4 class='expand_trigger'><img height='12' src='controller/images/arrow_right.png' /><span class='deadline'>"  . date($dateformat, $date_ts) .  "</span><span class='num_check_lists'>(" .  count($controls_on_date) . " kontroller)</span></h4></a>";
					$my_undone_controls_HTML .= "<ul class='expand_list'>";
				}

				foreach($controls_on_date as $my_undone_control)
				{
					$check_list_status = $my_undone_control[0];
					$deadline_ts = $my_undone_control[1];
					$my_control = $my_undone_control[2];
					$control_area_name = $this->get_control_area_name( $my_control["control_area_id"] );

					$date_str = date($dateformat, $deadline_ts);

					if($check_list_status == "add")
					{
						$check_list_type = $my_undone_control[3];
						if($check_list_type == "location")
						{
							$location_code = $my_undone_control[4];

							$location_name = $this->get_location_name($location_code);

							if(count( $controls_on_date) > 1 )
							{
								$link = "";
								$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "location", 'control_id' => $my_control['id'], 'location_code' => $location_code, 'deadline_ts' => $deadline_ts));

								$my_undone_controls_HTML .= "<li><a href='{$link}'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a></li>";
							}
							else
							{
								$link = "";
								$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "location", 'control_id' => $my_control['id'], 'location_code' => $location_code, 'deadline_ts' => $deadline_ts));

								$my_undone_controls_HTML .= "<a href='{$link}'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a>";
							}

						}
						else if($check_list_type == "component")
						{
							$location_id = $my_undone_control[4];
							$component_id = $my_undone_control[5];

							$short_descr = $this->get_short_description($location_id, $component_id);
							if(count( $controls_on_date) > 1 )
							{
								$link = "";
								$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "component", 'control_id' => $my_control['id'], 'location_id' => $location_id, 'component_id' => $component_id, 'deadline_ts' => $deadline_ts));

								$my_undone_controls_HTML .= "<li><a href='{$link}'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$short_descr}</div><div class='control-area'>{$control_area_name}</div></a></li>";
							}
							else
							{
								$link = "";
								$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "component", 'control_id' => $my_control['id'], 'location_id' => $location_id, 'component_id' => $component_id, 'deadline_ts' => $deadline_ts));

								$my_undone_controls_HTML .= "<a href='{$link}'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$short_descr}</div><div class='control-area'>{$control_area_name}</div></a>";
							}
						}
					}
					else if($check_list_status == "edit")
					{

						$location_id = isset($my_control['location_id']) && $my_control['location_id'] ? $my_control['location_id'] : 0;
						$component_id = isset($my_control['component_id']) && $my_control['component_id'] ? $my_control['component_id'] : 0;


						$check_list_id = $my_undone_control[3];
						$location_code = $my_undone_control[4];

						$location_name = $this->get_location_name($location_code);

						if($component_id)
						{
							$short_descr = $this->get_short_description($location_id, $component_id);

							$location_name .= "::{$short_descr}";
						}

						if(count( $controls_on_date) > 1 )
						{
							$link = "";
							$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));

							$my_undone_controls_HTML .= "<li><a href='{$link}'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a></li>";
						}
						else
						{
							$link = "";
							$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));

							$my_undone_controls_HTML .= "<li><a href='$link'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a></li>";
						}
					}
				}

				if(count( $controls_on_date ) > 1 )
				{
					$my_undone_controls_HTML .= "</ul>";
					$my_undone_controls_HTML .= "</li>";
				}
			}

			$my_undone_controls_HTML .= "</ul>";

			$my_undone_controls_HTML .= "</div>"; // home_portal

			echo "\n".'<!-- BEGIN checklist info -->'."\n <h2 class='heading'>Mine ugjorte kontroller</h2><div class='home-box'>".$my_undone_controls_HTML."</div>\n".'<!-- END checklist info -->'."\n";


			/* ================================  CONTROLS ASSIGNED TO CURRENT USER  ================================= */

			$my_assigned_controls_HTML = <<<HTML
				<div class='home_portal'>
					<h2>
						<div class='date heading'>Fristdato</div>
						<div class='control heading'>Tittel på kontroll</div>
						<div class='title heading'>Lokasjonsnavn</div>
						<div class='control-area heading'>Kontrollområde</div>
					</h2>
HTML;

			$from_date_ts =  strtotime("now");
			$to_date_ts = mktime(0, 0, 0, date("n")+1, date("j"), date("Y") );

			// fetch all repeat types
			$repeat_type = null;

			// Fetches controls current user is responsible for 1 month ahead in time
			$my_controls = array();
			$my_controls = $this->get_my_controls($my_locations, $from_date_ts, $to_date_ts, $repeat_type);

			$my_assigned_controls = array();

			$from_date_ts =  mktime(0, 0, 0, date("n"), date("j"), date("Y") );

			// Generates an array with undone controls
			foreach($my_controls as $container_arr)
			{
				$location_code = $container_arr[0];
				$control_type = $container_arr[1];
				$controls = $container_arr[2];

				foreach($controls as $my_control)
				{
					if($my_control["repeat_type"] == controller_control::REPEAT_TYPE_DAY)
					{
						// Daily control: Todate in one week
						$to_date_ts =  mktime(0, 0, 0, date("n"), date("j")+7, date("Y") );
					}
					else if(($my_control["repeat_type"] == controller_control::REPEAT_TYPE_WEEK)
								| ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_MONTH)
								| ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_YEAR))
					{
						// Daily, monthly yearly control: to_date in one month
						$to_date_ts =  mktime(0, 0, 0, date("n")+1, date("j"), date("Y") );
					}

					$date_generator = new date_generator($my_control["start_date"], $my_control["end_date"], $from_date_ts, $to_date_ts, $my_control["repeat_type"], $my_control["repeat_interval"]);
					$deadline_dates_for_control = $date_generator->get_dates();

					$check_list_array = array();
					foreach($deadline_dates_for_control as $deadline_ts)
					{
						$check_list = null;

						if($control_type == "location")
						{
							// Gets checklist for control with current date as deadline if there exists one
							$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, $location_code, null, null, "location"	);
						}
						else if($control_type == "component")
						{
							$component = $container_arr[3];

							// Gets checklist for control with current date as deadline if there exists one
							$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, null, $component['location_id'], $component['id'], "component"	);
						}

							// Check if there is a checklist on the deadline
						if($check_list == null)
						{
							if($control_type == "location")
							{
								$my_assigned_controls[$deadline_ts][] = array("add", $deadline_ts, $my_control, "location", $location_code );
							}
							else if($control_type == "component")
							{
								$component = $container_arr[3];
								$my_assigned_controls[$deadline_ts][] =  array("add", $deadline_ts, $my_control, "component", $component['location_id'], $component['id'] );
							}
						}
						// Do not put checklist with status planned in list
						else if( ($check_list->get_planned_date() == '' || $check_list->get_planned_date() == 0 ) && ( $check_list->get_status() == controller_check_list::STATUS_NOT_DONE || ($check_list->get_status() == controller_check_list::STATUS_CANCELED)) )
						{
							$my_assigned_controls[$deadline_ts][] = array("edit", $deadline_ts, $my_control, $check_list->get_id(), $location_code );
						}
					}
				}
			}

			// Sorts my_undone_controls by deadline date
			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;
			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => '','globals' => true,'use_acl' => $this->_category_acl));

			$my_assigned_controls_HTML .= "<ul>";

			foreach($my_assigned_controls as $date_ts => $assigned_controls_on_date)
			{
				if(count( $assigned_controls_on_date) > 1 )
				{
					$my_assigned_controls_HTML .= "<li>";
					$my_assigned_controls_HTML .= "<a href='#'><h4 class='expand_trigger'><img height='12' src='controller/images/arrow_right.png' /><span class='deadline'>"  . date($dateformat, $date_ts) .  "</span><span class='num_controls'>(" .  count($assigned_controls_on_date) . " kontroller)</span></h4></a>";
					$my_assigned_controls_HTML .= "<ul class='expand_list'>";
				}

				foreach($assigned_controls_on_date as $my_assigned_control)
				{
					$check_list_status = $my_assigned_control[0];
					$deadline_ts = $my_assigned_control[1];
					$my_control = $my_assigned_control[2];

					reset($control_areas['cat_list']);

					foreach($control_areas['cat_list'] as $area)
					{
						if($area['cat_id'] == $my_control["control_area_id"])
						{
							$control_area_name = $area['name'];
						}
					}

					$date_str = date($dateformat, $deadline_ts);

					if($check_list_status == "add")
					{
						$check_list_type = $my_assigned_control[3];

						if($check_list_type == "location")
						{
							$location_code = $my_assigned_control[4];

							$location_name = $this->get_location_name($location_code);

							$link = "";
							$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "location", 'control_id' => $my_control['id'], 'location_code' => $location_code, 'deadline_ts' => $deadline_ts));

							$my_assigned_controls_HTML .= "<li><a href='$link'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a></li>";
						}
						else if($check_list_type == "component")
						{
							$location_id = $my_assigned_control[4];
							$component_id = $my_assigned_control[5];
							
							$short_descr = $this->get_short_description($location_id, $component_id);

							$link = "";
							$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list', 'type' => "component", 'control_id' => $my_control['id'], 'location_id' => $location_id, 'component_id' => $component_id, 'deadline_ts' => $deadline_ts));

							$my_assigned_controls_HTML .= "<li><a href='$link'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$short_descr}</div><div class='control-area'>{$control_area_name}</div></a></li>";
						}
					}
					else if($check_list_status == "edit")
					{
						$check_list_id = $my_assigned_control[3];
						$location_code = $my_assigned_control[4];

						$location_name = $this->get_location_name($location_code);

						$link = "";
						$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));

						$my_assigned_controls_HTML .= "<li><a href='$link'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a></li>";
					}
				}

				if(count( $assigned_controls_on_date ) > 1 )
				{
					$my_assigned_controls_HTML .= "</li>";
					$my_assigned_controls_HTML .= "</ul>";
				}

			}

			$my_assigned_controls_HTML .= "</ul>";
			$my_assigned_controls_HTML .= "</div>"; // home_portal

			echo "\n".'<!-- BEGIN checklist info -->'."\n <h2 class='heading'>Mine tildelte kontroller</h2><div class='home-box'>" . $my_assigned_controls_HTML . "</div>\n".'<!-- END checklist info -->'."\n";
     		}
		}
		/* ================================  FUNCTIONS  ======================================== */


		function get_my_assigned_check_list($from_date_ts, $to_date_ts, $repeat_type, $completed = null)
		{
			$check_list_array = array();

			$so_control = CreateObject('controller.socontrol');

			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$assigned_check_list_at_location = $so_control->get_assigned_check_list_at_location( $from_date_ts, $to_date_ts, $repeat_type, $user_id,$completed, 'return_array');

			foreach ($assigned_check_list_at_location as $assigned_check_list)
			{
				$check_list_array[$assigned_check_list['id']] = $assigned_check_list;
			}
			unset($assigned_check_list);

			$assigned_check_list_at_component = $so_control->get_assigned_check_list_by_component( $from_date_ts, $to_date_ts, $repeat_type, $user_id, $completed, 'return_array');

			foreach ($assigned_check_list_at_component as $assigned_check_list)
			{
				$check_list_array[$assigned_check_list['id']] = $assigned_check_list;
			}

			return $check_list_array;
		}


		function get_my_controls($my_locations, $from_date_ts, $to_date_ts, $repeat_type)
		{
			$so_control = CreateObject('controller.socontrol');

			$my_controls = array();

			foreach($my_locations as $location)
			{
				$components_with_controls_array = array();
				$controls_at_location = array();
				$location_code = $location["location_code"];

				$controls_at_location = $so_control->get_controls_by_location( $location_code, $from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"] );

				$level = count(explode('-', $location_code));

				if($level == 1)
				{
					// Fetches all controls for the components for a location within time period
					$filter = "bim_item.location_code = '$location_code' ";
					$components_with_controls_array = $so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"], $filter);
				}
				else
				{
					// Fetches all controls for the components for a location within time period
					$filter = "bim_item.location_code LIKE '$location_code%' ";
					$components_with_controls_array = $so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"], $filter);
				}

				if( $controls_at_location )
				{
					// Saves location code, location type and an array containing controls at locations
					$my_controls[] = array( $location_code, 'location', $controls_at_location );
				}

				if( $components_with_controls_array )
				{
					foreach($components_with_controls_array as $component)
					{
					// Saves location code, location type, an array containing controls at locations and component object
						$my_controls[] = array( $location_code, 'component', $component['controls_array'], $component );
					}
				}
			}

			return $my_controls;
		}

		function get_control_area_name( $control_area_id )
		{
			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;

			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => '','globals' => true,'use_acl' => 0));

			foreach($control_areas['cat_list'] as $area)
			{
				if( $area['cat_id'] == $control_area_id )
				{
					$control_area_name = $area['name'];
				}
			}

			return $control_area_name;
		}
		
		function get_location_name($location_code)
		{
			static $location_array = array();
			if(!isset($location_array[$location_code]) || !$location_array[$location_code])
			{
				$_location_info = execMethod('property.bolocation.read_single', array
					(
						'location_code' => $location_code,
						'extra'			=> array('noattrib' => true)
					)
				);

				$_loc_name_arr = array();
				for ($i=1; $i < count(explode('-', $location_code)) +1;$i++)
				{
					$_loc_name_arr[] = $_location_info["loc{$i}_name"];
				}

				$location_array[$location_code] = implode(' | ',$_loc_name_arr);
			}

			return $location_array[$location_code];
		}
		
		function get_short_description($location_id, $component_id)
		{
			static $component_short_desc = array();

			if(!isset($component_short_desc[$location_id][$component_id]))
			{
				$component_short_desc[$location_id][$component_id] = execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));
			}

			return	$component_short_desc[$location_id][$component_id];
		}
	}
