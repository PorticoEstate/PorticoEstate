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
	* @subpackage budget
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bobudget
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $district_id;
		var $year;
		var $grouping;
		var $revision;
		var $allrows;
		var $details;
		var	$sum_budget_cost		= 0;
		var	$sum_obligation_cost	= 0;
		var	$sum_actual_cost		= 0;
		var	$sum_actual_cost_period		= 0;
		var $sum_hits				= 0;
		var	$total_records			= 0;

		var $public_functions = array
			(
				'read'				=> true,
				'read_single'		=> true,
				'save'				=> true,
				'delete'			=> true,
				'check_perms'		=> true
			);

		function property_bobudget($session=false)
		{
			$this->so 				= CreateObject('property.sobudget');
			$this->bocommon 		= CreateObject('property.bocommon');
			$this->cats				= & $this->so->cats;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$filter					= phpgw::get_var('filter', 'int');
			$cat_id					= phpgw::get_var('cat_id', 'int');
			$dimb_id				= phpgw::get_var('dimb_id', 'int');
			$department				= phpgw::get_var('department', 'int');
			$allrows				= phpgw::get_var('allrows', 'bool');
			$district_id			= phpgw::get_var('district_id', 'int');
			$year					= phpgw::get_var('year', 'int');
			$month					= phpgw::get_var('month', 'int');
			$grouping				= phpgw::get_var('grouping', 'int');
			$revision				= phpgw::get_var('revision', 'int');
			$allrows				= phpgw::get_var('allrows', 'bool');
			$details				= phpgw::get_var('details', 'bool');
			$direction				= phpgw::get_var('direction');

			$this->start			= $start;
			$this->query			= $query ? $query : $this->query;
			$this->direction		= $direction ? $direction : $this->direction;
			if( !$this->direction )
			{
				$this->direction = 'expenses';
			}
			
			$this->filter			= isset($filter) && $filter ? $filter : '';
			$this->sort				= isset($sort) && $sort ? $sort : '';
			$this->order			= isset($order) && $order ? $order : '';
			$this->cat_id			= isset($cat_id) && $cat_id ? $cat_id : '';
			$this->dimb_id			= isset($dimb_id) && $dimb_id ? $dimb_id : '';//$GLOBALS['phpgw_info']['user']['preferences']['property']['dimb'];
			$this->department		= isset($department) && $department ? $department : '';//$GLOBALS['phpgw_info']['user']['preferences']['property']['department'];

			$this->part_of_town_id	= isset($part_of_town_id) && $part_of_town_id ? $part_of_town_id : '';
			$this->district_id		= isset($district_id) && $district_id ? $district_id : '';
			$this->grouping			= isset($grouping) && $grouping ? $grouping : '';
			$this->revision			= isset($revision) && $revision ? $revision : 1;
			$this->allrows			= isset($allrows) && $allrows ? $allrows : '';
			$this->year				= isset($year) && $year ? $year : date('Y');
			$this->month			= isset($month) && $month ? $month : 0;
			$this->details			= $details;

			if(isset($year) && !$this->year == $year && !$GLOBALS['phpgw_info']['menuaction']=='property.uibudget.obligations')
			{
				$this->grouping = '';
				$this->revision = '';
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','budget',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','budget');

			$this->start			= isset($data['start'])?$data['start']:'';
			$this->filter			= isset($data['filter'])?$data['filter']:'';
			$this->sort				= isset($data['sort'])?$data['sort']:'';
			$this->order			= isset($data['order'])?$data['order']:'';;
			$this->cat_id			= isset($data['cat_id'])?$data['cat_id']:'';
			$this->dimb_id			= isset($data['dimb_id'])?$data['dimb_id']:'';
			$this->details			= isset($data['details'])?$data['details']:'';
			$this->direction		= isset($data['direction'])?$data['direction']:'';
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == true);
		}


		function read()
		{
			$budget = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,
				'district_id' => $this->district_id,'year' => $this->year,'grouping' => $this->grouping,'revision' => $this->revision,
				'cat_id' => $this->cat_id, 'dimb_id' => $this->dimb_id, 'department' => $this->department));

			$this->total_records		= $this->so->total_records;
			$this->sum_budget_cost		= $this->so->sum_budget_cost;
			foreach ($budget as & $entry)
			{
//				$entry['entry_date']	= $GLOBALS['phpgw']->common->show_date($entry['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$category = $this->cats->return_single($entry['cat_id']);
				$entry['category']		=$category[0]['name'];
			}

			return $budget;
		}

		function read_basis()
		{
			$budget = $this->so->read_basis(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,
				'district_id' => $this->district_id,'year' => $this->year,'grouping' => $this->grouping,'revision' => $this->revision,));

			$this->total_records = $this->so->total_records;

			for ($i=0; $i<count($budget); $i++)
			{
				$budget[$i]['entry_date']  = $GLOBALS['phpgw']->common->show_date($budget[$i]['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}
			return $budget;
		}


		function read_obligations()
		{
			//cramirez: add strtoupper function for $this->sort. in YUI use asc/desc (lowercase letters)
			$obligations = $this->so->read_obligations(array('start' => $this->start, 'query' => $this->query,
				'sort' => strtoupper($this->sort), 'order' => $this->order, 'filter' => $this->filter,
				'cat_id' => $this->cat_id, 'allrows'=>$this->allrows, 'district_id' => $this->district_id,
				'year' => $this->year,'month' => $this->month, 'grouping' => $this->grouping, 'revision' => $this->revision,
				'details' => $this->details,'dimb_id' => $this->dimb_id, 'department' => $this->department,
				'direction'	=> $this->direction));

			$this->total_records			= $this->so->total_records;
			$this->sum_budget_cost			= $this->so->sum_budget_cost;
			$this->sum_obligation_cost		= $this->so->sum_obligation_cost;
			$this->sum_actual_cost			= $this->so->sum_actual_cost;
			$this->sum_actual_cost_period	= $this->so->sum_actual_cost_period;
			$this->sum_hits					= $this->so->sum_hits;

			return $obligations;
		}

		function read_single($budget_id)
		{
			return $this->so->read_single($budget_id);
		}

		function read_single_basis($budget_id)
		{
			return $this->so->read_single_basis($budget_id);
		}

		function read_budget_name($budget_id)
		{
			return $this->so->read_budget_name($budget_id);
		}

		function save($budget)
		{

			if ($budget['budget_id'])
			{
				if ($budget['budget_id'] != 0)
				{
					$budget_id = $budget['budget_id'];
					$receipt=$this->so->edit($budget);
				}
			}
			else
			{
				$receipt = $this->so->add($budget);
			}
			return $receipt;
		}

		function save_basis($values)
		{
			if ($values['budget_id'])
			{
				if ($values['budget_id'] != 0)
				{
					$budget_id = $values['budget_id'];
					$receipt=$this->so->edit_basis($values);
				}
			}
			else
			{
				$receipt = $this->so->add_basis($values);
			}

			if(is_array($values['distribute']) && is_array($values['distribute_year']) && (!isset($receipt['error']) || !$receipt['error']))
			{
				if($values['distribute'][0]=='new')
				{
					$values['distribute'][0]= $receipt['budget_id'];
				}
				$this->distribute($values,$receipt);
			}
			return $receipt;
		}

		function distribute($values,$receipt='')
		{
			return $this->so->distribute($values,$receipt);
		}

		function delete($params)
		{
			if (is_array($params))
			{
				$this->so->delete($params[0]);
			}
			else
			{
				$this->so->delete($params);
			}
		}

		function delete_basis($params)
		{
			if (is_array($params))
			{
				$this->so->delete_basis($params[0]);
			}
			else
			{
				$this->so->delete_basis($params);
			}
		}

		function get_distribute_year_list($selected ='')
		{
			$distribute_year_list = $this->so->get_distribute_year_list();
			return $this->bocommon->select_multi_list($selected,$distribute_year_list);
		}

		function get_b_group_list($selected ='')
		{
			$b_group_list = $this->so->get_b_group_list();
			return $this->bocommon->select_list($selected,$b_group_list);
		}

		function get_revision_list($selected ='',$year='',$basis = '')
		{
			$revision_list = $this->so->get_revision_list($year,$basis);
			return $this->bocommon->select_list($selected,$revision_list);
		}

		function get_year_filter_list($selected ='',$basis = '')
		{
			$year_list = $this->so->get_year_filter_list($basis);
			return $this->bocommon->select_list($selected,$year_list);
		}

		function get_year_list()
		{
			$year_list = $this->so->get_year_filter_list();

			if(!$year_list)
			{
				$year_list = array(array('id' =>date('Y'), 'name' =>date('Y')));
			}
			$k = date('Y') - $year_list[0]['id'] + 5;
			$j = count($year_list);
			for ($i=0; $i < $k; $i++)
			{
				// FIXME
				//	if($year_list[$j-1]['id'] < date('Y') + 3)
				{
					$year_list[$j+$i]['id'] = $year_list[$j+$i-1]['id'] + 1;
					$year_list[$j+$i]['name'] = $year_list[$j+$i-1]['id'] + 1;
				}
			}
			return $year_list;
		}

		function get_revision_filter_list($selected ='',$basis = '')
		{
			if(!isset($_GET['year']))
			{
				$year = date('Y');
				$this->year = $year;
				$selected = $this->so->get_max_revision($year,$basis);
				$this->revision = $selected;
			}
			else
			{
				$year = $this->year;
			}

			$revision_list = $this->so->get_revision_filter_list($year,$basis);
			return $this->bocommon->select_list($selected,$revision_list);

		}

		function get_grouping_filter_list($selected ='',$basis = '')
		{
			if(!isset($_GET['year']))
			{
				$year = date('Y');
			}
			else
			{
				$year = $this->year;
			}
			$grouping_list = $this->so->get_grouping_filter_list($year,$basis);
			return $this->bocommon->select_list($selected,$grouping_list);
		}

	}
