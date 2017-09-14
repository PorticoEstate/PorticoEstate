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
	 * @subpackage vendor
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.socommon');

	class eventplanner_sovendor extends phpgwapi_socommon
	{

		protected static $so;

		public function __construct()
		{
			parent::__construct('eventplanner_vendor', eventplanner_vendor::get_fields());
			$this->acl_location = eventplanner_vendor::acl_location;
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
				self::$so = CreateObject('eventplanner.sovendor');
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
					$where = $clause ? 'OR' : 'AND';
					$org_id = phpgw::get_var('org_id','string' , 'SESSION', -1);
					$clause .= " {$where} eventplanner_vendor.organization_number = '{$org_id}'";
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
			$object = new eventplanner_vendor();
			foreach ($this->fields as $field => $field_info)
			{
				$object->set_field($field, $data[$field]);
			}

			return $object;
		}

		protected function update( $object )
		{
			$this->db->transaction_begin();
	//		$status_text = eventplanner_vendor::get_status_list();
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
						'vendor_id'	=> $object->get_id(),
						'time'		=> time(),
						'author'	=> $GLOBALS['phpgw_info']['user']['fullname'],
						'comment'	=> $label . ':: ' . lang('old value') . ': ' . $this->db->db_addslashes($old_value) . ', ' .lang('new value') . ': ' . $this->db->db_addslashes($new_value),
						'type'	=> 'history',
					);

					$this->db->query( 'INSERT INTO eventplanner_vendor_comment (' .  implode( ',', array_keys( $value_set ) )   . ') VALUES ('
					. $this->db->validate_insert( array_values( $value_set ) ) . ')',__LINE__,__FILE__);
				}

			}

			parent::update($object);

			return	$this->db->transaction_commit();
		}

		function check_duplicate_organization($organization_number = false, $vendor_id = 0)
		{
			if(!$organization_number)
			{
				return false;
			}

			$query = $this->db->db_addslashes($organization_number);
			$sql = "SELECT name FROM eventplanner_vendor WHERE organization_number = '{$query}'";
			if($vendor_id)
			{
				$sql .= ' AND id !=' . (int)$vendor_id;
			}

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();	
			return $this->db->f('name', true);
		}
	}