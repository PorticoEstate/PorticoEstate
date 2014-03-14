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
	* @subpackage entity
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soentity
	{
		var $entity_id;
		var $cat_id;
		var $total_records = 0;
		var $uicols;
		var $cols_extra;
		var $cols_return_lookup;
		var $type = 'entity';
		protected $sql;


		protected $type_app = array
			(
				'entity'	=> 'property',
				'catch'		=> 'catch'
			);

		function __construct($entity_id='',$cat_id='')
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->custom 		= createObject('property.custom_fields');
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->db2          = clone($this->db);
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
			$this->entity_id	= $entity_id;
			$this->cat_id		= $cat_id;
		}

		public function get_type_app()
		{
			return 	$this->type_app;
		}

		function select_status_list($entity_id,$cat_id)
		{
			if(!$entity_id || !$cat_id)
			{
				return;
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");

			$sql= "SELECT phpgw_cust_choice.id, phpgw_cust_choice.value FROM phpgw_cust_attribute"
				. " $this->join phpgw_cust_choice ON"
				. " phpgw_cust_attribute.location_id= phpgw_cust_choice.location_id AND"
				. " phpgw_cust_attribute.id= phpgw_cust_choice.attrib_id"
				. " WHERE phpgw_cust_attribute.column_name='status'"
				. " AND phpgw_cust_choice.location_id={$location_id} ORDER BY phpgw_cust_choice.id";

			$this->db->query($sql,__LINE__,__FILE__);

			$status = array();
			while ($this->db->next_record())
			{
				$status[] = array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> stripslashes($this->db->f('value'))
					);
			}
			return $status;
		}


		function get_list($data)
		{
			$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$filter			= isset($data['filter']) && $data['filter'] ? $data['filter'] : 'all';
			$query			= isset($data['query']) ? $data['query'] : '';
			$sort			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order			= isset($data['order']) ? $data['order'] : '';
			$cat_id			= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
			$district_id	= isset($data['district_id']) && $data['district_id'] ? $data['district_id'] : 0;
			$lookup			= isset($data['lookup']) ? $data['lookup'] : '';
			$allrows		= isset($data['allrows']) ? $data['allrows'] : '';
			$entity_id		= isset($data['entity_id']) ? $data['entity_id'] : '';
			$cat_id			= isset($data['cat_id']) ? $data['cat_id'] : '';
			$status			= isset($data['status']) ? $data['status'] : '';
			$start_date		= isset($data['start_date']) ? $data['start_date'] : '';
			$end_date		= isset($data['end_date']) ? $data['end_date'] : '';
			$dry_run		= isset($data['dry_run']) ? $data['dry_run'] : '';
			$this->type		= isset($data['type']) && $data['type'] ? $data['type'] : $this->type;
			$location_code	= isset($data['location_code']) ? $data['location_code'] : '';
			$criteria_id	= isset($data['criteria_id']) ? $data['criteria_id'] : '';
			$attrib_filter	= $data['attrib_filter'] ? $data['attrib_filter'] : array();
			$p_num			= isset($data['p_num']) ? $data['p_num'] : '';
			$custom_condition= isset($data['custom_condition']) ? $data['custom_condition'] : '';

			if(!$entity_id || !$cat_id || !$this->type)
			{
				return;
			}

			$grants 	= $GLOBALS['phpgw']->session->appsession('grants_entity_'.$entity_id.'_'.$cat_id,$this->type_app[$this->type]);

			if(!$grants)
			{
				$this->acl 	= & $GLOBALS['phpgw']->acl;
				$grants		= $this->acl->get_grants($this->type_app[$this->type],".{$this->type}.{$entity_id}.{$cat_id}");
				$GLOBALS['phpgw']->session->appsession('grants_entity_'.$entity_id.'_'.$cat_id, $this->type_app[$this->type], $grants);
			}

			$admin_entity	= CreateObject('property.soadmin_entity');
			$admin_entity->type = $this->type;

			$category = $admin_entity->read_single_category($entity_id,$cat_id);

			$entity_table = "fm_{$this->type}_{$entity_id}_{$cat_id}";


			if ($order)
			{
				switch($order)
				{
					case 'user_id':
		//				$ordermethod = " ORDER BY phpgw_accounts.account_lastname {$sort}";  // Don't work with LDAP.
						break;
					case 'loc1_name':
						$ordermethod = " ORDER BY fm_location1.loc1_name {$sort}";  // Don't work with LDAP.
						break;
					default:
						$ordermethod = " ORDER BY $entity_table.$order $sort";
				}
			}
			else
			{
				$ordermethod = " order by $entity_table.id DESC";
			}

			$where= 'WHERE';
			$filtermethod = '';

			$_config	= CreateObject('phpgwapi.config',$this->type_app[$this->type]);
			$_config->read();
			if(isset($_config->config_data['acl_at_location'])
				&& $_config->config_data['acl_at_location']
				&& $category['location_level'] > 0)
			{
				$access_location = $this->bocommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod = " WHERE {$entity_table}.loc1 in ('" . implode("','", $access_location) . "')";
				$where= 'AND';
			}

			unset($_config);

			if ($filter=='all')
			{
				if (is_array($grants))
				{
					foreach($grants as $user => $right)
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$filtermethod .= " $where ( $entity_table.user_id IN(" . implode(',',$public_user_list) . "))";

					$where= 'AND';
				}
			}
			else
			{
				$filtermethod = " $where $entity_table.user_id=$filter ";
				$where= 'AND';
			}
			$values = array();
			$name = 'title';
			$sql = "SELECT id, {$name} as name FROM {$entity_table} {$filtermethod}";

			$this->db->query($sql,__LINE__,__FILE__);
			while($this->db->next_record())
			{

				$values[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name', true)
				);
			}
			return $values;
		}


		/**
		 * Method for retreiving sublevels of a hierarchy.
		 *
		 * @param $data array array holding input parametres
		 * @return array of entities
		 */

		public function get_eav_list($data = array())
		{
			$start			= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$results		= isset($data['results']) && $data['results'] ? (int)$data['results'] : 0;
			$location_id  	= isset($data['location_id']) && $data['location_id'] ? (int)$data['location_id'] : 0;
			$conditions		= isset($data['conditions']) && $data['conditions'] ? $data['conditions'] : array();
			$location_code	= isset($data['location_code']) ? $data['location_code'] : '';
			$query			= isset($data['query']) ? $data['query'] : '';
			$allrows		= isset($data['allrows']) ? $data['allrows'] : '';

			if(!$location_id)
			{
				return array();
			}

			$_querymethod = array();
			$__querymethod = array();

//			$__querymethod = array("fm_bim_item.id = -1"); // block query waiting for conditions
			if($location_code)
			{
				$_querymethod = array("fm_bim_item.location_code {$this->like} '{$location_code}%'");
			}

			$attribute_table = 'phpgw_cust_attribute';

			foreach ($conditions as $condition)
			{
				$this->db->query("SELECT * FROM phpgw_cust_attribute WHERE location_id = {$location_id} AND id= " . (int) $condition['attribute_id']);
				$this->db->next_record();
				$attribute_name = $this->db->f('column_name');

				$attributes[$condition['attibute_id']]['name']						= $attribute_name;
				$attributes[$condition['attibute_id']]['datatype']					= $this->db->f('datatype');
				$attributes[$condition['attibute_id']]['get_list_function']			= $this->db->f('get_list_function',true);
				$attributes[$condition['attibute_id']]['get_list_function_input']	= $this->db->f('get_list_function_input') ? unserialize($this->db->f('get_list_function_input', true)) : '';
				$attributes[$condition['attibute_id']]['get_single_function']		= $this->db->f('get_single_function',true);
				$attributes[$condition['attibute_id']]['get_single_function_input']	= $this->db->f('get_single_function_input') ? unserialize($this->db->f('get_single_function_input', true)) : '';

				switch ($this->db->f('datatype'))
				{
					case 'I':
						switch($condition['operator'])
						{
							case '=':
							case 'equal':
							case 'eq':
								$_querymethod[]= "xmlexists('//{$attribute_name}[text() = ''{$condition['value']}'']' PASSING BY REF xml_representation)";
								break;
							case 'gt':
							case '>':
								$operator = '>';
								$_querymethod[]= "xmlexists('//{$attribute_name}[number() {$operator} ''{$condition['value']}'']' PASSING BY REF xml_representation)";
							break;
							case 'lt':
							case '<':
								$operator = '<';
								$_querymethod[]= "xmlexists('//{$attribute_name}[number() {$operator} ''{$condition['value']}'']' PASSING BY REF xml_representation)";
							break;
							default:
								throw new Exception('ERROR: Not a valid operator on conditions');
						}
						$__querymethod = array(); // remove block
						break;
					case 'CH':
						$__querymethod = array(); // remove block
						$_querymethod[]= "xmlexists('//{$attribute_name}[contains(.,'',{$condition['value']},'')]' PASSING BY REF xml_representation)";
						break;
					default:
						$_querymethod[]= "xmlexists('//{$attribute_name}[text() = ''{$condition['value']}'']' PASSING BY REF xml_representation)";
						$__querymethod = array(); // remove block
				}
			}

			$querymethod = '';

			$where = 'AND';
			$_querymethod = array_merge($__querymethod, $_querymethod);
			if ($_querymethod)
			{
				$querymethod = " $where (" . implode(' AND ',$_querymethod) . ')';
				unset($_querymethod);
			}
			$sql = "SELECT id, location_code, p_location_id, p_id, xml_representation FROM fm_bim_item WHERE location_id = {$location_id} $querymethod";

			$sql_cnt = "SELECT count(id) as cnt FROM fm_bim_item WHERE location_id = {$location_id} $querymethod";

			$this->db->query($sql_cnt,__LINE__,__FILE__);
			$this->db->next_record();
			unset($sql_cnt);

			$this->total_records = $this->db->f('cnt');

			$ordermethod = '';
			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start,__LINE__,__FILE__,$results);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$items = array();
			$j=0;
			while ($this->db->next_record())
			{
				$xmldata = $this->db->f('xml_representation');


				$xml = new DOMDocument('1.0', 'utf-8');
				$xml->loadXML($xmldata);

				foreach($attributes as $attrib_id => $field)
				{
					if(!$value = $xml->getElementsByTagName($field['name'])->item(0)->nodeValue)
					{
						$value = $this->db->f($field['name'],true);
					}
					$dataset[$j][$field['name']] = array
					(
						'value'						=> $value,
						'datatype'					=> $field['datatype'],
						'attrib_id'					=> $attrib_id,
						'get_list_function' 		=> $field['get_list_function'],
						'get_list_function_input'	=> $field['get_list_function_input'],
						'get_single_function' 		=> $field['get_single_function'],
						'get_single_function_input'	=> $field['get_single_function_input']
					);
				}

				$dataset[$j]['id'] = array
					(
						'value'		=> $this->db->f('id'),
						'datatype'	=> false,
						'attrib_id'	=> false,
					);
				$dataset[$j]['location_id'] = array
					(
						'value'		=> $location_id,
						'datatype'	=> false,
						'attrib_id'	=> false
					);

				$dataset[$j]['p_id'] = array
					(
						'value'		=> $this->db->f('p_id'),
						'datatype'	=> false,
						'attrib_id'	=> false,
					);
				$dataset[$j]['p_location_id'] = array
					(
						'value'		=> $this->db->f('p_location_id'),
						'datatype'	=> false,
						'attrib_id'	=> false,
					);

				$dataset[$j]['location_code'] = array
					(
						'value'		=> $this->db->f('location_code'),
						'datatype'	=> false,
						'attrib_id'	=> false,
					);

				$j++;
			}

			foreach ($dataset as &$entry)
			{
				$entry['short_description'] = array
				(
					'value'		=> $this->get_short_description(array('location_id' => $location_id, 'id' => $entry['id']['value'] ) ),
					'datatype'	=> false,
					'attrib_id'	=> false,
				);

			}

			$values = $this->custom->translate_value($dataset, $location_id);
			return $values;
		}


		protected function read_eav($data)
		{
			$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$results		= isset($data['results']) && $data['results'] ? $data['results'] : 0;
			$filter			= isset($data['filter']) && $data['filter'] ? $data['filter'] : 'all';
			$query			= isset($data['query']) ? $data['query'] : '';
			$sort			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order			= isset($data['order']) && $data['order'] ? $data['order'] : 'id';
			$cat_id			= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
			$district_id	= isset($data['district_id']) && $data['district_id'] ? $data['district_id'] : 0;
			$part_of_town_id= isset($data['part_of_town_id']) && $data['part_of_town_id'] ? $data['part_of_town_id'] : 0;
			$lookup			= isset($data['lookup']) ? $data['lookup'] : '';
			$allrows		= isset($data['allrows']) ? $data['allrows'] : '';
			$entity_id		= isset($data['entity_id']) ? $data['entity_id'] : '';
			$cat_id			= isset($data['cat_id']) ? $data['cat_id'] : '';
			$status			= isset($data['status']) ? $data['status'] : '';
			$start_date		= isset($data['start_date']) ? $data['start_date'] : '';
			$end_date		= isset($data['end_date']) ? $data['end_date'] : '';
			$dry_run		= isset($data['dry_run']) ? $data['dry_run'] : '';
			$this->type		= isset($data['type']) && $data['type'] ? $data['type'] : $this->type;
			$location_code	= isset($data['location_code']) ? $data['location_code'] : '';
			$criteria_id	= isset($data['criteria_id']) ? $data['criteria_id'] : '';
			$attrib_filter	= $data['attrib_filter'] ? $data['attrib_filter'] : array();
			$p_num			= isset($data['p_num']) ? $data['p_num'] : '';
			$custom_condition= isset($data['custom_condition']) ? $data['custom_condition'] : '';
			$control_registered= isset($data['control_registered']) ? $data['control_registered'] : '';
			$control_id		= isset($data['control_id']) && $data['control_id'] ? $data['control_id'] : 0;

			if(!$entity_id || !$cat_id)
			{
				return array();
			}

			$grants 	= $GLOBALS['phpgw']->session->appsession("grants_entity_{$entity_id}_{$cat_id}",$this->type_app[$this->type]);

			if(!$grants)
			{
				$this->acl 	= & $GLOBALS['phpgw']->acl;
				$this->acl->set_account_id($this->account);
				$grants		= $this->acl->get_grants($this->type_app[$this->type],".{$this->type}.{$entity_id}.{$cat_id}");
				$GLOBALS['phpgw']->session->appsession("grants_entity_{$entity_id}_{$cat_id}", $this->type_app[$this->type], $grants);
			}

			$admin_entity	= CreateObject('property.soadmin_entity');
			$admin_entity->type = $this->type;

			$category = $admin_entity->read_single_category($entity_id,$cat_id);

			$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");
			$entity_table = 'fm_bim_item';
			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';
			$attribute_filter = " location_id = {$location_id}";



			$this->get_cols($category,$entity_id,$cat_id,$lookup,$location_id);


			if (!$cat_id > 0)
			{
				return;
			}

			//_debug_array($cols_return_extra);

			$filtermethod = "WHERE fm_bim_type.location_id = {$location_id}";
			$where= 'AND';

			$_config	= CreateObject('phpgwapi.config',$this->type_app[$this->type]);
			$_config->read();
			if(isset($_config->config_data['acl_at_location'])
				&& $_config->config_data['acl_at_location']
				&& $category['location_level'] > 0)
			{
				$access_location = $this->bocommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod .= " $where {$entity_table}.loc1 IN ('" . implode("','", $access_location) . "')";
				$where= 'AND';
			}

			$bypass_acl_at_entity = false;
			if(isset($_config->config_data['bypass_acl_at_entity'])
				&& is_array($_config->config_data['bypass_acl_at_entity'])
				&& in_array($entity_id, $_config->config_data['bypass_acl_at_entity']))
			{
				$bypass_acl_at_entity = true;
			}

			unset($_config);

			if ($filter=='all')
			{
				if (is_array($grants) && !$bypass_acl_at_entity)
				{
					foreach($grants as $user => $right)
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$filtermethod .= " $where ( $entity_table.user_id IN(" . implode(',',$public_user_list) . "))";

					$where= 'AND';
				}
			}
			else
			{
				$filtermethod .= " $where $entity_table.user_id=$filter ";
				$where= 'AND';
			}

			if ($status)
			{
	//			$filtermethod .= " $where $entity_table.status='$status' ";
	//			$where= 'AND';
			}

			if ($district_id > 0 && $category['location_level'] && !$part_of_town_id)
			{
				$filtermethod .= " $where  fm_part_of_town.district_id='$district_id' ";
				$where = 'AND';
			}
			else if ($part_of_town_id > 0 && $category['location_level'])
			{
				$filtermethod .= " $where fm_part_of_town.part_of_town_id='$part_of_town_id' ";
				$where = 'AND';
			}

			if ($start_date)
			{
				$filtermethod .= " $where $entity_table.entry_date >= $start_date AND $entity_table.entry_date <= $end_date ";
				$where= 'AND';
			}

			if ($location_code)
			{
				$filtermethod .= " $where $entity_table.location_code $this->like '$location_code%'";
				$where= 'AND';
			}

			if ($attrib_filter)
			{
				$filtermethod .= " $where " . implode(' AND ', $attrib_filter);
				$where= 'AND';
			}

			if ($custom_condition)
			{
				$filtermethod .= " {$where} {$custom_condition}";
				$where= 'AND';
			}

			if ($p_num)
			{
				$filtermethod .= " $where $entity_table.p_id='$p_num'";
				$where= 'AND';
			}

			$_querymethod = array();
			$__querymethod = array();
			$_joinmethod_datatype = array();
			$_joinmethod_datatype_custom = array();
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$query = str_replace(",",'.',$query);
				$_int_query = (int) $query;
				if(stristr($query, '.'))
				{
					$query=explode(".",$query);
					$_querymethod[] = "($entity_table.location_code $this->like '" . $query[0] . "%' AND $entity_table.location_code $this->like '%" . $query[1] . "')";
				}
				else
				{
					if(!$criteria_id)
					{
						$_querymethod[] = "( {$entity_table}.location_code {$this->like} '%{$query}%' OR {$entity_table}.id = {$_int_query} OR address {$this->like} '%{$query}%')";
//						$where= 'OR';
					}
					else
					{
						$__querymethod = array("{$entity_table}.id = -1"); // block query waiting for criteria
					}
					//_debug_array($__querymethod);

					$this->db->query("SELECT * FROM $attribute_table WHERE $attribute_filter AND search='1'");

					while ($this->db->next_record())
					{
						switch ($this->db->f('datatype'))
						{
							case 'V':
							case 'email':
							case 'T':
								if(!$criteria_id)
								{
									$_querymethod[]= "xmlexists('//" . $this->db->f('column_name') . "[contains(.,''$query'')]' PASSING BY REF xml_representation)";
									$__querymethod = array(); // remove block
								}
								break;
							case 'CH':
								if(!$criteria_id)
								{
									// from filter
									$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " {$this->like} '%,{$query},%'";
									$__querymethod = array(); // remove block

									// from text-search
									$_filter_choise = "WHERE (phpgw_cust_choice.location_id =" . (int)$this->db->f('location_id')
										." AND phpgw_cust_choice.attrib_id =" . (int)$this->db->f('id')
										." AND phpgw_cust_choice.value {$this->like} '%{$query}%')";

									$this->db2->query("SELECT phpgw_cust_choice.id FROM phpgw_cust_choice {$_filter_choise}",__LINE__,__FILE__);
									while ($this->db2->next_record())
									{
										$_querymethod[]= "xmlexists('//" . $this->db->f('column_name') . "[contains(.,''," . $this->db2->f('id') . ",'')]' PASSING BY REF xml_representation)";
									}
								}
								break;
							case 'R':
							case 'LB':
								if(!$criteria_id)
								{
									$_filter_choise = "WHERE (phpgw_cust_choice.location_id =" . (int)$this->db->f('location_id')
										." AND phpgw_cust_choice.attrib_id =" . (int)$this->db->f('id')
										." AND phpgw_cust_choice.value {$this->like} '%{$query}%')";

									$this->db2->query("SELECT phpgw_cust_choice.id FROM phpgw_cust_choice {$_filter_choise}",__LINE__,__FILE__);
									$__filter_choise = array();
									while ($this->db2->next_record())
									{
										$_querymethod[]= "xmlexists('//" . $this->db->f('column_name') . "[text() = ''" . (int)$this->db2->f('id') . "'']' PASSING BY REF xml_representation)";
									}
									$__querymethod = array(); // remove block
								}
								break;
							case 'I':
								if(ctype_digit($query) && !$criteria_id)
								{
									$_querymethod[]= "xmlexists('//" . $this->db->f('column_name') . "[text() = ''" . (int)$query . "'']' PASSING BY REF xml_representation)";
									$__querymethod = array(); // remove block
								}
								break;
							case 'VENDOR':
								if($criteria_id == 'vendor')
								{
									$this->db2->query("SELECT id FROM fm_vendor WHERE fm_vendor.org_name {$this->like} '%{$query}%'",__LINE__,__FILE__);
									$__filter_choise = array();
									while ($this->db2->next_record())
									{
										$_querymethod[]= "xmlexists('//" . $this->db->f('column_name') . "[text() = ''" . (int)$this->db2->f('id') . "'']' PASSING BY REF xml_representation)";
									}

									$__querymethod = array(); // remove block
								}
								break;
							case 'AB':
								if($criteria_id == 'ab')
								{
									$this->db2->query("SELECT person_id as id FROM phpgw_contact_person WHERE phpgw_contact_person.first_name {$this->like} '%{$query}%' OR phpgw_contact_person.last_name {$this->like} '%{$query}%'",__LINE__,__FILE__);
									$__filter_choise = array();
									while ($this->db2->next_record())
									{
										$_querymethod[]= "xmlexists('//" . $this->db->f('column_name') . "[text() = ''" . (int)$this->db2->f('id') . "'']' PASSING BY REF xml_representation)";
									}

									$__querymethod = array(); // remove block
								}
								break;
							case 'ABO':
								if($criteria_id == 'abo')
								{
									$this->db2->query("SELECT org_id as id FROM phpgw_contact_org WHERE name {$this->like} '%{$query}%'",__LINE__,__FILE__);
									$__filter_choise = array();
									while ($this->db2->next_record())
									{
										$_querymethod[]= "xmlexists('//" . $this->db->f('column_name') . "[text() = ''" . (int)$this->db2->f('id') . "'']' PASSING BY REF xml_representation)";
									}
									$__querymethod = array(); // remove block
								}
								break;
							default:
								if(!$criteria_id)
								{
									$_querymethod[]= "xmlexists('//" . $this->db->f('column_name') . "[text() = ''$query'']' PASSING BY REF xml_representation)";
									$__querymethod = array(); // remove block
								}
						}
					}
				}
			}

			$sql = "SELECT fm_bim_item.* __XML-ORDER__ FROM fm_bim_item {$this->join} fm_bim_type ON (fm_bim_item.type = fm_bim_type.id)";
			if($control_registered)
			{
				$sql .= "{$this->join} controller_control_component_list ON (fm_bim_item.id = controller_control_component_list.component_id  AND controller_control_component_list.location_id = fm_bim_type.location_id)";
				$sql_cnt_control_fields = ',control_id ';
				$filtermethod .= " $where  controller_control_component_list.control_id = $control_id";
				$where = 'AND';
			}
			else
			{
				$sql_cnt_control_fields = '';
			}

			if(isset($category['location_level']) && $category['location_level'])
			{
				$sql .= "{$this->join} fm_location1 ON (fm_bim_item.loc1 = fm_location1.loc1)";
				$sql .= "{$this->join} fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id)";
				$sql .= "{$this->join} fm_owner ON (fm_location1.owner_id = fm_owner.id)";
			}

			$_joinmethod_datatype = array_merge($_joinmethod_datatype, $_joinmethod_datatype_custom);
			foreach($_joinmethod_datatype as $_joinmethod)
			{
				$sql .= $_joinmethod;
			}

