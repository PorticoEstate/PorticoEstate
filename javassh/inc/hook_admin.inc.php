<?php
 /**
 * phpGroupWare JavaSSH admin hook
 *
 * @author Dave Hall skwashd at phpgroupware.org
 * @copyright Copyright (c) 2002-2006 Free Software Foundation, Inc. http://fsf.org
 * @license GNU General Public License
 * @internal Development Sponsored by Advantage Business Systems - http://abcsinc.com
 * @package javassh
 * @subpackage admin
 * @version $Id: hook_admin.inc.php 17106 2006-09-09 09:04:58Z skwashd $
 */

 	{
		$file = array
		(
			'Site Configuration'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'javassh') ),
			'Manage Servers'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'javassh.ui_jssh.admin_list') ),
		);
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}

?>
