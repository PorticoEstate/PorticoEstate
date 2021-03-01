<?php
	phpgw::import_class('booking.bocommon');
	phpgw::import_class('phpgwapi.datetime');

	class booking_bocompleted_reservation_export extends booking_bocommon
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.socompleted_reservation_export');
		}

		protected function build_default_read_params()
		{
			$params = parent::build_default_read_params();

			$where_clauses = array();

			//build_default_read_params will not automatically build a filter for the to_ field
			//because it cannot match the name 'filter_to' to an existing field once the prefix 
			//'filter' is removed nor do we want it to, so we build that filter manually here:
			if ($filter_to = phpgw::get_var('filter_to', 'string', 'REQUEST', null))
			{
				$to_date = date('Y-m-d', phpgwapi_datetime::date_to_timestamp($filter_to));
				$where_clauses[] = "%%table%%" . sprintf(".to_ <= '%s 23:59:59'", $GLOBALS['phpgw']->db->db_addslashes($to_date));
			}
			
			/**
			 * filter on already processed
			 */
			if(phpgw::get_var('generate_files', 'bool', 'POST') )
			{
				$where_clauses[] = '%%table%%.id IN (' .
				' SELECT bb_completed_reservation_export.id FROM bb_completed_reservation_export'.
				' JOIN bb_completed_reservation_export_configuration '.
				' ON bb_completed_reservation_export_configuration.export_id = bb_completed_reservation_export.id'.
				' AND export_file_id IS NULL)';
			}

			if (count($where_clauses) > 0)
			{
				$params['filters']['where'] = $where_clauses;
			}

			return $params;
		}
	}