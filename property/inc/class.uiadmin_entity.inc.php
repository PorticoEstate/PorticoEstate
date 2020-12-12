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

	class property_uiadmin_entity extends phpgwapi_uicommon_jquery
	{

		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;
		var $receipt			 = array();
		var $public_functions = array
			(
			'query'						 => true,
			'index'						 => true,
			'category'					 => true,
			'edit'						 => true,
			'edit_category'				 => true,
			'view'						 => true,
			'delete'					 => true,
			'list_attribute_group'		 => true,
			'list_attribute'			 => true,
			'edit_attrib_group'			 => true,
			'edit_attrib'				 => true,
			'list_custom_function'		 => true,
			'edit_custom_function'		 => true,
			'get_template_attributes'	 => true,
			'convert_to_eav'			 => true,
			'save'						 => true,
			'save_category'				 => true,
			'add_choice_value'			 => true,
			'delete_choice_value'		 => true
		);
		private $bo;

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo		 = CreateObject('property.boadmin_entity', true);
			$this->bocommon	 = & $this->bo->bocommon;

			$this->start		 = $this->bo->start;
			$this->query		 = $this->bo->query;
			$this->sort			 = $this->bo->sort;
			$this->order		 = $this->bo->order;
			$this->entity_id	 = $this->bo->entity_id;
			$this->cat_id		 = $this->bo->cat_id;
			$this->allrows		 = $this->bo->allrows;
			$this->type			 = $this->bo->type;
			$this->type_app		 = $this->bo->type_app;
			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.admin.entity';
			$this->acl_read		 = $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->type_app[$this->type]);
			$this->acl_add		 = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->type_app[$this->type]);
			$this->acl_edit		 = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->type_app[$this->type]);
			$this->acl_delete	 = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->type_app[$this->type]);
			$this->acl_manage	 = $this->acl->check($this->acl_location, 16, $this->type_app[$this->type]);

			$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.$this->entity_id.{$this->cat_id}");
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin#{$location_id}";
		}

		function index()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$this->bocommon->reset_fm_cache();

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query(array('method' => $this->type));
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = lang('entity');
			$function_msg	 = lang('list entity type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiadmin_entity.index',
						'type'				 => $this->type,
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uiadmin_entity.edit',
						'type'		 => $this->type
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'id',
							'label'		 => lang('Entity ID'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'name',
							'label'		 => lang('Name'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'descr',
							'label'		 => lang('Descr'),
							'sortable'	 => false
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
						'name'	 => 'entity_id',
						'source' => 'id'
					),
				)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'categories',
				'statustext' => lang('categories'),
				'text'		 => lang('Categories'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.category',
					'type'		 => $this->type
				)),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'edit',
				'statustext' => lang('edit'),
				'text'		 => lang('edit'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.edit',
					'type'		 => $this->type
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array(
				'my_name'		 => 'delete',
				'statustext'	 => lang('delete'),
				'text'			 => lang('delete'),
				'confirm_msg'	 => lang('do you really want to delete this entry'),
				'action'		 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.delete',
					'type'		 => $this->type
				)),
				'parameters'	 => json_encode($parameters2)
			);


			#$data['datatable']['actions'][] = array(
			#	'my_name' 	=> 'add',
			#	'text' 		=> lang('add'),
			#	'action'	=> $GLOBALS['phpgw']->link('/index.php',array
			#	(
			#		'menuaction'	=> 'property.uiadmin_entity.edit',
			#		'type'		=> $this->type
			#	)));

			unset($parameters);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query( $data = array() )
		{

			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			switch ($data['method'])
			{
				case 'category':
					$entity_id	 = $data['entity_id'];
					break;
				case 'list_attribute':
				case 'list_attribute_group':
				case 'list_custom_function':
					$entity_id	 = $data['entity_id'];
					$cat_id		 = $data['cat_id'];
					break;
				default:$entity_id	 = "";
					break;
			}

			$export = phpgw::get_var('export', 'bool');

			$params = array(
				'start'		 => $this->start,
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'sort'		 => $order[0]['dir'],
				'order'		 => $columns[$order[0]['column']]['data'],
				'allrows'	 => phpgw::get_var('length', 'int') == -1 || $export,
				'entity_id'	 => $entity_id,
				'cat_id'	 => $cat_id
			);

			$result_objects	 = array();
			$result_count	 = 0;

			switch ($data['method'])
			{
				case 'category':
					$values	 = $this->bo->read_category($params);
					break;
				case 'list_attribute':
					$values	 = $this->bo->read_attrib($params);
					break;
				case 'list_attribute_group':
					$values	 = $this->bo->read_attrib_group($params);
					break;
				case 'list_custom_function':
					$values	 = $this->bo->read_custom_function($params);
					break;
				default:
					$values	 = $this->bo->read($params);
					break;
			}

			$new_values = array();
			foreach ($values as $value)
			{
				$new_values[] = $value;
			}

			if ($export)
			{
				return $new_values;
			}

			$result_data					 = array('results' => $new_values);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;
			switch ($data['method'])
			{
				case 'list_attribute':
					$variable	 = array(
						'menuaction' => 'property.uiadmin_entity.list_attribute',
						'entity_id'	 => $entity_id,
						'cat_id'	 => $cat_id,
						'allrows'	 => $this->allrows,
						'type'		 => $this->type
					);
					array_walk($result_data['results'], array($this, '_add_links'), $variable);
					break;
				case 'list_attribute_group':
					$variable	 = array(
						'menuaction' => 'property.uiadmin_entity.list_attribute_group',
						'entity_id'	 => $entity_id,
						'cat_id'	 => $cat_id,
						'allrows'	 => $this->allrows,
						'type'		 => $this->type
					);
					array_walk($result_data['results'], array($this, '_add_links'), $variable);
					break;
				case 'list_custom_function':
					$variable	 = array(
						'menuaction' => 'property.uiadmin_entity.list_custom_function',
						'entity_id'	 => $entity_id,
						'cat_id'	 => $cat_id,
						'allrows'	 => $this->allrows,
						'type'		 => $this->type
					);
					array_walk($result_data['results'], array($this, '_add_links'), $variable);
					break;
			}
			return $this->jquery_results($result_data);
		}

		function category()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$entity_id											 = phpgw::get_var('entity_id', 'int');
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 .= "::entity_{$entity_id}";

			$entity = $this->bo->read_single($entity_id);

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query(array('method' => 'category', 'entity_id' => $entity_id));
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = lang('Entity:' . $entity['name']);
			$function_msg	 = lang('list entity type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiadmin_entity.category',
						'entity_id'			 => $entity_id,
						'type'				 => $this->type,
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uiadmin_entity.edit_category',
						'entity_id'	 => $entity_id,
						'type'		 => $this->type
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array
							(
							'key'		 => 'location_id',
							'label'		 => lang('location_id'),
							'sortable'	 => true
						),
						array
							(
							'key'		 => 'id',
							'label'		 => lang('category ID'),
							'sortable'	 => true
						),
						array
							(
							'key'		 => 'name',
							'label'		 => lang('Name'),
							'sortable'	 => false
						),
						array
							(
							'key'		 => 'descr',
							'label'		 => lang('Descr'),
							'sortable'	 => false
						),
						array
							(
							'key'		 => 'prefix',
							'label'		 => lang('Prefix'),
							'sortable'	 => false
						),
						array
							(
							'key'		 => 'entity_id',
							'label'		 => lang('id'),
							'sortable'	 => false,
							'hidden'	 => true
						),
						array
							(
							'key'		 => 'is_eav',
							'label'		 => lang('is_eav'),
							'sortable'	 => false
						),
						array
							(
							'key'		 => 'enable_bulk',
							'label'		 => lang('enable bulk'),
							'sortable'	 => false
						),
						array
							(
							'key'		 => 'enable_controller',
							'label'		 => lang('enable controller'),
							'sortable'	 => false
						),
						array
							(
							'key'		 => 'entity_group_id',
							'label'		 => lang('entity group'),
							'sortable'	 => false
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
						'name'	 => 'id',
						'source' => 'id'
					),
					array
						(
						'name'	 => 'entity_id',
						'source' => 'entity_id'
					)
				)
			);

			$parameters2 = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'cat_id',
						'source' => 'id'
					),
					array
						(
						'name'	 => 'entity_id',
						'source' => 'entity_id'
					)
				)
			);

			$parameters3 = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'entity_id',
						'source' => 'entity_id'
					)
				)
			);

			$parameters4 = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'location_id',
						'source' => 'location_id'
					),
				)
			);


			$data['datatable']['actions'][] = array(
				'my_name'	 => 'attribute_groups',
				'statustext' => lang('attribute groups'),
				'text'		 => lang('attribute groups'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.list_attribute_group',
					'type'		 => $this->type
				)),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'attributes',
				'statustext' => lang('attributes'),
				'text'		 => lang('Attributes'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.list_attribute',
					'type'		 => $this->type
				)),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'config',
				'statustext' => lang('config'),
				'text'		 => lang('config'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'admin.uiconfig2.index'
				)),
				'parameters' => json_encode($parameters4)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'custom functions',
				'statustext' => lang('custom functions'),
				'text'		 => lang('Custom functions'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.list_custom_function',
					'type'		 => $this->type
				)),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'edit',
				'statustext' => lang('edit'),
				'text'		 => lang('edit'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.edit_category',
					'type'		 => $this->type
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array(
				'my_name'		 => 'delete',
				'statustext'	 => lang('delete'),
				'text'			 => lang('delete'),
				'confirm_msg'	 => lang('do you really want to delete this entry'),
				'action'		 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.delete',
					'type'		 => $this->type
				)),
				'parameters'	 => json_encode($parameters2)
			);


			$datatable['rowactions']['action'][] = array(
				'my_name'	 => 'add',
				'text'		 => lang('add'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.edit_category',
					'entity_id'	 => $entity_id,
					'type'		 => $this->type
				)),
				'parameters' => json_encode($parameters3)
			);

			unset($parameters);
			unset($parameters2);
			unset($parameters3);
			unset($parameters4);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}

			$id		 = (int)phpgw::get_var('id');
			$values	 = phpgw::get_var('values');
			$config	 = CreateObject('phpgwapi.config', $this->type_app[$this->type]);

			if (!$values['name'])
			{
				$this->receipt['error'][] = array('msg' => lang('Name not entered!'));
			}

			if ($id)
			{
				$values['id']	 = $id;
				$action			 = 'edit';
			}

			if (!$this->receipt['error'])
			{
				try
				{

					$this->receipt = $this->bo->save($values, $action);

					if (!$id)
					{
						$id = $this->receipt['id'];
					}

					$config->read();

					if (!is_array($config->config_data['location_form']))
					{
						$config->config_data['location_form'] = array();
					}

					if ($values['location_form'])
					{

						$config->config_data['location_form']['entity_' . $id] = 'entity_' . $id;
					}
					else
					{
						unset($config->config_data['location_form']['entity_' . $id]);
					}

					$config->save_repository();
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

				$msgbox_data = $this->bocommon->msgbox_data($this->receipt);
				$message	 = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				phpgwapi_cache::message_set($message[0]['msgbox_text'], 'message');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiadmin_entity.edit',
					'type'		 => $this->type, 'id'		 => $id));
			}
			else
			{
				$this->receipt['error'][] = array('msg' => lang('Entity has NOT been saved'));
			}
		}

		function edit()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $this->acl_location));
			}
			$id		 = (int)phpgw::get_var('id');
			$values	 = phpgw::get_var('values');
			$config	 = CreateObject('phpgwapi.config', $this->type_app[$this->type]);

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if ($id)
			{
				$values			 = $this->bo->read_single($id);
				$function_msg	 = lang('edit standard');
				$action			 = 'edit';
			}
			else
			{
				$function_msg	 = lang('add entity');
				$action			 = 'add';
			}

			$include_list	 = $this->bo->get_entity_list($values['lookup_entity']);
			$include_list_2	 = $this->bo->get_entity_list_2($values['include_entity_for']);
			$include_list_3	 = $this->bo->get_entity_list_3($values['start_entity_from']);

			$link_data = array
				(
				'menuaction' => 'property.uiadmin_entity.save',
				'id'		 => $id,
				'type'		 => $this->type
			);

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
				(
				'msgbox_data'					 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_name_standardtext'		 => lang('Enter a name of the standard'),
				'form_action'					 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_entity.index',
					'type'		 => $this->type)),
				'lang_id'						 => lang('standard ID'),
				'lang_name'						 => lang('Name'),
				'lang_descr'					 => lang('Descr'),
				'lang_save'						 => lang('save'),
				'lang_done'						 => lang('done'),
				'value_id'						 => $id,
				'value_name'					 => $values['name'],
				'lang_id_standardtext'			 => lang('Enter the standard ID'),
				'lang_descr_standardtext'		 => lang('Enter a description of the standard'),
				'lang_done_standardtext'		 => lang('Back to the list'),
				'lang_save_standardtext'		 => lang('Save the standard'),
				'type_id'						 => $values['type_id'],
				'value_descr'					 => $values['descr'],
				'lang_location_form'			 => lang('location form'),
				'value_location_form'			 => $values['location_form'],
				'lang_location_form_statustext'	 => lang('If this entity type is to be linked to a location'),
				'lang_include_in_location_form'	 => lang('include in location form'),
				'include_list'					 => $include_list,
				'lang_include_statustext'		 => lang('Which entity type is to show up in location forms'),
				'lang_include_this_entity'		 => lang('include this entity'),
				'include_list_2'				 => $include_list_2,
				'lang_include_2_statustext'		 => lang('Let this entity show up in location form'),
				'lang_start_this_entity'		 => lang('start this entity'),
				'include_list_3'				 => $include_list_3,
				'lang_include_3_statustext'		 => lang('Start this entity from'),
				'lang_select'					 => lang('select'),
				'lang_documentation'			 => lang('documentation'),
				'value_documentation'			 => $values['documentation'],
				'lang_documentation_statustext'	 => lang('If this entity type is to be linked to documents'),
				'tabs'							 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'						 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			self::add_javascript('property', 'portico', 'admin_entity.edit.js');
			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit' => $data));
		}

		public function save_category()
		{
			if (!$this->acl_add)
			{
				return;
			}
			if (!$_POST)
			{
				return $this->edit_category();
			}

			$entity_id		 = phpgw::get_var('entity_id', 'int');
			$id				 = phpgw::get_var('id', 'int');
			$values			 = phpgw::get_var('values');
			$template_attrib = phpgw::get_var('template_attrib');

			if ($template_attrib)
			{
				$values['template_attrib'] = array_values(explode(',', $template_attrib));
			}

			$values['entity_id'] = $entity_id;

			if (!$values['name'])
			{
				$this->receipt['error'][] = array('msg' => lang('Name not entered!'));
			}
			if (!$values['entity_id'])
			{
				$this->receipt['error'][] = array('msg' => lang('Entity not chosen'));
			}

			if ($id)
			{
				$values['id']	 = $id;
				$action			 = 'edit';
			}

			if (!$this->receipt['error'])
			{
				try
				{
					$this->receipt = $this->bo->save_category($values, $action);
					if (!$id)
					{
						$id = $this->receipt['id'];
					}
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$this->edit_category($values);
						return;
					}
				}

				$msgbox_data = $this->bocommon->msgbox_data($this->receipt);
				$message	 = $GLOBALS['phpgw']->common->msgbox($msgbox_data);

				phpgwapi_cache::message_set($message[0]['msgbox_text'], 'message');
				$GLOBALS['phpgw']->redirect_link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.edit_category',
					'type'		 => $this->type,
					'id'		 => $id,
					'entity_id'	 => $entity_id
					)
				);
			}
			else
			{
				$this->receipt['error'][] = array('msg' => lang('Category has NOT been saved'));
				$this->edit_category($values);
			}
		}

		function edit_category( $values = array() )
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => PHPGW_ACL_ADD, 'acl_location'	 => $this->acl_location));
			}

			$entity_id		 = phpgw::get_var('entity_id', 'int');
			$id				 = phpgw::get_var('id', 'int');
			$values			 = phpgw::get_var('values');
			$template_attrib = phpgw::get_var('template_attrib');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			if ($template_attrib)
			{
				$values['template_attrib'] = array_values(explode(',', $template_attrib));
			}

