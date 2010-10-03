<?php
	/**
	* Preferences - user manual
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @subpackage setup
	* @version $Id$
	*/

	$setup_info['preferences']['name']      = 'preferences';
	$setup_info['preferences']['title']     = 'Preferences';
	$setup_info['preferences']['version']   = '0.9.17.500';
	$setup_info['preferences']['app_order'] = 1;
	$setup_info['preferences']['tables']    = '';
	$setup_info['preferences']['enable']    = 2;
	$setup_info['preferences']['app_group']	= 'systools';

	// The hooks this app includes, needed for hooks registration
	$setup_info['preferences']['hooks'][] = 'deleteaccount';
	$setup_info['preferences']['hooks'][] = 'config';
	$setup_info['preferences']['hooks'][] = 'manual';
	$setup_info['preferences']['hooks'][] = 'settings';
	$setup_info['preferences']['hooks']['menu'] = 'preferences.menu.get_menu';

	// Dependacies for this app to work
	$setup_info['preferences']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => array('0.9.17', '0.9.18')
	);
?>
