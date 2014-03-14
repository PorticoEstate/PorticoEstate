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

	include_class('property', 'cron_parent', 'inc/cron/');

	class oppdater_namssakstatus_pr_leietaker extends property_cron_parent
	{

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('location');
			$this->function_msg	= 'Oppdatere namssaksstatus pr leietater';

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


		function execute()
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
		}
	}
