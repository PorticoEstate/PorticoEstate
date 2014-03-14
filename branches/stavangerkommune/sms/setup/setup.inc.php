<?php
	/**
	* phpGroupWare - sms: A SMS Gateway
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package sms
	* @subpackage setup
	* @version $Id$
	*/

	$setup_info['sms']['name']      = 'sms';
	$setup_info['sms']['version']   = '0.9.17.512';
	$setup_info['sms']['app_order'] = 8;
	$setup_info['sms']['enable']    = 1;
	$setup_info['sms']['app_group']	= 'office';
	$setup_info['sms']['description'] = 'sms gateway';

	$setup_info['sms']['author'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['sms']['maintainer'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['sms']['tables'] = array(
		'phpgw_sms_featautoreply',
		'phpgw_sms_featautoreply_log',
		'phpgw_sms_featautoreply_scenario',
		'phpgw_sms_featboard',
		'phpgw_sms_featcommand',
		'phpgw_sms_featcommand_log',
		'phpgw_sms_featcustom',
		'phpgw_sms_featcustom_log',
		'phpgw_sms_featpoll',
		'phpgw_sms_featpoll_choice',
		'phpgw_sms_featpoll_result',
		'phpgw_sms_gwmodclickatell_apidata',
		'phpgw_sms_gwmodkannel_dlr',
		'phpgw_sms_gwmoduplink',
		'phpgw_sms_tblsmsincoming',
		'phpgw_sms_tblsmsoutgoing',
		'phpgw_sms_tblsmstemplate',
		'phpgw_sms_tblusergroupphonebook',
		'phpgw_sms_tblusergroupphonebook_public',
		'phpgw_sms_tbluserinbox',
		'phpgw_sms_tbluserphonebook',
		'phpgw_sms_received_data'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['sms']['hooks'] = array
	(
//		'help',
		'settings',
		'preferences',
		'admin',
		'menu'	=> 'sms.menu.get_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['sms']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.16','0.9.17', '0.9.18')
	);

	$setup_info['sms']['depends'][] = array(
		'appname'  => 'admin',
		'versions' => Array('0.9.16','0.9.17', '0.9.18')
	);

	$setup_info['sms']['depends'][] = array(
		'appname'  => 'preferences',
		'versions' => Array('0.9.16','0.9.17', '0.9.18')
	);
