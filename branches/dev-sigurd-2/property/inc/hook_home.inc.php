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


	$accound_id = $GLOBALS['phpgw_info']['user']['account_id'];
	$save_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'property';
	$maxmatches = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
	$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = 5;


	if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['mainscreen_show_new_updated_tts'])
		&& $GLOBALS['phpgw_info']['user']['preferences']['property']['mainscreen_show_new_updated_tts'])
	{

//		$GLOBALS['phpgw']->translation->add_app('property');

		$app_id = $GLOBALS['phpgw']->applications->name2id('property');
		$GLOBALS['portal_order'][] = $app_id;

		$portalbox = CreateObject('phpgwapi.listbox', array
		(
			'title'	=> lang('Helpdesk'),
			'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
			'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
			'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
			'width'	=> '100%',
			'outerborderwidth'	=> '0',
			'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
		));

		$tts = CreateObject('property.sotts');

		$tickets = $tts->read(array('user_id' => $accound_id));

		$category_name = array(); // caching

		$portalbox->data = array();
		foreach ($tickets as $ticket)
		{
			if(!$ticket['subject'])
			{
				if(!isset($category_name[$ticket['cat_id']]))
				{
					$ticket['subject']= execMethod('property.botts.get_category_name', $ticket['cat_id']);
					$category_name[$ticket['cat_id']] = $ticket['subject'];
				}
				else
				{
					$ticket['subject'] = $category_name[$ticket['cat_id']];
				}
			}

			$portalbox->data[] = array
			(
				'text' => "{$ticket['address']} :: {$ticket['subject']}",
				'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view', 'id' => $ticket['id']))
			);
		}

		echo "\n".'<!-- BEGIN ticket info -->'."\n".$portalbox->draw()."\n".'<!-- END ticket info -->'."\n";

		unset($tts);
		unset($portalbox);
		unset($category_name);
	}

	if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['mainscreen_showapprovals'])
		&& $GLOBALS['phpgw_info']['user']['preferences']['property']['mainscreen_showapprovals'] )
	{

		$title = lang('approvals');
	
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
//			$portalbox->set_controls($key,$value);
		}

		$action_params = array
		(
			'appname'			=> 'property',
			'location'			=> '.project.workorder',
		//	'id'				=> $id,
			'responsible'		=> $accound_id,
			'responsible_type'  => 'user',
			'action'			=> 'approval',
			'deadline'			=> ''
		);

		$pending_approvals = execMethod('property.sopending_action.get_pending_action', $action_params);

		$portalbox->data = array();
		foreach ($pending_approvals as $entry)
		{
			$portalbox->data[] = array
			(
				'text' => 'Venter på godkjenning: ' . $entry['item_id'],
				'link' => $entry['url']
			);
		}
		
		echo "\n".'<!-- BEGIN approval info -->'."\n".$portalbox->draw()."\n".'<!-- END approval info -->'."\n";
		unset($portalbox);
	}

	if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['mainscreen_showvendor_reminder'])
		&& $GLOBALS['phpgw_info']['user']['preferences']['property']['mainscreen_showvendor_reminder'] )
	{

		$title = lang('vendor reminder');
	
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
//			$portalbox->set_controls($key,$value);
		}

		$action_params = array
		(
			'appname'			=> 'property',
			'location'			=> '.project.workorder',
		//	'id'				=> $id,
			'responsible'		=> '',
			'responsible_type'  => 'vendor',
			'action'			=> 'remind',
			'deadline'			=> '',
			'created_by'		=> $accound_id,
		);

		$pending_approvals = execMethod('property.sopending_action.get_pending_action', $action_params);

		$portalbox->data = array();
		foreach ($pending_approvals as $entry)
		{
			$sql='SELECT org_name FROM fm_vendor where id=' . (int)$entry['responsible'];
			$GLOBALS['phpgw']->db;
			$GLOBALS['phpgw']->db->query($sql);
			$GLOBALS['phpgw']->db->next_record();
			$vendor_name =  $GLOBALS['phpgw']->db->f('org_name',true);

			$portalbox->data[] = array
			(
				'text' => "påminning nr {$entry['reminder']} til leverandør {$vendor_name}- ordre nr: {$entry['item_id']}",
				'link' => $entry['url']
			);
		}
		
		echo "\n".'<!-- BEGIN reminder info -->'."\n".$portalbox->draw()."\n".'<!-- END reminder info -->'."\n";
	}
	
	$GLOBALS['phpgw_info']['flags']['currentapp'] = $save_app;
	$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = $maxmatches;

