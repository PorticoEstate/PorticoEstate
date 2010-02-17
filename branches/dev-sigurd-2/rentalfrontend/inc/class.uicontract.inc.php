<?php

    phpgw::import_class('rentalfrontend.uicommon');
    phpgw::import_class('rental.uicontract');
    phpgw::import_class('rental.socontract');

    class rentalfrontend_uicontract extends rental_uicontract
    {

        public $public_functions = array(
            'index'     => true,
            'show'      => true
        );


        /**
         * Show single contract details
         */
        public function show()
        {
            $contract = rental_socontract::get_instance()->get_single(phpgw::get_var('cid'));
            rentalfrontend_uicommon::render_template('contract', $this->contract);
            //_debug_array($contract);
        }


        /**
         * TODO
         */
        public function index()
        {

        }
    }
