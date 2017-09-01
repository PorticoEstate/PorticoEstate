<?php
	phpgw::import_class('booking.bopermission');

	class booking_bopermission_resource extends booking_bopermission
	{

		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			$role_permissions = parent::get_object_role_permissions($forObject, $defaultPermissions);
			$role_permissions['parent_role_permissions']['object']['parent_role_permissions']['building'] = array
				(
				booking_sopermission::ROLE_MANAGER => array(
					'write' => true,
					'delete' => true,
					'create' => true,
				),
			);
			return $role_permissions;
		}

		protected function get_collection_role_permissions( $defaultPermissions )
		{
			$role_permissions = parent::get_collection_role_permissions($defaultPermissions);
			$role_permissions['parent_role_permissions']['object']['parent_role_permissions']['building'] = array
				(
				booking_sopermission::ROLE_MANAGER => array(
					'create' => true,
				),
			);
			return $role_permissions;
		}
	}