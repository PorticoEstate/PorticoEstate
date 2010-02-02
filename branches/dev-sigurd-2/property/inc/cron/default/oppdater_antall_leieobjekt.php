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

	class oppdater_antall_leieobjekt
	{
		var	$function_name = 'oppdater_antall_leieobjekt';

		function oppdater_antall_leieobjekt()
		{
			$this->bocommon			= CreateObject('property.bocommon');
			$this->db 				= & $GLOBALS['phpgw']->db;
			$this->db2				= clone($this->db);
			$this->join				= $this->db->join;
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
				'lang_yes_statustext'	=> 'Oppdater antall leieobjekter for tv-anlegg',
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('location');
			$function_msg	= 'Oppdater antall leieobjekter for tv-anlegg';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{

			$this->db->transaction_begin();


			$sql = "SELECT kunde_nr_lev, fm_entity_1_3.loc1, fm_entity_1_3.loc2, Count(fm_location4.location_code) AS antall_leieobjekt, fm_entity_1_3.location_code
					FROM fm_entity_1_3 INNER JOIN fm_location4 ON (fm_entity_1_3.loc1 = fm_location4.loc1) AND (fm_entity_1_3.loc2 = fm_location4.loc2)
					WHERE fm_location4.category IN (1,2,3,4,6,10,14,15,17,22,23,24,25)
					GROUP BY kunde_nr_lev, fm_entity_1_3.loc1, fm_entity_1_3.loc2, fm_entity_1_3.location_code";

			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				if($this->db->f('kunde_nr_lev'))
				{
					$update[]= array(
					'kunde_nr_lev'	=>$this->db->f('kunde_nr_lev'),
					'antall_leieobjekt'	=>$this->db->f('antall_leieobjekt'),
					);
				}
			}

//_debug_array($update);

			for ($i=0; $i<count($update); $i++)
			{
				$this->db->query("UPDATE fm_entity_1_3 set ant_leil_pt =" . $update[$i]['antall_leieobjekt'] . " WHERE kunde_nr_lev= '" . $update[$i]['kunde_nr_lev'] . "'" ,__LINE__,__FILE__);
			}

			$this->receipt['message'][]=array('msg'=>'antall leieobjekter er oppdatert for tv-anlegg');

			unset($update);

			$this->db->transaction_commit();

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
	}

