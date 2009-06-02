<?php
	phpgw::import_class('rental.uidocument');

	class rental_uidocument_composite extends rental_uidocument
	{
		public function __construct()
		{
			parent::__construct();
			
			self::process_rental_unauthorized_exceptions();
			
			self::set_active_menu('rental::composites::documents');
		}
		
		protected function get_owner_pathway(array $forDocumentData)
		{
			return array( 
				array('text' => 'objects_plural_name', 	'href' => 'objects_plural_href'), 
				array('text' => 'object_singular_name', 'href' => 'object_singular_name'),
			);
		}
	}