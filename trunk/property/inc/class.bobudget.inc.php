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
 	* @version $Id: class.bobudget.inc.php 18358 2007-11-27 04:43:37Z skwashd $
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

		var $public_functions = array
		(
			'read'				=> True,
			'read_single'		=> True,
			'save'				=> True,
			'delete'			=> True,
			'check_perms'		=> True
		);

		function property_bobudget($session=False)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('property.sobudget');
			$this->bocommon 	= CreateObject('property.bocommon');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start		= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query		= phpgw::get_var('query');
			$sort		= phpgw::get_var('sort');
			$order		= phpgw::get_var('order');
			$filter		= phpgw::get_var('filter', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$district_id	= phpgw::get_var('district_id', 'int');
			$year		= phpgw::get_var('year', 'int');
			$grouping	= phpgw::get_var('grouping', 'int');
			$revision	= phpgw::get_var('revision', 'int');
			$this->allrows = phpgw::get_var('allrows', 'bool');

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
				$this->cat_id = '';
			}

			if(isset($district_id))
			{
				$this->district_id = $district_id;
			}
			if(isset($year))
			{
				$this->year = $year;
			}
			if(isset($grouping))
			{
				$this->grouping = $grouping;
			}
			if(isset($revision))
			{
				$this->revision = $revision;
			}

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

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == True);
		}


		function read()
		{
			$budget = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
							'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,
							'district_id' => $this->district_id,'year' => $this->year,'grouping' => $this->grouping,'revision' => $this->revision,));

			$this->total_records = $this->so->total_records;

			for ($i=0; $i<count($budget); $i++)
			{
				$budget[$i]['entry_date']  = $GLOBALS['phpgw']->common->show_date($budget[$i]['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
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
			$obligations = $this->so->read_obligations(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
							'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,
							'district_id' => $this->district_id,'year' => $this->year,'grouping' => $this->grouping,'revision' => $this->revision,));

			$this->total_records = $this->so->total_records;
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
			if(!isset($_GET['year']))
			{
				$selected = date(Y);
			}
			$year_list = $this->so->get_year_filter_list($basis);
			return $this->bocommon->select_list($selected,$year_list);
		}

		function get_year_list()
		{
			$year_list = $this->so->get_year_filter_list();

			$j = count($year_list);
			for ($i=0; $i < 4; $i++)
			{
				// FIXME
				if($year_list[$j-1]['id'] < date('Y') + 3)
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
				$year = (isset($GET['year'])?$this->year:date(Y));
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
				$year = (isset($GET['year'])?$this->year:date(Y));
			}
			else
			{
				$year = $this->year;
			}
			$grouping_list = $this->so->get_grouping_filter_list($year,$basis);
			return $this->bocommon->select_list($selected,$grouping_list);
		}

	}
?>
