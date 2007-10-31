<?php
	/**
	* phpGroupWare - demo: a demo application.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage setup
 	* @version $Id: setup.inc.php,v 1.3 2006/11/19 19:02:36 sigurdne Exp $
	*/

	$setup_info['newdesign']['name']      = 'newdesign';
	$setup_info['newdesign']['title']     = 'New design playground';
	$setup_info['newdesign']['version']   = '0.9.17.001';
	$setup_info['newdesign']['app_order'] = 20;
	$setup_info['newdesign']['enable']    = 1;
	$setup_info['newdesign']['globals_checked']    = True;
	$setup_info['newdesign']['app_group']	= 'office';

	$setup_info['newdesign']['author'][] = array
	(
		'name'	=> 'Jan Åge Johnsen',
		'email'	=> 'janaage@hikt.no'
	);

	$setup_info['newdesign']['maintainer'] = array
	(
		'name'	=> 'Jan Åge Johnsen',
		'email'	=> 'janaage@hikt.no'
	);

	$setup_info['newdesign']['license']  = 'GPL';
	$setup_info['newdesign']['description'] =
	'<div align="left">
		<b>New design</b> playground:		
	</div>';

	$setup_info['newdesign']['note'] =
		'Notes for the demo goes here';

	$setup_info['newdesign']['tables'] = array(
		//'phpgw_demo_table'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['newdesign']['hooks'] = array
	(
//		'add_def_pref',
//		'manual',
		'settings',
		'preferences',
		'admin',
//		'help',
		'sidebox_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['newdesign']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);


?>