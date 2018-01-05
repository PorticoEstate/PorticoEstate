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
	 * @version $Id: class.boreport.inc.php 14913 2016-04-27 12:27:37Z sigurdne $
	 */

	class property_boreport
	{
		private $so;
		var $public_functions = array
			(
			'read' => true,
			'get_views' => true
		);
		
		public function __construct()
		{
			$this->so = CreateObject('property.soreport');
			$this->bocommon = CreateObject('property.bocommon');
			$this->operators = $this->so->operators;
			
			$this->operators_equal = $this->so->operators_equal;
			$this->operators_like = $this->so->operators_like;
			$this->operators_in = $this->so->operators_in;
			$this->operators_null = $this->so->operators_null;			
		}

		function read_single( $id = '' )
		{
			$report = $this->so->read_single($id);

			return $report;
		}
		
		public function read($data = array())
		{			
			$values =  $this->so->read($data);
			$this->total_records_reports = $this->so->total_records_reports;
			
			return $values;
		}

		public function read_to_export($id, $data = array())
		{			
			$values =  $this->so->read_to_export($id, $data);
			
			return $values;
		}
		
		function get_views()
		{
			$values = $this->so->get_views();

			return $values;
		}
		
		function get_datasets()
		{
			$values = $this->so->get_datasets();

			return $values;
		}
		
		function get_view_columns($id)
		{
			$values = $this->so->get_view_columns($id);

			return $values;
		}

		function get_view_content($id)
		{
			$values = $this->so->get_view_content($id);

			return $values;
		}
		
		function read_single_dataset( $dataset_id = '' )
		{
			$dataset = $this->so->read_single_dataset($dataset_id);

			return $dataset;
		}
		
		function read_dataset( $params = array() )
		{
			$dataset = $this->so->read_dataset($params);
			$this->total_records_dataset = $this->so->total_records_dataset;

			return $dataset;
		}
		
		function save( $values )
		{
			if ($values['id'])
			{
				$receipt = $this->so->update($values);
			}
			else
			{
				$receipt = $this->so->add($values);
			}
			
			return $receipt;
		}
		
		function delete( $id )
		{
			$receipt = $this->so->delete($id);
			return $receipt;
		}
		
		function save_dataset( $values )
		{
			if ($values['id'])
			{
				$receipt = $this->so->update_dataset($values);
			}
			else
			{
				$receipt = $this->so->add_dataset($values);
			}
			
			return $receipt;
		}
		
		function delete_dataset( $id )
		{
			$receipt = $this->so->delete_dataset($id);
			return $receipt;
		}
		
	}