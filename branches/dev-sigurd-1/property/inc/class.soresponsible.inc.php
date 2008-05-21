<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage property
	* @category core
 	* @version $Id: class.uiresponsible.inc.php 732 2008-02-10 16:21:14Z sigurd $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * ResponsibleMatrix - handles automated assigning of tasks based on (physical)location and category.
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */

	class property_soresponsible
	{
		var $grants;
		var $db;
		var $account;
		var $acl_location;

		/**
		* @var the total number of records for a search
		*/
		public $total_records = 0;

		function __construct()
		{
			$this->account			=& $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db 				=& $GLOBALS['phpgw']->db;

			$this->like 			=& $this->db->like;
			$this->join 			=& $this->db->join;
			$this->left_join		=& $this->db->left_join;
		}

		/**
		* Read type
		*
		* @param array $values  array that Includes the fields: 'start', 'query', 'sort', 'order', 'allrows', 'filter' and 'location'
		*
		* @return array Responsibility types
		*/

		public function read_type($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
				$query		= isset($data['query']) ? $this->db->db_addslashes($data['query']) : '';
				$sort		= isset($data['sort']) && $data['sort'] ? $this->db->db_addslashes($data['sort']) : 'DESC';
				$order		= isset($data['order']) ? $this->db->db_addslashes($data['order']) : '';
				$allrows	= isset($data['allrows']) ? !!$data['allrows'] : '';
				$filter		= $data['filter'] ? $data['filter'] : '';
				$location	= isset($data['location']) ? $data['location'] : '';
				
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_responsibility.id DESC';
			}

			$where= 'WHERE';
			$filtermethod = '';

			if(is_array($filter) && $location)
			{
				$filtermethod .= " $where cat_id IN (" . implode(',', $filter) . ')';
				$where = 'AND';
			}
			$querymethod = '';
			if($query)
			{
				$querymethod = "$where (name $this->like '%$query%' OR descr $this->like '%$query%')";
			}

			$sql = "SELECT * FROM fm_responsibility $filtermethod $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$values = array();

			while ($this->db->next_record())
			{
				$values[] = array
				(
					'id'			=> $this->db->f('id'),
					'name'			=> $this->db->f('name', true),
					'descr'			=> $this->db->f('descr', true),
					'active'		=> $this->db->f('active'),
					'cat_id'		=> $this->db->f('cat_id'),
					'created_by'	=> $this->db->f('created_by'),
					'created_on'	=> $this->db->f('created_on'),
				);
			}

			return $values;
		}

		/**
		* Add responsibility type
		*
		* @param array $values  values to be stored/edited and referencing ID if editing
		*
		* @return array $receip with result on the action(failed/success)
		*/

		public function add_type($values)
		{
			$receipt = array();
			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$insert_values = array
			(
				$values['name'],
				$values['descr'],
				(int)$values['cat_id'],
				isset($values['active']) ? !!$values['active'] : '',
				$this->account,
				time()
			);

			$insert_values	= $this->db->validate_insert($insert_values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_responsibility (name, descr, cat_id, active, created_by, created_on) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('Responsibility type has been saved'));
				$receipt['id']= $this->db->get_last_insert_id('fm_responsibility', 'id');
			}
			else
			{
				$receipt['error'][]=array('msg'=>lang('Not saved'));
			}

			return $receipt;
		}


		public function edit_type($values)
		{
			$receipt = array();
			$value_set['name']		= $this->db->db_addslashes($values['name']);
			$value_set['descr']		= $this->db->db_addslashes($values['descr']);
			$value_set['cat_id']	= (int)$values['cat_id'];
			$value_set['active']	= isset($values['active']) ? !!$values['active'] : '';

			$value_set	= $this->db->validate_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_responsibility set $value_set WHERE id = " . (int)$values['id'],__LINE__,__FILE__);

			$receipt['id']= $values['id'];
			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('responsibility type has been edited'));
			}
			else
			{
				$receipt['error'][]=array('msg'=>lang('changes not saved'));
			}
			return $receipt;
		}

		/**
		* Read single responsibility type
		*
		* @param integer $id  ID of responsibility type
		*
		* @return array Responsibility type
		*/

		public function read_single_type($id)
		{
			$sql = 'SELECT * FROM fm_responsibility WHERE id= ' . (int)$id;

			$this->db->query($sql,__LINE__,__FILE__);

			$values = array();

			$this->db->next_record();
			$values = array
			(
				'id'			=> $this->db->f('id'),
				'name'			=> $this->db->f('name', true),
				'descr'			=> $this->db->f('descr', true),
				'active'		=> $this->db->f('active'),
				'cat_id'		=> $this->db->f('cat_id'),
				'created_by'	=> $this->db->f('created_by'),
				'created_on'	=> $this->db->f('created_on'),
			);

			return $values;
		}

		function delete_type($id)
		{
			$this->db->query('DELETE FROM fm_responsibility WHERE id='  . (int) $id, __LINE__, __FILE__);
		}
	}
