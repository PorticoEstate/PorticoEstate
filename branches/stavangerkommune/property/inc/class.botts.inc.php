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
	* @subpackage helpdesk
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_botts
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $acl_location;
		var $uicols_related = array();
		var $start_date;
		var $end_date;
		var $fields_updated = false;
		var $status_id;
		var $user_id;
		var $part_of_town_id;
		var $district_id;
		public $total_records	= 0;
		public $sum_budget		= 0;
		public $sum_actual_cost	= 0;
		public $sum_difference	= 0;
		public $show_finnish_date = false;
		public $simple = false;

		var $public_functions = array
		(
			'read'			=> true,
			'read_single'	=> true,
			'save'			=> true,
			'addfiles'		=> true,
		);

		function __construct($session=false)
		{
			if($GLOBALS['phpgw_info']['flags']['currentapp'] != 'property')
			{
				$GLOBALS['phpgw']->translation->add_app('property');
			}

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->so 					= CreateObject('property.sotts');
			$this->custom 				= & $this->so->custom;
			$this->bocommon 			= CreateObject('property.bocommon');
			$this->historylog			= & $this->so->historylog;
			$this->config				= CreateObject('phpgwapi.config','property');
			$this->dateformat			= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$this->cats					= CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$this->cats->supress_info	= true;
			$this->acl_location			= $this->so->acl_location;

			$this->config->read();


			$user_groups =  $GLOBALS['phpgw']->accounts->membership($this->account);
			$simple_group = isset($this->config->config_data['fmttssimple_group']) ? $this->config->config_data['fmttssimple_group'] : array();
			foreach ( $user_groups as $group => $dummy)
			{
				if ( in_array($group, $simple_group))
				{
					$this->simple = true;
					break;
				}
			}

			reset($user_groups);
			$group_finnish_date = isset($this->config->config_data['fmtts_group_finnish_date']) ? $this->config->config_data['fmtts_group_finnish_date'] : array();
			foreach ( $user_groups as $group => $dummy)
			{
				if ( in_array($group, $group_finnish_date))
				{
					$this->show_finnish_date = true;
					break;
				}
			}

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$status_id				= phpgw::get_var('status_id', 'string');
			$user_id				= phpgw::get_var('user_id', 'int');
			$cat_id					= phpgw::get_var('cat_id', 'int');
			$part_of_town_id		= phpgw::get_var('part_of_town_id', 'int');
			$district_id			= phpgw::get_var('district_id', 'int');
			$allrows				= phpgw::get_var('allrows', 'bool');
			$start_date				= phpgw::get_var('start_date', 'string');
			$end_date				= phpgw::get_var('end_date', 'string');
			$location_code			= phpgw::get_var('location_code');
			$vendor_id				= phpgw::get_var('vendor_id', 'int');
			$ecodimb				= phpgw::get_var('ecodimb', 'int');
			$b_account				= phpgw::get_var('b_account', 'string');
			$building_part			= phpgw::get_var('building_part', 'string');
			$branch_id				= phpgw::get_var('branch_id', 'int');
			$order_dim1				= phpgw::get_var('order_dim1', 'int');

			$this->start			= $start 							? $start 			: 0;

			$this->query			= isset($_REQUEST['query']) 		? $query			: $this->query;
			$this->sort				= isset($_REQUEST['sort']) 			? $sort				: $this->sort;
			$this->order			= isset($_REQUEST['order']) 		? $order			: $this->order;
			$this->cat_id			= isset($_REQUEST['cat_id']) 		? $cat_id			:  $this->cat_id;
			$this->status_id		= isset($_REQUEST['status_id'])		? $status_id		: $this->status_id;
			$this->user_id			= isset($_REQUEST['user_id']) 		? $user_id			: $this->user_id;;
			$this->part_of_town_id	= isset($_REQUEST['part_of_town_id'])? $part_of_town_id : $this->part_of_town_id;
			$this->district_id		= isset($_REQUEST['district_id']) 	? $district_id		: $this->district_id;
			$this->allrows			= isset($allrows) && $allrows 		? $allrows			: '';
			$this->start_date		= isset($_REQUEST['start_date']) 	? $start_date		: $this->start_date;
			$this->end_date			= isset($_REQUEST['end_date'])		? $end_date			: $this->end_date;
			$this->location_code	= isset($location_code) && $location_code ? $location_code : '';
			$this->vendor_id		= isset($_REQUEST['vendor_id']) 	? $vendor_id		: $this->vendor_id;
			$this->p_num			= phpgw::get_var('p_num');
			$this->ecodimb			= isset($_REQUEST['ecodimb']) 		? $ecodimb			: $this->ecodimb;
			$this->b_account		= isset($_REQUEST['b_account']) 	? $b_account		: $this->b_account;
			$this->building_part	= isset($_REQUEST['building_part']) ? $building_part	: $this->building_part;
			$this->branch_id		= isset($_REQUEST['branch_id']) 	? $branch_id		: $this->branch_id;
			$this->order_dim1		= isset($_REQUEST['order_dim1']) 	? $order_dim1		: $this->order_dim1;
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','fm_tts',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','fm_tts');

			$this->start			= isset($data['start'])?$data['start']:'';
			$this->query			= isset($data['query'])?$data['query']:'';
			$this->user_id			= isset($data['user_id'])?$data['user_id']:'';
			$this->sort				= isset($data['sort'])?$data['sort']:'';
			$this->order			= isset($data['order'])?$data['order']:'';
			$this->status_id		= isset($data['status_id'])?$data['status_id']:'';
			$this->cat_id			= isset($data['cat_id'])?$data['cat_id']:'';
			$this->district_id		= isset($data['district_id'])?$data['district_id']:'';
			$this->part_of_town_id	= isset($data['part_of_town_id'])?$data['part_of_town_id']:'';
			$this->allrows			= isset($data['allrows'])?$data['allrows']:'';
			$this->start_date		= isset($data['start_date'])?$data['start_date']:'';
			$this->end_date			= isset($data['end_date'])?$data['end_date']:'';
			$this->vendor_id		= isset($data['vendor_id'])?$data['vendor_id']:'';
		}


		function column_list($selected = array())
		{
			if(!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns'] : '';
			}
			$_columns = $this->get_columns();

			$columns = array();
			foreach($_columns as $id => $column_info)
			{
				$columns[] = $column_info;
			}

			$column_list=$this->bocommon->select_multi_list($selected,$columns);
			return $column_list;
		}

		public function get_columns()
		{
			$columns = array();


			$columns['location_code'] = array
				(
					'id' => 'location_code',
					'name'=> lang('location code')
				);

			$columns['modified_date'] = array
				(
					'id'		=> 'modified_date',
					'name'		=> lang('modified date'),
//					'sortable'	=> true
				);

			$columns['status'] = array
				(
					'id' => 'status',
					'name'=> lang('status')
				);
			$columns['address'] = array
				(
					'id' => 'address',
					'name'=> lang('address')
				);
			$columns['user'] = array
				(
					'id' => 'user',
					'name'=> lang('user')
				);
			$columns['assignedto'] = array
				(
					'id' => 'assignedto',
					'name'=> lang('assignedto')
				);

			if( $GLOBALS['phpgw']->acl->check('.ticket.order', PHPGW_ACL_ADD, 'property') )
			{
				$columns['order_id'] = array
					(
						'id' => 'order_id',
						'name'=> lang('order id')
					);
				$columns['estimate'] = array
					(
						'id' => 'estimate',
						'name'=> lang('estimate')
					);
				$columns['actual_cost'] = array
					(
						'id' => 'actual_cost',
						'name'=> lang('actual cost')
					);

				$columns['difference'] = array
					(
						'id' => 'difference',
						'name'=> lang('difference')
					);
			}

			$columns['vendor'] = array
				(
					'id' => 'vendor',
					'name'=> lang('vendor')
				);
			$columns['billable_hours'] = array
				(
					'id' => 'billable_hours',
					'name'=> lang('billable hours')
				);
			$columns['district'] = array
				(
					'id' => 'district',
					'name'=> lang('district')
				);

			$this->get_origin_entity_type();

			foreach($this->uicols_related as $related)
			{
				$columns[$related] = array
				(
						'id' => $related,
						'name'=> ltrim(lang(str_replace('_', ' ', $related)),'!')
				);
			}

			if( $this->show_finnish_date )
			{
				$columns['finnish_date'] = array
					(
						'id' => 'finnish_date',
						'name'=> lang('finnish_date')
					);
				$columns['delay'] = array
					(
						'id' => 'delay',
						'name'=> lang('delay')
					);
			}


			$custom_cols = $this->custom->find('property', '.ticket', 0, '', 'ASC', 'attrib_sort', true, true);
			foreach ($custom_cols as $custom_col)
			{
				$columns[$custom_col['column_name']] = array
				(
					'id' => $custom_col['column_name'],
					'name'=> $custom_col['input_text'],
					'datatype' => $custom_col['datatype'],
				);
			}

			return $columns;
		}

		function get_group_list($selected = 0)
		{
			$query='';
			$group_list	= $this->bocommon->get_group_list('select', $selected, $start=-1, $sort='ASC', $order='account_firstname', $query, $offset=-1);
			$_candidates = array();
			if(isset($this->config->config_data['fmtts_assign_group_candidates']) && is_array($this->config->config_data['fmtts_assign_group_candidates']))
			{
				foreach($this->config->config_data['fmtts_assign_group_candidates'] as $group_candidate)
				{
					if( $group_candidate )
					{
						$_candidates[] = $group_candidate;
					}
				}
			}

			if( $_candidates )
			{
				if($selected)
				{
					if( !in_array( $selected, $_candidates) )
					{
						$_candidates[] = $selected;
					}
				}

				$values = array();
				foreach ($group_list as $group)
				{
					if( in_array( $group['id'], $_candidates) )
					{
						$values[] = $group;
					}
				}

				return $values;
			}
			else
			{
				return $group_list;

			}
		}

		function filter($data=0)
		{
			if(is_array($data))
			{
				$format = (isset($data['format'])?$data['format']:'');
				$selected = (isset($data['filter'])?$data['filter']:$data['default']);
			}

			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('filter_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('filter_filter'));
				break;
			}

			$_filters[0]['id']='all';
			$_filters[0]['name']=lang('All');

			$filters = $this->_get_status_list(true);

			$filters = array_merge($_filters,$filters);

			return $this->bocommon->select_list($selected,$filters);
		}

		function get_status_list($selected)
		{
			$status = $this->_get_status_list();
			return $this->bocommon->select_list($selected,$status);
		}

		function _get_status_list($leave_out_open = '')
		{
			$i = 0;
			$status[$i]['id']='X';
			$status[$i]['name']=lang('Closed');
			$i++;

			if(!$leave_out_open)
			{
				$status[$i]['id']='O';
				$status[$i]['name']= isset($this->config->config_data['tts_lang_open']) && $this->config->config_data['tts_lang_open'] ? $this->config->config_data['tts_lang_open'] : lang('Open');
				$i++;
			}

			$custom_status	= $this->so->get_custom_status();
			foreach($custom_status as $custom)
			{
				$status[$i] = array
					(
						'id'			=> "C{$custom['id']}",
						'name'			=> $custom['name']
					);
				$i++;
			}

			return $status;
		}

		function get_status_text()
		{
			$status_text = array(
				'R' => lang('Re-opened'),
				'X' => lang('Closed'),
				'O' => isset($this->config->config_data['tts_lang_open']) && $this->config->config_data['tts_lang_open'] ? $this->config->config_data['tts_lang_open'] : lang('Open'),
				'A' => lang('Re-assigned'),
				'G' => lang('Re-assigned group'),
				'P' => lang('Priority changed'),
				'T' => lang('Category changed'),
				'S' => lang('Subject changed'),
				'B' => lang('Billing rate'),
				'H' => lang('Billing hours'),
				'F' => lang('finnish date'),
				'SC' => lang('Status changed'),
				'M' => lang('Sent by email to'),
				'MS' => lang('Sent by sms'),
				'AC'=> lang('actual cost changed'),
			);

			$custom_status	= $this->so->get_custom_status();
			foreach($custom_status as $custom)
			{
				$status_text["C{$custom['id']}"] = $custom['name'];
			}

			return $status_text;
		}

		function get_priority_list($selected = 0)
		{
			if(!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['prioritydefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['prioritydefault'] : $prioritylevels;
			}
			return execMethod('property.bogeneric.get_list', array('type' => 'ticket_priority', 'selected' => $selected) );
		}

		function get_category_name($cat_id)
		{
			$category = $this->cats->return_single($cat_id);
			return $category[0]['name'];
		}


		function get_origin_entity_type()
		{
			$related = $this->so->get_origin_entity_type();
			$this->uicols_related = $this->so->uicols_related;
			return $related;
		}

		function read($start_date='',$end_date='', $external='',$dry_run = '', $download = '')
		{
			static $category_name = array();
			static $account = array();
			static $vendor_cache = array();

			$interlink 	= CreateObject('property.interlink');
			$start_date	= $this->bocommon->date_to_timestamp($start_date);
			$end_date	= $this->bocommon->date_to_timestamp($end_date);

			$tickets = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'status_id' => $this->status_id,'cat_id' => $this->cat_id,'district_id' => $this->district_id,
				'part_of_town_id' => $this->part_of_town_id, 'start_date'=>$start_date,'end_date'=>$end_date,
				'allrows'=>$this->allrows,'user_id' => $this->user_id,'external'=>$external, 'dry_run' => $dry_run,
				'location_code' => $this->location_code, 'p_num' => $this->p_num, 'vendor_id' => $this->vendor_id,
				'ecodimb' => $this->ecodimb, 'b_account' => $this->b_account, 'building_part' => $this->building_part,
				'branch_id' => $this->branch_id ,'order_dim1' => $this->order_dim1));

			$this->total_records		= $this->so->total_records;
			$this->sum_budget			= $this->so->sum_budget;
			$this->sum_actual_cost		= $this->so->sum_actual_cost;
			$this->sum_difference		= $this->so->sum_difference;

			if(!$external)
			{
				$entity	= $this->get_origin_entity_type();
				$contacts	= CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor',false);

				$custom 		= createObject('property.custom_fields');
				$vendor_data['attributes'] = $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);
			}
			else
			{
				$entity[0]['type']='.project';
				$this->uicols_related	= array('project');
			}


			$custom_status	= $this->so->get_custom_status();
			$closed_status = array('X');
			foreach($custom_status as $custom)
			{
				if($custom['closed'])
				{
					$closed_status[] =  "C{$custom['id']}";
				}
			}

			foreach ($tickets as & $ticket)
			{
				if(!isset($category_name[$ticket['cat_id']]))
				{
					$category_name[$ticket['cat_id']] = $this->get_category_name($ticket['cat_id']);
				}

				$ticket['category']	= $category_name[$ticket['cat_id']];

				if(!$ticket['subject'])
				{
					$ticket['subject'] = $category_name[$ticket['cat_id']];
				}

				if(!isset($account[$ticket['user_id']]))
				{
					$ticket['user'] = $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
					$account[$ticket['user_id']] = $ticket['user'];
				}
				else
				{
					$ticket['user'] = $account[$ticket['user_id']];
				}


				$ticket['difference'] = 0;


				if($ticket['estimate'] && !in_array( $ticket['status'], $closed_status) )
				{
					$ticket['difference'] =  $ticket['estimate'] - (float)$ticket['actual_cost'];
					if($ticket['difference'] < 0)
					{
						$ticket['difference'] = 0;
					}
				}

				if($ticket['assignedto'])
				{
					if(!isset($account[$ticket['assignedto']]))
					{
						$ticket['assignedto'] = $GLOBALS['phpgw']->accounts->id2name($ticket['assignedto']);
						$account[$ticket['assignedto']] = $ticket['assignedto'];
					}
					else
					{
						$ticket['assignedto'] = $account[$ticket['assignedto']];
					}
				}
				else
				{
					if(!isset($account[$ticket['group_id']]))
					{
						$ticket['assignedto'] = $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);
						$account[$ticket['group_id']] = $ticket['assignedto'];
					}
					else
					{
						$ticket['assignedto'] = $account[$ticket['group_id']];
					}
				}

				$ticket['entry_date'] = $GLOBALS['phpgw']->common->show_date($ticket['entry_date'],$this->dateformat);
				$ticket['modified_date'] = $GLOBALS['phpgw']->common->show_date($ticket['modified_date'],$this->dateformat);
				if($ticket['finnish_date2'])
				{
					$ticket['delay'] = round(($ticket['finnish_date2']-$ticket['finnish_date'])/(24*3600));
					$ticket['finnish_date']=$ticket['finnish_date2'];
				}
				$ticket['finnish_date'] = (isset($ticket['finnish_date']) && $ticket['finnish_date'] ? $GLOBALS['phpgw']->common->show_date($ticket['finnish_date'],$this->dateformat):'');

