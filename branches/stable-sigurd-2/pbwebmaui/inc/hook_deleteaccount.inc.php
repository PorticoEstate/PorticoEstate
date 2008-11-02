<?php
	/**
	* phpGroupWare pbwebmaui - delete account hook
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/ GNU General Public License v2 or later
	* @package phpgroupware
	* @subpackage addressbook
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

 	$preferences = $GLOBALS['phpgw']->preferences->read();
	if (!$preferences['pbwebmaui']['keepDeleted'])
	{
		if($GLOBALS['hook_values']['account_lid'])
		{
			// don't ask
			// the hook is called two times
			// you certainly can imagine what happens if you want to delete an entry in LDAP two times

			/**
			* pbwebmaui mailserver
			*/
			require_once PHPGW_SERVER_ROOT . '/pbwebmaui/inc/pb.WebMAUI/lib/class.mailserver.php';

			/**
			* pbwebmaui application
			*/
			require_once PHPGW_SERVER_ROOT . '/pbwebmaui/inc/pb.WebMAUI/lib/class.application.php';

			// FIXME this is an ugly hack - skwashd may08
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'pbwebmaui';

			// available vars...
			// $GLOBALS['hook_values']['account_lid'];
			// $GLOBALS['hook_values']['account_id'];
			// $GLOBALS['hook_values']['new_passwd'];
			// $GLOBALS['hook_values']['account_firstname'];
			// $GLOBALS['hook_values']['account_lastname'];

			$dn = "uid={$GLOBALS['hook_values']['account_lid']},"
				. "ou={$GLOBALS['phpgw_info']['server']['default_domain']},"
				. $GLOBALS['phpgw_info']['server']['ldap_mailaccounts_context'];

			$pbapplication = new Application;

			$mailaccount = new Mailaccount($pbapplication, $dn, $GLOBALS['hook_values']['account_lid'],
							$email, $GLOBALS['phpgw_info']['server']['default_domain']);

			$mailaccount->delete();

			unset($mailaccount);
			unset($pbapplication);

			// revert hack
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $currentapp;
		}
	}
