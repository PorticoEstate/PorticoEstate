<?php
	/**
	* API Setup
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage setup
	* @version $Id$
	* @internal $Source$
	*/

	// Basic information about this app
	$setup_info['phpgwapi']['name']      = 'phpgwapi';
	$setup_info['phpgwapi']['title']     = 'phpgwapi';
	$setup_info['phpgwapi']['version']   = '0.9.17.544';
	$setup_info['phpgwapi']['versions']['current_header'] = '1.31';
	$setup_info['phpgwapi']['versions']['system'] = '1.0';
	$setup_info['phpgwapi']['enable']    = 3;
	$setup_info['phpgwapi']['app_order'] = 1;

	// The tables this app creates
	$setup_info['phpgwapi']['tables'] = array
	(
		'phpgw_access_log',
		'phpgw_accounts',
		'phpgw_account_delegates',
		'phpgw_acl',
		'phpgw_applications',
		'phpgw_app_sessions',
		'phpgw_async',
		'phpgw_cache_user',
		'phpgw_categories',
		'phpgw_config',
		'phpgw_contact',
		'phpgw_contact_addr',
		'phpgw_contact_addr_type',
		'phpgw_contact_comm',
		'phpgw_contact_comm_descr',
		'phpgw_contact_comm_type',
		'phpgw_contact_note',
		'phpgw_contact_note_type',
		'phpgw_contact_org',
		'phpgw_contact_org_person',
		'phpgw_contact_others',
		'phpgw_contact_person',
		'phpgw_contact_types',
		'phpgw_cust_attribute_group',
		'phpgw_cust_attribute',
		'phpgw_cust_choice',
		'phpgw_cust_function',
		'phpgw_group_map',
		'phpgw_history_log',
		'phpgw_hooks',
		'phpgw_interlink',
		'phpgw_interserv',
		'phpgw_lang',
		'phpgw_languages',
		'phpgw_locations',
		'phpgw_log',
		'phpgw_mail_handler',
		'phpgw_mapping',
		'phpgw_nextid',
		'phpgw_preferences',
		'phpgw_sessions',
		'phpgw_vfs',
		'phpgw_vfs_filedata',
		'phpgw_config2_section',
		'phpgw_config2_attrib',
		'phpgw_config2_choice',
		'phpgw_config2_value',
		'phpgw_notification'
	);

	$setup_info['phpgwapi']['hooks'] = array
	(
		'menu'	=> 'phpgwapi.menu_apps.get_menu',
		'login'	=> 'phpgwapi.menu.clear'
	);

	// Basic information about this app
	$setup_info['notifywindow']['name']			= 'notifywindow';
	$setup_info['notifywindow']['title']		= 'Notify Window';
	$setup_info['notifywindow']['version']		= '0.9.13.002';
	$setup_info['notifywindow']['enable']		= 2;
	$setup_info['notifywindow']['app_group']	= 'accessories';
	$setup_info['notifywindow']['app_order']	= 1;
	$setup_info['notifywindow']['tables']		= array();
	$setup_info['notifywindow']['hooks']		= array();
	$setup_info['notifywindow']['hooks'][]		= 'home';

