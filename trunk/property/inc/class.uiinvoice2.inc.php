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
 	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.yui');
	/**
	* Import the jQuery class
	*/
	phpgw::import_class('phpgwapi.jquery');


	class property_uiinvoice2
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
	
		private $config;

		var $public_functions = array
		(
			'index'								=> true,
			'query'								=> true,
			'edit'						 		=> true,
			'get_vouchers'						=> true,
			'get_single_voucher'				=> true,
			'get_single_line'					=> true,
			'update_voucher'					=> true,
			'get_first_line'					=> true
		);

		function __construct()
		{
//			parent::__construct();
		
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
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
		
//			self::set_active_menu('property::invoice::invoice2');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::invoice::invoice2';
			$this->config				= CreateObject('phpgwapi.config','property');
			$this->config->read();

		}

		public function add_javascript($app, $pkg, $name)
		{
  			return $GLOBALS['phpgw']->js->validate_file($pkg, str_replace('.js', '', $name), $app);
		}
		/**
		* A more flexible version of xslttemplate.add_file
		*/
		public function add_template_file($tmpl)
		{
			if(is_array($tmpl))
			{
				foreach($tmpl as $t)
				{
					$this->add_template_file($t);
				}
				return;
			}
			foreach(array_reverse($this->tmpl_search_path) as $path)
			{
				$filename = $path . '/' . $tmpl . '.xsl';
				if (file_exists($filename))
				{
					$GLOBALS['phpgw']->xslttpl->xslfiles[$tmpl] = $filename;
					return;
				}
			}
			echo "Template $tmpl not found in search path: ";
			print_r($this->tmpl_search_path);
			die;
		}

		public function link($data)
		{
			return $GLOBALS['phpgw']->link('/index.php', $data);
		}

		public function redirect($link_data)
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		/**
		 * Reassign and alter dimensions accordingly
		 * @param int $line_id
		 * @param bigint $order_id
		 * @return boolean true on success
		 */
		public function reassign_order($line_id, $order_id, $voucher_id)
		{
			if( $this->bo->reassign_order($line_id, $order_id))
			{
				phpgwapi_cache::message_set(lang('voucher is updated'), 'message');

				$result =  array
				(
					'status'	=> 'updated'
				);
			}
			else
			{
				$result =  array
				(
					'status'	=> 'error'
				);
			}
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				if( $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
				{
					phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
					$result['receipt'] = $receipt;
				}
				return $result;
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiinvoice2.index', 'voucher_id' => $voucher_id, 'line_id' => $line_id));
			}
		}

		function update_voucher()
		{
			$receipt = array();
			$voucher_id	= phpgw::get_var('voucher_id', 'int');
			$line_id	= phpgw::get_var('line_id', 'int');

			if($values = phpgw::get_var('values'))
			{

				if($values['order_id'] != $values['order_id_orig'])
				{
					return $this->reassign_order($line_id, $values['order_id'], $voucher_id);
				}

				$cats			= CreateObject('phpgwapi.categories', -1,  'property', '.project');
				$cats->supress_info	= true;
				$category	= $cats->return_single((int)$values['dim_e']);
				if(!isset($category[0]) || $category[0]['active'] != 1)
				{
					$receipt['error'][]=true;				
					phpgwapi_cache::message_set(lang('not a valid category'), 'error');
				}
				if(!$this->bo->check_valid_b_account($values['b_account_id']))
				{
					$receipt['error'][]=true;				
					phpgwapi_cache::message_set(lang('not a valid budget account'), 'error');
				}
				
				$order = execMethod('property.soworkorder.read_single',$values['order_id']);
				$project = execMethod('property.soproject.read_single', $order['project_id']);
				
				if($project['closed'])
				{
					$receipt['error'][]=true;				
					phpgwapi_cache::message_set(lang('Project is closed'), 'error');
				}
				
				$approve = execMethod('property.boinvoice.get_approve_role',  $values['dim_b']);

				if(!$approve)
				{
					$receipt['error'][]=true;
					phpgwapi_cache::message_set(lang('you are not approved for this task'), 'error');
				}

				$values['voucher_id'] = $voucher_id;
				$values['line_id'] = $line_id;
				if(!$receipt['error'])
				{
					if($this->bo->update_voucher2($values))
					{
						$result =  array
						(
							'status'	=> 'updated'
						);
					}
					else
					{
						$result =  array
						(
							'status'	=> 'error'
						);
					}
				}
			}

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				if( $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
				{
					phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
					$result['receipt'] = $receipt;
				}
				return $result;
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiinvoice2.index', 'voucher_id' => $voucher_id, 'line_id' => $line_id));
			}
		}


		function index()
		{
			$receipt = array();
			$voucher_id	= phpgw::get_var('voucher_id', 'int');
			$line_id	= phpgw::get_var('line_id', 'int');

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			if(isset($this->config->config_data['invoice_acl']) && $this->config->config_data['invoice_acl'] == 'dimb')
			{
				$janitor_list				= $this->bo->get_dimb_role_user(1,'', $janitor);
				$supervisor_list			= $this->bo->get_dimb_role_user(2,'', $supervisor);
				$budget_responsible_list	= $this->bo->get_dimb_role_user(3,'', $budget_responsible);
			}
			else
			{
				$janitor_list				= $this->bocommon->get_user_list_right(32,$janitor,'.invoice');
				$supervisor_list			= $this->bocommon->get_user_list_right(64,$supervisor,'.invoice');
				$budget_responsible_list	= $this->bocommon->get_user_list_right(128,$budget_responsible,'.invoice');
			}

			$userlist_default = array();
			$userlist_default[] = array('id'=> '*' . $GLOBALS['phpgw']->accounts->get($this->account_id)->lid, 'name'=>lang('mine vouchers'));
			$userlist_default[] = array('id'=>'','name'=>lang('no user'));

			$voucher_list = array();
			if($voucher_id)
			{
				$voucher_list = $this->bo->get_vouchers(array('query' => $voucher_id ));
			}
			
			if(!$voucher_list)
			{
				$voucher_list = array('id' => '', 'name' => lang('select'));
			}

			foreach($userlist_default as $default)
			{
				$janitor_list = array_merge(array($default), $janitor_list);
				$supervisor_list = array_merge(array($default), $supervisor_list);
				$budget_responsible_list = array_merge(array($default), $budget_responsible_list);
			}

			$msgbox_data = array();
			if( phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
			{
				phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			}

			$user = $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] );

			$myColumnDefs = array();
			$datavalues = array();
			$myButtons	= array();

			$datavalues[] = array
			(
				'name'				=> "0",
				'values' 			=> $this->query(),//json_encode(array()),
				'total_records'		=> 0,
				'permission'   		=> "''",
				'is_paginator'		=> 1,
				'edit_action'		=> "''",
				'footer'			=> 0
			);

			$datatable = array
			(
				array
				(
				'key' => 'id',
				'hidden' => true
				),
				array
				(
					'key' => 'approve_line',
					'label' => lang('select'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'status_line',
					'label' => lang('status'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key'	=>	'amount',
					'label'	=>	lang('amount'),
					'formatter' => 'FormatterRight',
					'sortable'	=>	true
				),
				array
				(
					'key' => 'approved_amount',
					'label' => lang('approved amount'),
					'sortable'	=> true,
					'formatter' => 'FormatterRight',
				),
				array
				(
					'key' => 'split',
					'label' => lang('split line'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'budget_account',
					'label' => lang('budget account'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'dima',
					'label' => lang('dim a'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'dimb',
					'label' => lang('dim b'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'order_id',
					'label' => lang('order'),
					'sortable' => false,
					'formatter' => 'FormatterRight',
				),
				array
				(
					'key' => 'dime',
					'label' => lang('dime'),
					'sortable' => false,
					'formatter' => 'FormatterRight',
				),
				array
				(
					'key' => 'project_group',
					'label' => lang('project group'),
					'sortable' => false,
					'formatter' => 'FormatterRight',
				),
				array
				(
					'key' => 'line_text',
					'label' => lang('invoice line text'),
					'sortable' => false,
					'formatter' => 'FormatterCenter',
				),
				array
				(
					'key' => 'approved_amount_hidden',
					'hidden' => true
				)
			);

			$myColumnDefs[0] = array
			(
				'name'		=> "0",
				'values'	=>	json_encode($datatable)
			);	

			$criteria_list = array
			(
				array
				(
					'id'	=> 'voucher_id',
					'name'	=> lang('voucher id'),
				),
				array
				(
					'id'	=> 'invoice_id',
					'name'	=> lang('invoice number'),
				),
				array
				(
					'id'	=> 'vendor_id',
					'name'	=> lang('vendor'),
				),
				array
				(
					'id'	=> 'order_id',
					'name'	=> lang('order id'),
				),
				array
				(
					'id'	=> 'b_account',
					'name'	=> lang('budget account'),
				),
				array
				(
					'id'	=> 'dimb',
					'name'	=> lang('dimb'),
				),
			);
			
			$data = array
			(
				'td_count'						=> '""',
				'base_java_url'					=> "{menuaction:'property.uiinvoice2.query'}",
				'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'						=> $datavalues,
				'myColumnDefs'					=> $myColumnDefs,
				'myButtons'						=> $myButtons,

				'msgbox_data'					=> $msgbox_data,
				'invoice_layout_config'			=> json_encode(execMethod('phpgwapi.template_portico.retrieve_local', 'invoice_layout_config')),
				'preferences_url'				=> $GLOBALS['phpgw']->link('/preferences/index.php'),
				'preferences_text'				=> lang('preferences'),
				'home_url'						=> $GLOBALS['phpgw']->link('/home.php'),
				'home_text'						=> lang('home'),
				'home_icon'						=> 'icon icon-home',
				'about_url'						=> $GLOBALS['phpgw']->link('/about.php', array('app' => $GLOBALS['phpgw_info']['flags']['currentapp']) ),
				'about_text'					=> lang('about'),
				'logout_url'					=> $GLOBALS['phpgw']->link('/logout.php'),
				'logout_text'					=> lang('logout'),
				'user_fullname' 				=> $user->__toString(),
				'site_title'					=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
				'filter_form' 					=> array
													(
														'janitor_list' 				=> array('options' => $janitor_list),
														'supervisor_list' 			=> array('options' => $supervisor_list),
														'budget_responsible_list' 	=> array('options' => $budget_responsible_list),
														'criteria_list'				=> array('options' => $criteria_list)
													),
				'filter_invoice' 					=> array
													(
														'voucher_list' 			=> array('options' => $voucher_list),
													),
				'voucher_info'					=> $this->get_single_line($line_id),
				'update_action'					=> self::link(array('menuaction' => 'property.uiinvoice2.update_voucher'))
			);
//_debug_array($data);die();			
			$GLOBALS['phpgw_info']['flags']['noframework']	= true;

			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/layout/assets/skins/sam/layout.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			phpgwapi_yui::load_widget('layout');
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			phpgwapi_jquery::load_widget('core');

			self::add_javascript('property', 'portico', 'ajax_invoice.js');
			self::add_javascript('property', 'yahoo', 'invoice2.index.js');
			self::add_javascript('phpgwapi', 'tinybox2', 'packed.js');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');

			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice2'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('data' => $data));
		}
	

		public function query()
		{
			$line_id =	phpgw::get_var('line_id', 'int');
			if ( ! $voucher_id = phpgw::get_var('voucher_id_filter') )
			{
				$voucher_id = phpgw::get_var('voucher_id');
			}
			$this->bo->allrows = true;
			$values = $this->bo->read_invoice_sub($voucher_id);

			foreach($values as &$entry)
			{
				$_checked = '';
				if($entry['id'] == $line_id)
				{
					$_checked = 'checked="checked"';
				}

				$entry['approve_line'] = "<input id=\"approve_line\" type =\"radio\" {$_checked} name=\"values[approve_line]\" value=\"{$entry['id']}\">";
				$entry['split'] = "<input type =\"text\" name=\"values[split_amount][{$entry['id']}]\" value=\"\" size=\"8\">";
				$entry['approved_amount_hidden'] = $entry['approved_amount'];
				$entry['approved_amount'] = "<input type =\"text\" name=\"values[approved_amount][{$entry['id']}]\" value=\"{$entry['approved_amount']}\" size=\"8\">";
				if($entry['dime'])
				{
					$catetory = execMethod('phpgwapi.categories.return_single',$entry['dime']);
					$entry['dime'] = $catetory[0]['name'];
				}

				$results['results'][]= $entry;
			}

			return json_encode($values);
		}

		public function get_vouchers()
		{
			$janitor_lid			= phpgw::get_var('janitor_lid', 'string');
			$supervisor_lid			= phpgw::get_var('supervisor_lid', 'string');
			$budget_responsible_lid	= phpgw::get_var('budget_responsible_lid', 'string');
			$criteria				= phpgw::get_var('criteria', 'string');
			$query					= phpgw::get_var('query', 'string');

			$vouchers = $this->bo->get_vouchers(array('janitor_lid' => $janitor_lid, 'supervisor_lid' => $supervisor_lid, 'budget_responsible_lid' =>$budget_responsible_lid, 'criteria' => $criteria, 'query' => $query ));

			return $vouchers;
		}

		/*not used*/
		public function get_single_voucher($voucher_id = 0)
		{
			$voucher = $this->bo->read_single_voucher($voucher_id);		
		}

		/*
		* Find and select the first line
		*/
		public function get_first_line()
		{
			$voucher_id =  phpgw::get_var('voucher_id', 'int');
			$voucher = $this->bo->read_invoice_sub($voucher_id);
			$ret = array('line_id' => 0);
			if($voucher)
			{
				$ret['line_id'] = $voucher[0]['id'];
			}
			return $ret;
		}

		public function get_single_line($line_id = 0)
		{
			$line_id	= $line_id ? $line_id : phpgw::get_var('line_id', 'int');
			$voucher_info = array();
			
			$voucher = $this->bo->read_single_line($line_id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$custom_config	= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$baseurl_invoice = isset($custom_config->config_data['common']['baseurl_invoice']) && $custom_config->config_data['common']['baseurl_invoice'] ? $custom_config->config_data['common']['baseurl_invoice'] : '';

//			$_last_period_last_year = (string)(date('Y') -1) . '12';
			$period_list = $this->bo->period_list();
			$periodization_start_list = $period_list;
//			array_unshift($period_list,array ('id'=> $_last_period_last_year,'name'=> $_last_period_last_year));

			$period_list = $this->bocommon->select_list(isset($voucher[0]['period']) ? $voucher[0]['period'] : '', $period_list);
			$periodization_start_list = $this->bocommon->select_list(isset($voucher[0]['period']) ? $voucher[0]['period'] : '', $periodization_start_list);

			array_unshift($period_list,array ('id'=> '','name'=> lang('select')));
			array_unshift($periodization_start_list,array ('id'=> '','name'=> lang('select')));

			$voucher_info['generic']['period_list']['options'] = $period_list;
			$voucher_info['generic']['periodization_start_list']['options'] = $periodization_start_list;

			$approved_list = array();

			$role_check = array
			(
				'is_janitor' 				=> lang('janitor'),
				'is_supervisor' 			=> lang('supervisor'),
				'is_budget_responsible' 	=> lang('b - responsible')
			);
			
			$sign_orig = '';
			$my_initials = $GLOBALS['phpgw_info']['user']['account_lid'];

			if(count($voucher))
			{

//---------start forward

				if(isset($this->config->config_data['invoice_acl']) && $this->config->config_data['invoice_acl'] == 'dimb')
				{
					$janitor_list				= $this->bo->get_dimb_role_user(1,$voucher[0]['dim_b'], isset($voucher[0]['janitor'])?$voucher[0]['janitor']:'');
					$supervisor_list			= $this->bo->get_dimb_role_user(2,$voucher[0]['dim_b'], isset($voucher[0]['supervisor'])?$voucher[0]['supervisor']:'');
					$budget_responsible_list	= $this->bo->get_dimb_role_user(3,$voucher[0]['dim_b'], isset($voucher[0]['budget_responsible'])?$voucher[0]['budget_responsible']:'');
				}
				else
				{
					$janitor_list				= $this->bocommon->get_user_list_right(32,isset($voucher[0]['janitor'])?$voucher[0]['janitor']:'','.invoice');
					$supervisor_list			= $this->bocommon->get_user_list_right(64,isset($voucher[0]['supervisor'])?$voucher[0]['supervisor']:'','.invoice');
					$budget_responsible_list	= $this->bocommon->get_user_list_right(128,isset($voucher[0]['budget_responsible'])?$voucher[0]['budget_responsible']:'','.invoice');
				}

				$approve = execMethod('property.boinvoice.get_approve_role', $voucher[0]['dim_b']);
 
				$approved_list[] = array
				(
					'role'		=> $role_check['is_janitor'],
					'role_sign'	=> 'oppsynsmannid',
					'initials'	=> $voucher[0]['janitor'] ? $voucher[0]['janitor'] : '',
					'date'		=> $voucher[0]['oppsynsigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['oppsynsigndato'] ) ) :'',
					'user_list'	=> !$voucher[0]['oppsynsigndato'] ? array('options' => $janitor_list) : ''
				);
				$approved_list[] = array
				(
					'role'		=> $role_check['is_supervisor'],
					'role_sign'	=> 'saksbehandlerid',
					'initials'	=> $voucher[0]['supervisor'] ? $voucher[0]['supervisor'] : '',
					'date'		=> $voucher[0]['saksigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['saksigndato'] ) ) :'',
					'user_list'	=> !$voucher[0]['saksigndato'] ?array('options' => $supervisor_list) : ''
				);
				$approved_list[] = array
				(
					'role'		=> $role_check['is_budget_responsible'],
					'role_sign'	=> 'budsjettansvarligid',
					'initials'	=> $voucher[0]['budget_responsible'] ? $voucher[0]['budget_responsible'] : '',
					'date'		=> $voucher[0]['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $voucher[0]['budsjettsigndato'] ) ) :'',
					'user_list'	=> !$voucher[0]['budsjettsigndato'] ? array('options' => $budget_responsible_list) : ''
				);

				foreach($approved_list as &$_approved_list)
				{
					if(isset($_approved_list['user_list']['options']))
					{
						array_unshift ($_approved_list['user_list']['options'], array('id' => '', 'name' => lang('forward')));
					}
				}

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
				array_unshift ($voucher_info['generic']['approve_list']['options'],array ('id'=>'','name'=>lang('reset approval')));
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
					$_image_url = "{$baseurl_invoice}{$voucher[0]['external_ref']}";
					$voucher[0]['external_ref'] = " <a href=\"javascript:openwindow('{$_image_url}','640','800')\" >" . lang('invoice number') . '</a>';
					$voucher[0]['image_url']	= $_image_url;
				}
				$voucher_info['generic']['process_log'] = $voucher[0]['process_log'];
//				$voucher[0]['image_url']	= '';//'http://www.nettavisen.no/';

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

			$cats	= CreateObject('phpgwapi.categories', -1,  'property', '.project');
			$cats->supress_info	= true;

			$categories = $cats->formatted_xslt_list(array('selected' => isset($voucher[0]['dime']) && $voucher[0]['dime'] ? $voucher[0]['dime'] : 0));
			
			foreach($categories['cat_list'] as &$cat)
			{
				$cat['id'] = $cat['cat_id'];
				$cat['selected'] = $cat['selected'] ? 1 : 0;
			}
			$voucher_info['generic']['dime_list'] = array('options' => $categories['cat_list']);			
			$voucher_info['generic']['approved_list'] = $approved_list;
			$voucher_info['generic']['process_code_list'] = array('options' => execMethod('property.bogeneric.get_list', array(
				'type'		=> 'voucher_process_code',
				'selected'	=> isset($voucher[0]['process_code']) ? $voucher[0]['process_code'] : '')));

			array_unshift ($voucher_info['generic']['process_code_list']['options'],array ('id'=>'','name'=>lang('select')));
			array_unshift ($voucher_info['generic']['dimb_list']['options'],array ('id'=>'','name'=>lang('select')));
			array_unshift ($voucher_info['generic']['dime_list']['options'],array ('id'=>'','name'=>lang('select')));
			array_unshift ($voucher_info['generic']['periodization_list']['options'],array('id' => '', 'name' => lang('none')));

			$voucher_info['voucher'] = $voucher;
			$voucher_info['generic']['sign_orig'] = $sign_orig;
			$voucher_info['generic']['my_initials'] = $my_initials;

//_debug_array($voucher_info);die();

			return $voucher_info;
		}
	}
