<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2018 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage helpdesk
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon');

	class controller_uisettings extends phpgwapi_uicommon
	{

		var $public_functions = array
		(
			'edit'			=> true,
			'users'			=> true,
		);

		private $acl_location, $acl_read, $acl_add, $acl_edit, $acl_delete,
			$so, $cats, $so_control;

		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('controller::settings');
			
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('preferences');

			$this->cats		= CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$this->cats->supress_info = true;
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.control';
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'controller');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'controller');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'controller');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'controller');
			$this->so			= CreateObject('controller.sosettings');
			$this->so_control = CreateObject('controller.socontrol');

			self::add_javascript('controller', 'base', 'settings.edit.js');

		}

		public function add()
		{
			if(!$this->acl_add)
			{
				phpgw::no_access();
			}

			$this->edit();
		}

		public function view()
		{
			if(!$this->acl_read)
			{
				phpgw::no_access();
			}

			/**
			 * Do not allow save / send here
			 */
			if(phpgw::get_var('save', 'bool') || phpgw::get_var('send', 'bool') || phpgw::get_var('init_preview', 'bool'))
			{
				phpgw::no_access();
			}
			$this->edit(array(), 'view');
		}


		public function edit( $error = false)
		{

			if(!$this->acl_add || !$this->acl_edit)
			{
				phpgw::no_access();
			}


			if(!$error && phpgw::get_var('save', 'bool'))
			{
				$this->save();
			}

			$controls = CreateObject('controller.socontrol')->get(0, 0, 'controller_control.title', true, '', '', array());
//			_debug_array($controls);

			$categories = $this->cats->return_sorted_array(0, false);
//			_debug_array($categories);
			$settings = $this->so->read();
//			_debug_array($settings);die();

			$cat_header[] = array
			(
				'lang_name'				=> lang('name'),
				'lang_status'			=> lang('status'),
				'lang_edit'				=> lang('edit'),
			);

			$html2text	 = createObject('phpgwapi.html2text');

			$content = array();
			foreach ($controls as $control)
			{
				$control_name	= $GLOBALS['phpgw']->strip_html($control->get_title());
				$html2text->setHtml($control->get_description());
				$control_name	.= '<p>' .($html2text->getText()) . '</p>';

				$selected_cat = !empty($settings[$control->get_id()]['cat_id']) ? $settings[$control->get_id()]['cat_id'] : 0;

				$_cat_list = $categories;

				foreach ($_cat_list as &$cat)
				{
					$level		= $cat['level'];
					$cat_name	= $GLOBALS['phpgw']->strip_html($cat['name']);

					if ($level > 0)
					{
						$space = ' . ';
						$spaceset = str_repeat($space,$level);
						$cat_name = $spaceset . $cat_name;
					}

					$cat['name'] = $cat_name;

					$cat['selected'] = $selected_cat == $cat['id'] ? 1 : 0;
				}

				$content[] = array
				(
					'control_id'				=> $control->get_id(),
					'name'						=> $control_name,
					'cat_list'					=> array('options' => $_cat_list),
				);
			}

			$link_data['menuaction'] = 'controller.uisettings.edit';

			$cat_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a category'),
				'action_url'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_done'				=> lang('done'),
				'lang_done_statustext'	=> lang('return to admin mainscreen')
			);

			$data = array
			(
				'form_action'	 => self::link(array('menuaction' => "{$this->currentapp}.uisettings.edit")),
				'edit_action'	 => self::link(array('menuaction' => "{$this->currentapp}.uisettings.edit")),
				'cancel_url'	 => self::link(array('menuaction' => "{$this->currentapp}.uitts.index")),
				'cat_header'	 => $cat_header,
				'cat_data'		 => $content,
				'cat_add'		 => $cat_add,
				'tabs'			 => self::_generate_tabs('category_assignment'),
			);

