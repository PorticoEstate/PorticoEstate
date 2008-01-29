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
 	* @version $Id: class.sogab.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_sogab
	{
		var $gab_insert_level;
		var $payment_date;

		function property_sogab($gab_insert_level)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();
			$this->join			= $this->bocommon->join;
			$this->like			= $this->bocommon->like;

			$this->gab_insert_level = $gab_insert_level;
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
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$loc1 = (isset($data['loc1'])?$data['loc1']:'');
				$gaards_nr = (isset($data['gaards_nr'])?$data['gaards_nr']:'');
				$bruksnr = (isset($data['bruksnr'])?$data['bruksnr']:'');
				$feste_nr = (isset($data['feste_nr'])?$data['feste_nr']:'');
				$seksjons_nr = (isset($data['seksjons_nr'])?$data['seksjons_nr']:'');
				$allrows 	= (isset($data['allrows'])?$data['allrows']:'');
				$address 	= (isset($data['address'])?$data['address']:'');
				$check_payments = (isset($data['check_payments'])?$data['check_payments']:'');
				
			}

			if ($order)
			{
				$ordermethod = " order by fm_gab_location.$order $sort";
			}
			else
			{
				$ordermethod = ' order by gab_id ASC';
			}

			$where = 'WHERE';
			$filtermethod = '';
			
			if ($cat_id > 0)
			{
				$filtermethod .= " $where fm_gab_location.category='$cat_id' ";
				$where = 'AND';
			}

			if ($address)
			{
				$filtermethod .= " $where fm_gab_location.address $this->like '%$address%' ";
				$where = 'AND';
			}
			if ($loc1)
			{
				$filtermethod .= " $where fm_gab_location.loc1='$loc1' ";
				$where = 'AND';
			}

			if ($gaards_nr)
			{
				$filtermethod .= " $where SUBSTRING(gab_id,5,5) $this->like '%$gaards_nr%' ";
				$where = 'AND';
			}
			if ($bruksnr)
			{
				$filtermethod .= " $where SUBSTRING(gab_id,10,4) $this->like '%$bruksnr%' ";
				$where = 'AND';
			}
			if ($feste_nr)
			{
				$filtermethod .= " $where SUBSTRING(gab_id,14,4) $this->like '%$feste_nr%' ";
				$where = 'AND';
			}
			if ($seksjons_nr)
			{
				$filtermethod .= " $where SUBSTRING(gab_id,18,3) $this->like '%$seksjons_nr%' ";
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
				$sql = "SELECT gab_id,count(gab_id) as hits, loc" . $j . "_name as address ,fm_gab_location.loc1 as location_code, fm_gab_location.owner as owner FROM fm_gab_location $joinmethod $filtermethod GROUP BY gab_id,fm_gab_location.loc1,loc" . $j . "_name,owner ";			
			}
			else
			{
				$sql = "SELECT gab_id,count(gab_id) as hits, loc" . $j . "_name as address ,fm_gab_location.location_code, fm_gab_location.owner as owner FROM fm_gab_location $joinmethod $filtermethod GROUP BY gab_id,fm_gab_location.location_code,loc" . $j . "_name,owner ";			
			}

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$gab_list = array();
			while ($this->db->next_record())
			{
				$gab_list[] = array
				(
					'gab_id'		=> $this->db->f('gab_id'),
					'location_code'	=> $this->db->f('location_code'),
					'address'		=> stripslashes($this->db->f('address')),
					'hits'			=> $this->db->f('hits'),
					'owner'			=> $this->db->f('owner')
					);
			}
			
			if($check_payments)
			{
				if($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'])
				{
					$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				}
				else
				{
					$dateformat = 'd-m-Y';
				}
				
				for ($i=0;$i<count($gab_list);$i++)
				{
					$sql = "SELECT * FROM fm_ecobilagoverf WHERE item_id = '" . $gab_list[$i]['gab_id'] . "'";
					$this->db->query($sql,__LINE__,__FILE__);
					while ($this->db->next_record())
					{
						$gab_list[$i]['payment'][date($dateformat,strtotime($this->db->f('forfallsdato')))] = $this->db->f('belop');
						$payment_date[strtotime($this->db->f('forfallsdato'))] = date($dateformat,strtotime($this->db->f('forfallsdato')));
					}

					$sql = "SELECT * FROM fm_ecobilag WHERE item_id = '" . $gab_list[$i]['gab_id'] . "'";
					$this->db->query($sql,__LINE__,__FILE__);
					while ($this->db->next_record())
					{
						$gab_list[$i]['payment'][date($dateformat,strtotime($this->db->f('forfallsdato')))] = $this->db->f('belop');
						$payment_date[strtotime($this->db->f('forfallsdato'))] = date($dateformat,strtotime($this->db->f('forfallsdato')));
					}
					
				}

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

			$cols .= $entity_table . '.location_code,';
			$cols .= $entity_table . '.owner';

			$cols_return[] = 'location_code';
			$cols .= ',gab_id';
			$cols_return[] = 'gab_id';
			$cols_return[] = 'owner';
			
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

			$this->uicols['input_type'][]		= 'text';
			$this->uicols['name'][]			= 'owner';
			$this->uicols['descr'][]		= lang('owner');
			$this->uicols['statustext'][]		= lang('owner');

			$cols_return		= $this->bocommon->cols_return;
			$type_id		= $this->bocommon->type_id;
			$this->cols_extra	= $this->bocommon->cols_extra;


			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

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
				$location = split('-',$location_code);
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
			$this->db2->query("SELECT count(*) FROM fm_gab_location where gab_id='$gab_id' and location_code='$location_code'");

			$this->db2->next_record();

			if ( $this->db2->f(0))
			{
				return True;
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
?>
