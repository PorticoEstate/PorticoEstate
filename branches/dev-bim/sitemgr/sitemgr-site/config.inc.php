<?php
	/***********************************************************\
	* Edit the values in the following array to configure       *
	* the site generator.                                       *
	\***********************************************************/

	$sitemgr_info = array(
		// add trailing slash
		'phpgw_path'		=> '../../',
		'htaccess_rewrite'	=> False,
		'phpgw_domain'		=> 'default', //which phpgw install to use
	);

	/***********************************************************\
	* Leave the rest of this file alone.                        *
	\***********************************************************/

		if (!file_exists($sitemgr_info['phpgw_path'] . 'header.inc.php'))
		{
			die("Header file not found.  Either your path to phpGroupWare in the config.inc.php file is bad, or you have not setup phpGroupWare.");
		}

		include($sitemgr_info['phpgw_path'] . 'header.inc.php');

		//hack to support sitemgr on non default domain
		$_GET['domain'] = $sitemgr_info['phpgw_domain'];

		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'login';
		include(PHPGW_SERVER_ROOT . '/phpgwapi/inc/functions.inc.php');
		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'sitemgr-site';

		$site_url = 'http://' . preg_replace('/\/[^\/]*$/','',$_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']) . '/';

		$GLOBALS['phpgw']->db->query("SELECT anonymous_user,anonymous_passwd FROM phpgw_sitemgr_sites WHERE site_url = '$site_url'");
		if ($GLOBALS['phpgw']->db->next_record())
		{
			$anonymous_user = $GLOBALS['phpgw']->db->f('anonymous_user');
			$anonymous_passwd = $GLOBALS['phpgw']->db->f('anonymous_passwd');
		}
		else
		{
			die(lang('THERE IS NO WEBSITE CONFIGURED FOR URL %1.  NOTIFY THE ADMINISTRATOR.',$site_url));
		}
		//this is useful when you changed the API session class to not overgeneralize the session cookies
		if ($GLOBALS['HTTP_GET_VARS']['PHPSESSID'])
		{
			$GLOBALS['phpgw']->session->phpgw_setcookie('PHPSESSID',$GLOBALS['HTTP_GET_VARS']['PHPSESSID']);
		}


		if (! $GLOBALS['phpgw']->session->verify())
		{
			$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($anonymous_user,$anonymous_passwd);
			if (!$GLOBALS['sessionid'])
			{
				die(lang('NO ANONYMOUS USER ACCOUNTS INSTALLED.  NOTIFY THE ADMINISTRATOR.'));
				//exit;
			}
			//$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link($sitemgr_url . 'index.php'));
		}
		?>
