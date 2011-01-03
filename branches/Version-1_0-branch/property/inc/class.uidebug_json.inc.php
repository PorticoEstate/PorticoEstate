<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author César Ramírez <cr@ccfirst.com>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @subpackage project
 	* @version $Id$
	*/

	//phpgw::import_class('phpgwapi.yui');

	/**
	 * Description
	 * @package property
	 */

	class property_uidebug_json
	{
		var $public_functions = array
			(
				'index' 		=> true
			);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$this->acl 				= & $GLOBALS['phpgw']->acl;
			$this->acl_location		= '.admin';
			$this->acl_read 		= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
		}

		public function index()
		{
			if(!$this->acl_read)
			{
				echo lang('no access');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$app = phpgw::get_var('app', 'string', 'GET');

			//get session's values
			$data = phpgwapi_cache::session_get($app,'id_debug');
			if(isset($data))
			{
				//clear session
				phpgwapi_cache::session_clear($app, 'id_debug');
				//replace '<' and '>'
				if (is_array($data))
				{
					self::_my_print_rec($data,0);
				}
				else
				{
					$data = htmlspecialchars($data);
				}
				_debug_array($data);
			}
			else
			{
				echo "empty session's value"; 
			}
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		static protected function _my_print_rec(&$val,$nivel=0)
		{
			foreach($val as $key => &$value)
			{
				if(is_array($value))
				{
					self::_my_print_rec($value,$nivel+1);
				}
				else
				{
					//	$value = str_replace(array('<','>'),array('&lt;','&gt;'),$value);
					$value = htmlspecialchars($value);
				}
			}
		}
	}

