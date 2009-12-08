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
			$link = 'http://'.$_SERVER['HTTP_HOST'] . $link;
			$link = str_replace('&amp;', '&', $link);
			$body = "Klikk på linken under for å se på søknaden:\r\n\r\n$link";
			try
			{
				$send->msg('email', $application['contact_email'], $subject, $body);
			}
			catch (phpmailerException $e)
			{
				// TODO: Inform user if something goes wrong
			}
		}
		
		public function read_dashboard_data($for_case_officer_id = null) {
			$params = $this->build_default_read_params();
			
			if (!isset($params['filters'])) $params['filters'] = array();
			$where_clauses = !isset($params['filters']['where']) ? array() : (array)$params['filters']['where'];
			
			if (!is_null($for_case_officer_id)) {
				$where_clauses[] = "(%%table%%.display_in_dashboard = 1 AND %%table%%.case_officer_id = ".intval($for_case_officer_id).')';
			}
			
			if ($building_id = phpgw::get_var('filter_building_id', 'int', 'GET', false)) {
				$where_clauses[] = "(%%table%%.id IN (SELECT DISTINCT a.id FROM bb_application a, bb_application_resource ar, bb_resource r WHERE ar.application_id = a.id AND ar.resource_id = r.id AND r.building_id = ".intval($building_id)."))";
			}
			
			$params['filters']['where'] = $where_clauses;
			
			return $this->so->read($params);
		}

	}

	class booking_boapplication_association extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = new booking_soapplication_association();
		}
	}
