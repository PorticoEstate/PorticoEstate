<?php

    phpgw::import_class('frontend.uicommon');
    phpgw::import_class('rental.uicontract');
    phpgw::import_class('rental.socontract');

    class frontend_uicontract extends rental_uicontract
    {

        public $public_functions = array(
            'index'     => true,
            'show'      => true
        );

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true; // må angis
//			parent::__construct();
		}

        /**
         * Show single contract details
         */
        public function show()
        {
            $cid = phpgw::get_var('cid', 'int', 'REQUEST', 0);
            $contract = array();
            if($cid)
            {
            	$contract = rental_socontract::get_instance()->get_single($cid);
            }
            //print_r($contract);
            $tpldata = array(); // Collect all contracts as arrays in this
            $tpldata[] = array(
                'id' => $contract->get_id()
            );
            frontend_uicommon::render_template(array('contract'), $tpldata);
            //_debug_array($contract);
        }


        /**
         * TODO
         */
        public function index()
        {
			$this->show();
        }
    }
