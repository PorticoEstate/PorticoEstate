<?php
	/**
	* Addressbook - User manual
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package addressbook
	* @subpackage manual
	* @version $Id$
	*/


	$GLOBALS['phpgw_info']['flags'] = Array
	(
		'headonly'		=> True,
		'currentapp'	=> 'addressbook'
	);

	/**
	* Include phpgroupware header
	*/
	include('../../../header.inc.php');
	$GLOBALS['phpgw']->help = CreateObject('phpgwapi.help_helper');
	$GLOBALS['phpgw']->help->set_params(array('app_name'	=> 'addressbook',
												'title'		=> lang('addressbook') . ' - ' . lang('add'),
												'controls'	=> array('up' => 'list.php')));

	$values['add'] = array
	(
		'add_img'				=> $GLOBALS['phpgw']->common->image('addressbook','help_add'),
		'item_1'				=> 'Add any and all information that you see fit. You can use the tab key to tab from one field to the next.',
		'item_2'				=> 'Once you have entered all the information, press OK to accept, Clear to erase all information in all the fields or Cancel to exit this screen.',
		'lang_lastname'			=> 'Last name',
		'lang_firstname'		=> 'First name',
		'lang_email'			=> 'E-mail',
		'lang_homephone'		=> 'Home phone',
		'lang_workphone'		=> 'Work phone',
		'lang_mobile'			=> 'Mobile',
		'lang_street'			=> 'Street',
		'lang_city'				=> 'City',
		'lang_state'			=> 'State',
		'lang_zip'				=> 'ZIP code',
		'lang_access'			=> 'Access',
		'lang_groupsettings'	=> 'Group settings',
		'lang_notes'			=> 'Notes',
		'lang_company'			=> 'Company name',
		'lang_fax'				=> 'Fax',
		'lang_pager'			=> 'Pager',
		'lang_othernumber'		=> 'Other number',
		'lang_birthday'			=> 'Birthday',
		'access_descr'			=> 'Access can be restricted to private, overriding acl preferences settings.
									From preferences, you can grant access to users to the be able to view, 
									edit, and even delete your entries.'
	);

	$GLOBALS['phpgw']->help->xdraw($values);
	$GLOBALS['phpgw']->xslttpl->set_var('phpgw',$GLOBALS['phpgw']->help->output);
?>
