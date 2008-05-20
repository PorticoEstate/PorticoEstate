<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
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
	include_once('header.inc.php');

	$sessionid = phpgw::get_var('sessionid');
	$kp3       = phpgw::get_var('kp3');

	$verified = $GLOBALS['phpgw']->session->verify();
	if ($verified)
	{
		if ( is_dir("{$GLOBALS['phpgw_info']['server']['temp_dir']}/{$sessionid}") )
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
		$GLOBALS['phpgw']->hooks->process('logout');
		$GLOBALS['phpgw']->session->destroy($sessionid,$kp3);
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
	$GLOBALS['phpgw']->session->phpgw_setcookie('sessionid');
	$GLOBALS['phpgw']->session->phpgw_setcookie('kp3');
	$GLOBALS['phpgw']->session->phpgw_setcookie('domain');
	if($GLOBALS['phpgw_info']['server']['sessions_type'] == 'php')
	{
		$GLOBALS['phpgw']->session->phpgw_setcookie(PHPGW_PHPSESSID);
	}

	$GLOBALS['phpgw']->redirect($GLOBALS['phpgw_info']['server']['webserver_url'].'/login.php?cd=1');
