<?php
	/**
	* EMail - Preferences hook
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @subpackage hooks
	* @version $Id$
	*/

{
	$title = $appname;
	$file = Array(
		'E-Mail Preferences'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'email.uipreferences.preferences')),
		'Extra E-Mail Accounts'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'email.uipreferences.ex_accounts_list')),
		'E-Mail Filters'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'email.uifilters.filters_list'))
	);
	// relfbecker recommends NOT using a version test for xslt check
	if (is_object($GLOBALS['phpgw']->xslttpl))
	{
		$phpgw_before_xslt = False;
	}
	else
	{
		$phpgw_before_xslt = True;
	}
	// now display according to the version of the template system in use
	if ($phpgw_before_xslt == True)
	{
		// the is the OLD, pre-xslt way to display pref items
		display_section($appname,$title,$file);
	}
	else
	{
		// this is the xslt template era
		display_section($appname,$file);
	}
	/*
	$this_ver = $GLOBALS['phpgw_info']['server']['versions']['phpgwapi'];
	$pre_xslt_ver = '0.9.14.0.1.1';
	if (function_exists(amorethanb))
	{
		if (($this_ver)
		&& (amorethanb($this_ver, $pre_xslt_ver)))
		{
			// this is the xslt template era
			display_section($appname,$file);
		}
		else
		{
			display_section($appname,$title,$file);
		}
	}
	else
	{
		if (($this_ver)
		&& ($GLOBALS['phpgw']->common->cmp_version_long($this_ver, $pre_xslt_ver)))
		{
			// this is the xslt template era
			display_section($appname,$file);
		}
		else
		{
			display_section($appname,$title,$file);
		}
	}
	*/
}
?>
