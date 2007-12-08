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
 	* @version $Id: class.soproject.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soproject
	{

		function property_soproject()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db =& $GLOBALS['phpgw']->db;
			$this->db2 = clone($this->db);

			$this->like =& $this->db->like;
			$this->join =& $this->db->join;
			$this->left_join = " LEFT JOIN ";

			$this->acl 		= CreateObject('phpgwapi.acl');
			$this->grants	= $this->acl->get_grants('property','.project');
		}


		function read_single_project_category($id='')
		{
			$this->db->query("SELECT descr FROM fm_workorder_category where id='$id' ");
			$this->db->next_record();
			return $this->db->f('descr');
		}

		function select_status_list()
		{
			$this->db->query("SELECT id, descr FROM fm_workorder_status ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$status_entries[$i]['id']				= $this->db->f('id');
				$status_entries[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $status_entries;
		}

		function select_branch_list()
		{
			$this->db->query("SELECT id, descr FROM fm_branch ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$branch_entries[$i]['id']				= $this->db->f('id');
				$branch_entries[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $branch_entries;
		}

		function select_key_location_list()
		{
			$this->db->query("SELECT id, descr FROM fm_key_loc ORDER BY descr ");

			$key_location_entries = '';
			$i = 0;
			while ($this->db->next_record())
			{
				$key_location_entries[$i]['id']				= $this->db->f('id');
				$key_location_entries[$i]['name']			= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $key_location_entries;
		}

		function read($data)
		{
			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$filter	= $data['filter']?$data['filter']:'all';
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$status_id = (isset($data['status_id'])?$data['status_id']:'');
				$start_date = (isset($data['start_date'])?$data['start_date']:'');
				$end_date = (isset($data['end_date'])?$data['end_date']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$wo_hour_cat_id = (isset($data['wo_hour_cat_id'])?$data['wo_hour_cat_id']:'');
			}

			$sql = $this->bocommon->fm_cache('sql_project_' . !!$wo_hour_cat_id);

			if(!$sql)
			{
				$entity_table = 'fm_project';

				$cols = $entity_table . '.location_code';
				$cols_return[] = 'location_code';

				$cols .= ",$entity_table.id as project_id";
				$cols_return[] 				= 'project_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'project_id';
				$uicols['descr'][]			= lang('Project');
				$uicols['statustext'][]		= lang('Project ID');

				$cols.= ",$entity_table.start_date";
				$cols_return[] 				= 'start_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'start_date';
				$uicols['descr'][]			= lang('start date');
				$uicols['statustext'][]		= lang('Project start date');

				$cols.= ",$entity_table.name as name";
				$cols_return[] 				= 'name';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'name';
				$uicols['descr'][]			= lang('name');
				$uicols['statustext'][]		= lang('Project name');

				$cols.= ",account_lid as coordinator";
				$cols_return[] 				= 'coordinator';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'coordinator';
				$uicols['descr'][]			= lang('Coordinator');
				$uicols['statustext'][]		= lang('Project coordinator');

				$cols.= ",(fm_project.budget + fm_project.reserve) as budget";
				$cols_return[] 				= 'budget';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'budget';
				$uicols['descr'][]			= lang('Project budget');
				$uicols['statustext'][]		= lang('Project budget');

				$cols .= ',sum(fm_workorder.combined_cost) as combined_cost';
				$cols_return[] = 'combined_cost';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'combined_cost';
				$uicols['descr'][]			= lang('Sum	workorder');
				$uicols['statustext'][]		= lang('Cost - either budget or calculation');

				$cols .= ',(sum(fm_workorder.act_mtrl_cost) + sum(fm_workorder.act_vendor_cost)) as actual_cost';
				$cols_return[] = 'actual_cost';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'actual_cost';
				$uicols['descr'][]			= lang('Actual cost');
				$uicols['statustext'][]		= lang('Actual cost - paid so far');


				$cols.= ",$entity_table.user_id";

				$joinmethod = " $this->join  phpgw_accounts ON ($entity_table.coordinator = phpgw_accounts.account_id))";
				$paranthesis ='(';

				$joinmethod .= " $this->left_join fm_workorder ON ($entity_table.id = fm_workorder.project_id))";
				$paranthesis .='(';

				//----- wo_hour_status

				if($wo_hour_cat_id)
				{
					$joinmethod .= " $this->join fm_wo_hours ON (fm_workorder.id = fm_wo_hours.workorder_id))";
					$paranthesis .='(';

					$joinmethod .= " $this->join fm_wo_hours_category ON (fm_wo_hours.category = fm_wo_hours_category.id))";
					$paranthesis .='(';
				}

				//----- wo_hour_status

				$sql	= $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols'=>$cols,'cols_return'=>$cols_return,
										'uicols'=>$uicols,'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,'query'=>$query,'force_location'=>true));

				$this->bocommon->fm_cache('sql_project_' . !!$wo_hour_cat_id,$sql);

				$this->uicols		= $this->bocommon->uicols;
				$cols_return		= $this->bocommon->cols_return;
				$type_id		= $this->bocommon->type_id;
				$this->cols_extra	= $this->bocommon->cols_extra;

				$this->bocommon->fm_cache('uicols_project_' . !!$wo_hour_cat_id,$this->uicols);
				$this->bocommon->fm_cache('cols_return_project_' . !!$wo_hour_cat_id,$cols_return);
				$this->bocommon->fm_cache('type_id_project_' . !!$wo_hour_cat_id,$type_id);
				$this->bocommon->fm_cache('cols_extra_project_' . !!$wo_hour_cat_id,$this->cols_extra);

			}
			else
			{
				$this->uicols		= $this->bocommon->fm_cache('uicols_project_' . !!$wo_hour_cat_id);
				$cols_return		= $this->bocommon->fm_cache('cols_return_project_' . !!$wo_hour_cat_id);
				$type_id		= $this->bocommon->fm_cache('type_id_project_' . !!$wo_hour_cat_id);
				$this->cols_extra	= $this->bocommon->fm_cache('cols_extra_project_' . !!$wo_hour_cat_id);
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_project.id DESC';
			}

			$where= 'WHERE';

			$filtermethod = '';
			if ($cat_id > 0)
			{
				$filtermethod .= " $where fm_project.category=$cat_id ";
				$where= 'AND';
			}

			if ($status_id)
			{
				$filtermethod .= " $where fm_project.status='$status_id' ";
				$where= 'AND';
			}

			if($wo_hour_cat_id)
			{
				$filtermethod .= " $where fm_wo_hours_category.id=$wo_hour_cat_id ";
				$where= 'AND';
			}

			$group_method = ' GROUP BY fm_project.location_code,fm_project.id,fm_project.start_date,fm_project.name,phpgw_accounts.account_lid,fm_project.user_id,fm_project.address,fm_project.budget,fm_project.reserve';

			if ($filter=='all')
			{
				if (is_array($this->grants))
				{
					$grants = $this->grants;
					while (list($user) = each($grants))
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$filtermethod .= " $where (fm_project.user_id IN(" . implode(',',$public_user_list) . "))";

					$where= 'AND';
				}
			}
			else
			{
				$filtermethod .= " $where fm_project.user_id=$filter ";
				$where= 'AND';
			}

			if ($start_date)
			{
				$filtermethod .= " $where fm_project.start_date >= $start_date AND fm_project.start_date <= $end_date ";
				$where= 'AND';
			}


			if($query)
			{
				$query = str_replace(",",'.',$query);
				if(stristr($query, '.'))
				{
					$query=explode(".",$query);
					$querymethod = " $where (fm_project.loc1='" . $query[0] . "' AND fm_project.loc".$type_id."='" . $query[1] . "')";
				}
				else
				{
					$query = ereg_replace("'",'',$query);
					$query = ereg_replace('"','',$query);
					$querymethod = " $where (fm_project.name $this->like '%$query%' or fm_project.address $this->like '%$query%' or fm_project.location_code $this->like '%$query%' or fm_project.id $this->like '%$query%')";
				}
			}
			else
			{
				$querymethod = '';
			}

			$sql .= " $filtermethod $querymethod";
//echo substr($sql,strripos($sql,'from'));
			if($GLOBALS['phpgw_info']['server']['db_type']=='postgres')
			{
				$sql2 = 'SELECT count(*) FROM (SELECT fm_project.id ' . substr($sql,strripos($sql,'from'))  . ' GROUP BY fm_project.id) as cnt';
				$this->db->query($sql2,__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f(0);
			}
			else
			{
				$sql2 = 'SELECT fm_project.id ' . substr($sql,strripos($sql,'from'))  . ' GROUP BY fm_project.id';
				$this->db->query($sql2,__LINE__,__FILE__);
				$this->total_records = $this->db->num_rows();
			}

			$sql .= " $group_method";
			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$project_list = array();
			$j=0;
			$k=count($cols_return);
			while ($this->db->next_record())
			{
				for ($i=0;$i<$k;$i++)
				{
					$project_list[$j][$cols_return[$i]] = stripslashes($this->db->f($cols_return[$i]));
					$project_list[$j]['grants'] = (int)$this->grants[$this->db->f('user_id')];
				}

				$location_code=	$this->db->f('location_code');
				$location = split('-',$location_code);
				$n=count($location);
				for ($m=0;$m<$n;$m++)
				{
					$project_list[$j]['loc' . ($m+1)] = $location[$m];
					$project_list[$j]['query_location']['loc' . ($m+1)]=implode("-", array_slice($location, 0, ($m+1)));
				}

				$j++;
			}

//_debug_array($project_list);
			return $project_list;
		}

		function get_meter_table()
		{
			$config = CreateObject('phpgwapi.config','property');
			$config->read_repository();
			return isset($config->config_data['meter_table'])?$config->config_data['meter_table']:'';
		}

		function read_single($project_id)
		{
			$sql = "SELECT * from fm_project where id='$project_id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$project['project_id']			= $this->db->f('id');
				$project['title']			= $this->db->f('title');
				$project['name']			= $this->db->f('name');
				$project['location_code']		= $this->db->f('location_code');
				$project['key_fetch']			= $this->db->f('key_fetch');
				$project['key_deliver']			= $this->db->f('key_deliver');
				$project['other_branch']		= $this->db->f('other_branch');
				$project['key_responsible']		= $this->db->f('key_responsible');
				$project['descr']			= stripslashes($this->db->f('descr'));
				$project['status']			= $this->db->f('status');
				$project['budget']			= (int)$this->db->f('budget');
				$project['reserve']			= (int)$this->db->f('reserve');
				$project['tenant_id']			= $this->db->f('tenant_id');
				$project['user_id']			= $this->db->f('user_id');
				$project['coordinator']			= $this->db->f('coordinator');
				$project['access']			= $this->db->f('access');
				$project['start_date']			= $this->db->f('start_date');
				$project['end_date']			= $this->db->f('end_date');
				$project['cat_id']			= $this->db->f('category');
				$project['grants'] 			= (int)$this->grants[$this->db->f('user_id')];
				$project['p_num']			= $this->db->f('p_num');
				$project['p_entity_id']			= $this->db->f('p_entity_id');
				$project['p_cat_id']			= $this->db->f('p_cat_id');
				$project['contact_phone']		= $this->db->f('contact_phone');

				$project['power_meter']	= $this->get_power_meter($this->db->f('location_code'));
			}

			$sql = "SELECT * FROM fm_origin WHERE destination = 'project' AND destination_id='$project_id' ORDER by origin DESC  ";

			$this->db->query($sql,__LINE__,__FILE__);

			$last_type = false;
			$i=-1;
			while ($this->db->next_record())
			{
				if($last_type != $this->db->f('origin'))
				{
					$i++;
				}
				$project['origin'][$i]['type'] = $this->db->f('origin');
				$project['origin'][$i]['link'] = $this->bocommon->get_origin_link($this->db->f('origin'));
				$project['origin'][$i]['data'][]= array(
					'id'=> $this->db->f('origin_id'),
					'type'=> $this->db->f('origin')
					);

				$last_type=$this->db->f('origin');
			}

//_debug_array($project);
				return $project;
		}

		function get_ticket($project_id = '')
		{
			$sql = "SELECT * FROM fm_origin WHERE origin ='tts' AND destination = 'project' AND destination_id='$project_id' ORDER by origin DESC  ";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('origin_id');
		}

		function get_power_meter($location_code = '')
		{
			if(!$meter_table = $this->get_meter_table())
			{
				return false;
			}

			$this->db2->query("SELECT ext_meter_id as power_meter FROM $meter_table where location_code='$location_code' and category='1'",__LINE__,__FILE__);

			$this->db2->next_record();

			return $this->db2->f('power_meter');
		}

		function project_workorder_data($project_id = '')
		{
			$budget = array();
			$this->db->query("SELECT act_mtrl_cost, act_vendor_cost, budget, fm_workorder.id as workorder_id, vendor_id, calculation,rig_addition,addition,deviation,charge_tenant,fm_workorder_status.descr as status"
			." FROM fm_workorder $this->join fm_workorder_status ON fm_workorder.status = fm_workorder_status.id WHERE project_id='$project_id'");
			while ($this->db->next_record())
			{
				$budget[] = array(
					'workorder_id'		=> $this->db->f('workorder_id'),
					'budget'			=> $this->db->f('budget'),
					'deviation'			=> $this->db->f('deviation'),
					'calculation'		=> ($this->db->f('calculation')*(1+$this->db->f('addition')/100))+$this->db->f('rig_addition'),
					'vendor_id'			=> $this->db->f('vendor_id'),
					'act_mtrl_cost'		=> $this->db->f('act_mtrl_cost'),
					'act_vendor_cost'	=> $this->db->f('act_vendor_cost'),
					'charge_tenant'		=> $this->db->f('charge_tenant'),
					'status'			=> $this->db->f('status')
					);
			}
			return $budget;
		}

		function branch_p_list($project_id = '')
		{
			$selected = array();
			$this->db2->query("SELECT branch_id from fm_projectbranch WHERE project_id=" .  (int)$project_id ,__LINE__,__FILE__);
			while ($this->db2->next_record())
			{
				$selected[] = array('branch_id' => $this->db2->f('branch_id'));
			}
			return $selected;
		}

		function increment_project_id()
		{
			$this->db->query("update fm_idgenerator set value = value + 1 where name = 'project'");
		}

		function next_project_id()
		{
			$this->db->query("select value from fm_idgenerator where name = 'project'");
			$this->db->next_record();
			$project_id = $this->db->f('value')+1;
			return $project_id;
		}

		function add($project)
		{
			$historylog	= CreateObject('property.historylog','project');

			while (is_array($project['location']) && list($input_name,$value) = each($project['location']))
			{
				if($value)
				{
					$cols[] = $input_name;
					$vals[] = $value;
				}
			}

			while (is_array($project['extra']) && list($input_name,$value) = each($project['extra']))
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

			if($project['street_name'])
			{
				$address[]= $project['street_name'];
				$address[]= $project['street_number'];
				$address = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($project['location_name']);
			}

			$project['descr'] = $this->db->db_addslashes($project['descr']);
			$project['name'] = $this->db->db_addslashes($project['name']);

			$values= array(
				$project['project_id'],
				$project['name'],
				'public',
				$project['cat_id'],
				time(),
				$project['start_date'],
				$project['end_date'],
				$project['coordinator'],
				$project['status'],
				$project['descr'],
				$project['budget'],
				$project['reserve'],
				$project['location_code'],
				$address,
				$project['key_deliver'],
				$project['key_fetch'],
				$project['other_branch'],
				$project['key_responsible'],
				$this->account);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_project (id,name,access,category,entry_date,start_date,end_date,coordinator,status,"
				. "descr,budget,reserve,location_code,address,key_deliver,key_fetch,other_branch,key_responsible,user_id $cols) "
				. "VALUES ($values $vals )",__LINE__,__FILE__);

			if($project['extra']['contact_phone'] && $project['extra']['tenant_id'])
			{
				$this->db->query("update fm_tenant set contact_phone='". $project['extra']['contact_phone']. "' where id='". $project['extra']['tenant_id']. "'",__LINE__,__FILE__);
			}

			if (isset($project['power_meter']) && $project['power_meter'])
			{
				$this->update_power_meter($project['power_meter'],$project['location_code'],$address);
			}

			if (count($project['branch']) != 0)
			{
				while($branch=each($project['branch']))
				{
					$this->db->query("insert into fm_projectbranch (project_id,branch_id) values ('" . $project['project_id']. "','$branch[1]')",__LINE__,__FILE__);
				}
			}

			if(is_array($project['origin']))
			{
				if($project['origin'][0]['data'][0]['id'])
				{
					$this->db->query("INSERT INTO  fm_origin (origin,origin_id,destination,destination_id,user_id,entry_date) "
						. "VALUES ('"
						. $project['origin'][0]['type']. "','"
						. $project['origin'][0]['data'][0]['id']. "',"
						. "'project',"
						. $project['project_id']. ","
						. $this->account . ","
						. time() . ")",__LINE__,__FILE__);
				}
			}

			if($this->db->transaction_commit())
			{
				$this->increment_project_id();
				$historylog->add('SO',$project['project_id'],$project['status']);
				$historylog->add('TO',$project['project_id'],$project['cat_id']);
				$historylog->add('CO',$project['project_id'],$project['coordinator']);
				if ($project['remark'])
				{
					$historylog->add('RM',$project['project_id'],$project['remark']);
				}

				$receipt['message'][] = array('msg'=>lang('project %1 has been saved',$project['project_id']));
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('the project has not been saved'));
			}
			return $receipt;
		}

		function update_power_meter($power_meter,$location_code,$address)
		{
			if(!$meter_table = $this->get_meter_table())
			{
				return;
			}

			$location=explode('-',$location_code);

			$i=1;
			if (isset($location) AND is_array($location))
			{
				foreach($location as $location_entry)
				{

					$cols[] = 'loc' . $i;
					$vals[] = $location_entry;

					$i++;
				}

			}

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= ",'" . implode("','", $vals) . "'";
			}


			$this->db->query("SELECT count(*) FROM $meter_table where location_code='$location_code' and category=1",__LINE__,__FILE__);

			$this->db->next_record();

			if ( $this->db->f(0))
			{
				$this->db->query("update $meter_table set ext_meter_id='$power_meter',address='$address' where location_code='$location_code' and category='1'",__LINE__,__FILE__);
			}
			else
			{
				$id = $this->bocommon->next_id($meter_table);

				$meter_id	= $this->generate_meter_id($meter_table);
				$this->db->query("insert into $meter_table (id,num,ext_meter_id,category,location_code,entry_date,user_id,address $cols) "
					. "VALUES ('"
					. $id. "','"
					. $meter_id. "','"
					. $power_meter. "',"
					. "1,'"
					. $location_code. "',"
					. time() . ",$this->account, '$address' $vals)",__LINE__,__FILE__);
			}
		}

		function generate_meter_id($meter_table)
		{
			$prefix = 'meter';
			$pos	= strlen($prefix);
			$this->db->query("select max(num) from $meter_table where num $this->like ('$prefix%')");
			$this->db->next_record();

			$max = $this->bocommon->add_leading_zero(substr($this->db->f(0),$pos));

			$meter_id= $prefix . $max;
			return $meter_id;
		}

		function edit($project)
		{
			$historylog	= CreateObject('property.historylog','project');

			while (is_array($project['location']) && list($input_name,$value) = each($project['location']))
			{
				$vals[]	= "$input_name = '$value'";
			}

			while (is_array($project['extra']) && list($input_name,$value) = each($project['extra']))
			{
				$vals[]	= "$input_name = '$value'";
			}

			if($vals)
			{
				$vals	= "," . implode(",",$vals);
			}

			if($project['street_name'])
			{
				$address[]= $project['street_name'];
				$address[]= $project['street_number'];
				$address = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($project['location_name']);
			}

			$project['descr'] = $this->db->db_addslashes($project['descr']);
			$project['name'] = $this->db->db_addslashes($project['name']);

			$value_set=array(
				'name'			=> $project['name'],
				'status'		=> $project['status'],
				'category'		=> $project['cat_id'],
				'start_date'		=> $project['start_date'],
				'end_date'		=> $project['end_date'],
				'coordinator'		=> $project['coordinator'],
				'descr'			=> $project['descr'],
				'budget'		=> (int)$project['budget'],
				'reserve'		=> (int)$project['reserve'],
				'key_deliver'		=> $project['key_deliver'],
				'key_fetch'		=> $project['key_fetch'],
				'other_branch'		=> $project['other_branch'],
				'key_responsible'	=> $project['key_responsible'],
				'location_code'		=> $project['location_code'],
				'address'		=> $address
				);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("SELECT status,category,coordinator FROM fm_project where id='" .$project['project_id']."'",__LINE__,__FILE__);
			$this->db->next_record();
			$old_status = $this->db->f('status');
			$old_category = (int)$this->db->f('category');
			$old_coordinator = (int)$this->db->f('coordinator');

			$this->db->query("UPDATE fm_project set $value_set $vals WHERE id= '" . $project['project_id'] ."'",__LINE__,__FILE__);

			if($project['extra']['contact_phone'] && $project['extra']['tenant_id'])
			{
				$this->db->query("update fm_tenant set contact_phone='". $project['extra']['contact_phone']. "' where id='". $project['extra']['tenant_id']. "'",__LINE__,__FILE__);
			}

			if (isset($project['power_meter']) && $project['power_meter'])
			{
				$this->update_power_meter($project['power_meter'],$project['location_code'],$address);
			}
	// -----------------which branch is represented
			$this->db->query("delete from fm_projectbranch where project_id='" . $project['project_id'] ."'",__LINE__,__FILE__);

			if (count($project['branch']) != 0)
			{
				while($branch=each($project['branch']))
				{
					$this->db->query("insert into fm_projectbranch (project_id,branch_id) values ('" . $project['project_id']. "','$branch[1]')",__LINE__,__FILE__);
				}
			}

			if($project['delete_request'])
			{
				$receipt = $this->delete_request_from_project($project['delete_request'],$project['project_id']);

			}

			$this->update_request_status($project['project_id'],$project['status'],$project['cat_id'],$project['coordinator']);

			if (($old_status != $project['status']) || $project['confirm_status'])
			{
				$this->db2->query("SELECT id from fm_workorder WHERE project_id=" .  (int)$project['project_id'] ,__LINE__,__FILE__);
				while ($this->db2->next_record())
				{
					$workorder[] = $this->db2->f('id');
				}

				if (isset($workorder) AND is_array($workorder))
				{
					$historylog_workorder	= CreateObject('property.historylog','workorder');
				}

				if($old_status != $project['status'])
				{
					$historylog->add('S',$project['project_id'],$project['status']);

					$this->db->query("UPDATE fm_workorder set status='". $project['status'] . "' WHERE project_id= '" . $project['project_id'] ."'",__LINE__,__FILE__);

					if (isset($workorder) AND is_array($workorder))
					{
						foreach($workorder as $workorder_id)
						{
							$historylog_workorder->add('S',$workorder_id,$project['status']);
						}
					}
					$receipt['notice_owner'][]=lang('Status changed') . ': ' . $project['status'];
				}
				elseif($project['confirm_status'])
				{
					$historylog->add('SC',$project['project_id'],$project['status']);

					if (isset($workorder) AND is_array($workorder))
					{
						foreach($workorder as $workorder_id)
						{
							$historylog_workorder->add('SC',$workorder_id,$project['status']);
						}
					}
					$receipt['notice_owner'][]=lang('Status confirmed') . ': ' . $project['status'];
				}

			}
			
			if ($old_category != $project['cat_id'])
			{
				$historylog->add('T',$project['project_id'],$project['cat_id']);
			}
			if ($old_coordinator != $project['coordinator'])
			{
				$historylog->add('C',$project['project_id'],$project['coordinator']);
				$receipt['notice_owner'][]=lang('Coordinator changed') . ': ' . $GLOBALS['phpgw']->accounts->id2name($project['coordinator']);
			}

			if ($project['remark'])
			{
				$historylog->add('RM',$project['project_id'],$project['remark']);
			}

			$receipt['message'][] = array('msg'=>lang('project %1 has been edited',$project['project_id']));

			$this->db->transaction_commit();

			return $receipt;

		}


		function delete_request_from_project($request_id,$project_id)
		{
			for ($i=0;$i<count($request_id);$i++)
			{
				$this->db2->query("update fm_request set project_id = NULL where id='". $request_id[$i] . "'",__LINE__,__FILE__);
				$this->db->query("DELETE FROM fm_origin WHERE destination ='project' AND origin_id='" . $request_id[$i] . "' AND origin='request'",__LINE__,__FILE__);
				$receipt['message'][] = array('msg'=>lang('Request %1 has been deleted from project %2',$request_id[$i],$project_id));
			}
			return $receipt;
		}


		function update_request_status($project_id='',$status='',$category=0,$coordinator=0)
		{
			$historylog_r	= CreateObject('property.historylog','request');

			$sql = "SELECT origin_id FROM fm_origin WHERE destination ='project' AND destination_id='$project_id' and origin ='request'";
//			$sql = "SELECT origin_id FROM fm_project_origin WHERE project_id='$project_id' and origin ='request'";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$request_id[]	= $this->db->f('origin_id');
			}

			for ($i=0;$i<count($request_id);$i++)
			{
				$this->db->query("SELECT status,category,coordinator FROM fm_request where id='" .$request_id[$i] ."'",__LINE__,__FILE__);

				$this->db->next_record();

				$old_status = $this->db->f('status');
				$old_category = (int)$this->db->f('category');
				$old_coordinator = (int)$this->db->f('coordinator');

				if ($old_status != $status)
				{
					$historylog_r->add('S',$request_id[$i],$status);
				}

				if ((int)$old_category != (int)$category)
				{
					$historylog_r->add('T',$request_id[$i],$category);
				}

				if ((int)$old_coordinator != (int)$coordinator)
				{
					$historylog_r->add('C',$request_id[$i],$coordinator);
				}

				$this->db2->query("update fm_request set status='$status',coordinator='$coordinator' where id='". $request_id[$i] . "'",__LINE__,__FILE__);
			}
		}


		function check_request($request_id)
		{
			$sql = "SELECT destination_id FROM fm_origin WHERE destination ='project' AND origin_id='$request_id' and origin ='request'";
			$this->db->query($sql,__LINE__,__FILE__);

			$this->db->next_record();

			if ( $this->db->f(0))
			{
				return $this->db->f('destination_id');
			}
		}

		function add_request($add_request,$id)
		{

			for ($i=0;$i<count($add_request['request_id']);$i++)
			{
				$project_id=$this->check_request($add_request['request_id'][$i]);

				if(!$project_id)
				{
					$this->db->query("INSERT INTO  fm_origin (origin,origin_id,destination,destination_id,user_id,entry_date) "
						. "VALUES ('request','"
						. $add_request['request_id'][$i]. "','"
						. "project',"
						. $id . ","
						. $this->account . ","
						. time() . ")",__LINE__,__FILE__);

					$this->db2->query("update fm_request set project_id='$id' where id='". $add_request['request_id'][$i] . "'",__LINE__,__FILE__);

					$receipt['message'][] = array('msg'=>lang('request %1 has been added',$add_request['request_id'][$i]));
				}
				else
				{
					$receipt['error'][] = array('msg'=>lang('request %1 has already been added to project %2',$add_request['request_id'][$i],$project_id));
				}

			}

			return $receipt;
		}

		function delete($project_id )
		{
			$sql = "SELECT origin_id FROM fm_origin WHERE destination ='project' AND destination_id='$project_id' and origin ='request'";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$request_id[]	= $this->db->f('origin_id');
			}

			$sql = "SELECT id as workorder_id FROM fm_workorder WHERE project_id='$project_id'";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$workorder_id[]	= $this->db->f('workorder_id');
			}

			$this->db->transaction_begin();

			for ($i=0;$i<count($request_id);$i++)
			{

				$this->db2->query("update fm_request set project_id = NULL where id='". $request_id[$i] . "'",__LINE__,__FILE__);
			}

			$this->db->query("DELETE FROM fm_project WHERE id='" . $project_id . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_project_history  WHERE  history_record_id='" . $project_id   . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_projectbranch  WHERE  project_id='" . $project_id   . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_origin WHERE destination ='project' AND destination_id ='" . $project_id . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_workorder WHERE project_id='" . $project_id . "'",__LINE__,__FILE__);

			for ($i=0;$i<count($workorder_id);$i++)
			{
				$this->db->query("DELETE FROM fm_wo_hours WHERE workorder_id='" . $workorder_id[$i] . "'",__LINE__,__FILE__);
				$this->db->query("DELETE FROM fm_workorder_history  WHERE  history_record_id='" . $workorder_id[$i]   . "'",__LINE__,__FILE__);
			}

			$this->db->transaction_commit();

		}
	}
?>
