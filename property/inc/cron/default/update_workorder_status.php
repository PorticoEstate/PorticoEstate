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

	class update_workorder_status extends property_cron_parent
	{
		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('workorder');
			$this->function_msg	= 'Manuell oppdatering av status';

			$this->bocommon			= CreateObject('property.bocommon');
			$this->db				= clone($GLOBALS['phpgw']->db);
			$this->date				=  1220245200;// unix timestamp 1. Sept 2008

die('er denne konfigurert?');

		}


		function execute()
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
