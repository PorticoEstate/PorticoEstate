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
	class property_soproject
	{

		var $total_records	 = 0;
		private $global_lock	 = false;

		function __construct()
		{
			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon	 = CreateObject('property.bocommon');
			$this->interlink = CreateObject('property.interlink');
			$this->custom	 = createObject('property.custom_fields');

			$this->db		 = & $GLOBALS['phpgw']->db;
			$this->db2		 = clone ($this->db);
			$this->join		 = & $this->db->join;
			$this->left_join = & $this->db->left_join;
			$this->like		 = & $this->db->like;

			$this->acl		 = & $GLOBALS['phpgw']->acl;
			$this->acl->set_account_id($this->account);
			$this->grants	 = $this->acl->get_grants('property', '.project');
			$this->config	 = CreateObject('phpgwapi.config', 'property');
			$this->config->read();
		}

		function select_status_list()
		{
			$this->db->query("SELECT id, descr FROM fm_project_status ORDER BY id ");
			$status = array();
			while($this->db->next_record())
			{
				$status[] = array
				(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('descr', true)
				);
			}
			return $status;
		}

		function select_branch_list()
		{
			$this->db->query("SELECT id, descr FROM fm_branch ORDER BY id ");

			$branch = array();
			while($this->db->next_record())
			{
				$branch[] = array
				(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('descr', true)
				);
			}
			return $branch;
		}

		function select_key_location_list()
		{
			$this->db->query("SELECT id, descr FROM fm_key_loc ORDER BY descr ");
			$location = array();
			while($this->db->next_record())
			{
				$location[] = array
				(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('descr', true)
				);
			}
			return $location;
		}

		function read($data)
		{
			$start			 = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$filter			 = $data['filter'] ? (int) $data['filter'] : 0;
			$query			 = isset($data['query']) ? $data['query'] : '';
			$sort			 = isset($data['sort']) ? $data['sort'] : 'DESC';
			$order			 = isset($data['order']) ? $data['order'] : '';
			$cat_id			 = isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
			$status_id		 = isset($data['status_id']) && $data['status_id'] ? $data['status_id'] : 'open';
			$start_date		 = isset($data['start_date']) && $data['start_date'] ? (int) $data['start_date'] : 0;
			$end_date		 = isset($data['end_date']) && $data['end_date'] ? (int) $data['end_date'] : 0;
			$allrows		 = isset($data['allrows']) ? $data['allrows'] : '';
			$wo_hour_cat_id	 = isset($data['wo_hour_cat_id']) ? $data['wo_hour_cat_id'] : '';
			$district_id	 = isset($data['district_id']) ? $data['district_id'] : '';
			$dry_run		 = isset($data['dry_run']) ? $data['dry_run'] : '';
			$criteria		 = isset($data['criteria']) && $data['criteria'] ? $data['criteria'] : array();
			$project_type_id = $data['project_type_id'] ? (int) $data['project_type_id'] : 0;
			$filter_year	 = isset($data['filter_year']) ? $data['filter_year'] : '';

			$sql = $this->bocommon->fm_cache('sql_project_' . !!$wo_hour_cat_id);

			if(!$sql)
			{
				$entity_table = 'fm_project';

				$cols			 = $entity_table . '.location_code';
				$cols_return[]	 = 'location_code';

				$cols.= ",project_type_id";
				$cols_return[] = 'project_type_id';
				/*
				  $uicols['input_type'][]		= 'hidden';
				  $uicols['name'][]			= 'project_type_id';
				  $uicols['descr'][]			= '';
				  $uicols['statustext'][]		= '';
				  $uicols['exchange'][]		= false;
				  $uicols['align'][] 			= '';
				  $uicols['datatype'][]		= '';
				  $uicols['formatter'][]		= '';
				  $uicols['classname'][]		= '';
				  $uicols['sortable'][]		= '';
				 */

				$cols .= ",$entity_table.id as project_id";
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

				$cols .= ", project_group";
				$cols_return[]			 = 'project_group';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'project_group';
				$uicols['descr'][]		 = lang('group');
				$uicols['statustext'][]	 = lang('Project group');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = '';
				$uicols['classname'][]	 = 'rightClasss';
				$uicols['sortable'][]	 = true;

				$cols .= ", fm_project_status.descr as status";
				$cols_return[]			 = 'status';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'status';
				$uicols['descr'][]		 = lang('status');
				$uicols['statustext'][]	 = lang('status');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = '';
				$uicols['classname'][]	 = '';
				$uicols['sortable'][]	 = true;

				$cols.= ",$entity_table.entry_date";
				$cols_return[]	 = 'entry_date';
				$cols.= ",$entity_table.start_date";
				$cols_return[]	 = 'start_date';
				$cols.= ",$entity_table.end_date";
				$cols_return[]	 = 'end_date';
				$cols.= ",$entity_table.ecodimb";
				$cols_return[]	 = 'ecodimb';

				/*
				  $uicols['input_type'][]		= 'text';
				  $uicols['name'][]			= 'entry_date';
				  $uicols['descr'][]			= lang('entry date');
				  $uicols['statustext'][]		= lang('Project entry date');
				  $uicols['exchange'][]		= false;
				  $uicols['align'][] 			= '';
				  $uicols['datatype'][]		= '';
				  $uicols['formatter'][]		= '';
				  $uicols['classname'][]		= '';
				  $uicols['sortable'][]		= '';
				 */
				$cols.= ",$entity_table.name as name";
				$cols_return[]			 = 'name';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'name';
				$uicols['descr'][]		 = lang('name');
				$uicols['statustext'][]	 = lang('Project name');
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
				  $uicols['descr'][]			= lang('loc1_name');
				  $uicols['statustext'][]		= lang('loc1_name');
				  $uicols['exchange'][]		= false;
				  $uicols['align'][] 			= '';
				  $uicols['datatype'][]		= '';
				  $uicols['formatter'][]		= '';
				  $uicols['classname'][]		= '';
				  $uicols['sortable'][]		= '';
				 */
				$cols.= ",account_lid as coordinator";
				$cols_return[]			 = 'coordinator';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'coordinator';
				$uicols['descr'][]		 = lang('Coordinator');
				$uicols['statustext'][]	 = lang('Project coordinator');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = '';
				$uicols['classname'][]	 = '';
				$uicols['sortable'][]	 = '';

//				$cols.= ",(fm_project.budget + fm_project.reserve) as budget";
				$cols_return[]			 = 'budget';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'budget';
				$uicols['descr'][]		 = lang('budget');
				$uicols['statustext'][]	 = lang('Project budget');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = 'myFormatCount2';
				$uicols['classname'][]	 = 'rightClasss';
				$uicols['sortable'][]	 = '';

//				$cols .= ',sum(fm_workorder.combined_cost) as combined_cost';
//				$cols_return[] = 'combined_cost';
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'obligation';
				$uicols['descr'][]		 = lang('obligation');
				$uicols['statustext'][]	 = lang('obligation');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = 'myFormatCount2';
				$uicols['classname'][]	 = 'rightClasss';
				$uicols['sortable'][]	 = '';

				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = 'actual_cost';
				$uicols['descr'][]		 = lang('Actual cost');
				$uicols['statustext'][]	 = lang('Actual cost - paid so far');
				$uicols['exchange'][]	 = false;
				$uicols['align'][]		 = '';
				$uicols['datatype'][]	 = '';
				$uicols['formatter'][]	 = 'myFormatCount2';
				$uicols['classname'][]	 = 'rightClasss';
				$uicols['sortable'][]	 = '';

//				$cols .= ',planned_cost';
//				$cols_return[] = 'planned_cost';

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

				$cols.= ",$entity_table.user_id";

//				$cols .= ',sum(fm_workorder.billable_hours) as billable_hours';
//				$cols_return[] = 'billable_hours';

				$joinmethod	 = " {$this->join} phpgw_accounts ON ($entity_table.coordinator = phpgw_accounts.account_id))";
				$paranthesis = '(';

				$joinmethod .= " {$this->join} fm_project_status ON ($entity_table.status = fm_project_status.id))";
				$paranthesis .='(';

				$joinmethod .= " {$this->left_join} fm_project_budget ON ($entity_table.id = fm_project_budget.project_id))";
				$paranthesis .='(';
				$joinmethod .= " {$this->left_join} fm_project_buffer_budget ON ($entity_table.id = fm_project_buffer_budget.buffer_project_id))";
				$paranthesis .='(';

				/*
				  $joinmethod .= " {$this->left_join} fm_workorder ON ($entity_table.id = fm_workorder.project_id))";
				  $paranthesis .='(';
				 */
				//----- wo_hour_status

				if($wo_hour_cat_id)
				{
					$joinmethod .= " {$this->join} fm_workorder ON ($entity_table.id = fm_workorder.project_id))";
					$paranthesis .='(';

					$joinmethod .= " {$this->join} fm_wo_hours ON (fm_workorder.id = fm_wo_hours.workorder_id))";
					$paranthesis .='(';

					$joinmethod .= " $this->join fm_wo_hours_category ON (fm_wo_hours.category = fm_wo_hours_category.id))";
					$paranthesis .='(';
				}

				//----- wo_hour_status

				$sql = $this->bocommon->generate_sql(array('entity_table'	 => $entity_table, 'cols'			 => $cols, 'cols_return'	 => $cols_return,
					'uicols'		 => $uicols, 'joinmethod'	 => $joinmethod, 'paranthesis'	 => $paranthesis, 'force_location' => true, 'location_level' => 0));

				$this->bocommon->fm_cache('sql_project_' . !!$wo_hour_cat_id, $sql);

				$uicols				 = $this->bocommon->uicols;
				$cols_return		 = $this->bocommon->cols_return;
				$type_id			 = $this->bocommon->type_id;
				$this->cols_extra	 = $this->bocommon->cols_extra;

				$this->bocommon->fm_cache('uicols_project_' . !!$wo_hour_cat_id, $uicols);
				$this->bocommon->fm_cache('cols_return_project_' . !!$wo_hour_cat_id, $cols_return);
				$this->bocommon->fm_cache('type_id_project_' . !!$wo_hour_cat_id, $type_id);
				$this->bocommon->fm_cache('cols_extra_project_' . !!$wo_hour_cat_id, $this->cols_extra);
			}
			else
			{
				$uicols				 = $this->bocommon->fm_cache('uicols_project_' . !!$wo_hour_cat_id);
				$cols_return		 = $this->bocommon->fm_cache('cols_return_project_' . !!$wo_hour_cat_id);
				$type_id			 = $this->bocommon->fm_cache('type_id_project_' . !!$wo_hour_cat_id);
				$this->cols_extra	 = $this->bocommon->fm_cache('cols_extra_project_' . !!$wo_hour_cat_id);
			}

			$user_columns	 = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns'] : array();
			$_user_columns	 = array();
			foreach($user_columns as $user_column_id)
			{
				if(ctype_digit($user_column_id))
				{
					$_user_columns[] = $user_column_id;
				}
			}
			$user_column_filter	 = '';
			$location_id		 = $GLOBALS['phpgw']->locations->get_id('property', '.project');
			$attribute_filter	 = " location_id = {$location_id}";

			if($_user_columns)
			{
				$user_column_filter = " OR ($attribute_filter AND id IN (" . implode(',', $_user_columns) . '))';
			}

			$attribute_table = 'phpgw_cust_attribute';
			$this->db->query("SELECT * FROM $attribute_table WHERE list=1 AND $attribute_filter $user_column_filter ORDER BY group_id, attrib_sort ASC");

			$_custom_cols = '';

			$_attrib = array();
			while($this->db->next_record())
			{
				$_column_name			 = $this->db->f('column_name');
				$_attrib[$_column_name]	 = $this->db->f('id');
				$_custom_cols.= ", fm_project.{$_column_name}";
				$cols_return[]			 = $_column_name;
				$uicols['input_type'][]	 = 'text';
				$uicols['name'][]		 = $_column_name;
				$uicols['descr'][]		 = $this->db->f('input_text');
				$uicols['statustext'][]	 = $this->db->f('statustext');
				$uicols['datatype'][]	 = $this->db->f('datatype');
				$uicols['sortable'][]	 = true;
				$uicols['exchange'][]	 = false;
				$uicols['formatter'][]	 = '';
				$uicols['classname'][]	 = '';
			}

			$this->uicols = $uicols;

			$order_field = '';
			if($order)
			{
				$ordermethod = "ORDER BY $order $sort";
				switch($order)
				{
					case 'project_id':
						$ordermethod = "ORDER BY fm_project.id {$sort}";
						break;
					case 'combined_cost':
						$order_field = ',sum(fm_workorder.combined_cost) as combined_cost';
						break;
					case 'address':
						$order_field = ", fm_project.address";
						$group_field = $order_field;
						break;
					case 'status':
						$order_field = ", fm_project_status.descr as status";
						$group_field = ', fm_project_status.descr';
						$ordermethod = "ORDER BY fm_project_status.descr {$sort}";
						break;
					case 'entry_date':
						$order_field = ", fm_project.entry_date";
						$group_field = $order_field;
						break;
					case 'start_date':
						$order_field = ", fm_project.start_date";
						$group_field = $order_field;
						break;
					case 'end_date':
						$order_field = ", fm_project.end_date";
						$group_field = $order_field;
						break;
					case 'ecodimb':
						$order_field = ", fm_project.ecodimb";
						$group_field = $order_field;
						break;
					case 'location_code':
						$order_field = ", fm_project.location_code";
						$group_field = $order_field;
						break;

					default:
						$order_field = ", {$order}";
						$group_field = $order_field;
				}
			}
			else
			{
				$ordermethod = ' ORDER BY fm_project.id DESC';
			}


			$where = 'WHERE';

			$filtermethod = '';

			$GLOBALS['phpgw']->config->read();
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
				$filtermethod .= " {$where} fm_project.category IN (" . implode(',', $cat_filter) . ')';

				$where = 'AND';
			}

			if($status_id && $status_id != 'all')
			{

				if($status_id == 'open')
				{
					$_status_filter = array();
					$this->db->query("SELECT * FROM fm_project_status WHERE closed IS NULL");
					while($this->db->next_record())
					{
						$_status_filter[] = $this->db->f('id');
					}
					$filtermethod .= " $where fm_project.status IN ('" . implode("','", $_status_filter) . "')";
				}
				else
				{
					$filtermethod .= " $where fm_project.status='$status_id' ";
				}
				$where = 'AND';
			}

			if($project_type_id)
			{
				$filtermethod .= " {$where} fm_project.project_type_id={$project_type_id}";
				$where = 'AND';
			}

			if($wo_hour_cat_id)
			{
				$filtermethod .= " $where fm_wo_hours_category.id=$wo_hour_cat_id";
				$where = 'AND';
			}

			if($district_id)
			{
				$filtermethod .= " {$where} fm_part_of_town.district_id = {$district_id}";
				$where = 'AND';
			}

			/*
			  $group_method = ' GROUP BY fm_project_status.descr,loc1_name,fm_project.location_code,fm_project.id,fm_project.entry_date,fm_project.start_date,fm_project.end_date,'
			  . 'fm_project.name,fm_project.ecodimb,phpgw_accounts.account_lid,fm_project.user_id,fm_project.address,'
			  . 'fm_project.budget,fm_project.reserve,planned_cost,project_group';
			 */

			if(is_array($this->grants))
			{
				$grants = $this->grants;
				while(list($user) = each($grants))
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod .= " $where (fm_project.user_id IN(" . implode(',', $public_user_list) . ")";

				$where = 'AND';
			}

			if($filter)
			{
				$filtermethod .= " $where fm_project.coordinator={$filter}";
				$where = 'AND';
			}

			if($start_date)
			{
				$end_date	 = $end_date + 3600 * 16 + phpgwapi_datetime::user_timezone();
				$start_date	 = $start_date - 3600 * 8 + phpgwapi_datetime::user_timezone();

				$filtermethod .= " $where fm_project.start_date >= $start_date AND fm_project.start_date <= $end_date ";
				$where = 'AND';
			}

			if($filter_year && $filter_year != 'all')
			{
				$filter_year = (int) $filter_year;
				$filtermethod .= " $where (fm_project_budget.year={$filter_year} OR fm_project_buffer_budget.year={$filter_year})";
				$where		 = 'AND';
			}

			$querymethod = '';
			if($query)
			{
				$query	 = $this->db->db_addslashes($query);
				$query	 = str_replace(",", '.', $query);
				if(isset($criteria[0]['field']) && $criteria[0]['field'] == 'fm_project.p_num')
				{
					$query		 = explode(".", $query);
					$querymethod = " $where (fm_project.p_entity_id='" . (int) $query[1] . "' AND fm_project.p_cat_id='" . (int) $query[2] . "' AND fm_project.p_num='" . (int) $query[3] . "')";
				}
				else if(stristr($query, '.'))
				{
					$query		 = explode(".", $query);
					$querymethod = " $where (fm_project.loc1='" . $query[0] . "' AND fm_project.loc" . $type_id . "='" . $query[1] . "')";
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
							else
							{
								$_query = $query;
							}

							$_querymethod[] = "{$field_info['field']} {$matchtypes[$field_info['matchtype']]} {$field_info['front']}{$_query}{$field_info['back']}";
						}

						$querymethod = $where . ' (' . implode(' OR ', $_querymethod) . ')';
						unset($_querymethod);
					}
					else
					{
						if($criteria[0]['type'] == int)
						{
							$_query = (int) $query;
						}
						else
						{
							$_query = $query;
						}

						$querymethod = "{$where} {$criteria[0]['field']} {$matchtypes[$criteria[0]['matchtype']]} {$criteria[0]['front']}{$_query}{$criteria[0]['back']}";
					}
				}
			}

			$querymethod .= ')';

			$sql = str_replace('FROM', "{$_custom_cols} FROM", $sql);

