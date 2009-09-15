<?php
phpgw::import_class('rental.uicommon');
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'billing', 'inc/model/');

class rental_uibilling extends rental_uicommon
{
	
	public $public_functions = array
	(
		'index'	=> true,
	);
	
	public function index()
	{
		if(!$this->isAdministrator())
		{
			$this->render('permission_denied.php');
			return;
		}
		// First we get all active contrcts
		// TOOD: This limit value is insane.. we have to get rid of it:
//		$contracs = rental_contract::get_all(0, 1000000, null, null, null, null, array('contract_status' => 'active'));
////		var_dump($contracs);
//		foreach ($contracs as $contract) // Runs through all active contracts
//		{
//			
//		}
		$data = array
		(
		);
		// Step 4
		if(phpgw::get_var('step') == '3' && phpgw::get_var('next') != null) // User clicked next on step 3
		{
			$this->render('billing_step4.php', $data);
		}
		// Step 3
		else if((phpgw::get_var('step') == '2' && phpgw::get_var('next') != null) || phpgw::get_var('step') == '4' && phpgw::get_var('previous') != null) // User clicked next on step 2 or previous on step 4
		{
			var_dump($_POST);
			$contract_ids = phpgw::get_var('contract'); // Ids of the contracts to bill
			if($contract_ids != null && is_array($contract_ids) && count($contract_ids) > 0) // User submitted contracts to bill
			{
				$billing_start_timestamps = array(); // Billing start timestamps for each of the contracts
				foreach($contract_ids as $contract_id)
				{
					$billing_start_timestamps[] = strtotime(phpgw::get_var('bill_start_date_' . $contract_id . '_hidden'));
				}
				rental_billing::create_billing(phpgw::get_var('contract_type'), phpgw::get_var('billing_term'), phpgw::get_var('year'), phpgw::get_var('month'), $contract_ids, $billing_start_timestamps);
			}
			$this->render('billing_step3.php', $data);
		}
		// Step 2
		else if((phpgw::get_var('step') == '1' && phpgw::get_var('next') != null) || phpgw::get_var('step') == '3' && phpgw::get_var('previous') != null) // User clicked next on step 1 or previous on step 3
		{
			$contracts = rental_contract::get_contracts_for_billing(phpgw::get_var('contract_type'), phpgw::get_var('billing_term'), phpgw::get_var('year'), phpgw::get_var('month'));
			$data = array
			(
				'contracts' => $contracts,
				'contract_type' => phpgw::get_var('contract_type'),
				'billing_term' => phpgw::get_var('billing_term'),
				'year' => phpgw::get_var('year'),
				'month' => phpgw::get_var('month')
			);
			$this->render('billing_step2.php', $data);
		}
		else // Step 1	
		{
			$data = array
			(
				'contract_type' => phpgw::get_var('contract_type'),
				'billing_term' => phpgw::get_var('billing_term'),
				'year' => phpgw::get_var('year'),
				'month' => phpgw::get_var('month')
			);
			$this->render('billing_step1.php', $data);
		}
	}
	
	public function query()
	{
		if(!$this->hasReadPermission())
		{
			$this->render('permission_denied.php');
			return;
		}
	}

}
?>