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
//				return;
			}

			$send = CreateObject('phpgwapi.send');

			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
			$reply_to = !empty($config->config_data['email_reply_to']) ? $config->config_data['email_reply_to'] : '';
			$external_site_address = !empty($config->config_data['external_site_address'])? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			$subject = $config->config_data['application_mail_subject'];


			$link = $external_site_address . '/bookingfrontend/?menuaction=bookingfrontend.uiapplication.show&id=' . $application['id'] . '&secret=' . $application['secret'];


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
					'sort' => 'from_', 'dir' => 'asc', 'results' =>'all'));
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
				if ($application['agreement_requirements'] != '')
				{
					$lang_additional_requirements = lang('additional requirements');
					$body .= "{$lang_additional_requirements}:<br />" . $application['agreement_requirements'] . "<br />";
				}
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

				phpgw::import_class('booking.sodocument');

				$where_filter = array();

				if(empty($application['building_id']))
				{
					$building_info = $this->so->get_building_info($application['id']);
					$application['building_id'] = $building_info['id'];
				}
				
				if($application['building_id'])
				{
					$where_filter[] = "(%%table%%.type='building' AND %%table%%.owner_id = {$application['building_id']})";
				}

				foreach ($application['resources'] as $resource_id)
				{
					$where_filter[] = "(%%table%%.type='resource' AND %%table%%.owner_id = {$resource_id})";
				}

				$regulations_params = array(
					'start' => 0,
					'sort'=> 'name',
					'filters' => array(
						'active' => 1,
						'category' => array(booking_sodocument::CATEGORY_REGULATION,
										booking_sodocument::CATEGORY_HMS_DOCUMENT,
										booking_sodocument::CATEGORY_PRICE_LIST),
						'where' => array('(' . join(' OR ', $where_filter) . ')')
					)
				);


				$sodocument_view = createObject('booking.sodocument_view');
				$files = $sodocument_view->read($regulations_params);
				$mime_magic	 = createObject('phpgwapi.mime_magic');
				$attachments = array();
				foreach ($files['results'] as $file)
				{
					$document = $sodocument_view->read_single($file['id']);
					$attachments[] = array
					(
						'file'	 => $document['filename'],
						'name'	 => basename($document['filename']),
						'type'	 => $mime_magic->filename2mime(basename($document['filename']))
					);
				}

				if (isset($config->config_data['application_notify_on_accepted']) && $config->config_data['application_notify_on_accepted'] == 1)
				{
					$buildingemail = $this->so->get_tilsyn_email($application['building_name']);
					if ($buildingemail['email1'] != '' || $buildingemail['email2'] != '' || $buildingemail['email3'] != '')
					{
						$resourcename = implode(",", $this->get_resource_name($application['resources']));
						$bsubject = $config->config_data['application_mail_subject'] . ": En søknad om leie/lån av " . $resourcename . " på " . $application['building_name'] . " er godkjent";
						$body = "<p>" . $application['contact_name'] . " sin søknad  om leie/lån av " . $resourcename . " på " . $application['building_name'] . "</p>";

						if ($adates)
						{
							$body .= "<pre>Godkjent:\n" . $adates . "</pre>";
						}

						if($application['equipment'] && $application['equipment'] != 'dummy')
						{
							$body .= "<p><b>{$config->config_data['application_equipment']}:</b><br />" . $application['equipment'] . "</p>";
						}

						foreach ($buildingemail as $bemail)
						{
							if(!$bemail)
							{
								continue;
							}

							try
							{
								$send->msg('email', $bemail, $bsubject, $body, '', '', '', $from, 'AktivKommune', 'html', '',array(), false, $reply_to);
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

			$building_info = $this->so->get_building_info($application['id']);

			$extra_mail_addresses = $this->get_mail_addresses( $building_info['id'], $application['case_officer_id'] );

			$mail_addresses = array();
			$cellphones = array();
			foreach ($extra_mail_addresses as $user_id => $extra_mail_addresse)
			{
				$prefs =CreateObject('phpgwapi.preferences',$user_id)->read();

				if(isset($prefs['booking']['notify_on_new']) && ($prefs['booking']['notify_on_new'] & 1))
				{
					$mail_addresses[] =  $prefs['common']['email'];
				}
				if(isset($prefs['booking']['notify_on_new']) && ($prefs['booking']['notify_on_new'] & 2))
				{
					$cellphones[] =  $prefs['common']['cellphone'];
				}
			}

			$bcc = implode(';', $mail_addresses);

			try
			{
				$send->msg('email', $application['contact_email'], $subject, $body, '', '', '', $from, 'AktivKommune', 'html', '',$attachments, false, $reply_to);
				if($bcc && $created)
				{

					/**
					 * Evil hack
					 */
					$enforce_ssl = $GLOBALS['phpgw_info']['server']['enforce_ssl'];
					$GLOBALS['phpgw_info']['server']['enforce_ssl'] = true;
					$link_backend =  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiapplication.show','id' => $application['id']), false, true, true);
					$GLOBALS['phpgw_info']['server']['enforce_ssl'] = $enforce_ssl;

					$new_body = "<h1>NB!! KOPI av epost til {$application['contact_email']}</h1>"
					. "$body"
					. "<br/>"
					. "<p>Forresten...:<br/>"
					. "<a href=\"{$link_backend}\">Link til søknad i backend</a></p>";

					$send->msg('email', $bcc, "KOPI::$subject", $new_body, '', '', '', $from, 'AktivKommune', 'html', '',array(), false);
				}
			}
			catch (Exception $e)
			{
				// TODO: Inform user if something goes wrong
			}

			if ($cellphones && $created)
			{
				try
				{
					$sms = CreateObject('sms.sms');
					$sms_message = "Ny søknad på {$application['building_name']}";
					foreach ($cellphones as $cellphone)
					{
						$sms->websend2pv($GLOBALS['phpgw_info']['user']['account_id'], $cellphone, $sms_message);
					}
				}
				catch (Exception $e)
				{
					// TODO: Inform user if something goes wrong
				}
			}
		}


		/**
		 *
		 * @param int $building_id
		 * @param int $user_id - the case officer, if any
		 * @return array
		 */
		function get_mail_addresses( $building_id, $user_id = 0 )
		{
			$roles_at_building = CreateObject('booking.sopermission_building')->get_roles_at_building($building_id);

			$users = array();

			foreach ($roles_at_building as $role)
			{
				$users[] = $role['user_id'];
			}

			if($user_id && !in_array($user_id, $users))
			{
				$users[] = $user_id;

			}

			$mail_addresses = array();
			foreach ($users as $user)
			{

				$prefs =CreateObject('phpgwapi.preferences',$user)->read();

				if(!empty($prefs['common']['email']))
				{
					$mail_addresses[$user] =  $prefs['common']['email'];
				}
			}

			return $mail_addresses;
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

			$external_site_address = !empty($config->config_data['external_site_address'])  ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			$subject = $config->config_data['application_comment_mail_subject_caseofficer'];

			$mailadresses = $config->config_data['emails'];
			$mailadresses = explode("\n", $mailadresses);


			$building_info = $this->so->get_building_info($application['id']);

			$extra_mail_addresses = $this->get_mail_addresses( $building_info['id'], $application['case_officer_id'] );

			if($mailadresses)
			{
				$mailadresses = array_merge($mailadresses, array_values($extra_mail_addresses));
			}
			else
			{
				$mailadresses = array_values($extra_mail_addresses);
			}

//			if ($GLOBALS['phpgw_info']['server']['webserver_url'] != '' && $external_site_address)
//			{
//				$link = $external_site_address . $GLOBALS['phpgw_info']['server']['webserver_url'] . '/index.php?menuaction=booking.uiapplication.show&id=' . $application['id'];
//			}
//			else
//			{
//				$link = $external_site_address . '/index.php?menuaction=booking.uiapplication.show&id=' . $application['id'];
//			}

			/**
			 * Evil hack
			 */
			$enforce_ssl = $GLOBALS['phpgw_info']['server']['enforce_ssl'];
			$GLOBALS['phpgw_info']['server']['enforce_ssl'] = true;
			$link =  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiapplication.show','id' => $application['id']), false, true, true);

			/**
			 * Text-version
			*/
			$link = str_replace('&amp;', '&', $link);

			$GLOBALS['phpgw_info']['server']['enforce_ssl'] = $enforce_ssl;


			$activity = $this->activity_bo->read_single($application['activity_id']);

			if (strlen($application['customer_organization_number']) == 9)
			{
				$orgid = $this->organization_bo->so->get_orgid($application['customer_organization_number']);
				$organization = $this->organization_bo->read_single($orgid);
				$body = '<b>Kommentar fra ' . $organization['name'] . '</b><br />' . $message . '<br /><br/>';
				$plain_text = "Kommentar fra {$organization['name']} \n{$message}\n";
			}
			else
			{
				$body = '<b>Kommentar fra ' . $application['contact_name'] . '</b><br />' . $message . '<br /><br/>';
				$plain_text = "Kommentar fra {$application['name']} \n{$message}\n";
			}

			$body .= '<b>Bygg: </b>' . $application['building_name'] . '<br />';
			$body .= '<b>Aktivitet: </b>' . $activity['name'] . '<br /><br />';
			$body .= '<b>Kontaktperson:</b> ' . $application['contact_name'] . '<br />';
			$body .= '<b>Epost:</b> ' . $application['contact_email'] . '<br />';
			$body .= '<b>Telefon:</b> ' . $application['contact_phone'] . '<br /><br />';
			$body .= '<a href="' . $link . '">Lenke til søknad</a><br /><br />';

			$plain_text .= "Bygg: {$application['building_name']}\n";
			$plain_text .= "Aktivitet: {$activity['name']}\n";
			$plain_text .= "Kontaktperson:{$application['contact_name']}\n";
			$plain_text .= "Epost: {$application['contact_email']}\n";
			$plain_text .= "Telefon: {$application['contact_phone']}\n";
			$plain_text .= "Lenke til søknad: {$link}\n";

			$html = <<<HTML
<!DOCTYPE html>
<html lang="no">
	<head>
		<meta charset="utf-8">
		<title>$subject</title>
	</head>
	<body>{$body}</body>
</html>
HTML;

			foreach ($mailadresses as $adr)
			{
				try
				{
					$send->msg('email', $adr, $subject, $plain_text, '', '', '', $from, 'AktivKommune', 'text');

					if($GLOBALS['phpgw_info']['flags']['currentapp'] == 'booking')
					{
						phpgwapi_cache::message_set("Epost er sendt til {$adr}");
					}
				}
				catch (Exception $e)
				{
					phpgwapi_cache::message_set("Epost feilet til {$adr}", 'error');
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


		function get_partials_list($session_id = '')
		{
			$list = array();
			if (!empty($session_id))
			{
				$filters = array('status' => 'NEWPARTIAL1', 'session_id' => $session_id);
				$params = array('filters' => $filters, 'results' =>'all');
				$applications = $this->so->read($params);
				$list = $applications;
			}
			return $list;
		}


		function delete_application($id)
		{
			$this->so->delete_application($id);
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