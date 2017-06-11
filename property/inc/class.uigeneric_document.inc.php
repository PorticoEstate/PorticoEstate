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
	 * @version $Id: class.uigeneric_document.inc.php 14913 2016-04-11 12:27:37Z sigurdne $
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uigeneric_document extends phpgwapi_uicommon_jquery
	{

		private $bo;
		private $receipt = array();
		public $public_functions = array(
			'query' => true,
			'index' => true,
			'view' => true,
			'add' => true,
			'edit' => true,
			'save' => true,
			'delete' => true,
			'get_vendors' => true,
			'get_users' => true,
			'edit_title' => true,
			'get_componentes' => true,
			'save_file_relations' => true,
			'get_location_filter' => true,
			'get_part_of_town' => true,
			'get_locations_for_type' => true,
			'get_categories_for_type' => true,
			'view_file' => true,
			'download' => true,
		);

		public function __construct()
		{
			parent::__construct();

			$this->bo = CreateObject('property.bogeneric_document');
			$this->bocommon = & $this->bo->bocommon;
			$this->soadmin_entity = CreateObject('property.soadmin_entity');
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.document';//$this->bo->acl_location;
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage = $this->acl->check($this->acl_location, 16, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::documentation::generic";
		}

		public function download()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
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

		/**
		 * Prepare UI
		 * @return void
		 */
		public function index()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
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
				'datatable_name' => lang('generic document'),
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
					'source' => self::link(array('menuaction' => 'property.uigeneric_document.index',
						'phpgw_return_as' => 'json')),
					'download' => self::link(array('menuaction' => 'property.uigeneric_document.download',
						'export' => true, 'allrows' => true)),
					'new_item' => self::link(array('menuaction' => 'property.uigeneric_document.add')),
					'allrows' => true,
					'editor_action' => self::link(array('menuaction' => 'property.uigeneric_document.edit_title')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'name',
							'label' => lang('Name'),
							'sortable' => true,
							'editor' => true
						),
						/* 						array(
						  'key' => 'descr',
						  'label' => lang('description'),
						  'sortable' => false,
						  ), */
						array(
							'key' => 'app',
							'label' => lang('app'),
							'sortable' => true
						),
						array(
							'key' => 'version',
							'label' => lang('version'),
							'sortable' => true
						),
						array(
							'key' => 'created',
							'label' => lang('created'),
							'sortable' => true,
							'className' => 'center'
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
				'my_name' => 'view_document',
				'text' => lang('view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uigeneric_document.view'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit_document',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uigeneric_document.edit'
				)),
				'parameters' => json_encode($parameters)
			);


			if ($GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_DELETE, 'property'))
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'delete_document',
					'text' => lang('delete'),
					'confirm_msg' => lang('do you really want to delete this entry') . '?',
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uigeneric_document.delete'
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
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'location' => 'generic_document',
				'allrows' => phpgw::get_var('length', 'int') == -1
			);

			$values = $this->bo->read($params);
			if (phpgw::get_var('export', 'bool'))
			{
				return $values;
			}

			foreach($values as &$item)
			{
				$item['name'] = '<a href="'.self::link(array('menuaction' => 'property.uigeneric_document.view_file', 'file_id' => $item['id'])).'">'.$item['name'].'</a>';
				$item['link'] = self::link(array('menuaction' => 'property.uigeneric_document.view', 'id' => $item['id']));
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		public function get_locations_for_type()
		{
			$type_id = phpgw::get_var('type_id', 'int');
			$file_id = phpgw::get_var('id', 'int');
			$only_related = phpgw::get_var('only_related', 'boolean');

			if (!$type_id)
			{
				$type_id = 1;
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'type_id' => $type_id,
				'district_id' => phpgw::get_var('district_id', 'int', 'REQUEST', 0),
				'part_of_town_id' => phpgw::get_var('part_of_town_id', 'int', 'REQUEST', 0),
				'allrows' => ($only_related) ? 1 : (phpgw::get_var('length', 'int') == -1)
			);

            $solocation = CreateObject('property.solocation');
            $locations = $solocation->read($params);

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$type_id}");
			if ($file_id)
			{
				$relation_values = $this->bo->get_file_relations($file_id, $location_id);
			}
			$values_location_item_id = array();
			if (count($relation_values))
			{
				foreach($relation_values as $item)
				{
					$values_location_item_id[] = $item['location_item_id'];
				}
			}

			$values = array();
			foreach($locations as $item)
			{
				$checked = in_array($item['id'], $values_location_item_id) ? 'checked="checked"' : '';
				$hidden = ($checked) ? '<input type="hidden" class="locations_related" value="'.$item['id'].'">' : '';

				if ($only_related && empty($checked))
				{
					continue;
				}

				$values[] = array(
					'location_code' => '<a href="'.self::link(array('menuaction' => 'property.uilocation.view', 'location_code' => $item['location_code'])).'">'.$item['location_code'].'</a>',
					'loc1_name' => $item['loc1_name'],
					'relate' => '<input value="'.$item['id'].'" class="locations mychecks" type="checkbox" '.$checked.'>'.$hidden
				);
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = ($only_related) ? count($values_location_item_id) : $solocation->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		public function view()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
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

			if (!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uigeneric_document.view',
					'id' => $id));
			}

			if ($mode == 'view')
			{
				if (!$this->acl_read)
				{
					phpgw::no_access();
					return;
				}
			}
			else
			{
				if (!$this->acl_add && !$this->acl_edit)
				{
					phpgw::no_access();
					return;
				}
			}

			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';

			if ($id)
			{
				if (!$values)
				{
					$values = (array) $this->bo->read_single($id);
					$paths = array();
					foreach ($values['path'] as $path)
					{
						$paths[] = $path;
					}
					$values['report_date'] = ($values['report_date']) ? date($this->dateFormat, $values['report_date']) : '';
					$values['paths'] = implode('<br/>',$paths);
				}
				$values['id'] = $id;
			}

			$categories = $this->_get_categories($values['cat_id']);

			self::message_set($this->receipt);

			$datatable_def = array();

			if ($id)
			{
				$tabs['relations'] = array('label' => lang('Components'), 'link' => '#relations');
				$tabs['locations'] = array('label' => lang('Locations'), 'link' => '#locations');

				$related_def = array
					(
					array('key' => 'id', 'label' => lang('id'), 'sortable' => false, 'resizeable' => true),
					array('key' => 'name', 'label' => lang('name'), 'sortable' => false, 'resizeable' => true),
					array('key' => 'category', 'label' => lang('Category'), 'sortable' => false, 'resizeable' => true),
					array('key' => 'relate', 'label' => lang('related'), 'sortable' => false, 'resizeable' => true),
				);

				$values_location = $this->get_location_filter();
				$entity_group = execMethod('property.bogeneric.get_list', array('type' => 'entity_group', 'add_empty' => true));
				$type_filter = 	execMethod('property.soadmin_location.read', array());
				$category_filter = $this->get_categories_for_type();

				$district_filter = $this->bocommon->select_district_list('filter');
				array_unshift($district_filter, array('id' => '', 'name' => lang('no district')));

				$part_of_town_filter = $this->get_part_of_town();

				$tabletools[] = array
					(
					'my_name' => 'relate',
					'text' => lang('Relate'),
					'className' => 'relate',
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'property.uigeneric_document.save_file_relations',
						'phpgw_return_as' => 'json'
					)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id')))) . ";
						setRelationsComponents(oArgs);
					"
				);

				$datatable_def[] = array
				(
					'container' => 'datatable-container_0',
					'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uigeneric_document.get_componentes',
							'id' => $id, 'location_id' => $values_location[0]['id'], 'phpgw_return_as' => 'json'))),
					'ColumnDefs' => $related_def,
					'tabletools' => ($mode == 'edit') ? $tabletools : array()
				);

				$related_def2 = array
					(
					array('key' => 'location_code', 'label' => lang('location'), 'sortable' => true, 'resizeable' => true),
					array('key' => 'loc1_name', 'label' => lang('name'), 'sortable' => true, 'resizeable' => true),
					//array('key' => 'location_id', 'label' => lang('location id'), 'sortable' => false, 'resizeable' => true, 'hidden' => true),
					array('key' => 'relate', 'label' => lang('related'), 'sortable' => false, 'resizeable' => true),
				);

				$tabletools2[] = array
					(
					'my_name' => 'relate_locations',
					'text' => lang('Relate'),
					'className' => 'relate',
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'property.uigeneric_document.save_file_relations',
						'phpgw_return_as' => 'json'
					)) . ";
						setRelationsLocations(oArgs);
					"
				);

				$datatable_def[] = array
				(
					'container' => 'datatable-container_1',
					'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uigeneric_document.get_locations_for_type', 'id' => $id, 'phpgw_return_as' => 'json'))),
					'ColumnDefs' => $related_def2,
					'tabletools' => ($mode == 'edit') ? $tabletools2 : array()
				);
			}

			$vfs = CreateObject('phpgwapi.vfs');
			$file_info = $vfs->get_info($id);

			$data = array
			(
				'datatable_def' => $datatable_def,
				'document' => $values,
				'lang_coordinator' =>  lang('coordinator'),
				'link_file' =>  self::link(array('menuaction' => 'property.uigeneric_document.view_file', 'file_id' => $file_info['file_id'])),
				'file_name' =>  $file_info['name'],
				'categories' => array('options' => $categories),
				'status_list' => array('options' => array('id' => 1, 'name' => 'status_1')),
				'editable' => $mode == 'edit',
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'location_filter' => array('options' => $values_location),
				'entity_group_filter' => array('options' => $entity_group),

				'type_filter' => array('options' => $type_filter),
				'category_filter' => array('options' => $category_filter),
				'district_filter' => array('options' => $district_filter),
				'part_of_town_filter' => array('options' => $part_of_town_filter),

				'link_controller_example' => self::link(array('menuaction' => 'controller.uicomponent.index'))
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('generic document');

			if ($mode == 'edit')
			{
				$GLOBALS['phpgw']->jqcal->add_listener('report_date');
				phpgwapi_jquery::load_widget('core');
				phpgwapi_jquery::formvalidator_generate(array('date', 'security','file'));
			}

			phpgwapi_jquery::load_widget('numberformat');
			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('property', 'portico', 'generic_document.edit.js');

			self::add_javascript('phpgwapi', 'tinybox2', 'packed.js');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');

			self::render_template_xsl(array('generic_document', 'datatable_inline'), $data);
		}

		public function get_categories_for_type()
		{
			$type_id = phpgw::get_var('type_id', 'int');

			if (!$type_id)
			{
				$type_id = 1;
			}

			$categories = $this->bocommon->select_category_list(array
				('format' => 'filter',
				'selected' => '',
				'type' => 'location',
				'type_id' => $type_id,
				'order' => 'descr')
			);
			array_unshift($categories, array('id' => '', 'name' => lang('no category')));

			return $categories;
		}

		public function get_part_of_town()
		{
			$district_id = phpgw::get_var('district_id', 'int');
			$values = $this->bocommon->select_part_of_town('filter', '', $district_id);
			array_unshift($values, array('id' => '', 'name' => lang('no part of town')));

			return $values;
		}

		public function get_location_filter()
		{
			$entity_group_id = phpgw::get_var('entity_group_id', 'int');

			$location_filter = phpgwapi_cache::session_get('property', "location_filter_{$entity_group_id}");

			if (!$location_filter)
			{
				//$this->soadmin_entity = CreateObject('property.soadmin_entity');
				$entity_list = $this->soadmin_entity->read(array('allrows' => true));

				$location_filter = array();
				foreach ($entity_list as $entry)
				{
					$categories = $this->soadmin_entity->read_category(array('entity_id' => $entry['id'],
						'order' => 'name', 'sort' => 'asc', 'enable_controller' => true, 'allrows' => true));
					foreach ($categories as $category)
					{
						if ($entity_group_id && $category['entity_group_id'] != $entity_group_id)
						{
							continue;
						}
						$location_filter[] = array(
							'id' => $category['location_id'],
							'name' => "{$entry['name']}::{$category['name']}",
						);
					}
				}
				phpgwapi_cache::session_set('property', "location_filter_{$entity_group_id}", $location_filter);
			}

			foreach ($location_filter as &$location)
			{
				$location['selected'] = $location['id'] == $location_id ? 1 : 0;
			}
			return $location_filter;
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
				$values = (array)$this->bo->read_single($id);
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
					$id = $this->_handle_files($id);
					$receipt = $this->bo->save($values, $id);
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

				if ($receipt['message'])
				{
					phpgwapi_cache::message_set($receipt['message'], 'message');
				} else {
					phpgwapi_cache::message_set($receipt['message'], 'error');
				}

				self::redirect(array('menuaction' => 'property.uigeneric_document.edit', 'id' => $id));
			}
		}

		/**
		 * Fetch a list of files to be displayed in view/edit
		 *
		 * @param int  $id  entity id
		 *
		 * @return array $ResultSet json resultset
		 */
		public function get_componentes()
		{
			if (!$this->acl_read)
			{
				return;
			}

			$file_id = phpgw::get_var('id', 'int');
			$location_id = phpgw::get_var('location_id', 'int');
			$search = phpgw::get_var('search');
			$draw = phpgw::get_var('draw', 'int');
			$only_related = phpgw::get_var('only_related', 'boolean');
			$all_types = phpgw::get_var('all_types', 'boolean');
			$entity_group_id = phpgw::get_var('entity_group_id');

			$name_field = 'tfm_nr';
			$entity_list = $this->soadmin_entity->read(array('allrows' => true));
			$e_list = array();
			foreach($entity_list as $entity)
			{
				$e_list[$entity['id']] = $entity['name'];
			}
				
			if ($all_types)
			{
				$result = $this->bo->get_file_relations_componentes(array(
					'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
					'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
					'allrows' => phpgw::get_var('length', 'int') == -1,
					'file_id' => $file_id,
					'entity_group_id' => $entity_group_id
					));

				$values = array();
				foreach($result as $item)
				{
					$values[] = array(
						'id' => '<a href="'.self::link(array('menuaction' => 'property.uientity.view', 'location_id' => $item['location_id'], 'id' => $item['id'])).'">'.$item['id'].'</a>',
						'name' => $item[$name_field],
						'category' => $e_list[$item['entity_id']].'::'.$item['category_name'],
						'relate' => '<input value="'.$item['id'].'_'.$item['location_id'].'" class="components mychecks" type="checkbox" checked="checked"><input type="hidden" class="components_related" value="'.$item['id'].'_'.$item['location_id'].'">',
					);
				}

				$result_data = array('results' => $values);
				$result_data['total_records'] = $this->bo->total_records_componentes;
				$result_data['draw'] = $draw;

				return $this->jquery_results($result_data);
			}
		
            $soentity = CreateObject('property.soentity');
            $_components = $soentity->read( array(
                'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
                'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
                'query' => $search['value'],
                'allrows' => ($only_related) ? 1 : (phpgw::get_var('length', 'int') == -1),
                'filter_entity_group' => 0,
                'location_id' => $location_id,
                'filter_item' => array()
                ));
			$category = $this->soadmin_entity->get_single_category($location_id, true);

			if ($file_id)
			{
				$relation_values = $this->bo->get_file_relations($file_id, $location_id );
			}
			$values_location_item_id = array();
			if (count($relation_values))
			{
				foreach($relation_values as $item)
				{
					$values_location_item_id[] = $item['location_item_id'];
				}
			}

			$values = array();
			foreach($_components as $item)
			{
				$checked = in_array($item['id'], $values_location_item_id) ? 'checked="checked"' : '';
				$hidden = ($checked) ? '<input type="hidden" class="components_related" value="'.$item['id'].'">' : '';

				if ($only_related && empty($checked))
				{
					continue;
				}

				$values[] = array(
					'id' => '<a href="'.self::link(array('menuaction' => 'property.uientity.view', 'location_id' => $location_id, 'id' => $item['id'])).'">'.$item['id'].'</a>',
					'name' => $item[$name_field],
					'category' => $e_list[$item['entity_id']].'::'.$category['name'],
					'relate' => '<input value="'.$item['id'].'" class="components mychecks" type="checkbox" '.$checked.'>'.$hidden,
				);
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = ($only_related) ? count($values_location_item_id) : $soentity->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}
		
		public function save_file_relations()
		{
			$receipt = array();

			$all_types = phpgw::get_var('all_types', 'int');
			$type_id = phpgw::get_var('type_id', 'int');
			$location_id = phpgw::get_var('location_id', 'int');
			$file_id = phpgw::get_var('file_id', 'int');
			$items = phpgw::get_var('items', 'array', 'REQUEST', array());
			$related = phpgw::get_var('related', 'array', 'REQUEST', array());

			$add = array_diff($items, $related);
			$delete = array_diff($related, $items);

			if ($all_types) 
			{
				if (count($add))
				{
					foreach ($add as $item) {
						$values = explode('_', $item);
						$result = $this->bo->save_file_relations( array($values[0]), array(), $values[1], $file_id );
					}
				}

				if (count($delete))
				{
					foreach ($delete as $item) {
						$values = explode('_', $item);
						$result = $this->bo->save_file_relations( array(), array($values[0]), $values[1], $file_id );
					}
				}
			} 
			else {
				
				if (empty($location_id))
				{
					$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$type_id}");
				}
				$result = $this->bo->save_file_relations( $add, $delete, $location_id, $file_id );
			}

			if ($result)
			{
				$receipt['message'][] = array('msg' => lang('Records has been added'));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('Nothing changed'));
			}

			return $receipt;
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

			if (empty($id))
			{
				if (!is_file($_FILES['file']['tmp_name']))
				{
					//phpgwapi_cache::message_set(lang('Failed to upload file !'), 'error');
					throw new Exception('Failed to upload file !');
				}
			}

			$bofiles = CreateObject('property.bofiles');

			$file_name = str_replace(' ', '_', $_FILES['file']['name']);

			if ($file_name)
			{
				$to_file = $bofiles->fakebase . '/generic_document/' .$file_name;
				/*if ($bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => array(RELATIVE_NONE)
					)))
				{
					//phpgwapi_cache::message_set(lang('This file already exists !'), 'error');
					throw new Exception('This file already exists !');
				}
				else
				{*/
					$receipt = $bofiles->create_document_dir("generic_document");
					if (count($receipt['error']))
					{
						throw new Exception('failed to create directory');
					}
					$bofiles->vfs->override_acl = 1;

					$file_id = $bofiles->vfs->cp3(array(
							'from' => $_FILES['file']['tmp_name'],
							'to' => $to_file,
							'id' => $id,
							'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL)));
					$bofiles->vfs->override_acl = 0;

					if (empty($file_id))
					{
						throw new Exception('Failed to upload file !');
					}

					return $file_id;
				//}
			} else {
				return $id;
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
		public function edit_title()
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
		 * Delete document and all related info
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
				$result = $this->bo->delete($id);
			}
			catch (Exception $e)
			{
				if ($e)
				{
					return $e->getMessage();
				}
			}
			return $result;
		}

		/*
		 * Overrides with incoming data from POST
		 */
		private function _populate( $data = array() )
		{

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