<?php

    phpgw::import_class('frontend.uifrontend');
    phpgw::import_class('rental.uicontract');
    phpgw::import_class('rental.socontract');

    class frontend_uicontract extends frontend_uifrontend
    {

        public $public_functions = array(
            'index'     => true,
            'show'      => true
        );

		public function __construct()
		{
			parent::__construct();
		}

        /**
         * Show single contract details
         */
        public function index()
        {
            $cid = phpgw::get_var('cid', 'int', 'REQUEST', 0);
            $contract = array();
            if($cid)
            {
            	$contract = rental_socontract::get_instance()->get_single($cid);
            }
            //print_r($contract);
            $tpldata = array(); // Collect all contracts as arrays in this
            if($contract)
            {
            	$tpldata[] = array
            	(
            	    'id' => $contract->get_id()
            	);
            }
  
 			$data = array
			(
				'tabs'		=> $this->tabs,
				'tpldata'	=> $tpldata
			);

            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'contract'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('contract' => $data));
 
            //_debug_array($contract);
        }

        /**
         * TODO
         */
        public function show()
        {
			$this->index();
        }
    }
