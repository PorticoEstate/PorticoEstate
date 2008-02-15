<?php
	/**
	* Trouble Ticket System - Setup
	*
	* @copyright Copyright (C) 2001-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage setup
	* @version $Id$
	*/

	/* Basic information about this app */
	$setup_info['tts']['name']		= 'tts';
	$setup_info['tts']['title']		= 'Trouble Ticket System';
	$setup_info['tts']['version']		= '0.9.17.501';
	$setup_info['tts']['app_order']		= 99;
	$setup_info['tts']['enable']		= 1;
	$setup_info['tts']['globals_checked']	= True;
	$setup_info['tts']['app_group']		= 'development';

	/* The tables this app creates */
	$setup_info['tts']['tables']    = array('phpgw_tts_tickets','phpgw_tts_views','phpgw_tts_email_map');

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['tts']['hooks'][] = 'admin';
	$setup_info['tts']['hooks'][] = 'home';
	$setup_info['tts']['hooks'][] = 'manual';
	$setup_info['tts']['hooks'][] = 'preferences';
	$setup_info['tts']['hooks'][] = 'settings';
	$setup_info['tts']['hooks'][] = 'deleteaccount';
	$setup_info['tts']['hooks']['cat_add'] = 'tts.bo_hooks.cat_add';
	$setup_info['tts']['hooks']['cat_delete'] = 'tts.bo_hooks.cat_delete';
	$setup_info['tts']['hooks']['cat_edit'] = 'tts.bo_hooks.cat_edit';

	/* Dependencies for this app to work */
	$setup_info['tts']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.17', '0.9.18')
	);
?>
