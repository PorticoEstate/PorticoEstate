<?php
	phpgw::import_class('booking.bodocument');

	class booking_bodocument_resource extends booking_bodocument
	{

		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			$role_permissions = parent::get_object_role_permissions($forObject, $defaultPermissions);
			$role_permissions['parent_role_permissions']['owner']['parent_role_permissions']['building'] = array
				(
				booking_sopermission::ROLE_MANAGER => array(
					'write' => true,
					'create' => true,
					'delete' => true,
				),
				booking_sopermission::ROLE_CASE_OFFICER => array(
					'write' => array_fill_keys(array('category', 'description'), true),
				),
			);
			return $role_permissions;
		}

		protected function get_collection_role_permissions( $defaultPermissions )
		{
			$role_permissions = parent::get_collection_role_permissions($defaultPermissions);
			$role_permissions['parent_role_permissions']['owner']['parent_role_permissions']['building'] = array
				(
				booking_sopermission::ROLE_MANAGER => array(
					'create' => true,
					'delete' => true,
				),
			);
			return $role_permissions;
		}
	}