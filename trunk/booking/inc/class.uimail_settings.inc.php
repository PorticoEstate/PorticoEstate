<?php
	phpgw::import_class('booking.uicommon');

	class booking_uimail_settings extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('booking::settings::mail_settings');
		}

		public function query()
		{
			
		}

		public function index()
		{
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				foreach ($_POST as $dim => $value)
				{
					if (strlen(trim($value)) > 0)
					{
						$config->value($dim, phpgw::clean_value($value));
					}
					else
					{
						unset($config->config_data[$dim]);
					}
				}
				$config->save_repository();
			}
			$tabs = array();
			$tabs['generic'] = array('label' => lang('Mail Settings'), 'link' => '#mail_settings');
			$active_tab = 'generic';

			$mail = array();
			$mail['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::render_template_xsl('mail_settings', array('config_data' => $config->config_data,
				'data' => $mail));
		}
	}