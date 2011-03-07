<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage manual
	* @version $Id$
	*/


	/**
	* Description
	*/

	include(PHPGW_SERVER_ROOT.'/'.'property'.'/setup/setup.inc.php');

	$GLOBALS['phpgw']->help->set_params(array('app_name'		=> 'property',
	'title'			=> lang('property'),
	'app_version'	=> $setup_info['property']['version']));
	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('overview'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('overview.odt'),
		'lang_link_statustext'	=> lang('overview')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('location'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('location.odt'),
		'lang_link_statustext'	=> lang('location')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('entities'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('entities.odt'),
		'lang_link_statustext'	=> lang('entities')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('project'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('project.odt'),
		'lang_link_statustext'	=> lang('project')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('requirement'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('requirement.odt'),
		'lang_link_statustext'	=> lang('requirement')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('agreements'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('agreement.vendor.odt'),
		'lang_link_statustext'	=> lang('agreements')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('invoice'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('invoice.odt'),
		'lang_link_statustext'	=> lang('invoice')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('helpdesk'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('helpdesk.odt'),
		'lang_link_statustext'	=> lang('helpdesk')
	);

	$GLOBALS['phpgw']->help->data[] = array
	(
		'text'					=> lang('document'),
		'url'					=> $GLOBALS['phpgw']->help->check_help_file('document.odt'),
		'lang_link_statustext'	=> lang('document')
	);


	$GLOBALS['phpgw']->help->draw();

