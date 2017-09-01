<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package eventplanner
	 * @subpackage calendar
	 * @version $Id:$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU Lesser General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	phpgw::import_class('phpgwapi.bocommon');
	phpgw::import_class('eventplanner.socalendar');

	include_class('eventplanner', 'calendar', 'inc/model/');

	class eventplanner_bocalendar extends phpgwapi_bocommon
	{
		protected static
			$bo,
			$fields,
			$acl_location;

		public function __construct()
		{
			$this->fields = eventplanner_calendar::get_fields();
			$this->acl_location = eventplanner_calendar::acl_location;
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *
		 * @return the class instance.
		 */
		public static function get_instance()
		{
			if (self::$bo == null)
			{
				self::$bo = new eventplanner_bocalendar();
			}
			return self::$bo;
		}

		public function store($object)
		{
			$this->store_pre_commit($object);
			$ret = eventplanner_socalendar::get_instance()->store($object);
			$this->store_post_commit($object);
			return $ret;
		}

		public function read($params)
		{
			$status_text = array(lang('inactive'), lang('active'));
			if(empty($params['filters']['active']))
			{
				$params['filters']['active'] = 1;
			}
			else
			{
				unset($params['filters']['active']);
			}
			$values =  eventplanner_socalendar::get_instance()->read($params);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($values['results'] as &$entry)
			{
				$entry['created'] = $GLOBALS['phpgw']->common->show_date($entry['created']);
				$entry['modified'] = $GLOBALS['phpgw']->common->show_date($entry['modified']);
				$entry['from_'] = $GLOBALS['phpgw']->common->show_date($entry['from_']);
				$entry['to_'] = $GLOBALS['phpgw']->common->show_date($entry['to_']);
				$entry['status'] = $status_text[$entry['active']];
			}
			return $values;
		}

		public function read_single($id, $return_object = true)
		{
			if ($id)
			{
				$values = eventplanner_socalendar::get_instance()->read_single($id, $return_object);
			}
			else
			{
				$values = new eventplanner_calendar();
			}

			return $values;
		}

		public function update_active_status( $ids, $action )
		{
			if($action == 'enable' && $ids)
			{
				$_ids = array();
				$application_id = eventplanner_socalendar::get_instance()->read_single($ids[0], true)->application_id;

				$application = createObject('eventplanner.boapplication')->read_single($application_id);
				$params = array();
				$params['filters']['active'] = 1;
				$params['filters']['application_id'] = $application_id;

				$calendars =  eventplanner_socalendar::get_instance()->read($params);

				$existing_calendar_ids = array();
				foreach ($calendars['results'] as $calendar)
				{
					$existing_calendar_ids[] = $calendar['id'];
				}

				$number_of_active = (int)$calendars['total_records'];
				$limit = (int)$application->num_granted_events;

				$error = false;
				foreach ($ids as $id)
				{
					if(in_array($id, $existing_calendar_ids) )
					{
						continue;
					}
					if($limit > $number_of_active)
					{
						$_ids[] = $id;
						$number_of_active ++;
					}
					else
					{
						$error = true;
						$message = lang('maximum of granted events are reached');
						phpgwapi_cache::message_set($message, 'error');
						break;
					}
				}
				if($ids && !$_ids && !$error)
				{
					return true;
				}
			}
			else if ($action == 'delete' && $ids)
			{
				foreach ($ids as $id)
				{
					$booking_id = createObject('eventplanner.bobooking')->get_booking_id_from_calendar($id);
					$booking = eventplanner_sobooking::get_instance()->read_single($booking_id, true);
					if(!$booking->customer_id)
					{
						$_ids[] = $id;
					}
					else
					{
						$message = lang('can not delete calendar with customer');
						phpgwapi_cache::message_set($message, 'error');
					}
				}		
			}
			else
			{
				$_ids = $ids;
			}

			if($action == 'disconnect' && $_ids)
			{
				$mail_info = $this->create_disconnect_email($_ids);
			}

			$ret = eventplanner_socalendar::get_instance()->update_active_status($_ids, $action);

			if($ret && $action == 'disconnect')
			{
				$this->send_disconnect_email($mail_info);
			}

			return $ret;
		}

		function create_disconnect_email($ids)
		{
			$config = CreateObject('phpgwapi.config', 'eventplanner')->read();
			$sobooking = createObject('eventplanner.sobooking');
			$mail_info = array();
			foreach ($ids as $calendar_id)
			{
				$booking_id = $sobooking->get_booking_id_from_calendar( $calendar_id );
				$booking = $sobooking->read_single($booking_id, true);

				$customer = createObject('eventplanner.bocustomer')->read_single($booking->customer_id, true, $relaxe_acl = true);
				$customer_name	=$customer->name;

				$customer_contact_name = $booking->customer_contact_name;
				$customer_contact_email = $booking->customer_contact_email;
				$customer_contact_phone = $booking->customer_contact_phone;
				$location = $booking->location;

				$calendar = createObject('eventplanner.bocalendar')->read_single($calendar_id, true, $relaxe_acl = true);
				$from_ = $GLOBALS['phpgw']->common->show_date($calendar->from_);
				$to_ = $GLOBALS['phpgw']->common->show_date($calendar->to_);

				$application = createObject('eventplanner.boapplication')->read_single($calendar->application_id, true, $relaxe_acl = true);
	//			_debug_array($application);
	//			_debug_array($application);

				$vendor_name = $application->vendor_name;
				$vendor_contact_name = $application->contact_name;
				$vendor_contact_email = $application->contact_email;
				$vendor_contact_phone = $application->contact_phone;

				$subject = !empty($config['canceled_subject']) ? $config['canceled_subject'] : $event_title;
				$event_title = $application->title;

				$lang_when = lang('when');
				$lang_where = lang('where');

				$body  = <<<HTML
					<h2>{$event_title}</h2>
					<table>
						<tr>
							<td>
								{$lang_when}:
							</td>
							<td>
								{$from_} - {$to_}
							</td>
						</tr>
						<tr>
							<td>
								{$lang_where}:
							</td>
							<td>
								{$location}
							</td>
						</tr>
					</table>
HTML;

				$lang_vendor = lang('vendor');
				$lang_customer = lang('customer');
				$lang_contact_info = lang('contact info');

				$body .= <<<HTML
				<table border='1' class='pure-table pure-table-bordered pure-table-striped'>
					<thead>
						<tr>
							<th colspan="2" align = "left">
								{$lang_contact_info}
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<b>{$lang_vendor}</b>
							</td>
							<td>
								{$vendor_name}
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								{$vendor_contact_name}
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								{$vendor_contact_email}
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								{$vendor_contact_phone}
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>

							</td>
						</tr>
						<tr>
							<td>
								<b>{$lang_customer}</b>
							</td>
							<td>
								{$customer_name}
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								{$customer_contact_name}
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								{$customer_contact_email}
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								{$customer_contact_phone}
							</td>
						</tr>

					</tbody>
				</table>
HTML;

				$vendor_receipt_text = !empty($config['vendor_canceled_text']) ? $config['vendor_canceled_text'] : null;

				if($vendor_receipt_text)
				{

				//	$lang_vendor_note = lang('vendor note');
					$body .= <<<HTML
					{$vendor_receipt_text}
HTML;

				}
				$customer_receipt_text = !empty($config['customer_canceled_text']) ? $config['customer_canceled_text'] : null;

				if($customer_receipt_text)
				{

				//	$lang_customer_note = lang('customer note');
					$body .= <<<HTML
					{$customer_receipt_text}
HTML;

				}

				$content = <<<HTML
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		{$body}
	</body>
</html>
HTML;

//echo $content; die();
		/**
			 * Vendor
			 */
				$cc = $customer_contact_email;
				$bcc = !empty($config['receipt_blind_copy']) ? $config['receipt_blind_copy'] : '';
				$to_email = $vendor_contact_email;
				$from_email = !empty($config['receipt_blind_copy']) ? $config['receipt_blind_copy'] : $customer_contact_email;
				$from_name = !empty($config['receipt_blind_copy']) ? $config['receipt_blind_copy'] : $customer_contact_name;

				$mail_info[] =  array(
					'to_email' => $to_email,
					'subject' => $subject,
					'content' => stripslashes($content),
					'cc'		=> $cc,
					'bcc' => $bcc,
					'from_email' => $from_email,
					'from_name' => $from_name,
				);

			
			}

			return $mail_info;
		}


		private function send_disconnect_email($mail_info)
		{
			$send = CreateObject('phpgwapi.send');
			foreach ($mail_info as $entry)
			{
				try
				{
					$rcpt = $send->msg('email', $entry['to_email'], $entry['subject'], $entry['content'], '', $entry['cc'], $entry['bcc'], $entry['from_email'], $entry['from_name'], 'html');
				}
				catch (phpmailerException $e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
				}

				phpgwapi_cache::message_set("Email: $to_email, $cc", 'message');
			}
		}

		public function update_schedule( $id, $from_ )
		{
			$calendar = eventplanner_socalendar::get_instance()->read_single($id, true);
			$calendar->from_ = $from_;
			$calendar->process_update = true;

			if($calendar->validate())
			{
				return $calendar->store();
			}
			return false;
		}
	}