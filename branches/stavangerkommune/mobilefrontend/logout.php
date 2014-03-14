<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id: logout.php 11511 2013-12-08 20:57:07Z sigurdne $
	*/

	$GLOBALS['phpgw_info']        = array();
	
	$GLOBALS['phpgw_info']['flags'] = array(
		'disable_template_class' => True,
		'currentapp'             => 'logout',
		'noheader'               => True,
		'nofooter'               => True,
		'nonavbar'               => True
	);

	/**
	* Include phpgroupware header
	*/
	include_once('../header.inc.php');

	$sessionid = phpgw::get_var('sessionphpgwsessid');

	$verified = $GLOBALS['phpgw']->session->verify();
	if ($verified)
	{
		if ( is_dir("{$GLOBALS['phpgw_info']['server']['temp_dir']}/{$sessionid}") && !empty($session_id) )
		{
			$dh = dir("{$GLOBALS['phpgw_info']['server']['temp_dir']}/{$sessionid}");
			while ( ($file = $dh->read()) !== false )
			{
				if ( $file == '.' || $file == '..' )
				{
					continue;
				}
				unlink("{$GLOBALS['phpgw_info']['server']['temp_dir']}/{$sessionid}/{$file}");
			}
			rmdir("{$GLOBALS['phpgw_info']['server']['temp_dir']}/{$sessionid}");
			$dh->close();
		}
//		execMethod('phpgwapi.menu.clear'); // moved to hook for login
		$GLOBALS['phpgw']->hooks->process('logout');
		$GLOBALS['phpgw']->session->destroy($sessionid);
	}
	else
	{
		if(is_object($GLOBALS['phpgw']->log))
		{
			$GLOBALS['phpgw']->log->write(array(
				'text' => 'W-VerifySession, could not verify session during logout',
				'line' => __LINE__,
				'file' => __FILE__
			));
		}
	}

	if ( isset($GLOBALS['phpgw_info']['server']['usecookies'])
		&& $GLOBALS['phpgw_info']['server']['usecookies'] )
	{
		$GLOBALS['phpgw']->session->phpgw_setcookie('sessionphpgwsessid');
		$GLOBALS['phpgw']->session->phpgw_setcookie('domain');
	}

	$GLOBALS['phpgw']->redirect_link('mobilefrontend/login.php', array('cd' => 1, 'logout' => true));
