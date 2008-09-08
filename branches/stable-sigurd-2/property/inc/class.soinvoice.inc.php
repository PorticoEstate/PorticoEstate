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
		var $total_records = 0;

		function property_soinvoice()
		{
			$this->bocommon		= CreateObject('property.bocommon');
			$this->account_id 	= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->acl 			= & $GLOBALS['phpgw']->acl;

			$this->join			= $this->bocommon->join;
			$this->left_join	= $this->bocommon->left_join;
			$this->like			= $this->bocommon->like;
			$this->db          	= $this->bocommon->new_db();
		}

		function read_invoice($data)
		{
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query 			= isset($data['query'])?$data['query']:'';
				$sort 			= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order 			= isset($data['order'])?$data['order']:'';
				$cat_id 		= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id']:0;
				$user_lid 		= isset($data['user_lid']) && $data['user_lid']?$data['user_lid']:'none';
				$paid 			= isset($data['paid'])?$data['paid']:'';
				$start_date 	= isset($data['start_date'])?$data['start_date']:'';
				$end_date 		= isset($data['end_date'])?$data['end_date']:'';
				$vendor_id 		= isset($data['vendor_id'])?$data['vendor_id']:'';
				$loc1 			= isset($data['loc1'])?$data['loc1']:'';
				$workorder_id 	= isset($data['workorder_id'])?$data['workorder_id']:'';
				$allrows 		= isset($data['allrows'])?$data['allrows']:'';
				$voucher_id 	= isset($data['voucher_id'])?$data['voucher_id']:'';
				$b_account_class= isset($data['b_account_class'])?$data['b_account_class']:'';
				$district_id 	= isset($data['district_id'])?$data['district_id']:'';
			}
			$join_tables	= '';
			$filtermethod	= '';
			$querymethod	= '';
//_debug_array($data);

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by bilagsnr DESC';
			}

			$where= 'WHERE';

			if ($user_lid=='none' || !$user_lid):
			{
				return;
			}
			elseif ($user_lid!='all'):
			{
				$filtermethod = " WHERE ( oppsynsmannid= '$user_lid' or saksbehandlerid= '$user_lid' or budsjettansvarligid= '$user_lid')";
				$where= 'AND';
			}
			endif;

			if ($cat_id > 0)
			{
				$filtermethod .= " $where typeid='$cat_id' ";
				$where= 'AND';
			}

			if ($district_id > 0)
			{
				$filtermethod .= " $where  district_id='$district_id' ";
				$join_tables = " $this->join fm_location1 ON fm_ecobilagoverf.loc1 = fm_location1.loc1"
						. " $this->join fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id)";
				$where= 'AND';
			}

			if ($vendor_id)
			{
				$filtermethod .= " $where  spvend_code ='$vendor_id' ";
				$where= 'AND';
			}
			if ($loc1)
			{
				$filtermethod .= " $where  dima $this->like '%$loc1%' ";
				$where= 'AND';
			}
			if ($workorder_id)
			{
				$filtermethod .= " $where  pmwrkord_code ='$workorder_id' ";
				$where= 'AND';
			}


			if ($voucher_id)
			{
				$filtermethod .= " $where  bilagsnr $this->like '%$voucher_id%' ";
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

				if (!$workorder_id && !$voucher_id)
				{
					$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					$dateformat = str_replace(".","",$dateformat);
					$dateformat = str_replace("-","",$dateformat);
					$dateformat = str_replace("/","",$dateformat);
					$y=strpos($dateformat,'y');
					$d=strpos($dateformat,'d');
					$m=strpos($dateformat,'m');

	 				$dateparts = explode('/', $start_date);
	 				$sday = $dateparts[$d];
	 				$smonth = $dateparts[$m];
	 				$syear = $dateparts[$y];

		 			$dateparts = explode('/', $end_date);
	 				$eday = $dateparts[$d];
	 				$emonth = $dateparts[$m];
	 				$eyear = $dateparts[$y];

					$start_date = date($this->bocommon->dateformat,mktime(2,0,0,$smonth,$sday,$syear));
					if ($syear == $eyear)
					{
						$end_date = date($this->bocommon->dateformat,mktime(2,0,0,12,31,$eyear));
						$filtermethod .= " $where (fakturadato >'$start_date' AND fakturadato < '$end_date'"
								. " AND periode >" . ($smonth -1) . " AND periode <" . ($emonth+1) . ')';
					}

					$end_date = date($this->bocommon->dateformat,mktime(2,0,0,$emonth,$eday,$eyear));


		/*			$filtermethod .= " $where (fakturadato >'$start_date' AND fakturadato < '$end_date'";

					if($smonth < 3)
					{
						$filtermethod .= " AND periode = 1)";
					}
					else
					{
						$filtermethod .= ")";
					}
		*/
				}
			}
			else
			{
				$table ='fm_ecobilag';
			}

			if($query)
			{
				$query = (int) $query;
				$querymethod = " $where ( spvend_code = {$query} OR bilagsnr = {$query})";
			}

			$sql = "SELECT bilagsnr, count(bilagsnr) as invoice_count, sum(belop) as belop,spvend_code,fakturadato FROM  $table $join_tables $filtermethod $querymethod group by bilagsnr,spvend_code,fakturadato ";
			$sql2 = "SELECT DISTINCT bilagsnr FROM  $table $join_tables $filtermethod $querymethod";

