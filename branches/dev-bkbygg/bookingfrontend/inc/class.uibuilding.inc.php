<?php
	phpgw::import_class('booking.uibuilding');

	class bookingfrontend_uibuilding extends booking_uibuilding
	{

		public $public_functions = array(
			'index' => true,
			'schedule' => true,
			'information_screen' => true,
			'extraschedule' => true,
			'show' => true,
			'toggle_show_inactive' => true,
			'find_buildings_used_by' => true,
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->booking_bo = CreateObject('booking.bobooking');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->module = "bookingfrontend";
		}

		public function information_screen()
		{
			$today = new DateTime(phpgw::get_var('date', 'string', 'GET'), new DateTimeZone('Europe/Oslo'));
			$date = $today;
			$currentday = $today;//Sigurd: Needed?

			$building_id = phpgw::get_var('id', 'int', 'GET');
			$building = $this->bo->read_single($building_id);
			$start = phpgw::get_var('start', 'int', 'GET');
			$end = phpgw::get_var('end', 'int', 'GET');
			$res = phpgw::get_var('res', 'string', 'GET');
			$color = phpgw::get_var('color', 'string', 'GET');
			$fontsize = phpgw::get_var('fontsize', 'int', 'GET');
			$weekend = phpgw::get_var('weekend', 'int', 'GET');


			if ($start)
			{
				$timestart = $start;
			}
			else
			{
				$timestart = 8;
			}

			if ($end)
			{
				$timeend = $end;
			}
			else
			{
				$timeend = 22;
			}

			$timediff = $timeend - $timestart;
			$cellwidth = 88 / ($timediff * 2);

			$days = array(
				"Mon" => "Mandag",
				"Tue" => "Tirsdag",
				"Wed" => "Onsdag",
				"Thu" => "Torsdag",
				"Fri" => "Fredag",
				"Sat" => "Lørdag",
				"Sun" => "Søndag"
			);

			$bookings = $this->booking_bo->building_infoscreen_schedule($building_id, $date, $res);
			$from = clone $date;
			$from->setTime(0, 0, 0);
			// Make sure $from is a monday
			if ($from->format('w') != 1)
			{
				$from->modify('last monday');
				if ($weekend == 1)
				{
					$from->modify('next Saturday');
				}
				if ($weekend == 3)
				{
					$currentday = clone $date;
					$currentday->setTime(0, 0, 0);
					$from = $currentday;
				}
			}


			$from = $from->format('d.m.Y');


			$list1 = array(
				'Mon' => array(),
				'Tue' => array(),
				'Wed' => array(),
				'Thu' => array(),
				'Fri' => array(),
			);
			$list2 = array(
				'Sat' => array(),
				'Sun' => array()
			);
			$list3 = array(
				'Mon' => array(),
				'Tue' => array(),
				'Wed' => array(),
				'Thu' => array(),
				'Fri' => array(),
				'Sat' => array(),
				'Sun' => array()
			);
			if ($weekend == 1)
			{
				$list = $list2;
			}
			elseif ($weekend == 2)
			{
				$list = $list3;
			}
			elseif ($weekend == 3)
			{
				$day = $currentday->format('D');
				$list = array($day => array());
			}
			else
			{
				$list = $list1;
			}

			foreach ($list as $key => &$item)
			{
				$item = $bookings['results'][$key];
			}

			$time = $timestart;
			$html = '<html><head><title>Kalender for ' . $building['name'] . '</title>';
			$html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
			$html .= '<meta name="author" content="Bergen Kommune">';
			$html .= '<style>';
			$html .= 'body { font-size: 12px; padding: 0px; border-spacing: 0px;} ';
			if ($fontsize != '')
			{
				$html .= 'table { font-family: Tahoma, Verdana, Helvetica; width: 100%; height: 100%; margin: 0px; font-size: ' . $fontsize . 'px; border-collapse: collapse;} ';
			}
			else
			{
				$html .= 'table { font-family: Tahoma, Verdana,Helvetica; width: 100%; height: 100%; margin: 0px; font-size: 12px; border-collapse: collapse;} ';
			}
			$html .= 'th { text-align: left; padding: 2px 8px; border: 1px solid black;} ';
			$html .= 'td { font-weight: bold; text-align: left; padding: 4px 8px; border: 1px solid black;} ';
			$html .= 'tr.header { background-color: #333; color: white; } ';
			if ($color != '')
			{
				$html .= 'td.data { background-color: #' . $color . '; } ';
			}
			else
			{
				$html .= 'td.data { background-color: #ccffff; } ';
			}
			$html .= '</style>';
			$html .= '</head><body style="color: black; margin: 8px; font-weight: bold;">';
			$html .= '<table class="calender">';
			$html .= '<thead>';
			$html .= '<tr>';
			$html .= '<th colspan="2" style="text-align: left; width: 12%;">Bane</th>';
			while ($time < $timeend)
			{
				$html .= '<th colspan="1" style="width: ' . $cellwidth . '%; text-align: left;">' . str_pad($time, 2, '0', STR_PAD_LEFT) . ':00</th>';
				$html .= '<th colspan="1" style="width: ' . $cellwidth . '%; text-align: left;">' . str_pad($time, 2, '0', STR_PAD_LEFT) . ':30</th>';
				$time += 1;
			}
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
			$first = '';
			$len = (($timeend - $timestart) * 2) + 2;

			foreach ($list as $day => $resources)
			{

				if ($first != $day)
				{
					$first = $day;
					$html .= '<tr class="header">';
					$html .= '<td colspan="' . $len . '" width="12%">';
					$html .= $days[$day];
					$html .= " ";
					$html .= $from;
					$html .= '</td>';
					$html .= '</tr>';

					$from = date('d.m.Y', strtotime($from . ' 00:00:01 +1 day'));
				}
				foreach ($resources as $res => $booking)
				{
					$html .= '<tr>';
					$html .= '<td colspan="2">';
					$html .= $res;
					$html .= '</td>';
					$last = -1;
					foreach ($booking as $date => $value)
					{
						$time2 = $timestart;

						$bftime = explode(':', substr($value['from_'], -8));
						$bttime = explode(':', substr($value['to_'], -8));

						if ($bftime[1] == 30)
						{
							$bftime = $bftime[0] + 0.5;
						}
						else
						{
							$bftime = intval($bftime[0]);
						}
						if ($bttime[1] == 30)
						{
							$bttime = $bttime[0] + 0.5;
						}
						else
						{
							$bttime = intval($bttime[0]);
						}

						while ($time2 < $timeend)
						{
							if ($bftime == $time2 && $time2 < $timeend)
							{
								$last = $bttime;
								$colspan = $value['colspan'];
								if ($bttime > $timeend)
								{
									$colspan = $value['colspan'] - ($bttime - $timeend);
								}
								$testlen = 12 * $colspan;

								$html .= '<td colspan="' . $colspan . '" class="data" style="">';
								if (strlen($value['name']) > $testlen)
								{
									$html .= $value['shortname'] . " ";
								}
								else
								{
									$html .= $value['name'] . " ";
								}
								$html .= '</td>';
							}
							elseif ($last === -1 && $bftime < $timestart && $bttime > $timestart)
							{
								$last = $bttime;
								$colspan = ($bttime - $timestart) * 2;
								$html .= '<td colspan="' . $colspan . '" class="data" style="">';
								$testlen = 12 * $colspan;
								if (strlen($value['name']) > $testlen)
								{
									$html .= $value['shortname'] . " ";
								}
								else
								{
									$html .= $value['name'] . " ";
								}
								$html .= '</td>';
							}
							elseif ($last === -1 && $bftime != $timestart && $bftime < $timeend && $bftime > $timestart)
							{
								$colspan = ($bftime - $timestart) * 2;

								$html .= '<td colspan="' . $colspan . '">';
								$html .= " ";
								$html .= '</td>';
								$last = $bttime;
							}
							elseif ($last != -1 && $bftime != $last && $time2 > $last && $last < $bftime && $bftime < $timeend)
							{
								$colspan = ($bftime - $last) * 2;
								$html .= '<td colspan="' . $colspan . '">';
								$html .= " ";
								$html .= '</td>';
								$last = $bttime;
							}

							if ($time2 >= $timeend)
							{
								$last = $timestart - 1;
							}
							$time2 += 0.5;
						}
					}
					$html .= '</tr>';
				}
			}
			$html .= '</tbody>';
			$html .= '</table>';
			$html .= '</body></html>';

			header('Content-type: text/html');
			echo $html;
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		public function schedule()
		{
			$backend = phpgw::get_var('backend', 'bool', 'GET');
			$building = $this->bo->get_schedule(phpgw::get_var('id', 'int', 'GET'), 'bookingfrontend.uibuilding');
			if ($building['deactivate_application'] == 0)
			{
				$building['application_link'] = self::link(array(
						'menuaction' => 'bookingfrontend.uiapplication.add',
						'building_id' => $building['id'],
						'building_name' => $building['name'],
				));
			}
			else
			{
				$building['application_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
						'id' => $building['id']));
			}

			$building['endOfSeason'] = $this->bo->so->get_endOfSeason($building['id']) . " 23:59:59";
			if (strlen($building['endOfSeason']) < 18)
			{
				$building['endOfSeason'] = false;
			}
			$building['datasource_url'] = self::link(array(
					'menuaction' => 'bookingfrontend.uibooking.building_schedule',
					'building_id' => $building['id'],
					'phpgw_return_as' => 'json',
			));

			// the schedule can also be used from backend
			// if so we want to change default date shown in the calendar
			if ($backend)
			{
				$building['date'] = phpgw::get_var('date', 'string', 'GET');
			}

			self::add_javascript('bookingfrontend', 'base', 'schedule.js');
			phpgwapi_jquery::load_widget("datepicker");

			$building['picker_img'] = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');

			self::render_template_xsl('building_schedule', array('building' => $building,
				'backend' => $backend));
		}

		public function extraschedule()
		{
			$backend = phpgw::get_var('backend', 'bool', 'GET');
			$building = $this->bo->get_schedule(phpgw::get_var('id', 'int', 'GET'), 'bookingfrontend.uibuilding');
			$building['application_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.extraschedule',
					'id' => $building['id']));
			$building['datasource_url'] = self::link(array(
					'menuaction' => 'bookingfrontend.uibooking.building_extraschedule',
					'building_id' => $building['id'],
					'phpgw_return_as' => 'json',
			));

			// the schedule can also be used from backend
			// if so we want to change default date shown in the calendar
			if ($backend)
			{
				$building['date'] = phpgw::get_var('date', 'string', 'GET');
			}
			$building['deactivate_application'] = 1;
			self::add_javascript('bookingfrontend', 'base', 'schedule.js');
			phpgwapi_jquery::load_widget("datepicker");
			$building['picker_img'] = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');
			self::render_template_xsl('building_schedule', array('building' => $building,
				'backend' => $backend));
		}

		public function show()
		{
			$this->check_active('booking.uibuilding.show');
			$building = $this->bo->read_single(phpgw::get_var('id', 'int', 'GET'));
			$building['schedule_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
					'id' => $building['id']));
			$building['extra_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.extraschedule',
					'id' => $building['id']));
			$building['message_link'] = self::link(array('menuaction' => 'bookingfrontend.uisystem_message.edit',
					'building_id' => $building['id'], 'building_name' => $building['name']));
			$building['start'] = self::link(array('menuaction' => 'bookingfrontend.uisearch.index',
					'type' => "building"));
			if (trim($building['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($building['homepage'])))
			{
				$building['homepage'] = 'http://' . $building['homepage'];
			}

			self::render_template_xsl('building', array("building" => $building));
		}
	}