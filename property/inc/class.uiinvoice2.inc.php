<?php
	/**
	* phpGroupWare - registration
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package registration
 	* @version $Id: class.uicheck_list.inc.php 8628 2012-01-21 10:42:05Z vator $
	*/

	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('registration.uicommon');
/*
	include_class('registration', 'check_list', 'inc/model/');
	include_class('registration', 'date_generator', 'inc/component/');
	include_class('registration', 'status_checker', 'inc/helper/');
	include_class('registration', 'date_helper', 'inc/helper/');
*/	
	class property_uiinvoice2 extends registration_uicommon
	{
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $type_id;
		var $location_code;
	
		private $so_control_area;
		private $so_control;
		private $so_check_list;
		private $so_control_item;
		private $so_check_item;
		private $so_procedure;

		var $public_functions = array
		(
			'index'								=> true,
			'query'								=> true,
			'edit'						 		=> true,
			'get_vouchers'						=> true,
			'get_single_voucher'				=> true
		);

		function __construct()
		{
			parent::__construct();
		
			$this->account_id 			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.boinvoice',true);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->status_id			= $this->bo->status_id;
			$this->allrows				= $this->bo->allrows;
		
			self::set_active_menu('property::invoice::invoice2');
		}

		function index()
		{
			$voucher_id	= phpgw::get_var('voucher_id', 'int');
			
			if($values = phpgw::get_var('values'))
			{
				$values['pending_users'] = isset($values['pending_users']) && $values['pending_users'] ? array_unique($values['pending_users']) : array();
				$values['pending_users_orig'] = isset($values['pending_users_orig']) && $values['pending_users_orig'] ? array_unique($values['pending_users_orig']) : array();
				
				$receipt = $this->bo->approve_users($values);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'registration.uipending.index'));
			}
			else
			{
				if(phpgw::get_var('phpgw_return_as') == 'json')
				{
					return $this->query();
				}

				$status_list = array
				(
					array
					(
						'id'	=> 0,
						'name'	=> lang('Select status')
					),
					array
					(
						'id'	=> 1,
						'name'	=> lang('approved')
					),
					array
					(
						'id'	=> 2,
						'name'	=> lang('pending')
					),
				);



				$janitor_list				= $this->bocommon->get_user_list_right(32,$janitor,'.invoice');
				$supervisor_list			= $this->bocommon->get_user_list_right(64,$supervisor,'.invoice');
				$budget_responsible_list	= $this->bocommon->get_user_list_right(128,$budget_responsible,'.invoice');

				$userlist_default = array();
				$userlist_default[] = array('id'=> '*' . $GLOBALS['phpgw']->accounts->get($this->account_id)->lid, 'name'=>lang('mine vouchers'));
				$userlist_default[] = array('id'=>'','name'=>lang('no user'));

				$voucher_list = array('id' => '', 'name' => lang('select'));

				foreach($userlist_default as $default)
				{
					$janitor_list = array_merge(array($default), $janitor_list);
					$supervisor_list = array_merge(array($default), $supervisor_list);
					$budget_responsible_list = array_merge(array($default), $budget_responsible_list);
				}

				$data = array(
					'filter_form' 				=> array
					(
						'janitor_list' 				=> array('options' => $janitor_list),
						'supervisor_list' 			=> array('options' => $supervisor_list),
						'budget_responsible_list' 	=> array('options' => $budget_responsible_list),
					),
					'filter_invoice' 				=> array
					(
						'voucher_list' 				=> array('options' => $voucher_list),
					),
					'voucher_info'					=> $this->get_single_voucher($voucher_id),
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'registration.uipending.query', 'phpgw_return_as' => 'json')),
						'field' => array(
							array(
								'key' => 'id',
								'hidden' => true
							),
							array(
								'key' => 'reg_id',
								'label' => lang('id'),
								'sortable'	=> true,
								'formatter' => 'formatLinkPending'
							),
							array(
								'key'	=>	'reg_lid',
								'label'	=>	lang('user'),
								'sortable'	=>	true
							),
							array(
								'key' => 'reg_dla',
								'label' => lang('time'),
								'sortable'	=> true
							),
							array(
								'key' => 'reg_approved',
								'label' => lang('approved'),
								'sortable'	=> true,
								'formatter' => 'FormatterCenter'
							),
							array(
								'key' => 'location_code',
								'label' => lang('location'),
								'sortable'	=> false
							),

							array(
									'key' => 'checked',
									'label' => lang('approve'),
									'sortable' => false,
									'formatter' => 'formatterCheckPending',
									'className' => 'mychecks'
							),
							array(
								'key' => 'actions',
								'hidden' => true
							),
							array(
								'key' => 'labels',
								'hidden' => true
							),
							array(
								'key' => 'ajax',
								'hidden' => true
							),array(
								'key' => 'parameters',
								'hidden' => true
							)					
						)
					)
				);
