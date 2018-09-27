<?php
	/*
	 * This class will update finnish date in ticket where tickets are linked to project.
	 */

	class update_ticket_on_project_change extends property_boproject
	{

		function __construct()
		{
			parent::__construct();
			if ($this->acl_location != '.project')
			{
				throw new Exception("'update_ticket_on_project_change' is intended for location = '.project'");
			}
			$this->historylog = CreateObject('property.historylog', 'tts');
			$this->botts = CreateObject('property.botts');
			$this->db = & $GLOBALS['phpgw']->db;
			$this->like = & $this->db->like;
			$this->join = & $this->db->join;
			$this->left_join = & $this->db->left_join;
		}

		public function check_values( $project, $values_attribute )
		{
			if (isset($project['id']) && $project['id'])
			{
				$origin = $this->interlink->get_relation('property', '.project', $project['id'], 'origin');
			}
			else if (isset($project['origin_data']) && is_array($project['origin_data']))
			{
				$origin = $project['origin_data'];
			}
			else
			{
				$origin = array();
			}
			$ids = array();
			foreach ($origin as $_origin)
			{
				if ($_origin['location'] == '.ticket')
				{
					foreach ($_origin['data'] as $data)
					{
						$ids[] = $data['id'];
					}
				}
			}


			foreach ($ids as $id)
			{
				$this->update_ticket($id, $project, $values_attribute);
			}
		}

		private function update_ticket( $id, $project, $values_attribute )
		{

			$this->db->query("SELECT status, cat_id, finnish_date, finnish_date2 FROM fm_tts_tickets WHERE id='$id'", __LINE__, __FILE__);
			$this->db->next_record();

			/**
			 * Oppdatere kun åpne meldinger
			 */

			$status = $this->db->f('status');
			$ticket_category = $this->db->f('cat_id');
			if ($status == 'X')
			{
				return;
			}

			$oldfinnish_date = (int)$this->db->f('finnish_date');
			$oldfinnish_date2 = (int)$this->db->f('finnish_date2');

			/*
			 * Check for multiple projects
			 * Related projects for the ticket
			 */
			$related  = $this->interlink->get_relation('property', '.ticket', $id, 'target');

			$related_projects = array();
			foreach ($related as $entry)
			{
				if($entry['location'] == ".project")
				{
					$related_projects = $entry['data'];
				}

			}

			if(empty($related_projects))
			{
				return;
			}

			$date_info = array();
			$project_is_closed = false;
			$do_not_close_ticket =false;

			foreach ($related_projects as $_project)
			{
				$_project_id = (int)$_project['id'];

				$this->db->query("SELECT account_group, location_code, end_date, fm_project_status.closed"
					. " FROM fm_project"
					. " JOIN fm_project_status ON fm_project.status = fm_project_status.id"
					. " WHERE fm_project.id={$_project_id}", __LINE__, __FILE__);
				$this->db->next_record();
				$finnish_date = $this->db->f('end_date');
				$project_is_closed = $this->db->f('closed');
				$skip_finnish_date = false;
				if(!$finnish_date)
				{
					if($project_is_closed)
					{
						$skip_finnish_date = true;
					}
					else
					{
						continue;
					}
				}
				$account_group = $this->db->f('account_group');
				$location_code = $this->db->f('location_code');

				$_finnish_date = $finnish_date;
				$note = 'FerdigDato er automatisk til prosjekt sluttDato';

				if ($ticket_category == 34) // klargjøring (48)
				{
					$do_not_close_ticket = true;
					//search for 2 working day delay
					for ($i = 2; $i < 10; $i++)
					{
						$finnish_date = $_finnish_date + (86400 * $i);
						$working_days = phpgwapi_datetime::get_working_days($_finnish_date, $finnish_date);
						if ($working_days == 2)
						{
							$note = 'FerdigDato er automatisk oppdatert til 2 virkedager etter prosjekt sluttDato';
							break;
						}
					}
				}
				else if($project_is_closed)//check if ticket should be closed
				{
					$note_closed = "Meldingen er automatisk avsluttet fra prosjekt";
				}

				if(!$skip_finnish_date)
				{
					$date_info[$finnish_date] = array
					(
						'note' => $note,
						'finnish_date' => $finnish_date,
						'location_code' => $location_code
					);
				}
			}

			/**
			 * Max date in front
			 */

			krsort($date_info);

			$date_info_keys = array_keys($date_info);

			$finnish_date = (int)$date_info_keys[0];
			if ($finnish_date)
			{
				$update = false;

				if ($oldfinnish_date && $finnish_date && $oldfinnish_date2 != $finnish_date)
				{
					$this->db->query("UPDATE fm_tts_tickets SET finnish_date2='{$finnish_date}' WHERE id='{$id}'", __LINE__, __FILE__);
					$old_value = $oldfinnish_date2;
					$update = true;
				}
				else if (!$oldfinnish_date && $finnish_date && $oldfinnish_date != $finnish_date)
				{
					$this->db->query("UPDATE fm_tts_tickets SET finnish_date='{$finnish_date}' , finnish_date2='{$finnish_date}' WHERE id='{$id}'", __LINE__, __FILE__);
					$old_value = $oldfinnish_date;
					$update = true;
				}

				if ($update)
				{
					$fields_updated = array('finnish_date');
					$this->historylog->add('F', $id, $finnish_date, $old_value);
					$this->historylog->add('C', $id, $date_info[$finnish_date]['note']);
					$this->botts->mail_ticket($id, $fields_updated, $receipt = array(), $date_info[$finnish_date]['location_code'], false, true);
					phpgwapi_cache::message_set(lang('finnish date changed'), 'message');
				}
			}

			if($project_is_closed && !$do_not_close_ticket)
			{
				$this->botts->update_status( array('status' => 'X'), $id );
				$this->historylog->add('C', $id, $note_closed);
			}
		}
	}
	$trigger = new update_ticket_on_project_change();
	$trigger->check_values($project, $values_attribute);

