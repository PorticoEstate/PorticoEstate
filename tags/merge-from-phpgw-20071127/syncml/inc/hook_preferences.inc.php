<?php
	/**
	 * phpGroupWare (http://phpgroupware.org/)
	 * SyncML interface
	 *
	 * @author    Johan Gunnarsson <johang@phpgroupware.org>
	 * @copyright Copyright (c) 2007 Free Software Foundation, Inc.
	 * @license   GNU General Public License 3 or later
	 * @package   syncml
	 * @version   $Id: hook_preferences.inc.php 18171 2007-07-30 13:04:38Z johang $
	 */
	
	$title = 'SyncML interface';
	
	$file = Array(
		'Rehash password' => $GLOBALS['phpgw']->link(
			'/index.php',
			array('menuaction' => 'syncml.uisyncml.rehash')
		),
		'Edit databases' => $GLOBALS['phpgw']->link(
			'/index.php',
			array('menuaction' => 'syncml.uisyncml.listdatabases')
		)
	);
	
	display_section('syncml', $title, $file);
?>
