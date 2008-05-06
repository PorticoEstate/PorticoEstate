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
	* @subpackage core
 	* @version $Id$
	*/

	if ($GLOBALS['phpgw_info']['user']['preferences']['property']['mainscreen_show_new_updated'])
	{
		$save_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'property';

		$GLOBALS['phpgw']->translation->add_app('property');

		$app_id = $GLOBALS['phpgw']->applications->name2id('property');
		$GLOBALS['portal_order'][] = $app_id;

		$GLOBALS['phpgw']->portalbox->set_params(array('app_id'	=> $app_id,
														'title'	=> lang('property')));

		$GLOBALS['HTTP_POST_VARS']['filter'] = phpgw::get_var('filter') = 'open';
		$property = CreateObject('property.uitts');

		$GLOBALS['phpgw']->portalbox->draw($property->index());

		unset($property);
		$GLOBALS['phpgw_info']['flags']['currentapp'] = $save_app;
	}


/*
	if($GLOBALS['phpgw_info']['user']['preferences']['property']['mainscreen_show_new_updated'])
	{
		$property = CreateObject('property.uitts');
		$property->bo->start = 0;
		$property->bo->limit = 5;
		$property->start = 0;
		$property->limit = 5;
		$extra_data = '<td>'."\n".$property->index(False).'</td>'."\n";

		$app_id = $GLOBALS['phpgw']->applications->name2id('property');
		$GLOBALS['portal_order'][] = $app_id;

		$GLOBALS['phpgw']->portalbox->set_params(array('app_id'	=> $app_id,
														'title'	=> lang('property')));
		$GLOBALS['phpgw']->portalbox->draw($extra_data);
	}
*/
?>
