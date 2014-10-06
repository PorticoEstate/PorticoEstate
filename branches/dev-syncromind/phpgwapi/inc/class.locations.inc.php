<?php
	/**
	* phpGroupWare API - Locations
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	* @package phpgroupware
	* @subpackage phpgwapi
	* @version $Id$
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

		You should have received a copy of the GNU Lesser General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* phpGroupWare API - Locations
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	*/
	class phpgwapi_locations
	{
		/**
		* @var object $_db Database connection
		*/
		protected $_db;

		/**
		* @var string $_join syntax for database joins
		*/
		protected $_join = 'JOIN';

		/**
		* @var string $_like syntax for like clause in queries
		*/
		protected $_like = 'LIKE';

		/**
		* Constructor
		*
		* @return null
		*/
		public function __construct()
		{
			$this->_db =& $GLOBALS['phpgw']->db;
			$this->_join =& $this->_db->join;
			$this->_like =& $this->_db->like;
		}

		/**
		 * Get list of xmlrpc or soap functions
		 *
		 * @param string $_type Type of methods to list. Could be xmlrpc or soap
		 *
		 * @return array Array with xmlrpc or soap functions. Might also be empty.
		 */
		public function list_methods($_type = 'xmlrpc')
		{
			// TODO implement me
			return array();
		}

		/**
		 * Add a location
		 *
		 * @param string  $location    the name of the location
		 * @param string  $descr       the description of the location - seen by users
		 * @param string  $appname     the name of the application for the location
		 * @param boolean $allow_grant allow grants on the location
		 * @param string  $custom_tbl  table associated with location
		 * @param boolean $c_function allow custom funtion on the location
		 * @param boolean $c_attrib allow custom attrib on the location
		 *
		 * @return int the new location id
		 */
		public function add($location, $descr, $appname, $allow_grant = true, $custom_tbl = null, $c_function = false, $c_attrib = false)
		{
			$app = $GLOBALS['phpgw']->applications->name2id($appname);

		 	$location = $this->_db->db_addslashes($location);
			$descr = $this->_db->db_addslashes($descr);
		 	$allow_grant = (int) $allow_grant;
		 	$c_function = (int) $c_function;

		 	$this->_db->query('SELECT location_id FROM phpgw_locations'
		 			. " WHERE app_id = {$app} AND name = '{$location}'", __LINE__, __FILE__);

		 	if ( $this->_db->next_record() )
			{
				return $this->_db->f('location_id'); // already exists so just return the id
		 	}

			if($custom_tbl)
			{
		 		$custom_tbl = $this->_db->db_addslashes($custom_tbl);
				$c_attrib = 1;
			}

			$value_set = array
			(
				'app_id'			=> $app,
				'name'				=> $location,
				'descr'				=> $descr,
				'allow_grant'		=> $allow_grant,
				'allow_c_attrib'	=> $c_attrib ? 1 : false,
				'c_attrib_table'	=> $custom_tbl,
				'allow_c_function'	=> $c_function
			);

			$cols = implode(',', array_keys($value_set));
			$values	= $this->_db->validate_insert(array_values($value_set));

			$this->_db->query("INSERT INTO phpgw_locations ({$cols}) VALUES ({$values})",__LINE__,__FILE__);

			return $this->_db->get_last_insert_id('phpgw_locations', 'location_id');
		}

		/**
		 * Deletes an ACL and all associated grants/masks for that location
		 *
		 * @param string  $appname    the application name
		 * @param string  $location   the location
		 * @param boolean $drop_table remove the associated custom attributes table if it exists
		 *
		 * @return boolean was the location found and deleted?
		 */
		public function delete($appname, $location, $drop_table = true)
		{
			$app = $GLOBALS['phpgw']->applications->name2id($appname);
			$location = $this->_db->db_addslashes($location);

			$sql = 'SELECT c_attrib_table FROM phpgw_locations'
				. " WHERE app_id = {$app} AND name = '{$location}'";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( !$this->_db->next_record() )
			{
				return false; //invalid location
			}

			$tbl = $this->_db->f('c_attrib_table');

			$this->_db->transaction_begin();

			// if there is a table and the user wants it dropped we drop it
			if ( $tbl && $drop_table )
			{
				$oProc = createObject('phpgwapi.schema_proc',
							$GLOBALS['phpgw_info']['server']['db_type']);

				$oProc->m_odb =& $this->_db;
				$Proc->m_odb->Halt_On_Error = 'report';

				$oProc->DropTable($tbl);
			}

			$GLOBALS['phpgw']->acl->delete_repository($appname, $location);

			$sql = 'DELETE FROM phpgw_locations'
				. " WHERE app_id = {$app} AND name = '{$location}'";
			$this->_db->query($sql, __LINE__, __FILE__);

			$this->_db->transaction_commit();

			return true;
		}

		/**
		 * Get the custom attributes table name for a given location
		 *
		 * @param string $appname  the application name for the location
		 * @param string $location the location name
		 *
		 * @return string the name of the table - not found returns an empty string
		 */
		public function get_attrib_table($appname, $location)
		{
			$appname  = $this->_db->db_addslashes($appname);
			$location = $this->_db->db_addslashes($location);

			$sql = 'SELECT c_attrib_table '
					. ' FROM phpgw_locations '
					. " {$this->_join} phpgw_applications ON phpgw_applications.app_id = phpgw_locations.app_id"
					. " WHERE phpgw_applications.app_name = '{$appname}'"
						. " AND phpgw_locations.name = '{$location}'";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( $this->_db->next_record() )
			{
				return $this->_db->f('c_attrib_table');
			}

			return '';
		}

		/**
		 * Get the ID of a location
		 *
		 * @param string $appname  the name of the module being looked up
		 * @param string $location the location within the module to look up
		 *
		 * @return integer the location id - 0 = not found
		 */
		public function get_id($appname, $location)
		{
			static $map = array();

			if ( isset($map[$appname][$location]) )
			{
				return $map[$appname][$location];
			}

			$map[$appname][$location] = 0;

			$appname  = $this->_db->db_addslashes($appname);
			$location = $this->_db->db_addslashes($location);

			$sql = 'SELECT location_id '
					. ' FROM phpgw_locations '
					. " {$this->_join} phpgw_applications ON phpgw_applications.app_id = phpgw_locations.app_id"
					. " WHERE phpgw_applications.app_name = '{$appname}'"
						. " AND phpgw_locations.name = '{$location}'";

			$this->_db->query($sql, __LINE__, __FILE__);
			if ( $this->_db->next_record() )
			{
				$map[$appname][$location] = $this->_db->f('location_id');
			}
			else
			{
//				throw new Exception("get_id ({$appname}, {$location}) returned 0");
			}
			return $map[$appname][$location];
		}

		/**
		 * Get the name of a location - useful for testing
		 *
		 * @param integer $location_id the location id to look up
		 *
		 * @return array the location - empty if not found
		 */
		public function get_name($location_id)
		{
			$location_id = (int) $location_id;

			$sql = 'SELECT phpgw_applications.app_name, phpgw_locations.name, phpgw_locations.descr'
					. ' FROM phpgw_locations '
					. " {$this->_join} phpgw_applications ON phpgw_applications.app_id = phpgw_locations.app_id"
					. " WHERE phpgw_locations.location_id = {$location_id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			if ( $this->_db->next_record() )
			{
				return array
				(
					'appname'	=> $this->_db->f('app_name', true),
					'location'	=> $this->_db->f('name', true),
					'descr'		=> $this->_db->f('descr', true)
				);
			}

			return array();
		}

		/**
		 * Get a list of sub locations for a give location
		 *
		 * @param string $appname  the name of the module being looked up
		 * @param string $location the location within the module to look up
		 *
		 * @return array map of locations (id => namne)
		 */
		public function get_subs($appname, $location)
		{
			static $map = array();

			if ( isset($map[$appname][$location]) )
			{
				return $map[$appname][$location];
			}

			$map[$appname][$location] = array();

			$entries =& $map[$appname][$location]; 
			$appname  = $this->_db->db_addslashes($appname);
			$location = $this->_db->db_addslashes($location);

			$sql = 'SELECT phpgw_locations.location_id, phpgw_locations.name'
				. ' FROM phpgw_locations, phpgw_applications'
				. ' WHERE phpgw_locations.app_id = phpgw_applications.app_id'
					. " AND phpgw_locations.name {$this->_like} '{$location}%'"
					. " AND phpgw_locations.name != '{$location}'"
					. " AND phpgw_applications.app_name='{$appname}'";
					
			$this->_db->query($sql, __LINE__, __FILE__);

			while ( $this->_db->next_record() )
			{
				$entries[$this->_db->f('location_id')] = $this->_db->f('name', true);
			}
			return $entries;
		}
		
		/**
		 * Get a list of locations that matches a given location pattern
		 *
		 * @param string $appname  the name of the module being looked up
		 * @param string $location the location within the module to look up
		 *
		 * @return array map of locations (id => namne)
		 */
		public function get_subs_from_pattern($appname, $pattern)
		{
			$appname  = $this->_db->db_addslashes($appname);
			$pattern  = $this->_db->db_addslashes($pattern);

			$sql = 'SELECT phpgw_locations.location_id, phpgw_locations.name, phpgw_locations.descr'
				. ' FROM phpgw_locations, phpgw_applications'
				. ' WHERE phpgw_locations.app_id = phpgw_applications.app_id'
					. " AND phpgw_locations.name {$this->_like} '{$pattern}'"
					. " AND phpgw_applications.app_name='{$appname}'";
			$this->_db->query($sql, __LINE__, __FILE__);
			while ( $this->_db->next_record() )
			{
				$entries[] = array(
					'location_id' => $this->_db->f('location_id'), 
					'name' => $this->_db->f('name', true),
					'descr' => $this->_db->f('descr',true)
				);
			}
			return $entries;
		}

		/**
		 * Update the description of a location
		 *
		 * @param string $location location within application
		 * @param string $descr    the description of the location - seen by users
		 * @param string $appname  the name of the application for the location
		 *
		 * @return boolean was the record updated?
		 */
		public function update_description($location, $descr, $appname)
		{
		 	$location	= $this->_db->db_addslashes($location);
			$descr		= $this->_db->db_addslashes($descr);
		 	$appname	= $this->_db->db_addslashes($appname);
		 	
		 	$location_id = $this->get_id($appname, $location);

		 	$this->_db->query("UPDATE phpgw_locations SET descr = '{$descr}'"
					. " WHERE phpgw_locations.location_id = {$location_id}", __LINE__, __FILE__);
			return $this->_db->affected_rows() == 1;
		}

		/**
		* Check that top level location are present for installed apps
		* If the location is missing - it will be inserted
		*
		* @param array    $apps apps to check
		* @param string   $location the location name to check
		*
		* @return void
		*/
		public function verify($apps, $location = '.')
		{
			$location = $this->_db->db_addslashes($location);

			if ( !is_array($apps) )
			{
				$apps = array();
			}

			foreach ( $apps as $appname => $values )
			{
				$appname = $this->_db->db_addslashes($appname);
				$app_id = $GLOBALS['phpgw']->applications->name2id($appname);
				if( $app_id > 0 )
				{
					$sql = 'SELECT name FROM phpgw_locations'
						. " WHERE app_id = {$app_id} AND name = '{$location}'";

					$this->_db->query($sql, __LINE__, __FILE__);

					if ( !$this->_db->next_record() )
					{
						$top = (int) $values['top_grant'];

						$sql = 'INSERT INTO phpgw_locations (app_id, name, descr, allow_grant)'
							. " VALUES ({$app_id}, '{$location}', 'Top', {$top})";

						$this->_db->query($sql, __LINE__, __FILE__);
					}
				}
			}
		}

		/**
		* Find locations within an application
		*
		* @param bool   $grant          Used for finding locations where users can grant rights to others
		* @param string $appname        Name of application in question
		* @param bool   $allow_c_attrib Used for finding locations where custom attributes can be applied
		* @param bool   $have_categories for finding locations which have categories
		*
		* @return array Array locations
		*/

		public function get_locations($grant = false, $appname = '', $allow_c_attrib = false, $c_function = false, $have_categories = false)
		{
			if ( !$appname )
			{
		 		$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$appname = $this->_db->db_addslashes($appname);
			
			$filter = " WHERE app_name='{$appname}' AND phpgw_locations.name != 'run'";
			
			$join_categories = '';
			if($have_categories)
			{
				$join_categories = "{$this->_join} phpgw_categories ON phpgw_locations.location_id = phpgw_categories.location_id";
			}
			
			if($allow_c_attrib)
			{
				$filter .= ' AND allow_c_attrib = 1';
			}

			if($grant)
			{
				$filter .= ' AND allow_grant = 1';
			}

			if($c_function)
			{
				$filter .= ' AND c_function = 1';
			}

			$sql = "SELECT phpgw_locations.location_id, phpgw_locations.name, phpgw_locations.descr FROM phpgw_locations"
				. " {$this->_join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " {$join_categories}"
				. " $filter ORDER BY phpgw_locations.name";

			$this->_db->query($sql, __LINE__, __FILE__);
			
			$locations = array();
			while ($this->_db->next_record())
			{
				$locations[$this->_db->f('name')] = $this->_db->f('descr', true);
			}
			return $locations;
		}
	}
