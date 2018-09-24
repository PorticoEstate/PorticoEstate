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
		var $reported_by;
		var $part_of_town_id;
		var $district_id;
		public $total_records = 0;
		public $sum_budget = 0;
		public $sum_actual_cost = 0;
		public $sum_difference = 0;
		public $show_finnish_date = false;
		public $simple = false;
		public $group_candidates = array(-1);
		protected $custom_filters = array();
		var $public_functions = array
			(
			'read' => true,
			'read_single' => true,
			'save' => true,
			'addfiles' => true,
		);

		function __construct( )
		{
			if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'property')
			{
				$GLOBALS['phpgw']->translation->add_app('property');
			}

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->so = CreateObject('property.sotts');
			$this->custom = & $this->so->custom;
			$this->bocommon = CreateObject('property.bocommon');
			$this->historylog = & $this->so->historylog;
			$this->config = CreateObject('phpgwapi.config', 'property');
			$this->dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$this->cats = CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$this->cats->supress_info = true;
			$this->acl_location = $this->so->acl_location;

			$this->config->read();


			$default_interface = isset($this->config->config_data['tts_default_interface']) ? $this->config->config_data['tts_default_interface'] : '';

			/*
			 * Inverted logic
			 */
			if($default_interface == 'simplified')
			{
				$this->simple = true;
			}

			$user_groups =  $GLOBALS['phpgw']->accounts->membership($this->account);
			$simple_group = isset($this->config->config_data['fmttssimple_group']) ? $this->config->config_data['fmttssimple_group'] : array();

			foreach ($user_groups as $group => $dummy)
			{
				if (in_array($group, $simple_group))
				{
					if($default_interface == 'simplified')
					{
						$this->simple = false;
					}
					else
					{
						$this->simple = true;
					}
					break;
				}
			}
			if (isset($this->config->config_data['fmtts_assign_group_candidates']) && is_array($this->config->config_data['fmtts_assign_group_candidates']))
			{
				foreach ($this->config->config_data['fmtts_assign_group_candidates'] as $group_candidate)
				{
					if ($group_candidate)
					{
						$this->group_candidates[] = $group_candidate;
					}
				}
			}
			reset($user_groups);

			foreach ( $user_groups as $group => $dummy)
			{
				if ( in_array($group, $this->group_candidates))
				{
					$this->simple = false;
					break;
				}
			}

			reset($user_groups);
			$group_finnish_date = isset($this->config->config_data['fmtts_group_finnish_date']) ? $this->config->config_data['fmtts_group_finnish_date'] : array();
			foreach ($user_groups as $group => $dummy)
			{
				if (in_array($group, $group_finnish_date))
				{
					$this->show_finnish_date = true;
					break;
				}
			}

			$this->start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$this->query = phpgw::get_var('query');
			$this->sort = phpgw::get_var('sort');
			$this->order = phpgw::get_var('order');
			$this->status_id = phpgw::get_var('status_id', 'string');
			$this->user_id = phpgw::get_var('user_id', 'int');
			$this->reported_by = phpgw::get_var('reported_by', 'int');
			$this->cat_id = phpgw::get_var('cat_id', 'int');
			$this->part_of_town_id = phpgw::get_var('part_of_town_id', 'int');
			$default_district = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'] : '');
			$district_id = phpgw::get_var('district_id', 'int');
			$this->district_id = isset($_REQUEST['district_id']) ? $district_id : $default_district;
			$this->allrows = phpgw::get_var('allrows', 'bool');
			$this->start_date = phpgw::get_var('filter_start_date', 'string');
			$this->end_date = phpgw::get_var('filter_end_date', 'string');
			$this->location_code = phpgw::get_var('location_code');
			$this->vendor_id = phpgw::get_var('vendor_id', 'int');
			$this->ecodimb = phpgw::get_var('ecodimb', 'int');
			$this->b_account = phpgw::get_var('b_account', 'string');
			$this->building_part = phpgw::get_var('building_part', 'string');
			$this->branch_id = phpgw::get_var('branch_id', 'int');
			$this->order_dim1 = phpgw::get_var('order_dim1', 'int');
			$this->p_num = phpgw::get_var('p_num');
		}

		function column_list( $selected = array() )
		{
			if (!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns'] : '';
			}
			$_columns = $this->get_columns();

			$columns = array();
			foreach ($_columns as $id => $column_info)
			{
				$columns[] = $column_info;
			}

			$column_list = $this->bocommon->select_multi_list($selected, $columns);
			return $column_list;
		}

		public function get_columns()
		{
			$columns = array();


			$columns['location_code'] = array
				(
				'id' => 'location_code',
				'name' => lang('location code')
			);

			$columns['modified_date'] = array
				(
				'id' => 'modified_date',
				'name' => lang('modified date'),
//					'sortable'	=> true
			);

			$columns['status'] = array
				(
				'id' => 'status',
				'name' => lang('status')
			);
			$columns['address'] = array
				(
				'id' => 'address',
				'name' => lang('address')
			);
			$columns['user'] = array
				(
				'id' => 'user',
				'name' => lang('user')
			);
			$columns['assignedto'] = array
				(
				'id' => 'assignedto',
				'name' => lang('assigned to')
			);

			if ($GLOBALS['phpgw']->acl->check('.ticket.order', PHPGW_ACL_ADD, 'property'))
			{
				$columns['order_id'] = array
					(
					'id' => 'order_id',
					'name' => lang('order id')
				);
				$columns['estimate'] = array
					(
					'id' => 'estimate',
					'name' => lang('estimate')
				);
				$columns['actual_cost'] = array
					(
					'id' => 'actual_cost',
					'name' => lang('actual cost')
				);

				$columns['difference'] = array
					(
					'id' => 'difference',
					'name' => lang('difference')
				);

				$columns['order_dim1'] = array
					(
					'id' => 'order_dim1',
					'name' => lang('order_dim1')
				);
				$columns['external_project_id'] = array
					(
					'id' => 'external_project_id',
					'name' => lang('external project')
				);
				$columns['contract_id'] = array
					(
					'id' => 'contract_id',
					'name' => lang('contract')
				);
				$columns['service_id'] = array
					(
					'id' => 'service_id',
					'name' => lang('service')
				);
				$columns['tax_code'] = array
					(
					'id' => 'tax_code',
					'name' => lang('tax code')
				);
				$columns['unspsc_code'] = array
					(
					'id' => 'unspsc_code',
					'name' => lang('unspsc code')
				);
				$columns['b_account_id'] = array
					(
					'id' => 'b_account_id',
					'name' => lang('budget account')
				);

				$columns['continuous'] = array
					(
					'id' => 'continuous',
					'name' => lang('continuous')
				);

			}

			$columns['ecodimb'] = array
				(
				'id' => 'ecodimb',
				'name' => lang('dim b')
			);
			$columns['vendor'] = array
				(
				'id' => 'vendor',
				'name' => lang('vendor')
			);
			$columns['billable_hours'] = array
				(
				'id' => 'billable_hours',
				'name' => lang('billable hours')
			);
			$columns['district'] = array
				(
				'id' => 'district',
				'name' => lang('district')
			);

			$this->get_origin_entity_type();

			foreach ($this->uicols_related as $related)
			{
				$columns[$related] = array
					(
					'id' => $related,
					'name' => ltrim(lang(str_replace('_', ' ', $related)), '!')
				);
			}

			if ($this->show_finnish_date)
			{
				$columns['finnish_date'] = array
					(
					'id' => 'finnish_date',
					'name' => lang('finnish_date')
				);
				$columns['delay'] = array
					(
					'id' => 'delay',
					'name' => lang('delay')
				);
			}

			$columns['details'] = array
				(
				'id' => 'details',
				'name' => lang('details')
			);


			$custom_cols = $this->get_custom_cols();

			foreach ($custom_cols as $custom_col)
			{
				$columns[$custom_col['column_name']] = array
					(
					'id' => $custom_col['column_name'],
					'name' => $custom_col['input_text'],
					'datatype' => $custom_col['datatype'],
				);
				if (($custom_col['datatype'] == 'LB' || $custom_col['datatype'] == 'CH' || $custom_col['datatype'] == 'R') && $custom_col['choice'])
				{
					$this->custom_filters[] = $custom_col['column_name'];
				}
			}
			return $columns;
		}

		function get_custom_cols()
		{
			static $custom_cols = array();

			if ($custom_cols)
			{
				return $custom_cols;
			}
			$custom_cols = $this->custom->find('property', '.ticket', 0, '', 'ASC', 'attrib_sort', true, true);
			return $custom_cols;
		}

		function get_custom_filters()
		{
			static $custom_filters = array();

			if ($custom_filters)
			{
				return $custom_filters;
			}

			$custom_cols = $this->get_custom_cols();
			foreach ($custom_cols as $custom_col)
			{
				if (($custom_col['datatype'] == 'LB' || $custom_col['datatype'] == 'CH' || $custom_col['datatype'] == 'R') && $custom_col['choice'])
				{
					$custom_filters[] = $custom_col['column_name'];
				}
			}
			return $custom_filters;
		}

		function get_group_list( $selected = 0 )
		{
			$query = '';
			$group_list = $this->bocommon->get_group_list('select', $selected, $start = -1, $sort = 'ASC', $order = 'account_firstname', $query, $offset = -1);
			$_candidates = array();
			if (isset($this->config->config_data['fmtts_assign_group_candidates']) && is_array($this->config->config_data['fmtts_assign_group_candidates']))
			{
				foreach ($this->config->config_data['fmtts_assign_group_candidates'] as $group_candidate)
				{
					if ($group_candidate)
					{
						$_candidates[] = $group_candidate;
					}
				}
			}

			if ($_candidates)
			{
				if ($selected)
				{
					if (!in_array($selected, $_candidates))
					{
						$_candidates[] = $selected;
					}
				}

				$values = array();
				foreach ($group_list as $group)
				{
					if (in_array($group['id'], $_candidates))
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

		function filter( $data = 0 )
		{
			if (is_array($data))
			{
				$format = (isset($data['format']) ? $data['format'] : '');
				$selected = (isset($data['filter']) ? $data['filter'] : $data['default']);
			}

			switch ($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('filter_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('filter_filter'));
					break;
			}

			$_filters[0]['id'] = 'all';
			$_filters[0]['name'] = lang('All');

			$filters = $this->_get_status_list(true);

			$filters = array_merge($_filters, $filters);

			return $this->bocommon->select_list($selected, $filters);
		}

		function get_status_list( $selected )
		{
			$status = $this->_get_status_list();
			return $this->bocommon->select_list($selected, $status);
		}

		function _get_status_list( $leave_out_open = '' )
		{
			$i = 0;
			$status[$i]['id'] = 'X';
			$status[$i]['name'] = lang('Closed');
			$i++;

			if (!$leave_out_open)
			{
				$status[$i]['id'] = 'O';
				$status[$i]['name'] = isset($this->config->config_data['tts_lang_open']) && $this->config->config_data['tts_lang_open'] ? $this->config->config_data['tts_lang_open'] : lang('Open');
				$i++;
			}

			$custom_status = $this->so->get_custom_status();
			foreach ($custom_status as $custom)
			{
				$status[$i] = array
					(
					'id' => "C{$custom['id']}",
					'name' => $custom['name']
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
				'O' => !empty($this->config->config_data['tts_lang_open']) ? $this->config->config_data['tts_lang_open'] : lang('Open'),
				'A' => lang('Re-assigned'),
				'G' => lang('Re-assigned group'),
				'P' => lang('Priority changed'),
				'T' => lang('Category changed'),
				'S' => lang('Subject changed'),
				'B' => lang('Budget changed'),
				'H' => lang('Billing hours'),
				'F' => lang('finnish date'),
				'SC' => lang('Status changed'),
				'M' => lang('Sent by email to'),
				'MS' => lang('Sent by sms'),
				'AC' => lang('actual cost changed'),
				'AR' => lang('Request for approval'),
				'AA' => lang('approved'),
			);

			$custom_status = $this->so->get_custom_status();
			foreach ($custom_status as $custom)
			{
				$status_text["C{$custom['id']}"] = $custom['name'];
			}

			return $status_text;
		}

		function get_priority_list( $selected = 0 )
		{
			if (!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['prioritydefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['prioritydefault'] : $prioritylevels;
			}
			return execMethod('property.bogeneric.get_list', array('type' => 'ticket_priority',
				'selected' => $selected));
		}

		function get_category_name( $cat_id )
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

		function readold2( $data = array() )
		{
			$locations = $this->so->read($data);

			$this->total_records = $this->so->total_records;
			$this->uicols = $this->so->uicols;

			return $locations;
		}

		function get_data_report( $data = array() )
		{
			return $this->so->get_data_report($data);
		}

		function read( $data = array() )
		{
			static $category_name = array();
			static $account = array();
			static $vendor_cache = array();

			$interlink = CreateObject('property.interlink');
			$data['start_date'] = $this->bocommon->date_to_timestamp($data['start_date']);
			$data['end_date'] = $this->bocommon->date_to_timestamp($data['end_date']);

			$custom_filtermethod = array();
			foreach ($this->get_custom_filters() as $custom_filter)
			{
				if ($_REQUEST[$custom_filter]) //just testing..
				{
					$custom_filtermethod[$custom_filter] = phpgw::get_var($custom_filter, 'int');
				}
			}

			$data['custom_filtermethod'] = $custom_filtermethod;

			$tickets = $this->so->read($data);

			$this->total_records = $this->so->total_records;
			$this->sum_budget = $this->so->sum_budget;
			$this->sum_actual_cost = $this->so->sum_actual_cost;
			$this->sum_difference = $this->so->sum_difference;

			if (!$external)
			{
				$entity = $this->get_origin_entity_type();
				$contacts = CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor', false);

				$custom = createObject('property.custom_fields');
				$vendor_data['attributes'] = $custom->find('property', '.vendor', 0, '', 'ASC', 'attrib_sort', true, true);
			}
			else
			{
				$entity[0]['type'] = '.project';
				$entity[0]['name'] = 'project';

				$this->uicols_related = array('project');
			}


			$selected_columns = !empty($GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['ticket_columns'] : array();

			$custom_status = $this->so->get_custom_status();
			$closed_status = array('X');
			foreach ($custom_status as $custom)
			{
				if ($custom['closed'])
				{
					$closed_status[] = "C{$custom['id']}";
				}
			}
			$order_dim1_list = $this->so->get_order_dim1();
			$order_dim1_arr = array();
			foreach ($order_dim1_list as $_order_dim1)
			{
				$order_dim1_arr[$_order_dim1['id']] = $_order_dim1['name'];
			}

			foreach ($tickets as & $ticket)
			{
				if(in_array('details', $selected_columns))
				{
					$ticket['details'] = "#1: {$ticket['details']}";
					$additional_notes = $this->read_additional_notes((int)$ticket['id'] );
					foreach ($additional_notes as $additional_note)
					{
						$ticket['details'] .= "<br/>#{$additional_note['value_count']}: {$additional_note['value_note']}";
					}
				}

				if (!isset($category_name[$ticket['cat_id']]))
				{
					$category_name[$ticket['cat_id']] = $this->get_category_name($ticket['cat_id']);
				}

				$ticket['category'] = $category_name[$ticket['cat_id']];

				if (!$ticket['subject'])
				{
					$ticket['subject'] = $category_name[$ticket['cat_id']];
				}

				if ($ticket['order_dim1'])
				{
					$ticket['order_dim1'] = $order_dim1_arr[$ticket['order_dim1']];
				}

				if (!isset($account[$ticket['user_id']]))
				{
					$ticket['user'] = $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
					$account[$ticket['user_id']] = $ticket['user'];
				}
				else
				{
					$ticket['user'] = $account[$ticket['user_id']];
				}


				$ticket['difference'] = 0;


				if ($ticket['estimate'] && !in_array($ticket['status'], $closed_status))
				{
					$ticket['difference'] = $ticket['estimate'] - (float)$ticket['actual_cost'];
					if ($ticket['difference'] < 0)
					{
						$ticket['difference'] = 0;
					}
				}

				if ($ticket['assignedto'])
				{
					if (!isset($account[$ticket['assignedto']]))
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
					if (!isset($account[$ticket['group_id']]))
					{
						$ticket['assignedto'] = $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);
						$account[$ticket['group_id']] = $ticket['assignedto'];
					}
					else
					{
						$ticket['assignedto'] = $account[$ticket['group_id']];
					}
				}

				$ticket['entry_date'] = $GLOBALS['phpgw']->common->show_date($ticket['entry_date'], $this->dateformat);
				$ticket['modified_date'] = $GLOBALS['phpgw']->common->show_date($ticket['modified_date'], $this->dateformat);
				if ($ticket['finnish_date2'])
				{
					$ticket['delay'] = round(($ticket['finnish_date2'] - $ticket['finnish_date']) / (24 * 3600));
					$ticket['finnish_date'] = $ticket['finnish_date2'];
				}
				$ticket['finnish_date'] = !empty($ticket['finnish_date']) ? $GLOBALS['phpgw']->common->show_date($ticket['finnish_date'], $this->dateformat) : '';
				$ticket['order_deadline'] = !empty($ticket['order_deadline'])  ? $GLOBALS['phpgw']->common->show_date($ticket['order_deadline'], $this->dateformat) : '';
				$ticket['order_deadline2'] = !empty($ticket['order_deadline2'])  ? $GLOBALS['phpgw']->common->show_date($ticket['order_deadline2'], $this->dateformat) : '';

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
//				_debug_array($this->uicols_related);
//				_debug_array($entity);
				if (isset($entity) && is_array($entity))
				{
					for ($j = 0; $j < count($entity); $j++)
					{
						$ticket['child_date'][$entity[$j]['name']] = $interlink->get_child_date('property', '.ticket', $entity[$j]['type'], $ticket['id'], isset($entity[$j]['entity_id']) ? $entity[$j]['entity_id'] : '', isset($entity[$j]['cat_id']) ? $entity[$j]['cat_id'] : '');
						if ($ticket['child_date'][$entity[$j]['name']]['date_info'] && !$download)
						{
							$ticket['child_date'][$entity[$j]['name']]['statustext'] = $interlink->get_relation_info(array(
								'location' => $entity[$j]['type']), $ticket['child_date'][$entity[$j]['name']]['date_info'][0]['target_id']);
						}
					}
				}
				if ($ticket['vendor_id'])
				{
					if (isset($vendor_cache[$ticket['vendor_id']]))
					{
						$ticket['vendor'] = $vendor_cache[$ticket['vendor_id']];
					}
					else
					{
						$vendor_data = $contacts->read_single(array('id' => $ticket['vendor_id']), $vendor_data);
						if ($vendor_data)
						{
							foreach ($vendor_data['attributes'] as $attribute)
							{
								if ($attribute['name'] == 'org_name')
								{
									$vendor_cache[$ticket['vendor_id']] = $attribute['value'];
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

		function read_single( $id, $values = array(), $view = false )
		{
			$this->so->update_view($id);

			$values['attributes'] = $this->get_custom_cols();
			$ticket = $this->so->read_single($id, $values);
			$ticket = $this->custom->prepare($ticket, 'property', '.ticket', $view);

			$ticket['user_lid'] = $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
			$ticket['group_lid'] = $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);

			$interlink = CreateObject('property.interlink');
			$ticket['origin'] = $interlink->get_relation('property', '.ticket', $id, 'origin');
			$ticket['target'] = $interlink->get_relation('property', '.ticket', $id, 'target');
			//_debug_array($ticket);
			if (isset($ticket['finnish_date2']) && $ticket['finnish_date2'])
			{
				$ticket['finnish_date'] = $ticket['finnish_date2'];
			}

			if ($ticket['finnish_date'])
			{
				$ticket['finnish_date'] = $GLOBALS['phpgw']->common->show_date($ticket['finnish_date'], $this->dateformat);
			}
			if ($ticket['order_deadline'])
			{
				$ticket['order_deadline'] = $GLOBALS['phpgw']->common->show_date($ticket['order_deadline'], $this->dateformat);
			}
			if ($ticket['order_deadline2'])
			{
				$ticket['order_deadline2'] = $GLOBALS['phpgw']->common->show_date($ticket['order_deadline2'], $this->dateformat);
			}

			if ($ticket['location_code'])
			{
				$solocation = CreateObject('property.solocation');
				$ticket['location_data'] = $solocation->read_single($ticket['location_code']);
			}
			//_debug_array($ticket['location_data']);
			if ($ticket['p_num'])
			{
				$soadmin_entity = CreateObject('property.soadmin_entity');
				$category = $soadmin_entity->read_single_category($ticket['p_entity_id'], $ticket['p_cat_id']);

				$ticket['p'][$ticket['p_entity_id']]['p_num'] = $ticket['p_num'];
				$ticket['p'][$ticket['p_entity_id']]['p_entity_id'] = $ticket['p_entity_id'];
				$ticket['p'][$ticket['p_entity_id']]['p_cat_id'] = $ticket['p_cat_id'];
				$ticket['p'][$ticket['p_entity_id']]['p_cat_name'] = $category['name'];
			}


			if ($ticket['tenant_id'] > 0)
			{
				$tenant_data = $this->bocommon->read_single_tenant($ticket['tenant_id']);
				$ticket['location_data']['tenant_id'] = $ticket['tenant_id'];
				$ticket['location_data']['contact_phone'] = $tenant_data['contact_phone'];
				$ticket['location_data']['last_name'] = $tenant_data['last_name'];
				$ticket['location_data']['first_name'] = $tenant_data['first_name'];
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
			$ticket['entry_date'] = $GLOBALS['phpgw']->common->show_date($ticket['entry_date'], $this->dateformat);

			// Figure out when it was last closed
			$history_values = $this->historylog->return_array(array(), array('O'), 'history_timestamp', 'ASC', $id);
			$ticket['last_opened'] = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime']);

			if ($ticket['status'] == 'X')
			{

				$history_values = $this->historylog->return_array(array(), array('X'), 'history_timestamp', 'DESC', $id);
				$ticket['timestampclosed'] = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'], $this->dateformat);
			}

			$status_text = $this->get_status_text();

			$ticket['status_name'] = $status_text[$ticket['status']];
			$ticket['user_lid'] = $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
			$ticket['category_name'] = ucfirst($this->get_category_name($ticket['cat_id']));

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$ticket['files'] = $vfs->ls(array(
				'string' => "/property/fmticket/{$id}",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$j = count($ticket['files']);
			for ($i = 0; $i < $j; $i++)
			{
				$ticket['files'][$i]['file_name'] = urlencode($ticket['files'][$i]['name']);
			}

			if (!isset($ticket['files'][0]['file_id']) || !$ticket['files'][0]['file_id'])
			{
				unset($ticket['files']);
			}
			return $ticket;
		}

		function read_additional_notes( $id )
		{
			$additional_notes = array();
			$history_array = $this->historylog->return_array(array(), array('C'), '', '', $id);

			$i = 2;
			foreach ($history_array as $value)
			{
				$additional_notes[] = array
					(
					'value_id' => $value['id'],
					'value_count' => $i,
					'value_date' => $GLOBALS['phpgw']->common->show_date($value['datetime']),
					'value_user' => $value['owner'],
					'value_note' => stripslashes($value['new_value']),
					'value_publish' => $value['publish'],
				);
				$i++;
			}
			return $additional_notes;
		}

		function read_record_history( $id )
		{
			$history_array = $this->historylog->return_array(array('C', 'O'), array(), '', '', $id);

			$status_text = $this->get_status_text();
			$record_history = array();
			$i = 0;

			foreach ($history_array as $value)
			{
				$record_history[$i]['value_date'] = $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$record_history[$i]['value_user'] = $value['owner'];

				switch ($value['status'])
				{
					case 'R': $type = lang('Re-opened');
						break;
					case 'X': $type = lang('Closed');
						break;
					case 'O': $type = lang('Opened');
						break;
					case 'A': $type = lang('Re-assigned');
						break;
					case 'G': $type = lang('Re-assigned group');
						break;
					case 'P': $type = lang('Priority changed');
						break;
					case 'T': $type = lang('Category changed');
						break;
					case 'S': $type = lang('Subject changed');
						break;
					case 'H': $type = lang('Billable hours changed');
						break;
					case 'B': $type = lang('Budget changed');
						break;
//				case 'B': $type = lang('Billable rate changed'); break;
					case 'F': $type = lang('finnish date changed');
						break;
					case 'IF': $type = lang('Initial finnish date');
						break;
					case 'L': $type = lang('Location changed');
						break;
					case 'AC': $type = lang('actual cost changed');
						break;
					case 'M':
						$type = lang('Sent by email to');
						$this->order_sent_adress = $value['new_value']; // in case we want to resend the order as an reminder
						break;
					case 'MS':
						$type = lang('Sent by sms');
						break;
					case 'RM': $type = lang('remark');
						break;
					case 'AR': $type = lang('request for approval');
						break;
					case 'AA': $type = lang('approved');
						break;
					default:
					// nothing
				}

				//		if ( $value['status'] == 'X' || $value['status'] == 'R' || (strlen($value['status']) == 2 && substr($value['new_value'], 0, 1) == 'C') ) // if custom status
				if ($value['status'] == 'X' || $value['status'] == 'R' || preg_match('/^C/i', $value['status']) || ( $value['status'] == 'R' && preg_match('/^C/i', $value['new_value']))) // if custom status
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

				$record_history[$i]['value_action'] = $type ? $type : '';
				unset($type);
				if ($value['status'] == 'A' || $value['status'] == 'G')
				{
					if ((int)$value['new_value'] > 0)
					{
						$record_history[$i]['value_new_value'] = $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
					}
					else
					{
						$record_history[$i]['value_new_value'] = lang('None');
					}

					if ((int)$value['old_value'] > 0)
					{
						$record_history[$i]['value_old_value'] = $value['old_value'] ? $GLOBALS['phpgw']->accounts->id2name($value['old_value']) : '';
					}
					else
					{
						$record_history[$i]['value_old_value'] = lang('None');
					}
				}
				else if ($value['status'] == 'T')
				{
					$record_history[$i]['value_new_value'] = $this->get_category_name($value['new_value']);
					$record_history[$i]['value_old_value'] = $this->get_category_name($value['old_value']);
				}
				else if (($value['status'] == 'F') || ($value['status'] == 'IF'))
				{
					$record_history[$i]['value_new_value'] = $GLOBALS['phpgw']->common->show_date($value['new_value'], $this->dateformat);
				}
				else if ($value['status'] != 'O' && $value['new_value'])
				{
					$record_history[$i]['value_new_value'] = $value['new_value'];
					$record_history[$i]['value_old_value'] = $value['old_value'];
				}
				else
				{
					$record_history[$i]['value_new_value'] = '';
				}

				$i++;
			}

			return $record_history;
		}

		/**
		 * Simplified method for adding tickets from external apps
		 * 	$data = array
		 * 	(
		 * 		'origin' 			=> $location_id,
		 * 		'origin_id'			=> $location_item_id,
		 * 		'location_code' 	=> $location_code,
		 * 		'cat_id'			=> $cat_id,
		 * 		'priority'			=> $priority, //optional (1-3)
		 * 		'title'				=> $title,
		 * 		'details'			=> $details,
		 * 		'file_input_name'	=> 'file' // default, optional
		 * 	);
		 *
		 */
		function add_ticket( $data )
		{

			$cancel_attachment = empty($data['cancel_attachment']) ? false : true;
			$boloc = CreateObject('property.bolocation');
			$location_details = $boloc->read_single($data['location_code'], array('noattrib' => true));

			$location = array();
			$_location_arr = explode('-', $data['location_code']);
			$i = 1;
			foreach ($_location_arr as $_loc)
			{
				$location["loc{$i}"] = $_loc;
				$i++;
			}

			$assignedto = execMethod('property.boresponsible.get_responsible', array('location' => $location,
				'cat_id' => $data['cat_id']));

			if($assignedto)
			{
				$group_or_user = get_class($GLOBALS['phpgw']->accounts->get($assignedto));
			}

			if($group_or_user == "phpgwapi_group")
			{
				$data['group_id'] = !empty($data['group_id']) ? $data['group_id'] : $assignedto;		
				$assignedto = 0;
			}


			if (!$assignedto)
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
				'origin_id' => isset($data['origin_id']) ? $data['origin_id'] : null,
				'origin_item_id' => isset($data['origin_item_id']) ? $data['origin_item_id'] : null,
				'cat_id' => $data['cat_id'],
				'group_id' => isset($data['group_id']) && $data['group_id'] ? $data['group_id'] : $default_group,
				'assignedto' => $assignedto,
				'priority' => isset($data['priority']) && $data['priority'] ? $data['priority'] : $default_priority,
				'status' => 'O', // O = Open
				'subject' => $data['title'],
				'details' => $data['details'],
				'apply' => true,
				'contact_id' => 0,
				'location' => $location,
				'location_code' => $data['location_code'],
				'street_name' => $location_details['street_name'],
				'street_number' => $location_details['street_number'],
				'location_name' => $location_details['loc1_name'],
				'send_mail'		=> true,
				'external_ticket_id' => !empty($data['external_ticket_id']) ? $data['external_ticket_id'] : null,
			);

			$result = $this->add($ticket);

			// Files
			$file_input_name = isset($data['file_input_name']) && $data['file_input_name'] ? $data['file_input_name'] : 'file';

			$file_name = @str_replace(' ', '_', $_FILES[$file_input_name]['name']);
			if (!$cancel_attachment && $file_name && $result['id'])
			{
				$bofiles = CreateObject('property.bofiles');
				$to_file = "{$bofiles->fakebase}/fmticket/{$result['id']}/{$file_name}";

				if ($bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => array(RELATIVE_NONE)
					)))
				{
					$msglog['error'][] = array('msg' => lang('This file already exists !'));
				}
				else
				{
					$bofiles->create_document_dir("fmticket/{$result['id']}");
					$bofiles->vfs->override_acl = 1;

					if (!$bofiles->vfs->cp(array(
							'from' => $_FILES[$file_input_name]['tmp_name'],
							'to' => $to_file,
							'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
					{
						$msglog['error'][] = array('msg' => lang('Failed to upload file!'));
					}
					$bofiles->vfs->override_acl = 0;
				}
			}
			return (int)$result['id'];
		}

		function add( $data, $values_attribute = array() )
		{
			if ((!isset($data['location_code']) || !$data['location_code']) && isset($data['location']) && is_array($data['location']))
			{
				foreach($data['location'] as $value)
				{
					if ($value)
					{
						$location[] = $value;
					}
				}
				$data['location_code'] = implode("-", $location);
			}

			$data['finnish_date'] = $this->bocommon->date_to_timestamp($data['finnish_date']);

			if ($values_attribute && is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			$criteria = array
				(
				'appname' => 'property',
				'location' => $this->acl_location,
				'allrows' => true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && $entry['pre_commit'])
				{
					require $file;
				}
			}

			$receipt = $this->so->add($data, $values_attribute);

			$this->config->read();

			if (!empty($data['send_mail']) || !empty($this->config->config_data['mailnotification']))
			{
				$receipt_mail = $this->mail_ticket($receipt['id'], false, $receipt, $data['location_code'], false, isset($data['send_mail']) && $data['send_mail'] ? true : false);
			}

			reset($custom_functions);
			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'])
				{
					require_once $file;
				}
			}

			if (isset($receipt_mail) && is_array($receipt_mail))
			{
				$receipt = array_merge($receipt, $receipt_mail);
			}
			return $receipt;
		}

		function get_address_element( $location_code = '' )
		{
			$address_element = array();
			if ($location_code)
			{
				$solocation = CreateObject('property.solocation');
				$custom = createObject('property.custom_fields');
				$location_data = $solocation->read_single($location_code);

				$location_types = execMethod('property.soadmin_location.select_location_type');
				$type_id = count(explode('-', $location_code));

				for ($i = 1; $i < $type_id + 1; $i++)
				{
					$address_element[] = array
						(
						'text' => $location_types[($i - 1)]['name'],
						'value' => $location_data["loc{$i}"] . '  ' . $location_data["loc{$i}_name"]
					);
				}

				$fm_location_cols = $custom->find('property', '.location.' . $type_id, 0, '', 'ASC', 'attrib_sort', true, true);
				$i = 0;
				foreach ($fm_location_cols as $location_entry)
				{
					if ($location_entry['lookup_form'])
					{
						$address_element[] = array
							(
							'text' => $location_entry['input_text'],
							'value' => $location_data[$location_entry['column_name']]
						);
					}
					$i++;
				}
			}
			return $address_element;
		}

		function mail_ticket( $id, $fields_updated, $receipt = array(), $location_code = '', $get_message = false, $force_send = false )
		{
			$log_recipients = array();
			$this->send = CreateObject('phpgwapi.send');

			$ticket = $this->read_single($id);

			$address_element = $this->get_address_element($ticket['location_code']);

			$history_values = $this->historylog->return_array(array(), array('O'), 'history_timestamp', 'DESC', $id);
			$entry_date = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime']);

			$status_text = $this->get_status_text();

			if ($ticket['status'] == 'X')
			{
				$history_values = $this->historylog->return_array(array(), array('X'), 'history_timestamp', 'DESC', $id);
				$timestampclosed = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime']);
			}

			$group_name = $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);

			// build subject
			$subject = '[' . lang('Ticket') . ' #' . $id . '] : ' . $location_code . ' ' . $this->get_category_name($ticket['cat_id']) . '; ' . $ticket['subject'];

			//-----------from--------

			$current_prefs_user = $this->bocommon->create_preferences('property', $GLOBALS['phpgw_info']['user']['account_id']);
			$current_user_address = "{$GLOBALS['phpgw_info']['user']['fullname']}<{$current_prefs_user['email']}>";

			//-----------from--------
			// build body

			$request_scheme = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http' : 'https';

			if($request_scheme == 'https')
			{
				$GLOBALS['phpgw_info']['server']['enforce_ssl'] = true;
			}
			$body = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view',
					'id' => $id), false, true) . '">' . lang('Ticket') . ' #' . $id . '</a>' . "\n";

			$body .= "<table>";
			$body .= '<tr><td>'. lang('Date Opened').'</td><td>:&nbsp;'.$entry_date."</td></tr>";
			$body .= '<tr><td>'. lang('status').'</td><td>:&nbsp;'.$status_text[$ticket['status']]."</td></tr>";
			$body .= '<tr><td>'. lang('Category').'</td><td>:&nbsp;'. $this->get_category_name($ticket['cat_id']) ."</td></tr>";
			$body .= '<tr><td>'. lang('Location') . '</td><td>:&nbsp;' . $ticket['location_code'] ."</td></tr>";
			$body .= '<tr><td>'. lang('Address') . '</td><td>:&nbsp;' . $ticket['address'] ."</td></tr>";
			if (isset($address_element) AND is_array($address_element))
			{
				foreach ($address_element as $address_entry)
				{
					$body .= '<tr><td>'. $address_entry['text'] . '</td><td>:&nbsp;' . $address_entry['value'] ."</td></tr>";
				}
			}

			if ($ticket['tenant_id'])
			{
				$tenant_data = $this->bocommon->read_single_tenant($ticket['tenant_id']);
				$body .= '<tr><td>'. lang('Tenant') . '</td><td>:&nbsp;' . $tenant_data['first_name'] . ' ' . $tenant_data['last_name'] ."</td></tr>";

				if ($tenant_data['contact_phone'])
				{
					$body .= '<tr><td>'. lang('Contact phone') . '</td><td>:&nbsp;' . $tenant_data['contact_phone'] ."</td></tr>";
				}
			}
			$body .= '<tr><td>'. lang('Assigned To').'</td><td>:&nbsp;'.$GLOBALS['phpgw']->accounts->id2name($ticket['assignedto'])."</td></tr>";
			if(empty($this->config->config_data['disable_priority']))
			{
				$body .= '<tr><td>'. lang('Priority').'</td><td>:&nbsp;'.$ticket['priority']."</td></tr>";
			}
			if ($group_name)
			{
				$body .= '<tr><td>'. lang('Group') . '</td><td>:&nbsp;' . $group_name  ."</td></tr>";
			}

			$body .= '<tr><td>'. lang('Opened By') . '</td><td>:&nbsp;' . $ticket['user_name'] ."</td></tr>";

			if ($timestampclosed)
			{
				$body .= '<tr><td>'. lang('Date Closed') . '</td><td>:&nbsp;' . $timestampclosed  ."</td></tr>";
			}

			if(!empty($ticket['attributes']))
			{
				$custom		= createObject('property.custom_fields');
				$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');

				foreach ($ticket['attributes'] as $attribute)
				{
					$custom_value = $custom->get_translated_value(array(
								'value' => $attribute['value'],
								'attrib_id' => $attribute['id'],
								'datatype' => $attribute['datatype'],
								'get_single_function' => $attribute['get_single_function'],
								'get_single_function_input' => $attribute['get_single_function_input']
								),
								$location_id);

					if($custom_value)
					{
						$body .= '<tr><td>'. $attribute['input_text'].'</td><td>:&nbsp;'.$custom_value."</td></tr>";
					}
				}
			}

			$body .= '</table>';

			$i = 1;
			$lang_date = lang('date');
			$lang_user = lang('user');
			$lang_note = lang('note');
			$table_content = <<<HTML
		<thead>
			<tr>
				<th>
					#
				</th>
				<th>
					{$lang_date}
				</th>
				<th>
					{$lang_user}
				</th>
				<th>
					{$lang_note}
				</th>
			</tr>
		</thead>
HTML;
			$table_content .= "<tr><td>{$i}</td><td>{$entry_date}</td><td>{$ticket['user_name']}</td><td>{$ticket['details']}</td></tr>";

			$additional_notes = $this->read_additional_notes($id);

			foreach ($additional_notes as $value)
			{
				$table_content .= "<tr><td>{$value['value_count']}</td><td>{$value['value_date']}</td><td>{$value['value_user']}</td><td>{$value['value_note']}</td></tr>";
				$i++;
			}

			$body.= "<table border='1' class='pure-table pure-table-bordered pure-table-striped'>{$table_content}</table>";

			$subject .= "::{$i}";

			if ($get_message)
			{
				return array('subject' => $subject, 'body' => $body);
			}

			$css = file_get_contents(PHPGW_SERVER_ROOT . "/phpgwapi/templates/pure/css/pure-min.css");

			$html = <<<HTML
<html>
	<head>
		<meta charset="utf-8">
		<style TYPE="text/css">
			{$css}
		</style>
	</head>
	<body>
		{$body}
	</body>
</html>
HTML;


			$members = array();

			if (isset($this->config->config_data['groupnotification']) && $this->config->config_data['groupnotification'] == 2)
			{
				// Never send to groups
			}
			else if ((isset($this->config->config_data['groupnotification']) && $this->config->config_data['groupnotification'] == 1 && $ticket['group_id'] ) || ($force_send && $ticket['group_id']))
			{
				$log_recipients[] = $group_name;
				$members_gross = $GLOBALS['phpgw']->accounts->member($ticket['group_id'], true);
				foreach ($members_gross as $user)
				{
					$members[$user['account_id']] = $user['account_name'];
				}
				unset($members_gross);
			}

			$GLOBALS['phpgw']->preferences->set_account_id($ticket['user_id'], true);
			if ((isset($GLOBALS['phpgw']->preferences->data['property']['tts_notify_me']) && ($GLOBALS['phpgw']->preferences->data['property']['tts_notify_me'] == 1)
				) || ($this->config->config_data['ownernotification'] && $ticket['user_id']))
			{
				// add owner to recipients
				$members[$ticket['user_id']] = $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
				$log_recipients[] = $GLOBALS['phpgw']->accounts->get($ticket['user_id'])->__toString();
			}

			if ($ticket['assignedto'])
			{
				$GLOBALS['phpgw']->preferences->set_account_id($ticket['assignedto'], true);
				if ((isset($GLOBALS['phpgw']->preferences->data['property']['tts_notify_me']) && ($GLOBALS['phpgw']->preferences->data['property']['tts_notify_me'] == 1)
					) || ($this->config->config_data['assignednotification'] && $ticket['assignedto']) || ($force_send && $ticket['assignedto'])
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

			foreach ($members as $account_id => $account_name)
			{
				$prefs = $this->bocommon->create_preferences('property', $account_id);
				if (!isset($prefs['tts_notify_me']) || $prefs['tts_notify_me'] == 1)
				{
					/**
					 * Calculate email from username
					 */
					if(!$prefs['email'])
					{
						$email_domain = !empty($GLOBALS['phpgw_info']['server']['email_domain']) ? $GLOBALS['phpgw_info']['server']['email_domain'] : 'bergen.kommune.no';
						$account_lid = $GLOBALS['phpgw']->accounts->get($account_id)->lid;
						$prefs['email'] = "{$account_lid}@{$email_domain}";
					}

					if ($validator->check_email_address($prefs['email']))
					{
						// Email address is technically valid
						// avoid problems with the delimiter in the send class
						if (strpos($account_name, ','))
						{
							$_account_name = explode(',', $account_name);
							$account_name = ltrim($_account_name[1]) . ' ' . $_account_name[0];
						}

						$toarray[] = "{$account_name}<{$prefs['email']}>";
					}
					else
					{
//						$receipt['error'][] = array('msg'=> lang('Your message could not be sent!'));
						$receipt['error'][] = array('msg' => lang('This user has not defined an email address !') . ' : ' . $account_name);
					}
				}
			}


			$notify_list = execMethod('property.notify.read', array
				(
				'location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location),
				'location_item_id' => $id
				)
			);

			if (isset($GLOBALS['phpgw_info']['user']['apps']['sms']))
			{

				$sms_text = "{$subject}. \r\n{$GLOBALS['phpgw_info']['user']['fullname']} \r\n{$GLOBALS['phpgw_info']['user']['preferences']['property']['email']}";
				$sms = CreateObject('sms.sms');

				foreach ($notify_list as $entry)
				{
					if ($entry['is_active'] && $entry['notification_method'] == 'sms' && $entry['sms'])
					{
						$sms->websend2pv($this->account, $entry['sms'], $sms_text);
						$toarray_sms[] = "{$entry['first_name']} {$entry['last_name']}({$entry['sms']})";
						$receipt['message'][] = array('msg' => lang('%1 is notified', "{$entry['first_name']} {$entry['last_name']}"));
					}
				}
				unset($entry);
				if ($toarray_sms)
				{
					$this->historylog->add('MS', $id, "{$subject}::" . implode(',', $toarray_sms));
				}
			}

			reset($notify_list);
			foreach ($notify_list as $entry)
			{
				if ($entry['is_active'] && $entry['notification_method'] == 'email' && $entry['email'])
				{
					$toarray[] = "{$entry['first_name']} {$entry['last_name']}<{$entry['email']}>";
				}

				$log_recipients[] = "{$entry['first_name']} {$entry['last_name']}";
			}
			unset($entry);

			$rc = false;
			if ($toarray)
			{
				$to = implode(';', $toarray);
				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					try
					{
						$rc = $this->send->msg('email', $to, $subject, $html, '', $cc, $bcc, $current_user_address, $GLOBALS['phpgw_info']['user']['fullname'], 'html');
					}
					catch (Exception $e)
					{
						$receipt['error'][] = array('msg' => $e->getMessage());
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('SMTP server is not set! (admin section)'));
				}
			}

			if ($rc && $log_recipients)
			{
				$this->historylog->add('M', $id, implode(';', array_unique($log_recipients)));
			}
			return $receipt;
		}

		function delete( $id )
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

		public function update_status( $data, $id = 0 )
		{
			$receipt = array();
			if ($this->so->update_status($data, $id))
			{
				$receipt['message'][] = array('msg' => lang('Ticket %1 has been updated', $id));
			}
			$this->fields_updated = $this->so->fields_updated;
			return $receipt;
		}

		public function update_priority( $data, $id = 0 )
		{
			$receipt = $this->so->update_priority($data, $id);
			$this->fields_updated = $this->so->fields_updated;
			return $receipt;
		}

		public function update_ticket( &$data, $id, $receipt = array(), $values_attribute = array() )
		{
			if ($values_attribute && is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			$criteria = array
				(
				'appname' => 'property',
				'location' => $this->acl_location,
				'allrows' => true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && $entry['pre_commit'])
				{
					require $file;
				}
			}

			$receipt = $this->so->update_ticket($data, $id, $receipt, $values_attribute, $this->simple);
			$this->fields_updated = $this->so->fields_updated;


			reset($custom_functions);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'])
				{
					require $file;
				}
			}

			return $receipt;
		}

		public function get_vendors( $selected )
		{
			$vendors = $this->so->get_vendors();
			foreach ($vendors as &$vendor)
			{
				if ($vendor['id'] == $selected)
				{
					$vendor['selected'] = 1;
					break;
				}
			}

			$default_value = array
				(
				'id' => '',
				'name' => lang('vendor')
			);
			array_unshift($vendors, $default_value);
			return $vendors;
		}

		public function get_ecodimb( $selected )
		{
			$values = $this->so->get_ecodimb();
			foreach ($values as &$value)
			{
				if ($value['id'] == $selected)
				{
					$value['selected'] = 1;
					break;
				}
			}

			$default_value = array
				(
				'id' => '',
				'name' => lang('dimb')
			);
			array_unshift($values, $default_value);
			return $values;
		}

		public function get_b_account( $selected )
		{
			$values = $this->so->get_b_account();
			foreach ($values as &$value)
			{
				if ($value['id'] == $selected)
				{
					$value['selected'] = 1;
					break;
				}
			}

			$default_value = array
				(
				'id' => '',
				'name' => lang('budget account')
			);
			array_unshift($values, $default_value);
			return $values;
		}

		public function get_building_part( $selected )
		{
			$values = $this->so->get_building_part();
			foreach ($values as &$value)
			{
				if ($value['id'] == $selected)
				{
					$value['selected'] = 1;
					break;
				}
			}

			$default_value = array
				(
				'id' => '',
				'name' => lang('building part')
			);
			array_unshift($values, $default_value);
			return $values;
		}

		public function get_branch( $selected )
		{
			$values = $this->so->get_branch();
			foreach ($values as &$value)
			{
				if ($value['id'] == $selected)
				{
					$value['selected'] = 1;
					break;
				}
			}

			$default_value = array
				(
				'id' => '',
				'name' => lang('branch')
			);
			array_unshift($values, $default_value);
			return $values;
		}

		public function get_order_dim1( $selected )
		{
			$values = $this->so->get_order_dim1();
			foreach ($values as &$value)
			{
				if ($value['id'] == $selected)
				{
					$value['selected'] = 1;
					break;
				}
			}

			$default_value = array
				(
				'id' => '',
				'name' => lang('order_dim1')
			);
			array_unshift($values, $default_value);
			return $values;
		}

		public function addfiles()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$acl = & $GLOBALS['phpgw']->acl;
			$acl_add = $acl->check('.ticket', PHPGW_ACL_ADD, 'property');
			$acl_edit = $acl->check('.ticket', PHPGW_ACL_EDIT, 'property');
			$id = phpgw::get_var('id', 'int');
			$check = phpgw::get_var('check', 'bool');
			$fileuploader = CreateObject('property.fileuploader');

			if (!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if (!$id)
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
					$targetFile = str_replace('//', '/', $targetPath) . $_FILES['Filedata']['name'];
					move_uploaded_file($tempFile, $targetFile);
					echo str_replace($GLOBALS['phpgw_info']['server']['temp_dir'], '', $targetFile);
				}
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if ($check)
			{
				$fileuploader->check("fmticket/{$id}");
			}
			else
			{
				$fileuploader->upload("fmticket/{$id}");
			}
		}

		public function get_attributes( $values )
		{
			$values['attributes'] = $this->get_custom_cols();
			$values = $this->custom->prepare($values, 'property', '.ticket', false);
			return $values;
		}

		public function get_budgets( $id )
		{
			$budgets = $this->so->get_budgets($id);
			foreach ($budgets as &$budget)
			{
				$budget['created_on_date'] = $GLOBALS['phpgw']->common->show_date($payment['created_on'], $this->dateformat);
			}
			return $budgets;
		}

		public function get_payments( $id )
		{
			$payments = $this->so->get_payments($id);
			foreach ($payments as &$payment)
			{
				$payment['created_on_date'] = $GLOBALS['phpgw']->common->show_date($payment['created_on'], $this->dateformat);
			}
			return $payments;
		}

		public function get_reported_by( $selected = 0 )
		{
			$values = $this->so->get_reported_by();

			foreach ($values as &$entry)
			{
				$entry['selected'] = $entry['id'] == $selected ? 1 : 0;
			}
			return $values;
		}

		public function receive_order( $id, $received_amount, $external_voucher_id = 0 )
		{
			$transfer_action = 'receive_order'; // used as trigger within the custom function
			$acl_location = $this->acl_location;

			$criteria = array(
				'appname' => 'property',
				'location' => $acl_location,
				'allrows' => true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'])
				{
					require $file;
				}
			}
			// $result from the custom function
			return array(
				'result' => $result,
				'time'	=> $GLOBALS['phpgw']->common->show_date(time())
				);
		}


		function add_relation( $add_relation, $id )
		{
			return $this->so->add_relation($add_relation, $id);
		}

		/**
		 *
		 * @param type $ecodimb
		 * @param type $amount
		 * @param type $order_id
		 * @return array
		 * @throws Exception
		 */
		public function check_purchase_right($ecodimb = 0, $amount = 0, $order_id = 0)
		{
			$need_approval = empty($this->config->config_data['workorder_approval']) ? false : true;
			if(!$need_approval)
			{
				return array();
			}
			$approval_amount_limit = !empty($this->config->config_data['approval_amount_limit']) ? (int) $this->config->config_data['approval_amount_limit'] : 0;
			$approval_amount_limit2 = !empty($this->config->config_data['approval_amount_limit2']) ? (int) $this->config->config_data['approval_amount_limit2'] : 0;

			$approval_amount_limit1 = 0;
			if($approval_amount_limit2)
			{
				$approval_amount_limit1 = $approval_amount_limit;
				$approval_amount_limit = 0;
			}

			$config		= CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.ticket'));
			$check_external_register= !!$config->config_data['external_register']['check_external_register'];
			$supervisors = array();
			if (isset($this->config->config_data['invoice_acl']) && $this->config->config_data['invoice_acl'] == 'dimb')
			{
				$invoice = CreateObject('property.soinvoice');
				$default_found = false;
				$supervisor_id = $invoice->get_default_dimb_role_user(3, $ecodimb);
				if($supervisor_id)
				{
					$supervisors[$supervisor_id] =  array('id' => $supervisor_id, 'required' => false, 'default' => true);
					$default_found = true;
				}

				$sodimb_role_users = execMethod('property.sodimb_role_user.read', array
					(
					'dimb_id' => $ecodimb,
					'role_id' => 2,
					'query_start' => date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
					'get_netto_list' => true
					)
				);
				if (isset($sodimb_role_users[$ecodimb][2]) && is_array($sodimb_role_users[$ecodimb][2]))
				{
					foreach ($sodimb_role_users[$ecodimb][2] as $supervisor_id => $entry)
					{
						$supervisors[$supervisor_id] = array('id' => $supervisor_id, 'required' => false, 'default' =>  !$default_found ? !!$entry['default_user'] : false);
					}
				}

			}
			else if($check_external_register && $ecodimb)
			{
				$url		= $config->config_data['external_register']['url'];
				$username	= $config->config_data['external_register']['username'];
				$password	= $config->config_data['external_register']['password'];
				$sub_check = 'fullmakter';

				try
				{
					$fullmakter = $this->check_external_register(array(
						'url'		=> $url,
						'username'	=> $username,
						'password'	=> $password,
						'sub_check'	=> $sub_check,
						'id'		=> sprintf("%06s", $ecodimb)
						)
					);
				}
				catch (Exception $ex)
				{
					throw $ex;
				}

				/**
				 * some magic...to decide $supervisor_lid
				 * Agresso/Bergen spesific
				 */
				if(isset($fullmakter[0]))
				{
//					if($amount > 5000 && $amount <= 100000)
					if($amount > 50000 && $amount <= 100000)
					{
						$supervisor_lid = strtolower($fullmakter[0]['inntil100k']);
					}
					else if ($amount > 100000 && $amount <= 1000000)
					{
						$supervisor_lid = strtolower($fullmakter[0]['fra100kTil1m']);
					}
					else if ($amount > 1000000 && $amount <= 5000000)
					{
						$supervisor_lid = strtolower($fullmakter[0]['fra1mTil5m']);
					}
					else if ($amount > 5000000)
					{
						$supervisor_lid = strtolower($fullmakter[0]['ubegrenset']);
					}
				}

				/*
					[inntil100k] => (string) DV645
					[fra100kTil1m] => (string) DV645
					[fra1mTil5m] => (string) YN450
					[ubegrenset] => (string) JG406
					[periodeFra] => (string) 200300
					[periodeTil] => (string) 209912
					[status] => (string) N
					[aktiv] => (bool) true
				*/

				if($supervisor_lid)
				{
					$supervisor_id = $GLOBALS['phpgw']->accounts->name2id($supervisor_lid);
					$supervisors[$supervisor_id] = array('id' => $supervisor_id, 'required' => true);
				}
			}
			else if($approval_amount_limit > 0 && $amount > $approval_amount_limit)
			{
				$supervisor_id = 0;

				if (!empty($GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from']))
				{
					$supervisor_id = $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];
				}

				if ($supervisor_id)
				{
					$supervisors[$supervisor_id] = array('id' => $supervisor_id, 'required' => true, 'default' => true);

					$prefs = $this->bocommon->create_preferences('property', $supervisor_id);

					if (!empty($prefs['approval_from']) && empty($supervisors[$prefs['approval_from']]))
					{
						$supervisor_id = $prefs['approval_from'];
						$supervisors[$supervisor_id] = array('id' => $supervisor_id, 'required' => false);
					}
					unset($prefs);
				}
			}
			else if($approval_amount_limit1 > 0 && $amount > $approval_amount_limit1)
			{
				$invoice = CreateObject('property.soinvoice');
				$level_1_required = true;

				if($approval_amount_limit2 > 0 && $amount > $approval_amount_limit2)
				{
					$supervisor_id = $invoice->get_default_dimb_role_user(2, $ecodimb);
					if($supervisor_id)
					{
						$supervisors[$supervisor_id] =  array('id' => $supervisor_id, 'required' => true, 'default' => true);
						$level_1_required = false;
					}
				}

				$supervisor_id = $invoice->get_default_dimb_role_user(1, $ecodimb);
				if($supervisor_id)
				{
					$supervisors[$supervisor_id] =  array('id' => $supervisor_id, 'required' => $level_1_required, 'default' => $level_1_required);
				}
			}

//			if(!$check_external_register && !empty($GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'])
//				&& empty($supervisors[$GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from']]))
//			{
//				$supervisor_id =  $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];
//				$supervisors[$supervisor_id] = array('id' => $supervisor_id, 'required' => false, 'default' => true);
//			}

			return $this->get_supervisor_approval($supervisors, $order_id);
		}

		/**
		 *
		 * @param array $supervisors
		 * @param int $order_id
		 * @return array
		 * @throws Exception
		 */
		protected function get_supervisor_approval($supervisors, $order_id = 0)
		{
			$order_type = $this->bocommon->socommon->get_order_type($order_id);

			if($order_id)
			{
				switch ($order_type)
				{
					case 'workorder':
						$location = '.project.workorder';
						$location_item_id = $order_id;
						break;
					case 'ticket':
						$location = '.ticket';
						$location_item_id = $this->so->get_ticket_from_order($order_id);
						break;
					default:
						throw new Exception('Order type not supported');
				}
			}

			$supervisor_email = array();

			//Check if user is asked for approval
			if(empty($supervisors[$this->account]) && $order_id)
			{
				$action_params = array(
					'appname' => 'property',
					'location' => $location,
					'id'		=> $location_item_id,
					'responsible' => $this->account,
					'responsible_type' => 'user',
					'action' => 'approval',
					'deadline' => '',
					'created_by' => '',
					'allrows' => false,
					'closed' => false
				);
				$requests = CreateObject('property.sopending_action')->get_pending_action($action_params);
				if($requests)
				{
					$supervisors[$this->account] = array('id' => $this->account, 'required' => true, 'default' => true);
				}
				else
				{
					$action_params['closed'] = true;
					$requests = CreateObject('property.sopending_action')->get_pending_action($action_params);
					if($requests)
					{
						$supervisors[$this->account] = array('id' => $this->account, 'required' => false, 'default' => true);
					}
				}
			}


			if ($supervisors)
			{
				$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

				foreach ($supervisors as $supervisor_id => $info)
				{

					if($location_item_id)
					{
						$pending_action = CreateObject('property.sopending_action');

						$action_params = array(
							'appname' => 'property',
							'location' => $location,
							'id'		=> $location_item_id,
							'responsible' => $supervisor_id,
							'responsible_type' => 'user',
							'action' => 'approval',
							'deadline' => '',
							'created_by' => '',
							'allrows' => false,
							'closed' => true
						);

						$approvals = $pending_action->get_pending_action($action_params);
						if(!$approvals)
						{
							$action_params['closed'] = false;
						}

						$requests = $pending_action->get_pending_action($action_params);
					}

					$prefs = $this->bocommon->create_preferences('property', $supervisor_id);
					if (!empty($prefs['email']))
					{
						$address = $prefs['email'];
					}
					else
					{
						$email_domain = !empty($GLOBALS['phpgw_info']['server']['email_domain']) ? $GLOBALS['phpgw_info']['server']['email_domain'] : 'bergen.kommune.no';
						$address = $GLOBALS['phpgw']->accounts->id2name($supervisor_id) . '&lt;' . $GLOBALS['phpgw']->accounts->id2lid($supervisor_id) . "@{$email_domain}&gt;";
					}

					$supervisor_email[] = array(
						'id' => $supervisor_id,
						'address' => $address,
						'required'	=> $info['required'],
						'default'	=> !!$info['default'],
						'requested'	=> !!$requests[0]['action_requested'],
						'requested_time'=> $GLOBALS['phpgw']->common->show_date($requests[0]['action_requested'], $dateformat),
						'approved'	=> !!$approvals[0]['action_performed'],
						'approved_time'	 => $GLOBALS['phpgw']->common->show_date($approvals[0]['action_performed'], $dateformat),
						'is_user'	=> $supervisor_id == $this->account ? true : false
					);

					unset($prefs);
				}
			}
			return $supervisor_email;
		}


		public function check_external_register($param)
		{
			$id = $param['id'];
	//		$url = "http://tjenester.usrv.ubergenkom.no/api/tilskudd/{$sub_check}";
			$url = "{$param['url']}/{$param['sub_check']}";
			$extravars = array
			(
				'id'		=> $id,
			);

			$url .= '?' . http_build_query($extravars, null, '&');

			$post_data = array();

			$ch = curl_init();
	//		curl_setopt($ch, CURLOPT_PROXY, $proxy);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERPWD, "{$param['username']}:{$param['password']}");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			// Set The Response Format to Json
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			//set data to be posted
			if($post_data)
			{
				$post_items = array();
				foreach ( $post_data as $key => $value)
				{
					$post_items[] = "{$key}={$value}";
				}
				curl_setopt($ch, CURLOPT_POSTFIELDS, implode ('&', $post_items));
			}

			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if($httpCode != 200)
			{
				throw new Exception("HTTP-status {$httpCode}");
			}

			return json_decode($result, true);
		}


		function validate_purchase_grant( $ecodimb, $budget_amount, $order_id )
		{
			if($order_id)
			{
				$order_type = $this->bocommon->socommon->get_order_type($order_id);

				switch ($order_type)
				{
					case 'workorder':
						$location = '.project.workorder';
						$location_item_id = $order_id;
						$historylog = CreateObject('property.historylog', 'workorder');
						$history_code = 'OA';
						break;
					case 'ticket':
						$location = '.ticket';
						$location_item_id = $this->so->get_ticket_from_order($order_id);
						$historylog = CreateObject('property.historylog', 'tts');
						$history_code = 'AA';
						break;
					default:
						throw new Exception('Order type not supported');
				}
			}

			try
			{
				$check_purchase = $this->check_purchase_right($ecodimb, $budget_amount, $order_id);

			}
			catch (Exception $ex)
			{
				throw $ex;
			}

			$purchase_grant_ok = true;

			foreach ($check_purchase as $purchase_grant)
			{
				if(!$purchase_grant['is_user'] && ($purchase_grant['required'] && !$purchase_grant['approved']))
				{
					$purchase_grant_ok = false;
					phpgwapi_cache::message_set(lang('approval from %1 is required for order %2',
							$GLOBALS['phpgw']->accounts->get($purchase_grant['id'])->__toString(), $order_id),
							'error'
					);
				}
				else if( $purchase_grant['is_user'] && ( $purchase_grant['required']  && !$purchase_grant['approved']))
				{
					$action_params = array(
						'appname' => 'property',
						'location' => $location,
						'id' => $location_item_id,
						'responsible' => '',
						'responsible_type' => 'user',
						'action' => 'approval',
						'remark' => '',
						'deadline' => ''
					);

					$_account_id = $purchase_grant['id'];//$this->account

					$action_params['responsible'] = $_account_id;
					if(!execMethod('property.sopending_action.get_pending_action', $action_params))
					{
						execMethod('property.sopending_action.set_pending_action', $action_params);
					}
					execMethod('property.sopending_action.close_pending_action', $action_params);
					$historylog->add($history_code, $location_item_id, $GLOBALS['phpgw']->accounts->get($_account_id)->__toString() . "::{$budget_amount}");
					$purchase_grant_ok = true;
				}
			}
			return $purchase_grant_ok;
		}
	}