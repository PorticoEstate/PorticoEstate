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

	/**
	 * Description
	 * @package property
	 */

	class property_boproject
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
		(
			'read'				=> true,
			'read_single'		=> true,
			'save'				=> true,
			'delete'			=> true,
			'check_perms'		=> true
		);

		function property_boproject($session=false)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 			= CreateObject('property.soproject');
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->solocation = CreateObject('property.solocation');
			$this->cats					= CreateObject('phpgwapi.categories');
			$this->cats->app_name		= 'property.project';
			$this->cats->supress_info	= true;
			$this->interlink 	= CreateObject('property.interlink');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$status_id	= phpgw::get_var('status_id');
			$wo_hour_cat_id	= phpgw::get_var('wo_hour_cat_id', 'int');

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
			if(isset($status_id))
			{
				$this->status_id = $status_id;
			}
			if(isset($wo_hour_cat_id))
			{
				$this->wo_hour_cat_id = $wo_hour_cat_id;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','project',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','project');

			$this->start			= isset($data['start'])?$data['start']:'';
			$this->query			= isset($data['query'])?$data['query']:'';
			$this->filter			= isset($data['filter'])?$data['filter']:'';
			$this->sort				= isset($data['sort'])?$data['sort']:'';
			$this->order			= isset($data['order'])?$data['order']:'';
			$this->cat_id			= isset($data['cat_id'])?$data['cat_id']:'';
			$this->status_id		= isset($data['status_id'])?$data['status_id']:'';
			$this->wo_hour_cat_id	= isset($data['wo_hour_cat_id'])?$data['wo_hour_cat_id']:'';
		}

		function select_status_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('status_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('status_filter'));
					break;
			}

			$status_entries= $this->so->select_status_list();

			return $this->bocommon->select_list($selected,$status_entries);
		}

		function select_branch_list($selected='')
		{
			$branch_entries= $this->so->select_branch_list();
			return $this->bocommon->select_list($selected,$branch_entries);
		}

		function select_branch_p_list($project_id='')
		{

			$selected		= $this->so->branch_p_list($project_id);
			$branch_entries	= $this->so->select_branch_list();

			$j=0;
			while (is_array($branch_entries) && list(,$branch) = each($branch_entries))
			{
				$branch_list[$j]['id'] = $branch['id'];
				$branch_list[$j]['name'] = $branch['name'];

				for ($i=0;$i<count($selected);$i++)
				{
					if($selected[$i]['branch_id'] == $branch['id'])
					{
						$branch_list[$j]['selected'] = 'selected';
					}
				}
				$j++;
			}

		/*	for ($i=0;$i<count($branch_list);$i++)
			{
				if ($branch_list[$i]['selected'] != 'selected')
				{
					unset($branch_list[$i]['selected']);
				}
			}
		*/

			return $branch_list;
		}


		function select_key_location_list($selected='')
		{

			$key_location_entries= $this->so->select_key_location_list();

			return $this->bocommon->select_list($selected,$key_location_entries);
		}

		function read($start_date='',$end_date='',$allrows='')
		{
			$start_date	= $this->bocommon->date_to_timestamp($start_date);
			$end_date	= $this->bocommon->date_to_timestamp($end_date);

			$project = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'status_id' => $this->status_id,'wo_hour_cat_id' => $this->wo_hour_cat_id,
											'start_date'=>$start_date,'end_date'=>$end_date,'allrows'=>$allrows));
			$this->total_records = $this->so->total_records;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->uicols	= $this->so->uicols;
			$this->uicols['input_type'][]	= 'link';
			$this->uicols['name'][]			= 'ticket_id';
			$this->uicols['descr'][]		= lang('ticket');
			$this->uicols['statustext'][]	= false;