//echo $sql;
			$this->db->query($sql2,__LINE__,__FILE__);
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
				$temp[] = array
				(
					'voucher_id'		=> $this->db->f('bilagsnr'),
					'invoice_count'		=> $this->db->f('invoice_count'),
					'amount'		=> $this->db->f('belop')
					);
			}

			$invoice = array();
			if (isset($temp) && $temp)
			{
				$role= $this->check_role();
				$i = 0;
				foreach($temp as $invoice_temp)
				{
					$voucher_id=$invoice_temp['voucher_id'];

					$sql = "SELECT spvend_code,oppsynsmannid,saksbehandlerid,budsjettansvarligid,"
					. " utbetalingid,oppsynsigndato,saksigndato,budsjettsigndato,utbetalingsigndato,fakturadato,org_name,"
					. " forfallsdato,periode,artid,kidnr,kreditnota "
					. " from $table $this->join fm_vendor ON fm_vendor.id = $table.spvend_code WHERE bilagsnr = $voucher_id "
					. " group by bilagsnr,spvend_code,oppsynsmannid,saksbehandlerid,budsjettansvarligid,"
					. " utbetalingid,oppsynsigndato,saksigndato,budsjettsigndato,utbetalingsigndato,fakturadato,org_name,"
					. " forfallsdato,periode,artid,kidnr,kreditnota ";

					$this->db->query($sql,__LINE__,__FILE__);

					$this->db->next_record();

					$timestamp_voucher_date= mktime(0,0,0,date('m',strtotime($this->db->f('fakturadato'))),date('d',strtotime($this->db->f('fakturadato'))),date('y',strtotime($this->db->f('fakturadato'))));
					$timestamp_payment_date= mktime(0,0,0,date('m',strtotime($this->db->f('forfallsdato'))),date('d',strtotime($this->db->f('forfallsdato'))),date('y',strtotime($this->db->f('forfallsdato'))));

					if($this->db->f('oppsynsmannid') && $this->db->f('oppsynsigndato'))
					{
//						$timestamp_jan_date			= mktime(0,0,0,date(m,strtotime($this->db->f('oppsynsigndato'))),date(d,strtotime($this->db->f('oppsynsigndato'))),date(y,strtotime($this->db->f('oppsynsigndato'))));
//						$invoice[$i]['jan_date']	= $GLOBALS['phpgw']->common->show_date($timestamp_jan_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
						$invoice[$i]['jan_date']=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($this->db->f('oppsynsigndato')));
					}
					else
					{
						$invoice[$i]['jan_date']	='';
					}
					if($this->db->f('saksbehandlerid') && $this->db->f('saksigndato'))
					{
//						$timestamp_super_date		= mktime(0,0,0,date(m,strtotime($this->db->f('saksigndato'))),date(d,strtotime($this->db->f('saksigndato'))),date(y,strtotime($this->db->f('saksigndato'))));
//						$invoice[$i]['super_date']	= $GLOBALS['phpgw']->common->show_date($timestamp_super_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
						$invoice[$i]['super_date']=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($this->db->f('saksigndato')));
					}
					else
					{
						$invoice[$i]['super_date']	='';
					}

					if($this->db->f('budsjettansvarligid') && $this->db->f('budsjettsigndato'))
					{
//							$timestamp_budget_date		= mktime(0,0,0,date(m,strtotime($this->db->f('budsjettsigndato'))),date(d,strtotime($this->db->f('budsjettsigndato'))),date(y,strtotime($this->db->f('budsjettsigndato'))));
//							$invoice[$i]['budget_date']	= $GLOBALS['phpgw']->common->show_date($timestamp_budget_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
							$invoice[$i]['budget_date']=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($this->db->f('budsjettsigndato')));
					}
					else
					{
						$invoice[$i]['budget_date']	='';
					}

					if($this->db->f('utbetalingid') && $this->db->f('utbetalingsigndato'))
					{
//						$timestamp_transfer_date= mktime(0,0,0,date(m,strtotime($this->db->f('utbetalingsigndato'))),date(d,strtotime($this->db->f('utbetalingsigndato'))),date(y,strtotime($this->db->f('utbetalingsigndato'))));
//						$invoice[$i]['transfer_date']			= $GLOBALS['phpgw']->common->show_date($timestamp_transfer_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
						$invoice[$i]['transfer_date']=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($this->db->f('utbetalingsigndato')));
					}
					else
					{
						$invoice[$i]['transfer_date']	='';
					}

					$invoice[$i]['counter']					= $i;
					$invoice[$i]['current_user']			= $GLOBALS['phpgw_info']['user']['account_lid'];
					$invoice[$i]['voucher_id']				= $voucher_id;
					$invoice[$i]['invoice_count']			= $invoice_temp['invoice_count'];
					$invoice[$i]['vendor_id']				= $this->db->f('spvend_code');
					$invoice[$i]['vendor']					= $this->db->f('org_name');
					$invoice[$i]['is_janitor']				= $role['is_janitor'];
					$invoice[$i]['is_supervisor']			= $role['is_supervisor'];
					$invoice[$i]['is_budget_responsible']	= $role['is_budget_responsible'];
					$invoice[$i]['is_janitor']				= $role['is_janitor'];
					$invoice[$i]['is_transfer']				= $role['is_transfer'];
					$invoice[$i]['janitor']					= $this->db->f('oppsynsmannid');
					$invoice[$i]['supervisor']				= $this->db->f('saksbehandlerid');
					$invoice[$i]['budget_responsible']		= $this->db->f('budsjettansvarligid');
					$invoice[$i]['transfer_id']				= $this->db->f('utbetalingid');
					$invoice[$i]['voucher_date'] 			= $GLOBALS['phpgw']->common->show_date($timestamp_voucher_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					$invoice[$i]['payment_date'] 			= $GLOBALS['phpgw']->common->show_date($timestamp_payment_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					$invoice[$i]['period']					= $this->db->f('periode');
					$invoice[$i]['type']					= $this->db->f('artid');
					$invoice[$i]['kidnr']					= $this->db->f('kidnr');
					$invoice[$i]['kreditnota']				= $this->db->f('kreditnota');
					$invoice[$i]['amount']					= $invoice_temp['amount'];
					$invoice[$i]['num_days']				= intval(($timestamp_payment_date-$timestamp_voucher_date)/(24*3600));
					$invoice[$i]['timestamp_voucher_date']	= $timestamp_voucher_date;

					if($invoice[$i]['current_user']==$invoice[$i]['janitor'] && $invoice[$i]['jan_date']):
					{
						$invoice[$i]['sign_orig']='sign_janitor';
					}
					elseif($invoice[$i]['current_user']==$invoice[$i]['supervisor'] && $invoice[$i]['super_date']):
					{
						$invoice[$i]['sign_orig']='sign_supervisor';
					}
					elseif($invoice[$i]['current_user']==$invoice[$i]['budget_responsible'] && $invoice[$i]['budget_date']):
					{
						$invoice[$i]['sign_orig']='sign_budget_responsible';
					}
					endif;

					$i++;

				}
			}
