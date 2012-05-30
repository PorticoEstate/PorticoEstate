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
	* @subpackage helpdesk
 	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */

	class property_soexportentity
	{
		var $uicols_related = array();
		var $acl_location = '.entity.1.11';
		var $entity_id = 1;
		var $cat_id = 11;
		public $total_records	= 0;
		public $sum_budget		= 0;
		public $sum_actual_cost	= 0;
		protected $type = 'entity';

		public $soap_functions = array
			(
				'read' => array(
					'in'  => array('array'),
					'out' => array('array')
				)
			);


		public $xmlrpc_methods = array
			(
				array
				(
					'name'       => 'read',
					'decription' => 'Get list of meter'
				)
			);

		public $public_functions = array
		(
			'read'			=> true
		);

		function __construct()
		{
			$this->account		= (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->historylog	= CreateObject('property.historylog','tts');
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->like 		= & $this->db->like;
			$this->join 		= & $this->db->join;
			$this->left_join 	= & $this->db->left_join;
			$this->dateformat 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$this->acl 			= & $GLOBALS['phpgw']->acl;
			$this->type			= 'entity';
		}


		function list_methods($_type='xmlrpc')
		{
			/*
			  This handles introspection or discovery by the logged in client,
			  in which case the input might be an array.  The server always calls
			  this function to fill the server dispatch map using a string.
			 */
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
			case 'xmlrpc':
				$xml_functions = array(
					'read' => array(
						'function'  => 'read',
						'signature' => array(array(xmlrpcArray,xmlrpcArray)),
						'docstring' => 'Get list of meters'
					),
				);
				return $xml_functions;
				break;
			case 'soap':
				return $this->soap_functions;
				break;
			default:
				return array();
				break;
			}
		}


		function read($data = array())
		{
			$this->entity_id	= isset($data['entity_id']) && $data['entity_id'] ? $data['entity_id'] : $this->entity_id;
			$this->cat_id 		= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : $this->cat_id;
			$acl_location		= ".entity.{$this->entity_id}.{$this->cat_id}";
			
			if(!$this->acl->check($acl_location, PHPGW_ACL_READ, 'property'))
			{
				return array('error' => 'sorry: no access to this function');
			}
			
			$soentity 			= CreateObject('property.soentity',$this->entity_id,$this->cat_id);
			$soentity->type		= $this->type;
			

			if(isset($this->allrows))
			{
				$data['allrows'] = true;
			}

			$custom	= createObject('phpgwapi.custom_fields');
			$attrib_data = $custom->find('property',$this->acl_location, 0, '','','',true, true);

			$attrib_filter = array();
			if($attrib_data)
			{
				foreach ( $attrib_data as $attrib )
				{
					if($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'R')
					{
						if($_attrib_filter_value = phpgw::get_var($attrib['column_name'], 'int'))
						{
							$attrib_filter[] = "fm_{$this->type}_{$this->entity_id}_{$this->cat_id}.{$attrib['column_name']} = '{$_attrib_filter_value}'";
						}
					}
					else if($attrib['datatype'] == 'CH')
					{
						if($_attrib_filter_value = phpgw::get_var($attrib['column_name'], 'int'))
						{
							$attrib_filter[] = "fm_{$this->type}_{$this->entity_id}_{$this->cat_id}.{$attrib['column_name']} {$GLOBALS['phpgw']->db->like} '%,{$_attrib_filter_value},%'";
						}
					}
				}
			}

			$criteria = array
			(
				'start'				=> isset($data['start']) && $data['start'] ? (int)$data['start'] : 0,
				'query'				=> isset($data['query']) ? $data['query'] : '',
				'sort'				=> isset($data['sort']) ? $data['sort'] : '',
				'order'				=> isset($data['order']) ? $data['order'] : '',
				'filter'			=> isset($data['filter']) ? $data['filter'] : '',
				'cat_id'			=> $this->cat_id,
				'district_id'		=> isset($data['district_id']) && $data['district_id'] ? (int)$data['district_id'] : 0,
				'lookup'			=> isset($data['lookup'])?$data['lookup']:'',
				'allrows'			=> isset($data['allrows'])?$data['allrows']:'',
				'entity_id'			=> (int)$this->entity_id,
				'cat_id'			=> (int)$this->cat_id,
				'status'			=> isset($data['status']) ? $data['status'] : '',
				'start_date'		=> phpgwapi_datetime::date_to_timestamp($data['start_date']),
				'end_date'			=> phpgwapi_datetime::date_to_timestamp($data['end_date']),
				'dry_run'			=> $data['dry_run'],
				'type'				=> $data['type'],
				'location_code' 	=> isset($data['location_code']) ? $data['location_code'] : '',
				'criteria_id' 		=> $data['criteria_id'],
				'attrib_filter' 	=> $attrib_filter,
				'p_num'				=> $this->p_num,
				'custom_condition'	=> $data['custom_condition']
			);

			$values = $soentity->read($criteria);

			$solocation 	= CreateObject('property.solocation');
			$custom 		= createObject('property.custom_fields');

			$_values['attributes'] = $custom->find('property','.location.1', 0, '', 'ASC', 'attrib_sort', true, true);

			if( isset($data['get_location_info']) && $data['get_location_info'])
			{
				foreach($values as &$entry)
				{
	//				$entry['address'] = utf8_decode($entry['address']);
	//				$entry['user'] = utf8_decode($entry['user_id']);
					$__values = $solocation->read_single($entry['loc1'],$_values);
	//				$entry['location_data'] = $solocation->read_single($entry['loc1'],$_values);
					$entry['location_data'] = $custom->prepare($__values, 'property',".location.1", true);
				}
			}

			$resultset = array
			(
				'total_records' => $soentity->total_records,
				'values'		=> $values
			);

//_debug_array($resultset);
			return $resultset;
		}
	}
