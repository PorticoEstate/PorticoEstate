<?php
	/**
	 * phpGroupWare - eventplanner.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package eventplanner
	 * @subpackage manual
	 * @version $Id: hook_help.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
	 */
	/**
	 * Description
	 */
	include(PHPGW_SERVER_ROOT . '/' . 'eventplanner' . '/setup/setup.inc.php');

	$GLOBALS['phpgw']->help->set_params(array('app_name' => 'eventplanner',
		'title' => lang('eventplanner'),
		'app_version' => $setup_info['eventplanner']['version']));
	$GLOBALS['phpgw']->help->data[] = array
		(
		'text' => lang('overview'),
		'url' => $GLOBALS['phpgw']->help->check_help_file('overview.php'),
		'lang_link_statustext' => lang('overview')
	);


	$GLOBALS['phpgw']->help->data[] = array
		(
		'text' => lang('eventplanner'),
		'url' => $GLOBALS['phpgw']->help->check_help_file('eventplanner.php'),
		'lang_link_statustext' => lang('eventplanner')
	);

	$GLOBALS['phpgw']->help->data[] = array
		(
		'text' => lang('project'),
		'url' => $GLOBALS['phpgw']->help->check_help_file('project.php'),
		'lang_link_statustext' => lang('project')
	);

	$GLOBALS['phpgw']->help->draw();
