<?php
/**************************************************************************\
* FeLaMiMail                                                               *
* http://www.egroupware.org                                                *
* Written by Lars Kneschke <lars@kneschke.de>                              *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; version 2 of the License. 			   *
\**************************************************************************/

/* $Id$ */

{
	// Only Modify the $file and $title variables.....
	$title = $appname;
	$mailPreferences = ExecMethod('felamimail.bopreferences.getPreferences');

	$file['Preferences'] = $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uisettings.index&appname=' . $appname);

	if($mailPreferences->userDefinedAccounts) {
		$linkData = array
		(
			'menuaction' => 'felamimail.uipreferences.listAccountData',
		);
		$file['Manage eMail: Accounts / Identities'] = $GLOBALS['phpgw']->link('/index.php',$linkData);
	}

	$file['Manage Folders'] = $GLOBALS['phpgw']->link('/index.php','menuaction=felamimail.uipreferences.listFolder');

	$icServer = $mailPreferences->getIncomingServer(0);

	if($icServer->enableSieve) {
		$file['filter rules'] = $GLOBALS['phpgw']->link('/index.php', 'menuaction=felamimail.uisieve.listRules');
		$file['vacation notice'] = $GLOBALS['phpgw']->link('/index.php','menuaction=felamimail.uisieve.editVacation');
	}
	
	//Do not modify below this line
	display_section($appname,$title,$file);
}
