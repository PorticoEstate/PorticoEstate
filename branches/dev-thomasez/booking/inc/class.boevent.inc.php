<?php
	phpgw::import_class('booking.bocommon_authorized');
	
	class booking_boevent extends booking_bocommon_authorized
	{
		function __construct()
		{
			parent::__construct();
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->so = CreateObject('booking.soevent');
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function include_subject_parent_roles(array $for_object = null)
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
		
		protected function get_object_role_permissions(array $forObject, $defaultPermissions)
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
				),
				$defaultPermissions
			);
		}
		
		protected function get_collection_role_permissions($defaultPermissions)
		{
			return array_merge(
				array(
					booking_sopermission::ROLE_MANAGER => array(
						'write' => true,
						'create' => true,
					),
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
				),
				$defaultPermissions
			);
		}
		
		public function complete_expired(&$events) {
			$this->so->complete_expired($events);
		}
		
		public function find_expired() {
			return $this->so->find_expired();
		}
	}
