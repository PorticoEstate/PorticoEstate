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

		var $public_functions = array
		(
			'read'			=> true,
			'read_single'		=> true,
			'save'			=> true,
			'delete'		=> true,
			'check_perms'		=> true
		);

		var $soap_functions = array(
			'list' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			),
			'read' => array(
				'in'  => array('int','struct'),
				'out' => array('array')
			),
			'save' => array(
				'in'  => array('int','struct'),
				'out' => array()
			),
			'delete' => array(
				'in'  => array('int','struct'),
				'out' => array()
			)
		);

		function property_botts($session=false)
		{
			$this->so 					= CreateObject('property.sotts');
			$this->bocommon 			= & $this->so->bocommon;
			$this->historylog			= & $this->so->historylog;
			$this->config				= CreateObject('phpgwapi.config');
			$this->dateformat			= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$this->cats					= CreateObject('phpgwapi.categories');
			$this->cats->app_name		= 'property.ticket';
			$this->cats->supress_info	= true;

			$this->config->read_repository();

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$status_id				= phpgw::get_var('status_id');
			$user_id				= phpgw::get_var('user_id');
			$cat_id					= phpgw::get_var('cat_id', 'int');
			$district_id			= phpgw::get_var('district_id', 'int');
			$allrows				= phpgw::get_var('allrows', 'bool');
			$start_date				= phpgw::get_var('start_date');
			$end_date				= phpgw::get_var('end_date');

			$this->start			= $start ? $start : 0;
			$this->query			= isset($query) ? $query : $this->query;
			$this->status_id		= isset($status_id) && $status_id ? $status_id : '';
			$this->user_id			= isset($user_id) && $user_id ? $user_id : '';
			$this->sort				= isset($sort) && $sort ? $sort : '';
			$this->order			= isset($order) && $order ? $order : '';
			$this->cat_id			= isset($cat_id) && $cat_id ? $cat_id : '';
			$this->part_of_town_id	= isset($part_of_town_id) && $part_of_town_id ? $part_of_town_id : '';
			$this->district_id		= isset($district_id) && $district_id ? $district_id : '';
			$this->status			= isset($status) && $status ? $status : '';
			$this->type_id			= isset($type_id) && $type_id ? $type_id : 1;
			$this->allrows			= isset($allrows) && $allrows ? $allrows : '';
			$this->start_date		= isset($start_date) && $start_date ? $start_date : '';
			$this->end_date			= isset($end_date) && $end_date ? $end_date : '';
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

			$this->start		= isset($data['start'])?$data['start']:'';
			$this->query		= isset($data['query'])?$data['query']:'';
			$this->filter		= isset($data['filter'])?$data['filter']:'';
			$this->user_filter	= isset($data['user_filter'])?$data['user_filter']:'';
			$this->sort			= isset($data['sort'])?$data['sort']:'';
			$this->order		= isset($data['order'])?$data['order']:'';
			$this->cat_id		= isset($data['cat_id'])?$data['cat_id']:'';
			$this->district_id	= isset($data['district_id'])?$data['district_id']:'';
			$this->allrows		= isset($data['allrows'])?$data['allrows']:'';
			$this->start_date	= isset($data['start_date'])?$data['start_date']:'';
			$this->end_date		= isset($data['end_date'])?$data['end_date']:'';
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
				$status[$i]['name']=lang('Open');
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

		function _get_status_text()
		{
			$status_text = array(
				'R' => 'Re-opened',
				'X' => 'Closed',
				'O' => 'Opened',
				'A' => 'Re-assigned',
				'G' => 'Re-assigned group',
				'P' => 'Priority changed',
				'T' => 'Category changed',
				'S' => 'Subject changed',
				'B' => 'Billing rate',
				'H' => 'Billing hours',
				'F' => 'finnish date',
				'SC' => 'Status changed'
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
			if(!$selected && isset($GLOBALS['phpgw_info']['user']['preferences']['property']['prioritydefault']))
			{
				$selected = $GLOBALS['phpgw_info']['user']['preferences']['property']['prioritydefault'];
			}

			$priority_comment[1]=' - '.lang('Lowest');
			$priority_comment[5]=' - '.lang('Medium');
			$priority_comment[10]=' - '.lang('Highest');

			$priorities = array();
			for ($i=1; $i<=10; $i++)
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


		function read($start_date='',$end_date='', $external='',$dry_run = '')
		{
			$interlink 	= CreateObject('property.interlink');
			$start_date	= $this->bocommon->date_to_timestamp($start_date);
			$end_date	= $this->bocommon->date_to_timestamp($end_date);

			$tickets = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'status_id' => $this->status_id,'cat_id' => $this->cat_id,'district_id' => $this->district_id,
											'start_date'=>$start_date,'end_date'=>$end_date,
											'allrows'=>$this->allrows,'user_id' => $this->user_id,'external'=>$external, 'dry_run' => $dry_run));
			$this->total_records = $this->so->total_records;
			if(!$external)
			{
				$entity	= $this->so->get_origin_entity_type();
				$this->uicols=$this->so->uicols;
			}
			else
			{
				$entity[0]['type']='.project';
				$this->uicols[]	= lang('project');
			}

			foreach ($tickets as & $ticket)
			{
				if(!$ticket['subject'])
				{
					$ticket['subject']= $this->get_category_name($ticket['cat_id']);
				}

				$ticket['user'] = $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);

				if($ticket['assignedto'])
				{
					$ticket['assignedto'] = $GLOBALS['phpgw']->accounts->id2name($ticket['assignedto']);
				}
				else
				{
					$ticket['assignedto'] = $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);
				}

				$ticket['timestampopened'] = $GLOBALS['phpgw']->common->show_date($ticket['entry_date'],$this->dateformat);

				if($ticket['finnish_date2'])
				{
					$ticket['delay']=($ticket['finnish_date2']-$ticket['finnish_date'])/(24*3600);
					$ticket['finnish_date']=$ticket['finnish_date2'];
				}
				$ticket['finnish_date'] = (isset($ticket['finnish_date']) && $ticket['finnish_date'] ? $GLOBALS['phpgw']->common->show_date($ticket['finnish_date'],$this->dateformat):'');

				if ($ticket['status'] == 'X')
				{
					$history_values = $this->historylog->return_array(array(),array('X'),'history_timestamp','DESC',$ticket['id']);
					$ticket['timestampclosed'] = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);
				}
				if (isset($ticket['new_ticket']))
				{
					$ticket['new_ticket'] = lang('New');
				}

				if(isset($entity) && is_array($entity))
				{
					for ($j=0;$j<count($entity);$j++)
					{
						$ticket['child_date'][$j] = $interlink->get_child_date('property', '.ticket', $entity[$j]['type'], $ticket['id'], isset($entity[$j]['entity_id'])?$entity[$j]['entity_id']:'',isset($entity[$j]['cat_id'])?$entity[$j]['cat_id']:'');
					}
				}
			}
