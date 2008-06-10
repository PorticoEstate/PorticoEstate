<?php
	/**
	* Inter module data linking manager for phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003 -2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3 or later
	* @version $Id$
	* @package phpgwapi
	* @subpackage utility
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
	* Finding acl-locations
	* @todo move this into acl
	*
	* @package phpgwapi
	* @subpackage utility
	*/

	class soadmin_acl
	{
		/**
		* Constructor
		*
		* @return void
		*/

		function __construct()
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->_db =& $GLOBALS['phpgw']->db;
			$this->_join =& $this->_db->join;
		}

		/**
		* Find locations within an application
		*
		* @param bool   $grant          Used for finding locations where users can grant rights to others
		* @param string $appname        Name of application in question
		* @param bool   $allow_c_attrib Used for finding locations where custom attributes can be applied
		*
		* @return array Array locations
		*/

		function select_location($grant = false, $appname = '', $allow_c_attrib = false)
		{
			$location = array();
			
			if ( !$appname )
			{
				$appname = $this->currentapp;
			}
			$appname = $this->_db->db_addslashes($appname);
			
			$filter = " WHERE app_name='{$appname}' AND phpgw_locations.name != 'run'";
			
			if($allow_c_attrib)
			{
				$filter .= ' AND allow_c_attrib = 1';
			}

			if($grant)
			{
				$filter .= ' AND allow_grant = 1';
			}

			$sql = "SELECT location_id, phpgw_locations.name, phpgw_locations.descr FROM phpgw_locations"
				. " $this->_join phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " $filter ORDER BY location_id";

			$this->_db->query($sql, __LINE__, __FILE__);
			
			$location = array();
			while ($this->_db->next_record())
			{
				$location[$this->_db->f('name')] = $this->_db->f('descr', true);
			}
			return $location;
		}
	}
