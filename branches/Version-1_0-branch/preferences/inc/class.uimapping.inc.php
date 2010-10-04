<?php
/**
* phpGroupWare
*
* phpgroupware base
* @author Quang Vu DANG <quang_vu.dang@int-evry.fr>
* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
* @package phpgroupware
* @subpackage preferences
* @version $Id$
*/

/**
* this class provide an user interface for manage mapping of current account 
* if phpGroupware is configured to supports the mapping by table
* View, Delete, Allow, Deny  mapping
* Using with Single Sign-On(Shibboleth, CAS...)
*/


	class uimapping
	{
		var $public_functions = array('index' => True);

		/**
		* constructor, sets up variables
		*
		*/						  
		function uimapping()
		{
		}

		/**
		* index
		* Show mapping list of current account
		* Delete, Allow, Deny a mapping to current account
		**/
		function index()
		{

			$GLOBALS['phpgw']->xslttpl->add_file('mapping');
			$table_head = array('lang_ext_user' => lang('ext_user'), 
						'lang_location' => lang('location'),
						'lang_auth_type' => lang('auth type'),
						'lang_allow_deny'=>lang('allow deny'),
						'lang_delete'=>lang('delete'),
						);

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
                        $GLOBALS['phpgw_info']['flags']['app_header'] = lang('Mapping preferences');
			$title = lang('account') . ': ' .  $GLOBALS['phpgw_info']['user']['account_lid'];
			$app_data = array('title' => $title,'table_head' => $table_head);
			
			$phpgw_map_location = isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) ? $_SERVER['HTTP_SHIB_ORIGIN_SITE'] : 'local';
			$phpgw_map_authtype = isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) ? 'shibboleth':'remoteuser';
	
	
			//Create the mapping if necessary :
			if(isset($GLOBALS['phpgw_info']['server']['mapping']) && !empty($GLOBALS['phpgw_info']['server']['mapping']))
			{
				if(!is_object($GLOBALS['phpgw']->mapping))
				{
					$GLOBALS['phpgw']->mapping = CreateObject('phpgwapi.mapping', array('auth_type'=> $phpgw_map_authtype, 'location' => $phpgw_map_location));
				}
			}

			$account_lid =  $GLOBALS['phpgw_info']['user']['account_lid'];
			$data =  $GLOBALS['phpgw']->mapping->get_list($account_lid);
			while(is_array($data) && (list(,$item) = each($data)))
			{
				$ext_user = $item['ext_user'];
				$location = $item['location'];
				$auth_type = $item['auth_type'];
				if($item['status'] == 'A')
				{
					$item['allow_deny_url'] = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uimapping.index',
													'appname'=>'preferences',
													'action'=>'deny',
													'ext_user'=>$ext_user,
													'location'=>$location,
													'auth_type'=>$auth_type));
					$item['lang_action'] = lang('deny');
				}
				else
				{
					$item['allow_deny_url'] = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uimapping.index',
													'appname'=>'preferences',
													'action'=>'allow',
													'ext_user'=>$ext_user,
													'location'=>$location,
													'auth_type'=>$auth_type));
					$item['lang_action'] = lang('allow');
				}
				$item['lang_del'] = lang('delete');
				$item['delete_url'] = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uimapping.index',
												'appname'=>'preferences',
												'action'=>'delete',
												'ext_user'=>$ext_user,
												'location'=>$location,
												'auth_type'=>$auth_type));

				if(isset($_SERVER['REMOTE_USER']) && ($ext_user == $_SERVER['REMOTE_USER']) &&  ($phpgw_map_location == $location) && ($phpgw_map_authtype == $auth_type))
				{
					$item['lang_action'] = '';
					$item['lang_del'] = '';
				}
		
				$app_data['table_row'][] = $item;
			}
			
			$app_data['msg'] = get_var('msg', 'GET', '');

			$action = get_var('action', 'GET', '');
			if ($action)
			{
				$ext_user = get_var('ext_user', 'GET', '');
				$location = get_var('location', 'GET', '');
				$auth_type = get_var('auth_type', 'GET', '');
				
				if(isset($_SERVER['REMOTE_USER']) && ($ext_user == $_SERVER['REMOTE_USER']) &&  ($phpgw_map_location == $location) && ($phpgw_map_authtype == $auth_type))
				{
					$msg = lang('Action denied');
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'preferences.uimapping.index',
					 							'appname'=>'preferences',
												'msg' => $msg));
				}
				
				if ($action == 'allow')
				{
					$status = 'A';
					if($GLOBALS['phpgw']->mapping->update_status(array('account_lid' => $account_lid,
											'ext_user' => $ext_user,
											'location' => $location,
											'auth_type' => $auth_type,
											'status' => $status)))
					{
						$msg = lang('Allow mapping success');
					}
					else
					{
						$msg = lang('mapping is not exist');
					}
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'preferences.uimapping.index',
											'appname'=>'preferences',
											'msg' => $msg));
				}
				else if ($action == 'deny')
				{
			
					$status = 'D';
					if($GLOBALS['phpgw']->mapping->update_status(array('account_lid' => $account_lid,
											'ext_user' => $ext_user,
											'location' => $location,
											'auth_type' => $auth_type,
											'status' => $status)))
					{
						$msg = lang('Deny mapping success');
					}
					else
					{
						$msg = lang('mapping is not exist');
					}
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'preferences.uimapping.index',
											'appname'=>'preferences',
											'msg' => $msg));
									 
				}
				else if ($action == 'delete')
				{
					if($GLOBALS['phpgw']->mapping->delete_mapping(array('account_lid'=>$account_lid,
											'ext_user' => $ext_user,
											'location' => $location,
											'auth_type' => $auth_type)))	
					{
						$msg = lang('Delete mapping success');
					}
					else
					{
						$msg = lang('mapping is not exist');
					}
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'preferences.uimapping.index',
											'appname'=>'preferences',
											'msg' => $msg));
				}
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $app_data));
			}
		}
	}
?>
