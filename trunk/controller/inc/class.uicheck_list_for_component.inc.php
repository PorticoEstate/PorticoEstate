<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
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

	/**
	* Import the jQuery class
	*/
	phpgw::import_class('phpgwapi.jquery');

	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.uicommon');
	//phpgw::import_class('bim.sobimitem');

	class controller_uicheck_list_for_component extends phpgwapi_uicommon
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
		private $so_control; 
		private $so_bim;

		var $public_functions = array(
										'index' => true,
										'add_component_to_control' => true,
										'get_component_types_by_category' => true
									);

		function __construct()
		{
			parent::__construct();

			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->so_control 			= CreateObject('controller.socontrol');

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

			self::set_active_menu('controller::control::component_for_check_list');
		}

		function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			$bim_types = $this->so_control->get_bim_types();

			$control_areas_array = $this->so_control_area->get_control_areas_as_array();
			$controls_array = $this->so_control->get_controls_by_control_area($control_areas_array[0]['id']);
			$control_id = $control_areas_array[0]['id'];

			if($control_id == null)
				$control_id = 0;

			$tabs = array( array(
						'label' => lang('View_component_for_control')
					), array(
						'label' => lang('Add_component_for_control'),
						'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list_for_component.add_component_to_control'))
					));

			$data = array(
				'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
				'view'					=> "view_component_for_control",
				'control_area_array' 	=> $control_areas_array,
				'control_array'			=> $control_array,
				'locations_table' => array(
					'source' => self::link(array('menuaction' => 'controller.uicheck_list_for_component.index','phpgw_return_as' => 'json')),
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
			phpgwapi_jquery::load_widget('core');

			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'ajax.js');

			self::render_template_xsl(array('control_component_tabs', 'common', 'view_component_for_control'), $data);
		}

		function add_component_to_control()
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

				$control_id = phpgw::get_var('control_id');
				//var_dump($control_id);
				if($control_id != null && is_numeric($control_id))
				{
					//add chosen component to control
					foreach($items_checked as $it)
					{
						$this->so_control->add_component_to_control($control_id, $it[0]);
					}
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicheck_list_for_component.index'));

			}
			else
			{
				if(phpgw::get_var('phpgw_return_as') == 'json') {
					return $this->get_component();
				}

				$bim_types = $this->so_control->get_bim_types();

				$control_areas_array = $this->so_control_area->get_control_areas_as_array();

				$tabs = array( array(
							'label' => lang('View_component_for_control'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list_for_component.index'))

						), array(
							'label' => lang('Add_component_for_control')
						));

				$data = array(
					'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
					'view'						=> "add_component_to_control",
					'control_filters'			=> array(
						'control_area_array' 		=> $control_areas_array,
						'control_array' 			=> $control_array
					),
					'filter_form' 				=> array(
						'bim_types' 			=> $bim_types
					),
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'controller.uicheck_list_for_component.add_component_to_control', 'phpgw_return_as' => 'json')),
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
				phpgwapi_jquery::load_widget('core');

				self::add_javascript('controller', 'yahoo', 'control_tabs.js');
				self::add_javascript('controller', 'controller', 'ajax.js');

				self::render_template_xsl(array('control_component_tabs', 'common', 'add_component_to_control'), $data);
			}
		}

		public function query()
		{
			$control_list = $this->so_control->get_control_component();

			foreach($control_list as $control)
			{
				$control['bim_name'] = $this->so_control->getBimItemAttributeValue($control['bim_item_guid'], 'description');
				$results['results'][]= $control;
			}

			$results['total_records'] = 10;
			$results['start'] = 1;
			$results['sort'] = 'id';
			array_walk($results['results'], array($this, 'add_links'), array($type));

			return $this->yui_results($results);
		}

		public function get_component()
		{

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
