<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.uicommon');

	class booking_uisettings extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('admin::booking::settings');
		}

		public function index()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();

			// Define internal and external billing sequence number values
			$internal_value = null;
			$external_value = null;

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				foreach ($_POST['config_data'] as $dim => $value)
				{
					if (strlen(trim($value)) > 0)
					{
						$config->value($dim, trim($value));
					}
					else
					{
						unset($config->config_data[$dim]);
					}
				}
				$config->save_repository();

				// Save internal and external sequential numbers to database
				if (isset($_POST['billing']))
				{
					$internal_value = intval($_POST['billing']['internal']);
					$external_value = intval($_POST['billing']['external']);
					$GLOBALS['phpgw']->db->query("UPDATE bb_billing_sequential_number_generator SET value=" . $internal_value . " WHERE name='internal'");
					$GLOBALS['phpgw']->db->query("UPDATE bb_billing_sequential_number_generator SET value=" . $external_value . " WHERE name='external'");
				}
			}

			// Load internal and external sequential numbers from database (if not already set) and insert into $billing
			if (is_null($internal_value) || is_null($external_value))
			{
				$db = $GLOBALS['phpgw']->db;
				$db->query("SELECT name, value FROM bb_billing_sequential_number_generator WHERE name='internal' OR name='external' LIMIT 2");
				while ($db->next_record())
				{
					if (!strcmp($db->f('name', false), "internal"))
					{
						$internal_value = intval($db->f('value', false));
					}
					else if (!strcmp($db->f('name', false), "external"))
					{
						$external_value = intval($db->f('value', false));
					}
				}
			}

			$tabs = array();
			$tabs['settings'] = array('label' => lang('settings'), 'link' => '#settings');
			$active_tab = 'settings';

			$billing = array('internal' => $internal_value, 'external' => $external_value);
			$billing['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::render_template('settings', array('config_data' => $config->config_data,
				'billing' => $billing));
		}

		/**
		 * Dummy function to meet the requirements of the parent class
		 */
		public function query()
		{

		}
	}