<?php
/**
 * pbwebmaui - hook for sidebox menu
 *
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package pbwebmaui
 * @version $Id$
 */

$appname = 'pbwebmaui';

switch ($_GET['menuaction'])
{
	case 'pbwebmaui.uipbwebmaui.show_adminSiteConf':
	case 'pbwebmaui.uipbwebmaui.show_adminMailserver':
		$menu_title = 'admin';
		$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Admin');

		$file = array(
		              array('text' => 'Site Configuration',
		                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.show_adminSiteConf'))
		                    ),
		              array('text' => 'Mailserver Type',
		                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.show_adminMailserver'))
		                    )
		             );
		display_sidebox($appname,$menu_title,$file);

		break;
		
	case 'pbwebmaui.uipbwebmaui.list_domain':
		$menu_title = lang('Maildomain Menu');
		
		$file = array(
		              array('text' => 'New Mail Account',
		                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.add_mailAccount'))
		                    )
		              );
		display_sidebox($appname,$menu_title,$file);
		break;

	case 'pbwebmaui.uipbwebmaui.list_filter':
	case 'pbwebmaui.uipbwebmaui.edit_mailFilter':
	case 'pbwebmaui.uipbwebmaui.edit_mailAccount':
		if ($GLOBALS['phpgw']->applications->data['admin']['enabled'])
		{
			$menu_title = lang('Mailfilter Menu');
				
			$file = array(
			              array('text' => 'filter & OOO',
		                      'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.list_filter',
		                                                                           'dn'         => $_GET['dn']
		                                                        ))
		                     ),
	   	              array('text' => '_NewLine_'),
			              array('text' => 'New Mail Filter',
			                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.edit_mailFilter',
			                                                                         'dn'         => $_GET['dn'],
			                                                                         'type'       => '1'
			                                                      ))
			                    ),
			              array('text' => 'New OOO Message',
			                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.edit_mailFilter',
			                                                                         'dn'         => $_GET['dn'],
			                                                                         'type'       => '2'
			                                                      ))
			                    )
			              );
			display_sidebox($appname,$menu_title,$file);
		}
		break;

	case 'pbwebmaui.uipbwebmaui.list_maildrops':
		$menu_title = lang('Maildrops Menu');
		
		$file = array(
		              array('text' => 'New Maildrop',
		                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.add_maildrop'))
		                    )
		              );
		display_sidebox($appname,$menu_title,$file);
		break;
	
	}
	if($GLOBALS['phpgw']->applications->data['admin']['enabled'])
	{
		$menu_title = 'listdomain';
		$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
	
		$file = array(
		              array('text' => 'Domain view',
		                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.list_domain'))
		                    ),
		              array('text' => 'Maildrops',
		                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.list_maildrops'))
		                    ),
/*
		              array('text' => '_NewLine_'),
		              array('text' => 'Preferences',
		                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.list_domain'))
		                   )
*/
		             );
		display_sidebox($appname,$menu_title,$file);
	}
	else
	{
		$menu_title = lang('Mailfilter Menu');
		
		$file = array(
		              array('text' => 'filter & OOO',
	                      'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.list_filter',
	                                                                           'dn'         => $_GET['dn']
	                                                        ))
	                     ),
   	              array('text' => '_NewLine_'),
		              array('text' => 'New Mail Filter',
		                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.edit_mailFilter',
		                                                                         'dn'         => $_GET['dn'],
		                                                                         'type'       => '1'
		                                                      ))
		                    ),
		              array('text' => 'New OOO Message',
		                    'url'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.edit_mailFilter',
		                                                                         'dn'         => $_GET['dn'],
		                                                                         'type'       => '2'
		                                                      ))
		                    )
		              );
		display_sidebox($appname,$menu_title,$file);
	}
?>