<?php

phpgw::import_class('frontend.uifrontend');
phpgw::import_class('frontend.uicontract');
phpgw::import_class('rental.uicontract');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.socomposite');
phpgw::import_class('rental.soparty');


class frontend_uicontract_internal extends frontend_uicontract
{
	
	public $public_functions = array(
            'index'     => true
	);

	public function __construct()
	{
		$this->contract_state_identifier = "contract_state";
		$this->contracts_per_location_identifier = "contracts_per_location";
		$this->form_url = "index.php?menuaction=frontend.uicontract_internal.index";
		phpgwapi_cache::user_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.rental.contract_in'), $GLOBALS['phpgw_info']['user']['account_id']);
		parent::__construct();
	}
}
