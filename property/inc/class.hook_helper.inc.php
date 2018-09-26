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

	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Hook helper
	 *
	 * @package property
	 */
	class property_hook_helper
	{

		private $skip_portalbox_controls;

		/**
		 * Clear ACL-based userlists
		 *
		 * @return void
		 */
		public function clear_userlist()
		{
			$cleared = ExecMethod('property.bocommon.reset_fm_cache_userlist');
			$message = lang('%1 userlists cleared from cache', $cleared);
			phpgwapi_cache::message_set($message, 'message');
		}

		/**
		 * Add a contact to a location
		 *
		 * @return void
		 */
		public function add_location_contact( $data )
		{
			if (!isset($data['location_code']) || !$data['location_code'])
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
			$values = $GLOBALS['phpgw']->db->validate_insert(array_values($value_set));
			$sql = "INSERT INTO fm_location_contact ({$cols}) VALUES ({$values})";
			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);

			if ($data['email'])
			{
				$pref = CreateObject('phpgwapi.preferences', $data['account_id']);
				$pref->read();
				$pref->add('property', 'email', $data['email'], 'user');
				$pref->save_repository();
			}

			$message = lang('user %1 added to %2', $data['account_lid'], $data['location_code']);
			phpgwapi_cache::message_set($message, 'message');
		}

		/**
		 * Show info for homepage - called from backend
		 *
		 * @return void
		 */
		public function home_backend()
		{
			$this->home_workorder_overdue_tender();
			$this->home_project_overdue_end_date();
			$this->home_tenant_claims();
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
			$this->skip_portalbox_controls = true;
			$this->home_ticket();
		}

		private function get_controls( $app_id )
		{
			if ($this->skip_portalbox_controls)
			{
				return array();
			}
			$var = array
				(
				'up' => array('url' => '/set_box.php', 'app' => $app_id),
				'down' => array('url' => '/set_box.php', 'app' => $app_id),
//				'close'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
//				'question'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
//				'edit'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id)
			);
			return $var;
		}

		/**
		 * Show project that is overdue
		 *
		 * @return void
		 */
		public function home_workorder_overdue_tender()
		{
			$accound_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$save_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'property';
			$maxmatches = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = 5;

			$prefs = $GLOBALS['phpgw_info']['user']['preferences'];

			if (isset($prefs['property']['mainscreen_show_project_overdue']) && $prefs['property']['mainscreen_show_project_overdue'] == 'yes')
			{
				$soworkorder = CreateObject('property.soworkorder');

				$values = $soworkorder->read(array(
					'filter' => $accound_id,
					'tender_deadline' => time()
				));

				$total_records = $soworkorder->total_records;
				$portalbox = CreateObject('phpgwapi.listbox', array
					(
					'title' => lang('tender delay') . " ({$total_records})",
					'primary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width' => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
				if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
				{
					$GLOBALS['portal_order'][] = $app_id;
				}

				$var = $this->get_controls($app_id);

				foreach ($var as $key => $value)
				{
					//				$portalbox->set_controls($key,$value);
				}
				foreach ($values as $entry)
				{
					$entry['tender_delay'] = ceil(phpgwapi_datetime::get_working_days($entry['tender_deadline'], time()));
					$portalbox->data[] = array
						(
						'text' => "Forsinkelse: {$entry['tender_delay']} dager :: bestilling nr:{$entry['workorder_id']} :: {$entry['location_code']} :: {$entry['address']}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.edit',
							'id' => $entry['workorder_id'], 'tab' => 'budget'))
					);
				}
				echo "\n" . '<!-- BEGIN ticket info -->' . "\n<div class='property_tickets' style='padding-left: 10px;'>" . $portalbox->draw() . "</div>\n" . '<!-- END ticket info -->' . "\n";

				unset($tts);
				unset($portalbox);
				unset($category_name);
				unset($default_status);
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $save_app;
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = $maxmatches;
		}

		/**
		 * Show project that is overdue
		 *
		 * @return void
		 */
		public function home_project_overdue_end_date()
		{
			$accound_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$save_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'property';
			$maxmatches = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = 5;

			$prefs = $GLOBALS['phpgw_info']['user']['preferences'];

			if (isset($prefs['property']['mainscreen_show_project_overdue']) && $prefs['property']['mainscreen_show_project_overdue'] == 'yes')
			{
				$soproject = CreateObject('property.soproject');

				$values = $soproject->read(array(
					'filter' => $accound_id,
					'overdue' => time(),
					'sort'	=>  'ASC',
					'order' =>  'end_date'

				));
				$total_records = $soproject->total_records;
				$portalbox = CreateObject('phpgwapi.listbox', array
					(
					'title' => lang('end date delay') . " ({$total_records})",
					'primary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width' => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
				if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
				{
					$GLOBALS['portal_order'][] = $app_id;
				}

				$var = $this->get_controls($app_id);

				foreach ($var as $key => $value)
				{
					//				$portalbox->set_controls($key,$value);
				}
				foreach ($values as $entry)
				{
					$entry['delay'] = ceil(phpgwapi_datetime::get_working_days($entry['end_date'], time()));
					$portalbox->data[] = array
						(
						'text' => "Forsinkelse: {$entry['delay']} dager :: prosjekt nr:{$entry['project_id']} :: {$entry['location_code']} :: {$entry['address']}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiproject.edit',
							'id' => $entry['project_id'], 'tab' => 'budget'))
					);
				}
				echo "\n" . '<!-- BEGIN ticket info -->' . "\n<div class='property_tickets' style='padding-left: 10px;'>" . $portalbox->draw() . "</div>\n" . '<!-- END ticket info -->' . "\n";

				unset($tts);
				unset($portalbox);
				unset($category_name);
				unset($default_status);
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $save_app;
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = $maxmatches;
		}

		/**
		 * Show tenant claims on homepage
		 *
		 * @return void
		 */
		public function home_tenant_claims()
		{
			$accound_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$save_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'property';
			$maxmatches = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = 5;

			$prefs = $GLOBALS['phpgw_info']['user']['preferences'];

			if (isset($prefs['property']['mainscreen_show_open_tenant_claim']) && $prefs['property']['mainscreen_show_open_tenant_claim'] == 'yes')
			{
				$sotenant_claim = CreateObject('property.sotenant_claim');
				$claims = $sotenant_claim->read(array
					(
					'start' => 0,
					'user_id' => $accound_id
					)
				);

				$total_records = $sotenant_claim->total_records;
				$portalbox = CreateObject('phpgwapi.listbox', array
					(
					'title' => lang('tenant claim') . " ({$total_records})",
					'primary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width' => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
				if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
				{
					$GLOBALS['portal_order'][] = $app_id;
				}

				$var = $this->get_controls($app_id);

				foreach ($var as $key => $value)
				{
					//				$portalbox->set_controls($key,$value);
				}
				foreach ($claims as &$entry)
				{
					$entry['entry_date'] = $GLOBALS['phpgw']->common->show_date($entry['entry_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					$location_info = execMethod('property.solocation.read_single', $entry['location_code']);
					$entry['loc1_name'] = $location_info['loc1_name'];
					$entry['loc_category'] = $location_info['category_name'];

					$portalbox->data[] = array
						(
						'text' => "{$entry['claim_id']} :: {$entry['location_code']} :: {$location_info['loc1_name']} :: {$location_info['category_name']} :: {$entry['name']}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitenant_claim.edit',
							'claim_id' => $entry['claim_id']))
					);
				}
				echo "\n" . '<!-- BEGIN ticket info -->' . "\n<div class='property_tickets' style='padding-left: 10px;'>" . $portalbox->draw() . "</div>\n" . '<!-- END ticket info -->' . "\n";

				unset($tts);
				unset($portalbox);
				unset($category_name);
				unset($default_status);
			}
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $save_app;
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = $maxmatches;
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


			if (isset($prefs['property']['mainscreen_show_new_updated_tts']) && $prefs['property']['mainscreen_show_new_updated_tts'] == 'yes')
			{

				$portalbox = CreateObject('phpgwapi.listbox', array
					(
					'title' => isset($prefs['property']['mainscreen_tts_title']) && $prefs['property']['mainscreen_tts_title'] ? "{$prefs['property']['mainscreen_tts_title']}" : lang('Helpdesk'),
					'primary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width' => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
				if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
				{
					$GLOBALS['portal_order'][] = $app_id;
				}

				$var = $this->get_controls($app_id);

				foreach ($var as $key => $value)
				{
					$portalbox->set_controls($key, $value);
				}

				$portalbox->data = array();

				echo "\n" . '<!-- BEGIN ticket info -->' . "\n<div class='property_tickets' style='padding-left: 10px;'>" . $portalbox->draw() . "</div>\n" . '<!-- END ticket info -->' . "\n";


				echo '<div id="ticket_info_container"></div>';

				$lang = js_lang( 'Name', 'address', 'status','subject', 'id', 'assigned to', 'modified date');


				$default_status = array();

				if(!empty($prefs['property']['tts_status']))
				{
					$default_status[] = $prefs['property']['tts_status'];
				}
				if(!empty($prefs['property']['tts_status_2']))
				{
					$default_status[] = $prefs['property']['tts_status_2'];
				}
				if(!empty($prefs['property']['tts_status_3']))
				{
					$default_status[] = $prefs['property']['tts_status_3'];
				}
				if(!empty($prefs['property']['tts_status_4']))
				{
					$default_status[] = $prefs['property']['tts_status_4'];
				}

				$status_filter = '';

				if(!$default_status)
				{
					$status_filter .= "&status_id=O"; //all variants of Open
				}
				else
				{
					foreach ($default_status as $_default_status)
					{
						$status_filter .= "&status_id[]={$_default_status}";
					}
				}

				$js = <<<JS
					<script type="text/javascript">
					var building_id = 1;
					var lang = $lang;
					var ticket_infoURL = phpGWLink('index.php', {
						menuaction:'property.uitts.query2',
						order:'id',
						sort:'asc',
						user_id:{$accound_id}
						}, true);

						ticket_infoURL += '{$status_filter}';
					var rTicket_info = [{n: 'ResultSet'},{n: 'Result'}];
		//			var rTicket_info = 'data';

					var colDefsTicket_info = [
						{key: 'id', label: lang['id'], formatter: genericLink},
						{key: 'subject', label: lang['subject']},
						{key: 'status', label: lang['status']},
						{key: 'address', label: lang['address']},
						{key: 'assignedto', label: lang['assigned to']},
						{key: 'modified_date', label: lang['modified date']}

						];

					var paginatorTableTicket_info = new Array();
					paginatorTableTicket_info.limit = 10;
					createPaginatorTable('ticket_info_container', paginatorTableTicket_info);

					createTable('ticket_info_container', ticket_infoURL, colDefsTicket_info, rTicket_info, '', paginatorTableTicket_info);

					</script>

JS;

				echo $js;

				unset($tts);
				unset($portalbox);
				unset($category_name);
				unset($default_status);
				unset($_default_status);
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

			if (isset($prefs['property']['mainscreen_project_1']) && $prefs['property']['mainscreen_project_1'] == 'yes')
			{

				$default_status = isset($prefs['property']['project_status_mainscreen_1']) ? $prefs['property']['project_status_mainscreen_1'] : '';
				$obj = CreateObject('property.soproject');
				$projects = $obj->read(array('filter' => $accound_id, 'status_id' => $default_status));
				$total_records = $obj->total_records;

				$portalbox = CreateObject('phpgwapi.listbox', array
					(
					'title' => isset($prefs['property']['mainscreen_projects_1_title']) && $prefs['property']['mainscreen_projects_1_title'] ? "{$prefs['property']['mainscreen_projects_1_title']} ({$total_records})" : lang('project') . '::' . lang('list') . ' ' . 1 . "::Status: {$default_status} ({$total_records})",
					'primary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width' => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
				if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
				{
					$GLOBALS['portal_order'][] = $app_id;
				}

				$var = $this->get_controls($app_id);

				foreach ($var as $key => $value)
				{
					//			$portalbox->set_controls($key,$value);
				}

				$portalbox->data = array();
				foreach ($projects as $project)
				{
					$portalbox->data[] = array
						(
						'text' => "{$project['address']} :: {$project['name']}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiproject.edit',
							'id' => $project['project_id']))
					);
				}

				echo "\n" . '<!-- BEGIN project 1 info -->' . "\n<div class='property_project' style='padding-left: 10px;'>" . $portalbox->draw() . "</div>\n" . '<!-- END project 1 info -->' . "\n";

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

			if (isset($prefs['property']['mainscreen_workorder_1']) && $prefs['property']['mainscreen_workorder_1'] == 'yes')
			{

				$default_status = isset($prefs['property']['workorder_status_mainscreen_1']) ? $prefs['property']['workorder_status_mainscreen_1'] : '';
				$obj = CreateObject('property.soworkorder');
				$workorders = $obj->read(array('filter' => $accound_id, 'status_id' => $default_status));
				$total_records = $obj->total_records;

				$portalbox = CreateObject('phpgwapi.listbox', array
					(
					'title' => isset($prefs['property']['mainscreen_workorders_1_title']) && $prefs['property']['mainscreen_workorders_1_title'] ? "{$prefs['property']['mainscreen_workorders_1_title']} ({$total_records})" : lang('workorder') . '::' . lang('list') . ' ' . 1 . "::Status: {$default_status} ({$total_records})",
					'primary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width' => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
				if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
				{
					$GLOBALS['portal_order'][] = $app_id;
				}

				$var = $this->get_controls($app_id);

				foreach ($var as $key => $value)
				{
					//			$portalbox->set_controls($key,$value);
				}

				$portalbox->data = array();
				foreach ($workorders as $workorder)
				{
					$portalbox->data[] = array
						(
						'text' => "{$workorder['address']} :: {$workorder['title']}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.edit',
							'id' => $workorder['workorder_id']))
					);
				}

				echo "\n" . '<!-- BEGIN workorder 1 info -->' . "\n<div class='property_workorder' style='padding-left: 10px;'>" . $portalbox->draw() . "</div>\n" . '<!-- END workorder 1 info -->' . "\n";

				unset($obj);
				unset($portalbox);
				unset($default_status);
			}
			if (isset($prefs['property']['mainscreen_workorder_2']) && $prefs['property']['mainscreen_workorder_2'] == 'yes')
			{

				$default_status = isset($prefs['property']['workorder_status_mainscreen_2']) ? $prefs['property']['workorder_status_mainscreen_2'] : '';
				$obj = CreateObject('property.soworkorder');
				$workorders = $obj->read(array('filter' => $accound_id, 'status_id' => $default_status));
				$total_records = $obj->total_records;

				$portalbox = CreateObject('phpgwapi.listbox', array
					(
					'title' => isset($prefs['property']['mainscreen_workorders_2_title']) && $prefs['property']['mainscreen_workorders_2_title'] ? "{$prefs['property']['mainscreen_workorders_2_title']} ({$total_records})" : lang('workorder') . '::' . lang('list') . ' ' . 2 . "::Status: {$default_status} ({$total_records})",
					'primary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width' => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
				if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
				{
					$GLOBALS['portal_order'][] = $app_id;
				}

				$var = $this->get_controls($app_id);

				foreach ($var as $key => $value)
				{
					//			$portalbox->set_controls($key,$value);
				}

				$portalbox->data = array();
				foreach ($workorders as $workorder)
				{
					$portalbox->data[] = array
						(
						'text' => "{$workorder['address']} :: {$workorder['title']}",
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.edit',
							'id' => $workorder['workorder_id']))
					);
				}

				echo "\n" . '<!-- BEGIN workorder 2 info -->' . "\n<div class='property_workorder' style='padding-left: 10px;'>" . $portalbox->draw() . "</div>\n" . '<!-- END workorder 2 info -->' . "\n";

				unset($obj);
				unset($portalbox);
				unset($default_status);
			}

			if (isset($prefs['property']['mainscreen_showapprovals_request']) && $prefs['property']['mainscreen_showapprovals_request'] == 'yes')
			{
				$total_records = 0;
				$title = isset($prefs['property']['mainscreen_showapprovals_request_title']) && $prefs['property']['mainscreen_showapprovals_request_title'] ? "{$prefs['property']['mainscreen_showapprovals_request_title']} ({$total_records})" : lang('approvals request') . " ({$total_records})";

				//TODO Make listbox css compliant
				$portalbox = CreateObject('phpgwapi.listbox', array
					(
					'title' => $title,
					'primary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width' => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');

				if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
				{
					$GLOBALS['portal_order'][] = $app_id;
				}

				$var = $this->get_controls($app_id);

				foreach ($var as $key => $value)
				{
					//			$portalbox->set_controls($key,$value);
				}

				$action_params = array
					(
					'appname' => 'property',
					'location' => '.project',
					//	'id'				=> $id,
					'responsible' => '',
					'responsible_type' => 'user',
					'action' => 'approval',
					'deadline' => '',
					'created_by' => $accound_id,
					'allrows' => true
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
					'appname' => 'property',
					'location' => '.project.workorder',
					//	'id'				=> $id,
					'responsible' => '',
					'responsible_type' => 'user',
					'action' => 'approval',
					'deadline' => '',
					'created_by' => $accound_id,
					'allrows' => true
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
					'appname' => 'property',
					'location' => '.ticket',
					//	'id'				=> $id,
					'responsible' => '',
					'responsible_type' => 'user',
					'action' => 'approval',
					'deadline' => '',
					'created_by' => $accound_id,
					'allrows' => true
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

				echo "\n" . '<!-- BEGIN approval info -->' . "\n<div class='property_approval' style='padding-left: 10px;'>" . $portalbox->draw() . "</div>\n" . '<!-- END approval info -->' . "\n";

				unset($portalbox);
				unset($obj);
				unset($pending_approvals);
			}

			if (isset($prefs['property']['mainscreen_showapprovals']) && $prefs['property']['mainscreen_showapprovals'] == 'yes')
			{
				$total_records = 0;
				$title = 'dummy';
				//TODO Make listbox css compliant
				$portalbox = CreateObject('phpgwapi.listbox', array
					(
					'title' => $title,
					'primary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width' => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');

				if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
				{
					$GLOBALS['portal_order'][] = $app_id;
				}

				$var = $this->get_controls($app_id);

				foreach ($var as $key => $value)
				{
					//			$portalbox->set_controls($key,$value);
				}

				$users_for_substitute = CreateObject('property.sosubstitute')->get_users_for_substitute( $accound_id);
				$users_for_substitute[] = $accound_id;


				$action_params = array
					(
					'appname' => 'property',
					'location' => '.project',
					'responsible' => $users_for_substitute,
					'responsible_type' => 'user',
					'action' => 'approval',
					'deadline' => '',
					'created_by' => '',
					'allrows' => true
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
					'appname' => 'property',
					'location' => '.project.workorder',
					//	'id'				=> $id,
					'responsible' => $accound_id,
					'responsible_type' => 'user',
					'action' => 'approval',
					'deadline' => '',
					'created_by' => '',
					'allrows' => true
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
					'appname' => 'property',
					'location' => '.ticket',
					//	'id'				=> $id,
					'responsible' => $accound_id,
					'responsible_type' => 'user',
					'action' => 'approval',
					'deadline' => '',
					'created_by' => '',
					'allrows' => true
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
				$title = isset($prefs['property']['mainscreen_showapprovals_title']) && $prefs['property']['mainscreen_showapprovals_title'] ? "{$prefs['property']['mainscreen_showapprovals_title']} ({$total_records})" : lang('approvals') . " ({$total_records})";
				$portalbox->setvar('title', $title);
				$portalbox->start_template();

				echo "\n" . '<!-- BEGIN approval info -->' . "\n<div class='property_approval' style='padding-left: 10px;'>" . $portalbox->draw() . "</div>\n" . '<!-- END approval info -->' . "\n";

				unset($portalbox);
				unset($pending_approvals);
			}

			//Sigurd: Consider remove
//			if ( isset($prefs['property']['mainscreen_showvendor_reminder'])
//			&& $prefs['property']['mainscreen_showvendor_reminder']  == 'yes' )
			if (false)
			{
				$total_records = 0;
				$title = 'dummy';
				//TODO Make listbox css compliant
				$portalbox = CreateObject('phpgwapi.listbox', array
					(
					'title' => $title,
					'primary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width' => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi', 'bg_filler', '.png', False)
				));

				$app_id = $GLOBALS['phpgw']->applications->name2id('property');
				if (!isset($GLOBALS['portal_order']) || !in_array($app_id, $GLOBALS['portal_order']))
				{
					$GLOBALS['portal_order'][] = $app_id;
				}

				$var = $this->get_controls($app_id);

				foreach ($var as $key => $value)
				{
					//			$portalbox->set_controls($key,$value);
				}

				$action_params = array
					(
					'appname' => 'property',
					'location' => '.project.workorder',
					//	'id'				=> $id,
					'responsible' => '',
					'responsible_type' => 'vendor',
					'action' => 'remind',
					'deadline' => '',
					'created_by' => $accound_id,
					'allrows' => true
				);

				$pending_reminder = execMethod('property.sopending_action.get_pending_action', $action_params);

				$portalbox->data = array();
				foreach ($pending_reminder as $entry)
				{
					$sql = 'SELECT org_name FROM fm_vendor where id=' . (int)$entry['responsible'];
					$GLOBALS['phpgw']->db;
					$GLOBALS['phpgw']->db->query($sql);
					$GLOBALS['phpgw']->db->next_record();
					$vendor_name = $GLOBALS['phpgw']->db->f('org_name', true);

					$portalbox->data[] = array
						(
						'text' => "påminning nr {$entry['reminder']} til leverandør {$vendor_name}- ordre nr: {$entry['item_id']}",
						'link' => $entry['url']
					);
					$total_records++;
				}

				$title = isset($prefs['property']['mainscreen_showvendor_reminder_title']) && $prefs['property']['mainscreen_showvendor_reminder_title'] ? "{$prefs['property']['mainscreen_showvendor_reminder_title']} ({$total_records})" : lang('vendor reminder') . " ({$total_records})";
				$portalbox->setvar('title', $title);
				$portalbox->start_template();

				echo "\n" . '<!-- BEGIN reminder info -->' . "\n<div class='property_reminder' style='padding-left: 10px;'>" . $portalbox->draw() . "</div>\n" . '<!-- END reminder info -->' . "\n";

				unset($pending_reminder);
				unset($portalbox);
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $save_app;
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = $maxmatches;
		}

		function after_navbar( )
		{
			$sosubstitute = CreateObject('property.sosubstitute');
			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$substitute_user_id = $sosubstitute->get_substitute( $user_id);
			$lang_substitute = $GLOBALS['phpgw']->translation->translate('substitute', array(), false, 'property');
			if($substitute_user_id)
			{
				echo '<div class="msg_good">';
				echo $lang_substitute .': ' . $GLOBALS['phpgw']->accounts->get($substitute_user_id)->__toString();
				echo '</div>';
			}

			$users_for_substitute = $sosubstitute->get_users_for_substitute( $user_id);
			$names = array();
			foreach ($users_for_substitute as $user_for_substitute)
			{
				$names[] = $GLOBALS['phpgw']->accounts->get($user_for_substitute)->__toString();
			}
			if($names)
			{
				echo '<div class="msg_good">';
				echo $lang_substitute .' for : ' . implode(', ', $names);
				echo '</div>';
			}

			if(in_array($substitute_user_id, $users_for_substitute))
			{
				echo '<div class="error">';
				echo $lang_substitute .': ' . lang('circle reference');
				echo '</div>';
			}
		}

		function delete_addressbook( $data )
		{
			$contact_id = (int) $data['contact_id'];
			$sql = "DELETE FROM fm_responsibility_contact WHERE contact_id = {$contact_id}";
			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
		}
	}