<?php
	/**
	* VFS class loader
	* @copyright Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage vfs
	* @version $Id: class.vfs.inc.php 16964 2006-08-16 14:18:45Z skwashd $
	*/

	if ( !isset($GLOBALS['phpgw_info']['server']['file_repository']) 
		|| empty($GLOBALS['phpgw_info']['server']['file_repository']) )
	{
		$GLOBALS['phpgw_info']['server']['file_repository'] = 'sql';
	}

	/**
	* Include shared vfs class
	*/
	include (PHPGW_API_INC . '/class.vfs_shared.inc.php');
	/**
	* Include vfs class
	*/
	include (PHPGW_API_INC . '/class.vfs_' . $GLOBALS['phpgw_info']['server']['file_repository'] . '.inc.php');
?>
