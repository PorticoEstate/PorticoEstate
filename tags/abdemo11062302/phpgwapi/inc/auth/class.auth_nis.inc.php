<?php
	/**
	* Authentication based on NIS maps
	* @author Dylan Adams <dadams@jhu.edu>
	* @copyright Copyright (C) 2001 Dylan Adams
	* @copyright Portions Copyright (C) 2004 - 2008 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
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
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Authentication based on NIS maps
	*
	* @package phpgwapi
	* @subpackage accounts
	* @ignore
	*/
	class phpgwapi_auth_nis extends phpgwapi_auth_
	{
		
		function __construct()
		{
			parent::__construct();
		}
		
		function authenticate($username, $passwd)
		{
			$domain = yp_get_default_domain();
			if( !empty($GLOBALS['phpgw_info']['server']['nis_domain']) )
			{
				$domain = $GLOBALS['phpgw_info']['server']['nis_domain'];
			}

			$map = "passwd.byname";
			if( !empty($GLOBALS['phpgw_info']['server']['nis_map']) )
			{
				$map = $GLOBALS['phpgw_info']['server']['nis_map'];
			}
			$entry = yp_match( $domain, $map, $username );

			/*
			 * we assume that the map is structured in the usual
			 * unix passwd flavor
			 */
			$entry_array = explode( ':', $entry );
			$stored_passwd = $entry_array[1];

			$encrypted_passwd = crypt( $passwd, $stored_passwd );

			return( $encrypted_passwd == $stored_passwd );
		}

		function change_password($old_passwd, $new_passwd, $account_id = '')
		{
			// can't change passwords unless server runs as root (bad idea)
			return '';
		}
	}
