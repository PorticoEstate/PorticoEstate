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

	class property_sostandard_3
	{
		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->socommon		= CreateObject('property.socommon');
			$this->_db 			= & $GLOBALS['phpgw']->db;

			$this->_join		= & $this->_db->join;
			$this->_like		= & $this->_db->like;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start']:0;
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$type		= isset($data['type']) ?$data['type']: '';
			}

			$standard = array();
			if (!$table = $this->select_table($type))
			{
				return $standard;
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by id asc';
			}

			if($query)
			{
				$query = $this->_db->db_addslashes($query);
				// FIXME: change fm_async_method.name to fm_async_method.num
				//$querymethod = " WHERE num $this->_like '%$query%' or descr $this->_like '%$query%'";
				$querymethod = " WHERE descr $this->_like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->_db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->_db->num_rows();
			$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);

			while ($this->_db->next_record())
			{
				$standard[] = array
				(
					'id'	=> $this->_db->f('id'),
					'num'	=> $this->_db->f('num', true),
					'descr'	=> $this->_db->f('descr',true)
				);
			}
			return $standard;
		}

		function select_table($type)
		{
			$table = '';
			switch($type)
			{
				case 'branch':
					$table='fm_branch';
					break;
				case 'key_location':
					$table='fm_key_loc';
					break;
				case 'async':
					$table='fm_async_method';
					break;
			}
			return $table;
		}

		function read_single($id, $type)
		{
			$id = (int) $id;
			$standard = array();

			if (!$table = $this->select_table($type))
			{
				return $standard;
			}

			$sql = "SELECT * FROM $table WHERE id={$id}";

			$this->_db->query($sql,__LINE__,__FILE__);

			if ($this->_db->next_record())
			{
				$standard = array
				(
					'id'		=> $this->_db->f('id'),
					'num'		=> $this->_db->f('num', true),
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

			$standard['num'] = $this->_db->db_addslashes($standard['num']);
			$standard['descr'] = $this->_db->db_addslashes($standard['descr']);

			$this->_db->transaction_begin();
			$standard['id'] = $this->socommon->next_id($table);
			$this->_db->query("INSERT INTO $table (id, num, descr) "
				. "VALUES ('" . $standard['id'] . "','" . $standard['num'] . "','" . $standard['descr']. "')",__LINE__,__FILE__);

			$this->_db->transaction_commit();
			$receipt['id'] = $standard['id'];
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

			$standard['num'] = $this->_db->db_addslashes($standard['num']);
			$standard['descr'] = $this->_db->db_addslashes($standard['descr']);

			$this->_db->transaction_begin();
			$this->_db->query("UPDATE $table set descr='" . $standard['descr'] . "', num='". $standard['num']
							. "' WHERE id='" . $standard['id']. "'",__LINE__,__FILE__);

			$this->_db->transaction_commit();

			$receipt['id'] = $standard['id'];
			$receipt['message'][] = array('msg' =>lang('standard has been edited'));
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
			$this->_db->query("DELETE FROM $table WHERE id=" . (int)$id ,__LINE__,__FILE__);
			$this->_db->transaction_commit();
		}
	}

