<?php
	/**
	* phpGroupWare - helpdesk: a Facilities Management System.
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
	* @package helpdesk
	* @subpackage helpdesk
 	* @version $Id: class.botts.inc.php 6728 2011-01-04 13:20:59Z sigurdne $
	*/

	/**
	 * Description
	 * @package helpdesk
	 */

	class helpdesk_botts
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $parent_cat_id;
		var $cat_id;
		var $acl_location;
		var $uicols_related = array();
		var $start_date;
		var $end_date;
		var $fields_updated = false;
		var $status_id;
		var $user_id;
		var $group_id;
		var $total_records;
		var $use_session;
		var $_simple, $_group_candidates, $_show_finnish_date, $account;

		var $public_functions = array
			(
				'read'			=> true,
				'read_single'	=> true,
				'save'			=> true,
			);

		function __construct($read_session = false)
		{
			$this->so 					= CreateObject('helpdesk.sotts');
			$this->custom				= & $this->so->custom;
			$this->bocommon 			= CreateObject('property.bocommon');
			$this->historylog			= & $this->so->historylog;
			$this->config				= CreateObject('phpgwapi.config','helpdesk');
			$this->dateformat			= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$this->cats					= CreateObject('phpgwapi.categories', -1, 'helpdesk', '.ticket');
			$this->cats->supress_info	= true;
			$this->acl_location			= $this->so->acl_location;
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->config->read();

			if ($read_session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}
			$this->start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$this->query = phpgw::get_var('query');
			$this->sort = phpgw::get_var('sort');
			$this->order = phpgw::get_var('order');
			$this->status_id = phpgw::get_var('status_id', 'string');
			$user_id = phpgw::get_var('user_id', 'int');
			$group_id = phpgw::get_var('group_id', 'int');

			if(isset($_POST))
			{
				$this->user_id	= $user_id;
			}
			if(isset($_REQUEST['group_id']))
			{
				$this->group_id	= $group_id;
			}

			$this->reported_by = phpgw::get_var('reported_by', 'int');
			$this->cat_id = phpgw::get_var('cat_id', 'int');
			if(!$this->cat_id && !empty($_POST['values']['cat_id']))
			{
				$this->cat_id = (int)$_POST['values']['cat_id'];
			}
			$this->parent_cat_id = phpgw::get_var('parent_cat_id', 'int');
			$this->allrows = phpgw::get_var('allrows', 'bool');
			$this->start_date = phpgw::get_var('filter_start_date', 'string');
			$this->end_date = phpgw::get_var('filter_end_date', 'string');

			$default_interface = isset($this->config->config_data['tts_default_interface']) ? $this->config->config_data['tts_default_interface'] : '';

			/*
			 * Inverted logic
			 */
			if($default_interface == 'simplified')
			{
				$this->_simple = true;
			}

			$user_groups =  $GLOBALS['phpgw']->accounts->membership($this->account);
			$simple_group = isset($this->config->config_data['fmttssimple_group']) ? $this->config->config_data['fmttssimple_group'] : array();
			foreach ($user_groups as $group => $dummy)
			{
				if (in_array($group, $simple_group))
				{
					if($default_interface == 'simplified')
					{
						$this->_simple = false;
					}
					else
					{
						$this->_simple = true;
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
						$this->_group_candidates[] = $group_candidate;
					}
				}
			}

			reset($user_groups);

			foreach ( $user_groups as $group => $dummy)
			{
				if ( in_array($group, $this->_group_candidates))
				{
					$this->_simple = false;
					break;
				}
			}

			reset($user_groups);
			$group_finnish_date = isset($this->config->config_data['fmtts_group_finnish_date']) ? $this->config->config_data['fmtts_group_finnish_date'] : array();
			foreach ( $user_groups as $group => $dummy)
			{
				if ( in_array($group, $group_finnish_date))
				{
					$this->_show_finnish_date = true;
					break;
				}
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$data = phpgwapi_cache::session_set('helpdesk', 'session_data',$data);
			}
		}

		function read_sessiondata()
		{
			$data = phpgwapi_cache::session_get('helpdesk', 'session_data');

			if(empty($data) || !is_array($data))
			{
				return;
			}

			$this->user_id	= $data['user_id'];
			$this->group_id	= $data['group_id'];
		}

		function column_list( $selected = array() )
		{
			if (!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns'] : '';
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

			$columns['entry_time'] = array(
				'id' => 'entry_time',
				'name' => lang('entry time'),
			);

			$columns['modified_date'] = array(
				'id' => 'modified_date',
				'name' => lang('modified date'),
//					'sortable'	=> true
			);

			$columns['modified_time'] = array(
				'id' => 'modified_time',
				'name' => lang('modified time'),
			);
			$columns['status'] = array(
				'id' => 'status',
				'name' => lang('status')
			);
			$columns['user'] = array(
				'id' => 'user',
				'name' => lang('user')
			);
			$columns['assignedto'] = array
				(
				'id' => 'assignedto',
				'name' => lang('assigned to')
			);
			$columns['category'] = array
				(
				'id' => 'category',
				'name' => lang('category')
			);

			$columns['billable_hours'] = array(
				'id' => 'billable_hours',
				'name' => lang('billable hours')
			);

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
				$columns['finnish_date'] = array(
					'id' => 'finnish_date',
					'name' => lang('finnish_date')
				);
				$columns['delay'] = array(
					'id' => 'delay',
					'name' => lang('delay')
				);
			}

			$custom_cols = $this->get_custom_cols();

			foreach ($custom_cols as $custom_col)
			{
				$columns[$custom_col['column_name']] = array(
					'id' => $custom_col['column_name'],
					'name' => $custom_col['input_text'],
					'datatype' => $custom_col['datatype'],
				);
				if (($custom_col['datatype'] == 'LB' || $custom_col['datatype'] == 'CH' || $custom_col['datatype'] == 'R') && $custom_col['choice'])
				{
					$this->custom_filters[] = $custom_col['column_name'];
				}
			}

			$columns['details'] = array
				(
				'id' => 'details',
				'name' => lang('details')
			);
			$columns['external_origin_email'] = array
				(
				'id' => 'external_origin_email',
				'name' => lang('external origin email')
			);

			return $columns;
		}

		function get_custom_cols()
		{
			static $custom_cols = array();

			if ($custom_cols)
			{
				return $custom_cols;
			}
			$custom_cols = $this->custom->find('helpdesk', '.ticket', 0, '', 'ASC', 'attrib_sort', true, true);
			return $custom_cols;
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

		function get_status_list($selected = '')
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
				'O' => lang('Opened'),
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
				'AC'=> lang('actual cost changed'),
				'FW'=> lang('ticket is forwarded'),
			);

			$custom_status	= $this->so->get_custom_status();
			foreach($custom_status as $custom)
			{
				$status_text["C{$custom['id']}"] = $custom['name'];
			}

			return $status_text;
		}


		function get_priority_list($selected='')
		{

			$prioritylevels = isset($this->config->config_data['prioritylevels']) && $this->config->config_data['prioritylevels'] ? $this->config->config_data['prioritylevels'] : 3;

			if(!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['prioritydefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['prioritydefault'] : $prioritylevels;
			}

			$priority_comment[$prioritylevels]=' - '.lang('Lowest');
			//			$priority_comment[2]=' - '.lang('Medium');
			$priority_comment[1]=' - '.lang('Highest');

			$priorities = array();
			for ($i=1; $i<= $prioritylevels; $i++)
			{
				$priorities[$i]['id'] =$i;
				$priorities[$i]['name'] =$i . (isset($priority_comment[$i])?$priority_comment[$i]:'');
			}

			return $this->bocommon->select_list($selected,$priorities);
		}

		function get_category_name($cat_id)
		{
			$category = $this->cats->return_single($cat_id);
			return $category[0]['name'];
		}

		function get_category_path($cat_id)
		{
			$path_array = $this->cats->get_path($cat_id);

			$path = array();
			foreach ($path_array as $entry)
			{
				$path[] = $entry['name'];
			}

			return implode('::', $path);
		}

		function get_origin_entity_type()
		{
			$related = $this->so->get_origin_entity_type();
			$this->uicols_related = $this->so->uicols_related;
			return $related;
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

		function read( $data = array() )
		{
			static $parent_category_name = array();
			static $category_name = array();
			static $account = array();
			static $vendor_cache = array();

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

			$selected_columns = !empty($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['ticket_columns'] : array();

			$custom_status = $this->so->get_custom_status();
			$closed_status = array('X');
			foreach ($custom_status as $custom)
			{
				if ($custom['closed'])
				{
					$closed_status[] = "C{$custom['id']}";
				}
			}

			foreach ($tickets as & $ticket)
			{
				if(in_array('details', $selected_columns))
				{
					$i = 1;
					$ticket['details'] = "#{$i}: {$ticket['details']}";
					$additional_notes = $this->read_additional_notes((int)$ticket['id'] );
					foreach ($additional_notes as $additional_note)
					{
						if($additional_note['value_publish'])
						{
							$i++;
							$ticket['details'] .= "<br/><br/>#$i: {$additional_note['value_note']}";
						}
					}
				}

				if (!isset($category_name[$ticket['cat_id']]))
				{
					$category_name[$ticket['cat_id']] = $this->get_category_name($ticket['cat_id']);
				}

				$ticket['category'] = $category_name[$ticket['cat_id']];


				if (!isset($parent_category_name[$ticket['cat_id']]))
				{
					$cat_path = $this->cats->get_path($ticket['cat_id']);
					if(count($cat_path) > 1)
					{
						$parent_cat_id = $cat_path[0]['id'];
					}

					$parent_category_name[$ticket['cat_id']] = $this->get_category_name($parent_cat_id);
				}

				if(isset($parent_category_name[$ticket['cat_id']]))
				{
					$ticket['parent_category'] = $parent_category_name[$ticket['cat_id']];
				}

				if (!$ticket['subject'])
				{
					$ticket['subject'] = $category_name[$ticket['cat_id']];
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

				$ticket['entry_time'] = $GLOBALS['phpgw']->common->show_date($ticket['entry_date'], 'H:i:s');
				$ticket['entry_date'] = $GLOBALS['phpgw']->common->show_date($ticket['entry_date'], $this->dateformat);
				$ticket['modified_time'] = $GLOBALS['phpgw']->common->show_date($ticket['modified_date'], 'H:i:s');
				$ticket['modified_date'] = $GLOBALS['phpgw']->common->show_date($ticket['modified_date'], $this->dateformat);
				if ($ticket['finnish_date2'])
				{
					$ticket['delay'] = round(($ticket['finnish_date2'] - $ticket['finnish_date']) / (24 * 3600));
					$ticket['finnish_date'] = $ticket['finnish_date2'];
				}
				$ticket['finnish_date'] = (isset($ticket['finnish_date']) && $ticket['finnish_date'] ? $GLOBALS['phpgw']->common->show_date($ticket['finnish_date'], $this->dateformat) : '');

				if ($ticket['new_ticket'])
				{
					$ticket['new_ticket'] = '*';
				}
			}
			return $tickets;
		}

		function read_single($id, $values = array(), $view = false )
		{
			$this->so->update_view($id);

			$values['attributes'] = $this->get_custom_cols();
			if(!$ticket = $this->so->read_single($id, $values))
			{
				return array();
			}
			$ticket = $this->custom->prepare($ticket, 'helpdesk', '.ticket', $view);

			$ticket['user_lid'] = $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
			$ticket['group_lid'] = $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);

			$interlink 	= CreateObject('property.interlink');
			$ticket['origin'] = $interlink->get_relation('helpdesk', '.ticket', $id, 'origin');
			$ticket['target'] = $interlink->get_relation('helpdesk', '.ticket', $id, 'target');
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
				$solocation 	= CreateObject('helpdesk.solocation');
				$ticket['location_data'] = $solocation->read_single($ticket['location_code']);
			}
			//_debug_array($ticket['location_data']);
			if($ticket['p_num'])
			{
				$soadmin_entity	= CreateObject('helpdesk.soadmin_entity');
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


			$history_values = $this->historylog->return_array(array(),array('O'),'history_timestamp','DESC',$id);
			$ticket['timestamp'] = $history_values[0]['datetime'];
			$ticket['entry_date'] = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);
			// Figure out when it was opened and last closed

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
				'string' => "/helpdesk/{$id}",
				'checksubdirs'	=> false,
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
			$history_array = $this->historylog->return_array(array(),array('C'),'history_timestamp','ASC',$id);

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
				$record_history[$i]['value_id']	= $i+1;
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
				case 'FW': $type = lang('ticket is forwarded'); break;
				case 'M':
					$type = lang('Sent by email to');
					$this->order_sent_adress = $value['new_value']; // in case we want to resend the order as an reminder
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
						$record_history[$i]['value_old_value'] = $value['old_value'] ? $GLOBALS['phpgw']->accounts->id2name($value['old_value']) : '';
					}
					else
					{
						$record_history[$i]['value_new_value']	= lang('None');
						$record_history[$i]['value_old_value']	= lang('None');
					}
				}
				else if ($value['status'] == 'T')
				{
					$record_history[$i]['value_new_value']	= $this->get_category_path($value['new_value']);
					$record_history[$i]['value_old_value']	= $this->get_category_path($value['old_value']);
				}
				else if ($value['status'] == 'FW')
				{
					$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->get($value['new_value'])->__toString();
					$record_history[$i]['value_old_value']	= $GLOBALS['phpgw']->accounts->get($value['old_value'])->__toString();
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
		 * 	$data = array
		 * 	(
		 * 		'assignedto' 		=> $assignedto,
		 * 		'group_id' 			=> $group_id,
		 *		'origin' 			=> $location_id,
		 * 		'origin_id'			=> $location_item_id,
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

			$group_or_user = '';
			$assignedto = $data['assignedto'];
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
				'send_mail'		=> true,
				'external_ticket_id' => !empty($data['external_ticket_id']) ? $data['external_ticket_id'] : null,
				'external_origin_email' => !empty($data['external_origin_email']) ? $data['external_origin_email'] : null,
			);

			$result = $this->add($ticket);

			// Files
			$file_input_name = isset($data['file_input_name']) && $data['file_input_name'] ? $data['file_input_name'] : 'file';

			$file_name = @str_replace(' ', '_', $_FILES[$file_input_name]['name']);
			if (!$cancel_attachment && $file_name && $result['id'])
			{
				$bofiles = CreateObject('property.bofiles', '/helpdesk');
				$to_file = "{$bofiles->fakebase}/{$result['id']}/{$file_name}";

				if ($bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'checksubdirs'	=> false,
						'relatives' => array(RELATIVE_NONE)
					)))
				{
					$msglog['error'][] = array('msg' => lang('This file already exists !'));
				}
				else
				{
					$bofiles->create_document_dir("{$result['id']}");
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
			$data['finnish_date'] = $this->bocommon->date_to_timestamp($data['finnish_date']);

			if ($values_attribute && is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			$criteria = array
				(
				'appname' => 'helpdesk',
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

				$file = PHPGW_SERVER_ROOT . "/helpdesk/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && $entry['pre_commit'])
				{
					require $file;
				}
			}

			if( !empty($data['set_user_alternative_lid']))
			{
				$data['set_user_id'] = $GLOBALS['phpgw']->accounts->name2id($data['set_user_alternative_lid']);
			}

			$receipt = $this->so->add($data, $values_attribute);

			$this->config->read();

			if (!empty($data['send_mail']) || !empty($this->config->config_data['mailnotification'] ))
			{
				if(!empty($data['send_mail']))
				{
					$_send_mail = true;
				}
				else if ($this->_simple)
				{
					$_send_mail = true;
				}
				else
				{
					$_send_mail = false;
				}
				$receipt_mail = $this->mail_ticket($receipt['id'], false, $receipt, false, $_send_mail);
			}

			reset($custom_functions);
			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/helpdesk/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
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


		function get_address_element($location_code = '')
		{
			$address_element = array();
			if($location_code)
			{
				$solocation 		= CreateObject('helpdesk.solocation');
				$custom = createObject('helpdesk.custom_fields');
				$location_data 		= $solocation->read_single($location_code);

				$location_types = execMethod('helpdesk.soadmin_location.select_location_type');
				$type_id=count(explode('-',$location_code));

				for ($i=1; $i<$type_id+1; $i++)
				{
					$address_element[] = array
						(
							'text' => $location_types[($i-1)]['name'],
							'value'=> $location_data["loc{$i}"] . '  ' . $location_data["loc{$i}_name"]
						);
				}

				$fm_location_cols = $custom->find('helpdesk','.location.' . $type_id, 0, '', 'ASC', 'attrib_sort', true, true);
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

		function mail_ticket($id, $fields_updated, $receipt = array(), $get_message = false, $send_mail = false)
		{
			//No message on assignment
			if(!$get_message && is_array($fields_updated) && count($fields_updated) == 1 && in_array('assignedto', $fields_updated))
			{
				return;
			}

			$log_recipients = array();
			$this->send			= CreateObject('phpgwapi.send');

			$ticket	= $this->read_single($id);

			if($ticket['status']=='X')
			{
				$history_values = $this->historylog->return_array(array(),array('X'),'history_timestamp','DESC',$id);
				$timestampclosed = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime']);
			}

			$history_values = $this->historylog->return_array(array(),array('O'),'history_timestamp','DESC',$id);
			$entry_date = $GLOBALS['phpgw']->common->show_date($ticket['timestamp']);

			$status_text = $this->get_status_text();

			$group_name= $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);

			// build subject
			$subject = '['.lang('Ticket').' #'.$id.'] : ' . $this->get_category_name($ticket['cat_id']) . '; ' .$ticket['subject'];

			//-----------from--------

			$current_prefs_user = $this->bocommon->create_preferences('common',$GLOBALS['phpgw_info']['user']['account_id']);

			if(!$current_prefs_user['email'])
			{
				$email_domain = !empty($GLOBALS['phpgw_info']['server']['email_domain']) ? $GLOBALS['phpgw_info']['server']['email_domain'] : 'bergen.kommune.no';
				$current_prefs_user['email'] = "{$GLOBALS['phpgw_info']['user']['account_lid']}@{$email_domain}";
			}

			$current_user_address = "{$GLOBALS['phpgw_info']['user']['fullname']}<{$current_prefs_user['email']}>";

			//-----------from--------
			// build body
			$request_scheme = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http' : 'https';

			if($request_scheme == 'https')
			{
				$GLOBALS['phpgw_info']['server']['enforce_ssl'] = true;
			}

			$link_text = lang('Ticket') . ' #' . $id ;

			$messages_sendt = $this->historylog->return_array(array(),array('M'),'history_timestamp','DESC',$id);
			$_additional_notes = $this->read_additional_notes($id);

			$notes = array(
				array(
					'value_id' => '', //not from historytable
					'value_count' => 1,
					'value_date' => $GLOBALS['phpgw']->common->show_date($ticket['timestamp']),
					'value_user' => $ticket['reverse_id']? $ticket['reverse_name'] : $ticket['user_name'],
					'value_note' => $ticket['details'],
					'value_publish' => $ticket['publish_note']
				)
			);

			$_additional_notes = array_merge($notes, $_additional_notes);

			$additional_notes = array();

			if ($this->_simple)
			{
				$i = 1;
				foreach ($_additional_notes as $note)
				{
					if ($note['value_publish'])
					{
						$note['value_count'] = $i++;
						$additional_notes[] = $note;
					}
				}
			}
			else
			{
				$i = 0;
				$j = 1;
				foreach ($_additional_notes as $note)
				{
					if ($note['value_publish'])
					{
						$i++;
						$j = 1;
						$additional_notes[] = $note;
					}
					else//verify this...
					{
						if($i)
						{
							$j++;
						}
						$note['value_count'] = "{$i}.{$j}";
						$note['value_note'] = "";
						$note['value_user'] = "";
						$additional_notes[] = $note;
					}
					$i = max(array(1, $i));
				}
			}


			$num_updates = count($additional_notes);

			$category_path = $this->cats->get_path($ticket['cat_id']);
			$category_parent = $category_path[0]['id'];

			$cat_respond_messages = createObject('helpdesk.socat_respond_messages')->read();

			$config_new_message = $cat_respond_messages[$category_parent]['new_message'] ? $cat_respond_messages[$category_parent]['new_message'] : $this->config->config_data['new_message'];
			$config_set_user_message = $cat_respond_messages[$category_parent]['set_user_message'] ? $cat_respond_messages[$category_parent]['set_user_message'] : $this->config->config_data['set_user_message'];
			$config_update_message = $cat_respond_messages[$category_parent]['update_message'] ? $cat_respond_messages[$category_parent]['update_message'] : $this->config->config_data['update_message'];
			$config_close_message = $cat_respond_messages[$category_parent]['close_message'] ? $cat_respond_messages[$category_parent]['close_message'] : $this->config->config_data['close_message'];

			//New message
			if(!$get_message && !empty($config_new_message))
			{
				$link_text = $config_new_message;
				$link_text = nl2br(str_replace(array('__ID__'), array($id), $link_text));
			}

			$set_user_id = false;
			if(!$get_message && !empty($config_set_user_message) && ($_POST['values']['set_user_alternative_lid'] || ($ticket['user_id'] != $ticket['reverse_id'] && $send_mail)))
			{
				$set_user_id =  true;
				$link_text = $config_set_user_message;
				$link_text = nl2br(str_replace(array('__ID__'), array($id), $link_text));
			}

			// Normal update message
			if(!$get_message && !empty($config_update_message) && $messages_sendt && !$set_user_id)
			{
				$link_text = $config_update_message;
				$link_text = nl2br(str_replace(array('__ID__', '__#__'), array($id, $num_updates), $link_text));
			}

			$status_closed = array('X' => true);
			$custom_status	= $this->so->get_custom_status();
			foreach($custom_status as $custom)
			{
				$status_closed["C{$custom['id']}"] = !!$custom['closed'];
			}


			//Message when ticket is closed
			if(!$get_message && !empty($config_close_message) && $status_closed[$ticket['status']])
			{
				$link_text = $config_close_message;
				$link_text = nl2br(str_replace(array('__ID__', '__#__'), array($id, $num_updates), $link_text));
			}

			//message when closed;
			$link_to_ticket =  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uitts.view',
					'id' => $id), false, true);

			preg_match_all("/\[[^\]\]]*\]\]/", $link_text, $matches);

			if (empty($matches[0][0]))
			{
				$body = "<a href ='{$link_to_ticket}'>{$link_text}</a>\n";
			}
			else
			{
				$replace = trim($matches[0][0], '[]');
				$link_to_ticket_text = "<a href ='{$link_to_ticket}'>{$replace}</a>";
				$body = str_ireplace($matches[0][0], $link_to_ticket_text, $link_text);
			}

			$body .= "<p>&nbsp;</p>"
				. "<table class='overview'>";

			if($ticket['on_behalf_of_name'])
			{
				$body .= '<tr><td>'. lang('on behalf of')."</td><td>:&nbsp;{$ticket['on_behalf_of_name']}</td></tr>";
			}

			$body .= '<tr><td>'. lang('Date Opened').'</td><td>:&nbsp;'.$entry_date."</td></tr>";
			$body .= '<tr><td>'. lang('status').'</td><td>:&nbsp;<b>'.$status_text[$ticket['status']]."</b></td></tr>";
			$body .= '<tr><td>'. lang('Category').'</td><td>:&nbsp;'. $this->get_category_name($ticket['cat_id']) ."</td></tr>";

			if($ticket['assignedto'])
			{
				$_assignedto = $GLOBALS['phpgw']->accounts->id2name($ticket['assignedto']);
			}
			else if($ticket['group_id'])
			{
				$_assignedto = str_replace('_', ' ', $group_name);
			}

			if($_assignedto)
			{
				$body .= '<tr><td>'. lang('Assigned To').'</td><td>:&nbsp;'.$_assignedto."</td></tr>";
			}

			if(empty($this->config->config_data['disable_priority']))
			{
				$body .= '<tr><td>'. lang('Priority').'</td><td>:&nbsp;'.$ticket['priority']."</td></tr>";
			}

			if($ticket['reverse_id'])
			{
				$user_name = $ticket['reverse_name'];
				$body .= '<tr><td>'. lang('Owned By').'</td><td>:&nbsp;'. $ticket['user_name'] ."</td></tr>";
			}
			else
			{
				$user_name = $ticket['user_name'];
			}

			$body .= '<tr><td>'. lang('Opened By').'</td><td>:&nbsp;'. $user_name ."</td></tr>";

			$notify_list = execMethod('property.notify.read', array
				(
				'location_id' => $GLOBALS['phpgw']->locations->get_id('helpdesk', $this->acl_location),
				'location_item_id' => $id
				)
			);

			$notify_arr = array();
			foreach ($notify_list as $entry)
			{
				if ($entry['is_active'])
				{
					$notify_arr[] = "{$entry['first_name']} {$entry['last_name']}";
				}
			}
			unset($entry);
			if($notify_arr)
			{
				$body .= '<tr><td>'. lang('notify').'</td><td>:&nbsp;'. implode(";", $notify_arr) ."</td></tr>";
			}

			if($timestampclosed)
			{
				$body .= '<tr><td>'. lang('Date Closed').'</td><td>:&nbsp;'.$timestampclosed."</td></tr>";
			}

			if(!empty($ticket['attributes']))
			{
				$custom		= createObject('property.custom_fields');
				$location_id = $GLOBALS['phpgw']->locations->get_id('helpdesk', '.ticket');

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
					$body .= '<tr><td>'. $attribute['input_text'].'</td><td>:&nbsp;'.$custom_value."</td></tr>";
				}
			}

			$body .= '</table>';


			if($get_message || !empty($cat_respond_messages[$category_parent]['include_content']))
			{
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

			//	$table_content .= "<tr><td style='vertical-align:top'>{$i}</td><td style='vertical-align:top'>{$entry_date}</td><td style='vertical-align:top'>{$user_name}</td><td style='white-space: pre-line'>{$ticket['details']}</td></tr>";

				foreach ($additional_notes as $value)
				{
					$table_content .= "<tr><td style='vertical-align:top'>{$value['value_count']}</td><td style='vertical-align:top'>{$value['value_date']}</td><td style='vertical-align:top'>{$value['value_user']}</td><td style='white-space: pre-line'>{$value['value_note']}</td></tr>";
				}
				$body.= "<br/><table class='details'>{$table_content}</table>";
				$subject .= "::{$i}";
			}

			if($get_message)
			{
				return array('subject' => $subject, 'body' => $body);
			}

			$html = <<<HTML
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>{$subject}</title>
		<style>

			html {
				font-family: arial;
				}

			.overview {
			  width: 100%;
			  border: 1px solid black;
			  border-collapse: collapse;
			}
			.overview th {
			  background: darkblue;
			  color: white;
			}
			.overview td,
			.overview th {
			  border: 1px solid black;
			  text-align: left;
			  padding: 5px 10px;
			}

			.details {
			  width: 100%;
			  border: 1px solid black;
			  border-collapse: collapse;
			}
			.details th {
			  background: darkblue;
			  color: white;
			}
			.details td,
			.details th {
			  border: 1px solid black;
			  text-align: left;
			  padding: 5px 10px;
			}
			.details tr:nth-child(even) {
			  background: lightblue;
			}


			@page {
			size: A4;
			}

			#order_deadline{
				width: 800px;
				border:0px solid transparent;
			}

			#order_deadline td{
				border:0px solid transparent;
			}
			@media print {
			li {page-break-inside: avoid;}
			h1, h2, h3, h4, h5 {
			page-break-after: avoid;
			}

			table, figure {
			page-break-inside: avoid;
			}
			}


			@page:left{
			@bottom-left {
			content: "Page " counter(page) " of " counter(pages);
			}
			}
			@media print
			{
				.btn
				{
					display: none !important;
				}
			}

			.btn{
			background: none repeat scroll 0 0 #2647A0;
			color: #FFFFFF;
			display: inline-block;
			margin-right: 5px;
			padding: 5px 10px;
			text-decoration: none;
			border: 1px solid #173073;
			cursor: pointer;
			}

			ul{
			list-style: none outside none;
			}

			li{
			list-style: none outside none;
			}

			li.list_item ol li{
			list-style: decimal;
			}

			ul.groups li {
			padding: 3px 0;
			}

			ul.groups li.odd{
			background: none repeat scroll 0 0 #DBE7F5;
			}

			ul.groups h3 {
			font-size: 18px;
			margin: 0 0 5px;
			}

		</style>
	</head>
	<body>
		<div style='width: 800px;'>
			{$body}
		</div>
	</body>
