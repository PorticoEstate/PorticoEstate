<?php
	/**
	 * phpGroupWare - Administration - ACL manager logic
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Others <unknown>
	 * @copyright Copyright (C) 2007-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
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

	/*
	 * phpGroupWare - Administration - ACL manager logic
	 *
	 * @package phpgroupware
	 * @subpackage admin
	 *
	 * @internal FIXME this is shitty insecure code - someone needs to fix this
	 */

	class admin_uiaclmanager
	{
		/**
		 * @var object $_template reference to global template object
		 */
		protected $_template;

		/**
		 *@var object $_boacl business logic
		 */
		protected $_boacl;

		/**
		 * @var array $public_functions publicly available methods class
		 */
		public $public_functions = array
		(
			'list_apps'				=> true,
			'access_form'			=> true,
			'account_list'			=> true,
			'list_addressmasters'	=> true,
			'edit_addressmasters'	=> true,
			'accounts_popup'		=> true,
		);

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->account_id = phpgw::get_var('account_id', 'int', 'GET',
								$GLOBALS['phpgw_info']['user']['account_id']);

			if ( !$this->account_id
				|| $GLOBALS['phpgw']->acl->check('account_access', 64, 'admin') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}

			$this->_template	=& $GLOBALS['phpgw']->template;
			$this->_boacl		= CreateObject('admin.boaclmanager');
		}

		/**
		 * Prepare the common template header
		 *
		 * @return void
		 */
		protected function _common_header()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			$this->_template->set_root(PHPGW_APP_TPL);
		}

		/**
		 * This does something - ask ceb and jengo what - they wrote it
		 *
		 * @return void
		 */
		public function list_apps()
		{
			$this->_common_header();

			$GLOBALS['phpgw']->hooks->process('acl_manager', array('preferences'));

			$this->_template->set_file('app_list', 'acl_applist.tpl');
			$this->_template->set_block('app_list', 'list');
			$this->_template->set_block('app_list', 'app_row');
			$this->_template->set_block('app_list', 'app_row_noicon');
			$this->_template->set_block('app_list', 'link_row');
			$this->_template->set_block('app_list', 'spacer_row');

			$this->_template->set_var('lang_header', lang('ACL Manager'));

			while (is_array($GLOBALS['acl_manager']) && list($app, $locations) = each($GLOBALS['acl_manager']))
			{
				$icon = $GLOBALS['phpgw']->common->image($app, array('navbar.gif', $app.'.gif'));
				$this->_template->set_var('icon_backcolor', $GLOBALS['phpgw_info']['theme']['row_off']);
				$this->_template->set_var('link_backcolor', $GLOBALS['phpgw_info']['theme']['row_off']);
				$this->_template->set_var('app_name', lang($GLOBALS['phpgw_info']['navbar'][$app]['title']));
				$this->_template->set_var('a_name', $appname);
				$this->_template->set_var('app_icon', $icon);

				if ($icon)
				{
					$this->_template->fp('rows', 'app_row', true);
				}
				else
				{
					$this->_template->fp('rows', 'app_row_noicon', true);
				}

				while (is_array($locations) && list($loc, $value) = each($locations))
				{
					$total_rights = 0;
					while (list($k, $v) = each($value['rights']))
					{
						$total_rights += $v;
					}
					reset($value['rights']);

					// If all of there rights are denied, then they shouldn't even see the option
					if ($total_rights != $GLOBALS['phpgw']->acl->get_rights($loc, $app))
					{
						$link_values = array
						(
							'menuaction' => 'admin.uiaclmanager.access_form',
							'location'   => $loc,
							'acl_app'    => $app,
							'account_id' => $GLOBALS['account_id']
						);

						$this->_template->set_var('link_location', $GLOBALS['phpgw']->link('/index.php', $link_values));
						$this->_template->set_var('lang_location', lang($value['name']));
						$this->_template->fp('rows', 'link_row', true);
					}
				}

				$this->_template->parse('rows', 'spacer_row', true);
			}
			$this->_template->pfp('out', 'list');
		}

		/**
		 * This does something - don't ask me what, but I suspect it involves rendering a form
		 *
		 * @return void
		 */
		public function access_form()
		{
			$acl_app	= phpgw::get_var('acl_app');
			$location	= phpgw::get_var('location');
			$account_id	= phpgw::get_var('account_id', 'int');
			$acl_man	= phpgw::get_var('acl_manager');

			$acl_manager = $acl_man[$acl_app][$location];

			$GLOBALS['phpgw']->hooks->single('acl_manager', $acl_app);


			$this->_common_header();
			$this->_template->set_file('form', 'acl_manager_form.tpl');

			$acc = createobject('phpgwapi.accounts', $account_id);
			$afn = (string) $GLOBALS['phpgw']->accounts->get($account_id);

			$msg = lang('Check items to <b>%1</b> to %2 for %3', $acl_manager['name'], $acl_app, $afn);
			$this->_template->set_var('lang_message', $msg);
			$link_values = array
			(
				'menuaction' => 'admin._boaclmanager.submit',
				'acl_app'    => $acl_app,
				'location'   => phpgw::get_var('location', 'string'),
				'account_id' => $account_id
			);

			$acl    = createobject('phpgwapi.acl', $account_id);
			$acl->read();

			$this->_template->set_var('form_action', $GLOBALS['phpgw']->link('/index.php', $link_values));
			$this->_template->set_var('lang_title', lang('ACL Manager'));

			$grants = $acl->get_rights($location, $acl_app);

			$select = <<<HTML
				<select name="acl_rights[]" multiple size="7">

HTML;

			foreach ( $acl_manager['rights'] as $name => $value )
			{
				if ( !$GLOBALS['phpgw']->acl->check($location, $value, $acl_app) )
				{
					$s = '';
					$name = lang($name);

					if ( $grants & $value )
					{
						$s = ' selected';
					}

					$select .= <<<HTML
					<option value="{$value}{$s}">{$name}</option>

HTML;
				}
			}
			$select = <<<HTML
				</select>

HTML;

			$this->_template->set_var('select_values', $select);
			$this->_template->set_var('lang_submit', lang('submit'));
			$this->_template->set_var('lang_cancel', lang('cancel'));

			$this->_template->pfp('out', 'form');
		}

		/**
		 * List current addressmasters
		 *
		 * @return void
		 */
		public function list_addressmasters()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$link_data = array
			(
				'menuaction'	=> 'admin.uiaclmanager.edit_addressmasters',
				'account_id'	=> $GLOBALS['phpgw_info']['user']['account_id']
			);

			if ( phpgw::get_var('edit', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			if ( phpgw::get_var('done', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ': ' . lang('list addressmasters');
			$GLOBALS['phpgw']->xslttpl->add_file('addressmaster');

			try
			{
				if($GLOBALS['phpgw']->locations->get_id('addressbook', 'addressmaster') == 0)
				{
					$GLOBALS['phpgw']->locations->add('addressmaster', 'Address Master', 'addressbook');
				}
			}
			catch(Exception $e)
			{
				$GLOBALS['phpgw']->locations->add('addressmaster', 'Address Master', 'addressbook');
			}

			$admins = $this->_boacl->list_addressmasters();

			//_debug_array($admins);
			//exit;

			//initialize the arrays
			$users = array();
			$groups = array();
			if(is_array($admins))
			{
				foreach($admins as $admin)
				{
					if ($admin['lastname'] != 'Group')
					{
						$users[] = array
						(
							'lid'		=> $admin['lid'],
							'firstname'=> $admin['firstname'],
							'lastname'	=> $admin['lastname']
						);
					}
					else if ($admin['lastname'] == 'Group')
					{
						$groups[] = array
						(
							'lid'		=> $admin['lid'],
							'firstname'=> $admin['firstname'],
							'lastname'	=> $admin['lastname']
						);
					}
				}
			}

			//_debug_array($users);
			//exit;

			$link_data['menuaction'] = 'admin.uiaclmanager.list_addressmasters';

			$data = array
			(
				'sort_lid'				=> lang('loginid'),
				'sort_firstname'		=> lang('firstname'),
				'sort_lastname'			=> lang('lastname'),
				'sort_name'				=> lang('name'),
				'lang_users'			=> lang('users'),
				'lang_groups'			=> lang('groups'),
				'addressmaster_user'	=> $users,
				'addressmaster_group'	=> $groups,
				'lang_edit'				=> lang('edit'),
				'lang_done'				=> lang('done'),
				'action_url'			=> $GLOBALS['phpgw']->link('/index.php', $link_data)
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('addressmaster_list' => $data));
		}

		/**
		 * Render the accounts popup widget
		 *
		 * @return string html snippet
		 */
		public function accounts_popup()
		{
			return $GLOBALS['phpgw']->accounts_popup->render('admin_acl');
		}

		/**
		 * Render for for editting addressmasters
		 *
		 * @return void
		 */
		public function edit_addressmasters()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$link_data = array
			(
				'menuaction' 	=> 'admin.uiaclmanager.list_addressmasters',
				'account_id'	=> $GLOBALS['phpgw_info']['user']['account_id']
			);

			if ( phpgw::get_var('save', 'bool', 'POST') )
			{
				$account_addressmaster = phpgw::get_var('account_addressmaster', 'string', 'POST', array());
				$group_addressmaster = phpgw::get_var('group_addressmaster', 'int', 'POST', array());

				$error = array();//$this->_boacl->check_values($account_addressmaster, $group_addressmaster);
				if ( count($error) )
				{
					$error_message = $GLOBALS['phpgw']->common->error_list($error);
				}
				else
				{
					$this->_boacl->edit_addressmasters($account_addressmaster, $group_addressmaster);
					$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
				}
			}

			if ( phpgw::get_var('cancel', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ': ' . lang('edit addressmaster list');
			$GLOBALS['phpgw']->xslttpl->add_file('addressmaster');

			$popwin_user = array();
			$select_user = array();
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['account_selection'])
				&& $GLOBALS['phpgw_info']['user']['preferences']['common']['account_selection'] == 'popup_xxxx') // FIXME 'popup is broken'
			{
				$usel = $this->_boacl->list_addressmasters();
				foreach ( $usel as $acc )
				{
					$user_list[] = array
					(
						'account_id'	=> $acc['account_id'],
						'select_value'	=> 'yes',
						'fullname'		=> (string) $GLOBALS['phpgw']->accounts->get($acc['lid'])
					);
				}

				$popwin_user = array
				(
					'url'				=> $GLOBALS['phpgw']->link('/index.php',
											array('menuaction' => 'admin.uiaclmanager.accounts_popup'), true),
					'width'				=> '800',
					'height'			=> '600',
					'lang_open_popup'	=> lang('open popup window'),
					'user_list'			=> $user_list
				);
			}
			else
			{
				$app_user = (array) $GLOBALS['phpgw']->acl->get_ids_for_location('run', 1, 'addressbook');

				$add_users = array
				(
					'users'		=> array(),
					'groups'	=> array()
				);

				if ( is_array($app_user) )
				{
						$add_users = $GLOBALS['phpgw']->accounts->return_members($app_user);
				}
				$add_users['groups'] = $GLOBALS['phpgw']->accounts->get_list('groups');

				$usel = $this->_boacl->get_addressmaster_ids();

				//_debug_array($usel);
				foreach ( $add_users['users'] as $user )
				{
					$select_value = '';
					if (is_array($usel) && in_array($user, $usel) )
					{
						$select_value = 'yes';
					}

					$user_list[] = array
					(
						'account_id'	=> $user,
						'select_value'	=> $select_value,
						'fullname'		=> $GLOBALS['phpgw']->common->grab_owner_name($user)
					);
				}

				if ( is_array($add_users['groups']) && count($add_users['groups']) )
				{
					foreach( $add_users['groups'] as $group )
					{
						$select_value = '';
						if (is_array($usel) && in_array($group, $usel))
						{
							$select_value = 'yes';
						}

						$group_list[] = array
						(
							'account_id'	=> $group->id,
							'select_value'	=> $select_value,
							'fullname'	=> lang('%1 group', $group->firstname)
						);
					}
				}

				$select_user = array
				(
					'lang_select_users'		=> lang('Select users'),
					'lang_select_groups'	=> lang('Select groups'),
					'group_list'			=> $group_list,
					'user_list'				=> $user_list
				);
			}

			$link_data['menuaction'] = 'admin.uiaclmanager.edit_addressmasters';

			$data = array
			(
				'lang_select_addressmasters'	=> lang('Select addressmasters'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'action_url'					=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'popwin_user'					=> $popwin_user,
				'select_user'					=> $select_user
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('addressmaster_edit' => $data));
		}
	}
