<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 * @version $Id$
	 */
	/*
	  This program is free software: you can redistribute it and/or modify
	  it under the terms of the GNU General Public License as published by
	  the Free Software Foundation, either version 2 of the License, or
	  (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU General Public License for more details.

	  You should have received a copy of the GNU General Public License
	  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * ResponsibleMatrix - handles automated assigning of tasks based on (physical)location and category.
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class property_uiresponsible extends phpgwapi_uicommon_jquery
	{

		/**
		 * @var integer $start for pagination
		 */
		var $start = 0;

		/**
		 * @var string $sort how to sort the queries - ASC/DESC
		 */
		var $sort;

		/**
		 * @var string $order field to order by in queries
		 */
		var $order;

		/**
		 * @var object $nextmatchs paging handler
		 */
		var $nextmatchs;

		/**
		 * @var object $bo business logic
		 */
		var $bo;

		/**
		 * @var object $acl reference to global access control list manager
		 */
		var $acl;

		/**
		 * @var string $acl_location the access control location
		 */
		var $acl_location;

		/**
		 * @var string $appname the application name
		 */
		var $appname;

		/**
		 * @var bool $acl_read does the current user have read access to the current location
		 */
		var $acl_read;

		/**
		 * @var bool $acl_add does the current user have add access to the current location
		 */
		var $acl_add;

		/**
		 * @var bool $acl_edit does the current user have edit access to the current location
		 */
		var $acl_edit;

		/**
		 * @var bool $allrows display all rows of result set?
		 */
		var $allrows;
		var $query;

		/**
		 * @var array $public_functions publicly available methods of the class
		 */
		var $public_functions = array
			(
			'query'			 => true,
			'index'			 => true,
			'contact'		 => true,
			'edit'			 => true,
			'edit_role'		 => true,
			'edit_contact'	 => true,
			'no_access'		 => true,
			'delete_type'	 => true,
			'view'			 => true,
			'save'			 => true
		);

		/**
		 * Constructor
		 */
		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->bo			 = CreateObject('property.boresponsible', true);
			$this->nextmatchs	 = CreateObject('phpgwapi.nextmatchs');
			$this->bocommon		 = CreateObject('property.bocommon');

			$this->acl					 = & $GLOBALS['phpgw']->acl;
			$this->acl_location			 = $this->bo->get_acl_location();
			$this->acl_read				 = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add				 = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit				 = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete			 = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->bolocation			 = CreateObject('preferences.boadmin_acl');
			$this->appname				 = $this->bo->appname;
			$this->bolocation->acl_app	 = $this->appname;
			$this->location				 = $this->bo->location;
			$this->cats					 = & $this->bo->cats;
			$this->query				 = $this->bo->query;
			$this->allrows				 = $this->bo->allrows;
			$this->sort					 = $this->bo->sort;
			$this->order				 = $this->bo->order;
			$this->cat_id				 = $this->bo->cat_id;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::{$this->appname}::responsible_matrix";
		}

		/**
		 * Save sessiondata
		 *
		 * @return void
		 */
		private function _save_sessiondata()
		{
			$data = array
				(
				'start'		 => $this->start,
				'query'		 => $this->query,
				'sort'		 => $this->sort,
				'order'		 => $this->order,
				'location'	 => $this->location,
				'allrows'	 => $this->allrows,
				'cat_id'	 => $this->cat_id
			);
			$this->bo->save_sessiondata($data);
		}

		/**
		 * list available responsible types
		 *
		 * @return void
		 */
		private function _get_Filter()
		{
			$values_combo_box	 = array();
			$combos				 = array();

			$locations = $GLOBALS['phpgw']->locations->get_locations(false, $this->appname, false, false, true);
			foreach ($locations as $loc_id => $loc_descr)
			{
				$values_combo_box[0][] = array
					(
					'id'	 => $loc_id,
					'name'	 => "{$loc_id} [{$loc_descr}]",
				);
			}

			$default_value	 = array('id' => '', 'name' => lang('No location'));
			array_unshift($values_combo_box[0], $default_value);
			$combos[]		 = array
				(
				'type'	 => 'filter',
				'name'	 => 'loc_id',
				'text'	 => lang('location'),
				'list'	 => $values_combo_box[0]
			);
			return $combos;
		}

		function index()
		{

			$bocommon = CreateObject('property.bocommon');

			if (!$this->acl_read)
			{
				$this->no_access();
				return;
			}

			$lookup = phpgw::get_var('lookup', 'bool');

			if ($lookup)
			{
				$GLOBALS['phpgw_info']['flags']['noframework']	 = true;
				$GLOBALS['phpgw_info']['flags']['headonly']		 = true;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');


			$function_msg = lang('list available responsible types');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";

			$data = array(
				'datatable_name' => 'responsible',
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uiresponsible.index',
						'location'			 => $this->location,
						'lookup'			 => $lookup,
						'appname'			 => $this->appname,
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction' => 'property.uiresponsible.edit',
						'appname'	 => $this->appname,
						'location'	 => $this->location
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
							'key'		 => 'name',
							'label'		 => lang('name'),
							'sortable'	 => true
						),
						array(
							'key'		 => 'descr',
							'label'		 => lang('descr'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'category',
							'label'		 => lang('category'),
							'sortable'	 => false,
							'hidden'	 => true
						),
						array(
							'key'		 => 'created_by',
							'label'		 => lang('user'),
							'sortable'	 => false,
							'hidden'	 => true
						),
						array(
							'key'		 => 'created_on',
							'label'		 => lang(''),
							'sortable'	 => false,
							'hidden'	 => true
						),
						array(
							'key'		 => 'appname',
							'label'		 => lang('application'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'active',
							'label'		 => lang('active'),
							'sortable'	 => false,
							'hidden'	 => true
						),
						array(
							'key'		 => 'loc',
							'label'		 => lang(''),
							'sortable'	 => false,
							'hidden'	 => true
						),
						array(
							'key'		 => 'location',
							'label'		 => lang(''),
							'sortable'	 => false,
							'hidden'	 => true
						)
					)
				)
			);

			$filters = $this->_get_Filter();
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			if (!$lookup)
			{
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
						array
							(
							'name'	 => 'location',
							'source' => 'location'
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
						),
						array
							(
							'name'	 => 'location',
							'source' => 'location'
						),
					)
				);

				if ($this->acl_edit)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name'	 => 'edit',
						'text'		 => lang('edit'),
						'action'	 => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiresponsible.edit',
							'appname'	 => $this->appname
