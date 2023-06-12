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
	 * @package booking
	 * @subpackage article
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.socommon');
	include_class('booking', 'service', 'inc/model/');

	class booking_soservice extends phpgwapi_socommon
	{

		protected static $so;
		var $acl_location;

		public function __construct()
		{
			parent::__construct('bb_service', booking_service::get_fields());
			$this->acl_location	 = booking_service::acl_location;
			$this->use_acl		 = true;
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
				self::$so = CreateObject('booking.soservice');
			}
			return self::$so;
		}

		function get_acl_condition()
		{
			$acl_condition = parent::get_acl_condition();

			if ($this->relaxe_acl)
			{
				return $acl_condition;
			}
			/*
			  $sql = "SELECT object_id, permission FROM booking_permission WHERE subject_id = {$this->account}";
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
			  return '(' . $acl_condition . ' OR bb_service.id IN (' . implode(',', $object_ids) . '))';
			  }
			  else
			  {
			  return 'bb_service.id IN (' . implode(',', $object_ids) . ')';
			  }
			 */
		}

		protected function populate( array $data )
		{
			$object = new booking_service();
			foreach ($this->fields as $field => $field_info)
			{
				$object->set_field($field, $data[$field]);
			}

			return $object;
		}

		protected function update( $object )
		{
			$this->db->transaction_begin();

			$this->set_prizing($object->get_id());

			parent::update($object);

			return $this->db->transaction_commit();
		}

		private function set_prizing( $article_mapping_id )
		{
			$article_prizing = phpgw::get_var('article_prizing');

			if (empty($article_prizing['date_from']))
			{
				return;
			}

			$date_from	 = phpgwapi_datetime::date_to_timestamp($article_prizing['date_from']);
			$price		 = floatval(str_replace(',', '.', str_replace('.', '', $article_prizing['price'])));

			if ($price && $date_from)
			{
				$value_set = array
					(
					'article_mapping_id' => $article_mapping_id,
					'price'				 => $price,
					'from_'				 => date('Y-m-d', $date_from),
					'remark'			 => $article_prizing['remark'],
				);

				$this->db->query('INSERT INTO bb_article_price (' . implode(',', array_keys($value_set)) . ') VALUES ('
					. $this->db->validate_insert(array_values($value_set)) . ')', __LINE__, __FILE__);
			}
		}

		public function get_pricing( $id )
		{
			$pricing = array();
			$this->db->query('SELECT *  FROM bb_article_price WHERE article_mapping_id = ' . (int)$id, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$pricing[] = array(
					'id'		 => $this->db->f('id'),
					'article_id' => $this->db->f('article_id'),
					'price'		 => $this->db->f('price'),
					'from_'		 => $this->db->f('from_'),
					'remark'	 => $this->db->f('remark', true),
				);
			}
			return $pricing;
		}

		function get_mapped_services()
		{
			$services = array();
			$this->db->query('SELECT article_id AS service_id FROM bb_service WHERE article_cat_id = 2', __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$services[] = $this->db->f('service_id');
			}
			return $services;
		}

		public function get_reserved_resources( $building_id )
		{
			$resources = array();
			$this->db->query('SELECT article_id AS resource_id FROM bb_service WHERE article_cat_id = 1', __LINE__, __FILE__);

			//join...

			while ($this->db->next_record())
			{
				$resources[] = $this->db->f('resource_id');
			}
			return $resources;
		}

		/**
		 * Get resources enabled for the service
		 */
		public function get_mapping( $service_id )
		{
			$sql		 = "SELECT resource_id FROM bb_resource_service WHERE service_id = ?";
			$condition	 = array((int)$service_id);

			$this->db->select($sql, $condition, __LINE__, __FILE__);

			$mapping = array();
			while ($this->db->next_record())
			{
				$mapping[] = $this->db->f('resource_id');
			}
			return $mapping;
		}

		/**
		 * Set enabled resourses for a given service
		 */
		public function set_mapping( $service_id, $selected_resources )
		{
			$this->db->transaction_begin();
			$delete_sql	 = "DELETE FROM bb_resource_service WHERE service_id = ?";
			$delete		 = array();
			$delete[]	 = array
				(
				1 => array
					(
					'value'	 => $service_id,
					'type'	 => PDO::PARAM_INT
				)
			);

			$this->db->delete($delete_sql, $delete, __LINE__, __FILE__);

			if ($selected_resources)
			{
				$add_sql = "INSERT INTO bb_resource_service (service_id, resource_id)"
					. " VALUES (?, ?)";

				$insert_update = array();
				foreach ($selected_resources as $resource_id)
				{
					if ($resource_id > 0)
					{
						$insert_update[] = array
							(
							1	 => array
								(
								'value'	 => $service_id,
								'type'	 => PDO::PARAM_INT
							),
							2	 => array
								(
								'value'	 => $resource_id,
								'type'	 => PDO::PARAM_INT
							),
						);
					}
				}
				$this->db->insert($add_sql, $insert_update, __LINE__, __FILE__);
			}
			return $this->db->transaction_commit();
		}
	}