//_debug_array($data);die();			
				phpgwapi_yui::load_widget('paginator');

				self::add_javascript('registration', 'yahoo', 'pending.index.js');
				self::add_javascript('controller', 'controller', 'jquery.js');
				self::add_javascript('property', 'portico', 'ajax_invoice.js');

				self::render_template_xsl(array('invoice2', 'common'), $data);
			}	
		}
	

		public function query()
		{
			$status_id = phpgw::get_var('status_id');

			$this->bo->start = phpgw::get_var('startIndex');
		
			$user_list = $this->bo->read(array('user_id' => $user_id, 'role_id' =>$role_id, 'type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,
												   'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run));
			
			foreach($user_list as &$user)
			{
				$reg_info = unserialize(base64_decode($user['reg_info']));
				$user['location_code'] = $reg_info['location_code'];
				$results['results'][]= $user;
			}
			$results['total_records'] = $this->bo->total_records;
			$results['start'] = $this->start;
			$results['sort'] = 'location_code';
			$results['dir'] = $this->bo->sort ? $this->bo->sort : 'ASC';
					
			array_walk($results['results'], array($this, 'add_links'), array($type));
						
			return $this->yui_results($results);
		}

		public function get_vouchers()
		{
			$janitor_lid			= phpgw::get_var('janitor_lid', 'string');
			$supervisor_lid			= phpgw::get_var('supervisor_lid', 'string');
			$budget_responsible_lid	= phpgw::get_var('budget_responsible_lid', 'string');
			$query					= phpgw::get_var('query', 'string');

			$vouchers = $this->bo->get_vouchers(array('janitor_lid' => $janitor_lid, 'supervisor_lid' => $supervisor_lid, 'budget_responsible_lid' =>$budget_responsible_lid, 'query' => $query ));

			return $vouchers;
		}
		public function get_single_voucher($voucher_id = 0)
		{
			$voucher_id	= $voucher_id ? $voucher_id : phpgw::get_var('voucher_id', 'int');
			$voucher_info = array();
			
			$voucher = $this->bo->read_single_voucher($voucher_id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$custom_config	= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$baseurl_invoice = isset($custom_config->config_data['common']['baseurl_invoice']) && $custom_config->config_data['common']['baseurl_invoice'] ? $custom_config->config_data['common']['baseurl_invoice'] : '';

			$_last_period_last_year = (string)(date('Y') -1) . '12';
			$period_list = $this->bo->period_list();
			$periodization_start_list = $period_list;
			array_unshift($period_list,array ('id'=> $_last_period_last_year,'name'=> $_last_period_last_year));

			$period_list = $this->bocommon->select_list(isset($voucher[0]['period']) ? $voucher[0]['period'] : '', $period_list);
			$periodization_start_list = $this->bocommon->select_list(isset($voucher[0]['period']) ? $voucher[0]['period'] : '', $periodization_start_list);

			array_unshift($period_list,array ('id'=> 0,'name'=> lang('select')));
			array_unshift($periodization_start_list,array ('id'=> 0,'name'=> lang('select')));

			$voucher_info['generic']['period_list']['options'] = $period_list;
			$voucher_info['generic']['periodization_start_list']['options'] = $periodization_start_list;

			$approved_list = array();

			$role_check = array
			(
				'is_janitor' 				=> lang('janitor'),
				'is_supervisor' 			=> lang('supervisor'),
				'is_budget_responsible' 	=> lang('b - responsible')
			);

			if(count($voucher))
			{

//---------start forward
				$approve = execMethod('property.uiinvoice.get_approve_role');
 
				$approved_list[] = array
				(
					'role'		=> $role_check['is_janitor'],
					'role_sign'	=> 'oppsynsmannid',
					'initials'	=> $voucher[0]['janitor'] ? $voucher[0]['janitor'] : '',
					'date'		=> $voucher[0]['oppsynsigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['oppsynsigndato'] ) ) :'',
					'user_list'	=> !$voucher[0]['oppsynsigndato'] ? array('options' => $this->bocommon->get_user_list_right(32,isset($voucher[0]['janitor'])?$voucher[0]['janitor']:'','.invoice')) : ''
				);
				$approved_list[] = array
				(
					'role'		=> $role_check['is_supervisor'],
					'role_sign'	=> 'saksbehandlerid',
					'initials'	=> $voucher[0]['supervisor'] ? $voucher[0]['supervisor'] : '',
					'date'		=> $voucher[0]['saksigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['saksigndato'] ) ) :'',
					'user_list'	=> !$voucher[0]['saksigndato'] ? array('options' => $this->bocommon->get_user_list_right(64,isset($voucher[0]['supervisor'])?$voucher[0]['supervisor']:'','.invoice')) : ''
				);
				$approved_list[] = array
				(
					'role'		=> $role_check['is_budget_responsible'],
					'role_sign'	=> 'budsjettansvarligid',
					'initials'	=> $voucher[0]['budget_responsible'] ? $voucher[0]['budget_responsible'] : '',
					'date'		=> $voucher[0]['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['budsjettsigndato'] ) ) :'',
					'user_list'	=> !$voucher[0]['budsjettsigndato'] ? array('options' => $this->bocommon->get_user_list_right(128,isset($voucher[0]['budget_responsible'])?$voucher[0]['budget_responsible']:'','.invoice')) : ''
				);

				$my_initials = $GLOBALS['phpgw_info']['user']['account_lid'];

				foreach($approve as &$_approve)
				{
					if($_approve['id'] == 'is_janitor' && $my_initials == $voucher[0]['janitor'] && $voucher[0]['oppsynsigndato'])
					{
						$_approve['selected'] = 1;
						$sign_orig = 'is_janitor';
					}
					else if($_approve['id'] == 'is_supervisor' && $my_initials == $voucher[0]['supervisor'] && $voucher[0]['saksigndato'])
					{
						$_approve['selected'] = 1;
						$sign_orig = 'is_supervisor';
					}
					else if($_approve['id'] == 'is_budget_responsible' && $my_initials == $voucher[0]['budget_responsible'] && $voucher[0]['budsjettsigndato'])
					{
						$_approve['selected'] = 1;
						$sign_orig = 'is_budget_responsible';
					}
				}

				unset($_approve);

				$approve_list = array();
				foreach($approve as $_approve)
				{
					if($_approve['id'] == 'is_janitor')
					{
						if(($my_initials == $voucher[0]['janitor'] && $voucher[0]['oppsynsigndato']) || !$voucher[0]['oppsynsigndato'])
						{
							$approve_list[] = $_approve;
						}
					}
					if($_approve['id'] == 'is_supervisor')
					{
						if(($my_initials == $voucher[0]['supervisor'] && $voucher[0]['saksigndato']) || !$voucher[0]['saksigndato'])
						{
							$approve_list[] = $_approve;
						}
					}
					if($_approve['id'] == 'is_budget_responsible')
					{
						if(($my_initials == $voucher[0]['budget_responsible'] && $voucher[0]['budsjettsigndato']) || !$voucher[0]['budsjettsigndato'])
						{
							$approve_list[] = $_approve;
						}
					}
				}

				$voucher_info['generic']['approve_list'] = array('options' => $approve_list);
				array_unshift ($voucher_info['generic']['approve_list']['options'],array ('id'=>'','name'=>lang('select')));
//---------end forward

				$voucher_info['generic']['approved_amount'] = 0;
				$voucher_info['generic']['amount'] = 0;
				foreach ($voucher as $line)
				{
					$voucher_info['generic']['approved_amount'] += $line['approved_amount'];
					$voucher_info['generic']['amount']  += $line['amount'];	
				}

				$voucher_info['generic']['approved_amount'] = number_format($voucher_info['generic']['approved_amount'], 2, ',', ' ');
				$voucher_info['generic']['amount'] = number_format($voucher_info['generic']['amount'], 2, ',', ' ');
				$voucher_info['generic']['dimb_list']['options']		= $this->bo->select_dimb_list($voucher[0]['dim_b']);
				$voucher_info['generic']['tax_code_list']['options']	= $this->bo->tax_code_list($voucher[0]['tax_code']);
				$voucher_info['generic']['periodization_list']['options'] = execMethod('property.bogeneric.get_list', array('type'=>'periodization', 'selected' => $voucher[0]['periodization'] ));


				$voucher[0]['invoice_date'] = $voucher[0]['invoice_date'] ?  $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['invoice_date'] ), $dateformat ) : '';
				$voucher[0]['payment_date'] = $voucher[0]['payment_date'] ?  $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['payment_date'] ), $dateformat ) : '';
				$voucher[0]['oppsynsigndato'] = $voucher[0]['oppsynsigndato'] ?  $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['oppsynsigndato'] ), $dateformat ) : '';
				$voucher[0]['saksigndato'] = $voucher[0]['saksigndato'] ?  $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['saksigndato'] ), $dateformat) : '';
				$voucher[0]['budsjettsigndato'] = $voucher[0]['budsjettsigndato'] ?  $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['budsjettsigndato'] ),$dateformat ) : '';

				if($voucher[0]['remark'])
				{
					$voucher[0]['remark_link']= " <a href=\"javascript:openwindow('".$GLOBALS['phpgw']->link('/index.php', array
						(
							'menuaction'=> 'property.uiinvoice.remark',
							'id'		=> $voucher[0]['id'],
						)). "','550','400')\" >".lang('Remark')."</a>";
				}
				if($voucher[0]['order_id'])
				{
					$voucher[0]['order_link']= $GLOBALS['phpgw']->link('/index.php', array
						(
							'menuaction'	=> 'property.uiinvoice.view_order',
							'order_id'		=> $voucher[0]['order_id']
						));
				}

				if($voucher[0]['external_ref'])
				{
					$voucher[0]['external_ref'] = " <a href=\"javascript:openwindow('{$baseurl_invoice}{$voucher[0]['external_ref']}','640','800')\" >" . lang('invoice id') . '</a>';
				}

			}
			else
			{
				$voucher_info['generic']['dimb_list']['options']		= $this->bo->select_dimb_list();
				$voucher_info['generic']['tax_code_list']['options']	= $this->bo->tax_code_list();
				$voucher_info['generic']['periodization_list']['options'] = execMethod('property.bogeneric.get_list', array('type'=>'periodization'));

				$approved_list[] = array
				(
					'role'		=> $role_check['is_janitor'],
					'role_sign'	=> 'oppsynsmannid',
				);
				$approved_list[] = array
				(
					'role'		=> $role_check['is_supervisor'],
					'role_sign'	=> 'saksbehandlerid',
				);
				$approved_list[] = array
				(
					'role'		=> $role_check['is_budget_responsible'],
					'role_sign'	=> 'budsjettansvarligid',
				);
			}

			$voucher_info['generic']['approved_list'] = $approved_list;
			$voucher_info['generic']['process_code_list'] = array('options' => execMethod('property.bogeneric.get_list', array(
				'type'		=> 'voucher_process_code',
				'selected'	=> isset($voucher[0]['process_code']) ? $voucher[0]['process_code'] : '')));

			array_unshift ($voucher_info['generic']['process_code_list']['options'],array ('id'=>'','name'=>lang('select')));
			array_unshift ($voucher_info['generic']['dimb_list']['options'],array ('id'=>'','name'=>lang('select')));
			array_unshift ($voucher_info['generic']['periodization_list']['options'],array('id' => '0', 'name' => lang('none')));

			$voucher_info['voucher'] = $voucher;
//_debug_array($voucher_info);die();

			return $voucher_info;
		}
	}
