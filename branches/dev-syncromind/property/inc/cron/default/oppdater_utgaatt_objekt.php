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
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	include_class('property', 'cron_parent', 'inc/cron/');

	class oppdater_utgaatt_objekt extends property_cron_parent
	{
		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('location');
			$this->function_msg	= lang('Update the not active category for locations');
			$this->soadmin_location	= CreateObject('property.soadmin_location');
		}

		function execute()
		{
			$location_types	= $this->soadmin_location->select_location_type();

			$m= count($location_types);

			$this->db->query("UPDATE fm_location" . $m. " set	status= 2  WHERE category='99'",__LINE__,__FILE__);

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

				$metadata = $this->db->metadata('fm_location'.($type_id-1));

				$this->db->transaction_begin();

				$j=0;
				for ($i=0; $i<count($update); $i++)
				{
					$sql = "SELECT category FROM $parent_table WHERE location_code= '" . $update[$i]['location_code'] ."'";

					$this->db->query($sql,__LINE__,__FILE__);
					$this->db->next_record();

					if($this->db->f('category')!=99)
					{
						$sql = "SELECT * from $parent_table WHERE location_code ='" . $update[$i]['location_code'] . "'";
						$this->db->query($sql,__LINE__,__FILE__);
						$this->db->next_record();

						foreach($metadata as $field => $val)
						{
							$cols[] = $field;
							$vals[] = $this->db->f($field);
						}

						$cols[] = 'exp_date';
						$vals[] = date($this->db->datetime_format(),time());

						$cols	=implode(",", $cols);
						$vals = $this->db->validate_insert($vals);

						$sql = "INSERT INTO fm_location" . ($type_id-1) ."_history ($cols) VALUES ($vals)";
						$this->db->query($sql,__LINE__,__FILE__);
						unset($cols);
						unset($vals);

						$j++;
						$this->db->query("UPDATE fm_location" . ($type_id-1). " set	status= 2, category=99, change_type=2  WHERE location_code= '" . $update[$i]['location_code'] ."'",__LINE__,__FILE__);
						if($type_id == 2)
						{
							$this->db->query("UPDATE fm_location1 set kostra_id = NULL  WHERE location_code= '" . $update[$i]['location_code'] ."'",__LINE__,__FILE__);
						}
					}
				}

				$this->receipt['message'][]=array('msg'=>lang('%1 location %2 has been updated to not active of %3 already not active',$j,$location_types[($type_id-2)]['descr'],count($update)));

				$log_msg .= lang('%1 location %2 has been updated to not active of %3 already not active',$j,$location_types[($type_id-2)]['descr'],count($update));
				unset($outdated);
				unset($update);
				unset($joinmethod);
				unset($paranthesis);
				unset($metadata);
				$this->db->transaction_commit();
			}
		}
	}
