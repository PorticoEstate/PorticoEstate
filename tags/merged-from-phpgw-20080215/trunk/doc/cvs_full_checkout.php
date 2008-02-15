#!/usr/bin/php
<?php

	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage development
	* @version $Id$
	*/

	exit; //remove this line to make the script work

	// ****************************************************************************
	// Config section
	// ****************************************************************************

	/**
	* Temp paths that can be read and written to
	*/
	$tmp_dir = '/tmp';
	/**
	* Directory that you want the phpgroupware directory to go in.  NO trailing /
	*/
	$co_dir = '/var/www/html';
	/**
	* If you do not have developer access to cvs, set to True
	*/
	$cvs_anonymous = True;
	/**
	* If you do not have developer access to phpgwapi cvs, set to True
	*/
	$cvs_api_anonymous = True;
	/**
	* Only needed if you have developers cvs access
	*/
	$cvs_login = '';
	/**
	* What release do you intend to check out? - leave empty for HEAD - example: 'Version-0_9_16-branch'
	*/
	$release_info = '';

	/**
	* Modules you want to checkout, do NOT add the phpgroupware module
	*/
	$co_modules[] = 'addressbook';
	$co_modules[] = 'admin';
	$co_modules[] = 'backup';
	$co_modules[] = 'bookkeeping';
	$co_modules[] = 'bookmarks';
	$co_modules[] = 'brewer';
	$co_modules[] = 'calendar';
	$co_modules[] = 'cart';
	$co_modules[] = 'ccs';
	$co_modules[] = 'cdb';
	$co_modules[] = 'chat';
	$co_modules[] = 'chora';
	$co_modules[] = 'comic';
	$co_modules[] = 'cron';
	$co_modules[] = 'developer_tools';
	$co_modules[] = 'dj';
	$co_modules[] = 'eldaptir';
	$co_modules[] = 'email';
	$co_modules[] = 'filemanager';
	$co_modules[] = 'folders';
	$co_modules[] = 'forum';
	$co_modules[] = 'ftp';
	$co_modules[] = 'fudforum';
	$co_modules[] = 'headlines';
	$co_modules[] = 'hr';
	$co_modules[] = 'hrm';
	$co_modules[] = 'infolog';
	$co_modules[] = 'inv';
	$co_modules[] = 'javassh';
	$co_modules[] = 'manual';
	$co_modules[] = 'mediadb';
	$co_modules[] = 'meerkat';
	$co_modules[] = 'messenger';
	$co_modules[] = 'netsaint';
	$co_modules[] = 'news_admin';
	$co_modules[] = 'nntp';
	$co_modules[] = 'notes';
	$co_modules[] = 'phonelog';
	$co_modules[] = 'phpGWShell_Win32_VB';
	$co_modules[] = 'phpsysinfo';
	$co_modules[] = 'polls';
	$co_modules[] = 'preferences';
	$co_modules[] = 'projects';
	$co_modules[] = 'property';
	$co_modules[] = 'qmailldap';
	$co_modules[] = 'rbs';
	$co_modules[] = 'setup';
	$co_modules[] = 'skel';
	$co_modules[] = 'sms';
	$co_modules[] = 'soap';
	$co_modules[] = 'squirrelmail';
	$co_modules[] = 'stocks';
	$co_modules[] = 'syncml-server';
	$co_modules[] = 'timetrack';
	$co_modules[] = 'todo';
	$co_modules[] = 'tts';
	$co_modules[] = 'wap';
	$co_modules[] = 'wcm';
	$co_modules[] = 'weather';
	$co_modules[] = 'xmlrpc';

	// ****************************************************************************
	// End config section
	// ****************************************************************************


	/**
	* Do cvs command
	*
	* This will do the cvs command
	* @param string $command
	* @param boolean $anonymous_login
	*/
	function docvscommand($command, $anonymous_login = False)
	{
		global $tmp_dir, $cvs_anonymous;

		$fp = fopen($tmp_dir . '/createrelease.exp','w');
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

		if ($cvs_anonymous && $anonymous_login)
		{
			$contents .= "expect \"CVS password:\"\n";
			$contents .= "send -- \"\\r\"\n";
		}

		$contents .= "expect eof\n";
		fputs($fp, $contents, strlen($contents));
		fclose($fp);
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
		docvscommand('cvs -z3 -d:pserver:anonymous@cvs.savannah.gnu.org:/sources/phpgroupware co ' . $release . 'phpgroupware');
	}
	else
	{
		docvscommand('cvs -z3 -d:ext:' . $cvs_login . '@cvs.savannah.gnu.org:/sources/phpgroupware co '  . $release . 'phpgroupware');
	}

	chdir($co_dir . '/phpgroupware');

	if ($cvs_anonymous)
	{
		docvscommand('cvs -z3 -d:pserver:anonymous@cvs.savannah.gnu.org:/sources/phpgroupware co ' . $release . implode(' ',$co_modules));
	}
	else
	{
		docvscommand('cvs -z3 -d:ext:' . $cvs_login . '@cvs.savannah.gnu.org:/sources/phpgroupware co ' . $release . implode(' ',$co_modules));
	}

	if ($cvs_api_anonymous)
	{
		docvscommand('cvs -z3 -d:pserver:anonymous@cvs.savannah.gnu.org:/sources/phpgwapi co ' . $release . 'phpgwapi');
	}
	else
	{
		docvscommand('cvs -z3 -d:ext:' . $cvs_login . '@cvs.savannah.gnu.org:/sources/phpgwapi co ' . $release . 'phpgwapi');
	}

	docvscommand('cvs update -dP');
