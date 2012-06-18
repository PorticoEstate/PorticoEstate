<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
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
	* @package property
	* @subpackage controller
 	* @version $Id$
	*/	


	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('controller.socontrol_area');
	//phpgw::import_class('bim.sobimitem');

	class controller_uicontrol_group_component extends phpgwapi_uicommon
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

		private $so_control_area;
		private $so_control_group;
		private $so_control;  
		private $so_bim;

		var $public_functions = array(
										'index' => true,
										'add_component_to_control_group' => true,
										'get_component_types_by_category' => true
									);

		function __construct()
		{
			parent::__construct();

			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->so_control_area 		= CreateObject('controller.socontrol_area');
			$this->so_control_group		= CreateObject('controller.socontrol_group');
			$this->so_control			= CreateObject('controller.socontrol');
			//$this->so_bim				= CreateObject('bim.sobimitem_impl');
			//$this->so_bim				= new sobimitem_impl();

			$this->type_id				= $this->bo->type_id;

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$this->status				= $this->bo->status;
			$this->allrows				= $this->bo->allrows;
			$this->lookup				= $this->bo->lookup;
			$this->location_code		= $this->bo->location_code;

			self::set_active_menu('controller::control_group::component_for_control_group');
		}

		function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			$bim_types = $this->so_control->get_bim_types();

			// Sigurd: START as categories
			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;

			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => '','globals' => true,'use_acl' => $this->_category_acl));
			array_unshift($control_areas['cat_list'],array ('cat_id'=>'','name'=> lang('select value')));
			$control_areas_array = array();
			foreach($control_areas['cat_list'] as $cat_list)
			{
				$control_areas_array[] = array
				(
					'id' 	=> $cat_list['cat_id'],
					'name'	=> $cat_list['name'],
				);		
			}
			// END as categories
			
			$control_groups_array = $this->so_control_group->get_control_groups_by_control_area($control_areas_array[1]['id']);
			//_debug_array($control_groups_array);
			//$control_id = $control_groups_array[0]['id'];
			array_unshift($control_groups_array,array ('id'=>'','group_name'=> lang('select value')));

