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
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uiinvoice extends phpgwapi_uicommon_jquery
	{

		private $receipt = array();
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
			'index' => true,
			'list_sub' => true,
			'consume' => true,
			'remark' => true,
			'delete' => true,
			'add' => true,
			'debug' => true,
			'view_order' => true,
			'download' => true,
			'download_sub' => true,
			'receipt' => true,
			'edit' => true,
			'reporting' => true,
			'forward' => true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::economy::invoice';
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo = CreateObject('property.boinvoice', true);
			$this->bocommon = &$this->bo->bocommon;

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->cat_id = $this->bo->cat_id;
			$this->user_lid = $this->bo->user_lid;
			$this->allrows = $this->bo->allrows;
			$this->district_id = $this->bo->district_id;

			$this->acl = & $GLOBALS['phpgw']->acl;

			$this->acl_location = '.invoice';
			$this->acl_read = $this->acl->check('.invoice', PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check('.invoice', PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check('.invoice', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check('.invoice', PHPGW_ACL_DELETE, 'property');
		}

		private function _get_Filters()
		{
			$paid = phpgw::get_var('paid', 'bool');
			$b_account_class = phpgw::get_var('b_account_class', 'int');
			$ecodimb = phpgw::get_var('ecodimb');

			$values_combo_box = array();
			$combos = array();

			$values_combo_box[0] = $this->bo->select_category('', $this->cat_id);
			array_unshift($values_combo_box[0], array('id' => '', 'name' => lang('no category')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'cat_id',
				'text' => lang('Category'),
				'list' => $values_combo_box[0]
			);


			$values_combo_box[1] = $this->bo->get_invoice_user_list('select', $this->user_lid, array(
				'all'), $default = 'all');
			array_unshift($values_combo_box[1], array('id' => $GLOBALS['phpgw']->accounts->get($this->account)->lid,
				'name' => lang('mine vouchers')));
			array_unshift($values_combo_box[1], array('id' => '', 'name' => lang('no user')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'user_lid',
				'text' => lang('User'),
				'list' => $values_combo_box[1]
			);

			if ($paid)
			{
				$values_combo_box[2] = $this->bo->select_account_class($b_account_class);
				array_unshift($values_combo_box[2], array('id' => '', 'name' => lang('No account')));
				$combos[] = array
					(
					'type' => 'filter',
					'name' => 'b_account_class',
					'text' => lang('Account'),
					'list' => $values_combo_box[2]
				);

				$values_combo_box[3] = $this->bocommon->select_category_list(array('type' => 'dimb',
					'selected' => $ecodimb));
				array_unshift($values_combo_box[3], array('id' => '', 'name' => lang('no dimb')));
				$combos[] = array
					(
					'type' => 'filter',
					'name' => 'ecodimb',
					'text' => lang('dimb'),
					'list' => $values_combo_box[3]
				);
			}

			return $combos;
		}

		private function _get_Filters_consume()
		{
			$district_id = phpgw::get_var('district_id', 'int');
			$b_account_class = phpgw::get_var('b_account_class', 'int');
			$b_account = phpgw::get_var('b_account', 'int');
	//		$b_account_class = $b_account_class ? $b_account_class : substr($b_account, 0, 2);
			$ecodimb = phpgw::get_var('ecodimb');

			$values_combo_box[0] = $this->bo->select_category('', $this->cat_id);
			array_unshift($values_combo_box[0], array('id' => '', 'name' => lang('no category')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'cat_id',
				'text' => lang('Category'),
				'list' => $values_combo_box[0]
			);

			$values_combo_box[1] = $this->bocommon->select_district_list('select', $district_id);
			array_unshift($values_combo_box[1], array('id' => '', 'name' => lang('no district')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'district_id',
				'text' => lang('District'),
				'list' => $values_combo_box[1]
			);

			$values_combo_box[2] = $this->bo->select_account_class($b_account_class);
			array_unshift($values_combo_box[2], array('id' => '', 'name' => lang('No account')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'b_account_class',
				'text' => lang('No account'),
				'list' => $values_combo_box[2]
			);

			$values_combo_box[3] = $this->bocommon->select_category_list(array('type' => 'dimb',
				'selected' => $ecodimb));
			array_unshift($values_combo_box[3], array('id' => '', 'name' => lang('no dimb')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'ecodimb',
				'text' => lang('dimb'),
				'list' => $values_combo_box[3]
			);

			return $combos;
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start' => $this->start,
				'query' => $this->query,
				'sort' => $this->sort,
				'order' => $this->order,
				'filter' => $this->filter,
				'cat_id' => $this->cat_id,
				'user_lid' => $this->user_lid,
				'allrows' => $this->allrows,
				'district_id' => $this->district_id
			);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{

			$list = $this->query();

                        if (is_array($list[0]))
                        {
                            foreach($list[0] as $name_entry => $value)
			{
				$name[] = $name_entry;
			}
                        }

			$descr = $name;

			$this->bocommon->download($list, $name, $descr);
		}

		function download_sub()
		{
			$list = $this->query_list_sub();

			$name = array
				(
				'workorder_id',
				'external_project_id',
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

			$this->bocommon->download($list, $name, $descr);
		}

		function index()
		{
			//--validacion para permisos
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}
			//-- captura datos de URL
			$paid = phpgw::get_var('paid', 'bool');
			$start_date = phpgw::get_var('start_date');
			$end_date = phpgw::get_var('end_date');
			$submit_search = phpgw::get_var('submit_search', 'bool');
			$vendor_id = phpgw::get_var('vendor_id', 'int');
			$workorder_id = phpgw::get_var('workorder_id', 'int');
			$project_id = phpgw::get_var('project_id', 'int');
			$loc1 = phpgw::get_var('loc1');
			$voucher_id = $this->query && ctype_digit($this->query) ? $this->query : phpgw::get_var('voucher_id');
			$invoice_id = phpgw::get_var('invoice_id');
			$b_account_class = phpgw::get_var('b_account_class', 'int');
			$ecodimb = phpgw::get_var('ecodimb');
			$this->save_sessiondata();

			//-- ubica focus del menu derecho
			if ($paid)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::economy::paid';
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::economy::invoice::invoice';
			}
			//-- captura datos de URL
			$start_date = urldecode($start_date);
			$end_date = urldecode($end_date);

			if (!$start_date)
			{
				$start_date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, '01', '01', date("Y")), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}
			if (!$end_date)
			{
				//-- fecha actual
				$end_date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}
			//-- edicion de registro
			$values = phpgw::get_var('values');

			if (phpgw::get_var('phpgw_return_as') == 'json' && is_array($values) && isset($values))
			{
				$values["save"] = "Save";
//			 	_debug_array($values);
				return $this->bo->update_invoice($values);
			}

			// Edit Period
			$period = phpgw::get_var('period');
			$voucher_id_for_period = phpgw::get_var('voucher_id_for_period');
			if (phpgw::get_var('phpgw_return_as') == 'json' && isset($period) && $period != '')
			{
				return $this->bo->update_period($voucher_id_for_period, $period);
			}

			// Edit Periodization
			$periodization = phpgw::get_var('periodization');
			$voucher_id_for_periodization = phpgw::get_var('voucher_id_for_periodization');
			if (phpgw::get_var('phpgw_return_as') == 'json' && isset($periodization) && $periodization != '')
			{
				return $this->bo->update_periodization($voucher_id_for_periodization, $periodization);
			}

			// Edit Periodization
			$periodization_start = phpgw::get_var('periodization_start');
			$voucher_id_for_periodization_start = phpgw::get_var('voucher_id_for_periodization_start');
			if (phpgw::get_var('phpgw_return_as') == 'json' && isset($periodization_start) && $periodization_start != '')
			{
				return $this->bo->update_periodization_start($voucher_id_for_periodization_start, $periodization_start);
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$appname = lang('invoice');
			$function_msg = lang('list voucher');

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date');
			phpgwapi_jquery::load_widget('datepicker');

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'property.uiinvoice.index',
						'paid' => $paid,
						'ecodimb' => $ecodimb,
						'vendor_id' => $vendor_id,
						'workorder_id' => $workorder_id,
						'project_id' => $project_id,
						'voucher_id' => $voucher_id,
						'invoice_id' => $invoice_id,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'b_account_class' => $b_account_class,
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array(
						'menuaction' => 'property.uiinvoice.download',
						'export' => true,
						'skip_origin' => true,
						'allrows' => true
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);
			if ($this->acl_add)
			{
				$data['datatable']['new_item'] = self::link(array(
						'menuaction' => 'property.uiinvoice.add'
				));
			}
			if ($paid)
			{
				$data['form']['toolbar']['item'][] = array
					(
					'type' => 'date-picker',
					'id' => 'start_date',
					'name' => 'start_date',
					'value' => $start_date,
					'text' => lang('from')
				);
				$data['form']['toolbar']['item'][] = array
					(
					'type' => 'date-picker',
					'id' => 'end_date',
					'name' => 'end_date',
					'value' => $end_date,
					'text' => lang('to')
				);
			}

			$filters = $this->_get_Filters();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$uicols = array(
				'input_type' => array
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
					$paid ? 'varchar' : 'input',
					'varchar',
					'varchar',
					'varchar',
					'hidden',
					'varchar',
					'varchar',
					'varchar',
					'varchar',
					'varchar',
					$paid ? 'hidden' : 'input',
					$paid ? 'hidden' : 'input',
					'special',
					'special',
					'special',
					'special2'
				),
				'type' => array
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
					$paid ? '' : 'text',
					'',
					'text',
					'text',
					'',
					'',
					'',
					'',
					'',
					'',
					$paid ? '' : 'checkbox',
					$paid ? '' : 'radio',
					'',
					'',
					'',
					''
				),
				'col_name' => array
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
				'name' => array
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
				'formatter' => array
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
					'JqueryPortico.FormatterAmount2',
					'',
					'',
					'',
					'',
					'',
					$paid ? '' : 'FormatterPeriod',
					$paid ? '' : 'FormatterPeriodization',
					$paid ? '' : 'FormatterPeriodization_start',
					'',
					'',
					'',
					'',
					'',
					''
				),
				'descr' => array
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
				'className' => array
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
					'center',
					'',
					'',
					'',
					$paid ? 'right' : '',
					'right',
					'',
					'',
					'',
					'right',
					'',
					$paid ? 'center' : 'center',
					$paid ? 'center' : 'center',
					$paid ? 'center' : 'center',
					'dt-center all',
					'dt-center all',
					'dt-center all',
					'dt-center all',
					'dt-center all',
					'dt-center all'
				)
			);

			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array
					(
					'key' => $uicols['col_name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false,
					'className' => ($uicols['className'][$k]) ? $uicols['className'][$k] : ''
				);

				if ($uicols['formatter'][$k])
				{
					$params['formatter'] = $uicols['formatter'][$k];
				}

				array_push($data['datatable']['field'], $params);
			}

			if (!$paid)
			{
				$parameters = array
					(
					'parameter' => array
						(
						array
							(
							'name' => 'voucher_id',
							'source' => 'voucher_id_num'
						),
					)
				);


				$data['datatable']['actions'][] = array
					(
					'my_name' => 'forward',
					'text' => lang('forward'),
					'type' => 'custom',
					'custom_code' => ""
					. "
										var selected = JqueryPortico.fnGetSelected(oTable);

										if (selected.length ==0){
											alert('None selected');
											return false;
										}
										var aData = oTable.fnGetData( selected[0] );
										var voucher_id_num = aData['voucher_id_num'];
							"
					. "JqueryPortico.openPopup({menuaction:'property.uiinvoice.forward', voucher_id: voucher_id_num}, {closeAction:'reload'})"
				);

				if ($this->acl_delete)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'delete',
						'text' => lang('delete'),
						'confirm_msg' => lang('do you really want to delete this entry'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiinvoice.delete',
						)),
						'parameters' => json_encode($parameters)
					);
				}

				$data['datatable']['actions'][] = array
					(
					'my_name' => 'f',
					'text' => lang('F'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uiinvoice.receipt'
					)),
					'target' => '_blank',
					'parameters' => json_encode($parameters)
				);
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'list_sub',
					'text' => lang('details'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uiinvoice.list_sub',
						'user_lid' => 'all'
					)),
					'parameters' => json_encode($parameters)
				);

				$data['datatable']['actions'][] = array
					(
					'my_name' => 'save',
					'text' => lang('save'),
					'className' => 'save',
					'type' => 'custom',
					'custom_code' => "onSave();"
				);

				unset($parameters);
			}

			$periodization_list = execMethod('property.bogeneric.get_list', array('type' => 'periodization'));

			if ($periodization_list)
			{
				array_unshift($periodization_list, array('id' => '0', 'name' => lang('none')));
			}

			$jscode = <<<JS
			    FormatterPeriodization = function(key, oData)
			   	{
JS;
			$jscode .= <<<JS
					var tmp_count = oData['counter_num'];
					var voucher_id_num = oData['voucher_id_num'];
		            var menu = [

JS;
			foreach ($periodization_list as $key => $periodization_entry)
			{
				$jscode_arr[] = "{ text: '{$periodization_entry['name']}', value: '{$periodization_entry['id']}' }";
			}

			$jscode_inner = implode(",\n", $jscode_arr);
			$jscode .= <<<JS
			$jscode_inner
			];

					var combo = $("<select></select>");

					$.each(menu, function (k, v) 
					{
						if (oData[key] == v.value)
						{
							combo.append($("<option selected></option>").attr("value", v.value).text(v.text));
						} else {
							combo.append($("<option></option>").attr("value", v.value).text(v.text));
						}
					});

					return "<select id='cboPeriodization"+tmp_count+"' onchange='onPeriodizationItemClick(this,"+voucher_id_num+")'>" + $(combo).html() + "</select>";
				}
