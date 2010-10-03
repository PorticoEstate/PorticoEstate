<?php
	/**************************************************************************\
	* phpGroupWare - Communik8r - Setup - default_records                      *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* Copyright (c) 2005 Dave Hall                                             *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
		  
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_communik8r_acct_types VALUES (1, 'imap', 'IMAP Mail', 'email', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_communik8r_acct_types VALUES (2, 'pop3', 'POP3 Mail', 'email', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_communik8r_acct_types VALUES (3, 'jabber', 'Jabber IM', 'jabber', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_communik8r_acct_types VALUES (4, 'sms', 'SMS Text Message', 'sms', 1)");

