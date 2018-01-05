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
	 * @subpackage customer
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.socommon');
	include_class('eventplanner', 'customer', 'inc/model/');

	class eventplanner_socustomer extends phpgwapi_socommon
	{

		protected static $so;

		public function __construct()
		{
			parent::__construct('eventplanner_customer', eventplanner_customer::get_fields());
			$this->acl_location = eventplanner_customer::acl_location;
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
				self::$so = CreateObject('eventplanner.socustomer');
			}
			return self::$so;
		}

		function get_acl_condition( )
		{
			$acl_condition = parent::get_acl_condition();

			if($this->relaxe_acl)
			{
				return $acl_condition;
			}
			
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
				return '(' . $acl_condition . ' OR eventplanner_customer.id IN (' . implode(',', $object_ids) . '))';
			}
			else
			{
				return 'eventplanner_customer.id IN (' . implode(',', $object_ids) . ')';	
			}

		}

		protected function populate( array $data )
		{
			$object = new eventplanner_customer();
			foreach ($this->fields as $field => $field_info)
			{
				$object->set_field($field, $data[$field]);
			}

			return $object;
		}

		protected function update( $object )
		{
			$this->db->transaction_begin();
	//		$status_text = eventplanner_customer::get_status_list();
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$lang_active = lang('active');
			$lang_inactive = lang('inactive');

			$original = $this->read_single($object->get_id());//returned as array()
			foreach ($this->fields as $field => $params)
			{
				$new_value = $object->$field;
				$old_value = $original[$field];
				if (!empty($params['history']) && ($new_value != $old_value))
				{
					$label = !empty($params['label']) ? lang($params['label']) : $field;
					switch ($field)
					{
						case 'status':
							$old_value = $status_text[$old_value];
							$new_value = $status_text[$new_value];
							break;
						case 'active':
							$old_value = $old_value ? $lang_active : $lang_inactive;
							$new_value = $new_value ? $lang_active : $lang_inactive;
							break;
						default:
							break;
					}
					$value_set = array
					(
						'customer_id'	=> $object->get_id(),
						'time'		=> time(),
						'author'	=> $GLOBALS['phpgw_info']['user']['fullname'],
						'comment'	=> $label . ':: ' . lang('old value') . ': ' . $this->db->db_addslashes($old_value) . ', ' .lang('new value') . ': ' . $this->db->db_addslashes($new_value),
						'type'	=> 'history',
					);

					$this->db->query( 'INSERT INTO eventplanner_customer_comment (' .  implode( ',', array_keys( $value_set ) )   . ') VALUES ('
					. $this->db->validate_insert( array_values( $value_set ) ) . ')',__LINE__,__FILE__);
				}

			}

			parent::update($object);

			return	$this->db->transaction_commit();
		}

	}