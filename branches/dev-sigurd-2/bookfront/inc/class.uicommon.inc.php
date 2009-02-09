<?php
	phpgw::import_class('phpgwapi.yui');

	class booking_uicommon
	{
		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			self::set_active_menu('booking');
			self::add_stylesheet('phpgwapi/js/yahoo/autocomplete/assets/skins/sam/autocomplete.css');
			self::add_stylesheet('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			self::add_stylesheet('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			self::add_stylesheet('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			self::add_javascript('booking', 'booking', 'common.js');
		}

		public function link($data)
		{
			return $GLOBALS['phpgw']->link('/index.php', $data);
		}

		public function redirect($link_data)
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		public function add_stylesheet($path)
		{
			$GLOBALS['phpgw']->css->add_external_file($path);
		}

		public function add_javascript($app, $pkg, $name)
		{
  			$GLOBALS['phpgw']->js->validate_file($pkg, str_replace('.js', '', $name), $app);
		}

        public function set_active_menu($item)
        {
            $GLOBALS['phpgw_info']['flags']['menu_selection'] = $item;
        }

        public function render_template($files, $data)
        {
            $output = phpgw::get_var('output', 'string', 'REQUEST', 'html');
            $GLOBALS['phpgw']->xslttpl->set_output($output);
                        $GLOBALS['phpgw']->xslttpl->add_file(array($files));
            $GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('data' => $data));
        }
	}
