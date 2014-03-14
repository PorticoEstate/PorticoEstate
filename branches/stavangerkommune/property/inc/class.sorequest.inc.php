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
	phpgw::import_class('property.socommon_core');

	/**
	 * Description
	 * @package property
	 */

	class property_sorequest extends property_socommon_core
	{
		public $sum_investment = 0;
		public $sum_operation = 0;
		public $sum_potential_grants = 0;
		public $uicols = array();

		protected $global_lock = false;
		function __construct()
		{
			parent::__construct();

			$this->soproject	= CreateObject('property.soproject');
			$this->historylog	= CreateObject('property.historylog','request');
			$this->bocommon		= CreateObject('property.bocommon');
			$this->interlink 	= CreateObject('property.interlink');
		}

		function read_priority_key()
		{
			$this->_db->query("SELECT * FROM fm_request_condition_type ORDER BY priority_key DESC, id ASC",__LINE__,__FILE__);

			$priority_key = array();
			while ($this->_db->next_record())
			{
				$priority_key[] = array
				(
					'id' 			=> $this->_db->f('id'),
					'name' 			=> $this->_db->f('name',true),
					'descr' 		=> $this->_db->f('descr',true),
					'priority_key' 	=> $this->_db->f('priority_key')
				);
			}

			return $priority_key;
		}

		function update_priority_key($values)
		{

			while (is_array($values['priority_key']) && list($id,$priority_key) = each($values['priority_key']))
			{
				$this->_db->query("UPDATE fm_request_condition_type SET priority_key = $priority_key WHERE id = $id",__LINE__,__FILE__);
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
				$this->_db->query("SELECT id FROM fm_request",__LINE__,__FILE__);

				while ($this->_db->next_record())
				{
					$request[] = $this->_db->f('id');
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
						. " {$this->_join}  fm_request_condition_type ON (fm_request_condition.condition_type = fm_request_condition_type.id) WHERE request_id = {$id}) WHERE fm_request.id = {$id}";

					$this->_db->query($sql,__LINE__,__FILE__);
				}
				else
				{
					$sql = "SELECT sum(priority_key * ( degree * probability * ( consequence ))) AS score FROM fm_request_condition"
						. " $this->_join  fm_request_condition_type ON (fm_request_condition.condition_type = fm_request_condition_type.id) WHERE request_id = $id";

					$this->_db->query($sql,__LINE__,__FILE__);

					$this->_db->next_record();
					$score = $this->_db->f('score');
					$this->_db->query("UPDATE fm_request SET score = $score WHERE id = $id",__LINE__,__FILE__);
				}
			}
			$this->_db->query("UPDATE fm_request SET score = 0 WHERE score IS NULL",__LINE__,__FILE__);
			$this->_db->query("UPDATE fm_request SET score = score + {$authorities_demands} WHERE authorities_demands = 1",__LINE__,__FILE__);
		}

		function select_status_list()
		{
			$this->_db->query("SELECT id, descr FROM fm_request_status ORDER BY sorting ");

			$status = array();
			while ($this->_db->next_record())
			{
				$status[] = array
				(
					'id'	=> $this->_db->f('id'),
					'name'	=> $this->_db->f('descr',true)
				);
			}
			return $status;
		}

		function select_condition_type_list()
		{
			$this->_db->query("SELECT * FROM fm_request_condition_type ORDER BY id",__LINE__,__FILE__);

			$values = array();
			while ($this->_db->next_record())
			{
				$id = $this->_db->f('id');
				$values[$id] = array
				(
					'id'		=> $id,
					'name'		=> $this->_db->f('name',true),
					'descr'		=> $this->_db->f('descr',true),
					'weight'	=> $this->_db->f('priority_key')
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
				$this->_db->query("SELECT * FROM fm_request_condition WHERE request_id={$request_id} AND condition_type = {$i}",__LINE__,__FILE__);

				$this->_db->next_record();

				$values[$i] = array
				(
					'request_id'		=> $request_id,
					'condition_type'	=> $this->_db->f('condition_type'),
					'reference'			=> $this->_db->f('reference'),
					'degree'			=> $this->_db->f('degree'),
					'probability'		=> $this->_db->f('probability'),
					'consequence'		=> $this->_db->f('consequence')
				);
			}

			return $values;
		}


		function read_survey_data($data)
		{
			$start					= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$condition_survey_id	= $data['condition_survey_id'] ? (int) $data['condition_survey_id'] : 0;
			$sort					= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order					= isset($data['order'])?$data['order']:'';


			if ($order)
			{
				switch($order)
				{
					case 'recommended_year':
						$ordermethod = " ORDER BY recommended_year $sort";
						break;
					case 'planned_year':
						$ordermethod = " ORDER BY start_date $sort";
						break;
					case 'url':
						$ordermethod = " ORDER BY fm_request.id $sort";
						break;
					default:
						$ordermethod = " ORDER BY $order $sort";					
				}
			}
			else
			{
				$ordermethod = ' ORDER BY fm_request.id DESC';
			}

			$filtermethod = " WHERE fm_request.condition_survey_id = '{$condition_survey_id}'";


			if ($cat_id > 0)
			{
				$filtermethod .= " AND fm_request.category='{$cat_id}'";
			}

			$sql = "SELECT DISTINCT fm_request.id as request_id,fm_request_status.descr as status,fm_request.building_part,"
			. " fm_request.start_date,fm_request.closed_date,fm_request.in_progress_date,fm_request.category as cat_id,"
			. " fm_request.delivered_date,fm_request.title as title,max(fm_request_condition.degree) as condition_degree,"
	//		. " sum(fm_request_planning.amount) as planned_budget,"
			. " (fm_request.amount_investment * fm_request.multiplier) as amount_investment,"
			. " (fm_request.amount_operation * fm_request.multiplier) as amount_operation,"
			. " (fm_request.amount_potential_grants * fm_request.multiplier) as amount_potential_grants,"
			. " fm_request.score,"
			. " fm_request.recommended_year,"
			. " fm_request.start_date"
			. " FROM (( fm_request  LEFT JOIN fm_request_status ON fm_request.status = fm_request_status.id)"
	//		. " LEFT JOIN fm_request_planning ON fm_request.id = fm_request_planning.request_id)"
	//		. " LEFT JOIN fm_request_consume ON fm_request.id = fm_request_consume.request_id)"
			. " LEFT JOIN fm_request_condition ON fm_request.id = fm_request_condition.request_id)"
			. " {$filtermethod}"
			. " GROUP BY fm_request.category, fm_request.multiplier,fm_request.recommended_year, fm_request_status.descr,"
			. " building_part,fm_request.start_date,fm_request.entry_date,fm_request.closed_date,"
			. " fm_request.in_progress_date,fm_request.delivered_date,title,amount_investment,amount_operation,amount_potential_grants,score,fm_request.id,fm_request_status.descr";
//_debug_array($sql);
			$sql2 = "SELECT count(*) as cnt, sum(amount_investment) as sum_investment, sum(amount_operation) as sum_operation, sum(amount_potential_grants) as sum_potential_grants FROM ({$sql}) as t";

			$this->_db->query($sql2,__LINE__,__FILE__);
			$this->_db->next_record();
			$this->_total_records = $this->_db->f('cnt');
			$this->sum_investment	= $this->_db->f('sum_investment');
			$this->sum_operation	= $this->_db->f('sum_operation');
			$this->sum_potential_grants	= $this->_db->f('sum_potential_grants');
	


/*
			$sql3 = "SELECT sum(fm_request_consume.amount) as sum_consume  FROM {$sql_arr[1]}";
			$this->_db->query($sql3,__LINE__,__FILE__);
			$this->_db->next_record();
			$this->sum_consume	= $this->_db->f('sum_consume');
*/			

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
					'id'						=> $this->_db->f('request_id'),
					'status'					=> $this->_db->f('status',true),
					'building_part'				=> $this->_db->f('building_part'),
					'title'						=> $this->_db->f('title',true),
					'condition_degree'			=> $this->_db->f('condition_degree'),
					'amount_investment'			=> $this->_db->f('amount_investment'),
					'amount_operation'			=> $this->_db->f('amount_operation'),
					'amount_potential_grants'	=> $this->_db->f('amount_potential_grants'),
					'planned_budget'			=> $this->_db->f('planned_budget'),
					'score'						=> $this->_db->f('score'),
					'recommended_year'			=> $this->_db->f('recommended_year') ?  $this->_db->f('recommended_year') : '',
					'planned_year'				=> $this->_db->f('start_date') ? date('Y', $this->_db->f('start_date')) : '',
					'cat_id'					=> $this->_db->f('cat_id'),
				);
			}
			return $values;
		}

		function read($data)
		{
			$start					= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$filter					= isset($data['filter'])?$data['filter']:'';
			$query					= isset($data['query'])?$data['query']:'';
			$sort					= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order					= isset($data['order'])?$data['order']:'';
			$cat_id					= isset($data['cat_id'])?$data['cat_id']:0;
			$property_cat_id		= isset($data['property_cat_id'])?$data['property_cat_id']:0;
			$status_id				= isset($data['status_id']) && $data['status_id'] ? $data['status_id'] : 'open';
			$district_id			= isset($data['district_id']) && $data['district_id'] ? $data['district_id']:0;
			$project_id				= isset($data['project_id'])?$data['project_id']:'';
			$allrows				= isset($data['allrows'])?$data['allrows']:'';
			$list_descr				= isset($data['list_descr'])?$data['list_descr']:'';
			$dry_run				= isset($data['dry_run']) ? $data['dry_run'] : '';
			$p_num					= isset($data['p_num']) ? $data['p_num'] : '';
			$start_date				= isset($data['start_date']) && $data['start_date'] ? phpgwapi_datetime::date_to_timestamp($data['start_date']) : 0;
			$end_date				= isset($data['end_date']) && $data['end_date'] ? phpgwapi_datetime::date_to_timestamp($data['end_date']) : 0;
			$building_part 			= isset($data['building_part']) && $data['building_part'] ? (int)$data['building_part'] : 0;
			$degree_id				= $data['degree_id'];
			$attrib_filter			= $data['attrib_filter'] ? $data['attrib_filter'] : array();
			$condition_survey_id	= $data['condition_survey_id'] ? (int) $data['condition_survey_id'] : 0;
			$responsible_unit		= (int)$data['responsible_unit'];
			$recommended_year		= (int)$data['recommended_year'];

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.project.request');
			$attribute_table = 'phpgw_cust_attribute';
			$attribute_filter = " location_id = {$location_id}";

			$entity_table = 'fm_request';

			$GLOBALS['phpgw']->config->read();

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

			$cols.= ",$entity_table.start_date,$entity_table.entry_date,$entity_table.closed_date,$entity_table.in_progress_date,$entity_table.delivered_date";
			$cols_return[] 				= "start_date";
			$cols_return[] 				= "entry_date";
			$cols_return[] 				= "closed_date";
			$cols_return[] 				= "in_progress_date";
			$cols_return[] 				= "delivered_date";

//			$cols_group[] 				= "{$entity_table}.start_date";
			$cols_group[] 				= "{$entity_table}.entry_date";
			$cols_group[] 				= "{$entity_table}.closed_date";
			$cols_group[] 				= "{$entity_table}.in_progress_date";
			$cols_group[] 				= "{$entity_table}.delivered_date";
/*
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
*/

			$cols.= ",$entity_table.title as title";
			$cols_return[] 				= 'title';
			$cols_group[] 				= "title";
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'title';
			$uicols['descr'][]			= lang('request title');
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


			$cols.= ",($entity_table.amount_investment * multiplier) as amount_investment";
			$cols_return[] 				= 'amount_investment';
			$cols_group[] 				= 'amount_investment';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'amount_investment';
			$uicols['descr'][]			= lang('investment');
			$uicols['statustext'][]		= lang('cost estimate');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= 'FormatterRight';
			$uicols['classname'][]		= 'rightClasss';
			$uicols['sortable'][]		= true;

			$cols.= ",($entity_table.amount_operation * multiplier) as amount_operation";
			$cols_return[] 				= 'amount_operation';
			$cols_group[] 				= 'amount_operation';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'amount_operation';
			$uicols['descr'][]			= lang('operation');
			$uicols['statustext'][]		= lang('cost estimate');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= 'FormatterRight';
			$uicols['classname'][]		= 'rightClasss';
			$uicols['sortable'][]		= true;

			$cols.= ",($entity_table.amount_potential_grants * multiplier) as amount_potential_grants";
			$cols_return[] 				= 'amount_potential_grants';
			$cols_group[] 				= 'amount_potential_grants';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'amount_potential_grants';
			$uicols['descr'][]			= lang('potential grants');
			$uicols['statustext'][]		= lang('potential grants');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= 'FormatterRight';
			$uicols['classname'][]		= 'rightClasss';
			$uicols['sortable'][]		= true;

//			$cols.= ",sum(amount) as consume";
//			$cols_return[] 				= 'consume';
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


			$cols.= ",recommended_year";
			$cols_return[] 				= 'recommended_year';
			$cols_group[] 				= 'recommended_year';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'recommended_year';
			$uicols['descr'][]			= lang('recommended year');
			$uicols['statustext'][]		= lang('recommended year');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= '';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= true;

			$cols.= ",start_date AS planned_year";
			$cols_return[] 				= 'planned_year';
			$cols_group[] 				= 'start_date';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'planned_year';
			$uicols['descr'][]			= lang('planned year');
			$uicols['statustext'][]		= lang('planned year');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= '';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= true;


			$this->_db->query("SELECT * FROM $attribute_table WHERE list=1 AND $attribute_filter");
			$_attrib = array();
			while ($this->_db->next_record())
			{
				$_column_name = $this->_db->f('column_name');
				$cols .= ",{$entity_table}.{$_column_name}";

				$cols_return[] 				= $_column_name;
				$cols_group[]				= $_column_name;
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= $_column_name;
				$uicols['descr'][]			= $this->_db->f('input_text',true);
				$uicols['statustext'][]		= $this->_db->f('statustext',true);
				$uicols['exchange'][]		= '';
				$uicols['align'][]			= '';
				$uicols['datatype'][]		= $this->_db->f('datatype');
				$uicols['formatter'][]		= '';
				$uicols['classname'][]		= '';
				$uicols['sortable'][]		= false;

				$_attrib[$_column_name] = $this->_db->f('id');
			}

			$cols.= ",$entity_table.coordinator";
			$cols_return[] 				= 'coordinator';
			$cols_group[]				= 'coordinator';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'coordinator';
			$uicols['descr'][]			= isset($GLOBALS['phpgw']->config->config_data['lang_request_coordinator']) && $GLOBALS['phpgw']->config->config_data['lang_request_coordinator'] ? $GLOBALS['phpgw']->config->config_data['lang_request_coordinator'] : lang('Coordinator');
			$uicols['statustext'][]		= lang('Project coordinator');
			$uicols['exchange'][]		= '';
			$uicols['align'][]			= '';
			$uicols['datatype'][]		= '';
			$uicols['formatter'][]		= '';
			$uicols['classname'][]		= '';
			$uicols['sortable'][]		= false;

			$paranthesis = '(';
			$joinmethod = "{$this->_left_join} fm_request_status ON {$entity_table}.status = fm_request_status.id)";

			$paranthesis .= '(';
			$joinmethod .= "{$this->_left_join} fm_request_planning ON {$entity_table}.id = fm_request_planning.request_id)";

			$paranthesis .= '(';
			$joinmethod .= "{$this->_left_join} fm_request_consume ON {$entity_table}.id = fm_request_consume.request_id)";
			$paranthesis .= '(';
			$joinmethod .= "{$this->_left_join} fm_request_condition ON {$entity_table}.id = fm_request_condition.request_id)";

			$_location_level = isset($GLOBALS['phpgw']->config->config_data['request_location_level']) && $GLOBALS['phpgw']->config->config_data['request_location_level'] ? $GLOBALS['phpgw']->config->config_data['request_location_level'] : 0;
			$sql	= $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols'=>$cols,'cols_return'=>$cols_return,
				'uicols'=>array(),'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,
				'query'=>$query,'force_location'=>true, 'location_level' => $_location_level));


			for ($i=2; $i< ($_location_level +1); $i++)
			{
				$cols_group[] = "fm_location{$i}.loc{$i}_name";
			}

			$cols_group[] = "{$entity_table}.multiplier";
			$cols_group[] = "{$entity_table}.id";
			$cols_group[] = 'fm_request_status.descr';
			$cols_group[] = "{$entity_table}.address";

			$groupmethod = 'GROUP BY ' . implode(',', $cols_group);

			if ($order)
			{
				switch($order)
				{
					case 'planned_year':
						$ordermethod = " ORDER BY planned_year $sort";
						break;
					default:
						$ordermethod = " order by $order $sort";					
				}
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

			if ($recommended_year > 0)
			{
				$filtermethod .= " $where fm_request.recommended_year = {$recommended_year}";
				$where = 'AND';
			}

			if ($condition_survey_id > 0)
			{
				$filtermethod .= " $where fm_request.condition_survey_id = '{$condition_survey_id}'";
				$where = 'AND';
			}

			if ($status_id && $status_id != 'all')
			{

				if($status_id == 'open')
				{
					$_status_filter = array();
					$this->_db->query("SELECT * FROM fm_request_status WHERE delivered IS NULL AND closed IS NULL");
					while($this->_db->next_record())
					{
						$_status_filter[] = $this->_db->f('id');
					}
					$filtermethod .= " $where fm_request.status IN ('" . implode("','", $_status_filter) . "')"; 
				}
				else
				{
					$filtermethod .= " $where fm_request.status='$status_id' ";
				}
				$where= 'AND';
			}

			if ($degree_id)
			{
				$degree_id = (int)$degree_id -1;
				$filtermethod .= " $where fm_request_condition.degree = {$degree_id}";
				$where = 'AND';
			}

			if ($building_part)
			{
				$filtermethod .= " $where fm_request.building_part {$this->_like} '{$building_part}%'";
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

			if ($attrib_filter)
			{
				$filtermethod .= " $where " . implode(' AND ', $attrib_filter);
				$where= 'AND';
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

			if ($responsible_unit)
			{
				$filtermethod .= " $where fm_request.responsible_unit='$responsible_unit'";
				$where = 'AND';
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
					$query = $this->_db->db_addslashes($query);
					$querymethod = " $where (fm_request.title {$this->_like} '%$query%' OR fm_request.address {$this->_like} '%$query%' OR fm_request.location_code {$this->_like} '%$query%' OR fm_request.id =" . (int)$query;
					for ($i=1;$i<=($_location_level);$i++)
					{
						$querymethod .= " OR fm_location{$i}.loc{$i}_name {$this->_like} '%$query%'";
					}
					$querymethod .= ')';
				}
			}

			$sql .= " $filtermethod $querymethod";
			$sql_arr = explode('FROM', $sql);

			$sql .= " $groupmethod";

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
			array_unshift($this->uicols['formatter'],'linktToRequest');
			array_unshift($this->uicols['classname'],'');
			array_unshift($this->uicols['sortable'],true);

			$cols_return		= $this->bocommon->cols_return;
			$this->cols_extra	= $this->bocommon->cols_extra;

			$this->_db->fetchmode = 'ASSOC';

//			$sql2 = "SELECT count(*) as cnt, sum(amount_investment) as sum_investment, sum(amount_operation) as sum_operation, sum(amount_potential_grants) as sum_potential_grants FROM ({$sql}) as t";
			$sql2 = "SELECT count(*) as cnt, (sum(amount_investment * multiplier)) as sum_investment, (sum(amount_operation * multiplier)) as sum_operation, (sum(amount_potential_grants * multiplier)) as sum_potential_grants FROM {$sql_arr[1]}";

			$this->_db->query($sql2,__LINE__,__FILE__);
			$this->_db->next_record();
			$this->_total_records = $this->_db->f('cnt');
			$this->sum_investment	= $this->_db->f('sum_investment');
			$this->sum_operation	= $this->_db->f('sum_operation');
			$this->sum_potential_grants	= $this->_db->f('sum_potential_grants');

			$sql3 = "SELECT sum(fm_request_consume.amount) as sum_consume  FROM {$sql_arr[1]}";
			$this->_db->query($sql3,__LINE__,__FILE__);
			$this->_db->next_record();
			$this->sum_consume	= $this->_db->f('sum_consume');
			
//			_debug_array($sql_arr);

			//cramirez.r@ccfirst.com 23/10/08 avoid retrieve data in first time, only render definition for headers (var myColumnDefs)
			if($dry_run)
			{
				return array();
			}
			else
			{
				if(!$allrows)
				{
					$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
				}
				else
				{
					$this->_db->query($sql . $ordermethod,__LINE__,__FILE__);
				}
			}
			$_datatype = array();
			foreach($this->uicols['name'] as $key => $_name)
			{
				$_datatype[$_name] =  $this->uicols['datatype'][$key];
			}
			$dataset = array();
			$j=0;
			while ($this->_db->next_record())
			{
				foreach($cols_return as $key => $field)
				{
					$dataset[$j][$field] = array
						(
							'value'		=> $this->_db->f($field),
							'datatype'	=> $_datatype[$field],
							'attrib_id'	=> $_attrib[$field]
						);
				}
				$j++;
			}

			foreach ($dataset as &$entry)
			{
				$sql = "SELECT sum(amount) as consume FROM fm_request_consume WHERE request_id={$entry['request_id']['value']}";
				$this->_db->query($sql,__LINE__,__FILE__);
				$this->_db->next_record();

				$entry['consume'] = array
				(
					'value'		=> $this->_db->f('consume'),
					'datatype'	=> false,
					'attrib_id'	=> false,
				);
					
			}

			$values = $this->custom->translate_value($dataset, $location_id);

			return $values;
		}

		function read_single($request_id, $values = array())
		{
			$request_id = (int) $request_id;
			$sql = "SELECT * FROM fm_request WHERE id={$request_id}";

			$this->_db->query($sql,__LINE__,__FILE__);

			$request = array();
			if ($this->_db->next_record())
			{
				$amount_investment			=  $this->_db->f('amount_investment');
				$amount_operation			=  $this->_db->f('amount_operation');
				$amount_potential_grants	=  $this->_db->f('amount_potential_grants');
				$budget = $amount_investment + $amount_operation;
				$recommended_year = $this->_db->f('recommended_year');

				$request = array
				(
					'id'						=> $this->_db->f('id'),
					'request_id'				=> $this->_db->f('id'), // FIXME
					'title'						=> $this->_db->f('title', true),
					'location_code'				=> $this->_db->f('location_code'),
					'descr'						=> $this->_db->f('descr', true),
					'status'					=> $this->_db->f('status'),
					'amount_investment'			=> $amount_investment,
					'amount_operation'			=> $amount_operation,
					'amount_potential_grants'	=> $amount_potential_grants,
					'budget'					=> (int)$budget,
					'tenant_id'					=> $this->_db->f('tenant_id'),
					'owner'						=> $this->_db->f('owner'),
					'coordinator'				=> $this->_db->f('coordinator'),
					'responsible_unit'			=> $this->_db->f('responsible_unit'),
					'recommended_year'			=> $recommended_year ? $recommended_year : '',//hide '0' - which is needed for sorting
					'access'					=> $this->_db->f('access'),
					'start_date'				=> $this->_db->f('start_date'),
					'end_date'					=> $this->_db->f('end_date'),
					'cat_id'					=> $this->_db->f('category'),
					'branch_id'					=> $this->_db->f('branch_id'),
					'authorities_demands'		=> $this->_db->f('authorities_demands'),
					'score'						=> $this->_db->f('score'),
					'p_num'						=> $this->_db->f('p_num'),
					'p_entity_id'				=> $this->_db->f('p_entity_id'),
					'p_cat_id'					=> $this->_db->f('p_cat_id'),
					'contact_phone'				=> $this->_db->f('contact_phone', true),
					'building_part'				=> $this->_db->f('building_part'),
					'entry_date'				=> $this->_db->f('entry_date'),
					'closed_date'				=> $this->_db->f('closed_date'),
					'in_progress_date'			=> $this->_db->f('in_progress_date'),
					'delivered_date'			=> $this->_db->f('delivered_date'),
					'regulations' 				=> explode(',', $this->_db->f('regulations')),
					'multiplier'				=> (float) $this->_db->f('multiplier'),
				);

				if ( isset($values['attributes']) && is_array($values['attributes']) )
				{
					$request['attributes'] = $values['attributes'];
					foreach ( $request['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->_db->f($attr['column_name']);
					}
				}

				$location_code = $this->_db->f('location_code');
				$request['power_meter']		= $this->soproject->get_power_meter($location_code);

				$sql = "SELECT * FROM fm_request_planning WHERE request_id={$request_id} ORDER BY date ASC";
				$this->_db->query($sql,__LINE__,__FILE__);
				while($this->_db->next_record())
				{
					$request['planning'][] = array
					(
						'id'			=> $this->_db->f('id'),
						'amount'		=> $this->_db->f('amount'),
						'date'			=> $this->_db->f('date'),
						'user_id'		=> $this->_db->f('user_id'),
						'entry_date'	=> $this->_db->f('entry_date'),
						'descr'			=> $this->_db->f('descr',true)
					);
				}

				$sql = "SELECT * FROM fm_request_consume WHERE request_id={$request_id} ORDER BY date ASC";
				$this->_db->query($sql,__LINE__,__FILE__);
				while($this->_db->next_record())
				{
					$request['consume'][] = array
					(
						'id'			=> $this->_db->f('id'),
						'amount'		=> $this->_db->f('amount'),
						'date'			=> $this->_db->f('date'),
						'user_id'		=> $this->_db->f('user_id'),
						'entry_date'	=> $this->_db->f('entry_date'),
						'descr'			=> $this->_db->f('descr',true)
					);
				}
			}

			return $request;
		}

		function request_workorder_data($request_id = '')
		{
			$request_id = (int)$request_id;
			$this->_db->query("select budget, id as workorder_id, vendor_id from fm_workorder where request_id='$request_id'");
			$budget = array();
			while ($this->_db->next_record())
			{
				$budget[] = array
					(
						'workorder_id'	=> $this->_db->f('workorder_id'),
						'budget'	=> sprintf("%01.2f",$this->_db->f('budget')),
						'vendor_id'	=> $this->_db->f('vendor_id')
					);
			}
			return $budget;
		}


		function increment_request_id()
		{
			$name = 'request';
			$now = time();
			$this->_db->query("SELECT value, start_date FROM fm_idgenerator WHERE name='{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->_db->next_record();
			$next_id = $this->_db->f('value') +1;
			$start_date = (int)$this->_db->f('start_date');
			$this->_db->query("UPDATE fm_idgenerator SET value = $next_id WHERE name = '{$name}' AND start_date = {$start_date}");
			return $next_id;
		}

		function next_id()
		{
			$name = 'request';
			$now = time();
			$this->_db->query("SELECT value FROM fm_idgenerator WHERE name = '{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->_db->next_record();
			$id = $this->_db->f('value')+1;
			return $id;
		}

		function add($request, $values_attribute = array())
		{
			$receipt = array();

			$value_set = array();

			$data = $request;
			$data['attributes'] = $values_attribute;
			
			$value_set			= $this->_get_value_set( $data );

			if ( $this->_db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->_db->transaction_begin();
			}

			$id = $this->next_id();

			$value_set['id'] 						= $id;
			$value_set['title']						= $this->_db->db_addslashes($request['title']);
			$value_set['owner']						= $this->account;
			$value_set['category']					= $request['cat_id'];
			$value_set['descr']						= $this->_db->db_addslashes($request['descr']);
//			$value_set['location_code']				= $request['location_code'];
			$value_set['entry_date']				= time();
			$value_set['amount_investment']			= (int) $request['amount_investment'];
			$value_set['amount_operation']			= (int) $request['amount_operation'];
			$value_set['amount_potential_grants']	= (int) $request['amount_potential_grants'];
			$value_set['status']					= $request['status'];
			$value_set['branch_id']					= $request['branch_id'];
			$value_set['coordinator']				= $request['coordinator'];
			$value_set['authorities_demands']		= $request['authorities_demands'];
			$value_set['building_part']				= $request['building_part'];
			$value_set['start_date']				= $request['start_date'];
			$value_set['end_date']					= $request['end_date'];
			$value_set['regulations']				= $request['regulations'] ? ',' . implode(',',$request['regulations']) . ',' : '';
			$value_set['condition_survey_id'] 		= $request['condition_survey_id'];
			$value_set['responsible_unit']			= $request['responsible_unit'];
			$value_set['recommended_year']			= (int) $request['recommended_year'];
			$value_set['multiplier']				= $request['multiplier'] ? (float)$request['multiplier'] : 1;
			

			$cols = implode(',', array_keys($value_set));
			$values	= $this->_db->validate_insert(array_values($value_set));

			$this->_db->query("INSERT INTO fm_request ({$cols}) VALUES ({$values})",__LINE__,__FILE__);


			if(isset($request['condition']) && is_array($request['condition']))
			{
				foreach( $request['condition'] as $condition_type => $value_type )
				{
					$_condition_type = isset($value_type['condition_type']) && $value_type['condition_type'] ? $value_type['condition_type'] : $condition_type;
					if($_condition_type)
					{
						$this->_db->query("INSERT INTO fm_request_condition (request_id,condition_type,reference,degree,probability,consequence,user_id,entry_date) "
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
				$this->_db->query("update fm_tenant set contact_phone='". $request['extra']['contact_phone']. "' where id='". $request['extra']['tenant_id']. "'",__LINE__,__FILE__);
			}

			if ($request['power_meter'] )
			{
				$this->soproject->update_power_meter($request['power_meter'],$request['location_code'],$address);
			}

			if($interlink_data = $this->_get_interlink_data($id, $request, '.project.request'))
			{
				$this->interlink->add($interlink_data,$this->_db);
			}

			$sql = "SELECT * FROM fm_request_status WHERE id='{$request['status']}'";
			$this->_db->query($sql,__LINE__,__FILE__);
			$this->_db->next_record();

			$value_set = array();
			if($this->_db->f('in_progress'))
			{
				$value_set['in_progress_date']	= time();
			}
			if($this->_db->f('closed'))
			{
				$value_set['closed_date']		= time();
			}
			if($this->_db->f('delivered'))
			{
				$value_set['delivered_date']	= time();
			}

			if($value_set)
			{
				$value_set	= $this->_db->validate_update($value_set);
				$this->_db->query("UPDATE fm_request SET $value_set WHERE id= '{$id}'",__LINE__,__FILE__);
			}

			if($request['planning_value'] && $request['planning_date'])
			{
				$this->_db->query("INSERT INTO fm_request_planning (request_id,amount,date,user_id,entry_date) "
					. "VALUES ('"
					. $id . "','"
					. (int)$request['planning_value'] . "',"
					. (int)$request['planning_date']. ","
					. $this->account . ","
					. time() . ")",__LINE__,__FILE__);
			}

			if($request['consume_value'] && $request['consume_date'])
			{
				$this->_db->query("INSERT INTO fm_request_consume (request_id,amount,date,user_id,entry_date) "
					. "VALUES ('"
					. $id . "','"
					. (int)$request['consume_value'] . "',"
					. (int)$request['consume_date']. ","
					. $this->account . ","
					. time() . ")",__LINE__,__FILE__);
			}

			$this->increment_request_id();
			$this->historylog->add('SO',$id,$request['status']);
			$this->historylog->add('TO',$id,$request['cat_id']);
			$this->historylog->add('CO',$id,$request['coordinator']);
			$receipt['message'][] = array('msg'=>lang('request %1 has been saved',$id));


			if ( !$this->global_lock )
			{
				$this->_db->transaction_commit();
			}

//				$receipt['error'][] = array('msg'=>lang('request %1 has not been saved',$id));

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
				$address = $this->_db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->_db->db_addslashes($request['location_name']);
			}

			$value_set = array
			(
				'title' 					=> $this->_db->db_addslashes($request['title']),
				'status'					=> $request['status'],
				'category'					=> $request['cat_id'],
				'start_date'				=> $request['start_date'],
				'end_date'					=> $request['end_date'],
				'coordinator'				=> $request['coordinator'],
				'descr'						=> $this->_db->db_addslashes($request['descr']),
				'amount_investment'			=> (int)$request['amount_investment'],
				'amount_operation'			=> (int)$request['amount_operation'],
				'amount_potential_grants'	=> (int)$request['amount_potential_grants'],
				'location_code'				=> $request['location_code'],
				'address'					=> $address,
				'authorities_demands'		=> $request['authorities_demands'],
				'building_part'				=> $request['building_part'],
				'regulations'				=> $request['regulations'] ? ',' . implode(',',$request['regulations']) . ',' : '',
				'responsible_unit'			=> $request['responsible_unit'],
				'recommended_year'			=> (int) $request['recommended_year'],
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


			$this->_db->transaction_begin();

			$this->_db->query("SELECT amount_investment, amount_operation, amount_potential_grants, status,category,coordinator FROM fm_request where id='" .$request['id']."'",__LINE__,__FILE__);
			$this->_db->next_record();

			$old_investment			= $this->_db->f('amount_investment');
			$old_operation			= $this->_db->f('amount_operation');
			$old_potential_grants	= $this->_db->f('amount_potential_grants');
			$old_status				= $this->_db->f('status');
			$old_category			= $this->_db->f('category');
			$old_coordinator		= $this->_db->f('coordinator');
			if($old_status != $request['status'])
			{
				$sql = "SELECT * FROM fm_request_status WHERE id='{$request['status']}'";
				$this->_db->query($sql,__LINE__,__FILE__);
				$this->_db->next_record();

				if($this->_db->f('in_progress'))
				{
					$value_set['in_progress_date']	= time();
				}
				if($this->_db->f('closed'))
				{
					$value_set['closed_date']		= time();
				}
				if($this->_db->f('delivered'))
				{
					$value_set['delivered_date']	= time();
				}
			}

			$value_set	= $this->_db->validate_update($value_set);

			$this->_db->query("UPDATE fm_request SET $value_set WHERE id= '{$request['id']}'",__LINE__,__FILE__);

			$this->_db->query("DELETE FROM fm_request_condition WHERE request_id='{$request['id']}'",__LINE__,__FILE__);

			if(isset($request['condition']) && is_array($request['condition']))
			{
				foreach( $request['condition'] as $condition_type => $value_type )
				{
					$_condition_type = isset($value_type['condition_type']) && $value_type['condition_type'] ? $value_type['condition_type'] : $condition_type;
					if(isset($value_type['condition_type']) && !$value_type['condition_type'])
					{
						continue;
					}
					$this->_db->query("INSERT INTO fm_request_condition (request_id,condition_type,reference,degree,probability,consequence,user_id,entry_date) "
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
				$this->_db->query("UPDATE fm_tenant SET contact_phone='{$request['extra']['contact_phone']}' WHERE id='{$request['extra']['tenant_id']}'",__LINE__,__FILE__);
			}

			if ($request['power_meter'] )
			{
				$this->soproject->update_power_meter($request['power_meter'],$request['location_code'],$address);
			}

			if($request['planning_value'] && $request['planning_date'])
			{
				$this->_db->query("INSERT INTO fm_request_planning (request_id,amount,date,user_id,entry_date) "
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
					$this->_db->query("DELETE FROM fm_request_planning WHERE id =" . (int)$delete_planning,__LINE__,__FILE__);
				}
			}

			if($request['consume_value'] && $request['consume_date'])
			{
				$this->_db->query("INSERT INTO fm_request_consume (request_id,amount,date,user_id,entry_date) "
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
					$this->_db->query("DELETE FROM fm_request_consume WHERE id =" . (int)$delete_consume,__LINE__,__FILE__);
				}
			}

			if($this->_db->transaction_commit())
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
/*
				if ((int)$old_investment != (int)$request['amount_investment'])
				{
					$this->historylog->add('B', $request['id'], $request['amount_investment'], $old_investment);
				}
				if ((int)$old_operation != (int)$request['amount_operation'])
				{
					$this->historylog->add('B', $request['id'], $request['amount_operation'], $old_operation);
				}
				if ((int)$old_potential_grants != (int)$request['amount_potential_grants'])
				{
					$this->historylog->add('B', $request['id'], $request['amount_potential_grants'], $old_potential_grants);
				}
*/
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
			$this->_db->transaction_begin();
			$this->_db->query("DELETE FROM fm_request_planning WHERE request_id = {$request_id}",__LINE__,__FILE__);
			$this->_db->query("DELETE FROM fm_request_consume WHERE request_id = {$request_id}",__LINE__,__FILE__);
			$this->_db->query("DELETE FROM fm_request_condition WHERE request_id = {$request_id}",__LINE__,__FILE__);
			$this->_db->query("DELETE FROM fm_request_history  WHERE  history_record_id = {$request_id}",__LINE__,__FILE__);
			$this->_db->query("DELETE FROM fm_request WHERE id = {$request_id}",__LINE__,__FILE__);
			$this->interlink->delete_at_target('property', '.project.request', $request_id, $this->_db);
			$this->_db->transaction_commit();
		}

		public function get_user_list()
		{
			$values = array();
			$users = $GLOBALS['phpgw']->accounts->get_list('accounts', $start=-1, $sort='ASC', $order='account_lastname', $query,$offset=-1);
			$sql = 'SELECT DISTINCT coordinator FROM fm_request';
			$this->_db->query($sql,__LINE__,__FILE__);

			$account_lastname = array();
			while($this->_db->next_record())
			{
				$user_id	= $this->_db->f('coordinator');
				if(isset($users[$user_id]))
				{
					$name	= $users[$user_id]->__toString();
					$values[] = array
					(
						'user_id' 	=> $user_id,
						'name'		=> $name
					);
					$account_lastname[]  = $name;
				}
			}

			if($values)
			{
				array_multisort($account_lastname, SORT_ASC, $values);
			}

			return $values;
		}

		public function update_status_from_related($data = array())
		{

		}

		public function get_recommended_year_list()
		{
			$sql = "SELECT DISTINCT recommended_year FROM fm_request"
			. " WHERE recommended_year IS NOT NULL AND recommended_year > 0"
			. " ORDER BY recommended_year ASC";
			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();
			while($this->_db->next_record())
			{
				$year	= $this->_db->f('recommended_year');
				$values[] = array
				(
					'id' 	=> $year,
					'name'	=> $year
				);
			}
			return $values;
		}

	}
