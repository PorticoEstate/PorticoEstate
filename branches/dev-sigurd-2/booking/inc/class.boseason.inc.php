<?php
	phpgw::import_class('booking.bocommon');
	
	require_once "schedule.php";
	
	class booking_boseason extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soseason');
			$this->bo_allocation = CreateObject('booking.boallocation');
			$this->so_boundary = new booking_soseason_boundary();
			$this->so_resource = CreateObject('booking.soresource');
			$this->so_wtemplate_alloc = new booking_sowtemplate_alloc();
		}

		function generate_allocation($season_id, $date, $to, $write=false)
		{
			$valid = array();
			$invalid = array();
			do
			{
				$wday = $date->format('N');
				$tallocations = $this->so_wtemplate_alloc->read(array('filters'=>array('season_id'=>$season_id, 'wday'=>$wday), 'sort'=>'from_'));
				foreach($tallocations['results'] as $talloc)
				{
				
					$allocation = extract_values($talloc, array('season_id', 'organization_id', 'cost', 'resources', 'organization_name'));
					$allocation['from_'] = $date->format("Y-m-d").' '.$talloc['from_'];
					$allocation['to_'] = $date->format("Y-m-d").' '.$talloc['to_'];
					if(!$this->bo_allocation->validate($allocation))
						$valid[] = $allocation;
					else
						$invalid[] = $allocation;
				}
				if($date->format('Y-m-d') == $to->format('Y-m-d'))
				{
					if($write)
					{
						$this->so->db->transaction_begin();
						foreach($valid as $alloc)
						{
							$this->bo_allocation->add($alloc);
						}
						$this->so->db->transaction_commit();
					}
					return array('valid' => $valid, 'invalid'=>$invalid);
				}
				$date->modify('+1 day');
			}
			while(true);

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
