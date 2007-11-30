<?php
	/**
	* Preferences
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @version $Id: index.php 17602 2006-11-27 08:39:53Z sigurdne $
	*/

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'preferences';
	
	/**
	 * Include phpgroupware header
	 */
	include('../header.inc.php');

	$pref_tpl = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$templates = Array(
		'pref' => 'index.tpl'
	);

	$pref_tpl->set_file($templates);

	$pref_tpl->set_block('pref','list');
	$pref_tpl->set_block('pref','app_row');
	$pref_tpl->set_block('pref','app_row_noicon');
	$pref_tpl->set_block('pref','link_row');
	$pref_tpl->set_block('pref','spacer_row');

	if ($GLOBALS['phpgw']->acl->check('run',1,'admin'))
	{
		// This is where we will keep track of our position.
		// Developers won't have to pass around a variable then
		$session_data = $GLOBALS['phpgw']->session->appsession('session_data','preferences');

		if (! is_array($session_data))
		{
			$session_data = array('type' => 'user');
			$GLOBALS['phpgw']->session->appsession('session_data','preferences',$session_data);
		}

		if (! isset($_GET['type']))
		{
			$type = $session_data['type'];
		}
		else
		{
			$type = $_GET['type'];
			$session_data = array('type' => $type);
			$GLOBALS['phpgw']->session->appsession('session_data','preferences',$session_data);
		}

		$tabs[] = array(
			'label' => lang('Your preferences'),
			'link'  => $GLOBALS['phpgw']->link('/preferences/index.php',array('type'=>'user'))
		);
		$tabs[] = array(
			'label' => lang('Default preferences'),
			'link'  => $GLOBALS['phpgw']->link('/preferences/index.php',array('type'=>'default'))
		);
		$tabs[] = array(
			'label' => lang('Forced preferences'),
			'link'  => $GLOBALS['phpgw']->link('/preferences/index.php',array('type'=>'forced'))
		);

		switch($type)
		{
			case 'user':    $selected = 0; break;
			case 'default': $selected = 1; break;
			case 'forced':  $selected = 2; break;
		}
		$pref_tpl->set_var('tabs',$GLOBALS['phpgw']->common->create_tabs($tabs,$selected));
	}

	/**
	 * Dump a row header
	 * 
	 * @param $appname=''
	 * @param $icon
	 */ 
	function section_start($appname='',$icon='')
	{
		global $pref_tpl;

		$pref_tpl->set_var('icon_backcolor',(isset($GLOBALS['phpgw_info']['theme']['row_off'])?$GLOBALS['phpgw_info']['theme']['row_off']:''));
//		$pref_tpl->set_var('link_backcolor',$GLOBALS['phpgw_info']['theme']['row_off']);
		$pref_tpl->set_var('a_name',$appname);
		$pref_tpl->set_var('app_name',$GLOBALS['phpgw_info']['apps'][$appname]['title']);
		$pref_tpl->set_var('app_icon',$icon);
		if ($icon)
		{
			$pref_tpl->parse('rows','app_row',True);
		}
		else
		{
			$pref_tpl->parse('rows','app_row_noicon',True);
		} 
	}

	/**
	 * 
	 * 
	 * @param string $pref_link
	 * @param string $pref_text
	 */
	function section_item($pref_link='',$pref_text='')
	{
		global $pref_tpl;

		$pref_tpl->set_var('pref_link',$pref_link);

		if (strtolower($pref_text) == 'grant access' && isset($GLOBALS['phpgw_info']['server']['deny_user_grants_access']) && $GLOBALS['phpgw_info']['server']['deny_user_grants_access'])
		{
			return False;
		}
		else
		{
			$pref_tpl->set_var('pref_text',$pref_text);
		}

		$pref_tpl->parse('rows','link_row',True);
	} 

	/**
	 * 
	 */
	function section_end()
	{
		global $pref_tpl;

		$pref_tpl->parse('rows','spacer_row',True);
	}

	/**
	 * 
	 * 
	 * @param $appname
	 * @param $file
	 * @param $file2
	 */
	function display_section($appname, $file, $file2 = array() )
	{
		if ( is_array($file2) && count($file2) )
		{
			$file = $file2;
		}
		

		if ( is_array($file) )
		{
			section_start($appname,$GLOBALS['phpgw']->common->image($appname,Array('navbar',$appname)));
			foreach ( $file as $text => $url )
			{
				section_item($url,lang($text));
			}
			section_end(); 
		}
	}

	$GLOBALS['phpgw']->hooks->process('preferences',array('preferences'));
	$pref_tpl->pfp('out','list');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
