<?php
	phpgw::import_class('booking.bocommon');
	
	require_once "schedule.php";
	
	class booking_boseason extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soseason');
			$this->so_boundary = new booking_soseason_boundary();
			$this->so_resource = CreateObject('booking.soresource');
			$this->so_wtemplate_alloc = new booking_sowtemplate_alloc();
		}

		function validate_boundary($boundary)
		{
			return $this->so_boundary->validate($boundary);
		}

		function add_boundary($boundary)
		{
			return $this->so_boundary->add($boundary);
		}
		
		function get_boundaries($season_id)
		{
			return $this->so_boundary->read(array('filters'=>array('season_id'=>$season_id), 'sort'=>'wday,from_'));
		}

		function add_wtemplate_alloc($alloc)
		{
			return $this->so_wtemplate_alloc->add($alloc);
		}

		function update_wtemplate_alloc($alloc)
		{
			return $this->so_wtemplate_alloc->update($alloc);
		}

		function validate_wtemplate_alloc($alloc)
		{
			return $this->so_wtemplate_alloc->validate($alloc);
		}

		/**
		 * Return a season's template schedule in a datatable
		 * compatible format
		 * 
		 * @param int	$season_id_id
		 *
		 * @return array containing values from $array for the keys in $keys.
		 */
		function wtemplate_schedule($season_id)
		{
			$season = $this->read_single($season_id);
			$allocations = $this->so_wtemplate_alloc->read(array('filters'=>array('season_id'=>$season_id), 'sort'=>'wday,from_'));
			$allocations = $allocations['results'];
			foreach($allocations as &$alloc)
			{
				$alloc['name'] = $alloc['organization_name'];
				$alloc['from_'] = substr($alloc['from_'], 0, 5);
				$alloc['to_'] = substr($alloc['to_'], 0, 5);
			}
			$resources = $this->so_resource->read(array('filters' => array('id' => $season['resources'])));
			$resources = $resources['results'];
			//$bookings = $this->_split_multi_day_bookings($bookings, $from, $to);
			$results = build_schedule_table($allocations, $resources);
			return array('total_records'=>count($results), 'results'=>$results);
		}

		function wtemplate_alloc_read_single($alloc_id)
		{
			return $this->so_wtemplate_alloc->read_single($alloc_id);
		}

	}
