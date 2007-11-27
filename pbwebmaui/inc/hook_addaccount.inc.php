<?php
/**
 * pbwebmaui - Hook to add an mailaccount to a phpgw account
 *
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package pbwebmaui
 * @version $Id: hook_addaccount.inc.php 15922 2005-05-10 12:42:25Z powerstat $
 */
 
	$preferences = $GLOBALS['phpgw']->preferences->read();
	if ($preferences['pbwebmaui']['syncAcc'])
	{
	
		/**
		* Include pbwebmaui mailserver
		*/
		require_once(PHPGW_SERVER_ROOT.'/pbwebmaui/inc/pb.WebMAUI/lib/class.mailserver.php');
		
		/**
		* Include pbwebmaui application
		*/
		require_once(PHPGW_SERVER_ROOT.'/pbwebmaui/inc/pb.WebMAUI/lib/class.application.php');
	
		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'pbwebmaui';
	
		// available vars...
		// $GLOBALS['hook_values']['account_lid'];
		// $GLOBALS['hook_values']['account_id'];
		// $GLOBALS['hook_values']['new_passwd'];
		// $GLOBALS['hook_values']['account_firstname'];
		// $GLOBALS['hook_values']['account_lastname'];
	
		$pbapplication = new Application;
		
		$email = $GLOBALS['hook_values']['account_lid'].'@'.$GLOBALS['phpgw_info']['server']['default_domain'];
		
		$mailaccount = new Mailaccount($pbapplication, $dn, $GLOBALS['hook_values']['account_lid'], $email, $GLOBALS['phpgw_info']['server']['default_domain']);
		$mailaccount->setAttribute('password', $GLOBALS['hook_values']['new_passwd']);
		$mailaccount->setAttribute('uidnumber', $GLOBALS['hook_values']['account_id']);
		$mailaccount->setAttribute('gidnumber', $GLOBALS['hook_values']['account_id']);
		
		$mailaccount->save(array('cn','mail','password','uidnumber', 'gidnumber'));
	}
?>