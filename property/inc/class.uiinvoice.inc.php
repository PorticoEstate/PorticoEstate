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
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.datetime');
	class property_uiinvoice
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $user_lid;
		var $sub;
		var $currentapp;

		var $public_functions = array
			(
				'index'  		=> true,
				'list_sub'		=> true,
				'consume'		=> true,
				'remark'		=> true,
				'delete'		=> true,
				'add'			=> true,
				'debug'			=> true,
				'view_order'	=> true,
				'download'		=> true,
				'download_sub'	=> true,
				'receipt'		=> true,
				'edit'			=> true,
				'reporting'		=> true,
				'forward'		=> true
			);

		function property_uiinvoice()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::invoice';
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo				= CreateObject('property.boinvoice',true);
			$this->bocommon			= &$this->bo->bocommon;

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->filter			= $this->bo->filter;
			$this->cat_id			= $this->bo->cat_id;
			$this->user_lid			= $this->bo->user_lid;
			$this->allrows			= $this->bo->allrows;
			$this->district_id		= $this->bo->district_id;

			$this->acl 				= & $GLOBALS['phpgw']->acl;

			$this->acl_location		= '.invoice';
			$this->acl_read 		= $this->acl->check('.invoice', PHPGW_ACL_READ, 'property');
			$this->acl_add 			= $this->acl->check('.invoice', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 		= $this->acl->check('.invoice', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 		= $this->acl->check('.invoice', PHPGW_ACL_DELETE, 'property');

		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'			=> $this->start,
				'query'			=> $this->query,
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'filter'		=> $this->filter,
				'cat_id'		=> $this->cat_id,
				'user_lid'		=> $this->user_lid,
				'allrows'		=> $this->allrows,
				'district_id'	=> $this->district_id
			);
			$this->bo->save_sessiondata($data);
		}


		function download()
		{
			$paid 			= phpgw::get_var('paid', 'bool');
			$start_date 	= phpgw::get_var('start_date');
			$end_date 		= phpgw::get_var('end_date');
			$submit_search 	= phpgw::get_var('submit_search', 'bool');
			$vendor_id 		= phpgw::get_var('vendor_id', 'int');
			$workorder_id 	= phpgw::get_var('workorder_id', 'int');
			$loc1 			= phpgw::get_var('loc1');
			$voucher_id 	= phpgw::get_var('voucher_id', 'int');

			$start_date	= urldecode($start_date);
			$end_date	= urldecode($end_date);

			if(!$end_date)
			{
				$end_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,date("m"),date("d"),date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$start_date = $end_date;
			}

			$list = $this->bo->read_invoice($paid,$start_date,$end_date,$vendor_id,$loc1,$workorder_id,$voucher_id);

			while (is_array($list[0]) && list($name_entry,) = each($list[0]))
			{
				$name[]=$name_entry;
			}

			$descr	= $name;

			$this->bocommon->download($list,$name,$descr);
		}


		function download_sub()
		{
			$voucher_id 	= phpgw::get_var('voucher_id', 'int');
			$paid 		= phpgw::get_var('paid', 'bool');

			if ($voucher_id)
			{
				$list = $this->bo->read_invoice_sub($voucher_id,$paid);

				$name = array
					(
						'workorder_id',
						'project_group',
						'status',
						'voucher_id',
						'invoice_id',
						'budget_account',
						'dima',
						'dimb',
						'dimd',
						'tax_code',
						'amount',
						'charge_tenant',
						'claim_issued',
						'vendor'
					);

				$descr = array
					(
						lang('Workorder'),
						lang('project group'),
						lang('status'),
						lang('voucher'),
						lang('Invoice Id'),
						lang('Budget account'),
						lang('Dim A'),
						lang('Dim B'),
						lang('Dim D'),
						lang('Tax code'),
						lang('Sum'),
						lang('Charge tenant'),
						lang('claim issued'),
						lang('vendor')
					);

				$this->bocommon->download($list,$name,$descr);
			}
		}

		function index()
		{
			//--validacion para permisos
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}
			//-- captura datos de URL
			$paid 			= phpgw::get_var('paid', 'bool');
			$start_date 	= phpgw::get_var('start_date');
			$end_date 		= phpgw::get_var('end_date');
			$submit_search 	= phpgw::get_var('submit_search', 'bool');
			$vendor_id 		= phpgw::get_var('vendor_id', 'int');
			$workorder_id 	= phpgw::get_var('workorder_id', 'int');
			$project_id 	= phpgw::get_var('project_id', 'int');
			$loc1 			= phpgw::get_var('loc1');
			$voucher_id 	= $this->query && ctype_digit($this->query) ? $this->query : phpgw::get_var('voucher_id');
			$invoice_id		= phpgw::get_var('invoice_id');
			$b_account_class= phpgw::get_var('b_account_class', 'int');
			$ecodimb 		= phpgw::get_var('ecodimb');
			$this->save_sessiondata();

			//-- ubica focus del menu derecho
			if ( $paid )
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::paid';
			}
			//-- captura datos de URL
			$start_date=urldecode($start_date);
			$end_date=urldecode($end_date);

			//-- si end_date no existe
			if(!$end_date)
			{
				//-- fecha actual
				$start_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,'01','01',date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$end_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,date("m"),date("d"),date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}
			//-- edicion de registro
			$values  = phpgw::get_var('values');
			$receipt = array();

			if( phpgw::get_var('phpgw_return_as') == 'json' && is_array($values) && isset($values))
			{
				$values["save"]="Save";
//			 	_debug_array($values);
				$receipt = $this->bo->update_invoice($values);

			}

			// Edit Period
			$period  = phpgw::get_var('period');
			$voucher_id_for_period  = phpgw::get_var('voucher_id_for_period');
			if( phpgw::get_var('phpgw_return_as') == 'json' &&  isset($period) &&  $period != '')
			{
				$receipt	= $this->bo->update_period($voucher_id_for_period,$period);
			}

			// Edit Periodization
			$periodization  = phpgw::get_var('periodization');
			$voucher_id_for_periodization  = phpgw::get_var('voucher_id_for_periodization');
			if( phpgw::get_var('phpgw_return_as') == 'json' &&  isset($periodization) &&  $periodization != '')
			{
				$receipt	= $this->bo->update_periodization($voucher_id_for_periodization,$periodization);
			}

			// Edit Periodization
			$periodization_start  = phpgw::get_var('periodization_start');
			$voucher_id_for_periodization_start  = phpgw::get_var('voucher_id_for_periodization_start');
			if( phpgw::get_var('phpgw_return_as') == 'json' &&  isset($periodization_start) &&  $periodization_start != '')
			{
				$receipt	= $this->bo->update_periodization_start($voucher_id_for_periodization_start,$periodization_start);
			}


			$datatable = array();
			$values_combo_box = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']				= $this->bocommon->get_menu();

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'		=> 'property.uiinvoice.index',
						'cat_id'			=> $this->cat_id,
						'user_lid'			=> $this->user_lid,
						'sub'				=> $this->sub,
					//	'query'				=> $this->query,
						'paid'				=> $paid,
						'ecodimb'			=> $ecodimb,
						'vendor_id'			=> $vendor_id,
						'workorder_id'		=> $workorder_id,
						'project_id'		=> $project_id,
						'start_date'		=> $start_date,
						'end_date'			=> $end_date,
						'filter'			=> $this->filter,
						'b_account_class'	=> $b_account_class,
						'district_id'		=> $this->district_id
					));
				$datatable['config']['allow_allrows'] = true;


				$datatable['config']['base_java_url'] = "menuaction:'property.uiinvoice.index',"
					."cat_id: '{$this->cat_id}',"
					."user_lid:'{$this->user_lid}',"
					."sub:'{$this->sub}',"
					."query:'{$this->query}',"
					."paid:'{$paid}',"
					."ecodimb:'{$ecodimb}',"
					."vendor_id:'{$vendor_id}',"
					."workorder_id:'{$workorder_id}',"
					."project_id:'{$project_id}',"
					."voucher_id:'{$voucher_id}',"
					."start_date:'{$start_date}',"
					."end_date:'{$end_date}',"
					."filter:'{$this->filter}',"
					."b_account_class:'{$b_account_class}',"
					."district_id:'{$this->district_id}'";

				// sin el primer parametro, para no hacer diferencia entre filtro o selecte
				$values_combo_box[0]  = $this->bo->select_category('',$this->cat_id);
				$default_value = array ('id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bo->get_invoice_user_list('select',$this->user_lid,array('all'),$default='all');
				//$default_value = array ();
				array_unshift ($values_combo_box[1],array('lid'=> $GLOBALS['phpgw']->accounts->get($this->account)->lid, 'firstname'=>lang('mine vouchers')));
				$default_value = array ('lid'=>'','firstname'=>lang('no user'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $this->bo->select_account_class($b_account_class);
				$default_value = array ('id'=>'','name'=>lang('No account'));
				array_unshift ($values_combo_box[2],$default_value);

				if($paid)
				{
					$GLOBALS['phpgw']->jqcal->add_listener('start_date');
					$GLOBALS['phpgw']->jqcal->add_listener('end_date');
				}
				if (!$paid)
				{
					$field_invoice = array
						(
							array
							( //boton 	CATEGORY
								'id' => 'btn_cat_id',
								'name' => 'cat_id',
								'value'	=> lang('Category'),
								'type' => 'button',
								'tab_index' => 1,
								'style' => 'filter'
							),
							array
							( //boton 	OWNER
								'id' => 'btn_user_lid',
								'name' => 'user_lid',
								'value'	=> 'user_lid',
								'type' => 'button',
								'tab_index' => 2,
								'style' => 'filter'
							),
							array
							( // boton exportar
								'type'	=> 'button',
								'id'	=> 'btn_export',
								'tab_index' => 7,
								'value'	=> lang('download')
							),
							array
							( // boton SAVE
								'id'	=> 'btn_save',
								//'name' => 'save',
								'value'	=> lang('save'),
								'tab_index' => 6,
								'type'	=> 'button'
							),
							array
							( // boton ADD
								'type'	=> 'button',//'submit',
								'id'	=> 'btn_new',
								'tab_index' => 5,
								'value'	=> lang('add')
							),
							array
							( // workorder link
								'type' => 'link',
								'id' => 'lnk_workorder',
								'url' => "",
								'value' => lang('Workorder ID'),
								'tab_index' => 5,
								'style' => 'filter'
							),
							array
							( // workorder box
								'name'     => 'workorder_id',
								'id'     => 'txt_workorder',
								'value'    => $workorder_id,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 10,
								'tab_index' => 6,
								'style' => 'filter'
							),
							array
							( //vendor link
								'type' => 'link',
								'id' => 'lnk_vendor',
								'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction' => 'property.uilookup.vendor',
								))."','Search','left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes')",
								'value' => lang('Vendor'),
								'tab_index' => 7,
								'style' => 'filter'
							),
							array
							( // Vendor box HIDDEN
								'name'     => 'vendor_name',
								'id'     => 'txt_vendor_name',
								'value'    => "",
								'type' => 'hidden',
								'size'    => 10,
								'style' => 'filter'
							),
							array
							( // Vendor box
								'name'     => 'vendor_id',
								'id'     => 'txt_vendor',
								'value'    => $vendor_id,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 10,
								'tab_index' => 8,
								'style' => 'filter'
							),
							array
							( // Voucher link
								'type' => 'link',
								'id' => 'lnk_invoice',
								'url' => "",
								'value' => lang('invoice number'),
								'tab_index' => 9,
								'style' => 'filter'
							),
							array
							( // Vendor box
								'name'     => 'invoice_id',
								'id'     => 'txt_invoice',
								'value'    => $invoice_id,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 10,
								'tab_index' => 10,
								'style' => 'filter'
							),
							array
							(
								'type' => 'link',
								'id' => 'lnk_property',
								'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction' => 'property.uilocation.index',
									'lookup'  	=> 1,
									'type_id'  	=> 1,
									'lookup_name'  	=> 0,
								))."','Search','left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes')",
								'value' => lang('property'),
								'tab_index' => 11,
								'style' => 'filter'
							),
							array
							( // txt Facilities Management
								'name'     => 'loc1_name',
								'id'     => 'txt_loc1_name',
								'value'    => "",
								'type' => 'hidden',
								'size'    => 8,
								'style' => 'filter'
							),
							array
							( // txt Facilities Management
								'name'     => 'loc1',
								'id'     => 'txt_loc1',
								'value'    => $loc1,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 8,
								'tab_index' => 12,
								'style' => 'filter'
							),
							array
							( // Voucher link
								'type' => 'link',
								'id' => 'lnk_voucher',
								'url' => "",
								'value' => lang('Voucher ID'),
								'tab_index' => 13,
								'style' => 'filter'
							),
							array
							( // Voucher box
								'name'     => 'voucher_id',
								'id'     => 'txt_voucher',
								'value'    => $voucher_id,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 8,
								'tab_index' => 14,
								'style' => 'filter'
							),
							array
							( //boton   SEARCH
								'id' => 'btn_search',
								'name' => 'search',
								'value'    => lang('search'),
								'tab_index' => 4,
								'type' => 'button'
							),
/*
							array
							( // TEXT IMPUT
								'name'     => 'query',
								'id'     => 'txt_query',
								'value'    => $this->query,//$query,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'tab_index' => 3,
								'size'    => 28
							),
*/
							array
							( // txtbox end_data hidden
								'name'     => 'end_date',
								'id'     => 'txt_end_date',
								'value'    => "",
								'type' => 'hidden',
								'size'    => 8
							),
							array
							( // txtbox start_data hidden
								'name'     => 'start_date',
								'id'     => 'txt_start_date',
								'value'    => "",
								'type' => 'hidden',
								'size'    => 8
							),
							array
							( //hidden paid
								'type'	=> 'hidden',
								'id'	=> 'paid',
								'value'	=> $paid
							)
						);
				}
				else
				{

					$values_combo_box[3]  = $this->bocommon->select_category_list(array('type'=>'dimb', 'selected' => $ecodimb));
					$default_value = array ('id'=>'','name'=>lang('no dimb'));
					array_unshift ($values_combo_box[3],$default_value);

					$field_invoice = array
						(
							array
							( // calendar1 start_date
								'type' => 'text',
								'name'     => 'start_date',
								'id'     => 'start_date',
								'value'    => $start_date,
								'size'    => 7,
								'readonly' => 'readonly',
								'tab_index' => 2,
								'style' => 'filter'
							),
							array
							( // calendar1 start_date
								'type' => 'text',
								'name'     => 'end_date',
								'id'     => 'end_date',
								'value'    => $end_date,
								'size'    => 7,
								'readonly' => 'readonly',
								'tab_index' => 4,
								'style' => 'filter'
							),
							array
							( // workorder link
								'type' => 'link',
								'id' => 'lnk_workorder',
								'url' => "",
								'value' => lang('Workorder ID'),
								'tab_index' => 5,
								'style' => 'filter'
							),
							array
							( // workorder box
								'name'     => 'workorder_id',
								'id'     => 'txt_workorder',
								'value'    => $workorder_id,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 10,
								'tab_index' => 6,
								'style' => 'filter'
							),
							array
							( // project
								'type' => 'link',
								'id' => 'lnk_project',
								'url' => "",
								'value' => lang('project id'),
								'tab_index' => 5,
								'style' => 'filter'
							),
							array
							( // project box
								'name'     => 'project_id',
								'id'     => 'txt_project',
								'value'    => $project_id,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 10,
								'tab_index' => 6,
								'style' => 'filter'
							),
							array
							( //vendor link
								'type' => 'link',
								'id' => 'lnk_vendor',
								'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction' => 'property.uilookup.vendor',
								))."','Search','left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes')",
								'value' => lang('Vendor'),
								'tab_index' => 7,
								'style' => 'filter'
							),
							array
							( // Vendor box HIDDEN
								'name'     => 'vendor_name',
								'id'     => 'txt_vendor_name',
								'value'    => "",
								'type' => 'hidden',
								'size'    => 10,
								'style' => 'filter'
							),
							array
							( // Vendor box
								'name'     => 'vendor_id',
								'id'     => 'txt_vendor',
								'value'    => $vendor_id,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 10,
								'tab_index' => 8,
								'style' => 'filter'
							),
							array
							( // Voucher link
								'type' => 'link',
								'id' => 'lnk_invoice',
								'url' => "",
								'value' => lang('invoice number'),
								'tab_index' => 9,
								'style' => 'filter'
							),
							array
							( // Vendor box
								'name'     => 'invoice_id',
								'id'     => 'txt_invoice',
								'value'    => $invoice_id,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 10,
								'tab_index' => 10,
								'style' => 'filter'
							),
							array
							(
								'type' => 'link',
								'id' => 'lnk_property',
								'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction' => 'property.uilocation.index',
									'lookup'  	=> 1,
									'type_id'  	=> 1,
									'lookup_name'  	=> 0,
								))."','Search','left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes')",
								'value' => lang('property'),
								'tab_index' => 11,
								'style' => 'filter'
							),
							array
							( // txt Facilities Management
								'name'     => 'loc1_name',
								'id'     => 'txt_loc1_name',
								'value'    => "",
								'type' => 'hidden',
								'size'    => 8,
								'style' => 'filter'
							),
							array
							( // txt Facilities Management
								'name'     => 'loc1',
								'id'     => 'txt_loc1',
								'value'    => $loc1,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 8,
								'tab_index' => 12,
								'style' => 'filter'
							),
							array
							( // Voucher link
								'type' => 'link',
								'id' => 'lnk_voucher',
								'url' => "",
								'value' => lang('Voucher ID'),
								'tab_index' => 13,
								'style' => 'filter'
							),
							array
							( // Voucher box
								'name'     => 'voucher_id',
								'id'     => 'txt_voucher',
								'value'    => $voucher_id,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 8,
								'tab_index' => 14,
								'style' => 'filter'
							),
							array
							( //boton   SEARCH
								'id' => 'btn_search',
								'name' => 'search',
								'value'    => lang('search'),
								'type' => 'button',
								'tab_index' => 15,
								'style' => 'filter'
							),
							array
							( // boton exportar
								'type'	=> 'button',
								'id'	=> 'btn_export',
								'tab_index' => 16,
								'value'	=> lang('download')
							),
							array
							( //boton 	CATEGORY
								'id' => 'btn_cat_id',
								'name' => 'cat_id',
								'value'	=> lang('Category'),
								'type' => 'button',
								'tab_index' => 17,
								'style' => 'filter'
							),
							array
							( //boton 	OWNER
								'id' => 'btn_user_lid',
								'name' => 'user_lid',
								'value'	=> user_lid,
								'type' => 'button',
								'tab_index' => 18,
								'style' => 'filter'
							),
							array
							( //boton 	ACCOUNT
								'id' => 'btn_b_account_class',
								'name' => 'b_account_class',
								'value'	=> lang('No account'),
								'type' => 'button',
								'tab_index' => 19,
								'style' => 'filter'
							),
							array
							( //hidden paid
								'type'	=> 'hidden',
								'id'	=> 'paid',
								'name'	=> 'paid',
								'value'	=> $paid,
								'style' => 'filter'
							),
							array
							( 
								'id' => 'sel_ecodimb',
								'name' => 'ecodimb',
								'value'	=> lang('dimb'),
								'type' => 'select',
								'style' => 'filter',
								'values' => $values_combo_box[3],
								'onchange'=> 'onChangeSelect("ecodimb");',
								'tab_index' => 5
							),
						);
				}

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'		=> 'property.uiinvoice.index',
								'order'				=> $this->order,
								'sort'				=> $this->sort,
								'cat_id'			=> $this->cat_id,
								'user_lid'			=> $this->user_lid,
								'sub'				=> $this->sub,
								'query'				=> $this->query,
							//	'start'				=> $this->start,
								'paid'				=> $paid,
								'vendor_id'			=> $vendor_id,
								'workorder_id'		=> $workorder_id,
								'project_id'		=> $project_id,
								'start_date'		=> $start_date,
								'end_date'			=> $end_date,
								'filter'			=> $this->filter,
								'b_account_class'	=> $b_account_class,
								'ecodimb'			=> $ecodimb,
								'district_id'		=> $this->district_id
							)
						),
						'fields'	=> array
						(
							'field' => $field_invoice,
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $this->bocommon->select2String($values_combo_box[1],'lid','firstname','lastname')
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $this->bocommon->select2String($values_combo_box[2])
								)
							)
						)
					)
				);

			$periodization_list = execMethod('property.bogeneric.get_list', array('type'=>'periodization'));
			if($periodization_list)
			{
				array_unshift ($periodization_list,array('id' => '0', 'name' => lang('none')));
			}

			$jscode = <<<JS
			    var myPeriodizationDropDown = function(elCell, oRecord, oColumn, oData)
			   	{
JS;
			$jscode .= <<<JS
				var _label = new String(oData);
				tmp_count = oRecord._oData.counter_num;
				voucher_id = oRecord._oData.voucher_id_num
			    elCell.innerHTML = "<div id=\"divPeriodizationDropDown"+tmp_count+"\"></div>";

		  	    var tmp_button = new YAHOO.widget.Button({
		                 type:"menu",
		                 id:"oPeriodizationDropDown"+tmp_count,
		                 label: "<en>" +_label+"</en>",
		                 value: oData,
		                 container: "divPeriodizationDropDown"+tmp_count,
		                 menu: [

JS;
       	    foreach ($periodization_list as $key => $periodization_entry)
        	{
	 			$jscode_arr[] = "{ text: '{$periodization_entry['name']}', value: '{$periodization_entry['id']}', onclick: { fn: onPeriodizationDropDownItemClick, idvoucher: voucher_id, id: '{$periodization_entry['id']}'} }";
        	}

			$jscode_inner = implode(",\n",  $jscode_arr);
			$jscode .= <<<JS
			$jscode_inner
			]});

					//Define this variable in the window scope (GLOBAL)
					eval("window.oPeriodizationDropDown"+tmp_count+" = tmp_button");
				}
