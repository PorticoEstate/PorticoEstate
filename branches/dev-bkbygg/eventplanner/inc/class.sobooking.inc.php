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
	 * @subpackage booking
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.socommon');
	include_class('eventplanner', 'booking', 'inc/model/');

	class eventplanner_sobooking extends phpgwapi_socommon
	{

		protected static $so;

		public function __construct()
		{
			parent::__construct('eventplanner_booking', eventplanner_booking::get_fields());
			$this->acl_location = eventplanner_booking::acl_location;
			$this->use_acl = true;
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
				self::$so = CreateObject('eventplanner.sobooking');
			}
			return self::$so;
		}

		function _get_conditions( $query, $filters )
		{
			$clauses = parent::_get_conditions($query, $filters);

			$clauses .= ' AND customer_id IS NOT NULL';

			return $clauses;

		}
		function get_acl_condition( )
		{
			if($this->relaxe_acl)
			{
				return;
			}

			$acl_condition = parent::get_acl_condition();

			$sql = "SELECT object_id, permission FROM eventplanner_permission WHERE subject_id = {$this->account}";
			$this->db->query($sql,__LINE__,__FILE__);
			$object_ids = array(-1);
			while ($this->db->next_record())
			{
				$permission = $this->db->f('permission');
				if($permission & PHPGW_ACL_READ)
				{
					$object_ids[] = $this->db->f('object_id');
				}
			}

			if($acl_condition)
			{
				return '(' . $acl_condition . ' OR eventplanner_booking.customer_id IN (' . implode(',', $object_ids) . '))';
			}
			else
			{
				return 'eventplanner_booking.customer_id IN (' . implode(',', $object_ids) . ')';
			}

		}

		protected function populate( array $data )
		{
			$object = new eventplanner_booking();
			foreach ($this->fields as $field => $field_info)
			{
				$object->set_field($field, $data[$field]);
			}

			return $object;
		}

		protected function update( $object )
		{
			$this->db->transaction_begin();
			$this->update_history($object, $this->fields);

			parent::update($object);

			return	$this->db->transaction_commit();
		}

		protected function update_history( $object, $fields )
		{
			$original = $this->read_single($object->get_id());//returned as array()
			foreach ($fields as $field => $params)
			{
				$new_value = $object->$field;
				$old_value = $original[$field];
				if (!empty($params['history']) && $new_value && $old_value && ($new_value != $old_value))
				{
					$label = !empty($params['label']) ? lang($params['label']) : $field;
					$value_set = array
					(
						'booking_id'	=> $object->get_id(),
						'time'		=> time(),
						'author'	=> $GLOBALS['phpgw_info']['user']['fullname'],
						'comment'	=> $label . ':: ' . lang('old value') . ': ' . $this->db->db_addslashes($old_value) . ', ' .lang('new value') . ': ' . $this->db->db_addslashes($new_value),
						'type'	=> 'history',
					);

					$this->db->query( 'INSERT INTO eventplanner_booking_comment (' .  implode( ',', array_keys( $value_set ) )   . ') VALUES ('
					. $this->db->validate_insert( array_values( $value_set ) ) . ')',__LINE__,__FILE__);
				}

			}
		}

		public function get_booking_id_from_calendar( $calendar_id )
		{
			$sql = "SELECT id FROM eventplanner_booking WHERE calendar_id = " . (int) $calendar_id;
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return (int) $this->db->f('id');
		}
	}