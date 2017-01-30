<?php
	/**
	 * phpGroupWare - eventplanner: a eventplanner application.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package eventplanner
	 * @subpackage setup
	 * @version $Id: setup.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
	 */
	$setup_info['eventplannerfrontend']['name'] = 'eventplannerfrontend';
	$setup_info['eventplannerfrontend']['version'] = '0.1.2';
	$setup_info['eventplannerfrontend']['app_order'] = 9;
	$setup_info['eventplannerfrontend']['enable'] = 1;
	$setup_info['eventplannerfrontend']['app_group'] = 'office';

	$setup_info['eventplannerfrontend']['description'] = 'Bergen kommune eventplannerfrontend';

	$setup_info['eventplannerfrontend']['author'][] = array
		(
		'name' => 'Sigurd Nes',
		'email' => 'sigurdne@online.no'
	);

	/* Dependencies for this app to work */
	$setup_info['eventplannerfrontend']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['eventplannerfrontend']['depends'][] = array(
		'appname' => 'eventplanner',
		'versions' => array('0.9.18')
	);


	/* The hooks this app includes, needed for hooks registration */
	$setup_info['eventplannerfrontend']['hooks'] = array(
		'menu'				=> 'eventplannerfrontend.menu.get_menu',
		'set_cookie_domain' => 'eventplannerfrontend.hook_helper.set_cookie_domain',
		'set_auth_type'		=> 'eventplannerfrontend.hook_helper.set_auth_type',
		'home'				=> 'eventplannerfrontend.hook_helper.home',
		'login'				=> 'eventplannerfrontend.hook_helper.login',
		'after_navbar'		=> 'eventplannerfrontend.hook_helper.after_navbar',
		'addaccount'		=> 'eventplannerfrontend.hook_helper.addaccount',
		'config',
	);
