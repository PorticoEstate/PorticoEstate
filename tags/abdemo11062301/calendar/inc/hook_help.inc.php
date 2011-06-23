<?php
	/**
	* phpGroupWare - calendar
	*
	* @author Craig Knudsen <cknudsen@radix.net>
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package calendar
	* @subpackage manual
 	* @version $Id$
	*/


	/**
	 * Description
	 */

	include(PHPGW_SERVER_ROOT.'/'.'calendar'.'/setup/setup.inc.php');

	$GLOBALS['phpgw']->help->set_params(array('app_name'		=> 'calendar',
												'title'			=> lang('calendar'),
												'app_version'	=> $setup_info['calendar']['version']));
	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('overview'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('overview.odt'),
		'lang_link_statustext'	=> lang('overview')
	);
	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('view'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('view.odt'),
		'lang_link_statustext'	=> lang('overview')
	);
	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('Add'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('add.odt'),
		'lang_link_statustext'	=> lang('overview')
	);
	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('Edit') .'/' .lang('Delete'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('edit_delete.odt'),
		'lang_link_statustext'	=> lang('overview')
	);

	$GLOBALS['phpgw']->help->draw();
?>
