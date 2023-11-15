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
	include_class('booking', 'article_mapping', 'inc/model/');

	class booking_soarticle_mapping extends phpgwapi_socommon
	{

	//	protected static $so;
	//	public $acl_location;

		public function __construct()
		{
			parent::__construct('bb_article_mapping', booking_article_mapping::get_fields());
			$this->acl_location	 = booking_article_mapping::acl_location;
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
				self::$so = CreateObject('booking.soarticle_mapping');
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
			  return '(' . $acl_condition . ' OR bb_article_mapping.id IN (' . implode(',', $object_ids) . '))';
			  }
			  else
			  {
			  return 'bb_article_mapping.id IN (' . implode(',', $object_ids) . ')';
			  }
			 */
		}

		protected function populate( array $data )
		{
			$object = new booking_article_mapping();
			foreach ($this->fields as $field => $field_info)
			{
				$object->set_field($field, $data[$field]);
			}

			return $object;
		}

		protected function update( $object )
		{
			$this->db->transaction_begin();

			$this->set_prizing_defaults($object->get_id());
			$this->set_prizing($object->get_id());
			parent::update($object);

			return $this->db->transaction_commit();
		}

		private function set_prizing_defaults($article_mapping_id )
		{
			$price_table = phpgw::get_var('price_table', 'int');

			if(!empty($price_table['active']))
			{
				$sql = 'UPDATE bb_article_price SET active = NULL WHERE article_mapping_id = ' . (int) $article_mapping_id;
				$sql .= ' AND id NOT IN (' . implode(',', array_keys($price_table['active'])) . ')';
				$this->db->query($sql, __LINE__, __FILE__);

				$sql = 'UPDATE bb_article_price SET active = 1 WHERE article_mapping_id = ' . (int) $article_mapping_id;
				$sql .= ' AND id IN (' . implode(',', array_keys($price_table['active'])) . ')';
				$this->db->query($sql, __LINE__, __FILE__);
			}

			if(!empty($price_table['default_']))
			{
				$sql = 'UPDATE bb_article_price SET default_ = NULL WHERE article_mapping_id = ' . (int) $article_mapping_id;
				$this->db->query($sql, __LINE__, __FILE__);

				$sql = 'UPDATE bb_article_price SET default_ = 1 WHERE id = ' . (int) $price_table['default_'];
				$this->db->query($sql, __LINE__, __FILE__);
			}

			if(!empty($price_table['delete']))
			{
				$sql = 'DELETE FROM bb_article_price WHERE id IN (' . implode(',', $price_table['delete']) . ')';
				$this->db->query($sql, __LINE__, __FILE__);
			}

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

			if (($price || $price == 0) && $date_from)
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

		public function get_pricing( $article_mapping_id, $filter_active = false )
		{
			$pricing = array();

			$sql = 'SELECT *  FROM bb_article_price WHERE article_mapping_id = ' . (int)$article_mapping_id;
			if($filter_active)
			{
				$sql .= ' AND active = 1';
			}

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$pricing[] = array(
					'id'				 => $this->db->f('id'),
					'article_mapping_id' => $this->db->f('article_mapping_id'),
					'price'				 => $this->db->f('price'),
					'from_'				 => $this->db->f('from_'),
					'remark'			 => $this->db->f('remark', true),
					'active'			 => $this->db->f('active'),
					'default_'			 => $this->db->f('default_'),
				);
			}
			return $pricing;
		}

		/**
		 * FIXME: group by relevant current price...
		 * @param array $article_mapping_ids
		 * @return type
		 */
		public function get_current_pricing(array $article_mapping_ids )
		{

			$now = date('Y-m-d');
			$pricing = array();

			if(empty($article_mapping_ids))
			{
				return array();
			}
			/**
			 * Dummy - in case price is not set;
			 */
			$sql = "SELECT bb_article_mapping.id, bb_article_mapping.tax_code, fm_ecomva.percent_ FROM bb_article_mapping"
				. " JOIN fm_ecomva ON bb_article_mapping.tax_code = fm_ecomva.id"
				. " WHERE bb_article_mapping.id IN ( " . implode (',',$article_mapping_ids) . ")";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$article_mapping_id = $this->db->f('id');
				$pricing[$article_mapping_id] = array(
					'price'				 => 0,
					'remark'			 => 'Price not set',
					'tax_code'			 => $this->db->f('tax_code'),
					'percent'			 => (int)$this->db->f('percent_')
				);
			}

			$sql = "SELECT id, article_mapping_id FROM bb_article_price"
				. " WHERE article_mapping_id IN ( " . implode (',',$article_mapping_ids) . ")"
				. " AND from_ < '{$now}'"
				. " ORDER BY default_ ASC, from_ DESC";


			$this->db->query($sql, __LINE__, __FILE__);
			$mapping_ids = array();
			$pricing_ids = array(-1);
			while ($this->db->next_record())
			{
				$article_mapping_id = $this->db->f('article_mapping_id');

				if(in_array($article_mapping_id, $mapping_ids))
				{
					continue;
				}
				$mapping_ids[] = $article_mapping_id;
				$pricing_ids[]	= (int) $this->db->f('id');
			}

			$this->db->query("SELECT bb_article_price.* , bb_article_mapping.tax_code, fm_ecomva.percent_"
				. " FROM bb_article_mapping"
				. " JOIN bb_article_price ON bb_article_price.article_mapping_id = bb_article_mapping.id"
				. " JOIN fm_ecomva ON bb_article_mapping.tax_code = fm_ecomva.id"
				. " WHERE bb_article_price.id IN ( " . implode (',',$pricing_ids) . ")", __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$article_mapping_id = $this->db->f('article_mapping_id');
				$pricing[$article_mapping_id] = array(
					'id'				 => $this->db->f('id'),
					'article_mapping_id' => $article_mapping_id,
					'price'				 => (float)$this->db->f('price'),
					'from_'				 => $this->db->f('from_'),
					'remark'			 => $this->db->f('remark', true),
					'tax_code'			 => $this->db->f('tax_code'),
					'percent'			 => (int)$this->db->f('percent_')
				);
			}
			return $pricing;
		}

		function get_mapped_services()
		{
			$services = array();
			$this->db->query('SELECT article_id AS service_id FROM bb_article_mapping WHERE article_cat_id = 2', __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$services[] = $this->db->f('service_id');
			}
			return $services;
		}

		public function get_reserved_resources( $building_id )
		{
			$resources = array();
			$sql = "SELECT article_id AS resource_id FROM bb_article_mapping"
				. " JOIN bb_building_resource ON bb_article_mapping.article_id = bb_building_resource.resource_id AND bb_article_mapping.article_cat_id = 1"
				. " WHERE bb_building_resource.building_id = " . (int) $building_id;

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$resources[] = $this->db->f('resource_id');
			}
			return $resources;
		}

		public function get_articles( $resources )
		{
			$filter = '';

			$_resources	 = array_merge(array(-1), (array)$resources);
			$filter		 = 'AND article_id IN (' . implode(',', $_resources) . ')';



			$articles	 = array();
			$_articles	 = array();
			/**
			 * Resources
			 */
			$sql = "SELECT bb_article_mapping.id AS mapping_id,"
				. " concat( article_cat_id || '_' || article_id ) AS article_id,"
				. " bb_resource.name as name ,article_id AS resource_id, unit, percent_ AS tax_percent, tax_code,"
				. " bb_article_mapping.group_id, bb_article_group.name AS article_group_name,"
				. " bb_article_group.remark AS article_group_remark"
				. " FROM bb_article_mapping"
				. " JOIN bb_resource ON (bb_article_mapping.article_id = bb_resource.id)"
				. " JOIN fm_ecomva ON (bb_article_mapping.tax_code = fm_ecomva.id)"
				. " JOIN bb_article_group ON (bb_article_mapping.group_id = bb_article_group.id)"
				. " WHERE article_cat_id = 1"
				. " AND bb_resource.active = 1 {$filter}"
				. " ORDER BY bb_resource.name";

			$this->db->query($sql, __LINE__, __FILE__);

			$found_resources = array();
			while ($this->db->next_record())
			{
				$_articles[] = array(
					'id'				 => $this->db->f('mapping_id'),
					'parent_mapping_id'	 => null,
					'resource_id'		 => $this->db->f('resource_id'),
					'article_id'		 => $this->db->f('article_id'),
					'name'				 => $this->db->f('name', true),
					'unit'				 => $this->db->f('unit', true),
					'tax_code'			 => $this->db->f('tax_code'),
					'tax_percent'		 => (int)$this->db->f('tax_percent'),
					'group_id'			 => (int)$this->db->f('group_id'),
					'article_remark'	 => '',
					'article_group_name' => $this->db->f('article_group_name', true),
					'article_group_remark' => $this->db->f('article_group_remark', true),
				);
			}

			foreach ($_articles as $_article)
			{
				$articles[]	 = $_article;
				/**
				 * Services
				 */
				$filter		 = 'AND bb_resource_service.resource_id =' . $_article['resource_id'];

				if($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
				{
					$filter .= ' AND deactivate_in_frontend IS NULL';
				}

				$sql = "SELECT bb_article_mapping.id AS mapping_id, concat( article_cat_id || '_' || article_id ) AS article_id,"
					. " bb_service.name as name, bb_service.description as article_remark, bb_resource_service.resource_id, unit, percent_ AS tax_percent,"
					. " bb_article_mapping.tax_code, bb_article_mapping.group_id, bb_article_group.name AS article_group_name,"
					. " bb_article_group.remark AS article_group_remark"
					. " FROM bb_article_mapping JOIN bb_service ON (bb_article_mapping.article_id = bb_service.id)"
					. " JOIN bb_resource_service ON (bb_service.id = bb_resource_service.service_id)"
					. " JOIN fm_ecomva ON (bb_article_mapping.tax_code = fm_ecomva.id)"
					. " JOIN bb_article_group ON (bb_article_mapping.group_id = bb_article_group.id)"
					. " WHERE article_cat_id = 2 {$filter}"
					. " ORDER BY bb_resource_service.resource_id, bb_service.name";
				$this->db->query($sql, __LINE__, __FILE__);

				while ($this->db->next_record())
				{
					$articles[] = array(
						'id'					 => $this->db->f('mapping_id'),
						'parent_mapping_id'		 => $_article['id'],
						'article_id'			 => $this->db->f('article_id'),
						'name'					 => "- " . $this->db->f('name', true),
						'unit'					 => $this->db->f('unit', true),
						'tax_code'				 => $this->db->f('tax_code'),
						'tax_percent'			 => (int)$this->db->f('tax_percent'),
						'group_id'				 => (int)$this->db->f('group_id'),
						'article_remark'		 => $this->db->f('article_remark', true),
						'article_group_name'	 => $this->db->f('article_group_name', true),
						'article_group_remark'	 => $this->db->f('article_group_remark', true),
					);
				}
			}


			foreach ($articles as &$article)
			{
				$sql = "SELECT price, remark FROM bb_article_price WHERE article_mapping_id = {$article['id']} "
				. " AND active = 1"
				. " ORDER BY default_ ASC";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$article['ex_tax_price'] = (float)$this->db->f('price');
				$article['tax']			 = $article['ex_tax_price'] * ($article['tax_percent'] / 100);
				$article['price']		 = $article['ex_tax_price'] * (1 + ($article['tax_percent'] / 100));
				$article['price_remark'] = $this->db->f('remark', true);
			}


			return $articles;
		}

		function get_building( $resource_id )
		{
			$sql = "SELECT bb_building.id, bb_building.name"
				. " FROM bb_building"
				. " JOIN bb_building_resource ON bb_building_resource.building_id = bb_building.id"
				. " WHERE bb_building_resource.resource_id =" .(int) $resource_id;
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return array(
				'id'	 => (int)$this->db->f('id'),
				'name'	 => $this->db->f('name', true)
				);
		}
	}