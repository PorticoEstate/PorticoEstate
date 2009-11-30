<?php
	phpgw::import_class('booking.async_task');
	phpgw::import_class('booking.sobooking');
	phpgw::import_class('booking.bobooking');
	phpgw::import_class('booking.boevent');
	phpgw::import_class('booking.bogroup');
	phpgw::import_class('booking.bobuilding');
	phpgw::import_class('booking.uicommon');
	
	class booking_async_task_send_reminder extends booking_async_task
	{
		// this value should be the same as the crontab interval for this script
		// to be sure that the reminders are only sent once per event/booking
		const interval_length = '60'; // in minutes

		public function __construct()
		{
			parent::__construct();
			$this->db = & $GLOBALS['phpgw']->db;

			$this->booking_bo = CreateObject('booking.bobooking');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->event_bo = CreateObject('booking.boevent');
			$this->group_bo = CreateObject('booking.bogroup');

			$this->send = CreateObject('phpgwapi.send');
		}

		public function run($options = array())
		{
			$this->send_reminder_bookings();
			$this->send_reminder_events();
		}

		private function send_reminder_bookings()
		{
			$sql = "SELECT distinct bo.id FROM bb_booking bo 
				inner join bb_group_contact gc on gc.group_id = bo.group_id and trim(gc.email) <> ''
				where bo.from_ > now() and bo.from_ < (now() + INTERVAL '".self::interval_length." minutes')
				and bo.reminder = 1";
			$this->db->query($sql);
			$bookings = $this->db->resultSet;

			foreach($bookings as $booking)
			{
				$booking = $this->booking_bo->read_single($booking['id']);
				$building = $this->building_bo->read_single($booking['building_id']);

				$body = $this->create_body_text($booking['from_'], $booking['to_'], $building['name'], $booking['group_name'], $booking['id'], $booking['secret'], 'booking');
				$subject = 'Rapporter deltakertall';

				$this->db->query("select distinct name, email from bb_group_contact where trim(email) <> '' and group_id = ".$booking['group_id']);
				$contacts = $this->db->resultSet;
				foreach($contacts as $contact) 
				{
					$this->send->msg('email', $contact['email'], $subject, $body);
				}
			}
		}

		private function send_reminder_events()
		{
			$sql = "SELECT id from bb_event
				where from_ > now() and from_ < (now() + INTERVAL '".self::interval_length." minutes')
				and trim(contact_email) <> ''
				and reminder = 1";
			$this->db->query($sql);
			$events = $this->db->resultSet;


			foreach($events as $event)
			{
				$event = $this->event_bo->read_single($event['id']);
				$building_info = $this->event_bo->so->get_building_info($event['id']);
				$building = $this->building_bo->read_single($building_info['id']);

				$body = $this->create_body_text($event['from_'], $event['to_'], $building['name'], '', $event['id'], $event['secret'], 'event');
				$subject = 'Rapporter deltakertall';
				$this->send->msg('email', $event['contact_email'], $subject, $body);
			}
		}

		private function create_body_text($from, $to, $where, $who, $id, $secret, $type)
		{
			$body = "Informasjon om kommende arrangement:\n";
			$body .= "Hvor: %WHERE%\n";
			$body .= "Når:  %WHEN%\n";
			if (strlen($who) > 0)
			{
				$body .= "Hvem: %WHO%\n";
			}
			$body .= "\nVennlist oppgi korrekt deltakertall\n";
			$body .= "Du kan gjøre dette ved å klikke på linken nedenfor\n\n%URL%";

			// FIXME: Change url
			$body = str_replace('%URL%', 'http://bk.localhost/bookingfrontend/?menuaction=bookingfrontend.ui'.$type.'.report_numbers&id='.$id.'&secret='.$secret, $body);
			$body = str_replace('%WHO%', $who, $body);
			$body = str_replace('%WHERE%', $where, $body);
			$body = str_replace('%WHEN%', pretty_timestamp($from).' - '.pretty_timestamp($to), $body);

			return $body;
		}
	}
