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

		private $home_alternative;
		private $skip_portalbox_controls;
		var $_category_acl;

		public function __construct()
		{
			$config					 = CreateObject('phpgwapi.config', 'controller');
			$config->read();
			$this->home_alternative	 = isset($config->config_data['home_alternative']) && $config->config_data['home_alternative'] == 1 ? true : false;
		}

		/**
		 * Show info for homepage - called from backend
		 *
		 * @return void
		 */
		public function home_backend()
		{
			$this->status_componants();
		}

		/**
		 * Show info for homepage - called from mobilefrontend
		 *
		 * @return void
		 */
		public function home_mobilefrontend()
		{
			$this->status_componants();
		}

		public function status_componants()
		{
			$app_id = $GLOBALS['phpgw']->applications->name2id('controller');
			if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
			{
				$GLOBALS['portal_order'][] = $app_id;
			}

			$component_short_desc		 = array();
			$component_short_desc[0][0]	 = '';

			$so_check_list = CreateObject('controller.socheck_list');

			$config				 = CreateObject('phpgwapi.config', 'controller');
			$config->read();
			$limit_no_of_planned = null;//isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls']) ? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls'] : (isset($config->config_data['no_of_planned_controls']) && $config->config_data['no_of_planned_controls'] > 0 ? $config->config_data['no_of_planned_controls'] : 5);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			/* =======================================  PLANNED CONTROLS FOR CURRENT USER  ================================= */

			// Todays date
			$from_date_ts	 = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
			// One month ahead in time
			$to_date_ts		 = mktime(0, 0, 0, date("n") + 1, date("j"), date("Y"));

			// fetch all repeat types
			$repeat_type = null;

			//hack to avoid duplicate controls
			$already_assigned = array();

			// Fetches controls current user is responsible for 1 month ahead
			$my_controls = array();

			$my_check_lists = $this->get_my_assigned_check_list($from_date_ts, $to_date_ts, $repeat_type, null, $limit_no_of_planned);


			$my_planned_controls_HTML = <<<HTML
				<style>
				 span.link {
				  background: none!important;
				  border: none;
				  padding: 0!important;
				  /*optional*/
				  font-family: arial, sans-serif;
				  /*input has OS specific font-family*/
				  color: #069;
				  text-decoration: underline;
				  cursor: pointer;
				}
				</style>
					<table class = "pure-table pure-table-bordered" width="100%">
						<thead>
							<tr>
								<th>Start</th>
								<th>Planlagt dato</th>
								<th>Fristdato</th>
								<th>Tittel på kontroll</th>
								<th>Lokasjonsnavn</th>
								<!--th>Kontrollområde</th-->
							</tr>
						</thead>
						<tbody>
HTML;

			$found_at_least_one = false;

			foreach ($my_check_lists as $check_list_id => $check_list)
			{
				$found_at_least_one			 = true;
				$my_planned_controls_HTML	 .= "<tr>";

				$deadline_ts = $check_list['deadline'];

				$location_id	 = isset($check_list['location_id']) && $check_list['location_id'] ? $check_list['location_id'] : 0;
				$component_id	 = isset($check_list['component_id']) && $check_list['component_id'] ? $check_list['component_id'] : 0;
				if($component_id)
				{
					$already_assigned[$component_id][$check_list['control_id']] = $check_list['planned_date'];
				}

				$control_area_name = $this->get_control_area_name($check_list["control_area_id"]);

				$deadline_formatted	 = date($dateformat, $deadline_ts);
				$planned_formatted	 = date($dateformat, $check_list['planned_date']);

				$location_code	 = $check_list['location_code'];

				$location_name = $this->get_location_name($location_code);

				if ($component_id)
				{
					$short_descr	 = $this->get_short_description($location_id, $component_id);
					$location_name	 .= "::{$short_descr}";
				}

				$link						 = $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'controller.uicase.add_case',
					'check_list_id'	 => $check_list_id));
				$my_planned_controls_HTML	 .= "<td><a href='$link' role='button' class='pure-button pure-button-primary'>Start</a></td>";
				$my_planned_controls_HTML	 .= "<td>{$planned_formatted}</td>";
				$my_planned_controls_HTML	 .= "<td>{$deadline_formatted}</td>";
				$my_planned_controls_HTML	 .= "<td>{$check_list['title']}</td>";
				$my_planned_controls_HTML	 .= "<td>{$location_name}</td>";
	//			$my_planned_controls_HTML	 .= "<td>{$control_area_name}</td>";
				$my_planned_controls_HTML	 .= "</tr>";
			}


			if (!$found_at_least_one)
			{
				$my_planned_controls_HTML .= "<tr><td colspan=\"5\">No records found</td></tr>";
			}

			$my_planned_controls_HTML .= "</tbody></table>"; // home_portal

			echo "<!-- BEGIN checklist info -->"
			. "<div class=\"container\">"
			. "		<div class=\"row mt-4\">\n"
			. "			\n <h2 class='heading'>Mine planlagte kontroller / " . count($my_check_lists) . "</h2>"
			. "			<div class='home-box'>" . $my_planned_controls_HTML . "</div>\n"
			. "		</div>"
			. "</div>"
			. '<!-- END checklist info -->' . "\n";
			{

				/* ================================  CONTROLS ASSIGNED TO CURRENT USER  ================================= */


				$from_date_ts	 = strtotime("now");
				$to_date_ts		 = mktime(0, 0, 0, date("n") + 1, date("j"), date("Y"));

				// fetch all repeat types
				$repeat_type = null;

				// Fetches my properties
				$criteria = array
					(
					'user_id'	 => $GLOBALS['phpgw_info']['user']['account_id'],
					'type_id'	 => 1, // Nivå i bygningsregisteret 1:eiendom
					'role_id'	 => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
					'allrows'	 => true,
					'bypass_responsibility' => true
				);

				$location_finder = new location_finder();
				$my_properties	 = $location_finder->get_responsibilities($criteria);

				// Fetches my buildings
				$criteria = array
					(
					'user_id'	 => $GLOBALS['phpgw_info']['user']['account_id'],
					'type_id'	 => 2, // Nivå i bygningsregisteret 1:eiendom
					'role_id'	 => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
					'allrows'	 => true,
					'bypass_responsibility' => true
				);

				$location_finder = new location_finder();
				$my_buildings	 = $location_finder->get_responsibilities($criteria);

				$my_locations = array_merge($my_properties, $my_buildings);

				// Fetches controls current user is responsible for 1 month ahead in time
				$my_controls = $this->get_my_controls($my_locations, $from_date_ts, $to_date_ts, $repeat_type);

//				_debug_array(date('Y-m-d'), $to_date_ts);
//				_debug_array($my_controls);
				$my_assigned_controls = array();

				$from_date_ts = mktime(0, 0, 0, date("n"), date("j"), date("Y"));

				// Generates an array with undone controls
				foreach ($my_controls as $container_arr)
				{
					$location_code	 = $container_arr[0];
					$control_type	 = $container_arr[1];
					$controls		 = $container_arr[2];

					foreach ($controls as $my_control)
					{

						if ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_DAY)
						{
							// Daily control: Todate in one week
							$to_date_ts = mktime(0, 0, 0, date("n"), date("j") + 7, date("Y"));
						}
						else if (($my_control["repeat_type"] == controller_control::REPEAT_TYPE_WEEK) | ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_MONTH) | ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_YEAR))
						{
							// Daily, monthly yearly control: to_date in one month
							$to_date_ts = mktime(0, 0, 0, date("n") + 1, date("j"), date("Y"));
						}

						$date_generator				 = new date_generator($my_control["start_date"], $my_control["end_date"], $from_date_ts, $to_date_ts, $my_control["repeat_type"], $my_control["repeat_interval"]);
						$deadline_dates_for_control	 = $date_generator->get_dates();

						$check_list_array = array();
						foreach ($deadline_dates_for_control as $deadline_ts)
						{
							/*
								$already_assigned[<component_id>][<control_id>] = <deadline_ts>
							*/
						//	continue;
							$check_list = null;

							if ($control_type == "location")
							{
								// Gets checklist for control with current date as deadline if there exists one
								$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, $location_code, null, null, "location");
							}
							else if ($control_type == "component")
							{
								$component = $container_arr[3];

								//hack to avoid duplicate controls
								if (isset($already_assigned[$component['id']][$my_control['id']]) && $already_assigned[$component['id']][$my_control['id']] == $deadline_ts)
								{
//										_debug_array("duplicate control");
									continue;
								}

							// Gets checklist for control with current date as deadline if there exists one
								$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, null, $component['location_id'], $component['id'], "component");
							}

							// Check if there is a checklist on the deadline
							if ($check_list == null)
							{
								if ($control_type == "location")
								{
									$my_assigned_controls[$deadline_ts][] = array("add", $deadline_ts, $my_control,
										"location", $location_code);
								}
								else if ($control_type == "component")
								{
									$component								 = $container_arr[3];
									$my_assigned_controls[$deadline_ts][]	 = array("add", $deadline_ts, $my_control,
										"component", $component['location_id'], $component['id'], $location_code);
								}
							}
							// Do not put checklist with status planned in list
							else if (($check_list->get_planned_date() == '' || $check_list->get_planned_date() == 0 ) && ( $check_list->get_status() == controller_check_list::STATUS_NOT_DONE || ($check_list->get_status() == controller_check_list::STATUS_CANCELED)))
							{
								$my_assigned_controls[$deadline_ts][] = array("edit", $deadline_ts, $my_control,
									$check_list->get_id(), $location_code);
							}
						}
					}
				}

				// Sorts my_controls by deadline date
				$cats				 = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info	 = true;
				$control_areas		 = $cats->formatted_xslt_list(array('format'	 => 'filter', 'selected'	 => '',
					'globals'	 => true, 'use_acl'	 => $this->_category_acl));

				$my_assigned_controls_HTML = <<<HTML
					<table class = "pure-table pure-table-bordered" width="100%">
						<thead>
							<tr>
								<th>Planlegg</th>
								<th>Fristdato</th>
								<th>Tittel på kontroll</th>
								<th>Lokasjonsnavn</th>
							</tr>
						</thead>
						<tbody>
