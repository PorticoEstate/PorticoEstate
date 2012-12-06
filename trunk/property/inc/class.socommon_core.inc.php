<?php
	/**
	* Common so-functions, database related helpers 
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
	* @version $Id: class.socommon_core.inc.php 10127 2012-10-07 17:06:01Z sigurdne $
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */




	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */

	class property_socommon_core
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
			$this->custom		= createObject('property.custom_fields');
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

		protected function _edit($id, $value_set, $table)
		{
			$id = (int)$id;

			$value_set	= $this->_db->validate_update($value_set);

			$this->_db->transaction_begin();

			$sql = "UPDATE {$table} SET $value_set WHERE id= {$id}";

			try
			{
				$this->_db->Exception_On_Error = true;
				$this->_db->query($sql,__LINE__,__FILE__);
				$this->_db->Exception_On_Error = false;
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			$this->_db->transaction_commit();
			return $id;
		}


		/**
		 * Get standard valueset for atttibutes and location
		 *
		 * @param array $data the data to organize
		 *
		 * @return array $value_set to either insert or edit
		 */

		protected function _get_value_set($data)
		{
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

			$value_set['address'] = $address;

			return $value_set;
		}
	}
