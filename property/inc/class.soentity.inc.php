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
 	* @version $Id: class.soentity.inc.php,v 1.35 2007/10/13 10:02:53 sigurdne Exp $
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

		function property_soentity($entity_id='',$cat_id='')
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           = $this->bocommon->new_db();
			$this->db2          = $this->bocommon->new_db();

			$this->join			= $this->bocommon->join;
			$this->left_join	= $this->bocommon->left_join;
			$this->like			= $this->bocommon->like;
			$this->entity_id	= $entity_id;
			$this->cat_id		= $cat_id;
		}

		function select_status_list($entity_id,$cat_id)
		{
			if(!$entity_id || !$cat_id)
			{
				return;
			}

			$sql= "SELECT fm_entity_choice.id, fm_entity_choice.value FROM fm_entity_attribute $this->join fm_entity_choice ON "
			. " fm_entity_attribute.entity_id= fm_entity_choice.entity_id AND "
			. " fm_entity_attribute.cat_id= fm_entity_choice.cat_id AND "
			. " fm_entity_attribute.id= fm_entity_choice.attrib_id "
			. " WHERE fm_entity_attribute.column_name='status' "
			. " AND fm_entity_choice.entity_id=$entity_id "
			. " AND fm_entity_choice.cat_id=$cat_id ORDER BY fm_entity_choice.id";


			$this->db->query($sql,__LINE__,__FILE__);

			$i = 0;
			$status = array();
			while ($this->db->next_record())
			{
				$status[$i]['id']				= $this->db->f('id');
				$status[$i]['name']				= stripslashes($this->db->f('value'));
				$i++;
			}
			return $status;
		}


		function read($data)
		{
			if(is_array($data))
			{
				$start	= (isset($data['start']) && $data['start'] ? $data['start']:0);
				$filter	= $data['filter']?$data['filter']:'all';
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$district_id = (isset($data['district_id'])?$data['district_id']:0);
				$lookup = (isset($data['lookup'])?$data['lookup']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$entity_id = (isset($data['entity_id'])?$data['entity_id']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:'');
				$status = (isset($data['status'])?$data['status']:'');
				$start_date = (isset($data['start_date'])?$data['start_date']:'');
				$end_date = (isset($data['end_date'])?$data['end_date']:'');
			}

			if(!$entity_id || !$cat_id)
			{
				return;
			}

			$grants 	= $GLOBALS['phpgw']->session->appsession('grants_entity_'.$entity_id.'_'.$cat_id,$this->currentapp);

			if(!$grants)
			{
				$this->acl 	= CreateObject('phpgwapi.acl');
				$grants		= $this->acl->get_grants($this->currentapp,'.entity.' . $entity_id . '.' . $cat_id);
				$GLOBALS['phpgw']->session->appsession('grants_entity_'.$entity_id.'_'.$cat_id,$this->currentapp,$grants);
			}

			$sql = $this->bocommon->fm_cache('sql_entity_' . $entity_id . '_' . $cat_id . '_' . $lookup);

			$admin_entity	= CreateObject('property.soadmin_entity');
			$category = $admin_entity->read_single_category($entity_id,$cat_id);

			$entity_table = 'fm_entity_' . $entity_id . '_' . $cat_id;
			if(!$sql)
			{
				$cols = $entity_table . '.*';
				$cols_return[] = 'location_code';

				$cols_return[] 			= 'num';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'num';
				$uicols['descr'][]		= lang('ID');
				$uicols['statustext'][]		= lang('ID');

				$cols_return[] 			= 'id';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]		= 'id';
				$uicols['descr'][]		= false;
				$uicols['statustext'][]		= false;
				if($lookup)
				{
					$cols .= ',num as entity_num_' . $entity_id;
					$cols_return[] = 'entity_num_' . $entity_id;
				}

				$cols .= ',account_lid';
				$cols_return[] 				= 'account_lid';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'account_lid';
				$uicols['descr'][]			= lang('User');
				$uicols['statustext'][]		= lang('User');

				$joinmethod = " $this->join phpgw_accounts ON ($entity_table.user_id = phpgw_accounts.account_id))";
				$paranthesis ='(';

				$sql = $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols_return'=>$cols_return,'cols'=>$cols,
								'uicols'=>$uicols,'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,'query'=>$query,'lookup'=>$lookup,'location_level'=>$category['location_level']));

				$this->bocommon->fm_cache('sql_entity_' . $entity_id . '_' . $cat_id . '_' . $lookup,$sql);
				$this->bocommon->fm_cache('uicols_entityt_' . $entity_id . '_' . $cat_id . '_' . $lookup,$this->bocommon->uicols);
				$this->bocommon->fm_cache('cols_return_entityt_' . $entity_id . '_' . $cat_id . '_' . $lookup,$this->bocommon->cols_return);
				$this->bocommon->fm_cache('cols_return_lookup_entityt_' . $entity_id . '_' . $cat_id . '_' . $lookup,$this->bocommon->cols_return_lookup);
				$this->bocommon->fm_cache('cols_extra_entityt_' . $entity_id . '_' . $cat_id . '_' . $lookup,$this->bocommon->cols_extra);

				$uicols				= $this->bocommon->uicols;
				$cols_return			= $this->bocommon->cols_return;
				$this->cols_return_lookup	= $this->bocommon->cols_return_lookup;
				$this->cols_extra		= $this->bocommon->cols_extra;
			}
			else
			{
				$uicols 			= $this->bocommon->fm_cache('uicols_entityt_' . $entity_id . '_' . $cat_id . '_' . $lookup);
				$cols_return			= $this->bocommon->fm_cache('cols_return_entityt_' . $entity_id . '_' . $cat_id . '_' . $lookup);
				$this->cols_return_lookup 	= $this->bocommon->fm_cache('cols_return_lookup_entityt_' . $entity_id . '_' . $cat_id . '_' . $lookup);
				$this->cols_extra		= $this->bocommon->fm_cache('cols_extra_entityt_' . $entity_id . '_' . $cat_id . '_' . $lookup);
			}

			if ($cat_id > 0)
			{
				$fm_entity_cols = $admin_entity->read_attrib(array('entity_id'=>$entity_id,'cat_id'=>$cat_id,'allrows'=>true));

				$result_columns = array();
				foreach ($fm_entity_cols as $col_entry)
				{
					if ($col_entry['list'])
					{
						$result_columns[] = $col_entry['id'];
					}
				}

				$user_columns = isset($GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]['entity_columns_'.$entity_id.'_'.$cat_id])?$GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]['entity_columns_'.$entity_id.'_'.$cat_id]:'';

				if (isset($user_columns) AND is_array($user_columns) AND $user_columns[0])
				{
					$result_columns = array_merge($result_columns, $user_columns);
					$result_columns = array_unique($result_columns);
				}
					
				$i	= count($uicols['name']);
				if(isset($result_columns) && is_array($result_columns))
				{
					foreach($result_columns as $column_id)
					{
						$this->db->query("SELECT * FROM fm_entity_attribute WHERE entity_id= $entity_id AND cat_id= $cat_id AND id= $column_id");
						$this->db->next_record();
						
						if($this->db->f('datatype') == 'link')
						{
							$uicols['input_type'][]		= 'link';
						}
						else
						{
							$uicols['input_type'][]		= 'text';
						}
						$uicols['name'][]			= $this->db->f('column_name');
						$uicols['descr'][]			= $this->db->f('input_text');
						$uicols['statustext'][]		= $this->db->f('statustext');
						$uicols['datatype'][$i]		= $this->db->f('datatype');
						$cols_return_extra[]= array(
							'name'	=> $this->db->f('column_name'),
							'datatype'	=> $this->db->f('datatype'),
							'attrib_id'	=> $this->db->f('id')
						);
						$i++;
					}
				}

				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'entry_date';
				$uicols['descr'][]			= lang('entry date');
				$uicols['statustext'][]		= lang('entry date' );
				$uicols['datatype'][$i]		= 'timestamp';
				$cols_return_extra[]= array(
					'name'	=> 'entry_date',
					'datatype'	=> 'timestamp',
				);
			}
			else
			{
				return;
			}

			$this->uicols	= $uicols;

