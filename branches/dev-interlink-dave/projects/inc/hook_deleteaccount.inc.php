<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id$
	* $Source: /sources/phpgroupware/projects/inc/hook_deleteaccount.inc.php,v $
	*/

	$account_id = phpgw::get_var('account_id', 'int');
	$new_owner = phpgw::get_var('new_owner', 'int');

	// Delete all records for a user
	$pro = CreateObject('projects.boprojects');

	if ( !$new_owner)
	{
		$pro->delete_project($account_id, 0, 'account');
	}
	else
	{
		$pro->change_owner($account_id, $new_owner);
	}
	unset($pro);
