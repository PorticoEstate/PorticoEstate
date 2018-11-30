<?php
	phpgw::import_class('booking.uicommon');

	class booking_uimetasettings extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('admin::bookingfrontend::metasettings');
			$this->fields = array(
				'metatag_author' => 'string',
				'metatag_robots' => 'string',
				'frontpagetitle' => 'string',
				'frontpagetext' => 'html',
				'frontimagetext' => 'html'
			);
		}

		public function index()
		{
			$appname = phpgw::get_var('appname');
			$appname = $appname ? $appname : 'booking';
			$config = CreateObject('phpgwapi.config', $appname);
			$config->read();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$metasettings = extract_values($_POST, $this->fields);

				foreach ($metasettings as $dim => $value)
				{
					if (strlen(trim($value)) > 0)
					{
						$config->value($dim, $value);
					}
					else
					{
						unset($config->config_data[$dim]);
					}
				}
				$config->save_repository();
			}

			$tabs = array();
			$tabs['meta'] = array('label' => lang('metadata settings'), 'link' => '#meta');
			$active_tab = 'meta';

			$meta['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			self::rich_text_editor('field_frontpagetext');
			self::rich_text_editor('field_frontimagetext');

			self::render_template('metasettings', array('config_data' => $config->config_data,
				'meta' => $meta));
		}

		function query()
		{

		}
	}
