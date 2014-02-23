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

	class property_sobudget
	{
		var	$sum_budget_cost			= 0;
		var	$sum_obligation_cost		= 0;
		var	$sum_actual_cost_period		= 0;
		var	$sum_actual_cost			= 0;
		var $sum_hits					= 0;

		function __construct()
		{
			$this->cats					= CreateObject('phpgwapi.categories', -1,  'property', '.project');
			$this->cats->supress_info	= true;

			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
		}


		/**
		 * Get a list of categories , included subs
		 *
		 * @param int $cat_id the parent doc-type
		 * @return array parent and children
		 */

		function get_sub_cats($cat_id = 0)
		{
			$cat_ids = array();
			if($cat_id)
			{
				$cat_ids[] = $cat_id;
				$cat_sub = $this->cats->return_sorted_array($start = 0,$limit = false,$query = '',$sort = '',$order = '',$globals = False, $parent_id = $cat_id);
				foreach ($cat_sub as $category)
				{
					$cat_ids[] = $category['id'];
				}
			}
			return $cat_ids;
		}


		function read($data)
		{
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter			= isset($data['filter']) && $data['filter'] ? $data['filter'] : 'none';
				$query			= isset($data['query']) ? $data['query'] : '';
				$sort			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
				$order			= isset($data['order']) ? $data['order'] : '';
				$allrows		= isset($data['allrows']) ? $data['allrows'] : '';
				$district_id	= isset($data['district_id']) ? $data['district_id'] : '';
				$year			= isset($data['year']) && $data['year'] ? (int) $data['year'] : 0;
				$grouping		= isset($data['grouping']) ? $data['grouping'] : '';
				$revision		= isset($data['revision']) ? $data['revision'] : '';
				$cat_id			= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id']: 0;
				$dimb_id			= isset($data['dimb_id']) && $data['dimb_id'] ? $data['dimb_id']: 0;
			}

			$cat_ids = $this->get_sub_cats($cat_id);


			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY id DESC';
			}


			$where = 'WHERE';

			if ($district_id > 0)
			{
				$filtermethod .= " $where district_id='$district_id' ";
				$where = 'AND';

			}
			if ($year > 0)
			{
				$filtermethod .= " $where year='$year' ";
				$where = 'AND';

			}
			if ($grouping > 0)
			{
				$filtermethod .= " $where fm_b_account.category='$grouping' ";
				$where = 'AND';

			}
			if ($revision > 0)
			{
				$filtermethod .= " $where revision='$revision' ";
				$where = 'AND';

			}

			if ($cat_ids && is_array($cat_ids))
			{
				$filtermethod .= " $where fm_budget.category IN (". implode(',', $cat_ids) . ')';
				$where = 'AND';
			}

			if ($dimb_id > 0)
			{
				$filtermethod .= " $where fm_budget.ecodimb={$dimb_id}";
				$where = 'AND';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where ( descr {$this->like} '%$query%' OR fm_budget.b_account_id='$query')";
			}


			$sql = "SELECT fm_budget.*, fm_budget.category as cat_id, ecodimb, descr,fm_b_account.category as grouping"
			. " FROM fm_budget {$this->join} fm_b_account ON fm_budget.b_account_id = fm_b_account.id"
			. " $filtermethod $querymethod";

			if($GLOBALS['phpgw_info']['server']['db_type']=='postgres')
			{
				$sql_count = 'SELECT count(id) as cnt, sum(budget_cost) AS sum_budget_cost FROM (SELECT DISTINCT fm_budget.id, budget_cost '. substr($sql,strripos($sql,'FROM')) .') AS t';
				$this->db->query($sql_count,__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records 		= $this->db->f('cnt');
				$this->sum_budget_cost		= $this->db->f('sum_budget_cost');
			}
			else
			{
				$sql_count = 'SELECT count(fm_budget.id) as cnt, sum(budget_cost) AS sum_budget_cost ' . substr($sql,strripos($sql,'FROM'));
				$this->db->query($sql_count,__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records 		= $this->db->f('cnt');
				$this->sum_budget_cost		= $this->db->f('sum_budget_cost');
			}

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$budget = array();
			while ($this->db->next_record())
			{
				$budget[] = array
					(
						'budget_id'			=> $this->db->f('id'),
						'year'				=> $this->db->f('year'),
						'grouping'			=> $this->db->f('grouping'),
						'b_account_id'		=> $this->db->f('b_account_id'),
						'b_account_name'	=> $this->db->f('descr'),
						'district_id'		=> $this->db->f('district_id'),
						'revision'			=> $this->db->f('revision'),
						'budget_cost'		=> $this->db->f('budget_cost'),
						'entry_date'		=> $this->db->f('entry_date'),
						'ecodimb'			=> $this->db->f('ecodimb'),
						'cat_id'			=> $this->db->f('cat_id'),
			//			'user'				=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('user_id'))
					);
			}
			return $budget;
		}

		function read_basis($data)
		{
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start']:0;
				$filter			= isset($data['filter']) && $data['filter'] ?$data['filter']:'none';
				$query			= isset($data['query'])?$data['query']:'';
				$sort			= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order			= isset($data['order'])?$data['order']:'';
				$allrows		= isset($data['allrows'])?$data['allrows']:'';
				$district_id	= isset($data['district_id'])?$data['district_id']:'';
				$year			= isset($data['year'])?$data['year']:'';
				$grouping		= isset($data['grouping'])?$data['grouping']:'';
				$revision		= isset($data['revision'])?$data['revision']:'';
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by id DESC';
			}


			$where = 'WHERE';

			if ($district_id > 0)
			{
				$filtermethod .= " $where district_id='$district_id' ";
				$where = 'AND';

			}
			if ($year > 0)
			{
				$filtermethod .= " $where year='$year' ";
				$where = 'AND';

			}
			if ($grouping > 0)
			{
				$filtermethod .= " $where b_group='$grouping' ";
				$where = 'AND';

			}
			if ($revision > 0)
			{
				$filtermethod .= " $where revision='$revision' ";
				$where = 'AND';

			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				//	$querymethod = " $where ( descr $this->like '%$query%')";
			}


			$sql = "SELECT * FROM fm_budget_basis $filtermethod $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$budget = array();
			while ($this->db->next_record())
			{
				$budget[] = array
					(
						'budget_id'		=> $this->db->f('id'),
						'year'			=> $this->db->f('year'),
						'grouping'		=> $this->db->f('b_group'),
						'district_id'	=> $this->db->f('district_id'),
						'revision'		=> $this->db->f('revision'),
						'budget_cost'	=> $this->db->f('budget_cost'),
						'entry_date'	=> $this->db->f('entry_date'),
						'ecodimb'		=> $this->db->f('ecodimb'),
						'cat_id'		=> $this->db->f('category'),
					);
			}
			return $budget;
		}

		function read_single_basis($budget_id)
		{
			$budget_id = (int) $budget_id;
			$this->db->query("SELECT * FROM fm_budget_basis WHERE id = {$budget_id}",__LINE__,__FILE__);

			$budget = array();
			if ($this->db->next_record())
			{
				$budget = array
					(
						'id'				=> $budget_id,
						'year'				=> $this->db->f('year'),
						'district_id'		=> $this->db->f('district_id'),
						'revision'			=> $this->db->f('revision'),
						'b_group'			=> $this->db->f('b_group'),
						'remark'			=> $this->db->f('remark',true),
						'budget_cost'		=> $this->db->f('budget_cost'),
						'entry_date'		=> $this->db->f('entry_date'),
						'distribute_year'	=> unserialize($this->db->f('distribute_year')),
						'ecodimb'			=> $this->db->f('ecodimb'),
						'cat_id'			=> $this->db->f('category')
					);
			}

			return $budget;
		}


		function add_basis($budget)
		{
			$receipt = array();
			$budget['remark'] = $this->db->db_addslashes($budget['remark']);

			$this->db->transaction_begin();

			$sql = "SELECT id FROM fm_budget_basis WHERE year ='" . $budget['year'] . "'  AND b_group ='" . $budget['b_group'] . "' AND revision = '" . $budget['revision'] . "' AND district_id='" . $budget['district_id'] . "'";
			$this->db->query($sql,__LINE__,__FILE__);

			if($this->db->next_record())
			{
				$receipt['error'][] = array('msg'=>lang('budget %1 already saved',$this->db->f('id')));
				$receipt['budget_id']= $this->db->f('id');
				return $receipt;
			}

			if(!$receipt['error'])
			{
				$id = $this->db->next_id('fm_budget_basis');

				$values= array(
					$id,
					time(),
					$budget['remark'],
					$this->account,
					$budget['year'],
					$budget['revision'],
					$budget['district_id'],
					$budget['b_group'],
					$budget['budget_cost'],
					serialize($budget['distribute_year']),
					$budget['ecodimb'],
					$budget['cat_id']
				);

				$values	= $this->db->validate_insert($values);


				$this->db->query("INSERT INTO fm_budget_basis (id,entry_date,remark,user_id,year,revision,district_id,b_group,budget_cost,distribute_year,ecodimb,category)"
					. "VALUES ($values)",__LINE__,__FILE__);

				$receipt['budget_id']= $id;
				$receipt['message'][] = array('msg'=>lang('budget %1 has been saved',$receipt['budget_id']));
			}

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit_basis($budget)
		{

			$budget['remark'] = $this->db->db_addslashes($budget['remark']);

			$value_set=array
				(
					'remark'			=> $budget['remark'],
					'entry_date'		=> time(),
					'budget_cost'		=> $budget['budget_cost'],
					'distribute_year'	=> serialize($budget['distribute_year']),
					'ecodimb'			=> $budget['ecodimb'],
					'category'			=> $budget['cat_id'],
				);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->transaction_begin();
			$this->db->query("UPDATE fm_budget_basis set $value_set WHERE id=" . intval($budget['budget_id']),__LINE__,__FILE__);
			$this->db->transaction_commit();

			if(is_array($budget['distribute_year']))
			{
				$budget['distribute'][0] = $budget['budget_id'];
				$this->distribute($budget);
			}

			$receipt['budget_id']= $budget['budget_id'];
			$receipt['message'][] = array('msg'=>lang('budget %1 has been edited',$budget['budget_id']));
			return $receipt;
		}

		function read_single($budget_id)
		{
			$this->db->query("select * from fm_budget where id='$budget_id'",__LINE__,__FILE__);

			$budget = array();
			if ($this->db->next_record())
			{
				$budget = array
					(
						'id'			=> (int)$this->db->f('id'),
						'year'			=> $this->db->f('year'),
						'district_id'	=> $this->db->f('district_id'),
						'revision'		=> $this->db->f('revision'),
						'b_account_id'	=> $this->db->f('b_account_id'),
						'remark'		=> $this->db->f('remark', true),
						'budget_cost'	=> $this->db->f('budget_cost'),
						'entry_date'	=> $this->db->f('entry_date'),
						'ecodimb'			=> $this->db->f('ecodimb'),
						'cat_id'			=> $this->db->f('category')
					);
			}

			return $budget;
		}

		function add($budget)
		{
			$receipt = array();
			$budget['remark'] = $this->db->db_addslashes($budget['remark']);

			$this->db->transaction_begin();

/*
			if($budget['district_id'])
			{
				$district_filter =  "AND district_id='{$budget['district_id']}'";
			}
			$sql = "SELECT id FROM fm_budget WHERE year ='{$budget['year']}' AND b_account_id ='{$budget['b_account_id']}' AND revision = '{$budget['revision']}' {$district_filter}";

			$this->db->query($sql,__LINE__,__FILE__);

			if($this->db->next_record())
			{
				$receipt['error'][] = array('msg'=>lang('budget %1 already saved',$this->db->f('id')));
			}
*/
			if(!$receipt['error'])
			{
				$id = $this->db->next_id('fm_budget');

				$values= array(
					$id,
					time(),
					$budget['remark'],
					$this->account,
					$budget['year'],
					$budget['revision'],
					$budget['district_id'],
					$budget['b_account_id'],
					(int)$budget['budget_cost'],
					$budget['ecodimb'],
					$budget['cat_id']
				);

				$values	= $this->db->validate_insert($values);

				$this->db->query("INSERT INTO fm_budget (id,entry_date,remark,user_id,year,revision,district_id,b_account_id,budget_cost,ecodimb,category)"
					. "VALUES ($values)",__LINE__,__FILE__);

				$receipt['budget_id']= $id;
				$receipt['message'][] = array('msg'=>lang('budget %1 has been saved',$receipt['budget_id']));
			}

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit($budget)
		{
			$receipt = array();
			$budget['remark'] = $this->db->db_addslashes($budget['remark']);

			$this->db->transaction_begin();

			$value_set = array
				(
					'remark'		=> $budget['remark'],
					'entry_date'	=> time(),
					'budget_cost'	=> (int)$budget['budget_cost'],
					'year'			=> $budget['year'],
					'revision'		=> $budget['revision'],
					'district_id'	=> $budget['district_id'],
					'ecodimb'		=> $budget['ecodimb'],
					'category'		=> $budget['cat_id']
				);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE fm_budget set $value_set WHERE id=" . intval($budget['budget_id']),__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['budget_id']= $budget['budget_id'];
			$receipt['message'][] = array('msg'=>lang('budget %1 has been edited',$budget['budget_id']));
			return $receipt;
		}

		function read_obligations($data)
		{
//			_debug_array($data);
			$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$filter			= isset($data['filter']) ? $data['filter'] : 'none';
			$query			= isset($data['query']) ? $data['query'] : '';
			$sort			= isset($data['sort']) ? $data['sort'] : 'DESC';
			$order			= isset($data['order']) ? $data['order'] : '';
			$allrows		= isset($data['allrows']) ? $data['allrows'] : '';
			$filter_district_id	= isset($data['district_id']) && $data['district_id'] ? (int)$data['district_id'] : 0;
			$grouping		= isset($data['grouping']) ? $data['grouping'] : '';
			$revision		= isset($data['revision']) ? $data['revision'] : 1;
			$year			= isset($data['year']) &&  $data['year'] ? (int)$data['year'] : 0;
			$month			= isset($data['month']) &&  $data['month'] ? (int)$data['month'] : 0;
			$cat_id			= isset($data['cat_id']) ? $data['cat_id'] : '';
			$details		= isset($data['details']) ? $data['details'] : '';
			$dimb_id		= isset($data['dimb_id'])  && $data['dimb_id'] ? (int)$data['dimb_id'] : 0;
			$department		= isset($data['department'])  && $data['department'] ? (int)$data['department'] : 0;
			$direction		= isset($data['direction'])  && $data['direction'] ? $data['direction'] : 'expenses';

			if(!$year)
			{
				return array();
			}

			$filter_period = $month ? sprintf("%s%02d",$year,$month) : '';

			$filtermethod_direction = '';

			if($direction == 'income')
			{
				$filtermethod_direction = "fm_b_account.id {$this->like} '3%'";
			}
			else
			{
				$filtermethod_direction = "fm_b_account.id NOT {$this->like} '3%'";
			}

			/* 0 => cancelled, 1 => obligation , 2 => paid */

			$filtermethod = '';

			$start_date = mktime(1, 1, 1, 1, 1, $year);
			$end_date = mktime  (23, 59, 59, 12, 31, $year);

			$start_periode = date('Ym',mktime(2,0,0,1,1,$year));
			$end_periode = date('Ym',mktime(2,0,0,12,31,$year));


			$filtermethod = '';
			$filtermethod_order = " WHERE (fm_workorder_budget.year = {$year} OR fm_workorder_status.closed IS NULL)";

			$where = 'AND';

			$cat_ids = array();
			if ($cat_id > 0)
			{
				$cat_ids = $this->get_sub_cats($cat_id);
			}

			if($cat_ids)
			{
				$filtermethod .= " {$where} fm_workorder.category IN (". implode(',', $cat_ids) . ')';
				$where = 'AND';
			}

			if ($filter_district_id > 0)
			{
				$filtermethod .= " {$where} district_id=" . (int)$filter_district_id;
				$where = 'AND';
			}

			if ($dimb_id > 0)
			{
				$filtermethod .= " {$where} fm_project.ecodimb={$dimb_id}";
				$where = 'AND';
			}

			$_department_dimb = array();
			if ($department > 0)
			{
				$_department_dimb[] = -1;//block in case no one found
				$this->db->query("SELECT id FROM fm_ecodimb WHERE department = $department ",__LINE__,__FILE__);
				while ($this->db->next_record())
				{
					$_department_dimb[] = $this->db->f('id');
				}
			}

			if($_department_dimb)
			{
				$filtermethod .= " $where fm_project.ecodimb IN (" . implode(',', $_department_dimb) . ')';
				$where = 'AND';
			}

			if ($grouping > 0)
			{
				$filtermethod .= " $where fm_b_account.category='{$grouping}' ";
				$where = 'AND';
			}

			$querymethod = '';
/*			if($query)
			{
				$query = $this->db->db_addslashes($query);
			}
 */
			$config						= CreateObject('phpgwapi.config','property');
			$config->read();

			if(isset($config->config_data['location_at_workorder']) && $config->config_data['location_at_workorder'])
			{
				$_join_district =	"{$this->join} fm_locations ON fm_workorder.location_code = fm_locations.location_code"
									. " {$this->join} fm_location1 ON fm_location1.loc1 = fm_locations.loc1";
			}
			else
			{
				$_join_district = "{$this->join} fm_location1 ON fm_project.loc1 = fm_location1.loc1";
			}

			if( $details )
			{
				$b_account_field = 'id';
			}
			else
			{
				$b_account_field = 'category';
			}

			$this->db->query('SELECT id, percent FROM fm_ecomva',__LINE__,__FILE__);
			$_taxcode = array(0 => 0);
			while ($this->db->next_record())
			{
				$_taxcode[$this->db->f('id')] = $this->db->f('percent');
			}

			$sql = "SELECT DISTINCT fm_workorder.id AS id, fm_location1.mva,project_id,"
				. " fm_b_account.{$b_account_field} AS b_account, district_id, fm_project.ecodimb"
				. " FROM fm_workorder"
				. " {$this->join} fm_workorder_status ON fm_workorder.status = fm_workorder_status.id"
				. " {$this->join} fm_workorder_budget ON (fm_workorder.id = fm_workorder_budget.order_id)"
				. " {$this->join} fm_b_account ON fm_workorder.account_id = fm_b_account.id"
				. " {$this->join} fm_project ON  fm_workorder.project_id = fm_project.id"
				. " {$_join_district}"
				. " {$this->join} fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id"
				. " {$filtermethod_order}{$filtermethod} {$querymethod} {$where} {$filtermethod_direction}"
				. " ORDER BY fm_workorder.id ASC";

//			_debug_array($sql);
			//die();
			$this->db->query($sql,__LINE__,__FILE__);
			$_temp_paid_info = array();
$projects = array();


			while ($this->db->next_record())
			{

				$_id = $this->db->f('id');
				$district_id = $filter_district_id ? (int)$this->db->f('district_id') : 0;


$projects[$this->db->f('project_id')] = 0;
$projects2[$_id] = $this->db->f('project_id');

				$_temp_paid_info[$_id] = array
				(
					'actual_cost'			=> 0,
					'mva'					=> (int)$this->db->f('mva'),
					'district_id'			=> $district_id,
					'ecodimb'				=> (int)$this->db->f('ecodimb'),
					'b_account'				=> $this->db->f('b_account'),
				);
			}
ksort($projects);
//_debug_array(count($projects2));
//_debug_array($projects2);

			$soworkorder = CreateObject('property.soworkorder');

			$sum_actual_cost_period = 0;
			$sum_actual_cost = 0;
			$actual_cost = array();
			$actual_cost_period = array();
			$sum_obligation_cost = 0;
			$obligations = array();
			$sum_hits = 0;

			$sum_hits = count($_temp_paid_info);
$_periods=array();
			foreach ($_temp_paid_info as $order_id => &$order_info)
			{
				$order_budget = $soworkorder->get_budget($order_id);

				$_count = false;
				foreach($order_budget as $budget)
				{
					if($budget['year'] == $year)
					{

						if($budget['period'] != "{$year}00" && $filter_period && ((int)$filter_period) < (int)$budget['period'])
						{
							break;
						}

						$_taxfactor		= 1 + ($_taxcode[(int)$order_info['mva']]/100);
						$_actual_cost	= round($budget['actual_cost']/$_taxfactor);

						$sum_actual_cost += $_actual_cost;
						if((int)$budget['actual_period']==(int)$filter_period)
						{
							$actual_cost_period[$order_info['b_account']][$order_info['district_id']][$order_info['ecodimb']] += $_actual_cost;
							$sum_actual_cost_period += $_actual_cost;
						}
//$_periods[] = $budget['actual_period'];

						$sum_obligation_cost += $budget['sum_oblications'];
						$obligations[$order_info['b_account']][$order_info['district_id']][$order_info['ecodimb']] += $budget['sum_oblications'];

						$actual_cost[$order_info['b_account']][$order_info['district_id']][$order_info['ecodimb']] += $_actual_cost;
					}

					if(!$_count)
					{
						$hits[$order_info['b_account']][$order_info['district_id']][$order_info['ecodimb']] += 1;
						$accout_info[$order_info['b_account']] = true;
						$district[$order_info['district_id']] = true;
						$ecodimb[$order_info['ecodimb']] = true;
						$_count = true;
					}
				}
			}

//_debug_array($_periods);
//			_debug_array($obligations);

			//----------- ad hoc order
			$filtermethod = "WHERE fm_tts_tickets.vendor_id > 0 AND budget > 0";

			//			$start_date = mktime(1, 1, 1, 1, 1, $year);
			//			$end_date = mktime  (23, 59, 59, 12, 31, $year);
			$filtermethod .= " AND fm_tts_tickets.entry_date >= $start_date AND fm_tts_tickets.entry_date <= $end_date";
			$filtermethod2 = " AND fm_tts_tickets.actual_cost = '0.00' ";

			$where = 'AND';

			if($cat_ids)
			{
				$filtermethod .= " $where fm_tts_tickets.cat_id IN (". implode(',', $cat_ids) . ')';
				$where = 'AND';
			}

			if ($filter_district_id > 0)
			{
				$filtermethod .= " $where district_id=" . (int)$filter_district_id;
				$where = 'AND';
			}

			if ($dimb_id > 0)
			{
				$filtermethod .= " $where fm_tts_tickets.ecodimb={$dimb_id}";
				$where = 'AND';
			}

			if($_department_dimb)
			{
				$filtermethod .= " $where fm_tts_tickets.ecodimb IN (" . implode(',', $_department_dimb) . ')';
				$where = 'AND';
			}

			if ($grouping > 0)
			{
				$filtermethod .= " $where fm_b_account.category='$grouping' ";
				$where = 'AND';
			}


			$sql = "SELECT sum(budget) as budget, count(fm_tts_tickets.id) as hits, fm_b_account.{$b_account_field} as {$b_account_field}, district_id, fm_tts_tickets.ecodimb"
				. " FROM fm_tts_tickets"
				. " {$this->join} fm_b_account ON fm_tts_tickets.b_account_id = fm_b_account.id "
				. " {$this->join} fm_location1 ON fm_tts_tickets.loc1 = fm_location1.loc1 "
				. " {$this->join} fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id $filtermethod $filtermethod2 $querymethod  {$where} {$filtermethod_direction} GROUP BY fm_b_account.{$b_account_field},district_id,fm_tts_tickets.ecodimb";

			//_debug_array($sql);die();
			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$_budget = round($this->db->f('budget'));
				$sum_obligation_cost += $_budget;
				$_hits = $this->db->f('hits');
				$sum_hits += $_hits;

				$district_id = $filter_district_id ? (int)$this->db->f('district_id') : 0;
				$obligations[$this->db->f($b_account_field)][$district_id][(int)$this->db->f('ecodimb')] += $_budget;
				$hits[$this->db->f($b_account_field)][$district_id][(int)$this->db->f('ecodimb')] += $_hits;
				$accout_info[$this->db->f($b_account_field)] = true;
				$district[$district_id] = true;
				$ecodimb[(int)$this->db->f('ecodimb')] = true;
			}

			//_debug_array($obligations);die();

			$sql = "SELECT sum(budget) as budget, count(fm_tts_tickets.id) as hits, fm_b_account.{$b_account_field} as {$b_account_field}, district_id, fm_tts_tickets.ecodimb"
				. " FROM fm_tts_tickets"
				. " {$this->join} fm_b_account ON fm_tts_tickets.b_account_id = fm_b_account.id "
				. " {$this->join} fm_location1 ON fm_tts_tickets.loc1 = fm_location1.loc1 "
				. " {$this->join} fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id {$filtermethod} {$querymethod} {$where} {$filtermethod_direction} GROUP BY fm_b_account.{$b_account_field},district_id,fm_tts_tickets.ecodimb";


			$sql = str_replace('budget', 'actual_cost', $sql);
			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$_actual_cost = round($this->db->f('actual_cost'));
				$sum_actual_cost += round($this->db->f('actual_cost'));

				$district_id = $filter_district_id ? (int)$this->db->f('district_id') : 0;

				$actual_cost[$this->db->f($b_account_field)][$district_id][(int)$this->db->f('ecodimb')] += $_actual_cost;
				$accout_info[$this->db->f($b_account_field)] = true;
				$district[$district_id] = true;
				$ecodimb[(int)$this->db->f('ecodimb')] = true;
			}
			//_debug_array($actual_cost);die();
			//----------- end ad hoc order

			$this->db->query("select max(revision) as revision from fm_budget where year={$year}",__LINE__,__FILE__);
			$this->db->next_record();
			$revision = (int)$this->db->f('revision');


			$filtermethod = '';
			$where = 'AND';
			if ($grouping > 0)
			{
				$filtermethod .= " $where fm_b_account.category='$grouping' ";
/*
				if (!$details)
				{
					$filtermethod = " $where b_group='$grouping' ";
					$where = 'AND';
				}
				else
				{
					$filtermethod = " $where fm_b_account.category='$grouping' ";
				}
 */
			}

			if($cat_ids)
			{
				$filtermethod .= " $where fm_budget.category IN (". implode(',', $cat_ids) . ')';
				$where = 'AND';
			}

			if ($filter_district_id > 0)
			{
				$filtermethod .= " $where district_id='$filter_district_id' ";
				$where = 'AND';
			}

			if ($dimb_id > 0)
			{
				$filtermethod .= " $where ecodimb={$dimb_id}";
				$where = 'AND';
			}

			if($_department_dimb)
			{
				$filtermethod .= " $where ecodimb IN (" . implode(',', $_department_dimb) . ')';
				$where = 'AND';
			}


			if( $details )
			{
				$sql = "SELECT budget_cost,b_account_id as b_account_field,district_id,ecodimb FROM fm_budget"
					. " {$this->join} fm_b_account ON fm_budget.b_account_id =fm_b_account.id WHERE year={$year} AND revision = '$revision' $filtermethod {$where} {$filtermethod_direction}"
					. " GROUP BY budget_cost,b_account_id,district_id,ecodimb";
			}
			else
			{
				$sql = "SELECT sum(budget_cost) as budget_cost ,fm_b_account.category as b_account_field,district_id,ecodimb FROM fm_budget"
					. " $this->join fm_b_account ON fm_budget.b_account_id =fm_b_account.id WHERE year={$year} AND revision = '$revision' $filtermethod  {$where} {$filtermethod_direction}"
					. " GROUP BY fm_b_account.category,district_id,ecodimb";
			}
			//_debug_array($sql);
			$this->db->query($sql,__LINE__,__FILE__);

			$sum_budget_cost = 0;
			$budget_cost = array();
			while ($this->db->next_record())
			{
				$_budget_cost = round($this->db->f('budget_cost'));
				$district_id = $filter_district_id ? (int)$this->db->f('district_id') : 0;
				$sum_budget_cost += $_budget_cost;
				$budget_cost[$this->db->f('b_account_field')][$district_id][(int)$this->db->f('ecodimb')] += $_budget_cost;
				$accout_info[$this->db->f('b_account_field')] = true;
				$district[$district_id] = true;
				$ecodimb[(int)$this->db->f('ecodimb')] = true;
			}

			//_debug_array($budget_cost);die();

// start service agreements

			$filtermethod = " fm_s_agreement_budget.year = $year";
			$where = 'AND';

			if($cat_ids)
			{
				$filtermethod .= " $where fm_s_agreement.category IN (". implode(',', $cat_ids) . ')';
				$where = 'AND';
			}

			if ($grouping > 0)
			{
				$filtermethod .= " $where fm_b_account.category='$grouping' ";
				$where = 'AND';
			}

			if ($dimb_id > 0)
			{
				$filtermethod .= " $where fm_s_agreement_budget.ecodimb={$dimb_id}";
				$where = 'AND';
			}

			if($_department_dimb)
			{
				$filtermethod .= " $where fm_s_agreement_budget.ecodimb IN (" . implode(',', $_department_dimb) . ')';
				$where = 'AND';
			}

			$sql = "SELECT sum(budget) as budget, count(fm_s_agreement.id) as hits, fm_b_account.{$b_account_field} as {$b_account_field}, fm_s_agreement_budget.ecodimb"
				. " FROM fm_s_agreement"
				. " {$this->join} fm_s_agreement_budget ON fm_s_agreement.id = fm_s_agreement_budget.agreement_id"
				. " $this->join fm_b_account ON fm_s_agreement_budget.budget_account = fm_b_account.id "
				. " WHERE $filtermethod $querymethod {$where} {$filtermethod_direction} GROUP BY fm_b_account.{$b_account_field},fm_s_agreement_budget.ecodimb";

			//_debug_array($sql);die();
			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);

			$_dummy_district = $filter_district_id ? $filter_district_id : 0;
			while ($this->db->next_record())
			{
				$_budget = round($this->db->f('budget'));
				$sum_obligation_cost += $_budget;
				$_hits = $this->db->f('hits');
				$sum_hits += $_hits;

				$obligations[$this->db->f($b_account_field)][$_dummy_district][(int)$this->db->f('ecodimb')] += $_budget;
				$hits[$this->db->f($b_account_field)][$_dummy_district][(int)$this->db->f('ecodimb')] += $_hits;
				$accout_info[$this->db->f($b_account_field)] = true;
				$district[$_dummy_district] = true;
				$ecodimb[(int)$this->db->f('ecodimb')] = true;
			}


