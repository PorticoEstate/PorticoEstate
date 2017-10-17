<?php
	phpgw::import_class('booking.bocommon_authorized');

	class booking_bodelegate extends booking_bocommon_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sodelegate');
			$this->org_bo = CreateObject('booking.boorganization');
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function include_subject_parent_roles( array $for_object = null )
		{
			$parent_roles = null;
			$parent_org = null;

			if (is_array($for_object))
			{
				if (!isset($for_object['organization_id']))
				{
					throw new InvalidArgumentException('Cannot initialize object parent roles unless organization_id is provided');
				}

				$parent_org = $this->org_bo->read_single($for_object['organization_id']);
			}

			//Note that a null value for $parent_org is acceptable. That only signifies
			//that any roles specified for any organization are returned rather than the roles 
			//for a specific organization
			$parent_roles['organization'] = $this->org_bo->get_subject_roles($parent_org);

			return $parent_roles;
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

				$defaultPermissions['parent_role_permissions']['organization'] = array();
			}

			if ($this->current_app() == 'bookingfrontend')
			{
				$defaultPermissions['parent_role_permissions']['organization'][booking_boorganization::ROLE_ADMIN] = array
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
				$defaultPermissions['parent_role_permissions']['organization'][booking_boorganization::ROLE_ADMIN]['create'] = true;
			}

			return $defaultPermissions;
		}
	}