//_debug_array($invoice);
//_debug_array($invoice_temp);

			return $invoice;

		}


		function read_invoice_sub($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter		= isset($data['filter']) ? $data['filter'] : 'none';
				$sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
				$order		= isset($data['order']) ? $data['order'] : '';
				$voucher_id	= isset($data['voucher_id']) && $data['voucher_id'] ? $data['voucher_id'] : 0;
				$paid		= isset($data['paid']) ? $data['paid'] : '';
			}

			if ($paid)
			{
				$table = 'fm_ecobilagoverf';
			}
			else
			{
				$table ='fm_ecobilag';
			}


			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by bilagsnr DESC';
			}

			if ($voucher_id)
			{
				$filtermethod = " WHERE ( bilagsnr= '$voucher_id')";
			}

			$sql = "SELECT $table.*,fm_workorder.status,fm_workorder.charge_tenant,org_name,fm_workorder.claim_issued FROM $table "
			. " $this->left_join fm_workorder on fm_workorder.id = $table.pmwrkord_code  "
			. " $this->join fm_vendor ON $table.spvend_code = fm_vendor.id $filtermethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();
			$this->db->query($sql . $ordermethod,$start,__LINE__,__FILE__);

			$i = 0;

			while ($this->db->next_record())
			{
				$invoice[$i]['counter']					= $i;
				$invoice[$i]['claim_issued']			= $this->db->f('claim_issued');
				$invoice[$i]['project_id']				= $this->db->f('project_id');
				$invoice[$i]['workorder_id']			= $this->db->f('pmwrkord_code');
				$invoice[$i]['status']					= $this->db->f('status');
				if ($this->db->f('status')=='closed')
				{
					$invoice[$i]['closed']				= true;
				}
				$invoice[$i]['voucher_id']				= $voucher_id;
				$invoice[$i]['id']						= $this->db->f('id');
				$invoice[$i]['invoice_id']				= $this->db->f('fakturanr');
				$invoice[$i]['budget_account']			= $this->db->f('spbudact_code');
				$invoice[$i]['dima']					= $this->db->f('dima');
				$invoice[$i]['dimb']					= $this->db->f('dimb');
				$invoice[$i]['dimd']					= $this->db->f('dimd');
				if ($this->db->f('merknad'))
				{
					$invoice[$i]['remark']				= true;
				}
				$invoice[$i]['tax_code']				= $this->db->f('mvakode');
				$invoice[$i]['amount']					= $this->db->f('belop');
				$invoice[$i]['charge_tenant']			= $this->db->f('charge_tenant');
				$invoice[$i]['vendor']					= $this->db->f('org_name');
				$i++;
			}

			return $invoice;
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
				$cat_id 		= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id']:0;
				$start_date 	= isset($data['start_date'])?$data['start_date']:'';
				$end_date 		= isset($data['end_date'])?$data['end_date']:'';
				$vendor_id 		= isset($data['vendor_id'])?$data['vendor_id']:'';
				$loc1 			= isset($data['loc1'])?$data['loc1']:'';
				$district_id 	= isset($data['district_id'])?$data['district_id']:'';
				$workorder_id 	= isset($data['workorder_id']) && $data['workorder_id'] ? $data['workorder_id']:0;
				$b_account_class = isset($data['b_account_class'])?$data['b_account_class']:'';
			}
