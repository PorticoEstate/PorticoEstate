<?php
	/**
	* phpGroupWare - registration
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
	* @package registration
 	* @version $Id: class.uicheck_list.inc.php 8628 2012-01-21 10:42:05Z vator $
	*/

	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('registration.uicommon');
//	phpgw::import_class('registration.socontrol_area');

/*
	include_class('registration', 'check_list', 'inc/model/');
	include_class('registration', 'date_generator', 'inc/component/');
	include_class('registration', 'status_checker', 'inc/helper/');
	include_class('registration', 'date_helper', 'inc/helper/');
*/	
	class registration_uipending extends registration_uicommon
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
		private $so_check_list;
		private $so_control_item;
		private $so_check_item;
		private $so_procedure;

		var $public_functions = array
		(
			'index'								=> true,
			'query'								=> true,
			'view_locations_for_control' 		=> true,
			'register_control_to_location'		=> true,
			'register_control_to_location_2'	=> true,
			'get_locations_for_control'			=> true,
			'get_location_category'				=> true,
			'get_district_part_of_town'			=> true
		);

		function __construct()
		{
			parent::__construct();
		
			$this->bo					= CreateObject('registration.bopending',true);
			$this->bocommon				= & $this->bo->bocommon;

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
		
			self::set_active_menu('registration::pending');
		}

		function index()
		{
			if(phpgw::get_var('save_location'))
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
				//$location_code = phpgw::get_var('location_code');
			
				$control_location  = null;
				$control_location_id = 0;
			
				foreach($items_checked as $location_code)
				{
					$control_location = $this->so_control->get_control_location($control_id, $location_code[0]);
				
					if($control_location == null )
					{				
						$control_location_id = $this->so_control->register_control_to_location($control_id, $location_code[0]);
					}
				}
			
/*				if($control_location_id > 0)
					return json_encode( array( "status" => "saved" ) );
				else
					return json_encode( array( "status" => "not_saved" ) );
*/
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'registration.uipending.index'));

			}
			else
			{
				if(phpgw::get_var('phpgw_return_as') == 'json')
				{
					return $this->query();
				}

				$status_list = array
				(
					array
					(
						'id'	=> 0,
						'name'	=> lang('Select status')
					),
					array
					(
						'id'	=> 1,
						'name'	=> lang('approved')
					),
					array
					(
						'id'	=> 2,
						'name'	=> lang('pending')
					),
				);
		
				$data = array(
					'filter_form' 				=> array(
						'status_list' 			=> array('options' => $status_list)
					),
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'registration.uipending.query', 'phpgw_return_as' => 'json')),
						'field' => array(
							array(
								'key' => 'reg_id',
								'label' => lang('id'),
								'sortable'	=> true,
								'formatter' => 'formatLinkPending'
							),
							array(
								'key'	=>	'reg_lid',
								'label'	=>	lang('user'),
								'sortable'	=>	true
							),
							array(
								'key' => 'reg_dla',
								'label' => lang('time'),
								'sortable'	=> true
							),
							array(
								'key' => 'reg_approved',
								'label' => lang('approved'),
								'sortable'	=> true
							),
							array(
									'key' => 'checked',
									'label' => lang('approve'),
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
							),array(
								'key' => 'parameters',
								'hidden' => true
							)					
						)
					)
				);
			
				phpgwapi_yui::load_widget('paginator');

				self::add_javascript('registration', 'yahoo', 'pending.index.js');
//				self::add_javascript('registration', 'registration', 'jquery.js');
//				self::add_javascript('registration', 'registration', 'ajax.js');

				self::render_template_xsl(array('pending_users', 'common'), $data);
			}	
		}
	
		// Returns locations for a control
		public function get_locations_for_control()
		{
			$control_id = phpgw::get_var('control_id');
		
			if(is_numeric($control_id) & $control_id > 0)
			{
				$locations_for_control_array = $this->so_control->get_locations_for_control($control_id);
		
				foreach($locations_for_control_array as $location)
				{
					$results['results'][]= $location;
				}
			
				$results['total_records'] = count( $locations_for_control_array );
				$results['start'] = 1;
				$results['sort'] = 'location_code';
			}
			else
			{
				$results['total_records'] = 0;
			}			
		
			return $this->yui_results($results);
		}
	
		public function query()
		{
			$status_id = phpgw::get_var('status_id');

			$this->bo->sort = "ASC";
			$this->bo->start = phpgw::get_var('startIndex');
		
			$user_list = $this->bo->read(array('user_id' => $user_id, 'role_id' =>$role_id, 'type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,
												   'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run));
		
			foreach($user_list as $user)
			{
				$results['results'][]= $user;
			}
		
			$results['total_records'] = $this->bo->total_records;
			$results['start'] = $this->start;
			$results['sort'] = 'location_code';
			$results['dir'] = "ASC";
					
			array_walk($results['results'], array($this, 'add_links'), array($type));
						
			return $this->yui_results($results);
		}

		public function register_control_to_location_2()
		{
			$control_id = phpgw::get_var('control_id');
			$location_code = phpgw::get_var('location_code');
		
			$control_location  = null;
			$control_location_id = 0;
		
			$control_location = $this->so_control->get_control_location($control_id, $location_code);
		
			if($control_location == null ){
			
				$control_location_id = $this->so_control->register_control_to_location($control_id, $location_code);
			}
		
			if($control_location_id > 0)
				return json_encode( array( "status" => "saved" ) );
			else
				return json_encode( array( "status" => "not_saved" ) );
		}
	
		public function add_actions(&$value, $key, $params)
		{
			unset($value['query_location']);
		
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();
			$value['parameters'] = array();
/*		
			$value['ajax'][] = true;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'registration.uipending.register_control_to_location_2','location_code' => $value['location_code'], 'phpgw_return_as' => 'json')));
			$value['labels'][] = lang('add_location');
			$value['parameters'][] = "control_id";
			*/
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
	}
