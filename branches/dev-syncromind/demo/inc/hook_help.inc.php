<?php
	/**
	* phpGroupWare - DEMO: A demo application.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage manual
 	* @version $Id$
	*/


	/**
	 * Description
	 */

	include(PHPGW_SERVER_ROOT.'/'.'demo'.'/setup/setup.inc.php');

	$GLOBALS['phpgw']->help->set_params(array('app_name' => 'demo',
							'title'	=> lang('demo'),
							'app_version'	=> $setup_info['demo']['version']));
	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('overview'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('overview.php'),
		'lang_link_statustext'	=> lang('overview')
	);


	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('hrm'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('demo.php'),
		'lang_link_statustext'	=> lang('demo')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('project'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('project.php'),
		'lang_link_statustext'	=> lang('project')
	);

	$GLOBALS['phpgw']->help->draw();
