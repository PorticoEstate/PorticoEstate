<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('property.bolocation');
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
			
			if(!isset($type_id) || $type_id < 1)
			{
				$type_id = 1;
			}
			$property_bolocation =  new property_bolocation();
			$rows = $property_bolocation->read(array('type_id' => $type_id, 'sallrows' => true));
			
			
			
			
			//Add context menu columns (actions and labels)
			array_walk($rows, array($this, 'add_actions'), array($composite_id, $type_id));
			//Build a YUI result from the data
			//
			$result_data = array('results' => $rows, 'total_records' => count($rows));	
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