<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009,2010,2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
 	* @version $Id: class.bocommon.inc.php 11511 2013-12-08 20:57:07Z sigurdne $
	*/

	/**
	 * Description
	 * @package property
	 */

	class mobilefrontend_bocommon
	{

		function __construct()
		{
		}

		/**
		* Check if session is expired
		*/
		function confirm_session()
		{
			if($GLOBALS['phpgw']->session->verify())
			{
				header('Content-Type: application/json');
				echo json_encode(array('sessionExpired'=>false));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
		}
	}
