<?php
	/**************************************************************************\
	* phpGroupWare - Administration                                            *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	phpgw::import_class('phpgwapi.jquery');

	class admin_uiaccess_history
	{
		public $public_functions = array
		(
			'list_history' => True
		);

		public function __construct()
		{
			phpgwapi_jquery::load_widget('select2');
		}

		public function list_history()
		{
			if ($GLOBALS['phpgw']->acl->check('access_log_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}

			$bo         = createobject('admin.boaccess_history');
			$nextmatches = createobject('phpgwapi.nextmatchs');

			$account_id	= phpgw::get_var('account_id', 'int', 'REQUEST', 0);
			$start		= phpgw::get_var('start', 'int', 'GET', 0);
			$sort		= phpgw::get_var('sort', 'int', 'POST', 0);
			$order		= phpgw::get_var('order', 'int', 'POST', 0);
			$query		= phpgw::get_var('query');

			if(!$account_id && $query)
			{
				$account_id = $GLOBALS['phpgw']->accounts->name2id($query);
			}
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Admin').' - '.lang('View access log');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::admin::access_log';

			$GLOBALS['phpgw']->common->phpgw_header(true);

			$t   =& $GLOBALS['phpgw']->template;
			$t->set_root(PHPGW_APP_TPL);
			$t->set_file('accesslog', 'accesslog.tpl');
			$t->set_block('accesslog','list');
			$t->set_block('accesslog','row');
			$t->set_block('accesslog','row_empty');

			$total_records = $bo->total($account_id);

			$var = array
			(
				'nextmatchs_left'	 => $nextmatches->left('/index.php', $start, $total_records, '&menuaction=admin.uiaccess_history.list_history&account_id=' . $account_id),
				'nextmatchs_right'	 => $nextmatches->right('/index.php', $start, $total_records, '&menuaction=admin.uiaccess_history.list_history&account_id=' . $account_id),
				'showing'			 => $nextmatches->show_hits($total_records, $start),
//				'nm_search'			 => $nextmatches->search(array('query' => $query)),
				'lang_loginid'		 => lang('LoginID'),
				'lang_ip'			 => lang('IP'),
				'lang_login'		 => lang('Login'),
				'lang_logout'		 => lang('Logout'),
				'lang_total'		 => lang('Total'),
			);
			$__account	 = $GLOBALS['phpgw']->accounts->get($account_id);
			if($__account->enabled)
			{
				$accounts[]	 = array
				(
					'id'	 => $__account->id,
					'name'	 => $__account->__toString()
				);
			}

			phpgw::import_class('phpgwapi.jquery');
			phpgwapi_jquery::load_widget('select2');

			$account_list	 = "<div><form class='pure-form' method='POST' action=''>";
			$account_list	 .= '<select name="account_id" id="account_id" onChange="this.form.submit();" style="width:50%;">';
			$account_list	 .= "<option value=''>" . lang('select user') . '</option>';
			foreach ($accounts as $account)
			{
				$account_list .= "<option value='{$account['id']}'";
				if ($account['id'] == $account_id)
				{
					$account_list .= ' selected';
				}
				$account_list .= "> {$account['name']}</option>\n";
			}
			$account_list	 .= '</select>';
			$account_list	 .= '<noscript><input type="submit" name="user" value="Select"></noscript>';
			$account_list	 .= '</form></div>';

			$lan_user = lang('Search for a user');
			$account_list	 .= <<<HTML
					<script>
						var oArgs = {menuaction: 'preferences.boadmin_acl.get_users'};
						var strURL = phpGWLink('index.php', oArgs, true);

						$("#account_id").select2({
						  ajax: {
							url: strURL,
							dataType: 'json',
							delay: 250,
							data: function (params) {
							  return {
								query: params.term, // search term
								page: params.page || 1
							  };
							},
							cache: true
						  },
						  width: '50%',
						  placeholder: '{$lan_user}',
						  minimumInputLength: 2,
						  language: "no",
						  allowClear: true
						});
					</script>
HTML;

			$var['select_user'] =  $account_list;

			if ($account_id)
			{
				$var['link_return_to_view_account'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php',
					Array(
						'menuaction' => 'admin.uiaccounts.view',
						'account_id' => $account_id
					)
				) . '">' . lang('Return to view account') . '</a>';
				$var['lang_last_x_logins'] = lang('Last %1 logins for %2',$total_records,$GLOBALS['phpgw']->common->grab_owner_name($account_id));
			}
			else
			{
				$var['lang_last_x_logins'] = lang('Last %1 logins',$total_records);
			}

			$var['actionurl']	= $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'admin.uiaccess_history.list_history'));

			$t->set_var($var);

			$records = $bo->list_history($account_id, $start, $order, $sort);
			if ( is_array($records) )
			{
				foreach ( $records as &$record )
				{
					$nextmatches->template_alternate_row_class($t);

					$var = array
					(
						'row_loginid' => $record['loginid'],
						'row_ip'      => $record['ip'],
						'row_li'      => $record['li'],
						'row_lo'      => $record['account_id'] ? $record['lo'] : '<b>' . lang($record['sessionid']) . '</b>',
						'row_total'   => ($record['lo'] ? $record['total'] : '&nbsp;')
					);
					$t->set_var($var);
					$t->fp('rows_access','row', true);
				}
			}

			if (! $total_records && $account_id)
			{
				$nextmatches->template_alternate_row_class($t);
				$t->set_var('row_message',lang('No login history exists for this user'));
				$t->fp('rows_access','row_empty', true);
			}

			$loggedout = $bo->return_logged_out($account_id);

			if ($total_records)
			{
				$percent = round(($loggedout / $total_records) * 100);
			}
			else
			{
				$percent = '0';
			}

			$var = array
			(
				'footer_total' => lang('Total records') . ': ' . $total_records
			);

			if ($account_id)
			{
				$var['lang_percent'] = lang('Percent this user has logged out') . ': ' . $percent . '%';
			}
			else
			{
				$var['lang_percent'] = lang('Percent of users that logged out') . ': ' . $percent . '%';
			}

			// create the menu on the left, if needed
			$var['rows'] = createObject('admin.uimenuclass')->createHTMLCode('view_account');

			$t->set_var($var);
			$t->pfp('out','list');
		}
	}
