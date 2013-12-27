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

	class synkroniser_med_boei extends property_cron_parent
	{

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('location');
			$this->function_msg	= 'Synkroniser_med_boei';

			$this->bocommon			= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->join				= $this->db->join;
			$this->like				= $this->db->like;
			$this->left_join 		= " LEFT JOIN ";

			if(isset($this->db->adodb) && $this->db->adodb)
			{
				$this->db_boei           	= CreateObject('phpgwapi.db',false,$GLOBALS['external_db']['boei']['db_type']);
				$this->db_boei->Host     	= $GLOBALS['external_db']['boei']['db_host'];
				$this->db_boei->Type     	= $GLOBALS['external_db']['boei']['db_type'];
				$this->db_boei->Database 	= $GLOBALS['external_db']['boei']['db_name'];
				$this->db_boei->User     	= $GLOBALS['external_db']['boei']['db_user'];
				$this->db_boei->Password 	= $GLOBALS['external_db']['boei']['db_pass'];
				$this->db_boei->Halt_On_Error 	= 'yes';
				$this->db_boei->connect();
			}
			else
			{
				$this->db_boei           	= CreateObject('property.db_mssql');
				$this->db_boei->Host     	= $GLOBALS['external_db']['boei']['db_host'];
				$this->db_boei->Type     	= $GLOBALS['external_db']['boei']['db_type'];
				$this->db_boei->Database 	= $GLOBALS['external_db']['boei']['db_name'];
				$this->db_boei->User     	= $GLOBALS['external_db']['boei']['db_user'];
				$this->db_boei->Password 	= $GLOBALS['external_db']['boei']['db_pass'];
				$this->db_boei->Halt_On_Error 	= 'yes';
			}

			$this->db_boei2 = clone($this->db_boei);
		}


		function execute()
		{
			set_time_limit(500);
			$receipt = $this->legg_til_eier_phpgw();
			$this->cron_log($receipt,$cron);
			$receipt = $this->legg_til_gateadresse_phpgw();
			$this->cron_log($receipt,$cron);
			$receipt = $this->legg_til_objekt_phpgw();
			$this->cron_log($receipt,$cron);
			$receipt = $this->legg_til_bygg_phpgw();
			$this->cron_log($receipt,$cron);
			$receipt = $this->legg_til_seksjon_phpgw();
			$this->cron_log($receipt,$cron);
			$receipt = $this->legg_til_leieobjekt_phpgw();
			$this->cron_log($receipt,$cron);
			$receipt = $this->legg_til_leietaker_phpgw();
			$this->cron_log($receipt,$cron);
			$receipt = $this->oppdater_leieobjekt();
			$this->cron_log($receipt,$cron);
			$receipt = $this->oppdater_boa_objekt();
			$this->cron_log($receipt,$cron);
			$receipt = $this->oppdater_boa_bygg();
			$this->cron_log($receipt,$cron);
			$receipt = $this->oppdater_boa_del();
			$this->cron_log($receipt,$cron);
			$receipt = $this->oppdater_oppsagtdato();
			$this->cron_log($receipt,$cron);
			$receipt = $this->slett_feil_telefon();
			$this->cron_log($receipt,$cron);
			$receipt = $this->update_tenant_name();
			$this->cron_log($receipt,$cron);

		}

		function cron_log($receipt='')
		{

			$insert_values= array(
				$this->cron,
				date($this->db->datetime_format()),
				$this->function_name,
				$receipt
				);

			$insert_values	= $this->db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function legg_til_eier_phpgw()
		{
			$sql = " SELECT TOP 100 PERCENT v_Eier.id, v_Eier.category"
				. " FROM  v_Eier";

			$this->db_boei->query($sql,__LINE__,__FILE__);
			while ($this->db_boei->next_record())
			{
				if($this->db_boei->f('category')==0)
				{
					$category = 4;
				}
				else
				{
					$category = $this->db_boei->f('category');
				}
				$owner[]= array (
				 'id' 		=> $this->db_boei->f('id'),
				 'category' 	=> $category,
				 );
			}
	//		$this->db->transaction_begin();
	//		$this->db_boei->transaction_begin();

			for ($i=0; $i<count($owner); $i++)
			{
				$sql2 = "UPDATE fm_owner set category = '{$owner[$i]['category']}' WHERE id = '{$owner[$i]['id']}'";

				$this->db->query($sql2,__LINE__,__FILE__);
				$this->db_boei->query($sql2,__LINE__,__FILE__);
			}

			unset($owner);

			$sql = "SELECT v_Eier.id, v_Eier.org_name,v_Eier.category FROM  fm_owner RIGHT OUTER JOIN "
			        . " v_Eier ON fm_owner.id = v_Eier.id"
					. " WHERE (fm_owner.id IS NULL)";

			$this->db_boei->query($sql,__LINE__,__FILE__);
			while ($this->db_boei->next_record())
			{
				if($this->db_boei->f('category')==0)
				{
					$category = 4;
				}
				else
				{
					$category = $this->db_boei->f('category');
				}
				$owner_utf[]= array (
				 'id' 		=> $this->db_boei->f('id'),
				 'org_name' 	=> $this->db->db_addslashes(utf8_encode($this->db_boei->f('org_name'))),
				 'remark' 	=> $this->db->db_addslashes(utf8_encode($this->db_boei->f('org_name'))),
				 'category' 	=> $category,
				 'entry_date'	=> time(),
				 'owner_id'		=> 6
				 );

				$owner_latin[]= array (
				 'id' 		=> $this->db_boei->f('id'),
				 'org_name' 	=> $this->db->db_addslashes($this->db_boei->f('org_name')),
				 'remark' 	=> $this->db->db_addslashes($this->db_boei->f('org_name')),
				 'category' 	=> $category,
				 'entry_date'	=> time(),
				 'owner_id'		=> 6
				 );

			}

			for ($i=0; $i<count($owner_utf); $i++)
			{
				$sql2_utf = "INSERT INTO fm_owner (id,org_name,remark,category,entry_date,owner_id)"
					. "VALUES (" . $this->db->validate_insert($owner_utf[$i]) . ")";

				$sql2_latin = "INSERT INTO fm_owner (id,org_name,remark,category,entry_date,owner_id)"
					. "VALUES (" . $this->db->validate_insert($owner_latin[$i]) . ")";

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei->query($sql2_latin,__LINE__,__FILE__);

				$owner_msg[]=$owner_utf[$i]['org_name'];
			}

	//		$this->db->transaction_commit();
	//		$this->db_boei->transaction_commit();

			$msg = count($owner) . ' eier er lagt til: ' . @implode(",", $owner_msg);
			$this->receipt['message'][]=array('msg'=> $msg);
			unset ($owner_utf);
			unset ($owner_latin);
			unset ($owner_msg);
			return $msg;

		}



		function legg_til_gateadresse_phpgw()
		{
			//legg til
			$sql = "SELECT v_Gateadresse.gateadresse_id, v_Gateadresse.gatenavn FROM fm_streetaddress RIGHT OUTER JOIN "
			        . " v_Gateadresse ON fm_streetaddress.id = v_Gateadresse.gateadresse_id"
					. " WHERE (fm_streetaddress.id IS NULL)";

			$this->db_boei->query($sql,__LINE__,__FILE__);
			$gate = array();
			while ($this->db_boei->next_record())
			{
				$gate[]= array (
				 'id' 		=> $this->db_boei->f('gateadresse_id'),
				 'descr' 	=> $this->db_boei->f('gatenavn')
				 );

			}
			$this->db->transaction_begin();
			$this->db_boei->transaction_begin();

			for ($i=0; $i<count($gate); $i++)
			{

				$sql = "DELETE from fm_streetaddress WHERE id = " . (int)$gate[$i]['id'];
				$sql2_latin = "INSERT INTO fm_streetaddress (id,descr)"
					. " VALUES (" . $gate[$i]['id'] . ", '". $gate[$i]['descr']. "')";

				$sql2_utf = "INSERT INTO fm_streetaddress (id,descr)"
					. " VALUES (" . $gate[$i]['id'] . ", '". utf8_encode($gate[$i]['descr']). "')";

				$this->db->query($sql,__LINE__,__FILE__);
				$this->db_boei->query($sql,__LINE__,__FILE__);
				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei->query($sql2_latin,__LINE__,__FILE__);
				$gate_msg[]=utf8_encode($gate[$i]['descr']);
			}


			//oppdater gatenavn - om det er endret

			$sql = "SELECT v_Gateadresse.gateadresse_id, v_Gateadresse.gatenavn FROM v_Gateadresse";

			$this->db_boei->query($sql,__LINE__,__FILE__);

			$msg = count($gate) . ' gateadresser er lagt til: ' . @implode(",", $gate_msg);

			$gate = array();
			while ($this->db_boei->next_record())
			{
				$gate[]= array
				(
					'id' 		=> $this->db_boei->f('gateadresse_id'),
					'descr' 	=> $this->db_boei->f('gatenavn')
				);
			}
			
			foreach ($gate as $gate_info)
			{
				$descr = utf8_encode($gate_info['descr']);
				$sql_utf = "UPDATE fm_streetaddress SET descr = '{$descr}' WHERE id = " . (int)$gate_info['id'];
				$this->db->query($sql_utf,__LINE__,__FILE__);
			}

			$this->db->transaction_commit();
			$this->db_boei->transaction_commit();


			$this->receipt['message'][]=array('msg'=> $msg);
			unset ($gate);
			unset ($gate_msg);
			return $msg;

		}

		function legg_til_objekt_phpgw()
		{
			$sql = "SELECT v_Objekt.objekt_id, v_Objekt.navn, v_Objekt.bydel_id, v_Objekt.eier_id,v_Objekt.tjenestested"
					. " FROM fm_location1 RIGHT OUTER JOIN "
			        . " v_Objekt ON fm_location1.loc1 = v_Objekt.objekt_id"
					. " WHERE fm_location1.loc1 IS NULL";

			$this->db_boei->query($sql,__LINE__,__FILE__);
			while ($this->db_boei->next_record())
			{
				$objekt_utf[]= array (
				 'location_code' 	=> $this->db_boei->f('objekt_id'),
				 'loc1' 			=> $this->db_boei->f('objekt_id'),
				 'loc1_name' 		=> utf8_encode($this->db_boei->f('navn')),
				 'part_of_town_id' 	=> $this->db_boei->f('bydel_id'),
				 'owner_id' 		=> $this->db_boei->f('eier_id'),
				 'kostra_id' 		=> $this->db_boei->f('tjenestested'),
				 'category' 		=> 1
				 );
				$objekt_latin[]= array (
				 'location_code' 	=> $this->db_boei->f('objekt_id'),
				 'loc1' 			=> $this->db_boei->f('objekt_id'),
				 'loc1_name' 		=> $this->db_boei->f('navn'),
				 'part_of_town_id' 	=> $this->db_boei->f('bydel_id'),
				 'owner_id' 		=> $this->db_boei->f('eier_id'),
				 'kostra_id' 		=> $this->db_boei->f('tjenestested'),
				 'category' 		=> 1
				 );

			}

	//		$this->db->transaction_begin();
	//		$this->db_boei->transaction_begin();

			for ($i=0; $i<count($objekt_latin); $i++)
			{

				$sql2_utf = "INSERT INTO fm_location1 (location_code, loc1, loc1_name, part_of_town_id, owner_id, kostra_id,category) "
					. "VALUES (" . $this->db->validate_insert($objekt_utf[$i]) . ")";
				$sql2_latin = "INSERT INTO fm_location1 (location_code, loc1, loc1_name, part_of_town_id, owner_id, kostra_id,category) "
					. "VALUES (" . $this->db->validate_insert($objekt_latin[$i]) . ")";

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei->query($sql2_latin,__LINE__,__FILE__);
				$this->db->query("INSERT INTO fm_locations (level, location_code, loc1) VALUES (1, '{$objekt_utf[$i]['location_code']}', '{$objekt_utf[$i]['loc1']}')",__LINE__,__FILE__);

				$obj_msg[]=$objekt_utf[$i]['loc1'];
			}

	//		$this->db->transaction_commit();
	//		$this->db_boei->transaction_commit();

			$msg = count($objekt_latin) . ' Objekt er lagt til: ' . @implode(",", $obj_msg);
			$this->receipt['message'][]=array('msg'=> $msg);
			unset ($objekt_utf);
			unset ($objekt_latin);
			unset ($obj_msg);
			return $msg;
		}

		function legg_til_bygg_phpgw()
		{
			$sql = "SELECT v_Bygg.objekt_id + '-' + v_Bygg.bygg_id AS location_code, v_Bygg.objekt_id, v_Bygg.bygg_id, v_Bygg.byggnavn,v_Bygg.driftstatus"
				. " FROM v_Bygg LEFT OUTER JOIN"
		        . " fm_location2 ON v_Bygg.objekt_id = fm_location2.loc1 AND v_Bygg.bygg_id = fm_location2.loc2"
		        . " WHERE fm_location2.loc1 IS NULL";


			$this->db_boei->query($sql,__LINE__,__FILE__);
			while ($this->db_boei->next_record())
			{
				$bygg_utf[]= array (
				 'location_code' 	=> $this->db_boei->f('location_code'),
				 'loc1' 			=> $this->db_boei->f('objekt_id'),
				 'loc2' 			=> $this->db_boei->f('bygg_id'),
				 'loc2_name' 		=> utf8_encode($this->db_boei->f('byggnavn')),
				 'category' 		=> 98
				 );
				$bygg_latin[]= array (
				 'location_code' 	=> $this->db_boei->f('location_code'),
				 'loc1' 			=> $this->db_boei->f('objekt_id'),
				 'loc2' 			=> $this->db_boei->f('bygg_id'),
				 'loc2_name' 		=> $this->db_boei->f('byggnavn'),
				 'category' 		=> 98
				 );
			}

		//	$this->db->transaction_begin();
		//	$this->db_boei->transaction_begin();

			for ($i=0; $i<count($bygg_latin); $i++)
			{

				$sql2_utf = "INSERT INTO fm_location2 (location_code, loc1, loc2, loc2_name,category) "
					. "VALUES (" . $this->db->validate_insert($bygg_utf[$i]) . ")";
				$sql2_latin = "INSERT INTO fm_location2 (location_code, loc1, loc2, loc2_name,category) "
					. "VALUES (" . $this->db->validate_insert($bygg_latin[$i]) . ")";

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei->query($sql2_latin,__LINE__,__FILE__);
				$this->db->query("INSERT INTO fm_locations (level, location_code, loc1) VALUES (2, '{$bygg_utf[$i]['location_code']}', '{$objekt_utf[$i]['loc1']}')",__LINE__,__FILE__);

				$bygg_msg[]=$bygg_utf[$i]['location_code'];
			}

		//	$this->db->transaction_commit();
		//	$this->db_boei->transaction_commit();

			$msg = count($bygg_latin) . ' Bygg er lagt til: ' . @implode(",", $bygg_msg);
			$this->receipt['message'][]=array('msg'=> $msg);
			unset ($bygg_utf);
			unset ($bygg_latin);
			unset ($bygg_msg);
			return $msg;

		}

		function legg_til_seksjon_phpgw()
		{

			$sql = "SELECT v_Seksjon.objekt_id + '-' + v_Seksjon.bygg_id + '-' + v_Seksjon.seksjons_id AS location_code, v_Seksjon.objekt_id, v_Seksjon.bygg_id,"
				. " v_Seksjon.seksjons_id, v_Seksjon.beskrivelse, v_Seksjon.totalt_fellesareal"
				. " FROM v_Seksjon LEFT OUTER JOIN"
				. " fm_location3 ON v_Seksjon.objekt_id = fm_location3.loc1 AND v_Seksjon.bygg_id = fm_location3.loc2 AND "
				. " v_Seksjon.seksjons_id = fm_location3.loc3"
				. " WHERE fm_location3.loc1 IS NULL";


			$this->db_boei->query($sql,__LINE__,__FILE__);
			while ($this->db_boei->next_record())
			{
				$seksjon_utf[]= array (
				 'location_code' 	=> $this->db_boei->f('location_code'),
				 'loc1' 			=> $this->db_boei->f('objekt_id'),
				 'loc2' 			=> $this->db_boei->f('bygg_id'),
				 'loc3' 			=> $this->db_boei->f('seksjons_id'),
				 'loc3_name' 		=> utf8_encode($this->db_boei->f('beskrivelse')),
				 'fellesareal' 	=> $this->db_boei->f('totalt_fellesareal'),
				 'category' 		=> 98
				 );
				$seksjon_latin[]= array (
				 'location_code' 	=> $this->db_boei->f('location_code'),
				 'loc1' 			=> $this->db_boei->f('objekt_id'),
				 'loc2' 			=> $this->db_boei->f('bygg_id'),
				 'loc3' 			=> $this->db_boei->f('seksjons_id'),
				 'loc3_name' 		=> $this->db_boei->f('beskrivelse'),
				 'fellesareal' 	=> $this->db_boei->f('totalt_fellesareal'),
				 'category' 		=> 98
				 );
			}

		//	$this->db->transaction_begin();
		//	$this->db_boei->transaction_begin();

			for ($i=0; $i<count($seksjon_latin); $i++)
			{

				$sql2_utf = "INSERT INTO fm_location3 (location_code, loc1, loc2, loc3, loc3_name, fellesareal,category) "
					. "VALUES (" . $this->db->validate_insert($seksjon_utf[$i]) . ")";
				$sql2_latin = "INSERT INTO fm_location3 (location_code, loc1, loc2, loc3, loc3_name, fellesareal,category) "
					. "VALUES (" . $this->db->validate_insert($seksjon_latin[$i]) . ")";

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei->query($sql2_latin,__LINE__,__FILE__);
				$this->db->query("INSERT INTO fm_locations (level, location_code, loc1) VALUES (3, '{$seksjon_utf[$i]['location_code']}', '{$objekt_utf[$i]['loc1']}')",__LINE__,__FILE__);

				$seksjon_msg[]=$seksjon_utf[$i]['location_code'];
			}

		//	$this->db->transaction_commit();
		//	$this->db_boei->transaction_commit();

			$msg = count($seksjon_latin) . ' Seksjon er lagt til: ' . @implode(",", $seksjon_msg);
			$this->receipt['message'][]=array('msg'=> $msg);
			unset ($seksjon_utf);
			unset ($seksjon_latin);
			unset ($seksjon_msg);
			return $msg;
		}

		function legg_til_leieobjekt_phpgw()
		{

			$sql = "SELECT v_Leieobjekt.objekt_id + '-' + v_Leieobjekt.bygg_id + '-' + v_Leieobjekt.seksjons_id + '-' + v_Leieobjekt.leie_id AS location_code,"
                  . " v_Leieobjekt.objekt_id, v_Leieobjekt.leie_id, v_Leieobjekt.leieobjekttype_id, v_Leieobjekt.bygg_id, v_Leieobjekt.seksjons_id,"
                  . " v_Leieobjekt.formaal_id, v_Leieobjekt.gateadresse_id, v_Leieobjekt.gatenr, v_Leieobjekt.etasje, v_Leieobjekt.antallrom,"
                  . " v_Leieobjekt.boareal, v_Leieobjekt.livslopsstd, v_Leieobjekt.heis, v_Leieobjekt.driftsstatus_id, v_Leieobjekt.leietaker_id,"
                  . " v_Leieobjekt.beregnet_boa, v_Leieobjekt.flyttenr"
                  . " FROM v_Leieobjekt LEFT OUTER JOIN"
                  . " fm_location4 ON v_Leieobjekt.objekt_id = fm_location4.loc1 AND v_Leieobjekt.leie_id = fm_location4.loc4"
                  . " WHERE fm_location4.loc1 IS NULL";


			$this->db_boei->query($sql,__LINE__,__FILE__);

			while ($this->db_boei->next_record())
			{
				$leieobjekt_utf[]= array (
				 'location_code' 	=> $this->db_boei->f('location_code'),
				 'loc1' 			=> $this->db_boei->f('objekt_id'),
				 'loc4' 			=> $this->db_boei->f('leie_id'),
				 'leieobjekttype_id'=> $this->db_boei->f('leieobjekttype_id'),
				 'loc2' 			=> $this->db_boei->f('bygg_id'),
				 'loc3' 			=> $this->db_boei->f('seksjons_id'),
				 'category' 		=> $this->db_boei->f('formaal_id'),
				 'street_id'	 	=> $this->db_boei->f('gateadresse_id'),
				 'street_number' 	=> utf8_encode($this->db_boei->f('gatenr')),
				 'etasje' 			=> utf8_encode($this->db_boei->f('etasje')),
				 'antallrom'	 	=> $this->db_boei->f('antallrom'),
				 'boareal' 			=> $this->db_boei->f('boareal'),
				 'livslopsstd' 		=> $this->db_boei->f('livslopsstd'),
				 'heis' 			=> $this->db_boei->f('heis'),
				 'driftsstatus_id' 	=> $this->db_boei->f('driftsstatus_id'),
				 'tenant_id'	 	=> $this->db_boei->f('leietaker_id'),
				 'beregnet_boa' 	=> $this->db_boei->f('beregnet_boa'),
				 'flyttenr' 		=> $this->db_boei->f('flyttenr')
				 );
				$leieobjekt_latin[]= array (
				 'location_code' 	=> $this->db_boei->f('location_code'),
				 'loc1' 			=> $this->db_boei->f('objekt_id'),
				 'loc4' 			=> $this->db_boei->f('leie_id'),
				 'leieobjekttype_id'=> $this->db_boei->f('leieobjekttype_id'),
				 'loc2' 			=> $this->db_boei->f('bygg_id'),
				 'loc3' 			=> $this->db_boei->f('seksjons_id'),
				 'category' 		=> $this->db_boei->f('formaal_id'),
				 'street_id'	 	=> $this->db_boei->f('gateadresse_id'),
				 'street_number' 	=> $this->db_boei->f('gatenr'),
				 'etasje' 			=> $this->db_boei->f('etasje'),
				 'antallrom'	 	=> $this->db_boei->f('antallrom'),
				 'boareal' 			=> $this->db_boei->f('boareal'),
				 'livslopsstd' 		=> $this->db_boei->f('livslopsstd'),
				 'heis' 			=> $this->db_boei->f('heis'),
				 'driftsstatus_id' 	=> $this->db_boei->f('driftsstatus_id'),
				 'tenant_id'	 	=> $this->db_boei->f('leietaker_id'),
				 'beregnet_boa' 	=> $this->db_boei->f('beregnet_boa'),
				 'flyttenr' 		=> $this->db_boei->f('flyttenr')
				 );

			}

		//	$this->db->transaction_begin();
		//	$this->db_boei->transaction_begin();

			for ($i=0; $i<count($leieobjekt_latin); $i++)
			{

				$sql2_utf = "INSERT INTO fm_location4 (location_code, loc1, loc4, leieobjekttype_id, loc2, loc3, category, street_id, street_number, etasje, antallrom, boareal, livslopsstd, heis, driftsstatus_id,
                      tenant_id, beregnet_boa, flyttenr)"
					. "VALUES (" . $this->db->validate_insert($leieobjekt_utf[$i]) . ")";
				$sql2_latin = "INSERT INTO fm_location4 (location_code, loc1, loc4, leieobjekttype_id, loc2, loc3, category, street_id, street_number, etasje, antallrom, boareal, livslopsstd, heis, driftsstatus_id,
                      tenant_id, beregnet_boa, flyttenr)"
					. "VALUES (" . $this->db->validate_insert($leieobjekt_latin[$i]) . ")";

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei->query($sql2_latin,__LINE__,__FILE__);
				$this->db->query("INSERT INTO fm_locations (level, location_code, loc1) VALUES (4, '{$leieobjekt_utf[$i]['location_code']}', '{$objekt_utf[$i]['loc1']}')",__LINE__,__FILE__);

				$leieobjekt_msg[]=$leieobjekt_utf[$i]['location_code'];
			}

		//	$this->db->transaction_commit();
		//	$this->db_boei->transaction_commit();

			$msg = count($leieobjekt_latin) . ' Leieobjekt er lagt til: ' . @implode(",", $leieobjekt_msg);
			$this->receipt['message'][]=array('msg'=> $msg);
			unset ($leieobjekt_latin);
			unset ($leieobjekt_utf);
			unset ($leieobjekt_msg);
			return $msg;
		}

		function legg_til_leietaker_phpgw()
		{
			$sql = " SELECT v_Leietaker.leietaker_id, v_Leietaker.fornavn, v_Leietaker.etternavn, v_Leietaker.kjonn_juridisk,"
				. " v_Leietaker.namssakstatusokonomi_id, v_Leietaker.namssakstatusdrift_id"
				. " FROM fm_tenant RIGHT OUTER JOIN"
				. " v_Leietaker ON fm_tenant.id = v_Leietaker.leietaker_id"
				. " WHERE fm_tenant.id IS NULL";

			$this->db_boei->query($sql,__LINE__,__FILE__);

			while ($this->db_boei->next_record())
			{
				$leietaker_utf[]= array (
				 'id' 				=> $this->db_boei->f('leietaker_id'),
				 'first_name'		=> $this->db->db_addslashes(utf8_encode($this->db_boei->f('fornavn'))),
				 'last_name' 		=> $this->db->db_addslashes(utf8_encode($this->db_boei->f('etternavn'))),
				 'category'			=> $this->db_boei->f('kjonn_juridisk') + 1,
				 'status_eco'		=> $this->db_boei->f('namssakstatusokonomi_id'),
				 'status_drift' 	=> $this->db_boei->f('namssakstatusdrift_id'),
				 'entry_date'		=> time(),
				 'owner_id'			=> 6
				 );
				$leietaker_latin[]= array (
				 'id' 				=> $this->db_boei->f('leietaker_id'),
				 'first_name'		=> $this->db->db_addslashes($this->db_boei->f('fornavn')),
				 'last_name' 		=> $this->db->db_addslashes($this->db_boei->f('etternavn')),
				 'category'			=> $this->db_boei->f('kjonn_juridisk') + 1,
				 'status_eco'		=> $this->db_boei->f('namssakstatusokonomi_id'),
				 'status_drift' 	=> $this->db_boei->f('namssakstatusdrift_id'),
				 'entry_date'		=> time(),
				 'owner_id'			=> 6
				 );
			}

		//	$this->db->transaction_begin();
		//	$this->db_boei->transaction_begin();

			for ($i=0; $i<count($leietaker_latin); $i++)
			{
				$this->db->query("DELETE FROM fm_tenant WHERE id=" . (int)$leietaker_latin[$i]['id'],__LINE__,__FILE__);
				$this->db_boei->query("DELETE FROM fm_tenant WHERE id=" . (int)$leietaker_latin[$i]['id'],__LINE__,__FILE__);

				$sql2_utf = "INSERT INTO fm_tenant (id, first_name, last_name, category, status_eco, status_drift,entry_date,owner_id)"
					. "VALUES (" . $this->db->validate_insert($leietaker_utf[$i]) . ")";
				$sql2_latin = "INSERT INTO fm_tenant (id, first_name, last_name, category, status_eco, status_drift,entry_date,owner_id)"
					. "VALUES (" . $this->db->validate_insert($leietaker_latin[$i]) . ")";

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei->query($sql2_latin,__LINE__,__FILE__);

				$leietaker_msg[]= '[' .$leietaker_utf[$i]['last_name'] . ', ' . $leietaker_utf[$i]['first_name'] . ']';
			}

		//	$this->db->transaction_commit();
		//	$this->db_boei->transaction_commit();

			$msg = count($leietaker_latin) . ' Leietaker er lagt til: ' . @implode(",", $leietaker_msg);
			$this->receipt['message'][]=array('msg'=> $msg);
			unset ($leietaker);
			unset ($leietaker_msg);
			return $msg;

		}

		function update_tenant_name()
		{
			$sql = " SELECT leietaker_id, fornavn, etternavn FROM v_Leietaker";
			$this->db_boei->query($sql,__LINE__,__FILE__);

			$i=0;
			while ($this->db_boei->next_record())
			{
				$sql2_utf = " UPDATE  fm_tenant SET "
				. " first_name		= '" . $this->db->db_addslashes(utf8_encode($this->db_boei->f('fornavn'))) . "',"
				. " last_name 		= '" . $this->db->db_addslashes(utf8_encode($this->db_boei->f('etternavn'))) ."'"
				. " WHERE  id = " . (int)$this->db_boei->f('leietaker_id');

				$sql2_latin = " UPDATE  fm_tenant SET "
				. " first_name		= '" . $this->db->db_addslashes($this->db_boei->f('fornavn')) . "',"
				. " last_name 		= '" . $this->db->db_addslashes($this->db_boei->f('etternavn')) ."'"
				. " WHERE  id = " . (int)$this->db_boei->f('leietaker_id');

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei2->query($sql2_latin,__LINE__,__FILE__);
				$i++;
			}

			$msg = $i . ' Leietakere er oppdatert';
			$this->receipt['message'][]=array('msg'=> $msg);
			return $msg;
		}


		function oppdater_leieobjekt()
		{
			$sql = " SELECT TOP 100 PERCENT v_Leieobjekt.objekt_id,v_Leieobjekt.leie_id,v_Leieobjekt.leietaker_id, boareal, formaal_id, gateadresse_id, gatenr, etasje,driftsstatus_id, v_Leieobjekt.flyttenr, innflyttetdato"
				. " FROM  v_Leieobjekt LEFT JOIN v_reskontro ON v_Leieobjekt.objekt_id=v_reskontro.objekt_id AND v_Leieobjekt.leie_id=v_reskontro.leie_id"
				. " AND v_Leieobjekt.flyttenr=v_reskontro.flyttenr AND v_Leieobjekt.leietaker_id=v_reskontro.leietaker_id";

			$this->db_boei->query($sql,__LINE__,__FILE__);

		//	$this->db->transaction_begin();
		//	$this->db_boei2->transaction_begin();


			$i=0;
			while ($this->db_boei->next_record())
			{
				$sql2_utf = " UPDATE  fm_location4 SET "
				. " tenant_id = '" . $this->db_boei->f('leietaker_id') . "',"
				. " category = '" . $this->db_boei->f('formaal_id') . "',"
				. " etasje = '" . utf8_encode($this->db_boei->f('etasje')) . "',"
				. " street_id = '" . $this->db_boei->f('gateadresse_id') . "',"
				. " street_number = '" . utf8_encode($this->db_boei->f('gatenr')) . "',"
				. " driftsstatus_id = '" . $this->db_boei->f('driftsstatus_id') . "',"
				. " boareal = '" . $this->db_boei->f('boareal') . "',"
				. " flyttenr = '" . $this->db_boei->f('flyttenr') . "',"
				. " innflyttetdato = '" . date($this->db->date_format(),strtotime($this->db_boei->f('innflyttetdato'))) . "'"
				. " WHERE  loc1 = '" . $this->db_boei->f('objekt_id') . "'  AND  loc4= '" . $this->db_boei->f('leie_id') . "'";
				$sql2_latin = " UPDATE  fm_location4 SET "
				. " tenant_id = '" . $this->db_boei->f('leietaker_id') . "',"
				. " category = '" . $this->db_boei->f('formaal_id') . "',"
				. " etasje = '" . $this->db_boei->f('etasje') . "',"
				. " street_id = '" . $this->db_boei->f('gateadresse_id') . "',"
				. " street_number = '" . $this->db_boei->f('gatenr') . "',"
				. " driftsstatus_id = '" . $this->db_boei->f('driftsstatus_id') . "',"
				. " boareal = '" . $this->db_boei->f('boareal') . "',"
				. " flyttenr = '" . $this->db_boei->f('flyttenr') . "',"
				. " innflyttetdato = '" . date("M d Y",strtotime($this->db_boei->f('innflyttetdato'))) . "'"
				. " WHERE  loc1 = '" . $this->db_boei->f('objekt_id') . "'  AND  loc4= '" . $this->db_boei->f('leie_id') . "'";

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei2->query($sql2_latin,__LINE__,__FILE__);
				$i++;
			}

		//	$this->db->transaction_commit();
		//	$this->db_boei2->transaction_commit();

			$msg = $i . ' Leieobjekt er oppdatert';
			$this->receipt['message'][]=array('msg'=> $msg);
			return $msg;

		}

		function oppdater_boa_objekt()
		{
			$sql = " SELECT TOP 100 PERCENT v_Objekt.objekt_id,bydel_id,tjenestested,navn,v_Objekt.eier_id FROM v_Objekt";
			$this->db_boei->query($sql,__LINE__,__FILE__);

			while ($this->db_boei->next_record())
			{
				$sql2_utf = " UPDATE fm_location1 SET "
				. " loc1_name = '" . utf8_encode($this->db_boei->f('navn')) . "',"
				. " part_of_town_id = " . (int)$this->db_boei->f('bydel_id') . ","
				. " owner_id = " . (int)$this->db_boei->f('eier_id') . ","
				. " kostra_id = " . (int)$this->db_boei->f('tjenestested')
				. " WHERE  loc1 = '" . $this->db_boei->f('objekt_id') . "'";
				$sql2_latin = " UPDATE fm_location1 SET "
				. " loc1_name = '" . $this->db_boei->f('navn') . "',"
				. " part_of_town_id = " . (int)$this->db_boei->f('bydel_id') . ","
				. " owner_id = " . (int)$this->db_boei->f('eier_id') . ","
				. " kostra_id = " . (int)$this->db_boei->f('tjenestested')
				. " WHERE  loc1 = '" . $this->db_boei->f('objekt_id') . "'";

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei2->query($sql2_latin,__LINE__,__FILE__);
			}

			$sql = " SELECT TOP 100 PERCENT sum(v_Leieobjekt.boareal) as sum_boa, count(leie_id) as ant_leieobjekt,"
					. " v_Objekt.objekt_id FROM  v_Objekt {$this->join} v_Leieobjekt ON v_Objekt.objekt_id = v_Leieobjekt.objekt_id"
					. " WHERE v_Leieobjekt.formaal_id NOT IN (99)"
					. " GROUP BY v_Objekt.objekt_id";

			$this->db_boei->query($sql,__LINE__,__FILE__);

		//	$this->db->transaction_begin();
		//	$this->db_boei2->transaction_begin();

			$i=0;
			while ($this->db_boei->next_record())
			{
				$sql2 = " UPDATE fm_location1 SET "
				. " sum_boa = '" . $this->db_boei->f('sum_boa') . "',"
				. " ant_leieobjekt = " . (int)$this->db_boei->f('ant_leieobjekt')
				. " WHERE  loc1 = '" . $this->db_boei->f('objekt_id') . "'";
				$this->db->query($sql2,__LINE__,__FILE__);
				$this->db_boei2->query($sql2,__LINE__,__FILE__);
				$i++;
			}
		//	$this->db->transaction_commit();
		//	$this->db_boei2->transaction_commit();

			$msg = $i . ' Objekt er oppdatert';
			$this->receipt['message'][]=array('msg'=> $msg);
			return $msg;
		}

		function oppdater_boa_bygg()
		{
			$sql = " SELECT TOP 100 PERCENT sum(v_Leieobjekt.boareal) as sum_boa, count(leie_id) as ant_leieobjekt,"
					. " v_Bygg.objekt_id,v_Bygg.bygg_id , byggnavn  FROM  v_Bygg $this->join v_Leieobjekt "
					. " ON v_Bygg.objekt_id = v_Leieobjekt.objekt_id AND v_Bygg.bygg_id = v_Leieobjekt.bygg_id"
					. " WHERE v_Leieobjekt.formaal_id NOT IN (99)"
					. " GROUP BY v_Bygg.objekt_id,v_Bygg.bygg_id ,byggnavn";

			$this->db_boei->query($sql,__LINE__,__FILE__);

		//	$this->db->transaction_begin();
		//	$this->db_boei2->transaction_begin();

			$i=0;
			while ($this->db_boei->next_record())
			{
				$sql2_utf = " UPDATE fm_location2 SET "
				. " loc2_name = '" . utf8_encode($this->db_boei->f('byggnavn')) . "',"
				. " sum_boa = '" . $this->db_boei->f('sum_boa') . "',"
				. " ant_leieobjekt = '" . $this->db_boei->f('ant_leieobjekt') . "'"
				. " WHERE  loc1 = '" . $this->db_boei->f('objekt_id') . "'  AND  loc2= '" . $this->db_boei->f('bygg_id') . "'";
				$sql2_latin = " UPDATE fm_location2 SET "
				. " loc2_name = '" . $this->db_boei->f('byggnavn') . "',"
				. " sum_boa = '" . $this->db_boei->f('sum_boa') . "',"
				. " ant_leieobjekt = '" . $this->db_boei->f('ant_leieobjekt') . "'"
				. " WHERE  loc1 = '" . $this->db_boei->f('objekt_id') . "'  AND  loc2= '" . $this->db_boei->f('bygg_id') . "'";

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei2->query($sql2_latin,__LINE__,__FILE__);
				$i++;
			}
		//	$this->db->transaction_commit();
		//	$this->db_boei2->transaction_commit();

			$msg = $i . ' Bygg er oppdatert';
			$this->receipt['message'][]=array('msg'=> $msg);
			return $msg;
		}

		function oppdater_boa_del()
		{

			$sql = " SELECT TOP 100 PERCENT sum(v_Leieobjekt.boareal) as sum_boa, count(leie_id) as ant_leieobjekt,"
					. " v_Seksjon.objekt_id,v_Seksjon.bygg_id,v_Seksjon.seksjons_id , beskrivelse   FROM  v_Seksjon $this->join v_Leieobjekt "
					. " ON v_Seksjon.objekt_id = v_Leieobjekt.objekt_id"
					. " AND v_Seksjon.bygg_id = v_Leieobjekt.bygg_id"
					. " AND v_Seksjon.seksjons_id = v_Leieobjekt.seksjons_id"
					. " WHERE v_Leieobjekt.formaal_id NOT IN (99)"
					. " GROUP BY v_Seksjon.objekt_id,v_Seksjon.bygg_id,v_Seksjon.seksjons_id,beskrivelse";

			$this->db_boei->query($sql,__LINE__,__FILE__);

			$i=0;

		//	$this->db->transaction_begin();
		//	$this->db_boei2->transaction_begin();

			while ($this->db_boei->next_record())
			{
				$sql2_utf = " UPDATE fm_location3 SET "
				. " loc3_name = '" . utf8_encode($this->db_boei->f('beskrivelse')) . "',"
				. " sum_boa = '" . $this->db_boei->f('sum_boa') . "',"
				. " ant_leieobjekt = '" . $this->db_boei->f('ant_leieobjekt') . "'"
				. " WHERE  loc1 = '" . $this->db_boei->f('objekt_id') . "'  AND  loc2= '" . $this->db_boei->f('bygg_id') . "'  AND  loc3= '" . $this->db_boei->f('seksjons_id') . "'";
				$sql2_latin = " UPDATE fm_location3 SET "
				. " loc3_name = '" . $this->db_boei->f('beskrivelse') . "',"
				. " sum_boa = '" . $this->db_boei->f('sum_boa') . "',"
				. " ant_leieobjekt = '" . $this->db_boei->f('ant_leieobjekt') . "'"
				. " WHERE  loc1 = '" . $this->db_boei->f('objekt_id') . "'  AND  loc2= '" . $this->db_boei->f('bygg_id') . "'  AND  loc3= '" . $this->db_boei->f('seksjons_id') . "'";

				$this->db->query($sql2_utf,__LINE__,__FILE__);
				$this->db_boei2->query($sql2_latin,__LINE__,__FILE__);
				$i++;
			}
		//	$this->db->transaction_commit();
		//	$this->db_boei2->transaction_commit();

			$msg = $i . ' Seksjoner er oppdatert';
			$this->receipt['message'][]=array('msg'=> $msg);
			return $msg;
		}

		function oppdater_oppsagtdato()
		{

			$sql = "SELECT TOP 100 PERCENT fm_tenant.id"
					. " FROM  fm_tenant LEFT OUTER JOIN"
                    . " v_Leietaker ON fm_tenant.id = v_Leietaker.leietaker_id AND "
                    . " fm_tenant.oppsagtdato = v_Leietaker.oppsagtdato"
					. " WHERE (v_Leietaker.leietaker_id IS NULL)";

			$this->db_boei->query($sql,__LINE__,__FILE__);

	//		$this->db->transaction_begin();
	//		$this->db_boei->transaction_begin();

			while ($this->db_boei->next_record())
			{
				$leietaker[]= $this->db_boei->f('id');
			}

			for ($i=0; $i<count($leietaker); $i++)
			{
				$sql = "SELECT oppsagtdato"
					. " FROM  v_Leietaker"
					. " WHERE (v_Leietaker.leietaker_id = '" . $leietaker[$i] . "')";

				$this->db_boei->query($sql,__LINE__,__FILE__);

				$this->db_boei->next_record();
				$leietaker_oppdatert[]= array (
				 'id' 				=> $leietaker[$i],
				 'oppsagtdato'		=> $this->db_boei->f('oppsagtdato')
				 );

			}

			for ($i=0; $i<count($leietaker_oppdatert); $i++)
			{
				$sql = " UPDATE fm_tenant SET "
				. " oppsagtdato = '" . $leietaker_oppdatert[$i]['oppsagtdato'] . "'"
				. " WHERE  id = '" . $leietaker_oppdatert[$i]['id'] . "'";

				$this->db->query($sql,__LINE__,__FILE__);
				$this->db_boei->query($sql,__LINE__,__FILE__);
			}

		//	$this->db->transaction_commit();
		//	$this->db_boei->transaction_commit();

			$msg = $i . ' oppsagtdato er oppdatert';
			$this->receipt['message'][]=array('msg'=> $msg);
			return $msg;

		}


		function slett_feil_telefon()
		{
			$sql = "SELECT count(contact_phone) as ant_tlf from fm_tenant WHERE id > 99999 OR id = 0";

			$this->db->query($sql,__LINE__,__FILE__);

			$this->db->next_record();

			$ant_tlf = $this->db->f('ant_tlf');

			$sql = "UPDATE fm_tenant SET contact_phone = NULL WHERE id > 99999 OR id = 0";

			$this->db->query($sql,__LINE__,__FILE__);

			$msg = $ant_tlf . ' Telefon nr er slettet';
			$this->receipt['message'][]=array('msg'=> $msg);
			return $msg;
		}
	}
