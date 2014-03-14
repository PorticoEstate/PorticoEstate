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

	class oppdater_antall_leieobjekt extends property_cron_parent
	{
		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('location');
			$this->function_msg	= 'Oppdater antall leieobjekter for tv-anlegg';
		}

		function execute()
		{
			$this->db->transaction_begin();

			$sql = "SELECT kunde_nr_lev, fm_entity_1_3.loc1, fm_entity_1_3.loc2, Count(fm_location4.location_code) AS antall_leieobjekt, fm_entity_1_3.location_code
					FROM fm_entity_1_3 INNER JOIN fm_location4 ON (fm_entity_1_3.loc1 = fm_location4.loc1) AND (fm_entity_1_3.loc2 = fm_location4.loc2)
					WHERE fm_location4.category IN (1,2,3,4,6,10,14,15,17,22,23,24,25)
					GROUP BY kunde_nr_lev, fm_entity_1_3.loc1, fm_entity_1_3.loc2, fm_entity_1_3.location_code";

			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				if($this->db->f('kunde_nr_lev'))
				{
					$update[]= array(
					'kunde_nr_lev'	=>$this->db->f('kunde_nr_lev'),
					'antall_leieobjekt'	=>$this->db->f('antall_leieobjekt'),
					);
				}
			}

//_debug_array($update);

			for ($i=0; $i<count($update); $i++)
			{
				$this->db->query("UPDATE fm_entity_1_3 set ant_leil_pt =" . $update[$i]['antall_leieobjekt'] . " WHERE kunde_nr_lev= '" . $update[$i]['kunde_nr_lev'] . "'" ,__LINE__,__FILE__);
			}

			$this->receipt['message'][]=array('msg'=>'antall leieobjekter er oppdatert for tv-anlegg');

			unset($update);

			$this->db->transaction_commit();

		}
	}

