<?php
	/**
	* Setup
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package admin
	* @subpackage setup
	* @version $Id$
	* @internal $Source$
	*/

	$test[] = '0.9.16.000';
	function admin_upgrade0_9_16_000()
	{
		$GLOBALS['setup_info']['admin']['currentver'] = '0.9.17.001';
		return $GLOBALS['setup_info']['admin']['currentver'];
	}
?>