//			$cols_extra		= $this->so->cols_extra;

			foreach ($project as & $entry)
			{
				$entry['start_date'] = $GLOBALS['phpgw']->common->show_date($entry['start_date'],$dateformat);
				$origin = $this->interlink->get_relation('property', '.project', $entry['project_id'], 'origin');
				if($origin[0]['location'] == '.ticket')
				{
					$entry['ticket_id'] = $origin[0]['data'][0]['id'];
				}
			}
			return $project;
		}

		function read_single($project_id)
		{
			$contacts		= CreateObject('property.soactor');
			$contacts->role='vendor';

			$config				= CreateObject('phpgwapi.config');
			$config->read_repository();
			$tax = 1+(isset($config->config_data['fm_tax'])?$config->config_data['fm_tax']:0)/100;

			$project				= $this->so->read_single($project_id);
			$dateformat				= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$project['start_date']			= $GLOBALS['phpgw']->common->show_date($project['start_date'],$dateformat);
			$project['end_date']			= isset($project['end_date']) && $project['end_date'] ? $GLOBALS['phpgw']->common->show_date($project['end_date'],$dateformat) : '';
			$workorder_data				= $this->so->project_workorder_data($project_id);

			$sum_workorder_budget = 0;
			$sum_deviation = 0;
			$sum_workorder_calculation = 0;
			$sum_workorder_actual_cost = 0;

			$custom 		= createObject('property.custom_fields');
			for ($i=0;$i<count($workorder_data);$i++)
			{
				$sum_workorder_budget= $sum_workorder_budget+$workorder_data[$i]['budget'];
				$sum_deviation= $sum_deviation+$workorder_data[$i]['deviation'];
				$sum_workorder_calculation= $sum_workorder_calculation+$workorder_data[$i]['calculation'];
				$sum_workorder_actual_cost= $sum_workorder_actual_cost+$workorder_data[$i]['act_mtrl_cost']+$workorder_data[$i]['act_vendor_cost'];

				$project['workorder_budget'][$i]['workorder_id']=$workorder_data[$i]['workorder_id'];
				$project['workorder_budget'][$i]['budget']=number_format($workorder_data[$i]['budget'], 2, ',', '');
				$project['workorder_budget'][$i]['calculation']=number_format($workorder_data[$i]['calculation']*$tax, 2, ',', '');
				$project['workorder_budget'][$i]['charge_tenant'] = $workorder_data[$i]['charge_tenant'];
				$project['workorder_budget'][$i]['status'] = $workorder_data[$i]['status'];

				if(isset($workorder_data[$i]['vendor_id']) && $workorder_data[$i]['vendor_id'])
				{
					$vendor['attributes'] = $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

					$vendor	= $contacts->read_single($workorder_data[$i]['vendor_id'], $vendor);
					foreach($vendor['attributes'] as $attribute)
					{
						if($attribute['name']=='org_name')
						{
							$project['workorder_budget'][$i]['vendor_name']=$attribute['value'];
							break;
						}
					}
				}
			}
			if($workorder_data)
			{
				$project['sum_workorder_budget']= number_format($sum_workorder_budget, 2, ',', '');
				$project['deviation']= $sum_deviation;
				$project['sum_workorder_calculation']= number_format($sum_workorder_calculation*$tax, 2, ',', '');
				$project['sum_workorder_actual_cost']= number_format($sum_workorder_actual_cost, 2, ',', '');
			}

			if($project['location_code'])
			{
				$project['location_data'] =$this->solocation->read_single($project['location_code']);
			}

			if($project['tenant_id']>0)
			{
				$tenant_data=$this->bocommon->read_single_tenant($project['tenant_id']);
				$project['location_data']['tenant_id']= $project['tenant_id'];
				$project['location_data']['contact_phone']= $tenant_data['contact_phone'];
				$project['location_data']['last_name']	= $tenant_data['last_name'];
				$project['location_data']['first_name']	= $tenant_data['first_name'];
			}
			else
			{
				unset($project['location_data']['tenant_id']);
				unset($project['location_data']['contact_phone']);
				unset($project['location_data']['last_name']);
				unset($project['location_data']['first_name']);
			}

			if($project['p_num'])
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');
				$category = $soadmin_entity->read_single_category($project['p_entity_id'],$project['p_cat_id']);

				$project['p'][$project['p_entity_id']]['p_num']=$project['p_num'];
				$project['p'][$project['p_entity_id']]['p_entity_id']=$project['p_entity_id'];
				$project['p'][$project['p_entity_id']]['p_cat_id']=$project['p_cat_id'];
				$project['p'][$project['p_entity_id']]['p_cat_name'] = $category['name'];
			}

			$project['origin'] = $this->interlink->get_relation('property', '.project', $project_id, 'origin');
			$project['target'] = $this->interlink->get_relation('property', '.project', $project_id, 'target');

