<?php

	/**
	 * phpGroupWare - logistic: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
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
	 * @subpackage logistic
	 * @version $Id: class.uiactivity.inc.php 10101 2012-10-03 09:46:51Z vator $
	 */
	phpgw::import_class('phpgwapi.uicommon');
//	phpgw::import_class('logistic.sobooking');

	
	class logistic_uibooking extends phpgwapi_uicommon
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

		private $so;
		public $public_functions = array(
			'query' => true,
			'add' 	=> true,
			'edit' => true,
			'view' => true,
			'index' => true
		);

		public function __construct()
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

//			$this->so = createObject('logistic.sobooking');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "logistic::project::booking";
		}
		
		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			self::add_javascript('phpgwapi', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$user_array = $this->get_user_array();

			$data = array(
				'datatable_name'	=> lang('booking'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'project',
								'text' => lang('Project') . ':',
								'list' => $project_array,
							),
							array('type' => 'filter',
								'name' => 'user',
								'text' => lang('Responsible user') . ':',
								'list' => $user_array,
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
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'logistic.uibooking.index', 'phpgw_return_as' => 'json')),
					'field' => array(
					array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
						),		
						array(
							'key' => 'location_code',
							'label' => lang('Location code'),
							'sortable' => true
						),
						array(
							'key' => 'address',
							'label' => lang('Address'),
							'sortable' => false
						)
					)
				),
			);

			self::render_template_xsl(array('datatable_common'), $data);
		}

		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'query' => phpgw::get_var('query'),
				'sort' => phpgw::get_var('sort'),
				'dir' => phpgw::get_var('dir'),
				'filters' => $filters
			);
			
		  $entity_id			= phpgw::get_var('entity_id', 'int');
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$district_id		= phpgw::get_var('district_id', 'int');
			$part_of_town_id	= phpgw::get_var('part_of_town_id', 'int');
			$control_id			= phpgw::get_var('control_id', 'int');
			$results 			= phpgw::get_var('results', 'int');
			
/*
 			if(!$entity_id && !$cat_id)
			{
				$values = array();
			}
			else
			{
			*/
			$entity_id = 3;
			$cat_id = 1;
				$location_id = 2295;//$GLOBALS['phpgw']->locations->get_id('property', ".entity.{$entity_id}.{$cat_id}");
				//$boentity	= CreateObject('property.boentity',false, 'entity');
				$boentity	= CreateObject('property.boentity',false, 'entity', $entity_id, $cat_id);
				$boentity->results = $results;
				//$values = $boentity->read(array('control_registered' => $control_registered, 'control_id' => $control_id));
				$values = $boentity->read();
			//}		

			foreach($values as &$entry)
			{
				$checked = '';
				if($this->so_control->check_control_component($control_id,$location_id,$entry['id']))
				{
					$checked =  'checked = "checked" disabled = "disabled"';
					$entry['delete'] = "<input class =\"mychecks_delete\" type =\"checkbox\" name=\"values[delete][]\" value=\"{$control_id}_{$location_id}_{$entry['id']}\">";
				}
				$entry['select'] = "<input class =\"mychecks_add\" type =\"checkbox\" $checked name=\"values[register_component][]\" value=\"{$control_id}_{$location_id}_{$entry['id']}\">";
			}

			$results = $results ? $results : $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			$result_data = array('results' => $values);

			$result_data['total_records'] = $boentity->total_records;
			$result_data['start'] = $params['start'];
			$result_data['sort'] = $params['sort'];
			$result_data['dir'] = $params['dir'];
			
			return $this->yui_results($result_data);
		}

		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uibooking.edit'));
		}

		public function edit()
		{
			$activity_id = phpgw::get_var('id');
			$parent_activity_id = phpgw::get_var('parent_id');
			
			if ($activity_id && is_numeric($activity_id))
			{
				$activity = $this->so->get_single($activity_id);
			}
			else
			{
				$activity = new logistic_activity();
			}

			if(phpgw::get_var('project_id') && phpgw::get_var('project_id') > 0)
			{
				$activity->set_project_id(phpgw::get_var('project_id'));
			}
			
			if($parent_activity_id > 0)
			{
				$activity->set_parent_id( $parent_activity_id );
				$parent_activity = $this->so->get_single( $parent_activity_id );
				$activity->set_project_id( $parent_activity->get_project_id() );
			}

			if (isset($_POST['save_activity']))
			{
				$user_id = $GLOBALS['phpgw_info']['user']['id'];
				$activity->set_id( phpgw::get_var('id') );
				$activity->set_name( phpgw::get_var('name') );
				$activity->set_update_user( $user_id );
				$activity->set_responsible_user_id( phpgw::get_var('responsible_user_id') );
				$activity->set_description( phpgw::get_var('description') );

				if(phpgw::get_var('start_date','string') != '')
				{
					$start_date_ts = phpgwapi_datetime::date_to_timestamp( phpgw::get_var('start_date','string') );
					$activity->set_start_date($start_date_ts);
				}
				else
				{
					$activity->set_start_date(0);
				}

				if( phpgw::get_var('end_date','string') != '')
				{
					$end_date_ts = phpgwapi_datetime::date_to_timestamp( phpgw::get_var('end_date','string') );
					$activity->set_end_date($end_date_ts);
				}
				else
				{
					$activity->set_end_date(0);
				}

				$activity_id = $this->so->store($activity);

				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.view', 'id' => $activity_id, 'project_id' => $activity->get_project_id()));
			}
			else if (isset($_POST['cancel_activity']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.view', 'id' => $activity_id));
			}
			else
			{
				$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_READ, 'run', 'logistic');
				
			  $activities = $this->so->get();
				$activities_array = $this->convert_to_array( $activities );
				
				$data = array
				(
					'responsible_users' => $accounts,
					'activities' => $activities_array,
					'activity' => $activity->toArray(),
					'editable' => true,
				);
				
				if($parent_activity_id > 0)
				{
					$data['parent_activity'] = $parent_activity->toArray();
				}

				$this->use_yui_editor('description');
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Add activity');

				$GLOBALS['phpgw']->jqcal->add_listener('start_date');
				$GLOBALS['phpgw']->jqcal->add_listener('end_date');

				self::add_javascript('logistic', 'logistic', 'ajax.js');
				self::render_template_xsl(array('activity_item'), $data);
			}
		}
		
		function convert_to_array($object_list)
		{
			$converted_array = array();
			
			foreach($object_list as $object)
			{
				$converted_array[] = $object->toArray();
			}
			
			return $converted_array; 
		}

		public function view()
		{
			$activity_id = phpgw::get_var('id');
			$project_id = phpgw::get_var('project_id');
			if (isset($_POST['edit_activity']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.edit', 'id' => $activity_id, 'project_id' => $project_id));
			}
			else if (isset($_POST['new_activity']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.edit', 'project_id' => $project_id));
			}
			else
			{
				if ($activity_id && is_numeric($activity_id))
				{
					$activity = $this->so->get_single($activity_id);
				}

				$activity_array = $activity->toArray();

				if ($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

				$activity->set_project_id($project_id);

				$data = array
					(
						'activity' => $activity->toArray(),
						'img_go_home' => 'rental/templates/base/images/32x32/actions/go-home.png',
						'dateformat' 				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project');
				self::render_template_xsl(array('activity_item'), $data);
			}
		}

		private function get_user_array()
		{
			$user_array = array();
			$user_array[] = array(
				'id' => '',
				'name' => lang('all_types')
			);
			$user_array[] = array(
				'id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'name' => lang('my_activities'),
				'selected' => 1
			);

			return $user_array;
		}
	}
