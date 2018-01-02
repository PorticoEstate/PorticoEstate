<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.uientity.inc.php 11914 2014-04-23 13:12:52Z sigurdne $
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

	phpgw::import_class('frontend.uicommon');

	/**
	 * Controller
	 *
	 * @package Frontend
	 */
	class frontend_uicontroller extends frontend_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
		);

		public function __construct()
		{
			$GLOBALS['phpgw']->translation->add_app('property');
			$this->location_id = phpgw::get_var('location_id', 'int', 'REQUEST', 0);
			$location_info = $GLOBALS['phpgw']->locations->get_name($this->location_id);
			$this->acl_location = $location_info['location'];

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'frontend');

			phpgwapi_cache::session_set('frontend', 'tab', $this->location_id);
			parent::__construct();
			$this->location_code = $this->header_state['selected_location'];
			/*
			  $this->bo->location_code = $this->location_code;

			  $_org_units = array();
			  if(is_array($this->header_state['org_unit']))
			  {
			  foreach ($this->header_state['org_unit'] as $org_unit)
			  {
			  $_org_unit_id = (int)$org_unit['ORG_UNIT_ID'];
			  $_subs = execMethod('property.sogeneric.read_tree',array('node_id' => $_org_unit_id, 'type' => 'org_unit'));
			  $_org_units[$_org_unit_id] = true;
			  foreach($_subs as $entry)
			  {
			  $_org_units[$entry['id']] = true;
			  if(isset($entry['children']) && $entry['children'])
			  {
			  $this->_get_children($entry['children'], $_org_units);
			  }
			  }
			  }
			  }
			  $org_units = array_keys($_org_units);
			  $this->bo->org_units = $org_units;
			 */
		}

		/**
		 * Get the sublevels of the org tree into one arry
		 */
		private function _get_children( $data = array(), &$_org_units )
		{
			foreach ($data as $entry)
			{
				$_org_units[$entry['id']] = true;
				if (isset($entry['children']) && $entry['children'])
				{
					$this->_get_children($entry['children'], $_org_units);
				}
			}
		}

		public function index()
		{
			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'controller.index';
			$this->insert_links_on_header_state();
			//redirect if no rights

			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$data = array
				(
				'header' => $this->header_state,
				'section' => array(
					'tabs' => $this->tabs,
					'menu' => $this->menu,
					'controller' => array('location_code' => $this->location_code)
				)
			);
			self::render_template_xsl(array('controller', 'datatable_inline', 'frontend'), $data);
		}

		public function query()
		{

		}
	}