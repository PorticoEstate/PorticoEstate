<?php
	/**
	* Manual - User manual
	*
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package manual
	* @version $Id$
	*/

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'		=> true,
		'nonavbar'		=> true,
		'currentapp'	=> 'manual'
	);
	
	/**
	 * Include phpgroupware header
	 */
	include('../header.inc.php');

	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'manual.uimanual.index') );
?>
