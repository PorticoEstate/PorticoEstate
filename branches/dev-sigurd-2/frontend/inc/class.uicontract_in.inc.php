<?php

phpgw::import_class('frontend.uifrontend');
phpgw::import_class('frontend.uicontract');
phpgw::import_class('rental.uicontract');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.socomposite');
phpgw::import_class('rental.soparty');

class frontend_uicontract_in extends frontend_uicontract
{
	
	public $public_functions = array(
            'index'     => true
	);

	public function __construct()
	{
		$this->contract_state_identifier = "contract_state_in";
		$this->contracts_per_location_identifier = "contracts_in_per_location";
		phpgwapi_cache::user_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.rental.contract_in'), $GLOBALS['phpgw_info']['user']['account_id']);
		parent::__construct();
	}
}
