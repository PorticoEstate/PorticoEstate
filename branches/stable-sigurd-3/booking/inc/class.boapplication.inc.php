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

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			if($created)
				$subject = "Søknad #{$application[id]} er mottatt";
			else
				$subject = "Søknad #{$application[id]} endret/oppdatert";
			$link = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction'=>'bookingfrontend.uiapplication.show', 'id'=>$application['id'], 'secret'=>$application['secret']));
			$link = 'http://'. $external_site_address . $link;
			$link = str_replace('&amp;', '&', $link);
			$body = "Klikk på linken under for å se på søknaden:\r\n\r\n$link";

			try
			{
				$send->msg('email', $application['contact_email'], $subject, $body, '', '', '', $from, '', 'plain');
			}
			catch (phpmailerException $e)
			{
				// TODO: Inform user if something goes wrong
			}
		}
		
		/**
		* Returns an array of application ids from applications assocciated with buildings
		* which the given user has access to
		*
		* @param int $user_id
		*/
		public function accessable_applications($user_id)
		{
			$applications = array();
			$this->db = & $GLOBALS['phpgw']->db;

			$sql = "select distinct ap.id
					from bb_application ap
					inner join bb_application_resource ar on ar.application_id = ap.id
					inner join bb_resource re on re.id = ar.resource_id
					inner join bb_building bu on bu.id = re.building_id
					inner join bb_permission pe on pe.object_id = bu.id and pe.object_type = 'building'
					where pe.subject_id = ".$user_id;
			$this->db->query($sql);
			$result = $this->db->resultSet;

			foreach($result as $r)
			{
				$applications[] = $r['id'];
			}

			return $applications;
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
