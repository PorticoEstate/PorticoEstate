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
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_sostandard_2
	{

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->_db 			= & $GLOBALS['phpgw']->db;
			$this->_like		= & $this->_db->like;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query']) ? $data['query'] : '';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order']) ? $data['order'] : '';
				$type		= isset($data['type']) ? $data['type'] : '';
				$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
			}

			$standard = array();
			if (!$table = $this->select_table($type))
			{
				return $standard;
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY id ASC';
			}

			if($query)
			{
				$query = $this->_db->db_addslashes($query);

				$querymethod = " WHERE id $this->_like '%$query%' OR descr $this->_like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->_db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->_db->num_rows();

			if(!$allrows)
			{
				$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->_db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->_db->next_record())
			{
				$standard[] = array
				(
					'id'	=> $this->_db->f('id'),
					'descr'	=> $this->_db->f('descr')
				);
			}
			return $standard;
		}


		function select_table($type)
		{

			switch($type)
			{
				case 'workorder_status':
					$table='fm_workorder_status';
					break;
				case 'request_status':
					$table='fm_request_status';
					break;
				case 'agreement_status':
					$table='fm_agreement_status';
					break;
				case 'building_part':
					$table='fm_building_part';
					break;
				case 'document_status':
					$table='fm_document_status';
					break;
				case 'unit':
					$table='fm_standard_unit';
					break;
			}

			return $table;
		}


		function read_single($id,$type)
		{
			$standard = array();

			if (!$table = $this->select_table($type))
			{
				return $standard;
			}

			$sql = "SELECT * FROM $table WHERE id='{$id}'";

			$this->_db->query($sql,__LINE__,__FILE__);

			if ($this->_db->next_record())
			{
				$standard = array
				(
					'id'		=> $this->_db->f('id'),
					'descr'		=> $this->_db->f('descr', true)
				);
			}
			return $standard;
		}

		function add($standard,$type)
		{
			$receipt = array();
			if (!$table = $this->select_table($type))
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			$standard['descr'] = $this->_db->db_addslashes($standard['descr']);

			$this->_db->transaction_begin();

			$this->_db->query("INSERT INTO $table (id, descr) "
				. "VALUES ('" . $standard['id'] . "','" . $standard['descr']. "')",__LINE__,__FILE__);

			$this->_db->transaction_commit();
			$receipt['message'][] = array('msg' => lang('standard has been saved'));
			return $receipt;
		}

		function edit($standard,$type)
		{
			$receipt = array();
			if (!$table = $this->select_table($type))
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			$standard['descr'] = $this->_db->db_addslashes($standard['descr']);

			$this->_db->transaction_begin();

			$this->_db->query("UPDATE $table set descr='" . $standard['descr']
							. "' WHERE id='" . $standard['id']. "'",__LINE__,__FILE__);

			$this->_db->transaction_commit();
			$receipt['message'][] = array('msg' => lang('standard has been edited'));
			return $receipt;
		}

		function delete($id,$type)
		{
			$receipt = array();
			if (!$table = $this->select_table($type))
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			$this->_db->transaction_begin();
			$this->_db->query("DELETE FROM $table WHERE id='{$id}'",__LINE__,__FILE__);
			$this->_db->transaction_commit();
		}
	}

