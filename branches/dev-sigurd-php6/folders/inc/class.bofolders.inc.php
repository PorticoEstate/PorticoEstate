<?php
/**
 * folders
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package folders
 * @version $Id$
 */

	/**
	* Include phplayersmenu - phplib template class
	*/
	require_once('folders/phplayersmenu/lib/PHPLIB.php');

	/**
	* Include phplayersmenu - common
	*/
	require_once('folders/phplayersmenu/lib/layersmenu-common.inc.php');

	/**
	* Include phplayersmenu - treemenu
	*/
	require_once('folders/phplayersmenu/lib/treemenu.inc.php');

	/**
	* Include phplayersmenu - treemenu
	*/
	require_once('folders/inc/class.treemenu.inc.php');


	/**
	* folders business object
	*
	* @package folders
	*/
	class bofolders
	{
		var $public_functions = Array(
			'folders'                    => True,
			'get_user_list'              => True,
			'return_sorted_user_folders' => True,
			'get_folder_list'            => True
		 );
		var $mid;
		var $debug = false;


		function bofolders()
		{
			$this->mid = new phpGWTreeMenu();

			$this->mid->setLibjsdir('folders/phplayersmenu/libjs');
			//$this->mid->setDirroot(PHPGW_SERVER_ROOT); // not correctly working :-(
			$this->mid->setImgwww('./');
			$this->mid->setImgdir(PHPGW_SERVER_ROOT.'/');

			$this->group            = CreateObject('phpgwapi.accounts');
			$this->bo               = CreateObject('email.bopreferences');

		 }

		function getAppLinkData($targetAppName)
		{
			$appLinkData = array();

			while(list($req_param, $req_value) = each($_REQUEST))
			{
				$search_param = $targetAppName.'_';
				if(strstr($req_param, $search_param) === false)
				{
					continue;
				}
				else
				{
					$req_param = str_replace($search_param, '', $req_param);
					$appLinkData[$req_param] = $req_value;
				}
			}

			//_debug_array($appLinkData);
			return $appLinkData;
		}
		

		function buildFolders($menuname)
		{
			$hookContent = $GLOBALS['phpgw']->hooks->process('getFolderContent');

			$folderContent = array();
			while(list($key, $value) = each($hookContent))
			{
				if (is_array($hookContent[$key]['content']))
				{
					while (list($xkey, $value) = each($hookContent[$key]['content']))
					{
						$folderContent[$xkey] = $hookContent[$key]['content'][$xkey];
					}
				}
			}
			$this->mid->scanTableForMenu($menuname,'', $folderContent);
		}

		function parseFolders($menuname)
		{
			return $this->mid->newTreeMenu($menuname);
		}
	}