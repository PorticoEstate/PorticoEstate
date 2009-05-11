<?php
	phpgw::import_class('booking.uicommon');

	class booking_uicontactperson extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bocontactperson');
		}
        public function index()
        {
            if(phpgw::get_var('phpgw_return_as') == 'json') {
                return $this->index_json();
            }
        }
        public function index_json()
        {   
            $persons = $this->bo->read();
            $data = array
            (   
                'ResultSet' => array(
                    "totalResultsAvailable" => $persons['total_records'], 
                    "Result" => $persons['results']
                )   
            );  
            return $data;
        }
    }

