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
			'edit_period'	=> true,
			'list_sub'		=> true,
			'consume'		=> true,
			'remark'		=> true,
			'delete'		=> true,
			'add'			=> true,
			'debug'			=> true,
			'view_order'	=> true,
			'download'		=> true,
			'download_sub'	=> true,
			'receipt'		=> true
		);

		function property_uiinvoice()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::invoice';
		//	$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.boinvoice',true);
			$this->bocommon			= CreateObject('property.bocommon');

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
				'district_id'		=> $this->district_id
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

			$start_date=urldecode($start_date);
			$end_date=urldecode($end_date);

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

				$name = array(
					'workorder_id',
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

				$descr = array(
					lang('Workorder'),
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


//------------------------------------------------------------------------------------------
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
			$loc1 			= phpgw::get_var('loc1');
			$voucher_id 	= phpgw::get_var('voucher_id', 'int');
			$b_account_class= phpgw::get_var('b_account_class', 'int');

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
				$end_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,date("m"),date("d"),date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$start_date = $end_date;
			}
			//-- edicion de registro
			$values  = phpgw::get_var('values');
			$receipt = array();

			if( phpgw::get_var('phpgw_return_as') == 'json' && is_array($values) && isset($values))
			 {
			 	$receipt = $this->bo->update_invoice($values);

			 }
			 // Edit Period
			$period  = phpgw::get_var('period');
			$voucher_id_for_period  = phpgw::get_var('voucher_id_for_period');
			if( phpgw::get_var('phpgw_return_as') == 'json' &&  isset($period) &&  $period != '')
			{
				$receipt	= $this->bo->update_period($voucher_id_for_period,$period);
			}

			$datatable = array();
			$values_combo_box = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
							    				(
													'menuaction'		=> 'property.uiinvoice.index',
													'order'				=> $this->order,
													'sort'				=> $this->sort,
													'cat_id'			=> $this->cat_id,
													//'user_lid'			=> 'all',
													'user_lid'			=> $this->user_lid,
													'sub'				=> $this->sub,
													'query'				=> $this->query,
													'start'				=> $this->start,
													'paid'				=> $paid,
													'vendor_id'			=> $vendor_id,
													'workorder_id'		=> $workorder_id,
													'start_date'		=> $start_date,
													'end_date'			=> $end_date,
													'filter'			=> $this->filter,
													'b_account_class'	=> $b_account_class,
													'district_id'		=> $this->district_id
												));


				$datatable['config']['base_java_url'] = "menuaction:'property.uiinvoice.index',"
	    											."order:'{$this->order}',"
	    											."sort:'{$this->sort}',"
 	                        						."cat_id: '{$this->cat_id}',"
 	                        						//."user_lid:'all',"
 	                        						."user_lid:'{$this->user_lid}',"
						 	                        ."sub:'{$this->sub}',"
 	                        						."query:'{$this->query}',"
						 	                        ."start:'{$this->start}',"
						 	                        ."paid:'{$paid}',"
						 	                        ."vendor_id:'{$vendor_id}',"
						 	                        ."workorder_id:'{$workorder_id}',"
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
				$default_value = array ('lid'=>'','firstname'=>lang('no user'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $this->bo->select_account_class($b_account_class);
				$default_value = array ('id'=>'','name'=>lang('No account'));
				array_unshift ($values_combo_box[2],$default_value);

				if($paid)
				{
					$jscal = CreateObject('phpgwapi.jscalendar');
					$jscal->add_listener('start_date');
					$jscal->add_listener('end_date');
				}


				if (!$paid)
				{
				$field_invoice = array(
	                                array( //boton 	CATEGORY
	                                    'id' => 'btn_cat_id',
	                                    'name' => 'cat_id',
	                                    'value'	=> lang('Category'),
	                                    'type' => 'button',
	                                    'style' => 'filter'
	                                ),
	                                array( //boton 	OWNER
	                                    'id' => 'btn_user_lid',
	                                    'name' => 'user_lid',
	                                    'value'	=> user_lid,
	                                    'type' => 'button',
	                                    'style' => 'filter'
	                                ),	                               	                       
	                                array( // boton exportar
		                                'type'	=> 'button',
		                            	'id'	=> 'btn_export',
		                                'value'	=> lang('download')
		                            ),
		                            array( //hidden paid
		                                'type'	=> 'hidden',
		                            	'id'	=> 'paid',
		                                'value'	=> $paid
		                            ),
		                            array( //hidden paid
		                                'type'	=> 'hidden',
		                            	'id'	=> 'valuesForUpdatePHP',
		                                'value'	=> ''
		                            ),
									array( // boton SAVE
		                                'id'	=> 'btn_save',
		                            	//'name' => 'save',
		                                'value'	=> lang('save'),
										'type'	=> 'button'
		                            ),		                            
									array( // boton ADD
		                                'type'	=> 'submit',
		                            	'id'	=> 'btn_new',
		                                'value'	=> lang('add')
		                            ),		                            
	                                array( //boton   SEARCH
	                                    'id' => 'btn_search',
	                                    'name' => 'search',
	                                    'value'    => lang('search'),
	                                    'type' => 'button',
	                                ),
	                                array( // TEXT IMPUT
	                                    'name'     => 'query',
	                                    'id'     => 'txt_query',
	                                    'value'    => $this->query,//$query,
	                                    'type' => 'text',
	                                    'size'    => 28
	                                ),	
	                                array( // txtbox end_data hidden
	                                    'name'     => 'end_date',
	                                    'id'     => 'txt_end_date',
	                                    'value'    => "",
	                                    'type' => 'hidden',
	                                    'size'    => 8
	                                ),	                                
	                                array( // txtbox start_data hidden
	                                    'name'     => 'start_date',
	                                    'id'     => 'txt_start_date',
	                                    'value'    => "",
	                                    'type' => 'hidden',
	                                    'size'    => 8
		                            ));
				}
				else
				{
				$field_invoice = array(
	                                array( // imag calendar1
	                                    'type' => 'img',
										'id'     => 'start_date-trigger',
	                                    'src'    => $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
	                                    'alt'	=> lang('Select date'),
	                                    'style' => 'filter'
	                                ),				
									array( // calendar1 start_date
	                                    'type' => 'text',
										'name'     => 'start_date',
	                                    'id'     => 'start_date',
	                                    'value'    => $start_date,
	                                    'size'    => 7,
	                                    'readonly' => 'readonly',
	                                    'style' => 'filter'
	                                ),
	                                array( // imag calendar1
	                                    'type' => 'img',
										'id'     => 'end_date-trigger',
	                                    'src'    => $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
	                                    'alt'	=> lang('Select date'),
	                                    'style' => 'filter'
	                                ),
									array( // calendar1 start_date
	                                    'type' => 'text',
										'name'     => 'end_date',
	                                    'id'     => 'end_date',
	                                    'value'    => $end_date,
	                                    'size'    => 7,
	                                    'readonly' => 'readonly',
	                                    'style' => 'filter'
	                                ),
	                                array( // workorder link
		                                'type' => 'link',
		                                'id' => 'lnk_workorder',
		                                'url' => "",
										'value' => lang('Workorder ID'),
										'style' => 'filter'
									),	                                
	                                array( // workorder box
	                                    'name'     => 'workorder_id',
	                                    'id'     => 'txt_workorder',
	                                    'value'    => $workorder_id,
	                                    'type' => 'text',
	                                    'size'    => 10,
	                                    'style' => 'filter'
	                                ),
	                                array( //vendor link
		                                'type' => 'link',
		                                'id' => 'lnk_vendor',
		                                'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
											           array
											              (
											               'menuaction' => 'property.uilookup.vendor',
											               ))."','Search','width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes')",
										'value' => lang('Vendor'),
										'style' => 'filter'
									),	     
	                                array( // Vendor box HIDDEN
	                                    'name'     => 'vendor_name',
	                                    'id'     => 'txt_vendor_name',
	                                    'value'    => "",
	                                    'type' => 'hidden',
	                                    'size'    => 10,
	                                    'style' => 'filter'
	                                ),									                           
									array( // Vendor box
	                                    'name'     => 'vendor_id',
	                                    'id'     => 'txt_vendor',
	                                    'value'    => $vendor_id,
	                                    'type' => 'text',
	                                    'size'    => 10,
	                                    'style' => 'filter'
	                                ),
	                                array(
		                                'type' => 'link',
		                                'id' => 'lnk_property',
		                                'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
											           array
											              (
											               'menuaction' => 'property.uilocation.index',
											               'lookup'  	=> 1,
											               'type_id'  	=> 1,
															'lookup_name'  	=> 0,
											               ))."','Search','width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes')",
										'value' => lang('property'),
										'style' => 'filter'
									),
	                                array( // txt Facilities Management
	                                    'name'     => 'loc1_name',
	                                    'id'     => 'txt_loc1_name',
	                                    'value'    => "",
	                                    'type' => 'hidden',
	                                    'size'    => 8,
	                                    'style' => 'filter'
	                                ),
	                                array( // txt Facilities Management
	                                    'name'     => 'loc1',
	                                    'id'     => 'txt_loc1',
	                                    'value'    => $loc1,
	                                    'type' => 'text',
	                                    'size'    => 8,
	                                    'style' => 'filter'
	                                ),
	                                array( // Voucher link
		                                'type' => 'link',
		                                'id' => 'lnk_voucher',
		                                'url' => "",
										'value' => lang('Voucher ID'),
										'style' => 'filter'
									),
	                                array( // Voucher box
	                                    'name'     => 'voucher_id',
	                                    'id'     => 'txt_voucher',
	                                    'value'    => $voucher_id,
	                                    'type' => 'text',
	                                    'size'    => 8,
	                                    'style' => 'filter'
	                                ),
									array( //boton   SEARCH
	                                    'id' => 'btn_search',
	                                    'name' => 'search',
	                                    'value'    => lang('search'),
	                                    'type' => 'button',
	                                    'style' => 'filter'
	                                ),
	                                array( //boton 	CATEGORY
	                                    'id' => 'btn_cat_id',
	                                    'name' => 'cat_id',
	                                    'value'	=> lang('Category'),
	                                    'type' => 'button',
	                                    'style' => 'filter'
	                                ),
									array( //boton 	OWNER
	                                    'id' => 'btn_user_lid',
	                                    'name' => 'user_lid',
	                                    'value'	=> user_lid,
	                                    'type' => 'button',
	                                    'style' => 'filter'
	                                ),
	                                array( //boton 	ACCOUNT
	                                    'id' => 'btn_b_account_class',
	                                    'name' => 'b_account_class',
	                                    'value'	=> lang('No account'),
	                                    'type' => 'button',
	                                    'style' => 'filter'
	                                ),
		                            array( //hidden paid
		                                'type'	=> 'hidden',
		                            	'id'	=> 'paid',
		                            	'name'	=> 'paid',
		                                'value'	=> $paid,
		                                'style' => 'filter'
		                            ));
				}

		$datatable['actions']['form'] = array(
			array(
				'action'	=> $GLOBALS['phpgw']->link('/index.php',
						array(
								'menuaction'		=> 'property.uiinvoice.index',
								'order'				=> $this->order,
								'sort'				=> $this->sort,
								'cat_id'			=> $this->cat_id,
								'user_lid'			=> $this->user_lid,
								'sub'				=> $this->sub,
								'query'				=> $this->query,
								'start'				=> $this->start,
								'paid'				=> $paid,
								'vendor_id'			=> $vendor_id,
								'workorder_id'		=> $workorder_id,
								'start_date'		=> $start_date,
								'end_date'			=> $end_date,
								'filter'			=> $this->filter,
								'b_account_class'	=> $b_account_class,
								'district_id'		=> $this->district_id
							)
					),
				'fields'	=> array(
                                    'field' => $field_invoice,
		                       		'hidden_value' => array(
					                                        array( //div values  combo_box_0
							                                            'id' => 'values_combo_box_0',
							                                            'value'	=> $this->bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
							                                      ),
							                                array( //div values  combo_box_1
							                                            'id' => 'values_combo_box_1',
							                                            'value'	=> $this->bocommon->select2String($values_combo_box[1],'lid','firstname','lastname')
							                                      ),
						                                      array( //div values  combo_box_2
							                                            'id' => 'values_combo_box_2',
							                                            'value'	=> $this->bocommon->select2String($values_combo_box[2])
						                                      		)
				                                      		)
									)
				  )
				);

			} //-- of if( phpgw::get_var('phpgw_return_as') != 'json' )

			$content = array();
			//the first time, $content is empty, because $user_lid=''.In the seconfd time, user_lid=all; It is done using  base_java_url.
			$content = $this->bo->read_invoice($paid,$start_date,$end_date,$vendor_id,$loc1,$workorder_id,$voucher_id);
			//die(_debug_array($content));
			$uicols = array (
				'input_type'	=>	array(hidden,hidden,hidden,hidden,link,link   ,hidden,hidden,hidden,$paid?varchar:input,varchar,link   ,hidden,varchar,varchar,varchar,$paid?hidden:input,$paid?hidden:input,special,special,special,special2),
				'type'			=>	array(number,''	   ,number,''	 ,url ,msg_box,''    ,''    ,''    ,$paid?'':text	   ,''     ,msg_box,''    ,''     ,''     ,''     ,$paid?'':checkbox ,$paid?'':radio	 ,''     ,''     ,''     ,''  	 ),

				'col_name'		=>	array(counter_num,counter,voucher_id_num,voucher_id,voucher_id_lnk,voucher_date_lnk,sign_orig ,num_days_orig,timestamp_voucher_date,num_days,amount_lnk,vendor_id_lnk,invoice_count,invoice_count_lnk,type_lnk,period,kreditnota,sign      ,janitor_lnk,supervisor_lnk,budget_responsible_lnk,transfer_lnk),
				'name'			=>	array(counter,counter,voucher_id    ,voucher_id,voucher_id    ,voucher_date    ,empty_fild,num_days     ,timestamp_voucher_date,num_days,amount    ,vendor_id    ,invoice_count,invoice_count    ,type    ,period,kreditnota,empty_fild,janitor    ,supervisor    ,budget_responsible    ,transfer_id),

				'formatter'		=>	array('','','','','','','','','','',myFormatDate,'','','','',$paid?'':myPeriodDropDown,'','','','','',''),
//				'editor'		=>	array('""','""','""','""','""','""','""','""','""','""','""','""','""','""','new YAHOO.widget.DropdownCellEditor({dropdownOptions:[{label:"Alabama", value:"AL"},{label:"Los angeles", value:"LA"}],disableBtns:true})','""','""','""','""','""','""'),

				'descr'			=>	array(dummy,dummy,dummy,dummy,lang('voucher'),lang('Voucher Date'),dummy,dummy,dummy,lang('Days'),lang('Sum'),lang('Vendor'),dummy,lang('Count'),lang('Type'),lang('Period'),lang('KreditNota'),lang('None'),lang('Janitor'),lang('Supervisor'),lang('Budget Responsible'),lang('Transfer')),
				'className'		=> 	array('','','','','','centerClasss','','','','','rightClasss','rightClasss','','rightClasss','','comboClasss','centerClasss','centerClasss','','','','centerClasss')
				);

			//url to detail of voucher
			$link_sub 	= 	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.list_sub','user_lid'=>all));

