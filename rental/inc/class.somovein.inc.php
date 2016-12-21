<?php
	/**
	 * phpGroupWare - rental: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package rental
	 * @subpackage movein
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.socommon');

	class rental_somovein extends phpgwapi_socommon
	{

		protected static $so;

		public function __construct()
		{
			parent::__construct('rental_movein', rental_movein::get_fields());
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *
		 * @return the class instance.
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.somovein');
			}
			return self::$so;
		}

		public function read_custom_field_values( $id, $custom_fields )
		{
			$sql = "SELECT * FROM {$this->table_name} WHERE id = " . (int)$id;
			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record() && is_array($custom_fields))
			{
				foreach ($custom_fields as &$attr)
				{
					$attr['value'] = $this->db->f($attr['column_name']);
				}
			}
			return $custom_fields;
		}

		protected function populate( array $data )
		{
			$object = new rental_movein();
			foreach ($this->fields as $field => $field_info)
			{
				$object->set_field($field, $data[$field]);
			}

			return $object;
		}

		protected function update( $object )
		{
			$this->db->transaction_begin();

			parent::update($object);

			return $this->db->transaction_commit();
		}
	}