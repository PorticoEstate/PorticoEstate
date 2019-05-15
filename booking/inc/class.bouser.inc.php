<?php
	phpgw::import_class('booking.bocommon_authorized');

	class booking_bouser extends booking_bocommon_authorized
	{

		const ROLE_ADMIN = 'user_admin';

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.souser');
		}

		public function anonymisation( $id )
		{
			return $this->so->anonymisation($id);
		}

		/**
		 * @see booking_bocommon_authorized
		 */
		protected function get_subject_roles( $for_object = null, $initial_roles = array() )
		{
			if ($this->current_app() == 'bookingfrontend')
			{

				$bouser = CreateObject('bookingfrontend.bouser');

				$external_login_info = $bouser->validate_ssn_login( array
				(
					'menuaction' => 'bookingfrontend.uiuser.edit'
				));

				if(!empty($external_login_info['ssn']) && $external_login_info['ssn'] == $for_object['customer_ssn'])
				{
					$initial_roles[] = array('role' => self::ROLE_ADMIN);
				}
			}

			return parent::get_subject_roles($for_object, $initial_roles);
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
				$defaultPermissions[self::ROLE_ADMIN] = array
				(
					'write' => array_fill_keys(array('name', 'homepage', 'phone', 'email', 'description',
						'street', 'zip_code', 'district', 'city', 'active', 'user_number',
						'contacts'), true),
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
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT]['write'] = true;
			}

			return $defaultPermissions;
		}

		public function get_permissions( array $entity )
		{
			return parent::get_permissions($entity);
		}

		/**
		 * Removes any extra contacts from entity if such exists (only two contacts allowed).
		 */
		protected function trim_contacts( &$entity )
		{
			if (isset($entity['contacts']) && is_array($entity['contacts']) && count($entity['contacts']) > 2)
			{
				$entity['contacts'] = array($entity['contacts'][0], $entity['contacts'][1]);
			}

			return $entity;
		}

		function add( $entity )
		{
			return parent::add($this->trim_contacts($entity));
		}

		function update( $entity )
		{
			return parent::update($this->trim_contacts($entity));
		}

		/**
		 * @see souser
		 */
		function find_building_users( $building_id, $split = false, $activities = array() )
		{
			return $this->so->find_building_users($building_id, $this->build_default_read_params(), $split, $activities);
		}
	}