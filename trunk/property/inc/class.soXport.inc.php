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

	phpgw::import_class('phpgwapi.datetime');

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
		protected $global_lock = false;
		var $debug = false;
		public $supertransaction = false;

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


		function auto_tax($loc1='')
		{
			if(!$loc1)
			{
				return;
			}
			$sql = "SELECT mva as tax_code FROM fm_location1 WHERE loc1='{$loc1}'";
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
			$sql = "SELECT mva as tax_code FROM fm_b_account WHERE id='$b_account_id'";
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
			$sql = "SELECT mva as tax_code FROM fm_vendor WHERE id='$vendor_id'";
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

		function get_kostra_id($loc1='')
		{
			if(!$loc1)
			{
				return;
			}
			$sql = "SELECT kostra_id FROM fm_location1 WHERE loc1='{$loc1}'";
			$this->db->query($sql);
			$this->db->next_record();

			return $this->db->f('kostra_id');
		}

		function anleggsnr_to_objekt($anleggsnr,$meter_table)
		{
			$this->db->query("SELECT $meter_table.maaler_nr,$meter_table.loc1,$meter_table.loc2,$meter_table.loc3,fm_part_of_town.district_id "
				. " FROM $meter_table $this->join fm_location1 ON $meter_table.loc1 = fm_location1.loc1 $this->join "
				. " fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id WHERE $meter_table.ext_system_id='$anleggsnr'");

			$this->db->next_record();

//			$location	= explode("-", $this->db->f('location_code'));

			$loc1 = $this->db->f('loc1');
			$loc2 = $this->db->f('loc2');
			$loc3 = $this->db->f('loc3');
			$dima=$loc1.$loc2.$loc3;

			$maalerinfo['loc1']=$loc1;
			$maalerinfo['dima']=$dima;
			$maalerinfo['maalernr']=$this->db->f('maaler_nr');
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
			$sql = "SELECT loc3_name FROM fm_location3 WHERE loc1 = '$loc1' and loc2= '$loc2' and loc3 = '$loc3' ";
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			$address=$GLOBALS['phpgw']->db->f('loc3_name');
			return $address;

		}

		function check_order($id)
		{
			$this->db->query("SELECT id,type FROM fm_orders WHERE id='{$id}'");
			$this->db->next_record();
			return $this->db->f('type');
		}

		function get_project($id)
		{
			$id = (int) $id;
			$sql = "SELECT project_group FROM fm_workorder"
				. " {$this->join} fm_project ON fm_workorder.project_id = fm_project.id WHERE fm_workorder.id={$id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('project_group');
		}

		function check_spbudact_code($id)
		{

			$this->db->query("SELECT count(*) as cnt FROM fm_b_account WHERE id='$id'");
			$this->db->next_record();
			return $this->db->f('cnt');
		}

		/**
		 * Add voucher to work-katalog
		 * @param array $buffer holds the dataset
		 * @param bool $skip_update_voucher_id do not increment voucher_id
		 * @return int number of records inserted
		 */
		function add($buffer, $skip_update_voucher_id = false)
		{
			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

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
						isset($fields['splitt']) && $fields['splitt'] ? $fields['splitt'] : false,
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
						isset($fields['dimd']) && $fields['dimd'] ? $fields['dimd'] : false,
						isset($fields['dime']) && $fields['dime'] ? $fields['dime'] : false,
						$fields['mvakode'],
						$fields['periode'],
						$this->db->db_addslashes($fields['merknad']),
						isset($fields['line_text']) && $fields['line_text'] ? $this->db->db_addslashes($fields['line_text']) : false,
						false,
						false,
						false,
						false,
						isset($fields['item_type']) && $fields['item_type'] ? $fields['item_type'] : false,
						isset($fields['item_id']) && $fields['item_id'] ? $fields['item_id'] : false,
						isset($fields['external_ref']) && $fields['external_ref'] ? $fields['external_ref'] : false,
						isset($fields['external_voucher_id']) && $fields['external_voucher_id'] ? $fields['external_voucher_id'] : false,
						isset($fields['currency']) && $fields['currency'] ? $fields['currency'] : 'NOK'
					);

					$bilagsnr	= (int)$fields['bilagsnr'];

					$values	= $this->db->validate_insert($values);

					$sql= "INSERT INTO fm_ecobilag (project_id,kostra_id,pmwrkord_code,bilagsnr,bilagsnr_ut,splitt,kildeid,kidnr,typeid,fakturadato,"
						. " forfallsdato,regtid,artid,spvend_code,dimb,oppsynsmannid,saksbehandlerid,budsjettansvarligid,"
						. " fakturanr,spbudact_code,loc1,dima,dimd,dime,mvakode,periode,merknad,line_text,oppsynsigndato,saksigndato,"
						. " budsjettsigndato,utbetalingsigndato,item_type,item_id,external_ref,external_voucher_id,currency,belop,godkjentbelop)"
						. " VALUES ({$values}," . $this->db->money_format($fields['belop']) . ',' . $this->db->money_format($fields['godkjentbelop']) .')';
//						_debug_array($sql);die();
					$this->db->query($sql,__LINE__,__FILE__);

					$num++;

					if(!$skip_update_voucher_id)
					{
						$now = time();
						$this->db->query("SELECT start_date FROM fm_idgenerator WHERE name='Bilagsnummer' AND start_date < {$now} ORDER BY start_date DESC");
						$this->db->next_record();
						$start_date = (int)$this->db->f('start_date');
						$this->db->query("UPDATE fm_idgenerator set value = {$bilagsnr} WHERE name = 'Bilagsnummer' AND start_date = {$start_date}");
					}
				}
			}

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}

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
				$data['dime'],
				$data['oppsynsmannid'],
				$data['saksbehandlerid'],
				$data['budsjettansvarligid'],
				$data['oppsynsigndato'],
				$data['saksigndato'],
				$data['budsjettsigndato'],
				$this->db->db_addslashes($data['merknad']),
				$this->db->db_addslashes($data['line_text']),
				$data['splitt'],
				$data['utbetalingid'],
				$data['utbetalingsigndato'],
				$data['filnavn'],
				isset($data['overftid']) && $data['overftid'] ? $data['overftid'] : date($this->db->datetime_format()),
				isset($data['item_type']) && $data['item_type'] ? $data['item_type'] : false,
				isset($data['item_id']) && $data['item_id'] ? $data['item_id'] : false,
				isset($data['external_ref']) && $data['external_ref'] ? $data['external_ref'] : false,
				isset($data['external_voucher_id']) && $data['external_voucher_id'] ? $data['external_voucher_id'] : false,
				$data['currency'],
				isset($data['manual_record']) && $data['manual_record'] ? $data['manual_record'] : false,
				isset($data['process_code']) && $data['process_code'] ? $data['process_code'] : false,
				$this->db->db_addslashes($data['process_log']),
			);

			$values	= $this->db->validate_insert($values);

			$sql="INSERT INTO fm_ecobilagoverf (id,bilagsnr,bilagsnr_ut,kidnr,typeid,kildeid,project_id,kostra_id,pmwrkord_code,fakturadato,"
				. " periode,periodization,periodization_start,forfallsdato,fakturanr,spbudact_code,regtid,artid,spvend_code,dima,loc1,"
				. " dimb,mvakode,dimd,dime,oppsynsmannid,saksbehandlerid,budsjettansvarligid,oppsynsigndato,saksigndato,"
				. " budsjettsigndato,merknad,line_text,splitt,utbetalingid,utbetalingsigndato,filnavn,overftid,item_type,item_id,external_ref,"
				. " external_voucher_id,currency,manual_record,process_code,process_log,belop,godkjentbelop,ordrebelop)"
				. "VALUES ($values, "
				. $this->db->money_format($data['belop']) . ","
				. $this->db->money_format($data['godkjentbelop']) . ","
				. $this->db->money_format($data['ordrebelop']) . ")";

			$this->db->query($sql,__LINE__,__FILE__);
			
