<?php

	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
 	* @version $Id$
	*/

	/**
	 * Start page
	 *
	 * This script will check if there is defined a startpage in the users
	 * preferences - and then forward the user to this page
	 */

	$currentapp='sms';

	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader'   => true,
		'nonavbar'   => true,
		'currentapp'	=> $currentapp
		);

	include('../header.inc.php');

	$start_page = 'sms.index';

	if ( isset($GLOBALS['phpgw_info']['user']['preferences']['sms']['default_start_page'])
		&& $GLOBALS['phpgw_info']['user']['preferences']['sms']['default_start_page'] )
	{
		$start_page = $GLOBALS['phpgw_info']['user']['preferences']['sms']['default_start_page'];
	}

	$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => "sms.ui{$start_page}"));
