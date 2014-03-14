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

				execMethod('property.soworkorder.check_project_status',$workorder_id);
				$command_output = 'success';
			}
		}
	}
