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

		private $perform_action = false;

		public function __construct()
		{
			/**
			 * Wait for it...
			 */
			//parent::__construct();

			$script_path = dirname(phpgw::get_var('SCRIPT_FILENAME', 'string', 'SERVER'));

			if(preg_match('/eventplannerfrontend/', $script_path))
			{
				$this->perform_action = true;
			}
		}
		/**
		 * set auth_type for custom login - called from login
		 *
		 * @return void
		 */
		public function set_auth_type()
		{
			if(!$this->perform_action)
			{
				return;
			}
			//get from local config
			$config = CreateObject('phpgwapi.config', 'eventplannerfrontend')->read();

			if (!empty($config['auth_type']))
			{
				$GLOBALS['phpgw_info']['server']['auth_type'] = $config['auth_type'];
			}
		}

		public function set_cookie_domain()
		{
			if(!$this->perform_action)
			{
				return;
			}

			//get from local config
			$config = CreateObject('phpgwapi.config', 'eventplannerfrontend')->read();

			$GLOBALS['phpgw_info']['server']['cookie_domain'] = !empty($GLOBALS['phpgw_info']['server']['cookie_domain']) ? $GLOBALS['phpgw_info']['server']['cookie_domain'] : '';

			if (!empty($config['cookie_domain']))
			{
				$GLOBALS['phpgw_info']['server']['cookie_domain'] = $config['cookie_domain'];
			}
			
		}

		public function login( )
		{
			if(!$this->perform_action)
			{
				return;
			}
			$bouser = CreateObject('eventplannerfrontend.bouser');
			$bouser->log_in();
		}

		/**
		 * Show info for homepage
		 *
		 * @return void
		 */
		public function home()
		{
			if(!$this->perform_action)
			{
				return;
			}
			parent::__construct();

			$data = array(
				'config' => CreateObject('phpgwapi.config', 'eventplannerfrontend')->read(),
			);
			self::render_template_xsl(array('home'), array('view' => $data));
		}

		public function after_navbar()
		{
			if(!$this->perform_action)
			{
				return;
			}
			//parent::__construct();


			if(!empty($_SESSION['orgs']) && is_array($_SESSION['orgs']))
			{
				$orgs = $_SESSION['orgs'];
				$org_id = !empty($_SESSION['org_id']) ? $_SESSION['org_id'] : '';
			}
			else
			{
				return;
			}

			$org_option ='';
			foreach ($orgs as $org)
			{
				$selected = '';
				if ($org_id == $org['id'])
				{
					$selected = 'selected = "selected"';
				}

				$org_option .= <<<HTML

				<option value='{$org['id']}'{$selected}>{$org['name']}</option>

HTML;
			}


//			_debug_array($_SERVER);

			if ($orgs)
			{
//				$action = $GLOBALS['phpgw']->link('/eventplannerfrontend/login.php', array('stage' => 2));
				$action = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
				$message = 'Velg organisasjon';

				$org_select = <<<HTML
							<p>
								<label for="org_id">Velg Organisasjon:</label>
								<select name="org_id" id="org_id">
									{$org_option}
								</select>
							</p>
HTML;
			}

			$html = <<<HTML
			﻿<!DOCTYPE html>
					<h2>{$message}</h2>
					<form action="{$action}" method="POST">
						<fieldset>
							<legend>
								Organisasjon
							</legend>
							$org_select
							<p>
								<input type="submit" name="submit" value="Fortsett"  />
							</p>
			 			</fieldset>
					</form>
HTML;

			echo $html;
		}
	}