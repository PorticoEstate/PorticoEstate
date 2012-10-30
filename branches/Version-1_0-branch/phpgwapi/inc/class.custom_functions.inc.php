<?php
	/**
	 * phpGroupWare custom functions
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @author Dave Hall dave.hall at skwashd.com
	 * @copyright Copyright (C) 2003-2006 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @package phpgroupware
	 * @subpackage phpgwapi
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
	   GNU Lesser General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * phpGroupWare custom functions
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 */
	class phpgwapi_custom_functions
	{

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

		/**
		* @var int $_total_records total number of records found
		*/
		protected $_total_records = 0;

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->_db           	=& $GLOBALS['phpgw']->db;
			$this->_join			=& $this->_db->join;
			$this->_like			=& $this->_db->like;
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
			if ( $varname == 'total_records' )
			{
				return $this->_total_records;
			}
		}

		/**
		 * Add a custom function
		 * 
		 * @param array $custom_function the data for the custom function
		 *
		 * @return integer the id of the function, -1 means invalid data and 0 means it wasn't added
		 */
		public function add($custom_function)
		{
			if(!$custom_function['location'] || !$custom_function['appname'])
			{
				return -1;
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($custom_function['appname'], $custom_function['location']);

			if ( isset($custom_function['active']) && $custom_function['active'] )
			{
				$custom_function['active'] = true;
			}

			$this->_db->transaction_begin();

			$custom_function['id'] = 1;
			$sql = 'SELECT MAX(id) as maximum FROM phpgw_cust_function'
				. " WHERE location_id = {$location_id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( $this->_db->next_record() )
			{
				$custom_function['id'] += $this->_db->f('maximum');
			}

			$custom_sort = 0;
			$sql = "SELECT MAX(custom_sort) as max_sort FROM phpgw_cust_function"
				. " WHERE location_id = {$location_id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( $this->_db->next_record() )
			{
				$custom_sort = $this->_db->f('max_sort') + 1;
			}

			$values = array
			(
				'location_id'	=> $location_id,
				'id'			=> (int) $custom_function['id'],
				'file_name'		=> $this->_db->db_addslashes($custom_function['custom_function_file']),
				'descr'			=> $this->_db->db_addslashes($custom_function['descr']),
				'active'		=> !!$custom_function['active'],
				'client_side'	=> !!$custom_function['client_side'],
				'custom_sort'	=> $custom_sort
			);

			$columns = implode(',', array_keys($values));
			$values	= $this->_db->validate_insert($values);

			$this->_db->query("INSERT INTO phpgw_cust_function({$columns}) "
				. "VALUES ({$values})", __LINE__, __FILE__);

			if ( $this->_db->transaction_commit() )
			{
				return $custom_function['id'];
			}

			return 0;
		}

		/**
		 * Delete a custom attribute or custom function
		 * 
		 * @param string $appname the application name
		 * @param string $location the location
		 * @param int $function_id the db pk of the function to delete
		 *
		 * @return bool was the function deleted?
		 */
		public function delete($appname, $location, $function_id)
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);
			$function_id = (int) $function_id;

			$this->_db->transaction_begin();

			$sql = 'SELECT custom_sort FROM phpgw_cust_function'
				. " WHERE location_id = {$location_id} AND id = {$function_id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( !$this->_db->next_record() )
			{
				// function has already been deleted
				return true;
			}

			$custom_sort = $this->_db->f('custom_sort');

			$sql = 'SELECT MAX(custom_sort) AS max_sort FROM phpgw_cust_function'
				. " WHERE location_id = {$location_id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			$this->_db->next_record();
			$max_sort	= $this->_db->f('max_sort');

			if ( $max_sort > $custom_sort )
			{
				$sql = 'UPDATE phpgw_cust_function SET custom_sort = (custom_sort - 1)'
					. " WHERE location_id = {$location_id} AND custom_sort > {$custom_sort}";
				$this->_db->query($sql, __LINE__, __FILE__);
			}

			$sql = 'DELETE FROM phpgw_cust_function'
				. " WHERE location_id = {$location_id} AND id = {$function_id}";
			$this->_db->query($sql, __LINE__, __FILE__);

			return $this->_db->transaction_commit();
		}

		/**
		* Edit a custom function
		*
		* @param array $custom_function function data
		*
		* @return bool was the function updated?
		*/
		public function edit($custom_function)
		{
			$id = (int) $custom_function['id'];

			if ( isset($custom_function['active']) ) 
			{
				$custom_function['active'] = !!$custom_function['active'];
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($custom_function['appname'], $custom_function['location']);

			$values = array
			(
				'descr'			=> $this->_db->db_addslashes($custom_function['descr']),
				'file_name'		=> $custom_function['custom_function_file'],
				'active'		=> $custom_function['active'],
				'client_side'	=> $custom_function['client_side'],
			);
			unset($custom_function);

			$vals = $this->_db->validate_update($values);

			$sql = "UPDATE phpgw_cust_function SET {$vals}" 
					. " WHERE location_id = {$location_id} AND id = {$id}";

			$this->_db->transaction_begin();

			$this->_db->query($sql, __LINE__, __FILE__);

			return $this->_db->transaction_commit();
		}

		/**
		 * Get a list of available custom functions
		 *
		 * @param array $data the search criteria
		 *
		 * @return array list of functions
		 */
		public function find($data = array())
		{
			$custom_functions = array();

			if ( !is_array($data) || !isset($data['appname']) || !isset($data['location']) )
			{
				return $custom_functions;
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($data['appname'], $data['location']);

			$start = 0;
			if ( isset($data['start']) )
			{
				$start = (int) $data['start'];
			}

			$query = '';
			if ( isset($data['query']) )
			{
				$query = $this->_db->db_addslashes($data['query']);
			}


			$ordermethod = ' ORDER BY custom_sort ASC';
			if ( isset($data['order']) && $data['order'] )
			{
				$data['sort'] = 'ASC';
				if ( isset($data['sort']) && $data['sort'] == 'DESC' )
				{
					$data['sort'] = 'DESC';
				}

				$data['order'] = $this->_db->db_addslashes($data['order']);
				$ordermethod = " ORDER BY {$data['order']} {$data['sort']}";
			}

			$allrows = false;
			if ( isset($data['allrows']) )
			{
				$allrows = !!$data['allrows'];
			}

			$querymethod = '';
			if($query)
			{
				$querymethod = " AND file_name {$this->_like} '%{$query}%'"
							. " OR descr {$this->_like} '%{$query}%'";
			}

			$sql = 'FROM phpgw_cust_function'
				. " WHERE location_id = {$location_id} {$querymethod}";

			$this->_total_records = 0;
			$this->_db->query("SELECT COUNT(*) AS cnt {$sql}", __LINE__,__FILE__);
			if ( $this->_db->next_record() )
			{
				$this->_total_records = $this->_db->f('cnt');
			}

			if ( true ) //$allrows )
			{
				$this->_db->query("SELECT * {$sql} {$ordermethod}", __LINE__, __FILE__);
			}
			else
			{
				$this->_db->limit_query("SELECT * {$sql} {$ordermethod}", $start, __LINE__, __FILE__);
			}

			while ( $this->_db->next_record() )
			{
				$id = $this->_db->f('id');
				$custom_functions[] = array
				(
					'id'			=> $id,
					'file_name'		=> $this->_db->f('file_name'),
					'sorting'		=> $this->_db->f('custom_sort'),
					'descr'			=> $this->_db->f('descr'),
					'active'		=> !!$this->_db->f('active'),
					'client_side'	=> !!$this->_db->f('client_side')
				);
			}

			return $custom_functions;
		}
		
		/**
		 * Fetch a single custom function record from the database
		 *
		 * @param string  $appname  the module the function belongs to
		 * @param string  $location the location the function is used
		 * @param integer $id       the ID for the function
		 *
		 * @return array the function values - null if not found
		 */
		public function get($appname, $location, $id)
		{
			$appname = $this->_db->db_addslashes($appname);
			$location = $this->_db->db_addslashes($location);
			$id = (int)$id;
			
			$sql = 'SELECT phpgw_cust_function.* FROM phpgw_cust_function '
				. " {$this->_join} phpgw_locations ON phpgw_cust_function.location_id = phpgw_locations.location_id"
				. " {$this->_join} phpgw_applications ON phpgw_applications.app_id = phpgw_locations.app_id"
				. " WHERE phpgw_applications.app_name = '{$appname}'"
					. " AND phpgw_locations.name = '{$location}'"
					. " AND phpgw_cust_function.id = {$id}";

			$this->_db->query($sql,__LINE__,__FILE__);

			if ( !$this->_db->next_record() )
			{
				return null;
			}

			return array
			(
				'id'					=> (int)$this->_db->f('id'),
				'descr'					=> $this->_db->f('descr', true),
				'custom_function_file'	=> $this->_db->f('file_name'),
				'active'				=> !!$this->_db->f('active'),
				'client_side'			=> !!$this->_db->f('client_side')
			);
		}

		/**
		 * Change the order functions
		 *
		 * @param integer $id       the function to reposition
		 * @param string  $resort   the direction to move the item - up/down
		 * @param string  $appname  the module for the function
		 * @param string  $location the location for the function
		 *
		 * @return bool was the item moved?
		 */
		public function resort($id, $resort, $appname, $location)
		{
			if ( !$location || !$appname )
			{
				return false;
			}

			$id		= (int)$id;

			if ( $resort == 'down' )
			{
				$resort = 'down';
			}
			else
			{
				$resort	= 'up';
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);

			$this->_db->transaction_begin();
			
			$sql = 'SELECT custom_sort FROM phpgw_cust_function'
				. " WHERE location_id = {$location_id} AND id = {$id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			$this->_db->next_record();
			$custom_sort = $this->_db->f('custom_sort');

			$sql = 'SELECT MAX(custom_sort) AS max_sort FROM phpgw_cust_function'
				. " WHERE location_id = {$location_id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			$this->_db->next_record();
			$max_sort = $this->_db->f('max_sort');

			$update = false;
			switch($resort)
			{
				case 'down':
					if($max_sort > $custom_sort)
					{
						$new_sort = $custom_sort + 1;
						$update = true;
					}
					break;

				case 'up':
				default:
					if($custom_sort>1)
					{
						$new_sort = $custom_sort - 1;
						$update = true;
					}
					break;
			}

			if ( !$update )
			{
				// nothing to be done - assume all is ok
				return true;
			}

			$sql = "UPDATE phpgw_cust_function SET custom_sort = {$custom_sort}"
				. " WHERE location_id = {$location_id}"
					. " AND custom_sort = {$new_sort}";
			$this->_db->query($sql,__LINE__,__FILE__);

			$sql = "UPDATE phpgw_cust_function SET custom_sort = {$new_sort}" 
				. " WHERE  location_id = {$location_id} AND id = {$id}";
			$this->_db->query($sql,__LINE__,__FILE__);

			return $this->_db->transaction_commit();
		}
	}
