<?php
	/**
	 * phpGroupWare - registration
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package registration
	 * @version $Id: class.uidimb_role_user.inc.php 16610 2017-04-21 14:21:03Z sigurdne $
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class property_uisubstitute extends phpgwapi_uicommon_jquery
	{

		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $type_id;
		var $location_code;
		private $config;
		var $public_functions = array
			(
			'index'		 => true,
			'query'		 => true,
			'edit'		 => true,
			'edit2'		 => true,
			'substitute' => true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app']	 = true;
			$this->account_id							 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo									 = CreateObject('property.bosubstitute');
			$this->bocommon								 = CreateObject('property.bocommon');
			$this->start								 = $this->bo->start;
			$this->query								 = $this->bo->query;
			$this->sort									 = $this->bo->sort;
			$this->order								 = $this->bo->order;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::accounting::dimb_role_user2::substitute';
		}

		function index()
		{
			$receipt = array();

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$msgbox_data = array();
			if (phpgw::get_var('phpgw_return_as') != 'json' && $receipt	 = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
			{
				phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			}

			$myColumnDefs = array
				(
				array
					(
					'key'	 => 'id',
					'hidden' => true
				),
				array
					(
					'key'		 => 'user',
					'label'		 => lang('user'),
					'sortable'	 => false
				),
				array
					(
					'key'		 => 'substitute',
					'label'		 => lang('substitute'),
					'sortable'	 => false
				),
				array(
					'key'		 => 'formatted_start_time',
					'label'		 => lang('Start time'),
					'sortable'	 => false),
				array
					(
					'key'		 => 'delete',
					'label'		 => lang('delete'),
					'sortable'	 => false,
					'formatter'	 => 'JqueryPortico.FormatterCenter',
				)
			);


			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction'		 => 'property.uisubstitute.query',
						'phpgw_return_as'	 => 'json'))),
				'ColumnDefs' => $myColumnDefs,
				'data'		 => '',
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$user_list		 = $this->_get_user_list();
			$substitute_list = $user_list;

			array_unshift($user_list, array('id' => '', 'name' => lang('select')));
			array_unshift($substitute_list, array('id' => '', 'name' => lang('select')));

			$data = array
				(
				'datatable_def'	 => $datatable_def,
				'msgbox_data'	 => $msgbox_data,
				'filter_form'	 => array
					(
					'user_list'			 => array('options' => $user_list),
					'substitute_list'	 => array('options' => $substitute_list),
				),
				'update_action'	 => self::link(array('menuaction' => 'property.uisubstitute.edit2'))
			);

			self::add_javascript('property', 'portico', 'substitute.index.js');

			self::add_jquery_translation($data);
			$GLOBALS['phpgw']->jqcal2->add_listener('start_time', 'datetime');
			$GLOBALS['phpgw']->xslttpl->add_file(array('substitute', 'datatable_inline'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('table' => $data));
		}

		public function query()
		{
			$user_id			 = phpgw::get_var('user_id', 'int');
			$substitute_user_id	 = phpgw::get_var('substitute_user_id', 'int');

			$values = $this->bo->read(array('user_id' => $user_id, 'substitute_user_id' => $substitute_user_id));

			foreach ($values as &$entry)
			{
				$entry['formatted_start_time']	 = $GLOBALS['phpgw']->common->show_date($entry['start_time']);
				$entry['delete']				 = "<input class=\"delete\" id=\"delete\" type =\"checkbox\" name=\"delete[]\" value=\"{$entry['id']}\">";
				$results['results'][]			 = $entry;
			}

			$result_data = array
				(
				'results'		 => $values,
				'total_records'	 => count($values),
				'draw'			 => phpgw::get_var('draw', 'int')
			);


			return $this->jquery_results($result_data);

			//	return json_encode($values);
		}

		public function edit2()
		{
			if (!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'property'))
			{
				$receipt['error'][] = true;
				phpgwapi_cache::message_set(lang('you are not approved for this task'), 'error');
			}

			$user_id			 = phpgw::get_var('user_id', 'int');
			$substitute_user_id	 = phpgw::get_var('substitute_user_id', 'int');
			$start_time			 = phpgw::get_var('start_time', 'date', 'POST', time());

			$save = phpgw::get_var('save', 'string');

			if ($save && $user_id && $substitute_user_id)
			{
				if ($this->bo->update_substitute($user_id, $substitute_user_id, $start_time))
				{
					$result = array
						(
						'status' => 'updated'
					);
				}
			}

			if ($delete = phpgw::get_var('delete', 'int'))
			{
				if (!$receipt['error'])
				{
					if ($this->bo->delete($delete))
					{
						$result = array
							(
							'status' => 'updated'
						);
					}
					else
					{
						$result = array
							(
							'status' => 'error'
						);
					}
				}
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				if ($receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
				{
					phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
					$result['receipt'] = $receipt;
				}
				else
				{
					$result['receipt'] = array();
				}
				return $result;
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'		 => 'property.uisubstitute.index',
					'user_id'			 => $user_id, 'substitute_user_id' => $substitute_user_id));
			}
		}

		public function edit()
		{
			$user_id			 = $this->account_id;
			$substitute_user_id	 = phpgw::get_var('substitute_user_id', 'int', 'POST');
			$start_time			 = phpgw::get_var('start_time', 'date', 'POST');
			$save				 = phpgw::get_var('save', 'string', 'POST');

			if ($save)
			{
				$this->bo->update_substitute($user_id, $substitute_user_id, $start_time);
				if ($delete = phpgw::get_var('delete', 'int'))
				{
					$this->bo->delete($delete);
				}
			}

//			$selected = $this->bo->get_substitute($user_id);

			$substitute_user_list = $this->bo->get_substitute_list($user_id);

			$i = 1;
			foreach ($substitute_user_list as &$substitute_user)
			{
				$substitute_user['sort_key']			 = $i++;
				$substitute_user['formatted_start_time'] = $GLOBALS['phpgw']->common->show_date($substitute_user['start_time']);
				$substitute_user['user_name']			 = $GLOBALS['phpgw']->accounts->get($substitute_user['substitute_user_id'])->__toString();
				$substitute_user['select']				 = '<input type="checkbox" name="delete[]" class="mychecks" value="' . $substitute_user['id'] . '" title="' . $substitute_user['id'] . '"/>';
			}

			$appname										 = lang('substitute');
			$function_msg									 = lang('set substitute');
			$GLOBALS['phpgw_info']['flags']['app_header']	 = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$controls_def									 = array
				(
				array('key' => 'id', 'hidden' => true),
				array('key'		 => 'sort_key', 'label'		 => '#', 'sortable'	 => true, 'resizeable' => true,
					'className'	 => 'center'),
				array('key' => 'user_name', 'label' => lang('User'), 'sortable' => false, 'resizeable' => true),
				array('key'		 => 'formatted_start_time', 'label'		 => lang('Start time'), 'sortable'	 => false,
					'resizeable' => true),
				array('key'		 => 'active', 'label'		 => lang('active'), 'sortable'	 => false,
					'resizeable' => true,
					'className'	 => 'center'),
				array('key'		 => 'select', 'label'		 => lang('delete'), 'sortable'	 => false,
					'resizeable' => true,
					'className'	 => 'center'),
			);

			$tabletools = array
				(
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $controls_def,
				'data'		 => json_encode($substitute_user_list),
				'tabletools' => $tabletools,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('order' => json_encode(array(0, 'asc')))
				)
			);

			$tabs			 = array();
			$tabs['assign']	 = array('label' => lang('assign'), 'link' => '#assign');

			$data = array
				(
				'datatable_def'		 => $datatable_def,
				'tabs'				 => phpgwapi_jquery::tabview_generate($tabs, 0),
				'value_active_tab'	 => 0,
				'form_action'		 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uisubstitute.edit')),
				'user_list'			 => array('options' => $this->_get_user_list()),
			);

			$GLOBALS['phpgw']->jqcal2->add_listener('start_time', 'datetime');

			$GLOBALS['phpgw']->xslttpl->add_file(array('substitute', 'datatable_inline'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit' => $data));
		}

		private function _get_user_list( $selected = 0 )
		{
			$users_controller	 = (array)$GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, '.checklist', 'controller');
			$users_property		 = (array)$GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, '.project', 'property');

			$users_gross = array();


			foreach ($users_controller as $user)
			{
				$name								 = (isset($user['account_lastname']) ? $user['account_lastname'] . ' ' : '') . $user['account_firstname'];
				$users_gross[$user['account_id']]	 = array(
					'id'	 => $user['account_id'],
					'name'	 => $name
				);
			}
			unset($user);
			foreach ($users_property as $user)
			{
				$name								 = (isset($user['account_lastname']) ? $user['account_lastname'] . ' ' : '') . $user['account_firstname'];
				$users_gross[$user['account_id']]	 = array(
					'id'	 => $user['account_id'],
					'name'	 => $name
				);
			}

			$user_list		 = array();
			$account_name	 = array();
			$selected_found	 = false;
			foreach ($users_gross as $value)
			{
				$user_list[]	 = $value;
				$account_name[]	 = $value['name'];
				if (!$selected_found)
				{
					$selected_found = $value['id'] == $selected ? true : false;
				}
			}

			if ($selected && !$selected_found)
			{
				$name		 = $GLOBALS['phpgw']->accounts->get($selected)->__toString();
				$user_list[] = array
					(
					'id'		 => $selected,
					'name'		 => $name,
					'selected'	 => 1
				);

				$account_name[] = $name;
			}

			// Sort the data with account_name ascending
			// Add $data as the last parameter, to sort by the common key
			if ($user_list)
			{
				array_multisort($account_name, SORT_ASC, $user_list);
			}

			return $user_list;
		}
	}