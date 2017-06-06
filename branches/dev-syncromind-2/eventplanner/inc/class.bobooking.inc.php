<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package eventplanner
	 * @subpackage booking
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
	phpgw::import_class('eventplanner.sobooking');

	include_class('eventplanner', 'booking', 'inc/model/');

	class eventplanner_bobooking extends phpgwapi_bocommon
	{

		protected static
			$bo,
			$fields,
			$acl_location;

		public function __construct()
		{
			$this->fields = eventplanner_booking::get_fields();
			$this->acl_location = eventplanner_booking::acl_location;
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
				self::$bo = new eventplanner_bobooking();
			}
			return self::$bo;
		}

		public function store( $object )
		{
			$save_last_booking = false;
			if (!$object->get_id())
			{
				$save_last_booking = true;
			}

			$this->store_pre_commit($object);
			$ret = eventplanner_sobooking::get_instance()->store($object);
			if ($ret && $save_last_booking)
			{
				phpgwapi_cache::system_set('eventplanner', "last_booking{$object->customer_id}", time());


				/**
				 * Send email receipt...
				 */
				$this->send_email($object);
			}

			$this->store_post_commit($object);
			return $ret;
		}

		public function send_email( $booking )
		{
			if (empty($GLOBALS['phpgw_info']['server']['smtp_server']))
			{
				phpgwapi_cache::message_set(lang('SMTP server is not set! (admin section)'), 'error');
				return false;
			}

			$config = CreateObject('phpgwapi.config', 'eventplanner')->read();
//			_debug_array($booking);

			$customer = createObject('eventplanner.bocustomer')->read_single($booking->customer_id, true, $relaxe_acl = true);
			$customer_name	=$customer->name;

			$customer_contact_name = $booking->customer_contact_name;
			$customer_contact_email = $booking->customer_contact_email;
			$customer_contact_phone = $booking->customer_contact_phone;
			$location = $booking->location;

			$calendar_id = $booking->calendar_id;
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
			$vendor_receipt_text = !empty($config['vendor_receipt_text']) ? $config['vendor_receipt_text'] : null;
			$customer_receipt_text = !empty($config['customer_receipt_text']) ? $config['customer_receipt_text'] : null;

			$subject = !empty($config['receipt_subject']) ? $config['receipt_subject'] : $event_title;
			$event_title = $application->title;

			$send = CreateObject('phpgwapi.send');

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


			$vendor_receipt_text = !empty($config['vendor_receipt_text']) ? $config['vendor_receipt_text'] : null;

			if($vendor_receipt_text)
			{

			//	$lang_vendor_note = lang('vendor note');
				$body .= <<<HTML
				{$vendor_receipt_text}
HTML;

			}
			$customer_receipt_text = !empty($config['customer_receipt_text']) ? $config['customer_receipt_text'] : null;

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

			try
			{
				$rcpt = $send->msg('email', $to_email, $subject, stripslashes($content), '', $cc, $bcc, $from_email, $from_name, 'html');
			}
			catch (phpmailerException $e)
			{
				phpgwapi_cache::message_set($e->getMessage(), 'error');
			}

			phpgwapi_cache::message_set("Email: $to_email, $cc", 'message');

			return $rcpt;
		}

		public function read( $params )
		{
			$status_text = array(lang('inactive'), lang('active'));
			if (empty($params['filters']['active']))
			{
				$params['filters']['active'] = 1;
			}
			else
			{
				unset($params['filters']['active']);
			}
			$values = eventplanner_sobooking::get_instance()->read($params);
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

		public function read_single( $id, $return_object = true )
		{
			if ($id)
			{
				$values = eventplanner_sobooking::get_instance()->read_single($id, $return_object);
			}
			else
			{
				$values = new eventplanner_booking();
			}

			return $values;
		}

		public function get_booking_id_from_calendar( $calendar_id )
		{
			return eventplanner_sobooking::get_instance()->get_booking_id_from_calendar($calendar_id);
		}
	}