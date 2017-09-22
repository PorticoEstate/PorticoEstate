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
	 * @subpackage vendor_report
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.socommon');

	class eventplanner_sovendor_report extends phpgwapi_socommon
	{

		protected static $so;

		public function __construct()
		{
			parent::__construct('eventplanner_booking_vendor_report', eventplanner_vendor_report::get_fields());
			$this->acl_location = eventplanner_vendor_report::acl_location;
			$this->cats = CreateObject('phpgwapi.categories', -1, 'eventplanner', $this->acl_location);
			$this->cats->supress_info = true;
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
				self::$so = CreateObject('eventplanner.sovendor_report');
			}
			return self::$so;
		}

		function get_acl_condition( )
		{
			$clause = '';

			if(!$this->relaxe_acl && ($this->use_acl && $this->currentapp && $this->acl_location))
			{
				$paranthesis = false;

				$grants = $this->acl->get_grants2($this->currentapp, $this->acl_location);
				$public_user_list = array();
				if (is_array($grants['accounts']) && $grants['accounts'])
				{
					foreach($grants['accounts'] as $user => $_right)
					{
						$public_user_list[] = $user;
					}
					unset($user);
					reset($public_user_list);
					$clause .= "({$this->table_name}.owner_id IN(" . implode(',', $public_user_list) . ")";
					$paranthesis = true;
				}

				$public_group_list = array();
				if (is_array($grants['groups']) && $grants['groups'])
				{
					foreach($grants['groups'] as $user => $_right)
					{
						$public_group_list[] = $user;
					}
					unset($user);
					reset($public_group_list);
					$where = $public_user_list ? 'OR' : 'AND';
					if(!$paranthesis)
					{
						$clause .='(';
					}
					$clause .= " $where phpgw_group_map.group_id IN(" . implode(',', $public_group_list) . ")";

					$paranthesis = true;
				}

				if($this->currentapp == 'eventplannerfrontend')
				{

					$org_id = $this->db->db_addslashes(phpgw::get_var('org_id','string' , 'SESSION', -1));


					$sql = "SELECT eventplanner_booking.id as booking_id"
						. " FROM eventplanner_booking"
						. " JOIN eventplanner_calendar ON eventplanner_booking.calendar_id = eventplanner_calendar.id"
						. " JOIN eventplanner_application ON eventplanner_calendar.application_id = eventplanner_application.id"
						. " JOIN eventplanner_vendor ON eventplanner_application.vendor_id = eventplanner_vendor.id"
						. " WHERE organization_number = '{$org_id}'";

						$bookings = array(-1);
						$this->db->query($sql);
						while ($this->db->next_record())
						{
							$bookings[] = $this->db->f('booking_id');
						}

					$where = $clause ? 'OR' : 'AND';
					$clause .= " {$where} eventplanner_booking_vendor_report.booking_id IN (" . implode(',', $bookings) . ')';
				}

				if($paranthesis)
				{
					$clause .=')';
				}
			}

			return $clause;

		}

		protected function populate( array $data )
		{
			$object = new eventplanner_vendor_report();
			foreach ($this->fields as $field => $field_info)
			{
				$object->set_field($field, $data[$field]);
			}

			return $object;
		}

		function get_category_name( $cat_id )
		{
			static $category_name = array();

			if (!isset($category_name[$cat_id]))
			{
				$category = $this->cats->return_single($cat_id);
				$category_name[$cat_id] = $category[0]['name'];
			}
			return $category_name[$cat_id];
		}

		protected function update( $object )
		{
			$this->db->transaction_begin();
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

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
						case 'date_start':
						case 'date_end':
							$old_value = $GLOBALS['phpgw']->common->show_date($old_value, $dateformat);
							$new_value = $GLOBALS['phpgw']->common->show_date($new_value, $dateformat);

							break;
						case 'case_officer_id':
							$old_value = $old_value ? $GLOBALS['phpgw']->accounts->get($old_value)->__toString() : '';
							$new_value = $new_value ? $GLOBALS['phpgw']->accounts->get($new_value)->__toString() : '';
							break;
						case 'category_id':
							$old_value = $old_value ? $this->get_category_name($old_value) : '';
							$new_value = $new_value ? $this->get_category_name($new_value) : '';
							break;
						default:
							break;
					}
					$value_set = array
					(
						'vendor_report_id'	=> $object->get_id(),
						'time'		=> time(),
						'author'	=> $GLOBALS['phpgw_info']['user']['fullname'],
						'comment'	=> $label . ':: ' . lang('old value') . ': ' . $this->db->db_addslashes($old_value) . ', ' .lang('new value') . ': ' . $this->db->db_addslashes($new_value),
						'type'	=> 'history',
					);

					$this->db->query( 'INSERT INTO eventplanner_vendor_report_comment (' .  implode( ',', array_keys( $value_set ) )   . ') VALUES ('
					. $this->db->validate_insert( array_values( $value_set ) ) . ')',__LINE__,__FILE__);
				}

			}

			parent::update($object);

			return	$this->db->transaction_commit();
		}

	}