HTML;

				$found_at_least_one = false;

				foreach ($my_assigned_controls as $date_ts => $assigned_controls_on_date)
				{
					foreach ($assigned_controls_on_date as $my_assigned_control)
					{
						$my_assigned_controls_HTML	 .= "<tr>";
						$found_at_least_one			 = true;
						$check_list_status			 = $my_assigned_control[0];
						$deadline_ts				 = $my_assigned_control[1];
						$my_control					 = $my_assigned_control[2];

						reset($control_areas['cat_list']);

						foreach ($control_areas['cat_list'] as $area)
						{
							if ($area['cat_id'] == $my_control["control_area_id"])
							{
								$control_area_name = $area['name'];
							}
						}

						$date_str = date($dateformat, $deadline_ts);

						if ($check_list_status == "add")
						{
							$check_list_type = $my_assigned_control[3];

							if ($check_list_type == "location")
							{
								$location_code = $my_assigned_control[4];

								$location_name = $this->get_location_name($location_code);

								$control_link_data =  array(
									'menuaction'	 => 'controller.uicheck_list.add_check_list',
									'type'			 => "location",
									'control_id'	 => $my_control['id'],
									'serie_id'		 => $my_control['serie_id'],
									'location_code'	 => $location_code,
									'deadline_ts'	 => $deadline_ts,
									'assigned_to'	 => $GLOBALS['phpgw_info']['user']['account_id'],
									'location_code'	 => $location_code,

								);

								$link = $GLOBALS['phpgw']->link('/index.php', $control_link_data);

								$month = date('m', $deadline_ts);
								$control_link = json_encode($control_link_data);
								$_onclick = "perform_action(\"set_planning_month\", {$control_link}, {$month});";
								$_link = "<span tabindex=\"0\" role='button' class=\"pure-button pure-button-primary\" onclick='{$_onclick}'>Planlegg</span>";

								$my_assigned_controls_HTML .= ""
									. "<td>$_link</td>"
									. "<td>{$date_str}</td>"
									. "<td>" . strip_tags($my_control['description']). "</td>"
									. "<td>{$location_name}</td>"
						//			. "<td>{$control_area_name}</td>"
									. "";
							}
							else if ($check_list_type == "component")
							{
								$location_id	 = $my_assigned_control[4];
								$component_id	 = $my_assigned_control[5];
								$location_code	 = $my_assigned_control[6];

								$location_name = $this->get_location_name($location_code);

								if ($component_id)
								{
									$short_descr	 = $this->get_short_description($location_id, $component_id);
									$location_name	 .= "::{$short_descr}";
								}

								$control_link_data = array(
									'menuaction'	 => 'controller.uicheck_list.add_check_list',
									'type'			 => "component",
									'control_id'	 => $my_control['id'],
									'location_id'	 => $location_id,
									'component_id'	 => $component_id,
									'deadline_ts'	 => $deadline_ts,
									'assigned_to'	 => $GLOBALS['phpgw_info']['user']['account_id'],
									'location_code'	 => $location_code,
									'serie_id'		 => $my_control['serie_id'],
								);

								$link = $GLOBALS['phpgw']->link('/index.php', $control_link_data);

								$month = date('m', $deadline_ts);
								$control_link = json_encode($control_link_data);
								$_onclick = "perform_action(\"set_planning_month\", {$control_link}, {$month});";
								$_link = "<span tabindex=\"0\" role='button' class=\"pure-button pure-button-primary\" onclick='{$_onclick}'>Planlegg</span>";

								$my_assigned_controls_HTML .= ""
									. "<td>$_link</td>"
									. "<td>$date_str</td>"
									. "<td>" . strip_tags($my_control['description']). "</td>"
									. "<td>{$location_name}</td>"
							//		. "<td>{$control_area_name}</td>"
									. "";
							}
						}
						else if ($check_list_status == "edit")
						{
							$check_list_id	 = $my_assigned_control[3];
							$location_code	 = $my_assigned_control[4];

							$location_name = $this->get_location_name($location_code);

							$link	 = "";
							$link	 = $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'controller.uicheck_list.edit_check_list',
								'check_list_id'	 => $check_list_id));

							$my_assigned_controls_HTML .= ""
								. "<td><a href='$link'><div class='date'>{$date_str}</div></a></td>"
								. "<td>" . strip_tags($my_control['description']). "</td>"
								. "<td>{$location_name}</td>"
								. "<td>{$control_area_name}</td>";
						}

						$my_assigned_controls_HTML .= "</tr>";
					}
				}

				if (!$found_at_least_one)
				{
					$my_assigned_controls_HTML .= "<tr><td colspan=\"4\">No records found</td></tr>";
				}

				$my_assigned_controls_HTML .= "</tbody></table>"; // home_portal


				echo "<!-- BEGIN assigned controls info -->"
				. "<div class=\"container\">"
				. "		<div class=\"row mt-4\">\n"
				. "			\n <h2 class='heading'>Mine tildelte kontroller</h2>"
				. "			<div class='home-box'>" . $my_assigned_controls_HTML . "</div>\n"
				. "		</div>"
				. "</div>\n"
				. '<!-- END assigned controls info -->' . "\n";
			}

			$this->set_planned_month();
		}

		private function set_planned_month()
		{
			$html = <<<HTML
			<div id="dialog-set_planned_month" title="Sett planlagt måned">
				<p>Angi ønsket planlagt måned</p>
				<form id="form_set_planned_month">
						<div class="pure-control-group">
							<label>Måned</label>
							<select id="planned_month" name="planned_month" class="pure-input-1" required='required'>
								<option value="1">Januar</option>
								<option value="2">Februar</option>
								<option value="3">Mars</option>
								<option value="4">April</option>
								<option value="5">Mai</option>
								<option value="6">Juni</option>
								<option value="7">Juli</option>
								<option value="8">August</option>
								<option value="9">September</option>
								<option value="10">Oktober</option>
								<option value="11">November</option>
								<option value="12">Desember</option>
							</select>
						</div>
					<input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
					</input>
				</form>
			</div>
HTML;
			echo $html;

			$js = <<<JS
			<script>
				var global_args;
				var initial_selected_month;
				perform_action = function (name, oArgs, init_month)
				{
					if (name === 'set_planning_month')
					{
						global_args = oArgs;
						initial_selected_month = init_month;
						dialog2.dialog("open");

					}
				};

				dialog2 = $("#dialog-set_planned_month").dialog({
					autoOpen: false,
					height: 250,
					width: 350,
					modal: true,
					buttons: {
						"Ok": get_planned_month,
						Cancel: function ()
						{
							dialog2.dialog("close");
						}
					},
					close: function ()
					{
						$('#form_set_planned_month').trigger("reset");
						$("#planned_month").removeClass("ui-state-error");
					},
					open: function (event, ui)
					{
						var init_month = initial_selected_month;
						$("#planned_month").each(function ()
						{
							$(this).find('option[value="' + init_month + '"]').prop('selected', true);
						});
					}
				});

				form2 = dialog2.find("form").on("submit", function (event)
				{
					event.preventDefault();
					get_planned_month();
				});

				function get_planned_month()
				{
					var valid = true;
					$("#planned_month").removeClass("ui-state-error");
					planned_month = $("#planned_month").val();
					dialog2.dialog("close");
					submit_set_planned_month();
					return valid;
				}

				submit_set_planned_month = function ()
				{
					global_args.menuaction = 'controller.uicheck_list.save_check_list';
					var requestUrl = phpGWLink('index.php', global_args, true);
					$.ajax({
						type: 'POST',
						data: {planned_month: planned_month},
						dataType: 'json',
						url: requestUrl,
						success: function (data)
						{
							if (data !== null)
							{
								if(data.status !== 'ok')
								{
									alert(data.message);
								}
								else
								{
									location.reload();
								}
							}
						}
					});
				};
			</script>

JS;

			echo $js;

		}

		private function get_controls( $app_id )
		{
			if ($this->skip_portalbox_controls)
			{
				//			return array();
			}
			$var = array
				(
				'up'	 => array('url' => '/set_box.php', 'app' => $app_id),
				'down'	 => array('url' => '/set_box.php', 'app' => $app_id),
//				'close'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
//				'question'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
//				'edit'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id)
			);
			return $var;
		}

		/**
		 * Show info for homepage
		 *
		 * @return void
		 */
		public function undone_controls( $front_end = false )
		{
			$app_id = $GLOBALS['phpgw']->applications->name2id('controller');
			if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
			{
				$GLOBALS['portal_order'][] = $app_id;
			}

			$location_array				 = array();
			$component_short_desc		 = array();
			$component_short_desc[0][0]	 = '';

			$so_check_list	 = CreateObject('controller.socheck_list');
			$so_control		 = CreateObject('controller.socontrol');

			$config					 = CreateObject('phpgwapi.config', 'controller');
			$config->read();
			$limit_no_of_planned	 = isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls']) ? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_planned_controls'] : (isset($config->config_data['no_of_planned_controls']) && $config->config_data['no_of_planned_controls'] > 0 ? $config->config_data['no_of_planned_controls'] : 5);
			$limit_no_of_assigned	 = isset($GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_assigned_controls']) ? $GLOBALS['phpgw_info']['user']['preferences']['controller']['no_of_assigned_controls'] : (isset($config->config_data['no_of_assigned_controls']) && $config->config_data['no_of_assigned_controls'] > 0 ? $config->config_data['no_of_assigned_controls'] : 10);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$styling = "\n" . '<!-- BEGIN checklist info -->' . "\n ";
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
			$styling .= "\n" . '<!-- END checklist info -->' . "\n";
			echo $styling;

			//Loaded from home.php

			$script	 = "<script>";
			$script	 .= "$(document).ready(function(){";
			$script	 .= " $('.expand_trigger').on('click', function() {";
			$script	 .= " var liTag = $(this).closest('li'); ";
			$script	 .= " var expandList = $(liTag).find('.expand_list'); ";
			$script	 .= " if( !$(expandList).hasClass('active') ){ $(expandList).show(); $(expandList).addClass('active');  ";
			$script	 .= " $(liTag).find('img').attr('src', 'controller/images/arrow_down.png');} ";
			$script	 .= " else{ $(expandList).hide(); $(expandList).removeClass('active');  ";
			$script	 .= " $(liTag).find('img').attr('src', 'controller/images/arrow_right.png');} ";
			$script	 .= " return false; ";
			$script	 .= " })";
			$script	 .= " })";
			$script	 .= "</script>";
			echo $script;

			// Fetches my properties
			$criteria = array
				(
				'user_id'	 => $GLOBALS['phpgw_info']['user']['account_id'],
				'type_id'	 => 1, // Nivå i bygningsregisteret 1:eiendom
				'role_id'	 => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
				'allrows'	 => false
			);

			$location_finder = new location_finder();
			$my_properties	 = $location_finder->get_responsibilities($criteria);

			// Fetches my buildings
			$criteria = array
				(
				'user_id'	 => $GLOBALS['phpgw_info']['user']['account_id'],
				'type_id'	 => 2, // Nivå i bygningsregisteret 1:eiendom
				'role_id'	 => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
				'allrows'	 => false
			);

			$location_finder = new location_finder();
			$my_buildings	 = $location_finder->get_responsibilities($criteria);

			$my_locations = array_merge($my_properties, $my_buildings);

			/* =======================================  UNDONE ASSIGNED CONTROLS FOR CURRENT USER  ================================= */

			// from date is set to 3 months back in time
			$from_date_ts	 = mktime(0, 0, 0, date("n") - 3, date("j"), date("Y"));
			$to_date_ts		 = mktime(0, 0, 0, date("n"), date("j"), date("Y"));

			// fetch all repeat types
			$repeat_type = null;

			// Fetches controls current user is responsible for 3 months back in time
			$my_controls		 = $this->get_my_controls($my_locations, $from_date_ts, $to_date_ts, $repeat_type);
			$my_undone_controls	 = array();

			// Generates an array containing undone controls
			foreach ($my_controls as $container_arr)
			{
				$location_code	 = $container_arr[0];
				$control_type	 = $container_arr[1];
				$controls		 = $container_arr[2];

				foreach ($controls as $my_control)
				{
					if ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_DAY)
					{
						// DAILY CONTROLS: Fetch undone controls one week back in time
						$from_date_ts = mktime(0, 0, 0, date("n"), date("j") - 7, date("Y"));
					}
					else if ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_WEEK)
					{
						// WEEKLY CONTROLS: Fetch undone controls one month back in time
						$from_date_ts = mktime(0, 0, 0, date("n") - 1, date("j"), date("Y"));
					}
					else if ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_MONTH)
					{
						// MONTHLY CONTROLS: Fetch undone controls three months back in time
						$from_date_ts = mktime(0, 0, 0, date("n") - 3, date("j"), date("Y"));
					}
					else if ($my_control["repeat_type"] == controller_control::REPEAT_TYPE_YEAR)
					{
						// YEARLY CONTROLS: Fetch undone controls one year back in time
						$from_date_ts = mktime(0, 0, 0, date("n"), date("j"), date("Y") - 1);
					}

					$date_generator				 = new date_generator($my_control["start_date"], $my_control["end_date"], $from_date_ts, $to_date_ts, $my_control["repeat_type"], $my_control["repeat_interval"]);
					$deadline_dates_for_control	 = $date_generator->get_dates();

					$check_list_array = array();
					foreach ($deadline_dates_for_control as $deadline_ts)
					{
						$check_list = null;

						if ($control_type == "location")
						{
							$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, $location_code, null, null, "location");
						}
						else if ($control_type == "component")
						{
							$component = $container_arr[3];

							$check_list = $so_check_list->get_check_list_for_control_by_date($my_control['id'], $deadline_ts, null, null, $component['location_id'], $component['id'], "component");
						}
						$control_id = $my_control['id'];

						if ($check_list == null & $control_type == "location")
						{
							$my_undone_controls[$deadline_ts][] = array("add", $deadline_ts, $my_control,
								"location", $location_code);
						}
						else if ($check_list == null & $control_type == "component")
						{
							$component							 = $container_arr[3];
							$my_undone_controls[$deadline_ts][]	 = array("add", $deadline_ts, $my_control,
								"component", $component['location_id'], $component['id']);
						}
						else if (($check_list->get_status() == controller_check_list::STATUS_NOT_DONE) || ($check_list->get_status() == controller_check_list::STATUS_CANCELED))
						{
							$my_undone_controls[$deadline_ts][] = array("edit", $deadline_ts, $my_control,
								$check_list->get_id(), $location_code);
						}
					}
				}
			}

			//Add assigned
			$my_check_lists = $this->get_my_assigned_check_list($from_date_ts, $to_date_ts, $repeat_type, true);

			/* ??
			  $_assigned_list = array();
			  foreach ($my_check_lists as $_key => $my_check_list)
			  {
			  $_assigned_list[$my_check_list['location_code']][$_key] = $my_check_list;
			  }
			 */
			foreach ($my_check_lists as $_key => $my_check_list)
			{
				$my_undone_controls[$my_check_list['deadline']][] = array("edit", $my_check_list['deadline'],
					$my_check_list, $_key, $my_check_list['location_code']);
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

			$lang_planned_date = lang('planned date');
			foreach ($my_undone_controls as $date_ts => $controls_on_date)
			{
				// If number of controls on a date exceeds 1 it will be put in expand list
				if (count($controls_on_date) > 1)
				{
					$my_undone_controls_HTML .= "<li>";
					$my_undone_controls_HTML .= "<a href='#'><h4 class='expand_trigger'><img height='12' src='controller/images/arrow_right.png' /><span class='deadline'>" . date($dateformat, $date_ts) . "</span><span class='num_check_lists'>(" . count($controls_on_date) . " kontroller)</span></h4></a>";
					$my_undone_controls_HTML .= "<ul class='expand_list'>";
				}

				foreach ($controls_on_date as $my_undone_control)
				{
					$check_list_status	 = $my_undone_control[0];
					$deadline_ts		 = $my_undone_control[1];
					$my_control			 = $my_undone_control[2];
					$control_area_name	 = $this->get_control_area_name($my_control["control_area_id"]);

					$date_str = date($dateformat, $deadline_ts);
					if ($my_control['planned_date'])
					{
						$date_str_planned = $lang_planned_date . ': ' . date($dateformat, $my_control['planned_date']);
					}

					if ($check_list_status == "add")
					{
						$check_list_type = $my_undone_control[3];
						if ($check_list_type == "location")
						{
							$location_code = $my_undone_control[4];

							$location_name = $this->get_location_name($location_code);

							if (count($controls_on_date) > 1)
							{
								$link	 = "";
								$link	 = $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'controller.uicheck_list.add_check_list',
									'type'			 => "location", 'control_id'	 => $my_control['id'], 'location_code'	 => $location_code,
									'deadline_ts'	 => $deadline_ts));

								$my_undone_controls_HTML .= "<li><a href='{$link}' title = '{$date_str_planned}'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a></li>";
							}
							else
							{
								$link	 = "";
								$link	 = $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'controller.uicheck_list.add_check_list',
									'type'			 => "location", 'control_id'	 => $my_control['id'], 'location_code'	 => $location_code,
									'deadline_ts'	 => $deadline_ts));

								$my_undone_controls_HTML .= "<a href='{$link} title = '{$date_str_planned}''><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a>";
							}
						}
						else if ($check_list_type == "component")
						{
							$location_id	 = $my_undone_control[4];
							$component_id	 = $my_undone_control[5];

							$short_descr = $this->get_short_description($location_id, $component_id);
							if (count($controls_on_date) > 1)
							{
								$link	 = "";
								$link	 = $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'controller.uicheck_list.add_check_list',
									'type'			 => "component", 'control_id'	 => $my_control['id'], 'location_id'	 => $location_id,
									'component_id'	 => $component_id, 'deadline_ts'	 => $deadline_ts));

								$my_undone_controls_HTML .= "<li><a href='{$link}' title = '{$date_str_planned}'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$short_descr}</div><div class='control-area'>{$control_area_name}</div></a></li>";
							}
							else
							{
								$link	 = "";
								$link	 = $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'controller.uicheck_list.add_check_list',
									'type'			 => "component", 'control_id'	 => $my_control['id'], 'location_id'	 => $location_id,
									'component_id'	 => $component_id, 'deadline_ts'	 => $deadline_ts));

								$my_undone_controls_HTML .= "<a href='{$link}' title = '{$date_str_planned}'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$short_descr}</div><div class='control-area'>{$control_area_name}</div></a>";
							}
						}
					}
					else if ($check_list_status == "edit")
					{

						$location_id	 = isset($my_control['location_id']) && $my_control['location_id'] ? $my_control['location_id'] : 0;
						$component_id	 = isset($my_control['component_id']) && $my_control['component_id'] ? $my_control['component_id'] : 0;

						$check_list_id	 = $my_undone_control[3];
						$location_code	 = $my_undone_control[4];

						$location_name = $this->get_location_name($location_code);

						if ($component_id)
						{
							$short_descr = $this->get_short_description($location_id, $component_id);

							$location_name .= "::{$short_descr}";
						}

						if (count($controls_on_date) > 1)
						{
							$link	 = "";
							$link	 = $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'controller.uicheck_list.edit_check_list',
								'check_list_id'	 => $check_list_id));

							$my_undone_controls_HTML .= "<li><a href='{$link}' title = '{$date_str_planned}'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a></li>";
						}
						else
						{
							$link	 = "";
							$link	 = $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'controller.uicheck_list.edit_check_list',
								'check_list_id'	 => $check_list_id));

							$my_undone_controls_HTML .= "<li><a href='$link' title = '{$date_str_planned}'><div class='date'>{$date_str}</div><div class='control'>{$my_control['title']}</div><div class='title'>{$location_name}</div><div class='control-area'>{$control_area_name}</div></a></li>";
						}
					}
				}

				if (count($controls_on_date) > 1)
				{
					$my_undone_controls_HTML .= "</ul>";
					$my_undone_controls_HTML .= "</li>";
				}
			}

			$my_undone_controls_HTML .= "</ul>";

			$my_undone_controls_HTML .= "</div>"; // home_portal

			echo "\n" . '<!-- BEGIN checklist info -->' . "\n <h2 class='heading'>Mine ugjorte kontroller</h2><div class='home-box'>" . $my_undone_controls_HTML . "</div>\n" . '<!-- END checklist info -->' . "\n";
		}
		/* ================================  FUNCTIONS  ======================================== */

		function get_my_assigned_check_list( $from_date_ts, $to_date_ts, $repeat_type, $completed = null, $limit_no_of_planned = null )
		{
			$check_list_array = array();

			$so_control = CreateObject('controller.socontrol');

			$user_id = array($GLOBALS['phpgw_info']['user']['account_id']);

			if (!empty($GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from']))
			{
				$user_id[] = $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];
			}

			$assigned_check_list_at_location = $so_control->get_assigned_check_list_at_location($from_date_ts, $to_date_ts, $repeat_type, $user_id, $completed, 'return_array', $limit_no_of_planned);

			foreach ($assigned_check_list_at_location as $assigned_check_list)
			{
				$check_list_array[$assigned_check_list['id']] = $assigned_check_list;
			}
			unset($assigned_check_list);

			$assigned_check_list_at_component = $so_control->get_assigned_check_list_by_component($from_date_ts, $to_date_ts, $repeat_type, $user_id, $completed, 'return_array', $limit_no_of_planned);

			foreach ($assigned_check_list_at_component as $assigned_check_list)
			{
				$check_list_array[$assigned_check_list['id']] = $assigned_check_list;
			}

			return $check_list_array;
		}

		function get_my_controls_old( $my_locations, $from_date_ts, $to_date_ts, $repeat_type )
		{
			$so_control = CreateObject('controller.socontrol');

			$my_controls = array();

			foreach ($my_locations as $location)
			{
				$components_with_controls_array	 = array();
				$controls_at_location			 = array();
				$location_code					 = $location["location_code"];

				$controls_at_location = $so_control->get_controls_by_location($location_code, $from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"]);

				$level = count(explode('-', $location_code));

				if ($level == 1)
				{
					// Fetches all controls for the components for a location within time period
					$filter							 = "bim_item.location_code = '$location_code' ";
					$components_with_controls_array	 = $so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"], $filter);
				}
				else
				{
					// Fetches all controls for the components for a location within time period
					$filter							 = "bim_item.location_code LIKE '$location_code%' ";
					$components_with_controls_array	 = $so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_array", $location["role_id"], $filter);
				}

				if ($controls_at_location)
				{
					// Saves location code, location type and an array containing controls at locations
					$my_controls[] = array($location_code, 'location', $controls_at_location);
				}

				if ($components_with_controls_array)
				{
					foreach ($components_with_controls_array as $component)
					{
						// Saves location code, location type, an array containing controls at locations and component object
						$my_controls[] = array($location_code, 'component', $component['controls_array'], $component);
					}
				}
			}

			return $my_controls;
		}
		function get_my_controls( $my_locations, $from_date_ts, $to_date_ts, $repeat_type )
		{

			$role_ids = array();
			$user_ids = array($GLOBALS['phpgw_info']['user']['account_id']);

			$substitute_users	 = CreateObject('property.sosubstitute')->get_users_for_substitute($user_ids[0]);

			foreach ($substitute_users as $user_for_substitute)
			{
				$user_ids[] = $user_for_substitute;
			}

			/**
			 * naah
			 */
//			foreach ($user_ids as $user_id)
//			{
//				$role_ids[] = $GLOBALS['phpgw']->accounts->get($user_id)->person_id;
//			}
			$so_control = CreateObject('controller.socontrol');

			$my_controls = array();
			$location_codes = array();

			foreach ($my_locations as $location)
			{
				$location_codes[]	 = $location["location_code"];
			}

			$controls_at_location = $so_control->get_controls_by_location($location_codes, $from_date_ts, $to_date_ts, $repeat_type, "return_array", $role_ids);

			$components_with_controls_array	 = $so_control->get_controls_by_serie($to_date_ts, "return_array", $user_ids);

			if ($controls_at_location)
			{
				foreach ($controls_at_location as $control)
				{
					// Saves location code, location type and an array containing controls at locations
					$my_controls[] = array(
						$control['location_code'],
						'location',
						$controls_at_location
					);
				}
			}

			if ($components_with_controls_array)
			{
				foreach ($components_with_controls_array as $component)
				{
					// Saves location code, location type, an array containing controls at locations and component object
					$my_controls[] = array(
						$component['location_code'],
						'component',
						$component['controls_array'],
						$component
					);
				}
			}

			return $my_controls;
		}

		function get_control_area_name( $control_area_id )
		{
			$cats				 = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	 = true;

			$control_areas = $cats->formatted_xslt_list(array('format'	 => 'filter', 'selected'	 => '',
				'globals'	 => true, 'use_acl'	 => 0));

			foreach ($control_areas['cat_list'] as $area)
			{
				if ($area['cat_id'] == $control_area_id)
				{
					$control_area_name = $area['name'];
				}
			}

			return $control_area_name;
		}

		function get_location_name( $location_code )
		{
			static $location_array = array();
			if (!isset($location_array[$location_code]) || !$location_array[$location_code])
			{
				$_location_info = execMethod('property.bolocation.read_single', array
					(
					'location_code'	 => $location_code,
					'extra'			 => array('noattrib' => true)
					)
				);

				$_loc_name_arr = array();
				for ($i = 1; $i < count(explode('-', $location_code)) + 1; $i++)
				{
					$_loc_name_arr[] = $_location_info["loc{$i}_name"];
				}

				$location_array[$location_code] = implode(' | ', $_loc_name_arr);
			}

			return $location_array[$location_code];
		}

		function get_short_description( $location_id, $component_id )
		{
			static $component_short_desc = array();

			if (!isset($component_short_desc[$location_id][$component_id]))
			{
				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$item_arr											 = createObject('property.solocation')->read_single('', array('location_id'	 => $location_id,
						'id'			 => $component_id), true);
					$component_short_desc[$location_id][$component_id]	 = execMethod('property.bolocation.get_location_name', $item_arr['location_code']);
				}
				else
				{
					$component_short_desc[$location_id][$component_id] = execMethod('property.soentity.get_short_description', array(
						'location_id'	 => $location_id, 'id'			 => $component_id));
				}
			}

			return $component_short_desc[$location_id][$component_id];
		}
	}