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
			$_finnish_date = (int)$project['end_date'];
			if (!$_finnish_date)
			{
				return;
			}

			$finnish_date = $_finnish_date;
			$note = 'FerdigDato er automatisk til prosjekt sluttDato';

			if ($project['b_account_id'] == 48) // klargjøring
			{
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

			$this->db->query("SELECT status, finnish_date, finnish_date2 FROM fm_tts_tickets WHERE id='$id'", __LINE__, __FILE__);
			$this->db->next_record();

			$status = $this->db->f('status');

			/**
			 * Kun oppdatere åpne meldinger
			 */
			if ($status == 'X')
			{
				return;
			}

			$oldfinnish_date = (int)$this->db->f('finnish_date');
			$oldfinnish_date2 = (int)$this->db->f('finnish_date2');

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
				$this->historylog->add('C', $id, $note);
				$this->botts->mail_ticket($id, $fields_updated, $receipt = array(), $project['location_code'], false, true);
				phpgwapi_cache::message_set(lang('finnish date changed'), 'message');
			}
		}
	}
	$trigger = new update_ticket_on_project_change();
	$trigger->check_values($project, $values_attribute);

