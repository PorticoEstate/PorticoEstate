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
	* @subpackage eco
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soinvestment
	{
		function __construct()
		{
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db		= & $GLOBALS['phpgw']->db;
			$this->join		= & $this->db->join;
			$this->like		= & $this->db->like;
		}

		function get_type_list()
		{
			$this->db->query("SELECT entity_type FROM fm_investment GROUP BY entity_type ");
			$type_list = array();
			while ($this->db->next_record())
			{
				$type_list[] = Array(
					'id'        => $this->db->f('entity_type'),
					'name'       => lang($this->db->f('entity_type'))
				);
			}
			return $type_list;
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
				$filter	= (isset($data['filter'])?$data['filter']:'');
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:'');
				$part_of_town_id = (isset($data['part_of_town_id'])?$data['part_of_town_id']:'');
				$allrows 		= (isset($data['allrows'])?$data['allrows']:'');

			}
			if (!$cat_id)
			{
				return;
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				if ($cat_id=='property')
				{
					$ordermethod = ' order by fm_part_of_town.name ,fm_location1.loc1 DESC';
				}
				else
				{
					$ordermethod = ' order by fm_investment.entity_id  DESC';
				}
			}

			if ($part_of_town_id)
			{
				$filtermethod = "and fm_part_of_town.part_of_town_id ='$part_of_town_id'";
			}

			if ($filter=='investment')
			{
				$filtermethod .= "and initial_value > 0";
			}
			if ($filter=='funding')
			{
				$filtermethod .= "and initial_value < 0";
			}

			if ($cat_id=='property')
			{
				$sql = "SELECT fm_investment.entity_id as entity_id, fm_investment.descr as descr, fm_investment_value.invest_id,initial_value, fm_location1.loc1_name as name, fm_part_of_town.district_id, fm_part_of_town.name as part_of_town,"
					. " fm_investment_value.value, fm_investment_value.index_date, fm_investment_value.this_index, "
					. " fm_investment_value.index_count"
					. " FROM (((fm_investment $this->join fm_investment_value ON ( fm_investment.entity_id = fm_investment_value.entity_id) AND "
					. " (fm_investment.invest_id = fm_investment_value.invest_id )) "
					. " $this->join fm_location1 ON (fm_investment.loc1 = fm_location1.loc1)) "
					. " $this->join fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id)) "
					. " WHERE ( current_index = '1'  or (this_index = NULL and index_count= '1'))  AND entity_type ='$cat_id' $filtermethod ";

			}
			else
			{
				$sql = "SELECT fm_investment.location_code,fm_investment.entity_id, fm_investment.descr as descr, fm_investment_value.invest_id,initial_value, fm_part_of_town.district_id, fm_part_of_town.name as part_of_town,"
					. " fm_investment_value.value, fm_investment_value.index_date, fm_investment_value.this_index,fm_entity_category.name as entity_name, "
					. " fm_investment_value.index_count "
					. " FROM ((((fm_investment $this->join "
					. " fm_entity_category ON (fm_investment.p_entity_id = fm_entity_category.entity_id AND fm_investment.p_cat_id = fm_entity_category.id)) $this->join "
					. " fm_investment_value ON (fm_investment_value.entity_id = fm_investment.entity_id) AND "
					. " (fm_investment_value.invest_id = fm_investment.invest_id)) "
					. " $this->join fm_location1 ON (fm_investment.loc1 = fm_location1.loc1)) "
					. " $this->join fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id)) "
					. " WHERE ( current_index = '1'  or (this_index = NULL and index_count= '1'))  AND entity_type ='$cat_id' $filtermethod ";
			}
			if($sql)
			{
				$this->db->query($sql,__LINE__,__FILE__);
				$this->total_records = $this->db->num_rows();

				if(!$allrows)
				{
					$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
				}
				else
				{
					$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
				}
			}


			$investment = array();
			$i=0;
			while ($this->db->next_record())
			{
				$investment[$i]['counter']			= $i;
				$investment[$i]['location_code'] 	= $this->db->f('location_code');
				$investment[$i]['entity_id'] 		= $this->db->f('entity_id');
				$investment[$i]['investment_id'] 	= $this->db->f('invest_id');
				$investment[$i]['district_id'] 		= $this->db->f('district_id');
				$investment[$i]['part_of_town'] 	= $this->db->f('part_of_town');
				$investment[$i]['descr'] 			= $this->db->f('descr');
				$investment[$i]['initial_value'] 	= $this->db->f('initial_value');
				$investment[$i]['value'] 			= $this->db->f('value');
				$investment[$i]['this_index'] 		= $this->db->f('this_index');
				$investment[$i]['index_count'] 		= $this->db->f('index_count');
				$investment[$i]['date'] 			= $this->db->f('index_date');
				if ($cat_id=='property')
				{
					$investment[$i]['entity_name'] 	= $this->db->f('name');
				}
				else
				{
					$investment[$i]['entity_name'] 	= $this->db->f('entity_name');
				}

				$investment[$i]['this_write_off'] 	= round(($this->db->f('this_index') * $this->db->f('initial_value')),2);
				$i++;
			}

			//_debug_array($investment);
			return $investment;
		}

		function save_investment($values)
		{
			//_debug_array($values);

			$receipt = array();
			while (is_array($values['location']) && list($input_name,$value) = each($values['location']))
			{
				if($value)
				{
					$cols[] = $input_name;
					$vals[] = $value;
				}
			}

			while (is_array($values['extra']) && list($input_name,$value) = each($values['extra']))
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

			if($values['street_name'])
			{
				$address[]= $values['street_name'];
				$address[]= $values['street_number'];
				$address	= $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($values['location_name']);
			}


			$period	 	= $values['period'];
			$type	 	= $values['funding'];
			$initial_value	= $values['initial_value'];
			$descr	 	= $this->db->db_addslashes($values['descr']);
			$date	 	= $values['date'];
			$location_code	= $values['location_code'];
			$entity_id	= $values['entity_id'];
			$entity_type	= $values['entity_type'];


			$this->db->query("select max(invest_id) as max_invest_id from fm_investment Where entity_id= '$entity_id'");
			$this->db->next_record();
			$next_invest_id  = $this->db->f('max_invest_id')+1;

			$this->db->transaction_begin();

			$this->db->query("insert into fm_investment (entity_id, invest_id,entity_type,location_code,writeoff_year, descr $cols) "
				. " values ('$entity_id', '$next_invest_id','$entity_type','$location_code','$period','$descr' $vals )");

			$this->db->query("insert into fm_investment_value (entity_id, invest_id, index_count, this_index, current_index, value,initial_value, index_date) "
				. " values ('$entity_id', '$next_invest_id','1', '0', '1','$initial_value','$initial_value','$date')");


			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg' => lang('Investment added !'));
				$receipt['message'][]=array('msg' => lang('Entity ID'). ' ' . $entity_id);
				$receipt['message'][]=array('msg' => lang('Investment ID:'). ' ' . $next_invest_id);
			}
			return $receipt;
		}

		function update_investment($values)
		{
			$receipt = array();

			if ($values)
			{
				$this->db->transaction_begin();
				foreach($values as $entry)
				{
					$this->db->query("select max(index_count) as max_index_count from fm_investment_value Where entity_id='" .$entry['entity_id'] . "' and invest_id=" .$entry['invest_id']);
					$this->db->next_record();
					$next_index_count  = $this->db->f('max_index_count')+1;

					$this->db->query("update fm_investment_value set current_index = Null"
						. " WHERE entity_id='" . $entry['entity_id'] . "' and invest_id=" . $entry['invest_id']);

					$insert= array(
						$entry['entity_id'],
						$entry['invest_id'],
						$next_index_count,
						$entry['new_index'],
						$entry['new_value'],
						$entry['initial_value'],
						$entry['date'],
						1
					);

					$insert	= $this->db->validate_insert($insert);


					$this->db->query("insert into fm_investment_value (entity_id, invest_id, index_count, this_index, value,initial_value, index_date,current_index) "
						. " values ($insert)");

					$receipt['message'][]=array('msg'=>lang('investment %1 is updated at entity %2', $entry['invest_id'], $entry['entity_id']));
				}
				$this->db->transaction_commit();
				return $receipt;
			}
		}

		function read_single($entity_id,$investment_id,$start,$allrows)
		{
			if (!$start)
			{
				$start=0;
			}


			$sql = "SELECT index_count, this_index,current_index,value, initial_value, index_date  "
				. " FROM fm_investment_value Where entity_id= '$entity_id' and invest_id= '$investment_id' order by index_count";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql,__LINE__,__FILE__);
			}

			$investment = array();
			$i=0;
			while ($this->db->next_record())
			{
				$investment[$i]['descr'] 			= $this->db->f('descr');
				$investment[$i]['initial_value'] 	= $this->db->f('initial_value');
				$investment[$i]['value'] 			= $this->db->f('value');
				$investment[$i]['this_index'] 		= $this->db->f('this_index');
				$investment[$i]['current_index'] 	= $this->db->f('current_index');
				$investment[$i]['index_count'] 		= $this->db->f('index_count');
				$investment[$i]['date'] 			= $this->db->f('index_date');
				$investment[$i]['this_write_off'] 	= round(($this->db->f('this_index') * $this->db->f('initial_value')),2);
				$i++;
			}

			return $investment;
		}


		function write_off_period_list()
		{
			$this->db->query("SELECT writeoff_year FROM fm_investment GROUP BY writeoff_year ",__LINE__,__FILE__);

			$period_list = array();
			while ($this->db->next_record())
			{
				$period_list[] = Array(
					'period'        => $this->db->f('writeoff_year')
				);
			}

			return $period_list;

		}

		function delete($entity_id,$invest_id,$index_count)
		{
			$this->db->transaction_begin();
			if ($index_count==1)
			{
				$this->db->query("update fm_investment_value set current_index = '0', this_index=Null, value=Null,initial_value=Null,index_date=Null  where entity_id='$entity_id' and invest_id= '$invest_id' and index_count= '1'");
			}
			else
			{
				$this->db->query("delete from fm_investment_value where entity_id='$entity_id' and invest_id= '$invest_id' and index_count= '$index_count'");

				$new_current= $index_count -1;

				$this->db->query("update fm_investment_value set current_index = '1' where entity_id='$entity_id' and invest_id= '$invest_id' and index_count= '$new_current'");
			}

			//		$this->db->query("DELETE FROM fm_investment_value WHERE entity_id= '$entity_id'  and invest_id='$invest_id' and index_count='$index_count'",__LINE__,__FILE__);
			$this->db->transaction_commit();
		}
	}
