<?php
	/**
	 * phpGroupWare - registration
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2018 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package registration
	 * @version $Id: class.uidimb_role_user.inc.php 16610 2017-04-21 14:21:03Z sigurdne $
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class property_uiscanner extends phpgwapi_uicommon_jquery
	{

		private $account_id;
		var $public_functions = array
			(
			'index'	 => true,
			'query'	 => true,
			'edit'	 => true,
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app']	 = true;
			$this->account_id							 = $GLOBALS['phpgw_info']['user']['account_id'];

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::qrcode_scanner';

		}

		function index()
		{

			self::add_javascript('phpgwapi', 'html5-qrcode', 'html5-qrcode.min.js');
			self::add_javascript('property', 'base', 'qrcode_scanner.js', true);
			$data = array();
			self::render_template_xsl(array('qrcode_scanner'), $data);
		}

		public function query()
		{

			//	return json_encode($values);
		}

		public function edit()
		{
			$data = array();
			self::render_template_xsl(array('qrcode_scanner'), $data);
		}
	}