//_debug_array($data);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$dateformat = str_replace(".","",$dateformat);
			$dateformat = str_replace("-","",$dateformat);
			$dateformat = str_replace("/","",$dateformat);
			$y=strpos($dateformat,'y');
			$d=strpos($dateformat,'d');
			$m=strpos($dateformat,'m');
 			$dateparts = explode('/', $start_date);
 			$sday = $dateparts[$d];
 			$smonth = $dateparts[$m];
 			$syear = $dateparts[$y];

 			$dateparts = explode('/', $end_date);
 			$eday = $dateparts[$d];
 			$emonth = $dateparts[$m];
 			$eyear = $dateparts[$y];

			$start_date = date($this->bocommon->dateformat,mktime(2,0,0,$smonth,$sday,$syear));
			$end_date = date($this->bocommon->dateformat,mktime(2,0,0,$emonth,$eday,$eyear));


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
				$filtermethod.= " $where pmwrkord_code = $workorder_id";
				$where= 'AND';
			}


			if ($cat_id>0)
			{
				$filtermethod .= " $where typeid = $cat_id";
				$where= 'AND';
			}


			$year = date("Y",strtotime($start_date));
			$month = date("m",strtotime($start_date));
			if($month < 3)
			{
				$start_date = date($this->bocommon->dateformat,mktime(2,0,0,3,1,$year));
			}

			$start_date2 = date($this->bocommon->dateformat,mktime(2,0,0,1,1,$year));

			$sql = "SELECT district_id,periode,sum(godkjentbelop) as consume $select_account_class "
				. " FROM  fm_ecobilagoverf $this->join fm_location1 ON (fm_ecobilagoverf.loc1 = fm_location1.loc1) "
				. " $this->join fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id) "
				. " $this->join fm_b_account ON (fm_ecobilagoverf.spbudact_code = fm_b_account.id) "
		        	. " WHERE (fakturadato >'$start_date' AND fakturadato < '$end_date' $filtermethod )"
		        	. " OR (fakturadato >'$start_date2' AND fakturadato < '$end_date'  AND periode < 3  $filtermethod) "
		        	. " GROUP BY district_id,periode $group_account_class"
		        	. " ORDER BY periode";
