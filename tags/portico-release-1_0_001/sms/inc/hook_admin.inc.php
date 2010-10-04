<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage admin
 	* @version $Id$
	*/

		{
			$file = array
			(
				'Configuration' => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uiconfig.index')),
				'Configure Access Permissions'=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uiadmin_acl.list_acl', 'acl_app'=>$appname)),
			);
		display_section($appname,$appname,$file);
		}

