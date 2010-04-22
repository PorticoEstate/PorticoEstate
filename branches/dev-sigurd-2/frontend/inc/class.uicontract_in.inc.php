<?php

phpgw::import_class('frontend.uifrontend');
phpgw::import_class('rental.uicontract');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.socomposite');
phpgw::import_class('rental.soparty');

class frontend_uicontract_in extends frontend_uifrontend
{

	public $public_functions = array(
            'index'     => true
	);

	public function __construct()
	{
		phpgwapi_cache::user_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.rental.contract(in)'), $GLOBALS['phpgw_info']['user']['account_id']);
		parent::__construct();
	}

	/**
	 * Show single contract details
	 */
	public function index()
	{
		
		$data = array (
			//'msgbox_data'   => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
			'header' 		=>	$this->header_state,
			'tabs' 			=> 	$this->tabs,
			'contract_data' => 	array (
				'select' => $contracts_for_selection, 
				'selected_contract' =>  $this->contract_state['selected'], 
				'contract'	=> null,//$this->contract_state['contract']->serialize(),
				'party'	=> $party_array,
				'composite' => $composite_array
			)
		);
		
		//var_dump($data);
                	
		$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $data));
		$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','contract_in'));
	}
}