//			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity', 'datatable_inline'));

			if ($id)
			{
				$values			 = $this->bo->read_single_category($entity_id, $id);
				$function_msg	 = lang('edit category');
				$action			 = 'edit';
			}
			else
			{
				$function_msg	 = lang('add category');
				$action			 = 'add';
			}


			$link_data = array
				(
				'menuaction' => 'property.uiadmin_entity.save_category',
				'entity_id'	 => $entity_id,
				'id'		 => $id,
				'type'		 => $this->type
			);
			//_debug_array($link_data);

			$entity				 = $this->bo->read_single($entity_id, false);
			$this->bo->allrows	 = true;

			$parent_list = $this->bocommon->select_list($values['parent_id'], $this->bo->read_category_tree2($entity_id));

			if ($id)
			{
				$exclude	 = array($id);
				$children	 = $this->bo->get_children2($entity_id, $id, 0, true);

				foreach ($children as $child)
				{
					$exclude[] = $child['id'];
				}

				$k = count($parent_list);
				for ($i = 0; $i < $k; $i++)
				{
					if (in_array($parent_list[$i]['id'], $exclude))
					{
						unset($parent_list[$i]);
					}
				}
			}

			$entity_list = $this->bo->read(array('allrows' => true));

			$category_list = array();
			foreach ($entity_list as $entry)
			{
				$cat_list = $this->bo->read_category(array('entity_id' => $entry['id'], 'allrows' => true));

				foreach ($cat_list as $category)
				{
					$category_list[] = array
						(
						'id'	 => "{$entry['id']}_{$category['id']}",
						'name'	 => "{$entry['name']}::{$category['name']}"
					);
				}
			}


			$myColumnDefs = array(
				array('key'		 => 'attrib_id', 'label'		 => lang('id'), 'sortable'	 => false,
					'resizeable' => true,
					'hidden'	 => false),
				array('key' => 'name', 'label' => lang('name'), 'sortable' => false, 'resizeable' => true),
				array('key'		 => 'datatype', 'label'		 => lang('datatype'), 'sortable'	 => false,
					'resizeable' => true),
				array('key'		 => 'select', 'label'		 => lang('select'), 'sortable'	 => false,
					'resizeable' => false,
					'formatter'	 => 'myFormatterCheck', 'width'		 => 30)
			);

			$datatable_def = array();

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $myColumnDefs,
				'data'		 => json_encode(array()),
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);


			$msgbox_data		 = $this->bocommon->msgbox_data($this->receipt);
			$location_level_list = $this->bo->get_location_level_list();

			array_unshift($location_level_list, array('id' => -2, 'name' => lang('no locations')));

			foreach ($location_level_list as &$entry)
			{
				$entry['selected'] = $entry['id'] == $values['location_level'];
			}

			$data = array
				(
				'datatable_def'							 => $datatable_def,
				'lang_entity'							 => lang('entity'),
				'entity_name'							 => $id ? $entity['name'] . ' :: ' . implode(' >> ', $this->bo->get_path($entity_id, $id)) : $entity['name'],
				'msgbox_data'							 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_prefix_standardtext'				 => lang('Enter a standard prefix for the id'),
				'lang_name_standardtext'				 => lang('Enter a name of the standard'),
				'form_action'							 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'							 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_entity.category',
					'entity_id'	 => $entity_id, 'type'		 => $this->type)),
				'base_java_url'							 => json_encode(array('menuaction' => "property.uiadmin_entity.get_template_attributes")),
				'lang_save'								 => lang('save'),
				'lang_done'								 => lang('done'),
				'value_id'								 => $id,
				'value_name'							 => $values['name'],
				'value_prefix'							 => $values['prefix'],
				'edit_prefix'							 => true,
				'lang_id_standardtext'					 => lang('Enter the standard ID'),
				'lang_descr_standardtext'				 => lang('Enter a description of the standard'),
				'lang_done_standardtext'				 => lang('Back to the list'),
				'lang_save_standardtext'				 => lang('Save the standard'),
				'type_id'								 => $values['type_id'],
				'value_descr'							 => $values['descr'],
				'lookup_tenant'							 => true,
				'value_lookup_tenant'					 => $values['lookup_tenant'],
				'lang_location_level'					 => lang('location level'),
				'location_level_list'					 => array('options' => $location_level_list),
				'lang_location_level_statustext'		 => lang('select location level'),
				'lang_no_location_level'				 => lang('None'),
				'lang_location_link_level'				 => lang('location link level'),
				'location_link_level_list'				 => array('options' => $this->bo->get_location_level_list($values['location_link_level'])),
				'lang_location_link_level_statustext'	 => lang('select location level'),
				'lang_no_location_link_level'			 => lang('None'),
				'tracking'								 => true,
				'value_tracking'						 => $values['tracking'],
				'org_unit'								 => true,
				'value_org_unit'						 => $values['org_unit'],
				'fileupload'							 => true,
				'value_fileupload'						 => $values['fileupload'],
				'value_jasperupload'					 => $values['jasperupload'],
				'loc_link'								 => true,
				'value_loc_link'						 => $values['loc_link'],
				'start_project'							 => true,
				'value_start_project'					 => $values['start_project'],
				'start_ticket'							 => true,
				'value_start_ticket'					 => $values['start_ticket'],
				'value_is_eav'							 => $values['is_eav'],
				'value_enable_bulk'						 => $values['enable_bulk'],
				'value_enable_controller'				 => $values['enable_controller'],
				'jasperupload'							 => true,
				'entity_group_list'						 => array('options' => execMethod('property.bogeneric.get_list', array(
						'type'		 => 'entity_group', 'selected'	 => $values['entity_group_id'], 'add_empty'	 => true))),
				'category_list'							 => $category_list,
				'parent_list'							 => $parent_list,
				'tabs'									 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'								 => phpgwapi_jquery::formvalidator_generate()
			);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::add_javascript('property', 'portico', 'admin_entity.edit_category.js');

			self::render_template_xsl(array('admin_entity', 'datatable_inline', 'nextmatchs'), array(
				'edit' => $data));
		}

		function get_template_attributes()
		{
			$template_info		 = explode('_', phpgw::get_var('category_template', 'string', 'GET'));
			$template_entity_id	 = $template_info[0];
			$template_cat_id	 = $template_info[1];

			$attrib_list = $this->bo->read_attrib(array('entity_id'	 => $template_entity_id,
				'cat_id'	 => $template_cat_id, 'allrows'	 => true));

			$content = array();
			foreach ($attrib_list as $_entry)
			{
				$content[] = array
					(
					'attrib_id'	 => $_entry['id'],
					'name'		 => $_entry['input_text'],
					'datatype'	 => $_entry['trans_datatype'],
				);
			}

			$result_data = array
				(
				'results'		 => $content,
				'total_records'	 => count($content),
				'draw'			 => phpgw::get_var('draw', 'int')
			);
			return $this->jquery_results($result_data);
		}

		function delete()
		{
			if (!$this->acl_delete)
			{
				if (phpgw::get_var('phpgw_return_as') == 'json')
				{
					return "Go away!";
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
						'perm'			 => 8, 'acl_location'	 => $this->acl_location));
				}
			}

			$entity_id			 = phpgw::get_var('entity_id', 'int');
			$cat_id				 = phpgw::get_var('cat_id', 'int');
			$attrib_id			 = phpgw::get_var('attrib_id', 'int');
			$group_id			 = phpgw::get_var('group_id', 'int');
			$acl_location		 = phpgw::get_var('acl_location');
			$custom_function_id	 = phpgw::get_var('custom_function_id', 'int');
			$confirm			 = phpgw::get_var('confirm', 'bool', 'POST');

			// JSON code delete
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($cat_id, $entity_id, $attrib_id, $acl_location, $custom_function_id, $group_id);
				return lang("this record has been deleted");
			}

			if ($group_id)
			{
				$function = 'list_attribute_group';
			}
			else if ($attrib_id)
			{
				$function = 'list_attribute';
			}
			else if ($custom_function_id)
			{
				$function = 'list_custom_function';
			}

			if (!$acl_location && $entity_id && $cat_id)
			{
				$acl_location = ".{$this->type}.{$entity_id}.{$cat_id}";
			}

			if (!$function)
			{
				if ($cat_id)
				{
					$function = 'category';
				}
				else
				{
					$function = 'index';
				}
			}


			$link_data = array
				(
				'menuaction' => 'property.uiadmin_entity.' . $function,
				'cat_id'	 => $cat_id,
				'entity_id'	 => $entity_id,
				'attrib_id'	 => $attrib_id,
				'type'		 => $this->type
			);

			$delete_data = array
				(
				'menuaction'		 => 'property.uiadmin_entity.delete',
				'cat_id'			 => $cat_id,
				'entity_id'			 => $entity_id,
				'group_id'			 => $group_id,
				'attrib_id'			 => $attrib_id,
				'acl_location'		 => $acl_location,
				'custom_function_id' => $custom_function_id,
				'type'				 => $this->type
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action'			 => $GLOBALS['phpgw']->link('/index.php', $delete_data),
				'lang_confirm_msg'		 => lang('do you really want to delete this entry'),
				'lang_yes'				 => lang('yes'),
				'lang_yes_standardtext'	 => lang('Delete the entry'),
				'lang_no_standardtext'	 => lang('Back to the list'),
				'lang_no'				 => lang('no')
			);

			$appname		 = lang('entity');
			$function_msg	 = lang('delete entity type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function list_attribute_group()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$entity_id	 = $this->entity_id;
			$cat_id		 = $this->cat_id;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

			$id		 = phpgw::get_var('id', 'int');
			$resort	 = phpgw::get_var('resort');

			if ($resort)
			{
				$this->bo->resort_attrib_group($id, $resort);
			}
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				if ($resort)
				{
					$this->bo->resort_attrib_group($id, $resort);
				}

				return $this->query(array
						(
						'method'	 => 'list_attribute_group',
						'entity_id'	 => $entity_id,
						'cat_id'	 => $cat_id
				));
			}
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname										 = lang('attribute group');
			$function_msg									 = lang('list entity attribute group');
			$GLOBALS['phpgw_info']['flags']['app_header']	 = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type'	 => 'link',
								'value'	 => lang('cancel'),
								'href'	 => self::link(array
									(
									'menuaction' => 'property.uiadmin_entity.category',
									'entity_id'	 => $entity_id,
									'type'		 => $this->type
								)),
								'class'	 => 'new_item'
							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiadmin_entity.list_attribute_group',
						'entity_id'			 => $entity_id,
						'cat_id'			 => $cat_id,
						'type'				 => $this->type,
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uiadmin_entity.edit_attrib_group',
						'entity_id'	 => $entity_id,
						'cat_id'	 => $cat_id,
						'type'		 => $this->type
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
							'key'		 => 'parent_id',
							'label'		 => lang('parent'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'name',
							'label'		 => lang('Name'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'text',
							'label'		 => lang('Descr'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'group_sort',
							'label'		 => lang('sorting'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'up',
							'label'		 => lang('up'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.formatLinkGenericLlistAttribute'
						),
						array(
							'key'		 => 'down',
							'label'		 => lang('down'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.formatLinkGenericLlistAttribute'
						),
						array(
							'key'		 => 'id',
							'label'		 => lang('id'),
							'sortable'	 => false,
							'hidden'	 => true
						)
					)
				)
			);

			$datatable['rowactions']['action'] = array();

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
						'name'	 => 'group_id',
						'source' => 'id'
					),
				)
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
					'menuaction' => 'property.uiadmin_entity.edit_attrib_group',
					'entity_id'	 => $entity_id,
					'cat_id'	 => $cat_id,
					'type'		 => $this->type
					)
				),
				'parameters' => json_encode($parameters)
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
					'menuaction' => 'property.uiadmin_entity.delete',
					'entity_id'	 => $entity_id,
					'cat_id'	 => $cat_id,
					'type'		 => $this->type
					)
				),
				'parameters'	 => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'list_attribute',
				'statustext' => lang('list attribute'),
				'text'		 => lang('list attribute'),
				'action'	 => $GLOBALS['phpgw']->link
					(
					'/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.list_attribute',
					'entity_id'	 => $entity_id,
					'cat_id'	 => $cat_id,
					'type'		 => $this->type
					)
				),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'new_attribute',
				'statustext' => lang('new attribute'),
				'text'		 => lang('new attribute'),
				'action'	 => $GLOBALS['phpgw']->link
					(
					'/index.php', array
					(
					'menuaction' => 'property.uiadmin_entity.edit_attrib',
					'entity_id'	 => $entity_id,
					'cat_id'	 => $cat_id,
					'type'		 => $this->type
					)
				),
				'parameters' => json_encode($parameters2)
			);

			unset($parameters);
			unset($parameters2);
			self::render_template_xsl('datatable_jquery', $data);
		}

		function list_attribute()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$entity_id	 = $this->entity_id;
			$cat_id		 = $this->cat_id;

			$entity		 = $this->bo->read_single($entity_id);
			$category	 = $this->bo->read_single_category($entity_id, $cat_id);

