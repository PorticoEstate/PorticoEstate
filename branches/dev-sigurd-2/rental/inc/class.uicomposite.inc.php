<?php
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.sounit');
	phpgw::import_class('rental.uicommon');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'property_location', 'inc/model/');

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
			'query'		=> true,
			'download'	=> true
		);

		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('rental::composites');
		}
		
		public function query()
		{
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', 1000);
			$sort_field		= phpgw::get_var('sort');
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			// Form variables
			$search_for 	= phpgw::get_var('query');
			$search_type	= phpgw::get_var('search_option');
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;
			
			//Retrieve a contract identifier and load corresponding contract
			$contract_id = phpgw::get_var('contract_id');
			if(isset($contract_id))
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);
			}
			
			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');
			switch($query_type)
			{
				case 'available_composites': // ... get all vacant composites
					$filters = array('is_vacant' => 'vacant');
					$result_objects = rental_socomposite::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_socomposite::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'included_composites': // ... get all composites in contract
					if(isset($contract))
					{
						$result_objects = $contract->get_composites();
						$result_count = count($result_objects);
					}
					break;
				case 'not_included_composites': // ... get all vacant and active composites not in contract
					$filters = array('is_active' => phpgw::get_var('is_active'), 'is_vacant' => phpgw::get_var('occupancy'), 'not_in_contract' => phpgw::get_var('contract_id'));
					$result_objects = rental_socomposite::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_socomposite::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'all_composites': // ... all composites, filters (active and vacant)
					$filters = array('is_active' => phpgw::get_var('is_active'), 'is_vacant' => phpgw::get_var('occupancy'));
					$result_objects = rental_socomposite::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_socomposite::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'included_areas': // Returns areas/units added to a specified composite
					$filters = array('included_areas' => phpgw::get_var('composite_id')); // Composite id
					$result_objects = rental_sounit::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_sounit::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'available_areas': // Returns areas/units available for a specified composite
					$filters = array('available_areas' => phpgw::get_var('id')); // Composite id
					$result_objects = rental_sounit::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_sounit::get_instance()->get_count($search_for, $search_type, $filters);
					break;
			}

			//Create an empty row set
			$rows = array();
			foreach($result_objects as $result) {
				if(isset($result))
				{
					if($result->has_permission(PHPGW_ACL_READ))
					{
						// ... add a serialized result
						$rows[] = $result->serialize();
					}
				}
			}
			
			// ... add result data
			$result_data = array('results' => $rows, 'total_records' => $object_count);
			
			$editable = phpgw::get_var('editable') == 'true' ? true : false;

			//Add action column to each row in result table
			array_walk(
				$result_data['results'],
				array($this, 'add_actions'), 
				array(													// Parameters (non-object pointers)
					$contract_id,										// [1] The contract id
					$type,												// [2] The type of query
					$editable,											// [3] Editable flag
					$this->type_of_user,								// [4] User role			
					isset($contract) ? $contract->serialize() : null,	// [5] Serialized contract
				)
			);

			return $this->yui_results($result_data, 'total_records', 'results');
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
			//Defining new columns
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			// Get parameters
			$contract_id = $params[0];
			$type = $params[1];
			$editable = $params[2];
			$user_is = $params[3];
			$serialized_contract= $params[4];

			// Get permissions on contract
			if(isset($serialized_contract))
			{
				$permissions = $serialized_contract['permissions'];
			}
			
			// Depending on the type of query: set an ajax flag and define the action and label for each row
			switch($type)
			{
				case 'included_composites':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id'])));
					$value['labels'][] = lang('show');
					if($permissions[PHPGW_ACL_EDIT] && $editable == true)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.remove_composite', 'composite_id' => $value['id'], 'contract_id' => $contract_id)));
						$value['labels'][] = lang('remove');
					}
					break;
				case 'not_included_composites':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id'])));
					$value['labels'][] = lang('show');
					if($permissions[PHPGW_ACL_EDIT] && $editable == true)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_composite', 'composite_id' => $value['id'], 'contract_id' => $contract_id)));
						$value['labels'][] = lang('add');
					}
					break;
				case 'included_areas':
					$value['ajax'][] = true;
					if($user_is[EXECUTIVE_OFFICER] && $editable == true)
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.remove_unit', 'id' => $contract_id, 'location_id' => $value['location_id'])));
						$value['labels'][] = lang('remove');
					}
					break;
				case 'available_areas':
					$value['ajax'][] = true;
					if($user_is[EXECUTIVE_OFFICER] && $editable == true)
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.add_unit', 'id' => $contract_id, 'location_id' => $value['location_id'], 'loc1' => $value['loc1'])));
						$value['labels'][] = lang('add');
					}
					break;
				case 'orphan_units':
					// TODO: What should this one really do?
					// No actions
					break;
				case 'contracts':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'])));
					$value['labels'][] = lang('show');
					if($permissions[PHPGW_ACL_EDIT] && $editable == true)
					{
						$value['ajax'][] = false;
						$value['actions']['edit_contract'] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('edit');
					}
					break;
				default:
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id'])));
					$value['labels'][] = lang('show');

					if($user_is[EXECUTIVE_OFFICER])
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('edit');
					}
			}
		}

		/**
		 * Shows a list of composites
		 */
		public function index()
		{
			$this->render('composite_list.php');
		}
		
		/**
	 	* Public method. Forwards the user to edit mode.
	 	*/
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicomposite.edit'));
		}

		/**
		 * Public method. Called when a user wants to view information about a composite.
		 * @param HTTP::id	the composite ID
		 */
		public function view() {
			//Retrieve the composite object
			$composite_id = (int)phpgw::get_var('id');
			if(isset($composite_id) && $composite_id > 0)
			{
				$composite = rental_socomposite::get_instance()->get_single($composite_id);
			}
			else
			{
				$this->render('permission_denied.php',array('error' => lang('invalid_request')));
				return;
			}

			if(isset($composite) && $composite->has_permission(PHPGW_ACL_READ))
			{
				return $this->render(
					'composite.php', 
					array (
						'composite' 	=> $composite,
						'editable' => false,
						'cancel_link' => self::link(array('menuaction' => 'rental.uicomposite.index'))
					)
				);	
			}
			else
			{
				$this->render('permission_denied.php',array('error' => lang('permission_denied_view_composite')));
			}
			
		}

		/**
		 * Public method. Called when user wants to edit a composite.
		 * @param HTTP::id	the composite ID
		 */
		public function edit(){
			// Get the composite ID
			$composite_id = (int)phpgw::get_var('id');
			
			// Retrieve the party object or create a new one if correct permissions
			if(($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				if(isset($composite_id) && $composite_id > 0)
				{
					$composite = rental_socomposite::get_instance()->get_single($composite_id);
				}
				else
				{
					$composite = new rental_composite();
				}
			}
			else
			{
				$this->render('permission_denied.php',array('error' => lang('permission_denied_edit')));
			}
			
			if(isset($_POST['save_composite'])) // The user has pressed the save button
			{
				if(isset($composite))
				{
					$composite->set_name(phpgw::get_var('name'));
					$composite->set_custom_address_1(phpgw::get_var('address_1'));
					$composite->set_has_custom_address($composite->get_custom_address_1() != null && $composite->get_custom_address_1() != '' ? true : false);
					$composite->set_custom_house_number(phpgw::get_var('house_number'));
					$composite->set_custom_address_2(phpgw::get_var('address_2'));
					$composite->set_custom_postcode(phpgw::get_var('postcode'));
					$composite->set_custom_place(phpgw::get_var('place'));
					$composite->set_is_active(phpgw::get_var('is_active') == 'on' ? true : false);
					$composite->set_description(phpgw::get_var('description'));
					
					if(rental_socomposite::get_instance()->store($composite))
					{
						$message = lang('messages_saved_form');
					}
					else
					{
						$error = lang('messages_form_error');
					}
				}
			}
			return $this->render('composite.php', array
				(
					'composite' 	=> $composite,
					'editable' => true,
					'message' => isset($message) ? $message : phpgw::get_var('message'),
					'error' => isset($error) ? $error : phpgw::get_var('error'),
					'cancel_link' => self::link(array('menuaction' => 'rental.uicomposite.index')),
				)	
			);
			
		}


		/**
		 * Public method. Called when user wants to add a unit to a composite
		 * @param HTTP::id	the composite ID
		 * @param HTTP::location_id
		 * @param HTTP::loc1
		 */
		function add_unit()
		{
			if(!$this->isExecutiveOfficer())
			{
				$this->render('permission_denied.php', array('message' => lang('permission_denied')));
				return;
			}
			$composite_id = (int)phpgw::get_var('id');
			if(isset($composite_id) && $composite_id > 0)
			{
				$composite = rental_composite::get($composite_id);

				if (isset($composite)) {
					$location_id = (int)phpgw::get_var('location_id');
					$loc1 = (int)phpgw::get_var('loc1');
					$composite->add_new_unit(new rental_property($loc1, $location_id));
					//$composite->store();
				}
			}
			

			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicomposite.edit', 'id' => $composite_id, 'active_tab' => 'area'));
		}

		/**
		 * Public method. Called when user wants to remove a unit to a composite
		 * @param HTTP::id	the composite ID
		 * @param HTTP::location_id
		 */
		function remove_unit()
		{
			if(!$this->isExecutiveOfficer())
			{
				$this->render('permission_denied.php', array('message' => lang('permission_denied')));
				return;
			}
			$composite_id = (int)phpgw::get_var('id');
			$location_code = phpgw::get_var('location_id');
			if($composite_id > 0 && isset($location_code))
			{
				rental_sounit::get_instance()->delete($composite_id, $location_code);
			}
		}

		/**
		 * Get a list of rental units or areas that are not tied to any rental composite
		 *
		 */
		public function orphan_units()
		{
			if(!$this->isExecutiveOfficer())
			{
				$this->render('permission_denied.php', array('message' => lang('permission_denied')));
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