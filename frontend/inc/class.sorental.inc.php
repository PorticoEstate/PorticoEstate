<?php
	/**
	* Common so-functions, database related helpers 
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
	* @version $Id: class.socommon_core.inc.php 11370 2013-10-11 15:29:50Z sigurdne $
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */




	/**
	 * Description
	 * @package frontend
	 */

	class frontend_sorental
	{
		/**
		* @var int $_total_records total number of records found
		*/
		protected $_total_records = 0;


		/**
		* @var int $_receipt feedback on actions
		*/
		protected $_receipt = array();


		/**
		 * @var object $_db reference to the global database object
		 */
		protected $_db;

		/**
		 * @var string $_join SQL JOIN statement
		 */
		protected $_join;

		/**
		 * @var string $_join SQL LEFT JOIN statement
		 */
		protected $_left_join;

		/**
		 * @var string $_like SQL LIKE statement
		 */
		protected $_like;

		protected $_global_lock = false;

		public function __construct()
		{
			$this->account		= (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->_db			= & $GLOBALS['phpgw']->db;
			$this->_join		= & $this->_db->join;
			$this->_like		= & $this->_db->like;
			$this->_left_join	= & $this->_db->left_join;
		}

		/**
		 * Magic get method
		 *
		 * @param string $varname the variable to fetch
		 *
		 * @return mixed the value of the variable sought - null if not found
		 */
		public function __get($varname)
		{
			switch ($varname)
			{
				case 'total_records':
					return $this->_total_records;
					break;
				case 'receipt':
					return $this->_receipt;
					break;
				default:
					return null;
			}
		}

		public function get_location($party_id)
		{
			$party_id = (int) $party_id;

		_debug_array($party_id);

			//FIXME something clever
			$sql = "SELECT * FROM somewhere WHERE id={$party_id}";

			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();
			while ($this->_db->next_record())
			{
				$values = array
				(
					'location_code'		=> $this->_db->f('location_code', true),
				);
			}

/*
            [loc1_name] => BERGEN RÅDHUS
            [loc2_name] => BERGEN RÅDHUS NYE
            [location_code] => 1102-01
            [address] => Rådhusgaten 10
            [area_net] => 0
            [area_gros] => 11277
*/


			return $values;

		}



	}
