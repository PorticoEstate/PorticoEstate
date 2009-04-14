<?php
	/**
	* Shared functions for other account repository managers and loader
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	* @package phpgwapi
	* @subpackage ui
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU Lesser General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */


	class phpgwapi_accounts_popup
	{
		/**
		 * @var object $t reference to global template object
		 */
		private $t;

		public function __construct()
		{
			$this->t =& $GLOBALS['phpgw']->template;
		}

		public function render($app)
		{
			$group_id = phpgw::get_var('group_id', 'int');

			$query = phpgw::get_var('query', 'string', 'POST');
			$start = phpgw::get_var('start', 'int', 'GET');
			$order = phpgw::get_var('order', 'string', 'GET', 'account_lid');
			$sort = phpgw::get_var('sort', 'string', 'GET', 'ASC');

			$nextmatches = createObject('phpgwapi.nextmatchs');

			$this->t->set_root(PHPGW_TEMPLATE_DIR);

			$this->t->set_file(array('accounts_list_t' => 'accounts_popup.tpl'));
			$this->t->set_block('accounts_list_t','group_select','select');
			$this->t->set_block('accounts_list_t','group_other','other');
			$this->t->set_block('accounts_list_t','group_all','all');

			$this->t->set_block('accounts_list_t','withperm_intro','withperm');
			//$this->t->set_block('accounts_list_t','other_intro','iother');
			$this->t->set_block('accounts_list_t','withoutperm_intro','withoutperm');


			$this->t->set_block('accounts_list_t','accounts_list','list');


			$this->t->set_var('title', isset($GLOBALS['phpgw_info']['site_title']) ? $GLOBALS['phpgw_info']['site_title'] : '');
			$this->t->set_var('charset', 'urf-8');
			$this->t->set_var('lang_search',lang('search'));
			$this->t->set_var('lang_groups',lang('user groups'));
			$this->t->set_var('lang_accounts',lang('user accounts'));

			$this->t->set_var('img',$GLOBALS['phpgw']->common->image('phpgwapi','select'));
			$this->t->set_var('lang_select_user',lang('Select user'));
			$this->t->set_var('lang_select_group',lang('Select group'));
			$this->t->set_var('css_file', "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/idots/css/idots.css");

			switch($app)
			{
				case 'admin':
					$action = 'admin.uiaccounts.accounts_popup';
					$this->t->set_var('select_name',"account_user[]']");
					$this->t->set_var('js_function','ExchangeAccountSelect');
					$this->t->set_var('lang_perm',lang('group name'));
					$this->t->fp('withperm','withperm_intro',true);
					break;
				case 'admin_acl':
					$action = 'admin.uiaclmanager.accounts_popup';
					$app = 'addressbook';
					$this->t->set_var('select_name',"account_addressmaster[]']");
					$this->t->set_var('js_function','ExchangeAccountSelect');
					$this->t->fp('withperm','withperm_intro',true);
					$this->t->fp('withoutperm','withoutperm_intro',true);
					break;
				case 'projects':
					$action = 'projects.uiprojects.accounts_popup';
					$this->t->set_var('select_name',"values[coordinator]']");
					$this->t->set_var('js_function','ExchangeAccountText');
					$this->t->fp('withperm','withperm_intro',true);
					$this->t->fp('withoutperm','withoutperm_intro',true);
					break;
				case 'e_projects':
					$action = 'projects.uiprojects.e_accounts_popup';
					$app = 'projects';
					$this->t->set_var('select_name',"employees[]']");
					$this->t->set_var('js_function','ExchangeAccountSelect');
					$this->t->fp('withperm','withperm_intro',true);
					$this->t->fp('withoutperm','withoutperm_intro',true);
					break;
			}

			$this->t->set_var('lang_perm',lang('Groups with permission for %1',lang($app)));
			$this->t->set_var('lang_nonperm',lang('Groups without permission for %1',lang($app)));

			$link_data = array
			(
				'menuaction'	=> $action,
				'group_id'		=> $group_id
			);

			$app_groups = array();

			if ($app != 'admin')
			{
				$user_groups = $GLOBALS['phpgw']->accounts->membership($this->account_id);
				$aclusers = $GLOBALS['phpgw']->acl->get_ids_for_location('run', 1, $app);
				$acl_users = $GLOBALS['phpgw']->accounts->return_members($aclusers);
				$app_user	= $acl_users['users'];
				$app_groups	= $acl_users['groups'];
				/*
				$app_groups	= $this->get_list('groups');
				$app_user	= $this->get_list('accounts');
				*/

			}
			else
			{
				$all_groups	= $GLOBALS['phpgw']->accounts->get_list('groups');
				$all_user	= $GLOBALS['phpgw']->accounts->get_list('accounts');

				while(is_array($all_groups) && (list(,$agroup) = each($all_groups)))
				{
					$user_groups[] = array
					(
						'account_id'	=> $agroup->id,
						'account_name'	=> $agroup->firstname
					);
				}

			//	$i = 0;
			//	for($j=0;$j<count($user_groups); ++$j)
				$app_groups = array();
				foreach ($all_user as $group)
				{
					$app_groups[] = $group->id;
				//	++$i;
				}

				$app_user = array();
				foreach ($all_user as $user)
				//for($j=0;$j<count($all_user);$j++)
				{
					$app_user[] = $user->id;
				//	++$i;
				}

//_debug_array($user_groups);
			}

			while ( isset($user_groups) && is_array($user_groups) && (list(,$group) = each($user_groups)) )
			{
				$i = 0;
				if (in_array($group->id, $app_groups))
				{
					$this->t->set_var('tr_class', $nextmatches->alternate_row_class(++$i%2));
					//$link_data['group_id'] = $group['account_id'];
					$this->t->set_var('link_user_group', $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $action, 'group_id' => (int)$group->id) ) );
					$this->t->set_var('name_user_group', $group->lid);
					$this->t->set_var('account_display', $GLOBALS['phpgw']->common->grab_owner_name($group->id));
					$this->t->set_var('accountid', $group->id);
					switch($app)
					{
						case 'addressbook':
						default:
							$this->t->fp('other','group_other',true);
					}
				}
				else
				{
//_debug_array($group['account_id']);
					if ($app != 'admin')
					{
						$this->t->set_var('link_all_group', $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $action, 'group_id' => (int)$group->id) ) );
						$this->t->set_var('name_all_group', $group->lid);
						$this->t->set_var('accountid', $group->id);
						$this->t->fp('all', 'group_all', true);
					}
				}
			}

			if ( !$query )
			{
				$val_users = array();
				if (isset($group_id) && !empty($group_id))
				{
					//echo 'GROUP_ID: ' . $group_id;
					$users =  $GLOBALS['phpgw']->accounts->get_members($group_id);

					for ($i=0;$i<count($users); ++$i)
					{
						if (in_array($users[$i],$app_user))
						{
							$account = $GLOBALS['phpgw']->accounts->get($users[$i]);

							switch ($order)
							{
								case 'account_firstname':
									$id = $account->firstname;
									break;
								case 'account_lastname':
									$id = $account->lastname;
									break;
								case 'account_lid':
								default:
									$id = $account->lid;
									break;
							}

							$id .= $account->lastname;	// default sort-order
							$id .= $account->firstname;
							$id .= $account->id;	// make our index unique

							$val_users[$id] = array
							(
								'account_id'		=> $account->id,
								'account_firstname'	=> $account->firstname,
								'account_lastname'	=> $account->lastname
							);
						}
					}

					if (is_array($val_users))
					{
						if ($sort == 'ASC')
						{
							ksort($val_users);
						}
						else
						{
							krsort($val_users);
						}
					}
					$val_users = array_values($val_users);	// get a numeric index
				}
				$total = count($val_users);
			}
			else
			{
				switch($app)
				{
					case 'calendar':	$select = 'both'; break;
					default:			$select = 'accounts'; break;
				}
				$entries	=$GLOBALS['phpgw']->accounts->get_list($select, $start, $sort, $order, $query);
				$total		= $this->total;
				foreach ( $entries as $entry)
				{
					if (in_array($entry->id,$app_user))
					{
						$val_users[] = array
						(
							'account_id'		=> $entry->id,
							'account_firstname'	=> $entry->firstname,
							'account_lastname'	=> $entry->lastname
						);
					}
				}
			}

	// --------------------------------- nextmatch ---------------------------

			$left = $nextmatches->left('/index.php',$start,$total,$link_data);
			$right = $nextmatches->right('/index.php',$start,$total,$link_data);
			$this->t->set_var('left',$left);
			$this->t->set_var('right',$right);

			$this->t->set_var('lang_showing',$nextmatches->show_hits($total,$start));

	// -------------------------- end nextmatch ------------------------------------

			$this->t->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->t->set_var('search_list',$nextmatches->search(array('query' => $query, 'search_obj' => 1)));

	// ---------------- list header variable template-declarations --------------------------

	// -------------- list header variable template-declaration ------------------------
			$this->t->set_var('sort_lid',$nextmatches->show_sort_order($sort,'account_lid',$order,'/index.php',lang('LoginID'),$link_data));
			$this->t->set_var('sort_firstname',$nextmatches->show_sort_order($sort,'account_firstname',$order,'/index.php',lang('Firstname'),$link_data));
			$this->t->set_var('sort_lastname',$nextmatches->show_sort_order($sort,'account_lastname',$order,'/index.php',lang('Lastname'),$link_data));

	// ------------------------- end header declaration --------------------------------
			$stop = $start + $nextmatches->maxmatches;
			for ($i=$start;$i<count($val_users)&&$i<$stop;$i++)
			{
				$this->t->set_var('tr_class', $nextmatches->alternate_row_class($i%2));
				
				$firstname = $val_users[$i]['account_firstname'];
				if (!$firstname)
				{
					$firstname = '&nbsp;';
				}
				
				$lastname = $val_users[$i]['account_lastname'];
				if (!$lastname)
				{
					$lastname = '&nbsp;';
				}

	// ---------------- template declaration for list records -------------------------- 

				$this->t->set_var(array
				(
					'firstname'			=> $firstname,
					'lastname'			=> $lastname,
					'accountid'			=> $val_users[$i]['account_id'],
					'account_display'	=> $GLOBALS['phpgw']->common->grab_owner_name($val_users[$i]['account_id'])
				));

				$this->t->fp('list','accounts_list',true);
			}

			$this->t->set_var('start', $start);
			$this->t->set_var('sort', $sort);
			$this->t->set_var('order', $order);
			$this->t->set_var('query', $query);
			$this->t->set_var('group_id', $group_id);

			$this->t->set_var('lang_done',lang('done'));
			$this->t->pfp('out','accounts_list_t',true);
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
	}
