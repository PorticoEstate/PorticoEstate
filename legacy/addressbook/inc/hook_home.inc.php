<?php
  /**************************************************************************\
  * phpGroupWare - E-Mail                                                    *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */
	
/* TODO Update for new contacts back end
	$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo "Failed attempt to break in via an old Security Hole!<br />\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);

	$prev_currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'addressbook';
	
	echo 'addressbook/inc/hook_home.inc.php called';
	
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
		
		$criteria = array('per_birthday' => $today);
		$bdays = $c->get_persons($qfields, 15, 0, '', '', $criteria);
		//$bdays = $c->read(0,15,$qfields,$today,'tid=n','','',$GLOBALS['phpgw_info']['user']['account_id']);
		
		$title = '<font color="#FFFFFF">'.lang('Birthdays').'</font>';

		if ((isset($prev_currentapp))                                                                                                                                    && ($prev_currentapp)                                                                                                                                            && ($GLOBALS['phpgw_info']['flags']['currentapp'] != $prev_currentapp))                                                                                          {                                                                                                                                                                        $GLOBALS['phpgw_info']['flags']['currentapp'] = $prev_currentapp;                                                                                        }  
		$portalbox = CreateObject('phpgwapi.listbox',
			Array(
				'title'     => $title,
				'primary'   => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'tertiary'  => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'width'     => '100%',
				'outerborderwidth' => '0',
				'header_background_image' => $GLOBALS['phpgw']->common->image($GLOBALS['phpgw']->common->get_tpl_dir('phpgwapi'),'bg_filler')
			)
		);
		$app_id = $GLOBALS['phpgw']->applications->name2id('addressbook');
		$GLOBALS['portal_order'][] = $app_id;
		$var = Array(
			'up'       => Array('url' => '/set_box.php', 'app' => $app_id),
			'down'     => Array('url' => '/set_box.php', 'app' => $app_id),
			'close'    => Array('url' => '/set_box.php', 'app' => $app_id),
			'question' => Array('url' => '/set_box.php', 'app' => $app_id),
			'edit'     => Array('url' => '/set_box.php', 'app' => $app_id)
		);

		while(list($key,$value) = each($var))
		{
			$portalbox->set_controls($key,$value);
		}

		$portalbox->data = Array();

		while(list($key,$val) = @each($bdays))
		{
			$portalbox->data[] = array(
				'text' => lang("Today is %1's birthday!", $val['per_first_name'] . ' ' . $val['per_last_name']),
				'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.view_person', 'ab_id' => $val['contact_id']))
			);
		}

		$tomorrow = $GLOBALS['phpgw']->common->show_date($now + 86400,'n/d/');

		$criteria = array('per_birthday' => $tomorrow);
	        $bdays = $c->get_persons($qfields, 15, 0, '', '', $criteria);

		while(list($key,$val) = @each($bdays))
		{
			$portalbox->data[] = array(
				'text' => lang("Tomorrow is %1's birthday.",$val['per_first_name'] . ' ' . $val['per_last_name']),
				'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.view_person', 'ab_id' => $val['contact_id']))
			);
		}
		
		if(count($portalbox->data))
		{
			echo $portalbox->draw();
		}

		//unset($portalbox);
		echo "\n<!-- Birthday info -->\n";
	}
*/
?>
