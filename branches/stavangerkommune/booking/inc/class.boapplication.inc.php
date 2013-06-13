<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boapplication extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soapplication');
		}

		function send_notification($application, $created=false, $assocciated=false)
		{
			if (!(isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server']))
				return;
			$send = CreateObject('phpgwapi.send');

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			$subject = $config->config_data['application_mail_subject'];

			$link = $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.uiapplication.show&id='.$application['id'].'&secret='.$application['secret'];

			if ($created) {
				$body = "<pre>".$config->config_data['application_mail_created']."</pre>";
				$body .= '<p><a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></p>';

			} elseif ($application['status'] == 'PENDING') {
				$body = "<p>Din søknad i ".$config->config_data['application_mail_systemname']." om leie/lån er".lang($application['status']); 
				$body .= "<pre>".$config->config_data['application_mail_pending']."</pre>";
				$body .= '<p><a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></p>';
				if ($application['comment'] != '') {
					$body .= '<p>Kommentar fra saksbehandler:<br />'.$application['comment'].'</p>';
				}
			} elseif ($application['status'] == 'ACCEPTED') {
				$body = "<p>Din søknad i ".$config->config_data['application_mail_systemname']." om leie/lån er".lang($application['status']); 
				$body .= '<pre>'.$config->config_data['application_mail_pending'].' <a href="'.$link.'">Link til '.$config->config_data
['application_mail_systemname'].': søknad #'.$application['id'].'</a></pre>';
				if ($application['comment'] != '') {
					$body .= '<p>Kommentar fra saksbehandler:<br />'.$application['comment'].'</p>';
				}
				$buildingemail = $this->get_tilsyn_email($application['building_id']);
				if ($buildingemail != '') {
					$resourcename = implode(",",$this->get_resource_name($application['resources']));
					$dates = "";
					foreach ($application['dates'] as $date) {
						$dates .=implode(", ",$date)." ";
					}
					$bbody = "<p>".$application['contact_name']." sin søknad  om leie/lån av ".$resourcename." i ".$application[building_name]."</p>"; 
					$bbody .= "<p>Den ".$dates."er Godkjent</p>";
				
					try
					{
						$send->msg('email', $buildingemail, $subject, $bbody, '', '', '', $from, '', 'html');
					}
					catch (phpmailerException $e)
					{
					// TODO: Inform user if something goes wrong
					}
				}
		
			} elseif ($application['status'] == 'REJECTED') {
				$body = "<p>Din søknad i ".$config->config_data['application_mail_systemname']." om leie/lån er".lang($application['status']); 
				$body .= '<pre>'.$config->config_data['application_mail_rejected'].'<a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></pre>';
				if ($application['comment'] != '') {
					$body .= '<p>Kommentar fra saksbehandler:<br />'.$application['comment'].'</p>';
				}
			}
			$body .= "<p>".$config->config_data['application_mail_signature']."</p>";

			try
			{
				$send->msg('email', $application['contact_email'], $subject, $body, '', '', '', $from, '', 'html');
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

#			$sql = "select distinct ap.id
#					from bb_application ap
#					inner join bb_application_resource ar on ar.application_id = ap.id
#					inner join bb_resource re on re.id = ar.resource_id
#					inner join bb_building bu on bu.id = re.building_id";
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

		public function read_dashboard_data($for_case_officer_id = array(null,null)) {
			$params = $this->build_default_read_params();

			if (!isset($params['filters'])) $params['filters'] = array();
			$where_clauses = !isset($params['filters']['where']) ? array() : (array)$params['filters']['where'];
			
			if (!is_null($for_case_officer_id[0])) {
				$where_clauses[] = "(%%table%%.display_in_dashboard = 1 AND %%table%%.case_officer_id = ".intval($for_case_officer_id[1]).')';
			} else {
				$where_clauses[] = "(%%table%%.case_officer_id = ".intval($for_case_officer_id[1]).')';
			}

			
			if ($building_id = phpgw::get_var('filter_building_id', 'int', 'GET', false)) {
				$where_clauses[] = "(%%table%%.id IN (SELECT DISTINCT a.id FROM bb_application a, bb_application_resource ar, bb_resource r WHERE ar.application_id = a.id AND ar.resource_id = r.id AND r.building_id = ".intval($building_id)."))";
			}
			
			if ( $status = phpgw::get_var('status') != '') {
                    $params['filters']['status'] = phpgw::get_var('status');       
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