//_debug_array($cols_return_extra);
			if ($order)
			{
				$ordermethod = " order by $entity_table.$order $sort";
			}
			else
			{
				$ordermethod = " order by $entity_table.id DESC";
			}

			$where= 'WHERE';
			$filtermethod = '';

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

			if ($status)
			{
				$filtermethod .= " $where $entity_table.status='$status' ";
				$where= 'AND';
			}

			if ($district_id > 0)
			{
				$filtermethod .= " $where  district_id='$district_id' ";
				$where = 'AND';
			}

			if ($start_date)
			{
				$filtermethod .= " $where $entity_table.entry_date >= $start_date AND $entity_table.entry_date <= $end_date ";
				$where= 'AND';
			}

			$querymethod = '';
			if($query)
			{
				$query = str_replace(",",'.',$query);
				if(stristr($query, '.'))
				{
					$query=explode(".",$query);
					$querymethod = " $where ($entity_table.location_code $this->like '" . $query[0] . "%' AND $entity_table.location_code $this->like '%" . $query[1] . "')";
				}
				else
				{
					$query = ereg_replace("'",'',$query);
					$query = ereg_replace('"','',$query);
					$filtermethod .= " $where ( $entity_table.location_code $this->like '%$query%' OR $entity_table.num $this->like '%$query%' OR address $this->like '%$query%')";
					$where= 'OR';

					$this->db->query("SELECT * FROM fm_entity_attribute where search='1' AND entity_id= $entity_id AND cat_id = $cat_id");

					while ($this->db->next_record())
					{
						if($this->db->f('datatype')=='V' || $this->db->f('datatype')=='email' || $this->db->f('datatype')=='CH'):
						{
							$querymethod[]= "$entity_table." . $this->db->f('column_name') . " $this->like '%$query%'";
						}
						elseif($this->db->f('datatype')=='I'):
						{
							if(ctype_digit($query))
							{
								$querymethod[]= "$entity_table." . $this->db->f('column_name') . " = " . intval($query);
							}
						}
						else:
						{
							$querymethod[]= "$entity_table." . $this->db->f('column_name') . " = '$query'";
						}
						endif;
					}

					if (isset($querymethod) AND is_array($querymethod))
					{
						$querymethod = " $where (" . implode (' OR ',$querymethod) . ')';
						$where = 'AND';
					}
				}
			}

			$sql .= " $filtermethod $querymethod";

