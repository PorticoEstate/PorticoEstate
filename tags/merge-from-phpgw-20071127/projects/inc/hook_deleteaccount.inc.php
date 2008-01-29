<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: hook_deleteaccount.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	* $Source: /sources/phpgroupware/projects/inc/hook_deleteaccount.inc.php,v $
	*/

	// Delete all records for a user
	$pro = CreateObject('projects.boprojects');

	if(intval($_POST['new_owner']) == 0)
	{
		$pro->delete_project(intval($_POST['account_id']),0,'account');
	}
	else
	{
		$pro->change_owner(intval($_POST['account_id']),intval($_POST['new_owner']));
	}
?>
