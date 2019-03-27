<?php
	phpgw::import_class('booking.uidocument_view');

	class bookingfrontend_uidocument_view extends booking_uidocument_view
	{

		public $public_functions = array
		(
			'regulations' => true,
			'download' => true
		);

		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->module = "bookingfrontend";
		}
		public function regulations()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgw::no_access();
		}

		public function download()
		{
			if ($id = phpgw::get_var('id', 'string'))
			{
				list($type, $id_value) = explode('::', urldecode($id), 2);

				if(!in_array($type, array('building','resource') ))
				{
					throw new Exception("{$type}::Not a valid document type for download at bookingfrontend");
				}

				$document = $this->bo->read_single(urldecode($id));
				self::send_file($document['filename'], array('filename' => $document['name']));
			}
		}

	}