<?php
    phpgw::import_class("booking.uicommon");

    class bookingfrontend_uiarrangement extends booking_uicommon
    {

        protected $module;

        public function __construct()
        {
            parent::__construct();
            $this->module= "bookingfrontend";
        }


        public $public_functions = array
        (
            'index' => true,
            'show'  => true
        );

        public function show()
        {
            echo "hellos";

            $arrangement['test']="test";
            $config = CreateObject('phpgwapi.config', 'booking');
            $config->read();
            _debug_array($arrangement);
            phpgwapi_jquery::load_widget("core");

            self::render_template_xsl('arrangement', array('arrangement' => $arrangement));

        }

        public function query()
        {
            // TODO: Implement query() method.
        }
        public function index()
        {
            if (phpgw::get_var('phpgw_return_as') == 'json')
            {
                return $this->query();
            }

            phpgw::no_access();
        }

}