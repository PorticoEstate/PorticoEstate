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
		var $total_records = 0;

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->interlink 	= CreateObject('property.interlink');
			$this->custom 		= createObject('property.custom_fields');

			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join 	= & $this->db->left_join;
			$this->like			= & $this->db->like;

			$this->acl 			= & $GLOBALS['phpgw']->acl;
			$this->acl->set_account_id($this->account);
			$this->grants		= $this->acl->get_grants('property','.project');
		}

		function select_status_list()
		{
			$this->db->query("SELECT id, descr FROM fm_project_status ORDER BY id ");
			$status = array();
			while ($this->db->next_record())
			{
				$status[] = array
					(
						'id' 	=> $this->db->f('id'),
						'name'	=> $this->db->f('descr',true)
					);
			}
			return $status;
		}

		function select_branch_list()
		{
			$this->db->query("SELECT id, descr FROM fm_branch ORDER BY id ");

			$branch = array();
			while ($this->db->next_record())
			{
				$branch[] = array
					( 
						'id' => $this->db->f('id'),
						'name'	=> $this->db->f('descr',true)
					);
			}
			return $branch;
		}

		function select_key_location_list()
		{
			$this->db->query("SELECT id, descr FROM fm_key_loc ORDER BY descr ");
			$location = array();
			while ($this->db->next_record())
			{
				$location[] = array
					( 
						'id' => $this->db->f('id'),
						'name'	=> $this->db->f('descr',true)
					);
			}
			return $location;
		}

		function read($data)
		{
			$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$filter			= $data['filter']?(int)$data['filter']:0;
			$query			= isset($data['query'])?$data['query']:'';
			$sort			= isset($data['sort'])?$data['sort']:'DESC';
			$order			= isset($data['order'])?$data['order']:'';
			$cat_id			= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
			$status_id		= isset($data['status_id']) && $data['status_id'] ? $data['status_id'] : 'open';
			$start_date		= isset($data['start_date']) && $data['start_date'] ? (int)$data['start_date'] : 0;
			$end_date		= isset($data['end_date']) && $data['end_date'] ? (int)$data['end_date'] : 0;
			$allrows		= isset($data['allrows'])?$data['allrows']:'';
			$wo_hour_cat_id = isset($data['wo_hour_cat_id'])?$data['wo_hour_cat_id']:'';
			$district_id	= isset($data['district_id'])?$data['district_id']:'';
			$dry_run		= isset($data['dry_run']) ? $data['dry_run'] : '';
			$criteria		= isset($data['criteria']) && $data['criteria'] ? $data['criteria'] : array();				

			$sql = $this->bocommon->fm_cache('sql_project_' . !!$wo_hour_cat_id);

			if(!$sql)
			{
				$entity_table = 'fm_project';

				$cols = $entity_table . '.location_code';
				$cols_return[] = 'location_code';

				$cols .= ",$entity_table.id as project_id";
				$cols_return[] 				= 'project_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'project_id';
				$uicols['descr'][]			= lang('Project');
				$uicols['statustext'][]		= lang('Project ID');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= true;

				$cols .= ", project_group";
				$cols_return[] 				= 'project_group';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'project_group';
				$uicols['descr'][]			= lang('group');
				$uicols['statustext'][]		= lang('Project group');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= 'rightClasss';
				$uicols['sortable'][]		= true;

				$cols .= ", fm_project_status.descr as status";
				$cols_return[] 				= 'status';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'status';
				$uicols['descr'][]			= lang('status');
				$uicols['statustext'][]		= lang('status');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= true;

				$cols.= ",$entity_table.entry_date";
				$cols_return[] 				= 'entry_date';
				$cols.= ",$entity_table.start_date";
				$cols_return[] 				= 'start_date';
				$cols.= ",$entity_table.end_date";
				$cols_return[] 				= 'end_date';
				$cols.= ",$entity_table.ecodimb";
				$cols_return[] 				= 'ecodimb';

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
				$cols_return[] 				= 'name';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'name';
				$uicols['descr'][]			= lang('name');
				$uicols['statustext'][]		= lang('Project name');
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
				$cols_return[] 				= 'coordinator';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'coordinator';
				$uicols['descr'][]			= lang('Coordinator');
				$uicols['statustext'][]		= lang('Project coordinator');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= '';

				$cols.= ",(fm_project.budget + fm_project.reserve) as budget";
				$cols_return[] 				= 'budget';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'budget';
				$uicols['descr'][]			= lang('Project budget');
				$uicols['statustext'][]		= lang('Project budget');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= 'myFormatCount2';
				$uicols['classname'][]		= 'rightClasss';
				$uicols['sortable'][]		= '';

				$cols .= ',sum(fm_workorder.combined_cost) as combined_cost';
				$cols_return[] = 'combined_cost';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'combined_cost';
				$uicols['descr'][]			= lang('Sum workorder');
				$uicols['statustext'][]		= lang('Cost - either budget or calculation');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= 'myFormatCount2';
				$uicols['classname'][]		= 'rightClasss';
				$uicols['sortable'][]		= '';

				$cols .= ',(sum(fm_workorder.act_mtrl_cost) + sum(fm_workorder.act_vendor_cost)) as actual_cost';
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
				$uicols['sortable'][]		= '';

				$cols .= ',planned_cost';
				$cols_return[] = 'planned_cost';
/*
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'planned_cost';
				$uicols['descr'][]			= lang('planned cost');
				$uicols['statustext'][]		= lang('ordered minus paid');
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= 'myFormatCount2';
				$uicols['classname'][]		= 'rightClasss';
				$uicols['sortable'][]		= '';
*/
				$cols.= ",$entity_table.user_id";

				$cols .= ',sum(fm_workorder.billable_hours) as billable_hours';
				$cols_return[] = 'billable_hours';

				$joinmethod = " $this->join phpgw_accounts ON ($entity_table.coordinator = phpgw_accounts.account_id))";
				$paranthesis ='(';

				$joinmethod .= " $this->join fm_project_status ON ($entity_table.status = fm_project_status.id))";
				$paranthesis .='(';

				$joinmethod .= " $this->left_join fm_workorder ON ($entity_table.id = fm_workorder.project_id))";
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

				$sql	= $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols'=>$cols,'cols_return'=>$cols_return,
					'uicols'=>$uicols,'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,'query'=>$query,'force_location'=>true));

				$this->bocommon->fm_cache('sql_project_' . !!$wo_hour_cat_id,$sql);

				$this->uicols		= $this->bocommon->uicols;
				$cols_return		= $this->bocommon->cols_return;
				$type_id		= $this->bocommon->type_id;
				$this->cols_extra	= $this->bocommon->cols_extra;

				$this->bocommon->fm_cache('uicols_project_' . !!$wo_hour_cat_id,$this->uicols);
				$this->bocommon->fm_cache('cols_return_project_' . !!$wo_hour_cat_id,$cols_return);
				$this->bocommon->fm_cache('type_id_project_' . !!$wo_hour_cat_id,$type_id);
				$this->bocommon->fm_cache('cols_extra_project_' . !!$wo_hour_cat_id,$this->cols_extra);

			}
			else
			{
				$this->uicols		= $this->bocommon->fm_cache('uicols_project_' . !!$wo_hour_cat_id);
				$cols_return		= $this->bocommon->fm_cache('cols_return_project_' . !!$wo_hour_cat_id);
				$type_id		= $this->bocommon->fm_cache('type_id_project_' . !!$wo_hour_cat_id);
				$this->cols_extra	= $this->bocommon->fm_cache('cols_extra_project_' . !!$wo_hour_cat_id);
			}

			if($dry_run)
			{
				return array();
			}


			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_project.id DESC';
			}

			$where= 'WHERE';

			$filtermethod = '';

			$GLOBALS['phpgw']->config->read();
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
				$cat_list_project	= $cats->return_sorted_array(0, false, '', '', '', false, $cat_id, false);//(0,$limit = false,$query = '',$sort = '',$order = '',$globals = False, $parent_id = $cat_id, $use_acl = false);
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
				$where= 'AND';
			}


			if($wo_hour_cat_id)
			{
				$filtermethod .= " $where fm_wo_hours_category.id=$wo_hour_cat_id ";
				$where= 'AND';
			}

			if($district_id)
			{
				$filtermethod .= " {$where} fm_part_of_town.district_id = {$district_id}";
				$where= 'AND';
			}

			$group_method = ' GROUP BY fm_project_status.descr,loc1_name,fm_project.location_code,fm_project.id,fm_project.entry_date,fm_project.start_date,fm_project.end_date,'
				. 'fm_project.name,fm_project.ecodimb,phpgw_accounts.account_lid,fm_project.user_id,fm_project.address,'
				. 'fm_project.budget,fm_project.reserve,planned_cost,project_group';


			if (is_array($this->grants))
			{
				$grants = $this->grants;
				while (list($user) = each($grants))
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod .= " $where (fm_project.user_id IN(" . implode(',',$public_user_list) . ")";

				$where= 'AND';
			}

			if ($filter)
			{
				$filtermethod .= " $where fm_project.coordinator={$filter}";
				$where= 'AND';
			}

			if ($start_date)
			{
				$end_date	= $end_date + 3600 * 16 + phpgwapi_datetime::user_timezone();
				$start_date	= $start_date - 3600 * 8 + phpgwapi_datetime::user_timezone();

				$filtermethod .= " $where fm_project.start_date >= $start_date AND fm_project.start_date <= $end_date ";
				$where= 'AND';
			}
			//_debug_array($criteria);
			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$query = str_replace(",",'.',$query);
				if(stristr($query, '.') && !isset($criteria[0]['field']))
				{
					$query=explode(".",$query);
					$querymethod = " $where (fm_project.loc1='" . $query[0] . "' AND fm_project.loc".$type_id."='" . $query[1] . "')";
				}
				else if(isset($criteria[0]['field']) && $criteria[0]['field'] == 'fm_project.p_num')
				{
					$query=explode(".",$query);
					$querymethod = " $where (fm_project.p_entity_id='" . (int)$query[1] . "' AND fm_project.p_cat_id='" . (int)$query[2] . "' AND fm_project.p_num='" . (int)$query[3] . "')";
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

			$sql .= " $filtermethod $querymethod";

			//echo substr($sql,strripos($sql,'from'));
			if($GLOBALS['phpgw_info']['server']['db_type']=='postgres')
			{
				$sql2 = 'SELECT count(*) as cnt FROM (SELECT DISTINCT fm_project.id ' . substr($sql,strripos($sql,'from'))  . ') as cnt';
				$this->db->query($sql2,__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f('cnt');
			}
			else
			{
				$sql2 = 'SELECT fm_project.id ' . substr($sql,strripos($sql,'from'))  . ' GROUP BY fm_project.id';
				$this->db->query($sql2,__LINE__,__FILE__);
				$this->total_records = $this->db->num_rows();
			}
			//_debug_array($sql2);
			$project_list = array();
			$sql .= " $group_method";
			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
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
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__, false, $_fetch_single );
				unset($_fetch_single);
			}

			$project_list = array();
			$j=0;
			$k=count($cols_return);
			while ($this->db->next_record())
			{
				for ($i=0;$i<$k;$i++)
				{
					$project_list[$j][$cols_return[$i]] = stripslashes($this->db->f($cols_return[$i]));
					$project_list[$j]['grants'] = (int)$this->grants[$this->db->f('user_id')];
				}
				$location_code=	$this->db->f('location_code');
				$location = explode('-',$location_code);
				$n=count($location);
				for ($m=0;$m<$n;$m++)
				{
					$project_list[$j]['loc' . ($m+1)] = $location[$m];
					$project_list[$j]['query_location']['loc' . ($m+1)]=implode("-", array_slice($location, 0, ($m+1)));
				}

				$j++;
			}
			return $project_list;
		}

		function get_meter_table()
		{
			$config = CreateObject('phpgwapi.config','property');
			$config->read();
			return isset($config->config_data['meter_table'])?$config->config_data['meter_table']:'';
		}

		function read_single($project_id, $values = array())
		{
			$project_id = (int) $project_id;
			$sql = "SELECT * from fm_project WHERE id={$project_id}";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$project = array
					(
						'project_id'			=> $this->db->f('id'),
						'title'					=> $this->db->f('title'),
						'name'					=> $this->db->f('name'),
						'location_code'			=> $this->db->f('location_code'),
						'key_fetch'				=> $this->db->f('key_fetch'),
						'key_deliver'			=> $this->db->f('key_deliver'),
						'other_branch'			=> $this->db->f('other_branch'),
						'key_responsible'		=> $this->db->f('key_responsible'),
						'descr'					=> $this->db->f('descr', true),
						'status'				=> $this->db->f('status'),
						'budget'				=> (int)$this->db->f('budget'),
						'planned_cost'			=> (int)$this->db->f('planned_cost'),
						'reserve'				=> (int)$this->db->f('reserve'),
						'tenant_id'				=> $this->db->f('tenant_id'),
						'user_id'				=> $this->db->f('user_id'),
						'coordinator'			=> $this->db->f('coordinator'),
						'access'				=> $this->db->f('access'),
						'start_date'			=> $this->db->f('start_date'),
						'end_date'				=> $this->db->f('end_date'),
						'cat_id'				=> $this->db->f('category'),
						'grants' 				=> (int)$this->grants[$this->db->f('user_id')],
						'p_num'					=> $this->db->f('p_num'),
						'p_entity_id'			=> $this->db->f('p_entity_id'),
						'p_cat_id'				=> $this->db->f('p_cat_id'),
						'contact_phone'			=> $this->db->f('contact_phone'),
						'project_group'			=> $this->db->f('project_group'),
						'ecodimb'				=> $this->db->f('ecodimb'),
						'b_account_id'			=> $this->db->f('account_group'),
						'contact_id'			=> $this->db->f('contact_id'),
					);

				if ( isset($values['attributes']) && is_array($values['attributes']) )
				{
					$project['attributes'] = $values['attributes'];
					foreach ( $project['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->db->f($attr['column_name']);
					}
				}

				$location_code = $this->db->f('location_code');
				$project['power_meter']		= $this->get_power_meter($location_code);
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

			$this->db->query("SELECT ext_meter_id as power_meter FROM $meter_table where location_code='$location_code' and category='1'",__LINE__,__FILE__);

			$this->db->next_record();

			return $this->db->f('power_meter');
		}

		function project_workorder_data($project_id = '')
		{
			$project_id = (int) $project_id;
			$budget = array();
			$this->db->query("SELECT fm_workorder.title, act_mtrl_cost, act_vendor_cost, budget, fm_workorder.id as workorder_id,contract_sum,"
				." vendor_id, calculation,rig_addition,addition,deviation,charge_tenant,fm_workorder_status.descr as status, fm_workorder.account_id as b_account_id"
				." FROM fm_workorder $this->join fm_workorder_status ON fm_workorder.status = fm_workorder_status.id WHERE project_id={$project_id}");
			while ($this->db->next_record())
			{
				$budget[] = array(
					'workorder_id'		=> $this->db->f('workorder_id'),
					'title'				=> $this->db->f('title',true),
					'budget'			=> (int)$this->db->f('budget'),
					'deviation'			=> $this->db->f('deviation'),
					'calculation'		=> $this->db->f('calculation'),
					'vendor_id'			=> $this->db->f('vendor_id'),
					'act_mtrl_cost'		=> $this->db->f('act_mtrl_cost'),
					'act_vendor_cost'	=> $this->db->f('act_vendor_cost'),
					'charge_tenant'		=> $this->db->f('charge_tenant'),
					'status'			=> $this->db->f('status'),
					'b_account_id'		=> $this->db->f('b_account_id'),
					'contract_sum'		=> (int)$this->db->f('contract_sum'),
				);
			}
			return $budget;
		}

		function branch_p_list($project_id = '')
		{
			$selected = array();
			$this->db->query("SELECT branch_id from fm_projectbranch WHERE project_id=" .  (int)$project_id ,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$selected[] = array('branch_id' => $this->db->f('branch_id'));
			}
			return $selected;
		}

		function increment_project_id()
		{
			$name = 'project';
			$now = time();
			$this->db->query("SELECT value, start_date FROM fm_idgenerator WHERE name='{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$next_id = $this->db->f('value') +1;
			$start_date = (int)$this->db->f('start_date');
			$this->db->query("UPDATE fm_idgenerator SET value = $next_id WHERE name = '{$name}' AND start_date = {$start_date}");
		}

		function next_project_id()
		{
			$name = 'project';
			$now = time();
			$this->db->query("SELECT value FROM fm_idgenerator WHERE name = '{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$id = $this->db->f('value')+1;
			return $id;
		}

		function add($project, $values_attribute = array())
		{
			$receipt = array();
			$historylog	= CreateObject('property.historylog','project');

			while (is_array($project['location']) && list($input_name,$value) = each($project['location']))
			{
				if($value)
				{
					$cols[] = $input_name;
					$vals[] = $value;
				}
			}

			while (is_array($project['extra']) && list($input_name,$value) = each($project['extra']))
			{
				if($value)
				{
					$cols[] = $input_name;
					$vals[] = $value;
				}
			}

			$data_attribute = $this->custom->prepare_for_db('fm_project', $values_attribute);
			if(isset($data_attribute['value_set']))
			{
				foreach($data_attribute['value_set'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= ",'" . implode("','", $vals) . "'";
			}

			if($project['street_name'])
			{
				$address[]= $project['street_name'];
				$address[]= $project['street_number'];
				$address = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($project['location_name']);
			}

			$project['descr'] = $this->db->db_addslashes($project['descr']);
			$project['name'] = $this->db->db_addslashes($project['name']);

			$this->db->transaction_begin();
			$id = $this->next_project_id();
			$values= array
				(
					$id,
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
					$project['contact_id']
				);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->query("INSERT INTO fm_project (id,project_group,name,access,category,entry_date,start_date,end_date,coordinator,status,"
				. "descr,budget,reserve,location_code,address,key_deliver,key_fetch,other_branch,key_responsible,user_id,ecodimb,account_group,contact_id $cols) "
				. "VALUES ($values $vals )",__LINE__,__FILE__);

			if($project['extra']['contact_phone'] && $project['extra']['tenant_id'])
			{
				$this->db->query("update fm_tenant set contact_phone='". $project['extra']['contact_phone']. "' where id='". $project['extra']['tenant_id']. "'",__LINE__,__FILE__);
			}

			if (isset($project['power_meter']) && $project['power_meter'])
			{
				$this->update_power_meter($project['power_meter'],$project['location_code'],$address);
			}

			if (count($project['branch']) != 0)
			{
				while($branch=each($project['branch']))
				{
					$this->db->query("insert into fm_projectbranch (project_id,branch_id) values ({$id},{$branch[1]})",__LINE__,__FILE__);
				}
			}

			if(is_array($project['origin']))
			{
				if($project['origin'][0]['data'][0]['id'])
				{					
					$interlink_data = array
						(
							'location1_id'		=> $GLOBALS['phpgw']->locations->get_id('property', $project['origin'][0]['location']),
							'location1_item_id' => $project['origin'][0]['data'][0]['id'],
							'location2_id'		=> $GLOBALS['phpgw']->locations->get_id('property', '.project'),			
							'location2_item_id' => $id,
							'account_id'		=> $this->account
						);

					$this->interlink->add($interlink_data,$this->db);
				}
			}

			if($this->db->transaction_commit())
			{
				$this->increment_project_id();
				$historylog->add('SO', $id, $project['status']);
				$historylog->add('TO', $id, $project['cat_id']);
				$historylog->add('CO', $id, $project['coordinator']);
				if ($project['remark'])
				{
					$historylog->add('RM', $id, $project['remark']);
				}

				$receipt['message'][] = array('msg'=>lang('project %1 has been saved',$id));
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('the project has not been saved'));
			}

			$receipt['id'] = $id;
			return $receipt;
		}

		function update_power_meter($power_meter,$location_code,$address)
		{
			if(!$meter_table = $this->get_meter_table())
			{
				return;
			}

			$location=explode('-',$location_code);

			$i=1;
			if (isset($location) AND is_array($location))
			{
				foreach($location as $location_entry)
				{
					$cols[] = 'loc' . $i;
					$vals[] = $location_entry;

					$i++;
				}
			}

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= ",'" . implode("','", $vals) . "'";
			}

			$this->db->query("SELECT count(*) as cnt FROM $meter_table where location_code='$location_code' and category=1",__LINE__,__FILE__);

			$this->db->next_record();

			if ( $this->db->f('cnt'))
			{
				$this->db->query("update $meter_table set ext_meter_id='$power_meter',address='$address' where location_code='$location_code' and category='1'",__LINE__,__FILE__);
			}
			else
			{
				$id = $this->bocommon->next_id($meter_table);

				$meter_id	= $this->generate_meter_id($meter_table);
				$this->db->query("insert into $meter_table (id,num,ext_meter_id,category,location_code,entry_date,user_id,address $cols) "
					. "VALUES ('"
					. $id. "','"
					. $meter_id. "','"
					. $power_meter. "',"
					. "1,'"
					. $location_code. "',"
					. time() . ",$this->account, '$address' $vals)",__LINE__,__FILE__);
			}
		}

		function generate_meter_id($meter_table)
		{
			$prefix = 'meter';
			$pos	= strlen($prefix);
			$this->db->query("select max(num) as current from $meter_table where num $this->like ('$prefix%')");
			$this->db->next_record();

			$max = $this->bocommon->add_leading_zero(substr($this->db->f('current'),$pos));

			$meter_id= $prefix . $max;
			return $meter_id;
		}

		function edit($project, $values_attribute = array())
		{
			$historylog	= CreateObject('property.historylog','project');
			$receipt = array();

			if($project['street_name'])
			{
				$address[]= $project['street_name'];
				$address[]= $project['street_number'];
				$address = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($project['location_name']);
			}

			$project['descr'] = $this->db->db_addslashes($project['descr']);
			$project['name'] = $this->db->db_addslashes($project['name']);

			$value_set=array(
				'project_group'		=> $project['project_group'],
				'name'				=> $project['name'],
				'status'			=> $project['status'],
				'category'			=> $project['cat_id'],
				'start_date'		=> $project['start_date'],
				'end_date'			=> $project['end_date'],
				'coordinator'		=> $project['coordinator'],
				'descr'				=> $project['descr'],
				'budget'			=> (int)$project['budget'],
				'reserve'			=> (int)$project['reserve'],
				'key_deliver'		=> $project['key_deliver'],
				'key_fetch'			=> $project['key_fetch'],
				'other_branch'		=> $project['other_branch'],
				'key_responsible'	=> $project['key_responsible'],
				'location_code'		=> $project['location_code'],
				'address'			=> $address,
				'ecodimb'			=> $project['ecodimb'],
				'account_group'		=> $project['b_account_id'],
				'contact_id'		=> $project['contact_id']
			);

			$data_attribute = $this->custom->prepare_for_db('fm_project', $values_attribute, $project['id']);

			if(isset($data_attribute['value_set']))
			{
				$value_set = array_merge($value_set, $data_attribute['value_set']);
			}

			while (is_array($project['location']) && list($input_name,$value) = each($project['location']))
			{
				$value_set[$input_name] = $value;
			}

			while (is_array($project['extra']) && list($input_name,$value) = each($project['extra']))
			{
				$value_set[$input_name] = $value;
			}

			$value_set	= $this->db->validate_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("SELECT status,category,coordinator,budget,reserve FROM fm_project WHERE id = {$project['id']}",__LINE__,__FILE__);
			$this->db->next_record();
			$old_status = $this->db->f('status');
			$old_category = (int)$this->db->f('category');
			$old_coordinator = (int)$this->db->f('coordinator');
			$old_budget = (int)$this->db->f('budget');
			$old_reserve = (int)$this->db->f('reserve');

			$this->db->query("UPDATE fm_project SET $value_set WHERE id= {$project['id']}",__LINE__,__FILE__);

			if($project['extra']['contact_phone'] && $project['extra']['tenant_id'])
			{
				$this->db->query("UPDATE fm_tenant SET contact_phone='". $project['extra']['contact_phone']. "' WHERE id='". $project['extra']['tenant_id']. "'",__LINE__,__FILE__);
			}

			if (isset($project['power_meter']) && $project['power_meter'])
			{
				$this->update_power_meter($project['power_meter'],$project['location_code'],$address);
			}
			// -----------------which branch is represented
			$this->db->query("DELETE FROM fm_projectbranch WHERE project_id={$project['id']}",__LINE__,__FILE__);

			if (count($project['branch']) != 0)
			{
				while($branch=each($project['branch']))
				{
					$this->db->query("INSERT INTO fm_projectbranch (project_id,branch_id) VALUES ({$project['id']}, {$branch[1]})",__LINE__,__FILE__);
				}
			}

			if($project['delete_request'])
			{
				$receipt = $this->delete_request_from_project($project['delete_request'],$project['id']);

			}

			$this->update_request_status($project['id'],$project['status'],$project['cat_id'],$project['coordinator']);
			$this->db->query("SELECT id from fm_workorder WHERE project_id=" .  (int)$project['id'] ,__LINE__,__FILE__);
			$workorders = array();
			while ($this->db->next_record())
			{
				$workorders[] = $this->db->f('id');
			}

			if (($old_status != $project['status']) || $project['confirm_status'])
			{
				$close_pending_action = false;
				$close_workorders = false;
				$this->db->query("SELECT * FROM fm_project_status WHERE id = '{$project['status']}'");
				$this->db->next_record();
				if ($this->db->f('closed') )
				{
					$close_workorders = true;
				}


				if ($this->db->f('approved') )
				{
					$close_pending_action = true;

					$action_params = array
						(
							'appname'			=> 'property',
							'location'			=> '.project',
							'id'				=> (int)$project['id'],
							'responsible'		=> $this->account,
							'responsible_type'  => 'user',
							'action'			=> 'approval',
							'remark'			=> '',
							'deadline'			=> ''
						);

					execMethod('property.sopending_action.close_pending_action', $action_params);
					unset($action_params);
				}

				if ($workorders)
				{
					$historylog_workorder	= CreateObject('property.historylog','workorder');
				}

				if($old_status != $project['status'])
				{
					$historylog->add('S',$project['id'],$project['status'], $old_status);
					$receipt['notice_owner'][]=lang('Status changed') . ': ' . $project['status'];
				}
				else if($old_status != $project['status'] && $close_workorders)
				{
					$historylog->add('S',$project['id'],$project['status'], $old_status);

					$this->db->query("UPDATE fm_workorder SET status='closed' WHERE project_id = {$project['id']}",__LINE__,__FILE__);

					foreach($workorders as $workorder_id)
					{
						$historylog_workorder->add('S',$workorder_id,'closed');
					}

					$receipt['notice_owner'][]=lang('Status changed') . ': ' . $project['status'];
				}
				elseif($project['confirm_status'])
				{
					$historylog->add('SC',$project['id'],$project['status']);

					if ($close_workorders)
					{
						foreach($workorders as $workorder_id)
						{
							$historylog_workorder->add('SC',$workorder_id,'closed');
						}
					}
					$receipt['notice_owner'][]=lang('Status confirmed') . ': ' . $project['status'];
				}

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
			}

			if(isset($project['project_group']) && $project['project_group'])
			{
				reset($workorders);
				foreach($workorders as $workorder_id)
				{
					$this->db->query("UPDATE fm_ecobilag SET project_id = '{$project['project_group']}' WHERE pmwrkord_code = '{$workorder_id}' ",__LINE__,__FILE__);
				}
			}

			if ($old_category != $project['cat_id'])
			{
				$historylog->add('T',$project['id'],$project['cat_id'], $old_category);
			}
			if ($old_coordinator != $project['coordinator'])
			{
				$historylog->add('C',$project['id'],$project['coordinator'], $old_coordinator);
				$receipt['notice_owner'][]=lang('Coordinator changed') . ': ' . $GLOBALS['phpgw']->accounts->id2name($project['coordinator']);
			}

			if ($old_budget != (int)$project['budget'])
			{
				$historylog->add('B',$project['id'],$project['budget'], $old_budget);
			}

			if ($old_reserve != (int)$project['reserve'])
			{
				$historylog->add('BR',$project['id'],$project['reserve'], $old_reserve);
			}

			if ($project['remark'])
			{
				$historylog->add('RM',$project['id'],$project['remark']);
			}

			execMethod('property.soworkorder.update_planned_cost', $project['id']);

			$receipt['id'] = $project['id'];
			$receipt['message'][] = array('msg'=>lang('project %1 has been edited', $project['id']));

			$this->db->transaction_commit();

			return $receipt;
		}

		function delete_request_from_project($request,$project_id)
		{
			foreach ($request as $request_id)
			{
				$this->db->query("UPDATE fm_request set project_id = NULL where id='{$request_id}'",__LINE__,__FILE__);
				$this->interlink->delete_at_origin('property', '.project.request', '.project', $request_id, $this->db);
				$receipt['message'][] = array('msg'=>lang('Request %1 has been deleted from project %2',$request_id,$project_id));
			}
			return $receipt;
		}


		function update_request_status($project_id='',$status='',$category=0,$coordinator=0)
		{
			$historylog_r	= CreateObject('property.historylog','request');

			$request = $this->interlink->get_specific_relation('property', '.project.request', '.project', $project_id, 'target');

			foreach ($request as $request_id)
			{
				$this->db->query("SELECT status,category,coordinator FROM fm_request WHERE id='{$request_id}'",__LINE__,__FILE__);

				$this->db->next_record();

				$old_status = $this->db->f('status');
				$old_category = (int)$this->db->f('category');
				$old_coordinator = (int)$this->db->f('coordinator');

				if ($old_status != $status)
				{
					$historylog_r->add('S',$request_id,$status);
				}

				if ((int)$old_category != (int)$category)
				{
					$historylog_r->add('T',$request_id,$category);
				}

				if ((int)$old_coordinator != (int)$coordinator)
				{
					$historylog_r->add('C',$request_id,$coordinator);
				}

				$this->db->query("UPDATE fm_request SET status='{$status}',coordinator='{$coordinator}' WHERE id='{$request_id}'",__LINE__,__FILE__);
			}
		}

		function check_request($request_id)
		{
			$target = $this->interlink->get_specific_relation('property', '.project.request', '.project', $request_id);

			if ( $target)
			{
				return $target[0];
			}
		}

		function add_request($add_request,$id)
		{
			for ($i=0;$i<count($add_request['request_id']);$i++)
			{
				$project_id=$this->check_request($add_request['request_id'][$i]);

				if(!$project_id)
				{
					$interlink_data = array
						(
							'location1_id'		=> $GLOBALS['phpgw']->locations->get_id('property', '.project.request'),
							'location1_item_id' => $add_request['request_id'][$i],
							'location2_id'		=> $GLOBALS['phpgw']->locations->get_id('property', '.project'),			
							'location2_item_id' => $id,
							'account_id'		=> $this->account
						);

					$this->interlink->add($interlink_data);

					$this->db->query("UPDATE fm_request SET project_id='$id' where id='". $add_request['request_id'][$i] . "'",__LINE__,__FILE__);

					$receipt['message'][] = array('msg'=>lang('request %1 has been added',$add_request['request_id'][$i]));
				}
				else
				{
					$receipt['error'][] = array('msg'=>lang('request %1 has already been added to project %2',$add_request['request_id'][$i],$project_id));
				}

			}

			return $receipt;
		}

		function delete($project_id )
		{
			$request = $this->interlink->get_specific_relation('property', '.project.request', '.project', $project_id);

			$sql = "SELECT id as workorder_id FROM fm_workorder WHERE project_id='$project_id'";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$workorder_id[]	= $this->db->f('workorder_id');
			}

			$this->db->transaction_begin();

			foreach ($request as $request_id)
			{
				$this->db->query("UPDATE fm_request set project_id = NULL where id='{$request_id}'",__LINE__,__FILE__);
			}

			$this->db->query("DELETE FROM fm_project WHERE id='{$project_id}'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_project_history  WHERE  history_record_id='" . $project_id   . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_projectbranch  WHERE  project_id='" . $project_id   . "'",__LINE__,__FILE__);
//			$this->db->query("DELETE FROM fm_origin WHERE destination ='project' AND destination_id ='" . $project_id . "'",__LINE__,__FILE__);
			$this->interlink->delete_at_origin('property', '.project.request', '.project', $project_id, $this->db);
			$this->interlink->delete_at_target('property', '.project', $project_id, $this->db);

			$this->db->query("DELETE FROM fm_workorder WHERE project_id='{$project_id}'",__LINE__,__FILE__);

			for ($i=0;$i<count($workorder_id);$i++)
			{
				$this->db->query("DELETE FROM fm_wo_hours WHERE workorder_id='{$workorder_id[$i]}'",__LINE__,__FILE__);
				$this->db->query("DELETE FROM fm_workorder_history  WHERE  history_record_id='{$workorder_id[$i]}'",__LINE__,__FILE__);
			}

			$this->db->transaction_commit();
		}


		function bulk_update_status($start_date, $end_date, $status_filter, $status_new, $execute, $type, $user_id = 0)
		{
			$start_date = phpgwapi_datetime::date_to_timestamp($start_date);
			$end_date = phpgwapi_datetime::date_to_timestamp($end_date);

			$filter = '';
			if($user_id)
			{
				$user_id = (int) $user_id;
				$filter = "AND user_id = $user_id";
			}

			if($status_filter)
			{
				$user_id = (int) $user_id;
				$filter .= "AND status = '{$status_filter}'";
			}

			switch($type)
			{
				case 'project':
					$table = 'fm_project';
					$status_table = 'fm_project_status';
					$title_field = 'fm_project.name as title';
					break;
				case 'workorder':
					$table = 'fm_workorder';
					$status_table = 'fm_workorder_status';
					$title_field = 'fm_workorder.title';
					break;
				default:
					return array();
			}

			$sql = "SELECT {$table}.id, $status_table.descr as status ,{$title_field} FROM {$table}"
			. " {$this->join} {$status_table} ON  {$table}.status = {$status_table}.id  WHERE start_date > {$start_date} AND start_date < {$end_date} {$filter}";
//_debug_array($sql);			
			$this->db->query($sql,__LINE__,__FILE__);
			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array
				(
					'id'		=> $this->db->f('id'),
					'title'		=> $this->db->f('title',true),
					'status'	=> $this->db->f('status',true)
				);
			}

			return $values;


			$this->db->transaction_begin();


			$this->db->transaction_commit();
		}

	}
