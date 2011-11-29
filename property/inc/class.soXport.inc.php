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
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soXport
	{
		var $db = '';
		var $account_id = 0;
		var $total_records = 0;
		var $bilagsnr;
		var $voucher_id;

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['currentapp']	=	'property';
			$this->soinvoice		= CreateObject('property.soinvoice',true);
			$this->db 				= & $GLOBALS['phpgw']->db;
			$this->like 			= & $this->db->like;
			$this->join 			= & $this->db->join;
			$this->left_join		= & $this->db->left_join;
			$this->datetimeformat	= $this->db->datetime_format();
			$this->account_id 		= $GLOBALS['phpgw_info']['user']['account_id'];
		}


		function auto_tax($dima='')
		{
			if(!$dima)
			{
				return;
			}
			$sql = "select mva as tax_code from fm_location1 where loc1='" . substr($dima,0,4) . "'";
			$this->db->query($sql);
			$this->db->next_record();

			return $this->db->f('tax_code');
		}

		function tax_b_account_override($mvakode='',$b_account_id='')
		{
			if(!$b_account_id)
			{
				return $mvakode;
			}
			$sql = "select mva as tax_code from fm_b_account where id='$b_account_id'";
			$this->db->query($sql);
			$this->db->next_record();

			if($this->db->f('tax_code'))
			{
				return $this->db->f('tax_code');
			}
			else
			{
				return $mvakode;
			}

		}

		function tax_vendor_override($mvakode='',$vendor_id='')
		{
			if(!$vendor_id)
			{
				return $mvakode;
			}
			$sql = "select mva as tax_code from fm_vendor where id='$vendor_id'";
			$this->db->query($sql);
			$this->db->next_record();

			if($this->db->f('tax_code'))
			{
				return $this->db->f('tax_code');
			}
			else
			{
				return $mvakode;
			}

		}

		function get_kostra_id($dima='')
		{
			if(!$dima)
			{
				return;
			}
			$sql = "select kostra_id from fm_location1 where loc1='" . substr($dima,0,4) . "'";
			$this->db->query($sql);
			$this->db->next_record();

			return $this->db->f('kostra_id');
		}

		function anleggsnr_to_objekt($anleggsnr,$meter_table)
		{
			$this->db->query("select $meter_table.ext_meter_id,$meter_table.loc1,$meter_table.loc2,$meter_table.loc3,fm_part_of_town.district_id "
				. " from $meter_table $this->join fm_location1 ON $meter_table.loc1 = fm_location1.loc1 $this->join "
				. " fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id where $meter_table.ext_system_id='$anleggsnr'");

			$this->db->next_record();

//			$location	= explode("-", $this->db->f('location_code'));

			$loc1 = $this->db->f('loc1');
			$loc2 = $this->db->f('loc2');
			$loc3 = $this->db->f('loc3');
			$dima=$loc1.$loc2.$loc3;

			$maalerinfo['loc1']=$loc1;
			$maalerinfo['dima']=$dima;
			$maalerinfo['maalernr']=$this->db->f('ext_meter_id');
			$maalerinfo['district']=$this->db->f('district_id');
			return $maalerinfo;

		}

		function gabnr_to_objekt($Gnr,$Bnr,$sekjonnr)
		{
			//Finn dima fra Boei
			$sql = "SELECT fm_gab_location.loc1, fm_gab_location.loc2, fm_gab_location.loc3,fm_part_of_town.district_id"
				. " FROM fm_gab_location, fm_location1, fm_owner, fm_part_of_town"
				. " WHERE substring(fm_gab_location.gab_id,5,5)='$Gnr' AND"
				. " substring(fm_gab_location.gab_id,10,4)='$Bnr' AND"
				. " substring(fm_gab_location.gab_id,18,3)='$sekjonnr' AND"
				. " fm_gab_location.loc1=fm_location1.loc1 AND"
				. " fm_location1.owner_id=fm_owner.id AND"
				. " fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id ";
			//	. "      and (fm_owner.category=0 or fm_owner.category=2)";

			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();

			$gabinfo['loc1']=$GLOBALS['phpgw']->db->f('loc1');
			$gabinfo['dima']=$GLOBALS['phpgw']->db->f('loc1').$GLOBALS['phpgw']->db->f('loc2').$GLOBALS['phpgw']->db->f('loc3');
			$gabinfo['district_id']=$GLOBALS['phpgw']->db->f('district_id');

			return $gabinfo;
		}


		function dima_to_address($dima)
		{
			$loc1=substr($dima,0,4);
			$loc2=substr($dima,4,2);
			$loc3=substr($dima,6,2);
			$sql = "select loc3_name from fm_location3 where loc1 = '$loc1' and loc2= '$loc2' and loc3 = '$loc3' ";
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			$address=$GLOBALS['phpgw']->db->f('loc3_name');
			return $address;

		}

		function check_order($id)
		{
			$this->db->query("SELECT id,type FROM fm_orders where id='{$id}'");
			$this->db->next_record();
			return $this->db->f('type');
		}

		function get_project($id)
		{
			$id = (int) $id;
			$sql = "SELECT project_group FROM fm_workorder"
				. " $this->join fm_project ON fm_workorder.project_id = fm_project.id WHERE fm_workorder.id={$id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('project_group');
		}

		function check_spbudact_code($id)
		{

			$this->db->query("select count(*) as cnt from fm_b_account where id='$id'");
			$this->db->next_record();
			return $this->db->f('cnt');
		}

		function add($buffer)
		{
			$this->db->transaction_begin();

			$num=0;
			foreach ($buffer as $fields)
			{
				if(abs($fields['belop'])>0)
				{
					if(!$fields['bilagsnr'])
					{
						$fields['bilagsnr']  = $this->soinvoice->next_bilagsnr();
						$this->bilagsnr = $fields['bilagsnr'];//FIXME
						$this->voucher_id = $fields['bilagsnr'];
					}

					$values= array(
						$fields['project_id'],
						$fields['kostra_id'],
						$fields['pmwrkord_code'],
						$fields['bilagsnr'],
						$fields['bilagsnr_ut'],
						$fields['splitt'],
						$fields['kildeid'],
						$fields['kidnr'],
						$fields['typeid'],
						$fields['fakturadato'],
						$fields['forfallsdato'],
						$fields['regtid'],
						$fields['artid'],
						$fields['spvend_code'],
						$fields['dimb'],
						$fields['oppsynsmannid'],
						$fields['saksbehandlerid'],
						$fields['budsjettansvarligid'],
						$fields['fakturanr'],
						$fields['spbudact_code'],
						$fields['loc1'],
						$fields['dima'],
						$fields['dimd'],
						$fields['mvakode'],
						$fields['periode'],
						$this->db->db_addslashes($fields['merknad']),
						false,
						false,
						false,
						false,
						$fields['item_type'],
						$fields['item_id'],
						$fields['external_ref'],
						isset($fields['currency']) && $fields['currency'] ? $fields['currency'] : 'NOK'
					);

					$bilagsnr	= (int)$fields['bilagsnr'];

					$values	= $this->db->validate_insert($values);

					$sql= "INSERT INTO fm_ecobilag (project_id,kostra_id,pmwrkord_code,bilagsnr,bilagsnr_ut,splitt,kildeid,kidnr,typeid,fakturadato,"
						. " forfallsdato,regtid,artid,spvend_code,dimb,oppsynsmannid,saksbehandlerid,budsjettansvarligid,"
						. " fakturanr,spbudact_code,loc1,dima,dimd,mvakode,periode,merknad,oppsynsigndato,saksigndato,"
						. " budsjettsigndato,utbetalingsigndato,item_type,item_id,external_ref,currency,belop,godkjentbelop)"
						. " VALUES ({$values}," . $this->db->money_format($fields['belop']) . ',' . $this->db->money_format($fields['godkjentbelop']) .')';

					$this->db->query($sql,__LINE__,__FILE__);

					$num++;
				}
			}

			$now = time();
			$this->db->query("SELECT start_date FROM fm_idgenerator WHERE name='Bilagsnummer' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$start_date = (int)$this->db->f('start_date');
			$this->db->query("UPDATE fm_idgenerator set value = {$bilagsnr} WHERE name = 'Bilagsnummer' AND start_date = {$start_date}");

			$this->db->transaction_commit();

			return $num;
		}

		function add_OverfBilag ($data)
		{
			$values= array(
				$data['id'],
				$data['bilagsnr'],
				$data['bilagsnr_ut'],
				$data['kidnr'],
				$data['typeid'],
				$data['kildeid'],
				$data['project_id'],
				$data['kostra_id'],
				$data['pmwrkord_code'],
				$data['fakturadato'],
				$data['periode'],
				$data['periodization'],
				$data['periodization_start'],
				$data['forfallsdato'],
				$data['fakturanr'],
				$data['spbudact_code'],
				$data['regtid'],
				$data['artid'],
				$data['spvend_code'],
				$data['dima'],
				$data['loc1'],
				$data['dimb'],
				$data['mvakode'],
				$data['dimd'],
				$data['oppsynsmannid'],
				$data['saksbehandlerid'],
				$data['budsjettansvarligid'],
				$data['oppsynsigndato'],
				$data['saksigndato'],
				$data['budsjettsigndato'],
				$this->db->db_addslashes($data['merknad']),
				$data['splitt'],
				$data['utbetalingid'],
				$data['utbetalingsigndato'],
				$data['filnavn'],
				date($this->db->datetime_format()),
				$data['item_type'],
				$data['item_id'],
				$data['external_ref'],
				$data['currency'],
				$this->db->db_addslashes($data['process_log']),
				$data['process_code']
			);

			$values	= $this->db->validate_insert($values);

			$sql="INSERT INTO fm_ecobilagoverf (id,bilagsnr,bilagsnr_ut,kidnr,typeid,kildeid,project_id,kostra_id,pmwrkord_code,fakturadato,"
				. " periode,periodization,periodization_start,forfallsdato,fakturanr,spbudact_code,regtid,artid,spvend_code,dima,loc1,"
				. " dimb,mvakode,dimd,oppsynsmannid,saksbehandlerid,budsjettansvarligid,oppsynsigndato,saksigndato,"
				. " budsjettsigndato,merknad,splitt,utbetalingid,utbetalingsigndato,filnavn,overftid,item_type,item_id,external_ref,"
				. " currency,process_log,process_code,belop,godkjentbelop,ordrebelop)"
				. "values ($values, "
				. $this->db->money_format($data['belop']) . ","
				. $this->db->money_format($data['godkjentbelop']) . ","
				. $this->db->money_format($data['ordrebelop']) . ")";

			$this->db->query($sql,__LINE__,__FILE__);
			//echo 'sql ' . $sql.'<br>';
		}

		function delete_from_fm_ecobilag($id)
		{
			$sql="delete from fm_ecobilag where id=$id";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		// Velg ut alle hoved bilag som skal overføres
		function hoved_bilag ($periode,$pre_transfer='')
		{
			if($pre_transfer)
			{
				$sql= "SELECT sum(belop) as belop, bilagsnr from fm_ecobilag WHERE periode='$periode' AND utbetalingsigndato IS NULL GROUP BY bilagsnr ";
			}
			else
			{
				$sql="select sum(belop) as belop, bilagsnr from fm_ecobilag where periode='$periode'  AND budsjettsigndato is not NULL  AND ( saksigndato is not NULL  OR oppsynsigndato is not NULL  ) AND utbetalingsigndato is not NULL group by bilagsnr";
			}

			$this->db->query($sql,__LINE__,__FILE__);
			$i = 0;
			while ($this->db->next_record())
			{
				$hoved_bilag_temp[$i]['belop']		= $this->db->f('belop');
				$hoved_bilag_temp[$i]['bilagsnr']	= $this->db->f('bilagsnr');
				$i++;
			}

			//_debug_array($hoved_bilag_temp);

			if ($hoved_bilag_temp)
			{
				$i = 0;
				while(each($hoved_bilag_temp))
				{
					$bilagsnr = $hoved_bilag_temp[$i]['bilagsnr'];

					$sql= "select fm_ecobilag.*,fm_ecouser.initials as saksbehandler from fm_ecobilag $this->join fm_ecouser on fm_ecobilag.budsjettansvarligid=fm_ecouser.lid where bilagsnr=$bilagsnr ";
					$this->db->query($sql,__LINE__,__FILE__);

					$this->db->next_record();

					$hoved_bilag[$i]['id']				= $this->db->f('id');
					$hoved_bilag[$i]['bilagsnr']		= $bilagsnr;
					$hoved_bilag[$i]['kidnr']			= $this->db->f('kidnr');
					$hoved_bilag[$i]['typeid']			= $this->db->f('typeid');
					$hoved_bilag[$i]['kildeid']			= $this->db->f('kildeid');
					$hoved_bilag[$i]['pmwrkord_code']	= $this->db->f('pmwrkord_code');
					$hoved_bilag[$i]['belop']			= $hoved_bilag_temp[$i]['belop'];
					$hoved_bilag[$i]['fakturadato']		= $this->db->f('fakturadato');
					$hoved_bilag[$i]['periode']			= $this->db->f('periode');
					$hoved_bilag[$i]['forfallsdato']	= $this->db->f('forfallsdato');
					$hoved_bilag[$i]['fakturanr']		= $this->db->f('fakturanr');
					$hoved_bilag[$i]['spbudact_code']	= $this->db->f('spbudact_code');
					$hoved_bilag[$i]['regtid']			= $this->db->f('regtid');
					$hoved_bilag[$i]['artid']			= $this->db->f('artid');
					$hoved_bilag[$i]['godkjentbelop']	= $hoved_bilag_temp[$i]['belop'];
					$hoved_bilag[$i]['spvend_code']		= $this->db->f('spvend_code');
					$hoved_bilag[$i]['dima']			= $this->db->f('dima');
					$hoved_bilag[$i]['dimb']			= $this->db->f('dimb');
					$hoved_bilag[$i]['mvakode']			= $this->db->f('mvakode');
					$hoved_bilag[$i]['dimd']			= $this->db->f('dimd');
					if($this->db->f('oppsynsmannid'))
					{
						$hoved_bilag[$i]['oppsynsmannid']	= $this->db->f('oppsynsmannid');
					}
					if($this->db->f('saksbehandlerid'))
					{
						$hoved_bilag[$i]['saksbehandlerid']	= $this->db->f('saksbehandlerid');
					}

					$hoved_bilag[$i]['budsjettansvarligid']	= $this->db->f('budsjettansvarligid');

					if($this->db->f('oppsynsigndato'))
					{
						$hoved_bilag[$i]['oppsynsigndato']	= $this->db->f('oppsynsigndato');
					}
					if($this->db->f('saksigndato'))
					{
						$hoved_bilag[$i]['saksigndato']	= $this->db->f('saksigndato');
					}

					$hoved_bilag[$i]['budsjettsigndato']	= $this->db->f('budsjettsigndato');
					$hoved_bilag[$i]['merknad']				= $this->db->f('merknad');
					$hoved_bilag[$i]['splitt']				= $this->db->f('splitt');
					$hoved_bilag[$i]['utbetalingid']		= $this->db->f('utbetalingid');
					$hoved_bilag[$i]['utbetalingsigndato']	= $this->db->f('utbetalingsigndato');
					$hoved_bilag[$i]['saksbehandler']		= $this->db->f('saksbehandler');
					$i++;
				}
			}
			//_debug_array($hoved_bilag);

			return $hoved_bilag;
		}

		//Velg ut alle underbilag

		function select_underbilag ($bilagsnr)
		{
			$sql= "SELECT fm_ecobilag.* ,fm_part_of_town.district_id FROM (fm_location1 $this->join fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id) $this->join fm_ecobilag ON fm_location1.loc1 = fm_ecobilag.loc1 WHERE bilagsnr='$bilagsnr'";

			$this->db->query($sql,__LINE__,__FILE__);
			$i = 0;
			while ($this->db->next_record())
			{
				$underbilag[$i]['id']	= $this->db->f('id');
				$underbilag[$i]['bilagsnr']	= $this->db->f('bilagsnr');
				$underbilag[$i]['kidnr']	= $this->db->f('kidnr');
				$underbilag[$i]['typeid']	= $this->db->f('typeid');
				$underbilag[$i]['kildeid']	= $this->db->f('kildeid');
				$underbilag[$i]['pmwrkord_code']	= $this->db->f('pmwrkord_code');
				$underbilag[$i]['belop']	= $this->db->f('belop');
				$underbilag[$i]['fakturadato']	= $this->db->f('fakturadato');
				$underbilag[$i]['periode']	= $this->db->f('periode');
				$underbilag[$i]['forfallsdato']	= $this->db->f('forfallsdato');
				$underbilag[$i]['fakturanr']	= $this->db->f('fakturanr');
				$underbilag[$i]['spbudact_code']	= $this->db->f('spbudact_code');
				$underbilag[$i]['regtid']	= $this->db->f('regtid');
				$underbilag[$i]['artid']	= $this->db->f('artid');
				$underbilag[$i]['godkjentbelop']	= $this->db->f('godkjentbelop');
				$underbilag[$i]['spvend_code']	= $this->db->f('spvend_code');
				$underbilag[$i]['dima']	= $this->db->f('dima');
				$underbilag[$i]['loc1']	= $this->db->f('loc1');
				$underbilag[$i]['dimb']	= $this->db->f('dimb');
				$underbilag[$i]['mvakode']	= $this->db->f('mvakode');
				$underbilag[$i]['dimd']	= $this->db->f('dimd');
				$underbilag[$i]['project_id']	= $this->db->f('project_id');
				$underbilag[$i]['kostra_id']	= $this->db->f('kostra_id');
				if($this->db->f('oppsynsmannid'))
				{
					$underbilag[$i]['oppsynsmannid']	= $this->db->f('oppsynsmannid');
				}
				if($this->db->f('saksbehandlerid'))
				{
					$underbilag[$i]['saksbehandlerid']	= $this->db->f('saksbehandlerid');
				}

				$underbilag[$i]['budsjettansvarligid']	= $this->db->f('budsjettansvarligid');

				if($this->db->f('oppsynsigndato'))
				{
					$underbilag[$i]['oppsynsigndato']	= $this->db->f('oppsynsigndato');
				}
				if($this->db->f('saksigndato'))
				{
					$underbilag[$i]['saksigndato']	= $this->db->f('saksigndato');
				}

				$underbilag[$i]['budsjettsigndato']	= $this->db->f('budsjettsigndato');
				$underbilag[$i]['merknad']	= $this->db->f('merknad');
				$underbilag[$i]['splitt']	= $this->db->f('splitt');
				$underbilag[$i]['utbetalingid']	= $this->db->f('utbetalingid');
				$underbilag[$i]['utbetalingsigndato']	= $this->db->f('utbetalingsigndato');
				$underbilag[$i]['district_id']	= $this->db->f('district_id');
				$underbilag[$i]['item_type']	= $this->db->f('item_type');
				$underbilag[$i]['item_id']	= $this->db->f('item_id');
				$i++;
			}

			return $underbilag;
		}

/*		function update_avvik($avvik)
		{
		}

 */
		function log_to_deviation_table($oRsBilag)
		{
			$bilagsnr=$oRsBilag['bilagsnr'];
			$fakturadato=$oRsBilag['fakturadato'];
			$forfallsdato=$oRsBilag['forfallsdato'];
			$oppsynsmannid=$oRsBilag['oppsynsmannid'];
			$oppsynsigndato=$oRsBilag['oppsynsigndato'];
			$saksbehandlerid=$oRsBilag['saksbehandlerid'];
			$saksigndato=$oRsBilag['saksigndato'];
			$budsjettansvarligid=$oRsBilag['budsjettansvarligid'];
			$budsjettsigndato=$oRsBilag['budsjettsigndato'];
			$artid=$oRsBilag['artid'];
			$spvend_code=$oRsBilag['spvend_code'];
			$belop=$oRsBilag['belop'];
			$godkjentbelop=$oRsBilag['godkjentbelop'];

			$sql="INSERT INTO fm_ecoavvik (bilagsnr,fakturadato,forfallsdato,oppsynsmannid,oppsynsigndato,saksbehandlerid,saksigndato,budsjettansvarligid,budsjettsigndato,artid,spvend_code,belop,godkjentbelop)  values "
				. "($bilagsnr','$fakturadato','$forfallsdato','$oppsynsmannid','$oppsynsigndato','$saksbehandlerid','$saksigndato','$budsjettansvarligid','$budsjettsigndato','$artid','$spvend_code','$belop','$godkjentbelop')";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function delete_avvik($bilagsnr)
		{
			$sql="delete from fm_ecoavvik where bilagsnr='$bilagsnr'";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function delete_invoice($bilagsnr)
		{
			$sql="delete from fm_ecobilagoverf where bilagsnr='$bilagsnr'";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		//Logg transaksjon
		function log_transaction($batchid,$bilagid,$message='')
		{
			$tid=date($this->datetimeformat);
			$sql= "insert into fm_ecologg (batchid,ecobilagid,melding,tid) values ('$batchid','$bilagid' ,'$message','$tid')";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function increment_batchid()
		{
			$name = 'Ecobatchid';
			$now = time();
			$this->db->query("SELECT value, start_date FROM fm_idgenerator WHERE name='{$name}' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$next_id = $this->db->f('value') +1;
			$start_date = (int)$this->db->f('start_date');
			$this->db->query("UPDATE fm_idgenerator SET value = $next_id WHERE name = '{$name}' AND start_date = {$start_date}");

			return $next_id;
		}

		function next_batchid()
		{
			$now = time();
			$this->db->query("SELECT value FROM fm_idgenerator WHERE name = 'Ecobatchid' AND start_date < {$now} ORDER BY start_date DESC");
			$this->db->next_record();
			$next_id = $this->db->f('value') +1;
			return $next_id;
		}

		function get_responsible($b_abbount_id)
		{
			$this->db->query("select account_lid from fm_b_account $this->join phpgw_accounts on fm_b_account.responsible = phpgw_accounts.account_id where fm_b_account.id = '$b_abbount_id'");
			$this->db->next_record();
			$responsible = $this->db->f('account_lid');
			return $responsible;
		}
	}
