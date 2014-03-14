<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

phpgw::import_class('rental.socommon');

include_class('rental', 'contract_date', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'composite', 'inc/model/');
include_class('rental', 'party', 'inc/model/');
include_class('rental', 'price_item', 'inc/model/');
include_class('rental', 'contract_price_item', 'inc/model/');

class rental_socontract extends rental_socommon
{
	protected static $so;
	protected $fields_of_responsibility; // Used for caching the values

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return rental_socontract the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.socontract');
		}
		return self::$so;
	}

	/**
	 * Filters:
	 * Contracts with party as contract party
	 * Contracts for executive officer
	 * Contracts last edited by user
	 * Contracts of type
	 * Contracts with this id (get single)
	 * Contracts with composite as contract composite
	 * Contracts with contract status
	 * Contracts for billing
	 *
	 * @see rental/inc/rental_socommon#get_query($sort_field, $ascending, $search_for, $search_type, $filters, $return_count)
	 */
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		$clauses = array('1=1');

		//Add columns to this array to include them in the query
		$columns = array();

		$dir = $ascending ? 'ASC' : 'DESC';
		if($sort_field == null || $sort_field == '')
		{
			$sort_field = 'contract.id';
		}
		else if ($sort_field == 'party'){
			$sort_field = "party.company_name {$dir}, party.last_name {$dir}, party.first_name";
		}
		else if ($sort_field == 'composite'){
			$sort_field = "composite.name";
		}
		else if ($sort_field == 'type'){
			$sort_field = 'contract.location_id';
		}
		else if($sort_field == 'term_label'){
			$sort_field = 'contract.term_id';
		}


		//Contracts for billing should always be sorted on biling start
		if(isset($filters['contracts_for_billing']))
		{
			$order = "ORDER BY contract.billing_start ASC";
		}
		else
		{
			$order = "ORDER BY {$sort_field} {$dir}";
		}

		// Search for based on search type
		if($search_for)
		{
			$search_for = $this->marshal($search_for,'field');
			$like_pattern = "'%".$search_for."%'";
			$int_value_of_search = (int) $search_for;
			$like_clauses = array();
			switch($search_type){
				case "id":
					$like_clauses[] = "contract.old_contract_id $this->like $like_pattern";
					break;
				case "party_name":
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					break;
				case "composite":
					$like_clauses[] = "composite.name $this->like $like_pattern";
					break;
				case "composite_address":
					$composite_address = true;
					break;
				case "location_id":
					$like_clauses[] = "r_u.location_code like '{$search_for}%'";
					break;
				case "all":

					$like_clauses[] = "contract.old_contract_id $this->like $like_pattern";
					$like_clauses[] = "contract.comment $this->like $like_pattern";
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					$like_clauses[] = "composite.name $this->like $like_pattern";
					$like_clauses[] = "r_u.location_code $this->like $like_pattern";
					break;
			}

			if($composite_address)
			{
				$sql_composite_address = "select rental_composite.id as rc_id from rental_composite,rental_unit,fm_gab_location where rental_unit.composite_id=rental_composite.id and fm_gab_location.location_code=rental_unit.location_code and fm_gab_location.address like upper({$like_pattern})";
				$this->db->query($sql_composite_address, __LINE__, __FILE__,false,true);
				$array_composites = array();
				while($this->db->next_record())
				{
					$array_composites[] = $this->db->f('rc_id');
				}
				if($array_composites)
				{
					$composites = implode(',',$array_composites);
					$like_clauses[] = "composite.id in ($composites)";
				}
				else
				{
					$like_clauses[] = "composite.id in (-1)";
				}
			}


			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}


		}

		$filter_clauses = array();

		// Contracts with party as contract party
		if(isset($filters['party_id'])){
			$party_id  =   $this->marshal($filters['party_id'],'int');
			$filter_clauses[] = "party.id = $party_id";
		}

		// Contracts for this executive officer
		if(isset($filters['executive_officer'])){
			$account_id  =   $this->marshal($filters['executive_officer'],'int');
			$filter_clauses[] = "contract.executive_officer = $account_id";
		}

		// Contracts of type
		if(isset($filters['contract_type']) && $filters['contract_type'] != 'all'){
			$type = $this->marshal($filters['contract_type'],'field');
			$filter_clauses[] = "contract.location_id IN ($type)";
		}

		// Contracts with this id (filter for retrieveing a single contract)
		if(isset($filters[$this->get_id_field_name()])){
			$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			$filter_clauses[] = "contract.id = {$id}";
		}

		// All contracts with composite as contract composite
		if(isset($filters['composite_id']))
		{
			$composite_id = $this->marshal($filters['composite_id'],'int');
			$filter_clauses[] = "composite.id = {$composite_id}";
		}

		// Affected contracts by regulation
		if(isset($filters['adjustment_interval']) && isset($filters['adjustment_year']))
		{
			$adjustment_interval = $this->marshal($filters['adjustment_interval'],'int');
			$adjustment_year = $this->marshal($filters['adjustment_year'],'int');

			if($filters['adjustment_is_executed']){
				$filter_clauses[] = "contract.adjustment_year = {$adjustment_year}";
			}
			else{
				$filter_clauses[] = "contract.adjustment_year + {$adjustment_interval} <= {$adjustment_year}";
			}

			$firstJanAdjYear = mktime(0,0,0,1,1,$adjustment_year);

			//make sure the contracts are active
			$filter_clauses[] = "(contract.date_end is null OR contract.date_end >= {$firstJanAdjYear})";
			$filter_clauses[] = "contract.date_start is not null AND contract.date_start <= {$firstJanAdjYear}";

			$filter_clauses[] = "contract.adjustable IS true";
			$filter_clauses[] = "contract.adjustment_interval = {$adjustment_interval}";

		}

		/*
		 * Contract status is defined by the dates in each contract compared to the target date (default today):
		 * - contracts under planning:
		 * the start date is larger (in the future) than the target date, or start date is undefined
		 * - active contracts:
		 * the start date is smaller (in the past) than the target date, and the end date is undefined (running) or
		 * larger (fixed) than the target date
		 * - under dismissal:
		 * the start date is smaller than the target date,
		 * the end date is larger than the target date, and
		 * the end date substracted the contract type notification period is smaller than the target date
		 * - ended:
		 * the end date is smaller than the target date
		 */
		if(isset($filters['contract_status']) && $filters['contract_status'] != 'all'){

			if(isset($filters['status_date_hidden']) && $filters['status_date_hidden'] != "")
			{
				$ts_query = strtotime($filters['status_date_hidden']); // target timestamp specified by user
			}
			else
			{
				$ts_query = strtotime(date('Y-m-d')); // timestamp for query (today)
			}
			switch($filters['contract_status']){
				case 'under_planning':
					$filter_clauses[] = "contract.date_start > {$ts_query} OR contract.date_start IS NULL";
					break;
				case 'active':
					$filter_clauses[] = "contract.date_start <= {$ts_query} AND ( contract.date_end >= {$ts_query} OR contract.date_end IS NULL)";
					break;
				case 'under_dismissal':
					$filter_clauses[] = "contract.date_start <= {$ts_query} AND contract.date_end >= {$ts_query} AND (contract.date_end - (type.notify_before * (24 * 60 * 60)))  <= {$ts_query}";
					break;
				case 'closing_due_date':
					$filter_clauses[] = "contract.due_date >= {$ts_query} AND (contract.due_date - (type.notify_before_due_date * (24 * 60 * 60)))  <= {$ts_query}";
					$order = "ORDER BY contract.due_date ASC";
					break;
				case 'terminated_contracts':
					$filter_clauses[] = "contract.date_end >= ({$ts_query} - (type.notify_after_termination_date * (24 * 60 * 60))) AND contract.date_end < {$ts_query}";
					$order = "ORDER BY contract.date_end DESC";
					break;
				case 'ended':
					$filter_clauses[] = "contract.date_end < {$ts_query}" ;
					break;
			}
		}

		/*
		 * Contracts for billing
		 */
		if(isset($filters['contracts_for_billing']))
		{
			$billing_term_id = (int)$filters['billing_term_id'];
			$sql = "SELECT months FROM rental_billing_term WHERE id = {$billing_term_id}";
			$result = $this->db->query($sql);
			if(!$result)
			{
				return;
			}
			if(!$this->db->next_record())
			{
				return;
			}
			$month = (int)$filters['month'];
			$year = (int)$filters['year'];
			$months = $this->unmarshal($this->db->f('months', true), 'int');
			$timestamp_end = strtotime("{$year}-{$month}-01"); // The first day in the month to bill for
			if($months == 1){
				$timestamp_start = $timestamp_end; // The first day of the period to bill for
			}else{
				$months = $months-1;
				$timestamp_start = strtotime("-{$months} months", $timestamp_end); // The first day of the period to bill for
			}
			$timestamp_end = strtotime('+1 month', $timestamp_end); // The first day in the month after the one to bill for
			//$timestamp_start = strtotime("{$year}-{$month}-01");

			$filter_clauses[] = "contract.term_id = {$billing_term_id}";
			$filter_clauses[] = "contract.date_start < $timestamp_end";
			$filter_clauses[] = "(contract.date_end IS NULL OR contract.date_end >= {$timestamp_start})";
			$filter_clauses[] = "(contract.billing_start IS NULL OR contract.billing_start < {$timestamp_end})";

			$specific_ordering = 'invoice.timestamp_end DESC, contract.billing_start DESC, contract.date_start DESC, contract.date_end DESC';
			$order = $order ? $order.', '.$specific_ordering : "ORDER BY {$specific_ordering}";
		}

		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(contract.id)) AS count';
			$order = ''; // No ordering
		}
		else
		{
			// columns to retrieve
			$columns[] = 'contract.id AS contract_id';
			$columns[] = 'contract.date_start, contract.date_end, contract.old_contract_id, contract.executive_officer, contract.last_updated, contract.location_id, contract.billing_start, contract.service_id, contract.responsibility_id, contract.reference, contract.invoice_header, contract.project_id, billing.deleted, contract.account_in, contract.account_out, contract.term_id, contract.security_type, contract.security_amount, contract.comment, contract.due_date, contract.contract_type_id,contract.rented_area,contract.adjustable,contract.adjustment_interval,contract.adjustment_share,contract.adjustment_year,contract.publish_comment';
			$columns[] = 'party.id AS party_id';
			$columns[] = 'party.first_name, party.last_name, party.company_name, party.department, party.org_enhet_id';
			$columns[] = 'c_t.is_payer';
			$columns[] = 'composite.id AS composite_id';
			$columns[] = 'composite.name AS composite_name';
			$columns[] = 'type.title, type.notify_before, type.notify_before_due_date, type.notify_after_termination_date';
			$columns[] = 'last_edited.edited_on';
			$columns[] = 'invoice.timestamp_end';
			$columns[] = 'r_b_t.title AS term_title';
			$cols = implode(',',$columns);
		}

		$tables = "rental_contract contract";
		$join_contract_type = 	$this->left_join.' rental_contract_responsibility type ON (type.location_id = contract.location_id)';
		$join_parties = $this->left_join.' rental_contract_party c_t ON (contract.id = c_t.contract_id) LEFT JOIN rental_party party ON (c_t.party_id = party.id)';
		$join_composites = 		$this->left_join." rental_contract_composite c_c ON (contract.id = c_c.contract_id) {$this->left_join} rental_composite composite ON c_c.composite_id = composite.id";
		$join_units = $this->left_join." rental_unit r_u ON (r_u.composite_id=composite.id)";
		$join_last_edited = $this->left_join.' rental_contract_last_edited last_edited ON (contract.id = last_edited.contract_id)';
		$join_last_billed = "{$this->left_join} rental_invoice invoice ON (contract.id = invoice.contract_id) {$this->left_join} rental_billing billing ON (invoice.billing_id = billing.id)";
		$join_term_title = "{$this->left_join} rental_billing_term r_b_t ON (contract.term_id = r_b_t.id)";
		$joins = $join_contract_type.' '.$join_parties.' '.$join_composites.' '.$join_units.' '.$join_last_edited.' '.$join_last_billed.' '.$join_term_title;

		//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");

		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}

	public function get_id_field_name($extended_info = false)
	{
		if(!$extended_info)
		{
			$ret = 'contract_id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'contract', // alias
				'field'			=> 'id',
				'translated'	=> 'contract_id'
			);
		}
		return $ret;
	}


	function populate(int $contract_id, &$contract)
	{

		if($contract == null ) // new contract
		{
			$contract_id = (int) $contract_id;
			$contract = new rental_contract($contract_id);
			$contract->set_contract_date(new rental_contract_date
				(
					$this->unmarshal($this->db->f('date_start'),'int'),
					$this->unmarshal($this->db->f('date_end'),'int')
				)
			);
			$contract->set_billing_start_date($this->unmarshal($this->db->f('billing_start'),'int'));
			$contract->set_old_contract_id($this->unmarshal($this->db->f('old_contract_id'),'string'));
			$contract->set_contract_type_title($this->unmarshal($this->db->f('title'),'string'));
			$contract->set_comment($this->unmarshal($this->db->f('comment'),'string'));
			$contract->set_last_edited_by_current_user($this->unmarshal($this->db->f('edited_on'),'int'));
			$contract->set_location_id($this->unmarshal($this->db->f('location_id'),'int'));
			$contract->set_last_updated($this->unmarshal($this->db->f('last_updated'),'int'));
			$contract->set_service_id($this->unmarshal($this->db->f('service_id'),'string'));
			$contract->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id'),'string'));
			$contract->set_reference($this->unmarshal($this->db->f('reference'),'string'));
			$contract->set_invoice_header($this->unmarshal($this->db->f('invoice_header'),'string'));
			$contract->set_account_in($this->unmarshal($this->db->f('account_in'),'string'));
			$contract->set_account_out($this->unmarshal($this->db->f('account_out'),'string'));
			$contract->set_project_id($this->unmarshal($this->db->f('project_id'),'string'));
			$contract->set_executive_officer_id($this->unmarshal($this->db->f('executive_officer'),'int'));
			$contract->set_term_id($this->unmarshal($this->db->f('term_id'),'int'));
			$contract->set_term_id_title($this->unmarshal($this->db->f('term_title'),'string'));
			$contract->set_security_type($this->unmarshal($this->db->f('security_type'),'int'));
			$contract->set_security_amount($this->unmarshal($this->db->f('security_amount'),'string'));
			$contract->set_due_date($this->unmarshal($this->db->f('due_date'),'int'));
			$contract->set_contract_type_id($this->unmarshal($this->db->f('contract_type_id'),int));
			$contract->set_rented_area($this->unmarshal($this->db->f('rented_area'),'float'));
			$contract->set_adjustable($this->unmarshal($this->db->f('adjustable'),'bool'));
			$contract->set_adjustment_interval($this->unmarshal($this->db->f('adjustment_interval'),'int'));
			$contract->set_adjustment_share($this->unmarshal($this->db->f('adjustment_share'),'int'));
			$contract->set_adjustment_year($this->unmarshal($this->db->f('adjustment_year'),'int'));
			$contract->set_publish_comment($this->unmarshal($this->db->f('publish_comment'),'bool'));
			$contract->set_notify_before($this->unmarshal($this->db->f('notify_before'),'int'));
			$contract->set_notify_before_due_date($this->unmarshal($this->db->f('notify_before_due_date'),'int'));
			$contract->set_notify_after_termination_date($this->unmarshal($this->db->f('notify_after_termination_date'),'int'));


		}

		$timestamp_end = $this->unmarshal($this->db->f('timestamp_end'),'int');
		$billing_deleted = $this->unmarshal($this->db->f('deleted'),'bool');
		if($timestamp_end && !$billing_deleted)
		{
			$contract->add_bill_timestamp($timestamp_end);
		}

		$total_price = $this->unmarshal($this->db->f('total_price'),'int');
		if($total_price)
		{
			$contract->set_total_price($total_price);
		}

		$party_id = $this->unmarshal($this->db->f('party_id', true), 'int');
		if($party_id)
		{
			$party = new rental_party($party_id);
			$party->set_first_name($this->unmarshal($this->db->f('first_name', true), 'string'));
			$party->set_last_name($this->unmarshal($this->db->f('last_name', true), 'string'));
			$party->set_company_name($this->unmarshal($this->db->f('company_name', true), 'string'));
			$party->set_department($this->unmarshal($this->db->f('department', true), 'string'));
			$party->set_org_enhet_id($this->unmarshal($this->db->f('org_enhet_id'), 'int'));
			$is_payer = $this->unmarshal($this->db->f('is_payer', true), 'bool');
			if($is_payer)
			{
				$contract->set_payer_id($party_id);
			}
			$contract->add_party($party);
		}

		$composite_id = $this->unmarshal($this->db->f('composite_id', true), 'int');
		if($composite_id)
		{
			$composite = new rental_composite($composite_id);
			$composite->set_name($this->unmarshal($this->db->f('composite_name', true), 'string'));
			$contract->add_composite($composite);
		}
		return $contract;
	}

	/**
	 * Get a key/value array of contract type titles keyed by their id
	 *
	 * @return array
	 */
	function get_fields_of_responsibility(){
		if($this->fields_of_responsibility == null)
		{
			$sql = "SELECT location_id,title FROM rental_contract_responsibility";
			$this->db->query($sql, __LINE__, __FILE__);
			$results = array();
			while($this->db->next_record()){
				$location_id = $this->db->f('location_id', true);
				$results[$location_id] = $this->db->f('title', true);
			}
			$this->fields_of_responsibility = $results;
		}
		return $this->fields_of_responsibility;
	}

	function get_default_account(int $location_id, bool $in){
		if(isset($location_id) && $location_id > 0)
		{
			if($in)
			{
				$col = 'account_in';
			}
			else
			{
				$col = 'account_out';
			}

			$sql = "SELECT {$col} FROM rental_contract_responsibility WHERE location_id = {$location_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f($col,true);
		}
		return '';
	}

	function get_default_project_number(int $location_id)
	{
		if(isset($location_id) && $location_id > 0)
		{
			$sql = "SELECT project_number FROM rental_contract_responsibility WHERE location_id = {$location_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('project_number',true);
		}
	}

	function get_responsibility_title(int $location_id)
	{
		if(isset($location_id) && $location_id > 0)
		{
			$sql = "SELECT title FROM rental_contract_responsibility WHERE location_id = {$location_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('title',true);
		}
	}

	/**
	 * Returns the range of year there are contracts. That is, the array
	 * returned contains reversed chronologically all the years from the earliest start
	 * year of the contracts to next year.
	 *
	 * @return array of string values, never null.
	 */
	public function get_year_range()
	{
		$year_range = array();
		$sql = "SELECT date_start FROM rental_contract ORDER BY date_start ASC";
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$first_year = (int)date('Y'); // First year in the array returned - we first set it to default this year
		if($this->db->next_record()){
			$date = $this->unmarshal($this->db->f('date_start', true), 'int');
			if($date != null && $date != '')
			{
				$first_contract_year = (int)date('Y', $date);
				if($first_contract_year < $first_year) // First contract year is before this year
				{
					$first_year = $first_contract_year;
				}
			}
		}
		$next_year = (int)date('Y', strtotime('+1 year'));
		for($year = $next_year; $year >= $first_year; $year--) // Runs through all years from next year to the first year we want
		{
			$year_range[] = $year;
		}

		return $year_range;
	}

	/**
	 * Update the database values for an existing contract object.
	 *
	 * @param $contract the contract to be updated
	 * @return result receipt from the db operation
	 */
	function update($contract)
	{
		$id = intval($contract->get_id());

		$values = array();

		// Set all fields in form

		// FORM COLUMN 1
		$values[] = "contract_type_id = ".	$this->marshal($contract->get_contract_type_id(), 'int');
		$values[] = "executive_officer = ". $this->marshal($contract->get_executive_officer_id(), 'int');

		if ($contract->get_contract_date()) {
			$values[] = "date_start = " . 	$this->marshal($contract->get_contract_date()->get_start_date(), 'int');
			$values[] = "date_end = " .		$this->marshal($contract->get_contract_date()->get_end_date(), 'int');
		}

		$values[] = "due_date = " . 		$this->marshal($contract->get_due_date(), 'int');
		$values[] = "invoice_header = ". 	$this->marshal($contract->get_invoice_header(),'string');
		$values[] = "term_id = " .			$this->marshal($contract->get_term_id(), 'int');
		$values[] = "billing_start = " . 	$this->marshal($contract->get_billing_start_date(), 'int');
		$values[] = "reference = ". 		$this->marshal($contract->get_reference(),'string');

		// FORM COLUMN 2
		$values[] = "service_id = ". 		$this->marshal($contract->get_service_id(),'string');
		$values[] = "responsibility_id = ". $this->marshal($contract->get_responsibility_id(),'string');
		$values[] = "account_in = ".		$this->marshal($contract->get_account_in(),'string');
		$values[] = "account_out = ".		$this->marshal($contract->get_account_out(),'string');
		$values[] = "project_id = ".		$this->marshal($contract->get_project_id(),'string');
		$values[] = "security_type = " . 	$this->marshal($contract->get_security_type(), 'int');
		$values[] = "security_amount = " . 	$this->marshal($contract->get_security_amount(), 'string');
		$values[] = "rented_area = ".		$this->marshal($contract->get_rented_area(),'float');
		$values[] = "adjustable = ".		($contract->is_adjustable() ? "true" : "false");
		$values[] = "adjustment_interval = ".		$this->marshal($contract->get_adjustment_interval(),'int');
		$values[] = "adjustment_share = ".		$this->marshal($contract->get_adjustment_share(),'int');
		$values[] = "publish_comment = ".	($contract->get_publish_comment() ? "true" : "false");

		// FORM COLUMN 3
		$values[] = "comment = ". 			$this->marshal($contract->get_comment(), 'string');


		// Set date last updated
		$values[] = "last_updated = ".		strtotime('now');

		$result = $this->db->query('UPDATE rental_contract SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

		if(isset($result))
		{
			$this->last_edited_by($id);
			return true;
		}

		return false;
	}

	/**
	 * This method marks the combination contract/user account with the current timestamp. It updates the record if the user has updated
	 * this contract before; inserts a new record if the user has never updated this contract.
	 *
	 * @param $contract_id
	 * @return true if the contract was marker, false otherwise
	 */
	public function last_edited_by($contract_id){
		$account_id = $GLOBALS['phpgw_info']['user']['account_id']; // current user
		$ts_now = strtotime('now');

		$sql_has_edited_before = "SELECT account_id FROM rental_contract_last_edited WHERE contract_id = $contract_id AND account_id = $account_id";
		$result = $this->db->query($sql_has_edited_before);

		if(isset($result))
		{
			if($this->db->next_record())
			{
				$sql = "UPDATE rental_contract_last_edited SET edited_on=$ts_now WHERE contract_id = $contract_id AND account_id = $account_id";
			}
			else
			{
				$sql = "INSERT INTO rental_contract_last_edited VALUES ($contract_id,$account_id,$ts_now)";
			}
			$result = $this->db->query($sql);
			if(isset($result))
			{
				return true;
			}
		}
		return false;
	}

	public function remove_Last_edited_by_information()
	{
		$sql = "DELETE * FROM rental_contract_last_edited";
		$this->db->query($sql);
	}

	public function get_last_edited_by($contract_id)
	{
		$sql = "SELECT account_id FROM rental_contract_last_edited where contract_id={$contract_id} ORDER by edited_on DESC";
		$result = $this->db->limit_query($sql,0,null,null,1);
		if(isset($result))
		{
			if($this->db->next_record())
			{
				$account_id = $this->db->f("account_id");
			}
			return $account_id;
		}
		return "";
	}

	/**
	 * This method markw the given contract with the current timestamp
	 *
	 * @param $contract_id
	 * @return true if the contract was marked, false otherwise
	 */
	public function last_updated($contract_id){
		$ts_now = strtotime('now');
		$sql = "UPDATE rental_contract SET last_updated=$ts_now where id=$contract_id";
		$result = $this->db->query($sql);
		if(isset($result))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Add a new contract to the database.  Adds the new insert id to the object reference.
	 *
	 * @param $contract the contract to be added
	 * @return array result receipt from the db operation
	 */
	function add(&$contract)
	{

        $contract->set_id(self::get_new_id($contract->get_old_contract_id()));

        // Contract has no old or new ID, get next ID available from DB
        if($this->marshal($contract->get_id(), 'int') == 0) {
            $new_id = $this->db->next_id('rental_contract');
            $contract->set_id($new_id);
        }

		// These are the columns we know we have or that are nullable
		$cols = array('location_id', 'term_id');//

		// Start making a db-formatted list of values of the columns we have to have
		$values = array(
			$this->marshal($contract->get_location_id(), 'int'),
			$this->marshal($contract->get_term_id(), 'int')
		);

        // Set ID according to old contract id or generate a new one
        $cols[] = 'id';
        $values[] = $new_id ? $new_id : $this->marshal($contract->get_id(), 'int');



		// Check values that can be null before trying to add them to the db-pretty list
		if ($contract->get_billing_start_date()) {
			$cols[] = 'billing_start';
			$values[] = $this->marshal($contract->get_billing_start_date(), 'int');
		}

		if ($contract->get_contract_date()) {
			$cols[] = 'date_start';
			$cols[] = 'date_end';
			$values[] = $this->marshal($contract->get_contract_date()->get_start_date(), 'int');
			$values[] = $this->marshal($contract->get_contract_date()->get_end_date(), 'int');
		}

		if($contract->get_executive_officer_id()) {
			$cols[] = 'executive_officer';
			$values[] = $this->marshal($contract->get_executive_officer_id(), 'int');
		}

		$cols[] = 'created';
		$cols[] = 'created_by';
		$values[] = strtotime('now');
		$values[] = $GLOBALS['phpgw_info']['user']['account_id'];


		$cols[] = 'service_id';
		$cols[] = 'responsibility_id';
		$values[] = $this->marshal($contract->get_service_id(),'string');
		$values[] = $this->marshal($contract->get_responsibility_id(),'string');

		$cols[] = 'reference';
		$cols[] = 'invoice_header';
		$values[] = $this->marshal($contract->get_reference(),'string');
		$values[] = $this->marshal($contract->get_invoice_header(),'string');

		$cols[] = 'account_in';
		$cols[] = 'account_out';
		$values[] = $this->marshal($contract->get_account_in(),'string');
		$values[] = $this->marshal($contract->get_account_out(),'string');

		$cols[] = 'project_id';
		$values[] = $this->marshal($contract->get_project_id(),'string');

		$cols[] = 'old_contract_id';
        $values[] = $new_id ? $this->marshal(self::get_old_id($new_id),'string') : $this->marshal($contract->get_old_contract_id(),'string');

        $cols[] = 'rented_area';
        $values[] =  $this->marshal($contract->get_rented_area(),'float');

		$cols[] = 'comment';
		$values[] = $this->marshal($contract->get_comment(),'string');

		$cols[] = 'adjustment_interval';
		$values[] = $this->marshal($contract->get_adjustment_interval(),'int');

		$cols[] = 'adjustment_share';
		$values[] = $this->marshal($contract->get_adjustment_share(),'int');

		$cols[] = 'adjustable';
		$values[] = ($contract->get_adjustable() ? "true" : "false");

		$cols[] = 'adjustment_year';
		$values[] = $this->marshal($contract->get_adjustment_year(),'int');

		$cols[] = 'publish_comment';
		$values[] = ($contract->get_publish_comment() ? "true" : "false");


		if ($contract->get_security_type()) {
			$cols[] = 'security_type';
			$values[] = $this->marshal($contract->get_security_type(),'int');
			$cols[] = 'security_amount';
			$values[] = $this->marshal($contract->get_security_amount(),'string');
		}

		if ($contract->get_due_date()) {
			$cols[] = 'due_date';
			$values[] = $this->marshal($contract->get_due_date(), 'int');
		}

		if($contract->get_contract_type_id()) {
			$cols[] = 'contract_type_id';
			$values[] = $this->marshal($contract->get_contract_type_id(), 'int');
		}

		// Insert the new contract
		$q ="INSERT INTO rental_contract (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);

		return $contract;
	}

	/**
	 * This method adds a party to a contract. Updates last edited history.
	 *
	 * @param $contract_id	the given contract
	 * @param $party_id	the party to add
	 * @return true if successful, false otherwise
	 */
	function add_party($contract_id, $party_id)
	{
		$q = "INSERT INTO rental_contract_party (contract_id, party_id) VALUES ($contract_id, $party_id)";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}

	/**
	 * This method removes a party from a contract. Updates last edited history.
	 *
	 * @param $contract_id	the given contract
	 * @param $party_id	the party to remove
	 * @return true if successful, false otherwise
	 */
	function remove_party($contract_id, $party_id)
	{
		$q = "DELETE FROM rental_contract_party WHERE contract_id = $contract_id AND party_id = $party_id";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}

	/**
	 * This method adds a composite to a contract. Updates last edited history.
	 *
	 * @param $contract_id	the given contract
	 * @param $composite_id	the composite to add
	 * @return true if successful, false otherwise
	 */
	function add_composite($contract_id, $composite_id)
	{
		$q = "INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES ($contract_id, $composite_id)";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}

	/**
	 * This method removes a composite from a contract. Updates last edited history.
	 *
	 * @param $contract_id	the given contract
	 * @param $party_id	the composite to remove
	 * @return true if successful, false otherwise
	 */
	function remove_composite($contract_id, $composite_id)
	{
		$q = "DELETE FROM rental_contract_composite WHERE contract_id = {$contract_id} AND composite_id = {$composite_id}";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}



	/**
	 * This method sets a payer on a contract
	 *
	 * @param $contract_id	the given contract
	 * @param $party_id	the party to be the payer
	 * @return true if successful, false otherwise
	 */
	function set_payer($contract_id, $party_id)
	{
		$pid =$this->marshal($party_id, 'int');
		$cid = $this->marshal($contract_id, 'int');
		$this->db->transaction_begin();
		$q = "UPDATE rental_contract_party SET is_payer = true WHERE party_id = ".$pid." AND contract_id = ".$cid;
		$result = $this->db->query($q);
		$q1 = "UPDATE rental_contract_party SET is_payer = false WHERE party_id != ".$pid." AND contract_id = ".$cid;
		$result1 = $this->db->query($q1);
		if($result && $result1)
		{
			$this->db->transaction_commit();
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		else
		{
			$this->db->transaction_abort();
		}
		return false;
	}

    /**
     * Convert old contract ID to new format
     *
     * @param $cid Old contract ID
     * @return int New contract ID
     */
    public static function get_new_id($old) {
        return (int) preg_replace('/[a-z]/i', '', $old);
    }

    /**
     * Get new contract ID in "old" format
     *
     * @param $cid New contract ID
     * @return string "Old" contract ID
     */
    public static function get_old_id($cid, $prefix = 'K', $digits = 8) {
        $length = strlen(''.$cid);

        while($length != $digits) {
            if($digits < $length) {
                // If number of digits is lower that current length, this will loop forever, return null to stop it.
                return null;
            }
            $cid = '0'.$cid;
            $length = strlen(''.$cid);
        }

        return $prefix.$cid;
    }

    public function get_contract_types($location_id){
    	$q1="SELECT rct.id, rct.label FROM rental_contract_types rct, rental_contract_responsibility rcr WHERE rcr.location_id={$location_id} AND rct.responsibility_id=rcr.id";
		$this->db->query($q1, __LINE__, __FILE__);
		$results = array();
		while($this->db->next_record()){
			$results[$this->db->f('id')] = $this->db->f('label');
		}

		return $results;
    }

    public function get_contract_type_label($contract_type_id){
    	$result = "Ingen";
    	if(isset($contract_type_id)){
	    	$q1="SELECT rct.label FROM rental_contract_types rct WHERE rct.id={$contract_type_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('label');
			}
    	}

		return $result;
    }

    public function get_contract_type_account($contract_type_id)
    {
    	$q1="SELECT rct.account FROM rental_contract_types rct WHERE rct.id={$contract_type_id}";
		$this->db->query($q1, __LINE__, __FILE__);
		$results = "";
		while($this->db->next_record()){
			$result = $this->db->f('account');
		}

		return $result;
    }


    public function get_term_label($billing_term_id){
    	$q1="SELECT rbt.title FROM rental_billing_term rbt WHERE rbt.id={$billing_term_id}";
		$this->db->query($q1, __LINE__, __FILE__);
		$results = "";
		while($this->db->next_record()){
			$result = $this->db->f('title');
		}

		return $result;
    }

    public function clear_last_edited_table() {
        $q = "DELETE FROM rental_contract_last_edited";
        $this->db->query($q, '', '', true);
    }
    public function copy_contract($contract_id, $old_contract_id){
    	//queries for selecting composites, parties and price items for the contract to be copied
    	$q_composites = "SELECT composite_id FROM rental_contract_composite WHERE contract_id={$old_contract_id}";
    	$q_parties = "SELECT party_id, is_payer FROM rental_contract_party WHERE contract_id={$old_contract_id}";
    	$q_price_items = "SELECT price_item_id, title, area, count, agresso_id, is_area, price, total_price, is_one_time FROM rental_contract_price_item WHERE contract_id={$old_contract_id}";
    	$success_composites = true;
    	$success_parties = true;
    	$success_price_items = true;

    	//composites
    	$this->db->query($q_composites);
    	while($this->db->next_record()){
    		$composite_id = $this->unmarshal($this->db->f('composite_id'),'int');
    		$composite_id = $this->marshal($composite_id, 'int');
    		$sql = "INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES ({$contract_id}, {$composite_id})";
    		$result_composites = $this->db->query($sql);
    		if($result_composites){
    			//noop
    		}
    		else{
    			$success_composites = false;
    		}
    	}

    	//parties
        $this->db->query($q_parties);
    	while($this->db->next_record()){
    		$party_id = $this->unmarshal($this->db->f('party_id'),'int');
    		$party_id = $this->marshal($party_id, 'int');
    		$is_payer = $this->unmarshal($this->db->f('is_payer'),'bool');
    		$is_payer = $this->marshal($is_payer ? 'true' : 'false','bool');
    		$sql = "INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES ({$contract_id}, {$party_id}, {$is_payer})";
    		$result_parties = $this->db->query($sql);
    		if($result_parties){
    			//noop
    		}
    		else{
    			$success_parties = false;
    		}
    	}

    	//price items
        $this->db->query($q_price_items);
    	while($this->db->next_record()){
    		$price_item_id = $this->unmarshal($this->db->f('price_item_id'),'int');
    		$price_item_id = $this->marshal($price_item_id, 'int');
    		$title = $this->unmarshal($this->db->f('title'),'string');
    		$title = $this->marshal($title, 'string');
    		$area = $this->unmarshal($this->db->f('area'),'float');
    		$area = $this->marshal($area, 'float');
    		$count = $this->unmarshal($this->db->f('count'),'int');
    		$count = $this->marshal($count, 'int');
    		$agresso_id = $this->unmarshal($this->db->f('agresso_id'),'string');
    		$agresso_id = $this->marshal($agresso_id, 'string');
    		$is_area = $this->unmarshal($this->db->f('is_area'),'bool');
    		$is_area = $this->marshal($is_area ? 'true' : 'false','bool');
    		$price = $this->unmarshal($this->db->f('price'),'float');
    		$price = $this->marshal($price, 'float');
    		$total_price = $this->unmarshal($this->db->f('total_price'),'float');
    		$total_price = $this->marshal($total_price, 'float');
    		$is_one_time = $this->unmarshal($this->db->f('is_one_time'),'bool');
    		$is_one_time = $this->marshal($is_one_time ? 'true' : 'false','bool');
    		$sql = "INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, is_one_time, date_start, date_end) VALUES ({$price_item_id}, {$contract_id}, {$title}, {$area}, {$count}, {$agresso_id}, {$is_area}, {$price}, {$total_price}, {$is_one_time}, null, null)";
    		$result_price_items = $this->db->query($sql);
    		if($result_price_items){
    			//noop
    		}
    		else{
    			$success_price_items = false;
    		}
    	}
//    	var_dump($success_composites.' '.$success_parties.' '.$success_price_items);
    	if($success_composites && $success_parties && $success_price_items){
    		return true;
    	}
    	else{
    		return false;
    	}
    }

    public function get_months_in_term($term_id)
    {
		$sql = "SELECT months FROM rental_billing_term WHERE id = {$term_id}";
		$result = $this->db->query($sql);
		if(!$result)
		{
			return 0;
		}
		if(!$this->db->next_record())
		{
			return 0;
		}
		$months = $this->unmarshal($this->db->f('months', true), 'int');
		return $months;
    }

    public function update_price_items($contract_id, $rented_area){
    	$success_price_item = true;
    	$new_area = $rented_area;
    	$q_price_items = "SELECT id AS rpi_id, price as rpi_price FROM rental_contract_price_item WHERE contract_id={$contract_id} AND is_area";
    	$res1 = $this->db->query($q_price_items, __LINE__, __FILE__,false,true);
    	while($this->db->next_record()){
    		$id = $this->db->f('rpi_id');
    		$price = $this->db->f('rpi_price');
    		$curr_total_price = ($new_area * $price);
    		$sql_pi = "UPDATE rental_contract_price_item SET area={$new_area}, total_price={$curr_total_price} WHERE id={$id}";
    		$result = $this->db->query($sql_pi, __LINE__, __FILE__,false,true);
    		if($result){
    			//noop
    		}
    		else{
    			$success_price_item = false;
    		}
    	}
    	if($success_price_item){
    		return true;
    	}
    	else{
    		return false;
    	}
    }

    public function import_contract_reference($contract_id, $reference)
    {
    	$reference = $this->marshal($reference,'string');
    	$sql = "UPDATE rental_contract SET reference={$reference} WHERE id = {$contract_id}";
    	$this->db->query($sql);
    }

    public function update_adjustment_year_interval($contract_id, $adjusted_year, $adjustment_interval)
    {
    	$new_adjusted_year = $this->marshal($adjusted_year, 'int');
    	$new_adjustment_interval = $this->marshal($adjustment_interval, 'int');
    	$sql = "UPDATE rental_contract SET adjustable=true, adjustment_interval={$new_adjustment_interval}, adjustment_year={$new_adjusted_year} WHERE id = {$contract_id} AND (adjustment_year IS NULL OR adjustment_year<{$new_adjusted_year})";
    	$this->db->query($sql);
    	return $this->db->affected_rows() > 0 ? true : false;
    }

    public function update_contract_end_date($contract_id, $date)
    {
    	$cid = $this->marshal($contract_id, 'int');
    	$end_date = $this->marshal($date, 'int');
    	$sql = "UPDATE rental_contract SET date_end={$end_date} WHERE id = {$cid}";
    	$this->db->query($sql);
    }

	public function update_adjustment_year($contract_id, $adjusted_year)
    {
    	$new_adjusted_year = $this->marshal($adjusted_year, 'int');
    	$sql = "UPDATE rental_contract SET adjustment_year={$new_adjusted_year} WHERE id={$contract_id} AND(adjustment_year IS NULL OR adjustment_year<{$new_adjusted_year})";
    	$this->db->query($sql);
    	return $this->db->affected_rows() > 0 ? true : false;
    }

    public function update_adjustment_share($contract_id, $adjustment_share)
    {
    	$new_adjustment_share = $this->marshal($adjustment_share, 'int');
    	$sql = "UPDATE rental_contract SET adjustment_share={$new_adjustment_share} WHERE id = {$contract_id}";
    	$this->db->query($sql);
    }

    public function get_default_price_items($location_id)
    {
    	$price_items = array();
    	$loc_id = $this->marshal($location_id, 'int');

    	//select all standard price_items for given location_id
    	$sql = "SELECT id FROM rental_price_item WHERE responsibility_id={$loc_id} AND NOT is_inactive AND standard";
    	$this->db->query($sql);
    	while($this->db->next_record())
    	{
    		$price_item_id = $this->unmarshal($this->db->f('id'),'int');
    		$price_items[] = $price_item_id;
    	}
    	return $price_items;
    }

}
?>
