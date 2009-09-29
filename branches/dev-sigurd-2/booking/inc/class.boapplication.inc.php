<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boapplication extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soapplication');
		}

		function send_notification($application, $created=false)
		{
			if (!(isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server']))
				return;
			$send = CreateObject('phpgwapi.send');
			if($created)
				$subject = "Søknad #{$application[id]} er mottatt";
			else
				$subject = "Søknad #{$application[id]} endret/oppdatert";
			$link = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction'=>'bookingfrontend.uiapplication.show', 'id'=>$application['id'], 'secret'=>$application['secret']));
			$link = str_replace('&amp;', '&', $link);
			$body = "Klikk på linken under for å se på søknaden:\r\n\r\n$link";
			$send->msg('email', $application['contact_email'], $subject, $body, '', '', '', 'noreply@bergen.kommune.no', 'Bergen Booking', 'plain');
		}

	}
