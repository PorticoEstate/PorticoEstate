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
		function property_sobudget()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon	= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->join		= $this->bocommon->join;
			$this->left_join	= $this->bocommon->left_join;
			$this->like		= $this->bocommon->like;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start	= (isset($data['start'])?$data['start']:0);
				$filter	= (isset($data['filter'])?$data['filter']:'none');
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$district_id = (isset($data['district_id'])?$data['district_id']:'');
				$year = (isset($data['year'])?$data['year']:'');
				$grouping = (isset($data['grouping'])?$data['grouping']:'');
				$revision = (isset($data['revision'])?$data['revision']:'');
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
				$filtermethod .= " $where category='$grouping' ";
				$where = 'AND';

			}
			if ($revision > 0)
			{
				$filtermethod .= " $where revision='$revision' ";
				$where = 'AND';

			}

			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " $where ( descr $this->like '%$query%')";
			}


			$sql = "SELECT fm_budget.*, descr,category FROM fm_budget $this->join fm_b_account ON fm_budget.b_account_id = fm_b_account.id $filtermethod $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$budget[] = array
				(
					'budget_id'		=> $this->db->f('id'),
					'year'			=> $this->db->f('year'),
					'grouping'		=> $this->db->f('category'),
					'b_account_id'		=> $this->db->f('b_account_id'),
					'b_account_name'	=> $this->db->f('descr'),
					'district_id'		=> $this->db->f('district_id'),
					'revision'		=> $this->db->f('revision'),
					'budget_cost'		=> $this->db->f('budget_cost'),
					'entry_date'		=> $this->db->f('entry_date'),
					'user'			=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('user_id'))
				);
			}
			return $budget;
		}

		function read_basis($data)
		{
			if(is_array($data))
			{
				$start	= (isset($data['start'])?$data['start']:0);
				$filter	= (isset($data['filter'])?$data['filter']:'none');
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$district_id = (isset($data['district_id'])?$data['district_id']:'');
				$year = (isset($data['year'])?$data['year']:'');
				$grouping = (isset($data['grouping'])?$data['grouping']:'');
				$revision = (isset($data['revision'])?$data['revision']:'');
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
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

			//	$querymethod = " $where ( descr $this->like '%$query%')";
			}


			$sql = "SELECT * FROM fm_budget_basis $filtermethod $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$budget[] = array
				(
					'budget_id'		=> $this->db->f('id'),
					'year'			=> $this->db->f('year'),
					'grouping'		=> $this->db->f('b_group'),
					'district_id'		=> $this->db->f('district_id'),
					'revision'		=> $this->db->f('revision'),
					'budget_cost'		=> $this->db->f('budget_cost'),
					'entry_date'		=> $this->db->f('entry_date'),
					'user'			=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('user_id'))
				);
			}
			return $budget;
		}

		function read_single_basis($budget_id)
		{
			$this->db->query("select * from fm_budget_basis where id='$budget_id'",__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$budget['id']			= (int)$this->db->f('id');
				$budget['year']			= $this->db->f('year');
				$budget['district_id']		= $this->db->f('district_id');
				$budget['revision']		= $this->db->f('revision');
				$budget['b_group']		= $this->db->f('b_group');
				$budget['remark']		= stripslashes($this->db->f('remark'));
				$budget['budget_cost']		= $this->db->f('budget_cost');
				$budget['entry_date']		= $this->db->f('entry_date');
				$budget['distribute_year']	= unserialize($this->db->f('distribute_year'));
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
				$id = $this->bocommon->next_id('fm_budget_basis');

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
					serialize($budget['distribute_year'])
					);

				$values	= $this->bocommon->validate_db_insert($values);


				$this->db->query("INSERT INTO fm_budget_basis (id,entry_date,remark,user_id,year,revision,district_id,b_group,budget_cost,distribute_year)"
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

			$value_set=array(
				'remark'	=> $budget['remark'],
				'entry_date'	=> time(),
				'budget_cost'	=> $budget['budget_cost'],
				'distribute_year' => serialize($budget['distribute_year'])
				);
			
			$value_set	= $this->bocommon->validate_db_update($value_set);

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

			if ($this->db->next_record())
			{
				$budget['id']			= (int)$this->db->f('id');
				$budget['year']			= $this->db->f('year');
				$budget['district_id']		= $this->db->f('district_id');
				$budget['revision']		= $this->db->f('revision');
				$budget['b_account_id']		= $this->db->f('b_account_id');
				$budget['remark']	= stripslashes($this->db->f('remark'));
				$budget['budget_cost']	= $this->db->f('budget_cost');
				$budget['entry_date']	= $this->db->f('entry_date');
			}

			return $budget;
		}

		function add($budget)
		{
			$budget['remark'] = $this->db->db_addslashes($budget['remark']);

			$this->db->transaction_begin();

			$sql = "SELECT id FROM fm_budget WHERE year ='" . $budget['year'] . "'  AND b_account_id ='" . $budget['b_account_id'] . "' AND revision = '" . $budget['revision'] . "' AND district_id='" . $budget['district_id'] . "'";
			$this->db->query($sql,__LINE__,__FILE__);

			if($this->db->next_record())
			{
				$receipt['error'][] = array('msg'=>lang('budget %1 already saved',$this->db->f('id')));
			}

			if(!$receipt['error'])
			{
				$id = $this->bocommon->next_id('fm_budget');

				$values= array(
					$id,
					time(),
					$budget['remark'],
					$this->account,
					$budget['year'],
					$budget['revision'],
					$budget['district_id'],
					$budget['b_account_id'],
					$budget['budget_cost'],
					);

				$values	= $this->bocommon->validate_db_insert($values);

				$this->db->query("INSERT INTO fm_budget (id,entry_date,remark,user_id,year,revision,district_id,b_account_id,budget_cost)"
					. "VALUES ($values)",__LINE__,__FILE__);

				$receipt['budget_id']= $id;
				$receipt['message'][] = array('msg'=>lang('budget %1 has been saved',$receipt['budget_id']));
			}

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit($budget)
		{
			$budget['remark'] = $this->db->db_addslashes($budget['remark']);
			
			$this->db->transaction_begin();

			$value_set=array(
				'remark'	=> $budget['remark'],
				'entry_date'	=> time(),
				'budget_cost'	=> $budget['budget_cost'],
				'year'			=> $budget['year'],
				'revision'		=> $budget['revision'],
				'district_id'	=> $budget['district_id'],
				);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE fm_budget set $value_set WHERE id=" . intval($budget['budget_id']),__LINE__,__FILE__);

			$this->db->transaction_commit();
			
			$receipt['budget_id']= $budget['budget_id'];
			$receipt['message'][] = array('msg'=>lang('budget %1 has been edited',$budget['budget_id']));
			return $receipt;
		}



		function read_obligations($data)
		{
			if(is_array($data))
			{
				$start	= (isset($data['start']) && $data['start'] ?$data['start']:0);
				$filter	= (isset($data['filter'])?$data['filter']:'none');
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$district_id = (isset($data['district_id'])?$data['district_id']:'');
				$year = (isset($data['year'])?$data['year']:'');
				$grouping = (isset($data['grouping'])?$data['grouping']:'');
				$revision = (isset($data['revision'])?$data['revision']:'');
				$year = (isset($data['year'])?$data['year']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:'');
			}

			$ordermethod = '';
			/* 0 => cancelled, 1 => obligation , 2 => paid */
			$filtermethod = " WHERE fm_workorder.paid = 1 and fm_workorder.vendor_id > 0";
			$where = 'AND';

			if ($cat_id > 0)
			{
				$filtermethod .= " $where fm_project.category = " . (int)$cat_id;
				$where = 'AND';
			}

			if ($district_id > 0)
			{
				$filtermethod .= " $where district_id=" . (int)$district_id;
				$where = 'AND';
			}
/*
			if ($year > 0)
			{
				$filtermethod .= " $where year='$year' ";
				$where = 'AND';
			}
*/
			if ($grouping > 0)
			{
				$filtermethod .= " $where fm_b_account.category='$grouping' ";
				$where = 'AND';
			}

			$querymethod = '';
			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

			//	$querymethod = " $where ( descr $this->like '%$query%')";
			}


			$sql = "SELECT sum(combined_cost) as combined_cost, count(fm_workorder.id) as hits, fm_b_account.category as b_group, district_id FROM"
				. " fm_workorder $this->join fm_b_account ON fm_workorder.account_id =fm_b_account.id "
				. " $this->join fm_project ON  fm_workorder.project_id =fm_project.id "
				. " $this->join fm_location1 ON fm_project.loc1 = fm_location1.loc1 "
				. " $this->join fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id $filtermethod $querymethod GROUP BY fm_b_account.category,district_id ";

			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();
//_debug_array($sql);
			if(!$year)
			{
				$year = date("Y");
			}

			while ($this->db->next_record())
			{
				$obligations[$this->db->f('b_group')][$this->db->f('district_id')] = round($this->db->f('combined_cost'));
				$hits[$this->db->f('b_group')][$this->db->f('district_id')] = $this->db->f('hits');
				$group_info[$this->db->f('b_group')] = true;
				$district[$this->db->f('district_id')] = true;
			}

//_debug_array($obligations);
			$this->db->query("select max(revision) as revision from fm_budget_basis where year='$year'",__LINE__,__FILE__);
			$this->db->next_record();
			$revision = (int)$this->db->f('revision');


			$filtermethod = '';
			$where = 'AND';
			if ($grouping > 0)
			{
				$filtermethod = " $where b_group='$grouping' ";
				$where = 'AND';
			}

			if ($district_id > 0)
			{
				$filtermethod .= " $where district_id='$district_id' ";
				$where = 'AND';
			}

			$sql = "select budget_cost,b_group,district_id from fm_budget_basis where year='$year' AND revision = '$revision' $filtermethod GROUP BY budget_cost,b_group,district_id";
			$this->db->query($sql,__LINE__,__FILE__);
	
			$budget_cost = array();
			while ($this->db->next_record())
			{
				$budget_cost[$this->db->f('b_group')][$this->db->f('district_id')] = round($this->db->f('budget_cost'));
				$group_info[$this->db->f('b_group')] = true;
				$district[$this->db->f('district_id')] = true;
			}

//_debug_array($budget_cost);

			$filtermethod = '';
			$where = 'AND';

			if ($cat_id > 0)
			{
				$filtermethod .= " $where fm_project.category = " . (int)$cat_id;
				$where = 'AND';
			}

			if ($grouping > 0)
			{
				$filtermethod = " $where fm_b_account.category='$grouping' ";
				$where = 'AND';
			}

			if ($district_id > 0)
			{
				$filtermethod .= " $where district_id='$district_id' ";
				$where = 'AND';
			}


			$start_date1 = date($this->bocommon->dateformat,mktime(2,0,0,3,1,$year));
			$start_date2 = date($this->bocommon->dateformat,mktime(2,0,0,1,1,$year));
			$end_date = date($this->bocommon->dateformat,mktime(2,0,0,12,31,$year));

			$sql = "SELECT fm_b_account.category as b_group, district_id, sum(godkjentbelop) as actual_cost FROM fm_ecobilagoverf"
				. " $this->join fm_project ON fm_ecobilagoverf.project_id =fm_project.id"
				. " $this->join fm_b_account ON fm_ecobilagoverf.spbudact_code =fm_b_account.id"
				. " $this->join fm_location1 ON fm_ecobilagoverf.loc1 = fm_location1.loc1"
				. " $this->join fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id"
				. " WHERE (fakturadato > '$start_date1' AND fakturadato < '$end_date' $filtermethod)"
				. " OR (fakturadato > '$start_date2' AND fakturadato < '$end_date' AND periode < 3 $filtermethod)"
				. " GROUP BY b_group, district_id";
				
//_debug_array($sql);				
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$actual_cost[$this->db->f('b_group')][$this->db->f('district_id')] = round($this->db->f('actual_cost'));
				$group_info[$this->db->f('b_group')] = true;
				$district[$this->db->f('district_id')] = true;
			}

			
			if (is_array($group_info))
			{
				if ($order == 'b_group')
				{
					switch ($sort)
					{
						case 'ASC':
							ksort($group_info);
							break;
						case 'DESC':
							krsort($group_info);
							break;					
						default:
							ksort($group_info);
					}
				}
				else
				{
					ksort($group_info);
				}

				ksort($district);			
				$group_info = array_keys($group_info);
				$district = array_keys($district);

				foreach($group_info as $b_group)
				{	
					foreach($district as $district_id)
					{
						if( (isset($actual_cost[$b_group][$district_id]) && $actual_cost[$b_group][$district_id])
						 || (isset($budget_cost[$b_group][$district_id]) && $budget_cost[$b_group][$district_id])
						 || (isset($obligations[$b_group][$district_id]) && $obligations[$b_group][$district_id]))
						{
							$result[] = array(
								'grouping'		=> $b_group,
								'district_id'	=> $district_id,
								'actual_cost'	=> isset($actual_cost[$b_group][$district_id]) && $actual_cost[$b_group][$district_id] ? round($actual_cost[$b_group][$district_id]) : 0,
								'budget_cost'	=> isset($budget_cost[$b_group][$district_id]) && $budget_cost[$b_group][$district_id] ? round($budget_cost[$b_group][$district_id]) : 0,
								'obligation'	=> isset($obligations[$b_group][$district_id]) && $obligations[$b_group][$district_id] ? round($obligations[$b_group][$district_id]) : 0,
								'hits'			=> isset($hits[$b_group][$district_id])?$hits[$b_group][$district_id]:0,
							);
						}
					}		
				}
			}

//_debug_array($result);
			return $result;
		}

		function get_b_group_list()
		{
			$sql = "SELECT id FROM fm_b_account_category order by id asc";
			$this->db->query($sql,__LINE__,__FILE__);

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

		function get_grouping_filter_list($year ='',$basis = '')
		{
			if($basis)
			{
				$sql = "SELECT DISTINCT b_group as grouping FROM fm_budget_basis WHERE year =". (int)$year;
			}
			else
			{
				$sql = "SELECT category as grouping FROM fm_budget $this->join fm_b_account ON fm_budget.b_account_id = fm_b_account.id WHERE year =". (int)$year . "  group by category";
			}
			
			$this->db->query($sql,__LINE__,__FILE__);

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

		function distribute($values,$receipt='')
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
							. $this->bocommon->next_id('fm_budget') . ","
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
?>
