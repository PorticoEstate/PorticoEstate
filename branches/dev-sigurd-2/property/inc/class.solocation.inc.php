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
	* @subpackage location
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_solocation
	{

		var $bocommon;
		function property_solocation($bocommon = '')
		{
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->soadmin_location	= CreateObject('property.soadmin_location');
			if(!$bocommon || !is_object($bocommon))
			{
				$this->bocommon			= CreateObject('property.bocommon');
			}
			else
			{
				$this->bocommon = $bocommon;
			}
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db($this->db);
			$this->socommon			= & $this->bocommon->socommon;

			$this->join			= $this->bocommon->join;
			$this->left_join	= $this->bocommon->left_join;
			$this->like			= $this->bocommon->like;
		}

		function read_entity_to_link($location_code)
		{
			$entity = array();

			$sql = "SELECT * FROM fm_entity_category where loc_link=1";

			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$sql = "SELECT count(*) as hits FROM fm_entity_" . $this->db->f('entity_id') . "_" . $this->db->f('id') . " WHERE location_code $this->like '$location_code%'";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
				if($this->db->f('hits'))
				{
					$entity[] = array
					(
						'entity_id'	=> $this->db->f('entity_id'),
						'cat_id'	=> $this->db->f('id'),
						'name'		=> $this->db->f('name') . ' [' . $this->db->f('hits') . ']',
						'descr'		=> $this->db->f('descr')
					);
				}
			}

			$sql = "SELECT count(*) as hits FROM fm_tts_tickets WHERE location_code $this->like '$location_code%'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('hits'))
			{
				$entity[] = array
				(
					'entity_link'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.index', 'query'=> $location_code)),
					'name'		=> lang('Helpdesk') . ' [' . $this->db->f('hits') . ']',
					'descr'		=> lang('Helpdesk')
				);
			}

			$sql = "SELECT count(*) as hits FROM fm_document WHERE location_code $this->like '$location_code%'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('hits'))
			{
				$entity[] = array
				(
					'entity_link'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uidocument.index','query'=> $location_code)),
					'name'		=> lang('Documentation') . ' [' . $this->db->f('hits') . ']',
					'descr'		=> lang('Documentation')
				);
			}

			$sql = "SELECT count(*) as hits FROM fm_request WHERE location_code $this->like '$location_code%'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('hits'))
			{
				$entity[] = array
				(
					'entity_link'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uirequest.index','query'=> $location_code)),
					'name'		=> lang('request') . ' [' . $this->db->f('hits') . ']',
					'descr'		=> lang('request')
				);
			}

			$sql = "SELECT count(*) as hits FROM fm_gab_location WHERE location_code $this->like '$location_code%'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('hits'))
			{
				$entity[] = array
				(
					'entity_link'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uigab.index','location_code'=> $location_code)),
					'name'		=> lang('gabnr') . ' [' . $this->db->f('hits') . ']',
					'descr'		=> lang('gab info')
				);
			}

			if (isset($entity))
			{
				return $entity;
			}

		}

		function select_status_list($type_id)
		{
			if(!$type_id)
			{
				return;
			}
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$type_id}");

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

		function get_owner_type_list()
		{
			$this->db->query("SELECT id,descr FROM fm_owner_category  ORDER BY descr ");

			$i = 0;
			while ($this->db->next_record())
			{
				$owner_type[$i]['id']			= $this->db->f('id');
				$owner_type[$i]['name']		= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $owner_type;
		}

		function get_owner_list()
		{
//			$this->db->query("SELECT fm_owner.* ,fm_owner_category.descr as category FROM fm_owner $this->join fm_owner_category on fm_owner.category=fm_owner_category.id  ORDER BY descr ");
			$this->db->query("SELECT *  FROM fm_owner ORDER BY org_name ");
			$i = 0;
			while ($this->db->next_record())
			{
				$owners[$i]['id']			= $this->db->f('id');
				$owners[$i]['name']		= stripslashes($this->db->f('org_name')); // . ' ['. $this->db->f('category') . ']';
				$i++;
			}
			return $owners;
		}

		function check_location($location_code='',$type_id='')
		{
			$this->db->query("SELECT count(*) FROM fm_location$type_id where location_code='$location_code'");

			$this->db->next_record();

			if ( $this->db->f(0))
			{
				return true;
			}
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start				= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter				= isset($data['filter']) && $data['filter'] ? $data['filter'] : 0;
				$query				= isset($data['query']) ? $data['query'] : '';
				$sort				= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
				$order				= isset($data['order']) ? $data['order'] : '';
				$cat_id				= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id']:0;
				$type_id			= isset($data['type_id']) ? $data['type_id'] : '';
				$lookup_tenant		= isset($data['lookup_tenant']) ? $data['lookup_tenant'] : '';
				$district_id		= isset($data['district_id']) ? $data['district_id'] : '';
				$allrows			= isset($data['allrows']) ? $data['allrows'] : '';
				$lookup				= isset($data['lookup']) ? $data['lookup'] : '';
				$status				= isset($data['status']) ? $data['status'] : '';
				$part_of_town_id	= isset($data['part_of_town_id']) ? $data['part_of_town_id'] : '';
				$dry_run			= isset($data['dry_run']) ? $data['dry_run'] : '';
			}

			if (!$type_id)
			{
				return;
			}

			$sql = $this->socommon->fm_cache('sql_'. $type_id . '_' . $lookup_tenant . '_' . $lookup);
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$type_id}");
			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';
			$attribute_filter = " custom = 1 AND location_id = {$location_id}";
			$attribute_choice_filter = " location_id = {$location_id}";

			if(!$sql)
			{
				$location_types	= $this->soadmin_location->select_location_type();

				$cols = "fm_location" . ($type_id) .'.*';
				$cols_return[] 				= 'location_code';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'location_code';
				$uicols['descr'][]			= 'dummy';
				$uicols['statustext'][]		= 'dummy';
				$uicols['exchange'][]		= false;
				$uicols['align'][] 			= '';
				$uicols['datatype'][]		= '';

				for ($i=0; $i<($type_id); $i++)
				{
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= 'loc' . $location_types[$i]['id'];
					$uicols['descr'][]			= $location_types[$i]['name'];
					$uicols['statustext'][]		= $location_types[$i]['descr'];
					$uicols['exchange'][]		= true;
					$uicols['align'][] 			= 'center';
					$uicols['datatype'][]		= 'link';
					$cols 						.= ",fm_location" . ($type_id) .".loc" . $location_types[$i]['id'];
					$cols_return[] 				= 'loc' . $location_types[$i]['id'];
				}

				$uicols['datatype'][$type_id] = 'I'; // correct the last one

				$list_info = $location_types[($type_id-1)]['list_info'];

				for ($i=1; $i<($type_id+1); $i++)
				{
					if(isset($list_info[$i]) && $list_info[$i])
					{
						$cols.= ',fm_location' . $i . '.loc' . ($i) . '_name';
						$cols_return[] 				= 'loc' . ($i) . '_name';
						$uicols['input_type'][]		= 'text';
						$uicols['name'][]			= 'loc' . ($i) . '_name';
						$uicols['descr'][]			= $location_types[($i-1)]['name'] . ' ' . lang('name');
						$uicols['statustext'][]		= $location_types[($i-1)]['name'] . ' ' . lang('name');
						$uicols['exchange'][]		= true;
						$uicols['align'][] 			= 'left';
						$uicols['datatype'][]		= 'V';
					}
				}

				$joinmethod ='';
				$paranthesis = '';
				for ($j=($type_id-1); $j>0; $j--)
				{
					$joinmethod .= " $this->join fm_location". ($j);

					$paranthesis .='(';

					$on = 'ON';
					for ($i=($j); $i>0; $i--)
					{
						$joinmethod .= " $on (fm_location" . ($j+1) .".loc" . ($i). " = fm_location" . ($j) . ".loc" . ($i) . ")";
						$on = 'AND';
						if($i==1)
						{
							$joinmethod .= ")";
						}
					}
				}

				$config = $this->soadmin_location->read_config('');

//_debug_array($config);

				if($lookup_tenant)
				{
					$cols.= ',fm_tenant.id as tenant_id';
					$cols_return[] 				= 'tenant_id';
					$uicols['input_type'][]		= 'hidden';
					$uicols['name'][]			= 'tenant_id';
					$uicols['descr'][]			= 'dummy';
					$uicols['statustext'][]		= 'dummy';
					$uicols['exchange'][]		= true;
					$uicols['align'][] 			= '';
					$uicols['datatype'][]		= '';

					$cols.= ',fm_tenant.last_name';
					$cols_return[] 				= 'last_name';
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= 'last_name';
					$uicols['datatype'][]		= 'V';
					$uicols['descr'][]			= lang('last name');
					$uicols['statustext'][]		= lang('last name');
					$uicols['exchange'][]		= true;
					$uicols['align'][] 			= 'left';
					$uicols['datatype'][]		= 'V';

					$cols.= ',fm_tenant.first_name';
					$cols_return[] 				= 'first_name';
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= 'first_name';
					$uicols['datatype'][]		= 'V';
					$uicols['descr'][]			= lang('first name');
					$uicols['statustext'][]		= lang('first name');
					$uicols['exchange'][]		= true;
					$uicols['align'][] 			= 'left';
					$uicols['datatype'][]		= 'V';

					$cols.= ',fm_tenant.contact_phone';
					$cols_return[] 				= 'contact_phone';
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= 'contact_phone';
					$uicols['datatype'][]		= 'V';
					$uicols['descr'][]			= lang('contact phone');
					$uicols['statustext'][]		= lang('contact phone');
					$uicols['exchange'][]		= true;
					$uicols['align'][] 			= 'left';
					$uicols['datatype'][]		= 'V';

					$sub_query_tenant=1;
					$this->socommon->fm_cache('sub_query_tenant_'. $type_id  . '_' . $lookup_tenant . '_' . $lookup,$sub_query_tenant);
				}

				$config_count	= count($config);
				for ($i=0;$i<$config_count;$i++)
				{
					if (($config[$i]['location_type'] <= $type_id) && ($config[$i]['f_key'] ==1))
					{
						if(!$lookup_tenant && $config[$i]['column_name']=='tenant_id')
						{
						}
						else
						{
							$joinmethod .= " $this->left_join  " . $config[$i]['reference_table'] . " ON ( fm_location" . $config[$i]['location_type'] . "." . $config[$i]['column_name'] . "=" . $config[$i]['reference_table'] . ".".$config[$i]['reference_id']."))";
							$paranthesis .='(';
						}
					}

					if (($config[$i]['location_type'] <= $type_id)  && ($config[$i]['query_value'] ==1))
					{

						if($config[$i]['column_name']=='street_id')
						{

							$sub_query_street=1;
							$this->socommon->fm_cache('sub_query_street_'. $type_id  . '_' . $lookup_tenant . '_' . $lookup,$sub_query_street);

							//list address at sublevels beneath address-level
							if($location_types[($type_id-1)]['list_address'])
							{
								$cols.= ',fm_streetaddress.descr as street_name';
								$cols_return[] 				= 'street_name';
								$uicols['input_type'][]		= 'text';
								$uicols['name'][]			= 'street_name';
								$uicols['descr'][]			= lang('street name');
								$uicols['statustext'][]		= lang('street name');
								$uicols['exchange'][]		= true;
								$uicols['align'][] 			= 'left';
								$uicols['datatype'][]		= 'V';

								$cols.= ',street_number';
								$cols_return[] 				= 'street_number';
								$uicols['input_type'][]		= 'text';
								$uicols['name'][]			= 'street_number';
								$uicols['descr'][]			= lang('street number');
								$uicols['statustext'][]		= lang('street number');
								$uicols['exchange'][]		= true;
								$uicols['align'][] 			= 'left';
								$uicols['datatype'][]		= 'V';

								$cols.= ',fm_location' . $config[$i]['location_type'] . '.' . $config[$i]['column_name'];
								$cols_return[] 				= $config[$i]['column_name'];
								$uicols['input_type'][]		= 'hidden';
								$uicols['name'][]			= $config[$i]['column_name'];
								$uicols['descr'][]			= lang($config[$i]['input_text']);
								$uicols['statustext'][]		= lang($config[$i]['input_text']);
								$uicols['exchange'][]		= true;
								$uicols['align'][] 			= '';
								$uicols['datatype'][]		= '';
							}
						}
						else
						{
							$cols.= ',fm_location' . $config[$i]['location_type'] . '.' . $config[$i]['column_name'];
							$cols_return[] 				= $config[$i]['column_name'];
							$uicols['input_type'][]		= 'hidden';
							$uicols['name'][]			= $config[$i]['column_name'];
							$uicols['descr'][]			= $config[$i]['input_text'];
							$uicols['statustext'][]		= $config[$i]['input_text'];
							$uicols['exchange'][]		= true;
							$uicols['align'][] 			= '';
						}
					}
				}

				$this->db->query("SELECT * FROM $attribute_table WHERE (list=1 OR lookup_form=1) AND $attribute_filter");
				while ($this->db->next_record())
				{
					$cols .= ",fm_location" . ($type_id) .'.' . $this->db->f('column_name');
				}

				$from = " FROM $paranthesis fm_location$type_id ";

				$sql = "SELECT $cols $from $joinmethod";

				$this->socommon->fm_cache('sql_'. $type_id . '_' . $lookup_tenant . '_' . $lookup ,$sql);
				$this->socommon->fm_cache('uicols_'. $type_id  . '_' . $lookup_tenant . '_' . $lookup,$uicols);
				$this->socommon->fm_cache('cols_return_'. $type_id  . '_' . $lookup_tenant . '_' . $lookup,$cols_return);

			}
			else
			{
				$uicols = $this->socommon->fm_cache('uicols_'. $type_id  . '_' . $lookup_tenant . '_' . $lookup);
				$cols_return = $this->socommon->fm_cache('cols_return_'. $type_id  . '_' . $lookup_tenant . '_' . $lookup);

				$sub_query_tenant	= $this->socommon->fm_cache('sub_query_tenant_'. $type_id  . '_' . $lookup_tenant . '_' . $lookup);
				$sub_query_street	= $this->socommon->fm_cache('sub_query_street_'. $type_id  . '_' . $lookup_tenant . '_' . $lookup);
			}