//-------start check paid service agreement-----------

			$sql = "SELECT fm_b_account.{$b_account_field} as {$b_account_field}, sum(fm_ecobilagoverf.godkjentbelop) as actual_cost,fm_s_agreement_budget.ecodimb,"
				. " periode FROM fm_ecobilagoverf"
				. " {$this->join} fm_b_account ON fm_ecobilagoverf.spbudact_code =fm_b_account.id"
				. " {$this->join} fm_s_agreement ON fm_ecobilagoverf.pmwrkord_code = fm_s_agreement.id"
				. " {$this->join} fm_s_agreement_budget ON fm_s_agreement.id = fm_s_agreement_budget.agreement_id"
				. " WHERE periode >= $start_periode AND periode <= $end_periode AND {$filtermethod} {$where} {$filtermethod_direction}"
				. " GROUP BY fm_b_account.{$b_account_field}, ecodimb, periode";
//_debug_array($sql);
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$_actual_cost = round($this->db->f('actual_cost'));
				$_account_value = $this->db->f($b_account_field);
				$_dimb = (int)$this->db->f('ecodimb');

				$sum_actual_cost += $_actual_cost;
				$actual_cost[$_account_value][$_dummy_district][$_dimb] += $_actual_cost;
				if((int)$this->db->f('periode')==(int)$filter_period)
				{
					$actual_cost_period[$_account_value][$_dummy_district][$_dimb] += $_actual_cost;
					$sum_actual_cost_period += $_actual_cost;
				}
				$obligations[$_account_value][$_dummy_district][$_dimb] -= $_actual_cost;
				$accout_info[$_account_value] = true;
				$district[$_dummy_district] = true;
				$ecodimb[(int)$this->db->f('dimb')] = true;
			}

