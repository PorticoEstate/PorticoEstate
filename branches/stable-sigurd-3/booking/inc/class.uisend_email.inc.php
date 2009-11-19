<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('phpgwapi.send');

	class booking_uisend_email extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'receipt'		=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->season_bo = CreateObject('booking.boseason');
			$this->bo = CreateObject('booking.bobooking');
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->agegroup_bo = CreateObject('booking.boagegroup');
			$this->audience_bo = CreateObject('booking.boaudience');
			$this->group_bo    = CreateObject('booking.bogroup');
			self::set_active_menu('booking::mailing');
			$this->fields = array('allocation_id', 'activity_id', 'resources',
								  'building_id', 'building_name', 'application_id',
								  'season_id', 'season_name', 
			                      'group_id', 'group_name', 
			                      'from_', 'to_', 'audience', 'active', 'cost', 'reminder');
		}
		
		public function index()
		{
			$errors = array();
			$buildings = $this->building_bo->read();
			$seasons = $this->season_bo->read();
			$cancel_link = self::link(array('menuaction' => 'booking.uisend_email.index'));
			$step = 1;

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$step = phpgw::get_var('step', 'POST');
				$step++;
				$building =  phpgw::get_var('building', 'POST');
				$season =  phpgw::get_var('season', 'POST');
				$mailsubject =  phpgw::get_var('mailsubject', 'POST');
				$mailbody =  phpgw::get_var('mailbody', 'POST');
				$contacts = null;

				if ($step == 1)
				{
					if ($building == '' || $season == '' || $mailsubject == '' || $mailbody == '')
					{
						$errors['incomplete form'] = lang('All fields are required');
					}
					else
					{
						$contacts = $this->get_email_addresses($building, $season);
						$step++;
					}
				}
				elseif ($step == 2)
				{
					$contacts = $this->get_email_addresses($building, $season);
					$step++;
				}
				elseif ($step == 3)
				{
					$contacts = $this->get_email_addresses($building, $season);
					$this->send_emails($contacts, $mailsubject, $mailbody);
					$this->redirect(array('menuaction' => 'booking.uisend_email.receipt'));
				}
			}

			$this->flash_form_errors($errors);
			if ($step == 1)
				self::render_template('email_index', 
					array('buildings' => $buildings['results'], 
					'seasons' => $seasons['results'], 
					'building' => $building,
					'season' => $season,
					'mailsubject' => $mailsubject,
					'mailbody' => $mailbody,
					'step' => $step));

			if ($step == 2)
				self::render_template('email_preview', 
					array('building' => $building,
					'season' => $season,
					'mailsubject' => $mailsubject,
					'mailbody' => $mailbody,
					'contacts' => $contacts,
					'step' => $step));
		}

		public function receipt()
		{
			self::render_template('email_receipt');
		}

		private function send_emails($contacts, $subject, $body)
		{
			$send = CreateObject('phpgwapi.send');

			foreach($contacts as $contact)
			{
				$send->msg('email', $contact['email'], $subject, $body);
			}
		}

		private function get_email_addresses($building_id, $season_id)
		{
			$contacts = array();
			$db = & $GLOBALS['phpgw']->db;

			$sql = "SELECT DISTINCT oc.name, oc.email
				FROM bb_allocation alo
				INNER JOIN bb_organization_contact oc ON oc.organization_id = alo.organization_id AND trim(oc.email) <> ''
				INNER JOIN bb_season se ON se.id = alo.season_id AND se.active = 1
				INNER JOIN bb_building bu ON bu.id = se.building_id AND bu.active = 1
				WHERE alo.active = 1 
				AND se.id = $season_id
				AND bu.id = $building_id
				UNION
				SELECT DISTINCT gc.name, gc.email
				FROM bb_booking bo
				INNER JOIN bb_allocation alo ON alo.id = bo.allocation_id AND alo.active = 1
				INNER JOIN bb_season se ON se.id = alo.season_id AND se.active = 1
				INNER JOIN bb_building bu ON bu.id = se.building_id AND bu.active = 1
				INNER JOIN bb_group_contact gc ON gc.group_id = bo.group_id AND trim(gc.email) <> ''
				WHERE bo.active = 1 
				AND se.id = $season_id
				AND bu.id = $building_id
				ORDER BY name";
			$db->query($sql);

			$result = $db->resultSet;
			foreach($result as $c)
			{
				$contacts[] = array('email' => $c['email'], 'name' => $c['name']);
			}

			return $contacts;
		}
	}
