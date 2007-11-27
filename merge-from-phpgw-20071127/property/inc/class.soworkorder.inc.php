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
	* @subpackage project
 	* @version $Id: class.soworkorder.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soworkorder
	{

		function property_soworkorder()
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();
			$this->join			= $this->bocommon->join;

			$this->left_join	= $this->bocommon->left_join;

			$this->like			= $this->bocommon->like;

		//	$this->grants 		= $GLOBALS['phpgw']->session->appsession('grants_project',$this->currentapp);
		//	if(!$this->grants)
			{
				$this->acl 		= CreateObject('phpgwapi.acl');
				$this->grants		= $this->acl->get_grants($this->currentapp,'.project');
		//		$GLOBALS['phpgw']->session->appsession('grants_project',$this->currentapp,$this->grants);
			}
		}

		function next_id()
		{
			$this->db->query("select value from fm_idgenerator where name = 'workorder'");
			$this->db->next_record();
			$id = $this->db->f('value')+1;
			return $id;
		}


		function read_single_project_category($id='')
		{
			$this->db->query("SELECT descr FROM fm_workorder_category where id='$id' ");
			$this->db->next_record();
			return $this->db->f('descr');
		}

		function get_b_account_name($id='')
		{
			$this->db->query("SELECT descr FROM fm_b_account where id='$id' ");
			$this->db->next_record();
			return $this->db->f('descr');
		}

		function select_status_list()
		{
			$this->db->query("SELECT id, descr FROM fm_workorder_status ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$status_entries[$i]['id']				= $this->db->f('id');
				$status_entries[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $status_entries;
		}

		function select_branch_list()
		{
			$this->db->query("SELECT id, descr FROM fm_branch ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$branch_entries[$i]['id']				= $this->db->f('id');
				$branch_entries[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $branch_entries;
		}

		function select_key_location_list()
		{
			$this->db->query("SELECT id, descr FROM fm_key_loc ORDER BY descr ");

			$i = 0;
			while ($this->db->next_record())
			{
				$key_location_entries[$i]['id']				= $this->db->f('id');
				$key_location_entries[$i]['name']			= stripslashes($this->db->f('descr'));
				$i++;
			}
			return (isset($key_location_entries)?$key_location_entries:'');
		}

		function read($data)
		{

			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$filter	= $data['filter']?$data['filter']:'all';
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$status_id = (isset($data['status_id'])?$data['status_id']:0);
				$search_vendor = (isset($data['search_vendor'])?$data['search_vendor']:'');
				$start_date = (isset($data['start_date'])?$data['start_date']:'');
				$end_date = (isset($data['end_date'])?$data['end_date']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$wo_hour_cat_id = (isset($data['wo_hour_cat_id'])?$data['wo_hour_cat_id']:'');
				$b_group = (isset($data['b_group'])?$data['b_group']:'');	
				$paid = (isset($data['paid'])?$data['paid']:'');
			}


			$sql = $this->bocommon->fm_cache('sql_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
//echo $sql;
			if(!$sql)
			{
				$entity_table = 'fm_project';

				$cols .= $entity_table . '.location_code';
				$cols_return[] = 'location_code';

				$cols .= ",$entity_table.id as project_id";
				$cols_return[] 				= 'project_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'project_id';
				$uicols['descr'][]			= lang('Project');
				$uicols['statustext'][]		= lang('Project ID');

				$cols .= ",fm_workorder.id as workorder_id";
				$cols_return[] 				= 'workorder_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'workorder_id';
				$uicols['descr'][]			= lang('Workorder');
				$uicols['statustext'][]		= lang('Workorder ID');

				$cols .= ",fm_workorder.title as title";
				$cols_return[] 				= 'title';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'title';
				$uicols['descr'][]			= lang('Title');
				$uicols['statustext'][]		= lang('Workorder title');

				$cols .= ",fm_workorder.status as status";
				$cols_return[] 				= 'status';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'status';
				$uicols['descr'][]			= lang('Status');
				$uicols['statustext'][]		= lang('Workorder status');

				$cols .= ",fm_workorder.entry_date as entry_date";
				$cols_return[] 				= 'entry_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'entry_date';
				$uicols['descr'][]			= lang('Entry date');
				$uicols['statustext'][]		= lang('Workorder entry date');

				$cols .= ",phpgw_accounts.account_lid as user_lid";
				$cols_return[] 				= 'user_lid';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'user_lid';
				$uicols['descr'][]			= lang('User');
				$uicols['statustext'][]		= lang('Workorder User');

				$cols .= ',fm_workorder.vendor_id';
				$cols_return[] = 'vendor_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'vendor_id';
				$uicols['descr'][]			= lang('Vendor ID');
				$uicols['statustext'][]		= lang('Vendor ID');

				$cols .= ",fm_project.user_id as project_owner";

				$joinmethod .= " $this->join  fm_workorder ON ($entity_table.id = fm_workorder.project_id) $this->join  phpgw_accounts ON (fm_workorder.user_id = phpgw_accounts.account_id))";
				$paranthesis .='(';

				$cols .= ',fm_vendor.org_name';
				$cols_return[] = 'org_name';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'org_name';
				$uicols['descr'][]			= lang('Vendor name');
				$uicols['statustext'][]		= lang('Vendor name');

				$cols .= ',fm_workorder.combined_cost';
				$cols_return[] = 'combined_cost';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'combined_cost';
				$uicols['descr'][]			= lang('Cost');
				$uicols['statustext'][]		= lang('Cost - either budget or calculation');

				$cols .= ',fm_workorder.act_mtrl_cost + fm_workorder.act_vendor_cost as actual_cost';
				$cols_return[] = 'actual_cost';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'actual_cost';
				$uicols['descr'][]			= lang('Actual cost');
				$uicols['statustext'][]		= lang('Actual cost - paid so far');

				$joinmethod .= " $this->left_join  fm_vendor ON (fm_workorder.vendor_id = fm_vendor.id))";
				$paranthesis .='(';

				//----- wo_hour_status

				if($wo_hour_cat_id)
				{
					$joinmethod .= " $this->join fm_wo_hours ON (fm_workorder.id = fm_wo_hours.workorder_id))";
					$paranthesis .='(';

					$joinmethod .= " $this->join fm_wo_hours_category ON (fm_wo_hours.category = fm_wo_hours_category.id))";
					$paranthesis .='(';
				}

				//----- wo_hour_status


				//----- b_group

				if($b_group)
				{
					$joinmethod .= " $this->join fm_b_account ON (fm_workorder.account_id =fm_b_account.id))"; 
					$paranthesis .='(';
				}

				//----- b_group


				$sql	= $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols'=>$cols,'cols_return'=>$cols_return,
									'uicols'=>$uicols,'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,'query'=>$query,'force_location'=>true));

				$this->bocommon->fm_cache('sql_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group,$sql);

				$this->uicols		= $this->bocommon->uicols;
				$cols_return		= $this->bocommon->cols_return;
				$type_id			= $this->bocommon->type_id;
//				$this->cols_extra	= $this->bocommon->cols_extra;

				$this->bocommon->fm_cache('uicols_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group,$this->uicols);
				$this->bocommon->fm_cache('cols_return_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group,$cols_return);
				$this->bocommon->fm_cache('type_id_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group,$type_id);
//				$this->bocommon->fm_cache('cols_extra_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group,$this->cols_extra);

			}
			else
			{
				$this->uicols		= $this->bocommon->fm_cache('uicols_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
				$cols_return		= $this->bocommon->fm_cache('cols_return_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
				$type_id			= $this->bocommon->fm_cache('type_id_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
//				$this->cols_extra	= $this->bocommon->fm_cache('cols_extra_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_workorder.id DESC';
			}

			$where= 'WHERE';

			$filtermethod = '';
			
			if ($cat_id > 0)
			{
				$filtermethod .= " $where fm_project.category=$cat_id ";
				$where= 'AND';
			}

			if ($status_id)
			{
				$filtermethod .= " $where fm_workorder.status='$status_id' ";
				$where= 'AND';
			}

			$group_method = '';
			if($wo_hour_cat_id)
			{
				$filtermethod .= " $where fm_wo_hours_category.id=$wo_hour_cat_id ";
				$where= 'AND';
				$group_method = " group by fm_project.id,fm_project.location_code,fm_workorder.id,workorder_id,title,fm_workorder.status,fm_workorder.entry_date,user_lid,fm_workorder.vendor_id,project_owner,fm_project.address,fm_vendor.org_name,fm_workorder.combined_cost,fm_workorder.act_mtrl_cost,fm_workorder.act_vendor_cost";
			}

			if ($b_group)
			{
				$filtermethod .= " $where fm_b_account.category='$b_group' ";
				$where= 'AND';
			}

			if ($paid)
			{
				/* 0 => cancelled, 1 => obligation , 2 => paid */
				$filtermethod .= " $where fm_workorder.paid = $paid AND vendor_id > 0";
				$where= 'AND';
			}

			if ($filter=='all')
			{
				if (is_array($this->grants))
				{
					$grants = $this->grants;
					while (list($user) = each($grants))
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$filtermethod .= " $where (fm_project.access='public' AND fm_project.user_id IN(" . implode(',',$public_user_list) . "))";
					$where= 'AND';
				}
			}
			else
			{
				$filtermethod .= " $where fm_workorder.user_id=$filter ";
				$where= 'AND';
			}

			if ($start_date)
			{
				$filtermethod .= " $where fm_workorder.start_date >= $start_date AND fm_workorder.start_date <= $end_date ";
				$where= 'AND';
			}

			$querymethod = '';
			if($query)
			{
				$query = str_replace(",",'.',$query);
				if(stristr($query, '.'))
				{
					$query=explode(".",$query);
					$querymethod = " $where (fm_project.loc1='" . $query[0] . "' AND fm_project.loc".$type_id."='" . $query[1] . "')";
				}
				else
				{
					$query = ereg_replace("'",'',$query);
					$query = ereg_replace('"','',$query);

					$querymethod = " $where (fm_workorder.title $this->like '%$query%' or fm_workorder.descr $this->like '%$query%' or fm_project.address $this->like '%$query%' or fm_project.location_code $this->like '%$query%' or fm_workorder.id $this->like '%$query%')";
				}
				$where= 'AND';
			}

			$querymethod_vendor = '';
			if($search_vendor)
			{
				if((int)$search_vendor>0)
				{
					$querymethod_vendor = " $where fm_workorder.vendor_id=" .(int)$search_vendor ;
				}
				else
				{
					$querymethod_vendor = " $where  fm_vendor.org_name $this->like '%$search_vendor%'";
				}
			}

			$sql .= " $filtermethod $querymethod $querymethod_vendor";
//echo $sql;

			if($GLOBALS['phpgw_info']['server']['db_type']=='postgres')
			{
				$sql2 = 'SELECT count(*) FROM (SELECT fm_workorder.id ' . substr($sql,strripos($sql,'from'))  . ') as cnt';
				$this->db->query($sql2,__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f(0);
			}
			else
			{
				$sql2 = 'SELECT fm_workorder.id ' . substr($sql,strripos($sql,'from'));
				$this->db->query($sql2,__LINE__,__FILE__);
				$this->total_records = $this->db->num_rows();
			}

			$sql .= " $group_method";

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$count_cols_return=count($cols_return);
			$j=0;
			while ($this->db->next_record())
			{
				for ($i=0;$i<$count_cols_return;$i++)
				{
					$workorder_list[$j][$cols_return[$i]] = $this->db->f($cols_return[$i]);
					$workorder_list[$j]['grants'] = (int)$this->grants[$this->db->f('project_owner')];
				}

				$location_code=	$this->db->f('location_code');
				$location = split('-',$location_code);
				$count_location =count($location);
				for ($m=0;$m<$count_location;$m++)
				{
					$workorder_list[$j]['loc' . ($m+1)] = $location[$m];
					$workorder_list[$j]['query_location']['loc' . ($m+1)]=implode("-", array_slice($location, 0, ($m+1)));
				}

				$j++;
			}

			return (isset($workorder_list)?$workorder_list:array());
		}

		function read_single($workorder_id)
		{
			$sql = "SELECT fm_workorder.*, fm_chapter.descr as chapter ,fm_project.user_id from fm_workorder $this->join fm_project on fm_workorder.project_id=fm_project.id  $this->left_join fm_chapter on "
				. " fm_workorder.chapter_id = fm_chapter.id where fm_workorder.id=$workorder_id";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$workorder['workorder_id']		= $this->db->f('id');
				$workorder['project_id']		= $this->db->f('project_id');
				$workorder['title']				= $this->db->f('title');
				$workorder['name']				= $this->db->f('name');
				$workorder['key_fetch']			= $this->db->f('key_fetch');
				$workorder['key_deliver']		= $this->db->f('key_deliver');
				$workorder['key_responsible']	= $this->db->f('key_responsible');
				$workorder['charge_tenant']		= $this->db->f('charge_tenant');
				$workorder['descr']				= stripslashes($this->db->f('descr'));
				$workorder['status']			= $this->db->f('status');
				$workorder['budget']			= (int)$this->db->f('budget');
				$workorder['calculation']			= $this->db->f('calculation')>0?($this->db->f('calculation')*(1+$this->db->f('addition')/100))+$this->db->f('rig_addition'):0;
				$workorder['b_account_id']			= (int)$this->db->f('account_id');
				$workorder['addition_percentage']	= (int)$this->db->f('addition');
				$workorder['addition_rs']			= (int)$this->db->f('rig_addition');
				$workorder['act_mtrl_cost']			= $this->db->f('act_mtrl_cost');
				$workorder['act_vendor_cost']		= $this->db->f('act_vendor_cost');
				$workorder['user_id']				= $this->db->f('user_id');
				$workorder['vendor_id']			= $this->db->f('vendor_id');
				$workorder['coordinator']		= $this->db->f('coordinator');
				$workorder['access']			= $this->db->f('access');
				$workorder['start_date']		= $this->db->f('start_date');
				$workorder['end_date']			= $this->db->f('end_date');
				$workorder['cat_id']			= $this->db->f('category');
				$workorder['chapter_id']		= $this->db->f('chapter_id');
				$workorder['chapter']			= $this->db->f('chapter');
				$workorder['deviation']			= $this->db->f('deviation');
				$workorder['grants'] 				= (int)$this->grants[$this->db->f('user_id')];
			}

//_debug_array($workorder);
				return $workorder;
		}


		function project_budget_from_workorder($project_id = '')
		{
			$this->db->query("select budget, id as workorder_id from fm_workorder where project_id='$project_id'");
			while ($this->db->next_record())
			{
				$budget[] = array(
					'workorder_id'	=> $this->db->f('workorder_id'),
					'budget'	=> sprintf("%01.2f",$this->db->f('budget'))
					);
			}
			return $budget;
		}

		function branch_p_list($project_id = '')
		{

			$this->db2->query("SELECT branch_id from fm_projectbranch WHERE project_id='$project_id' ",__LINE__,__FILE__);
			while ($this->db2->next_record())
			{
				$selected[] = array('branch_id' => $this->db2->f('branch_id'));
			}

			return $selected;
		}

		function increment_workorder_id()
		{
			$this->db->query("update fm_idgenerator set value = value + 1 where name = 'workorder'");
		}

		function add($workorder)
		{
			$historylog	= CreateObject('property.historylog','workorder');
			$workorder['descr'] = $this->db->db_addslashes($workorder['descr']);
			$workorder['title'] = $this->db->db_addslashes($workorder['title']);

			if(!$workorder['workorder_num'])
			{
				$workorder['workorder_num'] = $workorder['workorder_id'];
			}
			
			$values= array(
				$workorder['workorder_id'],
				$workorder['workorder_num'],
				$workorder['project_id'],
				$workorder['title'],
				'public',
				time(),
				$workorder['start_date'],
				$workorder['end_date'],
				$workorder['status'],
				$workorder['descr'],
				$workorder['budget'],
				$workorder['budget'],
				$workorder['b_account_id'],
				$workorder['addition_rs'],
				$workorder['addition_percentage'],
				$workorder['key_deliver'],
				$workorder['key_fetch'],
				$workorder['vendor_id'],
				$workorder['charge_tenant'],
				$this->account);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_workorder (id,num,project_id,title,access,entry_date,start_date,end_date,status,"
				. "descr,budget,combined_cost,account_id,rig_addition,addition,key_deliver,key_fetch,vendor_id,charge_tenant,user_id) "
				. "VALUES ( $values )",__LINE__,__FILE__);

			$this->db->query("INSERT INTO fm_orders (id,type) VALUES (" . $workorder['workorder_id'] . ",'workorder')");

/*
			if($workorder['charge_tenant'])
			{
				$this->db->query("UPDATE fm_project set charge_tenant = 1 WHERE id =" . $workorder['project_id']);
			}
*/
			if($this->db->transaction_commit())
			{
				$this->increment_workorder_id();
				$historylog->add('SO',$workorder['workorder_id'],$workorder['status']);
				if ($workorder['remark'])
				{
					$historylog->add('RM',$workorder['workorder_id'],$workorder['remark']);
				}

				$receipt['message'][] = array('msg'=>lang('workorder %1 has been saved',$workorder['workorder_id']));
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('the workorder has not been saved'));
			}
			return $receipt;
		}

		function edit($workorder)
		{
			$historylog	= CreateObject('property.historylog','workorder');
			$workorder['descr'] = $this->db->db_addslashes($workorder['descr']);
			$workorder['title'] = $this->db->db_addslashes($workorder['title']);

			$this->db->query("SELECT status,budget,calculation FROM fm_workorder where id='" .$workorder['workorder_id']."'",__LINE__,__FILE__);
			$this->db->next_record();

			if ($this->db->f('calculation') > 0)
			{
				$config	= CreateObject('phpgwapi.config');
				$config->read_repository();
				$tax = 1+(($config->config_data['fm_tax'])/100);
				$combined_cost = $this->db->f('calculation')* $tax;
			}
			else
			{
				$combined_cost = $workorder['budget'];
			}

			$old_status = $this->db->f('status');
			$old_budget = $this->db->f('budget');

			$this->db->query("SELECT bilagsnr FROM fm_ecobilag where pmwrkord_code ='" .$workorder['workorder_id']."'",__LINE__,__FILE__);
			$this->db->next_record();

			if($this->db->f('bilagsnr'))
			{
				$paid = 1;
			}

			$this->db->query("SELECT bilagsnr FROM fm_ecobilagoverf where pmwrkord_code ='" .$workorder['workorder_id']."'",__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('bilagsnr'))
			{
				$paid = 2;
			}


			$value_set=array(
				'title'			=> $workorder['title'],
				'status'		=> $workorder['status'],
				'start_date'		=> $workorder['start_date'],
				'end_date'		=> $workorder['end_date'],
				'descr'			=> $workorder['descr'],
				'budget'		=> (int)$workorder['budget'],
				'combined_cost'	=> $combined_cost,
				'key_deliver'		=> $workorder['key_deliver'],
				'key_fetch'		=> $workorder['key_fetch'],
				'account_id'		=> $workorder['b_account_id'],
				'rig_addition'		=> $workorder['addition_rs'],
				'addition'		=> $workorder['addition_percentage'],
				'charge_tenant'		=> $workorder['charge_tenant'],
				'vendor_id'		=> $workorder['vendor_id']
				);

			if($workorder['status'] == 'closed')
			{
				$value_set['paid'] = $paid = (isset($paid)?$paid:0);
			}
			
			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_workorder set $value_set WHERE id=" . $workorder['workorder_id'] ,__LINE__,__FILE__);

/*			if($workorder['charge_tenant'])
			{
				$this->db->query("UPDATE fm_project set charge_tenant = 1 WHERE id =" . $workorder['project_id']);
			}
*/
			if($this->db->transaction_commit())
			{
				if ($old_status != $workorder['status'])
				{
					$historylog->add('S',$workorder['workorder_id'],$workorder['status']);
					$receipt['notice_owner'][]=lang('Status changed') . ': ' . $workorder['status'];
				}
				elseif($workorder['confirm_status'])
				{
					$historylog->add('SC',$workorder['workorder_id'],$workorder['status']);
					$receipt['notice_owner'][]=lang('Status confirmed') . ': ' . $workorder['status'];
				}

				if ($old_budget != $workorder['budget'])
				{
					$historylog->add('B',$workorder['workorder_id'],$workorder['budget']);
				}

				if ($workorder['remark'])
				{
					$historylog->add('RM',$workorder['workorder_id'],$workorder['remark']);
				}

				$receipt['message'][] = array('msg'=>lang('workorder %1 has been edited',$workorder['workorder_id']));
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('workorder %1 has not been edited',$workorder['workorder_id']));
			}

			return $receipt;
		}

		function delete($workorder_id )
		{
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_workorder WHERE id='" . $workorder_id . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_workorder_history  WHERE  history_record_id='" . $workorder_id   . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_wo_hours WHERE workorder_id='" . $workorder_id   . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_orders WHERE id='" . $workorder_id . "'",__LINE__,__FILE__);
			$this->db->transaction_commit();

		}
	}
?>
