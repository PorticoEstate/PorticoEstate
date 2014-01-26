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
	phpgw::import_class('phpgwapi.datetime');

	class property_boproject
	{
		var $start;
		var $query;
		var $filter;
		var $filter_year;
		var $sort;
		var $order;
		var $cat_id;
		var $allrows;
		var $project_type_id;

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
			$this->so 					= CreateObject('property.soproject');
			$this->bocommon 			= & $this->so->bocommon;
			$this->cats					= CreateObject('phpgwapi.categories', -1,  'property', '.project');
			$this->cats->supress_info	= true;
			$this->interlink 			= & $this->so->interlink;
			$this->custom 				= & $this->so->custom;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}



			$default_filter_year 	= isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_project_filter_year']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['default_project_filter_year'] == 'current_year' ? date('Y') : 'all';

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$filter					= phpgw::get_var('filter', 'int');
			$filter_year			= phpgw::get_var('filter_year', 'string', 'REQUEST', $default_filter_year);
			$cat_id					= phpgw::get_var('cat_id', 'int');
			$status_id				= phpgw::get_var('status_id');
			$user_id				= phpgw::get_var('user_id', 'int');
			$wo_hour_cat_id			= phpgw::get_var('wo_hour_cat_id', 'int');
			$district_id			= phpgw::get_var('district_id', 'int');
			$criteria_id			= phpgw::get_var('criteria_id', 'int');
			$project_type_id		= phpgw::get_var('project_type_id', 'int');

			$this->allrows			= phpgw::get_var('allrows', 'bool');

			$this->start			= $start ? $start : 0;
			$this->filter_year		= $filter_year;

			if(isset($_POST['query']) || isset($_GET['query']))
			{
				$this->query = $query;
			}
			if(isset($_POST['sort']) || isset($_GET['sort']))
			{
				$this->sort = $sort;
			}
			if(isset($_POST['order']) || isset($_GET['order']))
			{
				$this->order = $order;
			}
			if(isset($_POST['filter']) || isset($_GET['filter']))
			{
				$this->filter = $filter;
			}
			if(isset($_POST['cat_id']) || isset($_GET['cat_id']))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($_POST['status_id']) || isset($_GET['status_id']))
			{
				$this->status_id = $status_id;
			}
			if(isset($_POST['user_id']) || isset($_GET['user_id']))
			{
				$this->user_id = $user_id;
			}
			if(isset($_POST['wo_hour_cat_id']) || isset($_GET['wo_hour_cat_id']))
			{
				$this->wo_hour_cat_id = $wo_hour_cat_id;
			}
			if(isset($_POST['district_id']) || isset($_GET['district_id']))
			{
				$this->district_id = $district_id;
			}
			if(isset($_POST['criteria_id']) || isset($_GET['criteria_id']))
			{
				$this->criteria_id = $criteria_id;
			}
			if(isset($_POST['project_type_id']) || isset($_GET['project_type_id']))
			{
				$this->project_type_id = $project_type_id;
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
			$this->user_id			= isset($data['user_id'])?$data['user_id']:'';
			$this->wo_hour_cat_id	= isset($data['wo_hour_cat_id'])?$data['wo_hour_cat_id']:'';
			$this->district_id		= isset($data['district_id'])?$data['district_id']:'';
			$this->criteria_id		= isset($data['criteria_id'])?$data['criteria_id']:'';
			$this->project_type_id	= isset($data['project_type_id'])?$data['project_type_id']:'';
		}

		function column_list($selected = array())
		{
			if(!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns'] : '';
			}
			$filter = array('list' => ''); // translates to "list IS NULL"
			$columns = $this->custom->find('property','.project', 0, '','','',true, false, $filter);
			$columns = array_merge( $columns, $this->get_column_list() );
			return $this->bocommon->select_multi_list($selected,$columns);
		}

		function get_column_list()
		{
			$columns = array();
/*
			$columns['planned_cost'] = array
				(
					'id'		=> 'planned_cost',
					'name'		=> lang('planned cost'),
					'sortable'	=> false,
					'formatter'	=> 'myFormatCount2',
					'classname'	=> 'rightClasss'
				);
*/
			$columns['ecodimb'] = array
				(
					'id'		=> 'ecodimb',
					'name'		=> lang('accounting dim b'),
					'sortable'	=> true
				);
			$columns['entry_date'] = array
				(
					'id'		=> 'entry_date',
					'name'		=> lang('entry date'),
					'sortable'	=> true
				);
			$columns['start_date'] = array
				(
					'id'		=> 'start_date',
					'name'		=> lang('start date'),
					'sortable'	=> true
				);
			$columns['end_date'] = array
				(
					'id'		=> 'end_date',
					'name'		=> lang('end date'),
					'sortable'	=> true
				);
			$columns['billable_hours'] = array
				(
					'id'		=> 'billable_hours',
					'name'		=> lang('billable hours'),
					'sortable'	=> true
				);

			return $columns;
		}

		public function get_project_types($selected)
		{
			$values = array
			(
				array
				(
					'id'	=> 1,
					'name'	=> lang('operation')
				),
				array
				(
					'id'	=> 2,
					'name'	=> lang('investment')
				),
				array
				(
					'id'	=> 3,
					'name'	=> lang('buffer')
				),

			);
			return $this->bocommon->select_list($selected, $values);
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

		function get_criteria_list($selected='')
		{
			$criteria = array
				(
					array
					(
						'id'	=> '1',
						'name'	=> lang('project group')
					),
					array
					(
						'id'	=> '2',
						'name'	=> lang('project id')
					),
					array
					(
						'id'	=> '3',
						'name'	=> lang('address')
					),
					array
					(
						'id'	=> '4',
						'name'	=> lang('location code')
					),
					array
					(
						'id'	=> '5',
						'name'	=> lang('title')
					),
					array
					(
						'id'	=> '6',
						'name'	=> lang('module')
					),
					array
					(
						'id'	=> '7',
						'name'	=> lang('accounting dim b')
					),
					array
					(
						'id'	=> '8',
						'name'	=> lang('budget account group')
					)
				);

			return $this->bocommon->select_list($selected,$criteria);
		}


		function get_criteria($id='')
		{
			$criteria = array();
			$criteria[1] = array
				(
					'field'		=> 'project_group',
					'type'		=> 'int',
					'matchtype' => 'exact',
					'front' => '',
					'back' => ''
				);
			$criteria[2] = array
				(
					'field'		=> 'fm_project.id',
					'type'		=> 'int',
					'matchtype' => 'exact',
					'front' => '',
					'back' => ''
				);
			$criteria[3] = array
				(
					'field'	=> 'fm_project.address',
					'type'	=> 'varchar',
					'matchtype' => 'like',
					'front' => "'%",
					'back' => "%'",
				);
			$criteria[4] = array
				(
					'field'	=> 'fm_project.location_code',
					'type'	=> 'varchar',
					'matchtype' => 'like',
					'front' => "'",
					'back' => "%'"
				);
			$criteria[5] = array
				(
					'field'	=> 'fm_project.name',
					'type'	=> 'varchar',
					'matchtype' => 'like',
					'front' => "'%",
					'back' => "%'"
				);
			$criteria[6] = array
				(
					'field'	=> 'fm_project.p_num',
					'type'	=> 'varchar',
					'matchtype' => 'exact',
					'front' => "'",
					'back' => "'"
				);
			$criteria[7] = array
				(
					'field'	=> 'fm_project.ecodimb',
					'type'	=> 'int',
					'matchtype' => 'exact',
					'front' => '',
					'back' => ''
				);
			$criteria[8] = array
				(
					'field'	=> 'fm_project.account_group',
					'type'	=> 'int',
					'matchtype' => 'exact',
					'front' => '',
					'back' => ''
				);

			if($id)
			{
				return array($criteria[$id]);
			}
			else
			{
				return $criteria;
			}
		}

		function select_key_location_list($selected='')
		{

			$key_location_entries= $this->so->select_key_location_list();

			return $this->bocommon->select_list($selected,$key_location_entries);
		}

		function read($data = array())
		{
			if(isset($this->allrows) && $this->allrows)
			{
				$data['allrows'] = true;
			}

			$start_date	= $this->bocommon->date_to_timestamp($data['start_date']);
			$end_date	= $this->bocommon->date_to_timestamp($data['end_date']);

			$project = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'status_id' => $this->status_id,'wo_hour_cat_id' => $this->wo_hour_cat_id,
				'start_date'=>$start_date,'end_date'=>$end_date,'allrows'=>isset($data['allrows']) ? $data['allrows'] : '','dry_run' => $data['dry_run'],
				'district_id' => $this->district_id, 'criteria' => $this->get_criteria($this->criteria_id),
				'project_type_id'	=> $this->project_type_id, 'filter_year' => $this->filter_year));

			$this->total_records = $this->so->total_records;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->uicols	= $this->so->uicols;

			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['project_columns'] : array();
			$column_list = $this->get_column_list();

			foreach ($custom_cols as $col_id)
			{
				if(!ctype_digit($col_id))
				{
					$this->uicols['input_type'][]	= 'text';
					$this->uicols['name'][]			= $col_id;
					$this->uicols['descr'][]		= $column_list[$col_id]['name'];
					$this->uicols['statustext'][]	= $column_list[$col_id]['name'];
					$this->uicols['exchange'][]		= false;
					$this->uicols['align'][] 		= '';
					$this->uicols['datatype'][]		= false;
					$this->uicols['sortable'][]		= $column_list[$col_id]['sortable'];
					$this->uicols['formatter'][]	= $column_list[$col_id]['formatter'];
					$this->uicols['classname'][]	= $column_list[$col_id]['classname'];
				}
			}

			if(!isset($data['skip_origin']) || !$data['skip_origin'])
			{
				$this->uicols['input_type'][]	= 'text';
				$this->uicols['name'][]			= 'ticket';
				$this->uicols['descr'][]		= lang('ticket');
				$this->uicols['statustext'][]	= false;
				$this->uicols['exchange'][]		= false;
				$this->uicols['align'][] 		= '';
				$this->uicols['datatype'][]		= 'link';
				$this->uicols['sortable'][]		= '';
				$this->uicols['formatter'][]	= '';
				$this->uicols['classname'][]	= '';
			}

			$cols_extra		= $this->so->cols_extra;

			foreach ($project as & $entry)
			{
				$entry['entry_date'] = $GLOBALS['phpgw']->common->show_date($entry['entry_date'],$dateformat);
				$entry['start_date'] = $GLOBALS['phpgw']->common->show_date($entry['start_date'],$dateformat);
				$entry['end_date'] = $GLOBALS['phpgw']->common->show_date($entry['end_date'],$dateformat);
				if(!isset($data['skip_origin']) || !$data['skip_origin'])
				{
					$origin = $this->interlink->get_relation('property', '.project', $entry['project_id'], 'origin');
					if(isset($origin[0]['location']) && $origin[0]['location'] == '.ticket')
					{
						$entry['ticket'] = array
							(
								'url' 			=> $GLOBALS['phpgw']->link('/index.php', array
								(
									'menuaction'	=> 'property.uitts.view',
									'id'			=> $origin[0]['data'][0]['id']
								)
							),
							'text'			=> $origin[0]['data'][0]['id'],
							'statustext'	=> $origin[0]['data'][0]['statustext'],
						);
					}
				}
			}
			return $project;
		}

		function read_single($project_id = 0, $values = array(), $view = false)
		{
			$values['attributes'] = $this->custom->find('property', '.project', 0, '', 'ASC', 'attrib_sort', true, true);
			if($project_id)
			{
				$values = $this->so->read_single($project_id, $values);
			}

			$values = $this->custom->prepare($values, 'property', '.project', $view);

			if(!$project_id)
			{
				return $values;
			}

			$dateformat				= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$values['start_date']	= $GLOBALS['phpgw']->common->show_date($values['start_date'],$dateformat);
			$values['end_date']		= isset($values['end_date']) && $values['end_date'] ? $GLOBALS['phpgw']->common->show_date($values['end_date'],$dateformat) : '';

			if($values['location_code'])
			{
				$values['location_data'] = execMethod('property.solocation.read_single', $values['location_code']);
			}

			if($values['tenant_id']>0)
			{
				$tenant_data=$this->bocommon->read_single_tenant($values['tenant_id']);
				$values['location_data']['tenant_id']= $values['tenant_id'];
				$values['location_data']['contact_phone']= $tenant_data['contact_phone'];
				$values['location_data']['last_name']	= $tenant_data['last_name'];
				$values['location_data']['first_name']	= $tenant_data['first_name'];
			}
			else
			{
				unset($values['location_data']['tenant_id']);
				unset($values['location_data']['contact_phone']);
				unset($values['location_data']['last_name']);
				unset($values['location_data']['first_name']);
			}

			if($values['p_num'])
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');
				$category = $soadmin_entity->read_single_category($values['p_entity_id'],$values['p_cat_id']);

				$values['p'][$values['p_entity_id']]['p_num']=$values['p_num'];
				$values['p'][$values['p_entity_id']]['p_entity_id']=$values['p_entity_id'];
				$values['p'][$values['p_entity_id']]['p_cat_id']=$values['p_cat_id'];
				$values['p'][$values['p_entity_id']]['p_cat_name'] = $category['name'];
			}

			$values['origin'] = $this->interlink->get_relation('property', '.project', $project_id, 'origin');
			$values['target'] = $this->interlink->get_relation('property', '.project', $project_id, 'target');

			//_debug_array($values);
			return $values;
		}

		public function get_orders($data)
		{
			$contacts	= CreateObject('property.sogeneric');
			$contacts->get_location_info('vendor',false);

			static $vendor_name = array();
			$values	= $this->so->project_workorder_data($data);

			$sum_deviation = 0;
			foreach ($values as &$entry)
			{
				$sum_deviation+= $entry['deviation'];

				$entry['cost'] = $entry['combined_cost'];
		//		$entry['title']=htmlspecialchars_decode($entry['title']);

				if(isset($entry['vendor_id']) && $entry['vendor_id'])
				{
					if(isset($vendor_name[$entry['vendor_id']]) && $vendor_name[$entry['vendor_id']])
					{
						$entry['vendor_name'] = $vendor_name[$entry['vendor_id']];
					}
					else
					{
						$vendor['attributes'] = $this->custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

						$vendor	= $contacts->read_single(array('id' => $entry['vendor_id']), $vendor);
						foreach($vendor['attributes'] as $attribute)
						{
							if($attribute['name']=='org_name')
							{
								$entry['vendor_name'] = $attribute['value'];
								$vendor_name[$entry['vendor_id']] = $attribute['value'];
								break;
							}
						}
					}
				}
			}
			return $values;
		}

		function read_single_mini($project_id)
		{
			if($project	= $this->so->read_single($project_id))
			{
				$dateformat						= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				$project['start_date']			= $GLOBALS['phpgw']->common->show_date($project['start_date'],$dateformat);
				$project['end_date']			= isset($project['end_date']) && $project['end_date'] ? $GLOBALS['phpgw']->common->show_date($project['end_date'],$dateformat) : '';
			}

			if($project['location_code'])
			{
				$project['location_data'] = execMethod('property.solocation.read_single', $project['location_code']);
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
			foreach ($history_array as $value)
			{

				$record_history[$i]['value_date']	= $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$record_history[$i]['value_user']	= $value['owner'];

				switch ($value['status'])
				{
				case 'B':
					$type = lang('Budget');
					break;
				case 'BR':
					$type = lang('reserve');
					break;
				case 'R':
					$type = lang('Re-opened');
					break;
				case 'RM':
					$type = lang('remark');
					break;
				case 'X':
					$type = lang('Closed');
					break;
				case 'O':
					$type = lang('Opened');
					break;
				case 'A':
					$type = lang('Re-assigned');
					break;
				case 'P':
					$type = lang('Priority changed');
					break;
				case 'CO':
					$type = lang('Initial Coordinator');
					break;
				case 'C':
					$type = lang('Coordinator changed');
					break;
				case 'TO':
					$type = lang('Initial Category');
					break;
				case 'T':
					$type = lang('Category changed');
					break;
				case 'SO':
					$type = lang('Initial Status');
					break;
				case 'S':
					$type = lang('Status changed');
					break;
				case 'SC':
					$type = lang('Status confirmed');
					break;
				case 'AP':
					$type = lang('Ask for approval');
					break;
				case 'ON':
					$type = lang('Owner notified');
					break;
				case 'MS':
					$type = lang('Sent by sms');
					break;
				default:
					break;
				}

				if($value['new_value']=='O')
				{
					$value['new_value']=lang('Opened');
				}
				if($value['new_value']=='X')
				{
					$value['new_value']=lang('Closed');
				}

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
					if (! $value['old_value'])
					{
						$record_history[$i]['value_old_value']	= '';
					}
					else
					{
						$record_history[$i]['value_old_value']	= $GLOBALS['phpgw']->accounts->id2name($value['old_value']);
					}
				}
				else if ($value['status'] == 'C' || $value['status'] == 'CO')
				{
					$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
					if (! $value['old_value'])
					{
						$record_history[$i]['value_old_value']	= '';
					}
					else
					{
						$record_history[$i]['value_old_value']	= $GLOBALS['phpgw']->accounts->id2name($value['old_value']);
					}
				}
				else if ($value['status'] == 'T' || $value['status'] == 'TO')
				{
					$category 								= $this->cats->return_single($value['new_value']);
					$record_history[$i]['value_new_value']	= $category[0]['name'];
					if($value['old_value'])
					{
						$category 								= $this->cats->return_single($value['old_value']);
						$record_history[$i]['value_old_value']	= $category[0]['name'];
					}
				}
				else if ($value['status'] == 'B' || $value['status'] == 'BR')
				{
					$record_history[$i]['value_new_value']	=number_format($value['new_value'], 0, ',', ' ');
					$record_history[$i]['value_old_value']	=number_format($value['old_value'], 0, ',', ' ');
				}
				else if ($value['status'] != 'O' && $value['new_value'])
				{
					$record_history[$i]['value_new_value']	= $value['new_value'];
					$record_history[$i]['value_old_value']	= $value['old_value'];
				}
				else
				{
					$record_history[$i]['value_new_value']	= '';
				}

				$i++;
			}

			return $record_history;
		}

		public function get_files($id = 0)
		{
			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$files = $vfs->ls(array(
				'string' => "/property/project/{$id}",
				'relatives' => array(RELATIVE_NONE)
			));

			$vfs->override_acl = 0;

			$j	= count($files);
			for ($i=0;$i<$j;$i++)
			{
				$files[$i]['file_name']=urlencode($files[$i]['name']);
			}
			return $files;
		}


		function next_project_id()
		{
			return $this->so->next_project_id();
		}

		function save($project,$action='',$values_attribute = array())
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

			$project['start_date']	=  phpgwapi_datetime::date_to_timestamp($project['start_date']);
			$project['end_date']	=  phpgwapi_datetime::date_to_timestamp($project['end_date']);

			if(is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($action=='edit')
			{
				try
				{
					$receipt = $this->so->edit($project, $values_attribute);
				}
				catch(Exception $e)
				{
					if ( $e )
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$receipt['id'] = $project['id'];
					}
				}
			}
			else
			{
				$receipt = $this->so->add($project, $values_attribute);
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

		function bulk_update_status($start_date, $end_date, $status_filter, $status_new, $execute, $type, $user_id,$ids,$paid,$closed_orders,$ecodimb,$transfer_budget,$new_budget,$b_account_id)
		{
			return $this->so->bulk_update_status($start_date, $end_date, $status_filter, $status_new, $execute, $type, $user_id,$ids,$paid,$closed_orders,$ecodimb,$transfer_budget,$new_budget,$b_account_id);
		}

		public function get_user_list($selected = 0)
		{
			$ser_list = $this->so->get_user_list();
			foreach($ser_list as &$user)
			{
				$user['selected'] = $user['id'] == $selected ? true : false;
			}
			return $ser_list;
		}

		public function get_budget($project_id)
		{
			return $this->so->get_budget($project_id);
		}

		public function get_buffer_budget($project_id)
		{
			return $this->so->get_buffer_budget($project_id);
		}

		public function get_periodizations_with_outline()
		{
			return $this->so->get_periodizations_with_outline();
		}
		public function get_filter_year_list($selected)
		{
			$values = $this->so->get_filter_year_list();
			return $this->bocommon->select_list($selected, $values);
		}

		public function get_order_time_span($id)
		{
			$values = $this->so->get_order_time_span($id);
			return $this->bocommon->select_list(date('Y'), $values);
		}

		public function get_missing_project_budget()
		{
			return $this->so->get_missing_project_budget();
		}
		
	}
