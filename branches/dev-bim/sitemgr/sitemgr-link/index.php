<?php
	/**************************************************************************\
	* phpGroupWare - Web Content Manager                                       *
	* http://www.phpgroupware.org                                              *
	* -----------------------------------------------                          *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp' => 'sitemgr-link',
		'noheader'   => True,
		'nonavbar'   => True,
		'noapi'      => False
	);
	if (file_exists('../header.inc.php'))
	{
		include('../header.inc.php');
	}
	else
	{
		echo "You need to make sure the sitemgr-link app is in the phpgroupware directory.  If you made a symbolic link... it isn't working.";
		die();
	}
	$site_id = get_var('site_id', array('POST', 'GET'), 0);

	$sites_bo = createobject('sitemgr.Sites_BO');
	
	if((isset($GLOBALS['phpgw_info']['user']['preferences']['sitemgr-link']['default_site'])
			&& $GLOBALS['phpgw_info']['user']['preferences']['sitemgr-link']['default_site'] != 0)
		|| $site_id
		||  ($sites_bo->getnumberofsites() == 1)
	)
	{
		if( $site_id) //if one site then there is only one choice
		{
			$GLOBALS['phpgw_info']['user']['preferences']['sitemgr']['currentsite'] = $site_id;
		}
		
		$siteinfo = $sites_bo->get_currentsiteinfo();
		$location = $siteinfo['site_url'];
		$dir = $siteinfo['site_dir'];
		$sitemgr_info['site_url'] = $location;
		if ($location && file_exists($dir . '/functions.inc.php'))
		{
			require_once($dir . '/functions.inc.php');
			Header('Location: ' . sitemgr_link(array('PHPSESSID' => session_id())));
			exit;
		}
		else
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$aclbo = CreateObject('sitemgr.ACL_BO', True);
			echo '<table width="50%"><tr><td>';
			if ($aclbo->is_admin())
			{
				echo lang('Before the public web site can be viewed, you must configure the various locations and preferences.  Please go to the sitemgr setup page by following this link:') . 
				  '<a href="' . 
				  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sitemgr.Common_UI.DisplayPrefs')) . 
				  '">' .
				  lang('sitemgr setup page') .
				  '</a>. ' .
				  lang('Note that you may get this message if your preferences are incorrect.  For example, if config.inc.php is not found in the directory that you specified.');
			}
			else
			{
				echo lang('Your administrator has not yet setup the web content manager for public viewing.  Please contact your administrator to get this fixed.');
			}
			echo '</td></tr></table>';
			$GLOBALS['phpgw']->common->phpgw_footer();
		}
	}
	else
	{
		$sbox = createObject('phpgwapi.sbox2');
		$sites = array(0 => lang('please select'));
		foreach($sites_bo->list_sites(False) as $key => $data)
		{
			$sites[$key] = $data['site_name'];
		}
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		// i know this bad, but it is a quick thing - for now.
		?>
			<h2><?php echo lang('select site to view') ?></h2>
			<form action="<? echo $GLOBALS['phpgw']->link('/sitemgr-link/index.php'); ?>" method="POST">
				<?php echo lang('goto') . ': ' . $sbox->getArrayItem('site_id', 0, $sites, False); ?>
				<input type="submit" name="go" value="<?php echo lang('go'); ?>" /> 
			</form>
			<br />&nbsp;
		<?php
	}
?>
