<?php

phpgw::import_class('activitycalendar.socommon');

include_class('activitycalendar', 'organization', 'inc/model/');
include_class('activitycalendar', 'contact_person', 'inc/model/');

class activitycalendar_soorganization extends activitycalendar_socommon
{
	protected static $so;

	var $public_functions = array
	(
		'fix_duplicates'  		=> true,
	);

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return rental_soparty the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('activitycalendar.soorganization');
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

		if($sort_field != null && !$return_count) {
			if($sort_field == 'identifier')
			{
				$sort_field = 'org.id';
			}
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = "ORDER BY $sort_field $dir";
		}
		else if(!$return_count)
		{
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = "ORDER BY org.name $dir";
		}
		if($search_for)
		{
			$query = $this->marshal($search_for,'string');
			$like_pattern = "'%".$search_for."%'";
			$like_clauses = array();
			switch($search_type){
				case "name":
					$like_clauses[] = "org.name $this->like $like_pattern";
					$like_clauses[] = "org.shortname $this->like $like_pattern";
					break;
				case "org_id":
					$like_clauses[] = "org.organization_number $this->like $like_pattern";
					break;
				case "district":
					$like_clauses[] = "org.district $this->like $like_pattern";
					break;
				default:
					$like_clauses[] = "org.name $this->like $like_pattern";
					break;
			}


			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}

		$filter_clauses = array();
		$filter_clauses[] = "show_in_portal=1";
		$filter_clauses[] = "NOT org.name=''";

		$use_local_org = false;

