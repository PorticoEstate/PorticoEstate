<?php
	/**
	* phpGroupWare - Start file for the phpGroupWare help system
	*
	* phpgroupware base
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage help
	* @version $Id$
	*/

	/**
	* @global array $GLOBALS['phpgw_info']
	*/
	$GLOBALS['phpgw_info'] = array();

	$app = $_GET['app'];

	if (!$app)
	{
		$app = 'help';
	}

	/**
	* @global array $GLOBALS['phpgw_info']['flags']
	*/
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'headonly'		=> True,
		'currentapp'	=> $app
	);
	
	/**
	* Include phpgroupware header
	*/
	include_once('header.inc.php');

	$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
	
	$GLOBALS['phpgw']->help = CreateObject('phpgwapi.help_helper');

	if ($app == 'help')
	{
		$GLOBALS['phpgw']->hooks->process('help',array('manual'));
	}
	else
	{
		$GLOBALS['phpgw']->hooks->single('help',$app);
	}

	$appname		= lang('Help');
	$function_msg	= lang('app');

	$GLOBALS['phpgw_info']['flags']['app_header'] = $appname . ' - ' . $appname;

	$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('help' => $GLOBALS['phpgw']->help->output));

?>