//			//url to edit period
//			$link_edit_period_pre	="javascript:var%20w=window.open(\"";
//			$link_edit_period		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.edit_period'));
//			$link_edit_period_post	="\",\"\",\"width=150,height=150\")";

			if($paid)
			{
				 $link_sub.="&paid=true";
			}

			$j=0;
			$sum=0;
			//---- llena DATATABLE-ROWS con los valores del READ
			if (isset($content) && is_array($content))
			{
				foreach($content as $invoices)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						//values column kreditnota
						if($uicols['type'][$i]=='checkbox' && $uicols['col_name'][$i]=='kreditnota')
						{
							$datatable['rows']['row'][$j]['column'][$i]['value']	= 'true';
						}
						//values column sign
						elseif($uicols['type'][$i]=='radio' && $uicols['col_name'][$i]=='sign')
						{
							$datatable['rows']['row'][$j]['column'][$i]['value']	= 'sign_none';
						}
						// others columnas
						else
						{
							$datatable['rows']['row'][$j]['column'][$i]['value']	= $invoices[$uicols['name'][$i]];
						}

						$datatable['rows']['row'][$j]['column'][$i]['name'] 		= $uicols['col_name'][$i];
						$datatable['rows']['row'][$j]['column'][$i]['align'] 		= 'center';
						//$datatable['rows']['row'][$j]['column'][$i]['className'] 	= $uicols['class'][$i];


						if($uicols['input_type'][$i]!='hidden')
						{
								//--- varchar--
								if($uicols['input_type'][$i]=='varchar' && $invoices[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'varchar';
								}

								//--- link--
								elseif($uicols['input_type'][$i]=='link' && $invoices[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
									$datatable['rows']['row'][$j]['column'][$i]['link']				= '#';
									if($uicols['type'][$i]=='url')
									{
										$datatable['rows']['row'][$j]['column'][$i]['link']			= $link_sub."&voucher_id=".$invoices[$uicols['name'][$i]];
									}
//									else if($uicols['type'][$i]=='w_open')
//									{
//										$datatable['rows']['row'][$j]['column'][$i]['link']			= $link_edit_period_pre.$link_edit_period."&period=".$invoices[$uicols['name'][$i]]."&voucher_id=".$invoices['voucher_id'].$link_edit_period_post;
//									}
									$datatable['rows']['row'][$j]['column'][$i]['target']			= '';

								}

								//--- special--
								elseif($uicols['input_type'][$i]=='special')
								{

									// the same name of columns
									$type_sign =  $datatable['rows']['row'][$j]['column'][$i]['format'] = $uicols['name'][$i];
									$datatable['rows']['row'][$j]['column'][$i]['for_json'] 			= $uicols['col_name'][$i];


									if(!$paid)
									{
										if( ($invoices['is_janitor']==1 && $type_sign == 'janitor') || ($invoices['is_supervisor']==1 && $type_sign == 'supervisor') || ($invoices['is_budget_responsible']==1 && $type_sign == 'budget_responsible'))
										{
											if( ($invoices['jan_date']=='' && $type_sign == 'janitor') || ($invoices['super_date=']=='' && $type_sign == 'supervisor') || ($invoices['budget_date==']=='' && $type_sign == 'budget_responsible'))
											{
												$datatable['rows']['row'][$j]['column'][$i]['name']			= 'sign';
												$datatable['rows']['row'][$j]['column'][$i]['type']			= 'radio';
												$datatable['rows']['row'][$j]['column'][$i]['value'] 		= ($type_sign == 'janitor'? 'sign_janitor':($type_sign == 'supervisor'? 'sign_supervisor' : 'sign_budget_responsible'));
												$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= "onMouseout=window.status='';return true;";
											}

											elseif( ($invoices['janitor']==$invoices['current_use'] && $type_sign == 'janitor')  || ($invoices['supervisor']==$invoices['current_use'] && $type_sign == 'supervisor') || ($invoices['budget_responsible']==$invoices['current_use'] && $type_sign == 'budget_responsible'))
											{
												$datatable['rows']['row'][$j]['column'][$i]['name'] 		= 'sign';
												$datatable['rows']['row'][$j]['column'][$i]['type'] 		= 'radio';
												$datatable['rows']['row'][$j]['column'][$i]['value']		= ($type_sign == 'janitor'? 'sign_janitor':($type_sign == 'supervisor'? 'sign_supervisor' : 'sign_budget_responsible'));
												$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= "onMouseout=window.status='';return true;   checked='checked'";
											}

											else
											{
												$datatable['rows']['row'][$j]['column'][$i]['name']			= '';
												$datatable['rows']['row'][$j]['column'][$i]['type']			= 'checkbox';
												$datatable['rows']['row'][$j]['column'][$i]['value']		= '';
												$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= " checked='checked' disabled='disabled' ";
											}
										}
										else
										{
											if( ($invoices['jan_date']=='' && $type_sign == 'janitor') || ($invoices['super_date']=='' && $type_sign == 'supervisor') || ($invoices['budget_date']=='' && $type_sign == 'budget_responsible') )
											{
											}
											else
											{
												$datatable['rows']['row'][$j]['column'][$i]['name']			= '';
												$datatable['rows']['row'][$j]['column'][$i]['type']			= 'checkbox';
												$datatable['rows']['row'][$j]['column'][$i]['value']		= '';
												$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= " checked='checked' disabled='disabled' ";
											}
										}
										$datatable['rows']['row'][$j]['column'][$i]['value2'] = ($type_sign == 'janitor'? $invoices['janitor']: ($type_sign == 'supervisor'? $invoices['supervisor'] : $invoices['budget_responsible']));
									}
									else //if($paid)
									{
											$datatable['rows']['row'][$j]['column'][$i]['value2'] = ($type_sign == 'janitor'? ($invoices['jan_date']." - ".$invoices['janitor']): ($type_sign == 'supervisor'? ($invoices['super_date']." - ".$invoices['supervisor']) : ($invoices['budget_date']." - ".$invoices['budget_responsible'])));
									}
								}
								//---- speciual2----
								elseif($uicols['input_type'][$i]=='special2')
									{

										// the same name of columns
										$type_sign =  $datatable['rows']['row'][$j]['column'][$i]['format'] = $uicols['name'][$i];
										$datatable['rows']['row'][$j]['column'][$i]['for_json'] 			= $uicols['col_name'][$i];


										if(!$paid)
										{
											if( ($invoices['is_transfer']==1))
											{
												if( ($invoices['transfer_date']==''))
												{
													$datatable['rows']['row'][$j]['column'][$i]['name']			= 'transfer';
													$datatable['rows']['row'][$j]['column'][$i]['type']			= 'checkbox';
													$datatable['rows']['row'][$j]['column'][$i]['value'] 		= 'true';
													$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= "onMouseout=\"window.status='';return true;\"";
												}

												else
												{
													$datatable['rows']['row'][$j]['column'][$i]['name']			= 'transfer';
													$datatable['rows']['row'][$j]['column'][$i]['type']			= 'checkbox';
													$datatable['rows']['row'][$j]['column'][$i]['value'] 		= 'true';
													$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= " checked=\"checked\" onMouseout=\"window.status='';return true;\"";
												}
											}
											else
											{
												if( ($invoices['transfer_id']!=''))
												{
													$datatable['rows']['row'][$j]['column'][$i]['name']			= '';
													$datatable['rows']['row'][$j]['column'][$i]['type']			= 'checkbox';
													$datatable['rows']['row'][$j]['column'][$i]['value'] 		= '';
													$datatable['rows']['row'][$j]['column'][$i]['extra_param']	= "checked=\"checked\" disabled=\"disabled\"";

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
									elseif($uicols['col_name'][$i]=='kreditnota' && $invoices[$uicols['name'][$i]] == '1')
									{
										$datatable['rows']['row'][$j]['column'][$i]['extra_param'] = "checked='checked' ";
									}
								}
						}
						else
						{
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'hidden';
								$datatable['rows']['row'][$j]['column'][$i]['type'] 			= $uicols['type'][$i];
						}

					}
					$sum += $invoices['amount'];
					$j++;
				}
			}

			//_debug_array($content);die;
			//die(_debug_array($datatable['rows']['row'][7]['column']));
			//die($sum);

			//---- PERMISOS
			$datatable['rowactions']['action'] = array();

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

			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array(
					'text' 			=> lang('delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uiinvoice.delete',
									)),
					'parameters'	=> $parameters
				);
			}

			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array(
					'text' 			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uiinvoice.add'
									))
				);
			}

			$datatable['rowactions']['action'][] = array(
					'text' 			=> lang('F'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uiinvoice.receipt'
									)),
					'parameters'	=> $parameters
				);

			unset($parameters);

			//--- CABECERAS DEL DATATABLE sirve para crear el MycolumnsDef
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
					if($uicols['name'][$i]=='voucher_id'):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'bilagsnr';
					}
					elseif($uicols['name'][$i]=='voucher_date'):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'fakturadato';
					}
					elseif($uicols['name'][$i]=='vendor_id'):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'spvend_code';
					}
					endif;
				}
				else
				{
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}
			//die(_debug_array($datatable['headers']['header']));

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($content);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column
			$datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC

			$appname = lang('location');

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	//// cramirez: necesary for include a partucular js
		  	phpgwapi_yui::load_widget('loader');
		  	//die(_debug_array($datatable['rows']['row'][7]));

