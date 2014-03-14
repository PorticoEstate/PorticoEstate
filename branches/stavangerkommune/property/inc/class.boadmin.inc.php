<?php

	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
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

	/**
	 * FIXME I need a proper description
	 * @package property
	 */
	class property_boadmin
	{

		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $offset;
		var $acl_app;

		function property_boadmin($session = '')
		{
			//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so		 = CreateObject('property.soadmin');
			$this->acl		 = & $GLOBALS['phpgw']->acl;
			$this->bocommon	 = CreateObject('property.bocommon');
			$this->right	 = array(1, 2, 4, 8, 16);
			$this->account_id = $GLOBALS['phpgw_info']['user']['account_id'];

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start			 = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query			 = phpgw::get_var('query');
			$sort			 = phpgw::get_var('sort');
			$order			 = phpgw::get_var('order');
			$filter			 = phpgw::get_var('filter', 'int');
			$cat_id			 = phpgw::get_var('cat_id', 'string');
			$permission		 = phpgw::get_var('permission');
	//		$location		 = get_var('location',array('POST','GET')); // don't work for some reason...
			$module			 = phpgw::get_var('module');
			$granting_group	 = phpgw::get_var('granting_group', 'int');
			$allrows		 = phpgw::get_var('allrows', 'bool');
			$acl_app		 = 'property'; //get_var('acl_app',array('POST','GET'));

			if ($start)
			{
				$this->start = $start;
			}
			else
			{
				$this->start = 0;
			}

			if (isset($query))
			{
				$this->query = $query;
			}
			if (isset($filter))
			{
				$this->filter = $filter;
			}
			if (isset($sort))
			{
				$this->sort = $sort;
			}
			if (isset($order))
			{
				$this->order = $order;
			}
			if (isset($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			if (isset($module))
			{
				$this->location = $module;
			}
			if (isset($granting_group))
			{
				$this->granting_group = $granting_group;
			}

			$this->allrows = $allrows ? $allrows : '';

			if (isset($acl_app))
			{
				$this->acl_app = $acl_app;
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data', 'fm_admin');

			$this->start			 = $data['start'];
			$this->query			 = $data['query'];
			$this->filter			 = $data['filter'];
			$this->sort				 = $data['sort'];
			$this->order			 = $data['order'];
			$this->cat_id			 = $data['cat_id'];
			$this->location			 = $data['location'];
			$this->granting_group	 = $data['granting_group'];
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data', 'fm_admin', $data);
			}
		}

		function select_category_list($format = '', $selected = '')
		{
			switch ($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
					break;
			}

			$categories[0]['id']	 = 'groups';
			$categories[0]['name']	 = lang('Groups');
			$categories[1]['id']	 = 'accounts';
			$categories[1]['name']	 = lang('Users');

			return $this->bocommon->select_list($selected, $categories);
		}

		function set_permission2($values, $r_processed, $grantor = -1, $type = 0)
		{
			if (!is_array($values))
			{
				//				return;
			}

			$totalacl = array();
			foreach ($values as $rowinfo => $perm)
			{
				list($user_id, $rights) = explode('_', $rowinfo);

				if (!isset($totalacl[$user_id]))
				{
					$totalacl[$user_id] = 0;
				}

				$totalacl[$user_id] += $rights;
			}

			$user_checked = array();
			foreach ($totalacl as $user_id => $rights)
			{
				$user_checked[]	 = $user_id;
				$this->acl->set_account_id($user_id, true, $this->acl_app, $this->location, $account_type	 = 'accounts');
				$this->acl->delete($this->acl_app, $this->location, $grantor, $type);
				$this->acl->add($this->acl_app, $this->location, $rights, $grantor, $type);
				$this->acl->save_repository($this->acl_app, $this->location);
			}

			if (is_array($r_processed) && count($user_checked))
			{
				$user_delete = array_diff($r_processed, $user_checked);
			}
			else
			{
				$user_delete = $r_processed;
			}

			$users_at_location = $this->acl->get_accounts_at_location($this->acl_app, $this->location, $grantor, $type);

			if (is_array($user_delete) && count($user_delete) > 0)
			{
				foreach ($user_delete as $user_id)
				{
					if (isset($users_at_location[$user_id]) && $users_at_location[$user_id])
					{
						$this->acl->set_account_id($user_id, true);
						$this->acl->delete($this->acl_app, $this->location, $grantor, $type);
						$this->acl->save_repository($this->acl_app, $this->location);
					}
				}
			}
		}

		function set_permission($values, $r_processed, $set_grant = false, $initials = '')
		{
			$this->acl->enable_inheritance = phpgw::get_var('enable_inheritance', 'bool', 'POST');

			if ($initials)
			{
				$this->so->set_initials($initials);
			}

			$process = explode('_', $r_processed);

			if (!isset($values['right']) || !is_array($values['right']))
			{
				$values['right'] = array();
			}

			if (!isset($values['mask']) || !is_array($values['mask']))
			{
				$values['mask'] = array();
			}

			$grantor = -1;
			if ($set_grant)
			{
				if ($this->granting_group)
				{
					$grantor = $this->granting_group;
				}
				else
				{
					$grantor = $this->account_id;
				}
			}

			$this->set_permission2($values['right'], $process, $grantor, 0);
			$this->set_permission2($values['mask'], $process, $grantor, 1);
			$cleared				 = $this->bocommon->reset_fm_cache_userlist();
			$receipt['message'][]	 = array('msg' => lang('permissions are updated!'));
			$receipt['message'][]	 = array('msg' => lang('%1 userlists cleared from cache', $cleared));
			phpgwapi_cache::user_clear('phpgwapi', 'menu', -1);
			return $receipt;
		}

		function get_user_list($type = '', $get_grants = '')
		{
			if ($type == 'groups')
			{
				$check_account_type = array('accounts');
				$acl_account_type	 = 'accounts';
				$valid_users		 = $GLOBALS['phpgw']->acl->get_ids_for_location('run', phpgwapi_acl::READ, 'property');
			}
			else
			{
				$check_account_type = array('groups', 'accounts');
				$acl_account_type	 = 'both';
				$_valid_users		 = $GLOBALS['phpgw']->acl->get_user_list_right(phpgwapi_acl::READ, 'run', $this->acl_app);
				$valid_users		 = array();
				foreach ($_valid_users as $_user)
				{
					$valid_users[] = $_user['account_id'];
				}
				unset($_user);
				unset($_valid_users);
			}

			$grantor = -1;
			if ($get_grants)
			{
				if ($this->granting_group)
				{
					$grantor = $this->granting_group;
				}
				else
				{
					$grantor = $this->account_id;
				}
			}

			if ($this->location == '.invoice')
			{
				$this->right = array(1, 2, 4, 8, 16, 32, 64, 128);
			}

			$right = $this->right;

			$allusers = $GLOBALS['phpgw']->accounts->get_list($type, -1, $this->sort, $this->order, $this->query);

			//			$allusers	= array_intersect_key($allusers, $valid_users);

			foreach ($allusers as $user)
			{
				if (!in_array($user->id, $valid_users))
				{
					unset($allusers[$user->id]);
				}
			}
			unset($user);
			reset($allusers);

			$this->total_records = count($allusers);
			$length				 = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			if ($this->allrows)
			{
				$this->start = 0;
				$length		 = $this->total_records;
			}

			$allusers = array_slice($allusers, $this->start, $length, true);

			$user_list = array();
			if (isset($allusers) && is_array($allusers))
			{
				$j = 0;
				foreach ($allusers as $account)
				{
					$user_list[$j]['account_id']		 = $account->id;
					$user_list[$j]['account_lid']		 = $account->lid;
					$user_list[$j]['account_firstname']	 = $account->firstname;
					$user_list[$j]['account_lastname']	 = $account->lastname;

					if ($this->location == '.invoice')
					{
						$user_list[$j]['initials'] = $this->so->get_initials($account->id);
					}

					$this->acl->set_account_id($account->id, true, $this->acl_app, $this->location, $acl_account_type);

					$count_right = count($right);

					for ($i = 0; $i < $count_right; ++$i)
					{
						if ($this->acl->check_rights($this->location, $right[$i], $this->acl_app, $grantor, 0, $check_account_type))
						{
							if ($this->acl->account_type == 'g')
							{
								$user_list[$j]['right'][$right[$i]] = 'from_group';
							}
							else
							{
								$user_list[$j]['right'][$right[$i]]	 = 'checked';
							}
							$user_list[$j]['result'][$right[$i]] = 'checked';
						}
						if ($this->acl->check_rights($this->location, $right[$i], $this->acl_app, $grantor, 1, $check_account_type))
						{
							if ($this->acl->account_type == 'g')
							{
								$user_list[$j]['mask'][$right[$i]] = 'from_group';
							}
							else
							{
								$user_list[$j]['mask'][$right[$i]] = 'checked';
							}
							unset($user_list[$j]['result'][$right[$i]]);
						}
					}
					$j++;
				}
			}

			//_debug_array($user_list);
			return $user_list;
		}

		function read_fm_id()
		{
			$fm_ids = $this->so->read_fm_id();
			return $fm_ids;
		}

		function edit_id($values = '')
		{
			return $this->so->edit_id($values);
		}

	}
