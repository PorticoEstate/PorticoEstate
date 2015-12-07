<?php
	phpgw::import_class('booking.uicommon');

	class booking_uimetasettings extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
		);
		
		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('admin::bookingfrontend::metasettings');
		}
		
		public function index()
		{
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				foreach($_POST as $dim => $value)
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
			$tabs['meta'] = array('label' => lang('metadata settings'), 'link' => '#meta');
			$active_tab = 'meta';
			
			$meta['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			phpgwapi_jquery::init_ckeditor('field_frontpagetext');

			self::render_template('metasettings', array('config_data' =>$config->config_data, 'meta' => $meta));
		}

		function query()
		{

		}
	}