/*
			if($data['manual_record'] && ($data['process_log'] || $data['process_code']))
			{
				$valueset_log = array
				(
					$data['bilagsnr'],
					$data['process_code'],
					$this->db->db_addslashes($data['process_log']),
					$this->account_id,
					time()
				); 

				$values	= $this->db->validate_insert($valueset_log);
				
				$sql = "INSERT INTO fm_ecobilag_process_log (bilagsnr,process_code,process_log,user_id,entry_date) VALUES ({$values})";
				$this->db->query($sql,__LINE__,__FILE__);
			}
*/
			return true;
		}


    	function get_voucher($bilagsnr)
    	{
  	  		$sql= "SELECT fm_ecobilag.* FROM fm_ecobilag WHERE bilagsnr = {$bilagsnr}";
			$this->db->query($sql,__LINE__,__FILE__);

			$voucher = array();
			while ($this->db->next_record())
			{
				$voucher[] = array
				(
					'id'					=> $this->db->f('id'),
					'bilagsnr'				=> $bilagsnr,
					'bilagsnr_ut'			=> $this->db->f('bilagsnr_ut'),
					'kidnr'					=> $this->db->f('kidnr'),
					'typeid'				=> $this->db->f('typeid'),
					'kildeid'				=> $this->db->f('kildeid'),
					'project_id'			=> $this->db->f('project_id'),
					'pmwrkord_code'			=> $this->db->f('pmwrkord_code'),
					'order_id'				=> $this->db->f('pmwrkord_code'),
					'belop'					=> $this->db->f('belop'),
					'fakturadato'			=> $this->db->f('fakturadato'),
					'periode'				=> $this->db->f('periode'),
					'periodization'			=> $this->db->f('periodization'),
					'periodization_start'	=> $this->db->f('periodization_start'),
					'forfallsdato'			=> $this->db->f('forfallsdato'),
					'fakturanr'				=> $this->db->f('fakturanr'),
					'spbudact_code'			=> $this->db->f('spbudact_code'),
					'regtid'				=> $this->db->f('regtid'),
					'artid'					=> $this->db->f('artid'),
					'godkjentbelop'			=> $this->db->f('godkjentbelop'),
					'spvend_code'			=> $this->db->f('spvend_code'),
					'loc1'					=> $this->db->f('loc1'),
					'dima'					=> $this->db->f('dima'),
					'dimb'					=> $this->db->f('dimb'),
					'mvakode'				=> $this->db->f('mvakode'),
					'dimd'					=> $this->db->f('dimd'),
					'dime'					=> $this->db->f('dime'),
					'oppsynsmannid'			=> $this->db->f('oppsynsmannid'),
					'saksbehandlerid'		=> $this->db->f('saksbehandlerid'),
					'budsjettansvarligid'	=> $this->db->f('budsjettansvarligid'),
					'oppsynsigndato'		=> $this->db->f('oppsynsigndato'),
					'saksigndato'			=> $this->db->f('saksigndato'),
					'budsjettsigndato'		=> $this->db->f('budsjettsigndato'),
					'merknad'				=> $this->db->f('merknad',true),
					'line_text'				=> $this->db->f('line_text',true),
					'splitt'				=> $this->db->f('splitt'),
					'utbetalingid'			=> $this->db->f('utbetalingid'),
					'utbetalingsigndato'	=> $this->db->f('utbetalingsigndato'),
					'external_ref'			=> $this->db->f('external_ref'),
					'external_voucher_id'	=> $this->db->f('external_voucher_id'),
					'kostra_id'				=> $this->db->f('kostra_id'),
					'currency'				=> $this->db->f('currency'),
	 	  			'process_log'			=> $this->db->f('process_log',true),
	 	  			'process_code'			=> $this->db->f('process_code'),

				);
			}

/*
 	  		if($voucher)
 	  		{
 		  		$sql= "SELECT * FROM fm_ecobilag_process_log WHERE bilagsnr = {$bilagsnr}";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
 				$process_log	= $this->db->f('process_log',true);
				$process_code	= $this->db->f('process_code');

	 	  		foreach ($voucher as &$line)
	 	  		{
	 	  			$line['process_log'] = $process_log;
	 	  			$line['process_code'] = $process_code;
	 	  		}
 	  		}
*/
			return $voucher;
    	}


		function delete_from_fm_ecobilag($id)
		{
			$sql="DELETE FROM fm_ecobilag WHERE id=$id";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		// Velg ut alle hoved bilag som skal overføres
		function hoved_bilag ($periode,$pre_transfer='')
		{
			if($pre_transfer)
			{
				$sql= "SELECT sum(belop) as belop, bilagsnr FROM fm_ecobilag WHERE periode='$periode' AND utbetalingsigndato IS NULL GROUP BY bilagsnr ";
			}
			else
			{
				$sql="SELECT sum(belop) as belop, bilagsnr FROM fm_ecobilag WHERE periode='$periode'  AND budsjettsigndato is not NULL  AND ( saksigndato is not NULL  OR oppsynsigndato is not NULL  ) AND utbetalingsigndato is not NULL group by bilagsnr";
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

					$sql= "SELECT fm_ecobilag.*,fm_ecouser.initials as saksbehandler FROM fm_ecobilag $this->join fm_ecouser on fm_ecobilag.budsjettansvarligid=fm_ecouser.lid WHERE bilagsnr='{$bilagsnr}'";
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
					$hoved_bilag[$i]['dime']			= $this->db->f('dime');
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
					$hoved_bilag[$i]['line_text']			= $this->db->f('line_text');
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
				$underbilag[$i]['dime']	= $this->db->f('dime');
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
				$underbilag[$i]['line_text']	= $this->db->f('line_text');
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

			$sql="INSERT INTO fm_ecoavvik (bilagsnr,fakturadato,forfallsdato,oppsynsmannid,oppsynsigndato,saksbehandlerid,saksigndato,budsjettansvarligid,budsjettsigndato,artid,spvend_code,belop,godkjentbelop)  VALUES "
				. "($bilagsnr','$fakturadato','$forfallsdato','$oppsynsmannid','$oppsynsigndato','$saksbehandlerid','$saksigndato','$budsjettansvarligid','$budsjettsigndato','$artid','$spvend_code','$belop','$godkjentbelop')";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function delete_avvik($bilagsnr)
		{
			$sql="DELETE FROM fm_ecoavvik WHERE bilagsnr='$bilagsnr'";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function delete_invoice($bilagsnr)
		{
			$sql="DELETE FROM fm_ecobilagoverf WHERE bilagsnr='$bilagsnr'";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function delete_voucher_from_fm_ecobilag($bilagsnr)
		{
			$bilagsnr = (int) $bilagsnr;
			$sql="DELETE FROM fm_ecobilag WHERE bilagsnr = $bilagsnr";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		//Logg transaksjon
		function log_transaction($batchid,$bilagid,$message='')
		{
			$tid=date($this->datetimeformat);
			$sql= "INSERT INTO fm_ecologg (batchid,ecobilagid,melding,tid) VALUES ('$batchid','$bilagid' ,'$message','$tid')";
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
			$this->db->query("SELECT account_lid FROM fm_b_account {$this->join} phpgw_accounts on fm_b_account.responsible = phpgw_accounts.account_id WHERE fm_b_account.id = '{$b_abbount_id}'",__LINE__,__FILE__);
			$this->db->next_record();
			$responsible = $this->db->f('account_lid');
			return $responsible;
		}

		function add_manual_invoice($values, $skip_update_voucher_id = false)
		{
			$orders_affected = array();
			$_dateformat = $this->db->date_format();

			if(!$this->supertransaction)
			{
				$this->db->transaction_begin();
			}

			$num = $this->add($values, $skip_update_voucher_id);
			$this->voucher_id = $values[0]['bilagsnr'];

			$voucher = $this->get_voucher($values[0]['bilagsnr']);
			foreach ($voucher as &$line)
			{
				$line['overftid']				= date($_dateformat,phpgwapi_datetime::date_to_timestamp($values[0]['paid_date']));	
				$line['ordrebelop']				= $line['godkjentbelop'];	
				$line['filnavn']				= 'dummy';		
				$line['oppsynsigndato']			= date( $_dateformat );
				$line['saksigndato']			= date( $_dateformat );
				$line['budsjettsigndato']		= date( $_dateformat );
				$line['utbetalingsigndato']		= date( $_dateformat );
				$line['utbetalingid']			= $GLOBALS['phpgw']->accounts->get($this->account_id)->lid;
				$line['manual_record']			= 1;

				$this->add_OverfBilag($line);
			}
			$this->delete_voucher_from_fm_ecobilag($values[0]['bilagsnr']);

			reset($voucher);

			if($this->debug)
			{
				return true;
			}

			foreach ($voucher as &$line)
			{
				if($line['order_id'])
				{
					$amount =  $line['godkjentbelop'] * 100; 
					//Oppdater beløp på bestilling
					if ($line['dimd'] % 2 == 0)
					{
						$actual_cost_field='act_mtrl_cost';
					}
					else
					{
						$actual_cost_field='act_vendor_cost';
					}
					$operator = '+';
					if(!$this->debug)
					{
						//notify_coordinator_on_consumption is performed here..
						$this->correct_actual_cost($line['order_id'],$amount, $actual_cost_field, $operator);
					}
				}
			}

 			$this->update_actual_cost_from_archive($orders_affected);

			if(!$this->supertransaction)
			{
				return $this->db->transaction_commit();
			}
			else
			{
				return $num;
			}
		}


		public function update_actual_cost_from_archive($orders_affected)
		{
			$soworkorder = CreateObject('property.soworkorder');

			$orders = array();
			if($orders_affected)
			{
				$sql = 'SELECT order_id, actual_cost FROM fm_orders_actual_cost_view WHERE order_id IN (' . implode(',', array_keys($orders_affected)) . ')';
				$this->db->query($sql,__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					$orders[] = array
					(
						'order_id'		=>	$this->db->f('order_id'),
						'actual_cost'	=>	$this->db->f('actual_cost')
					);
				}

				foreach ($orders as $order)
				{
					$this->db->query("UPDATE fm_workorder SET actual_cost = '{$order['actual_cost']}' WHERE id = '{$order['order_id']}'",__LINE__,__FILE__);

					$this->db->query("SELECT max(periode) AS period, max(amount) AS amount FROM fm_orders_paid_or_pending_view WHERE order_id =  {$order['order_id']} AND periode IS NOT NULL",__LINE__,__FILE__);
					$this->db->next_record();
					$period		=	$this->db->f('period');
					$amount		=	$this->db->f('amount');
					$year		= 	$period ? (int) substr($period,0,4) : date('Y');

					$this->db->query("SELECT order_id FROM fm_workorder_budget WHERE order_id = {$order['order_id']} AND year = {$year}",__LINE__,__FILE__);

					if (!$this->db->next_record())
					{
						try
						{
							$soworkorder->transfer_budget($order['order_id'], array('budget_amount' => $amount, 'latest_year' => ($year -1)), $year);
						}
						catch(Exception $e)
						{
							if ( $e )
							{
								phpgwapi_cache::message_set($e->getMessage(), 'error'); 
							}
						}
					}

				}

				reset($orders_affected);

				foreach ($orders_affected as $order_id => $dummy)
				{
					phpgwapi_cache::system_clear('property', "budget_order_{$order_id}");

					// Not yet processed
					$this->db->query("SELECT max(amount) AS amount FROM fm_orders_paid_or_pending_view WHERE order_id = {$order_id} AND periode IS NULL",__LINE__,__FILE__);
					$this->db->next_record();
					$amount		=	$this->db->f('amount');
					if($amount)
					{
						$year		= 	date('Y');
						$this->db->query("SELECT order_id FROM fm_workorder_budget WHERE order_id = {$order_id} AND year = {$year}",__LINE__,__FILE__);

						if (!$this->db->next_record())
						{
							try
							{
								$soworkorder->transfer_budget($order_id, array('budget_amount' => $amount, 'latest_year' => ($year -1)), $year);
							}
							catch(Exception $e)
							{
								if ( $e )
								{
									phpgwapi_cache::message_set($e->getMessage(), 'error'); 
								}
							}
						}
					}
				}
			}
		}

   	   	// Oppdater beløp på arbeidsordre
   	   	// operator="-" ved tilbakerulling
		public function correct_actual_cost($order_id, $amount, $actual_cost_field, $operator)
		{
			phpgwapi_cache::system_clear('property', "budget_order_{$order_id}");

			$sql = "SELECT type FROM fm_orders WHERE id='{$order_id}'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$table = '';
			$update_paid = '';
			switch($this->db->f('type'))
			{
				case 'workorder':
					$table = 'fm_workorder';
					if($operator == "-")
					{
						$update_paid = ", paid = 1";
					}
					else
					{
						$update_paid = ", paid = 2";
					}
					break;
				case 's_agreement':
					$table = 'fm_s_agreement';
					$actual_cost_field = 'actual_cost';
					break;
			}

			$amount = $amount/100;
			if(!$table)
			{
				$message = 'property_soXport::correct_actual_cost() ERROR: the order id %1 seems to not correspond with any order type';
				$GLOBALS['phpgw']->log->error(array(
						'text'	=> $message,
						'p1'	=> $order_id,
						'p2'	=> '',
						'line'	=> __LINE__,
						'file'	=> __FILE__
					));

				echo $message . "\n";
			}
			else
			{
				$sql="UPDATE {$table} SET {$actual_cost_field}={$actual_cost_field} {$operator} {$amount} {$update_paid} WHERE id='{$order_id}'";
				$this->db->query($sql,__LINE__,__FILE__);

				execMethod('property.boworkorder.notify_coordinator_on_consumption', $order_id);
			}
		}

		public function check_voucher_id($voucher_id)
		{
			$found = 0;
			$sql = "SELECT bilagsnr FROM fm_ecobilag WHERE bilagsnr='{$voucher_id}'";
			$this->db->query($sql,__LINE__,__FILE__);
			
			if($this->db->next_record())
			{
				$found++;
			}

			$sql = "SELECT bilagsnr FROM fm_ecobilagoverf WHERE bilagsnr='{$voucher_id}'";
			$this->db->query($sql,__LINE__,__FILE__);
			
			if($this->db->next_record())
			{
				$found++;
			}
			return $found;
		}

		public function check_invoice_id($vendor_id, $invoice_id)
		{
			$found = 0;

			$values['fakturanr']		= $values['invoice_id'];
			$values['spvend_code']		= $values['vendor_id'];

			$sql = "SELECT bilagsnr FROM fm_ecobilag WHERE spvend_code= '{$vendor_id}' AND fakturanr='{$invoice_id}'";
			$this->db->query($sql,__LINE__,__FILE__);
			
			if($this->db->next_record())
			{
				$found++;
			}

			$sql = "SELECT bilagsnr FROM fm_ecobilagoverf WHERE spvend_code= '{$vendor_id}' AND fakturanr='{$invoice_id}'";
			$this->db->query($sql,__LINE__,__FILE__);
			
			if($this->db->next_record())
			{
				$found++;
			}
			return $found;
		}
	}