</html>
HTML;

			$members = array();

			if( isset($this->config->config_data['groupnotification']) && $this->config->config_data['groupnotification']==1 && $ticket['group_id'] )
			{
//				$log_recipients[] = $group_name;
				$members_gross = $GLOBALS['phpgw']->accounts->member($ticket['group_id'], true);
				foreach($members_gross as $user)
				{
					$members[$user['account_id']] = $user['account_name'];
				}
				unset($members_gross);
			}

			$GLOBALS['phpgw']->preferences->set_account_id($ticket['user_id'], true);
			if( (isset($GLOBALS['phpgw']->preferences->data['helpdesk']['tts_notify_me'])
					&& ($GLOBALS['phpgw']->preferences->data['helpdesk']['tts_notify_me'] == 1)
				)
				|| ($this->config->config_data['ownernotification'] && $ticket['user_id'])
				|| ($ticket['user_id'] && $send_mail)
				|| ($set_user_id)
				)
			{
				// add owner to recipients
				$members[$ticket['user_id']] = $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
//				$log_recipients[] = $GLOBALS['phpgw']->accounts->get($ticket['user_id'])->__toString();
			}

			$GLOBALS['phpgw']->preferences->set_account_id($ticket['assignedto'], true);
			if( (isset($GLOBALS['phpgw']->preferences->data['helpdesk']['tts_notify_me'])
					&& ($GLOBALS['phpgw']->preferences->data['helpdesk']['tts_notify_me'] == 1)
				)
				|| ($this->config->config_data['assignednotification'] && $ticket['assignedto'])
			)
			{
				// add assigned to recipients
				$members[$ticket['assignedto']] = $GLOBALS['phpgw']->accounts->id2name($ticket['assignedto']);
//				$log_recipients[] = $GLOBALS['phpgw']->accounts->get($ticket['assignedto'])->__toString();
			}

			$error = array();
			$toarray = array();

			$validator = CreateObject('phpgwapi.EmailAddressValidator');

			foreach($members as $account_id => $account_name)
			{

				if($account_id == $this->account)
				{
					continue;
				}

				$log_recipients[] = $GLOBALS['phpgw']->accounts->get($account_id)->__toString();

				$prefs = $this->bocommon->create_preferences('common',$account_id);
				if(!isset($prefs['tts_notify_me'])	|| $prefs['tts_notify_me'] == 1 || $ticket['reverse_id'])
				{
					$account_lid = $GLOBALS['phpgw']->accounts->get($account_id)->lid;
					if ($validator->check_email_address($account_lid))
					{
						$prefs['email'] = $account_lid;
					}

					/**
					 * Calculate email from username
					 */
					if(!$prefs['email'])
					{
						$email_domain = !empty($GLOBALS['phpgw_info']['server']['email_domain']) ? $GLOBALS['phpgw_info']['server']['email_domain'] : 'bergen.kommune.no';
						$prefs['email'] = "{$account_lid}@{$email_domain}";
					}
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
						$receipt['error'][] = array('msg'=> lang('Your message could not be sent!'));
						$receipt['error'][] = array('msg'=>lang('This user has not defined an email address !') . ' : ' . $account_name);
					}
				}
			}

			if(!$send_mail)
			{
				$notify_list = array();
			}

			if (isset($GLOBALS['phpgw_info']['user']['apps']['sms']))
			{

				$sms_text = "{$subject}. \r\n{$GLOBALS['phpgw_info']['user']['fullname']} \r\n{$GLOBALS['phpgw_info']['user']['preferences']['common']['email']}";
				$sms = CreateObject('sms.sms');

				foreach ($notify_list as $entry)
				{
					if($entry['account_id'] == $this->account)
					{
						continue;
					}

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
				if($entry['account_id'] == $this->account)
				{
					continue;
				}

				/**
				 * Calculate email from username
				 */
				if ($validator->check_email_address($entry['account_lid']))
				{
					$entry['email'] = $entry['account_lid'];
				}

				if(!$entry['email'])
				{
					$email_domain = !empty($GLOBALS['phpgw_info']['server']['email_domain']) ? $GLOBALS['phpgw_info']['server']['email_domain'] : 'bergen.kommune.no';

					$entry['email'] = "{$entry['account_lid']}@{$email_domain}";
				}

				if ($entry['is_active'] && $entry['notification_method'] == 'email' && $entry['email'])
				{
					$toarray[] = "{$entry['first_name']} {$entry['last_name']}<{$entry['email']}>";
					$log_recipients[] = "{$entry['first_name']} {$entry['last_name']}";
				}
			}
			unset($entry);


			if(!empty($this->config->config_data['from_email']))
			{
				$from_address = $this->config->config_data['from_email'];
				$from_address_arr = explode('<', $from_address);
				if(!empty($from_address_arr[1]))
				{
					$from_name = trim($from_address_arr[0]);
				}
				else
				{
					$from_name = 'NoReply';
				}
			}
			else
			{
				$from_address = $current_user_address;
				$from_name = $GLOBALS['phpgw_info']['user']['fullname'];			
			}

			$rc = false;
			if($toarray)
			{
				$to = implode(';',$toarray);
				$cc = '';
				$bcc = '';//implode(';',$toarray);

				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					try
					{
						$rc = $this->send->msg('email', $to, $subject, $html, '', $cc, $bcc,$from_address, $from_name,'html');
					}
					catch (Exception $e)
					{
						$receipt['error'][] = array('msg' => $e->getMessage());
					}
				}
				else
				{
					$receipt['error'][] = array('msg'=>lang('SMTP server is not set! (admin section)'));
				}
			}

			if ($rc && $log_recipients)
			{
				$this->historylog->add('M', $id, implode(';', array_unique($log_recipients)));
			}

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

		public function take_over($id = 0)
		{
			$receipt 	= $this->so->take_over($id);
			$this->fields_updated = $this->so->fields_updated;
			return $receipt;
		}
		public function update_status($data, $id = 0)
		{
			$receipt 	= $this->so->update_status($data, $id);
			$this->fields_updated = $this->so->fields_updated;
			return $receipt;
		}

		public function update_priority( $data, $id = 0 )
		{
			$receipt = $this->so->update_priority($data, $id);
			$this->fields_updated = $this->so->fields_updated;
			return $receipt;
		}

		public function update_ticket( &$data, $id, $receipt = array(), $values_attribute = array() , $simple = false)
		{
			if ($values_attribute && is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			$criteria = array
				(
				'appname' => 'helpdesk',
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

				$file = PHPGW_SERVER_ROOT . "/helpdesk/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && $entry['pre_commit'])
				{
					require $file;
				}
			}

			$receipt = $this->so->update_ticket($data, $id, $receipt, $values_attribute, $simple);
			$this->fields_updated = $this->so->fields_updated;

			reset($custom_functions);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/helpdesk/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'] &&  !$entry['ajax'])
				{
					require $file;
				}
			}

			return $receipt;
		}

		public function get_reported_by( $selected = 0 )
		{
			$values = $this->so->get_reported_by($this->parent_cat_id);

			foreach ($values as &$entry)
			{
				$entry['selected'] = $entry['id'] == $selected ? 1 : 0;
			}
			return $values;
		}
		public function get_attributes( $values )
		{
			$values['attributes'] = $this->get_custom_cols();
			$values = $this->custom->prepare($values, 'helpdesk', '.ticket', false);
			return $values;
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

		public function get_assigned_groups2($selected)
		{
			return $this->so->get_assigned_groups2($selected);
		}

		function reset_views($id)
		{
			return $this->so->reset_views($id);
		}
	}
