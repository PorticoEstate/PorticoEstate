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

	class property_sogab
	{
		var $gab_insert_level;
		var $payment_date = array();

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->db2          = clone($this->db);
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;

			$this->config		= CreateObject('phpgwapi.config','property');
			$this->config->read();
			$this->gab_insert_level = isset($this->config->config_data['gab_insert_level']) && $this->config->config_data['gab_insert_level'] ? $this->config->config_data['gab_insert_level'] : 3;
		}

		function read($data)
		{

			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start'] : '0';
				$sort			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
				$order			= isset($data['order']) ? $data['order'] : '';
				$cat_id 		= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
				$location_code 	= isset($data['location_code']) ? $data['location_code'] : '';
				$gaards_nr		= isset($data['gaards_nr'])? (int)$data['gaards_nr'] : '';
				$bruksnr		= isset($data['bruksnr']) ? (int)$data['bruksnr'] : '';
				$feste_nr		= isset($data['feste_nr']) ? (int)$data['feste_nr'] : '';
				$seksjons_nr	= isset($data['seksjons_nr'])? (int)$data['seksjons_nr'] : '';
				$allrows		= isset($data['allrows']) ? $data['allrows'] : '';
				$address		= isset($data['address']) ? $this->db->db_addslashes($data['address']) : '';
				$check_payments	= isset($data['check_payments']) ? $data['check_payments'] : '';
			}



			if ($order)
			{
				$ordermethod = " order by fm_gab_location.{$order} {$sort}";
			}
			else
			{
				$ordermethod = ' order by gab_id ASC';
			}

			$where = 'WHERE';
			$filtermethod = '';

			if ($cat_id > 0)
			{
				$filtermethod .= " {$where} fm_gab_location.category='{$cat_id}' ";
				$where = 'AND';
			}

			if ($address)
			{
				$filtermethod .= " {$where} fm_gab_location.address {$this->like} '%{$address}%' ";
				$where = 'AND';
			}
			if ($location_code)
			{
				$location_code = explode('-',$location_code);
				$i = 1;		
				foreach($location_code as $_loc)
				{
					$loc[] = $_loc;
					if($i == $this->gab_insert_level)
					{
						break;
					}
					$i++;
				}
				$location_code = implode('-', $loc);

				$filtermethod .= " {$where} fm_gab_location.location_code {$this->like} '{$location_code}%' ";
				$where = 'AND';
			}

			if ($gaards_nr)
			{
				$filtermethod .= " {$where} SUBSTRING(gab_id,5,5) {$this->like} '%$gaards_nr' ";
				$where = 'AND';
			}
			if ($bruksnr)
			{
				$filtermethod .= " {$where} SUBSTRING(gab_id,10,4) {$this->like} '%$bruksnr' ";
				$where = 'AND';
			}
			if ($feste_nr)
			{
				$filtermethod .= " {$where} SUBSTRING(gab_id,14,4) {$this->like} '%$feste_nr' ";
				$where = 'AND';
			}
			if ($seksjons_nr)
			{
				$filtermethod .= " {$where} SUBSTRING(gab_id,18,3) {{$this->like}} '%$seksjons_nr' ";
				$where = 'AND';
			}

			if($check_payments)
			{

				$j = 1;
			}
			else
			{
				$j = $this->gab_insert_level;
			}

			$joinmethod = "$this->join fm_location". ($j);
			$on = 'ON';
			for ($i=($j); $i>0; $i--)
			{
				$joinmethod .= " $on (fm_gab_location.loc" . ($i). " = fm_location" . ($j) . ".loc" . ($i) . ")";
				$on = 'AND';
			}


			if($check_payments)
			{
				//				$sql = "SELECT gab_id,count(gab_id) as hits, address ,fm_gab_location.loc1 as location_code, fm_gab_location.owner as owner FROM fm_gab_location $joinmethod $filtermethod GROUP BY gab_id,fm_gab_location.loc1,address,owner ";
				//				$sql = "SELECT DISTINCT gab_id, fm_gab_location.loc1 as location_code, fm_gab_location.owner as owner FROM fm_gab_location $joinmethod $filtermethod GROUP BY gab_id,fm_gab_location.loc1,address,owner ";
				//				$sql = "SELECT gab_id, fm_gab_location.loc1 as location_code, fm_gab_location.owner as owner FROM fm_gab_location $joinmethod $filtermethod GROUP BY gab_id,fm_gab_location.loc1,address,owner ";

				$spvend_code = 9901;
				$spbudact_code = '11954111';
				switch($GLOBALS['phpgw_info']['server']['db_type'])
				{
				case 'postgres':
					$due_date 		= "to_char(forfallsdato,'MM/YYYY') as due_date";
					break;
				default:
					$due_date 		= "to_char(forfallsdato,'MM/YYYY') as due_date";
				}

				$sql = "SELECT sum(belop) as paid, count(fm_ecobilagoverf.loc1) as hits, {$due_date}, fm_ecobilagoverf.loc1, owner"
					. " FROM fm_ecobilagoverf {$this->left_join} fm_gab_location ON fm_ecobilagoverf.loc1 = fm_gab_location.loc1"
					. " WHERE spvend_code = '{$spvend_code}' AND spbudact_code = '{$spbudact_code}'"
					. " GROUP BY owner, forfallsdato,spbudact_code, fm_ecobilagoverf.loc1 ORDER BY forfallsdato ASC";

			}
			else
			{
				$sql = "SELECT gab_id,count(gab_id) as hits, address ,fm_gab_location.location_code, fm_gab_location.owner as owner FROM fm_gab_location $joinmethod $filtermethod GROUP BY gab_id,fm_gab_location.location_code,address,owner ";
			}

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

			$gab_list = array();
			if(!$check_payments)
			{
				while ($this->db->next_record())
				{
					$gab_list[] = array
						(
							'gab_id'		=> $this->db->f('gab_id'),
							'location_code'	=> $this->db->f('location_code'),
							'address'		=> $this->db->f('address',true),
							'hits'			=> $this->db->f('hits'),
							'owner'			=> $this->db->f('owner')
						);
				}
			}
			else
			{
				if($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'])
				{
					$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				}
				else
				{
					$dateformat = 'd-m-Y';
				}

				$gross_list = array();
				$dates = array();
				while ($this->db->next_record())
				{
					$gross_list[] = array
						(
							'gab_id'		=> '00000000000000000000',//$this->db->f('gab_id'),
							'location_code'	=> $this->db->f('loc1'),
							'address'		=> $this->db->f('address',true),
							'hits'			=> $this->db->f('hits'),
							'owner'			=> $this->db->f('owner'),
							'paid'			=> $this->db->f('paid'),
							'due_date'		=> $this->db->f('due_date'),
						);
					$dates[$this->db->f('due_date')] = true;
				}

				$dates = array_keys($dates);
				$payment_date = array();
				$location_buffer = array();
				$i=0;
				foreach ($gross_list as $entry)
				{
					if(!isset($location_buffer[$entry['location_code']]))
					{
						$gab_list[$i] = array
							(
								'location_code'	=> $entry['location_code'],
								'gab_id'		=> $entry['gab_id'],
								'address'		=> $entry['address'],
								'hits'			=> $entry['hits'],
								'owner'			=> $entry['owner']
							);
						$location_buffer[$entry['location_code']] = true;
						$j = $i;
						$i++;
					}
					foreach ( $dates as $date )
					{
						$gab_list[$i]['payment'][$date] = $entry['paid'];
					}
				}

				reset($dates);
				foreach ( $dates as $date )
				{
					$payment_date[$date] = $date;
				}

/*				foreach ($gab_list as &$entry)
				{
					$sql = "SELECT forfallsdato, belop FROM fm_ecobilagoverf WHERE item_id = '{$entry['gab_id']}' ORDER BY forfallsdato ASC";
					$this->db->query($sql,__LINE__,__FILE__);
					while ($this->db->next_record())
					{
						$entry['payment'][date($dateformat,strtotime($this->db->f('forfallsdato')))] = $this->db->f('belop');
						$payment_date[strtotime($this->db->f('forfallsdato'))] = date($dateformat,strtotime($this->db->f('forfallsdato')));
					}

					$sql = "SELECT forfallsdato, belop FROM fm_ecobilag WHERE item_id = '{$entry['gab_id']}'";
					$this->db->query($sql,__LINE__,__FILE__);
					while ($this->db->next_record())
					{
						$entry['payment'][date($dateformat,strtotime($this->db->f('forfallsdato')))] = $this->db->f('belop');
						$payment_date[strtotime($this->db->f('forfallsdato'))] = date($dateformat,strtotime($this->db->f('forfallsdato')));
					}

				}
 */
				$this->payment_date=$payment_date;
			}

			return $gab_list;
		}

		function read_detail($data)
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
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$gab_id = (isset($data['gab_id'])?$data['gab_id']:'0');
				$allrows 		= (isset($data['allrows'])?$data['allrows']:'');
			}


			$entity_table = 'fm_gab_location';

			$cols = $entity_table . '.*';

			$cols_return[] = 'location_code';
			$cols_return[] = 'gab_id';
			$cols_return[] = 'owner';
			$cols_return[] = 'address';

			$sql	= $this->bocommon->generate_sql(array('entity_table'=>$entity_table,'cols'=>$cols,'cols_return'=>$cols_return,
				'uicols'=>$uicols,'joinmethod'=>$joinmethod,'paranthesis'=>$paranthesis,'query'=>$query));


			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_gab_location.location_code ASC';
			}

			$filtermethod = " WHERE fm_gab_location.gab_id='$gab_id'";

			if ($cat_id > 0)
			{
				$filtermethod .= " AND fm_gab_location.category='$cat_id' ";
			}

			$sql .= " $filtermethod ";
			//echo $sql;
			$this->uicols		= $this->bocommon->uicols;

			$this->uicols['input_type'][]	= 'text';
			$this->uicols['name'][]			= 'owner';
			$this->uicols['descr'][]		= lang('owner');
			$this->uicols['statustext'][]	= lang('owner');
			$this->uicols['input_type'][]	= 'text';
			$this->uicols['name'][]			= 'location_code';
			$this->uicols['descr'][]		= 'location code';
			$this->uicols['statustext'][]	= 'location code';
			$this->uicols['input_type'][]	= 'text';
			$this->uicols['name'][]			= 'address';
			$this->uicols['descr'][]		= lang('address');
			$this->uicols['statustext'][]	= lang('address');

			$cols_return		= $this->bocommon->cols_return;
			$type_id			= $this->bocommon->type_id;
			$this->cols_extra	= $this->bocommon->cols_extra;


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

			$j=0;
			while ($this->db->next_record())
			{
				for ($i=0;$i<count($cols_return);$i++)
				{
					$gab_list[$j][$cols_return[$i]] = $this->db->f($cols_return[$i]);
				}

				$location_code=	$this->db->f('location_code');
				$location = explode('-',$location_code);
				for ($m=0;$m<count($location);$m++)
				{
					$gab_list[$j]['loc' . ($m+1)] = $location[$m];
					$gab_list[$j]['query_location']['loc' . ($m+1)]=implode("-", array_slice($location, 0, ($m+1)));
				}

				$j++;
			}
			return $gab_list;
		}

		function read_single($gab_id='',$location_code='')
		{
			$sql = "SELECT * from fm_gab_location where gab_id='$gab_id' and location_code='$location_code' ";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$gab['location_code']		= $location_code;
				$gab['remark']			= $this->db->f('remark');
				$gab['owner']			= $this->db->f('owner');
			}

			//_debug_array($gab);
			return $gab;
		}

		function exist_gab_location($gab_id='',$location_code='')
		{
			$this->db2->query("SELECT count(*) as cnt FROM fm_gab_location where gab_id='$gab_id' and location_code='$location_code'");

			$this->db2->next_record();

			if ( $this->db2->f('cnt'))
			{
				return true;
			}
		}


		function add($gab)
		{
			$location = explode('-', $gab['location_code']);
			$next_type	= count($location)+1;

			//_debug_array($gab);

			$where= 'WHERE';
			for ($i=0;$i<count($location);$i++)
			{
				$where_condition .= " $where loc" . ($i+1) . "='" . $location[$i] . "'";
				$where= 'AND';
			}

			$gab['remark'] = $this->db->db_addslashes($gab['remark']);

			if(!$gab['owner'])
			{
				$gab['owner']='no';
			}


			$gab_id= $gab['kommune_nr'] . sprintf("%05s",$gab['gaards_nr']) . sprintf("%04s",$gab['bruks_nr']) . sprintf("%04s",$gab['feste_nr']) . sprintf("%03s",$gab['seksjons_nr']);

			if($gab['propagate'] && ($next_type < ($this->gab_insert_level+1)))
			{

				$sql = 'SELECT location_code,loc' . $this->gab_insert_level . '_name as location_name FROM fm_location' . $this->gab_insert_level . " $where_condition ";

				$this->db->query($sql,__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					if(!$this->exist_gab_location($gab_id,$this->db->f('location_code')))
					{
						$gab_insert[] = array('location_code'	=> $this->db->f('location_code'),
							'gab_id'		=> $gab_id,
							'location_name'	=> $this->db->f('location_name'),
							'remark'		=> $gab['remark'],
							'owner'			=> $gab['owner']);
					}
					else
					{
						$gab_update[] = array('location_code'	=> $this->db->f('location_code'),
							'gab_id'		=> $gab_id,
							'location_name'	=> $this->db->f('location_name'),
							'remark'		=> $gab['remark'],
							'owner'			=> $gab['owner']);
					}
				}
			}
			else
			{
				if(count($location)==$this->gab_insert_level)
				{
					$gab_insert[] = array('location_code'=> $gab['location_code'],
						'gab_id'	=> $gab_id,
						'street_name'	=> $gab['street_name'],
						'street_number'	=> $gab['street_number'],
						'location_name'	=> $gab['location_name'],
						'remark'	=> $gab['remark'],
						'owner'			=> $gab['owner']);
				}
			}

			if($gab_insert)
			{
				$receipt = $this->insert($gab_insert);
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('Could not find any location to save to!'));
			}

			if($gab_update)
			{
				$receipt = $this->update($gab_update,$receipt);
			}

			$receipt['gab_id'] = $gab_id;

			return $receipt;
		}


		function insert($gab_insert)
		{
			$receipt['message'][] = array('msg'=>lang('gab %1 has been added',$gab_insert[0]['gab_id']));

			for ($i=0;$i<count($gab_insert);$i++)
			{
				$location = explode('-', $gab_insert[$i]['location_code']);

				while (is_array($location) && list($input_name,$value) = each($location))
				{
					if($value)
					{
						$col[] = 'loc' . ($input_name+1);
						$val[] = $value;
					}
				}

				if($col)
				{
					$cols	= "," . implode(",", $col);
					$vals	= ",'" . implode("','", $val) . "'";
				}

				if($gab_insert[$i]['street_name'])
				{
					$address[]= $gab_insert[$i]['street_name'];
					$address[]= $gab_insert[$i]['street_number'];
					$address	= $this->db->db_addslashes(implode(" ", $address));
				}

				if(!$address)
				{
					$address = $this->db->db_addslashes($gab_insert[$i]['location_name']);
				}

				$this->db->query("INSERT INTO fm_gab_location (location_code,gab_id,remark,owner,entry_date,user_id,address $cols) "
					. "VALUES ('"
					. $gab_insert[$i]['location_code']. "','"
					. $gab_insert[$i]['gab_id']. "','"
					. $gab_insert[$i]['remark']. "','"
					. $gab_insert[$i]['owner']. "','"
					. time() . "','"
					. $this->account. "','"
					. $address . "' $vals )",__LINE__,__FILE__);

				$receipt['message'][] = array('msg'=>lang('at location %1',$gab_insert[$i]['location_code']));

				unset($location);
				unset($col);
				unset($val);
				unset($cols);
				unset($vals);
				unset($address);

			}


			return $receipt;
		}

		function update($gab_update,$receipt)
		{
			$receipt['message'][] = array('msg'=>lang('gab %1 has been updated',$gab_update[0]['gab_id']));

			for ($i=0;$i<count($gab_update);$i++)
			{
				$this->db->query("UPDATE fm_gab_location set
					remark			='" . $gab_update[$i]['remark'] . "',
					owner			='" . $gab_update[$i]['owner'] . "',
					entry_date		='"	. time() . "',
					user_id			='" . $this->account
					. "' WHERE location_code = '" . $gab_update[$i]['location_code'] ."' AND gab_id= '" . $gab_update[$i]['gab_id'] . "'",__LINE__,__FILE__);

				$receipt['message'][] = array('msg'=>lang('at location %1',$gab_update[$i]['location_code']));
			}

			return $receipt;
		}

		function edit($gab)
		{
			$location = explode('-', $gab['location_code']);

			//_debug_array($gab);

			if(!$gab['owner'])
			{
				$gab['owner']='no';
			}

			if(count($location)==$this->gab_insert_level)
			{

				$this->db->query("UPDATE fm_gab_location set
					remark			='" . $gab['remark'] . "',
					owner			='" . $gab['owner'] . "',
					entry_date		='"	. time() . "',
					user_id			='" . $this->account
					. "' WHERE location_code= '" . $gab['location_code'] ."' and gab_id= '" . $gab['gab_id'] ."'",__LINE__,__FILE__);

				$receipt['message'][] = array('msg'=>lang('gab %1 has been edited',"'".$gab['gab_id']."'"));
				$receipt['message'][] = array('msg'=>lang('at location %1',$gab['location_code']));
			}
			else
			{
				$receipt['error'][] = array('msg'=>lang('Nothing to do!'));
			}
			$receipt['gab_id'] = $gab['gab_id'];
			return $receipt;

		}

		function delete($gab_id='',$location_code='')
		{
			$this->db->query("DELETE FROM fm_gab_location WHERE gab_id='$gab_id' and location_code='$location_code'",__LINE__,__FILE__);
		}
	}

