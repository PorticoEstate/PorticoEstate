<?php
	//phpgw::import_class('booking.bocommon_authorized');
	phpgw::import_class('booking.async_task');

	class booking_boasync_settings
	{

		function __construct()
		{
			#parent::__construct();

			if (!isset($GLOBALS['phpgw']->asyncservice) || !is_object($GLOBALS['phpgw']->asyncservice))
			{
				$GLOBALS['phpgw']->asyncservice = CreateObject('phpgwapi.asyncservice');
			}
		}

		protected function current_account_member_of_admins()
		{
			return booking_account_helper::current_account_member_of_admins();
		}

		/**
		 * Providing no id to this method results in that authorization
		 * is performed for the collection. If the 'object' is provided,
		 * then authorization is performed for that object.
		 *
		 * @param $operation
		 * @param $object (optional)
		 *
		 * @return boolean true if authorized
		 * @throws booking_unauthorized_exception if not authorized
		 */
		protected function authorize( $operation, $object = null )
		{
			switch ($operation)
			{
				case 'read':
					return true;
				case 'write':
					if ($this->current_account_member_of_admins())
					{
						return true;
					}
				default:
					throw new booking_unauthorized_exception($operation, sprintf('Operation \'%s\' was denied on %s', $operation, get_class($this)));
			}
		}

		function read()
		{
			$this->authorize('read');

			$settings = array();

			foreach (booking_async_task::getAvailableTasks() as $task_class)
			{
				$task = booking_async_task::create($task_class);
				$settings[str_replace('.', '_', "{$task_class}_enabled")] = $task->is_enabled();
			}

			$settings['permission'] = $this->get_permissions();

			return $settings;
		}

		function update( $settings )
		{
			$this->authorize('write', $settings);
			foreach (booking_async_task::getAvailableTasks() as $task_class)
			{
				$task = booking_async_task::create($task_class);
				$task->disable();

				if ($settings[str_replace('.', '_', "{$task_class}_enabled")] === true)
				{
					$task->enable();
				}
			}
		}

		public function get_permissions()
		{
			$permission = array('read' => true);
			if ($this->current_account_member_of_admins())
			{
				$permission['write'] = true;
			}

			return $permission;
		}
	}