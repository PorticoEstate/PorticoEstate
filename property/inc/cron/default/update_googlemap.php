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

	class update_googlemap
	{
		var	$function_name = 'update_googlemap';

		function update_googlemap()
		{
			$this->bocommon			= CreateObject('property.bocommon');
			$this->db 				= & $GLOBALS['phpgw']->db;
			$this->db2				= clone($this->db);

			$this->join				= $this->db->join;
			$this->like				= $this->db->like;
			$this->left_join 		= " LEFT JOIN ";
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
			$function_msg	= lang('Export info as files');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{
			$this->update_location4();

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

		function update_location4()
		{
			$fieldname = 'googlemap';
			$area = "Bergen";
			$sql = "SELECT fm_location4.location_code,fm_location4.loc1,fm_location4.loc2,fm_location4.loc3,fm_location4.loc4,"
			 . " fm_location1.loc1_name,fm_tenant.id as tenant_id,fm_tenant.last_name,fm_tenant.first_name,fm_tenant.contact_phone,fm_streetaddress.descr as street_name,street_number,fm_location4.street_id,fm_location4.etasje,fm_location4.antallrom,fm_location4.boareal"
			 . " FROM ((((((( fm_location4 JOIN fm_location3 ON (fm_location4.loc3 = fm_location3.loc3) AND (fm_location4.loc2 = fm_location3.loc2) AND (fm_location4.loc1 = fm_location3.loc1)) JOIN fm_location2 ON (fm_location3.loc2 = fm_location2.loc2) AND (fm_location3.loc1 = fm_location2.loc1)) JOIN fm_location1 ON (fm_location2.loc1 = fm_location1.loc1)) JOIN fm_owner ON ( fm_location1.owner_id=fm_owner.id)) JOIN fm_part_of_town ON ( fm_location1.part_of_town_id=fm_part_of_town.part_of_town_id)) JOIN fm_streetaddress ON ( fm_location4.street_id=fm_streetaddress.id)) JOIN fm_tenant ON ( fm_location4.tenant_id=fm_tenant.id)) WHERE (fm_location4.category !=99 OR fm_location4.category IS NULL) AND driftsstatus_id > 0 ";

			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$sql2 = "UPDATE fm_location4 SET $fieldname ='http://maps.google.no/maps?&q=$area," . $this->db->f('street_name'). ',' . $this->db->f('street_number') ."' WHERE location_code = '" . $this->db->f('location_code') . "'";
//_debug_array($sql2);
				$this->db2->query($sql2,__LINE__,__FILE__);
			}
		}
	}


