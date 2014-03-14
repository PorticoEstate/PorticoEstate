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
 	* @version $Id: class.check_list_status_manager.inc.php 10810 2013-02-13 19:49:14Z sigurdne $
	*/

	include_class('controller', 'check_list_status_info', 'inc/component/');

	class check_list_status_manager
	{		
		private $check_list;
		private $type;
						
		public function __construct($check_list, $type) {
			$this->check_list 		= $check_list;
    	$this->type 					= $type;
		}
		
		function get_status_for_check_list() {
			$check_list_status_info = new check_list_status_info();
				
			$todays_date_ts = $this->get_todays_date_ts();
			
			if( $this->check_list->get_id() == 0){
				
				if($this->check_list->get_deadline() < $todays_date_ts){
	        $status = "CONTROL_NOT_DONE";
	      }else{
	        $status = "CONTROL_REGISTERED";
	      }
			}else {
				if($this->check_list->get_status() == controller_check_list::STATUS_NOT_DONE & $this->check_list->get_planned_date() > 0 & $this->check_list->get_deadline() >= $todays_date_ts)
				{
					$status = "CONTROL_PLANNED";
				}
        else if( $this->check_list->get_status() == controller_check_list::STATUS_NOT_DONE & $this->check_list->get_deadline() >= $todays_date_ts )
				{
					$status = "CONTROL_REGISTERED";
				}
				else if( $this->check_list->get_status() == controller_check_list::STATUS_NOT_DONE & $this->check_list->get_planned_date() > 0 & $this->check_list->get_deadline() < $todays_date_ts )
				{
					$status = "CONTROL_NOT_DONE_WITH_PLANNED_DATE";
				}
				else if( $this->check_list->get_status() == controller_check_list::STATUS_DONE & $this->check_list->get_completed_date() > $this->check_list->get_deadline() & $this->check_list->get_num_open_cases() == 0)
				{
					$status = "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS";
				}
				else if( $this->check_list->get_status() == controller_check_list::STATUS_DONE & $this->check_list->get_completed_date() < $this->check_list->get_deadline() & $this->check_list->get_num_open_cases() == 0)
				{
					$status = "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS";
				}
				else if( $this->check_list->get_status() == controller_check_list::STATUS_DONE & $this->check_list->get_num_open_cases() > 0){
					$status = "CONTROL_DONE_WITH_ERRORS";
					
					$check_list_status_info->set_num_open_cases($this->check_list->get_num_open_cases());
				}
				else if( $this->check_list->get_status() == controller_check_list::STATUS_CANCELED)
				{
					$status = "CONTROL_CANCELED";
				}
        
				$check_list_status_info->set_check_list_id( $this->check_list->get_id() );
			}
      
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$check_list_status_info->set_control_id( $this->check_list->get_control_id() );
			$check_list_status_info->set_deadline_date_txt( date($dateformat, $this->check_list->get_deadline()) );
			$check_list_status_info->set_deadline_date_ts( $this->check_list->get_deadline() );
			$check_list_status_info->set_type( $this->type );
		      
			if($this->type == "component"){
				$check_list_status_info->set_component_id( $this->check_list->get_component_id() );
				$check_list_status_info->set_location_id( $this->check_list->get_location_id() );
			}else {
				$check_list_status_info->set_location_code( $this->check_list->get_location_code() );
			}
			
			$check_list_status_info->set_status($status);
			
			return $check_list_status_info; 
		}
		
		public static function get_todays_date_ts(){
		  $todays_date_ts = mktime(0,0,0,date("m"), date("d"), date("Y"));
		  
		  return $todays_date_ts;
	  }
	}
