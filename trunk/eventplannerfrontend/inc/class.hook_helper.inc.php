<?php
	/**
	 * property - Hook helper
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package property
	 * @version $Id: class.hook_helper.inc.php 14726 2016-02-11 20:07:07Z sigurdne $
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

	phpgw::import_class('phpgwapi.uicommon');

	/**
	 * Hook helper
	 *
	 * @package property
	 */
	class eventplannerfrontend_hook_helper extends phpgwapi_uicommon
	{

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
		}
		/**
		 * Show info for homepage
		 *
		 * @return void
		 */
		public function home()
		{
			$data = array(
				'config' => CreateObject('phpgwapi.config', 'eventplannerfrontend')->read(),
			);
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript('eventplannerfrontend', 'portico', 'validate.js');
			self::render_template_xsl(array('home'), array('view' => $data));
		}
	}