//_debug_array($tickets);
			return $tickets;
		}

		function read_single($id)
		{
			$this->so->update_view($id);

			$ticket = $this->so->read_single($id);

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


			$history_values = $this->historylog->return_array(array(),array('O'),'history_timestamp','DESC',$id);
			$ticket['timestampopened'] = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);
			// Figure out when it was opened and last closed

			$history_values = $this->historylog->return_array(array(),array('O'),'history_timestamp','ASC',$id);
			$ticket['last_opened'] = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime']);

			if($ticket['status']=='X')
			{

				$history_values = $this->historylog->return_array(array(),array('X'),'history_timestamp','DESC',$id);
				$ticket['timestampclosed']= $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);
			}

			$status_text = $this->_get_status_text();

			$ticket['status_name'] = lang($status_text[$ticket['status']]);
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
			$history_array = $this->historylog->return_array(array(),array('C'),'','',$id);
			$i=1;
			while (is_array($history_array) && list(,$value) = each($history_array))
			{
				$additional_notes[] = array
				(
					'value_count'	=> $i,
					'value_date'	=> $GLOBALS['phpgw']->common->show_date($value['datetime']),
					'value_user'	=> $value['owner'],
					'value_note'	=> stripslashes($value['new_value']),
					);
				$i++;
			}

			if(isset ($additional_notes))
			{
				return $additional_notes;
			}
		}


		function read_record_history($id)
		{
			$history_array = $this->historylog->return_array(array('C','O'),array(),'','',$id);
			$status_text = $this->_get_status_text();
			$record_history = array();
			$i=0;
			if (is_array($history_array))
			{
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
						case 'B': $type = lang('Billable rate changed'); break;
						case 'F': $type = lang('finnish date changed'); break;
						case 'IF': $type = lang('Initial finnish date'); break;
						default: break;
					}

					switch ($value['new_value'])
					{
						case 'O': $value['new_value']=lang('Opened'); break;
						case 'X': $value['new_value']=lang('Closed'); break;
						case 'I': $value['new_value']=lang('In Progress'); break; //initiated
						case 'C': $value['new_value']=lang('custom'); break; // FIXME: make configurable
						default: break;
					}

					if(strlen($value['new_value']) == 2 && substr($value['new_value'], 0, 1) == 'C') // if custom status
					{
						$type = lang('Status changed');
						$value['new_value'] = $status_text[$value['new_value']];
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
					}
					else if ($value['status'] == 'T')
					{
						$record_history[$i]['value_new_value']	= $this->get_category_name($value['new_value']);
					}
					else if (($value['status'] == 'F') || ($value['status'] =='IF'))
					{
						$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->common->show_date($value['new_value'],$this->dateformat);
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
			}
			return $record_history;
		}

		function add($ticket)
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


			$receipt = $this->so->add($ticket);

			$this->config->read_repository();

			if (isset($this->config->config_data['mailnotification']) && $this->config->config_data['mailnotification'] && isset($ticket['send_mail']) && $ticket['send_mail'])
			{
				$receipt = $this->mail_ticket($receipt['id'],$fields_updated,$receipt,$ticket['location_code']);
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

				$file = PHPGW_APP_INC . "/custom/{$entry['file_name']}";
				if ( $entry['active'] && is_file($file) )
				{
					require_once PHPGW_APP_INC . "/custom/{$entry['file_name']}";
				}
			}

			return $receipt;
		}


		function mail_ticket($id,$fields_updated,$receipt=0,$location_code='')
		{
			$this->send			= CreateObject('phpgwapi.send');

			$members = array();

			$ticket	= $this->so->read_single($id);

			if($ticket['location_code'])
			{
				$solocation 		= CreateObject('property.solocation');
				$custom = createObject('property.custom_fields');
				$location_data 		= $solocation->read_single($ticket['location_code']);

				$type_id=count(explode('-',$ticket['location_code']));
				$fm_location_cols = $custom->find('property','.location.' . $type_id, 0, '', 'ASC', 'attrib_sort', true, true);
				$i=0;
				if (isset($fm_location_cols) AND is_array($fm_location_cols))
				{
					foreach($fm_location_cols as $location_entry)
					{
						if($location_entry['lookup_form'])
						{
							$address_element[$i]['text']=$location_entry['input_text'];
							$address_element[$i]['value']=$location_data[$location_entry['column_name']];
						}
						$i++;
					}
				}
			}

			$history_values = $this->historylog->return_array(array(),array('O'),'history_timestamp','DESC',$id);
			$timestampopened = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);

			if($ticket['status']=='X')
			{
				$history_values = $this->historylog->return_array(array(),array('X'),'history_timestamp','DESC',$id);
				$timestampclosed = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);
			}

			$history_2 = $this->historylog->return_array(array('C','O'),array(),'','',$id);
			$m=count($history_2)-1;
			$ticket['status']=$history_2[$m]['status'];

		//	$status = $this->_get_status_text();

			$group_name= $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);

			// build subject
			$subject = '['.lang('Ticket').' #'.$id.'] : ' . $location_code .' ' .$this->get_category_name($ticket['cat_id']) . '; ' .$ticket['subject'];


		//	$prefs_user = $GLOBALS['phpgw']->preferences->create_email_preferences($ticket['user_id']);
			$prefs_user = $this->bocommon->create_preferences('property',$ticket['user_id']);

			$from_address=$prefs_user['email'];

	//-----------from--------

			$current_user_id=$GLOBALS['phpgw_info']['user']['account_id'];

			$current_user_firstname	= $GLOBALS['phpgw_info']['user']['firstname'];

			$current_user_lastname	=$GLOBALS['phpgw_info']['user']['lastname'];

			$current_user_name= $user_firstname . " " .$user_lastname ;

