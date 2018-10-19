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
	 * @subpackage agreement
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class property_uipricebook extends phpgwapi_uicommon_jquery
	{

		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $sub;
		var $currentapp;
		var $public_functions = array
			(
			'query' => true,
			'activity' => true,
			'index' => true,
			'agreement_group' => true,
			'edit_agreement_group' => true,
			'edit_activity' => true,
			'activity_vendor' => true,
			'prizing' => true,
			'delete' => true,
			'download' => true,
			'download_2' => true,
			'_get_Filters' => true,
			'_get_Filters_Activity' => true,
			'query_Activity' => true,
			'query_vendor' => true,
			'query_group' => true,
			'query_group_filter' => true,
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');

			$this->bo = CreateObject('property.bopricebook', true);
			$this->bocommon = CreateObject('property.bocommon');
			$this->contacts = CreateObject('property.sogeneric');
			$this->contacts->get_location_info('vendor', false);

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.agreement';
			$this->acl_read = $this->acl->check('.agreement', PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check('.agreement', PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check('.agreement', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check('.agreement', PHPGW_ACL_DELETE, 'property');
			$this->acl_manage = $this->acl->check('.agreement', 16, 'property');

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->cat_id = $this->bo->cat_id;
			$this->allrows = $this->bo->allrows;
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
				'allrows' => $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$list = $this->bo->read();

			$name = array('num', 'branch', 'vendor_id', 'm_cost', 'w_cost', 'total_cost',
				'this_index', 'unit', 'descr', 'index_count');
			$descr = array
				(
				lang('Activity Num'),
				lang('Branch'),
				lang('Vendor'),
				lang('Material cost'),
				lang('Labour cost'),
				lang('Total Cost'),
				lang('Last index'),
				lang('Unit'),
				lang('Description'),
				lang('Index Count')
			);

			$this->bocommon->download($list, $name, $descr);
		}

		private function _get_Filters()
		{
			$values_combo_box = array();
			$combos = array();

			$values_combo_box[0] = $this->bo->get_vendor_list('filter', $this->cat_id);
			array_unshift($values_combo_box[0], array('id' => '', 'name' => lang('no category')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'cat_id',
				'text' => lang('no Category'),
				'list' => $values_combo_box[0]
			);

			return $combos;
		}

		public function query()
		{
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
				'filter' => $this->filter,
				'cat_id' => $this->cat_id,
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
			);

			$pricebook_list = $this->bo->read($params);

			$i = 0;
			if (isSet($pricebook_list) AND is_array($pricebook_list))
			{
				foreach ($pricebook_list as $pricebook)
				{
					$check = '';
					if ($this->acl_manage)
					{
						if (!empty($pricebook['total_cost']))
						{
							$check = "<input type='checkbox' name='values[update][" . $i . "]' value='" . $i . "' class='mychecks select_check' />";
						}
					}
					$content[] = array
						(
						'counter' => $i,
						'activity_id' => $pricebook['activity_id'],
						'num' => $pricebook['num'],
						'branch' => $pricebook['branch'],
						'vendor_id' => $pricebook['vendor_id'],
						'agreement_id' => $pricebook['agreement_id'],
						'm_cost' => $pricebook['m_cost'],
						'w_cost' => $pricebook['w_cost'],
						'total_cost' => $pricebook['total_cost'],
						'this_index' => $pricebook['this_index'],
						'unit' => $pricebook['unit'],
						'descr' => $pricebook['descr'],
						'index_count' => $pricebook['index_count'],
						'select' => $check
					);
					$i++;
				}
			}

			if ($export)
			{
				return $content;
			}

			$result_data = array('results' => $content);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function index()
		{

			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$values = phpgw::get_var('values');

			if ($values['submit_update'] && phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->bo->update_pricebook($values);
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			phpgwapi_jquery::load_widget('datepicker');

			$appname = lang('pricebook');
			$function_msg = lang('list pricebook per vendor');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'property.uipricebook.index',
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array(
						'menuaction' => 'property.uipricebook.download',
						'cat_id' => $this->cat_id,
						'filter' => $this->filter,
						'export' => true,
						'skip_origin' => true,
						'allrows' => true
					)),
					'allrows' => true,
					'editor' => '',
					'field' => array(
						array('key' => 'activity_id', 'label' => lang('Activity Id'), 'sortable' => false,
							'hidden' => TRUE, 'className' => 'center'),
						array('key' => 'num', 'label' => lang('Activity num'), 'sortable' => true,
							'hidden' => FALSE, 'className' => 'center'),
						array('key' => 'vendor_id', 'label' => lang('Vendor'), 'sortable' => false,
							'hidden' => FALSE, 'className' => 'center'),
						array('key' => 'branch', 'label' => lang('branch'), 'sortable' => false, 'hidden' => FALSE,
							'className' => 'center'),
						array('key' => 'descr', 'label' => lang('Description'), 'sortable' => false,
							'hidden' => FALSE),
						array('key' => 'unit', 'label' => lang('Unit'), 'sortable' => false, 'hidden' => FALSE,
							'className' => 'center'),
						array('key' => 'w_cost', 'label' => lang('Labour cost'), 'sortable' => false,
							'hidden' => FALSE, 'className' => 'right'),
						array('key' => 'm_cost', 'label' => lang('Material cost'), 'sortable' => false,
							'hidden' => FALSE, 'className' => 'right'),
						array('key' => 'total_cost', 'label' => lang('Total Cost'), 'sortable' => true,
							'hidden' => FALSE, 'className' => 'right'),
						array('key' => 'this_index', 'label' => lang('Last index'), 'sortable' => false,
							'hidden' => FALSE, 'className' => 'center'),
						array('key' => 'index_count', 'label' => lang('Index count'), 'sortable' => false,
							'hidden' => FALSE, 'className' => 'center'),
						array('key' => 'agreement_id', 'label' => lang('Agreement id'), 'sortable' => false,
							'hidden' => TRUE, 'className' => 'center'),
						array('key' => 'select', 'label' => lang('Select'), 'sortable' => false, 'hidden' => false,
							'className' => 'center')
					)
				),
				'end-toolbar' => array(
					'fields' => array(
						'field' => array(
							array(
								'type' => 'label',
								'id' => 'lbl_input_index',
								'value' => lang('New Index'),
								'style' => 'filter',
								'group' => '1'
							),
							array
								(
								'type' => 'text',
								'id' => 'txt_index',
								'name' => 'txt_index',
								'tab_index' => 5,
								'style' => 'filter',
								'group' => '1'
							),
							array(
								'type' => 'date-picker',
								'id' => 'start_date',
								'name' => 'start_date',
								'value' => '',
								'style' => 'filter',
								'group' => '1'
							),
							array
								(
								'type' => 'button',
								'id' => 'btn_update',
								'value' => lang('Update'),
								'tab_index' => 5,
								'style' => 'filter',
								'group' => '1',
								'action' => 'onclikUpdatePricebook()'
							)
						)
					)
				)
			);

			$filters = $this->_get_Filters();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			/* if($this->acl_manage)
			  {
			  $GLOBALS['phpgw']->jqcal->add_listener('values_date');

			  $table_update[] = array
			  (
			  'lang_new_index'				=> lang('New index'),
			  'lang_new_index_statustext'		=> lang('Enter a new index'),
			  'lang_date_statustext'			=> lang('Select the date for the update'),
			  'lang_update'					=> lang('Update'),
			  'lang_update_statustext'		=> lang('update selected investments')
			  );
			  } */

			//$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'activity_id',
						'source' => 'activity_id'
					),
				)
			);

			$parameters2 = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'activity_id',
						'source' => 'activity_id'
					),
					array
						(
						'name' => 'agreement_id',
						'source' => 'agreement_id'
					),
					array
						(
						'name' => 'cat_id',
						'source' => 'vendor_id'
					)
				)
			);

			if ($this->acl_manage)
			{

				$data['datatable']['actions'][] = array
					(
					'my_name' => 'edit',
					'statustext' => lang('edit the pricebook'),
					'text' => lang('edit'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uipricebook.edit_activity',
					)),
					'parameters' => json_encode($parameters)
				);


				$data['datatable']['actions'][] = array
					(
					'my_name' => 'prizing',
					'statustext' => lang('view or edit prizing history of this element'),
					'text' => lang('prizing'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uipricebook.prizing',
					)),
					'parameters' => json_encode($parameters2)
				);
			}

			phpgwapi_jquery::load_widget('numberformat');

			self::add_javascript('property', 'portico', 'pricebook.index.js');
			self::render_template_xsl('datatable_jquery', $data);
		}

		private function query_group_filter()
		{
			$values_combo_box = array();
			$combos = array();

			$values_combo_box[0] = $this->bo->select_status_list('filter', $this->cat_id);
			$default_value = array('id' => '', 'name' => lang('No status'));
			array_unshift($values_combo_box[0], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'cat_id',
				'text' => lang('No status'),
				'list' => $values_combo_box[0]
			);

			return $combos;
		}

		public function query_group()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$cat_id = phpgw::get_var('cat_id');
			$export = phpgw::get_var('export', 'bool');

			switch ($columns[$order[0]['column']]['data'])
			{
				case 'agreement_group_id':
					$order_field = 'id';
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
				'filter' => $this->filter,
				'cat_id' => $cat_id,
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
			);

			$values = $this->bo->read_agreement_group($params);

			if ($export)
			{
				return $values;
			}

			$result_data = array('results' => $values);
			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function agreement_group()
		{
			if (!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 16, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::agreement::pricebook::group';

			$GLOBALS['phpgw']->session->appsession('referer', 'property', '');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_group();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname = lang('pricebook');
			$function_msg = lang('list agreement group');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

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
						'menuaction' => 'property.uipricebook.agreement_group',
						'cat_id' => $this->cat_id,
						'filter' => $this->filter,
						'phpgw_return_as' => 'json'
					)),
					'new_item' => self::link(array(
						'menuaction' => 'property.uipricebook.edit_agreement_group'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(
						array(
							'key' => 'agreement_group_id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'JqueryPortico.FormatterCenter'
						),
						array(
							'key' => 'num',
							'label' => lang('Activity Num'),
							'sortable' => true
						),
						array(
							'key' => 'descr',
							'label' => lang('Description'),
							'sortable' => false
						),
						array(
							'key' => 'status',
							'label' => lang('Status'),
							'sortable' => false,
							'formatter' => 'JqueryPortico.FormatterCenter'
						)
					)
				)
			);

			$filters = $this->query_group_filter();
			krsort($filters);
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
						'name' => 'agreement_group_id',
						'source' => 'agreement_group_id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uipricebook.edit_agreement_group'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('open edit in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uipricebook.edit_agreement_group',
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'delete',
				'text' => lang('delete'),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uipricebook.delete',
					'method' => 'agreement_group'
				)),
				'parameters' => json_encode($parameters)
			);

			unset($parameters);

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}

		function edit_agreement_group()
		{
			if (!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 16, 'acl_location' => $this->acl_location));
			}

			$agreement_group_id = phpgw::get_var('agreement_group_id', 'int');
			$values = phpgw::get_var('values');
			$tabs = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab = 'general';

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook'));

			if ($values['save'])
			{
				$values['agreement_group_id'] = $agreement_group_id;

				if (!$values['num'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter an agreement group code !'));
					$error_id = true;
				}
				if (!$values['status'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a status !'));
				}
				if (!$values['descr'])
				{
					$receipt['error'][] = array('msg' => lang('please enter a description !'));
				}

				if ($values['num'] && !$agreement_group_id)
				{
					if ($this->bo->check_agreement_group_num($values['num']))
					{
						$receipt['error'][] = array('msg' => lang('This agreement group code is already registered!') . '[ ' . $values['num'] . ' ]');
						$error_id = true;
					}
				}

				if ($agreement_group_id)
				{
					$action = 'edit';
				}

				if ($values['copy_agreement_group'])
				{
					$action = 'add';
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->save_agreement_group($values, $action);
					if (!empty($receipt['agreement_group_id']))
					{
						$agreement_group_id = $receipt['agreement_group_id'];
					}
				}

				if ($agreement_group_id)
				{
					$values['agreement_group_id'] = $agreement_group_id;
					$action = 'edit';
				}
				else
				{
					$agreement_group_id = $values['agreement_group_id'];
				}
			}
			else
			{
				$values['agreement_group_id'] = $agreement_group_id;
				if ($agreement_group_id)
				{
					$values = $this->bo->read_single_agreement_group($agreement_group_id);
				}
			}

			//_debug_array($values);
			if ($agreement_group_id)
			{
				$function_msg = lang('edit agreement group');
			}
			else
			{
				$function_msg = lang('add agreement group');
			}

			if ($values['cat_id'] > 0)
			{
				$this->cat_id = $values['cat_id'];
			}

			if ($error_id)
			{
				unset($values['num']);
			}

			$link_data = array
			(
				'menuaction' => 'property.uipricebook.edit_agreement_group',
				'agreement_group_id' => $agreement_group_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');

			$data = array
			(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uipricebook.agreement_group')),
				'lang_agreement_group_id' => lang('Agreement group ID'),
				'lang_num' => lang('Agreement group code'),
				'lang_status' => lang('Status'),
				'status_list' => $this->bo->select_status_list('select', $values['status']),
				'status_name' => 'values[status]',
				'lang_no_status' => lang('Select status'),
				'lang_save' => lang('save'),
				'lang_done' => lang('done'),
				'lang_descr' => lang('description'),
				'value_agreement_group_id' => $values['agreement_group_id'],
				'value_start_date' => $GLOBALS['phpgw']->common->show_date(
								phpgwapi_datetime::date_to_timestamp($values['start_date']),
								$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
							),
				'value_end_date' => $GLOBALS['phpgw']->common->show_date(
								phpgwapi_datetime::date_to_timestamp($values['end_date']),
								$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
							),
				'value_num' => $values['num'],
				'value_descr' => $values['descr'],
				'lang_num_statustext' => lang('An unique code for this activity'),
				'lang_done_statustext' => lang('Back to the list'),
				'lang_save_statustext' => lang('Save the building'),
				'lang_descr_statustext' => lang('Enter the description for this activity'),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
			);

			phpgwapi_jquery::formvalidator_generate(array());

			$appname = lang('pricebook');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_agreement_group' => $data));
		}

		function prizing()
		{
			if (!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 16, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook',
				'nextmatchs',
				'search_field'));

			$cat_id = phpgw::get_var('cat_id', 'int', 'GET');
			$activity_id = phpgw::get_var('activity_id', 'int');
			$vendor_id = phpgw::get_var('vendor_id', 'int', 'GET');
			$agreement_id = phpgw::get_var('agreement_id', 'int', 'GET');
			$values = phpgw::get_var('values');

			$referer = $GLOBALS['phpgw']->session->appsession('referer', 'property');
			if (!$referer)
			{
				$referer = phpgw::get_var('HTTP_REFERER', 'string', 'SERVER', phpgw::clean_value($GLOBALS['HTTP_REFERER']));
				$referer .= '&cat_id=' . $cat_id;
				$GLOBALS['phpgw']->session->appsession('referer', 'property', $referer);
			}

			if ($values['submit_update'])
			{
				if (!$values['date'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a date !'));
				}

				if (!$values['new_index'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter a new index for calculating next value(s)!'));
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->update_pricebook($values);
				}
			}

			if ($values['submit_add'])
			{
				if (!$values['date'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a date !'));
				}

				if (!$values['m_cost'] && !$values['w_cost'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter a value for either material cost, labour cost or both !'));
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->add_activity_first_prize($values);
				}
			}


			$pricebook_list = $this->bo->read_activity_prize($activity_id, $agreement_id);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if (isSet($pricebook_list) AND is_array($pricebook_list))
			{
				foreach ($pricebook_list as $pricebook)
				{

					if ($pricebook['current_index'])
					{
						$link_delete = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uipricebook.delete',
							'method' => 'prize', 'activity_id' => $activity_id, 'agreement_id' => $agreement_id,
							'index_count' => $pricebook['index_count']));
						$value_m_cost = $pricebook['m_cost'];
						$value_w_cost = $pricebook['w_cost'];
						$value_total_cost = $pricebook['total_cost'];
					}

					$content[] = array
						(
						'm_cost' => $pricebook['m_cost'],
						'w_cost' => $pricebook['w_cost'],
						'total_cost' => $pricebook['total_cost'],
						'this_index' => $pricebook['this_index'],
						'date' => $GLOBALS['phpgw']->common->show_date($pricebook['date'], $dateformat),
						'current_index' => $pricebook['current_index'],
						'index_count' => $pricebook['index_count'],
						'link_delete' => $link_delete,
						'lang_delete_statustext' => lang('Delete this entry'),
						'text_delete' => lang('delete'),
					);
				}
			}

			//_debug_array($content);
			$table_header[] = array
				(
				'lang_index_count' => lang('Index Count'),
				'lang_total_cost' => lang('Total Cost'),
				'lang_prizing' => lang('Prizing'),
				'lang_last_index' => lang('Last index'),
				'lang_m_cost' => lang('Material cost'),
				'lang_w_cost' => lang('Labour cost'),
				'lang_date' => lang('Date'),
				'lang_delete' => lang('Delete')
			);

			$GLOBALS['phpgw']->jqcal->add_listener('values_date');

			$table_update[] = array
				(
				'lang_new_index' => lang('New index'),
				'lang_new_index_statustext' => lang('Enter a new index'),
				'lang_date_statustext' => lang('Select the date for the update'),
				'lang_update' => lang('Update'),
				'lang_update_statustext' => lang('update selected investments')
			);

			$table_first_entry[] = array
				(
				'lang_m_cost' => lang('Material cost'),
				'lang_m_cost_statustext' => lang('Enter a value for the material cost'),
				'lang_w_cost' => lang('Labour cost'),
				'lang_w_cost_statustext' => lang('Enter a value for the labour cost'),
				'lang_date' => lang('Date'),
				'lang_date_statustext' => lang('Select the date for the first value'),
				'lang_add' => lang('Add'),
				'lang_add_statustext' => lang('Add first value for this prizing')
			);

			$link_data = array
				(
				'menuaction' => 'property.uipricebook.prizing',
				'activity_id' => $activity_id,
				'agreement_id' => $agreement_id
			);

			if (!$this->allrows)
			{
				$record_limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit = $this->bo->total_records;
			}

			$num_records = count($pricebook_list);


			$vendor_data = $this->contacts->read_single(array('id' => $vendor_id), array(
				'attributes' => array(array('column_name' => 'org_name'))));

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

			$activity = $this->bo->read_single_activity($activity_id);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action' => $referer,
				'lang_done' => lang('done'),
				'lang_done_statustext' => lang('Back to the list'),
				'allrows' => $this->allrows,
				'allow_allrows' => true,
				'start_record' => $this->start,
				'record_limit' => $record_limit,
				'num_records' => $num_records,
				'all_records' => $this->bo->total_records,
				'link_url' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'img_path' => $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
				'lang_vendor' => lang('Vendor'),
				'lang_activity' => lang('Activity'),
				'value_vendor_name' => $value_vendor_name,
				'value_activity_id' => $activity_id,
				'value_activity_code' => $activity['num'],
				'value_vendor_id' => $vendor_id,
				'value_m_cost' => $value_m_cost,
				'value_w_cost' => $value_w_cost,
				'value_total_cost' => $value_total_cost,
				'table_header_prizing' => $table_header,
				'values_prizing' => $content,
				'table_update' => $table_update,
				'table_first_entry' => $table_first_entry,
				'update_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uipricebook.prizing',
					'activity_id' => $activity_id, 'vendor_id' => $vendor_id))
			);

			$appname = lang('pricebook');
			$function_msg = lang('edit pricing');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('prizing' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function download_2()
		{

			$list = $this->bo->read_activities_pr_agreement_group();

			$name = array
				(
				'activity_id',
				'base_descr',
				'num',
				'descr',
				//	'branch',
				//	'dim_d',
				'ns3420',
				'unit',
			);

			$descr = array
				(
				'ID',
				lang('Base'),
				lang('Activity Num'),
				lang('Description'),
				//	lang('Branch'),
				//	lang('Dim d'),
				lang('NS3420'),
				lang('Unit'),
			);


			$this->bocommon->download($list, $name, $descr);
		}

		private function _get_Filters_Activity()
		{
			$values_combo_box = array();
			$combos = array();

			$values_combo_box[0] = $this->bo->get_agreement_group_list('filter', $this->cat_id);
			$default_value = array('id' => '', 'name' => lang('select agreement_group'));
			array_unshift($values_combo_box[0], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'cat_id',
				'text' => lang('select agreement_group'),
				'list' => $values_combo_box[0]
			);

			return $combos;
		}

		public function query_Activity()
		{
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
				'filter' => $this->filter,
				'cat_id' => $this->cat_id,
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export
			);

			$values = $this->bo->read_activities_pr_agreement_group($params);

			if ($export)
			{
				return $values;
			}

			$result_data = array('results' => $values);
			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;
			$result_data['sum_budget'] = number_format($this->bo->sum_budget_cost, 0, ',', ' ');

			return $this->jquery_results($result_data);
		}

		function activity()
		{
			if (!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 16, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::agreement::pricebook::activities';

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_Activity();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname = lang('pricebook');
			$function_msg = lang('list activities per agreement_group');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

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
						'menuaction' => 'property.uipricebook.activity',
						'cat_id' => $this->cat_id,
						'filter' => $this->filter,
						'phpgw_return_as' => 'json'
					)),
//                    'download'  => self::link(array(
//                        
//                    )),
					'new_item' => self::link(array(
						'menuaction' => 'property.uipricebook.edit_activity',
						'agreement_group' => $this->cat_id
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$filters = $this->_get_Filters_Activity();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}
			$uicols = array(
				'name' => array('activity_id', 'num', 'descr', 'unit_name', 'ns3420', 'base_descr',
					'branch', 'dim_d'),
				'input_type' => array('hidden', 'text', 'text', 'text', 'text', 'text', 'text',
					'text'),
				'descr' => array('', lang('Activity Num'), lang('Description'), lang('Unit'),
					lang('NS3420'), lang('Base'), lang('Branch'), lang('Dim d')),
				'formatter' => array('', '', '', '', '', '', '', '')
			);

			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'className' => $uicols['className'][$k],
					'sortable' => ($uicols['name'][$k] == 'num') ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);


				array_push($data['datatable']['field'], $params);
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'activity_id',
						'source' => 'activity_id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'vendor',
				'text' => lang('vendor'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uipricebook.activity_vendor',
					'agreement_group' => $this->cat_id
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'vendor',
				'text' => lang('open vendor in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uipricebook.activity_vendor',
					'agreement_group' => $this->cat_id,
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uipricebook.edit_activity',
					'agreement_group' => $this->cat_id
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('open edit in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uipricebook.edit_activity',
					'agreement_group' => $this->cat_id,
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'delete',
				'text' => lang('delete'),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uipricebook.delete',
					'method' => 'activity'
				)),
				'parameters' => json_encode($parameters)
			);

			unset($parameters);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query_vendor( $activity_id )
		{
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
				'filter' => $this->filter,
				'cat_id' => $this->cat_id,
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'activity_id' => $activity_id
			);

			$pricebook_list = $this->bo->read_vendor_pr_activity($params);

			foreach ($pricebook_list as $pricebook)
			{
				$content[] = array
					(
					'activity_id' => $pricebook['activity_id'],
					'agreement_id' => $pricebook['agreement_id'],
					'num' => $pricebook['num'],
					'branch' => $pricebook['branch'],
					'vendor_name' => $pricebook['vendor_name']
				);
			}

			if ($export)
			{
				return $content;
			}

			$result_data = array('results' => $content);
			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;
			$result_data['sum_budget'] = number_format($this->bo->sum_budget_cost, 0, ',', ' ');

			return $this->jquery_results($result_data);
		}

		function activity_vendor()
		{
			if (!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 16, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw']->session->appsession('referer', 'property', '');

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook', 'nextmatchs', 'search_field'));

			$activity_id = phpgw::get_var('activity_id', 'int');
			$values = phpgw::get_var('values');
			$values['vendor_id'] = phpgw::get_var('vendor_id', 'int', 'POST');

			if ($values['add'])
			{
				if (!$values['vendor_id'])
				{
					$receipt['error'][] = array('msg' => lang('Please - select a vendor!'));
				}
				else
				{
					$receipt = $this->bo->add_activity_vendor($values);
				}
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_vendor($activity_id);
			}


			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname = lang('pricebook');
			$function_msg = lang('list vendors per activity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

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
						'menuaction' => 'property.uipricebook.activity_vendor',
						'cat_id' => $this->cat_id,
						'filter' => $this->filter,
						'activity_id' => $activity_id,
						'phpgw_return_as' => 'json'
					)),
					'new_item' => self::link(array(
						'menuaction' => 'property.uipricebook.activity'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(
						array(
							'key' => 'activity_id',
							'label' => lang('Activity Id'),
							'sortable' => FALSE,
							'hidden' => TRUE,
							'formatter' => 'JqueryPortico.FormatterCenter'
						),
						array(
							'key' => 'agreement_id',
							'label' => lang('Agreement Id'),
							'sortable' => FALSE,
							'hidden' => TRUE,
							'formatter' => 'JqueryPortico.FormatterCenter'
						),
						array(
							'key' => 'num',
							'label' => lang('Activity Num'),
							'sortable' => TRUE,
							'formatter' => 'JqueryPortico.FormatterCenter'
						),
						array(
							'key' => 'branch',
							'label' => lang('branch'),
							'sortable' => FALSE,
							'formatter' => 'JqueryPortico.FormatterCenter'
						),
						array(
							'key' => 'vendor_name',
							'label' => lang('Vendor'),
							'sortable' => FALSE,
							'formatter' => 'JqueryPortico.FormatterCenter'
						)
					)
				)
			);

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array('vendor_id' => ''));


			if (!$this->allrows)
			{
				$record_limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit = $this->bo->total_records;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'activity_id',
						'source' => 'activity_id'
					),
					array
						(
						'name' => 'agreement_id',
						'source' => 'agreement_id'
					)
				)
			);

			$parameters2 = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'activity_id'
					),
					array
						(
						'name' => 'agreement_id',
						'source' => 'agreement_id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'prizing',
				'statustext' => lang('view edit the prize for this activity'),
				'text' => lang('prizing'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiagreement.edit_item',
					'from' => 'uipricebook.activity_vendor'
					)
				),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'delete',
				'statustext' => lang('delete this vendor from this activity'),
				'text' => lang('delete'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uipricebook.delete',
					'method' => 'activity_vendor'
					)
				),
				'parameters' => json_encode($parameters)
			);

			$this->save_sessiondata();

			self::render_template_xsl('datatable_jquery', $data);
		}

		function edit_activity()
		{
			if (!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 16, 'acl_location' => $this->acl_location));
			}

			$activity_id = phpgw::get_var('activity_id', 'int');
			$agreement_group = phpgw::get_var('agreement_group', 'int', 'GET');
			$values = phpgw::get_var('values');
			$values['ns3420_id'] = phpgw::get_var('ns3420_id');

			$tabs = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab = 'general';

			if (!$values['cat_id'])
			{
				$values['cat_id'] = $agreement_group;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('pricebook'));

			if ($values['save'])
			{
				if (!$values['num'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter an activity code !'));
					$error_id = true;
				}
				if (!$values['cat_id'])
				{
					$receipt['error'][] = array('msg' => lang('Please select an agreement_group !'));
				}

				if (!$values['branch_id'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a branch !'));
				}

				if ($values['num'] && !$activity_id)
				{
					if ($this->bo->check_activity_num($values['num'], $values['cat_id']))
					{
						$receipt['error'][] = array('msg' => lang('This activity code is already registered!') . '[ ' . $values['num'] . ' ]');
						$error_id = true;
					}
				}

				if ($activity_id)
				{
					$values['activity_id'] = $activity_id;
					$action = 'edit';
				}
				else
				{
					$activity_id = $values['activity_id'];
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->save_activity($values, $action);
					$activity_id = $receipt['activity_id'];
					$values['activity_id'] = $activity_id;
				}
			}
			else
			{
				$values['activity_id'] = $activity_id;
				if ($activity_id)
				{
					$values = $this->bo->read_single_activity($activity_id);
				}
			}

			//_debug_array($values);
			if ($activity_id)
			{
				$function_msg = lang('edit activity');
			}
			else
			{
				$function_msg = lang('add activity');
			}

			if ($values['cat_id'] > 0)
			{
				$this->cat_id = $values['cat_id'];
			}

			if ($error_id)
			{
				unset($values['num']);
			}

			$link_data = array
				(
				'menuaction' => 'property.uipricebook.edit_activity',
				'activity_id' => $activity_id,
				'agreement_group' => $agreement_group
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uipricebook.activity',
					'cat_id' => $values['cat_id'])),
				'lang_activity_id' => lang('Activity ID'),
				'lang_num' => lang('Activity code'),
				'lang_category' => lang('Agreement group'),
				'lang_unit' => lang('Unit'),
				'lang_save' => lang('save'),
				'lang_done' => lang('done'),
				'lang_descr' => lang('description'),
				'lang_base_descr' => lang('Base description'),
				'value_activity_id' => $values['activity_id'],
				'value_num' => $values['num'],
				'value_general_address' => $values['general_address'],
				'value_access' => $values['access'],
				'value_descr' => $values['descr'],
				'value_base_descr' => $values['base_descr'],
				'lang_num_statustext' => lang('An unique code for this activity'),
				'lang_done_statustext' => lang('Back to the list'),
				'lang_save_statustext' => lang('Save the building'),
				'lang_no_cat' => lang('Select agreement group'),
				'lang_cat_statustext' => lang('Select the agreement group this activity belongs to.'),
				'select_name' => 'values[cat_id]',
				'lang_descr_statustext' => lang('Enter the description for this activity'),
				'lang_base_descr_statustext' => lang('Enter a description for prerequisitions for this activity - if any'),
				'cat_list' => $this->bo->get_agreement_group_list('select', $values['cat_id']),
				'lang_dim_d' => lang('Dim D'),
				'dim_d_list' => $this->bo->get_dim_d_list($values['dim_d']),
				'select_dim_d' => 'values[dim_d]',
				'lang_no_dim_d' => lang('No Dim D'),
				'lang_dim_d_statustext' => lang('Select the Dim D for this activity. To do not use Dim D -  select NO DIM D'),
				'lang_unit' => lang('Unit'),
				'unit_list' => $this->bo->get_unit_list($values['unit']),
				'select_unit' => 'values[unit]',
				'lang_no_unit' => lang('Select Unit'),
				'lang_unit_statustext' => lang('Select the unit for this activity.'),
				'lang_branch' => lang('Branch'),
				'branch_list' => $this->bo->get_branch_list($values['branch_id']),
				'select_branch' => 'values[branch_id]',
				'lang_no_branch' => lang('Select branch'),
				'lang_branch_statustext' => lang('Select the branch for this activity.'),
				'ns3420_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uilookup.ns3420')),
				'lang_ns3420' => lang('NS3420'),
				'value_ns3420_id' => $values['ns3420_id'],
				'lang_ns3420_statustext' => lang('Select a standard-code from the norwegian standard'),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$appname = lang('pricebook');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_activity' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if (!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 16, 'acl_location' => $this->acl_location));
			}

			$method = phpgw::get_var('method');
			$activity_id = phpgw::get_var('activity_id', 'int');
			$agreement_id = phpgw::get_var('agreement_id', 'int', 'GET');
			$index_count = phpgw::get_var('index_count', 'int', 'GET');
			$agreement_group_id = phpgw::get_var('agreement_group_id', 'int');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			if ($method == 'activity_vendor')
			{
				$link_data = array
					(
					'menuaction' => 'property.uipricebook.activity_vendor',
					'activity_id' => $activity_id
				);

				$function_msg = lang('delete vendor activity');
				$delete_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uipricebook.delete',
					'method' => $method, 'activity_id' => $activity_id, 'agreement_id' => $agreement_id));

				if (phpgw::get_var('confirm', 'bool', 'POST'))
				{
					$this->bo->delete_activity_vendor($activity_id, $agreement_id);
					$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
				}
			}
			elseif ($method == 'activity')
			{
				//delete with JSON
				if (phpgw::get_var('phpgw_return_as') == 'json')
				{
					$function_msg = lang('delete activity');
					$this->bo->delete_activity($activity_id);
					return $function_msg;
				}

				$link_data = array
					(
					'menuaction' => 'property.uipricebook.activity'
				);

				//$function_msg	=lang('delete activity');
				$delete_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uipricebook.delete',
					'method' => $method, 'activity_id' => $activity_id));

				if (phpgw::get_var('confirm', 'bool', 'POST'))
				{
					$this->bo->delete_activity($activity_id);
					$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
				}
			}
			elseif ($method == 'prize')
			{
				$link_data = array
					(
					'menuaction' => 'property.uipricebook.prizing',
					'activity_id' => $activity_id,
					'agreement_id' => $agreement_id
				);

				$function_msg = lang('delete prize-index');
				$delete_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uipricebook.delete',
					'method' => $method, 'activity_id' => $activity_id, 'agreement_id' => $agreement_id,
					'index_count' => $index_count));

				if (phpgw::get_var('confirm', 'bool', 'POST'))
				{
					$this->bo->delete_prize_index($activity_id, $agreement_id, $index_count);
					$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
				}
			}
			elseif ($method == 'agreement_group')
			{

				//JsonCod for Delete
				if (phpgw::get_var('phpgw_return_as') == 'json')
				{
					$function_msg = lang('Delete agreement group and all the activities associated with it!');
					$this->bo->delete_agreement_group($agreement_group_id);
					return $function_msg;
				}

				$link_data = array
					(
					'menuaction' => 'property.uipricebook.agreement_group',
					'start' => $this->start
				);


				$delete_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uipricebook.delete',
					'method' => $method, 'agreement_group_id' => $agreement_group_id, 'start' => $this->start));

				if (phpgw::get_var('confirm', 'bool', 'POST'))
				{
					$this->bo->delete_agreement_group($agreement_group_id);
					$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
				}
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $delete_action,
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('pricebook');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}
	}