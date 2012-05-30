<?php
  /**************************************************************************\
  * phpGroupWare - Addressbook                                               *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo "Failed attempt to break in via an old Security Hole!<br />\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);

	$tmp_app_inc = PHPGW_APP_INC;
	define('PHPGW_APP_INC',$GLOBALS['phpgw']->common->get_inc_dir('addressbook'));

	if ($GLOBALS['phpgw_info']['user']['apps']['addressbook']
		&& $GLOBALS['phpgw_info']['user']['preferences']['addressbook']['mainscreen_showbirthdays'])
	{
		echo "\n<!-- Birthday info -->\n";

		$c = CreateObject('phpgwapi.contacts');
		$qfields = array(
			'contact_id' => 'contact_id',
			'per_first_name'  => 'per_first_name',
			'per_last_name' => 'per_last_name',
			'per_birthday'     => 'per_birthday'
		);
		$now = time() - ((60 * 60) * intval($GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset']));
		$today = $GLOBALS['phpgw']->common->show_date($now,'n/d/');
//		echo $today."\n";

		//$bdays = $c->read(0,15,$qfields,$today,'tid=n','','',$GLOBALS['phpgw_info']['user']['account_id']);
		$criteria = array('per_birthday' => $today);
		$bdays = $c->get_persons($qfields, 15, 0, '', '', $criteria);
		while(list($key,$val) = @each($bdays))
		{
			$tmp = '<a href="'
				. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.view_person', 'ab_id' => $val['contact_id'])) . '">'
				. $val['per_first_name'] . ' ' . $val['per_last_name'] . '</a>';
			echo '<tr><td align="left">' . lang("Today is %1's birthday!", $tmp) . "</td></tr>\n";
		}

		$tomorrow = $GLOBALS['phpgw']->common->show_date($now + 86400,'n/d/');
//		echo $tomorrow."\n";

		$criteria = array('per_birthday' => $tomorrow);
		$bdays = $c->get_persons($qfields, 15, 0, '', '', $criteria);
		//$bdays = $c->read(0,15,$qfields,$tomorrow,'tid=n','','',$GLOBALS['phpgw_info']['user']['account_id']);

		while(list($key,$val) = @each($bdays))
		{
			$tmp = '<a href="'
				. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.view_person', 'ab_id' => $val['contact_id'])) . '">'
				. $val['per_first_name'] . ' ' . $val['per_last_name'] . '</a>';
			echo '<tr><td align="left">' . lang("Tomorrow is %1's birthday.", $tmp) . "</td></tr>\n";
		}
		echo "\n<!-- Birthday info -->\n";
	}

	define('PHPGW_APP_INC',$tmp_app_inc);
?>
