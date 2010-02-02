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

	class oppdater_namssakstatus_pr_leietaker
	{
		var	$function_name = 'oppdater_namssakstatus_pr_leietaker';

		function oppdater_namssakstatus_pr_leietaker()
		{
			$this->bocommon			= CreateObject('property.bocommon');
			$this->db 				= & $GLOBALS['phpgw']->db;
			$this->db2				= clone($this->db);
			$this->join				= $this->db->join;
			$this->like				= $this->db->like;
			$this->left_join 		= " LEFT JOIN ";

			if(isset($this->db->adodb) && $this->db->adodb)
			{
				$this->db_boei           	= CreateObject('phpgwapi.db',false,$GLOBALS['external_db']['boei']['db_type']);
				$this->db_boei->Host     	= $GLOBALS['external_db']['boei']['db_host'];
				$this->db_boei->Type     	= $GLOBALS['external_db']['boei']['db_type'];
				$this->db_boei->Database 	= $GLOBALS['external_db']['boei']['db_name'];
				$this->db_boei->User     	= $GLOBALS['external_db']['boei']['db_user'];
				$this->db_boei->Password 	= $GLOBALS['external_db']['boei']['db_pass'];
				$this->db_boei->Halt_On_Error 	= 'yes';
				$this->db_boei->connect();
			}
			else
			{
				$this->db_boei           	= CreateObject('property.db_mssql');
				$this->db_boei->Host     	= $GLOBALS['external_db']['boei']['db_host'];
				$this->db_boei->Type     	= $GLOBALS['external_db']['boei']['db_type'];
				$this->db_boei->Database 	= $GLOBALS['external_db']['boei']['db_name'];
				$this->db_boei->User     	= $GLOBALS['external_db']['boei']['db_user'];
				$this->db_boei->Password 	= $GLOBALS['external_db']['boei']['db_pass'];
				$this->db_boei->Halt_On_Error 	= 'yes';
			}
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
				'lang_yes_statustext'	=> lang('Update the category to not active based on if there is only nonactive apartments'),
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('location');
			$function_msg	= 'Oppdatere namssaksstatus pr leietater';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{

			$receipt = $this->oppdater_namssakstatus();
			$this->cron_log($receipt,$cron);

			if(!$cron)
			{
				$this->confirm($execute=false);
			}

		}

		function cron_log($receipt='',$cron='')
		{

			$insert_values= array(
				$cron,
				date($this->bocommon->datetimeformat),
				$this->function_name,
				$receipt
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function oppdater_namssakstatus()
		{

			$sql = "SELECT TOP 100 PERCENT fm_tenant.id"
					. " FROM  fm_tenant LEFT OUTER JOIN"
                    . " v_Leietaker ON fm_tenant.id = v_Leietaker.leietaker_id AND "
                    . " fm_tenant.status_drift = v_Leietaker.namssakstatusdrift_id AND "
                    . " fm_tenant.status_eco = v_Leietaker.namssakstatusokonomi_id"
					. " WHERE (v_Leietaker.leietaker_id IS NULL)";

			$this->db_boei->query($sql,__LINE__,__FILE__);

			$this->db->transaction_begin();
			$this->db_boei->transaction_begin();

			while ($this->db_boei->next_record())
			{
				$leietaker[]= $this->db_boei->f('id');
			}

			for ($i=0; $i<count($leietaker); $i++)
			{
				$sql = "SELECT namssakstatusokonomi_id, namssakstatusdrift_id"
					. " FROM  v_Leietaker"
					. " WHERE (v_Leietaker.leietaker_id = '" . $leietaker[$i] . "')";

				$this->db_boei->query($sql,__LINE__,__FILE__);

				$this->db_boei->next_record();
				$leietaker_oppdatert[]= array (
				 'id' 				=> $leietaker[$i],
				 'status_drift'		=> $this->db_boei->f('namssakstatusdrift_id'),
				 'status_eco' 		=> $this->db_boei->f('namssakstatusokonomi_id')
				 );

			}

			for ($i=0; $i<count($leietaker_oppdatert); $i++)
			{
				$sql = " UPDATE fm_tenant SET "
				. " status_eco = '" . $leietaker_oppdatert[$i]['status_eco'] . "',"
				. " status_drift = '" . $leietaker_oppdatert[$i]['status_drift'] . "'"
				. " WHERE  id = '" . $leietaker_oppdatert[$i]['id'] . "'";

				$this->db->query($sql,__LINE__,__FILE__);
				$this->db_boei->query($sql,__LINE__,__FILE__);
			}

			$this->db->transaction_commit();
			$this->db_boei->transaction_commit();

			$msg = $i . ' namssakstatus er oppdatert';
			$this->receipt['message'][]=array('msg'=> $msg);
			return $msg;

		}
	}