//-------end check paid-----------

//-------start check active invoices service agreement-----------

			$filtermethod = '';
			$where = 'WHERE';

			if($cat_ids)
			{
				$filtermethod .= " $where fm_s_agreement.category IN (". implode(',', $cat_ids) . ')';
				$where = 'AND';
			}

			if ($grouping > 0)
			{
				$filtermethod .= " $where fm_b_account.category='$grouping' ";
				$where = 'AND';
			}

			if ($dimb_id > 0)
			{
				$filtermethod .= " $where fm_s_agreement_budget.ecodimb={$dimb_id}";
				$where = 'AND';
			}

			if($_department_dimb)
			{
				$filtermethod .= " $where fm_s_agreement_budget.ecodimb IN (" . implode(',', $_department_dimb) . ')';
				$where = 'AND';
			}

			$sql = "SELECT fm_b_account.{$b_account_field} as {$b_account_field}, sum(fm_ecobilag.godkjentbelop) as actual_cost,fm_s_agreement_budget.ecodimb,"
				. " periode FROM fm_ecobilag"
				. " {$this->join} fm_b_account ON fm_ecobilag.spbudact_code =fm_b_account.id"
				. " {$this->join} fm_s_agreement ON fm_ecobilag.pmwrkord_code = fm_s_agreement.id"
				. " {$this->join} fm_s_agreement_budget ON fm_s_agreement.id = fm_s_agreement_budget.agreement_id"
				. " {$filtermethod} {$where} {$filtermethod_direction}"
				. " GROUP BY fm_b_account.{$b_account_field}, ecodimb, periode";
