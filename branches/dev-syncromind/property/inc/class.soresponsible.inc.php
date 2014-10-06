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
 	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
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
		var $appname = 'property';

		/**
		 * @var the total number of records for a search
		 */
		public $total_records = 0;

		/**
		 * Constructor
		 *
		 */

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
		 * @param array $data array that Includes the fields: 'start', 'query', 'sort', 'order', 'allrows', 'filter' and 'location'
		 *
		 * @return array Responsibility types
		 */

		public function read_type($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? (int) $data['start'] : 0;
				$query		= isset($data['query']) ? $this->db->db_addslashes($data['query']) : '';
				$sort		= isset($data['sort']) && $data['sort'] ? $this->db->db_addslashes($data['sort']) : 'DESC';
				$order		= isset($data['order']) ? $this->db->db_addslashes($data['order']) : '';
				$allrows	= isset($data['allrows']) ? !!$data['allrows'] : '';
				$filter		= $data['filter'] ? $data['filter'] : '';
				$location	= isset($data['location']) ? $data['location'] : '';
				$appname	= isset($data['appname'])  && $data['appname'] ? $data['appname'] : 'property';
			}

			if ($order)
			{
				$ordermethod = " order by fm_responsibility.$order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_responsibility.id DESC';
			}

			$where= 'WHERE';
			$filtermethod = '';

/*
			if(is_array($filter) && $location)
			{
				$filtermethod .= " $where cat_id IN (" . implode(',', $filter) . ')';
				$where = 'AND';
			}

			if($location)
			{
				$filtermethod .= " $where fm_responsibility_module.location_id =" . $GLOBALS['phpgw']->locations->get_id($this->appname, $location);
				$where = 'AND';
			}
 */
			$querymethod = '';
			if($query)
			{
				$querymethod = "$where (fm_responsibility.name {$this->like} '%$query%' OR fm_responsibility.descr {$this->like} '%$query%')";
			}

			$sql = "SELECT fm_responsibility.* FROM fm_responsibility"
//			. " {$this->join} fm_responsibility_module ON fm_responsibility.id = fm_responsibility_module.responsibility_id"
//			. " {$this->join} phpgw_locations ON fm_responsibility_module.location_id = phpgw_locations.location_id"
//			. " {$this->join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
			. " {$filtermethod} {$querymethod}";
//			. " WHERE app_name = '{$appname}' $filtermethod $querymethod";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
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
					'location'		=> $this->db->f('location'),
					'cat_id'		=> $this->db->f('cat_id'),
					'created_by'	=> $this->db->f('created_by'),
					'created_on'	=> $this->db->f('created_on'),
				);
			}

			$cats	= CreateObject('phpgwapi.categories', -1);
			foreach($values as &$entry)
			{
				$sql = "SELECT location_id, cat_id FROM fm_responsibility_module WHERE responsibility_id = {$entry['id']}";
				$this->db->query($sql, __LINE__, __FILE__);			
				$locations = array();
				while ($this->db->next_record())
				{
					$location_id = $this->db->f('location_id');
					$cat_id = $this->db->f('cat_id');
					$locations["{$location_id}_{$cat_id}"] = true;
				}		
				$__location_info = array();
				foreach($locations as $location => $dummy)
				{
					
					$location_arr = explode('_', $location);
					$_location_info = $GLOBALS['phpgw']->locations->get_name($location_arr[0]);

					$category = $cats->return_single($location_arr[1]);

					$__location_info[] = "{$_location_info['appname']}::{$_location_info['location']}::{$category[0]['name']}({$location_arr[1]})";
				}	
				$entry['appname'] = implode(' | ', $__location_info);
			}

