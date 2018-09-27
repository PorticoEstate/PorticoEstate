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

	class property_uiagreement extends phpgwapi_uicommon_jquery
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
			'view' => true,
			'edit' => true,
			'delete' => true,
			'columns' => true,
			'edit_item' => true,
			'view_item' => true,
			'view_file' => true,
			'download' => true,
			'add_activity' => true,
			'save' => true,
			'get_content' => true,
			'deleteitem' => true,
			'get_contentalarm' => true,
			'edit_alarm' => true,
			'get_contentitem' => true,
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo = CreateObject('property.boagreement', true);
			$this->bocommon = CreateObject('property.bocommon');

			$this->role = $this->bo->role;

			$this->cats = CreateObject('phpgwapi.categories', -1, 'property', '.vendor');

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.agreement';

			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage = $this->acl->check($this->acl_location, 16, 'property');

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->cat_id = $this->bo->cat_id;
			$this->vendor_id = $this->bo->vendor_id;
			$this->allrows = $this->bo->allrows;
			$this->member_id = $this->bo->member_id;
			$this->status_id = $this->bo->status_id;
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
				'vendor_id' => $this->vendor_id,
				'allrows' => $this->allrows,
				'member_id' => $this->member_id,
				'status_id' => $this->status_id
			);
			$this->bo->save_sessiondata($data);
		}

		function columns()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values = phpgw::get_var('values');
			$receipt = array();

			if ($values['save'])
			{

				$GLOBALS['phpgw']->preferences->account_id = $this->account;
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('property', 'agreement_columns', $values['columns'], 'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg = lang('Select Column');

			$link_data = array
				(
				'menuaction' => 'property.uiagreement.columns',
				'role' => $this->role
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list' => $this->bo->column_list($values['columns'], $allrows = true),
				'function_msg' => $function_msg,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_columns' => lang('columns'),
				'lang_none' => lang('None'),
				'lang_save' => lang('save'),
				'select_name' => 'period'
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('columns' => $data));
		}

		function view_file()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			ExecMethod('property.bofiles.get_file', phpgw::get_var('file_id', 'int'));
		}

		private function _get_Filters()
		{
			$values_combo_box = array();
			$combos = array();
			$link_data = array
				(
				'menuaction' => 'property.uiagreement.index',
				'sort' => $this->sort,
				'order' => $this->order,
				'cat_id' => $this->cat_id,
				'filter' => $this->filter,
				'query' => $this->query,
				'role' => $this->role,
				'member_id' => $this->member_id,
				'status_id' => $this->status_id
			);

			$values_combo_box[0] = $this->cats->formatted_xslt_list(array('selected' => $this->member_id,
				'globals' => true, 'link_data' => $link_data));

			$default_value = array('id' => '', 'name' => lang('no member'));
			foreach ($values_combo_box[0]['cat_list'] as &$list)
			{

				$list['id'] = $list['cat_id'];
			}
			array_unshift($values_combo_box[0]['cat_list'], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'member_id',
				'text' => lang('Member'),
				'list' => $values_combo_box[0]['cat_list']
			);

			$values_combo_box[1] = $this->bocommon->select_category_list(array('format' => 'filter',
				'selected' => $this->cat_id, 'type' => 'branch', 'order' => 'descr'));
			$default_value = array('id' => '', 'name' => lang('no category'));
			array_unshift($values_combo_box[1], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'cat_id',
				'text' => lang('Category'),
				'list' => $values_combo_box[1]
			);

			$values_combo_box[2] = $this->bo->select_vendor_list('filter', $this->vendor_id);
			$default_value = array('id' => '', 'name' => lang('no vendor'));
			array_unshift($values_combo_box[2], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'vendor_id',
				'text' => lang('Vendor'),
				'list' => $values_combo_box[2]
			);

			$values_combo_box[3] = $this->bo->select_status_list('filter', $this->status_id);
			$default_value = array('id' => '', 'name' => lang('no status'));
			array_unshift($values_combo_box[3], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'status_id',
				'text' => lang('Status'),
				'list' => $values_combo_box[3]
			);

			return $combos;
		}

		function index()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', 'agreement_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data', 'agreement_receipt', '');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::agreement::pricebook::agreement';

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname = lang('agreement');
			$function_msg = lang('List') . ' ' . lang($this->role);

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
						'menuaction' => 'property.uiagreement.index',
						'sort' => $this->sort,
						'order' => $this->order,
						'cat_id' => $this->cat_id,
						'filter' => $this->filter,
						'query' => $this->query,
						'role' => $this->role,
						'member_id' => $this->member_id,
						'status_id' => $this->status_id,
						'phpgw_return_as' => 'json'
					)),
					"columns" => array('onclick' => "JqueryPortico.openPopup({menuaction:'property.uiagreement.columns', role:'{$this->role}'},{closeAction:'reload'})"),
					'new_item' => self::link(array(
						'menuaction' => 'property.uiagreement.edit',
						'role' => $this->role
					)),
					'download' => self::link(array(
						'menuaction' => 'property.uiagreement.download',
						'status_id' => $this->status_id,
						'allrows' => true
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$filters = $this->_get_Filters();
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$list = $this->bo->read(array('dry_run' => true));
			$uicols = $this->bo->uicols;
			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array
					(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? false : true,
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
						'name' => 'id',
						'source' => 'id'
					),
				)
			);

			$parameters2 = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'agreement_id',
						'source' => 'id'
					),
				)
			);

			if ($this->acl_read)
			{
				$data['datatable']['actions'][] = array(
					'my_name' => 'view',
					'statustext' => lang('view this entity'),
					'text' => lang('view'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uiagreement.view',
						'role' => $this->role
					)),
					'parameters' => json_encode($parameters)
				);

				$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

				foreach ($jasper as $report)
				{
					$data['datatable']['actions'][] = array(
						'my_name' => 'edit',
						'text' => lang('open JasperReport %1 in new window', $report['title']),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uijasper.view',
							'jasper_id' => $report['id'],
							'target' => '_blank'
						)),
						'parameters' => json_encode($parameters)
					);
				}
			}

			if ($this->acl_edit)
			{
				$data['datatable']['actions'][] = array(
					'my_name' => 'edit',
					'statustext' => lang('edit this entity'),
					'text' => lang('edit'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uiagreement.edit',
						'role' => $this->role
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_delete)
			{
				$data['datatable']['actions'][] = array(
					'my_name' => 'delete',
					'statustext' => lang('delete this entity'),
					'text' => lang('delete'),
					'confirm_msg' => lang('do you really want to delete this entry'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uiagreement.delete',
						'role' => $this->role
					)),
					'parameters' => json_encode($parameters2)
				);
			}

			unset($parameters);
			unset($parameters2);

			self::render_template_xsl('datatable_jquery', $data);
		}

		private function _get_params()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export = phpgw::get_var('export', 'bool');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'filter' => $this->filter,
				'cat_id' => $this->cat_id,
				'member_id' => $this->member_id,
				'vendor_id' => $this->vendor_id,
				'status_id' => $this->status_id
			);
			
			return $params;
			
		}

		public function query()
		{
			$export = phpgw::get_var('export', 'bool');

			$result_objects = array();
			$result_count = 0;

			$params = $this->_get_params();
			$values = $this->bo->read($params);

			if ($export)
			{
				return $values;
			}

			$result_data = array('results' => $values);
			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = phpgw::get_var('draw', 'int');

			return $this->jquery_results($result_data);
		}

		function list_content( $list, $uicols, $edit_item = '', $view_only = '' )
		{
			$j = 0;
			//_debug_array($list);
			if (isset($list) AND is_array($list))
			{
				foreach ($list as $entry)
				{
					$content[$j]['id'] = $entry['id'];
					$content[$j]['activity_id'] = $entry['activity_id'];
					$content[$j]['index_count'] = $entry['index_count'];
					$content[$j]['m_cost'] = $entry['m_cost'];
					$content[$j]['w_cost'] = $entry['w_cost'];
					$content[$j]['total_cost'] = $entry['total_cost'];
					$content[$j]['index_count'] = $entry['index_count'];
					for ($i = 0; $i < count($uicols['name']); $i++)
					{
						if ($uicols['input_type'][$i] != 'hidden')
						{
							$content[$j]['row'][$i]['value'] = $entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name'] = $uicols['name'][$i];
						}
					}

					if ($this->acl_read && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext'] = lang('view the entity');
						$content[$j]['row'][$i]['text'] = lang('view');
						$content[$j]['row'][$i++]['link'] = $GLOBALS['phpgw']->link('/index.php', array(
							'menuaction' => 'property.uiagreement.view_item', 'agreement_id' => $entry['agreement_id'],
							'id' => $entry['id']));
					}
					if ($this->acl_edit && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext'] = lang('edit the agreement');
						$content[$j]['row'][$i]['text'] = lang('edit');
						$content[$j]['row'][$i++]['link'] = $GLOBALS['phpgw']->link('/index.php', array(
							'menuaction' => 'property.uiagreement.edit_item', 'agreement_id' => $entry['agreement_id'],
							'id' => $entry['id']));
					}
					if ($this->acl_delete && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext'] = lang('delete this item');
						$content[$j]['row'][$i]['text'] = lang('delete');
						$content[$j]['row'][$i++]['link'] = $GLOBALS['phpgw']->link('/index.php', array(
							'menuaction' => 'property.uiagreement.edit', 'delete_item' => 1, 'agreement_id' => $entry['agreement_id'],
							'activity_id' => $entry['id']));
					}

					$j++;
				}
			}

			//html_print_r($content);
			for ($i = 0; $i < count($uicols['descr']); $i++)
			{
				if ($uicols['input_type'][$i] != 'hidden')
				{
					$table_header[$i]['header'] = $uicols['descr'][$i];
					$table_header[$i]['width'] = '5%';
					$table_header[$i]['align'] = 'center';
				}
			}

			if ($this->acl_read && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] = '5%';
				$table_header[$i]['align'] = 'center';
				$table_header[$i]['header'] = lang('view');
				$i++;
			}
			if ($this->acl_edit && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] = '5%';
				$table_header[$i]['align'] = 'center';
				$table_header[$i]['header'] = lang('edit');
				$i++;
			}
			if ($this->acl_delete && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] = '5%';
				$table_header[$i]['align'] = 'center';
				$table_header[$i]['header'] = lang('delete');
				$i++;
			}
			if ($this->acl_manage && !$edit_item && !$view_only)
			{
				$table_header[$i]['width'] = '5%';
				$table_header[$i]['align'] = 'center';
				$table_header[$i]['header'] = lang('Update');
				$i++;
			}

			return array('content' => $content, 'table_header' => $table_header);
		}

		function add_activity()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 2, 'acl_location' => $this->acl_location));
			}


			$agreement_id = phpgw::get_var('agreement_id', 'int');
			$group_id = phpgw::get_var('group_id', 'int');
			$values = phpgw::get_var('values');

