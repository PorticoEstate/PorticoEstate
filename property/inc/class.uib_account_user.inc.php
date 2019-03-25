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

	class property_uib_account_user extends phpgwapi_uicommon_jquery
	{

		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $type_id;
		var $public_functions = array
			(
			'index'	 => true,
			'query'	 => true,
			'edit'	 => true,
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app']	 = true;
			$this->account_id							 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo									 = CreateObject('property.bob_account_user');
			$this->bocommon								 = CreateObject('property.bocommon');
			$this->start								 = $this->bo->start;
			$this->query								 = $this->bo->query;
			$this->sort									 = $this->bo->sort;
			$this->order								 = $this->bo->order;
			$this->filter								 = $this->bo->filter;
			$this->status_id							 = $this->bo->status_id;
			$this->allrows								 = $this->bo->allrows;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'preferences::property::b_account_user';
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
					'key'		 => 'b_account',
					'label'		 => lang('b_account'),
					'sortable'	 => false,
				),
				array
					(
					'key'		 => 'active_txt',
					'label'		 => lang('active'),
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
			);


			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction'		 => 'property.uib_account_user.query',
						'phpgw_return_as'	 => 'json'))),
				'ColumnDefs' => $myColumnDefs,
				'data'		 => '',
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			if (!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'property'))
			{
				$user_list = array(
					array('id' => $this->account_id, 'name' => $GLOBALS['phpgw_info']['user']['fullname'])
				);
			}
			else
			{
				$user_list = $this->bocommon->get_user_list_right2('select', PHPGW_ACL_READ, $this->filter, '.invoice', array(), $this->account_id);
				array_unshift($user_list, array('id' => '', 'name' => lang('select')));
			}


			$b_account_list = execMethod('property.bogeneric.get_list', array('type'		 => 'budget_account',
				'selected'	 => $b_account_id));

			foreach ($b_account_list as &$entry)
			{
				$entry['name'] = "{$entry['id']} {$entry['name']}";
			}
			array_unshift($b_account_list, array('id' => '', 'name' => lang('select')));

			$data = array
				(
				'datatable_def'	 => $datatable_def,
				'msgbox_data'	 => $msgbox_data,
				'filter_form'	 => array
					(
					'user_list'		 => array('options' => $user_list),
					'b_account_list' => array('options' => $b_account_list),
				),
				'update_action'	 => self::link(array('menuaction' => 'property.uib_account_user.edit'))
			);

			$GLOBALS['phpgw']->jqcal->add_listener('query_start');
			$GLOBALS['phpgw']->jqcal->add_listener('query_end');
			$GLOBALS['phpgw']->jqcal->add_listener('active_from');
			$GLOBALS['phpgw']->jqcal->add_listener('active_to');

			self::add_javascript('property', 'portico', 'ajax_b_account_user.js');

			$GLOBALS['phpgw']->xslttpl->add_file(array('b_account_user', 'datatable_inline'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('data' => $data));
		}

		public function query()
		{
			$user_id		 = phpgw::get_var('user_id', 'int');
			$b_account_id	 = phpgw::get_var('b_account_id');

			$values = $this->bo->read(array('user_id' => $user_id, 'b_account_id' => $b_account_id));

			$i = 0;

			foreach ($values as &$entry)
			{
				$entry['id']		 = $i++;
				$entry['b_account']	 = "{$entry['b_account_id']} {$entry['descr']}";

				$entry['active_txt'] = $entry['active'] ? 'X' : '';

				if ($entry['active'])
				{
					$entry['delete'] = "<input class=\"delete\" id=\"delete\" type =\"checkbox\" name=\"values[delete][]\" value=\"{$entry['b_account_id']}_{$entry['user_id']}\">";
					$entry['add']	 = '';
				}
				else
				{
					$entry['delete'] = '';
					$entry['add']	 = "<input class=\"add\" id=\"add\" type =\"checkbox\" name=\"values[add][]\" value=\"{$entry['b_account_id']}_{$entry['user_id']}\">";
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
			$user_id		 = phpgw::get_var('user_id', 'int');
			$b_account_id	 = phpgw::get_var('b_account_id');

			if ($values = phpgw::get_var('values'))
			{
//				if (!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'property'))
//				{
//					$receipt['error'][] = true;
//					phpgwapi_cache::message_set(lang('you are not approved for this task'), 'error');
//				}
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
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uib_account_user.index',
					'user_id'		 => $user_id, 'b_account_id'	 => $b_account_id));
			}
		}
	}