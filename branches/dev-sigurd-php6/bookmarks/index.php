<?php
	/**
	* Bookmarks
	* @author totschnig
	* @copyright Copyright (C) 1998 Padraic Renaghan
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package bookmarks
	* @version $Id$
	* @internal http://www.renaghan.com/bookmarker
	*/

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'bookmarks',
		'nonavbar'   => True,
		'noheader' => True
	);
	
	/**
	 * Include phpgroupware header
	 */
	include('../header.inc.php');

	$obj = createobject('bookmarks.ui');
	$obj->init();
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
