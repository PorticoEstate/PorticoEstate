<?php
	/**************************************************************************\
	* phpGroupWare - Preferences                                               *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: hook_settings.inc.php 18280 2007-09-22 14:19:46Z sigurdne $ */

	$this->bofelamimail = CreateObject('felamimail.bofelamimail');
	$this->bofelamimail->openConnection('',OP_HALFOPEN);
	$folderList = $this->bofelamimail->getFolderList();
	@reset($folderList);
	
	$this->bofelamimail->closeConnection();
	
	$config = CreateObject('phpgwapi.config','felamimail');
	$config->read_repository();
	$felamimailConfig = $config->config_data;
	#_debug_array($felamimailConfig);
	unset($config);


	$refreshTime = array(
		'0' => lang('disabled'),
		'1' => '1',
		'2' => '2',
		'3' => '3',
		'4' => '4',
		'5' => '5',
		'6' => '6',
		'7' => '7',
		'8' => '8',
		'9' => '9',
		'10' => '10',
		'15' => '15',
		'20' => '20',
		'30' => '30'
	);
	create_select_box('Refresh time in minutes','refreshTime',$refreshTime);

	create_text_area('email signature','email_sig',3,50);

	$sortOrder = array(
		'0' => lang('date(newest first)'),
		'1' => lang('date(oldest first)')
	);
	create_select_box('Default sorting order','sortOrder',$sortOrder);

	$selectOptions = array(
		'0' => lang('no'),
		'1' => lang('yes')
	);
	create_select_box('show new messages on main screen','mainscreen_showmail',$selectOptions);
	
	$deleteOptions = array(
		'move_to_trash'		=> lang('move to trash'),
		'mark_as_deleted'	=> lang('mark as deleted'),
		'remove_immediately'	=> lang('remove immediately')
	);
	create_select_box('when deleting messages','deleteOptions',$deleteOptions);

	$htmlOptions = array(
		'never_display'		=> lang('never display html emails'),
		'only_if_no_text'	=> lang('display only when no plain text is available'),
		'always_display'	=> lang('always show html emails')
	);
	create_select_box('display of html emails','htmlOptions',$htmlOptions);

	$trashOptions = array_merge(
		array(
		'none' => lang("Don't use Trash")),
		$folderList
	);
	create_select_box('trash folder','trashFolder',$trashOptions);

	$sentOptions = array_merge(
		array(
		'none' => lang("Don't use Sent")),
		$folderList
	);
	create_select_box('sent folder','sentFolder',$sentOptions);

	if ($felamimailConfig['userDefinedAccounts'] == 'yes')
	{
		$selectOptions = array(
			'no' => lang('no'),
			'yes' => lang('yes')
		);
		create_select_box('use custom settings','use_custom_settings',$selectOptions);

		create_input_box('username','username','','',40);
		create_password_box('password','key','','',40);
		create_input_box('EMail Address','emailAddress','','',40);
		create_input_box('IMAP Server Address','imapServerAddress','','',40);

		$selectOptions = array(
			'no'			=> lang('IMAP'),
			'yes'			=> lang('IMAPS Encryption only'),
			'imaps-encr-auth'	=> lang('IMAPS Authentication'),
			'imaptls'		=> lang('IMAP TLS')
		);
		create_select_box('IMAP Server type','imapServerMode',$selectOptions);

		#$selectOptions = array(
		#	'no' => lang('no'),
		#	'yes' => lang('yes')
		#);
		#create_select_box('use custom settings','use_custom_settings',$selectOptions);
	}
?>
