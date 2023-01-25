<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('phpgwapi.send');

	class booking_uisend_email extends booking_uicommon
	{

		public $public_functions = array(
			'index'					 => true,
			'get_email_addresses'	 => true,
			'query'					 => true,
			'receipt'				 => true,
		);

		private $from;

		public function __construct()
		{
			parent::__construct();

			$config = CreateObject('phpgwapi.config', 'booking')->read();
			$this->from = isset($config['email_sender']) && $config['email_sender'] ? $config['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";


			self::set_active_menu('booking::mailing');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('booking') . "::" . lang('Send e-mail');
		}

		public function query()
		{
			
		}

		public function index()
		{
			$errors = array();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$building_id = phpgw::get_var('building_id', 'int');
				$building_name = phpgw::get_var('building_name', 'string');
				if (is_array(phpgw::get_var('seasons')))
				{
					$season = implode(',', phpgw::get_var('seasons'));
				}
				else
				{
					$season = phpgw::get_var('seasons');
				}
				$mailsubject = phpgw::get_var('mailsubject', 'string');
				$mailbody = phpgw::get_var('mailbody', 'html');
				$email_recipients = phpgw::get_var('email_recipients', 'string');

				if ($building_id == '' || $season == '' || $mailsubject == '' || $mailbody == '')
				{
					$errors['incomplete form'] = lang('All fields are required');
				}


				if (!$errors)
				{
					$_contacts = array();

					foreach ($email_recipients as $email_recipient)
					{
						$_contacts[] = array('email' => $email_recipient);
					}

					$result = $this->send_emails($_contacts, $mailsubject, $mailbody);
					self::redirect(array('menuaction' => 'booking.uisend_email.receipt',
						'ok' => count($result['ok']),
						'failed' => count($result['failed'])
					));
				}
				$building['id'] = $building_id;
				$building['name'] = $building_name;
			}

			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'base', 'email_send.js');
			phpgwapi_jquery::load_widget('autocomplete');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Send e-mail'), 'link' => '#building');
			$active_tab = 'generic';

			$building['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$building['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));
			phpgwapi_jquery::load_widget('bootstrap-multiselect');
			$this->rich_text_editor(array('field_mailbody'));
			self::render_template_xsl('email_index', array(
				'building'		 => $building,
				'season'		 => $season,
				'mailsubject'	 => $mailsubject,
				'mailbody'		 => $mailbody,
				'from'			 => $this->from,
				'html_editor'	 => $GLOBALS['phpgw_info']['user']['preferences']['common']['rteditor']
			));
		}

		public function receipt()
		{
			$ok_count = phpgw::get_var('ok');
			$fail_count = phpgw::get_var('failed');
			self::render_template_xsl('email_receipt', array('ok_count' => $ok_count, 'fail_count' => $fail_count));
		}

		private function send_emails( $contacts, $subject, $body )
		{
			$from = $this->from;

			$send = CreateObject('phpgwapi.send');
			$result = array('ok' => array(),'failed' => array());

			foreach ($contacts as $contact)
			{
				try
				{
					$send->msg('email', $contact['email'], $subject, $body, '', '', '', $from, 'AktivKommune', 'html');
					$result['ok'][] = $contact;
				}
				catch (Exception $e)
				{
					$result['failed'][] = $contact;
				}
			}
			return $result;
		}

		public function get_email_addresses()
		{
			$building_id = phpgw::get_var('building_id', 'int');
			$seasons = implode(',', phpgw::get_var('seasons', 'int'));

			$contacts = array();
			$db = & $GLOBALS['phpgw']->db;



			$sql = "SELECT from_ FROM public.bb_season WHERE bb_season.id IN($seasons) ORDER BY from_ ASC";
			$db->query($sql);
			$db->next_record();
			$from = $db->f('from_');

			$sql = "SELECT DISTINCT oc.name, oc.email
				FROM bb_allocation alo
				INNER JOIN bb_organization_contact oc ON oc.organization_id = alo.organization_id AND trim(oc.email) <> ''
				INNER JOIN bb_season se ON se.id = alo.season_id AND se.active = 1
				INNER JOIN bb_building bu ON bu.id = se.building_id AND bu.active = 1
				WHERE alo.active = 1 
				AND se.id in($seasons)
				AND bu.id = $building_id
				UNION
				SELECT DISTINCT gc.name, gc.email
				FROM bb_booking bo
				INNER JOIN bb_allocation alo ON alo.id = bo.allocation_id AND alo.active = 1
				INNER JOIN bb_season se ON se.id = alo.season_id AND se.active = 1
				INNER JOIN bb_building bu ON bu.id = se.building_id AND bu.active = 1
				INNER JOIN bb_group_contact gc ON gc.group_id = bo.group_id AND trim(gc.email) <> ''
				WHERE bo.active = 1
				AND se.id in($seasons)
				AND bu.id = $building_id
				UNION
				SELECT DISTINCT bb_event.contact_name as name, bb_event.contact_email as email
				FROM bb_event
				INNER JOIN bb_event_resource ON bb_event.id = bb_event_resource.event_id
				JOIN bb_season_resource ON bb_event_resource.resource_id = bb_season_resource.resource_id
				INNER JOIN bb_resource ON bb_resource.id = bb_season_resource.resource_id
				INNER JOIN bb_season ON bb_season.id = bb_season_resource.season_id
				WHERE bb_season.id in($seasons)
				AND bb_event.from_ > '$from'
				ORDER BY name";
				$db->query($sql);

			$result = $db->resultSet;

			$duplicates = array();

			foreach ($result as $c)
			{
				if(empty($c['email']))
				{
					continue;
				}
				if(!isset($duplicates[$c['email']]))
				{
					$contacts[] = array('email' => $c['email'], 'name' => $c['name']);
					$duplicates[$c['email']] = true;
				}
			}

			return $contacts;
		}
	}