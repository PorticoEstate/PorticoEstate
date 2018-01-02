<?php
/**
	 * phpGroupWare - eventplanner: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this permission was funded by http://www.bergen.kommune.no/
	 * @package eventplanner
	 * @subpackage permission
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('eventplanner', 'permission', 'inc/model/');

	class eventplanner_uipermission extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
			'object'	=> true,
			'delete'	=> true
		);

		protected
			$fields,
			$permissions,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('permission');
			$this->bo = createObject('eventplanner.bopermission');
			$this->fields = eventplanner_permission::get_fields();
			$this->permissions = eventplanner_permission::get_instance()->get_permission_array();
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::permission");
		}

		private function get_category_options( $selected = 0 )
		{
			$category_list = array();
			$category_list[] = array('id' => '','name' => lang('select'));
			$category_list[] = array('id' => 'customer', 'name' => lang('customer'));
	//		$category_list[] = array('id' => 'vendor', 'name' => lang('vendor'));

			foreach ($category_list as $option)
			{
				$options['selected'] = $option['id'] == $selected ? 1 : 0;
			}
			return $category_list;
		}

		public function index()
		{
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_jquery::load_widget('autocomplete');

			$function_msg = lang('permission');

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'filter',
								'name' => 'filter_object_type',
								'text' => lang('category'),
								'list' =>  $this->get_category_options()
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => "{$this->currentapp}.uipermission.index",
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => "{$this->currentapp}.uipermission.add")),
					'editor_action' => '',
					'field' => parent::_get_fields()
				)
			);

			$parameters = array(
				'parameter' => array(
					array(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uipermission.edit"
				)),
				'parameters' => json_encode($parameters)
			);

			if (!empty($this->permissions[PHPGW_ACL_ADD]))
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'delete',
					'statustext' => lang('delete entry'),
					'text' => lang('delete'),
					'confirm_msg' => lang('do you really want to delete this entry'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'eventplanner.uipermission.delete'
					)),
					'parameters' => json_encode($parameters)
				);

			}

			self::add_javascript('eventplanner', 'portico', 'permission.index.js');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}

		function delete()
		{
			if (empty($this->permissions[PHPGW_ACL_DELETE]))
			{
				phpgw::no_access();
			}

			$id = phpgw::get_var('id', 'int');
			if ($this->bo->delete($id))
			{
				return lang('entry %1 has been deleted', $id);
			}
			else
			{
				return lang('delete failed');
			}
		}
		/*
		 * Edit the price item with the id given in the http variable 'id'
		 */

		public function edit( $values = array(), $mode = 'edit' )
		{
			$active_tab = !empty($values['active_tab']) ? $values['active_tab'] : phpgw::get_var('active_tab', 'string', 'REQUEST', 'first_tab');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			if (!empty($values['object']))
			{
				$permission = $values['object'];
			}
			else
			{
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$permission = $this->bo->read_single($id);
			}

			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('permission'),
				'link' => '#first_tab'
			);

			$category_list = $this->get_category_options( $permission->object_type );
			unset($category_list[0]);

			$data = array(
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uipermission.save")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uipermission.index",)),
				'permission' => $permission,
				'object_type_list' => array('options' => $category_list),
				'subject_list' => array('options' => $this->get_subjet($permission->subject_id)),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);

			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript('eventplanner', 'portico', 'permission.edit.js');
			self::render_template_xsl(array('permission'), array('edit' => $data));
		}
		
		public function save()
		{
			parent::save();
		}

		public function object()
		{
			$object_type = phpgw::get_var('object_type');

			switch ($object_type)
			{
				case 'customer':
					return createObject('eventplanner.uicustomer')->get_list();
					break;
				case 'vendor':
					return createObject('eventplanner.uivendor')->get_list();
					break;
				default:
					break;
			}
		}

		public function get_subjet($selected = 0)
		{
			$users_frontend = (array)$GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_READ, 'run', 'eventplannerfrontend');
			$users_backend = (array)$GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_READ, 'run', 'eventplanner');

			$users = array();
			foreach ($users_frontend as $user)
			{
				$users[$user['account_id']] = $user;
			}
			unset($user);

			foreach ($users_backend as $user)
			{
				$users[$user['account_id']] = $user;
			}
			unset($user);

			$user_list = array();
			$account_name = array();
			foreach ($users as $user)
			{
				$name = (isset($user['account_lastname']) ? $user['account_lastname'] . ' ' : '') . $user['account_firstname'];
				$account_name[] = $name;
				$user_list[] = array
				(
					'id' => $user['account_id'],
					'name' => $name,
					'selected' => $user['account_id'] == $selected ? 1 : 0
				);
			}

			array_multisort($account_name, SORT_ASC, $user_list);

			array_unshift($user_list, array('id' => '','name' => lang('select')));

			return $user_list;
		}
	}