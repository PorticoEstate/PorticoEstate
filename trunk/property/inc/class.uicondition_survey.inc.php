<?php

	/**
	 * phpGroupWare - logistic: a part of a Facilities Management System.
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
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.jquery');

	class property_uicondition_survey extends phpgwapi_uicommon
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
			'get_vendors'	=> true
		);

		public function __construct()
		{
			parent::__construct();

			$this->bo 					= CreateObject('property.bocondition_survey');
			$this->bocommon				= & $this->bo->bocommon;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= $this->bo->acl_location;
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::condition_survey";
			$GLOBALS['phpgw']->css->add_external_file('logistic/templates/base/css/base.css');
		}


		public function index()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$categories = $this->_get_categories();
			

			$data = array(
				'datatable_name'	=> lang('condition survey'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'cat_id',
								'text' => lang('category') . ':',
								'list' => $categories,
							),
							array('type' => 'text',
								'text' => lang('search'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => lang('new survey'),
								'href' => self::link(array('menuaction' => 'property.uicondition_survey.add')),
								'class' => 'new_item'
							),
							array(
								'type' => 'link',
								'value' => lang('download'),
								'href' => self::link(array('menuaction' => 'property.uicondition_survey.index', 'export' => true, 'allrows' => true)),
								'class' => 'new_item'
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['allrows'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => 'property.uicondition_survey.index', 'allrows' => true))
							),

						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'property.uicondition_survey.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key' => 'name',
							'label' => lang('name'),
							'sortable' => true
						),
						array(
							'key' => 'description',
							'label' => lang('description'),
							'sortable' => false,
							'editor' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:true})'
						),
						array(
							'key' => 'survey_type_label',
							'label' => lang('type'),
							'sortable' => false
						),
						array(
							'key' => 'link',
							'hidden' => true
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
							'name'		=> 'id',
							'source'	=> 'id'
						),
					)
				);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'edit_survey',
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'logistic.uiactivity.edit'
						)),
						'parameters'	=> json_encode($parameters)
					);

			self::render_template_xsl('datatable_common', $data);
		}


		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', 0),
				'query' => phpgw::get_var('query'),
				'sort' => phpgw::get_var('sort'),
				'dir' => phpgw::get_var('dir'),
				'cat_id' => phpgw::get_var('dir', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('allrows', 'bool')
			);

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			$export = phpgw::get_var('export');

			$values = $this->bo->read($params);

			// ... add result data
			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['start'] = $params['start'];
			$result_data['sort'] = $params['sort'];
			$result_data['dir'] = $params['dir'];

			$editable = phpgw::get_var('editable') == 'true' ? true : false;

			if (!$export)
			{
				//Add action column to each row in result table
				array_walk(	$result_data['results'], array($this, '_add_links'), "property.uicondition_survey.view" );
			}
			return $this->yui_results($result_data);
		}


		public function view()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}
			$this->edit($mode = 'view');
		}

		public function add()
		{
			$this->edit();
		}

		public function edit($mode = 'edit')
		{
			$id 	= phpgw::get_var('id', 'int');

			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uicondition_survey.view', 'id'=> $id));
			}

			if($mode == 'view')
			{
				if( !$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if(!$this->acl_add && !$this->acl_edit)
				{
					$this->bocommon->no_access();
					return;
				}
			}

			$values = array();

			phpgwapi_yui::tabview_setup('survey_edit_tabview');
			$tabs = array();
			$tabs['generic']	= array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';
			$tabs['documents']	= array('label' => lang('documents'), 'link' => null);
			$tabs['import']	= array('label' => lang('import'), 'link' => null);

				$tabs['documents']['link'] = '#documents';
				$tabs['import']['link'] = '#import';

			if ($id)
			{

				$values = $this->so->read_single( array('id' => $id,  'view' => $mode == 'view') );
			}


			$categories = $this->_get_categories($survey['category']);

			$bolocation	= CreateObject('property.bolocation');
			$location_data = $bolocation->initiate_ui_location(array
				(
					'values'	=> $values['location_data'],
					'type_id'	=> 2,
					'no_link'	=> $_no_link, // disable lookup links for location type less than type_id
					'lookup_type'	=> $mode == 'edit' ? 'form' : 'view',
					'tenant'	=> false,
					'lookup_entity'	=> array(),
					'entity_data'	=> isset($values['p'])?$values['p']:''
				));


			$data = array
			(
				'survey'			=> $values,
				'categories'		=> array('options' => $categories),
				'status_list'		=> array('options' => execMethod('property.bogeneric.get_list',array('type' => 'condition_survey_status', 'selected' => $values['status_id'], 'add_empty' => true))),
				'editable' 			=> $mode == 'edit',
				'tabs'				=> phpgwapi_yui::tabview_generate($tabs, $active_tab),
				'location_data'		=> $location_data,
			);

			$GLOBALS['phpgw']->jqcal->add_listener('report_date');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('condition survey');
			
			phpgwapi_jquery::load_widget('core');
			self::add_javascript('property', 'portico', 'condition_survey_edit.js');
			self::add_javascript('phpgwapi', 'yui3', 'yui/yui-min.js');
			self::add_javascript('phpgwapi', 'yui3', 'gallery-formvalidator/gallery-formvalidator-min.js');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yui3/gallery-formvalidator/validatorCss.css');
			self::render_template_xsl(array('condition_survey'), $data);
		}
		
		public function save()
		{
			$id = phpgw::get_var('id');
			
			if ($id )
			{
				$values = $this->bo->read_single(array('id' => $id));
			}
			else
			{
				$values = array();
			}
			
			/*
			* Overrides with incoming data from POST
			*/
			$values = $this->_populate($values);
			
			if( !$this->receipt['error'] )
			{
				try
				{
					$id = $this->bo->save($values);
				}

				catch(Exception $e)
				{
					if ( $e )
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error'); 
						$this->edit( $values );
						return;
					}
				}

				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uicondition_survey.view', 'id' => $id));	
			}
			else
			{
				$this->edit( $values );
			}
		}

		public function get_vendors()
		{
			$query = phpgw::get_var('query');

			$sogeneric = CreateObject('property.sogeneric', 'vendor');
			$values = $sogeneric->read(array('query' => $query));
			foreach ($values as &$entry)
			{
				$entry['name'] = $entry['org_name'];
			}
			return array('ResultSet'=> array('Result'=>$values));
		}


		/*
		* Overrides with incoming data from POST
		*/
		private function _populate($data = array())
		{
			$insert_record = phpgwapi_cache::session_get('property', 'insert_record');
			$values = $this->bocommon->collect_locationdata($data,$insert_record);
			
			/*
			* Extra data from custom fields
			*/
			$values['attributes']	= phpgw::get_var('values_attribute');

			if(is_array($values['attributes']))
			{
				foreach ($values['attributes'] as $attribute )
				{
					if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
					{
						$this->receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
					}
				}
			}

			if(!isset($values['cat_id']) || !$values['cat_id'])
			{
				$receipt['error'][]=array('msg'=>lang('Please select a category !'));
			}

			if(isset($values['title']) || !$values['title'])
			{
				$receipt['error'][]=array('msg'=>lang('Please give a title !'));
			}

			if(!isset($values['report_date']) || !$values['report_date'])
			{
				$receipt['error'][]=array('msg'=>lang('Please select a date!'));
			}


			phpgwapi_cache::message_set('test-message', 'error'); 

			return $values;
		}

		private function _get_categories($selected = 0)
		{
			$cats	= CreateObject('phpgwapi.categories', -1, 'property', $this->acl_location);
			$cats->supress_info	= true;
			$categories = $cats->formatted_xslt_list(array('format'=>'filter','selected' => $selected,'globals' => true,'use_acl' => $this->_category_acl));
			$default_value = array ('cat_id'=>'','name'=> lang('no category'));
			array_unshift ($categories['cat_list'],$default_value);

			foreach ($categories['cat_list'] as & $_category)
			{
				$_category['id'] = $_category['cat_id'];
			}
			
			return $categories['cat_list'];
		}
	}
