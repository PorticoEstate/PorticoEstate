<?php

	require_once PHPGW_API_INC . '/soap_client/bra5/Bra5Autoload.php';

	class import_document_files
	{

		protected $config,
			$secKey,
			$fields,
			$baseclassname,
			$classname,
			$input_file,
			$file_map,
			$all_files;

		public function __construct()
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id('admin', 'vfs_filedata');

			$c = CreateObject('admin.soconfig', $location_id);

			$section		 = 'braArkiv';
			$location_url	 = $c->config_data[$section]['location_url'];//'http://braarkiv.adm.bgo/service/services.asmx';
			$braarkiv_user	 = $c->config_data[$section]['braarkiv_user'];
			$braarkiv_pass	 = $c->config_data[$section]['braarkiv_pass'];

			$this->classname	 = "FDV EBF";
			$this->baseclassname = "Eiendomsarkiver";

			$this->config = $c;

			if (!isset($c->config_data) || !$c->config_data)
			{
				$this->init_config();
			}

			if (!$location_url || !$braarkiv_pass || !$braarkiv_pass)
			{
				throw new Exception('braArkiv is not configured');
			}

			$wdsl	 = "{$location_url}?WSDL";
			$options = array();

			$options[Bra5WsdlClass::WSDL_URL]			 = $wdsl;
			$options[Bra5WsdlClass::WSDL_ENCODING]		 = 'UTF-8';
			$options[Bra5WsdlClass::WSDL_TRACE]			 = $this->debug;
			$options[Bra5WsdlClass::WSDL_SOAP_VERSION]	 = SOAP_1_2;

			try
			{
				$wsdlObject = new Bra5WsdlClass($options);
			}
			catch (Exception $e)
			{
				if ($e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
					return false;
				}
			}

			$bra5ServiceLogin = new Bra5ServiceLogin();
			if ($bra5ServiceLogin->Login(new Bra5StructLogin($braarkiv_user, $braarkiv_pass)))
			{
				$this->secKey = $bra5ServiceLogin->getResult()->getLoginResult()->LoginResult;
			}
			else
			{
				throw new Exception('vfs_fileoperation_braArkiv::Login failed');
			}
			$this->file_map = array();
		}

		private function init_config()
		{
			$receipt_section = $this->config->add_section(array
				(
				'name'	 => 'braArkiv',
				'descr'	 => 'braArkiv'
				)
			);

			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name'		 => 'location_url',
				'descr'		 => 'Location url'
				)
			);
			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name'		 => 'braarkiv_user',
				'descr'		 => 'braArkiv user'
				)
			);

			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'password',
				'name'		 => 'braarkiv_pass',
				'descr'		 => 'braArkiv password'
				)
			);

			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name'		 => 'pickup_catalog',
				'descr'		 => 'Pickup catalog for files'
				)
			);

			$GLOBALS['phpgw']->redirect_link('/index.php', array(
				'menuaction'	 => 'admin.uiconfig2.list_attrib',
				'section_id'	 => $receipt_section['section_id'],
				'location_id'	 => $GLOBALS['phpgw']->locations->get_id('admin', 'vfs_filedata')
				)
			);
		}

		public function get_attributes( )
		{

			$Bra5ServiceGet = new Bra5ServiceGet();

			$Bra5ServiceGet->getAvailableAttributes(new Bra5StructGetAvailableAttributes($this->secKey, $this->baseclassname, $this->classname));
			$attributes = $Bra5ServiceGet->getResult()->getAvailableAttributesResult;
			_debug_array($attributes);

		}

		public function get_document_categories()
		{
			$_values = array(
				'Avtaler',
				'Beskrivelser',
				'Bilder',
				'Brosjyrer',
				'Bruksanvisninger',
				'Drifts- og systeminformasjon',
				'Fargekoder',
				'Garantibefaring',
				'Generell orientering',
				'Ikke aktiv',
				'Innholdsfortegnelse',
				'Kart',
				'Låseplaner',
				'Prosjekt- og entreprenørlister',
				'Rapport',
				'Skjema',
				'Tegning',
				'Tegning, fasade',
				'Tegning, plan',
				'Tegning, snitt',
				'Teknisk informasjon',
				'Tilsyn og vedlikehold'
			);

			$values = array();
			foreach ($_values as $value)
			{
				$values[] = array(
					'id' => $value,
					'name' => $value,
				);
			}

			return $values;

		}

		public function get_branch_list( )
		{
			$_values = array(
				'Alarmer',
				'Arkitekt',
				'Automasjon',
				'Blikkenslager',
				'Brannslokking',
				'Brannvern',
				'Byggdata',
				'Byggeteknisk',
				'Dokumentasjon',
				'Elkraft',
				'Glassmester',
				'Grunn og betong',
				'Grøntanlegg',
				'Heis',
				'Konsulent',
				'Lås, Beslag og Smed',
				'Maler/Gulvlegging',
				'Murer',
				'Renhold',
				'Sanering',
				'Skadebegrensninger',
				'Skadedyr',
				'Tømrer',
				'Utendørsanlegg',
				'Vaktmestertjenester',
				'Ventilasjon',
				'Vintervedlikehold',
				'VVS'
			);

			$values = array();
			foreach ($_values as $value)
			{
				$values[] = array(
					'id' => $value,
					'name' => $value,
				);
			}

			return $values;

		}

		public function get_building_part_list( )
		{
			$_values = array(
				'20-Bygning, generelt',
				'21-Grunn og fundamenter',
				'22-Bæresystemer',
				'23-Yttervegger',
				'24-Innervegger',
				'25-Dekker',
				'26-Yttertak',
				'27-Fast inventar',
				'28-Trapper, balkonger, m.m.',
				'29-Andre bygningsmessige deler',
				'30-VVS-installasjoner, generelt',
				'31-Sanitær',
				'32-Varme',
				'33-Brannslokking',
				'34-Gass og trykkluft',
				'35-Prosesskjøling',
				'36-Luftbehandling',
				'37-Komfortkjøling',
				'38-Vannbehandling',
				'39-Andre VVS-installasjoner',
				'40-Elkraft, generelt',
				'41-Basisinstallasjoner for elkraft',
				'42-Høyspent forsyning',
				'43-Lavspent forsyning',
				'44-Lys',
				'45-Elvarme',
				'46-Reservekraft',
				'49-Andre elkraftinstallasjoner',
				'50-Tele og automatisering, generelt',
				'51-Basisinstallasjoner for tele og automatisering',
				'52-Integrert kommunikasjon',
				'53-Telefoni og personsøking',
				'54-Alarm og signal',
				'55-Lyd og bilde',
				'56-Automatisering',
				'57-Instrumentering',
				'59-Andre installasjoner for tele og automatisering',
				'60-Andre installasjoner, generelt',
				'61-Prefabrikkerte rom',
				'62-Person- og varetransport',
				'63-Transportanlegg for småvarer m.v.',
				'64-Sceneteknisk utstyr',
				'65-Avfall og støvsuging',
				'66-Fastmonterte spesialutrustning for virksomhet',
				'67-Løs spesialutrustning for virksomhet',
				'69-Andre tekniske installasjoner',
				'70-Utendørs, generelt',
				'71-Bearbeidet terreng',
				'72-Utendørs konstruksjoner',
				'73-Utendørs VVS',
				'74-Utendørs elkraft',
				'75-Utendørs tele og automatisering',
				'76-Veger og plasser',
				'77-Park og hage',
				'78-Utendørs infrastruktur',
				'79-Andre utendørs anlegg',
			);

			$values = array();
			foreach ($_values as $value)
			{
				$values[] = array(
					'id' => $value,
					'name' => $value,
				);
			}

			return $values;

		}



	}