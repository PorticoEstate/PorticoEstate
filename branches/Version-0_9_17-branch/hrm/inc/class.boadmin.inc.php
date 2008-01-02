<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage admin
 	* @version $Id: class.boadmin.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class hrm_boadmin
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		function hrm_boadmin($session='')
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 			= CreateObject('hrm.soadmin');
			$this->catbo = createobject('phpgwapi.categories');

			$this->acl			= CreateObject('phpgwapi.acl');
			$this->right		= array(1,2,4,8,16);

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$acl_app	= phpgw::get_var('acl_app');
			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$permission	= phpgw::get_var('permission');
			$module	= phpgw::get_var('module');
			$granting_group	= phpgw::get_var('granting_group', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if($acl_app)
			{
				$this->acl_app = $acl_app;
			}
			else
			{
				$this->acl_app = 'hrm';
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
			$this->allrows	= $data['allrows'];
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

			$i = count($locations);
			$api_cats = $this->catbo->return_array('all', 0, True, False, False, 'cat_name', True);
			if ( is_array($api_cats) )
			{
				foreach ($api_cats as $cat)
				{
					$locations[$i]['id']	= 'C' . $cat['id'];
					$locations[$i]['descr']	= $cat['name'];
					$i++;
				}
			}
			unset($api_cats);

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

			while (is_array($categories) && list(,$category) = each($categories))
			{
				$sel_category = '';
				if ($category['id']==$selected)
				{
					$sel_category = 'selected';
				}

				$category_list[] = array
				(
					'cat_id'	=> $category['id'],
					'name'		=> $category['name'],
					'selected'	=> $sel_category
				);
			}

			for ($i=0;$i<count($category_list);$i++)
			{
				if ($category_list[$i]['selected'] != 'selected')
				{
					unset($category_list[$i]['selected']);
				}
			}

			return $category_list;
		}


		function set_permission2($values,$r_processed, $grantor = False, $type = False)
		{
			@reset($values);
			$totalacl = array();
			while(list($rowinfo,$perm) = each($values))
			{
				list($user_id,$rights) = split('_',$rowinfo);
				$totalacl[$user_id] += $rights;
			}
			@reset($totalacl);
			while(list($user_id,$rights) = @each($totalacl))
			{
				$user_checked[]=$user_id;

				$this->acl->account_id=$user_id;
				$this->acl->read_repository();
				$this->acl->delete($appname = $this->acl_app, $this->location,$grantor,$type);
				$this->acl->add($appname = $this->acl_app, $this->location, $rights,$grantor,$type);
				$this->acl->save_repository();
			}

			if(is_array($r_processed) && is_array($user_checked))
			{
				$user_delete 	= array_diff($r_processed, $user_checked);
			}
			else
			{
				$user_delete	= $r_processed;
			}
			if(is_array($user_delete) && count($user_delete)>0)
			{
				while(list(,$user_id) = each($user_delete))
				{
					$this->acl->account_id=$user_id;
					$this->acl->read_repository();
					$this->acl->delete($appname = $this->acl_app, $this->location,$grantor,$type);
					$this->acl->save_repository();
				}
			}
		}

		function set_permission($values,$r_processed,$set_grant = '')
		{

			$r_processed=explode("_",$r_processed);

			if(!$values['right'])
			{
				$values['right'] = array();
			}
			if(!$values['mask'])
			{
				$values['mask'] = array();
			}

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

			$this->set_permission2($values['right'],$r_processed,$grantor,0);
			$this->set_permission2($values['mask'],$r_processed,$grantor,1);

			$receipt['message'][] = array('msg' => lang('permissions are updated!'));
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

			if ($this->allrows)
			{
				$this->start = -1;
				$offset = -1;
			}
			
			$allusers = $GLOBALS['phpgw']->accounts->get_list($type, $this->start,$this->sort, $this->order, $this->query, $offset);

			if (isSet($allusers) AND is_array($allusers))
			{
				$j=0;
				foreach($allusers as $account)
				{
					$user_list[$j]['account_id'] 		= $account['account_id'];
					$user_list[$j]['account_lid'] 		= $account['account_lid'];
					$user_list[$j]['account_firstname'] 	= $account['account_firstname'];
					$user_list[$j]['account_lastname'] 	= $account['account_lastname'];

					$this->acl->account_id=$account['account_id'];

					$this->acl->read_repository();

					$count_right=count($right);
					for ($i=0;$i<$count_right;$i++)
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

			return $user_list;
		}
	}