//								'location'		=> $this->location
						)),
						'parameters' => json_encode($parameters3)
					);
				}

				if ($this->acl_delete)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name'		 => 'delete',
						'text'			 => lang('delete'),
						'confirm_msg'	 => lang('do you really want to delete this entry'),
						'action'		 => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiresponsible.delete_type',
							'appname'	 => $this->appname
						)),
						'parameters'	 => json_encode($parameters)
					);
				}

				if ($this->acl_add)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name'	 => 'add',
						'text'		 => lang('add'),
						'action'	 => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiresponsible.edit',
							'appname'	 => $this->appname,
							'location'	 => $this->location
						))
					);
				}

				unset($parameters);
			}

			if ($lookup)
			{

				$function_exchange_values = '';

				$function_exchange_values	 .= 'opener.document.getElementsByName("responsibility_id")[0].value = "";' . "\r\n";
				$function_exchange_values	 .= 'opener.document.getElementsByName("responsibility_name")[0].value = "";' . "\r\n";

				$function_exchange_values	 .= 'opener.document.getElementsByName("responsibility_id")[0].value = data.getData("id");' . "\r\n";
				$function_exchange_values	 .= 'opener.document.getElementsByName("responsibility_name")[0].value = data.getData("name");' . "\r\n";

				$function_exchange_values .= 'window.close()';

				$datatable['exchange_values']	 = $function_exchange_values;
				$datatable['valida']			 = '';
			}

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
				'appname'	 => $this->appname,
				'location'	 => $this->location
			);

			$result_objects	 = array();
			$result_count	 = 0;

			$values = $this->bo->read_type($params);

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

			$id			 = phpgw::get_var('id', 'int');
			$location	 = phpgw::get_var('location', 'string');
			$values		 = phpgw::get_var('values');

			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				if ($GLOBALS['phpgw']->session->is_repost())
				{
					//				$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
				}

				if (!isset($values['location']) || !$values['location'])
				{
//					$receipt['error'][]=array('msg'=>lang('Please select a location!'));
				}

				if (!isset($values['name']) || !$values['name'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter a name!'));
				}

				if ($id)
				{
					$values['id'] = $id;
				}
				else
				{
					$id = $values['id'];
				}

				if (!$receipt['error'])
				{
					try
					{

						$receipt = $this->bo->save_type($values);
						$id		 = $receipt['id'];

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiresponsible.index',
								'appname'	 => $this->appname));
						}
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
					self::message_set($receipt);

					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiresponsible.edit',
						'appname'	 => $this->appname, 'id'		 => $id));
				}
				else
				{
					$this->edit();
				}
			}
			else
			{
				$this->edit($values);
			}
		}

		function edit()
		{
			if (!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $this->acl_location));
			}

			$id			 = phpgw::get_var('id', 'int');
			$location	 = phpgw::get_var('location', 'string');
			$values		 = phpgw::get_var('values');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			if (isset($values['cancel']) && $values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiresponsible.index',
					'appname'	 => $this->appname));
			}

			if ($id)
			{
				$values			 = $this->bo->read_single($id);
				$function_msg	 = lang('edit responsible');

//				  $this->acl->set_account_id($this->account);
//				  $grants	= $this->acl->get_grants('property','.responsible');
//				  if(!$this->bocommon->check_perms2($values['created_by'], $grants, PHPGW_ACL_READ))
//				  {
//					  phpgw::no_access();
//				  }
			}
			else
			{
				$function_msg = lang('add responsible');
			}

			$link_data = array
				(
				'menuaction' => 'property.uiresponsible.save',
				'id'		 => $id,
				'app'		 => $this->appname
			);

			$locations = $GLOBALS['phpgw']->locations->get_locations(false, $this->appname, false, false, true);

			$selected_location = $location ? $location : $values['location'];
			if (isset($values['location_id']) && $values['location_id'] && !$selected_location)
			{
				$locations_info		 = $GLOBALS['phpgw']->locations->get_name($values['location_id']);
				$selected_location	 = $locations_info['location'];
			}

			$location_list = array();
			foreach ($locations as $_location => $descr)
			{
				$location_list[] = array
					(
					'id'		 => $_location,
					'name'		 => "{$_location} [{$descr}]",
					'selected'	 => $_location == $selected_location
				);
			}

			$responsibility_module = isset($values['module']) && $values['module'] ? $values['module'] : array();

			foreach ($responsibility_module as &$module)
			{
				$_location_info		 = $GLOBALS['phpgw']->locations->get_name($module['location_id']);
				$module['appname']	 = $_location_info['appname'];
				$module['location']	 = $_location_info['location'];
				$category			 = $this->cats->return_single($module['cat_id']);
				$module['category']	 = $category[0]['name'];

				if ($this->acl->check('admin', PHPGW_ACL_EDIT, $module['appname']))
				{
					$_checked				 = $module['active'] ? 'checked = "checked"' : '';
					$module['active']		 = "<input type='checkbox' name='values[set_active][]' {$_checked} value='{$module['location_id']}_{$module['cat_id']}' title='" . lang('Check to set active') . "'>";
					$module['delete_module'] = "<input type='checkbox' name='values[delete_module][]' value='{$module['location_id']}_{$module['cat_id']}' title='" . lang('Check to delete') . "'>";
				}
			}


			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => array(
					array('key' => 'appname', 'label' => lang('appname'), 'sortable' => true, 'resizeable' => true),
					array('key'		 => 'location', 'label'		 => lang('location'), 'sortable'	 => true,
						'resizeable' => true),
					array('key'		 => 'category', 'label'		 => lang('category'), 'sortable'	 => true,
						'resizeable' => true),
					array('key' => 'active', 'label' => lang('active'), 'sortable' => true, 'resizeable' => true),
					array('key'		 => 'delete_module', 'label'		 => lang('delete'), 'sortable'	 => false,
						'resizeable' => true, 'formatter'	 => 'JqueryPortico.FormatterCenter')),
				'data'		 => json_encode($responsibility_module),
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);