//-- BEGIN----------------------------- JSON CODE ------------------------------
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
    		//values for Pagination
	    		$json = array
	    		(
	    			'recordsReturned' 	=> $datatable['pagination']['records_returned'],
    				'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
	    			'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
	    			'dir'				=> $datatable['sorting']['sort'],
	    			'sum_total'			=> $sum,
					'records'			=> array()
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
		    				  	$json_row[$column['name']] = "<a target='".$column['target']."' href='".$column['link']."' >".$column['value']."</a>";
		    				}
		    				elseif($column['format']== "input")
		    				{
								$json_row[$column['name']] = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]' class='myValuesForPHP' type='".$column['type']."' value='".$column['value']."' ".$column['extra_param']."/>";
		    				}
		    				elseif($column['format']== "varchar")
		    				{
								$json_row[$column['name']] = $column['value'];
		    				}
		    				elseif($column['format']== "janitor" || $column['format']== "supervisor" || $column['format']== "budget_responsible" || $column['format']== "transfer_id" )
		    				{
								$tmp_lnk = "";
		    					if($column['type']!='')
		    					{
			    					if($column['name']=='')
			    					{
			    						$tmp_lnk = " <input name='".$column['name']."' type='".$column['type']."' value='".$column['value']."' ".$column['extra_param']."/>";
			    					}
			    					else
			    					{
			    						$tmp_lnk = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]' class='myValuesForPHP' type='".$column['type']."' value='".$column['value']."' ".$column['extra_param']."/>";
			    					}
		    					}

		    					if($column['value2'])
	    						{
	    							$json_row[$column['for_json']] = $tmp_lnk . $column['value2'];
	    						}
		    					else
	    						{
	    							//$json_row[$column['for_json']]="";
	    							$json_row[$column['for_json']]=$tmp_lnk;

	    						}
		    				}

		    				else // for  hidden
		    				{
		    				  if($column['type']== 'number') // for values for delete,edit.
		    				  {
		    				  	$json_row[$column['name']] = $column['value'];
		    				  }
		    				  else // for imput hiddens
		    				  {
		    				  	$json_row[$column['name']] = " <input name='values[".$column['name']."][".$k."]' id='values[".$column['name']."][".$k."]'  class='myValuesForPHP'  type='hidden' value='".$column['value']."'/>";
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
					$json ['rights'] = $datatable['rowactions']['action'];
				}

				// message when editting & deleting records
				if(isset($receipt) && is_array($receipt) && count($receipt))
				{
					$json ['message'][] = $receipt;
				}

	    		return $json;
			}
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
		  	$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			//Title of Page
			$appname	= lang('invoice');
			$function_msg	= lang('list voucher');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			//die(_debug_array($datatable));
	  		// Prepare YUI Library
	  		if ($paid)
	  		{
	  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'invoice.paid.index', 'property' );
	  		}
  			else
  			{
  				$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'invoice.index', 'property' );
  			}


			//$this->save_sessiondata();
	}
