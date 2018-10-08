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
	 * @subpackage helpdesk
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */
	class property_sotts
	{

		var $fields_updated = false;
		var $uicols_related = array();
		var $acl_location = '.ticket';
		public $total_records = 0;
		public $sum_budget = 0;
		public $sum_actual_cost = 0;
		public $sum_difference = 0;
		protected $global_lock = false;
		public $soap_functions = array
			(
			'read' => array(
				'in' => array('array'),
				'out' => array('array')
			)
		);
		public $xmlrpc_methods = array
			(
			array
				(
				'name' => 'read',
				'decription' => 'Get list of tickets'
			)
		);

		function __construct()
		{
			$this->account = (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->historylog = CreateObject('property.historylog', 'tts');
			$this->custom = createObject('property.custom_fields');
			$this->db = & $GLOBALS['phpgw']->db;
			$this->like = & $this->db->like;
			$this->join = & $this->db->join;
			$this->left_join = & $this->db->left_join;
			$this->dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		}

		function list_methods( $_type = 'xmlrpc' )
		{
			/*
			  This handles introspection or discovery by the logged in client,
			  in which case the input might be an array.  The server always calls
			  this function to fill the server dispatch map using a string.
			 */
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch ($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'read' => array(
							'function' => 'read',
							'signature' => array(array(xmlrpcArray, xmlrpcArray)),
							'docstring' => 'Get list of tickets'
						),
					);
					return $xml_functions;
					break;
				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
					break;
			}
		}

		function get_category_name( $cat_id )
		{
			$this->db->query("SELECT descr FROM fm_tts_category  WHERE id='$cat_id' ");
			$this->db->next_record();
			return $this->db->f('descr', true);
		}

		function get_data_report( $data )
		{
			$type = isset($data['type']) && $data['type'] ? (int)$data['type'] : 0;
			$start_date = isset($data['start_date']) && $data['start_date'] ? (int)$data['start_date'] : 0;
			$end_date = isset($data['end_date']) && $data['end_date'] ? (int)$data['end_date'] :  mktime(23, 59, 59, date("n"),date("j"),date("Y"));
			$_end_date = $end_date + 3600 * 16 + phpgwapi_datetime::user_timezone();
			$_start_date = $start_date - 3600 * 8 + phpgwapi_datetime::user_timezone();

			$data_report = array();
			if($type ==1)
			{
				$fields = "count(cat_id) as count_category, cat_id";
				$groupmethod = " GROUP BY cat_id";
			}
			else if ($type ==2)
			{
				$fields = "count(status) as count_status, status";
				$groupmethod = " GROUP BY status";

			}
			else
			{
				return array();
			}

			$sql = "SELECT {$fields} FROM fm_tts_tickets WHERE fm_tts_tickets.entry_date >= $_start_date AND fm_tts_tickets.entry_date <= $_end_date "
				. " {$groupmethod}";

			$this->db->query($sql, __LINE__, __FILE__);

			while($this->db->next_record())
			{
				$data_report[] = array(
					'status'			=> $this->db->f('status'),
					'count_status'		=> $this->db->f('count_status'),
					'cat_id'			=> $this->db->f('cat_id'),
					'count_category'	=> $this->db->f('count_category'),
				);
			}
			return $data_report;
		}

		function read( $data )
		{
			$start = isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$status_id = isset($data['status_id']) && $data['status_id'] ? $data['status_id'] : 'O'; //O='Open'
			$user_id = isset($data['user_id']) && $data['user_id'] ? abs((int)$data['user_id']) : 0;
			$reported_by = isset($data['reported_by']) && $data['reported_by'] ? (int)$data['reported_by'] : 0;
			$query = isset($data['query']) ? $data['query'] : '';
			$sort = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order = isset($data['order']) ? $data['order'] : '';
			$cat_id = isset($data['cat_id']) && $data['cat_id'] ? (int)$data['cat_id'] : 0;
			$district_id = isset($data['district_id']) && $data['district_id'] ? (int)$data['district_id'] : 0;
			$part_of_town_id = isset($data['part_of_town_id']) && $data['part_of_town_id'] ? (int)$data['part_of_town_id'] : 0;
			$start_date = isset($data['start_date']) && $data['start_date'] ? (int)$data['start_date'] : 0;
			$results = isset($data['results']) && $data['results'] ? (int)$data['results'] : 0;
			$allrows = $results == -1 ? true : false;
			$end_date = isset($data['end_date']) && $data['end_date'] ? (int)$data['end_date'] :  mktime(23, 59, 59, date("n"),date("j"),date("Y"));
			$external = isset($data['external']) ? $data['external'] : '';
			$dry_run = isset($data['dry_run']) ? $data['dry_run'] : '';
			$new = isset($data['new']) ? $data['new'] : '';
			$location_code = isset($data['location_code']) ? $data['location_code'] : '';
			$p_num = isset($data['p_num']) ? $data['p_num'] : '';
			$vendor_id = isset($data['vendor_id']) && $data['vendor_id'] ? (int)$data['vendor_id'] : 0;
			$ecodimb = isset($data['ecodimb']) && $data['ecodimb'] ? (int)$data['ecodimb'] : 0;
			$b_account = isset($data['b_account']) && $data['b_account'] ? $data['b_account'] : '';
			$building_part = isset($data['building_part']) && $data['building_part'] ? $data['building_part'] : '';
			$branch_id = isset($data['branch_id']) && $data['branch_id'] ? (int)$data['branch_id'] : 0;
			$order_dim1 = isset($data['order_dim1']) && $data['order_dim1'] ? (int)$data['order_dim1'] : 0;
			$custom_filtermethod = isset($data['custom_filtermethod']) && $data['custom_filtermethod'] ? (array)$data['custom_filtermethod'] : array();
			$check_date_type = isset($data['check_date_type']) && $data['check_date_type'] ? (int)$data['check_date_type'] : 1;


			$result_order_field = array();
			$order_join = " {$this->join} phpgw_accounts ON fm_tts_tickets.user_id=phpgw_accounts.account_id";

			if ($order)
			{
				if ($order == 'assignedto')
				{
			//		$result_order_field = array('account_lastname' => 'account_lastname');
			//		$order_join = " LEFT OUTER JOIN phpgw_accounts ON fm_tts_tickets.assignedto=phpgw_accounts.account_id";
			//		$order = 'account_lastname';
				}
				else if ($order == 'user')
				{
					$result_order_field = array('account_lastname' => 'account_lastname');
					$order = 'account_lastname';
				}

				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY id DESC';
			}
			$union_select = false;
			$filtermethod = '';

			$where = 'WHERE';

			$config = $GLOBALS['phpgw']->config->read();

			if (!$start_date && (!isset($config['bypass_acl_at_tickets']) || !$config['bypass_acl_at_tickets']))
			{
				$order_join .= " {$this->join} phpgw_group_map ON (phpgw_accounts.account_id = phpgw_group_map.account_id)";
				$GLOBALS['phpgw']->acl->set_account_id($this->account);
				$grants = $GLOBALS['phpgw']->acl->get_grants2('property', '.ticket');

				$public_user_list = array();
				if (isset($config['acl_at_tts_category']) && $config['acl_at_tts_category'])
				{
					$categories = $GLOBALS['phpgw']->locations->get_subs('property', '.ticket.category');

					$category_grants = array();
					foreach ($categories as $location)
					{
						$category_grants = $GLOBALS['phpgw']->acl->get_grants2('property', $location);
						foreach ($category_grants['accounts'] as $user => $right)
						{
							$grants['accounts'][$user] = $right;
						}
						unset($user);
						unset($right);
						foreach ($category_grants['groups'] as $user => $right)
						{
							$grants['groups'][$user] = $right;
						}
						unset($user);
						unset($right);
					}
				}

				if (isset($config['acl_at_location']) && $config['acl_at_location'])
				{
					$access_location = execMethod('property.socommon.get_location_list', PHPGW_ACL_READ);
					if ($access_location)
					{
						$filtermethod .= " $where fm_tts_tickets.loc1 in ('" . implode("','", $access_location) . "')";
						$where = 'AND';
					}
				}

				$public_user_list = array();
				if (is_array($grants['accounts']) && $grants['accounts'])
				{
					foreach ($grants['accounts'] as $user => $right)
					{
						$public_user_list[] = $user;
					}
					unset($user);

					reset($public_user_list);
					$filtermethod .= " $where ( fm_tts_tickets.user_id IN(" . implode(',', $public_user_list) . ")";

					$where = 'AND';
				}

				$public_group_list = array();
				if (is_array($grants['groups']) && $grants['groups'])
				{
					foreach($grants['groups'] as $user => $_right)
					{
						$public_group_list[] = $user;
					}
					unset($user);
					reset($public_group_list);
					$where = $public_user_list ? 'OR' : $where;
					$filtermethod .= " $where phpgw_group_map.group_id IN(" . implode(',', $public_group_list) . "))";
					$where = 'AND';
				}
				if($public_user_list && !$public_group_list)
				{
					$filtermethod .=')';
				}
			}

			if ($tenant_id = $GLOBALS['phpgw']->session->appsession('tenant_id', 'property'))
			{
				$filtermethod .= $where . ' fm_tts_tickets.tenant_id=' . $tenant_id;
				$where = 'AND';
			}


			$custom_status = $this->get_custom_status();
			$closed_status = array('X');
			foreach ($custom_status as $custom)
			{
				if ($custom['closed'])
				{
					$closed_status[] = "C{$custom['id']}";
				}
			}
			reset($custom_status);
			$filter_closed = " AND fm_tts_tickets.status NOT IN ('" . implode("','", $closed_status) . "')";

			//get variants of closed
			if ($status_id == 'X')
			{
				$filtermethod .= " $where ( fm_tts_tickets.status IN ('" . implode("','", $closed_status) . "')";
				$where = 'AND';
			}
			else if ($status_id == 'O2') // explicite 'open'
			{
				$filtermethod .= " $where ( fm_tts_tickets.status='O'";
				$where = 'AND';
			}
			else if ($status_id == 'O')
			{
				$open = '';
				foreach ($custom_status as $custom)
				{
					if (!$custom['closed'])
					{
						$open .= " OR fm_tts_tickets.status = 'C{$custom['id']}'";
					}
				}

				$filtermethod .= " $where ( (fm_tts_tickets.status='O'{$open})";
				$where = 'AND';
			}
			else if ($status_id == 'all')
			{
				$filtermethod .= "{$where} (1=1";//nothing
				$where = 'AND';
			}
			else if (is_array($status_id) && count($status_id))
			{
				$or = '';
				$filtermethod .= "{$where} ((";

				foreach ($status_id as $value)
				{
					if ($value)
					{
						$filtermethod .= "{$or} fm_tts_tickets.status = '{$value}'";
						$or = ' OR';
					}
				}

				$filtermethod .= ')';

				$where = 'AND';
			}
			else
			{
				$filtermethod .= " $where (fm_tts_tickets.status='{$status_id}'";
				$where = 'AND';
			}

			if ($new)
			{
				$filtermethod .= " OR fm_tts_views.id IS NULL )";
			}
			else
			{
				$filtermethod .= ')';
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " $where cat_id=" . (int)$cat_id;
				$where = 'AND';
			}

			if ($vendor_id > 0)
			{
				$filtermethod .= " $where vendor_id=" . (int)$vendor_id;
				$where = 'AND';
			}
			if ($ecodimb > 0)
			{
				$filtermethod .= " $where ecodimb=" . (int)$ecodimb;
				$where = 'AND';
			}
			if ($b_account > 0)
			{
				$filtermethod .= " $where b_account_id='{$b_account}'";
				$where = 'AND';
			}
			if ($building_part)
			{
				$filtermethod .= " $where building_part='{$building_part}'";
				$where = 'AND';
			}
			if ($branch_id > 0)
			{
				$filtermethod .= " $where branch_id=" . (int)$branch_id;
				$where = 'AND';
			}
			if ($order_dim1 > 0)
			{
				$filtermethod .= " $where order_dim1=" . (int)$order_dim1;
				$where = 'AND';
			}

			if ($user_id > 0)
			{
				$filtermethod .= " {$where} (assignedto={$user_id}";
				$where = 'AND';

				if (!$membership = $GLOBALS['phpgw']->accounts->membership($user_id))
				{
					$membership = array(-1 => 0);
				}
				$filtermethod .= ' OR (assignedto IS NULL AND fm_tts_tickets.group_id IN (' . implode(',', array_keys($membership)) . ')))';
			}

			if ($user_id < 0)
			{
				$filtermethod .= " {$where} fm_tts_tickets.user_id=" . (int)abs($user_id);
				$where = 'AND';
			}

			if ($reported_by > 0)
			{
				$filtermethod .= " $where fm_tts_tickets.user_id=" . (int)$reported_by;
				$where = 'AND';
			}

			if ($district_id > 0)
			{
				$filtermethod .= " $where  district_id=" . (int)$district_id;
				$where = 'AND';
			}

			if ($part_of_town_id > 0)
			{
				$filtermethod .= " $where fm_part_of_town.id= " . (int)$part_of_town_id;
				$where = 'AND';
			}

			$actual_cost_field = array('actual_cost' => 'fm_tts_tickets.actual_cost');
			$budget_field = array('budget' => 'fm_tts_tickets.budget');

			$actual_cost_group_field = ',fm_tts_tickets.actual_cost';
			$budget_group_field = ',fm_tts_tickets.budget';
			$date_cost_join = '';
			$date_budget_join = '';

			if ($start_date)
			{
				$order_add = $GLOBALS['phpgw']->acl->check('.ticket.order', PHPGW_ACL_ADD, 'property');
				$order_edit = $GLOBALS['phpgw']->acl->check('.ticket.order', PHPGW_ACL_EDIT, 'property');
				$_end_date = $end_date + 3600 * 16 + phpgwapi_datetime::user_timezone();
				$_start_date = $start_date - 3600 * 8 + phpgwapi_datetime::user_timezone();
				if($check_date_type == 1)
				{
					$filtermethod .= " $where fm_tts_tickets.modified_date >= $_start_date AND fm_tts_tickets.modified_date <= $_end_date ";
				}
				else if($check_date_type == 2)
				{
					$filtermethod .= " $where fm_tts_tickets.entry_date >= $_start_date AND fm_tts_tickets.entry_date <= $_end_date ";
				}

				if ($order_add || $order_edit)
				{
					$union_select = true;
					$start_period = date('Ym', $start_date);
					$end_period = date('Ym', $end_date);
//					$filtermethod .= " OR (fm_tts_payments.period >= {$start_period} AND fm_tts_payments.period <= {$end_period})";
					$date_cost_join = " LEFT OUTER JOIN fm_tts_payments ON ( fm_tts_tickets.id=fm_tts_payments.ticket_id AND fm_tts_payments.period >= $start_period AND fm_tts_payments.period <= $end_period )";
//					$actual_cost_field = 'SUM(fm_tts_payments.amount) AS actual_cost';
					$actual_cost_field = array('SUM(actual_cost) AS actual_cost' => 'fm_tts_payments.amount as actual_cost');

					$actual_cost_group_field = '';

					$start_budget_period = date('Y', $start_date) . '00';
					$end_budget_period = date('Y', $end_date) . '13';
//					$filtermethod .= " OR (fm_tts_budget.period >= {$start_budget_period} AND fm_tts_budget.period <= {$end_budget_period}))";
					$date_budget_join = " LEFT OUTER JOIN fm_tts_budget ON ( fm_tts_tickets.id=fm_tts_budget.ticket_id AND fm_tts_budget.period >= $start_budget_period AND fm_tts_budget.period <= $end_budget_period )";
//					$budget_field = 'SUM(fm_tts_budget.amount) AS budget';
					$budget_field = array('SUM(budget) AS budget' => 'fm_tts_budget.amount as budget');
					$budget_group_field = '';
				}
				else
				{
//					$filtermethod .= ')';
				}

				$where = 'AND';
			}

			if ($location_code)
			{
				$filtermethod .= " $where fm_tts_tickets.location_code {$this->like} '{$location_code}%'";
				$where = 'AND';
			}

			foreach ($custom_filtermethod as $custom_filter_key => $custom_filter_value)
			{
				$filtermethod .= " $where fm_tts_tickets.{$custom_filter_key} = '{$custom_filter_value}'";
				$where = 'AND';
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');

			$querymethod = '';
			if ($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where ((subject $this->like '%$query%'"
					. " OR address $this->like '%$query%' "
					. " OR fm_location1.loc1_name $this->like '%$query%'"
					. " OR fm_tts_tickets.location_code $this->like '%$query%'";

				if (ctype_digit($query))
				{
					$querymethod .= " OR cast(fm_tts_tickets.order_id as text) {$this->like} '$query%'";
			//		$querymethod .= " OR fm_tts_tickets.order_id =" . (int)$query;
					$querymethod .= " OR fm_tts_tickets.id =" . (int)$query . ')';
				}
				else
				{
					$querymethod .= ')';
				}

				$query = str_replace(",", '.', $query);
				if (stristr($query, '.'))
				{
					if (!$p_num)
					{
						$query = explode(".", $query);
						$querymethod .= " OR (fm_tts_tickets.loc1='{$query[0]}' AND fm_tts_tickets.loc4='{$query[1]}')";
					}
					else
					{
						$query = explode(".", $query);
						$querymethod = " {$where} (fm_tts_tickets.p_entity_id='" . (int)$query[1] . "' AND fm_tts_tickets.p_cat_id='" . (int)$query[2] . "' AND fm_tts_tickets.p_num='{$query[3]}')";
					}
				}

				$custom_filter = $this->custom->get_custom_filter($location_id, 'fm_tts_tickets', $criteria_id = '', $query);

				if ($custom_filter['querymethod'])
				{
					$_where = $where == 'AND' ? 'OR' : 'WHERE';
					$querymethod .= " $_where (" . implode(' OR ', $custom_filter['querymethod']) . ')';
				}
				else if (isset($custom_filter['joinmethod_datatype']) && $custom_filter['joinmethod_datatype'])
				{
					$querymethod = '';
				}
				if ($querymethod)
				{
					$querymethod .= ')';
				}
			}

			$_return_field_array = array(
				'id' => 'fm_tts_tickets.id',
				'assignedto' => 'fm_tts_tickets.assignedto',
				'status' => 'fm_tts_tickets.status',
				'user_id' => 'fm_tts_tickets.user_id',
				'subject' => 'fm_tts_tickets.subject',
				'address' => 'fm_tts_tickets.address',
				'location_code' => 'fm_tts_tickets.location_code',
				'priority' => 'fm_tts_tickets.priority',
				'cat_id' => 'fm_tts_tickets.cat_id',
				'group_id' => 'fm_tts_tickets.group_id',
				'entry_date' => 'fm_tts_tickets.entry_date',
				'modified_date' => 'fm_tts_tickets.modified_date',
				'finnish_date' => 'fm_tts_tickets.finnish_date',
				'finnish_date2' => 'fm_tts_tickets.finnish_date2',
				'order_id' => 'fm_tts_tickets.order_id',
				'vendor_id' => 'fm_tts_tickets.vendor_id',
				'billable_hours' => 'fm_tts_tickets.billable_hours',
				'district' => 'fm_district.descr as district',
				'view' => 'fm_tts_views.id as view',
				'loc1_name' => 'fm_location1.loc1_name',
				'ecodimb' => 'fm_tts_tickets.ecodimb',
				'order_dim1' => 'fm_tts_tickets.order_dim1',
				'external_project_id' => 'fm_tts_tickets.external_project_id',
				'contract_id' => 'fm_tts_tickets.contract_id',
				'service_id' => 'fm_tts_tickets.service_id',
				'tax_code' => 'fm_tts_tickets.tax_code',
				'unspsc_code' => 'fm_tts_tickets.unspsc_code',
				'b_account_id' => 'fm_tts_tickets.b_account_id',
				'continuous' => 'fm_tts_tickets.continuous',
				'order_deadline' => 'fm_tts_tickets.order_deadline',
				'order_deadline2' => 'fm_tts_tickets.order_deadline2',
				'details' => 'fm_tts_tickets.details',
			);

			$custom_cols = $this->custom->find('property', '.ticket', 0, '', 'ASC', 'attrib_sort', true, true);

			foreach ($custom_cols as $custom_col)
			{
				$_return_field_array[$custom_col['column_name']] = "fm_tts_tickets.{$custom_col['column_name']}";
			}

			if ($result_order_field)
			{
				$_return_field_array = array_merge($_return_field_array, $result_order_field);
			}

			$return_field_array = array_merge($_return_field_array, $actual_cost_field);
			$return_field_array = array_merge($return_field_array, $budget_field);

			$return_fields_union = implode(',', array_values($_return_field_array));
			$return_fields = implode(',', array_keys($return_field_array));
			$return_fields_plain = implode(',', array_values($return_field_array));

			$union_budget = " 0 as actual_cost ,SUM(fm_tts_budget.amount) as budget FROM fm_tts_tickets"
				. " {$this->left_join} fm_location1 ON fm_tts_tickets.loc1=fm_location1.loc1"
				. " {$this->left_join} fm_part_of_town ON fm_location1.part_of_town_id=fm_part_of_town.id"
				. " {$this->left_join} fm_district ON fm_district.id = fm_part_of_town.district_id"
				. " {$order_join}{$date_budget_join}"
				. " LEFT OUTER JOIN fm_tts_views ON (fm_tts_tickets.id = fm_tts_views.id AND fm_tts_views.account_id='{$this->account}')";
			$union_cost = " SUM(fm_tts_payments.amount) as actual_cost, 0 as budget FROM fm_tts_tickets"
				. " {$this->left_join} fm_location1 ON fm_tts_tickets.loc1=fm_location1.loc1"
				. " {$this->left_join} fm_part_of_town ON fm_location1.part_of_town_id=fm_part_of_town.id"
				. " {$this->left_join} fm_district ON fm_district.id = fm_part_of_town.district_id"
				. " {$order_join}{$date_cost_join}"
				. " LEFT OUTER JOIN fm_tts_views ON (fm_tts_tickets.id = fm_tts_views.id AND fm_tts_views.account_id='{$this->account}')";

			if (isset($custom_filter['joinmethod_datatype']) && $custom_filter['joinmethod_datatype'])
			{
				foreach ($custom_filter['joinmethod_datatype'] as $_joinmethod)
				{
					$union_budget .= $_joinmethod;
					$union_cost .= $_joinmethod;
				}
			}

			$sql = "SELECT DISTINCT {$return_fields_plain} FROM fm_tts_tickets"
				. " {$this->left_join} fm_location1 ON fm_tts_tickets.loc1=fm_location1.loc1"
				. " {$this->left_join} fm_part_of_town ON fm_location1.part_of_town_id=fm_part_of_town.id"
				. " {$this->left_join} fm_district ON fm_district.id = fm_part_of_town.district_id"
				. " {$order_join}{$date_cost_join}{$date_budget_join}"
				. " LEFT OUTER JOIN fm_tts_views ON (fm_tts_tickets.id = fm_tts_views.id AND fm_tts_views.account_id='{$this->account}')";

			if (isset($custom_filter['joinmethod_datatype']) && $custom_filter['joinmethod_datatype'])
			{
				foreach ($custom_filter['joinmethod_datatype'] as $_joinmethod)
				{
					$sql .= $_joinmethod;
				}
			}

			$limit_and_offset = '';

			if (!$allrows && $union_select)
			{
				$allrows = true;//handled within the subselect
				$limit_and_offset = $this->db->get_offset('', $start);
			}

			$group_fields = str_ireplace(array('fm_district.descr as district', 'fm_tts_views.id as view'), array(
				'fm_district.descr', 'fm_tts_views.id'), $return_fields_plain);

			if ($union_select)
			{
				$cache_test = "{$return_fields_union},{$union_budget} {$filtermethod} {$querymethod}";
				$group_fields_union = str_ireplace(array('fm_district.descr as district', 'fm_tts_views.id as view'), array('fm_district.descr', 'fm_tts_views.id'), $return_fields_union);
				$sub_select = "(SELECT {$return_fields_union},{$union_budget} {$filtermethod} {$querymethod} GROUP BY {$group_fields_union} {$ordermethod} {$limit_and_offset})"
				. " UNION (SELECT {$return_fields_union},{$union_cost} {$filtermethod} {$querymethod} GROUP BY {$group_fields_union} {$ordermethod} {$limit_and_offset})";
				$main_sql = "SELECT {$return_fields} FROM ({$sub_select} ) as t GROUP BY " . implode(',', array_keys($_return_field_array)) . " {$ordermethod}";
			}
			else
			{
				$main_sql = $sql . " {$filtermethod} {$querymethod} GROUP BY {$group_fields}{$budget_group_field}{$actual_cost_group_field} {$ordermethod}";
				$cache_test = $main_sql;
			}

			$sql_cnt = "SELECT DISTINCT budget,actual_cost, fm_tts_tickets.id FROM fm_tts_tickets"
				. " {$this->left_join} fm_location1 ON fm_tts_tickets.loc1=fm_location1.loc1"
				. " {$this->left_join} fm_part_of_town ON fm_location1.part_of_town_id=fm_part_of_town.id"
				. " {$this->left_join} fm_district ON fm_district.id = fm_part_of_town.district_id"
				. " {$order_join}{$date_cost_join}{$date_budget_join}"
				. " LEFT OUTER JOIN fm_tts_views ON (fm_tts_tickets.id = fm_tts_views.id AND fm_tts_views.account_id='{$this->account}')";

			if (isset($custom_filter['joinmethod_datatype']) && $custom_filter['joinmethod_datatype'])
			{
				foreach ($custom_filter['joinmethod_datatype'] as $_joinmethod)
				{
					$sql_cnt .= $_joinmethod;
				}
			}

			$sql_cnt .= " {$filtermethod} {$querymethod}";

			$filter_closed = '';// not needed
			if ($union_select)
			{
				$sub_select = "(SELECT fm_tts_tickets.id,{$union_budget} {$filtermethod} {$querymethod} {$filter_closed} GROUP BY fm_tts_tickets.id)"
				. " UNION (SELECT fm_tts_tickets.id,{$union_cost} {$filtermethod} {$querymethod} {$filter_closed} GROUP BY fm_tts_tickets.id)";
				$sql2 = "SELECT count(*) as cnt, SUM(budget) AS sum_budget, SUM(actual_cost) AS sum_actual_cost FROM ({$sub_select}) as t";
			}
			else
			{
				$sql2 = "SELECT count(*) as cnt, SUM(budget) AS sum_budget, SUM(actual_cost) AS sum_actual_cost FROM ({$sql_cnt} {$filter_closed} GROUP BY fm_tts_tickets.id) as t";
			}

			$cache_info = phpgwapi_cache::session_get('property','tts_listing_metadata');

			if (!isset($cache_info['sql_hash']) || $cache_info['sql_hash'] != md5($cache_test))
			{
				$cache_info = array();
			}
//_debug_array($main_sql);
			if(!$cache_info)
			{


				$this->db->query($sql2, __LINE__, __FILE__);
				$this->db->next_record();
				$count = $this->db->f('cnt');
				$sum_budget = (float)$this->db->f('sum_budget');
				$sum_actual_cost = (float)$this->db->f('sum_actual_cost');

				$cache_info = array(
					'total_records'		=> $union_select ? ((int)$count / 2) : $count,
					'sum_budget'		=> $sum_budget,
					'sum_actual_cost'	=> $sum_actual_cost,
					'sum_difference'	=> $sum_budget - $sum_actual_cost,
					'sql_hash'			=> md5($cache_test)
				);

				phpgwapi_cache::session_set('property','tts_listing_metadata',$cache_info);
			}
			$this->total_records = (int)$cache_info['total_records'];
			$this->sum_budget = (int)$cache_info['sum_budget'];
			$this->sum_actual_cost = (int)$cache_info['sum_actual_cost'];
			$this->sum_difference = (float)$cache_info['sum_difference'];

			$tickets = array();
			if (!$dry_run)
			{
				if (!$allrows)
				{
					$this->db->limit_query($main_sql, $start, __LINE__, __FILE__, $results);
				}
				else
				{
					$_fetch_single = false;
					$this->db->query($main_sql, __LINE__, __FILE__, false, $_fetch_single);
					unset($_fetch_single);
				}

				$i = 0;
				while ($this->db->next_record())
				{
					$tickets[] = array
						(
						'id' => (int)$this->db->f('id'),
						'subject' => $this->db->f('subject', true),
						'loc1_name' => $this->db->f('loc1_name', true),
						'location_code' => $this->db->f('location_code'),
						'district' => $this->db->f('district', true),
						'user_id' => $this->db->f('user_id'),
						'address' => $this->db->f('address', true),
						'assignedto' => $this->db->f('assignedto'),
						'status' => $this->db->f('status'),
						'priority' => $this->db->f('priority'),
						'cat_id' => $this->db->f('cat_id'),
						'group_id' => $this->db->f('group_id'),
						'entry_date' => $this->db->f('entry_date'),
						'modified_date' => $this->db->f('modified_date'),
						'finnish_date' => $this->db->f('finnish_date'),
						'finnish_date2' => $this->db->f('finnish_date2'),
						'order_id' => $this->db->f('order_id'),
						'vendor_id' => $this->db->f('vendor_id'),
						'actual_cost' => $this->db->f('actual_cost'),
						'estimate' => $this->db->f('budget'),
						'new_ticket' => $this->db->f('view') ? false : true,
						'billable_hours' => $this->db->f('billable_hours'),
						'ecodimb' => $this->db->f('ecodimb'),
						'order_dim1' => $this->db->f('order_dim1'),
						'external_project_id' => $this->db->f('external_project_id'),
						'contract_id' => $this->db->f('contract_id'),
						'service_id' => $this->db->f('service_id'),
						'tax_code' => $this->db->f('tax_code'),
						'unspsc_code' => $this->db->f('unspsc_code'),
						'b_account_id' => $this->db->f('b_account_id'),
						'continuous' => $this->db->f('continuous'),
						'order_deadline' => $this->db->f('order_deadline'),
						'order_deadline2' => $this->db->f('order_deadline2'),
						'details' =>  $this->db->f('details', true),

					);

					foreach ($custom_cols as $custom_col)
					{
						if ($custom_value = $this->db->f($custom_col['column_name'], true))
						{
							$custom_value = $this->custom->get_translated_value(array('value' => $custom_value,
								'attrib_id' => $custom_col['id'], 'datatype' => $custom_col['datatype']), $location_id);
						}
						$tickets[$i][$custom_col['column_name']] = $custom_value;
					}
					$i ++;
				}
			}

			$this->db->set_fetch_single(false);

			return $tickets;
		}

		function get_origin_entity_type()
		{
			$sql = "SELECT entity_id, id as cat_id,name"
				. " FROM fm_entity_category "
				. " WHERE tracking=1 ORDER by entity_id,cat_id";

			$this->db->query($sql, __LINE__, __FILE__);

			$entity = array();
			$uicols = array();
			$i = 0;
			while ($this->db->next_record())
			{
				$entity[$i]['entity_id'] = $this->db->f('entity_id');
				$entity[$i]['cat_id'] = $this->db->f('cat_id');
				$entity[$i]['type'] = ".entity.{$this->db->f('entity_id')}.{$this->db->f('cat_id')}";

				$name = str_replace(' ', '_', $this->db->f('name', true));
				$entity[$i]['name'] = $name;
				$uicols[] = $name;
				$i++;
			}

			$entity[$i]['type'] = '.project';
			$entity[$i]['name'] = 'project';
			$uicols[] = 'project';
			$i++;

			$entity[$i]['type'] = '.project.workorder';
			$entity[$i]['name'] = 'workorder';
			$uicols[] = 'workorder';


			$this->uicols_related = $uicols;
			return $entity;
		}

		function read_single( $id, $values = array() )
		{
			$id = (int)$id;
			$sql = "SELECT * FROM fm_tts_tickets WHERE id = {$id}";

			$this->db->query($sql, __LINE__, __FILE__);

			if ($this->db->next_record())
			{
				$ticket['id'] = $id;
				$ticket['assignedto'] = $this->db->f('assignedto');
				$ticket['user_id'] = $this->db->f('user_id');
				$ticket['group_id'] = $this->db->f('group_id');
				$ticket['status'] = $this->db->f('status');
				$ticket['cat_id'] = $this->db->f('cat_id');
				$ticket['subject'] = $this->db->f('subject', true);
				$ticket['priority'] = $this->db->f('priority');
				$ticket['details'] = $this->db->f('details', true);
				$ticket['location_code'] = $this->db->f('location_code');
				$ticket['contact_phone'] = $this->db->f('contact_phone');
				$ticket['contact_email'] = $this->db->f('contact_email', true);
				$ticket['address'] = $this->db->f('address', true);
				$ticket['tenant_id'] = $this->db->f('tenant_id');
				$ticket['p_num'] = $this->db->f('p_num');
				$ticket['p_entity_id'] = $this->db->f('p_entity_id');
				$ticket['p_cat_id'] = $this->db->f('p_cat_id');
				$ticket['finnish_date'] = $this->db->f('finnish_date');
				$ticket['finnish_date2'] = $this->db->f('finnish_date2');
				$ticket['contact_id'] = $this->db->f('contact_id');
				$ticket['order_id'] = $this->db->f('order_id');
				$ticket['continuous'] = $this->db->f('continuous');
				$ticket['order_deadline'] = $this->db->f('order_deadline');
				$ticket['order_deadline2'] = $this->db->f('order_deadline2');
				$ticket['vendor_id'] = $this->db->f('vendor_id');
				$ticket['contract_id'] = $this->db->f('contract_id',true);
				$ticket['service_id'] = $this->db->f('service_id',true);
				$ticket['external_project_id'] = $this->db->f('external_project_id',true);
				$ticket['unspsc_code'] = $this->db->f('unspsc_code',true);
				$ticket['tax_code'] = $this->db->f('tax_code');
				$ticket['b_account_id'] = $this->db->f('b_account_id');
				$ticket['order_descr'] = $this->db->f('order_descr', true);
				$ticket['ecodimb'] = $this->db->f('ecodimb');
				$ticket['budget'] = $this->db->f('budget');
				$ticket['actual_cost'] = $this->db->f('actual_cost');
				$ticket['order_cat_id'] = $this->db->f('order_cat_id');
				$ticket['building_part'] = $this->db->f('building_part', true);
				$ticket['order_dim1'] = $this->db->f('order_dim1');
				$ticket['publish_note'] = $this->db->f('publish_note');
				$ticket['billable_hours'] = $this->db->f('billable_hours');
				$ticket['branch_id'] = $this->db->f('branch_id');
				$ticket['entry_date'] = $this->db->f('entry_date');
				$ticket['modified_date'] = $this->db->f('modified_date');
				$ticket['order_sent'] = $this->db->f('order_sent');
				$ticket['invoice_remark'] = $this->db->f('invoice_remark', true);
				$ticket['order_received'] = $this->db->f('order_received');
				$ticket['order_received_amount'] = $this->db->f('order_received_amount');
				$mail_recipients = trim($this->db->f('mail_recipients'), ',');
				$ticket['mail_recipients'] = $mail_recipients ? explode(',', $mail_recipients) : array();
				$file_attachments = trim($this->db->f('file_attachments'), ',');
				$ticket['file_attachments'] = $file_attachments ? explode(',', $file_attachments) : array();

				$user_id = (int)$this->db->f('user_id');

				$ticket['user_name'] = $GLOBALS['phpgw']->accounts->get($user_id)->__toString();
				if ($ticket['assignedto'] > 0)
				{
					$ticket['assignedto_name'] = $GLOBALS['phpgw']->accounts->get($ticket['assignedto'])->__toString();
				}

				if (isset($values['attributes']) && is_array($values['attributes']))
				{
					$ticket['attributes'] = $values['attributes'];
					foreach ($ticket['attributes'] as &$attr)
					{
						$attr['value'] = $this->db->f($attr['column_name']);
					}
				}
			}

			return $ticket;
		}

		function update_view( $id = '' )
		{
			// Have they viewed this ticket before ?
			$id = (int)$id;
			$this->db->query("SELECT count(*) as cnt FROM fm_tts_views where id={$id}"
				. " AND account_id='" . $GLOBALS['phpgw_info']['user']['account_id'] . "'", __LINE__, __FILE__);
			$this->db->next_record();

			if (!$this->db->f('cnt'))
			{
				$this->db->query("INSERT INTO fm_tts_views (id,account_id,time) values ({$id},'"
					. $GLOBALS['phpgw_info']['user']['account_id'] . "','" . time() . "')", __LINE__, __FILE__);
			}
		}

		function add( &$ticket, $values_attribute = array() )
		{
			$table = 'fm_tts_tickets';

			if (isset($ticket['location']) && is_array($ticket['location']))
			{
				foreach ($ticket['location'] as $input_name => $value)
				{
					if (isset($value) && $value)
					{
						$value_set[$input_name] = $value;
					}
				}
			}

			$data_attribute = $this->custom->prepare_for_db($table, $values_attribute);
			if (isset($data_attribute['value_set']))
			{
				foreach ($data_attribute['value_set'] as $input_name => $value)
				{
					if (isset($value) && $value)
					{
						$value_set[$input_name] = $value;
					}
				}
			}

			if (isset($ticket['extra']) && is_array($ticket['extra']))
			{
				foreach ($ticket['extra'] as $input_name => $value)
				{
					if (isset($value) && $value)
					{
						$value_set[$input_name] = $value;
					}
				}

				if ($ticket['extra']['p_num'] && $ticket['extra']['p_entity_id'] && $ticket['extra']['p_cat_id'])
				{
					$entity = CreateObject('property.soadmin_entity');
					$entity_category = $entity->read_single_category($ticket['extra']['p_entity_id'], $ticket['extra']['p_cat_id']);
				}
			}

			$_address = array();
			if (isset($ticket['street_name']) && $ticket['street_name'])
			{
				$_address[] = "{$ticket['street_name']} {$ticket['street_number']}";
			}

			if (isset($ticket['location_name']) && $ticket['location_name'])
			{
				$_address[] = $ticket['location_name'];
			}

			if (isset($ticket['additional_info']) && $ticket['additional_info'])
			{
				foreach ($ticket['additional_info'] as $key => $value)
				{
					if ($value)
					{
						$_address[] = "{$key}|{$value}";
					}
				}
			}

			if (isset($entity_category) && $entity_category)
			{
				$_address[] = "{$entity_category['name']}::{$ticket['extra']['p_num']}";
			}

			$address = $this->db->db_addslashes(implode('::', $_address));

			unset($_address);

			$value_set['priority'] = isset($ticket['priority']) ? $ticket['priority'] : 0;
			$value_set['user_id'] = $GLOBALS['phpgw_info']['user']['account_id'];
			$value_set['assignedto'] = $ticket['assignedto'];
			$value_set['group_id'] = $ticket['group_id'];
			$value_set['subject'] = $this->db->db_addslashes($ticket['subject']);
			$value_set['cat_id'] = $ticket['cat_id'];
			$value_set['status'] = $ticket['status'];
			$value_set['details'] = $this->db->db_addslashes($ticket['details']);
			$value_set['location_code'] = $ticket['location_code'];
			$value_set['address'] = $address;
			$value_set['entry_date'] = time();
			$value_set['modified_date'] = time();
			$value_set['finnish_date'] = $ticket['finnish_date'];
			$value_set['contact_id'] = $ticket['contact_id'];
			$value_set['publish_note'] = 1;
			$value_set['external_ticket_id'] = !empty($ticket['external_ticket_id']) ? $ticket['external_ticket_id'] : null;

			$cols = implode(',', array_keys($value_set));
			$values = $this->db->validate_insert(array_values($value_set));

			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$this->db->query("INSERT INTO {$table} ({$cols}) VALUES ({$values})", __LINE__, __FILE__);

			$id = $this->db->get_last_insert_id($table, 'id');
			if (isset($ticket['extra']['contact_phone']) && $ticket['extra']['contact_phone'] && isset($ticket['extra']['tenant_id']) && $ticket['extra']['tenant_id'])
			{
				$this->db->query("UPDATE fm_tenant SET contact_phone='{$ticket['extra']['contact_phone']}' WHERE id='{$ticket['extra']['tenant_id']}'", __LINE__, __FILE__);
			}

			$interlink = CreateObject('property.interlink');

			if (isset($ticket['origin']) && $ticket['origin'] && isset($ticket['origin_id']) && $ticket['origin_id'] )
			{
				$ticket['origin_location_id'] = $GLOBALS['phpgw']->locations->get_id('property', $ticket['origin']);
				$ticket['origin_item_id'] = $ticket['origin_id'];

				$interlink_data = array
					(
					'location1_id' => $ticket['origin_location_id'],
					'location1_item_id' => $ticket['origin_item_id'],
					'location2_id' => $GLOBALS['phpgw']->locations->get_id('property', '.ticket'),
					'location2_item_id' => $id,
					'account_id' => $this->account
				);

				$interlink->add($interlink_data, $this->db);

				if($ticket['origin']==".project.request")
				{
					$config = CreateObject('phpgwapi.config', 'property');
					$config->read();
					$request_ticket_hookup_status = isset($config->config_data['request_ticket_hookup_status']) && $config->config_data['request_ticket_hookup_status'] ? $config->config_data['request_ticket_hookup_status'] : false;

					if ($request_ticket_hookup_status)
					{
						$this->db->query("UPDATE fm_request SET status='{$request_ticket_hookup_status}' WHERE id='" . (int) $ticket['origin_item_id'] . "'", __LINE__, __FILE__);
					}

					phpgwapi_cache::message_set(lang('request %1 has been edited', $ticket['origin_item_id']), 'message');
				}
			}

			if (isset($ticket['extra']) && is_array($ticket['extra']) && isset($ticket['extra']['p_num']) && $ticket['extra']['p_num'])
			{
				$ticket['origin_location_id'] = $GLOBALS['phpgw']->locations->get_id('property', ".entity.{$ticket['extra']['p_entity_id']}.{$ticket['extra']['p_cat_id']}");

				$this->db->query('SELECT prefix FROM fm_entity_category WHERE entity_id = ' . (int)$ticket['extra']['p_entity_id'] . ' AND id = ' . (int)$ticket['extra']['p_cat_id']);
				$this->db->next_record();
				$prefix = $this->db->f('prefix');
				$ticket['origin_item_id'] = (int)ltrim($ticket['extra']['p_num'], $prefix);

				$interlink_data = array
					(
					'location1_id' => $ticket['origin_location_id'],
					'location1_item_id' => $ticket['origin_item_id'],
					'location2_id' => $GLOBALS['phpgw']->locations->get_id('property', '.ticket'),
					'location2_item_id' => $id,
					'account_id' => $this->account
				);
				$interlink->add($interlink_data, $this->db);
			}

			$add_history = true;
			if (!$this->global_lock)
			{
				$add_history = $this->db->transaction_commit();
			}

			if ($add_history)
			{
				$this->historylog->add('O', $id, time(), '');
				if ($ticket['finnish_date'])
				{
					$this->historylog->add('IF', $id, $ticket['finnish_date'], '');
				}
			}

			$receipt['message'][] = array('msg' => lang('Ticket %1 has been saved', $id));
			$receipt['id'] = $id;
			$ticket['id'] = $id; //returned by reference
			return $receipt;
		}

		/**
		 * Get a list of user(admin)-configured status
		 *
		 * @return array with list of custom status
		 */
		public function get_custom_status()
		{
			$sql = "SELECT * FROM fm_tts_status ORDER BY sorting ASC";
			$this->db->query($sql, __LINE__, __FILE__);

			$status = array();
			while ($this->db->next_record())
			{
				$status[] = array
					(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('name', true),
					'color' => $this->db->f('color'),
					'closed' => $this->db->f('closed')
				);
			}
			return $status;
		}

		function update_status( $ticket, $id = 0 )
		{
			$id = (int)$id;
			// DB Content is fresher than http posted value.
			$this->db->query("select * from fm_tts_tickets where id='$id'", __LINE__, __FILE__);
			$this->db->next_record();
			$old_status = $this->db->f('status');

			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}


			/*
			 * * phpgw_fm_tts_append.append_type - Defs
			 * * R - Reopen ticket
			 * * X - Ticket closed
			 * * O - Ticket opened
			 * * C - Comment appended
			 * * A - Ticket assignment
			 * * G - Ticket group assignment
			 * * P - Priority change
			 * * T - Category change
			 * * S - Subject change
			 * * B - Budget
			 * * AC - actual cost changed
			 * * H - Billing hours
			 * * F - finnish date
			 * * C% - Status changed
			 * * L - Location changed
			 * * M - Mail sent to vendor
			 */

			$this->fields_updated = false;

			if ($old_status != $ticket['status'])
			{
				$check_old_custom = (int)trim($old_status, 'C');
				$this->db->query("SELECT * from fm_tts_status WHERE id = {$check_old_custom}", __LINE__, __FILE__);
				$this->db->next_record();
				$old_closed = $this->db->f('closed');
				$this->fields_updated = true;
				if ($old_status == 'X' || $old_closed)
				{
					$new_status = $ticket['status'];
					$this->historylog->add('R', $id, $ticket['status'], $old_status);

					$this->db->query("UPDATE fm_tts_tickets SET status='{$new_status}' WHERE id= {$id}", __LINE__, __FILE__);
				}
				else
				{
					$this->historylog->add($ticket['status'], $id, $ticket['status'], $old_status);
					$this->db->query("UPDATE fm_tts_tickets SET status='{$ticket['status']}' WHERE id={$id}", __LINE__, __FILE__);
				}
				$this->check_pending_action($ticket, $id);

				//Close cases
				$check_new_custom = (int)trim($ticket['status'], 'C');
				$this->db->query("SELECT closed from fm_tts_status WHERE id = {$check_new_custom}", __LINE__, __FILE__);
				$this->db->next_record();

				if (($this->db->f('closed') || $ticket['status'] == 'X') && ($old_status != 'X' && !$old_closed))
				{
					$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');
					// at controller
					if (isset($GLOBALS['phpgw_info']['user']['apps']['controller']))
					{
						$controller = CreateObject('controller.uicase');
						$controller->updateStatusForCases($location_id, $id, 1);
					}
					// at request
					execMethod('property.sorequest.update_status_from_related', array(
						'location_id' => $location_id,
						'id' => $id,
						'status' => 'closed')
					);
				}
			}

			if ($this->fields_updated)
			{
				$this->db->query('UPDATE fm_tts_tickets SET modified_date= ' . time() . " WHERE id={$id}", __LINE__, __FILE__);
			}

			if (!$this->global_lock)
			{
				$this->db->transaction_commit();
			}

			return $this->fields_updated;
		}

		function update_priority( $ticket, $id = 0 )
		{
			$id = (int)$id;
			$receipt = array();
			$this->fields_updated = false;

			$this->db->query("SELECT priority FROM fm_tts_tickets WHERE id={$id}", __LINE__, __FILE__);
			$this->db->next_record();
			$oldpriority = $this->db->f('priority');

			$this->db->transaction_begin();

			if ($oldpriority != $ticket['priority'])
			{
				$this->fields_updated = true;
				$this->db->query("UPDATE fm_tts_tickets set priority='" . $ticket['priority']
					. "' WHERE id={$id}", __LINE__, __FILE__);
				$this->historylog->add('P', $id, $ticket['priority'], $oldpriority);
			}

			if ($this->fields_updated)
			{
				$this->db->query('UPDATE fm_tts_tickets SET modified_date= ' . time() . " WHERE id={$id}", __LINE__, __FILE__);
				$receipt['message'][] = array('msg' => lang('Ticket %1 has been updated', $id));
			}

			$this->db->transaction_commit();

			return $receipt;
		}

		function update_ticket( &$ticket, $id = 0, $receipt = array(), $values_attribute = array(), $simple = false )
		{
			$this->fields_updated = array();
			$id = (int)$id;
			$ticket['id'] = $id;
			// DB Content is fresher than http posted value.
			$this->db->query("SELECT * FROM fm_tts_tickets WHERE id='$id'", __LINE__, __FILE__);
			$this->db->next_record();

			$location_code = $this->db->f('location_code');
			$oldlocation_code = $this->db->f('location_code');
			$oldfinnish_date = $this->db->f('finnish_date');
			$oldfinnish_date2 = $this->db->f('finnish_date2');
			$oldassigned = $this->db->f('assignedto');
			$oldgroup_id = $this->db->f('group_id');
			$oldpriority = $this->db->f('priority');
			$oldcat_id = $this->db->f('cat_id');
			$old_status = $this->db->f('status');
			$ticket['old_status'] = $old_status; // used for custom functions
			//	$old_budget  			= $this->db->f('budget');
			$old_billable_hours = (float)$this->db->f('billable_hours');
			//	$old_billable_rate	= $this->db->f('billable_rate');
			$old_subject = $this->db->f('subject');
			$old_contact_id = $this->db->f('contact_id');
			$old_order_cat_id = $this->db->f('order_cat_id');
			$old_building_part = $this->db->f('building_part', true);
			$old_order_dim1 = (int)$this->db->f('order_dim1');
			$order_sent = $this->db->f('order_sent');

			if ($oldcat_id == 0)
			{
				$oldcat_id = '';
			}
			if ($old_order_cat_id == 0)
			{
				$old_order_cat_id = '';
			}
			if ($oldassigned == 0)
			{
				$oldassigned = '';
			}
			if ($oldgroup_id == 0)
			{
				$oldgroup_id = '';
			}

			// Figure out and last note

			$history_values = $this->historylog->return_array(array(), array('C'), 'history_timestamp', 'DESC', $id);
			$old_note = $history_values[0]['new_value'];

			if (!$old_note)
			{
				$old_note = $this->db->f('details');
			}

			$this->db->transaction_begin();

			/*
			 * * phpgw_fm_tts_append.append_type - Defs
			 * * R - Reopen ticket
			 * * X - Ticket closed
			 * * O - Ticket opened
			 * * C - Comment appended
			 * * A - Ticket assignment
			 * * G - Ticket group assignment
			 * * P - Priority change
			 * * T - Category change
			 * * S - Subject change
			 * * B - Budget change
			 * *	AC - actual cost changed
			 * * H - Billing hours
			 * * F - finnish date
			 * * C% - Status change
			 * * L - Location changed
			 * * M - Mail sent to vendor
			 */

			if (!$simple)
			{
				$this->db->query("UPDATE fm_tts_tickets SET publish_note = NULL WHERE id = {$id}", __LINE__, __FILE__);
				$this->db->query("UPDATE fm_tts_history SET publish = NULL WHERE history_record_id = {$id}", __LINE__, __FILE__);
				if (isset($ticket['publish_note']))
				{
					foreach ($ticket['publish_note'] as $publish_info)
					{
						$note = explode('_', $publish_info);
						if (!$note[1])
						{
							$this->db->query("UPDATE fm_tts_tickets SET publish_note = 1 WHERE id = {$note[0]}", __LINE__, __FILE__);
						}
						else
						{
							$this->db->query("UPDATE fm_tts_history SET publish = 1 WHERE history_id = {$note[1]}", __LINE__, __FILE__);
						}
					}
				}
			}

			if (($old_note != $ticket['note']) && $ticket['note'])
			{
				$this->fields_updated[] = 'note';
				$this->historylog->add('C', $id, $ticket['note'], $old_note);
				$_history_id = $this->db->get_last_insert_id('fm_tts_history', 'history_id');
				$this->db->query("UPDATE fm_tts_history SET publish = 1 WHERE history_id = $_history_id", __LINE__, __FILE__);
				unset($_history_id);
			}

			if (isset($this->fields_updated) && $this->fields_updated && $simple)
			{
				$receipt['message'][] = array('msg' => lang('Ticket1 has been updated'));
				$this->db->transaction_commit();
				return $receipt;
			}

			$finnish_date = (isset($ticket['finnish_date']) ? phpgwapi_datetime::date_to_timestamp($ticket['finnish_date']) : '');

			if ($oldfinnish_date && isset($ticket['finnish_date']) && $ticket['finnish_date'])
			{
				$this->db->query("update fm_tts_tickets set finnish_date2='" . $finnish_date
					. "' where id='$id'", __LINE__, __FILE__);
			}
			else if (!$oldfinnish_date && isset($ticket['finnish_date']) && $ticket['finnish_date'])
			{
				$this->db->query("update fm_tts_tickets set finnish_date='" . $finnish_date
					. "' where id='$id'", __LINE__, __FILE__);
			}

			if ($oldfinnish_date2 > 0)
			{
				$oldfinnish_date = $oldfinnish_date2;
			}
			if (isset($ticket['finnish_date']) && $ticket['finnish_date'])
			{
				if ($oldfinnish_date != $finnish_date)
				{
					$this->fields_updated[] = 'finnish_date';
					$this->historylog->add('F', $id, $finnish_date, $oldfinnish_date);
				}
			}

			if (isset($ticket['status']) && ($old_status != $ticket['status']))
			{
				$check_old_custom = (int)trim($old_status, 'C');
				$this->db->query("SELECT * from fm_tts_status WHERE id = {$check_old_custom}", __LINE__, __FILE__);
				$this->db->next_record();
				$old_closed = $this->db->f('closed');
				$this->fields_updated[] = 'status';
				if ($old_status == 'X' || $old_closed)
				{
					$new_status = $ticket['status'];
					$this->historylog->add('R', $id, $ticket['status'], $old_status);

					$this->db->query("UPDATE fm_tts_tickets SET status='{$new_status}' WHERE id= {$id}", __LINE__, __FILE__);
				}
				else
				{
					$this->historylog->add($ticket['status'], $id, $ticket['status'], $old_status);
					$this->db->query("UPDATE fm_tts_tickets SET status='{$ticket['status']}' WHERE id={$id}", __LINE__, __FILE__);
				}
				$this->check_pending_action($ticket, $id);

				//Close cases at related
				$check_new_custom = (int)trim($ticket['status'], 'C');
				$this->db->query("SELECT closed from fm_tts_status WHERE id = {$check_new_custom}", __LINE__, __FILE__);
				$this->db->next_record();

				if (($this->db->f('closed') || $ticket['status'] == 'X') && ($old_status != 'X' && !$old_closed))
				{
					$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');
					// at controller
					if (isset($GLOBALS['phpgw_info']['user']['apps']['controller']))
					{
						$controller = CreateObject('controller.uicase');
						$controller->updateStatusForCases($location_id, $id, 1);
					}
					// at request
					execMethod('property.sorequest.update_status_from_related', array(
						'location_id' => $location_id,
						'id' => $id,
						'status' => 'closed')
					);
				}
			}

			if ($ticket['assignedto'] && ( ($oldassigned != $ticket['assignedto']) && $ticket['assignedto'] != 'ignore'))
			{
				$this->fields_updated[] = 'assignedto';

				$value_set = array('assignedto' => $ticket['assignedto']);
				$value_set = $this->db->validate_update($value_set);

				$this->db->query("update fm_tts_tickets set $value_set where id='$id'", __LINE__, __FILE__);
				$this->historylog->add('A', $id, $ticket['assignedto'], $oldassigned);
			}

			if($oldgroup_id || $ticket['group_id'])
			{
				if($oldassigned && ! $ticket['assignedto'])
				{
					$this->fields_updated[] = 'assignedto';
					$this->db->query("UPDATE fm_tts_tickets SET assignedto = NULL WHERE id={$id}", __LINE__, __FILE__);
					$this->historylog->add('A', $id, '', $oldassigned);					
				}
			}

			if (($oldgroup_id != $ticket['group_id']) && $ticket['group_id'] != 'ignore')
			{
				$this->fields_updated[] = 'group_id';

				$value_set = array('group_id' => $ticket['group_id']);
				$value_set = $this->db->validate_update($value_set);

				$this->db->query("update fm_tts_tickets set $value_set where id='$id'", __LINE__, __FILE__);
				$this->historylog->add('G', $id, $ticket['group_id'], $oldgroup_id);
			}

			if ($oldpriority != $ticket['priority'])
			{
				$this->fields_updated[] = 'priority';
				$this->db->query("update fm_tts_tickets set priority='" . $ticket['priority']
					. "' where id='$id'", __LINE__, __FILE__);
				$this->historylog->add('P', $id, $ticket['priority'], $oldpriority);
			}

			if ($old_contact_id != $ticket['contact_id'])
			{
				$contact_id = (int)$ticket['contact_id'];
				$this->fields_updated[] = 'contact_id';
				$this->db->query("update fm_tts_tickets set contact_id={$contact_id} WHERE id=$id", __LINE__, __FILE__);
			}

			if ($ticket['cat_id'] && ( ($oldcat_id != $ticket['cat_id']) && $ticket['cat_id'] != 'ignore'))
			{
				$this->fields_updated[] = 'cat_id';
				$this->db->query("update fm_tts_tickets set cat_id='" . $ticket['cat_id']
					. "' where id='$id'", __LINE__, __FILE__);
				$this->historylog->add('T', $id, $ticket['cat_id'], $oldcat_id);
			}

			/*
			  if ($old_billable_rate != $ticket['billable_rate'])
			  {
			  $this->fields_updated[] = 'billable_rate';
			  $this->db->query("update fm_tts_tickets set billable_rate='" . $ticket['billable_rate']
			  . "' where id='$id'",__LINE__,__FILE__);
			  $this->historylog->add('B',$id,$ticket['billable_rate'],$old_billable_rate);
			  }
			 */
			if ($old_subject != $ticket['subject'])
			{
				$this->db->query("UPDATE fm_tts_tickets SET subject='" . $ticket['subject']
					. "' where id='$id'", __LINE__, __FILE__);
				$this->historylog->add('S', $id, $ticket['subject'], $old_subject);
				$receipt['message'][] = array('msg' => lang('Subject has been updated'));
			}

			if ($ticket['billable_hours'])
			{
				$ticket['billable_hours'] = (float)str_replace(',', '.', $ticket['billable_hours']);
				$ticket['billable_hours'] += (float)$old_billable_hours;
//			}
//			if ((float)$old_billable_hours != $ticket['billable_hours'])
//			{
				$this->db->query("UPDATE fm_tts_tickets SET billable_hours='{$ticket['billable_hours']}'"
					. " WHERE id='{$id}'", __LINE__, __FILE__);
				$this->historylog->add('H', $id, $ticket['billable_hours'], $old_billable_hours);
				$receipt['message'][] = array('msg' => lang('billable hours has been updated'));
			}

			if (isset($ticket['location']) && $ticket['location'])
			{
				$ticket['location_code'] = implode('-', $ticket['location']);
			}

			if (isset($ticket['location_code']) && $ticket['location_code'] && ($oldlocation_code != $ticket['location_code']))
			{
				$interlink = CreateObject('property.interlink');
				if ($interlink->get_relation('property', '.ticket', $id, 'origin') || $interlink->get_relation('property', '.ticket', $id, 'target'))
				{
					$receipt['message'][] = array('msg' => lang('location could not be changed'));
				}
				else
				{
					$value_set = array();

					$_address = array();
					if (isset($ticket['street_name']) && $ticket['street_name'])
					{
						$_address[] = "{$ticket['street_name']} {$ticket['street_number']}";
					}

					if (isset($ticket['location_name']) && $ticket['location_name'])
					{
						$_address[] = $ticket['location_name'];
					}

					if (isset($ticket['additional_info']) && $ticket['additional_info'])
					{
						foreach ($ticket['additional_info'] as $key => $value)
						{
							if ($value)
							{
								$_address[] = "{$key}|{$value}";
							}
						}
					}


					if (isset($ticket['extra']['p_num']) && $ticket['extra']['p_num'] && $ticket['extra']['p_entity_id'] && $ticket['extra']['p_cat_id'])
					{
						$entity = CreateObject('property.soadmin_entity');
						$entity_category = $entity->read_single_category($ticket['extra']['p_entity_id'], $ticket['extra']['p_cat_id']);
					}

					if (isset($entity_category) && $entity_category)
					{
						$_address[] = "{$entity_category['name']}::{$ticket['extra']['p_num']}";
					}

					$address = $this->db->db_addslashes(implode('::', $_address));

					unset($_address);

					$value_set['address'] = $address;

					if (isset($ticket['location_code']) && $ticket['location_code'])
					{
						$value_set['location_code'] = $ticket['location_code'];
					}

					$admin_location = CreateObject('property.soadmin_location');
					$admin_location->read(false);

					// Delete old values for location - in case of moving up in the hierarchy
					$metadata = $this->db->metadata('fm_tts_tickets');
					for ($i = 1; $i < $admin_location->total_records + 1; $i++)
					{
						if (isset($metadata["loc{$i}"]))
						{
							$value_set["loc{$i}"] = false;
						}
					}

					if (isset($ticket['location']) && is_array($ticket['location']))
					{
						foreach ($ticket['location'] as $column => $value)
						{
							$value_set[$column] = $value;
						}
					}


					$value_set = $this->db->validate_update($value_set);

					$this->db->query("UPDATE fm_tts_tickets SET $value_set WHERE id={$id}", __LINE__, __FILE__);

					$this->historylog->add('L', $id, $ticket['location_code'], $oldlocation_code);
					$receipt['message'][] = array('msg' => lang('Location has been updated'));
				}
				unset($interlink);
			}


			if (isset($ticket['make_order']) && $ticket['make_order'])
			{
				$order_id = execMethod('property.socommon.increment_id', 'order');
				if ($order_id)
				{
					$this->db->query("UPDATE fm_tts_tickets SET order_id = {$order_id}, ordered_by = {$this->account} WHERE id={$id}", __LINE__, __FILE__);
					$this->db->query("INSERT INTO fm_orders (id,type) VALUES ({$order_id},'ticket')");

					$ticket['invoice_remark'] = $ticket['subject'];
				}
			}

			$value_set = array();

			$data_attribute = $this->custom->prepare_for_db('fm_tts_tickets', $values_attribute);

			if (isset($data_attribute['value_set']))
			{
				foreach ($data_attribute['value_set'] as $input_name => $value)
				{
					$value_set[$input_name] = $value;
				}
			}

			$value_set['modified_date'] = time();

			// check order-rights

			$order_add = $GLOBALS['phpgw']->acl->check('.ticket.order', PHPGW_ACL_ADD, 'property');
			$order_edit = $GLOBALS['phpgw']->acl->check('.ticket.order', PHPGW_ACL_EDIT, 'property');

			if ($order_add || $order_edit)
			{
				if ((int)$ticket['budget'])
				{

					$this->db->query("SELECT sum(amount) AS budget FROM fm_tts_budget WHERE ticket_id = {$id}", __LINE__, __FILE__);
					$this->db->next_record();
					$old_budget = $this->db->f('budget');
					$new_budget = str_replace(',', '.', ($old_budget + $ticket['budget']));

					$this->db->query("UPDATE fm_tts_tickets SET budget='{$new_budget}' WHERE id='$id'", __LINE__, __FILE__);

					$value_set_cost = array
						(
						'ticket_id' => $id,
						'amount' => str_replace(',', '.', $ticket['budget']),
						'period' => $ticket['budget_period'] . '01',
						'created_on' => time(),
						'created_by' => $this->account
					);

					$cols_cost = implode(',', array_keys($value_set_cost));
					$values_cost = $this->db->validate_insert(array_values($value_set_cost));
					$this->db->query("INSERT INTO fm_tts_budget ({$cols_cost}) VALUES ({$values_cost})");

					$this->historylog->add('B', $id, (float)$new_budget, $old_budget);
					$receipt['message'][] = array('msg' => lang('budget changed'));
				}
				if ((int)$ticket['actual_cost'])
				{

					$this->db->query("SELECT sum(amount) AS actual_cost FROM fm_tts_payments WHERE ticket_id = {$id}", __LINE__, __FILE__);
					$this->db->next_record();
					$old_actual_cost = $this->db->f('actual_cost');
					$new_actual_cost = str_replace(',', '.', ($old_actual_cost + $ticket['actual_cost']));

					$this->db->query("UPDATE fm_tts_tickets SET actual_cost='{$new_actual_cost}' WHERE id='$id'", __LINE__, __FILE__);

					$value_set_cost = array
						(
						'ticket_id' => $id,
						'amount' => str_replace(',', '.', $ticket['actual_cost']),
						'period' => $ticket['actual_cost_period'],
						'created_on' => time(),
						'created_by' => $this->account
					);

					$cols_cost = implode(',', array_keys($value_set_cost));
					$values_cost = $this->db->validate_insert(array_values($value_set_cost));
					$this->db->query("INSERT INTO fm_tts_payments ({$cols_cost}) VALUES ({$values_cost})");

					$this->historylog->add('AC', $id, (float)$new_actual_cost, $old_actual_cost);
					$receipt['message'][] = array('msg' => lang('actual cost has been updated'));
				}

				if ((int)$old_order_cat_id != (int)$ticket['order_cat_id'])
				{
					$this->db->query("UPDATE fm_tts_tickets SET order_cat_id='" . (int)$ticket['order_cat_id']
						. "' WHERE id='$id'", __LINE__, __FILE__);
					$receipt['message'][] = array('msg' => lang('order category has been updated'));
					$this->fields_updated[] = 'order_cat_id';
				}

				if ((int)$old_order_dim1 != (int)$ticket['order_dim1'])
				{
					$this->db->query("UPDATE fm_tts_tickets SET order_dim1='" . (int)$ticket['order_dim1']
						. "' WHERE id='$id'", __LINE__, __FILE__);
					$receipt['message'][] = array('msg' => lang('order_dim1 has been updated'));
					$this->fields_updated[] = 'order_dim1';
				}

				if ($old_building_part != $ticket['building_part'])
				{
					$this->db->query("UPDATE fm_tts_tickets SET building_part='" . $ticket['building_part']
						. "' WHERE id='$id'", __LINE__, __FILE__);
					$receipt['message'][] = array('msg' => lang('building part has been updated'));
					$this->fields_updated[] = 'building_part';
				}
				/*
				  if($old_budget != $ticket['budget'])
				  {
				  $this->fields_updated[] = 'budget';
				  $this->db->query("UPDATE fm_tts_tickets set budget='" . (int)$ticket['budget']
				  . "' where id='$id'", __LINE__, __FILE__);
				  $this->historylog->add('B', $id, $ticket['budget'], $old_budget);
				  }
				 */
				$value_set['vendor_id'] = $ticket['vendor_id'];
				$value_set['contract_id'] = $this->db->db_addslashes($ticket['contract_id']);
				$value_set['service_id'] = $ticket['service_id'];
				$value_set['external_project_id'] = $this->db->db_addslashes($ticket['external_project_id']);
				$value_set['unspsc_code'] = $ticket['unspsc_code'];
				$value_set['b_account_id'] = $ticket['b_account_id'];
				$value_set['order_descr'] = $this->db->db_addslashes($ticket['order_descr']);
				$value_set['ecodimb'] = $ticket['ecodimb'];
				$value_set['branch_id'] = $ticket['branch_id'];
				$value_set['tax_code'] = $ticket['tax_code'];
				$value_set['continuous'] = $ticket['continuous'];
				$value_set['order_deadline'] = $ticket['order_deadline'];// delete value if empty
				$value_set['order_deadline2'] = $ticket['order_deadline2'];// delete value if empty

				if(!empty($ticket['invoice_remark']) && !$order_sent)
				{
					$value_set['invoice_remark'] = $this->db->db_addslashes($ticket['invoice_remark']);
				}

				if(!empty($ticket['order_deadline']))
				{
					$value_set['order_deadline'] = phpgwapi_datetime::date_to_timestamp($ticket['order_deadline']);
				}
				if(!empty($ticket['order_deadline2']))
				{
					$value_set['order_deadline2'] = phpgwapi_datetime::date_to_timestamp($ticket['order_deadline2']);
				}

				if(isset($ticket['vendor_email']) && is_array($ticket['vendor_email']))
				{
					$vendor_email = array();
					$validator = CreateObject('phpgwapi.EmailAddressValidator');
					foreach ($ticket['vendor_email'] as $_temp)
					{
						if ($_temp)
						{
							if ($validator->check_email_address($_temp))
							{
								$vendor_email[] = $_temp;
							}
							else
							{
								$receipt['error'][] = array('msg' => lang('%1 is not a valid address', $_temp));
							}
						}
					}
					$value_set['mail_recipients'] = implode(',', $vendor_email);
					unset($_temp);
				}

				if(isset($ticket['file_attach']) && is_array($ticket['file_attach']))
				{
					$file_attachments = array();
					foreach ($ticket['file_attach'] as $_temp)
					{
						$file_attachments[] = (int)$_temp;
					}
					$value_set['file_attachments'] = implode(',', $file_attachments);
				}
			}

			$value_set = $this->db->validate_update($value_set);
			$this->db->query("UPDATE fm_tts_tickets SET $value_set WHERE id={$id}", __LINE__, __FILE__);

			$value_set = array();

			if (isset($ticket['extra']) && is_array($ticket['extra']) && $ticket['extra'])
			{
				foreach ($ticket['extra'] as $column => $value)
				{
					$value_set[$column] = $value;
				}

				$value_set = $this->db->validate_update($value_set);

				$this->db->query("UPDATE fm_tts_tickets SET $value_set WHERE id={$id}", __LINE__, __FILE__);
			}

			$this->db->transaction_commit();

			if (isset($this->fields_updated) && $this->fields_updated)
			{
				$receipt['message'][] = array('msg' => lang('Ticket has been updated'));
			}
			return $receipt;
		}

		function check_pending_action( $ticket, $id )
		{
			$status = (int)trim($ticket['status'], 'C');
			$this->db->query("SELECT * FROM fm_tts_status WHERE id = '{$status}'");

			$this->db->next_record();

			if ($this->db->f('approved'))
			{
				$sosubstitute = CreateObject('property.sosubstitute');
				$users_for_substitute = $sosubstitute->get_users_for_substitute($this->account);
				$take_responsibility_for = array($this->account);

				$action_params = array
					(
					'appname' => 'property',
					'location' => '.ticket',
					'id' => $id,
					'responsible_type' => 'user',
					'action' => 'approval',
					'remark' => '',
					'deadline' => ''
				);

				$approvals = execMethod('property.sopending_action.get_pending_action', $action_params);

				foreach ($approvals as $approval)
				{
					if(in_array($approval['responsible'],$users_for_substitute))
					{
						$take_responsibility_for[] = $approval['responsible'];
					}
				}

				foreach ($take_responsibility_for as $__account_id)
				{
					$action_params['responsible'] = $__account_id;
					execMethod('property.sopending_action.close_pending_action', $action_params);
				}

				unset($action_params);

			}
			/*
			 * Sigurd: Consider remove
			 */
			/*
			  if ($this->db->f('in_progress') )
			  {
			  $action_params = array
			  (
			  'appname'			=> 'property',
			  'location'			=> '.ticket',
			  'id'				=> $id,
			  'responsible'		=> $ticket['vendor_id'],
			  'responsible_type'  => 'vendor',
			  'action'			=> 'remind',
			  'remark'			=> '',
			  'deadline'			=> ''
			  );

			  execMethod('property.sopending_action.close_pending_action', $action_params);
			  unset($action_params);
			  }
			 */

			if ($this->db->f('delivered'))
			{
				//close
			}
		}

		function delete( $id )
		{
			$id = (int)$id;

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');

			if (!$location_id)
			{
				throw new Exception("phpgwapi_locations::get_id ('property', '.ticket') returned 0");
			}

			$this->db->transaction_begin();

			$this->db->query("DELETE FROM fm_action_pending WHERE location_id = {$location_id} AND item_id = {$id}", __LINE__, __FILE__);
			$this->db->query("DELETE FROM phpgw_interlink WHERE location1_id = {$location_id} AND location1_item_id = {$id}", __LINE__, __FILE__);
			$this->db->query("DELETE FROM phpgw_interlink WHERE location2_id = {$location_id} AND location2_item_id = {$id}", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_tts_history WHERE history_record_id = {$id}", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_tts_payments WHERE ticket_id = {$id}", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_tts_budget WHERE ticket_id = {$id}", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_tts_views WHERE id = {$id}", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_tts_tickets WHERE id = {$id}", __LINE__, __FILE__);

			if ($this->db->transaction_commit())
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public function get_vendors()
		{
			$values = array();
			$sql = "SELECT DISTINCT fm_vendor.id, fm_vendor.org_name FROM fm_tts_tickets {$this->join} fm_vendor ON fm_tts_tickets.vendor_id = fm_vendor.id ORDER BY org_name ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('org_name', true)
				);
			}

			return $values;
		}

		public function get_ecodimb()
		{
			$values = array();
			$sql = "SELECT DISTINCT fm_ecodimb.id, fm_ecodimb.descr as name FROM fm_tts_tickets {$this->join} fm_ecodimb ON fm_tts_tickets.ecodimb = fm_ecodimb.id ORDER BY name ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('name', true)
				);
			}

			return $values;
		}

		public function get_b_account()
		{
			$values = array();
			$sql = "SELECT DISTINCT fm_b_account.id, fm_b_account.descr as name FROM fm_tts_tickets {$this->join} fm_b_account ON fm_tts_tickets.b_account_id = fm_b_account.id ORDER BY id ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('name', true)
				);
			}

			return $values;
		}

		public function get_building_part()
		{
			$values = array();
			$sql = "SELECT DISTINCT fm_building_part.id, fm_building_part.descr as name FROM fm_tts_tickets {$this->join} fm_building_part ON fm_tts_tickets.building_part = fm_building_part.id ORDER BY id ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$id = $this->db->f('id');
				$values[] = array
					(
					'id' => $id,
					'name' => $id . ' ' . $this->db->f('name', true)
				);
			}

			return $values;
		}

		public function get_branch()
		{
			$values = array();
			$sql = "SELECT DISTINCT fm_branch.id, fm_branch.descr as name FROM fm_tts_tickets {$this->join} fm_branch ON fm_tts_tickets.branch_id = fm_branch.id ORDER BY name ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('name', true)
				);
			}

			return $values;
		}

		public function get_order_dim1()
		{
			$values = array();
			$sql = "SELECT DISTINCT fm_order_dim1.id, fm_order_dim1.descr as name FROM fm_tts_tickets {$this->join} fm_order_dim1 ON fm_tts_tickets.order_dim1 = fm_order_dim1.id ORDER BY name ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('name', true)
				);
			}

			return $values;
		}

		public function get_budgets( $id )
		{
			$id = (int)$id;
			$values = array();
			$sql = "SELECT * FROM fm_tts_budget WHERE ticket_id = {$id}  ORDER BY period ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('id'),
					'ticket_id' => $this->db->f('ticket_id'),
					'amount' => $this->db->f('amount'),
					'period' => $this->db->f('period'),
					'created_on' => $this->db->f('created_on'),
					'created_by' => $this->db->f('created_by'),
					'remark' => $this->db->f('remark', true)
				);
			}
			return $values;
		}

		public function get_payments( $id )
		{
			$id = (int)$id;
			$values = array();
			$sql = "SELECT * FROM fm_tts_payments WHERE ticket_id = {$id}  ORDER BY period ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('id'),
					'ticket_id' => $this->db->f('ticket_id'),
					'amount' => $this->db->f('amount'),
					'period' => $this->db->f('period'),
					'created_on' => $this->db->f('created_on'),
					'created_by' => $this->db->f('created_by'),
					'remark' => $this->db->f('remark', true)
				);
			}
			return $values;
		}

		public function get_reported_by()
		{
			$values = array();
			$sql = "SELECT DISTINCT user_id as id , account_lastname, account_firstname FROM fm_tts_tickets $this->join phpgw_accounts ON fm_tts_tickets.user_id = phpgw_accounts.account_id ORDER BY account_lastname ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('account_lastname', true) . ', ' . $this->db->f('account_firstname', true)
				);
			}

			return $values;
		}

		function add_relation( $add_relation, $id )
		{
			$config = $GLOBALS['phpgw']->config->read();
			$interlink = CreateObject('property.interlink');
			$this->db->transaction_begin();

			foreach ($add_relation['request_id'] as $relation_id)
			{
				$target_id = false;
				$target = $interlink->get_specific_relation('property', '.project.request', '.ticket', $relation_id, 'target');
				if ($target)
				{
					$target_id =  $target[0];
				}

				if (!$target_id)
				{
					$interlink_data = array(
						'location1_id' => $GLOBALS['phpgw']->locations->get_id('property', '.project.request'),
						'location1_item_id' => $relation_id,
						'location2_id' => $GLOBALS['phpgw']->locations->get_id('property', '.ticket'),
						'location2_item_id' => $id,
						'account_id' => $this->account
					);

					$interlink->add($interlink_data);

					$request_ticket_hookup_status = isset($config['request_ticket_hookup_status']) && $config['request_ticket_hookup_status'] ? $config['request_ticket_hookup_status'] : false;

					if ($request_ticket_hookup_status)
					{
						$this->db->query("UPDATE fm_request SET status='{$request_ticket_hookup_status}' WHERE id='" . $relation_id . "'", __LINE__, __FILE__);
					}

					phpgwapi_cache::message_set(lang('request %1 has been added', $relation_id), 'message');
				}
				else
				{
					phpgwapi_cache::message_set(lang('request %1 has already been added to ticket %2', $relation_id, $target_id), 'error');
				}
			}
			$this->db->transaction_commit();
			return $receipt;
		}
		function get_ticket_from_order($order_id)
		{
			$order_id = (int) $order_id;
			$this->db->query("SELECT id FROM fm_tts_tickets WHERE order_id = {$order_id}", __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('id');

		}
	}