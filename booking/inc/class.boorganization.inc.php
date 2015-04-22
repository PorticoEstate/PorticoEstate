<?php
	phpgw::import_class('booking.bocommon_authorized');
	
	class booking_boorganization extends booking_bocommon_authorized
	{
		const ROLE_ADMIN = 'organization_admin';
		
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soorganization');
		}
		
        function get_groups($organization_id) {
            return $this->so->get_groups($organization_id);
        }

		/**
		 * @see booking_bocommon_authorized
		 */
		protected function get_subject_roles($for_object = null, $initial_roles=array())
		{
			if ($this->current_app() == 'bookingfrontend') {
				$bouser = CreateObject('bookingfrontend.bouser');
				
				$org_id = is_array($for_object) ? $for_object['id'] : (!is_null($for_object) ? $for_object : null);
				
				if ($bouser->is_organization_admin($org_id)) {
					$initial_roles[] = array('role' => self::ROLE_ADMIN);
				}
			}
			
			return parent::get_subject_roles($for_object, $initial_roles);
		}
		
		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions(array $forObject, $defaultPermissions)
		{
			if ($this->current_app() == 'booking') {
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT] = array
				(
					'read' 		=> true,
					'delete' 	=> true,
					'write' 	=> true,
					'create' 	=> true,
				);
			}
			
			if ($this->current_app() == 'bookingfrontend') {	             
				$defaultPermissions[self::ROLE_ADMIN] = array
				(
					'write' => array_fill_keys(array('name','homepage','phone','email','description','street','zip_code','district','city','active','organization_number','contacts'), true),
				);
			}
			
			return $defaultPermissions;
		}
		
		/**
		 * @see bocommon_authorized
		 */
		protected function get_collection_role_permissions($defaultPermissions)
		{
			if ($this->current_app() == 'booking')
			{
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT]['create'] = true;
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT]['write'] = true;
			}
			
			return $defaultPermissions;
		}
		
		public function get_permissions(array $entity)
		{
			return parent::get_permissions($entity);
		}
		
		/**
		 * Removes any extra contacts from entity if such exists (only two contacts allowed).
		 */
		protected function trim_contacts(&$entity)
		{
			if (isset($entity['contacts']) && is_array($entity['contacts']) && count($entity['contacts']) > 2)
			{	
				$entity['contacts'] = array($entity['contacts'][0], $entity['contacts'][1]);
			}
			
			return $entity;
		}

		function add($entity)
		{
			return parent::add($this->trim_contacts($entity));
		}
		
		function update($entity)
		{
			return parent::update($this->trim_contacts($entity));
		}
		
		/**
		 * @see soorganization
		 */
		function find_building_users($building_id, $split = false, $activities = array())
		{
			return $this->so->find_building_users($building_id, $this->build_default_read_params(), $split, $activities);
		}
	}
