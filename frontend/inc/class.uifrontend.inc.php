<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id$
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
	 * Frontend main class
	 *
	 * @package Frontend
	 */
	class frontend_uifrontend extends frontend_uicommon
	{

		public function __construct()
		{
			parent::__construct();
		}

		public function index()
		{
			//Forward to helpdesk
			$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', '.ticket');
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'frontend.uihelpdesk.index',
				'location_id' => $location_id));
		}

		public function query()
		{

		}

		public function save_profile()
		{
			$values = phpgw::get_var('values');
			
			if ($values)
			{
				$user_id = $GLOBALS['phpgw_info']['user']['account_id'];
				$pref = CreateObject('phpgwapi.preferences', $user_id);
				$pref->read();
				$pref->add('common', 'cellphone', $values['cellphone'], 'user');
				$pref->add('common', 'email', $values['email'], 'user');
				$pref->save_repository();
				
				return array('status' => 'saved');
			}
		}
	}