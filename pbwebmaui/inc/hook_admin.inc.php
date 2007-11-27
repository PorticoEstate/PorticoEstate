<?php
/**
 * pbwebmaui - Hook into admin module
 *
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package pbwebmaui
 * @version $Id: hook_admin.inc.php 17106 2006-09-09 09:04:58Z skwashd $
 */

	{
		$file = Array
		(
			'Site Configuration'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'pbwebmaui.uipbwebmaui.show_adminSiteConf') ),
			'Domain Administration' => $GLOBALS['phpgw']->link('/pbwebmaui/index.php'),
		);
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
