<?php

phpgw::import_class('frontend.uicontract');

class frontend_uicontract_ex extends frontend_uicontract
{
	public function __construct()
	{
		$this->contract_state_identifier = "contract_state_ex";
		$this->contracts_per_location_identifier = "contracts_ex_per_location";
		$this->form_url = "index.php?menuaction=frontend.uicontract_ex.index";
		phpgwapi_cache::session_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.rental.contract_ex'));
		parent::__construct();
	}
}
