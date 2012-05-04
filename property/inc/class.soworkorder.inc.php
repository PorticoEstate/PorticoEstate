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
 	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */

	class property_soworkorder
	{
		var $total_records = 0;
		protected $global_lock = false;

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->db2			= clone($this->db);
			$this->like 		= & $this->db->like;
			$this->join 		= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->interlink 	= CreateObject('property.interlink');
		//	$this->grants 		= $GLOBALS['phpgw']->session->appsession('grants_project','property');
		//	if(!$this->grants)
			{
				$this->acl 		= & $GLOBALS['phpgw']->acl;
				$this->acl->set_account_id($this->account);
				$this->grants		= $this->acl->get_grants('property','.project');
		//		$GLOBALS['phpgw']->session->appsession('grants_project','property',$this->grants);
			}
		}

		function next_id()
		{
			$name = 'workorder';
			$now = time();
			$this->db->query("SELECT value FROM fm_idgenerator WHERE name = '{$name}' AND start_date < {$now} ORDER BY start_date DESC");
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
			$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$filter			= $data['filter'] ? (int)$data['filter'] : 0;
			$query			= isset($data['query']) ? $data['query'] : '';
			$sort			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order			= isset($data['order']) ? $data['order'] : '';
			$cat_id			= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
			$status_id		= isset($data['status_id']) && $data['status_id'] ? $data['status_id'] : 'open';
			$start_date		= isset($data['start_date']) && $data['start_date'] ? (int)$data['start_date'] : 0;
			$end_date		= isset($data['end_date']) && $data['end_date'] ? (int)$data['end_date'] : 0;
			$allrows		= isset($data['allrows']) ? $data['allrows'] : '';
			$wo_hour_cat_id	= isset($data['wo_hour_cat_id']) ? $data['wo_hour_cat_id'] : '';
			$b_group		= isset($data['b_group']) ? $data['b_group'] : '';
			$paid			= isset($data['paid']) ? $data['paid'] : '';
			$b_account		= isset($data['b_account']) ? $data['b_account'] : '';
			$district_id	= isset($data['district_id']) ? $data['district_id'] : '';
			$dry_run		= isset($data['dry_run']) ? $data['dry_run'] : '';
			$criteria		= isset($data['criteria']) && $data['criteria'] ? $data['criteria'] : array();

			$GLOBALS['phpgw']->config->read();
			$sql = $this->bocommon->fm_cache('sql_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
			//echo $sql;
			if(!$sql)
			{
				$entity_table = 'fm_project';

				$cols = "$entity_table.id as project_id";
				$cols_return[] 				= 'project_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'project_id';
				$uicols['descr'][]			= lang('Project');
				$uicols['statustext'][]		= lang('Project ID');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= 'linktToProject';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= true;

				$cols .= ",fm_workorder.id as workorder_id";
				$cols_return[] 				= 'workorder_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'workorder_id';
				$uicols['descr'][]			= lang('Workorder');
				$uicols['statustext'][]		= lang('Workorder ID');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= 'linktToOrder';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= true;

				$cols .= ",fm_workorder.title as title";
				$cols_return[] 				= 'title';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'title';
				$uicols['descr'][]			= lang('Title');
				$uicols['statustext'][]		= lang('Workorder title');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= '';

				$cols .= ",fm_workorder_status.descr as status";
				$cols_return[] 				= 'status';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'status';
				$uicols['descr'][]			= lang('Status');
				$uicols['statustext'][]		= lang('Workorder status');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= '';

				$cols .= ",fm_workorder.entry_date as entry_date";
				$cols_return[] 				= 'entry_date';
				$cols .= ",fm_workorder.start_date as start_date";
				$cols_return[] 				= 'start_date';
				$cols .= ",fm_workorder.end_date as end_date";
				$cols_return[] 				= 'end_date';
				$cols.= ",fm_workorder.ecodimb";
				$cols_return[] 				= 'ecodimb';
				$cols.= ",fm_workorder.contract_sum";
				$cols_return[] 				= 'contract_sum';
/*
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'entry_date';
				$uicols['descr'][]			= lang('Entry date');
				$uicols['statustext'][]		= lang('Workorder entry date');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
*/
				$cols .= ",phpgw_accounts.account_lid as user_lid";
				$cols_return[] 				= 'user_lid';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'user_lid';
				$uicols['descr'][]			= lang('User');
				$uicols['statustext'][]		= lang('Workorder User');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= '';

				$cols .= ',fm_workorder.vendor_id';
				$cols_return[] = 'vendor_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'vendor_id';
				$uicols['descr'][]			= lang('Vendor ID');
				$uicols['statustext'][]		= lang('Vendor ID');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= '';

				$cols.= ",loc1_name";
//				$cols_return[] 				= 'loc1_name';
/*
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'loc1_name';
				$uicols['descr'][]			= '';
				$uicols['statustext'][]		= '';
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= '';
*/
				$cols .= ",fm_project.user_id as project_owner";

				$joinmethod .= "{$this->join} fm_workorder ON ({$entity_table}.id = fm_workorder.project_id) {$this->join} phpgw_accounts ON (fm_workorder.user_id = phpgw_accounts.account_id))";
				$paranthesis .='(';

				$joinmethod .= " {$this->join} fm_workorder_status ON (fm_workorder.status = fm_workorder_status.id))";
				$paranthesis .='(';

				$cols .= ',fm_vendor.org_name';
				$cols_return[] = 'org_name';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'org_name';
				$uicols['descr'][]			= lang('Vendor name');
				$uicols['statustext'][]		= lang('Vendor name');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= '';

				$cols .= ',fm_workorder.combined_cost';
				$cols_return[] = 'combined_cost';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'combined_cost';
				$uicols['descr'][]			= lang('Cost');
				$uicols['statustext'][]		= lang('Cost - either budget or calculation');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= 'myFormatCount2';
				$uicols['classname'][]		= 'rightClasss';
				$uicols['sortable'][]		= true;

				$cols .= ',fm_workorder.actual_cost';
				$cols_return[] = 'actual_cost';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'actual_cost';
				$uicols['descr'][]			= lang('Actual cost');
				$uicols['statustext'][]		= lang('Actual cost - paid so far');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= 'myFormatCount2';
				$uicols['classname'][]		= 'rightClasss';
				$uicols['sortable'][]		= true;

				$joinmethod .= " {$this->left_join} fm_vendor ON (fm_workorder.vendor_id = fm_vendor.id))";
				$paranthesis .='(';

				//----- wo_hour_status

				if($wo_hour_cat_id)
				{
					$joinmethod .= " {$this->join} fm_wo_hours ON (fm_workorder.id = fm_wo_hours.workorder_id))";
					$paranthesis .='(';

					$joinmethod .= " {$this->join} fm_wo_hours_category ON (fm_wo_hours.category = fm_wo_hours_category.id))";
					$paranthesis .='(';
				}

				//----- wo_hour_status


				//----- b_group

//				if($b_group)
				{
					$joinmethod .= " {$this->join} fm_b_account ON (fm_workorder.account_id =fm_b_account.id))";
					$paranthesis .='(';
				}

				//----- b_group


				$cols_return[] = 'location_code';
				$cols_return[] = 'billable_hours';
				$cols .= ',fm_workorder.billable_hours';
				$no_address = false;
				if(isset($GLOBALS['phpgw']->config->config_data['location_at_workorder']) && $GLOBALS['phpgw']->config->config_data['location_at_workorder'])
				{
					$no_address = true;
					$cols .= ',fm_workorder.location_code';
					$cols .= ',fm_workorder.address';
					$cols_return[] 				= 'address';
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= 'address';
					$uicols['descr'][]			= lang('address');
					$uicols['statustext'][]		= lang('address');
					$uicols['exchange'][]		= false;
					$uicols['align'][] 			= '';
					$uicols['datatype'][]		= '';
					$uicols['formatter'][]		= '';
					$uicols['classname'][]		= '';
					$uicols['sortable'][]		= true;
				}
				else
				{
					$cols .= ",{$entity_table}.location_code";
				}

				$sql	= $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols'=>$cols,'cols_return'=>$cols_return,
					'uicols'=>$uicols,'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,'query'=>$query,
					'force_location'=>true, 'no_address' => $no_address));

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

			if($dry_run)
			{
				return array();
			}

			$location_table = 'fm_project';
			if(isset($GLOBALS['phpgw']->config->config_data['location_at_workorder']) && $GLOBALS['phpgw']->config->config_data['location_at_workorder'])
			{
				$location_table = 'fm_workorder';
			}

			$order_field = '';
			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
				switch($order)
				{
					case 'workorder_id':
	//					$ordermethod = " ORDER BY fm_workorder.project_id {$sort},fm_workorder.id {$sort}";	
						$ordermethod = " ORDER BY fm_workorder.id {$sort}";
						break;
					case 'actual_cost':
						$order_field = ',fm_workorder.actual_cost';
						break;
					case 'address':
						if(isset($GLOBALS['phpgw']->config->config_data['location_at_workorder']) && $GLOBALS['phpgw']->config->config_data['location_at_workorder'])
						{
							$order_field = ", fm_workorder.address";
						}
						else
						{
							$order_field = ", fm_project.address";
						}
						break;
					case 'entry_date':
						$order_field = ", fm_workorder.entry_date";
						break;
					case 'start_date':
						$order_field = ", fm_workorder.start_date";
						break;
					case 'end_date':
						$order_field = ", fm_workorder.end_date";
						break;
					case 'ecodimb':
						$order_field = ", fm_workorder.ecodimb";
						break;
					default:
						$order_field = ", {$order}";
				}
			}
			else
			{
	//			$ordermethod = ' ORDER BY fm_workorder.project_id DESC,fm_workorder.id DESC';
				$ordermethod = ' ORDER BY fm_workorder.id DESC';
			}
