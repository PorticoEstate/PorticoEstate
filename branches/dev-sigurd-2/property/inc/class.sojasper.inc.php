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

	class property_sojasper
	{
		function __construct()
		{
			$this->account	= 	$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
		}

		function read($data)
		{
			$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '';
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY id asc';
			}

			$table = 'fm_jasper';

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " WHERE id $this->like '%$query%' or descr $this->like '%$query%'";
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

			$jasper = array();
			while ($this->db->next_record())
			{
				$jasper[] = array
				(
					'id'				=> $this->db->f('id'),
					'descr'				=> $this->db->f('descr',true),
					'location_id'		=> $this->db->f('location_id'),
					'title'				=> $this->db->f('title',true),
					'file_name'			=> $this->db->f('file_name',true),
					'version'			=> $this->db->f('version'),
					'user_id'			=> $this->db->f('user_id'),
					'access'			=> $this->db->f('access'),
					'entry_date'		=> $this->db->f('entry_date'),
					'modified_by'		=> $this->db->f('modified_by'),
					'modified_date'		=> $this->db->f('modified_date')
				);
			}
			return $jasper;
		}

		function read_single($id)
		{

			$id = (int)$id;
			$table = 'fm_jasper';

			$sql = "SELECT * FROM $table  WHERE id = $id";

			$this->db->query($sql,__LINE__,__FILE__);

			$jasper = array();
			if ($this->db->next_record())
			{
				$jasper = array
				(
					'id'				=> $this->db->f('id'),
					'descr'				=> $this->db->f('descr',true),
					'location_id'		=> $this->db->f('location_id'),
					'title'				=> $this->db->f('title',true),
					'file_name'			=> $this->db->f('file_name',true),
					'version'			=> $this->db->f('version'),
					'user_id'			=> $this->db->f('user_id'),
					'access'			=> $this->db->f('access'),
					'entry_date'		=> $this->db->f('entry_date'),
					'modified_by'		=> $this->db->f('modified_by'),
					'modified_date'		=> $this->db->f('modified_date')
				);

				$sql = "SELECT fm_jasper_input.id, fm_jasper_input.input_type_id,fm_jasper_input.name as input_name,fm_jasper_input_type.name as type_name"
				." FROM fm_jasper_input {$this->join} fm_jasper_input_type ON fm_jasper_input.input_type_id = fm_jasper_input_type.id WHERE jasper_id = $id ORDER BY id ASC";
				$this->db->query($sql,__LINE__,__FILE__);
				$i = 1;
				while ($this->db->next_record())
				{
					$jasper['input'][] = array
					(
						'count'				=> $i,
						'id'				=> $this->db->f('id'),
						'input_type_id'		=> $this->db->f('input_type_id'),
						'input_name'		=> $this->db->f('input_name',true),
						'type_name'			=> $this->db->f('type_name',true)
					);
					$i++;
				}

			}
			return $jasper;
		}

		function add($jasper)
		{
			$receipt = array();
			$table = 'fm_jasper';
//_debug_array($jasper);
			$value_set= array
			(
				'location_id'	=> $GLOBALS['phpgw']->locations->get_id('property', $jasper['location']),
				'title'			=> $this->db->db_addslashes($jasper['title']),
				'file_name'		=> $jasper['file_name'],
				'descr'			=> $this->db->db_addslashes($jasper['descr']),
				'version'		=> 1,
				'access'		=> $jasper['access'],
				'user_id'		=> $this->account,
				'entry_date'	=> time(),
				'modified_by'	=> $this->account,
				'modified_date'	=> time()
			);

			$values	= $this->db->validate_insert(array_values($value_set));
			$this->db->transaction_begin();

/*
_debug_array("INSERT INTO $table (" . implode(',', array_keys($value_set)) .") "
				. "VALUES ($values)");
die();
*/


			$this->db->query("INSERT INTO $table (" . implode(',', array_keys($value_set)) .") "
				. "VALUES ($values)",__LINE__,__FILE__);

			$id = $this->db->get_last_insert_id($table,'id');

			if(isset($jasper['input_name']) && $jasper['input_name'] && isset($jasper['input_type']) && (int)$jasper['input_type'])
			{
				$jasper['input_name'] =  $this->db->db_addslashes($jasper['input_name']);
				$jasper['input_type'] =  (int)$jasper['input_type'];

				$this->db->query("INSERT INTO fm_jasper_input (jasper_id,input_type_id,name)"
				." VALUES({$id},{$jasper['input_type']},'{$jasper['input_name']}')",__LINE__,__FILE__);
			}
			
			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('JasperReport %1 has been saved',$id));
			}
			return $receipt;
		}

		function edit($jasper)
		{
			$receipt = array();
			$table = 'fm_jasper';

			$value_set= array
			(
				'location_id'	=> $GLOBALS['phpgw']->locations->get_id('property', $jasper['location']),
				'title'			=> $this->db->db_addslashes($jasper['title']),
				'file_name'		=> $jasper['file_name'],
				'descr'			=> $this->db->db_addslashes($jasper['descr']),
				'access'		=> $jasper['access'],
				'modified_by'	=> $this->account,
				'modified_date'	=> time()
			);

			$value_set	= $this->db->validate_update($value_set);
			$this->db->transaction_begin();
			$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$jasper['id']}" ,__LINE__,__FILE__);

			if(isset($jasper['delete_input']) && $jasper['delete_input'])
			{
				foreach($jasper['delete_input'] as $delete_input)
				{
					$this->db->query("DELETE FROM fm_jasper_input WHERE id = {$delete_input} AND jasper_id = {$jasper['id']}",__LINE__,__FILE__);
				}
			}
			if(isset($jasper['input_name']) && $jasper['input_name'] && isset($jasper['input_type']) && (int)$jasper['input_type'])
			{
				$jasper['input_name'] =  $this->db->db_addslashes($jasper['input_name']);
				$jasper['input_type'] =  (int)$jasper['input_type'];

				$this->db->query("INSERT INTO fm_jasper_input (jasper_id,input_type_id,name)"
				." VALUES({$jasper['id']},{$jasper['input_type']},'{$jasper['input_name']}')",__LINE__,__FILE__);
			}


			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('JasperReport %1 has been edited',$jasper['id']));
			}
			return $receipt;
		}

		function delete($id)
		{
			$table = 'fm_jasper';

			$this->db->query("DELETE FROM $table WHERE id='" . $id . "'",__LINE__,__FILE__);
		}

		public function get_input_type_list()
		{
			$this->db->query('SELECT * FROM fm_jasper_input_type',__LINE__,__FILE__);

			$input_types = array();
			while ($this->db->next_record())
			{
				$input_types[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name',true)
				);
			}
			return $input_types;
		}
	}
