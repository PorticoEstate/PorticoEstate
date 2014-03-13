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

		var $total_records	 = 0;
		protected $global_lock	 = false;

		function __construct()
		{
			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon	 = CreateObject('property.bocommon');
			$this->db		 = & $GLOBALS['phpgw']->db;
			$this->db2		 = clone($this->db);
			$this->like		 = & $this->db->like;
			$this->join		 = & $this->db->join;
			$this->left_join = & $this->db->left_join;
			$this->interlink = CreateObject('property.interlink');
			//	$this->grants 		= $GLOBALS['phpgw']->session->appsession('grants_project','property');
			//	if(!$this->grants)
			{
				$this->acl		 = & $GLOBALS['phpgw']->acl;
				$this->acl->set_account_id($this->account);
				$this->grants	 = $this->acl->get_grants('property', '.project');
				//		$GLOBALS['phpgw']->session->appsession('grants_project','property',$this->grants);
			}
		}

		function next_id()
		{
			$name	 = 'workorder';
			$now	 = time();
			$this->db->query("SELECT value FROM fm_idgenerator WHERE name = '{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$id		 = $this->db->f('value') + 1;
			return $id;
		}

		function read_single_project_category($id = '')
		{
			$this->db->query("SELECT descr FROM fm_workorder_category where id='$id' ");
			$this->db->next_record();
			return $this->db->f('descr');
		}

		function get_b_account_name($id = '')
		{
			$this->db->query("SELECT descr FROM fm_b_account where id='$id' ");
			$this->db->next_record();
			return $this->db->f('descr');
		}

		function select_status_list()
		{
			$this->db->query("SELECT id, descr FROM fm_workorder_status ORDER BY id ");

			$i = 0;
			while($this->db->next_record())
			{
				$status_entries[$i]['id']	 = $this->db->f('id');
				$status_entries[$i]['name']	 = stripslashes($this->db->f('descr'));
				$i++;
			}
			return $status_entries;
		}

		function select_branch_list()
		{
			$this->db->query("SELECT id, descr FROM fm_branch ORDER BY id ");

			$i = 0;
			while($this->db->next_record())
			{
				$branch_entries[$i]['id']	 = $this->db->f('id');
				$branch_entries[$i]['name']	 = stripslashes($this->db->f('descr'));
				$i++;
			}
			return $branch_entries;
		}

		function select_key_location_list()
		{
			$this->db->query("SELECT id, descr FROM fm_key_loc ORDER BY descr ");

			$i = 0;
			while($this->db->next_record())
			{
				$key_location_entries[$i]['id']		 = $this->db->f('id');
				$key_location_entries[$i]['name']	 = stripslashes($this->db->f('descr'));
				$i++;
			}
			return (isset($key_location_entries) ? $key_location_entries : '');
		}

		function read($data)
		{
			$start			 = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$filter			 = $data['filter'] ? (int) $data['filter'] : 0;
			$query			 = isset($data['query']) ? $data['query'] : '';
			$sort			 = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order			 = isset($data['order']) ? $data['order'] : '';
			$cat_id			 = isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
			$status_id		 = isset($data['status_id']) && $data['status_id'] ? $data['status_id'] : 'open';
			$start_date		 = isset($data['start_date']) && $data['start_date'] ? (int) $data['start_date'] : 0;
			$end_date		 = isset($data['end_date']) && $data['end_date'] ? (int) $data['end_date'] : 0;
			$allrows		 = isset($data['allrows']) ? $data['allrows'] : '';
			$obligation		 = isset($data['obligation']) ? $data['obligation'] : '';
			$wo_hour_cat_id	 = isset($data['wo_hour_cat_id']) ? $data['wo_hour_cat_id'] : '';
			$b_group		 = isset($data['b_group']) ? $data['b_group'] : '';
			$ecodimb		 = isset($data['ecodimb']) ? $data['ecodimb'] : '';
			$paid			 = isset($data['paid']) ? $data['paid'] : '';
			$b_account		 = isset($data['b_account']) ? $data['b_account'] : '';
			$district_id	 = isset($data['district_id']) ? $data['district_id'] : '';
			$dry_run		 = isset($data['dry_run']) ? $data['dry_run'] : '';
			$criteria		 = isset($data['criteria']) && $data['criteria'] ? $data['criteria'] : array();
			$filter_year	 = isset($data['filter_year']) ? $data['filter_year'] : '';

			$GLOBALS['phpgw']->config->read();
			$sql = $this->bocommon->fm_cache('sql_workorder' . !!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
			//echo $sql;
			if(!$sql)
			{
				$cols					 = "fm_project.id as project_id";
				$cols_return[]			 = 'project_id';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'project_id';
				$uicols['descr'][]		 = lang('Project');
				$uicols['statustext'][]	 = lang('Project ID');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = 'linktToProject';
				$uicols['classname'][]	 = '';
				$uicols['sortable'][]	 = true;

				$cols .= ",fm_workorder.id as workorder_id";
				$cols_return[]			 = 'workorder_id';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'workorder_id';
				$uicols['descr'][]		 = lang('Workorder');
				$uicols['statustext'][]	 = lang('Workorder ID');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = 'linktToOrder';
				$uicols['classname'][]	 = '';
				$uicols['sortable'][]	 = true;

				$cols .= ",fm_workorder.title as title";
				$cols_return[]			 = 'title';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'title';
				$uicols['descr'][]		 = lang('Title');
				$uicols['statustext'][]	 = lang('Workorder title');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = '';
				$uicols['classname'][]	 = '';
				$uicols['sortable'][]	 = '';

				$cols .= ",fm_workorder_status.descr as status";
				$cols_return[]			 = 'status';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'status';
				$uicols['descr'][]		 = lang('Status');
				$uicols['statustext'][]	 = lang('Workorder status');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = '';
				$uicols['classname'][]	 = '';
				$uicols['sortable'][]	 = '';

				$cols .= ",fm_workorder.entry_date as entry_date";
				$cols_return[]	 = 'entry_date';
				$cols .= ",fm_workorder.start_date as start_date";
				$cols_return[]	 = 'start_date';
				$cols .= ",fm_workorder.end_date as end_date";
				$cols_return[]	 = 'end_date';
				$cols.= ",fm_project.ecodimb";
				$cols_return[]	 = 'ecodimb';
				$cols.= ",fm_workorder.contract_sum";
				$cols_return[]	 = 'contract_sum';
				$cols.= ",fm_workorder.approved";
				$cols_return[]	 = 'approved';

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
				$cols_return[]			 = 'user_lid';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'user_lid';
				$uicols['descr'][]		 = lang('User');
				$uicols['statustext'][]	 = lang('Workorder User');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = '';
				$uicols['classname'][]	 = '';
				$uicols['sortable'][]	 = '';

				$cols .= ',fm_workorder.vendor_id';
				$cols_return[]			 = 'vendor_id';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'vendor_id';
				$uicols['descr'][]		 = lang('Vendor ID');
				$uicols['statustext'][]	 = lang('Vendor ID');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = '';
				$uicols['classname'][]	 = '';
				$uicols['sortable'][]	 = '';

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

				$joinmethod .= "{$this->join} fm_workorder ON (fm_project.id = fm_workorder.project_id) {$this->join} phpgw_accounts ON (fm_workorder.user_id = phpgw_accounts.account_id))";
				$paranthesis .='(';

				$joinmethod .= " {$this->join} fm_workorder_status ON (fm_workorder.status = fm_workorder_status.id))";
				$paranthesis .='(';

				$cols .= ',fm_workorder_status.closed';
				$cols .= ',fm_vendor.org_name';
				$cols_return[]			 = 'org_name';
				$uicols['input_type'][]	 = 'hidden';
				$uicols['name'][]		 = 'org_name';
				$uicols['descr'][]		 = lang('Vendor name');
				$uicols['statustext'][]	 = lang('Vendor name');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = '';
				$uicols['classname'][]	 = '';
				$uicols['sortable'][]	 = '';

				$cols .= ',fm_workorder.budget';
				$cols_return[]			 = 'budget';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'budget';
				$uicols['descr'][]		 = lang('budget');
				$uicols['statustext'][]	 = lang('budget');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = 'myFormatCount2';
				$uicols['classname'][]	 = 'rightClasss';
				$uicols['sortable'][]	 = false;

//				$cols .= ',fm_workorder.combined_cost';
//				$cols_return[] = 'combined_cost';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'obligation';
				$uicols['descr'][]		 = lang('sum orders');
				$uicols['statustext'][]	 = lang('Cost - either budget or calculation');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = 'myFormatCount2';
				$uicols['classname'][]	 = 'rightClasss';
				$uicols['sortable'][]	 = false;

				$cols .= ',fm_workorder.actual_cost';
				$cols_return[]			 = 'actual_cost';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'actual_cost';
				$uicols['descr'][]		 = lang('Actual cost');
				$uicols['statustext'][]	 = lang('Actual cost - paid so far');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = 'myFormatCount2';
				$uicols['classname'][]	 = 'rightClasss';
				$uicols['sortable'][]	 = true;

				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'diff';
				$uicols['descr'][]		 = lang('difference');
				$uicols['statustext'][]	 = lang('difference');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = 'myFormatCount2';
				$uicols['classname'][]	 = 'rightClasss';
				$uicols['sortable'][]	 = '';

				$joinmethod .= " {$this->left_join} fm_vendor ON (fm_workorder.vendor_id = fm_vendor.id))";
				$paranthesis .='(';
				$joinmethod .= " {$this->left_join} fm_workorder_budget ON (fm_workorder.id = fm_workorder_budget.order_id))";
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


				$cols_return[]	 = 'location_code';
				$cols_return[]	 = 'billable_hours';
				$cols_return[]	 = 'continuous';

				$cols .= ',fm_workorder.billable_hours';
				$cols .= ',fm_workorder.continuous';

				$no_address = false;
				if(isset($GLOBALS['phpgw']->config->config_data['location_at_workorder']) && $GLOBALS['phpgw']->config->config_data['location_at_workorder'])
				{
					$no_address				 = true;
					$cols .= ',fm_workorder.location_code';
					$cols .= ',fm_workorder.address';
					$cols_return[]			 = 'address';
					$uicols['input_type'][]	 = 'text';
					$uicols['name'][]		 = 'address';
					$uicols['descr'][]		 = lang('address');
					$uicols['statustext'][]	 = lang('address');
					$uicols['exchange'][]	 = false;
					$uicols['align'][]		 = '';
					$uicols['datatype'][]	 = '';
					$uicols['formatter'][]	 = '';
					$uicols['classname'][]	 = '';
					$uicols['sortable'][]	 = true;

					$joinmethod .= "{$this->join} fm_locations ON (fm_workorder.location_code = fm_locations.location_code))";
					$paranthesis .='(';

					$location_table = 'fm_locations';
				}
				else
				{
					$cols .= ",fm_project.location_code";
					$location_table = 'fm_project';
				}

				$entity_table = 'fm_project';

				$sql = $this->bocommon->generate_sql(array('entity_table'	 => $entity_table, 'location_table' => $location_table, 'cols'			 => $cols, 'cols_return'	 => $cols_return,
					'uicols'		 => $uicols, 'joinmethod'	 => $joinmethod, 'paranthesis'	 => $paranthesis,
					'force_location' => true, 'no_address'	 => $no_address, 'location_level' => 0));

				$this->bocommon->fm_cache('sql_workorder' . !!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group, $sql);

				$this->uicols	 = $this->bocommon->uicols;
				$cols_return	 = $this->bocommon->cols_return;
				$type_id		 = $this->bocommon->type_id;
//				$this->cols_extra	= $this->bocommon->cols_extra;

				$this->bocommon->fm_cache('uicols_workorder' . !!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group, $this->uicols);
				$this->bocommon->fm_cache('cols_return_workorder' . !!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group, $cols_return);
				$this->bocommon->fm_cache('type_id_workorder' . !!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group, $type_id);
//				$this->bocommon->fm_cache('cols_extra_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group,$this->cols_extra);
			}
			else
			{
				$this->uicols	 = $this->bocommon->fm_cache('uicols_workorder' . !!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
				$cols_return	 = $this->bocommon->fm_cache('cols_return_workorder' . !!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
				$type_id		 = $this->bocommon->fm_cache('type_id_workorder' . !!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
//				$this->cols_extra	= $this->bocommon->fm_cache('cols_extra_workorder'.!!$search_vendor . '_' . !!$wo_hour_cat_id . '_' . !!$b_group);
			}


			$location_table = 'fm_project';
			if(isset($GLOBALS['phpgw']->config->config_data['location_at_workorder']) && $GLOBALS['phpgw']->config->config_data['location_at_workorder'])
			{
				$location_table = 'fm_workorder';
			}

			$order_field = '';
			if($order)
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
						$order_field = ", fm_project.ecodimb";
						break;
					case 'budget':
						$order_field = ", fm_workorder.budget";
						break;
					case 'approved':
						$order_field = ", fm_workorder.approved";
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
			$where = 'WHERE';

			$filtermethod = '';


			if(isset($GLOBALS['phpgw']->config->config_data['acl_at_location']) && $GLOBALS['phpgw']->config->config_data['acl_at_location'])
			{
				$access_location = $this->bocommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod	 = " WHERE fm_project.loc1 in ('" . implode("','", $access_location) . "')";
				$where			 = 'AND';
			}

			if($cat_id > 0)
			{
				$cats				 = CreateObject('phpgwapi.categories', -1, 'property', '.project');
				$cats->supress_info	 = true;
				$cat_list_project	 = $cats->return_sorted_array(0, false, '', '', '', false, $cat_id, false);
				$cat_filter			 = array($cat_id);
				foreach($cat_list_project as $_category)
				{
					$cat_filter[] = $_category['id'];
				}
				$filtermethod .= " {$where} fm_workorder.category IN (" . implode(',', $cat_filter) . ')';

				$where = 'AND';
			}

			if($status_id && $status_id != 'all')
			{

				if($status_id == 'open')
				{
					$filtermethod .= " $where fm_workorder_status.closed IS NULL";

					/* 					$_status_filter = array();
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
				$where = 'AND';
			}

			$group_method = '';
			if($wo_hour_cat_id)
			{
				$filtermethod .= " $where fm_wo_hours_category.id=$wo_hour_cat_id ";
				$where			 = 'AND';
				$group_method	 = " group by fm_project.id,{$location_table}.location_code,fm_workorder.id,workorder_id,title,fm_workorder.status,fm_workorder.entry_date,user_lid,fm_workorder.vendor_id,project_owner,{$location_table}.address,fm_vendor.org_name,fm_workorder.combined_cost,fm_workorder.actual_cost,fm_workorder.act_vendor_cost";
			}

			if($b_group)
			{
				$filtermethod .= " $where fm_b_account.category='$b_group' ";
				$where = 'AND';
			}

			if($paid)
			{
				/* 0 => cancelled, 1 => obligation , 2 => paid */
				$filtermethod .= " $where fm_workorder.paid = $paid AND vendor_id > 0";
				$where = 'AND';
			}

			if($ecodimb)
			{
				$filtermethod .= " $where fm_project.ecodimb =" . (int) $ecodimb;
				$where = 'AND';
			}

			if($b_account)
			{
				$filtermethod .= " {$where} fm_workorder.account_id = '{$b_account}'";
				$where = 'AND';
			}

			if($district_id)
			{
				$filtermethod .= " {$where} district_id = {$district_id}";
				$where = 'AND';
			}

			if(is_array($this->grants))
			{
				$grants = $this->grants;
				while(list($user) = each($grants))
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod .= " $where (fm_project.access='public' AND fm_project.user_id IN(" . implode(',', $public_user_list) . ")";
				$where = 'AND';
			}

			if($filter)
			{
				$filtermethod .= " $where fm_workorder.user_id={$filter}";
				$where = 'AND';
			}

			if($start_date)
			{
				$end_date	 = $end_date + 3600 * 16 + phpgwapi_datetime::user_timezone();
				$start_date	 = $start_date - 3600 * 8 + phpgwapi_datetime::user_timezone();

				$filtermethod .= " $where (fm_workorder.start_date >= $start_date AND fm_workorder.start_date <= $end_date";
				if($obligation)
				{
					$filtermethod .= " OR fm_workorder_status.closed IS NULL)";
				}
				else
				{
					$filtermethod .= ')';
				}

				$where = 'AND';
			}

			if($filter_year && $filter_year != 'all')
			{
				$filter_year = (int) $filter_year;
				$filtermethod .= " $where (fm_workorder_budget.year={$filter_year} OR fm_workorder_status.closed IS NULL)";
				$where		 = 'AND';
			}

			$querymethod = '';
			if($query)
			{
				$query	 = $this->db->db_addslashes($query);
				$query	 = str_replace(",", '.', $query);
				if(stristr($query, '.'))
				{
					$query		 = explode(".", $query);
					$querymethod = " $where ({$location_table}.location_code $this->like '{$query[0]}%' AND {$location_table}.location_code $this->like '%{$query[1]}')";
				}
				else
				{
					$matchtypes = array
					(
						'exact'	 => '=',
						'like'	 => $this->like
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
							else if($field_info['type'] == 'bigint' && !ctype_digit($query))
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
						else if($criteria[0]['type'] == 'bigint' && !ctype_digit($query))
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
				$where = 'AND';
			}
			$querymethod .= ')';

			$sql_full = "{$sql} {$filtermethod} {$querymethod}";

			$sql_base = substr($sql_full, strripos($sql_full, 'FROM'));

			if($GLOBALS['phpgw_info']['server']['db_type'] == 'postgres')
			{
				$sql_minimized	 = "SELECT DISTINCT fm_workorder.id {$sql_base}";
				$sql_count		 = "SELECT count(id) as cnt FROM ({$sql_minimized}) as t";

				$this->db->query($sql_count, __LINE__, __FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f('cnt');
			}
			else
			{
				$sql_count			 = 'SELECT DISTINCT fm_workorder.id ' . substr($sql_full, strripos($sql_full, 'FROM'));
				$this->db->query($sql_count, __LINE__, __FILE__);
				$this->total_records = $this->db->num_rows();
			}

			$workorder_list = array();

			if($dry_run)
			{
				return $workorder_list;
			}

			$sql_end = str_replace('SELECT DISTINCT fm_workorder.id', "SELECT DISTINCT fm_workorder.id {$order_field}", $sql_minimized) . $ordermethod;
//	_debug_array($sql_end);

			if(!$allrows)
			{
				$this->db->limit_query($sql_end, $start, __LINE__, __FILE__);
			}
			else
			{
				$_fetch_single = false;
//FIXME: something wrong here...
				/*
				  if($this->total_records > 200)
				  {
				  $_fetch_single = true;
				  }
				  else
				  {
				  $_fetch_single = false;
				  }

				 */
				$this->db->query($sql_end, __LINE__, __FILE__, false, $_fetch_single);
				unset($_fetch_single);
			}

			$count_cols_return = count($cols_return);

			$_order_list = array();
			while($this->db->next_record())
			{
				$workorder_list[]	 = array('workorder_id' => $this->db->f('id'));
				$_order_list[]		 = $this->db->f('id');
			}

			$this->db->set_fetch_single(false);

			$_actual_cost_arr = array();

			$this->db->query('SELECT id, percent FROM fm_ecomva', __LINE__, __FILE__);
			$_taxcode = array(0 => 0);
			while($this->db->next_record())
			{
				$_taxcode[$this->db->f('id')] = $this->db->f('percent');
			}

			foreach($workorder_list as &$workorder)
			{
				$this->db->query("{$sql} WHERE fm_workorder.id = '{$workorder['workorder_id']}'");
				$this->db->next_record();

				for($i = 0; $i < $count_cols_return; $i++)
				{
					$workorder[$cols_return[$i]] = $this->db->f($cols_return[$i]);
				}
				$workorder['actual_cost']	 = 0;
				$workorder['obligation']	 = 0;
				$workorder['combined_cost']	 = 0;
				$workorder['budget']		 = 0;
//---------
				$workorder['grants']		 = (int) $this->grants[$this->db->f('project_owner')];

				$location_code	 = $this->db->f('location_code');
				$location		 = explode('-', $location_code);
				$count_location	 = count($location);

				for($m = 0; $m < $count_location; $m++)
				{
					$workorder['loc' . ($m + 1)]					 = $location[$m];
					$workorder['query_location']['loc' . ($m + 1)]	 = implode("-", array_slice($location, 0, ($m + 1)));
				}
			}
			reset($workorder_list);

			foreach($workorder_list as &$workorder)
			{
				$order_budget = $this->get_budget($workorder['workorder_id']);
				foreach($order_budget as $entry)
				{
					if($entry['active'] == 2)
					{
						continue;
					}

					if($filter_year && $filter_year != 'all')
					{
						if($entry['year'] == $filter_year)
						{
							$workorder['actual_cost'] += $entry['actual_cost'];
							$workorder['combined_cost'] += $entry['sum_orders'];
							$workorder['budget'] += $entry['budget'];
							$workorder['obligation'] += $entry['sum_oblications'];
						}
					}
					else
					{
						$workorder['actual_cost'] += $entry['actual_cost'];

						if($entry['active'])
						{
							$workorder['combined_cost'] += $entry['sum_orders'];
							$workorder['budget'] += $entry['budget'];
							$workorder['obligation'] += $entry['sum_oblications'];
						}
					}
				}

				$_diff_start		 = abs($workorder['budget']) > 0 ? $workorder['budget'] : $workorder['combined_cost'];
				$workorder['diff']	 = $_diff_start - $workorder['obligation'] - $workorder['actual_cost'];
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

			$this->db->query($sql, __LINE__, __FILE__);

			$workorder = array();
			if($this->db->next_record())
			{
				$workorder = array
				(
					'id'					 => $this->db->f('id'),
					'workorder_id'			 => $this->db->f('id'), // FIXME
					'project_id'			 => $this->db->f('project_id'),
					'title'					 => $this->db->f('title'),
					'name'					 => $this->db->f('name'),
					'key_fetch'				 => $this->db->f('key_fetch'),
					'key_deliver'			 => $this->db->f('key_deliver'),
					'key_responsible'		 => $this->db->f('key_responsible'),
					'charge_tenant'			 => $this->db->f('charge_tenant'),
					'descr'					 => stripslashes($this->db->f('descr')),
					'status'				 => $this->db->f('status'),
					'calculation'			 => $this->db->f('calculation'),
					'b_account_id'			 => (int) $this->db->f('account_id'),
					'addition_percentage'	 => (int) $this->db->f('addition'),
					'addition_rs'			 => (int) $this->db->f('rig_addition'),
					//		'act_mtrl_cost'			=> $this->db->f('act_mtrl_cost'),
					//		'act_vendor_cost'		=> $this->db->f('act_vendor_cost'),
					'user_id'				 => $this->db->f('user_id'),
					'vendor_id'				 => $this->db->f('vendor_id'),
					//		'coordinator'			=> $this->db->f('coordinator'),
					'access'				 => $this->db->f('access'),
					'start_date'			 => $this->db->f('start_date'),
					'end_date'				 => $this->db->f('end_date'),
					'cat_id'				 => $this->db->f('category'),
					'chapter_id'			 => $this->db->f('chapter_id'),
					'chapter'				 => $this->db->f('chapter'),
					'deviation'				 => $this->db->f('deviation'),
					'ecodimb'				 => $this->db->f('ecodimb'),
					'location_code'			 => $this->db->f('location_code'),
					'p_num'					 => $this->db->f('p_num'),
					'p_entity_id'			 => $this->db->f('p_entity_id'),
					'p_cat_id'				 => $this->db->f('p_cat_id'),
					'contact_phone'			 => $this->db->f('contact_phone'),
					'tenant_id'				 => $this->db->f('tenant_id'),
					'cat_id'				 => $this->db->f('category'),
					'grants'				 => (int) $this->grants[$this->db->f('user_id')],
					'billable_hours'		 => $this->db->f('billable_hours'),
					'approved'				 => $this->db->f('approved'),
					'mail_recipients'		 => explode(',', trim($this->db->f('mail_recipients'), ',')),
					'actual_cost'			 => $this->db->f('actual_cost'),
					'continuous'			 => $this->db->f('continuous'),
					'fictive_periodization'	 => $this->db->f('fictive_periodization'),

				);

				$sql = "SELECT periodization_id,"
				. " sum(fm_workorder_budget.budget) AS budget, sum(fm_workorder_budget.combined_cost) AS combined_cost,"
				. " sum(fm_workorder_budget.contract_sum) AS contract_sum"
				. " FROM fm_workorder {$this->join} fm_project ON fm_workorder.project_id = fm_project.id"
				. " {$this->join} fm_workorder_budget ON fm_workorder.id = fm_workorder_budget.order_id"
				. " WHERE fm_workorder.id = '{$workorder_id}' GROUP BY periodization_id";

				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$workorder['budget']		 = (int) $this->db->f('budget');
				$workorder['contract_sum']	 = $this->db->f('contract_sum');
			}

			//_debug_array($workorder);
			return $workorder;
		}

		function project_budget_from_workorder($project_id = 0)
		{
			$project_id	 = (int) $project_id;
			$this->db->query("select budget, id as workorder_id from fm_workorder WHERE project_id={$project_id}");
			$budget		 = array();
			while($this->db->next_record())
			{
				$budget[] = array
				(
					'workorder_id'	 => $this->db->f('workorder_id'),
					'budget'		 => sprintf("%01.2f", $this->db->f('budget'))
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
			while($this->db->next_record())
			{
				$workorders[] = array
				(
					'paid'			 => $this->db->f('paid'), //0-cancelled /1-invoice received but not paid / 2 - paid
					'paid_percent'	 => $this->db->f('paid_percent') / 100,
					'actual_cost'	 => $this->db->f('act_mtrl_cost') + $this->db->f('act_vendor_cost'),
					'cost'			 => abs($this->db->f('combined_cost')) > 0 ? $this->db->f('combined_cost') : $this->db->f('budget'),
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
					$orded_or_paid = $orded_or_paid + ($workorder['actual_cost'] / $workorder['paid_percent']);
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
			$this->db->query("SELECT id FROM fm_workorder ORDER BY id ASC", __LINE__, __FILE__);
			$workorders = array();
			while($this->db->next_record())
			{
				$workorders[] = $this->db->f('id');
			}
			//_debug_array($workorders);die();

			foreach($workorders as $workorder_id)
			{
				$this->update_actual_cost($workorder_id);
			}
		}

		function update_planned_cost_global()
		{
			set_time_limit(3600);
			$this->db->query("SELECT id FROM fm_project ORDER BY id ASC", __LINE__, __FILE__);
			$projects = array();
			while($this->db->next_record())
			{
				$projects[] = $this->db->f('id');
			}
			//_debug_array($projects);die();

			foreach($projects as $project_id)
			{
				$this->update_planned_cost($project_id);
			}
		}

		function update_actual_cost($workorder_id)
		{
			$this->db->query("SELECT godkjentbelop, dimd FROM fm_ecobilagoverf WHERE pmwrkord_code = {$workorder_id}", __LINE__, __FILE__);
			$cost = array();
			while($this->db->next_record())
			{
				$cost[] = array
				(
					'godkjentbelop'	 => $this->db->f('godkjentbelop'),
					'dimd'			 => $this->db->f('dimd'),
				);
			}
			$act_mtrl_cost	 = 0;
			$act_vendor_cost = 0;
			foreach($cost as $entry)
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
			$project_id	 = (int) $project_id;
			$this->db2->query("SELECT branch_id from fm_projectbranch WHERE project_id={$project_id}", __LINE__, __FILE__);
			$selected	 = array();
			while($this->db2->next_record())
			{
				$selected[] = array('branch_id' => $this->db2->f('branch_id'));
			}

			return $selected;
		}

		function increment_workorder_id()
		{
			$name		 = 'workorder';
			$now		 = time();
			$this->db->query("SELECT value, start_date FROM fm_idgenerator WHERE name='{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$next_id	 = $this->db->f('value') + 1;
			$start_date	 = (int) $this->db->f('start_date');
			$this->db->query("UPDATE fm_idgenerator SET value = $next_id WHERE name = '{$name}' AND start_date = {$start_date}");
		}

		function add($workorder)
		{
			$receipt					 = array();
			$historylog					 = CreateObject('property.historylog', 'workorder');
			$workorder['descr']			 = $this->db->db_addslashes($workorder['descr']);
			$workorder['title']			 = $this->db->db_addslashes($workorder['title']);
			$workorder['billable_hours'] = (float) str_replace(',', '.', $workorder['billable_hours']);

			$cols	 = array();
			$vals	 = array();

			if(isset($workorder['extra']) && is_array($workorder['extra']))
			{
				foreach($workorder['extra'] as $input_name => $value)
				{
					if($value)
					{
						$cols[]	 = $input_name;
						$vals[]	 = $value;
					}
				}
			}

			if($workorder['location_code'])
			{
				$cols[]	 = 'location_code';
				$vals[]	 = $workorder['location_code'];

				if($workorder['street_name'])
				{
					$address[]	 = $workorder['street_name'];
					$address[]	 = $workorder['street_number'];
					$address	 = $this->db->db_addslashes(implode(" ", $address));
				}

				if(!$address)
				{
					$address = $this->db->db_addslashes($workorder['location_name']);
				}
				$cols[]	 = 'address';
				$vals[]	 = $address;
			}

			if($cols)
			{
				$cols	 = "," . implode(",", $cols);
				$vals	 = ",'" . implode("','", $vals) . "'";
			}
			else
			{
				$cols	 = '';
				$vals	 = '';
			}

			$this->db->transaction_begin();
			$id = $this->next_id();
			if(!$workorder['workorder_num'])
			{
				$workorder['workorder_num'] = $id;
			}

			if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['currency']))
			{
				$workorder['contract_sum'] = str_ireplace($GLOBALS['phpgw_info']['user']['preferences']['common']['currency'], '', $workorder['contract_sum']);
			}
			$workorder['contract_sum'] = str_replace(array(' ', ','), array('', '.'), $workorder['contract_sum']);


			$combined_cost = 0;
			if(abs((int) $workorder['contract_sum']) > 0)
			{
				$addition		 = 1 + ((int) $workorder['addition_percentage'] / 100);
				$combined_cost	 = (int) $workorder['contract_sum'] * $addition;
			}
			else
			{
				$combined_cost = (int) $workorder['budget'];
			}

			$values = array
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
				$combined_cost,
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
				$workorder['continuous'],
				$workorder['fictive_periodization'],
				isset($workorder['vendor_email']) && is_array($workorder['vendor_email']) ? implode(',', $workorder['vendor_email']) : ''
			);

			$values = $this->db->validate_insert($values);

			$this->db->query("INSERT INTO fm_workorder (id,num,project_id,title,access,entry_date,start_date,end_date,status,"
			. "descr,budget,combined_cost,account_id,rig_addition,addition,key_deliver,key_fetch,vendor_id,charge_tenant,"
			. "user_id,ecodimb,category,billable_hours,contract_sum,approved,continuous,fictive_periodization,mail_recipients  $cols) "
			. "VALUES ( {$values} {$vals})", __LINE__, __FILE__);

			$this->db->query("INSERT INTO fm_orders (id,type) VALUES ({$id},'workorder')");

			$periodization_id = isset($workorder['budget_periodization']) && $workorder['budget_periodization'] ? (int) $workorder['budget_periodization'] : 0;
			if($combined_cost)
			{
				$this->_update_order_budget($id, $workorder['budget_year'], $periodization_id, $workorder['budget'], $workorder['contract_sum'], $combined_cost);
			}

			$this->_update_project_budget($workorder['project_id']);

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
						'location1_id'		 => $GLOBALS['phpgw']->locations->get_id('property', $workorder['origin'][0]['location']),
						'location1_item_id'	 => $workorder['origin'][0]['data'][0]['id'],
						'location2_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.project.workorder'),
						'location2_item_id'	 => $id,
						'account_id'		 => $this->account
					);

					$this->interlink->add($interlink_data, $this->db);
				}
			}


			if($this->db->transaction_commit())
			{
				$this->increment_workorder_id();
				$historylog->add('SO', $id, $workorder['status']);
				if($workorder['remark'])
				{
					$historylog->add('RM', $id, $workorder['remark']);
				}

				$receipt['message'][] = array('msg' => lang('workorder %1 has been saved', $id));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('the workorder has not been saved'));
			}
			$receipt['id'] = $id;
			return $receipt;
		}

		function edit($workorder)
		{
			$historylog					 = CreateObject('property.historylog', 'workorder');
			$workorder['descr']			 = $this->db->db_addslashes($workorder['descr']);
			$workorder['title']			 = $this->db->db_addslashes($workorder['title']);
			$workorder['billable_hours'] = (float) str_replace(',', '.', $workorder['billable_hours']);

			phpgwapi_cache::system_clear('property', "budget_order_{$workorder['id']}");

			$this->db->query("SELECT status,calculation,billable_hours,approved FROM fm_workorder WHERE id = {$workorder['id']}", __LINE__, __FILE__);
			$this->db->next_record();

			$old_status			 = $this->db->f('status');
			$old_billable_hours	 = $this->db->f('billable_hours');
			$old_approved		 = $this->db->f('approved');

			if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['currency']))
			{
				$workorder['contract_sum'] = str_ireplace($GLOBALS['phpgw_info']['user']['preferences']['common']['currency'], '', $workorder['contract_sum']);
			}

			$workorder['contract_sum'] = str_replace(array(' ', ','), array('', '.'), $workorder['contract_sum']);

			if(abs((int) $workorder['contract_sum']) > 0)
			{
				$addition		 = 1 + ((int) $workorder['addition_percentage'] / 100);
				$combined_cost	 = (int) $workorder['contract_sum'] * $addition;
			}
			/* 			else if ($this->db->f('calculation') > 0)
			  {
			  $calculation = $this->db->f('calculation');
			  $config	= CreateObject('phpgwapi.config','property');
			  $config->read_repository();
			  $tax = 1+(($config->config_data['fm_tax'])/100);
			  $combined_cost = $calculation * $tax;
			  } */
			else
			{
				$combined_cost = (int) $workorder['budget'];
			}

			$this->db->query("SELECT bilagsnr FROM fm_ecobilag WHERE pmwrkord_code ='{$workorder['id']}'", __LINE__, __FILE__);
			$this->db->next_record();

			if($this->db->f('bilagsnr'))
			{
				$paid = 1;
			}

			$this->db->query("SELECT bilagsnr FROM fm_ecobilagoverf where pmwrkord_code = '{$workorder['id']}'", __LINE__, __FILE__);
			$this->db->next_record();
			if($this->db->f('bilagsnr'))
			{
				$paid = 2;
			}

			$value_set = array
			(
				'title'					 => $workorder['title'],
				'status'				 => $workorder['status'],
				'start_date'			 => $workorder['start_date'],
				'end_date'				 => $workorder['end_date'],
				'descr'					 => $workorder['descr'],
				'budget'				 => (int) $workorder['budget'],
//				'combined_cost'			=> $combined_cost,
				'key_deliver'			 => $workorder['key_deliver'],
				'key_fetch'				 => $workorder['key_fetch'],
				'account_id'			 => $workorder['b_account_id'],
				'rig_addition'			 => $workorder['addition_rs'],
				'addition'				 => $workorder['addition_percentage'],
				'charge_tenant'			 => $workorder['charge_tenant'],
				'vendor_id'				 => $workorder['vendor_id'],
				'user_id'				 => $workorder['user_id'],
				'ecodimb'				 => $workorder['ecodimb'],
				'category'				 => $workorder['cat_id'],
				'billable_hours'		 => $workorder['billable_hours'],
//				'contract_sum'			=> $workorder['contract_sum'],
				'approved'				 => $workorder['approved'],
				'continuous'			 => $workorder['continuous'],
				'fictive_periodization'	 => $workorder['fictive_periodization'],
				'mail_recipients'		 => isset($workorder['vendor_email']) && is_array($workorder['vendor_email']) ? implode(',', $workorder['vendor_email']) : '',
			);


			$this->db->query("SELECT closed AS is_closed FROM fm_workorder_status WHERE id = '{$workorder['status']}'");
			$this->db->next_record();
			$is_closed = !!$this->db->f('is_closed');


			if($is_closed)
			{
				$value_set['paid']			 = $paid						 = (isset($paid) ? $paid : 0);
				$value_set['paid_percent']	 = 100;
			}


			if(isset($workorder['extra']) && is_array($workorder['extra']))
			{
				foreach($workorder['extra'] as $input_name => $value)
				{
					$value_set[$input_name] = $value;
				}
			}

			if($workorder['location_code'])
			{
				$value_set['location_code'] = $workorder['location_code'];

				if($workorder['street_name'])
				{
					$address[]	 = $workorder['street_name'];
					$address[]	 = $workorder['street_number'];
					$address	 = $this->db->db_addslashes(implode(" ", $address));
				}

				if(!isset($address) || !$address)
				{
					$address = $this->db->db_addslashes($workorder['location_name']);
				}

				$value_set['address'] = $address;
			}

			$value_set = $this->db->validate_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_workorder SET {$value_set} WHERE id= {$workorder['id']}", __LINE__, __FILE__);

			$value_set_invoice					 = array();
			$value_set_invoice['spbudact_code']	 = $workorder['b_account_id'];
			$value_set_invoice['dime']			 = $workorder['cat_id'];
			$value_set_invoice['dimb']			 = $workorder['ecodimb'];

			$value_set_invoice = $this->db->validate_update($value_set_invoice);
			$this->db->query("UPDATE fm_ecobilag SET {$value_set_invoice} WHERE pmwrkord_code = '{$workorder['id']}'", __LINE__, __FILE__);

			$_active_period = array
			(
				'active_b_period'		 => isset($workorder['active_b_period']) && $workorder['active_b_period'] ? $workorder['active_b_period'] : array(),
				'active_orig_b_period'	 => isset($workorder['active_orig_b_period']) && $workorder['active_orig_b_period'] ? $workorder['active_orig_b_period'] : array()
			);

			$this->activate_period_from_budget($workorder['id'], $_active_period);

			unset($_close_period);
			unset($_active_period);


			if($workorder['delete_b_period'])
			{
				$this->db->query("SELECT sum(budget) AS budget FROM fm_workorder_budget WHERE order_id = '{$workorder['id']}'", __LINE__, __FILE__);
				$this->db->next_record();
				$old_budget = $this->db->f('budget');

				$this->delete_period_from_budget($workorder['id'], $workorder['delete_b_period']);

				$this->db->query("SELECT sum(budget) AS budget FROM fm_workorder_budget WHERE order_id = '{$workorder['id']}'", __LINE__, __FILE__);
				$this->db->next_record();
				$new_budget = $this->db->f('budget');

				$historylog->add('B', $workorder['id'], $new_budget, $old_budget);
			}

			$periodization_id = isset($workorder['budget_periodization']) && $workorder['budget_periodization'] ? (int) $workorder['budget_periodization'] : 0;
			if($combined_cost)
			{
				$this->db->query("SELECT sum(budget) AS budget FROM fm_workorder_budget WHERE order_id = '{$workorder['id']}'", __LINE__, __FILE__);
				$this->db->next_record();
				$old_budget = $this->db->f('budget');

				$this->_update_order_budget($workorder['id'], $workorder['budget_year'], $periodization_id, $workorder['budget'], $workorder['contract_sum'], $combined_cost);

				$this->db->query("SELECT sum(budget) AS budget FROM fm_workorder_budget WHERE order_id = '{$workorder['id']}'", __LINE__, __FILE__);
				$this->db->next_record();
				$new_budget = $this->db->f('budget');

				$historylog->add('B', $workorder['id'], $new_budget, $old_budget);
			}

			$this->_update_project_budget($workorder['project_id']);

			/* 			if($workorder['charge_tenant'])
			  {
			  $this->db->query("UPDATE fm_project set charge_tenant = 1 WHERE id =" . $workorder['project_id']);
			  }
			 */
//			$this->update_planned_cost($workorder['project_id']); // at project


			if($old_approved != $workorder['approved'])
			{
				if($workorder['approved'])
				{
					$historylog->add('OA', $workorder['id'], $workorder['approved'], $old_approved);
				}
				else//revoked
				{
					$historylog->add('OB', $workorder['id'], $workorder['approved'], $old_approved);
				}
				$check_pending_action = true;
			}

			$check_pending_action = false;
			if((float) $old_billable_hours != (float) $workorder['billable_hours'])
			{
				$historylog->add('H', $workorder['id'], $workorder['billable_hours'], $old_billable_hours);
				$receipt['message'][] = array('msg' => lang('billable hours has been updated'));
			}


			if($old_status != $workorder['status'])
			{
				$historylog->add('S', $workorder['id'], $workorder['status'], $old_status);
				$receipt['notice_owner'][]	 = lang('Status changed') . ': ' . $workorder['status'];
				$check_pending_action		 = true;
			}
			elseif($workorder['confirm_status'])
			{
				$check_pending_action		 = true;
				$historylog->add('SC', $workorder['id'], $workorder['status'], $old_status);
				$receipt['notice_owner'][]	 = lang('Status confirmed') . ': ' . $workorder['status'];
			}

			if($check_pending_action)
			{
				$this->db->query("SELECT * FROM fm_workorder_status WHERE id = '{$workorder['status']}'");
				$this->db->next_record();
				if($this->db->f('approved') || $workorder['approved'])
				{
					$action_params = array
					(
						'appname'			 => 'property',
						'location'			 => '.project.workorder',
						'id'				 => $workorder['id'],
						'responsible'		 => $this->account,
						'responsible_type'	 => 'user',
						'action'			 => 'approval',
						'remark'			 => '',
						'deadline'			 => ''
					);

					execMethod('property.sopending_action.close_pending_action', $action_params);
					unset($action_params);
				}
				if($this->db->f('in_progress'))
				{
					$action_params = array
					(
						'appname'			 => 'property',
						'location'			 => '.project.workorder',
						'id'				 => $workorder['id'],
						'responsible'		 => $workorder['vendor_id'],
						'responsible_type'	 => 'vendor',
						'action'			 => 'remind',
						'remark'			 => '',
						'deadline'			 => ''
					);

					execMethod('property.sopending_action.close_pending_action', $action_params);
					unset($action_params);
				}
				if($this->db->f('delivered') || $this->db->f('closed'))
				{
					$action_params = array
					(
						'appname'			 => 'property',
						'location'			 => '.project.workorder',
						'id'				 => $workorder['id'],
						'responsible'		 => $this->account,
						'responsible_type'	 => 'user',
						'action'			 => 'approval',
						'remark'			 => '',
						'deadline'			 => ''
					);

					execMethod('property.sopending_action.close_pending_action', $action_params);
					unset($action_params);
				}
			}

			if(isset($workorder['new_project_id']) && $workorder['new_project_id'] && ($workorder['new_project_id'] != $workorder['project_id']))
			{
				$new_project_id = (int) $workorder['new_project_id'];

				$this->db->query("SELECT ecodimb FROM fm_project WHERE id= $new_project_id", __LINE__, __FILE__);
				$this->db->next_record();
				$project_ecodimb = (int) $this->db->f('ecodimb');

				$this->db->query("UPDATE fm_workorder SET project_id = {$new_project_id} WHERE id= {$workorder['id']}", __LINE__, __FILE__);
				if($project_ecodimb)
				{
					$this->db->query("UPDATE fm_workorder SET ecodimb = {$project_ecodimb} WHERE id= {$workorder['id']}", __LINE__, __FILE__);
				}
				$historylog->add('NP', $workorder['id'], $new_project_id, $workorder['project_id']);
			}

			if($workorder['remark'])
			{
				$historylog->add('RM', $workorder['id'], $workorder['remark']);
			}

			$this->check_project_status($workorder['id']);

			if($this->db->transaction_commit())
			{

				$receipt['message'][] = array('msg' => lang('workorder %1 has been edited', $workorder['id']));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('workorder %1 has not been edited', $workorder['id']));
			}

			$receipt['id'] = $workorder['id'];
			return $receipt;
		}

		public function check_project_status($order_id)
		{
			$config = CreateObject('phpgwapi.config', 'property');
			$config->read_repository();

			$project_status_on_last_order_closed = isset($config->config_data['project_status_on_last_order_closed']) && $config->config_data['project_status_on_last_order_closed'] ? $config->config_data['project_status_on_last_order_closed'] : '';

			if($project_status_on_last_order_closed)
			{
				$this->db->query("SELECT project_id FROM fm_workorder WHERE id= '{$order_id}'", __LINE__, __FILE__);
				$this->db->next_record();
				$project_id = (int) $this->db->f('project_id');

				$this->db->query("SELECT count(id) AS orders_at_project FROM fm_workorder WHERE project_id= {$project_id}", __LINE__, __FILE__);
				$this->db->next_record();
				$orders_at_project = (int) $this->db->f('orders_at_project');

				$this->db->query("SELECT count(fm_workorder.id) AS closed_orders_at_project FROM fm_workorder {$this->join} fm_workorder_status ON (fm_workorder.status = fm_workorder_status.id) WHERE project_id= {$project_id} AND fm_workorder_status.closed = 1", __LINE__, __FILE__);
				$this->db->next_record();
				$closed_orders_at_project = (int) $this->db->f('closed_orders_at_project');

				$this->db->query("SELECT fm_project_status.closed AS closed_project, fm_project.status as old_status FROM fm_project {$this->join} fm_project_status ON (fm_project.status = fm_project_status.id) WHERE fm_project.id= {$project_id}", __LINE__, __FILE__);
				$this->db->next_record();
				$closed_project	 = !!$this->db->f('closed_project');
				$old_status		 = $this->db->f('old_status');

				if($orders_at_project == $closed_orders_at_project && !$closed_project)
				{
					$this->db->query("UPDATE fm_project SET status = '{$project_status_on_last_order_closed}' WHERE id= {$project_id}", __LINE__, __FILE__);

					$historylog_project = CreateObject('property.historylog', 'project');

					$historylog_project->add('S', $project_id, $project_status_on_last_order_closed, $old_status);
					$historylog_project->add('RM', $project_id, 'Status endret ved at siste bestilling er avsluttet');
				}
			}
		}

		function delete($workorder_id)
		{
			$this->db->transaction_begin();
			$this->interlink->delete_at_target('property', '.project.workorder', $workorder_id, $this->db);
			$this->db->query("DELETE FROM fm_workorder_budget WHERE order_id='{$workorder_id}'", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_workorder_history  WHERE  history_record_id='" . $workorder_id . "'", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_wo_hours WHERE workorder_id='" . $workorder_id . "'", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_orders WHERE id='" . $workorder_id . "'", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_workorder WHERE id='" . $workorder_id . "'", __LINE__, __FILE__);
			$this->db->transaction_commit();
		}

		public function get_user_list()
		{
			$values	 = array();
			$users	 = $GLOBALS['phpgw']->accounts->get_list('accounts', $start	 = -1, $sort	 = 'ASC', $order	 = 'account_lastname', $query, $offset	 = -1);
			$sql	 = 'SELECT DISTINCT user_id AS user_id FROM fm_workorder';
			$this->db->query($sql, __LINE__, __FILE__);

			$account_lastname = array();
			while($this->db->next_record())
			{
				$user_id = $this->db->f('user_id');
				if(isset($users[$user_id]))
				{
					$name				 = $users[$user_id]->__toString();
					$values[]			 = array
					(
						'id'	 => $user_id,
						'name'	 => $name
					);
					$account_lastname[]	 = $name;
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
			$config	 = CreateObject('phpgwapi.config', 'property');
			$config->read();
			$closed	 = isset($config->config_data['workorder_closed_status']) && $config->config_data['workorder_closed_status'] ? $config->config_data['workorder_closed_status'] : '';

			if(!$closed)
			{
				throw new Exception('property_soworkorder::close_orders() - "workorder_closed_status" not configured');
			}

			if($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}


			if($orders && is_array($orders))
			{
				$lang_closed			 = lang('closed');
				$historylog_workorder	 = CreateObject('property.historylog', 'workorder');

				foreach($orders as $id)
				{
					$this->db->query("SELECT type FROM fm_orders WHERE id='{$id}'", __LINE__, __FILE__);
					$this->db->next_record();
					switch($this->db->f('type'))
					{
						case 'workorder':
							$historylog_workorder->add('X', $id, $closed);
							$GLOBALS['phpgw']->db->query("UPDATE fm_workorder SET status='{$closed}' WHERE id = '{$id}'");
							$GLOBALS['phpgw']->db->query("UPDATE fm_workorder SET paid_percent=100 WHERE id= '{$id}'");
							$receipt['message'][] = array('msg' => lang('Workorder %1 is %2', $id, $lang_closed));
							break;
					}
				}
			}

			if(!$this->global_lock)
			{
				$this->db->transaction_commit();
			}

			return $receipt;
		}

		public function reopen_orders($orders)
		{
			if($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$config	 = CreateObject('phpgwapi.config', 'property');
			$config->read();
			$reopen	 = isset($config->config_data['workorder_reopen_status']) && $config->config_data['workorder_reopen_status'] ? $config->config_data['workorder_reopen_status'] : '';

			if(!$reopen)
			{
				throw new Exception('property_soworkorder::close_orders() - "workorder_reopen_status" not configured');
			}

			$lang_reopen = lang('Re-opened');

			$historylog_workorder = CreateObject('property.historylog', 'workorder');

			foreach($orders as $id)
			{
				$this->db->query("SELECT type FROM fm_orders WHERE id='{$id}'", __LINE__, __FILE__);
				$this->db->next_record();
				switch($this->db->f('type'))
				{
					case 'workorder':
						$historylog_workorder->add('R', $id, $reopen);
						$GLOBALS['phpgw']->db->query("UPDATE fm_workorder set status='{$reopen}' WHERE id = '{$id}'");
						$receipt['message'][] = array('msg' => lang('Workorder %1 is %2', $id, $lang_reopen));
				}
			}

			if(!$this->global_lock)
			{
				$this->db->transaction_commit();
			}
		}

		/**
		 * Get the percent of used funding
		 * @param integer $order_id
		 * @return float percent
		 */
		function get_order_budget_percent($order_id)
		{
			$_sub_budget	 = 0;
			$sum_actual_cost = 0;
			$sum_oblications = 0;

			$budget = $this->get_budget($order_id);
			foreach($budget as $entry)
			{
				if($entry['active'] == 1)
				{
					$_sub_budget += $entry['budget'];
					$sum_actual_cost += $entry['actual_cost'];
					$sum_oblications += $entry['sum_oblications'];
				}
			}
			$sum_budget	 = $_sub_budget == 0 ? 1 : $_sub_budget; // avoid zero-division
			$percent	 = round(($sum_actual_cost / $sum_budget) * 100, 1);

			$budget_info = array
			(
				'percent'		 => $percent,
				'budget'		 => $sum_budget,
				'actual_cost'	 => $sum_actual_cost,
				'obligation'	 => $sum_oblications
			);
			return $budget_info;
		}

		/**
		 * Get periodized budget for an order
		 * @param integer $order_id
		 * @param bool $calculate_fictive_periods
		 * @return array Array with budget information.
		 */
		function get_budget($order_id, $calculate_fictive_periods = true)
		{
			// Som før: Periodisering der det er definert
			// Som før: Enkelt posteringer for gjeldende periode der periodisering ikke er definert
			// Ny: Fiktiv periodisering over 12 mnd med startperiode for inneværende mnd for løpende som ikke er periodisert

			if(!$order_id)
			{
				return array();
			}
			$continuous = false;

			$cached_info = phpgwapi_cache::system_get('property', "budget_order_{$order_id}");

			if($cached_info)
			{
				return $cached_info;
			}

			$closed_period	 = array();
			$active_period	 = array();

			$sum_year_budget		 = array();
			$sum_year_combined_cost	 = array();

			$sql = "SELECT continuous, fm_workorder.start_date , fm_workorder_budget.budget, fm_workorder_budget.combined_cost,"
			. " project_type_id, year, month, active, closed, fictive_periodization"
			. " FROM fm_workorder"
			. " {$this->join} fm_project ON fm_workorder.project_id = fm_project.id"
			. " {$this->join} fm_workorder_status ON fm_workorder.status = fm_workorder_status.id"
			. " {$this->join} fm_workorder_budget ON fm_workorder.id = fm_workorder_budget.order_id"
			. " WHERE order_id = '{$order_id}'"
			. " ORDER BY year, month";

			$this->db->query($sql, __LINE__, __FILE__);
			$_check_periodization	 = array();
			$_order_budget			 = array();
			while($this->db->next_record())
			{
				$fictive_periodization	= !!$this->db->f('fictive_periodization');
				$project_type_id		= (int) $this->db->f('project_type_id');
				$year					= (int) $this->db->f('year');
				$month					= (int) $this->db->f('month');
				$continuous				= !!$this->db->f('continuous');
				$period					= sprintf("%s%02d", $year, $month
				);

				$budget					 = (int) $this->db->f('budget');
				$combined_cost			 = (int) $this->db->f('combined_cost');
				$closed_order			 = (int) $this->db->f('closed');
				$_order_budget[$period]	 = array
				(
					'order_id'		 => $order_id,
					'start_period'	 => date('Ym', $this->db->f('start_date')), //bigint
					'budget'		 => $budget,
					'combined_cost'	 => $combined_cost,
					'year'			 => $year,
					'month'			 => $month,
					'actual_cost'	 => 0, //for now..
					'closed_order'	 => $closed_order,
					'active_period'	 => (int) $this->db->f('active'),
				);

				$active_period[$period] = (int) $this->db->f('active');

				/**
				 * If the order is periodized - do not calculate fictitious periods
				 * */
				$_check_periodization[$year] += 1;
				if($calculate_fictive_periods)
				{
					$calculate_fictive_periods = $_check_periodization[$year] > 1 ? false : true;
				}
				if($continuous)
				{
					$sum_year_budget[$year] += $budget;
					$sum_year_combined_cost[$year] += $combined_cost;
				}
			}

			/**
			 * Fiktiv periodisering over 12 mnd med startperiode for inneværende mnd for løpende som ikke er periodisert
			 * Hopper over historiske år.
			 * Start-periode blir måned for første betaling dersom den er før inneværende måned
			 * ellers: Start-periode blir måned for start-dato for bestilling dersom den ligger frem i tid
			 * ellers: Dersom start-dato for bestilling er passert - blir start-periode inneværende måned.
			 * */
			$calculate_fictive_periods = $fictive_periodization ? $calculate_fictive_periods : false;
			$fictive_period						 = array();
			$exclude_from_fictive_period		 = array();
			$exclude_year_from_fictive_period	 = array();
			$order_budget						 = array();
			if($continuous && $calculate_fictive_periods)
			{
				//First payment;
				$sql = "SELECT periode FROM fm_orders_paid_or_pending_view"
				. " WHERE order_id = '{$order_id}'  AND periode > " . date('Y') . '00'
				. " ORDER BY periode ASC";

				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$current_paid_period = (int) $this->db->f('periode');

				/*
				  //FIXME total payment - if needed;
				  $sql = "SELECT sum(amount) AS actual_cost FROM  fm_orders_paid_or_pending_view"
				  . " WHERE order_id = '{$order_id}'  AND periode > " . date('Y') . '00';

				  $this->db->query($sql,__LINE__,__FILE__);
				  $this->db->next_record();
				  $_actual_cost = $this->db->f('actual_cost');
				  //_debug_array($_actual_cost);die();
				 */
				foreach($_order_budget as $_period => $_budget)
				{
					if($_period == "{$_budget['year']}00" && $_budget['year'] == date('Y'))
					{

						$order_budget[$_period] = $_budget;

						$active_period[$_period] = $active_period[$_period] ? 2 : 0;

						if($current_paid_period && $current_paid_period < (int) date('Ym'))
						{
							$_current_month = (int) substr($current_paid_period, -2);
						}
						else if((int) $_budget['start_period'] > (int) date('Ym'))
						{
							$_current_month = (int) substr($_budget['start_period'], -2);
						}
						else
						{
							$_current_month = date('n'); // Numeric representation of a month, without leading zeros 1 through 12
						}

						$_sum_year_combined_cost = $sum_year_combined_cost[$_budget['year']];


						$distribution_key = 1 / (13 - $_current_month);

						for($i = $_current_month; $i < 13; $i++)
						{
							$period = sprintf("%s%02d", $_budget['year'], $i
							);

							$fictive_period[$period]				 = true;
							$active_period[$period]					 = $active_period[$_period] ? 1 : 0;
							$order_budget[$period]					 = $_budget;
							$order_budget[$period]['budget']		 = $sum_year_budget[$_budget['year']] * $distribution_key;
							$order_budget[$period]['combined_cost']	 = $_sum_year_combined_cost * $distribution_key;
							$order_budget[$period]['active_period']	 = $_budget['active_period'];
							$order_budget[$period]['month']			 = $i;
							$closed_period[$period]					 = 0;//(int)$period < date('Ym');
						}

						$_start_month_remainig	 = $_current_month < 12 ? $_current_month + 1 : 0;
						$_start_year_remainig	 = $_budget['year'];
						$_start_period_remainig	 = array();

						$distribution_key_remaining = 1 / (13 - $_start_month_remainig);

						if($_start_month_remainig)
						{
							for($i = $_start_month_remainig; $i < 13; $i++)
							{
								$period = sprintf("%s%02d", $_budget['year'], $i
								);

								$_start_period_remainig[] = $period;
							}
						}
					}
					else
					{
						//FIXME
						$exclude_from_fictive_period[$_period]				 = true;
						$exclude_year_from_fictive_period[$_budget['year']]	 = true;

						$order_budget[$_period] = $_budget;
					}
					unset($_budget);
				}
			}
			else
			{
				$order_budget = $_order_budget;

				foreach($order_budget as $period => $_budget)
				{
					$this->db->query("SELECT closed FROM fm_workorder"
					. " {$this->join} fm_project ON fm_workorder.project_id = fm_project.id"
					. " {$this->join} fm_project_budget ON fm_project.id = fm_project_budget.project_id"
					. " WHERE fm_workorder.id = '{$_budget['order_id']}'"
					. " AND fm_project_budget.year = {$_budget['year']}"
					. " AND fm_project_budget.month = {$_budget['month']}", __LINE__, __FILE__);
					$this->db->next_record();
					$closed_period[$period] = (int) $this->db->f('closed');
				}
			}

			$sql						 = "SELECT periode, amount AS actual_cost, periodization, periodization_start"
			. " FROM fm_workorder {$this->join} fm_orders_paid_or_pending_view ON fm_workorder.id = fm_orders_paid_or_pending_view.order_id"
			. " WHERE order_id = '{$order_id}' ORDER BY periode ASC";
//_debug_array($sql);die();
			$this->db->query($sql, __LINE__, __FILE__);
			$orders_paid_or_pending		 = array();
			$orders_paid_or_pending_temp = array();

			while($this->db->next_record())
			{
				$orders_paid_or_pending_temp[] = array
				(
					'periode'				 => $this->db->f('periode'),
					'actual_cost'			 => $this->db->f('actual_cost'),
					'periodization'			 => (int) $this->db->f('periodization'),
					'periodization_start'	 => $this->db->f('periodization_start'),
				);
			}

			foreach($orders_paid_or_pending_temp as $entry)
			{
				if($entry['periodization'])
				{
					$periodization_start = $entry['periodization_start'] ? $entry['periodization_start'] : $entry['periodization'];

					$periodization_start_year	 = (int) substr($periodization_start, 0, 4);
					$periodization_start_month	 = (int) substr($periodization_start, -2);

					$sql = "SELECT month, value, dividend, divisor"
					. " FROM fm_eco_periodization_outline  WHERE periodization_id = {$entry['periodization']} ORDER BY month ASC";
					$this->db->query($sql, __LINE__, __FILE__);

					$periodization_outline = array();

					while($this->db->next_record())
					{
						$periodization_outline[] = array
						(
							'month'		 => $this->db->f('month'),
							'value'		 => $this->db->f('value'),
							'dividend'	 => $this->db->f('dividend'),
							'divisor'	 => $this->db->f('divisor')
						);
					}
					if(!$periodization_outline)
					{
						$periodization_outline[] = array
						(
							'month'	 => 1,
							'value'	 => 100,
						);
					}

					foreach($periodization_outline as $outline)
					{
						if($outline['dividend'] && $outline['divisor'])
						{
							$partial_actual_cost = $entry['actual_cost'] * $outline['dividend'] / $outline['divisor'];
						}
						else
						{
							$partial_actual_cost = $entry['actual_cost'] * $outline['value'] / 100;
						}

						$_period_month = (int) $periodization_start_month + (int) $outline['month'] - 1;

						$_future_year_count = floor(($_period_month - 1) / 12);

						$_periodization_start_year = $periodization_start_year + $_future_year_count;

						$_month = $_period_month - ($_future_year_count * 12);

						$orders_paid_or_pending[] = array
						(
							'periode'		 => sprintf("%s%02d", $_periodization_start_year, $_month),
							'actual_cost'	 => $partial_actual_cost,
							'periodization'	 => $entry['periodization'],
						);
					}
				}
				else
				{
					$orders_paid_or_pending[] = $entry;
				}
			}

			foreach($orders_paid_or_pending as $_orders_paid_or_pending)
			{

				$periode		 = $_orders_paid_or_pending['periode'];
				$_dummy_period	 = $periode ? $periode : date('Y') . '00';

				if(!$periode)
				{
					$periode = date('Ym');
				}

				$year = substr($periode, 0, 4);

				$_found = false;

				if($_start_month_remainig && $year == $_start_year_remainig)
				{
					if(!in_array($periode, $_start_period_remainig))
					{
						$_temp_obligation = $order_budget[$periode]['combined_cost'] - $_orders_paid_or_pending['actual_cost'];
						//FIXME
						if(((int)$order_budget[$periode]['combined_cost'] * (int)$_orders_paid_or_pending['actual_cost']) > 0)
						{
							$_sum_year_remaining_cost += $_temp_obligation;
							$order_budget[$periode]['combined_cost'] -= $_temp_obligation;
						}
					}
				}

				if(isset($_orders_paid_or_pending['periodization']) && $_orders_paid_or_pending['periodization'] && !isset($exclude_year_from_fictive_period[$year]))
				{

					$order_budget[$periode]['actual_cost'] += $_orders_paid_or_pending['actual_cost'];
					$order_budget[$periode]['actual_period'] = $periode;
					$order_budget[$periode]['year']			 = $year;
					$order_budget[$periode]['month']		 = substr($periode, -2);
					$order_budget[$periode]['closed_order']	 = $closed_order;

					$_found = true;
				}
				else if(isset($order_budget[$periode]))
				{
					$order_budget[$periode]['actual_cost'] += $_orders_paid_or_pending['actual_cost'];
					$order_budget[$periode]['actual_period'] = $periode;
					$_found									 = true;
				}
				else
				{
					for($i = 0; $i < 13; $i++)
					{
						$_period = $year . sprintf("%02s", $i);
						if(isset($order_budget[$_period]))
						{
							$order_budget[$_period]['actual_cost'] += $_orders_paid_or_pending['actual_cost'];
							$order_budget[$_period]['actual_period'] = $periode;
							$_found									 = true;
							break;
						}
					}
				}

				if(!$_found)
				{
					$order_budget[$_dummy_period]['year']			 = substr($_dummy_period, 0, 4);
					$order_budget[$_dummy_period]['month']			 = substr($_dummy_period, -2);
					$order_budget[$_dummy_period]['actual_cost'] += $_orders_paid_or_pending['actual_cost'];
					$order_budget[$_dummy_period]['actual_period']	 = $periode;
				}
			}
//_debug_array($order_budget);die();
			$sort_period		 = array();
			$values				 = array();
			$_current_period	 = date('Ym');
			$_delay_period_sum	 = 0;
			$_delay_period		 = false;

			foreach($order_budget as $period => $_budget)
			{

				if(isset($_start_period_remainig) && in_array($period, $_start_period_remainig))
				{
					$_budget['combined_cost'] += $_sum_year_remaining_cost * $distribution_key_remaining;
				}

				$_sum_orders		 = 0;
				$_sum_oblications	 = 0;
				$_actual_cost		 = 0;

				$_actual_cost += $_budget['actual_cost'];
				$_sum_orders += $_budget['combined_cost'];

				if(!$_budget['closed_order'])
				{
					if($active_period[$period] == 1)
					{
						$_sum_oblications += $_budget['combined_cost'];
						if(((int)$_budget['budget'] * (int)$_budget['actual_cost']) > 0)
						{
							$_sum_oblications -= $_budget['actual_cost'];
						}

						if($_budget['budget'] >= 0)
						{
							if($_sum_oblications < 0)
							{
								$_sum_oblications = 0;
							}
						}
						else // income
						{
							if($_sum_oblications > 0)
							{
								$_sum_oblications = 0;
							}
						}
					}
				}

				//override if periode is closed
				if(!isset($active_period[$period]) || !$active_period[$period] == 1)
				{
					$_sum_oblications = 0;
				}

				//override if periode is closed
				if(isset($closed_period[$period]) && $closed_period[$period])
				{
					$_sum_oblications = 0;
				}

				if(isset($active_period[$period]) && $active_period[$period] == 1 && $_delay_period_sum && !$_delay_period)
				{
					$_sum_oblications += $_delay_period_sum;
					$_delay_period_sum = 0;
				}
				$_delay_period = false;

				$values[] = array
				(
					'year'				 => $_budget['year'],
					'month'				 => $_budget['month'] > 0 ? sprintf("%02s", $_budget['month']) : '00',
					'period'			 => $period,
					'budget'			 => $_budget['budget'],
					'combined_cost'		 => $_budget['combined_cost'],
					'sum_orders'		 => $_sum_orders,
					'sum_oblications'	 => $_sum_oblications,
					'actual_cost'		 => $_actual_cost,
					'closed_order'		 => $_budget['closed_order'],
					'actual_period'		 => $_budget['actual_period']
				);

				$sort_period[] = $period;
			}

			if($values)
			{
				array_multisort($sort_period, SORT_ASC, $values);
			}

//_debug_array($values);die();
			$deviation_acc	 = 0;
			$budget_acc		 = 0;
			$_year = 0;
			foreach($values as &$entry)
			{
				/**
				 * operation: start over each year
				 */
				if($project_type_id == 1 && $_year != $entry['year'])
				{
					$_year = $entry['year'];
					$deviation_acc	 = 0;
					$budget_acc		 = 0;
				}

				if( abs($entry['actual_cost']) > 0 )
				{
					$_diff_start	= abs($entry['budget']) > 0 ? $entry['budget'] : $entry['sum_orders'];
					$entry['diff']	= $_diff_start - $entry['sum_oblications'] - $entry['actual_cost'];

					$_deviation		= $entry['budget'] - $entry['actual_cost'];
					$deviation		= $_deviation;
					$deviation_acc += $deviation;
				}
				else
				{
					$entry['diff']	 = 0;
					$deviation		 = 0;
				}

				$entry['deviation_period'] = $deviation;
				$budget_acc +=$entry['budget'];

				$entry['deviation_acc'] = abs($deviation) > 0 ? $deviation_acc : 0;


				$entry['deviation_percent_period']	 = $deviation / $entry['budget'] * 100;
				$entry['deviation_percent_acc']		 = $entry['deviation_acc'] / $budget_acc * 100;

				$entry['closed']	 = isset($closed_period[$entry['period']]) && $closed_period[$entry['period']];
				$entry['active']	 = isset($active_period[$entry['period']]) && $active_period[$entry['period']] ? $active_period[$entry['period']] : 0;
				$entry['fictive']	 = isset($fictive_period[$entry['period']]) && $fictive_period[$entry['period']];
			}

			phpgwapi_cache::system_set('property', "budget_order_{$order_id}", $values);

			return $values;
		}

		/**
		 * Delete entries from periodized workorder budget
		 *
		 * @return void
		 */
		function delete_period_from_budget($order_id, $data)
		{
			foreach($data as $entry)
			{
				$when	 = explode('_', $entry);
				$sql	 = "DELETE FROM fm_workorder_budget WHERE order_id = {$order_id} AND year = " . (int) $when[0] . ' AND month = ' . (int) $when[1];
				$this->db->query($sql, __LINE__, __FILE__);
			}
		}

		/**
		 * Set active status on budget periods
		 *
		 * @return void
		 */
		function activate_period_from_budget($order_id, $data)
		{
			$close_period	 = array();
			$open_period	 = array();
//_debug_array($data);die();
			foreach($data['active_orig_b_period'] as $period)
			{
				if(!in_array($period, $data['active_b_period']))
				{
					$inactive_period[] = $period;
				}
			}

			foreach($data['active_b_period'] as $period)
			{
				if(!in_array($period, $data['active_orig_b_period']))
				{
					$active_period[] = $period;
				}
			}

			foreach($active_period as $period)
			{
				$when	 = explode('_', $period);
				$sql	 = "UPDATE fm_workorder_budget SET active = 1 WHERE order_id = {$order_id} AND year =" . (int) $when[0] . ' AND month = ' . (int) $when[1];
				$this->db->query($sql, __LINE__, __FILE__);
			}

			foreach($inactive_period as $period)
			{
				$when	 = explode('_', $period);
				$sql	 = "UPDATE fm_workorder_budget SET active = 0 WHERE order_id = {$order_id} AND year =" . (int) $when[0] . ' AND month = ' . (int) $when[1];
				$this->db->query($sql, __LINE__, __FILE__);
			}
		}

		/**
		 * Recalculate actual cost from payment history for all workorders
		 *
		 * @return void
		 */
		function recalculate()
		{
			$this->db->transaction_begin();

			set_time_limit(0);


			$this->db->query("SELECT id, budget,project_type_id FROM fm_project ORDER BY id ASC", __LINE__, __FILE__);

			$projects = array();
			while($this->db->next_record())
			{
				$projects[] = array
				(
					'id'				 => (int) $this->db->f('id'),
					'budget'			 => (int) $this->db->f('budget'),
					'project_type_id'	 => (int) $this->db->f('project_type_id')
				);
			}


			foreach($projects as $project)
			{
				if($project['project_type_id'] == 3)//buffer
				{
					$this->db->query("SELECT sum(amount_in) AS amount_in, sum(amount_out) AS amount_out FROM fm_project_buffer_budget WHERE buffer_project_id = " . (int) $project['id'], __LINE__, __FILE__);
					$this->db->next_record();
					$new_budget = (int) $this->db->f('amount_in') - (int) $this->db->f('amount_out');
				}
				else
				{
					$this->db->query("SELECT sum(budget) AS sum_budget FROM fm_project_budget WHERE active = 1 AND project_id = " . (int) $project['id'], __LINE__, __FILE__);
					$this->db->next_record();
					$new_budget = (int) $this->db->f('sum_budget');
				}

				if($project['budget'] != $new_budget)
				{
					$this->db->query("UPDATE fm_project SET budget = {$new_budget} WHERE id = " . (int) $project['id'], __LINE__, __FILE__);
				}
			}

			$this->db->query("SELECT id FROM fm_workorder ORDER BY id ASC", __LINE__, __FILE__);

			$orders = array();
			while($this->db->next_record())
			{
				$orders[$this->db->f('id')] = true;
			}

			execMethod('property.soXport.update_actual_cost_from_archive', $orders);

			$config = CreateObject('phpgwapi.config', 'property');
			$config->read_repository();

			if(isset($config->config_data['location_at_workorder']) && $config->config_data['location_at_workorder'])
			{
				$this->db->query("SELECT id, project_id FROM fm_workorder WHERE location_code IS NULL", __LINE__, __FILE__);
				$orders = array();
				while($this->db->next_record())
				{
					$orders[] = array
					(
						'id'		 => $this->db->f('id'),
						'project_id' => $this->db->f('project_id')
					);
				}

				foreach($orders as $order)
				{
					$this->db->query("SELECT location_code FROM fm_project WHERE id = {$order['project_id']}", __LINE__, __FILE__);
					$this->db->next_record();
					$location_code = $this->db->f('location_code');
					$this->db->query("UPDATE fm_workorder SET location_code = '{$location_code}' WHERE id = {$order['id']}", __LINE__, __FILE__);
				}
			}

			$this->db->transaction_commit();
		}

		/**
		 * Add budget to project if missing.
		 * @param integer $project_id
		 */
		protected function _update_project_budget($project_id)
		{
			$soproject = CreateObject('property.soproject');

			$years	= array();
			$ids	= array();
			$this->db->query("SELECT id FROM fm_workorder WHERE project_id = {$project_id}", __LINE__, __FILE__);
			while($this->db->next_record())
			{
				$ids[] = $this->db->f('id');
			}
			$this->db->query("SELECT DISTINCT year FROM fm_workorder_budget WHERE order_id IN (" . implode(',', $ids) . ')', __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$years[] = $this->db->f('year');
			}
			foreach($years as $_year)
			{
				$soproject->check_and_update_project_budget($project_id, $_year);
			}
		}

		/**
		 * Transfer budget and cost from one year to the next
		 *
		 * */
		public function transfer_budget($id, $budget, $year)
		{
			if($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$id			 = (int) $id;
			$year		 = (int) $year;
			$latest_year = (int) $budget['latest_year'];

			$sql = "SELECT periodization_id, project_type_id, continuous"
			. " FROM fm_workorder"
			. " {$this->join} fm_project ON fm_workorder.project_id = fm_project.id"
			. " WHERE fm_workorder.id = {$id}";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$periodization_id	 = $this->db->f('periodization_id');
			$project_type_id	 = $this->db->f('project_type_id');
			$continuous			 = $this->db->f('continuous');

//~ * Løpende bestillinger settes til null via masseoppdatering, evt at nytt budsjett tastes inn i masseoppdatering (siste er ønskelig).
//~ * For Driftsbestillinger settes Betalt til null, Budsjett settes til restforpliktelse (budsjett tidligere trekkes ned med restforpliktelse)
//~ * For Investeringsbestillinger skal disse ikke se på år

			phpgwapi_cache::system_clear('property', "budget_order_{$id}");

			if($continuous)
			{
				$this->db->query("UPDATE fm_workorder_budget SET active = 0 WHERE order_id = {$id} AND year = {$latest_year}", __LINE__, __FILE__);
				if($budget['budget_amount'])
				{
					$this->_update_order_budget($id, $year, $periodization_id, (int) $budget['budget_amount'], (int) $budget['budget_amount'], (int) $budget['budget_amount'], $action = 'update', true);
				}
			}
			else if($project_type_id == 1)//operation
			{
				/*
				  if(abs($budget['obligation']) > 0)
				  {
				  $transferred = $this->_update_order_budget($id, $latest_year, $periodization_id, $budget['obligation'], $contract_sum, $combined_cost = 0, $action = 'update', $activate = 0);
				  }
				 */
				$this->db->query("SELECT sum(amount) as paid FROM fm_workorder"
				. " {$this->join} fm_orders_paid_or_pending_view ON fm_workorder.id = fm_orders_paid_or_pending_view.order_id"
				. " WHERE periode > {$latest_year}00 AND periode < {$latest_year}13 AND fm_workorder.id = {$id}", __LINE__, __FILE__);
				$this->db->next_record();
				$paid_last_year = $this->db->f('paid');

				$transferred = $this->_update_order_budget($id, $latest_year, $periodization_id, $paid_last_year, $paid_last_year, $paid_last_year, $action		 = 'update', $activate	 = 0);

				$this->_update_order_budget($id, $year, $periodization_id, (int) $budget['budget_amount'], (int) $budget['budget_amount'], (int) $budget['budget_amount'], $action = 'update', true);

				$this->db->query("UPDATE fm_workorder_budget SET active = 0 WHERE order_id = {$id} AND year = {$latest_year}", __LINE__, __FILE__);

				$last_day_of_year	 = mktime(13, 0, 0, 12, 31, date("Y"));
				$now				 = time();

				$this->db->query("UPDATE fm_workorder SET start_date = {$now}, end_date = {$last_day_of_year} WHERE id = {$id}", __LINE__, __FILE__);
			}
			else if($project_type_id == 2)//investment
			{
				// total budget
				$this->db->query("SELECT sum(combined_cost) AS budget FROM fm_workorder_budget WHERE order_id = {$id} AND year = {$latest_year}", __LINE__, __FILE__);
				$this->db->next_record();
				$last_budget = $this->db->f('budget');
				if(!abs($last_budget) > 0)
				{
					$this->_update_order_budget($id, $year, $periodization_id, 0, 0, 0, 'update', true);
					if(!$this->global_lock)
					{
						$this->db->transaction_commit();
					}

					return;
//					throw new Exception('property_workorder::transfer_budget() - no budget to transfer for this investment order: ' . $id);
				}

				//paid last year
				$this->db->query("SELECT sum(amount) as paid FROM fm_workorder"
				. " {$this->join} fm_orders_paid_or_pending_view ON fm_workorder.id = fm_orders_paid_or_pending_view.order_id"
				. " WHERE periode > {$latest_year}00 AND periode < {$latest_year}13 AND fm_workorder.id = {$id}", __LINE__, __FILE__);
				$this->db->next_record();
				$paid_last_year = $this->db->f('paid');

				$subtract = $last_budget - $paid_last_year;

				$_perform_subtraction = false;

				if($last_budget >= 0)
				{
					if($paid_last_year <= $last_budget)
					{
						$_perform_subtraction = true;
					}
				}
				else
				{
					if($paid_last_year >= $last_budget)
					{
						$_perform_subtraction = true;
					}
				}

				if($_perform_subtraction)
				{
					$transferred = $this->_update_order_budget($id, $latest_year, $periodization_id, $paid_last_year, $paid_last_year, $paid_last_year, $action		 = 'update', true);
					$new_budget	 = $last_budget - $paid_last_year;
				}
				else
				{
					$new_budget = 0;
				}

				$this->_update_order_budget($id, $year, $periodization_id, $new_budget, $new_budget, $new_budget, $action = 'update', true);
			}
//die();
			if(!$this->global_lock)
			{
				$this->db->transaction_commit();
			}
		}

		/**
		 * Maintain correct periodizing in relation to current project (in case the order is moved)
		 *
		 * */
		public function update_order_budget($order_id)
		{
			$this->db->query("SELECT fm_workorder_budget.year, periodization_id,"
			. " sum(fm_workorder_budget.budget) AS budget, sum(fm_workorder_budget.combined_cost) AS combined_cost,"
			. " sum(fm_workorder_budget.contract_sum) AS contract_sum"
			. " FROM fm_workorder {$this->join} fm_project ON fm_workorder.project_id = fm_project.id"
			. " {$this->join} fm_workorder_budget ON fm_workorder.id = fm_workorder_budget.order_id"
			. " WHERE fm_workorder.id = '{$order_id}' GROUP BY year, periodization_id", __LINE__, __FILE__);

			while($this->db->next_record())
			{
				$start_date			 = $this->db->f('start_date');
				$periodization_id	 = (int) $this->db->f('periodization_id');
				$budget				 = $this->db->f('budget');
				$contract_sum		 = $this->db->f('contract_sum');
				$combined_cost		 = $this->db->f('combined_cost');
				$this->_update_order_budget($order_id, date('Y', $start_date), $periodization_id, $budget, $contract_sum, $combined_cost);
			}
		}

		public function _update_order_budget($order_id, $year, $periodization_id, $budget, $contract_sum, $combined_cost = 0, $action = 'update', $activate = 0)
		{
			$year = $year ? (int) $year : date('Y');
//_debug_array($year);
			if($action == 'subtract')
			{
				$incoming_budget = $budget;
				$acc_partial	 = 0;

				$orig_budget = $this->get_budget($order_id, false);
//_debug_array($orig_budget);
				$hit		 = false;
				foreach($orig_budget as $entry)
				{
					if($entry['year'] == $year && $entry['active'])
					{
						$partial_budget	 = 0;
						$month			 = (int) substr($entry['period'], -2);
						$hit			 = true; // found at least one.
						if($entry['sum_orders'] >= 0)
						{
							if($entry['diff'] > 0)
							{
								if($entry['diff'] < $budget)
								{

									$partial_budget = $entry['diff'];
									$budget -= $partial_budget;
								}
								else
								{
									$partial_budget	 = $budget;
									$partial_budget	 = $partial_budget > 0 ? $partial_budget : 0;
									$budget			 = 0;
								}
							}
						}
						else if($entry['sum_orders'] < 0)
						{
							if($entry['diff'] < 0)
							{
								if($entry['diff'] > $budget)
								{
									$partial_budget = $entry['diff'];
									$budget -= $partial_budget;
								}
								else
								{
									$partial_budget	 = $budget;
									$partial_budget	 = $partial_budget < 0 ? $partial_budget : 0;
									$budget			 = 0;
								}
							}
						}
						if($partial_budget)
						{
							$acc_partial += $partial_budget;
							$this->_update_budget($order_id, $year, $month, $partial_budget, $partial_budget, $partial_budget, $action);
						}
					}
				}
//_debug_array($budget);
//die();
				if($hit && $budget) // still some left to go - place it on the last one
				{

					$acc_partial += $budget;

					$this->_update_budget($order_id, $year, $month, $budget, $budget, $budget, $action);
				}

				if(!$hit)
				{
//					throw new Exception('property_soproject::_update_order_budget() - found no active budget to transfer from');
				}

				return $acc_partial;
			}

			$periodization_id		 = (int) $periodization_id;
			$periodization_outline	 = array();

			if($periodization_id)
			{
				$this->db->query("SELECT month, value,dividend,divisor FROM fm_eco_periodization_outline WHERE periodization_id = {$periodization_id} ORDER BY month ASC", __LINE__, __FILE__);
				while($this->db->next_record())
				{
					$periodization_outline[] = array
					(
						'month'		 => $this->db->f('month'),
						'value'		 => $this->db->f('value'),
						'dividend'	 => $this->db->f('dividend'),
						'divisor'	 => $this->db->f('divisor')
					);
				}
			}
			else
			{
				$periodization_outline[] = array
				(
					'month'		 => 0,
					'value'		 => 100,
					'dividend'	 => 1,
					'divisor'	 => 1,
				);
			}
			$sql = "DELETE FROM fm_workorder_budget WHERE order_id = '{$order_id}' AND year = {$year}";
			$this->db->query($sql, __LINE__, __FILE__);

			foreach($periodization_outline as $outline)
			{
				if($outline['dividend'] && $outline['divisor'])
				{
					$partial_budget		 = $budget * $outline['dividend'] / $outline['divisor'];
					$partial_cost		 = $combined_cost * $outline['dividend'] / $outline['divisor'];
					$partial_contract	 = $contract_sum * $outline['dividend'] / $outline['divisor'];
				}
				else
				{
					$partial_budget		 = $budget * $outline['value'] / 100;
					$partial_cost		 = $combined_cost * $outline['value'] / 100;
					$partial_contract	 = $contract_sum * $outline['value'] / 100;
				}

				$this->_update_budget($order_id, $year, $outline['month'], $partial_budget, $partial_contract, $partial_cost);
			}
		}

		private function _update_budget($order_id, $year, $month, $budget, $contract_sum, $combined_cost)
		{
			$month	 = (int) $month;
			$budget	 = (int) $budget;
			$now	 = time();

			$sql = "SELECT order_id FROM fm_workorder_budget WHERE order_id = {$order_id} AND year = {$year} AND month = {$month}";

			$this->db->query($sql, __LINE__, __FILE__);
			if($this->db->next_record())
			{
				$sql = "UPDATE fm_workorder_budget SET budget = '{$budget}', contract_sum ='{$contract_sum}', combined_cost = '{$combined_cost}', modified_date = {$now} WHERE order_id = '{$order_id}' AND year = {$year} AND month = {$month}";
				$this->db->query($sql, __LINE__, __FILE__);
			}
			else
			{
				$value_set = array
				(
					'order_id'		 => $order_id,
					'year'			 => $year,
					'month'			 => $month,
					'budget'		 => $budget,
					'contract_sum'	 => $contract_sum,
					'combined_cost'	 => $combined_cost,
					'user_id'		 => $this->account,
					'entry_date'	 => $now,
					'modified_date'	 => $now
				);

				$cols	 = implode(',', array_keys($value_set));
				$values	 = $this->db->validate_insert(array_values($value_set));
//_debug_array($values);die();

				$this->db->query("INSERT INTO fm_workorder_budget ({$cols}) VALUES ({$values})", __LINE__, __FILE__);
			}
		}

	}	