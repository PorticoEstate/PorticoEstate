<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	* @todo Limit which users can access this program (ACL check)
	* @todo Detect bad logins and passwords, spit out generic message
	*/

	exit;

	// If your are going to use multiable accounts, remove the following lines
	$login  = 'anonymous';
	$passwd = 'anonymous';

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_Template_class' => True,
		'login' => True,
		'currentapp' => 'login',
		'noheader'  => True
	);
	
	/**
	* Include phpgroupware header
	*/
	include_once('./header.inc.php');

	// If your are going to use multiable accounts, remove the following lines 
	// You must create the useraccount and check its permissions before use 

	$login  = 'anonymous'; 
	$passwd = 'anonymous'; 

	$sessionid = $GLOBALS['phpgw']->session->create($login, $passwd);
	$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php'));
