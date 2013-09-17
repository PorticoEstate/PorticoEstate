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
		public $sum_investment = 0;
		public $sum_operation = 0;
		public $sum_potential_grants = 0;
		public $sum_consume = 0;
		public $acl_location = '.project.request';
		public $responsible_unit;

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
			$this->cats					= CreateObject('phpgwapi.categories', -1,  'property', '.project.request');
			$this->cats->supress_info	= true;
			$this->custom 				= & $this->so->custom;
//			$this->acl_location			= '.project.request';
			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start				= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query				= phpgw::get_var('query');
			$sort				= phpgw::get_var('sort');
			$order				= phpgw::get_var('order');
			$filter				= phpgw::get_var('filter', 'int');
			$property_cat_id	= phpgw::get_var('property_cat_id', 'int');
			$district_id		= phpgw::get_var('district_id', 'int');
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$status_id			= phpgw::get_var('status_id');
			$degree_id			= phpgw::get_var('degree_id', 'int');
			$allrows			= phpgw::get_var('allrows', 'bool');
			$this->p_num		= phpgw::get_var('p_num');

			$start_date			= phpgw::get_var('start_date');
			$end_date			= phpgw::get_var('end_date');
			$building_part		= phpgw::get_var('building_part');
			$responsible_unit	= phpgw::get_var('responsible_unit', 'int');
			$recommended_year	= phpgw::get_var('recommended_year', 'int');

			$this->condition_survey_id = phpgw::get_var('condition_survey_id', 'int', 'REQUEST', 0);

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

			if(isset($_POST['property_cat_id']) || isset($_GET['property_cat_id']))
			{
				$this->property_cat_id = $property_cat_id;
			}
			if(isset($_POST['cat_id']) || isset($_GET['cat_id']))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($_POST['status_id']) || isset($_GET['status_id']))
			{
				$this->status_id = $status_id;
			}
			if(isset($_POST['degree_id']) || isset($_GET['degree_id']))
			{
				$this->degree_id = $degree_id;
			}
			if(isset($_POST['criteria_id']) || isset($_GET['criteria_id']))
			{
				$this->criteria_id = $criteria_id;
			}
			if(isset($_POST['building_part']) || isset($_GET['building_part']))
			{
				$this->building_part = $building_part;
			}

			if(isset($_POST['responsible_unit']) || isset($_GET['responsible_unit']))
			{
				$this->responsible_unit = $responsible_unit;
			}

			if(isset($_POST['recommended_year']) || isset($_GET['recommended_year']))
			{
				$this->recommended_year = $recommended_year;
			}


			if($allrows)
			{
				$this->allrows = $allrows;
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

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				phpgwapi_cache::session_set('property.request','session_data', $data);
			}
		}

		function read_sessiondata()
		{
			$data =	phpgwapi_cache::session_get('property.request','session_data', $data);

			$this->start			= $data['start'];
			$this->query			= $data['query'];
			$this->filter			= $data['filter'];
			$this->sort				= $data['sort'];
			$this->order			= $data['order'];
			$this->district_id		= $data['district_id'];
			$this->cat_id			= $data['cat_id'];
			$this->property_cat_id 	= $data['property_cat_id'];
			$this->status_id		= $data['status_id'];
			$this->degree_id		= $data['degree_id'];
			$this->building_part	= $data['building_part'];
			$this->responsible_unit	= $data['responsible_unit'];
			$this->recommended_year	= $data['recommended_year'];
			$this->start_date		= isset($data['start_date']) ? $data['start_date']: '';
			$this->end_date			= isset($data['end_date']) ? $data['end_date']: '';
		}


		function column_list($selected = array())
		{
			if(!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['request_columns']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['request_columns'] : '';
			}

			$columns	= $this->get_column_list();
			return $this->bocommon->select_multi_list($selected,$columns);
		}

		function get_column_list()
		{
			$columns = array();
			$columns['entry_date'] = array
				(
					'id' => 'entry_date',
					'name'=> lang('entry date'),
					'sortable'	=> true
				);

			$columns['in_progress_date'] = array
				(
					'id'		=> 'in_progress_date',
					'name'		=> lang('in progress date'),
					'sortable'	=> true
				);
			$columns['delivered_date'] = array
				(
					'id'		=> 'delivered_date',
					'name'		=> lang('delivered date'),
					'sortable'	=> true
				);
			$columns['closed_date'] = array
				(
					'id'		=> 'closed_date',
					'name'		=> lang('closed date'),
					'sortable'	=> true
				);

			return $columns;
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

			$degree_comment[0]=' - '.lang('no symptoms');
			$degree_comment[1]=' - '.lang('minor symptoms');
			$degree_comment[2]=' - '.lang('medium symptoms');
			$degree_comment[3]=' - '.lang('serious symptoms');
			$degree_comment[4]=' - '.lang('condition not assessed');
			for ($i=0; $i<=4; $i++)
			{
				$degree_list[$i]['id'] = $i;
				$degree_list[$i]['name'] = $i . $degree_comment[$i];
				$degree_list[$i]['selected'] = $i==$selected ? 1 : 0;
			}

			return $degree_list;
		}

		function select_probability_list($probability_value='')
		{
			$selected=$probability_value;

			$probability_comment[1]=' - '.lang('low probability');
			$probability_comment[2]=' - '.lang('medium probability');
			$probability_comment[3]=' - '.lang('high probability');
			for ($i=1; $i<=3; $i++)
			{
				$probability_list[$i]['id'] = $i;
				$probability_list[$i]['name'] = $i . $probability_comment[$i];
				$probability_list[$i]['selected'] = $i==$selected ? 1 : 0;
			}

			return $probability_list;
		}


		function select_reference_list($reference_value = 0)
		{
			$selected = $reference_value ?  $reference_value : (int)$GLOBALS['phpgw_info']['user']['preferences']['property']['request_reference_level'];

			$reference_list = array();
			$reference_comment = array();
			$reference_comment[0]=' - '.lang('none');
			$reference_comment[1]=' - '.lang('minor');
			$reference_comment[2]=' - '.lang('medium');
			$reference_comment[3]=' - '.lang('serious');
			for ($i=0; $i<=3; $i++)
			{
				$reference_list[$i]['id'] = $i;
				$reference_list[$i]['name'] = "{$i}{$reference_comment[$i]}";
				$reference_list[$i]['selected'] = $i==$selected ? 1 : 0;
			}

			return $reference_list;
		}

		function select_conditions($request_id='')
		{
			$values = array();
			$condition_type_list = $this->so->select_condition_type_list();

			if($request_id)
			{
				$conditions = $this->so->select_conditions($request_id,$condition_type_list);
			}

			$config	= CreateObject('phpgwapi.config','property');
			$config->read();
			$disallow_multiple_condition_types = isset( $config->config_data['disallow_multiple_condition_types']) &&  $config->config_data['disallow_multiple_condition_types'] ? (int)$config->config_data['disallow_multiple_condition_types'] : 0;

			if( !$disallow_multiple_condition_types )
			{
				foreach($condition_type_list as $condition_type)
				{
					$i = $condition_type['id'];
					$risk	= (int)$conditions[$i]['probability'] * (int)$conditions[$i]['consequence'];
					$values[] = array
					(
						'reference'				=> array('options' => $this->select_reference_list($conditions[$i]['reference'])),
						'degree' 				=> array('options' => $this->select_degree_list($conditions[$i]['degree'])),
						'probability' 			=> array('options' => $this->select_probability_list($conditions[$i]['probability'])),
						'consequence' 			=> array('options' => $this->select_consequence_list($conditions[$i]['consequence'])),
						'condition_type'		=> $condition_type_list[$i]['id'],
						'condition_type_name'	=> $condition_type_list[$i]['name'],
						'failure'				=> (int)$conditions[$i]['reference'] - (int)$conditions[$i]['degree'] < 0 ? 'X' : '',
						'weight'				=> $condition_type_list[$i]['weight'],
						'risk'					=> $risk,
						'score'					=> $risk * (int)$condition_type_list[$i]['weight'] * (int)$conditions[$i]['degree']
					);
				}
			}
			else
			{
				$i = 0;
				foreach($conditions as $condition_type => $condition)
				{
					if($condition['condition_type'])
					{
						$i = $condition['condition_type'];
						break;
					}
				}
				$risk	= (int)$conditions[$i]['probability'] * (int)$conditions[$i]['consequence'];
				$values[] = array
				(
					'condition_type_list'	=> array('options' => $this->bocommon->select_list($i, $condition_type_list)),
					'reference'				=> array('options' => $this->select_reference_list($conditions[$i]['reference'])),
					'degree' 				=> array('options' => $this->select_degree_list($conditions[$i]['degree'])),
					'probability' 			=> array('options' => $this->select_probability_list($conditions[$i]['probability'])),
					'consequence' 			=> array('options' => $this->select_consequence_list($conditions[$i]['consequence'])),
					'condition_type'		=> (int)$condition_type_list[$i]['id'],
					'condition_type_name'	=> $condition_type_list[$i]['name'],
					'condition_type_descr'	=> $condition_type_list[$i]['descr'],
					'failure'				=> (int)$conditions[$i]['reference'] - (int)$conditions[$i]['degree'] < 0 ? 'X' : '',
					'weight'				=> $condition_type_list[$i]['weight'],
					'risk'					=> $risk,
					'score'					=> $risk * (int)$condition_type_list[$i]['weight'] * (int)$conditions[$i]['degree']
				);

				array_unshift($values[0]['condition_type_list']['options'], array ('id'=>'','name'=> lang('consequence type')));
			}
//_debug_array($values);die();
			return $values;
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
				$consequence_list[$i]['selected'] = $i==$selected ? 1 : 0;
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

		public function read_survey_data($data)
		{

			$interlink 	= CreateObject('property.interlink');


			$values = $this->so->read_survey_data($data);

			foreach($values as &$entry)
			{
				$target = $interlink->get_relation('property', $this->acl_location, $entry['id'], 'target');
				$related = array();
				if($target)
				{
					foreach($target as $_target_section)
					{
						foreach ($_target_section['data'] as $_target_entry)
						{
							$related[] = "<a href=\"{$_target_entry['link']}\" title=\"{$_target_entry['title']}\">{$_target_section['descr']}::{$_target_entry['id']}::{$_target_entry['statustext']}</a>";
						}
					}
					$entry['related'] = implode(' /</br>',$related);
				}

				$category 			= $this->cats->return_single($entry['cat_id']);
				$entry['category']	= $category[0]['name'];
			}

			$this->total_records	= $this->so->total_records;
			return $values;
		}

		function read($data)
		{
			$custom	= createObject('phpgwapi.custom_fields');
			$attrib_data = $custom->find('property', $this->acl_location, 0, '','','',true, true);

			$attrib_filter = array();
			if($attrib_data)
			{
				foreach ( $attrib_data as $attrib )
				{
					if($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'R')
					{
						if($_attrib_filter_value = phpgw::get_var($attrib['column_name'], 'int'))
						{
							$attrib_filter[] = "fm_request.{$attrib['column_name']} = '{$_attrib_filter_value}'";
						}
					}
					else if($attrib['datatype'] == 'CH')
					{
						if($_attrib_filter_value = phpgw::get_var($attrib['column_name'], 'int'))
						{
							$attrib_filter[] = "fm_request.{$attrib['column_name']} {$GLOBALS['phpgw']->db->like} '%,{$_attrib_filter_value},%'";
						}
					}
				}
			}

			$request = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'district_id' => $this->district_id,'cat_id' => $this->cat_id,'status_id' => $this->status_id,
				'project_id' => $data['project_id'],'allrows'=>$data['allrows'],'list_descr' => $data['list_descr'],
				'dry_run'=>$data['dry_run'], 'p_num' => $this->p_num,'start_date'=>$this->start_date,'end_date'=>$this->end_date,
				'property_cat_id' => $this->property_cat_id, 'building_part' => $this->building_part,
				'degree_id' => $this->degree_id, 'attrib_filter' => $attrib_filter, 'condition_survey_id' => $this->condition_survey_id,
				'responsible_unit' => $this->responsible_unit, 'recommended_year' => $this->recommended_year));

			$this->total_records			= $this->so->total_records;
			$this->sum_investment			= $this->so->sum_investment;
			$this->sum_operation			= $this->so->sum_operation;
			$this->sum_potential_grants		= $this->so->sum_potential_grants;
			$this->sum_consume				= $this->so->sum_consume;
			$this->uicols					= $this->so->uicols;
			$cols_extra						= $this->so->cols_extra;

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			for ($i=0; $i<count($request); $i++)
			{
				$request[$i]['coordinator'] = $GLOBALS['phpgw']->accounts->id2name($request[$i]['coordinator']);
				$request[$i]['start_date'] = $GLOBALS['phpgw']->common->show_date($request[$i]['start_date'],$dateformat);
				$request[$i]['entry_date'] = $GLOBALS['phpgw']->common->show_date($request[$i]['entry_date'],$dateformat);
				$request[$i]['planned_year'] = $request[$i]['planned_year'] ? date('Y',$request[$i]['planned_year']) : '';
				$request[$i]['closed_date'] = $GLOBALS['phpgw']->common->show_date($request[$i]['closed_date'],$dateformat);
				$request[$i]['in_progress_date'] = $GLOBALS['phpgw']->common->show_date($request[$i]['in_progress_date'],$dateformat);
				$request[$i]['delivered_date'] = $GLOBALS['phpgw']->common->show_date($request[$i]['delivered_date'],$dateformat);

				if($cols_extra)
				{
					$location_data=$this->solocation->read_single($request[$i]['location_code']);
					for ($j=0;$j<count($cols_extra);$j++)
					{
						$request[$i][$cols_extra[$j]] = $location_data[$cols_extra[$j]];
					}
				}
			}

			$column_list = $this->get_column_list();
			$custom_cols = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['request_columns']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['request_columns'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['request_columns'] : array();
			foreach ($custom_cols as $col_id)
			{
				$this->uicols['input_type'][]	= 'text';
				$this->uicols['name'][]			= $col_id;
				$this->uicols['descr'][]		= $column_list[$col_id]['name'];
				$this->uicols['statustext'][]	= $column_list[$col_id]['name'];
				$this->uicols['exchange'][]		= false;
				$this->uicols['align'][] 		= '';
				$this->uicols['datatype'][]		= '';
				$this->uicols['sortable'][]		= $column_list[$col_id]['sortable'];
				$this->uicols['formatter'][] 	= '';
				$this->uicols['classname'][] 	= '';
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
			$values['entry_date']		= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			$values['closed_date']		= $GLOBALS['phpgw']->common->show_date($values['closed_date'],$dateformat);
			$values['in_progress_date']	= $GLOBALS['phpgw']->common->show_date($values['in_progress_date'],$dateformat);
			$values['delivered_date']	= $GLOBALS['phpgw']->common->show_date($values['delivered_date'],$dateformat);

			if($values['location_code'])
			{
				$values['location_data'] =$this->solocation->read_single($values['location_code']);
			}

			if(isset($values['planning']) && $values['planning'])
			{
				foreach ($values['planning'] as &$planning)
				{
					$planning['date'] = $GLOBALS['phpgw']->common->show_date($planning['date'],'Y');
				}
			}
			else
			{
				$values['planning'] = array();
			}
			if(isset($values['consume']) && $values['consume'])
			{
				foreach ($values['consume'] as &$consume)
				{
					$consume['date'] = $GLOBALS['phpgw']->common->show_date($consume['date'],'Y');
				}
			}
			else
			{
				$values['consume'] = array();
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
					case 'B': $type = lang('Budget changed'); break;
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
				else if ($value['status'] == 'C')
				{
					$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
					$record_history[$i]['value_old_value']	= $GLOBALS['phpgw']->accounts->id2name($value['old_value']);
				}
				else if ($value['status'] == 'CO')
				{
					$record_history[$i]['value_new_value']	= $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
				}
				else if ($value['status'] == 'T' || $value['status'] == 'TO')
				{
					$category 								= $this->cats->return_single($value['new_value']);
					$record_history[$i]['value_new_value']	= $category[0]['name'];
				}
				else if ($value['status'] == 'B' && $value['new_value'])
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
			$request['planning_date']	= phpgwapi_datetime::date_to_timestamp($request['planning_date']);
			$request['consume_date']	= phpgwapi_datetime::date_to_timestamp($request['consume_date']);

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

		public function get_user_list()
		{
			return $this->so->get_user_list();
		}
		public function get_recommended_year_list($selected = 0)
		{
			$recommended_year_list = $this->so->get_recommended_year_list();
			return $this->bocommon->select_list($selected,$recommended_year_list);
		}
	}
