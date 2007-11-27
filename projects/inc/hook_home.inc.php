<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: hook_home.inc.php,v 1.7 2006/12/05 19:40:45 sigurdne Exp $
	* $Source: /sources/phpgroupware/projects/inc/hook_home.inc.php,v $
	*/

	$d1 = strtolower( substr( PHPGW_APP_INC, 0, 3 ) );

	if( $d1 == 'htt' || $d1 == 'ftp' )
	{
		echo "Failed attempt to break in via an old Security Hole!<br />\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	unset( $d1 );

	if ( isset( $GLOBALS['phpgw_info']['user']['preferences']['projects']['mainscreen_showevents'] ) && $GLOBALS['phpgw_info']['user']['preferences']['projects']['mainscreen_showevents'] )
	{
		$projects	= CreateObject( 'projects.uiprojects' );
		$extra_data	= '<td>' . "\n" . $projects->list_projects_home() . '</td>' . "\n";

		$portalbox = CreateObject('phpgwapi.listbox', array
		(
				'title'						=> '<font color="#FFFFFF">'.lang('projects').'</font>',
				//'primary'					=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				//'secondary'					=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				//'tertiary'					=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'primary'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['navbar_bg'],
				'secondary'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['navbar_bg'],
				'tertiary'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['theme']['navbar_bg'],
				'width'						=> '100%',
				'outerborderwidth'			=> '0',
				'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi/templates/default','bg_filler')
			)
		);

		$app_id = $GLOBALS['phpgw']->applications->name2id( 'projects' );
		$GLOBALS['portal_order'][] = $app_id;
		$var = array
		(
			'up'       => array( 'url' => '/set_box.php', 'app' => $app_id ),
			'down'     => array( 'url' => '/set_box.php', 'app' => $app_id ),
			'close'    => array( 'url' => '/set_box.php', 'app' => $app_id ),
			'question' => array( 'url' => '/set_box.php', 'app' => $app_id ),
			'edit'     => array( 'url' => '/set_box.php', 'app' => $app_id )
		);

		while( list( $key,$value ) = each( $var ) )
		{
			$portalbox->set_controls( $key,$value );
		}

		$portalbox->data = array();

		echo "\n" . '<!-- projects info -->' . "\n" . $portalbox->draw( $extra_data ) . '<!-- projects info -->' . "\n";
	}
?>
