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

	class property_sopricebook
	{

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
		}

		function add_activity_first_prize($m_cost,$w_cost,$total_cost,$activity_id,$agreement_id,$date)
		{
			$this->db->query("update fm_activity_price_index  set index_count='1',this_index='1', m_cost='$m_cost',w_cost='$w_cost',total_cost='$total_cost',index_date='$date',current_index='1' where activity_id='$activity_id' and agreement_id= '$agreement_id' and index_count= '1'",__LINE__,__FILE__);

			$receipt['message'][] = array('msg'=>lang('First entry is added!'));

			return $receipt;
		}

		function update_pricebook($update)
		{
			for ($i=0; $i<count($update); $i++)
			{
				$this->db->query("select max(index_count) as max_index_count from fm_activity_price_index Where activity_id='". $update[$i]['activity_id'] . "' and agreement_id='".$update[$i]['agreement_id'] . "'",__LINE__,__FILE__);
				$this->db->next_record();
				$next_index_count  = $this->db->f('max_index_count')+1;

				$this->db->query("update fm_activity_price_index set current_index = Null"
					. " WHERE activity_id='" . $update[$i]['activity_id'] . "' and agreement_id='" . $update[$i]['agreement_id'] . "'",__LINE__,__FILE__);

				$this->db->query("insert into fm_activity_price_index (activity_id, agreement_id, index_count, this_index, m_cost, w_cost, total_cost, index_date,current_index) "
					. " values ('" .
					$update[$i]['activity_id'] . "','" .
					$update[$i]['agreement_id'] . "','" .
					$next_index_count . "','" .
					$update[$i]['new_index'] . "','" .
					$update[$i]['new_m_cost'] . "','" .
					$update[$i]['new_w_cost'] . "','" .
					$update[$i]['new_total_cost'] . "','" .
					$update[$i]['new_date']. "', '1')",__LINE__,__FILE__);
			}

			$receipt['message'][] = array('msg'=>lang('%1 entries is updated!',$i));

			return $receipt;

		}

		function get_vendor_list()
		{
			$this->db->query("SELECT fm_vendor.org_name ,vendor_id "
				. " FROM fm_agreement $this->join fm_vendor ON fm_agreement.vendor_id = fm_vendor.id "
				. " WHERE fm_agreement.status='active'"
				. " GROUP by fm_vendor.org_name ,vendor_id "
				. " ORDER BY fm_vendor.org_name ",__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				//--------->fix this------->
				if($this->db->f('vendor_id'))
				{
					$vendor_list[]=array
						(
							'id'	=> $this->db->f('vendor_id'),
							'name'	=> $this->db->f('org_name')
						);
				}
			}
			return $vendor_list;
		}

		function get_agreement_group_list()
		{
			$this->db->query("SELECT * FROM fm_agreement_group ORDER BY descr asc");
			while ($this->db->next_record())
			{
				$agreement_group_list[]=array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $GLOBALS['phpgw']->strip_html($this->db->f('descr')).' [ '. $GLOBALS['phpgw']->strip_html($this->db->f('status')).' ] '
					);
			}
			return $agreement_group_list;
		}

		function get_dim_d_list()
		{
			$this->db->query("SELECT * FROM fm_ecodimd ORDER BY descr asc");
			while ($this->db->next_record())
			{
				$dim_d_list[]=array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $this->db->f('id')
					);
			}
			return $dim_d_list;
		}

		function get_unit_list()
		{
			$this->db->query("SELECT * FROM fm_standard_unit ORDER BY descr asc");
			while ($this->db->next_record())
			{
				$unit_list[]=array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $GLOBALS['phpgw']->strip_html($this->db->f('descr'))
					);
			}
			return $unit_list;
		}

		function get_branch_list()
		{
			$this->db->query("SELECT * FROM fm_branch ORDER BY descr asc");
			while ($this->db->next_record())
			{
				$branch_list[]=array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $GLOBALS['phpgw']->strip_html($this->db->f('descr'))
					);
			}
			return $branch_list;
		}

		function check_activity_num($num='',$agreement_group_id='')
		{
			$this->db->query("SELECT count(*) as cnt FROM fm_activities where num='$num' and agreement_group_id ='$agreement_group_id'");

			$this->db->next_record();

			if ( $this->db->f('cnt'))
			{
				return true;
			}
		}

		function check_agreement_group_num($num='')
		{
			$this->db->query("SELECT count(*) as cnt FROM fm_agreement_group where num='$num'");

			$this->db->next_record();

			if ( $this->db->f('cnt'))
			{
				return true;
			}
		}

		function read($data)
		{
			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$filter	= (isset($data['filter'])?$data['filter']:'none');
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$allrows 		= (isset($data['allrows'])?$data['allrows']:'');
			}

			//_debug_array($data);
			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by activity_id';
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " AND cat_id='$cat_id' ";
				$vendor_condition= "= $cat_id";
			}
			else
			{
				$vendor_condition= " IS NULL";
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND (fm_activities.descr $this->like '%$query%' or fm_activities.num $this->like '%$query%')";
			}

			$sql = "SELECT DISTINCT fm_activities.num, fm_activities.unit, fm_activities.dim_d, fm_activities.ns3420, fm_activities.descr AS descr,"
				. " fm_activities.base_descr, fm_activity_price_index.activity_id, fm_branch.descr AS branch,"
				. " fm_agreement.vendor_id, fm_activity_price_index.total_cost, fm_activity_price_index.m_cost,"
				. " fm_activity_price_index.w_cost, fm_activity_price_index.index_count, fm_activity_price_index.this_index, fm_agreement.id,"
				. " fm_standard_unit.name AS unit_name"
				. " FROM  fm_activities "
				. " $this->join fm_activity_price_index ON fm_activities.id = fm_activity_price_index.activity_id "
				. " $this->join fm_branch ON fm_activities.branch_id = fm_branch.id "
				. " $this->join fm_agreement ON fm_activity_price_index.agreement_id = fm_agreement.id "
				. " {$this->join} fm_standard_unit ON fm_activities.unit = fm_standard_unit.id"
				. " WHERE fm_agreement.status='active' AND (fm_agreement.vendor_id $vendor_condition and current_index is not null "
				. " OR (fm_agreement.vendor_id $vendor_condition) AND (fm_activity_price_index.this_index IS NULL)) $querymethod";


			//echo $sql;


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

			while ($this->db->next_record())
			{
				$pricebook[] = array
					(
						'activity_id'	=> $this->db->f('activity_id'),
						'num'			=> $this->db->f('num'),
						'branch'		=> $this->db->f('branch'),
						'vendor_id'		=> $this->db->f('vendor_id'),
						'm_cost'		=> $this->db->f('m_cost'),
						'w_cost'		=> $this->db->f('w_cost'),
						'total_cost'	=> $this->db->f('total_cost'),
						'this_index'	=> $this->db->f('this_index'),
						'unit'			=> $this->db->f('unit'),
						'unit_name'		=> $this->db->f('unit_name'),
						'dim_d'			=> $this->db->f('dim_d'),
						'ns3420_id'		=> $this->db->f('ns3420'),
						'descr'			=> $this->db->f('descr',true),
						'base_descr'	=> $this->db->f('base_descr',true),
						'index_count'	=> $this->db->f('index_count'),
						'agreement_id'	=> $this->db->f('fm_agreement.id')
					);
			}
			//		_debug_array($pricebook);
			return $pricebook;
		}

		function read_agreement_group($data)
		{
			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$filter	= (isset($data['filter'])?$data['filter']:'none');
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$allrows 		= (isset($data['allrows'])?$data['allrows']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by id asc';
			}

			$where = 'WHERE';

			if ($cat_id)
			{
				$filtermethod .= " $where status='$cat_id' ";
				$where = 'AND';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " $where (descr $this->like '%$query%' or num $this->like '%$query%')";
			}

			$sql = "SELECT * FROM  fm_agreement_group $filtermethod $querymethod";

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

			while ($this->db->next_record())
			{
				$agreement_group[] = array
					(
						'agreement_group_id'		=> $this->db->f('id'),
						'num'				=> $this->db->f('num'),
						'status'			=> $this->db->f('status'),
						'descr'				=> stripslashes($this->db->f('descr'))
					);
			}
			//		_debug_array($agreement_group);
			return $agreement_group;
		}

		function select_status_list()
		{
			$this->db->query("SELECT id, descr FROM fm_agreement_status ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$status_entries[$i]['id']				= $this->db->f('id');
				$status_entries[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $status_entries;
		}


		function read_activity_prize($data)
		{
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start']:0;
				$filter			= isset($data['filter'])?$data['filter']:'none';
				$query			= isset($data['query'])?$data['query']:'';
				$sort			= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order			= isset($data['order'])?$data['order']:'';
				$cat_id			= isset($data['cat_id'])?$data['cat_id']:0;
				$activity_id	= isset($data['activity_id'])?$data['activity_id']:0;
				$agreement_id	= isset($data['agreement_id']) && $data['agreement_id'] ? $data['agreement_id']:0;
				$allrows 		= isset($data['allrows'])?$data['allrows']:'';
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by index_count';
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " AND cat_id='$cat_id' ";
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND (fm_activities.descr $this->like '%$query%' or fm_activities.num $this->like '%$query%')";
			}

			$sql = "SELECT index_count,this_index,current_index,m_cost,w_cost,total_cost,index_date"
				. " FROM fm_activity_price_index $this->join fm_agreement on fm_activity_price_index.agreement_id = fm_agreement.id "
				. " Where activity_id= '$activity_id' and fm_activity_price_index.agreement_id= '$agreement_id'";

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

			while ($this->db->next_record())
			{
				$pricebook[] = array
					(
						'index_count'		=> $this->db->f('index_count'),
						'this_index'		=> $this->db->f('this_index'),
						'current_index'			=> $this->db->f('current_index'),
						'm_cost'			=> $this->db->f('m_cost'),
						'w_cost'			=> $this->db->f('w_cost'),
						'total_cost'		=> $this->db->f('total_cost'),
						'date'				=> $this->db->f('index_date')
					);
			}
			//		_debug_array($pricebook);
			return $pricebook;
		}

		function read_activities_pr_agreement_group($data)
		{
			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$filter	= (isset($data['filter'])?$data['filter']:'none');
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$allrows 		= (isset($data['allrows'])?$data['allrows']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = " order by activity_id asc";
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " Where agreement_group_id='$cat_id' ";
			}
			else
			{
				$filtermethod = " Where agreement_group_id IS NULL";
			}
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " and (fm_activities.descr $this->like '%$query%' or fm_activities.base_descr $this->like '%$query%' or fm_activities.num $this->like '%$query%') ";
			}

			$sql = "SELECT DISTINCT fm_activities.id AS activity_id, fm_activities.num, fm_activities.base_descr,"
				. " fm_activities.unit, fm_activities.dim_d, fm_branch.descr as branch, fm_activities.descr, ns3420,"
				. " fm_standard_unit.name AS unit_name"
				. " FROM  fm_activities"
				. " {$this->join} fm_branch on fm_activities.branch_id=fm_branch.id"
				. " {$this->left_join} fm_standard_unit ON fm_activities.unit = fm_standard_unit.id"
				. " $filtermethod $querymethod ";


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

			while ($this->db->next_record())
			{
				$pricebook[] = array
					(
						'activity_id'	=> $this->db->f('activity_id'),
						'num'			=> $this->db->f('num'),
						'base_descr'	=> $this->db->f('base_descr',true),
						'branch'		=> $this->db->f('branch'),
						'dim_d'			=> $this->db->f('dim_d'),
						'ns3420'		=> $this->db->f('ns3420'),
						'unit'			=> $this->db->f('unit'),
						'unit_name'		=> $this->db->f('unit_name'),
						'descr'			=> $this->db->f('descr',true)
					);
			}
			//		_debug_array($pricebook);
			return $pricebook;
		}

		function read_vendor_pr_activity($data)
		{
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter			= isset($data['filter']) && $data['filter'] ? $data['filter'] : 'none';
				$query 			= isset($data['query'])?$data['query']:'';
				$sort 			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
				$order 			= isset($data['order'])?$data['order']:'';
				$cat_id 		= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
				$allrows 		= isset($data['allrows'])?$data['allrows']:'';
				$activity_id 	= isset($data['activity_id'])?$data['activity_id']:'';
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = " order by fm_vendor.org_name asc";
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " Where agreement_group_id='$cat_id' ";
			}
			else
			{
				$filtermethod = " Where agreement_group_id = ''";
			}
			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND (fm_vendor.org_name $this->like '%$query%' or vendor_id $this->like '%$query%')";
			}

			$sql = "SELECT fm_activities.id as activity_id,fm_activities.num, fm_vendor.org_name,fm_branch.descr as branch ,fm_agreement.id as agreement_id"
				. " FROM (fm_activities  $this->join fm_activity_price_index ON fm_activities.id = fm_activity_price_index.activity_id) "
				. " $this->join fm_agreement ON fm_activity_price_index.agreement_id = fm_agreement.id "
				. " $this->join fm_vendor ON fm_agreement.vendor_id = fm_vendor.id "
				. " $this->join fm_branch on fm_branch.id = fm_activities.branch_id "
				. " Where fm_activity_price_index.activity_id= '$activity_id' $querymethod group by fm_activities.id,fm_activities.num,"
				. " fm_branch.descr,org_name , fm_agreement.id ";

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

			$pricebook = array();
			while ($this->db->next_record())
			{
				$pricebook[] = array
					(
						'activity_id'	=> $this->db->f('activity_id'),
						'num'			=> $this->db->f('num'),
						'branch'		=> $this->db->f('branch'),
						'vendor_name'	=> $this->db->f('org_name'),
						//	'vendor_id'		=> $this->db->f('vendor_id'),
						'agreement_id'		=> $this->db->f('agreement_id')
					);
			}
			return $pricebook;
		}

		function read_single_activity($id)
		{
			$sql = "SELECT * FROM fm_activities where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$activity['activity_id']= $id;
				$activity['num']		= $this->db->f('num');
				$activity['unit']		= $this->db->f('unit');
				$activity['cat_id']		= $this->db->f('agreement_group_id');
				$activity['ns3420_id']	= $this->db->f('ns3420');
				$activity['descr']		= $this->db->f('descr',true);
				$activity['base_descr']	= $this->db->f('base_descr',true);
				$activity['dim_d']		= $this->db->f('dim_d');
				$activity['branch_id']	= $this->db->f('branch_id');

				return $activity;
			}
		}

		function read_single_agreement_group($id)
		{
			$sql = "SELECT * FROM fm_agreement_group where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$agreement_group['agreement_group_id']	= $id;
				$agreement_group['num']			= $this->db->f('num');
				$agreement_group['status']		= $this->db->f('status');
				$agreement_group['descr']		= $this->db->f('descr',true);

				return $agreement_group;
			}
		}

		function add_activity($values)
		{
			$values['descr'] = $this->db->db_addslashes($values['descr']);
			$values['base_descr'] = $this->db->db_addslashes($values['base_descr']);

			$vals= array(
				$values['activity_id'],
				$values['num'] ,
				$values['unit'] ,
				$values['cat_id'] , // agreement_group
				$values['ns3420_id'] ,
				$values['dim_d'],
				$values['branch_id'],
				$values['descr'],
				$values['base_descr']
			);

			$vals	= $this->db->validate_insert($vals);

			$this->db->transaction_begin();
			$this->db->query("INSERT INTO fm_activities (id, num,unit,agreement_group_id,ns3420,dim_d,branch_id,descr,base_descr) "
				. "VALUES ($vals)",__LINE__,__FILE__);
			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('Activity has been saved'));
			$receipt['activity_id']= $values['activity_id'];
			return $receipt;
		}

		function edit_activity($values)
		{
			$values['descr'] = $this->db->db_addslashes($values['descr']);
			$values['base_descr'] = $this->db->db_addslashes($values['base_descr']);

			$value_set=array(
				'num'					=> $values['num'],
				'unit'					=> $values['unit'],
				'agreement_group_id'	=> $values['cat_id'],
				'ns3420'				=> $values['ns3420_id'],
				'dim_d'					=> $values['dim_d'],
				'branch_id'				=> $values['branch_id'],
				'descr'					=> $values['descr'],
				'base_descr'			=> $values['base_descr']
			);

			$value_set	= $this->db->validate_update($value_set);
			$this->db->transaction_begin();
			$this->db->query("UPDATE fm_activities set $value_set WHERE id= '" . $values['activity_id'] . "'",__LINE__,__FILE__);
			$this->db->transaction_commit();

			$receipt['activity_id']= $values['activity_id'];
			$receipt['message'][] = array('msg'=>lang('Activity has been edited'));
			return $receipt;
		}

		function add_agreement_group($values)
		{
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$vals= array(
				$values['agreement_group_id'],
				$values['num'],
				$values['status'],
				$values['descr']
			);

			$vals	= $this->db->validate_insert($vals);

			$this->db->query("INSERT INTO fm_agreement_group (id,num,status,descr) "
				. "VALUES ($vals)",__LINE__,__FILE__);

			$receipt['message'][] = array('msg'=>lang('Agreement group has been saved'));
			$receipt['agreement_group_id'] = $values['agreement_group_id'];
			return $receipt;
		}

		function edit_agreement_group($values)
		{
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$value_set=array(
				'num'	=> $values['num'],
				'status'=> $values['status'],
				'descr'	=> $values['descr']
			);

			$value_set	= $this->db->validate_update($value_set);
			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_agreement_group set $value_set WHERE id= '" . $values['agreement_group_id'] . "'",__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('Agreement group has been edited'));
			return $receipt;
		}

		function delete_activity_vendor($activity_id,$agreement_id)
		{
			$this->db->query("DELETE FROM fm_activity_price_index WHERE activity_id='$activity_id' and agreement_id='$agreement_id'",__LINE__,__FILE__);
		}

		function delete_activity($activity_id)
		{
			$this->db->query("DELETE FROM fm_activities WHERE id='$activity_id'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_activity_price_index WHERE activity_id='$activity_id'",__LINE__,__FILE__);
		}

		function delete_prize_index($activity_id,$agreement_id,$index_count)
		{
			if ($index_count==1)
			{
				$this->db->query("update fm_activity_price_index set index_count = '1', current_index = '0', this_index=Null, m_cost=Null,w_cost=Null,total_cost=Null,index_date=Null  where activity_id='$activity_id' and agreement_id= '$agreement_id' and index_count= '1'",__LINE__,__FILE__);
			}
			else
			{
				$this->db->query("delete from fm_activity_price_index where activity_id='$activity_id' and agreement_id= '$agreement_id' and index_count= '$index_count'",__LINE__,__FILE__);

				$new_index_count= $index_count -1;

				$this->db->query("update fm_activity_price_index set current_index = '1' where activity_id='$activity_id' and agreement_id= '$agreement_id' and index_count= '$new_index_count'",__LINE__,__FILE__);
			}

		}

		function delete_agreement_group($agreement_group_id)
		{
			$this->db->query("DELETE FROM fm_agreement_group WHERE id='$agreement_group_id'",__LINE__,__FILE__);
			//how to handle the activities and vendors ...?
		}



		/**
		 * @todo remove or alter this function
		 */

		function add_activity_vendor($values)
		{
			$this->db->query("SELECT count(*) as cnt FROM fm_activity_price_index WHERE activity_id='" . $values['activity_id'] . "' and agreement_id='" . $values['agreement_id'] . "'",__LINE__,__FILE__);

			$this->db->next_record();

			if ( $this->db->f('cnt'))
			{
				$receipt['error'][] = array('msg'=>lang('This Vendor is already registered for this activity'));
			}
			else
			{
				$this->db->query("insert into fm_activity_price_index (activity_id, agreement_id, index_count,current_index,m_cost,w_cost,total_cost) "
					. " values ('" .
					$values['activity_id']. "','" .
					$values['agreement_id']. "','1','0',NULL,NULL,NULL)",__LINE__,__FILE__);

				$receipt['message'][] = array('msg'=>lang('Vendor has been added'));

			}
			return $receipt;
		}
	}
