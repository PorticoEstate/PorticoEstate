<?php
	/**
	* phpGroupWare EMail - http://phpGroupWare.org
	*
	* @author Angles <angles@phpgroupware.org>
	* @copyright Copyright (C) 2001-2004 Angelo Tony Puglisi
	* @copyright Portions Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @subpackage setup
	* @version $Id$
	* @internal Based on AeroMail by Mark Cushman <mark@cushman.net>
	*/

	$setup_info['email']['name']      = 'email';
	$setup_info['email']['version']   = '0.9.17.500';
	$setup_info['email']['app_order'] = '2';
	$setup_info['email']['enable']    = 1;
	$setup_info['email']['app_group']	= 'office';
	
	$setup_info['email']['tables']    = array('phpgw_anglemail');

	$setup_info['email']['author'] = '&quot;Angles&quot; Angelo Tony Puglisi';
	$setup_info['email']['license']  = 'GPL';
	$setup_info['email']['description'] =
		'phpGroupWare Email reader with multiple accounts and mailbox filtering.';
	$setup_info['email']['globals_checked'] = True;

        $setup_info['email']['maintainer'] = array(
		'name'  => 'Dave Hall',
		'email' => 'skwashd at phpgroupware.org'
	);
					

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['email']['hooks'][] = 'admin';
	$setup_info['email']['hooks'][] = 'email_add_def_prefs';
	$setup_info['email']['hooks'][] = 'home';
	$setup_info['email']['hooks'][] = 'login';
	$setup_info['email']['hooks'][] = 'manual';
	$setup_info['email']['hooks'][] = 'notifywindow';
	$setup_info['email']['hooks'][] = 'notifywindow_simple';
	$setup_info['email']['hooks'][] = 'add_def_prefs';
	$setup_info['email']['hooks'][] = 'preferences';
	$setup_info['email']['hooks'][] = 'settings';
	//$setup_info['email']['hooks']['getFolderContent'] = 'email.email_service.getFolderContent';
	$setup_info['email']['hooks']['menu'] = 'email.service.get_menu';

	/* Dependacies for this app to work */
	$setup_info['email']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);

	$setup_info['email']['depends'][] = array(
		 'appname' => 'admin',
		 'versions' => Array('0.9.16','0.9.17','0.9.18')
	);

	$setup_info['email']['depends'][] = array(
		 'appname' => 'preferences',
		 'versions' => Array('0.9.16','0.9.17','0.9.18')
	);
?>
