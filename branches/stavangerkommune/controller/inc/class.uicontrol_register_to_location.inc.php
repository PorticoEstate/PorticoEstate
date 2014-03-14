<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
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
	* @package property
	* @subpackage controller
 	* @version $Id: class.uicontrol_register_to_location.inc.php 11147 2013-06-04 13:37:33Z sigurdne $
	*/
	
	phpgw::import_class('phpgwapi.yui');

	/**
	* Import the jQuery class
	*/
	phpgw::import_class('phpgwapi.jquery');

	phpgw::import_class('phpgwapi.uicommon');
		
	class controller_uicontrol_register_to_location extends phpgwapi_uicommon
	{
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $type_id;
		var $location_code;
		
		private $bo;
		private $bocommon;
		private $so_control;
	
		var $public_functions = array
		(
			'index'												=> true,
			'query'												=> true,
			'edit_location'								=> true,
			'get_location_category'				=> true,
			'get_district_part_of_town'		=> true,
			'get_category_by_entity'			=> true,
			'get_entity_table_def'				=> true,
			'get_locations'								=> true,
			'get_location_type_category'	=> true
		);

		function __construct()
		{
			parent::__construct();
			
			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->so_control 			= CreateObject('controller.socontrol');
			
			$this->type_id				= $this->bo->type_id;
			
			$this->start						= $this->bo->start;
			$this->query						= $this->bo->query;
			$this->sort							= $this->bo->sort;
			$this->order						= $this->bo->order;
			$this->filter						= $this->bo->filter;
			$this->cat_id						= $this->bo->cat_id;
			$this->part_of_town_id	= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$this->status						= $this->bo->status;
			$this->allrows					= $this->bo->allrows;
			$this->lookup						= $this->bo->lookup;
			$this->location_code		= $this->bo->location_code;
			
			self::set_active_menu('controller::control::location_for_check_list');
		}	
	


		function index()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$receipt = array();

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$msgbox_data = array();
			if( phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
			{
				phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			}

			$myColumnDefs = array();
			$datavalues = array();
			$myButtons	= array();

			$datavalues[] = array
			(
				'name'				=> "0",
				'values' 			=> json_encode(array()),
				'total_records'		=> 0,
				'permission'   		=> "''",
				'is_paginator'		=> 1,
				'edit_action'		=> "''",
				'footer'			=> 0
			);

			$myColumnDefs[0] = array
			(
				'name'		=> "0",
				'values'	=>	json_encode(array())
			);	

			$GLOBALS['phpgw']->translation->add_app('property');

			$district_list  = $this->bocommon->select_district_list('filter',$this->district_id);

			$part_of_town_list = execMethod('property.bogeneric.get_list', array('type'=>'part_of_town', 'selected' => $part_of_town_id ));
			$location_type_list = execMethod('property.soadmin_location.select_location_type');

			array_unshift($district_list ,array ('id'=>'','name'=>lang('select')));
			array_unshift($part_of_town_list ,array ('id'=>'','name'=>lang('select')));
			array_unshift($location_type_list ,array ('id'=>'','name'=>lang('select')));

			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;

			$control_area = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));

								
			$control_area_list = array();
			foreach($control_area['cat_list'] as $cat_list)
			{
				$control_area_list[] = array
				(
					'id' 	=> $cat_list['cat_id'],
					'name'	=> $cat_list['name'],
				);		
			}

			array_unshift ($control_area_list ,array ('id'=>'','name'=>lang('select')));

			
					
			$data = array
			(
				'td_count'						=> '""',
				'datatable'						=> $datavalues,
				'myColumnDefs'					=> $myColumnDefs,
				'myButtons'						=> $myButtons,

				'msgbox_data'					=> $msgbox_data,
				'control_area_list'		=> array('options' => $control_area_list),
				'filter_form' 					=> array
													(
														'control_area_list'		=> array('options' => $control_area_list),
														'district_list' 		=> array('options' => $district_list),
														'part_of_town_list'		=> array('options' => $part_of_town_list),
														'location_type_list'	=> array('options' => $location_type_list),
													),
				'update_action'					=> self::link(array('menuaction' => 'controller.uicontrol_register_to_location.edit_location'))
			);

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('autocomplete');

			self::add_javascript('controller', 'controller', 'ajax_control_to_location.js');
			self::add_javascript('controller', 'yahoo', 'register_control.js');

			self::render_template_xsl(array('control_location/register_control_to_location' ), $data);
		}

		
		/*
		 * Return categories based on chosen location
		 */
		public function get_location_category()
		{
			$type_id = phpgw::get_var('type_id');
		 	$category_types = $this->bocommon->select_category_list(array(
																		'format'=>'filter',
																		'selected' => 0,
																		'type' =>'location',
																		'type_id' =>$type_id,
																		'order'=>'descr'
																	));
			$default_value = array ('id'=>'','name'=>lang('no category selected'));
			array_unshift($category_types,$default_value);
			return json_encode( $category_types );
		}
		
		/*
		 * Return parts of town based on chosen district
		 */
		public function get_district_part_of_town()
		{
			$district_id = phpgw::get_var('district_id');
			$part_of_town_list =  $this->bocommon->select_part_of_town('filter',null,$district_id);
			$default_value = array ('id'=>'','name'=>lang('no part of town'));
			array_unshift($part_of_town_list,$default_value);

			return json_encode( $part_of_town_list );
		}


		/*

		 * Return parts of town based on chosen district
		 */
		public function get_category_by_entity()
		{
			$entity_id		= phpgw::get_var('entity_id');
			$entity			= CreateObject('property.soadmin_entity');

			$category_list = $entity->read_category(array('allrows'=>true,'entity_id'=>$entity_id));

			return $category_list;
		}


		public function get_location_type_category()
		{
			$location_type			= phpgw::get_var('location_type', 'int');

			$values  = $this->bocommon->select_category_list(array
					(
						'format'=>'filter',
					//	'selected' => $this->cat_id,
						'type' =>'location',
						'type_id' =>$location_type,
						'order'=>'descr'
					)
				);

			return $values;
		}


		public function get_entity_table_def()
		{

			$location_level		= phpgw::get_var('location_level', 'int', 'REQUEST', 1);
			$solocation	= CreateObject('property.solocation');
			$solocation->read(array('dry_run' => true, 'type_id' =>$location_level));
			$uicols = $solocation->uicols;

			$columndef = array();

			/*This one has to defined - chokes otherwise*/
			$columndef[] = array
			(
				'key'		=> 'id',
				'label'		=> '',
				'sortable'	=> false,
				'formatter'	=> false,
				'hidden'	=> true,
				'className' => false
			);

			$columndef[] = array
			(
				'key'		=> 'select',
				'label'		=> lang('select'),
				'sortable'	=> false,
				'formatter'	=> false,
				'hidden'	=> false,
				'className' => ''
			);

			$columndef[] = array
			(
				'key'		=> 'delete',
				'label'		=> lang('delete'),
				'sortable'	=> false,
				'formatter'	=> false,
				'hidden'	=> false,
				'className' => ''
			);

			$count_fields = count($uicols['name']);

			for ($i=0;$i<$count_fields;$i++)
			{
				switch($uicols['datatype'][$i])
				{
					case 'link':
						$formatter = 'link';
						break;
					default:
						$formatter = $uicols['formatter'][$i];
				}
				
				if( $uicols['name'][$i])
				{
					$columndef[] = array
					(
						'key'		=> $uicols['name'][$i],
						'label'		=> $uicols['descr'][$i],
						'sortable'	=> !!$uicols['sortable'][$i],
						'formatter'	=> $formatter,
						'hidden'	=> $uicols['input_type'][$i] == 'hidden' ? true : false	,		
						'className'	=> $uicols['classname'][$i],
					);
				}
			}


//_debug_array($columndef);
			return $columndef;
		}


		public function get_locations()
		{
			$location_code = phpgw::get_var('location_code');
			$child_level = phpgw::get_var('child_level', 'int', 'REQUEST', 1);
			$part_of_town_id = phpgw::get_var('part_of_town_id', 'int');

			$criteria = array
			(
				'location_code'		=> $location_code,
				'child_level'		=> $child_level,
				'field_name'		=> "loc{$child_level}_name",
				'part_of_town_id'	=> $part_of_town_id
			);
	
			$locations = execMethod('property.solocation.get_children',$criteria);
			return $locations;
		}


		public function query()
		{
			$type_id = phpgw::get_var('location_level', 'int', 'REQUEST', 1);
			$district_id		= phpgw::get_var('district_id', 'int');
			$part_of_town_id	= phpgw::get_var('part_of_town_id', 'int');
			$control_id			= phpgw::get_var('control_id', 'int');
			$results 			= phpgw::get_var('results', 'int');
			$control_registered	= phpgw::get_var('control_registered', 'bool');

			$results = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : $results;

			$this->bo->results = $results;			
            $this->bo->sort =  'ASC';
            $this->bo->order =  'location_code';
            $this->bo->start =  phpgw::get_var('startIndex', 'int', 'REQUEST', 0);

			$values = $this->bo->read(array('control_registered' => $control_registered,
					 'control_id' => $control_id,
					 'type_id'=>$type_id,
					 'allrows'=>$this->allrows,
					 'results' => $results
					)
				);


			if($control_id)
			{
				foreach($values as &$entry)
				{
					$checked = '';
					if( $this->so_control->get_control_location($control_id, $entry['location_code']) )
					{
						$checked =  'checked = "checked" disabled = "disabled"';
						$entry['delete'] = "<input class =\"mychecks_delete\" type =\"checkbox\" name=\"values[delete][]\" value=\"{$control_id}_{$entry['location_code']}\">";
					}
					$entry['select'] = "<input class =\"mychecks_add\" type =\"checkbox\" $checked name=\"values[register_location][]\" value=\"{$control_id}_{$entry['location_code']}\">";
				}
			}
			
			$data = array(
				 'ResultSet' => array(
					'totalResultsAvailable' => $this->bo->total_records,
					'startIndex' => $this->bo->start, 
					'sortKey' => 'location_code', 
					'sortDir' => "ASC", 
					'Result' => $values,
					'pageSize' => $results,
					'activePage' => floor($this->bo->start / $results) + 1
				)
			);

			return $data;
		}

		public function edit_location()
		{
			if($values = phpgw::get_var('values'))
			{
				if(!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'property'))
				{
					$receipt['error'][]=true;
					phpgwapi_cache::message_set(lang('you are not approved for this task'), 'error');
				}
				if(!$receipt['error'])
				{

					if($this->so_control->register_control_to_location($values))
					{
						$result =  array
						(
							'status'	=> 'updated'
						);
					}
					else
					{
						$result =  array
						(
							'status'	=> 'error'
						);
					}
				}
			}

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				if( $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
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
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_register_to_location.index'));
			}
		}
	}