//			if($control_id == null)
//				$control_id = 0;

			$tabs = array( array(
						'label' => lang('View_component_for_control_group')
					), array(
						'label' => lang('Add_component_for_control_group'),
						'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_group_component.add_component_to_control_group'))
					));

			$data = array(
				'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
				'view'					=> "view_component_for_control_group",
				'control_area_array' 	=> $control_areas_array,
				'control_group_array'	=> $control_groups_array,
				'locations_table' => array(
					'source' => self::link(array('menuaction' => 'controller.uicontrol_group_component.index','phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ControlId'),
							'sortable'	=> true,
						),
						array(
							'key'	=>	'title',
							'label'	=>	lang('Title'),
							'sortable'	=>	false
						),
						array(
							'key' => 'bim_id',
							'label' => lang('Bim_id'),
							'sortable'	=> false
						),
						array(
							'key' => 'bim_name',
							'label' => lang('Bim_name'),
							'sortable'	=> false
						),
						array(
							'key' => 'bim_type',
							'label' => lang('Bim_type'),
							'sortable'	=> false
						)
					)
				)
			);


			phpgwapi_yui::load_widget('paginator');

			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');

			self::render_template_xsl(array('control_group_component_tabs', 'common', 'view_component_for_control_group'), $data);
		}

		function add_component_to_control_group()
		{
			if(phpgw::get_var('save_component'))
			{
				//add component to control using component item ID
				$items_checked = array();
				$items = phpgw::get_var('values_assign');
				$item_arr = explode('|',$items);
				foreach($item_arr as $item)
				{
					$items_checked[] = explode(';',$item);
				}
				//var_dump($items_checked);

				$control_group_id = phpgw::get_var('control_group_id');
				//var_dump($control_id);
				if($control_group_id != null && is_numeric($control_group_id))
				{
					//add chosen component to control
					foreach($items_checked as $it)
					{
						$this->so_control_group->add_component_to_control_group($control_group_id, $it[0]);
					}
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_group_component.index'));

			}
			else
			{
				if(phpgw::get_var('phpgw_return_as') == 'json') {
					return $this->get_component();
				}

				$bim_types = $this->so_control->get_bim_types();

				// Sigurd: START as categories
				$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info	= true;
	
				$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => '','globals' => true,'use_acl' => $this->_category_acl));
				array_unshift($control_areas['cat_list'],array ('cat_id'=>'','name'=> lang('select value')));
				$control_areas_array = array();
				foreach($control_areas['cat_list'] as $cat_list)
				{
					$control_areas_array[] = array
					(
						'id' 	=> $cat_list['cat_id'],
						'name'	=> $cat_list['name'],
					);		
				}
				// END as categories

				$tabs = array( array(
							'label' => lang('View_component_for_control_group'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_group_component.index'))

						), array(
							'label' => lang('Add_component_for_control_group')
						));

				$data = array(
					'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
					'view'						=> "add_component_to_control_group",
					'control_group_filters'		=> array(
					'control_area_array' 		=> $control_areas_array,
					'control_group_array'		=> $control_group_array
					),
					'filter_form' 				=> array(
						'bim_types' 			=> $bim_types
					),
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'controller.uicontrol_group_component.add_component_to_control_group', 'phpgw_return_as' => 'json')),
						'field' => array(
							array(
								'key' => 'id',
								'label' => lang('ID'),
								'sortable'	=> true,
								'formatter' => 'YAHOO.portico.formatLink'
							),
							array(
								'key'	=>	'guid',
								'label'	=>	lang('GUID'),
								'sortable'	=>	false
							),
							array(
								'key' => 'type',
								'label' => lang('type'),
								'sortable'	=> false
							),
							array(
								'key' => 'checked',
								'label' => 'Velg',
								'sortable' => false,
								'formatter' => 'YAHOO.widget.DataTable.formatCheckbox',
								'className' => 'mychecks'
							),
							array(
								'key' => 'actions',
								'hidden' => true
							),
							array(
								'key' => 'labels',
								'hidden' => true
							),
							array(
								'key' => 'ajax',
								'hidden' => true
							)
						)
					)
				);


				phpgwapi_yui::load_widget('paginator');

				self::add_javascript('controller', 'yahoo', 'control_tabs.js');
				self::add_javascript('controller', 'controller', 'jquery.js');
				self::add_javascript('controller', 'controller', 'ajax.js');

				self::render_template_xsl(array('control_group_component_tabs', 'common', 'add_component_to_control_group'), $data);
			}
		}

		public function query()
		{
			$control_group_list = $this->so_control_group->get_control_group_component();

			foreach($control_group_list as $control_group)
			{
				$control_group['bim_name'] = $this->so_control->getBimItemAttributeValue($control['bim_item_guid'], 'description');
				$results['results'][]= $control_group;
			}

			$results['total_records'] = 10;
			$results['start'] = 1;
			$results['sort'] = 'id';
			array_walk($results['results'], array($this, 'add_links'), array($type));

			return $this->yui_results($results);
		}

		public function get_component()
		{

			/*$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$filter					= phpgw::get_var('filter', 'int');
			$cat_id					= phpgw::get_var('cat_id');
			$lookup_tenant			= phpgw::get_var('lookup_tenant', 'bool');
			$district_id			= phpgw::get_var('district_id', 'int');
			$part_of_town_id		= phpgw::get_var('part_of_town_id', 'int');
			$status					= phpgw::get_var('status');
			$type_id				= phpgw::get_var('type_id', 'int');
			$allrows				= phpgw::get_var('allrows', 'bool');
			$location_code			= phpgw::get_var('location_code');*/

			$control_group_id = phpgw::get_var('control_group_id');
			//var_dump($control_group_id);
			
			$type_id = phpgw::get_var('bim_type_id');

			$start = phpgw::get_var('startIndex');

			$component_list = array();

			$sort = "ASC";

			$component_list = $this->so_control->getAllBimItems(10,$type_id);
			//var_dump($component_list); 


			$results = array();
			foreach($component_list as $component)
			{
				$component['checked'] = false;
				$results['results'][]= $component;
				$i++;
			}

			$results['total_records'] = count($component_list);
			$results['start'] = $start;
			$results['sort'] = 'id';
			$results['dir'] = "ASC";

			array_walk($results['results'], array($this, 'add_links'), array($type));

			return $this->yui_results($results);
		}

		public function add_actions(&$value, $key, $params)
		{
			unset($value['query_location']);

			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uilocation.view', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('show');

			$value['ajax'][] = true;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.add_unit', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('add_location');
		}

		public function get_component_types_by_category()
		{
			$category = phpgw::get_var('ifc');
			if($ifc != null)
			{
				if($ifc = 1)
					$ifc = true;
				else
					$ifc = false;
			}


			$bim_types = $this->so_control->get_bim_types($ifc);
			if(count($bim_types)>0)
				return json_encode( $bim_types );
			else
				return null;
		}
	}
