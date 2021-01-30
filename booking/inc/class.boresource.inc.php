<?php
	phpgw::import_class('booking.bocommon_authorized');

	class booking_boresource extends booking_bocommon_authorized
	{

		protected
			$building_bo;

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soresource');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->facility_bo = CreateObject('booking.bofacility');
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function include_subject_parent_roles( array $for_object = null )
		{
			$parent_roles = null;
			$parent_building = null;

			if (is_array($for_object))
			{

				/*				 * FIXME: Sigurd 30 jan 2016: convert from single id to array of ids
				 *
				 */
				if (!isset($for_object['buildings'][0]))
				{
					throw new InvalidArgumentException('Cannot initialize object parent roles unless building_id is provided');
				}

				$parent_building = $this->building_bo->read_single($for_object['buildings'][0]);
			}

			//Note that a null value for $parent_building is acceptable. That only signifies
			//that any roles specified for any building are returned instead of roles for a specific building.
			$parent_roles['building'] = $this->building_bo->get_subject_roles($parent_building);

			return $parent_roles;
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			return array_merge(
				array
				(
				booking_sopermission::ROLE_MANAGER => array
					(
					'write' => true,
				),
				booking_sopermission::ROLE_CASE_OFFICER => array
					(
					'write' => array_fill_keys(array('name', 'description', 'opening_hours', 'contact_info',
						'activity_id', 'type', 'rescategory_id'), true),
				),
				'parent_role_permissions' => array
					(
					'building' => array
						(
						booking_sopermission::ROLE_MANAGER => array(
							'write' => true,
							'create' => true,
						),
					),
				),
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array
						(
						'write' => true,
						'delete' => true,
						'create' => true
					),
				),
				), $defaultPermissions
			);
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function get_collection_role_permissions( $defaultPermissions )
		{
			return array_merge(
				array
				(
				'parent_role_permissions' => array
					(
					'building' => array
						(
						booking_sopermission::ROLE_MANAGER => array(
							'create' => true,
						)
					)
				),
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array
						(
						'create' => true
					)
				),
				), $defaultPermissions
			);
		}

		public function populate_grid_data( $menuaction )
		{
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$resources = $this->read_all();
			$this->add_activity_facility_data($resources['results']);

			$building_ids = array();
			foreach ($resources['results'] as &$resource)
			{
				$resource['link'] = $this->link(array('menuaction' => $menuaction, 'id' => $resource['id']));
				$resource['direct_booking_date'] = $resource['direct_booking'] ? $GLOBALS['phpgw']->common->show_date($resource['direct_booking'], $dateformat) : null;
//				$resource['full_name']	 = $resource['building_name'] . ' / ' . $resource['name'];
				if (isset($resource['buildings']))
				{
					$building_ids = array_merge($building_ids, $resource['buildings']);
				}
			}
			unset($resource);

			$buildings = $this->building_bo->get_building_names(array_unique($building_ids));

			foreach ($resources['results'] as &$resource)
			{
				$_building_names = array();
				if (is_array($resource['buildings']))
				{

					$resource['building_street'] = $buildings[$resource['buildings'][0]]['street'];
					$resource['building_city'] = $buildings[$resource['buildings'][0]]['zip_code'];
					$resource['building_district'] = $buildings[$resource['buildings'][0]]['district'];

					foreach ($resource['buildings'] as $building_id)
					{
						$_building_names[] = "{$buildings[$building_id]['name']} ({$buildings[$building_id]['activity']})";
					}
				}
				$resource['building_name'] = implode(', <br/>', $_building_names);

				$resource['full_name'] = $resource['building_name'] . ' / ' . $resource['name'];
			}

			$data = array(
				'total_records' => $resources['total_records'],
				'start' => $resources['start'],
				'sort' => $resources['sort'],
				'dir' => $resources['dir'],
				'results' => $resources['results']
			);

//            echo '<pre>'; print_r($rpta); echo '</pre>'; exit('saul');
			return $data;
		}


		function add_activity_facility_data(&$resources)
		{
			// Get a list of all activities, grouped on top level activities, as well as all facilities
			$activitylist = $this->activity_bo->fetch_activities_hierarchy();
			$facilitylist = $this->facility_bo->get_facilities();

			foreach ($resources as &$resource)
			{
				// Add a list of activities with id and name. Only active activities are included, and only activities
				// belonging to the top level activity defined for the resource. Note that activity names containing
				// special characters (such as parentheses) seems to be doubly escaped when retrieved, so decode the
				// name once here (ie. from "&amp;#40;" to "&#40;") and then the templates can handle the second
				// decoding
				$toplevelactivity_id = $resource['activity_id'];
				$childactivities = array();
				if (array_key_exists($toplevelactivity_id, $activitylist))
				{
					$childactivities = $activitylist[$toplevelactivity_id]['children'];
				}
				$activity_ids = $resource['activities'];
				$resource['activities_list'] = array();
				foreach ($activity_ids as $activity_id)
				{
					if (array_key_exists($activity_id, $childactivities))
					{
						$childactivity = $childactivities[$activity_id];
						if ($childactivity['active'])
						{
							$resource['activities_list'][] = array('id' => $childactivity['id'], 'name' => html_entity_decode($childactivity['name']));
						}
					}
				}
				usort($resource['activities_list'], function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });
				// Add a list of facilities with id and name. Only active facilities are included
				$facility_ids = $resource['facilities'];
				$resource['facilities_list'] = array();
				foreach ($facility_ids as $facility_id)
				{
					if (array_key_exists($facility_id,$facilitylist))
					{
						$facility = $facilitylist[$facility_id];
						if ($facility['active'])
						{
							// Include the facility for the resource
							$resource['facilities_list'][] = array('id' => $facility['id'], 'name' => $facility['name']);
						}
					}
				}
				usort($resource['facilities_list'], function ($a,$b) { return strcmp(strtolower($a['name']),strtolower($b['name'])); });
			}
		}


		public function get_schedule( $id, $buildingmodule, $resourcemodule, $search = null )
		{
			$date = new DateTime(phpgw::get_var('date'));
			// Make sure $from is a monday
			if ($date->format('w') != 1)
			{
				$date->modify('last monday');
			}
			$prev_date = clone $date;
			$next_date = clone $date;
			$prev_date->modify('-1 week');
			$next_date->modify('+1 week');
			$resource = $this->read_single($id);
			if ($search)
			{
				$resource['buildings_link'] = self::link(array('menuaction' => $search, "type" => "building"));
			}
			else
			{
				$resource['buildings_link'] = self::link(array('menuaction' => $buildingmodule . '.index'));
			}
			$resource['building_link'] = self::link(array('menuaction' => $buildingmodule . '.schedule',
					'id' => $resource['building_id']));
			$resource['resource_link'] = self::link(array('menuaction' => $resourcemodule . '.show',
					'id' => $resource['id']));
			$resource['date'] = $date->format('Y-m-d');
			$resource['week'] = intval($date->format('W'));
			$resource['year'] = intval($date->format('Y'));
			$resource['prev_link'] = self::link(array('menuaction' => $resourcemodule . '.schedule',
					'id' => $resource['id'], 'date' => $prev_date->format('Y-m-d')));
			$resource['next_link'] = self::link(array('menuaction' => $resourcemodule . '.schedule',
					'id' => $resource['id'], 'date' => $next_date->format('Y-m-d')));
			for ($i = 0; $i < 7; $i++)
			{
				$resource['days'][] = array('label' => sprintf('%s<br/>%s %s', lang($date->format('l')), lang($date->format('M')), $date->format('d')),
					'key' => $date->format('D'));
				$date->modify('+1 day');
			}
			return $resource;
		}

		function add_building( $entity, $resource_id, $building_id )
		{
			if ($this->authorize_write($entity))
			{
				return parent::add_building($resource_id, $building_id);
			}
			return false;
		}

		function remove_building( $entity, $resource_id, $building_id )
		{
			if ($this->authorize_write($entity))
			{
				return parent::remove_building($resource_id, $building_id);
			}
			return false;
		}

		function add_e_lock( $entity, $resource_id, $e_lock_system_id, $e_lock_resource_id,$e_lock_name = '', $access_code_format = '' )
		{
			if ($this->authorize_write($entity))
			{
				return parent::add_e_lock($resource_id, $e_lock_system_id, $e_lock_resource_id, $e_lock_name, $access_code_format);
			}
			return false;
		}

		function remove_e_lock( $entity, $resource_id, $e_lock_system_id, $e_lock_resource_id)
		{
			if ($this->authorize_write($entity))
			{
				return parent::remove_e_lock($resource_id, $e_lock_system_id, $e_lock_resource_id);
			}
			return false;
		}

		function add_paricipant_limit( $entity, $resource_id, $limit_from, $limit_quantity )
		{
			if ($this->authorize_write($entity))
			{
				return parent::add_paricipant_limit($resource_id, $limit_from, $limit_quantity);
			}
			return false;
		}

	}