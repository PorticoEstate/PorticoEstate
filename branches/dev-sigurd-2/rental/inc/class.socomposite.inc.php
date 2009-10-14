<?php
phpgw::import_class('rental.socommon');
phpgw::import_class('rental.uicommon');

include_class('rental', 'composite', 'inc/model/');
include_class('rental', 'property_location', 'inc/model/');

class rental_socomposite extends rental_socommon
{
	protected static $so;
	
	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.socomposite');
		}
		return self::$so;
	}

	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{		
		$clauses = array('1=1');
		if($search_for)
		{
			$like_pattern = "'%" . $this->db->db_addslashes($search_for) . "%'";
			$like_clauses = array();
			switch($search_type){
				case "id":
					$like_clauses[] = "rental_composite.id = {$this->marshal($search_for, 'int')}";
					break;
				case "name":
					$like_clauses[] = "rental_composite.name $this->like $like_pattern";
					break;
				case "address":
					$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.address_2 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.house_number $this->like $like_pattern";
					break;
				case "property_id":
					$like_clauses[] = "rental_unit.location_code $this->like $like_pattern";
					break;
				case "all":
					$like_clauses[] = "rental_composite.id = {$this->marshal($search_for, 'int')}";
					$like_clauses[] = "rental_composite.name $this->like $like_pattern";
					$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.address_2 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.house_number $this->like $like_pattern";
					$like_clauses[] = "rental_unit.location_code $this->like $like_pattern";
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
				$filter_clauses[] = "rental_composite.is_active = TRUE";
				break;
			case "non_active":
				$filter_clauses[] = "rental_composite.is_active = FALSE";
				break;
			case "both":
				break;
		}

		if(isset($filters['not_in_contract'])){
			$filter_clauses[] = "rental_contract_composite.contract_id != ".$filters['not_in_contract'];
		}
		
		if(isset($filters['contract_id']))
		{
			$filter_clauses[] = "contract_id = {$this->marshal($filters['contract_id'],'int')}";
		}
		
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "rental_composite.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}

		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		$tables = "rental_composite";
		$joins = "	{$this->left_join} rental_unit ON (rental_composite.id = rental_unit.composite_id)";
		$joins .= "	{$this->left_join} rental_contract_composite ON (rental_contract_composite.composite_id = rental_composite.id)";
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(rental_composite.id)) AS count';
		}
		else
		{
			$cols = 'rental_composite.id AS composite_id, rental_unit.id AS unit_id, rental_unit.location_code, rental_composite.name, rental_composite.has_custom_address, rental_composite.address_1, rental_composite.house_number, rental_composite.address_2, rental_composite.postcode, rental_composite.place, rental_composite.is_active';
		}
		$dir = $ascending ? 'ASC' : 'DESC';
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	function populate(int $composite_id, &$composite)
	{ 
		if($composite == null ) // new object
		{
			$composite = new rental_composite($composite_id);
			$composite->set_description($this->unmarshal($this->db->f('description', true), 'string'));
			$composite->set_is_active($this->db->f('is_active'));
			$composite_name = $this->unmarshal($this->db->f('name', true), 'string');
			if($composite_name == null || $composite_name == '')
			{
				$composite_name = lang('no_name_composite', $composite_id);
			}
			$composite->set_name($composite_name);
			$composite->set_has_custom_address($this->unmarshal($this->db->f('has_custom_address', true), 'bool'));
			$composite->set_custom_address_1($this->unmarshal($this->db->f('address_1', true), 'string'));
			$composite->set_custom_address_2($this->unmarshal($this->db->f('address_2', true), 'string'));
			$composite->set_custom_house_number($this->unmarshal($this->db->f('house_number', true), 'string'));
			$composite->set_custom_postcode($this->unmarshal($this->db->f('postcode', true), 'string'));
			$composite->set_custom_place($this->unmarshal($this->db->f('place', true), 'string'));
		}
		// Location code
		$location_code = $this->unmarshal($this->db->f('location_code', true), 'string');
		$location = null;
		try
		{
			if(strpos($location_code, '.') === false)
			{
				// We get the data from the property module
				$data = @execMethod('property.bolocation.read_single', $location_code);
				if($data != null)
				{
					$level = -1;
					$names = array();
					$levelFound = false;
					for($i = 1; !$levelFound; $i++)
					{
						$loc_name = 'loc'.$i.'_name';
						if(array_key_exists($loc_name, $data))
						{
							$level = $i;
							$names[$level] = $data[$loc_name];
						}
						else{
							$levelFound = true;
						}
					}
					$gab_id = '';
					$gabinfos  = execMethod('property.sogab.read', array('location_code' => $location_code, 'sallrows' => true));
					if($gabinfos != null && is_array($gabinfos) && count($gabinfos) == 1)
					{
						$gabinfo = array_shift($gabinfos);
						$gab_id = $gabinfo['gab_id'];
					}
					$location = new rental_property_location($location_code, rental_uicommon::get_nicely_formatted_gab_id($gab_id), $level, $names);
					$location->set_address_1($data['street_name'].' '.$data['street_number']);
					foreach($data['attributes'] as $attributes)
					{
						switch($attributes['column_name'])
						{
							case 'area_gross':
								$location->set_area_gros($attributes['value']);
								break;
							case 'area_net':
								$location->set_area_net($attributes['value']);
								break;
						}
					}
				}
			}
			else
			{
				$location = new rental_property_location($location_code, null, 1, array());
			}
		}
		catch(Exception $e)
		{
			$location = new rental_property_location($location_code, null, 1, array());
		}
		$composite->add_unit(new rental_unit($this->unmarshal($this->db->f('unit_id', true), 'int'), $composite_id, $location));

		return $composite;
	}
	
	public function get_id_field_name(){
		return 'composite_id';
	}

	/**
	 * Update the database values for an existing composite object. Also updates associated rental units.
	 *
	 * @param $composite the composite to be updated
	 * @return result receipt from the db operation
	 */
	public function update($composite)
	{
		$id = intval($composite->get_id());

		$values = array(
			'name = \'' . $composite->get_name() . '\'',
			'description = \'' . $composite->get_description() . '\'',
			'has_custom_address = ' . ($composite->has_custom_address() ? "true" : "false"),
			'address_1 = \'' . $composite->get_custom_address_1() . '\'',
			'address_2 = \'' . $composite->get_custom_address_2() . '\'',
			'house_number = \'' . $composite->get_custom_house_number() . '\'',
			'postcode = \'' . $composite->get_custom_postcode() . '\'',
			'place = \'' . $composite->get_custom_place() . '\'',
			'is_active = \'' . ($composite->is_active() ? 'true' : 'false') . '\''
		);

		$result = $this->db->query('UPDATE rental_composite SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

		return $result != null;
	}
	
	/**
	 * Add a new composite to the database.  Adds the new insert id to the object reference.
	 * Also saves included rental_unit objects.
	 *
	 * @param $composite the composite to be added
	 * @return int with id of the composite
	 */
	public function add(&$composite)
	{
		// Build a db-friendly array of the composite object
		$cols = array('name', 'description', 'has_custom_address', 'address_1', 'address_2', 'house_number', 'postcode', 'place');
		$values = array(
			"'".$composite->get_name()."'",
			"'".$composite->get_description()."'",
			($composite->has_custom_address() ? "true" : "false"),
			"'".$composite->get_custom_address_1()."'",
			"'".$composite->get_custom_address_2()."'",
			"'".$composite->get_custom_house_number()."'",
			"'".$composite->get_custom_postcode()."'",
			"'".$composite->get_custom_place()."'"
		);

		$query ="INSERT INTO rental_composite (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($query);

		$composite_id = $this->db->get_last_insert_id('rental_composite', 'id');
		$composite->set_id($composite_id);
		return $composite_id;
	}
	
	/**
	 * HACK to return the location code for a given contract id. The metod
	 * could've been more generalized, but the Agresso file format already
	 * breaks the model of PE..
	 * 
	 * @param $contract_id int with id of contract.
	 * @return string with location code, empty string if not found.
	 */
	public function get_building_location_code($contract_id)
	{
		$query = "SELECT location_code FROM rental_unit {$this->left_join} rental_contract_composite ON (rental_contract_composite.composite_id = rental_unit.composite_id) WHERE rental_contract_composite.contract_id = {$contract_id}";
		$result = $this->db->limit_query($query, 0, __LINE__, __FILE__, 1);
		
		if($result && $this->db->next_record()) // Query ok
		{
			$location_code = $this->db->f('location_code', true);
			if($location_code != null && $location_code != '')
			{
				return substr(str_replace('-', '', $location_code), 0, 6);
			}
		}
		return '';
	}
	
}
?>
