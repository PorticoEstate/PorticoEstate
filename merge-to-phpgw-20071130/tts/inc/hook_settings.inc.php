<?php
	/**
	* Trouble Ticket System
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2001-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage hooks
	* @version $Id: hook_settings.inc.php 17615 2006-11-28 10:09:32Z skwashd $
	*/

	$yes_and_no = array
	(
		'True' => 'Yes',
		''     => 'No'
	);
	create_select_box('show new/updated tickets on main screen','mainscreen_show_new_updated', $yes_and_no);

	$acc = CreateObject('phpgwapi.accounts');
	$group_list = $acc->get_list('groups');
	$groups = array();
	foreach ( $group_list as $entry )
	{
		$groups[$entry['account_id']] = $GLOBALS['phpgw']->common->display_fullname($entry['account_lid'], $entry['account_firstname'], $entry['account_lastname']);;
	}
	create_select_box('Default group','groupdefault', $groups);

	$account_list = $acc->get_list('accounts');
	$accounts = array();
	foreach ( $account_list as $entry )
	{
		$accounts[$entry['account_id']] = $GLOBALS['phpgw']->common->display_fullname($entry['account_lid'], $entry['account_firstname'], $entry['account_lastname']);
	}
	create_select_box('Default assign to','assigntodefault', $accounts);

	// Choose the correct priority to display
	$priority = array
	(
		1	=> '1 - ' . lang('Lowest'),
		2	=> 2,
		3	=> 3,
		4	=> 4,
		5	=> '5 - ' . lang('Medium'),
		6	=> 6,
		7	=> 7,
		8	=> 8,
		9	=> 9,
		10	=> '10 - ' . lang('Highest')
	); 
	
	create_select_box('Default Priority','prioritydefault',$priority);
	//create_input_box('Refresh every (seconds)','refreshinterval');
