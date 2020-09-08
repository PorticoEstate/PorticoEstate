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
			$is_admin = $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin');
			$local_admin = false;
			if(!$is_admin)
			{
				if($GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'bookingfrontend'))
				{
					$local_admin = true;
				}
			}

			if(!$is_admin && !$local_admin)
			{
				phpgw::no_access();
			}

			parent::__construct();
			self::set_active_menu('admin::bookingfrontend::metasettings');
			$this->fields = array(
				'metatag_author' => 'string',
				'metatag_robots' => 'string',
				'frontpagetitle' => 'string',
				'frontpagetext' => 'html',
				'frontimagetext' => 'html',
				'participanttext' => 'html'
			);
		}

		public function index()
		{
			$appname = phpgw::get_var('appname');
			$appname = $appname ? $appname : 'booking';
			if(!$GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, $appname))
			{
				phpgw::no_access();
			}

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
			self::rich_text_editor('field_participanttext');

			self::render_template('metasettings', array('config_data' => $config->config_data,
				'meta' => $meta));
		}

		function query()
		{

		}
	}