JS;
			$GLOBALS['phpgw']->js->add_code('', $jscode, true);


			$inputFilters = array(
				array('id' => 'workorder_id', 'label' => lang('Workorder ID'), 'type' => 'text'),
				array('id' => '', 'label' => "<a href=\"#\" onClick=\"JqueryPortico.openPopup({menuaction:'property.uilookup.vendor'})\">" . lang('Vendor') . "</a>",
					'type' => 'link'),
				array('id' => 'vendor_id', 'label' => lang('Vendor'), 'type' => 'text'),
				array('id' => 'vendor_name', 'label' => '', 'type' => 'hidden'),
				array('id' => 'invoice_id', 'label' => lang('invoice number'), 'type' => 'text'),
				array('id' => '', 'label' => "<a href=\"#\" onClick=\"JqueryPortico.openPopup({menuaction:'property.uilocation.index', lookup:'1', type_id:'1', lookup_name:'0'})\">" . lang('property') . "</a>",
					'type' => 'link'),
				array('id' => 'loc1', 'label' => lang('property'), 'type' => 'text'),
				array('id' => 'loc1_name', 'label' => '', 'type' => 'hidden'),
				array('id' => 'voucher_id', 'label' => lang('Voucher ID'), 'type' => 'text')
			);

			$code = "var inputFilters = " . json_encode($inputFilters);

			$code .= <<<JS
				
				function initCompleteDatatable(oSettings, json, oTable) 
				{
					$('#datatable-container_filter').empty();
					$.each(inputFilters, function(i, val) 
					{
						if (val['type'] == 'text') 
						{
							$('#datatable-container_filter').append('<input type="text" placeholder="'+val['label']+'" id="'+val['id']+'" name="'+val['id']+'" />');
						}
						else if (val['type'] == 'hidden') 
						{
							$('#datatable-container_filter').append('<input type="hidden" id="'+val['id']+'" name="'+val['id']+'" />');
						}
						else {
							$('#datatable-container_filter').append(val['label']);
						}
					});
					
					var valuesInputFilter = {};
					
					$.each(inputFilters, function(i, val) 
					{
						if (val['type'] == 'text') 
						{
							valuesInputFilter[val['id']] = '';
							$( '#' + val['id']).on( 'keyup change', function () 
							{
								if ( $.trim($(this).val()) != $.trim(valuesInputFilter[val['id']]) ) 
								{
									filterData(val['id'], $(this).val());
									valuesInputFilter[val['id']] = $(this).val();
								}
							});
						}
					});
					
					setTimeout(function() 
					{
						var rds_supervisor = $('.supervisorClass');
						var rds_budget_responsible = $('.budget_responsibleClass');
					
						rds_budget_responsible.each(function(i, obj) 
						{
							if ($(obj).attr("checked"))
							{
								obj.checked = true;
							}
						});
						rds_supervisor.each(function(i, obj) 
						{
							if ($(obj).attr("checked"))
							{
								obj.checked = true;
							}
						});
					}, 100);					
				};
				
				function afterPopupClose() 
				{
					$('#loc1').change();
					$('#vendor_id').change();
				};
