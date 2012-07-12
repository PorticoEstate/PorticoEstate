<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('frontend.bofrontend');
	
	class uidelegate extends rental_uicommon
	{
		public $public_functions = array
		(
			'index'				=> true,
			'query'				=> true,
			'add_delegate'		=> true,
			'remove_delegate'	=> true
		
		);
		
		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('rental::delegates');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('delegates');
		}
		
		public function query(){
			$unit_id = (int)phpgw::get_var('unit_id');
			
			$delegates_data = array();
			
			if (isset($unit_id) && $unit_id > 0) {
				$delegates_per_org_unit = frontend_bofrontend::get_delegates($unit_id);

				$delegates_data = array('results' => $delegates_per_org_unit, 'total_records' => count($delegates_per_org_unit));
				
				$editable = phpgw::get_var('editable') == 'true' ? true : false;
				array_walk(
							$delegates_data['results'], 
							array($this, 'add_actions'),
							array(			// Parameters (non-object pointers)
									$unit_id	// [1] The unit id
					));
			
			}

			return $this->yui_results($delegates_data, 'total_records', 'results');
		}
		
		public function index(){
			$this->render('delegate_list.php');
		}
		
		public function add_actions(&$value, $key, $params)
		{			
			$config	= CreateObject('phpgwapi.config','rental');
			$config->read();

			$use_fellesdata = $config->config_data['use_fellesdata'];
			if(($this->isExecutiveOfficer() || $this->isAdministrator()) && $use_fellesdata)
			{
				$unit_id = $params[0];
				
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiresultunit.remove_delegate', 'id' => $unit_id, 'account_id' => $value['account_id'], 'owner_id' => $value['owner_id'])));
				$value['labels'][] = lang('remove');
			}
		}
	}
