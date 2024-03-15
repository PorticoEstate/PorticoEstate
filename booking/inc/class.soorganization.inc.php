<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.socontactperson');
	phpgw::import_class('booking.sogroup');

	class booking_soorganization extends booking_socommon
	{

		function __construct($get_ssn = null)
		{
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$fields = array(
				'id' => array('type' => 'int'),
				'organization_number' => array('type' => 'string', 'query' => true, 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array(
						'invalid' => '%field% is invalid'))),
				'name' => array('type' => 'string', 'required' => True, 'query' => True),
				'shortname' => array('type' => 'string', 'required' => False, 'query' => True),
				'homepage' => array('type' => 'string', 'required' => False, 'query' => True),
				'phone' => array('type' => 'string'),
				'email' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array(
						'invalid' => '%field% is invalid'))),
				'description_json' => array('type' => 'json', 'required' => false),
				'co_address' => array('type' => 'string'),
				'street' => array('type' => 'string'),
				'zip_code' => array('type' => 'string'),
				'district' => array('type' => 'string'),
				'city' => array('type' => 'string'),
				'active' => array('type' => 'int', 'required' => true),
				'show_in_portal' => array('type' => 'int', 'required' => true),
				'activity_id' => array('type' => 'int', 'required' => true),
				'customer_identifier_type' => array('type' => 'string', 'required' => False),
				'customer_number' => array('type' => 'string', 'required' => False),
				'customer_organization_number' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array(
						'invalid' => '%field% is invalid'))),
				'customer_internal' => array('type' => 'int', 'required' => true),
				'in_tax_register' => array('type' => 'int'),
				'activity_name' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_activity',
						'fkey' => 'activity_id',
						'key' => 'id',
						'column' => 'name'
					)),
				'contacts' => array('type' => 'string',
					'manytomany' => array(
						'table' => 'bb_organization_contact',
						'key' => 'organization_id',
						'column' => array('name',
//							'ssn' => array('sf_validator' => createObject('booking.sfValidatorNorwegianSSN')),
							'email' => array('sf_validator' => createObject('booking.sfValidatorEmail', array(), array(
									'invalid' => '%field% contains an invalid email'))),
							'phone')
					)
				)
			);

			/**
			 * Hide from bookingfrontend, but keep visible for cron jobs
			 */
			if( $get_ssn 
				|| in_array($currentapp, array('booking','login') )
				|| ( $GLOBALS['phpgw_info']['menuaction'] == 'bookingfrontend.uiorganization.add' && phpgw::get_var('customer_ssn', 'bool', 'POST') ) )
			{
				$fields['customer_ssn'] = array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN'),	'required' => false);
				$fields['contacts']['manytomany']['column']['ssn'] = array('sf_validator' => createObject('booking.sfValidatorNorwegianSSN'));
			}
			

			parent::__construct('bb_organization', $fields);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function get_metainfo( $id )
		{
			$this->db->limit_query("SELECT name, shortname, district, city, description_json FROM bb_organization where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return array('name' => $this->db->f('name', true),
				'shortname' => $this->db->f('shortname', true),
				'district' => $this->db->f('district', true),
				'city' => $this->db->f('city', true),
				'description_json' => json_decode($this->db->f('description_json'),true));
		}

		function get_orgid( $orgnr, $customer_ssn = null )
		{
			if(!$orgnr)
			{
				return False;
			}

			if($orgnr == '000000000' && $customer_ssn)
			{
				$this->db->limit_query("SELECT id FROM bb_organization WHERE customer_ssn ='{$customer_ssn}'", 0, __LINE__, __FILE__, 1);				
			}
			else
			{
				$this->db->limit_query("SELECT id FROM bb_organization WHERE organization_number ='{$orgnr}'", 0, __LINE__, __FILE__, 1);
			}

			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('id', false);
		}

		function get_groups( $organization_id )
		{
			static $groups = null;
			if ($groups === null)
			{
				$groups = new booking_sogroup();
			}
			$results = $groups->read(array('results' => -1, "filters" => array("organization_id" => $organization_id)));
			return $results;
		}

		function get_resource_activity( $resources )
		{
			if(!$resources)
			{
				return array();
			}

			$resource_ids = implode(',', $resources);
			$results = array();
			$sql = "SELECT activity_id FROM bb_resource where id in (" . $resource_ids . ")";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->db->f('activity_id', false);
			}
			return $results;
		}

		/**
		  Returns the organizations who've used the building with the specified id
		  within the last 300 days.

		  @param int $building_id
		  @param array $params Parameters to pass to socommon->read
		  @param bool $split Parameter
		  @param array $activities Parameters

		  @return array (in socommon->read format)
		 * */
		function find_building_users( $building_id, $params = array(), $split = false, $activities = array() )
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$test = '';

			$pools = !empty($config->config_data['split_pool_ids']) ? $config->config_data['split_pool_ids'] : '-1';
			$halls = !empty($config->config_data['split_pool2_ids']) ? $config->config_data['split_pool2_ids'] : '-1';
			$meeting = !empty($config->config_data['split_pool3_ids']) ? $config->config_data['split_pool3_ids'] : '-1';
			$excluded = !empty($config->config_data['split_pool4_ids']) ? $config->config_data['split_pool4_ids'] : '-1';

			if ($split)
			{
				if (count($activities) > 1)
				{
					if (array_intersect($activities, explode(',', $pools)) && array_intersect($activities, explode(',', $halls)))
					{
						$test = " AND r.activity_id not in (" . $excluded . ") ";
					}
					elseif (array_intersect($activities, explode(',', $pools)))
					{
						$test = " AND r.activity_id not in (" . $pools . "," . $excluded . ") ";
					}
					elseif (array_intersect($activities, explode(',', $halls)))
					{
						$test = " AND r.activity_id not in (" . $halls . "," . $excluded . ") ";
					}
					elseif (array_intersect($activities, explode(',', $excluded)))
					{
						$test = " AND r.activity_id not in (" . $halls . "," . $pools . "," . $meeting . "," . $excluded . ") ";
					}
					else
					{
						$test = " AND r.activity_id not in (" . $excluded . ") ";
					}
				}
				else
				{
					$activity = $activities[0];
					if (in_array($activity, explode(',', $pools)))
					{
						$test = " AND r.activity_id not in (" . $halls . "," . $meeting . "," . $excluded . ") ";
					}
					elseif (in_array($activity, explode(',', $halls)))
					{
						$test = " AND r.activity_id not in (" . $pools . "," . $excluded . ") ";
					}
					elseif (in_array($activity, explode(',', $excluded)))
					{
						$test = " AND r.activity_id not in (" . $halls . "," . $pools . "," . $meeting . "," . $excluded . ") ";
					}
					else
					{
						$test = " AND r.activity_id not in (" . $excluded . ") ";
					}
				}
			}
			if (!isset($params['filters']))
			{
				$params['filters'] = array();
			}
			if (!isset($params['results']))
			{
				$params['results'] = -1;
			}
			if (!isset($params['filters']['where']))
			{
				$params['filters']['where'] = array();
			}
			if ($config->config_data['mail_users_season'] == 'yes')
			{
				$params['filters']['where'][] = '%%table%%.id IN (' .
					'SELECT DISTINCT o.id FROM bb_resource r ' .
					'JOIN bb_allocation_resource ar ON ar.resource_id = r.id ' .
					'JOIN bb_allocation a ON a.id = ar.allocation_id ' .
					'JOIN bb_building_resource br ON r.id = br.resource_id AND br.building_id = ' . $this->_marshal($building_id, 'int') . ' ' .
					'JOIN bb_organization o ON o.id = a.organization_id ' .
					'JOIN bb_season s ON s.building_id = br.building_id ' .
					'WHERE s.active = 1 ' .
					'AND s.from_ <= \'now\'::timestamp ' .
					'AND s.to_ >= \'now\'::timestamp ' .
					'AND a.from_ >= s.from_ ' .
					'AND a.to_ <= s.to_ ' . $test . ' ORDER BY o.id ASC' .
					')';
			}
			else
			{
				$params['filters']['where'][] = '%%table%%.id IN (' .
					'SELECT DISTINCT o.id FROM bb_resource r ' .
					'JOIN bb_allocation_resource ar ON ar.resource_id = r.id ' .
					'JOIN bb_building_resource br ON r.id = br.resource_id AND br.building_id = ' . $this->_marshal($building_id, 'int') . ' ' .
					'JOIN bb_allocation a ON a.id = ar.allocation_id AND (a.from_ - \'now\'::timestamp < \'300 days\') ' .
					'JOIN bb_organization o ON o.id = a.organization_id ' . $test . ' ORDER BY o.id ASC' .
					')';
			}
			return $this->read($params);
		}

		protected function preValidate( &$entity )
		{
			if (!empty($entity['customer_organization_number']))
			{
				$entity['customer_organization_number'] = str_replace(" ", "", $entity['customer_organization_number']);
			}
			if (!empty($entity['customer_organization_number']))
			{
				$entity['organization_number'] = str_replace(" ", "", $entity['organization_number']);
			}
		}

		function get_organization_info( $org_number)
		{
			$result = array();
			array_set_default($result, 'name', '');
			array_set_default($result, 'id', '');
			array_set_default($result, 'street', '');
			array_set_default($result, 'zip_code', '');
			array_set_default($result, 'city', '');

			$org_number = (int)$org_number;
			if ($org_number)
			{
				$org_number = intval($org_number);
				$q1 = "SELECT name, id, street, zip_code, city FROM bb_organization WHERE organization_number='{$org_number}'";
				$this->db->query($q1, __LINE__, __FILE__);
				if($this->db->next_record())
				{
					$result['name']		 = $this->db->f('name', true);
					$result['id']		 = $this->db->f('id', true);
					$result['street']	 = $this->db->f('street', true);
					$result['zip_code']	 = $this->db->f('zip_code', true);
					$result['city']		 = $this->db->f('city', true);
				}
			}

			return $result;
		}

		function get_organization_number( $org_id )
		{
			$result = array();
			array_set_default($result, 'organization_number', '');
			array_set_default($result, 'organization_name', '');
			$org_id = (int)$org_id;
			if ($org_id)
			{
				$org_id = intval($org_id);
				$q1 = "SELECT organization_number, name FROM bb_organization WHERE id={$org_id}";
				$this->db->query($q1, __LINE__, __FILE__);

				if ($this->db->next_record())
				{
					$result['organization_number'] = $this->db->f('organization_number', true);
					$result['organization_name'] = $this->db->f('name', true);
				}
			}
			return $result;
		}

	}
