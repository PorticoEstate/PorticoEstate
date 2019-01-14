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
	 * @subpackage project
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class property_uitenant_claim extends phpgwapi_uicommon_jquery
	{

		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $public_functions = array
			(
			'query' => true,
			'index' => true,
			'check' => true,
			'view' => true,
			'edit' => true,
			'delete' => true,
			'view_file' => true,
			'close'		=> true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::economy::claim';

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo = CreateObject('property.botenant_claim', true);
			$this->bocommon = CreateObject('property.bocommon');
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.tenant_claim';

			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage = $this->acl->check($this->acl_location, 16, 'property');

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->user_id = $this->bo->user_id;
			$this->status = $this->bo->status;
			$this->cat_id = $this->bo->cat_id;
			$this->allrows = $this->bo->allrows;
			$this->project_id = $this->bo->project_id;
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start' => $this->start,
				'query' => $this->query,
				'sort' => $this->sort,
				'order' => $this->order,
				'user_id' => $this->user_id,
				'district_id' => $this->district_id,
				'status' => $this->status,
				'cat_id' => $this->cat_id,
				'allrows' => $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function view_file()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => PHPGW_ACL_READ, 'acl_location' => $this->acl_location));
			}
			ExecMethod('property.bofiles.get_file', phpgw::get_var('file_id', 'int'));
		}

		private function _get_filter_tenant()
		{
			$values_combo_box = array();
			$combos = array();

			$values_combo_box[0] = $this->bocommon->select_category_list(array('format' => 'filter',
				'selected' => $this->cat_id, 'type' => 'tenant_claim', 'order' => 'descr'));
			array_unshift($values_combo_box[0], array('id' => '', 'name' => lang('no category')));
			$combos[] = array('type' => 'filter',
				'name' => 'cat_id',
				'text' => lang('no category'),
				'list' => $values_combo_box[0]
			);

			$values_combo_box[1] = $this->bocommon->select_district_list('filter', $this->district_id);
			array_unshift($values_combo_box[1], array('id' => '', 'name' => lang('no district')));
			$combos[] = array('type' => 'filter',
				'name' => 'district_id',
				'text' => lang('no district'),
				'list' => $values_combo_box[1]
			);

			$values_combo_box[2] = $this->bo->get_status_list(array('format' => 'filter',
				'selected' => $this->status, 'default' => 'open'));
			array_unshift($values_combo_box[2], array('id' => '', 'name' => lang('open')));
			$combos[] = array('type' => 'filter',
				'name' => 'status',
				'text' => lang('open'),
				'list' => $values_combo_box[2]
			);

			$values_combo_box[3] = $this->bocommon->get_user_list_right2('filter', 2, $this->filter, $this->acl_location);
			array_unshift($values_combo_box[3], array('id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'name' => lang('mine tickets')));
			array_unshift($values_combo_box[3], array('id' => '', 'name' => lang('no user')));
			$combos[] = array('type' => 'filter',
				'name' => 'user_id',
				'text' => lang('User'),
				'list' => $values_combo_box[3]
			);

			return $combos;
		}

		function index( $project_id = '' )
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query(array('project_id' => phpgw::get_var('project_id')));
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname = lang('Tenant claim');
			$function_msg = lang('list claim');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$location1_info = CreateObject('property.soadmin_location')->read_single(1);

			$data = array(
				'datatable_name' => $appname,
				'form' => array(
					'toolbar' => array(
						'item' => array(
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'property.uitenant_claim.index',
						'project_id' => $this->project_id,
						'phpgw_return_as' => 'json'
					)),
					'new_item' => self::link(array(
						'menuaction' => 'property.uiproject.index',
						'lookup' => '1',
						'from' => 'tenant_claim'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(
						array(
							'key' => 'claim_id',
							'label' => lang('claim_id'),
							'sortable' => TRUE
						),
						array(
							'key' => 'district_id',
							'label' => lang('district_id'),
							'sortable' => TRUE
						),
						array(
							'key' => 'location_code',
							'label' => lang('location'),
							'sortable' => FALSE
						),
						array(
							'key' => 'loc1_name',
							'label' => $location1_info['name'],
							'sortable' => FALSE
						),
						array(
							'key' => 'address',
							'label' => lang('address'),
							'sortable' => FALSE
						),
						array(
							'key' => 'loc_category',
							'label' => lang('category'),
							'sortable' => FALSE
						),
						array(
							'key' => 'project_id',
							'label' => lang('Project'),
							'sortable' => TRUE
						),
						array(
							'key' => 'name',
							'label' => lang('name'),
							'sortable' => TRUE
						),
						array(
							'key' => 'entry_date',
							'label' => lang('entry_date'),
							'sortable' => TRUE
						),
						array(
							'key' => 'user',
							'label' => lang('user'),
							'sortable' => FALSE
						),
						array(
							'key' => 'claim_category',
							'label' => lang('category'),
							'sortable' => FALSE
						),
						array(
							'key' => 'status',
							'label' => lang('Status'),
							'sortable' => FALSE
						),
						array(
							'key' => 'amount',
							'label' => lang('amount'),
							'sortable' => true
						),
						array(
							'key' => 'remark',
							'label' => lang('remark'),
							'sortable' => false
						),
						array(
							'key' => 'tenant_id',
							'label' => lang('tenant_id'),
							'sortable' => FALSE,
							'hidden' => true
						)
					)
				)
			);

			$filters = $this->_get_filter_tenant();
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$parameters = array
			(
				'parameter' => array
				(
					array
					(
						'name' => 'claim_id',
						'source' => 'claim_id'
					),
				)
			);

			if ($this->acl_read)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'view',
					'statustext' => lang('view the claim'),
					'text' => lang('view'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uitenant_claim.view'
						)
					),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_edit)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'edit',
					'statustext' => lang('edit the claim'),
					'text' => lang('edit'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uitenant_claim.edit'
						)
					),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_edit)
			{
				$lang_close = lang('close');
				$data['datatable']['actions'][] = array
				(
					'my_name' => 'status',
					'statustext' => lang('Close the claim'),
					'text' => $lang_close,
					'confirm_msg' => lang('do you really want to change the status to %1', $lang_close),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
							'menuaction' => 'property.uitenant_claim.close',
							'delete' => 'dummy'// FIXME to trigger the json in property.js.
						)
					),
					'parameters' => json_encode($parameters)
				);
			}

			$jasper = array();//execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

			foreach ($jasper as $report)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'edit',
					'text' => lang('open JasperReport %1 in new window', $report['title']),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uijasper.view',
						'jasper_id' => $report['id']
					)),
					'target' => '_blank',
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_delete)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'delete',
					'statustext' => lang('delete the claim'),
					'text' => lang('delete'),
					'confirm_msg' => lang('do you really want to delete this entry'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uitenant_claim.delete'
						)
					),
					'parameters' => json_encode($parameters)
				);
			}

			unset($parameters);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query( $data = array() )
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$district_id = phpgw::get_var('district_id', 'int');
			$columns = phpgw::get_var('columns');
			$project_id = isset($data['project_id']) && $data['project_id'] ? $data['project_id'] : phpgw::get_var('project_id');
			$export = phpgw::get_var('export', 'bool');

			$params = array(
				'start' => $this->start,
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'sort' => $order[0]['dir'],
				'order' => $columns[$order[0]['column']]['data'],
				'user_id' => $this->user_id,
				'status' => $this->status,
				'cat_id' => $this->cat_id,
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'project_id' => $project_id,
				'district_id' => $district_id
			);

			$result_objects = array();
			$result_count = 0;
			//
			$values = $this->bo->read($params);
			if ($export)
			{
				return $values;
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			$link_data = array
				(
				'menuaction' => 'property.uigeneric.edit',
				'appname' => $this->appname,
				'type' => $this->type,
				'type_id' => $this->type_id
			);

			array_walk($result_data['results'], array($this, '_add_links'), $link_data);

			return $this->jquery_results($result_data);
		}

		function check()
		{
			$project_id = phpgw::get_var('project_id', 'int');

			$claim = $this->bo->check_claim_project($project_id);
			$total_records = $this->bo->total_records;

			if ($total_records > 1)
			{
				phpgwapi_cache::message_set(lang('%1 claim is already registered for this project', $total_records),'message');
				$GLOBALS['phpgw']->session->appsession('session_data', 'tenant_claim_receipt', $receipt);
				$this->bo->status = 'all';
				$this->status = 'all';
				$this->index($project_id);
			}
			else if( !empty($claim[0]['claim_id']))
			{
				$this->edit($project_id, $claim[0]['claim_id']);
			}
			else
			{
				$this->edit($project_id);
			}

			return;
		}

		function close()
		{
			if ( !$this->acl_edit)
			{
				phpgw::no_access();
			}
			$claim_id = phpgw::get_var('claim_id', 'int');

			if($this->bo->close($claim_id))
			{
				return lang('Tenant claim') . " " . $claim_id . " " . lang("has been closed");
			}
		}

		function edit( $project_id = '', $claim_id = 0 )
		{
			if (!$this->acl_add && !$this->acl_edit)
			{
				phpgw::no_access();
			}

			$claim_id = $claim_id ? $claim_id : phpgw::get_var('claim_id', 'int');

			$values = phpgw::get_var('values');
			//_debug_array($values);die;
			$values['project_id'] = phpgw::get_var('project_id', 'int');
			$values['b_account_id'] = phpgw::get_var('b_account_id', 'int', 'POST');
			$values['b_account_name'] = phpgw::get_var('b_account_name', 'string', 'POST');
			$values['tenant_id'] = phpgw::get_var('tenant_id', 'int', 'POST');
			$values['last_name'] = phpgw::get_var('last_name', 'string', 'POST');
			$values['first_name'] = phpgw::get_var('first_name', 'string', 'POST');

			$tabs = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab = 'general';

			if ($project_id)
			{
				$values['project_id'] = $project_id;
			}

			$this->boproject = CreateObject('property.boproject');

//			$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_claim','files'));

			if ($values['save'] || $values['apply'])
			{
				if (!$values['cat_id'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a category !'));
				}

				if (!$values['b_account_id'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a budget account !'));
				}

				if (!$values['workorder'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a workorder !'));
				}

				if (!$receipt['error'])
				{
					$values['claim_id'] = $claim_id;
					$receipt = $this->bo->save($values);
					$claim_id = $receipt['claim_id'];
					$this->cat_id = ($values['cat_id'] ? $values['cat_id'] : $this->cat_id);

					//----------files
					$bofiles = CreateObject('property.bofiles');
					if (isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles->delete_file("/tenant_claim/{$claim_id}/", $values);
					}

					$file_name = @str_replace(' ', '_', $_FILES['file']['name']);

					if ($file_name)
					{
						$to_file = "{$bofiles->fakebase}/tenant_claim/{$claim_id}/{$file_name}";

						if ($bofiles->vfs->file_exists(array(
								'string' => $to_file,
								'relatives' => Array(RELATIVE_NONE)
							)))
						{
							$receipt['error'][] = array('msg' => lang('This file already exists !'));
						}
						else
						{
							$bofiles->create_document_dir("tenant_claim/$claim_id");
							$bofiles->vfs->override_acl = 1;

							if (!$bofiles->vfs->cp(array(
									'from' => $_FILES['file']['tmp_name'],
									'to' => $to_file,
									'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
							{
								$receipt['error'][] = array('msg' => lang('Failed to upload file !'));
							}
							$bofiles->vfs->override_acl = 0;
						}
					}
					//-----------
					self::message_set($receipt);

					if ($values['save'])
					{
						self::redirect(array('menuaction' => 'property.uitenant_claim.index'));
					}
					else
					{
						self::redirect(array('menuaction' => 'property.uitenant_claim.edit', 'claim_id' => $claim_id));
					}
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uitenant_claim.index'));
			}


			if ($claim_id)
			{
				$values = $this->bo->read_single($claim_id);
			}

			$project_values = $this->boproject->read_single($values['project_id'], array(), true);

			$project_values['workorder_budget'] = $this->boproject->get_orders(array('project_id' => $values['project_id'],
				'year' => 0));
			//_debug_array($project_values);die();
			$soinvoice = CreateObject('property.soinvoice');

			foreach ($project_values['workorder_budget'] as &$workorder)
			{
				$_vouchers = array();
				$vouchers = $soinvoice->read_invoice(array('paid' => '1', 'workorder_id' => $workorder['workorder_id'],
					'user_lid' => 'all'));
				foreach ($vouchers as $entry)
				{
					$_vouchers[] = $entry['voucher_id'];
				}
				$vouchers = $soinvoice->read_invoice(array('workorder_id' => $workorder['workorder_id'],
					'user_lid' => 'all'));
				unset($entry);
				foreach ($vouchers as $entry)
				{
					$_vouchers[] = $entry['voucher_id'];
				}

				$workorder['voucher_id'] = implode(', ', $_vouchers);

				$workorder['selected'] = in_array($workorder['workorder_id'], $values['workorders']);
				$workorder['claim_issued'] = in_array($workorder['workorder_id'], $values['claim_issued']);
			}


			//_debug_array($project_values);die();

			$table_header_workorder[] = array
				(
				'lang_workorder_id' => lang('Workorder'),
				'lang_budget' => lang('Budget'),
				'lang_calculation' => lang('Calculation'),
				'lang_vendor' => lang('Vendor'),
				'lang_charge_tenant' => lang('Charge tenant'),
				'lang_select' => lang('Select')
			);

			$bolocation = CreateObject('property.bolocation');

			$location_data = $bolocation->initiate_ui_location(array(
				'values' => $project_values['location_data'],
				'type_id' => count(explode('-', $project_values['location_data']['location_code'])),
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => $project_values['location_data']['tenant_id'],
				'lookup_type' => 'view',
				'lookup_entity' => $this->bocommon->get_lookup_entity('project'),
				'entity_data' => $project_values['p']
			));

			if ($project_values['contact_phone'])
			{
				for ($i = 0; $i < count($location_data['location']); $i++)
				{
					if ($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}


			if ($project_values['location_data']['tenant_id'] && !$values['tenant_id'])
			{
				$values['tenant_id'] = $project_values['location_data']['tenant_id'];
				$values['last_name'] = $project_values['location_data']['last_name'];
				$values['first_name'] = $project_values['location_data']['first_name'];
			}
			else if ($values['tenant_id'])
			{
				$tenant = $this->bocommon->read_single_tenant($values['tenant_id']);
				$values['last_name'] = $tenant['last_name'];
				$values['first_name'] = $tenant['first_name'];
			}

			$this->cat_id = ($values['cat_id'] ? $values['cat_id'] : $this->cat_id);
			$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id' => $values['b_account_id'],
				'b_account_name' => $values['b_account_name']));

			$link_data = array
				(
				'menuaction' => 'property.uitenant_claim.edit',
				'claim_id' => $claim_id,
				'project_id' => $values['project_id']
			);

			$cats = CreateObject('phpgwapi.categories', -1, 'property', '.project');
			$cats->supress_info = true;

			$cat_list_project = $cats->return_array('', 0, false, '', '', '', false);
			$cat_list_project = $this->bocommon->select_list($project_values['cat_id'], $cat_list_project);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			for ($d = 0; $d < count($project_values['workorder_budget']); $d++)
			{
				if ($project_values['workorder_budget'][$d]['charge_tenant'] == 1)
				{
					$project_values['workorder_budget'][$d]['charge_tenant'] = 'x';
				}

				if ($project_values['workorder_budget'][$d]['selected'] == 1)
				{

					$project_values['workorder_budget'][$d]['budget_hidden'] = $project_values['workorder_budget'][$d]['budget'];
					$project_values['workorder_budget'][$d]['calculation_hidden'] = $project_values['workorder_budget'][$d]['calculation'];
					$project_values['workorder_budget'][$d]['actual_cost_hidden'] = $project_values['workorder_budget'][$d]['actual_cost'];
					$project_values['workorder_budget'][$d]['selected'] = '<input type="checkbox" name="values[workorder][]" checked value="' . $project_values['workorder_budget'][$d]['workorder_id'] . '">';
				}
				else
				{
					$project_values['workorder_budget'][$d]['budget_hidden'] = 0;
					$project_values['workorder_budget'][$d]['calculation_hidden'] = 0;
					$project_values['workorder_budget'][$d]['actual_cost_hidden'] = 0;
					$project_values['workorder_budget'][$d]['selected'] = '<input type="checkbox" name="values[workorder][]" value="' . $project_values['workorder_budget'][$d]['workorder_id'] . '">';
				}
//				$project_values['workorder_budget'][$d]['selected'].= $project_values['workorder_budget'][$d]['claim_issued'] ? 'ok' : '';

				if ($project_values['workorder_budget'][$d]['claim_issued'] == 1)
				{

					$sumaBudget += $project_values['workorder_budget'][$d]['budget_hidden'];
					$sumactualcost += $project_values['workorder_budget'][$d]['actual_cost'];
				}
			}


			$myColumnDefs0 = array
				(
				array('key' => 'workorder_id', 'label' => lang('Workorder'), 'sortable' => true,
					'resizeable' => true, 'formatter' => 'JqueryPortico.formatLinkTenant', 'value_footer' => lang('Sum')),
				array('key' => 'budget', 'label' => lang('Budget'), 'sortable' => true, 'resizeable' => true,
					'formatter' => 'JqueryPortico.FormatterAmount0', 'value_footer' => number_format($sumaBudget, 0, $this->decimal_separator, ' ')),
				array('key' => 'budget_hidden', 'hidden' => true),
				array('key' => 'calculation', 'label' => lang('Calculation'), 'sortable' => true,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'calculation_hidden', 'hidden' => true),
				array('key' => 'actual_cost', 'label' => lang('actual cost'), 'sortable' => true,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterAmount0', 'value_footer' => number_format($sumactualcost, 0, $this->decimal_separator, ' ')),
				array('key' => 'actual_cost_hidden', 'hidden' => true),
				array('key' => 'vendor_name', 'label' => lang('Vendor'), 'sortable' => true,
					'resizeable' => true),
				array('key' => 'charge_tenant', 'label' => lang('Charge tenant'), 'sortable' => true,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter'),
				array('key' => 'status', 'label' => 'Status', 'sortable' => true, 'resizeable' => true),
				array('key' => 'voucher_id', 'label' => lang('voucher'), 'sortable' => true,
					'resizeable' => true),
				array('key' => 'selected', 'label' => lang('select'), 'sortable' => false, 'resizeable' => false)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'data' => json_encode($project_values['workorder_budget']),
				'ColumnDefs' => $myColumnDefs0,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => false)
				)
			);

			if ($claim_id)
			{
				$record_history = $this->bo->read_record_history($claim_id);
			}
			else
			{
				$record_history = array();
			}

//--------------files
			$link_file_data = array
				(
				'menuaction' => 'property.uitenant_claim.view_file',
				'id' => $claim_id
			);

			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			$_files = $this->bo->get_files($claim_id);

			$lang_view_file = lang('click to view file');
			$lang_delete_file = lang('Check to delete file');
			$z = 0;
			$content_files = array();
			foreach ($_files as $_file)
			{
				$content_files[$z]['file_name'] = "<a href=\"{$link_view_file}&amp;file_id={$_file['file_id']}\" target=\"_blank\" title=\"{$lang_view_file}\">{$_file['name']}</a>";
				$content_files[$z]['delete_file'] = "<input type=\"checkbox\" name=\"values[file_action][]\" value=\"{$_file['file_id']}\" title=\"{$lang_delete_file}\">";
				$z++;
			}

			$myColumnDefs1 = array
				(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'delete_file', 'label' => lang('Delete file'), 'sortable' => false,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => "''",
				'data' => json_encode($content_files),
				'ColumnDefs' => $myColumnDefs1,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$myColumnDefs2 = array
				(
				array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_user', 'label' => lang('User'), 'Action' => true, 'resizeable' => true),
				array('key' => 'value_action', 'label' => lang('Action'), 'sortable' => true,
					'resizeable' => true),
				array('key' => 'value_old_value', 'label' => lang('old value'), 'sortable' => true,
					'resizeable' => true),
				array('key' => 'value_new_value', 'label' => lang('New Value'), 'sortable' => true,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => "''",
				'data' => json_encode($record_history),
				'ColumnDefs' => $myColumnDefs2,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'table_header_workorder' => $table_header_workorder,
				'lang_no_workorders' => lang('No workorder budget'),
				'workorder_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.view')),
				'lang_start_date' => lang('Project start date'),
				'value_start_date' => $project_values['start_date'],
				'value_entry_date' => $values['entry_date'] ? $GLOBALS['phpgw']->common->show_date($values['entry_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
				'base_java_url' => json_encode(array(menuaction => "property.uitenant_claim.edit",
					claim_id => $claim_id)),
				'lang_end_date' => lang('Project end date'),
				'value_end_date' => $project_values['end_date'],
				'lang_charge_tenant' => lang('Charge tenant'),
				'charge_tenant' => $project_values['charge_tenant'],
				'lang_power_meter' => lang('Power meter'),
				'value_power_meter' => $project_values['power_meter'],
				'lang_budget' => lang('Budget'),
				'value_budget' => $project_values['budget'],
				'lang_reserve' => lang('reserve'),
				'value_reserve' => $project_values['reserve'],
				'lang_reserve_statustext' => lang('Enter the reserve'),
				'lang_reserve_remainder' => lang('reserve remainder'),
				'value_reserve_remainder' => $reserve_remainder,
				'value_reserve_remainder_percent' => $remainder_percent,
				'vendor_data' => $vendor_data,
				'location_data' => $location_data,
				'location_type' => 'view',
				'lang_project_id' => lang('Project ID'),
				'value_project_id' => $project_values['project_id'],
				'lang_name' => lang('Name'),
				'value_name' => $project_values['name'],
				'lang_descr' => lang('Description'),
				'sum_workorder_budget' => $project_values['sum_workorder_budget'],
				'sum_workorder_calculation' => $project_values['sum_workorder_calculation'],
				'workorder_budget' => $project_values['workorder_budget'],
				'sum_workorder_actual_cost' => $project_values['sum_workorder_actual_cost'],
				'lang_actual_cost' => lang('Actual cost'),
				'lang_coordinator' => lang('Coordinator'),
				'lang_sum' => lang('Sum'),
				'select_user_name' => 'project_values[coordinator]',
				'lang_no_user' => lang('Select coordinator'),
				'user_list' => $this->bocommon->get_user_list('select', $project_values['coordinator'], $extra = false, $default = false, $start = -1, $sort = 'ASC', $order = 'account_lastname', $query = '', $offset = -1),
				'currency' => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'lang_contact_phone' => lang('Contact phone'),
				'contact_phone' => $project_values['contact_phone'],
				'b_account_data' => $b_account_data,
				'lang_select_workorder_statustext' => lang('Include the workorder to this claim'),
				'cat_list_project' => $cat_list_project,
				//------------------
				'lang_status' => lang('Status'),
				'lang_status_statustext' => lang('Select status'),
				'status_list' => $this->bo->get_status_list(array('format' => 'select', 'selected' => $values['status'],
					'default' => 'open')),
				'lang_no_status' => lang('No status'),
				'status_name' => 'values[status]',
				'lang_amount' => lang('amount'),
				'lang_amount_statustext' => lang('The total amount to claim'),
				'value_amount' => $values['amount'],
				'tenant_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uilookup.tenant')),
				'lang_tenant' => lang('tenant'),
				'value_tenant_id' => $values['tenant_id'],
				'value_last_name' => $values['last_name'],
				'value_first_name' => $values['first_name'],
				'lang_tenant_statustext' => lang('Select a tenant'),
				'size_last_name' => strlen($values['last_name']),
				'size_first_name' => strlen($values['first_name']),
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_claim_id' => lang('ID'),
				'value_claim_id' => $claim_id,
				'lang_remark' => lang('remark'),
				'lang_category' => lang('category'),
				'lang_save' => lang('save'),
				'lang_cancel' => lang('cancel'),
				'lang_apply' => lang('apply'),
				'value_remark' => $values['remark'],
				'value_cat' => $values['cat'],
				'lang_remark_statustext' => lang('Enter a remark for this claim'),
				'lang_apply_statustext' => lang('Apply the values'),
				'lang_cancel_statustext' => lang('Leave the claim untouched and return back to the list'),
				'lang_save_statustext' => lang('Save the claim and return back to the list'),
				'lang_no_cat' => lang('no category'),
				'lang_cat_statustext' => lang('Select the category the claim belongs to. To do not use a category select NO CATEGORY'),
				'select_name' => 'values[cat_id]',
				'cat_list' => $this->bocommon->select_category_list(array('format' => 'select',
					'selected' => $this->cat_id, 'type' => 'tenant_claim', 'order' => 'descr')),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location', 'date',
					'security', 'file'))
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Tenant claim') . ': ' . ($claim_id ? lang('edit claim') : lang('add claim'));

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::add_javascript('property', 'portico', 'tenant_claim.edit.js');

			self::render_template_xsl(array('tenant_claim', 'datatable_inline', 'files'), array(
				'edit' => $data));
		}

		function delete()
		{

			if (!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 8, 'acl_location' => $this->acl_location));
			}

			$claim_id = phpgw::get_var('claim_id', 'int');
			$delete = phpgw::get_var('delete', 'bool', 'POST');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($claim_id);
				return "claim_id " . $claim_id . " " . lang("has been deleted");
			}

			$link_data = array
				(
				'menuaction' => 'property.uitenant_claim.index'
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitenant_claim.delete',
					'claim_id' => $claim_id)),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('Tenant claim');
			$function_msg = lang('delete claim');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$claim_id = phpgw::get_var('claim_id', 'int');

			$this->boproject = CreateObject('property.boproject');
//			$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_claim'));

			$values = $this->bo->read_single($claim_id);

			$project_values = $this->boproject->read_single($values['project_id']);

			$tabs = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab = 'general';

			$table_header_workorder[] = array
				(
				'lang_workorder_id' => lang('Workorder'),
				'lang_budget' => lang('Budget'),
				'lang_calculation' => lang('Calculation'),
				'lang_vendor' => lang('Vendor'),
				'lang_charge_tenant' => lang('Charge tenant'),
				'lang_select' => lang('Select')
			);

			$bolocation = CreateObject('property.bolocation');

			$location_data = $bolocation->initiate_ui_location(array(
				'values' => $project_values['location_data'],
				'type_id' => count(explode('-', $project_values['location_data']['location_code'])),
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => $project_values['location_data']['tenant_id'],
				'lookup_type' => 'view',
				'lookup_entity' => $this->bocommon->get_lookup_entity('project'),
				'entity_data' => $project_values['p']
			));

			if ($project_values['contact_phone'])
			{
				for ($i = 0; $i < count($location_data['location']); $i++)
				{
					if ($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}


			if ($project_values['location_data']['tenant_id'] && !$values['tenant_id'])
			{
				$values['tenant_id'] = $project_values['location_data']['tenant_id'];
				$values['last_name'] = $project_values['location_data']['last_name'];
				$values['first_name'] = $project_values['location_data']['first_name'];
			}
			else if ($values['tenant_id'])
			{
				$tenant = $this->bocommon->read_single_tenant($values['tenant_id']);
				$values['last_name'] = $tenant['last_name'];
				$values['first_name'] = $tenant['first_name'];
			}


			if ($values['workorder'] && $project_values['workorder_budget'])
			{
				foreach ($values['workorder'] as $workorder_id)
				{
					for ($i = 0; $i < count($project_values['workorder_budget']); $i++)
					{
						if ($project_values['workorder_budget'][$i]['workorder_id'] == $workorder_id)
						{
							$project_values['workorder_budget'][$i]['selected'] = true;
						}
					}
				}
			}

			$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id' => $values['b_account_id'],
				'b_account_name' => $values['b_account_name'],
				'type' => 'view'));


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$cats = CreateObject('phpgwapi.categories', -1, 'property', '.project');
			$cats->supress_info = true;

			$cat_list_project = $cats->return_array('', 0, false, '', '', '', false);
			$cat_list_project = $this->bocommon->select_list($project_values['cat_id'], $cat_list_project);

			$data = array
				(
				'table_header_workorder' => $table_header_workorder,
				'lang_no_workorders' => lang('No workorder budget'),
				'workorder_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.view')),
				'lang_start_date' => lang('Project start date'),
				'value_start_date' => $project_values['start_date'],
				'lang_end_date' => lang('Project end date'),
				'value_end_date' => $project_values['end_date'],
				'lang_charge_tenant' => lang('Charge tenant'),
				'charge_tenant' => $project_values['charge_tenant'],
				'lang_power_meter' => lang('Power meter'),
				'value_power_meter' => $project_values['power_meter'],
				'lang_budget' => lang('Budget'),
				'value_budget' => $project_values['budget'],
				'lang_reserve' => lang('reserve'),
				'value_reserve' => $project_values['reserve'],
				'lang_reserve_statustext' => lang('Enter the reserve'),
				'lang_reserve_remainder' => lang('reserve remainder'),
				'value_reserve_remainder' => $reserve_remainder,
				'value_reserve_remainder_percent' => $remainder_percent,
				'location_data' => $location_data,
				'location_type' => 'view',
				'lang_project_id' => lang('Project ID'),
				'value_project_id' => $project_values['project_id'],
				'lang_name' => lang('Name'),
				'value_name' => $project_values['name'],
				'lang_descr' => lang('Description'),
				'sum_workorder_budget' => $project_values['sum_workorder_budget'],
				'sum_workorder_calculation' => $project_values['sum_workorder_calculation'],
				'workorder_budget' => $project_values['workorder_budget'],
				'sum_workorder_actual_cost' => $project_values['sum_workorder_actual_cost'],
				'lang_actual_cost' => lang('Actual cost'),
				'lang_coordinator' => lang('Coordinator'),
				'lang_sum' => lang('Sum'),
				'select_user_name' => 'project_values[coordinator]',
				'lang_no_user' => lang('Select coordinator'),
				'user_list' => $this->bocommon->get_user_list('select', $project_values['coordinator'], $extra = false, $default = false, $start = -1, $sort = 'ASC', $order = 'account_lastname', $query = '', $offset = -1),
				'lang_no_status' => lang('Select status'),
				'currency' => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'lang_contact_phone' => lang('Contact phone'),
				'contact_phone' => $project_values['contact_phone'],
				'b_account_data' => $b_account_data,
				'cat_list_project' => $cat_list_project,
				//------------------
				'lang_status' => lang('Status'),
				'status_list' => $this->bo->get_status_list(array('format' => 'select', 'selected' => $values['status'],
					'default' => 'open')),
				'lang_amount' => lang('amount'),
				'value_amount' => $values['amount'],
				'lang_tenant' => lang('tenant'),
				'value_tenant_id' => $values['tenant_id'],
				'value_last_name' => $values['last_name'],
				'value_first_name' => $values['first_name'],
				'size_last_name' => strlen($values['last_name']),
				'size_first_name' => strlen($values['first_name']),
				'lang_claim_id' => lang('ID'),
				'value_claim_id' => $claim_id,
				'lang_remark' => lang('remark'),
				'lang_category' => lang('category'),
				'lang_save' => lang('save'),
				'lang_cancel' => lang('cancel'),
				'lang_apply' => lang('apply'),
				'value_remark' => $values['remark'],
				'value_cat' => $values['cat'],
				'cat_list' => $this->bocommon->select_category_list(array('format' => 'select',
					'selected' => $values['cat_id'], 'type' => 'tenant_claim', 'order' => 'descr')),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitenant_claim.index')),
				'lang_done' => lang('done'),
				'value_entry_date' => $values['entry_date'] ? $GLOBALS['phpgw']->common->show_date($values['entry_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location', 'date',
					'security', 'file'))
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Tenant claim') . '::' . lang('view claim');

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl(array('tenant_claim', 'datatable_inline', 'nextmatchs'), array(
				'view' => $data));
		}
	}