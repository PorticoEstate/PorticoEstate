#!/usr/bin/perl
	############################################################################
	# phpGroupWare                                                             #
	# http://www.phpgroupware.org/                                             #
	# The file written by Miles Lott <milosch@phpgroupware.org>                #
	# --------------------------------------------                             #
	#  This program is free software; you can redistribute it and/or modify it #
	#  under the terms of the GNU General Public License as published by the   #
	#  Free Software Foundation; either version 2 of the License, or (at your  #
	#  option) any later version.                                              #
	############################################################################

	# $Id: cvs_full_checkout.pl 17058 2006-09-02 13:45:52Z sigurdne $

	#**************************************************************************#
	# Config section                                                           #
	#**************************************************************************#
	# Temp paths that can be read and written to
	$tmp_dir       = '/tmp';
	# Directory that you want the phpgroupware directory to go in.  NO trailing /
	$co_dir        = '/var/www/html';
	# If you do not have developer access to cvs, change to True
	$cvs_anonymous = True;
#	$cvs_anonymous = '';
	# If you do not have developer access to phpgwapi cvs, set to True
	$cvs_api_anonymous = True;
#	$cvs_api_anonymous = '';
	# Only needed if you have developers cvs access
	$cvs_login    = '';
	# What release do you intend to check out? - leave empty for HEAD - example: 'Version-0_9_16-branch'
	$release_info = '';


	# Modules you want to checkout, do NOT add the phpgroupware module
	@co_modules = (
		'addressbook',
		'admin',
		'backup',
		'bookkeeping',
		'bookmarks',
		'brewer',
		'calendar',
		'cart',
		'ccs',
		'cdb',
		'chat',
		'chora',
		'comic',
		'cron',
		'developer_tools',
		'dj',
		'eldaptir',
		'email',
		'filemanager',
		'folders',
		'forum',
		'ftp',
		'fudforum',
		'headlines',
		'hr',
		'hrm',
		'infolog',
		'inv',
		'javassh',
		'manual',
		'mediadb',
		'meerkat',
		'messenger',
		'netsaint',
		'news_admin',
		'nntp',
		'notes',
		'phonelog',
		'phpGWShell_Win32_VB',
#		'phpgwapi',
		'phpsysinfo',
		'polls',
		'preferences',
		'projects',
		'property',
		'qmailldap',
		'rbs',
		'setup',
		'skel',
		'sms',
		'soap',
		'squirrelmail',
		'stocks',
		'syncml-server',
		'timetrack',
		'todo',
		'tts',
		'wap',
		'wcm',
		'weather',
		'xmlrpc'
	);

	# -- End config section

	sub docvscommand
	{
		my $command = $_[0];
		my $anonymous_login = $_[1];

		open(FP, ">$tmp_dir/createrelease.exp");
		$contents = "#!/usr/bin/expect -f\n";
		$contents .= "send -- \"export CVS_RSH=ssh\"\n";
		$contents .= "set force_conservative 0\n";
		$contents .= "if {\$force_conservative} {\n";
		$contents .= "      set send_slow {1 .1}\n";
		$contents .= "      proc send {ignore arg} {\n";
		$contents .= "              sleep .1\n";
		$contents .= "              exp_send -s -- \$arg\n";
		$contents .= "      }\n";
		$contents .= "}\n";
		$contents .= "set timeout -1\n";
		$contents .= "spawn $command\n";
		$contents .= "match_max 100000\n";

		if ($cvs_anonymous and $anonymous_login)
		{
			$contents .= "expect \"CVS password:\"\n";
			$contents .= "send -- \"\\r\"\n";
		}

		$contents .= "expect eof\n";
		print FP $contents;
		close FP;
		system('/usr/bin/expect ' . $tmp_dir . '/createrelease.exp');
		unlink($tmp_dir . '/createrelease.exp');
	}


	if ($release_info)
	{
		$release = ' -r ' . $release_info . ' ';
	}
	else
	{
		$release = '';
	}

	chdir($co_dir);
	if ($cvs_anonymous)
	{
		&docvscommand('cvs -z3 -d:pserver:anonymous@cvs.savannah.gnu.org:/sources/phpgroupware co ' . $release . 'phpgroupware');
	}
	else
	{
		&docvscommand('cvs -z3 -d:ext:' . $cvs_login . '@cvs.savannah.gnu.org:/sources/phpgroupware co ' . $release . 'phpgroupware');
	}

	chdir($co_dir . '/phpgroupware');

	if ($cvs_anonymous)
	{
		&docvscommand('cvs -z3 -d:pserver:anonymous@cvs.savannah.gnu.org:/sources/phpgroupware co ' . $release . join(' ',@co_modules));
	}
	else
	{
		&docvscommand('cvs -z3 -d:ext:' . $cvs_login . '@cvs.savannah.gnu.org:/sources/phpgroupware co ' . $release . join(' ',@co_modules));
	}

	if ($cvs_api_anonymous)
	{
		&docvscommand('cvs -z3 -d:pserver:anonymous@cvs.savannah.gnu.org:/sources/phpgwapi co ' . $release . 'phpgwapi');
	}
	else
	{
		&docvscommand('cvs -z3 -d:ext:' . $cvs_login . '@cvs.savannah.gnu.org:/sources/phpgwapi co ' . $release . 'phpgwapi');
	}

	&docvscommand('cvs update -dP');
