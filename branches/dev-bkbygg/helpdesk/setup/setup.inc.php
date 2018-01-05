<?php
	/**
	* phpGroupWare - helpdesk.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2017 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package helpdesk
	* @subpackage setup
 	* @version $Id: setup.inc.php 6711 2010-12-28 15:15:42Z sigurdne $
	*/

	$setup_info['helpdesk']['name']			= 'helpdesk';
	$setup_info['helpdesk']['version']		= '0.9.18.006';
	$setup_info['helpdesk']['app_order']	= 8;
	$setup_info['helpdesk']['enable']		= 1;
	$setup_info['helpdesk']['app_group']	= 'office';

	$setup_info['helpdesk']['author'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['helpdesk']['maintainer'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['helpdesk']['license']  = 'GPL';
	$setup_info['helpdesk']['description'] =
	'<div align="left">

	<b>FM</b> (Facilities-management) providing:
	<ol>
		<li>Helpdesk</li>
	</ol>
	</div>';

	$setup_info['helpdesk']['note'] =
		'Note';


	$setup_info['helpdesk']['tables'] = array
	(
		'phpgw_helpdesk_status',
		'phpgw_helpdesk_tickets',
		'phpgw_helpdesk_views',
		'phpgw_helpdesk_response_template',
		'phpgw_helpdesk_custom_menu_items',
		'phpgw_helpdesk_email_template',
		'phpgw_helpdesk_email_out',
		'phpgw_helpdesk_email_out_recipient_set',
		'phpgw_helpdesk_email_out_recipient_list',
		'phpgw_helpdesk_email_out_recipient'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['helpdesk']['hooks'] = array
	(
		'manual',
		'settings',
		'menu'			=> 'helpdesk.menu.get_menu',
		'cat_add'		=> 'helpdesk.cat_hooks.cat_add',
		'cat_delete'	=> 'helpdesk.cat_hooks.cat_delete',
		'cat_edit'		=> 'helpdesk.cat_hooks.cat_edit',
		'auto_addaccount' => 'helpdesk.hook_helper.auto_addaccount',
		'config',
		'home',
	);

	/* Dependencies for this app to work */
	$setup_info['helpdesk']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['helpdesk']['depends'][] = array
	(
		'appname'  => 'admin',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['helpdesk']['depends'][] = array
	(
		'appname'  => 'preferences',
		'versions' => Array('0.9.17', '0.9.18')
	);
