<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boapplication extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
            $this->activity_bo = CreateObject('booking.boactivity');
            $this->organization_bo = CreateObject('booking.boorganization');
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
				$body = "<p>Din søknad i ".$config->config_data['application_mail_systemname']." om leie/lån er ".lang($application['status']); 
				$body .= "</p><pre>".$config->config_data['application_mail_pending']."</pre>";
				$body .= '<p><a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></p>';
				if ($application['comment'] != '') {
					$body .= '<p>Kommentar fra saksbehandler:<br />'.$application['comment'].'</p>';
				}
			} elseif ($application['status'] == 'ACCEPTED') {
				$accepted = $this->so->get_accepted($application['id']);				
				$adates = "";
				foreach ($accepted as $key => $date) {
						if($key === 0)
							$adates .= implode(" - ",$date)."\n";
						else						
							$adates .= "\t".implode(" - ",$date)."\n";
				}
				$rejected = $this->so->get_rejected($application['id']);				
				$rdates = "";
				foreach ($rejected as $key => $date) {
						if($key === 0)
							$rdates .= implode(" - ",$date)."\n";
						else						
							$rdates .= "\t".implode(" - ",$date)."\n";
				}

				$body = "<p>Din søknad i ".$config->config_data['application_mail_systemname']." om leie/lån er ".lang($application['status']); 
				$body .= '</p><pre>'.$config->config_data['application_mail_pending'].' <a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></pre>';
				$body .= "<pre>Godkjent: ".$adates."</pre>";
				$body .= "<pre>Avvist: ".$rdates."</pre>";

				if ($application['comment'] != '') {
					$body .= "<p>Kommentar fra saksbehandler:<br />".$application['comment']."</p>";
				}

				$buildingemail = $this->get_tilsyn_email($application['building_name']);
				if ($buildingemail['email1'] != '' || $buildingemail['email2'] != '' || $buildingemail['email3'] != '') {
					$resourcename = implode(",",$this->get_resource_name($application['resources']));
					$bsubject = "Aktivby: En søknad om leie/lån av ".$resourcename." på ".$application['building_name']." er godkjent";
					$bbody = "<p>".$application['contact_name']." sin søknad  om leie/lån av ".$resourcename." på ".$application['building_name']."</p>"; 
					$bbody .= "<p>Den ".$adates."er Godkjent</p>";
                    $bbody .= "<p><b>Ekstra informasjon fra søker:</b><br />".$application['equipment']."</p>";

					foreach ($buildingemail as $bemail)
					{
						try
						{
							$send->msg('email', $bemail, $bsubject, $bbody, '', '', '', $from, '', 'html');
						}
						catch (phpmailerException $e)
						{
						// TODO: Inform user if something goes wrong
						}

					}
				}
			} elseif ($application['status'] == 'REJECTED') {
				$body = "<p>Din søknad i ".$config->config_data['application_mail_systemname']." om leie/lån er ".lang($application['status']); 
				$body .= '</p><pre>'.$config->config_data['application_mail_rejected'].' <a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></pre>';
				if ($application['comment'] != '') {
					$body .= '<p>Kommentar fra saksbehandler:<br />'.$application['comment'].'</p>';
				}
			} else {
                $subject = $config->config_data['application_comment_mail_subject'];
                $body = "<pre><p>".$config->config_data['application_comment_added_mail']."</p>";
                $body .= '<p>Kommentar fra saksbehandler:<br />'.$application['comment'].'</p></pre>';
                $body .= '<p><a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].': søknad #'.$application['id'].'</a></p>';
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
         * @ Send message about comment on application to case officer.
         */
        function send_admin_notification($application, $message = null)
        {
            if (!(isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server']))
                return;
            $send = CreateObject('phpgwapi.send');

            $config = CreateObject('phpgwapi.config', 'booking');
            $config->read();

            $from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

            $external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

            $subject = $config->config_data['application_comment_mail_subject_caseofficer'];

            $mailadresses = $config->config_data['emails'];
            $mailadresses = explode("\n", $mailadresses);

            if ($GLOBALS['phpgw_info']['server']['webserver_url'] != '' && isset($config->config_data['external_site_address']))
                $link = $external_site_address . $GLOBALS['phpgw_info']['server']['webserver_url'] . '/index.php?menuaction=booking.uiapplication.show&id=' . $application['id'];
            else
                $link = $external_site_address . '/index.php?menuaction=booking.uiapplication.show&id=' . $application['id'];

            $activity = $this->activity_bo->read_single($application['activity_id']);

            if (strlen($application['customer_organization_number']) == 9) {
                $orgid = $this->organization_bo->so->get_orgid($application['customer_organization_number']);
                $organization = $this->organization_bo->read_single($orgid);
                $body = '<b>Kommentar fra ' . $organization['name'] . '</b><br />' . $message . '<br /><br/>';
            } else {
                $body = '<b>Kommentar fra '.$application['contact_name'].'</b><br />'.$message.'<br /><br/>';
            }

            $body .= '<b>Bygg: </b>'.$application['building_name'].'<br />';
            $body .= '<b>Aktivitet: </b>'.$activity['name'].'<br /><br />';
            $body .= '<b>Kontaktperson:</b> '.$application['contact_name'].'<br />';
            $body .= '<b>Epost:</b> '.$application['contact_email'].'<br />';
            $body .= '<b>Telefon:</b> '.$application['contact_phone'].'<br /><br />';
            $body .= '<a href="'.$link.'">Lenke til søknad</a><br /><br />';

            foreach ($mailadresses as $adr)
            {
                try
                {
                    $send->msg('email', $adr, $subject, $body, '', '', '', $from, '', 'html');
                }
                catch (phpmailerException $e)
                {
                    // TODO: Inform user if something goes wrong
                }
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
