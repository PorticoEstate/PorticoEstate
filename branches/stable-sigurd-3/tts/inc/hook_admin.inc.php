<?php
	/**
	* Trouble Ticket System
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage hooks
	* @version $Id$
	*/

	$values = array
	(
		'Admin options'		=> $GLOBALS['phpgw']->link('/tts/admin.php'),
		//'ticket types'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'tts.ui_ticket_types.index') ),
		'ticket types'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'tts')),
		'configure access permissions'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'tts'))
	);


	if (! $GLOBALS['phpgw']->acl->check('custom_fields_access', 1, 'admin'))
	{
		$values['custom fields'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'tts') );
	}

	$GLOBALS['phpgw']->common->display_mainscreen('tts', $values);
?>
