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
	 * @subpackage admin
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uiasync extends phpgwapi_uicommon_jquery
	{

		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;
		var $public_functions = array
			(
			'query'	 => true,
			'index'	 => true,
			'view'	 => true,
			'edit'	 => true,
			'delete' => true,
			'save'	 => true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app']			 = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'admin::property::async';

			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo		 = CreateObject('property.boasync', true);
			$this->bocommon	 = CreateObject('property.bocommon');

			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.admin';
			$this->acl_read		 = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add		 = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit		 = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete	 = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage	 = $this->acl->check($this->acl_location, 16, 'property');

			$this->start	 = $this->bo->start;
			$this->query	 = $this->bo->query;
			$this->sort		 = $this->bo->sort;
			$this->order	 = $this->bo->order;
			$this->allrows	 = $this->bo->allrows;

			if (!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 16, 'acl_location'	 => $this->acl_location));
			}
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start'	 => $this->start,
				'query'	 => $this->query,
				'sort'	 => $this->sort,
				'order'	 => $this->order,
				//			'allrows'	=> $this->allrows,
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = lang('method');
			$function_msg	 = lang('list async method');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiasync.index',
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uiasync.edit'
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'id',
							'label'		 => lang('method ID'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'name',
							'label'		 => lang('Name'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'data',
							'label'		 => lang('Data'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'descr',
							'label'		 => lang('Description'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'url',
							'label'		 => lang('URL'),
							'sortable'	 => false,
							'hidden'	 => true
						)
					)
				)
			);

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'menuaction',
						'source' => 'url'
					),
				)
			);

			$parameters2 = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'method_id',
						'source' => 'id'
					)
				)
			);

			$parameters3 = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'id',
						'source' => 'id'
					)
				)
			);


			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'run',
				'statustext' => lang('Run Now'),
				'text'		 => lang('Run Now'),
				'action'	 => $GLOBALS['phpgw']->link
					(
					'/index.php', array
					(
					//'menuaction'		=> 'property.uiasync.edit'
					)
				),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'schedule',
				'statustext' => lang('Schedule'),
				'text'		 => lang('Schedule'),
				'action'	 => $GLOBALS['phpgw']->link
					(
					'/index.php', array
					(
					'menuaction' => 'property.uialarm.edit'
					)
				),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'edit',
				'statustext' => lang('Edit'),
				'text'		 => lang('Edit'),
				'action'	 => $GLOBALS['phpgw']->link
					(
					'/index.php', array
					(
					'menuaction' => 'property.uiasync.edit'
					)
				),
				'parameters' => json_encode($parameters3)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'		 => 'delete',
				'statustext'	 => lang('Delete'),
				'text'			 => lang('Delete'),
				'confirm_msg'	 => lang('do you really want to delete this entry'),
				'action'		 => $GLOBALS['phpgw']->link
					(
					'/index.php', array
					(
					'menuaction' => 'property.uiasync.delete'
					)
				),
				'parameters'	 => json_encode($parameters3)
			);

			/* $data['datatable']['actions'][] = array
			  (
			  'my_name' 			=> 'add',
			  'statustext' 	=> lang('add'),
			  'text'			=> lang('add'),
			  'action'		=> $GLOBALS['phpgw']->link('/index.php',array
			  (
			  'menuaction'	=> 'property.uiasync.edit'
			  ))
			  ); */


			unset($parameters);
			unset($parameters2);
			unset($parameters3);

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
			);

			$result_objects	 = array();
			$result_count	 = 0;

			$values = $this->bo->read($params);
			foreach ($values as &$entry)
			{
				$entry['url']	 = "{$entry['name']}&data=" . urlencode(urlencode($entry['data']));
				$data_set		 = unserialize($entry['data']);
				$method_data	 = array();
				foreach ($data_set as $key => $value)
				{
					$method_data[] = "{$key}::{$value}";
				}
				$entry['data'] = @implode(',', $method_data);
			}

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

			if (!$this->acl_edit)
			{
				phpgw::no_access();
			}

			$id		 = phpgw::get_var('id', 'int');
			$values	 = phpgw::get_var('values');


			if ($id)
			{
				$values['id']	 = $id;
				$action			 = 'edit';
			}
			else
			{
				$id = $values['id'];
			}

			$data	 = str_replace(' ', '', stripslashes($values['data']));
			$data	 = html_entity_decode(stripslashes($values['data']));

			$data = explode(",", $data);

			if (is_array($data))
			{
				foreach ($data as $set)
				{
					$set				 = explode("::", $set);
					$data_set[$set[0]]	 = $set[1];
				}
			}

			if ($values['data'])
			{
				$values['data'] = serialize($data_set);
			}

			try
			{

				$receipt	 = $this->bo->save($values, $action);
				$id			 = $receipt['id'];
				$msgbox_data = $this->bocommon->msgbox_data($receipt);
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

			$message = $GLOBALS['phpgw']->common->msgbox($msgbox_data);

			phpgwapi_cache::message_set($message[0]['msgbox_text'], 'message');
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiasync.edit',
				'id'		 => $id));
		}

		function edit()
		{
			if (!$this->acl_edit)
			{
				phpgw::no_access();
			}
			$id				 = phpgw::get_var('id', 'int');
			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			if ($id)
			{
				$method		 = $this->bo->read_single($id);
				$data_set	 = unserialize($method['data']);

				if (is_array($data_set))
				{
					foreach ($data_set as $key => $value)
					{
						$method_data[] = $key . '::' . $value;
					}
				}

				$method_data	 = @implode(',', $method_data);
				$function_msg	 = lang('edit method');
				$action			 = 'edit';
			}
			else
			{
				$function_msg	 = lang('add method');
				$action			 = 'add';
			}


			$link_data = array
				(
				'menuaction' => 'property.uiasync.save',
				'id'		 => $id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data'			 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiasync.index')),
				'lang_id'				 => lang('method ID'),
				'lang_name'				 => lang('Name'),
				'lang_descr'			 => lang('Descr'),
				'lang_save'				 => lang('save'),
				'lang_done'				 => lang('done'),
				'value_id'				 => $id,
				'value_name'			 => $method['name'],
				'lang_id_statustext'	 => lang('Enter the method ID'),
				'lang_descr_statustext'	 => lang('Enter a description the method'),
				'lang_done_statustext'	 => lang('Back to the list'),
				'lang_save_statustext'	 => lang('Save the method'),
				'type_id'				 => $method['type_id'],
				'location_code'			 => $method['location_code'],
				'value_descr'			 => $method['descr'],
				'value_data'			 => $method_data,
				'lang_data'				 => lang('Data'),
				'lang_data_statustext'	 => lang('Input data for the nethod'),
				'tabs'					 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'				 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$appname = lang('async method');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl(array('async', 'datatable_inline'), array('edit' => $data));
		}

		function delete()
		{
			$id		 = phpgw::get_var('id', 'int');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($id);
				return "id " . $id . " " . lang("has been deleted");
			}

			$link_data = array
				(
				'menuaction' => 'property.uiasync.index'
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiasync.delete',
					'id'		 => $id)),
				'lang_confirm_msg'		 => lang('do you really want to delete this entry'),
				'lang_yes'				 => lang('yes'),
				'lang_yes_statustext'	 => lang('Delete the entry'),
				'lang_no_statustext'	 => lang('Back to the list'),
				'lang_no'				 => lang('no')
			);

			$appname		 = lang('async method');
			$function_msg	 = lang('delete async method');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}
	}