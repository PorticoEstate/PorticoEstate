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
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			// TODO: access control
			$type_id = phpgw::get_var('type_id');
			//$composite_id	= phpgw::get_var('composite_id');
			$search_type = phpgw::get_var('search_option');
			$part_of_town_id = phpgw::get_var('part_of_town_id', 'int');

			// YUI variables for paging and sorting
			$start_index = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$search_for = $search['value'];
			$num_of_objects = (phpgw::get_var('length', 'int') <= 0) ? $user_rows_per_page : phpgw::get_var('length', 'int');
			$sort_field = ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'location_code';
			$sort = $order[0]['dir'];

			$property_bolocation = new property_bolocation();

			if ($search_type == 'gab')
			{
				$query = explode('/', $search_for);
				//GAB search
				$property_sogab = new property_sogab();
				$gabinfo = $property_sogab->read(array(
					'gaards_nr' => empty($query[0]) ? '' : $query[0],
					'bruksnr' => empty($query[1]) ? '' : $query[1],
					'feste_nr' => empty($query[2]) ? '' : $query[2],
					'seksjons_nr' => empty($query[3]) ? '' : $query[3],
					'allrows' => true,
					'part_of_town_id' => $part_of_town_id,
					));

				$rows_total = count($gabinfo);
				$gab_list = array_slice($gabinfo, $start_index, $num_of_objects);
				foreach ($gab_list as $gabelement)
				{
					$row = $property_bolocation->read_single($gabelement['location_code']);
					$row['gab'] = rental_uicommon::get_nicely_formatted_gab_id($gabelement['gab_id']);
					$rows[] = $row;
				}
			}
			else
			{
				if (!isset($type_id) || $type_id < 1)
				{
					$type_id = 2;
				}

				$params = array(
					'start' => $start_index,
					'query' => $search_for,
					'results' => $num_of_objects,
					'order' => $sort_field,
					'sort' => $sort,
					'allrows' => phpgw::get_var('length', 'int') == -1,
					'type_id' => $type_id,
					'part_of_town_id' => $part_of_town_id,
				);
				$rows = $property_bolocation->read($params);
				$rows_total = $property_bolocation->total_records;
			}

			$result_data = array('results' => $rows);
			$result_data['total_records'] = $rows_total;
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
		/* public function add_actions(&$value, $key, $params)
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

		  } */
	}