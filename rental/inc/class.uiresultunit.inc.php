<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.bofellesdata');
	phpgw::import_class('frontend.bofrontend');
	
	class rental_uiresultunit extends rental_uicommon
	{
		public $public_functions = array
		(
			'index'				=> true,
			'edit'				=> true,
			'query'				=> true
		);
		
		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('rental::resultunit');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('delegates');
		}
		
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
			
			phpgwapi_cache::session_set('rental', 'composite_query', $search_for);
			phpgwapi_cache::session_set('rental', 'composite_search_type', $search_type);
			
			// Create an empty result set
			$result_count = 0;
			
			
			// get all result unit from fellesdata
			$bofelles = rental_bofellesdata::get_instance();

			$result_units = $bofelles->get_result_units_with_leader($search_for, $search_type);

			foreach($result_units as &$unit) {
				$delegates_per_org_unit = frontend_bofrontend::get_delegates($unit['ORG_UNIT_ID']);
				$unit['UNIT_NO_OF_DELEGATES'] = count($delegates_per_org_unit);
			}
			
			$resultunit_data = array('results' => $result_units, 'total_records' => count($result_units));
			
			$editable = phpgw::get_var('editable') == 'true' ? true : false;
			
			array_walk($resultunit_data['results'], array($this, 'add_actions'));
			
			return $this->yui_results($resultunit_data, 'total_records', 'results');
		}
		
		/**
		* View a list of all resultunits
		*/
		public function index()
		{
			$search_for = phpgw::get_var('search_for');
			if($search_for)
			{
				phpgwapi_cache::session_set('rental', 'resultunit_query', $search_for);
			}
			$this->render('resultunit_list.php');
		}
		
		
		public function add_actions(&$value)
		{
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiresultunit.edit', 'id' => $value['ORG_UNIT_ID'], 'initial_load' => 'no')));
			$value['labels'][] = lang('edit_delegate');
		}
		
		public function edit(){
			
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
			$unit_id = (int)phpgw::get_var('id');
			
			if (isset($unit_id) && $unit_id > 0) {
				
				$bofelles = rental_bofellesdata::get_instance();
				$unit = $bofelles->get_result_unit($unit_id);
				
				$delegates_per_org_unit = frontend_bofrontend::get_delegates($unit_id);
				$unit['UNIT_NO_OF_DELEGATES'] = count($delegates_per_org_unit);
				
				$this->render('resultunit.php', array ('unit' => $unit));
			}
		}
	}