//----------------------------------------------------------------------------------------
		function list_sub()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice',
										'nextmatchs'));

			$paid = phpgw::get_var('paid', 'bool');

			$values  = phpgw::get_var('values');
			$voucher_id = phpgw::get_var('voucher_id', 'int');

			$receipt = array();
			if(isset($values['save']) && $values['save'] && isset($values['counter']) && $values['counter'])
			{
				$receipt=$this->bo->update_invoice_sub($values);
			}

//_debug_array($values);

//echo $voucher_id;

			if ($voucher_id)
			{
				$content = $this->bo->read_invoice_sub($voucher_id,$paid);
			}

			$sum =0;
			$i=0;
			if(is_array($content))
			{
				while(each($content))
				{
					$sum									= $sum + $content[$i]['amount'];
					$content[$i]['amount'] 					= number_format($content[$i]['amount'], 2, ',', '');
					$content[$i]['paid']					= $paid;
					$content[$i]['lang_tax_code_statustext']= lang('select the appropriate tax code');
					$content[$i]['lang_dimb_statustext']= lang('select the appropriate dim code');
					$content[$i]['dimb_list']				= $this->bo->select_dimb_list($content[$i]['dimb']);
					$content[$i]['tax_code_list']			= $this->bo->tax_code_list($content[$i]['tax_code']);
					$content[$i]['lang_remark'] 			= lang('Remark');
					$content[$i]['link_remark'] 			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.remark'));
					$content[$i]['lang_remark_help'] 		= lang('click this link to view the remark');
					$content[$i]['link_order'] 				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.view_order'));
					$content[$i]['link_claim'] 				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitenant_claim.check'));
					$i++;
				}
			}

