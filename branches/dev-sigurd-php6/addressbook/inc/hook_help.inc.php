<?php
	/**
	* Addressbook - Hook for manual
	*
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package addressbook
	* @subpackage hooks
	* @version $Id$
	*/

	/**
	* Include addressbooks setup
	*/
	include(PHPGW_SERVER_ROOT . '/addressbook/setup/setup.inc.php');

	$GLOBALS['phpgw']->help->set_params(array('app_name'		=> 'addressbook',
												'title'			=> lang('addressbook'),
												'app_version'	=> $setup_info['addressbook']['version']));

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('owerview'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('overview.php'),
		'lang_link_statustext'	=> lang('owerview')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('list'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('list.php'),
		'lang_link_statustext'	=> lang('list')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('add'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('add.php'),
		'lang_link_statustext'	=> lang('add')
	);

	$GLOBALS['phpgw']->help->draw();
?>
