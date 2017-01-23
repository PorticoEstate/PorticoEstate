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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package eventplanner
	 * @subpackage resource
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('eventplanner', 'resource', 'inc/model/');
	use eventplanner_resource;

	class eventplanner_uiresource extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
		);

		protected
			$fields,
			$composite_types,
			$payment_methods,
			$permissions;

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('eventplanner::resource');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('resource');
			$this->bo = createObject('eventplanner.boresource');
			$this->fields = eventplanner_resource::get_fields();
			$this->permissions = eventplanner_resource::get_instance()->get_permission_array();
		}

		private function get_status_options( $selected = 0 )
		{
			$status_options = array();
			$status_list = eventplanner_resource::get_status_list();

			$status_options[] = array(
				'id' => '',
				'name' => lang('all')
			);

			foreach ($status_list as $_key => $_value)
			{
				$status_options[] = array(
					'id' => $_key,
					'name' => $_value,
					'selected' => $_key == $selected ? 1 : 0
				);
			}
			return $status_options;
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

			$status_options = $this->get_status_options();
			$function_msg = lang('resource');

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'filter',
								'name' => 'filter_status',
								'text' => lang('status'),
								'list' => $status_options
							),
							array('type' => 'autocomplete',
								'name' => 'ecodimb',
								'app' => 'property',
								'ui' => 'generic',
								'label_attr' => 'descr',
						//		'show_id'=> true,
								'text' => lang('dimb') . ':',
								'requestGenerator' => 'requestWithDimbFilter',
							),
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'eventplanner.uiresource.index',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => 'eventplanner.uiresource.add')),
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
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array
					(
					'menuaction' => 'eventplanner.uiresource.view'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array
					(
					'menuaction' => 'eventplanner.uiresource.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript('eventplanner', 'portico', 'resource.index.js');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
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
				$resource = $values['object'];
			}
			else
			{
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$resource = $this->bo->read_single($id);
			}

			if (empty($this->permissions[PHPGW_ACL_EDIT]))
			{
				$step = 1;
			}
			else if ($resource->get_id())
			{
				$step = 2;
			}

			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('resource'),
				'link' => '#first_tab',
				'function' => "set_tab('first_tab')"
			);
			$tabs['party'] = array(
				'label' => lang('party'),
				'link' => '#party',
				'function' => "set_tab('party')"
				);
			if($step > 1)
			{
				$tabs['assignment'] = array(
					'label' => lang('assignment'),
					'link' => '#assignment',
					'function' => "set_tab('assignment')"
				);
			}

			$composite_types = array();
			foreach ($this->composite_types as $_key => $_value)
			{
				$composite_types[] = array('id' => $_key, 'name' => $_value);
			}

			$payment_methods = array();
			foreach ($this->payment_methods as $_key => $_value)
			{
				$payment_methods[] = array('id' => $_key, 'name' => $_value);
			}

			$bocommon = CreateObject('property.bocommon');

			$GLOBALS['phpgw']->jqcal->add_listener('date_start');
			$GLOBALS['phpgw']->jqcal->add_listener('date_end');
			$GLOBALS['phpgw']->jqcal->add_listener('assign_date_start');
			$GLOBALS['phpgw']->jqcal->add_listener('assign_date_end');

			$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, '.resource', 'eventplanner');
			$executive_officer_options[] = array('id' => '', 'name' => lang('nobody'), 'selected' => 0);
			foreach ($accounts as $account)
			{
				$executive_officer_options[] = array(
					'id' => $account['account_id'],
					'name' => $GLOBALS['phpgw']->accounts->get($account['account_id'])->__toString(),
					'selected' => ($account['account_id'] == $resource->executive_officer) ? 1 : 0
				);
			}
			$comments = (array)$resource->comments;
			foreach ($comments as $key => &$comment)
			{
				$comment['value_count'] = $key +1;
				$comment['value_date'] = $GLOBALS['phpgw']->common->show_date($comment['time']);
			}

			$comments_def = array(
				array('key' => 'value_count', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'author', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'comment', 'label' => lang('Note'), 'sortable' => true, 'resizeable' => true)
			);
 
			$datatable_def[] = array(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $comments_def,
				'data' => json_encode($comments),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$data = array(
				'datatable_def' => $datatable_def,
				'form_action' => self::link(array('menuaction' => 'eventplanner.uiresource.save')),
				'cancel_url' => self::link(array('menuaction' => 'eventplanner.uiresource.index',)),
				'resource' => $resource,//->toArray(),
				'list_executive_officer' => array('options' => $executive_officer_options),
				'step'		=> $step,
				'value_ecodimb_descr' => ExecMethod('property.bogeneric.get_single_attrib_value', array(
					'type' => 'dimb',
					'id' => $resource->ecodimb_id,
					'attrib_name' => 'descr')
				),
				'district_list' => array('options' => $bocommon->select_district_list('', $resource->district_id)),
				'composite_type_list' => array('options' => $bocommon->select_list($resource->composite_type_id, $composite_types)),
				'payment_method_list' => array('options' => $bocommon->select_list($resource->payment_method, $payment_methods)),
				'status_list' => array('options' => $this->get_status_options($resource->status)),
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);
			phpgwapi_jquery::formvalidator_generate(array('date', 'security', 'file'));
			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('eventplanner', 'portico', 'resource.edit.js');
			self::render_template_xsl(array('resource', 'datatable_inline'), array($mode => $data));
		}

		
//		public function save()
//		{
//			parent::save();
//		}

	}