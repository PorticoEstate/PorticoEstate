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

	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */

	class property_sorequest
	{
		public $sum_budget = 0;
		public $sum_consume = 0;
		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->soproject	= CreateObject('property.soproject');
			$this->historylog	= CreateObject('property.historylog','request');
			$this->bocommon		= CreateObject('property.bocommon');
			$this->custom 		= createObject('property.custom_fields');
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
			$this->interlink 	= CreateObject('property.interlink');
		}

		function read_priority_key()
		{
			$this->db->query("SELECT * FROM fm_request_condition_type ORDER BY priority_key DESC, id ASC",__LINE__,__FILE__);

			$priority_key = array();
			while ($this->db->next_record())
			{
				$priority_key[] = array
				(
					'id' 			=> $this->db->f('id'),
					'name' 			=> $this->db->f('name',true),
					'descr' 		=> $this->db->f('descr',true),
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


		function update_score($request_id = 0)
		{
			if($request_id)
			{
				$request[] = (int)$request_id;
			}
			else
			{
				$request = array();
				$this->db->query("SELECT id FROM fm_request",__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					$request[] = $this->db->f('id');
				}
			}

			$config	= CreateObject('phpgwapi.config','property');
			$config->read();
			$authorities_demands = isset( $config->config_data['authorities_demands']) &&  $config->config_data['authorities_demands'] ? (int)$config->config_data['authorities_demands'] : 0;

			foreach ($request as $id)
			{
				if($GLOBALS['phpgw_info']['server']['db_type']=='pgsql' || $GLOBALS['phpgw_info']['server']['db_type']=='postgres')
				{
					$sql = "UPDATE fm_request SET score = (SELECT sum(CAST(priority_key as int4) * ( CAST(degree as int4) * CAST(probability as int4) * ( CAST(consequence as int4) )))  FROM fm_request_condition"
						. " {$this->join}  fm_request_condition_type ON (fm_request_condition.condition_type = fm_request_condition_type.id) WHERE request_id = {$id}) WHERE fm_request.id = {$id}";

					$this->db->query($sql,__LINE__,__FILE__);
				}
				else
				{
					$sql = "SELECT sum(priority_key * ( degree * probability * ( consequence ))) AS score FROM fm_request_condition"
						. " $this->join  fm_request_condition_type ON (fm_request_condition.condition_type = fm_request_condition_type.id) WHERE request_id = $id";

					$this->db->query($sql,__LINE__,__FILE__);

					$this->db->next_record();
					$score = $this->db->f('score');
					$this->db->query("UPDATE fm_request SET score = $score WHERE id = $id",__LINE__,__FILE__);
				}
			}
			$this->db->query("UPDATE fm_request SET score = 0 WHERE score IS NULL",__LINE__,__FILE__);
			$this->db->query("UPDATE fm_request SET score = score + {$authorities_demands} WHERE authorities_demands = 1",__LINE__,__FILE__);
		}

		function select_status_list()
		{
			$this->db->query("SELECT id, descr FROM fm_request_status ORDER BY sorting ");

			$status = array();
			while ($this->db->next_record())
			{
				$status[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('descr',true)
				);
			}
			return $status;
		}

		function select_condition_type_list()
		{
			$this->db->query("SELECT * FROM fm_request_condition_type ORDER BY id",__LINE__,__FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$id = $this->db->f('id');
				$values[$id] = array
				(
					'id'		=> $id,
					'name'		=> $this->db->f('name',true),
					'descr'		=> $this->db->f('descr',true),
					'weight'	=> $this->db->f('priority_key')					
				);
			}
			return $values;
		}

		function select_conditions($request_id, $condition_type_list = array())
		{
			$request_id = (int)$request_id;
			$values = array();
			foreach($condition_type_list as $condition_type)
			{
				$i = (int)$condition_type['id'];
				$this->db->query("SELECT * FROM fm_request_condition WHERE request_id={$request_id} AND condition_type = {$i}",__LINE__,__FILE__);

				$this->db->next_record();

				$values[$i] = array
				(
					'request_id'		=> $request_id,
					'condition_type'	=> $this->db->f('condition_type'),
					'reference'			=> $this->db->f('reference'),
					'degree'			=> $this->db->f('degree'),
					'probability'		=> $this->db->f('probability'),
					'consequence'		=> $this->db->f('consequence')
				);
			}

			return $values;
		}


		function read($data)
		{
			$start			= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$filter			= isset($data['filter'])?$data['filter']:'';
			$query			= isset($data['query'])?$data['query']:'';
			$sort			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order			= isset($data['order'])?$data['order']:'';
			$cat_id			= isset($data['cat_id'])?$data['cat_id']:0;
			$property_cat_id= isset($data['property_cat_id'])?$data['property_cat_id']:0;
			$status_id		= isset($data['status_id']) && $data['status_id'] ? $data['status_id']:0;
			$district_id	= isset($data['district_id']) && $data['district_id'] ? $data['district_id']:0;
			$project_id		= isset($data['project_id'])?$data['project_id']:'';
			$allrows		= isset($data['allrows'])?$data['allrows']:'';
			$list_descr		= isset($data['list_descr'])?$data['list_descr']:'';
			$dry_run		= isset($data['dry_run']) ? $data['dry_run'] : '';
			$p_num			= isset($data['p_num']) ? $data['p_num'] : '';
			$start_date		= isset($data['start_date']) && $data['start_date'] ? phpgwapi_datetime::date_to_timestamp($data['start_date']) : 0;
			$end_date		= isset($data['end_date']) && $data['end_date'] ? phpgwapi_datetime::date_to_timestamp($data['end_date']) : 0;
			$building_part 	= isset($data['building_part']) && $data['building_part'] ? (int)$data['building_part'] : 0;

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.project.request');
			$attribute_table = 'phpgw_cust_attribute';
			$attribute_filter = " location_id = {$location_id}";

			$entity_table = 'fm_request';

			$uicols = array();
			$cols .= "{$entity_table}.location_code";
			$cols_return[] 				= 'location_code';
			$cols_group[] 				= "{$entity_table}.location_code";
			$cols_group[] 				= 'fm_location1.loc1_name';

			$cols .= ",{$entity_table}.id as request_id";
			$cols_return[] 				= 'request_id';

			$cols.= ",fm_request_status.descr as status";
			$cols_return[] 				= 'status';
			$cols_group[] 				= 'fm_request_status.descr';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'status';
			$uicols['descr'][]			= lang('status');
			$uicols['statustext'][]		= lang('status');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= '';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= false;

			$cols.= ",max(fm_request_condition.degree) as condition_degree";
			$cols_return[] 				= 'condition_degree';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'condition_degree';
			$uicols['descr'][]			= lang('condition degree');
			$uicols['statustext'][]		= lang('condition degree');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= '';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= true;

			$cols.= ",$entity_table.start_date,$entity_table.entry_date,$entity_table.closed_date,$entity_table.in_progress_date,$entity_table.delivered_date";
			$cols_return[] 				= "start_date";
			$cols_return[] 				= "entry_date";
			$cols_return[] 				= "closed_date";
			$cols_return[] 				= "in_progress_date";
			$cols_return[] 				= "delivered_date";

			$cols_group[] 				= "{$entity_table}.start_date";
			$cols_group[] 				= "{$entity_table}.entry_date";
			$cols_group[] 				= "{$entity_table}.closed_date";
			$cols_group[] 				= "{$entity_table}.in_progress_date";
			$cols_group[] 				= "{$entity_table}.delivered_date";

			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'start_date';
			$uicols['descr'][]			= lang('start date');
			$uicols['statustext'][]		= lang('Request start date');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= '';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= true;


			$cols.= ",$entity_table.title as title";
			$cols_return[] 				= 'title';
			$cols_group[] 				= "title";
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'title';
			$uicols['descr'][]			= lang('title');
			$uicols['statustext'][]		= lang('Request title');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= '';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= true;


			if($list_descr)
			{
				$cols.= ",$entity_table.descr as descr";
				$cols_return[] 				= 'descr';
				$cols_group[] 				= "$entity_table.descr";
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'descr';
				$uicols['descr'][]			= lang('descr');
				$uicols['statustext'][]		= lang('Request descr');
				$uicols['exchange'][]		= '';
				$uicols['align'][]			= '';
				$uicols['datatype'][]		= '';
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= false;
			}


			$cols.= ",$entity_table.building_part";
			$cols_return[] 				= 'building_part';
			$cols_group[] 				= 'building_part';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'building_part';
			$uicols['descr'][]			= lang('building part');
			$uicols['statustext'][]		= lang('building part');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= '';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= true;

			$cols.= ",$entity_table.budget as budget";
			$cols_return[] 				= 'budget';
			$cols_group[] 				= 'budget';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'budget';
			$uicols['descr'][]			= lang('cost estimate');
			$uicols['statustext'][]		= lang('total cost estimate');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= 'FormatterRight';
			$uicols['classname'][]		= 'rightClasss';
			$uicols['sortable'][]		= true;

			

			$cols.= ",sum(amount) as consume";
			$cols_return[] 				= 'consume';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'consume';
			$uicols['descr'][]			= lang('consume');
			$uicols['statustext'][]		= lang('consume');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= 'FormatterRight';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= true;

			$cols.= ",$entity_table.coordinator";
			$cols_return[] 				= 'coordinator';
			$cols_group[]				= 'coordinator';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'coordinator';
			$uicols['descr'][]			= lang('Coordinator');
			$uicols['statustext'][]		= lang('Project coordinator');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= '';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= false;


			$cols.= ",$entity_table.score";
			$cols_return[] 				= 'score';
			$cols_group[]				= 'score';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'score';
			$uicols['descr'][]			= lang('score');
			$uicols['statustext'][]		= lang('score');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= 'FormatterRight';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= true;


			$this->db->query("SELECT * FROM $attribute_table WHERE list=1 AND $attribute_filter");
			while ($this->db->next_record())
			{
				$cols .= ",{$entity_table}." . $this->db->f('column_name');

				$cols_return[] 				= $this->db->f('column_name');
				$cols_group[]				= $this->db->f('column_name');
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= $this->db->f('column_name');
				$uicols['descr'][]			= $this->db->f('input_text',true);
				$uicols['statustext'][]		= $this->db->f('statustext',true);
				$uicols['exchange'][]		= '';
				$uicols['align'][]			= '';
				$uicols['datatype'][]		= $this->db->f('datatype');
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= false;
			}

			$paranthesis = '(';
			$joinmethod = "{$this->left_join} fm_request_status ON {$entity_table}.status = fm_request_status.id)";
			$paranthesis .= '(';
			$joinmethod .= "{$this->left_join} fm_request_consume ON {$entity_table}.id = fm_request_consume.request_id)";
			$paranthesis .= '(';
			$joinmethod .= "{$this->left_join} fm_request_condition ON {$entity_table}.id = fm_request_condition.request_id)";

			$GLOBALS['phpgw']->config->read();
			$_location_level = isset($GLOBALS['phpgw']->config->config_data['request_location_level']) && $GLOBALS['phpgw']->config->config_data['request_location_level'] ? $GLOBALS['phpgw']->config->config_data['request_location_level'] : 0;
			$sql	= $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols'=>$cols,'cols_return'=>$cols_return,
				'uicols'=>array(),'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,
				'query'=>$query,'force_location'=>true, 'location_level' => $_location_level));
			unset($_location_level);

			$cols_group[] = "{$entity_table}.id";
			$cols_group[] = 'fm_request_status.descr';
			$cols_group[] = "{$entity_table}.address";

			$groupmethod = 'GROUP BY ' . implode(',', $cols_group);
			
			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_request.id DESC';
			}

			$where = 'WHERE';
			$filtermethod = '';

			if(isset($GLOBALS['phpgw']->config->config_data['acl_at_location']) && $GLOBALS['phpgw']->config->config_data['acl_at_location'])
			{
				$access_location = $this->bocommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod = " WHERE fm_request.loc1 in ('" . implode("','", $access_location) . "')";
				$where= 'AND';
			}

			if ($property_cat_id > 0)
			{
				$filtermethod .= " $where fm_location1.category='{$property_cat_id}' ";
				$where = 'AND';
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " $where fm_request.category='{$cat_id}'";
				$where = 'AND';
			}

			if ($status_id)
			{
				$filtermethod .= " $where fm_request.status='{$status_id}'";
				$where = 'AND';
			}

			if ($building_part)
			{
				$filtermethod .= " $where fm_request.building_part='{$building_part}'";
				$where = 'AND';
			}

			if ($start_date)
			{
				$end_date	= $end_date + 3600 * 16 + phpgwapi_datetime::user_timezone();
				$start_date	= $start_date - 3600 * 8 + phpgwapi_datetime::user_timezone();

				$filtermethod .= " $where fm_request.start_date >= $start_date AND fm_request.start_date <= $end_date ";
				$where= 'AND';
			}

			if ($filter)
			{
				$filtermethod .= " $where fm_request.coordinator='$filter' ";
				$where = 'AND';
			}

			if ($project_id && !$status_id)// lookup requests not already allocated to projects
			{
//				$filtermethod .= " $where project_id is NULL ";
				$filtermethod .= " $where fm_request_status.closed is NULL ";
				$where = 'AND';
			}

			if($district_id)
			{
				$filtermethod .= " {$where} fm_part_of_town.district_id = {$district_id}";
				$where= 'AND';
			}

			if($query)
			{
				if(stristr($query, '.') && $p_num)
				{
					$query=explode(".",$query);
					$querymethod = " $where (fm_request.p_entity_id='" . (int)$query[1] . "' AND fm_request.p_cat_id='" . (int)$query[2] . "' AND fm_request.p_num='" . (int)$query[3] . "')";
				}
				else
				{
					$query = $this->db->db_addslashes($query);
					$querymethod = " $where (fm_request.title $this->like '%$query%' or fm_request.address $this->like '%$query%' or fm_request.location_code $this->like '%$query%')";
				}
			}

			$sql .= " $filtermethod $querymethod $groupmethod";
//_debug_array($sql);
			$this->uicols['input_type']	= array_merge($this->bocommon->uicols['input_type'], $uicols['input_type']);
			$this->uicols['name']		= array_merge($this->bocommon->uicols['name'], $uicols['name']);
			$this->uicols['descr']		= array_merge($this->bocommon->uicols['descr'], $uicols['descr']);
			$this->uicols['statustext']	= array_merge($this->bocommon->uicols['statustext'], $uicols['statustext']);
			$this->uicols['exchange']	= array_merge($this->bocommon->uicols['exchange'], $uicols['exchange']);
			$this->uicols['align']		= array_merge($this->bocommon->uicols['align'], $uicols['align']);
			$this->uicols['datatype']	= array_merge($this->bocommon->uicols['datatype'], $uicols['datatype']);
			$this->uicols['formatter']	= array_merge($this->bocommon->uicols['formatter'], $uicols['formatter']);
			$this->uicols['classname']	= array_merge($this->bocommon->uicols['classname'], $uicols['classname']);
			$this->uicols['sortable']	= array_merge($this->bocommon->uicols['sortable'], $uicols['sortable']);

			array_unshift($this->uicols['input_type'],'text');
			array_unshift($this->uicols['name'],'request_id');
			array_unshift($this->uicols['descr'],'ID');
			array_unshift($this->uicols['statustext'],'Request ID');
			array_unshift($this->uicols['exchange'],'');
			array_unshift($this->uicols['align'],'');
			array_unshift($this->uicols['datatype'],'');
			array_unshift($this->uicols['formatter'],'');
			array_unshift($this->uicols['classname'],'');
			array_unshift($this->uicols['sortable'],true);

			$cols_return		= $this->bocommon->cols_return;
			$type_id			= $this->bocommon->type_id;
			$this->cols_extra	= $this->bocommon->cols_extra;

			$this->db->fetchmode = 'ASSOC';

		//	$sql2 = 'SELECT count(*) as cnt, sum(budget) as sum_budget ' . substr($sql,strripos($sql,'FROM'));

			$sql2 = "SELECT count(*) as cnt, sum(budget) as sum_budget, sum(consume) as sum_consume FROM ({$sql}) as t";
//_debug_array($sql2);
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$this->total_records = $this->db->f('cnt');
			$this->sum_budget	= $this->db->f('sum_budget');
			$this->sum_consume	= $this->db->f('sum_consume');
			
			//cramirez.r@ccfirst.com 23/10/08 avoid retrieve data in first time, only render definition for headers (var myColumnDefs)
			if($dry_run)
			{
				return array();
			}
			else
			{
				if(!$allrows)
				{
					$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
				}
				else
				{
					$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
				}
			}

			$j=0;
			$request_list = array();
			while ($this->db->next_record())
			{
				for ($i=0;$i<count($cols_return);$i++)
				{
					$request_list[$j][$cols_return[$i]] = $this->db->f($cols_return[$i], true);
				}

				$location_code=	$this->db->f('location_code');
				$location = explode('-',$location_code);
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

		function read_single($request_id, $values = array())
		{
			$request_id = (int) $request_id;
			$sql = "SELECT * FROM fm_request WHERE id={$request_id}";

			$this->db->query($sql,__LINE__,__FILE__);

			$request = array();
			if ($this->db->next_record())
			{
				$request = array
					(
						'id'					=> $this->db->f('id'),
						'request_id'			=> $this->db->f('id'), // FIXME
						'title'					=> $this->db->f('title', true),
						'location_code'			=> $this->db->f('location_code'),
						'descr'					=> $this->db->f('descr', true),
						'status'				=> $this->db->f('status'),
						'budget'				=> (int)$this->db->f('budget'),
						'tenant_id'				=> $this->db->f('tenant_id'),
						'owner'					=> $this->db->f('owner'),
						'coordinator'			=> $this->db->f('coordinator'),
						'access'				=> $this->db->f('access'),
						'start_date'			=> $this->db->f('start_date'),
						'end_date'				=> $this->db->f('end_date'),
						'cat_id'				=> $this->db->f('category'),
						'branch_id'				=> $this->db->f('branch_id'),
						'authorities_demands'	=> $this->db->f('authorities_demands'),
						'score'					=> $this->db->f('score'),
						'p_num'					=> $this->db->f('p_num'),
						'p_entity_id'			=> $this->db->f('p_entity_id'),
						'p_cat_id'				=> $this->db->f('p_cat_id'),
						'contact_phone'			=> $this->db->f('contact_phone', true),
						'building_part'			=> $this->db->f('building_part'),
						'entry_date'			=> $this->db->f('entry_date'),
						'closed_date'			=> $this->db->f('closed_date'),
						'in_progress_date'		=> $this->db->f('in_progress_date'),
						'delivered_date'		=> $this->db->f('delivered_date'),
						'regulations' 			=> explode(',', $this->db->f('regulations'))
					);

				if ( isset($values['attributes']) && is_array($values['attributes']) )
				{
					$request['attributes'] = $values['attributes'];
					foreach ( $request['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->db->f($attr['column_name']);
					}
				}

				$location_code = $this->db->f('location_code');
				$request['power_meter']		= $this->soproject->get_power_meter($location_code);

				$sql = "SELECT * FROM fm_request_planning WHERE request_id={$request_id} ORDER BY date ASC";
				$this->db->query($sql,__LINE__,__FILE__);
				while($this->db->next_record())
				{
					$request['planning'][] = array
					(
						'id'			=> $this->db->f('id'),
						'amount'		=> $this->db->f('amount'),
						'date'			=> $this->db->f('date'),
						'user_id'		=> $this->db->f('user_id'),
						'entry_date'	=> $this->db->f('entry_date'),
						'descr'			=> $this->db->f('descr',true)
					);
				}

				$sql = "SELECT * FROM fm_request_consume WHERE request_id={$request_id} ORDER BY date ASC";
				$this->db->query($sql,__LINE__,__FILE__);
				while($this->db->next_record())
				{
					$request['consume'][] = array
					(
						'id'			=> $this->db->f('id'),
						'amount'		=> $this->db->f('amount'),
						'date'			=> $this->db->f('date'),
						'user_id'		=> $this->db->f('user_id'),
						'entry_date'	=> $this->db->f('entry_date'),
						'descr'			=> $this->db->f('descr',true)
					);
				}
			}

			return $request;
		}

		function request_workorder_data($request_id = '')
		{
			$request_id = (int)$request_id;
			$this->db->query("select budget, id as workorder_id, vendor_id from fm_workorder where request_id='$request_id'");
			$budget = array();
			while ($this->db->next_record())
			{
				$budget[] = array
					(
						'workorder_id'	=> $this->db->f('workorder_id'),
						'budget'	=> sprintf("%01.2f",$this->db->f('budget')),
						'vendor_id'	=> $this->db->f('vendor_id')
					);
			}
			return $budget;
		}


		function increment_request_id()
		{
			$name = 'request';
			$now = time();
			$this->db->query("SELECT value, start_date FROM fm_idgenerator WHERE name='{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$next_id = $this->db->f('value') +1;
			$start_date = (int)$this->db->f('start_date');
			$this->db->query("UPDATE fm_idgenerator SET value = $next_id WHERE name = '{$name}' AND start_date = {$start_date}");
			return $next_id;
		}

		function next_id()
		{
			$name = 'request';
			$now = time();
			$this->db->query("SELECT value FROM fm_idgenerator WHERE name = '{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$id = $this->db->f('value')+1;
			return $id;
		}

		function add($request, $values_attribute = array())
		{
			//_debug_array($request);
			$receipt = array();

			$value_set = array();

			while (is_array($request['location']) && list($input_name,$value) = each($request['location']))
			{
				if($value)
				{
					$value_set[$input_name] = $value;
				}
			}

			while (is_array($request['extra']) && list($input_name,$value) = each($request['extra']))
			{
				if($value)
				{
					$value_set[$input_name] = $value;
				}
			}

			$data_attribute = $this->custom->prepare_for_db('fm_request', $values_attribute);
			if(isset($data_attribute['value_set']))
			{
				foreach($data_attribute['value_set'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$value_set[$input_name] = $value;
					}
				}
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

			$this->db->transaction_begin();

			$id = $this->next_id();

			$value_set['id'] 					= $id;
			$value_set['title']					= $this->db->db_addslashes($request['title']);
			$value_set['owner']					= $this->account;
			$value_set['category']				= $request['cat_id'];
			$value_set['descr']					= $this->db->db_addslashes($request['descr']);
			$value_set['location_code']			= $request['location_code'];
			$value_set['address']				= $address;
			$value_set['entry_date']			= time();
			$value_set['budget']				= $request['budget'];
			$value_set['status']				= $request['status'];
			$value_set['branch_id']				= $request['branch_id'];
			$value_set['coordinator']			= $request['coordinator'];
			$value_set['authorities_demands']	= $request['authorities_demands'];
			$value_set['building_part']			= $request['building_part'];
			$value_set['start_date']			= $request['start_date'];
			$value_set['end_date']				= $request['end_date'];
			$value_set['regulations']			= $request['regulations'] ? ',' . implode(',',$request['regulations']) . ',' : '';

			$cols = implode(',', array_keys($value_set));
			$values	= $this->bocommon->validate_db_insert(array_values($value_set));

			$this->db->query("INSERT INTO fm_request ({$cols}) VALUES ({$values})",__LINE__,__FILE__);


			if(isset($request['condition']) && is_array($request['condition']))
			{
				foreach( $request['condition'] as $condition_type => $value_type )
				{
					$_condition_type = isset($value_type['condition_type']) && $value_type['condition_type'] ? $value_type['condition_type'] : $condition_type;
					if($_condition_type)
					{
						$this->db->query("INSERT INTO fm_request_condition (request_id,condition_type,reference,degree,probability,consequence,user_id,entry_date) "
							. "VALUES ("
							. (int) $id. ","
							. (int) $_condition_type . ","
							. (int) $value_type['reference']. ","
							. (int) $value_type['degree']. ","
							. (int) $value_type['probability']. ","
							. (int) $value_type['consequence']. ","
								. (int) $this->account . ","
							. time() . ")",__LINE__,__FILE__);
					}
				}
			}

			$this->update_score($id);

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
						'location2_item_id' => $id,
						'account_id'		=> $this->account
					);

				$this->interlink->add($interlink_data,$this->db);
			}
			
			$sql = "SELECT * FROM fm_request_status WHERE id='{$request['status']}'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$value_set = array();
			if($this->db->f('in_progress'))
			{
				$value_set['in_progress_date']	= time();
			}
			if($this->db->f('closed'))
			{
				$value_set['closed_date']		= time();
			}
			if($this->db->f('delivered'))
			{
				$value_set['delivered_date']	= time();
			}

			if($value_set)
			{
				$value_set	= $this->db->validate_update($value_set);
				$this->db->query("UPDATE fm_request SET $value_set WHERE id= '{$id}'",__LINE__,__FILE__);
			}

			if($request['planning_value'] && $request['planning_date'])
			{
				$this->db->query("INSERT INTO fm_request_planning (request_id,amount,date,user_id,entry_date) "
					. "VALUES ('"
					. $id . "','"
					. (int)$request['planning_value'] . "',"
					. (int)$request['planning_date']. ","
					. $this->account . ","
					. time() . ")",__LINE__,__FILE__);
			}

			if($request['consume_value'] && $request['consume_date'])
			{
				$this->db->query("INSERT INTO fm_request_consume (request_id,amount,date,user_id,entry_date) "
					. "VALUES ('"
					. $id . "','"
					. (int)$request['consume_value'] . "',"
					. (int)$request['consume_date']. ","
					. $this->account . ","
					. time() . ")",__LINE__,__FILE__);
			}

			if($this->db->transaction_commit())
			{
				$this->increment_request_id();
				$this->historylog->add('SO',$id,$request['status']);
				$this->historylog->add('TO',$id,$request['cat_id']);
				$this->historylog->add('CO',$id,$request['coordinator']);
				$receipt['message'][] = array('msg'=>lang('request %1 has been saved',$id));
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('request %1 has not been saved',$id));
			}
			$receipt['id'] = $id;
			return $receipt;
		}

		function edit($request, $values_attribute = array())
		{
			$receipt = array();

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

			$value_set = array
			(
				'title' 				=> $this->db->db_addslashes($request['title']),
				'status'				=> $request['status'],
				'category'				=> $request['cat_id'],
				'start_date'			=> $request['start_date'],
				'end_date'				=> $request['end_date'],
				'coordinator'			=> $request['coordinator'],
				'descr'					=> $this->db->db_addslashes($request['descr']),
				'budget'				=> (int)$request['budget'],
				'location_code'			=> $request['location_code'],
				'address'				=> $address,
				'authorities_demands'	=> $request['authorities_demands'],
				'building_part'			=> $request['building_part'],
				'regulations'			=> $request['regulations'] ? ',' . implode(',',$request['regulations']) . ',' : ''
			);

			while (is_array($request['location']) && list($input_name,$value) = each($request['location']))
			{
				$value_set[$input_name] = $value;
			}

			while (is_array($request['extra']) && list($input_name,$value) = each($request['extra']))
			{
				$value_set[$input_name] = $value;
			}

			$data_attribute = $this->custom->prepare_for_db('fm_request', $values_attribute, $request['id']);

			if(isset($data_attribute['value_set']))
			{
				$value_set = array_merge($value_set, $data_attribute['value_set']);
			}


			$this->db->transaction_begin();

			$this->db->query("SELECT budget,status,category,coordinator FROM fm_request where id='" .$request['id']."'",__LINE__,__FILE__);
			$this->db->next_record();

			$old_budget			= $this->db->f('budget');
			$old_status = $this->db->f('status');
			$old_category = $this->db->f('category');
			$old_coordinator = $this->db->f('coordinator');
			if($old_status != $request['status'])
			{
				$sql = "SELECT * FROM fm_request_status WHERE id='{$request['status']}'";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
			
				if($this->db->f('in_progress'))
				{
					$value_set['in_progress_date']	= time();
				}
				if($this->db->f('closed'))
				{
					$value_set['closed_date']		= time();
				}
				if($this->db->f('delivered'))
				{
					$value_set['delivered_date']	= time();
				}
			}

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE fm_request SET $value_set WHERE id= '{$request['id']}'",__LINE__,__FILE__);

			$this->db->query("DELETE FROM fm_request_condition WHERE request_id='{$request['id']}'",__LINE__,__FILE__);

			if(isset($request['condition']) && is_array($request['condition']))
			{
				foreach( $request['condition'] as $condition_type => $value_type )
				{
					$_condition_type = isset($value_type['condition_type']) && $value_type['condition_type'] ? $value_type['condition_type'] : $condition_type;
					if(isset($value_type['condition_type']) && !$value_type['condition_type'])
					{
						continue;
					}
					$this->db->query("INSERT INTO fm_request_condition (request_id,condition_type,reference,degree,probability,consequence,user_id,entry_date) "
						. "VALUES ("
						. (int)$request['id']. ","
						. (int)$_condition_type . ","
						. (int)$value_type['reference']. ","
						. (int)$value_type['degree']. ","
						. (int)$value_type['probability']. ","
						. (int)$value_type['consequence']. ","
						. (int)$this->account . ","
						. time() . ")",__LINE__,__FILE__);
				}
			}

			$this->update_score($request['id']);

			if($request['extra']['contact_phone'] && $request['extra']['tenant_id'])
			{
				$this->db->query("UPDATE fm_tenant SET contact_phone='{$request['extra']['contact_phone']}' WHERE id='{$request['extra']['tenant_id']}'",__LINE__,__FILE__);
			}

			if ($request['power_meter'] )
			{
				$this->soproject->update_power_meter($request['power_meter'],$request['location_code'],$address);
			}

			if($request['planning_value'] && $request['planning_date'])
			{
				$this->db->query("INSERT INTO fm_request_planning (request_id,amount,date,user_id,entry_date) "
					. "VALUES ('"
					. $request['id']. "','"
					. (int)$request['planning_value'] . "',"
					. (int)$request['planning_date']. ","
					. $this->account . ","
					. time() . ")",__LINE__,__FILE__);
			}

			if(isset($request['delete_planning']) && is_array($request['delete_planning']))
			{
				foreach ($request['delete_planning'] as $delete_planning)
				{
					$this->db->query("DELETE FROM fm_request_planning WHERE id =" . (int)$delete_planning,__LINE__,__FILE__);				
				}
			}

			if($request['consume_value'] && $request['consume_date'])
			{
				$this->db->query("INSERT INTO fm_request_consume (request_id,amount,date,user_id,entry_date) "
					. "VALUES ('"
					. $request['id']. "','"
					. (int)$request['consume_value'] . "',"
					. (int)$request['consume_date']. ","
					. $this->account . ","
					. time() . ")",__LINE__,__FILE__);
			}

			if(isset($request['delete_consume']) && is_array($request['delete_consume']))
			{
				foreach ($request['delete_consume'] as $delete_consume)
				{
					$this->db->query("DELETE FROM fm_request_consume WHERE id =" . (int)$delete_consume,__LINE__,__FILE__);				
				}
			}

			if($this->db->transaction_commit())
			{
				if ($old_status != $request['status'])
				{
					$this->historylog->add('S',$request['id'],$request['status'],$old_status);
				}
				if ($old_category != $request['cat_id'])
				{
					$this->historylog->add('T',$request['id'],$request['cat_id'],$old_category);
				}
				if ((int)$old_coordinator != (int)$request['coordinator'])
				{
					$this->historylog->add('C',$request['id'],$request['coordinator'],$old_coordinator);
				}

				if ($old_budget != $request['budget'])
				{
					$this->historylog->add('B', $request['id'], $request['budget'], $old_budget);
				}

				$receipt['message'][] = array('msg'=>lang('request %1 has been edited',$request['id']));
			}
			else
			{
				$receipt['message'][] = array('msg'=>lang('request %1 has not been edited',$request['id']));
			}

			$receipt['id'] = $request['id'];
			return $receipt;
		}

		function delete($request_id )
		{
			$request_id = (int) $request_id;
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_request_planning WHERE request_id = {$request_id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_request_consume WHERE request_id = {$request_id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_request_condition WHERE request_id = {$request_id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_request_history  WHERE  history_record_id = {$request_id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_request WHERE id = {$request_id}",__LINE__,__FILE__);
		//	$this->db->query("DELETE FROM fm_origin WHERE destination = 'request' AND destination_id='" . $request_id . "'",__LINE__,__FILE__);
			$this->interlink->delete_at_target('property', '.project.request', $request_id, $this->db);
			$this->db->transaction_commit();
		}
	}
