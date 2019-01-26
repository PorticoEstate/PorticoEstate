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
	 * @package controller
	 * @version $Id$
	 */

	phpgw::import_class('phpgwapi.uicommon');

	class controller_uibulk_update extends phpgwapi_uicommon
	{
		var $currentapp;
		var $bo;

		var $public_functions = array(
			'assign'  => true,
			'get_controller_serie'	=> true,
			'get_future_checklist'	=> true
		);


		function __construct()
		{
			parent::__construct();
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->currentapp = 'controller';
			$this->so = CreateObject('controller.socontrol');

			$this->acl_read = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_READ, 'controller');//1
			$this->acl_add = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_ADD, 'controller');//2
			$this->acl_edit = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_EDIT, 'controller');//4
			$this->acl_delete = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_DELETE, 'controller');//8
			
			self::set_active_menu("controller::bulk_update_assigned");
		}

		function assign()
		{
			$from = phpgw::get_var('from', 'int');
			$to = phpgw::get_var('to', 'int');
			$save = phpgw::get_var('save', 'bool', 'POST');
			$serie_ids = phpgw::get_var('serie_ids', 'int');
			$check_list_ids = phpgw::get_var('check_list_ids', 'int');


			if($save && $from && $to)
			{
				$this->so->save_bulk_uppdate_assign(array('from' => $from, 'to' => $to, 'serie_ids' => $serie_ids, 'check_list_ids' => $check_list_ids));
			}

			$tabs = array();
			$tabs['assign'] = array('label' => lang('assign'), 'link' => '#assign');

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, '.checklist');
			$from_list = array();
			foreach ($users as $user)
			{
				$from_list[] = array
				(
					'id' => $user['account_id'],
					'name' => "{$user['account_lastname']}, {$user['account_firstname']}",
					'selected' => 0
				);
			}
			unset($user);

			$to_list = array();
			foreach ($from_list as $user)
			{
				$user['selected'] = $user['id'] == $to ? 1 : 0;
				$to_list[] = $user;
			}

			array_unshift($from_list, array('id' => '', 'name' => lang('Select')));

			$controls_def = array
			(
				array('key' => 'component_name', 'label' => lang('where'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'serie_id', 'label' => 'serie', 'sortable' => false, 'resizeable' => true),
//				array('key' => 'control_id', 'label' => lang('controller'), 'sortable' => false,
//					'resizeable' => true),
				array('key' => 'title', 'label' => lang('title'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'assigned_to_name', 'label' => lang('user'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'start_date', 'label' => lang('start date'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'repeat_type', 'label' => lang('repeat type'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'repeat_interval', 'label' => lang('interval'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'controle_time', 'label' => lang('controle time'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'service_time', 'label' => lang('service time'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'total_time', 'label' => lang('total time'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'serie_enabled', 'label' => lang('enabled'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'select','label'=>lang('select'),'sortable'=>false,'resizeable'=>true,'className' => 'center'),
				array('key' => 'location_id', 'hidden' => true),
				array('key' => 'component_id', 'hidden' => true),
				array('key' => 'id', 'hidden' => true),
				array('key' => 'assigned_to', 'hidden' => true),
			);

			$tabletools = array
			(
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			$datatable_def[] = array
			(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $controls_def,
				'data' => json_encode(array()),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$_checklists_def = array
			(
				array('key' => 'component_name', 'label' => lang('where'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'id', 'label' => lang('id'), 'sortable' => false),
				array('key' => 'control_name', 'label' => lang('name'), 'sortable' => false),
	//			array('key' => 'status', 'label' => lang('status'), 'sortable' => true),
				array('key' => 'user', 'label' => lang('user'), 'sortable' => false),
				array('key' => 'deadline', 'label' => lang('deadline'), 'sortable' => false),
				array('key' => 'planned_date', 'label' => lang('planned date'), 'sortable' => true),
				array('key' => 'select','label'=>lang('select'),'sortable'=>false,'resizeable'=>true,'className' => 'center'),
			);

			$datatable_def[] = array
			(
					'container' => 'datatable-container_1',
					'requestUrl' => "''",
					'ColumnDefs' => $_checklists_def,
					'data' => json_encode(array()),
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true),
		//				array('singleSelect' => true)
					)
				);

			$data = array
			(
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uibulk_update.assign")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uibulk_update.assign",)),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, 0),
				'value_active_tab' => 0,
				'from_list' => array('options' => $from_list),
				'to_list' => array('options' => $to_list),
				'datatable_def' => $datatable_def,
			);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . ' :: ' . lang('bulk update assigned');
			self::add_javascript('controller', 'base', 'bulk_update.js');
			self::render_template_xsl(array('bulk_update', 'datatable_inline'), array('assign' => $data));
		}

		function get_controller_serie( )
		{
			$assigned_to = phpgw::get_var('assigned_to', 'int');

			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$repeat_type_array = array
			(
				"0" => lang('day'),
				"1" => lang('week'),
				"2" => lang('month'),
				"3" => lang('year')
			);

			$lang_history = lang('history');
			$controls = $this->so->get_controls_for_assigned($assigned_to);
			$soentity = createObject('property.soentity');

			foreach ($controls as &$entry)
			{
				$menuaction = 'controller.uicomponent.index';

				$control_link_data = array
					(
					'menuaction' => $menuaction,
					'location_id' => $entry['location_id'],
					'component_id' => $entry['component_id'],
					'filter_component' => "{$entry['location_id']}_{$entry['component_id']}"
				);

				$entry['component_name'] = $soentity->get_short_description(array('location_id' => $entry['location_id'], 'id' => $entry['component_id']));
				$entry['title_text'] = $entry['title'];
				$entry['title'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', $control_link_data) . '" target="_blank">' . $entry['title'] . '</a>';
				$entry['assigned_to_name'] = "<a title=\"{$lang_history}\" onclick='javascript:showlightbox_assigned_history({$entry['serie_id']});'>{$entry['assigned_to_name']}</a>";

				$entry['start_date'] = $GLOBALS['phpgw']->common->show_date($entry['start_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$entry['repeat_type'] = $repeat_type_array[$entry['repeat_type']];
				$entry['total_time'] = $entry['service_time'] + $entry['controle_time'];
				$entry['select'] = '<input type="checkbox" name="serie_ids[]" class="mychecks" value="' . $entry['serie_id'] . '" title="'. $entry['serie_id'] .'"/>';
			}

			$result_data = array
				(
				'results' => $controls,
				'total_records' => count($controls),
				'draw' => phpgw::get_var('draw', 'int')
			);

			return $this->jquery_results($result_data);
		}

		public function get_future_checklist()
		{
			$assigned_to = phpgw::get_var('assigned_to', 'int');

			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			if($assigned_to)
			{
				$assigned_to_name = $GLOBALS['phpgw']->accounts->get($assigned_to)->__toString();
			}
			$soentity = createObject('property.soentity');
			$check_lists = CreateObject('controller.socheck_list')->get_assigned_future_checklist($assigned_to);

			$_statustext = array();
			$_statustext[0] = lang('open');
			$_statustext[1] = lang('closed');
			$_statustext[2] = lang('pending');
			$_check_list = array();

			foreach ($check_lists as $check_list)
			{
				$_link = $GLOBALS['phpgw']->link('/index.php', array(
						'menuaction' => "controller.uicheck_list.edit_check_list",
						'check_list_id' => $check_list['id']
						)
				);
				$_check_list[] = array
				(
					'id' => $check_list['id'],
					'component_name' => $soentity->get_short_description(array('location_id' => $check_list['location_id'], 'id' => $check_list['component_id'])),
					'control_name' => "<a href=\"{$_link}\" >{$check_list['control_name']}</a>",
					'serie_id' => $_statustext[$check_list['serie_id']],
					'status' => $_statustext[$check_list['status']],
					'user' => $assigned_to_name,
					'deadline' => $GLOBALS['phpgw']->common->show_date($check_list['deadline'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
					'planned_date' => $GLOBALS['phpgw']->common->show_date($check_list['planned_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
					'select'	=> '<input type="checkbox" name="check_list_ids[]" class="mychecks" value="' . $check_list['id'] . '" title="'. $check_list['id'] .'"/>'
				);
				unset($_link);
			}
	
			$result_data = array
				(
				'results' => $_check_list,
				'total_records' => count($_check_list),
				'draw' => phpgw::get_var('draw', 'int')
			);

			return $this->jquery_results($result_data);
		}
	}
