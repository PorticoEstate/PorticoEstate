#!/usr/bin/php
<?php

	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
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
	* Repository where you are checking out the code.  NO trailing / - example: 'svn.savannah.gnu.org/phpgroupware'
	*/

	$repository = 'svn.resight.no/srv/svn/phpgroupware';

	/**
	* What do you want to do? valid actions are 'co' for standard checkout or 'export' for no svn informations
	*/

	$action = 'co';
//	$action = 'export';

	/**
	* Directory that you want the phpgroupware directory to go in.  NO trailing /
	*/

	$co_dir = '/path/to/dir';

	/**
	* Only needed if you have developers cvs access - leave empty for anonymous
	*/

	$cvs_login = '';

	/**
	* What release do you intend to check out? - leave empty for trunk - example: 'Version-0_9_16-branch'
	*/

	$release_info = 'dev-sigurd-2';

	/**
	* Modules you want to checkout, do NOT add the phpgroupware module
	*/

	/**
	* Base
	*/
	$co_modules[] = 'admin';
	$co_modules[] = 'doc';
	$co_modules[] = 'manual';
	$co_modules[] = 'phpgwapi';
	$co_modules[] = 'preferences';
	$co_modules[] = 'setup';
	$co_modules[] = 'syncml';
	$co_modules[] = 'xmlrpc';
	$co_modules[] = 'cron';
	$co_modules[] = 'soap';

	/**
	* Supported
	*/
	$co_modules[] = 'addressbook';
	$co_modules[] = 'calendar';
//	$co_modules[] = 'demo';
//	$co_modules[] = 'email';
//	$co_modules[] = 'felamimail';
//	$co_modules[] = 'filemanager';
//	$co_modules[] = 'folders';
//	$co_modules[] = 'ged';
	$co_modules[] = 'hrm';
	$co_modules[] = 'notes';
//	$co_modules[] = 'projects';
	$co_modules[] = 'property';
	$co_modules[] = 'sms';
	$co_modules[] = 'todo';
//	$co_modules[] = 'tts';

	/**
	* Some other stuff
	*/
//	$co_modules[] = 'backup';
//	$co_modules[] = 'bookkeeping';
//	$co_modules[] = 'bookmarks';
//	$co_modules[] = 'brewer';
//	$co_modules[] = 'cart';
//	$co_modules[] = 'ccs';
//	$co_modules[] = 'cdb';
//	$co_modules[] = 'chat';
//	$co_modules[] = 'chora';
//	$co_modules[] = 'comic';
//	$co_modules[] = 'developer_tools';
//	$co_modules[] = 'dj';
//	$co_modules[] = 'eldaptir';
//	$co_modules[] = 'forum';
//	$co_modules[] = 'ftp';
//	$co_modules[] = 'fudforum';
//	$co_modules[] = 'headlines';
//	$co_modules[] = 'hr';
//	$co_modules[] = 'infolog';
//	$co_modules[] = 'inv';
//	$co_modules[] = 'javassh';
//	$co_modules[] = 'mediadb';
//	$co_modules[] = 'meerkat';
//	$co_modules[] = 'messenger';
//	$co_modules[] = 'netsaint';
//	$co_modules[] = 'news_admin';
//	$co_modules[] = 'nntp';
//	$co_modules[] = 'phonelog';
//	$co_modules[] = 'phpGWShell_Win32_VB';
//	$co_modules[] = 'phpsysinfo';
//	$co_modules[] = 'polls';
//	$co_modules[] = 'qmailldap';
//	$co_modules[] = 'rbs';
//	$co_modules[] = 'skel';
//	$co_modules[] = 'squirrelmail';
//	$co_modules[] = 'stocks';
//	$co_modules[] = 'syncml-server';
//	$co_modules[] = 'timetrack';
//	$co_modules[] = 'wap';
//	$co_modules[] = 'wcm';
//	$co_modules[] = 'weather';


	// ****************************************************************************
	// End config section
	// ****************************************************************************

	/**
	* If you do not have developer access to cvs, set to True
	*/
	$cvs_anonymous = false;
	if(!$cvs_login)
	{
		$cvs_anonymous = true;	
	}

	if ($release_info)
	{
		$release = "/{$release_info}";
		$branch = 'branches';
	}
	else
	{
		$release = '';
		$branch = 'trunk';
	}

	chdir($co_dir);
	if ($cvs_anonymous)
	{
		system("svn {$action} svn://{$repository}/{$branch}{$release} phpgroupware --non-recursive");
	}
	else
	{
		system("svn {$action} svn+ssh://{$cvs_login}@{$repository}/{$branch}{$release}  phpgroupware --non-recursive");
	}

	chdir($co_dir . '/phpgroupware');

	foreach($co_modules as $module)
	{
		if ($cvs_anonymous)
		{
			system("svn {$action} svn://{$repository}/{$branch}{$release}/{$module}");
		}
		else
		{
			system("svn {$action} svn+ssh://{$cvs_login}@{$repository}/{$branch}$release/{$module}");
		}
	}

//	system('svn up');
