<?php
	/**
	 * property - Hook helper
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package property
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


	/**
	 * Hook helper
	 *
	 * @package property
	 */
	class property_hook_helper
	{
		/**
		 * Clear ACL-based userlists
		 *
		 * @return void
		 */
		public function clear_userlist()
		{
			$cleared = ExecMethod('property.bocommon.reset_fm_cache_userlist');
			$message =lang('%1 userlists cleared from cache',$cleared);
			phpgwapi_cache::message_set($message, 'message');
		}

		/**
		 * Add a contact to a location
		 *
		 * @return void
		 */
		public function add_location_contact($data)
		{
			if(!isset($data['location_code']) || !$data['location_code'])
			{
				phpgwapi_cache::message_set("location_code not set", 'error');
				return false;
			}

			$value_set = array();
			$value_set['location_code'] = $data['location_code'];
			$value_set['contact_id'] = $data['contact_id'];
			$value_set['user_id'] = $GLOBALS['phpgw_info']['user']['account_id'];
			$value_set['entry_date'] = time();
			$value_set['modified_date'] = time();
			
			$cols = implode(',', array_keys($value_set));
			$values	= $GLOBALS['phpgw']->db->validate_insert(array_values($value_set));
			$sql = "INSERT INTO fm_location_contact ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);

			if($data['email'])
			{
				$pref = CreateObject('phpgwapi.preferences',  $data['account_id']);
				$pref->read();
				$pref->add('property','email', $data['email'],'user');
				$pref->save_repository();
			}

			$message =lang('user %1 added to %2',$data['account_lid'],$data['location_code']);
			phpgwapi_cache::message_set($message, 'message');
		}

		/**
		 * Show info for homepage - called from backend
		 *
		 * @return void
		 */
		public function home_backend()
		{
			$this->home_ticket();
			$this->home_project();
			$this->home_workorder();
		}
		/**
		 * Show info for homepage - called from mobilefrontend
		 *
		 * @return void
		 */
		public function home_mobilefrontend()
		{
			$this->home_ticket();
		}

		/**
		 * Show ticket info for homepage
		 *
		 * @return void
		 */
		public function home_ticket()
		{
			$accound_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$save_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'property';
			$maxmatches = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = 5;

			$prefs = $GLOBALS['phpgw_info']['user']['preferences'];


			if ( isset($prefs['property']['mainscreen_show_new_updated_tts'])
			&& $prefs['property']['mainscreen_show_new_updated_tts'] == 'yes')
			{

				$default_status 	= isset($prefs['property']['tts_status']) ? $prefs['property']['tts_status'] : '';
				$tts = CreateObject('property.sotts');
				$tickets = $tts->read(array('user_id' => $accound_id, 'status_id' => array($default_status, 'O'), 'new' => true));
				$total_records = $tts->total_records;

				$portalbox = CreateObject('phpgwapi.listbox', array
				(
					'title'		=> isset($prefs['property']['mainscreen_tts_title']) && $prefs['property']['mainscreen_tts_title']? "{$prefs['property']['mainscreen_tts_title']} ({$total_records})" : lang('Helpdesk') . " ({$total_records})",
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
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
				foreach ($tickets as $ticket)
				{
					if(!$ticket['subject'])
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
					}
					$portalbox->data[] = array
					(
						'text' => "{$location['loc1_name']} :: {$ticket['subject']}{$group}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view', 'id' => $ticket['id']))
					);
				}

				echo "\n".'<!-- BEGIN ticket info -->'."\n<div class='property_tickets' style='padding-left: 10px;'>".$portalbox->draw()."</div>\n".'<!-- END ticket info -->'."\n";

				unset($tts);
				unset($portalbox);
				unset($category_name);
				unset($default_status);
			}


			if ( isset($prefs['property']['mainscreen_show_new_updated_tts_2'])
			&& $prefs['property']['mainscreen_show_new_updated_tts_2'] == 'yes')
			{

				$default_status 	= isset($prefs['property']['tts_status_2']) ? $prefs['property']['tts_status_2'] : '';
				$tts = CreateObject('property.sotts');
				$tickets = $tts->read(array('user_id' => $accound_id, 'status_id' => $default_status));
				$total_records = $tts->total_records;

				$portalbox = CreateObject('phpgwapi.listbox', array
				(
					'title'		=> isset($prefs['property']['mainscreen_tts_title_2']) && $prefs['property']['mainscreen_tts_title_2']? "{$prefs['property']['mainscreen_tts_title_2']} ({$total_records})" : lang('Helpdesk') . " ({$total_records})",
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
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
				foreach ($tickets as $ticket)
				{
					if(!$ticket['subject'])
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
					}

					$portalbox->data[] = array
					(
						'text' => "{$location['loc1_name']} :: {$ticket['subject']}{$group}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view', 'id' => $ticket['id']))
					);
				}

				echo "\n".'<!-- BEGIN ticket info -->'."\n<div class='property_tickets' style='padding-left: 10px;'>".$portalbox->draw()."</div>\n".'<!-- END ticket info -->'."\n";

				unset($tts);
				unset($portalbox);
				unset($category_name);
				unset($default_status);
			}


			if ( isset($prefs['property']['mainscreen_show_new_updated_tts_3'])
			&& $prefs['property']['mainscreen_show_new_updated_tts_3'] == 'yes')
			{

				$default_status 	= isset($prefs['property']['tts_status_3']) ? $prefs['property']['tts_status_3'] : '';
				$tts = CreateObject('property.sotts');
				$tickets = $tts->read(array('user_id' => $accound_id, 'status_id' => $default_status));
				$total_records = $tts->total_records;

				$portalbox = CreateObject('phpgwapi.listbox', array
				(
					'title'		=> isset($prefs['property']['mainscreen_tts_title_3']) && $prefs['property']['mainscreen_tts_title_3']? "{$prefs['property']['mainscreen_tts_title_3']} ({$total_records})" : lang('Helpdesk') . " ({$total_records})",
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
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
				foreach ($tickets as $ticket)
				{
					if(!$ticket['subject'])
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
					}

					$portalbox->data[] = array
					(
						'text' => "{$location['loc1_name']} :: {$ticket['subject']}{$group}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view', 'id' => $ticket['id']))
					);
				}

				echo "\n".'<!-- BEGIN ticket info -->'."\n<div class='property_tickets' style='padding-left: 10px;'>".$portalbox->draw()."</div>\n".'<!-- END ticket info -->'."\n";

				unset($tts);
				unset($portalbox);
				unset($category_name);
				unset($default_status);
			}

			if ( isset($prefs['property']['mainscreen_show_new_updated_tts_4'])
			&& $prefs['property']['mainscreen_show_new_updated_tts_4'] == 'yes')
			{

				$default_status 	= isset($prefs['property']['tts_status_4']) ? $prefs['property']['tts_status_4'] : '';
				$tts = CreateObject('property.sotts');
				$tickets = $tts->read(array('user_id' => $accound_id, 'status_id' => $default_status));
				$total_records = $tts->total_records;

				$portalbox = CreateObject('phpgwapi.listbox', array
				(
					'title'		=> isset($prefs['property']['mainscreen_tts_title_4']) && $prefs['property']['mainscreen_tts_title_4']? "{$prefs['property']['mainscreen_tts_title_4']} ({$total_records})" : lang('Helpdesk') . " ({$total_records})",
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
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

				$status = array();
				$status['X'] = array
				(
					'name'			=> lang('closed'),
				);
				$status['O'] = array
				(
					'name'			=> lang('open'),
				);

				$custom_status	= execMethod('property.botts.get_custom_status');

				foreach($custom_status as $custom)
				{
					$status["C{$custom['id']}"] = array
					(
						'status'			=> $custom['name'],
					);
				}

				$category_name = array(); // caching

				$portalbox->data = array();
				foreach ($tickets as $ticket)
				{
					if(!$ticket['subject'])
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
					$portalbox->data[] = array
					(
						'text' => "{$location['loc1_name']} :: {$ticket['subject']} :: {$status[$ticket['status']]['name']}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view', 'id' => $ticket['id']))
					);
				}

				echo "\n".'<!-- BEGIN ticket info -->'."\n<div class='property_tickets' style='padding-left: 10px;'>".$portalbox->draw()."</div>\n".'<!-- END ticket info -->'."\n";

				unset($tts);
				unset($portalbox);
				unset($category_name);
				unset($default_status);
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $save_app;
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = $maxmatches;
		}

		/**
		 * Show project info for homepage
		 *
		 * @return void
		 */
		public function home_project()
		{
			$accound_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$save_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'property';
			$maxmatches = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = 5;

			$prefs = $GLOBALS['phpgw_info']['user']['preferences'];

			if ( isset($prefs['property']['mainscreen_project_1'])
			&& $prefs['property']['mainscreen_project_1'] == 'yes')
			{

				$default_status 	= isset($prefs['property']['project_status_mainscreen_1']) ? $prefs['property']['project_status_mainscreen_1'] : '';
				$obj = CreateObject('property.soproject');
				$projects = $obj->read(array('filter' => $accound_id, 'status_id' => $default_status));
				$total_records = $obj->total_records;

				$portalbox = CreateObject('phpgwapi.listbox', array
				(
					'title'	=> isset($prefs['property']['mainscreen_projects_1_title']) && $prefs['property']['mainscreen_projects_1_title']? "{$prefs['property']['mainscreen_projects_1_title']} ({$total_records})" : lang('project') . '::' . lang('list') . ' ' . 1 . "::Status: {$default_status} ({$total_records})",
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
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

				$portalbox->data = array();
				foreach ($projects as $project)
				{
					$portalbox->data[] = array
					(
						'text' => "{$project['address']} :: {$project['name']}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiproject.edit', 'id' => $project['project_id']))
					);
				}

				echo "\n".'<!-- BEGIN project 1 info -->'."\n<div class='property_project' style='padding-left: 10px;'>".$portalbox->draw()."</div>\n".'<!-- END project 1 info -->'."\n";

				unset($obj);
				unset($portalbox);
				unset($default_status);
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $save_app;
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = $maxmatches;
		}

		/**
		 * Show workorder info for homepage
		 *
		 * @return void
		 */
		public function home_workorder()
		{
			$accound_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$save_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'property';
			$maxmatches = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = 5;

			$prefs = $GLOBALS['phpgw_info']['user']['preferences'];

			if ( isset($prefs['property']['mainscreen_workorder_1'])
			&& $prefs['property']['mainscreen_workorder_1'] == 'yes')
			{

				$default_status 	= isset($prefs['property']['workorder_status_mainscreen_1']) ? $prefs['property']['workorder_status_mainscreen_1'] : '';
				$obj = CreateObject('property.soworkorder');
				$workorders = $obj->read(array('filter' => $accound_id, 'status_id' => $default_status));
				$total_records = $obj->total_records;

				$portalbox = CreateObject('phpgwapi.listbox', array
				(
					'title'	=> isset($prefs['property']['mainscreen_workorders_1_title']) && $prefs['property']['mainscreen_workorders_1_title']? "{$prefs['property']['mainscreen_workorders_1_title']} ({$total_records})" : lang('workorder') . '::' . lang('list') . ' ' . 1 . "::Status: {$default_status} ({$total_records})",
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
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

				$portalbox->data = array();
				foreach ($workorders as $workorder)
				{
					$portalbox->data[] = array
					(
						'text' => "{$workorder['address']} :: {$workorder['title']}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.edit', 'id' => $workorder['workorder_id']))
					);
				}

				echo "\n".'<!-- BEGIN workorder 1 info -->'."\n<div class='property_workorder' style='padding-left: 10px;'>".$portalbox->draw()."</div>\n".'<!-- END workorder 1 info -->'."\n";

				unset($obj);
				unset($portalbox);
				unset($default_status);
			}
			if ( isset($prefs['property']['mainscreen_workorder_2'])
			&& $prefs['property']['mainscreen_workorder_2'] == 'yes')
			{

				$default_status 	= isset($prefs['property']['workorder_status_mainscreen_2']) ? $prefs['property']['workorder_status_mainscreen_2'] : '';
				$obj = CreateObject('property.soworkorder');
				$workorders = $obj->read(array('filter' => $accound_id, 'status_id' => $default_status));
				$total_records = $obj->total_records;

				$portalbox = CreateObject('phpgwapi.listbox', array
				(
					'title'	=> isset($prefs['property']['mainscreen_workorders_2_title']) && $prefs['property']['mainscreen_workorders_2_title']? "{$prefs['property']['mainscreen_workorders_2_title']} ({$total_records})" : lang('workorder') . '::' . lang('list') . ' ' . 2 . "::Status: {$default_status} ({$total_records})",
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
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

				$portalbox->data = array();
				foreach ($workorders as $workorder)
				{
					$portalbox->data[] = array
					(
						'text' => "{$workorder['address']} :: {$workorder['title']}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.edit', 'id' => $workorder['workorder_id']))
					);
				}

				echo "\n".'<!-- BEGIN workorder 2 info -->'."\n<div class='property_workorder' style='padding-left: 10px;'>".$portalbox->draw()."</div>\n".'<!-- END workorder 2 info -->'."\n";

				unset($obj);
				unset($portalbox);
				unset($default_status);
			}

			if ( isset($prefs['property']['mainscreen_showapprovals_request'])
			&& $prefs['property']['mainscreen_showapprovals_request'] == 'yes' )
			{
				$total_records = 0;
				$title = isset($prefs['property']['mainscreen_showapprovals_request_title']) && $prefs['property']['mainscreen_showapprovals_request_title']? "{$prefs['property']['mainscreen_showapprovals_request_title']} ({$total_records})" : lang('approvals request') . " ({$total_records})";

				//TODO Make listbox css compliant
				$portalbox = CreateObject('phpgwapi.listbox', array
				(
					'title'	=> $title,
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');

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

				$action_params = array
				(
					'appname'			=> 'property',
					'location'			=> '.project',
					//	'id'				=> $id,
					'responsible'		=> '',
					'responsible_type'  => 'user',
					'action'			=> 'approval',
					'deadline'			=> '',
					'created_by'		=> $accound_id,
					'allrows'			=> true
				);

				$obj = CreateObject('property.sopending_action');
				$pending_approvals = $obj->get_pending_action($action_params);
				$total_records = $obj->total_records;

				$portalbox->data = array();
				foreach ($pending_approvals as $entry)
				{
					$responsible = $entry['responsible'] ? $GLOBALS['phpgw']->accounts->get($entry['responsible'])->__toString() : '';
					$portalbox->data[] = array
					(
						'text' => "{$responsible}: Prosjekt venter på godkjenning: {$entry['item_id']}",
						'link' => $entry['url']
					);
				}
				$action_params = array
				(
					'appname'			=> 'property',
					'location'			=> '.project.workorder',
					//	'id'				=> $id,
					'responsible'		=> '',
					'responsible_type'  => 'user',
					'action'			=> 'approval',
					'deadline'			=> '',
					'created_by'		=> $accound_id,
					'allrows'			=> true
				);

				$pending_approvals = $obj->get_pending_action($action_params);
				$total_records = $total_records + $obj->total_records;

				foreach ($pending_approvals as $entry)
				{
					$responsible = $entry['responsible'] ? $GLOBALS['phpgw']->accounts->get($entry['responsible'])->__toString() : '';
					$portalbox->data[] = array
					(
						'text' => "{$responsible}: Ordre venter på godkjenning: {$entry['item_id']}",
						'link' => $entry['url']
					);
				}
				$action_params = array
				(
					'appname'			=> 'property',
					'location'			=> '.ticket',
					//	'id'				=> $id,
					'responsible'		=> '',
					'responsible_type'  => 'user',
					'action'			=> 'approval',
					'deadline'			=> '',
					'created_by'		=> $accound_id,
					'allrows'			=> true
				);

				$pending_approvals = $obj->get_pending_action($action_params);
				$total_records = $total_records + $obj->total_records;

				foreach ($pending_approvals as $entry)
				{
					$responsible = $entry['responsible'] ? $GLOBALS['phpgw']->accounts->get($entry['responsible'])->__toString() : '';
					$portalbox->data[] = array
					(
						'text' => "{$responsible}: Melding venter på godkjenning: {$entry['item_id']}",
						'link' => $entry['url']
					);
				}

				echo "\n".'<!-- BEGIN approval info -->'."\n<div class='property_approval' style='padding-left: 10px;'>".$portalbox->draw()."</div>\n".'<!-- END approval info -->'."\n";

				unset($portalbox);
				unset($obj);
				unset($pending_approvals);
			}

			if ( isset($prefs['property']['mainscreen_showapprovals'])
			&& $prefs['property']['mainscreen_showapprovals'] == 'yes' )
			{
				$total_records = 0;
				$title = 'dummy';	
				//TODO Make listbox css compliant
				$portalbox = CreateObject('phpgwapi.listbox', array
				(
					'title'	=> $title,
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');

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

				$action_params = array
				(
					'appname'			=> 'property',
					'location'			=> '.project',
					//	'id'				=> $id,
					'responsible'		=> $accound_id,
					'responsible_type'  => 'user',
					'action'			=> 'approval',
					'deadline'			=> '',
					'created_by'		=> '',
					'allrows'			=> true
				);

				$pending_approvals = execMethod('property.sopending_action.get_pending_action', $action_params);

				$portalbox->data = array();
				foreach ($pending_approvals as $entry)
				{
					$portalbox->data[] = array
					(
						'text' => 'Prosjekt venter på godkjenning: ' . $entry['item_id'],
						'link' => $entry['url']
					);
					$total_records++;
				}

				//		echo "\n".'<!-- BEGIN approval info -->'."\n".$portalbox->draw()."\n".'<!-- END approval info -->'."\n";

				$action_params = array
				(
					'appname'			=> 'property',
					'location'			=> '.project.workorder',
					//	'id'				=> $id,
					'responsible'		=> $accound_id,
					'responsible_type'  => 'user',
					'action'			=> 'approval',
					'deadline'			=> '',
					'created_by'		=> '',
					'allrows'			=> true
				);

				$pending_approvals = execMethod('property.sopending_action.get_pending_action', $action_params);

				//		$portalbox->data = array();
				foreach ($pending_approvals as $entry)
				{
					$portalbox->data[] = array
					(
						'text' => 'Ordre venter på godkjenning: ' . $entry['item_id'],
						'link' => $entry['url']
					);
					$total_records++;
				}

				$action_params = array
				(
					'appname'			=> 'property',
					'location'			=> '.ticket',
					//	'id'				=> $id,
					'responsible'		=> $accound_id,
					'responsible_type'  => 'user',
					'action'			=> 'approval',
					'deadline'			=> '',
					'created_by'		=> '',
					'allrows'			=> true
				);

				$pending_approvals = execMethod('property.sopending_action.get_pending_action', $action_params);

				//		$portalbox->data = array();
				foreach ($pending_approvals as $entry)
				{
					$portalbox->data[] = array
					(
						'text' => 'Melding venter på godkjenning: ' . $entry['item_id'],
						'link' => $entry['url']
					);
					$total_records++;
				}
				//Hack
				$title = isset($prefs['property']['mainscreen_showapprovals_title']) && $prefs['property']['mainscreen_showapprovals_title']? "{$prefs['property']['mainscreen_showapprovals_title']} ({$total_records})" : lang('approvals') . " ({$total_records})";	
				$portalbox->setvar('title', $title);
				$portalbox->start_template();

				echo "\n".'<!-- BEGIN approval info -->'."\n<div class='property_approval' style='padding-left: 10px;'>".$portalbox->draw()."</div>\n".'<!-- END approval info -->'."\n";

				unset($portalbox);
				unset($pending_approvals);
			}

			if ( isset($prefs['property']['mainscreen_showvendor_reminder'])
			&& $prefs['property']['mainscreen_showvendor_reminder']  == 'yes' )
			{
				$total_records = 0;
				$title = 'dummy';
				//TODO Make listbox css compliant
				$portalbox = CreateObject('phpgwapi.listbox', array
				(
					'title'	=> $title,
					'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'	=> '100%',
					'outerborderwidth'	=> '0',
					'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
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

				$action_params = array
				(
					'appname'			=> 'property',
					'location'			=> '.project.workorder',
					//	'id'				=> $id,
					'responsible'		=> '',
					'responsible_type'  => 'vendor',
					'action'			=> 'remind',
					'deadline'			=> '',
					'created_by'		=> $accound_id,
					'allrows'			=> true
				);

				$pending_reminder = execMethod('property.sopending_action.get_pending_action', $action_params);

				$portalbox->data = array();
				foreach ($pending_reminder as $entry)
				{
					$sql='SELECT org_name FROM fm_vendor where id=' . (int)$entry['responsible'];
					$GLOBALS['phpgw']->db;
					$GLOBALS['phpgw']->db->query($sql);
					$GLOBALS['phpgw']->db->next_record();
					$vendor_name =  $GLOBALS['phpgw']->db->f('org_name',true);

					$portalbox->data[] = array
					(
						'text' => "påminning nr {$entry['reminder']} til leverandør {$vendor_name}- ordre nr: {$entry['item_id']}",
						'link' => $entry['url']
					);
					$total_records++;
				}

				$title = isset($prefs['property']['mainscreen_showvendor_reminder_title']) && $prefs['property']['mainscreen_showvendor_reminder_title']? "{$prefs['property']['mainscreen_showvendor_reminder_title']} ({$total_records})" : lang('vendor reminder') . " ({$total_records})";	
				$portalbox->setvar('title', $title);
				$portalbox->start_template();

				echo "\n".'<!-- BEGIN reminder info -->'."\n<div class='property_reminder' style='padding-left: 10px;'>".$portalbox->draw()."</div>\n".'<!-- END reminder info -->'."\n";

				unset($pending_reminder);
				unset($portalbox);
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $save_app;
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = $maxmatches;

		}
	}
