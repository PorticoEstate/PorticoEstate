<?php
	phpgw::import_class('booking.bocommon_authorized');
	
	class booking_boequipment extends booking_bocommon_authorized
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soequipment');
			$this->resource_bo = CreateObject('booking.boresource');
		}
		
		protected function include_subject_parent_roles(array $for_object)
		{
			$parent_roles = null;
			
			if (!is_null($for_object))
			{
				if (!isset($for_object['resource_id']))
				{
					throw new InvalidArgumentException('Cannot initialize object parent roles unless resource_id is provided');
				}
				
				$parent_resource = $this->resource_bo->read_single($for_object['resource_id']);
				$parent_roles['resource'] = $this->resource_bo->get_subject_roles($parent_resource);
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
						'resource' => array
						(
							booking_sopermission::ROLE_MANAGER => array(
								'write' => array_fill_keys(array('name', 'description'), true),
								'create' => true,
							),
							'parent_role_permissions' => array
							(
								'building' => array(
									booking_sopermission::ROLE_MANAGER => array(
										'write' => true,
										'create' => true,
									),
									booking_sopermission::ROLE_CASE_OFFICER => array(
										'write' => array_fill_keys(array('name', 'description'), true),
										'create' => true,
									),
								)
							),
						),
					),
					'global' => array
					(
						booking_sopermission::ROLE_MANAGER => array
						(
							'write' => true,
							'create' => true,
							'delete' => true,
						),
						booking_sopermission::ROLE_CASE_OFFICER => array(
							'write' => true,
							'create' => true,
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
		
		public function populate_json_data($module) {
			$resources = $this->read();
			foreach($resources['results'] as &$resource)
			{
				$resource['link'] = $this->link(array('menuaction' => $module.'.show', 'id' => $resource['id']));
			}
			$data = array
			(
				'ResultSet' => array(
					"totalResultsAvailable" => $resources['total_records'], 
					"Result" => $resources['results']
				)
			);
			return $data;
		}
	}
