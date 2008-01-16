<?php
	/**************************************************************************\
	* phpGroupWare - account administration                                    *
	* http://www.phpgroupware.org                                              *
	* Written by coreteam <phpgroupware-developers@gnu.org>                    *
	* -----------------------------------------------------                    *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: class.uiaccounts.inc.php 18358 2007-11-27 04:43:37Z skwashd $ */

	class admin_uiaccounts
	{
		public $public_functions = array
		(
			'list_groups'	=> True,
			'list_users'	=> True,
			'delete_group'	=> True,
			'delete_user'	=> True,
			'edit_user'		=> True,
			'edit_group'	=> True,
			'view_user'		=> True,
			'group_manager'	=> True
		);

		private $bo;
		private $nextmatchs;

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::admin';

			$this->bo = createObject('admin.boaccounts');
			$this->nextmatchs =createObject('phpgwapi.nextmatchs');

			@set_time_limit(300);
		}

		function row_action($action,$type,$account_id)
		{
			return '<a href="'.$GLOBALS['phpgw']->link('/index.php',Array(
				'menuaction' => 'admin.uiaccounts.'.$action.'_'.$type,
				'account_id' => $account_id
			)).'"> '.lang($action).' </a>';
		}

		function list_groups()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::groups';
			
			if ( phpgw::get_var('done', 'bool', 'POST') || $GLOBALS['phpgw']->acl->check('group_access', PHPGW_ACL_READ,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uimainscreen.mainscreen'));
			}

			if ( phpgw::get_var('add', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiaccounts.edit_group', 'account_id' => 0) );
			}

			$start = phpgw::get_var('start', 'int');

			$order = phpgw::get_var('order', 'string', 'GET', 'account_lid');
			
			$sort = phpgw::get_var('sort', 'string', 'GET', 'ASC');
			
			$total = 0;

			$query = phpgw::get_var('query', 'string', 'POST');

			$GLOBALS['cd'] = phpgw::get_var('cd', 'int', 'GET');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('administration') . ': ' . lang('list groups');

			$GLOBALS['phpgw']->xslttpl->add_file('groups');

			$account_info = $GLOBALS['phpgw']->accounts->get_list('groups',$start,$sort, $order, $query, $total);
			//echo '<pre>' . print_r($account_info, true) . '</pre>';
			$total = $GLOBALS['phpgw']->accounts->total;

			$link_data = array
			(
				'menuaction' => 'admin.uiaccounts.list_groups'
			);

			$group_header = array
			(
				'sort_name'				=> $this->nextmatchs->show_sort_order(array
											(
												'sort'	=> $sort,
												'var'	=> 'account_lid',
												'order'	=> $order,
												'extra'	=> $link_data
											)),
				'lang_name'				=> lang('name'),
				'lang_edit'				=> lang('edit'),
				'lang_delete'			=> lang('delete'),
				'lang_sort_statustext'	=> lang('sort the entries')
			);

			foreach ( $account_info as $account )
			{
				$group_data[] = Array
				(
					'edit_url'					=> ($this->bo->check_rights('edit')?$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.edit_group', 'account_id'=> $account['account_id'])):''),
					'lang_edit'					=> ($this->bo->check_rights('edit')?lang('edit'):''),
					'lang_edit_statustext'		=> ($this->bo->check_rights('edit')?lang('edit this group'):''),
					'group_name'				=> (!$account['account_lid']?'':$account['account_lid']),
					'delete_url'				=> ($this->bo->check_rights('delete')?$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.delete_group', 'account_id' =>$account['account_id'])):''),
					'lang_delete_statustext'	=> ($this->bo->check_rights('delete')?lang('delete this group'):''),
					'lang_delete'				=> ($this->bo->check_rights('delete')?lang('delete'):'')
				);
			}

			$group_add = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a group'),
				'action_url'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'admin.uiaccounts.list_groups')),
				'lang_done'				=> lang('done'),
				'lang_done_statustext'	=> lang('return to admin mainscreen'),
				'add_access'			=> ($this->bo->check_rights('add')?'yes':''),
			);

			$nm = array
			(
				'start'	=> $start,
 				'num_records'	=> count($account_info),
 				'all_records'	=> $total,
				'link_data'		=> $link_data
			);

			$data = array
			(
				'nm_data'		=> $this->nextmatchs->xslt_nm($nm),
				'search_data'	=> $this->nextmatchs->xslt_search(array('query' => $query,'link_data' => $link_data)),
				'group_header'	=> $group_header,
				'group_data'	=> $group_data,
				'group_add'		=> $group_add,
				'search_access'	=> ($this->bo->check_rights('search')?'yes':'')
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('group_list' => $data));
		}

		function list_users($param_cd = '')
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::users';

			if ( phpgw::get_var('done', 'bool', 'POST') 
				|| $GLOBALS['phpgw']->acl->check('account_access',1,'admin') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'admin.uimainscreen.mainscreen'));
			}

			if ( phpgw::get_var('add', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiaccounts.edit_user'));
			}

			if($param_cd)
			{
				$cd = $param_cd;
			}

			//this is a work around hack for the ugly nextmatch code
			$query = $GLOBALS['query'] = phpgw::get_var('query', 'string', 'POST');
			$start = phpgw::get_var('start', 'int');
			$order = phpgw::get_var('order', 'string', 'GET', 'account_lid');
			$sort = phpgw::get_var('sort', 'string', 'GET', 'ASC');
			
			$total = 0;

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('administration') . ': ' . lang('list users');

			$GLOBALS['phpgw']->xslttpl->add_file('users');

			$account_info = $GLOBALS['phpgw']->accounts->get_list('accounts', $start, $sort, $order, $query, $total);
			$total = $GLOBALS['phpgw']->accounts->total;

			$link_data = array
			(
				'menuaction' => 'admin.uiaccounts.list_users'
			);

			$user_header = array
			(
				'sort_lid'				=> $this->nextmatchs->show_sort_order(array
											(
												'sort'	=> $sort,
												'var'	=> 'account_lid',
												'order'	=> $order,
												'extra'	=> $link_data
											)),
				'lang_lid'				=> lang('loginid'),
				'sort_lastname'			=> $this->nextmatchs->show_sort_order(array
											(
												'sort'	=> $sort,
												'var'	=> 'account_lastname',
												'order'	=> $order,
												'extra'	=> $link_data
											)),
				'lang_lastname'				=> lang('Lastname'),
				'sort_firstname'			=> $this->nextmatchs->show_sort_order(array
											(
												'sort'	=> $sort,
												'var'	=> 'account_firstname',
												'order'	=> $order,
												'extra'	=> $link_data
											)),
				'lang_firstname'		=> lang('firstname'),
				'sort_status'			=> $this->nextmatchs->show_sort_order(array
											(
												'sort'	=> $sort,
												'var'	=> 'account_status',
												'order'	=> $order,
												'extra'	=> $link_data
											)),
				'lang_status'			=> lang('status'),
				'lang_view'				=> lang('view'),
				'lang_edit'				=> lang('edit'),
				'lang_delete'			=> lang('delete'),
				'lang_sort_statustext'	=> lang('sort the entries')
			);

			$user_data = array();
			foreach ( $account_info as $account )
			{
				$user_data[] = Array
				(
					'view_url'					=> $this->bo->check_rights('view','account_access')
													? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.view_user', 'account_id' => $account['account_id']) )
													: '',
					'lang_view'					=> ($this->bo->check_rights('view','account_access')?lang('view'):''),
					'lang_view_statustext'		=> ($this->bo->check_rights('view','account_access')?lang('view this user'):''),
					'edit_url'					=> $this->bo->check_rights('edit','account_access')
													? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.edit_user', 'account_id' => $account['account_id']) )
													: '',
					'lang_edit'					=> ($this->bo->check_rights('edit','account_access')?lang('edit'):''),
					'lang_edit_statustext'		=> ($this->bo->check_rights('edit','account_access')?lang('edit this user'):''),
					'lid'						=> (!$account['account_lid']?'':$account['account_lid']),
					'firstname'					=> (!$account['account_firstname']?'':$account['account_firstname']),
					'lastname'					=> (!$account['account_lastname']?'':$account['account_lastname']),
					'status'					=> (!$account['account_status']?'':$account['account_status']),
					'delete_url'				=> $this->bo->check_rights('delete','account_access')
													?$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.delete_user', 'account_id' => $account['account_id']) )
													: '',
					'lang_delete_statustext'	=> ($this->bo->check_rights('delete','account_access')?lang('delete this user'):''),
					'lang_delete'				=> ($this->bo->check_rights('delete','account_access')?lang('delete'):'')
				);
			}

			$user_add = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a user'),
				'action_url'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.list_users')),
				'lang_done'				=> lang('done'),
				'lang_done_statustext'	=> lang('return to admin mainscreen'),
				'add_access'			=> ($this->bo->check_rights('add','account_access')?'yes':''),
			);

			$nm = array
			(
				'start'			=> $start,
 				'num_records'	=> count($account_info),
 				'all_records'	=> $total,
				'link_data'		=> $link_data
			);

			$data = array
			(
				'nm_data'		=> $this->nextmatchs->xslt_nm($nm),
				'search_data'	=> $this->nextmatchs->xslt_search(array('query' => $query,'link_data' => $link_data)),
				'user_header'	=> $user_header,
				'user_data'		=> $user_data,
				'user_add'		=> $user_add,
				'search_access'	=> ($this->bo->check_rights('search','account_access')?'yes':'')
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('account_list' => $data));
		}

		function edit_group()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::groups';

			$account_apps	= array();
			$account_id		= phpgw::get_var('account_id', 'int');
			$error_list		= '';
			$values			= phpgw::get_var('values', 'string', 'POST', array());

			if ( (isset($values['cancel']) && $values['cancel'])
				|| !$account_id && $GLOBALS['phpgw']->acl->check('group_access', PHPGW_ACL_EDIT, 'admin')
				|| $account_id && $GLOBALS['phpgw']->acl->check('group_access', PHPGW_ACL_PRIVATE, 'admin') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiaccounts.list_groups'));
			}

			//echo 'POST variables<pre>' . print_r($values, true) . '</pre>';
			if ( isset($values['save']) && $values['save'] )
			{
				$account_apps	= phpgw::get_var('account_apps', 'bool', 'POST');
				$account_user	= phpgw::get_var('account_user', 'int', 'POST');
				$group_manager	= phpgw::get_var('group_manager', 'int', 'POST');
				
				$error = $this->bo->validate_group($values);

				if (is_array($error))
				{
					$error_list = $GLOBALS['phpgw']->common->error_list($error);
					echo 'FIXME errors are not displayed with idots :(<pre>' . print_r($error, true) . '</pre>';
				}
				else
				{
					if (is_array($account_user))
					{
						$values['account_user'] = $account_user;
					}

					if (is_array($account_apps))
					{
						$values['account_apps'] = $account_apps;
					}
					else
					{
						$values['account_apps'] = array();
					}
					
					if ( $group_manager )
					{
						$values['group_manager'] = $group_manager;
					}
					$account_id = $this->bo->edit_group($values);
				}
			}
			
			if ( !isset($GLOBALS['phpgw']->js) || !is_object($GLOBALS['phpgw']->js) )
			{
				$GLOBALS['phpgw']->js = createObject('phpgwapi.javascript');
			}
			$js =& $GLOBALS['phpgw']->js;
			$js->validate_file('base', 'groups', 'admin');

			$group = createObject('phpgwapi.accounts', $account_id, 'g');
			$group->read_repository();
			$group->member($account_id);
			$group_members = $group->get_members();
			$group_apps = $this->bo->load_group_apps($account_id);
			
			$group_manager = $GLOBALS['phpgw']->acl->get_ids_for_location($account_id, PHPGW_ACL_GROUP_MANAGERS, 'phpgw_group');

			if ( !is_array($group_members) )
			{
				$group_members = array();
			}

			// this information should be provided by the app itself
			$apps_with_acl = array
			(
				'addressbook'	=> array('top_grant' => true),
				'bookmarks'		=> array('top_grant' => true),
				'calendar'		=> array('top_grant' => true),
				'filemanager'	=> array('top_grant' => true),
				'img'			=> array('top_grant' => true),
				'infolog'		=> array('top_grant' => true),
				'inv'			=> array('top_grant' => true),
				'netsaint'		=> array('top_grant' => true),
				'notes'			=> array('top_grant' => true),
				'phonelog'		=> array('top_grant' => true),
				'phpwebhosting'	=> array('top_grant' => true),
				'projects'		=> array('top_grant' => true),
				'todo'			=> array('top_grant' => true),
				'tts'			=> array('top_grant' => true),
			);

			$GLOBALS['phpgw']->acl->verify_location($apps_with_acl);
			
			$accounts =& $GLOBALS['phpgw']->accounts;
			$account_list = $accounts->get_list('accounts');
			$account_num = count($account_list);
			
			$members = array();
			$user_list = array();
			$i = 0;
			foreach ( $account_list as $key => $entry )
			{
				$user_list[$i] = array
				(
					'account_id'   => $entry['account_id'],
					'account_name' => $GLOBALS['phpgw']->common->display_fullname($entry['account_lid'],
																				  $entry['account_firstname'],
																				  $entry['account_lastname']
																				 ),
 					'selected'		=> in_array(intval($entry['account_id']), $group_members) ? ' selected' : ''
				);
				if ( in_array( (int)$entry['account_id'], $group_members) )
				{
					$user_list[$i]['selected'] = 'selected';
					$members[$entry['account_id']] = $user_list[$i]['account_name'];
				}
				else
				{
					$user_list[$i]['selected'] = '';
				} 
				++$i;
			}

			$manager_list = array();
			foreach ( $members as $id => $username )
			{
				$manager_list[] = array
				(
					'account_id'	=> $id,
					'account_name'	=> $username,
					'selected' 		=> isset($group_manager[0]) && $id == $group_manager[0] ? 'selected' : ''
				);
			}
			
			$apps = array_keys($GLOBALS['phpgw_info']['apps']);
			asort($apps);
			
			$img_acl = $GLOBALS['phpgw']->common->image('admin', 'share', '.png', false);
			$img_acl_grey = $GLOBALS['phpgw']->common->image('admin', 'share-grey', '.png', false);
			$img_grants = $GLOBALS['phpgw']->common->image('admin', 'dot', '.png', false);
			$img_grants_grey = $GLOBALS['phpgw']->common->image('admin', 'dot-grey', '.png', false);

			foreach ( $apps as $app )
			{
				if ($GLOBALS['phpgw_info']['apps'][$app]['enabled'] && $GLOBALS['phpgw_info']['apps'][$app]['status'] != 3)
				{
					$grants_enabled = isset($apps_with_acl[$app]) && $account_id;
					$app_list[] = array
					(
						'app_name'		=> $app,
						'app_title'		=> lang($app),
						'checkbox_name'	=> "account_apps[{$app}]",
						'checked'       => isset($group_apps[$app]),
						'acl_url'       => $grants_enabled
											? $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'preferences.uiadmin_acl.list_acl',
																						'acl_app'		=> $app,
																						'cat_id'=>'groups',
																						'module'=>'.')) : '',
						'acl_img'		=> $grants_enabled ? $img_acl : $img_acl_grey,
						'acl_img_name'	=> lang('Set general permissions'),
						'grant_img'		=> $grants_enabled ? $img_grants : $img_grants_grey,
						'grant_img_name'=> lang('Grant Access'),
						'grant_url'		=> $grants_enabled
											? $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'preferences.uiadmin_acl.aclprefs',
																						'acl_app'		=> $app,
																						'cat_id'=>'groups',
																						'module'=>'.',
																						'granting_group'=>$account_id)) : ''
					);
				}
			}
			
			$GLOBALS['phpgw']->xslttpl->add_file('msgbox', PHPGW_TEMPLATE_DIR);			
			$GLOBALS['phpgw']->xslttpl->add_file('groups');
			$GLOBALS['phpgw_info']['flags']['app_header'] =  $account_id > 0 ? lang('edit group') : lang('add group');
			$data = array
			(
				'account_id'		=> $account_id,
				'app_list'			=> $app_list,				
				'edit_url'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.edit_group',
																					'account_id' => $account_id
																				   )),
				'group_manager'		=> $manager_list,
				'guser_list'		=> $user_list,
				'img_close'			=> $GLOBALS['phpgw']->common->image('phpgwapi', 'stock_close', '.png', false),
				'img_save'			=> $GLOBALS['phpgw']->common->image('phpgwapi', 'stock_save', '.png', false),
				'lang_account_name'	=> lang('group name'),
				'lang_acl'			=> lang('acl'),
				'lang_application'	=> lang('application'),
				'lang_cancel'		=> lang('cancel'),
				'lang_close'		=> lang('close'),
				'lang_grant'		=> lang('grant'),
				'lang_group_manager'=> lang('group manager'),
				'lang_include_user'	=> lang('members'),
				'lang_permissions'	=> lang('applications'),				
				'lang_save'			=> lang('save'),
				'msgbox_data'		=> $error_list,
				'select_size'		=> 5,
				'value_account_name'=> $group->lid,
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('group_edit' => $data));
		}

		function edit_user()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::users';

			$cd                            = phpgw::get_var('cd', 'int', 'GET');
			$account_id                    = phpgw::get_var('account_id', 'int');
			$values                        = phpgw::get_var('values', 'string', 'POST');
			$values['old_loginid']         = phpgw::get_var('old_loginid', 'string', 'GET');
			$values['account_groups']      = phpgw::get_var('account_groups', 'int', 'POST');
			$values['account_permissions'] = phpgw::get_var('account_permissions', 'bool', 'POST');
			//FIXME Caeies fix waiting for JSCAL
			$values['account_expires_year']= phpgw::get_var('account_expires_year', 'int', 'POST');
			$values['account_expires_month']= phpgw::get_var('account_expires_month', 'string', 'POST'); // we use string here to allow for MMM
			$values['account_expires_day'] = phpgw::get_var('account_expires_day', 'int', 'POST');

			if ( (isset($values['cancel']) && $values['cancel']) 
				|| !$account_id && $GLOBALS['phpgw']->acl->check('account_access', PHPGW_ACL_EDIT, 'admin') 
				|| $account_id && $GLOBALS['phpgw']->acl->check('account_access', PHPGW_ACL_PRIVATE, 'admin') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiaccounts.list_users'));
			}
			$error_list = '';
			if (isset($values['save']) && $values['save'])
			{
				$error = $this->bo->validate_user($values);

				if (is_array($error))
				{
					$error_list = $GLOBALS['phpgw']->common->error_list($error);
				}
				else
				{
					if ($account_id)
					{
						$values['account_id'] = $account_id;
					}
					$this->bo->save_user($values);
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiaccounts.list_users'));
				}
			}

			$sbox = CreateObject('phpgwapi.sbox');

			/* XXX Caeies And ?
			if ($GLOBALS['phpgw_info']['server']['ldap_extra_attributes'] && ($GLOBALS['phpgw_info']['server']['account_repository'] == 'ldap'))
			{
			}
			 */
			//XXX Caeies Where does that comes from ???
			//print_debug('Type : '.gettype($_userData).'<br>_userData(size) = "'.$_userData.'"('.strlen($_userData).')');

			$GLOBALS['phpgw']->xslttpl->add_file('users');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('administration') . ': ' . ($account_id?lang('edit user account'):lang('add user account'));

			$acl = createObject('phpgwapi.acl', $account_id);
			if ($account_id)
			{
				$user_info = array
				(
					'account_name' => $GLOBALS['phpgw']->accounts->id2name($account_id),
					'account_user' => $this->bo->load_group_users($account_id),
					'account_apps' => $this->bo->load_group_apps($account_id)
				);
			}

			if($account_id)
			{
				$account = createObject('phpgwapi.accounts',intval($account_id),'u');
				$userData = $account->read_repository();
				$userGroups = $account->membership($account_id);
				$userData['anonymous'] = $acl->check('anonymous', 1, 'phpgwapi');
				$userData['changepassword'] = $acl->check('changepassword', 0xFFFF, 'preferences');
				
			}
			else
			{
				$account = createObject('phpgwapi.accounts');
				$userData = Array();
				$userData['status'] = 'A';
				$userData['anonymous'] = false;
				$userData['changepassword'] = true;
				$userGroups = Array();
			}
			$allGroups = $account->get_list('groups');

			if ($userData['expires'] == -1) //switch to js cal - skwashd
			{
				$userData['account_expires_month'] = 0;
				$userData['account_expires_day']   = 0;
				$userData['account_expires_year']  = 0;
			}
			else
			{
				$time_var = time() + $GLOBALS['phpgw_info']['server']['auto_create_expire']; // we assume this is sane
				$userData['account_expires_month'] = date('m',$userData['expires'] > 0 ? $userData['expires'] : $time_var);
				$userData['account_expires_day']   = date('d',$userData['expires'] > 0 ? $userData['expires'] : $time_var);
				$userData['account_expires_year']  = date('Y',$userData['expires'] > 0 ? $userData['expires'] : $time_var);
			}

			$homedirectory = '';
			$loginshell = '';
			$lang_homedir  = '';
			$lang_shell = '';
			if (isset($GLOBALS['phpgw_info']['server']['ldap_extra_attributes']) && !empty($GLOBALS['phpgw_info']['server']['ldap_extra_attributes']))
			{
				$lang_homedir	= lang('home directory');
				$lang_shell		= lang('login shell');
				$homedirectory = '<input name="homedirectory" value="'
					. ($account_id ? $userData['homedirectory'] : "{$GLOBALS['phpgw_info']['server']['ldap_account_home']}/{$account_lid}")
					. '">';
				$loginshell = '<input name="loginshell" value="'
					. ($account_id?$userData['loginshell']:$GLOBALS['phpgw_info']['server']['ldap_account_shell'])
					. '">';
			}
			
			$add_masters	= $GLOBALS['phpgw']->acl->get_ids_for_location('addressmaster',7,'addressbook');
			$add_users	= $GLOBALS['phpgw']->accounts->return_members($add_masters);
			$masters	= $add_users['users'];

			if (is_array($masters) && in_array($GLOBALS['phpgw_info']['user']['account_id'],$masters))
			{
				if($userData['person_id'])
				{
					$url_contacts_text = lang('Edit entry');
					$url_contacts =   $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'    => 'addressbook.uiaddressbook.edit_person',
						'ab_id'         => $userData['person_id'],
						'referer'       => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.edit_user', 'account_id' =>  $account_id) )  
					));
				}
				else
				{
					$url_contacts_text = lang('This account has no contact entry yet');
					$url_contacts = '#';
				}
			}
			else
			{
				$url_contacts_text = lang('You do not have edit access to addressmaster contacts');
				$url_contacts =   $GLOBALS['phpgw']->link('/index.php', array
				(
					'menuaction'    => 'admin.uiaclmanager.edit_addressmasters',
					'account_id'    => $GLOBALS['phpgw_info']['user']['account_id'],
					'referer'       => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.edit_user', 'account_id' =>  $account_id) )
				));
			}
			
			$_y = $sbox->getyears('account_expires_year',$userData['account_expires_year'],date('Y'),date('Y')+10);
			$_m = $sbox->getmonthtext('account_expires_month',$userData['account_expires_month']);
			$_d = $sbox->getdays('account_expires_day',$userData['account_expires_day']);
			

		/*	$account_file_space = '';
			if (!$userData['file_space'])
			{
				$userData['file_space'] = $GLOBALS['phpgw_info']['server']['vfs_default_account_size_number'] . "-" . $GLOBALS['phpgw_info']['server']['vfs_default_account_size_type'];
			}
			$file_space_array = explode ('-', $userData['file_space']);
			$account_file_space_number = $file_space_array[0];
			$account_file_space_type = $file_space_array[1];
			$account_file_space_type_selected[$account_file_space_type] = ' selected';

			$account_file_space = '<input type=text name="account_file_space_number" value="' . trim($account_file_space_number) . '" size="7">';
			$account_file_space_select ='<select name="account_file_space_type">';
			$account_file_space_types = array ('gb', 'mb', 'kb', 'b');
			while (list ($num, $type) = each ($account_file_space_types))
			
			{
				$account_file_space_select .= '<option value="'.$type.'"' . $account_file_space_type_selected[$type] . '>' . strtoupper ($type) . '</option>';
			}
			$account_file_space_select .= '</select>';

			$var = Array(
				'lang_file_space'    => 'File space',
				'account_file_space' => $account_file_space,
				'account_file_space_select' => $account_file_space_select
			);
			$t->set_var($var);
		*/

			reset($allGroups);
			while (list($key,$value) = each($allGroups)) 
			{
				$group_list[] = array
				(
					'account_id'		=> $value['account_id'],
					'account_lid'		=> $value['account_lid']
				);
			}

			for ($i=0;$i<count($userGroups);$i++)
			{
				for($j=0;$j<count($group_list);$j++)
				{
					if ($userGroups[$i]['account_id'] == $group_list[$j]['account_id'])
					{
						$group_list[$j]['selected'] = 'yes';
					}
				}
			}

			/* create list of available apps */
			$i = 0;
			$apps = createObject('phpgwapi.applications',$account_id);
			$db_perms = $apps->read_account_specific();

			$available_apps = $GLOBALS['phpgw_info']['apps'];
			@asort($available_apps);
			foreach ( $available_apps as $key => $application ) 
			{
				if ($application['enabled'] && $application['status'] != 3) 
				{
					$perm_display[$i]['appName']        = $key;
					$perm_display[$i]['translatedName'] = lang($perm_display[$i]['appName']);
					$i++;
				}
			}

			/* create apps output */
			$appRightsOutput = '';
			//@reset($perm_display);
			for ($i=0;$i<count($perm_display);$i++) 
			{
				$app_list[] = array
				(
					'app_title'		=> $perm_display[$i]['translatedName'],
					'checkbox_name'	=> 'account_permissions[' . $perm_display[$i]['appName'] . ']',
					'checked'		=> (isset($userData['account_permissions']) && $userData['account_permissions'][$perm_display[$i]['appName']]) 
										|| (isset($db_perms[$perm_display[$i]['appName']]) && $db_perms[$perm_display[$i]['appName']])
				);
			}

			$page_params['menuaction'] = 'admin.uiaccounts.edit_user';
			if($account_id)
			{
				$page_params['account_id']  = $account_id;
				$page_params['old_loginid'] = rawurlencode($userData['account_lid']);
			}

			$data = array
			(
				'msgbox_data'			=> $error_list,
				'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',$page_params),
				'lang_lid'				=> lang('loginid'),
				'lang_account_active'	=> lang('account active'),
				'lang_anonymous'		=> lang('Anonymous User (not shown in list sessions)'),
				'lang_changepassword'	=> lang('Can change password'),
				'lang_contact'			=> lang('contact'),
				'lang_password'			=> lang('password'),
				'lang_reenter_password'	=> lang('Re-Enter Password'),
				'lang_lastname'			=> lang('lastname'),
				'lang_groups'			=> lang('groups'),
				'lang_expires'			=> lang('expires'),
				'lang_firstname'		=> lang('firstname'),
				'lang_applications'		=> lang('applications'),
				'lang_quota'			=> lang('quota'),
				'lang_save'				=> lang('save'),
				'lang_cancel'			=> lang('cancel'),
				'select_expires'		=> $GLOBALS['phpgw']->common->dateformatorder($_y,$_m,$_d,True),
				'lang_never'			=> lang('Never'),
				'account_lid'			=> $userData['account_lid'],
				'lang_homedir'			=> $lang_homedir,
				'lang_shell'			=> $lang_shell,
				'homedirectory'			=> $homedirectory,
				'loginshell'			=> $loginshell,
				'account_status'		=> ($userData['status']?'yes':''),
				'account_firstname'		=> $userData['account_firstname'],
				'account_lastname'		=> $userData['account_lastname'],
				'account_passwd'		=> '',
				'account_passwd_2'		=> '',
				'account_quota'			=> $userData['quota'],
				'anonymous'				=> (int) $userData['anonymous'],
				'changepassword'		=> (int) $userData['changepassword'],
				'expires_never'			=> (($userData['expires'] == -1)?'yes':''),
				'group_list'			=> $group_list,
				'app_list'				=> $app_list,
				'url_contacts'			=> $url_contacts,
				'url_contacts_text'		=> $url_contacts_text
			);

			/* create the menu on the left, if needed
			$menuClass = CreateObject('admin.uimenuclass');
			This is now using ExecMethod()
			$t->set_var('rows',ExecMethod('admin.uimenuclass.createHTMLCode','edit_user')); */
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('account_edit' => $data));
		}

		function view_user()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::users';

			$account_id = phpgw::get_var('account_id', 'int', 'GET');
			if ( $GLOBALS['phpgw']->acl->check('account_access', 8, 'admin') || !$account_id )
			{
				$this->list_users();
				return false;
			}

			$account = createObject('phpgwapi.accounts', $account_id,'u');
			$userData = $account->read_repository();
			
			if ($userData['status'])
			{
				$userData['account_status'] = lang('Enabled');
			}
			else
			{
				$userData['account_status'] = lang('Disabled');
			}
			
			if ($userData['lastlogin'])
			{
				$userData['account_lastlogin'] = $GLOBALS['phpgw']->common->show_date($userData['lastlogin']);
			}
			else
			{
				$userData['account_lastlogin'] = lang('Never');
			}
			
			if ( (int) $userData['expires'] <> -1)
			{
				$userData['input_expires'] = $GLOBALS['phpgw']->common->show_date($userData['expires']);
			}
			else
			{
				$userData['input_expires'] = lang('Never');
			}
			// Find out which groups they are members of
			$usergroups = $account->membership($account_id);
			while (list(,$group) = each($usergroups))
			{
				$userData['groups'][] = $group['account_name'];
			}

			//Permissions
			$availableApps = $GLOBALS['phpgw_info']['apps'];
			$apps  = CreateObject('phpgwapi.applications', $account_id);
			$perms = array_keys($apps->read_account_specific());
			if(is_array($availableApps) && count($availableApps))
			{
				asort($availableApps);
				$i = 0;
				while ($application = each($availableApps)) 
				{
					if ($application[1]['enabled'] && $application[1]['status'] != 2) 
					{
						$userData['permissions'][$i]['name'] = lang($application[1]['name']);
						if(in_array($application[1]['name'], $perms))
						{
							$userData['permissions'][$i]['enabled'] = true;
						}
					}
					$i++;
				}
			}

			// Labels
			$userData['l_action']		= lang('View user account');
			$userData['l_loginid']		= lang('LoginID');
			$userData['l_status']		= lang('Account status');
			$userData['l_password']		= lang('Password');
			$userData['l_lastname']		= lang('Last Name');
			$userData['l_groups']		= lang('Groups');
			$userData['l_firstname']	= lang('First Name');
			$userData['l_pwchange']		= lang('Last password change');
			$userData['l_lastlogin']	= lang('Last login');
			$userData['l_lastloginfrom']= lang('Last login from');
			$userData['l_expires']		= lang('Expires');
			$userData['l_back']			= lang('Back');
			$userData['l_user']			= lang('user');
			$userData['l_applications']	= lang('applications');

			// Interactions
			$userData['i_back']			= $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.list_users'));
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('account "%1" properties', $userData['account_lid']);
			$GLOBALS['phpgw']->xslttpl->add_file('users');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('account_view' => $userData));
		}

		function delete_group()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::groups';

			$account_id = phpgw::get_var('account_id', 'int');

			if ( phpgw::get_var('cancel', 'bool', 'POST') || $GLOBALS['phpgw']->acl->check('group_access',32,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiaccounts.list_groups'));
			}

			if ($account_id && phpgw::get_var('delete', 'bool', 'POST') )
			{
				$this->bo->delete_group($account_id);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiaccounts.list_groups'));
			}

			$GLOBALS['phpgw']->xslttpl->set_root(PHPGW_APP_TPL);			
			$GLOBALS['phpgw']->xslttpl->add_file('app_delete');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('administration') . ': ' . lang('delete group');

			$data = array
			(
				'delete_url'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.delete_group', 'account_id' => $account_id)),
				'lang_delete'				=> lang('delete'),
				'lang_cancel'				=> lang('cancel'),
				'lang_delete_statustext'	=> lang('delete the group'),
				'lang_cancel_statustext'	=> lang('Leave the group untouched and return back to the list'),
				'lang_delete_msg'			=> lang('are you sure you want to delete this group ?')
			);

			$old_group_list = $GLOBALS['phpgw']->acl->get_ids_for_location(intval($account_id),1,'phpgw_group');

			if($old_group_list)
			{
				$group_name = $GLOBALS['phpgw']->accounts->id2name($account_id);

				$user_list = '';
				while (list(,$id) = each($old_group_list))
				{
					$data['user_list'][] = array
					(
						'user_url'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.edit_user', 'account_id' => $id)),
						'user_name'					=> $GLOBALS['phpgw']->common->grab_owner_name($id),
						'lang_user_url_statustext'	=> lang('edit user')
					);
				}

				$data['lang_confirm_msg']			= lang('the users bellow are still members of group %1',$group_name) . '. '
													. lang('they must be removed before you can continue');
				$data['lang_remove_user']			= lang('Remove all users from this group ?');
			}

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function delete_user()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::users';

			if ( $GLOBALS['phpgw']->acl->check('account_access',32,'admin') 
				|| $GLOBALS['phpgw_info']['user']['account_id'] == phpgw::get_var('account_id', 'int', 'GET') )
			{
				$this->list_users();
				return False;
			}

			if ( phpgw::get_var('deleteAccount', 'bool') )
			{
				if ( !$this->bo->delete_user(phpgw::get_var('account_id', 'int'), phpgw::get_var('account', 'int') ) )
				{
					//TODO Make this nicer
					echo 'Failed to delete user';
				}
			}
			if( phpgw::get_var('cancel', 'bool') )
			{
				$this->list_users();
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('administration');
				
				//Add list entry to delete all references to this account (user)
				$alist = array();
				$alist[0] = array
				(
					'account_id'   => '0',
					'account_name' => lang('Delete all entries')
				);

				// get account list for new owner
				$accounts = CreateObject('phpgwapi.accounts');
				$accounts_list = $accounts->get_list('accounts');

				$account_id = phpgw::get_var('account_id', 'int');
				foreach ( $accounts_list as $account )
				{
					if((int)$account['account_id'] != $account_id )
					{
						$alist[] = array
						(
						  'account_id'   => $account['account_id'],
						  'account_name' => $accounts->id2name($account['account_id'])
						);
					}
				}
				unset($accounts);
				unset($accounts_list);
				$data = array
				(
					'account_id'		=> $account_id,
					'accountlist'		=> $alist,
					'form_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.delete_user') ),
					'lang_new_owner'	=> lang('Who would you like to transfer ALL records owned by the deleted user to?'),
					'l_cancel'			=> lang('cancel'),
					'l_delete'			=> lang('delete')
				);
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('new_owner_list' => $data));
			}
		}

		function group_manager($cd='',$account_id='')
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::users';

			if ($GLOBALS['phpgw']->acl->check('group_access',16,'admin'))
			{
				$this->list_groups();
				return False;
			}

			$cdid = (int) $cd;
			$cd = phpgw::get_var('cd', 'int', 'GET', $cdid);

			$accountid = (int) $account_id;
			$account_id = phpgw::get_var('account_id', 'int', 'GET', $accountid);
			
			// todo
			// not needed if i use the same file for new groups too
			if (! $account_id)
			{
				$this->list_groups();
			}
			else
			{
				$group_info = Array(
					'account_id'   => $account_id,
					'account_name' => $GLOBALS['phpgw']->accounts->id2name($account_id),
					'account_user' => $GLOBALS['phpgw']->accounts->member($account_id),
					'account_managers' => $this->bo->load_group_managers($account_id)
				);

				$this->edit_group_managers($group_info);
			}
		}

		function edit_group_managers($group_info,$_errors='')
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::users';

			if ($GLOBALS['phpgw']->acl->check('group_access',16,'admin'))
			{
				$this->list_groups();
				return False;
			}

			$accounts = createObject('phpgwapi.accounts',$group_info['account_id'],'u');
			$account_list = $accounts->member($group_info['account_id']);
			$user_list = '';
			while (list($key,$entry) = each($account_list))
			{
				$user_list .= '<option value="' . $entry['account_id'] . '"'
					. $group_info['account_managers'][intval($entry['account_id'])] . '>'
					. $GLOBALS['phpgw']->common->grab_owner_name($entry['account_id'])
					. '</option>'."\n";
			}

			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw']->common->phpgw_header();

			$t = createObject('phpgwapi.Template',PHPGW_APP_TPL);
			$t->set_unknowns('remove');

			$t->set_file(
				Array(
					'manager'	=>'group_manager.tpl'
				)
			);

			$t->set_block('manager','form','form');
			$t->set_block('manager','link_row','link_row');

			$var['th_bg'] = $GLOBALS['phpgw_info']['user']['theme']['th_bg'];
			$var['lang_group'] = lang('Group');
			$var['group_name'] = $group_info['account_name'];
			$var['tr_color1'] = $GLOBALS['phpgw_info']['user']['theme']['row_on'];
			$var['form_action'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.boaccounts.set_group_managers'));
			$var['hidden'] = '<input type="hidden" name="account_id" value="'.$group_info['account_id'].'">';
			$var['lang_select_managers'] = lang('Select Group Managers');
			$var['group_members'] = '<select name="managers[]" size="'.(count($account_list)<5?count($account_list):5).'" multiple>'.$user_list.'</select>';
			$var['form_buttons'] = '<tr align="center"><td colspan="2"><input type="submit" name="submit" value="'.lang('Submit').'">&nbsp;&nbsp;'
				. '<input type="submit" name="cancel" value="'.lang('Cancel').'"><td></tr>';
			$t->set_var($var);

			// create the menu on the left, if needed
			$t->set_var('rows',ExecMethod('admin.uimenuclass.createHTMLCode','edit_group'));

			$t->pfp('out','form');
		}

	}
?>
