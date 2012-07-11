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

	class property_soasync
	{
		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query']) ? $data['query'] : '';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
				$order		= isset($data['order']) ? $data['order'] : '';
				$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY id asc';
			}

			$table='fm_async_method';

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " WHERE name $this->like '%$query%' OR data $this->like '%$query%' OR descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$method = array();
			while ($this->db->next_record())
			{
				$method[] = array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $this->db->f('name',true),
						'data'	=> $this->db->f('data',true),
						'descr'	=> $this->db->f('descr',true)
					);
			}
			return $method;
		}


		function read_single($id)
		{
			$id = (int) $id;

			$table='fm_async_method';

			$sql = "SELECT * FROM {$table} WHERE id={$id}";

			$this->db->query($sql,__LINE__,__FILE__);

			$method = array();
			if ($this->db->next_record())
			{
				$method['id']		= $this->db->f('id');
				$method['name']		= $this->db->f('name', true);
				$method['data']		= $this->db->f('data', true);
				$method['descr']	= $this->db->f('descr', true);
			}
			return $method;
		}

		function add($method)
		{
			$receipt = array();
			$table='fm_async_method';

			$this->db->transaction_begin();
			$method['id'] = $this->db->next_id($table);
			$method['name'] = $this->db->db_addslashes($method['name']);
			$method['descr'] = $this->db->db_addslashes($method['descr']);

			$this->db->query("INSERT INTO $table (id, name,data, descr) "
				. "VALUES ('" . $method['id'] . "','" . $method['name'] . "','" . $method['data'] . "','" . $method['descr']. "')",__LINE__,__FILE__);

			if( $this->db->transaction_commit() )
			{
				$receipt['id'] = $method['id'];
				$receipt['message'][] = array('msg' => lang('async method has been saved'));
			}

			return $receipt;
		}

		function edit($method)
		{
			$table='fm_async_method';

			$method['name'] = $this->db->db_addslashes($method['name']);
			$method['descr'] = $this->db->db_addslashes($method['descr']);

			$this->db->query("UPDATE $table set descr='" . $method['descr'] . "', name='". $method['name'] . "', data='". $method['data']
				. "' WHERE id='" . $method['id']. "'",__LINE__,__FILE__);

			$receipt['id'] = $method['id'];
			$receipt['message'][] = array('msg' =>lang('method has been edited'));
			return $receipt;
		}

		function delete($id)
		{
			$table='fm_async_method';

			$this->db->query("DELETE FROM $table WHERE id='" . $id . "'",__LINE__,__FILE__);
		}
	}