//---------------------start custom user cols

				$user_columns = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['location_columns_'.$type_id . !!$lookup]) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['location_columns_'.$type_id . !!$lookup] : '';
				$user_column_filter = '';
				if (isset($user_columns) AND is_array($user_columns) AND $user_columns[0])
				{
					$user_column_filter = " OR ($attribute_filter AND id IN (" . implode(',',$user_columns) .'))';
				}

				$this->db->query("SELECT DISTINCT * FROM $attribute_table WHERE (list=1 OR lookup_form=1) AND $attribute_filter $user_column_filter ORDER BY attrib_sort ASC");
				$i	= count($uicols['name']);
				while ($this->db->next_record())
				{
					$input_type = 'text';
					if($this->db->f('lookup_form') == 1 && $this->db->f('list') != 1)
					{
						$input_type = 'hidden';
						$exchange	= true;
					}
					else if($this->db->f('lookup_form') == 1)
					{
						$exchange	= true;
					}
					else
					{
						$input_type = 'text';
						$exchange	= false;
					}

					$uicols['input_type'][]		= $input_type;
					$uicols['name'][]			= $this->db->f('column_name');
					$uicols['descr'][]			= $this->db->f('input_text');
					$uicols['statustext'][]		= $this->db->f('statustext');
					$uicols['datatype'][$i]		= $this->db->f('datatype');
					$uicols['exchange'][]		= $exchange;
					$custom = array
					(
						'name'	=> $this->db->f('column_name'),
						'datatype'	=> $this->db->f('datatype'),
						'attrib_id'	=> $this->db->f('id')
					);
					
					$cols_return_extra[]		= $custom;
					
					$uicols['cols_return_extra'][$i]	= $custom;
					unset($custom);

					//TODO: move alignment to ui
					switch ($this->db->f('datatype'))
					{
						case 'V':
						case 'C':
						case 'N':
							$uicols['align'][] 	= 'left';
							break;
						case 'D':
						case 'I':
							$uicols['align'][] 	= 'right';
							break;
						default:
							$uicols['align'][] 	= 'center';
					}
					$i++;
				}