//_debug_array($sql);
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$_actual_cost = round($this->db->f('actual_cost'));
				$_account_value = $this->db->f($b_account_field);
				$_dimb = (int)$this->db->f('ecodimb');

				$sum_actual_cost += $_actual_cost;
				if((int)$this->db->f('periode')==(int)$filter_period)
				{
					$actual_cost_period[$_account_value][$_dummy_district][$_dimb] += $_actual_cost;
					$sum_actual_cost_period += $_actual_cost;
				}
				$actual_cost[$_account_value][$_dummy_district][$_dimb] += $_actual_cost;
				$obligations[$_account_value][$_dummy_district][$_dimb] -= $_actual_cost;
				$accout_info[$_account_value] = true;
				$district[$_dummy_district] = true;
				$ecodimb[(int)$this->db->f('dimb')] = true;
			}


//-------end check active invoices-----------
// end service agreements
			$this->sum_budget_cost			= $sum_budget_cost;
			$this->sum_obligation_cost		= $sum_obligation_cost;
			$this->sum_actual_cost			= $sum_actual_cost;
			$this->sum_actual_cost_period	= $sum_actual_cost_period;
			$this->sum_hits 				= $sum_hits;

			//_debug_array($actual_cost);die();
			$result = array();

			if (is_array($accout_info))
			{
				if ($order == 'b_account')
				{
					switch ($sort)
					{
					case 'ASC':
						ksort($accout_info);
						break;
					case 'DESC':
						krsort($accout_info);
						break;
					default:
						ksort($accout_info);
					}
				}
				else
				{
					ksort($accout_info);
				}

				ksort($district);
				$accout_info = array_keys($accout_info);
				$district = array_keys($district);
				$ecodimb = array_keys($ecodimb);

				$result = array();
				foreach($accout_info as $b_account)
				{
					foreach($district as $district_id)
					{
						foreach ($ecodimb as $dimb)
						{
							if( (isset($actual_cost[$b_account][$district_id][$dimb]) && $actual_cost[$b_account][$district_id][$dimb])
								|| (isset($budget_cost[$b_account][$district_id][$dimb]) && $budget_cost[$b_account][$district_id][$dimb])
								|| (isset($obligations[$b_account][$district_id][$dimb]) && $obligations[$b_account][$district_id][$dimb]))
							{
								$result[] = array(
									'grouping'				=> $details ? '' : $b_account, 
									'b_account'				=> $details ? $b_account : '',
									'district_id'			=> $district_id,
									'ecodimb'				=> $dimb,
									'actual_cost'			=> isset($actual_cost[$b_account][$district_id][$dimb]) && $actual_cost[$b_account][$district_id][$dimb] ? round($actual_cost[$b_account][$district_id][$dimb]) : 0,
									'actual_cost_period'	=> isset($actual_cost_period[$b_account][$district_id][$dimb]) && $actual_cost_period[$b_account][$district_id][$dimb] ? round($actual_cost_period[$b_account][$district_id][$dimb]) : 0,
									'budget_cost'			=> isset($budget_cost[$b_account][$district_id][$dimb]) && $budget_cost[$b_account][$district_id][$dimb] ? round($budget_cost[$b_account][$district_id][$dimb]) : 0,
									'obligation'			=> isset($obligations[$b_account][$district_id][$dimb]) && $obligations[$b_account][$district_id][$dimb] ? round($obligations[$b_account][$district_id][$dimb]) : 0,
									'hits'					=> isset($hits[$b_account][$district_id][$dimb])?$hits[$b_account][$district_id][$dimb]:0,
								);
							}
						}
					}
				}
			}

			foreach ($result as &$entry )
			{
				$entry['percent'] = round((($entry['actual_cost'] + $entry['obligation'])/$entry['budget_cost'])*100, 1);
			}
			$this->total_records = count($result);

			//cramirez
			if($this->total_records == 0)
			{
				return $result;
			}

			if(!$allrows)
			{
				$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'])?intval($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']):15;

				//_debug_array(array($start,$this->total_records,$this->total_records,$num_rows));
				$page = ceil( ( $start / $this->total_records ) * ($this->total_records/ $num_rows) );

				$out = array_chunk($result, $num_rows);

				return $out[$page];
			}
			else
			{
				return $result;
			}
		}

		function get_b_group_list()
		{
			$sql = "SELECT DISTINCT fm_b_account.category as id FROM fm_budget $this->join fm_b_account ON (fm_budget.b_account_id = fm_b_account.id) ORDER BY id asc";
			$this->db->query($sql,__LINE__,__FILE__);

			$group_list = array();
			while ($this->db->next_record())
			{
				$group_list[] = array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $this->db->f('id')
					);

			}
			return $group_list;
		}

		function get_revision_list($year='',$basis = '')
		{
			$table = $basis?'fm_budget_basis':'fm_budget';
			$revision_list = array();

			if(!$year)
			{
				$year = date("Y");
			}
			$sql = "SELECT revision FROM $table where year =" . (int)$year . " GROUP BY revision";
			$this->db->query($sql,__LINE__,__FILE__);


			$i = 1;
			while ($this->db->next_record())
			{
				$revision_list[$i] = array
					(
						'id'	=> $this->db->f('revision'),
						'name'	=> $this->db->f('revision')
					);
				$i++;
			}

			$revision_list[] = array
				(
					'id'	=> $i,
					'name'	=> $i
				);

			return $revision_list;
		}

		function get_year_filter_list($basis = '')
		{
			$table = $basis?'fm_budget_basis':'fm_budget';
			$sql = "SELECT year FROM $table group by year ORDER BY year ASC";
			$this->db->query($sql,__LINE__,__FILE__);

			$year_list = array();
			while ($this->db->next_record())
			{
				$year_list[] = array
					(
						'id'	=> $this->db->f('year'),
						'name'	=> $this->db->f('year')
					);
			}
			return $year_list;
		}

		function get_max_revision($year ='',$basis = '')
		{
			$table = $basis?'fm_budget_basis':'fm_budget';
			$sql = "SELECT max(revision) as revision FROM $table WHERE year =". (int)$year;
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('revision');
		}

		function get_revision_filter_list($year ='',$basis = '')
		{
			$table = $basis?'fm_budget_basis':'fm_budget';
			$sql = "SELECT revision FROM $table WHERE year =". (int)$year . "  group by revision";
			$this->db->query($sql,__LINE__,__FILE__);

			$revision_list = array();
			while ($this->db->next_record())
			{
				$revision_list[] = array
					(
						'id'	=> $this->db->f('revision'),
						'name'	=> $this->db->f('revision')
					);
			}
			return $revision_list;
		}

		function get_grouping_filter_list($year = 0,$basis = '')
		{
			$year = (int)$year;
			if($basis)
			{
				$sql = "SELECT DISTINCT b_group as grouping FROM fm_budget_basis WHERE year ={$year} ORDER BY b_group ASC";
			}
			else
			{
				$sql = "SELECT fm_b_account.category as grouping FROM fm_budget $this->join fm_b_account ON fm_budget.b_account_id = fm_b_account.id WHERE year ={$year} GROUP BY fm_b_account.category ORDER BY fm_b_account.category ASC";
			}

			$this->db->query($sql,__LINE__,__FILE__);

			$grouping_list = array();
			while ($this->db->next_record())
			{
				$grouping_list[] = array
					(
						'id'	=> $this->db->f('grouping'),
						'name'	=> $this->db->f('grouping')
					);
			}
			return $grouping_list;
		}

		function get_distribute_year_list()
		{
			$table = 'fm_budget_cost';
			$sql = "SELECT year FROM $table group by year order by year asc";
			$this->db->query($sql,__LINE__,__FILE__);

			$year_list = array();
			while ($this->db->next_record())
			{
				$year_list[] = array
					(
						'id'	=> $this->db->f('year'),
						'name'	=> $this->db->f('year')
					);
			}
			return $year_list;
		}


		function delete($budget_id)
		{
			$this->db->query('DELETE FROM fm_budget WHERE id=' . intval($budget_id),__LINE__,__FILE__);
		}

		function delete_basis($basis_id)
		{
			$this->db->transaction_begin();

			$this->db->query("SELECT * FROM fm_budget_basis where id='$basis_id'",__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$basis['year']			= $this->db->f('year');
				$basis['district_id']		= $this->db->f('district_id');
				$basis['revision']		= $this->db->f('revision');
				$basis['b_group']		= $this->db->f('b_group');
				$basis['budget_cost']		= $this->db->f('budget_cost');

				$this->db->query("SELECT id FROM fm_b_account where category=" . (int)$basis['b_group'],__LINE__,__FILE__);
				while ($this->db->next_record())
				{
					$b_account[] = $this->db->f('id');
				}

			}

			$this->db->query("DELETE FROM fm_budget WHERE year ='" . $basis['year'] . "'  AND b_account_id in ('" . implode("','",$b_account) . "') AND revision = '" . $basis['revision'] . "' AND district_id='" . $basis['district_id'] . "'" ,__LINE__,__FILE__);
			$this->db->query('DELETE FROM fm_budget_basis WHERE id=' . intval($basis_id),__LINE__,__FILE__);
			$this->db->transaction_commit();
		}

		function distribute($values,$receipt = array())
		{
			$year_condition = 'year in (' . implode(",",$values['distribute_year']). ')';

			$this->db->transaction_begin();

			foreach ($values['distribute'] as $basis_id)
			{
				$this->db->query("SELECT * FROM fm_budget_basis where id='$basis_id'",__LINE__,__FILE__);

				if ($this->db->next_record())
				{
					$basis['year']			= $this->db->f('year');
					$basis['district_id']		= $this->db->f('district_id');
					$basis['revision']		= $this->db->f('revision');
					$basis['b_group']		= $this->db->f('b_group');
					$basis['budget_cost']		= $this->db->f('budget_cost');
				}

				$sql = "SELECT SUM(amount) as group_sum FROM fm_budget_cost $this->join fm_b_account ON fm_budget_cost.b_account_id = fm_b_account.id WHERE fm_b_account.category = '" . $basis['b_group'] . "' AND $year_condition";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
				$group_sum = $this->db->f('group_sum');

				$sql = "SELECT SUM(amount) as account_sum, b_account_id FROM fm_budget_cost $this->join fm_b_account ON fm_budget_cost.b_account_id = fm_b_account.id WHERE fm_b_account.category = '" . $basis['b_group'] . "' AND $year_condition group by b_account_id";
				$this->db->query($sql,__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					$budget[] = array(
						'b_account_id'	=> $this->db->f('b_account_id'),
						'account_sum'	=> round(($this->db->f('account_sum')/$group_sum) * $basis['budget_cost'],-3),
					);

					$test_sum = $test_sum + round(($this->db->f('account_sum')/$group_sum) * $basis['budget_cost'],-3);
				}


				$this->db->query("SELECT id FROM fm_b_account where category=" . (int)$basis['b_group'],__LINE__,__FILE__);
				while ($this->db->next_record())
				{
					$b_account[] = $this->db->f('id');
				}

				$sql = "DELETE FROM fm_budget WHERE year = " . (int)$basis['year']
					. " AND district_id = " . (int)$basis['district_id']
					. " AND revision = " . (int)$basis['revision']
					. " AND b_account_id in ('" . implode("','",$b_account) . "')";

				$this->db->query($sql,__LINE__,__FILE__);

				if(is_array($budget))
				{
					foreach ($budget as $entry)
					{
						if(abs($entry['account_sum'])>0)
						{

							$this->db->query("INSERT INTO fm_budget (id, year, b_account_id, district_id,revision,user_id,entry_date,budget_cost) VALUES ("
								. $this->db->next_id('fm_budget') . ","
								. $basis['year'] . ",'"
								. $entry['b_account_id']. "',"
								. $basis['district_id'] . ","
								. $basis['revision'] . ","
								. $this->account . ","
								. time() . ","
								. $entry['account_sum'] . ")",__LINE__,__FILE__);
						}
					}

					if($test_sum != $basis['budget_cost'])
					{
						$diff_sum = $basis['budget_cost'] - $test_sum;
						$sql = "SELECT max(budget_cost) as budget_cost FROM fm_budget WHERE year = " . (int)$basis['year']
							. " AND district_id = " . (int)$basis['district_id']
							. " AND revision = " . (int)$basis['revision'];

						$this->db->query($sql,__LINE__,__FILE__);
						$this->db->next_record();
						$max_budget_cost = $this->db->f('budget_cost');

						$sql = "SELECT id FROM fm_budget WHERE year = " . (int)$basis['year']
							. " AND district_id = " . (int)$basis['district_id']
							. " AND revision = " . (int)$basis['revision']
							. " AND budget_cost = $max_budget_cost";

						$this->db->query($sql,__LINE__,__FILE__);
						$this->db->next_record();

						$this->db->query("UPDATE fm_budget set budget_cost = budget_cost + $diff_sum WHERE id = " . (int)$this->db->f('id') ,__LINE__,__FILE__);
					}
				}
			}
			$this->db->transaction_commit();
			return $receipt;
		}
	}
