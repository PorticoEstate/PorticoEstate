<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uibuilding extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'	=>	true
		);

		function __construct()
		{
			$this->bo = CreateObject('bookingfrontend.bobuilding');
			parent::__construct();
			$old_top = array_pop($this->tmpl_search_path);
			array_push($this->tmpl_search_path, PHPGW_SERVER_ROOT . '/booking/templates/base');
			array_push($this->tmpl_search_path, $old_top);
		}
		
		function index()
		{
			$search = array();
			$target_id = phpgw::get_var('id');
			$search['results'] = $this->bo->read_single($target_id);
			$search['results']['start'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookingfrontend.uisearch.index'));
				
			self::render_template('building', array('search' => $search));
		}
		
		
	}
