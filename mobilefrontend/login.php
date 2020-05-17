<?php
	/**
	 * phpGroupWare
	 *
	 * phpgroupware base
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @version $Id$
	 */
	require_once '../phpgwapi/inc/class.login.inc.php';

	$GLOBALS['phpgw_info']['flags'] = array(
		'custom_frontend' => 'mobilefrontend',
		'session_name' => 'mobilefrontendsession'
	);
	
	
	
	/**
	 * ID-porten for å komme inn på siden
	 * Brukere som har en rolle i systemet blir logget på som normalt
	 * andre (eksterne leverandører) logges på med anonym systembruker med svært få rettigheter
	 * 
	 */

	$phpgwlogin = new phpgwapi_login;
	$anonymous = true;
	$phpgwlogin->login('mobilefrontend', $anonymous);

