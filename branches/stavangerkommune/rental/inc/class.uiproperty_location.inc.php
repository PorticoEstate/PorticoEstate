<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('property.bolocation');
	phpgw::import_class('property.sogab');
	class rental_uiproperty_location extends rental_uicommon
	{	
		public $public_functions = array
		(
			'query' => true
		);
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public function query()
		{
			// TODO: access control
			$type_id	= phpgw::get_var('type_id');
			$composite_id = phpgw::get_var('composite_id');
			$search_type	= phpgw::get_var('search_option');
			
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', 10);
			$sort_field		= phpgw::get_var('sort');
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			
			$property_bolocation =  new property_bolocation();
			$property_bolocation->order = $sort_field;
			$property_bolocation->sort = phpgw::get_var('dir');
			$property_bolocation->start = $start_index;
			
			if($search_type == 'gab')
			{
				$q = phpgw::get_var('query');
				$query = explode('/', $q);
				//GAB search
				$property_sogab = new property_sogab();
				$gabinfo = $property_sogab->read(array(
					'gaards_nr' => empty($query[0])?'':$query[0],
					'bruksnr' => empty($query[1])?'':$query[1],
					'feste_nr' => empty($query[2])?'':$query[2],
					'seksjons_nr' => empty($query[3])?'':$query[3],
					'allrows' => true));
				
				//var_dump($gabinfo);
				
				foreach ($gabinfo as $gabelement)
				{
					$row = $property_bolocation->read_single($gabelement['location_code']);
					$row['gab'] = rental_uicommon::get_nicely_formatted_gab_id($gabelement['gab_id']);  
					$rows[] = $row;
					$rows_total[] = $row; 
					//TODO: Add gabno for element 
				}
			}
			else
			{
				if(!isset($type_id) || $type_id < 1)
				{
					$type_id = 2;
				}
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
				$tmp_count = phpgw::get_var('results', 'int', 'GET', 0);
				if(isset($tmp_count) && $tmp_count > 0)
				{
					$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = $num_of_objects;
				}
				$rows_total = $property_bolocation->read(array('type_id' => $type_id, 'allrows' => true));
				$rows = $property_bolocation->read(array('type_id' => $type_id));
				$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] = $user_rows_per_page;
			}
			
			//Add context menu columns (actions and labels)
			array_walk($rows, array($this, 'add_actions'), array($composite_id, $type_id));
			//Build a YUI result from the data
			//
			$result_data = array('results' => $rows, 'total_records' => count($rows_total));	
			return $this->yui_results($result_data, 'total_records', 'results');
		}
		
		/**
		 * Add data for context menu
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [type of query, editable]
		 */
		public function add_actions(&$value, $key, $params)
		{
			unset($value['query_location']);
			
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();
			
			$composite_id = $params[0];
			$type_id = $params[1];
			
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uilocation.view', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('show');
			
			$value['ajax'][] = true;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.add_unit', 'location_code' => $value['location_code'], 'composite_id' => $composite_id, 'level' => $type_id)));
			$value['labels'][] = lang('add_location');
			
		}
	}
?>
