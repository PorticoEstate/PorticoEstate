<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage admin
 	* @version $Id$
	*/

		{
			$file = array
			(
				'Global Categories'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'hrm')),
				'training category'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'training')),
				'skill level'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'skill_level')),
				'experience category'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'experience')),
				'qualification category'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'qualification')),
				'Configure Access Permissions'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => $appname)),
			);
			$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
		}