//			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

			$id		 = phpgw::get_var('id');
			$resort	 = phpgw::get_var('resort');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				if ($resort)
				{
					$this->bo->resort_attrib($id, $resort);
				}

				return $this->query(array
						(
						'method'	 => 'list_attribute',
						'entity_id'	 => $entity_id,
						'cat_id'	 => $cat_id
						)
				);
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname										 = lang('attribute');
			$function_msg									 = lang('list entity attribute');
			$GLOBALS['phpgw_info']['flags']['app_header']	 = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type'	 => 'link',
								'value'	 => lang('cancel'),
								'href'	 => self::link(array
									(
									'menuaction' => 'property.uiadmin_entity.category',
									'entity_id'	 => $entity_id,
									'type'		 => $this->type
									)
								),
								'class'	 => 'new_item'
							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiadmin_entity.list_attribute',
						'entity_id'			 => $entity_id,
						'cat_id'			 => $cat_id,
						'type'				 => $this->type,
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uiadmin_entity.edit_attrib',
						'entity_id'	 => $entity_id,
						'cat_id'	 => $cat_id,
						'type'		 => $this->type
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'column_name',
							'label'		 => lang('Name'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'input_text',
							'label'		 => lang('Descr'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'trans_datatype',
							'label'		 => lang('Datatype'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'group_id',
							'label'		 => lang('group'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'attrib_sort',
							'label'		 => lang('sorting'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'up',
							'label'		 => lang('up'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.formatLinkGenericLlistAttribute'
						),
						array(
							'key'		 => 'down',
							'label'		 => lang('down'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.formatLinkGenericLlistAttribute'
						),
						array(
							'key'		 => 'search',
							'label'		 => lang('search'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'id',
							'label'		 => lang('id'),
							'sortable'	 => false,
							'hidden'	 => true
						),
						array(
							'key'		 => 'short_description',
							'label'		 => lang('short description'),
							'sortable'	 => false
						)
					)
				)
			);

			/* $current_Consult = array ();
			  for($i=0;$i<2;$i++)
			  {
			  if($i==0)
			  {
			  $current_Consult[] = array('entity',$entity['name']);
			  }
			  if($i==1)
			  {
			  $current_Consult[] = array('Category',$category['name']);
			  }
			  } */

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
						'name'	 => 'attrib_id',
						'source' => 'id'
					),
				)
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
					'menuaction' => 'property.uiadmin_entity.edit_attrib',
					'entity_id'	 => $entity_id,
					'cat_id'	 => $cat_id,
					'type'		 => $this->type
					)
				),
				'parameters' => json_encode($parameters)
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
					'menuaction' => 'property.uiadmin_entity.delete',
					'entity_id'	 => $entity_id,
					'cat_id'	 => $cat_id,
					'type'		 => $this->type
					)
				),
				'parameters'	 => json_encode($parameters2)
			);

			unset($parameters);
			unset($parameters2);

			self::render_template_xsl('datatable_jquery', $data);
		}

		function edit_attrib_group()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $this->acl_location));
			}

			$entity_id	 = phpgw::get_var('entity_id', 'int');
			$cat_id		 = phpgw::get_var('cat_id', 'int');
			$id			 = phpgw::get_var('id', 'int');
			$values		 = phpgw::get_var('values');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

			if (!$values)
			{
				$values = array();
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if (isset($values['save']) && $values['save'])
			{
				if ($id)
				{
					$values['id']	 = $id;
					$action			 = 'edit';
				}

				$values['entity_id'] = $entity_id;
				$values['cat_id']	 = $cat_id;

				if (!$values['group_name'])
				{
					$receipt['error'][] = array('msg' => lang('group name not entered!'));
				}

				if (!$values['descr'])
				{
					$receipt['error'][] = array('msg' => lang('description not entered!'));
				}

				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg' => lang('entity type not chosen!'));
				}


				if (!isset($receipt['error']))
				{
					$receipt = $this->bo->save_attrib_group($values, $action);

					if (!$id)
					{
						$id = $receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Attribute group has NOT been saved'));
				}
			}

			if ($id)
			{
				$values			 = $this->bo->read_single_attrib_group($entity_id, $cat_id, $id);
				$type_name		 = $values['type_name'];
				$function_msg	 = lang('edit attribute group') . ' ' . lang($type_name);
				$action			 = 'edit';
			}
			else
			{
				$function_msg	 = lang('add attribute group');
				$action			 = 'add';
			}


			$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");

			$parent_list = $GLOBALS['phpgw']->custom_fields->find_group($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", 0, '', '', '', true);

			$parent_list = $this->bocommon->select_list($values['parent_id'], $parent_list);
//_debug_array($parent_list);die();

			if ($id)
			{
				$exclude	 = array($id);
				$children	 = $GLOBALS['phpgw']->custom_fields->get_attribute_group_children($location_id, $id, 0, 0, true);

				foreach ($children as $child)
				{
					$exclude[] = $child['id'];
				}

				$k = count($parent_list);
				for ($i = 0; $i < $k; $i++)
				{
					if (in_array($parent_list[$i]['id'], $exclude))
					{
						unset($parent_list[$i]);
					}
				}
			}

			$link_data = array
				(
				'menuaction' => 'property.uiadmin_entity.edit_attrib_group',
				'entity_id'	 => $entity_id,
				'cat_id'	 => $cat_id,
				'id'		 => $id,
				'type'		 => $this->type
			);


			$entity		 = $this->bo->read_single($entity_id, false);
			$category	 = $this->bo->read_single_category($entity_id, $cat_id);

			$msgbox_data = (isset($receipt) ? $this->bocommon->msgbox_data($receipt) : '');

			$data = array
				(
				'lang_entity'				 => lang('entity'),
				'entity_name'				 => $entity['name'],
				'lang_category'				 => lang('category'),
				'category_name'				 => $category['name'],
				'msgbox_data'				 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'				 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_entity.list_attribute_group',
					'entity_id'	 => $entity_id, 'cat_id'	 => $cat_id, 'type'		 => $this->type)),
				'lang_id'					 => lang('Attribute group ID'),
				'lang_entity_type'			 => lang('Entity type'),
				'lang_no_entity_type'		 => lang('No entity type'),
				'lang_save'					 => lang('save'),
				'lang_done'					 => lang('done'),
				'value_id'					 => $id,
				'lang_group_name'			 => lang('group name'),
				'value_group_name'			 => $values['group_name'],
				'lang_group_name_statustext' => lang('enter the name for the group'),
				'lang_descr'				 => lang('descr'),
				'value_descr'				 => $values['descr'],
				'lang_descr_statustext'		 => lang('enter the input text for records'),
				'lang_remark'				 => lang('remark'),
				'lang_remark_statustext'	 => lang('Enter a remark for the group'),
				'value_remark'				 => $values['remark'],
				'lang_done_attribtext'		 => lang('Back to the list'),
				'lang_save_attribtext'		 => lang('Save the attribute'),
				'parent_list'				 => $parent_list,
				'tabs'						 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'					 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);
			//_debug_array($values);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_attrib_group' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_attrib()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $this->acl_location));
			}

			$entity_id	 = phpgw::get_var('entity_id', 'int');
			$cat_id		 = phpgw::get_var('cat_id', 'int');
			$id			 = phpgw::get_var('id', 'int');
			$values		 = phpgw::get_var('values');
			$group_id	 = phpgw::get_var('group_id', 'int');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

			if (!$values)
			{
				$values = array();
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if (isset($values['save']) && $values['save'])
			{
				if ($id)
				{
					$values['id']	 = $id;
					$action			 = 'edit';
				}

				$values['entity_id'] = $entity_id;
				$values['cat_id']	 = $cat_id;

				if (!$values['column_name'])
				{
					$receipt['error'][] = array('msg' => lang('Column name not entered!'));
				}

				if (!preg_match('/^[a-z0-9_]+$/i', $values['column_name']))
				{
					$receipt['error'][] = array('msg' => lang('Column name %1 contains illegal character', $values['column_name']));
				}

				if (!$values['input_text'])
				{
					$receipt['error'][] = array('msg' => lang('Input text not entered!'));
				}
				if (!$values['statustext'])
				{
					$receipt['error'][] = array('msg' => lang('Statustext not entered!'));
				}

				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg' => lang('entity type not chosen!'));
				}

				if (!$values['column_info']['type'])
				{
					$receipt['error'][] = array('msg' => lang('Datatype type not chosen!'));
				}

				if (!ctype_digit($values['column_info']['precision']) && $values['column_info']['precision'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter precision as integer !'));
					unset($values['column_info']['precision']);
				}

				if ($values['column_info']['scale'] && !ctype_digit($values['column_info']['scale']))
				{
					$receipt['error'][] = array('msg' => lang('Please enter scale as integer !'));
					unset($values['column_info']['scale']);
				}

				if (!$values['column_info']['nullable'])
				{
					$receipt['error'][] = array('msg' => lang('Nullable not chosen!'));
				}


				if (!isset($receipt['error']))
				{
					$receipt = $this->bo->save_attrib($values, $action);

					if (!$id)
					{
						$id = $receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Attribute has NOT been saved'));
				}
			}

			if ($id)
			{
				$values			 = $this->bo->read_single_attrib($entity_id, $cat_id, $id);
				$type_name		 = $values['type_name'];
				$function_msg	 = lang('edit attribute') . ' ' . lang($type_name);
				$action			 = 'edit';
			}
			else
			{
				$function_msg	 = lang('add attribute');
				$action			 = 'add';
			}

			$link_data = array
				(
				'menuaction' => 'property.uiadmin_entity.edit_attrib',
				'entity_id'	 => $entity_id,
				'cat_id'	 => $cat_id,
				'id'		 => $id,
				'type'		 => $this->type
			);

			$multiple_choice	 = false;
			$custom_get_list	 = false;
			$custom_get_single	 = false;
			switch ($values['column_info']['type'])
			{
				case 'R':
				case 'CH':
				case 'LB':
					$multiple_choice	 = true;
					break;
				case 'custom1':
					$custom_get_list	 = true;
					break;
				case 'custom2':
				case 'custom3':
					$custom_get_list	 = true;
					$custom_get_single	 = true;
					break;
				default:
			}

			$entity		 = $this->bo->read_single($entity_id, false);
			$category	 = $this->bo->read_single_category($entity_id, $cat_id);

			$msgbox_data = (isset($receipt) ? $this->bocommon->msgbox_data($receipt) : '');

			$data = array
				(
				'entity_name'						 => $entity['name'],
				'category_name'						 => $category['name'],
				'multiple_choice'					 => $multiple_choice,
				'value_table_filter'				 => $values['table_filter'],
				'value_choice'						 => (isset($values['choice']) ? $values['choice'] : ''),
				'custom_get_list'					 => $custom_get_list,
				'custom_get_single'					 => $custom_get_single,
				'msgbox_data'						 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'						 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_entity.list_attribute',
					'entity_id'	 => $entity_id,
					'cat_id'	 => $cat_id,
					'type'		 => $this->type)
				),
				'value_id'							 => $id,
				'value_column_name'					 => $values['column_name'],
				'value_input_text'					 => $values['input_text'],
				'value_statustext'					 => $values['statustext'],
				'datatype_list'						 => $this->bocommon->select_datatype($values['column_info']['type']),
				'datatype'							 => $values['column_info']['type'],
				'attrib_group_list'					 => $this->bo->get_attrib_group_list($entity_id, $cat_id, $values['group_id'] ? $values['group_id'] : $group_id),
				'value_precision'					 => $values['column_info']['precision'],
				'value_scale'						 => $values['column_info']['scale'],
				'value_default'						 => $values['column_info']['default'],
				'nullable_list'						 => $this->bocommon->select_nullable($values['column_info']['nullable']),
				'value_lookup_form'					 => $values['lookup_form'],
				'value_list'						 => $values['list'],
				'value_search'						 => $values['search'],
				'value_history'						 => $values['history'],
				'value_disabled'					 => $values['disabled'],
				'value_helpmsg'						 => $values['helpmsg'],
				'value_get_list_function'			 => $values['get_list_function'],
				'value_get_list_function_input'		 => print_r($values['get_list_function_input'], true),
				'value_get_single_function'			 => $values['get_single_function'],
				'value_get_single_function_input'	 => print_r($values['get_single_function_input'], true),
				'value_short_description'			 => $values['short_description'],
				'value_javascript_action'			 => $values['javascript_action'],
				'tabs'								 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'							 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_attrib' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function add_choice_value()
		{
			$add_controller = $this->acl->check('.checklist', PHPGW_ACL_ADD, 'controller');
			$add_location = $this->acl->check('.location', PHPGW_ACL_ADD, 'property');

			if(!$add_controller && !$add_location)
			{
				phpgw::no_access();
			}

			$location_id	 = phpgw::get_var('location_id', 'int');
			$attribute_id	 = phpgw::get_var('attribute_id', 'int');
			$new_value		 = phpgw::get_var('new_value');

			$id = $this->bo->add_choice_value($location_id, $attribute_id, $new_value);


			$receipt = array(
				'status' => 'ok',
				'choice_id' => $id
			);

			return $receipt;

		}

		function delete_choice_value()
		{
			$add_controller = $this->acl->check('.checklist', PHPGW_ACL_ADD, 'controller');
			$add_location = $this->acl->check('.location', PHPGW_ACL_ADD, 'property');

			if(!$add_controller && !$add_location)
			{
				phpgw::no_access();
			}

			$location_id	 = phpgw::get_var('location_id', 'int');
			$attribute_id	 = phpgw::get_var('attribute_id', 'int');
			$choice_id		 = phpgw::get_var('choice_id', 'int');

			$ok = $this->bo->delete_choice_value($location_id, $attribute_id, $choice_id);

			$receipt = array(
				'status' => $ok ? 'ok' : 'fail',
				'message' => $ok ? 'ok' : lang('value is used in records'),
			);

			return $receipt;

		}

		function list_custom_function()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}


			$entity_id	 = $this->entity_id;
			$cat_id		 = $this->cat_id;
			$id			 = phpgw::get_var('id', 'int');
			$resort		 = phpgw::get_var('resort');


			$entity		 = $this->bo->read_single($entity_id);
			$category	 = $this->bo->read_single_category($entity_id, $cat_id);

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";


			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				if ($resort)
				{
					$this->bo->resort_custom_function($id, $resort);
				}

				return $this->query(array
						(
						'method'	 => 'list_custom_function',
						'entity_id'	 => $entity_id,
						'cat_id'	 => $cat_id
						)
				);
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname										 = lang('attribute');
			$function_msg									 = lang('list entity attribute');
			$GLOBALS['phpgw_info']['flags']['app_header']	 = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type'	 => 'link',
								'value'	 => lang('cancel'),
								'href'	 => self::link(array
									(
									'menuaction' => 'property.uiadmin_entity.category',
									'entity_id'	 => $entity_id,
									'type'		 => $this->type
									)
								),
								'class'	 => 'new_item'
							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiadmin_entity.list_custom_function',
						'entity_id'			 => $entity_id,
						'cat_id'			 => $cat_id,
						'type'				 => $this->type,
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uiadmin_entity.edit_custom_function',
						'entity_id'	 => $entity_id,
						'cat_id'	 => $cat_id,
						'type'		 => $this->type
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'id',
							'label'		 => lang('id'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'descr',
							'label'		 => lang('descr'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'client_side',
							'label'		 => lang('client-side'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'active',
							'label'		 => lang('active'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'sorting',
							'label'		 => lang('sorting'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'up',
							'label'		 => lang('up'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.formatLinkGenericLlistAttribute'
						),
						array(
							'key'		 => 'down',
							'label'		 => lang('down'),
							'sortable'	 => false,
							'formatter'	 => 'JqueryPortico.formatLinkGenericLlistAttribute'
						),
						array(
							'key'		 => 'file_name',
							'label'		 => lang('file name'),
							'sortable'	 => false
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
						'name'	 => 'custom_function_id',
						'source' => 'id'
					),
				)
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
					'menuaction' => 'property.uiadmin_entity.edit_custom_function',
					'entity_id'	 => $entity_id,
					'cat_id'	 => $cat_id,
					'type'		 => $this->type
					)
				),
				'parameters' => json_encode($parameters)
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
					'menuaction'	 => 'property.uiadmin_entity.delete',
					'entity_id'		 => $entity_id,
					'cat_id'		 => $cat_id,
					'type'			 => $this->type,
					'acl_location'	 => ".{$this->type}.{$entity_id}.{$cat_id}"
					)
				),
				'parameters'	 => json_encode($parameters2)
			);



			unset($parameters);
			unset($parameters2);

			self::render_template_xsl('datatable_jquery', $data);
		}

		function edit_custom_function()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $this->acl_location));
			}

			$entity_id	 = phpgw::get_var('entity_id', 'int');
			$cat_id		 = phpgw::get_var('cat_id', 'int');
			$id			 = phpgw::get_var('id', 'int');
			$values		 = phpgw::get_var('values');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

