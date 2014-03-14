<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @category utilities
 	 * @version $Id: class.odt2xhtml.inc.php 10127 2012-10-07 17:06:01Z sigurdne $
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
	* Document me!
	*
	* @package phpgwapi
	* @subpackage utilities
	*/
	if ( !isset($GLOBALS['phpgw_info']['server']['temp_dir'])  
			|| !is_dir($GLOBALS['phpgw_info']['server']['temp_dir']) )
	{
		if ( substr(PHP_OS, 3) == 'WIN' )
		{
			$GLOBALS['phpgw_info']['server']['temp_dir'] = 'c:/temp';
		}
		else
		{
			$GLOBALS['phpgw_info']['server']['temp_dir'] = '/tmp/';
		}
	}

	/**
	* Include the odt2xhtml class
	* @see odt2xhtml
	*/

	require_once PHPGW_API_INC . '/odt2xhtml/index.php5';
