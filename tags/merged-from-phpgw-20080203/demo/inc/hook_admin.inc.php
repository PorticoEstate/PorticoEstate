<?php
	/**
	* phpGroupWare - DEMO: A demo application.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage admin
 	* @version $Id$
	*/

		{
			$file = array
			(
				'Global Categories'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => $appname)),
				'Configure Access Permissions'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => $appname)),
				'custom fields'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => $appname)),
				'custom functions'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_custom_function', 'appname' =>  $appname))
			);
			$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
		}
