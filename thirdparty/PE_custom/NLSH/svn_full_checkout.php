#!/usr/bin/php
<?php

	/**
	* Portico Estate
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2017 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package Portico
	* @subpackage development
	* @version $Id: svn_full_checkout.php 4237 2009-11-27 23:17:21Z sigurd $
	*/

	//exit; //remove this line to make the script work

	// ****************************************************************************
	// Config section
	// ****************************************************************************

	 //Example: /usr/bin/php -q svn_full_checkout.php user=<username>

	/**
	* Repository where you are checking out the code.  NO trailing / - example: 'svn.savannah.nongnu.org/fmsystem'
	*/

	$repository = 'svn.savannah.nongnu.org/fmsystem';

	/**
	* What do you want to do? valid actions are 'co' for standard checkout or 'export' for no svn informations
	*/

	$action = 'co';
//	$action = 'export';
	$revision =  '';

	/**
	* Directory that you want the portico directory to go in.  NO trailing /
	*/

	$co_dir = '/srv/www/default';
	$base_dir = 'htdocs';

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
	* Modules you want to checkout
	*/

	/**
	* Base
	*/
	$co_modules = array();
	$co_modules[] = array('admin', $revision);
	$co_modules[] = array('doc', $revision);
	$co_modules[] = array('manual', $revision);
	$co_modules[] = array('phpgwapi', $revision);
	$co_modules[] = array('preferences', $revision);
	$co_modules[] = array('setup', $revision);
	$co_modules[] = array('xmlrpc', $revision);
	$co_modules[] = array('soap', $revision);
	$co_modules[] = array('registration', $revision);
	$co_modules[] = array('addressbook', $revision);

	/**
	* FM
	*/
	$co_modules[] = array('mobilefrontend', $revision);
	$co_modules[] = array('controller', $revision);
//	$co_modules[] = array('hrm', $revision);
	$co_modules[] = array('property', $revision);
	$co_modules[] = array('sms', $revision);
	$co_modules[] = array('bim', $revision);


	/*
	 * Booking
	 */
//	$co_modules[] = array('booking', $revision);
//	$co_modules[] = array('bookingfrontend', $revision);
//	$co_modules[] = array('activitycalendar', $revision);
//	$co_modules[] = array('activitycalendarfrontend', $revision);


	/*
	 * rental
	 */
	$co_modules[] = array('rental', $revision);
//	$co_modules[] = array('frontend', $revision);

	/**
	* Some other stuff
	*/
	$co_modules[] = array('catch', $revision);
//	$co_modules[] = array('logistic', $revision);
//	$co_modules[] = array('helpdesk', $revision);
//	$co_modules[] = array('eventplanner', $revision);
//	$co_modules[] = array('eventplannerfrontend', $revision);


	$pe_custom = array();
	$pe_custom['BK_EBE'] = array
		(
			array('catch', ''),
			array('property', ''),
			array('rental', '')
		);

	// ****************************************************************************
	// End config section
	// ****************************************************************************

	/**
	* If you do not have developer access to cvs, set to True
	*/

	$base_dir = !empty($base_dir) ? $base_dir : 'portico';

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

	$_revision = $revision ? "-r $revision" : '';

	chdir($co_dir);

	echo "$action {$_revision} {$repository}/{$branch}{$release} to {$co_dir}\n";

	if ($cvs_anonymous)
	{
		system("svn {$action} {$_revision} svn://{$repository}/{$branch}{$release} $base_dir --non-recursive");
	}
	else
	{
		system("svn {$action} {$_revision} svn+ssh://{$svn_login}@{$repository}/{$branch}{$release} $base_dir --non-recursive");
	}

	chdir($co_dir . "/{$base_dir}");

	foreach($co_modules as $_module)
	{
		$module = $_module[0];
		$_revision = !empty($_module[1]) ? "-r {$_module[1]}" : '';
		echo "$action {$_revision} {$repository}/{$branch}{$release}/{$module} to {$co_dir}/$base_dir\n";
		if ($cvs_anonymous)
		{
			system("svn {$action} {$_revision} svn://{$repository}/{$branch}{$release}/{$module}");
		}
		else
		{
			system("svn {$action} {$_revision} svn+ssh://{$svn_login}@{$repository}/{$branch}$release/{$module}");
		}
	}

	foreach($pe_custom as $section => $modules)
	{
		foreach ($modules as $_module)
		{
			$module = $_module[0];
			$_revision = !empty($_module[1]) ? "-r {$_module[1]}" : '';

			echo "export {$_revision} {$repository}/thirdparty/PE_custom/{$section}/{$module} to {$co_dir}/$base_dir\n";

			if ($cvs_anonymous)
			{
				system("svn export {$_revision} svn://{$repository}/thirdparty/PE_custom/{$section}/{$module} --force");
			}
			else
			{
				system("svn export {$_revision} svn+ssh://{$svn_login}@{$repository}/thirdparty/PE_custom/{$section}/{$module} --force");
			}
		}
	}
