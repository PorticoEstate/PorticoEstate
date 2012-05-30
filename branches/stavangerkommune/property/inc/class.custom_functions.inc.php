<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * This is a class used to gain access to custom classes stored in /inc/cron to be run as cron jobs
	 * or from the admin interface.
	 * usage (example): /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default forward_mail_as_sms user=<username> cellphone=<phonenumber>
	 * @package property
	 */

	class property_custom_functions
	{
		var $public_functions = array
			(
				'index' => true
			);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true;
		}

		/**
		 * @param mixed $data
		 * If $data is an array - then the process is run as cron - and will look for $data['function'] to
		 * determine which custom class to load
		 */

		function index($data='')
		{
			if ( !isset($GLOBALS['phpgw_info']['user']['apps']['admin']))
			{
				return;
			}

			if(is_array($data))
			{
				$function = $data['function'];
			}
			else
			{
				$data = unserialize(urldecode(phpgw::get_var('data')));

				$data = phpgw::clean_value($data);
				if(!isset($data['function']))
				{
					$function = phpgw::get_var('function');
				}
				else
				{
					$function =$data['function'];
				}
			}
			// prevent path traversal
			if ( preg_match('/\.\./', $function) )
			{
				return;
			}

			$file = PHPGW_SERVER_ROOT . "/property/inc/cron/{$GLOBALS['phpgw_info']['user']['domain']}/{$function}.php";

			if (is_file($file))
			{
				require_once $file;
				$custom = new $function;
				$custom->pre_run($data);
			}
			else
			{
				echo "no such file: path_to_phpgw_server_root/property/inc/cron/{$GLOBALS['phpgw_info']['user']['domain']}/{$function}.php";
			}
		}
	}
