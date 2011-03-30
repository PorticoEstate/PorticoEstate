<?php
phpgw::import_class('activitycalendar.uicommon');

class activitycalendar_uiorganizationlist extends activitycalendar_uicommon
{
	public $public_functions = array
	(
		'index'				=> true,
		'query'				=> true
	);
	
	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('booking::activities::organizationList');
		$config	= CreateObject('phpgwapi.config','activitycalendar');
		$config->read();
	}
	
	public function index()
	{
		$this->render('organization_list.php', $data);
	}
	

	/**
	 * (non-PHPdoc)
	 * @see rental/inc/rental_uicommon#query()
	 */
	public function query()
	{
		if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
		{
			$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
		}
		else {
			$user_rows_per_page = 10;
		}
		// YUI variables for paging and sorting
		$start_index	= phpgw::get_var('startIndex', 'int');
		$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
		$sort_field		= phpgw::get_var('sort', 'string', 'GET', 'identifier');
		$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
		// Form variables
		$search_for 	= phpgw::get_var('query');
		$search_type	= phpgw::get_var('search_option');
		// Create an empty result set
		$result_objects = array();
		$result_count = 0;
		
		//Create an empty result set
		$parties = array();
		
		$exp_param 	= phpgw::get_var('export');
		$export = false;
		if(isset($exp_param)){
			$export=true;
			$num_of_objects = null;
		}
		
		//Retrieve the type of query and perform type specific logic
		$type = phpgw::get_var('type');

		$config	= CreateObject('phpgwapi.config','activitycalendar');
		$config->read();
		switch($type)
		{
			case 'included_parties': // ... get all parties incolved in the contract
				$filters = array('contract_id' => $contract_id);
				break;
			case 'not_included_parties': // ... get all parties not included in the contract
				$filters = array('not_contract_id' => $contract_id, 'party_type' => phpgw::get_var('party_type'));
				break;
			case 'sync_parties':
			case 'sync_parties_res_unit':
			case 'sync_parties_identifier':
			case 'sync_parties_org_unit':
				$filters = array('sync' => $type, 'party_type' => phpgw::get_var('party_type'), 'active' => phpgw::get_var('active'));
				if($use_fellesdata)
				{
					$bofelles = rental_bofellesdata::get_instance();
				}
				break;
			default: // ... get all parties of a given type
				//$filters = array('party_type' => phpgw::get_var('party_type'), 'active' => phpgw::get_var('active'));
				break;
		}
		
		$result_objects = activitycalendar_soorganization::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
		$result_count = activitycalendar_soorganization::get_instance()->get_count($search_for, $search_type, $filters);
		
		var_dump($result_objects);
		// Create an empty row set
		$rows = array();
		foreach ($result_objects as $organization) {
			if(isset($organization))
			{
				//$serialized = $party->serialize($contract);
				$rows[] = $organization;
			}
		}
		// ... add result data
		$organization_data = array('results' => $rows, 'total_records' => $result_count);

		$editable = phpgw::get_var('editable') == 'true' ? true : false;

		if(!$export){
			array_walk(
				$party_data['results'], 
				array($this, 'add_actions'), 
				array(													// Parameters (non-object pointers)
					$contract_id,										// [1] The contract id
					$type,												// [2] The type of query
					//isset($contract) ? $contract->serialize() : null, 	// [3] Serialized contract
					$editable,											// [4] Editable flag
					$this->type_of_user									// [5] User role			
				)
			);
		}
		
		
		return $this->yui_results($organization_data, 'total_records', 'results');
	}

	/**
	 * Public method. Called when a user wants to view information about a party.
	 * @param HTTP::id	the party ID
	 */
	public function view()
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');
		// Get the contract part id
		$party_id = (int)phpgw::get_var('id');
		if(isset($party_id) && $party_id > 0)
		{
			$party = rental_soparty::get_instance()->get_single($party_id); 
		}
		else
		{
			$this->render('permission_denied.php',array('error' => lang('invalid_request')));
			return;
		}
		
		if(isset($party) && $party->has_permission(PHPGW_ACL_READ))
		{
			return $this->render(
				'party.php', 
				array (
					'party' 	=> $party,
					'editable' => false,
					'cancel_link' => self::link(array('menuaction' => 'rental.uiparty.index', 'populate_form' => 'yes')),
				)
			);
		}
		else
		{
			$this->render('permission_denied.php',array('error' => lang('permission_denied_view_party')));
		}
	}

	public function download_agresso(){
		$browser = CreateObject('phpgwapi.browser');
		$browser->content_header('export.txt','text/plain');
		print rental_soparty::get_instance()->get_export_data();
	}
}
?>