JS;
				$GLOBALS['phpgw']->js->add_code('', $jscode);

			} //-- of if( phpgw::get_var('phpgw_return_as') != 'json' )

			$content = array();
			//the first time, $content is empty, because $user_lid=''.In the seconfd time, user_lid=all; It is done using  base_java_url.
			$content = $this->bo->read_invoice($paid,$start_date,$end_date,$vendor_id,$loc1,$workorder_id,$voucher_id,$invoice_id,$ecodimb,$project_id);


			$uicols = array (
				'input_type'	=>	array
									(
										'hidden',
										'hidden',
										'hidden',
										'hidden',
										'hidden',
										'hidden',
										'hidden',
										'hidden',
										'hidden',
										'link',
										'link',
										'hidden',
										'hidden',
										'hidden',
										$paid?'varchar':'input',
										'varchar',
										'varchar',
										'varchar',
										'hidden',
										'varchar',
										'varchar',
										'varchar',
										'varchar',
										'varchar',
										$paid?'hidden':'input',
										$paid?'hidden':'input',
										'special',
										'special',
										'special',
										'special2'
										),
				'type'			=>	array
									(
										'number',
										'',
										'',
										'',
										'number',
										'number',
										'',
										'number',
										'',
										'url',
										'msg_box',
										'',
										'',
										'',
										$paid?'':'text',
										'',
										'text',
										'text',
										'',
										'',
										'',
										'',
										'',
										'',
										$paid?'':'checkbox',
										$paid?'':'radio',
										'',
										'',
										'',
										''
									),
				'col_name'		=>	array
									(
										'payment_date',
										'transfer',
										'kreditnota',
										'sign',
										'vendor_name',
										'counter_num',
										'counter',
										'voucher_id_num',
										'voucher_id',
										'voucher_id_lnk',
										'voucher_date_lnk',
										'sign_orig',
										'num_days_orig',
										'timestamp_voucher_date',
										'num_days',
										'amount_lnk',
										'currency',
										'vendor',
										'invoice_count',
										'invoice_count_lnk',
										'type_lnk',
										'period',
										'periodization',
										'periodization_start',
										'kreditnota_tmp',
										'sign_tmp',
										'janitor_lnk',
										'supervisor_lnk',
										'budget_responsible_lnk',
										'transfer_lnk'
									),
				'name'			=>	array
									(
										'payment_date',
										'dummy',
										'dummy',
										'dummy',
										'vendor',
										'counter',
										'counter',
										'voucher_id',
										'voucher_id',
										'voucher_id',
										'voucher_date',
										'sign_orig',
										'num_days',
										'timestamp_voucher_date',
										'num_days',
										'approved_amount',
										'currency',
										'vendor',
										'invoice_count',
										'invoice_count',
										'type',
										'period',
										'periodization',
										'periodization_start',
										'kreditnota',
										'empty_fild',
										'janitor',
										'supervisor',
										'budget_responsible',
										'transfer_id'
									),

				'formatter'		=>	array
									(
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'myFormatDate',
										'',
										'',
										'',
										'',
										'',
										$paid?'':'myPeriodDropDown',
										$paid?'':'myPeriodizationDropDown',
										$paid?'':'myPeriodization_startDropDown',
										'',
										'',
										'',
										'',
										'',
										''
									),

				'descr'			=>	array
									(
										'dummy',
										'dummy',
										'dummy',
										'dummy',
										'dummy',
										'dummy',
										'dummy',
										'dummy',
										'dummy',
										lang('voucher'),
										lang('Voucher Date'),
										'dummy',
										'dummy',
										'dummy',
										lang('Days'),
										lang('approved amount'),
										lang('currency'),
										lang('Vendor'),
										'dummy',
										lang('Count'),
										lang('Type'),
										lang('Period'),
										lang('periodization'),
										lang('periodization start'),
										lang('KreditNota'),
										lang('None'),
										lang('Janitor'),
										lang('Supervisor'),
										lang('Budget Responsible'),
										lang('Transfer')
									),
				'className'		=> 	array
									(
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										'centerClasss',
										'',
										'',
										'',
										$paid?'rightClasss':'',
										'rightClasss',
										'',
										'',
										'',
										'rightClasss',
										'',
										$paid?'centerClasss':'comboClasss',
										$paid?'centerClasss':'comboClasss',
										$paid?'centerClasss':'comboClasss',
										'centerClasss',
										'centerClasss',
										'',
										'',
										'centerClasss',
										'centerClasss'
									)
			);

