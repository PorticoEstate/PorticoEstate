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

	phpgw::import_class('property.solocation');

	class location_finder
	{
		private $so;
		
		public function __construct()
		{
			$this->so = CreateObject('property.solocation');
		}

		function get_responsibilities($data = array())
		{
			$data['filter_role_on_contact'] = $GLOBALS['phpgw']->accounts->get($data['user_id'])->person_id;
			$locations = $this->so->read($data);
			
			$total_records = $this->so->total_records;
			
			return $locations;
		}
    
    function get_buildings_on_property($user_role, $location_code, $level)
		{

			// Property level
			if ($level == 1)
			{
				$property_location_code = $location_code;
			}
			// Building level
			else if ($level > 1)
			{
				$split_loc_code_array = explode('-', $location_code);
				$property_location_code = $split_loc_code_array[0];
			}

			if ($user_role)
			{
				$criteria = array();
				$criteria['location_code'] = $property_location_code;
				$criteria['field_name'] = 'loc2_name';
				$criteria['child_level'] = '2';

				$buildings_on_property = execMethod('property.solocation.get_children', $criteria);
			}
			else
			{
				$buildings_on_property = execMethod('property.solocation.get_children', $property_location_code);
			}

			return $buildings_on_property;
		}
	}