//_debug_array($content);
			$table_header[] = array
			(
				'sort_workorder'		=> $this->nextmatchs->show_sort_order(array
												(
													'sort'	=> $this->sort,
													'var'	=> 'pmwrkord_code',
													'order'	=> $this->order,
													'extra'	=> array('menuaction'	=> 'property.uiinvoice.list_sub',
															'cat_id'	=> $this->cat_id,
															'sub'		=> $this->sub,
															'paid'		=> $paid,
															'voucher_id'	=> $voucher_id,
															'query'		=> $this->query)
												)),
				'lang_workorder'			=> lang('Workorder'),
				'sort_budget_account'		=> $this->nextmatchs->show_sort_order(array
												(
													'sort'	=> $this->sort,
													'var'	=> 'spbudact_code',
													'order'	=> $this->order,
													'extra'	=> array('menuaction'	=> 'property.uiinvoice.list_sub',
															'cat_id'	=> $this->cat_id,
															'sub'		=> $this->sub,
															'paid'		=> $paid,
															'voucher_id'	=> $voucher_id,
															'query'		=> $this->query)
												)),
				'lang_budget_account'		=> lang('Budget account'),

				'sort_sum'					=> $this->nextmatchs->show_sort_order(array
												(
													'sort'	=> $this->sort,
													'var'	=> 'belop',
													'order'	=> $this->order,
													'extra'	=> array('menuaction'	=> 'property.uiinvoice.list_sub',
															'cat_id'	=> $this->cat_id,
															'sub'		=> $this->sub,
															'paid'		=> $paid,
															'voucher_id'	=> $voucher_id,
															'query'		=> $this->query)
												)),

				'lang_sum'			=> lang('Sum'),
				'lang_type'			=> lang('Type'),
				'lang_close_order'		=> lang('Close order'),
				'lang_charge_tenant'		=> lang('Charge tenant'),
				'lang_invoice_id'		=> lang('Invoice Id'),
				'sort_dima'			=> $this->nextmatchs->show_sort_order(array
												(
													'sort'	=> $this->sort,
													'var'	=>	'dima',
													'order'	=>	$this->order,
													'extra'		=> array('menuaction'	=> 'property.uiinvoice.list_sub',
																'cat_id'	=> $this->cat_id,
																'sub'		=> $this->sub,
																'paid'		=> $paid,
																'voucher_id'	=> $voucher_id,
																'query'		=> $this->query)
												)),
				'lang_dima'			=> lang('Dim A'),
				'lang_dimb'			=> lang('Dim B'),
				'lang_dimd'			=> lang('Dim D'),
				'lang_tax_code'			=> lang('Tax code'),
				'lang_remark'			=> lang('Remark'),
			);

			$table_done[] = array
			(
				'lang_done'		=> lang('Done'),
				'lang_done_statustext'	=> lang('Close this window')
			);

			$link_data = array
			(
				'menuaction'		=> 'property.uiinvoice.list_sub',
				'order'			=> $this->order,
				'sort'			=> $this->sort,
				'cat_id'		=> $this->cat_id,
				'user_lid'		=> $this->user_lid,
				'sub'			=> $this->sub,
				'query'			=> $this->query,
				'start'			=> $this->start,
				'paid'			=> $paid,
				'voucher_id'		=> $voucher_id,
				'user_lid'		=> $this->user_lid,
				'query'			=> $this->query
			);

			if ($paid)
			{
				$function_msg	= lang('list paid invoice');

			}
			else
			{
				$function_msg	= lang('list invoice');
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			$link_download = array
			(
				'menuaction'	=> 'property.uiinvoice.download_sub',
				'voucher_id'	=> $voucher_id,
				'paid'		=> $paid
			);

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');
			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$data = array
			(
				'menu'							=> $this->bocommon->get_menu(),
				'lang_download'					=> 'download',
				'link_download'					=> $GLOBALS['phpgw']->link('/index.php',$link_download),
				'lang_download_help'				=> lang('Download table to your browser'),

				'img_check'					=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'sum'						=> number_format($sum, 2, ',', ''),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('Done'),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.index', 'user_lid'=> $this->user_lid, 'query'=> $this->query)),
				'lang_done_statustext'				=> lang('Back to the list'),
				'lang_save_statustext'				=> lang('Save the voucher'),
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> count($content),//$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($content),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_submit'					=> lang('submit'),
				'table_header_list_invoice_sub'			=> $table_header,
				'values_list_invoice_sub'			=> $content,
				'paid'						=> $paid,
				'vendor'					=> $content[0]['vendor'],
				'lang_vendor'					=> lang('Vendor'),
				'voucher_id'					=> $voucher_id,
				'lang_voucher_id'				=> lang('Voucher Id'),
				'lang_claim'					=> lang('Claim'),
				'table_done'					=> $table_done
			);

//_debug_array($data);

			$appname = lang('invoice');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_invoice_sub' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}


		function edit_period()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$voucher_id 	= phpgw::get_var('voucher_id', 'int');
			$period 	= phpgw::get_var('period', 'int');
			$submit 	= phpgw::get_var('submit', 'bool');

			$receipt = array();
			if($submit)
			{
				$receipt	= $this->bo->update_period($voucher_id,$period);
			}

			$function_msg	= lang('Edit period');

			$link_data = array
			(
				'menuaction'	=> 'property.uiinvoice.edit_period',
				'voucher_id'	=> $voucher_id);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'		=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'period_list'		=> $this->bo->period_list($period),
				'function_msg'		=> $function_msg,
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_save'		=> lang('save'),
				'select_name'		=> 'period'
			);

