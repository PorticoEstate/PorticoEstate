<?php

phpgw::import_class('booking.uicommon');
phpgw::import_class('phpgwapi.send');

class booking_uireports extends booking_uicommon
{
	public $public_functions = array(
			'index'                 =>      true,
			'participants'          =>      true,
		);

	public function __construct()
	{
		parent::__construct();

		$this->building_bo = CreateObject('booking.bobuilding');
		self::set_active_menu('booking::reportcenter');
	}

	public function index()
	{

		$reports[] = array('name' => lang('Participants Per Age Group Per Month'), 'url' => self::link(array('menuaction' => 'booking.uireports.participants')));

		self::render_template('report_index',
		array('reports' => $reports));

	}

	public function participants()
	{
		$errors = array();
		$buildings = $this->building_bo->read();
		$to = '2009-01-01';
		$from = '2009-01-01';

		if ($_SERVER['REQUEST_METHOD'] == 'POST') 
		{

			$to = phpgw::get_var('to', 'POST'); 
			$from = phpgw::get_var('from', 'POST');

			$output_type = phpgw::get_var('otype', 'POST');
			$building_list = phpgw::get_var('building', 'POST');

			if (!count($building_list)) 
			{
				$errors['incomplete form'] = lang('No buildings selected');
			}

			if (!count($errors)) 
			{ 
				$jasper_parameters = sprintf("\"BK_DATE_FROM|%s;BK_DATE_TO|%s;BK_BUILDINGS|%s\"",
					$from,
					$to,
					implode(",", $building_list));
				// DEBUG
				//print_r($jasper_parameters);
				//exit(0);

				$jasper_wrapper 	= CreateObject('phpgwapi.jasper_wrapper');
				$report_source		= PHPGW_SERVER_ROOT.'/booking/jasper/templates/participants.jrxml';
				$jasper_info		= $jasper_wrapper->create_jasper_info($report_source);

				$jasper_wrapper->jasper_info = $jasper_info;
				$jasper_wrapper->execute($jasper_parameters, $output_type, $errors);     
				if(is_file($jasper_info['config']))
				{
					unlink($jasper_info['config']);
				}
			}
		} 
		else 
		{
			$to = date("Y-m-d", time());
			$from = date("Y-m-d", time());
		}

		$this->flash_form_errors($errors);
		self::render_template('report_participants', 
		array('from' => $from, 'to' => $to, 'buildings' => $buildings['results']));
	}
}
