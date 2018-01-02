<?php
	phpgw::import_class('booking.bocommon_global_manager_authorized');

	class booking_boaccount_code_set extends booking_bocommon_global_manager_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soaccount_code_set');
		}

		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			return array_merge(
				array
				(
				booking_sopermission::ROLE_MANAGER => array
					(
					'write' => true,
				),
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array
						(
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
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array
						(
						'create' => true, #means that this role may create new objects of the present type
						'delete' => true,
					),
				),
				), $defaultPermissions
			);
		}
	}