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
				$subject = "Söknad #{$application[id]} är registrerad";
			else
				$subject = "Söknad #{$application[id]} uppdaterad";
			$link = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction'=>'bookingfrontend.uiapplication.show', 'id'=>$application['id'], 'secret'=>$application['secret']));
			$link = str_replace('&amp;', '&', $link);
			$body = "Klicka på länken nedan för att titta på söknaden:\r\n\n\r$link";
			$send->msg('email', $application['contact_email'], $subject, $body, '', '', '', 'jonas@borgstrom.se', 'Bergen Booking', 'plain');
		}

	}