//			$GLOBALS['phpgw']->xslttpl->add_file(array('agreement'));

			$agreement = $this->bo->read_single(array('agreement_id' => $agreement_id));
			$tabs = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab = 'general';

			if ($this->acl_add && (is_array($values)))
			{
				if ($values['save'] || $values['apply'])
				{
					$receipt = $this->bo->add_activity($values, $agreement_id);

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data', 'agreement_receipt', $receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiagreement.edit',
							'id' => $agreement_id, 'tab' => 'items'));
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiagreement.edit',
						'id' => $agreement_id, 'tab' => 'items'));
				}
			}

			$content = $this->bo->read_group_activity($group_id, $agreement_id);
			//_debug_array($content);
			$uicols = $this->bo->uicols;

			$uicols['name'][] = 'select';
			$uicols['descr'][] = lang('select');

			for ($i = 0; $i < count($uicols['descr']); $i++)
			{
				if ($uicols['input_type'][$i] != 'hidden')
				{
					$table_header[$i]['key'] = $uicols['name'][$i];
					$table_header[$i]['label'] = $uicols['descr'][$i];
					$table_header[$i]['className'] = 'center';
				}
			}

			for ($z = 0; $z < count($content); $z++)
			{
				$content[$z]['select'] = '<input type="checkbox" name="values[select][]" class="mychecks" value="' . $content[$z]['id'] . '" title=""/>';
			}

			$tabletools = array
				(
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'data' => json_encode($content),
				'tabletools' => $tabletools,
				'ColumnDefs' => $table_header,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);
			self::add_javascript('property', 'core', 'check.js');

			$data = array
				(
				'datatable_def' => $datatable_def,
				'lang_id' => lang('ID'),
				'value_agreement_id' => $agreement_id,
				'lang_name' => lang('name'),
				'value_name' => $agreement['name'],
				'lang_descr' => lang('descr'),
				'value_descr' => $agreement['descr'],
				'lang_select_all' => lang('Select All'),
				'img_check' => $GLOBALS['phpgw']->common->get_image_path('property') . '/check.png',
				'add_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.add_activity',
					'group_id' => $group_id, 'agreement_id' => $agreement_id)),
				'agreement_id' => $agreement_id,
				'table_header' => $table_header,
				'values' => $content,
				'lang_save' => lang('save'),
				'lang_cancel' => lang('cancel'),
				'lang_apply' => lang('apply'),
				'lang_apply_statustext' => lang('Apply the values'),
				'lang_cancel_statustext' => lang('Leave the agreement untouched and return back to the list'),
				'lang_save_statustext' => lang('Save the agreement and return back to the list'),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . lang('add activity');
			//_debug_array($data);
			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');
