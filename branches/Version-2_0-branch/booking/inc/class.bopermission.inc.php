<?php
	phpgw::import_class('booking.bocommon_authorized');

	abstract class booking_bopermission extends booking_bocommon_authorized
	{

		protected
			$object_bo;

		function __construct()
		{
			parent::__construct();
			$object_type = substr(get_class($this), 21);
			$this->so = CreateObject(sprintf('booking.sopermission_%s', $object_type));
			$this->object_bo = CreateObject(sprintf('booking.bo%s', $object_type));
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function include_subject_parent_roles( array $for_object = null )
		{
			$parent_roles = null;
			$object = null;

			if (is_array($for_object))
			{
				if (!isset($for_object['object_id']))
				{
					throw new InvalidArgumentException('Cannot initialize object parent roles unless object_id is provided');
				}

				$object = $this->object_bo->read_single($for_object['object_id']);
			}

			//Note that a null value for $object is acceptable. That only signifies
			//that any roles specified for any object are returned instead of roles 
			//for a specific object.
			$parent_roles['object'] = $this->object_bo->get_subject_roles($object);

			return $parent_roles;
		}

		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions( $forObject, $defaultPermissions )
		{
			return array_merge(
				array
				(
				'parent_role_permissions' => array
					(
					'object' => array(),
				),
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
				'parent_role_permissions' => array
					(
					'object' => array()
				),
				'global' => array
					(
					booking_sopermission::ROLE_MANAGER => array
						(
						'create' => true,
					)
				),
				), $defaultPermissions
			);
		}

		public function get_roles()
		{
			return $this->so->get_roles();
		}

		public function read_object( $object_id )
		{
			return $this->so->read_object($object_id);
		}
	}