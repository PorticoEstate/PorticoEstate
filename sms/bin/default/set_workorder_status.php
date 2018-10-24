<?php
	/**
	 * phpGroupWare - sms: A SMS Gateway
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package sms
	 * @subpackage sms
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package sms
	 */

	$interlink = CreateObject('property.interlink');
	$historylog_tts = CreateObject('property.historylog', 'tts');
	$botts = CreateObject('property.botts');

	$status_code = array
		(
		1 => 'utført',
		2 => 'ikke_tilgang',
		3 => 'i_arbeid',
	);

	$param = explode(' ', str_ireplace(array("\n", 'status '), array('', ''), $command_param));
//	_debug_array($param);

	for ($i = 0; $i < count($param); $i++)
	{
		$order_index = $i;
		$status_index = $i +1;

		if (ctype_digit($param[$order_index]) && ctype_digit($param[$status_index]))
		{
			$workorder_id = $param[$order_index];
			if ($status = $status_code[$param[$status_index]])
			{
				$this->db->query("SELECT project_id, status FROM fm_workorder WHERE id='{$workorder_id}'", __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$project_id = (int)$this->db->f('project_id');
					$status_old = $this->db->f('status');
					$this->db->query("UPDATE fm_workorder SET status = '{$status}' WHERE id='{$workorder_id}'", __LINE__, __FILE__);
					$historylog = CreateObject('property.historylog', 'workorder');
					// temporary - fix this
					$historylog->account = 6;
					$historylog->add('S', $workorder_id, $status, $status_old);
					$historylog->add('RM', $workorder_id, 'Status endret av: ' . $sms_sender);

					if (in_array($param[$status_index], array(1, 3)))
					{
						$this->db->query("SELECT status FROM fm_project WHERE id='{$project_id}'", __LINE__, __FILE__);
						$this->db->next_record();
						$status_old = $this->db->f('status');
						if ($status_old != 'i_arbeid')
						{
							$this->db->query("UPDATE fm_project SET status = 'i_arbeid' WHERE id='{$project_id}'", __LINE__, __FILE__);
							$historylog_project = CreateObject('property.historylog', 'project');
							$historylog_project->account = 6;
							$historylog_project->add('S', $project_id, 'i_arbeid', $status_old);
							$historylog_project->add('RM', $project_id, "Bestilling {$workorder_id} endret av: {$sms_sender}");
						}

						//				execMethod('property.soworkorder.check_project_status',$workorder_id);

						$project_status_on_last_order_closed = 'utført';

						$this->db->query("SELECT count(id) AS orders_at_project FROM fm_workorder WHERE project_id= {$project_id}", __LINE__, __FILE__);
						$this->db->next_record();
						$orders_at_project = (int)$this->db->f('orders_at_project');

						$this->db->query("SELECT count(fm_workorder.id) AS closed_orders_at_project"
							. " FROM fm_workorder"
							. " JOIN fm_workorder_status ON (fm_workorder.status = fm_workorder_status.id)"
							. " WHERE project_id= {$project_id}"
							. " AND (fm_workorder_status.closed = 1 OR fm_workorder_status.delivered = 1)", __LINE__, __FILE__);

						$this->db->next_record();
						$closed_orders_at_project = (int)$this->db->f('closed_orders_at_project');

						$this->db->query("SELECT fm_project_status.closed AS closed_project, fm_project.status as old_status"
							. " FROM fm_project"
							. " JOIN fm_project_status ON (fm_project.status = fm_project_status.id)"
							. " WHERE fm_project.id= {$project_id}", __LINE__, __FILE__);

						$this->db->next_record();
						$closed_project = !!$this->db->f('closed_project');
						$old_status = $this->db->f('old_status');

						if ($status == 'utført' && $orders_at_project == $closed_orders_at_project && $old_status != $project_status_on_last_order_closed)
						{
							$this->db->query("UPDATE fm_project SET status = '{$project_status_on_last_order_closed}' WHERE id= {$project_id}", __LINE__, __FILE__);

							$historylog_project = CreateObject('property.historylog', 'project');

							$historylog_project->add('S', $project_id, $project_status_on_last_order_closed, $old_status);
							$historylog_project->add('RM', $project_id, 'Status endret ved at siste bestilling er satt til utført');
						}

						/**
						 * Avslutte meldinger som er relatert til bestillinger som settes til utført
						 * @param type $project_id
						 * @param type $workorder_id
						 */
						if($status_id == 1)
						{
							$origin_data = $interlink->get_relation('property', '.project.workorder', $workorder_id, 'origin');
							$origin_data = array_merge($origin_data, $interlink->get_relation('property', '.project', $project_id, 'origin'));

							$tickets = array();
							foreach ($origin_data as $__origin)
							{
								if($__origin['location'] != '.ticket')
								{
									continue;
								}

								foreach ($__origin['data'] as $_origin_data)
								{
									$tickets[] = (int)$_origin_data['id'];
								}
							}

							$note_closed = "Meldingen er automatisk avsluttet fra bestilling som er satt til utført";

							foreach ($tickets as $ticket_id)
							{
								$this->db->query("SELECT status, cat_id, finnish_date, finnish_date2 FROM fm_tts_tickets WHERE id='$ticket_id'", __LINE__, __FILE__);
								$this->db->next_record();

								/**
								 * Oppdatere kun åpne meldinger
								 */

								$ticket_status = $this->db->f('status');
								$ticket_category = $this->db->f('cat_id');
								if ($ticket_status == 'X' || $ticket_category == 34) // klargjøring (48)
								{
									continue;
								}

								$botts->update_status( array('status' => 'X'), $ticket_id );
								$historylog_tts->add('C', $ticket_id, $note_closed);
							}

						}
					}

					$command_output = 'success';
				}
			}
		}

		$i++;
	}