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
			$session_org_id = phpgw::get_var('session_org_id','int' , 'POST');
			if($session_org_id)
			{
				try
				{
					$_SESSION['org_id'] = createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($session_org_id);
				}
				catch (sfValidatorError $e)
				{
					$_SESSION['org_id'] = '';
				}
			}
			else if ($_POST['session_org_id'])
			{
				$_SESSION['org_id'] = '';
			}

			/**
			 * $_SESSION['orgs'] is set in eventplannerfrontend_external_user::get_user_org_id()
			 */

			if(!empty($_SESSION['orgs']) && is_array($_SESSION['orgs']))
			{
				$orgs = phpgw::get_var('orgs', 'string', 'SESSION');
				$org_id = phpgw::get_var('org_id','int' , 'SESSION');
			}
			else
			{
				return;
			}

			$lang_none = lang('none');
			$org_option ="<option value='-1'>{$lang_none}</option>";
			foreach ($orgs as $org)
			{
				$selected = '';
				if ($org_id == (int)$org['id'])
				{
					$selected = ' selected="selected"';
				}

				$org_option .= <<<HTML

				<option value='{$org['id']}'{$selected}>{$org['name']}</option>

HTML;
			}

			if ($orgs)
			{
				if(!empty($_GET['menuaction']))
				{
					$action = $GLOBALS['phpgw']->link('/eventplannerfrontend/',
						array
						(
							'menuaction' => phpgw::get_var('menuaction')
						)
					);
				}
				else
				{
					$action = $GLOBALS['phpgw']->link('/eventplannerfrontend/home.php');
				}

				$message = 'Velg organisasjon';

				$org_select = <<<HTML
				
					<label for="org_id">Velg Organisasjon:</label>
					<select name="session_org_id" id="org_id" onChange="this.form.submit();">
						{$org_option}
					</select>
				
HTML;
			}

			$html = <<<HTML

			<div id="organsation_select">
				<form action="{$action}" method="POST">
					$org_select
				</form>
			</div>
HTML;

			echo $html;
		}

		/**
		* hook to add account
		*
		* this function is a wrapper function for eventplanner
		*
		* @param _hookValues contains the hook values as array
		* @returns nothing
		*/
		function addaccount()
		{
			$account_id = (int)$GLOBALS['hook_values']['account_id'];
			$headers = getallheaders();
			$ssn = $headers['uid'];

			if(!$ssn)
			{
				return;
			}

			$ssn_hash = "{SHA}" . base64_encode(phpgwapi_common::hex2bin(sha1($ssn)));

			$hash_safe = $GLOBALS['phpgw']->db->db_addslashes($ssn_hash); // just to be safe :)

			$data = json_encode(array('ssn_hash' => $hash_safe));

			$sql = "INSERT INTO phpgw_accounts_data (account_id, account_data) VALUES ({$account_id}, '{$data}')";
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
		}

	}