<?php
	/**
	* Filemanager setup
	*
	* @copyright Copyright (C) 2002-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @subpackage setup
	* @version $Id$
	*/

	// OLD FILEMANAGER CHANGES MOVED TO API - long time ago
	$test[] = '0.9.13.005';
	
	/**
	 * Upgrade from 0.9.13.005 to 0.9.14.500
	 * 
	 * @return string New version string
	 */
	function filemanager_upgrade0_9_13_005()
	{
		$sql = "UPDATE phpgw_acl SET acl_appname='filemanager' WHERE acl_appname='phpwebhosting'";
		$GLOBALS['phpgw_setup']->oProc->query($sql);
		$GLOBALS['setup_info']['filemanager']['currentver'] = '0.9.14.500';
		return $GLOBALS['setup_info']['filemanager']['currentver'];
	}

	$test[] = '0.9.14.500';

	/**
	 * Upgrade from 0.9.14.500 to 0.9.17.500
	 * 
	 * @return string New version string
	 */
	function filemanager_upgrade0_9_14_500()
	{
		return $GLOBALS['setup_info']['filemanager']['currentver'] = '0.9.17.500';
	}
?>
