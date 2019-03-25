<?php
	/**
	 * phpGroupWare - registration
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class property_uidimb_role_user extends phpgwapi_uicommon_jquery
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
			'substitute' => true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app']	 = true;
			$this->account_id							 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo									 = CreateObject('property.bodimb_role_user');
			$this->bocommon								 = CreateObject('property.bocommon');
			$this->start								 = $this->bo->start;
			$this->query								 = $this->bo->query;
			$this->sort									 = $this->bo->sort;
			$this->order								 = $this->bo->order;
			$this->filter								 = $this->bo->filter;
			$this->status_id							 = $this->bo->status_id;
			$this->allrows								 = $this->bo->allrows;

			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'admin::property::accounting::dimb_role_user2';
			$this->config										 = CreateObject('phpgwapi.config', 'property');
			$this->config->read();
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
					'key'		 => 'ecodimb',
					'label'		 => lang('dim b'),
					'sortable'	 => false,
					'formatter'	 => 'JqueryPortico.FormatterRight',
				),
				array
					(
					'key'		 => 'role',
					'label'		 => lang('role'),
					'formatter'	 => 'JqueryPortico.FormatterRight',
					'sortable'	 => true
				),
				array
					(
					'key'		 => 'default_user',
					'label'		 => lang('default'),
					'sortable'	 => false,
					'formatter'	 => 'JqueryPortico.FormatterCenter',
				),
				array
					(
					'key'		 => 'active_from',
					'label'		 => lang('date from'),
					'sortable'	 => true,
					'formatter'	 => 'JqueryPortico.FormatterRight',
				),
				array
					(
					'key'		 => 'active_to',
					'label'		 => lang('date to'),
					'sortable'	 => false,
					'formatter'	 => 'JqueryPortico.FormatterCenter',
				),
				array
					(
					'key'		 => 'add',
					'label'		 => lang('add'),
					'sortable'	 => false,
					'formatter'	 => 'JqueryPortico.FormatterCenter',
				),
				array
					(
					'key'		 => 'delete',
					'label'		 => lang('delete'),
					'sortable'	 => false,
					'formatter'	 => 'JqueryPortico.FormatterCenter',
				),
				array
					(
					'key'		 => 'alter_date',
					'label'		 => lang('alter date'),
					'sortable'	 => false,
					'formatter'	 => 'JqueryPortico.FormatterCenter',
				),
			);


			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction'		 => 'property.uidimb_role_user.query',
						'phpgw_return_as'	 => 'json'))),
				'ColumnDefs' => $myColumnDefs,
				'data'		 => '',
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);


			$user_list	 = $this->bocommon->get_user_list_right2('select', PHPGW_ACL_READ, $this->filter, '.invoice', array(), $this->account_id);
			$role_list	 = execMethod('property.bogeneric.get_list', array('type'		 => 'dimb_role',
				'selected'	 => $role));
			$dimb_list	 = execMethod('property.bogeneric.get_list', array('type'		 => 'dimb',
				'selected'	 => $dimb));

			array_unshift($user_list, array('id' => '', 'name' => lang('select')));
			array_unshift($role_list, array('id' => '', 'name' => lang('select')));
			array_unshift($dimb_list, array('id' => '', 'name' => lang('select')));

			$data = array
				(
				'datatable_def'	 => $datatable_def,
				'msgbox_data'	 => $msgbox_data,
				'filter_form'	 => array
					(
					'user_list'	 => array('options' => $user_list),
					'role_list'	 => array('options' => $role_list),
					'dimb_list'	 => array('options' => $dimb_list),
				),
				'update_action'	 => self::link(array('menuaction' => 'property.uidimb_role_user.edit'))
			);

			$GLOBALS['phpgw']->jqcal->add_listener('query_start');
			$GLOBALS['phpgw']->jqcal->add_listener('query_end');
			$GLOBALS['phpgw']->jqcal->add_listener('active_from');
			$GLOBALS['phpgw']->jqcal->add_listener('active_to');

			self::add_javascript('property', 'portico', 'ajax_dimb_role_user.js');

			$GLOBALS['phpgw']->xslttpl->add_file(array('dimb_role_user', 'datatable_inline'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('data' => $data));
		}

		public function query()
		{
			$user_id	 = phpgw::get_var('user_id', 'int');
			$dimb_id	 = phpgw::get_var('dimb_id', 'int');
			$role_id	 = phpgw::get_var('role_id', 'int');
			$query_start = phpgw::get_var('query_start');
			$query_end	 = phpgw::get_var('query_end');

//			$this->bo->allrows = true;
			$values = $this->bo->read(array('user_id'		 => $user_id, 'dimb_id'		 => $dimb_id,
				'role_id'		 => $role_id, 'query_start'	 => $query_start, 'query_end'		 => $query_end));

			foreach ($values as &$entry)
			{
				if ($entry['active_from'])
				{
					$default_user_checked	 = $entry['default_user'] == 1 ? 'checked = "checked"' : '';
					$default_user_orig		 = $entry['default_user'] == 1 ? $entry['id'] : '';
					$entry['default_user']	 = "<input  type =\"hidden\"  name=\"values[default_user_orig][]\" value=\"{$default_user_orig}\">";
					$entry['default_user']	 .= "<input class=\"default_user\" id=\"default_user\" type =\"checkbox\" $default_user_checked name=\"values[default_user][]\" value=\"{$entry['id']}\">";
					$entry['delete']		 = "<input class=\"delete\" id=\"delete\" type =\"checkbox\" name=\"values[delete][]\" value=\"{$entry['id']}\">";
					$entry['alter_date']	 = "<input class=\"alter_date\" id=\"alter_date\" type =\"checkbox\" name=\"values[alter_date][]\" value=\"{$entry['id']}\">";
					$entry['add']			 = '';
				}
				else
				{
					$entry['default_user_orig']	 = '';
					$entry['default_user']		 = '';
					$entry['delete']			 = '';
					$entry['alter_date']		 = '';
					$entry['add']				 = "<input class=\"add\" id=\"add\" type =\"checkbox\" name=\"values[add][]\" value=\"{$entry['ecodimb']}_{$entry['role_id']}_{$entry['user_id']}\">";
				}
				$results['results'][] = $entry;
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

		public function edit()
		{
			$user_id = phpgw::get_var('user_id', 'int');
			$dimb_id = phpgw::get_var('dimb_id', 'int');
			$role_id = phpgw::get_var('role_id', 'int');
			$query	 = phpgw::get_var('query');

			if ($values = phpgw::get_var('values'))
			{
				if (!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'property'))
				{
					$receipt['error'][] = true;
					phpgwapi_cache::message_set(lang('you are not approved for this task'), 'error');
				}
				if (!$receipt['error'])
				{
					if ($this->bo->edit($values))
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
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uidimb_role_user.index',
					'user_id'	 => $user_id, 'dimb_id'	 => $dimb_id, 'role_id'	 => $role_id, 'query'		 => $query));
			}
		}
	}