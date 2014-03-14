<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.uicontract_ex.inc.php 11487 2013-11-25 12:44:37Z sigurdne $
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

	phpgw::import_class('frontend.uicontract');

	class frontend_uicontract_ex extends frontend_uicontract
	{
		public function __construct()
		{
			$this->contract_state_identifier = "contract_state_ex";
			$this->contracts_per_location_identifier = "contracts_ex_per_location";
		//	$this->form_url = "index.php?menuaction=frontend.uicontract_ex.index";
			$this->form_url = $GLOBALS['phpgw']->link('/',array('menuaction' => 'frontend.uicontract_ex.index'));
			phpgwapi_cache::session_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.rental.contract_ex'));
			parent::__construct();
		}
	}
