<?php
	/**************************************************************************\
	* eGroupWare - E-Mail                                                      *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$homedisplay = isset($GLOBALS['phpgw_info']['user']['preferences']['felamimail']['mainscreen_showmail'])?intval($GLOBALS['phpgw_info']['user']['preferences']['felamimail']['mainscreen_showmail']):0;
	if($homedisplay > 0) {
		$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
		if($d1 == 'htt' || $d1 == 'ftp' ) {
			echo 'Failed attempt to break in via an old Security Hole!<br>'."\n";
			$GLOBALS['phpgw']->common->egw_exit();
		}
		unset($d1);

		$tmp_app_inc = $GLOBALS['phpgw']->common->get_inc_dir('felamimail');

		$GLOBALS['phpgw']->translation->add_app('felamimail');

		$title = lang('felamimail');
	
		$portalbox =& CreateObject('phpgwapi.listbox',
			Array(
				'title'				=> $title,
				'primary'			=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'secondary'			=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'tertiary'			=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'width'				=> '100%',
				'outerborderwidth'		=> '0',
				'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi/templates/phpgw_website','bg_filler.gif')
			)
		);

		$app_id = $GLOBALS['phpgw']->applications->name2id('felamimail');
		$var = Array(
			'up'		=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
			'down'		=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
			'close'		=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
			'question'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
			'edit'		=> Array('url'	=> '/set_box.php', 'app'	=> $app_id)
		);

		while(list($key,$value) = each($var)) {
			$portalbox->set_controls($key,$value);
		}

		$portalbox->data = Array();
	
		if($data) {
			$portalbox->data = $data;
		}
		
		$this->displayCharset	= 'utf-8';
		$this->bofelamimail	=& CreateObject('felamimail.bofelamimail',$this->displayCharset);
		
		if(!$this->bofelamimail->openConnection()) {
			$extra_data = lang("can't connect to INBOX!!");
		} else {
			$folderList	= $this->bofelamimail->getFolderObjects(true, true);
			#_debug_array($folderList);
			$extra_data = '<table border="0" cellspacing="0" cellpading="0" width="100%">
					<tr class="th">
						<td>
							<b>'.lang('foldername').'</b>
						</td>
						<td>
							<b>'.lang('total').'</b>
						</td>
						<td>
							<b>'.lang('unseen').'</b>
						</td>
					<tr>';
			$showFolders = array();
			if (!empty($this->bofelamimail->mailPreferences->preferences['mainscreen_showfolders'])) $showFolders = explode(',',$this->bofelamimail->mailPreferences->preferences['mainscreen_showfolders']);
			foreach($folderList as $key => $value) {
				#echo count($showFolders).'-'.in_array($key, $showFolders).'#<br>'; 
				#_debug_array($value);
				if (count($showFolders) == 0 || (count($showFolders)>0 && in_array($key, $showFolders))) {
					if(is_object($value->counter)) {
						$messages	= $value->counter->messages;
						$unseen		= $value->counter->unseen;
						$recent		= $value->counter->recent;
					}

					if($recent > 0) {
						$newMessages = "$unseen($recent)";
					} else {
						$newMessages = "$unseen";
					}
				
				
					$linkData = array
					(
						'menuaction'    => 'felamimail.uifelamimail.changeFolder',
						'mailbox'	=> urlencode($key)
					);
					$folderLink = $GLOBALS['phpgw']->link('/index.php',$linkData);
				
					$extra_data .= "<tr><td><a href='$folderLink'>$value->displayName</a></td><td>$messages</td><td>$newMessages</td></tr>";
				}
			}
			$extra_data .= '</table>';
		}    
		
		// output the portalbox and below it (1) the folders listbox (if applicable) and (2) Compose New mail link
		echo "\r\n".'<!-- start Mailbox info -->'."\r\n"
			.$portalbox->draw($extra_data)
			.'<!-- ends Mailox info -->'."\r\n";
	}
?>
