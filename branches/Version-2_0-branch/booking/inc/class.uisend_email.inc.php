<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('phpgwapi.send');

	class booking_uisend_email extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'receipt' => true,
		);

		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('booking::mailing');
		}

		public function query()
		{
			
		}

		public function index()
		{
			$errors = array();
			$step = 1;

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$step = phpgw::get_var('step', 'int');
				$step++;
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
				$mailbody = phpgw::get_var('mailbody', 'string');
				$contacts = null;

				if ($step == 1)
				{
					if ($building_id == '' || $season == '' || $mailsubject == '' || $mailbody == '')
					{
						$errors['incomplete form'] = lang('All fields are required');
					}
					else
					{
						$contacts = $this->get_email_addresses($building_id, $season);
						$step++;
					}
				}
				elseif ($step == 2)
				{
					$contacts = $this->get_email_addresses($building_id, $season);
					$step++;
				}
				elseif ($step == 3)
				{
					$contacts = $this->get_email_addresses($building_id, $season);
					$result = $this->send_emails($contacts, $mailsubject, $mailbody);
					$this->redirect(array('menuaction' => 'booking.uisend_email.receipt',
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

			if ($step == 1)
				self::render_template_xsl('email_index', array('building' => $building,
					'season' => $season,
					'mailsubject' => $mailsubject,
					'mailbody' => $mailbody,
					'step' => $step));

			if ($step == 2)
				self::render_template_xsl('email_preview', array('building' => $building,
					'building_id' => $building_id,
					'season' => $season,
					'mailsubject' => $mailsubject,
					'mailbody' => $mailbody,
					'contacts' => $contacts,
					'step' => $step));
		}

		public function receipt()
		{
			$ok_count = phpgw::get_var('ok');
			$fail_count = phpgw::get_var('failed');
			self::render_template('email_receipt', array('ok_count' => $ok_count, 'fail_count' => $fail_count));
		}

		private function send_emails( $contacts, $subject, $body )
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			$send = CreateObject('phpgwapi.send');
			$result = array();

			foreach ($contacts as $contact)
			{
				try
				{
					$send->msg('email', $contact['email'], $subject, $body, '', '', '', $from, '', 'html');
					$result['ok'][] = $contact;
				}
				catch (phpmailerException $e)
				{
					$result['failed'][] = $contact;
				}
			}
			return $result;
		}

		private function get_email_addresses( $building_id, $season_id )
		{
			$contacts = array();
			$db = & $GLOBALS['phpgw']->db;

			$sql = "SELECT DISTINCT oc.name, oc.email
				FROM bb_allocation alo
				INNER JOIN bb_organization_contact oc ON oc.organization_id = alo.organization_id AND trim(oc.email) <> ''
				INNER JOIN bb_season se ON se.id = alo.season_id AND se.active = 1
				INNER JOIN bb_building bu ON bu.id = se.building_id AND bu.active = 1
				WHERE alo.active = 1 
				AND se.id in($season_id)
				AND bu.id = $building_id
				UNION
				SELECT DISTINCT gc.name, gc.email
				FROM bb_booking bo
				INNER JOIN bb_allocation alo ON alo.id = bo.allocation_id AND alo.active = 1
				INNER JOIN bb_season se ON se.id = alo.season_id AND se.active = 1
				INNER JOIN bb_building bu ON bu.id = se.building_id AND bu.active = 1
				INNER JOIN bb_group_contact gc ON gc.group_id = bo.group_id AND trim(gc.email) <> ''
				WHERE bo.active = 1 
				AND se.id in($season_id)
				AND bu.id = $building_id
				ORDER BY name";
			$db->query($sql);

			$result = $db->resultSet;
			foreach ($result as $c)
			{
				$contacts[] = array('email' => $c['email'], 'name' => $c['name']);
			}

			return $contacts;
		}
	}