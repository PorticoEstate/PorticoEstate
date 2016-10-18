<?php
	/**
	 * phpGroupWare - Event Planner.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package hrm
	 * @version $Id: index.php 14728 2016-02-11 22:28:46Z sigurdne $
	 */
	/**
	 * Start page
	 *
	 * This script will check if there is defined a startpage in the users
	 * preferences - and then forward the user to this page
	 */
	$currentapp = 'eventplanner';


	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader' => True,
		'nonavbar' => True,
		'currentapp' => $currentapp
	);

	include('../header.inc.php');

	$start_page = (isset($GLOBALS['phpgw_info']['user']['preferences'][$currentapp]['default_start_page']) ? $GLOBALS['phpgw_info']['user']['preferences'][$currentapp]['default_start_page'] : '');

	if ($start_page)
	{
		$start_page = array('menuaction' => $currentapp . '.ui' . $start_page . '.index');
	}
	else
	{
		$start_page = array('menuaction' => $currentapp . '.uiapplication.index');
	}

	$GLOBALS['phpgw']->redirect_link('/index.php', $start_page);
?>
