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
	* @subpackage project
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_sorequest
	{
		function property_sorequest()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->soproject	= CreateObject('property.soproject');
			$this->historylog	= CreateObject('property.historylog','request');
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->join			= $this->bocommon->join;
			$this->like			= $this->bocommon->like;
			$this->interlink 	= CreateObject('property.interlink');
		}

		function read_priority_key()
		{
			$this->db->query("SELECT * FROM fm_request_condition_type",__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$priority_key[] = array(
					'id' 		=> $this->db->f('id'),
					'descr' 	=> $this->db->f('descr'),
					'priority_key' 	=> $this->db->f('priority_key')
					);
			}

			return $priority_key;
		}

		function update_priority_key($values)
		{

			while (is_array($values['priority_key']) && list($id,$priority_key) = each($values['priority_key']))
			{
				$this->db->query("UPDATE fm_request_condition_type SET priority_key = $priority_key WHERE id = $id",__LINE__,__FILE__);
			}

			$this->update_score();

			$receipt['message'][] = array('msg'=> lang('priority keys has been updated'));
			return $receipt;
		}


		function update_score($request_id='')
		{
			if($request_id)
			{
				$request[] = $request_id;
			}
			else
			{
				$this->db->query("SELECT id FROM fm_request",__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					$request[] = $this->db->f('id');
				}
			}

			while (is_array($request) && list(,$id) = each($request))
			{

				if($GLOBALS['phpgw_info']['server']['db_type']=='pgsql' || $GLOBALS['phpgw_info']['server']['db_type']=='postgres')
				{
					$sql = "UPDATE fm_request SET score = (SELECT sum(priority_key * ( degree * probability * ( consequence +1 )))  FROM fm_request_condition"
					 . " $this->join  fm_request_condition_type ON (fm_request_condition.condition_type = fm_request_condition_type.id) WHERE request_id = $id) WHERE fm_request.id = $id";

					$this->db->query($sql,__LINE__,__FILE__);
				}
				else
				{
					$sql = "SELECT sum(priority_key * ( degree * probability * ( consequence +1 ))) AS score FROM fm_request_condition"
					 . " $this->join  fm_request_condition_type ON (fm_request_condition.condition_type = fm_request_condition_type.id) WHERE request_id = $id";

					$this->db->query($sql,__LINE__,__FILE__);

					$this->db->next_record();
					$score = $this->db->f('score');
					$this->db->query("UPDATE fm_request SET score = $score WHERE id = $id",__LINE__,__FILE__);
				}

				$this->db->query("UPDATE fm_request SET score = score +10000 WHERE id = $id AND authorities_demands = 1",__LINE__,__FILE__);
			}
		}

		function select_status_list()
		{
			$this->db->query("SELECT id, descr FROM fm_request_status ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$status_entries[$i]['id']				= $this->db->f('id');
				$status_entries[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $status_entries;
		}

		function select_condition_type_list()
		{
			$this->db->query("SELECT id, descr FROM fm_request_condition_type ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$condition_type_list[$i]['id']		= $this->db->f('id');
				$condition_type_list[$i]['name']	= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $condition_type_list;
		}

		function select_conditions($request_id='',$condition_type_list='')
		{
			for ($i=0;$i<count($condition_type_list);$i++)
			{
				$this->db->query("SELECT degree,probability,consequence FROM fm_request_condition WHERE request_id=$request_id AND condition_type =" . $condition_type_list[$i]['id']);
				$this->db->next_record();
				$conditions[$i]['request_id']		= $request_id;
				$conditions[$i]['degree']		= $this->db->f('degree');
				$conditions[$i]['probability']		= $this->db->f('probability');
				$conditions[$i]['consequence']		= $this->db->f('consequence');
			}

			return $conditions;
		}


		function read($data)
		{
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter			= isset($data['filter'])?$data['filter']:'';
				$query			= isset($data['query'])?$data['query']:'';
				$sort			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
				$order			= isset($data['order'])?$data['order']:'';
				$cat_id			= isset($data['cat_id'])?$data['cat_id']:0;
				$status_id		= isset($data['status_id']) && $data['status_id'] ? $data['status_id']:0;
				$project_id		= isset($data['project_id'])?$data['project_id']:'';
				$allrows		= isset($data['allrows'])?$data['allrows']:'';
				$list_descr		= isset($data['list_descr'])?$data['list_descr']:'';
			}

			$sql = $this->bocommon->fm_cache('sql_request_' . !!$list_descr);

			if(!$sql)
			{
				$entity_table = 'fm_request';

				$cols .= $entity_table . '.location_code';
				$cols_return[] = 'location_code';

				$cols .= ",$entity_table.id as request_id";
				$cols_return[] 			= 'request_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'request_id';
				$uicols['descr'][]		= lang('Request');
				$uicols['statustext'][]		= lang('Request ID');

				$cols.= ",$entity_table.entry_date";
				$cols_return[] 			= 'entry_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'entry_date';
				$uicols['descr'][]		= lang('entry date');
				$uicols['statustext'][]		= lang('Request entry date');

				$cols.= ",$entity_table.title as title";
				$cols_return[] 			= 'title';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'title';
				$uicols['descr'][]		= lang('title');
				$uicols['statustext'][]		= lang('Request title');

				if($list_descr)
				{
					$cols.= ",$entity_table.descr as descr";
					$cols_return[] 			= 'descr';
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]		= 'descr';
					$uicols['descr'][]		= lang('descr');
					$uicols['statustext'][]		= lang('Request descr');
				}

				$cols.= ",$entity_table.budget as budget";
				$cols_return[] 			= 'budget';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'budget';
				$uicols['descr'][]		= lang('budget');
				$uicols['statustext'][]		= lang('Request budget');

				$cols.= ",$entity_table.coordinator";
				$cols_return[] 			= 'coordinator';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'coordinator';
				$uicols['descr'][]		= lang('Coordinator');
				$uicols['statustext'][]		= lang('Project coordinator');

				$cols.= ",$entity_table.score";
				$cols_return[] 			= 'score';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'score';
				$uicols['descr'][]		= lang('score');
				$uicols['statustext'][]		= lang('score');

				$sql	= $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols'=>$cols,'cols_return'=>$cols_return,
															'uicols'=>$uicols,'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,
															'query'=>$query,'force_location'=>true));

				$this->uicols		= $this->bocommon->uicols;
				$type_id			= $this->bocommon->type_id;
				$this->cols_extra	= $this->bocommon->cols_extra;

				$this->bocommon->fm_cache('sql_request_' . !!$list_descr, $sql);
				$this->bocommon->fm_cache('uicols_request_' . !!$list_descr, $this->uicols);
				$this->bocommon->fm_cache('cols_return_request_' . !!$list_descr, $cols_return);
				$this->bocommon->fm_cache('type_id_request_' . !!$list_descr, $type_id);
				$this->bocommon->fm_cache('cols_extra_request_' . !!$list_descr, $this->cols_extra);
			}
			else
			{
				$this->uicols		= $this->bocommon->fm_cache('uicols_request_' . !!$list_descr);
				$cols_return		= $this->bocommon->fm_cache('cols_return_request_' . !!$list_descr);
				$type_id			= $this->bocommon->fm_cache('type_id_request_' . !!$list_descr);
				$this->cols_extra	= $this->bocommon->fm_cache('cols_extra_request_' . !!$list_descr);
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_request.id ASC';
			}

			$where = 'WHERE';
			$filtermethod = '';

			$GLOBALS['phpgw']->config->read_repository();
			if(isset($GLOBALS['phpgw']->config->config_data['acl_at_location']) && $GLOBALS['phpgw']->config->config_data['acl_at_location'])
			{
				$access_location = $this->bocommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod = " WHERE fm_request.loc1 in ('" . implode("','", $access_location) . "')";
				$where= 'AND';
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " $where fm_request.category='$cat_id' ";
				$where = 'AND';
			}

			if ($status_id)
			{
				$filtermethod .= " $where  fm_request.status='$status_id' ";
				$where = 'AND';
			}

			if ($filter)
			{
				$filtermethod .= " $where fm_request.coordinator='$filter' ";
				$where = 'AND';
			}

			if ($project_id)// lookup requests not already allocated to projects
			{
				$filtermethod .= " $where project_id is NULL ";
				$where = 'AND';
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where (fm_request.title $this->like '%$query%' or fm_request.address $this->like '%$query%' or fm_request.location_code $this->like '%$query%')";
			}

			$sql .= " $filtermethod $querymethod";

			$this->db->fetchmode = 'ASSOC';
			$sql2 = 'SELECT count(*) as cnt ' . substr($sql,strripos($sql,'from'));
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$this->total_records = $this->db->f('cnt');

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$j=0;
			$request_list = array();

			while ($this->db->next_record())
			{
				for ($i=0;$i<count($cols_return);$i++)
				{
					$request_list[$j][$cols_return[$i]] = $this->db->f($cols_return[$i], true);
				}
				$location_code=	$request_list[$j]['location_code'];
				$location = split('-',$location_code);
				for ($m=0;$m<count($location);$m++)
				{
					$request_list[$j]['loc' . ($m+1)] = $location[$m];
					$request_list[$j]['query_location']['loc' . ($m+1)]=implode("-", array_slice($location, 0, ($m+1)));
				}

				$j++;
			}
