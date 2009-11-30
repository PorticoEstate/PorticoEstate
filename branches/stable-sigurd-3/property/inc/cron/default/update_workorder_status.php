<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class update_workorder_status
	{
		var	$function_name = 'update_workorder_status';

		function update_workorder_status()
		{
		//	$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->bocommon			= CreateObject('property.bocommon');
			$this->db				= clone($GLOBALS['phpgw']->db);
			$this->date				=  1220245200;// unix timestamp 1. Sept 2008
		}

		function pre_run($data='')
		{
			if($data['enabled']==1)
			{
				$confirm	= true;
				$cron		= true;
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= phpgw::get_var('execute', 'bool', 'GET');
			}

			if ($confirm)
			{
				$this->execute($cron);
			}
			else
			{
				$this->confirm($execute=false);
			}
		}
		function confirm($execute='')
		{
			$link_data = array
			(
				'menuaction' => 'property.custom_functions.index',
				'function'	=>$this->function_name,
				'execute'	=> $execute,
			);

			if(!$execute)
			{
				$lang_confirm_msg 	= lang('do you want to perform this action');
			}

			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> lang('Export info as files'),
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('location');
			$dateformat		= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$function_msg	= lang('close workorders older than %1', $GLOBALS['phpgw']->common->show_date($this->date,$dateformat));
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{
			$this->update_status();

			if(!$cron)
			{
				$this->confirm($execute=false);
			}

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$insert_values= array(
				$cron,
				date($this->bocommon->datetimeformat),
				$this->function_name,
				implode(',',(array_keys($msgbox_data)))
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function update_status()
		{
			set_time_limit(0);
			$sql = "SELECT id,status from fm_workorder WHERE entry_date < {$this->date} AND status !='closed'";
			$this->db->query($sql,__LINE__,__FILE__);

			$workorders = array();
			while ($this->db->next_record())
			{
				$workorders[] = array
				(
					'id'		=> $this->db->f('id'),
					'status'	=> $this->db->f('status')
				);
			}
			$sql = "SELECT id,status from fm_project WHERE entry_date < {$this->date} AND status !='closed'";
			$this->db->query($sql,__LINE__,__FILE__);

			$projects = array();
			while ($this->db->next_record())
			{
				$projects[] = array
				(
					'id'		=> $this->db->f('id'),
					'status'	=> $this->db->f('status')
				);
			}

			$GLOBALS['phpgw']->db->transaction_begin();
			
			$historylog	= CreateObject('property.historylog','workorder');
			foreach ($workorders as $workorder)
			{
				$historylog->add('S',$workorder['id'], 'closed');	
			}

			unset($historylog);

			$historylog	= CreateObject('property.historylog','project');
			foreach ($projects as $project)
			{
				$historylog->add('S',$project['id'], 'closed');	
			}

			if($GLOBALS['phpgw']->db->transaction_commit())
			{
				$this->db->transaction_begin();

				$sql = "UPDATE fm_workorder SET status = 'closed' WHERE entry_date < {$this->date} AND status !='closed'";
				$this->db->query($sql,__LINE__,__FILE__);

				$sql = "UPDATE fm_project SET status = 'closed' WHERE entry_date < {$this->date} AND status !='closed'";
				$this->db->query($sql,__LINE__,__FILE__);
		
				$this->db->transaction_commit();			
			}
		}
	}
