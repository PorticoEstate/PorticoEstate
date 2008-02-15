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
 	* @version $Id$
	*/

	$setup_info['demo']['name']      = 'demo';
	$setup_info['demo']['version']   = '0.9.17.001';
	$setup_info['demo']['app_order'] = 20;
	$setup_info['demo']['enable']    = 1;
	$setup_info['demo']['globals_checked']    = True;
	$setup_info['demo']['app_group']	= 'office';

	$setup_info['demo']['author'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['demo']['maintainer'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['demo']['license']  = 'GPL';
	$setup_info['demo']['description'] =
	'<div align="left">
		<b>DEMO</b> Demo application:
		<ol>
			<li>XSLT</li>
				<ol>
					<li>HTML</li>
				</ol>
				<ol>
					<li>WAP (WML)</li>
				</ol>
		</ol>
	</div>';

	$setup_info['demo']['note'] =
		'Notes for the demo goes here';

	$setup_info['demo']['tables'] = array(
		'phpgw_demo_table'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['demo']['hooks'] = array
	(
//		'add_def_pref',
		'manual',
//		'settings',
		'preferences',
		'admin',
		'help',
		'menu'	=> 'demo.menu.get_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['demo']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

