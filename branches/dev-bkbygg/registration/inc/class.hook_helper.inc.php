<?php
	/**
	 * registration - Hook helper
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package registration
	 * @version $Id: class.hook_helper.inc.php 9104 2012-04-04 09:47:47Z sigurdne $
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

	/**
	 * Hook helper
	 *
	 * @package registration
	 */
	class registration_hook_helper
	{

		/**
		 * Clear reg_accounts either as part of a cron-job or logout hook
		 *
		 * @return void
		 */
		public function clear_reg_accounts()
		{
			$c = createobject('phpgwapi.config', 'registration');
			$c->read();

			if ($c->config_data['activate_account'] == 'pending_approval')
			{
				$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_reg_accounts WHERE reg_dla <= '"
					. (time() - 7200) . "' AND reg_info IS NULL", __LINE__, __FILE__);
			}
			else
			{
				$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_reg_accounts WHERE reg_dla <= '"
					. (time() - 7200) . "'", __LINE__, __FILE__);
			}
		}
	}