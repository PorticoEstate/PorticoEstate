<?php
	phpgw::import_class('booking.uipermission');

	class booking_uipermission_season extends booking_uipermission
	{

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('booking::buildings::seasons::permissions');
		}

		protected function get_parent_pathway( array $forDocumentData )
		{
			return array(
				array('text' => 'objects_plural_name', 'href' => 'objects_plural_href'),
				array('text' => 'object_singular_name', 'href' => 'object_singular_name'),
			);
		}
	}