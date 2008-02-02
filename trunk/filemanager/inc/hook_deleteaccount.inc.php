<?php
	/***
	* Filemanager delete account hook
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @version $Id$
	*/

	// NOTE: This is untested
	// WIP: it should get all files owned by $account_id, not just in /home/account_id
	// Should also be capable of transfering files to another user

	/*
	$phpgw->vfs->working_id = $account_id;
	$ls_array = $phpgw->vfs->ls ($phpgw->vfs->fakebase . "/" . $account_id, array (RELATIVE_NONE));
	while (list ($num, $entry) = each ($ls_array))
	{
		$phpgw->vfs->rm ($entry["dir"] . "/" . $entry["name"], array (RELATIVE_NONE));
	}
*/
?>
