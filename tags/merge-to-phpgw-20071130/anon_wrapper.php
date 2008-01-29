<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id: anon_wrapper.php 15834 2005-04-15 13:19:15Z powerstat $
	* @todo Limit which users can access this program (ACL check)
	* @todo Global disabler
	* @todo Detect bad logins and passwords, spit out generic message
	*/

	// If your are going to use multiable accounts, remove the following lines
	$login  = 'anonymous';
	$passwd = 'anonymous';

	$GLOBALS['phpgw_info']['flags'] = array(
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

	$sessionid = $GLOBALS['phpgw']->session->create($login,$passwd,'text');
	$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/index.php'));
?>
