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
	phpgw::import_class('phpgwapi.datetime');

	class property_bopricebook
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

		function property_bopricebook($session=false)
		{
			//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('property.sopricebook');
			$this->socommon = CreateObject('property.socommon');
			$this->bocommon = CreateObject('property.bocommon');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$allrows			= phpgw::get_var('allrows', 'bool');

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
			if(isset($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','pricebook',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','pricebook');

			//_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort	= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
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

		function read()
		{
			$pricebook = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;
			return $pricebook;
		}

		function read_agreement_group()
		{
			$agreement_group = $this->so->read_agreement_group(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;
			return $agreement_group;
		}

		function read_activity_prize($activity_id,$agreement_id)
		{
			$pricebook = $this->so->read_activity_prize(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,'activity_id'=>$activity_id,'agreement_id'=>$agreement_id));
			$this->total_records = $this->so->total_records;
			return $pricebook;
		}


		function read_activities_pr_agreement_group()
		{
			$pricebook = $this->so->read_activities_pr_agreement_group(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;
			return $pricebook;
		}

		function read_vendor_pr_activity($activity_id)
		{
			$pricebook = $this->so->read_vendor_pr_activity(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,'activity_id'=>$activity_id));
			$this->total_records = $this->so->total_records;
			return $pricebook;
		}

		function add_activity_vendor($values)
		{
			return $this->so->add_activity_vendor($values);
		}

		function read_single_activity($id='')
		{
			return $this->so->read_single_activity($id);
		}

		function read_single_agreement_group($id='')
		{
			return $this->so->read_single_agreement_group($id);
		}


		function read_category_name($cat_id)
		{
			return $this->so->read_category_name($cat_id);
		}

		function update_pricebook($values)
		{

			//_debug_array($values);
			$date_array=phpgwapi_datetime::date_array($values['date']);

			$date = mktime (2,0,0,$date_array['month'],$date_array['day'],$date_array['year']);
//			$date= date($GLOBALS['phpgw']->db->date_format(),$date);

			$new_index=str_replace(",",".",$values['new_index']);

			while($entry=@each($values['update']))
			{
				$n=$entry[0];

				if(!$values['old_total_cost'][$n])
				{
					$new_total_cost=($values['old_m_cost'][$n]+$values['old_w_cost'][$n])*$new_index;
				}
				else
				{
					$new_total_cost=$values['old_total_cost'][$n]*$new_index;
				}

				if(!$values['old_m_cost'][$n])
				{
					$new_m_cost=0;
				}
				else
				{
					$new_m_cost=$values['old_m_cost'][$n]*$new_index;
				}

				if(!$values['old_w_cost'][$n])
				{
					$new_w_cost=0;
				}
				else
				{
					$new_w_cost=$values['old_w_cost'][$n]*$new_index;
				}

				$update[]=array(
					'new_m_cost' 		=> $new_m_cost,
					'new_w_cost' 		=> $new_w_cost,
					'new_total_cost' 	=> $new_total_cost,
					'activity_id'		=> $values['activity_id'][$n],
					'agreement_id'			=> $values['agreement_id'][$n],
					'new_index'			=> $new_index,
					'new_date'			=> $date,
				);

			}
			//_debug_array($update);

			if($update)
			{
				$receipt = 	$this->so->update_pricebook($update);
			}
			else
			{
				$receipt['message'][] = array('msg'=>lang('Nothing to do!'));
			}

			return $receipt;

		}

		function add_activity_first_prize($values)
		{

			$date_array=phpgwapi_datetime::date_array($values['date']);

			$date = mktime (2,0,0,$date_array['month'],$date_array['day'],$date_array['year']);
//			$date= date($GLOBALS['phpgw']->db->date_format(),$date);

			$m_cost			= str_replace(",",".",$values['m_cost']);
			$w_cost			= str_replace(",",".",$values['w_cost']);
			$total_cost		= $m_cost + $w_cost;
			$activity_id	= $values['activity_id'][0];
			$agreement_id		= $values['agreement_id'][0];

			$receipt = $this->so->add_activity_first_prize($m_cost,$w_cost,$total_cost,$activity_id,$agreement_id,$date);

			return $receipt;
		}

		function get_vendor_list($format='',$selected='')
		{
			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
				break;
			}

			$vendors= $this->so->get_vendor_list();
			return $this->bocommon->select_list($selected,$vendors);
		}

		function get_dim_d_list($selected='')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('dim_d_select'));

			$dim_ds= $this->so->get_dim_d_list();

			return $this->bocommon->select_list($selected,$dim_ds);
		}

		function get_unit_list($selected='')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('unit_select'));

			$units= $this->so->get_unit_list();

			return $this->bocommon->select_list($selected,$units);
		}

		function get_branch_list($selected='')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('branch_select'));

			$branches= $this->so->get_branch_list();

			return $this->bocommon->select_list($selected,$branches);
		}

		function get_agreement_group_list($format='',$selected='')
		{
			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
				break;
			}

			$agreement_groups= $this->so->get_agreement_group_list();

			return $this->bocommon->select_list($selected,$agreement_groups);
		}


		function check_activity_num($num='',$agreement_group_id='')
		{
			return $this->so->check_activity_num($num,$agreement_group_id);
		}

		function save_activity($values,$action='')
		{
			if ($action=='edit')
			{
				$receipt = $this->so->edit_activity($values);
			}
			else
			{
				$values['activity_id']=$this->socommon->next_id('fm_activities');
				$receipt = $this->so->add_activity($values);
			}
			return $receipt;
		}

		function check_agreement_group_num($num='')
		{
			return $this->so->check_agreement_group_num($num);
		}

		function save_agreement_group($values,$action='')
		{
			if ($action=='edit')
			{
				$receipt = $this->so->edit_agreement_group($values);
			}
			else
			{
				$values['agreement_group_id']=$this->socommon->next_id('fm_agreement_group');
				$receipt = $this->so->add_agreement_group($values);
			}
			return $receipt;
		}

		function delete_activity_vendor($activity_id,$agreement_id)
		{
			$this->so->delete_activity_vendor($activity_id,$agreement_id);
		}

		function delete_activity($activity_id)
		{
			$this->so->delete_activity($activity_id);
		}

		function delete_prize_index($activity_id,$agreement_id,$index_count)
		{
			$this->so->delete_prize_index($activity_id,$agreement_id,$index_count);
		}

		function delete_agreement_group($agreement_group_id)
		{
			$this->so->delete_agreement_group($agreement_group_id);
		}
	}
