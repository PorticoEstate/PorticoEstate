<?php
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class helpdesk_uiasync_settings extends phpgwapi_uicommon_jquery
	{

		protected $asyncservice, $acl_edit;

		public $public_functions = array
			(
			'index'	 => true,
			'query'	 => true
		);

		public function __construct()
		{
			parent::__construct();

			if (!isset($GLOBALS['phpgw']->asyncservice) || !is_object($GLOBALS['phpgw']->asyncservice))
			{
				$GLOBALS['phpgw']->asyncservice = CreateObject('phpgwapi.asyncservice');
			}

			$this->asyncservice	 = $GLOBALS['phpgw']->asyncservice;
			$this->acl_edit		 = $GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'helpdesk');

			self::set_active_menu('admin::helpdesk::async_settings');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('helpdesk') . "::" . lang('Asynchronous Tasks');
		}

		public function index()
		{
			if (!$this->acl_edit)
			{
				phpgw::no_access();
			}
			$settings = $this->read();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$settings['helpdesk_async_task_anonyminizer_enabled'] = phpgw::get_var('helpdesk_async_task_anonyminizer_enabled', 'bool', 'POST');
				$this->update($settings);
			}

			$tabs			 = array();
			$tabs['generic'] = array('label' => lang('Asynchronous Tasks'), 'link' => '#async_settings');
			$active_tab		 = 'generic';


			$settings['tabs']		 = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$settings['form_action'] = '';

			self::render_template_xsl('async_settings_form', array('settings' => $settings));
		}

		public static function getAvailableTasks()
		{
			return array(
				'helpdesk_async_task_anonyminizer' => 'helpdesk.hook_helper.anonyminizer'
			);
		}

		function read()
		{
			$settings = array();

			foreach (self::getAvailableTasks() as $task_id => $method)
			{
				$settings["{$task_id}_enabled"] = $this->is_enabled($task_id);
			}

			return $settings;
		}

		private function update( $settings )
		{
			foreach (self::getAvailableTasks()  as $task_id => $method)
			{
				$this->disable($task_id);

				if ($settings["{$task_id}_enabled"] === true)
				{
					$this->enable($task_id);
				}
			}
		}

		private function get_default_times()
		{
			return array(
//				'min'	 => '*',
//				'hour'	 => '*',
				'day' => '*/1',
				'month'	 => '*',
				'year'	 => '*'
				);
		}

		private function is_enabled($task_id)
		{
			return is_array($this->asyncservice->read($task_id));
		}

		private function disable($task_id)
		{
			$this->asyncservice->cancel_timer($task_id);
		}

		private function enable($task_id, $times = null )
		{
			if ($times === null)
			{
				$times = $this->get_default_times();
			}
			$task_list = self::getAvailableTasks();
			$this->asyncservice->set_timer(	$times, $task_id, $task_list[$task_id], null);
		}

		public function query()
		{

		}
	}