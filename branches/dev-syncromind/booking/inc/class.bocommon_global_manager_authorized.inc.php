<?php
	phpgw::import_class('booking.bocommon_authorized');

	class booking_bocommon_global_manager_authorized extends booking_bocommon_authorized
	{

		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			return array_merge(
				array
				(
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array
						(
						'write' => true,
						'delete' => true,
						'create' => true,
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
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array
						(
						'create' => true,
						'delete' => true,
						'write' => true,
					)
				),
				), $defaultPermissions
			);
		}
	}