<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
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
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
			$GLOBALS['phpgw']->acl->set_account_id($this->account);
			$this->grants		= $GLOBALS['phpgw']->acl->get_grants('property','.jasper');
		}

		public function read($data)
		{
			$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query			= isset($data['query']) ? $data['query'] : '';
			$sort			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order			= isset($data['order']) ? $data['order'] : '';
			$allrows		= isset($data['allrows']) ? $data['allrows'] : '';
			$location_id	= isset($data['location_id']) && $data['location_id'] ? (int)$data['location_id'] : 0;
			$app			= isset($data['app']) ? $data['app'] : '';

			$grants	= & $this->grants;

			$table = 'fm_jasper';			
			$app_filter = '';
			if($app)
			{
				$app_id = (int)$GLOBALS['phpgw_info']['apps'][$app]['id'];
				$app_filter = "{$this->join} phpgw_locations ON (phpgw_locations.app_id = {$app_id} AND phpgw_locations.location_id = {$table}.location_id)";
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY id asc';
			}


			$filtermethod = "WHERE ( {$table}.user_id = {$this->account}";
			if (is_array($grants))
			{
				foreach($grants as $user => $right)
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod .= " OR (access='public' AND {$table}.user_id IN(" . implode(',',$public_user_list) . ")))";
			}
			else
			{
				$filtermethod .= ' )';
			}

			if($location_id)
			{
				$filtermethod .= " AND location_id = {$location_id}";
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = "AND (title {$this->like} '%{$query}%' OR descr {$this->like} '%{$query}%')";
			}

			$sql = "SELECT * FROM {$table} {$app_filter} {$filtermethod} {$querymethod}";

			if(!$allrows)
			{
				$this->db->query("SELECT count(*) as cnt FROM {$table} {$app_filter} {$filtermethod} {$querymethod}",__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f('cnt');
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
				$this->total_records = $this->db->num_rows();
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
						'formats'			=> @unserialize($this->db->f('formats',true)),
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

		public function read_single($id)
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
						'formats'			=> @unserialize($this->db->f('formats',true)),
						'version'			=> $this->db->f('version'),
						'user_id'			=> $this->db->f('user_id'),
						'access'			=> $this->db->f('access'),
						'entry_date'		=> $this->db->f('entry_date'),
						'modified_by'		=> $this->db->f('modified_by'),
						'modified_date'		=> $this->db->f('modified_date')
					);

				$sql = "SELECT fm_jasper_input.id, fm_jasper_input.input_type_id,fm_jasper_input.name as input_name,fm_jasper_input_type.name as type_name,is_id"
					." FROM fm_jasper_input {$this->join} fm_jasper_input_type ON fm_jasper_input.input_type_id = fm_jasper_input_type.id WHERE jasper_id = $id ORDER BY id ASC";
				$this->db->query($sql,__LINE__,__FILE__);
				$i = 0;
				while ($this->db->next_record())
				{
					$jasper['input'][] = array
						(
							'counter'			=> $i,
							'id'				=> $this->db->f('id'),
							'input_type_id'		=> $this->db->f('input_type_id'),
							'input_name'		=> $this->db->f('input_name',true),
							'datatype'			=> $this->db->f('type_name',true),
							'type_name'			=> $this->db->f('type_name',true),
							'is_id'				=> $this->db->f('is_id')
						);
					$i++;
				}

			}
			return $jasper;
		}

		public function add($jasper)
		{
			$receipt = array();
			$table = 'fm_jasper';

			$value_set= array
				(
					'location_id'	=> $GLOBALS['phpgw']->locations->get_id($jasper['app'], $jasper['location']),
					'title'			=> $this->db->db_addslashes($jasper['title']),
					'descr'			=> $this->db->db_addslashes($jasper['descr']),
					'formats'		=> serialize($jasper['formats']),
					'version'		=> 1,
					'access'		=> $jasper['access'],
					'user_id'		=> $this->account,
					'entry_date'	=> time(),
					'modified_by'	=> $this->account,
					'modified_date'	=> time()
				);

			$values	= $this->db->validate_insert(array_values($value_set));
			$this->db->transaction_begin();

			$this->db->query("INSERT INTO $table (" . implode(',', array_keys($value_set)) .") "
				. "VALUES ($values)",__LINE__,__FILE__);

			$id = $this->db->get_last_insert_id($table,'id');

			if(isset($jasper['input_name']) && $jasper['input_name'] && isset($jasper['input_type']) && (int)$jasper['input_type'])
			{
				$jasper['input_name'] =  $this->db->db_addslashes($jasper['input_name']);
				$jasper['input_type'] =  (int)$jasper['input_type'];

				$is_id = (int)$jasper['is_id'];
				$this->db->query("INSERT INTO fm_jasper_input (jasper_id,input_type_id,name,is_id)"
					." VALUES({$id},{$jasper['input_type']},'{$jasper['input_name']}',{$is_id})",__LINE__,__FILE__);
			}

			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('JasperReport %1 has been saved',$id));
				$receipt['id'] = $id;
			}
			return $receipt;
		}

		public function edit($jasper)
		{
			$receipt = array();
			$jasper['id'] = (int)$jasper['id'];
			$receipt['id'] = $jasper['id'];

			$table = 'fm_jasper';

			$this->db->query("SELECT user_id FROM {$table} WHERE id = {$jasper['id']}",__LINE__,__FILE__);
			$this->db->next_record();
			$user_id = $this->db->f('user_id');

			if(! ($this->grants[$user_id] & PHPGW_ACL_EDIT))
			{
				$receipt['error'][] = array('msg'=>lang('JasperReport %1 has not been edited',$jasper['id']));
				return 	$receipt;
			}

			$value_set= array
				(
					'location_id'	=> $GLOBALS['phpgw']->locations->get_id($jasper['app'], $jasper['location']),
					'title'			=> $this->db->db_addslashes($jasper['title']),
					'descr'			=> $this->db->db_addslashes($jasper['descr']),
					'formats'		=> serialize($jasper['formats']),
					'access'		=> $jasper['access'],
					'modified_by'	=> $this->account,
					'modified_date'	=> time()
				);

			$value_set	= $this->db->validate_update($value_set);
			$this->db->transaction_begin();
			$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$jasper['id']}" ,__LINE__,__FILE__);

			$this->db->query("UPDATE fm_jasper_input SET is_id = 0 WHERE jasper_id = {$jasper['id']}",__LINE__,__FILE__);

			if(isset($jasper['edit_is_id']) && $jasper['edit_is_id'])
			{
				foreach($jasper['edit_is_id'] as $edit_is_id)
				{
					$this->db->query("UPDATE fm_jasper_input SET is_id = 1 WHERE id = {$edit_is_id} AND jasper_id = {$jasper['id']}",__LINE__,__FILE__);
				}
			}

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
				$is_id =  (int)$jasper['is_id'];

				$this->db->query("INSERT INTO fm_jasper_input (jasper_id,input_type_id,name,is_id)"
					." VALUES({$jasper['id']},{$jasper['input_type']},'{$jasper['input_name']}',$is_id)",__LINE__,__FILE__);
			}

			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('JasperReport %1 has been edited',$jasper['id']));
			}

			return $receipt;
		}

		public function delete($id)
		{
			$id = (int)$id;

			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_jasper_input WHERE jasper_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_jasper WHERE id = {$id}",__LINE__,__FILE__);
			$this->db->transaction_commit();
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
						'descr'	=> $this->db->f('descr',true)
					);
			}
			return $input_types;
		}

		public function get_format_type_list()
		{
			$this->db->query('SELECT * FROM fm_jasper_format_type',__LINE__,__FILE__);

			$format_types = array();
			while ($this->db->next_record())
			{
				$format_types[] = array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $this->db->f('id')
					);
			}
			return $format_types;
		}
	}
