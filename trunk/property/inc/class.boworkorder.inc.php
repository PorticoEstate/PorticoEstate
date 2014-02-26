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

	class property_boworkorder
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $filter_year;
		var $cat_id;
		var $order_sent_adress; // in case we want to resend the order as an reminder
		var $allrows;

		var $public_functions = array
			(
				'read'			=> true,
				'read_single'	=> true,
				'save'			=> true,
				'delete'		=> true,
				'check_perms'	=> true,
				'get_category'	=> true
			);

		function property_boworkorder($session=false)
		{
			$this->so 			= CreateObject('property.soworkorder');
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->cats			= CreateObject('phpgwapi.categories', -1,  'property', '.project');
			$this->cats->supress_info	= true;
			$this->interlink 	= & $this->so->interlink;
			
			$obligation	= phpgw::get_var('obligation', 'bool');
			
			if ($session && !$obligation)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$default_filter_year 	= isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_project_filter_year']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['default_project_filter_year'] == 'current_year' ? date('Y') : 'all';

			$start				= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query				= phpgw::get_var('query');
			$sort				= phpgw::get_var('sort');
			$order				= phpgw::get_var('order');
			$filter				= phpgw::get_var('filter', 'int');
			$filter_year		= phpgw::get_var('filter_year', 'string', 'REQUEST', $default_filter_year);
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$status_id			= phpgw::get_var('status_id');
			$wo_hour_cat_id		= phpgw::get_var('wo_hour_cat_id', 'int');
			$start_date			= phpgw::get_var('start_date');
			$end_date			= phpgw::get_var('end_date');
			$b_group			= phpgw::get_var('b_group');
			$ecodimb			= phpgw::get_var('ecodimb');
			$paid				= phpgw::get_var('paid', 'bool');
			$b_account			= phpgw::get_var('b_account');
			$district_id		= phpgw::get_var('district_id', 'int');
			$criteria_id		= phpgw::get_var('criteria_id', 'int');
			$this->allrows		= phpgw::get_var('allrows', 'bool');
			$this->obligation	= $obligation;

			$this->start		= $start ? $start : 0;
			$this->filter_year	= $filter_year;

			if(array_key_exists('district_id',$_POST) || array_key_exists('district_id',$_GET) )
			{
				$this->district_id = $district_id;
			}

			$this->paid = $paid;

			$this->b_group = $b_group;
			$this->ecodimb = $ecodimb;
			$this->b_account = $b_account;

			if(array_key_exists('query',$_POST) || array_key_exists('query',$_GET) )
			{
				$this->query = $query;
			}
			if(array_key_exists('filter',$_POST) || array_key_exists('filter',$_GET) )
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
			if(array_key_exists('cat_id',$_POST) || array_key_exists('cat_id',$_GET))
			{
				$this->cat_id = $cat_id;
			}
			if(array_key_exists('criteria_id',$_POST) || array_key_exists('criteria_id',$_GET))
			{
				$this->criteria_id = $criteria_id;
			}
			if(array_key_exists('status_id',$_POST)  || array_key_exists('status_id',$_GET))
			{
				$this->status_id = $status_id;
			}
			if(array_key_exists('wo_hour_cat_id',$_POST)  || array_key_exists('wo_hour_cat_id',$_GET))
			{
				$this->wo_hour_cat_id = $wo_hour_cat_id;
			}
			if(array_key_exists('start_date',$_POST) || array_key_exists('start_date',$_GET))
			{
				$this->start_date = $start_date;
			}
			if(array_key_exists('end_date',$_POST) || array_key_exists('end_date',$_GET))
			{
				$this->end_date = $end_date;
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','workorder');

			$this->start			= isset($data['start']) ? $data['start'] : '';
			$this->query			= isset($data['query']) ? $data['query']: '';
			$this->filter			= isset($data['filter']) ? $data['filter']: '';
			$this->sort				= isset($data['sort']) ? $data['sort']: '';
			$this->order			= isset($data['order']) ? $data['order']: '';
			$this->cat_id			= isset($data['cat_id']) ? $data['cat_id']: '';
			$this->status_id		= isset($data['status_id']) ? $data['status_id']: '';
			$this->wo_hour_cat_id	= isset($data['wo_hour_cat_id']) ? $data['wo_hour_cat_id']: '';
	//		$this->start_date		= isset($data['start_date']) ? $data['start_date']: '';
	//		$this->end_date			= isset($data['end_date']) ? $data['end_date']: '';
	//		$this->b_group			= isset($data['b_group']) ? $data['b_group']: '';
	//		$this->paid				= isset($data['paid']) ? $data['paid']: '';
	//		$this->b_account		= isset($data['b_account']) ? $data['b_account']: '';
			$this->district_id		= isset($data['district_id']) ? $data['district_id']: '';
			$this->criteria_id		= isset($data['criteria_id'])?$data['criteria_id']:'';
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','workorder',$data);
			}
		}

		public function get_category()
		{
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$category	= $this->cats->return_single($cat_id);
			return $category[0];
		}

		function column_list($selected = array())
		{
			if(!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['workorder_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['workorder_columns'] : '';
			}

			$columns	= $this->get_column_list();
			return $this->bocommon->select_multi_list($selected,$columns);
		}

		function get_column_list()
		{
			$columns = array();
			$columns['continuous'] = array
				(
					'id'		=> 'continuous',
					'name'		=> lang('continuous'),
					'sortable'	=> true
				);
			$columns['ecodimb'] = array
				(
					'id'		=> 'ecodimb',
					'name'		=> lang('accounting dim b'),
					'sortable'	=> true
				);
			$columns['entry_date'] = array
				(
					'id' => 'entry_date',
					'name'=> lang('entry date'),
					'sortable'	=> true
				);

			$columns['start_date'] = array
				(
					'id' => 'start_date',
					'name'=> lang('start date'),
					'sortable'	=> true
				);
			$columns['end_date'] = array
				(
					'id' => 'end_date',
					'name'=> lang('end date'),
					'sortable'	=> true
				);
			$columns['billable_hours'] = array
				(
					'id' => 'billable_hours',
					'name'=> lang('billable hours'),
					'sortable'	=> true
				);
			$columns['contract_sum'] = array
				(
					'id' => 'contract_sum',
					'name'=> lang('contract sum'),
					'sortable'	=> true
				);

			$columns['approved'] = array
				(
					'id'		=> 'approved',
					'name'		=> lang('approved'),
					'sortable'	=> true
				);

			return $columns;
		}

		function next_id()
		{
			return $this->so->next_id();
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

			return $branch_list;
		}

		function select_key_location_list($selected='')
		{
			$key_location_entries= $this->so->select_key_location_list();
			return $this->bocommon->select_list($selected,$key_location_entries);
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
						'name'	=> lang('workorder id')
					),
					array
					(
						'id'	=> '4',
						'name'	=> lang('address')
					),

					array
					(
						'id'	=> '5',
						'name'	=> lang('location code')
					),
					array
					(
						'id'	=> '6',
						'name'	=> lang('title')
					),
					array
					(
						'id'	=> '7',
						'name'	=> lang('vendor')
					),
					array
					(
						'id'	=> '8',
						'name'	=> lang('vendor id')
					),
					array
					(
						'id'	=> '9',
						'name'	=> lang('accounting dim b')
					),
					array
					(
						'id'	=> '10',
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
					'field'		=> 'fm_workorder.id',
					'type'		=> 'bigint',
					'matchtype' => 'exact',
					'front' => "'",
					'back' => "'"
				);
			$criteria[4] = array
				(
					'field'	=> 'fm_project.address',
					'type'	=> 'varchar',
					'matchtype' => 'like',
					'front' => "'%",
					'back' => "%'",
				);
			$criteria[5] = array
				(
					'field'	=> 'fm_project.location_code',
					'type'	=> 'varchar',
					'matchtype' => 'like',
					'front' => "'",
					'back' => "%'"
				);
			$criteria[6] = array
				(
					'field'	=> 'fm_workorder.title',
					'type'	=> 'varchar',
					'matchtype' => 'like',
					'front' => "'%",
					'back' => "%'"
				);
			$criteria[7] = array
				(
					'field'	=> 'fm_vendor.org_name',
					'type'	=> 'varchar',
					'matchtype' => 'like',
					'front' => "'%",
					'back' => "%'"
				);
			$criteria[8] = array
				(
					'field'	=> 'fm_vendor.id',
					'type'	=> 'int',
					'matchtype' => 'exact',
					'front' => '',
					'back' => ''
				);
			$criteria[9] = array
				(
					'field'	=> 'fm_workorder.ecodimb',
					'type'	=> 'int',
					'matchtype' => 'exact',
					'front' => '',
					'back' => ''
				);
			$criteria[10] = array
				(
					'field'	=> 'fm_b_account.category',
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


		function read($data = array())
		{
			$start_date	= $this->bocommon->date_to_timestamp($data['start_date']);
			$end_date	= $this->bocommon->date_to_timestamp($data['end_date']);

			if(isset($this->allrows) && $this->allrows)
			{
				$data['allrows'] = true;
			}

			$workorder = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'status_id' => $this->status_id,
				'wo_hour_cat_id' => $this->wo_hour_cat_id,
				'start_date'=>$start_date,'end_date'=>$end_date,'allrows'=>$data['allrows'],
				'b_group'=>$this->b_group,'ecodimb'=>$this->ecodimb, 'paid'=>$this->paid,'b_account' => $this->b_account,
				'district_id' => $this->district_id,'dry_run'=>$data['dry_run'], 'criteria' => $this->get_criteria($this->criteria_id),
				'obligation' => $this->obligation, 'filter_year' => $this->filter_year) );

			$this->total_records = $this->so->total_records;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->uicols	= $this->so->uicols;
			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['workorder_columns']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['workorder_columns'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['workorder_columns'] : array();

			$column_list = $this->get_column_list();

			foreach ($custom_cols as $col_id)
			{
				$this->uicols['input_type'][]	= 'text';
				$this->uicols['name'][]			= $col_id;
				$this->uicols['descr'][]		= $column_list[$col_id]['name'];
				$this->uicols['statustext'][]	= $column_list[$col_id]['name'];
				$this->uicols['exchange'][]		= false;
				$this->uicols['align'][] 		= '';
				$this->uicols['datatype'][]		= false;
				$this->uicols['sortable'][]		= $column_list[$col_id]['sortable'];
			}

			foreach ($workorder as &$entry)
			{
				$entry['entry_date'] = $GLOBALS['phpgw']->common->show_date($entry['entry_date'],$dateformat);
				$entry['start_date'] = $GLOBALS['phpgw']->common->show_date($entry['start_date'],$dateformat);
				$entry['end_date'] = $GLOBALS['phpgw']->common->show_date($entry['end_date'],$dateformat);
			}

			return $workorder;
		}

		function read_single($workorder_id)
		{
			if(!$workorder_id)
			{
				return array();
			}

			$contacts	= CreateObject('property.sogeneric');
			$contacts->get_location_info('vendor',false);
			$workorder						= $this->so->read_single($workorder_id);
			$dateformat						= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$workorder['start_date']		= $GLOBALS['phpgw']->common->show_date($workorder['start_date'],$dateformat);
			$workorder['end_date']			= $GLOBALS['phpgw']->common->show_date($workorder['end_date'],$dateformat);

			if(isset($workorder['vendor_id']) && $workorder['vendor_id'])
			{
				$custom 		= createObject('property.custom_fields');
				$vendor['attributes'] = $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);
				$vendor			= $contacts->read_single(array('id' => $workorder['vendor_id']),$vendor);
				foreach($vendor['attributes'] as $attribute)
				{
					if($attribute['name']=='org_name')
					{
						$workorder['vendor_name']=$attribute['value'];
					}
					if($attribute['name']=='email')
					{
						$workorder['vendor_email']=$attribute['value'];
					}
				}
			}

			$workorder['b_account_name']	= $this->so->get_b_account_name($workorder['b_account_id']);

			$config				= CreateObject('phpgwapi.config','property');
			$config->read();
			$tax = 1+($config->config_data['fm_tax'])/100;
			$workorder['calculation']	= $workorder['calculation'] * $tax;

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$workorder['files'] = $vfs->ls(array(
				'string' => "/property/workorder/{$workorder_id}",
				'relatives' => array(RELATIVE_NONE)
			));

			$vfs->override_acl = 0;

			$j	= count($workorder['files']);
			for ($i=0;$i<$j;$i++)
			{
				$workorder['files'][$i]['file_name']=urlencode($workorder['files'][$i]['name']);
			}

			$workorder['origin'] = $this->interlink->get_relation('property', '.project.workorder', $workorder_id, 'origin');
			$workorder['target'] = $this->interlink->get_relation('property', '.project.workorder', $workorder_id, 'target');

			if($workorder['location_code'])
			{
				$solocation 	= CreateObject('property.solocation', $this->bocommon);
				$workorder['location_data'] = $solocation->read_single($workorder['location_code']);
			}

			if($workorder['tenant_id']>0)
			{
				$tenant_data=$this->bocommon->read_single_tenant($workorder['tenant_id']);
				$workorder['location_data']['tenant_id']= $workorder['tenant_id'];
				$workorder['location_data']['contact_phone']= $tenant_data['contact_phone'];
				$workorder['location_data']['last_name']	= $tenant_data['last_name'];
				$workorder['location_data']['first_name']	= $tenant_data['first_name'];
			}
			else
			{
				unset($workorder['location_data']['tenant_id']);
				unset($workorder['location_data']['contact_phone']);
				unset($workorder['location_data']['last_name']);
				unset($workorder['location_data']['first_name']);
			}

			if($workorder['p_num'])
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');
				$category = $soadmin_entity->read_single_category($workorder['p_entity_id'],$workorder['p_cat_id']);

				$workorder['p'][$workorder['p_entity_id']]['p_num']=$workorder['p_num'];
				$workorder['p'][$workorder['p_entity_id']]['p_entity_id']=$workorder['p_entity_id'];
				$workorder['p'][$workorder['p_entity_id']]['p_cat_id']=$workorder['p_cat_id'];
				$workorder['p'][$workorder['p_entity_id']]['p_cat_name'] = $category['name'];
			}

			$event_criteria = array
				(
					'appname'		=> 'property',
					'location'		=> '.project.workorder',
					'location_item_id'	=> $workorder_id
				);

			$events = execMethod('property.soevent.read_at_location', $event_criteria);
			$workorder['event_id'] = $events ? $events[0]['id'] : '';

			return $workorder;
		}

		function read_record_history($id)
		{
			$historylog	= CreateObject('property.historylog','workorder');
			$history_array = $historylog->return_array(array('O'),array(),'','',$id);

			$_status_list = $this->so->select_status_list();
			$status_text = array();
			foreach ($_status_list as $_status_entry)
			{
				$status_text[$_status_entry['id']] = $_status_entry['name'];
			}

			$i=0;
			foreach ($history_array as $value) 
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
					case 'M':
						$type = lang('Sent by email to');
						$_order_sent_adress = explode('::',$value['new_value']);
						$this->order_sent_adress = $_order_sent_adress[0]; // in case we want to resend the order as an reminder
						unset($_order_sent_adress);
						break;
					case 'MS':
						$type = lang('Sent by sms');
						break;
					case 'B': $type = lang('Budget changed'); break;
					case 'CO': $type = lang('Initial Coordinator'); break;
					case 'C': $type = lang('Coordinator changed'); break;
					case 'TO': $type = lang('Initial Category'); break;
					case 'T': $type = lang('Category changed'); break;
					case 'SO': $type = lang('Initial Status'); break;
					case 'S': $type = lang('Status changed'); break;
					case 'SC': $type = lang('Status confirmed'); break;
					case 'AP': $type = lang('Ask for approval'); break;
					case 'ON': $type = lang('Owner notified'); break;
					case 'H': $type = lang('Billable hours changed'); break;
					case 'NP': $type = lang('moved to another project'); break;
					case 'OA': $type = lang('order approved'); break;
					case 'OB': $type = lang('order approval revoked'); break;
					default:
				}

				if($value['new_value']=='O'){$value['new_value']=lang('Opened');}
				if($value['new_value']=='X'){$value['new_value']=lang('Closed');}

				$record_history[$i]['value_action']	= $type ? $type:'';
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
				else if ($value['status'] == 'S' || $value['status'] == 'SO'  || $value['status'] == 'R' || $value['status'] == 'X')
				{
					$record_history[$i]['value_new_value']	= $status_text[$value['new_value']];
					$record_history[$i]['value_old_value']	= $status_text[$value['old_value']];
				}
				else if ($value['status'] != 'O' && $value['new_value'])
				{
					$record_history[$i]['value_new_value']	= $value['new_value'];
					$record_history[$i]['value_old_value']	= $value['old_value'];
				}
				else if ($value['status'] != 'B' && $value['new_value'])
				{
					$record_history[$i]['value_new_value']	=number_format($value['new_value'], 0, ',', ' ');
					$record_history[$i]['value_old_value']	=number_format($value['old_value'], 0, ',', ' ');
				}
				else
				{
					$record_history[$i]['value_new_value']	= '';
				}

				$i++;
			}

			return $record_history;
		}

		function save($workorder,$action='')
		{
			$workorder['start_date']	= $this->bocommon->date_to_timestamp($workorder['start_date']);
			$workorder['end_date']	= $this->bocommon->date_to_timestamp($workorder['end_date']);
			$workorder['location_code'] = isset($workorder['location']) && $workorder['location'] ? implode('-',$workorder['location']) : '';

			if ($action=='edit')
			{
				$receipt = $this->so->edit($workorder);
			}
			else
			{
				$receipt = $this->so->add($workorder);
				$workorder['id'] = $receipt['id'];
			}

			if(isset($workorder['charge_tenant']) && $workorder['charge_tenant'] && $workorder['id'])
			{
				if(!$_tenant_id = $workorder['extra']['tenant_id'])
				{
					$project = execMethod('property.soproject.read_single',$workorder['project_id']);
					$_tenant_id = $project['tenant_id'];
				}

				$boclaim = CreateObject('property.botenant_claim');

				$value_set = array
				(
					'workorder' 		=> $target,
					'project_id'		=> $workorder['project_id'],
				);

				$claim = $boclaim->read(array('project_id' => $workorder['project_id']));
				$target = array();
				if($claim)
				{
					$value_set['claim_id'] = $claim[0]['claim_id'];
					$claim_old  = $boclaim->read_single($claim[0]['claim_id']);
					if(isset($claim_old['workorder']) && $claim_old['workorder'])
					{
						$target = $claim_old['workorder'];
					}
					$value_set['amount']		= $claim_old['amount'];
					$value_set['tenant_id']		= $claim_old['tenant_id'];
					$value_set['b_account_id']	= $claim_old['b_account_id'];
					$value_set['cat_id']		= $claim_old['cat_id'];
					$value_set['status']		= $claim_old['status'];
					$value_set['remark']		= $claim_old['remark'];					
				}
				else
				{
					$value_set['amount']		= 0;
					$value_set['tenant_id']		= $_tenant_id;
					$value_set['b_account_id']	= $workorder['b_account_id'];
					$value_set['cat_id']		= 99;
					$value_set['status']		= 'open';
					$value_set['remark']		= '';
				}

				if(!in_array($workorder['id'],$target))
				{
					$target[] = $workorder['id'];
				}

				$value_set['workorder']	= $target;

				if(!$value_set['tenant_id'])
				{
					$receipt['error'][] = array('msg'=>lang('tenant is not defined, claim not issued'));
				}
				else
				{
					$receipt_claim = $boclaim->save($value_set);
					unset($receipt_claim['id']);
					$receipt['error'] = array_merge($receipt['error'] , $receipt_claim['error']);
					$receipt['message'] = array_merge($receipt['message'] , $receipt_claim['message']);
				}
			}
			if ($workorder['id'])
			{
				//temporary
				execMethod('property.soXport.update_actual_cost_from_archive',array($workorder['id'] => true));
				$this->notify_coordinator_on_consumption($workorder['id']);
			}
			return $receipt;
		}

		function delete($workorder_id)
		{
			$this->so->delete($workorder_id);
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

		public function get_budget($order_id)
		{
			return $this->so->get_budget($order_id);
		}

		/**
		* Recalculate actual cost from payment history for all workorders
		*
		* @return void
		*/
		function recalculate()
		{
			$this->so->recalculate();
		}

		/**
		 * Check the consumption  on an order - and notify the coordinator
		 * @param integer $order_id
		 */
		function notify_coordinator_on_consumption($order_id)
		{
			$notify_coordinator = true;
			if(!$notify_coordinator)
			{
				return false;
			}
			$toarray = array();
			$workorder	= $this->so->read_single($order_id);

			if(!$workorder['continuous'])
			{
				return false;
			}

			$project	= ExecMethod('property.boproject.read_single_mini',$workorder['project_id']);
			$coordinator = $project['coordinator'];
			$prefs_coordinator = $this->bocommon->create_preferences('property',$coordinator);
			if(isset($prefs_coordinator['email']) && $prefs_coordinator['email'])
			{
				$toarray[] = $prefs_coordinator['email'];
			}

			if ($toarray)
			{
				$budget_info = $this->so->get_order_budget_percent($order_id);

				if($budget_info['percent'] < 90)
				{
					return false;
				}

				if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['email']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['email'])
				{
					$from_name=$GLOBALS['phpgw_info']['user']['fullname'];
					$from_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
				}
				else
				{
					$from_name	 = 'noreply';
					$from_email	 = "{$from_name}<noreply@bergen.kommune.no>";
				}

				$subject	 = "Bestilling # {$order_id} har disponert {$budget_info['percent']} prosent av budsjettet";

				$lang_budget = lang('budget');
				$lang_actual_cost = lang('actual cost');
				$lang_percent = lang('percent');
				$lang_obligation = lang('obligation');

				$_budget = number_format($budget_info['budget'], 0, ',', ' ');
				$_actual_cost = number_format($budget_info['actual_cost'], 0, ',', ' ');
				$_budget = number_format($budget_info['budget'], 0, ',', ' ');
				$_obligation = number_format($budget_info['obligation'], 0, ',', ' ');

				$to = implode(';',$toarray);
				$cc = false;
				$bcc = 'sigurd.nes@bergen.kommune.no';//test phase
				$body = '<a href ="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit','id'=> $order_id),false,true).'">' . lang('workorder %1 has been edited',$order_id) .'</a>' . "\n";
				$body .= <<<HTML
				</br>
				<h2>{$workorder['title']}</h2>
				</br>
				</br>
				<table>
					<tr>
						<td>
							{$lang_budget}
						</td>
						<td align = 'right'>
							{$_budget}
						</td>
					</tr>
					<tr>
						<td>
							{$lang_actual_cost}
						</td>
						<td align = 'right'>
							{$_actual_cost}
						</td>
					</tr>
					<tr>
						<td>
							{$lang_percent}
						</td>
						<td align = 'right'>
							{$budget_info['percent']}
						</td>
					</tr>
					<tr>
						<td>
							{$lang_obligation}
						</td>
						<td align = 'right'>
							{$_obligation}
						</td>
					</tr>
				</table>
HTML;

				if (!is_object($GLOBALS['phpgw']->send))
				{
					$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
				}

				try
				{
					$ok = $GLOBALS['phpgw']->send->msg('email',$to,$subject,$body, false,$cc,$bcc, $from_email, $from_name, 'html');
				}
				catch (phpmailerException $e)
				{
					phpgwapi_cache::message_set( $e->getMessage(), 'error' );
				}

				if($ok)
				{
					$historylog	= CreateObject('property.historylog','workorder');
					$historylog->add('ON', $order_id, lang('%1 is notified',$to));
					$historylog->add('RM', $order_id, $subject);
					return true;
				}
			}
		}
	}