//			$current_prefs_user = $GLOBALS['phpgw']->preferences->create_email_preferences($current_user_id);
			$current_prefs_user = $this->bocommon->create_preferences('property',$current_user_id);
			$current_user_address=$current_prefs_user['email'];

			$headers = "Return-Path: <". $current_user_address .">\r\n";
			$headers .= "From: " . $current_user_name . "<" . $current_user_address .">\r\n";
			$headers .= "Bcc: " . $current_user_name . "<" . $current_user_address .">\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "MIME-Version: 1.0\r\n";

	//-----------from--------
		// build body
			$body  = '';
	//		$body .= lang('Ticket').' #'.$id."\n";
			$body .= '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php','menuaction='.'property.uitts.view&id=' . $id).'">' . lang('Ticket').' #' .$id .'</a>'."\n";
			$body .= lang('Date Opened').': '.$timestampopened."\n";
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
			$body .= lang('Opened By').': '. $ticket['user_name'] ."\n\n";
			$body .= lang('First Note Added').":\n";
			$body .= stripslashes(strip_tags($ticket['details']))."\n\n";

			/**************************************************************\
			* Display additional notes                                     *
			\**************************************************************/
			if($fields_updated)
			{
				$i=1;

				$history_array = $this->historylog->return_array(array(),array('C'),'','',$id);
				while (is_array($history_array) && list(,$value) = each($history_array))
				{
					$body .= lang('Date') . ': '.$GLOBALS['phpgw']->common->show_date($value['datetime'])."\n";
					$body .= lang('User') . ': '.$value['owner']."\n";
					$body .=lang('Note').': '. nl2br(stripslashes($value['new_value']))."\n\n";
					$i++;
				}
				$subject.= "-" .$i;
			}

			/**************************************************************\
			* Display record history                                       *
			\**************************************************************/

			if($timestampclosed)
			{
				$body .= lang('Date Closed').': '.$timestampclosed."\n\n";
			}

			if ($this->config->config_data['groupnotification'] && $ticket['group_id'])
			{
				// select group recipients
				$members  = $this->bocommon->active_group_members($ticket['group_id']);
			}

			if ($this->config->config_data['ownernotification'] && $ticket['user_id'])
			{
				// add owner to recipients
				$members[] = array('account_id' => $ticket['user_id'], 'account_name' => $GLOBALS['phpgw']->accounts->id2name($ticket['user_id']));
			}

			if ($this->config->config_data['assignednotification'] && $ticket['assignedto'])
			{
				// add assigned to recipients
				$members[] = array('account_id' => $ticket['assignedto'], 'account_name' => $GLOBALS['phpgw']->accounts->id2name($ticket['assignedto']));
			}

			$error = Array();
			$toarray = Array();
			$i=0;
			for ($i=0;$i<count($members);$i++)
			{
				if ($members[$i]['account_id'])
				{
			//		$prefs = $GLOBALS['phpgw']->preferences->create_email_preferences($members[$i]['account_id']);
					$prefs = $this->bocommon->create_preferences('property',$members[$i]['account_id']);
					if (strlen($prefs['email'])> (strlen($members[$i]['account_name'])+1))
					{
						$toarray[$prefs['email']] = $prefs['email'];
					}
					else
					{
						$receipt['error'][] = array('msg'=> lang('Your message could not be sent!'));
						$receipt['error'][] = array('msg'=>lang('This user has not defined an email address !') . ' : ' . $members[$i]['account_name']);
					}
				}
			}

			if(count($toarray) > 1)
			{
				$to = implode(',',$toarray);
			}
			else
			{
				$to = current($toarray);
			}

			$body = str_replace("\n" ,"</br>",$body);
			if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				$rc = $this->send->msg('email', $to, $subject, stripslashes($body), '', $cc, $bcc,$current_user_address,$current_user_name,'html');
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('SMTP server is not set! (admin section)'));
			}

			if (!$rc && ($this->config->config_data['groupnotification'] || $this->config->config_data['ownernotification'] || $this->config->config_data['groupnotification']))
			{
				$receipt['error'][] = array('msg'=> lang('Your message could not be sent by mail!'));
				$receipt['error'][] = array('msg'=> lang('The mail server returned'));
				$receipt['error'][] = array('msg'=> 'From :' . $current_user_name . '<' . $current_user_address .'>');
				$receipt['error'][] = array('msg'=> 'to: '.$to);
				$receipt['error'][] = array('msg'=> 'subject: '.$subject);
				$receipt['error'][] = array('msg'=> $body );
	//			$receipt['error'][] = array('msg'=> 'cc: ' . $cc);
	//			$receipt['error'][] = array('msg'=> 'bcc: '.$bcc);
				$receipt['error'][] = array('msg'=> 'group: '.$group_name);
				$receipt['error'][] = array('msg'=> 'err_code: '.$this->send->err['code']);
				$receipt['error'][] = array('msg'=> 'err_msg: '. htmlspecialchars($this->send->err['msg']));
				$receipt['error'][] = array('msg'=> 'err_desc: '. $GLOBALS['phpgw']->err['desc']);
			}

//_debug_array($receipt);
			return $receipt;
		}

		function delete($id)
		{
			$this->so->delete($id);
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

	}
