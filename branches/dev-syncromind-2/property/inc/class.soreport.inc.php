<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @version $Id: class.soreport.inc.php 14913 2016-04-27 12:27:37Z sigurdne $
	 */

	class property_soreport 
	{

		function __construct()
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->join = & $this->db->join;
			$this->left_join = & $this->db->left_join;
			$this->like = & $this->db->like;		
			$this->total_records = 0;
		}

		public function read($data)
		{
			return array();
		}
		
		public function get_views()
		{
			$sql = "SELECT table_name as name
					FROM information_schema.tables
					WHERE table_schema = current_schema()
					AND table_type = 'VIEW'";
	
			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			$values = array();

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'name' => $this->db->f('name')
				);
			}
			
			return $values;
		}
		
	}