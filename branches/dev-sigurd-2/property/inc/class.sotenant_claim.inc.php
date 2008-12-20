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

	/**
	 * Description
	 * @package property
	 */

	class property_sotenant_claim
	{
		function property_sotenant_claim()
		{
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();

			$this->join			= $this->bocommon->join;
			$this->like			= $this->bocommon->like;
			$this->interlink 	= CreateObject('property.interlink');
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
				switch($order)
				{
					case 'claim_id':
						$order = 'fm_tenant_claim.id';
						break;
					case 'name':
						$order = 'last_name';
						break;
				}
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by id DESC';
			}


			$where = 'WHERE';
			if ($cat_id > 0)
			{
				$filtermethod .= " $where fm_tenant_claim.category='$cat_id' ";
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
				$query = $this->db->db_addslashes($query);

				$querymethod = " $where ( first_name $this->like '%$query%' OR last_name $this->like '%$query%')";
			}

			$sql = "SELECT fm_tenant_claim.*, descr as category, fm_tenant.last_name, fm_tenant.first_name FROM fm_tenant_claim "
			 . " $this->join fm_tenant_claim_category on fm_tenant_claim.category=fm_tenant_claim_category.id"
			 . " $this->join fm_tenant ON fm_tenant_claim.tenant_id = fm_tenant.id"
			 . " $filtermethod $querymethod";

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

			$claims = array();
			while ($this->db->next_record())
			{
				$claims[] = array
				(
					'claim_id'		=> $this->db->f('id'),
					'project_id'	=> $this->db->f('project_id'),
					'tenant_id'		=> $this->db->f('tenant_id'),
					'name'			=> $this->db->f('last_name') . ', ' . $this->db->f('first_name'),
					'remark'		=> $this->db->f('remark',true),
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

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

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
			$claim = $this->interlink->get_specific_relation('property', '.project.workorder', '.tenant_claim', $workorder_id, 'origin');

			if ( $claim)
			{
				return implode(",", $claim);
			}
		}

		function read_single($id)
		{
			$id = (int) $id;
			$this->db->query("SELECT * FROM fm_tenant_claim WHERE id={$id}",__LINE__,__FILE__);

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

			$target = $this->interlink->get_specific_relation('property', '.project.workorder', '.tenant_claim', $id, 'origin');

			if ( $target)
			{
				$claim['workorder'] = $target;
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
				$interlink_data = array
				(
					'location1_id'		=> $GLOBALS['phpgw']->locations->get_id('property', '.project.workorder'),
					'location1_item_id' => $workorder_id,
					'location2_id'		=> $GLOBALS['phpgw']->locations->get_id('property', '.tenant_claim'),			
					'location2_item_id' => $claim_id,
					'account_id'		=> $this->account
				);
					
				$this->interlink->add($interlink_data,$this->db);

				$this->db->query("UPDATE fm_workorder SET claim_issued = 1 WHERE id=" . $workorder_id ,__LINE__,__FILE__);
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

			$this->db->query("UPDATE fm_tenant_claim set $value_set  WHERE id=" . (int)$claim['claim_id'],__LINE__,__FILE__);

			$claim_id = $claim['claim_id'];

			$this->interlink->delete_from_target('property', '.tenant_claim', $claim_id, $this->db);

			$this->db->query("UPDATE fm_workorder set claim_issued = NULL WHERE project_id = {$claim['project_id']}" ,__LINE__,__FILE__);

			foreach ($claim['workorder'] as $workorder_id)
			{
				$interlink_data = array
				(
					'location1_id'		=> $GLOBALS['phpgw']->locations->get_id('property', '.project.workorder'),
					'location1_item_id' => $workorder_id,
					'location2_id'		=> $GLOBALS['phpgw']->locations->get_id('property', '.tenant_claim'),			
					'location2_item_id' => $claim_id,
					'account_id'		=> $this->account
				);
					
				$this->interlink->add($interlink_data,$this->db);

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
			$this->interlink->delete_from_target('property', '.tenant_claim', $id, $this->db);
			$this->db->transaction_commit();

		}
	}

