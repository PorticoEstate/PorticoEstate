<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Benoit Hamet <caeies@phpgroupware.org>
	* @author Quang Vu DANG <quang_vu.dang@int-evry.fr>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/

	/*
	* This file should be protected by apache configuration. Please take a look in the README file !
	*/

	// Set configuration variables needed by Half remote_user mode
	$GLOBALS['phpgw_remote_user'] = 'remoteuser';

	//We go back to the root directory
	chdir('../../../');
	
	//Now do the right work :)
	require_once 'login.php';

	$GLOBALS['phpgw']->common->phpgw_exit();