//			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add_activity' => $data));
			self::render_template_xsl(array('agreement', 'datatable_inline', 'attributes_form'), array(
				'add_activity' => $data));
		}

		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}

			$id = phpgw::get_var('id', 'int');
			$values = phpgw::get_var('values');

			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{

				$values['vendor_id'] = phpgw::get_var('vendor_id', 'int', 'POST');
				$values['vendor_name'] = phpgw::get_var('vendor_name', 'string', 'POST');

				$values_attribute = phpgw::get_var('values_attribute');

				$insert_record_agreement = $GLOBALS['phpgw']->session->appsession('insert_record_values.agreement', 'property');
				if (isset($insert_record_agreement) && is_array($insert_record_agreement))
				{
					for ($j = 0; $j < count($insert_record_agreement); $j++)
					{
						$insert_record['extra'][$insert_record_agreement[$j]] = $insert_record_agreement[$j];
					}
				}
				if (isset($insert_record['extra']) && is_array($insert_record['extra']))
				{
					foreach ($insert_record['extra'] as $key => $column)
					{
						if ($_POST[$key])
						{
							$values['extra'][$column] = phpgw::get_var($key, 'string', 'POST');
						}
					}
				}

				if (!$values['cat_id'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a category !'));
				}
				if (!$values['name'])
				{
					$receipt['error'][] = array('msg' => lang('please enter a name !'));
				}
				if (!$values['descr'])
				{
					$receipt['error'][] = array('msg' => lang('please enter a description!'));
				}
				if (!$values['start_date'])
				{
					$receipt['error'][] = array('msg' => lang('please enter a start date!'));
				}
				if (!$values['end_date'])
				{
					$receipt['error'][] = array('msg' => lang('please enter a end date!'));
				}

				if ($id)
				{
					$values['agreement_id'] = $id;
					$action = 'edit';
				}
				else
				{
					$values['agreement_id'] = $this->bo->request_next_id();
				}

				$bofiles = CreateObject('property.bofiles');
				if (isset($id) && $id && isset($values['file_action']) && is_array($values['file_action']))
				{
					$bofiles->delete_file("/agreement/{$id}/", $values);
				}

				$values['file_name'] = str_replace(' ', '_', $_FILES['file']['name']);
				$to_file = "{$bofiles->fakebase}/agreement/{$values['agreement_id']}/{$values['file_name']}";

				if (!$values['document_name_orig'] && $bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					$receipt['error'][] = array('msg' => lang('This file already exists !'));
				}


				if (!$receipt['error'])
				{
					try
					{

						$receipt = $this->bo->save($values, $values_attribute, $action);
						$id = $receipt['agreement_id'];
						$this->cat_id = ($values['cat_id'] ? $values['cat_id'] : $this->cat_id);
						$msgbox_data = $this->bocommon->msgbox_data($receipt);

						if ($values['file_name'])
						{
							$bofiles->create_document_dir("agreement/{$id}");
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


						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', 'agreement_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiagreement.index',
								'role' => $this->role));
						}
					}
					catch (Exception $e)
					{

						if ($e)
						{
							phpgwapi_cache::message_set($e->getMessage(), 'error');
							$this->edit();
							return;
						}
					}

					self::message_set($receipt);
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiagreement.edit',
						'id' => $id));
				}
				else
				{
					self::message_set($receipt);
					if (isset($receipt['error']) && (isset($values_attribute) && is_array($values_attribute)))
					{
						$values = $this->bocommon->preserve_attribute_values($values, $values_attribute);
					}
					$this->edit($values);
				}
			}
			else
			{
				$this->edit($values);
			}
		}

		public function get_content()
		{

			$agreement_id = phpgw::get_var('agreement_id', 'int');
			if (!$agreement_id)
			{
				$result_data = array('results' => array());
				$result_data['total_records'] = 0;
				$result_data['draw'] = 0;

				return $this->jquery_results($result_data);
			}

			$year = phpgw::get_var('year', 'int');
			$draw = phpgw::get_var('draw', 'int');
			$order = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');

			$params = array(
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir']
			);

			$values = $this->bo->read_details($agreement_id, $params);

			$total_records = count($values);

			$result_data = array('results' => $values);

			$result_data['total_records'] = $total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		public function get_contentalarm()
		{
			$id = phpgw::get_var('id', 'int');
			$acl_location = phpgw::get_var('acl_location');
			$times = phpgw::get_var('times');
			$method = phpgw::get_var('method');
			$data = phpgw::get_var('data');
			$account_id = phpgw::get_var('account_id');

			if (!$id)
			{
				$result_data = array('results' => array());
				$result_data['total_records'] = 0;
				$result_data['draw'] = 0;

				return $this->jquery_results($result_data);
			}

			$params = array
				(
				'acl_location' => $acl_location,
				'alarm_type' => 'agreement',
				'type' => 'form',
				'text' => 'Email notification',
				'times' => $times,
				'id' => $id,
				'method' => $method,
				'data' => $data,
				'account_id' => $account_id
			);

			$values = $this->bocommon->initiate_ui_alarm($params);
			$total_records = count($values['values']);

			$result_data = array('results' => $values['values']);

			$result_data['total_records'] = $total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function deleteitem()
		{
			$activity_id = phpgw::get_var('id', 'int');
			$id = phpgw::get_var('agreement_id', 'int');

			if ($id && $activity_id)
			{
				$this->bo->delete_item($id, $activity_id);
				$get_items = true;
			}
		}

		function edit_alarm()
		{
			$boalarm = CreateObject('property.boalarm');
			$ids_alarm = !empty($_POST['ids']) ? $_POST['ids'] : '';
			$type_alarm = !empty($_POST['type']) ? $_POST['type'] : '';

			//Add Alarm
			$idAgreement = !empty($_POST['id']) ? $_POST['id'] : '';
			$day = !empty($_POST['day']) ? $_POST['day'] : '';
			$hour = !empty($_POST['hour']) ? $_POST['hour'] : '';
			$minute = !empty($_POST['minute']) ? $_POST['minute'] : '';
			$user_list = !empty($_POST['user_list']) ? $_POST['user_list'] : '';

			//Update Index and Date
			$date = !empty($_POST['date']) ? $_POST['date'] : '';
			$index = !empty($_POST['index']) ? $_POST['index'] : '';
			$mcosto = !empty($_POST['mcost']) ? $_POST['mcost'] : '';
			$wcosto = !empty($_POST['wcost']) ? $_POST['wcost'] : '';
			$tcosto = !empty($_POST['tcost']) ? $_POST['tcost'] : '';
			$icount = !empty($_POST['icoun']) ? $_POST['icoun'] : '';


			$requestUrl_Alarm = json_encode(self::link(array(
					'menuaction' => 'property.uiagreement.get_contentalarm',
					'id' => $idAgreement,
					'acl_location' => $this->acl_location,
					'times' => isset($times) ? $times : '',
					'method' => isset($method) ? $method : '',
					'data' => isset($data) ? $data : '',
					'account_id' => isset($account_id) ? $account_id : '',
					'phpgw_return_as' => 'json'
					)
				)
			);

			$receipt = array();

			if (!empty($type_alarm))
			{

				if ($type_alarm == 'update' || $type_alarm == 'update_item')
				{
					$values = array(
						'select' => $ids_alarm,
						'agreement_id' => $idAgreement,
						'id' => $icount,
						'new_index' => $index,
						'm_cost' => $mcosto,
						'w_cost' => $wcosto,
						'total_cost' => $tcosto,
						'date' => $date,
					);

					$receipt = $this->bo->update($values);

					if ($type_alarm == 'update')
					{
						$requestUrl = json_encode(self::link(array('menuaction' => 'property.uiagreement.get_content',
								'agreement_id' => $idAgreement, 'phpgw_return_as' => 'json')));
					}
					else
					{
						$requestUrl = json_encode(self::link(array('menuaction' => 'property.uiagreement.get_contentitem',
								'agreement_id' => $idAgreement, 'activity_id' => $ids_alarm[0], 'phpgw_return_as' => 'json')));
					}
					return $requestUrl;
				}
				else if (($type_alarm == 'delete_alarm' || $type_alarm == 'delete_item' ) && count($ids_alarm))
				{
					if ($type_alarm == 'delete_alarm')
					{
						$boalarm->delete_alarm('agreement', $ids_alarm);
					}
					else
					{
						$this->bo->delete_last_index($idAgreement, $ids_alarm);
						$requestUrl = json_encode(self::link(array('menuaction' => 'property.uiagreement.get_contentitem',
								'agreement_id' => $idAgreement, 'activity_id' => $ids_alarm, 'role' => null,
								'phpgw_return_as' => 'json')));
						return $requestUrl;
					}
				}
				else if (($type_alarm == 'disable_alarm' || $type_alarm == 'enable_alarm' ) && count($ids_alarm))
				{
					$type_alarm = ($type_alarm == 'enable_alarm') ? $type_alarm : '';
					$boalarm->enable_alarm('agreement', $ids_alarm, $type_alarm);
				}
				else if ($type_alarm == 'add_alarm')
				{
					$time = intval($day) * 24 * 3600 + intval($hour) * 3600 + intval($minute) * 60;

					if ($time > 0)
					{
						$boalarm->add_alarm('agreement', $this->bo->read_event(array('agreement_id' => $idAgreement)), $time, $user_list);
					}

					return $requestUrl_Alarm;
				}
			}
		}

		function edit( $values = array(), $mode = 'edit' )
		{
			$mode = ($mode == 'edit') ? 2 : 1;
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => $mode, 'acl_location' => $this->acl_location));
			}

			$id = isset($values['id']) && $values['id'] ? $values['id'] : phpgw::get_var('id', 'int');

			$config = CreateObject('phpgwapi.config', 'property');
			$get_items = false;


			$tabs = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab = 'general';
			$tabs['items'] = array('label' => lang('items'), 'link' => "#items");

			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_termination_date');

			if ($id)
			{
				$agreement = $this->bo->read_single(array('agreement_id' => $id));
				if($values)
				{
					$agreement = array_merge($agreement, $values);
				}
				$list = $this->bo->read_details($id);

				$content = $list;

				$uicols = $this->bo->uicols;

				for ($i = 0; $i < count($uicols['descr']); $i++)
				{
					if ($uicols['input_type'][$i] != 'hidden')
					{
						$table_header[$i]['header'] = $uicols['descr'][$i];
						$table_header[$i]['width'] = '5%';
						$table_header[$i]['align'] = 'center';
					}
				}

				if (isset($content) && is_array($content))
				{
					$GLOBALS['phpgw']->jqcal->add_listener('values_date');
					$table_update[] = array
						(
						'lang_new_index' => lang('New index'),
						'lang_new_index_statustext' => lang('Enter a new index'),
						'lang_date_statustext' => lang('Select the date for the update'),
						'lang_update' => lang('Update'),
						'lang_update_statustext' => lang('update selected investments')
					);
				}
			}
			else
			{
				$agreement = $values;
			}
			$this->cat_id = ($agreement['cat_id'] ? $agreement['cat_id'] : $this->cat_id);
			$this->member_id = ($agreement['member_of'] ? $agreement['member_of'] : $this->member_id);

			$link_data = array
				(
				'menuaction' => 'property.uiagreement.save',
				'sort' => $this->sort,
				'order' => $this->order,
				'id' => $id,
				'role' => $this->role
			);

			$link_data_cancel = array
				(
				'menuaction' => 'property.uiagreement.index',
				'role' => $this->role
			);

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id' => $agreement['vendor_id'],
				'vendor_name' => isset($agreement['vendor_name']) ? $agreement['vendor_name'] : '',
				'required'	=> true
				)
			);

			if ($agreement['vendor_id'])
			{
				$member_of_data = $this->cats->formatted_xslt_list(array('selected' => $this->member_id,
					'globals' => true, 'link_data' => array()));
			}

			$alarm_data = $this->bocommon->initiate_ui_alarm(array(
				'acl_location' => $this->acl_location,
				'alarm_type' => 'agreement',
				'type' => 'form',
				'text' => 'Email notification',
				'times' => isset($times) ? $times : '',
				'id' => $id,
				'method' => isset($method) ? $method : '',
				'data' => isset($data) ? $data : '',
				'account_id' => isset($account_id) ? $account_id : ''
			));

			$table_add[] = array
				(
				'lang_add' => lang('add detail'),
				'lang_add_standardtext' => lang('add an item to the details'),
				'add_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.add_activity',
					'agreement_id' => $id, 'group_id' => $agreement['group_id']))
			);


			$link_file_data = array
				(
				'menuaction' => 'property.uiagreement.view_file',
				'id' => $id
			);

			if (isset($agreement['files']) && is_array($agreement['files']))
			{
				$j = count($agreement['files']);
				for ($i = 0; $i < $j; $i++)
				{
					$agreement['files'][$i]['file_name'] = urlencode($agreement['files'][$i]['name']);
				}
			}

			$link_download = array
				(
				'menuaction' => 'property.uiagreement.download',
				'id' => $id,
				'allrows' => $this->allrows
			);


			if (!$this->allrows)
			{
				$record_limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit = $this->bo->total_records;
			}

			self::add_javascript('property', 'overlib', 'overlib.js');
			self::add_javascript('property', 'core', 'check.js');

			if (isset($agreement['attributes']) && is_array($agreement['attributes']))
			{
				$location = $this->acl_location;
				$attributes_groups = $this->bo->get_attribute_groups($location, $agreement['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if (isset($group['attributes']))
					{
						$attributes[] = $group;
					}
				}
				unset($attributes_groups);
				unset($agreement['attributes']);
			}

			//---datatable0 settings---------------------------------------------------
			$requestUrl_Alarm = json_encode(self::link(array(
					'menuaction' => 'property.uiagreement.get_contentalarm',
					'id' => $id,
					'acl_location' => $this->acl_location,
					'times' => isset($times) ? $times : '',
					'method' => isset($method) ? $method : '',
					'data' => isset($data) ? $data : '',
					'account_id' => isset($account_id) ? $account_id : '',
					'phpgw_return_as' => 'json'
					)
				)
			);


			$tabletools = array
				(
				array(
					'my_name' => 'enable_alarm',
					'text' => lang($alarm_data[alter_alarm][0][lang_enable]),
					'type' => 'custom',
					'custom_code' => "
										var api = oTable0.api();
										var selected = api.rows( { selected: true } ).data();
										var numSelected = 	selected.length;

										if (numSelected ==0){
											alert('None selected');
											return false;
										}
										var ids = [];
										for ( var n = 0; n < selected.length; ++n )
										{
											var aData = selected[n];
											ids.push(aData['id']);
										}
										onActionsClick_notify('enable_alarm', ids , {$requestUrl_Alarm} );"
//                                        JqueryPortico.updateinlineTableHelper(oTable0, );"
				),
				array(
					'my_name' => 'disable_alarm',
					'text' => lang($alarm_data[alter_alarm][0][lang_disable]),
					'type' => 'custom',
					'custom_code' => "
										var api = oTable0.api();
										var selected = api.rows( { selected: true } ).data();

										var numSelected = 	selected.length;

										if (numSelected ==0){
											alert('None selected');
											return false;
										}
										var ids = [];
										for ( var n = 0; n < selected.length; ++n )
										{
											var aData = selected[n];
											ids.push(aData['id']);
										}
										onActionsClick_notify('disable_alarm', ids , {$requestUrl_Alarm});"
//                                        JqueryPortico.updateinlineTableHelper(oTable0, );"
				),
				array(
					'my_name' => 'delete_alarm',
					'text' => lang($alarm_data[alter_alarm][0][lang_delete]),
					'type' => 'custom',
					'custom_code' => "
										var api = oTable0.api();
										var selected = api.rows( { selected: true } ).data();

										var numSelected = 	selected.length;

										if (numSelected ==0){
											alert('None selected');
											return false;
										}
										var ids = [];
										for ( var n = 0; n < selected.length; ++n )
										{
											var aData = selected[n];
											ids.push(aData['id']);
										}
										onActionsClick_notify('delete_alarm', ids,{$requestUrl_Alarm});"
//                                        JqueryPortico.updateinlineTableHelper(oTable0, {$requestUrl_Alarm});"
				)
			);


			$myColumnDefs0 = array
				(
				array('key' => 'time', 'label' => $alarm_data['header'][0]['lang_time'], 'sortable' => true,
					'resizeable' => true, 'width' => 140),
				array('key' => 'text', 'label' => $alarm_data['header'][0]['lang_text'], 'sortable' => true,
					'resizeable' => true, 'width' => 340),
				array('key' => 'user', 'label' => $alarm_data['header'][0]['lang_user'], 'sortable' => true,
					'resizeable' => true, 'width' => 200),
				array('key' => 'enabled', 'label' => $alarm_data['header'][0]['lang_enabled'],
					'sortable' => true, 'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter',
					'width' => 60),
				array('key' => 'alarm_id', 'label' => "dummy", 'sortable' => true, 'resizeable' => true,
					'hidden' => true),
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => $requestUrl_Alarm,
				'data' => json_encode(array()),
				'tabletools' => $tabletools,
				'ColumnDefs' => $myColumnDefs0,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			//---datatable1 settings---------------------------------------------------
			$parameters['view'] = array('parameter' => array(
					array('name' => 'agreement_id', 'source' => 'agreement_id'),
					array('name' => 'id', 'source' => 'id')));

			$parameters['edit'] = array('parameter' => array(
					array('name' => 'agreement_id', 'source' => 'agreement_id'),
					array('name' => 'id', 'source' => 'id')));

			$parameters['delete'] = array('parameter' => array(
					array('name' => 'delete_item', 'source' => 1, 'ready' => 1),
					array('name' => 'id', 'source' => 'agreement_id'),
					array('name' => 'activity_id', 'source' => 'activity_id')));

			$permission_update = false;
			if ($this->acl_read && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
			{
				$permissions['rowactions'][] = array(
					'text' => lang('view'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.view_item')),
					'parameters' => $parameters['view']
				);
			}
			if ($this->acl_edit && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
			{
				$permissions['rowactions'][] = array(
					'text' => lang('edit'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.edit_item')),
					'parameters' => $parameters['edit']
				);
			}
			if ($this->acl_delete && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
			{
				$permissions['rowactions'][] = array(
					'text' => lang('delete'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.edit')),
					'confirm_msg' => lang('do you really want to delete this entry'),
					'parameters' => $parameters['delete']
				);
			}
			if ($this->acl_manage && (!isset($edit_item) || !$edit_item) && (!isset($view_only) || !$view_only))
			{
				$permission_update = true;
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'activity_id'
					),
				)
			);

			$tabletools = array
				(
				array('my_name' => 'view', 'text' => lang('View'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.view_item',
						'agreement_id' => $id)), 'parameters' => json_encode($parameters)),
				array('my_name' => 'edit', 'text' => lang('Edit'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.edit_item',
						'agreement_id' => $id)), 'parameters' => json_encode($parameters)),
				array('my_name' => 'delete', 'text' => lang('Delete'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.deleteitem',
						'agreement_id' => $id)), 'parameters' => json_encode($parameters)),
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			$myColumnDefs1 = array
				(
				array('key' => 'id', 'label' => $table_header[0]['header'], 'sortable' => false,
					'resizeable' => true),
				array('key' => 'num', 'label' => $table_header[1]['header'], 'sortable' => false,
					'resizeable' => true),
				array('key' => 'descr', 'label' => $table_header[2]['header'], 'sortable' => false,
					'resizeable' => true),
				array('key' => 'unit_name', 'label' => $table_header[3]['header'], 'sortable' => false,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter'),
				array('key' => 'm_cost', 'label' => $table_header[4]['header'], 'sortable' => false,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterRight'),
				array('key' => 'w_cost', 'label' => $table_header[5]['header'], 'sortable' => false,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterRight'),
				array('key' => 'total_cost', 'label' => $table_header[6]['header'], 'sortable' => false,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterRight'),
				array('key' => 'this_index', 'label' => $table_header[7]['header'], 'sortable' => false,
					'resizeable' => true),
				array('key' => 'index_count', 'label' => $table_header[8]['header'], 'sortable' => false,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter'),
				array('key' => 'index_date', 'label' => $table_header[9]['header'], 'sortable' => false,
					'resizeable' => true),
//                $permission_update?array('key' => 'select',	'label' =>$table_header[13]['header'], 'sortable' => false, 'resizeable' => false, 'formatter'=>FormatterCheckItems):"",
				array('key' => 'activity_id', 'hidden' => true),
				array('key' => 'agreement_id', 'hidden' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiagreement.get_content',
						'agreement_id' => $id, 'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'tabletools' => $tabletools,
				'ColumnDefs' => $myColumnDefs1,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			for ($z = 0; $z < count($agreement['files']); $z++)
			{
				$content_files[$z]['file_name'] = '<a href="' . $link_view_file . '&amp;file_id=' . $agreement['files'][$z]['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $agreement['files'][$z]['name'] . '</a>';
				$content_files[$z]['delete_file'] = '<input type="checkbox" name="values[file_action][]" value="' . $agreement['files'][$z]['file_id'] . '" title="' . lang('Check to delete file') . '">';
			}

			$myColumnDefs2 = array
				(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'delete_file', 'label' => lang('Delete file'), 'sortable' => false,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter')
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => "''",
				'data' => json_encode($content_files),
				'ColumnDefs' => $myColumnDefs2,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$indice = array('id' => '0');
			array_unshift($alarm_data['add_alarm']['day_list'], $indice);
			array_unshift($alarm_data['add_alarm']['hour_list'], $indice);
			array_unshift($alarm_data['add_alarm']['minute_list'], $indice);
			//----------------------------------------------datatable settings--------

			$data = array
				(
				'datatable_def' => $datatable_def,
				'base_java_url' => json_encode(array(menuaction => "property.uiagreement.edit",
					id => $id)),
				'allow_allrows' => true,
				'allrows' => $this->allrows,
				'start_record' => $this->start,
				'record_limit' => $record_limit,
				'num_records' => count($list),
				'all_records' => $this->bo->total_records,
				'link_url' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'img_path' => $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
				'alarm_data' => $alarm_data,
				'lang_alarm' => lang('Alarm'),
				'lang_download' => 'download',
				'link_download' => $GLOBALS['phpgw']->link('/index.php', $link_download),
				'lang_download_help' => lang('Download table to your browser'),
				'fileupload' => true,
				'link_view_file' => $GLOBALS['phpgw']->link('/index.php', $link_file_data),
				'files' => isset($agreement['files']) ? $agreement['files'] : '',
				'lang_files' => lang('files'),
				'lang_filename' => lang('Filename'),
				'lang_file_action' => lang('Delete file'),
				'lang_view_file_statustext' => lang('click to view file'),
				'lang_file_action_statustext' => lang('Check to delete file'),
				'lang_upload_file' => lang('Upload file'),
				'lang_file_statustext' => lang('Select file to upload'),
				'edit_url' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', $link_data_cancel),
				'lang_id' => lang('ID'),
				'value_agreement_id' => $id,
				'lang_category' => lang('category'),
				'lang_save' => lang('save'),
				'lang_cancel' => lang('cancel'),
				'lang_apply' => lang('apply'),
				'value_cat' => isset($agreement['cat']) ? $agreement['cat'] : '',
				'lang_apply_statustext' => lang('Apply the values'),
				'lang_cancel_statustext' => lang('Leave the agreement untouched and return back to the list'),
				'lang_save_statustext' => lang('Save the agreement and return back to the list'),
				'lang_no_cat' => lang('no category'),
				'lang_cat_statustext' => lang('Select the category the agreement belongs to. To do not use a category select NO CATEGORY'),
				'select_name' => 'values[cat_id]',
				'cat_list' => $this->bocommon->select_category_list(array('format' => 'select',
					'selected' => $this->cat_id, 'type' => 'branch', 'order' => 'descr')),
				'lang_member_of' => lang('member of'),
				'member_of_name' => 'member_id',
				'member_of_list' => $member_of_data['cat_list'],
				'attributes_group' => $attributes,
				'lookup_functions' => isset($agreement['lookup_functions']) ? $agreement['lookup_functions'] : '',
				'dateformat' => $dateformat,
				'lang_datetitle' => lang('Select date'),
				'lang_start_date_statustext' => lang('Select the estimated end date for the agreement'),
				'lang_start_date' => lang('start date'),
				'value_start_date' => $agreement['start_date'],
				'lang_end_date_statustext' => lang('Select the estimated end date for the agreement'),
				'lang_end_date' => lang('end date'),
				'value_end_date' => $agreement['end_date'],
				'lang_termination_date_statustext' => lang('Select the estimated termination date'),
				'lang_termination_date' => lang('termination date'),
				'value_termination_date' => $agreement['termination_date'],
				'vendor_data' => $vendor_data,
				'lang_name' => lang('name'),
				'lang_name_statustext' => lang('name'),
				'value_name' => $agreement['name'],
				'value_contract_id' => $agreement['contract_id'],
				'lang_descr' => lang('descr'),
				'lang_descr_statustext' => lang('descr'),
				'value_descr' => $agreement['descr'],
				'table_add' => $table_add,
				'values' => $content,
				'table_header' => $table_header,
				'table_update' => $table_update,
				'update_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.edit',
					'id' => $id)),
				'lang_select_all' => lang('Select All'),
				'img_check' => $GLOBALS['phpgw']->common->get_image_path('property') . '/check.png',
				'set_column' => $set_column,
				'lang_agreement_group' => lang('Agreement group'),
				'lang_no_agreement_group' => lang('Select agreement group'),
				'agreement_group_list' => $this->bo->get_agreement_group_list($agreement['group_id']),
				'lang_status' => lang('Status'),
				'status_list' => $this->bo->select_status_list('select', $agreement['status']),
				'status_name' => 'values[status]',
				'status_required'	=> true,
				'lang_no_status' => lang('Select status'),
				'textareacols' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location', 'date',
					'security', 'file'))
			);
			//---datatable settings--------------------

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . ($id ? lang('edit') . ' ' . lang($this->role) : lang('add') . ' ' . lang($this->role));

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::add_javascript('property', 'portico', 'agreement.edit.js');
			self::render_template_xsl(array('agreement', 'datatable_inline', 'files', 'attributes_form',
				'nextmatchs'), array('edit' => $data));
		}

		function download()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$id = phpgw::get_var('id', 'int');
			$this->bo->allrows = true;
			if ($id)
			{
				$list = $this->bo->read_details($id);
			}
			else
			{
				$list = $this->bo->read($this->_get_params());
			}
			$uicols = $this->bo->uicols;
			$this->bocommon->download($list, $uicols['name'], $uicols['descr'], $uicols['input_type']);
		}

		function get_contentitem()
		{
			$agreement_id = phpgw::get_var('agreement_id', 'int');
			$id = phpgw::get_var('activity_id', 'int');

			if (empty($agreement_id) || empty($id))
			{
				$result_data = array('results' => array());
				$result_data['total_records'] = 0;
				$result_data['draw'] = 0;

				return $this->jquery_results($result_data);
			}

			$list = $this->bo->read_prizing(array('agreement_id' => $agreement_id, 'activity_id' => $id));
			$uicols = $this->bo->uicols;
			$list = $this->list_content($list, $uicols, $edit_item = true);
			$content = $list['content'];

			$content_values = array();

			$hidden = '';
			for ($y = 0; $y < count($content); $y++)
			{
				for ($z = 0; $z <= count($content[$y]['row']); $z++)
				{
					if ($content[$y]['row'][$z]['name'] != '')
					{
						$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
					}
				}
			}

			$hidden .= " <input name='values[select][0]' type='hidden' value='" . $content_values[$y - 1]['activity_id'] . "' id='selidsul' />";
			$hidden .= " <input name='values[total_cost][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['total_cost'] . "' id='tcostul'/>";
			$hidden .= " <input name='values[w_cost][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['w_cost'] . "' id='wcostul'/>";
			$hidden .= " <input name='values[m_cost][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['m_cost'] . "' id='mcostul'/>";
			$hidden .= " <input name='values[id][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['index_count'] . "' id='icountul'/>";

			$content_values[$y - 1]['index_date'] .= $hidden;

			$total_records = count($content_values);

			$result_data = array('results' => $content_values);

			$result_data['total_records'] = $total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function edit_item()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 2, 'acl_location' => $this->acl_location));
			}


			$agreement_id = phpgw::get_var('agreement_id', 'int');
			$id = phpgw::get_var('id', 'int');
			$values = phpgw::get_var('values');

			$values_attribute = phpgw::get_var('values_attribute');

			$tabs = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab = 'general';

			if (is_array($values))
			{

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{

					if (!$receipt['error'])
					{
						$values['agreement_id'] = $agreement_id;
						$values['id'] = $id;
						$receipt = $this->bo->save_item($values, $values_attribute);
						$agreement_id = $receipt['agreement_id'];
						$id = $receipt['id'];
						$this->cat_id = ($values['cat_id'] ? $values['cat_id'] : $this->cat_id);

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', 'agreement_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiagreement.edit',
								'id' => $agreement_id, 'tab' => 'items'));
						}
					}
				}
				else if (!$values['save'] && !$values['apply'] && !$values['update'])
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiagreement.edit',
						'id' => $agreement_id, 'tab' => 'items'));
				}
			}

			$agreement = $this->bo->read_single(array('agreement_id' => $agreement_id));
			$values = $this->bo->read_single_item(array('agreement_id' => $agreement_id,
				'id' => $id));

			$link_data = array
				(
				'menuaction' => 'property.uiagreement.edit_item',
				'agreement_id' => $agreement_id,
				'id' => $id,
				'role' => $this->role
			);


			$GLOBALS['phpgw']->jqcal->add_listener('values_date');

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$member_of_data = $this->cats->formatted_xslt_list(array('selected' => $this->member_id,
				'globals' => true, link_data => array()));

			$table_add[] = array
				(
				'lang_add' => lang('add detail'),
				'lang_add_standardtext' => lang('add an item to the details'),
				'add_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.edit_item',
					'agreement_id' => $agreement_id))
			);

			if ($id)
			{
				$list = $this->bo->read_prizing(array('agreement_id' => $agreement_id, 'activity_id' => $id));
				$activity_descr = $this->bo->get_activity_descr($id);
			}

			$uicols = $this->bo->uicols;
			$list = $this->list_content($list, $uicols, $edit_item = true);
			$content = $list['content'];
			$table_header = $list['table_header'];

			for ($i = 0; $i < count($list['content'][0]['row']); $i++)
			{
				$set_column[] = true;
			}

			$table_update[] = array
				(
				'lang_new_index' => lang('New index'),
				'lang_new_index_statustext' => lang('Enter a new index'),
				'lang_date_statustext' => lang('Select the date for the update'),
				'lang_update' => lang('Update'),
				'lang_update_statustext' => lang('update selected investments')
			);

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{

				$content_values = array();

				$hidden = '';
				for ($y = 0; $y < count($content); $y++)
				{
					for ($z = 0; $z <= count($content[$y]['row']); $z++)
					{
						if ($content[$y]['row'][$z]['name'] != '')
						{
							$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
						}
					}
				}

				$hidden .= " <input name='values[select][0]' type='hidden' value='" . $content_values[$y - 1]['activity_id'] . "'/>";
				$hidden .= " <input name='values[total_cost][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['total_cost'] . "'/>";
				$hidden .= " <input name='values[w_cost][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['w_cost'] . "'/>";
				$hidden .= " <input name='values[m_cost][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['m_cost'] . "'/>";
				$hidden .= " <input name='values[id][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['index_count'] . "'/>";

				$content_values[$y - 1]['index_date'] .= $hidden;

				if (count($content_values))
				{
					return json_encode($content_values);
				}
				else
				{
					return "";
				}
			}

			$hidden = '';
			for ($y = 0; $y < count($content); $y++)
			{
				for ($z = 0; $z <= count($content[$y]['row']); $z++)
				{
					if ($content[$y]['row'][$z]['name'] != '')
					{
						$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
					}
				}
			}

			$hidden .= " <input name='values[select][0]'  type='hidden' value='" . $content_values[$y - 1]['activity_id'] . "'/>";
			$hidden .= " <input name='values[total_cost][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['total_cost'] . "'/>";
			$hidden .= " <input name='values[w_cost][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['w_cost'] . "'/>";
			$hidden .= " <input name='values[m_cost][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['m_cost'] . "'/>";
			$hidden .= " <input name='values[id][" . $content_values[$y - 1]['activity_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['index_count'] . "'/>";

			$content_values[$y - 1]['index_date'] .= $hidden;

			$myColumnDefs0 = array
				(
				array('key' => 'activity_id', 'label' => lang('Activity ID'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'm_cost', label => lang('m_cost'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'w_cost', 'label' => lang('w_cost'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'total_cost', 'label' => lang('Total Cost'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'this_index', 'label' => lang('index'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'index_count', 'label' => lang('index_count'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'index_date', 'label' => lang('Date'), 'sortable' => false, 'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiagreement.get_contentitem',
						'agreement_id' => $agreement_id, 'activity_id' => $id, 'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => $myColumnDefs0,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			self::add_javascript('property', 'core', 'check.js');

			$data = array
				(
				'datatable_def' => $datatable_def,
				'base_java_url' => json_encode(array(menuaction => "property.uiagreement.edit_item",
					agreement_id => $agreement_id, id => $id, role => $this->role)),
				'activity_descr' => $activity_descr,
				'lang_descr' => lang('Descr'),
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_id' => lang('ID'),
				'value_id' => $values['id'],
				'value_num' => $values['num'],
				'value_agreement_id' => $agreement_id,
				'lang_category' => lang('category'),
				'lang_save' => lang('save'),
				'lang_cancel' => lang('cancel'),
				'lang_apply' => lang('apply'),
				'lang_apply_statustext' => lang('Apply the values'),
				'lang_cancel_statustext' => lang('Leave the agreement untouched and return back to the list'),
				'lang_save_statustext' => lang('Save the agreement and return back to the list'),
				'attributes_values' => $values['attributes'],
				'lookup_functions' => $values['lookup_functions'],
				'dateformat' => $dateformat,
				'lang_agreement' => lang('Agreement'),
				'agreement_name' => $agreement['name'],
				'table_add' => $table_add,
				'values' => $content,
				'index_count' => $content[0]['index_count'],
				'table_header' => $table_header,
				'acl_manage' => $this->acl_manage,
				'table_update' => $table_update,
				'update_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.edit_item',
					'agreement_id' => $agreement_id, 'id' => $id)),
				'lang_select_all' => lang('Select All'),
				'img_check' => $GLOBALS['phpgw']->common->get_image_path('property') . '/check.png',
				'lang_m_cost' => lang('Material cost'),
				'lang_m_cost_statustext' => lang('Material cost'),
				'value_m_cost' => $values['m_cost'],
				'lang_w_cost' => lang('Labour cost'),
				'lang_w_cost_statustext' => lang('Labour cost'),
				'value_w_cost' => $values['w_cost'],
				'lang_total_cost' => lang('Total cost'),
				'value_total_cost' => $values['total_cost'],
				'set_column' => $set_column,
				'lang_delete_last' => lang('delete last index'),
				'lang_delete_last_statustext' => lang('delete the last index'),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.edit_item',
					'delete_last' => 1, 'agreement_id' => $agreement_id, 'id' => $id)),
				'textareacols' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . ($values['id'] ? lang('edit item') . ' ' . $agreement['name'] : lang('add item') . ' ' . $agreement['name']);

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::add_javascript('property', 'portico', 'agreement.edit.js');
			self::render_template_xsl(array('agreement', 'datatable_inline', 'attributes_form'), array(
				'edit_item' => $data));
		}

		function view_item()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$agreement_id = phpgw::get_var('agreement_id', 'int');
			$id = phpgw::get_var('id', 'int');

			$agreement = $this->bo->read_single(array('agreement_id' => $agreement_id));
			$values = $this->bo->read_single_item(array('agreement_id' => $agreement_id,
				'id' => $id));

			$link_data = array
				(
				'menuaction' => 'property.uiagreement.edit',
				'id' => $agreement_id
			);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat, 'y')] = 'yyyy';
			$dlarr[strpos($dateformat, 'm')] = 'MM';
			$dlarr[strpos($dateformat, 'd')] = 'DD';
			ksort($dlarr);

			$dateformat = (implode($sep, $dlarr));

			if ($id)
			{
				$list = $this->bo->read_prizing(array('agreement_id' => $agreement_id, 'activity_id' => $id));
				$activity_descr = $this->bo->get_activity_descr($id);
			}

			$uicols = $this->bo->uicols;
			$list = $this->list_content($list, $uicols, $edit_item = true);
			$content = $list['content'];
			$table_header = $list['table_header'];

			self::add_javascript('property', 'core', 'check.js');

			//---datatable1 settings---------------------------------------------------
			//Prepare array for $datavalues[0]
			for ($y = 0; $y < count($content); $y++)
			{
				for ($z = 0; $z <= count($content[$y]['row']); $z++)
				{
					if ($content[$y]['row'][$z]['name'] != '')
					{
						$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
					}
				}
			}

			$myColumnDefs0 = array
				(
				array('key' => 'activity_id', 'label' => lang($table_header[0]['header']), 'sortable' => true,
					'resizeable' => true),
				array('key' => 'm_cost', 'label' => lang($table_header[2]['header']), 'sortable' => true,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterRight'),
				array('key' => 'w_cost', 'label' => lang($table_header[3]['header']), 'sortable' => true,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterRight'),
				array('key' => 'total_cost', 'label' => lang($table_header[4]['header']), 'sortable' => true,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterRight'),
				array('key' => 'this_index', 'label' => lang($table_header[5]['header']), 'sortable' => true,
					'resizeable' => true),
				array('key' => 'index_count', 'label' => lang($table_header[6]['header']), 'sortable' => true,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter'),
				array('key' => 'index_date', 'label' => lang($table_header[7]['header']), 'sortable' => true,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'data' => json_encode($content_values),
				'ColumnDefs' => $myColumnDefs0,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'base_java_url' => json_encode(array(menuaction => "property.uiagreement.view_item")),
				'datatable' => $datavalues,
				'myColumnDefs' => $myColumnDefs,
				'activity_descr' => $activity_descr,
				'lang_descr' => lang('Descr'),
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_id' => lang('ID'),
				'value_id' => $values['id'],
				'value_num' => $values['num'],
				'value_agreement_id' => $agreement_id,
				'lang_category' => lang('category'),
				'lang_cancel' => lang('cancel'),
				'lang_cancel_statustext' => lang('Leave the agreement untouched and return back to the list'),
				'lang_dateformat' => lang(strtolower($dateformat)),
				'attributes_view' => $values['attributes'],
				'lang_agreement' => lang('Agreement'),
				'agreement_name' => $agreement['name'],
				'table_add' => $table_add,
				'values' => $content,
				'table_header' => $table_header,
				'lang_m_cost' => lang('Material cost'),
				'value_m_cost' => $values['m_cost'],
				'lang_w_cost' => lang('Labour cost'),
				'value_w_cost' => $values['w_cost'],
				'lang_total_cost' => lang('Total cost'),
				'value_total_cost' => $values['total_cost'],
				'set_column' => $set_column,
				'textareacols' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . lang('view item') . ' ' . $agreement['name'];
			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl(array('agreement', 'datatable_inline', 'attributes_view'), array(
				'view_item' => $data));
		}

		function delete()
		{
			if (!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 8, 'acl_location' => $this->acl_location));
			}

			$agreement_id = phpgw::get_var('agreement_id', 'int');
			$delete = phpgw::get_var('delete', 'bool', 'POST');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'property.uiagreement.index',
				'role' => $this->role
			);

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($agreement_id);
				return "agreement_id " . $agreement_id . " " . lang("has been deleted");
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiagreement.delete',
					'agreement_id' => $agreement_id, 'role' => $this->role)),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('agreement');
			$function_msg = lang('delete') . ' ' . lang($this->role);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}

		function view()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$this->bo->allrows = 1;
			$agreement_id = phpgw::get_var('id', 'int');
			$config = CreateObject('phpgwapi.config', 'property');
			$agreement = $this->bo->read_single(array('agreement_id' => $agreement_id));

			$tabs = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab = 'general';
			$tabs['items'] = array('label' => lang('items'), 'link' => "#items");

			if ($agreement_id)
			{
				$this->cat_id = ($agreement['cat_id'] ? $agreement['cat_id'] : $this->cat_id);
				$this->member_id = ($agreement['member_of'] ? $agreement['member_of'] : $this->member_id);
				$list = $this->bo->read_details($agreement_id);

				$uicols = $this->bo->uicols;
				$list = $this->list_content($list, $uicols, $edit_item = false, $view_only = true);
				$content = $list['content'];
				$table_header = $list['table_header'];
			}

			$link_data = array('menuaction' => 'property.uiagreement.index');

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id' => $agreement['vendor_id'],
				'vendor_name' => $agreement['vendor_name'],
				'type' => 'view'));

			$alarm_data = $this->bocommon->initiate_ui_alarm(array(
				'acl_location' => $this->acl_location,
				'alarm_type' => 'agreement',
				'type' => 'view',
				'text' => 'Email notification',
				'times' => $times,
				'id' => $agreement_id,
				'method' => $method,
				'data' => $data,
				'account_id' => $account_id
			));


			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat, 'y')] = 'yyyy';
			$dlarr[strpos($dateformat, 'm')] = 'MM';
			$dlarr[strpos($dateformat, 'd')] = 'DD';
			ksort($dlarr);

			$dateformat = (implode($sep, $dlarr));

			$member_of_data = $this->cats->formatted_xslt_list(array('selected' => $this->member_id,
				'globals' => true, link_data => array()));

			$link_file_data = array
				(
				'menuaction' => 'property.uiagreement.view_file',
				'id' => $agreement_id
			);


			if (isset($agreement['files']) && is_array($agreement['files']))
			{
				$j = count($agreement['files']);
				for ($i = 0; $i < $j; $i++)
				{
					$agreement['files'][$i]['file_name'] = urlencode($agreement['files'][$i]['name']);
				}
			}

			if (!$this->allrows)
			{
				$record_limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit = $this->bo->total_records;
			}

			//---datatable0 settings---------------------------------------------------

			$myColumnDefs = array
				(
				array('key' => 'time', 'label' => $alarm_data['header'][0]['lang_time'], 'sortable' => true,
					'resizeable' => true),
				array('key' => 'text', 'label' => $alarm_data['header'][0]['lang_text'], 'sortable' => true,
					'resizeable' => true),
				array('key' => 'user', 'label' => $alarm_data['header'][0]['lang_user'], 'sortable' => true,
					'resizeable' => true),
				array('key' => 'enabled', 'label' => $alarm_data['header'][0]['lang_enabled'],
					'sortable' => true, 'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter'),
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'data' => json_encode($alarm_data['values']),
				'ColumnDefs' => $myColumnDefs,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);
			//---datatable1 settings---------------------------------------------------

			for ($y = 0; $y < count($content); $y++)
			{
				for ($z = 0; $z <= count($content[$y]['row']); $z++)
				{
					if ($content[$y]['row'][$z]['name'] != '')
					{
						$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
					}
				}
			}

			$myColumnDefs1 = array
				(
				array('key' => 'activity_id', 'label' => $table_header[0]['header'], 'sortable' => TRUE,
					'resizeable' => true),
				array('key' => 'num', 'label' => $table_header[1]['header'], 'sortable' => TRUE,
					'resizeable' => true),
				array('key' => 'descr', 'label' => $table_header[2]['header'], 'sortable' => TRUE,
					'resizeable' => true),
				array('key' => 'unit_name', 'label' => $table_header[3]['header'], 'sortable' => TRUE,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter'),
				array('key' => 'm_cost', 'label' => $table_header[4]['header'], 'sortable' => FALSE,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterRight'),
				array('key' => 'w_cost', 'label' => $table_header[5]['header'], 'sortable' => FALSE,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterRight'),
				array('key' => 'total_cost', 'label' => $table_header[6]['header'], 'sortable' => TRUE,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterRight'),
				array('key' => 'this_index', 'label' => $table_header[7]['header'], 'sortable' => TRUE,
					'resizeable' => true),
				array('key' => 'index_count', 'label' => $table_header[8]['header'], 'sortable' => TRUE,
					'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter'),
				array('key' => 'index_date', 'label' => $table_header[9]['header'], 'sortable' => TRUE,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => "''",
				'data' => json_encode($content_values),
				'ColumnDefs' => $myColumnDefs1,
				'config' => array(
					array('disableFilter' => true),
//					array('disablePagination'	=> true)
				)
			);
			//---datatable2 settings---------------------------------------------------
			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			for ($z = 0; $z < count($agreement['files']); $z++)
			{
				$content_files[$z]['file_name'] = '<a href="' . $link_view_file . '&amp;file_id=' . $agreement['files'][$z]['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $agreement['files'][$z]['name'] . '</a>';
			}

			$myColumnDefs2 = array
				(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => "''",
				'data' => json_encode($content_files),
				'ColumnDefs' => $myColumnDefs2,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$link_download = array
				(
				'menuaction' => 'property.uiagreement.download',
				'id' => $agreement_id
			);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'base_java_url' => json_encode(array(menuaction => "property.uiagreement.view")),
				'allow_allrows' => true,
				'allrows' => $this->allrows,
				'start_record' => $this->start,
				'record_limit' => $record_limit,
				'num_records' => count($content),
				'lang_total_records' => lang('Total'),
				'all_records' => $this->bo->total_records,
				'img_path' => $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
				'alarm_data' => $alarm_data,
				'lang_alarm' => lang('Alarm'),
				'link_view_file' => $GLOBALS['phpgw']->link('/index.php', $link_file_data),
				'files' => isset($agreement['files']) ? $agreement['files'] : '',
				'lang_files' => lang('files'),
				'lang_filename' => lang('Filename'),
				'lang_view_file_statustext' => lang('click to view file'),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_id' => lang('ID'),
				'value_agreement_id' => $agreement_id,
				'lang_category' => lang('category'),
				'lang_save' => lang('save'),
				'lang_cancel' => lang('done'),
				'lang_apply' => lang('apply'),
				'value_cat' => $agreement['cat'],
				'lang_cancel_statustext' => lang('return back to the list'),
				'cat_list' => $this->bocommon->select_category_list(array('format' => 'select',
					'selected' => $this->cat_id, 'type' => 'branch', 'order' => 'descr')),
				'lang_member_of' => lang('member of'),
				'member_of_name' => 'member_id',
				'member_of_list' => $member_of_data['cat_list'],
				'lang_dateformat' => lang(strtolower($dateformat)),
				'attributes_view' => $agreement['attributes'],
				'dateformat' => $dateformat,
				'lang_start_date' => lang('start date'),
				'value_start_date' => $agreement['start_date'],
				'lang_end_date' => lang('end date'),
				'value_end_date' => $agreement['end_date'],
				'lang_termination_date' => lang('termination date'),
				'value_termination_date' => $agreement['termination_date'],
				'vendor_data' => $vendor_data,
				'lang_name' => lang('name'),
				'value_name' => $agreement['name'],
				'lang_descr' => lang('descr'),
				'value_descr' => $agreement['descr'],
				'table_add' => $table_add,
				'values' => $content,
				'table_header' => $table_header,
				'lang_agreement_group' => lang('Agreement group'),
				'agreement_group_list' => $this->bo->get_agreement_group_list($agreement['group_id']),
				'lang_status' => lang('Status'),
				'status_list' => $this->bo->select_status_list('select', $agreement['status']),
				'textareacols' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'lang_download' => 'download',
				'link_download' => $GLOBALS['phpgw']->link('/index.php', $link_download),
				'lang_download_help' => lang('Download table to your browser'),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location', 'date',
					'security', 'file'))
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('agreement') . ': ' . lang('view');

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl(array('agreement', 'datatable_inline', 'files', 'nextmatchs',
				'attributes_view'), array('view' => $data));
		}
	}