<?php
	/**
	* phpGroupWare Administration - Accounts User Interface
	*
	* @author coreteam <phpgroupware-developers@gnu.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/ GNU General Public License v2 or later
	* @package phpgroupware
	* @subpackage admin
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	phpgw::import_class('phpgwapi.yui');

	/**
	* phpGroupWare Administration - Accounts User Interface
	*
	* @author coreteam <phpgroupware-developers@gnu.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/ GNU General Public License v2 or later
	* @package phpgroupware
	* @subpackage admin
	* @category accounts
	*/
	class admin_uiaccounts
	{
		/**
		 * @var array $public_functions Publicly available methods
		 */
		public $public_functions = array
		(
			'list_groups'				=> true,
			'list_users'				=> true,
			'delete_group'				=> true,
			'delete_user'				=> true,
			'edit_user'					=> true,
			'edit_group'				=> true,
			'view_user'					=> true,
			'sync_accounts_contacts'	=> true
		);

		/**
		 * @var object $_bo the logic object
		 */
		protected $_bo;

		/**
		 * @var object $_nextmatches pager object
		 */
		protected $_nextmatches;

		/**
		 * @var boolean $_ldap_extended Use LDAP extended attributes
		 */
		protected $_ldap_extended = false;

		/**
		 * Constructor
		 *
		 * @return null
		 */
		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::admin';

			$this->_bo = createObject('admin.boaccounts');
			$this->_nextmatches =createObject('phpgwapi.nextmatchs');

			$this->_ldap_extended = $GLOBALS['phpgw_info']['server']['account_repository'] == 'ldap'
				&& isset($GLOBALS['phpgw_info']['server']['ldap_extra_attributes'])
				&& $GLOBALS['phpgw_info']['server']['ldap_extra_attributes'];
		}

		/**
		 * Render a list of groups
		 *
		 * @return null
		 */
		public function list_groups()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::groups';

			if ( phpgw::get_var('done', 'bool', 'POST')
				|| $GLOBALS['phpgw']->acl->check('group_access', PHPGW_ACL_READ, 'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',
						array('menuaction' => 'admin.uimainscreen.mainscreen'));
			}

			if ( phpgw::get_var('add', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',
						array('menuaction' => 'admin.uiaccounts.edit_group', 'account_id' => 0));
			}

			$start = phpgw::get_var('start', 'int');
			$order = phpgw::get_var('order', 'string', 'GET', 'account_lid');
			$sort = phpgw::get_var('sort', 'string', 'GET', 'ASC');
			$total = 0;
			$query = phpgw::get_var('query', 'string');
			$GLOBALS['cd'] = phpgw::get_var('cd', 'int', 'GET');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('administration')
															. ': ' . lang('list groups');
			$GLOBALS['phpgw']->xslttpl->add_file('groups');

			$account_info = $GLOBALS['phpgw']->accounts->get_list('groups', $start, $sort,
																	$order, $query, $total);

			$total = $GLOBALS['phpgw']->accounts->total;

			$link_data = array
			(
				'menuaction' => 'admin.uiaccounts.list_groups'
			);

			$group_header = array
			(
				'sort_name'				=> $this->_nextmatches->show_sort_order(array
											(
												'sort'	=> $sort,
												'var'	=> 'lid',
												'order'	=> $order,
												'extra'	=> $link_data
											)),
				'lang_name'				=> lang('name'),
				'lang_edit'				=> lang('edit'),
				'lang_delete'			=> lang('delete'),
				'lang_sort_statustext'	=> lang('sort the entries')
			);

			$edit_rights = $this->_bo->check_rights('edit');
			$lang_edit = lang('edit');
			if ( $edit_rights )
			{
				$edit_args = array
				(
					'menuaction' => 'admin.uiaccounts.edit_group',
					'account_id' => '#ID'
				);

				$url_edit = $GLOBALS['phpgw']->link('/index.php', $edit_args);
			}

			$delete_rights = $this->_bo->check_rights('delete');
			$lang_delete = lang('delete');
			if ( $delete_rights )
			{
				$del_args = array
				(
					'menuaction' => 'admin.uiaccounts.delete_group',
					'account_id' => '#ID'
				);

				$url_delete = $GLOBALS['phpgw']->link('/index.php', $del_args);
			}

			$edit_url = '';
			$delete_url = '';
			foreach ( $account_info as $account )
			{
				if ( $edit_rights )
				{
					$edit_url = preg_replace('/%23ID/', $account->id, $url_edit);
				}

				if ( $delete_rights )
				{
					$delete_url = preg_replace('/%23ID/', $account->id, $url_delete);
				}

				$group_data[] = Array
				(
					'edit_url'					=> $edit_url,
					'lang_edit'					=> $lang_edit,
					'group_name'				=> $account->lid ? $account->lid : '',
					'delete_url'				=> $delete_url,
					'lang_delete'				=> $lang_delete
				);
			}

			$group_add = array
			(
				'lang_add'			=> lang('add'),
				'add_url'			=> $GLOBALS['phpgw']->link('/index.php',
										array('menuaction' => 'admin.uiaccounts.edit_group')),
				'done_url'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'lang_done'			=> lang('done'),
				'add_access'		=> (int) $this->_bo->check_rights('add')
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
				'nm_data'		=> $this->_nextmatches->xslt_nm($nm),
				'search_data'	=> $this->_nextmatches->xslt_search(array('query' => $query, 'link_data' => $link_data)),
				'group_header'	=> $group_header,
				'group_data'	=> $group_data,
				'group_add'		=> $group_add,
				'search_access'	=> $this->_bo->check_rights('search')
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('group_list' => $data));
		}

		/**
		 * Render a list of user accounts
		 *
		 * @param integer $cd error number
		 *
		 * @return null
		 */
		public function list_users($cd = 0)
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::users';

			if ( phpgw::get_var('done', 'bool', 'POST')
				|| $GLOBALS['phpgw']->acl->check('account_access', phpgwapi_acl::READ, 'admin') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',
						array('menuaction' => 'admin.uimainscreen.mainscreen'));
			}

			if ( phpgw::get_var('add', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',
						array('menuaction' => 'admin.uiaccounts.edit_user'));
			}

			$query		= phpgw::get_var('query', 'string');
			$start		= phpgw::get_var('start', 'int', 'GET', -1);
			$order		= phpgw::get_var('order', 'string', 'GET', 'account_lid');
			$sort		= phpgw::get_var('sort', 'string', 'GET', 'ASC');
			$allrows	= phpgw::get_var('allrows', 'bool');

			//this is a work around hack for the ugly nextmatch code
			$GLOBALS['query'] = $query;

			$total = 0;
			if( $allrows )
			{
				$start = -1;
				$total = -1;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('administration') . ': ' . lang('list users');

			$GLOBALS['phpgw']->xslttpl->add_file('users');

			$account_info = $GLOBALS['phpgw']->accounts->get_list('accounts', $start, $sort, $order, $query, $total);
			$total = $GLOBALS['phpgw']->accounts->total;

			$link_data = array
			(
				'menuaction' => 'admin.uiaccounts.list_users'
			);

			$lang = array
			(
				'delete'	=> lang('delete'),
				'edit'		=> lang('edit'),
				'status'	=> lang('status'),
				'view'		=> lang('view')
			);

			$user_header = array
			(
				'sort_lid'				=> $this->_nextmatches->show_sort_order(array
											(
												'sort'	=> $sort,
												'var'	=> 'account_lid',
												'order'	=> $order,
												'extra'	=> $link_data
											)),
				'lang_lid'				=> lang('loginid'),
				'sort_lastname'			=> $this->_nextmatches->show_sort_order(array
											(
												'sort'	=> $sort,
												'var'	=> 'account_lastname',
												'order'	=> $order,
												'extra'	=> $link_data
											)),
				'lang_lastname'				=> lang('Lastname'),
				'sort_firstname'			=> $this->_nextmatches->show_sort_order(array
											(
												'sort'	=> $sort,
												'var'	=> 'account_firstname',
												'order'	=> $order,
												'extra'	=> $link_data
											)),
				'lang_firstname'		=> lang('firstname'),
				'sort_status'			=> $this->_nextmatches->show_sort_order(array
											(
												'sort'	=> $sort,
												'var'	=> 'account_status',
												'order'	=> $order,
												'extra'	=> $link_data
											)),
				'lang_status'			=> $lang['status'],
				'lang_view'				=> $lang['view'],
				'lang_edit'				=> $lang['edit'],
				'lang_delete'			=> $lang['delete']
			);

			$status_data = array
			(
				'img_disabled'	=> $GLOBALS['phpgw']->common->image('phpgwapi', 'disabled', '.png'),
				'img_enabled'	=> $GLOBALS['phpgw']->common->image('phpgwapi', 'enabled', '.png'),
				'lang_disabled'	=> lang('disabled'),
				'lang_enabled'	=> lang('enabled')
			);

			$lang_view	= '';
			$view_url	= '';
			$view_rights = $this->_bo->check_rights('view', 'account_access');
			if ( $view_rights )
			{
				$view_args = array
				(
					'menuaction' => 'admin.uiaccounts.view_user',
					'account_id' => '#ID'
				);
				$url_view	= $GLOBALS['phpgw']->link('/index.php', $view_args);
				$lang_view	= $lang['view'];
				unset($view_args);
			}

			$edit_url	= '';
			$lang_edit	= '';
			$edit_rights = $this->_bo->check_rights('edit', 'account_access');
			if ( $edit_rights )
			{
				$edit_args = array
				(
					'menuaction' => 'admin.uiaccounts.edit_user',
					'account_id' => '#ID'
				);
				$url_edit	= $GLOBALS['phpgw']->link('/index.php', $edit_args);
				$lang_edit	= $lang['edit'];
				unset($edit_args);
			}

			$delete_url		= '';
			$lang_delete	= '';
			$delete_rights	= $this->_bo->check_rights('delete', 'account_access');
			if ( $delete_rights )
			{
				$delete_args = array
				(
					'menuaction' => 'admin.uiaccounts.delete_user',
					'account_id' => '#ID'
				);
				$url_delete		= $GLOBALS['phpgw']->link('/index.php', $delete_args);
				$lang_delete	= $lang['delete'];
				unset($delete_args);
			}

			$user_data = array();
			foreach ( $account_info as $account )
			{
				if ( $view_rights )
				{
					$view_url = preg_replace('/%23ID/', $account->id, $url_view);
				}

				if ( $edit_rights )
				{
					$edit_url = preg_replace('/%23ID/', $account->id, $url_edit);
				}

				if ( $delete_rights )
				{
					$delete_url = preg_replace('/%23ID/', $account->id, $url_delete);
				}

				$user_data[] = Array
				(
					'lid'						=> $account->lid,
					'firstname'					=> $account->firstname,
					'lastname'					=> $account->lastname,

					'status'					=> $account->enabled,
					'status_img'				=> $account->enabled
													? $status_data['img_enabled']
													: $status_data['img_disabled'],
					'status_text'				=> $account->enabled
													? $status_data['lang_enabled']
													: $status_data['lang_disabled'],

					'view_url'					=> $view_url,
					'lang_view'					=> $lang_view,

					'edit_url'					=> $edit_url,
					'lang_edit'					=> $lang_edit,

					'delete_url'				=> $delete_url,
					'lang_delete'				=> $lang_delete
				);
			}

			$user_add = array
			(
				'add_access'	=> $this->_bo->check_rights('add', 'account_access'),
				'lang_add'		=> lang('add'),
				'lang_done'		=> lang('done'),
				'url_add'		=> $GLOBALS['phpgw']->link('/index.php',
										array('menuaction' => 'admin.uiaccounts.edit_user')),
				'url_done'		=> 	$GLOBALS['phpgw']->link('/index.php',
										array('menuaction' => 'admin.uimainscreen.mainscreen'))
			);

			$nm = array
			(
				'start'				=> $start,
 				'num_records'		=> count($account_info),
 				'all_records'		=> $total,
				'link_data'			=> $link_data,
				'allow_all_rows'	=> true,
				'allrows'			=> $allrows
			);

			$data = array
			(
				'nm_data'		=> $this->_nextmatches->xslt_nm($nm),
				'search_data'	=> $this->_nextmatches->xslt_search(array('query' => $query, 'link_data' => $link_data)),
				'user_header'	=> $user_header,
				'user_data'		=> $user_data,
				'user_add'		=> $user_add,
				'search_access'	=> $this->_bo->check_rights('search', 'account_access')
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('account_list' => $data));
		}

		/**
		 * Render form for editting a group account
		 *
		 * @return null
		 */
		public function edit_group()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::groups';

			$account_apps	= array();
			$account_id		= phpgw::get_var('account_id', 'int');
			$error_list		= array();

			if ( phpgw::get_var('cancel', 'bool', 'POST')
				|| ( !$account_id
					&& $GLOBALS['phpgw']->acl->check('group_access', phpgwapi_acl::EDIT, 'admin') )
				|| ( $account_id
					&& $GLOBALS['phpgw']->acl->check('group_access', phpgwapi_acl::PRIV, 'admin') ) )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',
						array('menuaction' => 'admin.uiaccounts.list_groups'));
			}

			if ( phpgw::get_var('save', 'bool', 'POST') )
			{
				$values			= phpgw::get_var('values', 'string', 'POST', array());
				$account_apps	= phpgw::get_var('account_apps', 'bool', 'POST');
				$account_user	= phpgw::get_var('account_user', 'int', 'POST');

				$values['account_user'] = array();
				if (is_array($account_user))
				{
					$values['account_user'] = $account_user;
				}

				$values['account_apps'] = array();
				if ( is_array($account_apps) && count($account_apps) )
				{
					$values['account_apps'] = $account_apps;
				}

				//FIXME exception/error handling needed here!
				$error = array();

				$account_id = $this->_bo->save_group($values);
				if ( $account_id )
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array
					(
						'menuaction' => 'admin.uiaccounts.edit_group',
						'account_id' => $account_id
					));
				}

				$error_list = array('msgbox_text' => $GLOBALS['phpgw']->common->error_list($error));
			}

			$js =& $GLOBALS['phpgw']->js;
			$js->validate_file('base', 'groups', 'admin');

			$accounts =& $GLOBALS['phpgw']->accounts;

			$group = $accounts->get($account_id);
			if ( !$account_id && !is_object($group) )
			{
				$group = new phpgwapi_group();
			}

			$group_members = $accounts->member($account_id);

			$account_list = $accounts->get_list('accounts');

			$members = array();
			$user_list = array();
			foreach ( $account_list as $id => $user )
			{
				$user_list[$id] = array
				(
					'account_id'	=> $id,
					'account_name'	=> (string) $user,
 					'selected'		=> (int) isset($group_members[$id])
				);
			}

			//FIXME this needs to be provided by the app itself - thats why we have hooks
			$apps_with_acl = array
			(
				'addressbook'	=> array('top_grant' => true),
				'bookmarks'		=> array('top_grant' => true),
				'calendar'		=> array('top_grant' => true),
				'filemanager'	=> array('top_grant' => true),
				'hrm'			=> array('top_grant' => true),
				'infolog'		=> array('top_grant' => true),
				'notes'			=> array('top_grant' => true),
				'projects'		=> array('top_grant' => true),
				'property'		=> array('top_grant' => true),
				'sms'			=> array('top_grant' => true),
				'todo'			=> array('top_grant' => true),
				'tts'			=> array('top_grant' => true),
			);
			$GLOBALS['phpgw']->locations->verify($apps_with_acl);

			$group_apps = $this->_bo->load_apps($account_id);
			$apps = array_keys($GLOBALS['phpgw_info']['apps']);
			asort($apps);

			$img_acl = $GLOBALS['phpgw']->common->image('admin', 'share', '.png', false);
			$img_acl_grey = $GLOBALS['phpgw']->common->image('admin', 'share-grey', '.png', false);
			$lang_acl = lang('Set general permissions');
			$img_grants = $GLOBALS['phpgw']->common->image('admin', 'dot', '.png', false);
			$img_grants_grey = $GLOBALS['phpgw']->common->image('admin', 'dot-grey', '.png', false);
			$lang_grants = lang('grant access');

			$url_acl = $GLOBALS['phpgw']->link('/index.php', array
			(
				'menuaction'		=> 'preferences.uiadmin_acl.list_acl',
				'acl_app'			=> '##APP##',
				'cat_id'			=> 'groups',
				'module'			=> '.'
			));

			$url_grant = $GLOBALS['phpgw']->link('/index.php', array
			(
				'menuaction'		=> 'preferences.uiadmin_acl.aclprefs',
				'acl_app'			=> '##APP##',
				'cat_id'			=> 'groups',
				'module'			=> '.',
				'granting_group'	=> $account_id
			));

			foreach ( $apps as $app )
			{

				if ($GLOBALS['phpgw_info']['apps'][$app]['enabled']
						&& $GLOBALS['phpgw_info']['apps'][$app]['status'] != 3)
				{
					$grants_enabled = isset($apps_with_acl[$app]) && $account_id;

					$acl_url = '';
					$grant_url = '';
					if ( $grants_enabled )
					{
						$acl_url = preg_replace('/%23%23APP%23%23/', $app, $url_acl);
						$grant_url = preg_replace('/%23%23APP%23%23/', $app, $url_grant);
					}

					$app_list[] = array
					(
						'elmid'			=> "admin_applist_$app",
						'app_name'		=> $app,
						'app_title'		=> lang($app),
						'checkbox_name'	=> "account_apps[{$app}]",
						'checked'       => isset($group_apps[$app]),
						'acl_url'       => $acl_url,
						'acl_img'		=> $grants_enabled ? $img_grants : $img_grants_grey,
						'acl_img_name'	=> $lang_acl,
						'grant_img'		=> $grants_enabled ? $img_acl : $img_acl_grey,
						'grant_img_name'=> $lang_grants,
						'grant_url'		=> $grant_url
					);
				}
			}

			$tabs = array
			(
				'data'	=> array('label' => lang('group data'), 'link' => '#group'),
				'apps'	=> array('label' => lang('applications'), 'link' => '#apps')
			);
			phpgwapi_yui::tabview_setup('group_edit_tabview');

			// this is in the api, so lets not waste loops looking for it the app tpl dirs
			$GLOBALS['phpgw']->xslttpl->add_file('msgbox', PHPGW_TEMPLATE_DIR, 3);
			$GLOBALS['phpgw']->xslttpl->add_file('groups');

			$GLOBALS['phpgw_info']['flags']['app_header'] =  lang('edit group');
			if ( $account_id )
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('add group');
			}

			$data = array
			(
				'page_title'		=> $account_id ? lang('edit group') : lang('add group'),
				'account_id'		=> $account_id,
				'app_list'			=> $app_list,
				'edit_url'			=> $GLOBALS['phpgw']->link('/index.php', array
										(
											'menuaction' => 'admin.uiaccounts.edit_group',
											'account_id' => $account_id
										)),
				'guser_list'		=> $user_list,
				'img_close'			=> $GLOBALS['phpgw']->common->image('phpgwapi', 'stock_close', '.png', false),
				'img_save'			=> $GLOBALS['phpgw']->common->image('phpgwapi', 'stock_save', '.png', false),
				'lang_account_name'	=> lang('group name'),
				'lang_acl'			=> lang('acl'),
				'lang_application'	=> lang('application'),
				'lang_cancel'		=> lang('cancel'),
				'lang_close'		=> lang('close'),
				'lang_grant'		=> lang('grant'),
				'lang_include_user'	=> lang('members'),
				'lang_permissions'	=> lang('applications'),
				'lang_save'			=> lang('save'),
				'msgbox_data'		=> $error_list,
				'select_size'		=> 5,
				'value_account_name'=> $group->lid,
				'tabs'				=> phpgwapi_yui::tabview_generate($tabs, 'data')
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('group_edit' => $data));
		}

		/**
		 * Render a form for editing a user account
		 *
		 * @return bool was the account edited?
		 */
		public function edit_user()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::users';

			if ( phpgw::get_var('save', 'bool', 'POST') )
			{
				$this->_user_save();
				return true;
			}

			if ( phpgw::get_var('cancel', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',
						array('menuaction' => 'admin.uiaccounts.list_users'));
				return true;
			}

			$account_id = phpgw::get_var('account_id', 'int');
			if ( $account_id )
			{
				$user = $GLOBALS['phpgw']->accounts->get($account_id);
			}
			else
			{
				$user = new phpgwapi_user();
			}

			$this->_user_form($user);
		}

		/**
		 * Save a user account
		 *
		 * @return null
		 */
		public function _user_save()
		{
			$values							= phpgw::get_var('values', 'string', 'POST');
			$values['account_groups']		= (array) phpgw::get_var('account_groups', 'int', 'POST');
			$values['account_permissions']	= phpgw::get_var('account_permissions', 'bool', 'POST');
			//FIXME Caeies fix waiting for JSCAL
			$values['account_expires_year']	= phpgw::get_var('account_expires_year', 'int', 'POST');
			// we use string here to allow for MMM formatted months
			$values['account_expires_month']= phpgw::get_var('account_expires_month', 'string', 'POST');
			$values['account_expires_day']	= phpgw::get_var('account_expires_day', 'int', 'POST');

			$errors = array();
			try
			{
				$account_id = $this->_bo->save_user($values);
			}
			catch ( Exception $e )
			{
				$ignored = array
				(
					'passwd',
					'passwd_2',
					'changepassword',
					'expires_never',
					'account_expires',
					'account_expires_year',
					'account_expires_month',
					'account_expires_day',
					'account_permissions',
					'account_groups'
				);

				$errors[] = $e->getMessage();
				$user = new phpgwapi_user;
				foreach ( $values as $key => $value )
				{
					if ( in_array($key, $ignored) )
					{
						continue;
					}

					try
					{
						$user->$key = $value;
					}
					catch ( Exception $e )
					{
						$errors[] = $e->getMessage();
					}
				}
				$this->_user_form($user, $errors);
				return;
			}
			$GLOBALS['phpgw']->redirect_link('/index.php',
					array('menuaction' => 'admin.uiaccounts.edit_user', 'account_id' => $account_id));
		}

		/**
		 * Generate a user edit form
		 *
		 * @param object $user   the user account object to be edited
		 * @param array  $errors any error messages that should be shown to the user
		 *
		 * @return null
		 */
		protected function _user_form($user, $errors = array())
		{
			$account_id = $user->id;
			$user_data = $user->toArray();
			$account = createObject('phpgwapi.accounts', $account_id, 'u');

			$sbox = CreateObject('phpgwapi.sbox');

			$GLOBALS['phpgw']->xslttpl->add_file('users');
			// no point in wasting loops
			$GLOBALS['phpgw']->xslttpl->add_file('msgbox', PHPGW_TEMPLATE_DIR, 3);

			$acl = createObject('phpgwapi.acl', $account_id);
			
			$user_data['status'] = 'A';
			$user_data['anonymous'] = false;
			$user_data['changepassword'] = true;
			$user_data['account_permissions'] = array();

			$user_groups = array();

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('administration') . ': ';
			if ( $account_id )
			{
				$user_data['anonymous'] = $acl->check('anonymous', 1, 'phpgwapi');
				$user_data['changepassword'] = $acl->check('changepassword', 1, 'preferences');
				$user_data['account_permissions'] = $this->_bo->load_apps($account_id);
				$user_groups = $account->membership($account_id);

				$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('edit user account');
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] .= lang('add user account');
			}

			if ( !$user_data['expires'] )
			{
				// we assume this is a sane value
				$user_data['expires'] = time() + $GLOBALS['phpgw_info']['server']['auto_create_expire'];
			}

			if ($user_data['expires'] == -1) //switch to js cal - skwashd
			{
				$user_data['account_expires_month'] = 0;
				$user_data['account_expires_day']   = 0;
				$user_data['account_expires_year']  = 0;
			}
			else
			{
				$user_data['account_expires_month'] = date('m', $user_data['expires']);
				$user_data['account_expires_day']   = date('d', $user_data['expires']);
				$user_data['account_expires_year']  = date('Y', $user_data['expires']);
			}

			$homedirectory = '';
			$loginshell = '';
			$lang_homedir  = '';
			$lang_shell = '';
			if ( $this->_ldap_extended )
			{
				$server =& $GLOBALS['phpgw_info']['server'];
				if ( !$account_id )
				{
					$user_data['homedirectory'] = "{$server['ldap_account_home']}/{$account_lid}";
					$user_data['loginshell'] = $server['ldap_account_shell'];
				}

				$lang_homedir	= lang('home directory');
				$lang_shell		= lang('login shell');
				$homedirectory = "<input name=\"homedirectory\" value=\"{$user_data['homedirectory']}\">";
				$loginshell = "<input name=\"loginshell\" value=\"{$user_data['loginshell']}\">";
			}

			$add_masters	= $GLOBALS['phpgw']->acl->get_ids_for_location('addressmaster', 7, 'addressbook');
			$add_users		= $GLOBALS['phpgw']->accounts->return_members($add_masters);
			$masters		= $add_users['users'];

			if ( is_array($masters)
				&& in_array($GLOBALS['phpgw_info']['user']['account_id'], $masters) )
			{
				if ( $user_data['person_id'] )
				{
					$url_contacts_text = lang('Edit entry');
					$url_contacts =   $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'    => 'addressbook.uiaddressbook.edit_person',
						'ab_id'         => $user_data['person_id'],
						'referer'       => $GLOBALS['phpgw']->link('/index.php', array
											(
												'menuaction' => 'admin.uiaccounts.edit_user',
												'account_id' =>  $account_id
											))
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
					'referer'       => $GLOBALS['phpgw']->link('/index.php', array
										(
											'menuaction' => 'admin.uiaccounts.edit_user',
											'account_id' =>  $account_id
										))
				));
			}

			$_y = $sbox->getyears('account_expires_year',
									$user_data['account_expires_year'], date('Y'), date('Y')+10);

			$_m = $sbox->getmonthtext('account_expires_month', $user_data['account_expires_month']);
			$_d = $sbox->getdays('account_expires_day', $user_data['account_expires_day']);

			$group_list = array();

			$all_groups = $account->get_list('groups');
			foreach ( $all_groups as $group )
			{
				$group_list[$group->id] = array
				(
					'account_id'	=> $group->id,
					'account_lid'	=> (string) $group
				);
			}

			$group_ids = array_keys($group_list);
			foreach ( $user_groups as $group )
			{
				$group_list[$group->id]['selected'] = in_array($group->id, $group_ids);
			}
			unset($group_ids);

			/* create list of available apps */
			$apps = createObject('phpgwapi.applications', $account_id ? $account_id : -1);
			$db_perms = $apps->read_account_specific();

			$available_apps = $GLOBALS['phpgw_info']['apps'];
			asort($available_apps);
			foreach ( $available_apps as $key => $application )
			{
				if ($application['enabled'] && $application['status'] != 3)
				{
					$perm_display[] = array
					(
						'app_name'			=> $key,
						'translated_name'	=> lang($key)
					);
				}
			}
			asort($perm_display);

			foreach ( $perm_display as $perm )
			{
				$checked = false;
				if ( ( isset($user_data['account_permissions'][$perm['app_name']])
						&& $user_data['account_permissions'][$perm['app_name']] )
					|| ( isset($db_perms[$perm['app_name']]) && $db_perms[$perm['app_name']] ) )
				{
					$checked = true;
				}

				$app_list[] = array
				(
					'app_title'		=> $perm['translated_name'],
					'checkbox_name'	=> "account_permissions[{$perm['app_name']}]",
					'checked'		=> $checked
				);
			}

			$tabs = array
			(
				'data'	=> array('label' => lang('user data'), 'link' => '#user'),
				'groups'	=> array('label' => lang('groups'), 'link' => '#groups'),
				'apps'	=> array('label' => lang('applications'), 'link' => '#apps')
			);
			phpgwapi_yui::tabview_setup('account_edit_tabview');


			$data = array
			(
				'page_title'			=> $account_id ? lang('edit user') : lang('add user'),
				'msgbox_data'			=> array('msgbox_text' => $GLOBALS['phpgw']->common->error_list($errors)),
				'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',
												array('menuaction' => 'admin.uiaccounts.edit_user')),
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
				'select_expires'		=> $GLOBALS['phpgw']->common->dateformatorder($_y, $_m, $_d, true),
				'lang_never'			=> lang('Never'),
				'account_id'			=> $account_id,
				'account_lid'			=> $user_data['lid'],
				'lang_homedir'			=> $lang_homedir,
				'lang_shell'			=> $lang_shell,
				'homedirectory'			=> $homedirectory,
				'loginshell'			=> $loginshell,
				'account_enabled'		=> (int) $user_data['enabled'],
				'account_firstname'		=> $user_data['firstname'],
				'account_lastname'		=> $user_data['lastname'],
				'account_passwd'		=> '',
				'account_passwd_2'		=> '',
				'account_quota'			=> $user_data['quota'],
				'anonymous'				=> (int) $user_data['anonymous'],
				'changepassword'		=> (int) $user_data['changepassword'],
				'expires_never'			=> $user_data['expires'] == -1,
				'group_list'			=> $group_list,
				'app_list'				=> $app_list,
				'url_contacts'			=> $url_contacts,
				'url_contacts_text'		=> $url_contacts_text,
				'tabs'					=> phpgwapi_yui::tabview_generate($tabs, 'data')
			);

			/*
				create the menu on the left, if needed
				$menuClass = CreateObject('admin.uimenuclass');
				This is now using ExecMethod()
				$t->set_var('rows',ExecMethod('admin.uimenuclass.createHTMLCode','edit_user'));
			*/
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('account_edit' => $data));
		}

		/**
		 * Render a form for viewing a user account
		 *
		 * @return null
		 */
		public function view_user()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::users';

			$account_id = phpgw::get_var('account_id', 'int', 'GET');
			if ( $GLOBALS['phpgw']->acl->check('account_access', phpgwapi_acl::DELETE, 'admin')
					|| !$account_id )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',
						array('menuaction' => 'admin.uiaccounts.list_users'));
			}

			$user_data = $GLOBALS['phpgw']->accounts->get($account_id)->toArray();

			$lang_never = lang('never');
			$lang_disabled = lang('disabled');
			$lang_enabled = lang('enabled');

			if ($user_data['enabled'])
			{
				$user_data['account_status'] = $lang_enabled;
			}
			else
			{
				$user_data['account_status'] = $lang_disabled;
			}

			$user_data['account_lastlogin'] = $lang_never;
			if ( $user_data['last_login'] )
			{
				$user_data['account_lastlogin'] = lang('%1 from %2',
						$GLOBALS['phpgw']->common->show_date($user_data['last_login']), $user_data['last_login_from']);
			}

			$user['account_lastpasswd_change'] = $lang_never;
			if ( $user_data['last_passwd_change'] )
			{
				$user['account_lastpasswd_change'] = $GLOBALS['phpgw']->common->show_date($user_data['last_passwd_change']);
			}

			$user_data['input_expires'] = $lang_never;
			if ( $user_data['expires'] <> -1 )
			{
				$user_data['input_expires'] = $GLOBALS['phpgw']->common->show_date($user_data['expires']);
			}

			// Find out which groups they are members of
			$usergroups = $GLOBALS['phpgw']->accounts->membership($account_id);
			foreach ( $usergroups as $group )
			{
				$user_data['groups'][$group->id] = (string) $group;
			}

			//Permissions
			$available_apps = $GLOBALS['phpgw_info']['apps'];
			$apps  = CreateObject('phpgwapi.applications', $account_id);
			$perms = array_keys($apps->read_account_specific());
			if ( is_array($available_apps) && count($available_apps) )
			{
				$img_disabled = $GLOBALS['phpgw']->common->image('phpgwapi', 'stock_no', '.png', false);
				$img_enabled = $GLOBALS['phpgw']->common->image('phpgwapi', 'stock_yes', '.png', false);

				sort($available_apps);
				foreach ( $available_apps as $app )
				{
					if ( !$app['enabled'] || $app['status'] > 2 )
					{
						continue;
					}
					$enabled = in_array($app['name'], $perms);
					$user_data['permissions'][] = array
					(
						'name'	=> lang($app['name']),
						'img'	=> $enabled ? $img_enabled : $img_disabled,
						'alt'	=> $enabled ? $lang_enabled : $lang_disabled
					);
				}
			}

			// Labels
			$user_data['l_action']			= lang('View user account');
			$user_data['l_loginid']			= lang('LoginID');
			$user_data['l_status']			= lang('Account status');
			$user_data['l_password']		= lang('Password');
			$user_data['l_lastname']		= lang('Last Name');
			$user_data['l_groups']			= lang('Groups');
			$user_data['l_firstname']		= lang('First Name');
			$user_data['l_pwchange']		= lang('Last password change');
			$user_data['l_lastlogin']		= lang('Last login');
			$user_data['l_expires']			= lang('Expires');
			$user_data['l_back']			= lang('Back');
			$user_data['l_user']			= lang('user');
			$user_data['l_applications']	= lang('applications');

			// Interactions
			$user_data['i_back']			= $GLOBALS['phpgw']->link('/index.php',
												array('menuaction' => 'admin.uiaccounts.list_users'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('account "%1" properties', $user_data['lid']);
			$GLOBALS['phpgw']->xslttpl->add_file('users');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('account_view' => $user_data));
		}

		/**
		 * Render a confirmation form for deleting a group or deletes the group
		 *
		 * @return null
		 */
		public function delete_group()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::groups';

			$account_id = phpgw::get_var('account_id', 'int');

			if ( phpgw::get_var('cancel', 'bool', 'POST')
				|| $GLOBALS['phpgw']->acl->check('group_access', phpgwapi_acl::GROUP_MANAGERS, 'admin') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',
						array('menuaction' => 'admin.uiaccounts.list_groups'));
			}

			$group = $GLOBALS['phpgw']->accounts->get($account_id);
			if ( !is_object($group) )
			{
				// FIXME add proper error handling here
				die('Invalid Group');
			}

			if ($account_id && phpgw::get_var('confirm', 'bool', 'POST') )
			{
				$this->_bo->delete_group($account_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',
						array('menuaction' => 'admin.uiaccounts.list_groups'));
			}

			$GLOBALS['phpgw']->xslttpl->set_root(PHPGW_APP_TPL);
			$GLOBALS['phpgw']->xslttpl->add_file('confirm_delete');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('administration') . ': ' . lang('delete group');

			$data = array
			(
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php', array
												(
													'menuaction' => 'admin.uiaccounts.delete_group',
													'account_id' => $account_id
												)),
				'lang_yes'					=> lang('yes'),
				'lang_no'					=> lang('no'),
				'lang_confirm_msg'			=> lang('are you sure you want to delete group "%1"?', $group->lid)
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}

		/**
		 * Render a confirmation form for deleting a user or deletes the user account
		 *
		 * @return bool was the account deleted?
		 */
		public function delete_user()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::users';

			if ( $GLOBALS['phpgw']->acl->check('account_access', phpgwapi_acl::GROUP_MANAGERS, 'admin')
				|| $GLOBALS['phpgw_info']['user']['account_id'] == phpgw::get_var('account_id', 'int', 'GET') )
			{
				$this->list_users();
				return false;
			}

			if ( phpgw::get_var('deleteAccount', 'bool') )
			{
				if ( !$this->_bo->delete_user(phpgw::get_var('account_id', 'int'), phpgw::get_var('account', 'int')) )
				{
					//TODO Make this nicer
					echo 'Failed to delete user';
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',
						array('menuaction' => 'admin.uiaccounts.list_users'));
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
					if ( (int) $account->id != $account_id )
					{
						$alist[] = array
						(
						  'account_id'   => $account->id,
						  'account_name' => $accounts->id2name($account->id)
						);
					}
				}
				unset($accounts);
				unset($accounts_list);

				$lang_newowner = lang('Who would you like to transfer ALL records owned by the deleted user to?');
				$data = array
				(
					'account_id'		=> $account_id,
					'accountlist'		=> $alist,
					'form_action'		=> $GLOBALS['phpgw']->link('/index.php',
											array('menuaction' => 'admin.uiaccounts.delete_user')),
					'lang_new_owner'	=> $new_owner,
					'l_cancel'			=> lang('cancel'),
					'l_delete'			=> lang('delete')
				);
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('new_owner_list' => $data));
			}
		}

		/**
		 * Create a link for handling various account actions
		 *
		 * @param string  $action     the action to be performed
		 * @param string  $type       the type of account to use
		 * @param integer $account_id the account to be used
		 *
		 * @return string HTML href fragment with label
		 *
		 * @internal this seems to be unsused
		 */
		protected function _row_action($action, $type, $account_id)
		{
			$lang_action = lang($action);
			$url = $GLOBALS['phpgw']->link('/index.php', array
			(
				'menuaction' => "admin.uiaccounts.{$action}_{$type}",
				'account_id' => $account_id
			));

			return "<a href=\"{$url}\">{$lang_action}</a>";
		}

		/**
		* Generates contacts from users
		*
		* @return void
		*/

		function sync_accounts_contacts()
		{
			$GLOBALS['phpgw']->accounts->sync_accounts_contacts();
			$GLOBALS['phpgw']->redirect_link('/admin/index.php');
		}

	}
