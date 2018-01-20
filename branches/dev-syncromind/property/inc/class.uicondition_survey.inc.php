<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @package property
	 * @subpackage logistic
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uicondition_survey extends phpgwapi_uicommon_jquery
	{

		private $bo;
		private $receipt = array();
		public $public_functions = array
			(
			'query' => true,
			'index' => true,
			'view' => true,
			'add' => true,
			'edit' => true,
			'save' => true,
			'delete' => true,
			'delete_imported_records' => true,
			'get_vendors' => true,
			'get_users' => true,
			'edit_survey_title' => true,
			'get_files' => true,
			'get_request' => true,
			'get_summation' => true,
			'view_file' => true,
			'import' => true,
			'export'	=> true,
			'download' => true,
			'summation' => true,
			'handle_multi_upload_file' => true,
			'build_multi_upload_file' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->bo = CreateObject('property.bocondition_survey');
			$this->bocommon = & $this->bo->bocommon;
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = $this->bo->acl_location;
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage = $this->acl->check($this->acl_location, 16, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::project::condition_survey";
			//			$GLOBALS['phpgw']->css->add_external_file('logistic/templates/base/css/base.css');
		}

		public function download()
		{
			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$values = $this->query();

			$descr = array();
			$columns = array();
			$columns[] = 'id';
			$columns[] = 'title';
			$columns[] = 'descr';
			$columns[] = 'address';
			$columns[] = 'cnt';

			foreach ($columns as $_column)
			{
				$descr[] = lang(str_replace('_', ' ', $_column));
			}

			$this->bocommon->download($values, $columns, $descr);
		}

		public function handle_multi_upload_file()
		{
			$id = phpgw::get_var('id');
			
			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php', 
					array('menuaction' => 'property.uicondition_survey.handle_multi_upload_file', 'id' => $id));
			
			phpgw::import_class('property.multiuploader');
	

			$options['base_dir'] = "condition_survey/{$id}";
			$options['upload_dir'] = $GLOBALS['phpgw_info']['server']['files_dir'].'/property/'.$options['base_dir'].'/';
			$options['script_url'] = html_entity_decode($multi_upload_action);
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
			
			$id = phpgw::get_var('id');
			
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			
			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php', 
					array('menuaction' => 'property.uicondition_survey.handle_multi_upload_file', 'id' => $id));

			$data = array
				(
				'multi_upload_action' => $multi_upload_action					
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('files', 'multi_upload_file'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('multi_upload' => $data));
		}
		
		/**
		 * Prepare UI
		 * @return void
		 */
		public function index()
		{
			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$categories = $this->_get_categories();

			$data = array(
				'datatable_name' => lang('condition survey'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'cat_id',
								'text' => lang('category') . ':',
								'list' => $categories,
							)
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'property.uicondition_survey.index',
						'phpgw_return_as' => 'json')),
					'download' => self::link(array('menuaction' => 'property.uicondition_survey.download',
						'export' => true, 'allrows' => true)),
					'new_item' => self::link(array('menuaction' => 'property.uicondition_survey.add')),
					'allrows' => true,
					'editor_action' => self::link(array('menuaction' => 'property.uicondition_survey.edit_survey_title')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'title',
							'label' => lang('title'),
							'sortable' => true,
							'editor' => true
						),
						/* 						array(
						  'key' => 'descr',
						  'label' => lang('description'),
						  'sortable' => false,
						  ), */
						array(
							'key' => 'address',
							'label' => lang('buildingname'),
							'sortable' => true
						),
						array(
							'key' => 'vendor',
							'label' => lang('vendor'),
							'sortable' => true
						),
						array(
							'key' => 'year',
							'label' => lang('year'),
							'sortable' => true,
							'className' => 'center'
						),
						array(
							'key' => 'multiplier',
							'label' => lang('multiplier'),
							'sortable' => false,
							'className' => 'right'
						),
						array(
							'key' => 'cnt',
							'label' => lang('count'),
							'sortable' => false,
							'className' => 'center'
						),
						array(
							'key' => 'link',
							'label' => 'dummy',
							'sortable' => false,
							'hidden' => true,
						)
					)
				),
			);

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					),
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view_survey',
				'text' => lang('view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uicondition_survey.view'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit_survey',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uicondition_survey.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'import_survey',
				'text' => lang('import'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uicondition_survey.import'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'export_survey',
				'text' => lang('export'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uicondition_survey.export'
				)),
				'parameters' => json_encode($parameters)
			);


			if ($GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_DELETE, 'property'))
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'delete_imported_records',
					'text' => lang('delete imported records'),
					'confirm_msg' => lang('do you really want to delete this entry') . '?',
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uicondition_survey.delete_imported_records'
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if ($GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_DELETE, 'property'))
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'delete_survey',
					'text' => lang('delete'),
					'confirm_msg' => lang('do you really want to delete this entry') . '?',
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uicondition_survey.delete'
					)),
					'parameters' => json_encode($parameters)
				);
			}

			self::render_template_xsl('datatable_jquery', $data);
		}

		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$export = phpgw::get_var('export', 'bool');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'sort' => phpgw::get_var('sort'),
				'dir' => phpgw::get_var('dir'),
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export
			);

			$result_objects = array();
			$result_count = 0;

			$values = $this->bo->read($params);
			if ($export)
			{
				return $values;
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			array_walk($result_data['results'], array($this, '_add_links'), array('menuaction' => 'property.uicondition_survey.view'));

			return $this->jquery_results($result_data);
		}

		public function view()
		{
			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}
			$this->edit(null, $mode = 'view');
		}

		public function add()
		{
			$this->edit();
		}

		/**
		 * Prepare data for view and edit - depending on mode
		 *
		 * @param array  $values  populated object in case of retry
		 * @param string $mode    edit or view
		 * @param int    $id      entity id - no id means 'new'
		 *
		 * @return void
		 */
		public function edit( $values = array(), $mode = 'edit' )
		{
			$id = isset($values['id']) && $values['id'] ? $values['id'] : phpgw::get_var('id', 'int');

			if ($mode == 'view')
			{
				if (!$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if (!$this->acl_add && !$this->acl_edit)
				{
					$this->bocommon->no_access();
					return;
				}
			}

			phpgwapi_cache::session_clear('property.request', 'session_data');

			//phpgwapi_jquery::tabview_setup('survey_edit_tabview');
			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';
			$tabs['documents'] = array('label' => lang('documents'), 'link' => "#documents",
				'disable' => 1);
			$tabs['request'] = array('label' => lang('request'), 'link' => "#request", 'disable' => 1);
			$tabs['summation'] = array('label' => lang('summation'), 'link' => "#summation",
				'disable' => 1);
			$tabs['import'] = array('label' => lang('import'), 'link' => "#import", 'disable' => 1);

			if ($id)
			{
				if ($mode == 'edit')
				{
					$tabs['import']['link'] = '#import';
					$tabs['import']['disable'] = 0;
				}
				$tabs['documents']['link'] = '#documents';
				$tabs['documents']['disable'] = 0;
				$tabs['request']['link'] = '#request';
				$tabs['request']['disable'] = 0;
				$tabs['summation']['link'] = '#summation';
				$tabs['summation']['disable'] = 0;

				if (!$values)
				{
					$values = $this->bo->read_single(array('id' => $id, 'view' => $mode == 'view'));
				}
			}

			if (isset($values['location_code']) && $values['location_code'])
			{
				$values['location_data'] = execMethod('property.solocation.read_single', $values['location_code']);
			}

			$this->config = CreateObject('phpgwapi.config', 'property');
			$this->config->read();
			
			$survey_location_level = empty($this->config->config_data['survey_location_level']) ? 2 : (int)$this->config->config_data['survey_location_level'];

			$categories = $this->_get_categories($values['cat_id']);

			$bolocation = CreateObject('property.bolocation');


			$location_data = $bolocation->initiate_ui_location(array
				(
				'values' => $values['location_data'],
				'type_id' => $survey_location_level,
				'required_level' => 1,
				'no_link' => $_no_link, // disable lookup links for location type less than type_id
				'lookup_type' => $mode == 'edit' ? 'form2' : 'view2',
				'tenant' => false,
				'lookup_entity' => array(),
				'entity_data' => isset($values['p']) ? $values['p'] : ''
			));

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$file_def = array
				(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'delete_file', 'label' => lang('Delete file'), 'sortable' => false,
					'resizeable' => true),
			);


			$datatable_def = array();
			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uicondition_survey.get_files',
						'id' => $id, 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $file_def,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$related_def = array
				(
				array('key' => 'url', 'label' => lang('id'), 'sortable' => true),
				array('key' => 'title', 'label' => lang('title'), 'sortable' => false), //width not working...
				array('key' => 'status', 'label' => lang('status'), 'sortable' => true),
//				array('key' => 'category','label'=>lang('category'),'sortable'=>false,'resizeable'=>true),
				array('key' => 'condition_degree', 'label' => lang('condition degree'), 'sortable' => false),
				array('key' => 'score', 'label' => lang('score'), 'sortable' => true),
				array('key' => 'amount_investment', 'label' => lang('investment'), 'sortable' => true,
					'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'amount_operation', 'label' => lang('operation'), 'sortable' => true,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'amount_potential_grants', 'label' => lang('potential grants'),
					'sortable' => true, 'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterAmount0'),
//				array('key' => 'planned_budget','label'=>lang('planned budget'),'sortable'=>true,'resizeable'=>true,'formatter'=>'JqueryPortico.FormatterAmount0'),
				array('key' => 'recommended_year', 'label' => lang('recommended year'), 'sortable' => true,
					'className' => 'center'),
				array('key' => 'planned_year', 'label' => lang('planned year'), 'sortable' => true,
					'className' => 'center'),
				array('key' => 'related', 'label' => lang('related'), 'sortable' => false),
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uicondition_survey.get_request',
						'id' => $id, 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $related_def,
				'config' => array(
					array('allrows' => true),
				)
			);

			$summation_def = array
				(
				array('key' => 'building_part', 'label' => lang('building part'), 'sortable' => false),
				array('key' => 'category', 'label' => lang('category'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'period_1', 'label' => lang('year') . ':: < 1', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'period_2', 'label' => lang('year') . ':: 1 - 5', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'period_3', 'label' => lang('year') . ':: 6 - 10', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'period_4', 'label' => lang('year') . ':: 11 - 15', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'period_5', 'label' => lang('year') . ':: 16 - 20', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'period_6', 'label' => lang('year') . ':: 21 +', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'sum', 'label' => lang('sum'), 'sortable' => false, 'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0'),
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uicondition_survey.get_summation',
						'id' => $id, 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $summation_def,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'survey' => $values,
				'location_data2' => $location_data,
				'lang_coordinator' => isset($this->config->config_data['lang_request_coordinator']) && $this->config->config_data['lang_request_coordinator'] ? $this->config->config_data['lang_request_coordinator'] : lang('coordinator'),
				'categories' => array('options' => $categories),
				'status_list' => array('options' => execMethod('property.bogeneric.get_list', array(
						'type' => 'condition_survey_status', 'selected' => $values['status_id'], 'add_empty' => true))),
				'editable' => $mode == 'edit',
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'multiple_uploader' => $mode == 'edit' ? true : ''
			);

			//print_r($data['tabs']); die;

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('condition survey');

			if ($mode == 'edit')
			{
				$GLOBALS['phpgw']->jqcal->add_listener('report_date');
				phpgwapi_jquery::load_widget('core');
				self::add_javascript('property', 'portico', 'condition_survey_edit.js');
				phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
					'file'));
				$data['multi_upload_parans'] = "{menuaction:'property.uicondition_survey.build_multi_upload_file', id:'{$id}'}";			
			}

			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('property', 'portico', 'condition_survey.js');

			self::add_javascript('phpgwapi', 'tinybox2', 'packed.js');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');

			self::render_template_xsl(array('condition_survey', 'files', 'datatable_inline'), $data);
		}

		/**
		 * Saves an entry to the database for new/edit - redirects to view
		 *
		 * @param int  $id  entity id - no id means 'new'
		 *
		 * @return void
		 */
		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}

			$id = (int)phpgw::get_var('id');

			if ($id)
			{
				$values = $this->bo->read_single(array('id' => $id, 'view' => true));
			}
			else
			{
				$values = array();
			}

			/*
			 * Overrides with incoming data from POST
			 */
			$values = $this->_populate($values);

			if ($this->receipt['error'])
			{
				$this->edit($values);
			}
			else
			{

				try
				{
					$id = $this->bo->save($values);
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

				$this->_handle_files($id);
				if ($_FILES['import_file']['tmp_name'])
				{
					$this->_handle_import($id);
				}
				else
				{
					phpgwapi_cache::message_set('ok!', 'message');
					self::redirect(array('menuaction' => 'property.uicondition_survey.edit',
						'id' => $id));
				}
			}
		}

		/**
		 * Fetch a list of files to be displayed in view/edit
		 *
		 * @param int  $id  entity id
		 *
		 * @return array $ResultSet json resultset
		 */
		public function get_files()
		{
			$id = phpgw::get_var('id', 'int', 'REQUEST');

			if (!$this->acl_read)
			{
				return;
			}

			$link_file_data = array
				(
				'menuaction' => 'property.uicondition_survey.view_file',
				'id' => $id
			);


			$link_view_file = self::link($link_file_data);

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$files = $vfs->ls(array(
				'string' => "/property/condition_survey/{$id}",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;


//------ Start pagination

			$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$total_records = count($files);

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int)$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;

			if ($allrows)
			{
				$out = $files;
			}
			else
			{
				//	$page = ceil( ( $start / $total_records ) * ($total_records/ $num_rows) );
				$page = ceil(( $start / $num_rows));
				$files_part = array_chunk($files, $num_rows);
				$out = $files_part[$page];
			}

//------ End pagination


			$lang_view = lang('click to view file');
			$lang_delete = lang('click to delete file');

			$values = array();
			foreach ($out as $_entry)
			{
				$values[] = array
					(
					'file_name' => "<a href='{$link_view_file}&amp;file_id={$_entry['file_id']}' target='_blank' title='{$lang_view}'>{$_entry['name']}</a>",
					'delete_file' => "<input type='checkbox' name='file_action[]' value='{$_entry['file_id']}' title='$lang_delete'>",
				);
			}

			return array(
				'recordsTotal' => $total_records,
				'recordsFiltered' => $total_records,
				'draw' => phpgw::get_var('draw', 'int'),
				'data' => $values,
			);
		}

		function get_summation()
		{

			$id = phpgw::get_var('id', 'int', 'REQUEST');
			$year = phpgw::get_var('year', 'int', 'REQUEST');

			if (!$this->acl_read)
			{
				return;
			}

			$values = $this->bo->get_summation($id, $year);

			$total_records = count($values);

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int)$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;
			$start = phpgw::get_var('start', 'int', 'REQUEST', 0);

			$allrows = true;
			$num_rows = $total_records;

			if ($allrows)
			{
				$out = $values;
			}
			else
			{
				$page = ceil(( $start / $total_records ) * ($total_records / $num_rows));
				$values_part = array_chunk($values, $num_rows);
				$out = $values_part[$page];
			}

			return array(
				'recordsTotal' => $total_records,
				'recordsFiltered' => $total_records,
				'draw' => phpgw::get_var('draw', 'int'),
				'data' => $out
			);
		}

		function get_request()
		{
			$id = phpgw::get_var('id', 'int', 'REQUEST');

			if (!$this->acl_read)
			{
				return;
			}

			$borequest = CreateObject('property.borequest');

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'condition_survey_id' => $id,
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
			);

			$values = $borequest->read_survey_data($params);
			$total_records = $borequest->total_records;

			$base_url = self::link(array('menuaction' => 'property.uirequest.edit'));
			foreach ($values as &$_entry)
			{
				$_entry['url'] = "<a href=\"{$base_url}&id={$_entry['id']}\" >{$_entry['id']}</a>";
			}

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int)$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;

			return array(
				'recordsTotal' => $total_records,
				'recordsFiltered' => $total_records,
				'draw' => phpgw::get_var('draw', 'int'),
				'data' => $values,
			);
		}

		/**
		 * Dowloads a single file to the browser
		 *
		 * @param int  $id  entity id
		 *
		 * @return file
		 */
		function view_file()
		{
			if (!$this->acl_read)
			{
				return lang('no access');
			}
			ExecMethod('property.bofiles.get_file', phpgw::get_var('file_id', 'int'));
		}

		/**
		 * Store and / or delete files related to an entity
		 *
		 * @param int  $id  entity id
		 *
		 * @return void
		 */
		private function _handle_files( $id )
		{
			$id = (int)$id;
			if (!$id)
			{
				throw new Exception('uicondition_survey::_handle_files() - missing id');
			}
			$bofiles = CreateObject('property.bofiles');

			if (isset($_POST['file_action']) && is_array($_POST['file_action']))
			{
				$bofiles->delete_file("/condition_survey/{$id}/",array('file_action' => $_POST['file_action']));
			}
			$file_name = str_replace(' ', '_', $_FILES['file']['name']);

			if ($file_name)
			{
				if (!is_file($_FILES['file']['tmp_name']))
				{
					phpgwapi_cache::message_set(lang('Failed to upload file !'), 'error');
					return;
				}

				$to_file = $bofiles->fakebase . '/condition_survey/' . $id . '/' . $file_name;
				if ($bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					phpgwapi_cache::message_set(lang('This file already exists !'), 'error');
				}
				else
				{
					$bofiles->create_document_dir("condition_survey/{$id}");
					$bofiles->vfs->override_acl = 1;

					if (!$bofiles->vfs->cp(array(
							'from' => $_FILES['file']['tmp_name'],
							'to' => $to_file,
							'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
					{
						phpgwapi_cache::message_set(lang('Failed to upload file !'), 'error');
					}
					$bofiles->vfs->override_acl = 0;
				}
			}
		}


		public function export()
		{
			$id = phpgw::get_var('id', 'int', 'REQUEST');
			$data = $this->bo->get_export_data($id);

			$filename = 'tilstandsanalyse';

			$names = array(
				'request_id',
				'building_part',
				'building_part_text',
				'title',
				'descr',
				'Bilde nr',
				'Hjemmel',
				'condition_degree',
				'condition_type_name',
				'consequence',
				'probability',
				'risk',
				'score',
				'recommended_year',
				'amount_total',
				'percentage_investment',
				'percentage_grants',
				'amount_operation',
				'amount_investment',
				'amount_potential_grants'
			);

			$descr = array(
				'ID',
				'Bygningsdel',
				'Bygningsdel tekst',
				'Tilstandbeskrivelse',
				'Tiltak',
				'Bildenummer fotoserie',
				'Hjemmel/ krav',
				'Tilstandsgrad 0-3',
				'Konsekvenstype',
				'Konsekvensgrad 0-3',
				'Sannsynlighet 0-3',
				'Risiko=KGXS',
				'Score=TGxKTxR',
				'Anbefales utført innen',
				'Kalkyle netto TOTALT (V+U)',
				'Andel U',
				'Andel off. tilskudd av U',
				'Andel Vedlikeholds-kostnad (V)',
				'Andel Utviklings- kostnad (U)',
				'Andel off. tilskudd potensiale (av U)'
			);
			$this->excel_out($data, $names, $descr, $input_type = array(), $filename);
		}

		function excel_out( $data, $names, $descr, $input_type = array(), $filename = '' )
		{
			$condition_survey = $data['condition_survey'];
//			_debug_array($condition_survey);die();

			phpgw::import_class('phpgwapi.phpexcel');
			if ($filename)
			{
				$filename_arr = explode('.', str_replace(' ', '_', basename($filename)));
				$filename = $filename_arr[0] . '.xlsx';
			}
			else
			{
				$filename = str_replace(' ', '_', $GLOBALS['phpgw_info']['user']['account_lid']) . '.xlsx';
			}

			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($filename, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
			$cacheSettings = array('memoryCacheSize' => '32MB');
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

			$objPHPExcel = new PHPExcel();


			$objPHPExcel->getProperties()->setCreator($GLOBALS['phpgw_info']['user']['fullname'])
				->setLastModifiedBy($GLOBALS['phpgw_info']['user']['fullname'])
				->setTitle("Download from {$GLOBALS['phpgw_info']['server']['system_name']}")
				->setSubject("Office 2007 XLSX Document")
				->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
				->setKeywords("office 2007 openxml php")
				->setCategory("downloaded file");

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
//			$sheet = $objPHPExcel->getActiveSheet();
// 			$sheet->getRowDimension('1');

			$objPHPExcel->getActiveSheet()->getStyle('A1:T14')->getFill()->applyFromArray(array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					'rgb' => '99cc33'
				)
			));
			$objPHPExcel->getActiveSheet()->getStyle('A15:T15')->getFill()->applyFromArray(array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					'rgb' => 'ffff66'
				)
			));

			$i = 0;
			$row = 1;
			//heading
			$lang = array
				(
				'id' => 'Tilstandsanalyse #',
				'title' => 'Navn',
				'descr' => 'Beskrivelse',
				'location_code' => 'Objekt',
				//			'status_id' => 'Status',
				//			'cat_id' => 'Kategori',
				'vendor_id' => 'Leverandør ID',
				'vendor_name' => 'Utført av',
				//			'coordinator_id' => 'Koordinator',
				'report_date' => 'Analyse dato',
				'multiplier' => 'Multiplikator',
			);
			PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
//			_debug_array($condition_survey);die();
			foreach ($lang as $key => $translation)
			{
				if (isset($condition_survey[$key]))
				{
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $row, $translation);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $row, $condition_survey[$key]);
					$objPHPExcel->getActiveSheet()->getStyle("B{$row}")->getAlignment()->applyFromArray(
						array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
							//	'justify'   => PHPExcel_Style_Alignment::VERTICAL_JUSTIFY,
							'rotation' => 0,
							'wrap' => false
						)
					);
					$objPHPExcel->getActiveSheet()->getStyle("C{$row}")->getAlignment()->applyFromArray(
						array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
							//	'justify'   => PHPExcel_Style_Alignment::VERTICAL_JUSTIFY,
							'rotation' => 0,
							'wrap' => false
						)
					);
					$row++;
				}
			}
			foreach (range('B', 'C', 'D') as $columnID)
			{
				$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

			$count_uicols_name = count($names);
			$_first_row = 15;

			$text_format = array();
			//overskrifter
			$m = 0;
			$col = 'A';
			$objPHPExcel->getActiveSheet()->getRowDimension($_first_row)->setRowHeight(210);
			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				if (!isset($input_type[$k]) || $input_type[$k] != 'hidden')
				{
					if (preg_match('/^loc/i', $names[$k]))
					{
						$text_format[$m] = true;
					}
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($m, $_first_row, $descr[$k]);
					if ($m > 0)
					{
						$objPHPExcel->getActiveSheet()->getStyle("{$col}{$_first_row}")->getAlignment()->setTextRotation(90);
					}
					$m++;
					$col++;
					/*
					  $objPHPExcel->getActiveSheet()->getStyle("D{$_first_row}")->getAlignment()->applyFromArray(
					  array(
					  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					  'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					  //	'justify'   => PHPExcel_Style_Alignment::VERTICAL_JUSTIFY,
					  'rotation'   => 90,
					  'wrap'       => false
					  )
					  );
					 */
				}
			}
			//data
			$j = 0;
			if (isset($data['values']) && is_array($data['values']))
			{
				foreach ($data['values'] as $entry)
				{
					$m = 0;
					foreach ($names as $name)
					{
						$content[$j][$m] = str_replace("\r\n", " ", $entry[$name]);
						$m++;
					}
					$j++;
				}

				$line = $_first_row;

				foreach ($content as $row)
				{
					$col = 'A';

					$line++;
					$rows = count($row);
					for ($i = 0; $i < $rows; $i++)
					{
						if (isset($text_format[$i]))
						{
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicitByColumnAndRow($i, $line, $row[$i], PHPExcel_Cell_DataType::TYPE_STRING);
						}
						else
						{
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, $line, $row[$i]);
						}
						$objPHPExcel->getActiveSheet()->getStyle("{$col}{$line}")->getAlignment()->applyFromArray(
							array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
								'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
								'rotation' => 0,
								'wrap' => true
							)
						);
						$objPHPExcel->getActiveSheet()->getRowDimension($line)->setRowHeight(100);


						$col++;
					}
				}
			}

			// Save Excel 2007 file