//_debug_array($data);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_period' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function remark()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice'));
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$id 	= phpgw::get_var('id', 'int');
			$paid 	= phpgw::get_var('paid', 'bool');

			$data = array
			(
				'remark' => $this->bo->read_remark($id,$paid)
			);

//_debug_array($data);

			$appname	= lang('invoice');
			$function_msg	= lang('remark');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('remark' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function consume()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::consume';

			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice',
										'nextmatchs',
										'search_field'));

			$start_date 		= phpgw::get_var('start_date');
			$end_date 		= phpgw::get_var('end_date');
			$submit_search 		= phpgw::get_var('submit_search', 'bool');
			$vendor_id 		= phpgw::get_var('vendor_id', 'int');

			$workorder_id 		= phpgw::get_var('workorder_id', 'int');
			$loc1 			= phpgw::get_var('loc1');
			$district_id 		= phpgw::get_var('district_id', 'int');
			$b_account_class 	= phpgw::get_var('b_account_class', 'int');

			if($vendor_id)
			{
				$contacts		= CreateObject('property.soactor');
				$contacts->role		= 'vendor';
				$vendor			= $contacts->read_single(array('actor_id'=>(int)$vendor_id));
				if(is_array($vendor))
				{
					foreach($vendor['attributes'] as $attribute)
					{
						if($attribute['name']=='org_name')
						{
							$vendor_name=$attribute['value'];
							break;
						}
					}
				}
			}

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
//_debug_array($values);
			if(!$submit_search)
			{
				$start_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,date("m"),date("d"),date("Y")),$dateformat);
				$end_date	= $start_date;
			}
			else
			{
				$content = $this->bo->read_consume($start_date,$end_date,$vendor_id,$loc1,$workorder_id,$b_account_class,$district_id);
			}

			if(is_array($content))
			{
				$p_year = date("Y",strtotime($start_date));
			$p_month = date("m",strtotime($start_date));
			$i=0;
				while(each($content))
				{
					$p_start_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,$content[$i]['period'],1,$p_year),$dateformat);
					$p_end_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,($content[$i]['period']+1),0,$p_year),$dateformat);
					$sum				= $sum+$content[$i]['consume'];
					$content[$i]['link_voucher'] 	= $GLOBALS['phpgw']->link('/index.php',array(
														'menuaction'	=> 'property.uiinvoice.index',
														'paid'		=> true,
														'user_lid'	=> 'all',
														'district_id'	=> $district_id,
														'b_account_class'=> $b_account_class,
														'start_date'	=> $p_start_date,
														'end_date'	=> $p_end_date
														)
													);
					$content[$i]['consume'] 	= number_format($content[$i]['consume'], 0, ',', ' ');
					$i++;
				}
			}


			$table_header[] = array
			(
				'lang_district'			=> lang('District'),
				'lang_period'			=> lang('Period'),
				'lang_budget_account'		=> lang('Budget account'),
				'lang_consume'			=> lang('Consume'),
			);

			$table_done[] = array
			(
				'lang_done'		=> lang('Done'),
				'lang_done_statustext'	=> lang('Close this window')
			);

			$link_data = array
			(
				'menuaction'		=> 'property.uiinvoice.consume',
				'order'			=> $this->order,
				'sort'			=> $this->sort,
				'cat_id'		=> $this->cat_id,
				'district_id'		=> $district_id,
				'sub'			=> $this->sub,
				'query'			=> $this->query,
				'start'			=> $this->start,
				'filter'		=> $this->filter
			);

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');
			$data['menu']						= $this->bocommon->get_menu();
			$data['lang_sum']				= lang('Sum');
			$data['sum']					= number_format($sum, 0, ',', ' ');
			$data['allow_allrows']				= false;
			$data['start_record']				= $this->start;
			$data['record_limit']				= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$data['num_records']				= count($content);
			$data['all_records']				= $this->bo->total_records;
			$data['link_url']				= $GLOBALS['phpgw']->link('/index.php',$link_data);
			$data['img_path']				= $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default');
			$data['lang_no_cat']				= lang('no category');
			$data['lang_cat_statustext']			= lang('Select the category the building belongs to. To do not use a category select NO CATEGORY');
			$data['select_name']				= 'cat_id';
			$data['select_action']				= $GLOBALS['phpgw']->link('/index.php',$link_data);
			$data['lang_no_district']			= lang('No district');
			$data['lang_district_statustext']		= lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT');
			$data['select_district_name']			= 'district_id';
			$data['lang_searchfield_statustext']		= lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again');
			$data['lang_searchbutton_statustext']		= lang('Submit the search string');
			$data['lang_search']				= lang('search');
			$data['query']					= $this->query;
			$data['form_action']				= $GLOBALS['phpgw']->link('/index.php',$link_data);

			$data['district_list']				= $this->bocommon->select_district_list('select',$district_id);
			$data['cat_list']				= $this->bo->select_category('select',$this->cat_id);
			$data['start_date']				= $start_date;
			$data['end_date']				= $end_date;
			$data['vendor_id']				= $vendor_id;
			$data['vendor_name']				= $vendor_name;

			$data['account_class_list']			= $this->bo->select_account_class($b_account_class);
			$data['lang_no_account_class']			= lang('No account');
			$data['lang_account_class_statustext']		= lang('Select the account class the selection belongs to');
			$data['select_account_class_name']		= 'b_account_class';

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('start_date');
			$jscal->add_listener('end_date');

			$data['img_cal']						= $GLOBALS['phpgw']->common->image('phpgwapi','cal');
			$data['lang_datetitle']				= lang('Select date');

			$data['lang_workorder']				= lang('Workorder ID');
			$data['lang_workorder_statustext']		= lang('enter the Workorder ID to search by workorder - at any date');
			$data['workorder_id']				= $workorder_id;

			$data['addressbook_link']			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.vendor'));
			$data['lang_select_vendor_statustext']		= lang('Select the vendor by clicking this link');
			$data['lang_vendor']				= lang('Vendor');

			$bolocation					= CreateObject('property.bolocation');
			$location_data					= $bolocation->initiate_ui_location(array('type_id'=> 1));

			$data['property_link']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.index', 'lookup'=> 1, 'type_id'=> 1, 'lookup_name'=> 0));

			$data['lang_select_property_statustext']	= lang('Select the property by clicking this link');
			$data['lang_property_statustext']		= lang('Search by property');

			$data['lang_property']				= lang('property');
			$data['loc1']					= $loc1;
			$data['lang_search']				= lang('Search');
			$data['lang_search_statustext']			= lang('Search for paid invoices');

			$data['table_header_consume']			= $table_header;
			$data['values_consume']				= $content;

			$appname					= lang('consume');
			$function_msg					= lang('list consume');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('consume' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();

			$this->save_sessiondata();
		}

		function delete()
		{
			$voucher_id = phpgw::get_var('voucher_id', 'int');

			//cramirez add JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
	    		$this->bo->delete($voucher_id);
	    		$json = array
	    		(
	    			'result' 		=> 1,
    				'voucher_id' 	=> $voucher_id
	    		);
				return $json ;
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
				$values['art']			= phpgw::get_var('art', 'int');
				$values['type']			= phpgw::get_var('type');
				$values['dim_b']		= phpgw::get_var('dim_b', 'int');
				$values['invoice_num']		= phpgw::get_var('invoice_num');
				$values['kid_nr']		= phpgw::get_var('kid_nr');
				$values['vendor_id']		= phpgw::get_var('vendor_id', 'int');
				$values['vendor_name']		= phpgw::get_var('vendor_name');
				$values['janitor']		= phpgw::get_var('janitor');
				$values['supervisor']		= phpgw::get_var('supervisor');
				$values['budget_responsible']	= phpgw::get_var('budget_responsible');
				$values['invoice_date'] 	= urldecode(phpgw::get_var('invoice_date'));
				$values['num_days']		= phpgw::get_var('num_days', 'int');
				$values['payment_date'] 	= urldecode(phpgw::get_var('payment_date'));
				$values['sday'] 		= phpgw::get_var('sday', 'int');
				$values['smonth'] 		= phpgw::get_var('smonth', 'int');
				$values['syear']		= phpgw::get_var('syear', 'int');
				$values['eday'] 		= phpgw::get_var('eday', 'int');
				$values['emonth'] 		= phpgw::get_var('emonth', 'int');
				$values['eyear']		= phpgw::get_var('eyear', 'int');
				$values['auto_tax'] 		= phpgw::get_var('auto_tax', 'bool');
				$values['merknad']		= phpgw::get_var('merknad');
				$values['b_account_id']		= phpgw::get_var('b_account_id', 'int');
				$values['b_account_name']	= phpgw::get_var('b_account_name');
				$values['amount']		= phpgw::get_var('amount', 'float');
				$values['order_id']		= phpgw::get_var('order_id', 'int');

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$values = $this->bocommon->collect_locationdata($values,$insert_record);

				$GLOBALS['phpgw']->session->appsession('session_data','add_values',$values);
			}
			else
			{
				$values	= $GLOBALS['phpgw']->session->appsession('session_data','add_values');
				$GLOBALS['phpgw']->session->appsession('session_data','add_values','');
			}

			$location_code 			= phpgw::get_var('location_code');
			$debug 				= phpgw::get_var('debug', 'bool');
			$add_invoice 			= phpgw::get_var('add_invoice', 'bool');


			if($location_code)
			{
				$values['location_data'] = $bolocation->read_single($location_code,array('tenant_id'=>$tenant_id,'p_num'=>$p_num));
			}

			if($add_invoice && is_array($values))
			{

				if($values['order_id'] && !ctype_digit($values['order_id'])):
				{
					$receipt['error'][]=array('msg'=>lang('Please enter an integer for order!'));
					unset($values['order_id']);
				}
				elseif($values['order_id']):
				{
					$order=true;
				}
				endif;

				if (!$values['amount'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - enter an amount!'));
				}
				if (!$values['art'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - select type invoice!'));
				}
				if (!$values['vendor_id'] && (!isset($order) || !$order))
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

				if((!isset($order) || !$order) && $values['vendor_id'])
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
					$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					$dateformat = str_replace(".","",$dateformat);
					$dateformat = str_replace("-","",$dateformat);
					$dateformat = str_replace("/","",$dateformat);
					$y=strpos($dateformat,'y');
					$d=strpos($dateformat,'d');
					$m=strpos($dateformat,'m');

					if($values['invoice_date'])
					{
			 			$dateparts = explode('/', $values['invoice_date']);
			 			$values['sday'] = $dateparts[$d];
			 			$values['smonth'] = $dateparts[$m];
			 			$values['syear'] = $dateparts[$y];

			 			$dateparts = explode('/', $values['payment_date']);
			 			$values['eday'] = $dateparts[$d];
			 			$values['emonth'] = $dateparts[$m];
			 			$values['eyear'] = $dateparts[$y];
					}

					$values['regtid'] 		= date($this->bocommon->datetimeformat);

					$receipt = $this->bo->add($values,$debug);

					if($debug)
					{
						$this->debug($receipt);
						return;
					}
					unset($values);
					$GLOBALS['phpgw']->session->appsession('session_data','add_receipt',$receipt);
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiinvoice.add'));
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

			$location_data=$bolocation->initiate_ui_location(array(
   						'values'	=> isset($values['location_data'])?$values['location_data']:'',
   						'type_id'	=> -1, // calculated from location_types
   						'no_link'	=> false, // disable lookup links for location type less than type_id
   						'tenant'	=> false,
   						'lookup_type'	=> 'form',
   						'lookup_entity'	=> false, //$this->bocommon->get_lookup_entity('project'),
   						'entity_data'	=> false //$values['p']
   						));

			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> isset($values['b_account_id'])?$values['b_account_id']:'',
						'b_account_name'	=> isset($values['b_account_name'])?$values['b_account_name']:''));

			$link_data = array
			(
				'menuaction'	=> 'property.uiinvoice.add',
				'debug'		=> true
			);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('invoice_date');
			$jscal->add_listener('payment_date');

			$data = array
			(
				'menu'							=> $this->bocommon->get_menu(),
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'img_cal'							=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'					=> lang('Select date'),

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

			$GLOBALS['phpgw_info']['flags'][noheader] = true;
			$GLOBALS['phpgw_info']['flags'][nofooter] = true;
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

				$contacts	= CreateObject('property.soactor');
				$contacts->role='vendor';
				if($values[0]['vendor_id'])
				{
					$custom 					= createObject('property.custom_fields');
					$vendor_data['attributes']	= $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);
					$vendor_data				= $contacts->read_single($values[0]['vendor_id'],$vendor_data);
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
			$pdf->selectFont(PHPGW_APP_INC . '/pdf/fonts/Helvetica.afm');

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
				'location_code'		=> $values[0]['location_code'],
				'art'			=> $values[0]['art'],
				'type'			=> $values[0]['type'],
				'dim_b'			=> $values[0]['dim_b'],
				'invoice_num'		=> $values[0]['invoice_num'],
				'kid_nr'		=> $values[0]['kid_nr'],
				'vendor_id'		=> $values[0]['spvend_code'],
				'vendor_name'		=> $values[0]['vendor_name'],
				'janitor'		=> $values[0]['janitor'],
				'supervisor'		=> $values[0]['supervisor'],
				'budget_responsible'	=> $values[0]['budget_responsible'],
				'invoice_date' 		=> urlencode($values[0]['invoice_date']),
				'num_days'		=> $values[0]['num_days'],
				'payment_date' 		=> urlencode($values[0]['payment_date']),
				'sday' 			=> $values[0]['sday'],
				'smonth' 		=> $values[0]['smonth'],
				'syear'			=> $values[0]['syear'],
				'eday' 			=> $values[0]['eday'],
				'emonth' 		=> $values[0]['emonth'],
				'eyear'			=> $values[0]['eyear'],
				'auto_tax' 		=> $values[0]['auto_tax'],
				'merknad'		=> $values[0]['merknad'],
				'b_account_id'		=> $values[0]['spbudact_code'],
				'b_account_name'	=> $values[0]['b_account_name'],
				'amount'		=> $values[0]['amount'],
				'order'			=> $values[0]['order'],
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


			$import = array(
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
				'artid'					=> $values[0]['artid'],
				'lang_type'				=> lang('Type'),
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
				'lang_budget_responsible'		=> lang('Budget Responsible'),
				'budsjettansvarligid'			=> $values[0]['budsjettansvarligid'],
				'lang_sum'				=> lang('Sum'),
				'sum'					=> number_format($values[0]['amount'], 2, ',', ''),
				'table_header'				=> $table_header,
				'values'				=> $content,
				'table_add'				=> $table_add
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
			$order_id	= phpgw::get_var('order_id', 'int');
			$soXport    = CreateObject('property.soXport');

			$order_type = $soXport->check_order(intval($order_id));
			switch($order_type)
			{
				case 'workorder':
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiwo_hour.view', 'no_email'=> true, 'show_cost'=> true, 'workorder_id'=> $order_id));
					break;
				case 's_agreement':
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uis_agreement.view', 'id'=> $order_id));
					break;
			}
		}
	}

