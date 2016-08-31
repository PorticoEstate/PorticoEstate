<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
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
	 * @subpackage application
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.socommon');

	class rental_soapplication extends phpgwapi_socommon
	{

		protected static $so;

		public function __construct()
		{
			parent::__construct('rental_application', rental_application::get_fields());
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
				self::$so = CreateObject('rental.soapplication');
			}
			return self::$so;
		}


		protected function populate( array $data )
		{
			$object = new rental_application();
			foreach ($this->fields as $field => $field_info)
			{
				$object->set_field($field, $data[$field]);
			}

			return $object;
		}

		protected function update( $object )
		{
			$this->db->transaction_begin();

			$original = $this->read_single($object->get_id());//returned as array()
			foreach ($this->fields as $field => $params)
			{
				$new_value = $object->$field;
				$old_value = $original[$field];
				$label = !empty($params['label']) ? lang($params['label']) : $field;
				if (!empty($params['history']) && ($new_value != $old_value))
				{
					$value_set = array
					(
						'application_id'	=> $object->get_id(),
						'time'		=> time(),
						'author'	=> $GLOBALS['phpgw_info']['user']['fullname'],
						'comment'	=> $label . ':: ' . lang('old value') . ': ' . $this->db->db_addslashes($old_value) . ', ' .lang('new value') . ': ' . $this->db->db_addslashes($new_value),
						'type'	=> 'history',
					);

					$this->db->query( 'INSERT INTO rental_application_comment (' .  implode( ',', array_keys( $value_set ) )   . ') VALUES ('
					. $this->db->validate_insert( array_values( $value_set ) ) . ')',__LINE__,__FILE__);
				}

			}

			parent::update($object);
	
			return	$this->db->transaction_commit();


		}

	}