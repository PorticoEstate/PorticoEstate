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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package eventplanner
	 * @subpackage permission
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.socommon');

	class eventplanner_sopermission extends phpgwapi_socommon
	{

		protected static $so;

		public function __construct()
		{
			parent::__construct('eventplanner_permission', eventplanner_permission::get_fields());
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
				self::$so = CreateObject('eventplanner.sopermission');
			}
			return self::$so;
		}


		protected function populate( array $data )
		{
			$object = new eventplanner_permission();
			foreach ($this->fields as $field => $field_info)
			{
				$object->set_field($field, $data[$field]);
			}

			return $object;
		}

		protected function update( $object )
		{
			return parent::update($object);
		}

		public function delete( $id )
		{
			return 	$this->db->query( 'DELETE FROM eventplanner_permission WHERE id=' . (int) $id ,__LINE__,__FILE__);
		}

	}