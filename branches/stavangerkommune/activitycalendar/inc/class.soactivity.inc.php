<?php
phpgw::import_class('activitycalendar.socommon');
phpgw::import_class('activitycalendar.soorganization');
phpgw::import_class('activitycalendar.sogroup');
//phpgw::import_class('activitycalendar.socontactperson');

include_class('activitycalendar', 'activity', 'inc/model/');
include_class('activitycalendar', 'target', 'inc/model/');
include_class('activitycalendar', 'category', 'inc/model/');

class activitycalendar_soactivity extends activitycalendar_socommon
{
	protected static $so;
	protected $soap = false;

	public $soap_functions = array
		(
			'get_activities' => array
			(
				'in'  => array('array'),
				'out' => array('array')
			)
		);

	public $xmlrpc_methods = array
	(
		array
		(
			'name'       => 'get_activities',
			'decription' => 'Get list of activities'
		),
		array
		(
			'name'       => 'get_targetgroups',
			'decription' => 'Get list of targetgroups'
		),
		array
		(
			'name'       => 'get_statuscodes',
			'decription' => 'Get list of statuscodes'
		),
		array
		(
			'name'       => 'get_category_list',
			'decription' => 'Get list of categories'
		),
		array
		(
			'name'       => 'get_organizations',
			'decription' => 'Get list of organizations'
		),
		array
		(
			'name'       => 'get_groups',
			'decription' => 'Get list of groups'
		),
		array
		(
			'name'       => 'debug_xmlrpc',
			'decription' => 'Return incoming params'
		)
	);

