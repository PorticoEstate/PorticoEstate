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

	phpgw::import_class('phpgwapi.datetime');
	/**
	 * Description
	 * @package property
	 */

	class property_boinvoice
	{
		public $total_records = 0;
		public $sum_amount = 0;
		public $results = 0;
		public $allrows = false;
		public $debug = false;
		/**
		 *
		 * @var boolean In case of mass-import of fictious invoices
		 */
		public $supertransaction = false;

		function property_boinvoice($session=false)
		{
			$this->so			= CreateObject('property.soinvoice',true);
			$this->bocommon		= CreateObject('property.bocommon');
			$this->account_id	= $GLOBALS['phpgw_info']['user']['account_id'];

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$filter					= phpgw::get_var('filter', 'int');
			$cat_id					= phpgw::get_var('cat_id', 'int');
			$user_lid				= phpgw::get_var('user_lid');
			$allrows				= phpgw::get_var('allrows', 'bool');
			$b_account_class		= phpgw::get_var('b_account_class', 'int');
			$district_id			= phpgw::get_var('district_id', 'int');
			$b_account				= phpgw::get_var('b_account');

	//		$this->start			= $start ? $start : (int)$this->start;

			$this->start			= isset($_REQUEST['start']) 		? $start			: $this->start;

			if(phpgw::get_var('workorder_id', 'int'))
			{
				$this->start = 0;
			}

			$this->query			= isset($query) ? $query : $query;

			if(!phpgw::get_var('paid', 'bool'))
			{
	//			$voucher_id 	= $this->query && ctype_digit($this->query) ? $this->query : 0;
			}
	//		else
			{
				$user_lid = isset($_REQUEST['user_lid']) ? $user_lid : 'all';
			}

			if($voucher_id)
			{
				$this->start 	= 0;
			}


			$this->b_account		= isset($b_account) ? $b_account : $b_account;
			$this->district_id		= isset($district_id) ? $district_id : $district_id;
			$this->b_account_class	= isset($b_account_class) ? $b_account_class : $b_account_class;
			$this->filter			= isset($filter) ? $filter : $filter;
			$this->sort				= isset($sort) ? $sort : $sort;
			$this->order			= isset($order) ? $order : $order;
			$this->cat_id			= isset($cat_id) ? $cat_id : $cat_id;
			$this->user_lid			= $user_lid ? $user_lid : $this->user_lid;
			$this->allrows			= isset($allrows) ? $allrows : $allrows;
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','invoice',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','invoice');

//_debug_array($data);

			$this->start			= isset($data['start'])?$data['start']:'';
			$this->query			= isset($data['query'])?$data['query']:'';
			$this->filter			= isset($data['filter'])?$data['filter']:'';
			$this->sort				= isset($data['sort'])?$data['sort']:'';
			$this->order			= isset($data['order'])?$data['order']:'';
			$this->cat_id			= isset($data['cat_id'])?$data['cat_id']:'';
			$this->user_lid			= $data['user_lid']?$data['user_lid']:'';
			$this->sub				= isset($data['sub'])?$data['sub']:'';
			$this->allrows			= isset($data['allrows'])?$data['allrows']:'';
			$this->b_account_class	= isset($data['b_account_class'])?$data['b_account_class']:'';
			$this->district_id		= isset($data['district_id'])?$data['district_id']:'';
		}

		function read_invoice($paid='',$start_date='',$end_date='',$vendor_id='',$loc1='',$workorder_id='',$voucher_id='', $invoice_id = '',$ecodimb = '',$project_id = 0)
		{
			if(!phpgw::get_var('paid', 'bool'))
			{
//				$voucher_id 	= $this->query && ctype_digit($this->query) ? $this->query : 0;
			}

			$start_date	= $this->bocommon->date_to_timestamp($start_date);
			$end_date	= $this->bocommon->date_to_timestamp($end_date);

			$invoice = $this->so->read_invoice(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'user_lid' => $this->user_lid,'cat_id' => $this->cat_id, 'paid' => $paid,
				'start_date'=>$start_date,'end_date'=>$end_date,'vendor_id'=>$vendor_id,
				'loc1'=>$loc1,'workorder_id'=>$workorder_id,'allrows'=>$this->allrows,
				'voucher_id'=>$voucher_id,'b_account_class' =>$this->b_account_class,
				'district_id' => $this->district_id, 'invoice_id' => $invoice_id, 'ecodimb' => $ecodimb, 'project_id' => $project_id));

			$soXport    = CreateObject('property.soXport');
			$soworkorder = CreateObject('property.soworkorder');
			$sos_agreement = CreateObject('property.sos_agreement');
			foreach ( $invoice as & $entry )
			{
				if( $entry['order_id'] )
				{
					if($order_type = $soXport->check_order($entry['order_id']))
					{
						if($order_type == 'workorder')
						{
							$workorder	= $soworkorder->read_single($entry['order_id']);

							if($workorder['vendor_id'] && ($workorder['vendor_id'] != $entry['vendor_id']))
							{
								$entry['vendor']		=  $this->get_vendor_name($workorder['vendor_id']) . ' => ' . $entry['vendor'];
							}
						}

						if($order_type == 's_agreement')
						{
							$s_agreement = $sos_agreement->read_single($entry['order_id']);

							if($s_agreement['vendor_id'] && ($s_agreement['vendor_id'] != $entry['vendor_id']))
							{
								$entry['vendor']		= $this->get_vendor_name($s_agreement['vendor_id']) . ' => ' . $entry['vendor'];
							}
						}
					}
				}
			}

			$this->total_records = $this->so->total_records;
			$this->sum_amount = $this->so->sum_amount;
			return $invoice;
		}

		function read_invoice_sub($voucher_id=0,$paid = false)
		{
			$invoice = $this->so->read_invoice_sub(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'user_lid' => $this->user_lid,'cat_id' => $this->cat_id,'voucher_id'=>$voucher_id,'paid' => $paid, 'results' => $this->results,'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;
			return $invoice;
		}

		function read_remark($id='',$paid='')
		{
			return $remark = $this->so->read_remark($id,$paid);
		}

		function read_single_voucher($voucher_id)
		{
			return $this->so->read_single_voucher($voucher_id);
		}


		function read_single_line($line_id)
		{

			$invoice = $this->so->read_single_voucher(0, $line_id);
			
			$soXport    = CreateObject('property.soXport');
			$soworkorder = CreateObject('property.soworkorder');
			$sos_agreement = CreateObject('property.sos_agreement');
			foreach ( $invoice as & $entry )
			{
				if( $entry['order_id'] )
				{
					if($order_type = $soXport->check_order($entry['order_id']))
					{
						if($order_type == 'workorder')
						{
							$workorder	= $soworkorder->read_single($entry['order_id']);

							if($workorder['vendor_id'] && ($workorder['vendor_id'] != $entry['vendor_id']))
							{
						//		$entry['vendor']	=  $this->get_vendor_name($workorder['vendor_id']) . ' => ' . $entry['vendor'];
								$entry['vendor']	= $workorder['vendor_id'] . ' [' .$this->get_vendor_name($workorder['vendor_id']) . "] =>  {$entry['vendor_id']} [{$entry['vendor']}]";
							}
						}

						if($order_type == 's_agreement')
						{
							$s_agreement = $sos_agreement->read_single($entry['order_id']);

							if($s_agreement['vendor_id'] && ($s_agreement['vendor_id'] != $entry['vendor_id']))
							{
								$entry['vendor']	= $s_agreement['vendor_id'] . ' [' .$this->get_vendor_name($s_agreement['vendor_id']) . "] =>  {$entry['vendor_id']} [{$entry['vendor']}]";
							}
						}
					}
				}
			}

			return $invoice;
		}

		function read_consume($start_date='',$end_date='',$vendor_id='',$loc1='',$workorder_id='',$b_account_class='',$district_id='',$ecodimb = '')
		{
			$start_date	= $this->bocommon->date_to_timestamp($start_date);
			$end_date	= $this->bocommon->date_to_timestamp($end_date);

			$invoice = $this->so->read_consume(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'user_lid' => $this->user_lid,'cat_id' => $this->cat_id,
				'start_date'=>$start_date,'end_date'=>$end_date,'vendor_id'=>$vendor_id,
				'loc1'=>$loc1,'workorder_id'=>$workorder_id,'b_account_class' =>$b_account_class,
				'district_id' => $district_id, 'b_account' => $this->b_account,'ecodimb' => $ecodimb ));

			$this->total_records = $this->so->total_records;

			return $invoice;
		}

		function update_invoice($values)
		{

			return $this->so->update_invoice($values);

		}

		function update_invoice_sub($values)
		{
			return $this->so->update_invoice_sub($values);
		}

		function update_single_line($values, $paid = false)
		{
			return $this->so->update_single_line($values, $paid);
		}

		function select_account_class($selected='')
		{
			$b_account_class_list= $this->so->select_account_class();
			return $this->bocommon->select_list($selected,$b_account_class_list);
		}

		function period_list($selected='')
		{
			
			$sogeneric		= CreateObject('property.sogeneric','period_transition');
			$config			= $sogeneric->read(array('allrows' => true));
			
			$period_transition = array();
			foreach($config as $entry)
			{
				$period_transition[$entry['month']] = $entry;
			}
			
			$current_month = date('n');
			
			if(isset($period_transition[$current_month]))
			{
				$_lag_day = (int)$period_transition[$current_month]['day'];
				$_lag_hour = (int)$period_transition[$current_month]['hour'];
				$_lag_seconds = ($_lag_day * 24 * 3600) + ($_lag_hour * 3600);
			}
			else if(isset($period_transition[13]))
			{
				$_lag_day = (int)$period_transition[13]['day'];
				$_lag_hour = (int)$period_transition[13]['hour'];
				$_lag_seconds = ($_lag_day * 24 * 3600) + ($_lag_hour * 3600);
			}
			else
			{
				$_lag			= date('n') == 1 ? 11 : 7;//6 days into next month, 10 days into next year
				$_lag_seconds	= $_lag * 24 * 3600;
			}
			
			$_lag_seconds -= phpgwapi_datetime::user_timezone();
			
//_debug_array($period_transition);			
			$time = time();
			$timestamp_at_start_month =  mktime( $hour = 0, $minute = 0, $second = 0, $month = date("n"), $day = 0, $year = date("Y") );


			if(($time - $timestamp_at_start_month) < $_lag_seconds)
			{
				$time = $time - $_lag_seconds;			
			}

			$month = date('n', $time);
			$year = date('Y',$time);
			$check_year = true;
			$period_list = array();
			for ($i=$month; $i<=12; $i++)
			{
				$period = sprintf("%s%02d",$year,$i);
				$period_list[] = array
				(
					'id'	=> $period,
					'name'	=> $period
				);

				if($check_year = true && $year != date('Y'))
				{
					$i = 0;
					$year++;
					$check_year = false;
				}
			}

			if(count($period_list) == 1) //last month in year
			{
				$year++;
				$period = sprintf("%s%02d",$year,1);
				$period_list[] = array
				(
					'id'	=> $period,
					'name'	=> $period
				);
			
			}

			foreach ($period_list as &$_period)
			{
				$_period['selected'] = $_period['id'] == $selected ? 1 : 0;
			}

			return $period_list;
		}

		function tax_code_list($selected='', $tax_codes = array())
		{
			if(!$selected && $selected !== '0' )
			{
				$selected = '#';
			}
			else
			{
				$selected = (int)$selected;
			}

			if(! $tax_codes)
			{
				$tax_codes = $this->so->tax_code_list();
			}
			
			foreach ($tax_codes as &$tax_code)
			{
				$tax_code['selected'] = (int)$tax_code['id'] === $selected ? 1 : 0;
			}
			return $tax_codes;
		}

		function update_period($voucher_id='',$period='')
		{
			return $this->so->update_period($voucher_id,$period);
		}

		function update_periodization($voucher_id='',$periodization='')
		{
			return $this->so->update_periodization($voucher_id,$periodization);
		}

		function update_periodization_start($voucher_id='',$period='')
		{
			return $this->so->update_periodization_start($voucher_id,$period);
		}

		function increment_bilagsnr()
		{
			return $this->so->increment_bilagsnr();
		}

		function next_bilagsnr()
		{
			return $this->so->next_bilagsnr();
		}

		function check_vendor($vendor_id)
		{

			return $this->so->check_vendor($vendor_id);
		}

		function get_lisfm_ecoart($selected='')
		{
			$arts=$this->so->get_lisfm_ecoart();
			return $this->bocommon->select_list($selected,$arts);
		}

		function select_category($format='',$selected='')
		{
			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
				break;
			}

			$categories= $this->so->get_type_list();

			return $this->bocommon->select_list($selected,$categories);
		}


		function get_invoice_user_list($format='',$selected='',$extra='',$default='')
		{
			if(!$selected && $default)
			{
				$selected = $default;
			}

			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('user_lid_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('user_lid_filter'));
				break;
			}
			$users=$this->bocommon->get_user_list_right(array(32, 64, 128),$selected,'.invoice',$extra,$default);
			return $users;
		}

		function get_type_list($selected='')
		{
			$types=$this->so->get_type_list();
			return $this->bocommon->select_list($selected,$types);
		}

		function select_dimb_list($selected='')
		{
			$dimbs=$this->so->select_dimb_list($selected);
			return $this->bocommon->select_list($selected,$dimbs);
		}

		function select_dimd_list($selected='')
		{
			$dimds=$this->so->select_dimd_list();
			return $this->bocommon->select_list($selected,$dimds);
		}

		function select_tax_code_list($selected='')
		{
			$tax_codes=$this->so->select_tax_code_list();
			return $this->bocommon->select_list($selected,$tax_codes);
		}

		function delete($params)
		{
			if (is_array($params))
			{
				$this->so->delete($params[0]);
			}
			else
			{
				$this->so->delete($params);
			}
		}

		public function add_manual_invoice($values)
		{
			$receipt = array();
			$config	= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id('property', '.invoice'));

			$buffer = array();
			$soXport    = CreateObject('property.soXport');
			$soXport->supertransaction = $this->supertransaction;
			$soXport->debug = $this->debug;
			if($values['loc1']=$values['location']['loc1'])
			{
				$values['dima']=implode('',$values['location']);
				$values['location_code']	= explode('-', $values['location']);
			}
			$values['spbudact_code']	= $values['b_account_id'];
			$values['fakturanr']		= $values['invoice_id'];
			$values['spvend_code']		= $values['vendor_id'];
			$values['belop'] 			= $values['amount'];
			$values['godkjentbelop']	= $values['amount'];

			$_dateformat = $GLOBALS['phpgw']->db->date_format();

			$invoice_date 	= $values['invoice_date'] ? phpgwapi_datetime::date_to_timestamp($values['invoice_date']) : time();
			$payment_date 	= $values['payment_date'] ? phpgwapi_datetime::date_to_timestamp($values['payment_date']) : time();
			$paid_date		= $values['paid_date'] ? phpgwapi_datetime::date_to_timestamp($values['paid_date']) : time();

			$values['fakturadato'] 		= date($_dateformat, $invoice_date);
			$values['forfallsdato'] 	= date($_dateformat, $payment_date);
			$values['periode']			= date('Ym', $paid_date);

			$values['kildeid'] 			= 1;
			$values['pmwrkord_code']	= $values['order_id'];

			if(isset($config->config_data['common']['manual_voucher_id']) && $config->config_data['common']['manual_voucher_id'])
			{
				if( $soXport->check_voucher_id($values['voucher_out_id']))
				{
					$receipt['error'][] = array('msg'=>lang('voucher id already taken'));
				}
				$skip_update_voucher_id = true;
				$values['bilagsnr']		= $values['voucher_out_id'];
				$values['bilagsnr_ut']	= '';
			}
			else
			{
				$skip_update_voucher_id = false;
				$values['bilagsnr']		= execMethod('property.socommon.increment_id','Bilagsnummer');
				$values['bilagsnr_ut']	= $values['voucher_out_id'];			
			}

			if( $soXport->check_invoice_id($values['vendor_id'], $values['invoice_id']))
			{
				$receipt['error'][] = array('msg'=>lang('invoice id already taken for this vendor'));
			}

			if(isset($receipt['error']))
			{
				return $receipt;
			}

			$values['kostra_id'] 		= $soXport->get_kostra_id($values['loc1']);
			$values['mvakode'] 			= (int)$values['tax_code'];
			$values['project_id']		= $values['project_group'];			

			$values['oppsynsmannid']		= $values['janitor'];
			$values['saksbehandlerid']		= $values['supervisor'];
			$values['budsjettansvarligid']	= $values['budget_responsible'];

			if($values['order_id'] && $order_type = $soXport->check_order($values['order_id']))
			{
				if($order_type=='workorder')
				{
					$buffer[0]=$values;
				}

				if($order_type=='s_agreement')
				{
					$sos_agreement = CreateObject('property.sos_agreement');
					$s_agreement = $sos_agreement->read_single($values['order_id']);
			//		$values = $this->set_responsible($values,$s_agreement['user_id'],$s_agreement['b_account_id']);
					$s_agreement_detail = $sos_agreement->read(array('allrows'=>true,'s_agreement_id'=>$values['order_id'],'detail'=>true));

					$sum_agreement=0;
					for ($i=0;$i<count($s_agreement_detail);$i++)
					{
						$s_agreement_detail[$i]['cost'] = abs($s_agreement_detail[$i]['cost']) > 0 ? $s_agreement_detail[$i]['cost'] : 1;
						$sum_agreement = $sum_agreement + $s_agreement_detail[$i]['cost'];
					}

					for ($i=0;$i<count($s_agreement_detail);$i++)
					{
						$_location = explode('-', $s_agreement_detail[$i]['location_code']);
						$buffer[$i]						= $values;
						$buffer[$i]['location_code']	= $s_agreement_detail[$i]['location_code'];
						$buffer[$i]['loc1']				= $_location[0];
						$buffer[$i]['dima']				= str_replace('-','',$s_agreement_detail[$i]['location_code']);
						$buffer[$i]['belop']			= round($values['belop'] / $sum_agreement * $s_agreement_detail[$i]['cost'],2);
						$buffer[$i]['godkjentbelop']	= $buffer[$i]['belop'];
					}
				}

				if($soXport->add_manual_invoice($buffer,$skip_update_voucher_id))
				{
					$_msg = $this->debug ? 'DEBUG: ': '';
					$receipt['message'][] = array('msg'=> $_msg. lang('Invoice %1 is added',$soXport->voucher_id));
					$receipt['voucher_id'] = $soXport->voucher_id;
				}
				else
				{
					$receipt['error'][] = array('msg'=>lang('Invoice is NOT added!'));
				}
			}
			return $receipt;
		}

		function add($values,$debug='')
		{
			$this->soXport    = CreateObject('property.soXport');
			if($values['loc1']=$values['location']['loc1'])
			{
				$values['dima']=implode('',$values['location']);
			}

			$values['spbudact_code']	= $values['b_account_id'];
			$values['fakturanr']		= $values['invoice_num'];
			$values['spvend_code']		= $values['vendor_id'];
			$values['belop'] 			= $values['amount'];
			$values['godkjentbelop']	= $values['amount'];

			$values['fakturadato'] = date($GLOBALS['phpgw']->db->date_format(),mktime(2,0,0,$values['smonth'],$values['sday'],$values['syear']));

			if($values['num_days'])
			{
				$values['forfallsdato'] = date($GLOBALS['phpgw']->db->date_format(),mktime(2,0,0,$values['smonth'],$values['sday'],$values['syear'])+(86400*$values['num_days']));
			}
			else
			{
				$values['forfallsdato'] = date($GLOBALS['phpgw']->db->date_format(),mktime(2,0,0,$values['emonth'],$values['eday'],$values['eyear']));
			}

			$values['artid'] 			= $values['art'];
			$values['periode']			= $values['syear'] . sprintf("%02d",$values['smonth']);
			$values['dimb']				= $values['dim_b'];
			$values['oppsynsmannid']	= $values['janitor'];
			$values['saksbehandlerid']	= $values['supervisor'];
			$values['budsjettansvarligid'] = $values['budget_responsible'];
			$values['kildeid'] 			= 1;
			$values['kidnr'] 			= $values['kid_nr'];
			$values['typeid'] 			= $values['type'];
			//_debug_array($values);die();
			if($values['order_id'] && $order_type = $this->soXport->check_order($values['order_id']))
			{
				if($order_type=='workorder')
				{
					$soworkorder	= CreateObject('property.soworkorder');
					$soproject		= CreateObject('property.soproject');
					$workorder		= $soworkorder->read_single($values['order_id']);
					$project		= $soproject->read_single($workorder['project_id']);

					$values['spvend_code']		= $workorder['vendor_id'];
					$values['spbudact_code']	= $workorder['b_account_id'];
					$values['location_code']	= $project['location_code'];
					$values['dima']				= str_replace('-','',$project['location_code']);
					$values['vendor_name']		= $this->get_vendor_name($workorder['vendor_id']);
					$values['pmwrkord_code']	= $values['order_id'];
					$values['project_id']		= $workorder['project_id'];
					if(!$values['dimb'])
					{
						$values['dimb']			= $workorder['ecodimb'];
					}

					$values = $this->set_responsible($values,$workorder['user_id'],$workorder['b_account_id']);

					if($values['auto_tax'])
					{
						$values['mvakode'] = $this->soXport->auto_tax($values['dima']);
						$values['mvakode'] = $this->soXport->tax_b_account_override($values['mvakode'],$values['spbudact_code']);
						$values['mvakode'] = $this->soXport->tax_vendor_override($values['mvakode'],$values['spvend_code']);
						$values['kostra_id'] = $this->soXport->get_kostra_id($values['dima']);
					}

					$buffer[0]=$values;
				}

				if($order_type=='s_agreement')
				{
					$sos_agreement = CreateObject('property.sos_agreement');
					$s_agreement = $sos_agreement->read_single(array('s_agreement_id'=>$values['order_id']));

					$values['spvend_code']		= $s_agreement['vendor_id'];
					$values['spbudact_code']	= $s_agreement['b_account_id'];
					$values['vendor_name']		= $this->get_vendor_name($s_agreement['vendor_id']);
					$values['pmwrkord_code']	= intval($values['order_id']);
					$values = $this->set_responsible($values,$s_agreement['user_id'],$s_agreement['b_account_id']);


					$s_agreement_detail = $sos_agreement->read(array('allrows'=>true,'s_agreement_id'=>$values['order_id'],'detail'=>true));

					$sum_agreement=0;
					for ($i=0;$i<count($s_agreement_detail);$i++)
					{
						$sum_agreement = $sum_agreement + $s_agreement_detail[$i]['cost'];
					}


					for ($i=0;$i<count($s_agreement_detail);$i++)
					{
						$buffer[$i]=$values;

						$buffer[$i]['location_code']	=$s_agreement_detail[$i]['location_code'];
						$buffer[$i]['dima']				=str_replace('-','',$s_agreement_detail[$i]['location_code']);

						$buffer[$i]['belop']	=	round($values['belop'] / $sum_agreement * $s_agreement_detail[$i]['cost'],2);
						$buffer[$i]['godkjentbelop'] =$buffer[$i]['belop'];

						if($values['auto_tax'])
						{
							$buffer[$i]['mvakode'] = $this->soXport->auto_tax($buffer[$i]['dima']);
							$buffer[$i]['mvakode'] = $this->soXport->tax_b_account_override($buffer[$i]['mvakode'],$buffer[$i]['spbudact_code']);
							$buffer[$i]['mvakode'] = $this->soXport->tax_vendor_override($buffer[$i]['mvakode'],$buffer[$i]['spvend_code']);
							$buffer[$i]['kostra_id'] = $this->soXport->get_kostra_id($buffer[$i]['dima']);
						}
					}
				}
			}
			else
			{
				if($values['auto_tax'])
				{
					$values['mvakode'] = $this->soXport->auto_tax($values['loc1']);
					$values['mvakode'] = $this->soXport->tax_b_account_override($values['mvakode'],$values['spbudact_code']);
					$values['mvakode'] = $this->soXport->tax_vendor_override($values['mvakode'],$values['spvend_code']);
					$values['kostra_id'] = $this->soXport->get_kostra_id($values['loc1']);
				}

				$buffer[0]=$values;
			}

			if($debug)
			{
				return $buffer;
			}

			if($this->soXport->add($buffer)>0)
			{
				$receipt['message'][] = array('msg'=>lang('Invoice %1 is added',$this->soXport->voucher_id));
				$receipt['voucher_id'] = $this->soXport->voucher_id;
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('Invoice is NOT added!'));
			}
			return $receipt;
		}

		function get_vendor_name($vendor_id='')
		{
			$contacts	= CreateObject('property.sogeneric');
			$contacts->get_location_info('vendor',false);

			$criteria = array
				(
					'attributes' => array
					(
						array
						(
							'column_name' => 'org_name'
						)
					)
				);

			$vendor_data	= $contacts->read_single(array('id' => $vendor_id), $criteria);

			if(is_array($vendor_data))
			{
				foreach($vendor_data['attributes'] as $attribute)
				{
					if($attribute['column_name']=='org_name')
					{
						return $attribute['value'];
					}
				}
			}
		}

		function set_responsible($values, $user_id=0, $b_account_id='')
		{
			$config				= CreateObject('phpgwapi.config','property');
			$config->read();
			$responsible_supervisor = isset($config->config_data['dimb_responsible_1']) && $config->config_data['dimb_responsible_1'] ? $config->config_data['dimb_responsible_1'] : 0;
			$responsible_responsible = isset($config->config_data['dimb_responsible_2']) && $config->config_data['dimb_responsible_2'] ? $config->config_data['dimb_responsible_2'] : 0;

			$responsible		= CreateObject('property.soresponsible');
			if (!$values['budget_responsible'])
			{
				$criteria_budget_responsible		= array('ecodimb' => $values['dimb'], 'cat_id' => $responsible_responsible);
				if($budget_responsible_contact_id = $responsible->get_responsible($criteria_budget_responsible))
				{
					$budget_responsible_user_id			= $responsible->get_contact_user_id($budget_responsible_contact_id);
					$values['budget_responsible']		= $GLOBALS['phpgw']->accounts->get($budget_responsible_user_id)->lid;
					$values['budsjettansvarligid']		= $values['budget_responsible'];
				}
			}

			if (!$values['budget_responsible'])
			{
				$values['budget_responsible'] = execMethod('property.soXport.get_responsible',$b_account_id);
				$values['budsjettansvarligid'] = $values['budget_responsible'];
			}

			if(!$values['supervisor'])
			{
				$criteria_supervisor				= array('ecodimb' => $values['dimb'], 'cat_id' => $responsible_supervisor);
				if($supervisor_contact_id = $responsible->get_responsible($criteria_supervisor))
				{
					$supervisor_user_id					= $responsible->get_contact_user_id($supervisor_contact_id);
					$values['supervisor']				= $GLOBALS['phpgw']->accounts->get($supervisor_user_id)->lid;
					$values['saksbehandlerid']			= $values['supervisor'];
				}
			}

			$values['janitor']				= $GLOBALS['phpgw']->accounts->get($user_id)->lid;
			$values['oppsynsmannid']		= $values['janitor'];

			if(!$values['supervisor'])
			{
				$acl 	= CreateObject('phpgwapi.acl',$user_id);
				if($acl->check('.invoice', 32, 'property') && !$acl->check('.invoice', 64, 'property'))
				{
					$values['janitor']	= $GLOBALS['phpgw']->accounts->get($user_id)->lid;
					$values['oppsynsmannid']	= $values['janitor'];
				}
				else if($acl->check('.invoice', 64, 'property'))
				{
					$values['supervisor']	= $GLOBALS['phpgw']->accounts->get($user_id)->lid;
					$values['saksbehandlerid']	= $values['supervisor'];
				}
			}
			return $values;
		}

		public function get_single_line($id, $paid = false)
		{
			$line = $this->so->get_single_line($id, $paid);

			$soXport    = CreateObject('property.soXport');
			$soworkorder = CreateObject('property.soworkorder');
			$sos_agreement = CreateObject('property.sos_agreement');
			if( $line['order_id'] )
			{
				if($order_type = $soXport->check_order($line['order_id']))
				{
					if($order_type == 'workorder')
					{
						$workorder	= $soworkorder->read_single($line['order_id']);
						if($workorder['vendor_id'] && ($workorder['vendor_id'] != $line['vendor_id']))
						{
							$line['vendor']		=  $this->get_vendor_name($workorder['vendor_id']) . ' => ' . $this->get_vendor_name($line['vendor_id']);
						}
					}
					if($order_type == 's_agreement')
					{
						$s_agreement = $sos_agreement->read_single(array('s_agreement_id'=>$line['order_id']));
						if($s_agreement['vendor_id'] && ($s_agreement['vendor_id'] != $line['vendor_id']))
						{
							$line['vendor']		= $this->get_vendor_name($s_agreement['vendor_id']) . ' => ' . $this->get_vendor_name($line['vendor_id']);
						}
					}
					$line['order_type'] = $order_type;
				}
			}

			if(!isset($line['vendor']))
			{
				$line['vendor']	= $this->get_vendor_name($line['vendor_id']);
			}
			return $line;
		}

		function check_role($dimb = 0)
		{
			return $this->so->check_role($dimb);
		}

		public function get_dimb_role_user($role_id, $dimb = '', $selected = '')
		{
			return $this->so->get_dimb_role_user($role_id, $dimb, $selected);
		}

		public function get_historical_accounting_periods()
		{
			return $this->so->get_historical_accounting_periods();
		}


		public function export_historical_transactions_at_periods($data = array())
		{
			$values =  $this->so->get_historical_transactions_at_periods($data);
			
			if($values)
			{
				$names = $descr = array_keys($values[0]);
				$this->bocommon->download($values,$names,$descr);
			}
		}

		public function export_deposition()
		{
			$values =  $this->so->get_deposition();
			
			if($values)
			{
				$names = $descr = array_keys($values[0]);
				$this->bocommon->download($values,$names,$descr);
			}
		}

		public function forward($data)
		{
			return $this->so->forward($data);
		}

		public function get_vouchers($data)
		{
			return $this->so->get_vouchers($data);
		}

		public function update_voucher2($data)
		{
			return $this->so->update_voucher2($data);
		}

		function get_approve_role($dimb = 0)
		{
			$role_check = array
			(
				'is_janitor' 				=> lang('janitor'),
				'is_supervisor' 			=> lang('supervisor'),
				'is_budget_responsible' 	=> lang('b - responsible')
			);

			$roles 	= $this->check_role($dimb);

			$approve = array();
			foreach ($roles as $role => $role_value)
			{
				if ($role_value && isset($role_check[$role]))
				{
					$approve[] = array
					(
						'id'		=> $role,
						'name'		=> $role_check[$role],
						'selected'	=> 0
					);
				}
			}
			return $approve;
		}

		public function get_auto_generated_invoice_num($vendor_id)
		{
			return $this->so->get_auto_generated_invoice_num($vendor_id);
		}

		/**
		 * Reassign and alter dimensions accordingly
		 * @param int $line_id
		 * @param bigint $order_id
		 * @return boolean true on success
		 */
		public function reassign_order($line_id, $order_id)
		{
			return $this->so->reassign_order($line_id, $order_id);
		}

		/**
		 * Check if provided budget account is valid
		 * @param string $b_account_id
		 * @return boolean true on valid budget account
		 */
		function check_valid_b_account($b_account_id)
		{
			return $this->so->check_valid_b_account($b_account_id);
		}
	}
