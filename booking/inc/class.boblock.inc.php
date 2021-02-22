<?php
	phpgw::import_class('booking.bocommon_authorized');

	class booking_boblock extends booking_bocommon_authorized
	{

		const ROLE_ADMIN = 'user_admin';

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soblock');
		}


		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			$defaultPermissions[booking_sopermission::ROLE_DEFAULT] = array
			(
				'read' => true,
				'delete' => true,
				'write' => true,
				'create' => true,
			);

			return $defaultPermissions;
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function get_collection_role_permissions( $defaultPermissions )
		{
			return array_merge(
				array(
					'global' => array(
						booking_sopermission::ROLE_MANAGER => array(
							'create' => true
						)
					),
				), $defaultPermissions
			);
		}

		public function delete_expired()
		{
			$this->so->delete_expired();
		}

		public function cancel_block($session_id, $dates, $resources)
		{
			$this->so->cancel_block($session_id, $dates, $resources);

		}
	}