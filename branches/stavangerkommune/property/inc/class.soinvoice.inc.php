<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2014 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage eco
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package property
	 */
	class property_soinvoice
	{

		public $total_records		 = 0;
		public $sum_amount			 = 0;
		public $role				 = array();
		protected $invoice_approval	 = 2;

		function __construct()
		{
			$this->account_id		 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->acl				 = & $GLOBALS['phpgw']->acl;
			$this->db				 = & $GLOBALS['phpgw']->db;
			$this->join				 = & $this->db->join;
			$this->left_join		 = & $this->db->left_join;
			$this->like				 = & $this->db->like;
			$this->config			 = CreateObject('phpgwapi.config', 'property');
			$this->config->read();
			$custom_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));

			$this->invoice_approval	 = isset($custom_config->config_data['common']['invoice_approval']) && $custom_config->config_data['common']['invoice_approval'] ? $custom_config->config_data['common']['invoice_approval'] : 2;
		}

		function read_invoice($data)
		{
			$valid_order = array
				(
				'bilagsnr'			 => true,
				'spvend_code'		 => true,
				'fakturadato'		 => true,
				'oppsynsigndato'	 => true,
				'saksigndato'		 => true,
				'budsjettsigndato'	 => true,
				'periode'			 => true
			);

			$start			 = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query			 = isset($data['query']) ? $data['query'] : '';
			$sort			 = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order			 = isset($data['order']) && $valid_order[$data['order']] ? $data['order'] : '';
			$cat_id			 = isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
			$user_lid		 = isset($data['user_lid']) && $data['user_lid'] ? $data['user_lid'] : 'none';
			$paid			 = isset($data['paid']) ? $data['paid'] : '';
			$start_date		 = isset($data['start_date']) && $data['start_date'] ? $data['start_date'] : mktime(0, 0, 0, '01', '01', date('Y'));
			$end_date		 = isset($data['end_date']) && $data['end_date'] ? $data['end_date'] : time();
			$vendor_id		 = isset($data['vendor_id']) ? $data['vendor_id'] : '';
			$loc1			 = isset($data['loc1']) ? $data['loc1'] : '';
			$workorder_id	 = isset($data['workorder_id']) ? $data['workorder_id'] : '';
			$project_id		 = isset($data['project_id']) ? $data['project_id'] : '';
			$allrows		 = isset($data['allrows']) ? $data['allrows'] : '';
			$voucher_id		 = isset($data['voucher_id']) ? $data['voucher_id'] : '';
			$b_account_class = isset($data['b_account_class']) ? $data['b_account_class'] : '';
			$district_id	 = isset($data['district_id']) ? $data['district_id'] : '';
			$invoice_id		 = $data['invoice_id'] ? $data['invoice_id'] : '';
			$ecodimb		 = isset($data['ecodimb']) ? $data['ecodimb'] : '';

			$join_tables	 = '';
			$filtermethod	 = '';
			$querymethod	 = '';

			$this->db->query("SELECT * FROM fm_ecoart");
			$art_list = array();
			while($this->db->next_record())
			{
				$art_list[$this->db->f('id')] = $this->db->f('descr', true);
			}

			if($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' order by bilagsnr DESC';
			}

			$where = 'WHERE';

			if($user_lid == 'none' || !$user_lid)
			{
				return array();
			}
			else if($user_lid != 'all')
			{
				$filtermethod	 = " WHERE ( oppsynsmannid= '$user_lid' or saksbehandlerid= '$user_lid' or budsjettansvarligid= '$user_lid')";
				$where			 = 'AND';
			}

			if($cat_id > 0)
			{
				$filtermethod .= " $where typeid='$cat_id' ";
				$where = 'AND';
			}

			if($ecodimb)
			{
				$filtermethod .= " $where dimb = " . (int) $ecodimb;
				$where = 'AND';
			}

			if($district_id > 0 && $paid)
			{
				$filtermethod .= " $where  district_id='$district_id' ";
				$join_tables = " $this->join fm_location1 ON fm_ecobilagoverf.loc1 = fm_location1.loc1"
				. " $this->join fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id)";
				$where		 = 'AND';
			}

			if($vendor_id)
			{
				$filtermethod .= " $where  spvend_code ='{$vendor_id}' ";
				$where = 'AND';
			}
			if($loc1)
			{
				$filtermethod .= " $where  dima {$this->like} '%$loc1%' ";
				$where = 'AND';
			}

			if($invoice_id)
			{
				$filtermethod .= " $where fakturanr ='{$invoice_id}'";
				$where = 'AND';
			}

			if($paid)
			{
				$table = 'fm_ecobilagoverf';

				if($b_account_class)
				{
					$filtermethod .= " $where  fm_b_account.category ='$b_account_class' ";
					$where = 'AND';
					$join_tables .= " $this->join fm_b_account ON fm_ecobilagoverf.spbudact_code = fm_b_account.id";
				}

				if(!$workorder_id && !$voucher_id && !$invoice_id)
				{
					$start_periode	 = date('Ym', $start_date);
					$end_periode	 = date('Ym', $end_date);

					$filtermethod .= " $where (periode >='$start_periode' AND periode <= '$end_periode')";
					$where = 'AND';
				}
			}
			else
			{
				$table = 'fm_ecobilag';
			}

			$no_q = false;
			if($voucher_id)
			{
				$filtermethod	 = " WHERE bilagsnr = " . (int) $voucher_id . " OR bilagsnr_ut = '{$voucher_id}'";// OR spvend_code = ". (int)$query;
				$no_q			 = true;
			}

			if($workorder_id)
			{
				$filtermethod	 = " WHERE pmwrkord_code ='$workorder_id' ";
				$no_q			 = true;
			}
			else if($project_id)
			{
				$this->db->query("SELECT id FROM fm_workorder WHERE project_id='{$project_id}'", __LINE__, __FILE__);
				$_workorders = array(-1);
				while($this->db->next_record())
				{
					$_workorders[] = $this->db->f('id');
				}

				$filtermethod	 = ' WHERE pmwrkord_code IN (' . implode(',', $_workorders) . ')';
				$filtermethod .= " AND (periode >='$start_periode' AND periode <= '$end_periode')";
				$no_q			 = true;
			}

			if($query && !$no_q)
			{
				$query		 = (int) $query;
				$querymethod = " $where ( spvend_code = {$query} OR bilagsnr = {$query})";
			}


			$sql	 = "SELECT bilagsnr, bilagsnr_ut, count(bilagsnr) as invoice_count, sum(belop) as belop, sum(godkjentbelop) as godkjentbelop,spvend_code,fakturadato FROM  $table $join_tables $filtermethod $querymethod GROUP BY periode, bilagsnr,bilagsnr_ut,spvend_code,fakturadato,oppsynsigndato,saksigndato,budsjettsigndato";
			$sql2	 = "SELECT DISTINCT bilagsnr FROM  $table $join_tables $filtermethod $querymethod";

			if($GLOBALS['phpgw_info']['server']['db_type'] == 'postgres')
			{
				$sql_count			 = 'SELECT count(bilagsnr) as cnt, sum(godkjentbelop) AS sum_amount FROM (SELECT DISTINCT bilagsnr, sum(godkjentbelop) as godkjentbelop ' . substr($sql2, strripos($sql2, 'FROM')) . ' GROUP BY bilagsnr) AS t';
				//_debug_array($sql_count);
				$this->db->query($sql_count, __LINE__, __FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f('cnt');
				$this->sum_amount	 = $this->db->f('sum_amount');
			}
			else
			{
				$this->db->query($sql2, __LINE__, __FILE__);
				$this->total_records = $this->db->num_rows();

				$sql3				 = "SELECT sum(godkjentbelop) as sum_amount FROM $table $join_tables $filtermethod $querymethod";
				$this->db->query($sql3, __LINE__, __FILE__);
				$this->db->next_record();
				$this->sum_amount	 = $this->db->f('sum_amount');
			}

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
			}

			$temp = array();
			while($this->db->next_record())
			{
				$temp[] = array
					(
					'voucher_id'		 => $this->db->f('bilagsnr'),
					'voucher_out_id'	 => $this->db->f('bilagsnr_ut'),
					'invoice_count'		 => $this->db->f('invoice_count'),
					'amount'			 => $this->db->f('belop'),
					'approved_amount'	 => $this->db->f('godkjentbelop')
				);
			}

			$invoice = array();

			if($temp)
			{
				$role	 = $this->check_role();
				$i		 = 0;
				foreach($temp as $invoice_temp)
				{
					$voucher_id = $invoice_temp['voucher_id'];

					$sql = "SELECT pmwrkord_code,spvend_code,oppsynsmannid,saksbehandlerid,budsjettansvarligid,"
					. " utbetalingid,oppsynsigndato,saksigndato,budsjettsigndato,utbetalingsigndato,fakturadato,org_name,"
					. " forfallsdato,periode,periodization,periodization_start,artid,kidnr,kreditnota,currency "
					. " FROM {$table} {$this->join} fm_vendor ON fm_vendor.id = {$table}.spvend_code WHERE bilagsnr = {$voucher_id} "
					. " GROUP BY bilagsnr,pmwrkord_code,spvend_code,oppsynsmannid,saksbehandlerid,budsjettansvarligid,"
					. " utbetalingid,oppsynsigndato,saksigndato,budsjettsigndato,utbetalingsigndato,fakturadato,org_name,"
					. " forfallsdato,periode,periodization,periodization_start,artid,kidnr,kreditnota,currency";

					$this->db->query($sql, __LINE__, __FILE__);

					$this->db->next_record();

					$timestamp_voucher_date	 = mktime(0, 0, 0, date('m', strtotime($this->db->f('fakturadato'))), date('d', strtotime($this->db->f('fakturadato'))), date('y', strtotime($this->db->f('fakturadato'))));
					$timestamp_payment_date	 = mktime(0, 0, 0, date('m', strtotime($this->db->f('forfallsdato'))), date('d', strtotime($this->db->f('forfallsdato'))), date('y', strtotime($this->db->f('forfallsdato'))));

					if($this->db->f('oppsynsmannid') && $this->db->f('oppsynsigndato'))
					{
						$invoice[$i]['jan_date'] = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->db->f('oppsynsigndato')));
					}
					else
					{
						$invoice[$i]['jan_date'] = '';
					}
					if($this->db->f('saksbehandlerid') && $this->db->f('saksigndato'))
					{
						$invoice[$i]['super_date'] = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->db->f('saksigndato')));
					}
					else
					{
						$invoice[$i]['super_date'] = '';
					}

					if($this->db->f('budsjettansvarligid') && $this->db->f('budsjettsigndato'))
					{
						$invoice[$i]['budget_date'] = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->db->f('budsjettsigndato')));
					}
					else
					{
						$invoice[$i]['budget_date'] = '';
					}

					if($this->db->f('utbetalingid') && $this->db->f('utbetalingsigndato'))
					{
						$invoice[$i]['transfer_date'] = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->db->f('utbetalingsigndato')));
					}
					else
					{
						$invoice[$i]['transfer_date'] = '';
					}

					$invoice[$i]['counter']					 = $i;
					$invoice[$i]['current_user']			 = $GLOBALS['phpgw_info']['user']['account_lid'];
					$invoice[$i]['voucher_id']				 = $voucher_id;
					$invoice[$i]['voucher_out_id']			 = $invoice_temp['voucher_out_id'];
					$invoice[$i]['invoice_count']			 = $invoice_temp['invoice_count'];
					$invoice[$i]['vendor_id']				 = $this->db->f('spvend_code');
					$invoice[$i]['vendor']					 = $this->db->f('org_name', true);
					$invoice[$i]['is_janitor']				 = $role['is_janitor'];
					$invoice[$i]['is_supervisor']			 = $role['is_supervisor'];
					$invoice[$i]['is_budget_responsible']	 = $role['is_budget_responsible'];
					$invoice[$i]['is_transfer']				 = $role['is_transfer'];
					$invoice[$i]['janitor']					 = $this->db->f('oppsynsmannid');
					$invoice[$i]['supervisor']				 = $this->db->f('saksbehandlerid');
					$invoice[$i]['budget_responsible']		 = $this->db->f('budsjettansvarligid');
					$invoice[$i]['transfer_id']				 = $this->db->f('utbetalingid');
					$invoice[$i]['voucher_date']			 = $GLOBALS['phpgw']->common->show_date($timestamp_voucher_date, $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					$invoice[$i]['payment_date']			 = $GLOBALS['phpgw']->common->show_date($timestamp_payment_date, $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					$invoice[$i]['period']					 = $this->db->f('periode');
					$invoice[$i]['periodization']			 = $this->db->f('periodization');
					$invoice[$i]['periodization_start']		 = $this->db->f('periodization_start');

					$invoice[$i]['type']					 = $art_list[$this->db->f('artid')];
					$invoice[$i]['kidnr']					 = $this->db->f('kidnr');
					$invoice[$i]['kreditnota']				 = $this->db->f('kreditnota');
					$invoice[$i]['currency']				 = $this->db->f('currency');
					$invoice[$i]['order_id']				 = $this->db->f('pmwrkord_code');
					$invoice[$i]['amount']					 = $invoice_temp['amount'];
					$invoice[$i]['approved_amount']			 = $invoice_temp['approved_amount'];
					$invoice[$i]['num_days']				 = intval(($timestamp_payment_date - $timestamp_voucher_date) / (24 * 3600));
					$invoice[$i]['timestamp_voucher_date']	 = $timestamp_voucher_date;

					if($invoice[$i]['current_user'] == $invoice[$i]['janitor'] && $invoice[$i]['jan_date'])
					{
						$invoice[$i]['sign_orig'] = 'sign_janitor';
					}
					else if($invoice[$i]['current_user'] == $invoice[$i]['supervisor'] && $invoice[$i]['super_date'])
					{
						$invoice[$i]['sign_orig'] = 'sign_supervisor';
					}
					else if($invoice[$i]['current_user'] == $invoice[$i]['budget_responsible'] && $invoice[$i]['budget_date'])
					{
						$invoice[$i]['sign_orig'] = 'sign_budget_responsible';
					}

					$i++;
				}
			}
			//_debug_array($invoice);
			//_debug_array($invoice_temp);

			return $invoice;
		}

		function read_invoice_sub($data)
		{
			$start		 = isset($data['start']) && $data['start'] ? (int) $data['start'] : 0;
			$filter		 = isset($data['filter']) ? $data['filter'] : 'none';
			$sort		 = isset($data['sort']) ? $data['sort'] : 'DESC';
			$order		 = isset($data['order']) ? $data['order'] : '';
			$voucher_id	 = isset($data['voucher_id']) && $data['voucher_id'] ? (int) $data['voucher_id'] : 0;
			$paid		 = isset($data['paid']) ? $data['paid'] : '';
			$project_id	 = isset($data['project_id']) && $data['project_id'] ? (int) $data['project_id'] : 0;
			$order_id	 = isset($data['order_id']) && $data['order_id'] ? $data['order_id'] : 0;//might be bigint
			$results	 = isset($data['results']) && $data['results'] ? (int) $data['results'] : 0;
			$allrows	 = isset($data['allrows']) ? $data['allrows'] : '';
			if($paid)
			{
				$table = 'fm_ecobilagoverf';
			}
			else
			{
				$table = 'fm_ecobilag';
			}

			switch($order)
			{
				case 'id':
				case 'dima':
				case 'spbudact_code':
				case 'pmwrkord_code':
					$ordermethod = " ORDER BY $order $sort";
					break;
				case 'amount':
					$ordermethod = " ORDER BY belop $sort";
					break;
				case 'approved_amount':
					$ordermethod = " ORDER BY godkjentbelop $sort";
					break;
				default:
					$ordermethod = ' ORDER BY pmwrkord_code DESC, id DESC';
			}

			$filtermethod	 = '';
			$where			 = 'WHERE';

			if($voucher_id)
			{
				$filtermethod .= " {$where} bilagsnr= '$voucher_id'";
				$where = 'AND';
			}
			else if(!$order_id)
			{
				return array();
			}

			if($order_id)
			{
				$filtermethod .= " {$where} pmwrkord_code= '{$order_id}'";
				$where = 'AND';
			}

			if($project_id)
			{
				$filtermethod .= " {$where} fm_project.id = '{$project_id}'";
				$where = 'AND';
			}

			$sql = "SELECT $table.*,fm_workorder.status,fm_workorder.charge_tenant,org_name,"
			. "fm_workorder.claim_issued,fm_workorder_status.closed,periodization_id,project_type_id"
			. " FROM {$table}"
			. " {$this->left_join} fm_workorder ON fm_workorder.id = $table.pmwrkord_code"
			. " {$this->left_join} fm_workorder_status ON fm_workorder.status = fm_workorder_status.id"
			. " {$this->left_join} fm_project ON fm_workorder.project_id = fm_project.id"
			. " {$this->join} fm_vendor ON $table.spvend_code = fm_vendor.id $filtermethod";

			$this->db->query('SELECT count(*) AS cnt ' . substr($sql, strripos($sql, ' FROM')), __LINE__, __FILE__);
			$this->db->next_record();
			$this->total_records = $this->db->f('cnt');


			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
			}

			$i = 0;

			$invoice = array();
			while($this->db->next_record())
			{
				$status_line = 0;
				if($this->db->f('budsjettsigndato'))
				{
					$status_line = 3;
				}
				else if($this->db->f('saksigndato'))
				{
					$status_line = 2;
				}
				else if($this->db->f('oppsynsigndato'))
				{
					$status_line = 1;
				}

				$invoice[] = array
					(
					'counter'			 => $i,
					'claim_issued'		 => $this->db->f('claim_issued'),
					//		'project_id'			=> $this->db->f('project_id'),
					'workorder_id'		 => $this->db->f('pmwrkord_code'),
					'order_id'			 => $this->db->f('pmwrkord_code'),
					'status'			 => $this->db->f('status', true),
					'closed'			 => !!$this->db->f('closed'),
					'project_type_id'	 => $this->db->f('project_type_id'),
					'periodization_id'	 => $this->db->f('periodization_id'),
					'voucher_id'		 => $this->db->f('bilagsnr'),
					'voucher_out_id'	 => $this->db->f('bilagsnr_ut'),
					'id'				 => $this->db->f('id'),
					'invoice_id'		 => $this->db->f('fakturanr'),
					'budget_account'	 => $this->db->f('spbudact_code'),
					'dima'				 => $this->db->f('dima'),
					'dimb'				 => $this->db->f('dimb'),
					'dimd'				 => $this->db->f('dimd'),
					'dime'				 => $this->db->f('dime'),
					'remark'			 => !!$this->db->f('merknad', true),
					'tax_code'			 => $this->db->f('mvakode'),
					'amount'			 => $this->db->f('belop'),
					'approved_amount'	 => $this->db->f('godkjentbelop'),
					'charge_tenant'		 => $this->db->f('charge_tenant'),
					'vendor'			 => $this->db->f('org_name', true),
					//			'paid_percent'			=> $this->db->f('paid_percent'),
					'project_group'		 => $this->db->f('project_id'),
					'external_ref'		 => $this->db->f('external_ref'),
					'currency'			 => $this->db->f('currency'),
					'budget_responsible' => $this->db->f('budsjettansvarligid'),
					'budsjettsigndato'	 => $this->db->f('budsjettsigndato'),
					'transfer_time'		 => $this->db->f('overftid'),
					'line_text'			 => $this->db->f('line_text', true),
					'status_line'		 => $status_line
				);

				$i++;
			}

			return $invoice;
		}

		function read_invoice_sub_sum($data)
		{
			$start		 = isset($data['start']) && $data['start'] ? (int) $data['start'] : 0;
			$filter		 = isset($data['filter']) ? $data['filter'] : 'none';
			$sort		 = isset($data['sort']) ? $data['sort'] : 'DESC';
			$order		 = isset($data['order']) ? $data['order'] : '';
			$voucher_id	 = isset($data['voucher_id']) && $data['voucher_id'] ? (int) $data['voucher_id'] : 0;
			$paid		 = isset($data['paid']) ? $data['paid'] : '';
			$year		 = isset($data['year']) ? $data['year'] : '';
			$project_id	 = isset($data['project_id']) && $data['project_id'] ? (int) $data['project_id'] : 0;
			$order_id	 = isset($data['order_id']) && $data['order_id'] ? $data['order_id'] : 0;//might be bigint

			if($paid)
			{
				$table		 = 'fm_ecobilagoverf';
				$overftid	 = ',overftid';
			}
			else
			{
				$table		 = 'fm_ecobilag';
				$overftid	 = '';
			}

			switch($order)
			{
				case 'dima':
				case 'belop':
				case 'spbudact_code':
				case 'pmwrkord_code':
					$ordermethod = " ORDER BY $order $sort";
					break;
				default:
					$ordermethod = " ORDER BY pmwrkord_code DESC";
			}

			$filtermethod	 = '';
			$where			 = 'WHERE';

			if($voucher_id)
			{
				$filtermethod .= " {$where} bilagsnr= '$voucher_id'";
				$where = 'AND';
			}

			if($order_id)
			{
				$filtermethod .= " {$where} pmwrkord_code= '{$order_id}'";
				$where = 'AND';
			}

			if($project_id)
			{
				$filtermethod .= " {$where} fm_project.id = '{$project_id}'";
				$where = 'AND';
			}

			if($year)
			{
				$filtermethod .= " {$where} ({$table}.periode > {$year}00 AND {$table}.periode < {$year}13 OR {$table}.periode IS NULL)";
				$where = 'AND';
			}

			$groupmethod = "GROUP BY pmwrkord_code,bilagsnr,bilagsnr_ut,fakturanr,"
			. " currency,budsjettansvarligid,org_name,periode,periodization,periodization_start";

			$sql = "SELECT DISTINCT pmwrkord_code,bilagsnr,bilagsnr_ut,fakturanr,sum(belop) as belop, sum(godkjentbelop) as godkjentbelop,"
			. " currency,budsjettansvarligid,org_name,periode,periodization,periodization_start"
			. " FROM {$table}"
			. " {$this->join} fm_ecoart ON fm_ecoart.id = $table.artid"
			. " {$this->join} fm_workorder ON fm_workorder.id = $table.pmwrkord_code"
			. " {$this->join} fm_project ON fm_workorder.project_id = fm_project.id"
			. " {$this->join} fm_vendor ON {$table}.spvend_code = fm_vendor.id {$filtermethod} {$groupmethod}";

			$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			$values = array();
			while($this->db->next_record())
			{
				$values[] = array
					(
					'workorder_id'			 => $this->db->f('pmwrkord_code'),
					'voucher_id'			 => $this->db->f('bilagsnr'),
					'voucher_out_id'		 => $this->db->f('bilagsnr_ut'),
					'invoice_id'			 => $this->db->f('fakturanr'),
					'amount'				 => $this->db->f('belop'),
					'approved_amount'		 => $this->db->f('godkjentbelop'),
					'vendor'				 => $this->db->f('org_name', true),
					'currency'				 => $this->db->f('currency'),
					'period'				 => $this->db->f('periode'),
					'periodization'			 => $this->db->f('periodization'),
					'periodization_start'	 => $this->db->f('periodization_start'),
					'budget_responsible'	 => $this->db->f('budsjettansvarligid')
				);
			}

			foreach($values as &$entry)
			{
				$sql = "SELECT budsjettsigndato{$overftid},fm_ecoart.descr as type"
				. " FROM {$table} {$this->join} fm_ecoart ON fm_ecoart.id = $table.artid"
				. " WHERE pmwrkord_code = '{$entry['workorder_id']}' AND bilagsnr = '{$entry['voucher_id']}' AND fakturanr = '{$entry['invoice_id']}'";

				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$entry['budsjettsigndato']	 = $this->db->f('budsjettsigndato');
				$entry['transfer_time']		 = $this->db->f('overftid');
				$entry['type']				 = $this->db->f('type');
			}

			return $values;
		}

		function read_consume($data)
		{
			if(is_array($data))
			{
				$start			 = isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter			 = isset($data['filter']) ? $data['filter'] : 'none';
				$query			 = isset($data['query']) ? $data['query'] : '';
				$sort			 = isset($data['sort']) ? $data['sort'] : 'DESC';
				$order			 = isset($data['order']) ? $data['order'] : '';
				$cat_id			 = isset($data['cat_id']) && $data['cat_id'] ? (int) $data['cat_id'] : 0;
				$start_date		 = isset($data['start_date']) && $data['start_date'] ? $data['start_date'] : 0;
				$end_date		 = isset($data['end_date']) && $data['end_date'] ? $data['end_date'] : time();
				$vendor_id		 = isset($data['vendor_id']) ? (int) $data['vendor_id'] : 0;
				$loc1			 = isset($data['loc1']) ? $data['loc1'] : '';
				$district_id	 = isset($data['district_id']) ? (int) $data['district_id'] : 0;
				$workorder_id	 = isset($data['workorder_id']) && $data['workorder_id'] ? $data['workorder_id'] : 0;
				$b_account_class = isset($data['b_account_class']) ? $data['b_account_class'] : '';
				$b_account		 = isset($data['b_account']) ? $data['b_account'] : '';
				$ecodimb		 = isset($data['ecodimb']) ? $data['ecodimb'] : '';
			}

			$where = 'AND';

			if($b_account_class)
			{
				$filtermethod	 = " $where fm_b_account.category='$b_account_class'";
				$where			 = 'AND';
			}
			else
			{
				$select_account_class	 = ',fm_b_account.category as b_account_class';
				$group_account_class	 = ', spbudact_code,fm_b_account.category';
			}

			if($b_account)
			{
				$filtermethod .= " {$where} fm_b_account.id = '{$b_account}'";
				$where					 = 'AND';
				$select_account_class	 = ',fm_b_account.id as b_account_class';
				$group_account_class	 = ', spbudact_code,fm_b_account.id';
			}


			if($vendor_id)
			{
				$filtermethod .= " $where (spvend_code = $vendor_id)";
				$where = 'AND';
			}

			if($loc1)
			{
				$filtermethod .=" $where (dima $this->like '%$loc1%')";
				$where = 'AND';
			}


			if($district_id)
			{
				$filtermethod.= " $where district_id= $district_id ";
				$where = 'AND';
			}

			if($workorder_id)
			{
				$filtermethod.= " $where pmwrkord_code = '{$workorder_id}'";
				$where = 'AND';
			}


			if($cat_id > 0)
			{
				$filtermethod .= " $where typeid = $cat_id";
				$where = 'AND';
			}

			if($ecodimb)
			{
				$filtermethod .= " $where dimb = " . (int) $ecodimb;
				$where = 'AND';
			}

			$start_periode	 = date('Ym', $start_date);
			$end_periode	 = date('Ym', $end_date);

			$sql = "SELECT district_id,periode,sum(godkjentbelop) as consume {$select_account_class}"
			. " FROM  fm_ecobilagoverf {$this->join} fm_location1 ON (fm_ecobilagoverf.loc1 = fm_location1.loc1) "
			. " {$this->join} fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id) "
			. " {$this->join} fm_b_account ON (fm_ecobilagoverf.spbudact_code = fm_b_account.id) "
			. " WHERE (periode >='{$start_periode}' AND periode <= '{$end_periode}' {$filtermethod})"
			. " GROUP BY district_id,periode $group_account_class"
			. " ORDER BY periode";
			//echo $sql;

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			$consume = array();

			while($this->db->next_record())
			{
				$consume[] = array
				(
					'consume'		 => round($this->db->f('consume')),
					'period'		 => $this->db->f('periode'),
					'district_id'	 => $this->db->f('district_id'),
					'account_class'	 => $b_account_class ? $b_account_class : $this->db->f('b_account_class'),
					'paid'			 => 'x'
				);
			}

			$sql = "SELECT district_id,periode,sum(godkjentbelop) as consume {$select_account_class}"
			. " FROM  fm_ecobilag {$this->join} fm_location1 ON (fm_ecobilag.loc1 = fm_location1.loc1) "
			. " {$this->join} fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id) "
			. " {$this->join} fm_b_account ON (fm_ecobilag.spbudact_code = fm_b_account.id) "
			. " WHERE (1=1 {$filtermethod})"
			. " GROUP BY district_id,periode $group_account_class"
			. " ORDER BY periode";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records += $this->db->num_rows();

			while($this->db->next_record())
			{
				$consume[] = array
				(
					'consume'		 => round($this->db->f('consume')),
					'period'		 => $this->db->f('periode'),
					'district_id'	 => $this->db->f('district_id'),
					'account_class'	 => $b_account_class ? $b_account_class : $this->db->f('b_account_class'),
					'paid'			 => ''
				);
			}

			return $consume;
		}

		function check_for_updates($values)
		{
			$update = false;

			if($values['sign_orig'] != $values['sign'])
			{
				$update = true;
				return $update;
			}

			$sql = "SELECT * FROM fm_ecobilag WHERE bilagsnr=" . $values['voucher_id'];
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

			$this->db->next_record();

			if(($this->db->f('utbetalingsigndato') && !$values['transfer']) || (!$this->db->f('utbetalingsigndato') && $values['transfer']))
			{
				$update = true;
				return $update;
			}

			if(($this->db->f('kreditnota') && !$values['kreditnota']) || (!$this->db->f('kreditnota') && $values['kreditnota']))
			{
				$update = true;
				return $update;
			}

			$timestamp_voucher_date	 = mktime(0, 0, 0, date('m', strtotime($this->db->f('fakturadato'))), date('d', strtotime($this->db->f('fakturadato'))), date('y', strtotime($this->db->f('fakturadato'))));
			$timestamp_payment_date	 = mktime(0, 0, 0, date('m', strtotime($this->db->f('forfallsdato'))), date('d', strtotime($this->db->f('forfallsdato'))), date('y', strtotime($this->db->f('forfallsdato'))));

			if(((intval(($timestamp_payment_date - $timestamp_voucher_date) / (24 * 3600))) != $values['num_days']))
			{
				$update = true;
				return $update;
			}
		}

		function update_invoice_sub($values)
		{
			$update_status	 = array();
			$receipt		 = array();
			$GLOBALS['phpgw']->db->transaction_begin();

			while($entry = each($values['counter']))
			{
				$local_error = false;

				$n = $entry[0];

				//_debug_array($entry);
				$id				 = (int) $values['id'][$n];
				$approved_amount = isset($values['approved_amount'][$n]) && $values['approved_amount'][$n] ? str_replace(',', '.', $values['approved_amount'][$n]) : 0;
				if(!$approved_amount || $approved_amount == '00.0')
				{
					$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag SET godkjentbelop = $approved_amount WHERE id='$id'");
					$receipt['message'][] = array('msg' => lang('Voucher is updated '));
					continue;
				}

				if($values['budget_account'][$n])
				{

					$budget_account = $values['budget_account'][$n];
					if (!$this->check_valid_b_account($budget_account))
					{
						$receipt['error'][]	 = array('msg' => lang('This account is not valid:') . " " . $budget_account);
						$local_error		 = true;
					}
				}
				else
				{
					$receipt['error'][]	 = array('msg' => lang('Budget account is missing:'));
					$local_error		 = true;
				}


				if(!$values['dimb'][$n])
				{
					$dimb_field			 = "dimb=NULL";
					$local_error		 = true;
					$receipt['error'][]	 = array('msg' => lang('Please select dimb!'));
				}
				else
				{
					$dimb = $values['dimb'][$n];
					$GLOBALS['phpgw']->db->query("select count(*) as cnt from fm_ecodimb where id ='$dimb'");
					$GLOBALS['phpgw']->db->next_record();
					if($GLOBALS['phpgw']->db->f('cnt') == 0)
					{
						$receipt['error'][]	 = array('msg' => lang('This Dim B is not valid:') . " " . $dimd);
						$local_error		 = true;
					}

					$dimb_field = "dimb='{$dimb}'";
				}

					
				if(!$values['dimd'][$n])
				{
					$dimd_field			 = "dimd=NULL";
					$local_error		 = true;
					$receipt['error'][]	 = array('msg' => lang('Dim D is mandatory'));
				}
				else
				{
					$dimd = $values['dimd'][$n];
					$GLOBALS['phpgw']->db->query("select count(*) as cnt from fm_ecodimd where id ='$dimd'");
					$GLOBALS['phpgw']->db->next_record();
					if($GLOBALS['phpgw']->db->f('cnt') == 0)
					{
						$receipt['error'][]	 = array('msg' => lang('This Dim D is not valid:') . " " . $dimd);
						$local_error		 = true;
					}

					$dimd_field = "dimd='{$dimd}', dime = '{$dimd}'";
				}

				if(!$values['dima'][$n])
				{
					$dima_field			 = "dima=NULL";
					$receipt['error'][]	 = array('msg' => lang('Dim A is missing'));
					$local_error		 = true;
				}
				else
				{
					$dima_check = substr($values['dima'][$n], 0, 4);
					$GLOBALS['phpgw']->db->query("select loc1, kostra_id from fm_location1 where loc1 = '$dima_check' ");
					$GLOBALS['phpgw']->db->next_record();
					if(!$GLOBALS['phpgw']->db->f('loc1'))
					{
						$receipt['error'][]	 = array('msg' => lang('This Dim A is not valid:') . " " . $values['dima'][$n]);
						$local_error		 = true;
					}

					if(!$GLOBALS['phpgw']->db->f('kostra_id') || $GLOBALS['phpgw']->db->f('kostra_id') == 0)
					{
						$receipt['error'][]	 = array('msg' => 'objektet mangler tjeneste - utgått? ' . " " . $values['dima'][$n]);
						$local_error		 = true;
					}

					//	$dima_field="dima="."'" . $values['dima'][$n] . "'";
					$dima_field = "dima=" . "'" . $values['dima'][$n] . "',loc1=" . "'" . substr($values['dima'][$n], 0, 4) . "'";

					$kostra_field = "kostra_id=" . "'" . $GLOBALS['phpgw']->db->f('kostra_id') . "'";
				}

				if(!$local_error)
				{
					$tax_code		 = (int) $values['tax_code'][$n];
					$workorder_id	 = $values['workorder_id'][$n];
					if(isset($values['close_order'][$n]) && $values['close_order'][$n] && !$values['close_order_orig'][$n])
					{
						$update_status[$workorder_id] = 'X';
					}

					if((!isset($values['close_order'][$n]) || !$values['close_order'][$n]) && (isset($values['close_order_orig'][$n]) && $values['close_order_orig'][$n]))
					{
						$update_status[$workorder_id] = 'R';
					}

					/*
					  if(isset($values['paid_percent'][$n]) && $values['paid_percent'][$n])
					  {
					  $update_paid_percent[$workorder_id] = $values['paid_percent'][$n];
					  }
					 */
					if($values['workorder_id'][$n])
					{
						$GLOBALS['phpgw']->db->query("SELECT id FROM fm_workorder WHERE id = '{$values['workorder_id'][$n]}'", __LINE__, __FILE__);
						if($this->db->next_record())
						{
							$GLOBALS['phpgw']->db->query("UPDATE fm_workorder SET category = '{$values['dimd'][$n]}' ,account_id = '{$values['budget_account'][$n]}' WHERE id='{$values['workorder_id'][$n]}'", __LINE__, __FILE__);
						}
					}

					$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag SET $dima_field ,$kostra_field,{$dimd_field},{$dimb_field}, mvakode = {$tax_code},spbudact_code = '{$budget_account}',godkjentbelop = $approved_amount WHERE id='{$id}'", __LINE__, __FILE__);

					$receipt['message'][] = array('msg' => lang('Voucher is updated '));
				}
			}

			if($update_status)
			{
				$closed	 = isset($this->config->config_data['workorder_closed_status']) && $this->config->config_data['workorder_closed_status'] ? $this->config->config_data['workorder_closed_status'] : '';
				$reopen	 = isset($this->config->config_data['workorder_reopen_status']) && $this->config->config_data['workorder_reopen_status'] ? $this->config->config_data['workorder_reopen_status'] : '';

				if(!$closed)
				{
					throw new Exception('property_soinvoice::update_invoice_sub() - "workorder_closed_status" not configured');
				}
				if(!$reopen)
				{
					throw new Exception('property_soinvoice::update_invoice_sub() - "workorder_reopen_status" not configured');
				}

				$status_code = array('X' => $closed, 'R' => $reopen);

				$historylog_workorder = CreateObject('property.historylog', 'workorder');

				foreach($update_status as $id => $entry)
				{
					$this->db->query("SELECT type FROM fm_orders WHERE id={$id}", __LINE__, __FILE__);
					$this->db->next_record();
					switch($this->db->f('type'))
					{
						case 'workorder':
							$GLOBALS['phpgw']->db->query("SELECT id FROM fm_workorder WHERE status='{$status_code[$entry]}' AND id = {$id}");
							if(!$GLOBALS['phpgw']->db->next_record())
							{
								$historylog_workorder->add($entry, $id, $status_code[$entry]);
								$GLOBALS['phpgw']->db->query("UPDATE fm_workorder set status='{$status_code[$entry]}' WHERE id = {$id}");
								$receipt['message'][] = array('msg' => lang('Workorder %1 is %2', $id, $status_code[$entry]));
							}
							break;
					}
				}
			}

			/*
			  if (isset($update_paid_percent) AND is_array($update_paid_percent))
			  {
			  $workorder = CreateObject('property.soworkorder');
			  foreach ($update_paid_percent as $workorder_id => $paid_percent)
			  {
			  $paid_percent = (int) $paid_percent;
			  $GLOBALS['phpgw']->db->query("UPDATE fm_workorder set paid_percent={$paid_percent} WHERE id= '$workorder_id'");

			  $this->db->query("SELECT type FROM fm_orders WHERE id='{$workorder_id}'",__LINE__,__FILE__);
			  $this->db->next_record();
			  switch ( $this->db->f('type') )
			  {
			  case 'workorder':
			  $this->db->query("SELECT project_id FROM fm_workorder WHERE id='{$workorder_id}'",__LINE__,__FILE__);
			  $this->db->next_record();
			  $project_id = $this->db->f('project_id');
			  $workorder->update_planned_cost($project_id);
			  break;
			  }
			  }
			  }
			 */
			$GLOBALS['phpgw']->db->transaction_commit();

			return $receipt;
		}

		function update_single_line($values, $paid = false)
		{
			$this->db->transaction_begin();

			$id = (int) $values['id'];

			$table = 'fm_ecobilag';

			if($paid) // only minor corrections are allowed
			{
				$table = 'fm_ecobilagoverf';

				$value_set = array
				(
					//			'project_id'	=> $values['project_group'] ? $values['project_group'] : '',
					'pmwrkord_code' => $values['order_id'],
				//			'process_log'	=> $this->db->db_addslashes($values['process_log']),
				//			'process_code'	=> $values['process_code'],
				);

				$value_set = $this->db->validate_update($value_set);

				$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$id}", __LINE__, __FILE__);

				return $this->db->transaction_commit();
			}

			if($values['approve'] != $values['sign_orig'])
			{
				switch($values['sign_orig'])
				{
					case 'is_janitor':
						$value_set['oppsynsigndato']	 = null;
						break;
					case 'is_supervisor':
						$value_set['saksigndato']		 = null;
						break;
					case 'is_budget_responsible':
						$value_set['budsjettsigndato']	 = null;
						break;
				}

				switch($values['approve'])
				{
					case 'is_janitor':
						$value_set['oppsynsigndato']		 = date($this->db->datetime_format());
						$value_set['oppsynsmannid']			 = $values['my_initials'];
						break;
					case 'is_supervisor':
						$value_set['saksigndato']			 = date($this->db->datetime_format());
						$value_set['saksbehandlerid']		 = $values['my_initials'];
						break;
					case 'is_budget_responsible':
						$value_set['budsjettsigndato']		 = date($this->db->datetime_format());
						$value_set['budsjettansvarligid']	 = $values['my_initials'];
						break;
				}


				if(isset($value_set['budsjettansvarligid']) && !$value_set['budsjettansvarligid'])
				{
					phpgwapi_cache::message_set('Mangler anviser', 'error');
				}
				else
				{
					$value_set = $this->db->validate_update($value_set);
					$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$id}", __LINE__, __FILE__);
				}
			}

			$value_set = array
				(
				'godkjentbelop'	 => $values['approved_amount'],
				'project_id'	 => $values['project_group'] ? $values['project_group'] : '',
				'pmwrkord_code'	 => $values['order_id'],
				'process_log'	 => $this->db->db_addslashes($values['process_log']),
				'process_code'	 => $values['process_code'],
			);


			$value_set = $this->db->validate_update($value_set);

			$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$id}", __LINE__, __FILE__);

			if(!$values['approved_amount'])
			{
				$this->db->query("UPDATE {$table} SET godkjentbelop = 0 WHERE id= {$id}", __LINE__, __FILE__);
			}

			if(isset($values['split_line']) && $values['split_amount'] && isset($values['split_amount']) && $values['split_amount'])
			{
				$metadata	 = $this->db->metadata($table);
				$sql		 = "SELECT * FROM {$table} WHERE id= {$id}";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();

				$value_set = array();

				foreach($metadata as $_field)
				{
					if($_field->name != 'id')
					{
						$value_set[$_field->name] = $this->db->f($_field->name, true);
					}
				}

				$this->db->query("INSERT INTO {$table} (" . implode(',', array_keys($value_set)) . ')'
				. ' VALUES (' . $this->db->validate_insert(array_values($value_set)) . ')', __LINE__, __FILE__);

				$new_id = $this->db->get_last_insert_id($table, 'id');

				$this->db->query("SELECT belop FROM {$table} WHERE id={$id}", __LINE__, __FILE__);
				$this->db->next_record();
				$amount		 = $this->db->f('belop');
				$new_amount	 = $amount - $values['split_amount'];

				$value_set	 = array
					(
					'belop'			 => $new_amount,
					'godkjentbelop'	 => $new_amount,
				);
				$value_set	 = $this->db->validate_update($value_set);
				$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$id}", __LINE__, __FILE__);

				$value_set	 = array
					(
					'belop'			 => $values['split_amount'],
					'godkjentbelop'	 => $values['split_amount'],
				);
				$value_set	 = $this->db->validate_update($value_set);
				$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$new_id}", __LINE__, __FILE__);
			}

			return $this->db->transaction_commit();
		}

		function read_remark($id = '', $paid = '')
		{
			if($paid)
			{
				$table = 'fm_ecobilagoverf';
			}
			else
			{
				$table = 'fm_ecobilag';
			}

			$this->db->query(" SELECT merknad from $table  where id= '$id'");
			$this->db->next_record();

			return $this->db->f('merknad');
		}

		function check_role($dimb = 0)
		{
			if($this->role && !$dimb)
			{
				return $this->role;
			}

			if(isset($this->config->config_data['invoice_acl']) && $this->config->config_data['invoice_acl'] == 'dimb')
			{
				$dimb						 = (int) $dimb;
				$filter_dimb				 = $dimb ? "AND ecodimb = {$dimb}" : '';
				$this->db->query("SELECT user_id FROM fm_ecodimb_role_user WHERE user_id = {$this->account_id} AND role_id IN (1, 2, 3) {$filter_dimb} AND expired_on IS NULL AND active_from < " . time() . ' AND (active_to > ' . time() . ' OR active_to = 0)');
				$this->db->next_record();
				$this->role['is_janitor']	 = !!$this->db->f('user_id');

				$this->db->query("SELECT user_id FROM fm_ecodimb_role_user WHERE user_id = {$this->account_id} AND role_id IN (2, 3) {$filter_dimb} AND expired_on IS NULL AND active_from < " . time() . ' AND (active_to > ' . time() . ' OR active_to = 0)');
				$this->db->next_record();
				$this->role['is_supervisor'] = !!$this->db->f('user_id');

				$this->db->query("SELECT user_id FROM fm_ecodimb_role_user WHERE user_id = {$this->account_id} AND role_id IN (3) {$filter_dimb} AND expired_on IS NULL AND active_from < " . time() . ' AND (active_to > ' . time() . ' OR active_to = 0)');
				$this->db->next_record();
				$this->role['is_budget_responsible'] = !!$this->db->f('user_id');
			}
			else
			{
				$this->role = array(
					'is_janitor'			 => $this->acl->check('.invoice', 32, 'property'),
					'is_supervisor'			 => $this->acl->check('.invoice', 64, 'property'),
					'is_budget_responsible'	 => $this->acl->check('.invoice', 128, 'property'),
					'is_transfer'			 => $this->acl->check('.invoice', 16, 'property')
				);
			}
			return $this->role;
		}

		function get_dimb_role_user($role_id, $dimb = '', $selected = '')
		{
			$filter_dimb = $dimb ? "AND ecodimb = {$dimb}" : '';
			$role_id	 = (int) $role_id;
			switch($role_id)
			{
				case 1:
					$role_filter = "role_id IN (1, 2, 3)";
					break;
				case 2:
					$role_filter = "role_id IN (2, 3)";
					break;
				case 3:
					$role_filter = "role_id IN (3)";
					break;
				default:
					$role_filter = "role_id = {$role_id}";
			}


			$sql = "SELECT DISTINCT account_lid,account_lastname, account_firstname FROM fm_ecodimb_role_user"
			. " {$this->db->join} phpgw_accounts ON fm_ecodimb_role_user.user_id = phpgw_accounts.account_id"
			. " WHERE {$role_filter} {$filter_dimb} AND expired_on IS NULL"
			. ' AND active_from < ' . time()
			. ' AND (active_to > ' . time() . ' OR active_to = 0)'
			. " ORDER BY account_lastname ASC, account_firstname ASC";

//_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while($this->db->next_record())
			{
				$id			 = $this->db->f('account_lid');
				$values[]	 = array
					(
					'id'		 => $id,
					'name'		 => $this->db->f('account_lastname') . ', ' . $this->db->f('account_firstname'),
					'selected'	 => $selected == $id ? 1 : 0
				);
			}
			return $values;
		}

		function get_default_dimb_role_user($role_id, $dimb)
		{
			$dimb	 = (int) $dimb;
			$role_id = (int) $role_id;
			$sql	 = "SELECT user_id FROM fm_ecodimb_role_user"
			. " WHERE role_id = {$role_id} AND ecodimb = {$dimb} AND expired_on IS NULL AND default_user = 1  AND active_from < " . time() . ' AND (active_to > ' . time() . ' OR active_to = 0)';
//_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return (int) $this->db->f('user_id');
		}

		function check_count($voucher_id)
		{

			$this->db->query("SELECT count(id) AS invoice_count, count(dima) AS dima_count, count(spbudact_code) AS spbudact_code_count, count(dimd) AS dimd_count"
			. " FROM fm_ecobilag WHERE bilagsnr ='{$voucher_id}'");
			$this->db->next_record();

			$check_count = array
				(
				'dima_count'			 => $this->db->f('dima_count'),
				'spbudact_code_count'	 => $this->db->f('spbudact_code_count'),
				'invoice_count'			 => $this->db->f('invoice_count'),
				'dimd_count'			 => $this->db->f('dimd_count'),
			);

			$this->db->query("select count(kostra_id) as kostra_count  from fm_ecobilag where bilagsnr ='$voucher_id' and kostra_id > 0");
			$this->db->next_record();
			$check_count['kostra_count'] = $this->db->f('kostra_count');

			return $check_count;
		}

		function update_period($voucher_id = '', $period = '')
		{
			$receipt = array();
			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_ecobilag set periode='$period' where bilagsnr='$voucher_id'");

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg' => lang('voucher period is updated'));
			return $receipt;
		}

		function update_periodization($voucher_id = '', $periodization = '')
		{
			$receipt = array();
			$this->db->transaction_begin();

			if($periodization)
			{
				$value = "'{$periodization}'";
			}
			else
			{
				$value = 'NULL';
			}

			$this->db->query("UPDATE fm_ecobilag SET periodization={$value} where bilagsnr='{$voucher_id}'");

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg' => lang('voucher periodization is updated'));
			return $receipt;
		}

		function update_periodization_start($voucher_id = '', $periodization_start = '')
		{
			$receipt = array();
			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_ecobilag set periodization_start='$periodization_start' where bilagsnr='$voucher_id'");

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg' => lang('voucher periodization start is updated'));
			return $receipt;
		}

		function increment_bilagsnr()
		{
			$name		 = 'Bilagsnummer';
			$now		 = time();
			$this->db->query("SELECT value, start_date FROM fm_idgenerator WHERE name = '{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$bilagsnr	 = $this->db->f('value') + 1;
			$start_date	 = (int) $this->db->f('start_date');

			$this->db->query("UPDATE fm_idgenerator SET value = value + 1 WHERE name = '{$name}' AND start_date = $start_date");
			return $bilagsnr;
		}

		function next_bilagsnr()
		{
			$name		 = 'Bilagsnummer';
			$now		 = time();
			$this->db->query("SELECT value FROM fm_idgenerator WHERE name = '{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$bilagsnr	 = $this->db->f('value') + 1;

			return $bilagsnr;
		}

		function check_vendor($vendor_id)
		{
			$this->db->query("select count(*) as cnt from fm_vendor where id='$vendor_id'");
			$this->db->next_record();
			return $this->db->f('cnt');
		}

		function tax_code_list()
		{
			$this->db->query("SELECT * FROM fm_ecomva ORDER BY id ASC ");
			$values = array();
			while($this->db->next_record())
			{
				$id			 = $this->db->f('id');
				$values[]	 = array
					(
					'id'	 => $id,
					'name'	 => $id,
				);
			}
			return $values;
		}

		function get_lisfm_ecoart()
		{
			$this->db->query("SELECT * FROM fm_ecoart order by id asc ");
			$art_list = array();
			while($this->db->next_record())
			{
				$art_list[] = Array(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('descr')
				);
			}

			return $art_list;
		}

		//----------

		function get_type_list()
		{
			$this->db->query("SELECT * FROM fm_ecobilag_category order by id asc ");
			$category = array();
			while($this->db->next_record())
			{
				$category[] = Array(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('descr')
				);
			}
			return $category;
		}

		//----------
		function select_dimb_list($selected = 0)
		{
			$selected			 = (int) $selected;
			$include_selected	 = false;

			if(isset($this->config->config_data['invoice_acl']) && $this->config->config_data['invoice_acl'] == 'dimb')
			{
				$sql				 = "SELECT DISTINCT fm_ecodimb.* FROM fm_ecodimb {$this->db->join} fm_ecodimb_role_user ON fm_ecodimb.id = fm_ecodimb_role_user.ecodimb"
				. ' WHERE fm_ecodimb_role_user.user_id = ' . (int) $this->account_id
				. ' AND expired_on IS NULL'
				. ' ORDER BY descr ASC';
				$include_selected	 = true;
			}
			else
			{
				$sql = "SELECT * FROM fm_ecodimb ORDER BY descr ASC";
			}

			$selected_found	 = false;
			$this->db->query($sql);
			$dimb_list		 = array();
			while($this->db->next_record())
			{
				$id = $this->db->f('id');
				if($id == $selected)
				{
					$selected_found = true;
				}

				$dimb_list[] = array
					(
					'id'	 => $id,
					'name'	 => $this->db->f('descr', true)
				);
			}

			if($include_selected && $selected && !$selected_found)
			{
				$this->db->query("SELECT descr FROM fm_ecodimb WHERE id={$selected}");
				$this->db->next_record();
				array_unshift($dimb_list, array('id' => $selected, 'name' => '**' . $this->db->f('descr', true) . '**'));
			}

			return $dimb_list;
		}

		//-------------------
		function select_dimd_list()
		{
			$this->db->query("SELECT * FROM fm_ecodimd order by id asc ");
			$dimd_list = array();
			while($this->db->next_record())
			{
				$dimd_list[] = Array(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('descr')
				);
			}
			return $dimd_list;
		}

		//---------------------

		function select_tax_code_list()
		{
			$this->db->query("SELECT * FROM fm_ecomva order by id asc ");
			$tax_code_list = array();
			while($this->db->next_record())
			{
				$tax_code_list[] = Array(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('descr')
				);
			}
			return $tax_code_list;
		}

		function select_account_class()
		{
			$sql = "SELECT id from fm_b_account_category order by id";
			$this->db->query($sql, __LINE__, __FILE__);

			$class = array();
			while($this->db->next_record())
			{
				$class[] = Array(
					'id'	 => $this->db->f('id'),
					'name'	 => $this->db->f('id')
				);
			}
			return $class;
		}

		function delete($bilagsnr)
		{
			$this->db->query("DELETE FROM fm_ecobilag WHERE bilagsnr ='" . $bilagsnr . "'", __LINE__, __FILE__);
		}

		function read_single_voucher($bilagsnr = 0, $id = 0, $paid = false)
		{
			$table = 'fm_ecobilag';
			if($paid)
			{
				$table = 'fm_ecobilagoverf';
			}

			$bilagsnr	 = (int) $bilagsnr;
			$id			 = (int) $id;

			if($bilagsnr)
			{
				$filtermethod = "WHERE bilagsnr ='$bilagsnr'";
			}
			else if($id)
			{
				$filtermethod = " WHERE {$table}.id ='{$id}'";
			}
			else
			{
				return array();
			}

			$sql = "SELECT {$table}.*,fm_workorder_status.descr as status, fm_workorder.charge_tenant,org_name,"
			. " fm_workorder.claim_issued, fm_workorder.continuous, fm_workorder_status.closed FROM {$table}"
			. " {$this->left_join} fm_workorder ON fm_workorder.id = {$table}.pmwrkord_code"
			. " {$this->left_join} fm_workorder_status ON fm_workorder.status = fm_workorder_status.id"
			. " {$this->left_join} fm_project ON fm_workorder.project_id = fm_project.id"
			. " {$this->join} fm_vendor ON $table.spvend_code = fm_vendor.id {$filtermethod}";

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while($this->db->next_record())
			{
				$values[] = array
					(
					'voucher_id'			 => $this->db->f('bilagsnr'),
					'voucher_out_id'		 => $this->db->f('bilagsnr_ut'),
					'id'					 => $this->db->f('id'),
					'art'					 => $this->db->f('artid'),
					'type'					 => $this->db->f('typeid'),
					'dim_a'					 => $this->db->f('dima'),
					'dim_b'					 => $this->db->f('dimb'),
					'dim_d'					 => $this->db->f('dimd'),
					'dim_e'					 => $this->db->f('dime'),
					'tax_code'				 => $this->db->f('mvakode'),
					'invoice_id'			 => $this->db->f('fakturanr'),
					'kid_nr'				 => $this->db->f('kidnr'),
					'vendor_id'				 => $this->db->f('spvend_code'),
					'vendor'				 => $this->db->f('org_name', true),
					'janitor'				 => $this->db->f('oppsynsmannid'),
					'supervisor'			 => $this->db->f('saksbehandlerid'),
					'budget_responsible'	 => $this->db->f('budsjettansvarligid'),
					'invoice_date'			 => $this->db->f('fakturadato'),
					'project_id'			 => $this->db->f('project_id'),
					'project_group'			 => $this->db->f('project_id'),
					'payment_date'			 => $this->db->f('forfallsdato'),
					'merknad'				 => $this->db->f('merknad', true),
					'line_text'				 => $this->db->f('line_text', true),
					'b_account_id'			 => $this->db->f('spbudact_code'),
					'amount'				 => $this->db->f('belop'),
					'approved_amount'		 => $this->db->f('godkjentbelop'),
					'order'					 => $this->db->f('pmwrkord_code'),
					'order_id'				 => $this->db->f('pmwrkord_code'),
					'kostra_id'				 => $this->db->f('kostra_id'),
					'currency'				 => $this->db->f('currency'),
					'process_code'			 => $this->db->f('process_code'),
					'process_log'			 => $this->db->f('process_log', true),
					'oppsynsigndato'		 => $this->db->f('oppsynsigndato'),
					'saksigndato'			 => $this->db->f('saksigndato'),
					'budsjettsigndato'		 => $this->db->f('budsjettsigndato'),
					'charge_tenant'			 => $this->db->f('charge_tenant'),
					'external_ref'			 => $this->db->f('external_ref'),
					'status'				 => $this->db->f('status'),
					'closed'				 => $this->db->f('closed'),
					'parked'				 => $this->db->f('kreditnota'),
					'period'				 => $this->db->f('periode'),
					'periodization'			 => $this->db->f('periodization'),
					'periodization_start'	 => $this->db->f('periodization_start'),
					'continuous'			 => $this->db->f('continuous'),
				);
			}

			/*
			  if($values)
			  {
			  $bilagsnr = (int)$values[0]['voucher_id'];
			  $sql= "SELECT * FROM fm_ecobilag_process_log WHERE bilagsnr = {$bilagsnr}";
			  $this->db->query($sql,__LINE__,__FILE__);
			  $this->db->next_record();
			  $process_log	= $this->db->f('process_log',true);
			  $process_code	= $this->db->f('process_code');

			  foreach ($values as &$line)
			  {
			  $line['process_log'] = $process_log;
			  $line['process_code'] = $process_code;
			  }
			  }
			 */
			//_debug_array($values);
			return $values;
		}

		function update_invoice($values)
		{

			//_debug_array($values);
			$receipt = array();
			foreach($values['counter'] as $n)
			{
				$local_error = '';

				if($values['voucher_id'][$n])
				{
					$voucher_id = $values['voucher_id'][$n];

					$check_value = array('voucher_id' => $voucher_id,
						'sign_orig'	 => $values['sign_orig'][$n],
						'sign'		 => isset($values['sign'][$n]) ? $values['sign'][$n] : '',
						'transfer'	 => isset($values['transfer'][$n]) ? $values['transfer'][$n] : '',
						'kreditnota' => isset($values['kreditnota'][$n]) ? $values['kreditnota'][$n] : '',
						'num_days'	 => $values['num_days'][$n]);

					if($this->check_for_updates($check_value))
					{

						$check_count = $this->check_count($voucher_id);

						if(!($check_count['dima_count'] == $values['invoice_count'][$n]))
						{
							$receipt['error'][]	 = array('msg' => lang('Dima is missing from sub invoice in:') . " " . $values['voucher_id'][$n]);
							$local_error		 = true;
						}

						if(!($check_count['spbudact_code_count'] == $values['invoice_count'][$n]))
						{
							$receipt['error'][]	 = array('msg' => lang('Budget code is missing from sub invoice in :') . " " . $values['voucher_id'][$n]);
							$local_error		 = true;
						}
						else
						{
							$this->db->query("SELECT DISTINCT spbudact_code FROM fm_ecobilag WHERE bilagsnr = '{$values['voucher_id'][$n]}' AND spbudact_code IS NOT NULL",__LINE__,__FILE__);
							$_check_b_accounts = array();
							while ($this->db->next_record())
							{
								$_check_b_accounts[] = $this->db->f('spbudact_code');
							}
							foreach($_check_b_accounts as $_check_b_account)
							{
								if (!$this->check_valid_b_account($_check_b_account))
								{
									$receipt['error'][]	 = array('msg' => lang('this account is not valid:') . " " . $_check_b_account);
									$local_error		 = true;
								}
							}
						}

						if($check_count['dimd_count'] != $check_count['invoice_count'])
						{
							$receipt['error'][]	 = array('msg' => lang('Dim D is mandatory') . ": {$values['voucher_id'][$n]}");
							$local_error		 = true;
						}

						if(!($check_count['kostra_count'] == $values['invoice_count'][$n]))
						{
							$receipt['error'][]	 = array('msg' => 'Tjenestekode mangler for undebilag: ' . " " . $values['voucher_id'][$n]);
							$local_error		 = true;
						}

						if($this->check_claim($voucher_id))
						{
							$receipt['error'][]	 = array('msg' => lang('Tenant claim is not issued for project in voucher %1', $voucher_id));
							$local_error		 = true;
						}

						$blank_date			 = '';
						$sign_field			 = '';
						$sign_id			 = '';
						$sign_date_field	 = '';
						$sign_date			 = '';
						$kommma				 = '';
						$wait_for_kreditnota = '';
						$user_lid			 = $GLOBALS['phpgw_info']['user']['account_lid'];

						if(($values['sign'][$n] == 'sign_none') && ($values['sign_orig'][$n] == 'sign_janitor'))
						{
							$blank_date		 = 'oppsynsigndato= NULL';
							$sign_field		 = '';
							$sign_id		 = '';
							$sign_date_field = '';
							$sign_date		 = '';
							$kommma			 = '';
						}
						else if(($values['sign'][$n] == 'sign_none') && ($values['sign_orig'][$n] == 'sign_supervisor'))
						{
							$blank_date		 = 'saksigndato= NULL';
							$sign_field		 = '';
							$sign_id		 = '';
							$sign_date_field = '';
							$sign_date		 = '';
							$kommma			 = '';
						}
						else if(($values['sign'][$n] == 'sign_none') && ($values['sign_orig'][$n] == 'sign_budget_responsible'))
						{
							$blank_date		 = 'budsjettsigndato= NULL';
							$sign_field		 = '';
							$sign_id		 = '';
							$sign_date_field = '';
							$sign_date		 = '';
							$kommma			 = '';
						}
						else if($values['sign'][$n] == 'sign_janitor' && !$values['sign_orig'][$n])
						{
							$blank_date		 = '';
							$sign_field		 = 'oppsynsmannid=';
							$sign_id		 = "'$user_lid'";
							$sign_date_field = 'oppsynsigndato=';
							$sign_date		 = "'" . date($this->db->datetime_format()) . "'";
							$kommma			 = ",";
						}
						else if($values['sign'][$n] == 'sign_janitor' && $values['sign_orig'][$n] == 'sign_supervisor')
						{
							$blank_date		 = 'saksigndato= NULL';
							$sign_field		 = 'oppsynsmannid=';
							$sign_id		 = "'$user_lid'";
							$sign_date_field = 'oppsynsigndato=';
							$sign_date		 = "'" . date($this->db->datetime_format()) . "'";
							$kommma			 = ",";
						}
						else if($values['sign'][$n] == 'sign_janitor' && $values['sign_orig'][$n] == 'sign_budget_responsible')
						{
							$blank_date		 = 'budsjettsigndato= NULL';
							$sign_field		 = 'oppsynsmannid=';
							$sign_id		 = "'$user_lid'";
							$sign_date_field = 'oppsynsigndato=';
							$sign_date		 = "'" . date($this->db->datetime_format()) . "'";
							$kommma			 = ",";
						}
						else if($values['sign'][$n] == 'sign_supervisor' && !$values['sign_orig'][$n])
						{
							$blank_date		 = '';
							$sign_field		 = 'saksbehandlerid=';
							$sign_id		 = "'$user_lid'";
							$sign_date_field = 'saksigndato=';
							$sign_date		 = "'" . date($this->db->datetime_format()) . "'";
							$kommma			 = ",";
						}
						else if($values['sign'][$n] == 'sign_supervisor' && $values['sign_orig'][$n] == 'sign_janitor')
						{
							$blank_date		 = 'oppsynsigndato= NULL';
							$sign_field		 = 'saksbehandlerid=';
							$sign_id		 = "'$user_lid'";
							$sign_date_field = 'saksigndato=';
							$sign_date		 = "'" . date($this->db->datetime_format()) . "'";
							$kommma			 = ",";
						}
						else if($values['sign'][$n] == 'sign_supervisor' && $values['sign_orig'][$n] == 'sign_budget_responsible')
						{
							$blank_date		 = 'budsjettsigndato= NULL';
							$sign_field		 = 'saksbehandlerid=';
							$sign_id		 = "'$user_lid'";
							$sign_date_field = 'saksigndato=';
							$sign_date		 = "'" . date($this->db->datetime_format()) . "'";
							$kommma			 = ",";
						}
						else if($values['sign'][$n] == 'sign_budget_responsible' && $values['sign_orig'][$n] == 'sign_janitor')
						{
							$blank_date		 = 'oppsynsigndato= NULL';
							$sign_field		 = 'budsjettansvarligid=';
							$sign_id		 = "'$user_lid'";
							$sign_date_field = 'budsjettsigndato=';
							$sign_date		 = "'" . date($this->db->datetime_format()) . "'";
							$kommma			 = ",";
						}
						else if($values['sign'][$n] == 'sign_budget_responsible' && $values['sign_orig'][$n] == 'sign_supervisor')
						{
							$blank_date		 = 'saksigndato= NULL';
							$sign_field		 = 'budsjettansvarligid=';
							$sign_id		 = "'$user_lid'";
							$sign_date_field = 'budsjettsigndato=';
							$sign_date		 = "'" . date($this->db->datetime_format()) . "'";
							$kommma			 = ",";
						}
						else if($values['sign'][$n] == 'sign_budget_responsible' && !$values['sign_orig'][$n])
						{
							$blank_date		 = '';
							$sign_field		 = 'budsjettansvarligid=';
							$sign_id		 = "'$user_lid'";
							$sign_date_field = 'budsjettsigndato=';
							$sign_date		 = "'" . date($this->db->datetime_format()) . "'";
							$kommma			 = ",";
						}


						if($blank_date)
						{
							$kommma_blank = ",";
						}
						else
						{
							$kommma_blank = '';
						}

						$transfer_sign_field = 'utbetalingid=';
						$transfer_date_field = 'utbetalingsigndato=';

						if(!($values['num_days_orig'][$n] == $values['num_days'][$n]))
						{
							$payment_date = date($this->db->date_format(), $values['timestamp_voucher_date'][$n] + (24 * 3600 * $values['num_days'][$n]));
							$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag set forfallsdato= '$payment_date' where bilagsnr='$voucher_id'");
						}

						$transfer_id	 = "Null" . ",";
						$transfer_date	 = "Null";

						if($values['transfer'][$n])
						{
							if($this->check_for_transfer($voucher_id))
							{
								$transfer_id	 = "'$user_lid',";
								$transfer_date	 = "'" . date($this->db->datetime_format()) . "'";
							}
							else
							{
								$receipt['error'][]	 = array('msg' => 'Dette bilaget er ikkje godkjent: ' . " " . $voucher_id);
								$local_error		 = true;
							}
						}

						if($values['kreditnota'][$n])
						{
							$wait_for_kreditnota = 1;
							$transfer_date		 = "Null";
						}
						else
						{
							$wait_for_kreditnota = 'NULL';
						}

						if(!$local_error)
						{
							$sql = "UPDATE fm_ecobilag SET $blank_date $kommma_blank $sign_field $sign_id $kommma $sign_date_field $sign_date $kommma $transfer_sign_field $transfer_id $transfer_date_field $transfer_date ,kreditnota=$wait_for_kreditnota  where bilagsnr='$voucher_id'";
							$GLOBALS['phpgw']->db->transaction_begin();
							$GLOBALS['phpgw']->db->query($sql);
							if($GLOBALS['phpgw']->db->transaction_commit())
							{
								$receipt['message'][] = array('msg' => lang('voucher is updated: ') . $voucher_id);
							}
						}
					}
				}
			}

			$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag set utbetalingid = NULL, utbetalingsigndato = NULL WHERE budsjettsigndato IS NULL");
			if($this->invoice_approval == 2)
			{
				$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag set utbetalingid = NULL, utbetalingsigndato = NULL WHERE oppsynsigndato IS NULL AND saksigndato IS NULL");
			}

			return $receipt;
		}

		function check_for_transfer($voucher_id = '')
		{
			$allow_transfer = false;

			$sql = "SELECT * FROM fm_ecobilag WHERE bilagsnr='$voucher_id'";
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

			$this->db->next_record();

			if($this->invoice_approval == 1)
			{
				if($this->db->f('budsjettsigndato'))
				{
					$allow_transfer = true;
				}
			}
			else
			{
				if($this->db->f('budsjettsigndato') && ($this->db->f('oppsynsigndato') || $this->db->f('saksigndato')))
				{
					$allow_transfer = true;
				}
			}

			return $allow_transfer;
		}

		function check_claim($voucher_id = 0, $line_id = 0)
		{
			$condition = '';

			if($line_id)
			{
				$condition = 'WHERE fm_ecobilag.id =' . (int) $line_id;
			}
			else if($voucher_id)
			{
				$condition = 'WHERE fm_ecobilag.bilagsnr =' . (int) $voucher_id;
			}

			if(!$condition)
			{
				return false;
			}

			$sql = "SELECT count(*) as cnt FROM fm_ecobilag $this->left_join fm_workorder on fm_ecobilag.pmwrkord_code = fm_workorder.id "
			. " {$condition} AND fm_workorder.charge_tenant=1 AND fm_workorder.claim_issued IS NULL";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('cnt');
		}

		public function get_single_line($id, $paid = false)
		{
			$line = $this->read_single_voucher(0, $id, $paid);
			return $line[0];
		}

		public function get_historical_accounting_periods()
		{
			$sql = "SELECT DISTINCT periode FROM fm_ecobilagoverf ORDER BY periode DESC";
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while($this->db->next_record())
			{
				$periode	 = $this->db->f('periode');
				$values[]	 = array
					(
					'id'	 => $periode,
					'name'	 => $periode
				);
			}

			$i = 0;
			foreach($values as &$periode)
			{
				if($i > 5)
				{
					break;
				}
				$sql			 = "SELECT count(id) as cnt FROM fm_ecobilagoverf WHERE periode = {$periode['id']}";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$periode['name'] = $periode['name'] . ' [' . sprintf("%010s", $this->db->f('cnt')) . ']';
				$i ++;
			}
			return $values;
		}

		public function get_historical_transactions_at_periods($data = array())
		{
			if(!$data && !is_array($data))
			{
				return array();
			}

			$filter = 'WHERE periode IN(' . implode(',', $data) . ')';
//			$filter .= ' AND manual_record IS NULL';

			$sql = "SELECT * FROM fm_ecobilagoverf {$filter} ORDER BY periode DESC, id ASC";
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while($this->db->next_record())
			{
				$values[] = $this->db->Record;
			}
			return $values;
		}

		/*
		 * Orders without related historic invoices, tagged as 'delivered'
		 *  - And orders related to active (not processed) invoices.
		 */

		public function get_deposition()
		{
			$sql = "SELECT "
			. "dimb as kostnadssted,"
			. "spbudact_code as art,"
			. "project_group as prosjekt,"
			. "sum(belop) as belop,"
			. "currency"
			. ' FROM fm_workorder'
			. " {$this->join} fm_project ON (fm_workorder.project_id = fm_project.id)"
			. " {$this->join} fm_ecobilag ON (fm_workorder.id = fm_ecobilag.pmwrkord_code)"
			. " GROUP BY art, kostnadssted, project_group, currency ORDER BY kostnadssted,project_group, art, currency ASC";
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			while($this->db->next_record())
			{
				$values[] = $this->db->Record;
			}

			return $values;
		}

		/*
		 *  Forward vouchers to other responsible
		 */

		public function forward($data)
		{
			$condition = '';

			$global_check = false;
			if(isset($data['forward']) && is_array($data['forward']) && isset($data['line_id']) && $data['line_id'])
			{
				$condition = 'WHERE id =' . (int) $data['line_id'];
			}
			else if(isset($data['forward']) && is_array($data['forward']) && isset($data['voucher_id']) && $data['voucher_id'])
			{
				$condition		 = 'WHERE bilagsnr =' . (int) $data['voucher_id'];
				$global_check	 = true;
			}

			$receipt	 = array();
			$local_error = false;
			if($condition)
			{
				//start check
				$check_count = $this->check_count($data['voucher_id']);

				if($global_check)
				{
					if(!($check_count['dima_count'] == $check_count['invoice_count']))
					{
						phpgwapi_cache::message_set(lang('Dima is missing from sub invoice in:') . " " . $data['voucher_id'], 'error');
						$local_error = true;
					}

					if(!($check_count['spbudact_code_count'] == $check_count['invoice_count']))
					{
						phpgwapi_cache::message_set(lang('Budget code is missing from sub invoice in :') . " " . $data['voucher_id'], 'error');
						$local_error = true;
					}

					if(!($check_count['kostra_count'] == $check_count['invoice_count']))
					{
						phpgwapi_cache::message_set('Tjenestekode mangler for undebilag: ' . " " . $data['voucher_id'], 'error');
						$local_error = true;
					}

					if($check_count['dimd_count'] != $check_count['invoice_count'])
					{
						phpgwapi_cache::message_set(lang('Dim D is mandatory') . ": {$data['voucher_id']}", 'error');
						$local_error = true;
					}

					if($this->check_claim($data['voucher_id']))
					{
						phpgwapi_cache::message_set(lang('Tenant claim is not issued for project in voucher %1', $data['voucher_id']), 'error');
						$local_error = true;
					}
				}
				else
				{
					if($this->check_claim(0, $data['line_id']))
					{
						phpgwapi_cache::message_set(lang('Tenant claim is not issued for project in voucher %1', $data['voucher_id']), 'error');
						$local_error = true;
					}
				}


				if($local_error)
				{
					return false;
				}
				// end check

				$value_set = array();

				foreach($data['forward'] as $role => $user_lid)
				{
					$value_set[$role] = $user_lid;
				}

				if($data['approve'] != $data['sign_orig'])
				{
					switch($data['sign_orig'])
					{
						case 'is_janitor':
							$value_set['oppsynsigndato']	 = null;
							break;
						case 'is_supervisor':
							$value_set['saksigndato']		 = null;
							break;
						case 'is_budget_responsible':
							$value_set['budsjettsigndato']	 = null;
							break;
					}

					switch($data['approve'])
					{
						case 'is_janitor':
							$value_set['oppsynsigndato']		 = date($this->db->datetime_format());
							$value_set['oppsynsmannid']			 = $data['my_initials'];
							break;
						case 'is_supervisor':
							$value_set['saksigndato']			 = date($this->db->datetime_format());
							$value_set['saksbehandlerid']		 = $data['my_initials'];
							break;
						case 'is_budget_responsible':
							$value_set['budsjettsigndato']		 = date($this->db->datetime_format());
							$value_set['budsjettansvarligid']	 = $data['my_initials'];
							break;
					}
				}

				if(isset($value_set['budsjettansvarligid']) && !$value_set['budsjettansvarligid'])
				{
					phpgwapi_cache::message_set('Mangler anviser', 'error');
				}
				else
				{
					$value_set = $this->db->validate_update($value_set);
					return $this->db->query("UPDATE fm_ecobilag SET $value_set {$condition}", __LINE__, __FILE__);
				}
			}

			return false;
		}

		function get_order_info($order_id)
		{
			$order_info	 = array();
			$toarray	 = array();
			$order_id	 = (int) $order_id;
			$sql		 = "SELECT fm_workorder.location_code,fm_workorder.vendor_id,fm_workorder.account_id,fm_workorder.ecodimb,fm_workorder.category, fm_workorder.user_id,fm_workorder.title"
			. " FROM fm_workorder {$this->join} fm_project ON fm_workorder.project_id = fm_project.id WHERE fm_workorder.id = {$order_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			if($this->db->next_record())
			{
				$order_info['order_exist'] = true;
			}
			if($this->db->f('location_code'))
			{
				$parts				 = explode('-', $this->db->f('location_code'));
				$order_info['dima']	 = implode('', $parts);
				$order_info['loc1']	 = $parts[0];
			}

			$order_info['vendor_id']	 = $this->db->f('vendor_id');
			$order_info['spbudact_code'] = $this->db->f('account_id');
			$order_info['dimb']			 = $this->db->f('ecodimb');
			$order_info['dime']			 = $this->db->f('category');
			$order_info['title']		 = $this->db->f('title', true);

			$janitor_user_id		 = $this->db->f('user_id');
			$order_info['janitor']	 = $GLOBALS['phpgw']->accounts->get($janitor_user_id)->lid;
			$supervisor_user_id		 = $this->get_default_dimb_role_user(2, $order_info['dimb']);
			if($supervisor_user_id)
			{
				$order_info['supervisor'] = $GLOBALS['phpgw']->accounts->get($supervisor_user_id)->lid;
			}

			$budget_responsible_user_id = $this->get_default_dimb_role_user(3, $order_info['dimb']);
			if($budget_responsible_user_id)
			{
				$order_info['budget_responsible'] = $GLOBALS['phpgw']->accounts->get($budget_responsible_user_id)->lid;
			}

			if(!$order_info['budget_responsible'])
			{
				$order_info['budget_responsible'] = isset($this->config->config_data['import']['budget_responsible']) && $this->config->config_data['import']['budget_responsible'] ? $this->config->config_data['import']['budget_responsible'] : 'karhal';
			}

			$order_info['toarray'] = $toarray;
			return $order_info;
		}

		public function update_voucher_by_changed_order($line_id, $order_id)
		{
			$order_info = $this->get_order_info($order_id);
			if(!$order_info['order_exist'])
			{
				phpgwapi_cache::message_set(lang('not a valid order'), 'error');
				return false;
			}

			$GLOBALS['phpgw']->db->transaction_begin();
			$this->db->query("SELECT * FROM fm_ecobilag WHERE id =" . (int) $line_id, __LINE__, __FILE__);

			$this->db->next_record();
			$old_janitor			 = $this->db->f('oppsynsmannid');
			$old_supervisor			 = $this->db->f('saksbehandlerid');
			$old_budget_responsible	 = $this->db->f('budsjettansvarligid');

			$value_set = array();

			if($old_janitor != $order_info['janitor'])
			{
				$value_set['oppsynsigndato'] = '';
			}

			if($old_supervisor != $order_info['supervisor'])
			{
				$value_set['saksigndato'] = '';
			}

			if($old_budget_responsible != $order_info['budget_responsible'])
			{
				$value_set['budsjettsigndato'] = '';
			}

			$value_set['pmwrkord_code']			 = $order_id;
			$value_set['dima']					 = $order_info['dima'];
			$value_set['dimb']					 = $order_info['dimb'];
			$value_set['dime']					 = $order_info['dime'];
			$value_set['loc1']					 = $order_info['loc1'];
			$value_set['line_text']				 = $order_info['title'];
			$value_set['spbudact_code']			 = $order_info['spbudact_code'];
			$value_set['oppsynsmannid']			 = $order_info['janitor'];
			$value_set['saksbehandlerid']		 = $order_info['supervisor'];
			$value_set['budsjettansvarligid']	 = $order_info['budget_responsible'];
			$value_set['project_id']			 = execMethod('property.soXport.get_project', $order_id);
			$value_set							 = $this->db->validate_update($value_set);
			$this->db->query("UPDATE fm_ecobilag SET $value_set WHERE id =" . (int) $line_id, __LINE__, __FILE__);
			return $GLOBALS['phpgw']->db->transaction_commit();
		}

		public function update_voucher2($data)
		{
			if(!isset($data['line_id']) || !$data['line_id'])
			{
				phpgwapi_cache::message_set(lang('select invoice'), 'error');
				return false;
			}

			$this->db->query("SELECT pmwrkord_code as order_id FROM fm_ecobilag WHERE id = " . (int) $data['line_id'], __LINE__, __FILE__);
			$this->db->next_record();
			if($data['order_id'] != $this->db->f('order_id'))
			{
				if($this->update_voucher_by_changed_order($data['line_id'], $data['order_id']))
				{
					phpgwapi_cache::message_set(lang('voucher info updated from order'), 'message');
					return true;
				}
				else
				{
					phpgwapi_cache::message_set(lang('something went wrong'), 'error');
					return false;
				}
			}

			$GLOBALS['phpgw']->db->transaction_begin();
			$value_set = array();

			$value_set['periode']				 = $data['period'];
			$value_set['periodization']			 = $data['periodization'];
			$value_set['periodization_start']	 = $data['periodization_start'];
			$value_set['kreditnota']			 = !!$data['park_invoice'];

			$value_set = $this->db->validate_update($value_set);
			$this->db->query("UPDATE fm_ecobilag SET $value_set WHERE bilagsnr =" . (int) $data['voucher_id'], __LINE__, __FILE__);
			unset($value_set);

			$value_set_line = array();

			$value_set_line['pmwrkord_code'] = $data['order_id'];
			$value_set_line['dimb']			 = $data['dim_b'];
			$value_set_line['dima']			 = $data['dim_a'];
			$value_set_line['dime']			 = $data['dim_e'];
			$value_set_line['mvakode']		 = $data['tax_code'];
			$value_set_line['project_id']	 = $data['project_group'];
			$value_set_line['spbudact_code'] = $data['b_account_id'];
			$value_set_line['line_text']	 = $this->db->db_addslashes($data['line_text']);
			$value_set_line['process_log']	 = $this->db->db_addslashes($data['process_log']);
			$value_set_line['process_code']	 = $data['process_code'];

			$value_set_line = $this->db->validate_update($value_set_line);
			$this->db->query("UPDATE fm_ecobilag SET {$value_set_line} WHERE id = " . (int) $data['line_id'], __LINE__, __FILE__);
			unset($value_set_line);

			//update workorder
			if($data['order_id'] && $data['b_account_id'])
			{
				$this->db->query("SELECT type FROM fm_orders WHERE id={$data['order_id']}", __LINE__, __FILE__);
				$this->db->next_record();
				switch($this->db->f('type'))
				{
					case 'workorder':
						$value_set_line['account_id']	 = $data['b_account_id'];
						$value_set_line['category']		 = $data['dim_e'];

						$value_set_line = $this->db->validate_update($value_set_line);
						$this->db->query("UPDATE fm_workorder SET {$value_set_line} WHERE id='{$data['order_id']}'");
						unset($value_set_line);
						break;
				}
			}

			foreach($data['approved_amount'] as $line_id => $approved_amount)
			{
				$approved_amount = str_replace(array(' ', ','), array('', '.'), $approved_amount);
				if(is_numeric($approved_amount))
				{
					$this->db->query("UPDATE fm_ecobilag SET godkjentbelop = '$approved_amount' WHERE id = '{$line_id}'", __LINE__, __FILE__);
				}
				else
				{
					phpgwapi_cache::message_set(lang('Not a valid amount'), 'error');
				}
			}

			if(isset($data['split_amount']) && $data['split_amount'] && is_array($data['split_amount']))
			{
				foreach($data['split_amount'] as $id => $split_amount)
				{
					if(!$split_amount)
					{
						continue;
					}

					$split_amount = str_replace(array(' ', ','), array('', '.'), $split_amount);

					if(!is_numeric($split_amount))
					{
						phpgwapi_cache::message_set(lang('Not a valid amount'), 'error');
						continue;
					}

					$table = 'fm_ecobilag';

					$this->db->query("SELECT belop FROM {$table} WHERE id={$id}", __LINE__, __FILE__);
					$this->db->next_record();
					$amount = $this->db->f('belop');

					if($amount > 0)
					{
						if(($amount - $split_amount) <= 0)
						{
							phpgwapi_cache::message_set(lang('negative sum'), 'error');
							continue;
						}
					}
					else
					{
						if(($amount - $split_amount) >= 0)
						{
							phpgwapi_cache::message_set(lang('positive sum'), 'error');
							continue;
						}
					}

					$metadata	 = $this->db->metadata($table);
					$sql		 = "SELECT * FROM {$table} WHERE id= {$id}";
					$this->db->query($sql, __LINE__, __FILE__);
					$this->db->next_record();

					$value_set = array();

					$skip_values = array('id', 'project_id', 'pmwrkord_code', 'dima', 'dime', 'loc1', 'mvakode', 'dimd', 'merknad', 'line_text', 'oppsynsmannid', 'saksbehandlerid', 'oppsynsigndato', 'saksigndato', 'budsjettsigndato', 'process_code', 'process_log');

					foreach($metadata as $_field)
					{
						if(!in_array($_field->name, $skip_values))
						{
							$value_set[$_field->name] = $this->db->f($_field->name, true);
						}
					}

					$this->db->query("INSERT INTO {$table} (" . implode(',', array_keys($value_set)) . ')'
					. ' VALUES (' . $this->db->validate_insert(array_values($value_set)) . ')', __LINE__, __FILE__);

					$new_id = $this->db->get_last_insert_id($table, 'id');


					$value_set	 = array
						(
						'belop'			 => $split_amount,
						'godkjentbelop'	 => $split_amount
					);
					$value_set	 = $this->db->validate_update($value_set);
					$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$id}", __LINE__, __FILE__);

					$value_set	 = array
						(
						'belop'			 => $amount - $split_amount,
						'godkjentbelop'	 => $amount - $split_amount,
						'splitt'		 => $id
					);
					$value_set	 = $this->db->validate_update($value_set);
					$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$new_id}", __LINE__, __FILE__);
				}
			}

			/*
			  if($data['process_log'] || $data['process_code'])
			  {
			  $valueset_log = array
			  (
			  'bilagsnr'		=> $data['voucher_id'],
			  'process_code'	=> $data['process_code'],
			  'process_log'	=> $this->db->db_addslashes($data['process_log']),
			  'user_id'		=> $this->account_id,
			  'entry_date'	=> time(),
			  'modified_date'	=> time()
			  );

			  $sql = "SELECT id FROM fm_ecobilag_process_log WHERE bilagsnr = '{$data['voucher_id']}'";
			  $this->db->query($sql,__LINE__,__FILE__);
			  if($this->db->next_record())
			  {
			  $process_log_id = (int)$this->db->f('id');
			  $valueset_log	= $this->db->validate_update($valueset_log);
			  $this->db->query("UPDATE fm_ecobilag_process_log SET $valueset_log WHERE id = $process_log_id",__LINE__,__FILE__);
			  }
			  else
			  {
			  $cols = implode(',', array_keys($valueset_log));
			  $values	= $this->db->validate_insert($valueset_log);
			  $this->db->query("INSERT INTO fm_ecobilag_process_log ({$cols}) VALUES ({$values})",__LINE__,__FILE__);
			  }
			  }

			 */
			if(isset($data['order_id']) && $data['order_id'])
			{
				if(isset($data['close_order']) && $data['close_order'] && !$data['close_order_orig'])
				{
					execMethod('property.soworkorder.close_orders', array($data['order_id']));
				}
				if(isset($data['close_order_orig']) && $data['close_order_orig'] && !$data['close_order'])
				{
					execMethod('property.soworkorder.reopen_orders', array($data['order_id']));
				}
			}

			$receipt = $this->forward($data);
			phpgwapi_cache::message_set(lang('voucher is updated'), 'message');

			return $GLOBALS['phpgw']->db->transaction_commit();
		}

		public function get_vouchers($data)
		{
			$filtermethod	 = '';
			$querymethod	 = '';
			$where			 = 'WHERE';

			if($data['janitor_lid'])
			{
				if(stripos($data['janitor_lid'], '*') === 0)
				{
					$data['janitor_lid'] = ltrim($data['janitor_lid'], '*');
					$filtermethod .= " $where oppsynsigndato IS NULL";
					$where				 = 'AND';
				}
				$filtermethod .= " $where oppsynsmannid = '{$data['janitor_lid']}'";
				$where = 'AND';
			}

			if($data['supervisor_lid'])
			{
				if(stripos($data['supervisor_lid'], '*') === 0)
				{
					$data['supervisor_lid']	 = ltrim($data['supervisor_lid'], '*');
					//			$filtermethod .= " $where oppsynsigndato IS NOT NULL AND saksigndato IS NULL";
					$filtermethod .= " $where saksigndato IS NULL";
					$where					 = 'AND';
				}

				$filtermethod .= " $where saksbehandlerid = '{$data['supervisor_lid']}'";
				$where = 'AND';
			}

			if($data['budget_responsible_lid'])
			{
				if(stripos($data['budget_responsible_lid'], '*') === 0)
				{
					$data['budget_responsible_lid']	 = ltrim($data['budget_responsible_lid'], '*');
					$filtermethod .= " $where saksigndato IS NOT NULL AND budsjettsigndato IS NULL";
					$where							 = 'AND';
				}
				$filtermethod .= " $where budsjettansvarligid = '{$data['budget_responsible_lid']}'";
				$where = 'AND';
			}

			if($data['query'])
			{
				switch($data['criteria'])
				{
					case 'voucher_id':
						$query		 = (int) $data['query'];
						$querymethod = " $where (bilagsnr = {$query} OR bilagsnr_ut = {$query})";
						break;

					case 'invoice_id':
						$query		 = $data['query'];
						$querymethod = " $where fakturanr = '{$query}'";
						break;

					case 'order_id':
						$query		 = $data['query'];
						$querymethod = " $where pmwrkord_code = '{$query}'";
						break;

					case 'vendor_id':
						$query		 = (int) $data['query'];
						$querymethod = " $where (spvend_code = {$query} OR org_name {$this->like} '%{$data['query']}%')";
						break;

					case 'b_account':
						$query		 = $data['query'];
						$querymethod = " $where spbudact_code = '{$query}'";
						break;

					case 'dimb':
						$query		 = (int) $data['query'];
						$querymethod = " $where dimb = {$query}";
						break;

					default:
				}

				$where = 'AND';
			}

			$sql = "SELECT bilagsnr,bilagsnr_ut, org_name, currency, kreditnota, fm_ecoart.descr as type, godkjentbelop, forfallsdato, oppsynsigndato, saksigndato,budsjettsigndato"
			. " FROM fm_ecobilag"
			. " {$this->join} fm_vendor ON fm_vendor.id = fm_ecobilag.spvend_code"
			. " {$this->join} fm_ecoart ON fm_ecoart.id = fm_ecobilag.artid"
			. " $filtermethod $querymethod ORDER BY forfallsdato ASC, bilagsnr ASC";

			$lang_voucer			 = lang('voucher id');
			$lang_vendor			 = lang('vendor');
			$lang_currency			 = lang('currency');
			$lang_parked			 = lang('parked');
			$lang_type				 = lang('type');
			$lang_approved_amount	 = lang('approved amount');
			$lang_payment_date		 = lang('payment date');

			$this->db->query($sql, __LINE__, __FILE__);
			$values = array();

			while($this->db->next_record())
			{
				$bilagsnr = $this->db->f('bilagsnr');

				$values[$bilagsnr]['bilagsnr_ut']	 = $this->db->f('bilagsnr_ut');
				$values[$bilagsnr]['org_name']		 = $this->db->f('org_name', true);
				$values[$bilagsnr]['currency']		 = $this->db->f('currency');
				$values[$bilagsnr]['kreditnota']	 = $this->db->f('kreditnota');
				$values[$bilagsnr]['type']			 = $this->db->f('type');
				$values[$bilagsnr]['payment_date']	 = $this->db->f('forfallsdato');

				if(isset($values[$bilagsnr]['godkjentbelop']))
				{
					$values[$bilagsnr]['godkjentbelop'] += $this->db->f('godkjentbelop');
				}
				else
				{
					$values[$bilagsnr]['godkjentbelop'] = $this->db->f('godkjentbelop');
				}

				$status = 0;
				if($this->db->f('budsjettsigndato'))
				{
					$status = 3;
				}
				else if($this->db->f('saksigndato'))
				{
					$status = 2;
				}
				else if($this->db->f('oppsynsigndato'))
				{
					$status = 1;
				}

				$values[$bilagsnr]['status'][] = $status;
			}

			$voucers = array();
			foreach($values as $bilagsnr => $entry)
			{
				$payment_date	 = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($entry['payment_date']));
				$status			 = $entry['status'];
				sort($status);

				$voucher_id	 = $entry['bilagsnr_ut'] ? $entry['bilagsnr_ut'] : $bilagsnr;
				$name		 = sprintf("{$lang_payment_date}: % 10s | {$lang_voucer}:% 8s | {$lang_vendor}: % 50s | {$lang_currency}: % 3s | {$lang_parked}: % 1s | {$lang_type}: % 12s | {$lang_approved_amount}: % 19s | Status: % 1s", $payment_date, $voucher_id, trim(strtoupper($entry['org_name'])), $entry['currency'], $entry['kreditnota'] ? 'X' : '', $entry['type'], number_format($entry['godkjentbelop'], 2, ',', ' '), $status[0]
				);

				$voucers[] = array
					(
					'id'	 => $bilagsnr,
					'name'	 => $name
				);
			}

			return $voucers;
		}

		public function get_auto_generated_invoice_num($vendor_id)
		{
			$vendor_id	 = (int) $vendor_id;
			$sql		 = "SELECT max(cast(fakturanr as int)) as invoice_num FROM fm_ecobilagoverf WHERE spvend_code = {$vendor_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$invoice_num = $this->db->f('invoice_num') + 1;
			return $invoice_num;
		}

		public function reassign_order($line_id, $order_id)
		{
			$line_id = (int) $line_id;
			$order_info = $this->get_order_info($order_id); // henter default verdier selv om  $order_id ikke er gyldig.
			$value_set = array();

			if(!$order_id)
			{
				$merknad = 'Mangler bestillingsnummer';
				phpgwapi_cache::message_set(lang('Project is closed'), 'error');
				return false;
			}
			else if (!ctype_digit($order_id))
			{
				$remark = "bestillingsnummeret er på feil format: {$order_id}";
				phpgwapi_cache::message_set($remark, 'error');
				return false;
			}
			else if (!$order_info['order_exist'])
			{
				$remark = 'bestillingsnummeret ikke gyldig: ' . $order_id;
				phpgwapi_cache::message_set($remark, 'error');
				return false;
			}
			else
			{
				$value_set['project_id']			 = execMethod('property.soXport.get_project', $order_id);
			}

			$value_set['pmwrkord_code']		= $order_id;
			$value_set['dima'] 				= $order_info['dima'];
			$value_set['dimb'] 				= $order_info['dimb'];
			$value_set['dime'] 				= $order_info['dime'];
			$value_set['loc1'] 				= $order_info['loc1'];
			$value_set['line_text']			= $order_info['title'];
			$value_set['spbudact_code'] = $order_info['spbudact_code'];
			if(isset($order_info['janitor']) && $order_info['janitor'])
			{
				$value_set['oppsynsmannid'] = $order_info['janitor'];
			}

			if(isset($order_info['supervisor']) && $order_info['supervisor'])
			{
				$value_set['saksbehandlerid']		= $order_info['supervisor'];
			}

			if(isset($order_info['budget_responsible']) && $order_info['budget_responsible'])
			{
				$value_set['budsjettansvarligid']	= $order_info['budget_responsible'];
			}
			$value_set	= $this->db->validate_update($value_set);
			return $this->db->query("UPDATE fm_ecobilag SET $value_set WHERE id = $line_id",__LINE__,__FILE__);

		}

		/**
		 * Check if provided budget account is valid
		 * @param string $b_account_id
		 * @return boolean true on valid budget account
		 */
		function check_valid_b_account($b_account_id)
		{
			$this->db->query("SELECT active FROM fm_b_account WHERE id = '{$b_account_id}'",__LINE__,__FILE__);
			$this->db->next_record();
			return !!$this->db->f('active');
		}
	}	
