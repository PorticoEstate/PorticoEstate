<?php

phpgw::import_class('frontend.uicontract');

class frontend_uicontract_internal extends frontend_uicontract
{
	public function __construct()
	{
		$this->contract_state_identifier = "contract_state";
		$this->contracts_per_location_identifier = "contracts_per_location";
	//	$this->form_url = "index.php?menuaction=frontend.uicontract_internal.index";
		$this->form_url = $GLOBALS['phpgw']->link('/',array('menuaction' => 'frontend.uicontract_internal.index'));
		phpgwapi_cache::session_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.rental.contract'));
		parent::__construct();
	}
}