//_debug_array($order_field);die;
			$where= 'WHERE';

			$filtermethod = '';


			if(isset($GLOBALS['phpgw']->config->config_data['acl_at_location']) && $GLOBALS['phpgw']->config->config_data['acl_at_location'])
			{
				$access_location = $this->bocommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod = " WHERE fm_project.loc1 in ('" . implode("','", $access_location) . "')";
				$where= 'AND';
			}

			if ($cat_id > 0)
			{
				$cats	= CreateObject('phpgwapi.categories', -1,  'property', '.project');
				$cats->supress_info	= true;
				$cat_list_project	= $cats->return_sorted_array(0, false, '', '', '', false, $cat_id, false);
				$cat_filter = array($cat_id);
				foreach ($cat_list_project as $_category)
				{
					$cat_filter[] = $_category['id'];
				}
				$filtermethod .= " {$where} fm_project.category IN (" .  implode(',', $cat_filter) .')';

				$where= 'AND';
			}

			if ($status_id && $status_id != 'all')
			{

				if($status_id == 'open')
				{
					$filtermethod .= " $where fm_workorder_status.closed IS NULL"; 

/*					$_status_filter = array();
					$this->db->query("SELECT * FROM fm_workorder_status WHERE closed IS NULL");
					$this->db->query("SELECT * FROM fm_workorder_status WHERE delivered IS NULL AND closed IS NULL");
					while($this->db->next_record())
					{
						$_status_filter[] = $this->db->f('id');
					}
					$filtermethod .= " $where fm_workorder.status IN ('" . implode("','", $_status_filter) . "')"; 
*/
				}
				else
				{
					$filtermethod .= " $where fm_workorder.status='$status_id' ";
				}
				$where= 'AND';
			}

			$group_method = '';
			if($wo_hour_cat_id)
			{
				$filtermethod .= " $where fm_wo_hours_category.id=$wo_hour_cat_id ";
				$where= 'AND';
				$group_method = " group by fm_project.id,{$location_table}.location_code,fm_workorder.id,workorder_id,title,fm_workorder.status,fm_workorder.entry_date,user_lid,fm_workorder.vendor_id,project_owner,{$location_table}.address,fm_vendor.org_name,fm_workorder.combined_cost,fm_workorder.actual_cost,fm_workorder.act_vendor_cost";
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

			if ($b_account)
			{
				$filtermethod .= " {$where} fm_workorder.account_id = '{$b_account}'";
				$where= 'AND';
			}

			if ($district_id)
			{
				$filtermethod .= " {$where} district_id = {$district_id}";
				$where= 'AND';
			}

			if (is_array($this->grants))
			{
				$grants = $this->grants;
				while (list($user) = each($grants))
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod .= " $where (fm_project.access='public' AND fm_project.user_id IN(" . implode(',',$public_user_list) . ")";
				$where= 'AND';
			}

			if ($filter)
			{
				$filtermethod .= " $where fm_workorder.user_id={$filter}";
				$where= 'AND';
			}

			if ($start_date)
			{
				$end_date	= $end_date + 3600 * 16 + phpgwapi_datetime::user_timezone();
				$start_date	= $start_date - 3600 * 8 + phpgwapi_datetime::user_timezone();

				$filtermethod .= " $where fm_workorder.start_date >= $start_date AND fm_workorder.start_date <= $end_date ";
				$where= 'AND';
			}

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$query = str_replace(",",'.',$query);
				if(stristr($query, '.'))
				{
					$query=explode(".",$query);
					$querymethod = " $where ({$location_table}.location_code $this->like '{$query[0]}%' AND {$location_table}.location_code $this->like '%{$query[1]}')";
				}
				else
				{
					$matchtypes = array
						(
							'exact' => '=',
							'like'	=> $this->like
						);

					if(count($criteria) > 1)
					{
						$_querymethod = array();
						foreach($criteria as $field_info)
						{
							if($field_info['type'] == int)
							{
								$_query = (int) $query;
							}
							else if($field_info['type'] == 'bigint'  && !ctype_digit($query))
							{
								$_query = 0;
							}
							else
							{
								$_query = $query;
							}

							$_querymethod[] = "{$field_info['field']} {$matchtypes[$field_info['matchtype']]} {$field_info['front']}{$_query}{$field_info['back']}";
						}
						$querymethod = $where . ' (' . implode(' OR ', $_querymethod) . ')';
						unset($_querymethod);
						//_debug_array($querymethod);
					}
					else
					{
						if($criteria[0]['type'] == 'int')
						{
							$_query = (int) $query;
						}
						else if($criteria[0]['type'] == 'bigint'  && !ctype_digit($query))
						{
							$_query = 0;
						}
						else
						{
							$_query = $query;
						}

						$querymethod = "{$where} {$criteria[0]['field']} {$matchtypes[$criteria[0]['matchtype']]} {$criteria[0]['front']}{$_query}{$criteria[0]['back']}";
					}

				}
				$where= 'AND';
			}
			$querymethod .= ')';

			$sql_full = "{$sql} {$filtermethod} {$querymethod}";

			$sql_base = substr($sql_full,strripos($sql_full,'FROM'));

			if($GLOBALS['phpgw_info']['server']['db_type']=='postgres')
			{				
				$sql_minimized = "SELECT DISTINCT fm_workorder.id {$sql_base}";
				$sql_count = "SELECT count(id) as cnt FROM ({$sql_minimized}) as t";

				$this->db->query($sql_count,__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f('cnt');
			}
			else
			{
				$sql_count = 'SELECT DISTINCT fm_workorder.id ' . substr($sql_full,strripos($sql_full,'FROM'));
				$this->db->query($sql_count,__LINE__,__FILE__);
				$this->total_records = $this->db->num_rows();
			}

			$workorder_list = array();

			$sql_end =   str_replace('SELECT DISTINCT fm_workorder.id',"SELECT DISTINCT fm_workorder.id {$order_field}", $sql_minimized) . $ordermethod;
//	_debug_array($sql_end);die();

			if(!$allrows)
			{
				$this->db->limit_query($sql_end,$start,__LINE__,__FILE__);
			}
			else
			{
				if($this->total_records > 200)
				{
					$_fetch_single = true;
				}
				else
				{
					$_fetch_single = false;
				}
				$this->db->query($sql_end,__LINE__,__FILE__, false, $_fetch_single );
				unset($_fetch_single);
			}

			$count_cols_return=count($cols_return);

			while ($this->db->next_record())
			{
				$workorder_list[] = array('workorder_id' => $this->db->f('id'));
			}

			foreach($workorder_list as &$workorder)
			{
				$this->db->query("{$sql} WHERE fm_workorder.id = '{$workorder['workorder_id']}'");
				$this->db->next_record();

				for ($i=0;$i<$count_cols_return;$i++)
				{
					$workorder[$cols_return[$i]] = $this->db->f($cols_return[$i]);
				}
				$workorder['grants'] = (int)$this->grants[$this->db->f('project_owner')];

				$location_code=	$this->db->f('location_code');
				$location = explode('-',$location_code);
				$count_location =count($location);

				for ($m=0;$m<$count_location;$m++)
				{
					$workorder['loc' . ($m+1)] = $location[$m];
					$workorder['query_location']['loc' . ($m+1)]=implode("-", array_slice($location, 0, ($m+1)));
				}

				$sql_workder  = 'SELECT godkjentbelop AS actual_cost'
				. " FROM fm_ecobilag  WHERE pmwrkord_code = '{$workorder['workorder_id']}'";

				$this->db->query($sql_workder);
				while ($this->db->next_record())
				{
					$_actual_cost = (int)$this->db->f('actual_cost');
					$workorder['combined_cost']	-= $_actual_cost;
					$workorder['actual_cost']	+= $_actual_cost;
				}

				if($workorder['combined_cost'] < 0)
				{
					$workorder['combined_cost'] = 0;
				}
			}

			return $workorder_list;
		}

		function read_single($workorder_id = 0)
		{
			//	$this->update_actual_cost_global();
			//	$this->update_planned_cost_global();

			if(!$workorder_id)
			{
				return array();
			}
			$sql = "SELECT fm_workorder.*, fm_chapter.descr as chapter ,fm_workorder.user_id as user_id FROM fm_workorder"
	//			. " $this->join fm_project on fm_workorder.project_id=fm_project.id"
				. " $this->left_join fm_chapter on "
				. " fm_workorder.chapter_id = fm_chapter.id WHERE fm_workorder.id='{$workorder_id}'";

			$this->db->query($sql,__LINE__,__FILE__);

			$workorder = array();
			if ($this->db->next_record())
			{
				$workorder = array
					(
						'id'					=> $this->db->f('id'),
						'workorder_id'			=> $this->db->f('id'), // FIXME
						'project_id'			=> $this->db->f('project_id'),
						'title'					=> $this->db->f('title'),
						'name'					=> $this->db->f('name'),
						'key_fetch'				=> $this->db->f('key_fetch'),
						'key_deliver'			=> $this->db->f('key_deliver'),
						'key_responsible'		=> $this->db->f('key_responsible'),
						'charge_tenant'			=> $this->db->f('charge_tenant'),
						'descr'					=> stripslashes($this->db->f('descr')),
						'status'				=> $this->db->f('status'),
						'budget'				=> (int)$this->db->f('budget'),
						'calculation'			=> $this->db->f('calculation'),
						'b_account_id'			=> (int)$this->db->f('account_id'),
						'addition_percentage'	=> (int)$this->db->f('addition'),
						'addition_rs'			=> (int)$this->db->f('rig_addition'),
			//			'act_mtrl_cost'			=> $this->db->f('act_mtrl_cost'),
			//			'act_vendor_cost'		=> $this->db->f('act_vendor_cost'),
						'user_id'				=> $this->db->f('user_id'),
						'vendor_id'				=> $this->db->f('vendor_id'),
			//			'coordinator'			=> $this->db->f('coordinator'),
						'access'				=> $this->db->f('access'),
						'start_date'			=> $this->db->f('start_date'),
						'end_date'				=> $this->db->f('end_date'),
						'cat_id'				=> $this->db->f('category'),
						'chapter_id'			=> $this->db->f('chapter_id'),
						'chapter'				=> $this->db->f('chapter'),
						'deviation'				=> $this->db->f('deviation'),
						'ecodimb'				=> $this->db->f('ecodimb'),
						'location_code'			=> $this->db->f('location_code'),
						'p_num'					=> $this->db->f('p_num'),
						'p_entity_id'			=> $this->db->f('p_entity_id'),
						'p_cat_id'				=> $this->db->f('p_cat_id'),
						'contact_phone'			=> $this->db->f('contact_phone'),
						'tenant_id'				=> $this->db->f('tenant_id'),
						'cat_id'				=> $this->db->f('category'),
						'grants'				=> (int)$this->grants[$this->db->f('user_id')],
						'billable_hours'		=> $this->db->f('billable_hours'),
						'contract_sum'			=> $this->db->f('contract_sum'),
						'approved'				=> $this->db->f('approved'),
						'mail_recipients'		=> explode(',', trim($this->db->f('mail_recipients'),',')),
					);
			}

			//_debug_array($workorder);
			return $workorder;
		}


		function project_budget_from_workorder($project_id = '')
		{
			$project_id = (int) $project_id;
			$this->db->query("select budget, id as workorder_id from fm_workorder WHERE project_id={$project_id}");
			$budget = array();
			while ($this->db->next_record())
			{
				$budget[] = array(
					'workorder_id'	=> $this->db->f('workorder_id'),
					'budget'	=> sprintf("%01.2f",$this->db->f('budget'))
				);
			}
			return $budget;
		}

		/**
		 * planned cost start out as the project budget - and reflect the amount yet to be spent on the project
		 * When an order is placed  - the "planned cost" is reduced with expected cost for that order.
		 * When an invoice is paid -  the "planned cost" is reduced with actual cost for that order (replace the expected cost).
		 *
		 * @param integer $project_id the project in question
		 *
		 * @return void
		 */

		function update_planned_cost($project_id)
		{
			$this->db->query("SELECT paid, paid_percent, act_mtrl_cost, act_vendor_cost, combined_cost, budget FROM fm_workorder WHERE project_id={$project_id}");
			$workorders = array();
			while ($this->db->next_record())
			{
				$workorders[] = array
					(
						'paid'			=> $this->db->f('paid'), //0-cancelled /1-invoice received but not paid / 2 - paid
						'paid_percent'	=> $this->db->f('paid_percent')/100,
						'actual_cost'	=> $this->db->f('act_mtrl_cost') + $this->db->f('act_vendor_cost'),
						'cost'			=> abs($this->db->f('combined_cost')) > 0 ? $this->db->f('combined_cost') : $this->db->f('budget'),
					);
			}

			$orded_or_paid = 0;

			foreach($workorders as $workorder)
			{
				if($workorder['paid'] == 0 || $workorder['paid'] == 1)
				{
					$orded_or_paid = $orded_or_paid + $workorder['cost'];
				}
				else
				{
					if(!$workorder['paid_percent'])
					{
						$workorder['paid_percent'] = 1;
					}
					$orded_or_paid = $orded_or_paid + ($workorder['actual_cost']/$workorder['paid_percent']);
				}
			}

			$this->db->query("SELECT budget, reserve FROM fm_project WHERE id={$project_id}"); 
			$this->db->next_record(); 
			$project_sum = $this->db->f('budget') + $this->db->f('reserve'); 

			$project_planned_cost = round($project_sum - $orded_or_paid);

			if($project_planned_cost < 0)
			{
				$project_planned_cost = 0;
			}

			//_debug_array("UPDATE fm_project SET planned_cost = {$project_planned_cost} WHERE id = {$project_id}");
			$this->db->query("UPDATE fm_project SET planned_cost = {$project_planned_cost} WHERE id = {$project_id}");
		}

		function update_actual_cost_global()
		{
			set_time_limit(1800);
			$this->db->query("SELECT id FROM fm_workorder ORDER BY id ASC",__LINE__,__FILE__);
			$workorders = array();
			while ($this->db->next_record())
			{
				$workorders[] = $this->db->f('id');
			}
			//_debug_array($workorders);die();

			foreach ($workorders as $workorder_id)
			{
				$this->update_actual_cost($workorder_id);
			}
		}

		function update_planned_cost_global()
		{
			set_time_limit(3600);
			$this->db->query("SELECT id FROM fm_project ORDER BY id ASC",__LINE__,__FILE__);
			$projects = array();
			while ($this->db->next_record())
			{
				$projects[] = $this->db->f('id');
			}
			//_debug_array($projects);die();

			foreach ($projects as $project_id)
			{
				$this->update_planned_cost($project_id);
			}
		}

		function update_actual_cost($workorder_id)
		{
			$this->db->query("SELECT godkjentbelop, dimd FROM fm_ecobilagoverf WHERE pmwrkord_code = {$workorder_id}",__LINE__,__FILE__);
			$cost = array();
			while ($this->db->next_record())
			{
				$cost[] = array
					(
						'godkjentbelop' => $this->db->f('godkjentbelop'),
						'dimd' => $this->db->f('dimd'),
					);
			}
			$act_mtrl_cost = 0;
			$act_vendor_cost = 0;
			foreach ($cost as $entry)
			{
				if($entry['dimd'] % 2 == 0)
				{
					$act_mtrl_cost = $act_mtrl_cost + $entry['godkjentbelop'];
				}
				else
				{
					$act_vendor_cost = $act_vendor_cost + $entry['godkjentbelop'];
				}
			}
			//_debug_array("UPDATE fm_workorder SET act_mtrl_cost = {$act_mtrl_cost}, act_vendor_cost = {$act_vendor_cost}  WHERE id = {$workorder_id}");
			$this->db->query("UPDATE fm_workorder SET act_mtrl_cost = {$act_mtrl_cost}, act_vendor_cost = {$act_vendor_cost}  WHERE id = {$workorder_id}");

		}

		function branch_p_list($project_id = '')
		{
			$project_id = (int) $project_id;
			$this->db2->query("SELECT branch_id from fm_projectbranch WHERE project_id={$project_id}",__LINE__,__FILE__);
			$selected = array();
			while ($this->db2->next_record())
			{
				$selected[] = array('branch_id' => $this->db2->f('branch_id'));
			}

			return $selected;
		}

		function increment_workorder_id()
		{
			$name = 'workorder';
			$now = time();
			$this->db->query("SELECT value, start_date FROM fm_idgenerator WHERE name='{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$next_id = $this->db->f('value') +1;
			$start_date = (int)$this->db->f('start_date');
			$this->db->query("UPDATE fm_idgenerator SET value = $next_id WHERE name = '{$name}' AND start_date = {$start_date}");
		}

		function add($workorder)
		{
			$receipt = array();
			$historylog	= CreateObject('property.historylog','workorder');
			$workorder['descr'] = $this->db->db_addslashes($workorder['descr']);
			$workorder['title'] = $this->db->db_addslashes($workorder['title']);
			$workorder['billable_hours'] = (float)str_replace(',','.', $workorder['billable_hours']);

			$cols = array();
			$vals = array();

			if (isset($workorder['extra']) && is_array($workorder['extra']))
			{
				foreach ($workorder['extra'] as $input_name => $value)
				{
					if($value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}

			if ($workorder['location_code'])
			{
				$cols[] = 'location_code';
				$vals[] = $workorder['location_code'];

				if($workorder['street_name'])
				{
					$address[]= $workorder['street_name'];
					$address[]= $workorder['street_number'];
					$address = $this->db->db_addslashes(implode(" ", $address));
				}

				if(!$address)
				{
					$address = $this->db->db_addslashes($workorder['location_name']);
				}
				$cols[] = 'address';
				$vals[] = $address;
			}

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= ",'" . implode("','", $vals) . "'";
			}
			else
			{
				$cols = '';
				$vals = '';
			}

			$this->db->transaction_begin();
			$id = $this->next_id();
			if(!$workorder['workorder_num'])
			{
				$workorder['workorder_num'] = $id;
			}

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['currency']))
			{
				$workorder['contract_sum'] 		= str_ireplace($GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],'',$workorder['contract_sum']);
			}
			$workorder['contract_sum'] 		= str_replace(array(' ',','),array('','.'),$workorder['contract_sum']);

			$values= array
				(
					$id,
					$workorder['workorder_num'],
					$workorder['project_id'],
					$workorder['title'],
					'public',
					time(),
					$workorder['start_date'],
					$workorder['end_date'],
					$workorder['status'],
					$workorder['descr'],
					(int) $workorder['budget'],
					(int) $workorder['budget'],
					$workorder['b_account_id'],
					$workorder['addition_rs'],
					$workorder['addition_percentage'],
					$workorder['key_deliver'],
					$workorder['key_fetch'],
					$workorder['vendor_id'],
					$workorder['charge_tenant'],
					$workorder['user_id'] ? $workorder['user_id'] : $this->account,
					$workorder['ecodimb'],
					$workorder['cat_id'],
					$workorder['billable_hours'],
					$workorder['contract_sum'],
					$workorder['approved'],
					isset($workorder['vendor_email']) && is_array($workorder['vendor_email']) ? implode(',', $workorder['vendor_email']) : ''
				);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->query("INSERT INTO fm_workorder (id,num,project_id,title,access,entry_date,start_date,end_date,status,"
				. "descr,budget,combined_cost,account_id,rig_addition,addition,key_deliver,key_fetch,vendor_id,charge_tenant,user_id,ecodimb,category,billable_hours,contract_sum,approved,mail_recipients  $cols) "
				. "VALUES ( $values $vals)",__LINE__,__FILE__);

			$this->db->query("INSERT INTO fm_orders (id,type) VALUES ({$id},'workorder')");