//_debug_array($request_list);
			return $request_list;
		}

		function read_single($request_id)
		{
			$sql = "SELECT * from fm_request where id='$request_id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$request['request_id']			= $this->db->f('id');
				$request['title']			= $this->db->f('title');
				$request['location_code']		= $this->db->f('location_code');
				$request['descr']			= $this->db->f('descr');
				$request['status']			= $this->db->f('status');
				$request['budget']			= (int)$this->db->f('budget');
				$request['tenant_id']			= $this->db->f('tenant_id');
				$request['owner']			= $this->db->f('owner');
				$request['coordinator']			= $this->db->f('coordinator');
				$request['access']			= $this->db->f('access');
				$request['start_date']			= $this->db->f('start_date');
				$request['end_date']			= $this->db->f('end_date');
				$request['cat_id']			= $this->db->f('category');
				$request['branch_id']			= $this->db->f('branch_id');
				$request['authorities_demands']		= $this->db->f('authorities_demands');
				$request['score']			= $this->db->f('score');
				$request['p_num']			= $this->db->f('p_num');
				$request['p_entity_id']			= $this->db->f('p_entity_id');
				$request['p_cat_id']			= $this->db->f('p_cat_id');
				$request['contact_phone']		= $this->db->f('contact_phone');

				$request['power_meter']	= $this->soproject->get_power_meter($this->db->f('location_code'));
			}

			return $request;
		}

		function request_workorder_data($request_id = '')
		{
			$this->db->query("select budget, id as workorder_id, vendor_id from fm_workorder where request_id='$request_id'");
			while ($this->db->next_record())
			{
				$budget[] = array(
					'workorder_id'	=> $this->db->f('workorder_id'),
					'budget'	=> sprintf("%01.2f",$this->db->f('budget')),
					'vendor_id'	=> $this->db->f('vendor_id')
					);
			}
			return $budget;
		}


		function increment_request_id()
		{
			$this->db->query("update fm_idgenerator set value = value + 1 where name = 'request'");
		}

		function next_id()
		{
			$this->db->query("select value from fm_idgenerator where name = 'request'");
			$this->db->next_record();
			$id = $this->db->f('value')+1;
			return $id;
		}

		function add($request)
		{
//_debug_array($request);
			while (is_array($request['location']) && list($input_name,$value) = each($request['location']))
			{
				if($value)
				{
					$cols[] = $input_name;
					$vals[] = $value;
				}
			}

			while (is_array($request['extra']) && list($input_name,$value) = each($request['extra']))
			{
				if($value)
				{
					$cols[] = $input_name;
					$vals[] = $value;
				}
			}

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= ",'" . implode("','", $vals) . "'";
			}

			if($request['street_name'])
			{
				$address[]= $request['street_name'];
				$address[]= $request['street_number'];
				$address	= $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($request['location_name']);
			}

			$request['descr'] = $this->db->db_addslashes($request['descr']);
			$request['name'] = $this->db->db_addslashes($request['name']);
			$request['title'] = $this->db->db_addslashes($request['title']);

			$values= array(
				$request['request_id'],
				$request['title'],
				$this->account,
				$request['cat_id'],
				$request['descr'],
				$request['location_code'],
				$address,
				time(),
				$request['budget'],
				$request['status'],
				$request['branch_id'],
				$request['coordinator'],
				$request['authorities_demands']);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->transaction_begin();

			$this->db->query("insert into fm_request (id,title,owner,category,descr,location_code,"
				. "address,entry_date,budget,status,branch_id,coordinator,"
				. "authorities_demands  $cols) "
				. "VALUES ($values $vals )",__LINE__,__FILE__);

			while (is_array($request['condition']) && list($condition_type,$value_type) = each($request['condition']))
			{
				$this->db->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
					. "VALUES ('"
					. $request['request_id']. "','"
					. $condition_type . "',"
					. $value_type['degree']. ","
					. $value_type['probability']. ","
					. $value_type['consequence']. ","
					. $this->account . ","
					. time() . ")",__LINE__,__FILE__);
			}

			$this->update_score($request['request_id']);


			if($request['extra']['contact_phone'] && $request['extra']['tenant_id'])
			{
				$this->db->query("update fm_tenant set contact_phone='". $request['extra']['contact_phone']. "' where id='". $request['extra']['tenant_id']. "'",__LINE__,__FILE__);
			}

			if ($request['power_meter'] )
			{
				$this->soproject->update_power_meter($request['power_meter'],$request['location_code'],$address);
			}

			if(is_array($request['origin']) && isset($request['origin'][0]['data'][0]['id']))
			{
				$interlink_data = array
				(
					'location1_id'		=> $GLOBALS['phpgw']->locations->get_id('property', $request['origin'][0]['location']),
					'location1_item_id' => $request['origin'][0]['data'][0]['id'],
					'location2_id'		=> $GLOBALS['phpgw']->locations->get_id('property', '.project.request'),			
					'location2_item_id' => $request['request_id'],
					'account_id'		=> $this->account
				);
					
				$this->interlink->add($interlink_data,$this->db);
			}

			if($this->db->transaction_commit())
			{
				$this->increment_request_id();
				$this->historylog->add('SO',$request['request_id'],$request['status']);
				$this->historylog->add('TO',$request['request_id'],$request['cat_id']);
				$this->historylog->add('CO',$request['request_id'],$request['coordinator']);
				$receipt['message'][] = array('msg'=>lang('request %1 has been saved',$request['request_id']));
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('request %1 has not been saved',$request['request_id']));
			}
			return $receipt;
		}

		function edit($request)
		{
			while (is_array($request['location']) && list($input_name,$value) = each($request['location']))
			{
				$vals[]	= "$input_name = '$value'";
			}

			while (is_array($request['extra']) && list($input_name,$value) = each($request['extra']))
			{
				$vals[]	= "$input_name = '$value'";
			}

			if($vals)
			{
				$vals	= "," . implode(",",$vals);
			}

			if($request['street_name'])
			{
				$address[]= $request['street_name'];
				$address[]= $request['street_number'];
				$address = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($request['location_name']);
			}


			$request['descr'] = $this->db->db_addslashes($request['descr']);
			$request['name'] = $this->db->db_addslashes($request['name']);
			$request['title'] = $this->db->db_addslashes($request['title']);
//_debug_array($request);

			$value_set=array(
				'status'		=> $request['status'],
				'category'		=> $request['cat_id'],
				'start_date'		=> $request['start_date'],
				'end_date'		=> $request['end_date'],
				'coordinator'		=> $request['coordinator'],
				'descr'			=> $request['descr'],
				'budget'		=> (int)$request['budget'],
				'location_code'		=> $request['location_code'],
				'address'		=> $address,
				'authorities_demands' => $request['authorities_demands']
				);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("SELECT status,category,coordinator FROM fm_request where id='" .$request['request_id']."'",__LINE__,__FILE__);
			$this->db->next_record();

			$old_status = $this->db->f('status');
			$old_category = $this->db->f('category');
			$old_coordinator = $this->db->f('coordinator');

			$this->db->query("UPDATE fm_request set $value_set $vals WHERE id= '" . $request['request_id'] ."'",__LINE__,__FILE__);

			$this->db->query("DELETE FROM fm_request_condition WHERE request_id='" . $request['request_id'] . "'",__LINE__,__FILE__);
			while (is_array($request['condition']) && list($condition_type,$value_type) = each($request['condition']))
			{
				$this->db->query("INSERT INTO fm_request_condition (request_id,condition_type,degree,probability,consequence,user_id,entry_date) "
					. "VALUES ('"
					. $request['request_id']. "','"
					. $condition_type . "',"
					. $value_type['degree']. ","
					. $value_type['probability']. ","
					. $value_type['consequence']. ","
					. $this->account . ","
					. time() . ")",__LINE__,__FILE__);
			}

			$this->update_score($request['request_id']);

			if($request['extra']['contact_phone'] && $request['extra']['tenant_id'])
			{
				$this->db->query("update fm_tenant set contact_phone='". $request['extra']['contact_phone']. "' where id='". $request['extra']['tenant_id']. "'",__LINE__,__FILE__);
			}

			if ($request['power_meter'] )
			{
				$this->soproject->update_power_meter($request['power_meter'],$request['location_code'],$address);
			}

			if($this->db->transaction_commit())
			{
				if ($old_status != $request['status'])
				{
					$this->historylog->add('S',$request['request_id'],$request['status']);
				}
				if ($old_category != $request['cat_id'])
				{
					$this->historylog->add('T',$request['request_id'],$request['cat_id']);
				}
				if ($old_coordinator != $request['coordinator'])
				{
					$this->historylog->add('C',$request['request_id'],$request['coordinator']);
				}

				$receipt['message'][] = array('msg'=>lang('request %1 has been edited',$request['request_id']));
			}
			else
			{
				$receipt['message'][] = array('msg'=>lang('request %1 has not been edited',$request['request_id']));
			}
			return $receipt;

		}

		function delete($request_id )
		{
			$request_id = (int) $request_id;
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_request WHERE id = {$request_id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_request_condition WHERE request_id = {$request_id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_request_history  WHERE  history_record_id = {$request_id}",__LINE__,__FILE__);
		//	$this->db->query("DELETE FROM fm_origin WHERE destination = 'request' AND destination_id='" . $request_id . "'",__LINE__,__FILE__);
			$this->interlink->delete_at_target('property', '.project.request', $request_id, $this->db);
			$this->db->transaction_commit();
		}
	}

