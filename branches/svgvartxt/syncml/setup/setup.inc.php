<?php
	/**
	 * phpGroupWare (http://phpgroupware.org/)
	 * SyncML interface
	 *
	 * @author    Johan Gunnarsson <johang@phpgroupware.org>
	 * @copyright Copyright (c) 2007 Free Software Foundation, Inc.
	 * @license   GNU General Public License 3 or later
	 * @package   syncml
	 * @version   $Id$
	 */

	$setup_info['syncml']['name'] = 'syncml';
	$setup_info['syncml']['title'] = 'SyncML Synchronization';
	$setup_info['syncml']['version'] = '0.9.17.003';
	$setup_info['syncml']['license']  = 'GNU General Public License';

	$setup_info['syncml']['app_order'] = 10;
	$setup_info['syncml']['enable'] = 2;

	$setup_info['syncml']['tables'] = array
	(
		'phpgw_syncml_sessions',
		'phpgw_syncml_hashes',
		'phpgw_syncml_sources',
		'phpgw_syncml_mappings',
		'phpgw_syncml_channels',
		'phpgw_syncml_databases'
	);

	$setup_info['syncml']['hooks'] = array
	(
		'changepassword',
		'preferences'
	);

	$setup_info['syncml']['author'][] = array
	(
		'name'	=> 'Johan Gunnarsson',
		'email'	=> 'johang@phpgroupware.org'
	);

	$setup_info['syncml']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => array('0.9.17', '0.9.18')
	);
?>