//-----------------------------------------------

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
				'datatable_def'	 => $datatable_def,
				'msgbox_data'	 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'	 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'value_appname'	 => $this->appname,
				'value_location' => $location,
				'value_id'		 => $id,
				'value_name'	 => $values['name'],
				'value_descr'	 => $values['descr'],
				'value_access'	 => $values['access'],
				'apps_list'		 => array('options' => execMethod('property.bojasper.get_apps', $this->appname)),
				'location_list'	 => array('options' => $location_list),
				'td_count'		 => '""',
				'lang_category'	 => lang('category'),
				'lang_no_cat'	 => lang('no category'),
				'cat_select'	 => $this->cats->formatted_xslt_list(array
					(
					'select_name'	 => 'values[cat_id]',
					'selected'		 => isset($values['cat_id']) ? $values['cat_id'] : ''
				)),
				'tabs'			 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'		 => phpgwapi_jquery::formvalidator_generate(array('location', 'date',
					'security', 'file'))
			);

			$appname = 'Responsible';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::$function_msg::" . lang($this->appname);
			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible', 'datatable_inline'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit' => $data));
		}

		function edit_role()
		{
			if (!$this->acl_add && !$this->acl_edit)
			{
				phpgw::no_access();
			}

			$id			 = phpgw::get_var('id', 'int');
			$location	 = phpgw::get_var('location', 'string');
			$values		 = phpgw::get_var('values');

			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				if ($GLOBALS['phpgw']->session->is_repost())
				{
					//				$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
				}

				if (!isset($values['location']) || !$values['location'])
				{
					//				$receipt['error'][]=array('msg'=>lang('Please select a location!'));
				}

				if (!isset($values['name']) || !$values['name'])
				{
					$receipt['error'][] = array('msg' => lang('Please enter a name!'));
				}

				if ($id)
				{
					$values['id'] = $id;
				}
				else
				{
					$id = $values['id'];
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->save_role($values);
					$id		 = $receipt['id'];

					if (isset($values['save']) && $values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_receipt', $receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uigeneric.index',
							'type'		 => 'responsibility_role', 'appname'	 => $this->appname));
					}
				}
			}

			if (isset($values['cancel']) && $values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uigeneric.index',
					'type'		 => 'responsibility_role', 'appname'	 => $this->appname));
			}

			if ($id)
			{
				$values			 = $this->bo->read_single_role($id);
				$function_msg	 = lang('edit role');
			}
			else
			{
				$function_msg = lang('add role');
			}


			$link_data = array
				(
				'menuaction' => 'property.uiresponsible.edit_role',
				'id'		 => $id,
				'app'		 => $this->appname
			);

			$location_types = execMethod('property.soadmin_location.get_location_type');

			$levels		 = isset($values['location_level']) && $values['location_level'] ? $values['location_level'] : array();
			$level_list	 = array();
			foreach ($location_types as $location_type)
			{
				$level_list[] = array
					(
					'id'		 => $location_type['id'],
					'name'		 => $location_type['name'],
					'selected'	 => in_array($location_type['id'], $levels)
				);
			}
			//-----------------------------------------------

			$msgbox_data	 = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');

			$data = array
				(
				'msgbox_data'			 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'value_appname'			 => $this->appname,
				'value_location'		 => $location,
				'value_id'				 => $id,
				'value_name'			 => $values['name'],
				'value_remark'			 => $values['remark'],
				'value_access'			 => $values['access'],
				'responsibility_list'	 => array('options' => execMethod('property.boresponsible.get_responsibilities', array(
						'appname'	 => $this->appname, 'selected'	 => $values['responsibility_id']))),
				'level_list'			 => array('checkbox' => $level_list),
				'tabs'					 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
			);

			$appname = 'Responsible';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::$function_msg::" . lang($this->appname);
			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_role' => $data));
		}

		/**
		 * List of contacts given responsibilities within locations
		 *
		 * @return void
		 */
		public function contact()
		{
			if (!$this->acl_read)
			{
				$this->no_access();
				return;
			}

			$type_id = phpgw::get_var('type_id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible', 'nextmatchs', 'search_field'));

			$responsible_info = $this->bo->read_contact($type_id);

			$content = array();
			foreach ($responsible_info as $entry)
			{
				$link_edit			 = '';
				$lang_edit_demo_text = '';
				$text_edit			 = '';
				if ($this->acl_edit)
				{
					$link_edit		 = $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uiresponsible.edit_contact',
						'id'		 => $entry['id'],
						'appname'	 => $this->appname,
						'location'	 => str_replace($this->appname, '', $entry['app_name']),
						'type_id'	 => $type_id
					));
					$lang_edit_text	 = lang('edit type');
					$text_edit		 = lang('edit');
				}

				$link_delete			 = '';
				$text_delete			 = '';
				$lang_delete_demo_text	 = '';
				/* 	if ($this->acl_delete)
				  {
				  $link_delete			= $GLOBALS['phpgw']->link('/index.php', array
				  (
				  'menuaction'=> 'property.uiresponsible.delete_contact',
				  'id'=> $entry['id']
				  ));
				  $text_delete			= lang('delete');
				  $lang_delete_text		= lang('delete type');
				  }
				 */

				$content[] = array
					(
					'location_code'		 => $entry['location_code'],
					'item'				 => $entry['item'],
					'active_from'		 => $entry['active_from'],
					'active_to'			 => $entry['active_to'],
					'created_by'		 => $entry['created_by'],
					'created_on'		 => $entry['created_on'],
					'contact_name'		 => $entry['contact_name'],
					'remark'			 => $entry['remark'],
					'link_edit'			 => $link_edit,
					'text_edit'			 => $text_edit,
					'lang_edit_text'	 => $lang_edit_text,
					'link_delete'		 => $link_delete,
					'text_delete'		 => $text_delete,
					'lang_delete_text'	 => $lang_delete_text
				);
			}

			$table_header[] = array
				(
				'sort_location'		 => $this->nextmatchs->show_sort_order(array
					(
					'sort'	 => $this->sort,
					'var'	 => 'location_code',
					'order'	 => $this->order,
					'extra'	 => array
						(
						'menuaction' => 'property.uiresponsible.contact',
						'allrows'	 => $this->allrows,
						'appname'	 => $this->appname,
						'location'	 => $this->location,
						'type_id'	 => $type_id
					)
				)),
				'sort_active_from'	 => $this->nextmatchs->show_sort_order(array
					(
					'sort'	 => $this->sort,
					'var'	 => 'active_from',
					'order'	 => $this->order,
					'extra'	 => array
						(
						'menuaction' => 'property.uiresponsible.contact',
						'allrows'	 => $this->allrows,
						'appname'	 => $this->appname,
						'location'	 => $this->location,
						'type_id'	 => $type_id
					)
				)),
				'sort_active_to'	 => $this->nextmatchs->show_sort_order(array
					(
					'sort'	 => $this->sort,
					'var'	 => 'active_to',
					'order'	 => $this->order,
					'extra'	 => array
						(
						'menuaction' => 'property.uiresponsible.contact',
						'allrows'	 => $this->allrows,
						'appname'	 => $this->appname,
						'location'	 => $this->location,
						'type_id'	 => $type_id
					)
				)),
				'lang_contact'		 => lang('contact'),
				'lang_location'		 => lang('location'),
				'lang_item'			 => lang('item'),
				'lang_active_from'	 => lang('active from'),
				'lang_active_to'	 => lang('active to'),
				'lang_created_on'	 => lang('created'),
				'lang_created_by'	 => lang('supervisor'),
				'lang_remark'		 => lang('remark'),
				'lang_edit'			 => $this->acl_edit ? lang('edit') : '',
				//		'lang_delete'		=> $this->acl_delete ? lang('delete') : '',
			);

			if (!$this->allrows)
			{
				$record_limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit = $this->bo->total_records;
			}

			$link_data = array
				(
				'menuaction' => 'property.uiresponsible.contact',
				'sort'		 => $this->sort,
				'order'		 => $this->order,
				'query'		 => $this->query,
				'appname'	 => $this->appname,
				'location'	 => $this->location,
				'type_id'	 => $type_id
			);

			$link_add_action = array
				(
				'menuaction' => 'property.uiresponsible.edit_contact',
				'appname'	 => $this->appname,
				'location'	 => $this->location,
				'type_id'	 => $type_id
			);

			$table_add[] = array
				(
				'lang_add'				 => lang('add'),
				'lang_add_statustext'	 => lang('add contact'),
				'add_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_add_action),
				'lang_cancel'			 => lang('cancel'),
				'lang_cancel_statustext' => lang('back to list type'),
				'cancel_action'			 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiresponsible.index',
					'appname'	 => $this->appname
					)
				)
			);

			$receipt	 = $GLOBALS['phpgw']->session->appsession('session_data', 'responsible_contact_receipt');
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_contact_receipt', '');

			$type_info	 = $this->bo->read_single_type($type_id);
			$category	 = $this->cats->return_single($type_info['cat_id']);
			$data		 = array
				(
				'msgbox_data'					 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'allow_allrows'					 => true,
				'allrows'						 => $this->allrows,
				'start_record'					 => $this->start,
				'record_limit'					 => $record_limit,
				'num_records'					 => $responsible_info ? count($responsible_info) : 0,
				'all_records'					 => $this->bo->total_records,
				'select_action'					 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'link_url'						 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'img_path'						 => $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
				'lang_searchfield_statustext'	 => lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	 => lang('Submit the search string'),
				'query'							 => $this->query,
				'lang_search'					 => lang('search'),
				'table_header_contact'			 => $table_header,
				'table_add'						 => $table_add,
				'values_contact'				 => $content,
				'lang_no_location'				 => lang('No location'),
				'lang_location_statustext'		 => lang('Select submodule'),
				'select_name_location'			 => 'location',
				'location_name'					 => "property{$this->location}", //FIXME once interlink is settled
				'lang_no_cat'					 => lang('no category'),
				'type_name'						 => $type_info['name'],
				'category_name'					 => $category[0]['name']
			);

			$function_msg = lang('list available responsible contacts');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('list_contact' => $data));
			$this->_save_sessiondata();
		}

		/**
		 * Add or Edit available contact related to responsible types and (physical) locations
		 *
		 * @return void
		 */
		public function edit_contact()
		{
			if (!$this->acl_add)
			{
				$this->no_access();
				return;
			}

			$id					 = phpgw::get_var('id', 'int');
			$type_id			 = phpgw::get_var('type_id', 'int');
			$values				 = phpgw::get_var('values', 'string', 'POST');
			$contact_id			 = phpgw::get_var('contact', 'int');
			$contact_name		 = phpgw::get_var('contact_name', 'string');
			$responsibility_id	 = phpgw::get_var('responsibility_id', 'int');
			$responsibility_name = phpgw::get_var('responsibility_name', 'string');
			$bolocation			 = CreateObject('property.bolocation');
			$bocommon			 = CreateObject('property.bocommon');

			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible'));

			if (isset($values) && is_array($values))
			{
				if (!$this->acl_edit)
				{
					$this->no_access();
					return;
				}

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{
					$insert_record			 = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
					$insert_record_entity	 = $GLOBALS['phpgw']->session->appsession('insert_record_entity', 'property');

					if (isset($insert_record_entity) && is_array($insert_record_entity))
					{
						foreach ($insert_record_entity as $insert_record_entry)
						{
							$insert_record['extra'][$insert_record_entry] = $insert_record_entry;
						}
					}

					$values = $bocommon->collect_locationdata($values, $insert_record);

					if ($id)
					{
						$values['id'] = $id;
					}
					if ($contact_id)
					{
						$values['contact_id'] = $contact_id;
					}

					if ($contact_name)
					{
						$values['contact_name'] = $contact_name;
					}

					if ($responsibility_id)
					{
						$values['responsibility_id'] = $responsibility_id;
					}

					if ($contact_name)
					{
						$values['responsibility_name'] = $responsibility_name;
					}

					if (!isset($values['responsibility_id']))
					{
						$receipt['error'][] = array('msg' => lang('Please select a responsibility!'));
					}

					if (!isset($values['contact_id']))
					{
						$receipt['error'][] = array('msg' => lang('Please select a contact!'));
					}

					if (!isset($values['location']['loc1']))
					{
						//			$receipt['error'][]=array('msg'=>lang('Please select a location!'));
					}

					if ($GLOBALS['phpgw']->session->is_repost())
					{
						$receipt['error'][] = array('msg' => lang('Hmm... looks like a repost!'));
					}

					if (!isset($receipt['error']) || !$receipt['error'])
					{
						$receipt = $this->bo->save_contact($values);
						$id		 = $receipt['id'];

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_contact_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array
								(
								'menuaction' => 'property.uiresponsible.contact',
								'appname'	 => $this->appname,
								'location'	 => $this->location,
								'type_id'	 => $type_id
							));
						}
						else if (isset($values['apply']) && $values['apply'])
						{
							$GLOBALS['phpgw']->redirect_link('/index.php', array
								(
								'menuaction' => 'property.uiresponsible.edit_contact',
								'appname'	 => $this->appname,
								'location'	 => $this->location,
								'type_id'	 => $type_id,
								'id'		 => $id
							));
						}
					}
					else
					{
						if (isset($values['location']) && $values['location'])
						{
							$location_code			 = implode("-", $values['location']);
							$values['location_data'] = $bolocation->read_single($location_code, isset($values['extra']) ? $values['extra'] : false);
						}
						if (isset($values['extra']['p_num']) && $values['extra']['p_num'])
						{
							$values['p'][$values['extra']['p_entity_id']]['p_num']		 = $values['extra']['p_num'];
							$values['p'][$values['extra']['p_entity_id']]['p_entity_id'] = $values['extra']['p_entity_id'];
							$values['p'][$values['extra']['p_entity_id']]['p_cat_id']	 = $values['extra']['p_cat_id'];
							$values['p'][$values['extra']['p_entity_id']]['p_cat_name']	 = phpgw::get_var('entity_cat_name_' . $values['extra']['p_entity_id'], 'string', 'POST');
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array
						(
						'menuaction' => 'property.uiresponsible.contact',
						'appname'	 => $this->appname,
						'location'	 => $this->location,
						'type_id'	 => $type_id
					));
				}
			}


			if ($id)
			{
				$function_msg	 = lang('edit responsible type');
				$values			 = $this->bo->read_single_contact($id);
			}
			else
			{
				$function_msg = lang('add responsible type');
			}

			$location_data = $bolocation->initiate_ui_location(array
				(
				'values'		 => $values['location_data'],
				'type_id'		 => -1, // calculated from location_types
				'no_link'		 => false, // disable lookup links for location type less than type_id
				'tenant'		 => false,
				'lookup_type'	 => 'form',
				'lookup_entity'	 => $bocommon->get_lookup_entity('project'),
				'entity_data'	 => isset($values['p']) ? $values['p'] : ''
				)
			);

			$link_data = array
				(
				'menuaction' => 'property.uiresponsible.edit_contact',
				'id'		 => $id,
				'appname'	 => $this->appname,
				'location'	 => $this->location,
				'type_id'	 => $type_id
			);

			$msgbox_data = (isset($receipt) ? $GLOBALS['phpgw']->common->msgbox_data($receipt) : '');

			$lookup_link_contact		 = "menuaction:'property.uilookup.addressbook', column:'contact', clear_state:1";
			$lookup_link_responsibility	 = "menuaction:'property.uiresponsible.index', location:'{$this->location}', lookup:1";

			$lookup_function = "\n"
				. '<script type="text/javascript">' . "\n"
				. '//<[CDATA[' . "\n"
				. 'function lookup_contact()' . "\r\n"
				. "{\r\n"
				. ' var oArgs = {' . $lookup_link_contact . "};\n"
				. " var strURL = phpGWLink('index.php', oArgs);\n"
				. ' Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");' . "\r\n"
				. '}' . "\r\n"
				//				. 'function lookup_responsibility()' ."\r\n"
				//				. "{\r\n"
				//				. ' var oArgs = {' . $lookup_link_responsibility . "};\n"
				//				. " var strURL = phpGWLink('index.php', oArgs);\n"
				//				. ' Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");' . "\r\n"
				//				. '}'."\r\n"
				. '//]]' . "\n"
				. "</script>\n";

			if (!isset($GLOBALS['phpgw_info']['flags']['java_script']))
			{
				$GLOBALS['phpgw_info']['flags']['java_script'] = '';
			}

			$GLOBALS['phpgw_info']['flags']['java_script'] .= $lookup_function;

			$GLOBALS['phpgw']->jqcal->add_listener('values_active_from');
			$GLOBALS['phpgw']->jqcal->add_listener('values_active_to');

			$type = $this->bo->read_single_type($type_id);

			$data = array
				(
				'value_entry_date'					 => isset($values['entry_date']) ? $values['entry_date'] : '',
				'value_name'						 => isset($values['name']) ? $values['name'] : '',
				'value_remark'						 => isset($values['remark']) ? $values['remark'] : '',
				'lang_entry_date'					 => lang('Entry date'),
				'lang_remark'						 => lang('remark'),
				'lang_responsibility'				 => lang('responsibility'),
				'lang_responsibility_status_text'	 => lang('responsibility'),
				'value_responsibility_id'			 => $type_id,
				'value_responsibility_name'			 => $type['name'],
				'lang_contact'						 => lang('contact'),
				'lang_contact_status_text'			 => lang('click to select contact'),
				'value_contact_id'					 => isset($values['contact_id']) ? $values['contact_id'] : '',
				'value_contact_name'				 => isset($values['contact_name']) ? $values['contact_name'] : '',
				'msgbox_data'						 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_id'							 => lang('ID'),
				'lang_save'							 => lang('save'),
				'lang_cancel'						 => lang('cancel'),
				'value_id'							 => $id,
				'lang_cancel_status_text'			 => lang('Back to the list'),
				'lang_save_status_text'				 => lang('Save the responsible type'),
				'lang_apply'						 => lang('apply'),
				'lang_apply_status_text'			 => lang('Apply the values'),
				'lang_location'						 => lang('location'),
				'value_location_name'				 => "property{$this->location}", //FIXME once interlink is settled
				'location_data'						 => $location_data,
				'lang_active_from'					 => lang('active from'),
				'lang_active_to'					 => lang('active to'),
				'value_active_from'					 => isset($values['active_from']) ? $values['active_from'] : '',
				'value_active_to'					 => isset($values['active_to']) ? $values['active_to'] : '',
				'lang_active_from_statustext'		 => lang('Select the start date for this responsibility'),
				'lang_active_to_statustext'			 => lang('Select the closing date for this responsibility'),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . "::{$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_contact' => $data));
		}

		/**
		 * Display an error in case of missing rights
		 *
		 * @return void
		 */
		public function no_access()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('no_access'));

			$receipt['error'][] = array('msg' => lang('NO ACCESS'));

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data)
			);

			$function_msg = lang('No access');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('no_access' => $data));
		}

		/**
		 * Delete a responsibility type
		 *
		 * @return void
		 */
		public function delete_type()
		{
			if (!$this->acl_delete)
			{
				return 'No access';
			}

			$id = phpgw::get_var('id', 'int');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete_type($id);
				return lang('id %1 has been deleted', $id);
			}
		}
	}