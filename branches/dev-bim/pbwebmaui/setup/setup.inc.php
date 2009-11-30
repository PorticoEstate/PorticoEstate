<?php
/**
 * pbwebmaui module
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package folders
 * @version $Id:
 */
 
	$setup_info['pbwebmaui']['name']      = 'pbwebmaui';
	$setup_info['pbwebmaui']['title']     = 'pb.WebMAUI for phpGroupWare';
	$setup_info['pbwebmaui']['version']   = '0.0.2';
	$setup_info['pbwebmaui']['app_order'] = 15;
	$setup_info['pbwebmaui']['enable']    = 1;
	$setup_info['pbwebmaui']['app_group']	= 'systools';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['pbwebmaui']['hooks'][] = 'admin';
	$setup_info['pbwebmaui']['hooks'][] = 'addaccount';
	$setup_info['pbwebmaui']['hooks'][] = 'editaccount';
	$setup_info['pbwebmaui']['hooks'][] = 'deleteaccount';
	$setup_info['pbwebmaui']['hooks'][] = 'sidebox_menu';
	$setup_info['pbwebmaui']['hooks'][] = 'changepassword';
		
	/* Dependacies for this app to work */
	$setup_info['pbwebmaui']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.13','0.9.14', '0.9.16', '0.9.17', '0.9.18')
	);

	$setup_info['pbwebmaui']['depends'][] = array(
		'appname'  => 'admin',
		'versions' => Array('0.9.13','0.9.14', '0.9.16', '0.9.17', '0.9.18')
	);

	$setup_info['pbwebmaui']['depends'][] = array(
		'appname'  => 'preferences',
		'versions' => Array('0.9.13','0.9.14', '0.9.16', '0.9.17', '0.9.18')
	);
?>