JS;

			$GLOBALS['phpgw']->js->add_code('', $code, true);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('property', 'portico', 'invoice.index.js');
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$paid = phpgw::get_var('paid', 'bool');
			$start_date = phpgw::get_var('start_date');
			$end_date = phpgw::get_var('end_date');
			$vendor_id = phpgw::get_var('vendor_id', 'int');
			$workorder_id = phpgw::get_var('workorder_id', 'int');
			$project_id = phpgw::get_var('project_id', 'int');
			$loc1 = phpgw::get_var('loc1');
			$voucher_id = $this->query && ctype_digit($this->query) ? $this->query : phpgw::get_var('voucher_id');
			$invoice_id = phpgw::get_var('invoice_id');
			$ecodimb = phpgw::get_var('ecodimb');
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export = phpgw::get_var('export', 'bool');

			$params = array
				(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'paid' => $paid,
				'vendor_id' => $vendor_id,
				'workorder_id' => $workorder_id,
				'project_id' => $project_id,
				'loc1' => $loc1,
				'voucher_id' => $voucher_id,
				'invoice_id' => $invoice_id,
				'ecodimb' => $ecodimb
			);

			$invoice_list = $this->bo->read_invoice($params);

			if ($export)
			{
				return $invoice_list;
			}

			$uicols = array(
				'input_type' => array
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
					$paid ? 'varchar' : 'input',
					'varchar',
					'varchar',
					'varchar',
					'hidden',
					'varchar',
					'varchar',
					'varchar',
					'varchar',
					'varchar',
					$paid ? 'hidden' : 'input',
					$paid ? 'hidden' : 'input',
					'special',
					'special',
					'special',
					'special2'
				),
				'type' => array
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
					$paid ? '' : 'text',
					'',
					'text',
					'text',
					'',
					'',
					'',
					'',
					'',
					'',
					$paid ? '' : 'checkbox',
					$paid ? '' : 'radio',
					'',
					'',
					'',
					''
				),
				'col_name' => array
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
				'name' => array
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
				'formatter' => array
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
					'JqueryPortico.FormatterAmount2',
					'',
					'',
					'',
					'',
					'',
					$paid ? '' : 'myPeriodDropDown',
					$paid ? '' : 'myPeriodizationDropDown',
					$paid ? '' : 'myPeriodization_startDropDown',
					'',
					'',
					'',
					'',
					'',
					''
				),
				'descr' => array
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
				'className' => array
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
					$paid ? 'rightClasss' : '',
					'rightClasss',
					'',
					'',
					'',
					'rightClasss',
					'',
					$paid ? 'centerClasss' : 'comboClasss',
					$paid ? 'centerClasss' : 'comboClasss',
					$paid ? 'centerClasss' : 'comboClasss',
					'centerClasss',
					'centerClasss',
					'',
					'',
					'centerClasss',
					'centerClasss'
				)
			);

			$link_sub = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.list_sub',
				'user_lid' => $this->user_lid));

			if ($paid)
			{
				$link_sub.="&paid=true";
			}

			$data = array();
			$j = 0;
			if (isset($invoice_list) && is_array($invoice_list))
			{
				foreach ($invoice_list as $invoices)
				{
					for ($i = 0; $i < count($uicols['name']); $i++)
					{
						//values column kreditnota
						if ($uicols['type'][$i] == 'checkbox' && $uicols['col_name'][$i] == 'kreditnota_tmp')
						{
							$data[$j]['column'][$i]['value'] = 'true';
						}
						//values column sign
						else if ($uicols['type'][$i] == 'radio' && $uicols['col_name'][$i] == 'sign_tmp')
						{
							$data[$j]['column'][$i]['value'] = 'sign_none';
						}
						// others columnas
						else
						{
							$data[$j]['column'][$i]['value'] = $invoices[$uicols['name'][$i]];
						}

						$data[$j]['column'][$i]['name'] = $uicols['col_name'][$i];

						if ($uicols['input_type'][$i] != 'hidden')
						{
							//--- varchar--
							if ($uicols['input_type'][$i] == 'varchar' && $invoices[$uicols['name'][$i]])
							{
								$data[$j]['column'][$i]['format'] = 'varchar';
							}
							//--- link--
							else if ($uicols['input_type'][$i] == 'link' && $invoices[$uicols['name'][$i]])
							{
								$data[$j]['column'][$i]['format'] = 'link';
								$data[$j]['column'][$i]['link'] = '#';
								if ($uicols['type'][$i] == 'url')
								{
									$data[$j]['column'][$i]['link'] = $link_sub . "&voucher_id=" . $invoices[$uicols['name'][$i]];
								}
								$data[$j]['column'][$i]['target'] = '';
							}
							//--- special--
							else if ($uicols['input_type'][$i] == 'special')
							{
								// the same name of columns
								$type_sign = $data[$j]['column'][$i]['format'] = $uicols['name'][$i];
								$data[$j]['column'][$i]['for_json'] = $uicols['col_name'][$i];

								//LOGICA
								if (!$paid)
								{
									if (($invoices['is_janitor'] == 1 && $type_sign == 'janitor') || ($invoices['is_supervisor'] == 1 && $type_sign == 'supervisor') || ($invoices['is_budget_responsible'] == 1 && $type_sign == 'budget_responsible'))
									{
										if (( (!$invoices['jan_date']) && $type_sign == 'janitor') || ((!$invoices['super_date']) && $type_sign == 'supervisor') || ((!$invoices['budget_date']) && $type_sign == 'budget_responsible'))
										{
											$data[$j]['column'][$i]['name'] = 'sign_tmp';
											$data[$j]['column'][$i]['type'] = 'radio';
											$data[$j]['column'][$i]['value'] = ($type_sign == 'janitor' ? 'sign_janitor' : ($type_sign == 'supervisor' ? 'sign_supervisor' : 'sign_budget_responsible'));
											$data[$j]['column'][$i]['extra_param'] = "";
										}
										else if ((($invoices['janitor'] == $invoices['current_user']) && $type_sign == 'janitor') || (($invoices['supervisor'] == $invoices['current_user']) && $type_sign == 'supervisor') || (($invoices['budget_responsible'] == $invoices['current_user']) && $type_sign == 'budget_responsible'))
										{
											$data[$j]['column'][$i]['name'] = 'sign_tmp';
											$data[$j]['column'][$i]['type'] = 'radio';
											$data[$j]['column'][$i]['value'] = ($type_sign == 'janitor' ? 'sign_janitor' : ($type_sign == 'supervisor' ? 'sign_supervisor' : 'sign_budget_responsible'));
											$data[$j]['column'][$i]['extra_param'] = ' checked="true" ';
										}
										else
										{
											$data[$j]['column'][$i]['name'] = '';
											$data[$j]['column'][$i]['type'] = 'checkbox';
											$data[$j]['column'][$i]['value'] = '';
											$data[$j]['column'][$i]['extra_param'] = " disabled=\"disabled\" checked ";
										}
									}
									else
									{
										if ((!$invoices['jan_date'] && $type_sign == 'janitor') || (!$invoices['super_date'] && $type_sign == 'supervisor') || (!$invoices['budget_date'] && $type_sign == 'budget_responsible'))
										{

										}
										else
										{
											$data[$j]['column'][$i]['name'] = '';
											$data[$j]['column'][$i]['type'] = 'checkbox';
											$data[$j]['column'][$i]['value'] = '';
											$data[$j]['column'][$i]['extra_param'] = " disabled=\"disabled\" checked ";
										}
									}
									$data[$j]['column'][$i]['value2'] = $type_sign == 'janitor' ? $invoices['janitor'] : ($type_sign == 'supervisor' ? $invoices['supervisor'] : $invoices['budget_responsible']);
									$data[$j]['column'][$i]['value0'] = $type_sign == 'janitor' ? $invoices['jan_date'] : ($type_sign == 'supervisor' ? $invoices['super_date'] : $invoices['budget_date']);
								}
								else //if($paid)
								{
									$data[$j]['column'][$i]['value2'] = ($type_sign == 'janitor' ? ($invoices['jan_date'] . " - " . $invoices['janitor']) : ($type_sign == 'supervisor' ? ($invoices['super_date'] . " - " . $invoices['supervisor']) : ($invoices['budget_date'] . " - " . $invoices['budget_responsible'])));
								}
							}
							//---- speciual2----
							else if ($uicols['input_type'][$i] == 'special2')
							{
								// the same name of columns
								$type_sign = $data[$j]['column'][$i]['format'] = $uicols['name'][$i];
								$data[$j]['column'][$i]['for_json'] = $uicols['col_name'][$i];

								if (!$paid)
								{
									if (($invoices['is_transfer'] == 1))
									{
										if (!$invoices['transfer_date'])
										{
											$data[$j]['column'][$i]['name'] = 'transfer_tmp';
											$data[$j]['column'][$i]['type'] = 'checkbox';
											$data[$j]['column'][$i]['value'] = 'true';
											$data[$j]['column'][$i]['extra_param'] = "";
										}
										else
										{
											$data[$j]['column'][$i]['name'] = 'transfer_tmp';
											$data[$j]['column'][$i]['type'] = 'checkbox';
											$data[$j]['column'][$i]['value'] = 'true';
											$data[$j]['column'][$i]['extra_param'] = " checked ";
										}
									}
									else
									{
										if (($invoices['transfer_id'] != ''))
										{
											$data[$j]['column'][$i]['name'] = '';
											$data[$j]['column'][$i]['type'] = 'checkbox';
											$data[$j]['column'][$i]['value'] = '';
											$data[$j]['column'][$i]['extra_param'] = " disabled=\"disabled\" checked ";
										}
									}

									$data[$j]['column'][$i]['value2'] = $invoices['transfer_id'];
								}
								else //if($paid)
								{
									$data[$j]['column'][$i]['value2'] = $invoices['transfer_date'] . " - " . $invoices['transfer_id'];
								}
							}
							else //for input controls
							{
								$data[$j]['column'][$i]['format'] = $uicols['input_type'][$i];
								$data[$j]['column'][$i]['type'] = $uicols['type'][$i];

								if ($data[$j]['column'][$i]['type'] == 'text')
								{
									$data[$j]['column'][$i]['extra_param'] = "size='1' ";
								}
								else if ($uicols['col_name'][$i] == 'kreditnota_tmp' && $invoices[$uicols['name'][$i]] == '1')
								{
									$data[$j]['column'][$i]['extra_param'] = " checked ";
								}
								else
								{
									$data[$j]['column'][$i]['extra_param'] = " ";
								}
							}
						}
						else
						{
							$data[$j]['column'][$i]['format'] = 'hidden';
							$data[$j]['column'][$i]['type'] = $uicols['type'][$i];
						}
					}

					$j++;
				}
			}

			$values = array();

			if (isset($data) && is_array($data))
			{
				$k = 0;
				foreach ($data as $row)
				{
					$json_row = array();
					foreach ($row['column'] as $column)
					{
						//-- links a otros modulos
						if ($column['format'] == "link")
						{
							if ($column['name'] == 'voucher_id_lnk')
							{
								$_value = isset($invoice_list[$k]['voucher_out_id']) && $invoice_list[$k]['voucher_out_id'] ? $invoice_list[$k]['voucher_out_id'] : $column['value'];
								$json_row[$column['name']] = "<a target='" . $column['target'] . "' href='" . $column['link'] . "' >" . $_value . "</a>";
							}
							else
							{
								$json_row[$column['name']] = "<a target='" . $column['target'] . "' href='" . $column['link'] . "' >" . $column['value'] . "</a>";
							}
						}
						else if ($column['format'] == "input")
						{
							//this class was used for botton selectAll in Footer Datatable
							if ($column['name'] == 'sign_tmp')
							{
								$json_row[$column['name']] = " <input name='values[" . $column['name'] . "][" . $k . "]' id='values[" . $column['name'] . "][" . $k . "]' class=' signClass' type='" . $column['type'] . "' value='" . $column['value'] . "' " . $column['extra_param'] . "/>";
							}
							else if ($column['name'] == 'kreditnota_tmp')
							{
								$json_row[$column['name']] = " <input name='values[" . $column['name'] . "][" . $k . "]' id='values[" . $column['name'] . "][" . $k . "]' class=' kreditnota_tmp' type='" . $column['type'] . "' value='" . $column['value'] . "' " . $column['extra_param'] . "/>";
							}
							else
							{
								$json_row[$column['name']] = " <input name='values[" . $column['name'] . "][" . $k . "]' id='values[" . $column['name'] . "][" . $k . "]' class='myValuesForPHP' type='" . $column['type'] . "' value='" . $column['value'] . "' " . $column['extra_param'] . "/>";
							}
						}
						else if ($column['format'] == "varchar")
						{
							$json_row[$column['name']] = $column['value'];
						}
						else if ($column['format'] == "janitor" || $column['format'] == "supervisor" || $column['format'] == "budget_responsible" || $column['format'] == "transfer_id")
						{
							$tmp_lnk = "";
							//this class was used for botton selectAll in Footer Datatable
							$class = $column['format'] . "Class";
							if ($column['type'] != '')
							{
								if ($column['name'] == '')
								{
									$tmp_lnk = " <input name='" . $column['name'] . "' type='" . $column['type'] . "' value='" . $column['value'] . "' " . $column['extra_param'] . " class='" . $class . "' />";
								}
								else
								{
									$tmp_lnk = " <input name='values[" . $column['name'] . "][" . $k . "]' id='values[" . $column['name'] . "][" . $k . "]' class='" . $class . "' type='" . $column['type'] . "' value='" . $column['value'] . "' " . $column['extra_param'] . "/>";
								}
							}

							$json_row[$column['for_json']] = $column['value0'] . $tmp_lnk . $column['value2'];
						}
						else // for  hidden
						{
							/* if($column['type']== 'number') // for values for delete,edit.
							  { */
							$json_row[$column['name']] = $column['value'];
							/* }
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
							  } */
						}
					}
					$values[] = $json_row;
					$k++;
				}
			}

			$result_data = array('results' => $values);
			$result_data['total_records'] = $this->bo->total_records;
			$result_data['sum_amount'] = number_format($this->bo->sum_amount, 2, ',', ' ');
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function list_sub()
		{
			$paid = phpgw::get_var('paid', 'bool');
			$values = phpgw::get_var('values');
			$voucher_id = phpgw::get_var('voucher_id');

			if (phpgw::get_var('phpgw_return_as') == 'json' && is_array($values) && isset($values))
			{
				if ($this->bo->get_approve_role())
				{
					return $this->bo->update_invoice_sub($values);
				}
				else
				{
					return array('msg' => lang('you are not approved for this task'));
				}
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_list_sub();
			}

			$uicols = array(
				array(
					'key' => 'workorder', 'label' => lang('Workorder'), 'className' => 'center',
					'sortable' => true, 'hidden' => false),
				array(
					'key' => 'external_project_id', 'label' => lang('external project'), 'className' => 'center',
					'sortable' => false, 'hidden' => false),
				array(
					'key' => 'close_order', 'label' => lang('Close order'), 'className' => 'center',
					'sortable' => false, 'hidden' => false),
				array(
					'key' => 'change_tenant', 'label' => lang('Charge tenant'), 'className' => 'center',
					'sortable' => false, 'hidden' => false),
				array(
					'key' => 'invoice_id', 'label' => lang('Invoice Id'), 'className' => 'center',
					'sortable' => false, 'hidden' => false),
				array(
					'key' => 'budget_Account', 'label' => lang('Budget account'), 'className' => 'center',
					'sortable' => true, 'hidden' => false),
				array(
					'key' => 'sum', 'label' => lang('Sum'), 'className' => 'right', 'sortable' => true,
					'sort_field' => 'belop', 'hidden' => false),
				array(
					'key' => 'approved_amount', 'label' => lang('approved amount'), 'className' => 'center',
					'sortable' => false, 'hidden' => false),
				array(
					'key' => 'currency', 'label' => lang('currency'), 'className' => 'center',
					'sortable' => false, 'hidden' => false),
				array(
					'key' => 'dim_A', 'label' => lang('Dim A'), 'className' => 'center', 'sortable' => true,
					'hidden' => false),
				array(
					'key' => 'dim_B', 'label' => lang('Dim B'), 'className' => 'center', 'sortable' => false,
					'hidden' => false),
				array(
					'key' => 'dim_D', 'label' => lang('Dim D'), 'className' => 'center', 'sortable' => false,
					'hidden' => false),
				array(
					'key' => 'Tax_code', 'label' => lang('Tax code'), 'className' => 'center',
					'sortable' => false, 'hidden' => false),
				array(
					'key' => 'Remark', 'label' => lang('Remark'), 'className' => 'center',
					'sortable' => false, 'hidden' => false),
				array(
					'key' => 'external_ref', 'label' => lang('external ref'), 'className' => 'center',
					'sortable' => false, 'hidden' => false),
				array(
					'key' => 'counter', 'hidden' => true),
				array(
					'key' => 'id', 'hidden' => true),
				array(
					'key' => '_external_ref', 'hidden' => true)
			);

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'docid',
						'source' => '_external_ref'
					)
				)
			);

			$tabletools[] = array
				(
				'my_name' => 'save',
				'text' => lang('save'),
				'type' => 'custom',
				'className' => 'save',
				'custom_code' => "onSave();"
			);

			$custom_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$baseurl_invoice = isset($custom_config->config_data['common']['baseurl_invoice']) && $custom_config->config_data['common']['baseurl_invoice'] ? $custom_config->config_data['common']['baseurl_invoice'] : '';
			$lang_picture = lang('picture');

			if ($this->acl_read && $baseurl_invoice)
			{
				$_baseurl_invoice = rtrim($baseurl_invoice, "?{$parameters['parameter'][0]['name']}=");
				$tabletools[] = array
					(
					'my_name' => 'picture',
					'text' => $lang_picture,
					'action' => "{$_baseurl_invoice}?target=_blank",
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_edit)
			{
				$tabletools[] = array
					(
					'my_name' => 'edit',
					'text' => $paid ? lang('view') : lang('edit'),
					'type' => 'custom',
					'custom_code' => ""
					. "
										var selected = JqueryPortico.fnGetSelected(oTable0);

										if (selected.length ==0){
											alert('None selected');
											return false;
										}
										var aData = oTable0.fnGetData( selected[0] );
										var id = aData['id'];
							"
					. "JqueryPortico.openPopup({menuaction:'property.uiinvoice.edit', voucher_id:'{$voucher_id}', user_lid:'{$this->user_lid}', paid:'{$paid}', id: id}, {closeAction:'reload'})"
				);
			}

			$tabletools[] = array
				(
				'my_name' => 'download',
				'download' => self::link(array(
					'menuaction' => 'property.uiinvoice.download_sub',
					'export' => true,
					'allrows' => true
				))
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array(
						'menuaction' => 'property.uiinvoice.list_sub',
						'paid' => $paid,
						'sub' => $this->sub,
						'voucher_id' => $voucher_id,
						'phpgw_return_as' => 'json'
				))),
				'ColumnDefs' => $uicols,
				'data' => json_encode(array()),
				'tabletools' => $tabletools,
				'config' => array(
					array('singleSelect' => true),
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$top_toolbar = array
				(
				array
					(
					'type' => 'button',
					'id' => 'btn_cancel',
					'value' => lang('Cancel'),
					'url' => self::link(array
						(
						'menuaction' => 'property.uiinvoice.index',
						'paid' => $paid
					))
				)
			);

			//Title of Page
			$appname = lang('location');
			if ($paid)
			{
				$function_msg = lang('list paid invoice');
			}
			else
			{
				$function_msg = lang('list invoice');
			}
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'top_toolbar' => $top_toolbar,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
			);

			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('property', 'portico', 'invoice.list_sub.js');
			self::render_template_xsl(array('invoice_list_sub', 'datatable_inline'), array(
				'list_sub' => $data));
		}

		public function query_list_sub()
		{
			$paid = phpgw::get_var('paid', 'bool');
			$voucher_id = phpgw::get_var('voucher_id');
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$order_field = '';

			switch ($columns[$order[0]['column']]['data'])
			{
				case 'workorder':
					$order_field = 'pmwrkord_code';
					break;
				case 'budget_Account':
					$order_field = 'spbudact_code';
					break;
				case 'sum':
					$order_field = 'belop';
					break;
				case 'dim_A':
					$order_field = 'dima';
					break;
				default:
					$order_field = $columns[$order[0]['column']]['data'];
			}

			$params = array
				(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $order_field,
				'sort' => $order[0]['dir'],
				'allrows' => 1,
				'paid' => $paid ? $paid : false,
				'voucher_id' => $voucher_id,
			);

			$content = array();
			if ($voucher_id)
			{
				$this->bo->allrows = true;
				$content = $this->bo->read_invoice_sub($params);
			}

			if (phpgw::get_var('export', 'bool'))
			{
				return $content;
			}

			$sum = 0;

			$dimb_list = $this->bo->select_dimb_list();
			$tax_code_list = $this->bo->tax_code_list();
			$_link_order = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.view_order'));
			$_link_claim = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitenant_claim.check'));

			foreach ($content as &$entry)
			{
				$sum += $entry['amount'];
				$entry['amount'] = number_format($entry['amount'], 2, ',', '');
				$entry['paid'] = $paid;
				$entry['dimb_list'] = $this->bocommon->select_list($entry['dimb'], $dimb_list);
				$entry['tax_code_list'] = $this->bo->tax_code_list($entry['tax_code'], $tax_code_list);
				$entry['link_order'] = $_link_order;
				$entry['link_claim'] = $_link_claim;
			}

			$custom_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$baseurl_invoice = isset($custom_config->config_data['common']['baseurl_invoice']) && $custom_config->config_data['common']['baseurl_invoice'] ? $custom_config->config_data['common']['baseurl_invoice'] : '';
			$lang_picture = lang('picture');

			$uicols = array(
				array(
					'col_name' => 'workorder', 'label' => lang('Workorder'), 'className' => 'centerClasss',
					'sortable' => true, 'sort_field' => 'pmwrkord_code', 'visible' => true),
				array(
					'col_name' => 'external_project_id', 'label' => lang('external project'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'close_order', 'label' => lang('Close order'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'change_tenant', 'label' => lang('Charge tenant'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'invoice_id', 'label' => lang('Invoice Id'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'budget_Account', 'label' => lang('Budget account'), 'className' => 'centerClasss',
					'sortable' => true, 'sort_field' => 'spbudact_code', 'visible' => true),
				array(
					'col_name' => 'sum', 'label' => lang('Sum'), 'className' => 'rightClasss',
					'sortable' => true, 'sort_field' => 'belop', 'visible' => true),
				array(
					'col_name' => 'approved_amount', 'label' => lang('approved amount'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'currency', 'label' => lang('currency'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'dim_A', 'label' => lang('Dim A'), 'className' => 'centerClasss',
					'sortable' => true, 'sort_field' => 'dima', 'visible' => true),
				array(
					'col_name' => 'dim_B', 'label' => lang('Dim B'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'dim_D', 'label' => lang('Dim D'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'Tax_code', 'label' => lang('Tax code'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'Remark', 'label' => lang('Remark'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'external_ref', 'label' => lang('external ref'), 'className' => 'centerClasss',
					'sortable' => false, 'sort_field' => '', 'visible' => true),
				array(
					'col_name' => 'counter', 'visible' => false),
				array(
					'col_name' => 'id', 'visible' => false),
				array(
					'col_name' => 'workorder_id', 'visible' => false),
				array(
					'col_name' => '_external_ref', 'visible' => false)
			);

			$link_sub = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.list_sub',
				'user_lid' => $this->user_lid));

			if ($paid)
			{
				$link_sub.="&paid=true";
			}

			$values = array();
			$j = 0;
			//---- llena DATATABLE-ROWS con los valores del READ
			$workorders = array();
			foreach ($content as $invoices)
			{
				for ($i = 0; $i < count($uicols); $i++)
				{
					$json_row[$uicols[$i]['col_name']] = "";

					if ($i == 0)
					{
						/* $json_row[$uicols[$i]['col_name']] .= " <input name='values[counter][".$j."]' id='values[counter][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$invoices['counter']."'/>";
						  $json_row[$uicols[$i]['col_name']] .= " <input name='values[id][".$j."]' id='values[id][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$invoices['id']."'/>";
						  $json_row[$uicols[$i]['col_name']] .= " <input name='values[workorder_id][".$j."]' id='values[workorder_id][".$j."]'  class='myValuesForPHP'  type='hidden' value='".$invoices['workorder_id']."'/>"; */
						$json_row[$uicols[$i]['col_name']] .= " <a target='_blank' href='" . $invoices['link_order'] . '&order_id=' . $invoices['workorder_id'] . "'>" . $invoices['workorder_id'] . "</a>";
					}
					else if (($i == 1))
					{
						$json_row[$uicols[$i]['col_name']] .= $invoices['external_project_id'];
					}
					else if (($i == 2))
					{
						if (!isset($invoices['workorder_id']) || !$invoices['workorder_id'])
						{
							//nothing
						}
						else if (!$invoices['paid'] && !array_key_exists($invoices['workorder_id'], $workorders))
						{
							$_checked = '';
							if ($invoices['closed'] == 1)
							{
								$_checked = 'checked="checked"';
							}
							else if ($invoices['project_type_id'] == 1 && !$invoices['periodization_id']) // operation projekts
							{
								$_checked = 'checked="checked"';
							}
							else if (!$invoices['continuous'])
							{
								$_checked = 'checked="checked"';
							}

							$json_row[$uicols[$i]['col_name']] .= " <input counter='" . $invoices['counter'] . "' name='values[close_order_orig][{$j}]' id='values[close_order_orig][{$j}]' class='close_order_orig ' type='hidden' value='{$invoices['closed']}'/>";
							$json_row[$uicols[$i]['col_name']] .= " <input counter='" . $invoices['counter'] . "' name='values[close_order_tmp][{$j}]' id='values[close_order_tmp][{$j}]' class='close_order_tmp transfer_idClass' type='checkbox' value='true' {$_checked}/>";
							$json_row[$uicols[$i]['col_name']] .= " <input counter='" . $invoices['counter'] . "' name='values[close_order][{$j}]' id='values[close_order][{$j}]' class='close_order' type='hidden' value=''/>";
						}
						else
						{
							if ($invoices['closed'] == 1)
							{
								$json_row[$uicols[$i]['col_name']] .= "<b>x</b>";
							}
						}
					}
					else if (($i == 3))
					{
						if ($invoices['charge_tenant'] == 1)
						{
							if (!$invoices['claim_issued'])
							{
								$_workorder = execMethod('property.soworkorder.read_single', $invoices['workorder_id']);
								$json_row[$uicols[$i]['col_name']] .= " <a target='_blank' href='" . $invoices['link_claim'] . '&project_id=' . $_workorder['project_id'] . "'>" . lang('Claim') . "</a>";
								unset($_workorder);
							}
							else
							{
								$json_row[$uicols[$i]['col_name']] .= "<b>x</b>";
							}
						}
						else
						{
							$json_row[$uicols[$i]['col_name']] .= "<b>-</b>";
						}
					}
					else if (($i == 4))
					{
						$json_row[$uicols[$i]['col_name']] .= $invoices['invoice_id'];
					}
					else if (($i == 5))
					{
						if ($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']] .= $invoices['budget_account'];
						}
						else
						{
							$json_row[$uicols[$i]['col_name']] .= " <input name='values[budget_account][" . $j . "]' id='values[budget_account][" . $j . "]'  class='budget_account'  type='text' size='7' value='" . $invoices['budget_account'] . "'/>";
						}
					}
					else if (($i == 6))
					{
						$json_row[$uicols[$i]['col_name']] .= $invoices['amount'];
					}
					else if (($i == 7))
					{

						if ($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']] .= $invoices['approved_amount'];
						}
						else
						{
							$json_row[$uicols[$i]['col_name']] .= " <input name='values[approved_amount][" . $j . "]' id='values[approved_amount][" . $j . "]'  class='approved_amount'  type='text' size='7' value='" . $invoices['approved_amount'] . "'/>";
						}
					}
					else if (($i == 8))
					{
						$json_row[$uicols[$i]['col_name']] .= $invoices['currency'];
					}
					else if (($i == 9))
					{
						if ($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']] .= $invoices['dima'];
						}
						else
						{
							$json_row[$uicols[$i]['col_name']] .= " <input name='values[dima][" . $j . "]' id='values[dima][" . $j . "]'  class='dima'  type='text' size='7' value='" . $invoices['dima'] . "'/>";
						}
					}
					else if (($i == 10))
					{
						if ($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']] .= $invoices['dimb'];
						}
						else
						{
							$json_row[$uicols[$i]['col_name']] .= " <select name='values[dimb_tmp][" . $j . "]' id='values[dimb_tmp][" . $j . "]' class='dimb_tmp'><option value=''></option>";

							for ($k = 0; $k < count($invoices['dimb_list']); $k++)
							{
								if (isset($invoices['dimb_list'][$k]['selected']) && $invoices['dimb_list'][$k]['selected'] != "")
								{
									$json_row[$uicols[$i]['col_name']] .= "<option value='" . $invoices['dimb_list'][$k]['id'] . "' selected >" . $invoices['dimb_list'][$k]['name'] . "</option>";
								}
								else
								{
									$json_row[$uicols[$i]['col_name']] .= "<option value='" . $invoices['dimb_list'][$k]['id'] . "'>" . $invoices['dimb_list'][$k]['name'] . "</option>";
								}
							}
							$json_row[$uicols[$i]['col_name']] .="</select>";
							$json_row[$uicols[$i]['col_name']] .= " <input name='values[dimb][" . $j . "]' id='values[dimb][" . $j . "]'  class='dimb'  type='hidden' value=''/>";
						}
					}
					else if (($i == 11))
					{
						if ($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']] .= $invoices['dimd'];
						}
						else
						{
							$json_row[$uicols[$i]['col_name']] .= " <input name='values[dimd][" . $j . "]' id='values[dimd][" . $j . "]'  class='dimd'  type='text' size='4' value='" . $invoices['dimd'] . "'/>";
						}
					}
					else if (($i == 12))
					{
						if ($invoices['paid'] == true)
						{
							$json_row[$uicols[$i]['col_name']] .= $invoices['tax_code'];
						}
						else
						{
							$json_row[$uicols[$i]['col_name']] .= " <select name='values[tax_code_tmp][" . $j . "]' id='values[tax_code_tmp][" . $j . "]'  class='tax_code_tmp'><option value=''></option>";

							for ($k = 0; $k < count($invoices['tax_code_list']); $k++)
							{
								if (isset($invoices['tax_code_list'][$k]['selected']) && $invoices['tax_code_list'][$k]['selected'] != "")
								{
									$json_row[$uicols[$i]['col_name']] .= "<option value='" . $invoices['tax_code_list'][$k]['id'] . "'  selected >" . $invoices['tax_code_list'][$k]['id'] . "</option>";
								}
								else
								{
									$json_row[$uicols[$i]['col_name']] .= "<option value='" . $invoices['tax_code_list'][$k]['id'] . "'>" . $invoices['tax_code_list'][$k]['id'] . "</option>";
								}
							}
							$json_row[$uicols[$i]['col_name']] .="</select>";
							$json_row[$uicols[$i]['col_name']] .= " <input name='values[tax_code][" . $j . "]' id='values[tax_code][" . $j . "]'  class='tax_code'  type='hidden' value=''/>";
						}
					}
					else if (($i == 13))
					{
						if ($invoices['remark'] == true)
						{
							$json_row[$uicols[$i]['col_name']] .= "<a href='#' onClick=\"JqueryPortico.openPopup({'menuaction':'property.uiinvoice.remark', id:'{$invoices['id']}', paid:'{$invoices['paid']}'})\">" . lang('Remark') . "</a>";
						}
						else
						{
							$json_row[$uicols[$i]['col_name']] .= "<b>-</b>";
						}
					}
					else if (($i == 14))
					{
						if (isset($invoices['external_ref']) && $invoices['external_ref'])
						{
							$json_row[$uicols[$i]['col_name']] = " <a href=\"javascript:openwindow('{$baseurl_invoice}{$invoices['external_ref']}','640','800')\" >{$lang_picture}</a>";
						}
						else
						{
							$json_row[$uicols[$i]['col_name']] .= "<b>-</b>";
						}
					}
					else if ($i == 15)
					{
						$json_row[$uicols[$i]['col_name']] = $invoices['counter'];
					}
					else if ($i == 16)
					{
						$json_row[$uicols[$i]['col_name']] = $invoices['id'];
					}
					else if ($i == 17)
					{
						$json_row[$uicols[$i]['col_name']] = $invoices['workorder_id'];
					}
					else if ($i == 18)
					{
						$json_row[$uicols[$i]['col_name']] = $invoices['external_ref'];
					}
				}

				if ($invoices['workorder_id'])
				{
					$workorders[$invoices['workorder_id']] = true;
				}

				$values[] = $json_row;
				$j++;
			}

			$result_data = array('results' => $values);
			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;
			$result_data['sum_amount'] = number_format($sum, 2, ',', ' ');
			$result_data['vendor'] = lang('Vendor') . ": " . $content[0]['vendor'];
			$result_data['voucher_id'] = lang('Voucher Id') . ": " . ($content[0]['voucher_out_id'] ? $content[0]['voucher_out_id'] : $content[0]['voucher_id']);

			return $this->jquery_results($result_data);
		}

		public function query_consume()
		{
			$start_date = urldecode(phpgw::get_var('start_date'));
			$end_date = urldecode(phpgw::get_var('end_date'));
			$vendor_id = phpgw::get_var('vendor_id', 'int');
			$workorder_id = phpgw::get_var('workorder_id', 'int');
			$loc1 = phpgw::get_var('loc1');
			$district_id = phpgw::get_var('district_id', 'int');
			$b_account_class = phpgw::get_var('b_account_class', 'int');
			$b_account = phpgw::get_var('b_account', 'int');
//			$b_account_class = $b_account_class ? $b_account_class : substr($b_account, 0, 2);
			$ecodimb = phpgw::get_var('ecodimb');
			$draw = phpgw::get_var('draw', 'int');

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$actual_date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $dateformat);

			if (!$start_date)
			{
				$start_date = $actual_date;
			}
			if (!$end_date)
			{
				$end_date = $actual_date;
			}

			$content = $this->bo->read_consume($start_date, $end_date, $vendor_id, $loc1, $workorder_id, $b_account_class, $district_id, $ecodimb);

			$sum = 0;
			$sum_refund = 0;
			foreach ($content as &$entry)
			{
				$sum = $sum + $entry['consume'];
				$sum_refund = $sum_refund + $entry['refund'];
				$entry['link_voucher'] = urldecode($GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uiinvoice.index',
						'paid' => true,
						'district_id' => $district_id,
						'b_account_class' => $b_account_class,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'ecodimb' => $ecodimb
						)
				));
				$entry['consume'] = number_format($entry['consume'], 0, ',', ' ');
				$entry['refund'] = number_format($entry['refund'], 0, ',', ' ');
			}

			$result_data = array('results' => $content);
			$result_data['total_records'] = count($content);
			$result_data['draw'] = $draw;
			$result_data['sum'] = number_format($sum, 0, ',', ' ');
			$result_data['sum_refund'] = number_format($sum_refund, 0, ',', ' ');

			return $this->jquery_results($result_data);
		}

		/**
		 * Edit single line within a voucher
		 *
		 */
		public function edit()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$paid = phpgw::get_var('paid', 'bool');
			$id = phpgw::get_var('id', 'int', 'GET', 0);
			$user_lid = phpgw::get_var('user_lid', 'string', 'GET');
			$voucher_id = phpgw::get_var('voucher_id', 'int', 'GET');
			$redirect = false;

			$role_check = array
				(
				'is_janitor' => lang('janitor'),
				'is_supervisor' => lang('supervisor'),
				'is_budget_responsible' => lang('b - responsible')
			);

			$approve = $this->bo->get_approve_role();

			$values = phpgw::get_var('values');

			if (isset($values['save']))
			{
				$values['external_project_id'] = phpgw::get_var('external_project_id', 'string', 'POST');
				if ($GLOBALS['phpgw']->session->is_repost())
				{
					$this->receipt['error'][] = array('msg' => lang('repost'));
				}

				if (!$approve)
				{
					$this->receipt['error'][] = array('msg' => lang('you are not approved for this task'));
				}

				if (!isset($values['process_log']) || !$values['process_log'])
				{
//					$receipt['error'][]=array('msg'=>lang('Missing log message'));
				}

				if ($values['approved_amount'])
				{
					$values['approved_amount'] = str_replace(' ', '', $values['approved_amount']);
					$values['approved_amount'] = str_replace(',', '.', $values['approved_amount']);
					if (isset($values['order_id']) && $values['order_id'] && !execMethod('property.soXport.check_order', $values['order_id']))
					{
						$this->receipt['error'][] = array('msg' => lang('no such order: %1', $values['order_id']));
					}
				}
				else
				{
					unset($values['split_amount']);
					unset($values['split_line']);
				}

				if (isset($values['split_line']) && isset($values['split_amount']) && $values['split_amount'])
				{
					$values['split_amount'] = str_replace(' ', '', $values['split_amount']);
					$values['split_amount'] = str_replace(',', '.', $values['split_amount']);
					if (!is_numeric($values['split_amount']))
					{
						$this->receipt['error'][] = array('msg' => lang('Not a valid amount'));
					}
				}

				if (!$this->receipt['error'])
				{
					$redirect = true;
					$values['id'] = $id;
					$line = $this->bo->update_single_line($values, $paid);
				}
			}

			$line = $this->bo->get_single_line($id, $paid);

//			_debug_array($line);

			$approved_list = array();

			$approved_list[] = array
				(
				'role' => $role_check['is_janitor'],
				'initials' => $line['janitor'] ? $line['janitor'] : '',
				'date' => $line['oppsynsigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($line['oppsynsigndato'])) : ''
			);
			$approved_list[] = array
				(
				'role' => $role_check['is_supervisor'],
				'initials' => $line['supervisor'] ? $line['supervisor'] : '',
				'date' => $line['saksigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($line['saksigndato'])) : ''
			);
			$approved_list[] = array
				(
				'role' => $role_check['is_budget_responsible'],
				'initials' => $line['budget_responsible'] ? $line['budget_responsible'] : '',
				'date' => $line['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($line['budsjettsigndato'])) : ''
			);

			$my_initials = $GLOBALS['phpgw_info']['user']['account_lid'];

			foreach ($approve as &$_approve)
			{
				if ($_approve['id'] == 'is_janitor' && $my_initials == $line['janitor'] && $line['oppsynsigndato'])
				{
					$_approve['selected'] = 1;
					$sign_orig = 'is_janitor';
				}
				else if ($_approve['id'] == 'is_supervisor' && $my_initials == $line['supervisor'] && $line['saksigndato'])
				{
					$_approve['selected'] = 1;
					$sign_orig = 'is_supervisor';
				}
				else if ($_approve['id'] == 'is_budget_responsible' && $my_initials == $line['budget_responsible'] && $line['budsjettsigndato'])
				{
					$_approve['selected'] = 1;
					$sign_orig = 'is_budget_responsible';
				}
			}

			unset($_approve);

			$approve_list = array();
			foreach ($approve as $_approve)
			{
				if ($_approve['id'] == 'is_janitor')
				{
					if (($my_initials == $line['janitor'] && $line['oppsynsigndato']) || !$line['oppsynsigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
				if ($_approve['id'] == 'is_supervisor')
				{
					if (($my_initials == $line['supervisor'] && $line['saksigndato']) || !$line['saksigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
				if ($_approve['id'] == 'is_budget_responsible')
				{
					if (($my_initials == $line['budget_responsible'] && $line['budsjettsigndato']) || !$line['budsjettsigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
			}

			$process_code_list = execMethod('property.bogeneric.get_list', array(
				'type' => 'voucher_process_code',
				'selected' => isset($values['process_code']) ? $values['process_code'] : $line['process_code']));

			$external_project_data = $this->bocommon->initiate_external_project_lookup(array(
				'external_project_id' => $values['external_project_id'] ? $values['external_project_id'] : $line['external_project_id'],
				'external_project_name' => $values['external_project_name']));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
				(
				'redirect' => $redirect ? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.list_sub',
						'user_lid' => $user_lid, 'voucher_id' => $voucher_id, 'paid' => $paid)) : null,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'from_name' => $GLOBALS['phpgw_info']['user']['fullname'],
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.edit',
					'id' => $id, 'user_lid' => $user_lid, 'voucher_id' => $voucher_id)),
				'approve_list' => $approve_list,
				'approved_list' => $approved_list,
				'sign_orig' => $sign_orig,
				'my_initials' => $my_initials,
				'process_code_list' => $process_code_list,
				'external_project_data' => $external_project_data,
				'order_id' => $line['order_id'],
				'value_amount' => $line['amount'],
				'value_approved_amount' => $line['approved_amount'],
				'value_currency' => $line['currency'],
				'value_process_log' => isset($values['process_log']) && $values['process_log'] ? $values['process_log'] : $line['process_log'],
				'paid' => $paid,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			self::render_template_xsl(array('invoice'), array('edit' => $data));
		}

		function remark()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice'));
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$id = phpgw::get_var('id', 'int');
			$paid = phpgw::get_var('paid', 'bool');

			$text = $this->bo->read_remark($id, $paid);

			$html = '';
			if (stripos($text, '<table'))
			{
				$html = 1;
			}

			$data = array
				(
				'remark' => $text,
				'html' => $html
			);

			$appname = lang('invoice');
			$function_msg = lang('remark');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('remark' => $data));
		}

		function consume()
		{
			//-- captura datos de URL
			$start_date = phpgw::get_var('start_date');
			$end_date = phpgw::get_var('end_date');
			$vendor_id = phpgw::get_var('vendor_id', 'int');
			$workorder_id = phpgw::get_var('workorder_id', 'int');
			$loc1 = phpgw::get_var('loc1');
			$district_id = phpgw::get_var('district_id', 'int');
			$b_account_class = phpgw::get_var('b_account_class', 'int');
			$b_account = phpgw::get_var('b_account', 'int');

		//	$b_account_class = $b_account_class ? $b_account_class : substr($b_account, 0, 2);
			$ecodimb = phpgw::get_var('ecodimb');

			//-- ubica focus del menu derecho
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::economy::consume';

			//-- captura datos de URL
			$start_date = urldecode($start_date);
			$end_date = urldecode($end_date);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if (!$start_date)
			{
				//-- actual date
				$start_date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $dateformat);
				$end_date = $start_date;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_consume();
			}

			$appname = lang('consume');
			$function_msg = lang('list consume');

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date');
			phpgwapi_jquery::load_widget('datepicker');

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'date-picker',
								'id' => 'start_date',
								'name' => 'start_date',
								'value' => $start_date,
								'text' => lang('from')
							),
							array(
								'type' => 'date-picker',
								'id' => 'end_date',
								'name' => 'end_date',
								'value' => $end_date,
								'text' => lang('to')
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'property.uiinvoice.consume',
						'start_date' => $start_date,
						'end_date' => $end_date,
						'district_id' => $district_id,
						'ecodimb' => $ecodimb,
						'b_account_class' => $b_account_class,
						'b_account'	=> $b_account,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(),
					'disablePagination' => true
				)
			);

			$filters = $this->_get_Filters_consume();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$vendor_name = "";

			if ($vendor_id)
			{
				$contacts = CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor', false);

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

				$vendor = $contacts->read_single(array('id' => $vendor_id), $lookup);

				if (is_array($vendor))
				{
					foreach ($vendor['attributes'] as $attribute)
					{
						if ($attribute['column_name'] == 'org_name')
						{
							$vendor_name = $attribute['value'];
							break;
						}
					}
				}
			}

			$current_Consult = array();
			for ($i = 0; $i < 3; $i++)
			{
				if ($i == 0 && $workorder_id != "")
				{
					$current_Consult[] = array(lang('Workorder ID'), $workorder_id);
				}
				if ($i == 1 && $vendor_name != "")
				{
					$current_Consult[] = array(lang('Vendor'), $vendor_name);
				}
				if ($i == 2 && $loc1 != "")
				{
					$current_Consult[] = array(lang('property'), $loc1);
				}
			}


			$uicols = array
				(
				'input_type' => array('varchar', 'varchar', 'varchar', 'link', 'varchar', 'varchar'),
				'type' => array('text', 'text', 'text', 'url', 'text', 'text'),
				'col_name' => array('district_id', 'period', 'account_class', 'consume','refund', 'paid'),
				'name' => array('district_id', 'period', 'account_class', 'consume','refund', 'paid'),
				'formatter' => array('', '', '', 'formatLinkIndexInvoice', '', ''),
				'descr' => array(lang('District'), lang('Period'), lang('Budget account'),
					lang('Consume'),lang('refund'), lang('paid')),
				'className' => array('center', 'center', 'center', 'right', 'right', 'center')
			);

			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array
					(
					'key' => $uicols['col_name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false,
					'className' => ($uicols['className'][$k]) ? $uicols['className'][$k] : ''
				);

				if ($uicols['formatter'][$k])
				{
					$params['formatter'] = $uicols['formatter'][$k];
				}

				array_push($data['datatable']['field'], $params);
			}

			$inputFilters = array(
				array('id' => 'workorder_id', 'label' => lang('Workorder ID'), 'type' => 'text'),
				array('id' => '', 'label' => "<a href=\"#\" onClick=\"JqueryPortico.openPopup({menuaction:'property.uilookup.vendor'})\">" . lang('Vendor') . "</a>",
					'type' => 'link'),
				array('id' => 'vendor_id', 'label' => lang('Vendor'), 'type' => 'text'),
				array('id' => 'vendor_name', 'label' => '', 'type' => 'hidden'),
				array('id' => '', 'label' => "<a href=\"#\" onClick=\"JqueryPortico.openPopup({menuaction:'property.uilocation.index', lookup:'1', type_id:'1', lookup_name:'0'})\">" . lang('property') . "</a>",
					'type' => 'link'),
				array('id' => 'loc1', 'label' => lang('property'), 'type' => 'text'),
				array('id' => 'loc1_name', 'label' => '', 'type' => 'hidden')
			);

			$code = "var inputFilters = " . json_encode($inputFilters);

			$code .= <<<JS

				function initCompleteDatatable(oSettings, json, oTable) 
			{
					$('#datatable-container_filter').empty();
					$.each(inputFilters, function(i, val) 
				{
						if (val['type'] == 'text') 
			{
							$('#datatable-container_filter').append('<input type="text" placeholder="'+val['label']+'" id="'+val['id']+'" name="'+val['id']+'" />');
			}
						else if (val['type'] == 'hidden') 
			{
							$('#datatable-container_filter').append('<input type="hidden" id="'+val['id']+'" name="'+val['id']+'" />');
			}
						else {
							$('#datatable-container_filter').append(val['label']);
			}
					});

					var valuesInputFilter = {};
					
					$.each(inputFilters, function(i, val) 
						{
						if (val['type'] == 'text') 
						{
							valuesInputFilter[val['id']] = '';
							$( '#' + val['id']).on( 'keyup change', function () 
			{
								if ( $.trim($(this).val()) != $.trim(valuesInputFilter[val['id']]) ) 
			{
									filterData(val['id'], $(this).val());
									valuesInputFilter[val['id']] = $(this).val();
			}
							});
			}
					});									
				};

				function afterPopupClose() 
			{
					$('#loc1').change();
					$('#vendor_id').change();
				};
JS;

			$GLOBALS['phpgw']->js->add_code('', $code, true);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('property', 'portico', 'invoice.consume.js');
			self::render_template_xsl('datatable_jquery', $data);
		}

		function delete()
		{
			$voucher_id = phpgw::get_var('voucher_id', 'int');

			//cramirez add JsonCod for Delete
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($voucher_id);
				return "voucher_id " . $voucher_id . " " . lang("has been deleted");
			}


			if (!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 8, 'acl_location' => $this->acl_location));
			}


			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'property.uiinvoice.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($voucher_id);
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.delete',
					'voucher_id' => $voucher_id)),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('invoice');
			$function_msg = lang('delete voucher');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function add()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 2, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::add';

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', 'add_receipt');
			if (!$receipt)
			{
				$receipt = array();
			}

			if (isset($receipt['voucher_id']) && $receipt['voucher_id'])
			{
				$link_receipt = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.receipt',
					'voucher_id' => $receipt['voucher_id']));
			}

			$GLOBALS['phpgw']->session->appsession('session_data', 'add_receipt', '');

			$bolocation = CreateObject('property.bolocation');

			$referer = parse_url(phpgw::get_var('HTTP_REFERER', 'string', 'SERVER'));
			parse_str($referer['query']); // produce $menuaction
			if (phpgw::get_var('cancel', 'bool') || $menuaction != 'property.uiinvoice.add')
			{
				$GLOBALS['phpgw']->session->appsession('session_data', 'add_values', '');
			}

			if (!$GLOBALS['phpgw']->session->appsession('session_data', 'add_values') && phpgw::get_var('add_invoice', 'bool'))
			{
				$values['art'] = phpgw::get_var('art', 'int');
				$values['type'] = phpgw::get_var('type');
				$values['dim_b'] = phpgw::get_var('dim_b', 'int');
				$values['invoice_num'] = phpgw::get_var('invoice_num');
				$values['kid_nr'] = phpgw::get_var('kid_nr');
				$values['vendor_id'] = phpgw::get_var('vendor_id', 'int');
				$values['vendor_name'] = phpgw::get_var('vendor_name');
				$values['janitor'] = phpgw::get_var('janitor');
				$values['supervisor'] = phpgw::get_var('supervisor');
				$values['budget_responsible'] = phpgw::get_var('budget_responsible');
				$values['invoice_date'] = urldecode(phpgw::get_var('invoice_date'));
				$values['num_days'] = phpgw::get_var('num_days', 'int');
				$values['payment_date'] = urldecode(phpgw::get_var('payment_date'));
				$values['sday'] = phpgw::get_var('sday', 'int');
				$values['smonth'] = phpgw::get_var('smonth', 'int');
				$values['syear'] = phpgw::get_var('syear', 'int');
				$values['eday'] = phpgw::get_var('eday', 'int');
				$values['emonth'] = phpgw::get_var('emonth', 'int');
				$values['eyear'] = phpgw::get_var('eyear', 'int');
				$values['auto_tax'] = phpgw::get_var('auto_tax', 'bool');
				$values['merknad'] = phpgw::get_var('merknad');
				$values['b_account_id'] = phpgw::get_var('b_account_id', 'int');
				$values['b_account_name'] = phpgw::get_var('b_account_name');
				$values['amount'] = phpgw::get_var('amount'); // float - has to accept string until client side validation is in place.

				if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['currency']))
				{
					$values['amount'] = str_ireplace($GLOBALS['phpgw_info']['user']['preferences']['common']['currency'], '', $values['amount']);
				}

				$values['amount'] = str_replace(' ', '', $values['amount']);
				$values['amount'] = str_replace(',', '.', $values['amount']);

				$values['order_id'] = phpgw::get_var('order_id');

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
				$values = $this->bocommon->collect_locationdata($values, $insert_record);

				$GLOBALS['phpgw']->session->appsession('session_data', 'add_values', $values);
			}
			else
			{
				$values = $GLOBALS['phpgw']->session->appsession('session_data', 'add_values');
				$GLOBALS['phpgw']->session->appsession('session_data', 'add_values', '');
			}

			$location_code = phpgw::get_var('location_code');
			$debug = phpgw::get_var('debug', 'bool');
			$add_invoice = phpgw::get_var('add_invoice', 'bool');


			if ($location_code)
			{
				$values['location_data'] = $bolocation->read_single($location_code, array('tenant_id' => $tenant_id,
					'p_num' => $p_num));
			}

			if ($add_invoice && is_array($values))
			{
				$order = false;
				if ($values['order_id'] && !ctype_digit($values['order_id']))
				{
					$receipt['error'][] = array('msg' => lang('Please enter an integer for order!'));
					unset($values['order_id']);
				}
				else if ($values['order_id'])
				{
					$order = true;
				}

				if (!$values['amount'])
				{
					$receipt['error'][] = array('msg' => lang('Please - enter an amount!'));
				}
				if (!$values['art'])
				{
					$receipt['error'][] = array('msg' => lang('Please - select type invoice!'));
				}
				if (!$values['vendor_id'] && !$order)
				{
					$receipt['error'][] = array('msg' => lang('Please - select Vendor!'));
				}

				if (!$values['type'])
				{
					$receipt['error'][] = array('msg' => lang('Please - select type order!'));
				}

				if (!$values['budget_responsible'] && (!isset($order) || !$order))
				{
					$receipt['error'][] = array('msg' => lang('Please - select budget responsible!'));
				}

				if (!$values['invoice_num'])
				{
					$receipt['error'][] = array('msg' => lang('Please - enter a invoice num!'));
				}

				if (!$order && $values['vendor_id'])
				{
					if (!$this->bo->check_vendor($values['vendor_id']))
					{
						$receipt['error'][] = array('msg' => lang('That Vendor ID is not valid !') . ' : ' . $values['vendor_id']);
					}
				}

				if (!$values['payment_date'] && !$values['num_days'])
				{
					$receipt['error'][] = array('msg' => lang('Please - select either payment date or number of days from invoice date !'));
				}

				//_debug_array($values);
				if (!is_array($receipt['error']))
				{
					if ($values['invoice_date'])
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

					$values['regtid'] = date($GLOBALS['phpgw']->db->datetime_format());


					$_receipt = array();//local errors
					$receipt = $this->bo->add($values, $debug);

					if (!$receipt['message'] && $values['order_id'] && !$receipt[0]['spvend_code'])
					{
						$_receipt['error'][] = array('msg' => lang('vendor is not defined in order %1', $values['order_id']));

						$debug = false;// try again..
						if ($receipt[0]['location_code'])
						{
							//					$values['location_data'] = $bolocation->read_single($receipt['location_code'],array('tenant_id'=>$tenant_id,'p_num'=>$p_num));
						}
					}

					if ($debug)
					{
						$this->debug($receipt);
						return;
					}
					if (!$_receipt['error']) // all ok
					{
						unset($values);
						$GLOBALS['phpgw']->session->appsession('session_data', 'add_receipt', $receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiinvoice.add'));
					}
				}
				else
				{
					if ($values['location'])
					{
						$location_code = implode("-", $values['location']);
						$values['location_data'] = $bolocation->read_single($location_code, isset($values['extra']) ? $values['extra'] : '');
					}
					$GLOBALS['phpgw']->session->appsession('session_data', 'add_values', '');
				}
			}

			if (isset($receipt['voucher_id']) && $receipt['voucher_id'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array
					(
					'menuaction' => 'property.uiinvoice.list_sub',
					'user_lid' => $GLOBALS['phpgw_info']['user']['account_lid'],
					'voucher_id' => $receipt['voucher_id']
					)
				);
			}

			$location_data = $bolocation->initiate_ui_location(array
				(
				'values' => isset($values['location_data']) ? $values['location_data'] : '',
				'type_id' => -1, // calculated from location_types
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => false,
				'lookup_type' => 'form',
				'lookup_entity' => false, //$this->bocommon->get_lookup_entity('project'),
				'entity_data' => false //$values['p']
				)
			);
			$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array
				(
				'b_account_id' => isset($values['b_account_id']) ? $values['b_account_id'] : '',
				'b_account_name' => isset($values['b_account_name']) ? $values['b_account_name'] : '')
			);

			$link_data = array
				(
				'menuaction' => 'property.uiinvoice.add',
				'debug' => true
			);

			if ($_receipt)
			{
				$receipt = array_merge($receipt, $_receipt);
			}
			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';

			$GLOBALS['phpgw']->jqcal->add_listener('invoice_date');
			$GLOBALS['phpgw']->jqcal->add_listener('payment_date');

			$data = array
				(
				'menu' => $this->bocommon->get_menu(),
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'cancel_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.index')),
				'lang_cancel' => lang('Cancel'),
				'lang_cancel_statustext' => lang('cancel'),
				'action_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property' . '.uiinvoice.add')),
				'tsvfilename' => '',
				'lang_add' => lang('add'),
				'lang_add_statustext' => lang('click this button to add a invoice'),
				'lang_invoice_date' => lang('invoice date'),
				'lang_payment_date' => lang('Payment date'),
				'lang_no_of_days' => lang('Days'),
				'lang_invoice_number' => lang('Invoice Number'),
				'lang_invoice_num_statustext' => lang('Enter Invoice Number'),
				'lang_select' => lang('Select per button !'),
				'lang_kidnr' => lang('KID nr'),
				'lang_kid_nr_statustext' => lang('Enter Kid nr'),
				'lang_vendor' => lang('Vendor'),
				'addressbook_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uilookup.vendor')),
				'lang_invoice_date_statustext' => lang('Enter the invoice date'),
				'lang_num_days_statustext' => lang('Enter the payment date or the payment delay'),
				'lang_payment_date_statustext' => lang('Enter the payment date or the payment delay'),
				'lang_vendor_statustext' => lang('Select the vendor by clicking the button'),
				'lang_vendor_name_statustext' => lang('Select the vendor by clicking the button'),
				'lang_select_vendor_statustext' => lang('Select the vendor by clicking this button'),
				'value_invoice_date' => isset($values['invoice_date']) ? $values['invoice_date'] : '',
				'value_payment_date' => isset($values['payment_date']) ? $values['payment_date'] : '',
				'value_belop' => isset($values['belop']) ? $values['belop'] : '',
				'value_vendor_id' => isset($values['vendor_id']) ? $values['vendor_id'] : '',
				'value_vendor_name' => isset($values['vendor_name']) ? $values['vendor_name'] : '',
				'value_kid_nr' => isset($values['kid_nr']) ? $values['kid_nr'] : '',
				'value_dim_b' => isset($values['dim_b']) ? $values['dim_b'] : '',
				'value_invoice_num' => isset($values['invoice_num']) ? $values['invoice_num'] : '',
				'value_merknad' => isset($values['merknad']) ? $values['merknad'] : '',
				'value_num_days' => isset($values['num_days']) ? $values['num_days'] : '',
				'value_amount' => isset($values['amount']) ? $values['amount'] : '',
				'value_order_id' => isset($values['order_id']) ? $values['order_id'] : '',
				'lang_auto_tax' => lang('Auto TAX'),
				'lang_auto_tax_statustext' => lang('Set tax'),
				'lang_amount' => lang('Amount'),
				'lang_amount_statustext' => lang('Amount of the invoice'),
				'lang_order' => lang('Order ID'),
				'lang_order_statustext' => lang('Order # that initiated the invoice'),
				'lang_art' => lang('Art'),
				'art_list' => $this->bo->get_lisfm_ecoart(isset($values['art']) ? $values['art'] : ''),
				'select_art' => 'art',
				'lang_select_art' => lang('Select Invoice Type'),
				'lang_art_statustext' => lang('You have to select type of invoice'),
				'lang_type' => lang('Type invoice II'),
				'type_list' => $this->bo->get_type_list(isset($values['type']) ? $values['type'] : ''),
				'select_type' => 'type',
				'lang_no_type' => lang('No type'),
				'lang_type_statustext' => lang('Select the type  invoice. To do not use type -  select NO TYPE'),
				'lang_dimb' => lang('Dim B'),
				'dimb_list' => $this->bo->select_dimb_list(isset($values['dim_b']) ? $values['dim_b'] : ''),
				'select_dimb' => 'dim_b',
				'lang_no_dimb' => lang('No Dim B'),
				'lang_dimb_statustext' => lang('Select the Dim B for this invoice. To do not use Dim B -  select NO DIM B'),
				'lang_janitor' => lang('Janitor'),
				'janitor_list' => $this->bocommon->get_user_list_right(32, isset($values['janitor']) ? $values['janitor'] : '', '.invoice'),
				'select_janitor' => 'janitor',
				'lang_no_janitor' => lang('No janitor'),
				'lang_janitor_statustext' => lang('Select the janitor responsible for this invoice. To do not use janitor -  select NO JANITOR'),
				'lang_supervisor' => lang('Supervisor'),
				'supervisor_list' => $this->bocommon->get_user_list_right(64, isset($values['supervisor']) ? $values['supervisor'] : '', '.invoice'),
				'select_supervisor' => 'supervisor',
				'lang_no_supervisor' => lang('No supervisor'),
				'lang_supervisor_statustext' => lang('Select the supervisor responsible for this invoice. To do not use supervisor -  select NO SUPERVISOR'),
				'lang_budget_responsible' => lang('B - responsible'),
				'budget_responsible_list' => $this->bocommon->get_user_list_right(128, isset($values['budget_responsible']) ? $values['budget_responsible'] : '', '.invoice'),
				'select_budget_responsible' => 'budget_responsible',
				'lang_select_budget_responsible' => lang('Select B-Responsible'),
				'lang_budget_responsible_statustext' => lang('You have to select a budget responsible for this invoice in order to add the invoice'),
				'lang_merknad' => lang('Descr'),
				'lang_merknad_statustext' => lang('Descr'),
				'location_data' => $location_data,
				'b_account_data' => $b_account_data,
				'link_receipt' => isset($link_receipt) ? $link_receipt : '',
				'lang_receipt' => lang('receipt'),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			//_debug_array($data);

			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice'));

			$appname = lang('Invoice');
			$function_msg = lang('Add invoice');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('add' => $data));
		}

		function receipt()
		{

			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$voucher_id = phpgw::get_var('voucher_id', 'int');

			if ($voucher_id)
			{
				$values = $this->bo->read_single_voucher($voucher_id);
			}
			//	_debug_array($values);
			$pdf = CreateObject('phpgwapi.pdf');

			if (isSet($values) AND is_array($values))
			{

				$contacts = CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor', false);

				if ($values[0]['vendor_id'])
				{
					$custom = createObject('property.custom_fields');
					$vendor_data['attributes'] = $custom->find('property', '.vendor', 0, '', 'ASC', 'attrib_sort', true, true);
					$vendor_data = $contacts->read_single(array('id' => $values[0]['vendor_id']), $vendor_data);
					if (is_array($vendor_data))
					{
						foreach ($vendor_data['attributes'] as $attribute)
						{
							if ($attribute['name'] == 'org_name')
							{
								$value_vendor_name = $attribute['value'];
								break;
							}
						}
					}
				}

				$sum = 0;
				foreach ($values as $entry)
				{
					$content[] = array
						(
						lang('order') => $entry['order'],
						lang('invoice id') => $entry['invoice_id'],
						lang('budget account') => $entry['b_account_id'],
						lang('object') => $entry['dim_a'],
						lang('dim_d') => $entry['dim_d'],
						lang('Tax code') => $entry['tax'],
						'Tjeneste' => $entry['kostra_id'],
						lang('amount') => number_format($entry['amount'], 2, ',', ' ')
					);
					$sum = $sum + $entry['amount'];
				}
			}

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date('', $dateformat);

			// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
			//error_reporting(7);
			//error_reporting(E_ALL);
			set_time_limit(1800);
			$pdf->ezSetMargins(50, 70, 50, 50);
			$pdf->selectFont('Helvetica');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();
			$pdf->setStrokeColor(0, 0, 0, 1);
			$pdf->line(20, 40, 578, 40);
			$pdf->line(20, 822, 578, 822);
			$pdf->addText(50, 823, 6, lang('voucher'));
			$pdf->addText(50, 34, 6, 'BBB');
			$pdf->addText(300, 34, 6, $date);

			$pdf->setColor(1, 0, 0);
			$pdf->addText(500, 750, 40, 'E', -10);
			$pdf->ellipse(512, 768, 30);
			$pdf->setColor(1, 0, 0);


			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all, 'all');
			$pdf->ezStartPageNumbers(500, 28, 10, 'right', '{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}', 1);
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
				'text' => lang('voucher id'),
				'value' => $voucher_id
			);
			$content_heading[] = array
				(
				'text' => lang('Type'),
				'value' => $values[0]['art']
			);
			$content_heading[] = array
				(
				'text' => lang('vendor'),
				'value' => $values[0]['vendor_id'] . ' ' . $value_vendor_name
			);
			$content_heading[] = array
				(
				'text' => lang('invoice date'),
				'value' => $GLOBALS['phpgw']->common->show_date(strtotime($values[0]['invoice_date']), $dateformat)
			);
			$content_heading[] = array
				(
				'text' => lang('Payment date'),
				'value' => $GLOBALS['phpgw']->common->show_date(strtotime($values[0]['payment_date']), $dateformat)
			);
			$content_heading[] = array
				(
				'text' => lang('Janitor'),
				'value' => $values[0]['janitor']
			);
			$content_heading[] = array
				(
				'text' => lang('Supervisor'),
				'value' => $values[0]['supervisor']
			);
			$content_heading[] = array
				(
				'text' => lang('Budget Responsible'),
				'value' => $values[0]['budget_responsible']
			);

			if ($values[0]['project_id'])
			{
				$content_heading[] = array
					(
					'text' => lang('Project id'),
					'value' => $values[0]['project_id']
				);
			}

			$content_heading[] = array
				(
				'text' => lang('Sum'),
				'value' => number_format($sum, 2, ',', ' ')
			);

			$pdf->ezTable($content_heading, '', '', array('xPos' => 70, 'xOrientation' => 'right',
				'width' => 400, 0, 'shaded' => 0, 'fontSize' => 8, 'gridlines' => 0,
				'titleFontSize' => 12, 'outerLineThickness' => 0, 'showHeadings' => 0
				, 'cols' => array('text' => array('justification' => 'left', 'width' => 100),
					'value' => array('justification' => 'left', 'width' => 200))
				)
			);

			$pdf->ezSetDy(-20);

			$table_header = array(
				lang('order') => array('justification' => 'right', 'width' => 60),
				lang('invoice id') => array('justification' => 'right', 'width' => 60),
				lang('budget account') => array('justification' => 'right', 'width' => 80),
				lang('object') => array('justification' => 'right', 'width' => 70),
				lang('dim_d') => array('justification' => 'right', 'width' => 50),
				lang('Tax code') => array('justification' => 'right', 'width' => 50),
				'Tjeneste' => array('justification' => 'right', 'width' => 50),
				lang('amount') => array('justification' => 'right', 'width' => 80),
			);


			if (is_array($values))
			{
				$pdf->ezTable($content, '', '', array('xPos' => 70, 'xOrientation' => 'right',
					'width' => 500, 0, 'shaded' => 0, 'fontSize' => 8, 'gridlines' => EZ_GRIDLINE_ALL,
					'titleFontSize' => 12, 'outerLineThickness' => 2
					, 'cols' => $table_header
					)
				);
			}

			$document = $pdf->ezOutput();
			$pdf->print_pdf($document, 'receipt_' . $voucher_id);
		}

		function debug( $values )
		{
			//			_debug_array($values);
			$GLOBALS['phpgw_info']['flags'][noheader] = true;
			$GLOBALS['phpgw_info']['flags'][nofooter] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice', 'table_header'));

			$link_data_add = array
				(
				'menuaction' => 'property.uiinvoice.add',
				'add_invoice' => true
			);

			$link_data_cancel = array
				(
				'menuaction' => 'property.uiinvoice.add'
			);

			$post_data = array
				(
				'location_code' => $values[0]['location_code'],
				'art' => $values[0]['art'],
				'type' => $values[0]['type'],
				'dim_b' => $values[0]['dim_b'],
				'invoice_num' => $values[0]['invoice_num'],
				'kid_nr' => $values[0]['kid_nr'],
				'vendor_id' => $values[0]['spvend_code'],
				'vendor_name' => $values[0]['vendor_name'],
				'janitor' => $values[0]['janitor'],
				'supervisor' => $values[0]['supervisor'],
				'budget_responsible' => $values[0]['budget_responsible'],
				'invoice_date' => urlencode($values[0]['invoice_date']),
				'num_days' => $values[0]['num_days'],
				'payment_date' => urlencode($values[0]['payment_date']),
				'sday' => $values[0]['sday'],
				'smonth' => $values[0]['smonth'],
				'syear' => $values[0]['syear'],
				'eday' => $values[0]['eday'],
				'emonth' => $values[0]['emonth'],
				'eyear' => $values[0]['eyear'],
				'auto_tax' => $values[0]['auto_tax'],
				'merknad' => $values[0]['merknad'],
				'b_account_id' => $values[0]['spbudact_code'],
				'b_account_name' => $values[0]['b_account_name'],
				'amount' => $values[0]['amount'],
				'order_id' => $values[0]['order_id'],
			);

			$link_data_add = $link_data_add + $post_data;
			$link_data_cancel = $link_data_cancel + $post_data;

			$table_add[] = array
				(
				'lang_add' => lang('Add'),
				'lang_add_statustext' => lang('Add this invoice'),
				'add_action' => $GLOBALS['phpgw']->link('/index.php', $link_data_add),
				'lang_cancel' => lang('cancel'),
				'lang_cancel_statustext' => lang('Do not add this invoice'),
				'cancel_action' => $GLOBALS['phpgw']->link('/index.php', $link_data_cancel)
			);


			$import = array
				(
				'Bestilling' => 'pmwrkord_code',
				'Fakt. Nr' => 'fakturanr',
				'Konto' => 'spbudact_code',
				'Objekt' => 'dima',
				'Fag/Timer/Matr' => 'dimd',
				'MVA' => 'mvakode',
				'Tjeneste' => 'kostra_id',
				'Beløp [kr]' => 'belop'
			);

			$header = array('Bestilling', 'Fakt. Nr', 'Konto', 'Objekt', 'Fag/Timer/Matr',
				'MVA', 'Tjeneste', 'Beløp [kr]');

			for ($i = 0; $i < count($header); $i++)
			{
				$table_header[$i]['header'] = $header[$i];
				$table_header[$i]['width'] = '5%';
				$table_header[$i]['align'] = 'center';
			}
			//	$sum=0;

			$import_count = count($import);
			$values_count = count($values);
			for ($i = 0; $i < $values_count; $i++)
			{
				for ($k = 0; $k < $import_count; $k++)
				{
					$content[$i]['row'][$k]['value'] = $values[$i][$import[$header[$k]]];
					if ($import[$header[$k]] == 'belop')
					{
						$content[$i]['row'][$k]['align'] = 'right';
						//		$sum=$sum+$values[$i][$import[$header[$k]]];
						$content[$i]['row'][$k]['value'] = number_format($values[$i][$import[$header[$k]]], 2, ',', '');
					}
				}
			}

			$tabs = array();
			$tabs['confirm'] = array('label' => lang('confirm'), 'link' => '#confirm');
			$active_tab = 'confirm';

			$data = array
				(
				'artid' => $values[0]['artid'],
				'lang_type' => lang('Type'),
				'project_id' => $values[0]['project_id'],
				'lang_project_id' => lang('Project id'),
				'lang_vendor' => lang('Vendor'),
				'vendor_name' => $values[0]['vendor_name'],
				'spvend_code' => $values[0]['spvend_code'],
				'lang_fakturadato' => lang('invoice date'),
				'fakturadato' => $values[0]['fakturadato'],
				'lang_forfallsdato' => lang('Payment date'),
				'forfallsdato' => $values[0]['forfallsdato'],
				'lang_janitor' => lang('Janitor'),
				'oppsynsmannid' => $values[0]['oppsynsmannid'],
				'lang_supervisor' => lang('Supervisor'),
				'saksbehandlerid' => $values[0]['saksbehandlerid'],
				'lang_budget_responsible' => lang('Budget Responsible'),
				'budsjettansvarligid' => $values[0]['budsjettansvarligid'],
				'lang_sum' => lang('Sum'),
				'sum' => number_format($values[0]['amount'], 2, ',', ''),
				'table_header' => $table_header,
				'values' => $content,
				'table_add' => $table_add,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			//_debug_array($data);
			$appname = lang('Invoice');
			$function_msg = lang('Add invoice: Debug');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('debug' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view_order()
		{
			$order_id = phpgw::get_var('order_id'); // could be bigint
			$soXport = CreateObject('property.soXport');

			$nonavbar = phpgw::get_var('nonavbar', 'bool');
			$lean = phpgw::get_var('lean', 'bool');

			$order_type = $soXport->check_order($order_id);
			switch ($order_type)
			{
				case 'workorder':
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiworkorder.edit',
						'id' => $order_id, 'tab' => 'budget', 'nonavbar' => $nonavbar, 'lean' => $lean));
					break;
				case 's_agreement':
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uis_agreement.view',
						'id' => $order_id, 'nonavbar' => $nonavbar, 'lean' => $lean));
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
			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$type = phpgw::get_var('type', 'string', 'GET', 'deposition');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::economy::{$type}";

			$values = phpgw::get_var('values');

			$receipt = array();
			if ($values)
			{
				//		_debug_array($values);die();

				if (isset($values['export_reconciliation']) && $values['export_reconciliation'])
				{
					if (!isset($values['periods']))
					{
						$type = 'reconciliation';
						$receipt['error'][] = array('msg' => lang('missing values'));
					}
					else
					{
						$this->bo->export_historical_transactions_at_periods($values['periods']);
					}
				}
				else if (isset($values['export_deposition']) && $values['export_deposition'])
				{
					if (!isset($values['deposition']))
					{
						$type = 'deposition';
						$receipt['error'][] = array('msg' => lang('nothing to do'));
					}
					else
					{
						$this->bo->export_deposition();
					}
				}
				return;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$tabs = array();
			$tabs['deposition'] = array('label' => lang('deposition'), 'link' => '#deposition');
			$tabs['reconciliation'] = array('label' => lang('reconciliation'), 'link' => '#reconciliation');
			$active_tab = $type;

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.reporting')),
				'accounting_periods' => array('options' => $this->bo->get_historical_accounting_periods()),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			$function_msg = lang('reporting');
			$appname = lang('invoice');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl(array('invoice_reporting'), $data);
		}

		/**
		 * forward voucher to other persons
		 *
		 */
		public function forward()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$user_lid = phpgw::get_var('user_lid', 'string', 'GET', 'all');
			$voucher_id = phpgw::get_var('voucher_id', 'int', 'GET');
			$redirect = false;

			$role_check = array
				(
				'is_janitor' => lang('janitor'),
				'is_supervisor' => lang('supervisor'),
				'is_budget_responsible' => lang('b - responsible')
			);

			$approve = $this->bo->get_approve_role();

			$values = phpgw::get_var('values');

			$receipt = array();
			if (isset($values['save']))
			{
				if ($GLOBALS['phpgw']->session->is_repost())
				{
					$this->receipt['error'][] = array('msg' => lang('repost'));
				}

				if (!$approve)
				{
					$this->receipt['error'][] = array('msg' => lang('you are not approved for this task'));
				}

				if (!$this->receipt['error'])
				{
					$values['voucher_id'] = $voucher_id;
					$this->receipt = $this->bo->forward($values);
					if (!$this->receipt['error'])
					{
						execMethod('property.soworkorder.close_orders', phpgw::get_var('orders'));
						$redirect = true;
					}
				}
			}

			$voucher = $this->bo->read_single_voucher($voucher_id);
			$orders = array();
			$_orders = array();
			foreach ($voucher as $line)
			{
				if ($line['order_id'])
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
				'role' => $role_check['is_janitor'],
				'role_sign' => 'oppsynsmannid',
				'initials' => $line['janitor'] ? $line['janitor'] : '',
				'date' => $line['oppsynsigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($line['oppsynsigndato'])) : '',
				'user_list' => !$line['oppsynsigndato'] ? array('options_user' => $this->bocommon->get_user_list_right(32, isset($line['janitor']) ? $line['janitor'] : '', '.invoice')) : ''
			);
			$approved_list[] = array
				(
				'role' => $role_check['is_supervisor'],
				'role_sign' => 'saksbehandlerid',
				'initials' => $line['supervisor'] ? $line['supervisor'] : '',
				'date' => $line['saksigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($line['saksigndato'])) : '',
				'user_list' => !$line['saksigndato'] ? array('options_user' => $this->bocommon->get_user_list_right(64, isset($line['supervisor']) ? $line['supervisor'] : '', '.invoice')) : ''
			);
			$approved_list[] = array
				(
				'role' => $role_check['is_budget_responsible'],
				'role_sign' => 'budsjettansvarligid',
				'initials' => $line['budget_responsible'] ? $line['budget_responsible'] : '',
				'date' => $line['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($line['budsjettsigndato'])) : '',
				'user_list' => !$line['budsjettsigndato'] ? array('options_user' => $this->bocommon->get_user_list_right(128, isset($line['budget_responsible']) ? $line['budget_responsible'] : '', '.invoice')) : ''
			);

			$my_initials = $GLOBALS['phpgw_info']['user']['account_lid'];

			foreach ($approve as &$_approve)
			{
				if ($_approve['id'] == 'is_janitor' && $my_initials == $line['janitor'] && $line['oppsynsigndato'])
				{
					$_approve['selected'] = 1;
					$sign_orig = 'is_janitor';
				}
				else if ($_approve['id'] == 'is_supervisor' && $my_initials == $line['supervisor'] && $line['saksigndato'])
				{
					$_approve['selected'] = 1;
					$sign_orig = 'is_supervisor';
				}
				else if ($_approve['id'] == 'is_budget_responsible' && $my_initials == $line['budget_responsible'] && $line['budsjettsigndato'])
				{
					$_approve['selected'] = 1;
					$sign_orig = 'is_budget_responsible';
				}
			}

			unset($_approve);

			$approve_list = array();
			foreach ($approve as $_approve)
			{
				if ($_approve['id'] == 'is_janitor')
				{
					if (($my_initials == $line['janitor'] && $line['oppsynsigndato']) || !$line['oppsynsigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
				if ($_approve['id'] == 'is_supervisor')
				{
					if (($my_initials == $line['supervisor'] && $line['saksigndato']) || !$line['saksigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
				if ($_approve['id'] == 'is_budget_responsible')
				{
					if (($my_initials == $line['budget_responsible'] && $line['budsjettsigndato']) || !$line['budsjettsigndato'])
					{
						$approve_list[] = $_approve;
					}
				}
			}

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$tabs = array();
			$tabs['record_detail'] = array('label' => lang('record detail'), 'link' => '#record_detail');
			$active_tab = 'record_detail';

			$data = array
				(
				'redirect' => $redirect ? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.index',
						'user_lid' => $user_lid)) : null,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'from_name' => $GLOBALS['phpgw_info']['user']['fullname'],
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.forward',
					'user_lid' => $user_lid, 'voucher_id' => $voucher_id)),
				'approve_list' => $approve_list,
				'approved_list' => $approved_list,
				'sign_orig' => $sign_orig,
				'my_initials' => $my_initials,
				'external_project_data' => $external_project_data,
				'orders' => $orders,
				'value_amount' => $line['amount'],
				'value_currency' => $line['currency'],
				'value_process_log' => isset($values['process_log']) && $values['process_log'] ? $values['process_log'] : $line['process_log'],
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			self::render_template_xsl(array('invoice'), array('forward' => $data));
		}
	}