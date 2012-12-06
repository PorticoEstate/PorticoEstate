<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
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

	phpgw::import_class('phpgwapi.datetime');
	phpgw::import_class('property.socommon_core');

	/**
	 * Description
	 * @package property
	 */

	class property_socondition_survey extends property_socommon_core
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
		 * @var string $_like SQL LIKE statement
		 */
		protected $_like;


		public function __construct()
		{
			parent::__construct();
		}


		function read($data = array())
		{
			$start		= isset($data['start'])  ? (int) $data['start'] : 0;
			$filter		= isset($data['filter']) ? $data['filter'] : 'none';
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '' ;
			$cat_id		= isset($data['cat_id']) ? (int)$data['cat_id'] : 0;
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';

			$table = 'fm_condition_survey';
			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by id DESC';
			}

			$where = 'WHERE';
			if ($cat_id)
			{
				$filtermethod .= " {$where} category = {$cat_id}";
				$where = 'AND';
			}

			if($query)
			{
				$query			= $this->_db->db_addslashes($query);
				$querymethod	= " {$where} title {$this->_like} '%{$query}%'";
			}

			$sql = "SELECT * FROM {$table} $filtermethod $querymethod";

			$this->_db->query($sql,__LINE__,__FILE__);
			$this->_total_records = $this->_db->num_rows();

			if(!$allrows)
			{
				$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->_db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$values = array();
			while ($this->_db->next_record())
			{
				$values[] = array
				(
					'id'			=> $this->_db->f('id'),
					'title'			=> $this->_db->f('title',true),
					'descr'			=> $this->_db->f('descr',true),
					'address'		=> $this->_db->f('address',true),
					'entry_date'	=> $this->_db->f('entry_date'),
					'user'			=> $this->_db->f('user_id')
				);
			}

			return $values;
		}

		function read_single($data = array())
		{
			$table = 'fm_condition_survey';

			$id		= (int)$data['id'];
			$this->_db->query("SELECT * FROM {$table} WHERE id={$id}",__LINE__,__FILE__);

			$values = array();
			if ($this->_db->next_record())
			{
				$values = array
				(
					'id'				=> $id,
					'title'				=> $this->_db->f('title',true),
					'descr'				=> $this->_db->f('descr', true),
					'location_code'		=> $this->_db->f('location_code', true),
					'status_id'			=> (int)$this->_db->f('status_id'),
					'cat_id'			=> (int)$this->_db->f('category'),
					'vendor_id'			=> (int)$this->_db->f('vendor_id'),
					'coordinator_id'	=> (int)$this->_db->f('coordinator_id'),
					'report_date'		=> (int)$this->_db->f('report_date'),
					'user_id'			=> (int)$this->_db->f('user_id'),
					'entry_date'		=> (int)$this->_db->f('entry_date'),
					'modified_date'		=> (int)$this->_db->f('modified_date'),
				);

				if ( isset($data['attributes']) && is_array($data['attributes']) )
				{
					$values['attributes'] = $data['attributes'];
					foreach ( $values['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->db->f($attr['column_name']);
					}
				}
			}

			return $values;
		}


		function add($data)
		{
			$table = 'fm_condition_survey';

			$this->_db->transaction_begin();

			$id = $this->_db->next_id($table);

			$value_set						= $this->_get_value_set( $data );
			$value_set['id']				= $id;
			$value_set['entry_date']		= time();
			$value_set['title']				= $this->_db->db_addslashes($data['title']);
			$value_set['descr']				= $this->_db->db_addslashes($data['descr']);
			$value_set['status_id']			= (int)$data['status_id'];
			$value_set['category']			= (int)$data['cat_id'];
			$value_set['vendor_id']			= (int)$data['vendor_id'];
			$value_set['coordinator_id']	= (int)$data['coordinator_id'];
			$value_set['report_date']		= phpgwapi_datetime::date_to_timestamp($data['report_date']);
			$value_set['user_id']			= $this->account;
			$value_set['modified_date']		= time();


			$cols = implode(',', array_keys($value_set));
			$values	= $this->_db->validate_insert(array_values($value_set));
			$sql = "INSERT INTO {$table} ({$cols}) VALUES ({$values})";

			try
			{
				$this->_db->Exception_On_Error = true;
				$this->_db->query($sql,__LINE__,__FILE__);
				$this->_db->Exception_On_Error = false;
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
				return 0;
			}

			if($this->_db->transaction_commit())
			{
				return $id;
			}
			else
			{
				return 0;
			}
		}

		public function edit($data)
		{
			$table = 'fm_condition_survey';
			$id = (int)$data['id'];

			$value_set	= $this->_get_value_set( $data );

			$value_set['title']				= $this->_db->db_addslashes($data['title']);
			$value_set['descr']				= $this->_db->db_addslashes($data['descr']);
			$value_set['status_id']			= (int)$data['status_id'];
			$value_set['category']			= (int)$data['cat_id'];
			$value_set['vendor_id']			= (int)$data['vendor_id'];
			$value_set['coordinator_id']	= (int)$data['coordinator_id'];
			$value_set['report_date']		= phpgwapi_datetime::date_to_timestamp($data['report_date']);
			$value_set['user_id']			= $this->account;
			$value_set['modified_date']		= time();

			try
			{
				$this->_edit($id, $value_set, 'fm_condition_survey');
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			return $id;
		}

		public function edit_title($data)
		{
			$id = (int)$data['id'];

			$value_set	= array
			(
				'title' => $data['title']
			);

			try
			{
				$this->_edit($id, $value_set, 'fm_condition_survey');
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			return $id;
		}

		public function import($survey, $import_data = array())
		{
			if(!isset($survey['id']) || !$survey['id'])
			{
				throw new Exception('property_socondition_survey::import - missing id');
			}

			$location_data = execMethod('property.solocation.read_single', $survey['location_code']);
			
			$_locations = explode('-', $survey['location_code']);
			$i=1;
			foreach ($_locations as $_location)
			{
				$location["loc{$i}"] = $_location;
				$i++;
			}

			$sorequest	= CreateObject('property.sorequest');

//_debug_array($survey);			

			$this->_db->transaction_begin();

			foreach ($import_data as $entry)
			{
				if( ctype_digit($entry['condition_degree']) &&  $entry['condition_degree'] > 0)
				{
					$request = array();
					$request['street_name'] = $location_data['street_name'];
					$request['street_number'] = $location_data['street_number'];
					$request['location'] = $location;
					$request['location_code'] = $survey['location_code'];
					$request['origin'] = array(array('location' => '.project.condition_survey', 'data' => array(array('id' => (int)$survey['id']))));

					$request['title'] = $entry['title'];
					$request['descr'] = $entry['descr'];
					$request['cat_id'] = 13; //???? FIXME
					$request['building_part'] = $entry['building_part'];
					$request['coordinator'] = $survey['coordinator_id'];
					$request['status'] = 'registrert';//???? FIXME
					$request['budget'] = $entry['amount'];
					$request['planning_date'] = mktime(13,0,0,7,1, $entry['due_year']?$entry['due_year']:date('Y'));
					$request['planning_value'] = $entry['amount'];
					$request['condition'] = array
					(
						array
						(
							'degree' => $entry['condition_degree'],
							'condition_type' => $entry['condition_type'],
							'consequence' => $entry['consequence'],
							'probability' => $entry['probability']
						)
					);
//_debug_array($request);
					$sorequest->add($request, $values_attribute = array());
				}
			}
//		die();

			$this->_db->transaction_commit();
		}

		public function delete($id)
		{
			$id = (int) $id;
			$this->_db->transaction_begin();

			try
			{
				$this->_db->Exception_On_Error = true;
				$this->_db->query("DELETE FROM fm_condition_survey WHERE id={$id}",__LINE__,__FILE__);
				$this->_db->query("DELETE FROM fm_request WHERE condition_survey_id={$id}",__LINE__,__FILE__);
				$this->_db->Exception_On_Error = false;
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			$this->_db->transaction_commit();
		}
	}