//			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang($mode);

			self::render_template_xsl(array('settings'), array('edit' => $data));

		}

		protected function _generate_tabs( $active_tab = '' )
		{
			$tabs = array(
				'category_assignment' => array('label' => lang('category assignment'), 'link' => self::link(array('menuaction' => "{$this->currentapp}.uisettings.edit"))),
				'users' => array('label' => lang('users'), 'link' => self::link(array('menuaction' => "{$this->currentapp}.uisettings.users"))),
//				'vendors' => array('label' => lang('vendors'), 'link' => self::link(array('menuaction' => "{$this->currentapp}.uisettings.vendors"))),
			);

			foreach ($tabs as $key => &$tab)
			{
				if($active_tab == $key)
				{
					$tab['link'] = "#{$key}";
				}
			}

			return phpgwapi_jquery::tabview_generate($tabs, $active_tab);
		}

		public function save()
		{
			$values = phpgw::get_var('values');

			try
			{
				$receipt = $this->so->save($values);
			}
			catch (Exception $e)
			{
				if ($e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
					$this->edit($error = true);
					return;
				}
			}

			$this->receipt['message'][] = array('msg' => lang('category assignment has been saved'));

			self::message_set($this->receipt);
			self::redirect(array('menuaction' => "{$this->currentapp}.uisettings.edit"));
		}

		private function save_users()
		{
			$values = phpgw::get_var('values');

			try
			{
				$receipt = $this->so->save_users($values);
			}
			catch (Exception $e)
			{
				if ($e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
					$this->users( $error = true);
					return;
				}
			}

			$this->receipt['message'][] = array('msg' => lang('user settings has been saved'));

			self::message_set($this->receipt);
			self::redirect(array('menuaction' => "{$this->currentapp}.uisettings.users"));
		}


		public function users($error = false)
		{
			if(!$this->acl_read)
			{
				phpgw::no_access();
			}

			if(!$error && phpgw::get_var('save', 'bool'))
			{
				$this->save_users();
			}

			$control_area_id = phpgw::get_var('control_area_id', 'int');
			$control_id		 = phpgw::get_var('control_id', 'int');
			$part_of_town_id = phpgw::get_var('part_of_town_id', 'int');
			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			if($control_area_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_control_area_id", $control_area_id, $user_id);
			}
			else
			{
				$control_area_id = (int)phpgwapi_cache::user_get('controller', "calendar_control_area_id", $user_id);
			}
			if($control_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_planner_control_id", $control_id, $user_id);
			}
			else
			{
				$control_id = (int)phpgwapi_cache::user_get('controller', "calendar_planner_control_id", $user_id);
			}
			if($part_of_town_id)
			{
				phpgwapi_cache::user_set('controller', "settings_control_id", $part_of_town_id, $user_id);
			}
			else
			{
				$part_of_town_id = (int)phpgwapi_cache::user_get('controller', "settings_control_id", $user_id);
			}

			$control_types = $this->so_control->get_controls_by_control_area($control_area_id);

			$control_type_list = array(array('id' => '-1', 'name' => lang('select')));
			foreach ($control_types as $control_type)
			{
				$control_type_list[] = array(
					'id'		 => $control_type['id'],
					'name'		 => $control_type['title'],
					'selected'	 => $control_id == $control_type['id'] ? 1 : 0
				);
			}

			$cats				 = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	 = true;

			$control_area = $cats->formatted_xslt_list(array('format'	 => 'filter', 'globals'	 => true,
				'use_acl'	 => $this->_category_acl));


			$control_area_list = array();
			foreach ($control_area['cat_list'] as $cat_list)
			{
				$control_area_list[] = array
					(
					'id'		 => $cat_list['cat_id'],
					'name'		 => $cat_list['name'],
					'selected'	 => $control_area_id == $cat_list['cat_id'] ? 1 : 0
				);
			}

			array_unshift($control_area_list, array('id' => '-1', 'name' => lang('select')));

			$dateformat = "{$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']} H:i";

			
			$part_of_towns = createObject('property.bogeneric')->get_list(array(
				'type'		 => 'part_of_town',
				'selected'	 => $part_of_town_id,
				'order'		 => 'name',
				'sort'		 => 'asc'
				)
			);

			$part_of_town_list = array(array('id' => '-1', 'name' => lang('select')));

			foreach ($part_of_towns as $part_of_town)
			{
				if($part_of_town['id'] > 0)
				{
					$part_of_town_list[] = $part_of_town;				
				}

			}

			if($control_id > 0 && $control_area_id > 0 && $part_of_town_id > 0)
			{
				$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, '.checklist');
			}
			else
			{
				$users = array();
			}

			/**
			 * Bit-operator
			 */
			$roles = array(
				array(
					'id' => 1,
					'name' => lang('administrator')
				),
				array(
					'id' => 2,
					'name' => lang('inspector')// 'Kontrollør'
				),
				array(
					'id' => 4,
					'name' => lang('supervisor')// 'Soneleder'
				),
			);

			$user_roles = $this->so->get_roles($control_id, $part_of_town_id);
//			_debug_array($user_roles);
			$user_list = array();

			$lang_status = array(
				'A' => lang('acktive'),
				'I' => lang('inacktive'),
			);


			foreach ($users as $user)
			{
				$selected_role = $user_roles[$user['account_id']];

				$user_list[] = array
				(
					'id' => $user['account_id'],
					'name' => "{$user['account_lastname']}, {$user['account_firstname']}",
					'lastlogin' => $GLOBALS['phpgw']->common->show_date($user['account_lastlogin'], $dateformat),
					'status' => $lang_status[$user['account_status']],
					'selected_role' => array(1 & $selected_role, 2 & $selected_role, 4 & $selected_role),
					'control_id' => $control_id,
					'part_of_town_id'	=> $part_of_town_id,
					'original_value' => $user_roles[$user['account_id']]
				);
			}

	//		_debug_array($user_list);
			$data = array
			(
				'user_data'			 => $user_list,
				'roles'				 => array('options' => $roles),
				'control_area_list'	 => array('options' => $control_area_list),
				'control_type_list'	 => array('options' => $control_type_list),
				'part_of_town_list'	 => array('options' => $part_of_town_list),
				'form_action'	 => self::link(array('menuaction' => "{$this->currentapp}.uisettings.users")),
				'edit_action'	 => self::link(array('menuaction' => "{$this->currentapp}.uisettings.users")),
				'cancel_url'	 => self::link(array('menuaction' => "{$this->currentapp}.uitts.index")),
				'cat_header'	 => $cat_header,
				'cat_data'		 => $content,
				'cat_add'		 => $cat_add,
				'tabs'			 => self::_generate_tabs('users'),
			);

			phpgwapi_jquery::load_widget('bootstrap-multiselect');
			self::render_template_xsl(array('settings'), array('users' => $data));

		}
	}