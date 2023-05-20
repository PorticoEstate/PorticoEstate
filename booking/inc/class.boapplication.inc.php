<?php
	phpgw::import_class('booking.bocommon');

	class booking_boapplication extends booking_bocommon
	{
		var $activity_bo,
		 $organization_bo;

		function __construct()
		{
			parent::__construct();
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->organization_bo = CreateObject('booking.boorganization');
			$this->so = CreateObject('booking.soapplication');
		}

		/*
		 * Used for external archive
		 */
		function get_export_text1( $application, $config)
		{
			$dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$resourcename = implode(", ", $this->get_resource_name($application['resources']));

			$_adates = array();

			foreach ($application['dates'] as $date)
			{
				$_adates[] = "\t" . date("$dateformat H:i:s", strtotime($date['from_'])) . " - " . date("$dateformat H:i:s", strtotime($date['to_']));
			}

			$adates = implode("\n", $_adates);

			$customer_name = !empty($application['customer_organization_name']) ? $application['customer_organization_name'] : $application['contact_name'];

			$start_date = reset($application['dates']);
			$start_date_formatted = date($dateformat, strtotime($start_date['from_']));
			$end_date = end($application['dates']);
			$end_date_formatted = date($dateformat, strtotime($end_date['to_']));

			if($start_date_formatted == $end_date_formatted)
			{
				$timespan = $start_date_formatted;
			}
			else
			{
				$timespan = "{$start_date_formatted} - {$end_date_formatted}";
			}

			$title = "Forespørsel om leie av {$application['building_name']}/{$resourcename} - {$timespan} - $customer_name";

			$body = "<p>" . $application['contact_name'] . " har søkt " . $config['application_mail_systemname'] . " om leie/lån av " . $resourcename . " på " . $application['building_name'] . '</p>';
			if ($application['agreement_requirements'] != '')
			{
				$lang_additional_requirements = lang('additional requirements');
				$body .= "{$lang_additional_requirements}:<br />" . $application['agreement_requirements'] . "<br />";
			}
			if ($adates)
			{
				$body .= "<pre>Tidsrom:\n" . $adates . "</pre><br/>";
			}

			if ($application['comment'] != '')
			{
				$body .= "<p>Kommentar fra saksbehandler:<br />" . ($application['comment']) . "</p>";
			}

			$attachments = $this->get_related_files($application);

			$file_names = array();
			foreach ($attachments as $attachment)
			{
				$file_names[] =  $attachment['name'];
			}

			if($file_names)
			{
				$body .= "<pre>Søker har kvittert for å ha lest vedlagte dokument(er):\n";
				$body .=  implode("\n", $file_names);
				$body .=  "</pre>";
			}

			return array
			(
				'title' => $title,
				'body'=> $body
			);

		}
		/*
		 * Used for external archive
		 */
		function get_export_text2( $application, $config )
		{

			$resourcename = implode(", ", $this->get_resource_name($application['resources']));

			$customer_name = !empty($application['customer_organization_name']) ? "{$application['customer_organization_name']}/{$application['contact_name']}" : $application['contact_name'];

			$dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$start_date = reset($application['dates']);
			$start_date_formatted = date($dateformat, strtotime($start_date['from_']));
			$end_date = end($application['dates']);
			$end_date_formatted = date($dateformat, strtotime($end_date['to_']));

			if($start_date_formatted == $end_date_formatted)
			{
				$timespan = $start_date_formatted;
			}
			else
			{
				$timespan = "{$start_date_formatted} - {$end_date_formatted}";
			}

			$title = "Svar på forespørsel om leie av {$application['building_name']}/{$resourcename} - {$timespan}  - $customer_name";

			if ($application['status'] == 'PENDING')
			{
				$body = "<p>" . $application['contact_name'] . " sin søknad i " . $config['application_mail_systemname'] . " om leie/lån av " . $resourcename . " på " . $application['building_name'] ." er " . strtolower(lang($application['status'])) . '</p>';
				if ($application['comment'] != '')
				{
					$body .= '<p>Kommentar fra saksbehandler:<br />' . $application['comment'] . '</p>';
				}
				$body .= "<pre>" . $config['application_mail_pending'] . "</pre>";
			}
			elseif ($application['status'] == 'ACCEPTED')
			{
				$assoc_bo = new booking_boapplication_association();
				$associations = $assoc_bo->so->read(array('filters' => array('application_id' => $application['id']),
					'sort' => 'from_', 'dir' => 'asc', 'results' =>'all'));
				$_adates = array();

				foreach ($associations['results'] as $assoc)
				{
					if ($assoc['active'])
					{
						$_adates[] = "\t" . date("$dateformat H:i:s", strtotime($assoc['from_'])) . " - " . date("$dateformat H:i:s", strtotime($assoc['to_']));
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

				$body = "<p>" . $application['contact_name'] . " sin søknad i " . $config['application_mail_systemname'] . " om leie/lån av " . $resourcename . " på " . $application['building_name'] ." er " . strtolower(lang($application['status'])) . '</p>';
				if ($application['agreement_requirements'] != '')
				{
					$lang_additional_requirements = lang('additional requirements');
					$body .= "{$lang_additional_requirements}:<br />" . $application['agreement_requirements'] . "<br />";
				}
				if ($application['comment'] != '')
				{
					$body .= "<p>Kommentar fra saksbehandler:<br />" . ($application['comment']) . "</p>";
				}
				$body .= "<p>{$config['application_mail_accepted']}</p>";
				if ($adates)
				{
					$body .= "<pre>Godkjent:\n" . $adates . "</pre>";
				}
				if ($rdates)
				{
					$body .= "<pre>Avvist: " . $rdates . "</pre>";
				}
			}
			elseif ($application['status'] == 'REJECTED')
			{

				$body = "<p>" . $application['contact_name'] . " sin søknad i " . $config['application_mail_systemname'] . " om leie/lån av " . $resourcename . " på " . $application['building_name'] ." er " . strtolower(lang($application['status'])) . '</p>';
				if ($application['comment'] != '')
				{
					$body .= '<p>Kommentar fra saksbehandler:<br />' . ($application['comment']) . '</p>';
				}
				$body .= '<pre>' . $config['application_mail_rejected'] . '</pre>';
			}

			$body .= "<p>" . $config['application_mail_signature'] . "</p>";

			return array
			(
				'title' => $title,
				'body'=> $body
			);

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

			$resourcename = implode(", ", $this->get_resource_name($application['resources']));

			$resources = CreateObject('booking.soresource')->read(array('filters' => array('id' => $application['resources']),
				'results'	 => 100));

			$bogeneric = createObject('booking.bogeneric');
			$e_lock_instructions = array();
			foreach ($resources['results'] as $resource)
			{
				if (!$resource['e_locks'])
				{
					continue;
				}

				foreach ($resource['e_locks'] as $e_lock)
				{
					if (!$e_lock['e_lock_system_id'] || !$e_lock['e_lock_resource_id'])
					{
						continue;
					}

					$lock_system = $bogeneric->read_single(
						array(
							'id'			 => $e_lock['e_lock_system_id'],
							'location_info'	 => array(
								'type' => 'e_lock_system'
							)
						)
					);

					$e_lock_instructions[] = $lock_system['instruction'];
				}
			}

			$subject = $config->config_data['application_mail_subject'];


			$link = $external_site_address . '/bookingfrontend/?menuaction=bookingfrontend.uiapplication.show&id=' . $application['id'] . '&secret=' . $application['secret'];


			$attachments = array();
			if ($created)
			{
				$body = "<p>" . $config->config_data['application_mail_created'] . "</p>";
				$body .= '<p><a href="' . $link . '">Link til ' . $config->config_data['application_mail_systemname'] . ': søknad #' . $application['id'] . '</a></p>';
			}
			elseif ($application['status'] == 'PENDING')
			{
				$body = "<p>Din søknad i " . $config->config_data['application_mail_systemname'] . " om leie/lån av " . $resourcename . " på " . $application['building_name']. " er " . lang($application['status']) . '</p>';
				if ($application['comment'] != '')
				{
					$body .= '<p>Kommentar fra saksbehandler:<br />' . $application['comment'] . '</p>';
				}
				$body .= "<p>" . $config->config_data['application_mail_pending'] . "</p>";
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

				$cost = 0;
				foreach ($associations['results'] as $assoc)
				{
					if ($assoc['active'])
					{
						$_adates[] = "\t{$assoc['from_']} - {$assoc['to_']}";
						$cost += (float)$assoc['cost'];
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

				$body = "<p>Din søknad i " . $config->config_data['application_mail_systemname'] . " om leie/lån av " . $resourcename . " på " . $application['building_name']. " er " . lang($application['status']) . '</p>';

				if ($adates)
				{
					$body .= "<pre>Godkjent tid:\n" . $adates . "</pre>";
					$body .= "<br />";
				}

				if($cost)
				{
					$body .= "<pre>Totalkostnad: kr " .  number_format($cost, 2, ",", '.') . "</pre>";
					$body .= "<br />";
				}

				if ($rdates)
				{
					$body .= "<pre>Avvist: " . $rdates . "</pre>";
					$body .= "<br />";
				}

				if ($application['agreement_requirements'] != '')
				{
					$lang_additional_requirements = lang('additional requirements');
					$body .= "{$lang_additional_requirements}:<br />" . $application['agreement_requirements'] . "<br />";
				}
				if ($application['comment'] != '')
				{
					$body .= "<p>Kommentar fra saksbehandler:<br />" . $application['comment'] . "</p>";
				}
				$body .= "<p>{$config->config_data['application_mail_accepted']}</p>"
					. "<br /><a href=\"{$link}\">Link til {$config->config_data['application_mail_systemname']}: søknad #{$application['id']}</a>";

				if($e_lock_instructions)
				{
					$body .= "\n" . implode("\n", $e_lock_instructions);
				}

				$attachments = $this->get_related_files($application);

				if (isset($config->config_data['application_notify_on_accepted']) && $config->config_data['application_notify_on_accepted'] == 1)
				{
					$buildingemail = $this->so->get_tilsyn_email($application['building_name']);
					if ($buildingemail['email1'] != '' || $buildingemail['email2'] != '' || $buildingemail['email3'] != '')
					{
						$subject_notify_on_accepted = $config->config_data['application_mail_subject'] . ": En søknad om leie/lån av " . $resourcename . " på " . $application['building_name'] . " er godkjent";
						$body_notify_on_accepted = "<p>" . $application['contact_name'] . " sin søknad  om leie/lån av " . $resourcename . " på " . $application['building_name'] . "</p>";

						if ($adates)
						{
							$body_notify_on_accepted .= "<pre>Godkjent:\n" . $adates . "</pre>";
						}

						if($application['equipment'] && $application['equipment'] != 'dummy')
						{
							$body_notify_on_accepted .= "<p><b>{$config->config_data['application_equipment']}:</b><br />" . $application['equipment'] . "</p>";
						}

						$_buildingemail = array_unique($buildingemail);
						foreach ($_buildingemail as $email_notify_on_accepted)
						{
							if(!$email_notify_on_accepted)
							{
								continue;
							}

							try
							{
								$send->msg('email', $email_notify_on_accepted, $subject_notify_on_accepted, $body_notify_on_accepted, '', '', '', $from, 'AktivKommune', 'html', '',array(), false, $reply_to);
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
				$body = "<p>Din søknad i " . $config->config_data['application_mail_systemname'] . " om leie/lån av " . $resourcename . " på " . $application['building_name']. " er " . lang($application['status']) . '</p>';
				if ($application['comment'] != '')
				{
					$body .= '<p>Kommentar fra saksbehandler:<br />' . $application['comment'] . '</p>';
				}
				$body .= '<p>' . $config->config_data['application_mail_rejected'] . ' <a href="' . $link . '">Link til ' . $config->config_data['application_mail_systemname'] . ': søknad #' . $application['id'] . '</a></p>';
			}
			else
			{
				$subject = $config->config_data['application_comment_mail_subject'];
				$body = "<p>" . $config->config_data['application_comment_added_mail'] . "</p>";
				$body .= '<p>Kommentar fra saksbehandler:<br />' . $application['comment'] . '</p>';
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
			$recipient = '';

			try
			{
				$rcpt = $send->msg('email', $application['contact_email'], $subject, $body, '', '', '', $from, 'AktivKommune', 'html', '',$attachments, false, $reply_to);

				if($rcpt && $GLOBALS['phpgw_info']['flags']['currentapp'] == 'booking')
				{
					$recipient = $application['contact_email'];
				}
			}
			catch (Exception $e)
			{
				phpgwapi_cache::message_set("Epost feilet for {$application['contact_email']}", 'error');
				phpgwapi_cache::message_set($e->getMessage(), 'error');

			}

			if($bcc && $created)
			{
				try
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
				catch (Exception $ex)
				{
					phpgwapi_cache::message_set("Epost feilet for {$application['contact_email']}", 'error');
					phpgwapi_cache::message_set($e->getMessage(), 'error');

				}
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

			return $recipient;
		}


		function get_related_files( $application )
		{
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

			return $attachments;

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

			if(!empty($mailadresses[0]))
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

			$_mailadresses = array_unique($mailadresses);
			foreach ($_mailadresses as $adr)
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
		 * Returns a sql-statement of application ids from applications assocciated with buildings
		 * which the given user has access to
		 *
		 * @param int $user_id
		 * @param int $building_id
		 * @return string $sql
		 */
		public function accessable_applications( $user_id, $building_id )
		{
			$filtermethod = array();

			$filtermethod[] = '1=1';

			$sql = "SELECT DISTINCT ap.id"
				. " FROM bb_application ap"
				. " INNER JOIN bb_application_resource ar ON ar.application_id = ap.id"
				. " INNER JOIN bb_building_resource br ON br.resource_id = ar.resource_id"
				. " INNER JOIN bb_building bu ON bu.id = br.building_id";

			if($user_id)
			{
				if(is_array($user_id))
				{
					$users = $user_id;
				}
				else
				{
					$users = array($user_id);
				}
				$filtermethod[] = "((pe.subject_id IN ( " . implode(',', $users) . ")"
					. " AND ap.case_officer_id IS NULL) OR  ap.case_officer_id IN ( " . implode(',', $users) . "))";
				$sql.= " INNER JOIN bb_permission pe ON pe.object_id = bu.id and pe.object_type = 'building'";
			}

			if($building_id)
			{
				$filtermethod[] = "bu.id = {$building_id}";
			}

			$sql.=  " WHERE " . implode(' AND ', $filtermethod);
			return $sql;
		}

		public function read_dashboard_data( $for_case_officer_id = array(null, null) )
		{
			$params = $this->build_default_read_params();

			if (!isset($params['filters']))
			{
				$params['filters'] = array();
			}
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
				$this->so->get_purchase_order($applications);
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

		var $so;
		function __construct()
		{
			parent::__construct();
			$this->so = new booking_soapplication_association();
		}
	}