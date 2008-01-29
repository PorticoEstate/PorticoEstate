<?php
/**
 * pbwebmaui - hook to delete a mailaccount
 *
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package pbwebmaui
 * @version $Id: hook_deleteaccount.inc.php 15922 2005-05-10 12:42:25Z powerstat $
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
			require_once(PHPGW_SERVER_ROOT.'/pbwebmaui/inc/pb.WebMAUI/lib/class.mailserver.php');
			
			/**
			* pbwebmaui application
			*/
			require_once(PHPGW_SERVER_ROOT.'/pbwebmaui/inc/pb.WebMAUI/lib/class.application.php');
			
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'pbwebmaui';
			
			// available vars...
			// $GLOBALS['hook_values']['account_lid'];
			// $GLOBALS['hook_values']['account_id'];
			// $GLOBALS['hook_values']['new_passwd'];
			// $GLOBALS['hook_values']['account_firstname'];
			// $GLOBALS['hook_values']['account_lastname'];
			
			$dn = 'uid='.$GLOBALS['hook_values']['account_lid'].',ou='.$GLOBALS['phpgw_info']['server']['default_domain'].','.$GLOBALS['phpgw_info']['server']['ldap_mailaccounts_context'];

			$pbapplication = new Application;
			
			$mailaccount = new Mailaccount($pbapplication, $dn, $GLOBALS['hook_values']['account_lid'], $email, $GLOBALS['phpgw_info']['server']['default_domain']);
			$mailaccount->delete();
		}	
	}
?>