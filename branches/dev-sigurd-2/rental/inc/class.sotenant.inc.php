<?php

	phpgw::import_class('rental.socommon');
	
	include_class('rental', 'tenant', 'inc/model/');
	
	class rental_sotenant extends rental_socommon
	{
		function __construct()
		{
			parent::__construct('rental_tenant',
			array
			(
				'id'	=> array('type' => 'int'),
				'agresso_id' => array('type' => 'string'),
				'personal_identification_number' => array('type' => 'string'),
				'first_name' => array('type' => 'string'),
				'last_name' => array('type' => 'string'),
				'type_id'	=> array('type' => 'int'),
				'is_active' => array('type', 'bool'),
				'title' => array('type' => 'string'),
				'company_name' => array('type' => 'string'),
				'department' => array('type' => 'string'),
				'address_1' => array('type' => 'string'),
				'address_2' => array('type' => 'string'),
				'postal_code' => array('type' => 'string'),
				'place' => array('type' => 'string'),
				'phone' => array('type' => 'string'),
				'fax' => array('type' => 'string'),
				'email' => array('type' => 'string'),
				'url' => array('type' => 'string'),
				'post_bank_account_number' => array('type' => 'string'),
				'account_number' => array('type' => 'string'),
				'reskontro' => array('type' => 'string')
			));
		}
		
		/**
		 * Get single tenant
		 * 
		 * @param	$id	id of the tenant to return
		 * @return a rental_tenant
		 */
		function get_single($id)
		{
			$id = (int)$id;

      $sql = "SELECT * FROM " . $this->table_name ." WHERE " . $this->table_name . ".id={$id}";

      $this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

      $tenant = new rental_tenant();

      $this->db->next_record();

      $tenant->set_id($this->unmarshal($this->db->f('id', true), 'int'));
      $tenant->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
      $tenant->set_personal_identification_number($this->unmarshal($this->db->f('personal_identification_number', true), 'string'));
      $tenant->set_first_name($this->unmarshal($this->db->f('first_name', true), 'string'));
      $tenant->set_last_name($this->unmarshal($this->db->f('last_name', true), 'string'));
      $tenant->set_type_id($this->unmarshal($this->db->f('type_id', true), 'int'));
      $tenant->set_is_active($this->unmarshal($this->db->f('is_active', true), 'bool'));

      $tenant->set_title($this->unmarshal($this->db->f('title', true), 'string'));
      $tenant->set_company_name($this->unmarshal($this->db->f('company_name', true), 'string'));
      $tenant->set_department($this->unmarshal($this->db->f('department', true), 'string'));

      $tenant->set_address_1($this->unmarshal($this->db->f('address_1', true), 'string'));
      $tenant->set_address_2($this->unmarshal($this->db->f('address_2', true), 'string'));
      $tenant->set_postal_code($this->unmarshal($this->db->f('postal_code', true), 'string'));
      $tenant->set_place($this->unmarshal($this->db->f('place', true), 'string'));

      $tenant->set_phone($this->unmarshal($this->db->f('phone', true), 'string'));
      $tenant->set_fax($this->unmarshal($this->db->f('fax', true), 'string'));
      $tenant->set_email($this->unmarshal($this->db->f('email', true), 'string'));
      $tenant->set_url($this->unmarshal($this->db->f('url', true), 'string'));
      $tenant->set_post_bank_account_number($this->unmarshal($this->db->f('post_bank_account_number', true), 'string'));
      $tenant->set_account_number($this->unmarshal($this->db->f('account_number', true), 'string'));
      $tenant->set_reskontro($this->unmarshal($this->db->f('reskontro', true), 'string'));

      return $tenant;
		}

	/**
	 * Get a list of composite objects matching the specific filters
	 * 
	 * @param $start search result offset
	 * @param $results number of results to return
	 * @param $sort field to sort by
	 * @param $query LIKE-based query string
	 * @param $filters array of custom filters
	 * @return list of rental_tenant objects
	*/
	function get_tenant_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$condition = $this->get_conditions($query, $filters,$search_option);
		$this->db->limit_query("SELECT * FROM rental_tenant WHERE $condition $order", $start, __LINE__, __FILE__, $limit);

		$tenants = array();

		while ($this->db->next_record()) {
        	$row = array();
			foreach($this->fields as $field => $fparams)
			{
				$row[$field] = $this->unmarshal($this->db->f($field, true), $params['type']);
			}

			$tenant = new rental_tenant($row['id']);

			$tenant->set_agresso_id($row['agresso_id']);
			$tenant->set_personal_identification_number($row['personal_identification_number']);
			$tenant->set_first_name($row['first_name']);
			$tenant->set_last_name($row['last_name']);
			$tenant->set_type_id($row['type_id']);
			$tenant->set_is_active($row['is_active']);

			$tenant->set_title($row['title']);
			$tenant->set_company_name($row['company_name']);
			$tenant->set_department($row['department']);
			
			$tenant->set_address_1($row['address_1']);
			$tenant->set_address_2($row['address_2']);
			$tenant->set_postal_code($row['postal_code']);
			$tenant->set_place($row['place']);
			
			$tenant->set_phone($row['phone']);
			$tenant->set_fax($row['fax']);
			$tenant->set_email($row['email']);
			$tenant->set_url($row['url']);
			$tenant->set_post_bank_account_number($row['post_bank_account_number']);
			$tenant->set_account_number($row['account_number']);
			$tenant->set_reskontro($row['reskontro']);
			
			$tenants[] = $tenant;
      	}

		return $tenants;
    }
	
		function add()
		{
			parent::add(array('is_active' => true));
			return $receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');	
		}
		
		protected function get_conditions($query, $filters,$search_option)
		{	
			$clauses = array('1=1');
			if($query)
			{
				
				$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
				$like_clauses = array();
				switch($search_option){
					case "id":
						$like_clauses[] = "rental_tenant.id = $query";
						break;
					case "name":
						$like_clauses[] = "rental_tenant.first_name $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.last_name $this->like $like_pattern";
						break;
					case "address":
						$like_clauses[] = "rental_tenant.address_1 $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.address_2 $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.postal_code $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.place $this->like $like_pattern";
						break;
					case "ssn":
						$like_clauses[] = "rental_tenant.personal_identification_number $this->like $like_pattern";
						break;
					case "result_unit_number":
						$like_clauses[] = "rental_tenant.result_unit $this->like $like_pattern";
					case "organisation_number":
						$like_clauses[] = "rental_tenant.organisation_number $this->like $like_pattern";
					case "account":
						$like_clauses[] = "rental_tenant.reskontro = $like_pattern";
					case "all":
						$like_clauses[] = "rental_tenant.first_name $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.last_name $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.address_1 $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.address_2 $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.postal_code $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.place $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.personal_identification_number $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.result_unit $this->like $like_pattern";
						$like_clauses[] = "rental_tenant.organisation_number = $like_pattern";
						$like_clauses[] = "rental_tenant.reskontro = $like_pattern";
						break;
				}
				
				
				if(count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
				
				
			}
			
			$filter_clauses = array();
			switch($filters['is_active']){
				case "active":
					$filter_clauses[] = "rental_tenant.is_active = TRUE";
					break;
				case "non_active":
					$filter_clauses[] = "rental_tenant.is_active = FALSE";
					break;
				case "both":
					break;
			}
				
			if(count($filter_clauses))
				{
					$clauses[] = join(' AND ', $filter_clauses);
				}
			
			return join(' AND ', $clauses);
		}
	}
?>