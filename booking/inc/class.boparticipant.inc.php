<?php
	phpgw::import_class('booking.bocommon_authorized');

	class booking_boparticipant extends booking_bocommon_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soparticipant');
		}


		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			if ($this->current_app() == 'booking')
			{
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT] = array
					(
					'read' => true,
					'delete' => true,
					'write' => true,
					'create' => true,
				);
			}

			if ($this->current_app() == 'bookingfrontend')
			{
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT] = array
				(
					'write' => true,
					'create' => true,
				);
			}

			return $defaultPermissions;
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function get_collection_role_permissions( $defaultPermissions )
		{
			if ($this->current_app() == 'booking')
			{
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT]['create'] = true;
			}

			if ($this->current_app() == 'bookingfrontend')
			{
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT]['create'] = true;
			}

			return $defaultPermissions;
		}
	}