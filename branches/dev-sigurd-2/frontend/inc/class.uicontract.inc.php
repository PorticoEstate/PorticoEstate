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
            // This is the main container for all contract data sent to XSLT template stuff
            $contractdata = array();

            // Array of errors and other notifications displayed to user
            $msglog = array();

            // Holds the contract object (for use in this function only), if any
            $contract = null;
            $cid = phpgw::get_var('cid', 'int', 'REQUEST', 0);
            
            if($cid)
            {
            	$contract = rental_socontract::get_instance()->get_single($cid);
            }
            else
            {
                $msglog['error'][] = array('msg' => 'Gje meg en kontraktid!');
            }

            if(is_object($contract))
            {
            	$contractdata[] = array
            	(
            	    'id' => $contract->get_id(),
                    'rawdata' => var_export($contract, true)
            	);
            }
  
 			$data = array
			(
				'tabs'          => $this->tabs,
				'contract'      => $contractdata,
                'msgbox_data'   => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
			);

            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'header'));
            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'contract'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array(
                'header'    => $this->header_state,
                'contract'  => $data
            ));
        }

        /**
         * TODO
         */
        public function show()
        {
			$sotts = CreateObject('property.botts');
            
            /*$json = $sotts->read(array(
                'query' => '1101-01-01-01-105'

            ));*/
            $sotts->query = '1101-01-01-01-105';
            $json = $sotts->read();

            print_r($json);



        }
    }
