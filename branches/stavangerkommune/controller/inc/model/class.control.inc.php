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

	include_class('controller', 'model', 'inc/model/');
	phpgw::import_class('phpgwapi.datetime');

	class controller_control extends controller_model
	{
		public static $so;
		
		const REPEAT_TYPE_DAY = 0;
		const REPEAT_TYPE_WEEK = 1;
		const REPEAT_TYPE_MONTH = 2;
		const REPEAT_TYPE_YEAR = 3;
		
		protected $id;
		protected $title;
		protected $description;
		protected $start_date;
		protected $end_date;
		protected $repeat_type;
		protected $repeat_type_label;
		protected $repeat_interval;
		protected $procedure_id;
		protected $procedure_name;
		protected $enabled;
		protected $requirement_id;
		protected $costresponsibility_id;
		protected $responsibility_id;
		protected $responsibility_name;
		protected $control_area_id;
		protected $control_area_name;

		// Objects
		protected $check_lists_array = array();
		// Array that contains open cases for a month   
		protected $agg_open_cases_pr_month_array = array();
		// Array that contains error messages. Is populted in function validate
		protected $error_msg_array;
		
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int)$id;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }
		
		public function set_title($title)
		{
			$this->title = $title;
		}
		
		public function get_title() { return $this->title; }
		
		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description() { return $this->description; }
		
		public function set_end_date($end_date)
		{
			$this->end_date = $end_date;
		}
		
		public function get_end_date() { return $this->end_date; }
		
		public function set_start_date($start_date)
		{
			$this->start_date = $start_date;
		}
		
		public function get_start_date() { return $this->start_date; }
		
		public function set_repeat_type($repeat_type)
		{
			$this->repeat_type = $repeat_type;
		}
					
		public function get_repeat_type() { return $this->repeat_type; }
		
		public function set_repeat_type_label(int $repeat_type = null)
		{
			switch($repeat_type)
			{
				case controller_control::REPEAT_TYPE_DAY:
					$this->repeat_type_label = lang('repeat_type_day');
					break;
				case controller_control::REPEAT_TYPE_WEEK:
					$this->repeat_type_label = lang('repeat_type_week');
					break;
				case controller_control::REPEAT_TYPE_MONTH:
					$this->repeat_type_label = lang('repeat_type_month');
					break;
				case controller_control::REPEAT_TYPE_YEAR;
					$this->repeat_type_label = lang('repeat_type_year');
					break;
				default:
					$this->repeat_type_label = lang('repeat_type_none');
					break;
			}
		}
					
		public function get_repeat_type_label() { return $this->repeat_type_label; }
		
		public function set_repeat_interval($repeat_interval)
		{
			$this->repeat_interval = $repeat_interval;
		}
		
		public function get_repeat_interval() { return $this->repeat_interval; }
		
		public function set_procedure_id($procedure_id)
		{
			$this->procedure_id = $procedure_id;
		}
		
		public function get_procedure_id() { return $this->procedure_id; }
		
		public function set_procedure_name($procedure_name)
		{
			$this->procedure_name = $procedure_name;
		}
		
		public function get_procedure_name() { return $this->procedure_name; }
		
		public function set_enabled($enabled)
		{
			$this->enabled = $enabled;
		}
		
		public function get_enabled() { return $this->enabled; }
		
		public function set_requirement_id($requirement_id)
		{
			$this->requirement_id = $requirement_id;
		}
		
		public function get_requirement_id() { return $this->requirement_id; }
		
		public function set_costresponsibility_id($costresponsibility_id)
		{
			$this->costresponsibility_id = $costresponsibility_id;
		}
		
		public function get_costresponsibility_id() { return $this->costresponsibility_id; }
		
		public function set_responsibility_id($responsibility_id)
		{
			$this->responsibility_id = $responsibility_id;
		}
		
		public function get_responsibility_id() { return $this->responsibility_id; }
		
		public function set_responsibility_name($responsibility_name)
		{
			$this->responsibility_name = $responsibility_name;
		}
		
		public function get_responsibility_name() { return $this->responsibility_name; }
		
		public function set_control_area_id($control_area_id)
		{
			$this->control_area_id = $control_area_id;
		}
		
		public function get_control_area_id() { return $this->control_area_id; }
		
		public function set_control_area_name($control_area_name)
		{
			$this->control_area_name = $control_area_name;
		}
		
		public function get_control_area_name() { return $this->control_area_name; }
		
		public function set_check_lists_array($check_lists_array)
		{
			$this->check_lists_array = $check_lists_array;
		}
		
		public function get_check_lists_array() { return $this->check_lists_array; }
		
		public function set_agg_open_cases_pr_month_array($agg_open_cases_pr_month_array)
		{
			$this->agg_open_cases_pr_month_array = $agg_open_cases_pr_month_array;
		}
		
		public function get_agg_open_cases_pr_month_array() { return $this->agg_open_cases_pr_month_array; }
		
		public function get_error_msg_array() { return $this->error_msg_array; }
		
		public function set_error_msg_array( $error_msg_array )
		{
			$this->error_msg_array = $error_msg_array;
		}

		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('controller.socontrol');
			}
			
			return self::$so;
		}
		
		public function populate()
		{
				$this->set_title(phpgw::get_var('title','string'));
				$this->set_description(phpgw::get_var('description','html'));
				
				if(phpgw::get_var('start_date','string') != '')
				{
					$start_date_ts = phpgwapi_datetime::date_to_timestamp( phpgw::get_var('start_date','string') );
					$this->set_start_date($start_date_ts);
				}else
					$this->set_start_date(0);
								
				if( phpgw::get_var('end_date','string') != '')
				{
					$end_date_ts = phpgwapi_datetime::date_to_timestamp( phpgw::get_var('end_date','string') );
					$this->set_end_date( $end_date_ts );
				}else
				{
					$this->set_end_date( 0 );
				}
				
				$this->set_repeat_type(phpgw::get_var('repeat_type','string'));
				$this->set_repeat_interval(phpgw::get_var('repeat_interval','string'));
				$this->set_procedure_id(phpgw::get_var('procedure_id','int'));
				$this->set_control_area_id(phpgw::get_var('control_area_id','int'));
				$this->set_responsibility_id(phpgw::get_var('responsibility_id','int'));
		}
		
		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'title' => $this->get_title(),
				'description' => $this->get_description(),
				'start_date' => $this->get_start_date(),
				'end_date' => $this->get_end_date(),
				'procedure_id' => $this->get_procedure_id(),
				'procedure_name' => $this->get_procedure_name(),
				'control_area_id' => $this->get_control_area_id(),
				'control_area_name' => $this->get_control_area_name(),
			  	'repeat_type' => $this->get_repeat_type(),
				'repeat_interval' => $this->get_repeat_interval(),
				'responsibility_name' => $this->get_responsibility_name()
			);
		}
				
		public function validate()
		{
			$status = true;
	
			// Validate CONTROL AREA
			if( empty( $this->control_area_id ) && (intval($this->control_area_id) == 0) )
		  {
		  	$status = false;
		  	$this->error_msg_array['control_area_id'] = "error_msg_2";
		  }
		  
		  // Validate PROCEDURE		  		  
			if( empty( $this->procedure_id ) && (intval($this->procedure_id) == 0) )
		  {
		  	$status = false;
		  	$this->error_msg_array['procedure_id'] = "error_msg_2";
		  }
			
		  // Validate TITLE
		  if( empty($this->title) )
		  {
		  	$status = false;
		  	$this->error_msg_array['title'] = "error_msg_1";
		  }
		 		  
		  // Validate START DATE
			if( empty($this->start_date) )
		  {
		  	$status = false;
		  	$this->error_msg_array['start_date'] = "error_msg_1";
		  }

		  // Validate END DATE
			if( !empty($this->end_date) && ($this->end_date < $this->start_date) )
		  {
		   	$status = false;
		  	$this->error_msg_array['end_date'] = "error_msg_3";
		  }  
		  
		  // Validate REPEAT TYPE
		  if( $this->repeat_type == "" )
		  {
		  	$status = false;
		  	$this->error_msg_array['repeat_type'] = "error_msg_2";
		  }

		  // Validate REPEAT INTERVAL
		  if( ($this->repeat_interval == "") || (intval($this->repeat_interval) < 1) )
		  {
		  	$status = false;
		  	$this->error_msg_array['repeat_interval'] = "error_msg_1";
		  }
		  
			// Validate RESPONSIBILITY
		  if( $this->responsibility_id == "" || (!is_numeric($this->responsibility_id)) )
		  {
		  	$status = false;
		  	$this->error_msg_array['responsibility_id'] = "error_msg_2";
		  }
		  
		  return $status;
		}
	}
