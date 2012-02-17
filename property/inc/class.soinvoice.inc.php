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
	* @subpackage eco
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soinvoice
	{
		public $total_records = 0;
		public $sum_amount = 0;
		public $role = array();
		protected $invoice_approval = 2;

		function __construct()
		{
			$this->account_id 	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->acl 			= & $GLOBALS['phpgw']->acl;
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
			$this->config		= CreateObject('phpgwapi.config','property');
			$this->config->read();
			$this->invoice_approval = isset($this->config->config_data['invoice_approval']) && $this->config->config_data['invoice_approval'] ? $this->config->config_data['invoice_approval'] : 2;
		}

		function read_invoice($data)
		{
			$valid_order = array
				(
					'bilagsnr'			=> true,
					'spvend_code'		=> true,
					'fakturadato'		=> true,
					'oppsynsigndato'	=> true,
					'saksigndato'		=> true,
					'budsjettsigndato'	=> true,
					'periode'			=> true
				);

			$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query 			= isset($data['query'])?$data['query']:'';
			$sort 			= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
			$order 			= isset($data['order']) && $valid_order[$data['order']] ? $data['order']:'';
			$cat_id 		= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id']:0;
			$user_lid 		= isset($data['user_lid']) && $data['user_lid']?$data['user_lid']:'none';
			$paid 			= isset($data['paid'])?$data['paid']:'';
			$start_date 	= isset($data['start_date']) && $data['start_date'] ? $data['start_date'] : 0;
			$end_date 		= isset($data['end_date']) && $data['end_date'] ? $data['end_date'] : time();
			$vendor_id 		= isset($data['vendor_id'])?$data['vendor_id']:'';
			$loc1 			= isset($data['loc1'])?$data['loc1']:'';
			$workorder_id 	= isset($data['workorder_id'])?$data['workorder_id']:'';
			$allrows 		= isset($data['allrows'])?$data['allrows']:'';
			$voucher_id 	= isset($data['voucher_id'])?$data['voucher_id']:'';
			$b_account_class= isset($data['b_account_class'])?$data['b_account_class']:'';
			$district_id 	= isset($data['district_id'])?$data['district_id']:'';
			$invoice_id		= $data['invoice_id'] ? $data['invoice_id'] :'';

			$join_tables	= '';
			$filtermethod	= '';
			$querymethod	= '';

			$this->db->query("SELECT * FROM fm_ecoart");
			$art_list = array();
			while ($this->db->next_record())
			{
				$art_list[$this->db->f('id')] = $this->db->f('descr',true);
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' order by bilagsnr DESC';
			}

			$where= 'WHERE';

			if ($user_lid=='none' || !$user_lid)
			{
				return array();
			}
			else if ($user_lid!='all')
			{
				$filtermethod = " WHERE ( oppsynsmannid= '$user_lid' or saksbehandlerid= '$user_lid' or budsjettansvarligid= '$user_lid')";
				$where= 'AND';
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " $where typeid='$cat_id' ";
				$where= 'AND';
			}

			if ($district_id > 0 && $paid)
			{
				$filtermethod .= " $where  district_id='$district_id' ";
				$join_tables = " $this->join fm_location1 ON fm_ecobilagoverf.loc1 = fm_location1.loc1"
					. " $this->join fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id)";
				$where= 'AND';
			}

			if ($vendor_id)
			{
				$filtermethod .= " $where  spvend_code ='{$vendor_id}' ";
				$where= 'AND';
			}
			if ($loc1)
			{
				$filtermethod .= " $where  dima {$this->like} '%$loc1%' ";
				$where= 'AND';
			}

			if ($invoice_id)
			{
				$filtermethod .= " $where fakturanr ='{$invoice_id}'";
				$where= 'AND';
			}

			if ($paid)
			{
				$table = 'fm_ecobilagoverf';

				if ($b_account_class)
				{
					$filtermethod .= " $where  fm_b_account.category ='$b_account_class' ";
					$where= 'AND';
					$join_tables .= " $this->join fm_b_account ON fm_ecobilagoverf.spbudact_code = fm_b_account.id";
				}

				if (!$workorder_id && !$voucher_id && !$invoice_id)
				{
					$start_periode = date('Ym',$start_date);
					$end_periode = date('Ym',$end_date);

					$filtermethod .= " $where (periode >='$start_periode' AND periode <= '$end_periode')";
					$where= 'AND';
				}
			}
			else
			{
				$table ='fm_ecobilag';
			}

			$no_q = false;
			if ($voucher_id)
			{
				$filtermethod = " WHERE bilagsnr = " . (int)$voucher_id . " OR bilagsnr_ut = '{$voucher_id}' OR spvend_code = ". (int)$query;
				$no_q = true;
			}

			if ($workorder_id)
			{
				$filtermethod = " WHERE pmwrkord_code ='$workorder_id' ";
				$no_q = true;
			}

			if($query && !$no_q)
			{
				$query = (int) $query;
				$querymethod = " $where ( spvend_code = {$query} OR bilagsnr = {$query})";
			}

			$sql = "SELECT bilagsnr, bilagsnr_ut, count(bilagsnr) as invoice_count, sum(belop) as belop, sum(godkjentbelop) as godkjentbelop,spvend_code,fakturadato FROM  $table $join_tables $filtermethod $querymethod GROUP BY periode, bilagsnr,bilagsnr_ut,spvend_code,fakturadato,oppsynsigndato,saksigndato,budsjettsigndato";
			$sql2 = "SELECT DISTINCT bilagsnr FROM  $table $join_tables $filtermethod $querymethod";
			_debug_array($sql);
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$sql3 = "SELECT sum(godkjentbelop) as sum_amount FROM  $table $join_tables $filtermethod $querymethod";
			$this->db->query($sql3,__LINE__,__FILE__);
			$this->db->next_record();
			$this->sum_amount		= $this->db->f('sum_amount');


			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$temp = array();
			while ($this->db->next_record())
			{
				$temp[] = array
					(
						'voucher_id'		=> $this->db->f('bilagsnr'),
						'voucher_out_id'	=> $this->db->f('bilagsnr_ut'),
						'invoice_count'		=> $this->db->f('invoice_count'),
						'amount'			=> $this->db->f('belop'),
						'approved_amount'	=> $this->db->f('godkjentbelop')
					);
			}

			$invoice	= array();

			if ($temp)
			{
				$role= $this->check_role();
				$i = 0;
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

					$this->db->query($sql,__LINE__,__FILE__);

					$this->db->next_record();

					$timestamp_voucher_date= mktime(0,0,0,date('m',strtotime($this->db->f('fakturadato'))),date('d',strtotime($this->db->f('fakturadato'))),date('y',strtotime($this->db->f('fakturadato'))));
					$timestamp_payment_date= mktime(0,0,0,date('m',strtotime($this->db->f('forfallsdato'))),date('d',strtotime($this->db->f('forfallsdato'))),date('y',strtotime($this->db->f('forfallsdato'))));

					if($this->db->f('oppsynsmannid') && $this->db->f('oppsynsigndato'))
					{
						$invoice[$i]['jan_date']=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($this->db->f('oppsynsigndato')));
					}
					else
					{
						$invoice[$i]['jan_date']	='';
					}
					if($this->db->f('saksbehandlerid') && $this->db->f('saksigndato'))
					{
						$invoice[$i]['super_date']=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($this->db->f('saksigndato')));
					}
					else
					{
						$invoice[$i]['super_date']	='';
					}

					if($this->db->f('budsjettansvarligid') && $this->db->f('budsjettsigndato'))
					{
						$invoice[$i]['budget_date']=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($this->db->f('budsjettsigndato')));
					}
					else
					{
						$invoice[$i]['budget_date']	='';
					}

					if($this->db->f('utbetalingid') && $this->db->f('utbetalingsigndato'))
					{
						$invoice[$i]['transfer_date']=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($this->db->f('utbetalingsigndato')));
					}
					else
					{
						$invoice[$i]['transfer_date']	='';
					}

					$invoice[$i]['counter']					= $i;
					$invoice[$i]['current_user']			= $GLOBALS['phpgw_info']['user']['account_lid'];
					$invoice[$i]['voucher_id']				= $voucher_id;
					$invoice[$i]['voucher_out_id']			= $invoice_temp['voucher_out_id'];
					$invoice[$i]['invoice_count']			= $invoice_temp['invoice_count'];
					$invoice[$i]['vendor_id']				= $this->db->f('spvend_code');
					$invoice[$i]['vendor']					= $this->db->f('org_name');
					$invoice[$i]['is_janitor']				= $role['is_janitor'];
					$invoice[$i]['is_supervisor']			= $role['is_supervisor'];
					$invoice[$i]['is_budget_responsible']	= $role['is_budget_responsible'];
					$invoice[$i]['is_transfer']				= $role['is_transfer'];
					$invoice[$i]['janitor']					= $this->db->f('oppsynsmannid');
					$invoice[$i]['supervisor']				= $this->db->f('saksbehandlerid');
					$invoice[$i]['budget_responsible']		= $this->db->f('budsjettansvarligid');
					$invoice[$i]['transfer_id']				= $this->db->f('utbetalingid');
					$invoice[$i]['voucher_date'] 			= $GLOBALS['phpgw']->common->show_date($timestamp_voucher_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					$invoice[$i]['payment_date'] 			= $GLOBALS['phpgw']->common->show_date($timestamp_payment_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					$invoice[$i]['period']					= $this->db->f('periode');
					$invoice[$i]['periodization']			= $this->db->f('periodization');
					$invoice[$i]['periodization_start']		= $this->db->f('periodization_start');
					
					$invoice[$i]['type']					= $art_list[$this->db->f('artid')];
					$invoice[$i]['kidnr']					= $this->db->f('kidnr');
					$invoice[$i]['kreditnota']				= $this->db->f('kreditnota');
					$invoice[$i]['currency']				= $this->db->f('currency');
					$invoice[$i]['order_id']				= $this->db->f('pmwrkord_code');
					$invoice[$i]['amount']					= $invoice_temp['amount'];
					$invoice[$i]['approved_amount']			= $invoice_temp['approved_amount'];
					$invoice[$i]['num_days']				= intval(($timestamp_payment_date-$timestamp_voucher_date)/(24*3600));
					$invoice[$i]['timestamp_voucher_date']	= $timestamp_voucher_date;

					if($invoice[$i]['current_user']==$invoice[$i]['janitor'] && $invoice[$i]['jan_date'])
					{
						$invoice[$i]['sign_orig']='sign_janitor';
					}
					else if($invoice[$i]['current_user']==$invoice[$i]['supervisor'] && $invoice[$i]['super_date'])
					{
						$invoice[$i]['sign_orig']='sign_supervisor';
					}
					else if($invoice[$i]['current_user']==$invoice[$i]['budget_responsible'] && $invoice[$i]['budget_date'])
					{
						$invoice[$i]['sign_orig']='sign_budget_responsible';
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
			$start		= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$filter		= isset($data['filter']) ? $data['filter'] : 'none';
			$sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '';
			$voucher_id	= isset($data['voucher_id']) && $data['voucher_id'] ? (int)$data['voucher_id'] : 0;
			$paid		= isset($data['paid']) ? $data['paid'] : '';
			$project_id	= isset($data['project_id']) && $data['project_id'] ? (int)$data['project_id'] : 0;
			$order_id 	= isset($data['order_id']) && $data['order_id'] ? $data['order_id'] : 0 ;//might be bigint

			if ($paid)
			{
				$table = 'fm_ecobilagoverf';
			}
			else
			{
				$table ='fm_ecobilag';
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
					$ordermethod = ' ORDER BY pmwrkord_code DESC, id DESC';					
			}

			$filtermethod = '';
			$where = 'WHERE';

			if ($voucher_id)
			{
				$filtermethod .= " {$where} bilagsnr= '$voucher_id'";
				$where = 'AND';
			}

			if ($order_id)
			{
				$filtermethod .= " {$where} pmwrkord_code= '{$order_id}'";
				$where = 'AND';
			}

			if ($project_id)
			{
				$filtermethod .= " {$where} fm_project.id = '{$project_id}'";
				$where = 'AND';
			}

			$sql = "SELECT $table.*,fm_workorder.status,fm_workorder.charge_tenant,org_name,"
				. "fm_workorder.claim_issued FROM $table"
				. " {$this->left_join} fm_workorder ON fm_workorder.id = $table.pmwrkord_code"
				. " {$this->left_join} fm_project ON fm_workorder.project_id = fm_project.id"
				. " {$this->join} fm_vendor ON $table.spvend_code = fm_vendor.id $filtermethod";

			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$i = 0;

			$closed = isset($this->config->config_data['workorder_closed_status']) && $this->config->config_data['workorder_closed_status'] ? $this->config->config_data['workorder_closed_status'] : 'closed';
			$invoice = array();
			while ($this->db->next_record())
			{
				$invoice[] = array
					(
						'counter'				=> $i,
						'claim_issued'			=> $this->db->f('claim_issued'),
				//		'project_id'			=> $this->db->f('project_id'),
						'workorder_id'			=> $this->db->f('pmwrkord_code'),
						'status'				=> $this->db->f('status'),
						'closed'				=> $this->db->f('status') == $closed,
						'voucher_id'			=> $this->db->f('bilagsnr'),
						'voucher_out_id'		=> $this->db->f('bilagsnr_ut'),
						'id'					=> $this->db->f('id'),
						'invoice_id'			=> $this->db->f('fakturanr'),
						'budget_account'		=> $this->db->f('spbudact_code'),
						'dima'					=> $this->db->f('dima'),
						'dimb'					=> $this->db->f('dimb'),
						'dimd'					=> $this->db->f('dimd'),
						'remark' 				=> !!$this->db->f('merknad'),
						'tax_code'				=> $this->db->f('mvakode'),
						'amount'				=> $this->db->f('belop'),
						'approved_amount'		=> $this->db->f('godkjentbelop'),
						'charge_tenant'			=> $this->db->f('charge_tenant'),
						'vendor'				=> $this->db->f('org_name'),
			//			'paid_percent'			=> $this->db->f('paid_percent'),
						'project_group'			=> $this->db->f('project_id'),
						'external_ref'			=> $this->db->f('external_ref'),
						'currency'				=> $this->db->f('currency'),
						'budget_responsible'	=> $this->db->f('budsjettansvarligid'),
						'budsjettsigndato'		=> $this->db->f('budsjettsigndato'),
						'transfer_time'			=> $this->db->f('overftid'),
					);

				$i++;
			}

			return $invoice;
		}

		function read_invoice_sub_sum($data)
		{
			$start		= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$filter		= isset($data['filter']) ? $data['filter'] : 'none';
			$sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '';
			$voucher_id	= isset($data['voucher_id']) && $data['voucher_id'] ? (int)$data['voucher_id'] : 0;
			$paid		= isset($data['paid']) ? $data['paid'] : '';
			$project_id	= isset($data['project_id']) && $data['project_id'] ? (int)$data['project_id'] : 0;
			$order_id 	= isset($data['order_id']) && $data['order_id'] ? $data['order_id'] : 0 ;//might be bigint

			if ($paid)
			{
				$table = 'fm_ecobilagoverf';
				$overftid = ',overftid';
			}
			else
			{
				$table ='fm_ecobilag';
				$overftid = '';
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

			$filtermethod = '';
			$where = 'WHERE';

			if ($voucher_id)
			{
				$filtermethod .= " {$where} bilagsnr= '$voucher_id'";
				$where = 'AND';
			}

			if ($order_id)
			{
				$filtermethod .= " {$where} pmwrkord_code= '{$order_id}'";
				$where = 'AND';
			}

			if ($project_id)
			{
				$filtermethod .= " {$where} fm_project.id = '{$project_id}'";
				$where = 'AND';
			}

			$groupmethod = "GROUP BY pmwrkord_code,bilagsnr,bilagsnr_ut,fakturanr,"
				. " currency,budsjettansvarligid,org_name";
			
			$sql = "SELECT DISTINCT pmwrkord_code,bilagsnr,bilagsnr_ut,fakturanr,sum(belop) as belop, sum(godkjentbelop) as godkjentbelop,"
				. " currency,budsjettansvarligid,org_name"
				. " FROM $table"
				. " {$this->join} fm_workorder ON fm_workorder.id = $table.pmwrkord_code"
				. " {$this->join} fm_project ON fm_workorder.project_id = fm_project.id"
				. " {$this->join} fm_vendor ON {$table}.spvend_code = fm_vendor.id {$filtermethod} {$groupmethod}";

			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array
				(
					'workorder_id'			=> $this->db->f('pmwrkord_code'),
					'voucher_id'			=> $this->db->f('bilagsnr'),
					'voucher_out_id'		=> $this->db->f('bilagsnr_ut'),
					'invoice_id'			=> $this->db->f('fakturanr'),
					'amount'				=> $this->db->f('belop'),
					'approved_amount'		=> $this->db->f('godkjentbelop'),
					'vendor'				=> $this->db->f('org_name'),
					'currency'				=> $this->db->f('currency'),
					'budget_responsible'	=> $this->db->f('budsjettansvarligid')
				);
			}

			foreach ($values as &$entry)
			{
				$sql = "SELECT budsjettsigndato{$overftid} FROM $table WHERE pmwrkord_code = '{$entry['workorder_id']}' AND bilagsnr = '{$entry['voucher_id']}' AND fakturanr = '{$entry['invoice_id']}'";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
				$entry['budsjettsigndato']	= $this->db->f('budsjettsigndato');
				$entry['transfer_time']		= $this->db->f('overftid');
			}

			return $values;
		}


		function read_consume($data)
		{
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter			= isset($data['filter'])?$data['filter']:'none';
				$query 			= isset($data['query'])?$data['query']:'';
				$sort 			= isset($data['sort'])?$data['sort']:'DESC';
				$order 			= isset($data['order'])?$data['order']:'';
				$cat_id 		= isset($data['cat_id']) && $data['cat_id'] ? (int)$data['cat_id']:0;
				$start_date 	= isset($data['start_date']) && $data['start_date'] ? $data['start_date'] : 0;
				$end_date 		= isset($data['end_date']) && $data['end_date'] ? $data['end_date'] : time();
				$vendor_id 		= isset($data['vendor_id'])?(int)$data['vendor_id']:0;
				$loc1 			= isset($data['loc1'])?$data['loc1']:'';
				$district_id 	= isset($data['district_id'])?(int)$data['district_id']:0;
				$workorder_id 	= isset($data['workorder_id']) && $data['workorder_id'] ? $data['workorder_id']:0;
				$b_account_class = isset($data['b_account_class'])?$data['b_account_class']:'';
				$b_account		= isset($data['b_account']) ? $data['b_account'] : '';
			}

			$where = 'AND';

			if($b_account_class)
			{
				$filtermethod= " $where fm_b_account.category='$b_account_class'";
				$where= 'AND';
			}
			else
			{
				$select_account_class=',fm_b_account.category as b_account_class';
				$group_account_class=', spbudact_code,fm_b_account.category';
			}

			if ($b_account)
			{
				$filtermethod .= " {$where} fm_b_account.id = '{$b_account}'";
				$where= 'AND';
				$select_account_class=',fm_b_account.id as b_account_class';
				$group_account_class=', spbudact_code,fm_b_account.id';

			}


			if ($vendor_id)
			{
				$filtermethod .= " $where (spvend_code = $vendor_id)";
				$where= 'AND';
			}

			if($loc1)
			{
				$filtermethod .=" $where (dima $this->like '%$loc1%')";
				$where= 'AND';
			}


			if ($district_id)
			{
				$filtermethod.= " $where district_id= $district_id ";
				$where= 'AND';
			}

			if ($workorder_id)
			{
				$filtermethod.= " $where pmwrkord_code = '{$workorder_id}'";
				$where= 'AND';
			}


			if ($cat_id>0)
			{
				$filtermethod .= " $where typeid = $cat_id";
				$where= 'AND';
			}

			$start_periode = date('Ym',$start_date);
			$end_periode = date('Ym',$end_date);

			$sql = "SELECT district_id,periode,sum(godkjentbelop) as consume $select_account_class "
				. " FROM  fm_ecobilagoverf $this->join fm_location1 ON (fm_ecobilagoverf.loc1 = fm_location1.loc1) "
				. " $this->join fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id) "
				. " $this->join fm_b_account ON (fm_ecobilagoverf.spbudact_code = fm_b_account.id) "
				. " WHERE (periode >='$start_periode' AND periode <= '$end_periode' $filtermethod )"
				. " GROUP BY district_id,periode $group_account_class"
				. " ORDER BY periode";
			//echo $sql;

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$consume = array();

			while ($this->db->next_record())
			{
				$consume[] = array
					(
						'consume'		=> round($this->db->f('consume')),
						'period'		=> $this->db->f('periode'),
						'district_id'	=> $this->db->f('district_id'),
						'account_class'	=> $b_account_class ? $b_account_class : $this->db->f('b_account_class')
					);	
			}

			return $consume;
		}

		function check_for_updates($values)
		{
			$update=false;

			if($values['sign_orig']!=$values['sign'])
			{
				$update=true;
				return $update;
			}

			$sql = "SELECT * FROM fm_ecobilag WHERE bilagsnr=" . $values['voucher_id'];
			$this->db->limit_query($sql,0,__LINE__,__FILE__,1);

			$this->db->next_record();

			if( ($this->db->f('utbetalingsigndato') && !$values['transfer']) || (!$this->db->f('utbetalingsigndato') && $values['transfer']))
			{
				$update=true;
				return $update;
			}

			if( ($this->db->f('kreditnota') && !$values['kreditnota']) || (!$this->db->f('kreditnota') && $values['kreditnota']) )
			{
				$update=true;
				return $update;
			}

			$timestamp_voucher_date= mktime(0,0,0,date('m',strtotime($this->db->f('fakturadato'))),date('d',strtotime($this->db->f('fakturadato'))),date('y',strtotime($this->db->f('fakturadato'))));
			$timestamp_payment_date= mktime(0,0,0,date('m',strtotime($this->db->f('forfallsdato'))),date('d',strtotime($this->db->f('forfallsdato'))),date('y',strtotime($this->db->f('forfallsdato'))));

			if( ((intval(($timestamp_payment_date-$timestamp_voucher_date)/(24*3600)))!=$values['num_days']) )
			{
				$update=true;
				return $update;
			}
		}

		function update_invoice_sub($values)
		{
			$receipt = array();
			$GLOBALS['phpgw']->db->transaction_begin();

			while($entry=each($values['counter']))
			{
				$local_error='';

				$n=$entry[0];

				//_debug_array($entry);
				$id			= (int)$values['id'][$n];
				$approved_amount = isset($values['approved_amount'][$n]) && $values['approved_amount'][$n] ? str_replace(',', '.', $values['approved_amount'][$n]) : 0;
				if(!$approved_amount || $approved_amount == '00.0')
				{
					$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag SET godkjentbelop = $approved_amount WHERE id='$id'");
					$receipt['message'][] = array('msg'=>lang('Voucher is updated '));
					continue;
				}

				if ($values['budget_account'][$n])
				{
					$budget_account=$values['budget_account'][$n];

					$GLOBALS['phpgw']->db->query("select count(*) as cnt from fm_b_account  where id ='{$budget_account}'");
					$GLOBALS['phpgw']->db->next_record();
					if ($GLOBALS['phpgw']->db->f('cnt') == 0)
					{
						$receipt['error'][] = array('msg'=> lang('This account is not valid:'). " ".$budget_account);
						$local_error= true;
					}
				}
				else
				{
					$receipt['error'][] = array('msg'=>lang('Budget account is missing:'));
					$local_error= true;
				}

				if(!$values['dimd'][$n])
				{
					$dimd_field="dimd=NULL";
				}
				else
				{
					$dimd=$values['dimd'][$n];
					$GLOBALS['phpgw']->db->query("select count(*) as cnt from fm_ecodimd where id ='$dimd'");
					$GLOBALS['phpgw']->db->next_record();
					if ($GLOBALS['phpgw']->db->f('cnt') == 0)
					{
						$receipt['error'][] = array('msg'=>lang('This Dim D is not valid:'). " ".$dimd);
						$local_error= true;
					}

					$dimd_field="dimd="."'" . $dimd . "'";
				}

				if (!$values['dima'][$n])
				{
					$dima_field="dima=NULL";
					$receipt['error'][] = array('msg'=>lang('Dim A is missing'));
					$local_error= true;
				}
				else
				{
					$dima_check=substr($values['dima'][$n],0,4);
					$GLOBALS['phpgw']->db->query("select loc1, kostra_id from fm_location1 where loc1 = '$dima_check' ");
					$GLOBALS['phpgw']->db->next_record();
					if (!$GLOBALS['phpgw']->db->f('loc1'))
					{
						$receipt['error'][] = array('msg'=>lang('This Dim A is not valid:'). " ".$values['dima'][$n]);
						$local_error= true;
					}

					if (!$GLOBALS['phpgw']->db->f('kostra_id') || $GLOBALS['phpgw']->db->f('kostra_id') == 0)
					{
						$receipt['error'][] = array('msg'=>'objektet mangler tjeneste - utgått? '. " ".$values['dima'][$n]);
						$local_error= true;
					}

					//	$dima_field="dima="."'" . $values['dima'][$n] . "'";
					$dima_field="dima="."'" . $values['dima'][$n] . "',loc1=" . "'" . substr($values['dima'][$n],0,4) . "'";

					$kostra_field="kostra_id="."'" . $GLOBALS['phpgw']->db->f('kostra_id') . "'";

				}

				if (! $local_error)
				{
					$tax_code	= (int)$values['tax_code'][$n];
					$dimb		= isset($values['dimb'][$n]) && $values['dimb'][$n] ? (int)$values['dimb'][$n] : 'NULL';
					$workorder_id=$values['workorder_id'][$n];
					if(isset($values['close_order'][$n]) && $values['close_order'][$n] && !$values['close_order_orig'][$n])
					{
						$update_status[$workorder_id]='X';
					}

					if((!isset($values['close_order'][$n]) || !$values['close_order'][$n]) && (isset($values['close_order_orig'][$n]) && $values['close_order_orig'][$n]))
					{
						$update_status[$workorder_id]='R';
					}

/*
					if(isset($values['paid_percent'][$n]) && $values['paid_percent'][$n])
					{
						$update_paid_percent[$workorder_id] = $values['paid_percent'][$n];
					}
*/
					$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag SET $dima_field ,$kostra_field,{$dimd_field}, mvakode = {$tax_code},spbudact_code = '{$budget_account}',dimb = $dimb,godkjentbelop = $approved_amount WHERE id='$id'");

					$receipt['message'][] = array('msg'=>lang('Voucher is updated '));
				}

			}

			if (isset($update_status) AND is_array($update_status))
			{
				$closed = isset($this->config->config_data['workorder_closed_status']) && $this->config->config_data['workorder_closed_status'] ? $this->config->config_data['workorder_closed_status'] : 'closed';
				$reopen = isset($this->config->config_data['workorder_reopen_status']) && $this->config->config_data['workorder_reopen_status'] ? $this->config->config_data['workorder_reopen_status'] : 're_opened';

				$status_code=array('X' => $closed,'R' => $reopen);

				$historylog_workorder	= CreateObject('property.historylog','workorder');

				foreach ($update_status as $id => $entry)
				{
					$this->db->query("SELECT type FROM fm_orders WHERE id={$id}",__LINE__,__FILE__);
					$this->db->next_record();
					switch ( $this->db->f('type') )
					{
					case 'workorder':
						$historylog_workorder->add($entry,$id,$status_code[$entry]);
						$GLOBALS['phpgw']->db->query("UPDATE fm_workorder set status=" . "'$status_code[$entry]'" . "where id=$id");
						$receipt['message'][] = array('msg'=>lang('Workorder %1 is %2',$id, $status_code[$entry]));
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

		function update_single_line($values)
		{
			$table ='fm_ecobilag';
			$id = (int)$values['id'];

			$this->db->transaction_begin();

			// Approval applies to all lines within voucher
			if( $values['approve'] != $values['sign_orig'] )
			{
				switch ( $values['sign_orig'] )
				{
					case 'is_janitor':
						$value_set['oppsynsigndato'] = null;
						break;
					case 'is_supervisor':
						$value_set['saksigndato'] = null;
						break;
					case 'is_budget_responsible':
						$value_set['budsjettsigndato'] = null;
						break;
				}
 
				switch ( $values['approve'] )
				{
					case 'is_janitor':
						$value_set['oppsynsigndato'] = date( $this->db->datetime_format() );
						$value_set['oppsynsmannid'] = $values['my_initials'];
						break;
					case 'is_supervisor':
						$value_set['saksigndato'] = date( $this->db->datetime_format() );
						$value_set['saksbehandlerid'] = $values['my_initials'];
						break;
					case 'is_budget_responsible':
						$value_set['budsjettsigndato'] = date( $this->db->datetime_format() );
						$value_set['budsjettansvarligid'] = $values['my_initials'];
						break;
				}

				$sql ="SELECT bilagsnr FROM {$table} WHERE id= {$id}";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
				$bilagsnr = (int)$this->db->f('bilagsnr');
				$value_set	= $this->db->validate_update($value_set);
				$this->db->query("UPDATE {$table} SET $value_set WHERE bilagsnr= {$bilagsnr}" ,__LINE__,__FILE__);
			}

			$value_set = array
			(
				'godkjentbelop'	=> $values['approved_amount'],
				'project_id'	=> $values['project_group'] ? $values['project_group'] : '',
				'pmwrkord_code'	=> $values['order_id'],
				'process_log'	=> $this->db->db_addslashes($values['process_log']),
				'process_code'	=> $values['process_code'],
			);
			

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$id}" ,__LINE__,__FILE__);

			if(!$values['approved_amount'])
			{
				$this->db->query("UPDATE {$table} SET godkjentbelop = 0 WHERE id= {$id}" ,__LINE__,__FILE__);
			}

			if(isset($values['split_line']) && $values['split_amount'] && isset($values['split_amount']) && $values['split_amount'])
			{
				$metadata = $this->db->metadata($table);
				$sql ="SELECT * FROM {$table} WHERE id= {$id}";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();

				$value_set = array();

				foreach($metadata as $_field)
				{
					if($_field->name != 'id')
					{
						$value_set[$_field->name] = $this->db->f($_field->name,true);
					}
				}

				$this->db->query( "INSERT INTO {$table} (" . implode( ',', array_keys($value_set) ) . ')'
					. ' VALUES (' . $this->db->validate_insert( array_values($value_set) ) . ')',__LINE__,__FILE__);

				$new_id = $this->db->get_last_insert_id($table,'id');

				$this->db->query("SELECT belop FROM {$table} WHERE id={$id}",__LINE__,__FILE__);
				$this->db->next_record();
				$amount = $this->db->f('belop');
				$new_amount = $amount - $values['split_amount'];

				$value_set= array
				(
					'belop'			=> $new_amount,
					'godkjentbelop' => $new_amount,
				);
				$value_set	= $this->db->validate_update($value_set);
				$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$id}" ,__LINE__,__FILE__);

				$value_set= array
				(
					'belop'			=> $values['split_amount'],
					'godkjentbelop' => $values['split_amount'],
				);
				$value_set	= $this->db->validate_update($value_set);
				$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$new_id}" ,__LINE__,__FILE__);
			}

			$this->db->transaction_commit();
		}

		function read_remark($id='',$paid='')
		{
			if ($paid)
			{
				$table = 'fm_ecobilagoverf';
			}
			else
			{
				$table ='fm_ecobilag';
			}

			$this->db->query(" SELECT merknad from $table  where id= '$id'");
			$this->db->next_record();

			return $this->db->f('merknad');
		}

		function check_role()
		{
			if(!isset($this->role) || !$this->role)
			{
				$this->role = array(
					'is_janitor' 				=> $this->acl->check('.invoice', 32, 'property'),
					'is_supervisor' 			=> $this->acl->check('.invoice', 64, 'property'),
					'is_budget_responsible' 	=> $this->acl->check('.invoice', 128, 'property'),
					'is_transfer' 				=> $this->acl->check('.invoice', 16, 'property')
				);
			}
			return $this->role;
		}

		function check_count($voucher_id)
		{

			$this->db->query("SELECT count(id) as invoice_count, count(dima) as dima_count, count(spbudact_code) as spbudact_code_count FROM fm_ecobilag WHERE bilagsnr ='$voucher_id'");
			$this->db->next_record();

			$check_count = array
			(
				'dima_count' 				=> $this->db->f('dima_count'),
				'spbudact_code_count' 		=> $this->db->f('spbudact_code_count'),
				'invoice_count'				=> $this->db->f('invoice_count'),
			);

			$this->db->query("select count(kostra_id) as kostra_count  from fm_ecobilag where bilagsnr ='$voucher_id' and kostra_id > 0");
			$this->db->next_record();
			$check_count['kostra_count'] = $this->db->f('kostra_count');

			return $check_count;
		}


		function update_period($voucher_id='',$period='')
		{
			$receipt = array();
			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_ecobilag set periode='$period' where bilagsnr='$voucher_id'");

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('voucher period is updated'));
			return $receipt;
		}

		function update_periodization($voucher_id='',$periodization='')
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

			$receipt['message'][] = array('msg'=>lang('voucher periodization is updated'));
			return $receipt;
		}

		function update_periodization_start($voucher_id='',$periodization_start='')
		{
			$receipt = array();
			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_ecobilag set periodization_start='$periodization_start' where bilagsnr='$voucher_id'");

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('voucher periodization start is updated'));
			return $receipt;
		}


		function increment_bilagsnr()
		{
			$name = 'Bilagsnummer';
			$now = time();
			$this->db->query("SELECT value, start_date FROM fm_idgenerator WHERE name = '{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$bilagsnr = $this->db->f('value') +1;
			$start_date = (int)$this->db->f('start_date');

			$this->db->query("UPDATE fm_idgenerator SET value = value + 1 WHERE name = '{$name}' AND start_date = $start_date");
			return $bilagsnr;
		}

		function next_bilagsnr()
		{
			$name = 'Bilagsnummer';
			$now = time();
			$this->db->query("SELECT value FROM fm_idgenerator WHERE name = '{$name}' AND start_date < {$now} ORDER BY start_date DESC" );
			$this->db->next_record();
			$bilagsnr = $this->db->f('value')+1;

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
			while ($this->db->next_record())
			{
				$id = $this->db->f('id');
				$values[] = array
				(
					'id'	=> $id,
					'name'	=> $id,
				);
			}
			return $values;
		}


		function get_lisfm_ecoart()
		{
			$this->db->query("SELECT * FROM fm_ecoart order by id asc ");
			$art_list = array();
			while ($this->db->next_record())
			{
				$art_list[] = Array(
					'id'        => $this->db->f('id'),
					'name'       => $this->db->f('descr')
				);
			}

			return $art_list;
		}

		//----------

		function get_type_list()
		{
			$this->db->query("SELECT * FROM fm_ecobilag_category order by id asc ");
			$category = array();
			while ($this->db->next_record())
			{
				$category[] = Array(
					'id'        => $this->db->f('id'),
					'name'       => $this->db->f('descr')
				);
			}
			return $category;
		}

		//----------
		function select_dimb_list()
		{
			$this->db->query("SELECT * FROM fm_ecodimb order by id asc ");
			$dimb_list = array();
			while ($this->db->next_record())
			{
				$dimb_list[] = Array(
					'id'        => $this->db->f('id'),
					'name'       => $this->db->f('descr')
				);
			}
			return $dimb_list;
		}

		//-------------------
		function select_dimd_list()
		{
			$this->db->query("SELECT * FROM fm_ecodimd order by id asc ");
			$dimd_list = array();
			while ($this->db->next_record())
			{
				$dimd_list[] = Array(
					'id'        => $this->db->f('id'),
					'name'       => $this->db->f('descr')
				);
			}
			return $dimd_list;
		}
		//---------------------

		function select_tax_code_list()
		{
			$this->db->query("SELECT * FROM fm_ecomva order by id asc ");
			$tax_code_list = array();
			while ($this->db->next_record())
			{
				$tax_code_list[] = Array(
					'id'        => $this->db->f('id'),
					'name'       => $this->db->f('descr')
				);
			}
			return $tax_code_list;
		}

		function select_account_class()
		{
			$sql = "SELECT id from fm_b_account_category order by id";
			$this->db->query($sql,__LINE__,__FILE__);

			$class = array();
			while ($this->db->next_record())
			{
				$class[] = Array(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('id')
				);
			}
			return $class;
		}

		function delete($bilagsnr)
		{
			$this->db->query("DELETE FROM fm_ecobilag WHERE bilagsnr ='" . $bilagsnr  ."'",__LINE__,__FILE__);
		}

		function read_single_voucher($bilagsnr = 0, $id = 0)
		{
			$bilagsnr =(int)$bilagsnr;
			$id = (int)$id;
			if($bilagsnr)
			{
				$sql = "SELECT * from fm_ecobilag WHERE bilagsnr ='$bilagsnr'";
			}
			else if ($id)
			{
				$sql = "SELECT * from fm_ecobilag WHERE id ='$id'";
			}
			else
			{
				return array();
			}			

			$this->db->query($sql,__LINE__,__FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array
					(
						'id'					=> $this->db->f('id'),
						'art'					=> $this->db->f('artid'),
						'type'					=> $this->db->f('typeid'),
						'dim_a'					=> $this->db->f('dima'),
						'dim_b'					=> $this->db->f('dimb'),
						'dim_d'					=> $this->db->f('dimd'),
						'tax'					=> $this->db->f('mvakode'),
						'invoice_id'			=> $this->db->f('fakturanr'),
						'kid_nr'				=> $this->db->f('kidnr'),
						'vendor_id'				=> $this->db->f('spvend_code'),
						'janitor'				=> $this->db->f('oppsynsmannid'),
						'supervisor'			=> $this->db->f('saksbehandlerid'),
						'budget_responsible'	=> $this->db->f('budsjettansvarligid'),
						'invoice_date' 			=> $this->db->f('fakturadato'),
						'project_id'			=> $this->db->f('project_id'),
						'project_group'			=> $this->db->f('project_id'),
						'payment_date' 			=> $this->db->f('forfallsdato'),
						'merknad'				=> $this->db->f('merknad'),
						'b_account_id'			=> $this->db->f('spbudact_code'),
						'amount'				=> $this->db->f('belop'),
						'approved_amount'		=> $this->db->f('godkjentbelop'),
						'order'					=> $this->db->f('pmwrkord_code'),
						'order_id'				=> $this->db->f('pmwrkord_code'),
						'kostra_id'				=> $this->db->f('kostra_id'),
						'currency'				=> $this->db->f('currency'),
						'process_code'			=> $this->db->f('process_code'),
						'process_log'			=> $this->db->f('process_log',true),
						'oppsynsigndato'		=> $this->db->f('oppsynsigndato'),
						'saksigndato'			=> $this->db->f('saksigndato'),
						'budsjettsigndato'		=> $this->db->f('budsjettsigndato'),
					);
			}



			//_debug_array($values);
			return $values;
		}

		function update_invoice($values)
		{

			//_debug_array($values);
			$receipt = array();
			foreach($values['counter'] as $n)
			{
				$local_error='';

				if($values['voucher_id'][$n])
				{
					$voucher_id=$values['voucher_id'][$n];

					$check_value=array('voucher_id'=>$voucher_id,
						'sign_orig'		=> $values['sign_orig'][$n],
						'sign'			=> isset($values['sign'][$n])?$values['sign'][$n]:'',
						'transfer'		=> isset($values['transfer'][$n])?$values['transfer'][$n]:'',
						'kreditnota'	=> isset($values['kreditnota'][$n])?$values['kreditnota'][$n]:'',
						'num_days'		=> $values['num_days'][$n]);

					if($this->check_for_updates($check_value))
					{

						$check_count = $this->check_count($voucher_id);

						if (!($check_count['dima_count'] == $values['invoice_count'][$n]))
						{
							$receipt['error'][] = array('msg'=>lang('Dima is missing from sub invoice in:'). " ".$values['voucher_id'][$n]);
							$local_error= true;
						}

						if (!($check_count['spbudact_code_count'] == $values['invoice_count'][$n]))
						{
							$receipt['error'][] = array('msg'=>lang('Budget code is missing from sub invoice in :'). " ".$values['voucher_id'][$n]);
							$local_error= true;
						}

						if (!($check_count['kostra_count'] == $values['invoice_count'][$n]))
						{
							$receipt['error'][] = array('msg'=>'Tjenestekode mangler for undebilag: ' . " ".$values['voucher_id'][$n]);
							$local_error= true;
						}

						if ($this->check_claim($voucher_id))
						{
							$receipt['error'][] = array('msg'=>lang('Tenant claim is not issued for project in voucher %1',$voucher_id));
							$local_error= true;
						}

						$blank_date = '';
						$sign_field='';
						$sign_id='';
						$sign_date_field='';
						$sign_date='';
						$kommma='';
						$wait_for_kreditnota='';
						$user_lid	=$GLOBALS['phpgw_info']['user']['account_lid'];

						if (($values['sign'][$n]=='sign_none') && ($values['sign_orig'][$n]=='sign_janitor'))
						{
							$blank_date = 'oppsynsigndato= NULL';
							$sign_field='';
							$sign_id='';
							$sign_date_field='';
							$sign_date='';
							$kommma='';
						}
						else if (($values['sign'][$n]=='sign_none') && ($values['sign_orig'][$n]=='sign_supervisor'))
						{
							$blank_date = 'saksigndato= NULL';
							$sign_field='';
							$sign_id='';
							$sign_date_field='';
							$sign_date='';
							$kommma='';
						}
						else if (($values['sign'][$n]=='sign_none') && ($values['sign_orig'][$n]=='sign_budget_responsible'))
						{
							$blank_date = 'budsjettsigndato= NULL';
							$sign_field='';
							$sign_id='';
							$sign_date_field='';
							$sign_date='';
							$kommma='';
						}
						else if ($values['sign'][$n]=='sign_janitor' && !$values['sign_orig'][$n])
						{
							$blank_date = '';
							$sign_field = 'oppsynsmannid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'oppsynsigndato=';
							$sign_date="'" . date($this->db->datetime_format()) . "'";
							$kommma=",";
						}
						else if ($values['sign'][$n]=='sign_janitor' && $values['sign_orig'][$n]=='sign_supervisor')
						{
							$blank_date = 'saksigndato= NULL';
							$sign_field = 'oppsynsmannid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'oppsynsigndato=';
							$sign_date="'" . date($this->db->datetime_format()) . "'";
							$kommma=",";
						}
						else if ($values['sign'][$n]=='sign_janitor' && $values['sign_orig'][$n]=='sign_budget_responsible')
						{
							$blank_date = 'budsjettsigndato= NULL';
							$sign_field = 'oppsynsmannid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'oppsynsigndato=';
							$sign_date="'" . date($this->db->datetime_format()) . "'";
							$kommma=",";
						}
						else if ($values['sign'][$n]=='sign_supervisor' && !$values['sign_orig'][$n])
						{
							$blank_date = '';
							$sign_field = 'saksbehandlerid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'saksigndato=';
							$sign_date="'" . date($this->db->datetime_format()) . "'";
							$kommma=",";
						}
						else if ($values['sign'][$n]=='sign_supervisor' && $values['sign_orig'][$n]=='sign_janitor')
						{
							$blank_date = 'oppsynsigndato= NULL';
							$sign_field = 'saksbehandlerid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'saksigndato=';
							$sign_date="'" . date($this->db->datetime_format()) . "'";
							$kommma=",";
						}
						else if ($values['sign'][$n]=='sign_supervisor' && $values['sign_orig'][$n]=='sign_budget_responsible')
						{
							$blank_date = 'budsjettsigndato= NULL';
							$sign_field = 'saksbehandlerid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'saksigndato=';
							$sign_date="'" . date($this->db->datetime_format()) . "'";
							$kommma=",";
						}
						else if ($values['sign'][$n]=='sign_budget_responsible' && $values['sign_orig'][$n]=='sign_janitor')
						{
							$blank_date = 'oppsynsigndato= NULL';
							$sign_field = 'budsjettansvarligid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'budsjettsigndato=';
							$sign_date="'" . date($this->db->datetime_format()) . "'";
							$kommma=",";
						}
						else if ($values['sign'][$n]=='sign_budget_responsible' && $values['sign_orig'][$n]=='sign_supervisor')
						{
							$blank_date = 'saksigndato= NULL';
							$sign_field = 'budsjettansvarligid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'budsjettsigndato=';
							$sign_date="'" . date($this->db->datetime_format()) . "'";
							$kommma=",";
						}
						else if ($values['sign'][$n]=='sign_budget_responsible' && !$values['sign_orig'][$n])
						{
							$blank_date = '';
							$sign_field = 'budsjettansvarligid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'budsjettsigndato=';
							$sign_date="'" . date($this->db->datetime_format()) . "'";
							$kommma=",";
						}


						if($blank_date )
						{
							$kommma_blank=",";
						}
						else
						{
							$kommma_blank='';
						}

						$transfer_sign_field='utbetalingid=';
						$transfer_date_field='utbetalingsigndato=';

						if (!($values['num_days_orig'][$n]==$values['num_days'][$n]))
						{
							$payment_date = date($this->db->date_format(),$values['timestamp_voucher_date'][$n]+(24*3600*$values['num_days'][$n]));
							$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag set forfallsdato= '$payment_date' where bilagsnr='$voucher_id'");
						}

						$transfer_id="Null".",";
						$transfer_date="Null";

						if ($values['transfer'][$n])
						{
							if ($this->check_for_transfer($voucher_id))
							{
								$transfer_id="'$user_lid',";
								$transfer_date="'" . date($this->db->datetime_format()) . "'";
							}
							else
							{
								$receipt['error'][] = array('msg'=>'Dette bilaget er ikkje godkjent: ' . " ".$voucher_id);
								$local_error= true;
							}
						}

						if ($values['kreditnota'][$n])
						{
							$wait_for_kreditnota=1;
							$transfer_date="Null";
						}
						else
						{
							$wait_for_kreditnota='NULL';
						}

						if (! $local_error)
						{
							$sql= "UPDATE fm_ecobilag SET $blank_date $kommma_blank $sign_field $sign_id $kommma $sign_date_field $sign_date $kommma $transfer_sign_field $transfer_id $transfer_date_field $transfer_date ,kreditnota=$wait_for_kreditnota  where bilagsnr='$voucher_id'";
							$GLOBALS['phpgw']->db->transaction_begin();
							$GLOBALS['phpgw']->db->query($sql);
							if($GLOBALS['phpgw']->db->transaction_commit())
							{
								$receipt['message'][] = array('msg'=> lang('voucher is updated: ') . $voucher_id);
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

		function check_for_transfer($voucher_id='')
		{
			$allow_transfer=false;

			$sql = "SELECT * FROM fm_ecobilag WHERE bilagsnr='$voucher_id'";
			$this->db->limit_query($sql,0,__LINE__,__FILE__,1);

			$this->db->next_record();

			if($this->invoice_approval == 1)
			{
				if ($this->db->f('budsjettsigndato'))
				{
					$allow_transfer=true;
				}			
			}
			else
			{
				if ($this->db->f('budsjettsigndato') && ($this->db->f('oppsynsigndato') || $this->db->f('saksigndato')))
				{
					$allow_transfer=true;
				}
			}

			return $allow_transfer;
		}

		function check_claim($voucher_id='')
		{
			$sql = "SELECT count(*) as cnt FROM fm_ecobilag $this->left_join fm_workorder on fm_ecobilag.pmwrkord_code = fm_workorder.id "
				. " WHERE bilagsnr='$voucher_id' AND fm_workorder.charge_tenant=1 AND fm_workorder.claim_issued IS NULL";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('cnt');
		}


		public function get_single_line($id)
		{
			$line = $this->read_single_voucher(0, $id);
			return $line[0];
		}

		public function get_historical_accounting_periods()
		{
			$sql = "SELECT DISTINCT periode FROM fm_ecobilagoverf ORDER BY periode DESC";
			$this->db->query($sql,__LINE__,__FILE__);
			
			$values = array();
			while ($this->db->next_record())
			{
				$periode = $this->db->f('periode');
				$values[] = array
				(
					'id'	=> $periode,
					'name'	=> $periode
				);
			}

			$i = 0;
			foreach ($values as &$periode)
			{
				if($i > 5)
				{
					break;
				}
				$sql = "SELECT count(id) as cnt FROM fm_ecobilagoverf WHERE periode = {$periode['id']}";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
				$periode['name'] = $periode['name'] . ' [' . sprintf("%010s",$this->db->f('cnt')) . ']';
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
			$this->db->query($sql,__LINE__,__FILE__);
			
			$values = array();
			while ($this->db->next_record())
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
			. "spbudact_code as art,"
			. "sum(belop) as belop,"
			. "dimb as kostnadssted,"
			. "currency"
			. ' FROM fm_workorder'
			. " {$this->join} fm_project ON (fm_workorder.project_id = fm_project.id)"
			. " {$this->join} fm_ecobilag ON (fm_workorder.id = fm_ecobilag.pmwrkord_code)"
			. " GROUP BY kostnadssted, art, currency ORDER BY kostnadssted, art, currency ASC";
			$this->db->query($sql,__LINE__,__FILE__);
			
			$values = array();
			while ($this->db->next_record())
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
			$receipt = array();
			$local_error= false;
			if(isset($data['forward']) && is_array($data['forward']) && isset($data['voucher_id']) && $data['voucher_id'])
			{
				//start check
				$check_count = $this->check_count($data['voucher_id']);

				if (!($check_count['dima_count'] == $check_count['invoice_count']))
				{
					$receipt['error'][] = array('msg'=>lang('Dima is missing from sub invoice in:'). " ".$data['voucher_id']);
					$local_error= true;
				}

				if (!($check_count['spbudact_code_count'] == $check_count['invoice_count']))
				{
					$receipt['error'][] = array('msg'=>lang('Budget code is missing from sub invoice in :'). " ".$data['voucher_id']);
					$local_error= true;
				}

				if (!($check_count['kostra_count'] == $check_count['invoice_count']))
				{
					$receipt['error'][] = array('msg'=>'Tjenestekode mangler for undebilag: ' . " ".$data['voucher_id']);
					$local_error= true;
				}

				if ($this->check_claim($data['voucher_id']))
				{
					$receipt['error'][] = array('msg'=>lang('Tenant claim is not issued for project in voucher %1',$data['voucher_id']));
					$local_error= true;
				}

				if($local_error)
				{
					return $receipt;
				}
				// end check

				$value_set = array();
				
				foreach ($data['forward'] as $role => $user_lid)
				{
					$value_set[$role] =  $user_lid;
				}

				if( $data['approve'] != $data['sign_orig'] )
				{
					switch ( $data['sign_orig'] )
					{
						case 'is_janitor':
								$value_set['oppsynsigndato'] = null;
							break;
						case 'is_supervisor':
							$value_set['saksigndato'] = null;
							break;
						case 'is_budget_responsible':
							$value_set['budsjettsigndato'] = null;
							break;
					}
 
					switch ( $data['approve'] )
					{
						case 'is_janitor':
							$value_set['oppsynsigndato'] = date( $this->db->datetime_format() );
								$value_set['oppsynsmannid'] = $data['my_initials'];
							break;
						case 'is_supervisor':
							$value_set['saksigndato'] = date( $this->db->datetime_format() );
							$value_set['saksbehandlerid'] = $data['my_initials'];
							break;
						case 'is_budget_responsible':
							$value_set['budsjettsigndato'] = date( $this->db->datetime_format() );
							$value_set['budsjettansvarligid'] = $data['my_initials'];
							break;
					}
				}

				$value_set	= $this->db->validate_update($value_set);
				return $this->db->query("UPDATE fm_ecobilag SET $value_set WHERE bilagsnr = '{$data['voucher_id']}'",__LINE__,__FILE__);
			}

			return false;
		}
	}