//_debug_array($uicols);
			//url to detail of voucher
			$link_sub 	= 	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.list_sub','user_lid'=>$this->user_lid));

			if($paid)
			{
				$link_sub.="&paid=true";
			}

			$j=0;
			$count_uicols = count($uicols['name']);
			//---- llena DATATABLE-ROWS con los valores del READ
			if (isset($content) && is_array($content))
			{
				foreach($content as $invoices)
				{
					for ($i=0;$i<$count_uicols;$i++)
					{
						//values column kreditnota
						if($uicols['type'][$i]=='checkbox' && $uicols['col_name'][$i]=='kreditnota_tmp')
						{
							$datatable['rows']['row'][$j]['column'][$i]['value']	= 'true';
						}
						//values column sign
						else if($uicols['type'][$i]=='radio' && $uicols['col_name'][$i]=='sign_tmp')
						{
							$datatable['rows']['row'][$j]['column'][$i]['value']	= 'sign_none';
						}
						// others columnas
						else
						{
							$datatable['rows']['row'][$j]['column'][$i]['value']	= $invoices[$uicols['name'][$i]];
						}

						$datatable['rows']['row'][$j]['column'][$i]['name'] 		= $uicols['col_name'][$i];

						if($uicols['input_type'][$i]!='hidden')
						{
							//--- varchar--
							if($uicols['input_type'][$i]=='varchar' && $invoices[$uicols['name'][$i]])
							{
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'varchar';
							}

							//--- link--
							else if($uicols['input_type'][$i]=='link' && $invoices[$uicols['name'][$i]])
							{
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['link']				= '#';
								if($uicols['type'][$i]=='url')
								{
									$datatable['rows']['row'][$j]['column'][$i]['link']			= $link_sub."&voucher_id=".$invoices[$uicols['name'][$i]];
								}

								$datatable['rows']['row'][$j]['column'][$i]['target']			= '';
							}

							//--- special--
							else if($uicols['input_type'][$i]=='special')
							{

								// the same name of columns
								$type_sign =  $datatable['rows']['row'][$j]['column'][$i]['format'] = $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['for_json'] 			= $uicols['col_name'][$i];

								//LOGICA
								if(!$paid)
								{
									if( ($invoices['is_janitor']== 1 && $type_sign == 'janitor') || ($invoices['is_supervisor']== 1 && $type_sign == 'supervisor') || ($invoices['is_budget_responsible']== 1 && $type_sign == 'budget_responsible'))
									{
										if( ( (!$invoices['jan_date']) && $type_sign == 'janitor') || ((!$invoices['super_date']) && $type_sign == 'supervisor') || ((!$invoices['budget_date']) && $type_sign == 'budget_responsible'))
										{
											$datatable['rows']['row'][$j]['column'][$i]['name']			= 'sign_tmp';
											$datatable['rows']['row'][$j]['column'][$i]['type']			= 'radio';
											$datatable['rows']['row'][$j]['column'][$i]['value'] 		= ($type_sign == 'janitor'? 'sign_janitor':($type_sign == 'supervisor'? 'sign_supervisor' : 'sign_budget_responsible'));
											$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= "";
										}

										else if( (($invoices['janitor'] == $invoices['current_user']) && $type_sign == 'janitor')  || (($invoices['supervisor'] == $invoices['current_user']) && $type_sign == 'supervisor') || (($invoices['budget_responsible'] == $invoices['current_user']) && $type_sign == 'budget_responsible'))
										{
											$datatable['rows']['row'][$j]['column'][$i]['name'] 		= 'sign_tmp';
											$datatable['rows']['row'][$j]['column'][$i]['type'] 		= 'radio';
											$datatable['rows']['row'][$j]['column'][$i]['value']		= ($type_sign == 'janitor'? 'sign_janitor':($type_sign == 'supervisor'? 'sign_supervisor' : 'sign_budget_responsible'));
											$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= " checked ";
										}

										else
										{

											$datatable['rows']['row'][$j]['column'][$i]['name']			= '';
											$datatable['rows']['row'][$j]['column'][$i]['type']			= 'checkbox';
											$datatable['rows']['row'][$j]['column'][$i]['value']		= '';
											$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= " disabled=\"disabled\" checked ";
										}
									}
									else
									{
										if( (!$invoices['jan_date'] && $type_sign == 'janitor') || (!$invoices['super_date'] && $type_sign == 'supervisor') || (!$invoices['budget_date'] && $type_sign == 'budget_responsible') )
										{
										}
										else
										{
											$datatable['rows']['row'][$j]['column'][$i]['name']			= '';
											$datatable['rows']['row'][$j]['column'][$i]['type']			= 'checkbox';
											$datatable['rows']['row'][$j]['column'][$i]['value']		= '';
											$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= " disabled=\"disabled\" checked ";
										}
									}
									$datatable['rows']['row'][$j]['column'][$i]['value2'] = $type_sign == 'janitor'? $invoices['janitor']: ($type_sign == 'supervisor'? $invoices['supervisor'] : $invoices['budget_responsible']);
									$datatable['rows']['row'][$j]['column'][$i]['value0'] = $type_sign == 'janitor'? $invoices['jan_date']: ($type_sign == 'supervisor'? $invoices['super_date'] : $invoices['budget_date']);
								}
								else //if($paid)
								{
									$datatable['rows']['row'][$j]['column'][$i]['value2'] = ($type_sign == 'janitor'? ($invoices['jan_date']." - ".$invoices['janitor']): ($type_sign == 'supervisor'? ($invoices['super_date']." - ".$invoices['supervisor']) : ($invoices['budget_date']." - ".$invoices['budget_responsible'])));
								}
							}
							//---- speciual2----
							else if($uicols['input_type'][$i]=='special2')
							{
								// the same name of columns
								$type_sign =  $datatable['rows']['row'][$j]['column'][$i]['format'] = $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['for_json'] 			= $uicols['col_name'][$i];


								if(!$paid)
								{
									if( ($invoices['is_transfer']==1))
									{
										if(!$invoices['transfer_date'])
										{
											$datatable['rows']['row'][$j]['column'][$i]['name']			= 'transfer_tmp';
											$datatable['rows']['row'][$j]['column'][$i]['type']			= 'checkbox';
											$datatable['rows']['row'][$j]['column'][$i]['value'] 		= 'true';
											$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= "";
										}

										else
										{
											$datatable['rows']['row'][$j]['column'][$i]['name']			= 'transfer_tmp';
											$datatable['rows']['row'][$j]['column'][$i]['type']			= 'checkbox';
											$datatable['rows']['row'][$j]['column'][$i]['value'] 		= 'true';
											$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= " checked ";
										}
									}
									else
									{
										if( ($invoices['transfer_id']!=''))
										{
											$datatable['rows']['row'][$j]['column'][$i]['name']			= '';
											$datatable['rows']['row'][$j]['column'][$i]['type']			= 'checkbox';
											$datatable['rows']['row'][$j]['column'][$i]['value'] 		= '';
											$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= " disabled=\"disabled\" checked ";

										}
									}

									$datatable['rows']['row'][$j]['column'][$i]['value2'] = $invoices['transfer_id'];
								}
								else //if($paid)
								{
									$datatable['rows']['row'][$j]['column'][$i]['value2'] = $invoices['transfer_date']." - ".$invoices['transfer_id'];

								}
							}


							else //for input controls
							{
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= $uicols['input_type'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['type'] 			= $uicols['type'][$i];

								if($datatable['rows']['row'][$j]['column'][$i]['type']=='text')
								{
									$datatable['rows']['row'][$j]['column'][$i]['extra_param'] 		= "size='1' ";
								}
								else if($uicols['col_name'][$i]=='kreditnota_tmp' && $invoices[$uicols['name'][$i]] == '1')
								{
									$datatable['rows']['row'][$j]['column'][$i]['extra_param'] = " checked ";
								}
								else 
								{
									$datatable['rows']['row'][$j]['column'][$i]['extra_param'] = " ";
								}
							}
						}
						else
						{
							$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'hidden';
							$datatable['rows']['row'][$j]['column'][$i]['type'] 			= $uicols['type'][$i];
						}

					}
					$j++;
				}
			}

			//---- RIGHTS
			$datatable['rowactions']['action'] = array();
			if(!$paid)
			{
				$parameters = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'voucher_id',
								'source'	=> 'voucher_id_num'
							),
						)
					);


				$datatable['rowactions']['action'][] = array
					(
						'my_name'			=> 'forward',
						'text' 			=> lang('forward'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uiinvoice.forward',
							'target'			=> '_lightbox'
						)),
						'parameters'	=> $parameters
					);

				if($this->acl_delete)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'delete',
							'text' 			=> lang('delete'),
							'confirm_msg'	=> lang('do you really want to delete this entry'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiinvoice.delete',
							)),
							'parameters'	=> $parameters
						);
				}

				$datatable['rowactions']['action'][] = array
					(
						'my_name'			=> 'f',
						'text' 			=> lang('F'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uiinvoice.receipt',
							'target'		=> '_blank'
						)),
						'parameters'	=> $parameters
					);

				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiinvoice.add'
							))
						);
				}

				unset($parameters);

			}

			$uicols_count	= count($uicols['descr']);

			for ($i=0;$i<$uicols_count;$i++)
			{
				$datatable['headers']['header'][$i]['name'] 			= $uicols['col_name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]=='' ?  '""' : $uicols['formatter'][$i]);
				$datatable['headers']['header'][$i]['className']		= $uicols['className'][$i];
				$datatable['headers']['header'][$i]['editor']			= $uicols['editor'][$i];

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);

					$datatable['headers']['header'][$i]['sortable']			= false;

					//-- ordemanientos particulares para cada columna
					if($uicols['name'][$i]=='voucher_id')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'bilagsnr';
					}
					else if($uicols['name'][$i]=='voucher_date')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'fakturadato';
					}
					else if($uicols['name'][$i]=='vendor_id')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'spvend_code';
					}
					else if($uicols['name'][$i]=='janitor')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'oppsynsigndato';
					}
					else if($uicols['name'][$i]=='supervisor')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'saksigndato';
					}
					else if($uicols['name'][$i]=='budget_responsible')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'budsjettsigndato';
					}
					else if($uicols['name'][$i]=='period')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'periode';
					}
				}
				else
				{
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			//for maintein page number in datatable
			if ( !(phpgw::get_var('start')) && !phpgw::get_var('order','string'))
			{
				//avoid ,in the last page, reformate paginator when records are lower than records_returned
				if(count($content) <= $datatable['pagination']['records_limit'])
				{
					$datatable['pagination']['records_returned']= count($content);
				}
				else
				{
					$datatable['pagination']['records_returned']= $datatable['pagination']['records_limit'];
				}

				$datatable['sorting']['currentPage']	= 1;
				$datatable['sorting']['order'] 			= 'voucher_id_lnk'; // name key Column in myColumnDef
				$datatable['sorting']['sort']			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['currentPage']	= phpgw::get_var('currentPage');
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort']			= phpgw::get_var('sort', 'string'); // ASC / DESC
				$datatable['pagination']['records_returned']= phpgw::get_var('recordsReturned', 'int');
			}



			//-- BEGIN----------------------------- JSON CODE ------------------------------
			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'currentPage'		=> $datatable['sorting']['currentPage'],
					'records'			=> array(),
					'sum_amount'		=> $this->bo->sum_amount,
					'periodization'		=> $paid ? array() : $periodization_list
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				$k=0;
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						//-- links a otros modulos
						if($column['format']== "link")
						{
							if($column['name'] == 'voucher_id_lnk')
							{
								$_value = isset($content[$k]['voucher_out_id']) && $content[$k]['voucher_out_id'] ? $content[$k]['voucher_out_id'] : $column['value'];
								$json_row[$column['name']] = "<a target='".$column['target']."' href='".$column['link']."' >".$_value."</a>";
							}
							else
							{
								$json_row[$column['name']] = "<a target='".$column['target']."' href='".$column['link']."' >".$column['value']."</a>";
							}
						}
						else if($column['format']== "input")
						{
							//this class was used for botton selectAll in Footer Datatable
							if($column['name']=='sign_tmp')
							{
								$json_row[$column['name']] = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]' class=' signClass' type='".$column['type']."' value='".$column['value']."' ".$column['extra_param']."/>";
							}
							else if($column['name']=='kreditnota_tmp')
							{
								$json_row[$column['name']] = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]' class=' kreditnota_tmp' type='".$column['type']."' value='".$column['value']."' ".$column['extra_param']."/>";
							}
							else
							{
								$json_row[$column['name']] = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]' class='myValuesForPHP' type='".$column['type']."' value='".$column['value']."' ".$column['extra_param']."/>";
							}
						}
						else if($column['format']== "varchar")
						{
							$json_row[$column['name']] = $column['value'];
						}
						else if($column['format']== "janitor" || $column['format']== "supervisor" || $column['format']== "budget_responsible" || $column['format']== "transfer_id" )
						{
							$tmp_lnk = "";
							//this class was used for botton selectAll in Footer Datatable
							$class = $column['format']."Class";
							if($column['type']!='')
							{
								if($column['name']=='')
								{
									$tmp_lnk = " <input name='".$column['name']."' type='".$column['type']."' value='".$column['value']."' ".$column['extra_param']." class='".$class."' />";
								}
								else
								{
									$tmp_lnk = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]' class='".$class."' type='".$column['type']."' value='".$column['value']."' ".$column['extra_param']."/>";
								}
							}

							$json_row[$column['for_json']] = $column['value0'].$tmp_lnk . $column['value2'];
						}

						else // for  hidden
						{
							if($column['type']== 'number') // for values for delete,edit.
							{
								$json_row[$column['name']] = $column['value'];
							}
							else if($column['name']== "sign_orig")
							{
								$json_row[$column['name']] = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]'  class='myValuesForPHP sign_origClass'  type='hidden' value='".$column['value']."'/>";
							}
							else if($column['name']== "sign")
							{
								$json_row[$column['name']] = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]'  class='myValuesForPHP sign_tophp'  type='hidden' value='".$column['value']."'/>";
							}
							else if($column['name']== "kreditnota")
							{
								$json_row[$column['name']] = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]'  class='myValuesForPHP kreditnota_tophp'  type='hidden' value='".$column['value']."'/>";
							}
							else if($column['name']== "transfer")
							{
								$json_row[$column['name']] = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]'  class='myValuesForPHP transfer_tophp'  type='hidden' value='".$column['value']."'/>";
							}
							else // for imput hiddens  (type == "")
							{
								$json_row[$column['name']] = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]'  class='myValuesForPHP '  type='hidden' value='".$column['value']."'/>";
							}
						}
					}
					$json['records'][] = $json_row;
					$k++;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json['rights'] = $datatable['rowactions']['action'];
			}

			// message when editting & deleting records
			if(isset($receipt) && is_array($receipt) && count($receipt))
			{
				$json['message'][] = $receipt;
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			//// cramirez: necesary for include a partucular js
			phpgwapi_yui::load_widget('loader');
			//cramirez: necesary for use opener . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			//FIXME this one is only needed when $lookup==true - so there is probably an error
			phpgwapi_yui::load_widget('animation');


			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			$appname	= lang('invoice');
			$function_msg	= lang('list voucher');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			if ($paid)
			{
				$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'invoice.paid.index', 'property' );
			}
			else
			{
				$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'invoice.index', 'property' );
			}

		}

		function list_sub()
		{
			$paid 		= phpgw::get_var('paid', 'bool');
			$values		= phpgw::get_var('values');
			$voucher_id = phpgw::get_var('voucher_id');

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uiinvoice.list_sub',
						'order'			=> $this->order,
						'sort'			=> $this->sort,
						'cat_id'		=> $this->cat_id,
						'user_lid'		=> $this->user_lid,
						'sub'			=> $this->sub,
				//		'query'			=> $this->query,
				//		'start'			=> $this->start,
						'paid'			=> $paid,
						'voucher_id'	=> $voucher_id,
				//		'query'			=> $voucher_id
					));
				$datatable['config']['allow_allrows'] = false;


				$datatable['config']['base_java_url'] = "menuaction:'property.uiinvoice.list_sub',"
					."order:'{$this->order}',"
					."sort:'{$this->sort}',"
					."cat_id: '{$this->cat_id}',"
					."user_lid:'{$this->user_lid}',"
					."sub:'{$this->sub}',"
				//	."query:'{$this->query}',"
				//	."start:'{$this->start}',"
					."paid:'{$paid}',"
					."voucher_id:'{$voucher_id}'";
				//	."query:'{$voucher_id}'";

				$field_invoice = array
					(array
					( // mensaje
						'type'	=> 'label',
						'id'	=> 'msg_header',
						'value'	=> '',
						'style' => 'filter'
					),
					array
					( // boton exportar
						'type'	=> 'button',
						'id'	=> 'btn_export',
						'tab_index' => 3,
						'value'	=> lang('download')
					),
					array
					( // boton exportar
						'type'	=> 'button',
						'id'	=> 'btn_done',
						'tab_index' => 2,
						'value'	=> lang('done')
					),
					array
					( // boton SAVE
						'type'	=> 'button',
						'id'	=> 'btn_save',
						//'name' => 'save',
						'tab_index' => 1,
						'value'	=> lang('save')
					),
					array
					( //container of  control's Form
						'type'	=> 'label',
						'id'	=> 'controlsForm_container',
						'value'	=> ''
					)

				);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'		=> 'property.uiinvoice.list_sub',
								'user_lid'			=> $this->user_lid,
								'paid'				=> $paid,
								'voucher_id'		=> $voucher_id
							)
						),
						'fields'	=> array
						(
							'field' => $field_invoice
						)
					)
				);

			} //-- of if( phpgw::get_var('phpgw_return_as') != 'json' )

			//-- edicion de registro
			$values  = phpgw::get_var('values');
			$receipt = array();

			if( phpgw::get_var('phpgw_return_as') == 'json' && is_array($values) && isset($values))
			{
				if($this->bo->get_approve_role())
				{
					$receipt = $this->bo->update_invoice_sub($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('you are not approved for this task'));
				}
			}

			$content = array();
			if ($voucher_id)
			{
				$this->bo->allrows = true;
				$content = $this->bo->read_invoice_sub($voucher_id,$paid);
			}

			$sum=0;

			$dimb_list			= $this->bo->select_dimb_list();
			$tax_code_list		= $this->bo->tax_code_list();
			$_link_order 		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.view_order'));
			$_link_claim 		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitenant_claim.check'));
			
			foreach ($content as &$entry)
			{
				$sum						+= $entry['amount'];
				$entry['amount'] 			= number_format($entry['amount'], 2, ',', '');
				$entry['paid']				= $paid;
				$entry['dimb_list']			= $this->bocommon->select_list($entry['dimb'], $dimb_list);
				$entry['tax_code_list']		= $this->bo->tax_code_list($entry['tax_code'], $tax_code_list);
				$entry['link_order'] 		= $_link_order;
				$entry['link_claim'] 		= $_link_claim;
			}

			unset($entry);
			unset($_link_order);
			unset($_link_claim);
			unset($dimb_list);
			unset($tax_code_list);

			$uicols = array (
				array(
					'col_name'=>'workorder'		,'label'=>lang('Workorder'),	'className'=>'centerClasss', 'sortable'=>true,	'sort_field'=>'pmwrkord_code',	'visible'=>true),
				array(
					'col_name'=>'project_group','label'=>lang('project group'),	'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',				'visible'=>true),
				array(
					'col_name'=>'close_order',	'label'=>lang('Close order'),	'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',				'visible'=>true),
				array(
					'col_name'=>'change_tenant',	'label'=>lang('Charge tenant'),	'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',				'visible'=>true),
				array(
					'col_name'=>'invoice_id',		'label'=>lang('Invoice Id'),	'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',				'visible'=>true),
				array(
					'col_name'=>'budget_Account',	'label'=>lang('Budget account'),'className'=>'centerClasss', 'sortable'=>true,	'sort_field'=>'spbudact_code',	'visible'=>true),
				array(
					'col_name'=>'sum',				'label'=>lang('Sum'),			'className'=>'rightClasss', 'sortable'=>true,	'sort_field'=>'belop',			'visible'=>true),
				array(
					'col_name'=>'approved_amount',	'label'=>lang('approved amount'),'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',	'visible'=>true),
				array(
					'col_name'=>'currency',			'label'=>lang('currency'),		'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',			'visible'=>true),
				array(
					'col_name'=>'dim_A',			'label'=>lang('Dim A'),			'className'=>'centerClasss', 'sortable'=>true,	'sort_field'=>'dima',			'visible'=>true),
				array(
					'col_name'=>'dim_B',			'label'=>lang('Dim B'),			'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',				'visible'=>true),
				array(
					'col_name'=>'dim_D',			'label'=>lang('Dim D'),			'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',				'visible'=>true),
				array(
					'col_name'=>'Tax_code',			'label'=>lang('Tax code'),		'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',				'visible'=>true),
				array(
					'col_name'=>'Remark',			'label'=>lang('Remark'),		'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',				'visible'=>true),
				array(
					'col_name'=>'external_ref'		,'label'=>lang('external ref'),	'className'=>'centerClasss', 'sortable'=>false,	'sort_field'=>'',			'visible'=>true),
				array(
					'col_name'=>'counter',		'visible'=>false),
				array(
					'col_name'=>'id',		'visible'=>false),
				array(
					'col_name'=>'_external_ref',		'visible'=>false)
				);


	//		$config		= CreateObject('phpgwapi.config','property');
	//		$config->read();
			$custom_config	= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$baseurl_invoice = isset($custom_config->config_data['common']['baseurl_invoice']) && $custom_config->config_data['common']['baseurl_invoice'] ? $custom_config->config_data['common']['baseurl_invoice'] : '';
			$lang_picture = lang('picture');

			$j=0;
			//---- llena DATATABLE-ROWS con los valores del READ
			$workorders = array();
			foreach($content as $invoices)
			{
				for ($i=0;$i<count($uicols);$i++)
				{
					$json_row[$uicols[$i]['col_name']] = "";

					if($i == 0)
					{
						$json_row[$uicols[$i]['col_name']] .= " <input name='values[counter][".$j."]' id='values[counter][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$invoices['counter']."'/>";
						$json_row[$uicols[$i]['col_name']] .= " <input name='values[id][".$j."]' id='values[id][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$invoices['id']."'/>";
						$json_row[$uicols[$i]['col_name']] .= " <input name='values[workorder_id][".$j."]' id='values[workorder_id][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$invoices['workorder_id']."'/>";
						$json_row[$uicols[$i]['col_name']] .= " <a target='_blank' href='".$invoices['link_order'].'&order_id='.$invoices['workorder_id']."'>".$invoices['workorder_id']."</a>";
					}
					else if(($i == 1))
					{
						$json_row[$uicols[$i]['col_name']]  .= $invoices['project_group'];
					}
					else if(($i == 2))
					{
						if(!isset($invoices['workorder_id']) || !$invoices['workorder_id'])
						{
							//nothing
						}
						else if(!$invoices['paid'] && !array_key_exists($invoices['workorder_id'], $workorders))
						{
							$_checked = '';
							if($invoices['closed']== 1)
							{
								$_checked = 'checked="checked"';
							}
							else if($invoices['project_type_id']== 1 && !$invoices['periodization_id']) // operation projekts
							{
								$_checked = 'checked="checked"';
							}
							else if(!$invoices['continuous'])
							{
								$_checked = 'checked="checked"';
							}

							$json_row[$uicols[$i]['col_name']]  .= " <input name='values[close_order_orig][{$j}]' id='values[close_order_orig][{$j}]' class='myValuesForPHP ' type='hidden' value='{$invoices['closed']}'/>";
							$json_row[$uicols[$i]['col_name']]  .= " <input name='values[close_order_tmp][{$j}]' id='values[close_order_tmp][{$j}]' class='close_order_tmp transfer_idClass' type='checkbox' value='true' {$_checked}/>";
							$json_row[$uicols[$i]['col_name']]  .= " <input name='values[close_order][{$j}]' id='values[close_order][{$j}]' class='myValuesForPHP close_order' type='hidden' value=''/>";
						}
						else
						{
							if($invoices['closed']== 1)
							{
								$json_row[$uicols[$i]['col_name']]  .= "<b>x</b>";
							}
						}
					}
					else if(($i == 3))
					{
						if($invoices['charge_tenant'] == 1)
						{
							if(!$invoices['claim_issued'])
							{
								$_workorder = execMethod('property.soworkorder.read_single', $invoices['workorder_id']);
								$json_row[$uicols[$i]['col_name']] .= " <a target='_blank' href='".$invoices['link_claim'].'&project_id='.$_workorder['project_id']."'>".lang('Claim')."</a>";
								unset($_workorder);
							}
							else
							{
								$json_row[$uicols[$i]['col_name']]  .= "<b>x</b>";
							}
						}
						else
						{
							$json_row[$uicols[$i]['col_name']]  .= "<b>-</b>";
						}

					}
					else if(($i == 4))
					{
						$json_row[$uicols[$i]['col_name']]  .= $invoices['invoice_id'];
					}

					else if(($i == 5))
					{
						if($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']] .= $invoices['budget_account'];
						}
						else
						{
							$json_row[$uicols[$i]['col_name']]  .= " <input name='values[budget_account][".$j."]' id='values[budget_account][".$j."]'  class='myValuesForPHP'  type='text' size='7' value='".$invoices['budget_account']."'/>";
						}
					}

					else if(($i == 6))
					{
						$json_row[$uicols[$i]['col_name']]  .= $invoices['amount'];
					}

					else if(($i == 7))
					{

						if($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']] .= $invoices['approved_amount'];
						}
						else
						{
							$json_row[$uicols[$i]['col_name']]  .= " <input name='values[approved_amount][".$j."]' id='values[approved_amount][".$j."]'  class='myValuesForPHP'  type='text' size='7' value='".$invoices['approved_amount']."'/>";
						}


					}

					else if(($i == 8))
					{
						$json_row[$uicols[$i]['col_name']]  .= $invoices['currency'];
					}

					else if(($i == 9))
					{
						if($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']]  .= $invoices['dima'];
						}
						else
						{
							$json_row[$uicols[$i]['col_name']]  .= " <input name='values[dima][".$j."]' id='values[dima][".$j."]'  class='myValuesForPHP'  type='text' size='7' value='".$invoices['dima']."'/>";
						}
					}
					else if(($i == 10))
					{
						if($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']]  .= $invoices['dimb'];
						}
						else
						{

							$json_row[$uicols[$i]['col_name']]  .= " <select name='values[dimb_tmp][".$j."]' id='values[dimb_tmp][".$j."]'  class='dimb_tmp'><option value=''></option>";

							for($k = 0 ;$k < count($invoices['dimb_list']) ; $k++)
							{
								if(isset($invoices['dimb_list'][$k]['selected']) && $invoices['dimb_list'][$k]['selected']!="")
								{
									$json_row[$uicols[$i]['col_name']]  .= "<option value='".$invoices['dimb_list'][$k]['id']."' selected >".$invoices['dimb_list'][$k]['name']."</option>";
								}
								else
								{
									$json_row[$uicols[$i]['col_name']]  .= "<option value='".$invoices['dimb_list'][$k]['id']."'>".$invoices['dimb_list'][$k]['name']."</option>";
								}
							}
							$json_row[$uicols[$i]['col_name']]  .="</select>";
							$json_row[$uicols[$i]['col_name']]  .= " <input name='values[dimb][".$j."]' id='values[dimb][".$j."]'  class='myValuesForPHP dimb'  type='hidden' value=''/>";

						}
					}
					else if(($i == 11))
					{
						if($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']]  .= $invoices['dimd'];
						}
						else
						{
							$json_row[$uicols[$i]['col_name']]  .= " <input name='values[dimd][".$j."]' id='values[dimd][".$j."]'  class='myValuesForPHP'  type='text' size='4' value='".$invoices['dimd']."'/>";
						}
					}
					else if(($i == 12))
					{
						if($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']]  .= $invoices['tax_code'];
						}
						else
						{

							$json_row[$uicols[$i]['col_name']]  .= " <select name='values[tax_code_tmp][".$j."]' id='values[tax_code_tmp][".$j."]'  class='tax_code_tmp'><option value=''></option>";

							for($k = 0 ;$k < count($invoices['tax_code_list']) ; $k++)
							{
								if(isset($invoices['tax_code_list'][$k]['selected']) && $invoices['tax_code_list'][$k]['selected']!="")
								{
									$json_row[$uicols[$i]['col_name']]  .= "<option value='".$invoices['tax_code_list'][$k]['id']."'  selected >".$invoices['tax_code_list'][$k]['id']."</option>";
								}
								else
								{
									$json_row[$uicols[$i]['col_name']]  .= "<option value='".$invoices['tax_code_list'][$k]['id']."'>".$invoices['tax_code_list'][$k]['id']."</option>";
								}
							}
							$json_row[$uicols[$i]['col_name']]  .="</select>";
							$json_row[$uicols[$i]['col_name']]  .= " <input name='values[tax_code][".$j."]' id='values[tax_code][".$j."]'  class='myValuesForPHP tax_code'  type='hidden' value=''/>";

						}
					}
					else if(($i == 13))
					{
						if($invoices['remark'] == true)
						{
							$json_row[$uicols[$i]['col_name']] .= " <a href=\"javascript:openwindow('".$GLOBALS['phpgw']->link('/index.php', array
								(
									'menuaction'=> 'property.uiinvoice.remark',
									'id'		=> $invoices['id'],
									'paid'		=> $invoices['paid']
								)). "','550','400')\" >".lang('Remark')."</a>";
						}
						else
						{
							$json_row[$uicols[$i]['col_name']]  .= "<b>-</b>";
						}
					}
					else if(($i == 14))
					{
						if(isset($invoices['external_ref']) && $invoices['external_ref'])
						{
							//	$json_row[$uicols[$i]['col_name']] = " <a target='_blank' href='".$baseurl_invoice. $invoices['external_ref']."'>{$lang_picture}</a>";
							$json_row[$uicols[$i]['col_name']] = " <a href=\"javascript:openwindow('{$baseurl_invoice}{$invoices['external_ref']}','640','800')\" >{$lang_picture}</a>";
						}
						else
						{
							$json_row[$uicols[$i]['col_name']]  .= "<b>-</b>";
						}
					}
					else if($i == 15)
					{
						$json_row[$uicols[$i]['col_name']]  = $invoices['counter'];
					}
					else if($i == 16)
					{
						$json_row[$uicols[$i]['col_name']]  = $invoices['id'];
					}
					else if($i == 17)
					{
						$json_row[$uicols[$i]['col_name']]  = $invoices['external_ref'];
					}
				}

				if($invoices['workorder_id'])
				{
					$workorders[$invoices['workorder_id']] = true;
				}

				$datatable['rows']['row'][] = $json_row;
				$j++;
			}


			$current_Consult = array ();
			for($i=0;$i<2;$i++)
			{
				if($i==0)
				{
					$current_Consult[] = array(lang('Vendor'),$content[0]['vendor']);
				}
				if($i==1)
				{
					$current_Consult[] = array(lang('Voucher Id'),$content[0]['voucher_out_id'] ? $content[0]['voucher_out_id'] : $content[0]['voucher_id']);
				}
			}

			//no grants
			$datatable['rowactions']['action'] = array();

			for ($i=0;$i<count($uicols);$i++)
			{
				$datatable['headers']['header'][$i]['name'] 			= $uicols[$i]['col_name'];
				$datatable['headers']['header'][$i]['text'] 			= $uicols[$i]['label'];
				$datatable['headers']['header'][$i]['formatter'] 		= ($uicols[$i]['formatter']=='' ?  '""' : $uicols[$i]['formatter']);
				$datatable['headers']['header'][$i]['className']		= $uicols[$i]['className'];
				$datatable['headers']['header'][$i]['visible'] 			= $uicols[$i]['visible'];
				$datatable['headers']['header'][$i]['sortable']			= $uicols[$i]['sortable'];
				$datatable['headers']['header'][$i]['sort_field']		= $uicols[$i]['sort_field'];
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= count($content);
			$datatable['pagination']['records_returned']= count($content);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== 0) && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= $uicols[11]['col_name']; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}


			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array()
				);

			// values for datatable
			$json['records']	= $datatable['rows']['row'];

			// right in datatable
			$json['rights']	= $datatable['rowactions']['action'];

			$json['sum']		= number_format($sum, 2, ',', '');

			// message when editting & deleting records
			if(isset($receipt) && is_array($receipt) && count($receipt))
			{
				$json['message']= $GLOBALS['phpgw']->common->msgbox($this->bocommon->msgbox_data($receipt));
			}

			// query parameters
			if(isset($current_Consult) && is_array($current_Consult))
			{
				$json['current_consult'] = $current_Consult;
			}


			//-------------- menu
			$datatable['rowactions']['action'] = array();

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'id',
							'source'	=> 'id'
						),
					)
				);

			$parameters2 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'docid',
							'source'	=> '_external_ref'
						),
					)
				);


			if($this->acl_read && $baseurl_invoice)
			{
				$_baseurl_invoice = rtrim($baseurl_invoice,"?{$parameters2['parameter'][0]['name']}=");
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'picture',
						'text'			=> $lang_picture,
						'action'		=> "{$_baseurl_invoice}?target=_blank",
						'parameters'	=> $parameters2
					);
			}

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text'			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'		=> 'property.uiinvoice.edit',
							'voucher_id'		=> $voucher_id,
							'user_lid'			=> $this->user_lid,
							'target'			=> '_tinybox',
							'paid'				=> $paid
						)),
						'parameters'	=> $parameters
					);
			}


			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json['rights'] = $datatable['rowactions']['action'];
			}

			//--------------

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			//FIXME this one is only needed when $lookup==true - so there is probably an error
			phpgwapi_yui::load_widget('animation');

			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');


			$GLOBALS['phpgw']->js->validate_file( 'tinybox2', 'packed', 'phpgwapi' );
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');



			//Title of Page
			$appname = lang('location');
			if ($paid)
			{
				$function_msg	= lang('list paid invoice');
			}
			else
			{
				$function_msg	= lang('list invoice');
			}
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'invoice.list_sub', 'property' );
		}

		/**
		 * Edit single line within a voucher
		 *
		 */

		public function edit()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] =  true;

			$paid 		= phpgw::get_var('paid', 'bool');
			$id			= phpgw::get_var('id', 'int', 'GET' , 0);
			$user_lid	= phpgw::get_var('user_lid', 'string', 'GET');
			$voucher_id	= phpgw::get_var('voucher_id', 'int', 'GET');
			$redirect	= false;

			$role_check = array
			(
				'is_janitor' 				=> lang('janitor'),
				'is_supervisor' 			=> lang('supervisor'),
				'is_budget_responsible' 	=> lang('b - responsible')
			);

			$approve = $this->bo->get_approve_role();

			$values	= phpgw::get_var('values');

			$receipt = array();
			if (isset($values['save']))
			{
				$values['project_group'] = phpgw::get_var('project_group', 'int', 'POST');
				if($GLOBALS['phpgw']->session->is_repost())
				{
					$receipt['error'][]=array('msg'=>lang('repost'));
				}

				if(!$approve)
				{
					$receipt['error'][]=array('msg'=>lang('you are not approved for this task'));
				}

				if(!isset($values['process_log']) || !$values['process_log'])
				{
//					$receipt['error'][]=array('msg'=>lang('Missing log message'));
				}

				if($values['approved_amount'])
				{
					$values['approved_amount'] 		= str_replace(' ','',$values['approved_amount']);
					$values['approved_amount'] 		= str_replace(',','.',$values['approved_amount']);
					if( isset($values['order_id']) && $values['order_id'] && !execMethod('property.soXport.check_order',$values['order_id']) )
					{
						$receipt['error'][]=array('msg'=>lang('no such order: %1',$values['order_id']));
					}
				}
				else
				{
					unset($values['split_amount']);
					unset($values['split_line']);
				}

				if(isset($values['split_line']) && isset($values['split_amount']) && $values['split_amount'])
				{
					$values['split_amount'] 		= str_replace(' ','',$values['split_amount']);
					$values['split_amount'] 		= str_replace(',','.',$values['split_amount']);
					if(!is_numeric($values['split_amount']))
					{
						$receipt['error'][]=array('msg'=>lang('Not a valid amount'));
					}
				}

				if (!$receipt['error'])
				{
					$redirect = true;
					$values['id'] = $id;
					$line = $this->bo->update_single_line($values,$paid);
				}
			}

			$line = $this->bo->get_single_line($id, $paid);

