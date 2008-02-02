<?php
/**
 * pbwebmaui
 *
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package pbwebmaui
 * @version $Id$
 */
 
 
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp' => 'pbwebmaui',
		'noheader'   => true,
		'nonavbar'   => true
	);
	
	/**
	* Include phpgroupware header
	*/
	require_once('../header.inc.php');

	$obj = CreateObject('pbwebmaui.uipbwebmaui');
	switch ($_GET['action'])
	{
		case 'EditAccount':
			$_GET['menuaction'] = 'pbwebmaui.uipbwebmaui.edit_mailAccount';
			$obj->edit_mailAccount();
			break;
		
		case 'EditDrop':
			$_GET['menuaction'] = 'pbwebmaui.uipbwebmaui.edit_maildrop';
			$obj->edit_maildrop();
			break;
			
		case 'EditFilter':
			$_GET['menuaction'] = 'pbwebmaui.uipbwebmaui.edit_mailFilter';
			$obj->edit_mailFilter();
			break;

		case 'FolderList':
			$_GET['menuaction'] = 'pbwebmaui.uipbwebmaui.list_folders';
			$obj->list_folders();
			break;
			
		default:
			$_GET['user'] = $GLOBALS['phpgw']->session->user['userid'];
			if($GLOBALS['phpgw']->applications->data['admin']['enabled'])
			{
				$_GET['menuaction'] = 'pbwebmaui.uipbwebmaui.list_domain';
				$obj->list_domain();
			}
			else
			{
				$_GET['menuaction'] = 'pbwebmaui.uipbwebmaui.add_mailAccount';
				$obj->edit_mailAccount();
			}				
	}
?>