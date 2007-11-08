<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id: logout.php,v 1.30 2006/09/09 11:35:16 skwashd Exp $
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

	$GLOBALS['sessionid'] = phpgw::get_var('sessionid', 'string', 'any');
	$GLOBALS['kp3']       = phpgw::get_var('kp3', 'string', 'any');

	$verified = $GLOBALS['phpgw']->session->verify();
	if ($verified)
	{
		if (file_exists($GLOBALS['phpgw_info']['server']['temp_dir'] . SEP . $GLOBALS['sessionid']))
		{
			$dh = opendir($GLOBALS['phpgw_info']['server']['temp_dir'] . SEP . $GLOBALS['sessionid']);
			while ($file = readdir($dh))
			{
				if ($file != '.' && $file != '..')
				{
					unlink($GLOBALS['phpgw_info']['server']['temp_dir'] . SEP . $GLOBALS['sessionid'] . SEP . $file);
				}
			}
			rmdir($GLOBALS['phpgw_info']['server']['temp_dir'] . SEP . $GLOBALS['sessionid']);
		}
		$GLOBALS['phpgw']->hooks->process('logout');
		$GLOBALS['phpgw']->session->destroy($GLOBALS['sessionid'],$GLOBALS['kp3']);
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
?>
