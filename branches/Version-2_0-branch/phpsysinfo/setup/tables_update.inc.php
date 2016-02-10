<?php
	/**
	* phpsysinfo - Setup
	*
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpsysinfo
	* @subpackage setup
	* @version $Id: tables_update.inc.php 4732 2010-02-04 13:16:56Z sigurd $
	*/


	/**
	* Update from 1.7 to 3.0
	*
	* @return string New version number
	*/

	$test[] = '1.7';
	function phpsysinfo_upgrade1_7()
	{
		$GLOBALS['setup_info']['phpsysinfo']['currentver'] = '3.0';
		return $GLOBALS['setup_info']['phpsysinfo']['currentver'];
	}

	/**
	* Update from 3.0 to 3.0.4
	*
	* @return string New version number
	*/

	$test[] = '3.0';
	function phpsysinfo_upgrade3_0()
	{
		$GLOBALS['setup_info']['phpsysinfo']['currentver'] = '3.0.4';
		return $GLOBALS['setup_info']['phpsysinfo']['currentver'];
	}

	/**
	* Update from 3.0.4 to 3.1.7
	*
	* @return string New version number
	*/

	$test[] = '3.0.4';
	function phpsysinfo_upgrade3_0_4()
	{
		$GLOBALS['setup_info']['phpsysinfo']['currentver'] = '3.1.7';
		return $GLOBALS['setup_info']['phpsysinfo']['currentver'];
	}
