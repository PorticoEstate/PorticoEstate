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

	class property_uis_agreement extends phpgwapi_uicommon_jquery
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
			'query'					 => true,
			'index'					 => true,
			'view'					 => true,
			'edit'					 => true,
			'delete'				 => true,
			'columns'				 => true,
			'edit_item'				 => true,
			'view_item'				 => true,
			'view_file'				 => true,
			'download'				 => true,
			'import'				 => true,
			'get_vendor_member_info' => true,
			'save'					 => true,
			'get_contentalarm'		 => true,
			'edit_alarm'			 => true,
			'deleteitem'			 => true,
			'get_content'			 => true,
			'get_contentitem'		 => true,
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app']			 = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'property::agreement::service';
			$this->account										 = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		 = CreateObject('property.bos_agreement', true);
			$this->bocommon	 = & $this->bo->bocommon;

			$this->role = $this->bo->role;

			$this->cats			 = & $this->bo->cats;
			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.s_agreement';

			$this->acl_read		 = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add		 = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit		 = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete	 = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage	 = $this->acl->check($this->acl_location, 16, 'property');
			$this->custom		 = & $this->bo->custom;

			$this->start		 = $this->bo->start;
			$this->query		 = $this->bo->query;
			$this->sort			 = $this->bo->sort;
			$this->order		 = $this->bo->order;
			$this->filter		 = $this->bo->filter;
			$this->cat_id		 = $this->bo->cat_id;
			$this->vendor_id	 = $this->bo->vendor_id;
			$this->allrows		 = $this->bo->allrows;
			$this->member_id	 = $this->bo->member_id;
			$this->p_num		 = $this->bo->p_num;
			$this->status_id	 = $this->bo->status_id;
			$this->location_code = $this->bo->location_code;
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start'		 => $this->start,
				'query'		 => $this->query,
				'sort'		 => $this->sort,
				'order'		 => $this->order,
				'filter'	 => $this->filter,
				'cat_id'	 => $this->cat_id,
				'vendor_id'	 => $this->vendor_id,
				'allrows'	 => $this->allrows,
				'member_id'	 => $this->member_id,
				'status_id'	 => $this->status_id
			);
			$this->bo->save_sessiondata($data);
		}

		function columns()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$values = phpgw::get_var('values');

			if ($values['save'])
			{

				$GLOBALS['phpgw']->preferences->account_id = $this->account;
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('property', 's_agreement_columns', $values['columns'], 'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg = lang('Select Column');

			$link_data = array
				(
				'menuaction' => 'property.uis_agreement.columns',
				'role'		 => $this->role
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data'	 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list'	 => $this->bo->column_list($values['columns'], $allrows		 = true),
				'function_msg'	 => $function_msg,
				'form_action'	 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_columns'	 => lang('columns'),
				'lang_none'		 => lang('None'),
				'lang_save'		 => lang('save'),
				'select_name'	 => 'period'
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('columns' => $data));
		}

		function view_file()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			ExecMethod('property.bofiles.get_file', phpgw::get_var('file_id', 'int'));
		}

		private function _get_Filters()
		{
			$values_combo_box	 = array();
			$combos				 = array();

			$values_combo_box[0] = $this->cats->formatted_xslt_list(array('selected'	 => $this->member_id,
				'globals'	 => true, 'link_data'	 => $link_data));
			$default_value		 = array('cat_id' => '', 'name' => lang('no member'));
			foreach ($values_combo_box[0]['cat_list'] as &$list)
			{

				$list['id'] = $list['cat_id'];
			}
			array_unshift($values_combo_box[0]['cat_list'], $default_value);
			$combos[] = array(
				'type'	 => 'filter',
				'name'	 => 'member_id',
				'text'	 => lang('Member'),
				'list'	 => $values_combo_box[0]['cat_list']
			);

			$values_combo_box[1] = $this->bocommon->select_category_list(array('format'	 => 'filter',
				'selected'	 => $this->cat_id, 'type'		 => 's_agreement', 'order'		 => 'descr'));
			$default_value		 = array('id' => '', 'name' => lang('no category'));
			array_unshift($values_combo_box[1], $default_value);
			$combos[]			 = array(
				'type'	 => 'filter',
				'name'	 => 'cat_id',
				'text'	 => lang('Category'),
				'list'	 => $values_combo_box[1]
			);

			$values_combo_box[2] = $this->bo->select_vendor_list('filter', $this->vendor_id);
			$default_value		 = array('id' => '', 'name' => lang('no vendor'));
			array_unshift($values_combo_box[2], $default_value);
			$combos[]			 = array(
				'type'	 => 'filter',
				'name'	 => 'vendor_id',
				'text'	 => lang('Vendor'),
				'list'	 => $values_combo_box[2]
			);

			$values_combo_box[3] = $this->bo->select_status_list('filter', $this->status_id);
			$default_value		 = array('id' => '', 'name' => lang('no status'));
			array_unshift($values_combo_box[3], $default_value);
			$combos[]			 = array(
				'type'	 => 'filter',
				'name'	 => 'status_id',
				'text'	 => lang('Status'),
				'list'	 => $values_combo_box[3]
			);

			return $combos;
		}

		function index()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', 's_agreement_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data', 's_agreement_receipt', '');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = lang('agreement');
			$function_msg	 = lang('List') . ' ' . lang($this->role);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uis_agreement.index',
						'cat_id'			 => $this->cat_id,
						'filter'			 => $this->filter,
						'role'				 => $this->role,
						'member_id'			 => $this->member_id,
						'p_num'				 => $this->p_num,
						'status_id'			 => $this->status_id,
						'location_code'		 => $this->location_code,
						'phpgw_return_as'	 => 'json'
					)),
					'download'		 => self::link(array(
						'menuaction' => 'property.uis_agreement.download',
						'id'		 => $id
					)),
					"columns"		 => array('onclick' => "JqueryPortico.openPopup({menuaction:'property.uis_agreement.columns', role:'{$this->role}'},{closeAction:'reload'})"),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uis_agreement.edit',
						'role'		 => $this->role
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array()
				)
			);

			$filters = $this->_get_Filters();
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$list				 = $this->bo->read(array('dry_run' => true));
			$uicols				 = $this->bo->uicols;
			//$j = 0;
			$count_uicols_name	 = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array
					(
					'key'		 => $uicols['name'][$k],
					'label'		 => $uicols['descr'][$k],
					'sortable'	 => ($uicols['sortable'][$k]) ? false : true,
					'hidden'	 => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				array_push($data['datatable']['field'], $params);
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'id',
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
						'name'	 => 's_agreement_id',
						'source' => 'id'
					),
				)
			);

			if ($this->acl_read)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name'	 => 'view',
					'statustext' => lang('view this entity'),
					'text'		 => lang('view'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uis_agreement.view',
						'role'		 => $this->role
					)),
					'parameters' => json_encode($parameters)
				);

				$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

				foreach ($jasper as $report)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name'	 => 'edit',
						'text'		 => lang('open JasperReport %1 in new window', $report['title']),
						'action'	 => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uijasper.view',
							'jasper_id'	 => $report['id']
						)),
						'target'	 => '_blank',
						'parameters' => json_encode($parameters)
					);
				}
			}

			if ($this->acl_edit)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name'	 => 'edit',
					'statustext' => lang('edit this entity'),
					'text'		 => lang('edit'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uis_agreement.edit',
						'role'		 => $this->role
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_delete)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name'		 => 'delete',
					'statustext'	 => lang('delete this entity'),
					'text'			 => lang('delete'),
					'confirm_msg'	 => lang('do you really want to delete this entry'),
					'action'		 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uis_agreement.delete',
						'role'		 => $this->role
					)),
					'parameters'	 => json_encode($parameters2)
				);
			}

			unset($parameters);
			unset($parameters2);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export	 = phpgw::get_var('export', 'bool');

			$params = array(
				'start'			 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'		 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'			 => $search['value'],
				'order'			 => $columns[$order[0]['column']]['data'],
				'sort'			 => $order[0]['dir'],
				'allrows'		 => phpgw::get_var('length', 'int') == -1 || $export,
				'filter'		 => $this->filter,
				'cat_id'		 => $this->cat_id,
				'member_id'		 => $this->member_id,
				'vendor_id'		 => $this->vendor_id,
				'p_num'			 => $this->p_num,
				'status_id'		 => $this->status_id,
				'location_code'	 => $this->location_code
			);

			$result_objects	 = array();
			$result_count	 = 0;

			$values = $this->bo->read($params);

			if ($export)
			{
				return $values;
			}

			$result_data					 = array('results' => $values);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}

		function list_content( $list, $uicols, $edit_item = '', $view_only = '' )
		{
			$j = 0;

			if (isset($list) AND is_array($list))
			{
				foreach ($list as $entry)
				{
					$content[$j]['id']			 = $entry['id'];
					$content[$j]['item_id']		 = $entry['item_id'];
					$content[$j]['index_count']	 = $entry['index_count'];
					$content[$j]['cost']		 = $entry['cost'];
					for ($i = 0; $i < count($uicols['name']); $i++)
					{
						if ($uicols['input_type'][$i] != 'hidden')
						{
							$content[$j]['row'][$i]['value'] = $entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name']	 = $uicols['name'][$i];
						}
					}

					if ($this->acl_read && $view_only != 'no_link')
					{
						$content[$j]['row'][$i]['statustext']	 = lang('view the entity');
						$content[$j]['row'][$i]['text']			 = lang('view');
						$content[$j]['row'][$i++]['link']		 = $GLOBALS['phpgw']->link('/index.php', array(
							'menuaction'	 => 'property.uis_agreement.view_item', 's_agreement_id' => $entry['agreement_id'],
							'id'			 => $entry['id'], 'from'			 => $view_only ? 'view' : 'edit'));
					}
					if ($this->acl_edit && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext']	 = lang('edit the agreement');
						$content[$j]['row'][$i]['text']			 = lang('edit');
						$content[$j]['row'][$i++]['link']		 = $GLOBALS['phpgw']->link('/index.php', array(
							'menuaction'	 => 'property.uis_agreement.edit_item', 's_agreement_id' => $entry['agreement_id'],
							'id'			 => $entry['id']));
					}
					if ($this->acl_delete && !$edit_item && !$view_only)
					{
						$content[$j]['row'][$i]['statustext']	 = lang('delete this item');
						$content[$j]['row'][$i]['text']			 = lang('delete');
						$content[$j]['row'][$i++]['link']		 = $GLOBALS['phpgw']->link('/index.php', array(
							'menuaction'	 => 'property.uis_agreement.edit', 'delete_item'	 => 1, 'id'			 => $entry['agreement_id'],
							'item_id'		 => $entry['id']));
					}

					$j++;
				}
			}

			//html_print_r($content);
			for ($i = 0; $i < count($uicols['descr']); $i++)
			{
				if ($uicols['input_type'][$i] != 'hidden')
				{
					$table_header[$i]['header']	 = $uicols['descr'][$i];
					$table_header[$i]['width']	 = '5%';
					$table_header[$i]['align']	 = 'center';
				}
			}

			if ($this->acl_read && $view_only != 'no_link')
			{
				$table_header[$i]['width']	 = '5%';
				$table_header[$i]['align']	 = 'center';
				$table_header[$i]['header']	 = lang('view');
				$i++;
			}
			if ($this->acl_edit && !$edit_item && !$view_only)
			{
				$table_header[$i]['width']	 = '5%';
				$table_header[$i]['align']	 = 'center';
				$table_header[$i]['header']	 = lang('edit');
				$i++;
			}
			if ($this->acl_delete && !$edit_item && !$view_only)
			{
				$table_header[$i]['width']	 = '5%';
				$table_header[$i]['align']	 = 'center';
				$table_header[$i]['header']	 = lang('delete');
				$i++;
			}
			if ($this->acl_manage && !$edit_item && !$view_only)
			{
				$table_header[$i]['width']	 = '5%';
				$table_header[$i]['align']	 = 'center';
				$table_header[$i]['header']	 = lang('Update');
				$i++;
			}

			return array('content' => $content, 'table_header' => $table_header);
		}

		function import()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $this->acl_location));
			}

			$active_tab = phpgw::get_var('tab', 'string', 'POST', 'items');

			$import = CreateObject('property.import');

			$importfile	 = $import->importfile();
			$id			 = phpgw::get_var('id');
			if (isset($importfile) && is_file($importfile) && !phpgw::get_var('cancel'))
			{
				$list		 = $this->bo->read_details(0);
				$uicols		 = $this->bo->uicols;
				$valueset	 = $import->prepare_data($importfile, $list, $uicols);

				if (phpgw::get_var('confirm', 'bool'))
				{
					if (is_file($importfile))
					{
						unlink($importfile);
					}
					foreach ($valueset as $values)
					{
						$this->bo->import($values, $id);
					}
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uis_agreement.edit',
						'id'		 => $id, 'tab'		 => $active_tab));
				}
				else
				{
					$import_action	 = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uis_agreement.import',
						'id'		 => $id));
					$import->pre_import($importfile, $valueset, $import_action, $header_info	 = lang('service agreement'));
				}
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uis_agreement.edit',
					'id'		 => $id, 'tab'		 => $active_tab));
			}
		}

		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}

			$id					 = phpgw::get_var('id');
			$values				 = phpgw::get_var('values');
			$values['ecodimb']	 = phpgw::get_var('ecodimb');

			if ($values['save'] || $values['apply'])
			{
				$values['vendor_id']		 = phpgw::get_var('vendor_id', 'int', 'POST');
				$values['vendor_name']		 = phpgw::get_var('vendor_name', 'string', 'POST');
				$values['b_account_id']		 = phpgw::get_var('b_account_id', 'int', 'POST');
				$values['b_account_name']	 = phpgw::get_var('b_account_name', 'string', 'POST');

				$values_attribute			 = phpgw::get_var('values_attribute');
				$insert_record_s_agreement	 = $GLOBALS['phpgw']->session->appsession('insert_record_values.s_agreement', 'property');
				for ($j = 0; $j < count($insert_record_s_agreement); $j++)
				{
					$insert_record['extra'][$insert_record_s_agreement[$j]] = $insert_record_s_agreement[$j];
				}

				if (is_array($insert_record['extra']))
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


				if (isset($values['budget']) && $values['budget'])
				{
					if (!ctype_digit($values['budget']))
					{
						$receipt['error'][] = array('msg' => lang('budget') . ': ' . lang('Please enter an integer !'));
					}
					if (!isset($values['ecodimb']) || !$values['ecodimb'])
					{
						$receipt['error'][] = array('msg' => lang('accounting dim b'));
					}
					if (!isset($values['order_category']) || !$values['order_category'])
					{
						$receipt['error'][] = array('msg' => lang('category'));
					}
					if (!isset($values['b_account_id']) || !$values['b_account_id'])
					{
						$receipt['error'][] = array('msg' => lang('budget account'));
					}
				}

				if ($id)
				{
					$values['s_agreement_id']	 = $id;
					$action						 = 'edit';
				}
				else
				{
					$values['s_agreement_id'] = $this->bo->request_next_id();
				}

				$bofiles = CreateObject('property.bofiles');
				if (isset($values['file_action']) && is_array($values['file_action']))
				{
					$bofiles->delete_file("/service_agreement/{$id}/", $values);
				}

				$values['file_name'] = str_replace(' ', '_', $_FILES['file']['name']);
				$to_file			 = "{$bofiles->fakebase}/service_agreement/{$values['s_agreement_id']}/{$values['file_name']}";

				if (!$values['document_name_orig'] && $bofiles->vfs->file_exists(array(
						'string'	 => $to_file,
						'relatives'	 => Array(RELATIVE_NONE)
					)))
				{
					$receipt['error'][] = array('msg' => lang('This file already exists !'));
				}

				if (!$receipt['error'])
				{
					try
					{

						$receipt		 = $this->bo->save($values, $values_attribute, $action);
						$id				 = $receipt['s_agreement_id'];
						$this->cat_id	 = ($values['cat_id'] ? $values['cat_id'] : $this->cat_id);

						if ($values['file_name'])
						{
							$bofiles->create_document_dir("service_agreement/{$id}");
							$bofiles->vfs->override_acl = 1;

							if (!$bofiles->vfs->cp(array(
									'from'		 => $_FILES['file']['tmp_name'],
									'to'		 => $to_file,
									'relatives'	 => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
							{
								$receipt['error'][] = array('msg' => lang('Failed to upload file !'));
							}
							$bofiles->vfs->override_acl = 0;
						}


						if ($values['save'])
						{
							self::message_set($receipt);
							//					$GLOBALS['phpgw']->session->appsession('session_data', 's_agreement_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uis_agreement.index',
								'role'		 => $this->role));
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
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uis_agreement.edit',
						'id'		 => $id));
				}
				else
				{
					self::message_set($receipt);
					/* Preserve attribute values from post */
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
			$s_agreement_id = phpgw::get_var('s_agreement_id', 'int');

			if (empty($s_agreement_id))
			{
				$result_data					 = array('results' => array());
				$result_data['total_records']	 = 0;
				$result_data['draw']			 = 0;

				return $this->jquery_results($result_data);
			}

			$year	 = phpgw::get_var('year', 'int');
			$draw	 = phpgw::get_var('draw', 'int');
			$order	 = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');


			$list	 = $this->bo->read_details($s_agreement_id);
			$uicols	 = $this->bo->uicols;
			$values	 = $this->list_content($list, $uicols);

			$content_values = array();

			for ($y = 0; $y < count($values['content']); $y++)
			{
				for ($z = 0; $z < count($values['content'][$y]['row']); $z++)
				{
					if ($values['content'][$y]['row'][$z + 1]['name'] != '')
					{
						$content_values[$y][$values['content'][$y]['row'][$z + 1]['name']] = $values['content'][$y]['row'][$z + 1]['value'];
					}
				}
			}

			$total_records = count($content_values);

			$result_data = array('results' => $content_values);

			$result_data['total_records']	 = $total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}

		public function get_contentalarm()
		{
			$id				 = phpgw::get_var('id', 'int');
			$acl_location	 = phpgw::get_var('acl_location');
			$times			 = phpgw::get_var('times');
			$method			 = phpgw::get_var('method');
			$data			 = phpgw::get_var('data');
			$account_id		 = phpgw::get_var('account_id');

			if (empty($id))
			{
				$result_data					 = array('results' => array());
				$result_data['total_records']	 = 0;
				$result_data['draw']			 = 0;

				return $this->jquery_results($result_data);
			}

			$params = array
				(
				'acl_location'	 => $acl_location,
				'alarm_type'	 => 's_agreement',
				'type'			 => 'form',
				'text'			 => 'Email notification',
				'times'			 => $times,
				'id'			 => $id,
				'method'		 => $method,
				'data'			 => $data,
				'account_id'	 => $account_id
			);

			$values			 = $this->bocommon->initiate_ui_alarm($params);
			$total_records	 = count($values['values']);

			$result_data = array('results' => $values['values']);

			$result_data['total_records']	 = $total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}

		function deleteitem()
		{
			$item_id = phpgw::get_var('id', 'int');
			$id		 = phpgw::get_var('s_agreement_id', 'int');

			if ($id && $item_id)
			{
				$this->bo->delete_item($id, $item_id);
				$get_items = true;
			}
		}

		function edit_alarm()
		{
			$boalarm	 = CreateObject('property.boalarm');
			$ids_alarm	 = !empty($_POST['ids']) ? $_POST['ids'] : '';
			$type_alarm	 = !empty($_POST['type']) ? $_POST['type'] : '';

			//Add Alarm
			$idAgreement = !empty($_POST['id']) ? $_POST['id'] : '';
			$day		 = !empty($_POST['day']) ? $_POST['day'] : '';
			$hour		 = !empty($_POST['hour']) ? $_POST['hour'] : '';
			$minute		 = !empty($_POST['minute']) ? $_POST['minute'] : '';
			$user_list	 = !empty($_POST['user_list']) ? $_POST['user_list'] : '';

			//Update Index and Date
			$date	 = !empty($_POST['date']) ? $_POST['date'] : '';
			$index	 = !empty($_POST['index']) ? $_POST['index'] : '';
			$mcosto	 = !empty($_POST['mcost']) ? $_POST['mcost'] : '';
			$icount	 = !empty($_POST['icoun']) ? $_POST['icoun'] : '';

			$requestUrl_Alarm = json_encode(self::link(array(
					'menuaction'		 => 'property.uis_agreement.get_contentalarm',
					'id'				 => $idAgreement,
					'alarm_type'		 => 's_agreement',
					'type'				 => 'form',
					'text'				 => 'Email notification',
					'acl_location'		 => $this->acl_location,
					'times'				 => isset($times) ? $times : '',
					'method'			 => isset($method) ? $method : '',
					'data'				 => isset($data) ? $data : '',
					'account_id'		 => isset($account_id) ? $account_id : '',
					'phpgw_return_as'	 => 'json'
					)
				)
			);

			$receipt = array();

			if (!empty($type_alarm))
			{

				if ($type_alarm == 'update' || $type_alarm == 'update_item')
				{
					$values	 = array(
						'agreement_id'	 => $idAgreement,
						'select'		 => $mcosto,
						'item_id'		 => $ids_alarm,
						'id'			 => $icount,
						'date'			 => $date,
						'new_index'		 => $index,
						'update'		 => 'Update',
					);
					$receipt = $this->bo->update($values);
					if ($type_alarm == 'update')
					{
						$requestUrl = json_encode(self::link(array('menuaction'		 => 'property.uis_agreement.get_content',
								's_agreement_id'	 => $idAgreement, 'phpgw_return_as'	 => 'json')));
					}
					else
					{
						$requestUrl = json_encode(self::link(array('menuaction'		 => 'property.uis_agreement.get_contentitem',
								's_agreement_id'	 => $idAgreement, 'item_id'			 => $ids_alarm[0], 'phpgw_return_as'	 => 'json')));
					}
					return $requestUrl;
				}
				else if (($type_alarm == 'delete_alarm' || $type_alarm == 'delete_item' ) && count($ids_alarm))
				{
					if ($type_alarm == 'delete_alarm')
					{
						$boalarm->delete_alarm('s_agreement', $ids_alarm);
					}
					else
					{
						$this->bo->delete_last_index($idAgreement, $ids_alarm);
						$requestUrl = json_encode(self::link(array('menuaction'		 => 'property.uis_agreement.get_contentitem',
								's_agreement_id'	 => $idAgreement, 'item_id'			 => $ids_alarm, 'role'				 => null,
								'phpgw_return_as'	 => 'json')));
						return $requestUrl;
					}
				}
				else if (($type_alarm == 'disable_alarm' || $type_alarm == 'enable_alarm' ) && count($ids_alarm))
				{
					$type_alarm = ($type_alarm == 'enable_alarm') ? $type_alarm : '';
					$boalarm->enable_alarm('s_agreement', $ids_alarm, $type_alarm);
				}
				else if ($type_alarm == 'add_alarm')
				{
					$time = intval($day) * 24 * 3600 + intval($hour) * 3600 + intval($minute) * 60;

					if ($time > 0)
					{
						$receipt = $boalarm->add_alarm('s_agreement', $this->bo->read_event(array(
								's_agreement_id' => $idAgreement)), $time, $user_list);
					}
					return $requestUrl_Alarm;
				}
			}
		}

		function edit( $values = array() )
		{
			$id = phpgw::get_var('id'); // in case of bigint

			if (!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uis_agreement.view',
					'id'		 => $id));
			}

			phpgwapi_jquery::load_widget('core');
//			$active_tab		= phpgw::get_var('tab', 'string', 'REQUEST', 'general');

			$boalarm	 = CreateObject('property.boalarm');
			$get_items	 = false;

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';
			$tabs['items']	 = array('label' => lang('items'), 'link' => "#items");

			$GLOBALS['phpgw']->xslttpl->add_file(array('s_agreement', 'attributes_form', 'files'));

			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_termination_date');

			if ($id)
			{
				$values			 = $this->bo->read_single(array('s_agreement_id' => $id));
				$list			 = $this->bo->read_details($id);
				$uicols			 = $this->bo->uicols;
				$list			 = $this->list_content($list, $uicols);
				$content		 = $list['content'];
				$table_header	 = $list['table_header'];
				for ($i = 0; $i < count($list['content'][0]['row']); $i++)
				{
					$set_column[] = true;
				}

				if (isset($content) && is_array($content))
				{
					$GLOBALS['phpgw']->jqcal->add_listener('values_date');

					$table_update[] = array
						(
						'lang_new_index'			 => lang('New index'),
						'lang_new_index_statustext'	 => lang('Enter a new index'),
						'lang_date_statustext'		 => lang('Select the date for the update'),
						'lang_update'				 => lang('Update'),
						'lang_update_statustext'	 => lang('update selected investments')
					);
				}
			}
			$this->cat_id	 = ($values['cat_id'] ? $values['cat_id'] : $this->cat_id);
			$this->member_id = $values['member_of'] ? $values['member_of'] : $this->member_id;

			$link_data = array
				(
				'menuaction' => 'property.uis_agreement.save',
				'id'		 => $id,
				'role'		 => $this->role
			);

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id'		 => $values['vendor_id'],
				'vendor_name'	 => $values['vendor_name'],
				'required'		 => true
				)
			);

			$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array
				(
				'b_account_id'	 => $values['b_account_id'],
				'b_account_name' => $values['b_account_name']));

			$ecodimb_data = $this->bocommon->initiate_ecodimb_lookup(array
				(
				'ecodimb'		 => $values['ecodimb'],
				'ecodimb_descr'	 => $values['ecodimb_descr']));

			$alarm_data = $this->bocommon->initiate_ui_alarm(array(
				'acl_location'	 => $this->acl_location,
				'alarm_type'	 => 's_agreement',
				'type'			 => 'form',
				'text'			 => 'Email notification',
				'times'			 => $times,
				'id'			 => $id,
				'method'		 => $method,
				'data'			 => $data,
				'account_id'	 => $account_id
				)
			);

			if ($values['vendor_id'])
			{
				$member_of_list = $this->get_vendor_member_info($values['vendor_id']);
			}
			else
			{
				$member_of_list = array();
			}

			$table_add[] = array
				(
				'lang_add'				 => lang('add detail'),
				'lang_add_standardtext'	 => lang('add an item to the details'),
				'add_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.edit_item',
					's_agreement_id' => $id))
			);


			$link_file_data = array
				(
				'menuaction' => 'property.uis_agreement.view_file',
				'id'		 => $id
			);


			$j = count($values['files']);
			for ($i = 0; $i < $j; $i++)
			{
				$values['files'][$i]['file_name'] = urlencode($values['files'][$i]['name']);
			}

			$link_download = array
				(
				'menuaction' => 'property.uis_agreement.download',
				'id'		 => $id
			);

			self::add_javascript('property', 'overlib', 'overlib.js');
			self::add_javascript('property', 'core', 'check.js');

			if (isset($values['attributes']) && is_array($values['attributes']))
			{

				$location			 = $this->acl_location;
				$attributes_groups	 = $this->bo->get_attribute_groups($location, $values['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if (isset($group['attributes']))
					{
						$attributes[] = $group;
					}
				}
				unset($attributes_groups);
				unset($values['attributes']);
			}

			$requestUrl_Alarm = json_encode(self::link(array(
					'menuaction'		 => 'property.uis_agreement.get_contentalarm',
					'acl_location'		 => $this->acl_location,
					'alarm_type'		 => 's_agreement',
					'type'				 => 'form',
					'text'				 => 'Email notification',
					'times'				 => isset($times) ? $times : '',
					'id'				 => $id,
					'method'			 => isset($method) ? $method : '',
					'data'				 => isset($data) ? $data : '',
					'account_id'		 => isset($account_id) ? $account_id : '',
					'phpgw_return_as'	 => 'json'
					)
				)
			);

			$tabletools = array
				(
				array(
					'my_name'		 => 'enable_alarm',
					'text'			 => lang($alarm_data[alter_alarm][0][lang_enable]),
					'type'			 => 'custom',
					'custom_code'	 => "
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
                                        onActionsClick_notify('enable_alarm', ids, {$requestUrl_Alarm});
                                        "
				),
				array(
					'my_name'		 => 'disable_alarm',
					'text'			 => lang($alarm_data[alter_alarm][0][lang_disable]),
					'type'			 => 'custom',
					'custom_code'	 => "
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
                                        onActionsClick_notify('disable_alarm', ids, {$requestUrl_Alarm});
                                        "
				),
				array(
					'my_name'		 => 'delete_alarm',
					'text'			 => lang($alarm_data[alter_alarm][0][lang_delete]),
					'type'			 => 'custom',
					'custom_code'	 => "
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
                                        onActionsClick_notify('delete_alarm', ids, {$requestUrl_Alarm});
                                        "
				)
			);

			$myColumnDefs0 = array
				(
				array('key'		 => 'time', 'label'		 => $alarm_data['header'][0]['lang_time'],
					'sortable'	 => true,
					'resizeable' => true, 'width'		 => 140),
				array('key'		 => 'text', 'label'		 => $alarm_data['header'][0]['lang_text'],
					'sortable'	 => true,
					'resizeable' => true, 'width'		 => 340),
				array('key'		 => 'user', 'label'		 => $alarm_data['header'][0]['lang_user'],
					'sortable'	 => true,
					'resizeable' => true, 'width'		 => 200),
				array('key'		 => 'enabled', 'label'		 => $alarm_data['header'][0]['lang_enabled'],
					'sortable'	 => true, 'resizeable' => true, 'formatter'	 => 'JqueryPortico.FormatterCenter',
					'width'		 => 60),
				array('key'		 => 'alarm_id', 'label'		 => "dummy", 'sortable'	 => true, 'resizeable' => true,
					'hidden'	 => true)
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => $requestUrl_Alarm,
				'data'		 => json_encode(array()),
				'tabletools' => $tabletools,
				'ColumnDefs' => $myColumnDefs0,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$td_count		 = 0;
			$ColumnDefs_data = array();
			if (isset($uicols['input_type']) && $uicols['input_type'])
			{
				foreach ($uicols['input_type'] as $key => $input_type)
				{
					if ($input_type != 'hidden')
					{
						$ColumnDefs_data[] = array
							(
							'key'		 => $uicols['name'][$key],
							'label'		 => $uicols['descr'][$key],
							'sortable'	 => false,
							'resizeable' => true
						);
						$td_count ++;
					}
				}
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'id',
						'source' => 'item_id'
					),
				)
			);

			$tabletools = array
				(
				array('my_name'	 => 'view', 'text'		 => lang('View'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.view_item',
						's_agreement_id' => $id, 'from'			 => $view_only ? 'view' : 'edit')), 'parameters' => json_encode($parameters)),
				array('my_name'	 => 'edit', 'text'		 => lang('Edit'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.edit_item',
						's_agreement_id' => $id)), 'parameters' => json_encode($parameters)),
				array('my_name'	 => 'delete', 'text'		 => lang('Delete'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.deleteitem',
						's_agreement_id' => $id)), 'parameters' => json_encode($parameters)),
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction'		 => 'property.uis_agreement.get_content',
						's_agreement_id'	 => $id, 'phpgw_return_as'	 => 'json'))),
				'data'		 => json_encode(array()),
				'tabletools' => $tabletools,
				'ColumnDefs' => $ColumnDefs_data,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);


			unset($ColumnDefs_data);

			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			for ($z = 0; $z < count($values['files']); $z++)
			{
				$content_files[$z]['file_name']		 = '<a href="' . $link_view_file . '&amp;file_id=' . $values['files'][$z]['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $values['files'][$z]['name'] . '</a>';
				$content_files[$z]['delete_file']	 = '<input type="checkbox" name="values[file_action][]" value="' . $values['files'][$z]['file_id'] . '" title="' . lang('Check to delete file') . '">';
			}

			$myColumnDefs2 = array
				(
				array('key'		 => 'file_name', 'label'		 => lang('Filename'), 'sortable'	 => false,
					'resizeable' => true),
				array('key'		 => 'delete_file', 'label'		 => lang('Delete file'), 'sortable'	 => false,
					'resizeable' => true, 'formatter'	 => 'JqueryPortico.FormatterCenter')
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_2',
				'requestUrl' => "''",
				'data'		 => json_encode($content_files),
				'ColumnDefs' => $myColumnDefs2,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);


			if ($id)
			{
				$content_budget	 = $this->bo->get_budget($id);
				$lang_delete	 = lang('Check to delete year');
				foreach ($content_budget as & $b_entry)
				{
					$b_entry['delete_year'] = "<input type='checkbox' name='values[delete_b_year][]' value='{$b_entry['year']}' title='{$lang_delete}'>";
				}
			}

			$myColumnDefs3 = array
				(
				array('key' => 'year', 'label' => lang('year'), 'sortable' => false, 'resizeable' => true),
				array('key'		 => 'category', 'label'		 => lang('category'), 'sortable'	 => false,
					'resizeable' => true),
				array('key' => 'ecodimb', 'label' => lang('dimb'), 'sortable' => false, 'resizeable' => true),
				array('key'		 => 'budget_account', 'label'		 => lang('budget account'), 'sortable'	 => false,
					'resizeable' => true),
				array('key' => 'budget', 'label' => lang('budget'), 'sortable' => false, 'resizeable' => true),
				array('key'		 => 'actual_cost', 'label'		 => lang('actual cost'), 'sortable'	 => false,
					'resizeable' => true),
				array('key'		 => 'delete_year', 'label'		 => lang('Delete'), 'sortable'	 => false,
					'resizeable' => true, 'formatter'	 => 'JqueryPortico.FormatterCenter')
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_3',
				'requestUrl' => "''",
				'data'		 => json_encode($content_budget),
				'ColumnDefs' => $myColumnDefs3,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);
			//--------------------------------------------JSON CODE------------

			$this->cats->set_appname('property', '.project');

			$indice = array('id' => '0');
			array_unshift($alarm_data['add_alarm']['day_list'], $indice);
			array_unshift($alarm_data['add_alarm']['hour_list'], $indice);
			array_unshift($alarm_data['add_alarm']['minute_list'], $indice);


			$data = array
				(
				'datatable_def'						 => $datatable_def,
				'td_count'							 => $td_count,
				'base_java_url'						 => json_encode(array('menuaction' => "property.uis_agreement.edit",
					'id'		 => $id)),
				'link_import'						 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uis_agreement.import',
					'tab'		 => 'items')),
				'alarm_data'						 => $alarm_data,
				'lang_alarm'						 => lang('Alarm'),
				'lang_download'						 => 'download',
				'link_download'						 => $GLOBALS['phpgw']->link('/index.php', $link_download),
				'lang_download_help'				 => lang('Download table to your browser'),
				'fileupload'						 => true,
				'link_view_file'					 => $GLOBALS['phpgw']->link('/index.php', $link_file_data),
				'link_to_files'						 => $link_to_files,
				'files'								 => $values['files'],
				'lang_files'						 => lang('files'),
				'lang_filename'						 => lang('Filename'),
				'lang_file_action'					 => lang('Delete file'),
				'lang_view_file_statustext'			 => lang('click to view file'),
				'lang_file_action_statustext'		 => lang('Check to delete file'),
				'lang_upload_file'					 => lang('Upload file'),
				'lang_file_statustext'				 => lang('Select file to upload'),
				'edit_url'							 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'cancel_url'						 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uis_agreement.index')),
				'lang_id'							 => lang('ID'),
				'value_s_agreement_id'				 => $id,
				'lang_save'							 => lang('save'),
				'lang_cancel'						 => lang('cancel'),
				'lang_apply'						 => lang('apply'),
				'value_cat'							 => $values['cat'],
				'lang_apply_statustext'				 => lang('Apply the values'),
				'lang_cancel_statustext'			 => lang('Leave the service agreement untouched and return back to the list'),
				'lang_save_statustext'				 => lang('Save the service agreement and return back to the list'),
				'lang_no_cat'						 => lang('no category'),
				'select_name'						 => 'values[cat_id]',
				'cat_list'							 => array('options' => $this->bocommon->select_category_list(array(
						'format'	 => 'select',
						'selected'	 => $this->cat_id, 'type'		 => 's_agreement', 'order'		 => 'descr'))),
				'member_of_list2'					 => $member_of_list,
				'attributes_group'					 => $attributes,
				'lookup_functions'					 => $values['lookup_functions'],
				'dateformat'						 => $dateformat,
				'lang_start_date_statustext'		 => lang('Select the estimated end date for the Project'),
				'lang_start_date'					 => lang('start date'),
				'value_start_date'					 => $values['start_date'],
				'lang_end_date_statustext'			 => lang('Select the estimated end date for the Project'),
				'lang_end_date'						 => lang('end date'),
				'value_end_date'					 => $values['end_date'],
				'lang_termination_date_statustext'	 => lang('Select the estimated termination date'),
				'lang_termination_date'				 => lang('termination date'),
				'value_termination_date'			 => $values['termination_date'],
				'vendor_data'						 => $vendor_data,
				'lang_budget'						 => lang('Budget'),
				'lang_budget_statustext'			 => lang('Budget for selected year'),
				'value_budget'						 => $values['budget'] ? $values['budget'] : '',
				'currency'							 => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'lang_year'							 => lang('year'),
				'lang_year_statustext'				 => lang('Budget year'),
				'year'								 => $this->bocommon->select_list($values['year'], $this->bo->get_year_list($id)),
				'lang_days_statustext'				 => lang('Days'),
				'b_account_data'					 => $b_account_data,
				'ecodimb_data'						 => $ecodimb_data,
				'lang_category'						 => lang('category'),
				'lang_no_cat'						 => lang('Select category'),
				'cat_select'						 => $this->cats->formatted_xslt_list(array('select_name'	 => 'values[order_category]',
					'selected'		 => $values['order_category'])),
				'lang_name'							 => lang('name'),
				'lang_name_statustext'				 => lang('name'),
				'value_name'						 => $values['name'],
				'lang_descr'						 => lang('descr'),
				'lang_descr_statustext'				 => lang('descr'),
				'value_descr'						 => $values['descr'],
				'table_add'							 => $table_add,
				'values'							 => $content,
				'table_header'						 => $table_header,
				'acl_manage'						 => $this->acl_manage,
				'table_update'						 => $table_update,
				'update_action'						 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uis_agreement.edit',
					'id'		 => $id)),
				'lang_select_all'					 => lang('Select All'),
				'img_check'							 => $GLOBALS['phpgw']->common->get_image_path('property') . '/check.png',
				'set_column'						 => $set_column,
				'lang_import_detail'				 => lang('import detail'),
				'lang_detail_import_statustext'		 => lang('import details to this agreement from spreadsheet'),
				'lang_import'						 => lang('import'),
				'textareacols'						 => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'						 => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs'								 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'							 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('service agreement') . ': ' . ($id ? lang('edit') . ' ' . lang($this->role) : lang('add') . ' ' . lang($this->role));

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::add_javascript('property', 'portico', 's_agreement.edit.js');
			self::render_template_xsl(array('s_agreement', 'datatable_inline', 'files', 'attributes_form'), array(
				'edit' => $data));
		}

		function get_vendor_member_info( $vendor_id = 0 )
		{
			if (!$vendor_id)
			{
				$vendor_id = phpgw::get_var('vendor_id', 'int');
			}
			$generic		 = CreateObject('property.bogeneric');
			$generic->get_location_info('vendor');
			$vendor			 = $generic->read_single(array('id' => $vendor_id));
			$member_of		 = explode(',', trim($vendor['member_of'], ','));
			$member_of_data	 = $this->cats->formatted_xslt_list(array('selected'	 => $member_of,
				'globals'	 => true));
			return isset($member_of_data['cat_list']) && $member_of_data['cat_list'] ? $member_of_data['cat_list'] : array();
		}

		function download()
		{
			if (!$this->acl_read)
			{
				return;
			}
			$id = phpgw::get_var('id');
			if ($id)
			{
				$list = $this->bo->read_details($id);
			}
			else
			{
				$list = $this->bo->read();
			}
			$uicols = $this->bo->uicols;
			$this->bocommon->download($list, $uicols['name'], $uicols['descr'], $uicols['input_type']);
		}

		function get_contentitem()
		{
			$s_agreement_id	 = phpgw::get_var('s_agreement_id', 'int');
			$id				 = phpgw::get_var('item_id', 'int');

			if (empty($s_agreement_id) || empty($id))
			{
				$result_data					 = array('results' => array());
				$result_data['total_records']	 = 0;
				$result_data['draw']			 = 0;

				return $this->jquery_results($result_data);
			}

			$list		 = $this->bo->read_prizing(array('s_agreement_id' => $s_agreement_id, 'item_id' => $id));
			$uicols		 = $this->bo->uicols;
			$list		 = $this->list_content($list, $uicols, $edit_item	 = true);
			$content	 = $list['content'];

			$content_values	 = array();
			$hidden			 = '';

			for ($y = 0; $y < count($content); $y++)
			{
				for ($z = 0; $z < count($content[$y]['row']); $z++)
				{
					if ($content[$y]['row'][$z]['name'] != '')
					{
						$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
					}
				}
			}

			$hidden	 .= " <input name='values[select][0]' type='hidden' value='" . $content_values[$y - 1]['item_id'] . "' id='selidsul' />";
			$hidden	 .= " <input name='values[select][" . $content_values[$y - 1]['item_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['cost'] . "' id='mcostul' />";
			$hidden	 .= " <input name='values[id][" . $content_values[$y - 1]['item_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['index_count'] . "' id='icountul'/>";

			$content_values[$y - 1]['index_date'] .= $hidden;

			$total_records = count($content_values);

			$result_data = array('results' => $content_values);

			$result_data['total_records']	 = $total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}

		function edit_item()
		{
			if (!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $this->acl_location));
			}

			$s_agreement_id	 = phpgw::get_var('s_agreement_id'); // in case of bigint
			$id				 = phpgw::get_var('id', 'int');
			$values			 = phpgw::get_var('values');
			$delete_last	 = phpgw::get_var('delete_last', 'bool', 'GET');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			if ($delete_last)
			{
				$this->bo->delete_last_index($s_agreement_id, $id);
			}

			$bolocation = CreateObject('property.bolocation');

			$values_attribute = phpgw::get_var('values_attribute');

//			$GLOBALS['phpgw']->xslttpl->add_file(array('s_agreement','attributes_form'));

			if (is_array($values))
			{
				$insert_record			 = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
				$insert_record_entity	 = $GLOBALS['phpgw']->session->appsession('insert_record_entity', 'property');

				$insert_record_s_agreement1 = $GLOBALS['phpgw']->session->appsession('insert_record_values.s_agreement.detail', 'property');
				//_debug_array($insert_record_s_agreement1);

				for ($j = 0; $j < count($insert_record_entity); $j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]] = $insert_record_entity[$j];
				}

				for ($j = 0; $j < count($insert_record_s_agreement1); $j++)
				{
					$insert_record['extra'][$insert_record_s_agreement1[$j]] = $insert_record_s_agreement1[$j];
				}

				$values = $this->bocommon->collect_locationdata($values, $insert_record);
				//_debug_array($values);
				if ($values['save'] || $values['apply'])
				{

					if (!$receipt['error'])
					{
						$values['s_agreement_id']	 = $s_agreement_id;
						$values['id']				 = $id;
						$receipt					 = $this->bo->save_item($values, $values_attribute);
						$s_agreement_id				 = $receipt['s_agreement_id'];
						$id							 = $receipt['id'];
						$this->cat_id				 = ($values['cat_id'] ? $values['cat_id'] : $this->cat_id);

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', 's_agreement_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uis_agreement.edit',
								'id'		 => $s_agreement_id));
						}
					}
					else
					{
						if ($values['location'])
						{
							$location_code			 = implode("-", $values['location']);
							$values['location_data'] = $bolocation->read_single($location_code, $values['extra']);
						}
						if ($values['extra']['p_num'])
						{
							$values['p'][$values['extra']['p_entity_id']]['p_num']		 = $values['extra']['p_num'];
							$values['p'][$values['extra']['p_entity_id']]['p_entity_id'] = $values['extra']['p_entity_id'];
							$values['p'][$values['extra']['p_entity_id']]['p_cat_id']	 = $values['extra']['p_cat_id'];
							$values['p'][$values['extra']['p_entity_id']]['p_cat_name']	 = phpgw::get_var('entity_cat_name_' . $values['extra']['p_entity_id'], 'string', 'POST');
						}
					}
				}
				else if ($values['update'])
				{
					if (!$values['date'])
					{
						$receipt['error'][] = array('msg' => lang('Please select a date !'));
					}
					if (!$values['new_index'])
					{
						$receipt['error'][] = array('msg' => lang('Please enter a index !'));
					}

					if (!$receipt['error'])
					{
						$receipt = $this->bo->update($values);
					}
				}
				else if (!$values['save'] && !$values['apply'] && !$values['update'])
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uis_agreement.edit',
						'id'		 => $s_agreement_id));
				}
			}

			$s_agreement = $this->bo->read_single(array('s_agreement_id' => $s_agreement_id,
				'view'			 => true));
			$values		 = $this->bo->read_single_item(array('s_agreement_id' => $s_agreement_id,
				'id'			 => $id));

			$link_data = array
				(
				'menuaction'	 => 'property.uis_agreement.edit_item',
				's_agreement_id' => $s_agreement_id,
				'id'			 => $id,
				'role'			 => $this->role
			);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$member_of_data = $this->cats->formatted_xslt_list(array('selected'	 => $this->member_id,
				'globals'	 => true, link_data	 => array()));

			$table_add[] = array
				(
				'lang_add'				 => lang('add detail'),
				'lang_add_standardtext'	 => lang('add an item to the details'),
				'add_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.edit_item',
					's_agreement_id' => $s_agreement_id))
			);


			if ($id)
			{
				$list = $this->bo->read_prizing(array('s_agreement_id' => $s_agreement_id, 'item_id' => $id));
				$GLOBALS['phpgw']->jqcal->add_listener('values_date');
			}

			$uicols			 = $this->bo->uicols;
			$list			 = $this->list_content($list, $uicols, $edit_item		 = true);
			$content		 = $list['content'];
			$table_header	 = $list['table_header'];

			//------JSON code-------------------
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$content_values	 = array();
				$hidden			 = '';

				for ($y = 0; $y < count($content); $y++)
				{
					for ($z = 0; $z < count($content[$y]['row']); $z++)
					{
						if ($content[$y]['row'][$z]['name'] != '')
						{
							$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
						}
					}
				}

				$hidden	 .= " <input name='values[select][" . $content_values[$y - 1]['item_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['cost'] . "'/>";
				$hidden	 .= " <input name='values[id][" . $content_values[$y - 1]['item_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['index_count'] . "'/>";

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
			//--------------------JSON code-----


			for ($i = 0; $i < count($list['content'][0]['row']); $i++)
			{
				$set_column[] = true;
			}
			//_debug_array($list);

			$table_update[] = array
				(
				'lang_new_index'			 => lang('New index'),
				'lang_new_index_statustext'	 => lang('Enter a new index'),
				'lang_date_statustext'		 => lang('Select the date for the update'),
				'lang_update'				 => lang('Update'),
				'lang_update_statustext'	 => lang('update selected investments')
			);


			$lookup_type = 'form';

			//_debug_array($values);
			$location_data = $bolocation->initiate_ui_location(array
				(
				'values'		 => $values['location_data'],
				'type_id'		 => -1, // calculated from location_types
				'no_link'		 => false, // disable lookup links for location type less than type_id
				'tenant'		 => false,
				'lookup_type'	 => $lookup_type,
				'lookup_entity'	 => $this->bocommon->get_lookup_entity('s_agreement'),
				'entity_data'	 => $values['p']
			));

			self::add_javascript('property', 'overlib', 'overlib.js');
			self::add_javascript('property', 'core', 'check.js');


			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if ($attribute['history'] == true)
					{
						$link_history_data = array
							(
							'menuaction'	 => 'property.uientity.attrib_history',
							'acl_location'	 => '.s_agreement',
							'id'			 => $s_agreement_id,
							'attrib_id'		 => $attribute['id'],
							'detail_id'		 => $id,
							'edit'			 => true,
							'role'			 => 'detail'
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php', $link_history_data);
					}
				}


				$location			 = $this->acl_location . '.detail';
				$attributes_groups	 = $this->bo->get_attribute_groups($location, $values['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if (isset($group['attributes']))
					{
						$attributes[] = $group;
					}
				}
				unset($attributes_groups);
				unset($s_agreement['attributes']);
			}

			$content_values = array();

			for ($y = 0; $y < count($content); $y++)
			{
				for ($z = 0; $z < count($content[$y]['row']); $z++)
				{
					if ($content[$y]['row'][$z]['name'] != '')
					{
						$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
					}
				}
			}

			$hidden = '';

			for ($y = 0; $y < count($content); $y++)
			{
				for ($z = 0; $z < count($content[$y]['row']); $z++)
				{
					if ($content[$y]['row'][$z]['name'] != '')
					{
						$content_values[$y][$content[$y]['row'][$z]['name']] = $content[$y]['row'][$z]['value'];
					}
				}
			}
			$hidden	 .= " <input name='values[select][" . $content_values[$y - 1]['item_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['cost'] . "'/>";
			$hidden	 .= " <input name='values[id][" . $content_values[$y - 1]['item_id'] . "]'  type='hidden' value='" . $content_values[$y - 1]['index_count'] . "'/>";

			$content_values[$y - 1]['index_date'] .= $hidden;


			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 's_agreement_id',
						'source' => $s_agreement_id,
						'ready'	 => 1
					),
					array
						(
						'name'	 => 'from',
						'source' => $view_only ? 'view' : 'edit',
						'ready'	 => 1
					)
				)
			);


			/* REQUIRES VALIDATION OF PERMISSIONS */
			$permissions['rowactions'][] = array
				(
				'text'		 => lang('View'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uis_agreement.view_item'
				)),
				'parameters' => $parameters
			);

			$tabletools = array
				(
				array('my_name'	 => 'view', 'text'		 => lang('View'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.view_item',
						's_agreement_id' => $s_agreement_id, 'from'			 => 'edit'))),
			);

			$myColumnDefs0 = array
				(
				array('key' => 'item_id', 'label' => lang('ID'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'cost', 'label' => lang('Cost'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'this_index', 'label' => lang('index'), 'sortable' => true, 'resizeable' => true),
				array('key'		 => 'index_count', 'label'		 => lang('index_count'), 'sortable'	 => true,
					'resizeable' => true),
				array('key' => 'index_date', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction'		 => 'property.uis_agreement.get_contentitem',
						's_agreement_id'	 => $s_agreement_id, 'item_id'			 => $id, 'phpgw_return_as'	 => 'json'))),
				'data'		 => json_encode(array()),
				'tabletools' => $tabletools,
				'ColumnDefs' => $myColumnDefs0,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$data = array
				(
				'datatable_def'					 => $datatable_def,
				'base_java_url'					 => json_encode(array('menuaction'	 => "property.uis_agreement.edit_item",
					'id'			 => $id, 's_agreement_id' => $s_agreement_id)),
				'datatable'						 => $datavalues,
				'myColumnDefs'					 => $myColumnDefs,
				'myButtons'						 => $myButtons,
				'msgbox_data'					 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'						 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_id'						 => lang('ID'),
				'value_id'						 => $values['id'],
				'value_s_agreement_id'			 => $s_agreement_id,
				'lang_category'					 => lang('category'),
				'lang_save'						 => lang('save'),
				'lang_cancel'					 => lang('cancel'),
				'lang_apply'					 => lang('apply'),
				'lang_apply_statustext'			 => lang('Apply the values'),
				'lang_cancel_statustext'		 => lang('Leave the service agreement untouched and return back to the list'),
				'lang_save_statustext'			 => lang('Save the service agreement and return back to the list'),
				'attributes_group'				 => $attributes,
				'lookup_functions'				 => $values['lookup_functions'],
				'lang_agreement'				 => lang('Agreement'),
				'agreement_name'				 => $s_agreement['name'],
				'table_add'						 => $table_add,
				'values'						 => $content,
				'table_header'					 => $table_header,
				'acl_manage'					 => $this->acl_manage,
				'table_update'					 => $table_update,
				'update_action'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.edit_item',
					's_agreement_id' => $s_agreement_id, 'id'			 => $id)),
				'lang_select_all'				 => lang('Select All'),
				'img_check'						 => $GLOBALS['phpgw']->common->get_image_path('property') . '/check.png',
				'location_data'					 => $location_data,
				'lang_cost'						 => lang('cost'),
				'lang_cost_statustext'			 => lang('cost'),
				'value_cost'					 => $values['cost'],
				'set_column'					 => $set_column,
				'lang_delete_last'				 => lang('delete last index'),
				'lang_delete_last_statustext'	 => lang('delete the last index'),
				'delete_action'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.edit_item',
					'delete_last'	 => 1, 's_agreement_id' => $s_agreement_id, 'id'			 => $id)),
				'lang_history'					 => lang('history'),
				'lang_history_help'				 => lang('history of this attribute'),
				'lang_history_date_statustext'	 => lang('Enter the date for this reading'),
				'textareacols'					 => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'					 => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs'							 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'						 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('service agreement') . ': ' . ($values['id'] ? lang('edit item') . ' ' . $s_agreement['name'] : lang('add item') . ' ' . $s_agreement['name']);

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('property', 'portico', 's_agreement.edit.js');
			self::render_template_xsl(array('s_agreement', 'datatable_inline', 'attributes_form'), array(
				'edit_item' => $data));
		}

		function view_item()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$from			 = phpgw::get_var('from');
			$from			 = $from == 'edit' ? 'edit' : 'view';
			$s_agreement_id	 = phpgw::get_var('s_agreement_id'); // in case of bigint
			$id				 = phpgw::get_var('id', 'int');
			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';
			$bolocation		 = CreateObject('property.bolocation');

			$s_agreement = $this->bo->read_single(array('s_agreement_id' => $s_agreement_id,
				'view'			 => true));
			$values		 = $this->bo->read_single_item(array('s_agreement_id' => $s_agreement_id,
				'id'			 => $id));

			$link_data = array
				(
				'menuaction' => 'property.uis_agreement.' . $from,
				'id'		 => $s_agreement_id
			);

			$dateformat						 = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep							 = '/';
			$dlarr[strpos($dateformat, 'y')] = 'yyyy';
			$dlarr[strpos($dateformat, 'm')] = 'MM';
			$dlarr[strpos($dateformat, 'd')] = 'DD';
			ksort($dlarr);

			$dateformat = (implode($sep, $dlarr));

			if ($id)
			{
				$list = $this->bo->read_prizing(array('s_agreement_id' => $s_agreement_id, 'item_id' => $id));
			}

			$uicols			 = $this->bo->uicols;
			$list			 = $this->list_content($list, $uicols, $edit_item		 = true);
			$content		 = $list['content'];
			$table_header	 = $list['table_header'];

			//_debug_array($table_header[0]['header']); die;

			$lookup_type = 'view';

			$location_data = $bolocation->initiate_ui_location(array
				(
				'values'		 => $values['location_data'],
				'type_id'		 => -1, // calculated from location_types
				'no_link'		 => false, // disable lookup links for location type less than type_id
				'tenant'		 => false,
				'lookup_type'	 => $lookup_type,
				'lookup_entity'	 => $this->bocommon->get_lookup_entity('s_agreement'),
				'entity_data'	 => $values['p']
			));


			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if ($attribute['history'] == true)
					{
						$link_history_data = array
							(
							'menuaction'	 => 'property.uientity.attrib_history',
							'acl_location'	 => '.s_agreement',
							'id'			 => $s_agreement_id,
							'attrib_id'		 => $attribute['id'],
							'detail_id'		 => $id,
							'edit'			 => false,
							'role'			 => 'detail'
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php', $link_history_data);
					}
				}
				$location			 = $this->acl_location . '.detail';
				$attributes_groups	 = $this->bo->get_attribute_groups($location, $values['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if (isset($group['attributes']))
					{
						$attributes[] = $group;
					}
				}
				unset($attributes_groups);
				unset($s_agreement['attributes']);
			}

			self::add_javascript('property', 'overlib', 'overlib.js');
			self::add_javascript('property', 'core', 'check.js');

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 's_agreement_id',
						'source' => $s_agreement_id,
						'ready'	 => 1
					),
					array
						(
						'name'	 => 'from',
						'source' => 'edit',
						'ready'	 => 1
					)
				)
			);

			$permissions['rowactions'][] = array
				(
				'text'		 => lang('View'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uis_agreement.view_item'
				)),
				'parameters' => $parameters
			);

			$content_values = array();

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
			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'id',
						'source' => 'item_id'
					),
				)
			);

			$tabletools = array
				(
				array('my_name'	 => 'view', 'text'		 => lang('View'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.view_item',
						's_agreement_id' => $s_agreement_id, 'from'			 => 'edit'))),
			);

			$myColumnDefs0 = array
				(
				array('key'		 => 'item_id', 'label'		 => $table_header[0]['header'], 'sortable'	 => true,
					'resizeable' => true, 'width'		 => 140),
				array('key'		 => 'cost', 'label'		 => $table_header[2]['header'], 'sortable'	 => true,
					'resizeable' => true, 'width'		 => 340),
				array('key'		 => 'this_index', 'label'		 => $table_header[3]['header'], 'sortable'	 => true,
					'resizeable' => true, 'width'		 => 200),
				array('key'		 => 'index_count', 'label'		 => $table_header[4]['header'], 'sortable'	 => true,
					'resizeable' => true, 'formatter'	 => 'JqueryPortico.FormatterCenter', 'width'		 => 60),
				array('key'		 => 'index_date', 'label'		 => $table_header[5]['header'], 'sortable'	 => true,
					'resizeable' => true, 'formatter'	 => 'JqueryPortico.FormatterCenter', 'width'		 => 60)
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'data'		 => json_encode($content_values),
				'tabletools' => $tabletools,
				'ColumnDefs' => $myColumnDefs0,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$data = array
				(
				'datatable_def'			 => $datatable_def,
				'base_java_url'			 => json_encode(array('menuaction' => "property.uis_agreement.view_item")),
				'datatable'				 => $datavalues,
				'myColumnDefs'			 => $myColumnDefs,
				'msgbox_data'			 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'				 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_id'				 => lang('ID'),
				'value_id'				 => $values['id'],
				'value_s_agreement_id'	 => $s_agreement_id,
				'lang_category'			 => lang('category'),
				'lang_cancel'			 => lang('cancel'),
				'lang_cancel_statustext' => lang('Leave the service agreement untouched and return back to the list'),
				'lang_dateformat'		 => lang(strtolower($dateformat)),
				'attributes_group'		 => $attributes,
				'lang_agreement'		 => lang('Agreement'),
				'agreement_name'		 => $s_agreement['name'],
				'table_add'				 => $table_add,
				'values'				 => $content,
				'table_header'			 => $table_header,
				'location_data'			 => $location_data,
				'lang_cost'				 => lang('cost'),
				'lang_cost_statustext'	 => lang('cost'),
				'value_cost'			 => $values['cost'],
				'set_column'			 => $set_column,
				'lang_history'			 => lang('history'),
				'lang_history_help'		 => lang('history of this attribute'),
				'textareacols'			 => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'			 => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs'					 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'				 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date',
					'security', 'file'))
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('service agreement') . ': ' . lang('view item') . ' ' . $s_agreement['name'];
			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl(array('s_agreement', 'datatable_inline', 'attributes_view'), array(
				'view_item' => $data));
		}

		function delete()
		{
			if (!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 8, 'acl_location'	 => $this->acl_location));
			}

			$s_agreement_id	 = phpgw::get_var('s_agreement_id'); // in case of bigint
			$confirm		 = phpgw::get_var('confirm', 'bool', 'POST');

			//json code delete
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($s_agreement_id);
				return "s_agreement_id " . $s_agreement_id . " " . lang("has been deleted");
			}

			$link_data = array
				(
				'menuaction' => 'property.uis_agreement.index',
				'role'		 => $this->role
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.delete',
					's_agreement_id' => $s_agreement_id, 'role'			 => $this->role)),
				'lang_confirm_msg'		 => lang('do you really want to delete this entry'),
				'lang_yes'				 => lang('yes'),
				'lang_yes_statustext'	 => lang('Delete the entry'),
				'lang_no_statustext'	 => lang('Back to the list'),
				'lang_no'				 => lang('no')
			);

			$appname		 = lang('service agreement');
			$function_msg	 = lang('delete') . ' ' . lang($this->role);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{

			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$s_agreement_id = phpgw::get_var('id'); // in case of bigint

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';
			$tabs['items']	 = array('label' => lang('items'), 'link' => "#items");

			$s_agreement = $this->bo->read_single(array('s_agreement_id' => $s_agreement_id));


			if ($s_agreement_id)
			{
				$this->cat_id	 = ($s_agreement['cat_id'] ? $s_agreement['cat_id'] : $this->cat_id);
				$this->member_id = ($s_agreement['member_of'] ? $s_agreement['member_of'] : $this->member_id);
				$list			 = $this->bo->read_details($s_agreement_id);
				$total_records	 = count($list);

				$uicols			 = $this->bo->uicols;
				$list			 = $this->list_content($list, $uicols, $edit_item		 = false, $view_only		 = 'view');
				$content		 = $list['content'];
				$table_header	 = $list['table_header'];
			}

			$link_data = array
				(
				'menuaction'	 => 'property.uis_agreement.index',
				's_agreement_id' => $s_agreement_id,
			);

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array
				(
				'vendor_id'		 => $s_agreement['vendor_id'],
				'vendor_name'	 => $s_agreement['vendor_name'],
				'type'			 => 'view'
				)
			);

			$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array
				(
				'b_account_id'	 => $s_agreement['b_account_id'],
				'b_account_name' => $s_agreement['b_account_name'],
				'type'			 => 'view'
				)
			);


			$alarm_data = $this->bocommon->initiate_ui_alarm(array
				(
				'acl_location'	 => $this->acl_location,
				'alarm_type'	 => 's_agreement',
				'type'			 => 'view',
				'text'			 => 'Email notification',
				'times'			 => $times,
				'id'			 => $s_agreement_id,
				'method'		 => $method,
				'data'			 => $data,
				'account_id'	 => $account_id
				)
			);


			$dateformat						 = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep							 = '/';
			$dlarr[strpos($dateformat, 'y')] = 'yyyy';
			$dlarr[strpos($dateformat, 'm')] = 'MM';
			$dlarr[strpos($dateformat, 'd')] = 'DD';
			ksort($dlarr);

			$dateformat = (implode($sep, $dlarr));

			$member_of_data = $this->cats->formatted_xslt_list(array('selected'	 => $this->member_id,
				'globals'	 => true, link_data	 => array()));

			$link_file_data = array
				(
				'menuaction' => 'property.uis_agreement.view_file',
				'id'		 => $s_agreement_id
			);

			$j = count($s_agreement['files']);
			for ($i = 0; $i < $j; $i++)
			{
				$s_agreement['files'][$i]['file_name'] = urlencode($s_agreement['files'][$i]['name']);
			}


			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'id',
						'source' => 'item_id'
					)
				)
			);

			$permissions['rowactions'][] = array
				(
				'text'		 => lang('View'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uis_agreement.view_item'
				)),
				'parameters' => $parameters
			);


			$myColumnDefs0 = array
				(
				array('key'		 => 'time', 'label'		 => $alarm_data['header'][0]['lang_time'],
					'sortable'	 => true,
					'resizeable' => true, 'width'		 => 140),
				array('key'		 => 'text', 'label'		 => $alarm_data['header'][0]['lang_text'],
					'sortable'	 => true,
					'resizeable' => true, 'width'		 => 340),
				array('key'		 => 'user', 'label'		 => $alarm_data['header'][0]['lang_user'],
					'sortable'	 => true,
					'resizeable' => true, 'width'		 => 200),
				array('key'		 => 'enabled', 'label'		 => $alarm_data['header'][0]['lang_enabled'],
					'sortable'	 => true, 'resizeable' => true, 'formatter'	 => 'JqueryPortico.FormatterCenter',
					'width'		 => 60),
				array('key'		 => 'alarm_id', 'label'		 => "dummy", 'sortable'	 => true, 'resizeable' => true,
					'hidden'	 => true)
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'data'		 => json_encode($alarm_data['values']),
				'ColumnDefs' => $myColumnDefs0,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$content_values = array();

			for ($y = 0; $y < count($content); $y++)
			{
				for ($z = 0; $z < count($content[$y]['row']); $z++)
				{
					if ($content[$y]['row'][$z + 1]['name'] != '')
					{
						$content_values[$y][$content[$y]['row'][$z + 1]['name']] = $content[$y]['row'][$z + 1]['value'];
					}
				}
			}


			$td_count		 = 0;
			$ColumnDefs_data = array();
			if (isset($uicols['input_type']) && $uicols['input_type'])
			{
				foreach ($uicols['input_type'] as $key => $input_type)
				{
					if ($input_type != 'hidden')
					{
						$ColumnDefs_data[] = array
							(
							'key'		 => $uicols['name'][$key],
							'label'		 => $uicols['descr'][$key],
							'sortable'	 => true,
							'resizeable' => true
						);
						$td_count ++;
					}
				}
			}

			$tabletools = array
				(
				array('my_name'	 => 'view', 'text'		 => lang('View'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uis_agreement.view_item',
						's_agreement_id' => $s_agreement_id, 'from'			 => 'view')), 'parameters' => json_encode($parameters)),
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_1',
				'requestUrl' => "''",
				'data'		 => json_encode($content_values),
				'tabletools' => $tabletools,
				'ColumnDefs' => $ColumnDefs_data,
				'config'	 => array(
					array('disableFilter' => true),
				)
			);


			unset($ColumnDefs_data);


			//---datatable2 settings---------------------------------------------------

			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			for ($z = 0; $z < count($s_agreement['files']); $z++)
			{
				$content_files[$z]['file_name'] = '<a href="' . $link_view_file . '&amp;file_id=' . $s_agreement['files'][$z]['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $s_agreement['files'][$z]['name'] . '</a>';
			}

			$myColumnDefs2 = array
				(
				array('key'		 => 'file_name', 'label'		 => lang('Filename'), 'sortable'	 => false,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_2',
				'requestUrl' => "''",
				'data'		 => json_encode($content_files),
				'ColumnDefs' => $myColumnDefs2,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$content_budget = $this->bo->get_budget($s_agreement_id);

			$myColumnDefs3 = array
				(
				array('key' => 'year', 'label' => lang('year'), 'sortable' => false, 'resizeable' => true),
				array('key'		 => 'category', 'label'		 => lang('category'), 'sortable'	 => false,
					'resizeable' => true),
				array('key' => 'ecodimb', 'label' => lang('dimb'), 'sortable' => false, 'resizeable' => true),
				array('key'		 => 'budget_account', 'label'		 => lang('budget account'), 'sortable'	 => false,
					'resizeable' => true),
				array('key' => 'budget', 'label' => lang('budget'), 'sortable' => false, 'resizeable' => true),
				array('key'		 => 'actual_cost', 'label'		 => lang('actual cost'), 'sortable'	 => false,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_3',
				'requestUrl' => "''",
				'data'		 => json_encode($content_budget),
				'ColumnDefs' => $myColumnDefs3,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			if (isset($values['attributes']) && is_array($values['attributes']))
			{

				$location			 = $this->acl_location;
				$attributes_groups	 = $this->bo->get_attribute_groups($location, $values['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if (isset($group['attributes']))
					{
						$attributes[] = $group;
					}
				}
				unset($attributes_groups);
				unset($values['attributes']);
			}

			$data = array
				(
				'datatable_def'				 => $datatable_def,
				'lang_budget'				 => lang('budget'),
				'base_java_url'				 => json_encode(array('menuaction' => "property.uis_agreement.view")),
				'datatable'					 => $datavalues,
				'myColumnDefs'				 => $myColumnDefs,
				'lang_total_records'		 => lang('Total'),
				'total_records'				 => $total_records,
				'alarm_data'				 => $alarm_data,
				'lang_alarm'				 => lang('Alarm'),
				'link_view_file'			 => $GLOBALS['phpgw']->link('/index.php', $link_file_data),
				'link_to_files'				 => $link_to_files,
				'files'						 => $s_agreement['files'],
				'lang_files'				 => lang('files'),
				'lang_filename'				 => lang('Filename'),
				'lang_view_file_statustext'	 => lang('click to view file'),
				'edit_url'					 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_id'					 => lang('ID'),
				'value_s_agreement_id'		 => $s_agreement_id,
				'lang_category'				 => lang('category'),
				'lang_save'					 => lang('save'),
				'lang_cancel'				 => lang('done'),
				'lang_apply'				 => lang('apply'),
				'value_cat'					 => $s_agreement['cat'],
				'lang_cancel_statustext'	 => lang('return back to the list'),
				'cat_list'					 => $this->bocommon->select_category_list(array('format'	 => 'select',
					'selected'	 => $this->cat_id, 'type'		 => 's_agreement', 'order'		 => 'descr')),
				'lang_member_of'			 => lang('member of'),
				'member_of_name'			 => 'member_id',
				'member_of_list'			 => $member_of_data['cat_list'],
				'lang_dateformat'			 => lang(strtolower($dateformat)),
//				'attributes_view' => $s_agreement['attributes'],
				'attributes_group'			 => $attributes,
				'dateformat'				 => $dateformat,
				'lang_start_date'			 => lang('start date'),
				'value_start_date'			 => $s_agreement['start_date'],
				'lang_end_date'				 => lang('end date'),
				'value_end_date'			 => $s_agreement['end_date'],
				'lang_termination_date'		 => lang('termination date'),
				'value_termination_date'	 => $s_agreement['termination_date'],
				'vendor_data'				 => $vendor_data,
				'b_account_data'			 => $b_account_data,
				'lang_name'					 => lang('name'),
				'value_name'				 => $s_agreement['name'],
				'lang_descr'				 => lang('descr'),
				'value_descr'				 => $s_agreement['descr'],
				'table_add'					 => $table_add,
				'values'					 => $content,
				'table_header'				 => $table_header,
				'textareacols'				 => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				 => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs'						 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'					 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date',
					'security', 'file'))
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('service agreement') . ': ' . lang('view');

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl(array('s_agreement', 'datatable_inline', 'files', 'attributes_view'), array(
				'view' => $data));
		}
	}