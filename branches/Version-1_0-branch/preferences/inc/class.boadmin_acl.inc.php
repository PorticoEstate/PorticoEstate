<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class boadmin_acl
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $acl_app;

		/**
		* @var int $total_records the total number of records found during last search
		*/
		var $total_records = 0;

		function boadmin_acl($session='')
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];

			$this->acl 		= $GLOBALS['phpgw']->acl;
			$this->right		= array(1,2,4,8,16);

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$acl_app	= phpgw::get_var('acl_app');
			$start		= phpgw::get_var('start');
			$query		= phpgw::get_var('query');
			$sort		= phpgw::get_var('sort');
			$order		= phpgw::get_var('order');
			$filter		= phpgw::get_var('filter');
			$cat_id		= phpgw::get_var('cat_id');
			$permission	= phpgw::get_var('permission');
			$module		= phpgw::get_var('module');
			$granting_group	= phpgw::get_var('granting_group');
			$allrows	= phpgw::get_var('allrows');

			$this->allrows = $allrows ? $allrows : '';

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if($acl_app && !$this->acl_app)
			{
				$this->acl_app = $acl_app;
			}
			else
			{
				$this->acl_app = $this->currentapp;
			}
			if(isset($query))
			{
				$this->query = $query;
			}
			if(isset($filter))
			{
				$this->filter = $filter;
			}
			if(isset($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order))
			{
				$this->order = $order;
			}
			if(isset($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($module))
			{
				$this->location = $module;
			}
			if(isset($granting_group))
			{
				$this->granting_group = $granting_group;
			}
		}


		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','fm_admin');

			$this->start		= $data['start'];
			$this->query		= $data['query'];
			$this->filter		= $data['filter'];
			$this->sort			= $data['sort'];
			$this->order		= $data['order'];
			$this->cat_id		= $data['cat_id'];
			$this->location		= $data['location'];
			$this->granting_group	= $data['granting_group'];
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','fm_admin',$data);
			}
		}

		function select_location($format = 'filter', $selected='', $grant = false, $allow_c_attrib = false)
		{

			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('select_location'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('filter_location'));
					break;
			}

			$location_list = array();

			$locations = $GLOBALS['phpgw']->locations->get_locations($grant, $this->acl_app, $allow_c_attrib);

			$i = 0;
			foreach ( $locations as $loc_id => $loc_descr )
			{
				$sel_loc = '';
				if ($loc_id == $selected)
				{
					$sel_loc = 'selected';
				}

				$location_list[$i] = array
				(
					'id'		=> $loc_id,
					'descr'		=> "{$loc_id} [{$loc_descr}]",
					'selected'	=> $sel_loc
				);

				if ($location_list[$i]['selected'] != 'selected')
				{
					unset($location_list[$i]['selected']);
				}
				++$i;
			}
			return $location_list;
		}

		function get_locations($data = array())
		{
			$location_list = array();
	
			$grant				= isset($data['grant']) && $data['grant'] ? $data['grant'] : false;
			$grant				= isset($data['allow_c_attrib']) && $data['allow_c_attrib'] ? $data['allow_c_attrib'] : false;
			$acl_app			= isset($data['acl_app']) && $data['acl_app'] ? $data['acl_app'] : $GLOBALS['phpgw_info']['flags']['currentapp'];
			$selected			= isset($data['selected']) && $data['selected'] ? $data['selected'] : '';
			$c_function			= isset($data['$c_function']) && $data['$c_function'] ? true : false;
			$have_categories	= isset($data['have_categories']) && $data['have_categories'] ? true : false;

			$locations = $GLOBALS['phpgw']->locations->get_locations($grant, $acl_app, $allow_c_attrib, $c_function, $have_categories);

			foreach ( $locations as $loc_id => $loc_descr )
			{
				$location_list[] = array
				(
					'id'		=> $loc_id,
					'name'		=> "{$loc_id} [{$loc_descr}]",
					'selected'	=> $loc_id == $selected ? 1 : 0
				);

			}
			return $location_list;
		}

		function select_category_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
					break;
			}

			$categories[0]['id']	= 'groups';
			$categories[0]['name']	= lang('Groups');
			$categories[1]['id']	= 'accounts';
			$categories[1]['name']	= lang('Users');

			foreach ( $categories as $row => $category )
			{
				if ($category['id']==$selected)
				{
					$category_list[] = array
					(
						'cat_id'	=> $category['id'],
						'name'		=> $category['name'],
						'selected'	=> 'selected'
					);
				}
				else
				{
					$category_list[] = array
					(
						'cat_id'	=> $category['id'],
						'name'		=> $category['name'],
					);
				}
			}

			return $category_list;
		}

		function set_permission2($values,$r_processed, $grantor = -1, $type = 0)
		{
			if ( !is_array($values) )
			{
//				return;
			}

			$totalacl = array();
			foreach ( $values as $rowinfo => $perm )
			{
				list($user_id,$rights) = split('_', $rowinfo);

				if ( !isset($totalacl[$user_id]) )
				{
					$totalacl[$user_id] = 0;
				}

				$totalacl[$user_id] += $rights;
			}

			$user_checked = array();
			foreach ( $totalacl as $user_id => $rights )
			{
				$user_checked[] = $user_id;

				$this->acl->set_account_id($user_id, true);
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

			$users_at_location = $this->acl->get_accounts_at_location($this->acl_app, $this->location, $grantor ,$type);

			if(is_array($user_delete) && count($user_delete)>0)
			{
				foreach ($user_delete as $user_id)
				{
					if(isset($users_at_location[$user_id]) && $users_at_location[$user_id])
					{
						$this->acl->set_account_id($user_id, true, $this->acl_app, $this->location, 'accounts'); 
						$this->acl->delete($this->acl_app, $this->location, $grantor, $type);
						$this->acl->save_repository($this->acl_app, $this->location);
					}
				}
			}
		}

		function set_permission($values,$r_processed,$set_grant = false)
		{
			$this->acl->enable_inheritance = phpgw::get_var('enable_inheritance', 'bool', 'POST');

			$process = explode('_', $r_processed);

			if ( !isset($values['right']) || !is_array($values['right']) )
			{
				$values['right'] = array();
			}

			if ( !isset($values['mask']) || !is_array($values['mask']) )
			{
				$values['mask'] = array();
			}

			$grantor = -1;
			if($set_grant)
			{
				if($this->granting_group)
				{
					$grantor = $this->granting_group;
				}
				else
				{
					$grantor = $GLOBALS['phpgw_info']['user']['account_id'];
				}
			}

			$this->set_permission2($values['right'], $process, $grantor, 0);
			$this->set_permission2($values['mask'], $process, $grantor, 1);
			$receipt['message'][] = array('msg' => lang('permissions are updated!'));

			// this feature will probably move into the api as standard
			if($this->acl_app == 'property')
			{
				$cleared = execMethod('property.bocommon.reset_fm_cache_userlist');
				$receipt['message'][] = array('msg' => lang('%1 userlists cleared from cache',$cleared));
			}

			phpgwapi_cache::user_clear('phpgwapi', 'menu', -1);

			return $receipt;
		}


		function get_user_list($type='',$get_grants='')
		{
			if($type == 'groups')
			{
				$check_account_type = array('accounts');
				$acl_account_type = 'accounts';
				$valid_users	= $GLOBALS['phpgw']->acl->get_ids_for_location('run', phpgwapi_acl::READ, $this->acl_app);
			}
			else
			{
				$check_account_type = array('groups','accounts');
				$acl_account_type = 'both';
				$_valid_users	= $GLOBALS['phpgw']->acl->get_user_list_right(phpgwapi_acl::READ, 'run', $this->acl_app);
				$valid_users = array();
				foreach($_valid_users as $_user)
				{
					$valid_users[] = $_user['account_id'];
				}
				unset($_user);
				unset($_valid_users);
			}

			$grantor = -1;
			if($get_grants)
			{
				if($this->granting_group)
				{
					$grantor = $this->granting_group;
				}
				else
				{
					$grantor = $GLOBALS['phpgw_info']['user']['account_id'];
				}
			}

			$right=$this->right;

			$allusers = $GLOBALS['phpgw']->accounts->get_list($type, -1,$this->sort, $this->order, $this->query);

//_debug_array($type);die();
//_debug_array($valid_users);die();
			//$allusers	= array_intersect_key($allusers, $valid_users);

			foreach($allusers as  $user)
			{
				if(!in_array($user->id, $valid_users))
				{
					unset($allusers[$user->id]);
				}
			}
			unset($user);
			reset($allusers);

			$this->total_records = count($allusers);
			$length = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			if ($this->allrows)
			{
				$this->start = 0;
				$length = $this->total_records;
			}

			$allusers = array_slice($allusers, $this->start , $length, true);

			if ( isset($allusers) && is_array($allusers))
			{
				$j=0;
				foreach($allusers as $account)
				{
					$user_list[$j]['account_id'] 		= $account->id;
					$user_list[$j]['account_lid'] 		= $account->lid;
					$user_list[$j]['account_firstname'] = $account->firstname;
					$user_list[$j]['account_lastname'] 	= $account->lastname;

					$this->acl->set_account_id($account->id, true, $this->acl_app, $this->location, $acl_account_type);

					$count_right=count($right);
					for ( $i = 0; $i < $count_right; ++$i )
					{
						if($this->acl->check_rights($this->location, $right[$i],$this->acl_app,$grantor,0,$check_account_type))
						{
							if($this->acl->account_type == 'g')
							{
								$user_list[$j]['right'][$right[$i]] = 'from_group';
							}
							else
							{
								$user_list[$j]['right'][$right[$i]] = 'checked';
							}
							$user_list[$j]['result'][$right[$i]] = 'checked';
						}
						if($this->acl->check_rights($this->location, $right[$i],$this->acl_app,$grantor,1,$check_account_type))
						{
							if($this->acl->account_type == 'g')
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

			return $user_list;
		}

		function get_group_list($format='',$selected='',$start='', $sort='', $order='', $query='',$offset='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('group_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('group_filter'));
					break;
			}

			$groups = $GLOBALS['phpgw']->accounts->get_list('groups', $start, $sort, $order, $query,$offset);

			unset($accounts);
			if (isset($groups) AND is_array($groups))
			{
				foreach($groups as $group)
				{
					if ($group->id==$selected)
					{
						$group_list[] = array
						(
							'id'		=> $group->id,
							'name'		=> $group->firstname,
							'selected'	=> 'selected'
						);
					}
					else
					{
						$group_list[] = array
						(
							'id'		=> $group->id,
							'name'		=> $group->firstname,
						);
					}
				}
			}
			return $group_list;
		}
	}
