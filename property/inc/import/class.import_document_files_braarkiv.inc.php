<?php

	require_once PHPGW_API_INC . '/soap_client/bra5/Bra5Autoload.php';

	class import_document_files
	{

		protected $config,
			$secKey,
			$fields,
			$baseclassname,
			$classname,
			$input_file;

		public function __construct()
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id('admin', 'vfs_filedata');

			$c = CreateObject('admin.soconfig', $location_id);

			$section		 = 'braArkiv';
			$location_url	 = $c->config_data[$section]['location_url'];//'http://braarkiv.adm.bgo/service/services.asmx';
			$braarkiv_user	 = $c->config_data[$section]['braarkiv_user'];
			$braarkiv_pass	 = $c->config_data[$section]['braarkiv_pass'];

			$this->classname	 = $c->config_data[$section]['classname'];//"FDV EBF";
			$this->baseclassname = $c->config_data[$section]['baseclassname'];//"Eiendomsarkiver";

			$this->config = $c;

			if (!isset($c->config_data) || !$c->config_data)
			{
//				$this->init_config();
			}

			if (!$location_url || !$braarkiv_pass || !$braarkiv_pass)
			{
//				throw new Exception('braArkiv is not configured');
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

			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name'		 => 'classname',
				'descr'		 => 'Underklasse/arkivdel'
				)
			);

			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name'		 => 'baseclassname',
				'descr'		 => 'Navn på baseklasse'
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


		/*
		 * Leses fra regneark
		 */

		function get_data( $path )
		{
			phpgw::import_class('phpgwapi.phpspreadsheet');

			$accepted_file_formats = array('xls', 'xlsx', 'ods', 'csv');

			$dir_handle	 = opendir($path);
			while ($file		 = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file("{$path}/{$file}"))
				{

					$extension		 = pathinfo($file, PATHINFO_EXTENSION);
					$processing_file = basename($file, $extension) . 'process';
					$lock			 = basename($file, $extension) . 'lck';

					if (!in_array($extension, $accepted_file_formats))
					{
						continue;
					}

					if (is_file("{$path}/{$lock}"))
					{
						$this->receipt['error'][] = array('msg' => "'{$path}/{$file}' er allerede behandlet");
						return array();
					}
					else
					{
						touch("{$path}/{$processing_file}");
						$input_file = "{$path}/{$file}";
						break;
					}
				}
			}
			closedir($dir_handle);

			if (!$input_file)
			{
				return array();
			}

			$this->input_file = $input_file;

			/** Load $inputFileName to a Spreadsheet Object  * */
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($input_file);


			$spreadsheet->setActiveSheetIndex(0);

			$result = array();

			$highestColumm		 = $spreadsheet->getActiveSheet()->getHighestDataColumn();
			$highestColumnIndex	 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumm);
			$rows				 = $spreadsheet->getActiveSheet()->getHighestDataRow();
			$first_cell_value	 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($highestColumnIndex, 1)->getCalculatedValue();

			$start = $first_cell_value ? 1 : 2; // Read the first line to get the headers out of the way

			for ($j = 1; $j < $highestColumnIndex + 1; $j++)
			{
				$this->fields[] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($j, $start)->getCalculatedValue();
			}

			$start++; // first data line

			$rows = $rows ? $rows : 1;
			for ($row = $start; $row < $rows; $row++)
			{
				$_result = array();

				for ($j = 1; $j <= $highestColumnIndex; $j++)
				{
					$value		 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($j, $row)->getCalculatedValue();
					$_result[]	 = $value;
				}

				if ($value)
				{
					$result[] = $_result;
				}
			}

			$this->receipt['message'][] = array('msg' => "'{$input_file}' contained " . count($result) . " lines");

			return $result;
		}

		/**
		 *
		 * @param array $file
		 * @param array $file_tags
		 */
		function process_file( $file_info, $file_tags )
		{

			$cadastral_unit_arr = explode('/', $file_tags['cadastral_unit']);

			$gnr				 = trim($cadastral_unit_arr[0]);
			$bnr				 = trim($cadastral_unit_arr[1]);
			$byggNummer			 = $file_tags['building_number'];
			$lokasjonskode		 = $file_tags['location_code'];
			$file				 = str_replace('\\', '/', $file_info['path_absolute']);
			$fileDokumentTittel	 = $file_info['file_name'];
			$fileKategorier		 = (array)$file_tags['document_category'];
			$fileBygningsdeler	 = (array)$file_tags['building_part'];
			$fileFag			 = (array)$file_tags['branch'];
			$remark				 = $file_tags['remark'];

			if(!empty($file_tags['remark_detail']))
			{
				$remark .= "; {$file_tags['remark_detail']}";
			}

			$ok = false;
			if (is_file($file))
			{
				try
				{
					$ok = $this->uploadFile($gnr, $bnr, $byggNummer, $file, $fileDokumentTittel, $fileKategorier, $fileBygningsdeler, $fileFag, $lokasjonskode, $remark);
				}
				catch (Exception $e)
				{
//					echo $e->getTraceAsString();
					$this->receipt['error'][] = array('msg' => $e->getMessage());
				}

				if ($ok)
				{
					$this->receipt['message'][] = array('msg' => "{$file} er importert");
				}
			}

			return $ok;
		}

		/**
		 * 
		 * @param int $gnr
		 * @param int $bnr
		 * @param int $byggNummer
		 * @param string $file
		 * @param string $DokumentTittel
		 * @param array $kategorier
		 * @param array $bygningsdeler
		 * @param array $fag
		 * @param string $lokasjonskode
		 * @param string $remark
		 * @return boolean
		 * @throws Exception
		 */
		function uploadFile( $gnr, $bnr, $byggNummer, $file, $DokumentTittel, $kategorier, $bygningsdeler, $fag, $lokasjonskode, $remark )
		{

			$accepted_file_formats	 = array(
				'dwg',
				'dxf',
				'jpg',
				'doc',
				'docx',
				'xls',
				'xlsx',
				'pdf',
				'tif',
				'gif',
//				'txt'
			);
			$extension				 = pathinfo($file, PATHINFO_EXTENSION);

			if ($extension == null || $extension == "" || !in_array(strtolower($extension), $accepted_file_formats))
			{
				$this->receipt['error'][] = array('msg' => "{$file}: Fileformat not accepted: {$extension}");
				return false;
			}

			$file_date	 = date('Y-m-d', filemtime($file));
			$document	 = $this->setupDocument($gnr, $bnr, $byggNummer, $DokumentTittel, $kategorier, $bygningsdeler, $fag, $lokasjonskode, $file_date, $remark);

//			_debug_array($document);
			//Her blir det alvor....

//			return true;

			$bra5ServiceCreate	 = new Bra5ServiceCreate();
			$bra5CreateDocument	 = new Bra5StructCreateDocument($_assignDocKey = false, $this->secKey, $document);

			if (!$bra5ServiceCreate->createDocument($bra5CreateDocument))
			{
				$LastError = $bra5ServiceCreate->getLastError();

				_debug_array($LastError);
				throw new Exception($LastError['Bra5ServiceCreate::createDocument']->getMessage());
			}


//			echo "SOAP HEADERS:\n</br>";
//			echo $bra5ServiceCreate->getSoapClient()->__getLastRequestHeaders();
//			echo "</br>SOAP REQUEST:\n</br>";
//			echo $bra5ServiceCreate->getSoapClient()->__getLastRequest();

			$document_id = $bra5ServiceCreate->getResult()->getCreateDocumentResult()->getcreateDocumentResult()->ID;

			if (!$document_id)
			{
				return false;
			}

			return $this->write($file, $document_id);
		}

		/**
		 * 	Initierer en ny overføring.
		 * @param string $file
		 * @param int $document_id
		 * @return boolean true on success
		 */
		public function write( $file, $document_id = 0 )
		{
			$ok			 = false;
			$filename	 = basename($file);

			$bra5ServiceFile = new Bra5ServiceFile();

			if ($bra5ServiceFile->fileTransferSendChunkedInit(new Bra5StructFileTransferSendChunkedInit($this->secKey, $document_id, $filename)))
			{
				$transaction_id = $bra5ServiceFile->getResult()->getfileTransferSendChunkedInitResult()->fileTransferSendChunkedInitResult;
			}
			else
			{
//				trigger_error(nl2br($bra5ServiceFile->getLastError()), E_USER_ERROR);
				$this->receipt['error'][] = array('msg' => "{$file}: lagring av fil feilet, document_id: {$document_id}");
			}

			if ($transaction_id)
			{
				$new_string = chunk_split(base64_encode(file_get_contents($file)), 1048576);// Definerer en bufferstørrelse/pakkestørrelse på ca 1mb.

				$content_arr = explode("\r\n", $new_string);

				foreach ($content_arr as $content_part)
				{
					$bra5ServiceFile->fileTransferSendChunk(new Bra5StructFileTransferSendChunk($this->secKey, $transaction_id, $content_part));
				}

				$ok = !!$bra5ServiceFile->fileTransferSendChunkedEnd(new Bra5StructFileTransferSendChunkedEnd($this->secKey, $transaction_id));
			}

			return $ok;
		}

		/**
		 * 
		 * @param int $gnr
		 * @param int $bnr
		 * @param int $byggNummer
		 * @param string $dokumentTittel
		 * @param array $kategorier
		 * @param array $bygningsdeler
		 * @param array $fag
		 * @param string $lokasjonskode
		 * @param string $file_date
		 * @param string $remark
		 * @return \Bra5StructDocument
		 */
		private function setupDocument( $gnr, $bnr, $byggNummer, $dokumentTittel, $kategorier, $bygningsdeler, $fag, $lokasjonskode, $file_date, $remark )
		{
			/*
			 * @param boolean $_bFDoubleSided
			 * @param boolean $_bFSeparateKeySheet
			 * @param boolean $_classified
			 * @param int $_priority
			 * @param int $_productionLineID
			 * @param int $_docSplitTypeID
			 */

			$doc	 = new Bra5StructDocument(false, false, false, 5, 1, 1001);
			$attribs = new Bra5StructArrayOfAttribute();

			$attribute_arr = array();

			$asta			 = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$asta->setName("ASTA_Signatur");
			$asta->setStringValue("masseimport av filer");
			$attribute_arr[] = $asta->build();

			$objekt			 = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$objekt->setName("Lokasjonskode");
			$objekt->setStringValue($lokasjonskode);
			$attribute_arr[] = $objekt->build();

			$matrikkel		 = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVMATRIKKEL);
			$matrikkel->setName("Eiendom");
			$matrikkel->setMatrikkelValue($gnr, $bnr, 0, 0);
			$attribute_arr[] = $matrikkel->build();

			$bygningsnummer	 = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$bygningsnummer->setName("Byggnr");
			$bygningsnummer->setStringValue($byggNummer);
			$attribute_arr[] = $bygningsnummer->build();

			$innhold		 = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$innhold->setName("Innhold");
			$innhold->setStringValue($dokumentTittel);
			$attribute_arr[] = $innhold->build();

			$dato			 = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVDATE);
			$dato->setName("Dokumentdato");
			$dato->setDateValue($file_date);
			$attribute_arr[] = $dato->build();

			$dokumentkategorier	 = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$dokumentkategorier->setUsesLookupValues(true);
			$dokumentkategorier->setName("Dokumentkategori");
			$dokumentkategorier->setStringArrayValue($kategorier);
			$attribute_arr[]	 = $dokumentkategorier->build();

			$fagAttrib		 = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$fagAttrib->setUsesLookupValues(true);
			$fagAttrib->setName("Fag");
			$fagAttrib->setStringArrayValue($fag);
			$attribute_arr[] = $fagAttrib->build();

			$bygningsdelAttrib	 = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$bygningsdelAttrib->setUsesLookupValues(true);
			$bygningsdelAttrib->setName("Bygningsdel");
			$bygningsdelAttrib->setStringArrayValue($bygningsdeler);
			$attribute_arr[]	 = $bygningsdelAttrib->build();

			$merknad		 = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$merknad->setName("Merknad");
			$merknad->setStringValue($remark);
			$attribute_arr[] = $merknad->build();

			$attribs->setAttribute($attribute_arr);

			$doc->setAttributes($attribs);

			$doc->setBaseClassName($this->baseclassname);
			$doc->setClassName($this->classname);
			$doc->setClassified(false);

			return $doc;
		}
	}

	class AttributeFactory
	{

		private $attribute;

		function __construct( $_attribType )
		{
			$this->attribute	 = new Bra5StructAttribute($_usesLookupValues	 = false, $_attribType);
		}

		public function setUsesLookupValues( $_usesLookupValues )
		{
			$this->attribute->setUsesLookupValues($_usesLookupValues);
			return $this;
		}

		public function setType( $type )
		{
			$this->attribute->setAttribType($type);
			return $this;
		}

		public function setName( $attributeName )
		{
			$this->attribute->setName($attributeName);
			return $this;
		}

		public function setStringValue( $value )
		{
			$values = array($value);
			$this->setStringArrayValue($values);
			return $this;
		}

		public function setStringArrayValue( $values )
		{
			foreach ($values as &$value)
			{
				$value = new SoapVar($value, XSD_STRING, "string", "http://www.w3.org/2001/XMLSchema");
			}

			$attributeValue = new Bra5StructArrayOfAnyType();
			$attributeValue->setAnyType($values);
			$this->attribute->setValue($attributeValue);
			return $this;
		}

		public function setMatrikkelValue( $gnr, $bnr, $fnr, $snr )
		{
			$gnrBnrValue = new Bra5StructArrayOfAnyType();
			$matrikkel	 = new Bra5StructMatrikkel();

			$matrikkel->setGNr("{$gnr}");
			$matrikkel->setBNr("{$bnr}");
			$matrikkel->setFNr("{$fnr}");
			$matrikkel->setSNr("{$snr}");

			$gnrBnrValue->setAnyType($matrikkel);
			$this->attribute->setValue($gnrBnrValue);
			return $this;
		}

		public function setDateValue( $date )
		{
			$datoValue	 = new Bra5StructArrayOfAnyType();
			$value		 = new SoapVar($date, XSD_STRING, "string", "http://www.w3.org/2001/XMLSchema");
			$datoValue->setAnyType($value);
			$this->attribute->setValue($datoValue);
			return $this;
		}

		public function build()
		{
			return $this->attribute;
		}
	}