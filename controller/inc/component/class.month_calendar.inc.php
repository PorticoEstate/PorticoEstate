<?php

phpgw::import_class('controller.socheck_list');
include_class('controller', 'date_generator', 'inc/component/');
include_class('controller', 'check_list_status_info', 'inc/component/');
include_class('controller', 'check_list_status_manager', 'inc/component/');

/* This class transforms controls with checklists or controls with aggregated number of open cases, 
 *  and puts these values in a calendar array for each control
 */

class month_calendar {

  private $year;
  private $month;
  private $control;
  private $type;
  private $component;
  private $location_code;
  private $calendar_array = array();

  public function __construct($control, $year, $month, $component, $location_code, $type) {
    $this->control = $control;
    $this->year = $year;
    $this->month = $month;
    $this->component = $component;
    $this->location_code = $location_code;
    $this->type = $type;

    $this->init_calendar();
  }

  /* Initializes calendar by setting status for each month in calendar array. 
   * 	- CONTROL_NOT_DONE if month date is in the past 
   * 	- CONTROL_REGISTERED if month date is in the future */

  function init_calendar() {
    $ctr_start_date_ts = $this->control->get_start_date();
    $ctr_end_date_ts = $this->control->get_end_date();
    $period_start_date_ts = $this->get_start_date_month_ts($this->year, $this->month);
    $period_end_date_ts = $this->get_next_start_date_month_ts($this->year, $this->month);
    $repeat_type = $this->control->get_repeat_type();
    $repeat_interval = $this->control->get_repeat_interval();

    $num_days_in_month = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);

    for ($i = 1; $i <= $num_days_in_month; $i++) {
      $this->calendar_array[$i] = null;
    }

    $date_generator = new date_generator($ctr_start_date_ts, $ctr_end_date_ts, $period_start_date_ts, $period_end_date_ts, $repeat_type, $repeat_interval);
    $dates_array = $date_generator->get_dates();

    // Set status for control on each date to NOT DONE or REGISTERED   
    foreach ($dates_array as $date_ts) {
      $check_list = new controller_check_list();
      $check_list->set_deadline($date_ts);
      $check_list->set_control_id($this->control->get_id());

      if ($this->type == "component") {
        $check_list->set_component_id($this->component->get_id());
        $check_list->set_location_id($this->component->get_location_id());
        $check_list_status_manager = new check_list_status_manager($check_list, "component");
      } else {
        $check_list->set_location_code($this->location_code);
        $check_list_status_manager = new check_list_status_manager($check_list, "location");
      }

      $check_list_status_info = $check_list_status_manager->get_status_for_check_list();

      $this->calendar_array[date("j", $date_ts)]["status"] = $check_list_status_info->get_status();
      $this->calendar_array[date("j", $date_ts)]["info"] = $check_list_status_info->serialize();
    }
  }

  public function build_calendar($check_lists_array) {
    foreach ($check_lists_array as $check_list) {
      $check_list_status_manager = new check_list_status_manager($check_list);
      $check_list_status_info = $check_list_status_manager->get_status_for_check_list();

      $this->calendar_array[date("j", $check_list->get_deadline())]["status"] = $check_list_status_info->get_status();
      $this->calendar_array[date("j", $check_list->get_deadline())]["info"] = $check_list_status_info->serialize();
    }

    return $this->calendar_array;
  }

  public static function get_heading_array($year, $month) {
    $num_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $heading_array = array();

    for ($i = 1; $i <= $num_days_in_month; $i++) {
      $heading_array[$i] = "$i";
    }

    return $heading_array;
  }

  public static function get_start_date_month_ts($year, $month) {
    return strtotime("$month/01/$year");
  }

  public static function get_next_start_date_month_ts($year, $month) {
    if (($month + 1) > 12) {
      $to_month = 1;
      $to_year = $year + 1;
    } else {
      $to_month = $month + 1;
      $to_year = $year;
    }

    return strtotime("$to_month/01/$to_year");
  }

}