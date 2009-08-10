<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.uidocument_composite');
	include_class('rental', 'composite', 'inc/model/');
	
	class rental_uicomposite extends rental_uicommon
	{	
		public $public_functions = array
		(
			'index'		=> true,
			'view'		=> true,
			'edit'		=> true,
			'columns'	=> true,
			'add'		=> true,
			'add_unit' => true,
			'remove_unit' => true,
			'orphan_units' => true,
			'query'		=> true
		);

		public function __construct()
		{
			parent::__construct(); 
			 
			self::set_active_menu('rental::composites');
		}
		
		/**
		 * Shows a list of composites
		 */
		public function index()
		{			
			if(!$this->hasReadPermission())
			{
				$this->render('permission_denied.php');
				return;	
			}
			$this->render('composite_list.php');
			
		}

		//View rental composite
		public function view() {
		if(!self::hasReadPermission())
			{
				$this->render('permission_denied.php');
				return;
			}
			$composite_id = (int)phpgw::get_var('id');
			return $this -> viewedit(false, $composite_id);
		}
		
		//Edit rental composite
		public function edit(){
		if(!$this->hasWritePermission())
			{
				$this->render('permission_denied.php');
				return;
			}	
			$composite_id = (int)phpgw::get_var('id');
			if(isset($_POST['save_composite']))
			{
				$composite = new rental_composite($composite_id);
				$composite->set_name(phpgw::get_var('name'));
				$composite->set_gab_id(phpgw::get_var('gab_id'));
				$composite->set_address_1(phpgw::get_var('address_1'));
				$composite->set_has_custom_address($composite->get_address_1() != null && $composite->get_address_1() != '' ? true : false);
				// XXX: Why do we have to use these functionand not the set_custom_*() ones? Does the SO layer use the incorrect functions?
				$composite->set_house_number(phpgw::get_var('house_number'));
				$composite->set_address_2(phpgw::get_var('address_2'));
				$composite->set_postcode(phpgw::get_var('postcode'));
				$composite->set_place(phpgw::get_var('place'));
				$composite->set_is_active(phpgw::get_var('is_active') == 'on' ? true : false);
				$composite->set_description(phpgw::get_var('description'));
				$composite->store();
				// XXX: How to get error msgs back to user?
			}
			return $this -> viewedit(true, $composite_id);
		}
		
		//Create new rental composite
		public function add()
		{
		if(!$this->hasWritePermission())
			{
				$this->render('permission_denied.php');
				return;
			}
			$composite = new rental_composite();
			$composite->set_name(phpgw::get_var('rental_composite_name'));
			$receipt = rental_composite::add($composite);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicomposite.edit', 'id' => $receipt['id'], 'message' => lang('rental_messages_new_composite')));
		}
		
		public function query()
		{
			if(!$this->hasReadPermission())
			{
				$this->render('permission_denied.php');
				return;
			}
			$type = phpgw::get_var('type');
			switch($type)
			{
				case 'available_composites':
					$rows = array();
					$composites = rental_composite::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array( 'is_vacant' => 'vacant'));
					foreach ($composites as $composite) {
						$rows[] = $composite->serialize();
					}
					$composite_data = array('results' => $rows, 'total_records' => count($rows));
					break;
				case 'included_composites':
					$contract_id = phpgw::get_var('contract_id');
					$contract = rental_contract::get($contract_id);
					$composites = $contract->get_composites();
					$rows = array();
					foreach ($composites as $composite) {
						$rows[] = $composite->serialize();
					}
					$composite_data = array('results' => $rows, 'total_records' => count($rows));
					break;
				case 'not_included_composites':
					$composites = rental_composite::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							'is_active' => phpgw::get_var('is_active'), 
							'is_vacant' => phpgw::get_var('occupancy'),
							'contract_id' => phpgw::get_var('contract_id')
						)
					);
					$rows = array();
					foreach ($composites as $composite) {
						$rows[] = $composite->serialize();
					}
					$composite_data = array('results' => $rows, 'total_records' => count($rows));
					break;
				case 'details':
					$composite_data = rental_composite::get(phpgw::get_var('id'))->serialize();
					break;
				case 'included_areas':
					$composite = rental_composite::get(phpgw::get_var('id'));
					$rental_units = $composite->get_included_rental_units(phpgw::get_var('sort'), phpgw::get_var('dir'), phpgw::get_var('startIndex'), phpgw::get_var('results'));
					$composite_data = array();
					$composite_data[total_records] = count($rental_units);
					$composite_data['results'] = array();
					foreach ($rental_units as $unit) {
//						var_dump($unit);
						$result = array
						(
							'location_code' => $unit->get_location_code(),
							'location_id' => $unit->get_location_id(),
							'loc1' => $unit->get_location_code_property(),
							'address' => $unit->get_address(),
							'area_net' => $unit->get_area_net(),
							'area_gros' => $unit->get_area_gros(),
							'loc1_name' => $unit->get_property_name()
						);
						if($unit instanceof rental_building)
						{
							$result['loc2_name'] = $unit->get_building_name();
							if($unit instanceof rental_floor)
							{
								$result['loc3_name'] = $unit->get_floor_name();
								if($unit instanceof rental_section)
								{
									$result['loc4_name'] = $unit->get_section_name();
									if($unit instanceof rental_room)
									{
										$result['loc5_name'] = $unit->get_room_name();
									}
								}
							}
						}
						$composite_data['results'][] = $result;
					}
					break;
				case 'available_areas':
					$composite_data = array();
					$composite_data[total_records] = count(rental_unit::get_available_rental_units((int)phpgw::get_var('level'), phpgw::get_var('available_date_hidden'), phpgw::get_var('id'), 0, 10000));
					$composite_data['results'] = array();
					$unit_array = rental_unit::get_available_rental_units((int)phpgw::get_var('level'), phpgw::get_var('id'), phpgw::get_var('available_date_hidden'), phpgw::get_var('startIndex'), phpgw::get_var('results'), phpgw::get_var('sort'), phpgw::get_var('dir') == ' desc' ? false : true);
					foreach($unit_array as $unit)
					{
						$occupied_date_array = $unit->get_occupied_date_array();
						if($occupied_date_array !== null)
						{
							$data = &$composite_data['results'][];
							$data['location_code'] = $unit->get_location_code();
							$data['location_id'] = $unit->get_location_id();
							$data['loc1'] = $unit->get_location_code_property();
							$data['address'] = $unit->get_address();
							$data['area_net'] = $unit->get_area_net();
							$data['area_gros'] = $unit->get_area_gros();
							$data['loc1_name'] = $unit->get_property_name();
							$occupied = '';
							if(count($occupied_date_array) == 0)
							{
								$occupied = lang('rental_common_available');
							}
							else
							{
								$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
								foreach($occupied_date_array as $contract_date)
								{
									if($occupied != '')
									{
										$occupied .= ', ';
									}
									$occupied .= ($contract_date->has_start_date() ? date($date_format, strtotime($contract_date->get_start_date())) : '').' - '.($contract_date->has_end_date() ? date($date_format, strtotime($contract_date->get_end_date())) : '');
								}
								$occupied = lang('rental_common_occupied').' '.$occupied;
							}
							$data['occupied'] = $occupied;
							if($unit instanceof rental_building)
							{
								$data['loc2_name'] = $unit->get_building_name();
								if($unit instanceof rental_floor)
								{
									$data['loc3_name'] = $unit->get_floor_name();
									if($unit instanceof rental_section)
									{
										$data['loc4_name'] = $unit->get_section_name();
										if($unit instanceof rental_room)
										{
											$data['loc5_name'] = $unit->get_room_name();
										}
									}
								}
							}
						}
					}
					break;
				case 'contracts':
					$composite = rental_composite::get(phpgw::get_var('id'));
					$contracts = $composite->get_contracts(phpgw::get_var('id'), phpgw::get_var('sort'), phpgw::get_var('dir'), phpgw::get_var('startIndex'), phpgw::get_var('results'), phpgw::get_var('contract_status'), phpgw::get_var('contract_date'));
					$composite_data = array();
					$composite_data[total_records] = count($contracts);
					$composite_data['results'] = array();
					
					foreach ($contracts as $contract) {
						$composite_data['results'][] = array(
							'id' => $contract->get_id(),
							'date_start' => $contract->get_contract_date()->get_start_date(),
							'date_end' => $contract->get_contract_date()->get_end_date(),
							'billing_start_date' => $contract->get_billing_start_date(),
							'type_id' => $contract->get_type_id(),
							'term_id' => $contract->get_term_id(),
							'account' => $contract->get_account()
						);
					}
					break;
				case 'orphan_units':
					$composite_data = array();
					$units = rental_unit::get_orphan_rental_units(phpgw::get_var('startIndex'), phpgw::get_var('results'));
					$composite_data[total_records] = rental_unit::get_orphan_rental_unit_count();
					$composite_data['results'] = array();
					
					foreach($units as $unit)
					{
						$data = &$composite_data['results'][];
						$data['location_code'] = $unit->get_location_code();
						$data['location_id'] = $unit->get_location_id();
						$data['loc1'] = $unit->get_location_code_property();
						$data['address'] = $unit->get_address();
						$data['area_net'] = $unit->get_area_net();
						$data['area_gros'] = $unit->get_area_gros();
						$data['loc1_name'] = $unit->get_property_name();

						if($unit instanceof rental_building)
						{
							$data['loc2_name'] = $unit->get_building_name();
						}
						if($unit instanceof rental_floor)
						{
							$data['loc3_name'] = $unit->get_floor_name();
						}
						if($unit instanceof rental_section)
						{
							$data['loc4_name'] = $unit->get_section_name();
						}
						if($unit instanceof rental_room)
						{
							$data['loc5_name'] = $unit->get_room_name();
						}
					}
					break;
				case 'all_composites':
				default:
					$rows = array();
					$composites = rental_composite::get_all(phpgw::get_var('startIndex'),phpgw::get_var('results'),phpgw::get_var('sort'),phpgw::get_var('dir'),phpgw::get_var('query'),phpgw::get_var('search_option'),array('is_active' => phpgw::get_var('is_active'), 'is_vacant' => phpgw::get_var('occupancy')));
					foreach ($composites as $composite) {
						$rows[] = $composite->serialize();
					}
					$composite_data = array('results' => $rows, 'total_records' => count($rows));
					break;
					
			}
			
			$editable = phpgw::get_var('editable') == 'true' ? true : false;
			
			//Add action column to each row in result table
			array_walk($composite_data['results'], array($this, 'add_actions'), array(phpgw::get_var('id'),$type,$editable));
			
			return $this->yui_results($composite_data, 'total_records', 'results');
		}
		
		/**
		 * Add action links and labels for the context menu of the list items
		 * 
		 * @param $value pointer to 
		 * @param $key ?
		 * @param $params [composite_id, type of query, editable]
		 */
		public function add_actions(&$value, $key, $params)
		{
		
			$value['actions'] = array();
			$value['labels'] = array();
			
			$editable = $params[2];
			
			switch($params[1])
			{
				case 'included_composites':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id'])));
					$value['labels'][] = lang('rental_common_show');
					if($this->hasWritePermission() && $editable == true)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.remove_composite', 'composite_id' => $value['id'], 'contract_id' => phpgw::get_var('contract_id'))));
						$value['labels'][] = lang('rental_common_remove');
					}
					break;
				case 'not_included_composites':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id'])));
					$value['labels'][] = lang('rental_common_show');
					if($this->hasWritePermission() && $editable == true)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_composite', 'composite_id' => $value['id'], 'contract_id' => phpgw::get_var('contract_id'))));
						$value['labels'][] = lang('rental_common_add');
					}
					break;
				case 'included_areas':
					$value['ajax'][] = true;
					if($this->hasWritePermission() && $editable == true)
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.remove_unit', 'id' => $params[0], 'location_id' => $value['location_id'])));
						$value['labels'][] = lang('rental_common_remove');
					}
					break;
				case 'available_areas':
					$value['ajax'][] = true;
					if($this->hasWritePermission() && $editable == true)
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.add_unit', 'id' => $params[0], 'location_id' => $value['location_id'], 'loc1' => $value['loc1'])));
						$value['labels'][] = lang('rental_common_add');
					}
					break;
				case 'orphan_units':
					// No actions
					break;
				case 'contracts':
					$value['ajax'][] = false;
					$value['actions']['view_contract'] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'])));
					$value['labels'][] = lang('rental_common_show');
					if($this->hasWritePermission() && $editable == true)
					{
						$value['ajax'][] = false;
						$value['actions']['edit_contract'] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('rental_common_edit');
					}
					break;
				default:
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id'])));
					$value['labels'][] = lang('rental_common_show');
					
					if($this->hasWritePermission()) 
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('rental_common_edit');
					}
			}
		}
		
		/**
		 * View or edit rental composite
		 * 
		 * @param $editable true renders fields editable, false renders fields disabled
		 * @param $composite_id	the rental composite id	
		 */
		protected function viewedit($editable, $composite_id)
		{
			if ($composite_id > 0) {
				$composite = rental_composite::get($composite_id);
				$data = array
				(
					'composite' 	=> $composite,
					'editable' => $editable,
					'message' => phpgw::get_var('message'),
					'error' =>  phpgw::get_var('error'),
					'cancel_link' => self::link(array('menuaction' => 'rental.uicomposite.index'))
				);				
				$this->render('composite.php', $data);
			}
		}
		
		//Add a unit to a rental composite
		function add_unit()
		{
			if(!$this->hasWritePermission())
			{
				$this->render('permission_denied.php');
				return;
			}
			$composite_id = (int)phpgw::get_var('id');
			$composite = rental_composite::get($composite_id);
			
			if (($composite) != null) {
				$location_id = (int)phpgw::get_var('location_id');
				$loc1 = (int)phpgw::get_var('loc1');
				$composite->add_unit(new rental_property($loc1, $location_id));
				$composite->store();
			}
			
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicomposite.edit', 'id' => $composite_id, 'active_tab' => 'rental_common_area'));
		}
		
		//Remove a unit from a rental composite
		function remove_unit()
		{
			if(!$this->hasWritePermission())
			{
				$this->render('permission_denied.php');
				return;
			}
			$composite_id = (int)phpgw::get_var('id');
			$composite = rental_composite::get($composite_id);

			$location_id = (int)phpgw::get_var('location_id');
						
			if ($composite != null) {
				$composite->remove_unit(new rental_property(null, $location_id));
				$composite->store();
			}
			
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicomposite.edit', 'id' => $composite_id, 'active_tab' => 'rental_common_area'));
			
		}

		/**
		 * Get a list of rental units or areas that are not tied to any rental composite
		 * 
		 */
		public function orphan_units()
		{
			if(!$this->hasReadPermission())
			{
				$this->render('permission_denied.php');
				return;
			}
			
			self::set_active_menu('rental::composites::orphan_units');
			
			$data = array
			(
				'message' => phpgw::get_var('message'),
				'error' =>  phpgw::get_var('error'),
				'cancel_link' => self::link(array('menuaction' => 'rental.uicomposite.orphan_units'))
			);
			
			$this->render('orphan_unit_list.php', $data);
		}
				
		/**
		 * Stores which columns that should be displayed in index(). The data
		 * is stored per user.
		 *  
		 */
		function columns()
		{
			$values = phpgw::get_var('values');
			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->account_id=$GLOBALS['phpgw_info']['user']['account_id'];
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('rental','rental_columns_composite',$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
			}
		}
	}
?>