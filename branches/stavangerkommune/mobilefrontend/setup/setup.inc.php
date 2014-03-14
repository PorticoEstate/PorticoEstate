<?php
	/**
	* phpGroupWare - mobilefrontend
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package mobilefrontend
	* @subpackage setup
 	* @version $Id: setup.inc.php 11048 2013-04-10 10:22:37Z sigurdne $
	*/

	$setup_info['mobilefrontend']['name']		= 'mobilefrontend';
	$setup_info['mobilefrontend']['version']	= '0.1.2';
	$setup_info['mobilefrontend']['app_order']	= 80;
	$setup_info['mobilefrontend']['enable']		= 1;
	$setup_info['mobilefrontend']['app_group']	= 'office';

	$setup_info['property']['author'] = array
	(
//		'name'	=> 'Sigurd Nes',
//		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['mobilefrontend']['maintainer'] = array
	(
//		'name'	=> 'Sigurd Nes',
//		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['mobilefrontend']['license']  = 'GPL';
	$setup_info['mobilefrontend']['description'] =
	'<div align="left">

	<b>Mobilefrontend</b> for:
	<ol>
		<li>Conntroller</li>
		<li>Property::tickets</li>
	</ol>

	<b>Mobilefrontend</b> is organized as a set of submodules as extensions to their backend-parents.
	</div>';

	$setup_info['mobilefrontend']['note'] ='';


//	$setup_info['mobilefrontend']['tables'] = array();

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['mobilefrontend']['hooks'] = array
	(
		'config',
		'home',
		'set_auth_type'	=> 'mobilefrontend.hook_helper.set_auth_type',
		'menu'			=> 'mobilefrontend.menu.get_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['mobilefrontend']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => array('0.9.17', '0.9.18')
	);

/*
	$setup_info['mobilefrontend']['depends'][] = array
	(
		'appname'  => 'controller',
		'versions' => array('0.1.41')
	);
*/

