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

		function send_notification( $application, $created = false, $assocciated = false )
		{
			if (!(isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server']))
			{
				return;
			}

			$send = CreateObject('phpgwapi.send');

			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
			$external_site_address = !empty($config->config_data['external_site_address'])? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			$subject = $config->config_data['application_mail_subject'];


//			if ($GLOBALS['phpgw_info']['server']['webserver_url'] != '' && isset($config->config_data['external_site_address']))
//			{
//				$link = $external_site_address . $GLOBALS['phpgw_info']['server']['webserver_url'] . '/bookingfrontend/?menuaction=bookingfrontend.uiapplication.show&id=' . $application['id'] . '&secret=' . $application['secret'];
//
//			}
//			else
			{
				$link = $external_site_address . '/bookingfrontend/?menuaction=bookingfrontend.uiapplication.show&id=' . $application['id'] . '&secret=' . $application['secret'];
			}

			if ($created)
			{
				$body = "<pre>" . $config->config_data['application_mail_created'] . "</pre>";
				$body .= '<p><a href="' . $link . '">Link til ' . $config->config_data['application_mail_systemname'] . ': søknad #' . $application['id'] . '</a></p>';
			}
			elseif ($application['status'] == 'PENDING')
			{
				$body = "<p>Din søknad i " . $config->config_data['application_mail_systemname'] . " om leie/lån er " . lang($application['status']) . '</p>';
				if ($application['comment'] != '')
				{
					$body .= '<p>Kommentar fra saksbehandler:<br />' . nl2br($application['comment']) . '</p>';
				}
				$body .= "<pre>" . $config->config_data['application_mail_pending'] . "</pre>";
				$body .= '<p><a href="' . $link . '">Link til ' . $config->config_data['application_mail_systemname'] . ': søknad #' . $application['id'] . '</a></p>';
			}
			elseif ($application['status'] == 'ACCEPTED')
			{
				// Sigurd:
				// Check if any bookings, allocations or events are associated with this application
				$assoc_bo = new booking_boapplication_association();
				$associations = $assoc_bo->so->read(array('filters' => array('application_id' => $application['id']),
					'sort' => 'from_', 'dir' => 'asc'));
				$_adates = array();

				foreach ($associations['results'] as $assoc)
				{
					if ($assoc['active'])
					{
						$_adates[] = "\t{$assoc['from_']} - {$assoc['to_']}";
					}
				}

				$adates = implode("\n", $_adates);

				//FIXME Sigurd 2. sept 2015: Something wrong with this one;
//				$rejected = $this->so->get_rejected($application['id']);
				$rejected = array();
				$rdates = "";
				foreach ($rejected as $key => $date)
				{
					if ($key === 0)
					{
						$rdates .= implode(" - ", $date) . "\n";
					}
					else
					{
						$rdates .= "\t" . implode(" - ", $date) . "\n";
					}
				}

				$body = "<p>Din søknad i " . $config->config_data['application_mail_systemname'] . " om leie/lån er " . lang($application['status']) . '</p>';
				if ($application['comment'] != '')
				{
					$body .= "<p>Kommentar fra saksbehandler:<br />" . nl2br($application['comment']) . "</p>";
				}
				$body .= '<pre>' . $config->config_data['application_mail_accepted'] . '<br /><a href="' . $link . '">Link til ' . $config->config_data['application_mail_systemname'] . ': søknad #' . $application['id'] . '</a></pre>';
				if ($adates)
				{
					$body .= "<pre>Godkjent:\n" . $adates . "</pre>";
				}
				if ($rdates)
				{
					$body .= "<pre>Avvist: " . $rdates . "</pre>";
				}


				if (isset($config->config_data['application_notify_on_accepted']) && $config->config_data['application_notify_on_accepted'] == 1)
				{
					$buildingemail = $this->so->get_tilsyn_email($application['building_name']);
					if ($buildingemail['email1'] != '' || $buildingemail['email2'] != '' || $buildingemail['email3'] != '')
					{
						$resourcename = implode(",", $this->get_resource_name($application['resources']));
						$bsubject = $config->config_data['application_mail_subject'] . ": En søknad om leie/lån av " . $resourcename . " på " . $application['building_name'] . " er godkjent";
						$bbody = "<p>" . $application['contact_name'] . " sin søknad  om leie/lån av " . $resourcename . " på " . $application['building_name'] . "</p>";
//						$bbody .= "<p>Den ".$adates."er Godkjent</p>";
						if ($adates)
						{
							$body .= "<pre>Godkjent:\n" . $adates . "</pre>";
						}

						$bbody .= "<p><b>{$config->config_data['application_equipment']}:</b><br />" . $application['equipment'] . "</p>";

						foreach ($buildingemail as $bemail)
						{
							try
							{
								$send->msg('email', $bemail, $bsubject, $bbody, '', '', '', $from, '', 'html');
							}
							catch (Exception $e)
							{
								// TODO: Inform user if something goes wrong
							}
						}
					}
				}
			}
			elseif ($application['status'] == 'REJECTED')
			{
				$body = "<p>Din søknad i " . $config->config_data['application_mail_systemname'] . " om leie/lån er " . lang($application['status']) . '</p>';
				if ($application['comment'] != '')
				{
					$body .= '<p>Kommentar fra saksbehandler:<br />' . nl2br($application['comment']) . '</p>';
				}
				$body .= '<pre>' . $config->config_data['application_mail_rejected'] . ' <a href="' . $link . '">Link til ' . $config->config_data['application_mail_systemname'] . ': søknad #' . $application['id'] . '</a></pre>';
			}
			else
			{
				$subject = $config->config_data['application_comment_mail_subject'];
				$body = "<pre><p>" . $config->config_data['application_comment_added_mail'] . "</p>";
				$body .= '<p>Kommentar fra saksbehandler:<br />' . nl2br($application['comment']) . '</p></pre>';
				$body .= '<p><a href="' . $link . '">Link til ' . $config->config_data['application_mail_systemname'] . ': søknad #' . $application['id'] . '</a></p>';
			}
			$body .= "<p>" . $config->config_data['application_mail_signature'] . "</p>";

			try
			{
				$send->msg('email', $application['contact_email'], $subject, $body, '', '', '', $from, '', 'html');
			}
			catch (Exception $e)
			{
				// TODO: Inform user if something goes wrong
			}
		}

		/**
		 * @ Send message about comment on application to case officer.
		 */
		function send_admin_notification( $application, $message = null )
		{
			if (!(isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server']))
			{
//				return;
			}
			$send = CreateObject('phpgwapi.send');

			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();

			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			$subject = $config->config_data['application_comment_mail_subject_caseofficer'];

			$mailadresses = $config->config_data['emails'];
			$mailadresses = explode("\n", $mailadresses);

			if ($GLOBALS['phpgw_info']['server']['webserver_url'] != '' && isset($config->config_data['external_site_address']))
			{
				$link = $external_site_address . $GLOBALS['phpgw_info']['server']['webserver_url'] . '/index.php?menuaction=booking.uiapplication.show&id=' . $application['id'];
			}
			else
			{
				$link = $external_site_address . '/index.php?menuaction=booking.uiapplication.show&id=' . $application['id'];
			}

			$activity = $this->activity_bo->read_single($application['activity_id']);

			if (strlen($application['customer_organization_number']) == 9)
			{
				$orgid = $this->organization_bo->so->get_orgid($application['customer_organization_number']);
				$organization = $this->organization_bo->read_single($orgid);
				$body = '<b>Kommentar fra ' . $organization['name'] . '</b><br />' . $message . '<br /><br/>';
			}
			else
			{
				$body = '<b>Kommentar fra ' . $application['contact_name'] . '</b><br />' . $message . '<br /><br/>';
			}

			$body .= '<b>Bygg: </b>' . $application['building_name'] . '<br />';
			$body .= '<b>Aktivitet: </b>' . $activity['name'] . '<br /><br />';
			$body .= '<b>Kontaktperson:</b> ' . $application['contact_name'] . '<br />';
			$body .= '<b>Epost:</b> ' . $application['contact_email'] . '<br />';
			$body .= '<b>Telefon:</b> ' . $application['contact_phone'] . '<br /><br />';
			$body .= '<a href="' . $link . '">Lenke til søknad</a><br /><br />';

			foreach ($mailadresses as $adr)
			{
				try
				{
					$send->msg('email', $adr, $subject, $body, '', '', '', $from, '', 'html');
				}
				catch (Exception $e)
				{
					// TODO: Inform user if something goes wrong
					$GLOBALS['phpgw']->log->error(array(
						'text'	=> 'booking_boapplication::send_admin_notification() : error when trying to send email. Error: %1',
						'p1'	=> $e->getMessage(),
						'line'	=> __LINE__,
						'file'	=> __FILE__
					));
				}
			}
		}

		/**
		 * Returns an array of application ids from applications assocciated with buildings
		 * which the given user has access to
		 *
		 * @param int $user_id
		 * @param int $building_id
		 */
		public function accessable_applications( $user_id, $building_id )
		{
			$applications = array();
			$this->db = & $GLOBALS['phpgw']->db;

			$filtermethod = array();

			$filtermethod[] = '1=1';

			if($user_id)
			{
				$filtermethod[] = "pe.subject_id = {$user_id}";
			}
			if($building_id)
			{
				$filtermethod[] = "bu.id = {$building_id}";
			}

			$sql = "SELECT DISTINCT ap.id"
				. " FROM bb_application ap"
				. " INNER JOIN bb_application_resource ar ON ar.application_id = ap.id"
				. " INNER JOIN bb_building_resource br ON br.resource_id = ar.resource_id"
				. " INNER JOIN bb_building bu ON bu.id = br.building_id"
				. " INNER JOIN bb_permission pe ON pe.object_id = bu.id and pe.object_type = 'building'"
				. " WHERE " . implode(' AND ', $filtermethod);

			$this->db->query($sql);
			$result = $this->db->resultSet;

			foreach ($result as $r)
			{
				$applications[] = $r['id'];
			}

			return $applications;
		}

		public function read_dashboard_data( $for_case_officer_id = array(null, null) )
		{
			$params = $this->build_default_read_params();

			if (!isset($params['filters']))
				$params['filters'] = array();
			$where_clauses = !isset($params['filters']['where']) ? array() : (array)$params['filters']['where'];

			if (!is_null($for_case_officer_id[0]))
			{
				$where_clauses[] = "(%%table%%.display_in_dashboard = 1 AND %%table%%.case_officer_id = " . intval($for_case_officer_id[1]) . ')';
			}
			else
			{
				$where_clauses[] = "(%%table%%.display_in_dashboard = 1)";
//				$where_clauses[] = "(%%table%%.case_officer_id = " . intval($for_case_officer_id[1]) . ')';
			}

			if ($building_id = phpgw::get_var('filter_building_id', 'int', 'REQUEST', 0))
			{
				$where_clauses[] = "(%%table%%.id IN ("
					. " SELECT DISTINCT a.id"
					. " FROM bb_application a, bb_application_resource ar, bb_resource r, bb_building_resource br "
					. " WHERE ar.application_id = a.id AND ar.resource_id = r.id AND br.resource_id =r.id  AND br.building_id = " . intval($building_id) . "))";
			}

			if ($status = phpgw::get_var('status') != '')
			{
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