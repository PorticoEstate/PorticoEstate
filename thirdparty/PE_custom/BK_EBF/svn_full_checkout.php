#!/usr/bin/php
<?php

	/**
	* Portico Estate
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2017 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage development
	* @version $Id: svn_full_checkout.php 4237 2009-11-27 23:17:21Z sigurd $
	*/

	//exit; //remove this line to make the script work

	// ****************************************************************************
	// Config section
	// ****************************************************************************

	 //Example: /usr/local/bin/php -q svn_full_checkout.php user=<username>

	/**
	* Repository where you are checking out the code.  NO trailing / - example: 'svn.savannah.gnu.org/phpgroupware'
	*/

	$repository = 'svn.savannah.nongnu.org/fmsystem';

	/**
	* What do you want to do? valid actions are 'co' for standard checkout or 'export' for no svn informations
	*/

	$action = 'co';
//	$action = 'export';
	$revision =  '-r 17207';

	/**
	* Directory that you want the phpgroupware directory to go in.  NO trailing /
	*/

	$co_dir = '/var/www/html';

	/**
	* Only needed if you have developers cvs access - leave empty for anonymous
	*/

	$svn_login = '';
	$_svn_login = isset($_SERVER['argv'][1]) ? explode('=', $_SERVER['argv'][1]) : array();
	if($_svn_login)
	{
		$svn_login = $_svn_login[1];
	}

	/**
	* What release do you intend to check out? - leave empty for trunk - example: 'Version-2_0-branch'
	*/

	$release_info = 'Version-2_0-branch';

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
	$co_modules[] = 'xmlrpc';
	$co_modules[] = 'soap';
	$co_modules[] = 'registration';
	$co_modules[] = 'addressbook';

	/**
	* FM
	*/
	$co_modules[] = 'mobilefrontend';
	$co_modules[] = 'controller';
	$co_modules[] = 'hrm';
	$co_modules[] = 'property';
	$co_modules[] = 'sms';
	$co_modules[] = 'bim';


	/*
	 * Booking
	 */
	$co_modules[] = 'booking';
	$co_modules[] = 'bookingfrontend';
	$co_modules[] = 'activitycalendar';
	$co_modules[] = 'activitycalendarfrontend';


	/*
	 * rental
	 */
	$co_modules[] = 'rental';
	$co_modules[] = 'frontend';

	/**
	* Some other stuff
	*/
	$co_modules[] = 'logistic';
	$co_modules[] = 'helpdesk';
	$co_modules[] = 'eventplanner';
	$co_modules[] = 'eventplannerfrontend';


	// ****************************************************************************
	// End config section
	// ****************************************************************************

	/**
	* If you do not have developer access to cvs, set to True
	*/
	$cvs_anonymous = false;
	if(!$svn_login)
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
		system("svn {$action} {$revision} svn://{$repository}/{$branch}{$release} portico --non-recursive");
	}
	else
	{
		system("svn {$action} {$revision} svn+ssh://{$svn_login}@{$repository}/{$branch}{$release}  portico --non-recursive");
	}

	chdir($co_dir . '/portico');

	foreach($co_modules as $module)
	{
		if ($cvs_anonymous)
		{
			system("svn {$action} {$revision} svn://{$repository}/{$branch}{$release}/{$module}");
		}
		else
		{
			system("svn {$action} {$revision} svn+ssh://{$svn_login}@{$repository}/{$branch}$release/{$module}");
		}
	}
