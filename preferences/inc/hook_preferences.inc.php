<?php
	/**
	* Preferences - preferences hook
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @version $Id$
	*/

	if ($GLOBALS['phpgw']->acl->check('changepassword',1))
	{
		$file['Change your Password'] = $GLOBALS['phpgw']->link('/preferences/changepassword.php');
	}
	if((isset($GLOBALS['phpgw_info']['server']['auth_type']) && $GLOBALS['phpgw_info']['server']['auth_type'] == 'remoteuser') || (isset($GLOBALS['phpgw_info']['server']['half_remote_user']) && $GLOBALS['phpgw_info']['server']['half_remote_user'] == 'remoteuser'))
	{
		if($GLOBALS['phpgw_info']['server']['mapping'] == 'table' || $GLOBALS['phpgw_info']['server']['mapping'] == 'all')
		{
			$file['mapping'] = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uimapping.index','appname'=>'preferences'));
		}
	}
												
	$file['change your settings'] = $GLOBALS['phpgw']->link('/preferences/preferences.php',array('appname'=> 'preferences'));

	display_section('preferences',$file);
?>