//			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if ($values['save'])
			{
				if ($id)
				{
					$values['id']	 = $id;
					$action			 = 'edit';
				}

				$values['entity_id'] = $entity_id;
				$values['cat_id']	 = $cat_id;


				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg' => lang('entity type not chosen!'));
				}

				if (!$values['custom_function_file'])
				{
					$receipt['error'][] = array('msg' => lang('custom function file not chosen!'));
				}


				if (!$receipt['error'])
				{

					$receipt = $this->bo->save_custom_function($values, $action);

					if (!$id)
					{
						$id = $receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Custom function has NOT been saved'));
				}
			}

			if ($id)
			{
				$values			 = $this->bo->read_single_custom_function($entity_id, $cat_id, $id);
				$type_name		 = $values['type_name'];
				$function_msg	 = lang('edit custom function') . ' ' . lang($type_name);
				$action			 = 'edit';
			}
			else
			{
				$function_msg	 = lang('add custom function');
				$action			 = 'add';
			}

			$link_data = array
				(
				'menuaction' => 'property.uiadmin_entity.edit_custom_function',
				'entity_id'	 => $entity_id,
				'cat_id'	 => $cat_id,
				'id'		 => $id,
				'type'		 => $this->type
			);

			//_debug_array($values);

			$entity		 = $this->bo->read_single($entity_id, false);
			$category	 = $this->bo->read_single_category($entity_id, $cat_id);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
				'lang_entity'						 => lang('entity'),
				'entity_name'						 => $entity['name'],
				'lang_category'						 => lang('category'),
				'category_name'						 => $category['name'],
				'msgbox_data'						 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'						 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_entity.list_custom_function',
					'entity_id'	 => $entity_id, 'cat_id'	 => $cat_id, 'type'		 => $this->type)),
				'lang_id'							 => lang('Custom function ID'),
				'lang_entity_type'					 => lang('Entity type'),
				'lang_no_entity_type'				 => lang('No entity type'),
				'lang_save'							 => lang('save'),
				'lang_done'							 => lang('done'),
				'value_id'							 => $id,
				'lang_descr'						 => lang('descr'),
				'lang_descr_custom_functiontext'	 => lang('Enter a descr for the custom function'),
				'value_descr'						 => $values['descr'],
				'lang_done_custom_functiontext'		 => lang('Back to the list'),
				'lang_save_custom_functiontext'		 => lang('Save the custom function'),
				'lang_custom_function'				 => lang('custom function'),
				'lang_custom_function_statustext'	 => lang('Select a custom function'),
				'lang_no_custom_function'			 => lang('No custom function'),
				'custom_function_list'				 => $this->bo->select_custom_function($values['custom_function_file']),
				'value_active'						 => $values['active'],
				'value_client_side'					 => $values['client_side'],
				'lang_active'						 => lang('Active'),
				'lang_active_statustext'			 => lang('check to activate custom function'),
				'tabs'								 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'							 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

//			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_custom_function' => $data));

			self::render_template_xsl(array('admin_entity', 'datatable_inline', 'nextmatchs'), array(
				'edit_custom_function' => $data));
		}

		function convert_to_eav()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = "admin::{$this->type_app[$this->type]}::entity::convert_to_eav";
			$function											 = 'list_attribute';
			if ($custom_function_id)
			{
				$function = 'list_custom_function';
			}

			$redirect_args = array
				(
				'menuaction' => 'admin.uimainscreen.mainscreen'
			);

			if (phpgw::get_var('delete', 'bool', 'POST'))
			{
				$this->bo->convert_to_eav();
				$GLOBALS['phpgw']->redirect_link('/index.php', $redirect_args);
			}

			if (phpgw::get_var('cancel', 'bool', 'POST'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $redirect_args);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('delete'));

			$link_data = array
				(
				'menuaction' => 'property.uiadmin_entity.convert_to_eav',
			);

			$data = array
				(
				'delete_url'		 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_confirm_msg'	 => lang('do you really want to convert to eav?'),
				'lang_delete'		 => lang('yes'),
				'lang_cancel'		 => lang('no')
			);

			$function_msg = lang('convert to eav');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}
	}