//			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->setUseDiskCaching(true, $GLOBALS['phpgw_info']['server']['temp_dir']);
			$objWriter->setOffice2003Compatibility(true);
//			echo "Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB";
//			die();

			$objWriter->save('php://output');
		}
		public function import()
		{
			$id = phpgw::get_var('id', 'int', 'REQUEST');
			$this->_handle_import($id);
		}

		/**
		 * Import deviations found in the survey to the database from a spreadsheet
		 *
		 * @param int  $id  entity id
		 *
		 * @return void
		 */
		private function _handle_import( $id )
		{
			$id = (int)$id;
			if (!$id)
			{
				throw new Exception('uicondition_survey::_handle_import() - missing id');
			}
			phpgwapi_jquery::formvalidator_generate(array('file'));

			$step = phpgw::get_var('step', 'int', 'REQUEST');
			$sheet_id = phpgw::get_var('sheet_id', 'int', 'REQUEST');

			$sheet_id = $sheet_id ? $sheet_id : phpgw::get_var('selected_sheet_id', 'int', 'REQUEST');

			if (!$step)
			{
				if ($cached_file = phpgwapi_cache::session_get('property', 'condition_survey_import_file'))
				{
					phpgwapi_cache::session_clear('property', 'condition_survey_import_file');
					unlink($cached_file);
					unset($cached_file);
				}
			}

			if ($start_line = phpgw::get_var('start_line', 'int', 'REQUEST'))
			{
				phpgwapi_cache::system_set('property', 'import_sheet_start_line', $start_line);
			}
			else
			{
				$start_line = phpgwapi_cache::system_get('property', 'import_sheet_start_line');
				$start_line = $start_line ? $start_line : 1;
			}


			if ($columns = phpgw::get_var('columns'))
			{
				phpgwapi_cache::system_set('property', 'import_sheet_columns', $columns);
			}
			else
			{
				$columns = phpgwapi_cache::system_get('property', 'import_sheet_columns');
				$columns = $columns && is_array($columns) ? $columns : array();
			}


			if ($step > 1)
			{
				$cached_file = phpgwapi_cache::session_get('property', 'condition_survey_import_file');
			}

			if ($step == 1 || isset($_FILES['import_file']['tmp_name']))
			{
				$file = $_FILES['import_file']['tmp_name'];
				$cached_file = "{$file}_temporary_import_file";
				// save a copy to survive multiple steps
				file_put_contents($cached_file, file_get_contents($file));
				phpgwapi_cache::session_set('property', 'condition_survey_import_file', $cached_file);
				$step = 1;

				// Add the file to documents
				$bofiles = CreateObject('property.bofiles');
				$to_file = "{$bofiles->fakebase}/condition_survey/{$id}/" . str_replace(' ', '_', $_FILES['import_file']['name']);

				$bofiles->vfs->rm(array(
					'string' => $to_file,
					'relatives' => array(
						RELATIVE_NONE
					)
					)
				);

				$bofiles->create_document_dir("condition_survey/{$id}");
				$bofiles->vfs->override_acl = 1;

				$bofiles->vfs->cp(array(
					'from' => $_FILES['import_file']['tmp_name'],
					'to' => $to_file,
					'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL)));
				$bofiles->vfs->override_acl = 0;
				unset($bofiles);
			}

			$tabs = array();

			switch ($step)
			{
				case 0:
					$active_tab = 'step_1';
					$lang_submit = lang('continue');
					$tabs['step_1'] = array('label' => lang('choose file'), 'link' => '#step_1');
					$tabs['step_2'] = array('label' => lang('choose sheet'), 'link' => null, 'disable' => true);
					$tabs['step_3'] = array('label' => lang('choose start line'), 'link' => null, 'disable' => true);
					$tabs['step_4'] = array('label' => lang('choose columns'), 'link' => null, 'disable' => true);
					break;
				case 1:
					$active_tab = 'step_2';
					$lang_submit = lang('continue');
					$tabs['step_1'] = array('label' => lang('choose file'), 'link' => self::link(array(
							'menuaction' => 'property.uicondition_survey.import', 'id' => $id, 'step' => 0,
							'sheet_id' => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_2'] = array('label' => lang('choose sheet'), 'link' => '#step_2');
					$tabs['step_3'] = array('label' => lang('choose start line'), 'link' => null, 'disable' => true);
					$tabs['step_4'] = array('label' => lang('choose columns'), 'link' => null, 'disable' => true);
					break;
				case 2:
					$active_tab = 'step_3';
					$lang_submit = lang('continue');
					$tabs['step_1'] = array('label' => lang('choose file'), 'link' => self::link(array(
							'menuaction' => 'property.uicondition_survey.import', 'id' => $id, 'step' => 0,
							'sheet_id' => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_2'] = array('label' => lang('choose sheet'), 'link' => self::link(array(
							'menuaction' => 'property.uicondition_survey.import', 'id' => $id, 'step' => 1,
							'sheet_id' => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_3'] = array('label' => lang('choose start line'), 'link' => '#step_3');
					$tabs['step_4'] = array('label' => lang('choose columns'), 'link' => null, 'disable' => true);
					break;
				case 3:
					$active_tab = 'step_4';
					$lang_submit = lang('import');
					$tabs['step_1'] = array('label' => lang('choose file'), 'link' => self::link(array(
							'menuaction' => 'property.uicondition_survey.import', 'id' => $id, 'step' => 0,
							'sheet_id' => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_2'] = array('label' => lang('choose sheet'), 'link' => self::link(array(
							'menuaction' => 'property.uicondition_survey.import', 'id' => $id, 'step' => 1,
							'sheet_id' => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_3'] = array('label' => lang('choose start line'), 'link' => self::link(array(
							'menuaction' => 'property.uicondition_survey.import', 'id' => $id, 'step' => 2,
							'sheet_id' => $sheet_id, 'start_line' => $start_line)));
					$tabs['step_4'] = array('label' => lang('choose columns'), 'link' => '#step_4');
					break;
				/*
				  case 4://temporary
				  phpgwapi_cache::session_clear('property', 'condition_survey_import_file');
				  unlink($cached_file);
				  $GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'property.uicondition_survey.import', 'id' =>$id, 'step' => 0));
				  break;
				 */
			}

//-----------

			if (!$step)
			{
				phpgwapi_cache::session_clear('property', 'condition_survey_import_file');
				unlink($cached_file);
			}
			else if ($cached_file)
			{
				phpgw::import_class('phpgwapi.phpexcel');

				try
				{
					$objPHPExcel = PHPExcel_IOFactory::load($cached_file);
					$AllSheets = $objPHPExcel->getSheetNames();

					$sheets = array();
					if ($AllSheets)
					{
						foreach ($AllSheets as $key => $sheet)
							$sheets[] = array
								(
								'id' => $key,
								'name' => $sheet,
								'selected' => $sheet_id == $key
							);
					}

					$objPHPExcel->setActiveSheetIndex((int)$sheet_id);
					$rows = $objPHPExcel->getActiveSheet()->getHighestDataRow();
					$highestColumm = $objPHPExcel->getActiveSheet()->getHighestDataColumn();
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						phpgwapi_cache::session_clear('property', 'condition_survey_import_file');
						unlink($cached_file);
					}
				}
			}

			$survey = $this->bo->read_single(array('id' => $id, 'view' => $mode == 'view'));

			$i = 0;
			$html_table = '<table class="pure-table pure-table-bordered">';
			if ($rows > 1 && $step == 2)
			{

				$cols = array();
				for ($j = 0; $j < $highestColumnIndex; $j++)
				{
					$cols[] = $this->getexcelcolumnname($j);
				}

				$html_table .= "<thead><tr><th align = 'center'>" . lang('select') . "</th><th align = 'center'>" . lang('row') . "</th><th align='center'>" . implode("</th><th align='center'>", $cols) . '</th></tr></thead>';
				foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row)
				{
					if ($i > 20)
					{
						break;
					}
					$i++;

					$row_key = $i;
					$_checked = '';
					if ($start_line == $row_key)
					{
						$_checked = 'checked="checked"';
					}

					$_radio = "<input id=\"start_line\" type =\"radio\" {$_checked} name=\"start_line\" value=\"{$row_key}\">";

					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);

					$row_values = array();
					foreach ($cellIterator as $cell)
					{
						if (!is_null($cell))
						{
							$row_values[] = $cell->getCalculatedValue();
						}
					}
					$html_table .= "<tr><td>{$_radio}</td><td>{$row_key}</td><td>" . implode('</td><td>', $row_values) . '</td></tr>';
				}
				echo '</table>';
			}
			else if ($rows > 1 && $step == 3)
			{
				$_options = array
					(
					'_skip_import_' => 'Utelates fra import/implisitt',
					'import_type' => 'import type',
					'building_part' => 'bygningsdels kode',
					'descr' => 'Tilstandbeskrivelse',
					'title' => 'Tiltak/overskrift',
					'condition_degree' => 'Tilstandsgrad',
					'condition_type' => 'Konsekvenstype',
					'consequence' => 'Konsekvensgrad',
					'probability' => 'Sannsynlighet',
					'due_year' => 'År (innen)',
					'amount_investment' => 'Beløp investering',
					'amount_operation' => 'Beløp drift',
					'amount_potential_grants' => 'Potensial for offentlig støtte',
				);

				$custom = createObject('phpgwapi.custom_fields');
				$attributes = $custom->find('property', '.project.request', 0, '', '', '', true, true);

				foreach ($attributes as $attribute)
				{
					$_options["custom_attribute_{$attribute['id']}"] = $attribute['input_text'];
				}

				phpgw::import_class('phpgwapi.sbox');

				for ($j = 0; $j < $highestColumnIndex; $j++)
				{
					$_column = $this->getexcelcolumnname($j);
					$_value = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, $start_line)->getCalculatedValue();
					$selected = isset($columns[$_column]) && $columns[$_column] ? $columns[$_column] : '';

					$_listbox = phpgwapi_sbox::getArrayItem("columns[{$_column}]", $selected, $_options, true);
					$html_table .= "<tr><td>[{$_column}] {$_value}</td><td>{$_listbox}</td><tr>";
				}
			}
			else if ($rows > 1 && $step == 4)
			{

				$rows = $objPHPExcel->getActiveSheet()->getHighestDataRow();
				$rows = $rows ? $rows + 1 : 0;

				$import_data = array();

				for ($i = $start_line; $i < $rows; $i++)
				{
					$_result = array();

					foreach ($columns as $_row_key => $_value_key)
					{
						if ($_value_key != '_skip_import_')
						{
							$_result[$_value_key] = $objPHPExcel->getActiveSheet()->getCell("{$_row_key}{$i}")->getCalculatedValue();
						}
					}
					$import_data[] = $_result;
				}
				if ($import_data)
				{
					try
					{
						$this->bo->import($survey, $import_data);
					}
					catch (Exception $e)
					{
						if ($e)
						{
							phpgwapi_cache::message_set($e->getMessage(), 'error');
						}
					}
				}

//				$msg = "'{$cached_file}' contained " . count($import_data) . " lines";
//				phpgwapi_cache::message_set($msg, 'message'); 
			}



			$html_table .= '</table>';




			if (isset($survey['location_code']) && $survey['location_code'])
			{
				$survey['location_data'] = execMethod('property.solocation.read_single', $survey['location_code']);
			}

			$bolocation = CreateObject('property.bolocation');
			$location_data = $bolocation->initiate_ui_location(array
				(
				'values' => $survey['location_data'],
				'type_id' => 2,
				'lookup_type' => 'view2',
				'tenant' => false,
				'lookup_entity' => array(),
				'entity_data' => isset($survey['p']) ? $survey['p'] : ''
			));

			$data = array
				(
				'lang_submit' => $lang_submit,
				'survey' => $survey,
				'location_data2' => $location_data,
				'step' => $step + 1,
				'sheet_id' => $sheet_id,
				'start_line' => $start_line,
				'html_table' => $html_table,
				'sheets' => array('options' => $sheets),
				'tabs' => $GLOBALS['phpgw']->common->create_tabs($tabs, $active_tab),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('condition survey import');

			self::render_template_xsl(array('condition_survey_import'), $data);
		}

		/**
		 * Get excel column name
		 * @param index : a column index we want to get the value in excel column format
		 * @return (string) : excel column format
		 */
		private function getexcelcolumnname( $index )
		{
			//Get the quotient : if the index superior to base 26 max ?
			$quotient = $index / 26;
			if ($quotient >= 1)
			{
				//If yes, get top level column + the current column code
				return $this->getexcelcolumnname($quotient - 1) . chr(($index % 26) + 65);
			}
			else
			{
				//If no just return the current column code
				return chr(65 + $index);
			}
		}

		/**
		 * Gets user candidates to be used as coordinator - called as ajax from edit form
		 *
		 * @param string  $query
		 *
		 * @return array
		 */
		public function get_users()
		{
			if (!$this->acl_read)
			{
				return;
			}

			$query = phpgw::get_var('query');

			$accounts = $GLOBALS['phpgw']->accounts->get_list('accounts', $start, $sort, $order, $query, $offset);

			$values = array();
			foreach ($accounts as $account)
			{
				if ($account->enabled)
				{
					$values[] = array
						(
						'id' => $account->id,
						'name' => $account->__toString(),
					);
				}
			}
			return array('ResultSet' => array('Result' => $values));
		}

		/**
		 * Gets vendor canidated to be used as vendor - called as ajax from edit form
		 *
		 * @param string  $query
		 *
		 * @return array
		 */
		public function get_vendors()
		{
			if (!$this->acl_read)
			{
				return;
			}

			$query = phpgw::get_var('query');

			$sogeneric = CreateObject('property.sogeneric', 'vendor');
			$values = $sogeneric->read(array('query' => $query));
			foreach ($values as &$entry)
			{
				$entry['name'] = $entry['org_name'];
			}
			return array('ResultSet' => array('Result' => $values));
		}

		/**
		 * Edit title fo entity directly from table
		 *
		 * @param int  $id  id of entity
		 * @param string  $value new title of entity
		 *
		 * @return string text to appear in ui as receipt on action
		 */
		public function edit_survey_title()
		{
			$id = phpgw::get_var('id', 'int', 'POST');

			if (!$this->acl_edit)
			{
				return lang('no access');
			}

			if ($id)
			{
				$values = $this->bo->read_single(array('id' => $id, 'view' => true));
				$values['title'] = phpgw::get_var('value');

				try
				{
					$this->bo->edit_title($values);
				}
				catch (Exception $e)
				{
					if ($e)
					{
						echo $e->getMessage();
					}
				}
				echo true;
			}
			else
			{
				echo "ERROR";
			}
		}

		/**
		 * Delete survey and all related info
		 *
		 * @param int  $id  id of entity
		 *
		 * @return string text to appear in ui as receipt on action
		 */
		public function delete()
		{
			if (!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_DELETE, 'property'))
			{
				return 'No access';
			}
			$id = phpgw::get_var('id', 'int', 'GET');

			try
			{
				$this->bo->delete($id);
			}
			catch (Exception $e)
			{
				if ($e)
				{
					return $e->getMessage();
				}
			}
			return 'Deleted';
		}

		/**
		 * Delete related requests only
		 *
		 * @param int  $id  id of entity
		 *
		 * @return string text to appear in ui as receipt on action
		 */
		public function delete_imported_records()
		{
			if (!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_DELETE, 'property'))
			{
				return 'No access';
			}
			$id = phpgw::get_var('id', 'int', 'GET');

			try
			{
				$this->bo->delete_imported_records($id);
			}
			catch (Exception $e)
			{
				if ($e)
				{
					return $e->getMessage();
				}
			}
			return 'Deleted';
		}

		/**
		 * Prepare data for summation - single survey or all
		 *
		 * @return void
		 */
		public function summation()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::project::condition_survey::summation";

			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uicondition_survey.index'));
			}

			$params = array
				(
				'start' => 0,
				'sort' => 'id',
				'dir' => 'asc',
				'cat_id' => 0,
				'allrows' => true
			);

			$survey_list = $this->bo->read($params);

			$surveys = array();
			$surveys[] = array
				(
				'id' => 0,
				'name' => lang('select'),
			);
			$surveys[] = array
				(
				'id' => -1,
				'name' => lang('all'),
			);

			foreach ($survey_list as $survey)
			{
				$surveys[] = array
					(
					'id' => $survey['id'],
					'name' => $survey['title'],
					'description' => $survey['address'],
				);
			}



			$current_year = date('Y');

			$years = array();

			for ($i = 0; $i < 6; $i++)
			{
				$years[] = array
					(
					'id' => $current_year,
					'name' => $current_year
				);
				$current_year++;
			}

			$summation_def = array
				(
				array('key' => 'building_part', 'label' => lang('building part'), 'sortable' => false),
				array('key' => 'category', 'label' => lang('category'), 'sortable' => false),
				array('key' => 'period_1', 'label' => lang('year') . ':: < 1', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'period_2', 'label' => lang('year') . ':: 1 - 5', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'period_3', 'label' => lang('year') . ':: 6 - 10', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'period_4', 'label' => lang('year') . ':: 11 - 15', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'period_5', 'label' => lang('year') . ':: 16 - 20', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'period_6', 'label' => lang('year') . ':: 21 +', 'sortable' => false,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'sum', 'label' => lang('sum'), 'sortable' => false, 'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0'),
			);

			$datatable_def = array();
			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uicondition_survey.get_summation',
						'id' => $id, 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $summation_def
			);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'surveys' => array('options' => $surveys),
				'years' => array('options' => $years),
			);

			phpgwapi_jquery::load_widget('numberformat');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('condition survey');

			self::render_template_xsl(array('condition_survey_summation'), $data);
		}
		/*
		 * Overrides with incoming data from POST
		 */

		private function _populate( $data = array() )
		{
			$insert_record = phpgwapi_cache::session_get('property', 'insert_record');

			$values = phpgw::get_var('values');

			$_fields = array
				(
				array
					(
					'name' => 'title',
					'type' => 'string',
					'required' => true
				),
				array
					(
					'name' => 'descr',
					'type' => 'string',
					'required' => true
				),
				array
					(
					'name' => 'cat_id',
					'type' => 'integer',
					'required' => true
				),
				array
					(
					'name' => 'report_date',
					'type' => 'string',
					'required' => true
				),
				array
					(
					'name' => 'status_id',
					'type' => 'integer',
					'required' => true
				),
				array
					(
					'name' => 'vendor_id',
					'type' => 'integer',
					'required' => false
				),
				array
					(
					'name' => 'vendor_name',
					'type' => 'string',
					'required' => false
				),
				array
					(
					'name' => 'coordinator_id',
					'type' => 'integer',
					'required' => false
				),
				array
					(
					'name' => 'coordinator_name',
					'type' => 'string',
					'required' => false
				),
				array
					(
					'name' => 'multiplier',
					'type' => 'float',
					'required' => false
				),
			);


			foreach ($_fields as $_field)
			{
				if ($data[$_field['name']] = $_POST['values'][$_field['name']])
				{
					$data[$_field['name']] = phpgw::clean_value($data[$_field['name']], $_field['type']);
				}
				if ($_field['required'] && !$data[$_field['name']])
				{
					$this->receipt['error'][] = array('msg' => lang('Please enter value for attribute %1', $_field['name']));
				}
			}

//_debug_array($data);die();

			$values = $this->bocommon->collect_locationdata($data, $insert_record);

			if (!isset($values['location_code']) || !$values['location_code'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a location !'));
			}

			/*
			 * Extra data from custom fields
			 */
			$values['attributes'] = phpgw::get_var('values_attribute');

			if (is_array($values['attributes']))
			{
				foreach ($values['attributes'] as $attribute)
				{
					if ($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
					{
						$this->receipt['error'][] = array('msg' => lang('Please enter value for attribute %1', $attribute['input_text']));
					}
				}
			}

			if (!isset($values['cat_id']) || !$values['cat_id'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a category !'));
			}

			if (!isset($values['title']) || !$values['title'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please give a title !'));
			}

			if (!isset($values['report_date']) || !$values['report_date'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a date!'));
			}

			return $values;
		}

		private function _get_categories( $selected = 0 )
		{
			$cats = CreateObject('phpgwapi.categories', -1, 'property', $this->acl_location);
			$cats->supress_info = true;
			$categories = $cats->formatted_xslt_list(array('format' => 'filter', 'selected' => $selected,
				'globals' => true, 'use_acl' => $this->_category_acl));
			$default_value = array('cat_id' => '', 'name' => lang('no category'));
			array_unshift($categories['cat_list'], $default_value);

			foreach ($categories['cat_list'] as & $_category)
			{
				$_category['id'] = $_category['cat_id'];
			}

			return $categories['cat_list'];
		}
	}