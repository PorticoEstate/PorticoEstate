<?php

	/**
	 * phpGroupWare - mobilefrontend.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @package mobilefrontend
	 * @version $Id: class.uifront.inc.php 10804 2013-02-13 13:24:06Z sigurdne $
	 */
	phpgw::import_class('phpgwapi.uicommon');
//	phpgw::import_class('phpgwapi.jquery');

	class mobilefrontend_uifront extends phpgwapi_uicommon
	{
		public $public_functions = array
		(
			'index' => true,
		);

		public function __construct()
		{
			parent::__construct();
		}

		 /**
		 * Entry function for this class
		 *
		 * @return void
		 */
		public function index()
		{
			$data = array
			(
				'message' => 'Hello World'
			);
			self::render_template_xsl('hello_world', $data);
		}

		public function query()
		{
		
		}

	}

