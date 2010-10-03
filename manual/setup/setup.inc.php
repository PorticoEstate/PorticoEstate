<?php
	/**
	* Manual - User manual
	*
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package manual
	* @subpackage setup
	* @version $Id$
	*/

	// Basic information about this app
	$setup_info['manual']['name']      = 'manual';
	$setup_info['manual']['version']   = '0.9.17.500';
	$setup_info['manual']['app_order'] = 5;
	$setup_info['manual']['enable']    = 1;
	$setup_info['manual']['app_group']	= 'accessories';

	// The hooks this app includes, needed for hooks registration
	$setup_info['manual']['hooks'][] = 'help';
	$setup_info['manual']['hooks'][] = 'sidebox_menu';

	// Dependencies for this app to work
	$setup_info['manual']['depends'][] = array
	(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.17', '0.9.18')
	);
?>
