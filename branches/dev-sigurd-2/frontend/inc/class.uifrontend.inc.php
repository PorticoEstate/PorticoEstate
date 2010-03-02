<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.uifrontend.inc.php 4859 2010-02-18 23:09:16Z sigurd $
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	phpgw::import_class('frontend.bofrontend');
	/**
	 * Frontend main class
	 *
	 * @package Frontend
	 */

	class frontend_uifrontend
	{
        /**
         * Used to save state of header (select box, ++) between requests
         * @var array
         */
        public $header_state;

		public $public_functions = array
		(
			'index'		=> true,
			'demo'		=> true
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$noframework = phpgw::get_var('noframework', 'bool');
			$GLOBALS['phpgw_info']['flags']['noframework'] = $noframework;
			$locations = frontend_bofrontend::get_sections();

			$tabs = array();
			foreach ($locations as $key => $entry)
			{
				$name = $entry['name'];
				$location = $entry['location'];

				if ( $GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, 'frontend') )
				{
					$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', $location);
					$tabs[$location_id] = array(
						'label' => lang($name),
						'link'  => $GLOBALS['phpgw']->link('/',array('menuaction' => "frontend.ui{$name}.index", 'type'=>$location_id, 'noframework' => $noframework))
					);
				}			
			}
			
			$selected = phpgw::get_var('type', 'int', 'REQUEST', 0);
			$this->tabs = $GLOBALS['phpgw']->common->create_tabs($tabs, $selected);
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "frontend::{$selected}";

			$this->acl 	= & $GLOBALS['phpgw']->acl;

            $this->header_state = array(
                'selected' => array('-1')
            );
		}

		/**
		 * TODO
		 */
		public function index()
		{
			$this->demo();
		}


		public function demo()
		{
			$data = array
			(
				'tabs'		=> $this->tabs
			);
            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'demo'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('demo_1' => $data));
		}
	}