//_debug_array($sql);
			$querymethod = '';

			$_querymethod = array_merge($__querymethod, $_querymethod);
			if ($_querymethod)
			{
				$querymethod = " $where (" . implode (' OR ',$_querymethod) . ')';
				unset($_querymethod);
			}

//			$filtermethod .= "AND xmlexists('//location_code[text() = ''5002-02'']' PASSING BY REF xml_representation)";

			$sql .= " $filtermethod $querymethod";

			$_sql = str_replace('__XML-ORDER__', '', $sql);

//			$cache_info = phpgwapi_cache::session_get($this->type_app[$this->type],"{$location_id}_listing_metadata");

			if (!isset($cache_info['sql_hash']) || $cache_info['sql_hash'] != md5($_sql))
			{
				$cache_info = array();
			}
//_debug_array($_sql);die();
//			if(!$cache_info)
			{
				$sql_cnt = "SELECT DISTINCT fm_bim_item.id {$sql_cnt_control_fields}" . substr($_sql,strripos($_sql,'FROM'));
				$sql2 = "SELECT count(*) as cnt FROM ({$sql_cnt}) as t";

				$this->db->query($sql2,__LINE__,__FILE__);
				$this->db->next_record();
				unset($sql2);
				unset($sql_cnt);

				$cache_info = array
				(
					'total_records'		=> $this->db->f('cnt'),
					'sql_hash'			=> md5($_sql)
				);
				phpgwapi_cache::session_set($this->type_app[$this->type],"{$location_id}_listing_metadata",$cache_info);
			}

			$this->total_records	= $cache_info['total_records'];


			if($dry_run)
			{
				return array();
			}

			$ordermethod = '';
			$xml_order = '';
			if ($order)
			{
				switch($order)
				{
					case 'user_id':
		//				$ordermethod = " ORDER BY phpgw_accounts.account_lastname {$sort}";  // Don't work with LDAP.
						break;
					case 'loc1_name':
						$ordermethod = " ORDER BY fm_location1.loc1_name {$sort}";
						break;
					case 'num':
					case 'id':
						$ordermethod = " ORDER BY {$entity_table}.id {$sort}";
						break;
					default:
						$xml_order = ',cast (_order_field[1] as text) as _order_field_text';
						$sql = str_replace('FROM fm_bim_item', "FROM (SELECT fm_bim_item.*, xpath('$order/text()', xml_representation) as _order_field FROM fm_bim_item", $sql);
						$sql .= ") as fm_bim_item ORDER BY _order_field_text {$sort}";
				}
			}
			else
			{
				$ordermethod = "  ORDER BY {$entity_table}.id DESC";
			}

			$sql = str_replace('__XML-ORDER__', $xml_order, $sql);
