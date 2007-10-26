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
 	* @version $Id: class.botts.inc.php,v 1.38 2007/10/14 12:18:52 sigurdne Exp $
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
			'read'			=> True,
			'read_single'		=> True,
			'save'			=> True,
			'delete'		=> True,
			'check_perms'		=> True
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

		function property_botts($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 			= CreateObject('property.sotts');
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->historylog	= CreateObject('property.historylog','tts');
			$this->config		= CreateObject('phpgwapi.config');
			$this->config->read_repository();
			$this->dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->vfs 			= CreateObject('phpgwapi.vfs');
			$this->rootdir 		= $this->vfs->basedir;
			$this->fakebase 	= $this->vfs->fakebase;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$user_filter	= phpgw::get_var('user_filter');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$district_id	= phpgw::get_var('district_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$start_date	= phpgw::get_var('start_date');
			$end_date	= phpgw::get_var('end_date');

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(array_key_exists('query',$_POST) || array_key_exists('query',$_GET))
			{
				$this->query = $query;
			}
			if(array_key_exists('filter',$_POST))
			{
				$this->filter = $filter;
			}
			if(array_key_exists('user_filter',$_POST))
			{
				$this->user_filter = $user_filter;
			}
			if(array_key_exists('sort',$_POST) || array_key_exists('sort',$_GET))
			{
				$this->sort = $sort;
			}
			if(array_key_exists('order',$_POST) || array_key_exists('order',$_GET))
			{
				$this->order = $order;
			}
			if(array_key_exists('cat_id',$_POST))
			{
				$this->cat_id = $cat_id;
			}
			if(array_key_exists('district_id',$_POST))
			{
				$this->district_id = $district_id;
			}
			if(array_key_exists('allrows',$_POST) || array_key_exists('allrows',$_GET))
			{
				$this->allrows = $allrows;
			}
			if(array_key_exists('start_date',$_POST))
			{
				$this->start_date = $start_date;
			}
			if(array_key_exists('end_date',$_POST))
			{
				$this->end_date = $end_date;
			}
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

			$this->start		= $data['start'];
			$this->query		= $data['query'];
			$this->filter		= $data['filter'];
			$this->user_filter	= $data['user_filter'];
			$this->sort		= $data['sort'];
			$this->order		= $data['order'];
			$this->cat_id		= $data['cat_id'];
			$this->district_id	= $data['district_id'];
			$this->allrows		= $data['allrows'];
			$this->start_date	= $data['start_date'];
			$this->end_date		= $data['end_date'];
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

			$filters[0]['id']='progress';
			$filters[0]['name']=lang('In progress');
			$filters[1]['id']='closed';
			$filters[1]['name']=lang('Closed');
			$filters[2]['id']='all';
			$filters[2]['name']=lang('All');

			return $this->bocommon->select_list($selected,$filters);
		}

		function get_status_list($selected)
		{

			$filters[0]['id']='X';
			$filters[0]['name']=lang('Closed');
			$filters[1]['id']='O';
			$filters[1]['name']=lang('Open');
			$filters[2]['id']='I';
			$filters[2]['name']=lang('In progress');

			return $this->bocommon->select_list($selected,$filters);
		}


		function get_priority_list($selected='')
		{
			if(!$selected && isset($GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]['prioritydefault']))
			{
				$selected = $GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]['prioritydefault'];
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
			return $this->so->get_category_name($cat_id);
		}


		function read($start_date='',$end_date='', $external='')
		{
			$start_date	= $this->bocommon->date_to_timestamp($start_date);
			$end_date	= $this->bocommon->date_to_timestamp($end_date);

			$tickets = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'district_id' => $this->district_id,
											'start_date'=>$start_date,'end_date'=>$end_date,
											'allrows'=>$this->allrows,'user_filter' => $this->user_filter,'external'=>$external));
			$this->total_records = $this->so->total_records;
			if(!$external)
			{
				$entity	= $this->so->get_origin_entity_type();
				$this->uicols=$this->so->uicols;
			}
			else
			{
				$entity[0]['type']='project';			
				$this->uicols[]	= lang('project');
			}
			
			for ($i=0; $i<count($tickets); $i++)
			{
				if($tickets[$i]['assignedto'])
				{
					$tickets[$i]['assignedto'] = $GLOBALS['phpgw']->accounts->id2name($tickets[$i]['assignedto']);
				}
				else
				{
					$tickets[$i]['assignedto'] = $GLOBALS['phpgw']->accounts->id2name($tickets[$i]['group_id']);
				}

				$tickets[$i]['timestampopened'] = $GLOBALS['phpgw']->common->show_date($tickets[$i]['entry_date'],$this->dateformat);

				if($tickets[$i]['finnish_date2'])
				{
					$tickets[$i]['delay']=($tickets[$i]['finnish_date2']-$tickets[$i]['finnish_date'])/(24*3600);
					$tickets[$i]['finnish_date']=$tickets[$i]['finnish_date2'];
				}
				$tickets[$i]['finnish_date'] = (isset($tickets[$i]['finnish_date']) && $tickets[$i]['finnish_date'] ? $GLOBALS['phpgw']->common->show_date($tickets[$i]['finnish_date'],$this->dateformat):'');

				if ($tickets[$i]['status'] == 'X')
				{
					$history_values = $this->historylog->return_array(array(),array('X'),'history_timestamp','DESC',$tickets[$i]['id']);
					$tickets[$i]['timestampclosed'] = $GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'],$this->dateformat);
				}
				if (isset($tickets[$i]['new_ticket']))
				{
					$tickets[$i]['new_ticket'] = lang('New');
				}

				if(isset($entity) && is_array($entity))
				{
					for ($j=0;$j<count($entity);$j++)
					{
						$tickets[$i]['child_date'][$j] = $this->so->get_child_date($tickets[$i]['id'],$entity[$j]['type'],(isset($entity[$j]['entity_id'])?$entity[$j]['entity_id']:''),(isset($entity[$j]['cat_id'])?$entity[$j]['cat_id']:''));
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
				'I' => 'In progress'
			);

			$ticket['status_name'] = lang($status_text[$ticket['status']]);
			$ticket['user_lid']=$GLOBALS['phpgw']->accounts->id2name($ticket['user_id']);
			$ticket['category_name']=ucfirst($this->get_category_name($ticket['cat_id']));

			$this->vfs->override_acl = 1;

			$ticket['files'] = $this->vfs->ls (array(
			     'string' => $this->fakebase. SEP . 'fmticket' . SEP . $id,
			     'relatives' => array(RELATIVE_NONE)));

			$this->vfs->override_acl = 0;

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
					'value_note'	=> stripslashes(stripslashes($value['new_value'])),
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
			$i=0;
			while (is_array($history_array) && list(,$value) = each($history_array))
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
					case 'I': $type = lang('Status changed'); break;
					default: break;
				}

				switch ($value['status'])
				{
					case 'O': $value['new_value']=lang('Opened'); break;
					case 'X': $value['new_value']=lang('Closed'); break;
					case 'I': $value['new_value']=lang('In Progress'); break; //initiated		
					default: break;
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
			return (isset($record_history)?$record_history:'');
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

			$soadmin_custom = CreateObject('property.soadmin_custom');
			$custom_functions = $soadmin_custom->read(array('acl_location' => $this->acl_location,'allrows'=>True));

			if (isSet($custom_functions) AND is_array($custom_functions))
			{
				foreach($custom_functions as $entry)
				{
					if (is_file(PHPGW_APP_INC . SEP . 'custom' . SEP . $entry['file_name']) && $entry['active'])
					include (PHPGW_APP_INC . SEP . 'custom' . SEP . $entry['file_name']);
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
				$soadmin_location 	= CreateObject('property.soadmin_location');
				$location_data 		= $solocation->read_single($ticket['location_code']);

				$type_id=count(explode('-',$ticket['location_code']));
				$fm_location_cols = $soadmin_location->read_attrib(array('type_id'=>$type_id,'lookup_type'=>$type_id));
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

			$stat = $ticket['status'];
			$status = array(
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
				'F' => 'finnish date changed',
				'I'=> 'Status changed'
			);


			$group_name= $GLOBALS['phpgw']->accounts->id2name($ticket['group_id']);

			// build subject
			$subject = '['.lang('Ticket').' #'.$id.'] : ' . $location_code .' ' .$this->get_category_name($ticket['cat_id']) . '; ' .$ticket['subject'];


		//	$prefs_user = $GLOBALS['phpgw']->preferences->create_email_preferences($ticket['user_id']);
			$prefs_user = $this->bocommon->create_preferences($this->currentapp,$ticket['user_id']);

			$from_address=$prefs_user['email'];

	//-----------from--------

			$current_user_id=$GLOBALS['phpgw_info']['user']['account_id'];

			$current_user_firstname	= $GLOBALS['phpgw_info']['user']['firstname'];

			$current_user_lastname	=$GLOBALS['phpgw_info']['user']['lastname'];

			$current_user_name= $user_firstname . " " .$user_lastname ;

//			$current_prefs_user = $GLOBALS['phpgw']->preferences->create_email_preferences($current_user_id);
			$current_prefs_user = $this->bocommon->create_preferences($this->currentapp,$current_user_id);
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
			$body .= '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php','menuaction='.$this->currentapp.'.uitts.view&id=' . $id).'">' . lang('Ticket').' #' .$id .'</a>'."\n";
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
					$prefs = $this->bocommon->create_preferences($this->currentapp,$members[$i]['account_id']);
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

		function create_document_dir($id='')
		{
			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase. SEP . 'fmticket',
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;
				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. SEP . 'fmticket',
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. SEP . 'fmticket');
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. SEP . 'fmticket');
				}
				$this->vfs->override_acl = 0;
			}


			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase. SEP . 'fmticket' .  SEP . $id,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;
				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. SEP . 'fmticket' .  SEP . $id,
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. SEP  . 'fmticket' .  SEP . $id);
				}
				else
				{
					$receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. SEP . 'fmticket' .  SEP . $id);
				}
				$this->vfs->override_acl = 0;
			}

//_debug_array($receipt);
			return $receipt;
		}
		
		function delete_file($values,$id='')
		{
			for ($i=0;$i<count($values['delete_file']);$i++)
			{
				$file = $this->fakebase. SEP . 'fmticket' . SEP . $id . SEP . $values['delete_file'][$i];

				if($this->vfs->file_exists(array(
					'string' => $file,
					'relatives' => Array(RELATIVE_NONE)
				)))
				{
					$this->vfs->override_acl = 1;

					if(!$this->vfs->rm (array(
						'string' => $file,
					     'relatives' => array(
					          RELATIVE_NONE
					     )
					)))
					{
						$receipt['error'][]=array('msg'=>lang('failed to delete file') . ' :'. $this->fakebase. SEP . 'fmticket' . SEP . $id . SEP .$values['delete_file'][$i]);
					}
					else
					{
						$receipt['message'][]=array('msg'=>lang('file deleted') . ' :'. $this->fakebase. SEP . 'fmticket' . SEP . $id . SEP . $values['delete_file'][$i]);
					}
					$this->vfs->override_acl = 0;
				}
			}
			return $receipt;
		}
	}
?>
