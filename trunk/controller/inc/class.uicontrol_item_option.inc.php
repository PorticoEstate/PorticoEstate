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
 	* @version $Id: class.uicontrol_item.inc.php 9082 2012-03-29 12:58:24Z vator $
	*/

	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('controller.socontrol_item_option');
		
	include_class('controller', 'control_item_option', 'inc/model/');

	class controller_uicontrol_item_option extends phpgwapi_uicommon
	{
		private $so;
		private $so_control_item;
	
		public $public_functions = array
		(
			'add'		=> true,
			'edit'		=> true,
			'save'		=> true,
			'delete'	=> true,
			'query'		=> true
		);

		public function __construct()
		{ 
			parent::__construct();
			$this->so = CreateObject('controller.socontrol_item_option');
			$this->so_control_item = CreateObject('controller.socontrol_item');
		}

		public function add()
		{
			$control_item_id = phpgw::get_var('control_item_id');
			
			$control_item = $this->so_control_item->get_single($control_item_id);	
			
			$data = array
			(
				'control_item'	=> $control_item->toArray()
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');

			self::render_template_xsl('control_item/control_item_option', $data);
		}
		
		public function save()
		{	
			$option_value = phpgw::get_var('option_value');
			$control_item_id = phpgw::get_var('control_item_id');
			
			$control_item_option = new controller_control_item_option($option_value, $control_item_id);
			$control_item_option_id = $this->so->store( $control_item_option );
			
			if($control_item_option_id > 0){
				$control_item_option = $this->so->get_single($control_item_option_id);	 
				
				return json_encode( array( "status" => "saved", "saved_object" => $control_item_option->toArray() ) );
			}
			else{
				return json_encode( array("status" => "not_saved") );
			}
		}

		public function edit()
		{
			$control_item_option_id = phpgw::get_var('id');
			$label = phpgw::get_var('label');
			$control_item_id = phpgw::get_var('control_item_id');
			
			$control_item_option = new controller_control_item_option($label, $control_item_id);
			$control_item_option_id = $this->so->store( $control_item_option );
		}
		
		public function query(){}
	}
