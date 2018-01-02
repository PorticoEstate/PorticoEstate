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
	 * @package helpdesk
	 * @subpackage core
	 * @version $Id: hook_settings.inc.php 14969 2016-05-04 08:19:06Z sigurdne $
	 */

	create_input_box('Refresh TTS every (seconds)', 'refreshinterval', 'The intervall for Helpdesk refresh - cheking for new tickets');

	$yes_and_no = array(
		'yes' => 'Yes',
		'no' => 'No'
	);


	$status_list_tts = execMethod('helpdesk.botts._get_status_list');

	if ($status_list_tts)
	{
		foreach ($status_list_tts as $entry)
		{
			$_status_tts[$entry['id']] = $entry['name'];
		}
	}

	create_select_box('show quick link for changing status for tickets', 'tts_status_link', $yes_and_no, 'Enables to set status wihout entering the ticket');

	$acc = & $GLOBALS['phpgw']->accounts;
	$group_list = $acc->get_list('groups');
	foreach ($group_list as $entry)
	{
		$_groups[$entry->id] = $entry->lid;
	}
	create_select_box('Default group TTS', 'groupdefault', $_groups, 'The default group to assign a ticket in Helpdesk-submodule');

	$account_list = $acc->get_list('accounts', -1, 'ASC', 'account_lastname');

	foreach ($account_list as $entry)
	{
		if ($entry->enabled == true)
		{
			$_accounts[$entry->id] = $entry->__toString();
		}
	}
	create_select_box('Default assign to TTS', 'assigntodefault', $_accounts, 'The default user to assign a ticket in Helpdesk-submodule');

	$priority_list_tts = execMethod('helpdesk.botts.get_priority_list');

	if ($priority_list_tts)
	{
		foreach ($priority_list_tts as $entry)
		{
			$_priority_tts[$entry['id']] = $entry['name'];
		}
	}

	create_select_box('Default Priority TTS', 'prioritydefault', $_priority_tts, 'The default priority for tickets in the Helpdesk-submodule');

	$cats = CreateObject('phpgwapi.categories', -1, 'helpdesk', '.ticket');

	$cat_data = $cats->formatted_xslt_list(array('globals' => true, 'link_data' => array()));
	$cat_list = $cat_data['cat_list'];

	if (is_array($cat_list))
	{
		foreach ($cat_list as $entry)
		{
			$_categories_tts[$entry['cat_id']] = $entry['name'];
		}
	}

	unset($sotts);
	create_select_box('default ticket categories', 'tts_category', $_categories_tts, 'The default category for TTS');

	$yes_and_no = array(
		'1' => 'Yes',
		'2' => 'No'
	);

	create_select_box('Filter tickets on assigned to me', 'tts_assigned_to_me', $yes_and_no, '');
	create_select_box('Notify me by mail when ticket is assigned or altered', 'tts_notify_me', $yes_and_no, '');

	create_select_box('Send e-mail from TTS', 'tts_user_mailnotification', $yes_and_no, 'Send e-mail from TTS as default');

	create_select_box('Set myself as contact when adding a ticket', 'tts_me_as_contact', $yes_and_no, '');

	create_input_box('With of textarea', 'textareacols', 'With of textarea in forms');
	create_input_box('Height of textarea', 'textarearows', 'Height of textarea in forms');
	create_input_box('Your Email', 'email', 'Insert your email address');
