<?php
	/**
	* Preferences - settings hook
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.country');
	phpgw::import_class('phpgwapi.common');

	$_templates = array();
	foreach ( phpgwapi_common::list_templates() as $key => $value)
	{
		$_templates[$key] = $value['title'];
	}

	$_themes = array();
	foreach ( phpgwapi_common::list_themes() as $theme )
	{
		$_themes[$theme] = $theme;
	}


	create_input_box('Max matches per page','maxmatchs',
		'Any listing in phpGW will show you this number of entries or lines per page.<br>To many slow down the page display, to less will cost you the overview.','',3);
	create_select_box('Interface/Template Selection','template_set',$_templates,
		'A template defines the layout of phpGroupWare and it contains icons for each application.');
	create_select_box('Theme (colors/fonts) Selection','theme',$_themes,
		'A theme defines the colors and fonts used by the template.');


/*
	$format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	$format = ($format ? $format : 'Y/m/d') . ', ';
	if ($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12')
	{
		$format .= 'h:i a';
	}
	else
	{
		$format .= 'H:i';
	}
	for ($i = -23; $i<24; $i++)
	{
		$t = time() + $i * 60*60;
		$tz_offset[$i] = $i . ' ' . lang('hours').': ' . date($format,$t);
	}
	create_select_box('Time zone offset','tz_offset',$tz_offset,
		'How many hours are you in front or after the timezone of the server.<br>If you are in the same time zone as the server select 0 hours, else select your locale date and time.');

*/
	$timezone_identifiers = DateTimeZone::listIdentifiers();

	$timezone = array();
	foreach($timezone_identifiers as $identifier)
	{
		$timezone[$identifier] = $identifier;
	}
	create_select_box('Time zone','timezone',$timezone,
		'A time zone is a region of the earth that has uniform standard time, usually referred to as the local time. By convention, time zones compute their local time as an offset from UTC');

	$date_formats = array
	(
		'm/d/Y' => 'm/d/Y',
		'm-d-Y' => 'm-d-Y',
		'm.d.Y' => 'm.d.Y',
		'Y/d/m' => 'Y/d/m',
		'Y-d-m' => 'Y-d-m',
		'Y.d.m' => 'Y.d.m',
		'Y/m/d' => 'Y/m/d',
		'Y-m-d' => 'Y-m-d',
		'Y.m.d' => 'Y.m.d',
		'd/m-Y' => 'd/m-Y',
		'd/m/Y' => 'd/m/Y',
		'd-m-Y' => 'd-m-Y',
		'd.m.Y' => 'd.m.Y',
		'd-M-Y' => 'd-M-Y'
	);
	create_select_box('Date format','dateformat',$date_formats,
		'How should phpGroupWare display dates for you.');

	$time_formats = array(
		'12' => lang('12 hour'),
		'24' => lang('24 hour')
	);
	create_select_box('Time format','timeformat',$time_formats,
		'Do you prefer a 24 hour time format, or a 12 hour one with am/pm attached.');

	create_select_box('Country','country', phpgwapi_country::get_translated_list(),
		'In which country are you. This is used to set certain defaults for you.');
	
	$langs = $GLOBALS['phpgw']->translation->get_installed_langs();
	foreach ($langs as $key => $name)	// if we have a translation use it
	{
		$trans = lang($name);
		if ($trans != $name . '*')
		{
			$langs[$key] = $trans;
		}
	} 
	create_select_box('Language','lang',$langs,
		'Select the language of texts and messages within phpGroupWare.<br>Some languages may not contain all messages, in that case you will see an english message.');
	
	// preference.php handles this function
	if (is_admin())
	{
		create_check_box('Show number of current users','show_currentusers',
			'Should the number of active sessions be displayed for you all the time.');
	}

	reset($GLOBALS['phpgw_info']['user']['apps']);
	while (list($app) = each($GLOBALS['phpgw_info']['user']['apps']))
	{
		if ($GLOBALS['phpgw_info']['apps'][$app]['status'] != 2 && $app)
		{
			$user_apps[$app] = $GLOBALS['phpgw_info']['apps'][$app]['title'] ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : lang($app);
		}
	}
	create_select_box('Default application','default_app',$user_apps,
		"The default application will be started when you enter phpGroupWare or click on the homepage icon.<br>You can also have more than one application showing up on the homepage, if you don't choose a specific application here (has to be configured in the preferences of each application).");

	create_input_box('Currency','currency',
		'Which currency symbol or name should be used in phpGroupWare.');
		
	$account_sels = array(
		'selectbox' => lang('Selectbox'),
		'popup'     => lang('Popup with search')
	);
	create_select_box('How do you like to select accounts','account_selection',$account_sels,
		'The selectbox shows all available users (can be very slow on big installs with many users). The popup can search users by name or group.');

	$account_display = array(
		'firstname' => lang('Firstname'). ' '.lang('Lastname'),
		'lastname'  => lang('Lastname').', '.lang('Firstname'),
	);
	create_select_box('How do you like to display accounts','account_display',$account_display,
		'Set this to your convenience. For security reasons, you might not want to show your Loginname in public.');

	
	$rteditors = array
	(
		'none'		=> lang('none'),
		'fckeditor'	=> 'FCKeditor',
		'tinymce'	=> 'tinyMCE'
	);
	create_select_box('Rich text (WYSIWYG) editor', 'rteditor', $rteditors,
		'Which editor would you like to use for editing html and other rich content?');

	create_check_box('Show helpmessages by default','show_help',
		'Should this help messages shown up always, when you enter the preferences or only on request.');

	$menu_formats = array(
		'sidebox' => lang('Sidebox'),
		'jsmenu' => lang('JS-menu'),
		'ajax_menu' => lang('ajax menu'),
		'no_sidecontent' => lang('No SideContent')
	);
	create_select_box('SideContent','sidecontent',$menu_formats,
		'Do you want your menues as sidecontent');
	create_check_box('Show breadcrumbs','show_breadcrumbs',
			'Should history navigation urls as breadcrumbs');
	create_check_box('activate nowrap in YUI-tables','yui_table_nowrap',
			'activate nowrap in YUI-tables');

