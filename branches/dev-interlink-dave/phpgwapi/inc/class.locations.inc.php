<?php
	/**
	* phpGroupWare API - Locations
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License Version 3 or later
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.acl.inc.php 682 2008-02-01 12:19:55Z dave $
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
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
	* This can manage rights to 'run' applications, and limit certain features within an application.
	* It is also used for granting a user "membership" to a group, or making a user have the security 
	* equivilance of another user. It is also used for granting a user or group rights to various records,
	* such as todo or calendar items of another user.
	* @package phpgwapi
	* @subpackage accounts
	* @internal syntax: CreateObject('phpgwapi.acl',int account_id);
	* @internal example: $acl = createObject('phpgwapi.acl');  // user id is the current user
	* @internal example: $acl = createObject('phpgwapi.acl',10);  // 10 is the user id
	*/
	class phpgwapi_locations
	{
		/**
		* Database connection
		* @var object Database connection
		*/
		private $db;

		/**
		* @var string like ???
		*/
		private $like = 'LIKE';

		/**
		* @var string $join ???
		*/
		private $join = 'JOIN';

		/**
		* ACL constructor for setting account id
		*
		* Sets the ID for $account_id. Can be used to change a current instances id as well.
		* Some functions are specific to this account, and others are generic.
		* @param integer $account_id Account id
		*/
		public function __construct($account_id = 0)
		{	
			$this->db =& $GLOBALS['phpgw']->db;

			$this->like =& $this->db->like;
			$this->join =& $this->db->join;
		}

		/**
		* Get list of xmlrpc or soap functions
		*
		* @param string|array $_type Type of methods to list. Could be xmlrpc or soap
		* @return array Array with xmlrpc or soap functions. Might also be empty.
		* This handles introspection or discovery by the logged in client,
		* in which case the input might be an array.  The server always calls
		* this function to fill the server dispatch map using a string.
		*/
		public function list_methods($_type='xmlrpc')
		{
			// not (yet) implemented
			// TODO implement me
		}

		/**
		 * Add a location
		 * 
		 * @param string $location the name of the location
		 * @param string $description the description of the location - seen by users
		 * @param string $appname the name of the application for the location
		 * @return int the new location id
		 */
		 public function add($location, $descr, $appname = '', $allow_grant = true, $custom_tbl = '')
		 {
		 	if ( $appname === '' )
		 	{
		 		$appname = $GLOBALS['phpgw']['flags']['currentapp'];
		 	}

			$app = $GLOBALS['phpgw']->applications->name2id($appname);

		 	$location = $this->db->db_addslashes($location);
			$descr = $this->db->db_addslashes($descr);
		 	$allow_grant = (int) $allow_grant;

		 	$this->db->query('SELECT location_id FROM phpgw_locations'
		 			. " WHERE app_id = {$app} AND name = '{$location}'", __LINE__, __FILE__);

		 	if ( $this->db->next_record() )
			{
				return $this->db->f('location_id'); // already exists so just return the id
		 	}

		 	if ( $custom_tbl === '' )
		 	{
		 		$sql = 'INSERT INTO phpgw_locations (app_id, name, descr, allow_grant)'
		 			. " VALUES ({$app}, '{$location}', '{$descr}', {$allow_grant})";
		 	}
		 	else
		 	{
		 		$custom_tbl = $this->db->db_addslashes($custom_tbl);
		 		$sql = 'INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_attrib, c_attrib_table)'
		 			. " VALUES ({$app}, '{$location}', '{$descr}', {$allow_grant}, 1, '{$custom_tbl}')";
		 	}
			$this->db->query($sql, __LINE__, __FILE__);
			
			return $this->db->last_insert_id('phpgw_locations', 'location_id');
		 }

		/**
		* Deletes an ACL and all associated grants/masks for that location
		*
		* @param string $appname the application name
		* @param string $location the location
		* @param bool $remove_table remove the associate custom attributes table if it exists
		* @return bool was the location found and deleted?
		*/
		public function delete($appnane, $location, $drop_table = true)
		{
			$app = $GLOBALS['phpgw']->applications->name2id($appname);
			$location = $this->db->db_addslashes($location);

			$this->db->query('SELECT c_attrib_table FROM phpgw_locations'
				. " WHERE app_id = {$app} AND name = '{$location}'", __LINE__, __FILE__);
			if ( !$this->db->next_record() )
			{
				return false; //invalid location
			}

			$tbl = $this->db->f('c_attrib_table');
			
			$oProc = createObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$oProc->m_odb =& $this->db;
			$Proc->m_odb->Halt_On_Error = 'report';

			$this->db->transaction_begin();

			if ( $drop_table )
			{
				$oProc->DropTable($tbl);
			}

			$this->db->query('DELETE FROM phpgw_locations'
				. " WHERE app_id = {$app}"
					. " AND name = '{$location}'", __LINE__, __FILE__);

			$this->delete_repository($appname, $location);

			$this->db->transaction_commit();

			return true;
		}

		/**
		 * Update the description of a location
		 * 
		 * @param string $location location within application
		 * @param string $description the description of the location - seen by users
		 * @param string $appname the name of the application for the location
		 */
		public function update_description($location, $description, $appname = '')
		{
		 	if ( $appname === '' )
		 	{
		 		$appname = $GLOBALS['phpgw']['flags']['currentapp'];
		 	}

		 	$location = $this->db->db_addslashes($location);
			$description = $this->db->db_addslashes($description);
		 	$appname = $this->db->db_addslashes($appname);

		 	$this->db->query('UPDATE phpgw_locations, phpgw_applications'
		 			. " SET descr = '{$description}'"
		 			. ' WHERE phpgw_locations.app_id = phpgw_applications.app_id '
						. " AND phpgw_applications.appname = '{$appname}'"
						. " AND phpgw_locations.name = '{$location}'", __LINE__, __FILE__);
			return $this->db->rows_affected() == 1;
		}

		/**
		* This does something
		*
		* @param ??? $apps ???
		* @return ???
		*/
		public function verify($apps, $location = '.')
		{
			$location = $this->db->db_addslashes($location);

			if ( !is_array($apps) )
			{
				$apps = array();
			}

			foreach ( $apps as $appname => $values )
			{
				$appname = $this->db->db_addslashes($appname);
				$sql = 'SELECT phpgw_applications.name'
					. ' FROM phpgw_applications'
					. " {$this->join} phpgw_locations ON phpgw_applications.app_id = phpgw_locations.app_id"
					. " WHERE phpgw_applications.name = '{$appname}'"
						. " AND phpgw_locations.name = '{$location}'";
				$this->db->query($sql ,__LINE__,__FILE__);

				if ($this->db->num_rows()==0)
				{
					$top = (int) $value['top_grant'];
					$app_id = $GLOBALS['phpgw']->applications->name2id($appname);
					$sql = 'INSERT INTO phpgw_locations (app_id, name, descr, allow_grant)'
						. " VALUES ({$app_id}, '{$location}', 'Top', {$top})";
					$this->db->query($sql ,__LINE__,__FILE__);
				}
			}
		}
	}
