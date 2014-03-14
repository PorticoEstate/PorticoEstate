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
 	* @version $Id: class.control_item_option.inc.php 10810 2013-02-13 19:49:14Z sigurdne $
	*/

	include_class('controller', 'model', 'inc/model/');
	
	class controller_control_item_option extends controller_model
	{
		public static $so;
		
		protected $id;
		protected $option_value;
		protected $control_item_id;
		
		public function __construct($option_value, $control_item_id)
		{
			$this->option_value = $option_value;
			$this->control_item_id = $control_item_id;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }

		public function set_option_value($option_value)
		{
			$this->option_value = $option_value;
		}
		
		public function get_option_value() { return $this->option_value; }
		
		public function set_control_item_id($control_item_id)
		{
			$this->control_item_id = $control_item_id;
		}
		
		public function get_control_item_id() { return $this->control_item_id; }
}
