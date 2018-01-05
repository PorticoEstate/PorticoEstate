<?php
	phpgw::import_class('booking.bocommon_authorized');

	require_once "schedule.php";

	class booking_boseason extends booking_bocommon_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soseason');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->bo_allocation = CreateObject('booking.boallocation');
			$this->so_boundary = new booking_soseason_boundary();
			$this->so_resource = CreateObject('booking.soresource');
			$this->so_wtemplate_alloc = new booking_sowtemplate_alloc();
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
				if (!isset($for_object['building_id']))
				{
					throw new InvalidArgumentException('Cannot initialize object parent roles unless building_id is provided');
				}

				$parent_building = $this->building_bo->read_single($for_object['building_id']);
			}

			//Note that a null value for $parent_building is acceptable. That only signifies
			//that any roles specified for any building are returned instead of roles for a specific building.
			$parent_roles['building'] = $this->building_bo->get_subject_roles($parent_building);

			return $parent_roles;
		}

		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			return array_merge(
				array
				(
				booking_sopermission::ROLE_MANAGER => array(
					'write' => true,
					'create' => true,
				),
				booking_sopermission::ROLE_CASE_OFFICER => array(
					'write' => true,
				),
				'parent_role_permissions' => array
					(
					'building' => array
						(
						booking_sopermission::ROLE_MANAGER => array(
							'write' => true,
							'create' => true,
						),
						booking_sopermission::ROLE_CASE_OFFICER => array(
							'write' => true,
						),
					),
				),
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array(
						'read' => true,
						'write' => true,
						'create' => true,
						'delete' => true,
					),
				)
				), $defaultPermissions
			);
		}

		protected function get_collection_role_permissions( $defaultPermissions )
		{
			return array_merge(
				array(
				'parent_role_permissions' => array
					(
					'building' => array(
						booking_sopermission::ROLE_MANAGER => array(
							'create' => true,
						),
					),
				),
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array
						(
						'create' => true,
					),
				),
				), $defaultPermissions
			);
		}

		function generate_allocation( $season_id, $date, $to, $interval, $write = false )
		{
			$season = $this->so->read_single($season_id);
			$this->authorize_write($season_id);
			$valid = array();
			$invalid = array();
			do
			{
				$wday = $date->format('N');
				$tallocations = $this->so_wtemplate_alloc->read(array('filters' => array('season_id' => $season_id,
						'wday' => $wday), 'sort' => 'from_'));
				foreach ($tallocations['results'] as $talloc)
				{

					$allocation = extract_values($talloc, array('season_id', 'organization_id',
						'cost', 'resources', 'organization_name'));
					$allocation['active'] = '1';
					$allocation['from_'] = $date->format("Y-m-d") . ' ' . $talloc['from_'];
					$allocation['to_'] = $date->format("Y-m-d") . ' ' . $talloc['to_'];
					$allocation['building_name'] = $season['building_name'];
					$allocation['completed'] = 1;
					$errors = $this->bo_allocation->validate($allocation);

					if (!$errors)
						$valid[] = $allocation;
					elseif (count($this->bo_allocation->filter_conflict_errors($errors)) === 0)
						$invalid[] = $allocation;
					else
						throw new UnexpectedValueException('Encountered an unexpected validation error');
				}
				if ($date->format('N') == 7) // sunday
				{
					if ($interval == 2)
						$date->modify('+7 days');
					elseif ($interval == 3)
						$date->modify('+14 days');
					elseif ($interval == 4)
						$date->modify('+21 days');
				}

				$date->modify('+1 day');

				if ($date->format('Y-m-d') > $to->format('Y-m-d'))
				{
					if ($write)
					{
						$this->so->transaction_begin();
						foreach ($valid as $alloc)
						{
							$this->bo_allocation->add($alloc);
						}
						$this->so->transaction_commit();
					}
					return array('valid' => $valid, 'invalid' => $invalid);
				}
			}
			while (true);
		}

		function read_boundary( $boundary_id )
		{
			return $this->so_boundary->read_single($boundary_id);
		}

		function delete_boundary( array $boundary )
		{
			$this->authorize_write($boundary['season_id']);
			$this->so_boundary->delete($boundary['id']);
		}

		function validate_boundary( $boundary )
		{
			return $this->so_boundary->validate($boundary);
		}

		function add_boundary( $boundary )
		{
			$this->authorize_write($boundary['season_id']);
			return $this->so_boundary->add($boundary);
		}

		function get_boundaries( $season_id )
		{
			return $this->so_boundary->read(array('filters' => array('season_id' => $season_id),
					'sort' => 'wday,from_', 'dir' => 'asc'));
		}

		function add_wtemplate_alloc( $alloc )
		{
			$this->authorize_write($alloc['season_id']);
			return $this->so_wtemplate_alloc->add($alloc);
		}

		function delete_wtemplate_alloc( $alloc )
		{
			$this->authorize_write($alloc['season_id']);
			return $this->so_wtemplate_alloc->delete($alloc['id']);
		}

		function update_wtemplate_alloc( $alloc )
		{
			$this->authorize_write($alloc['season_id']);
			return $this->so_wtemplate_alloc->update($alloc);
		}

		function validate_wtemplate_alloc( $alloc )
		{
			return $this->so_wtemplate_alloc->validate($alloc);
		}

		/**
		 * Return a season's template schedule in a datatable
		 * compatible format
		 * 
		 * @param int	$season_id_id
		 *
		 * @return array containing values from $array for the keys in $keys.
		 */
		function wtemplate_schedule( $season_id )
		{
			$season = $this->read_single($season_id);
			$allocations = $this->so_wtemplate_alloc->read(array('filters' => array('season_id' => $season_id),
				'sort' => 'wday,from_'));
			$allocations = $allocations['results'];
			foreach ($allocations as &$alloc)
			{
				$alloc['name'] = $alloc['organization_name'];
				$alloc['from_'] = substr($alloc['from_'], 0, 5);
				$alloc['to_'] = substr($alloc['to_'], 0, 5);
			}
			$resources = $this->so_resource->read(array('filters' => array('id' => $season['resources'])));
			$resources = $resources['results'];
			//$bookings = $this->_split_multi_day_bookings($bookings, $from, $to);
			$results = build_schedule_table($allocations, $resources);
			return array('total_records' => count($results), 'results' => $results);
		}

		function wtemplate_alloc_read_single( $alloc_id )
		{
			return $this->so_wtemplate_alloc->read_single($alloc_id);
		}
	}