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
	 * @subpackage custom
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uicustom extends phpgwapi_uicommon_jquery
	{

		protected $currentapp,
			$account,
			$bo,
			$bocommon,
			$start,
			$query,
			$sort,
			$order,
			$filter,
			$cat_id,
			$allrows,
			$acl,
			$acl_location,
			$acl_read,
			$acl_add,
			$acl_edit,
			$acl_delete,
			$xsl_rootdir;
		var $public_functions = array
			(
			'query'		 => true,
			'index'		 => true,
			'view'		 => true,
			'edit'		 => true,
			'download'	 => true,
			'delete'	 => true,
			'save'		 => true,
			'query_view' => true,
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app']			 = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'property::custom';

			$this->currentapp	 = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		 = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		 = CreateObject('property.bocustom', true);
			$this->bocommon	 = CreateObject('property.bocommon');

			$this->start	 = $this->bo->start;
			$this->query	 = $this->bo->query;
			$this->sort		 = $this->bo->sort;
			$this->order	 = $this->bo->order;
			$this->filter	 = $this->bo->filter;
			$this->cat_id	 = $this->bo->cat_id;
			$this->allrows	 = $this->bo->allrows;

			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.custom';
			$this->acl_read		 = $this->acl->check('.custom', PHPGW_ACL_READ, $this->currentapp);
			$this->acl_add		 = $this->acl->check('.custom', PHPGW_ACL_ADD, $this->currentapp);
			$this->acl_edit		 = $this->acl->check('.custom', PHPGW_ACL_EDIT, $this->currentapp);
			$this->acl_delete	 = $this->acl->check('.custom', PHPGW_ACL_DELETE, $this->currentapp);
			$this->xsl_rootdir	 = PHPGW_SERVER_ROOT . "/property/templates/base";
			$this->config		 = CreateObject('phpgwapi.config', $this->currentapp);
			$this->config->read();
			if (!empty($this->config->config_data['app_name']))
			{
				$this->lang_app_name = $this->config->config_data['app_name'];
			}
			else
			{
				$this->lang_app_name = lang($this->currentapp);
			}
		}

		function index()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', 'custom_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data', 'custom_receipt', '');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = lang('custom');
			$function_msg	 = lang('list custom');

			$GLOBALS['phpgw_info']['flags']['app_header'] = $this->lang_app_name . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => "{$this->currentapp}.uicustom.index",
						'cat_id'			 => $this->cat_id,
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => "{$this->currentapp}.uicustom.edit"
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'custom_id',
							'label'		 => lang('ID'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'name',
							'label'		 => lang('Name'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'entry_date',
							'label'		 => lang('date'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'user',
							'label'		 => lang('User'),
							'sortable'	 => true
						)
					)
				)
			);

			$list = $this->bo->read(array('dry_run' => true));

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'custom_id',
						'source' => 'custom_id'
					),
				)
			);

			if ($this->acl_read)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name'	 => 'view',
					'statustext' => lang('view the entity'),
					'text'		 => lang('view'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => "{$this->currentapp}.uicustom.view"
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_edit)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name'	 => 'edit',
					'statustext' => lang('edit the actor'),
					'text'		 => lang('edit'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => "{$this->currentapp}.uicustom.edit"
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_delete)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name'		 => 'delete',
					'statustext'	 => lang('delete the actor'),
					'text'			 => lang('delete'),
					'confirm_msg'	 => lang('do you really want to delete this entry'),
					'action'		 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => "{$this->currentapp}.uicustom.delete"
					)),
					'parameters'	 => json_encode($parameters)
				);
			}

			unset($parameters);

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
				'filter'	 => $this->filter,
				'cat_id'	 => $this->cat_id
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
			if (!$_POST)
			{
				return $this->edit();
			}

			$custom_id			 = phpgw::get_var('custom_id', 'int');
			$values				 = phpgw::get_var('values');
			$values['sql_text']	 = $_POST['values']['sql_text'];


			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				if (!$values['name'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter a name !'));
				}

				if (!$values['sql_text'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter a sql query !'));
				}

				if (!$receipt['error'])
				{
					try
					{
						$values['custom_id'] = $custom_id;
						$receipt			 = $this->bo->save($values);
						$custom_id			 = $receipt['custom_id'];
						$this->cat_id		 = ($values['cat_id'] ? $values['cat_id'] : $this->cat_id);

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', 'custom_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => "{$this->currentapp}.uicustom.index"));
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
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => "{$this->currentapp}.uicustom.edit",
						'custom_id'	 => $custom_id));
				}
				else
				{
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
				phpgw::no_access();
			}

			$custom_id			 = phpgw::get_var('custom_id', 'int');
			$cols_id			 = phpgw::get_var('cols_id', 'int');
			$resort				 = phpgw::get_var('resort');
			$values				 = phpgw::get_var('values');
			$values['sql_text']	 = $_POST['values']['sql_text'];
			if ($cols_id)
			{
				$this->bo->resort(array('custom_id' => $custom_id, 'id' => $cols_id, 'resort' => $resort));
			}

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';
//			$tabs['items']	= array('label' => lang('items'), 'link' => "#items");
			//$GLOBALS['phpgw']->xslttpl->add_file(array('custom'));

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => "{$this->currentapp}.uicustom.index"));
			}

			if ($custom_id)
			{
				$custom			 = $this->bo->read_single($custom_id);
				$this->cat_id	 = ($custom['cat_id'] ? $custom['cat_id'] : $this->cat_id);
			}

			$link_data = array
				(
				'menuaction' => "{$this->currentapp}.uicustom.save",
				'custom_id'	 => $custom_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);



			$custom_def = array
				(
				array('key' => 'id', 'label' => lang('Id'), 'sortable' => true, 'hidden' => true),
				array('key' => 'name', 'label' => lang('Column name'), 'sortable' => FALSE),
				array('key' => 'descr', 'label' => lang('Column description'), 'sortable' => FALSE),
				array('key' => 'order', 'label' => lang('Order'), 'sortable' => FALSE, 'className' => 'center'),
				array('key'		 => 'sorting', 'label'		 => lang('Sorting'), 'sortable'	 => FALSE,
					'className'	 => 'center',
					'formatter'	 => 'JqueryPortico.formatUpDown'),
				array('key'		 => 'delete', 'label'		 => lang('Delete column'), 'sortable'	 => FALSE,
					'formatter'	 => 'JqueryPortico.formatCheckCustom'),
				array('key' => 'link_up', 'label' => lang('Up'), 'sortable' => FALSE, 'hidden' => TRUE),
				array('key' => 'link_down', 'label' => lang('Down'), 'sortable' => FALSE, 'hidden' => TRUE)
			);
			//formatLink formatCheck
			//while (is_array($custom['cols']) && list(, $entry) = each($custom['cols']))
			if (is_array($custom['cols']))
			{
				foreach ($custom['cols'] as $entry)
				{
					$cols[] = array(
						'id'		 => $entry['id'],
						'name'		 => $entry['name'],
						'descr'		 => $entry['descr'],
						'order'		 => $entry['sorting'],
						'sorting'	 => $entry['sorting'],
						'link_up'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "{$this->currentapp}.uicustom.edit",
							'resort'	 => 'up', 'cols_id'	 => $entry['id'], 'custom_id'	 => $custom_id)),
						'link_down'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "{$this->currentapp}.uicustom.edit",
							'resort'	 => 'down', 'cols_id'	 => $entry['id'], 'custom_id'	 => $custom_id)),
						'delete'	 => $entry['id'],
					);
				}
			}

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'data'		 => json_encode($cols),
				'ColumnDefs' => $custom_def,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('order' => json_encode(array('2', 'asc')))
				)
			);

			$data = array
				(
				'datatable_def'					 => $datatable_def,
				'msgbox_data'					 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'						 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_custom_id'				 => lang('ID'),
				'value_custom_id'				 => $custom_id,
				'lang_sql_text'					 => lang('sql'),
				'lang_name'						 => lang('name'),
				'lang_save'						 => lang('save'),
				'lang_cancel'					 => lang('cancel'),
				'lang_apply'					 => lang('apply'),
				'value_sql_text'				 => $custom['sql_text'],
				'value_name'					 => $custom['name'],
				'lang_name_statustext'			 => lang('Enter a name for the query'),
				'lang_sql_statustext'			 => lang('Enter a sql query'),
				'lang_apply_statustext'			 => lang('Apply the values'),
				'lang_cancel_statustext'		 => lang('Leave the custom untouched and return back to the list'),
				'lang_save_statustext'			 => lang('Save the custom and return back to the list'),
				'lang_no_cat'					 => lang('no category'),
				'lang_cat_statustext'			 => lang('Select the category the custom belongs to. To do not use a category select NO CATEGORY'),
				'lang_descr'					 => lang('descr'),
				'lang_new_name_statustext'		 => lang('name'),
				'lang_new_descr_statustext'		 => lang('descr'),
				'cols'							 => $cols,
				'lang_col_name'					 => lang('Column name'),
				'lang_col_descr'				 => lang('Column description'),
				'lang_delete_column'			 => lang('Delete column'),
				'lang_delete_cols_statustext'	 => lang('Delete this column from the output'),
				'lang_up_text'					 => lang('Up'),
				'lang_down_text'				 => lang('Down'),
				'lang_sorting'					 => lang('Sorting'),
				'tabs'							 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'						 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('custom') . ': ' . ($custom_id ? lang('edit custom') : lang('add custom'));

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl(array('custom', 'datatable_inline'), array('edit' => $data), $this->xsl_rootdir);
		}

		function delete()
		{
			$custom_id	 = phpgw::get_var('custom_id', 'int');
			$confirm	 = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => "{$this->currentapp}.uicustom.index"
			);

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($custom_id);
				return "custom_id " . $custom_id . " " . lang("has been deleted");
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'), $this->xsl_rootdir);

			$data = array
				(
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "{$this->currentapp}.uicustom.delete",
					'custom_id'	 => $custom_id)),
				'lang_confirm_msg'		 => lang('do you really want to delete this entry'),
				'lang_yes'				 => lang('yes'),
				'lang_yes_statustext'	 => lang('Delete the entry'),
				'lang_no_statustext'	 => lang('Back to the list'),
				'lang_no'				 => lang('no')
			);

			$appname		 = lang('custom');
			$function_msg	 = lang('delete custom');

			$GLOBALS['phpgw_info']['flags']['app_header'] = $this->lang_app_name . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}

		function view()
		{
			$custom_id = phpgw::get_var('custom_id', 'int', 'GET');

//			$datatable = array();

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$custom = $this->bo->read_single($custom_id);

			$appname		 = $this->lang_app_name;
			$function_msg	 = $custom['name'];

			$GLOBALS['phpgw_info']['flags']['app_header'] = $appname . ': ' . $function_msg;

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_view($custom_id);
			}

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
						'menuaction'		 => "{$this->currentapp}.uicustom.view",
						'custom_id'			 => $custom_id,
						'filter'			 => $this->filter,
						'phpgw_return_as'	 => 'json'
					)),
					'download'		 => self::link(array(
						'menuaction'	 => "{$this->currentapp}.uicustom.download",
						'filter'		 => $this->filter,
						'custom_id'		 => $custom_id,
						'export'		 => true,
						'skip_origin'	 => true,
						'allrows'		 => true
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array()
				)
			);


			$list	 = $this->bo->read_custom(array('custom_id' => $custom_id));
			$uicols	 = $this->bo->uicols;

			$count_uicols_name = count($uicols);

			for ($i = 0; $i < $count_uicols_name; $i++)
			{

				$params = array
					(
					'key'		 => $uicols[$i]['name'],
					'label'		 => $uicols[$i]['descr'],
					'sortable'	 => ($uicols[$i]['sortable']) ? true : false,
					'hidden'	 => ($uicols[$i]['input_type'] == 'hidden') ? true : false
				);

				array_push($data['datatable']['field'], $params);
			}

			$data['datatable']['actions'][] = array();

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query_view( $custom_id )
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
				'filter'	 => $this->filter,
				'custom_id'	 => $custom_id
			);

			$values = $this->bo->read_custom($params);

			if ($export)
			{
				return $values;
			}

			$result_data					 = array('results' => $values);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}

		function download()
		{
			$custom_id	 = phpgw::get_var('custom_id', 'int');
			$params		 = array(
				'custom_id'	 => $custom_id,
				'allrows'	 => true,
			);
			$list		 = $this->bo->read_custom($params);
			$uicols		 = $this->bo->uicols;
			foreach ($uicols as $col)
			{
				$names[] = $col['name'];
				$descr[] = $col['descr'];
			}
			$this->bocommon->download($list, $names, $descr);
		}
	}