<?php
	/**
	* Frontend : a simplified tool for end users.
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
	* @version $Id: class.sorental.inc.php 11380 2013-10-18 12:29:11Z sigurdne $
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

		public function get_location($parties)
		{
			$parties = (array) $parties;
			if(!$parties)
			{
				return array();
			}

			$ts = time();
			
			$filtermethod = 'WHERE rental_party.id IN (' . implode(',', $parties) . ')'; 
			//active contract
			$filtermethod .= " AND ({$ts} >= date_start AND (date_end IS NULL OR {$ts} <= date_end))";
			
			$sql = "SELECT DISTINCT location_code FROM"
			. " rental_contract {$this->_db->join} rental_contract_party ON (rental_contract.id = rental_contract_party.contract_id)"
			. " {$this->_db->join} rental_party ON (rental_party.id = rental_contract_party.party_id)"
			. " {$this->_db->join} rental_contract_composite ON (rental_contract.id = rental_contract_composite.contract_id)"
			. " {$this->_db->join} rental_composite ON (rental_contract_composite.composite_id = rental_composite.id)"
			. " {$this->_db->join} rental_unit ON (rental_composite.id = rental_unit.composite_id)"
			. " {$filtermethod}";

//_debug_array($sql);
			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();
			$level = 0;
			$map_level = array();
			while ($this->_db->next_record())
			{
				$location_code	= $this->_db->f('location_code', true);
				
				$_level = substr_count($location_code, '-') + 1;
				
				$level = $_level > $level ? $_level : $level;
				
				$map_level[$level][] = $location_code;
			}
			
			foreach ($map_level as $level => $locations)
			{
			
				$sql = "SELECT loc{$level}_name as name, location_code FROM fm_location{$level} WHERE location_code IN ('" . implode("','", $locations) . "')";
				$this->_db->query($sql,__LINE__,__FILE__);
				while ($this->_db->next_record())
				{
					$values[] = array
					(
						'location_code'	=> $this->_db->f('location_code', true),
						'name'			=> $this->_db->f('name', true),
					);
				}
			}

			return $values;
		}


		/**
		* translate from org_unit to party.id
		**/
		function get_parties($org_units)
		{
			if(!$org_units)
			{
				return array();
			}
			
			$sql = 'SELECT id FROM rental_party WHERE org_enhet_id IN (' . implode(',', $org_units) . ')'; 
//_debug_array($sql);
			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();

			while ($this->_db->next_record())
			{
				$values[] = $this->_db->f('id');
			}

			return $values;
		}

		public function get_total_cost_and_area($org_units = array(), $selected_location = '')
		{
			if(!$org_units)
			{
				return array();
			}

			$ts = time();
			$filtermethod = 'WHERE rental_party.id IN (' . implode(',', $org_units) . ')'; 
			//active contract
			$filtermethod .= " AND ({$ts} >= rental_contract.date_start AND (rental_contract.date_end IS NULL OR {$ts} <= rental_contract.date_end))";
			
			
			$join_method = '';
			if($selected_location)
			{
				$filtermethod .= " AND location_code {$this->_db->like} '{$selected_location}%'";
				$join_method =  " {$this->_db->join} rental_contract_composite ON (rental_contract.id = rental_contract_composite.contract_id)"
					. " {$this->_db->join} rental_composite ON (rental_contract_composite.composite_id = rental_composite.id)"
					. " {$this->_db->join} rental_unit ON (rental_composite.id = rental_unit.composite_id) ";

			}
			
			$sql = "SELECT sum(total_price::numeric) AS sum_total_price FROM"
			. " rental_contract {$this->_db->join} rental_contract_party ON (rental_contract.id = rental_contract_party.contract_id)"
			. " {$this->_db->join} rental_party ON (rental_party.id = rental_contract_party.party_id)"
			. " {$this->_db->join} rental_contract_price_item ON (rental_contract.id  = rental_contract_price_item.contract_id)"
			. " {$join_method}{$filtermethod} AND NOT is_one_time";


			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();
			$this->_db->next_record();
			$values['sum_total_price'] = $this->_db->f('sum_total_price');


			$sql = "SELECT sum(rental_contract.rented_area::numeric) AS sum_total_area FROM"
			. " rental_contract {$this->_db->join} rental_contract_party ON (rental_contract.id = rental_contract_party.contract_id)"
			. " {$this->_db->join} rental_party ON (rental_party.id = rental_contract_party.party_id)"
			. " {$join_method}{$filtermethod}";


			$this->_db->query($sql,__LINE__,__FILE__);

			$this->_db->next_record();
			$values['sum_total_area'] = $this->_db->f('sum_total_area');

			return $values;
		}
	}
