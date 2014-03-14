<?php
	/**
	* phpGroupWare - mobilefrontend.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package mobilefrontend
	* @subpackage setup
 	* @version $Id: tables_update.inc.php 11048 2013-04-10 10:22:37Z sigurdne $
	*/

	/**
	* Update mobilefrontend version from '0.1.1' to '0.1.2';
	*/

	$test[] = '0.1.1';
	function mobilefrontend_upgrade0_1_1()
	{
		$GLOBALS['setup_info']['mobilefrontend']['currentver'] = '0.1.2';
		return $GLOBALS['setup_info']['mobilefrontend']['currentver'];
	}

