<?php
	phpgw::import_class('booking.bocommon_authorized');
	
	class booking_boallocation extends booking_bocommon_authorized
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soallocation');
		}
		
		/**
		 * @see bocommon_authorized
		 */
		protected function include_subject_parent_roles(array $for_object = null)
		{
			$this->season_bo = CreateObject('booking.boseason');
			$parent_roles = null;
			$parent_season = null;
			
			if (is_array($for_object)) {
				if (!isset($for_object['season_id'])) {
					throw new InvalidArgumentException('Cannot initialize object parent roles unless season_id is provided');
				}
				$parent_season = $this->season_bo->read_single($for_object['season_id']);
			}
			
			//Note that a null value for $parent_season is acceptable. That only signifies
			//that any roles specified for any season are returned instead of roles for a specific season.
			$parent_roles['season'] = $this->season_bo->get_subject_roles($parent_season);
			return $parent_roles;
		}
		
		
		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions(array $forObject, $defaultPermissions)
		{
			return array_merge(
				array
				(
					'parent_role_permissions' => array
					(
						'season' => array
						(
							booking_sopermission::ROLE_MANAGER => array(
								'write' => true,
								'create' => true,
							),
							booking_sopermission::ROLE_CASE_OFFICER => array(
								'write' => true,
								'create' => true,
							),
							'parent_role_permissions' => array(
								'building' => array(
									booking_sopermission::ROLE_MANAGER => array(
										'write' => true,
										'create' => true,
									),
								),
							)
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
				),
				$defaultPermissions
			);
		}
		
		/**
		 * @see bocommon_authorized
		 */
		protected function get_collection_role_permissions($defaultPermissions)
		{
			return array_merge(
				array
				(
					'parent_role_permissions' => array
					(
						'season' => array
						(
							booking_sopermission::ROLE_MANAGER => array(
								'create' => true,
							),
							booking_sopermission::ROLE_CASE_OFFICER => array(
								'create' => true,
							),
							'parent_role_permissions' => array(
								'building' => array(
									booking_sopermission::ROLE_MANAGER => array(
										'create' => true,
									),
								),
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
				),
				$defaultPermissions
			);
		}
		
		public function complete_expired(&$allocations) {
			$this->so->complete_expired($allocations);
		}
		
		public function find_expired() {
			return $this->so->find_expired();
		}
	}