//			$sql .= " $filtermethod $querymethod";
			$sql_full = "{$sql} {$filtermethod} {$querymethod}";
			//echo substr($sql,strripos($sql,'from'));

			if($GLOBALS['phpgw_info']['server']['db_type'] == 'postgres')
			{
				$sql_minimized		 = 'SELECT DISTINCT fm_project.id ' . substr($sql_full, strripos($sql_full, 'FROM'));
				$sql_count			 = "SELECT count(id) as cnt FROM ({$sql_minimized}) as t";
				$this->db->query($sql_count, __LINE__, __FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f('cnt');
			}
			else
			{
				$sql_count			 = 'SELECT DISTINCT fm_project.id ' . substr($sql_full, strripos($sql_full, 'FROM'));
				$this->db->query($sql_count, __LINE__, __FILE__);
				$this->total_records = $this->db->num_rows();
			}

			$sql_end = str_replace('SELECT DISTINCT fm_project.id', "SELECT DISTINCT fm_project.id {$order_field}", $sql_minimized) . " GROUP BY fm_project.id {$group_field} {$ordermethod}";

			$project_list = array();

			if(!$dry_run)
			{
				if(!$allrows)
				{
					$this->db->limit_query($sql_end, $start, __LINE__, __FILE__);
				}
				else
				{
					$_fetch_single = false;
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

				$project_list		 = array();
//_debug_array($cols_return);
				$count_cols_return	 = count($cols_return);

				while($this->db->next_record())
				{
					$project_list[] = array('project_id' => $this->db->f('id'));
				}

				$this->db->set_fetch_single(false);
//$test=array();
				foreach($project_list as &$project)
				{
					$this->db->query("{$sql} WHERE fm_project.id = '{$project['project_id']}' {$group_method}");
					$this->db->next_record();
//_debug_array("{$sql} WHERE fm_project.id = '{$project['project_id']}' {$group_method}");
					for($i = 0; $i < $count_cols_return; $i++)
					{
						$project[$cols_return[$i]] = $this->db->f($cols_return[$i]);
					}
					$project['grants'] = (int) $this->grants[$this->db->f('user_id')];

					$location_code	 = $this->db->f('location_code');
					$location		 = explode('-', $location_code);
					$count_location	 = count($location);

					for($m = 0; $m < $count_location; $m++)
					{
						$project['loc' . ($m + 1)]					 = $location[$m];
						$project['query_location']['loc' . ($m + 1)] = implode("-", array_slice($location, 0, ($m + 1)));
					}

					$project['combined_cost']	 = 0;
					$project['actual_cost']		 = 0;
					$project['billable_hours']	 = 0;
				}
//_debug_array($project_list);
				unset($project);

				$_datatype = array();
				foreach($this->uicols['name'] as $key => $_name)
				{
					$_datatype[$_name] = $this->uicols['datatype'][$key];
				}

				$dataset = array();
				$j		 = 0;

				foreach($project_list as $project)
				{
					foreach($project as $field => $value)
					{
						$dataset[$j][$field] = array
						(
							'value'		 => $value,
							'datatype'	 => isset($_datatype[$field]) && $_datatype[$field] ? $_datatype[$field] : false,
							'attrib_id'	 => isset($_attrib[$field]) && $_attrib[$field] ? $_attrib[$field] : false
						);
					}
					$j++;
				}

				$values = $this->custom->translate_value($dataset, $location_id);
				foreach($values as &$project)
				{
					$project['combined_cost']	 = 0;
					$project['budget']			 = 0;
					$project['obligation']		 = 0;
					$project['actual_cost']		 = 0;

					if($project['project_type_id'] == 3)//buffer
					{
						$buffer_budget = $this->get_buffer_budget($project['project_id']);

						foreach($buffer_budget as $entry)
						{
							$project['budget'] += $entry['amount_in'];
							$project['budget'] -= $entry['amount_out'];
						}
						unset($entry);
					}
					else
					{

						$year			 = (int) $filter_year;
						$project_budget	 = $this->get_budget($project['project_id']);
						foreach($project_budget as $entry)
						{
							if($year && $entry['year'] == $year)
							{
								if($entry['active'])
								{
									$project['combined_cost'] += $entry['sum_orders'];
									$project['budget'] += $entry['budget'];
									if(!$entry['closed'])
									{
										$project['obligation'] += $entry['sum_oblications'];
									}
								}
								$project['actual_cost'] += $entry['actual_cost'];
							}
							else if(!$year)
							{
								if($entry['active'])
								{
									$project['combined_cost'] += $entry['sum_orders'];
									$project['budget'] += $entry['budget'];
									if(!$entry['closed'])
									{
										$project['obligation'] += $entry['sum_oblications'];
									}
								}
								$project['actual_cost'] += $entry['actual_cost'];
							}
						}
						/*

						  $workorder_data = $this->project_workorder_data(array('project_id' => $project['project_id'], 'year' => (int)$filter_year));
						  foreach($workorder_data as $entry)
						  {
						  $project['actual_cost']		+= $entry['actual_cost'];
						  $project['combined_cost']	+= $entry['combined_cost'];
						  $project['budget']			+= $entry['budget'];
						  $project['obligation']		+= $entry['obligation'];
						  }

						  unset($entry);
						 */
						$_diff_start = abs($project['budget']) > 0 ? $project['budget'] : $project['combined_cost'];
						if(abs($_diff_start) > 0)
						{
							$project['diff'] = $_diff_start - $project['obligation'] - $project['actual_cost'];
						}
						else
						{
							$project['diff'] = 0;
						}
					}
				}

//_debug_array($values);
//_debug_array($test);
				return $values;
			}

			return array();
		}

		function get_meter_table()
		{
			$config = CreateObject('phpgwapi.config', 'property');
			$config->read();
			return isset($config->config_data['meter_table']) ? $config->config_data['meter_table'] : '';
		}

		function read_single($project_id, $values = array())
		{
			$project_id	 = (int) $project_id;
			$project	 = array();
			$sql		 = "SELECT fm_project.*, fm_project_status.closed FROM fm_project"
			. " {$this->join} fm_project_status ON fm_project.status = fm_project_status.id"
			. " WHERE fm_project.id={$project_id}";

			$this->db->query($sql, __LINE__, __FILE__);

			$project = array();
			if($this->db->next_record())
			{
				$project = array
				(
					'project_id'		 => $this->db->f('id'),
					'project_type_id'	 => $this->db->f('project_type_id'),
					'title'				 => $this->db->f('title'),
					'name'				 => $this->db->f('name'),
					'location_code'		 => $this->db->f('location_code'),
					'key_fetch'			 => $this->db->f('key_fetch'),
					'key_deliver'		 => $this->db->f('key_deliver'),
					'other_branch'		 => $this->db->f('other_branch'),
					'key_responsible'	 => $this->db->f('key_responsible'),
					'descr'				 => $this->db->f('descr', true),
					'status'			 => $this->db->f('status'),
					'closed'			 => $this->db->f('closed'),
					'budget'			 => (int) $this->db->f('budget'),
					//		'planned_cost'			=> (int)$this->db->f('planned_cost'),
					'reserve'			 => (int) $this->db->f('reserve'),
					'tenant_id'			 => $this->db->f('tenant_id'),
					'user_id'			 => $this->db->f('user_id'),
					'coordinator'		 => $this->db->f('coordinator'),
					'access'			 => $this->db->f('access'),
					'start_date'		 => $this->db->f('start_date'),
					'end_date'			 => $this->db->f('end_date'),
					'cat_id'			 => $this->db->f('category'),
					'grants'			 => (int) $this->grants[$this->db->f('user_id')],
					'p_num'				 => $this->db->f('p_num'),
					'p_entity_id'		 => $this->db->f('p_entity_id'),
					'p_cat_id'			 => $this->db->f('p_cat_id'),
					'contact_phone'		 => $this->db->f('contact_phone'),
					'project_group'		 => $this->db->f('project_group'),
					'ecodimb'			 => $this->db->f('ecodimb'),
					'b_account_id'		 => $this->db->f('account_group'),
					'contact_id'		 => $this->db->f('contact_id'),
					'inherit_location'	 => $this->db->f('inherit_location'),
					'periodization_id'	 => $this->db->f('periodization_id')
				);

				if(isset($values['attributes']) && is_array($values['attributes']))
				{
					$project['attributes'] = $values['attributes'];
					foreach($project['attributes'] as &$attr)
					{
						$attr['value'] = $this->db->f($attr['column_name']);
					}
				}

				$location_code			 = $this->db->f('location_code');
				$project['power_meter']	 = $this->get_power_meter($location_code);
			}

			if($project)
			{
				$this->db->query("SELECT sum(budget) AS sum_budget FROM fm_project_budget WHERE project_id = $project_id AND active = 1", __LINE__, __FILE__);
				$this->db->next_record();
				$project['budget'] = (int) $this->db->f('sum_budget');
			}

			//_debug_array($project);
			return $project;
		}

		function get_power_meter($location_code = '')
		{
			if(!$meter_table = $this->get_meter_table())
			{
				return false;
			}

			$this->db->query("SELECT maaler_nr as power_meter FROM $meter_table where location_code='$location_code' and category='1'", __LINE__, __FILE__);

			$this->db->next_record();

			return $this->db->f('power_meter');
		}

		function project_workorder_data($data = array())
		{
			$project_id	 = (int) $data['project_id'];
			$year		 = (int) $data['year'];
			$values		 = array();

			$filter_year = '';
			if($year)
			{
				$filter_year = "AND (fm_workorder_budget.year = {$year} OR fm_workorder_status.closed IS NULL)";
			}

			$this->db->query("SELECT DISTINCT fm_workorder.id AS workorder_id, fm_workorder.title, fm_workorder.vendor_id, fm_workorder.addition,"
			. " fm_workorder_status.descr as status, fm_workorder_status.closed, fm_workorder.account_id AS b_account_id, fm_workorder.charge_tenant"
			. " FROM fm_workorder"
			. " {$this->join} fm_workorder_status ON fm_workorder.status = fm_workorder_status.id"
			. " {$this->join} fm_workorder_budget ON fm_workorder.id = fm_workorder_budget.order_id"
			. " WHERE project_id={$project_id} {$filter_year}", __LINE__, __FILE__);

			$_orders = array();

			while($this->db->next_record())
			{
				$values[]	 = array
				(
					'workorder_id'			 => $this->db->f('workorder_id'),
					'title'					 => $this->db->f('title', true),
					'vendor_id'				 => $this->db->f('vendor_id'),
					'charge_tenant'			 => $this->db->f('charge_tenant'),
					'status'				 => $this->db->f('status'),
					'closed'				 => !!$this->db->f('closed'),
					'b_account_id'			 => $this->db->f('b_account_id'),
					'addition_percentage'	 => (int) $this->db->f('addition'),
					'calculation'			 => $this->db->f('calculation'),
					'combined_cost'			 => 0,
					'budget'				 => 0,
					'obligation'			 => 0,
					'actual_cost'			 => 0,
				);
				$_orders[]	 = $this->db->f('workorder_id');
			}

			if($_orders)
			{
				$soworkorder	 = CreateObject('property.soworkorder');
				$order_budgets	 = array();
				foreach($_orders as $_order_id)
				{
					$order_budgets[$_order_id] = $soworkorder->get_budget($_order_id);
				}
			}

			foreach($values as &$entry)
			{
				foreach($order_budgets[$entry['workorder_id']] as $budget)
				{
					if($budget['active'] == 2)
					{
						continue;
					}

					if($year)
					{
						if($budget['year'] == $year)
						{
							$entry['actual_cost'] += $budget['actual_cost'];
							$entry['combined_cost'] += $budget['sum_orders'];
							$entry['budget'] += $budget['budget'];
							$entry['obligation'] += $budget['sum_oblications'];
						}
					}
					else
					{
						$entry['actual_cost'] += $budget['actual_cost'];
						if($budget['active'])
						{
							$entry['combined_cost'] += $budget['sum_orders'];
							$entry['budget'] += $budget['budget'];
							$entry['obligation'] += $budget['sum_oblications'];
						}
					}
				}

				//		FIXME
				//		$_taxfactor = 1 + ($_taxcode[(int)$this->db->f('mvakode')]/100);
				//		$_actual_cost = round($actual_cost/$_taxfactor);

				$_diff_start	 = abs($entry['budget']) > 0 ? $entry['budget'] : $entry['combined_cost'];
				$entry['diff']	 = $_diff_start - $entry['obligation'] - $entry['actual_cost'];
			}

			return $values;
		}

		function branch_p_list($project_id = '')
		{
			$selected = array();
			$this->db->query("SELECT branch_id from fm_projectbranch WHERE project_id=" . (int) $project_id, __LINE__, __FILE__);
			while($this->db->next_record())
			{
				$selected[] = array('branch_id' => $this->db->f('branch_id'));
			}
			return $selected;
		}

		function increment_project_id()
		{
			$name		 = 'project';
			$now		 = time();
			$this->db->query("SELECT value, start_date FROM fm_idgenerator WHERE name='{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$next_id	 = $this->db->f('value') + 1;
			$start_date	 = (int) $this->db->f('start_date');
			$this->db->query("UPDATE fm_idgenerator SET value = $next_id WHERE name = '{$name}' AND start_date = {$start_date}");
		}

		function next_project_id()
		{
			$name	 = 'project';
			$now	 = time();
			$this->db->query("SELECT value FROM fm_idgenerator WHERE name = '{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$id		 = $this->db->f('value') + 1;
			return $id;
		}

		function add($project, $values_attribute = array())
		{
			$receipt	 = array();
			$historylog	 = CreateObject('property.historylog', 'project');

			while(is_array($project['location']) && list($input_name, $value) = each($project['location']))
			{
				if($value)
				{
					$cols[]	 = $input_name;
					$vals[]	 = $value;
				}
			}

			while(is_array($project['extra']) && list($input_name, $value) = each($project['extra']))
			{
				if($value)
				{
					$cols[]	 = $input_name;
					$vals[]	 = $value;
				}
			}

			$data_attribute = $this->custom->prepare_for_db('fm_project', $values_attribute);
			if(isset($data_attribute['value_set']))
			{
				foreach($data_attribute['value_set'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[]	 = $input_name;
						$vals[]	 = $value;
					}
				}
			}

			if($cols)
			{
				$cols	 = "," . implode(",", $cols);
				$vals	 = ",'" . implode("','", $vals) . "'";
			}

			if($project['street_name'])
			{
				$address[]	 = $project['street_name'];
				$address[]	 = $project['street_number'];
				$address	 = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($project['location_name']);
			}

			$project['descr']	 = $this->db->db_addslashes($project['descr']);
			$project['name']	 = $this->db->db_addslashes($project['name']);

			$this->db->transaction_begin();
			$id		 = $this->next_project_id();
			$values	 = array
			(
				$id,
				$project['project_type_id'],
				$project['project_group'],
				$project['name'],
				'public',
				$project['cat_id'],
				time(),
				$project['start_date'],
				$project['end_date'],
				$project['coordinator'],
				$project['status'],
				$project['descr'],
				(int) $project['budget'],
				(int) $project['reserve'],
				$project['location_code'],
				$address,
				$project['key_deliver'],
				$project['key_fetch'],
				$project['other_branch'],
				$project['key_responsible'],
				$this->account,
				$project['ecodimb'],
				$project['b_account_id'],
				$project['contact_id'],
				$project['inherit_location'],
				$project['budget_periodization'],
			);

			$values = $this->db->validate_insert($values);

			$this->db->query("INSERT INTO fm_project (id,project_type_id,project_group,name,access,category,entry_date,start_date,end_date,coordinator,status,"
			. "descr,budget,reserve,location_code,address,key_deliver,key_fetch,other_branch,key_responsible,user_id,ecodimb,account_group,contact_id,inherit_location,periodization_id $cols) "
			. "VALUES ($values $vals )", __LINE__, __FILE__);

			if($project['budget'])
			{
				$this->update_budget($id, $project['budget_year'], $project['budget_periodization'], $project['budget'], $project['budget_periodization_all'], 'update', $project['budget_periodization_activate']);
			}

			if($project['extra']['contact_phone'] && $project['extra']['tenant_id'])
			{
				$this->db->query("update fm_tenant set contact_phone='" . $project['extra']['contact_phone'] . "' where id='" . $project['extra']['tenant_id'] . "'", __LINE__, __FILE__);
			}

			if(isset($project['power_meter']) && $project['power_meter'])
			{
				$this->update_power_meter($project['power_meter'], $project['location_code'], $address);
			}

			if(count($project['branch']) != 0)
			{
				while($branch = each($project['branch']))
				{
					$this->db->query("insert into fm_projectbranch (project_id,branch_id) values ({$id},{$branch[1]})", __LINE__, __FILE__);
				}
			}

			if(is_array($project['origin']))
			{
				if($project['origin'][0]['data'][0]['id'])
				{
					$interlink_data = array
					(
						'location1_id'		 => $GLOBALS['phpgw']->locations->get_id('property', $project['origin'][0]['location']),
						'location1_item_id'	 => $project['origin'][0]['data'][0]['id'],
						'location2_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.project'),
						'location2_item_id'	 => $id,
						'account_id'		 => $this->account
					);

					$this->interlink->add($interlink_data, $this->db);
				}
			}

			if($this->db->transaction_commit())
			{
				$this->increment_project_id();
				$historylog->add('SO', $id, $project['status']);
				$historylog->add('TO', $id, $project['cat_id']);
				$historylog->add('CO', $id, $project['coordinator']);
				if($project['remark'])
				{
					$historylog->add('RM', $id, $project['remark']);
				}

				$receipt['message'][] = array('msg' => lang('project %1 has been saved', $id));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('the project has not been saved'));
			}

			$receipt['id'] = $id;
			return $receipt;
		}

		function update_power_meter($power_meter, $location_code, $address)
		{
			if(!$meter_table = $this->get_meter_table())
			{
				return;
			}

			$location = explode('-', $location_code);

			$i = 1;
			if(isset($location) AND is_array($location))
			{
				foreach($location as $location_entry)
				{
					$cols[]	 = 'loc' . $i;
					$vals[]	 = $location_entry;

					$i++;
				}
			}

			if($cols)
			{
				$cols	 = "," . implode(",", $cols);
				$vals	 = ",'" . implode("','", $vals) . "'";
			}

			$this->db->query("SELECT count(*) as cnt FROM $meter_table where location_code='$location_code' and category=1", __LINE__, __FILE__);

			$this->db->next_record();

			if($this->db->f('cnt'))
			{
				$this->db->query("update $meter_table set maaler_nr='$power_meter',address='$address' where location_code='$location_code' and category='1'", __LINE__, __FILE__);
			}
			else
			{
				$id = $this->bocommon->next_id($meter_table);

				$meter_id = $this->generate_meter_id($meter_table);
				$this->db->query("insert into $meter_table (id,num,maaler_nr,category,location_code,entry_date,user_id,address $cols) "
				. "VALUES ('"
				. $id . "','"
				. $meter_id . "','"
				. $power_meter . "',"
				. "1,'"
				. $location_code . "',"
				. time() . ",$this->account, '$address' $vals)", __LINE__, __FILE__);
			}
		}

		function generate_meter_id($meter_table)
		{
			$prefix	 = 'meter';
			$pos	 = strlen($prefix);
			$this->db->query("select max(num) as current from $meter_table where num $this->like ('$prefix%')");
			$this->db->next_record();

			$max = $this->bocommon->add_leading_zero(substr($this->db->f('current'), $pos));

			$meter_id = $prefix . $max;
			return $meter_id;
		}

		function edit($project, $values_attribute = array())
		{
			$historylog	 = CreateObject('property.historylog', 'project');
			$receipt	 = array();

			if($project['street_name'])
			{
				$address[]	 = $project['street_name'];
				$address[]	 = $project['street_number'];
				$address	 = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($project['location_name']);
			}

			$project['descr']	 = $this->db->db_addslashes($project['descr']);
			$project['name']	 = $this->db->db_addslashes($project['name']);

			$value_set = array
			(
				'project_type_id'	 => $project['project_type_id'],
				'project_group'		 => $project['project_group'],
				'name'				 => $project['name'],
				'status'			 => $project['status'],
				'category'			 => $project['cat_id'],
				'start_date'		 => $project['start_date'],
				'end_date'			 => $project['end_date'],
				'coordinator'		 => $project['coordinator'],
				'descr'				 => $project['descr'],
				'reserve'			 => (int) $project['reserve'],
				'key_deliver'		 => $project['key_deliver'],
				'key_fetch'			 => $project['key_fetch'],
				'other_branch'		 => $project['other_branch'],
				'key_responsible'	 => $project['key_responsible'],
				'location_code'		 => $project['location_code'],
				'address'			 => $address,
				'ecodimb'			 => $project['ecodimb'],
				'account_group'		 => $project['b_account_id'],
				'contact_id'		 => $project['contact_id'],
				'inherit_location'	 => $project['inherit_location'],
			);

			if(isset($project['budget_periodization']) && $project['budget_periodization'])
			{
				$value_set['periodization_id'] = $project['budget_periodization'];
			}

			$data_attribute = $this->custom->prepare_for_db('fm_project', $values_attribute, $project['id']);

			if(isset($data_attribute['value_set']))
			{
				$value_set = array_merge($value_set, $data_attribute['value_set']);
			}

			while(is_array($project['location']) && list($input_name, $value) = each($project['location']))
			{
				$value_set[$input_name] = $value;
			}

			while(is_array($project['extra']) && list($input_name, $value) = each($project['extra']))
			{
				$value_set[$input_name] = $value;
			}

			$value_set = $this->db->validate_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("SELECT status,category,coordinator,budget,reserve FROM fm_project WHERE id = {$project['id']}", __LINE__, __FILE__);
			$this->db->next_record();
			$old_status		 = $this->db->f('status');
			$old_category	 = (int) $this->db->f('category');
			$old_coordinator = (int) $this->db->f('coordinator');
			$old_budget		 = (int) $this->db->f('budget');
			$old_reserve	 = (int) $this->db->f('reserve');

			$this->db->query("UPDATE fm_project SET $value_set WHERE id= {$project['id']}", __LINE__, __FILE__);

			$_closed_period	 = array
			(
				'closed_b_period'		 => isset($project['closed_b_period']) && $project['closed_b_period'] ? $project['closed_b_period'] : array(),
				'closed_orig_b_period'	 => isset($project['closed_orig_b_period']) && $project['closed_orig_b_period'] ? $project['closed_orig_b_period'] : array()
			);
			$_active_period	 = array
			(
				'active_b_period'		 => isset($project['active_b_period']) && $project['active_b_period'] ? $project['active_b_period'] : array(),
				'active_orig_b_period'	 => isset($project['active_orig_b_period']) && $project['active_orig_b_period'] ? $project['active_orig_b_period'] : array()
			);

			$this->close_period_from_budget($project['id'], $_closed_period);
			$this->activate_period_from_budget($project['id'], $_active_period);

			unset($_close_period);
			unset($_active_period);

			if($project['delete_b_period'])
			{
				$this->delete_period_from_budget($project['id'], $project['delete_b_period']);
			}

			$workorders = array();

			if($project['project_type_id'] == 3)//buffer
			{
				if($project['budget'])
				{
					$this->_update_buffer_budget($project['id'], $project['budget_year'], $project['budget'], null, null);
				}

				if(isset($project['transfer_amount']) && $project['transfer_amount'] && isset($project['transfer_target']) && $project['transfer_target'])
				{
					$this->_update_buffer_budget($project['id'], date('Y'), $project['transfer_amount'], null, $project['transfer_target'], $project['transfer_remark']);

					if(isset($project['transfer_remark']) && $project['transfer_remark'])
					{
						$historylog->add('RM', $project['transfer_target'], $project['transfer_remark'], false);
					}
				}

				$this->db->query("SELECT sum(amount_in) AS amount_in, sum(amount_out) AS amount_out FROM fm_project_buffer_budget WHERE buffer_project_id = " . (int) $project['id'], __LINE__, __FILE__);
				$this->db->next_record();
				$new_budget = (int) $this->db->f('amount_in') - (int) $this->db->f('amount_out');

				if($old_budget != $new_budget)
				{
					$this->db->query("UPDATE fm_project SET budget = {$new_budget} WHERE id = " . (int) $project['id'], __LINE__, __FILE__);
					$historylog->add('B', $project['id'], $project['budget'], $old_budget);
				}

				if($project['budget_reset_buffer'])
				{
					$this->db->query("UPDATE fm_project SET budget = 0 WHERE id = " . (int) $project['id'], __LINE__, __FILE__);
					$this->db->query("DELETE FROM fm_project_buffer_budget WHERE buffer_project_id = " . (int) $project['id'], __LINE__, __FILE__);
					$historylog->add('B', $project['id'], 0, $old_budget);
					$historylog->add('RM', $project['id'], 'reset', false);
				}
			}
			else // investment or operation
			{
				if(isset($project['transfer_amount']) && $project['transfer_amount'] && isset($project['transfer_target']) && $project['transfer_target'])
				{
					$this->db->query("SELECT project_type_id FROM fm_project WHERE id = " . (int) $project['transfer_target'], __LINE__, __FILE__);
					$this->db->next_record();
					if($this->db->f('project_type_id') != 3)
					{
						throw new Exception('property_soproject::edit() - target project is not a buffer-project');
					}

					$this->_update_buffer_budget($project['transfer_target'], date('Y'), $project['transfer_amount'], $project['id'], null, $project['transfer_remark']);

					$this->db->query("SELECT sum(amount_in) AS amount_in, sum(amount_out) AS amount_out FROM fm_project_buffer_budget WHERE buffer_project_id = " . (int) $project['transfer_target'], __LINE__, __FILE__);
					$this->db->next_record();
					$new_budget = (int) $this->db->f('amount_in') - (int) $this->db->f('amount_out');
					$this->db->query("UPDATE fm_project SET budget = {$new_budget} WHERE id = " . (int) $project['transfer_target'], __LINE__, __FILE__);

					if(isset($project['transfer_remark']) && $project['transfer_remark'])
					{
						$historylog->add('RM', $project['id'], $project['transfer_remark'], false);
					}
				}


				if($project['budget'])
				{
					$this->update_budget($project['id'], $project['budget_year'], $project['budget_periodization'], $project['budget'], $project['budget_periodization_all'], 'update', $project['budget_periodization_activate']);
				}

				$this->db->query("SELECT sum(budget) AS sum_budget FROM fm_project_budget WHERE active = 1 AND project_id = " . (int) $project['id'], __LINE__, __FILE__);
				$this->db->next_record();
				$new_budget = (int) $this->db->f('sum_budget');

				if($old_budget != $new_budget)
				{
					$this->db->query("UPDATE fm_project SET budget = {$new_budget} WHERE id = " . (int) $project['id'], __LINE__, __FILE__);
					$historylog->add('B', $project['id'], $project['budget'], $old_budget);
				}

				$this->db->query("SELECT id FROM fm_workorder WHERE project_id=" . (int) $project['id'], __LINE__, __FILE__);

				while($this->db->next_record())
				{
					$workorder_id	 = $this->db->f('id');
					$workorders[]	 = $workorder_id;
					phpgwapi_cache::system_clear('property', "budget_order_{$workorder_id}");
				}

				if($workorders)
				{
					$historylog_workorder = CreateObject('property.historylog', 'workorder');
				}

				if(isset($project['new_project_id']) && $project['new_project_id'] && ($project['new_project_id'] != $project['id']))
				{
					$new_project_id = (int) $project['new_project_id'];
					reset($workorders);
					foreach($workorders as $workorder_id)
					{
						$historylog_workorder->add('NP', $workorder_id, $new_project_id, $project['id']);
					}

					$sql					 = "SELECT sum(budget) AS sum_budget FROM fm_project_budget WHERE project_id = {$new_project_id}";
					$this->db->query($sql, __LINE__, __FILE__);
					$this->db->next_record();
					$old_budget_new_project	 = (int) $this->db->f('sum_budget');

					$sql = "SELECT * FROM fm_project_budget WHERE project_id = " . (int) $project['id'];
					$this->db->query($sql, __LINE__, __FILE__);

					$budget = array();
					while($this->db->next_record())
					{
						$budget[] = array
						(
							'project_id'	 => (int) $project['id'],
							'year'			 => $this->db->f('year'),
							'month'			 => $this->db->f('month'),
							'budget'		 => (int) $this->db->f('budget'),
							'user_id'		 => $this->db->f('user_id'),
							'entry_date'	 => $this->db->f('entry_date'),
							'modified_date'	 => $this->db->f('modified_date'),
							'closed'		 => $this->db->f('closed'),
							'active'		 => $this->db->f('active')
						);
					}

					foreach($budget as $entry)
					{
						$sql = "SELECT * FROM fm_project_budget WHERE project_id = {$new_project_id} AND year = {$entry['year']} AND month = {$entry['month']}";
						$this->db->query($sql, __LINE__, __FILE__);
						if($this->db->next_record())
						{
							$sql = "UPDATE fm_project_budget SET budget = budget + {$entry['budget']} WHERE project_id = {$new_project_id} AND year = {$entry['year']} AND month = {$entry['month']}";
							$this->db->query($sql, __LINE__, __FILE__);
						}
						else
						{
							$value_set	 = array
							(
								'project_id'	 => $new_project_id,
								'year'			 => $entry['year'],
								'month'			 => $entry['month'],
								'budget'		 => $entry['budget'],
								'user_id'		 => $entry['user_id'],
								'entry_date'	 => $entry['entry_date'],
								'modified_date'	 => $entry['modified_date'],
								'closed'		 => $entry['closed'],
								'active'		 => $entry['active']
							);
							$cols		 = implode(',', array_keys($value_set));
							$values		 = $this->db->validate_insert(array_values($value_set));
							$this->db->query("INSERT INTO fm_project_budget ({$cols}) VALUES ({$values})", __LINE__, __FILE__);
						}
					}

					if($old_budget)
					{
						$historylog->add('B', $project['id'], 0, $old_budget);
					}

					$sql					 = "SELECT sum(budget) AS sum_budget FROM fm_project_budget WHERE project_id = {$new_project_id}";
					$this->db->query($sql, __LINE__, __FILE__);
					$this->db->next_record();
					$new_budget_new_project	 = (int) $this->db->f('sum_budget');

					$sql				 = "SELECT ecodimb FROM fm_project WHERE id = {$new_project_id}";
					$this->db->query($sql, __LINE__, __FILE__);
					$this->db->next_record();
					$ecodimb_new_project = (int) $this->db->f('ecodimb');

					$sql				 = "SELECT reserve FROM fm_project WHERE id = " . (int) $project['id'];
					$this->db->query($sql, __LINE__, __FILE__);
					$this->db->next_record();
					$reserve_old_project = (int) $this->db->f('reserve');

					if($new_budget_new_project != $old_budget_new_project)
					{
						$historylog->add('B', $new_project_id, $new_budget_new_project, $old_budget_new_project);
					}

					$this->db->query("UPDATE fm_workorder SET project_id = {$new_project_id}, ecodimb = {$ecodimb_new_project} WHERE project_id = {$project['id']}", __LINE__, __FILE__);
					$this->db->query("UPDATE fm_project SET reserve = 0 WHERE reserve IS NULL AND id = {$new_project_id}", __LINE__, __FILE__);
					$this->db->query("UPDATE fm_project SET budget = {$new_budget_new_project}, reserve = reserve + {$reserve_old_project} WHERE id = {$new_project_id}", __LINE__, __FILE__);
					$this->db->query("UPDATE fm_project SET budget = 0, reserve = 0 WHERE id =  " . (int) $project['id'], __LINE__, __FILE__);
					$this->db->query("DELETE FROM fm_project_budget WHERE project_id =  " . (int) $project['id'], __LINE__, __FILE__);
					$historylog->add('RM', (int) $project['id'], "Budsjett og alle bestillinger er overført fra prosjekt {$project['id']} til prosjekt {$new_project_id}");
					$historylog->add('RM', $new_project_id, "Budsjett og alle bestillinger er overført fra prosjekt {$project['id']} til prosjekt {$new_project_id}");

					reset($workorders);
					foreach($workorders as $workorder_id)
					{
						execMethod('property.soworkorder.update_order_budget', $workorder_id);
					}
				}
			}

			if($project['extra']['contact_phone'] && $project['extra']['tenant_id'])
			{
				$this->db->query("UPDATE fm_tenant SET contact_phone='" . $project['extra']['contact_phone'] . "' WHERE id='" . $project['extra']['tenant_id'] . "'", __LINE__, __FILE__);
			}

			if(isset($project['power_meter']) && $project['power_meter'])
			{
				$this->update_power_meter($project['power_meter'], $project['location_code'], $address);
			}
			// -----------------which branch is represented
			$this->db->query("DELETE FROM fm_projectbranch WHERE project_id={$project['id']}", __LINE__, __FILE__);

			if(count($project['branch']) != 0)
			{
				while($branch = each($project['branch']))
				{
					$this->db->query("INSERT INTO fm_projectbranch (project_id,branch_id) VALUES ({$project['id']}, {$branch[1]})", __LINE__, __FILE__);
				}
			}

			if($project['delete_request'])
			{
				$receipt = $this->delete_request_from_project($project['delete_request'], $project['id']);
			}

			$this->update_request_status($project['id'], $project['status'], $project['cat_id'], $project['coordinator']);


			if(($old_status != $project['status']) || $project['confirm_status'])
			{
				$close_pending_action	 = false;
				$close_workorders		 = false;
				$this->db->query("SELECT * FROM fm_project_status WHERE id = '{$project['status']}'");
				$this->db->next_record();
				if($this->db->f('closed'))
				{
					$close_workorders = true;
				}


				if($this->db->f('approved'))
				{
					$close_pending_action = true;

					$action_params = array
					(
						'appname'			 => 'property',
						'location'			 => '.project',
						'id'				 => (int) $project['id'],
						'responsible'		 => $this->account,
						'responsible_type'	 => 'user',
						'action'			 => 'approval',
						'remark'			 => '',
						'deadline'			 => ''
					);

					execMethod('property.sopending_action.close_pending_action', $action_params);
					unset($action_params);
				}

				$workorder_closed_status = isset($this->config->config_data['workorder_closed_status']) && $this->config->config_data['workorder_closed_status'] ? $this->config->config_data['workorder_closed_status'] : 'closed';

				if($old_status != $project['status'])
				{
					$historylog->add('S', $project['id'], $project['status'], $old_status);
					$receipt['notice_owner'][] = lang('Status changed') . ': ' . $project['status'];
				}
				else if($old_status != $project['status'] && $close_workorders)
				{
					$historylog->add('S', $project['id'], $project['status'], $old_status);

					//			$this->db->query("UPDATE fm_workorder SET status='{$workorder_closed_status}' WHERE project_id = {$project['id']}",__LINE__,__FILE__);
					$this->_update_status_workorder(true, $workorder_closed_status, $workorders);
					foreach($workorders as $workorder_id)
					{
						//				$historylog_workorder->add('S',$workorder_id,'closed');
					}

					$receipt['notice_owner'][] = lang('Status changed') . ': ' . $project['status'];
				}
				elseif($project['confirm_status'])
				{
					$historylog->add('SC', $project['id'], $project['status']);

					if($close_workorders)
					{
						//				$this->db->query("UPDATE fm_workorder SET status='{$workorder_closed_status}' WHERE project_id = {$project['id']}",__LINE__,__FILE__);

						$this->_update_status_workorder(true, $workorder_closed_status, $workorders);
						foreach($workorders as $workorder_id)
						{
							//					$historylog_workorder->add('SC',$workorder_id,'closed');
						}
					}
					$receipt['notice_owner'][] = lang('Status confirmed') . ': ' . $project['status'];
				}

				/*
				  // Handled in _update_status_workorder();
				  if($close_pending_action)
				  {
				  $action_params = array
				  (
				  'appname'			=> 'property',
				  'location'			=> '.project.workorder',
				  'id'				=> 0,
				  'responsible'		=> $this->account,
				  'responsible_type'  => 'user',
				  'action'			=> 'approval',
				  'remark'			=> '',
				  'deadline'			=> ''
				  );


				  foreach($workorders as $workorder_id)
				  {
				  $action_params['id'] =  $workorder_id;
				  execMethod('property.sopending_action.close_pending_action', $action_params);
				  }
				  unset($action_params);
				  }
				 */
			}

			if(isset($project['project_group']) && $project['project_group'])
			{
				reset($workorders);
				foreach($workorders as $workorder_id)
				{
					$this->db->query("UPDATE fm_ecobilag SET project_id = '{$project['project_group']}' WHERE pmwrkord_code = '{$workorder_id}' ", __LINE__, __FILE__);
				}
			}

			if($old_category != $project['cat_id'])
			{
				$historylog->add('T', $project['id'], $project['cat_id'], $old_category);
			}
			if($old_coordinator != $project['coordinator'])
			{
				$historylog->add('C', $project['id'], $project['coordinator'], $old_coordinator);
				$receipt['notice_owner'][] = lang('Coordinator changed') . ': ' . $GLOBALS['phpgw']->accounts->id2name($project['coordinator']);
			}


			if($old_reserve != (int) $project['reserve'])
			{
				$historylog->add('BR', $project['id'], $project['reserve'], $old_reserve);
			}

			if($project['remark'])
			{
				$historylog->add('RM', $project['id'], $project['remark']);
			}

			/**
			 * Add budget to project if missing.
			 */
			$this->_update_project_budget($project['id']);

			$receipt['id']			 = $project['id'];
			$receipt['message'][]	 = array('msg' => lang('project %1 has been edited', $project['id']));

			$this->db->transaction_commit();

			return $receipt;
		}

		function delete_request_from_project($request, $project_id)
		{
			foreach($request as $request_id)
			{
				$this->db->query("UPDATE fm_request set project_id = NULL where id='{$request_id}'", __LINE__, __FILE__);
				$this->interlink->delete_at_origin('property', '.project.request', '.project', $request_id, $this->db);
				$receipt['message'][] = array('msg' => lang('request %1 has been deleted from project %2', $request_id, $project_id));
			}
			return $receipt;
		}

		public function get_buffer_budget($project_id)
		{
			$sql	 = "SELECT * FROM fm_project_buffer_budget WHERE buffer_project_id = {$project_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$values	 = array();
			while($this->db->next_record())
			{
				$values[] = array
				(
					'buffer_project_id'	 => $this->db->f('buffer_project_id'),
					'year'				 => $this->db->f('year'),
					'amount_in'			 => $this->db->f('amount_in'),
					'amount_out'		 => $this->db->f('amount_out'),
					'from_project'		 => $this->db->f('from_project'),
					'to_project'		 => $this->db->f('to_project'),
					'user_id'			 => $this->db->f('user_id'),
					'entry_date'		 => $this->db->f('entry_date'),
					'active'			 => !!$this->db->f('active'),
					'remark'			 => $this->db->f('remark', true)
				);
			}
			return $values;
		}

		private function _update_buffer_budget($project_id, $year, $amount, $from_project, $to_project, $transfer_remark)
		{
			$year	 = (int) $year;
			$amount	 = (int) $amount;

			if(!$year)
			{
				$year = date('Y');
			}

			if($from_project || (!$from_project && !$to_project))
			{
				$amount_in	 = $amount;
				$amount_out	 = null;
			}
			else if($to_project && !$from_project)
			{
				$amount_in	 = null;
				$amount_out	 = $amount;
			}
			else
			{
				throw new Exception('property_soproject::update_buffer_budget() - wrong input');
			}

			$value_set = array
			(
				'buffer_project_id'	 => $project_id,
				'year'				 => $year,
				'amount_in'			 => $amount_in,
				'amount_out'		 => $amount_out,
				'from_project'		 => $from_project,
				'to_project'		 => $to_project,
				'user_id'			 => $this->account,
				'entry_date'		 => time(),
				'active'			 => 1,
				'remark'			 => $this->db->db_addslashes($transfer_remark)
			);

			$from_project	 = (int) $from_project;
			$to_project		 = (int) $to_project;

			$cols	 = implode(',', array_keys($value_set));
			$values	 = $this->db->validate_insert(array_values($value_set));
			$this->db->query("INSERT INTO fm_project_buffer_budget ({$cols}) VALUES ({$values})", __LINE__, __FILE__);

			/**
			 * Transfer fund to another project
			 * */
			if($amount_out)
			{
				$this->db->query("SELECT periodization_id FROM fm_project WHERE id = {$to_project}", __LINE__, __FILE__);
				$this->db->next_record();
				$periodization_id = $this->db->f('periodization_id');
				$this->update_budget($to_project, $year, $periodization_id, $amount_out, false, 'add');
			}

			/**
			 * Transfer fund from another project
			 * */
			if($amount_in && $from_project)
			{
				$this->db->query("SELECT periodization_id FROM fm_project WHERE id = {$from_project}", __LINE__, __FILE__);
				$this->db->next_record();
				$periodization_id	 = $this->db->f('periodization_id');
				$transferred		 = $this->update_budget($from_project, $year, $periodization_id, $amount_in, false, 'subtract');
				if(!$transferred == $amount_in)
				{
					throw new Exception('property_soproject::update_buffer_budget() - failed to transfer the full amount');
				}
			}
		}

		function update_budget($project_id, $year, $periodization_id, $budget, $budget_periodization_all = false, $action = 'update', $activate = 0)
		{
			$project_id	 = (int) $project_id;
			$year		 = $year ? (int) $year : date('Y');


			if($action == 'subtract')
			{
				$incoming_budget = $budget;
				$acc_partial	 = 0;

				$orig_budget = $this->get_budget($project_id);

				$hit = false;
				foreach($orig_budget as $entry)
				{
					if($entry['year'] == $year && $entry['active'])
					{
						$partial_budget	 = 0;
						$month			 = (int) substr($entry['period'], -2);
						$hit			 = true; // found at least one.
						if($entry['budget'] >= 0)
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
						if($entry['budget'] < 0)
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
							$this->_update_budget($project_id, $year, $month, $partial_budget, $action);
						}
					}
				}
//_debug_array($budget);
//die();
				if($hit && $budget) // still some left to go - place it on the last one
				{

					$acc_partial += $budget;

					$this->_update_budget($project_id, $year, $month, $budget, $action);
				}

				if(!$hit)
				{
					throw new Exception('property_soproject::update_buffer_budget() - found no active budget to transfer from');
				}

				return $acc_partial;
			}

			$periodization_id		 = (int) $periodization_id;
			$periodization_outline	 = array();
			$skip_period			 = 0;

			if($periodization_id)
			{
				$this->db->query("SELECT month, value,dividend,divisor FROM fm_eco_periodization_outline WHERE periodization_id = {$periodization_id} ORDER BY month ASC", __LINE__, __FILE__);
				while($this->db->next_record())
				{
					$month = $this->db->f('month');
					if($month < date('n'))
					{
						$skip_period ++;
					}
					$periodization_outline[] = array
					(
						'month'		 => $month,
						'value'		 => $this->db->f('value'),
						'dividend'	 => $this->db->f('dividend'),
						'divisor'	 => $this->db->f('divisor')
					);
				}
				if($skip_period && $skip_period == count($periodization_outline))
				{
					$skip_period -= 1;
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

			//reset skip in case of 'all'
			if($budget_periodization_all)
			{
				$skip_period = 0;
			}


			$percentage_to_move = 0;
			foreach($periodization_outline as $_key => $outline)
			{
				if($skip_period && $skip_period == ($_key + 1))
				{
					if($outline['dividend'] && $outline['divisor'])
					{
						$percentage_to_move += $outline['dividend'] / $outline['divisor'];
					}
					else
					{
						$percentage_to_move += $outline['value'] / 100;
					}

					continue;
				}

				if($outline['dividend'] && $outline['divisor'])
				{
					$partial_budget = $budget * $outline['dividend'] / $outline['divisor'];
				}
				else
				{
					$partial_budget = $budget * $outline['value'] / 100;
				}
				$partial_budget = $partial_budget * (1 + $percentage_to_move);

				$this->_update_budget($project_id, $year, $outline['month'], $partial_budget, $action, $activate);
			}

			$sql		 = "SELECT sum(budget) as sum_budget FROM fm_project_budget WHERE active = 1 AND project_id = {$project_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$sum_budget	 = (int) $this->db->f('sum_budget');
			$sql		 = "UPDATE fm_project SET budget = {$sum_budget} WHERE id = {$project_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			return $sum_budget;
		}

		private function _update_budget($project_id, $year, $month, $budget, $action = 'update', $active = 0)
		{
			$month		 = (int) $month;
			$budget		 = (int) $budget;
			$now		 = time();
			$active		 = (int) $active;
			$sql		 = "SELECT budget,active FROM fm_project_budget WHERE project_id = {$project_id} AND year = {$year} AND month = {$month}";
//_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			if($old_budget	 = $this->db->f('budget'))
			{
				if($action == 'add')
				{
					$new_budget = $old_budget + $budget;
				}
				else if($action == 'update')
				{
					$new_budget = $budget;
//					$active = (int)$this->db->f('active');
				}
				else if($action == 'subtract')
				{
					$new_budget = $old_budget - $budget;
//					$active = (int)$this->db->f('active');
				}

				$sql = "UPDATE fm_project_budget SET budget = {$new_budget}, modified_date = {$now} WHERE project_id = {$project_id} AND year = {$year} AND month = {$month}";
//_debug_array($sql);
				$this->db->query($sql, __LINE__, __FILE__);
			}
			else
			{
				$value_set = array
				(
					'project_id'	 => $project_id,
					'year'			 => $year,
					'month'			 => $month,
					'budget'		 => $budget,
					'user_id'		 => $this->account,
					'entry_date'	 => $now,
					'modified_date'	 => $now,
					'active'		 => $active // only for new entries
				);

//_debug_array($value_set);die();

				$cols	 = implode(',', array_keys($value_set));
				$values	 = $this->db->validate_insert(array_values($value_set));
				$this->db->query("INSERT INTO fm_project_budget ({$cols}) VALUES ({$values})", __LINE__, __FILE__);
			}
		}

		function get_budget($project_id)
		{
			$project_id				 = (int) $project_id;
			$closed_period			 = array();
			$active_period			 = array();
			$project_budget			 = array();
			$project_total_budget	 = 0;

			$sql = "SELECT fm_project_budget.year, fm_project_budget.month, fm_project_budget.budget,"
			. " fm_project_budget.closed, fm_project_budget.active, sum(combined_cost) AS order_amount, project_type_id"
			. " FROM fm_project"
			. " {$this->left_join} fm_project_budget ON fm_project_budget.project_id = fm_project.id"
			. " {$this->left_join} fm_workorder ON fm_project.id = fm_workorder.project_id"
			. " WHERE fm_project_budget.project_id = {$project_id}"
			. " GROUP BY project_type_id, fm_project_budget.year, fm_project_budget.month, fm_project_budget.budget,"
			. " fm_project_budget.closed, fm_project_budget.active"
			. " ORDER BY fm_project_budget.year, fm_project_budget.month";
			$this->db->query($sql, __LINE__, __FILE__);

			while($this->db->next_record())
			{
				$project_type_id		= (int) $this->db->f('project_type_id');
				$period					= $this->db->f('year') . sprintf("%02s", $this->db->f('month'));

				$project_budget[$period] = (int) $this->db->f('budget');
				$closed_period[$period]	 = !!$this->db->f('closed');
				$active_period[$period]	 = !!$this->db->f('active');
			}

			$project_total_budget = array_sum($project_budget);

			$sql = "SELECT fm_workorder.id AS order_id "
			. " FROM fm_workorder"
			. " WHERE project_id = {$project_id}";

			$this->db->query($sql, __LINE__, __FILE__);
			$_order_list = array();
			while($this->db->next_record())
			{
				$_order_list[] = $this->db->f('order_id');
			}

			$soworkorder = CreateObject('property.soworkorder');

			$order_budget = array();
			foreach($_order_list as $_order_id)
			{
				$order_budgets[$_order_id] = $soworkorder->get_budget($_order_id);
			}

			foreach($order_budgets as $_order_id => $order_budget)
			{

				foreach($order_budget as $budget_entry)
				{
					$period	 = $budget_entry['period'];
					$year	 = $budget_entry['year'];

					$_found_actual_cost = false;
					if(isset($project_budget[$period]))
					{
						$_orders[$period]['actual_cost'] += $budget_entry['actual_cost'];
						$_found_actual_cost = true;
					}

					$_found = false;
					if(isset($project_budget[$period]) && !$budget_entry['closed_order'])
					{
						$_orders[$period]['sum_oblications'] += $budget_entry['sum_oblications'];
						$_orders[$period]['sum_orders'] += $budget_entry['sum_orders'];
						$_found = true;
					}
					else
					{
						for($i = 0; $i < 13; $i++)
						{
							$_period = $year . sprintf("%02s", $i);
							if(isset($project_budget[$_period]))
							{
								if(!$_found_actual_cost)
								{
									$_orders[$_period]['actual_cost'] += $budget_entry['actual_cost'];
									$_found_actual_cost = true;
								}
								$_orders[$_period]['sum_oblications'] += $budget_entry['sum_oblications'];
								$_orders[$_period]['sum_orders'] += $budget_entry['sum_orders'];

								$_found = true;
								break;
							}
						}
					}

					if(!$_found_actual_cost)
					{
						$_orders[$period]['actual_cost'] += $budget_entry['actual_cost'];
					}
					if(!$_found)
					{
						$_orders[$period]['sum_oblications'] += $budget_entry['sum_oblications'];
						$_orders[$period]['sum_orders'] += $budget_entry['sum_orders'];
					}
				}
			}
			$sort_period = array();

			$_values = array();
			foreach($project_budget as $period => $_budget)
			{
				$sort_period[]		 = $period;
				$_values[$period]	 = array
				(
					'project_id'		 => $project_id,
					'period'			 => $period,
					'budget'			 => $_budget,
					'sum_orders'		 => $_orders[$period]['sum_orders'],
					'sum_oblications'	 => $_orders[$period]['sum_oblications'],
					'actual_cost'		 => $_orders[$period]['actual_cost'],
					'deviation_acc'		 => 0
				);
				unset($_orders[$period]);
			}
			unset($_budget);
			unset($period);

			if(isset($_orders) && $_orders)
			{
				foreach($_orders as $period => $_budget)
				{
					$sort_period[]		 = $period;
					$_values[$period]	 = array
					(
						'project_id'		 => $project_id,
						'period'			 => $period,
						'budget'			 => 0,
						'sum_orders'		 => $_budget['sum_orders'],
						'sum_oblications'	 => $_budget['sum_oblications'],
						'actual_cost'		 => $_budget['actual_cost'],
						'deviation_acc'		 => 0
					);
				}
			}

			ksort($_values);

			$values = array();

			$total_sum = 0;
			foreach($_values as $period => $_budget)
			{
				$values[] = $_budget;
				if($active_period[$period])
				{
					$total_sum +=$_budget['budget'];
				}
			}

			$corretion		 = $total_sum >= 0 ? 1 : -1;
			$deviation_acc	 = 0;
			$budget_acc		 = 0;
			$_year = 0;
			foreach($values as &$entry)
			{
				$entry['year']	 = substr($entry['period'], 0, 4);
				$month			 = substr($entry['period'], 4, 2);
				$entry['month']	 = $month == '00' ? '' : $month;

				/**
				 * operation: start over each year
				 */
				if($project_type_id == 1 && $_year != $entry['year'])
				{
					$_year = $entry['year'];
					$deviation_acc	 = 0;
					$budget_acc		 = 0;
				}

				if(abs($entry['actual_cost']) > 0 )
				{
					$_diff_start	 = abs($entry['budget']) > 0 ? $entry['budget'] : $entry['sum_orders'];
					$entry['diff']	 = $_diff_start - $entry['sum_oblications'] - $entry['actual_cost'];

					$_deviation	 = $entry['budget'] - $entry['actual_cost'];
					$deviation	 = $_deviation;
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

				$entry['deviation_percent_period']	 = $corretion * $deviation / $entry['budget'] * 100;
				$entry['deviation_percent_acc']		 = $corretion * $entry['deviation_acc'] / $total_sum * 100;
				$entry['closed']					 = $closed_period[$entry['period']];
				$entry['active']					 = $active_period[$entry['period']];
			}

			return $values;
		}

		function delete_period_from_budget($project_id, $data)
		{
			$project_id = (int) $project_id;
			foreach($data as $entry)
			{
				$when	 = explode('_', $entry);
				$sql	 = "DELETE FROM fm_project_budget WHERE project_id = {$project_id} AND year = " . (int) $when[0] . ' AND month = ' . (int) $when[1];
				$this->db->query($sql, __LINE__, __FILE__);
			}
		}

		function close_period_from_budget($project_id, $data)
		{
			$project_id				 = (int) $project_id;
			$closed_orig_b_period	 = isset($data['closed_orig_b_period']) && $data['closed_orig_b_period'] ? $data['closed_orig_b_period'] : array();
			$closed_b_period		 = isset($data['closed_b_period']) && $data['closed_b_period'] ? $data['closed_b_period'] : array();

			$close_period	 = array();
			$open_period	 = array();

			foreach($closed_orig_b_period as $period)
			{
				if(!in_array($period, $closed_b_period))
				{
					$open_period[] = $period;
				}
			}

			foreach($closed_b_period as $period)
			{
				if(!in_array($period, $closed_orig_b_period))
				{
					$close_period[] = $period;
				}
			}

			foreach($close_period as $period)
			{
				$when	 = explode('_', $period);
				$sql	 = "UPDATE fm_project_budget SET closed = 1 WHERE project_id = {$project_id} AND year =" . (int) $when[0] . ' AND month = ' . (int) $when[1];
				$this->db->query($sql, __LINE__, __FILE__);
			}

			foreach($open_period as $period)
			{
				$when	 = explode('_', $period);
				$sql	 = "UPDATE fm_project_budget SET closed = 0 WHERE project_id = {$project_id} AND year =" . (int) $when[0] . ' AND month = ' . (int) $when[1];
				$this->db->query($sql, __LINE__, __FILE__);
			}
//_debug_array($close_period);
//_debug_array($open_period);die();
		}

		function activate_period_from_budget($project_id, $data)
		{
			$project_id		 = (int) $project_id;
			$close_period	 = array();
			$open_period	 = array();

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
				$sql	 = "UPDATE fm_project_budget SET active = 1 WHERE project_id = {$project_id} AND year =" . (int) $when[0] . ' AND month = ' . (int) $when[1];
				$this->db->query($sql, __LINE__, __FILE__);
			}

			foreach($inactive_period as $period)
			{
				$when	 = explode('_', $period);
				$sql	 = "UPDATE fm_project_budget SET active = 0 WHERE project_id = {$project_id} AND year =" . (int) $when[0] . ' AND month = ' . (int) $when[1];
				$this->db->query($sql, __LINE__, __FILE__);
			}
//_debug_array($close_period);
//_debug_array($open_period);die();
		}

		function update_request_status($project_id = '', $status = '', $category = 0, $coordinator = 0)
		{
			$historylog_r = CreateObject('property.historylog', 'request');

			$request = $this->interlink->get_specific_relation('property', '.project.request', '.project', $project_id, 'target');

			foreach($request as $request_id)
			{
				$this->db->query("SELECT status,category,coordinator FROM fm_request WHERE id='{$request_id}'", __LINE__, __FILE__);

				$this->db->next_record();

				$old_status		 = $this->db->f('status');
				$old_category	 = (int) $this->db->f('category');
				$old_coordinator = (int) $this->db->f('coordinator');

				if($old_status != $status)
				{
					$historylog_r->add('S', $request_id, $status);
				}

				if((int) $old_category != (int) $category)
				{
					$historylog_r->add('T', $request_id, $category);
				}

				if((int) $old_coordinator != (int) $coordinator)
				{
					$historylog_r->add('C', $request_id, $coordinator);
				}

				$this->db->query("UPDATE fm_request SET status='{$status}',coordinator='{$coordinator}' WHERE id='{$request_id}'", __LINE__, __FILE__);
			}
		}

		function check_request($request_id)
		{
			$target = $this->interlink->get_specific_relation('property', '.project.request', '.project', $request_id, 'target');
			if($target)
			{
				return $target[0];
			}
		}

		function add_request($add_request, $id)
		{
			for($i = 0; $i < count($add_request['request_id']); $i++)
			{
				$project_id = $this->check_request($add_request['request_id'][$i]);

				if(!$project_id)
				{
					$interlink_data = array
					(
						'location1_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.project.request'),
						'location1_item_id'	 => $add_request['request_id'][$i],
						'location2_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.project'),
						'location2_item_id'	 => $id,
						'account_id'		 => $this->account
					);

					$this->interlink->add($interlink_data);

					$this->db->query("UPDATE fm_request SET project_id='$id' WHERE id='" . $add_request['request_id'][$i] . "'", __LINE__, __FILE__);

					$request_project_hookup_status = isset($this->config->config_data['request_project_hookup_status']) && $this->config->config_data['request_project_hookup_status'] ? $this->config->config_data['request_project_hookup_status'] : false;

					if($request_project_hookup_status)
					{
						$this->db->query("UPDATE fm_request SET status='{$request_project_hookup_status}' WHERE id='" . $add_request['request_id'][$i] . "'", __LINE__, __FILE__);
					}

					$receipt['message'][] = array('msg' => lang('request %1 has been added', $add_request['request_id'][$i]));
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('request %1 has already been added to project %2', $add_request['request_id'][$i], $project_id));
				}
			}

			return $receipt;
		}

		function delete($project_id)
		{
			$request = $this->interlink->get_specific_relation('property', '.project.request', '.project', $project_id);

			$sql = "SELECT id as workorder_id FROM fm_workorder WHERE project_id='$project_id'";
			$this->db->query($sql, __LINE__, __FILE__);

			while($this->db->next_record())
			{
				$workorder_id[] = $this->db->f('workorder_id');
			}

			$this->db->transaction_begin();

			foreach($request as $request_id)
			{
				$this->db->query("UPDATE fm_request set project_id = NULL where id='{$request_id}'", __LINE__, __FILE__);
			}

			$this->db->query("DELETE FROM fm_project WHERE id='{$project_id}'", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_project_history  WHERE  history_record_id='" . $project_id . "'", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_projectbranch  WHERE  project_id='" . $project_id . "'", __LINE__, __FILE__);
//			$this->db->query("DELETE FROM fm_origin WHERE destination ='project' AND destination_id ='" . $project_id . "'",__LINE__,__FILE__);
			$this->interlink->delete_at_origin('property', '.project.request', '.project', $project_id, $this->db);
			$this->interlink->delete_at_target('property', '.project', $project_id, $this->db);

			$this->db->query("DELETE FROM fm_workorder WHERE project_id='{$project_id}'", __LINE__, __FILE__);

			for($i = 0; $i < count($workorder_id); $i++)
			{
				$this->db->query("DELETE FROM fm_workorder_budget WHERE order_id='{$workorder_id[$i]}'", __LINE__, __FILE__);
				$this->db->query("DELETE FROM fm_wo_hours WHERE workorder_id='{$workorder_id[$i]}'", __LINE__, __FILE__);
				$this->db->query("DELETE FROM fm_workorder_history  WHERE  history_record_id='{$workorder_id[$i]}'", __LINE__, __FILE__);
			}

			$this->db->transaction_commit();
		}

		private function transfer_budget($id, $budget, $year)
		{
			$this->db->transaction_begin();

			$id					 = (int) $id;
			$year				 = (int) $year;
			$latest_year		 = (int) $budget['latest_year'];
			$this->db->query("SELECT periodization_id, project_type_id FROM fm_project WHERE id = {$id}", __LINE__, __FILE__);
			$this->db->next_record();
			$periodization_id	 = $this->db->f('periodization_id');
			$project_type_id	 = $this->db->f('project_type_id');

			if($project_type_id == 2) // investment
			{
				// total budget
				$this->db->query("SELECT sum(budget) as budget FROM fm_project_budget WHERE project_id = {$id} AND year = {$latest_year} AND active = 1", __LINE__, __FILE__);
				$this->db->next_record();
				$last_budget = $this->db->f('budget');

				if(!abs($last_budget) > 0)
				{
					$this->update_budget($id, $year, $periodization_id, 0, true, 'update', true);
					$this->db->transaction_commit();
					return;
					//		throw new Exception('property_soproject::transfer_budget() - no budget to transfer for this investment project: ' . $id);
				}

				//paid last year
				$this->db->query("SELECT sum(amount) as paid FROM fm_project"
				. " {$this->join} fm_workorder ON fm_project.id = fm_workorder.project_id"
				. " {$this->join} fm_orders_paid_or_pending_view ON fm_workorder.id = fm_orders_paid_or_pending_view.order_id"
				. " WHERE periode > {$latest_year}00 AND periode < {$latest_year}13 AND fm_project.id = {$id}", __LINE__, __FILE__);
				$this->db->next_record();
				$paid_last_year = $this->db->f('paid');

				$subtract				 = $last_budget - $paid_last_year;
				$_perform_subtraction	 = false;

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
				/*
				  _debug_array($last_budget);
				  _debug_array($paid_last_year);
				  _debug_array($subtract);
				  _debug_array($_perform_subtraction);
				  die();
				 */
				if($_perform_subtraction)
				{
					$transferred = $this->update_budget($id, $latest_year, $periodization_id, $subtract, false, 'subtract');
					$new_budget	 = $last_budget - $paid_last_year;
				}
				else
				{
					$new_budget = 0;
				}

				$this->update_budget($id, $year, $periodization_id, $new_budget, true, 'update', true);
			}
			else if($project_type_id == 1)//operation
			{
				//		if($budget['budget_amount'])
				{
					$this->db->query("UPDATE fm_project_budget SET active = 0 WHERE project_id = {$id}", __LINE__, __FILE__); // previous
					$this->update_budget($id, $year, $periodization_id, (int) $budget['budget_amount'], true, 'update', true);
				}
			}

			$this->db->transaction_commit();
		}

		public function bulk_update_status($start_date, $end_date, $status_filter, $status_new, $execute, $type, $user_id = 0, $ids, $paid = false, $closed_orders = false, $ecodimb = 0, $transfer_budget_year = 0, $new_budget = array(), $b_account_id = 0)
		{
			if($transfer_budget_year && $execute && $new_budget)
			{
				//		echo "<H1> Overføre budsjett for valgte prosjekt/bestillinger til år {$transfer_budget_year} </H1>";
				$soworkorder = CreateObject('property.soworkorder');

				foreach($ids as $_id)
				{
					if((int) $new_budget[$_id]['latest_year'] >= (int) $transfer_budget_year)
					{
						continue;
					}
					switch($type)
					{
						case 'project':
							try
							{
								$this->transfer_budget($_id, $new_budget[$_id], $transfer_budget_year);
							}
							catch(Exception $e)
							{
								if($e)
								{
									phpgwapi_cache::message_set($e->getMessage(), 'error');
								}
							}
							break;
						case 'workorder':
							try
							{
								$soworkorder->transfer_budget($_id, $new_budget[$_id], $transfer_budget_year);
							}
							catch(Exception $e)
							{
								if($e)
								{
									phpgwapi_cache::message_set($e->getMessage(), 'error');
								}
							}

							break;
						default:
							throw new Exception('property_soproject::bulk_update_status() - not a valid type');
					}
				}

//				die();
			}

			$start_date	 = $start_date ? phpgwapi_datetime::date_to_timestamp($start_date) : time();
			$start_date -= 3600 * 24;
			$end_date	 = $end_date ? phpgwapi_datetime::date_to_timestamp($end_date) : time();

			$filter = '';
			if($user_id)
			{
				$user_id = (int) $user_id;
				$filter .= "AND fm_{$type}.user_id = $user_id";
			}

			if($ecodimb)
			{
				$ecodimb = (int) $ecodimb;
				$filter .= "AND fm_{$type}.ecodimb = $ecodimb";
			}

			if($status_filter)
			{
				if($status_filter == 'open')
				{
					$filter .= " AND fm_{$type}_status.closed IS NULL";
				}
				else
				{
					$filter .= " AND fm_{$type}.status='{$status_filter}' ";
				}
			}

			switch($type)
			{
				case 'project':

					$sql_budget = "SELECT DISTINCT year, month, active, sum(budget) as amount FROM fm_project_budget WHERE ";
					$sql_budget .= 'project_id = %d GROUP BY year, month, active ORDER BY year';

					if($closed_orders)
					{
						$filter .= " AND fm_open_workorder_view.project_id IS NULL";
					}

					$table			 = 'fm_project';
					$status_table	 = 'fm_project_status';
					$title_field	 = 'fm_project.name as title';
					$this->_update_status_project($execute, $status_new, $ids);
					$sql			 = "SELECT DISTINCT {$table}.id,{$status_table}.closed, {$status_table}.descr as status ,{$title_field},{$table}.start_date,{$table}.project_type_id, count(project_id) as num_open FROM {$table}"
					. " {$this->join} {$status_table} ON  {$table}.status = {$status_table}.id "
					. " {$this->left_join} fm_open_workorder_view ON {$table}.id = fm_open_workorder_view.project_id "
					. " WHERE ({$table}.start_date > {$start_date} AND {$table}.start_date < {$end_date} OR {$table}.start_date IS NULL)  {$filter}"
					. " GROUP BY {$table}.id, {$status_table}.closed, {$status_table}.descr ,{$table}.name, {$table}.start_date,project_type_id"
					. " ORDER BY {$table}.id DESC";

					break;
				case 'workorder':

					$sql_budget = "SELECT DISTINCT year, month, active, sum(combined_cost) as amount FROM fm_workorder_budget WHERE ";
					$sql_budget .= 'order_id = %d GROUP BY year, month, active ORDER BY year';

					if($b_account_id)
					{
						$filter .= " AND fm_workorder.account_id = '{$b_account_id}'";
					}

					$table			 = 'fm_workorder';
					$status_table	 = 'fm_workorder_status';
					$title_field	 = 'fm_workorder.title';
					$actual_cost	 = ',actual_cost';

					$join_method = "{$this->join} {$status_table} ON  {$table}.status = {$status_table}.id";
					$join_method .= " {$this->join} fm_project ON  {$table}.project_id = fm_project.id";

					if($paid)
					{
						$join_method .= " {$this->join} fm_orders_actual_cost_view ON fm_workorder.id = fm_orders_actual_cost_view.order_id";
						$actual_cost	 = ',fm_orders_actual_cost_view.actual_cost';
						$group_method	 = '';
					}
					else
					{
						$start_period	 = (date('Y') - 1) . '00';
						$end_period		 = (date('Y') - 1) . 13;
						$join_method .= " {$this->left_join} fm_ecobilagoverf ON ( fm_workorder.id = fm_ecobilagoverf.pmwrkord_code AND fm_ecobilagoverf.periode > $start_period AND fm_ecobilagoverf.periode < $end_period)";
						$actual_cost	 = ',sum(fm_ecobilagoverf.godkjentbelop) AS actual_cost';
						$group_method	 = "GROUP BY fm_workorder.id, fm_workorder.account_id, fm_workorder_status.closed,fm_workorder_status.descr,fm_project.project_type_id";
					}

					$this->_update_status_workorder($execute, $status_new, $ids);
					$sql = "SELECT {$table}.id, {$table}.project_id,{$status_table}.closed, {$table}.account_id, {$status_table}.descr as status ,{$title_field},{$table}.start_date {$actual_cost},"
					. " project_type_id, continuous"
					. " FROM {$table} {$join_method}"
					. " WHERE ({$table}.start_date > {$start_date} AND {$table}.start_date < {$end_date} {$filter}) OR {$table}.start_date is NULL"
					. " {$group_method} ORDER BY {$table}.id DESC";
					break;
				default:
					return array();
			}

			$project_types = array
			(
				1	 => lang('operation'),
				2	 => lang('investment'),
				3	 => lang('buffer')
			);

			$this->db->query($sql, __LINE__, __FILE__);
			$values		 = array();
			$dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			while($this->db->next_record())
			{
				$values[] = array
				(
					'id'				 => $this->db->f('id'),
					'project_id'		 => $this->db->f('project_id'),
					'closed'			 => $this->db->f('closed'),
					'title'				 => $this->db->f('title', true),
					'status'			 => $this->db->f('status', true),
					'actual_cost'		 => (float) $this->db->f('actual_cost'),
					'start_date'		 => $GLOBALS['phpgw']->common->show_date($this->db->f('start_date'), $dateformat),
					'num_open'			 => (int) $this->db->f('num_open'),
					'project_type_id'	 => $this->db->f('project_type_id'),
					'continuous'		 => $this->db->f('continuous') ? X : '',
					'project_type'		 => $project_types[$this->db->f('project_type_id')],
					'b_account_id'		 => $this->db->f('account_id')// only applies to workorders
				);
			}

			foreach($values as &$entry)
			{
				$sql = sprintf($sql_budget, $entry['id']);
				$this->db->query($sql, __LINE__, __FILE__);

				$budget			 = array();
				$_budget		 = array();
				$_year			 = 0;
				$_active_amount	 = array();

				while($this->db->next_record())
				{
					$_year	 = $this->db->f('year');
					$_amount = $this->db->f('amount');
					$_active = $this->db->f('active') ? X : 0;
					if($_active)
					{
						$_active_amount[$_year] += $_amount;
					}

					$_budget[$_year] += $_amount;
				}

				foreach($_budget as $__year => $__budget)
				{
					$budget[] = $__year . ' [' . number_format((int) $_active_amount[$__year], 0, ',', '.') . '/' . number_format((int) $__budget, 0, ',', '.') . ']';
				}

				$entry['budget']		 = implode(' ;', $budget);
				$entry['latest_year']	 = $_year;
				$entry['active_amount']	 = array_sum($_active_amount);
			}

			return $values;
		}

		protected function _update_status_project($execute, $status_new, $ids)
		{
			if(!$execute || !$status_new)
			{
				return;
			}
			$historylog = CreateObject('property.historylog', 'project');

			$workorder_closed_status = isset($this->config->config_data['workorder_closed_status']) && $this->config->config_data['workorder_closed_status'] ? $this->config->config_data['workorder_closed_status'] : false;

			$this->db->transaction_begin();
			foreach($ids as $id)
			{
				if(!$id)
				{
					continue;
				}

				$this->db->query("SELECT status FROM fm_project WHERE id = '{$id}'", __LINE__, __FILE__);
				$this->db->next_record();
				$old_status = $this->db->f('status');

				if($old_status != $status_new)
				{
					$this->db->query("UPDATE fm_project SET status = '{$status_new}' WHERE id = '{$id}'", __LINE__, __FILE__);
					$historylog->add('S', $id, $status_new, $old_status);
					$historylog->add('RM', $id, 'Status endret via masseoppdatering');
				}

				$action_params_approved = array
				(
					'appname'			 => 'property',
					'location'			 => '.project',
					'id'				 => $id,
					'responsible'		 => $this->account,
					'responsible_type'	 => 'user',
					'action'			 => 'approval',
					'remark'			 => '',
					'deadline'			 => ''
				);

				$this->db->query("SELECT * FROM fm_project_status WHERE id = '{$status_new}'");
				$this->db->next_record();
				$approved	 = $this->db->f('approved');
				$closed		 = $this->db->f('closed');

				if($approved || $closed)
				{
					execMethod('property.sopending_action.close_pending_action', $action_params_approved);
				}

				if($closed && $workorder_closed_status)
				{
					$sql = "SELECT fm_workorder.id FROM fm_workorder"
					. " {$this->join} fm_workorder_status ON fm_workorder.status  = fm_workorder_status.id"
					. " WHERE project_id = '{$id}' AND closed IS NULL";

					$this->db->query($sql);
					$orders[] = array();
					while($this->db->next_record())
					{
						$orders[] = $this->db->f('id');
					}
					$this->_update_status_workorder($execute, $workorder_closed_status, $orders);
				}
			}

			$this->db->transaction_commit();
		}

		protected function _update_status_workorder($execute, $status_new, $ids)
		{
			if(!$execute || !$status_new)
			{
				return;
			}
			$historylog = CreateObject('property.historylog', 'workorder');

			if($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			foreach($ids as $id)
			{
				if(!$id)
				{
					continue;
				}

				$this->db->query("SELECT status, vendor_id FROM fm_workorder WHERE id = '{$id}'", __LINE__, __FILE__);
				$this->db->next_record();
				$old_status	 = $this->db->f('status');
				$vendor_id	 = $this->db->f('vendor_id');

				if($old_status != $status_new)
				{
					$this->db->query("UPDATE fm_workorder SET status = '{$status_new}' WHERE id = '{$id}'", __LINE__, __FILE__);
					$historylog->add('S', $id, $status_new, $old_status);
					$historylog->add('RM', $id, 'Status endret via masseoppdatering eller prosjekt');
				}

				$action_params_approved = array
				(
					'appname'			 => 'property',
					'location'			 => '.project.workorder',
					'id'				 => $id,
					'responsible'		 => $this->account,
					'responsible_type'	 => 'user',
					'action'			 => 'approval',
					'remark'			 => '',
					'deadline'			 => ''
				);

				$action_params_progress = array
				(
					'appname'			 => 'property',
					'location'			 => '.project.workorder',
					'id'				 => $id,
					'responsible'		 => $vendor_id,
					'responsible_type'	 => 'vendor',
					'action'			 => 'remind',
					'remark'			 => '',
					'deadline'			 => ''
				);

				$this->db->query("SELECT * FROM fm_workorder_status WHERE id = '{$status_new}'");
				$this->db->next_record();
				if($this->db->f('approved'))
				{
					execMethod('property.sopending_action.close_pending_action', $action_params_approved);
				}
				if($this->db->f('in_progress'))
				{
					execMethod('property.sopending_action.close_pending_action', $action_params_progress);
				}
				if($this->db->f('delivered') || $this->db->f('closed'))
				{
					execMethod('property.sopending_action.close_pending_action', $action_params_approved);
					execMethod('property.sopending_action.close_pending_action', $action_params_progress);
				}
			}

			if(!$this->global_lock)
			{
				$this->db->transaction_commit();
			}
		}

		public function get_user_list()
		{
			$values	 = array();
			$users	 = $GLOBALS['phpgw']->accounts->get_list('accounts', $start	 = -1, $sort	 = 'ASC', $order	 = 'account_lastname', $query, $offset	 = -1);
			$sql	 = 'SELECT DISTINCT coordinator AS user_id FROM fm_project';
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

		public function get_periodizations_with_outline()
		{
			$values	 = array();
			$sql	 = 'SELECT DISTINCT fm_eco_periodization.id, fm_eco_periodization.descr FROM fm_eco_periodization'
			. " {$this->join} fm_eco_periodization_outline ON fm_eco_periodization.id = fm_eco_periodization_outline.periodization_id"
			. " ORDER BY id ASC";
			$this->db->query($sql, __LINE__, __FILE__);

			while($this->db->next_record())
			{
				$id	 = $this->db->f('id');

				$values[] = array
				(
					'id'	 => $id,
					'name'	 => $id . ' - ' . $this->db->f('descr'),
				);
			}

			return $values;
		}

		public function get_filter_year_list()
		{
			$sql = 'SELECT min(start_date) AS start_date FROM fm_project WHERE start_date <> 0';
			$this->db->query($sql, __LINE__, __FILE__);
			if($this->db->next_record() && $this->db->f('start_date'))
			{
				$start_year = date('Y', $this->db->f('start_date'));
			}
			else
			{
				$start_year = date('Y');
			}

			$end_year	 = date('Y') + 1;
			$year_list	 = array();
			for($i = $start_year; $i < $end_year; $i++)
			{
				$year_list[] = array
				(
					'id'	 => $i,
					'name'	 => $i
				);
			}
			$year_list = array_reverse($year_list);

			return $year_list;
		}

		public function get_order_time_span($id)
		{
			if(!$id)
			{
				return array();
			}

			$current_year	 = date('Y');
			$found			 = false;
			$year_list		 = array();
			$sql			 = 'SELECT min(start_date) AS start_date, max(end_date) AS end_date FROM fm_workorder WHERE project_id = ' . (int) $id;
			$this->db->query($sql, __LINE__, __FILE__);
			if($this->db->next_record())
			{
				$start_year	 = $this->db->f('start_date') ? date('Y', $this->db->f('start_date')) : date('Y');
				$end_year	 = $this->db->f('end_date') ? date('Y', $this->db->f('end_date')) : date('Y');

				for($i = $start_year; $i < ($end_year + 1); $i++)
				{
					if($current_year == $i)
					{
						$found = true;
					}

					$year_list[] = array
					(
						'id'	 => $i,
						'name'	 => $i
					);
				}
			}
			if(!$found)
			{
				if($start_year < $current_year)
				{
					$year_list[] = array
					(
						'id'	 => $current_year,
						'name'	 => $current_year
					);
				}
				else
				{
					array_unshift($year_list, array('id' => $current_year, 'name' => $current_year));
				}
			}

			return $year_list;
		}

		public function get_missing_project_budget()
		{
			$values = array();

			$sql = "SELECT fm_project_budget_year_from_order_view.project_id,fm_project_budget_year_from_order_view.year"
			. " FROM fm_project_budget_year_from_order_view"
			. " {$this->left_join} fm_project_budget_year_view ON (fm_project_budget_year_from_order_view.project_id = fm_project_budget_year_view.project_id AND fm_project_budget_year_from_order_view.year = fm_project_budget_year_view.year)"
			. " WHERE fm_project_budget_year_view.project_id IS NULL"
			. " ORDER BY fm_project_budget_year_from_order_view.project_id,fm_project_budget_year_from_order_view.year";

			$this->db->query($sql, __LINE__, __FILE__);

			while($this->db->next_record())
			{
				$values[] = array
				(
					'project_id' => $this->db->f('project_id'),
					'year'		 => $this->db->f('year'),
				);
			}
			foreach($values as $key => $value)
			{
				$this->db->transaction_begin();

				$this->check_and_update_project_budget($value['project_id'], $value['year'], true);

				$this->db->transaction_commit();
			}
			return $values;
		}

		/**
		 * Add budget to project if missing.
		 * @param integer $project_id
		 * @param integer $year
		 * @param boolean $force
		 */
		public function check_and_update_project_budget($project_id, $year, $force = false)
		{
			$update_project_budget_from_order = isset($this->config->config_data['update_project_budget_from_order']) && $this->config->config_data['update_project_budget_from_order'] ? $this->config->config_data['update_project_budget_from_order'] : false;

			if(!$force && !$update_project_budget_from_order)
			{
				return;
			}

			$project_id		 = (int) $project_id;
			$year			 = $year ? (int) $year : date('Y');
			$current_year	 = date('Y');
			$activate		 = false;

			if($year == $current_year)
			{
				$activate = true;
			}

			$ids = array();
			$this->db->query("SELECT id FROM fm_workorder WHERE project_id = {$project_id}", __LINE__, __FILE__);
			while($this->db->next_record())
			{
				$id = $this->db->f('id');
				$ids[] = $id;
				phpgwapi_cache::system_clear('property', "budget_order_{$id}");
			}
			if(!$ids)
			{
				return false;
			}
			$this->db->query("SELECT sum(budget) AS budget FROM fm_workorder_budget WHERE year = {$year} AND order_id IN (" . implode(',', $ids) . ')', __LINE__, __FILE__);
			$this->db->next_record();
			$workorder_budget = $this->db->f('budget');

			$this->db->query("SELECT sum(budget) AS budget FROM fm_project_budget WHERE project_id = {$project_id} AND year = {$year}", __LINE__, __FILE__);
			$this->db->next_record();
			$project_budget = $this->db->f('budget');

			$update = false;

			if($project_budget < 0 && $workorder_budget < $project_budget)
			{
				$update = true;
			}
			else if($workorder_budget > $project_budget)
			{
				$update = true;
			}

			if($update)
			{
				$this->db->query("UPDATE fm_project_budget SET active = 0 WHERE project_id = {$project_id} AND year != {$current_year}", __LINE__, __FILE__);

				$this->db->query("SELECT id, periodization_id FROM fm_project WHERE id = {$project_id}", __LINE__, __FILE__);
				if($this->db->next_record())
				{
					$periodization_id = (int) $this->db->f('periodization_id');

					$this->update_budget($project_id, $year, $periodization_id, (int) $workorder_budget, true, 'update', $activate);
				}
			}
		}

		/**
		 * Add budget to project if missing.
		 * @param integer $project_id
		 */
		protected function _update_project_budget($project_id)
		{
			$years	= array();
			$ids	= array();
			$this->db->query("SELECT id FROM fm_workorder WHERE project_id = {$project_id}", __LINE__, __FILE__);
			while($this->db->next_record())
			{
				$ids[] = $this->db->f('id');
			}
			if($ids)
			{
				$this->db->query("SELECT DISTINCT year FROM fm_workorder_budget WHERE order_id IN (" . implode(',', $ids) . ')', __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					$years[] = $this->db->f('year');
				}
				foreach($years as $_year)
				{
					$this->check_and_update_project_budget($project_id, $_year);
				}
			}
		}

	}	