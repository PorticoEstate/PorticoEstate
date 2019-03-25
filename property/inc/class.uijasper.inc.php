<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage admin
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class property_uijasper extends phpgwapi_uicommon_jquery
	{

		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $allrows;
		var $public_functions = array
			(
			'query'		 => true,
			'index'		 => true,
			'edit'		 => true,
			'delete'	 => true,
			'download'	 => true,
			'view'		 => true,
			'get_file'	 => true,
			'save'		 => true
		);

		function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['xslt_app']			 = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'property::jasper';

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		 = CreateObject('property.bojasper', true);
			$this->bocommon	 = CreateObject('property.bocommon');

			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.jasper';
			$this->acl_read		 = $this->acl->check('.jasper', PHPGW_ACL_READ, 'property');
			$this->acl_add		 = $this->acl->check('.jasper', PHPGW_ACL_ADD, 'property');
			$this->acl_edit		 = $this->acl->check('.jasper', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete	 = $this->acl->check('.jasper', PHPGW_ACL_DELETE, 'property');
			$this->grants		 = & $this->bo->grants;

			$this->start	 = $this->bo->start;
			$this->query	 = $this->bo->query;
			$this->sort		 = $this->bo->sort;
			$this->order	 = $this->bo->order;
			$this->allrows	 = $this->bo->allrows;
			$this->app		 = $this->bo->app;
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start'		 => $this->start,
				'query'		 => $this->query,
				'sort'		 => $this->sort,
				'order'		 => $this->order,
				'allrows'	 => $this->allrows,
				'app'		 => $this->app,
			);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$list				 = $this->bo->read();
			$uicols				 = array();
			$uicols['name'][]	 = 'id';
			$uicols['descr'][]	 = lang('id');
			$uicols['name'][]	 = 'title';
			$uicols['descr'][]	 = lang('title');
			$uicols['name'][]	 = 'descr';
			$uicols['descr'][]	 = lang('Description');
			$uicols['name'][]	 = 'file_name';
			$uicols['descr'][]	 = lang('filename');
			$uicols['name'][]	 = 'location';
			$uicols['descr'][]	 = lang('location');
			$uicols['name'][]	 = 'user';
			$uicols['descr'][]	 = lang('user');
			$uicols['name'][]	 = 'entry_date';
			$uicols['descr'][]	 = lang('entry date');
			$uicols['name'][]	 = 'access';
			$uicols['descr'][]	 = lang('access');
			$uicols['name'][]	 = 'formats';
			$uicols['descr'][]	 = lang('formats');

			$this->bocommon->download($list, $uicols['name'], $uicols['descr'], $uicols['input_type']);
		}

		function get_file()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$jasper_id	 = phpgw::get_var('jasper_id', 'int');
			$values		 = $this->bo->read_single($jasper_id);

			$bofiles = CreateObject('property.bofiles');
			$file	 = "{$bofiles->fakebase}/jasper/{$jasper_id}/{$values['file_name']}";
			$bofiles->view_file('', $file);
		}

		private function _get_Filters()
		{
			$values_combo_box	 = array();
			$combos				 = array();

			$values_combo_box[0] = $this->bo->get_apps();
			//echo '<pre>';print_r($values_combo_box[0]); echo '</pre>'; exit('saul');
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'app',
				'text'	 => lang('App'),
				'list'	 => $values_combo_box[0]
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

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = 'JasperReport';
			$function_msg	 = lang('list report definitions');

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
						'menuaction'		 => 'property.uijasper.index',
						'app'				 => $this->app,
						'phpgw_return_as'	 => 'json'
					)),
					'download'		 => self::link(array(
						'menuaction'	 => 'property.uijasper.download',
						'export'		 => true,
						'skip_origin'	 => true,
						'allrows'		 => true
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uijasper.edit',
						'app'		 => $this->app
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'id',
							'label'		 => lang('id'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'title',
							'label'		 => lang('title'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'descr',
							'label'		 => lang('Description'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'file_name',
							'label'		 => lang('filename'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'location',
							'label'		 => lang('location'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'user',
							'label'		 => lang('user'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'entry_date',
							'label'		 => lang('entry date'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'access',
							'label'		 => lang('access'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'formats',
							'label'		 => lang('formats'),
							'sortable'	 => false
						)
					)
				)
			);

			$filters = $this->_get_Filters();
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$jasper_list = $this->bo->read($type);

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

			$parameters_view = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'jasper_id',
						'source' => 'id'
					),
				)
			);

			if ($this->acl_edit)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name'	 => 'edit',
					'statustext' => lang('edit the jasper entry'),
					'text'		 => lang('edit'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uijasper.edit'
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_read)
			{
				$data['datatable']['actions'][]	 = array
					(
					'my_name'	 => 'edit',
					'text'		 => lang('open JasperReport %1 in new window', $report['title']),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uijasper.view',
					)),
					'target'	 => '_blank',
					'parameters' => json_encode($parameters_view)
				);
				$data['datatable']['actions'][]	 = array
					(
					'my_name'	 => 'edit',
					'text'		 => lang('download JasperReport %1 definition', $report['title']),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uijasper.get_file',
					)),
					'target'	 => '_blank',
					'parameters' => json_encode($parameters_view)
				);
			}

			if ($this->acl_delete)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name'		 => 'delete',
					'statustext'	 => lang('delete the jasper entry'),
					'text'			 => lang('delete'),
					'confirm_msg'	 => lang('do you really want to delete this entry'),
					'action'		 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uijasper.delete'
					)),
					'parameters'	 => json_encode($parameters)
				);
			}

			/* $data['datatable']['actions'][] = array
			  (
			  'my_name' 		=> 'add',
			  'text' 			=> lang('add'),
			  'action'		=> $GLOBALS['phpgw']->link('/index.php',array
			  (
			  'menuaction'	=> 'property.uijasper.edit',
			  'app'			=> $this->app
			  ))); */
			unset($parameters);
			unset($parameters_view);

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
				'start'		 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'order'		 => $columns[$order[0]['column']]['data'],
				'sort'		 => $order[0]['dir'],
				'allrows'	 => phpgw::get_var('length', 'int') == -1 || $export,
				'app'		 => $this->app
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

		public function save()
		{
			$id		 = phpgw::get_var('id', 'int');
			$values	 = phpgw::get_var('values');

			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				if ($GLOBALS['phpgw']->session->is_repost())
				{
					//				$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
				}

				if (!isset($values['location']) || !$values['location'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a location!'));
				}

				if (!isset($values['title']) || !$values['title'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter a title!'));
				}

				if ($id)
				{
					$values['id'] = $id;
				}
				else
				{
					$id = $values['id'];
				}

				if (!$receipt['error'])
				{
					try
					{
						$receipt = $this->bo->save($values);
						$id		 = $receipt['id'];

						//-------------start files
						$bofiles = CreateObject('property.bofiles');
						$file	 = array();
						if (isset($_FILES['file']['name']) && $_FILES['file']['name'])
						{
							$file_name			 = str_replace(' ', '_', $_FILES['file']['name']);
							$values['file_name'] = $file_name;

							$to_file = "{$bofiles->fakebase}/jasper/{$id}/{$file_name}";

							if ($old_file = $bofiles->vfs->ls(array(
								'string'	 => "{$bofiles->fakebase}/jasper/{$id}",
								'relatives'	 => Array(RELATIVE_NONE)
								)))
							{
								$bofiles->vfs->rm(array(
									'string'	 => "{$bofiles->fakebase}/jasper/{$id}/{$old_file[0]['name']}",
									'relatives'	 => Array(RELATIVE_NONE)
								));
								$receipt['message'][] = array('msg' => lang('old file %1 removed', $old_file[0]['name']));
							}


							$file = array
								(
								'from_file'	 => $_FILES['file']['tmp_name'],
								'to_file'	 => $to_file
							);


							unset($to_file);


							if ($file)
							{
								$bofiles->create_document_dir("jasper/{$id}");
								$bofiles->vfs->override_acl = 1;

								if ($bofiles->vfs->cp(array(
										'from'		 => $file['from_file'],
										'to'		 => $file['to_file'],
										'relatives'	 => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
								{
									$receipt['message'][] = array('msg' => lang('file %1 uploaded', $file_name));
								}
								else
								{
									$receipt['error'][] = array('msg' => lang('Failed to upload file !'));
								}
								$bofiles->vfs->override_acl = 0;
							}
							unset($file);
							unset($file_name);
						}
						//-------------end files

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', 'jasper_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uijasper.index',
								'app'		 => $this->app));
						}
					}
					catch (Exception $e)
					{
						if ($e)
						{
							phpgwapi_cache::message_set($e->getMessage(), 'error');
							$this->edit($values);
							return;
						}
					}

					self::redirect(array('menuaction' => 'property.uijasper.edit', 'id' => $id));
				}
				else
				{
					if ($id)
					{
						self::message_set($receipt);
						self::redirect(array('menuaction' => 'property.uijasper.edit', 'id' => $id));
					}
					$this->edit();
				}
			}
			else
			{
				$this->edit($values);
			}
		}

		function edit()
		{

			if (!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $this->acl_location));
			}

			$id		 = phpgw::get_var('id', 'int');
			$values	 = phpgw::get_var('values');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';
			if (isset($values['cancel']) && $values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uijasper.index',
					'app'		 => $this->app));
			}

			if ($id)
			{
				$values			 = $this->bo->read_single($id);
				$function_msg	 = lang('edit report');
				if (!$this->bocommon->check_perms2($values['user_id'], $this->grants, PHPGW_ACL_READ))
				{
					phpgw::no_access();
				}
			}
			else
			{
				$function_msg = lang('add report');
			}

			$link_data_cancel = array
				(
				'menuaction' => 'property.uijasper.index',
				'app'		 => $this->app
			);

			$link_data = array
				(
				'menuaction' => 'property.uijasper.save',
				'id'		 => $id,
				'app'		 => $this->app
			);

			$locations			 = $GLOBALS['phpgw']->locations->get_locations(false, $this->app);
			$selected_location	 = isset($values['location']) ? $values['location'] : '';
			if (isset($values['location_id']) && $values['location_id'])
			{
				$locations_info		 = $GLOBALS['phpgw']->locations->get_name($values['location_id']);
				$selected_location	 = $locations_info['location'];
			}

			$location_list = array();
			foreach ($locations as $location => $descr)
			{
				$location_list[] = array
					(
					'id'		 => $location,
					'name'		 => "{$location} [{$descr}]",
					'selected'	 => $location == $selected_location
				);
			}

			$formats			 = isset($values['formats']) && $values['formats'] ? $values['formats'] : array();
			$format_type_list	 = $this->bo->get_format_type_list($formats);

			$type_def = array
				(
				array('key' => 'counter', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'type_name', 'label' => lang('type'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'input_name', 'label' => lang('name'), 'sortable' => true, 'resizeable' => true),
				array('key'		 => 'is_id', 'label'		 => lang('is id'), 'sortable'	 => false, 'resizeable' => true,
					'formatter'	 => 'JqueryPortico.FormatterCenter')
			);

			$inputs = isset($values['input']) && $values['input'] ? $values['input'] : array();

			if ($this->acl_edit)
			{
				$type_def[] = array('key'		 => 'delete_input', 'label'		 => lang('delete'), 'sortable'	 => false,
					'resizeable' => true, 'formatter'	 => 'JqueryPortico.FormatterCenter');
				foreach ($inputs as &$input)
				{
					$_checked				 = $input['is_id'] ? 'checked = "checked"' : '';
					$input['is_id']			 = "<input type='checkbox' name='values[edit_is_id][]' {$_checked} value='" . $input['id'] . "' title='" . lang('Check to set as is id') . "'>";
					$input['delete_input']	 = '<input type="checkbox" name="values[delete_input][]" value="' . $input['id'] . '" title="' . lang('Check to delete input') . '">';
				}
			}

			$datavalues[0] = array
				(
				'name'			 => "0",
				'values'		 => json_encode($inputs),
				'total_records'	 => count($inputs),
				'is_paginator'	 => 0,
				'footer'		 => 0
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'data'		 => json_encode($inputs),
				'ColumnDefs' => $type_def,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);


			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
				'datatable_def'		 => $datatable_def,
				'msgbox_data'		 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'		 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'form_cancel'		 => $GLOBALS['phpgw']->link('/index.php', $link_data_cancel),
				'value_app'			 => $this->app,
				'value_id'			 => $id,
				'value_file_name'	 => $values['file_name'],
				'value_title'		 => $values['title'],
				'value_descr'		 => $values['descr'],
				'value_access'		 => $values['access'],
				'apps_list'			 => $this->bo->get_apps($this->app),
				'input_type_list'	 => $this->bo->get_input_type_list(),
				'format_type_list'	 => $format_type_list,
				'location_list'		 => $location_list,
				'td_count'			 => '""',
				'base_java_url'		 => "{menuaction:'property.uijasper.edit'}",
				'datatable'			 => $datavalues,
				'tabs'				 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'			 => phpgwapi_jquery::formvalidator_generate(array('location', 'date',
					'security', 'file'))
			);


			$appname = 'JasperReports';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::$function_msg::" . lang($this->app);

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl(array('jasper', 'datatable_inline'), array('edit' => $data));
		}

		function view()
		{
			$jasper_id			 = phpgw::get_var('jasper_id');
			$values				 = $this->bo->read_single($jasper_id);
			$values_attribute	 = phpgw::get_var('values_attribute');
			$sel_format			 = phpgw::get_var('sel_format');
			$first_run			 = true;

			if ($values_attribute)
			{
				$values['input'] = $values_attribute;
				$first_run		 = false;
			}
			if (!$this->bocommon->check_perms2($values['user_id'], $this->grants, PHPGW_ACL_READ))
			{
				echo lang('not allowed');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			$user_input = false;


			if ($first_run)
			{
				foreach ($values['input'] as &$input)
				{
					$input['name']	 = $input['input_name'];
					if (!($input['value']	 = phpgw::get_var(strtolower($input['input_name']))) || !$input['is_id'])
					{
						$user_input = true;
					}
				}
				if (isset($values['formats'][1]) && $values['formats'][1])// More than one
				{
					$user_input = true;
				}
			}
			else
			{
				foreach ($values['input'] as &$input)
				{
					$input['name'] = $input['input_name'];
					if (!$input['value'])
					{
						$user_input		 = true;
						$input['name']	 = $input['input_name'];
						if (($input['value']	 = phpgw::get_var(strtolower($input['input_name']))))
						{
							$user_input = false;
						}
					}
					else
					{
						if ($input['datatype'] == 'date')
						{
							$input['value'] = date($GLOBALS['phpgw']->db->date_format(), strtotime($input['value']));
						}
						if ($input['datatype'] == 'timestamp')
						{
							$input['value'] = strtotime($input['value']);
						}
					}
				}
			}
			if (!$user_input)
			{
				$GLOBALS['phpgw_info']['flags']['noheader']	 = true;
				$GLOBALS['phpgw_info']['flags']['nofooter']	 = true;
				$GLOBALS['phpgw_info']['flags']['xslt_app']	 = false;

				$jasper_parameters	 = '';
				$_parameters		 = array();

				foreach ($values['input'] as &$input)
				{

					if (is_array($input['value']))
					{
						$_parameters[] = $input['input_name'] . '|' . implode(',', $input['value']);
					}
					else
					{
						$_parameters[] = $input['input_name'] . '|' . $input['value'];
					}
				}
				if ($_parameters)
				{
					$jasper_parameters = '"' . implode(';', $_parameters) . '"';
				}

				unset($_parameters);

				if ($sel_format)
				{
					$output_type = $sel_format;
				}
				else
				{
					$output_type = isset($values['formats'][0]) && $values['formats'][0] ? $values['formats'][0] : 'PDF';
				}

				$report_source	 = "{$GLOBALS['phpgw_info']['server']['files_dir']}/property/jasper/{$jasper_id}/{$values['file_name']}";
				$jasper_wrapper	 = CreateObject('phpgwapi.jasper_wrapper');

				//_debug_array($jasper_parameters);
				//_debug_array($output_type);
				//_debug_array($report_source);die();

				try
				{
					$jasper_wrapper->execute($jasper_parameters, $output_type, $report_source);
				}
				catch (Exception $e)
				{
					$error = $e->getMessage();
					//FIXME Do something clever with the error
					echo "<H1>{$error}</H1>";
				}
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;

				if (!isset($values['formats'][0]))
				{
					$values['formats'][0] = 'PDF';
				}
				$custom_fields			 = CreateObject('property.custom_fields');
				$values['attributes']	 = $values['input'];
				unset($values['input']);

				$values = $custom_fields->prepare($values);

				$receipt['error'][] = array('msg' => lang('enter input'));

				$function_msg = lang('parameters');

				$link_data = array
					(
					'menuaction' => 'property.uijasper.view',
					'jasper_id'	 => $jasper_id
				);

				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

				$formats = array();
				foreach ($values['formats'] as $format)
				{
					$formats[] = array
						(
						'id'		 => $format,
						'name'		 => $format,
						'selected'	 => $format == $sel_format
					);
				}

				$data = array
					(
					'msgbox_data'		 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'		 => $GLOBALS['phpgw']->link('/index.php', $link_data),
					'attributes'		 => $values['attributes'],
					'lookup_functions'	 => isset($values['lookup_functions']) ? $values['lookup_functions'] : '',
					'formats'			 => $formats
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
				$GLOBALS['phpgw']->xslttpl->add_file(array('jasper'));
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('user_input' => $data));
			}
		}

		function delete()
		{
			if (!$this->acl_delete)
			{
				return lang('not allowed');
			}

			$id		 = phpgw::get_var('id'); // string
			$values	 = $this->bo->read_single($id);
			if (!$this->bocommon->check_perms2($values['user_id'], $this->grants, PHPGW_ACL_DELETE))
			{
				return lang('not allowed');
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$bofiles	 = CreateObject('property.bofiles');
				if ($old_file	 = $bofiles->vfs->ls(array(
					'string'	 => "{$bofiles->fakebase}/jasper/{$id}",
					'relatives'	 => Array(RELATIVE_NONE)
					)))
				{
					$bofiles->vfs->rm(array(
						'string'	 => "{$bofiles->fakebase}/jasper/{$id}/{$old_file[0]['name']}",
						'relatives'	 => Array(RELATIVE_NONE)
					));
					$bofiles->vfs->rm(array(
						'string'	 => "{$bofiles->fakebase}/jasper/{$id}",
						'relatives'	 => Array(RELATIVE_NONE)
					));
					$receipt = lang('file %1 removed', $old_file[0]['name']);
				}

				$this->bo->delete($id);
				$receipt .= " and id $id " . lang("has been deleted");
				return $receipt;
			}
		}
	}