//_debug_array($project);
			return $project;
		}

		function read_single_mini($project_id)
		{
			$project						= $this->so->read_single($project_id);
			$dateformat						= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$project['start_date']			= $GLOBALS['phpgw']->common->show_date($project['start_date'],$dateformat);
			$project['end_date']			= isset($project['end_date']) && $project['end_date'] ? $GLOBALS['phpgw']->common->show_date($project['end_date'],$dateformat) : '';

			if($project['location_code'])
			{
				$project['location_data'] =$this->solocation->read_single($project['location_code']);
			}

			if($project['tenant_id']>0)
			{
				$tenant_data=$this->bocommon->read_single_tenant($project['tenant_id']);
				$project['location_data']['tenant_id']= $project['tenant_id'];
				$project['location_data']['contact_phone']= $tenant_data['contact_phone'];
				$project['location_data']['last_name']	= $tenant_data['last_name'];
				$project['location_data']['first_name']	= $tenant_data['first_name'];
			}
			else
			{
				unset($project['location_data']['tenant_id']);
				unset($project['location_data']['contact_phone']);
				unset($project['location_data']['last_name']);
				unset($project['location_data']['first_name']);
			}

//_debug_array($project);
			return $project;
		}


		function read_record_history($id)
		{
			$historylog	= CreateObject('property.historylog','project');
			$history_array = $historylog->return_array(array('O'),array(),'','',$id);
			$i=0;
			while (is_array($history_array) && list(,$value) = each($history_array))
			{

				$record_history[$i]['value_date']	= $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$record_history[$i]['value_user']	= $value['owner'];

				switch ($value['status'])
				{
					case 'R': $type = lang('Re-opened'); break;
					case 'RM': $type = lang('remark'); break;
					case 'X': $type = lang('Closed');    break;
					case 'O': $type = lang('Opened');    break;
					case 'A': $type = lang('Re-assigned'); break;
					case 'P': $type = lang('Priority changed'); break;
					case 'CO': $type = lang('Initial Coordinator'); break;
					case 'C': $type = lang('Coordinator changed'); break;
					case 'TO': $type = lang('Initial Category'); break;
					case 'T': $type = lang('Category changed'); break;
					case 'SO': $type = lang('Initial Status'); break;
					case 'S': $type = lang('Status changed'); break;
					case 'SC': $type = lang('Status confirmed'); break;
					default: break;
				}

				if($value['new_value']=='O'){$value['new_value']=lang('Opened');}
				if($value['new_value']=='X'){$value['new_value']=lang('Closed');}


				$record_history[$i]['value_action']	= $type?$type:'';
				unset($type);

				if ($value['status'] == 'A')
				{
					if (! $value['new_value'])
					{
						$record_history[$i]['value_new_value']	= lang('None');
					}
					else
					{
						$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
					}
				}
				else if ($value['status'] == 'C' || $value['status'] == 'CO')
				{
					$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
				}
				else if ($value['status'] == 'T' || $value['status'] == 'TO')
				{
					$category 								= $this->cats->return_single($value['new_value']);
					$record_history[$i]['value_new_value']	= $category[0]['name'];
				}
				else if ($value['status'] != 'O' && $value['new_value'])
				{
					$record_history[$i]['value_new_value']	= $value['new_value'];
				}
				else
				{
					$record_history[$i]['value_new_value']	= '';
				}

				$i++;
			}

			return $record_history;
		}


		function next_project_id()
		{
			return $this->so->next_project_id();
		}

		function save($project,$action='')
		{

//_debug_array($project);
			while (is_array($project['location']) && list(,$value) = each($project['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$project['location_code']=implode("-", $location);

			$start_date	= $this->bocommon->date_array($project['start_date']);
			$end_date	= $this->bocommon->date_array($project['end_date']);

			$project['start_date']	= mktime (2,0,0,$start_date['month'],$start_date['day'],$start_date['year']);
			$project['end_date']	= $end_date ? mktime (2,0,0,$end_date['month'],$end_date['day'],$end_date['year']) : '';


			if ($action=='edit')
			{
					$receipt = $this->so->edit($project);
			}
			else
			{
				$receipt = $this->so->add($project);
			}
			return $receipt;
		}

		function add_request($add_request,$id)
		{

			return $this->so->add_request($add_request,$id);
		}

		function delete($project_id)
		{
			$this->so->delete($project_id);
		}

	}

