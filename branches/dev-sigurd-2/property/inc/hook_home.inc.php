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

/*
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
*/


	if ( !isset($GLOBALS['phpgw_info']['user']['preferences']['property']['mainscreen_showapprovals'])
		|| !$GLOBALS['phpgw_info']['user']['preferences']['property']['mainscreen_showapprovals'] )
	{
		return;
	}
//	$GLOBALS['phpgw']->translation->add_app('property');
	
	$title = lang('property');
	
	//TODO Make listbox css compliant
	$portalbox = CreateObject('phpgwapi.listbox', array
	(
		'title'	=> $title,
		'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'width'	=> '100%',
		'outerborderwidth'	=> '0',
		'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
	));

	$app_id = $GLOBALS['phpgw']->applications->name2id('property');
	$GLOBALS['portal_order'][] = $app_id;
	$var = array
	(
		'up'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'down'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'close'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'question'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'edit'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id)
	);

	foreach ( $var as $key => $value )
	{
//		$portalbox->set_controls($key,$value);
	}

	$portalbox->data = array();

	$db = & $GLOBALS['phpgw']->db;
	$sql = "SELECT * FROM fm_approval";// WHERE  account_id = {$GLOBALS['phpgw_info']['user']['account_id']}";
	$db->query($sql, __LINE__,__FILE__);
	while($this->db->next_record())
	{
		$portalbox->data[] = array
		(
			'text' => 'Venter på godkjenning: ' . $db->f('id'),
			'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.edit', 'id' => $db->f('id')))
		);
	}
		
	if(count($portalbox->data))
	{
		echo "\n".'<!-- BEGIN property info -->'."\n".$portalbox->draw()."\n".'<!-- END property info -->'."\n";
	}