	var $public_functions = array
		(
			'get_activities'  		=> true,
			'get_organizations'  	=> true,
			'get_groups'  			=> true,
		);

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return rental_soparty the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('activitycalendar.soactivity');
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
			//$order = "ORDER BY id $dir";
			$order = "ORDER BY $sort_field $dir";
		}
		/*else
		{
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = "ORDER BY id $dir";
		}*/
		//var_dump($search_type);
		//var_dump($search_for);
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
				default:
					$like_clauses[] = "activity.title $this->like $like_pattern";
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
			$filter_clauses[] = "activity.id = {$id}";
		}
		if(isset($filters['new_activities']))
		{
			if(!isset($filters['activity_state']) || (isset($filters['activity_state']) && $filters['activity_state'] == 'all')){
				$filter_clauses[] = "(activity.state=1 OR activity.state=2)";
			}
			if(isset($filters['activity_state']) && $filters['activity_state'] != 'all'){
				$activity_state = $this->marshal($filters['activity_state'],'int');
				$filter_clauses[] = "activity.state = {$activity_state}";
			}
			if(isset($filters['activity_org']) && $filters['activity_org'] != '0'){
				$activity_org = $this->marshal($filters['activity_org'],'int');
				$filter_clauses[] = "activity.organization_id = {$activity_org}";
			}
			if(isset($filters['activity_category']) && $filters['activity_category'] != 'all'){
				$activity_category = $this->marshal($filters['activity_category'],'int');
				$filter_clauses[] = "activity.category = {$activity_category}";
			}
			if(isset($filters['activity_district'])){
				if($filters['activity_district'] != 'all')
				{
					$activity_district = $this->marshal($filters['activity_district'],'int');
					$filter_clauses[] = "activity.office = '{$activity_district}'";
				}
			}
			else
			{
				$activity_district = $this->get_office_from_user($filters['user_id']);
				if($activity_district && $activity_district != '')
				{
                                    if($activity_district == 1)
                                        $activity_district_corr = 2;
                                    else if ($activity_district == 2)
                                        $activity_district_corr = 1;
                                    else
                                        $activity_district_corr = (int)$activity_district;

                                    $filter_clauses[] = "activity.office = '{$activity_district_corr}'";
				}
			}
			if(isset($filters['updated_date_hidden']) && $filters['updated_date_hidden'] != "")
			{
				$ts_query = strtotime($filters['updated_date_hidden']); // target timestamp specified by user
				$filter_clauses[] = "activity.last_change_date < {$ts_query}";
			}
		}
		else
		{
			if(isset($filters['activity_state']) && $filters['activity_state'] != 'all'){
				$activity_state = $this->marshal($filters['activity_state'],'int');
				$filter_clauses[] = "activity.state = {$activity_state}";
			}
			if(isset($filters['activity_org']) && $filters['activity_org'] != '0'){
				$activity_org = $this->marshal($filters['activity_org'],'int');
				$filter_clauses[] = "activity.organization_id = {$activity_org}";
			}
			if(isset($filters['activity_category']) && $filters['activity_category'] != 'all'){
				$activity_category = $this->marshal($filters['activity_category'],'int');
				$filter_clauses[] = "activity.category = {$activity_category}";
			}
			if(isset($filters['activity_district'])){
				if($filters['activity_district'] != 'all')
				{
					$activity_district = $this->marshal($filters['activity_district'],'int');
					$filter_clauses[] = "activity.office = '{$activity_district}'";
				}
			}
			else
			{
				$activity_district = $this->get_office_from_user($filters['user_id']);
				if($activity_district && $activity_district != '')
				{
                                    if($activity_district == 1)
                                        $activity_district = 2;
                                    else if ($activity_district == 2)
                                        $activity_district = 1;

                                    $filter_clauses[] = "activity.office = '{$activity_district}'";
				}
			}
			if(isset($filters['updated_date_hidden']) && $filters['updated_date_hidden'] != "")
			{
				$ts_query = strtotime($filters['updated_date_hidden']); // target timestamp specified by user
				$filter_clauses[] = "activity.last_change_date < {$ts_query}";
			}
		}

		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(activity.id)) AS count';
		}
		else
		{
			$columns[] = 'activity.id';
			$columns[] = 'activity.title';
			$columns[] = 'activity.organization_id';
			$columns[] = 'activity.group_id';
			$columns[] = 'activity.district';
			$columns[] = 'activity.office';
			$columns[] = 'activity.state';
			$columns[] = 'activity.category';
			$columns[] = 'activity.target';
			$columns[] = 'activity.description';
			$columns[] = 'activity.arena';
			$columns[] = 'activity.internal_arena';
			$columns[] = 'activity.time';
			$columns[] = 'activity.create_date';
			$columns[] = 'activity.last_change_date';
			$columns[] = 'activity.contact_person_1';
			$columns[] = 'activity.contact_person_2';
			$columns[] = 'activity.contact_person_2_address';
			$columns[] = 'activity.contact_person_2_zip';
			$columns[] = 'activity.special_adaptation';
			$columns[] = 'activity.secret';
			$columns[] = 'activity.frontend';
			$columns[] = 'activity.new_org';
			$columns[] = 'activity.new_group';

			$cols = implode(',',$columns);
		}

		$tables = "activity_activity activity";

		//$join_contracts = "	{$this->left_join} rental_contract_party c_p ON (c_p.party_id = party.id)
		//{$this->left_join} rental_contract contract ON (contract.id = c_p.contract_id)";

		//var_dump("SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}");
		return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
	}



	/**
	 * Function for adding a new activity to the database. Updates the activity object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$activity)
	{
		// Insert a new activity
		$ts_now = strtotime('now');
		$secret = $this->generate_secret();
		$q ="INSERT INTO activity_activity (title, create_date,secret) VALUES ('tmptitle', $ts_now, '{$secret}')";
		$result = $this->db->query($q, __LINE__,__FILE__);

		if(isset($result)) {
			// Set the new party ID
			$activity->set_id($this->db->get_last_insert_id('activity_activity', 'id'));
			// Forward this request to the update method
			return $this->update($activity);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update the database values for an existing activity object.
	 *
	 * @param $activity the activity to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update($activity)
	{
		$id = intval($activity->get_id());
		$ts_now = strtotime('now');

		$values = array(
			'title = '     . $this->marshal($activity->get_title(), 'string'),
			'organization_id = '. $this->marshal($activity->get_organization_id(), 'int'),
			'group_id = '     . $this->marshal($activity->get_group_id(), 'int'),
			'district =  '     . $this->marshal($activity->get_district(), 'string'),
			'office =  '     . $this->marshal($activity->get_office(), 'int'),
			'category = '          . $this->marshal($activity->get_category(), 'int'),
			'state = '          . $this->marshal($activity->get_state(), 'int'),
			'target = '   . $this->marshal($activity->get_target(), 'string'),
			'description = '     . $this->marshal($activity->get_description(), 'string'),
			'arena = '      . $this->marshal($activity->get_arena(), 'int'),
			'internal_arena = '      . $this->marshal($activity->get_internal_arena(), 'int'),
			'time = '      . $this->marshal($activity->get_time(), 'string'),
			'last_change_date = '    . $this->marshal($ts_now, 'int'),
			'contact_person_1 = '          . $this->marshal($activity->get_contact_person_1(), 'int'),
			'contact_person_2 = '          . $this->marshal($activity->get_contact_person_2(), 'int'),
			'contact_person_2_address = '          . $this->marshal($activity->get_contact_person_2_address(), 'string'),
			'contact_person_2_zip = '          . $this->marshal($activity->get_contact_person_2_zip(), 'string'),
			'special_adaptation = '			.($activity->get_special_adaptation() ? "true" : "false"),
			'frontend = '			.($activity->get_frontend() ? "true" : "false"),
			'new_org = '			.($activity->get_new_org() ? "true" : "false"),
			'new_group = '			.($activity->get_new_group() ? "true" : "false")
		);

		//var_dump('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id");
		$result = $this->db->query('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

		return isset($result);
	}

	function import_activity($activity)
	{
		$id = intval($activity->get_id());
		$ts_now = strtotime('now');

		$columns = array(
			'title',
			'organization_id',
			'group_id',
			'district',
			'office',
			'category',
			'state',
			'target',
			'description',
			'arena',
			'internal_arena',
			'time',
			'last_change_date',
			'create_date',
			'contact_person_1',
			'contact_person_2',
			'contact_person_2_address',
			'contact_person_2_zip',
			'secret',
			'special_adaptation'
		);

		$values = array(
			$this->marshal($activity->get_title(), 'string'),
			$this->marshal($activity->get_organization_id(), 'int'),
			$this->marshal($activity->get_group_id(), 'int'),
			$this->marshal($activity->get_district(), 'string'),
			$this->marshal($activity->get_office(), 'int'),
			$this->marshal($activity->get_category(), 'int'),
			$this->marshal($activity->get_state(), 'int'),
			$this->marshal($activity->get_target(), 'string'),
			$this->marshal($activity->get_description(), 'string'),
			$this->marshal($activity->get_arena(), 'int'),
			$this->marshal($activity->get_internal_arena(), 'int'),
			$this->marshal($activity->get_time(), 'string'),
			$this->marshal($activity->get_last_change_date(), 'int'),
			$this->marshal($ts_now, 'int'),
			$this->marshal($activity->get_contact_person_1(), 'int'),
			$this->marshal($activity->get_contact_person_2(), 'int'),
			$this->marshal($activity->get_contact_person_2_address(), 'string'),
			$this->marshal($activity->get_contact_person_2_zip(), 'string'),
			$this->marshal($this->generate_secret(),'string'),
			($activity->get_special_adaptation() ? "true" : "false")
		);

		$result = $this->db->query('INSERT INTO activity_activity (' . join(',', $columns) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);

		return isset($result);
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
				'table'			=> 'activity', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		return $ret;
	}

	protected function populate(int $activity_id, &$activity)
	{

		if($activity == null) {
			$activity = new activitycalendar_activity((int) $activity_id);

			$activity->set_title($this->unmarshal($this->db->f('title'), 'string'));
			$activity->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$activity->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
			$activity->set_district($this->unmarshal($this->db->f('district'), 'string'));
			$activity->set_office($this->unmarshal($this->db->f('office'), 'int'));
			$activity->set_category($this->unmarshal($this->db->f('category'), 'int'));
			$activity->set_state($this->unmarshal($this->db->f('state'), 'int'));
			$activity->set_target($this->unmarshal($this->db->f('target'), 'string'));
			$activity->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$activity->set_arena($this->unmarshal($this->db->f('arena'), 'string'));
			$activity->set_internal_arena($this->unmarshal($this->db->f('internal_arena'), 'string'));
			$activity->set_time($this->unmarshal($this->db->f('time'), 'string'));
			$activity->set_last_change_date($this->unmarshal($this->db->f('last_change_date'), 'int'));
			$activity->set_special_adaptation($this->unmarshal($this->db->f('special_adaptation', 'bool')));
			$activity->set_secret($this->unmarshal($this->db->f('secret'), 'string'));
			$activity->set_contact_person_2_address($this->unmarshal($this->db->f('contact_person_2_address'), 'string'));
			$activity->set_contact_person_2_zip($this->unmarshal($this->db->f('contact_person_2_zip'), 'string'));
			$activity->set_frontend($this->unmarshal($this->db->f('frontend', 'bool')));
			$activity->set_new_org($this->unmarshal($this->db->f('new_org', 'bool')));
			$activity->set_new_group($this->unmarshal($this->db->f('new_group', 'bool')));

			if($activity->get_group_id() && $activity->get_group_id() > 0)
			{
				if($activity->get_new_group())
				{
					$contacts = activitycalendar_sogroup::get_instance()->get_contacts_local($activity->get_group_id());
					$activity->set_contact_persons($contacts);
					//$org_tmp = activitycalendar_sogroup::get_instance()->get_orgid_from_group($activity->get_group_id());
					//$activity->set_organization_id($org_tmp);
				}
				else
				{
					$contacts = activitycalendar_sogroup::get_instance()->get_contacts($activity->get_group_id());
					$activity->set_contact_persons($contacts);
					$org_tmp = activitycalendar_sogroup::get_instance()->get_orgid_from_group($activity->get_group_id());
					$activity->set_organization_id($org_tmp);
				}
			}
			else if($activity->get_organization_id() && $activity->get_organization_id() > 0)
			{
				if($activity->get_new_org())
				{
					$contacts = activitycalendar_soorganization::get_instance()->get_contacts_local($activity->get_organization_id());
					$activity->set_contact_persons($contacts);
				}
				else
				{
					$contacts = activitycalendar_soorganization::get_instance()->get_contacts($activity->get_organization_id());
					$activity->set_contact_persons($contacts);
				}
			}

		}

		return $activity;
	}

	function get_category_name($category_id)
	{
		$result = "Ingen";
		if($category_id != null)
		{
			$sql = "SELECT name FROM bb_activity where id=$category_id";
			$this->db->query($sql, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name');
			}
    	}
		return $result;
	}

	function get_categories()
	{
		$categories = array();
		$sql = "SELECT * FROM bb_activity where active=1 and parent_id=1";
		$this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record()){
			$category = new activitycalendar_category($this->db->f('id'));
			$category->set_parent_id($this->db->f('parent_id'));
			$category->set_name($this->db->f('name'));
			$categories[] = $category;
		}
		return $categories;
	}

	function select_district_list()
	{
		$this->db->query("SELECT id, descr FROM fm_district where id >'0' AND NOT descr LIKE '%vrige%' ORDER BY id ", __LINE__, __FILE__);

		$i = 0;
		while ($this->db->next_record())
		{
			$district[$i]['id'] = $this->db->f('id');
			$district[$i]['name'] = stripslashes($this->db->f('descr'));
			$i++;
		}

		return $district;
	}

	function get_district_from_name($name)
	{
		$this->db->query("SELECT part_of_town_id FROM fm_part_of_town where name like UPPER('%{$name}%') ", __LINE__, __FILE__);
		while($this->db->next_record()){
			$result = $this->db->f('part_of_town_id');
		}
		return $result;
	}

	function get_district_from_id($d_id)
	{
		$this->db->query("SELECT name FROM fm_part_of_town where part_of_town_id={$d_id} ", __LINE__, __FILE__);
		while($this->db->next_record()){
			$result = $this->db->f('name');
		}
		return $result;
	}


	function get_district_name($district_id)
	{
		//$result = "Ingen";
		$values = array();
		if($district_id != null)
		{
			$sql = "SELECT district_id, name FROM fm_part_of_town where part_of_town_id in ($district_id)";
			$this->db->query($sql, __LINE__, __FILE__);
			while($this->db->next_record()){
				$name = $this->db->f('name');
				$values[] = $name;
				//$result .= $name . ',';
			}
			$result = implode(", ",$values);
			return $result;
    	}
    	return "";
	}

	function get_districts()
	{
		$this->db->query("SELECT part_of_town_id, name FROM fm_part_of_town district_id ", __LINE__, __FILE__);

		$i = 0;
		while ($this->db->next_record())
		{
			$name = $this->db->f('name');
			if($name != 'ØVRIGE')
			{
				$district[$i]['part_of_town_id'] = $this->db->f('part_of_town_id');
				$district[$i]['name'] = stripslashes($this->db->f('name'));
				$i++;
			}
		}

		return $district;
	}


	function get_office_from_user($user_id)
	{
		if(user_id)
		{
			$user_id = (int)$user_id;
			$q1="SELECT office FROM bb_office_user WHERE account_id={$user_id}";
			//var_dump($q1);
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$office_id = $this->db->f('office');
			}
		}
		return $office_id;
	}

	function get_office_name($district_id)
	{
		$result = "Ingen";
		if($district_id != null)
		{
			$sql = "SELECT descr FROM fm_district where id=$district_id";
			$this->db->query($sql, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('descr');
			}
    	}
		return $result;
	}

        function get_office_description($office_id)
	{
            $result = "";
            if($office_id != null)
            {
		$sql = "SELECT description FROM bb_office where id=$office_id";
		$this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record()){
                    $result = $this->db->f('description');
		}
            }
            return $result;
	}

	function get_target_name($target_id)
	{
		$result = "Ingen";
		if($target_id != null)
		{
			$sql = "SELECT name FROM bb_agegroup where id=$target_id";
			$this->db->query($sql, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name');
			}
    	}
		return $result;
	}

	function get_targets()
	{
		$targets = array();
		$sql = "SELECT * FROM bb_agegroup where active=1 ORDER BY sort";
		$this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record()){
			$name = $this->db->f('name');
			if($name != 'Tilskuere')
			{
				$target = new activitycalendar_target($this->db->f('id'));
				$target->set_description($this->db->f('description'));
				$target->set_name($this->db->f('name'));
				$targets[] = $target;
			}
		}
		return $targets;
	}

	function get_category_from_name($name)
	{
    	if($name != null)
    	{
			$sql = "select id from bb_activity where name like '%{$name}%'";
    		$this->db->query($sql, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('id');
			}
    	}
		return $result;
	}

	function get_target_from_sort_id($id)
	{
    	if($id != null && is_numeric($id))
    	{
			$sql = "select id from bb_agegroup where sort={$id} and active=1";
    		$this->db->query($sql, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('id');
			}
    	}
		return $result;
	}

	function get_orgid_from_orgno($orgno)
	{
    	if($orgno != null)
    	{
			$sql = "select id from bb_organization where organization_number='{$orgno}'";
    		$this->db->query($sql, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('id');
			}
    	}
		return $result;
	}

	function update_org_description($org_id, $description)
	{
    	if($org_id != null)
    	{
			$sql = "update bb_organization set description='{$description}' where id={$org_id}";
    		$result = $this->db->query($sql, __LINE__, __FILE__);
    	}
		return isset($result);
	}

	function set_org_active($org_id)
	{
		if($org_id != null)
		{
			$sql = "update bb_organization set show_in_portal=1 where id={$org_id}";
    		$result = $this->db->query($sql, __LINE__, __FILE__);
		}
		return isset($result);
	}

	function get_activities($parameters = array())
	{
		$soap = isset($parameters['soap']) && $parameters['soap'] ? true : false;
		$this->soap = $soap;
		//fromdate -> innparam for uthenting av delta - timestamp
		$whereclause_date = "";
		if($parameters['fromdate'])
		{
			$from_date = (int)$parameters['fromdate'];
			$whereclause_date = "AND last_change_date > {$from_date}";
		}
		$activities = array();
		$sql = "SELECT * FROM activity_activity where state in (2,3,5) {$whereclause_date}";
		$this->db->query($sql, __LINE__, __FILE__);
		while ($this->db->next_record())
		{
		    $gr = $this->db->f('group_id');
			$activities[]= array
			(
				'id'				=> (int) $this->db->f('id'),
				'title'				=> $soap ? $this->db->f('title',true) : utf8_decode($this->db->f('title',true)),
				'organization_id'	=> $this->db->f('organization_id'),
				'group_id'			=> $this->db->f('group_id'),
				'district'			=> $this->db->f('district'),
				'category'			=> $this->db->f('category'),
				'state'				=> $this->db->f('state'),
				'target'			=> $this->db->f('target'),
				'arena'				=> $this->db->f('arena'),
			    'internal_arena'	=> $this->db->f('internal_arena'),
				'time'				=> $soap ? $this->db->f('time',true) : utf8_decode($this->db->f('time',true)),
				'contact_person_1'	=> $this->db->f('contact_person_1'),
				'contact_person_2'	=> $this->db->f('contact_person_2'),
				'special_adaptation'=> $this->db->f('special_adaptation'),
			);
		}

		foreach ($activities as &$activity)
		{
				if($activity['group_id'] && !$activity['group_id'] == '' && !$activity['group_id'] == 0)
				{
					$activity['group_info']			= $this->get_group_info($activity['group_id']);
					$activity['organization_info']	= $this->get_org_info($activity['group_info']['organization_id']);
				}
				else
				{
					$activity['organization_info']	= $this->get_org_info($activity['organization_id']);
					$activity['group_info']			= $this->get_group_info($activity['group_id']);
				}
				$activity['district_name']		= $soap ? $this->get_district_name($activity['district']) : utf8_decode($this->get_district_name($activity['district']));
				$activity['category_name']		= $soap ? $this->get_category_name($activity['category']) : utf8_decode($this->get_category_name($activity['category']));
				$activity['description']		= $this->get_activity_description($activity['organization_id'],$activity['group_id']);
				$activity['arena_info']			= $this->get_all_arena_info($activity['arena'], $activity['internal_arena']);
				$activity['internal_arena_info']= $this->get_internal_arena_info($activity['internal_arena']);
				$activity['contact_person']		= $this->get_contact_person($activity['organization_id'],$activity['group_id'],$activity['contact_person_1']);
		}
//_debug_array($activities);
		return $activities;
	}

	function get_contact_person($org_id, $group_id, $cont_pers)
	{
		if($group_id)
		{
			$group_id = (int)$group_id;
	//		$this->db->query("SELECT * FROM bb_group_contact WHERE id={$cont_pers}", __LINE__, __FILE__);
			$this->db->query("SELECT * FROM bb_group_contact WHERE group_id={$group_id} LIMIT 1", __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = array('name' => $this->soap ? $this->db->f('name') : utf8_decode($this->db->f('name')),'phone' => $this->db->f('phone'),'email' => $this->db->f('email'));
			}
		}
		else if($org_id)
		{
			$org_id = (int)$org_id;
			$this->db->query("SELECT * FROM bb_organization_contact WHERE organization_id={$org_id} LIMIT 1", __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = array('name' => $this->soap ? $this->db->f('name') : utf8_decode($this->db->f('name')),'phone' => $this->db->f('phone'),'email' => $this->db->f('email'));
			}
		}
		return $result;
	}

	function get_activity_description($org_id, $group_id)
	{
		if($group_id)
		{
			$group_id = (int)$group_id;
			$this->db->query("SELECT * FROM bb_group WHERE id={$group_id}", __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->soap ? $this->db->f('description') : utf8_decode($this->db->f('description'));
			}
		}
		else if($org_id)
		{
			$org_id = (int)$org_id;
			$this->db->query("SELECT * FROM bb_organization WHERE id={$org_id}", __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->soap ? $this->db->f('description') : utf8_decode($this->db->f('description'));
			}
		}
		return $result;
	}


	function get_organizations()
	{
		$organizations = array();
		$this->db->query("SELECT * FROM bb_organization WHERE show_in_portal=1", __LINE__, __FILE__);
		while($this->db->next_record())
		{
			$homepage = $this->db->f('homepage');
			if ( trim($homepage) != '' && !preg_match("/^http|https:\/\//", trim($homepage)) )
			{
				$homepage = 'http://'.$homepage;
			}
			$organizations[] = array
			(
				'id'			=> (int) $this->db->f('id'),
				'name'			=> utf8_decode($this->db->f('name')),
				'shortname'		=> utf8_decode($this->db->f('shortname')),
				'description'	=> utf8_decode($this->db->f('description')),
				'homepage'		=> $homepage,
				'phone'			=> $this->db->f('phone'),
				'email'			=> $this->db->f('email')
			);
		}
//	_debug_array($organizations);
		return $organizations;
	}

	function get_org_info($org_id)
	{
		$result = array();
		if($org_id)
		{
			$org_id = (int)$org_id;
			$this->db->query("SELECT * FROM bb_organization WHERE id={$org_id}", __LINE__, __FILE__);
			$this->db->next_record();
			$result = array
			(
				'name'			=> utf8_decode($this->db->f('name')),
				'shortname'		=> utf8_decode($this->db->f('shortname')),
				'description'	=> utf8_decode($this->db->f('description')),
				'homepage'		=> $this->db->f('homepage'),
				'phone'			=> $this->db->f('phone'),
				'email'			=> $this->db->f('email')
			);
		}
		return $result;
	}

	function get_groups()
	{
		$groups = array();
		$join = " {$this->left_join} bb_organization ON (bb_group.organization_id = bb_organization.id)";
		$this->db->query("SELECT bb_group.*, bb_organization.homepage FROM bb_group {$join} WHERE bb_group.show_in_portal=1", __LINE__, __FILE__);
		while($this->db->next_record())
		{
			$groups[] = array
			(
				'id'				=> (int) $this->db->f('id'),
				'name'				=> utf8_decode($this->db->f('name')),
				'shortname'			=> utf8_decode($this->db->f('shortname')),
				'description'		=> utf8_decode($this->db->f('description')),
				'homepage'			=> utf8_decode($this->db->f('homepage')),
				'organization_id'	=> $this->db->f('organization_id')
			);
		}
//	_debug_array($groups);
		return $groups;
	}


	/*
	* Return incoming
	*/
	function debug_xmlrpc($data = array())
	{
		if($data['fromdate'])
		{
			return $data['fromdate'];
		}
		else
		{
			return $data;
		}
	}


	function get_group_info($group_id)
	{
		$result = array();
		if($group_id)
		{
			$group_id = (int)$group_id;
			$this->db->query("SELECT * FROM bb_group WHERE id={$group_id}", __LINE__, __FILE__);
			$this->db->next_record();
			$result = array
			(
				'name'				=> utf8_decode($this->db->f('name')),
				'shortname'			=> utf8_decode($this->db->f('shortname')),
				'description'		=> utf8_decode($this->db->f('description')),
				'organization_id'	=> $this->db->f('organization_id')
			);

		}
		return $result;
	}

	function get_all_arena_info($arena_id, $int_arena_id)
	{
		$result = array();
		if($arena_id && is_numeric($arena_id))
		{
			$arena_id = (int)$arena_id;
			$this->db->query("SELECT * FROM activity_arena WHERE id={$arena_id}", __LINE__, __FILE__);
			$this->db->next_record();
			$result = array
			(
				'arena_name' => $this->soap ? $this->db->f('arena_name') : utf8_decode($this->db->f('arena_name')),
				'address' => $this->soap ? $this->db->f('address') : utf8_decode($this->db->f('address'))
			);
		}
		else if($int_arena_id && is_numeric($int_arena_id))
		{
			$int_arena_id = (int)$int_arena_id;
			$this->db->query("SELECT id, name, street FROM bb_building WHERE id={$int_arena_id}", __LINE__, __FILE__);
			$this->db->next_record();
			$result = array
			(
				'arena_name' => $this->soap ? $this->db->f('name') : utf8_decode($this->db->f('name')),
				'address' => $this->soap ? $this->db->f('street') : utf8_decode($this->db->f('street'))
			);
		}
		return $result;
	}

	function get_arena_info($arena_id)
	{
		$result = array();
		if($arena_id)
		{
			$arena_id = (int)$arena_id;
			$this->db->query("SELECT * FROM activity_arena WHERE id={$arena_id}", __LINE__, __FILE__);
			$this->db->next_record();
			$result = array
			(
				'arena_name' => $this->soap ? $this->db->f('arena_name') : utf8_decode($this->db->f('arena_name')),
				'address' => $this->soap ? $this->db->f('address') : utf8_decode($this->db->f('address'))
			);
		}
		return $result;
	}

	function get_internal_arena_info($arena_id)
	{
		$result = array();
		if($arena_id)
		{
			$arena_id = (int)$arena_id;
			$this->db->query("SELECT id, name, street FROM bb_building WHERE id={$arena_id}", __LINE__, __FILE__);
			$this->db->next_record();
			$result = array
			(
				'arena_name' => $this->soap ? $this->db->f('name') : utf8_decode($this->db->f('name')),
				'address' => $this->soap ? $this->db->f('street') : utf8_decode($this->db->f('street'))
			);
		}
		return $result;
	}

	function get_statuscodes()
	{
		$statuscodes[] = array('id' => '0', 'name' => utf8_decode('Ingen'));
		$statuscodes[] = array('id' => '1', 'name' => utf8_decode('Ny'));
		$statuscodes[] = array('id' => '2', 'name' => utf8_decode('Endring'));
		$statuscodes[] = array('id' => '3', 'name' => utf8_decode('Akseptert'));
		$statuscodes[] = array('id' => '4', 'name' => utf8_decode('Behandlet'));
		$statuscodes[] = array('id' => '5', 'name' => utf8_decode('Avvist'));

		return $statuscodes;
	}

	function get_targetgroups()
	{
		$sql = "SELECT * FROM bb_agegroup where active=1 AND NOT name like 'Tilskuer%' ORDER BY sort";
		$this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record()){
			$targets[] = array(
					'id'				=> (int) $this->db->f('id'),
					'name'				=> utf8_decode($this->db->f('name',true)),
					'sort'				=> (int) $this->db->f('sort'),
			);
		}
		return $targets;
	}

	function get_category_list()
	{
		$sql = "SELECT * FROM bb_activity where active=1 and parent_id=1";
		$this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record()){
			$categories[] = array(
					'id'				=> (int) $this->db->f('id'),
					'name'				=> utf8_decode($this->db->f('name',true)),
			);
		}
		return $categories;
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
		$street = $org_info['street'];
		if(!$street)
		{
			$street = '';
		}
		$zip = $org_info['zip'];
		if($zip && strlen($zip) > 5)
		{
			$zip_code = substr($zip,0,4);
			$city = substr($zip, 5);
		}
		else
		{
			$zip_code = '';
			$city = '';
		}
		$district = $org_info['district'];
		if(!$district)
		{
			$district = '';
		}
		$activity_id = $org_info['activity_id'];
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
			'district = ' . $this->marshal($district),
			'activity_id = ' . $this->marshal($activity_id, 'int'),
			'show_in_portal = 1'
		);

		$result = $this->db->query('UPDATE bb_organization SET ' . join(',', $values) . " WHERE id=$orgid", __LINE__,__FILE__);
	}
	function add_organization($org_info)
	{
		$name = $org_info['name'];
		$orgnr = $org_info['orgnr'];
		$homepage = $org_info['homepage'];
		$phone = $org_info['phone'];
		$email = $org_info['email'];
		$description = $org_info['description'];
		$street = $org_info['street'];
		$zip = $org_info['zip'];
		if($zip && strlen($zip) > 5)
		{
			$zip_code = substr($zip,0,4);
			$city = substr($zip, 5);
		}
		else
		{
			$zip_code = '';
			$city = '';
		}
		$district = $org_info['district'];
		$activity_id = $org_info['activity_id'];
		$show_in_portal = 1;

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
		$columns[] = 'show_in_portal';
		$cols = implode(',',$columns);

		$values[] = "'{$name}'";
		$values[] = "'{$homepage}'";
		$values[] = "'{$phone}'";
		$values[] = "'{$email}'";
		$values[] = "'{$description}'";
		$values[] = "'{$street}'";
		$values[] = "'{$zip_code}'";
		$values[] = "'{$city}'";
		$values[] = "'{$district}'";
		$values[] = "'{$orgnr}'";
		$values[] = $this->marshal($activity_id, 'int');
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

	function add_organization_local($org_info)
	{
		$name = $org_info['name'];
		$orgnr = $org_info['orgnr'];
		$homepage = $org_info['homepage'];
		$phone = $org_info['phone'];
		$email = $org_info['email'];
		$description = $org_info['description'];
		$street = $org_info['street'];
		$streetnumber = $org_info['streetnumber'];
		$zip_code = $org_info['zip'];
		$city = $org_info['postaddress'];
		$district = $org_info['district'];
		$status = $org_info['status'];
		$original_org_id = $org_info['original_org_id'];
		if(!isset($original_org_id) || $original_org_id == '')
		{
			$original_org_id = 0;
		}

		$columns[] = 'name';
		$columns[] = 'homepage';
		$columns[] = 'phone';
		$columns[] = 'email';
		$columns[] = 'description';
		$columns[] = 'address';
		$columns[] = 'addressnumber';
		$columns[] = 'zip_code';
		$columns[] = 'city';
		$columns[] = 'orgno';
		$columns[] = 'district';
		$columns[] = 'change_type';
		$columns[] = 'original_org_id';
		$cols = implode(',',$columns);

		$values[] = "'{$name}'";
		$values[] = "'{$homepage}'";
		$values[] = "'{$phone}'";
		$values[] = "'{$email}'";
		$values[] = "'{$description}'";
		$values[] = "'{$street}'";
		$values[] = "'{$streetnumber}'";
		$values[] = "'{$zip_code}'";
		$values[] = "'{$city}'";
		$values[] = "'{$orgnr}'";
		$values[] = "'{$district}'";
		$values[] = "'{$status}'";
		$values[] = $original_org_id;
		$vals = implode(',',$values);

		//var_dump("INSERT INTO activity_organization ({$cols}) VALUES ({$vals})");
		$sql = "INSERT INTO activity_organization ({$cols}) VALUES ({$vals})";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		if(isset($result))
		{
			return $this->db->get_last_insert_id('activity_organization', 'id');
		}
		else
		{
			return 0;
		}
	}

	function add_group($group_info)
	{
		$name = $group_info['name'];
		$orgid = $group_info['organization_id'];
		$description = $group_info['description'];
		$activity_id = $group_info['activity_id'];
		$show_in_portal = 1;

		$columns[] = 'name';
		$columns[] = 'description';
		$columns[] = 'organization_id';
		$columns[] = 'activity_id';
		$columns[] = 'show_in_portal';
		$cols = implode(',',$columns);

		$values[] = "'{$name}'";
		$values[] = "'{$description}'";
		$values[] = "'{$orgid}'";
		$values[] = $this->marshal($activity_id, 'int');
		$values[] = $show_in_portal;
		$vals = implode(',',$values);

		$sql = "INSERT INTO bb_group ({$cols}) VALUES ({$vals})";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		if(isset($result))
		{
			return $this->db->get_last_insert_id('bb_group', 'id');
		}
		else
		{
			return 0;
		}
	}

	function add_group_local($group_info)
	{
		$name = $group_info['name'];
		$orgid = $group_info['organization_id'];
		$description = $group_info['description'];
		$status = $group_info['status'];

		$columns[] = 'name';
		$columns[] = 'description';
		$columns[] = 'organization_id';
		$columns[] = 'change_type';
		$cols = implode(',',$columns);

		$values[] = "'{$name}'";
		$values[] = "'{$description}'";
		$values[] = "'{$orgid}'";
		$values[] = "'{$status}'";
		$vals = implode(',',$values);

		$sql = "INSERT INTO activity_group ({$cols}) VALUES ({$vals})";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		if(isset($result))
		{
			return $this->db->get_last_insert_id('activity_group', 'id');
		}
		else
		{
			return 0;
		}
	}

	function delete_contact_persons($org_id)
	{
		if($org_id)
		{
			$org = (int)$org_id;
			$sql = "DELETE FROM bb_organization_contact WHERE organization_id={$org}";
			$result = $this->db->query($sql, __LINE__, __FILE__);
			return isset($result);
		}
/*		else if($group_id)
		{
			$group = (int)$group_id;
			$sql = "DELETE FROM bb_group_contact WHERE group_id={$group}";
			$result = $this->db->query($sql, __LINE__, __FILE__);
			return isset($result);
		}*/
	}

	function add_contact_person_org($contact)
	{
		$name = $contact['name'];
		$phone = $contact['phone'];
		$mail = $contact['mail'];
		$org_id = $contact['org_id'];
		$ssn = '';

		$columns[] = 'name';
		$columns[] = 'ssn';
		$columns[] = 'phone';
		$columns[] = 'email';
		$columns[] = 'organization_id';
		$cols = implode(',',$columns);

		$values[] = "'{$name}'";
		$values[] = "'{$ssn}'";
		$values[] = "'{$phone}'";
		$values[] = "'{$mail}'";
		$values[] = $org_id;
		$vals = implode(',',$values);

		$sql = "INSERT INTO bb_organization_contact ({$cols}) VALUES ({$vals})";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		return isset($result);
	}

	function update_contact_person_org($contact)
	{

	}

	function add_contact_person_group($contact)
	{
		$name = $contact['name'];
		$phone = $contact['phone'];
		$mail = $contact['mail'];
		$org_id = $contact['group_id'];

		$columns[] = 'name';
		$columns[] = 'phone';
		$columns[] = 'email';
		$columns[] = 'group_id';
		$cols = implode(',',$columns);

		$values[] = "'{$name}'";
		$values[] = "'{$phone}'";
		$values[] = "'{$mail}'";
		$values[] = $org_id;
		$vals = implode(',',$values);

		$sql = "INSERT INTO bb_group_contact ({$cols}) VALUES ({$vals})";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		return isset($result);
	}

	function update_contact_person_group($contact)
	{

	}

	function add_contact_person_local($contact)
	{
		$name = $contact['name'];
		$phone = $contact['phone'];
		$mail = $contact['mail'];
		$org_id = $contact['org_id'];
		$group_id = $contact['group_id'];

		$columns[] = 'name';
		$columns[] = 'phone';
		$columns[] = 'email';
		$columns[] = 'organization_id';
		$columns[] = 'group_id';
		$columns[] = 'address';
		$columns[] = 'zipcode';
		$columns[] = 'city';
		$cols = implode(',',$columns);

		$values[] = "'{$name}'";
		$values[] = "'{$phone}'";
		$values[] = "'{$mail}'";
		$values[] = $org_id;
		$values[] = $group_id;
		$values[] = "''";
		$values[] = "''";
		$values[] = "''";
		$vals = implode(',',$values);

		//var_dump("INSERT INTO activity_contact_person ({$cols}) VALUES ({$vals})");
		$sql = "INSERT INTO activity_contact_person ({$cols}) VALUES ({$vals})";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		return isset($result);
	}

	function get_activities_for_update($org_id, $group = false)
	{
		$activity_ids = array();
		if($group)
		{
			$sql = "SELECT id FROM activity_activity WHERE new_group AND group_id={$org_id}";
		}
		else
		{
			$sql = "SELECT id FROM activity_activity WHERE new_org AND organization_id={$org_id}";
		}

		$this->db->query($sql, __LINE__, __FILE__);
		while ($this->db->next_record())
		{
		    $activity_ids[] = $this->db->f('id');
		}

		return $activity_ids;
	}

	function get_connected_activities($org_id)
	{
		$activities = array();
		$sql = "SELECT * FROM activity_activity WHERE organization_id={$org_id}";

		$this->db->query($sql, __LINE__, __FILE__);
		while ($this->db->next_record())
		{
			$activity = new activitycalendar_activity((int) $this->db->f('id'));

			$activity->set_title($this->unmarshal($this->db->f('title'), 'string'));
			$activity->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$activity->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
			$activity->set_district($this->unmarshal($this->db->f('district'), 'int'));
			$activity->set_office($this->unmarshal($this->db->f('office'), 'int'));
			$activity->set_category($this->unmarshal($this->db->f('category'), 'int'));
			$activity->set_state($this->unmarshal($this->db->f('state'), 'int'));
			$activity->set_target($this->unmarshal($this->db->f('target'), 'string'));
			$activity->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$activity->set_arena($this->unmarshal($this->db->f('arena'), 'string'));
			$activity->set_internal_arena($this->unmarshal($this->db->f('internal_arena'), 'string'));
			$activity->set_time($this->unmarshal($this->db->f('time'), 'string'));
			$activity->set_last_change_date($this->unmarshal($this->db->f('last_change_date'), 'int'));
			$activity->set_special_adaptation($this->unmarshal($this->db->f('special_adaptation', 'bool')));
			$activity->set_secret($this->unmarshal($this->db->f('secret'), 'string'));
			$activity->set_contact_person_2_address($this->unmarshal($this->db->f('contact_person_2_address'), 'string'));
			$activity->set_contact_person_2_zip($this->unmarshal($this->db->f('contact_person_2_zip'), 'string'));
			$activity->set_frontend($this->unmarshal($this->db->f('frontend', 'bool')));
			$activity->set_new_org($this->unmarshal($this->db->f('new_org', 'bool')));

			$activities[] = $activity;
		}

		return $activities;
	}

	function update_organization_connection($activity_id, $organization_id)
	{
		$id = intval($activity_id);
		$org_id = intval($organization_id);

                $result = $this->db->query("UPDATE activity_activity SET organization_id={$org_id} WHERE id={$id}", __LINE__,__FILE__);

		return isset($result);
	}

        function get_activities_without_groups()
        {
            $activities = array();
            $sql_activities = "select a.*, o.description as org_desc from activity_activity a, bb_organization o where (a.group_id is null or a.group_id = 0) and o.id = a.organization_id";
            $this->db->query($sql_activities, __LINE__, __FILE__);
            while($this->db->next_record())
            {
                $activity_id = $this->db->f('id');
                $activity_title = $this->db->f('title');
                $activity_organization = $this->db->f('organization_id');
                $description = $this->db->f('org_desc');

                $activities[] = array(
                    'id'=>$activity_id,
                    'title'=>$activity_title,
                    'organization'=>$activity_organization,
                    'description'=>$description
                );
            }
            return $activities;
        }

        /*
         * Function to be run once.
         * Generates new groups based on activity where group is not registered.
         * Adds new group to booking
         */
        function generate_groups()
        {
            //TODO
        }


        function remove_old_activities($activity_id)
        {
            //$sql = "delete from activity_activity where id in (1293,1294,1297,1299)"; //1293,1294,1297,1299
						$sql = "delete from activity_activity where id={$activity_id}";
            $result = $this->db->query($sql, __LINE__, __FILE__);

            return isset($result);
        }

        function save_with_no_changes($activity)
        {
            $id = intval($activity->get_id());
            $ts_now = strtotime('now');

            $values = "last_change_date = " . $this->marshal($ts_now, 'int');

            $result = $this->db->query("UPDATE activity_activity SET {$values} WHERE id={$id}", __LINE__,__FILE__);

            return isset($result);
        }

        function update_activity_group($activity_id, $group_id)
        {
            $id = intval($activity_id);
            $g_id = intval($group_id);

            $values = "group_id = " . $g_id;
            //var_dump("UPDATE activity_activity SET {$values} WHERE id={$id}");
            //die;

            $result = $this->db->query("UPDATE activity_activity SET {$values} WHERE id={$id}", __LINE__,__FILE__);

            return isset($result);
        }
}
