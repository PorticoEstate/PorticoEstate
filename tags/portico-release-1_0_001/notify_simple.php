<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/

	$phpgw_info = array();

	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader' => True,
		'nonavbar' => True,
		'disable_Template_class' => True,
		'currentapp' => 'notifywindow'
	);
	
	/**
	* Include phpgroupware header
	*/
	include_once('header.inc.php');

	echo '<html><body>';
	$GLOBALS['phpgw']->hooks->process('notifywindow_simple',array('email'));
	echo '</body></html>';
?>
