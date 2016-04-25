<?php
	phpgw::import_class('booking.uidocument');

	class booking_uidocument_building extends booking_uidocument
	{

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('booking::buildings::documents');
		}

		protected function get_owner_pathway( array $forDocumentData )
		{
			return array(
				array('text' => 'objects_plural_name', 'href' => 'objects_plural_href'),
				array('text' => 'object_singular_name', 'href' => 'object_singular_name'),
			);
		}
	}