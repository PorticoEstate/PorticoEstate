<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
 	* @version $Id: class.uidebug_json.inc.php 1973 2008-12-18 23:18:49Z cesar $
	*/

	//phpgw::import_class('phpgwapi.yui');

	/**
	 * Description
	 * @package property
	 */

	class property_debug_json
	{
		var $public_functions = array
		(
			'index' 		=> true
		);

		function property_debug_json()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->acl 				= & $GLOBALS['phpgw']->acl;
			$this->acl_location		= '.debug_json';

		}

		function index()
		{
			echo "hello";
			//_debug_array(phpgwapi_cache::session_get($GLOBALS['phpgw_info']['flags']['currentapp'],"id_debug"));

		}
	}

