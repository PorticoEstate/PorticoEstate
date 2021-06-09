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
	phpgw::import_class('phpgwapi.uicommon_jquery');

	/**
	 * Description
	 * @package property
	 */
	class property_uiadmin_location extends phpgwapi_uicommon_jquery
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
			'query'					 => true,
			'index'					 => true,
			'config'				 => true,
			'edit_config'			 => true,
			'view'					 => true,
			'edit'					 => true,
			'delete'				 => true,
			'list_attribute'		 => true,
			'edit_attrib'			 => true,
			'list_attribute_group'	 => true,
			'edit_attrib_group'		 => true,
			'save'					 => true,
			'save_attrib'			 => true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app']			 = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'admin::property::location';

			$this->nextmatchs	 = CreateObject('phpgwapi.nextmatchs');
			$this->account		 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo			 = CreateObject('property.boadmin_location', true);
			$this->bocommon		 = CreateObject('property.bocommon');

			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.admin.location';
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
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start'	 => $this->start,
				'query'	 => $this->query,
				'sort'	 => $this->sort,
				'order'	 => $this->order,
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			#$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::location';

			$this->bocommon->reset_fm_cache();

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query(array
						(
						'method' => 'index',
						)
				);
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = lang('entity');
			$function_msg	 = lang('list entity type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type'	 => 'link',
								'value'	 => lang('cancel'),
								'href'	 => self::link(array(
									'menuaction' => 'admin.uimainscreen.mainscreen'
								)),
								'class'	 => 'new_item'
							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiadmin_location.index',
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uiadmin_location.edit'
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'location_id',
							'label'		 => lang('location_id'),
							'sortable'	 => FALSE
						),
						array(
							'key'		 => 'id',
							'label'		 => lang('standard id'),
							'sortable'	 => TRUE
						),
						array(
							'key'		 => 'name',
							'label'		 => lang('name'),
							'sortable'	 => TRUE
						),
						array(
							'key'		 => 'descr',
							'label'		 => lang('Descr'),
							'sortable'	 => FALSE
						),
						array
							(
							'key'		 => 'enable_controller',
							'label'		 => lang('enable controller'),
							'sortable'	 => false
						),
						array
							(
							'key'		 => 'list_address',
							'label'		 => lang('address'),
							'sortable'	 => false
						),
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
						'name'	 => 'type_id',
						'source' => 'id'
					),
				)
			);
			$parameters3 = array
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
				'my_name'	 => 'categories',
				'statustext' => lang('categories'),
				'text'		 => lang('Categories'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uigeneric.index',
					'type'		 => 'location'
				)),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'attribute_groups',
				'statustext' => lang('attribute groups'),
				'text'		 => lang('attribute groups'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_location.list_attribute_group'
				)),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'attributes',
				'statustext' => lang('attributes'),
				'text'		 => lang('Attributes'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_location.list_attribute'
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
				'parameters' => json_encode($parameters3)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'edit',
				'statustext' => lang('edit'),
				'text'		 => lang('edit'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiadmin_location.edit'
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
					'menuaction' => 'property.uiadmin_location.delete'
				)),
				'parameters'	 => json_encode($parameters)
			);
			unset($parameters);
			unset($parameters2);
			unset($parameters3);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query( $data = array() )
		{
			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$type_id = phpgw::get_var('type_id', 'int');

			switch ($data['method'])
			{
				case 'list_attribute':
					$id_type = $type_id;
					break;
				case 'list_attribute_group':
					$id_type = $type_id;
					break;
				default:$id_type = '';
					break;
			}

			$export	 = phpgw::get_var('export', 'bool');
			$params	 = array(
				'start'		 => $this->start,
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'sort'		 => $order[0]['dir'],
				'order'		 => $columns[$order[0]['column']]['data'],
				'allrows'	 => phpgw::get_var('length', 'int') == -1 || $export,
				'type_id'	 => $id_type
			);

			$result_objects	 = array();
			$result_count	 = 0;

			switch ($data['method'])
			{
				case 'list_attribute':
					$values	 = $this->bo->read_attrib($params);
					break;
				case 'list_attribute_group':
					$values	 = $this->bo->read_attrib_group($params);
					break;
				default:
					$values	 = $this->bo->read($params);
					foreach ($values as &$entry)
					{
						$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$entry['id']}");
					}
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
						'menuaction' => 'property.uiadmin_location.list_attribute',
						'allrows'	 => $this->allrows,
						'type_id'	 => $type_id
					);
					array_walk($result_data['results'], array($this, '_add_links'), $variable);
					break;
				case 'list_attribute_group':
					$variable	 = array(
						'menuaction' => 'property.uiadmin_location.list_attribute_group',
						'allrows'	 => $this->allrows,
						'type_id'	 => $type_id
					);
					array_walk($result_data['results'], array($this, '_add_links'), $variable);
					break;
			}
			return $this->jquery_results($result_data);
		}

		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}

			$id		 = (int)phpgw::get_var('id');
			$values	 = phpgw::get_var('values');

			if (!isset($values['name']) || !$values['name'])
			{
				$receipt['error'][] = array('msg' => lang('Name not entered!'));
			}
			if ($id)
			{
				$values['id'] = $id;
			}

			if (!isset($receipt['error']))
			{
				try
				{
					$receipt	 = $this->bo->save($values);
					$id			 = $receipt['id'];
					$msgbox_data = $this->bocommon->msgbox_data($receipt);
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

				$message = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				phpgwapi_cache::message_set($message[0]['msgbox_text'], 'message');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiadmin_location.edit',
					'id'		 => $id));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('Table has NOT been saved'));
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

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_location'));

			if ($id)
			{
				$values			 = $this->bo->read_single($id);
				$function_msg	 = lang('edit standard');
				$action			 = 'edit';
			}
			else
			{
				$function_msg	 = lang('add standard');
				$action			 = 'add';
			}


			$link_data = array
				(
				'menuaction' => 'property.uiadmin_location.save',
				'id'		 => $id
			);

			$msgbox_data = (isset($receipt) ? $this->bocommon->msgbox_data($receipt) : '');

			$data = array
				(
				'msgbox_data'				 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_name_standardtext'	 => lang('Enter a name of the standard'),
				'form_action'				 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'				 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_location.index')),
				'value_id'					 => (isset($id) ? $id : ''),
				'value_name'				 => (isset($values['name']) ? $values['name'] : ''),
				'lang_id_standardtext'		 => lang('Enter the standard ID'),
				'lang_descr_standardtext'	 => lang('Enter a description of the standard'),
				'lang_done_standardtext'	 => lang('Back to the list'),
				'lang_save_standardtext'	 => lang('Save the standard'),
				'value_descr'				 => (isset($values['descr']) ? $values['descr'] : ''),
				'lang_select'				 => lang('select'),
				'value_list_info'			 => $this->bo->get_list_info((isset($id) ? $id : ''), $values['list_info']),
				'lang_list_info_statustext'	 => lang('Names of levels to list at this level'),
				'value_enable_controller'	 => $values['enable_controller'],
				'value_list_address'		 => isset($values['list_address']) ? $values['list_address'] : '',
				'value_list_documents'		 => isset($values['list_documents']) ? $values['list_documents'] : '',
				'tabs'						 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'					 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$appname = lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit' => $data));
		}

		function delete()
		{
			if (!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 8, 'acl_location'	 => $this->acl_location));
			}

			$group_id	 = phpgw::get_var('group_id', 'int');
			$attrib		 = phpgw::get_var('attrib');
			$type_id	 = phpgw::get_var('type_id', 'int');
			$id			 = phpgw::get_var('id', 'int');
			$confirm	 = phpgw::get_var('confirm', 'bool', 'POST');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$receipt = $this->bo->delete($type_id, $id, $attrib, $group_id);

				//FIXME
				if (isset($receipt['message']))
				{
					return $receipt['message'][0]['msg'];
				}
				else
				{
					return $receipt['error'][0]['msg'];
				}
			}

			if ($attrib)
			{
				$function = 'list_attribute';
			}
			else
			{
				$function = 'index';
			}
			$link_data = array
				(
				'menuaction' => 'property.uiadmin_location.' . $function,
				'type_id'	 => $type_id
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_location.delete',
					'id'		 => $id, 'attrib'	 => $attrib, 'type_id'	 => $type_id)),
				'lang_confirm_msg'		 => lang('do you really want to delete this entry'),
				'lang_yes'				 => lang('yes'),
				'lang_yes_standardtext'	 => lang('Delete the entry'),
				'lang_no_standardtext'	 => lang('Back to the list'),
				'lang_no'				 => lang('no')
			);

			$appname		 = lang('location');
			$function_msg	 = lang('delete location standard');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
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

			$type_id = phpgw::get_var('type_id', 'int');
			$id		 = phpgw::get_var('id', 'int');
			$resort	 = phpgw::get_var('resort');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::location::attribute_loc_{$type_id}";

			if ($resort)
			{
				$this->bo->resort_attrib_group(array('resort'	 => $resort, 'type_id'	 => $type_id,
					'id'		 => $id));
			}

			$type = $this->bo->read_single($type_id);

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query(array
						(
						'method' => 'list_attribute_group'
				));
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = lang('attribute');
			$function_msg	 = lang('list entity attribute group');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . $appname . '::' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type'	 => 'link',
								'value'	 => lang('cancel'),
								'href'	 => self::link(array(
									'menuaction' => 'property.uiadmin_location.index'
								)),
								'class'	 => 'new_item'
							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiadmin_location.list_attribute_group',
						'type_id'			 => $type_id,
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uiadmin_location.edit_attrib_group',
						'type_id'	 => $type_id
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'name',
							'label'		 => lang('Name'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'descr',
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
							'key'	 => 'id',
							'label'	 => lang('id'),
							'hidden' => true
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
					'menuaction' => 'property.uiadmin_location.edit_attrib_group',
					'type_id'	 => $type_id
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
					'menuaction' => 'property.uiadmin_location.delete',
					'type_id'	 => $type_id
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

			$type_id											 = phpgw::get_var('type_id', 'int');
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 .= "::location::attribute_loc_{$type_id}";
			$location											 = ".location.{$type_id}";
			$id													 = phpgw::get_var('id', 'int');
			$values												 = phpgw::get_var('values');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

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

				$values['location'] = $location;

				if (!$values['group_name'])
				{
					$receipt['error'][] = array('msg' => lang('group name not entered!'));
				}

				if (!$values['descr'])
				{
					$receipt['error'][] = array('msg' => lang('description not entered!'));
				}

				if (!$location)
				{
					$receipt['error'][] = array('msg' => lang('location not chosen!'));
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
				$values			 = $this->bo->read_single_attrib_group($location, $id);
				$type_name		 = $values['type_name'];
				$function_msg	 = lang('edit attribute group') . ' ' . lang($type_name);
				$action			 = 'edit';
			}
			else
			{
				$function_msg	 = lang('add attribute group');
				$action			 = 'add';
			}

			$link_data = array
				(
				'menuaction' => 'property.uiadmin_location.edit_attrib_group',
				'type_id'	 => $type_id,
				'id'		 => $id
			);


			$type = $this->bo->read_single($type_id, false);

			$msgbox_data = (isset($receipt) ? $this->bocommon->msgbox_data($receipt) : '');

			$data = array
				(
				'lang_entity'				 => lang('location'),
				'entity_name'				 => $type['name'],
				'msgbox_data'				 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'				 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_location.list_attribute_group',
					'type_id'	 => $type_id)),
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
				'tabs'						 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'					 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$appname = lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_attrib_group' => $data));
		}

		function list_attribute()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$type_id = phpgw::get_var('type_id', 'int');
			$id		 = phpgw::get_var('id', 'int');
			$resort	 = phpgw::get_var('resort');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::location::attribute_loc_{$type_id}";

			if ($resort)
			{
				$this->bo->resort_attrib(array('resort' => $resort, 'type_id' => $type_id, 'id' => $id));
			}

			$type = $this->bo->read_single($type_id);

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query(array
						(
						'method' => 'list_attribute'
				));
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = lang('attribute');
			$function_msg	 = lang('list entity attribute');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type'	 => 'link',
								'value'	 => lang('cancel'),
								'href'	 => self::link(array(
									'menuaction' => 'property.uiadmin_location.index'
								)),
								'class'	 => 'new_item'
							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiadmin_location.list_attribute',
						'type_id'			 => $type_id,
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uiadmin_location.edit_attrib',
						'type_id'	 => $type_id
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'column_name',
							'label'		 => lang('Name'),
							'sortable'	 => TRUE
						),
						array(
							'key'		 => 'input_text',
							'label'		 => lang('Descr'),
							'sortable'	 => FALSE
						),
						array(
							'key'		 => 'trans_datatype',
							'label'		 => lang('Datatype'),
							'sortable'	 => FALSE
						),
						array(
							'key'		 => 'group_id',
							'label'		 => lang('group'),
							'sortable'	 => FALSE
						),
						array(
							'key'		 => 'attrib_sort',
							'label'		 => lang('sorting'),
							'sortable'	 => TRUE
						),
						array(
							'key'		 => 'up',
							'label'		 => lang('up'),
							'sortable'	 => FALSE,
							'formatter'	 => 'JqueryPortico.formatLinkGenericLlistAttribute'
						),
						array(
							'key'		 => 'down',
							'label'		 => lang('down'),
							'sortable'	 => FALSE,
							'formatter'	 => 'JqueryPortico.formatLinkGenericLlistAttribute'
						),
						array(
							'key'	 => 'id',
							'label'	 => lang('id'),
							'hidden' => TRUE
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

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'edit',
				'statustext' => lang('Edit'),
				'text'		 => lang('Edit'),
				'action'	 => $GLOBALS['phpgw']->link
					(
					'/index.php', array
					(
					'menuaction' => 'property.uiadmin_location.edit_attrib',
					'type_id'	 => $type_id
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
					'menuaction' => 'property.uiadmin_location.delete',
					'type_id'	 => $type_id,
					'attrib'	 => true
					)
				),
				'parameters'	 => json_encode($parameters)
			);

			unset($parameters);
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function save_attrib()
		{
			if (!$_POST)
			{
				return $this->edit_attrib();
			}

			$type_id = (int)phpgw::get_var('type_id');
			$id		 = (int)phpgw::get_var('id');
			$values	 = phpgw::get_var('values');

			if ($id)
			{
				$values['id']	 = $id;
				$action			 = 'edit';
			}
			$type_id = $values['type_id'];

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

			if (!$values['type_id'])
			{
				$receipt['error'][] = array('msg' => lang('Location type not chosen!'));
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

			if (!$receipt['error'])
			{
				try
				{
					$receipt = $this->bo->save_attrib($values, $action);
					if (!$id)
					{
						$id = $receipt['id'];
					}
					$msgbox_data = $this->bocommon->msgbox_data($receipt);
				}
				catch (Exception $e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
					$this->edit_attrib($values);
					return;
				}

				$message = $GLOBALS['phpgw']->common->msgbox($msgbox_data);

				phpgwapi_cache::message_set($message[0]['msgbox_text'], 'message');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiadmin_location.edit_attrib',
					'id'		 => $id, 'type_id'	 => $type_id));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('Attribute has NOT been saved'));
			}
		}

		function edit_attrib()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $this->acl_location));
			}

			$type_id = phpgw::get_var('type_id', 'int');
			$id		 = phpgw::get_var('id', 'int');
			$values	 = phpgw::get_var('values');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::location::attribute_loc_{$type_id}";

			if (!$values)
			{
				$values = array();
			}

			//_debug_array($values);
			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_location'));

			if ($id)
			{
				$values			 = $this->bo->read_single_attrib($type_id, $id);
				$function_msg	 = lang('edit attribute') . ' ' . $values['input_text'];
				$action			 = 'edit';
			}
			else
			{
				$function_msg	 = lang('add attribute');
				$action			 = 'add';
			}


			$link_data = array
				(
				'menuaction' => 'property.uiadmin_location.save_attrib',
				'id'		 => $id
			);
			//_debug_array($values);

			$multiple_choice = '';
			if ($values['column_info']['type'] == 'R' || $values['column_info']['type'] == 'CH' || $values['column_info']['type'] == 'LB')
			{
				$multiple_choice = true;
			}


			$msgbox_data = (isset($receipt) ? $this->bocommon->msgbox_data($receipt) : '');

			$data = array
				(
				'lang_choice'					 => lang('Choice'),
				'lang_new_value'				 => lang('New value'),
				'lang_new_value_statustext'		 => lang('New value for multiple choice'),
				'multiple_choice'				 => $multiple_choice,
				'value_table_filter'			 => $values['table_filter'],
				'value_choice'					 => (isset($values['choice']) ? $values['choice'] : ''),
				'lang_delete_value'				 => lang('Delete value'),
				'lang_value'					 => lang('value'),
				'lang_delete_choice_statustext'	 => lang('Delete this value from the list of multiple choice'),
				'msgbox_data'					 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_location.list_attribute',
					'type_id'	 => $type_id)),
				'lang_id'						 => lang('Attribute ID'),
				'lang_location_type'			 => lang('Type'),
				'lang_no_location_type'			 => lang('No entity type'),
				'lang_save'						 => lang('save'),
				'lang_done'						 => lang('done'),
				'value_id'						 => $id,
				'lang_column_name'				 => lang('Column name'),
				'value_column_name'				 => $values['column_name'],
				'lang_column_name_statustext'	 => lang('enter the name for the column'),
				'lang_input_text'				 => lang('input text'),
				'value_input_text'				 => $values['input_text'],
				'lang_input_name_statustext'	 => lang('enter the input text for records'),
				'lang_id_attribtext'			 => lang('Enter the attribute ID'),
				'lang_entity_statustext'		 => lang('Select a entity type'),
				'lang_statustext'				 => lang('Statustext'),
				'lang_statustext_attribtext'	 => lang('Enter a statustext for the inputfield in forms'),
				'value_statustext'				 => $values['statustext'],
				'lang_done_attribtext'			 => lang('Back to the list'),
				'lang_save_attribtext'			 => lang('Save the attribute'),
				'type_id'						 => $values['type_id'],
				'entity_list'					 => $this->bo->select_location_type($type_id),
				'select_location_type'			 => 'values[type_id]',
				'datatype_list'					 => $this->bocommon->select_datatype($values['column_info']['type']),
				'value_search'					 => $values['search'],
				'attrib_group_list'				 => $this->bo->get_attrib_group_list($type_id, $values['group_id']),
				'value_precision'				 => $values['column_info']['precision'],
				'value_scale'					 => $values['column_info']['scale'],
				'value_default'					 => $values['column_info']['default'],
				'nullable_list'					 => $this->bocommon->select_nullable($values['column_info']['nullable']),
				'value_lookup_form'				 => $values['lookup_form'],
				'value_list'					 => $values['list'],
				'tabs'							 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'						 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date',
					'security', 'file'))
			);
			//_debug_array($data);

			$appname = lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_attrib' => $data));
		}

		function config()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::config';

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$search	 = phpgw::get_var('search');
				$order	 = phpgw::get_var('order');
				$draw	 = phpgw::get_var('draw', 'int');
				$columns = phpgw::get_var('columns');

				$params = array(
					'start'		 => $this->start,
					'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
					'query'		 => $search['value'],
					'sort'		 => $order[0]['dir'],
					'order'		 => $columns[$order[0]['column']]['data'],
					'allrows'	 => phpgw::get_var('length', 'int') == -1,
				);

				$standard_list			 = $this->bo->read_config($params);
				$text_edit				 = lang('edit');
				$lang_edit_standardtext	 = lang('edit the column relation');

				$content = array();
				foreach ($standard_list as $standard)
				{
					$content[] = array(
						'column_name'	 => $standard['column_name'],
						'name'			 => $standard['location_name'],
						'link_edit'		 => "<a title =\"{$lang_edit_standardtext}\" href=\"" . $GLOBALS['phpgw']->link('/index.php',
																								   array('menuaction'	 => 'property.uiadmin_location.edit_config',
								'column_name'	 => $standard['column_name']
						)) . "\">{$text_edit}</a>",
					);
				}
				$result_data					 = array('results' => $content);
				$result_data['total_records']	 = $this->bo->total_records;
				$result_data['draw']			 = $draw;
				return $this->jquery_results($result_data);
			}

			$appname		 = lang('location');
			$function_msg	 = lang('list config');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form'			 => array(
					'toolbar' => array(
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiadmin_location.config',
						'phpgw_return_as'	 => 'json'
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
							'key'		 => 'name',
							'label'		 => lang('table name'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'link_edit',
							'label'		 => lang('edit'),
							'sortable'	 => false
						),
					)
				)
			);

			$parameters = array
				(
				'parameter' => array(
					array(
						'name'	 => 'column_name',
						'source' => 'column_name'
					),
				)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'edit',
				'statustext' => lang('Edit'),
				'text'		 => lang('Edit'),
				'action'	 => $GLOBALS['phpgw']->link(
					'/index.php', array(
					'menuaction' => 'property.uiadmin_location.edit_config',
					)
				),
				'parameters' => json_encode($parameters)
			);

			unset($parameters);
			self::render_template_xsl('datatable_jquery', $data);
		}

		function edit_config()
		{
			if (!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 16, 'acl_location'	 => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::config';

			$column_name = phpgw::get_var('column_name');
			$values		 = phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_location'));

			if (isset($values['save']) && $values['save'])
			{
				$receipt = $this->bo->save_config($values, $column_name);
			}

			$type_id = $this->bo->read_config_single($column_name);

			$function_msg = lang('edit location config for') . ' ' . $column_name;

			$link_data = array
				(
				'menuaction'	 => 'property.uiadmin_location.edit_config',
				'column_name'	 => $column_name
			);

			$msgbox_data = (isset($receipt) ? $this->bocommon->msgbox_data($receipt) : '');

			$data = array
				(
				'msgbox_data'			 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_location.config')),
				'lang_column_name'		 => lang('Column name'),
				'lang_save'				 => lang('save'),
				'lang_done'				 => lang('done'),
				'column_name'			 => $column_name,
				'value_name'			 => (isset($values['name']) ? $values['name'] : ''),
				'location_list'			 => $this->bo->select_location_type($type_id),
				'lang_config_statustext' => lang('Select the level for this information'),
				'lang_done_standardtext' => lang('Back to the list'),
				'lang_save_standardtext' => lang('Save the standard'),
				'type_id'				 => (isset($values['type_id']) ? $values['type_id'] : ''),
				'value_descr'			 => (isset($values['descr']) ? $values['descr'] : '')
			);

			$appname = lang('location');

			//_debug_array($data);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_config' => $data));
		}
	}