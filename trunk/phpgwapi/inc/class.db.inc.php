<?php
	/**
	* Database abstraction class
	* @author NetUSE AG Boris Erdmann, Kristian Koehntopp
   	* @author Dan Kuykendall, Dave Hall and others
   	* @author Sigurd Nes
	* @copyright Copyright (C) 1998-2000 NetUSE AG Boris Erdmann, Kristian Koehntopp
	* @copyright Portions Copyright (C) 2001-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @link http://www.sanisoft.com/phplib/manual/DB_sql.php
	* @package phpgwapi
	* @subpackage database
	* @version $Id$
	*/

	if ( empty($GLOBALS['phpgw_info']['server']['db_type']) )
	{
		$GLOBALS['phpgw_info']['server']['db_type'] = 'mysql';
	}

	if ( empty($GLOBALS['phpgw_info']['server']['db_abstraction']) )
	{
		require_once PHPGW_API_INC . '/class.db_pdo.inc.php';
	}
	else
	{
		require_once PHPGW_API_INC . "/class.db_{$GLOBALS['phpgw_info']['server']['db_abstraction']}.inc.php";	
	}
