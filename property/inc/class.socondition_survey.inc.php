<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @subpackage project
 	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */

	class property_socondition_survey
	{
		/**
		* @var int $_total_records total number of records found
		*/
		protected $_total_records = 0;


		/**
		* @var int $_receipt feedback on actions
		*/
		protected $_receipt = array();


		/**
		 * @var object $_db reference to the global database object
		 */
		protected $_db;

		/**
		 * @var string $_join SQL JOIN statement
		 */
		protected $_join;

		/**
		 * @var string $_like SQL LIKE statement
		 */
		protected $_like;


		public function __construct()
		{
			$this->account		= (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->_db			= & $GLOBALS['phpgw']->db;
			$this->_join		= & $this->_db->join;
			$this->_like		= & $this->_db->like;
			$this->custom 		= createObject('property.custom_fields');
		}

		/**
		 * Magic get method
		 *
		 * @param string $varname the variable to fetch
		 *
		 * @return mixed the value of the variable sought - null if not found
		 */
		public function __get($varname)
		{
			switch ($varname)
			{
				case 'total_records':
					return $this->_total_records;
					break;
				case 'receipt':
					return $this->_receipt;
					break;
				default:
					return null;
			}
		}

		function read($data = array())
		{
			$start		= isset($data['start'])  ? (int) $data['start'] : 0;
			$filter		= isset($data['filter']) ? $data['filter'] : 'none';
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '' ;
			$cat_id		= isset($data['cat_id']) ? (int)$data['cat_id'] : 0;
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';

			$table = 'fm_condition_survey';
			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by id DESC';
			}

			$where = 'WHERE';
			if ($cat_id)
			{
				$filtermethod .= " {$where} category = {$cat_id}";
				$where = 'AND';
			}

			if($query)
			{
				$query			= $this->_db->db_addslashes($query);
				$querymethod	= " {$where} name {$this->_like} '%{$query}%'";
			}

			$sql = "SELECT * FROM {$table} $filtermethod $querymethod";

			$this->_db->query($sql,__LINE__,__FILE__);
			$this->_total_records = $this->_db->num_rows();

			if(!$allrows)
			{
				$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->_db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$values = array();
			while ($this->_db->next_record())
			{
				$values[] = array
				(
					'id'			=> $this->_db->f('id'),
					'title'			=> $this->_db->f('title',true),
					'descr'			=> $this->_db->f('descr',true),
					'entry_date'	=> $this->_db->f('entry_date'),
					'user'			=> $this->_db->f('user_id')
				);
			}

			return $values;
		}

		function read_single($id, $data = array())
		{
			$table = 'fm_condition_survey';

			$id = (int) $id;
			$this->_db->query("SELECT * FROM {$table} WHERE id={$id}",__LINE__,__FILE__);

			$values = array();
			if ($this->_db->next_record())
			{
				$values = array
				(
					'id'				=> $id,
					'title'				=> $this->_db->f('title',true),
					'descr'				=> $this->_db->f('descr', true),
					'location_code'		=> $this->_db->f('location_code', true),
					'status_id'			=> (int)$this->_db->f('status_id'),
					'cat_id'			=> (int)$this->_db->f('category'),
					'vendor_id'			=> (int)$this->_db->f('vendor_id'),
					'coordinator'		=> (int)$this->_db->f('coordinator'),
					'report_date'		=> (int)$this->_db->f('report_date'),
					'user_id'			=> (int)$this->_db->f('user_id'),
					'entry_date'		=> (int)$this->_db->f('entry_date'),
					'modified_date'		=> (int)$this->_db->f('modified_date'),
				);

				if ( isset($data['attributes']) && is_array($data['attributes']) )
				{
					$values['attributes'] = $data['attributes'];
					foreach ( $values['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->db->f($attr['column_name']);
					}
				}
			}

			return $values;
		}


		function add($data)
		{

			$table = 'fm_condition_survey';

			$this->_db->transaction_begin();

			$id = $this->_db->next_id($table);

			$value_set = array
			(
				'id'				=> $id,
				'title'				=> $this->_db->db_addslashes($data['title']),
				'descr'				=> $this->_db->db_addslashes($data['descr']),
				'status_id'			=> (int)$data['status_id'],
				'category'			=> (int)$data['cat_id'],
				'vendor_id'			=> (int)$data['vendor_id'],
				'coordinator'		=> (int)$this->account,
				'report_date'		=> phpgwapi_datetime::date_to_timestamp($data['report_date']),
				'user_id'			=> $this->account,
				'entry_date'		=> time(),
				'modified_date'		=> time()
			);


			if(isset($data['location']) && is_array($data['location']))
			{
				foreach ($data['location'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$value_set[$input_name] = $value;
					}
				}
				$value_set['location_code'] = implode('-', $data['location']);
			}

			if(isset($data['extra']) && is_array($data['extra']))
			{
				foreach ($data['extra'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$value_set[$input_name] = $value;
					}
				}

				if($data['extra']['p_num'] && $data['extra']['p_entity_id'] && $data['extra']['p_cat_id'])
				{
					$entity	= CreateObject('property.soadmin_entity');
					$entity_category = $entity->read_single_category($data['extra']['p_entity_id'],$data['extra']['p_cat_id']);
				}
			}

			if(isset($values['attributes']) && is_array($values['attributes']))
			{
				$data_attribute = $this->custom->prepare_for_db($table, $values['attributes']);
				if(isset($data_attribute['value_set']))
				{
					foreach($data_attribute['value_set'] as $input_name => $value)
					{
						if(isset($value) && $value)
						{
							$value_set[$input_name] = $value;
						}
					}
				}
			}

			$_address = array();
			if(isset($data['street_name']) && $data['street_name'])
			{
				$_address[] = "{$data['street_name']} {$data['street_number']}";
			}

			if(isset($data['location_name']) && $data['location_name'])
			{
				$_address[] = $data['location_name'];
			}

			if(isset($data['additional_info']) && $data['additional_info'])
			{
				foreach($data['additional_info'] as $key => $value)
				{
					if($value)
					{
						$_address[] = "{$key}|{$value}";
					}
				}
			}

			if(isset($entity_category) && $entity_category)
			{
				$_address[] = "{$entity_category['name']}::{$data['extra']['p_num']}";
			}

			$address	= $this->_db->db_addslashes(implode('::', $_address));
	//		$value_set['address'] = $address;

			unset($_address);

			$cols = implode(',', array_keys($value_set));
			$values	= $this->_db->validate_insert(array_values($value_set));

			try
			{
				$this->_db->Exception_On_Error = true;
				$this->_db->query("INSERT INTO {$table} ({$cols}) VALUES ({$values})",__LINE__,__FILE__);
				$this->_db->Exception_On_Error = false;
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;				
				}
			}

			if($this->_db->transaction_commit())
			{
				return $id;
			}

			return 0;
		}

		function edit($data)
		{
			$table = 'fm_condition_survey';
			$id = (int)$data['id'];

			$value_set	= $this->db->validate_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$id}",__LINE__,__FILE__);

			if($this->_db->transaction_commit())
			{
				$this->_receipt['message'][] = array('msg'=>lang('survey %1 has been saved',$id));
			}
			return $id;
		}

		function delete($id)
		{
			$id = (int) $id;
			$table = 'fm_condition_survey';
			$this->_db->query("DELETE FROM $table WHERE id={$id}",__LINE__,__FILE__);
		}
	}