/*
			if($workorder['charge_tenant'])
			{
				$this->db->query("UPDATE fm_project set charge_tenant = 1 WHERE id =" . $workorder['project_id']);
			}
 */

			if(is_array($workorder['origin']))
			{
				if($workorder['origin'][0]['data'][0]['id'])
				{
					$interlink_data = array
						(
							'location1_id'		=> $GLOBALS['phpgw']->locations->get_id('property', $workorder['origin'][0]['location']),
							'location1_item_id' => $workorder['origin'][0]['data'][0]['id'],
							'location2_id'		=> $GLOBALS['phpgw']->locations->get_id('property', '.project.workorder'),
							'location2_item_id' => $id,
							'account_id'		=> $this->account
						);

					$this->interlink->add($interlink_data,$this->db);
				}
			}


			if($this->db->transaction_commit())
			{
				$this->increment_workorder_id();
				$historylog->add('SO', $id, $workorder['status']);
				if ($workorder['remark'])
				{
					$historylog->add('RM', $id, $workorder['remark']);
				}

				$receipt['message'][] = array('msg'=>lang('workorder %1 has been saved', $id));
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('the workorder has not been saved'));
			}
			$receipt['id'] = $id;
			return $receipt;
		}

		function edit($workorder)
		{
			$historylog	= CreateObject('property.historylog','workorder');
			$workorder['descr'] = $this->db->db_addslashes($workorder['descr']);
			$workorder['title'] = $this->db->db_addslashes($workorder['title']);
			$workorder['billable_hours'] = (float)str_replace(',','.', $workorder['billable_hours']);

			$this->db->query("SELECT status,budget,calculation,billable_hours,approved FROM fm_workorder WHERE id = {$workorder['id']}",__LINE__,__FILE__);
			$this->db->next_record();

			$old_status			= $this->db->f('status');
			$old_budget			= $this->db->f('budget');
			$old_billable_hours	= $this->db->f('billable_hours');
			$old_approved		= $this->db->f('approved');

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['currency']))
			{
				$workorder['contract_sum'] 		= str_ireplace($GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],'',$workorder['contract_sum']);
			}

			$workorder['contract_sum'] 		= str_replace(array(' ',','),array('','.'),$workorder['contract_sum']);

			if ( abs((int)$workorder['contract_sum']) > 0)
			{
				$addition = 1 + ((int)$workorder['addition_percentage']/100);
				$combined_cost = (int)$workorder['contract_sum'] * $addition;
			}
			else if ($this->db->f('calculation') > 0)
			{
				$calculation = $this->db->f('calculation');
				$config	= CreateObject('phpgwapi.config','property');
				$config->read_repository();
				$tax = 1+(($config->config_data['fm_tax'])/100);
				$combined_cost = $calculation * $tax;
			}
			else
			{
				$combined_cost = (int)$workorder['budget'];
			}

			$this->db->query("SELECT bilagsnr FROM fm_ecobilag WHERE pmwrkord_code ='{$workorder['id']}'",__LINE__,__FILE__);
			$this->db->next_record();

			if($this->db->f('bilagsnr'))
			{
				$paid = 1;
			}

			$this->db->query("SELECT bilagsnr FROM fm_ecobilagoverf where pmwrkord_code = '{$workorder['id']}'",__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('bilagsnr'))
			{
				$paid = 2;
			}

			$value_set = array
				(
					'title'				=> $workorder['title'],
					'status'			=> $workorder['status'],
					'start_date'		=> $workorder['start_date'],
					'end_date'			=> $workorder['end_date'],
					'descr'				=> $workorder['descr'],
					'budget'			=> (int)$workorder['budget'],
					'combined_cost'		=> $combined_cost,
					'key_deliver'		=> $workorder['key_deliver'],
					'key_fetch'			=> $workorder['key_fetch'],
					'account_id'		=> $workorder['b_account_id'],
					'rig_addition'		=> $workorder['addition_rs'],
					'addition'			=> $workorder['addition_percentage'],
					'charge_tenant'		=> $workorder['charge_tenant'],
					'vendor_id'			=> $workorder['vendor_id'],
					'user_id'			=> $workorder['user_id'],
					'ecodimb'			=> $workorder['ecodimb'],
					'category'			=> $workorder['cat_id'],
					'billable_hours'	=> $workorder['billable_hours'],
					'contract_sum'		=> $workorder['contract_sum'],
					'approved'			=> $workorder['approved'],
					'mail_recipients'	=> isset($workorder['vendor_email']) && is_array($workorder['vendor_email']) ? implode(',', $workorder['vendor_email']) : '',
				);

			if($workorder['status'] == 'closed')
			{
				$value_set['paid'] = $paid = (isset($paid)?$paid:0);
				$value_set['paid_percent'] = 100;
			}

			if (isset($workorder['extra']) && is_array($workorder['extra']))
			{
				foreach ($workorder['extra'] as $input_name => $value)
				{
					$value_set[$input_name] = $value;
				}
			}

			if ($workorder['location_code'])
			{
				$value_set['location_code'] = $workorder['location_code'];

				if($workorder['street_name'])
				{
					$address[]= $workorder['street_name'];
					$address[]= $workorder['street_number'];
					$address = $this->db->db_addslashes(implode(" ", $address));
				}

				if(!isset($address) || !$address)
				{
					$address = $this->db->db_addslashes($workorder['location_name']);
				}

				$value_set['address'] = $address;
			}

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_workorder SET {$value_set} WHERE id= {$workorder['id']}" ,__LINE__,__FILE__);

			$value_set_invoice = array();
			$value_set_invoice['spbudact_code'] = $workorder['b_account_id'];
			$value_set_invoice['dime']			= $workorder['cat_id'];

			$value_set_invoice	= $this->bocommon->validate_db_update($value_set_invoice);

			$this->db->query("UPDATE fm_ecobilag SET {$value_set_invoice} WHERE pmwrkord_code = '{$workorder['id']}'" ,__LINE__,__FILE__);

