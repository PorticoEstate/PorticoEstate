<?php
	/**************************************************************************\
	* phpGroupWare - Administration                                            *
	* http://www.phpgroupware.org                                              *
	* ------------------------------------------------------------------------ *
	* Copyright 2001 - 2003 Free Software Foundation, Inc                      *
	* This program is part of the GNU project, see http://www.gnu.org/         *
	* ------------------------------------------------------------------------ *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: class.uiaclmanager.inc.php 18358 2007-11-27 04:43:37Z skwashd $ */

	class uiaclmanager
	{
		var $template;
		var $nextmatchs;
		var $public_functions = array
		(
			'list_apps'				=> true,
			'access_form'			=> true,
			'account_list'			=> true,
			'list_addressmasters'	=> true,
			'edit_addressmasters'	=> true,
			'accounts_popup'		=> true,
			'java_script'			=> true
		);

		function uiaclmanager()
		{
			$this->account_id	= phpgw::get_var('account_id', 'int', 'GET', $GLOBALS['phpgw_info']['user']['account_id']);

			if (!$this->account_id || $GLOBALS['phpgw']->acl->check('account_access',64,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}
			$this->template		= createobject('phpgwapi.Template',PHPGW_APP_TPL);
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->boacl		= CreateObject('admin.boaclmanager');
		}

		function common_header()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
		}

		function list_apps()
		{
			$this->common_header();

			$GLOBALS['phpgw']->hooks->process('acl_manager',array('preferences'));

			$this->template->set_file(array(
				'app_list'   => 'acl_applist.tpl'
			));
			$this->template->set_block('app_list','list');
			$this->template->set_block('app_list','app_row');
			$this->template->set_block('app_list','app_row_noicon');
			$this->template->set_block('app_list','link_row');
			$this->template->set_block('app_list','spacer_row');

			$this->template->set_var('lang_header',lang('ACL Manager'));

			while (is_array($GLOBALS['acl_manager']) && list($app,$locations) = each($GLOBALS['acl_manager']))
			{
				$icon = $GLOBALS['phpgw']->common->image($app,array('navbar.gif',$app.'.gif'));
				$this->template->set_var('icon_backcolor',$GLOBALS['phpgw_info']['theme']['row_off']);
				$this->template->set_var('link_backcolor',$GLOBALS['phpgw_info']['theme']['row_off']);
				$this->template->set_var('app_name',lang($GLOBALS['phpgw_info']['navbar'][$app]['title']));
				$this->template->set_var('a_name',$appname);
				$this->template->set_var('app_icon',$icon);

				if ($icon)
				{
					$this->template->fp('rows','app_row',True);
				}
				else
				{
					$this->template->fp('rows','app_row_noicon',True);
				}

				while (is_array($locations) && list($loc,$value) = each($locations))
				{
					$total_rights = 0;
					while (list($k,$v) = each($value['rights']))
					{
						$total_rights += $v;
					}
					reset($value['rights']);

					// If all of there rights are denied, then they shouldn't even see the option
					if ($total_rights != $GLOBALS['phpgw']->acl->get_rights($loc,$app))
					{
						$link_values = array(
							'menuaction' => 'admin.uiaclmanager.access_form',
							'location'   => urlencode(base64_encode($loc)),
							'acl_app'    => $app,
							'account_id' => $GLOBALS['account_id']
						);

						$this->template->set_var('link_location',$GLOBALS['phpgw']->link('/index.php',$link_values));
						$this->template->set_var('lang_location',lang($value['name']));
						$this->template->fp('rows','link_row',True);
					}
				}

				$this->template->parse('rows','spacer_row',True);
			}
			$this->template->pfp('out','list');
		}

		function access_form()
		{
			$GLOBALS['phpgw']->hooks->single('acl_manager',$GLOBALS['acl_app']);
			$location = base64_decode($GLOBALS['location']);

			$acl_manager = $GLOBALS['acl_manager'][$GLOBALS['acl_app']][$location];

			$this->common_header();
			$this->template->set_file('form','acl_manager_form.tpl');

			$acc = createobject('phpgwapi.accounts',$GLOBALS['account_id']);
			$acc->read_repository();
			$afn = $GLOBALS['phpgw']->common->display_fullname($acc->data['account_lid'],$acc->data['firstname'],$acc->data['lastname']);

			$this->template->set_var('lang_message',lang('Check items to <b>%1</b> to %2 for %3',$acl_manager['name'],$GLOBALS['acl_app'],$afn));
			$link_values = array(
				'menuaction' => 'admin.boaclmanager.submit',
				'acl_app'    => $GLOBALS['acl_app'],
				'location'   => urlencode($GLOBALS['location']),
				'account_id' => $GLOBALS['account_id']
			);

			$acl    = createobject('phpgwapi.acl',$GLOBALS['account_id']);
			$acl->read_repository();

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php',$link_values));
			$this->template->set_var('lang_title',lang('ACL Manager'));

			$total = 0;
			while (list($name,$value) = each($acl_manager['rights']))
			{
				$grants = $acl->get_rights($location,$GLOBALS['acl_app']);

				if (! $GLOBALS['phpgw']->acl->check($location,$value,$GLOBALS['acl_app']))
				{
					$s .= '<option value="' . $value . '"';
					$s .= (($grants & $value)?' selected="selected"':'');
					$s .= '>' . lang($name) . '</option>';
					$total++;
				}
			}

			$size = 7;
			if ($total < 7)
			{
				$size = $total;
			}
			$this->template->set_var('select_values','<select name="acl_rights[]" multiple size="' . $size . '">' . $s . '</select>');
			$this->template->set_var('lang_submit',lang('Submit'));
			$this->template->set_var('lang_cancel',lang('Cancel'));

			$this->template->pfp('out','form');
		}

		function list_addressmasters()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;

			$link_data = array
			(
				'menuaction'	=> 'admin.uiaclmanager.edit_addressmasters',
				'account_id'	=> $GLOBALS['phpgw_info']['user']['account_id']
			);

			if ( phpgw::get_var('edit', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			if ( phpgw::get_var('done', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ': ' . lang('list addressmasters');
			$GLOBALS['phpgw']->xslttpl->add_file('addressmaster');

			$admins = $this->boacl->list_addressmasters();
			
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
					elseif ($admin['lastname'] == 'Group')
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
				'action_url'			=> $GLOBALS['phpgw']->link('/index.php',$link_data) 
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('addressmaster_list' => $data));
		}

		function accounts_popup()
		{
			return $GLOBALS['phpgw']->accounts->accounts_popup('admin_acl');
		}

		function java_script()
		{
			//return $GLOBALS['phpgw']->accounts->java_script('admin_acl');
		}

		function edit_addressmasters()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$link_data = array
			(
				'menuaction' 	=> 'admin.uiaclmanager.list_addressmasters',
				'account_id'	=> $GLOBALS['phpgw_info']['user']['account_id']
			);

			if ( phpgw::get_var('save', 'bool', 'POST') ) 
			{
				$account_addressmaster = phpgw::get_var('account_addressmaster', 'int', 'POST', array());
				$group_addressmaster = phpgw::get_var('group_addressmaster', 'int', 'POST', array());

				$error = $this->boacl->check_values($account_addressmaster, $group_addressmaster);
				if(is_array($error))
				{
					$error_message = $GLOBALS['phpgw']->common->error_list($error);
				}
				else
				{
					$this->boacl->edit_addressmasters($account_addressmaster, $group_addressmaster);
					$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
				}
			}

			if ( phpgw::get_var('cancel', 'bool', 'POST') )
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('admin') . ': ' . lang('edit addressmaster list');
			$GLOBALS['phpgw']->xslttpl->add_file('addressmaster');

			$popwin_user = array();
			$select_user = array();
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['account_selection'])
				&& $GLOBALS['phpgw_info']['user']['preferences']['common']['account_selection'] == 'popup')
			{
				$usel = $this->boacl->list_addressmasters();
				foreach ( $usel as $acc )
				{
					$user_list[] = array
					(
						'account_id'	=> $acc['account_id'],
						'select_value'	=> 'yes',
						'fullname'		=> $GLOBALS['phpgw']->common->display_fullname($acc['lid'],$acc['firstname'],$acc['lastname'])
					);
				}

				$popwin_user = array
				(
					'url'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaclmanager.accounts_popup'), true),
					'width'				=> '800',
					'height'			=> '600',
					'lang_open_popup'	=> lang('open popup window'),
					'user_list'			=> $user_list
				);
			}
			else
			{
				$app_user = $GLOBALS['phpgw']->acl->get_ids_for_location('run',1,'addressbook');

				$add_users = $GLOBALS['phpgw']->accounts->return_members($app_user);
				$add_users['groups'] = $GLOBALS['phpgw']->accounts->get_list('groups');

				$usel = $this->boacl->get_addressmaster_ids();

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
							'account_id'	=> $group['account_id'],
							'select_value'	=> $select_value,
							'fullname'	=> lang('%1 group', $group['account_firstname'])
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
				'action_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'popwin_user'					=> $popwin_user,
				'select_user'					=> $select_user
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('addressmaster_edit' => $data));
		}
	}
?>
