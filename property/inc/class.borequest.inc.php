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

	class property_borequest
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

		function property_borequest($session=false)
		{
			$this->so 					= CreateObject('property.sorequest');
			$this->bocommon 			= CreateObject('property.bocommon');
			$this->solocation 			= CreateObject('property.solocation');
			$this->historylog			= CreateObject('property.historylog','request');
			$this->cats					= CreateObject('phpgwapi.categories', -1,  'property', '.project');
			$this->cats->supress_info	= true;
			$this->custom 				= & $this->so->custom;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start			= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query			= phpgw::get_var('query');
			$sort			= phpgw::get_var('sort');
			$order			= phpgw::get_var('order');
			$filter			= phpgw::get_var('filter', 'int');
			$district_id	= phpgw::get_var('district_id', 'int');
			$cat_id			= phpgw::get_var('cat_id', 'int');
			$status_id		= phpgw::get_var('status_id');
			$allrows		= phpgw::get_var('allrows', 'bool');
			$this->p_num	= phpgw::get_var('p_num');

			if(isset($_POST['start']) || isset($_GET['start']))
			{
				$this->start = $start;
			}
			if(isset($_POST['query']) || isset($_GET['query']))
			{
				$this->query = $query;
			}
			if(isset($_POST['filter']) || isset($_GET['filter']))
			{
				$this->filter = $filter;
			}
			if(isset($_POST['sort']) || isset($_GET['sort']))
			{
				$this->sort = $sort;
			}
			if(isset($_POST['order']) || isset($_GET['order']))
			{
				$this->order = $order;
			}
			if(isset($_POST['district_id']) || isset($_GET['district_id']))
			{
				$this->district_id = $district_id;
			}
			if(isset($_POST['cat_id']) || isset($_GET['cat_id']))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($_POST['status_id']) || isset($_GET['status_id']))
			{
				$this->status_id = $status_id;
			}
			if(isset($_POST['criteria_id']) || isset($_GET['criteria_id']))
			{
				$this->criteria_id = $criteria_id;
			}
			if($allrows)
			{
				$this->allrows = $allrows;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','request',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','request');

			$this->start		= $data['start'];
			$this->query		= $data['query'];
			$this->filter		= $data['filter'];
			$this->sort			= $data['sort'];
			$this->order		= $data['order'];
			$this->district_id	= $data['district_id'];
			$this->cat_id		= $data['cat_id'];
			$this->status_id	= $data['status_id'];
		}

		function select_degree_list($degree_value='',$degreedefault_type='')
		{
			if ($degree_value)
			{
				$selected=$degree_value;
			}
			else
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences']['property'][$degreedefault_type];
			}

			$degree_comment[0]=' - '.lang('None');
			$degree_comment[1]=' - '.lang('Minor');
			$degree_comment[2]=' - '.lang('Medium');
			$degree_comment[3]=' - '.lang('Serious');
			for ($i=0; $i<=3; $i++)
			{
				$degree_list[$i]['id'] = $i;
				$degree_list[$i]['name'] = $i . $degree_comment[$i];
				if ($i==$selected)
				{
					$degree_list[$i]['selected']= 'selected';
				}
			}

			return $degree_list;
		}

		function select_probability_list($probability_value='')
		{
			$selected=$probability_value;

			$probability_comment[1]=' - '.lang('Small');
			$probability_comment[2]=' - '.lang('Medium');
			$probability_comment[3]=' - '.lang('Large');
			for ($i=1; $i<=3; $i++)
			{
				$probability_list[$i]['id'] = $i;
				$probability_list[$i]['name'] = $i . $probability_comment[$i];
				if ($i==$selected)
				{
					$probability_list[$i]['selected']= 'selected';
				}
			}

			return $probability_list;
		}

		function select_conditions($request_id='')
		{
			$condition_type_list = $this->so->select_condition_type_list();

			if($request_id)
			{
				$conditions = $this->so->select_conditions($request_id,$condition_type_list);
			}

			for ($i=0;$i<count($condition_type_list);$i++)
			{
				$conditions[$i]['degree'] 				= $this->select_degree_list($conditions[$i]['degree']);
				$conditions[$i]['probability'] 			= $this->select_probability_list($conditions[$i]['probability']);
				$conditions[$i]['consequence'] 			= $this->select_consequence_list($conditions[$i]['consequence']);
				$conditions[$i]['condition_type']		= $condition_type_list[$i]['id'];
				$conditions[$i]['condition_type_name']	= $condition_type_list[$i]['name'];
			}

			return $conditions;
		}

		function select_consequence_list($consequence_value='',$consequencedefault_type='')
		{
			if ($consequence_value)
			{
				$selected=$consequence_value;
			}
			else
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences']['property'][$consequencedefault_type];
			}

			$consequence_comment[0]=' - '.lang('None Consequences');
			$consequence_comment[1]=' - '.lang('Minor Consequences');
			$consequence_comment[2]=' - '.lang('Medium Consequences');
			$consequence_comment[3]=' - '.lang('Serious Consequences');
			for ($i=0; $i<=3; $i++)
			{
				$consequence_list[$i][id] = $i;
				$consequence_list[$i]['name'] = $i . $consequence_comment[$i];
				if ($i==$selected)
				{
					$consequence_list[$i]['selected']= 'selected';
				}
			}

			return $consequence_list;
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


		function read_priority_key()
		{
			return	$this->so->read_priority_key();
		}

		function update_priority_key($values)
		{
			return	$this->so->update_priority_key($values);
		}

		function read($data)
		{
			$request = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'district_id' => $this->district_id,'cat_id' => $this->cat_id,'status_id' => $this->status_id,
				'project_id' => $data['project_id'],'allrows'=>$data['allrows'],'list_descr' => $data['list_descr'],
				'dry_run'=>$data['dry_run'], 'p_num' => $this->p_num));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;
			$cols_extra		= $this->so->cols_extra;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			for ($i=0; $i<count($request); $i++)
			{
				$request[$i]['coordinator'] = $GLOBALS['phpgw']->accounts->id2name($request[$i]['coordinator']);
				$request[$i]['start_date'] = $GLOBALS['phpgw']->common->show_date($request[$i]['start_date'],$dateformat);
				if($cols_extra)
				{
					$location_data=$this->solocation->read_single($request[$i]['location_code']);
					for ($j=0;$j<count($cols_extra);$j++)
					{
						$request[$i][$cols_extra[$j]] = $location_data[$cols_extra[$j]];
					}
				}
			}

			return $request;
		}

		function read_single($request_id = 0, $values = array(),$view = false)
		{
			$values['attributes'] = $this->custom->find('property', '.project.request', 0, '', 'ASC', 'attrib_sort', true, true);

			if($request_id)
			{
				$values = $this->so->read_single($request_id, $values);
			}

			$values = $this->custom->prepare($values, 'property', '.project.request', $view);

			if(!$request_id)
			{
				return $values;
			}

			$dateformat					= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$values['start_date']		= $GLOBALS['phpgw']->common->show_date($values['start_date'],$dateformat);
			$values['end_date']			= $GLOBALS['phpgw']->common->show_date($values['end_date'],$dateformat);

			if($values['location_code'])
			{
				$values['location_data'] =$this->solocation->read_single($values['location_code']);
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

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$values['files'] = $vfs->ls (array(
				'string' => "/property/request/$request_id",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			if(!isset($values['files'][0]['file_id']))
			{
				$values['files'] = array();
			}

			$interlink 	= CreateObject('property.interlink');
			$values['origin'] = $interlink->get_relation('property', '.project.request', $request_id, 'origin');
			$values['target'] = $interlink->get_relation('property', '.project.request', $request_id, 'target');

			return $values;
		}


		function read_record_history($id)
		{
			$history_array = $this->historylog->return_array(array('O'),array(),'','',$id);
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
				case 'P': $type = lang('Priority changed'); break;
				case 'CO': $type = lang('Initial Coordinator'); break;
				case 'C': $type = lang('Coordinator changed'); break;
				case 'TO': $type = lang('Initial Category'); break;
				case 'T': $type = lang('Category changed'); break;
				case 'SO': $type = lang('Initial Status'); break;
				case 'S': $type = lang('Status changed'); break;
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


		function next_id()
		{
			return $this->so->next_id();
		}

		function save($request,$action='',$values_attribute = array())
		{
			while (is_array($request['location']) && list(,$value) = each($request['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$request['location_code']	= implode("-", $location);
			$request['start_date']		= phpgwapi_datetime::date_to_timestamp($request['start_date']);
			$request['end_date']		= phpgwapi_datetime::date_to_timestamp($request['end_date']);

			if(is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($action=='edit')
			{
				$receipt = $this->so->edit($request,$values_attribute);
			}
			else
			{
				$receipt = $this->so->add($request,$values_attribute);
			}
			return $receipt;
		}

		function delete($request_id)
		{
			$this->so->delete($request_id);
		}
	}
