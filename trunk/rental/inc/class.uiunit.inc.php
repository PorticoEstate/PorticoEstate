<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.sounit');

	class rental_uiunit extends rental_uicommon
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
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$start_index = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_of_objects = (phpgw::get_var('length', 'int') <= 0) ? 10 : phpgw::get_var('length', 'int');
			$sort_field = ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'id';
			$sort_ascending = ($order[0]['dir'] == 'desc') ? false : true;
			$search_for = $search['value'];
			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', 'all');

			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			// TODO: access control
			$composite_id = phpgw::get_var('composite_id');
			$filters = array('composite_id' => $composite_id);

			$result_objects = rental_sounit::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$result_count = rental_sounit::get_instance()->get_count($search_for, $search_type, $filters);

			$editable = phpgw::get_var('editable') == 'true' ? true : false;

			//Serialize the documents found
			$rows = array();
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
					if ($result->has_permission(PHPGW_ACL_READ)) // check for read permission
					{
						$rows[] = $result->serialize();
					}
				}
			}

			//Add context menu columns (actions and labels)
			//array_walk($rows, array($this, 'add_actions'), array($composite_id, $editable));
			//Build a YUI result from the data
			//
			$result_data = array('results' => $rows);
			$result_data['total_records'] = count($rows);
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * Add data for context menu
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [type of query, editable]
		 */
		public function add_actions( &$value, $key, $params )
		{
			unset($value['query_location']);

			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			$composite_id = $params[0];
			$editable = $params[1];

			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uilocation.view',
					'location_code' => $value['location_code'])));
			$value['labels'][] = lang('show');

			if ($editable == true)
			{
				$value['ajax'][] = true;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.remove_unit',
						'id' => $value['id'])));
				$value['labels'][] = lang('remove_location');
			}
		}
	}