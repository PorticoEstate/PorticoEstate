<?php
	/**
	* Setup
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @subpackage setup
	* @version $Id: tables_update.inc.php 17922 2007-02-07 20:37:22Z sigurdne $
	* @internal $Source$
	*/

	$test[] = '0.9.16.000';
	function preferences_upgrade0_9_16_000()
	{
		$GLOBALS['setup_info']['preferences']['currentver'] = '0.9.17.500';
		return $GLOBALS['setup_info']['preferences']['currentver'];
	}
?>
