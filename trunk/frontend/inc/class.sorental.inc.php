<?php
	/**
	* Common so-functions, database related helpers 
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
	* @version $Id$
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


			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();
			while ($this->_db->next_record())
			{
				$values[] = array
				(
					'location_code'		=> $this->_db->f('location_code', true),
				);
			}
			
			foreach ($values as &$entry)
			{

				$location_code = $entry['location_code'];
				// We get the data from the property module
				$data = execMethod('property.bolocation.read_single', array('location_code' => $location_code, 'extra' => array('view' => true)));

				$stop_search = false;
				for($i = 1; !$stop_search; $i++)
				{
					$loc_name = "loc{$i}_name";
					if(array_key_exists($loc_name, $data))
					{
						$entry[$loc_name] =  $data[$loc_name];
					}
					else
					{
						$stop_search = true;
					}
				}

				$entry['address'] = $data['street_name'].' '.$data['street_number'];
				foreach($data['attributes'] as $attributes)
				{
					switch($attributes['column_name'])
					{
						case 'area_gross':
							$entry['area_gros'] = $attributes['value'];
							break;
						case 'area_net':
							$entry['area_net'] = $attributes['value'];
							break;
						case 'bruttoareal':
							$entry['area_gros'] = $attributes['value'];
							break;
						case 'nettoareal':
							$entry['area_net'] = $attributes['value'];
							break;
					}
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
_debug_array($sql);
			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();

			while ($this->_db->next_record())
			{
				$values[] = $this->_db->f('id');
			}

			return $values;
		}

		public function get_total_cost_and_area($org_units = array())
		{
			if(!$org_units)
			{
				return array();
			}

			$ts = time();
			$filtermethod = 'WHERE rental_party.id IN (' . implode(',', $org_units) . ')'; 
			//active contract
			$filtermethod .= " AND ({$ts} >= rental_contract.date_start AND (rental_contract.date_end IS NULL OR {$ts} <= rental_contract.date_end))";
			
			$sql = "SELECT sum(total_price::numeric) AS sum_total_price FROM"
			. " rental_contract {$this->_db->join} rental_contract_party ON (rental_contract.id = rental_contract_party.contract_id)"
			. " {$this->_db->join} rental_party ON (rental_party.id = rental_contract_party.party_id)"
			. " {$this->_db->join} rental_contract_price_item ON (rental_contract.id  = rental_contract_price_item.contract_id)"
			. " {$filtermethod} AND NOT is_one_time";

			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();
			$this->_db->next_record();
			$values['sum_total_price'] = $this->_db->f('sum_total_price');

			$sql = "SELECT sum(rental_contract.rented_area::numeric) AS sum_total_area FROM"
			. " rental_contract {$this->_db->join} rental_contract_party ON (rental_contract.id = rental_contract_party.contract_id)"
			. " {$this->_db->join} rental_party ON (rental_party.id = rental_contract_party.party_id)"
			. " {$filtermethod}";


			$this->_db->query($sql,__LINE__,__FILE__);

			$this->_db->next_record();
			$values['sum_total_area'] = $this->_db->f('sum_total_area');

			return $values;
		}
	}
