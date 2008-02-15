<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id$
	* $Source: /sources/phpgroupware/projects/index.php,v $
	*/

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp' => 'projects',
		'noheader'   => true,
		'nonavbar'   => true
	);

	include('../header.inc.php');

	$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'projects.uiprojects.list_projects','action'=>'mains'));
	$GLOBALS['phpgw']->common->phpgw_exit();
?>
