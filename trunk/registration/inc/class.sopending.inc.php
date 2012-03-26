<?php
	/**
	* phpGroupWare - registration
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
	* @package registration
 	* @version $Id: class.solocation.inc.php 8572 2012-01-15 14:16:40Z sigurdne $
	*/

	/**
	 * Description
	 * @package registration
	 */

	class registration_sopending
	{

		var $bocommon;
		var $total_records;
		protected $global_lock = false;

		function __construct()
		{
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
		}

		public function read($data)
		{
			$start					= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$filter					= isset($data['filter']) && $data['filter'] ? $data['filter'] : 0;
			$query					= isset($data['query']) ? $data['query'] : '';
			$sort					= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'ASC';
			$order					= isset($data['order']) && $data['order'] ? $data['order'] : 'reg_id';
			$status_id				= isset($data['status_id']) && $data['status_id'] ? (int)$data['status_id'] : 0;
			$allrows				= isset($data['allrows']) ? $data['allrows'] : '';
			$results				= $data['results'] ? (int)$data['results'] : 0;

			$ordermethod = " ORDER BY {$order} {$sort}";

			$where= 'WHERE';
			$filtermethod = '';

			switch ($status_id)
			{
				case '1':
					$filtermethod .= "$where reg_approved = 1";
					$where= 'AND';
					break;
				case '2':
					$filtermethod .= "$where reg_approved IS NULL";
					$where= 'AND';
					break;
				default:
					// nothing
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = "{$where} reg_lid {$this->like} '%$query%'";
			}

			$sql = "SELECT * FROM phpgw_reg_accounts {$filtermethod} {$querymethod}";

			$values = array();
			$this->db->query('SELECT count(*) AS cnt ' . substr($sql,strripos($sql,' FROM')),__LINE__,__FILE__);
			$this->db->next_record();
			$this->total_records = $this->db->f('cnt');

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__,$results);
			}
			else
			{
				if($this->total_records > 200)
				{
					$_fetch_single = true;
				}
				else
				{
					$_fetch_single = false;
				}
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__, false, $_fetch_single );
				unset($_fetch_single);
			}

			$j=0;

			$values = array();

			while ($this->db->next_record())
			{
				$values[] = array
				(
					'reg_id'		=> $this->db->f('reg_id'),
					'reg_lid'		=> $this->db->f('reg_lid'),
					'reg_info'		=> $this->db->f('reg_info'),
					'reg_dla'		=> $this->db->f('reg_dla'),
					'reg_approved'	=> $this->db->f('reg_approved')
				); 

			}
			return $values;
		}

		public function approve_users($data)
		{
			$delete_approval = array();
			$add_approval = array();
			foreach($data['pending_users_orig'] as $id)
			{
				if(!in_array($id, $data['pending_users']))
				{
					$delete_approval[] = $id;
				}
			}

			foreach($data['pending_users'] as $id)
			{
				if(!in_array($id, $data['pending_users_orig']))
				{
					$add_approval[] = $id;
				}
			}

			$this->db->transaction_begin();
			foreach ($delete_approval as $reg_id)
			{
				$this->db->query("UPDATE phpgw_reg_accounts SET reg_approved = NULL WHERE reg_id = '{$reg_id}'",__LINE__,__FILE__);			
			}

			foreach ($add_approval as $reg_id)
			{
				$this->db->query("UPDATE phpgw_reg_accounts SET reg_approved = 1 WHERE reg_id = '{$reg_id}'",__LINE__,__FILE__);			
			}

			return $this->db->transaction_commit();
		}
	}
