<?php
	phpgw::import_class('rental.bocommon');
	
	class rental_bocomposite extends rental_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('rental.socomposite');
		}
		
		protected function get_object_role_permissions(array $forObject, $defaultPermissions)
		{
			return array_merge(
				array
				(
					rental_sopermission::ROLE_MANAGER => array
					(
						'write' => true,
					),
					rental_sopermission::ROLE_CASE_OFFICER => array
					(
						'write' => array_fill_keys(array('name', 'homepage', 'description', 'email', 'phone', 'address'), true),
					),
					'global' => array
					(
						rental_sopermission::ROLE_MANAGER => array
						(
							'read' => true,
							'write' => true,
							'create' => true,
							'delete' => true,
						),
					)
				),
				$defaultPermissions
			);
		}
		
		protected function get_collection_role_permissions($defaultPermissions)
		{
			return array_merge(
				array(
					'global' => array
					(
						rental_sopermission::ROLE_MANAGER => array
						(
							'create' => true, #means that this role may create new objects of the present type
							'delete' => true,
						),
					),
				),
				$defaultPermissions
			);
		}
		
		public function get_included_rental_units($params)
		{
			return $this->so->get_included_rental_units($params);
		}
		
		public function get_available_rental_units($params)
		{
			return $this->so->get_available_rental_units($params);
		}
		
		/**
		 * Includes the given location_id as a rental unit as part of the composite specified by the composite id 
		 * 
		 * @param $composite_id the id of the composite
		 * @param $location_id the id of the rental unit
		 * @return true if the composite includes the given unit
		 */
		public function add_unit($composite_id, $location_id, $loc1)
		{
			if (!$this->has_unit($composite_id, $location_id)) {
				$this->so->add_unit($composite_id, $location_id, $loc1);
			}
			
			return false;
		}
		
		/**
		 * Removes the relation between the given composite and location 
		 * 
		 * @param $composite_id the id of the composite
		 * @param $location_id the id of the rental unit
		 * @return true if the composite includes the given unit
		 */
		public function remove_unit($composite_id, $location_id)
		{
		if ($this->has_unit($composite_id, $location_id)) {
				$this->so->remove_unit($composite_id, $location_id);
			}
			return false;
		}
		
		/**
		 * Returns true if the composite includes the rental unit specified by the provided location_id, false otherwise. 
		 * 
		 * @param $composite_id the id of the composite to check
		 * @param $location_id the id of the rental unit identified by a location
		 * @return true if the composite includes the given unit
		 */
		public function has_unit($composite_id, $location_id)
		{
			// Get all rental units for this composite
			$units = $this->get_included_rental_units(array('id' => $composite_id));
			
			foreach ($units as $unit) {
				if ($unit['location_id'] == $location_id) {
					// We found our unit
					return true;
				}
			}
			
			// The given rental unit wasn't found
			return false;
		}
		
		
		/**
		 * Returns the contracts for the specified composite.
		 * 
		 * @param $params array with paramters for the query.
		 * @return array with contract data.
		 */
		public function get_contracts($params)
		{
			return $this->so->get_contracts($params);
		}
		
		/**
		 * Returns array of available contract statuses
		 * @return array(
		 * 	'id' => id of status,
		 *  'status' => textual presentation of status
		 * )
		 */
		public function get_contract_status_array()
		{
			return $this->so->get_contract_status_array();
		}
		
	}
?>