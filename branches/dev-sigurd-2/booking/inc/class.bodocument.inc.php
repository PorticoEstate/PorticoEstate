<?php
	phpgw::import_class('booking.bocommon_authorized');
	
	abstract class booking_bodocument extends booking_bocommon_authorized
	{
		protected 
			$owner_bo;
			
		function __construct()
		{
			parent::__construct();
			$owningType = substr(get_class($this), 19);
			$this->so = CreateObject(sprintf('booking.sodocument_%s', $owningType));
			$this->owner_bo = CreateObject(sprintf('booking.bo%s', $owningType));
		}
		
		protected function include_subject_parent_roles(array $for_object)
		{
			$parent_roles = null;
			
			if (!is_null($for_object))
			{
				if (!isset($for_object['owner_id']))
				{
					throw new InvalidArgumentException('Cannot initialize object parent roles unless owner_id is provided');
				}
				
				$owner = $this->owner_bo->read_single($for_object['owner_id']);
				$parent_roles['owner'] = $this->owner_bo->get_subject_roles($owner);
			}
			
			return $parent_roles;
		}
		
		protected function get_object_role_permissions(array $forObject, $defaultPermissions)
		{
			return array_merge(
				array
				(
					'parent_role_permissions' => array
					(
						'owner' => array
						(
							booking_sopermission::ROLE_MANAGER => array
							(
								'write' => true,
								'create' => true,
								'delete' => true,
							),
							booking_sopermission::ROLE_CASE_OFFICER => array
							(
								'write' => array_fill_keys(array('category', 'description'), true),
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
				),
				$defaultPermissions
			);
		}
		
		protected function get_collection_role_permissions($defaultPermissions)
		{
			return $defaultPermissions;
		}
		
		public function get_files_root()
		{
			return $this->so->get_files_root();
		}
		
		public function get_files_path()
		{
			return $this->so->get_files_path();
		}
		
		public function get_categories()
		{
			return $this->so->get_categories();
		}
		
		public function read_parent($owner_id)
		{
			return $this->so->read_parent($owner_id);
		}
	}