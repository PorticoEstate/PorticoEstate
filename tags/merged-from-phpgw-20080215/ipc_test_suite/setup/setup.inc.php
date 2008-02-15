<?php
/**
 * IPC Test Suite
 *
 * @author      Dirk Schaller <dschaller@probusiness.de>
 * @copyright   Copyright (C) 2003 Free Software Foundation http://www.fsf.org/
 * @license     http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package     ipc_test_suite
 * @version     $Id$
 */

	/* Basic information about this app */
	$setup_info['ipc_test_suite']['name']      = 'ipc_test_suite';
	$setup_info['ipc_test_suite']['title']     = 'IPC Test Suite';
	$setup_info['ipc_test_suite']['version']   = '0.9.16.001';
	$setup_info['ipc_test_suite']['app_order'] = '21';
	$setup_info['ipc_test_suite']['enable']    = 1;

	$setup_info['ipc_test_suite']['author'] = 'Dirk Schaller';
	$setup_info['ipc_test_suite']['license']  = 'GPL';
	$setup_info['ipc_test_suite']['description'] = 'IPC Test Suite';
	$setup_info['ipc_test_suite']['maintainer'] = 'Dirk Schaller';
	$setup_info['ipc_test_suite']['maintainer_email'] = 'dschaller@probusiness.de';

	/* The tables this app creates */
	//$setup_info['ipc_test_suite']['tables']	= array();

	/* The hooks this app includes, needed for hooks registration */
	//$setup_info['ipc_test_suite']['hooks'][] = 'admin';
	//$setup_info['ipc_test_suite']['hooks'][] = 'preferences';

	/* Dependencies for this app to work */
	$setup_info['ipc_test_suite']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.14','0.9.15', '0.9.16', '0.9.17', '0.9.18')
	);
?>
