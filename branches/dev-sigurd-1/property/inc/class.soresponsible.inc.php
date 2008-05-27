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


		/**
		* Read responsibility type contact
		*
		* @param array $values  array that Includes the fields: 'start', 'query', 'sort', 'order', 'allrows', 'filter' and 'location'
		*
		* @return array Responsibility type contacts
		*/

		public function read_contact($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
				$query		= isset($data['query']) ? $this->db->db_addslashes($data['query']) : '';
				$sort		= isset($data['sort']) && $data['sort'] ? $this->db->db_addslashes($data['sort']) : 'DESC';
				$order		= isset($data['order']) ? $this->db->db_addslashes($data['order']) : '';
				$allrows	= isset($data['allrows']) ? !!$data['allrows'] : '';
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by responsibility_id DESC';
			}

			$filtermethod = ' WHERE expired_on IS NULL';
			$where = 'AND';

			$querymethod = '';
			if($query)
			{
				$querymethod = "$where (remark $this->like '%$query%')";
			}

			$sql = "SELECT * FROM fm_responsibility_contact $filtermethod $querymethod";

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
					'id'				=> $this->db->f('id'),
					'responsibility_id'	=> $this->db->f('responsibility_id'),
					'contact_id'		=> $this->db->f('contact_id'),
					'location_code'		=> $this->db->f('location_code'),
					'priority'			=> $this->db->f('priority'),
					'active_from'		=> $this->db->f('active_from'),
					'active_to'			=> $this->db->f('active_to'),
					'created_on'		=> $this->db->f('created_on'),
					'created_by'		=> $this->db->f('created_by'),
					'expired_on'		=> $this->db->f('expired_on'), // historical records
					'expired_by'		=> $this->db->f('expired_by'),
					'p_num'				=> $this->db->f('p_num', true),
					'p_entity_id'		=> $this->db->f('p_entity_id'),
					'p_cat_id'			=> $this->db->f('p_cat_id'),
					'remark'			=> $this->db->f('remark', true),
				);
			}

 		return $values;
		}


		/**
		* Add responsibility contact
		*
		* @param array $values  values to be stored/edited
		*
		* @return array $receip with result on the action(failed/success)
		*/

		public function add_contact($values)
		{
			$receipt = array();
			$values['remark'] = $this->db->db_addslashes($values['remark']);

			$insert_values = array
			(
				(int)$values['responsibility_id'],
				(int)$values['contact_id'],
				implode('-', $values['location']),
				$values['active_from'],
				$values['active_to'],
				isset($values['extra']['p_num']) ? $values['extra']['p_num'] : '',
				isset($values['extra']['p_entity_id']) ? $values['extra']['p_entity_id'] : '',
				isset($values['extra']['p_cat_id']) ? $values['extra']['p_cat_id'] : '',
				$values['remark'],
				$this->account,
				time()
			);

			$insert_values	= $this->db->validate_insert($insert_values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_responsibility_contact (responsibility_id, contact_id,"
				." location_code, active_from, active_to, p_num, p_entity_id, p_cat_id, remark, created_by, created_on) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('Responsibility contact has been saved'));
				$receipt['id']= $this->db->get_last_insert_id('fm_responsibility_contact', 'id');
			}
			else
			{
				$receipt['error'][]=array('msg'=>lang('Not saved'));
			}

			return $receipt;
		}

		/**
		* Edit responsibility contact
		*
		* @param array $values  values to be stored/edited
		*
		* @return array $receip with result on the action(failed/success)
		*/

		public function edit_contact($values)
		{
			$receipt = array();

			$orig = $this->read_single_contact($values['id']);

			if(implode('-', $values['location']) != $orig['location_code']
				|| $values['active_from'] != $orig['active_from']
				|| $values['active_to'] != $orig['active_to']
				|| $values['p_num'] != $orig['p_num']
				|| $values['remark'] != $orig['remark']
				|| !$orig['expired_on'])
			{
				$receipt = $this->add_contact($values);
				
				if(!isset($receipt['error']))
				{
					unset($receipt['message']);

					$value_set['expired_by']	= $this->account;
					$value_set['expired_on']	= time();
				}

				$value_set	= $this->db->validate_update($value_set);

				$this->db->transaction_begin();

				$this->db->query("UPDATE fm_responsibility_contact set $value_set WHERE id = " . (int)$values['id'],__LINE__,__FILE__);

				if($this->db->transaction_commit())
				{
					$receipt['message'][]=array('msg'=>lang('Responsibility contact has been changed'));
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Not saved'));
				}

			}
			else
			{
				$receipt['id']= $values['id'];
				$receipt['message'][]=array('msg'=>lang('Nothing changed'));
			}

			return $receipt;
		}



		/**
		* Read single responsibility contact
		*
		* @param integer $id  ID of responsibility_contact
		*
		* @return array Responsibility contact
		*/

		public function read_single_contact($id)
		{
			$sql = 'SELECT * FROM fm_responsibility_contact WHERE id= ' . (int)$id;

			$this->db->query($sql,__LINE__,__FILE__);

			$values = array();

			$this->db->next_record();
			$values = array
			(
				'id'				=> $this->db->f('id'),
				'responsibility_id'	=> $this->db->f('responsibility_id'),
				'contact_id'		=> $this->db->f('contact_id'),
				'location_code'		=> $this->db->f('location_code'),
				'p_num'				=> $this->db->f('p_num'),
				'p_entity_id'		=> $this->db->f('p_entity_id'),
				'p_cat_id'			=> $this->db->f('p_cat_id'),
				'remark'			=> $this->db->f('remark', true),
				'active_from'		=> $this->db->f('active_from'),
				'active_to'			=> $this->db->f('active_to'),
				'created_by'		=> $this->db->f('created_by'),
				'created_on'		=> $this->db->f('created_on'),
				'expired_by'		=> $this->db->f('expired_by'),
				'expired_on'		=> $this->db->f('expired_on'),
				'priority'			=> $this->db->f('priority'), // FIXME - evaluate the need for this one
			);

			return $values;
		}

		function delete_contact($id)
		{
			$this->db->query('DELETE FROM fm_responsibility_contact WHERE id='  . (int) $id, __LINE__, __FILE__);
		}

		/**
		* Get the responsibility for a particular category conserning a given location or item
		*
		* @param array $array  containing cat_id, location_code and optional item-information
		*
		* @return contact_id
		*/

		public function get_responsible($values = array())
		{
			if(!isset($values['location_code']) || !$values['location_code'])
			{
				return 0;
			}

			//FIXME:$item_filter = something

			$sql = "SELECT contact_id FROM fm_responsibility_contact WHERE location_code = {$values['location_code']} {$item_filter}"
			 . 'AND active_from < ' . time() . ' AND active_to > ' . time() . ' AND expired_on IS NULL';

			$this->db->query($sql,__LINE__,__FILE__);

			$this->db->next_record();

			return $this->db->f('contact_id');
		}

		/**
		* Get the user_id for a particular contact
		*
		* @param integer $contact_id
		*
		* @return user_id
		*/

		public function get_contact_user_id($person_id)
		{
			$sql = 'SELECT account_id FROM phpgw_accounts WHERE person_id =' . (int)$person_id;
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('account_id');
		}
	}