//			_debug_array($line);

			$approved_list = array();

			$approved_list[] = array
			(
				'role'		=> $role_check['is_janitor'],
				'initials'	=> $line['janitor'] ? $line['janitor'] : '',
				'date'		=> $line['oppsynsigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $line['oppsynsigndato'] ) ) :''
			);
			$approved_list[] = array
			(
				'role'		=> $role_check['is_supervisor'],
				'initials'	=> $line['supervisor'] ? $line['supervisor'] : '',
				'date'		=> $line['saksigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $line['saksigndato'] ) ) :''
			);
			$approved_list[] = array
			(
				'role'		=> $role_check['is_budget_responsible'],
				'initials'	=> $line['budget_responsible'] ? $line['budget_responsible'] : '',
				'date'		=> $line['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $line['budsjettsigndato'] ) ) :''
			);

			$my_initials = $GLOBALS['phpgw_info']['user']['account_lid'];

			foreach($approve as &$_approve)
			{
				if($_approve['id'] == 'is_janitor' && $my_initials == $line['janitor'] && $line['oppsynsigndato'])
				{
					$_approve['selected'] = 1;
					$sign_orig = 'is_janitor';
				}
				else if($_approve['id'] == 'is_supervisor' && $my_initials == $line['supervisor'] && $line['saksigndato'])
				{
					$_approve['selected'] = 1;
					$sign_orig = 'is_supervisor';
				}
				else if($_approve['id'] == 'is_budget_responsible' && $my_initials == $line['budget_responsible'] && $line['budsjettsigndato'])
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
					if(($my_initials == $line['janitor'] && $line['oppsynsigndato']) || !$line['oppsynsigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
				if($_approve['id'] == 'is_supervisor')
				{
					if(($my_initials == $line['supervisor'] && $line['saksigndato']) || !$line['saksigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
				if($_approve['id'] == 'is_budget_responsible')
				{
					if(($my_initials == $line['budget_responsible'] && $line['budsjettsigndato']) || !$line['budsjettsigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
			}

			$process_code_list = execMethod('property.bogeneric.get_list', array(
				'type'		=> 'voucher_process_code',
				'selected'	=> isset($values['process_code']) ? $values['process_code'] : $line['process_code']));

			$project_group_data = $this->bocommon->initiate_project_group_lookup(array(
				'project_group'			=> $values['project_group']?$values['project_group']:$line['project_group'],
				'project_group_descr'	=> $values['project_group_descr']));

			$data = array
			(
					'redirect'				=> $redirect ? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.list_sub', 'user_lid' => $user_lid, 'voucher_id' => $voucher_id, 'paid' => $paid)) : null,
					'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($receipt)),
					'from_name'				=> $GLOBALS['phpgw_info']['user']['fullname'],
					'form_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiinvoice.edit', 'id' => $id, 'user_lid' => $user_lid, 'voucher_id' => $voucher_id)),
					'approve_list'			=> $approve_list,
					'approved_list'			=> $approved_list,
					'sign_orig'				=> $sign_orig,
					'my_initials'			=> $my_initials,
					'process_code_list' 	=> $process_code_list,
					'project_group_data'	=> $project_group_data,
					'order_id'				=> $line['order_id'],
					'value_amount'			=> $line['amount'],
					'value_approved_amount'	=> $line['approved_amount'],
					'value_currency'		=> $line['currency'],
					'value_process_log'		=> isset($values['process_log']) && $values['process_log'] ? $values['process_log'] : $line['process_log'],
					'paid'					=> $paid
			);

			$GLOBALS['phpgw']->xslttpl->add_file('invoice');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit' => $data));
		}

		function remark()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice'));
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$id 	= phpgw::get_var('id', 'int');
			$paid 	= phpgw::get_var('paid', 'bool');

			$text = $this->bo->read_remark($id,$paid);

			$html = '';
			if(stripos($text, '<table') )
			{
				$html = 1;
			}

			$data = array
			(
				'remark' => $text,
				'html'	=> $html
			);

			$appname	= lang('invoice');
			$function_msg	= lang('remark');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('remark' => $data));
		}

		function consume()
		{
			//-- captura datos de URL
			$start_date 	= phpgw::get_var('start_date');
			$end_date 		= phpgw::get_var('end_date');
			$vendor_id 		= phpgw::get_var('vendor_id', 'int');
			$workorder_id 	= phpgw::get_var('workorder_id', 'int');
			$loc1 			= phpgw::get_var('loc1');
			$district_id 	= phpgw::get_var('district_id', 'int');
			$b_account_class= phpgw::get_var('b_account_class', 'int');
			$b_account= phpgw::get_var('b_account', 'int');

			$b_account_class = $b_account_class ? $b_account_class : substr($b_account,0,2);

			$ecodimb 		= phpgw::get_var('ecodimb');

			//-- ubica focus del menu derecho
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::consume';

			//-- captura datos de URL
			$start_date	=urldecode($start_date);
			$end_date	=urldecode($end_date);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if(!$start_date)
			{
				//-- actual date
				$start_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,date("m"),date("d"),date("Y")),$dateformat);
				$end_date	= $start_date;
			}


			$datatable = array();
			$values_combo_box = array();
			$sum = 0;
			$vendor_name ="";

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']				= $this->bocommon->get_menu();

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uiinvoice.consume',
						'order'			=> $this->order,
						'sort'			=> $this->sort,
						'cat_id'		=> $this->cat_id,
						'district_id'	=> $district_id,
						'sub'			=> $this->sub,
						'query'			=> $this->query,
						'start'			=> $this->start,
						'filter'		=> $this->filter,
						'ecodimb'		=> $ecodimb
					));

				$datatable['config']['allow_allrows'] = false;

				$datatable['config']['base_java_url'] = "menuaction:'property.uiinvoice.consume',"
					."order: '{$this->order}',"
					."sort:'{$this->sort}',"
					."cat_id:'{$this->cat_id}',"
					."district_id:'{$district_id}',"
					."sub:'{$this->sub}',"
					."query:'{$this->query}',"
					."start:'{$this->start}',"
					."filter:'{$this->filter}',"
					."ecodimb:'{$ecodimb}',"
					."b_account_class:'{$b_account_class}'";

				$values_combo_box[0]  = $this->bo->select_category('',$this->cat_id);
				$default_value = array ('id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bocommon->select_district_list('select',$district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $this->bo->select_account_class($b_account_class);
				$default_value = array ('id'=>'','name'=>lang('No account'));
				array_unshift ($values_combo_box[2],$default_value);

				$values_combo_box[3]  = $this->bocommon->select_category_list(array('type'=>'dimb', 'selected' => $ecodimb));
				$default_value = array ('id'=>'','name'=>lang('no dimb'));
				array_unshift ($values_combo_box[3],$default_value);

				$field_invoice = array
					(
						array
						( // imag calendar1
							'type' 		=> 'img',
							'id'     	=> 'start_date-trigger',
						//	'src'    	=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
							'alt'		=> lang('Select date'),
							'tab_index' => 1,
							'style' 	=> 'filter'
						),
						array
						( // calendar1 start_date
							'type' 		=> 'text',
							'name'     	=> 'start_date',
							'id'     	=> 'start_date',
							'value'    	=> $start_date,
							'size'    	=> 7,
							'readonly' 	=> 'readonly',
							'tab_index' => 2,
							'style' 	=> 'filter'
						),
						array
						( // imag calendar2
							'type' 		=> 'img',
							'id'     	=> 'end_date-trigger',
						//	'src'    	=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
							'alt'		=> lang('Select date'),
							'tab_index' => 3,
							'style' 	=> 'filter'
						),
						array
						( // calendar2 start_date
							'type' 		=> 'text',
							'name'     	=> 'end_date',
							'id'     	=> 'end_date',
							'value'    	=> $end_date,
							'size'    	=> 7,
							'readonly' 	=> 'readonly',
							'tab_index' => 4,
							'style' 	=> 'filter'
						),
						array
						( // workorder link
							'type' 		=> 'link',
							'id' 		=> 'lnk_workorder',
							'url' 		=> "",
							'value' 	=> lang('Workorder ID'),
							'tab_index' => 5,
							'style' 	=> 'filter'
						),
						array
						( // workorder box
							'name'     	=> 'workorder_id',
							'id'     	=> 'txt_workorder',
							'value'    	=> $workorder_id,
							'type' 		=> 'text',
							'size'    	=> 10,
							'tab_index' => 6,
							//'readonly' => 'readonly',
							'style' 	=> 'filter'
						),
						array
						( //vendor link
							'type' 		=> 'link',
							'id' 		=> 'lnk_vendor',
							'url' 		=> "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' => 'property.uilookup.vendor',
							))."','Search','left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes')",
							'value' 	=> lang('Vendor'),
							'tab_index' => 7,
							'style' 	=> 'filter'
						),
						array
						( // this field hidden is necesary for avoid js error
							'name'     => 'vendor_name',
							'id'     => 'txt_vendor_name',
							'value'    => "",
							'type' => 'hidden',
							'size'    => 10,
							'style' => 'filter'
						),
						array
						( // Vendor box
							'name'		=> 'vendor_id',
							'id'     	=> 'txt_vendor',
							'value'    	=> $vendor_id,
							'type'		=> 'text',
							'size'		=> 10,
							'tab_index' => 8,
							//'readonly' => 'readonly',
							'style'		=> 'filter'
						),
						array
						(
							'type'		=> 'link',
							'id'		=> 'lnk_property',
							'url'		=> "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' => 'property.uilocation.index',
								'lookup'  	=> 1,
								'type_id'  	=> 1,
								'lookup_name'  	=> 0,
							))."','Search','left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes')",
							'value' => lang('property'),
							'tab_index' => 9,
							'style' => 'filter'
						),
						array
						( // txt Facilities Management
							'name'		=> 'loc1',
							'id'		=> 'txt_loc1',
							'value'		=> $loc1,
							'type'		=> 'text',
							'size'		=> 8,
							'tab_index' => 10,
							//'readonly' => 'readonly',
							'style'		=> 'filter'
						),
						array
						( //boton   SEARCH
							'id'		=> 'btn_search',
							'name'		=> 'search',
							'value'		=> lang('search'),
							'type'		=> 'button',
							'tab_index' => 11,
							'style'		=> 'filter'
						),
						array
						( //boton 	CATEGORY
							'id'		=> 'btn_cat_id',
							'name'		=> 'cat_id',
							'value'		=> lang('Category'),
							'type'		=> 'button',
							'tab_index' => 12,
							'style'		=> 'filter'
						),
						array
						( //boton 	DISTRICT
							'id'		=> 'btn_district_id',
							'name'		=> 'district_id',
							'value'		=> lang('District'),
							'type'		=> 'button',
							'tab_index' => 13,
							'style'		=> 'filter'
						),
						array
						( //boton 	ACCOUNT
							'id'		=> 'btn_b_account_class',
							'name'		=> 'b_account_class',
							'value'		=> lang('No account'),
							'type'		=> 'button',
							'tab_index' => 14,
							'style'		=> 'filter'
						),
						array
						( 
							'id' => 'sel_ecodimb',
							'name' => 'ecodimb',
							'value'	=> lang('dimb'),
							'type' => 'select',
							'style' => 'filter',
							'values' => $values_combo_box[3],
							'onchange'=> 'onChangeSelect("ecodimb");',
							'tab_index' => 5
						),
					);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'		=> 'property.uiinvoice.consume',
								'order'				=> $this->order,
								'sort'				=> $this->sort,
								'cat_id'			=> $this->cat_id,
								'district_id'		=> district_id,
								'sub'				=> $this->sub,
								'query'				=> $this->query,
								'start'				=> $this->start,
								'filter'			=> $this->filter,
								'ecodimb'			=> $ecodimb
							)
						),
						'fields'	=> array
						(
							'field' => $field_invoice,
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0])
								),
								array
								( //div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $this->bocommon->select2String($values_combo_box[2])
								)
							)
						)
					)
				);

			} //-- of if( phpgw::get_var('phpgw_return_as') != 'json' )

			if($vendor_id)
			{
				$contacts	= CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor',false);

				$lookup = array
					(
						'attributes' => array
						(
							0 => array
							(
								'column_name' => 'org_name'
							)
						)
					);

				$vendor			= $contacts->read_single(array('id' => $vendor_id), $lookup);

				if(is_array($vendor))
				{
					foreach($vendor['attributes'] as $attribute)
					{
						if($attribute['column_name']=='org_name')
						{
							$vendor_name=$attribute['value'];
							break;
						}
					}
				}
			}

			$current_Consult = array ();
			for($i=0;$i<3;$i++)
			{
				if($i==0 && $workorder_id != "")
				{
					$current_Consult[] = array(lang('Workorder ID'),$workorder_id);
				}
				if($i==1 && $vendor_name != "")
				{
					$current_Consult[] = array(lang('Vendor'),$vendor_name);
				}
				if($i==2 && $loc1 != "")
				{
					$current_Consult[] = array(lang('property'),$loc1);
				}

			}

			$content = $this->bo->read_consume($start_date,$end_date,$vendor_id,$loc1,$workorder_id,$b_account_class,$district_id,$ecodimb);

			$sum = 0;
			foreach ($content as & $entry)
			{
				$sum			= $sum + $entry['consume'];
				$entry['link_voucher'] 	= $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'		=> 'property.uiinvoice.index',
						'paid'				=> true,
				//		'user_lid'			=> 'all',
						'district_id'		=> $district_id,
						'b_account_class'	=> $b_account_class,
						'start_date'		=> $start_date,
						'end_date'			=> $end_date,
						'ecodimb'			=> $ecodimb
					)
				);
				$entry['consume'] 	= number_format($entry['consume'], 0, ',', ' ');
			}


			$uicols = array
				(
					'input_type'	=>	array('varchar','varchar','varchar','link', 'varchar'),
					'type'			=>	array('text'	 ,'text'	 ,'text'	 ,'url', 'text' ),
					'col_name'		=>	array('district_id','period','account_class','consume', 'paid'),
					'name'			=>	array('district_id','period','account_class','consume', 'paid'),
					'formatter'		=>	array('','','','',''),
					'descr'			=>	array(lang('District'),lang('Period'),lang('Budget account'),lang('Consume'),lang('paid')),
					'className'		=> 	array('centerClasss','centerClasss','centerClasss','rightClasss','centerClasss')
				);

			$j=0;

			if (isset($content) && is_array($content))
			{
				foreach($content as $invoices)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['value']	= $invoices[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['col_name'][$i];
						$datatable['rows']['row'][$j]['column'][$i]['type'] 	= $uicols['type'][$i];

						if($uicols['input_type'][$i]!='hidden')
						{
							//--- varchar--
							if($uicols['input_type'][$i]=='varchar' && $invoices[$uicols['name'][$i]])
							{
								$datatable['rows']['row'][$j]['column'][$i]['format']	= 'varchar';
							}
							//--- link--
							else if($uicols['input_type'][$i]=='link' && $invoices[$uicols['name'][$i]])
							{
								$datatable['rows']['row'][$j]['column'][$i]['format']	= 'link';
								if($uicols['type'][$i]=='url')
								{
									$datatable['rows']['row'][$j]['column'][$i]['link']	= $invoices['link_voucher'];
								}
								$datatable['rows']['row'][$j]['column'][$i]['target']	= '';
							}
						}
					}
					$j++;
				}
			}

			//not grants
			$datatable['rowactions']['action'] = array();


			$uicols_count	= count($uicols['descr']);

			for ($i=0;$i<$uicols_count;$i++)
			{
				$datatable['headers']['header'][$i]['name'] 		= $uicols['col_name'][$i];
				$datatable['headers']['header'][$i]['text'] 		= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['formatter'] 	= ($uicols['formatter'][$i]=='' ?  '""' : $uicols['formatter'][$i]);
				$datatable['headers']['header'][$i]['className']	= $uicols['className'][$i];

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['visible'] 	= true;
					$datatable['headers']['header'][$i]['format'] 	= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']	= false;
				}
			}


			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($content);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;


			//for maintein page number in datatable
			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order']	= 'district_id'; // name key Column in myColumnDef
				$datatable['sorting']['sort']	= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']	= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort']	= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			//// cramirez: necesary for include a partucular js
			phpgwapi_yui::load_widget('loader');
			//cramirez: necesary for use opener . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			//FIXME this one is only needed when $lookup==true - so there is probably an error
			phpgwapi_yui::load_widget('animation');

			//-- BEGIN----------------------------- JSON CODE ------------------------------
			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array(),
					'sum'				=> number_format($sum, 0, ',', ' ')
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."' target=''>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}
			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json['rights'] = $datatable['rowactions']['action'];
			}
			// query parameters
			if(isset($current_Consult) && is_array($current_Consult))
			{
				$json['current_consult'] = $current_Consult;
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------
			$GLOBALS['phpgw']->jqcal->add_listener('start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('end_date');

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			$appname		= lang('consume');

			$function_msg	= lang('list consume');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'invoice.consume', 'property' );
			//die(_debug_array($datatable));
		}

		function delete()
		{
			$voucher_id = phpgw::get_var('voucher_id', 'int');

			//cramirez add JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($voucher_id);
				return "voucher_id ".$voucher_id." ".lang("has been deleted");
			}


			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}


			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uiinvoice.index'
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($voucher_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.delete', 'voucher_id'=> $voucher_id)),
					'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
					'lang_yes'		=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'		=> lang('no')
				);

			$appname	= lang('invoice');
			$function_msg	= lang('delete voucher');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function add()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::add';

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','add_receipt');
			if(!$receipt)
			{
				$receipt = array();
			}

			if(isset($receipt['voucher_id']) && $receipt['voucher_id'])
			{
				$link_receipt = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.receipt', 'voucher_id'=> $receipt['voucher_id']));
			}

			$GLOBALS['phpgw']->session->appsession('session_data','add_receipt','');

			$bolocation	= CreateObject('property.bolocation');

			$referer = parse_url(phpgw::get_var('HTTP_REFERER', 'string' , 'SERVER'));
			parse_str($referer['query']); // produce $menuaction
			if(phpgw::get_var('cancel', 'bool') || $menuaction != 'property.uiinvoice.add')
			{
				$GLOBALS['phpgw']->session->appsession('session_data','add_values','');
			}

			if(!$GLOBALS['phpgw']->session->appsession('session_data','add_values') && phpgw::get_var('add_invoice', 'bool'))
			{
				$values['art']					= phpgw::get_var('art', 'int');
				$values['type']					= phpgw::get_var('type');
				$values['dim_b']				= phpgw::get_var('dim_b', 'int');
				$values['invoice_num']			= phpgw::get_var('invoice_num');
				$values['kid_nr']				= phpgw::get_var('kid_nr');
				$values['vendor_id']			= phpgw::get_var('vendor_id', 'int');
				$values['vendor_name']			= phpgw::get_var('vendor_name');
				$values['janitor']				= phpgw::get_var('janitor');
				$values['supervisor']			= phpgw::get_var('supervisor');
				$values['budget_responsible']	= phpgw::get_var('budget_responsible');
				$values['invoice_date'] 		= urldecode(phpgw::get_var('invoice_date'));
				$values['num_days']				= phpgw::get_var('num_days', 'int');
				$values['payment_date'] 		= urldecode(phpgw::get_var('payment_date'));
				$values['sday'] 				= phpgw::get_var('sday', 'int');
				$values['smonth'] 				= phpgw::get_var('smonth', 'int');
				$values['syear']				= phpgw::get_var('syear', 'int');
				$values['eday'] 				= phpgw::get_var('eday', 'int');
				$values['emonth'] 				= phpgw::get_var('emonth', 'int');
				$values['eyear']				= phpgw::get_var('eyear', 'int');
				$values['auto_tax'] 			= phpgw::get_var('auto_tax', 'bool');
				$values['merknad']				= phpgw::get_var('merknad');
				$values['b_account_id']			= phpgw::get_var('b_account_id', 'int');
				$values['b_account_name']		= phpgw::get_var('b_account_name');
				$values['amount']				= phpgw::get_var('amount'); // float - has to accept string until client side validation is in place.

				if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['currency']))
				{
					$values['amount'] 		= str_ireplace($GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],'',$values['amount']);
				}

				$values['amount'] 		= str_replace(' ','',$values['amount']);
				$values['amount'] 		= str_replace(',','.',$values['amount']);

				$values['order_id']		= phpgw::get_var('order_id');

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$values = $this->bocommon->collect_locationdata($values,$insert_record);

				$GLOBALS['phpgw']->session->appsession('session_data','add_values',$values);
			}
			else
			{
				$values	= $GLOBALS['phpgw']->session->appsession('session_data','add_values');
				$GLOBALS['phpgw']->session->appsession('session_data','add_values','');
			}

			$location_code 		= phpgw::get_var('location_code');
			$debug 				= phpgw::get_var('debug', 'bool');
			$add_invoice 		= phpgw::get_var('add_invoice', 'bool');


			if($location_code)
			{
				$values['location_data'] = $bolocation->read_single($location_code,array('tenant_id'=>$tenant_id,'p_num'=>$p_num));
			}

			if($add_invoice && is_array($values))
			{
				$order = false;
				if($values['order_id'] && !ctype_digit($values['order_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please enter an integer for order!'));
					unset($values['order_id']);
				}
				else if($values['order_id'])
				{
					$order=true;
				}

				if (!$values['amount'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - enter an amount!'));
				}
				if (!$values['art'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - select type invoice!'));
				}
				if (!$values['vendor_id'] && !$order)
				{
					$receipt['error'][] = array('msg'=>lang('Please - select Vendor!'));
				}

				if (!$values['type'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - select type order!'));
				}

				if (!$values['budget_responsible'] && (!isset($order) || !$order))
				{
					$receipt['error'][] = array('msg'=>lang('Please - select budget responsible!'));
				}

				if (!$values['invoice_num'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - enter a invoice num!'));
				}

				if(!$order && $values['vendor_id'])
				{
					if (!$this->bo->check_vendor($values['vendor_id']))
					{
						$receipt['error'][] = array('msg'=>lang('That Vendor ID is not valid !'). ' : ' . $values['vendor_id']);
					}
				}

				if (!$values['payment_date'] && !$values['num_days'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - select either payment date or number of days from invoice date !'));
				}

				//_debug_array($values);
				if (!is_array($receipt['error']))
				{
					if($values['invoice_date'])
					{
						$sdateparts = phpgwapi_datetime::date_array($values['invoice_date']);
						$values['sday'] = $sdateparts['day'];
						$values['smonth'] = $sdateparts['month'];
						$values['syear'] = $sdateparts['year'];
						unset($sdateparts);

						$edateparts = phpgwapi_datetime::date_array($values['payment_date']);
						$values['eday'] = $edateparts['day'];
						$values['emonth'] = $edateparts['month'];
						$values['eyear'] = $edateparts['year'];
						unset($edateparts);
					}

					$values['regtid'] 		= date($GLOBALS['phpgw']->db->datetime_format());


					$_receipt = array();//local errors
					$receipt = $this->bo->add($values,$debug);

					if(!$receipt['message'] && $values['order_id'] && !$receipt[0]['spvend_code'])
					{
						$_receipt['error'][] = array('msg'=>lang('vendor is not defined in order %1', $values['order_id']));

						$debug = false;// try again..
						if($receipt[0]['location_code'])
						{
							//					$values['location_data'] = $bolocation->read_single($receipt['location_code'],array('tenant_id'=>$tenant_id,'p_num'=>$p_num));
						}
					}

					if($debug)
					{
						$this->debug($receipt);
						return;
					}
					if(!$_receipt['error']) // all ok
					{
						unset($values);
						$GLOBALS['phpgw']->session->appsession('session_data','add_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiinvoice.add'));
					}
				}
				else
				{
					if($values['location'])
					{
						$location_code=implode("-", $values['location']);
						$values['location_data'] = $bolocation->read_single($location_code,isset($values['extra'])?$values['extra']:'');
					}
					$GLOBALS['phpgw']->session->appsession('session_data','add_values','');
				}
			}

			if (isset($receipt['voucher_id']) && $receipt['voucher_id'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array
					(
						'menuaction'	=> 'property.uiinvoice.list_sub',
						'user_lid'		=> $GLOBALS['phpgw_info']['user']['account_lid'],
						'voucher_id'	=> $receipt['voucher_id']
					)
				);
			}

			$location_data=$bolocation->initiate_ui_location(array
				(
					'values'	=> isset($values['location_data'])?$values['location_data']:'',
					'type_id'	=> -1, // calculated from location_types
					'no_link'	=> false, // disable lookup links for location type less than type_id
					'tenant'	=> false,
					'lookup_type'	=> 'form',
					'lookup_entity'	=> false, //$this->bocommon->get_lookup_entity('project'),
					'entity_data'	=> false //$values['p']
				)
			);
			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array
				(
					'b_account_id'		=> isset($values['b_account_id'])?$values['b_account_id']:'',
					'b_account_name'	=> isset($values['b_account_name'])?$values['b_account_name']:'')
				);

			$link_data = array
				(
					'menuaction'	=> 'property.uiinvoice.add',
					'debug'		=> true
				);

			if($_receipt)
			{
				$receipt = array_merge($receipt, $_receipt);
			}
			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			$GLOBALS['phpgw']->jqcal->add_listener('invoice_date');
			$GLOBALS['phpgw']->jqcal->add_listener('payment_date');

			$data = array
				(
					'menu'								=> $this->bocommon->get_menu(),
					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'cancel_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.index')),
					'lang_cancel'						=> lang('Cancel'),
					'lang_cancel_statustext'			=> lang('cancel'),
					'action_url'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>  'property' .'.uiinvoice.add')),
					'tsvfilename'						=> '',

					'lang_add'							=> lang('add'),
					'lang_add_statustext'				=> lang('click this button to add a invoice'),

					'lang_invoice_date'					=> lang('invoice date'),
					'lang_payment_date'					=> lang('Payment date'),
					'lang_no_of_days'					=> lang('Days'),
					'lang_invoice_number'				=> lang('Invoice Number'),
					'lang_invoice_num_statustext'		=> lang('Enter Invoice Number'),

					'lang_select'						=> lang('Select per button !'),
					'lang_kidnr'						=> lang('KID nr'),
					'lang_kid_nr_statustext'			=> lang('Enter Kid nr'),

					'lang_vendor'						=> lang('Vendor'),
					'addressbook_link'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.vendor')),

					'lang_invoice_date_statustext'		=> lang('Enter the invoice date'),
					'lang_num_days_statustext'			=> lang('Enter the payment date or the payment delay'),
					'lang_payment_date_statustext'		=> lang('Enter the payment date or the payment delay'),
					'lang_vendor_statustext'			=> lang('Select the vendor by clicking the button'),
					'lang_vendor_name_statustext'		=> lang('Select the vendor by clicking the button'),
					'lang_select_vendor_statustext'		=> lang('Select the vendor by clicking this button'),

					'value_invoice_date'				=> isset($values['invoice_date'])?$values['invoice_date']:'',
					'value_payment_date'				=> isset($values['payment_date'])?$values['payment_date']:'',
					'value_belop'						=> isset($values['belop'])?$values['belop']:'',
					'value_vendor_id'					=> isset($values['vendor_id'])?$values['vendor_id']:'',
					'value_vendor_name'					=> isset($values['vendor_name'])?$values['vendor_name']:'',
					'value_kid_nr'						=> isset($values['kid_nr'])?$values['kid_nr']:'',
					'value_dim_b'						=> isset($values['dim_b'])?$values['dim_b']:'',
					'value_invoice_num'					=> isset($values['invoice_num'])?$values['invoice_num']:'',
					'value_merknad'						=> isset($values['merknad'])?$values['merknad']:'',
					'value_num_days'					=> isset($values['num_days'])?$values['num_days']:'',
					'value_amount'						=> isset($values['amount'])?$values['amount']:'',
					'value_order_id'						=> isset($values['order_id'])?$values['order_id']:'',

					'lang_auto_tax'						=> lang('Auto TAX'),
					'lang_auto_tax_statustext'			=> lang('Set tax'),

					'lang_amount'						=> lang('Amount'),
					'lang_amount_statustext'			=> lang('Amount of the invoice'),

					'lang_order'						=> lang('Order ID'),
					'lang_order_statustext'				=> lang('Order # that initiated the invoice'),

					'lang_art'							=> lang('Art'),
					'art_list'							=> $this->bo->get_lisfm_ecoart(isset($values['art'])?$values['art']:''),
					'select_art'						=> 'art',
					'lang_select_art' 					=> lang('Select Invoice Type'),
					'lang_art_statustext'				=> lang('You have to select type of invoice'),

					'lang_type'							=> lang('Type invoice II'),
					'type_list'							=> $this->bo->get_type_list(isset($values['type'])?$values['type']:''),
					'select_type'						=> 'type',
					'lang_no_type'						=> lang('No type'),
					'lang_type_statustext'				=> lang('Select the type  invoice. To do not use type -  select NO TYPE'),

					'lang_dimb'							=> lang('Dim B'),
					'dimb_list'							=> $this->bo->select_dimb_list(isset($values['dim_b'])?$values['dim_b']:''),
					'select_dimb'						=> 'dim_b',
					'lang_no_dimb'						=> lang('No Dim B'),
					'lang_dimb_statustext'				=> lang('Select the Dim B for this invoice. To do not use Dim B -  select NO DIM B'),

					'lang_janitor'						=> lang('Janitor'),
					'janitor_list'						=> $this->bocommon->get_user_list_right(32,isset($values['janitor'])?$values['janitor']:'','.invoice'),
					'select_janitor'					=> 'janitor',
					'lang_no_janitor'					=> lang('No janitor'),
					'lang_janitor_statustext'			=> lang('Select the janitor responsible for this invoice. To do not use janitor -  select NO JANITOR'),

					'lang_supervisor'					=> lang('Supervisor'),
					'supervisor_list'					=> $this->bocommon->get_user_list_right(64,isset($values['supervisor'])?$values['supervisor']:'','.invoice'),
					'select_supervisor'					=> 'supervisor',
					'lang_no_supervisor'				=> lang('No supervisor'),
					'lang_supervisor_statustext'		=> lang('Select the supervisor responsible for this invoice. To do not use supervisor -  select NO SUPERVISOR'),

					'lang_budget_responsible'			=> lang('B - responsible'),
					'budget_responsible_list'			=> $this->bocommon->get_user_list_right(128,isset($values['budget_responsible'])?$values['budget_responsible']:'','.invoice'),
					'select_budget_responsible'			=> 'budget_responsible',
					'lang_select_budget_responsible'	=> lang('Select B-Responsible'),
					'lang_budget_responsible_statustext'=> lang('You have to select a budget responsible for this invoice in order to add the invoice'),
					'lang_merknad'						=> lang('Descr'),
					'lang_merknad_statustext'			=> lang('Descr'),
					'location_data'						=> $location_data,
					'b_account_data'					=> $b_account_data,
					'link_receipt'						=> isset($link_receipt)?$link_receipt:'',
					'lang_receipt'						=> lang('receipt')
				);

			//_debug_array($data);

			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice'));

			$appname						= lang('Invoice');
			$function_msg					= lang('Add invoice');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function receipt()
		{

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$voucher_id = phpgw::get_var('voucher_id', 'int');

			if($voucher_id)
			{
				$values = $this->bo->read_single_voucher($voucher_id);
			}
			//	_debug_array($values);
			$pdf	= CreateObject('phpgwapi.pdf');

			if (isSet($values) AND is_array($values))
			{

				$contacts	= CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor',false);

				if($values[0]['vendor_id'])
				{
					$custom 					= createObject('property.custom_fields');
					$vendor_data['attributes']	= $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);
					$vendor_data				= $contacts->read_single(array('id' => $values[0]['vendor_id']),$vendor_data);
					if(is_array($vendor_data))
					{
						foreach($vendor_data['attributes'] as $attribute)
						{
							if($attribute['name']=='org_name')
							{
								$value_vendor_name = $attribute['value'];
								break;
							}
						}
					}
				}

				$sum = 0;
				foreach($values as $entry)
				{
					$content[] = array
						(
							lang('order')		=> $entry['order'],
							lang('invoice id')	=> $entry['invoice_id'],
							lang('budget account')	=> $entry['b_account_id'],
							lang('object')		=> $entry['dim_a'],
							lang('dim_d')		=> $entry['dim_d'],
							lang('Tax code')	=> $entry['tax'],
							'Tjeneste'		=> $entry['kostra_id'],
							lang('amount')		=> number_format($entry['amount'], 2, ',', ' ')

						);
					$sum = $sum + $entry['amount'];
				}
			}

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date('',$dateformat);

			// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
			//error_reporting(7);
			//error_reporting(E_ALL);
			set_time_limit(1800);
			$pdf -> ezSetMargins(50,70,50,50);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();
			$pdf->setStrokeColor(0,0,0,1);
			$pdf->line(20,40,578,40);
			$pdf->line(20,822,578,822);
			$pdf->addText(50,823,6,lang('voucher'));
			$pdf->addText(50,34,6,'BBB');
			$pdf->addText(300,34,6,$date);

			$pdf->setColor(1,0,0);
			$pdf->addText(500,750,40,'E',-10);
			$pdf->ellipse(512,768,30);
			$pdf->setColor(1,0,0);


			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all,'all');
			$pdf->ezStartPageNumbers(500,28,10,'right','{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}',1);
	/*
			$pdf->ezText(lang('voucher id') . ': ' . $voucher_id,14);
			$pdf->ezText(lang('Type') . ' ' .$values[0]['art'] ,14);
			$pdf->ezText(lang('vendor') . ' ' .$values[0]['vendor_id'] . ' ' . $value_vendor_name ,14);
			$pdf->ezText(lang('invoice date') . ' ' . $GLOBALS['phpgw']->common->show_date(strtotime($values[0]['invoice_date']),$dateformat) ,14);
			$pdf->ezText(lang('Payment date') . ' ' . $GLOBALS['phpgw']->common->show_date(strtotime($values[0]['payment_date']),$dateformat) ,14);
			$pdf->ezText(lang('Janitor') . ' ' .$values[0]['janitor'] ,14);
			$pdf->ezText(lang('Supervisor') . ' ' .$values[0]['supervisor'] ,14);
			$pdf->ezText(lang('Budget Responsible') . ' ' .$values[0]['budget_responsible'] ,14);
			$pdf->ezText(lang('Project id') . ' ' .$values[0]['project_id'] ,14);
			$pdf->ezText(lang('Sum') . ' ' .number_format($sum, 2, ',', ' ') ,14);
	 */

			$content_heading[] = array
				(
					'text'		=> lang('voucher id'),
					'value'		=> $voucher_id
				);
			$content_heading[] = array
				(
					'text'		=> lang('Type'),
					'value'		=> $values[0]['art']
				);
			$content_heading[] = array
				(
					'text'		=> lang('vendor'),
					'value'		=> $values[0]['vendor_id'] . ' ' . $value_vendor_name
				);
			$content_heading[] = array
				(
					'text'		=> lang('invoice date'),
					'value'		=> $GLOBALS['phpgw']->common->show_date(strtotime($values[0]['invoice_date']),$dateformat)
				);
			$content_heading[] = array
				(
					'text'		=> lang('Payment date'),
					'value'		=> $GLOBALS['phpgw']->common->show_date(strtotime($values[0]['payment_date']),$dateformat)
				);
			$content_heading[] = array
				(
					'text'		=> lang('Janitor'),
					'value'		=> $values[0]['janitor']
				);
			$content_heading[] = array
				(
					'text'		=> lang('Supervisor'),
					'value'		=> $values[0]['supervisor']
				);
			$content_heading[] = array
				(
					'text'		=> lang('Budget Responsible'),
					'value'		=> $values[0]['budget_responsible']
				);

			if($values[0]['project_id'])
			{
				$content_heading[] = array
					(
						'text'		=> lang('Project id'),
						'value'		=> $values[0]['project_id']
					);
			}

			$content_heading[] = array
				(
					'text'		=> lang('Sum'),
					'value'		=> number_format($sum, 2, ',', ' ')
				);

			$pdf->ezTable($content_heading,'','',
				array('xPos'=>70,'xOrientation'=>'right','width'=>400,0,'shaded'=>0,'fontSize' => 8,'showLines'=> 0,'titleFontSize' => 12,'outerLineThickness'=>0,'showHeadings'=>0
				,'cols'=>array('text'=>array('justification'=>'left','width'=>100),
					'value'=>array('justification'=>'left','width'=>200))
				)
			);

			$pdf->ezSetDy(-20);

			$table_header = array(
				lang('order')=>array('justification'=>'right','width'=>60),
				lang('invoice id')=>array('justification'=>'right','width'=>60),
				lang('budget account')=>array('justification'=>'right','width'=>80),
				lang('object')=>array('justification'=>'right','width'=>70),
				lang('dim_d')=>array('justification'=>'right','width'=>50),
				lang('Tax code')=>array('justification'=>'right','width'=>50),
				'Tjeneste'=>array('justification'=>'right','width'=>50),
				lang('amount')=>array('justification'=>'right','width'=>80),
			);


			if(is_array($values))
			{
				$pdf->ezTable($content,'','',
					array('xPos'=>70,'xOrientation'=>'right','width'=>500,0,'shaded'=>0,'fontSize' => 8,'showLines'=> 2,'titleFontSize' => 12,'outerLineThickness'=>2
					,'cols'=>$table_header
				)
			);
			}

			$document= $pdf->ezOutput();
			$pdf->print_pdf($document,'receipt_'.$voucher_id);
		}


		function debug($values)
		{
			//			_debug_array($values);
			$GLOBALS['phpgw_info']['flags'][noheader] = true;
			$GLOBALS['phpgw_info']['flags'][nofooter] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice','table_header'));

			$link_data_add = array
				(
					'menuaction'	=> 'property.uiinvoice.add',
					'add_invoice'	=> true
				);

			$link_data_cancel = array
				(
					'menuaction'	=> 'property.uiinvoice.add'
				);

			$post_data = array
				(
					'location_code'			=> $values[0]['location_code'],
					'art'					=> $values[0]['art'],
					'type'					=> $values[0]['type'],
					'dim_b'					=> $values[0]['dim_b'],
					'invoice_num'			=> $values[0]['invoice_num'],
					'kid_nr'				=> $values[0]['kid_nr'],
					'vendor_id'				=> $values[0]['spvend_code'],
					'vendor_name'			=> $values[0]['vendor_name'],
					'janitor'				=> $values[0]['janitor'],
					'supervisor'			=> $values[0]['supervisor'],
					'budget_responsible'	=> $values[0]['budget_responsible'],
					'invoice_date' 			=> urlencode($values[0]['invoice_date']),
					'num_days'				=> $values[0]['num_days'],
					'payment_date' 			=> urlencode($values[0]['payment_date']),
					'sday' 					=> $values[0]['sday'],
					'smonth' 				=> $values[0]['smonth'],
					'syear'					=> $values[0]['syear'],
					'eday' 					=> $values[0]['eday'],
					'emonth' 				=> $values[0]['emonth'],
					'eyear'					=> $values[0]['eyear'],
					'auto_tax' 				=> $values[0]['auto_tax'],
					'merknad'				=> $values[0]['merknad'],
					'b_account_id'			=> $values[0]['spbudact_code'],
					'b_account_name'		=> $values[0]['b_account_name'],
					'amount'				=> $values[0]['amount'],
					'order_id'				=> $values[0]['order_id'],
				);

			$link_data_add		= $link_data_add + $post_data;
			$link_data_cancel	= $link_data_cancel + $post_data;

			$table_add[] = array
				(
					'lang_add'		=> lang('Add'),
					'lang_add_statustext'	=> lang('Add this invoice'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data_add),
					'lang_cancel'		=> lang('cancel'),
					'lang_cancel_statustext'=> lang('Do not add this invoice'),
					'cancel_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data_cancel)
				);


			$import = array
				(
					'Bestilling'		=> 'pmwrkord_code',
					'Fakt. Nr' 		=> 'fakturanr',
					'Konto'			=> 'spbudact_code',
					'Objekt'		=> 'dima',
					'Fag/Timer/Matr' 	=> 'dimd',
					'MVA'			=> 'mvakode',
					'Tjeneste'		=> 'kostra_id',
					'Beløp [kr]'		=> 'belop'
				);

			$header = array('Bestilling','Fakt. Nr','Konto','Objekt','Fag/Timer/Matr','MVA','Tjeneste','Beløp [kr]');

			for ($i=0;$i<count($header);$i++)
			{
				$table_header[$i]['header'] 	= $header[$i];
				$table_header[$i]['width'] 		= '5%';
				$table_header[$i]['align'] 		= 'center';
			}
			//	$sum=0;

			$import_count = count($import);
			$values_count = count($values);
			for ($i=0; $i<$values_count; $i++)
			{
				for ($k=0; $k<$import_count; $k++)
				{
					$content[$i]['row'][$k]['value'] 	= $values[$i][$import[$header[$k]]];
					if ($import[$header[$k]]=='belop')
					{
						$content[$i]['row'][$k]['align'] 	= 'right';
						//		$sum=$sum+$values[$i][$import[$header[$k]]];
						$content[$i]['row'][$k]['value'] 	= number_format($values[$i][$import[$header[$k]]], 2, ',', '');
					}
				}
			}



			$data = array
				(
					'artid'						=> $values[0]['artid'],
					'lang_type'					=> lang('Type'),
					'project_id'				=> $values[0]['project_id'],
					'lang_project_id'			=> lang('Project id'),
					'lang_vendor'				=> lang('Vendor'),
					'vendor_name'				=> $values[0]['vendor_name'],
					'spvend_code'				=> $values[0]['spvend_code'],
					'lang_fakturadato'			=> lang('invoice date'),
					'fakturadato'				=> $values[0]['fakturadato'],
					'lang_forfallsdato'			=> lang('Payment date'),
					'forfallsdato'				=> $values[0]['forfallsdato'],
					'lang_janitor'				=> lang('Janitor'),
					'oppsynsmannid'				=> $values[0]['oppsynsmannid'],
					'lang_supervisor'			=> lang('Supervisor'),
					'saksbehandlerid'			=> $values[0]['saksbehandlerid'],
					'lang_budget_responsible'	=> lang('Budget Responsible'),
					'budsjettansvarligid'		=> $values[0]['budsjettansvarligid'],
					'lang_sum'					=> lang('Sum'),
					'sum'						=> number_format($values[0]['amount'], 2, ',', ''),
					'table_header'				=> $table_header,
					'values'					=> $content,
					'table_add'					=> $table_add
				);

			//_debug_array($data);
			$appname						= lang('Invoice');
			$function_msg					= lang('Add invoice: Debug');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('debug' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view_order()
		{
			$order_id	= phpgw::get_var('order_id'); // could be bigint
			$soXport    = CreateObject('property.soXport');

			$nonavbar = phpgw::get_var('nonavbar', 'bool');
			$lean = phpgw::get_var('lean', 'bool');

			$order_type = $soXport->check_order($order_id);
			switch($order_type)
			{
				case 'workorder':
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'id'=> $order_id, 'tab' => 'budget', 'nonavbar' => $nonavbar, 'lean' => $lean));
					break;
				case 's_agreement':
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uis_agreement.view', 'id'=> $order_id, 'nonavbar' => $nonavbar, 'lean' => $lean));
					break;
				default:
					$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
					$GLOBALS['phpgw']->common->phpgw_header(true);
					echo 'No such order';
					$GLOBALS['phpgw']->common->phpgw_exit();
			}
		}

		public function reporting()
		{
			$acl_location = '.demo_location';
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$type	= phpgw::get_var('type', 'string', 'GET', 'deposition');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::{$type}";

			$values	= phpgw::get_var('values');

			$receipt = array();
			if($values)
			{
	//		_debug_array($values);die();

				if(isset($values['export_reconciliation']) && $values['export_reconciliation'])
				{
					if(!isset($values['periods']))
					{
						$type	= 'reconciliation';
						$receipt['error'][]=array('msg'=>lang('missing values'));
					}
					else
					{
						$this->bo->export_historical_transactions_at_periods($values['periods']);

					}
				}
				else if(isset($values['export_deposition']) && $values['export_deposition'])
				{
					if(!isset($values['deposition']))
					{
						$type	= 'deposition';
						$receipt['error'][]=array('msg'=>lang('nothing to do'));
					}
					else
					{
						$this->bo->export_deposition();
					}
				}
			}


			$tab_info = array
			(
				'deposition'		=> array('label' => lang('deposition'), 'link' => '#deposition'),
				'reconciliation'	=> array('label' => lang('reconciliation'), 'link' => '#reconciliation')
			);

			phpgwapi_yui::tabview_setup('reporting_tabview');

			$msgbox_data = isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'';

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.reporting')),
				'accounting_periods'		=> array('options' => $this->bo->get_historical_accounting_periods()),
				'tabs'						=> phpgwapi_yui::tabview_generate($tab_info, $type)
			);

			$function_msg = lang('reporting');
			$appname		= lang('invoice');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice_reporting','attributes_form'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('reporting' => $data));

		}
		/**
		 * forward voucher to other persons
		 *
		 */

		public function forward()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] =  true;

			$user_lid	= phpgw::get_var('user_lid', 'string', 'GET', 'all');
			$voucher_id	= phpgw::get_var('voucher_id', 'int', 'GET');
			$redirect	= false;

			$role_check = array
			(
				'is_janitor' 				=> lang('janitor'),
				'is_supervisor' 			=> lang('supervisor'),
				'is_budget_responsible' 	=> lang('b - responsible')
			);

			$approve = $this->bo->get_approve_role();

			$values	= phpgw::get_var('values');

			$receipt = array();
			if (isset($values['save']))
			{
				if($GLOBALS['phpgw']->session->is_repost())
				{
					$receipt['error'][]=array('msg'=>lang('repost'));
				}

				if(!$approve)
				{
					$receipt['error'][]=array('msg'=>lang('you are not approved for this task'));
				}

				if (!$receipt['error'])
				{
					$values['voucher_id'] = $voucher_id;
					$receipt = $this->bo->forward($values);
					if(!$receipt['error'])
					{
						execMethod('property.soworkorder.close_orders',phpgw::get_var('orders'));
						$redirect = true;
					}
				}
			}

			$voucher = $this->bo->read_single_voucher($voucher_id);
			$orders = array();
			$_orders = array();
			foreach ($voucher as $line)
			{
				if($line['order_id'])
				{
					$_orders[] = $line['order_id'];
				}
			}

			$_orders = array_unique($_orders);

			foreach ($_orders as $_order)
			{
					$orders[] = array('id' => $_order);
			}

			$approved_list = array();
 
			$approved_list[] = array
			(
				'role'		=> $role_check['is_janitor'],
				'role_sign'	=> 'oppsynsmannid',
				'initials'	=> $line['janitor'] ? $line['janitor'] : '',
				'date'		=> $line['oppsynsigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $line['oppsynsigndato'] ) ) :'',
				'user_list'	=> !$line['oppsynsigndato'] ? array('options_user' => $this->bocommon->get_user_list_right(32,isset($line['janitor'])?$line['janitor']:'','.invoice')) : ''
			);
			$approved_list[] = array
			(
				'role'		=> $role_check['is_supervisor'],
				'role_sign'	=> 'saksbehandlerid',
				'initials'	=> $line['supervisor'] ? $line['supervisor'] : '',
				'date'		=> $line['saksigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $line['saksigndato'] ) ) :'',
				'user_list'	=> !$line['saksigndato'] ? array('options_user' => $this->bocommon->get_user_list_right(64,isset($line['supervisor'])?$line['supervisor']:'','.invoice')) : ''
			);
			$approved_list[] = array
			(
				'role'		=> $role_check['is_budget_responsible'],
				'role_sign'	=> 'budsjettansvarligid',
				'initials'	=> $line['budget_responsible'] ? $line['budget_responsible'] : '',
				'date'		=> $line['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date( strtotime( $line['budsjettsigndato'] ) ) :'',
				'user_list'	=> !$line['budsjettsigndato'] ? array('options_user' => $this->bocommon->get_user_list_right(128,isset($line['budget_responsible'])?$line['budget_responsible']:'','.invoice')) : ''
			);

			$my_initials = $GLOBALS['phpgw_info']['user']['account_lid'];

			foreach($approve as &$_approve)
			{
				if($_approve['id'] == 'is_janitor' && $my_initials == $line['janitor'] && $line['oppsynsigndato'])
				{
					$_approve['selected'] = 1;
					$sign_orig = 'is_janitor';
				}
				else if($_approve['id'] == 'is_supervisor' && $my_initials == $line['supervisor'] && $line['saksigndato'])
				{
					$_approve['selected'] = 1;
					$sign_orig = 'is_supervisor';
				}
				else if($_approve['id'] == 'is_budget_responsible' && $my_initials == $line['budget_responsible'] && $line['budsjettsigndato'])
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
					if(($my_initials == $line['janitor'] && $line['oppsynsigndato']) || !$line['oppsynsigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
				if($_approve['id'] == 'is_supervisor')
				{
					if(($my_initials == $line['supervisor'] && $line['saksigndato']) || !$line['saksigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
				if($_approve['id'] == 'is_budget_responsible')
				{
					if(($my_initials == $line['budget_responsible'] && $line['budsjettsigndato']) || !$line['budsjettsigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
			}

			$data = array
			(
					'redirect'				=> $redirect ? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.index', 'user_lid' => $user_lid)) : null,
					'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($receipt)),
					'from_name'				=> $GLOBALS['phpgw_info']['user']['fullname'],
					'form_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiinvoice.forward', 'user_lid' => $user_lid, 'voucher_id' => $voucher_id)),
					'approve_list'			=> $approve_list,
					'approved_list'			=> $approved_list,
					'sign_orig'				=> $sign_orig,
					'my_initials'			=> $my_initials,
					'project_group_data'	=> $project_group_data,
					'orders'				=> $orders,
					'value_amount'			=> $line['amount'],
					'value_currency'		=> $line['currency'],
					'value_process_log'		=>  isset($values['process_log']) && $values['process_log'] ? $values['process_log'] : $line['process_log']
			);

			$GLOBALS['phpgw']->xslttpl->add_file('invoice');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('forward' => $data));
		}

	}
