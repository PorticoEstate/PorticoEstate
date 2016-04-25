<?php
	require_once dirname(__FILE__) . '/entryPoint.php';

	function randomReservations( PhpgwContext $c )
	{
		date_default_timezone_set('Europe/Stockholm');

		$types = array('booking', 'event', 'allocation');

		$options = array('season_id' => null, 'building_id');

		$options['season_id'] = isset($_ENV['SEASON_ID']) ? $_ENV['SEASON_ID'] : null;
		$options['building_id'] = isset($_ENV['BUILDING_ID']) ? $_ENV['BUILDING_ID'] : null;

		if (!$options['season_id'])
		{
			throw new InvalidArgumentException('Missing SEASON_ID');
		}

		$season_so = CreateObject('booking.soseason');
		$options['season'] = $season_so->read_single($options['season_id']);

		if (!$options['season'])
		{
			throw new InvalidArgumentException('Invalid SEASON_ID');
		}

		$options['building_id'] = $options['season']['building_id'];

		if (!$options['building_id'])
		{
			throw new InvalidArgumentException('Missing BUILDING_ID');
		}

		$resource_so = CreateObject('booking.soresource');

		$resources = $resource_so->read(array(
			'filters' => array(
				'building_id' => $options['building_id'],
				'active' => 1,
			)
		));

		if (!isset($resources['total_records']) || $resources['total_records'] < 1)
		{
			throw new InvalidArgumentException('Building with BUILDING_ID has no available resources');
		}

		$agegroup_bo = CreateObject('booking.boagegroup');
		$agegroups = $agegroup_bo->fetch_age_groups();
		$options['agegroups'] = $agegroups['results'];

		$activity_bo = CreateObject('booking.boactivity');
		$activities = $activity_bo->fetch_activities();
		$options['activities'] = $activities['results'];

		$groups_so = CreateObject('booking.sogroup');
		$groups = $groups_so->read(array(
			'filters' => array('active' => '1')
		));
		$options['groups'] = $groups['results'];

		$org_so = CreateObject('booking.soorganization');
		$organizations = $org_so->read(array(
			'filters' => array('active' => '1')
		));
		$options['organizations'] = $organizations['results'];

		$audience_bo = CreateObject('booking.boaudience');
		$audience = $audience_bo->fetch_target_audience();
		$options['audience'] = $audience['results'];

		$resources = $resources['results'];
		$resources_length = count($resources);

		//$date_funcs = array('set_future_date', 'set_current_date', 'set_past_date', 'set_past_date');
		$date_funcs = array('set_past_date', 'set_past_date');
		$date_funcs_length = count($date_funcs);

		foreach ($types as $type)
		{
			$initialize_func = 'initialize_' . $type;

			echo 'Now: ' . date('Y-m-d H:i:s') . "\n";

			for ($i = 1; $i <= 10; $i++)
			{
				$data = array();
				$data['active'] = '1';
				$data['completed'] = '0';
				$reservation = CreateObject('booking.bo' . $type);
				$date_func = $date_funcs[rand(0, $date_funcs_length - 1)];
				$data = call_user_func_array($date_func, array($data));
				$data['resources'] = select_resources($resources, rand(1, $resources_length));
				$data['cost'] = rand(30, 200) + (rand(1, 99) / 100);
				$activity = select_activity($options['activities'], rand(0, count($options['activities']) - 1));
				$data['activity_id'] = $activity['id'];
				$data['activity_name'] = $activity['name'];
				$data = call_user_func_array($initialize_func, array($data, $options));
				$reservation->add($data);
			}
		}
	}

	function generate_random_organization_number()
	{
		$rand_nums = array();
		for ($i = 0; $i < 9; $i++)
		{
			$rand_nums[] = rand(1, 9);
		}
		return join($rand_nums, '');
	}

	function generate_random_ssn()
	{
		return str_pad(rand(1, 28), 2, 0, STR_PAD_LEFT) . /* Dag */
			str_pad(rand(1, 12), 2, 0, STR_PAD_LEFT) . /* Måned */
			str_pad(rand(0, 99), 2, 0, STR_PAD_LEFT) . /* År */
			rand(100, 999) . /* Individsiffre + Kön */
			str_pad(rand(0, 99), 2, 0, STR_PAD_LEFT); /* Kontroll */
	}

	function set_random_customer_identifier( &$entity )
	{
		static $customer_identifier_types = array('ssn', 'organization_number');
		static $customer_id;

		$current_type = $customer_identifier_types[rand(0, 1)];
		$entity['customer_identifier_type'] = $current_type;
		$entity['customer_' . $current_type] = $current_id = call_user_func('generate_random_' . $current_type);

		if (count($errors = CreateObject('booking.customer_identifier')->validate($entity)) > 0)
		{
			throw new LogicException(
			sprintf('Unable to create valid random customer %s. Generated %s', $current_type, $current_id)
			);
		}
	}

	function initialize_booking( $data, $options )
	{
		$data['season_id'] = $options['season_id'];
		$agegroups = array();

		foreach ($options['agegroups'] as $agegroup)
		{
			$agegroups[] = array('agegroup_id' => $agegroup['id'], 'female' => (string)rand(1, 3),
				'male' => (string)rand(1, 3));
		}

		$audiences = array();

		foreach ($options['audience'] as $audience)
		{
			$audiences[] = $audience['id'];
		}

		$data['audience'] = $audiences;

		$data['agegroups'] = $agegroups;

		$group = $options['groups'][rand(0, count($options['groups']) - 1)];

		$data['group_id'] = $group['id'];
		return $data;
	}

	function initialize_event( $data, $options )
	{
		$data['description'] = $data['activity_name'] . ': ' . $data['from_'] . ' - ' . $data['to_'];
		$data['contact_name'] = 'John Doe';
		$data['contact_email'] = 'john.doe@domain.com';
		$data['contact_phone'] = '123456789';
		set_random_customer_identifier($data);
		return initialize_booking($data, $options);
	}

	function initialize_allocation( $data, $options )
	{
		$data['season_id'] = $options['season_id'];

		$org = $options['organizations'][rand(0, count($options['organizations']) - 1)];

		$data['organization_id'] = $org['id'];
		return $data;
	}

	function select_activity( &$activities, $position )
	{
		$activity = $activities[$position];
		return $activity;
	}

	function select_resources( &$available_resources, $number )
	{
		$resources = array();
		for ($i = 0; $i < $number; $i++)
		{
			$resources[] = $available_resources[$i]['id'];
		}
		return $resources;
	}

	function set_future_date( $data )
	{
		$start_date = strtotime(sprintf('+%s days +%s hours', rand(1, 40), rand(12, 24)));
		$end_date = strtotime(sprintf('+%s days +%s hours', rand(1, 4), rand(1, 3)), $start_date);
		$data['from_'] = date('Y-m-d H:i', $start_date) . ':00';
		$data['to_'] = date('Y-m-d H:i', $end_date) . ':00';
		echo 'Future date: ' . $data['from_'] . ' - ' . $data['to_'] . "\n";
		return $data;
	}

	function set_past_date( $data )
	{
		$end_date = strtotime(sprintf('-%s days -%s hours', rand(2, 40), rand(1, 12)));
		$start_date = strtotime(sprintf('-%s days -%s hours', rand(1, 4), rand(1, 3)), $end_date);

		$data['from_'] = date('Y-m-d H:i', $start_date) . ':00';
		$data['to_'] = date('Y-m-d H:i', $end_date) . ':00';
		echo 'Past date: ' . $data['from_'] . ' - ' . $data['to_'] . "\n";
		return $data;
	}

	function set_current_date( $data )
	{
		$start_date = strtotime(sprintf('-%s days -%s hours', rand(0, 2), rand(1, 6)));
		$end_date = strtotime(sprintf('+%s days +%s hours', rand(0, 2), rand(1, 6)));

		$data['from_'] = date('Y-m-d H:i', $start_date) . ':00';
		$data['to_'] = date('Y-m-d H:i', $end_date) . ':00';
		echo 'Current date: ' . $data['from_'] . ' - ' . $data['to_'] . "\n";
		return $data;
	}
	PhpgwEntry::phpgw_call('randomReservations');
