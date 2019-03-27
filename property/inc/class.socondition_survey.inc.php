<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage project
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.datetime');
	phpgw::import_class('property.socommon_core');

	/**
	 * Description
	 * @package property
	 */
	class property_socondition_survey extends property_socommon_core
	{

		public function __construct()
		{
			parent::__construct();
		}

		function read( $data = array() )
		{
			$start		 = isset($data['start']) ? (int)$data['start'] : 0;
			$filter		 = isset($data['filter']) ? $data['filter'] : 'none';
			$query		 = isset($data['query']) ? $data['query'] : '';
			$sort		 = isset($data['sort']) ? $data['sort'] : '';
			$order		 = isset($data['order']) ? $data['order'] : '';
			$dir		 = isset($data['dir']) ? $data['dir'] : 'DESC';
			$cat_id		 = isset($data['cat_id']) ? (int)$data['cat_id'] : 0;
			$status_id	 = isset($data['status_id']) ? (int)$data['status_id'] : 0;
			$status_open = empty($data['status_open']) ? false : true;
			$allrows	 = isset($data['allrows']) ? $data['allrows'] : '';
			$results	 = isset($data['results']) ? (int)$data['results'] : 0;

			$table = 'fm_condition_survey';
			if ($order)
			{
				switch ($order)
				{
					case 'year':
						$sort = 'entry_date';
						break;
					default:
					//
				}

				$metadata = $this->_db->metadata($table);
				if (isset($metadata[$order]))
				{
					$ordermethod = " ORDER BY {$table}.$order $dir";
				}
			}
			else
			{
				$ordermethod = " ORDER BY {$table}.id DESC";
			}

			$where = 'WHERE';
			if ($cat_id)
			{
				$filtermethod	 .= " {$where} {$table}.category = {$cat_id}";
				$where			 = 'AND';
			}

			if ($status_id)
			{
				$filtermethod	 .= " {$where} {$table}.status_id = {$status_id}";
				$where			 = 'AND';
			}

			if ($status_open)
			{
				$filtermethod	 .= " {$where} {$table}_status.closed IS NULL";
				$where			 = 'AND';
			}
			if ($query)
			{
				$query		 = $this->_db->db_addslashes($query);
				$querymethod = " {$where} {$table}.title {$this->_like} '%{$query}%' OR {$table}.address {$this->_like} '%{$query}%'";
			}

			$groupmethod = "GROUP BY {$table}_status.descr, $table.id, $table.title, $table.descr, $table.address, $table.entry_date, $table.user_id, org_name, $table.multiplier, {$table}_status.closed";
			$sql		 = "SELECT DISTINCT $table.id, $table.title, $table.descr, $table.address, $table.entry_date, $table.user_id, $table.multiplier,"
				. " COUNT(condition_survey_id) AS cnt, org_name AS vendor, {$table}_status.descr AS status, {$table}_status.closed"
				. " FROM {$table} "
				. " {$this->_left_join} {$table}_status ON {$table}_status.id = {$table}.status_id"
				. " {$this->_left_join} fm_vendor ON {$table}.vendor_id = fm_vendor.id"
				. " {$this->_left_join} fm_request ON {$table}.id =fm_request.condition_survey_id {$filtermethod} {$querymethod} {$groupmethod}";


			$this->_db->query($sql, __LINE__, __FILE__);
			$this->_total_records = $this->_db->num_rows();

			if (!$allrows)
			{
				$this->_db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->_db->query($sql . $ordermethod, __LINE__, __FILE__);
			}

			$values = array();
			while ($this->_db->next_record())
			{
				$values[] = array
					(
					'id'		 => $this->_db->f('id'),
					'title'		 => $this->_db->f('title', true),
					'descr'		 => $this->_db->f('descr', true),
					'address'	 => $this->_db->f('address', true),
					'vendor'	 => $this->_db->f('vendor', true),
					'entry_date' => $this->_db->f('entry_date'),
					'user'		 => $this->_db->f('user_id'),
					'multiplier' => $this->_db->f('multiplier'),
					'cnt'		 => $this->_db->f('cnt'),
					'status'	 => $this->_db->f('status', true),
					'closed'	 => $this->_db->f('closed'),
				);
			}

			foreach ($values as &$entry)
			{
				$summation = $this->get_summation($entry['id']);

				$entry['summation'] = 0;
				foreach ($summation as $sum)
				{
					$entry['summation'] += $sum['amount'] * $sum['multiplier'] * $sum['representative'];
				}
			}


			return $values;
		}

		function read_single( $data = array() )
		{
			$table = 'fm_condition_survey';

			$id = (int)$data['id'];
			$this->_db->query("SELECT * FROM {$table} WHERE id={$id}", __LINE__, __FILE__);

			$values = array();
			if ($this->_db->next_record())
			{
				$values = array
					(
					'id'			 => $id,
					'title'			 => $this->_db->f('title', true),
					'descr'			 => $this->_db->f('descr', true),
					'location_code'	 => $this->_db->f('location_code', true),
					'status_id'		 => (int)$this->_db->f('status_id'),
					'cat_id'		 => (int)$this->_db->f('category'),
					'vendor_id'		 => (int)$this->_db->f('vendor_id'),
					'coordinator_id' => (int)$this->_db->f('coordinator_id'),
					'report_date'	 => (int)$this->_db->f('report_date'),
					'user_id'		 => (int)$this->_db->f('user_id'),
					'entry_date'	 => (int)$this->_db->f('entry_date'),
					'modified_date'	 => (int)$this->_db->f('modified_date'),
					'multiplier'	 => (float)$this->_db->f('multiplier'),
				);

				if (isset($data['attributes']) && is_array($data['attributes']))
				{
					$values['attributes'] = $data['attributes'];
					foreach ($values['attributes'] as &$attr)
					{
						$attr['value'] = $this->db->f($attr['column_name']);
					}
				}
			}

			return $values;
		}

		function add( $data )
		{
			$table = 'fm_condition_survey';

			$this->_db->transaction_begin();

			$value_set = $this->_get_value_set($data);

			$id = $this->_db->next_id($table);

			$value_set['id']			 = $id;
			$value_set['entry_date']	 = time();
			$value_set['title']			 = $this->_db->db_addslashes($data['title']);
			$value_set['descr']			 = $this->_db->db_addslashes($data['descr']);
			$value_set['status_id']		 = (int)$data['status_id'];
			$value_set['category']		 = (int)$data['cat_id'];
			$value_set['vendor_id']		 = (int)$data['vendor_id'];
			$value_set['coordinator_id'] = (int)$data['coordinator_id'];
			$value_set['report_date']	 = phpgwapi_datetime::date_to_timestamp($data['report_date']);
			$value_set['user_id']		 = $this->account;
			$value_set['modified_date']	 = time();
			$value_set['multiplier']	 = (float)$data['multiplier'];

			$cols	 = implode(',', array_keys($value_set));
			$values	 = $this->_db->validate_insert(array_values($value_set));
			$sql	 = "INSERT INTO {$table} ({$cols}) VALUES ({$values})";

			try
			{
				$this->_db->Exception_On_Error	 = true;
				$this->_db->query($sql, __LINE__, __FILE__);
				$this->_db->Exception_On_Error	 = false;
			}
			catch (Exception $e)
			{
				if ($e)
				{
					throw $e;
				}
				return 0;
			}

			if ($this->_db->transaction_commit())
			{
				return $id;
			}
			else
			{
				return 0;
			}
		}

		public function edit( $data )
		{
			$table	 = 'fm_condition_survey';
			$id		 = (int)$data['id'];

			$value_set = $this->_get_value_set($data);

			$value_set['title']			 = $this->_db->db_addslashes($data['title']);
			$value_set['descr']			 = $this->_db->db_addslashes($data['descr']);
			$value_set['status_id']		 = (int)$data['status_id'];
			$value_set['category']		 = (int)$data['cat_id'];
			$value_set['vendor_id']		 = (int)$data['vendor_id'];
			$value_set['coordinator_id'] = (int)$data['coordinator_id'];
			$value_set['report_date']	 = phpgwapi_datetime::date_to_timestamp($data['report_date']);
			$value_set['user_id']		 = $this->account;
			$value_set['modified_date']	 = time();
			$value_set['multiplier']	 = (float)$data['multiplier'];


			$this->_db->query("SELECT coordinator_id FROM fm_condition_survey WHERE id = {$id}", __LINE__, __FILE__);
			$this->_db->next_record();
			$old_coordinator_id = (int)$this->_db->f('coordinator_id');


			$this->_db->transaction_begin();
			try
			{
				$this->_db->Exception_On_Error = true;

				if ($old_coordinator_id != $value_set['coordinator_id'])
				{
					$this->_db->query("UPDATE fm_request SET coordinator = {$value_set['coordinator_id']} WHERE condition_survey_id = {$id}", __LINE__, __FILE__);
				}

				$this->_edit($id, $value_set, 'fm_condition_survey');
				$this->_db->query("UPDATE fm_request SET multiplier = '{$data['multiplier']}' WHERE condition_survey_id = {$id}", __LINE__, __FILE__);
				$this->_db->Exception_On_Error = false;
			}
			catch (Exception $e)
			{
				if ($e)
				{
					$this->_db->transaction_abort();
					throw $e;
				}
			}

			$this->_db->transaction_commit();
			return $id;
		}

		public function edit_title( $data )
		{
			$id = (int)$data['id'];

			$value_set = array
				(
				'title' => $data['title']
			);

			try
			{
				$this->_edit($id, $value_set, 'fm_condition_survey');
			}
			catch (Exception $e)
			{
				if ($e)
				{
					throw $e;
				}
			}

			return $id;
		}

		public function edit_multiplier( $data )
		{
			$id = (int)$data['id'];

			$value_set = array
				(
				'multiplier' => $data['multiplier']
			);

			$this->_db->transaction_begin();

			try
			{
				$this->_edit($id, $value_set, 'fm_condition_survey');
				$this->_db->query("UPDATE fm_request SET multiplier = '{$data['multiplier']}' WHERE condition_survey_id = {$id}", __LINE__, __FILE__);
			}
			catch (Exception $e)
			{
				if ($e)
				{
					$this->_db->transaction_abort();
					throw $e;
				}
			}

			$this->_db->transaction_commit();

			return $id;
		}

		public function import( $survey, $import_data = array() )
		{
			if (!isset($survey['id']) || !$survey['id'])
			{
				throw new Exception('property_socondition_survey::import - missing id');
			}

			$location_data = execMethod('property.solocation.read_single', $survey['location_code']);

			$_locations	 = explode('-', $survey['location_code']);
			$i			 = 1;
			foreach ($_locations as $_location)
			{
				$location["loc{$i}"] = $_location;
				$i++;
			}

			$sorequest = CreateObject('property.sorequest');

			$this->_db->transaction_begin();

			$config = CreateObject('phpgwapi.config', 'property');
			$config->read();

			if (!$survey['location_code'])
			{
				throw new Exception('property_socondition_survey::import - condition survey location_code not configured');
			}

			//FIXME
			if (!isset($config->config_data['condition_survey_import_cat']) || !is_array($config->config_data['condition_survey_import_cat']))
			{
				throw new Exception('property_socondition_survey::import - condition survey import categories not configured');
			}

			if (!isset($config->config_data['condition_survey_initial_status']) || !$config->config_data['condition_survey_initial_status'])
			{
				throw new Exception('property_socondition_survey::import - condition survey initial status not configured');
			}

			if (!isset($config->config_data['condition_survey_hidden_status']) || !$config->config_data['condition_survey_hidden_status'])
			{
				throw new Exception('property_socondition_survey::import - condition survey hidden status not configured');
			}

			/**
			 * Park old request on the location and below as obsolete
			 */
			if (isset($config->config_data['condition_survey_obsolete_status']) && $config->config_data['condition_survey_obsolete_status'])
			{
				$this->_db->query("UPDATE fm_request SET status = '{$config->config_data['condition_survey_obsolete_status']}'"
					. " WHERE location_code {$this->_db->like} '{$survey['location_code']}%'", __LINE__, __FILE__);
			}
			else
			{
//				throw new Exception('property_socondition_survey::import - condition survey obsolete status not configured');
			}

			$cats				 = CreateObject('phpgwapi.categories', -1, 'property', '.project');
			$cats->supress_info	 = true;
			$categories			 = $cats->return_sorted_array(0, false, '', '', '', $globals			 = true, '', $use_acl			 = false);


			/*
			  $import_types = array
			  (
			  1 => 'Hidden',
			  2 => 'Normal import',
			  3 => 'Users/Customers responsibility',
			  );
			 */


			$import_type_responsibility = array();

			$import_type_responsibility[1]	 = 1; //hidden => responsible_unit 1
			$import_type_responsibility[2]	 = 1; //'Normal import' => responsible_unit 1
			$import_type_responsibility[3]	 = 2; //'Customers' => responsible_unit 2
			$import_type_responsibility[4]	 = 3; //'Customers' => responsible_unit 3
			$import_type_responsibility[5]	 = 4; //'Customers' => responsible_unit 3
			$import_type_responsibility[6]	 = 5; //'Customers' => responsible_unit 3
			$import_type_responsibility[7]	 = 6; //'Customers' => responsible_unit 3
			$import_type_responsibility[8]	 = 7; //'Customers' => responsible_unit 3

			/*
			  $cats_candidates = array
			  (
			  1 => 'Investment',
			  2 => 'Operation',
			  3 => 'Combined::Investment/Operation',
			  );

			 */
			$_update_buildingpart	 = array();
			$filter_buildingpart	 = isset($config->config_data['filter_buildingpart']) ? $config->config_data['filter_buildingpart'] : array();

			if ($filter_key = array_search('.project.request', $filter_buildingpart))
			{
				$_update_buildingpart = array("filter_{$filter_key}" => 1);
			}

			foreach ($import_data as &$entry)
			{
				$entry['amount_investment']			 = (int)str_replace(array(' ', ','), array('',
						'.'), $entry['amount_investment']);
				$entry['amount_operation']			 = (int)str_replace(array(' ', ','), array('', '.'), $entry['amount_operation']);
				$entry['amount_potential_grants']	 = (int)str_replace(array(' ', ','), array(
						'', '.'), $entry['amount_potential_grants']);
				$entry['import_type']				 = (int)$entry['import_type'];
				$entry['condition_degree']			 = (int)$entry['condition_degree'];
				$entry['amount']					 = $entry['amount_investment'] + $entry['amount_operation'] + $entry['amount_potential_grants'];
			}


			unset($entry);

			$custom		 = createObject('phpgwapi.custom_fields');
			$attributes	 = $custom->find('property', '.project.request', 0, '', '', '', true, true);

			$origin_id = $GLOBALS['phpgw']->locations->get_id('property', '.project.condition_survey');
			foreach ($import_data as $entry)
			{
				//if( $entry['condition_degree'] > 0 && $entry['building_part'] && $entry['import_type'] > 0)
				if ($entry['amount'] && $entry['building_part'] && $entry['import_type'] > 0)
				{

					$request = array();

					if ($entry['amount_investment'] && !$entry['amount_operation'])
					{
						if (isset($config->config_data['condition_survey_import_cat'][1]))
						{
							$request['cat_id'] = (int)$config->config_data['condition_survey_import_cat'][1];
						}
					}
					if (!$entry['amount_investment'] && $entry['amount_operation'])
					{
						if (isset($config->config_data['condition_survey_import_cat'][2]))
						{
							$request['cat_id'] = (int)$config->config_data['condition_survey_import_cat'][2];
						}
					}
					else
					{
						if (isset($config->config_data['condition_survey_import_cat'][3]))
						{
							$request['cat_id'] = (int)$config->config_data['condition_survey_import_cat'][3];
						}
					}

					if (!isset($request['cat_id']) || !$request['cat_id'])
					{
						$request['cat_id'] = (int)$categories[0]['id'];
					}

					$this->_check_building_part($entry['building_part'], $_update_buildingpart);


					$request['condition_survey_id']	 = $survey['id'];
					$request['multiplier']			 = $survey['multiplier'];
					$request['street_name']			 = $location_data['street_name'];
					$request['street_number']		 = $location_data['street_number'];
					$request['location']			 = $location;
					$request['location_code']		 = $survey['location_code'];
					$request['origin_id']			 = $origin_id;
					$request['origin_item_id']		 = (int)$survey['id'];
					$request['title']				 = $entry['title'];
					$request['descr']				 = phpgw::clean_value($entry['descr'], 'string');
					$request['building_part']		 = phpgw::clean_value($entry['building_part'], 'string');
					$request['coordinator']			 = $survey['coordinator_id'];

					if ($entry['import_type'] == 1)
					{
						$request['status'] = $config->config_data['condition_survey_hidden_status'];
					}
					else
					{
						$request['status'] = $config->config_data['condition_survey_initial_status'];
					}

					$request['amount_investment']		 = $entry['amount_investment'];
					$request['amount_operation']		 = $entry['amount_operation'];
					$request['amount_potential_grants']	 = $entry['amount_potential_grants'];

					$request['planning_value']	 = $entry['amount'];
					$request['planning_date']	 = mktime(13, 0, 0, 7, 1, $entry['due_year'] ? (int)$entry['due_year'] : date('Y'));
					$request['recommended_year'] = $entry['due_year'] ? (int)$entry['due_year'] : date('Y');

					$request['responsible_unit'] = (int)$import_type_responsibility[$entry['import_type']];

					$request['condition'] = array
						(
						array
							(
							'degree'		 => $entry['condition_degree'],
							'condition_type' => $entry['condition_type'],
							'consequence'	 => $entry['consequence'],
							'probability'	 => $entry['probability'] ? $entry['probability'] : 2
						)
					);

					$values_attribute = array();
					foreach ($entry as $_field => $_value)
					{
						if (preg_match('/^custom_attribute_/', $_field) && $_value)
						{
							$attribute_id = (int)ltrim($_field, 'custom_attribute_');

							$values_attribute[] = array
								(
								'name'		 => $attributes[$attribute_id]['column_name'],
								'value'		 => $_value,
								'datatype'	 => $attributes[$attribute_id]['datatype'],
							);
						}
					}

					$sorequest->add($request, $values_attribute);
				}
			}

			$this->_db->transaction_commit();
		}

		private function _check_building_part( $id, $_update_buildingpart )
		{
			$sql = "SELECT id FROM fm_building_part WHERE id = '{$id}'";
			$this->_db->query($sql, __LINE__, __FILE__);
			if (!$this->_db->next_record())
			{
				$sql = "INSERT INTO fm_building_part (id, descr) VALUES ('{$id}', '{$id}::__')";
				$this->_db->query($sql, __LINE__, __FILE__);
			}

			if ($_update_buildingpart)
			{
				$value_set = $this->_db->validate_update($_update_buildingpart);
				$this->_db->query("UPDATE fm_building_part SET {$value_set} WHERE id = '{$id}'", __LINE__, __FILE__);
			}
		}

		public function get_summation( $id )
		{
			$id_filter = '';

			$condition_survey_id = (int)$id;

			if ($condition_survey_id == -1) // all
			{
				$id_filter = "condition_survey_id > 0";
			}
			else
			{
				$id_filter = "condition_survey_id = {$condition_survey_id}";
			}

			$sql = "SELECT condition_survey_id, substr(building_part, 1,3) as building_part_,"
				. " amount_investment as investment ,amount_operation as operation,"
				. " recommended_year as year, fm_request.multiplier, area_gross, representative"
				. " FROM fm_request {$this->_join} fm_request_status ON fm_request.status = fm_request_status.id"
				. " {$this->_join} fm_request_condition ON fm_request_condition.request_id = fm_request.id"
				. " {$this->_join} fm_location1 ON fm_request.loc1 = fm_location1.loc1"
				. " {$this->_join} fm_condition_survey ON fm_request.condition_survey_id = fm_condition_survey.id"
				. " {$this->_join} fm_condition_survey_status ON fm_condition_survey.status_id = fm_condition_survey_status.id"
				. " WHERE {$id_filter}"
				. " AND fm_condition_survey_status.closed IS NULL"
				. " AND degree > 1"
				//		. " GROUP BY condition_survey_id, building_part_ , year, fm_request.multiplier, area_gross"
				. " ORDER BY building_part_";

			$this->_db->query($sql, __LINE__, __FILE__);

			$values = array();
			while ($this->_db->next_record())
			{
				$amount			 = $this->_db->f('investment') + $this->_db->f('operation');
				$_multiplier	 = $this->_db->f('multiplier');
				$_representative = $this->_db->f('representative');

				$values[] = array
					(
					'condition_survey_id'	 => $this->_db->f('condition_survey_id'),
					'building_part'			 => $this->_db->f('building_part_'),
					'amount_investment'		 => $this->_db->f('investment'),
					'amount_operation'		 => $this->_db->f('operation'),
					'year'					 => $this->_db->f('year'),
					'multiplier'			 => $_multiplier ? (float)$_multiplier : 1,
					'area_gross'			 => (float)$this->_db->f('area_gross'),
					'representative'		 => $_representative ? (float)$_representative : 1,
				);
			}

			$lang_operation	 = lang('O&M');//Operations and Maintenance
			$lang_investment = lang('investment');

			$return = array();
			foreach ($values as $entry)
			{
				if ($entry['amount_investment'])
				{
					$return[] = array
						(
						'condition_survey_id'	 => $entry['condition_survey_id'],
						'building_part'			 => $entry['building_part'],
						'amount'				 => $entry['amount_investment'],
						'year'					 => $entry['year'],
						'category'				 => $lang_investment,
						'multiplier'			 => $entry['multiplier'],
						'area_gross'			 => $entry['area_gross'],
						'representative'		 => $entry['representative'],
					);
				}
				if ($entry['amount_operation'])
				{
					$return[] = array
						(
						'condition_survey_id'	 => $entry['condition_survey_id'],
						'building_part'			 => $entry['building_part'],
						'amount'				 => $entry['amount_operation'],
						'year'					 => $entry['year'],
						'category'				 => $lang_operation,
						'multiplier'			 => $entry['multiplier'],
						'area_gross'			 => $entry['area_gross'],
						'representative'		 => $entry['representative'],
					);
				}
			}

			return $return;
		}

		public function delete( $id )
		{

			$this->_db->transaction_begin();

			try
			{
				$this->_db->Exception_On_Error	 = true;
				$this->delete_imported_records($id);
				$this->_db->query("DELETE FROM fm_condition_survey WHERE id = {$id}", __LINE__, __FILE__);
				$this->_db->Exception_On_Error	 = false;
			}
			catch (Exception $e)
			{
				if ($e)
				{
					$this->_db->transaction_abort();
					throw $e;
				}
			}

			$this->_db->transaction_commit();
		}

		public function delete_imported_records( $id )
		{
			$id			 = (int)$id;
			$interlink	 = CreateObject('property.interlink');

			if ($this->_db->get_transaction())
			{
				$this->_global_lock = true;
			}
			else
			{
				$this->_db->transaction_begin();
			}

			$requests = array();
			$this->_db->query("SELECT id AS request_id FROM fm_request WHERE condition_survey_id={$id}", __LINE__, __FILE__);
			while ($this->_db->next_record())
			{
				$requests[] = $this->_db->f('request_id');
			}

			try
			{
				$this->_db->Exception_On_Error = true;
				if ($requests)
				{
					$this->_db->query('DELETE FROM fm_request_planning WHERE request_id IN (' . implode(',', $requests) . ')', __LINE__, __FILE__);
					$this->_db->query('DELETE FROM fm_request_consume WHERE request_id IN (' . implode(',', $requests) . ')', __LINE__, __FILE__);
					$this->_db->query('DELETE FROM fm_request_condition WHERE request_id IN (' . implode(',', $requests) . ')', __LINE__, __FILE__);
					$this->_db->query('DELETE FROM fm_request_history  WHERE  history_record_id IN (' . implode(',', $requests) . ')', __LINE__, __FILE__);
				}
				$this->_db->query("DELETE FROM fm_request WHERE condition_survey_id = {$id}", __LINE__, __FILE__);

				foreach ($requests as $request_id)
				{
					$interlink->delete_at_target('property', '.project.request', $request_id, $this->_db);
				}

				$this->_db->Exception_On_Error = false;
			}
			catch (Exception $e)
			{
				if ($e)
				{
					if (!$this->_global_lock)
					{
						$this->_db->transaction_abort();
					}

					throw $e;
				}
			}

			if (!$this->_global_lock)
			{
				$this->_db->transaction_commit();
			}
		}

		function get_export_data( $id )
		{
			$condition_survey_id = (int)$id;

			$sql	 = "SELECT DISTINCT fm_request.location_code,fm_request.id as request_id,fm_request_status.descr as status,"
				. "fm_request.building_part,fm_building_part.descr as building_part_text, fm_request.start_date,fm_request.entry_date,fm_request.closed_date,"
				. "fm_request.in_progress_date,fm_request.delivered_date,fm_request.title as title,fm_request.descr,"
				. "max(fm_request_condition.degree) as condition_degree,"
				. "max(fm_request_condition.consequence) as consequence,"
				. "max(fm_request_condition.probability) as probability,"
				. "fm_request_condition_type.name as condition_type_name,"
				. "fm_request_condition_type.priority_key as priority_key,"
				. "(fm_request.amount_investment * multiplier) as amount_investment,"
				. "(fm_request.amount_operation * multiplier) as amount_operation,"
				. "(fm_request.amount_potential_grants * multiplier) as amount_potential_grants,fm_request.score,"
				. "recommended_year,start_date AS planned_year,fm_request.coordinator,"
				. "fm_location1.loc1_name,loc1_name,loc2_name,loc3_name,fm_request.address "
				. "FROM (((((((((( fm_request  LEFT JOIN fm_request_status ON fm_request.status = fm_request_status.id) "
				. "LEFT JOIN fm_request_planning ON fm_request.id = fm_request_planning.request_id) "
				. "LEFT JOIN fm_request_consume ON fm_request.id = fm_request_consume.request_id) "
				. "LEFT JOIN fm_request_condition ON fm_request.id = fm_request_condition.request_id) "
				. "JOIN  fm_location1 ON (fm_request.loc1 = fm_location1.loc1)) "
				. "JOIN  fm_request_condition_type ON (fm_request_condition.condition_type = fm_request_condition_type.id) "
				. "JOIN  fm_building_part ON (fm_request.building_part = fm_building_part.id)) "
				. "JOIN  fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.id)) "
				. "JOIN   fm_owner ON (fm_location1.owner_id = fm_owner.id)) "
				. "LEFT JOIN  fm_location2 ON (fm_location2.loc1 = fm_location1.loc1 AND  fm_location2.loc2 = fm_request.loc2)) "
				. "LEFT JOIN  fm_location3 ON (fm_location3.loc2 = fm_location2.loc2 AND  fm_location3.loc3 = fm_request.loc3) AND (fm_location3.loc1 = fm_location2.loc1 AND  fm_location3.loc3 = fm_request.loc3)) "
				. "WHERE fm_request.condition_survey_id = {$condition_survey_id} "
				. "GROUP BY fm_request.location_code,fm_location1.loc1_name,fm_request_status.descr,building_part,fm_building_part.descr,"
				. "fm_request_condition_type.name,fm_request.entry_date,fm_request.closed_date,fm_request.in_progress_date,"
				. "fm_request.delivered_date,title,priority_key,"
				. "fm_request.descr,amount_investment,amount_operation,amount_potential_grants,score,recommended_year,start_date,coordinator,fm_location2.loc2_name,fm_location3.loc3_name,fm_request.multiplier,fm_request.id,fm_request_status.descr,fm_request.address"
				. " ORDER BY fm_request.building_part ASC";
//			_debug_array($sql);die();
			$this->_db->query($sql);
			$values	 = array();
			while ($this->_db->next_record())
			{
				$values[] = $this->_db->Record;
			}
			foreach ($values as &$value)
			{
				$value['risk']					 = $value['consequence'] * $value['probability'];
				$value['score']					 = $value['risk'] * $value['priority_key'];
				$value['amount_total']			 = $value['amount_investment'] + $value['amount_operation'];
				$value['percentage_investment']	 = ($value['amount_investment'] / $value['amount_total']) * 100;
				$value['percentage_grants']		 = ($value['amount_potential_grants'] / $value['amount_investment']) * 100;
			}

			return $values;
		}
	}