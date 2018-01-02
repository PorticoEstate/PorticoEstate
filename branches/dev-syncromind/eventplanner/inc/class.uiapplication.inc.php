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
	 * @subpackage application
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('eventplanner', 'application', 'inc/model/');

	class eventplanner_uiapplication extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'get_list'=> true,
			'view' => true,
			'edit' => true,
			'save' => true,
			'handle_multi_upload_file' => true,
			'build_multi_upload_file' => true,
			'get_files'				=> true,
			'view_file'				=> true
		);

		protected
			$fields,
			$permissions,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('application');
			$this->bo = createObject('eventplanner.boapplication');
			$this->cats = & $this->bo->cats;
			$this->fields = eventplanner_application::get_fields();
			$this->permissions = eventplanner_application::get_instance()->get_permission_array();
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::application");
		}

		private function get_status_options( $selected = 0 )
		{
			$status_options = array();
			$status_list = eventplanner_application::get_status_list();

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

		private function _get_filters()
		{
			$combos = array();
			$combos[] = array(
				'type' => 'autocomplete',
				'name' => 'vendor',
				'app' => $this->currentapp,
				'ui' => 'vendor',
				'function' => 'get_list',
				'label_attr' => 'name',
				'text' => lang('vendor') . ':',
				'requestGenerator' => 'requestWithVendorFilter'
			);

			$status_options = $this->get_status_options();
			array_unshift($status_options, array('id' => '','name' => lang('all')));

			$combos[] = array(
				'type' => 'filter',
				'name' => 'filter_status',
				'extra' => '',
				'text' => lang('status'),
				'list' => $status_options
			);

			$categories = $this->cats->formatted_xslt_list(array('format' => 'filter',
					'selected' => $this->cat_id, 'globals' => true, 'use_acl' => $this->_category_acl));
			$default_value = array('cat_id' => '', 'name' => lang('no category'));
			array_unshift($categories['cat_list'], $default_value);

			$_categories = array();
			foreach ($categories['cat_list'] as $_category)
			{
				$_categories[] = array('id' => $_category['cat_id'], 'name' => $_category['name']);
			}

			$combos[] = array('type' => 'filter',
				'name' => 'filter_category_id',
				'extra' => '',
				'text' => lang('category'),
				'list' => $_categories
			);

			return $combos;

		}
		public function index()
		{
			self::set_active_menu("{$this->currentapp}::vendor::application");
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{

				$message = '';
				if($this->currentapp == 'eventplannerfrontend')
				{
					$GLOBALS['phpgw']->redirect_link('login.php', array('after' => 'eventplannerfrontend.uiapplication.index'));
					$message = lang('you need to log in to access this page.');
				}
				phpgw::no_access(false, $message);
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_jquery::load_widget('autocomplete');

			if($this->currentapp == 'eventplanner')
			{
				$function_msg = lang('application');
			}
			else
			{
				$function_msg = lang('my applications');
			}


			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => "{$this->currentapp}.uiapplication.index",
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'sorted_by'	=> array('key' => $this->currentapp == 'eventplanner' ? 6 : 3, 'dir' => 'asc'),
					'new_item' => self::link(array('menuaction' => "{$this->currentapp}.uiapplication.add")),
					'editor_action' => '',
					'field' => parent::_get_fields()
				)
			);

			if($this->currentapp == 'eventplanner')
			{
				$filters = $this->_get_filters();

				foreach ($filters as $filter)
				{
					array_unshift($data['form']['toolbar']['item'], $filter);
				}
			}

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
					'menuaction' => "{$this->currentapp}.uiapplication.edit"
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript($this->currentapp, 'portico', 'application.index.js');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function add()
		{
			self::set_active_menu("{$this->currentapp}::vendor::new_application");
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				if($this->currentapp == 'eventplannerfrontend')
				{
					$GLOBALS['phpgw']->redirect_link('login.php', array('after' => 'eventplannerfrontend.uiapplication.add'));
				}
			}

			$this->edit();
		}

		public function edit( $values = array(), $mode = 'edit' )
		{
			$active_tab = !empty($values['active_tab']) ? $values['active_tab'] : phpgw::get_var('active_tab', 'string', 'REQUEST', 'first_tab');
		//	$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			if (!empty($values['object']))
			{
				$application = $values['object'];
			}
			else
			{
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$application = $this->bo->read_single($id);
			}


			$vendor_id = phpgw::get_var('vendor_id', 'int');

			if($vendor_id && !$application->vendor_id)
			{
				$vendor = createObject('eventplanner.bovendor')->read_single($vendor_id);
				$application->vendor_id = $vendor_id;
				$application->vendor_name = $vendor->name;
				$application->contact_name = $vendor->contact_name;
				$application->contact_email = $vendor->contact_email;
				$application->contact_phone = $vendor->contact_phone;
			}

			$config = CreateObject('phpgwapi.config', 'eventplanner')->read();
			$default_category = !empty($config['default_application_category']) ? $config['default_application_category'] : null;

			$config_frontend = CreateObject('phpgwapi.config', 'eventplannerfrontend')->read();

			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('application'),
				'link' => '#first_tab',
				'function' => "set_tab('first_tab')"
			);
			$tabs['demands'] = array(
				'label' => lang('demands'),
				'link' => '#demands',
				'function' => "set_tab('demands')"
			);
			$tabs['files'] = array(
				'label' => lang('files'),
				'link' => '#files',
				'function' => "set_tab('files')",
				'disable'	=> $id ? false : true
			);
			$tabs['calendar'] = array(
				'label' => lang('calendar'),
				'link' => '#calendar',
				'function' => "set_tab('calendar')",
				'disable'	=> $id ? false : true
			);

			$bocommon = CreateObject('property.bocommon');

			$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, '.application', 'eventplanner');
			$case_officer_options[] = array('id' => '', 'name' => lang('select'), 'selected' => 0);
			foreach ($accounts as $account)
			{
				$case_officer_options[] = array(
					'id' => $account['account_id'],
					'name' => $GLOBALS['phpgw']->accounts->get($account['account_id'])->__toString(),
					'selected' => ($account['account_id'] == $application->case_officer_id) ? 1 : 0
				);
			}
			$comments = (array)$application->comments;
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

			$dates_def = array(
				array('key' => 'id', 'label' => lang('id'), 'sortable' => false, 'resizeable' => true,'formatter' => 'JqueryPortico.formatLink'),
				array('key' => 'from_', 'label' => lang('From'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'to_', 'label' => lang('To'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'status', 'label' => lang('status'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'customer_name', 'label' => lang('who'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'location', 'label' => lang('location'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'comment', 'label' => lang('Note'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'application_id', 'hidden' => true),
			);

			$tabletools = array(
				array(
					'my_name' => 'add',
					'text' => lang('add'),
					'type' => 'custom',
					'className' => 'add',
					'custom_code' => "
								add_schedule();"
				),
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none'),
				array(
					'my_name' => 'enable',
					'text' => lang('enable'),
					'type' => 'custom',
					'custom_code' => "
								onActionsClick('enable');"
				),
				array(
					'my_name' => 'disable',
					'text' => lang('disable'),
					'type' => 'custom',
					'custom_code' => "
								onActionsClick('disable');"
				),
				array(
					'my_name' => 'delete',
					'text' => lang('delete'),
					'type' => 'custom',
					'custom_code' => "
								onActionsClick('delete');"
				),
				array(
					'my_name' => 'edit',
					'text' => lang('edit'),
					'type' => 'custom',
					'custom_code' => "
								onActionsClick('edit');"
				),
				array(
					'my_name' => 'disconnect',
					'text' => lang('disconnect'),
					'type' => 'custom',
					'custom_code' => "
								onActionsClick('disconnect');"
				)
			);

		$list_public_types = array(
			array(
				'id' => 0,
				'name' => lang('application public type public'),
				'selected' => $application->non_public == 0 ? 1 : 0
				),
			array('id' => 1,
				'name' => lang('application public type non public'),
				'selected' => $application->non_public == 1 ? 1 : 0
				),

		);

			$datatable_def[] = array(
				'container' => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uicalendar.query_relaxed",
					'filter_application_id' => $id,
					'filter_active'	=> 1,
					'redirect'	=> 'booking',
					'phpgw_return_as' => 'json'))),
				'tabletools' => $tabletools,
				'ColumnDefs' => $dates_def,
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('order' => json_encode(array(1,'asc'))),
				)
			);

			$config_calendar = array();
			$active_year = !empty($config['active_year']) ? $config['active_year'] : null;
			if($active_year)
			{
				$config_calendar = array(
					'min_date' => "{$active_year}, 1 -1, 1",
					'max_date' => "{$active_year}, 12 -1, 31"
				);
			}

			$GLOBALS['phpgw']->jqcal->add_listener('date_start', 'date', '', $config_calendar);
			$GLOBALS['phpgw']->jqcal->add_listener('date_end', 'date', '', $config_calendar);
			$GLOBALS['phpgw']->jqcal2->add_listener('from_', 'datetime', $application->date_start, array(
					'min_date' => date('Y/m/d', $application->date_start),
					'max_date' => date('Y/m/d', $application->date_end)
				)
			);

			$application_type_list = execMethod('eventplanner.bogeneric.get_list', array('type' => 'application_type'));
			$types = (array)$application->types;
			if($types)
			{
				foreach ($application_type_list as &$application_type)
				{
					foreach ($types as $type)
					{
						if((!empty($type['type_id']) && $type['type_id'] == $application_type['id']) || ($type == $application_type['id']))
						{
							$application_type['selected'] = 1;
							break;
						}
					}
				}
			}
			$wardrobe_list = array();
			$wardrobe_list[] = array('id' => 0, 'name' => lang('no'));
			$wardrobe_list[] = array('id' => 1, 'name' => lang('yes'));

			foreach ($wardrobe_list as &$wardrobe)
			{
				$wardrobe['selected'] = $wardrobe['id'] == $application->wardrobe ? 1: 0;
			}

			$file_def = array
			(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,'resizeable' => true),
				array('key' => 'picture', 'label' => '', 'sortable' => false,'resizeable' => false, 'formatter' => 'JqueryPortico.showPicture')
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uiapplication.get_files",
					'id' => $id,
					'section' => 'cv',
					'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $file_def,
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_3',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uiapplication.get_files",
					'id' => $id,
					'section' => 'documents',
					'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $file_def,
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);


			$vendor_list = array();

			if($this->currentapp == 'eventplannerfrontend')
			{
				$vendors = createObject('eventplanner.bovendor')->read(array());
				foreach($vendors['results'] as $vendor)
				{
					$vendor_list[] = array(
						'id' => $vendor['id'],
						'name' => $vendor['name'],
						'selected' => $vendor['id'] ==  $application->vendor_id ? 1 : 0,
					);
				}

				array_unshift($vendor_list, array('id' => '', 'name' => lang('select')));
			}

			$data = array(
				'datatable_def' => $datatable_def,
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uiapplication.save")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uiapplication.index",)),
				'application' => $application,
				'vendor_list'	=> array('options' => $vendor_list),
				'new_vendor_url' => self::link(array('menuaction' => "{$this->currentapp}.uivendor.add")),
				'list_case_officer' => array('options' => $case_officer_options),
				'list_public_types'	=> array('options' => $list_public_types),
				'cat_select' => $this->cats->formatted_xslt_list(array(
					'select_name' => 'category_id',
					'selected'	=> $application->category_id ? $application->category_id : $default_category,
					'use_acl' => $this->_category_acl,
					'required' => true,
					'class'=>'pure-input-1-2')),
				'status_list' => array('options' => $this->get_status_options($application->status)),
				'application_type_list' => $application_type_list,
				'wardrobe_list'	=>  array('options' => $wardrobe_list),
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab,
				'multi_upload_parans' => "{menuaction:'{$this->currentapp}.uiapplication.build_multi_upload_file', id:'{$id}'}",
				'multiple_uploader' => true,
				'application_condition' => !empty($config_frontend['application_condition']) ? $config_frontend['application_condition'] : null,
				'user_agreement_text_1'=> !empty($config_frontend['user_agreement_text_1']) ? $config_frontend['user_agreement_text_1'] : null,
				'user_agreement_text_2'=> !empty($config_frontend['user_agreement_text_2']) ? $config_frontend['user_agreement_text_2'] : null,
			);
			phpgwapi_jquery::formvalidator_generate(array('date', 'security', 'file'));
			phpgwapi_jquery::load_widget('autocomplete');
			self::rich_text_editor('summary');
			self::add_javascript($this->currentapp, 'portico', 'application.edit.js');
			self::render_template_xsl(array('application', 'datatable_inline', 'files'), array($mode => $data));
		}

		function get_files()
		{
			$id = phpgw::get_var('id', 'int');
			$section = phpgw::get_var('section', 'string', 'REQUEST', 'documents');

			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				return array();
			}

			$link_file_data = array
				(
				'menuaction' => "{$this->currentapp}.uiapplication.view_file",
			);


			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$files = $vfs->ls(array(
				'string' => "/eventplanner/application/{$id}/$section",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$img_types = array(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$content_files = array();

			$z = 0;
			foreach ($files as $_entry)
			{

				$content_files[] = array(
					'file_name' => '<a href="' . $link_view_file . '&amp;file_id=' . $_entry['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $_entry['name'] . '</a>',
					'delete_file' => '<input type="checkbox" name="values[file_action][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to delete file') . '">',
				);
				if ( in_array($_entry['mime_type'], $img_types))
				{
					$content_files[$z]['file_name'] = $_entry['name'];
					$content_files[$z]['img_id'] = $_entry['file_id'];
					$content_files[$z]['img_url'] = self::link(array(
							'menuaction' => "{$this->currentapp}.uiapplication.view_file",
							'file_id'	=>  $_entry['file_id'],
							'file' => $_entry['directory'] . '/' . urlencode($_entry['name'])
					));
					$content_files[$z]['thumbnail_flag'] = 'thumb=1';
				}
				$z ++;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{

				$total_records = count($content_files);

				return array
					(
					'data' => $content_files,
					'draw' => phpgw::get_var('draw', 'int'),
					'recordsTotal' => $total_records,
					'recordsFiltered' => $total_records
				);
			}
			return $content_files;
		}

		public function handle_multi_upload_file()
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			$section = phpgw::get_var('section', 'string', 'REQUEST', 'documents');
			$id = phpgw::get_var('id', 'int');

			phpgw::import_class('property.multiuploader');

			$options['fakebase'] = "/eventplanner";
			$options['base_dir'] = "application/{$id}/{$section}";
			$options['upload_dir'] = $GLOBALS['phpgw_info']['server']['files_dir'].'/eventplanner/'.$options['base_dir'].'/';
			$options['script_url'] = html_entity_decode(self::link(array('menuaction' => "{$this->currentapp}.uiapplication.handle_multi_upload_file", 'id' => $id, 'section' => $section)));
			$upload_handler = new property_multiuploader($options, false);

			switch ($_SERVER['REQUEST_METHOD']) {
				case 'OPTIONS':
				case 'HEAD':
					$upload_handler->head();
					break;
				case 'GET':
					$upload_handler->get();
					break;
				case 'PATCH':
				case 'PUT':
				case 'POST':
					$upload_handler->add_file();
					break;
				case 'DELETE':
					$upload_handler->delete_file();
					break;
				default:
					$upload_handler->header('HTTP/1.1 405 Method Not Allowed');
			}

			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		public function build_multi_upload_file()
		{
			phpgwapi_jquery::init_multi_upload_file();
			$id = phpgw::get_var('id', 'int');
			$section = phpgw::get_var('section', 'string', 'REQUEST', 'documents');

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$multi_upload_action = self::link(array('menuaction' => "{$this->currentapp}.uiapplication.handle_multi_upload_file", 'id' => $id, 'section' => $section));

			$data = array
				(
				'multi_upload_action' => $multi_upload_action
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('files', 'multi_upload_file'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('multi_upload' => $data));
		}

		public function save()
		{
			parent::save();
		}

	}