//_debug_array($sql);

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start,__LINE__,__FILE__,$results);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$j=0;

			$uicols = $this->uicols;
			$cols_return = $uicols['name'];
			$dataset = array();

			while ($this->db->next_record())
			{
				$xmldata = $this->db->f('xml_representation');
				$xml = new DOMDocument('1.0', 'utf-8');
				$xml->loadXML($xmldata);

				foreach($cols_return as $key => $field)
				{
					if(!$value = $xml->getElementsByTagName($field)->item(0)->nodeValue)
					{
						$value = $this->db->f($field,true);
					}
					$dataset[$j][$field] = array
					(
						'value'						=> $value,
						'datatype'					=> $uicols['datatype'][$key],
						'attrib_id'					=> $uicols['cols_return_extra'][$key]['attrib_id'],
						'get_list_function' 		=> $uicols['get_list_function'][$key],
						'get_list_function_input'	=> $uicols['get_list_function_input'][$key],
						'get_single_function' 		=> $uicols['get_single_function'][$key],
						'get_single_function_input'	=> $uicols['get_single_function_input'][$key]
					);
				}


				$dataset[$j]['num']['value'] = $dataset[$j]['id']['value'];

				$dataset[$j]['entity_id'] = array
					(
						'value'		=> $entity_id,
						'datatype'	=> false,
						'attrib_id'	=> false,
					);
				$dataset[$j]['cat_id'] = array
					(
						'value'		=> $cat_id,
						'datatype'	=> false,
						'attrib_id'	=> false
					);

				$dataset[$j]['_type'] = array
					(
						'value'		=> $this->type,
						'datatype'	=> false,
						'attrib_id'	=> false
					);

				if($lookup)
				{
					$dataset[$j]["entity_num_{$entity_id}"] = array
						(
							'value'		=> $this->db->f('id'),
							'datatype'	=> false,
							'attrib_id'	=> false,
						);

					$dataset[$j]["entity_cat_name_{$entity_id}"] = array
						(
							'value'		=> $category['name'],
							'datatype'	=> false,
							'attrib_id'	=> false
						);
					$dataset[$j]["entity_id_{$entity_id}"] = array
						(
							'value'		=> $entity_id,
							'datatype'	=> false,
							'attrib_id'	=> false
						);
					$dataset[$j]["cat_id_{$entity_id}"] = array
						(
							'value'		=> $cat_id,
							'datatype'	=> false,
							'attrib_id'	=> false
						);
				}
				$j++;
			}

			$values = $this->custom->translate_value($dataset, $location_id);

			return $values;
		}


		function get_cols($category,$entity_id,$cat_id,$lookup,$location_id)
		{

			if($category['is_eav'])
			{
				$entity_table = "fm_bim_item";
			}
			else
			{
				$entity_table = "fm_{$this->type}_{$entity_id}_{$cat_id}";
			}

			$this->uicols 	= $this->bocommon->fm_cache("uicols_{$this->type}_{$entity_id}_{$cat_id}_{$lookup}");

			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';
			$attribute_filter = " location_id = {$location_id}";

			if(!$this->uicols)
			{

				$cols_return_extra	= array();
				$cols_return		= array();
				$uicols				= array();
				$cols				= "{$entity_table}.*";

				$cols_return[]				= 'location_code';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'location_code';
				$uicols['descr'][]			= 'dummy';
				$uicols['statustext'][]		= 'dummy';
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['sortable'][]		= true;
				$uicols['exchange'][]		= false;
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';

				$cols_return[] 				= 'num';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'num';
				$uicols['descr'][]			= lang('ID');
				$uicols['statustext'][]		= lang('ID');
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['sortable'][]		= true;
				$uicols['exchange'][]		= false;
				$uicols['formatter'][]		= $lookup ? '' : 'linktToEntity';
				$uicols['classname'][]		= '';

				$cols_return[] 				= 'id';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'id';
				$uicols['descr'][]			= false;
				$uicols['statustext'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['sortable'][]		= false;
				$uicols['exchange'][]		= false;
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';

				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'entity_id';
				$uicols['descr'][]			= false;
				$uicols['statustext'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['sortable'][]		= false;
				$uicols['exchange'][]		= false;
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';

				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'cat_id';
				$uicols['descr'][]			= false;
				$uicols['statustext'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['sortable'][]		= false;
				$uicols['exchange'][]		= false;
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';

				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= '_type';
				$uicols['descr'][]			= false;
				$uicols['statustext'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';
				$uicols['sortable'][]		= false;
				$uicols['exchange'][]		= false;
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';


				if($lookup)
				{
					$cols .= ',num as entity_num_' . $entity_id;
					$cols_return[] = 'entity_num_' . $entity_id;
					$uicols['input_type'][]		= 'hidden';
					$uicols['name'][]			= 'entity_num_' . $entity_id;
					$uicols['descr'][]			= 'dummy';
					$uicols['statustext'][]		= 'dummy';
					$uicols['align'][] 			= '';
					$uicols['datatype'][]		= '';
					$uicols['sortable'][]		= false;
					$uicols['exchange'][]		= false;
					$uicols['formatter'][]		= '';
					$uicols['classname'][]		= '';
				}

		//		$cols .= ", {$entity_table}.user_id";
				$cols_return[] 				= 'user_id';

				// Don't work with LDAP - where phpgw_accounts is empty
				//			$joinmethod = " $this->join phpgw_accounts ON ($entity_table.user_id = phpgw_accounts.account_id))";
				//			$paranthesis ='(';

				$this->sql = $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols_return'=>$cols_return,'cols'=>$cols,
					'uicols'=>$uicols,'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,'query'=>$query,'lookup'=>$lookup,'location_level'=>$category['location_level']));


				if(!$category['is_eav'])
				{
					$this->bocommon->fm_cache("sql_{$this->type}_{$entity_id}_{$cat_id}_{$lookup}", $this->sql);
				}

				$this->bocommon->fm_cache("uicols_{$this->type}_{$entity_id}_{$cat_id}_{$lookup}", $this->bocommon->uicols);
				$this->bocommon->fm_cache("cols_return_{$this->type}_{$entity_id}_{$cat_id}_{$lookup}", $this->bocommon->cols_return);
				$this->bocommon->fm_cache("cols_return_lookup_{$this->type}_{$entity_id}_{$cat_id}_{$lookup}", $this->bocommon->cols_return_lookup);
				$this->bocommon->fm_cache("cols_extra_{$this->type}_{$entity_id}_{$cat_id}_{$lookup}", $this->bocommon->cols_extra);

				$this->uicols				= $this->bocommon->uicols;
				$this->cols_return_lookup	= $this->bocommon->cols_return_lookup;
				$this->cols_extra			= $this->bocommon->cols_extra;
			}
			else
			{

				if(!$category['is_eav'])
				{
					$this->sql = $this->bocommon->fm_cache("sql_{$this->type}_{$entity_id}_{$cat_id}_{$lookup}");
				}

				$this->cols_return_lookup 	= $this->bocommon->fm_cache("cols_return_lookup_{$this->type}_{$entity_id}_{$cat_id}_{$lookup}");
				$this->cols_extra			= $this->bocommon->fm_cache("cols_extra_{$this->type}_{$entity_id}_{$cat_id}_{$lookup}");
			}

			$uicols = &$this->uicols;

			if ($cat_id > 0)
			{
				//-------------------

				$user_columns = isset($GLOBALS['phpgw_info']['user']['preferences'][$this->type_app[$this->type]]['entity_columns_'.$entity_id.'_'.$cat_id])?$GLOBALS['phpgw_info']['user']['preferences'][$this->type_app[$this->type]]['entity_columns_'.$entity_id.'_'.$cat_id]:array();

				$_user_columns = array();
				foreach ($user_columns as $user_column_id)
				{
					if(ctype_digit($user_column_id))
					{
						$_user_columns[] = $user_column_id;
					}
				}
				$user_column_filter = '';
				if ($_user_columns)
				{
					$user_column_filter = " OR ({$attribute_filter} AND id IN (" . implode(',',$_user_columns) .'))';
				}

				$this->db->query("SELECT * FROM {$attribute_table} WHERE list=1 AND {$attribute_filter} {$user_column_filter} ORDER BY group_id, attrib_sort ASC");

				$i	= count($uicols['name']);
				while ($this->db->next_record())
				{
					$uicols['input_type'][]						= 'text';
					$uicols['name'][]							= $this->db->f('column_name');
					$uicols['descr'][]							= $this->db->f('input_text');
					$uicols['statustext'][]						= $this->db->f('statustext');
					$uicols['datatype'][$i]						= $this->db->f('datatype');
					$uicols['get_list_function'][$i]			= $this->db->f('get_list_function',true);
					$uicols['get_list_function_input'][$i]		= $this->db->f('get_list_function_input') ? unserialize($this->db->f('get_list_function_input', true)) : '';
					$uicols['get_single_function'][$i]			= $this->db->f('get_single_function',true);
					$uicols['get_single_function_input'][$i]	= $this->db->f('get_single_function_input') ? unserialize($this->db->f('get_single_function_input', true)) : '';
					$uicols['sortable'][$i]						= true;
					$uicols['exchange'][$i]						= false;
					$uicols['formatter'][$i]					= '';
					$uicols['classname'][$i]					= '';

					$uicols['cols_return_extra'][$i] = array
						(
							'name'	=> $this->db->f('column_name'),
							'datatype'	=> $this->db->f('datatype'),
							'attrib_id'	=> $this->db->f('id')
						);

					$i++;
				}

				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'entry_date';
				$uicols['descr'][]			= lang('entry date');
				$uicols['statustext'][]		= lang('entry date' );
				$uicols['datatype'][]		= 'timestamp';
				$uicols['sortable'][]		= true;
				$uicols['exchange'][]		= false;
				$uicols['formatter'][]	= '';
				$uicols['classname'][]	= '';

				$uicols['cols_return_extra'][$i] = array
					(
						'name'		=> 'entry_date',
						'datatype'	=> 'timestamp',
					);
			}

		}


		function read($data)
		{
			$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$results		= isset($data['results']) && $data['results'] ? $data['results'] : 0;
			$filter			= isset($data['filter']) && $data['filter'] ? $data['filter'] : 'all';
			$query			= isset($data['query']) ? $data['query'] : '';
			$sort			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order			= isset($data['order']) ? $data['order'] : '';
			$cat_id			= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
			$district_id	= isset($data['district_id']) && $data['district_id'] ? $data['district_id'] : 0;
			$part_of_town_id= isset($data['part_of_town_id']) && $data['part_of_town_id'] ? $data['part_of_town_id'] : 0;
			$lookup			= isset($data['lookup']) ? $data['lookup'] : '';
			$allrows		= isset($data['allrows']) ? $data['allrows'] : '';
			$entity_id		= isset($data['entity_id']) ? $data['entity_id'] : '';
			$cat_id			= isset($data['cat_id']) ? $data['cat_id'] : '';
			$status			= isset($data['status']) ? $data['status'] : '';
			$start_date		= isset($data['start_date']) ? $data['start_date'] : '';
			$end_date		= isset($data['end_date']) ? $data['end_date'] : '';
			$dry_run		= isset($data['dry_run']) ? $data['dry_run'] : '';
			$this->type		= isset($data['type']) && $data['type'] ? $data['type'] : $this->type;
			$location_code	= isset($data['location_code']) ? $data['location_code'] : '';
			$criteria_id	= isset($data['criteria_id']) ? $data['criteria_id'] : '';
			$attrib_filter	= $data['attrib_filter'] ? $data['attrib_filter'] : array();
			$p_num			= isset($data['p_num']) ? $data['p_num'] : '';
			$custom_condition= isset($data['custom_condition']) ? $data['custom_condition'] : '';

			if(!$entity_id || !$cat_id)
			{
				return array();
			}

			$grants 	= $GLOBALS['phpgw']->session->appsession("grants_entity_{$entity_id}_{$cat_id}",$this->type_app[$this->type]);

			if(!$grants)
			{
				$this->acl 	= & $GLOBALS['phpgw']->acl;
				$this->acl->set_account_id($this->account);
				$grants		= $this->acl->get_grants($this->type_app[$this->type],".{$this->type}.{$entity_id}.{$cat_id}");
				$GLOBALS['phpgw']->session->appsession("grants_entity_{$entity_id}_{$cat_id}", $this->type_app[$this->type], $grants);
			}

			$admin_entity	= CreateObject('property.soadmin_entity');
			$admin_entity->type = $this->type;

			$category = $admin_entity->read_single_category($entity_id,$cat_id);

			$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");

			$this->get_cols($category,$entity_id,$cat_id,$lookup,$location_id);

			if($category['is_eav'])
			{
				return $this->read_eav($data);
			}

			$entity_table = "fm_{$this->type}_{$entity_id}_{$cat_id}";
			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';
			$attribute_filter = " location_id = {$location_id}";

			$sql = $this->sql;

			if (!$cat_id > 0)
			{
				return;
			}

			//_debug_array($cols_return_extra);

			if ($order)
			{
				switch($order)
				{
					case 'user_id':
		//				$ordermethod = " ORDER BY phpgw_accounts.account_lastname {$sort}";  // Don't work with LDAP.
						break;
					case 'loc1_name':
						$ordermethod = " ORDER BY fm_location1.loc1_name {$sort}";  // Don't work with LDAP.
						break;
					default:
						$metadata = $this->db->metadata($entity_table);
						if(isset($metadata[$order]))
						{
							$ordermethod = " ORDER BY $entity_table.$order $sort";
						}
				}
			}
			else
			{
				$ordermethod = " order by $entity_table.id DESC";
			}

			$where= 'WHERE';
			$filtermethod = '';

			$_config	= CreateObject('phpgwapi.config',$this->type_app[$this->type]);
			$_config->read();
			if(isset($_config->config_data['acl_at_location'])
				&& $_config->config_data['acl_at_location']
				&& $category['location_level'] > 0)
			{
				$access_location = $this->bocommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod = " WHERE {$entity_table}.loc1 in ('" . implode("','", $access_location) . "')";
				$where= 'AND';
			}

			$bypass_acl_at_entity = false;
			if(isset($_config->config_data['bypass_acl_at_entity'])
				&& is_array($_config->config_data['bypass_acl_at_entity'])
				&& in_array($entity_id, $_config->config_data['bypass_acl_at_entity']))
			{
				$bypass_acl_at_entity = true;
			}

			unset($_config);

			if ($filter=='all')
			{
				if (is_array($grants) && !$bypass_acl_at_entity)
				{
					foreach($grants as $user => $right)
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$filtermethod .= " $where ( $entity_table.user_id IN(" . implode(',',$public_user_list) . "))";

					$where= 'AND';
				}
			}
			else
			{
				$filtermethod = " $where $entity_table.user_id=$filter ";
				$where= 'AND';
			}

			if ($status)
			{
				$filtermethod .= " $where $entity_table.status='$status' ";
				$where= 'AND';
			}

			if ($district_id > 0 && $category['location_level'] && !$part_of_town_id)
			{
				$filtermethod .= " $where  fm_part_of_town.district_id='$district_id' ";
				$where = 'AND';
			}
			else if ($part_of_town_id > 0 && $category['location_level'])
			{
				$filtermethod .= " $where  fm_part_of_town.part_of_town_id='$part_of_town_id' ";
				$where = 'AND';
			}

			if ($start_date)
			{
				$filtermethod .= " $where $entity_table.entry_date >= $start_date AND $entity_table.entry_date <= $end_date ";
				$where= 'AND';
			}

			if ($location_code)
			{
				$filtermethod .= " $where $entity_table.location_code {$this->like} '$location_code%'";
				$where= 'AND';
				$query = '';
			}

			if ($attrib_filter)
			{
				$filtermethod .= " $where " . implode(' AND ', $attrib_filter);
				$where= 'AND';
			}

			if ($custom_condition)
			{
				$filtermethod .= " {$where} {$custom_condition}";
				$where= 'AND';
			}

			if ($p_num)
			{
				$filtermethod .= " $where $entity_table.p_num='$p_num'";
				$where= 'AND';
			}

			$_querymethod = array();
			$__querymethod = array();
			$_joinmethod_datatype = array();
			$_joinmethod_datatype_custom = array();
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$query = str_replace(",",'.',$query);
				if(stristr($query, '.'))
				{
					$query=explode(".",$query);
					$_querymethod[] = "($entity_table.location_code $this->like '" . $query[0] . "%' AND $entity_table.location_code $this->like '%" . $query[1] . "')";
				}
				else
				{
					if(!$criteria_id)
					{
						$_querymethod[] .= "( {$entity_table}.location_code {$this->like} '%{$query}%' OR {$entity_table}.num {$this->like} '%{$query}%' OR address {$this->like} '%{$query}%')";
//						$where= 'OR';
					}
					else
					{
						$__querymethod = array("{$entity_table}.id = -1"); // block query waiting for criteria
					}
					//_debug_array($__querymethod);

					$this->db->query("SELECT * FROM $attribute_table WHERE $attribute_filter AND search='1'");

					while ($this->db->next_record())
					{
						switch ($this->db->f('datatype'))
						{
							case 'V':
							case 'email':
							case 'T':
								if(!$criteria_id)
								{
									$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " {$this->like} '%{$query}%'";
									$__querymethod = array(); // remove block
								}
								break;
							case 'CH':
								if(!$criteria_id)
								{
									// from filter
									$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " {$this->like} '%,{$query},%'";
									$__querymethod = array(); // remove block

									// from text-search
									$_filter_choise = "WHERE (phpgw_cust_choice.location_id =" . (int)$this->db->f('location_id')
										." AND phpgw_cust_choice.attrib_id =" . (int)$this->db->f('id')
										." AND phpgw_cust_choice.value {$this->like} '%{$query}%')";

									$this->db2->query("SELECT phpgw_cust_choice.id FROM phpgw_cust_choice {$_filter_choise}",__LINE__,__FILE__);
									while ($this->db2->next_record())
									{
										$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " {$this->like} '%,". $this->db2->f('id') . ",%'";
									}
								}
								break;
							case 'R':
							case 'LB':
								if(!$criteria_id)
								{
									$_filter_choise = "WHERE (phpgw_cust_choice.location_id =" . (int)$this->db->f('location_id')
										." AND phpgw_cust_choice.attrib_id =" . (int)$this->db->f('id')
										." AND phpgw_cust_choice.value {$this->like} '%{$query}%')";

									$this->db2->query("SELECT phpgw_cust_choice.id FROM phpgw_cust_choice {$_filter_choise}",__LINE__,__FILE__);
									$__filter_choise = array();
									while ($this->db2->next_record())
									{
										$__filter_choise[] = $this->db2->f('id');
									}

									if($__filter_choise)
									{
										$_querymethod[]= "$entity_table." . $this->db->f('column_name') . ' IN (' . implode(',', $__filter_choise) . ')';
									}

									$__querymethod = array(); // remove block
								}
								break;
							case 'I':
								if(ctype_digit($query) && !$criteria_id)
								{
									$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " = " . (int)$query;
									$__querymethod = array(); // remove block
								}
								break;
							case 'VENDOR':
								if($criteria_id == 'vendor')
								{
									$_joinmethod_datatype[] = "{$this->join} fm_vendor ON ({$entity_table}." . $this->db->f('column_name') . " = fm_vendor.id AND fm_vendor.org_name {$this->like} '%{$query}%') ";
									$__querymethod = array(); // remove block
								}
								break;
							case 'AB':
								if($criteria_id == 'ab')
								{
									$_joinmethod_datatype[] = "{$this->join} phpgw_contact_person ON ({$entity_table}." . $this->db->f('column_name') . " = pphpgw_contact_person.person_id AND (phpgw_contact_person.first_name {$this->like} '%{$query}%' OR phpgw_contact_person.last_name {$this->like} '%{$query}%'))";
									$__querymethod = array(); // remove block
								}
								break;
							case 'ABO':
								if($criteria_id == 'abo')
								{
									$_joinmethod_datatype[] = "{$this->join} phpgw_contact_org ON ({$entity_table}." . $this->db->f('column_name') . " = phpgw_contact_org.org_id AND phpgw_contact_org.name {$this->like} '%{$query}%')";
									$__querymethod = array(); // remove block
								}
								break;
							default:
								if(!$criteria_id)
								{
									$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " = '{$query}'";
									$__querymethod = array(); // remove block
								}
						}
					}
				}
			}

			$_joinmethod_datatype = array_merge($_joinmethod_datatype, $_joinmethod_datatype_custom);
			foreach($_joinmethod_datatype as $_joinmethod)
			{
				$sql .= $_joinmethod;
			}

			$querymethod = '';

			$_querymethod = array_merge($__querymethod, $_querymethod);
			if ($_querymethod)
			{
				$querymethod = " $where (" . implode (' OR ',$_querymethod) . ')';
				unset($_querymethod);
			}

			$sql .= " $filtermethod $querymethod";



			$cache_info = phpgwapi_cache::session_get($this->type_app[$this->type],"{$entity_table}_listing_metadata");

			if (!isset($cache_info['sql_hash']) || $cache_info['sql_hash'] != md5($sql))
			{
				$cache_info = array();
			}

			if(!$cache_info)
			{
				$sql_cnt = "SELECT DISTINCT {$entity_table}.id " . substr($sql,strripos($sql,'FROM'));
				$sql2 = "SELECT count(*) as cnt FROM ({$sql_cnt}) as t";

				$this->db->query($sql2,__LINE__,__FILE__);
				$this->db->next_record();
				unset($sql2);
				unset($sql_cnt);

				$cache_info = array
				(
					'total_records'		=> $this->db->f('cnt'),
					'sql_hash'			=> md5($sql)
				);
				phpgwapi_cache::session_set($this->type_app[$this->type],"{$entity_table}_listing_metadata",$cache_info);
			}

			$this->total_records	= $cache_info['total_records'];


			if($dry_run)
			{
				return array();
			}

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__,$results);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$j=0;
			$uicols = $this->uicols;
			$cols_return = $uicols['name'];
			$dataset = array();
			while ($this->db->next_record())
			{
				foreach($cols_return as $key => $field)
				{
					$dataset[$j][$field] = array
					(
						'value'						=> $this->db->f($field),
						'datatype'					=> $uicols['datatype'][$key],
						'attrib_id'					=> $uicols['cols_return_extra'][$key]['attrib_id'],
						'get_list_function' 		=> $uicols['get_list_function'][$key],
						'get_list_function_input'	=> $uicols['get_list_function_input'][$key],
						'get_single_function' 		=> $uicols['get_single_function'][$key],
						'get_single_function_input'	=> $uicols['get_single_function_input'][$key]

					);
				}
				$dataset[$j]['entity_id'] = array
					(
						'value'		=> $entity_id,
						'datatype'	=> false,
						'attrib_id'	=> false
					);
				$dataset[$j]['cat_id'] = array
					(
						'value'		=> $cat_id,
						'datatype'	=> false,
						'attrib_id'	=> false
					);
				$dataset[$j]['_type'] = array
					(
						'value'		=> $this->type,
						'datatype'	=> false,
						'attrib_id'	=> false
					);

				if($lookup)
				{
					$dataset[$j]["entity_cat_name_{$entity_id}"] = array
						(
							'value'		=> $category['name'],
							'datatype'	=> false,
							'attrib_id'	=> false
						);
					$dataset[$j]["entity_id_{$entity_id}"] = array
						(
							'value'		=> $entity_id,
							'datatype'	=> false,
							'attrib_id'	=> false
						);
					$dataset[$j]["cat_id_{$entity_id}"] = array
						(
							'value'		=> $cat_id,
							'datatype'	=> false,
							'attrib_id'	=> false
						);
				}
				$j++;
			}

			$values = $this->custom->translate_value($dataset, $location_id);

			return $values;
		}

		function read_single($data, $values = array())
		{
			$entity_id	= isset($data['entity_id']) && $data['entity_id'] ? (int)$data['entity_id'] : $this->entity_id;
			$cat_id		= isset($data['cat_id']) && $data['cat_id'] ? (int)$data['cat_id'] : $this->cat_id;

			$admin_entity	= CreateObject('property.soadmin_entity');
			$admin_entity->type = $this->type;

			$category = $admin_entity->read_single_category($entity_id,$cat_id);

			if($category['is_eav'])
			{
				return $this->read_single_eav($data, $values);
			}

			$id			= (int)$data['id'];
			$num		= isset($data['num']) && $data['num'] ? $data['num'] : '';
			$table = "fm_{$this->type}_{$entity_id}_{$cat_id}";

			if($num)
			{
				$filtermethod = "WHERE num = '{$num}'";
			}
			else
			{
				$filtermethod = "WHERE id = {$id}";
			}

			$this->db->query("SELECT * FROM {$table} {$filtermethod}");

			if($this->db->next_record())
			{
				$values['id']				= $id;
				$values['num']				= $this->db->f('num');
				$values['p_num']			= $this->db->f('p_num');
				$values['p_entity_id']		= $this->db->f('p_entity_id');
				$values['p_cat_id']			= $this->db->f('p_cat_id');
				$values['location_code']	= $this->db->f('location_code');
				$values['tenant_id']		= $this->db->f('tenant_id');
				$values['contact_phone']	= $this->db->f('contact_phone');
				$values['status']			= $this->db->f('status');
				$values['user_id']			= $this->db->f('user_id');
				$values['entry_date']		= $this->db->f('entry_date');

				if ( isset($values['attributes']) && is_array($values['attributes']) )
				{
					foreach ( $values['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->db->f($attr['column_name']);
					}
				}
			}

			return	$values;
		}


		function read_single_eav($data, $values = array())
		{
			$sql = '';

			if(isset($data['guid']) && $data['guid'])
			{
				$sql = "SELECT * FROM fm_bim_item WHERE guid = '{$data['guid']}'";
			}
			else if ( isset($data['location_id']) && $data['location_id'])
			{
				$id			= (int)$data['id'];
				$location_id = (int) $data['location_id'];
			}
			else
			{
				$id			= (int)$data['id'];
				$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$data['entity_id']}.{$data['cat_id']}");
			}

			if(!$sql)
			{
//				$sql = "SELECT fm_bim_item.* FROM fm_bim_item {$this->join} fm_bim_type ON fm_bim_type.id = fm_bim_item.type WHERE fm_bim_item.id = {$id} AND location_id = $location_id";
				$sql = "SELECT * FROM fm_bim_item WHERE fm_bim_item.id = {$id} AND location_id = $location_id";
			}

			$this->db->query($sql,__LINE__,__FILE__);

			if($this->db->next_record())
			{
				$values['id']				= $id;
				$values['num']				= $id;
				$values['p_id']				= $this->db->f('p_id');
				$values['p_location_id']	= $this->db->f('p_location_id');
				$values['location_code']	= $this->db->f('location_code');
				$values['user_id']			= $this->db->f('user_id');
				$values['entry_date']		= $this->db->f('entry_date');

				$xmldata = $this->db->f('xml_representation',true);
				$xml = new DOMDocument('1.0', 'utf-8');
				$xml->loadXML($xmldata);

				if ( isset($values['attributes']) && is_array($values['attributes']) )
				{
					foreach ( $values['attributes'] as &$attr )
					{
						$attr['value'] 	= $xml->getElementsByTagName($attr['column_name'])->item(0)->nodeValue;
					}
				}
			}

			return	$values;
		}

		public function get_short_description($data = array() )
		{
			static $system_location = array();
			static $cache_attributes = array();
			$location_id	= (int)$data['location_id'];
			$id				= (int)$data['id'];

			if(!$location_id && !$id)
			{
				throw new Exception("property_soentity::get_short_description() - Missing entity information info in input");
			}

			if(!isset($system_location[$location_id]))
			{
				$system_location[$location_id] = $GLOBALS['phpgw']->locations->get_name($location_id);
			}

			if(!isset($cache_attributes[$location_id]))
			{
				$filters = array("short_description" => "IS NOT NULL");
				$cache_attributes[$location_id]['attributes'] = $GLOBALS['phpgw']->custom_fields->find2($location_id, 0, '', 'ASC', 'short_description', true, true,$filters);
			}

			$params = array
			(
				'location_id'	=> $location_id,
				'id'			=> $id
			);

			if( substr($system_location[$location_id]['location'], 1, 6) == 'entity' )
			{
				$type					= explode('.',$system_location[$location_id]['location']);
				$params['entity_id']	= $type[2];
				$params['cat_id']		= $type[3];
			}
			else
			{
				throw new Exception("property_soentity::get_short_description() - entity not found");
			}

			$prop_array = $this->read_single($params, $cache_attributes[$location_id]);

			$_short_description = array();

			foreach ($prop_array['attributes'] as $key => $attribute)
			{
				$description_value = $attribute['value'];

				if(isset($cache_attributes[$location_id]['attributes'][$key]['choice']) && $cache_attributes[$location_id]['attributes'][$key]['choice'])
				{
					$choice = $cache_attributes[$location_id]['attributes'][$key]['choice'];
					foreach($choice as $choice_value)
					{
						if ($choice_value['id'] == $attribute['value'])
						{
							$description_value = $choice_value['value'];
							break;
						}
					}
				}

				if($description_value)
				{
					$short_description[] = "{$attribute['input_text']}: {$description_value}";
				}
			}

			$short_description = implode(', ', $short_description);

			return $short_description;
		}


		function check_entity($entity_id,$cat_id,$num)
		{
			$table = "fm_{$this->type}_{$entity_id}_{$cat_id}";
			$this->db->query("SELECT count(*) as cnt FROM $table where num='$num'");

			$this->db->next_record();

			if ( $this->db->f('cnt'))
			{
				return true;
			}
		}

		function generate_id($data)
		{
			$table = "fm_{$this->type}_{$data['entity_id']}_{$data['cat_id']}";
			$this->db->query("select max(id) as id from $table");
			$this->db->next_record();
			$id = $this->db->f('id')+1;

			return $id;
		}

		function generate_num($entity_id,$cat_id,$id)
		{
			$this->db->query("select prefix from fm_{$this->type}_category WHERE entity_id=$entity_id AND id=$cat_id ");
			$this->db->next_record();
			$prefix = $this->db->f('prefix');

			if (strlen($id) == 4)
				$return = $id;
			if (strlen($id) == 3)
				$return = "0$id";
			if (strlen($id) == 2)
				$return = "00$id";
			if (strlen($id) == 1)
				$return = "000$id";
			if (strlen($id) == 0)
				$return = "0001";

			return $prefix . strtoupper($return);
		}


		public function add($values,$values_attribute,$entity_id,$cat_id)
		{
			$values_insert = array();

			if(isset($values['street_name']) && $values['street_name'])
			{
				$address[]= $values['street_name'];
				$address[]= $values['street_number'];
				$address = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!isset($address) || !$address)
			{
				$address = isset($values['location_name']) ? $this->db->db_addslashes($values['location_name']) : '';
			}

			if(isset($address) && $address)
			{
				$values_insert['address'] = $address;
			}

			if (isset($values['location_code']) && $values['location_code'])
			{
				$values_insert['location_code'] = $values['location_code'];
			}

			if(isset($values['location']) && is_array($values['location']))
			{
				foreach ($values['location'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$values_insert[$input_name] = $value;
					}
				}
			}

			if(isset($values['extra']) && is_array($values['extra']))
			{
				foreach ($values['extra'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$values_insert[$input_name] = $value;
					}
				}
			}

			if (isset($values_attribute) && is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['value'])
					{
						if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$entry['value'] = $this->db->db_addslashes($entry['value']);
						}
						$values_insert[$entry['name']] = $entry['value'];

						if($entry['history'] == 1)
						{
							$history_set[$entry['attrib_id']] = array
							(
								'value' => $entry['value'],
								'date'  => $this->bocommon->date_to_timestamp($entry['date'])
							);
						}
					}
				}
			}

			$admin_entity	= CreateObject('property.soadmin_entity');
			$admin_entity->type = $this->type;
			$category = $admin_entity->read_single_category($entity_id, $cat_id);

			$this->db->transaction_begin();

			if(isset($values_insert['p_num']) && $values_insert['p_num'])
			{
			//	$p_category		= $admin_entity->read_single_category($values_insert['p_entity_id'], $values_insert['p_cat_id']);
			//	$p_id			= (int) ltrim($values_insert['p_num'], $p_category['prefix']);
				$p_id			= $values_insert['p_num'];
				$p_location_id	= $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$values_insert['p_entity_id']}.{$values_insert['p_cat_id']}");
			}


			if($category['is_eav'])
			{
 				if(isset($values_insert['p_num']) && $values_insert['p_num'])
				{
					$values_insert['p_id']			= $p_id;
					$values_insert['p_location_id'] = $p_location_id;
				}

				$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");
				$values['id'] = $this->_save_eav($values_insert, $location_id, ".{$this->type}.{$entity_id}.{$cat_id}");
			}
			else
			{
				$table = "fm_{$this->type}_{$entity_id}_$cat_id";
				$values['id'] = $this->generate_id(array('entity_id'=>$entity_id,'cat_id'=>$cat_id));
				$num=$this->generate_num($entity_id,$cat_id,$values['id']);
				$values_insert['id'] = $values['id'];
				$values_insert['num'] = $num;
				$values_insert['entry_date'] =  time();
				$values_insert['user_id'] = $this->account;

				if(isset($values_insert['p_num']) && $values_insert['p_num'])
				{
					$values_insert['p_num'] = $p_id;
				}

				$this->db->query("INSERT INTO {$table} (" . implode(',',array_keys($values_insert)) . ') VALUES ('
				 . $this->db->validate_insert(array_values($values_insert)) . ')',__LINE__,__FILE__);

			}

			if(isset($values['origin']) && is_array($values['origin']))
			{
				if($values['origin'][0]['data'][0]['id'])
				{
					$interlink_data = array
						(
							'location1_id'		=> $GLOBALS['phpgw']->locations->get_id('property', $values['origin'][0]['location']),
							'location1_item_id' => $values['origin'][0]['data'][0]['id'],
							'location2_id'		=> $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}"),
							'location2_item_id' => $values['id'],
							'account_id'		=> $this->account
						);

					$interlink 	= CreateObject('property.interlink');
					$interlink->add($interlink_data,$this->db);
				}
			}

			if (isset($history_set) AND is_array($history_set))
			{
				$historylog	= CreateObject('property.historylog',"{$this->type}_{$entity_id}_{$cat_id}");
				foreach ($history_set as $attrib_id => $history)
				{
					$historylog->add('SO',$values['id'],$history['value'],false, $attrib_id,$history['date']);
				}
			}

			$this->db->transaction_commit();

			$receipt = array();
			$receipt['id'] = $values['id'];
			$receipt['message'][] = array('msg'=>lang('Entity %1 has been saved',$values['id']));
			return $receipt;
		}

		protected function _save_eav($data = array(),$location_id, $location_name)
		{
			$location_id = (int) $location_id;
			$location_name = str_replace('.', '_', $location_name);

			$this->db->query("SELECT id as type FROM fm_bim_type WHERE location_id = {$location_id}",__LINE__,__FILE__);
			$this->db->next_record();
			$type = $this->db->f('type');
			$id = $this->db->next_id('fm_bim_item',array('type'	=> $type));

			phpgw::import_class('phpgwapi.xmlhelper');
			$xmldata = phpgwapi_xmlhelper::toXML($data, $location_name);
			$doc = new DOMDocument;
			$doc->preserveWhiteSpace = true;
			$doc->loadXML( $xmldata );
			$domElement = $doc->getElementsByTagName($location_name)->item(0);
			$domAttribute = $doc->createAttribute('appname');
			$domAttribute->value = $this->type_app[$this->type];

			// Don't forget to append it to the element
			$domElement->appendChild($domAttribute);

			// Append it to the document itself
			$doc->appendChild($domElement);
			$doc->formatOutput = true;

			$xml = $doc->saveXML();

		//	_debug_array($xml);

			if (function_exists('com_create_guid') === true)
			{
				$guid = trim(com_create_guid(), '{}');
			}
			else
			{
				$guid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
			}

			$values_insert = array
			(
  				'id'					=> $id,
  				'location_id'			=> $location_id,
  				'type'					=> $type,
  				'guid'					=> $guid,
				'xml_representation'	=> $this->db->db_addslashes($xml),
				'model'					=> 0,
				'p_location_id'			=> isset($data['p_location_id']) && $data['p_location_id'] ? $data['p_location_id'] : '',
				'p_id'					=> isset($data['p_id']) && $data['p_id'] ? $data['p_id'] : '',
				'location_code'			=> $data['location_code'],
				'loc1'					=> $data['loc1'],
				'address'				=> $data['address'],
				'entry_date'			=> time(),
				'user_id'				=> $this->account
			);

			$this->db->query("INSERT INTO fm_bim_item (" . implode(',',array_keys($values_insert)) . ') VALUES ('
			 . $this->db->validate_insert(array_values($values_insert)) . ')',__LINE__,__FILE__);

			return $id;
		}

		protected function _edit_eav($data = array(),$location_id, $location_name, $id)
		{
			$location_id = (int) $location_id;
			$id = (int) $id;

			$this->db->query("SELECT id as type FROM fm_bim_type WHERE location_id = {$location_id}",__LINE__,__FILE__);
			$this->db->next_record();
			$type = (int)$this->db->f('type');

			$location_name = str_replace('.', '_', $location_name);

			phpgw::import_class('phpgwapi.xmlhelper');

/*
			// FIXME: not working
			if($data)
			{
				foreach ($data as $key => &$value)
				{
					if(preg_match('/[<>&]/', $value)) // or use CDATA..
					{
						$value =  str_ireplace ( array('&lt;','&gt;','<','>') , array('[',']', '[', ']') , $value);
					}
				}
			}
*/
			$xmldata = phpgwapi_xmlhelper::toXML($data, $location_name);
			$doc = new DOMDocument;
			$doc->preserveWhiteSpace = true;
			$doc->loadXML( $xmldata );
			$domElement = $doc->getElementsByTagName($location_name)->item(0);
			$domAttribute = $doc->createAttribute('appname');
			$domAttribute->value = $this->type_app[$this->type];

			// Don't forget to append it to the element
			$domElement->appendChild($domAttribute);

			// Append it to the document itself
			$doc->appendChild($domElement);

			$doc->formatOutput = true;
			$xml = $doc->saveXML();

//			_debug_array($xml);

			$value_set = array
			(
				'xml_representation'	=> $this->db->db_addslashes($xml),
				'p_location_id'			=> isset($data['p_location_id']) && $data['p_location_id'] ? $data['p_location_id'] : '',
				'p_id'					=> isset($data['p_id']) && $data['p_id'] ? $data['p_id'] : '',
				'location_code'			=> $data['location_code'],
				'loc1'					=> $data['loc1'],
				'address'				=> $data['address'],
			);

			$value_set	= $this->db->validate_update($value_set);
			return $this->db->query("UPDATE fm_bim_item SET $value_set WHERE id = $id AND type = {$type}",__LINE__,__FILE__);
		}

		function edit($values,$values_attribute,$entity_id,$cat_id)
		{
			$receipt	= array();
			$value_set	= array();
			$table = "fm_{$this->type}_{$entity_id}_{$cat_id}";

			if(isset($values['street_name']) && $values['street_name'])
			{
				$address[]= $values['street_name'];
				$address[]= $values['street_number'];
				$value_set['address'] = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!isset($address) || !$address)
			{
				$address = isset($values['location_name']) ? $this->db->db_addslashes($values['location_name']) : '';
				if($address)
				{
					$value_set['address'] = $address;
				}
			}

			if (isset($values['location_code']) && $values['location_code'])
			{
				$value_set['location_code'] = $values['location_code'];
			}

			$admin_location	= CreateObject('property.soadmin_location');
			$admin_location->read(false);

			// Delete old values for location - in case of moving up in the hierarchy
			$metadata = $this->db->metadata($table);
			for ($i = 1;$i < $admin_location->total_records + 1; $i++)
			{
				if(isset($metadata["loc{$i}"]))
				{
					$value_set["loc{$i}"]	= false;
				}
			}

			if(isset($values['location']) && is_array($values['location']))
			{
				foreach ($values['location'] as $column => $value)
				{
					$value_set[$column]	= $value;
				}
			}

			if(isset($values['extra']) && is_array($values['extra']))
			{
				foreach ($values['extra'] as $column => $value)
				{
					$value_set[$column]	= $value;
				}
			}

			$admin_entity	= CreateObject('property.soadmin_entity');
			$admin_entity->type = $this->type;
			$category = $admin_entity->read_single_category($entity_id, $cat_id);
			$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");

			if (isset($values_attribute) && is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['datatype']!='AB' && $entry['datatype']!='VENDOR' && $entry['datatype']!='user' && $entry['datatype']!='event')
					{
						if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$entry['value'] = $this->db->db_addslashes($entry['value']);
						}

						if($entry['datatype'] == 'pwd' && $entry['value'] && $entry['value2'])
						{
							if($entry['value'] || $entry['value2'])
							{
								if($entry['value'] == $entry['value2'])
								{
									$value_set[$entry['name']]	= md5($entry['value']);
								}
								else
								{
									$receipt['error'][]=array('msg'=>lang('Passwords do not match!'));
								}
							}
						}
						else
						{
							$value_set[$entry['name']]	= isset($entry['value'])?$entry['value']:'';
						}
					}

					if($entry['history'] == 1)
					{
						if(!$category['is_eav'])
						{
							$this->db->query("SELECT {$entry['name']} FROM {$table} WHERE id = '{$values['id']}'",__LINE__,__FILE__);
							$this->db->next_record();
							$old_value = $this->db->f($entry['name'],true);
						}
						else
						{
							$sql = "SELECT * FROM fm_bim_item WHERE fm_bim_item.id = {$values['id']} AND location_id = $location_id";

							$this->db->query($sql,__LINE__,__FILE__);

							$this->db->next_record();
							$xmldata = $this->db->f('xml_representation');
							$xml = new DOMDocument('1.0', 'utf-8');
							$xml->loadXML($xmldata);
							$old_value 	= $xml->getElementsByTagName($entry['name'])->item(0)->nodeValue;
						}

						if($entry['datatype'] == 'D')
						{
							$old_value = $old_value ? date(phpgwapi_db::date_format(), strtotime($old_value)) : '';
						}

						if($entry['value'] != $old_value)
						{
							$history_set[$entry['attrib_id']] = array
							(
								'value'		=> $entry['value'],
								'old_value'	=> $old_value,
								'date'		=> $this->bocommon->date_to_timestamp($entry['date'])
							);
						}
					}
				}
			}

			$this->db->transaction_begin();


 			if(isset($value_set['p_num']) && $value_set['p_num'])
			{
			//	$p_category		= $admin_entity->read_single_category($value_set['p_entity_id'], $value_set['p_cat_id']);
			//	$p_id			= (int) ltrim($value_set['p_num'], $p_category['prefix']);
				$p_id			= $value_set['p_num'];
				$p_location_id	= $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$value_set['p_entity_id']}.{$value_set['p_cat_id']}");
			}


			if($category['is_eav'])
			{
 				if(isset($value_set['p_num']) && $value_set['p_num'])
				{
					$value_set['p_id']			= $p_id;
					$value_set['p_location_id'] = $p_location_id;
				}

				$this->_edit_eav($value_set, $location_id, ".{$this->type}.{$entity_id}.{$cat_id}", $values['id']);
			}
			else
			{
	 			if(isset($value_set['p_num']) && $value_set['p_num'])
				{
					$value_set['p_num'] = $p_id;
				}

				$value_set	= $this->db->validate_update($value_set);
				$this->db->query("UPDATE $table set $value_set WHERE id=" . $values['id'],__LINE__,__FILE__);
			}

			if (isset($history_set) && is_array($history_set))
			{
				$historylog	= CreateObject('property.historylog',"{$this->type}_{$entity_id}_{$cat_id}");
				foreach ($history_set as $attrib_id => $history)
				{
					$historylog->add('SO',$values['id'],$history['value'],$history['old_value'], $attrib_id,$history['date']);
				}
			}

			$this->db->transaction_commit();

			$receipt['id'] = $values['id'];
			$receipt['message'][] = array('msg'=>lang('entity %1 has been edited',$values['num']));
			return $receipt;
		}

		function delete($entity_id,$cat_id,$id )
		{
			$entity_id	= (int) $entity_id;
			$cat_id		= (int) $cat_id;
			$id			= (int) $id;

			$location_id	= $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");

			$admin_entity	= CreateObject('property.soadmin_entity');
			$admin_entity->type = $this->type;
			$category = $admin_entity->read_single_category($entity_id, $cat_id);

			$this->db->transaction_begin();

			if($category['is_eav'])
			{
				$this->db->query("SELECT id as type FROM fm_bim_type WHERE location_id = {$location_id}",__LINE__,__FILE__);
				$this->db->next_record();
				$type = (int)$this->db->f('type');
				$this->db->query("DELETE FROM fm_bim_item WHERE id = $id AND type = {$type}",__LINE__,__FILE__);
			}
			else
			{
				$table = "fm_{$this->type}_{$entity_id}_{$cat_id}";
				$this->db->query("DELETE FROM $table WHERE id = $id",__LINE__,__FILE__);
			}

			$this->db->query("DELETE FROM phpgw_interlink WHERE location1_id ={$location_id} AND location1_item_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_interlink WHERE location2_id ={$location_id} AND location2_item_id = {$id}",__LINE__,__FILE__);

			$this->db->transaction_commit();
		}

		function read_attrib_help($data)
		{
			$entity_id = (isset($data['entity_id'])?$data['entity_id']:'');
			$cat_id = (isset($data['cat_id'])?$data['cat_id']:'');
			$attrib_id = (isset($data['attrib_id'])?$data['attrib_id']:'');

			if(!$entity_id || !$cat_id || !$attrib_id)
			{
				return;
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");

			$this->db->query("SELECT helpmsg FROM fphpgw_cust_attribute WHERE location_id = {$location_id} AND id =" . (int)$attrib_id );

			$this->db->next_record();
//			$helpmsg = str_replace("\n","<br>",stripslashes($this->db->f('helpmsg')));
			$helpmsg = stripslashes($this->db->f('helpmsg'));
			return $helpmsg;
		}


		function read_entity_to_link($data)
		{
			if(!isset($data['cat_id']) || !$data['cat_id'] || !isset($data['entity_id']) || !$data['entity_id'] || !isset($data['id']) || !$data['id'])
			{
				throw new Exception("property_soentity::read_entity_to_link - Missing entity information info in input");
			}

			$cat_id = (int)$data['cat_id'];
			$entity_id = (int)$data['entity_id'];
			$p_id = $data['id'];
			$p_location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");
			$entity = array();

			foreach ($this->type_app as $type => $app)
			{
				if( !$GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, $app))
				{
					continue;
				}

				$sql = "SELECT * FROM fm_{$type}_category";
				$this->db->query($sql,__LINE__,__FILE__);

				$category = array();
				while ($this->db->next_record())
				{
					$category[] = array
					(
						'entity_id'	=> $this->db->f('entity_id'),
						'cat_id'	=> $this->db->f('id'),
						'name'		=> $this->db->f('name', true),
						'descr'		=> $this->db->f('descr', true),
						'is_eav'	=> $this->db->f('is_eav')
					);
				}

				foreach($category as $entry)
				{
					if($type == 'catch' && $entry['entity_id'] == 1 && $entry['cat_id'] == 1)
					{
						continue;
					}

					if($entry['is_eav'])
					{
						$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$type], ".{$type}.{$entry['entity_id']}.{$entry['cat_id']}");

						$sql = "SELECT count(*) as hits FROM fm_bim_item WHERE location_id = {$location_id} AND p_location_id = {$p_location_id} AND p_id = '{$p_id}'";
					}
					else
					{
						$sql = "SELECT count(*) as hits FROM fm_{$type}_{$entry['entity_id']}_{$entry['cat_id']} WHERE p_entity_id = {$entity_id} AND p_cat_id = {$cat_id} AND p_num = '{$p_id}'";
					}

					$this->db->query($sql,__LINE__,__FILE__);
					$this->db->next_record();
					if($this->db->f('hits'))
					{
						$entity['related'][] = array
							(
								'entity_link'	=> $GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'	=> "property.uientity.index",
									'entity_id'		=> $entry['entity_id'],
									'cat_id'		=> $entry['cat_id'],
									'p_entity_id'	=> $entity_id,
									'p_cat_id' 		=> $cat_id,
									'p_num' 		=> $p_id,
									'type'			=> $type
								)
							),
							'name'			=> $entry['name'] . ' [' . $this->db->f('hits') . ']',
							'descr'			=> $entry['descr']
						);
					}
				}
			}

			$sql = "SELECT count(*) as hits FROM fm_tts_tickets WHERE p_entity_id = {$entity_id} AND p_cat_id = {$cat_id} AND p_num = '{$p_id}'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('hits'))
			{
				$hits = $this->db->f('hits');
				$entity['related'][] = array
					(
						'entity_link'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.index',
					//	'p_entity_id'	=> $entity_id,
					//	'p_cat_id' 		=> $cat_id,
						'p_num' 		=> $p_id,
						'query'=> "entity.{$entity_id}.{$cat_id}.{$p_id}")),
						'name'		=> lang('Helpdesk') . " [{$hits}]",
						'descr'		=> lang('Helpdesk')
					);
			}

			$sql = "SELECT count(*) as hits FROM fm_request WHERE p_entity_id = {$entity_id} AND p_cat_id = {$cat_id} AND p_num = '{$p_id}'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('hits'))
			{
				$hits = $this->db->f('hits');
				$entity['related'][] = array
					(
						'entity_link'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uirequest.index',
					//	'p_entity_id'	=> $entity_id,
					//	'p_cat_id' 		=> $cat_id,
						'p_num' 		=> $p_id,
						'query'=> "entity.{$entity_id}.{$cat_id}.{$p_id}")),
						'name'		=> lang('request') . " [{$hits}]",
						'descr'		=> lang('request')
					);
			}

			$sql = "SELECT count(*) as hits FROM fm_project WHERE p_entity_id = {$entity_id} AND p_cat_id = {$cat_id} AND p_num = '{$p_id}'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('hits'))
			{
				$hits = $this->db->f('hits');
				$entity['related'][] = array
					(
						'entity_link'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiproject.index',
						'query'=> "entity.{$entity_id}.{$cat_id}.{$p_id}",
						'criteria_id' => 6)), //FIXME: criteria 6 is for entities should be altered to locations
						'name'		=> lang('project') . " [{$hits}]",
						'descr'		=> lang('project')
					);
			}

			$sql = "SELECT count(*) as hits FROM fm_s_agreement {$this->join} fm_s_agreement_detail ON fm_s_agreement.id = fm_s_agreement_detail.agreement_id WHERE p_entity_id = {$entity_id} AND p_cat_id = {$cat_id} AND p_num = '{$p_id}'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('hits'))
			{
				$hits = $this->db->f('hits');
				$entity['related'][] = array
					(
						'entity_link'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uis_agreement.index',
																'query'	=> "entity.{$entity_id}.{$cat_id}.{$p_id}",
																'p_num' => $p_id)),
						'name'			=> lang('service agreement') . " [{$hits}]",
						'descr'			=> lang('service agreement')
					);
			}

			return $entity;
		}

		/**
		 * Method for retreiving inventory of bulk items.
		 *
		 * @param $data array array holding input parametres
		 * @return array of entities
		 */

		public function get_inventory($data = array())
		{
			$location_id  	= isset($data['location_id']) && $data['location_id'] ? (int)$data['location_id'] : 0;
			$id				= (int)$data['id'];
			$inventory_id  	= isset($data['inventory_id']) && $data['inventory_id'] ? (int)$data['inventory_id'] : 0;

			$filtermethod = '';

			if($inventory_id)
			{
				$filtermethod = "WHERE fm_bim_item_inventory.id = {$inventory_id}";
			}
			else
			{
				$filtermethod = "WHERE location_id = {$location_id} AND fm_bim_item_inventory.item_id = {$id} AND expired_on IS NULL";			
			}

			if(!$filtermethod)
			{
				return array();
			}

			$sql = "SELECT fm_bim_item_inventory.*, fm_standard_unit.name AS unit FROM fm_bim_item_inventory"
			. " {$this->join} fm_standard_unit ON fm_bim_item_inventory.unit_id = fm_standard_unit.id"
			. " {$filtermethod}"
			. " ORDER BY p_location_id, p_id";

			$this->db->query($sql,__LINE__,__FILE__);
			$inventory = array();
			while ($this->db->next_record())
			{
				$inventory[] = array
				(
					'inventory_id'	=> $this->db->f('id'),
					'inventory'		=> $this->db->f('inventory'),
					'allocated'		=> 0,
					'unit_id'		=> $this->db->f('unit_id'),
					'unit'			=> $this->db->f('unit', true),
					'remark'		=> $this->db->f('remark', true),
					'p_location_id'	=> $this->db->f('p_location_id'),
					'p_id'			=> $this->db->f('p_id'),
					'bookable'		=> $this->db->f('bookable'),
					'active_from'	=> $this->db->f('active_from'),
					'active_to'		=> $this->db->f('active_to'),
					'bookable'		=> $this->db->f('bookable'),
				);
			}


			if(isset($GLOBALS['phpgw_info']['user']['apps']['logistic']))
			{
				$start_date	= time();
				$end_date	= time();

				foreach ($inventory as &$entry)
				{
					$sql = "SELECT SUM(item_inventory_amount) AS allocated"
					. " FROM lg_calendar"
					. " WHERE location_id = {$location_id}"
					. " AND lg_calendar.item_id = {$id}"
					. " AND item_inventory_id = {$entry['inventory_id']}"
					. " AND lg_calendar.end_date >= {$start_date} AND lg_calendar.start_date <= {$end_date}";

					$this->db->query($sql,__LINE__,__FILE__);
			
					$this->db->next_record();
					$entry['allocated'] = (int) $this->db->f('allocated');
				}
			}

			return $inventory;
		}

		public function add_inventory($values)
		{
			$p_location_id = $GLOBALS['phpgw']->locations->get_id('property', '.location.' . count(explode('-', $values['location_code'])));
			
			$p_id = execMethod('property.solocation.get_item_id',$values['location_code']);
			
			if(!$p_location_id && !$p_id)
			{
				throw new Exception('ERROR: Not a valid location');			
			}
			
			$table = 'fm_bim_item_inventory';

			$value_set = array
			(
				'location_id'		=> $values['location_id'],
				'item_id'			=> $values['item_id'],
				'p_location_id'		=> $p_location_id,
				'p_id'				=> $p_id,
				'unit_id'			=> $values['unit_id'],
				'inventory'			=> (int)$values['inventory'],
				'write_off'			=> (int)$values['write_off'],
				'bookable'			=> (int)$values['bookable'],
				'active_from'		=> $values['active_from'],
				'active_to'			=> $values['active_to'],
				'created_on'		=> time(),
				'created_by'		=> $this->account,
	//			'expired_on'		=> ,
	//			'expired_by'		=> ,
				'remark'			=> $this->db->db_addslashes($values['remark'])
			);

			return	$this->db->query("INSERT INTO {$table} (" . implode(',',array_keys($value_set)) . ') VALUES ('
				 . $this->db->validate_insert(array_values($value_set)) . ')',__LINE__,__FILE__);

		}


		public function edit_inventory($values)
		{
			$inventory_id = (int)$values['inventory_id'];
			if(!$inventory_id)
			{
				throw new Exception('ERROR: Not a valid id');			
			}
			
			$this->db->transaction_begin();

			$table = 'fm_bim_item_inventory';

			$value_set = array
			(
				'expired_on'		=> time(),
				'expired_by'		=> $this->account,
			);

			$value_set	= $this->db->validate_update($value_set);
			$this->db->query("UPDATE {$table} SET $value_set WHERE id = {$inventory_id}",__LINE__,__FILE__);

			if(!(int)$values['inventory'])
			{
				return $this->db->transaction_commit();			
			}

			$sql = "SELECT * FROM fm_bim_item_inventory WHERE id = {$inventory_id}";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$value_set = array
			(
				'location_id'		=> $this->db->f('location_id'),
				'item_id'			=> $this->db->f('item_id'),
				'p_location_id'		=> $this->db->f('p_location_id'),
				'p_id'				=> $this->db->f('p_id'),
				'unit_id'			=> $this->db->f('unit_id'),
				'inventory'			=> (int)$values['inventory'],
				'write_off'			=> (int)$values['write_off'],
				'bookable'			=> (int)$values['bookable'],
				'active_from'		=> $values['active_from'],
				'active_to'			=> $values['active_to'],
				'created_on'		=> time(),
				'created_by'		=> $this->account,
				'remark'			=> $this->db->db_addslashes($values['remark'])
			);

			$this->db->query("INSERT INTO {$table} (" . implode(',',array_keys($value_set)) . ') VALUES ('
				 . $this->db->validate_insert(array_values($value_set)) . ')',__LINE__,__FILE__);

			return $this->db->transaction_commit();
		}
		
		public function convert_num_to_id($data = array())
		{
			$entity_id = (int) $data['entity_id'];
			$cat_id = (int) $data['cat_id'];
			$num	= $data['num'];
			
			$id = '';
			if($num)
			{
				$admin_entity	= CreateObject('property.soadmin_entity');
				$admin_entity->type = isset($data['type']) && $data['type'] ? $data['type'] : 'entity';
				$category = $admin_entity->read_single_category($entity_id, $cat_id);

				$id	= (int) ltrim($num, $category['prefix']);
			}
			return $id;
		}
	}
