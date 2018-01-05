<?php

	/**
	 * An entryPoint for running tasks
	 */
	interface PhpgwContext
	{

		function getDb();
	}

	class PhpgwEntry implements PhpgwContext
	{

		protected $db;

		function __construct( $callable, $parameters = array() )
		{
			$this->call($callable, $parameters);
		}

		protected function in()
		{
			$path_to_phpgroupware = dirname(__FILE__) . '/../..'; // need to be adapted if this script is moved somewhere else
			$_GET['domain'] = 'default';

			$GLOBALS['phpgw_info']['flags'] = array(
				'currentapp' => 'login',
				'noapi' => True  // this stops header.inc.php to include phpgwapi/inc/function.inc.php
			);

			/**
			 * Include phpgroupware header
			 */
			include($path_to_phpgroupware . '/header.inc.php');

			unset($GLOBALS['phpgw_info']['flags']['noapi']);

			$db_type = $GLOBALS['phpgw_domain'][$_GET['domain']]['db_type'];
			if ($db_type == 'postgres')
			{
				$db_type = 'pgsql';
			}
			if (!extension_loaded($db_type) && !dl($db_type . '.so'))
			{
				echo "Extension '$db_type' is not loaded and can't be loaded via dl('$db_type.so') !!!\n";
			}

			$GLOBALS['phpgw_info']['server']['sessions_type'] = 'db';

			/**
			 * Include API functions
			 */
			include(PHPGW_API_INC . '/functions.inc.php');

			for ($i = 0; $i < 10; $i++)
			{
				restore_error_handler(); //Remove at least 10 levels of custom error handling
			}

			for ($i = 0; $i < 10; $i++)
			{
				restore_exception_handler(); //Remove at least 10 levels of custom exception handling
			}

			$this->initializeContext();
		}

		protected function out()
		{
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		protected function initializeContext()
		{
			phpgw::import_class('booking.bocommon_authorized');
			booking_bocommon_authorized::disable_authorization();
			$this->db = &$GLOBALS['phpgw']->db;
		}

		public function getDb()
		{
			return $this->db;
		}

		protected function call( $callable, $parameters = array() )
		{
			$this->in();
			array_unshift($parameters, $this);
			call_user_func_array($callable, $parameters);
			$this->out();
		}

		static public function phpgw_call( $callable, $parameters = array() )
		{
			new self($callable, $parameters);
		}
	}