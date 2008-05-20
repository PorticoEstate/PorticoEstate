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

	class property_boinvoice
	{
		var $db = '';

		function property_boinvoice($session=false)
		{
			$this->db		= $GLOBALS['phpgw']->db;
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so		= CreateObject('property.soinvoice',true);
			$this->bocommon		= CreateObject('property.bocommon');
			$this->account_id	= $GLOBALS['phpgw_info']['user']['account_id'];

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start			= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query			= phpgw::get_var('query');
			$sort			= phpgw::get_var('sort');
			$order			= phpgw::get_var('order');
			$filter			= phpgw::get_var('filter', 'int');
			$cat_id			= phpgw::get_var('cat_id', 'int');
			$user_lid		= phpgw::get_var('user_lid');
			$allrows		= phpgw::get_var('allrows', 'bool');
			$b_account_class	= phpgw::get_var('b_account_class', 'int');
			$district_id		= phpgw::get_var('district_id', 'int');


			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(isset($district_id))
			{
				$this->district_id = $district_id;
			}

			if(isset($b_account_class))
			{
				$this->b_account_class = $b_account_class;
			}

			if(isset($query))
			{
				$this->query = $query;
			}

			if(!empty($filter))
			{
				$this->filter = $filter;
			}
			if(isset($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order))
			{
				$this->order = $order;
			}
			if(isset($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($user_lid))
			{
				$this->user_lid = $user_lid;
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
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

//	_debug_array($data);

			$this->start			= isset($data['start'])?$data['start']:'';
			$this->query			= isset($data['query'])?$data['query']:'';
			$this->filter			= isset($data['filter'])?$data['filter']:'';
			$this->sort				= isset($data['sort'])?$data['sort']:'';
			$this->order			= isset($data['order'])?$data['order']:'';
			$this->cat_id			= isset($data['cat_id'])?$data['cat_id']:'';
			$this->user_lid			= isset($data['user_lid'])?$data['user_lid']:'';
			$this->sub				= isset($data['sub'])?$data['sub']:'';
			$this->allrows			= isset($data['allrows'])?$data['allrows']:'';
			$this->b_account_class	= isset($data['b_account_class'])?$data['b_account_class']:'';
			$this->district_id		= isset($data['district_id'])?$data['district_id']:'';
		}

		function read_invoice($paid='',$start_date='',$end_date='',$vendor_id='',$loc1='',$workorder_id='',$voucher_id='')
		{
			$invoice = $this->so->read_invoice(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'user_lid' => $this->user_lid,'cat_id' => $this->cat_id, 'paid' => $paid,
											'start_date'=>$start_date,'end_date'=>$end_date,'vendor_id'=>$vendor_id,
											'loc1'=>$loc1,'workorder_id'=>$workorder_id,'allrows'=>$this->allrows,
											'voucher_id'=>$voucher_id,'b_account_class' =>$this->b_account_class,
											'district_id' => $this->district_id));

			$this->total_records = $this->so->total_records;

			return $invoice;
		}

		function read_invoice_sub($voucher_id='',$paid='')
		{
			$invoice = $this->so->read_invoice_sub(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'user_lid' => $this->user_lid,'cat_id' => $this->cat_id,'voucher_id'=>$voucher_id,'paid' => $paid));
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

		function read_consume($start_date='',$end_date='',$vendor_id='',$loc1='',$workorder_id='',$b_account_class='',$district_id='')
		{
			$invoice = $this->so->read_consume(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'user_lid' => $this->user_lid,'cat_id' => $this->cat_id,
											'start_date'=>$start_date,'end_date'=>$end_date,'vendor_id'=>$vendor_id,
											'loc1'=>$loc1,'workorder_id'=>$workorder_id,'b_account_class' =>$b_account_class,
											'district_id' => $district_id ));

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

		function select_account_class($selected='')
		{
			$b_account_class_list= $this->so->select_account_class();
			return $this->bocommon->select_list($selected,$b_account_class_list);
		}

		function period_list($selected='')
		{
			for ($i=1; $i<=12; $i++)
			{
				$period_list[$i]['id'] = $i;
				$period_list[$i]['name'] = $i;
				if($i==$selected)
				{
					$period_list[$i]['selected'] = 'selected';
				}
			}
			return $period_list;
		}

		function tax_code_list($selected='')
		{
			$tax_codes=$this->so->tax_code_list();

			while (is_array($tax_codes) && list(,$code) = each($tax_codes))
			{
				$sel_code = '';
				if ($code['id']==$selected)
				{
					$sel_code = 'selected';
				}

				$tax_code_list[] = array
				(
					'id'			=> $code['id'],
					'selected'		=> $sel_code
				);
			}

			for ($i=0;$i<count($tax_code_list);$i++)
			{
				if ($tax_code_list[$i]['selected'] != 'selected')
				{
					unset($tax_code_list[$i]['selected']);
				}
			}

			return $tax_code_list;
		}

		function update_period($voucher_id='',$period='')
		{
			return $this->so->update_period($voucher_id,$period);
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
	//----------

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
			$users=$this->bocommon->get_user_list_right(1,$selected,'.invoice',$extra,$default);
			return $users;
		}

		function get_type_list($selected='')
		{
			$types=$this->so->get_type_list();
			return $this->bocommon->select_list($selected,$types);
		}

	//----------
		function select_dimb_list($selected='')
		{
			$dimbs=$this->so->select_dimb_list();
			return $this->bocommon->select_list($selected,$dimbs);
		}

	//-------------------
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

		function add($values,$debug='')
		{
			$this->soXport    = CreateObject('property.soXport');
			if($values['loc1']=$values['location']['loc1'])
			{
				$values['dima']=implode('',$values['location']);
			}

			$values['spbudact_code']=$values['b_account_id'];
			$values['fakturanr']=$values['invoice_num'];
			$values['spvend_code']=$values['vendor_id'];

			$values['belop'] = str_replace('kr','',$values['amount']);
			$values['belop'] = str_replace(' ','',$values['belop']);
			$values['belop'] = str_replace(',','.',$values['belop']);
			$values['godkjentbelop']=$values['belop'];

			$values['fakturadato'] = date($this->bocommon->dateformat,mktime(2,0,0,$values['smonth'],$values['sday'],$values['syear']));

			if($values['num_days'])
			{
				$values['forfallsdato'] = date($this->bocommon->dateformat,mktime(2,0,0,$values['smonth'],$values['sday'],$values['syear'])+(86400*$values['num_days']));
			}
			else
			{
				$values['forfallsdato'] = date($this->bocommon->dateformat,mktime(2,0,0,$values['emonth'],$values['eday'],$values['eyear']));
			}

			$values['artid'] 			= $values['art'];
			$values['periode']			= $values['smonth'];
			$values['dimb']				= $values['dim_b'];
			$values['oppsynsmannid']	= $values['janitor'];
			$values['saksbehandlerid']	= $values['supervisor'];
			$values['budsjettansvarligid'] = $values['budget_responsible'];
			$values['kildeid'] 			= 1;
			$values['kidnr'] 			= $values['kid_nr'];
			$values['typeid'] 			= $values['type'];
			if($order_type = $this->soXport->check_order(intval($values['order_id'])))
			{
				if($order_type=='workorder')
				{
					$soworkorder = CreateObject('property.soworkorder');
					$soproject = CreateObject('property.soproject');
					$workorder	= $soworkorder->read_single($values['order_id']);
					$project	= $soproject->read_single($workorder['project_id']);

					$values['spvend_code']	= $workorder['vendor_id'];
					$values['spbudact_code']	= $workorder['b_account_id'];
					$values['location_code']	=$project['location_code'];
					$values['dima']				=str_replace('-','',$project['location_code']);
					$values['vendor_name']		= $this->get_vendor_name($workorder['vendor_id']);
					$values['pmwrkord_code']	= $values['order_id'];
					$values['project_id']			= $workorder['project_id'];

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
			$contacts	= CreateObject('property.soactor');
			$contacts->role='vendor';

			$vendor_data	= $contacts->read_single(array('actor_id'=>$vendor_id));
			if(is_array($vendor_data))
			{
				foreach($vendor_data['attributes'] as $attribute)
				{
					if($attribute['name']=='org_name')
					{
						return $attribute['value'];
					}
				}
			}
		}

		function set_responsible($values,$user_id='',$b_account_id='')
		{
			if (!$values['budget_responsible'])
			{
				$values['budget_responsible'] = $this->soXport->get_responsible($b_account_id);
				$values['budsjettansvarligid'] = $values['budget_responsible'];
			}

			$acl 	= CreateObject('phpgwapi.acl',$user_id);
			if($acl->check('.invoice',32) && !$acl->check('.invoice',64)):
			{
				$values['janitor']	= $GLOBALS['phpgw']->accounts->id2name($user_id);
				$values['oppsynsmannid']	= $values['janitor'];
			}
	//		elseif((!$acl->check('.invoice',32) && $acl->check('.invoice',64)) || ($acl->check('.invoice',32) && $acl->check('.invoice',64))):
			elseif($acl->check('.invoice',64)):
			{
				$values['supervisor']	= $GLOBALS['phpgw']->accounts->id2name($user_id);
				$values['saksbehandlerid']	= $values['supervisor'];
			}
			endif;

			return $values;
		}
	}

