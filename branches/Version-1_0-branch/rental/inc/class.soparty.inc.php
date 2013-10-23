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
phpgw::import_class('rental.bofellesdata');

include_class('rental', 'agresso_cs15', 'inc/model/');
include_class('rental', 'party', 'inc/model/');
include_class('rental', 'location_hierarchy', 'inc/locations/');
include_class('rental', 'result_unit', 'inc/locations/');

class rental_soparty extends rental_socommon
{
	const LOCATION_ROOT = '.';
	const LOCATION_IN = '.RESPONSIBILITY.INTO';
	const LOCATION_OUT = '.RESPONSIBILITY.OUT';
	const LOCATION_INTERNAL = '.RESPONSIBILITY.INTERNAL';

	protected static $so;

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return rental_soparty the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.soparty');
		}
		return self::$so;
	}

	/**
	 * Generate SQL query
	 *
	 * @todo Add support for filter "party_type", meaning what type of contracts
	 * the party is involved in.
	 *
	 * @param string $sort_field
	 * @param boolean $ascending
	 * @param string $search_for
	 * @param string $search_type
	 * @param array $filters
	 * @param boolean $return_count
	 * @return string SQL
	 */
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		$clauses = array('1=1');

		//Add columns to this array to include them in the query
		$columns = array();

		if($sort_field != null) {
			$dir = $ascending ? 'ASC' : 'DESC';
			if($sort_field == 'name')
			{
				$order = "ORDER BY party.last_name {$dir}, party.first_name {$dir}";
			}
			else
			{
				if($sort_field == 'address')
				{
					$sort_field = 'party.address_1';
				}
				$order = "ORDER BY {$this->marshal($sort_field,'field')} $dir";
			}
		}
		if($search_for)
		{
			$query = $this->marshal($search_for,'string');
			$like_pattern = "'%".$search_for."%'";
			$like_clauses = array();
			switch($search_type){
				case "name":
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					break;
				case "address":
					$like_clauses[] = "party.address_1 $this->like $like_pattern";
					$like_clauses[] = "party.address_2 $this->like $like_pattern";
					$like_clauses[] = "party.postal_code $this->like $like_pattern";
					$like_clauses[] = "party.place $this->like $like_pattern";
					break;
				case "identifier":
					$like_clauses[] = "party.identifier $this->like $like_pattern";
					break;
				case "reskontro":
					$like_clauses[] = "party.reskontro $this->like $like_pattern";
					break;
				case "result_unit_number":
					$like_clauses[] = "party.result_unit_number $this->like $like_pattern";
					break;
				case "all":
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					$like_clauses[] = "party.address_1 $this->like $like_pattern";
					$like_clauses[] = "party.address_2 $this->like $like_pattern";
					$like_clauses[] = "party.postal_code $this->like $like_pattern";
					$like_clauses[] = "party.place $this->like $like_pattern";
					$like_clauses[] = "party.identifier $this->like $like_pattern";
					$like_clauses[] = "party.comment $this->like $like_pattern";
					$like_clauses[] = "party.reskontro $this->like $like_pattern";
					break;
			}


			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}

		$filter_clauses = array();

		if(isset($filters[$this->get_id_field_name()])){
			$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			$filter_clauses[] = "party.id = {$id}";
		}

		// All parties with contracts of type X
		if(isset($filters['party_type']))
		{
			$party_type = $this->marshal($filters['party_type'],'int');
			if(isset($party_type) && $party_type > 0)
			{
				$filter_clauses[] = "contract.location_id = {$party_type}";
			}
		}

		if(isset($filters['active']))
		{
			if($filters['active'] == 'active')
			{
				$filter_clauses[] = "NOT party.is_inactive";
			}
			else if($filters['active'] == 'inactive')
			{
				$filter_clauses[] = "party.is_inactive = TRUE";
			}
		}

		if(isset($filters['contract_id']))
		{
			$contract_id = $this->marshal($filters['contract_id'],'int');
			if(isset($contract_id) && $contract_id > 0)
			{
				$filter_clauses[] = "c_p.contract_id = {$contract_id}";
			}
		}

		if(isset($filters['not_contract_id']))
		{
			$contract_id = $this->marshal($filters['not_contract_id'],'int');
			if(isset($contract_id) && $contract_id > 0)
			{
				$filter_clauses[] = "party.id NOT IN (SELECT party_id FROM rental_contract_party WHERE contract_id = {$contract_id} OR contract_id IS NULL) AND NOT party.is_inactive";
			}
		}

		if(isset($filters['org_unit_id']))
		{
			$bofelles = rental_bofellesdata::get_instance();
			$org_unit_id = $this->marshal($filters['org_unit_id'],'string');
			if(isset($org_unit_id))
			{
				//check if org_unit is on top level
				if($bofelles->org_unit_is_top_level($org_unit_id))
				{
					//get connected units on level 4
					$org_unit_ids_tmp = $bofelles->get_org_unit_ids_from_top($org_unit_id);
					$org_unit_ids = implode(',',$org_unit_ids_tmp);
					$filter_clauses[] = "party.org_enhet_id IN ({$org_unit_ids})";
				}
				else
				{
					$filter_clauses[] = "party.org_enhet_id = {$org_unit_id}";
				}
			}
		}

		if(isset($filters['email'])){
			$email = $this->marshal($filters['email'],'string');
			if(isset($email))
			{
				$filter_clauses[] = "party.email = {$email}";
			}
		}

		if(isset($filters['sync']))
		{
			$filter_clauses[] = "NOT party.is_inactive";

			if($filters['sync'] == 'sync_parties')
			{
				// involved in contract with service- and responsibility identifiers
				$filter_clauses[] = "NOT contract.responsibility_id IS NULL";
				$filter_clauses[] = "party.org_enhet_id IS NULL";
				// involved in active contracts
				$ts_query = strtotime(date('Y-m-d')); // timestamp for query (today)
				$filter_clauses[] = "(NOT contract.date_start IS NULL AND contract.date_start < $ts_query AND (contract.date_end IS NULL OR (NOT contract.date_end IS NULL AND contract.date_end > $ts_query)))";
			}
			else if($filters['sync'] == 'sync_parties_res_unit')
			{
				$filter_clauses[] = "NOT party.result_unit_number IS NULL";
				$filter_clauses[] = "party.result_unit_number LIKE '____'";
				$filter_clauses[] = "NOT party.result_unit_number LIKE 'KF__'";
				$filter_clauses[] = "contract.service_id IS NULL";
				$filter_clauses[] = "contract.responsibility_id IS NULL";
			}
			else if($filters['sync'] == 'sync_parties_org_unit')
			{
				$filter_clauses[] = "NOT party.org_enhet_id IS NULL";
			}
			else if($filters['sync'] == 'sync_parties_identifier')
			{
				$filter_clauses[] = "NOT party.identifier IS NULL";
				$filter_clauses[] = "party.identifier LIKE '____'";
				$filter_clauses[] = "NOT party.identifier LIKE 'KF__'";
				$filter_clauses[] = "contract.service_id IS NULL";
				$filter_clauses[] = "contract.responsibility_id IS NULL";
				$filter_clauses[] = "party.result_unit_number IS NULL";
			}
		}

		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(party.id)) AS count';
		}
		else
		{
			$columns[] = 'party.id AS party_id';
			$columns[] = 'party.identifier';
			$columns[] = 'party.first_name';
			$columns[] = 'party.last_name';
			$columns[] = 'party.comment';
			$columns[] = 'party.is_inactive';
			$columns[] = 'party.title';
			$columns[] = 'party.company_name';
			$columns[] = 'party.department';
			$columns[] = 'party.address_1';
			$columns[] = 'party.address_2';
			$columns[] = 'party.postal_code';
			$columns[] = 'party.place';
			$columns[] = 'party.phone';
			$columns[] = 'party.mobile_phone';
			$columns[] = 'party.fax';
			$columns[] = 'party.email';
			$columns[] = 'party.url';
			$columns[] = 'party.account_number';
			$columns[] = 'party.reskontro';
			$columns[] = 'party.location_id as org_location_id';
			$columns[] = 'party.result_unit_number';
			$columns[] = 'party.org_enhet_id';
			$columns[] = 'contract.location_id as resp_location_id';
			$columns[] = 'contract.service_id';
			$columns[] = 'contract.responsibility_id';
			$cols = implode(',',$columns);
		}

		$tables = "rental_party party";

		$join_contracts = "	{$this->left_join} rental_contract_party c_p ON (c_p.party_id = party.id)
		{$this->left_join} rental_contract contract ON (contract.id = c_p.contract_id)";

		$joins = $join_contracts;

		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}



	/**
	 * Function for adding a new party to the database. Updates the party object.
	 *
	 * @param rental_party $party the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$party)
	{
		// Insert a new party
		$q ="INSERT INTO rental_party (is_inactive) VALUES (false)";
		$result = $this->db->query($q);

		if(isset($result)) {
			// Set the new party ID
			$party->set_id($this->db->get_last_insert_id('rental_party', 'id'));
			// Forward this request to the update method
			return $this->update($party);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update the database values for an existing party object.
	 *
	 * @param $party the party to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update($party)
	{
		$id = intval($party->get_id());


		$location_id = $this->marshal($party->get_location_id(), 'int');

		if($location_id)
		{
			$loc = $GLOBALS['phpgw']->locations->get_name($location_id);
			$name = $loc['location'];
			$level_identifier = result_unit::get_identifier_from_name($name);
		}

		$result_unit_number = $this->marshal($level_identifier, 'string');

		$values = array(
			'identifier = '		. $this->marshal($party->get_identifier(), 'string'),
			'first_name = '     . $this->marshal($party->get_first_name(), 'string'),
			'last_name =  '     . $this->marshal($party->get_last_name(), 'string'),
			'title = '          . $this->marshal($party->get_title(), 'string'),
			'company_name = '   . $this->marshal($party->get_company_name(), 'string'),
			'department = '     . $this->marshal($party->get_department(), 'string'),
			'address_1 = '      . $this->marshal($party->get_address_1(), 'string'),
			'address_2 = '      . $this->marshal($party->get_address_2(), 'string'),
			'postal_code = '    . $this->marshal($party->get_postal_code(), 'string'),
			'place = '          . $this->marshal($party->get_place(), 'string'),
			'phone = '          . $this->marshal($party->get_phone(), 'string'),
			'mobile_phone = '	. $this->marshal($party->get_mobile_phone(), 'string'),
			'fax = '            . $this->marshal($party->get_fax(), 'string'),
			'email = '          . $this->marshal($party->get_email(), 'string'),
			'url = '            . $this->marshal($party->get_url(), 'string'),
			'account_number = ' . $this->marshal($party->get_account_number(), 'string'),
			'reskontro = '      . $this->marshal($party->get_reskontro(), 'string'),
			'is_inactive = '    . $this->marshal(($party->is_inactive() ? 'true' : 'false'), 'bool'),
			'comment = '        . $this->marshal($party->get_comment(), 'string'),
			'org_enhet_id = '	. $this->marshal($party->get_org_enhet_id(), 'int'),
			'unit_leader = '	. $this->marshal($party->get_unit_leader(), 'string'),
			'location_id = '	. $location_id,
			'result_unit_number = ' . $result_unit_number
		);

		$result = $this->db->query('UPDATE rental_party SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

		return isset($result);
	}

	public function get_id_field_name($extended_info = false)
	{
		if(!$extended_info)
		{
			$ret = 'party_id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'party', // alias
				'field'			=> 'id',
				'translated'	=> 'party_id'
			);
		}
		return $ret;
	}

	protected function populate(int $party_id, &$party)
	{

		if($party == null) {
			$party = new rental_party((int) $party_id);

			$party->set_account_number( $this->unmarshal($this->db->f('account_number'), 'string'));
			$party->set_address_1(      $this->unmarshal($this->db->f('address_1'), 'string'));
			$party->set_address_2(      $this->unmarshal($this->db->f('address_2'), 'string'));
			$party->set_comment(        $this->unmarshal($this->db->f('comment'), 'string'));
			$party->set_company_name(   $this->unmarshal($this->db->f('company_name'), 'string'));
			$party->set_department(     $this->unmarshal($this->db->f('department'), 'string'));
			$party->set_email(          $this->unmarshal($this->db->f('email'), 'string'));
			$party->set_fax(            $this->unmarshal($this->db->f('fax'), 'string'));
			$party->set_first_name(     $this->unmarshal($this->db->f('first_name'), 'string'));
			$party->set_is_inactive(    $this->unmarshal($this->db->f('is_inactive'), 'bool'));
			$party->set_last_name(      $this->unmarshal($this->db->f('last_name'), 'string'));
			$party->set_location_id(    $this->unmarshal($this->db->f('org_location_id'), 'int'));
			$party->set_identifier(		$this->unmarshal($this->db->f('identifier'), 'string'));
			$party->set_mobile_phone(	$this->unmarshal($this->db->f('mobile_phone'), 'string'));
			$party->set_place(          $this->unmarshal($this->db->f('place'), 'string'));
			$party->set_postal_code(    $this->unmarshal($this->db->f('postal_code'), 'string'));
			$party->set_reskontro(      $this->unmarshal($this->db->f('reskontro'), 'string'));
			$party->set_title(          $this->unmarshal($this->db->f('title'), 'string'));
			$party->set_url(            $this->unmarshal($this->db->f('url'), 'string'));
			$party->set_org_enhet_id(   $this->unmarshal($this->db->f('org_enhet_id'), 'string'));
			$party->set_unit_leader(   $this->unmarshal($this->db->f('unit_leader'), 'string'));
			$sync_message = $party->set_sync_data(
				array(
					'responsibility_id' => $this->unmarshal($this->db->f('responsibility_id'), 'string'),
					'org_enhet_id' => $this->unmarshal($this->db->f('org_enhet_id'), 'string'),
					'result_unit_number' => $this->unmarshal($this->db->f('result_unit_number'), 'string'),
				)
			);
			if(isset($sync_message) && $sync_message != '')
			{
				$party->add_sync_problem($sync_message);
			}
		}
		return $party;
	}

	public function get_export_data()
	{
		$parties = rental_soparty::get_instance()->get(null, null, null, null, null, null, null);
		$exportable = new rental_agresso_cs15($parties);
		return $exportable->get_contents();
	}

	public function get_number_of_parties()
	{
		$q ="SELECT COUNT(id) FROM rental_party";
		$result = $this->db->query($q);
		$this->db->query($q, __LINE__, __FILE__);
		$this->db->next_record();
		return (int) $this->db->f('count',true);
	}

	public function has_contract($party_id)
	{
		$sql = "SELECT * FROM rental_contract_party WHERE party_id={$party_id}";
		$result = $this->db->query($sql);
		if($this->db->next_record())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function delete_party($party_id)
	{
		if($party_id)
		{
			$sql = "DELETE FROM rental_party WHERE id={$party_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			return true;
		}
	}


 		/**
		 * Check to see if this user is an administrator
		 *
		 * @return true if private permission on root, false otherwise
		 */

		protected function isAdministrator()
		{
			return $GLOBALS['phpgw']->acl->check(self::LOCATION_ROOT,PHPGW_ACL_PRIVATE,'rental');
		}

		/**
		 * Check to see if the user is an executive officer
		 *
		 * @return true if at least add permission on fields of responsibilities (locations: .RESPONSIBIITY.*)
		 */
		protected function isExecutiveOfficer()
		{
			return (
				$GLOBALS['phpgw']->acl->check(self::LOCATION_IN,PHPGW_ACL_ADD,'rental')	||
				$GLOBALS['phpgw']->acl->check(self::LOCATION_OUT,PHPGW_ACL_ADD,'rental')	||
				$GLOBALS['phpgw']->acl->check(self::LOCATION_INTERNAL,PHPGW_ACL_ADD,'rental')
			);
		}

 	/**
 	 * Synchronization job to update company name on contract parties which are connected to Fellesdata.
 	 *
 	 * Uses property org_enhet_id on party to link party with unit from Fellesdata.
 	 * To be run as a scheduled job
 	 */
 	function syncronize_party_name()
 	{
 		$config	= CreateObject('phpgwapi.config','rental');
		$config->read();

		$use_fellesdata = $config->config_data['use_fellesdata'];
		if(!$use_fellesdata){
			return;
		}
		$bofelles = rental_bofellesdata::get_instance();

		$parties = rental_soparty::get_instance()->get();
		$result_count = rental_soparty::get_instance()->get_count();
		$updated_parties;

		$updated_parties[] = "Total number of parties: {$result_count}";

		if(($this->isExecutiveOfficer() || $this->isAdministrator()))
		{
			$count = 0;
			$count_result_unit_number = 0;
			$count_identifier = 0;
			$count_responsibility = 0;

			foreach ($parties as $party) {
				$unit_found = false;
				$fellesdata = NULL;

				if(isset($party)) {
					$sync_data = $party->get_sync_data();

					$fellesdata = $bofelles->result_unit_exist($sync_data['result_unit_number'],4);
					if ($fellesdata) {
						$updated_parties[] = "Unit id found {$fellesdata['UNIT_ID']} by result unit number check. The unit name is {$fellesdata['UNIT_NAME']}<br />";
						$count_result_unit_number++;
					}

					if ($fellesdata && isset($fellesdata['UNIT_ID']) && is_numeric($fellesdata['UNIT_ID'])) {
						// We found a match, so store the new connection
						$party->set_org_enhet_id($fellesdata['UNIT_ID']);
						$old_party_name = $party->get_company_name();
						$party->set_company_name($fellesdata['UNIT_NAME']);
						$updated_parties[] = "Updated company name on party {$party->get_id()} with unit ID {$party->get_org_enhet_id} from {$old_party_name} to {$party->get_company_name()}";
						$count++;
					} else {
						// No match was found. Do nothing
						//$party->set_org_enhet_id(NULL);
					}
					rental_soparty::get_instance()->store($party);
				}
			}

			$updated_parties[] = "Number of parties found through result unit number {$count_result_unit_number}";
			$updated_parties[] = "Number of parties that have been updated {$count}";
			$this->log_sync_messages($updated_parties);
		}
 	}

	private function log_sync_messages($messages)
	{
		$msgs = array_merge(
			array('---------------Messages-------------------'),
			$messages
		);

		//use PHPGW tmp-catalog to store log-file
		$path = $GLOBALS['phpgw_info']['server']['temp_dir'];

		//Write to the log-file
		$date_now = date('Y-m-d');
		file_put_contents("{$path}/FD_name_sync_{$date_now}.log", implode(PHP_EOL, $msgs));
	}

}

