<?php
	/**
	* Manual - common help
	*
	* @copyright Copyright (C) 2000-2003,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package manual
	* @version $Id$
	*/

	/**
	 * Include api setup
	 */

	include(PHPGW_SERVER_ROOT . '/' . 'phpgwapi' . '/setup/setup.inc.php');

	$GLOBALS['phpgw']->help->set_params(array('app_name'		=> 'manual',
												'title'			=> 'phpGroupWare',
												'app_version'	=> $setup_info['phpgwapi']['version']));

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('overview'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('overview.odt'),
		'lang_link_statustext'	=> lang('overview')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('home'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('home.odt'),
		'lang_link_statustext'	=> lang('home')
	);

	$GLOBALS['phpgw']->help->draw();
