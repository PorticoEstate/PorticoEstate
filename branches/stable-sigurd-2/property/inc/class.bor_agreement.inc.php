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
	* @subpackage agreement
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bor_agreement
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $role;
		var $member_id;

		/**
		* @var object $custom reference to custom fields object
		*/
		protected $custom;

		var $public_functions = array
		(
			'read'				=> true,
			'read_single'		=> true,
			'save'				=> true,
			'delete'			=> true,
			'check_perms'		=> true
		);

		function property_bor_agreement($session=false)
		{
		//	$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so = CreateObject('property.sor_agreement');
			$this->bocommon = CreateObject('property.bocommon');
			$this->custom 		= createObject('property.custom_fields');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$reset_query 		= phpgw::get_var('reset_query', 'bool');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$role	= phpgw::get_var('role');

			if($reset_query)
			{
				$start	= '';
				$query	= '';
				$filter	= '';
				$cat_id	= '';
				$customer_id ='';
				$member_id	= '';
				$loc1	= '';
			}
			else
			{
				$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
				$query	= phpgw::get_var('query');
				$filter	= phpgw::get_var('filter', 'int');
				$cat_id	= phpgw::get_var('cat_id', 'int');
				$customer_id	= phpgw::get_var('tenant_id', 'int');
				$member_id	= phpgw::get_var('member_id', 'int');
				$loc1	= phpgw::get_var('loc1');
			}


			$this->role	= $role;
			$this->so->role	= $role;

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
			if(!empty($filter))
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
			if(isset($cat_id) && !empty($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			else
			{
				unset($this->cat_id);
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
			if(isset($member_id))
			{
				$this->member_id = $member_id;
			}
			if(isset($customer_id))
			{
				$this->customer_id = $customer_id;
			}
			if(isset($loc1))
			{
				$this->loc1 = $loc1;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','r_agreement',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','r_agreement');

			//_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->customer_id= $data['customer_id'];
			$this->member_id= $data['member_id'];
			$this->allrows	= $data['allrows'];
			$this->loc1	= $data['loc1'];
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == true);
		}


		function select_vendor_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('select_vendor'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('filter_vendor'));
					break;
			}

			$input_list= $this->so->select_vendor_list();
			$vendor_list= $this->bocommon->select_list($selected,$input_list);

			return $vendor_list;
		}

		function read()
		{
			$r_agreement = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,'member_id'=>$this->member_id,
											'customer_id'=>$this->customer_id, 'loc1' => $this->loc1));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($r_agreement); $i++)
			{
				if($r_agreement[$i]['start_date'])
				{
					$r_agreement[$i]['start_date']  = $GLOBALS['phpgw']->common->show_date($r_agreement[$i]['start_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}
				if($r_agreement[$i]['end_date'])
				{
					$r_agreement[$i]['end_date']  = $GLOBALS['phpgw']->common->show_date($r_agreement[$i]['end_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}
			}
			return $r_agreement;
		}

		function read_details($id)
		{
			$list = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,'member_id'=>$this->member_id,
											'r_agreement_id'=>$id,'detail'=>true));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($list); $i++)
			{
				$list[$i]['index_date']  = $GLOBALS['phpgw']->common->show_date($list[$i]['index_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			return $list;
		}

		function read_prizing($data)
		{
			$list = $this->so->read_prizing($data);
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($list); $i++)
			{
				$list[$i]['index_date']  = $GLOBALS['phpgw']->common->show_date($list[$i]['index_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$list[$i]['from_date']  = $GLOBALS['phpgw']->common->show_date($list[$i]['from_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$list[$i]['to_date']  = $GLOBALS['phpgw']->common->show_date($list[$i]['to_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				if($list[$i]['tenant_id'])
				{
					$list[$i]['tenant'] = $this->so->get_tenant_name($list[$i]['tenant_id']);
				}
			}

			return $list;
		}

		function read_event($data)
		{
			$boalarm		= CreateObject('property.boalarm');
			$event	= $this->so->read_single($data['r_agreement_id']);
			$event['alarm_date']=$event['termination_date'];
			$event['alarm']	= $boalarm->read_alarms($type='r_agreement',$data['r_agreement_id']);
			return $event;
		}

		function read_single($data)
		{

			$values['attributes'] = $this->custom->find('property', '.r_agreement', 0, '', 'ASC', 'attrib_sort', true, true);

			if(isset($data['r_agreement_id']) && $data['r_agreement_id'])
			{
				$values = $this->so->read_single($data['r_agreement_id'], $values);
			}

			$values = $this->custom->prepare($values, 'property', '.r_agreement', $data['view']);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$values['start_date']		= $GLOBALS['phpgw']->common->show_date($values['start_date'],$dateformat);
			$values['end_date']		= $GLOBALS['phpgw']->common->show_date($values['end_date'],$dateformat);
			if($values['termination_date'])
			{
				$values['termination_date']= $GLOBALS['phpgw']->common->show_date($values['termination_date'],$dateformat);
			}

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$values['files'] = $vfs->ls (array(
			     'string' => '/property/rental_agreement/' . $data['r_agreement_id'],
			     'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			if(!$values['files'][0]['file_id'])
			{
				unset($values['files']);
			}

			return $values;
		}

		function read_single_item($data)
		{
			$values['attributes'] = $this->custom->find('property', '.r_agreement.detail', 0, '', 'ASC', 'attrib_sort', true, true);

			if(isset($data['r_agreement_id']) && $data['r_agreement_id'] && isset($data['id']) && $data['id'])
			{
				$values = $this->so->read_single_item($data, $values);
			}
			$values = $this->custom->prepare($values, 'property', '.r_agreement.detail');

			if($values['location_code'])
			{
				$solocation	= CreateObject('property.solocation');
				$values['location_data'] =$solocation->read_single($values['location_code']);
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
			return $values;
		}

		function save($values,$values_attribute='',$action='')
		{

			$values['start_date']	= $this->bocommon->date_to_timestamp($values['start_date']);
			$values['end_date']	= $this->bocommon->date_to_timestamp($values['end_date']);
			$values['termination_date']	= $this->bocommon->date_to_timestamp($values['termination_date']);

			if(is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($action=='edit')
//			if ($values['r_agreement_id'])
			{
				if ($values['r_agreement_id'] != 0)
				{
					$receipt=$this->so->edit($values,$values_attribute);
				}
			}
			else
			{
				$receipt = $this->so->add($values,$values_attribute);
			}
			return $receipt;
		}

		function save_item($values,$values_attribute='')
		{

			if($values['start_date'])
			{
				$values['start_date']	= $this->bocommon->date_to_timestamp($values['start_date']);
			}

			if($values['start_date'])
			{
				$values['end_date']	= $this->bocommon->date_to_timestamp($values['end_date']);
			}

			while (is_array($values['location']) && list(,$value) = each($values['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$values['location_code']=@implode("-", $location);

			if(is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($values['id'])
			{
				if ($values['id'] != 0)
				{
					$receipt=$this->so->edit_item($values,$values_attribute);
				}
			}
			else
			{
				$receipt = $this->so->add_item($values,$values_attribute);
			}
			return $receipt;
		}


		function update_item_history($values)
		{
			if($values['start_date'])
			{
				$values['start_date']	= $this->bocommon->date_to_timestamp($values['start_date']);
			}

			if($values['start_date'])
			{
				$values['end_date']	= $this->bocommon->date_to_timestamp($values['end_date']);
			}

			$values['date']	= $this->bocommon->date_to_timestamp($values['date']);

			return $this->so->update_item_history($values);
		}

		function delete_last_index($r_agreement_id,$id)
		{
			$this->so->delete_last_index($r_agreement_id,$id);
		}


		function delete_item($r_agreement_id,$item_id)
		{
			$this->so->delete_item($r_agreement_id,$item_id);
		}

		function delete($r_agreement_id='')
		{
			$this->so->delete($r_agreement_id);
		}

		function read_attrib($type_id='')
		{
			$attrib = $this->so->read_attrib(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));

			for ($i=0; $i<count($attrib); $i++)
			{
				$attrib[$i]['datatype'] = $this->bocommon->translate_datatype($attrib[$i]['datatype']);
			}

			$this->total_records = $this->so->total_records;

			return $attrib;
		}


		function column_list($selected='',$allrows='')
		{
			if(!$selected)
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences']['property']['r_agreement_columns'];
			}
			$columns = $this->custom->find('property','.r_agreement', 0, '','','',true);
			$column_list=$this->bocommon->select_multi_list($selected,$columns);
			return $column_list;
		}

		function request_next_id()
		{
				return $this->so->request_next_id();
		}

		function get_rental_type_list($selected = '')
		{
			$input_list[0]['id'] = 1;
			$input_list[0]['name'] = lang('plain');
			$input_list[1]['id'] = 2;
			$input_list[1]['name'] = lang('Floor common');
			$input_list[2]['id'] = 3;
			$input_list[2]['name'] = lang('Building common');
			$input_list[3]['id'] = 4;
			$input_list[3]['name'] = lang('Shared use');

			$rental_type_list= $this->bocommon->select_list($selected,$input_list);

			return $rental_type_list;
		}

		function get_rental_type_list2($selected = '')
		{
			$rental_type = array(
			1 => lang('plain'),
			2 => lang('Floor common'),
			3 => lang('Building common'),
			4 => lang('Shared use'),
			);

			return $rental_type[$selected];
		}

		function read_common($id)
		{
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$values	= $this->so->read_common($id);

			$this->total_records = $this->so->total_records;

			for ($i=0; $i<count($values); $i++)
			{
				$values[$i]['from_date']  = $GLOBALS['phpgw']->common->show_date($values[$i]['from_date'],$dateformat);
				$values[$i]['to_date']  = $GLOBALS['phpgw']->common->show_date($values[$i]['to_date'],$dateformat);
			}


			return $values;
		}

		function read_single_common($data)
		{
			$values	= $this->so->read_single_common($data);

			return $values;
		}
		function read_common_history($data)
		{
			$values	= $this->so->read_common_history($data);
			$this->total_records = $this->so->total_records;
			for ($i=0; $i<count($values); $i++)
			{
				$values[$i]['from_date']  = $GLOBALS['phpgw']->common->show_date($values[$i]['from_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$values[$i]['to_date']  = $GLOBALS['phpgw']->common->show_date($values[$i]['to_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			return $values;
		}

		function save_common($values)
		{
			if($values['start_date'])
			{
				$values['start_date']	= $this->bocommon->date_to_timestamp($values['start_date']);
			}

			if($values['start_date'])
			{
				$values['end_date']	= $this->bocommon->date_to_timestamp($values['end_date']);
			}

			if ($values['c_id'])
			{
				if ($values['c_id'] != 0)
				{
					$receipt=$this->so->add_common_history($values);
				}
			}
			else
			{
				$receipt = $this->so->add_common($values);
			}
			return $receipt;
		}
		function delete_common_h($r_agreement_id,$c_id,$id)
		{
			$this->so->delete_common_h($r_agreement_id,$c_id,$id);
		}
	}

