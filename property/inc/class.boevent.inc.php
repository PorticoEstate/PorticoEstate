<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage admin
	 * @version $Id$
	 */
	/*
	 * Import the datetime class for date processing
	 */
	phpgw::import_class('phpgwapi.datetime');

	if (!extension_loaded('mcal'))
	{
		define('MCAL_RECUR_NONE', 0);
		define('MCAL_RECUR_DAILY', 1);
		define('MCAL_RECUR_WEEKLY', 2);
		define('MCAL_RECUR_MONTHLY_MDAY', 3);
		define('MCAL_RECUR_MONTHLY_WDAY', 4);
		define('MCAL_RECUR_YEARLY', 5);

		define('MCAL_M_SUNDAY', 1);
		define('MCAL_M_MONDAY', 2);
		define('MCAL_M_TUESDAY', 4);
		define('MCAL_M_WEDNESDAY', 8);
		define('MCAL_M_THURSDAY', 16);
		define('MCAL_M_FRIDAY', 32);
		define('MCAL_M_SATURDAY', 64);

		define('MCAL_M_WEEKDAYS', 62);
		define('MCAL_M_WEEKEND', 65);
		define('MCAL_M_ALLDAYS', 127);
	}

	/**
	 * Description
	 * @package property
	 */
	class property_boevent
	{

		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $location_info = array();
		var $cached_events;
		protected $event_functions = array();
		var $public_functions = array
			(
			'event_schedule_data' => true,
			'event_schedule_week_data' => true,
//				'action'					=> true
		);

		function __construct( $session = false )
		{
			$this->so = CreateObject('property.soevent');
			$this->custom = CreateObject('property.custom_fields');//& $this->so->custom;
			$this->sbox = CreateObject('phpgwapi.sbox');
			$this->asyncservice = CreateObject('phpgwapi.asyncservice');

			if (isset($GLOBALS['phpgw_info']['user']['apps']['sms']))
			{
				$this->event_functions[1] = array
					(
					'id' => 1,
					'name' => 'Send SMS',
					'action' => 'property.boevent.send_sms'
				);
			}

			if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				$this->event_functions[2] = array
					(
					'id' => 2,
					'name' => 'Send Email',
					'action' => 'property.boevent.send_email'
				);
			}
			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query = phpgw::get_var('query');
			$sort = phpgw::get_var('sort');
			$order = phpgw::get_var('order');
			$filter = phpgw::get_var('filter', 'int');
			$cat_id = phpgw::get_var('cat_id', 'int');
			$location_id = phpgw::get_var('location_id', 'int');
			$allrows = phpgw::get_var('allrows', 'bool');
			$type = phpgw::get_var('type');
			$type_id = phpgw::get_var('type_id', 'int');
			$user_id = phpgw::get_var('user_id', 'int');
			$status_id = phpgw::get_var('status_id');

			$this->start = $start ? $start : 0;
			$this->query = isset($_REQUEST['query']) ? $query : $this->query;
			$this->sort = isset($_REQUEST['sort']) ? $sort : $this->sort;
			$this->order = isset($_REQUEST['order']) ? $order : $this->order;
			$this->filter = isset($_REQUEST['filter']) ? $filter : $this->filter;
			$this->cat_id = isset($_REQUEST['cat_id']) ? $cat_id : $this->cat_id;
			$this->location_id = isset($_REQUEST['location_id']) ? $location_id : $this->location_id;
			$this->user_id = isset($_REQUEST['user_id']) ? $user_id : $this->user_id;
			$this->allrows = isset($allrows) ? $allrows : false;
			$this->status_id = isset($_REQUEST['status_id']) ? $status_id : $this->status_id;
		}

		public function save_sessiondata( $data )
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data', 'category', $data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data', 'category');

			//		_debug_array($data);

			$this->start = $data['start'];
			$this->query = $data['query'];
			$this->filter = $data['filter'];
			$this->sort = $data['sort'];
			$this->order = $data['order'];
			$this->cat_id = $data['cat_id'];
			$this->allrows = $data['allrows'];
			$this->location_id = $data['location_id'];
			$this->user_id = $data['user_id'];
			$this->status_id = $data['status_id'];
		}

		public function read( $data = array() )
		{
//			$values = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
//			'allrows'=>$this->allrows, 'location_id' => $this->location_id, 'user_id' => $this->user_id, 'dry_run'=>$dry_run,
//			'status_id' => $this->status_id));

			$values = $this->so->read($data);

			static $locations = array();
			static $urls = array();
			$interlink = CreateObject('property.interlink');
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($values as &$entry)
			{
				$entry['date'] = $GLOBALS['phpgw']->common->show_date($entry['schedule_time'], $dateformat);
				$entry['receipt_date'] = $GLOBALS['phpgw']->common->show_date($entry['receipt_date'], $dateformat);

				if ($locations[$entry['location_id']])
				{
					$location = $locations[$entry['location_id']];
				}
				else
				{
					$location = $GLOBALS['phpgw']->locations->get_name($entry['location_id']);
					$locations[$entry['location_id']] = $location;
				}

				if ($urls[$entry['location_id']][$entry['location_item_id']])
				{
					$entry['url'] = $urls[$entry['location_id']][$entry['location_item_id']];
				}
				else
				{
					$entry['url'] = $interlink->get_relation_link($location['location'], $entry['location_item_id']);
					$urls[$entry['location_id']][$entry['location_item_id']] = $entry['url'];
				}
				$entry['location_name'] = $interlink->get_location_name($location['location']);
				$entry['location'] = $location['location'];
			}

			$this->total_records = $this->so->total_records;
			$this->uicols = $this->so->uicols;

			return $values;
		}

		public function read_single( $id )
		{
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$values = $this->so->read_single($id);
			if ($values)
			{
				$values['start_date'] = $GLOBALS['phpgw']->common->show_date($values['start_date'], $dateformat);
				$values['end_date'] = $GLOBALS['phpgw']->common->show_date($values['end_date'], $dateformat);
				if ($values['rpt_day'])
				{
					$rpt_day = array
						(
						1 => 'Sunday',
						2 => 'Monday',
						4 => 'Tuesday',
						8 => 'Wednesday',
						16 => 'Thursday',
						32 => 'Friday',
						64 => 'Saturday'
					);

					foreach ($rpt_day as $mask => $name)
					{
						if ($mask & $values['rpt_day'])
						{
							$values['repeat_day'][$mask] = $name;
						}
					}
				}

				$location = phpgw::get_var('location');
				$job_id = "property{$location}::{$values['location_item_id']}::{$values['attrib_id']}";
				$job = execMethod('phpgwapi.asyncservice.read', $job_id);

				$values['next'] = $GLOBALS['phpgw']->common->show_date($job[$job_id]['next'], $dateformat);
			}

//			$this->find_scedules($criteria);

			return $values;
		}

		public function update_receipt( $data )
		{
			return $this->so->update_receipt($data);
		}

		public function save( $data )
		{
			$data['start_date'] = phpgwapi_datetime::date_to_timestamp($data['start_date']);
			$data['end_date'] = phpgwapi_datetime::date_to_timestamp($data['end_date']);
//			_debug_array($data);die();
			if (isset($data['id']) && $data['id'] > 0 && $this->so->read_single($data['id']))
			{
				$receipt = $this->so->edit($data);
			}
			else
			{
				$receipt = $this->so->add($data);
			}
			/*
			  $action_object		= CreateObject('property.sogeneric');
			  $action_object->get_location_info('event_action',false);
			  $action	= $action_object->read_single(array('id'=> $data['action']),$values = array());
			 */
			$rpt_day = array
				(
				1 => 0, //'Sunday',
				2 => 1, //'Monday',
				4 => 2, //'Tuesday',
				8 => 3, //'Wednesday',
				16 => 4, //'Thursday',
				32 => 5, //'Friday',
				64 => 6  //'Saturday'
			);

			$repeat_day = array();
			if ($data['repeat_day'])
			{
				foreach ($data['repeat_day'] as $day)
				{
					if (isset($rpt_day[$day]))
					{
						$repeat_day[] = $rpt_day[$day];
					}
				}
				$repeat_day = implode(',', $repeat_day);
			}

			if (!isset($data['repeat_type']) || !$data['repeat_type'])
			{
				$times = $data['start_date'];
			}
			else
			{
				$dow = $rpt_day[$data['repeat_day'][0]];
				switch ($data['repeat_type'])
				{
					case '0':
						$times = $data['start_date'];
						break;
					case '1': //'Daily'
						if ($data['repeat_interval'])
						{
							$times = array('day' => "*/{$data['repeat_interval']}");
						}
						else
						{
							$times = array('day' => "*/1");
						}
						break;
					case '2': //'Weekly'
						if ($data['repeat_interval'])
						{
							$day = $data['repeat_interval'] * 7;
							$times = array('day' => "*/{$day}");
						}
						else
						{
							$times = array('day' => "*/7");
						}
						if ($data['repeat_day'])
						{
							$times['dow'] = $repeat_day;
						}
						break;
					case '3': //'Monthly (by day)'
						if (!isset($data['repeat_day']) || !is_array($data['repeat_day']))
						{
							$dow = 1;
						}

						if ($data['repeat_interval'])
						{
							$times = array('month' => "*/{$data['repeat_interval']}", 'dow' => $dow);
						}
						else
						{
							$times = array('month' => "*/1", 'dow' => $dow);
						}
						break;
					case '4': //'Monthly (by date)'
						if ($data['repeat_interval'])
						{
							$times = array('month' => "*/{$data['repeat_interval']}", 'day' => 1);
						}
						else
						{
							$times = array('day' => 1);
						}
						break;
					case '5': //'Yearly'
						$month = date(n, $data['start_date']);
						if ($data['repeat_interval'])
						{
							$times = array('year' => "*/{$data['repeat_interval']}", 'month' => $month);
						}
						else
						{
							$times = array('month' => $month);
						}
						break;
					default:
						$times = $data['start_date'];
						break;
				}
			}

			//$times['min']= '*'; // for testing the  - every minute

			$account_id = execMethod('property.soresponsible.get_contact_user_id', $data['responsible_id']);

			$timer_data = array
				(
				'start' => $data['start_date'],
				'enabled' => true,
				'owner' => $account_id,
				'enabled' => !!$data['enabled'],
//					'action'		=> $action['action'],
				'action' => $this->event_functions[$data['action']]['action'],
				'action_data' => array('contact_id' => $data['contact_id'])
			);

			if ($data['end_date'])
			{
				$timer_data['end'] = $data['end_date'];
			}

			if ($action['data'])
			{
				str_replace(";", '', $action['data']);
				eval('$action_data = ' . htmlspecialchars_decode($action['data']) . ';');
				$timer_data = array_merge($timer_data, $action_data);
			}

			$location = phpgw::get_var('location');

			$id = "property{$location}::{$data['item_id']}::{$receipt['id']}";
			$timer_data['id'] = $id;

			$this->asyncservice->cancel_timer($id);
			$this->asyncservice->set_timer($times, $id, 'property.boevent.action', $timer_data, $account_id);

			$event = $this->so->read_single($receipt['id']);

			$criteria = array
				(
				'start_date' => $event['start_date'],
				'end_date' => $event['end_date'],
				'location_id' => $event['location_id'],
				'location_item_id' => $event['location_item_id']
			);

			$this->find_scedules($criteria);
			$schedule = $this->cached_events;
			$this->so->create_schedule(array('event_id' => $receipt['id'], 'schedule' => $schedule));

			return $receipt;
		}

		public function action( $data )
		{
			$parts = explode('::', $data['id']);
			$id = end($parts);
			$now = time();

			$event = $this->so->read_single($id);
			$_perform_action = false;

			foreach ($event['event_schedule'] as $_schedule)
			{
				if (in_array($_schedule, $event['event_receipt']) || in_array($_schedule, $event['repeat_exception']))
				{
					continue;
				}
				if ($_schedule < $now && $data['enabled'])
				{
					$_perform_action = true;
					break;
				}
			}

			if ($_perform_action)
			{
				list($module, $classname) = explode('.', $data['action'], 3);
				$file = PHPGW_INCLUDE_ROOT . "/{$module}/inc/class.{$classname}.inc.php";
				if (is_file($file))
				{
					$message = execMethod($data['action'], $data);
				}
				else
				{
					$message = "No such file: {$file}";
				}

				$this->so->cron_log(array
					(
					'cron' => true, // or false for manual...
					'action' => isset($data['action']) && $data['action'] ? $data['action'] : 'dummy',
					'message' => $message
					)
				);
			}
		}

		public function delete( $id )
		{
			$values = $this->read_single($id);
			$location = phpgw::get_var('location');
			$job_id = "property{$location}::{$values['location_item_id']}::{$values['attrib_id']}";
			$job = execMethod('phpgwapi.asyncservice.cancel_timer', $job_id);

			return $this->so->delete($id);
		}

		public function get_rpt_type_list( $selected = '' )
		{
			$rpt_type = array
				(
				0 => 'None',
				1 => 'Daily',
				2 => 'Weekly',
				3 => 'Monthly (by date)',
				4 => 'Monthly (by day)',
				5 => 'Yearly'
			);


			return $this->sbox->getArrayItem('values[repeat_type]', $selected, $rpt_type);
		}

		public function get_rpt_day_list( $selected = array() )
		{
			$rpt_day = array
				(
				1 => 'Sunday',
				2 => 'Monday',
				4 => 'Tuesday',
				8 => 'Wednesday',
				16 => 'Thursday',
				32 => 'Friday',
				64 => 'Saturday'
			);

			$title = lang('(for weekly)');
			$i = 0;
			$boxes = '';
			foreach ($rpt_day as $mask => $name)
			{
				$boxes .= '<input type="checkbox" title = "' . $title . '"name="values[repeat_day][]" value="' . $mask . '"' . (isset($selected[$mask]) && $selected[$mask] ? ' checked' : '') . '></input> ' . lang($name) . "\n";
				if (++$i == 5)
					$boxes .= '<br />';
			}
			return $boxes;
		}

		public function get_responsible( $selected = '' )
		{
			$responsible = CreateObject('property.soresponsible');

			$location = phpgw::get_var('location');
			$values = $responsible->read_type(array('start' => 0, 'query' => '', 'sort' => '',
				'order' => '', 'location' => $location, 'allrows' => true,
				'filter' => ''));
			$list = array(0 => lang('none'));
			foreach ($values as $entry)
			{
				$list[$entry['id']] = $entry['name'];
			}

			return $this->sbox->getArrayItem('values[responsible]', $selected, $list, true);
		}

		public function get_action( $selected = '' )
		{
			/* 			$action_object					= CreateObject('property.sogeneric');
			  $action_object->get_location_info('event_action',false);
			  $values					= $action_object->read(array('allrows'=> true));
			 */
			$list = array(0 => lang('none'));

			foreach ($this->event_functions as $entry)
			{
				$list[$entry['id']] = $entry['name'];
			}

			return $this->sbox->getArrayItem('values[action]', $selected, $list, true);
		}

		public function send_sms( $data )
		{
			$parts = explode('::', $data['id']);
			$id = $parts[1];
			$location_arr = explode('.', $parts[0]);
			$interlink = CreateObject('property.interlink');
			$relation_link = $interlink->get_relation_link(".{$location_arr[1]}", $id, 'view', true);

			$contact_id = isset($data['action_data']['contact_id']) ? $data['action_data']['contact_id'] : 0;
			if (!$contact_id)
			{
				return false;
			}

			$comms = execMethod('addressbook.boaddressbook.get_comm_contact_data', $contact_id);

			$number = $comms[$contact_id]['mobile (cell) phone'];
			$subject = lang('reminder');
			$message = "<a href =\"{$relation_link}\">" . lang('record') . ' #' . $id . '</a>' . "\n";

			$data = array
				(
				'p_num_text' => $number,
				'message' => "{$subject}:\n{$message}"
			);

			if (execMethod('sms.bosms.send_sms', $data))
			{
				return $number;
			}
		}

		public function send_email( $data )
		{
			$parts = explode('::', $data['id']);
			$id = $parts[1];
			$location_arr = explode('.', $parts[0]);
			$interlink = CreateObject('property.interlink');
			$relation_link = $interlink->get_relation_link(".{$location_arr[1]}", $id, 'view', true);

			$contact_id = isset($data['action_data']['contact_id']) ? $data['action_data']['contact_id'] : 0;
			if (!$contact_id)
			{
				return false;
			}

			$account_id = $GLOBALS['phpgw']->accounts->search_person($contact_id);
			$socommon = CreateObject('property.socommon');
			$prefs = $socommon->create_preferences('property', $account_id);
			$comms = execMethod('addressbook.boaddressbook.get_comm_contact_data', $contact_id);
			$_address = isset($comms[$contact_id]['work email']) && $comms[$contact_id]['work email'] ? $comms[$contact_id]['work email'] : $prefs['email'];

			$subject = lang('reminder');
			$message = "<a href =\"{$relation_link}\">" . lang('record') . ' #' . $id . '</a>' . "\n";
			if (!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}
			try
			{
				$GLOBALS['phpgw']->send->msg('email', $_address, $subject, stripslashes($message), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html');
			}
			catch (Exception $e)
			{
				$receipt['error'][] = array('msg' => $e->getMessage());
				$GLOBALS['phpgw']->log->error(array(
					'text' => 'property_boevent::send_email() failed with %1',
					'p1' => $e->getMessage(),
					'p2' => '',
					'line' => __LINE__,
					'file' => __FILE__
				));

				return false;
			}
			return $_address;
		}

		/**
		 * Find recurring events
		 *
		 * @param array $date the date array to convert, must contain keys 'start_date', 'end_date', 'appname', 'location'
		 * @return array events
		 */
		function find_scedules( $params )
		{
			if (!is_array($params))
			{
				return False;
			}

			if (!isset($params['location_id']) || !$params['location_id'])
			{
				if (!isset($params['appname']) || !$params['appname'] || !isset($params['location']) || !$params['location'])
				{
					throw new Exception("property_boevent::find_scedules - Missing location info in input");
				}
				$location_id = $GLOBALS['phpgw']->locations->get_id($params['appname'], $params['location']);
			}
			else
			{
				$location_id = $params['location_id'];
			}

			if (!isset($params['location_item_id']) || !$params['location_item_id'])
			{
				throw new Exception("property_boevent::find_scedules - Missing location_item_id in input");
			}
			$location_item_id = $params['location_item_id'];

			if ($params['start_date'])
			{
				$syear = date('Y', $params['start_date']);
				$smonth = date('m', $params['start_date']);
				$sday = date('d', $params['start_date']);
			}

			$eyear = $params['end_date'] ? date('Y', $params['end_date']) : 0;
			$emonth = $params['end_date'] ? date('m', $params['end_date']) : 0;
			$eday = $params['end_date'] ? date('d', $params['end_date']) : 0;

			$owner_id = (isset($params['owner']) ? $params['owner'] : 0);
			if ($owner_id == 0 && $this->is_group)
			{
				unset($owner_id);
				$owner_id = $this->g_owner;
				if ($this->debug)
				{
					echo '<!-- owner_id in (' . implode($owner_id, ',') . ') -->' . "\n";
				}
			}

			if (!$eyear && !$emonth && !$eday)
			{
				$edate = mktime(23, 59, 59, $smonth + 1, $sday + 1, $syear);
				$eyear = date('Y', $edate);
				$emonth = date('m', $edate);
				$eday = date('d', $edate);
			}
			else
			{
				if (!$eyear)
				{
					$eyear = $syear;
				}
				if (!$emonth)
				{
					$emonth = $smonth + 1;
					if ($emonth > 12)
					{
						$emonth = 1;
						$eyear++;
					}
				}
				if (!$eday)
				{
					$eday = $sday + 1;
				}
				$edate = mktime(23, 59, 59, $emonth, $eday, $eyear);
			}

			if ($this->debug)
			{
				echo '<!-- Start Date : ' . sprintf("%04d%02d%02d", $syear, $smonth, $sday) . ' -->' . "\n";
				echo '<!-- End   Date : ' . sprintf("%04d%02d%02d", $eyear, $emonth, $eday) . ' -->' . "\n";
			}

			$find_criteria = array
				(
				'syear' => $syear,
				'smonth' => $smonth,
				'sday' => $sday,
				'eyear' => $eyear,
				'emonth' => $emonth,
				'eday' => $eday,
				'tz_offset' => 0,
				'extra' => '',
				'owner_id' => $owner_id,
				'location_id' => $location_id,
				'location_item_id' => $location_item_id
			);

			$cached_event_ids = $this->so->list_events($find_criteria);
			$cached_event_ids_repeating = $this->so->list_repeated_events($find_criteria);
			//_debug_array($cached_event_ids_repeating);die();
			unset($find_criteria);

			if ($this->debug)
			{
				echo '<!-- events cached : ' . count($cached_event_ids) . ' : for : ' . sprintf("%04d%02d%02d", $syear, $smonth, $sday) . ' -->' . "\n";
				echo '<!-- repeating events cached : ' . count($cached_event_ids_repeating) . ' : for : ' . sprintf("%04d%02d%02d", $syear, $smonth, $sday) . ' -->' . "\n";
			}

			$this->cached_events = array();

			if (!$cached_event_ids && !$cached_event_ids_repeating)
			{
				return;
			}


			foreach ($cached_event_ids as $cached_event_id)
			{
				$event = $this->so->read_single($cached_event_id);
				$startdate = intval(date('Ymd', $event['start_date']));
				$enddate = intval(date('Ymd', $event['end_date']));
				$this->cached_events[$startdate][] = $event;
				if ($startdate != $enddate && !$cached_event_ids_repeating)
				{
					$start['year'] = intval(substr($startdate, 0, 4));
					$start['month'] = intval(substr($startdate, 4, 2));
					$start['mday'] = intval(substr($startdate, 6, 2));
					for ($j = $startdate, $k = 0; $j <= $enddate; $k++, $j = intval(date('Ymd', mktime(0, 0, 0, $start['month'], $start['mday'] + $k, $start['year']))))
					{
						$c_evt_day = 0;
						if (isset($this->cached_events[$j]) && is_array($this->cached_events[$j]))
						{
							$c_evt_day = count($this->cached_events[$j]) - 1;
						}
						if ($this->debug)
						{
							echo 'Date: ' . $j . ' Count : ' . count($this->cached_events[$j]) . "\n";
						}

						if (!isset($this->cached_events[$j][$c_evt_day]) || $this->cached_events[$j][$c_evt_day]['id'] != $event['id'])
						{
							if ($this->debug)
							{
								echo "Adding Event ID {$event['id']} for Date: {$j}</br>";
							}
							$this->cached_events[$j][] = $event;
						}
					}
				}
			}

			$this->repeating_events = array();
			if ($cached_event_ids_repeating)
			{
				//_debug_array($cached_event_ids_repeating);die();
				$i = 0;
				foreach ($cached_event_ids_repeating as $cached_event_id)
				{
					$this->repeating_events[] = $this->so->read_single($cached_event_id);

					if ($this->debug)
					{
						echo 'Cached Events ID: ' . $cached_event_id . ' (' . sprintf("%04d%02d%02d", $this->repeating_events[$i]['start']['year'], $this->repeating_events[$i]['start']['month'], $this->repeating_events[$i]['start']['mday']) . ')</br>';
					}
					$i++;
				}
//				$edate -= phpgwapi_datetime::user_timezone();
//				for($date=mktime(0,0,0,$smonth,$sday,$syear) - phpgwapi_datetime::tz_offset;$date<=$edate;$date += 86400)
				for ($date = mktime(0, 0, 0, $smonth, $sday, $syear); $date <= $edate; $date += phpgwapi_datetime::SECONDS_IN_DAY)
				{
					if ($this->debug)
					{
						$search_date = date('Ymd', $date);
						echo 'Calling check_repeating_events(' . $search_date . ')</br>';
					}
					$this->check_repeating_events($date);
					if ($this->debug)
					{
						echo 'Total events found matching ' . $search_date . ' = ' . count($this->cached_events[$search_date]) . '</br>';
						for ($i = 0; $i < count($this->cached_events[$search_date]); $i++)
						{
							echo '<!-- Date: ' . $search_date . ' [' . $i . '] = ' . $this->cached_events[$search_date][$i]['id'] . ' -->' . "\n";
						}
					}
				}
			}
			$retval = array();
			for ($j = date('Ymd', mktime(0, 0, 0, $smonth, $sday, $syear)), $k = 0; $j <= date('Ymd', mktime(0, 0, 0, $emonth, $eday, $eyear)); $k++, $j = date('Ymd', mktime(0, 0, 0, $smonth, $sday + $k, $syear)))
			{
				if (isset($this->cached_events[$j]) && is_array($this->cached_events[$j]))
				{
					$retval[$j] = $this->cached_events[$j];
				}
			}
			//_debug_array($this->cached_events);die();
			return $retval;
//			return $this->cached_events;
		}

		function check_repeating_events( $datetime )
		{
			@reset($this->repeating_events);
			$search_date_full = date('Ymd', $datetime);
			$search_date_year = date('Y', $datetime);
			$search_date_month = date('m', $datetime);
			$search_date_day = date('d', $datetime);
			$search_date_dow = date('w', $datetime);
			$search_date_week = date('YW', $datetime);
			$search_beg_day = mktime(13, 0, 0, $search_date_month, $search_date_day, $search_date_year);
			if ($this->debug)
			{
				echo '<!-- Search Date Full = ' . $search_date_full . ' -->' . "\n";
			}
			$repeated = $this->repeating_events;
			$r_events = count($repeated);
			for ($i = 0; $i < $r_events; $i++)
			{
				if ($this->repeating_events[$i]['repeat_type'] != 0)
				{
					$rep_events = $this->repeating_events[$i];

					$id = $rep_events['id'];
					$rep_events['start']['month'] = date('m', $rep_events['start_date']);
					$rep_events['start']['mday'] = date('d', $rep_events['start_date']);
					$rep_events['start']['year'] = date('Y', $rep_events['start_date']);

//					$event_beg_day = mktime(0,0,0,$rep_events['start']['month'],$rep_events['start']['mday'],$rep_events['start']['year']);
					$event_beg_day = $rep_events['start_date'];
					$event_beg_week = date('YW', $rep_events['start_date']);

					if (isset($rep_events['end_date']) && $rep_events['end_date'])
					{
						$event_recur_time = $rep_events['end_date'];
					}
					else
					{
						$event_recur_time = mktime(0, 0, 0, 1, 1, 2030);
					}
					/*
					  if($rep_events['recur_enddate']['month'] != 0 && $rep_events['recur_enddate']['mday'] != 0 && $rep_events['recur_enddate']['year'] != 0)
					  {
					  $event_recur_time = $this->maketime($rep_events['recur_enddate']);
					  }
					  else
					  {
					  $event_recur_time = mktime(0,0,0,1,1,2030);
					  }
					 */
					$end_recur_date = date('Ymd', $event_recur_time);
					$full_event_date = date('Ymd', $event_beg_day);

					if ($this->debug)
					{
						echo '<!-- check_repeating_events - Processing ID - ' . $id . ' -->' . "\n";
						echo '<!-- check_repeating_events - Recurring End Date - ' . $end_recur_date . ' -->' . "\n";
					}

					// only repeat after the beginning, and if there is an rpt_end before the end date
					if (($search_date_full > $end_recur_date) || ($search_date_full < $full_event_date))
					{
						continue;
					}

					if ($search_date_full == $full_event_date)
					{
						$this->sort_event($rep_events, $search_date_full);
						continue;
					}
					else
					{
						$freq = (isset($rep_events['repeat_interval']) && $rep_events['repeat_interval'] ? $rep_events['repeat_interval'] : 1);
						$type = $rep_events['repeat_type'];
						switch ($type)
						{
							case MCAL_RECUR_DAILY:
								if ($this->debug)
								{
									echo '<!-- check_repeating_events - MCAL_RECUR_DAILY - ' . $id . ' -->' . "\n";
								}
								//if ($freq == 1 && $rep_events['recur_enddate']['month'] != 0 && $rep_events['recur_enddate']['mday'] != 0 && $rep_events['recur_enddate']['year'] != 0 && $search_date_full <= $end_recur_date)
								if ($freq == 1 && $rep_events['end_date'] && $search_date_full <= $end_recur_date)
								{
									$this->sort_event($rep_events, $search_date_full);
								}
								elseif (floor(($search_beg_day - $event_beg_day) / 86400) % $freq)
								{
									continue 2;
								}
								else
								{
									$this->sort_event($rep_events, $search_date_full);
								}
								break;
							case MCAL_RECUR_WEEKLY:
								$test = (($search_beg_day - $event_beg_day) / 604800) / $freq;
								$test_subtract = floor($test);

								//if (floor(($search_beg_day - $event_beg_day)/604800) % $freq)
								if (!$rep_events['rpt_day'])
								{
									if (!($test - $test_subtract))
									{
										$this->sort_event($rep_events, $search_date_full);
									}
								}
								else
								{
									$test = ($search_date_week - $event_beg_week) / $freq;
									$test_subtract = floor($test);
									if (($test - $test_subtract))
									{
										continue 2;
									}

									$check = 0;
									switch ($search_date_dow)
									{
										case 0:
											$check = MCAL_M_SUNDAY;
											break;
										case 1:
											$check = MCAL_M_MONDAY;
											break;
										case 2:
											$check = MCAL_M_TUESDAY;
											break;
										case 3:
											$check = MCAL_M_WEDNESDAY;
											break;
										case 4:
											$check = MCAL_M_THURSDAY;
											break;
										case 5:
											$check = MCAL_M_FRIDAY;
											break;
										case 6:
											$check = MCAL_M_SATURDAY;
											break;
									}

									if ($rep_events['rpt_day'] & $check)
									{
										$this->sort_event($rep_events, $search_date_full);
									}
								}
								break;
							case MCAL_RECUR_MONTHLY_WDAY:
								if ((($search_date_year - $rep_events['start']['year']) * 12 + $search_date_month - $rep_events['start']['month']) % $freq)
								{
									continue 2;
								}

								if ((phpgwapi_datetime::day_of_week($rep_events['start']['year'], $rep_events['start']['month'], $rep_events['start']['mday']) == phpgwapi_datetime::day_of_week($search_date_year, $search_date_month, $search_date_day)) && (ceil($rep_events['start']['mday'] / 7) == ceil($search_date_day / 7)))
								{
									$this->sort_event($rep_events, $search_date_full);
								}
								break;
							case MCAL_RECUR_MONTHLY_MDAY:
								if ((($search_date_year - $rep_events['start']['year']) * 12 + $search_date_month - $rep_events['start']['month']) % $freq)
								{
									continue 2;
								}
								if ($search_date_day == $rep_events['start']['mday'])
								{
									$this->sort_event($rep_events, $search_date_full);
								}
								break;
							case MCAL_RECUR_YEARLY:
								if (($search_date_year - $rep_events['start']['year']) % $freq)
								{
									continue 2;
								}
								if (date('dm', $datetime) == date('dm', $event_beg_day))
								{
									$this->sort_event($rep_events, $search_date_full);
								}
								break;
						}
					}
				}
			} // end for loop
		}

// end function

		function sort_event( $event, $date )
		{
			$inserted = False;
			$event['start']['month'] = date('m', $event['start_date']);
			$event['start']['mday'] = date('d', $event['start_date']);
			$event['start']['year'] = date('Y', $event['start_date']);

			if (is_array($event['repeat_exception']) && $inserted == false)
			{
				//$event_time = mktime($event['start']['hour'],$event['start']['min'],0,intval(substr($date,4,2)),intval(substr($date,6,2)),intval(substr($date,0,4))) - phpgwapi_datetime::user_timezone();
				$event_time = mktime($event['start']['hour'], $event['start']['min'], 0, intval(substr($date, 4, 2)), intval(substr($date, 6, 2)), intval(substr($date, 0, 4)));
                                //while ($inserted == false && list($key, $exception_time) = each($event['repeat_exception']))                                       
                                foreach($event['repeat_exception'] as $key => $exception_time)
				{
					if ($this->debug)
					{
						echo '<!-- checking exception datetime ' . $exception_time . ' to event datetime ' . $event_time . ' -->' . "\n";
					}
					if ($exception_time == $event_time)
					{
						//_debug_array(date('Y-m-d',$event_time));die();
						//						_debug_array($event);
						//						_debug_array($this->cached_events);die();
						//						$inserted = true;
						$event['exception'] = true;

						/*
						  for($i=0;$i<count($this->cached_events[$date]);$i++)
						  {
						  if($this->cached_events[$date][$i]['id'] == $event['id'])
						  {
						  die();
						  }
						  }
						 */
					}
				}
			}
			if (isset($this->cached_events[$date]) && $this->cached_events[$date] && $inserted == false)
			{

				if ($this->debug)
				{
					echo '<!-- Cached Events found for ' . $date . ' -->' . "\n";
				}
				$year = substr($date, 0, 4);
				$month = substr($date, 4, 2);
				$day = substr($date, 6, 2);

				if ($this->debug)
				{
					echo '<!-- Date : ' . $date . ' Count : ' . count($this->cached_events[$date]) . ' -->' . "\n";
				}

				for ($i = 0; $i < count($this->cached_events[$date]); $i++)
				{
					$events = $this->cached_events[$date][$i];
					if ($this->cached_events[$date][$i]['id'] == $event['id'] || $this->cached_events[$date][$i]['reference'] == $event['id'])
					{
						if ($this->debug)
						{
							echo '<!-- Item already inserted! -->' . "\n";
						}
						$inserted = True;
						break;
					}
					/* This puts all spanning events across multiple days up at the top. */
					if ($this->cached_events[$date][$i]['repeat_type'] == MCAL_RECUR_NONE)
					{
						if ($this->cached_events[$date][$i]['start']['mday'] != $day && $this->cached_events[$date][$i]['end']['mday'] >= $day)
						{
							continue;
						}
					}
					if (date('Hi', mktime($event['start']['hour'], $event['start']['min'], $event['start']['sec'], $month, $day, $year)) < date('Hi', mktime($this->cached_events[$date][$i]['start']['hour'], $this->cached_events[$date][$i]['start']['min'], $this->cached_events[$date][$i]['start']['sec'], $month, $day, $year)))
					{
						//				for($j=count($this->cached_events[$date]);$j>=$i;$j--)
						for ($j = count($this->cached_events[$date]); $j >= ($i + 1); $j--)
						{
							$this->cached_events[$date][$j] = $this->cached_events[$date][$j - 1];
						}
						if ($this->debug)
						{
							echo '<!-- Adding event ID: ' . $event['id'] . ' to cached_events -->' . "\n";
						}
						$inserted = True;
						$this->cached_events[$date][$i] = $event;
						break;
					}
				}
			}
			if (!$inserted)
			{
				if ($this->debug)
				{
					echo '<!-- Adding event ID: ' . $event['id'] . ' to cached_events -->' . "\n";
				}
				$this->cached_events[$date][] = $event;
			}
		}

		public function init_schedule_week( $id, $buildingmodule, $resourcemodule, $search = null )
		{
			$date = new DateTime(phpgw::get_var('date'));
			// Make sure $from is a monday
			if ($date->format('w') != 1)
			{
				$date->modify('last monday');
			}

			$prev_date = clone $date;
			$next_date = clone $date;
			$prev_date->modify('-1 week');
			$next_date->modify('+1 week');
			$resource = $this->read_single($id);
			if ($search)
			{
				$resource['buildings_link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $search,
					"type" => "building"));
			}
			else
			{
				$resource['buildings_link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $buildingmodule . '.index'));
			}

			$resource['building_link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $buildingmodule . '.show',
				'id' => $resource['building_id']));
			$resource['resource_link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $resourcemodule . '.show',
				'id' => $resource['id']));
			$resource['date'] = $date->format('Y-m-d');
			$resource['week'] = intval($date->format('W'));
			$resource['year'] = intval($date->format('Y'));
			$resource['prev_link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $resourcemodule . '.schedule_week',
				'id' => $resource['id'], 'date' => $prev_date->format('Y-m-d')));
			$resource['next_link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $resourcemodule . '.schedule_week',
				'id' => $resource['id'], 'date' => $next_date->format('Y-m-d')));
			for ($i = 0; $i < 7; $i++)
			{
				$resource['days'][] = array('label' => sprintf('%s<br/>%s %s', lang($date->format('l')), lang($date->format('M')), $date->format('d')),
					'key' => $date->format('D'));
				$date->modify('+1 day');
			}
			return $resource;
		}

		/**
		 * Find recurring events for a week
		 *
		 * @return array schedule
		 */
		public function event_schedule_week_data()
		{
			//		    $date = new DateTime(phpgw::get_var('date')); Use this one when moving to php 5.3

			$datetime = CreateObject('phpgwapi.datetime');
			$date = $datetime->convertDate(phpgw::get_var('date'), 'Y-m-d', $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$datetime_start = $datetime->date_to_timestamp($date);

			$id = phpgw::get_var('resource_id', 'int');

			$event = $this->so->read_single($id);
			$criteria = array
				(
				'start_date' => $datetime_start,
				'end_date' => $datetime_start + (86400 * 6),
				'location_id' => $event['location_id'],
				'location_item_id' => $event['location_item_id']
			);

			$this->find_scedules($criteria);
			$schedules = $this->cached_events;

			$total_records = 0;
			foreach ($schedules as $_date => $set)
			{
				if (count($set) > $total_records)
				{
					$total_records = count($set);
				}
			}

			$lang_exception = lang('exception');
			$values = array();
			for ($i = 0; $i < $total_records; $i++)
			{
				$values[$i] = array
					(
					'resource' => 'descr',
					'resource_id' => 11,
					'time' => $i + 1,
					'_from' => '16:30',
					'_to' => '17:00'
				);

				foreach ($schedules as $_date => $set)
				{
					$__date = substr($_date, 0, 4) . '-' . substr($_date, 4, 2) . '-' . substr($_date, 6, 2);
					$date = new DateTime($__date);
					$day_of_week = $date->format('D');
					$values[$i][$day_of_week] = array
						(
						'exception' => $set[$i]['exception'],
						'lang_exception' => $lang_exception,
						'type' => 'event',
						'name' => $set[$i]['descr'],
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uievent.show',
							'location_id' => $set[$i]['location_id'], 'location_item_id' => $set[$i]['location_item_id']))
					);
				}
			}

			$data = array
				(
				'ResultSet' => array(
					"totalResultsAvailable" => $total_records,
					"Result" => $values
				)
			);
			//_debug_array($data);die();
			return $data;
		}

		/**
		 * Find recurring events for a period defined by the event
		 *
		 * @return array schedule
		 */
		public function event_schedule_data()
		{
			$id = phpgw::get_var('id', 'int');

			$event = $this->so->read_single($id);

			$criteria = array
				(
				'start_date' => $event['start_date'],
				'end_date' => $event['end_date'],
				'location_id' => $event['location_id'],
				'location_item_id' => $event['location_item_id']
			);

			$this->find_scedules($criteria);
			$schedules = $this->cached_events;

			$total_records = 0;

			$lang_exception = lang('exception');
			$values = array();

			$i = 1;
			foreach ($schedules as $_date => $set)
			{
				$__date = substr($_date, 0, 4) . '-' . substr($_date, 4, 2) . '-' . substr($_date, 6, 2);
				$date = phpgwapi_datetime::convertDate($__date, 'Y-m-d', $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

				foreach ($set as $entry)
				{
					$values[] = array
						(
						'time' => $i,
						'date' => array
							(
							'exception' => $entry['exception'],
							'lang_exception' => $lang_exception,
							'type' => 'event',
							'name' => $date,
							'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uievent.show',
								'location_id' => $entry['location_id'], 'location_item_id' => $entry['location_item_id']))
						)
					);

					$i++;
				}
			}

			$data = array
				(
				'ResultSet' => array(
					"totalResultsAvailable" => $total_records,
					"Result" => $values
				)
			);

			return $data;
		}

		public function set_exceptions( $data = array() )
		{
			if (!isset($data['event_id']) || !$data['event_id'])
			{
				throw new Exception("property_boevent::set_exceptions - Missing event_id info in input");
			}
			$this->so->set_exceptions($data);
		}

		public function get_event_location()
		{
			$interlink = CreateObject('property.interlink');
			$locations = $this->so->get_event_location();
			foreach ($locations as &$location)
			{
				$temp = $GLOBALS['phpgw']->locations->get_name($location['id']);
				$location['name'] = $interlink->get_location_name($temp['location']);
			}
			return $locations;
		}
	}