//---------------------end custom user cols

			$this->uicols = $uicols;

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_location' . ($type_id) .'.loc1 ASC';

				if ($type_id > 1)
				{
					for ($i=1;$i<($type_id+1);$i++)
					{
						$ordermethod .= ",fm_location{$type_id}.loc{$i} ASC";
					}
				}
			}

			$where= 'WHERE';

			$filtermethod = '';
			$GLOBALS['phpgw']->config->read();
			if(isset($GLOBALS['phpgw']->config->config_data['acl_at_location']) && $GLOBALS['phpgw']->config->config_data['acl_at_location'])
			{
				$access_location = $this->bocommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod = " WHERE fm_location{$type_id}.loc1 in ('" . implode("','", $access_location) . "')";
				$where= 'AND';
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " $where fm_location" . ($type_id). ".category=$cat_id ";
				$where= 'AND';
			}
			else
			{
				$filtermethod .= " $where  (fm_location" . ($type_id). ".category !=99 OR fm_location" . ($type_id). ".category IS NULL)";
				$where= 'AND';
			}

			if ($filter > 0)
			{
				//cramirez.r@ccfirst.com 16/09/08 	validacion is added to avoid notice
				if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
				//if($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
				{
					$filtermethod .= " $where fm_owner.id='$filter' ";
				}
				else
				{
					$filtermethod .= " $where fm_owner.category='$filter' ";
				}
				$where= 'AND';
			}


			if ($status > 0)
			{
				$filtermethod .= " $where fm_location" . ($type_id). ".status=$status ";
				$where= 'AND';
			}
			else
			{
//				$filtermethod .= " $where fm_location" . ($type_id). ".status IS NULL ";
//				$filtermethod .= " $where fm_location" . ($type_id). ".status !=2 ";
//				$where= 'AND';
			}


			if ($district_id > 0)
			{
				$filtermethod .= " $where fm_part_of_town.district_id='$district_id' ";
				$where= 'AND';
			}

			if ($part_of_town_id > 0)
			{
				$filtermethod .= " $where fm_part_of_town.part_of_town_id='$part_of_town_id' ";
				$where= 'AND';
			}

			$querymethod = '';

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$query = str_replace(",",'.',$query);
				if(stristr($query, '.'))
				{
					$query=explode(".",$query);
					$querymethod = " $where (fm_location" . ($type_id).".loc1='" . $query[0] . "' AND fm_location" . $type_id .".loc" . ($type_id)."='" . $query[1] . "')";
				}
				else
				{
					$sub_query = '';

					if($sub_query_tenant)
					{
						$sub_query = "OR fm_tenant.last_name $this->like '%$query%' OR fm_tenant.first_name $this->like '%$query%' OR fm_tenant.contact_phone $this->like '%$query%'";
					}

					if($sub_query_street)
					{
						$sub_query .= "OR fm_streetaddress.descr $this->like '%$query%'";
					}

					$query_name = '';
					for ($i=1;$i<($type_id+1);$i++)
					{
						$query_name .= "OR loc{$i}_name $this->like '%$query%'";
					}

					$querymethod = " {$where} (fm_location{$type_id}.loc1 {$this->like} '%{$query}%' {$sub_query} OR fm_location{$type_id}.location_code {$this->like} '%{$query}%' {$query_name})";
				}
				$where= 'AND';
			}

			$sql .= "$filtermethod $querymethod";