		if(isset($filters[$this->get_id_field_name()])){
			$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			$filter_clauses[] = "org.id = {$id}";
		}
		if(isset($filters['changed_orgs'])){
			$use_local_org = true;
			//$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			//$filter_clauses[] = "org.id = {$id}";
			unset($filter_clauses);
			if(isset($filters[$this->get_id_field_name()])){
				$id = $this->marshal($filters[$this->get_id_field_name()],'int');
				$filter_clauses[] = "org.id = {$id}";
			}
		}
		if(isset($filters['new_orgs'])){
			$use_local_org = true;
			//$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			//$filter_clauses[] = "org.id = {$id}";
			unset($filter_clauses);
			$filter_clauses[] = "org.change_type = 'new' OR org.change_type = 'change' ";
			if(isset($filters[$this->get_id_field_name()])){
				$id = $this->marshal($filters[$this->get_id_field_name()],'int');
				$filter_clauses[] = "org.id = {$id}";
			}
		}
		if(isset($filters['edit_from_frontend']))
		{
			$filter_clauses[] = "org.id IN (SELECT organization_id from activity_activity where state = 3 OR state = 4)";
		}

		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		if($use_local_org)
		{
			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(org.id)) AS count';
			}
			else
			{
				$columns[] = 'org.id';
				$columns[] = 'org.name';
				$columns[] = 'org.homepage';
				$columns[] = 'org.phone';
				$columns[] = 'org.email';
				$columns[] = 'org.description';
				$columns[] = 'org.address';
                                $columns[] = 'org.addressnumber';
				$columns[] = 'org.zip_code';
				$columns[] = 'org.city';
				$columns[] = 'org.district';
				$columns[] = 'org.change_type';
				$columns[] = 'org.transferred';
				$columns[] = 'org.original_org_id';
				$columns[] = 'org.orgno AS organization_number';

				$cols = implode(',',$columns);
			}

			$tables = "activity_organization org";
		}
		else
		{
			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(org.id)) AS count';
			}
			else
			{
				$columns[] = 'org.id';
				$columns[] = 'org.name';
				$columns[] = 'org.homepage';
				$columns[] = 'org.phone';
				$columns[] = 'org.email';
				$columns[] = 'org.description';
				$columns[] = 'org.active';
				$columns[] = 'org.street AS address';
				$columns[] = 'org.zip_code';
				$columns[] = 'org.city';
				$columns[] = 'org.district';
				$columns[] = 'org.organization_number';
				$columns[] = 'org.activity_id';
				$columns[] = 'org.customer_number';
				$columns[] = 'org.customer_identifier_type';
				$columns[] = 'org.customer_organization_number';
				$columns[] = 'org.customer_ssn';
				$columns[] = 'org.customer_internal';
				$columns[] = 'org.shortname';
				$columns[] = 'org.show_in_portal';

				$cols = implode(',',$columns);
			}

			$tables = "bb_organization org";
		}

		//var_dump("SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}");
		return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
	}

	function get_organization_name($org_id)
	{
		$result = "Ingen";
    	if(isset($org_id)){
	        $org_id = intval($org_id);
    	    $q1="SELECT name FROM bb_organization WHERE id={$org_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name');
			}
    	}

		return $result;
	}

	function get_duplicates()
	{
		$result = array();
		//$q1= "select bb.id as orgid, bb.name as orgname, bb.organization_number as orgno, bb.street as orgstreet, bb.zip_code as zip, bb.city, cp.* from bb_organization bb, bb_organization_contact cp where cp.organization_id=bb.id and bb.show_in_portal=1 and bb.active=1 order by bb.name, bb.id";
		$q1= "select bb.id as orgid, bb.name as orgname, bb.organization_number as orgno, bb.street as orgstreet, bb.zip_code as zip, bb.city from bb_organization bb where bb.show_in_portal=1 and bb.active=1 order by bb.name, bb.id";
//	    	$q1="SELECT name FROM bb_organization WHERE id={$org_id}";
		$this->db->query($q1, __LINE__, __FILE__);
		while($this->db->next_record()){
			//if($org->get_name() != $this->db->f('orgname')) //new organization
			//{
/*				$org = new activitycalendar_organization();
				$org->set_id($this->db->f('orgid'));
				$org->set_name($this->db->f('orgname'));
				$org->set_address($this->db->f('orgstreet').', '.$this->db->f('zip').' '.$this->db->f('city'));
				$org->set_organization_number($this->db->f('orgno'));
*/
				$result[$this->db->f('orgid')] = array(
					'orgid' => $this->db->f('orgid'),
					'orgname' => $this->db->f('orgname'),
					'orgstreet' =>  $this->db->f('orgstreet'),
					'zip' => $this->db->f('zip'),
					'city' => $this->db->f('city'),
					'orgno' => $this->db->f('orgno')
				);
			//}
			//else if (isset($this->db->f('orgstreet')) && $this->db->f('orgstreet') != '')
			//{
			//	$org->set_address($this->db->f('orgstreet').', '.$this->db->f('zip').' '.$this->db->f('city'));
			//}
			//$result[] = $org;
		}
		//_debug_array($result);
		return $result;
	}

	function fix_duplicates()
	{
		$so_activity = CreateObject('activitycalendar.soactivity');
		$orgs1 = $this->get_duplicates();
		$orgs2 = $this->get_duplicates();
		$new_orgs = array();
		$removed_orgs = array();
		$orgmappings = array();
		foreach($orgs1 as $org)
		{
			$tmpName = $org['orgname'];
			$curr_id = $org['orgid'];
			if(!in_array($curr_id, $removed_orgs))
			{
				foreach($orgs2 as $o2)
				{
					$removeId = $o2['orgid'];
					//var_dump($removeId .':'.$curr_id . '<br/>');
					if($removeId != $curr_id && $o2['orgname'] == $tmpName)
					{
						//var_dump($removeId.'-' . $o2['orgname'].' skal fjernes <br/>');
						//update previous instance
						$org['orgstreet'] = $o2['orgstreet'];
						$org['zip'] = $o2['zip'];
						$org['city'] = $o2['city'];
						$org['removed_org'] = $removeId;
						$removed_orgs[] = $removeId;
						$orgmappings[$curr_id][] = $removeId;

						//unset($orgs1[$removeId]);
					}
				}
				$new_orgs[] = $org;
			}
		}
//		_debug_array($new_orgs);
//		_debug_array($orgmappings);

		$this->db->transaction_begin();
		//loop through activities and update organization-connection
		foreach($orgmappings as $key => $value)
		{
			foreach($value as $orgmapping)
			{
//				var_dump($orgmapping.' skal flyttes til '.$key.'<br/>');
				//get activity connected to current orgid
				$activities = $so_activity->get_connected_activities($orgmapping);
				foreach($activities as $activity)
				{
					var_dump($activity->get_title().' flyttes fra '.$orgmapping.' til '.$key.'</br>');
					$so_activity->update_organization_connection($activity->get_id(), $key);
				}
				var_dump("Oppdaterer organisasjon ".$orgmapping.', settes til inaktiv.<br/>');
				$this->set_organization_inactive($orgmapping);
				//get affected stuff from booking
				$alloc = $this->get_affected_allocations($orgmapping);
				foreach($alloc as $a)
				{
					var_dump('Allocation id: '.$a.' flyttes fra '.$orgmapping.' til '.$key.'</br>');
					$this->update_affected_allocations($a, $key);
				}

				$res = $this->get_affected_reservations($orgmapping);
				foreach($res as $r)
				{
					var_dump('Reservation id: '.$r.' flyttes fra '.$orgmapping.' til '.$key.'</br>');
					$this->update_affected_reservations($r, $key);
				}
				$event = $this->get_affected_events($orgmapping);
				foreach($event as $e)
				{
					var_dump('Event id: '.$e.' flyttes fra '.$orgmapping.' til '.$key.'</br>');
					$this->update_affected_events($e, $key);
				}
			}
		}

		//loop through organizations and update them.
		foreach($new_orgs as $no)
		{
			//update organization with new information
			//_debug_array($no);
			var_dump("Oppdaterer organisasjon ".$no['orgid'].','.$no['orgname'].' med ny adresse.<br/>');
			$this->update_organization_with_new_info($no);
		}

		$this->db->transaction_commit();
	}

	function get_organization_name_local($org_id)
	{
		$result = "Ingen";
    	if(isset($org_id)){
	    	$q1="SELECT name FROM activity_organization WHERE id={$org_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name');
			}
    	}

		return $result;
	}

	function get_contacts($organization_id)
	{
		$contacts = array();
    	if(isset($organization_id)){
	    	$q1="SELECT id FROM bb_organization_contact WHERE organization_id={$organization_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$cont_id = $this->db->f('id');
				$contacts[] = $cont_id;
			}
			//$result=$contacts;
    	}
		return $contacts;
	}

	function get_contacts_as_objects($organization_id)
	{
		$contacts = array();
    	if(isset($organization_id)){
	    	$q1="SELECT * FROM bb_organization_contact WHERE organization_id={$organization_id}";
	    	//var_dump($q1);
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$contact_person = new activitycalendar_contact_person((int) $this->db->f('id'));
				$contact_person->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
				$contact_person->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
				$contact_person->set_name($this->unmarshal($this->db->f('name'), 'string'));
				$contact_person->set_phone($this->unmarshal($this->db->f('phone'), 'string'));
				$contact_person->set_email($this->unmarshal($this->db->f('email'), 'string'));
				$contacts[] = $contact_person;			}
    	}
		return $contacts;
	}


	function get_contacts_local($organization_id)
	{
		$contacts = array();
    	if(isset($organization_id)){
	    	$q1="SELECT id FROM activity_contact_person WHERE organization_id='{$organization_id}'";
	    	//var_dump($q1);
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$cont_id = $this->db->f('id');
				$contacts[] = $cont_id;
			}
			//$result=$contacts;
    	}
		return $contacts;
	}

	function get_contacts_local_as_objects($organization_id)
	{
		$contacts = array();
    	if(isset($organization_id)){
	    	$q1="SELECT * FROM activity_contact_person WHERE organization_id='{$organization_id}'";
	    	//var_dump($q1);
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$contact_person = new activitycalendar_contact_person((int) $this->db->f('id'));
				$contact_person->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
				$contact_person->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
				$contact_person->set_name($this->unmarshal($this->db->f('name'), 'string'));
				$contact_person->set_phone($this->unmarshal($this->db->f('phone'), 'string'));
				$contact_person->set_email($this->unmarshal($this->db->f('email'), 'string'));
				$contacts[] = $contact_person;
			}
    	}
		return $contacts;
	}

	function get_description($organization_id)
	{
    	if(isset($organization_id)){
	    	$q1="SELECT description FROM bb_organization WHERE id={$organization_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$desc = $this->db->f('description');
			}
    	}
		return $desc;
	}

	function get_description_local($organization_id)
	{
    	if(isset($organization_id)){
	    	$q1="SELECT description FROM activity_organization WHERE id={$organization_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$desc = $this->db->f('description');
			}
    	}
		return $desc;
	}


	function get_district_from_name($name)
	{
		$this->db->query("SELECT part_of_town_id FROM fm_part_of_town where name like UPPER('%{$name}%') ", __LINE__, __FILE__);
		while($this->db->next_record()){
			$result = $this->db->f('part_of_town_id');
		}
		return $result;
	}

	function get_office_from_district($district_id)
	{
		if($district_id)
		{
			$district_id = (int)$district_id;
			$q1="SELECT fm_district.descr FROM fm_part_of_town,fm_district WHERE fm_part_of_town.part_of_town_id={$district_id} AND fm_district.id = fm_part_of_town.district_id";
			//var_dump($q1);
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$office_name = $this->db->f('descr');
			}
		}
		return $office_name;
	}

	/**
	 * Function for adding a new party to the database. Updates the party object.
	 *
	 * @param rental_party $party the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$organization)
	{
		return false;
	}

	/**
	 * Update the database values for an existing party object.
	 *
	 * @param $party the party to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update_local($organization)
	{
		$name = $organization->get_name();
		$orgnr = $organization->get_organization_number();
		$homepage = $organization->get_homepage();
		$phone = $organization->get_phone();
		$email = $organization->get_email();
		$description = $organization->get_description();
		$street = $organization->get_address();
		$streetnumber = $organization->get_addressnumber();
		$zip_code = $organization->get_zip_code();
		$city = $organization->get_city();
		$district = $organization->get_district();
		$change_type = $organization->get_change_type();
		$transferred = ($organization->get_transferred() == 1 || $organization->get_transferred() == true)?'true':'false';
		$original_org_id = ($organization->get_original_org_id() && $organization->get_original_org_id() != '')?$organization->get_original_org_id():0;

		$values[] = "NAME='{$name}'";
		$values[] = "HOMEPAGE='{$homepage}'";
		$values[] = "PHONE='{$phone}'";
		$values[] = "EMAIL='{$email}'";
		$values[] = "DESCRIPTION='{$description}'";
		$values[] = "ADDRESS='{$street}'";
		$values[] = "ADDRESSNUMBER='{$streetnumber}'";
		$values[] = "ZIP_CODE='{$zip_code}'";
		$values[] = "CITY='{$city}'";
		$values[] = "ORGNO='{$orgnr}'";
		$values[] = "DISTRICT='{$district}'";
		$values[] = "CHANGE_TYPE='{$change_type}'";
		$values[] = "TRANSFERRED={$transferred}";
		$values[] = "ORIGINAL_ORG_ID={$original_org_id}";
		$vals = implode(',',$values);

		$sql = "UPDATE activity_organization SET {$vals} WHERE ID={$organization->get_id()}";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		if(isset($result))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get_id_field_name($extended_info = false)
	{
		if(!$extended_info)
		{
			$ret = 'id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'org', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		return $ret;
	}

	protected function populate(int $org_id, &$organization)
	{

		if($organization == null) {
			$organization = new activitycalendar_organization((int) $org_id);

			$organization->set_name($this->unmarshal($this->db->f('name'), 'string'));
			$organization->set_organization_number($this->unmarshal($this->db->f('organization_number'), 'int'));
			$organization->set_address($this->unmarshal($this->db->f('address'), 'string'));
			$organization->set_addressnumber($this->unmarshal($this->db->f('addressnumber'), 'string'));
			$organization->set_zip_code($this->unmarshal($this->db->f('zip_code'), 'string'));
			$organization->set_city($this->unmarshal($this->db->f('city'), 'string'));
			$organization->set_phone($this->unmarshal($this->db->f('phone'), 'string'));
			$organization->set_email($this->unmarshal($this->db->f('email'), 'string'));
			$organization->set_homepage($this->unmarshal($this->db->f('homepage'), 'string'));
			$organization->set_district($this->unmarshal($this->db->f('district'), 'string'));
			$organization->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$organization->set_change_type($this->unmarshal($this->db->f('change_type'), 'string'));
			$organization->set_transferred($this->unmarshal($this->db->f('transferred'), 'bool'));
			$organization->set_show_in_portal($this->unmarshal($this->db->f('show_in_portal'), 'int'));
			$organization->set_original_org_id($this->unmarshal($this->db->f('original_org_id'), 'int'));
		}
		return $organization;
	}

	function add_organization_local($organization)
	{
		$name = $organization->get_name();
		$orgnr = $organization->get_organization_number();
		$homepage = $organization->get_homepage();
		$street = $organization->get_address();
		$streetnumber = $organization->get_address_number();
		$zip_code = $organization->get_zip_code();
		$city = $organization->get_city();
		if($organization->get_original_org_id() && $organization->get_original_org_id() != '')
		{
			$original_org_id = $organization->get_original_org_id();
		}
		else
		{
			$original_org_id = 0;
		}


		$values[] = "NAME='{$name}'";
		$values[] = "HOMEPAGE='{$homepage}'";
		$values[] = "ADDRESS='{$street}'";
		$values[] = "ADDRESSNUMBER='{$streetnumber}'";
		$values[] = "ZIP_CODE='{$zip_code}'";
		$values[] = "CITY='{$city}'";
		$values[] = "ORGNO='{$orgnr}'";
		$values[] = "ORIGINAL_ORG_ID={$original_org_id}";
		$vals = implode(',',$values);

		//var_dump("INSERT INTO activity_organization ({$cols}) VALUES ({$vals})");
		$sql = "UPDATE activity_organization SET {$vals} WHERE ID={$organization->get_id()}";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		if(isset($result))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function transfer_organization($org_info)
	{
		$name = $org_info['name'];
		$orgnr = $org_info['orgnr'];
		$homepage = $org_info['homepage'];
		$street_1 = $org_info['street'];
		$street_2 = $org_info['streetnumber'];
		$street = $street_1 . ' ' . $street_2;
		$zip_code = $org_info['zip'];
                $district = $org_info['district'];
		$city = $org_info['postaddress'];
		$activity_id = 1;
		$show_in_portal = 1;
		$customer_internal = 0;

		$columns[] = 'name';
		$columns[] = 'homepage';
		$columns[] = 'phone';
		$columns[] = 'email';
		$columns[] = 'description';
		$columns[] = 'street';
		$columns[] = 'zip_code';
		$columns[] = 'city';
		$columns[] = 'district';
		$columns[] = 'organization_number';
		$columns[] = 'activity_id';
		$columns[] = 'customer_internal';
		$columns[] = 'show_in_portal';
		$cols = implode(',',$columns);

		$values[] = "'{$name}'";
		$values[] = "'{$homepage}'";
		$values[] = "''";
		$values[] = "''";
		$values[] = "''";
		$values[] = "'{$street}'";
		$values[] = "'{$zip_code}'";
		$values[] = "'{$city}'";
		$values[] = "'{$district}'";
		$values[] = "'{$orgnr}'";
		$values[] = $this->marshal($activity_id, 'int');
		$values[] = $customer_internal;
		$values[] = $show_in_portal;
		$vals = implode(',',$values);

		$sql = "INSERT INTO bb_organization ({$cols}) VALUES ({$vals})";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		if(isset($result))
		{
			return $this->db->get_last_insert_id('bb_organization', 'id');
		}
		else
		{
			return 0;
		}
	}

	function get_organization_local($org_id)
	{
		$sql = "SELECT * FROM activity_organization WHERE id={$org_id}";
		//var_dump($sql);
		$this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record())
		{
			$organization = new activitycalendar_organization((int) $this->db->f('id'));

			$organization->set_name($this->unmarshal($this->db->f('name'), 'string'));
			$organization->set_organization_number($this->unmarshal($this->db->f('organization_number'), 'int'));
			$organization->set_address($this->unmarshal($this->db->f('address'), 'string'));
			$organization->set_addressnumber($this->unmarshal($this->db->f('addressnumber'), 'string'));
			$organization->set_zip_code($this->unmarshal($this->db->f('zip_code'), 'string'));
			$organization->set_city($this->unmarshal($this->db->f('city'), 'string'));
			$organization->set_phone($this->unmarshal($this->db->f('phone'), 'string'));
			$organization->set_email($this->unmarshal($this->db->f('email'), 'string'));
			$organization->set_homepage($this->unmarshal($this->db->f('homepage'), 'string'));
			$organization->set_district($this->unmarshal($this->db->f('district'), 'string'));
			$organization->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$organization->set_change_type($this->unmarshal($this->db->f('change_type'), 'string'));
			$organization->set_transferred($this->unmarshal($this->db->f('transferred'), 'bool'));
			$organization->set_show_in_portal($this->unmarshal($this->db->f('show_in_portal'), 'int'));
			$organization->set_original_org_id($this->unmarshal($this->db->f('original_org_id'), 'int'));

			return $organization;
		}
	}

	function update_organization($org_info)
	{
		$name = $org_info['name'];
		$orgid = (int)$org_info['orgid'];
		$homepage = $org_info['homepage'];
		if(!$homepage)
		{
			$homepage = '';
		}
		$phone = $org_info['phone'];
		if(!$phone)
		{
			$phone = '';
		}
		$email = $org_info['email'];
		if(!$email)
		{
			$email = '';
		}
		$description = $org_info['description'];
		if(!$description)
		{
			$description = '';
		}
		$street = $org_info['street'] . ' ' . $org_info['streetnumber'];
		if(!$street)
		{
			$street = '';
		}
		$zip_code = $org_info['zip_code'];
		$city = $org_info['city'];
		$district = $org_info['district'];
		if(!$district)
		{
			$district = '';
		}
		$activity_id = 1;
		$show_in_portal = 1;

		$values = array(
			'name = ' . $this->marshal($name, 'string'),
			'homepage = ' . $this->marshal($homepage, 'string'),
			'phone = ' . $this->marshal($phone, 'string'),
			'email = ' . $this->marshal($email, 'string'),
			'description = ' . $this->marshal($description, 'string'),
			'street = ' . $this->marshal($street, 'string'),
			'zip_code = ' . $this->marshal($zip_code, 'string'),
			'city = ' . $this->marshal($city, 'string'),
			'district = ' . $this->marshal($district, 'string'),
			'activity_id = ' . $this->marshal($activity_id, 'int'),
			'show_in_portal = 1'
		);

		$result = $this->db->query('UPDATE bb_organization SET ' . join(',', $values) . " WHERE id=$orgid", __LINE__,__FILE__);
	}

	function update_organization_with_new_info($organization)
	{
		$name = $organization['orgname'];
		$orgid = (int)$organization['orgid'];
		$street = $organization['orgstreet'];
		if(!$street)
		{
			$street = '';
		}
		$zip = $organization['zip'];
		if(!$zip)
		{
			$zip = '';
		}
		$city = $organization['city'];
		if(!$city)
		{
			$city = '';
		}

		$values = array(
			'street = ' . $this->marshal($street, 'string'),
			'zip_code = ' . $this->marshal($zip, 'string'),
			'city = ' . $this->marshal($city, 'string')
		);
		//var_dump("UPDATE bb_organization SET " . join(',', $values) . " WHERE id=$orgid");
		$result = $this->db->query('UPDATE bb_organization SET ' . join(',', $values) . " WHERE id=$orgid", __LINE__,__FILE__);
	}

	function set_organization_inactive($org_id)
	{
		$orgid = (int)$org_id;

		//var_dump("UPDATE bb_organization SET active=0, show_in_portal=0 WHERE id={$orgid}");
		$result = $this->db->query("UPDATE bb_organization SET active=0, show_in_portal=0 WHERE id={$orgid}", __LINE__,__FILE__);
	}

	function get_affected_allocations($org_id)
	{
		$result = array();
		$sql = "select id from bb_allocation where organization_id={$org_id}";
		$this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record()){
			$result[] = $this->db->f('id');
		}

		return $result;
	}

	function update_affected_allocations($id, $org_id)
	{
		$result = $this->db->query("update bb_allocation set organization_id={$org_id} where id={$id}", __LINE__, __FILE__);
	}

	function get_affected_reservations($org_id)
	{
		$result = array();
		$sql = "select id from bb_completed_reservation where organization_id={$org_id}";
		$this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record()){
			$result[] = $this->db->f('id');
		}

		return $result;
	}

	function update_affected_reservations($id, $org_id)
	{
		$result = $this->db->query("update bb_completed_reservation set organization_id={$org_id} where id={$id}", __LINE__, __FILE__);
	}

	function get_affected_events($org_id)
	{
		$result = array();
		$sql = "select id from bb_event where customer_organization_id={$org_id}";
		$this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record()){
			$result[] = $this->db->f('id');
		}

		return $result;
	}

	function update_affected_events($id, $org_id)
	{
		$result = $this->db->query("update bb_event set customer_organization_id={$org_id} where id={$id}", __LINE__, __FILE__);
	}

	function update($organization)
	{
		return false;
	}

        function update_org_district_local($org_id, $district_id)
	{
            $sql = "UPDATE activity_organization SET district='{$district_id}' WHERE ID={$org_id}";
            $result = $this->db->query($sql, __LINE__, __FILE__);
            if(isset($result))
            {
            	return true;
            }
            else
            {
		return false;
            }
	}

	function get_organization_homepage($org_id)
	{
		$result = "Ingen";
		if(isset($org_id)){
			$org_id = intval($org_id);
			$q1="SELECT homepage FROM bb_organization WHERE id={$org_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record())
			{
				$result = $this->db->f('homepage');
			}
		}

		return $result;
	}

	function get_organization_homepage_local($org_id)
	{
		$result = "Ingen";
		if(isset($org_id)){
			$q1="SELECT homepage FROM activity_organization WHERE id={$org_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record())
			{
				$result = $this->db->f('homepage');
			}
		}

		return $result;
	}

	//$org->set_change_type("rejected");
	function reject_organization($org_id)
	{
		if(isset($org_id))
		{
			$query = "UPDATE activity_organization set change_type='rejected' where id={$org_id}";
			$result = $this->db->query($query, __LINE__, __FILE__);
			if(isset($result))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return false;
	}
}
?>
