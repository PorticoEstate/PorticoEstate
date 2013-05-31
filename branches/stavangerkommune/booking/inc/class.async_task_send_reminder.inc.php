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

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			foreach($bookings as $booking)
			{
				$booking = $this->booking_bo->read_single($booking['id']);
				$building = $this->building_bo->read_single($booking['building_id']);

				$body = $this->create_body_text($booking['from_'], $booking['to_'], $building['name'], $booking['group_name'], $booking['id'], $booking['secret'], 'booking', $external_site_address);
				$subject = 'Rapporter deltakertall';

				$this->db->query("select distinct name, email from bb_group_contact where trim(email) <> '' and group_id = ".$booking['group_id']);
				$contacts = $this->db->resultSet;
				foreach($contacts as $contact) 
				{
					try
					{
						$this->send->msg('email', $contact['email'], $subject, $body, '', '', '', $from, '', 'plain');
						
						// status set to 'sent, not responded to'
						$sql = "update bb_booking set reminder = 3 where id = ".$booking['id'];
						$this->db->query($sql);
					} 
					catch (phpmailerException $e)
					{
						// do nothing. nowhere to log or display error messages
					}
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

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			foreach($events as $event)
			{
				$event = $this->event_bo->read_single($event['id']);
				$building_info = $this->event_bo->so->get_building_info($event['id']);
				$building = $this->building_bo->read_single($building_info['id']);

				$body = $this->create_body_text($event['from_'], $event['to_'], $building['name'], '', $event['id'], $event['secret'], 'event', $external_site_address);
				$subject = 'Rapporter deltakertall';
				try
				{
					$this->send->msg('email', $event['contact_email'], $subject, $body, '', '', '', $from, '', 'plain');
					
					// status set to 'sent, not responded to'
					$sql = "update bb_event set reminder = 3 where id = ".$event['id'];
					$this->db->query($sql);
				}
				catch (phpmailerException $e)
				{
					// do nothing. nowhere to log or display error messages
				}
			}
		}

		private function create_body_text($from, $to, $where, $who, $id, $secret, $type, $external_site_address)
		{
			$body = "Informasjon om kommende arrangement:\n";
			$body .= "Hvor: %WHERE%\n";
			$body .= "Når:  %WHEN%\n";
			if (strlen($who) > 0)
			{
				$body .= "Hvem: %WHO%\n";
			}
			$body .= "\nStavanger kommune fører statistikk på bruk av lokaler og ber derfor om at dere \n";
			$body .= "\netter arrangementet melder inn korrekt deltakertall til oss.\n";
			$body .= "Du kan gjøre dette ved å klikke på linken nedenfor.\n\n%URL%";

			$body = str_replace('%URL%', $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.ui'.$type.'.report_numbers&id='.$id.'&secret='.$secret, $body);
			$body = str_replace('%WHO%', $who, $body);
			$body = str_replace('%WHERE%', $where, $body);
			$body = str_replace('%WHEN%', pretty_timestamp($from).' - '.pretty_timestamp($to), $body);

			return $body;
		}
	}
