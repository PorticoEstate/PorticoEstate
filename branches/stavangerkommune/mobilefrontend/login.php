<?php

	/**
	 * phpGroupWare
	 *
	 * phpgroupware base
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @version $Id: login.php 11511 2013-12-08 20:57:07Z sigurdne $
	 */

	require_once '../phpgwapi/inc/class.login.inc.php';

	$phpgwlogin = new phpgwapi_login;
	$phpgwlogin->login('mobilefrontend');

