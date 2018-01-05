<?php
	include_class('frontend', 'ticket', 'inc/model/');

	class frontend_boproperty
	{

		public function get_location_name( $location_code )
		{
			// Se på uthenting av adresser o.l. i leiemodulen
		}

		public function get_location_picture( $location_code )
		{
			return 'http://static04.vg.no/drfront/images/2010-03/02/86-55d4f606-80ed507a.jpeg';
		}

		public function get_ticket_details( $ticket_id )
		{
			/*
			 * Foreløpig opskrift:
			 * 1. Hent ut ticket
			 * 2. hent ut ticket history
			 * 3. Merge med dato for å få kronologisk rekkefølge
			 */
		}

		public static function get_tickets( $location_code )
		{
			$botts = CreateObject('property.botts');
			$botts->query = $location_code;
			$tickets = $botts->read();

			$ticketobjects = array();

			foreach ($tickets as $ticket)
			{
				$ticketobj = new frontend_ticket();
				$ticketobj->set_id($ticket['id']);
				$ticketobj->set_date($ticket['date']);
				$ticketobj->set_title($ticket['subject']);
				$ticketobj->set_user($ticket['user']);

				$ticketobjects[] = $ticketobj;
			}

			return $ticketobjects;
		}

		public function add_ticket( $location_code, $msg_title, $msg_text, $attachment )
		{

		}

		public function add_message( $ticket_id, $msg_text, $attachment )
		{

		}
	}
	$foo = new frontend_boproperty();
	$foo->get_tickets($location_code);
