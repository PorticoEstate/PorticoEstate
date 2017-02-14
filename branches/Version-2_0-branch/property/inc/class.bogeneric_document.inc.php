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
	 * @version $Id: class.bogeneric_document.inc.php 14913 2016-04-27 12:27:37Z sigurdne $
	 */

	class property_bogeneric_document
	{
		private $so;
		var $public_functions = array
			(
			'read' => true,
			'read_single' => true,
			'save' => true,
			'delete' => true,
			'get_file_relations' => true,
			'get_file_relations_componentes' => true
		);
		
		public function __construct()
		{
			$this->so = CreateObject('property.sogeneric_document');
			$this->bocommon = CreateObject('property.bocommon');
		}

		public function read($data = array())
		{
			
			$values =  $this->so->read($data);
			$this->total_records = $this->so->total_records;
			
			return $values;
		}
		
		public function read_single( $file_id )
		{
			$values = $this->so->read_single($file_id);

			return json_decode($values);
		}

		function get_file_relations( $file_id, $location_id )
		{
			$values = $this->so->get_file_relations($file_id, $location_id);

			return $values;
		}
		
		function get_file_relations_componentes( $data )
		{
			$values = $this->so->get_file_relations_componentes($data);
			$this->total_records_componentes = $this->so->total_records_componentes;

			return $values;
		}
		
		function save( $values = array(), $file_id )
		{
			$report_date = phpgwapi_datetime::date_array($values['report_date']);
			$values['report_date'] = mktime(2, 0, 0, $report_date['month'], $report_date['day'], $report_date['year']);
			
			$result = $this->so->read_single( $file_id );

			if (count($result))
			{
				$receipt = $this->so->update($values, $file_id);
			}
			else
			{
				$receipt = $this->so->add($values, $file_id);
			}
			
			return $receipt;
		}
		
		function save_file_relations( $add, $delete, $location_id, $file_id  )
		{
			
			$receipt = $this->so->save_file_relations( $add, $delete, $location_id, $file_id );
			
			return $receipt;
		}
		
		function delete( $file_id )
		{		
			$receipt = $this->so->delete( $file_id );
			
			return $receipt;
		}
		
	}