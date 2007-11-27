<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage admin
 	* @version $Id: class.boadmin.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
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

		function property_boadmin($session='')
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 			= CreateObject('property.soadmin');
			$this->acl 			= CreateObject('phpgwapi.acl');
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->right		= array(1,2,4,8,16);

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'string');
			$permission	= phpgw::get_var('permission');
	//		$location	= get_var('location',array('POST','GET')); // don't work for some reason...
			$module	= phpgw::get_var('module');
			$granting_group	= phpgw::get_var('granting_group', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$acl_app	= $this->currentapp; //get_var('acl_app',array('POST','GET'));

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
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
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}

			if(isset($acl_app))
			{
				$this->acl_app = $acl_app;
			}

			if ($this->allrows)
			{
				$this->start = -1;
				$this->offset = -1;
			}
		}


		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','fm_admin');

			$this->start			= $data['start'];
			$this->query			= $data['query'];
			$this->filter			= $data['filter'];
			$this->sort				= $data['sort'];
			$this->order			= $data['order'];
			$this->cat_id			= $data['cat_id'];
			$this->location			= $data['location'];
			$this->granting_group	= $data['granting_group'];
			$this->allrows			= $data['allrows'];
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','fm_admin',$data);
			}
		}

		function select_location($format='',$selected='',$grant='')
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

			$locations= $this->so->select_location($grant);

			while (is_array($locations) && list(,$loc) = each($locations))
			{
				$sel_loc = '';
				if ($loc['id']==$selected)
				{
					$sel_loc = 'selected';
				}

				$location_list[] = array
				(
					'id'		=> $loc['id'],
					'descr'		=> $loc['id'] . ' [' . $loc['descr'] . ']',
					'selected'	=> $sel_loc
				);
			}

			for ($i=0;$i<count($location_list);$i++)
			{
				if ($location_list[$i]['selected'] != 'selected')
				{
					unset($location_list[$i]['selected']);
				}
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
			
			return $this->bocommon->select_list($selected,$categories);
		}

		function set_permission2($values,$r_processed, $grantor = 0, $type = 0)
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

				$this->acl->account_id = $user_id;
				$this->acl->read_repository();
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

			$users_at_location = $this->so->get_accounts_at_location($this->acl_app, $this->location, $grantor ,$type);

			if(is_array($user_delete) && count($user_delete)>0)
			{
				while(list(,$user_id) = each($user_delete))
				{
					if(isset($users_at_location[$user_id]) && $users_at_location[$user_id])
					{
						$this->acl->account_id = $user_id;
						$this->acl->read_repository();
						$this->acl->delete($this->acl_app, $this->location, $grantor, $type);
						$this->acl->save_repository($this->acl_app, $this->location);
					}
				}
			}
		}

		function set_permission($values,$r_processed,$set_grant = false,$initials='')
		{
			if($initials)
			{
				$this->so->set_initials($initials);
			}

			$process = explode('_', $r_processed);

			if ( !isset($values['right']) || !is_array($values['right']) )
			{
				$values['right'] = array();
			}

			if ( !isset($values['mask']) || !is_array($values['mask']) )
			{
				$values['mask'] = array();
			}

			$grantor = 0;
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
			$cleared = $this->bocommon->reset_fm_cache_userlist();
			$receipt['message'][] = array('msg' => lang('permissions are updated!'));
			$receipt['message'][] = array('msg' => lang('%1 userlists cleared from cache',$cleared));
			return $receipt;
		}

		function get_user_list($type='',$get_grants='')
		{
			if($type == 'groups')
			{
				$check_account_type = array('accounts');
			}
			else
			{
				$check_account_type = array('groups','accounts');
			}

			$grantor = 0;
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

			if($this->location == '.invoice')
			{
				$this->right		= array(1,2,4,8,16,32,64,128);
			}

			$right=$this->right;

			$offset = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			if ($this->allrows)
			{
				$this->start = -1;
				$offset = -1;
			}
			
			$allusers = $GLOBALS['phpgw']->accounts->get_list($type, $this->start,$this->sort, $this->order, $this->query, $offset);

			if ( isset($allusers) && is_array($allusers))
			{
				$j=0;
				foreach($allusers as $account)
				{
					$user_list[$j]['account_id'] 			= $account['account_id'];
					$user_list[$j]['account_lid'] 			= $account['account_lid'];
					$user_list[$j]['account_firstname'] 	= $account['account_firstname'];
					$user_list[$j]['account_lastname'] 		= $account['account_lastname'];
					
					if($this->location == '.invoice')
					{
						$user_list[$j]['initials']			= $this->so->get_initials($account['account_id']);
					}
										
					$this->acl->account_id=$account['account_id'];

					$this->acl->read_repository();

					$count_right=count($right);
					
					for ( $i = 0; $i < $count_right; ++$i )
					{
						if($this->acl->check_brutto($this->location, $right[$i],$this->acl_app,$grantor,0,$check_account_type))
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
						if($this->acl->check_brutto($this->location, $right[$i],$this->acl_app,$grantor,1,$check_account_type))
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
			$this->total_records = $GLOBALS['phpgw']->accounts->total;
//_debug_array($user_list);
			return $user_list;
		}

		function read_fm_id()
		{

			$fm_ids = $this->so->read_fm_id();
			return $fm_ids;

		}
		function edit_id($values='')
		{
			return $this->so->edit_id($values);

		}
	}
?>
