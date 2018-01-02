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

	class synkroniser_med_boei_old extends property_cron_parent
	{

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('location');
			$this->function_msg = 'Synkroniser_med_boei_old';

			$this->bocommon = CreateObject('property.bocommon');
			$this->db = $this->bocommon->new_db();
			$this->join = $this->db->join;
			$this->like = $this->db->like;
			$this->left_join = " LEFT JOIN ";

			if (isset($this->db->adodb) && $this->db->adodb)
			{
				$this->db_boei = CreateObject('phpgwapi.db', false, $GLOBALS['external_db']['boei']['db_type']);
				$this->db_boei->Host = $GLOBALS['external_db']['boei']['db_host'];
				$this->db_boei->Type = $GLOBALS['external_db']['boei']['db_type'];
				$this->db_boei->Database = $GLOBALS['external_db']['boei']['db_name'];
				$this->db_boei->User = $GLOBALS['external_db']['boei']['db_user'];
				$this->db_boei->Password = $GLOBALS['external_db']['boei']['db_pass'];
				$this->db_boei->Halt_On_Error = 'yes';
				$this->db_boei->connect();
			}
			else
			{
				$this->db_boei = CreateObject('property.db_mssql');
				$this->db_boei->Host = $GLOBALS['external_db']['boei']['db_host'];
				$this->db_boei->Type = $GLOBALS['external_db']['boei']['db_type'];
				$this->db_boei->Database = $GLOBALS['external_db']['boei']['db_name'];
				$this->db_boei->User = $GLOBALS['external_db']['boei']['db_user'];
				$this->db_boei->Password = $GLOBALS['external_db']['boei']['db_pass'];
				$this->db_boei->Halt_On_Error = 'yes';
			}

			$this->db_boei2 = clone($this->db_boei);
			$this->db2 = clone($this->db);
		}

		function execute()
		{
			$start = time();
			set_time_limit(1200);
			$this->update_tables();

			$this->legg_til_eier_phpgw();
			$this->legg_til_gateadresse_phpgw();
			$this->legg_til_objekt_phpgw();
			$this->legg_til_bygg_phpgw();
			$this->legg_til_seksjon_phpgw();
			$this->legg_til_leieobjekt_phpgw();
			$this->legg_til_leietaker_phpgw();
			$this->oppdater_leieobjekt();
			$this->oppdater_boa_objekt();
			$this->oppdater_boa_bygg();
			$this->oppdater_boa_del();
			$this->oppdater_oppsagtdato();
			$this->slett_feil_telefon();
			$this->update_tenant_name();
			$this->update_obskode();
			$this->oppdater_namssakstatus_pr_leietaker();
			$msg = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);

			$this->receipt['message'][] = array('msg' => $msg);
		}

		function cron_log( $receipt = '' )
		{

			$insert_values = array(
				$this->cron,
				date($this->db->datetime_format()),
				$this->function_name,
				$receipt
			);

			$insert_values = $this->db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
				. "VALUES ($insert_values)";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		 * v_Eier
		 * 	v_Gateadresse
		 * 	boei_objekt
		 * 	boei_bygg
		 * 	boei_seksjon
		 * 	boei_leieobjekt
		 * 	boei_leietaker
		 * 	boei_reskontro
		 */
		function update_tables()
		{
			$this->update_table_eier();
			$this->update_table_gateadresse();
			$this->update_table_Objekt();
			$this->update_table_Bygg();
			$this->update_table_seksjon();
			$this->update_table_leieobjekt();
			$this->update_table_leietaker();
			$this->update_table_reskontro();
		}

		function update_table_eier()
		{
			$metadata = $this->db_boei->metadata('Eier');
//_debug_array($metadata);
			$metadata = $this->db->metadata('boei_eier');
//_debug_array($metadata);
			if (!$metadata)
			{
				$sql_table = <<<SQL
				CREATE TABLE boei_eier
				(
				  eier_id integer NOT NULL,
				  navn character varying(50),
				  eiertype_id integer NOT NULL,
				  CONSTRAINT boei_eier_pkey PRIMARY KEY (eier_id)
				);
SQL;
				$this->db->query($sql_table, __LINE__, __FILE__);
			}
			$this->db->query('DELETE FROM boei_eier', __LINE__, __FILE__);
			$sql_boei = 'SELECT TOP 100 PERCENT * FROM Eier';
			$this->db_boei->query($sql_boei, __LINE__, __FILE__);
			// using stored prosedures
			$sql = 'INSERT INTO boei_eier (eier_id, navn, eiertype_id)'
				. ' VALUES(?, ?, ?)';
			$valueset = array();

			while ($this->db_boei->next_record())
			{
				$valueset[] = array
					(
					1 => array
						(
						'value' => (int)$this->db_boei->f('Eier_ID'),
						'type' => PDO::PARAM_INT
					),
					2 => array
						(
						'value' => utf8_encode($this->db_boei->f('Navn')),
						'type' => PDO::PARAM_STR
					),
					3 => array
						(
						'value' => (int)$this->db_boei->f('EierType_ID'),
						'type' => PDO::PARAM_INT
					)
				);
			}

			$this->db->insert($sql, $valueset, __LINE__, __FILE__);
		}

		function update_table_Gateadresse()
		{
			$metadata = $this->db_boei->metadata('Gateadresse');
//_debug_array($metadata);
			$metadata = $this->db->metadata('boei_gateadresse');
//_debug_array($metadata);
//die();
			if (!$metadata)
			{
				$sql_table = <<<SQL
				CREATE TABLE boei_gateadresse
				(
				  gateadresse_id integer NOT NULL,
				  gatenavn character varying(50),
				  nasjonalid integer,
				  CONSTRAINT boei_gateadresse_pkey PRIMARY KEY (gateadresse_id)
				);
SQL;
				$this->db->query($sql_table, __LINE__, __FILE__);
			}
			$this->db->query('DELETE FROM boei_gateadresse', __LINE__, __FILE__);
			$sql_boei = 'SELECT TOP 100 PERCENT * FROM Gateadresse';
			$this->db_boei->query($sql_boei, __LINE__, __FILE__);
			// using stored prosedures
			$sql = 'INSERT INTO boei_gateadresse (gateadresse_id, gatenavn, nasjonalid)'
				. ' VALUES(?, ?, ?)';
			$valueset = array();

			while ($this->db_boei->next_record())
			{
				$valueset[] = array
					(
					1 => array
						(
						'value' => (int)$this->db_boei->f('Gateadresse_ID'),
						'type' => PDO::PARAM_INT
					),
					2 => array
						(
						'value' => utf8_encode($this->db_boei->f('GateNavn')),
						'type' => PDO::PARAM_STR
					),
					3 => array
						(
						'value' => (int)$this->db_boei->f('NasjonalID'),
						'type' => PDO::PARAM_INT
					)
				);
			}

			$this->db->insert($sql, $valueset, __LINE__, __FILE__);
		}

		function update_table_Objekt()
		{
			$metadata = $this->db_boei->metadata('Objekt');
//_debug_array($metadata);
			$metadata = $this->db->metadata('boei_objekt');
//_debug_array($metadata);
//die();
			if (!$metadata)
			{
				$sql_table = <<<SQL
				CREATE TABLE boei_objekt
				(
					objekt_id character varying(4) NOT NULL,
					navn character varying(50),
					generelladresse character varying(50),
					bydel_id integer,
					postnr_id character varying(4),
					eier_id integer,
					tjenestested integer,
				  CONSTRAINT boei_objekt_pkey PRIMARY KEY (objekt_id)
				);
SQL;
				$this->db->query($sql_table, __LINE__, __FILE__);
			}
			$this->db->query('DELETE FROM boei_objekt', __LINE__, __FILE__);
			$sql_boei = 'SELECT TOP 100 PERCENT * FROM Objekt';
			$this->db_boei->query($sql_boei, __LINE__, __FILE__);
			// using stored prosedures
			$sql = 'INSERT INTO boei_objekt (objekt_id, navn, generelladresse, bydel_id,postnr_id,eier_id,tjenestested)'
				. ' VALUES(?, ?, ?, ?, ?, ?, ?)';
			$valueset = array();

			while ($this->db_boei->next_record())
			{
				$valueset[] = array
					(
					1 => array
						(
						'value' => $this->db_boei->f('Objekt_ID'),
						'type' => PDO::PARAM_STR
					),
					2 => array
						(
						'value' => utf8_encode($this->db_boei->f('Navn')),
						'type' => PDO::PARAM_STR
					),
					3 => array
						(
						'value' => utf8_encode($this->db_boei->f('GenerellAdresse')),
						'type' => PDO::PARAM_STR
					),
					4 => array
						(
						'value' => (int)$this->db_boei->f('Bydel_ID'),
						'type' => PDO::PARAM_INT
					),
					5 => array
						(
						'value' => $this->db_boei->f('Postnr_ID'),
						'type' => PDO::PARAM_STR
					),
					6 => array
						(
						'value' => (int)$this->db_boei->f('Eier_ID'),
						'type' => PDO::PARAM_INT
					),
					7 => array
						(
						'value' => (int)$this->db_boei->f('Tjenestested'),
						'type' => PDO::PARAM_INT
					)
				);
			}

			$this->db->insert($sql, $valueset, __LINE__, __FILE__);
		}

		function update_table_Bygg()
		{
			$metadata = $this->db_boei->metadata('Bygg');
//_debug_array($metadata);
			$metadata = $this->db->metadata('boei_bygg');
//_debug_array($metadata);
//die();
			if (!$metadata)
			{
				$sql_table = <<<SQL
				CREATE TABLE boei_bygg
				(
					objekt_id character varying(4) NOT NULL,
					bygg_id character varying(2) NOT NULL,
					byggnavn character varying(50),
					generelladresse character varying(50),
					driftstatus smallint,
				  CONSTRAINT boei_bygg_pkey PRIMARY KEY (objekt_id, bygg_id)
				);
SQL;
				$this->db->query($sql_table, __LINE__, __FILE__);
			}
			$this->db->query('DELETE FROM boei_bygg', __LINE__, __FILE__);
			$sql_boei = 'SELECT TOP 100 PERCENT * FROM Bygg';
			$this->db_boei->query($sql_boei, __LINE__, __FILE__);
			// using stored prosedures
			$sql = 'INSERT INTO boei_bygg (objekt_id, bygg_id, byggnavn, generelladresse, driftstatus)'
				. ' VALUES(?, ?, ?, ?, ?)';
			$valueset = array();

			while ($this->db_boei->next_record())
			{
				$valueset[] = array
					(
					1 => array
						(
						'value' => $this->db_boei->f('Objekt_ID'),
						'type' => PDO::PARAM_STR
					),
					2 => array
						(
						'value' => $this->db_boei->f('Bygg_ID'),
						'type' => PDO::PARAM_STR
					),
					3 => array
						(
						'value' => utf8_encode($this->db_boei->f('ByggNavn')),
						'type' => PDO::PARAM_STR
					),
					4 => array
						(
						'value' => utf8_encode($this->db_boei->f('GenerellAdresse')),
						'type' => PDO::PARAM_STR
					),
					5 => array
						(
						'value' => (int)$this->db_boei->f('Driftstatus'),
						'type' => PDO::PARAM_INT
					),
				);
			}

			$this->db->insert($sql, $valueset, __LINE__, __FILE__);
		}

		function update_table_Seksjon()
		{
			$metadata = $this->db_boei->metadata('Seksjon');
//_debug_array($metadata);
			$metadata = $this->db->metadata('boei_seksjon');
//_debug_array($metadata);
//die();
			if (!$metadata)
			{
				$sql_table = <<<SQL
				CREATE TABLE boei_seksjon
				(
					objekt_id character varying(4) NOT NULL,
					bygg_id character varying(2) NOT NULL,
					seksjons_id character varying(2) NOT NULL,
					beskrivelse character varying(35),
				  CONSTRAINT boei_seksjon_pkey PRIMARY KEY (objekt_id, bygg_id, seksjons_id)
				);
SQL;
				$this->db->query($sql_table, __LINE__, __FILE__);
			}
			$this->db->query('DELETE FROM boei_seksjon', __LINE__, __FILE__);
			$sql_boei = 'SELECT TOP 100 PERCENT * FROM Seksjon';
			$this->db_boei->query($sql_boei, __LINE__, __FILE__);
			// using stored prosedures
			$sql = 'INSERT INTO boei_seksjon (objekt_id, bygg_id, seksjons_id, beskrivelse)'
				. ' VALUES(?, ?, ?, ?)';
			$valueset = array();

			while ($this->db_boei->next_record())
			{
				$valueset[] = array
					(
					1 => array
						(
						'value' => $this->db_boei->f('Objekt_ID'),
						'type' => PDO::PARAM_STR
					),
					2 => array
						(
						'value' => $this->db_boei->f('Bygg_ID'),
						'type' => PDO::PARAM_STR
					),
					3 => array
						(
						'value' => $this->db_boei->f('Seksjons_ID'),
						'type' => PDO::PARAM_STR
					),
					4 => array
						(
						'value' => utf8_encode($this->db_boei->f('Beskrivelse')),
						'type' => PDO::PARAM_STR
					)
				);
			}

			$this->db->insert($sql, $valueset, __LINE__, __FILE__);
		}

		function update_table_leieobjekt()
		{
			$metadata = $this->db_boei->metadata('Leieobjekt');
//_debug_array($metadata);
			$metadata = $this->db->metadata('boei_leieobjekt');
//_debug_array($metadata);
//die();
			if (!$metadata)
			{
				$sql_table = <<<SQL
				CREATE TABLE boei_leieobjekt
				(
					objekt_id character varying(4) NOT NULL,
					bygg_id character varying(2) NOT NULL,
					seksjons_id character varying(2) NOT NULL,
					leie_id character varying(3) NOT NULL,
					flyttenr smallint,
					formaal_id smallint,
					gateadresse_id integer,
					gatenr character varying(30),
					etasje character varying(5),
					antallrom smallint,
					boareal integer,
					andelavfellesareal smallint,
					livslopsstd smallint,
					heis smallint,
					driftsstatus_id smallint,
					leietaker_id integer,
					beregnet_boa numeric(20,2),

				  CONSTRAINT boei_leieobjekt_pkey PRIMARY KEY (objekt_id, bygg_id, seksjons_id, leie_id)
				);
SQL;
				$this->db->query($sql_table, __LINE__, __FILE__);
			}
			$this->db->query('DELETE FROM boei_leieobjekt', __LINE__, __FILE__);
			$sql_boei = 'SELECT TOP 100 PERCENT * FROM Leieobjekt';
			$this->db_boei->query($sql_boei, __LINE__, __FILE__);
			// using stored prosedures
			$sql = 'INSERT INTO boei_leieobjekt (objekt_id, bygg_id, seksjons_id, leie_id, flyttenr, formaal_id, gateadresse_id, gatenr, etasje, antallrom, boareal, andelavfellesareal,livslopsstd, heis, driftsstatus_id, leietaker_id,beregnet_boa)'
				. ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$valueset = array();

			while ($this->db_boei->next_record())
			{
				$valueset[] = array
					(
					1 => array
						(
						'value' => $this->db_boei->f('Objekt_ID'),
						'type' => PDO::PARAM_STR
					),
					2 => array
						(
						'value' => $this->db_boei->f('Bygg_ID'),
						'type' => PDO::PARAM_STR
					),
					3 => array
						(
						'value' => $this->db_boei->f('Seksjons_ID'),
						'type' => PDO::PARAM_STR
					),
					4 => array
						(
						'value' => $this->db_boei->f('Leie_ID'),
						'type' => PDO::PARAM_STR
					),
					5 => array
						(
						'value' => (int)$this->db_boei->f('Flyttenr'),
						'type' => PDO::PARAM_INT
					),
					6 => array
						(
						'value' => (int)$this->db_boei->f('Formaal_ID'),
						'type' => PDO::PARAM_INT
					),
					7 => array
						(
						'value' => (int)$this->db_boei->f('Gateadresse_ID'),
						'type' => PDO::PARAM_INT
					),
					8 => array
						(
						'value' => utf8_encode($this->db_boei->f('Gatenr')),
						'type' => PDO::PARAM_STR
					),
					9 => array
						(
						'value' => $this->db_boei->f('Etasje'),
						'type' => PDO::PARAM_STR
					),
					10 => array
						(
						'value' => (int)$this->db_boei->f('AntallRom'),
						'type' => PDO::PARAM_INT
					),
					11 => array
						(
						'value' => (int)$this->db_boei->f('Boareal'),
						'type' => PDO::PARAM_INT
					),
					12 => array
						(
						'value' => (int)$this->db_boei->f('AndelAvFellesareal'),
						'type' => PDO::PARAM_INT
					),
					13 => array
						(
						'value' => (int)$this->db_boei->f('Livslopsstd'),
						'type' => PDO::PARAM_INT
					),
					14 => array
						(
						'value' => (int)$this->db_boei->f('Heis'),
						'type' => PDO::PARAM_INT
					),
					15 => array
						(
						'value' => (int)$this->db_boei->f('Driftsstatus_ID'),
						'type' => PDO::PARAM_INT
					),
					16 => array
						(
						'value' => (int)$this->db_boei->f('Leietaker_ID'),
						'type' => PDO::PARAM_INT
					),
					17 => array
						(
						'value' => (float)$this->db_boei->f('Beregnet_Boa'),
						'type' => PDO::PARAM_STR
					)
				);
			}

			$this->db->insert($sql, $valueset, __LINE__, __FILE__);
		}

		function update_table_leietaker()
		{
			$metadata = $this->db_boei->metadata('Leietaker');
//_debug_array($metadata);
			$metadata = $this->db->metadata('boei_leietaker');
//_debug_array($metadata);
//die();
			if (!$metadata)
			{
				$sql_table = <<<SQL
				CREATE TABLE boei_leietaker
				(
					leietaker_id integer NOT NULL,
					fornavn character varying(30),
					etternavn character varying(30),
					kjonn_juridisk smallint,
					oppsagtdato character varying(10),
					namssakstatusdrift_id smallint,
					namssakstatusokonomi_id smallint,
					hemmeligadresse smallint,
					obskode character varying(12),
					CONSTRAINT boei_leietaker_pkey PRIMARY KEY (leietaker_id)
				);
SQL;
				$this->db->query($sql_table, __LINE__, __FILE__);
			}
			$this->db->query('DELETE FROM boei_leietaker', __LINE__, __FILE__);
			$sql_boei = 'SELECT TOP 100 PERCENT * FROM Leietaker';
			$this->db_boei->query($sql_boei, __LINE__, __FILE__);
			// using stored prosedures
			$sql = 'INSERT INTO boei_leietaker (leietaker_id, fornavn, etternavn, kjonn_juridisk,oppsagtdato,namssakstatusdrift_id,namssakstatusokonomi_id,hemmeligadresse,obskode)'
				. ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$valueset = array();

			while ($this->db_boei->next_record())
			{
				$valueset[] = array
					(
					1 => array
						(
						'value' => (int)$this->db_boei->f('Leietaker_ID'),
						'type' => PDO::PARAM_INT
					),
					2 => array
						(
						'value' => utf8_encode($this->db_boei->f('Fornavn')),
						'type' => PDO::PARAM_STR
					),
					3 => array
						(
						'value' => utf8_encode($this->db_boei->f('Etternavn')),
						'type' => PDO::PARAM_STR
					),
					4 => array
						(
						'value' => (int)$this->db_boei->f('Kjonn_Juridisk'),
						'type' => PDO::PARAM_INT
					),
					5 => array
						(
						'value' => $this->db_boei->f('OppsagtDato'),
						'type' => PDO::PARAM_STR
					),
					6 => array
						(
						'value' => (int)$this->db_boei->f('NamssakStatusDrift_ID'),
						'type' => PDO::PARAM_INT
					),
					7 => array
						(
						'value' => (int)$this->db_boei->f('NamssakStatusOkonomi_ID'),
						'type' => PDO::PARAM_INT
					),
					8 => array
						(
						'value' => (int)$this->db_boei->f('hemmeligAdresse'),
						'type' => PDO::PARAM_INT
					),
					9 => array
						(
						'value' => utf8_encode($this->db_boei->f('OBSKode')),
						'type' => PDO::PARAM_STR
					)
				);
			}
			$this->db->insert($sql, $valueset, __LINE__, __FILE__);
		}

		function update_table_reskontro()
		{
			$metadata = $this->db_boei->metadata('reskontro');
//_debug_array($metadata);
			$metadata = $this->db->metadata('boei_reskontro');
//_debug_array($metadata);
//die();
			if (!$metadata)
			{
				$sql_table = <<<SQL
				CREATE TABLE boei_reskontro
				(
					objekt_id character varying(4) NOT NULL,
					leie_id character varying(3) NOT NULL,
					flyttenr smallint,
					leietaker_id integer NOT NULL,
					innflyttetdato character varying(10),
					CONSTRAINT boei_reskontro_pkey PRIMARY KEY (objekt_id,leie_id,flyttenr)
				);
SQL;
				$this->db->query($sql_table, __LINE__, __FILE__);
			}
			$this->db->query('DELETE FROM boei_reskontro', __LINE__, __FILE__);
			$sql_boei = 'SELECT TOP 100 PERCENT * FROM reskontro';
			$this->db_boei->query($sql_boei, __LINE__, __FILE__);
			// using stored prosedures
			$sql = 'INSERT INTO boei_reskontro (objekt_id,leie_id,flyttenr,leietaker_id, innflyttetdato )'
				. ' VALUES(?, ?, ?, ?, ?)';
			$valueset = array();

			while ($this->db_boei->next_record())
			{
				$valueset[] = array
					(
					1 => array
						(
						'value' => $this->db_boei->f('Objekt_ID'),
						'type' => PDO::PARAM_STR
					),
					2 => array
						(
						'value' => $this->db_boei->f('Leie_ID'),
						'type' => PDO::PARAM_STR
					),
					3 => array
						(
						'value' => (int)$this->db_boei->f('Flyttenr'),
						'type' => PDO::PARAM_INT
					),
					4 => array
						(
						'value' => (int)$this->db_boei->f('Leietaker_ID'),
						'type' => PDO::PARAM_INT
					),
					5 => array
						(
						'value' => $this->db_boei->f('InnflyttetDato'),
						'type' => PDO::PARAM_STR
					)
				);
			}

			$this->db->insert($sql, $valueset, __LINE__, __FILE__);
		}

		function legg_til_eier_phpgw()
		{
			$sql = " SELECT boei_eier.eier_id as id, boei_eier.eiertype_id as category"
				. " FROM boei_eier";

			$this->db->query($sql, __LINE__, __FILE__);
			$owners = array();
			while ($this->db->next_record())
			{
				$category = $this->db->f('category');
				$owners[] = array
					(
					'id' => (int)$this->db->f('id'),
					'category' => $category == 0 ? 4 : $category
				);
			}
			$this->db->transaction_begin();

			foreach ($owners as $owner)
			{
				$sql2 = "UPDATE fm_owner set category = '{$owner['category']}' WHERE id = '{$owner['id']}'";

				$this->db->query($sql2, __LINE__, __FILE__);
			}

			unset($owner);
			$owners = array();

			$sql = "SELECT boei_eier.eier_id, boei_eier.navn as org_name,boei_eier.eiertype_id as category FROM  fm_owner RIGHT OUTER JOIN "
				. " boei_eier ON fm_owner.id = boei_eier.eier_id"
				. " WHERE (fm_owner.id IS NULL)";

			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$category = $this->db->f('category');

				$owners[] = array
					(
					'id' => $this->db->f('eier_id'),
					'org_name' => $this->db->f('org_name'),
					'remark' => $this->db->f('org_name'),
					'category' => $category == 0 ? 4 : $category,
					'entry_date' => time(),
					'owner_id' => 6
				);
			}

			foreach ($owners as $owner)
			{

				$sql2 = "INSERT INTO fm_owner (id,org_name,remark,category,entry_date,owner_id)"
					. "VALUES (" . $this->db->validate_insert($owner) . ")";

				$this->db->query($sql2, __LINE__, __FILE__);

				$owner_msg[] = $owner['org_name'];
			}

			$this->db->transaction_commit();

			$msg = count($owners) . ' eier er lagt til: ' . @implode(",", $owner_msg);
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function legg_til_gateadresse_phpgw()
		{
			//legg til
			$sql = "SELECT boei_gateadresse.gateadresse_id, boei_gateadresse.gatenavn FROM fm_streetaddress RIGHT OUTER JOIN "
				. " boei_gateadresse ON fm_streetaddress.id = boei_gateadresse.gateadresse_id"
				. " WHERE (fm_streetaddress.id IS NULL)";

			$this->db->query($sql, __LINE__, __FILE__);
			$gater = array();
			while ($this->db->next_record())
			{
				$gater[] = array
					(
					'id' => (int)$this->db->f('gateadresse_id'),
					'descr' => $this->db->f('gatenavn')
				);
			}
			$this->db->transaction_begin();

			foreach ($gater as $gate)
			{
				$sql2 = "INSERT INTO fm_streetaddress (id,descr)"
					. " VALUES ({$gate['id']}, '{$gate['descr']}')";

				$this->db->query($sql2, __LINE__, __FILE__);
				$gate_msg[] = $gate['descr'];
			}


			//oppdater gatenavn - om det er endret

			$sql = "SELECT boei_gateadresse.gateadresse_id, boei_gateadresse.gatenavn FROM boei_gateadresse";

			$this->db->query($sql, __LINE__, __FILE__);

			$msg = count($gate) . ' gateadresser er lagt til: ' . @implode(",", $gate_msg);

			$gate = array();
			while ($this->db->next_record())
			{
				$gate[] = array
					(
					'id' => (int)$this->db->f('gateadresse_id'),
					'descr' => $this->db->f('gatenavn')
				);
			}

			foreach ($gate as $gate_info)
			{
				$sql_utf = "UPDATE fm_streetaddress SET descr = '{$gate_info['descr']}' WHERE id = " . (int)$gate_info['id'];
				$this->db->query($sql_utf, __LINE__, __FILE__);
			}

			$this->db->transaction_commit();

			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function legg_til_objekt_phpgw()
		{
			$sql = "SELECT boei_objekt.objekt_id, boei_objekt.navn, boei_objekt.bydel_id, boei_objekt.eier_id,boei_objekt.tjenestested"
				. " FROM fm_location1 RIGHT OUTER JOIN "
				. " boei_objekt ON fm_location1.loc1 = boei_objekt.objekt_id"
				. " WHERE fm_location1.loc1 IS NULL";

			$this->db->query($sql, __LINE__, __FILE__);
			$objekt_latin = array();
			while ($this->db->next_record())
			{
				$objekt_latin[] = array
					(
					'location_code' => $this->db->f('objekt_id'),
					'loc1' => $this->db->f('objekt_id'),
					'loc1_name' => $this->db->f('navn'),
					'part_of_town_id' => $this->db->f('bydel_id'),
					'owner_id' => $this->db->f('eier_id'),
					'kostra_id' => $this->db->f('tjenestested'),
					'category' => 1
				);
			}

			$this->db->transaction_begin();

			foreach ($objekt_latin as $objekt)
			{

				$sql2 = "INSERT INTO fm_location1 (location_code, loc1, loc1_name, part_of_town_id, owner_id, kostra_id,category) "
					. "VALUES (" . $this->db->validate_insert($objekt) . ")";

				$this->db->query($sql2, __LINE__, __FILE__);
				$this->db->query("INSERT INTO fm_locations (level, location_code, loc1) VALUES (1, '{$objekt['location_code']}', '{$objekt['loc1']}')", __LINE__, __FILE__);

				$obj_msg[] = $objekt['loc1'];
			}

			$this->db->transaction_commit();

			$msg = count($objekt_latin) . ' Objekt er lagt til: ' . @implode(",", $obj_msg);
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function legg_til_bygg_phpgw()
		{
			$sql = "SELECT boei_bygg.objekt_id || '-' || boei_bygg.bygg_id AS location_code, boei_bygg.objekt_id, boei_bygg.bygg_id, boei_bygg.byggnavn,boei_bygg.driftstatus"
				. " FROM boei_bygg LEFT OUTER JOIN"
				. " fm_location2 ON boei_bygg.objekt_id = fm_location2.loc1 AND boei_bygg.bygg_id = fm_location2.loc2"
				. " WHERE fm_location2.loc1 IS NULL";

			$this->db->query($sql, __LINE__, __FILE__);
			$bygg_latin = array();
			while ($this->db->next_record())
			{
				$bygg_latin[] = array
					(
					'location_code' => $this->db->f('location_code'),
					'loc1' => $this->db->f('objekt_id'),
					'loc2' => $this->db->f('bygg_id'),
					'loc2_name' => $this->db->f('byggnavn'),
					'category' => 98
				);
			}

			$this->db->transaction_begin();

			foreach ($bygg_latin as $bygg)
			{

				$sql2 = "INSERT INTO fm_location2 (location_code, loc1, loc2, loc2_name,category) "
					. "VALUES (" . $this->db->validate_insert($bygg) . ")";

				$this->db->query($sql2, __LINE__, __FILE__);
				$this->db->query("INSERT INTO fm_locations (level, location_code, loc1) VALUES (2, '{$bygg['location_code']}', '{$bygg['loc1']}')", __LINE__, __FILE__);

				$bygg_msg[] = $bygg['location_code'];
			}

			$this->db->transaction_commit();

			$msg = count($bygg_latin) . ' Bygg er lagt til: ' . @implode(",", $bygg_msg);
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function legg_til_seksjon_phpgw()
		{

			$sql = "SELECT boei_seksjon.objekt_id || '-' || boei_seksjon.bygg_id || '-' || boei_seksjon.seksjons_id AS location_code, boei_seksjon.objekt_id, boei_seksjon.bygg_id,"
				. " boei_seksjon.seksjons_id, boei_seksjon.beskrivelse"
				. " FROM boei_seksjon LEFT OUTER JOIN"
				. " fm_location3 ON boei_seksjon.objekt_id = fm_location3.loc1 AND boei_seksjon.bygg_id = fm_location3.loc2 AND "
				. " boei_seksjon.seksjons_id = fm_location3.loc3"
				. " WHERE fm_location3.loc1 IS NULL";

			$this->db->query($sql, __LINE__, __FILE__);
			$seksjon_latin = array();
			while ($this->db->next_record())
			{
				$seksjon_latin[] = array(
					'location_code' => $this->db->f('location_code'),
					'loc1' => $this->db->f('objekt_id'),
					'loc2' => $this->db->f('bygg_id'),
					'loc3' => $this->db->f('seksjons_id'),
					'loc3_name' => $this->db->f('beskrivelse'),
					'category' => 98
				);
			}

			$this->db->transaction_begin();

			foreach ($seksjon_latin as $seksjon)
			{
				$sql2 = "INSERT INTO fm_location3 (location_code, loc1, loc2, loc3, loc3_name, category) "
					. "VALUES (" . $this->db->validate_insert($seksjon) . ")";

				$this->db->query($sql2, __LINE__, __FILE__);
				$this->db->query("INSERT INTO fm_locations (level, location_code, loc1) VALUES (3, '{$seksjon['location_code']}', '{$seksjon['loc1']}')", __LINE__, __FILE__);

				$seksjon_msg[] = $seksjon['location_code'];
			}

			$this->db->transaction_commit();

			$msg = count($seksjon_latin) . ' Seksjon er lagt til: ' . @implode(",", $seksjon_msg);
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function legg_til_leieobjekt_phpgw()
		{
			$sql = "SELECT boei_leieobjekt.objekt_id || '-' || boei_leieobjekt.bygg_id || '-' || boei_leieobjekt.seksjons_id || '-' || boei_leieobjekt.leie_id AS location_code,"
				. " boei_leieobjekt.objekt_id, boei_leieobjekt.leie_id, boei_leieobjekt.bygg_id, boei_leieobjekt.seksjons_id,"
				. " boei_leieobjekt.formaal_id, boei_leieobjekt.gateadresse_id, boei_leieobjekt.gatenr, boei_leieobjekt.etasje, boei_leieobjekt.antallrom,"
				. " boei_leieobjekt.boareal, boei_leieobjekt.livslopsstd, boei_leieobjekt.heis, boei_leieobjekt.driftsstatus_id, boei_leieobjekt.leietaker_id,"
				. " boei_leieobjekt.beregnet_boa, boei_leieobjekt.flyttenr"
				. " FROM boei_leieobjekt LEFT OUTER JOIN"
				. " fm_location4 ON boei_leieobjekt.objekt_id = fm_location4.loc1 AND boei_leieobjekt.leie_id = fm_location4.loc4"
				. " WHERE fm_location4.loc1 IS NULL";


			$this->db->query($sql, __LINE__, __FILE__);

			$leieobjekt_latin = array();

			while ($this->db->next_record())
			{
				$leieobjekt_latin[] = array
					(
					'location_code' => $this->db->f('location_code'),
					'loc1' => $this->db->f('objekt_id'),
					'loc4' => $this->db->f('leie_id'),
					'loc2' => $this->db->f('bygg_id'),
					'loc3' => $this->db->f('seksjons_id'),
					'category' => $this->db->f('formaal_id'),
					'street_id' => $this->db->f('gateadresse_id'),
					'street_number' => $this->db->f('gatenr'),
					'etasje' => $this->db->f('etasje'),
					'antallrom' => $this->db->f('antallrom'),
					'boareal' => $this->db->f('boareal'),
					'livslopsstd' => $this->db->f('livslopsstd'),
					'heis' => $this->db->f('heis'),
					'driftsstatus_id' => $this->db->f('driftsstatus_id'),
					'tenant_id' => $this->db->f('leietaker_id'),
					'beregnet_boa' => $this->db->f('beregnet_boa'),
					'flyttenr' => $this->db->f('flyttenr')
				);
			}

			$this->db->transaction_begin();

			foreach ($leieobjekt_latin as $leieobjekt)
			{
				$sql2 = "INSERT INTO fm_location4 (location_code, loc1, loc4, loc2, loc3, category, street_id, street_number, etasje, antallrom, boareal, livslopsstd, heis, driftsstatus_id,
                      tenant_id, beregnet_boa, flyttenr)"
					. "VALUES (" . $this->db->validate_insert($leieobjekt) . ")";

				$this->db->query($sql2, __LINE__, __FILE__);
				$this->db->query("INSERT INTO fm_locations (level, location_code, loc1) VALUES (4, '{$leieobjekt['location_code']}', '{$leieobjekt['loc1']}')", __LINE__, __FILE__);

				$leieobjekt_msg[] = $leieobjekt['location_code'];
			}

			$this->db->transaction_commit();

			$msg = count($leieobjekt_latin) . ' Leieobjekt er lagt til: ' . @implode(",", $leieobjekt_msg);
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function legg_til_leietaker_phpgw()
		{
			$sql = " SELECT boei_leietaker.leietaker_id, boei_leietaker.fornavn, boei_leietaker.etternavn, boei_leietaker.kjonn_juridisk,"
				. " boei_leietaker.namssakstatusokonomi_id, boei_leietaker.namssakstatusdrift_id, boei_leietaker.obskode"
				. " FROM fm_tenant RIGHT OUTER JOIN"
				. " boei_leietaker ON fm_tenant.id = boei_leietaker.leietaker_id"
				. " WHERE fm_tenant.id IS NULL";

			$this->db->query($sql, __LINE__, __FILE__);

			$leietakere = array();

			while ($this->db->next_record())
			{
				$leietakere[] = array
					(
					'id' => $this->db->f('leietaker_id'),
					'first_name' => $this->db->f('fornavn'),
					'last_name' => $this->db->f('etternavn'),
					'category' => $this->db->f('kjonn_juridisk') + 1,
					'status_eco' => $this->db->f('namssakstatusokonomi_id'),
					'status_drift' => $this->db->f('namssakstatusdrift_id'),
					'obskode' => $this->db->f('obskode'),
					'entry_date' => time(),
					'owner_id' => 6
				);
			}

			$this->db->transaction_begin();

			foreach ($leietakere as $leietaker)
			{
				$sql2 = "INSERT INTO fm_tenant (id, first_name, last_name, category, status_eco, status_drift, obskode, entry_date,owner_id)"
					. "VALUES (" . $this->db->validate_insert($leietaker) . ")";

				$this->db->query($sql2, __LINE__, __FILE__);

				$leietaker_msg[] = "[{$leietaker['last_name']}, '{$leietaker['first_name']}']";
			}

			$this->db->transaction_commit();

			$msg = count($leietakere) . ' Leietaker er lagt til: ' . @implode(",", $leietaker_msg);
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function update_tenant_name()
		{
			$sql = "SELECT boei_leietaker.leietaker_id, boei_leietaker.fornavn, boei_leietaker.etternavn FROM boei_leietaker"
				. " JOIN fm_tenant ON boei_leietaker.leietaker_id = fm_tenant.id"
				. " WHERE first_name != fornavn OR last_name != etternavn";
			$this->db->query($sql, __LINE__, __FILE__);

			$i = 0;
			while ($this->db->next_record())
			{
				$sql2 = "UPDATE fm_tenant SET"
					. " first_name = '" . $this->db->f('fornavn') . "',"
					. " last_name = '" . $this->db->f('etternavn') . "'"
					. " WHERE id = " . (int)$this->db->f('leietaker_id');
//_debug_array($sql2);
				$this->db2->query($sql2, __LINE__, __FILE__);
				$i++;
			}

			$msg = $i . ' Leietakere er oppdatert';
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function update_obskode()
		{
			$sql = "SELECT DISTINCT boei_leietaker.leietaker_id as tenant_id, boei_leietaker.obskode FROM boei_leietaker"
				. " JOIN fm_location4 ON boei_leietaker.leietaker_id = fm_location4.tenant_id"
				. " WHERE fm_location4.tenant_id > 0 AND (boei_leietaker.obskode != fm_location4.obskode OR"
				. " (boei_leietaker.obskode IS NULL AND fm_location4.obskode IS NOT NULL) OR"
				. " (boei_leietaker.obskode IS NOT NULL AND fm_location4.obskode IS NULL))";

			$this->db->query($sql, __LINE__, __FILE__);

			$obskoder = array();
			while ($this->db->next_record())
			{
				$obskoder[] = array
					(
					'tenant_id' => (int)$this->db->f('tenant_id'),
					'obskode' => $this->db->f('obskode')
				);
			}
			foreach ($obskoder as $entry)
			{
				$sql2 = "UPDATE fm_location4 SET obskode = '{$entry['obskode']}'"
					. " WHERE tenant_id = {$entry['tenant_id']}";

				$this->db2->query($sql2, __LINE__, __FILE__);
			}

			$msg = count($obskoder) . ' OBSKoder er oppdatert';
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function oppdater_leieobjekt()
		{
			$sql = "SELECT boei_leieobjekt.objekt_id,boei_leieobjekt.leie_id,boei_leieobjekt.leietaker_id, boareal, formaal_id, gateadresse_id, gatenr, etasje,driftsstatus_id, boei_leieobjekt.flyttenr, innflyttetdato"
				. " FROM  boei_leieobjekt LEFT JOIN boei_reskontro ON boei_leieobjekt.objekt_id=boei_reskontro.objekt_id AND boei_leieobjekt.leie_id=boei_reskontro.leie_id"
				. " AND boei_leieobjekt.flyttenr=boei_reskontro.flyttenr AND boei_leieobjekt.leietaker_id=boei_reskontro.leietaker_id";

			$this->db->query($sql, __LINE__, __FILE__);

			$this->db->transaction_begin();


			$i = 0;
			while ($this->db->next_record())
			{
				$sql2 = " UPDATE  fm_location4 SET "
					. " tenant_id = '" . $this->db->f('leietaker_id') . "',"
					. " category = '" . $this->db->f('formaal_id') . "',"
					. " etasje = '" . $this->db->f('etasje') . "',"
					. " street_id = '" . $this->db->f('gateadresse_id') . "',"
					. " street_number = '" . $this->db->f('gatenr') . "',"
					. " driftsstatus_id = '" . $this->db->f('driftsstatus_id') . "',"
					. " boareal = '" . $this->db->f('boareal') . "',"
					. " flyttenr = '" . $this->db->f('flyttenr') . "',"
					. " innflyttetdato = '" . date("M d Y", strtotime($this->db->f('innflyttetdato'))) . "'"
					. " WHERE  loc1 = '" . $this->db->f('objekt_id') . "'  AND  loc4= '" . $this->db->f('leie_id') . "'";

				$this->db2->query($sql2, __LINE__, __FILE__);
				$i++;
			}

			$this->db->transaction_commit();

			$msg = $i . ' Leieobjekt er oppdatert';
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function oppdater_boa_objekt()
		{
			$sql = " SELECT boei_objekt.objekt_id,bydel_id,tjenestested,navn,boei_objekt.eier_id"
				. " FROM boei_objekt JOIN fm_location1 ON boei_objekt.objekt_id = fm_location1.loc1"
				. " WHERE boei_objekt.navn != fm_location1.loc1_name"
				. " OR  boei_objekt.bydel_id != fm_location1.part_of_town_id"
				. " OR  boei_objekt.eier_id != fm_location1.owner_id"
				. " OR  boei_objekt.tjenestested != fm_location1.kostra_id";
			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$sql2 = " UPDATE fm_location1 SET "
					. " loc1_name = '" . $this->db->f('navn') . "',"
					. " part_of_town_id = " . (int)$this->db->f('bydel_id') . ","
					. " owner_id = " . (int)$this->db->f('eier_id') . ","
					. " kostra_id = " . (int)$this->db->f('tjenestested')
					. " WHERE  loc1 = '" . $this->db->f('objekt_id') . "'";

				$this->db2->query($sql2, __LINE__, __FILE__);
			}

			$sql = " SELECT sum(boei_leieobjekt.boareal) as sum_boa, count(leie_id) as ant_leieobjekt,"
				. " boei_objekt.objekt_id FROM  boei_objekt {$this->join} boei_leieobjekt ON boei_objekt.objekt_id = boei_leieobjekt.objekt_id"
				. " WHERE boei_leieobjekt.formaal_id NOT IN (99)"
				. " GROUP BY boei_objekt.objekt_id";

			$this->db->query($sql, __LINE__, __FILE__);

			//	$this->db->transaction_begin();

			$i = 0;
			while ($this->db->next_record())
			{
				$sql2 = " UPDATE fm_location1 SET "
					. " sum_boa = '" . $this->db->f('sum_boa') . "',"
					. " ant_leieobjekt = " . (int)$this->db->f('ant_leieobjekt')
					. " WHERE  loc1 = '" . $this->db->f('objekt_id') . "'";
				$this->db2->query($sql2, __LINE__, __FILE__);
				$i++;
			}
			//	$this->db->transaction_commit();

			$msg = $i . ' Objekt er oppdatert';
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function oppdater_boa_bygg()
		{
			$sql = " SELECT sum(boei_leieobjekt.boareal) as sum_boa, count(leie_id) as ant_leieobjekt,"
				. " boei_bygg.objekt_id,boei_bygg.bygg_id , byggnavn  FROM  boei_bygg $this->join boei_leieobjekt "
				. " ON boei_bygg.objekt_id = boei_leieobjekt.objekt_id AND boei_bygg.bygg_id = boei_leieobjekt.bygg_id"
				. " WHERE boei_leieobjekt.formaal_id NOT IN (99)"
				. " GROUP BY boei_bygg.objekt_id,boei_bygg.bygg_id ,byggnavn";

			$this->db->query($sql, __LINE__, __FILE__);

			//	$this->db->transaction_begin();

			$i = 0;
			while ($this->db->next_record())
			{
				$sql2 = " UPDATE fm_location2 SET "
					. " loc2_name = '" . $this->db->f('byggnavn') . "',"
					. " sum_boa = '" . $this->db->f('sum_boa') . "',"
					. " ant_leieobjekt = '" . $this->db->f('ant_leieobjekt') . "'"
					. " WHERE  loc1 = '" . $this->db->f('objekt_id') . "'  AND  loc2= '" . $this->db->f('bygg_id') . "'";

				$this->db2->query($sql2, __LINE__, __FILE__);
				$i++;
			}
			//	$this->db->transaction_commit();

			$msg = $i . ' Bygg er oppdatert';
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function oppdater_boa_del()
		{
			$sql = " SELECT sum(boei_leieobjekt.boareal) as sum_boa, count(leie_id) as ant_leieobjekt,"
				. " boei_seksjon.objekt_id,boei_seksjon.bygg_id,boei_seksjon.seksjons_id , beskrivelse   FROM  boei_seksjon $this->join boei_leieobjekt "
				. " ON boei_seksjon.objekt_id = boei_leieobjekt.objekt_id"
				. " AND boei_seksjon.bygg_id = boei_leieobjekt.bygg_id"
				. " AND boei_seksjon.seksjons_id = boei_leieobjekt.seksjons_id"
				. " WHERE boei_leieobjekt.formaal_id NOT IN (99)"
				. " GROUP BY boei_seksjon.objekt_id,boei_seksjon.bygg_id,boei_seksjon.seksjons_id,beskrivelse";

			$this->db->query($sql, __LINE__, __FILE__);

			$i = 0;

			//	$this->db->transaction_begin();

			while ($this->db->next_record())
			{
				$sql2 = "UPDATE fm_location3 SET "
					. " loc3_name = '" . $this->db->f('beskrivelse') . "',"
					. " sum_boa = '" . $this->db->f('sum_boa') . "',"
					. " ant_leieobjekt = '" . $this->db->f('ant_leieobjekt') . "'"
					. " WHERE  loc1 = '" . $this->db->f('objekt_id') . "'  AND  loc2= '" . $this->db->f('bygg_id') . "'  AND  loc3= '" . $this->db->f('seksjons_id') . "'";

				$this->db2->query($sql2, __LINE__, __FILE__);
				$i++;
			}
			//	$this->db->transaction_commit();

			$msg = $i . ' Seksjoner er oppdatert';
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function oppdater_oppsagtdato()
		{
			$sql = "SELECT fm_tenant.id,boei_leietaker.oppsagtdato"
				. " FROM  fm_tenant LEFT OUTER JOIN"
				. " boei_leietaker ON fm_tenant.id = boei_leietaker.leietaker_id AND "
				. " fm_tenant.oppsagtdato = boei_leietaker.oppsagtdato"
				. " WHERE (boei_leietaker.leietaker_id IS NULL)";

			$this->db->query($sql, __LINE__, __FILE__);

			//		$this->db->transaction_begin();

			while ($this->db->next_record())
			{
				$sql2 = "UPDATE fm_tenant SET "
					. " oppsagtdato = '" . $this->db->f('oppsagtdato') . "'"
					. " WHERE  id = " . (int)$this->db->f('id');

				$this->db2->query($sql, __LINE__, __FILE__);
			}
			//	$this->db->transaction_commit();

			$msg = $i . ' oppsagtdato er oppdatert';
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function slett_feil_telefon()
		{
			$sql = "SELECT count(contact_phone) as ant_tlf from fm_tenant WHERE id > 99999 OR id = 0";

			$this->db->query($sql, __LINE__, __FILE__);

			$this->db->next_record();

			$ant_tlf = $this->db->f('ant_tlf');

			$sql = "UPDATE fm_tenant SET contact_phone = NULL WHERE id > 99999 OR id = 0";

			$this->db->query($sql, __LINE__, __FILE__);

			$msg = $ant_tlf . ' Telefon nr er slettet';
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}

		function oppdater_namssakstatus_pr_leietaker()
		{

			$sql = "SELECT fm_tenant.id"
				. " FROM  fm_tenant LEFT OUTER JOIN"
				. " boei_leietaker ON fm_tenant.id = boei_leietaker.leietaker_id AND "
				. " fm_tenant.status_drift = boei_leietaker.namssakstatusdrift_id AND "
				. " fm_tenant.status_eco = boei_leietaker.namssakstatusokonomi_id"
				. " WHERE (boei_leietaker.leietaker_id IS NULL)";

			$this->db->query($sql, __LINE__, __FILE__);

			$this->db->transaction_begin();

			while ($this->db->next_record())
			{
				$leietaker[] = (int)$this->db->f('id');
			}

			for ($i = 0; $i < count($leietaker); $i++)
			{
				$sql = "SELECT namssakstatusokonomi_id, namssakstatusdrift_id"
					. " FROM  boei_leietaker"
					. " WHERE (boei_leietaker.leietaker_id = '" . $leietaker[$i] . "')";

				$this->db->query($sql, __LINE__, __FILE__);

				$this->db->next_record();
				$leietaker_oppdatert[] = array(
					'id' => (int)$leietaker[$i],
					'status_drift' => (int)$this->db->f('namssakstatusdrift_id'),
					'status_eco' => (int)$this->db->f('namssakstatusokonomi_id')
				);
			}

			for ($i = 0; $i < count($leietaker_oppdatert); $i++)
			{
				$sql = " UPDATE fm_tenant SET "
					. " status_eco = '" . $leietaker_oppdatert[$i]['status_eco'] . "',"
					. " status_drift = '" . $leietaker_oppdatert[$i]['status_drift'] . "'"
					. " WHERE  id = '" . $leietaker_oppdatert[$i]['id'] . "'";

				$this->db->query($sql, __LINE__, __FILE__);
			}

			$this->db->transaction_commit();

			$msg = $i . ' namssakstatus er oppdatert';
			$this->receipt['message'][] = array('msg' => $msg);
			$this->cron_log($msg);
		}
	}
