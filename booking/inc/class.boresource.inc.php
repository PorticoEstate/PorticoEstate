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
		}

		public function allowed_types()
		{
			return booking_soresource::allowed_types();
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
					'write' => array_fill_keys(array('name', 'description', 'activity_id', 'type'), true),
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
			$resources = $this->read();

			$building_ids = array();
			foreach ($resources['results'] as &$resource)
			{
				$resource['link'] = $this->link(array('menuaction' => $menuaction, 'id' => $resource['id']));
				$resource['type'] = lang($resource['type']);
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
	}