//echo $sql;

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();
			$this->db->query($sql . $ordermethod,$start,__LINE__,__FILE__);

			$i = 0;

			while ($this->db->next_record())
			{
				$consume[$i]['consume']				= round($this->db->f('consume'));
				$consume[$i]['period']					= $this->db->f('periode');
				$consume[$i]['district_id']				= $this->db->f('district_id');
				if(!$b_account_class)
				{
					$consume[$i]['account_class']			= $this->db->f('b_account_class');
				}
				else
				{
					$consume[$i]['account_class']			= $b_account_class;
				}

				$i++;
			}
//_debug_array($consume);
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

			$GLOBALS['phpgw']->db->transaction_begin();

			while($entry=each($values['counter']))
				{
					$local_error='';

					$n=$entry[0];


//_debug_array($entry);


				if ($values['budget_account'][$n])
				{
					$budget_account=$values['budget_account'][$n];

					$GLOBALS['phpgw']->db->query("select count(*) from fm_b_account  where id ='{$budget_account}'");
					$GLOBALS['phpgw']->db->next_record();
					if ($GLOBALS['phpgw']->db->f(0) == 0)
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
					$GLOBALS['phpgw']->db->query("select count(*) from fm_ecodimd where id ='$dimd'");
					$GLOBALS['phpgw']->db->next_record();
					if ($GLOBALS['phpgw']->db->f(0) == 0)
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
					$id			= $values['id'][$n];
					$tax_code	= $values['tax_code'][$n];
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

					$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag set $dima_field ,$kostra_field,$dimd_field, mvakode = '$tax_code',spbudact_code = '$budget_account',dimb = $dimb where id='$id'");

					$receipt['message'][] = array('msg'=>lang('Voucher is updated '));
				}

			}

			if (isset($update_status) AND is_array($update_status))
			{
				$status_code=array('X'=>'closed','R'=>'re_opened');

				$historylog_workorder	= CreateObject('property.historylog','workorder');

				while (list($id,$entry) = each($update_status))
				{
					$historylog_workorder->add($entry,$id,$status_code[$entry]);
					$GLOBALS['phpgw']->db->query("UPDATE fm_workorder set status=" . "'$status_code[$entry]'" . "where id=$id");
					$receipt['message'][] = array('msg'=>lang('Workorder %1 is %2',$id, $status_code[$entry]));
				}
			}

			$GLOBALS['phpgw']->db->transaction_commit();

			return $receipt;
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
				$this->role=array(
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

			$this->db->query("select count(dima) as dima_count , count(spbudact_code) as spbudact_code_count from fm_ecobilag where bilagsnr ='$voucher_id'");
			$this->db->next_record();

			$check_count=array(
				'dima_count' 				=> $this->db->f('dima_count'),
				'spbudact_code_count' 		=> $this->db->f('spbudact_code_count')
				);

			$this->db->query("select count(kostra_id) as kostra_count  from fm_ecobilag where bilagsnr ='$voucher_id' and kostra_id > 0");
			$this->db->next_record();
			$check_count['kostra_count'] = $this->db->f('kostra_count');

			return $check_count;
		}


		function update_period($voucher_id='',$period='')
		{
			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_ecobilag set periode='$period' where bilagsnr='$voucher_id'");

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('voucher period is updated'));
			return $receipt;
		}


		function increment_bilagsnr()
		{

			$this->db->query("UPDATE fm_idgenerator set value = value + 1 where name = 'Bilagsnummer'");
			$this->db->query("select value from fm_idgenerator where name = 'Bilagsnummer'");
			$this->db->next_record();
			$bilagsnr = $this->db->f('value');
			return $bilagsnr;

		}

		function next_bilagsnr()
		{

			$this->db->query("select value from fm_idgenerator where name = 'Bilagsnummer'");
			$this->db->next_record();
			$bilagsnr = $this->db->f('value')+1;

			return $bilagsnr;
		}

		function check_vendor($vendor_id)
		{

			$this->db->query("select count(*) from fm_vendor where id='$vendor_id'");
			$this->db->next_record();
			return $this->db->f(0);
		}


		function tax_code_list($selected='')
		{
			$this->db->query("SELECT * FROM fm_ecomva order by id asc ");
			while ($this->db->next_record())
			{
				$tax_code_list[] = Array(
					'id'        => $this->db->f('id'),
				);
			}

			return $tax_code_list;
		}


		function get_lisfm_ecoart()
		{
			$this->db->query("SELECT * FROM fm_ecoart order by id asc ");
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
		function select_dimb_list()
		{
			$this->db->query("SELECT * FROM fm_ecodimb order by id asc ");
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

		function read_single_voucher($bilagsnr)
		{
			$sql = "SELECT * from fm_ecobilag WHERE bilagsnr ='$bilagsnr'";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$values[] = Array(

				'location_code'		=> $this->db->f('id'),
				'art'			=> $this->db->f('artid'),
				'type'			=> $this->db->f('typeid'),
				'dim_a'			=> $this->db->f('dima'),
				'dim_b'			=> $this->db->f('dimb'),
				'dim_d'			=> $this->db->f('dimd'),
				'tax'			=> $this->db->f('mvakode'),
				'invoice_id'		=> $this->db->f('fakturanr'),
				'kid_nr'		=> $this->db->f('kidnr'),
				'vendor_id'		=> $this->db->f('spvend_code'),
				'janitor'		=> $this->db->f('oppsynsmannid'),
				'supervisor'		=> $this->db->f('saksbehandlerid'),
				'budget_responsible'	=> $this->db->f('budsjettansvarligid'),
				'invoice_date' 		=> $this->db->f('fakturadato'),
				'project_id'		=> $this->db->f('project_id'),
				'payment_date' 		=> $this->db->f('forfallsdato'),
				'merknad'		=> $this->db->f('merknad'),
				'b_account_id'		=> $this->db->f('spbudact_code'),
				'amount'		=> $this->db->f('belop'),
				'order'			=> $this->db->f('pmwrkord_code'),
				'kostra_id'		=> $this->db->f('kostra_id'),
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

						if (($values['sign'][$n]=='sign_none') && ($values['sign_orig'][$n]=='sign_janitor')):
							$blank_date = 'oppsynsigndato= NULL';
							$sign_field='';
							$sign_id='';
							$sign_date_field='';
							$sign_date='';
							$kommma='';
						elseif (($values['sign'][$n]=='sign_none') && ($values['sign_orig'][$n]=='sign_supervisor')):
							$blank_date = 'saksigndato= NULL';
							$sign_field='';
							$sign_id='';
							$sign_date_field='';
							$sign_date='';
							$kommma='';
						elseif (($values['sign'][$n]=='sign_none') && ($values['sign_orig'][$n]=='sign_budget_responsible')):
							$blank_date = 'budsjettsigndato= NULL';
							$sign_field='';
							$sign_id='';
							$sign_date_field='';
							$sign_date='';
							$kommma='';
						elseif ($values['sign'][$n]=='sign_janitor' && !$values['sign_orig'][$n]):
							$blank_date = '';
							$sign_field = 'oppsynsmannid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'oppsynsigndato=';
							$sign_date="'" . date($this->bocommon->datetimeformat) . "'";
							$kommma=",";
						elseif ($values['sign'][$n]=='sign_janitor' && $values['sign_orig'][$n]=='sign_supervisor'):
							$blank_date = 'saksigndato= NULL';
							$sign_field = 'oppsynsmannid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'oppsynsigndato=';
							$sign_date="'" . date($this->bocommon->datetimeformat) . "'";
							$kommma=",";
						elseif ($values['sign'][$n]=='sign_janitor' && $values['sign_orig'][$n]=='sign_budget_responsible'):
							$blank_date = 'budsjettsigndato= NULL';
							$sign_field = 'oppsynsmannid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'oppsynsigndato=';
							$sign_date="'" . date($this->bocommon->datetimeformat) . "'";
							$kommma=",";
						elseif ($values['sign'][$n]=='sign_supervisor' && !$values['sign_orig'][$n]):
							$blank_date = '';
							$sign_field = 'saksbehandlerid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'saksigndato=';
							$sign_date="'" . date($this->bocommon->datetimeformat) . "'";
							$kommma=",";
						elseif ($values['sign'][$n]=='sign_supervisor' && $values['sign_orig'][$n]=='sign_janitor'):
							$blank_date = 'oppsynsigndato= NULL';
							$sign_field = 'saksbehandlerid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'saksigndato=';
							$sign_date="'" . date($this->bocommon->datetimeformat) . "'";
							$kommma=",";
						elseif ($values['sign'][$n]=='sign_supervisor' && $values['sign_orig'][$n]=='sign_budget_responsible'):
							$blank_date = 'budsjettsigndato= NULL';
							$sign_field = 'saksbehandlerid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'saksigndato=';
							$sign_date="'" . date($this->bocommon->datetimeformat) . "'";
							$kommma=",";
						elseif ($values['sign'][$n]=='sign_budget_responsible' && $values['sign_orig'][$n]=='sign_janitor'):
							$blank_date = 'oppsynsigndato= NULL';
							$sign_field = 'budsjettansvarligid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'budsjettsigndato=';
							$sign_date="'" . date($this->bocommon->datetimeformat) . "'";
							$kommma=",";
						elseif ($values['sign'][$n]=='sign_budget_responsible' && $values['sign_orig'][$n]=='sign_supervisor'):
							$blank_date = 'saksigndato= NULL';
							$sign_field = 'budsjettansvarligid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'budsjettsigndato=';
							$sign_date="'" . date($this->bocommon->datetimeformat) . "'";
							$kommma=",";
						elseif ($values['sign'][$n]=='sign_budget_responsible' && !$values['sign_orig'][$n]):
							$blank_date = '';
							$sign_field = 'budsjettansvarligid=';
							$sign_id = "'$user_lid'";
							$sign_date_field = 'budsjettsigndato=';
							$sign_date="'" . date($this->bocommon->datetimeformat) . "'";
							$kommma=",";
						endif;


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
							$payment_date = date($this->bocommon->dateformat,$values['timestamp_voucher_date'][$n]+(24*3600*$values['num_days'][$n]));
							$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag set forfallsdato= '$payment_date' where bilagsnr='$voucher_id'");
						}

						$transfer_id="Null".",";
						$transfer_date="Null";

						if ($values['transfer'][$n])
						{
							if ($this->check_for_transfer($voucher_id))
							{
								$transfer_id="'$user_lid',";
								$transfer_date="'" . date($this->bocommon->datetimeformat) . "'";
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
							$sql= "UPDATE fm_ecobilag set $blank_date $kommma_blank $sign_field $sign_id $kommma $sign_date_field $sign_date $kommma $transfer_sign_field $transfer_id $transfer_date_field $transfer_date ,kreditnota=$wait_for_kreditnota  where bilagsnr='$voucher_id'";
							$GLOBALS['phpgw']->db->transaction_begin();
							$GLOBALS['phpgw']->db->query($sql);
							$GLOBALS['phpgw']->db->transaction_commit();

							$receipt['message'][] = array('msg'=> lang('voucher is updated: ') . $voucher_id);
						}
					}
				}
			}

			$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag set utbetalingid = NULL, utbetalingsigndato = NULL WHERE budsjettsigndato IS NULL");
			$GLOBALS['phpgw']->db->query("UPDATE fm_ecobilag set utbetalingid = NULL, utbetalingsigndato = NULL WHERE oppsynsigndato IS NULL AND saksigndato IS NULL");

			return $receipt;
		}

		function check_for_transfer($voucher_id='')
		{
			$allow_transfer=false;

			$sql = "SELECT * FROM fm_ecobilag WHERE bilagsnr='$voucher_id'";
			$this->db->limit_query($sql,0,__LINE__,__FILE__,1);

			$this->db->next_record();

			if ($this->db->f('budsjettsigndato') && ($this->db->f('oppsynsigndato') || $this->db->f('saksigndato')))
			{
				$allow_transfer=true;
			}

			return $allow_transfer;
		}

		function check_claim($voucher_id='')
		{
			$sql = "SELECT count(*) FROM fm_ecobilag $this->left_join fm_workorder on fm_ecobilag.pmwrkord_code = fm_workorder.id "
			. " WHERE bilagsnr='$voucher_id' AND fm_workorder.charge_tenant=1 AND fm_workorder.claim_issued IS NULL";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f(0);
		}
	}

