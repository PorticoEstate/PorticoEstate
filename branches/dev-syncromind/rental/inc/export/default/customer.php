<?php

$parties = array();
$contract_ids = array();
$sql_contracts = "SELECT contract_id from rental_invoice, rental_billing where rental_invoice.billing_id = rental_billing.id and rental_billing.id = {$billing_id}";
$this->db->query($sql_contracts, __LINE__, __FILE__);
while($this->db->next_record())
{
	$contract_ids[] = $this->unmarshal($this->db->f('contract_id'), 'int');
}

foreach($contract_ids as $contract_id)
{
	$sql_parties = "select rental_party.* ";
	$sql_parties .="from rental_party, rental_contract_party, rental_contract ";
	$sql_parties .="where rental_contract_party.contract_id = rental_contract.id ";
	$sql_parties .="and rental_contract_party.is_payer ";
	$sql_parties .="and rental_party.id = rental_contract_party.party_id ";
	$sql_parties .="and rental_contract.id = {$contract_id}";
	$this->db->query($sql_parties, __LINE__, __FILE__);
	while($this->db->next_record())
	{
		//generate party-objects
		$party = new rental_party($this->unmarshal($this->db->f('id'), 'string'));
		$party->set_account_number($this->unmarshal($this->db->f('account_number'), 'string'));
		$party->set_address_1($this->unmarshal($this->db->f('address_1'), 'string'));
		$party->set_address_2($this->unmarshal($this->db->f('address_2'), 'string'));
		$party->set_comment($this->unmarshal($this->db->f('comment'), 'string'));
		$party->set_company_name($this->unmarshal($this->db->f('company_name'), 'string'));
		$party->set_department($this->unmarshal($this->db->f('department'), 'string'));
		$party->set_email($this->unmarshal($this->db->f('email'), 'string'));
		$party->set_fax($this->unmarshal($this->db->f('fax'), 'string'));
		$party->set_first_name($this->unmarshal($this->db->f('first_name'), 'string'));
		$party->set_is_inactive($this->unmarshal($this->db->f('is_inactive'), 'bool'));
		$party->set_last_name($this->unmarshal($this->db->f('last_name'), 'string'));
		$party->set_location_id($this->unmarshal($this->db->f('org_location_id'), 'int'));
		$party->set_identifier($this->unmarshal($this->db->f('identifier'), 'string'));
		$party->set_mobile_phone($this->unmarshal($this->db->f('mobile_phone'), 'string'));
		$party->set_place($this->unmarshal($this->db->f('place'), 'string'));
		$party->set_postal_code($this->unmarshal($this->db->f('postal_code'), 'string'));
		$party->set_reskontro($this->unmarshal($this->db->f('reskontro'), 'string'));
		$party->set_title($this->unmarshal($this->db->f('title'), 'string'));
		$party->set_url($this->unmarshal($this->db->f('url'), 'string'));

		if(!in_array($party, $parties))
		{
			$parties[] = $party;
		}
	}
}
$customer_export = new rental_agresso_cs15($parties);