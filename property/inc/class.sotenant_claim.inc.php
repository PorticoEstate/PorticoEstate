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

		function __construct()
		{
			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db		 = & $GLOBALS['phpgw']->db;
			$this->join		 = & $this->db->join;
			$this->like		 = & $this->db->like;
			$this->interlink = CreateObject('property.interlink');
		}

		function read( $data )
		{
			$start		 = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$user_id	 = isset($data['user_id']) && $data['user_id'] ? (int)$data['user_id'] : '0';
			$status		 = isset($data['status']) && $data['status'] ? $data['status'] : 'open';
			$query		 = isset($data['query']) ? $data['query'] : '';
			$sort		 = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order		 = isset($data['order']) ? $data['order'] : '';
			$cat_id		 = isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
			$allrows	 = isset($data['allrows']) ? $data['allrows'] : '';
			$project_id	 = isset($data['project_id']) ? $data['project_id'] : '';
			$ticket_id	 = isset($data['ticket_id']) ? $data['ticket_id'] : '';
			$district_id = isset($data['district_id']) ? (int)$data['district_id'] : 0;
			$results	 = isset($data['results']) ? (int)$data['results'] : 0;

			if ($order)
			{
				switch ($order)
				{
					case 'claim_id':
						$order	 = 'fm_tenant_claim.id';
						break;
					case 'name':
						$order	 = 'last_name';
						break;
				}
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by id DESC';
			}

			$filtermethod = '';
			$filtermethod_ticket = '';
			$where = 'WHERE';
			if ($cat_id > 0)
			{
				$filtermethod	 .= " $where fm_tenant_claim.category='$cat_id' ";
				$filtermethod_ticket	 .= " $where fm_tenant_claim.category='$cat_id' ";
				$where			 = 'AND';
			}

			if ($project_id > 0)
			{
				$filtermethod	 .= " $where project_id='$project_id' ";
				$filtermethod_ticket	 .= " $where ticket_id=-1 ";
				$where			 = 'AND';
			}

			if ($ticket_id > 0)
			{
				$filtermethod	 .= " $where project_id=-1 ";
				$filtermethod_ticket	 .= " $where ticket_id='{$ticket_id}' ";
				$where			 = 'AND';
			}

			if ($status && $status != 'all')
			{
				$filtermethod	 .= " $where fm_tenant_claim.status='{$status}'";
				$filtermethod_ticket	 .= " $where fm_tenant_claim.status='{$status}'";
				$where			 = 'AND';
			}

			if ($user_id > 0)
			{
				$filtermethod	 .= " $where fm_tenant_claim.user_id={$user_id} ";
				$filtermethod_ticket	 .= " $where fm_tenant_claim.user_id={$user_id} ";
				$where			 = 'AND';
			}

			if ($district_id > 0)
			{
				$filtermethod	 .= " $where  district_id=" . (int)$district_id;
				$filtermethod_ticket	 .= " $where  district_id=" . (int)$district_id;
				$where			 = 'AND';
			}

			$querymethod = '';
			if ($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " $where (address {$this->like} '%{$query}%'"
					. " OR first_name {$this->like} '%{$query}%'"
					. " OR last_name {$this->like} '%{$query}%'"
					. " OR fm_project.location_code {$this->like} '{$query}%'"
					. " OR fm_tenant.last_name || ', ' || fm_tenant.first_name {$this->like} '%{$query}%'"
					. " OR cast(fm_tenant_claim.id as text) {$this->like} '{$query}%'"
					. " OR project_id=" . (int)$query . ')';
				$querymethod_ticket = " $where (address {$this->like} '%{$query}%'"
					. " OR first_name {$this->like} '%{$query}%'"
					. " OR last_name {$this->like} '%{$query}%'"
					. " OR fm_tts_tickets.location_code {$this->like} '{$query}%'"
					. " OR fm_tenant.last_name || ', ' || fm_tenant.first_name {$this->like} '%{$query}%'"
					. " OR cast(fm_tenant_claim.id as text) {$this->like} '{$query}%'"
					. " OR ticket_id=" . (int)$query . ')';
			}

			$sql = "SELECT 'project' as type, fm_tenant_claim.*, fm_tenant_claim_category.descr as claim_category, fm_tenant.last_name, fm_tenant.first_name,district_id,"
				. " fm_project.address, fm_project.category, fm_project.location_code FROM fm_tenant_claim "
				. " $this->join fm_tenant_claim_category on fm_tenant_claim.category=fm_tenant_claim_category.id"
				. " $this->join fm_tenant on fm_tenant_claim.tenant_id=fm_tenant.id"
				. " $this->join fm_project ON fm_project.id = fm_tenant_claim.project_id"
				. " $this->join fm_location1 ON fm_project.loc1=fm_location1.loc1"
				. " $this->join fm_part_of_town ON fm_location1.part_of_town_id=fm_part_of_town.id"
				. " $filtermethod $querymethod";
			$sql .= " UNION "
				. " SELECT 'ticket' as type, fm_tenant_claim.*, fm_tenant_claim_category.descr as claim_category, fm_tenant.last_name, fm_tenant.first_name,district_id,"
				. " fm_tts_tickets.address, fm_tts_tickets.cat_id, fm_tts_tickets.location_code FROM fm_tenant_claim "
				. " $this->join fm_tenant_claim_category on fm_tenant_claim.category=fm_tenant_claim_category.id"
				. " $this->join fm_tenant on fm_tenant_claim.tenant_id=fm_tenant.id"
				. " $this->join fm_tts_tickets ON fm_tts_tickets.id = fm_tenant_claim.ticket_id"
				. " $this->join fm_location1 ON fm_tts_tickets.loc1=fm_location1.loc1"
				. " $this->join fm_part_of_town ON fm_location1.part_of_town_id=fm_part_of_town.id"
				. " $filtermethod_ticket $querymethod_ticket";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			if (!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
			}


			$claims = array();
			while ($this->db->next_record())
			{
				$claims[] = array
				(
					'type'			 => $this->db->f('type'),
					'claim_id'		 => $this->db->f('id'),
					'ticket_id'		 => $this->db->f('ticket_id'),
					'project_id'	 => $this->db->f('project_id'),
					'tenant_id'		 => $this->db->f('tenant_id'),
					'name'			 => $this->db->f('last_name') . ', ' . $this->db->f('first_name'),
					'remark'		 => $this->db->f('remark', true),
					'entry_date'	 => $this->db->f('entry_date'),
					'category'		 => $this->db->f('category'),
					'status'		 => $this->db->f('status'),
					'user_id'		 => $this->db->f('user_id'),
					'district_id'	 => $this->db->f('district_id'),
					'address'		 => $this->db->f('address', true),
					'category'		 => $this->db->f('category'),
					'location_code'	 => $this->db->f('location_code'),
					'claim_category' => $this->db->f('claim_category', true),
					'amount'		 => $this->db->f('amount')
				);
			}

			$soproject = CreateObject('property.soproject');
			foreach ($claims as &$claim)
			{
				$project_budget	 = $soproject->get_budget($claim['project_id']);
				$actual_cost	 = 0;
				foreach ($project_budget as $entry)
				{
					$actual_cost += (float)$entry['actual_cost'];
				}

				$claim['actual_cost'] = $actual_cost;
			}

			return $claims;
		}

		function check_claim_project( $project_id )
		{
			$sql = "SELECT fm_tenant_claim.*, descr as category FROM fm_tenant_claim"
				. " {$this->join} fm_tenant_claim_category on fm_tenant_claim.category=fm_tenant_claim_category.id"
				. " WHERE project_id = $project_id";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			$this->db->query($sql . $ordermethod, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$claims[] = array
					(
					'claim_id'	 => $this->db->f('id'),
					'project_id' => $this->db->f('project_id'),
					'tenant_id'	 => $this->db->f('tenant_id'),
					'entry_date' => $this->db->f('entry_date'),
					'category'	 => $this->db->f('category')
				);
			}
			return $claims;
		}

		function read_single( $id )
		{
			$id = (int)$id;
			$this->db->query("SELECT * FROM fm_tenant_claim WHERE id={$id}", __LINE__, __FILE__);

			if ($this->db->next_record())
			{
				$claim['id']			 = $id;
				$claim['project_id']	 = $this->db->f('project_id');
				$claim['ticket_id']		 = $this->db->f('ticket_id');
				$claim['tenant_id']		 = $this->db->f('tenant_id');
				$claim['remark']		 = $this->db->f('remark', true);
				$claim['entry_date']	 = $this->db->f('entry_date');
				$claim['cat_id']		 = (int)$this->db->f('category');
				$claim['amount']		 = $this->db->f('amount');
				$claim['b_account_id']	 = $this->db->f('b_account_id');
				$claim['cat_id']		 = (int)$this->db->f('category');
				$claim['status']		 = $this->db->f('status');
			}

			$targets = $this->interlink->get_specific_relation('property', '.project.workorder', '.tenant_claim', $id, 'origin');

			$claim['workorders']	 = $targets;
			$claim['claim_issued']	 = array();

			foreach ($targets as $workorder_id)
			{
				$this->db->query("SELECT claim_issued FROM fm_workorder WHERE id='{$workorder_id}' AND claim_issued = 1", __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$claim['claim_issued'][] = $workorder_id;
				}
			}

			$targets = $this->interlink->get_specific_relation('property', '.ticket', '.tenant_claim', $id, 'origin');

			$claim['tickets']	 = $targets;
			foreach ($targets as $id)
			{
				$this->db->query("SELECT claim_issued FROM fm_tts_tickets WHERE id='{$id}' AND claim_issued = 1", __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$claim['claim_issued'][] = $id;
				}
			}

			return $claim;
		}

		function add( $claim )
		{
			$this->db->transaction_begin();

			$claim['name']	 = $this->db->db_addslashes($claim['name']);
			$claim['amount'] = str_replace(",", ".", $claim['amount']);

			$values_insert = array(
				$claim['project_id'],
				$claim['ticket_id'],
				$claim['tenant_id'],
				$claim['amount'],
				$claim['b_account_id'],
				$claim['cat_id'],
				$claim['remark'],
				$this->account,
				time(),
				$claim['status']
			);

			$values_insert = $this->db->validate_insert($values_insert);


			$this->db->query("INSERT INTO fm_tenant_claim (project_id,ticket_id,tenant_id,amount,b_account_id,category,remark,user_id,entry_date,status) "
				. "VALUES ($values_insert)", __LINE__, __FILE__);

			$claim_id			 = $this->db->get_last_insert_id('fm_tenant_claim', 'id');
			$receipt['claim_id'] = $claim_id;

			foreach ($claim['workorder'] as $workorder_id)
			{
				$interlink_data = array
					(
					'location1_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.project.workorder'),
					'location1_item_id'	 => $workorder_id,
					'location2_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.tenant_claim'),
					'location2_item_id'	 => $claim_id,
					'account_id'		 => $this->account
				);

				$this->interlink->add($interlink_data, $this->db);

				$this->db->query("UPDATE fm_workorder SET claim_issued = 1 WHERE id=" . $workorder_id, __LINE__, __FILE__);
			}

			foreach ($claim['ticket'] as $ticket_id)
			{
				$interlink_data = array
					(
					'location1_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.ticket'),
					'location1_item_id'	 => $ticket_id,
					'location2_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.tenant_claim'),
					'location2_item_id'	 => $claim_id,
					'account_id'		 => $this->account
				);

				$this->interlink->add($interlink_data, $this->db);

				$this->db->query("UPDATE fm_tts_tickets SET claim_issued = 1 WHERE id=" . $ticket_id, __LINE__, __FILE__);
			}

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg' => lang('claim %1 has been saved', $claim_id));
			return $receipt;
		}

		function edit( $claim )
		{
			$historylog = CreateObject('property.historylog', 'tenant_claim');

			$this->db->transaction_begin();

			$this->db->query("select status from fm_tenant_claim where id=" . (int)$claim['claim_id'], __LINE__, __FILE__);
			$this->db->next_record();

			$old_status = $this->db->f('status');

			$claim['name']	 = $this->db->db_addslashes($claim['name']);
			$claim['amount'] = str_replace(",", ".", $claim['amount']);

			$value_set = array(
				'tenant_id'		 => $claim['tenant_id'],
				'b_account_id'	 => $claim['b_account_id'],
				'amount'		 => $claim['amount'],
				'category'		 => $claim['cat_id'],
				'status'		 => $claim['status'],
				'remark'		 => $this->db->db_addslashes($claim['remark'])
			);

			$value_set = $this->db->validate_update($value_set);

			$claim_id = (int)$claim['claim_id'];
			$this->db->query("UPDATE fm_tenant_claim set {$value_set} WHERE id={$claim_id}", __LINE__, __FILE__);

			if ($old_status != $claim['status'])
			{

				$historylog->add('S', $claim_id, $claim['status'], $old_status);
			}

			$this->interlink->delete_from_target('property', '.tenant_claim', $claim_id, $this->db);

			if(!empty($claim['project_id']))
			{
				$project_id = (int)$claim['project_id'];
				$this->db->query("UPDATE fm_workorder set claim_issued = NULL WHERE project_id = {$project_id}", __LINE__, __FILE__);
			}
			if(!empty($claim['ticket_id']))
			{
				$ticket_id = (int)$claim['ticket_id'];
				$this->db->query("UPDATE fm_tts_tickets set claim_issued = NULL WHERE id = {$ticket_id}", __LINE__, __FILE__);
			}

			foreach ($claim['workorder'] as $workorder_id)
			{
				$interlink_data = array
					(
					'location1_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.project.workorder'),
					'location1_item_id'	 => $workorder_id,
					'location2_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.tenant_claim'),
					'location2_item_id'	 => $claim_id,
					'account_id'		 => $this->account
				);

				$this->interlink->add($interlink_data, $this->db);

				$this->db->query("UPDATE fm_workorder set claim_issued = 1 WHERE id=" . $workorder_id, __LINE__, __FILE__);
			}

			foreach ($claim['ticket'] as $ticket_id)
			{
				$interlink_data = array
					(
					'location1_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.ticket'),
					'location1_item_id'	 => $ticket_id,
					'location2_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.tenant_claim'),
					'location2_item_id'	 => $claim_id,
					'account_id'		 => $this->account
				);

				$this->interlink->add($interlink_data, $this->db);

				$this->db->query("UPDATE fm_tts_tickets SET claim_issued = 1 WHERE id=" . (int)$ticket_id, __LINE__, __FILE__);
			}

			$this->db->transaction_commit();

			$receipt['claim_id']	 = $claim['claim_id'];
			$receipt['message'][]	 = array('msg' => lang('claim %1 has been edited', $claim['claim_id']));
			return $receipt;
		}

		function delete( $id )
		{
			$this->db->transaction_begin();
			$this->db->query('DELETE FROM fm_tenant_claim WHERE id=' . (int)$id, __LINE__, __FILE__);
			$this->interlink->delete_from_target('property', '.tenant_claim', (int)$id, $this->db);
			$this->db->transaction_commit();
		}

		public function close( $id )
		{
			$claim_id	 = (int)$id;
			$historylog	 = CreateObject('property.historylog', 'tenant_claim');

			$this->db->transaction_begin();

			$this->db->query("SELECT status FROM fm_tenant_claim WHERE id=" . $claim_id, __LINE__, __FILE__);
			$this->db->next_record();
			$old_status = $this->db->f('status');


			$value_set = array(
				'status' => 'closed',
			);

			$value_set_update = $this->db->validate_update($value_set);

			$this->db->query("UPDATE fm_tenant_claim SET {$value_set_update} WHERE id={$claim_id}", __LINE__, __FILE__);

			if ($old_status != 'closed')
			{
				$historylog->add('S', $claim_id, 'closed', $old_status);
			}

			return $this->db->transaction_commit();
		}
	}