//echo $sql; die();
			//cramirez.r@ccfirst.com 23/07/08 avoid retrieve data in first time, only render definition for headers (var myColumnDefs)
			if(!$dry_run)
			{
					$this->db->query('SELECT count(*)' . substr($sql,strripos($sql,'from')),__LINE__,__FILE__);
					$this->db->next_record();
					$this->total_records = $this->db->f(0);

					if(!$allrows)
					{
						$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
					}
					else
					{
						$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
					}
					$contacts		= CreateObject('phpgwapi.contacts');

			}


			$j=0;
			$location_count 	= $type_id-1;
			//$contacts		= CreateObject('phpgwapi.contacts');
			$location_list		= array();

			while ($this->db->next_record())
			{
				foreach ($cols_return as $col)
				{
					$location_list[$j][$col] = $this->db->f($col,true);
				}

				if(isset($cols_return_extra) && is_array($cols_return_extra))
				{
					foreach($cols_return_extra as $col_extra)
					{
						$value = $this->db->f($col_extra['name'], true);

						if(($col_extra['datatype']=='R' || $col_extra['datatype']=='LB') && $value)
						{
					//		$sql="SELECT value FROM fm_location_choice where type_id=$type_id AND attrib_id=" .$col_extra['attrib_id']. "  AND id=" . $value;
							$sql="SELECT value FROM $choice_table WHERE $attribute_choice_filter AND attrib_id=" .$col_extra['attrib_id']. "  AND id=" . $value;
							$this->db2->query($sql);
							$this->db2->next_record();
							$location_list[$j][$col_extra['name']] = $this->db2->f('value');
						}
						else if($col_extra['datatype']=='AB' && $value)
						{
							$contact_data	= $contacts->read_single_entry($value,array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
							$location_list[$j][$col_extra['name']]	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
						}
						else if($col_extra['datatype']=='VENDOR' && $value)
						{
							$sql="SELECT org_name FROM fm_vendor where id=$value";
							$this->db2->query($sql);
							$this->db2->next_record();
							$location_list[$j][$col_extra['name']] = $this->db2->f('org_name');
						}
						else if($col_extra['datatype']=='CH' && $value)
						{
							$ch= unserialize($value);

							if (isset($ch) AND is_array($ch))
							{
								for ($k=0;$k<count($ch);$k++)
								{
			//						$sql="SELECT value FROM fm_location_choice where type_id=$type_id AND attrib_id=" .$col_extra['attrib_id']. "  AND id=" . $ch[$k];
									$sql="SELECT value FROM $choice_table WHERE $attribute_choice_filter AND attrib_id=" .$col_extra['attrib_id']. "  AND id=" . $ch[$k];
									$this->db2->query($sql);
									while ($this->db2->next_record())
									{
										$ch_value[]=$this->db2->f('value');
									}
								}
								$location_list[$j][$col_extra['name']] = @implode(",", $ch_value);
								unset($ch_value);
							}
						}
						else if($col_extra['datatype']=='D' && $value)
						{
							$location_list[$j][$col_extra['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($value));
						}
						else if($col_extra['datatype']=='timestamp' && $value)
						{
							$location_list[$j][$col_extra['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$value);
						}
						else if($col_extra['datatype']=='link' && $value)
						{
							$location_list[$j][$col_extra['name']]= phpgw::safe_redirect($value);
						}
						else
						{
							$location_list[$j][$col_extra['name']] = $value;
						}
					}
					unset($value);
				}

				$location_code=	$this->db->f('location_code');
				$location = split('-',$location_code);
				for ($m=0;$m<$location_count;$m++)
				{
					$location_list[$j]['loc' . ($m+1)] = $location[$m];
					$location_list[$j]['query_location']['loc' . ($m+1)]=implode("-", array_slice($location, 0, ($m+1)));
				}

				$j++;
			}

			return $location_list;
		}

		function generate_sql($type_id='',$cols='',$cols_return='',$uicols='',$read_single='')
		{
			$joinmethod = " fm_location" . ($type_id);

			$location_types	= $this->soadmin_location->select_location_type();

	//		$cols .= "fm_location" . ($type_id) .".location_code";
			$cols_return[] = 'location_code';
			for ($i=0; $i<($type_id); $i++)
			{
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'loc' . $location_types[$i]['id'];
				$uicols['descr'][]		= $location_types[$i]['name'];
				$uicols['statustext'][]		= $location_types[$i]['descr'];
	//			$cols 				.= ",fm_location" . ($type_id) .".loc" . $location_types[$i]['id'];
				$cols_return[] 			= 'loc' . $location_types[$i]['id'];
			}


			if($type_id !=1)
			{
//				$cols.= ',fm_location1.loc1_name as loc1_name';
//				$cols_return[] 			= 'loc1_name';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'loc1_name';
				$uicols['descr'][]		= lang('Property Name');
				$uicols['statustext'][]		= lang('Property Name');
			}

			$paranthesis = '';
			for ($j=($type_id-1); $j>0; $j--)
			{
				$joinmethod .= " $this->join fm_location". ($j);

				$paranthesis .='(';

				$on = 'ON';
				for ($i=($j); $i>0; $i--)
				{
					$joinmethod .= " $on (fm_location" . ($j+1) .".loc" . ($i). " = fm_location" . ($j) . ".loc" . ($i) . ")";
					$on = 'AND';
					if($i==1)
					{
						$joinmethod .= ")";
					}
				}
			}

			$config = $this->soadmin_location->read_config('');

			$config_count	= count($config);
			for ($i=0;$i<$config_count;$i++)
			{
				if (($config[$i]['location_type'] <= $type_id) && ($config[$i]['f_key'] ==1))
				{

					if($config[$i]['column_name']=='tenant_id' || $config[$i]['column_name']=='street_id')
					{
						$join=$this->left_join;
					}
					else
					{
						$join =$this->join;
					}

					$joinmethod .= " $join  " . $config[$i]['reference_table'] . " ON ( fm_location" . $config[$i]['location_type'] . "." . $config[$i]['column_name'] . "=" . $config[$i]['reference_table'] . ".".$config[$i]['reference_id']."))";

					$paranthesis .='(';
				}

				if ($config[$i]['location_type'] <= $type_id)
				{

					if($config[$i]['column_name']=='street_id')
					{
						$cols.= ',fm_streetaddress.descr as street_name';
						$cols_return[] 			= 'street_name';
						$uicols['input_type'][]		= 'text';
						$uicols['name'][]		= 'street_name';
						$uicols['descr'][]		= lang('street name');
						$uicols['statustext'][]		= lang('street name');

						$cols.= ',street_number';
						$cols_return[] 			= 'street_number';
						$uicols['input_type'][]		= 'text';
						$uicols['name'][]		= 'street_number';
						$uicols['descr'][]		= lang('street number');
						$uicols['statustext'][]		= lang('street number');

						$cols.= ',fm_location' . $config[$i]['location_type'] . '.' . $config[$i]['column_name'];
						$cols_return[] 			= $config[$i]['column_name'];
						$uicols['input_type'][]		= 'hidden';
						$uicols['name'][]		= $config[$i]['column_name'];
						$uicols['descr'][]		= lang($config[$i]['input_text']);
						$uicols['statustext'][]		= lang($config[$i]['input_text']);

					}
					else if($config[$i]['column_name']=='tenant_id')
					{
						$cols.= ',fm_tenant.id as tenant_id';
						$cols_return[] 			= 'tenant_id';
						$uicols['input_type'][]		= 'hidden';
						$uicols['name'][]		= 'tenant_id';
						$uicols['descr'][]		= 'dummy';
						$uicols['statustext'][]		= 'dummy';

						$cols.= ',fm_tenant.last_name as last_name';
						$cols_return[] 			= 'last_name';
						$uicols['input_type'][]		= 'text';
						$uicols['name'][]		= 'last_name';
						$uicols['descr'][]		= lang('last name');
						$uicols['statustext'][]		= lang('last name');

						$cols.= ',fm_tenant.first_name as first_name';
						$cols_return[] 			= 'first_name';
						$uicols['input_type'][]		= 'text';
						$uicols['name'][]		= 'first_name';
						$uicols['descr'][]		= lang('first name');
						$uicols['statustext'][]		= lang('first name');

						$cols.= ',contact_phone';
						$cols_return[] 			= 'contact_phone';
						$uicols['input_type'][]		= 'text';
						$uicols['name'][]		= 'contact_phone';
						$uicols['descr'][]		= lang('contact phone');
						$uicols['statustext'][]		= lang('contact phone');

					}
					else
					{
						$cols.= ',fm_location' . $config[$i]['location_type'] . '.' . $config[$i]['column_name'];
						$cols_return[] 			= $config[$i]['column_name'];
						$uicols['input_type'][]		= 'text';
						$uicols['name'][]		= $config[$i]['column_name'];
						$uicols['descr'][]		= $config[$i]['input_text'];
						$uicols['statustext'][]		= $config[$i]['input_text'];
					}
				}
			}

			$custom 	= createObject('property.custom_fields');

			$fm_location_cols = $custom->find('property', '.location.' . $type_id, 0, '', '', '', true);
//_debug_array($fm_location_cols);

			if($read_single)
			{
				$cols .= ",fm_location{$type_id}.*";
				foreach ($fm_location_cols as $location_col)
				{
					if($location_col['lookup_form'] == 1)
					{
						$cols_return[] 			= $location_col['column_name'];
					}
				}
			}
			else
			{
				foreach ($fm_location_cols as $location_col)
				{
					if($location_col['list'] == 1)
					{
						$cols .= ",fm_location" . ($type_id) .".".$location_col['column_name'];
						$cols_return[] 			= $location_col['column_name'];
						$uicols['input_type'][]		= 'text';
						$uicols['name'][]		= $location_col['column_name'];
						$uicols['descr'][]		= $location_col['input_text'];
						$uicols['statustext'][]		= $location_col['statustext'];
					}
				}
			}


			$cols.= ',district_id';
			$cols_return[] 	= 'district_id';

			$this->uicols 		= $uicols;
			$this->cols_return	= $cols_return;

			$from = " FROM $paranthesis ";

			$sql = "SELECT $cols $from $joinmethod";

			$this->socommon->fm_cache('sql_single_'. $type_id,$sql);
			$this->socommon->fm_cache('uicols_single_'. $type_id,$uicols);
			$this->socommon->fm_cache('cols_return_single_'. $type_id,$cols_return);

			return $sql;
		}

		function read_single($location_code='',$values = array())
		{
			$location_array = split('-',$location_code);
			$type_id= count($location_array);

			if (!$type_id)
			{
				return;
			}

			$cols = 'fm_location' . $type_id .'.category as cat_id';
			$cols_return[] 	= 'cat_id';

			for ($i=1;$i<($type_id);$i++)
			{
				$cols.= ',fm_location' . $i .'.loc' . $i .'_name';
				$cols_return[] 				= 'loc' . $i .'_name';
			}
			$cols_return[] 				= 'loc' . $type_id .'_name';

		//	$cols.= 'fm_location' . $type_id . '.change_type,';
			$cols_return[] 				= 'change_type';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'loc' . $type_id .'_name';
			$uicols['descr'][]			= lang('name');
			$uicols['statustext'][]		= lang('name');

		//	$cols.= 'fm_location' . $type_id .'.remark as remark,';
			$cols_return[] 				= 'remark';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'descr';
			$uicols['descr'][]			= lang('remark');
			$uicols['statustext'][]		= lang('remark');

			$sql = $this->socommon->fm_cache('sql_single_'. $type_id);

			if(!$sql)
			{
				$sql	= $this->generate_sql($type_id,$cols,$cols_return,$uicols,true);
			}
			else
			{

				$this->uicols	= 	$this->socommon->fm_cache('uicols_single_'. $type_id);
				$this->cols_return	= 	$this->socommon->fm_cache('cols_return_single_'. $type_id);
			}

			$sql .= " WHERE fm_location$type_id.location_code='$location_code' ";

			$this->db->query($sql,__LINE__,__FILE__);

//echo $sql;
			$cols_return	= $this->cols_return;

			$this->db->next_record();

			foreach ($cols_return as $col)
			{
				$values[$col] = $this->db->f($col,true);
			}

			if ( isset($values['attributes']) && is_array($values['attributes']) )
			{
				foreach ( $values['attributes'] as &$attr )
				{
					$attr['value'] 	= $this->db->f($attr['column_name']);
				}
			}

//_debug_array($cols_return);
//_debug_array($values);
			return $values;
		}

		function add($location='',$values_attribute='',$type_id='')
		{
			while (is_array($location) && list($input_name,$value) = each($location))
			{
				if($value)
				{
					if($input_name=='cat_id')
					{
						$input_name='category';
					}
					$cols[] = $input_name;
					$vals[] = $value;
				}
			}

			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['value'])
					{
						if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$entry['value'] = $this->db->db_addslashes($entry['value']);
						}

						$cols[]	= $entry['name'];
						$vals[]	= $entry['value'];
					}
				}
			}

			$cols	=implode(",", $cols) . ",entry_date,user_id";
			$vals	="'" . implode("','", $vals) . "'," . "'" . time() . "','" . $this->account . "'";

			$this->db->transaction_begin();
			$sql	= "INSERT INTO fm_location$type_id ($cols) VALUES ($vals)";

//echo $sql;
			$this->db->query($sql,__LINE__,__FILE__);

			$sql	= "INSERT INTO fm_locations (level, location_code) VALUES ({$type_id}, '{$location['location_code']}')";
			$this->db->query($sql,__LINE__,__FILE__);

			$this->db->transaction_commit();
			$receipt['message'][] = array('msg'=>lang('Location %1 has been saved',$location['location_code']));
			return $receipt;
		}

		function edit($location='',$values_attribute='',$type_id='')
		{
//_debug_array($values_attribute);
			while (is_array($location) && list($input_name,$value) = each($location))
			{
				if($value)
				{
					if($input_name=='cat_id')
					{
						$input_name='category';
					}
					$value_set[$input_name]	= $value;
				}
			}

			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
					{
						$entry['value'] = $this->db->db_addslashes($entry['value']);
					}
					$value_set[$entry['name']]	= $entry['value'];
				}
			}

			$value_set['entry_date'] = time();

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$sql = "SELECT * from fm_location$type_id where location_code ='" . $location['location_code'] . "'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$metadata = $this->db->metadata('fm_location'.$type_id);
			if(isset($this->db->adodb))
			{
				$i = 0;
				foreach($metadata as $key => $val)
				{
					$metadata_temp[$i]['name'] = $key;
					$i++;
				}
				$metadata = $metadata_temp;
				unset ($metadata_temp);
			}

			for ($i=0; $i<count($metadata); $i++)
			{
				$cols[] = $metadata[$i]['name'];

				if (ctype_digit($this->db->f($metadata[$i]['name'])))
				{
					$vals[] = $this->db->f($metadata[$i]['name']);
				}
				else
				{
					$vals[] = $this->db->db_addslashes($this->db->f($metadata[$i]['name'],true));
				}
			}

			$cols[] = 'exp_date';
			$vals[] = date($this->bocommon->datetimeformat,time());

			$cols	=implode(",", $cols);
			$vals = $this->bocommon->validate_db_insert($vals);

			$this->db->transaction_begin();
			$sql = "INSERT INTO fm_location" . $type_id ."_history ($cols) VALUES ($vals)";
			$this->db->query($sql,__LINE__,__FILE__);

			$sql = "UPDATE fm_location$type_id SET $value_set WHERE location_code='" . $location['location_code'] . "'";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->transaction_commit();
			$receipt['message'][] = array('msg'=>lang('Location %1 has been edited',$location['location_code']));
			return $receipt;
		}

		function delete($location_code )
		{
			$location_array = split('-',$location_code);
			$type_id= count($location_array);
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_location$type_id WHERE location_code='{$location_code}'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_locations WHERE location_code='{$location_code}'",__LINE__,__FILE__);
			$this->db->transaction_commit();
		}

		function update_cat()
		{
			$location_types	= $this->soadmin_location->select_location_type();

			$m= count($location_types);

			$this->db->transaction_begin();

			$this->db->query("UPDATE fm_location" . $m. " set	status= 2  WHERE category=99",__LINE__,__FILE__);

			for ($type_id=$m; $type_id>1; $type_id--)
			{
				$parent_table = 'fm_location' . ($type_id-1);

				$joinmethod .= " $this->join $parent_table";

				$paranthesis .='(';

				$on = 'ON';
				for ($i=($type_id-1); $i>0; $i--)
				{
					$joinmethod .= " $on (fm_location" . ($type_id) .".loc" . ($i). ' = '.$parent_table . ".loc" . ($i) . ")";
					$on = 'AND';
					if($i==1)
					{
						$joinmethod .= ")";
					}
				}

				$sql = "SELECT $parent_table.location_code ,count(*) as count_99  FROM $paranthesis fm_location$type_id $joinmethod where fm_location$type_id.status=2 group by $parent_table.location_code ";
				$this->db->query($sql,__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					$outdated[$this->db->f('location_code')]['count_99']=$this->db->f('count_99');
				}

				$sql = "SELECT $parent_table.location_code ,count(*) as count_all  FROM $paranthesis fm_location$type_id $joinmethod group by $parent_table.location_code ";
				$this->db->query($sql,__LINE__,__FILE__);
				while ($this->db->next_record())
				{
					if( $outdated[$this->db->f('location_code')]['count_99']==$this->db->f('count_all'))
					{
						$update[]=array('location_code'	=> $this->db->f('location_code'));
					}
				}

				$j=0;
				for ($i=0; $i<count($update); $i++)
				{

					$sql = "SELECT status  FROM $parent_table WHERE location_code= '" . $update[$i]['location_code'] ."'";

					$this->db->query($sql,__LINE__,__FILE__);
					$this->db->next_record();

					if($this->db->f('status')!=2)
					{
						$j++;
						$this->db->query("UPDATE fm_location" . ($type_id-1). " set	status= 2  WHERE location_code= '" . $update[$i]['location_code'] ."'",__LINE__,__FILE__);
					}
				}

				$receipt['message'][]=array('msg'=>lang('%1 location %2 has been updated to not active of %3 already not active',$j,$location_types[($type_id-2)]['descr'],count($update)));

				unset($outdated);
				unset($update);
				unset($joinmethod);
				unset($paranthesis);
			}

			$this->db->transaction_commit();

			return $receipt;
		}


		function update_location()
		{
			$this->db->transaction_begin();

			$this->db->query('SELECT max(id) as levels FROM fm_location_type');
			$this->db->next_record();
			$levels =  $this->db->f('levels');

			//perform an update on all location_codes on all levels to make sure they are consistent and unique
			$locations = array();
			for ($level=1;$level<($levels+1);$level++)
			{
				$sql = "SELECT * from fm_location{$level}";
				$this->db->query($sql,__LINE__,__FILE__);
				$i = 0;
				while($this->db->next_record())
				{
					$location_code = array();
					$where = 'WHERE';
					$locations[$level][$i]['condition'] = '';
					for ($j=1;$j<($level+1);$j++)
					{
						$loc = $this->db->f("loc{$j}");
						$location_code[] = $loc;
						$locations[$level][$i]['condition'] .= "$where loc{$j}='{$loc}'";
						$where = 'AND';
					}
					$locations[$level][$i]['new_values']['location_code'] = implode('-', $location_code);
					$i++;
				}

			}

			foreach($locations as $level => $location_at_leve)
			{
				foreach($location_at_leve as $location )
				{
					$sql = "UPDATE fm_location{$level} SET location_code = '{$location['new_values']['location_code']}' {$location['condition']}";
					$this->db->query($sql,__LINE__,__FILE__);
				}
			}

			$locations = array();
			for ($i=1;$i<($levels+1);$i++)
			{
				$this->db->query("SELECT fm_location{$i}.location_code from fm_location{$i} $this->left_join fm_locations ON fm_location{$i}.location_code = fm_locations.location_code WHERE fm_locations.location_code IS NULL");
				while($this->db->next_record())
				{
					$locations[] = array
					(
						'level' 		=> $i,
						'location_code' => $this->db->f('location_code')
					);
				}
			}

			$receipt = array();
			foreach ($locations as $location)
			{
				$this->db->query("INSERT INTO fm_locations (level, location_code) VALUES ({$location['level']}, '{$location['location_code']}')");

				$receipt['message'][]=array('msg'=>lang('location %1 added at level %2', $location['location_code'], $location['level']));
			}

			if( $this->db->transaction_commit() )
			{
				return $receipt;
			}
			else
			{
				return $receipt['error'][]=array('msg'=>lang('update failed'));
			}
		}


		function read_summary($data='')
		{
			if(is_array($data))
			{
				$filter	= (isset($data['filter'])?$data['filter']:0);
				$type_id = (isset($data['type_id'])?$data['type_id']:'');
				$district_id = (isset($data['district_id'])?$data['district_id']:'');
				$part_of_town_id = (isset($data['part_of_town_id'])?$data['part_of_town_id']:'');
			}

			if(!$type_id)
			{
				$type_id=4;
			}

			$entity_table = 'fm_location' . $type_id ;
			$cols_return = array();
			$paranthesis = '';

			$cols= "count(*) as number, $entity_table.category, $entity_table"."_category.descr as type";

			$groupmethod = " GROUP by $entity_table.category , $entity_table"."_category.descr";

			$uicols['name'][]	= 'type';
			$uicols['descr'][]	= lang('type');
			$uicols['input_type'][]	= 'text';

			$filtermethod = '';
			$where = 'WHERE';
			if($district_id>0)
			{
				$uicols['name'][]	= 'district_id';
				$uicols['descr'][]	= lang('district_id');
				$uicols['input_type'][]	= 'text';
				$cols.=", fm_part_of_town.district_id as district_id";
				$groupmethod .= " ,fm_part_of_town.district_id";
				$filtermethod = " $where fm_part_of_town.district_id=$district_id";
				$where = 'AND';
			}

			if($part_of_town_id>0)
			{
				$uicols['name'][]	= 'part_of_town';
				$uicols['descr'][]	= lang('part of town');
				$uicols['input_type'][]	= 'text';
				$groupmethod .= " ,fm_part_of_town.name";
				$cols.=", fm_part_of_town.name as part_of_town";
				$filtermethod .= " $where fm_part_of_town.part_of_town_id=$part_of_town_id";
				$where = 'AND';
			}

			if($filter>0)
			{
				if($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
				{
					$filtermethod .= " $where fm_owner.id='$filter' ";
				}
				else
				{
					$filtermethod .= " $where fm_owner.category='$filter' ";
				}
				$where= 'AND';
			}

			$uicols['name'][]	= 'number';
			$uicols['descr'][]	= lang('number');
			$uicols['input_type'][]	= 'text';

			$joinmethod= "$this->join $entity_table"."_category on $entity_table.category=$entity_table"."_category.id";

			$sql = $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols_return'=>$cols_return,'cols'=>$cols,
								'uicols'=>$uicols,'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,'no_address'=>true,'location_level'=>$type_id));

			$this->db->query($sql . $filtermethod . $groupmethod . " ORDER BY $entity_table.category",__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$summary[]=array(
					'number'		=> $this->db->f('number'),
					'type'			=> '[' . $this->db->f('category') . '] ' .$this->db->f('type'),
					'part_of_town'	=> $this->db->f('part_of_town'),
					'district_id'	=> $this->db->f('district_id')
					);
			}


			$this->uicols		= $uicols;
			return $summary;
		}

		function check_history($location_code='')
		{
			$location_array = split('-',$location_code);
			$type_id= count($location_array);

			if (!$type_id)
			{
				return false;
			}

			$table = 'fm_location' . $type_id . '_history';

			$sql = "SELECT count(*) FROM $table WHERE location_code='$location_code'";

			$this->db->query($sql,__LINE__,__FILE__);

			$this->db->next_record();

			if($this->db->f('0')>0)
			{
				return true;
			}
			else
			{
				return;
			}
		}

		function get_history($location_code='')
		{
			$this->uicols = array();
			$location_array = split('-',$location_code);
			$type_id= count($location_array);
			$contacts			= CreateObject('phpgwapi.contacts');

			if (!$type_id)
			{
				return;
			}

			$table = 'fm_location' . $type_id . '_history';

			$table_category = 'fm_location' . $type_id . '_category';
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$type_id}");
			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';
			$attribute_filter = " location_id = {$location_id}";

			$sql = "SELECT column_name,datatype,input_text,id as attrib_id FROM $attribute_table WHERE $attribute_filter";

			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$attrib[] = array(
					'column_name' => $this->db->f('column_name'),
					'input_text' => $this->db->f('input_text'),
					'datatype' => $this->db->f('datatype'),
					'attrib_id' => $this->db->f('attrib_id')
				);

				$this->uicols['input_type'][] = 'text';
				$this->uicols['name'][] = $this->db->f('column_name');
				$this->uicols['descr'][] = $this->db->f('input_text');
			}

			$this->uicols['input_type'][] = 'text';
			$this->uicols['name'][] = 'exp_date';
			$this->uicols['descr'][] = lang('exp date');


			$attrib[] = array(
				'column_name' => 'exp_date',
				'input_text' => 'exp date',
				'datatype' => 'D'
			);

			$sql = "SELECT $table.*, $table_category.descr as category FROM $table $this->left_join $table_category ON $table.category =$table_category.id WHERE location_code='$location_code' ORDER BY exp_date DESC";
			$this->db->query($sql,__LINE__,__FILE__);

			$j=0;
			while ($this->db->next_record())
			{
				for ($i=0; $i<count($attrib); $i++)
				{
					$location[$j][$attrib[$i]['column_name']]=$this->db->f($attrib[$i]['column_name']);

					$value = $this->db->f($attrib[$i]['column_name']);
					if(($attrib[$i]['datatype']=='R' || $attrib[$i]['datatype']=='LB') && $value)
					{

						$sql="SELECT value FROM $choice_table WHERE $attribute_filter AND attrib_id=" .$attrib[$i]['attrib_id']. "  AND id=" . $value;
						$this->db2->query($sql);
						$this->db2->next_record();
						$location[$j][$attrib[$i]['column_name']] = $this->db2->f('value');
					}
					else if($attrib[$i]['datatype']=='AB' && $value)
					{
						$contact_data	= $contacts->read_single_entry($value,array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$location[$j][$attrib[$i]['column_name']]	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}
					else if($attrib[$i]['datatype']=='VENDOR' && $value)
					{
						$sql="SELECT org_name FROM fm_vendor where id=$value";
						$this->db2->query($sql);
						$this->db2->next_record();
						$location[$j][$attrib[$i]['column_name']] = $this->db2->f('org_name');
					}
					else if($attrib[$i]['datatype']=='CH' && $value)
					{
						$ch= unserialize($value);
						if (isset($ch) AND is_array($ch))
						{
							for ($k=0;$k<count($ch);$k++)
							{
								$sql="SELECT value FROM $choice_table WHERE $attribute_filter AND attrib_id=" .$attrib[$i]['attrib_id']. "  AND id=" . $ch[$k];
								$this->db2->query($sql);
								while ($this->db2->next_record())
								{
									$ch_value[]=$this->db2->f('value');
								}
							}
							$location[$j][$attrib[$i]['column_name']] = @implode(",", $ch_value);
							unset($ch_value);
						}
					}
					else if($attrib[$i]['datatype']=='D' && $value)
					{
						$location[$j][$attrib[$i]['column_name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($value));
					}
					else if($attrib[$i]['datatype']=='link' && $value)
					{
						$location_list[$j][$attrib[$i]['name']]= phpgw::safe_redirect($value);
					}
					else if($attrib[$i]['column_name']=='entry_date' && $value)
					{
						$location[$j][$attrib[$i]['column_name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$value);
					}
					else
					{
						$location_list[$j][$attrib[$i]['name']] = $value;
					}

					unset($value);
				}
				$j++;
			}

			return $location;
		}

		function get_tenant_location($tenant_id='')
		{
			$location_code = '';

			$location_level = $this->soadmin_location->read_config_single('tenant_id');

			$this->db->query("SELECT location_code FROM fm_location{$location_level} WHERE tenant_id='" . $tenant_id ."'",__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$location_code[] = $this->db->f('location_code');
			}
			if (count($location_code) ==1)
			{
				return $location_code[0];
			}
			else
			{
				return $location_code;
			}
		}

	}

