<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.account_helper');
	phpgw::import_class('booking.unauthorized_exception');

	class booking_uiasync_settings extends booking_uicommon
	{	
		public $public_functions = array
		(
			'index'	=>	true,
		);
		
		protected $fields = array('booking_async_task_update_reservation_state_enabled', 'booking_async_task_send_reminder_enabled');
		
		public function __construct()
		{
			parent::__construct();
			
			self::process_booking_unauthorized_exceptions();
			$this->bo = CreateObject('booking.boasync_settings');
			
			self::set_active_menu('booking::settings::async_settings');
		}
		
		public function index() {
			$settings = $this->bo->read();
			
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$settings['booking_async_task_update_reservation_state_enabled'] = phpgw::get_var('booking_async_task_update_reservation_state_enabled', 'bool', 'POST');
				$settings['booking_async_task_send_reminder_enabled'] = phpgw::get_var('booking_async_task_send_reminder_enabled', 'bool', 'POST');
				$this->bo->update($settings);
			}
			
			self::render_template('async_settings_form', array('settings' => $settings));
		} 
		
	}
