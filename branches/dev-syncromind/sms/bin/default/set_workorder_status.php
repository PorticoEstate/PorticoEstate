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

	$status_code = array
	(
		1 => 'utført',
		2 => 'ikke_tilgang',
		3 => 'i_arbeid',
	);

	$param 		= explode(' ', $command_param);

	if (ctype_digit($param[0]) && ctype_digit($param[1]))
	{
		$workorder_id 	= $param[0];
		if(	$status	= $status_code[$param[1]])
		{
			$this->db->query("SELECT project_id, status FROM fm_workorder WHERE id='{$workorder_id}'",__LINE__,__FILE__);
			if($this->db->next_record())
			{
				$project_id = (int)$this->db->f('project_id');
				$status_old = $this->db->f('status');
				$this->db->query("UPDATE fm_workorder SET status = '{$status}' WHERE id='{$workorder_id}'" ,__LINE__,__FILE__);
				$historylog	= CreateObject('property.historylog','workorder');
	// temporary - fix this
				$historylog->account = 6;
				$historylog->add('S',$workorder_id,$status, $status_old);
				$historylog->add('RM',$workorder_id,'Status endret av: ' . $sms_sender);
				
				if(in_array($param[1],array(1,3)))
				{
					$this->db->query("SELECT status FROM fm_project WHERE id='{$project_id}'",__LINE__,__FILE__);
					$this->db->next_record();
					$status_old = $this->db->f('status');
					if($status_old != 'i_arbeid')
					{
						$this->db->query("UPDATE fm_project SET status = 'i_arbeid' WHERE id='{$project_id}'" ,__LINE__,__FILE__);
						$historylog_project	= CreateObject('property.historylog','project');
						$historylog_project->account = 6;
						$historylog_project->add('S',$project_id,'i_arbeid', $status_old);
						$historylog_project->add('RM',$project_id,"Bestilling {$workorder_id} endret av: {$sms_sender}");
					}
				}

//				execMethod('property.soworkorder.check_project_status',$workorder_id);

				$project_status_on_last_order_closed = 'utført';

				$this->db->query("SELECT count(id) AS orders_at_project FROM fm_workorder WHERE project_id= {$project_id}", __LINE__, __FILE__);
				$this->db->next_record();
				$orders_at_project = (int) $this->db->f('orders_at_project');

				$this->db->query("SELECT count(fm_workorder.id) AS closed_orders_at_project"
				. " FROM fm_workorder"
				. " {$this->join} fm_workorder_status ON (fm_workorder.status = fm_workorder_status.id)"
				. " WHERE project_id= {$project_id}"
				. " AND (fm_workorder_status.closed = 1 OR fm_workorder_status.delivered = 1)", __LINE__, __FILE__);

				$this->db->next_record();
				$closed_orders_at_project = (int) $this->db->f('closed_orders_at_project');

				$this->db->query("SELECT fm_project_status.closed AS closed_project, fm_project.status as old_status"
				. " FROM fm_project"
				. " {$this->join} fm_project_status ON (fm_project.status = fm_project_status.id)"
				. " WHERE fm_project.id= {$project_id}", __LINE__, __FILE__);

				$this->db->next_record();
				$closed_project	 = !!$this->db->f('closed_project');
				$old_status		 = $this->db->f('old_status');

				if($status == 'utført'
					&& $orders_at_project == $closed_orders_at_project
					&& $old_status != $project_status_on_last_order_closed)
				{
					$this->db->query("UPDATE fm_project SET status = '{$project_status_on_last_order_closed}' WHERE id= {$project_id}", __LINE__, __FILE__);

					$historylog_project = CreateObject('property.historylog', 'project');

					$historylog_project->add('S', $project_id, $project_status_on_last_order_closed, $old_status);
					$historylog_project->add('RM', $project_id, 'Status endret ved at siste bestilling er satt til utført');
				}
			
				$command_output = 'success';
			}
		}
	}