//echo $sql;
			$this->db2->query('SELECT count(*)' . substr($sql,strripos($sql,'from')),__LINE__,__FILE__);
			$this->db2->next_record();
			$this->total_records = $this->db2->f(0);

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$j=0;
			$n=count($cols_return);
//_debug_array($cols_return);
			$contacts			= CreateObject('phpgwapi.contacts');

			$entity_list = array();
			while ($this->db->next_record())
			{
				for ($i=0;$i<$n;$i++)
				{
					$entity_list[$j][$cols_return[$i]] = $this->db->f($cols_return[$i]);
					$entity_list[$j]['grants'] = (int)$grants[$this->db->f('user_id')];
					if($lookup)
					{
						$entity_list[$j]['entity_cat_name_' . $entity_id] = $category['name'];
						$entity_list[$j]['entity_id_' . $entity_id] = $entity_id;
						$entity_list[$j]['cat_id_' . $entity_id] = $cat_id;
					}
				}

				if(isset($cols_return_extra) && is_array($cols_return_extra))
				{
					for ($i=0;$i<count($cols_return_extra);$i++)
					{
						$value = $this->db->f($cols_return_extra[$i]['name']);

						if(($cols_return_extra[$i]['datatype']=='R' || $cols_return_extra[$i]['datatype']=='LB') && $value)
						{
							$sql="SELECT value FROM fm_entity_choice where entity_id=$entity_id AND cat_id=$cat_id AND attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $value;
							$this->db2->query($sql);
							$this->db2->next_record();
							$entity_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('value');
						}
						else if($cols_return_extra[$i]['datatype']=='AB' && $value)
						{
							$contact_data	= $contacts->read_single_entry($value,array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
							$entity_list[$j][$cols_return_extra[$i]['name']]	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
						}
						else if($cols_return_extra[$i]['datatype']=='VENDOR' && $value)
						{
							$sql="SELECT org_name FROM fm_vendor where id=$value";
							$this->db2->query($sql);
							$this->db2->next_record();
							$entity_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('org_name');
						}
						else if($cols_return_extra[$i]['datatype']=='CH' && $value)
						{
							$ch= unserialize($value);
	
							if (isset($ch) AND is_array($ch))
							{
								for ($k=0;$k<count($ch);$k++)
								{
									$sql="SELECT value FROM fm_entity_choice where entity_id=$entity_id AND cat_id=$cat_id AND attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $ch[$k];
									$this->db2->query($sql);
									while ($this->db2->next_record())
									{
										$ch_value[]=$this->db2->f('value');
									}
								}
								$entity_list[$j][$cols_return_extra[$i]['name']] = @implode(",", $ch_value);
								unset($ch_value);
							}
						}
						else if($cols_return_extra[$i]['datatype']=='D' && $value)
						{
							$entity_list[$j][$cols_return_extra[$i]['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($value));
						}
						else if($cols_return_extra[$i]['datatype']=='timestamp' && $value)
						{
							$entity_list[$j][$cols_return_extra[$i]['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$value);
						}
						else
						{
							$entity_list[$j][$cols_return_extra[$i]['name']]=stripslashes($value);
						}
					}
				}

				$location_code=	$this->db->f('location_code');
				$location = split('-',$location_code);
				for ($m=0;$m<count($location);$m++)
				{
					$entity_list[$j]['loc' . ($m+1)] = $location[$m];
					$entity_list[$j]['query_location']['loc' . ($m+1)]=implode("-", array_slice($location, 0, ($m+1)));
				}

				$j++;
			}
//_debug_array($entity_list);
			return $entity_list;
		}

		function read_single($data)
		{
			$entity_id =$data['entity_id'];
			$cat_id =$data['cat_id'];
			$id =$data['id'];

			$this->db->query("SELECT * FROM fm_entity_attribute WHERE entity_id =$entity_id AND cat_id =$cat_id ORDER BY attrib_sort");

			while ($this->db->next_record())
			{
				$entity['attributes'][] = array
				(
					'attrib_id'		=> $this->db->f('id'),
					'name'			=> $this->db->f('column_name'),
					'input_text'	=> stripslashes($this->db->f('input_text')),
					'statustext'	=> stripslashes($this->db->f('statustext')),
//					'statustext'	=> str_replace("\n"," ",stripslashes($this->db->f('statustext'))),
					'datatype'		=> $this->db->f('datatype'),
					'history'		=> $this->db->f('history'),
					'allow_null'	=> $this->db->f('nullable'),
					'disabled'		=> $this->db->f('disabled'),
					'helpmsg'		=> !!$this->db->f('helpmsg')
				);
			}
//_debug_array($entity);

			if($id)
			{
				$table='fm_entity_' . $entity_id .'_' . $cat_id;

				$this->db->query("SELECT * FROM $table WHERE id =$id");

				if($this->db->next_record())
				{
					$entity['id']				= $id;
					$entity['num']				= $this->db->f('num');
					$entity['p_num']			= $this->db->f('p_num');
					$entity['p_entity_id']		= $this->db->f('p_entity_id');
					$entity['p_cat_id']			= $this->db->f('p_cat_id');
					$entity['location_code']	= $this->db->f('location_code');
					$entity['tenant_id']		= $this->db->f('tenant_id');
					$entity['contact_phone']	= $this->db->f('contact_phone');
					$entity['status']			= $this->db->f('status');

					for ($i=0;$i<count($entity['attributes']);$i++)
					{
						$entity['attributes'][$i]['value'] 	= $this->db->f($entity['attributes'][$i]['name']);
						$entity['attributes'][$i]['datatype_text'] 	= $this->bocommon->translate_datatype($entity['attributes'][$i]['datatype']);
					}
				}

// ------------- get origin---------------
				$sql = "SELECT * FROM fm_origin WHERE destination ='entity_" . $entity_id . '_' . $cat_id . "' AND destination_id = $id ORDER by origin DESC";
				$this->db->query($sql,__LINE__,__FILE__);

				$last_type = '';
				$i=-1;
				while ($this->db->next_record())
				{
					if($last_type != $this->db->f('origin'))
					{
						$i++;
					}
					$entity['origin'][$i]['type'] = $this->db->f('origin');
					$entity['origin'][$i]['link'] = $this->bocommon->get_origin_link($this->db->f('origin'));
					$entity['origin'][$i]['data'][]= array(
						'id'=> $this->db->f('origin_id'),
						'type'=> $this->db->f('origin')
						);

					$last_type = $this->db->f('origin');
				}
// ------------- end get origin---------------
// ------------- get destination---------------
				$sql = "SELECT * FROM fm_origin WHERE origin ='entity_" . $entity_id . '_' . $cat_id . "' AND origin_id = $id ORDER by destination DESC";
				$this->db->query($sql,__LINE__,__FILE__);

				$last_type = '';
				$i=-1;
				while ($this->db->next_record())
				{
					if($last_type != $this->db->f('destination'))
					{
						$i++;
					}
					$entity['destination'][$i]['type'] = $this->db->f('destination');
					$entity['destination'][$i]['link'] = $this->bocommon->get_origin_link($this->db->f('destination'));
					$entity['destination'][$i]['data'][]= array(
						'id'=> $this->db->f('destination_id'),
						'type'=> $this->db->f('destination')
						);

					$last_type=$this->db->f('destination');
				}
			}
// ------------- end get destination---------------
			return	$entity;
		}


		function check_entity($entity_id,$cat_id,$num)
		{
			$table='fm_entity_' . $entity_id .'_' . $cat_id;
			$this->db->query("SELECT count(*) FROM $table where num='$num'");

			$this->db->next_record();

			if ( $this->db->f(0))
			{
				return True;
			}
		}

		function generate_id($data)
		{
			$table='fm_entity_' . $data['entity_id'] .'_' . $data['cat_id'];
			$this->db->query("select max(id) as id from $table");
			$this->db->next_record();
			$id = $this->db->f('id')+1;

			return $id;
		}

		function generate_num($entity_id,$cat_id,$id)
		{
			$this->db->query("select prefix from fm_entity_category WHERE entity_id=$entity_id AND id=$cat_id ");
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


		function add($values,$values_attribute,$entity_id,$cat_id)
		{
			if(isset($values['street_name']) && $values['street_name'])
			{
				$address[]= $values['street_name'];
				$address[]= $values['street_number'];
				$address = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!isset($address) || !$address)
			{
				$address = $this->db->db_addslashes($values['location_name']);
			}

			if(isset($values['location']) && is_array($values['location']))
			{
				foreach ($values['location'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}

			if(isset($values['extra']) && is_array($values['extra']))
			{
				foreach ($values['extra'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
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

						if($entry['history'] == 1)
						{
							$history_set[$entry['attrib_id']] = $entry['value'];
						}
					}
				}
			}

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= "," . $this->bocommon->validate_db_insert($vals);
			}

			$table='fm_entity_' . $entity_id .'_' . $cat_id;
			$num=$this->generate_num($entity_id,$cat_id,$values['id']);
			$this->db->transaction_begin();

			$this->db->query("INSERT INTO $table (id,num,address,location_code,entry_date,user_id $cols) "
				. "VALUES ("
				. $values['id']. ",'"
				. $num . "','"
				. $address. "','"
				. $values['location_code']. "',"
				. time() . ","
				. $this->account. " $vals)",__LINE__,__FILE__);

			if (is_array($values['origin']))
			{
				if($values['origin'][0]['data'][0]['id'])
				{
					$this->db->query("INSERT INTO fm_origin (origin,origin_id,destination,destination_id,user_id,entry_date) "
						. "VALUES ('"
						. $values['origin'][0]['type']. "',"
						. $values['origin'][0]['data'][0]['id']. ","
						. "'entity_" . $entity_id .'_' . $cat_id . "',"
						. $values['id']. ","
						. $this->account. ","
						. time() . ")",__LINE__,__FILE__);
				}
			}

			if (isset($history_set) AND is_array($history_set))
			{
				$historylog	= CreateObject('property.historylog','entity_' . $entity_id .'_' . $cat_id);
				foreach ($history_set as $attrib_id => $new_value)
				{
					$historylog->add('SO',$values['id'],$new_value,False, $attrib_id);
				}
			}

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('Entity %1 has been saved',$values['id']));
			return $receipt;
		}

		function edit($values,$values_attribute,$entity_id,$cat_id)
		{
			if(isset($values['street_name']) && $values['street_name'])
			{
				$address[]= $values['street_name'];
				$address[]= $values['street_number'];
				$address	= implode(" ", $address);
			}

			if(!isset($address) || !$address)
			{
				$address = $values['location_name'];
			}

			$value_set=array(
				'location_code'	=> $values['location_code'],
				'address'	=> $this->db->db_addslashes($address)
				);

			$admin_location	= CreateObject('property.soadmin_location');
			$admin_location->read(false);

			// Delete old values for location - in case of moving up in the hierarchy
			for ($i = 1;$i < $admin_location->total_records + 1; $i++)
			{
				$value_set["loc{$i}"]	= false;
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

//_debug_array($values_attribute);
			$table = 'fm_entity_' . $entity_id .'_' . $cat_id;

			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['datatype']!='AB' && $entry['datatype']!='VENDOR')
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
						$this->db->query("select " . $entry['name'] . " from $table WHERE id=" . $values['id'],__LINE__,__FILE__);
						$this->db->next_record();
						$old_value = $this->db->f($entry['name']);
						if($entry['value'] != $old_value)
						{
							$history_set[$entry['attrib_id']] = array('value' => $entry['value'],
												'date'  => $this->bocommon->date_to_timestamp($entry['date']));
						}
					}
				}
			}

//_debug_array($history_set);
			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("UPDATE $table set $value_set WHERE id=" . $values['id'],__LINE__,__FILE__);

			if (isset($history_set) AND is_array($history_set))
			{
				$historylog	= CreateObject('property.historylog','entity_' . $entity_id .'_' . $cat_id);
				foreach ($history_set as $attrib_id => $history)
				{
					$historylog->add('SO',$values['id'],$history['value'],False, $attrib_id,$history['date']);
				}
			}

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('entity %1 has been edited',$values['num']));
			return $receipt;
		}

		function delete($entity_id,$cat_id,$id )
		{
			$table='fm_entity_' . $entity_id .'_' . $cat_id;
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM $table WHERE id=$id",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_origin WHERE destination ='entity_" . $entity_id . '_' . $cat_id . "' AND destination_id=$id",__LINE__,__FILE__);
			$this->db->transaction_commit();
		}

		function read_attrib_help($data)
		{	
			$entity_id = (isset($data['entity_id'])?$data['entity_id']:'');
			$cat_id = (isset($data['cat_id'])?$data['cat_id']:'');
			$attrib_id = (isset($data['attrib_id'])?$data['attrib_id']:'');
			
			$this->db->query("SELECT helpmsg FROM fm_entity_attribute WHERE entity_id ='$entity_id' AND cat_id ='$cat_id' AND id = '$attrib_id'");

			$this->db->next_record();
//			$helpmsg = str_replace("\n","<br>",stripslashes($this->db->f('helpmsg')));
			$helpmsg = stripslashes($this->db->f('helpmsg'));
			return $helpmsg;
		}
	}
?>