/*
				if ($ticket['status'] == 'X')
				{
					$history_values = $this->historylog->return_array(array(),array('X'),'history_timestamp','DESC',$ticket['id']);
					$ticket['timestampclosed'] = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);
				}
*/
				if ($ticket['new_ticket'])
				{
					$ticket['new_ticket'] = '*';
				}

				if(isset($entity) && is_array($entity))
				{
					for ($j=0;$j<count($entity);$j++)
					{
						$ticket['child_date'][$j] = $interlink->get_child_date('property', '.ticket', $entity[$j]['type'], $ticket['id'], isset($entity[$j]['entity_id'])?$entity[$j]['entity_id']:'',isset($entity[$j]['cat_id'])?$entity[$j]['cat_id']:'');
						if($ticket['child_date'][$j]['date_info'] && !$download)
						{
							$ticket['child_date'][$j]['statustext'] = $interlink->get_relation_info(array('location' => $entity[$j]['type']), $ticket['child_date'][$j]['date_info'][0]['target_id']);
						}
					}
				}
				if( $ticket['vendor_id'])
				{
					if(isset($vendor_cache[$ticket['vendor_id']]))
					{
						$ticket['vendor'] = $vendor_cache[$ticket['vendor_id']];
					}
					else
					{
						$vendor_data	= $contacts->read_single(array('id' => $ticket['vendor_id']),$vendor_data);
						if($vendor_data)
						{
							foreach($vendor_data['attributes'] as $attribute)
							{
								if($attribute['name']=='org_name')
								{
									$vendor_cache[$ticket['vendor_id']]=$attribute['value'];
									$ticket['vendor'] = $attribute['value'];
									break;
								}
							}
						}
					}
				}
			}
			return $tickets;
		}

		function read_single($id, $values = array(), $view = false)
		{
			$this->so->update_view($id);

			$values['attributes'] = $this->custom->find('property', '.ticket', 0, '', 'ASC', 'attrib_sort', true, true);
			$ticket = $this->so->read_single($id, $values);
			$ticket = $this->custom->prepare($ticket, 'property', '.ticket', $view);

			$ticket['user_lid'] = $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
			$ticket['group_lid'] = $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);

			$interlink 	= CreateObject('property.interlink');
			$ticket['origin'] = $interlink->get_relation('property', '.ticket', $id, 'origin');
			$ticket['target'] = $interlink->get_relation('property', '.ticket', $id, 'target');
			//_debug_array($ticket);
			if(isset($ticket['finnish_date2']) && $ticket['finnish_date2'])
			{
				$ticket['finnish_date']=$ticket['finnish_date2'];
			}

			if($ticket['finnish_date'])
			{
				$ticket['finnish_date'] = $GLOBALS['phpgw']->common->show_date($ticket['finnish_date'],$this->dateformat);
			}

			if($ticket['location_code'])
			{
				$solocation 	= CreateObject('property.solocation');
				$ticket['location_data'] = $solocation->read_single($ticket['location_code']);
			}
			//_debug_array($ticket['location_data']);
			if($ticket['p_num'])
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');
				$category = $soadmin_entity->read_single_category($ticket['p_entity_id'],$ticket['p_cat_id']);

				$ticket['p'][$ticket['p_entity_id']]['p_num']=$ticket['p_num'];
				$ticket['p'][$ticket['p_entity_id']]['p_entity_id']=$ticket['p_entity_id'];
				$ticket['p'][$ticket['p_entity_id']]['p_cat_id']=$ticket['p_cat_id'];
				$ticket['p'][$ticket['p_entity_id']]['p_cat_name'] = $category['name'];
			}


			if($ticket['tenant_id']>0)
			{
				$tenant_data=$this->bocommon->read_single_tenant($ticket['tenant_id']);
				$ticket['location_data']['tenant_id']= $ticket['tenant_id'];
				$ticket['location_data']['contact_phone']= $tenant_data['contact_phone'];
				$ticket['location_data']['last_name']	= $tenant_data['last_name'];
				$ticket['location_data']['first_name']	= $tenant_data['first_name'];
			}
			else
			{
				unset($ticket['location_data']['tenant_id']);
				unset($ticket['location_data']['contact_phone']);
				unset($ticket['location_data']['last_name']);
				unset($ticket['location_data']['first_name']);
			}

			// Figure out when it was opened

			$ticket['timestamp'] = $ticket['entry_date'];
			$ticket['entry_date'] = $GLOBALS['phpgw']->common->show_date($ticket['entry_date'],$this->dateformat);

			// Figure out when it was last closed
			$history_values = $this->historylog->return_array(array(),array('O'),'history_timestamp','ASC',$id);
			$ticket['last_opened'] = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime']);

			if($ticket['status']=='X')
			{

				$history_values = $this->historylog->return_array(array(),array('X'),'history_timestamp','DESC',$id);
				$ticket['timestampclosed']= $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);
			}

			$status_text = $this->get_status_text();

			$ticket['status_name'] = $status_text[$ticket['status']];
			$ticket['user_lid']=$GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
			$ticket['category_name']=ucfirst($this->get_category_name($ticket['cat_id']));

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$ticket['files'] = $vfs->ls (array(
				'string' => "/property/fmticket/{$id}",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$j	= count($ticket['files']);
			for ($i=0;$i<$j;$i++)
			{
				$ticket['files'][$i]['file_name']=urlencode($ticket['files'][$i]['name']);
			}

			if(!isset($ticket['files'][0]['file_id']) || !$ticket['files'][0]['file_id'])
			{
				unset($ticket['files']);
			}
			return $ticket;
		}

		function read_additional_notes($id)
		{
			$additional_notes = array();
			$history_array = $this->historylog->return_array(array(),array('C'),'','',$id);

			$i=2;
			foreach ($history_array as $value)
			{
				$additional_notes[] = array
					(
						'value_id'		=> $value['id'],
						'value_count'	=> $i,
						'value_date'	=> $GLOBALS['phpgw']->common->show_date($value['datetime']),
						'value_user'	=> $value['owner'],
						'value_note'	=> stripslashes($value['new_value']),
						'value_publish'	=> $value['publish'],
					);
				$i++;
			}
			return $additional_notes;
		}


		function read_record_history($id)
		{
			$history_array = $this->historylog->return_array(array('C','O'),array(),'','',$id);

			$status_text = $this->get_status_text();
			$record_history = array();
			$i=0;

			foreach ($history_array as $value)
			{
				$record_history[$i]['value_date']	= $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$record_history[$i]['value_user']	= $value['owner'];

				switch ($value['status'])
				{
				case 'R': $type = lang('Re-opened'); break;
				case 'X': $type = lang('Closed');    break;
				case 'O': $type = lang('Opened');    break;
				case 'A': $type = lang('Re-assigned'); break;
				case 'G': $type = lang('Re-assigned group'); break;
				case 'P': $type = lang('Priority changed'); break;
				case 'T': $type = lang('Category changed'); break;
				case 'S': $type = lang('Subject changed'); break;
				case 'H': $type = lang('Billable hours changed'); break;
				case 'B': $type = lang('Budget changed'); break;
//				case 'B': $type = lang('Billable rate changed'); break;
				case 'F': $type = lang('finnish date changed'); break;
				case 'IF': $type = lang('Initial finnish date'); break;
				case 'L': $type = lang('Location changed'); break;
				case 'AC': $type = lang('actual cost changed'); break;
				case 'M':
					$type = lang('Sent by email to');
					$this->order_sent_adress = $value['new_value']; // in case we want to resend the order as an reminder
					break;
				case 'MS':
					$type = lang('Sent by sms');
					break;
				default:
					// nothing
				}

		//		if ( $value['status'] == 'X' || $value['status'] == 'R' || (strlen($value['status']) == 2 && substr($value['new_value'], 0, 1) == 'C') ) // if custom status
				if ( $value['status'] == 'X' || $value['status'] == 'R' || preg_match('/^C/i', $value['status']) || ( $value['status'] == 'R' && preg_match('/^C/i', $value['new_value']))) // if custom status
				{
					switch ($value['status'])
					{
					case 'R': 
						$type = lang('Re-opened');
						break;
					case 'X':
						$type = lang('Closed');
						break;
					default:
						$type = lang('Status changed');
					}
					$value['new_value'] = $status_text[$value['new_value']];
					$value['old_value'] = $status_text[$value['old_value']];
				}

				$record_history[$i]['value_action']	= $type?$type:'';
				unset($type);
				if ($value['status'] == 'A' || $value['status'] == 'G')
				{
					if ((int)$value['new_value']>0)
					{
						$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
					}
					else
					{
						$record_history[$i]['value_new_value']	= lang('None');
					}

					if ((int)$value['old_value']>0)
					{
						$record_history[$i]['value_old_value'] = $value['old_value'] ? $GLOBALS['phpgw']->accounts->id2name($value['old_value']) : '';
					}
					else
					{
						$record_history[$i]['value_old_value']	= lang('None');
					}

				}
				else if ($value['status'] == 'T')
				{
					$record_history[$i]['value_new_value']	= $this->get_category_name($value['new_value']);
					$record_history[$i]['value_old_value']	= $this->get_category_name($value['old_value']);
				}
				else if (($value['status'] == 'F') || ($value['status'] =='IF'))
				{
					$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->common->show_date($value['new_value'],$this->dateformat);
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

		/**
		 * Simplified method for adding tickets from external apps
		 *	$data = array
		 *	(
		 *		'origin' 			=> $location_id,
		 *		'origin_id'			=> $location_item_id,
		 *		'location_code' 	=> $location_code,
		 * 		'cat_id'			=> $cat_id,
		 *		'priority'			=> $priority, //optional (1-3)
		 *		'title'				=> $title,
		 *		'details'			=> $details,
		 *		'file_input_name'	=> 'file' // default, optional
		 *	);
		 * 
		 */
		function add_ticket($data)
		{

			$boloc	= CreateObject('property.bolocation');
			$location_details = $boloc->read_single($data['location_code'], array('noattrib' => true));

			$location = array();
			$_location_arr = explode('-', $data['location_code']);
			$i = 1;
			foreach($_location_arr as $_loc)
			{
				$location["loc{$i}"] = $_loc;
				$i++;
			}

			$assignedto = execMethod('property.boresponsible.get_responsible', array('location' => $location, 'cat_id' => $data['cat_id']));

			if(!$assignedto)
			{
				$default_group = (int)$this->config->config_data['tts_default_group'];
			}
			else
			{
				$default_group = 0;
			}

			$priority_list = $this->get_priority_list();

			$default_priority = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['prioritydefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['prioritydefault'] : count($priority_list);

			$ticket = array
			(
				'origin_id'			=> isset($data['origin_id']) ? $data['origin_id'] : null,
				'origin_item_id'	=> isset($data['origin_item_id']) ? $data['origin_item_id'] : null,
				'cat_id'   			=> $data['cat_id'],
				'group_id'  		=> isset($data['group_id']) && $data['group_id'] ? $data['group_id']: $default_group,
				'assignedto'		=> $assignedto,
				'priority'			=> isset($data['priority']) && $data['priority'] ? $data['priority'] : $default_priority,
				'status'			=> 'O', // O = Open
				'subject'			=> $data['title'],
				'details'			=> $data['details'],
				'apply'				=> true,
				'contact_id'		=> 0,
				'location'			=> $location,
				'location_code'		=> $data['location_code'],
				'street_name'		=> $location_details['street_name'],
				'street_number'		=> $location_details['street_number'],
				'location_name'		=> $location_details['loc1_name'],
			);

			$result = $this->add($ticket);

			// Files
			$file_input_name = isset($data['file_input_name']) && $data['file_input_name'] ? $data['file_input_name'] : 'file';
			
			$file_name = @str_replace(' ','_',$_FILES[$file_input_name]['name']);
			if($file_name && $result['id'])
			{
				$bofiles = CreateObject('property.bofiles');
				$to_file = "{$bofiles->fakebase}/fmticket/{$result['id']}/{$file_name}";

				if($bofiles->vfs->file_exists(array(
					'string' => $to_file,
					'relatives' => array(RELATIVE_NONE)
				)))
				{
					$msglog['error'][] = array('msg'=>lang('This file already exists !'));
				}
				else
				{
					$bofiles->create_document_dir("fmticket/{$result['id']}");
					$bofiles->vfs->override_acl = 1;

					if(!$bofiles->vfs->cp(array (
					'from'	=> $_FILES[$file_input_name]['tmp_name'],
					'to'	=> $to_file,
					'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
					{
						$msglog['error'][] = array('msg' => lang('Failed to upload file!'));
					}
					$bofiles->vfs->override_acl = 0;
				}
			}
			return (int)$result['id'];	
		}

		function add($ticket, $values_attribute = array())
		{
			if((!isset($ticket['location_code']) || ! $ticket['location_code']) && isset($ticket['location']) && is_array($ticket['location']))
			{
				while (is_array($ticket['location']) && list(,$value) = each($ticket['location']))
				{
					if($value)
					{
						$location[] = $value;
					}
				}
				$ticket['location_code']=implode("-", $location);
			}

			$ticket['finnish_date']	= $this->bocommon->date_to_timestamp($ticket['finnish_date']);

			if($values_attribute && is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			$receipt = $this->so->add($ticket, $values_attribute);

			$this->config->read();

			if ( (isset($ticket['send_mail']) && $ticket['send_mail']) 
				|| (isset($this->config->config_data['mailnotification'])
					&& $this->config->config_data['mailnotification'])
			)
			{
				$receipt_mail = $this->mail_ticket($receipt['id'],false,$receipt,$ticket['location_code'], false, isset($ticket['send_mail']) && $ticket['send_mail'] ? true : false);
			}

			$criteria = array
				(
					'appname'	=> 'property',
					'location'	=> $this->acl_location,
					'allrows'	=> true
				);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ( $custom_functions as $entry )
			{
				// prevent path traversal
				if ( preg_match('/\.\./', $entry['file_name']) )
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ( $entry['active'] && is_file($file) )
				{
					require_once $file;
				}
			}

			if(isset($receipt_mail) && is_array($receipt_mail))
			{
				$receipt = array_merge($receipt, $receipt_mail);
			}
			return $receipt;
		}


		function get_address_element($location_code = '')
		{
			$address_element = array();
			if($location_code)
			{
				$solocation 		= CreateObject('property.solocation');
				$custom = createObject('property.custom_fields');
				$location_data 		= $solocation->read_single($location_code);

				$location_types = execMethod('property.soadmin_location.select_location_type');
				$type_id=count(explode('-',$location_code));

				for ($i=1; $i<$type_id+1; $i++)
				{
					$address_element[] = array
						(
							'text' => $location_types[($i-1)]['name'],
							'value'=> $location_data["loc{$i}"] . '  ' . $location_data["loc{$i}_name"]
						);
				}

				$fm_location_cols = $custom->find('property','.location.' . $type_id, 0, '', 'ASC', 'attrib_sort', true, true);
				$i=0;
				foreach($fm_location_cols as $location_entry)
				{
					if($location_entry['lookup_form'])
					{
						$address_element[] = array
							(
								'text' => $location_entry['input_text'],
								'value'=> $location_data[$location_entry['column_name']]
							);
					}
					$i++;
				}
			}
			return $address_element;
		}

		function mail_ticket($id, $fields_updated, $receipt = array(),$location_code='', $get_message = false, $force_send = false)
		{
			$log_recipients = array();
			$this->send			= CreateObject('phpgwapi.send');

			$ticket	= $this->so->read_single($id);

			$address_element = $this->get_address_element($ticket['location_code']);

			$history_values = $this->historylog->return_array(array(),array('O'),'history_timestamp','DESC',$id);
			$entry_date = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);

			if($ticket['status']=='X')
			{
				$history_values = $this->historylog->return_array(array(),array('X'),'history_timestamp','DESC',$id);
				$timestampclosed = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);
			}

			$history_2 = $this->historylog->return_array(array('C','O'),array(),'','',$id);
			$m=count($history_2)-1;
			$ticket['status']=$history_2[$m]['status'];

			$group_name= $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);

			// build subject
			$subject = '['.lang('Ticket').' #'.$id.'] : ' . $location_code .' ' .$this->get_category_name($ticket['cat_id']) . '; ' .$ticket['subject'];

			$prefs_user = $this->bocommon->create_preferences('property',$ticket['user_id']);

			$from_address=$prefs_user['email'];

			//-----------from--------

			$current_prefs_user = $this->bocommon->create_preferences('property',$GLOBALS['phpgw_info']['user']['account_id']);
			$current_user_address = "{$GLOBALS['phpgw_info']['user']['fullname']}<{$current_prefs_user['email']}>";

			//-----------from--------
			// build body
			$body  = '';
			$body .= '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view', 'id' => $id),false,true).'">' . lang('Ticket').' #' .$id .'</a>'."\n";
			$body .= lang('Date Opened').': '.$entry_date."\n";
			$body .= lang('Category').': '. $this->get_category_name($ticket['cat_id']) ."\n";
//			$body .= lang('Subject').': '. $ticket['subject'] ."\n";
			$body .= lang('Location').': '. $ticket['location_code'] ."\n";
			$body .= lang('Address').': '. $ticket['address'] ."\n";
			if (isset($address_element) AND is_array($address_element))
			{
				foreach($address_element as $address_entry)
				{
					$body .= $address_entry['text'].': '. $address_entry['value'] ."\n";
				}
			}

			if($ticket['tenant_id'])
			{
				$tenant_data=$this->bocommon->read_single_tenant($ticket['tenant_id']);
				$body .= lang('Tenant').': '. $tenant_data['first_name'] . ' ' .$tenant_data['last_name'] ."\n";

				if($tenant_data['contact_phone'])
				{
					$body .= lang('Contact phone').': '. $tenant_data['contact_phone'] ."\n";

				}
			}
			$body .= lang('Assigned To').': '.$GLOBALS['phpgw']->accounts->id2name($ticket['assignedto'])."\n";
			$body .= lang('Priority').': '.$ticket['priority']."\n";
			if($group_name)
			{
				$body .= lang('Group').': '. $group_name ."\n";
			}

			/**************************************************************\
			 * Display additional notes                                     *
			 \**************************************************************/
//			if($fields_updated)
			{
				$i=1;

				$history_array = $this->historylog->return_array(array(),array('C'),'history_id','DESC',$id);

				foreach($history_array as $value)
				{
					$body .= lang('Date') . ': '.$GLOBALS['phpgw']->common->show_date($value['datetime'])."\n";
					$body .= lang('User') . ': '.$value['owner']."\n";
					$body .=lang('Note').': '. nl2br(stripslashes($value['new_value']))."\n\n";
					$i++;
				}
				$subject .= "::{$i}";
			}
			/**************************************************************\
			 * Display record history                                       *
			 \**************************************************************/

			if($timestampclosed)
			{
				$body .= lang('Date Closed').': '.$timestampclosed."\n\n";
			}


			$body .= lang('Opened By').': '. $ticket['user_name'] ."\n\n";
			$body .= lang('First Note Added').":\n";
			$body .= stripslashes(strip_tags($ticket['details']))."\n\n";

			if($get_message)
			{
				return array('subject' => $subject, 'body' => $body);
			}

			$members = array();

			if( isset($this->config->config_data['groupnotification']) && $this->config->config_data['groupnotification'] == 2)
			{
				// Never send to groups
			}
			else if( (isset($this->config->config_data['groupnotification']) && $this->config->config_data['groupnotification'] == 1 && $ticket['group_id'] )
					|| ($force_send && $ticket['group_id']))
			{
				$log_recipients[] = $group_name;
				$members_gross = $GLOBALS['phpgw']->accounts->member($ticket['group_id'], true);
				foreach($members_gross as $user)
				{
					$members[$user['account_id']] = $user['account_name'];
				}
				unset($members_gross);
			}

			$GLOBALS['phpgw']->preferences->set_account_id($ticket['user_id'], true);
			if( (isset($GLOBALS['phpgw']->preferences->data['property']['tts_notify_me'])
					&& ($GLOBALS['phpgw']->preferences->data['property']['tts_notify_me'] == 1)
				)
				|| ($this->config->config_data['ownernotification'] && $ticket['user_id']))
			{
				// add owner to recipients
				$members[$ticket['user_id']] = $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
				$log_recipients[] = $GLOBALS['phpgw']->accounts->get($ticket['user_id'])->__toString();
			}

			if($ticket['assignedto'])
			{
				$GLOBALS['phpgw']->preferences->set_account_id($ticket['assignedto'], true);
				if( (isset($GLOBALS['phpgw']->preferences->data['property']['tts_notify_me'])
						&& ($GLOBALS['phpgw']->preferences->data['property']['tts_notify_me'] == 1)
					)
					|| ($this->config->config_data['assignednotification'] && $ticket['assignedto'])
					|| ($force_send && $ticket['assignedto'])
				)
				{
					// add assigned to recipients
					$members[$ticket['assignedto']] = $GLOBALS['phpgw']->accounts->id2name($ticket['assignedto']);
					$log_recipients[] = $GLOBALS['phpgw']->accounts->get($ticket['assignedto'])->__toString();
				}
			}

			$error = array();
			$toarray = array();

			$validator = CreateObject('phpgwapi.EmailAddressValidator');

			foreach($members as $account_id => $account_name)
			{
				$prefs = $this->bocommon->create_preferences('property',$account_id);
				if(!isset($prefs['tts_notify_me'])	|| $prefs['tts_notify_me'] == 1)
				{
					if ($validator->check_email_address($prefs['email']))
					{
						// Email address is technically valid
						// avoid problems with the delimiter in the send class
						if(strpos($account_name,','))
						{
							$_account_name = explode(',', $account_name);
							$account_name = ltrim($_account_name[1]) . ' ' . $_account_name[0];
						}

						$toarray[] = "{$account_name}<{$prefs['email']}>";
					}
					else
					{
//						$receipt['error'][] = array('msg'=> lang('Your message could not be sent!'));
						$receipt['error'][] = array('msg'=>lang('This user has not defined an email address !') . ' : ' . $account_name);
					}
				}
			}


			$notify_list = execMethod('property.notify.read', array
				(
					'location_id'		=> $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location),
					'location_item_id'	=> $id
				)
			);

			if(isset($GLOBALS['phpgw_info']['user']['apps']['sms']))
			{

				$sms_text = "{$subject}. \r\n{$GLOBALS['phpgw_info']['user']['fullname']} \r\n{$GLOBALS['phpgw_info']['user']['preferences']['property']['email']}";
				$sms	= CreateObject('sms.sms');

				foreach($notify_list as $entry)
				{
					if($entry['is_active'] && $entry['notification_method'] == 'sms' && $entry['sms'])
					{
						$sms->websend2pv($this->account,$entry['sms'],$sms_text);
						$toarray_sms[] = "{$entry['first_name']} {$entry['last_name']}({$entry['sms']})";
						$receipt['message'][]=array('msg'=>lang('%1 is notified',"{$entry['first_name']} {$entry['last_name']}"));
					}
				}
				unset($entry);
				if($toarray_sms)
				{
					$this->historylog->add('MS',$id,"{$subject}::" . implode(',',$toarray_sms));						
				}
			}

			reset($notify_list);
			foreach($notify_list as $entry)
			{
				if($entry['is_active'] && $entry['notification_method'] == 'email' && $entry['email'])
				{
					$toarray[] = "{$entry['first_name']} {$entry['last_name']}<{$entry['email']}>";
				}

				$log_recipients[] = "{$entry['first_name']} {$entry['last_name']}";
			}
			unset($entry);

			$rc = false;
			if($toarray)
			{
				$to = implode(';',$toarray);
				$body = nl2br($body);

				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					try
					{
						$rc = $this->send->msg('email', $to, $subject, stripslashes($body), '', $cc, $bcc,$current_user_address,$GLOBALS['phpgw_info']['user']['fullname'],'html');
					}
					catch (phpmailerException $e)
					{
						$receipt['error'][] = array('msg' => $e->getMessage());
					}
				}
				else
				{
					$receipt['error'][] = array('msg'=>lang('SMTP server is not set! (admin section)'));
				}
			}

			if($rc && $log_recipients)
			{
				$this->historylog->add('M',$id,implode(';',array_unique($log_recipients)));				
			}
/*
			if (!$rc && ($this->config->config_data['groupnotification'] || $this->config->config_data['ownernotification'] || $this->config->config_data['groupnotification']))
			{
				$receipt['error'][] = array('msg'=> lang('Your message could not be sent by mail!'));
				$receipt['error'][] = array('msg'=> lang('The mail server returned'));
				$receipt['error'][] = array('msg'=> "From : {$current_user_address}");
				$receipt['error'][] = array('msg'=> 'to: '.$to);
				$receipt['error'][] = array('msg'=> 'subject: '.$subject);
				$receipt['error'][] = array('msg'=> $body );
	//			$receipt['error'][] = array('msg'=> 'cc: ' . $cc);
	//			$receipt['error'][] = array('msg'=> 'bcc: '.$bcc);
				$receipt['error'][] = array('msg'=> 'group: '.$group_name);
				$receipt['error'][] = array('msg'=> 'err_code: '.$this->send->err['code']);
				$receipt['error'][] = array('msg'=> 'err_msg: '. htmlspecialchars($this->send->err['msg']));
				$receipt['error'][] = array('msg'=> 'err_desc: '. $this->send->err['desc']);
			}
*/
			//_debug_array($receipt);
			return $receipt;
		}

		function delete($id)
		{
			return $this->so->delete($id);
		}

		/**
		 * Get a list of user(admin)-configured status
		 *
		 * @return array with list of custom status
		 */

		public function get_custom_status()
		{
			return $this->so->get_custom_status();
		}

		public function update_status($data, $id = 0)
		{
			$receipt = array();
			if ($this->so->update_status($data, $id))
			{
				$receipt['message'][]= array('msg' => lang('Ticket %1 has been updated',$id));
			}
			$this->fields_updated = $this->so->fields_updated;
			return $receipt;
		}

		public function update_priority($data, $id = 0)
		{
			$receipt 	= $this->so->update_priority($data, $id);
			$this->fields_updated = $this->so->fields_updated;
			return $receipt;
		}

		public function update_ticket($data, $id,$receipt = array(),$values_attribute = array())
		{
			if($values_attribute && is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			$receipt = $this->so->update_ticket($data, $id, $receipt, $values_attribute);
			$this->fields_updated = $this->so->fields_updated;

			$criteria = array
			(
				'appname'	=> 'property',
				'location'	=> $this->acl_location,
				'allrows'	=> true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ( $custom_functions as $entry )
			{
				// prevent path traversal
				if ( preg_match('/\.\./', $entry['file_name']) )
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ( $entry['active'] && is_file($file) )
				{
					require $file;
				}
			}

			return $receipt;
		}

		public function get_vendors($selected)
		{
			$vendors = $this->so->get_vendors();
			foreach ($vendors as &$vendor)
			{
				if($vendor['id'] == $selected)
				{
					$vendor['selected'] = 1;
					break;
				}
			}

			$default_value = array
			(
				'id'	=> '',
				'name'	=> lang('vendor')
			);
			array_unshift($vendors,$default_value);
			return $vendors;
		}
		public function get_ecodimb($selected)
		{
			$values = $this->so->get_ecodimb();
			foreach ($values as &$value)
			{
				if($value['id'] == $selected)
				{
					$value['selected'] = 1;
					break;
				}
			}

			$default_value = array
			(
				'id'	=> '',
				'name'	=> lang('dimb')
			);
			array_unshift($values,$default_value);
			return $values;
		}
		public function get_b_account($selected)
		{
			$values = $this->so->get_b_account();
			foreach ($values as &$value)
			{
				if($value['id'] == $selected)
				{
					$value['selected'] = 1;
					break;
				}
			}

			$default_value = array
			(
				'id'	=> '',
				'name'	=> lang('budget account')
			);
			array_unshift($values,$default_value);
			return $values;
		}
		public function get_building_part($selected)
		{
			$values = $this->so->get_building_part();
			foreach ($values as &$value)
			{
				if($value['id'] == $selected)
				{
					$value['selected'] = 1;
					break;
				}
			}

			$default_value = array
			(
				'id'	=> '',
				'name'	=> lang('building part')
			);
			array_unshift($values,$default_value);
			return $values;
		}
		public function get_branch($selected)
		{
			$values = $this->so->get_branch();
			foreach ($values as &$value)
			{
				if($value['id'] == $selected)
				{
					$value['selected'] = 1;
					break;
				}
			}

			$default_value = array
			(
				'id'	=> '',
				'name'	=> lang('branch')
			);
			array_unshift($values,$default_value);
			return $values;
		}
		public function get_order_dim1($selected)
		{
			$values = $this->so->get_order_dim1();
			foreach ($values as &$value)
			{
				if($value['id'] == $selected)
				{
					$value['selected'] = 1;
					break;
				}
			}

			$default_value = array
			(
				'id'	=> '',
				'name'	=> lang('order_dim1')
			);
			array_unshift($values,$default_value);
			return $values;
		}

		public function addfiles()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$acl 			= & $GLOBALS['phpgw']->acl;
			$acl_add 		= $acl->check('.ticket', PHPGW_ACL_ADD, 'property');
			$acl_edit 		= $acl->check('.ticket', PHPGW_ACL_EDIT, 'property');
			$id				= phpgw::get_var('id', 'int');
			$check			= phpgw::get_var('check', 'bool');
			$fileuploader	= CreateObject('property.fileuploader');

			if(!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if(!$id)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$test = false;
			if ($test)
			{
				if (!empty($_FILES))
				{
					$tempFile = $_FILES['Filedata']['tmp_name'];
					$targetPath = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/";
					$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
					move_uploaded_file($tempFile,$targetFile);
					echo str_replace($GLOBALS['phpgw_info']['server']['temp_dir'],'',$targetFile);
				}
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
	
			if($check)
			{
				$fileuploader->check("fmticket/{$id}");
			}
			else
			{
				$fileuploader->upload("fmticket/{$id}");
			}
		}

		public function get_attributes($values)
		{
			$values['attributes'] = $this->custom->find('property', '.ticket', 0, '', 'ASC', 'attrib_sort', true, true);
			$values = $this->custom->prepare($values, 'property', '.ticket', false);
			return $values;
		}
	}
