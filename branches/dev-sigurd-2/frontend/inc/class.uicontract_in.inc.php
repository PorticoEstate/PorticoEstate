<?php
class frontend_uicontract_in extends frontend_uicontract
{
	public function __construct()
	{
		$this->contract_state_identifier = "contract_state_in";
		$this->contracts_per_location_identifier = "contracts_in_per_location";
		$this->form_url = "index.php?menuaction=frontend.uicontract_in.index";
		phpgwapi_cache::user_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.rental.contract_in'), $GLOBALS['phpgw_info']['user']['account_id']);
		parent::__construct();
	}
}
