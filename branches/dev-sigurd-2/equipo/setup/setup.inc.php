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
 	* @version $Id: setup.inc.php 752 2008-02-13 13:53:17Z sigurd $
	*/

	$setup_info['equipo']['name']      = 'equipo';
	$setup_info['equipo']['title']     = 'New design playground';
	$setup_info['equipo']['version']   = '0.9.17.001';
	$setup_info['equipo']['app_order'] = 20;
	$setup_info['equipo']['enable']    = 1;
	$setup_info['equipo']['globals_checked']    = True;
	$setup_info['equipo']['app_group']	= 'other';

	$setup_info['equipo']['author'][] = array
	(
		'name'	=> 'Jan Åge Johnsen',
		'email'	=> 'janaage@hikt.no'
	);

	$setup_info['equipo']['maintainer'] = array
	(
		'name'	=> 'Jan �ge Johnsen',
		'email'	=> 'janaage@hikt.no'
	);

	$setup_info['equipo']['license']  = 'GPL';
	$setup_info['equipo']['description'] =
	'<div align="left">
		<b>New design</b> playground:
	</div>';

	$setup_info['equipo']['note'] =
		'Notes for the demo goes here';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['equipo']['hooks'] = array
	(
//		'add_def_pref',
//		'manual',
		'settings',
		'preferences',
		'admin',
//		'help',
		'menu'	=> 'equipo.menu.get_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['equipo']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);


?>