/*			if($workorder['charge_tenant'])
			{
				$this->db->query("UPDATE fm_project set charge_tenant = 1 WHERE id =" . $workorder['project_id']);
			}
 */
//			$this->update_planned_cost($workorder['project_id']); // at project


			if ($old_approved != $workorder['approved'])
			{
				if($workorder['approved'])
				{
					$historylog->add('OA',$workorder['id'],$workorder['approved'], $old_approved);
				}
				else//revoked
				{
					$historylog->add('OB',$workorder['id'],$workorder['approved'], $old_approved);				
				}
				$check_pending_action = true;
			}

			$check_pending_action = false;
			if ((float)$old_billable_hours != (float)$workorder['billable_hours'])
			{
				$historylog->add('H',$workorder['id'],$workorder['billable_hours'],$old_billable_hours);
				$receipt['message'][]= array('msg' => lang('billable hours has been updated'));
			}


			if ($old_status != $workorder['status'])
			{
				$historylog->add('S',$workorder['id'],$workorder['status'], $old_status);
				$receipt['notice_owner'][]=lang('Status changed') . ': ' . $workorder['status'];
				$check_pending_action = true;
			}
			elseif($workorder['confirm_status'])
			{
				$check_pending_action = true;
				$historylog->add('SC',$workorder['id'],$workorder['status'], $old_status);
				$receipt['notice_owner'][]=lang('Status confirmed') . ': ' . $workorder['status'];
			}

			if( $check_pending_action )
			{
				$this->db->query("SELECT * FROM fm_workorder_status WHERE id = '{$workorder['status']}'");
				$this->db->next_record();
				if ($this->db->f('approved') || $workorder['approved'] )
				{
					$action_params = array
						(
							'appname'			=> 'property',
							'location'			=> '.project.workorder',
							'id'				=> $workorder['id'],
							'responsible'		=> $this->account,
							'responsible_type'  => 'user',
							'action'			=> 'approval',
							'remark'			=> '',
							'deadline'			=> ''
						);

					execMethod('property.sopending_action.close_pending_action', $action_params);
					unset($action_params);
				}
				if ($this->db->f('in_progress') )
				{
					$action_params = array
						(
							'appname'			=> 'property',
							'location'			=> '.project.workorder',
							'id'				=> $workorder['id'],
							'responsible'		=> $workorder['vendor_id'],
							'responsible_type'  => 'vendor',
							'action'			=> 'remind',
							'remark'			=> '',
							'deadline'			=> ''
						);

					execMethod('property.sopending_action.close_pending_action', $action_params);
					unset($action_params);

				}
				if ($this->db->f('delivered') || $this->db->f('closed'))
				{
					//close
				}
			}

			if ($old_budget != $workorder['budget'])
			{
				$historylog->add('B', $workorder['id'], $workorder['budget'], $old_budget);
			}

			if (isset($workorder['new_project_id']) && $workorder['new_project_id'] && ($workorder['new_project_id'] != $workorder['project_id']))
			{
				$new_project_id = (int) $workorder['new_project_id'];
				$this->db->query("UPDATE fm_workorder SET project_id = {$new_project_id} WHERE id= {$workorder['id']}" ,__LINE__,__FILE__);
				$historylog->add('NP',$workorder['id'],$new_project_id, $workorder['project_id']);
			}

			if ($workorder['remark'])
			{
				$historylog->add('RM', $workorder['id'], $workorder['remark']);
			}
			if($this->db->transaction_commit())
			{

				$receipt['message'][] = array('msg'=>lang('workorder %1 has been edited', $workorder['id']));
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('workorder %1 has not been edited', $workorder['id']));
			}

			$receipt['id'] = $workorder['id'];
			return $receipt;
		}

		function delete($workorder_id )
		{
			$this->db->transaction_begin();
			$this->interlink->delete_at_target('property', '.project.workorder', $workorder_id, $this->db);
			$this->db->query("DELETE FROM fm_workorder WHERE id='" . $workorder_id . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_workorder_history  WHERE  history_record_id='" . $workorder_id   . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_wo_hours WHERE workorder_id='" . $workorder_id   . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_orders WHERE id='" . $workorder_id . "'",__LINE__,__FILE__);
			$this->db->transaction_commit();

		}

		public function get_user_list()
		{
			$values = array();
			$users = $GLOBALS['phpgw']->accounts->get_list('accounts', $start=-1, $sort='ASC', $order='account_lastname', $query,$offset=-1);
			$sql = 'SELECT DISTINCT user_id AS user_id FROM fm_workorder';
			$this->db->query($sql,__LINE__,__FILE__);

			$account_lastname = array();
			while($this->db->next_record())
			{
				$user_id	= $this->db->f('user_id');
				if(isset($users[$user_id]))
				{
					$name	= $users[$user_id]->__toString();
					$values[] = array
					(
						'id' 	=> $user_id,
						'name'	=> $name
					);
					$account_lastname[]  = $name;
				}
			}

			if($values)
			{
				array_multisort($account_lastname, SORT_ASC, $values);
			}

			return $values;
		}

		public function close_orders($orders)
		{
			$config		= CreateObject('phpgwapi.config','property');
			$config->read();
			$closed = isset($config->config_data['workorder_closed_status']) && $config->config_data['workorder_closed_status'] ? $config->config_data['workorder_closed_status'] : 'closed';

			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}


			if ($orders && is_array($orders))
			{
				$historylog_workorder	= CreateObject('property.historylog','workorder');

				foreach ($orders as $id)
				{
					$this->db->query("SELECT type FROM fm_orders WHERE id='{$id}'",__LINE__,__FILE__);
					$this->db->next_record();
					switch ( $this->db->f('type') )
					{
						case 'workorder':
							$historylog_workorder->add($entry,$id,$closed);
							$GLOBALS['phpgw']->db->query("UPDATE fm_workorder SET status='{$closed}' WHERE id = '{$id}'");
							$GLOBALS['phpgw']->db->query("UPDATE fm_workorder SET paid_percent=100 WHERE id= '{$id}'");				
							$receipt['message'][] = array('msg'=>lang('Workorder %1 is %2',$id, $closed));
							$this->db->query("SELECT project_id FROM fm_workorder WHERE id='{$id}'",__LINE__,__FILE__);
							$this->db->next_record();
							$project_id = $this->db->f('project_id');
				//			$this->update_planned_cost($project_id);
							break;
					}
				}
			}

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}

			return $receipt;
		}

		public function reopen_orders($orders)
		{
			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$config		= CreateObject('phpgwapi.config','property');
			$config->read();
			$reopen = isset($config->config_data['workorder_reopen_status']) && $config->config_data['workorder_reopen_status'] ? $config->config_data['workorder_reopen_status'] : 're_opened';
			$status_code=array('X' => $closed,'R' => $reopen);

			$historylog_workorder	= CreateObject('property.historylog','workorder');

			foreach ($orders as $id)
			{
				$id = (int) $id;
				$this->db->query("SELECT type FROM fm_orders WHERE id={$id}",__LINE__,__FILE__);
				$this->db->next_record();
				switch ( $this->db->f('type') )
				{
					case 'workorder':
						$historylog_workorder->add('R', $id, $reopen);
						$GLOBALS['phpgw']->db->query("UPDATE fm_workorder set status='{$reopen}' WHERE id = {$id}");
						$receipt['message'][] = array('msg'=>lang('Workorder %1 is %2',$id, $status_code[$entry]));
				}
			}

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}
		}
	}
