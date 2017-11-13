<?php
	phpgw::import_class('controller.socheck_list');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'check_list_status_info', 'inc/component/');
	include_class('controller', 'check_list_status_manager', 'inc/component/');
	include_class('controller', 'check_list', 'inc/model/');

	class year_calendar
	{

		private $year;
		private $control;
		private $type;
		private $component;
		private $location_code;
		private $calendar_array = array();
		private $control_relation = array();

		public function __construct( $control, $year, $component, $location_code, $type, $control_relation = array() )
		{
			$this->year = $year;
			$this->control = $control;
			$this->component = $component;
			$this->location_code = $location_code;
			$this->type = $type;
			$this->control_relation = $control_relation;

			$this->init_calendar();
		}
		/* Initializes calendar by setting status for each month in calendar array.
		 * 	- CONTROL_NOT_DONE if month date is in the past 
		 * 	- CONTROL_REGISTERED if month date is in the future */

		function init_calendar()
		{
			// Sets null values for twelve months in calendar array
			for ($i = 1; $i <= 12; $i++)
			{
				$this->calendar_array[$i] = null;
			}

			if ($this->control_relation && !$this->control_relation['serie_enabled'])
			{
				return;
			}

			$ctr_start_date_ts = $this->control->get_start_date();
			$ctr_end_date_ts = $this->control->get_end_date();
			$period_start_date_ts = $this->get_start_date_year_ts($this->year);
			$period_end_date_ts = $this->get_start_date_year_ts($this->year + 1);
			$repeat_type = $this->control->get_repeat_type();
			$repeat_interval = $this->control->get_repeat_interval();

			// Generates dates for time period with specified interval
			$date_generator = new date_generator($ctr_start_date_ts, $ctr_end_date_ts, $period_start_date_ts, $period_end_date_ts, $repeat_type, $repeat_interval);
			$dates_array = $date_generator->get_dates();

			// Set status for control on each date to NOT DONE or REGISTERED
			foreach ($dates_array as $date_ts)
			{
				$check_list = new controller_check_list();
				$check_list->set_deadline($date_ts);
				$check_list->set_control_id($this->control->get_id());
				$check_list->set_assigned_to($this->control_relation['assigned_to']);

				if ($this->type == "component")
				{
					$check_list->set_component_id($this->component->get_id());
					$check_list->set_location_id($this->component->get_location_id());
					$check_list_status_manager = new check_list_status_manager($check_list, "component");
				}
				else
				{
					$check_list->set_location_code($this->location_code);
					$check_list_status_manager = new check_list_status_manager($check_list, "location");
				}

				$check_list_status_info = $check_list_status_manager->get_status_for_check_list();

				$month_nr = date("n", $date_ts);

				$this->calendar_array[$month_nr]["status"] = $check_list_status_info->get_status();
				$this->calendar_array[$month_nr]["info"] = $check_list_status_info->serialize();
				if (!$this->calendar_array[$month_nr]["info"]['serie_id'])
				{
					$this->calendar_array[$month_nr]["info"]['serie_id'] = $this->control_relation['serie_id'];
				}
				$this->calendar_array[$month_nr]["info"]['service_time'] = $this->control_relation['service_time'];
				$this->calendar_array[$month_nr]["info"]['controle_time'] = $this->control_relation['controle_time'];
			}
		}

		public function build_calendar( $check_lists_array, $ctrl_status = NULL )
		{
			foreach ($check_lists_array as $check_list)
			{
				$has_planned_date = false;
				$has_completed_date = false;
				if (isset($this->control_relation['serie_id']) && $check_list->get_serie_id() != $this->control_relation['serie_id'])
				{
					continue;
				}

				$check_list_status_manager = new check_list_status_manager($check_list);
				$check_list_status_info = $check_list_status_manager->get_status_for_check_list();

				$month_nr = date("n", $check_list_status_info->get_deadline_date_ts());
				if($check_list_status_info->get_planned_date_ts() && $check_list_status_info->get_planned_date_ts() > 0)
				{
					$month_nr_planned = date("n", $check_list_status_info->get_planned_date_ts());
					$has_planned_date = true;
				}
				
				if($check_list_status_info->get_completed_date_ts() && $check_list_status_info->get_completed_date_ts() > 0)
				{
					$has_completed_date = true;
					$month_nr_completed = date("n", $check_list_status_info->get_completed_date_ts());
				}

				$repeat_type = $check_list->get_repeat_type();
				//		if( !isset($this->calendar_array[ $month_nr ]) || $repeat_type > $this->calendar_array[ $month_nr ]['repeat_type'])
				if($has_completed_date)
				{
					$this->calendar_array[$month_nr_completed]['repeat_type'] = $repeat_type;
					$this->calendar_array[$month_nr_completed]["status"] = $check_list_status_info->get_status();
					$this->calendar_array[$month_nr_completed]["info"] = $check_list_status_info->serialize();
				}
				else if($has_planned_date)
				{
					$this->calendar_array[$month_nr_planned]['repeat_type'] = $repeat_type;
					$this->calendar_array[$month_nr_planned]["status"] = $check_list_status_info->get_status();
					$this->calendar_array[$month_nr_planned]["info"] = $check_list_status_info->serialize();
				}
				else
				{
					$this->calendar_array[$month_nr]['repeat_type'] = $repeat_type;
					$this->calendar_array[$month_nr]["status"] = $check_list_status_info->get_status();
					$this->calendar_array[$month_nr]["info"] = $check_list_status_info->serialize();
				}
			}
			
			/*Insert code to remove controls with changed due-date from array*/
			$m_cnt = 0;
			$not_done_due_date;
			$new_calendar_array = array();
			$new_calendar_array2 = array();
			$found = false;
			$moved_control_dates = array();
			$moved_control_month = array();
			foreach ($this->calendar_array as $cal)
			{
				if(is_array($cal))
				{
					if(isset($cal['info']['original_deadline_date_ts']) && $cal['info']['original_deadline_date_ts'] > 0)
					{
						$found = true;
//						$moved_control_dates[] = $cal['info']['original_deadline_date_ts'];
						$moved_control_month[] = date('n', $cal['info']['original_deadline_date_ts']);
					}
				}
			}
			if($found)
			{
				foreach ($this->calendar_array as $cal2)
				{
					$m_cnt++;
					if(is_array($cal2))
					{
						if($cal2['info']['status'] == 'CONTROL_NOT_DONE' || $cal2['info']['status'] == 'CONTROL_REGISTERED')
						{
//							if(in_array($cal2['info']['deadline_date_ts'], $moved_control_dates))
							if(in_array($m_cnt, $moved_control_month))
							{
								$new_calendar_array[$m_cnt] = NULL;
							}
							else
							{
								$new_calendar_array[$m_cnt] = $cal2;
							}
						}
						else
						{
							$new_calendar_array[$m_cnt] = $cal2;
						}
					}
					else
					{
						$new_calendar_array[$m_cnt] = NULL;
					}
				}
				$this->calendar_array = $new_calendar_array;
			}
			$m_cnt2 = 0;
			if(isset($ctrl_status))
			{
				if ($ctrl_status == "ALLE")
				{
					//do nothing
				}
				else
				{
					foreach ($this->calendar_array as $cal3)
					{
						$m_cnt2++;
						if(is_array($cal3))
						{
							if($ctrl_status == 'CONTROL_NOT_DONE')
							{
								if($cal3['info']['status'] == $ctrl_status || $cal3['info']['status'] == 'CONTROL_NOT_DONE_WITH_PLANNED_DATE')
								{
									$new_calendar_array2[$m_cnt2] = $cal3;
								}
								else
								{
									$new_calendar_array2[$m_cnt2] = NULL;
								}
							}
							else
							{
								if($cal3['info']['status'] == $ctrl_status)
								{
									$new_calendar_array2[$m_cnt2] = $cal3;
								}
								else
								{
									$new_calendar_array2[$m_cnt2] = NULL;
								}
							}
						}
						else
						{
							$new_calendar_array2[$m_cnt2] = NULL;
						}
					}
					$this->calendar_array = $new_calendar_array2;
				}
			}
			return $this->calendar_array;
		}

		public function build_agg_month_calendar( $agg_open_cases_pr_month_array )
		{

			foreach ($agg_open_cases_pr_month_array as $status_agg_month_info)
			{
				$status = "CONTROLS_DONE_WITH_ERRORS";

				$this->calendar_array[$status_agg_month_info->get_month_nr()]["status"] = $status;
				$this->calendar_array[$status_agg_month_info->get_month_nr()]["info"]["agg_open_errors"] = $status_agg_month_info->get_agg_open_cases();
			}

			return $this->calendar_array;
		}

		public static function get_heading_array()
		{

			$heading_array = array();

			for ($i = 1; $i <= 12; $i++)
			{
				$heading_array[$i] = "$i";
			}

			return $heading_array;
		}

		public static function get_start_date_year_ts( $year )
		{
			$start_date_year_ts = strtotime("01/01/$year");

			return $start_date_year_ts;
		}
	}