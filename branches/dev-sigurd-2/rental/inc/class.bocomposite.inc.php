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
	}
?>