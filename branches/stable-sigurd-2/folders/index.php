<?php
/**
 * folders module
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package folders
 * @version $Id$
 */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp' => 'folders',
		'noheader'   => true,
		'nonavbar'   => false
	);

	/**
	* Include phpgroupware header
	*/
	require_once('../header.inc.php');

	$parms = Array(
		'menuaction'=> 'folders.uifolders.showFolders'
	);

	Header('Location: '.$GLOBALS['phpgw']->link('/index.php',$parms));
	$GLOBALS['phpgw']->common->phpgw_exit();
?>