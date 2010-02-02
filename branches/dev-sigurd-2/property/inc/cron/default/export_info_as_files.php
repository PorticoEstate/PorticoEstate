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

	class export_info_as_files
	{
		var	$function_name = 'export_info_as_files';

		function export_info_as_files()
		{
			$this->bocommon			= CreateObject('property.bocommon');
			$this->db 				= & $GLOBALS['phpgw']->db;
			$this->db2				= clone($this->db);
			$this->soadmin_location	= CreateObject('property.soadmin_location');

			$this->join				= $this->db->join;
			$this->like				= $this->db->like;
			$this->left_join 		= " LEFT JOIN ";
			$this->saveto			= '/mnt/filer2/VaktPC_filer';
		//	$this->saveto			= '/tmp';
			$this->export_method = 'csv';
		//	$this->export_method = 'excel';
 		//	$this->export_method = 'xml';
 			$this->dateformat = 'd/m/Y';

		}

		function pre_run($data='')
		{
			if($data['enabled']==1)
			{
				$confirm	= true;
				$cron		= true;
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= phpgw::get_var('execute', 'bool', 'GET');
			}

			if ($confirm)
			{
				$this->execute($cron);
			}
			else
			{
				$this->confirm($execute=false);
			}
		}


		function confirm($execute='')
		{
			$link_data = array
			(
				'menuaction' => 'property.custom_functions.index',
				'function'	=>$this->function_name,
				'execute'	=> $execute,
			);


			if(!$execute)
			{
				$lang_confirm_msg 	= lang('do you want to perform this action');
			}

			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));


			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> lang('Export info as files'),
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('location');
			$function_msg	= lang('Export info as files');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{

			$this->export_heiser();
			$this->export_brannalarm();
			$this->export_ventilasjon();
			$this->export_kabeltv();
			$this->export_sprinkler();
			$this->export_smokevent();
			$this->export_tenants();
			$this->export_keyes();

			if(!$cron)
			{
				$this->confirm($execute=false);
			}

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$insert_values= array(
				$cron,
				date($this->bocommon->datetimeformat),
				$this->function_name,
				implode(',',(array_keys($msgbox_data)))
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function export_heiser()
		{
			$descr = array('ID','Lokalisering','Adresse','status','Garanti faser','servicefrekvens','service firma','tlf service',
				'tlf service','Alarmtype','merknad_telefon','nhk_nummer','fabrikasjons_nr','heis_type',
				'Alarm til', 'Service kontrakt','Merknad');
			$name = array('num', 'location_code', 'address', 'status', 'garanti_faser', 'servicefrekvens',
			'service_firma', 'tlf_service', 'tlf_heishus', 'alarmtype', 'merknad_telefon', 'nhk_nummer',
			'fabrikasjons_nr', 'heis_type', 'alarm_til', 'service_kontrakt', 'merknad');

			$filename= 'HEISER';
			$sql = "SELECT * from fm_entity_1_1";

			$this->export_to_file($name,$descr,$filename, $sql);
		}

		function export_to_file($name,$descr,$filename, $sql)
		{
			switch ($this->export_method)
			{
				case 'excel':
					$this->export_as_excel($name,$descr,$filename, $sql);
					break;
				case 'csv':
					$this->export_as_csv($name,$descr,$filename, $sql);
					break;
				case 'xml':
					$this->export_as_xml($name,$descr,$filename, $sql);
					break;
			}
		}

		function export_brannalarm()
		{
			$descr = array('ID', 'Objekt', 'Bygg', 'Inngang', 'addresse', 'adresserbart', 'service_firma', 'Leverandør kontakt', 'vakttelefon', 'merknad_bbb', 'type anlegg', 'sprinkelanlegg', 'securitnet nr', 'tidsforsinkelse', 'Telenor tlf 1', 'Telenor tlf 12', 'BBB vakt 24 T');
			$name = array('num', 'loc1', 'loc2', 'loc3', 'address', 'adreserbart', 'service_firma', 'lev_kontakt', 'vakttelefon', 'merknad_bbb', 'type_anlegg', 'sprinkel', 'securitnet_nr', 'tidsforsinkelse', 'telenor_1', 'telenor_2', 'vakt_24_bbb');

			$filename= 'BRANNALARMER';
			$sql = "SELECT * from fm_entity_1_2";

			$this->export_to_file($name,$descr,$filename, $sql);
		}

		function export_ventilasjon()
		{
			$descr = array('ID', 'Objekt', 'Bygg', 'addresse', 'status', 'Anleggs type', 'Aggregat type', 'Filtertype', 'Antall filte', 'Plassering', 'Merknad');
			$name = array('num', 'loc1', 'loc2', 'address', 'status', 'v_type', 'aggr_type', 'filtertype', 'f_antall', 'plassering', 'merknad');

			$filename= 'VENTILASJON';
			$sql = "SELECT * from fm_entity_1_7";

			$this->export_to_file($name,$descr,$filename, $sql);
		}

		function export_kabeltv()
		{
			$descr = array('ID', 'Objekt', 'Bygg', 'addresse', 'antall leiligh. PT', 'leverandør', 'kontakt person', 'kotakt tlf (bbb)', 'kotakt tlf (beboer)', 'Kunde nr leverandør', 'Kunde nr PT', 'Nett nr Post/tele', 'merknad');
			$name = array('num', 'loc1', 'loc2', 'address', 'ant_leil_pt', 'leverandor', 'kontakt_person', 'k_tlf_bbb', 'k_tlf_beboer', 'kunde_nr_lev', 'kunde_nr_pt', 'nett_nr_pt', 'merknad');

			$filename= 'KABEL-TV';
			$sql = "SELECT * from fm_entity_1_3";

			$this->export_to_file($name,$descr,$filename, $sql);
		}

		function export_sprinkler()
		{
			$descr = array('ID', 'Objekt', 'Bygg', 'Inngang', 'addresse', 'status', 'eier', 'type anlegg', 'Drift start', 'Leverandør', 'Telefon nr', 'Kontakt person', 'service avtale', 'plassering av sentral', 'merknad');
			$name = array('num', 'loc1', 'loc2', 'loc3', 'address', 'status', 'eier', 'type', 'dr_start', 'org_name', 'lev_tlf', 'kont_person', 'service_avtale', 'plassering', 'merknad');

			$filename= 'SPRINKLER';
			$sql = "SELECT fm_entity_1_9.* , fm_vendor.org_name from fm_entity_1_9 left join fm_vendor on fm_entity_1_9.leverandor = fm_vendor.id ";

			$this->export_to_file($name,$descr,$filename, $sql);
		}


		function export_smokevent()
		{
			$descr = array('ID', 'Objekt', 'Bygg', 'addresse', 'status', 'beskrivelse', 'merknad');
			$name = array('num', 'loc1', 'loc2', 'address', 'status', 'beskrivelse', 'merknad');

			$filename= 'ROYKVENTILASJON';
			$sql = "SELECT * from fm_entity_1_8";

			$this->export_to_file($name,$descr,$filename, $sql);
		}

		function export_tenants()
		{
			$descr = array('Objekt', 'Bygg','Inngang','Leieobjekt','Flyttenr','Reskontronr', 'Objekt Navn', 'Etternavn', 'Fornavn', 'Kontakt tlf','Gatenavn','GateNr','Etasje','Antall Rom','Boareal','Ferdigdato','klargjøringsstatus');
			$name = array('loc1', 'loc2', 'loc3','loc4','flyttenr','reskontronr','loc1_name', 'last_name', 'first_name', 'contact_phone','street_name','street_number','etasje','antallrom','boareal','finnish_date','klargj_st');

			$filename= 'LEIETAKER';
			$sql = "SELECT fm_location4.location_code,fm_location4.loc1,fm_location4.loc2,fm_location4.loc3,fm_location4.loc4,fm_location4.flyttenr,"
			. " (fm_location4.loc1 || '.' || fm_location4.loc4 || '.' || fm_location4.flyttenr)as reskontronr, fm_location1.loc1_name,fm_tenant.id as tenant_id,fm_tenant.last_name,fm_tenant.first_name,fm_tenant.contact_phone,fm_streetaddress.descr as street_name,street_number,fm_location4.street_id,fm_location4.etasje,fm_location4.antallrom,fm_location4.boareal,"
			 . "to_char(fm_location4.finnish_date, 'DD/MM/YYYY') as finnish_date ,fm_location4.klargj_st FROM ((((((( fm_location4 JOIN fm_location3 ON (fm_location4.loc3 = fm_location3.loc3) AND (fm_location4.loc2 = fm_location3.loc2) AND (fm_location4.loc1 = fm_location3.loc1)) JOIN fm_location2 ON (fm_location3.loc2 = fm_location2.loc2) AND (fm_location3.loc1 = fm_location2.loc1)) JOIN fm_location1 ON (fm_location2.loc1 = fm_location1.loc1)) JOIN fm_owner ON ( fm_location1.owner_id=fm_owner.id)) JOIN fm_part_of_town ON ( fm_location1.part_of_town_id=fm_part_of_town.part_of_town_id)) JOIN fm_streetaddress ON ( fm_location4.street_id=fm_streetaddress.id)) JOIN fm_tenant ON ( fm_location4.tenant_id=fm_tenant.id)) WHERE (fm_location4.category !=99 OR fm_location4.category IS NULL) AND driftsstatus_id > 0 ORDER BY reskontronr ASC";

			$this->export_to_file($name,$descr,$filename, $sql);
		}

		function export_keyes()
		{
			$descr = array('ID', 'Objekt', 'Bygg', 'Addresse','System Nr');
			$name = array('num', 'loc1', 'loc2', 'address', 'system_nr');

			$filename= 'NOEKLER';
			$sql = "SELECT * from fm_entity_1_6";

			$this->export_to_file($name,$descr,$filename, $sql);
		}


		function export_as_excel($name,$descr,$filename, $sql)
		{
			$workbook	= CreateObject('phpgwapi.excel', "{$this->saveto}/{$filename}.xls");

			$worksheet1 =& $workbook->add_worksheet('First One');

			$this->db->query($sql,__LINE__,__FILE__);

			for ($i=0;$i<count($descr);$i++)
			{
				$worksheet1->write_string(0, $i, $this->bocommon->utf2ascii($descr[$i]));
			}

			$worksheet1->write_string(0, $i, lang('date'));

			$line =1;
			while ($this->db->next_record())
			{
				for ($i=0;$i<count($name);$i++)
				{
					$worksheet1->write($line,$i, $this->bocommon->utf2ascii($this->db->f($name[$i])));
				}
				$worksheet1->write($line,$i, $GLOBALS['phpgw']->common->show_date(time(),$this->dateformat));
				$line++;
			}


			$workbook->close();
		}

		function export_as_csv($name,$descr,$filename, $sql)
		{

			$fp = fopen("{$this->saveto}/{$filename}.txt",'wb');

		    $descr[] = 'Dato';
		    fputcsv($fp, $descr, ';');

			$this->db->query($sql,__LINE__,__FILE__);

			$j=0;
			while ($this->db->next_record())
			{
				for ($i=0;$i<count($name);$i++)
				{
					$content[$j][] = str_replace(array("\r","\n")," ",$this->bocommon->utf2ascii($this->db->f($name[$i])));
				}
				$content[$j][] = $GLOBALS['phpgw']->common->show_date(time(),$this->dateformat);
				$j++;
			}

			foreach ($content as $line)
			{
			    fputcsv($fp, $line, ';');
			}

			fclose($fp);
		}

		function export_as_xml($name,$descr,$filename, $sql)
		{
		    $descr[] = 'Dato';
			$this->db->query($sql,__LINE__,__FILE__);

			$j=0;
			while ($this->db->next_record())
			{
				for ($i=0;$i<count($name);$i++)
				{
					$xmlvars[$j][$descr[$i]] = str_replace(array("\r","\n")," ",$this->bocommon->utf2ascii($this->db->f($name[$i])));
				}
				$xmlvars[$j][$descr[$i]] = $GLOBALS['phpgw']->common->show_date(time(),$this->dateformat);
				$j++;
			}

			$xmltool = CreateObject('phpgwapi.xmltool');
			while(list($key,$value) = each($xmlvars))
			{
				$xmldata[$key] = $value;
			}

			$xml = var2xml('PHPGW',$xmldata);
			$fp = fopen("{$this->saveto}/{$filename}.xml",'wb');
			fwrite($fp,$xml);
			fclose($fp);
		}

/*
		function arrayToXML($a)
		{
			$xml = '';

			foreach($a as $k => $v)
			$xml .= "<$k>" . (is_array($v) ? $this->arrayToXML($v) : $v) . "</$k>";

			return $xml;
		}
*/

	}


