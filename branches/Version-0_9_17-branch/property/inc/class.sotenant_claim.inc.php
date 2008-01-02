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
 	* @version $Id: class.sotenant_claim.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_sotenant_claim
	{
		function property_sotenant_claim()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();

			$this->join			= $this->bocommon->join;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start	= (isset($data['start'])?$data['start']:0);
				$filter	= (isset($data['filter'])?$data['filter']:'none');
				$status	= (isset($data['status'])?$data['status']:'open');
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$allrows 	= (isset($data['allrows'])?$data['allrows']:'');
				$project_id 	= (isset($data['project_id'])?$data['project_id']:'');

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
			if ($cat_id > 0)
			{
				$filtermethod .= " $where category='$cat_id' ";
				$where = 'AND';
			}

			if ($project_id > 0)
			{
				$filtermethod .= " $where project_id='$project_id' ";
				$where = 'AND';
			}

			if ($status == 'closed'):
			{
				$filtermethod .= " $where fm_tenant_claim.status='closed'";
				$where = 'AND';
			}
			elseif($status == ''):
			{
				$filtermethod .= " $where fm_tenant_claim.status='open'";
				$where = 'AND';
			}
			endif;

			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " $where ( abid = '$query' or org_name LIKE '%$query%')";
			}

			$sql = "SELECT fm_tenant_claim.*, descr as category FROM fm_tenant_claim $this->join fm_tenant_claim_category on fm_tenant_claim.category=fm_tenant_claim_category.id $filtermethod $querymethod";

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
				$claims[] = array
				(
					'claim_id'		=> $this->db->f('id'),
					'project_id'	=> $this->db->f('project_id'),
					'tenant_id'		=> $this->db->f('tenant_id'),
					'remark'		=> stripslashes($this->db->f('remark')),
					'entry_date'	=> $this->db->f('entry_date'),
					'category'		=> $this->db->f('category'),
					'status'		=> $this->db->f('status')
				);
			}
			return $claims;
		}

		function check_claim_project($project_id)
		{
			$sql = "SELECT fm_tenant_claim.*, descr as category FROM fm_tenant_claim"
			 . " $this->join fm_tenant_claim_category on fm_tenant_claim.category=fm_tenant_claim_category.id"
			 . " WHERE project_id = $project_id";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$claims[] = array
				(
					'claim_id'		=> $this->db->f('id'),
					'project_id'	=> $this->db->f('project_id'),
					'tenant_id'		=> $this->db->f('tenant_id'),
					'entry_date'	=> $this->db->f('entry_date'),
					'category'		=> $this->db->f('category')
				);
			}
			return $claims;
		}

		function check_claim_workorder($workorder_id)
		{
			$this->db->query("select * from fm_origin WHERE destination ='tenant_claim' AND origin_id='$workorder_id'",__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$claim[] = $this->db->f('destination_id');
			}

			return @implode(",", $claim);
		}

		function read_single($id)
		{
			$this->db->query("select * from fm_tenant_claim where id='$id'",__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$claim['id']			= $id;
				$claim['project_id']	= $this->db->f('project_id');
				$claim['tenant_id']		= $this->db->f('tenant_id');
				$claim['remark']		= stripslashes($this->db->f('remark'));
				$claim['entry_date']	= $this->db->f('entry_date');
				$claim['cat_id']		= (int)$this->db->f('category');
				$claim['amount']		= $this->db->f('amount');
				$claim['b_account_id']	= $this->db->f('b_account_id');
				$claim['cat_id']		= (int)$this->db->f('category');
				$claim['status']		= $this->db->f('status');

			}

			$this->db->query("select * from fm_origin WHERE destination ='tenant_claim' AND destination_id='$id'",__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$claim['workorder'][] = $this->db->f('origin_id');
			}

			return $claim;
		}

		function add($claim)
		{
			$this->db->transaction_begin();

			$claim['name'] = $this->db->db_addslashes($claim['name']);
			$claim['amount'] =  str_replace(",",".",$claim['amount']);

			$values_insert= array(
				$claim['project_id'],
				$claim['tenant_id'],
				$claim['amount'],
				$claim['b_account_id'],
				$claim['cat_id'],
				$claim['remark'],
				$this->account,
				time(),
				$claim['status']
				);

			$values_insert	= $this->bocommon->validate_db_insert($values_insert);


			$this->db->query("INSERT INTO fm_tenant_claim (project_id,tenant_id,amount,b_account_id,category,remark,user_id,entry_date,status) "
				. "VALUES ($values_insert)",__LINE__,__FILE__);

			$claim_id = $this->db->get_last_insert_id('fm_tenant_claim','id');
			$receipt['claim_id'] = $claim_id;

			foreach ($claim['workorder'] as $workorder_id)
			{
				$this->db->query("INSERT INTO fm_origin (origin,origin_id,destination,destination_id,entry_date,user_id) "
				. "VALUES ('workorder',"
				. $workorder_id .","
				. "'tenant_claim',"
				. $claim_id . ","
				. time().","
				. $this->account .")",__LINE__,__FILE__);

				$this->db->query("UPDATE fm_workorder set claim_issued = 1 WHERE id=" . $workorder_id ,__LINE__,__FILE__);
			}

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('claim %1 has been saved',$claim_id));
			return $receipt;
		}

		function edit($claim)
		{
			$this->db->transaction_begin();

			$claim['name'] = $this->db->db_addslashes($claim['name']);
			$claim['amount'] =  str_replace(",",".",$claim['amount']);

			$value_set=array(
				'amount'			=> $claim['amount'],
				'tenant_id'			=> $claim['tenant_id'],
				'b_account_id'		=> $claim['b_account_id'],
				'amount'			=> $claim['amount'],
				'category'			=> $claim['cat_id'],
				'status'			=> $claim['status'],
				'user_id'			=> $this->account,
				'remark'			=> $this->db->db_addslashes($claim['remark'])
				);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE fm_tenant_claim set $value_set  WHERE id=" . intval($claim['claim_id']),__LINE__,__FILE__);

			$claim_id = $claim['claim_id'];

			$this->db->query("DELETE FROM fm_origin WHERE destination ='tenant_claim' AND destination_id=$claim_id",__LINE__,__FILE__);

			$this->db->query("UPDATE fm_workorder set claim_issued = NULL WHERE id=" . $claim['project_id'] ,__LINE__,__FILE__);

			foreach ($claim['workorder'] as $workorder_id)
			{
				$this->db->query("INSERT INTO fm_origin (origin,origin_id,destination,destination_id,entry_date,user_id) "
				. "VALUES ('workorder',"
				. $workorder_id .","
				. "'tenant_claim',"
				. $claim_id . ","
				. time().","
				. $this->account .")",__LINE__,__FILE__);

				$this->db->query("UPDATE fm_workorder set claim_issued = 1 WHERE id=" . $workorder_id ,__LINE__,__FILE__);
			}

			$this->db->transaction_commit();

			$receipt['claim_id']= $claim['claim_id'];
			$receipt['message'][] = array('msg'=>lang('claim %1 has been edited',$claim['claim_id']));
			return $receipt;
		}

		function delete($id)
		{
			$this->db->transaction_begin();
			$this->db->query('DELETE FROM fm_tenant_claim WHERE id=' . intval($id),__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_origin WHERE destination ='tenant_claim' AND destination_id=$id",__LINE__,__FILE__);
			$this->db->transaction_commit();

		}
	}
?>