//_debug_array($values);die();
			return $values;
		}

		/**
		 * Add responsibility type
		 *
		 * @param array $data values to be stored/edited and referencing ID if editing
		 *
		 * @return array $receip with result on the action(failed/success)
		 */

		public function add_type($data)
		{
			$receipt = array();
			$data['name'] = $this->db->db_addslashes($data['name']);
			$data['descr'] = $this->db->db_addslashes($data['descr']);

			$insert_values = array
			(
				$data['name'],
				$data['descr'],
				$this->account,
				time()
			);

			$insert_values	= $this->db->validate_insert($insert_values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_responsibility (name, descr, created_by, created_on) "
				. "VALUES ($insert_values)", __LINE__, __FILE__);

			$id = $this->db->get_last_insert_id('fm_responsibility', 'id');

			if($data['cat_id'])
			{
				$location_id = $GLOBALS['phpgw']->locations->get_id( $data['appname'],  $data['location']);

				$this->db->query("SELECT * FROM fm_responsibility_module  WHERE location_id = " . (int) $location_id . ' AND cat_id = ' . (int)$data['cat_id'], __LINE__, __FILE__);
				
				if(!$this->db->next_record())
				{
					$value_set = array();

					$value_set['responsibility_id']		= $id;
					$value_set['location_id']			= $location_id;
					$value_set['cat_id']				= (int) $data['cat_id'];
					$value_set['active']				= 1;//isset($data['active']) ? !!$data['active'] : '';
					$value_set['created_on']			= time();										
					$value_set['created_by']			= $this->account;

					$cols = implode(',', array_keys($value_set));
					$values	= $this->db->validate_insert(array_values($value_set));
					$this->db->query("INSERT INTO fm_responsibility_module ({$cols}) VALUES ({$values})",__LINE__,__FILE__);
				}
			}

			if($this->db->transaction_commit())
			{
				$receipt['message'][]	= array('msg'=>lang('Responsibility type has been saved'));
				$receipt['id']			= $id;
			}
			else
			{
				$receipt['error'][]=array('msg'=>lang('Not saved'));
			}

			return $receipt;
		}

		/**
		 * Edit responsibility type
		 *
		 * @param array $data values to be stored/edited and referencing ID if editing
		 *
		 * @return array $receip with result on the action(failed/success)
		 */

		public function edit_type($data)
		{
			$receipt = array();
			$value_set = array();
			$value_set['name']		= $this->db->db_addslashes($data['name']);
			$value_set['descr']		= $this->db->db_addslashes($data['descr']);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_responsibility SET $value_set WHERE id = " . (int) $data['id'], __LINE__, __FILE__);
			$this->db->query("UPDATE fm_responsibility_module SET active = NULL WHERE responsibility_id = " . (int) $data['id'], __LINE__, __FILE__);

			if($data['cat_id'])
			{
				$location_id = $GLOBALS['phpgw']->locations->get_id( $data['appname'],  $data['location']);

				$this->db->query("SELECT * FROM fm_responsibility_module  WHERE responsibility_id = " . (int) $data['id'] . ' AND location_id = ' . (int) $location_id . ' AND cat_id = ' . (int)$data['cat_id'], __LINE__, __FILE__);

				if(!$this->db->next_record())
				{
					$value_set = array();

					$value_set['responsibility_id']		= (int) $data['id'];
					$value_set['location_id']			= $location_id;
					$value_set['cat_id']				= (int) $data['cat_id'];
					$value_set['active']				= 1;//isset($data['active']) ? !!$data['active'] : '';
					$value_set['created_on']			= time();										
					$value_set['created_by']			= $this->account;

					$cols = implode(',', array_keys($value_set));
					$values	= $this->db->validate_insert(array_values($value_set));
					$this->db->query("INSERT INTO fm_responsibility_module ({$cols}) VALUES ({$values})",__LINE__,__FILE__);
				}
			}

//_debug_array($data);die();
			if(isset($data['delete_module']) && $data['delete_module'])
			{
				foreach($data['delete_module'] as $to_delete)
				{
					$_to_delete = explode('_', $to_delete);
					$this->db->query("DELETE FROM fm_responsibility_module  WHERE location_id = " . (int) $_to_delete[0] . ' AND cat_id = ' . (int) $_to_delete[1], __LINE__, __FILE__);
				}
			}

			if(isset($data['set_active']) && $data['set_active'])
			{
				foreach($data['set_active'] as $set_active)
				{
					$_set_active = explode('_', $set_active);
					$this->db->query("UPDATE fm_responsibility_module SET active = 1 WHERE location_id = " . (int) $_set_active[0] . ' AND cat_id = ' . (int) $_set_active[1], __LINE__, __FILE__);
				}
			}
//_debug_array($data);die();

			$receipt['id']= $data['id'];
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
		 * @param integer $id ID of responsibility type
		 *
		 * @return array Responsibility type
		 */

		public function read_single_type($id)
		{
			$sql = 'SELECT * FROM fm_responsibility WHERE id= ' . (int) $id;

			$this->db->query($sql, __LINE__, __FILE__);

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

		/**
		 * Read single responsibility role
		 *
		 * @param integer $id ID of responsibility role
		 *
		 * @return array Responsibility type
		 */

		public function read_single_role($id)
		{
			$sql = 'SELECT * FROM fm_responsibility_role WHERE id= ' . (int) $id;

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();

			$this->db->next_record();
			$values = array
			(
				'id'				=> $this->db->f('id'),
				'name'				=> $this->db->f('name', true),
				'remark'			=> $this->db->f('remark', true),
				'location_level'	=> explode(',', ltrim($this->db->f('location_level'), '.location.')),
				'responsibility_id'	=> $this->db->f('responsibility_id'),
				'user_id'			=> $this->db->f('user_id'),
				'entry_date'		=> $this->db->f('entry_date'),
				'modified_date'		=> $this->db->f('modified_date'),
				'appname'			=> $this->db->f('appname'),
			);

			return $values;
		}


		public function add_role($data)
		{
			$receipt = array();

			$values_insert['name']					=$this->db->db_addslashes($data['name']);
			$values_insert['remark']				= $this->db->db_addslashes($data['remark']);
			$values_insert['responsibility_id']		= $data['responsibility_id'];
			$values_insert['location_level']		= implode(',', $data['location_level']);
			$values_insert['modified_date']			= time();
			$values_insert['entry_date']			= time();
			$values_insert['user_id']				= $this->account;
			//FIXME
			$values_insert['appname']				= 'property';
			$values_insert['id'] = $this->db->next_id('fm_responsibility_role');

 			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_responsibility_role (" . implode(',',array_keys($values_insert)) . ') VALUES ('
			 . $this->db->validate_insert(array_values($values_insert)) . ')',__LINE__,__FILE__);


			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('Responsibility role has been changed'));
				$receipt['id']= $values_insert['id'];//$this->db->get_last_insert_id('fm_responsibility_role', 'id');
			}
			else
			{
				$receipt['message'][]=array('msg'=>lang('Nothing changed'));
			}

			return $receipt;
		}


		public function edit_role($data)
		{
			$receipt = array();

			$value_set['name']					=$this->db->db_addslashes($data['name']);
			$value_set['remark']				= $this->db->db_addslashes($data['remark']);
			$value_set['responsibility_id']		= $data['responsibility_id'];
			$value_set['location_level']				= implode(',', $data['location_level']);
			$value_set['modified_date']			= time();


			$value_set	= $this->db->validate_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_responsibility_role set $value_set WHERE id = " . (int) $data['id'], __LINE__, __FILE__);

			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('Responsibility role has been changed'));
			}
			else
			{
				$receipt['message'][]=array('msg'=>lang('Nothing changed'));
			}
			$receipt['id']= $data['id'];

			return $receipt;
		}


		/**
		 * Read single responsibility type
		 *
		 * @param integer $id ID of responsibility type
		 *
		 * @return array Responsibility type
		 */

		public function read_single($id)
		{
			$sql = 'SELECT * FROM fm_responsibility WHERE id= ' . (int) $id;

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();

			if(	$this->db->next_record())
			{
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

				$sql = 'SELECT * FROM fm_responsibility_module WHERE responsibility_id= ' . (int) $id;
				$this->db->query($sql, __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					$values['module'][] = array
					(
						'location_id'		=> $this->db->f('location_id'),
						'cat_id'			=> $this->db->f('cat_id'),
						'active'			=> $this->db->f('active'),
						'created_on'		=> $this->db->f('created_on'),
						'created_by'		=> $this->db->f('created_by'),
					);
				}
			}

			return $values;
		}


		/**
		 * Delete responsibility type
		 *
		 * @param integer $id ID of responsibility type
		 *
		 * @return void
		 */

		function delete_type($id)
		{
			$this->db->transaction_begin();
			$this->db->query('DELETE FROM fm_responsibility_module WHERE responsibility_id='  . (int) $id, __LINE__, __FILE__);
			$this->db->query('DELETE FROM fm_responsibility_contact WHERE responsibility_id='  . (int) $id, __LINE__, __FILE__);
			$this->db->query('DELETE FROM fm_responsibility WHERE id='  . (int) $id, __LINE__, __FILE__);
			$this->db->transaction_commit();
		}


		/**
		 * Read responsibility type contact
		 *
		 * @param array $data array that Includes the fields: 'start', 'query', 'sort', 'order', 'allrows' and 'type_id'
		 *
		 * @return array Responsibility type contacts
		 */

		public function read_contact($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? (int) $data['start'] : 0;
				$query		= isset($data['query']) ? $this->db->db_addslashes($data['query']) : '';
				$sort		= isset($data['sort']) && $data['sort'] ? $this->db->db_addslashes($data['sort']) : 'DESC';
				$order		= isset($data['order']) ? $this->db->db_addslashes($data['order']) : '';
				$allrows	= isset($data['allrows']) ? !!$data['allrows'] : '';
				$type_id		= isset($data['type_id']) && $data['type_id'] ? (int) $data['type_id'] : 0;
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

			if($type_id > 0)
			{
				$filtermethod .= " $where responsibility_id = $type_id";
			}
			$querymethod = '';
			if($query)
			{
				$querymethod = "$where (remark $this->like '%$query%')";
			}

			$sql = "SELECT * FROM fm_responsibility_contact $filtermethod $querymethod";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
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
						'ecodimb'			=> $this->db->f('ecodimb'),
						'remark'			=> $this->db->f('remark', true),
					);
			}

			return $values;
		}


		/**
		 * Add responsibility contact
		 *
		 * @param array $values values to be stored/edited
		 *
		 * @return array $receip with result on the action(failed/success)
		 */

		public function add_contact($values)
		{
			$receipt = array();
			$values['remark'] = $this->db->db_addslashes($values['remark']);

			$insert_values = array
				(
					(int) $values['responsibility_id'],
					(int) $values['contact_id'],
					@implode('-', $values['location']),
					(int) $values['active_from'],
					(int) $values['active_to'],
					isset($values['extra']['p_num']) ? $values['extra']['p_num'] : '',
					isset($values['extra']['p_entity_id']) ? $values['extra']['p_entity_id'] : '',
					isset($values['extra']['p_cat_id']) ? $values['extra']['p_cat_id'] : '',
					$values['ecodimb'],
					$values['remark'],
					$this->account,
					time()
				);

			$insert_values	= $this->db->validate_insert($insert_values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_responsibility_contact (responsibility_id, contact_id,"
				." location_code, active_from, active_to, p_num, p_entity_id, p_cat_id, ecodimb, remark, created_by, created_on)"
				." VALUES ($insert_values)", __LINE__, __FILE__);

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
		 * @param array $values values to be stored/edited
		 *
		 * @return array $receip with result on the action(failed/success)
		 */

		public function edit_contact($values)
		{
			$receipt = array();

			$orig = $this->read_single_contact($values['id']);

			if(isset($values['location']) &&(@implode('-', $values['location']) != $orig['location_code'])
				|| $values['contact_id'] != $orig['contact_id']
				|| $values['active_from'] != $orig['active_from']
				|| $values['active_to'] != $orig['active_to']
				|| $values['extra']['p_num'] != $orig['p_num']
				|| $values['remark'] != $orig['remark']
				|| $values['ecodimb'] != $orig['ecodimb'])
			{
				$receipt = $this->add_contact($values);

				if(!isset($receipt['error']))
				{
					unset($receipt['message']);

					$value_set['expired_by']	= $this->account;
					$value_set['expired_on']	= time();
					$value_set['ecodimb']		= $values['ecodimb'];
				}

				$value_set	= $this->db->validate_update($value_set);

				$this->db->transaction_begin();

				$this->db->query("UPDATE fm_responsibility_contact set $value_set WHERE id = " . (int) $values['id'], __LINE__, __FILE__);

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
		 * @param integer $id ID of responsibility_contact
		 *
		 * @return array Responsibility contact
		 */

		public function read_single_contact($id)
		{
			$sql = "SELECT fm_responsibility_contact.*,  fm_responsibility.name as responsibility_name"
				. " FROM fm_responsibility_contact"
				. " {$this->join} fm_responsibility ON fm_responsibility_contact.responsibility_id = fm_responsibility.id" 
				. ' WHERE fm_responsibility_contact.id='  . (int) $id;

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();

			$this->db->next_record();
			$values = array
				(
					'id'				=> $this->db->f('id'),
					'responsibility_id'	=> $this->db->f('responsibility_id'),
					'responsibility_name'=> $this->db->f('responsibility_name'),
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
					'ecodimb'			=> $this->db->f('ecodimb')
				);

			return $values;
		}

		/**
		 * Delete responsibility type contact
		 *
		 * @param integer $id ID of responsibility type
		 *
		 * @return void
		 */

		function delete_contact($id)
		{
			$this->db->query('DELETE FROM fm_responsibility_contact WHERE id='  . (int) $id, __LINE__, __FILE__);
		}


		/**
		 * Deactivate a responsibility type contact - leaving the information as history
		 *
		 * @param integer $id ID of responsibility type
		 *
		 * @return void
		 */

		function expire_contact($id)
		{
			$value_set['expired_by']	= $this->account;
			$value_set['expired_on']	= time();
			$value_set	= $this->db->validate_update($value_set);
			$this->db->transaction_begin();
			$this->db->query("UPDATE fm_responsibility_contact SET {$value_set} WHERE id = " . (int) $id, __LINE__, __FILE__);
			$this->db->transaction_commit();
		}

		/**
		 * Get the contact and relation id for a particular role at a given location
		 *
		 * @param array $location_code location_code
		 * @param array $role_id  role_id
		 *
		 * @return array
		 */

		public function get_active_responsible_at_location($location_code, $role_id)
		{
			$role_id = (int)$role_id;
			$time = time() +1;

			$sql = "SELECT fm_responsibility_contact.id, contact_id FROM fm_responsibility_contact"
				. " {$this->join} fm_responsibility ON fm_responsibility_contact.responsibility_id = fm_responsibility.id"
				. " {$this->join} fm_responsibility_role ON fm_responsibility.id = fm_responsibility_role.responsibility_id"
				. " WHERE fm_responsibility_role.id ={$role_id}"
				. " AND fm_responsibility_contact.location_code ='{$location_code}'"
				. " AND active_from < {$time} AND (active_to > {$time} OR active_to = 0) AND expired_on IS NULL";

			$values = array();
			$this->db->query($sql, __LINE__, __FILE__);

			if($this->db->next_record())
			{
				$values = array
					(
						'id'			=>  $this->db->f('id'),
						'contact_id'	=> $this->db->f('contact_id')
					);
			}
			return $values;

		}
		/**
		 * Get the responsibility for a particular category conserning a given location or item
		 * Locations are checked bottom up at the deepest level - before checkin on it's parent if it is a miss.
		 *
		 * @param array $data containing cat_id, location_code and optional item-information
		 *
		 * @return contact_id
		 */

		public function get_responsible($data = array())
		{
			$location_filter = array();

			$todo = false;
			$item_filter = '';

			if(isset($data['ecodimb']) && $data['ecodimb'])
			{
				$item_filter =   " AND ecodimb = '{$data['ecodimb']}'";
				$location_filter[] = '';
				$todo = true;
			}
			elseif(isset($data['extra']['p_entity_id']) && $data['extra']['p_entity_id'])
			{
				$location_code = implode('-', $data['location']);

				$item_filter =   " AND p_num = '{$data['extra']['p_num']}'"
					.' AND p_entity_id =' . (int) $data['extra']['p_entity_id']
					.' AND p_cat_id =' . (int) $data['extra']['p_cat_id'];

				$location_filter[] = " AND location_code = '{$location_code}'";
				$ordermethod = '';
				$todo = true;
			}
			else if(isset($data['location']) && $data['location'])
			{
				if(!is_array($data['location']))
				{
					throw new exception('soresponsible::get_responsible(): location-input needs to be an array');
				}

				$location_filter[] = ''; // when the responsibility is generic - not located to any location
				$location_code = '';
				$location_array = array();
				foreach ($data['location'] as $location)
				{
					$location_array[]	= $location;
					$location_code		= implode('-', $location_array);
					$location_filter[]	= "AND location_code $this->like '$location_code%'";
				}

				// Start at the bottom level
				$location_filter	= array_reverse($location_filter);				

				$ordermethod = ' ORDER by location_code.id ASC';
				$todo = true;
			}

			if( !$todo )
			{
				return 0;
			}

			$sql = "SELECT contact_id FROM fm_responsibility_contact"
				. " {$this->join} fm_responsibility ON fm_responsibility_contact.responsibility_id = fm_responsibility.id"
				. " {$this->join} fm_responsibility_module ON fm_responsibility.id = fm_responsibility_module.responsibility_id"
				. ' WHERE cat_id =' . (int) $data['cat_id']
				. ' AND active = 1 AND active_from < ' . time() . ' AND (active_to > ' . time() . ' OR active_to = 0) AND expired_on IS NULL'
				. " {$item_filter}";

			foreach ($location_filter as $filter_at_location)
			{
				$this->db->query($sql . $filter_at_location, __LINE__, __FILE__);
				$this->db->next_record();
				if($this->db->f('contact_id'))
				{
					return $this->db->f('contact_id');
				}
			}

			return 0;
		}

		/**
		 * Get the user_id for a particular contact
		 *
		 * @param integer $person_id the ID of the given contact
		 *
		 * @return user_id
		 */

		public function get_contact_user_id($person_id)
		{
			$person_id = (int) $person_id;
			$sql = "SELECT account_id FROM phpgw_accounts WHERE person_id ={$person_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return (int)$this->db->f('account_id');
		}

		/**
		 * Get the user_id for a particular responsibility at a location
		 *
		 * @param array 
		 *
		 * @return user_id
		 */

		public function get_responsible_user_id($data = array())
		{
			$responsibility_id = (int)$data['responsibility_id'];
			$location_code = $data['location_code'];
			$now = time();

			$sql = "SELECT contact_id FROM fm_responsibility_contact"
				. " {$this->join} fm_responsibility ON fm_responsibility_contact.responsibility_id = fm_responsibility.id"
				. " WHERE fm_responsibility.id = {$responsibility_id}"
				. " AND active_from < {$now}"
				. " AND expired_on IS NULL"
				. " AND location_code = '{$location_code}'";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record(); 
			$contact_id = $this->db->f('contact_id');
			return $this->get_contact_user_id($contact_id);
		}
	}
